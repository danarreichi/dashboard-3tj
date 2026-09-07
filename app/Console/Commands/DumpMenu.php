<?php

namespace App\Console\Commands;

use App\Models\Inventory;
use App\Models\InventoryHistory;
use App\Models\Menu;
use App\Models\MenuCategory;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class DumpMenu extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dump:menu
                            {--output= : JSON output path. Defaults to storage/app/exports/menu.json}
                            {--pretty : Pretty-print the JSON output}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export menu, active prices, inventory masters, and recipes for saas2 import';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $categories = MenuCategory::query()
            ->orderBy('id')
            ->get();
        $inventories = Inventory::query()
            ->orderBy('id')
            ->get();
        $histories = InventoryHistory::query()
            ->with('inventory')
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();
        $menus = Menu::query()
            ->with(['category', 'price.recipes.history.inventory'])
            ->orderBy('id')
            ->get();

        $warnings = [];
        $export = [
            'format' => 'tj3-menu-export',
            'version' => 2,
            'source' => [
                'application' => 'dashboard-3tj',
                'exported_at' => now()->toIso8601String(),
                'tables' => [
                    'menu_categories',
                    'menus',
                    'menu_prices',
                    'inventories',
                    'inventory_histories',
                    'menu_recipes',
                ],
            ],
            'categories' => $categories->map(fn (MenuCategory $category): array => [
                'source_id' => $category->id,
                'source_uuid' => $category->uuid,
                'name' => $category->name,
            ])->values()->all(),
            'inventories' => $inventories->map(fn (Inventory $inventory): array => [
                'source_id' => $inventory->id,
                'source_uuid' => $inventory->uuid,
                'name' => $inventory->name,
                'source_unit' => $inventory->unit,
                'base_unit' => $inventory->unit,
                'stock_type' => $inventory->stock_type,
                'is_stock_tracked' => $inventory->stock_type !== Inventory::FIXED,
                'source_qty' => $inventory->qty === null ? null : (string) $inventory->qty,
            ])->values()->all(),
            'histories' => $histories->map(function (InventoryHistory $history) use (&$warnings): array {
                $inventory = $history->inventory;
                if (! $inventory) {
                    $warnings[] = $this->warning(
                        'history_missing_inventory',
                        "Inventory history {$history->id} does not resolve to an inventory master.",
                        ['history_source_id' => $history->id, 'history_source_uuid' => $history->uuid],
                    );
                }

                $priceMinor = $this->toMinorUnits($history->price);

                return [
                    'source_id' => $history->id,
                    'source_uuid' => $history->uuid,
                    'inventory_source_id' => $inventory?->id,
                    'inventory_source_uuid' => $inventory?->uuid,
                    'status' => $history->status,
                    'qty' => $history->qty === null ? null : (string) $history->qty,
                    'price' => (string) $history->price,
                    'price_minor' => $priceMinor,
                    'is_custom' => (bool) $history->is_custom,
                    'payload' => is_array($history->payload) ? $history->payload : null,
                    'created_at' => $history->created_at?->toIso8601String(),
                    'updated_at' => $history->updated_at?->toIso8601String(),
                    'is_importable' => $inventory !== null,
                ];
            })->values()->all(),
            'menus' => [],
            'recipes' => [],
            'warnings' => [],
        ];

        $this->addDuplicateWarnings($warnings, $categories, 'category');
        $this->addDuplicateWarnings($warnings, $inventories, 'inventory');

        $export['menus'] = $menus->map(function (Menu $menu) use (&$warnings, &$export): array {
            $category = $menu->category;
            $activePrice = $menu->price;

            if (! $category) {
                $warnings[] = $this->warning(
                    'menu_missing_category',
                    "Menu {$menu->name} does not have an active category.",
                    ['menu_source_id' => $menu->id, 'menu_source_uuid' => $menu->uuid],
                );
            }

            if (! $activePrice) {
                $warnings[] = $this->warning(
                    'menu_missing_active_price',
                    "Menu {$menu->name} does not have an active price.",
                    ['menu_source_id' => $menu->id, 'menu_source_uuid' => $menu->uuid],
                );
            }

            if ($activePrice) {
                foreach ($activePrice->recipes as $recipe) {
                    $history = $recipe->history;
                    $inventory = $history?->inventory;

                    if (! $history || ! $inventory) {
                        $warnings[] = $this->warning(
                            'recipe_missing_inventory',
                            "Recipe for menu {$menu->name} does not resolve to an inventory master.",
                            [
                                'menu_source_id' => $menu->id,
                                'menu_price_source_id' => $activePrice->id,
                                'recipe_source_id' => $recipe->id,
                                'inventory_history_source_id' => $recipe->inventory_history_id,
                            ],
                        );
                    }

                    $export['recipes'][] = [
                        'source_id' => $recipe->id,
                        'source_uuid' => $recipe->uuid,
                        'menu_source_id' => $menu->id,
                        'menu_source_uuid' => $menu->uuid,
                        'menu_price_source_id' => $activePrice->id,
                        'menu_price_source_uuid' => $activePrice->uuid,
                        'inventory_history_source_id' => $recipe->inventory_history_id,
                        'inventory_history_source_uuid' => $history?->uuid,
                        'inventory_source_id' => $inventory?->id,
                        'inventory_source_uuid' => $inventory?->uuid,
                        'qty_base' => (string) $recipe->qty,
                        'is_custom' => (bool) ($history?->is_custom ?? false),
                        'is_importable' => $inventory !== null,
                    ];
                }
            }

            return [
                'source_id' => $menu->id,
                'source_uuid' => $menu->uuid,
                'name' => $menu->name,
                'category_source_id' => $category?->id,
                'category_source_uuid' => $category?->uuid,
                'active_price' => $activePrice ? [
                    'source_id' => $activePrice->id,
                    'source_uuid' => $activePrice->uuid,
                    'amount' => (string) $activePrice->price,
                    'price_minor' => $this->toMinorUnits($activePrice->price),
                ] : null,
            ];
        })->values()->all();

        $export['warnings'] = $warnings;
        $export['counts'] = [
            'categories' => count($export['categories']),
            'inventories' => count($export['inventories']),
            'histories' => count($export['histories']),
            'menus' => count($export['menus']),
            'recipes' => count($export['recipes']),
            'warnings' => count($export['warnings']),
        ];

        $outputPath = $this->resolveOutputPath($this->option('output'));
        File::ensureDirectoryExists(dirname($outputPath));

        $flags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR;

        if ($this->option('pretty')) {
            $flags |= JSON_PRETTY_PRINT;
        }

        file_put_contents($outputPath, json_encode($export, $flags));

        $this->info('Menu export created: '.$outputPath);
        $this->line(sprintf(
            'Categories: %d | Inventories: %d | Histories: %d | Menus: %d | Recipes: %d',
            $export['counts']['categories'],
            $export['counts']['inventories'],
            $export['counts']['histories'],
            $export['counts']['menus'],
            $export['counts']['recipes'],
        ));

        if ($warnings !== []) {
            $this->warn('Warnings: '.count($warnings));
            $this->table(
                ['Code', 'Message'],
                collect($warnings)->map(fn (array $warning): array => [$warning['code'], $warning['message']])->all(),
            );
        }

        return self::SUCCESS;
    }

    /**
     * @param  array<int, array<string, mixed>>  $warnings
     * @param  \Illuminate\Database\Eloquent\Collection<int, MenuCategory|Inventory>  $records
     */
    private function addDuplicateWarnings(array &$warnings, Collection $records, string $type): void
    {
        $duplicates = $records
            ->groupBy(fn ($record): string => Str::lower(trim($record->name)))
            ->filter(fn ($group): bool => $group->count() > 1);

        foreach ($duplicates as $name => $group) {
            $warnings[] = $this->warning(
                $type.'_duplicate_name',
                ucfirst($type).' name appears more than once: '.$name.'.',
                ['source_ids' => $group->pluck('id')->values()->all()],
            );
        }
    }

    /**
     * @param  array<string, mixed>  $context
     * @return array{code: string, message: string, context: array<string, mixed>}
     */
    private function warning(string $code, string $message, array $context = []): array
    {
        return compact('code', 'message', 'context');
    }

    private function toMinorUnits(mixed $amount): int
    {
        return (int) round((float) $amount, 0, PHP_ROUND_HALF_UP);
    }

    private function resolveOutputPath(?string $output): string
    {
        if (! $output) {
            return storage_path('app/exports/menu.json');
        }

        if (preg_match('/^(?:[A-Za-z]:[\\\\\/]|[\\\\\/]{2})/', $output) === 1) {
            return $output;
        }

        return base_path($output);
    }
}
