<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\ServiceProvider;

class MacroServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        $this->registerWithCalculation();
    }

    /**
     * Add a calculated aggregate to the query using a relationship (or nested relationship).
     *
     * Mendukung BelongsTo, HasOne/HasMany, MorphOne/MorphMany, BelongsToMany, MorphToMany,
     * dan HasManyThrough. Nested via dot-notation, mis. 'posts.comments'.
     *
     * Behavior penting:
     * - Soft deletes pada model related (dan model di sepanjang chain) otomatis di-respect.
     * - Global scopes dari relasi diterapkan (mengikuti pola withCount() Laravel).
     *
     * KEAMANAN: parameter $calculation di-inject via selectRaw. JANGAN pernah passing
     * nilai dari user input. Hanya gunakan ekspresi yang sudah hardcoded/trusted.
     */
    private function registerWithCalculation(): void
    {
        Builder::macro('withCalculation', function (string $relationName, string $calculation, ?string $alias = null) {
            $alias = $alias ?? str_replace('.', '_', $relationName).'_calculation';
            $model = $this->getModel();
            $modelTable = $model->getTable();

            $segments = explode('.', $relationName);

            $chain = [];
            $currentModel = $model;
            foreach ($segments as $segment) {
                $method = lcfirst(str_replace([' ', '_', '-'], '', ucwords(str_replace([' ', '_', '-'], ' ', $segment))));
                if (! method_exists($currentModel, $method)) {
                    throw new \InvalidArgumentException("Relationship {$method} not found on ".get_class($currentModel));
                }
                $chain[] = $currentModel->$method();
                $currentModel = end($chain)->getRelated();
            }

            $usesSoftDeletes = fn ($modelInstance) => in_array(SoftDeletes::class, class_uses_recursive($modelInstance), true);

            $resolveJoin = function ($relation) {
                $related = $relation->getRelated();
                $relatedTable = $related->getTable();
                $parent = $relation->getParent();
                $parentTable = $parent->getTable();

                if ($relation instanceof \Illuminate\Database\Eloquent\Relations\MorphToMany) {
                    return [
                        'kind' => 'pivot',
                        'pivot' => [
                            'table' => $relation->getTable(),
                            'leftCol' => $relation->getForeignPivotKeyName(),
                            'parentCol' => $relation->getParentKeyName(),
                            'rightCol' => $relation->getRelatedPivotKeyName(),
                            'relatedCol' => $relation->getRelatedKeyName(),
                        ],
                        'parentTable' => $parentTable,
                        'parentModel' => $parent,
                        'relatedTable' => $relatedTable,
                        'relatedModel' => $related,
                        'extraWheres' => [
                            [$relation->getTable().'.'.$relation->getMorphType(), '=', $relation->getMorphClass()],
                        ],
                    ];
                }

                if ($relation instanceof \Illuminate\Database\Eloquent\Relations\BelongsToMany) {
                    return [
                        'kind' => 'pivot',
                        'pivot' => [
                            'table' => $relation->getTable(),
                            'leftCol' => $relation->getForeignPivotKeyName(),
                            'parentCol' => $relation->getParentKeyName(),
                            'rightCol' => $relation->getRelatedPivotKeyName(),
                            'relatedCol' => $relation->getRelatedKeyName(),
                        ],
                        'parentTable' => $parentTable,
                        'parentModel' => $parent,
                        'relatedTable' => $relatedTable,
                        'relatedModel' => $related,
                        'extraWheres' => [],
                    ];
                }

                if ($relation instanceof \Illuminate\Database\Eloquent\Relations\HasManyThrough) {
                    $throughModel = $relation->getThroughParent();
                    $farParent = $relation->getFarParent();

                    return [
                        'kind' => 'through',
                        'through' => [
                            'table' => $throughModel->getTable(),
                            'pk' => $throughModel->getKeyName(),
                            'firstKey' => $relation->getFirstKeyName(),
                            'secondKey' => $relation->getForeignKeyName(),
                        ],
                        'throughModel' => $throughModel,
                        'parentTable' => $farParent->getTable(),
                        'parentCol' => $relation->getLocalKeyName(),
                        'parentModel' => $farParent,
                        'relatedTable' => $relatedTable,
                        'relatedModel' => $related,
                        'extraWheres' => [],
                    ];
                }

                if ($relation instanceof \Illuminate\Database\Eloquent\Relations\MorphOneOrMany) {
                    return [
                        'kind' => 'simple',
                        'parentTable' => $parentTable,
                        'parentCol' => $relation->getLocalKeyName(),
                        'parentModel' => $parent,
                        'relatedTable' => $relatedTable,
                        'relatedCol' => $relation->getForeignKeyName(),
                        'relatedModel' => $related,
                        'extraWheres' => [
                            [$relatedTable.'.'.$relation->getMorphType(), '=', $relation->getMorphClass()],
                        ],
                    ];
                }

                if ($relation instanceof \Illuminate\Database\Eloquent\Relations\HasOneOrMany) {
                    return [
                        'kind' => 'simple',
                        'parentTable' => $parentTable,
                        'parentCol' => $relation->getLocalKeyName(),
                        'parentModel' => $parent,
                        'relatedTable' => $relatedTable,
                        'relatedCol' => $relation->getForeignKeyName(),
                        'relatedModel' => $related,
                        'extraWheres' => [],
                    ];
                }

                if ($relation instanceof \Illuminate\Database\Eloquent\Relations\BelongsTo) {
                    return [
                        'kind' => 'simple',
                        'parentTable' => $parentTable,
                        'parentCol' => $relation->getForeignKeyName(),
                        'parentModel' => $parent,
                        'relatedTable' => $relatedTable,
                        'relatedCol' => $relation->getOwnerKeyName(),
                        'relatedModel' => $related,
                        'extraWheres' => [],
                    ];
                }

                throw new \InvalidArgumentException('Relation type ['.get_class($relation).'] not supported.');
            };

            $deepestRelation = end($chain);
            $deepestModel = $currentModel;
            $deepestTable = $deepestModel->getTable();

            $relatedQuery = $deepestRelation->getRelated()->newQuery();
            $relatedQuery->applyScopes();

            $subquery = $relatedQuery->getQuery();
            $subquery->selectRaw($calculation);

            if ($usesSoftDeletes($deepestModel)) {
                $subquery->whereNull($deepestTable.'.'.$deepestModel->getDeletedAtColumn());
            }

            for ($i = count($chain) - 1; $i >= 0; $i--) {
                $relation = $chain[$i];
                $spec = $resolveJoin($relation);
                $isOutermost = ($i === 0);

                if ($spec['kind'] === 'pivot') {
                    $pivot = $spec['pivot'];

                    $subquery->join(
                        $pivot['table'],
                        "{$pivot['table']}.{$pivot['rightCol']}",
                        '=',
                        "{$spec['relatedTable']}.{$pivot['relatedCol']}"
                    );

                    foreach ($spec['extraWheres'] as $w) {
                        $subquery->where($w[0], $w[1], $w[2]);
                    }

                    if ($isOutermost) {
                        $subquery->whereColumn(
                            "{$pivot['table']}.{$pivot['leftCol']}",
                            "{$modelTable}.{$pivot['parentCol']}"
                        );
                    } else {
                        $subquery->join(
                            $spec['parentTable'],
                            "{$pivot['table']}.{$pivot['leftCol']}",
                            '=',
                            "{$spec['parentTable']}.{$pivot['parentCol']}"
                        );
                        if ($usesSoftDeletes($spec['parentModel'])) {
                            $subquery->whereNull(
                                $spec['parentTable'].'.'.$spec['parentModel']->getDeletedAtColumn()
                            );
                        }
                    }
                    continue;
                }

                if ($spec['kind'] === 'through') {
                    $t = $spec['through'];

                    $subquery->join(
                        $t['table'],
                        "{$t['table']}.{$t['pk']}",
                        '=',
                        "{$spec['relatedTable']}.{$t['secondKey']}"
                    );

                    if ($usesSoftDeletes($spec['throughModel'])) {
                        $subquery->whereNull(
                            $t['table'].'.'.$spec['throughModel']->getDeletedAtColumn()
                        );
                    }

                    if ($isOutermost) {
                        $subquery->whereColumn(
                            "{$t['table']}.{$t['firstKey']}",
                            "{$modelTable}.{$spec['parentCol']}"
                        );
                    } else {
                        $subquery->join(
                            $spec['parentTable'],
                            "{$t['table']}.{$t['firstKey']}",
                            '=',
                            "{$spec['parentTable']}.{$spec['parentCol']}"
                        );
                        if ($usesSoftDeletes($spec['parentModel'])) {
                            $subquery->whereNull(
                                $spec['parentTable'].'.'.$spec['parentModel']->getDeletedAtColumn()
                            );
                        }
                    }
                    continue;
                }

                foreach ($spec['extraWheres'] as $w) {
                    $subquery->where($w[0], $w[1], $w[2]);
                }

                if ($isOutermost) {
                    $subquery->whereColumn(
                        "{$spec['relatedTable']}.{$spec['relatedCol']}",
                        "{$modelTable}.{$spec['parentCol']}"
                    );
                } else {
                    $subquery->join(
                        $spec['parentTable'],
                        "{$spec['relatedTable']}.{$spec['relatedCol']}",
                        '=',
                        "{$spec['parentTable']}.{$spec['parentCol']}"
                    );
                    if ($usesSoftDeletes($spec['parentModel'])) {
                        $subquery->whereNull(
                            $spec['parentTable'].'.'.$spec['parentModel']->getDeletedAtColumn()
                        );
                    }
                }
            }

            return $this->addSelect([$alias => $subquery]);
        });
    }
}
