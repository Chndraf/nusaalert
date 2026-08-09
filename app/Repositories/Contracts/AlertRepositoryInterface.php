<?php

namespace App\Repositories\Contracts;

use App\Models\Alert;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AlertRepositoryInterface extends BaseRepositoryInterface
{
    public function getForUserPaginated(int $userId, array $filters = [], int $perPage = 15): LengthAwarePaginator;
    
    public function getUnreadForUser(int $userId, int $limit = 10): Collection;
    
    public function getUnreadCountForUser(int $userId): int;
    
    public function markAsRead(int $alertId, int $userId): ?Alert;
    
    public function markAllAsReadForUser(int $userId): int;
}
