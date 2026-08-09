<?php

namespace App\Repositories\Contracts;

use App\Models\Laporan;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface LaporanRepositoryInterface extends BaseRepositoryInterface
{
    public function getPaginated(array $filters = [], int $perPage = 20): LengthAwarePaginator;

    public function getVerifiedAndNonPendingPaginated(int $perPage = 15): LengthAwarePaginator;

    public function getPendingPaginated(int $perPage = 15): LengthAwarePaginator;

    public function getTotalCount(): int;

    public function getPendingCount(): int;

    public function getLatestPending(int $limit = 5): Collection;
}
