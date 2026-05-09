<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

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

        Builder::macro('withCalculation', function ($relationName, $calculation, $alias = null) {
            $alias = $alias ?? 'calculation';
            $model = $this->getModel();
            $method = lcfirst(str_replace([' ', '_'], '', ucwords(str_replace('_', ' ', $relationName))));

            if (!method_exists($model, $method)) {
                throw new \InvalidArgumentException("Relationship {$method} not found on " . get_class($model));
            }

            $relation     = $model->$method();
            $relatedModel = $relation->getRelated();
            $relatedTable = $relatedModel->getTable();
            $relatedKey   = $relatedModel->getKeyName();
            $modelTable   = $model->getTable();

            if ($relation instanceof \Illuminate\Database\Eloquent\Relations\BelongsToMany) {
                $pivotTable   = $relation->getTable();
                $foreignKey   = $relation->getForeignPivotKeyName();
                $relatedPivot = $relation->getRelatedPivotKeyName();

                $subquery = DB::table($pivotTable)
                    ->join($relatedTable, "{$pivotTable}.{$relatedPivot}", '=', "{$relatedTable}.{$relatedKey}")
                    ->selectRaw($calculation)
                    ->whereColumn("{$pivotTable}.{$foreignKey}", "{$modelTable}.id");

            } elseif ($relation instanceof \Illuminate\Database\Eloquent\Relations\HasMany) {
                $foreignKey = $relation->getForeignKeyName();

                $subquery = DB::table($relatedTable)
                    ->selectRaw($calculation)
                    ->whereColumn("{$relatedTable}.{$foreignKey}", "{$modelTable}.id");

            } else {
                $type = get_class($relation);
                throw new \InvalidArgumentException("Relation type [{$type}] not supported.");
            }

            return $this->addSelect([$alias => $subquery]);
        });
    }
}
