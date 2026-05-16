<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->shiftTimestamps('+');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->shiftTimestamps('-');
    }

    private function shiftTimestamps(string $sign): void
    {
        $driver = DB::connection()->getDriverName();
        $tables = $this->listTables($driver);

        DB::transaction(function () use ($driver, $sign, $tables) {
            foreach ($tables as $table) {
                foreach (['created_at', 'updated_at'] as $column) {
                    if (! Schema::hasColumn($table, $column)) {
                        continue;
                    }
                    DB::statement($this->buildShiftSql($driver, $table, $column, $sign));
                }
            }
        });
    }

    /**
     * @return string[]
     */
    private function listTables(string $driver): array
    {
        return match ($driver) {
            'sqlite' => array_column(
                DB::select("SELECT name FROM sqlite_master WHERE type = 'table' AND name NOT LIKE 'sqlite_%'"),
                'name'
            ),
            'mysql' => array_column(
                DB::select('SELECT TABLE_NAME as name FROM information_schema.tables WHERE TABLE_SCHEMA = DATABASE()'),
                'name'
            ),
            'pgsql' => array_column(
                DB::select('SELECT tablename as name FROM pg_tables WHERE schemaname = current_schema()'),
                'name'
            ),
            default => throw new \RuntimeException("Unsupported driver for timestamp shift: {$driver}"),
        };
    }

    private function buildShiftSql(string $driver, string $table, string $column, string $sign): string
    {
        return match ($driver) {
            'sqlite' => "UPDATE \"{$table}\" SET {$column} = datetime({$column}, '{$sign}7 hours') WHERE {$column} IS NOT NULL",
            'mysql' => "UPDATE `{$table}` SET `{$column}` = DATE_ADD(`{$column}`, INTERVAL {$sign}7 HOUR) WHERE `{$column}` IS NOT NULL",
            'pgsql' => "UPDATE \"{$table}\" SET \"{$column}\" = \"{$column}\" {$sign} INTERVAL '7 hours' WHERE \"{$column}\" IS NOT NULL",
        };
    }
};
