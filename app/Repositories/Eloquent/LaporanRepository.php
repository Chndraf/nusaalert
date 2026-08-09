<?php

namespace App\Repositories\Eloquent;

use App\Models\Laporan;
use App\Repositories\Contracts\LaporanRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class LaporanRepository extends BaseRepository implements LaporanRepositoryInterface
{
    public function __construct(Laporan $model)
    {
        parent::__construct($model);
    }

    public function getPaginated(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->model->with('user')->orderBy('created_at', 'desc');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->paginate($perPage);
    }

    public function getVerifiedAndNonPendingPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->where('status', '!=', 'pending')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getPendingPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->where('status', 'pending')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getTotalCount(): int
    {
        return $this->model->count();
    }

    public function getPendingCount(): int
    {
        return $this->model->where('status', 'pending')->count();
    }

    public function getLatestPending(int $limit = 5): Collection
    {
        return $this->model->where('status', 'pending')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
