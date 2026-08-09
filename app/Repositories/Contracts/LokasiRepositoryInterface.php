<?php

namespace App\Repositories\Contracts;

use App\Models\Lokasi;
use Illuminate\Database\Eloquent\Collection;

interface LokasiRepositoryInterface extends BaseRepositoryInterface
{
    public function getForUser(int $userId): Collection;

    public function createForUser(int $userId, array $attributes): Lokasi;

    public function getActive(): Collection;

    public function toggleActive(int $lokasiId): ?Lokasi;
}
