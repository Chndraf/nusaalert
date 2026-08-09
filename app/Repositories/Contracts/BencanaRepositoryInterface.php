<?php

namespace App\Repositories\Contracts;

use App\Models\Bencana;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BencanaRepositoryInterface extends BaseRepositoryInterface
{
    public function getPaginated(array $filters = [], int $perPage = 20): LengthAwarePaginator;

    public function getActiveInDays(int $days = 30): Collection;

    public function getNearby(float $lat, float $lng, float $radius = 100.0, int $days = 30): Collection;

    public function getCountToday(): int;

    public function getTotalCount(): int;

    public function getLatest(int $limit = 5): Collection;

    public function existsByEventId(string $eventId): bool;
}
