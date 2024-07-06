<?php

namespace App\Repositories;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Interface EloquentRepositoryInterface
 * @package App\Repository
 */
interface EloquentRepositoryInterface
{
    public function index(array $filters = [], array $sort = []): QueryBuilder;

    public function create(array $attributes): Model;

    public function upsert(array $query, array $attributes): Model;

    public function find($id): ?Model;

    public function findOne(string $column, string $value): ?Model;

    public function update(Model|Authenticatable &$model, array $attributes): Model;

    public function destroy(Model|Authenticatable $model);

    public function restore($id): Model;
}
