<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

interface BaseRepositoryInterface
{
    public function all(array $columns = ['*'], array $relations = []): Collection;
    public function find(int $id, array $columns = ['*'], array $relations = []): ?Model;
    public function findOrFail(int $id, array $columns = ['*'], array $relations = []): Model;
    public function create(array $attributes): Model;
    public function update(int $id, array $attributes): bool;
    public function delete(int $id): bool;
}
