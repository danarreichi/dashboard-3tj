<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        URL::forceScheme('https');

        /**
         * Add a calculated aggregate to the query using a relationship.
         *
         * This macro allows you to add a subquery that performs calculations on related models.
         * It supports multiple relationship types including BelongsToMany, HasManyThrough, MorphMany,
         * and HasMany relationships.
         *
         * @param  string  $relationName  The name of the relationship method on the model
         * @param  string  $calculation  The raw SQL calculation expression (e.g., 'COUNT(*)', 'SUM(amount)')
         * @param  string|null  $alias  The alias for the calculated column in results (defaults to 'calculation')
         * @return \Illuminate\Database\Eloquent\Builder The query builder instance for method chaining
         *
         * @throws \InvalidArgumentException If the relationship method doesn't exist on the model
         * @throws \InvalidArgumentException If the relationship type is not supported
         *
         * @example
         * // Count related items
         * User::withCalculation('posts', 'COUNT(*)')->get();
         *
         * // Sum related amounts
         * Order::withCalculation('items', 'SUM(price)', 'total_price')->get();
         */
        Builder::macro('withCalculation', function ($relationName, $calculation, $alias = null) {
            $alias = $alias ?? 'calculation';
            $model = $this->getModel();
            $modelKey = $model->getKeyName();
            $method = lcfirst(str_replace([' ', '_', '-'], '', ucwords(str_replace([' ', '_', '-'], ' ', $relationName))));

            if (! method_exists($model, $method)) {
                throw new \InvalidArgumentException("Relationship {$method} not found on ".get_class($model));
            }

            $relation = $model->$method();
            $relatedModel = $relation->getRelated();
            $relatedTable = $relatedModel->getTable();
            $relatedKey = $relatedModel->getKeyName();
            $modelTable = $model->getTable();

            // Handle BelongsToMany relationships through pivot table
            if ($relation instanceof \Illuminate\Database\Eloquent\Relations\BelongsToMany) {
                $pivotTable = $relation->getTable();
                $foreignKey = $relation->getForeignPivotKeyName();
                $relatedPivot = $relation->getRelatedPivotKeyName();

                $subquery = DB::table($pivotTable)
                    ->join($relatedTable, "{$pivotTable}.{$relatedPivot}", '=', "{$relatedTable}.{$relatedKey}")
                    ->selectRaw($calculation)
                    ->whereColumn("{$pivotTable}.{$foreignKey}", "{$modelTable}.{$modelKey}");

                // Handle HasManyThrough relationships through intermediate table
            } elseif ($relation instanceof \Illuminate\Database\Eloquent\Relations\HasManyThrough) {
                $throughTable = $relation->getParent()->getTable();
                $throughKey = $relation->getFirstKeyName();
                $throughForeign = $relation->getForeignKeyName();

                $subquery = DB::table($relatedTable)
                    ->join($throughTable, "{$throughTable}.id", '=', "{$relatedTable}.{$throughForeign}")
                    ->selectRaw($calculation)
                    ->whereColumn("{$throughTable}.{$throughKey}", "{$modelTable}.{$modelKey}");

                // Handle MorphMany relationships with polymorphic type checking
            } elseif ($relation instanceof \Illuminate\Database\Eloquent\Relations\MorphMany) {
                $foreignKey = $relation->getForeignKeyName();
                $morphType = $relation->getMorphType();
                $morphClass = $relation->getMorphClass();

                $subquery = DB::table($relatedTable)
                    ->selectRaw($calculation)
                    ->whereColumn("{$relatedTable}.{$foreignKey}", "{$modelTable}.{$modelKey}")
                    ->where("{$relatedTable}.{$morphType}", $morphClass);

                // Handle HasMany relationships
            } elseif ($relation instanceof \Illuminate\Database\Eloquent\Relations\HasMany) {
                $foreignKey = $relation->getForeignKeyName();

                $subquery = DB::table($relatedTable)
                    ->selectRaw($calculation)
                    ->whereColumn("{$relatedTable}.{$foreignKey}", "{$modelTable}.{$modelKey}");

                // Unsupported relationship type
            } else {
                $type = get_class($relation);
                throw new \InvalidArgumentException("Relation type [{$type}] not supported.");
            }

            return $this->addSelect([$alias => $subquery]);
        });
    }
}
