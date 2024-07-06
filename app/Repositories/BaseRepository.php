<?php

namespace App\Repositories;

use App\Repositories\EloquentRepositoryInterface;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\QueryBuilder;

class BaseRepository implements EloquentRepositoryInterface
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function index(array $filters = [], array $sort = []): QueryBuilder
    {
        $query = QueryBuilder::for($this->model::class)
            ->allowedFilters($filters)
            ->allowedSorts($sort);
        return $query;
    }

    public function create(array $attributes): Model
    {
        return $this->model->create($attributes);
    }

    public function find($id): ?Model
    {
        return $this->model->find($id);
    }

    public function findOne(string $column, string $value): ?Model
    {
        return $this->model->where($column, $value)->firstOrFail();
    }

    public function update(Model|Authenticatable &$model, array $attributes): Model
    {
        $model->update($attributes);
        return $model;
    }

    public function upsert(array $query, array $attributes): Model
    {
        return $this->model->updateOrCreate($query, $attributes);
    }

    public function upsertWithTrashed(array $query, array $attributes): Model
    {
        return $this->model->withTrashed()->updateOrCreate($query, $attributes);
    }

    public function createBulk(array $attributes)
    {
        return $this->model->insert($attributes);
    }

    public function destroy(Model|Authenticatable $model)
    {
        return $model->delete();
    }

    public function restore($id): Model
    {
        $model = $this->model->onlyTrashed()->where($this->model->getRouteKeyName(), $id)->firstOrFail();
        $model->restore();
        return $model;
    }
}
