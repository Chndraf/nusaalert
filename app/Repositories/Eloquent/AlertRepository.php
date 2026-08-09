<?php

namespace App\Repositories\Eloquent;

use App\Models\Alert;
use App\Repositories\Contracts\AlertRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AlertRepository extends BaseRepository implements AlertRepositoryInterface
{
    public function __construct(Alert $model)
    {
        parent::__construct($model);
    }

    public function getForUserPaginated(int $userId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->where('user_id', $userId)
            ->with(['bencana', 'lokasi'])
            ->orderBy('created_at', 'desc');

        if (!empty($filters['jenis'])) {
            $query->whereHas('bencana', function ($q) use ($filters) {
                $q->where('jenis_bencana', $filters['jenis']);
            });
        }

        if (!empty($filters['tanggal'])) {
            $query->whereDate('created_at', $filters['tanggal']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->paginate($perPage);
    }
    
    public function getUnreadForUser(int $userId, int $limit = 10): Collection
    {
        return $this->model->where('user_id', $userId)
            ->where('status', 'sent')
            ->with(['bencana', 'lokasi'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
    
    public function getUnreadCountForUser(int $userId): int
    {
        return $this->model->where('user_id', $userId)
            ->where('status', 'sent')
            ->count();
    }
    
    public function markAsRead(int $alertId, int $userId): ?Alert
    {
        /** @var Alert|null $alert */
        $alert = $this->model->where('id', $alertId)
            ->where('user_id', $userId)
            ->first();

        if ($alert instanceof Alert) {
            $alert->update([
                'status' => 'read',
                'read_at' => now(),
            ]);
            return $alert->fresh();
        }

        return null;
    }
    
    public function markAllAsReadForUser(int $userId): int
    {
        return $this->model->where('user_id', $userId)
            ->where('status', 'sent')
            ->update([
                'status' => 'read',
                'read_at' => now(),
            ]);
    }
}
