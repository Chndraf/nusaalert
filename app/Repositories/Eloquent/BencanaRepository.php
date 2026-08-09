<?php

namespace App\Repositories\Eloquent;

use App\Models\Bencana;
use App\Repositories\Contracts\BencanaRepositoryInterface;
use App\Services\BmkgService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BencanaRepository extends BaseRepository implements BencanaRepositoryInterface
{
    public function __construct(Bencana $model)
    {
        parent::__construct($model);
    }

    public function getPaginated(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->model->orderBy('terjadi_pada', 'desc');

        if (!empty($filters['jenis'])) {
            $query->where('jenis_bencana', $filters['jenis']);
        }

        if (!empty($filters['days'])) {
            $query->where('terjadi_pada', '>=', now()->subDays((int)$filters['days']));
        }

        if (!empty($filters['sumber'])) {
            $query->where('sumber_api', $filters['sumber']);
        }

        if (isset($filters['min_magnitude'])) {
            $query->where('magnitude', '>=', (float) $filters['min_magnitude']);
        }

        return $query->paginate($perPage);
    }

    public function getActiveInDays(int $days = 30): Collection
    {
        return $this->model->where('terjadi_pada', '>=', now()->subDays($days))
            ->orderBy('terjadi_pada', 'desc')
            ->get();
    }

    public function getNearby(float $lat, float $lng, float $radius = 100.0, int $days = 30): Collection
    {
        return $this->model->where('terjadi_pada', '>=', now()->subDays($days))
            ->orderBy('terjadi_pada', 'desc')
            ->get()
            ->filter(function ($b) use ($lat, $lng, $radius) {
                $distance = BmkgService::haversineDistance($lat, $lng, (float) $b->latitude, (float) $b->longitude);
                $b->distance_km = $distance;
                return $distance <= $radius;
            })
            ->sortBy('distance_km')
            ->values();
    }

    public function getCountToday(): int
    {
        return $this->model->whereDate('terjadi_pada', today())->count();
    }

    public function getTotalCount(): int
    {
        return $this->model->count();
    }

    public function getLatest(int $limit = 5): Collection
    {
        return $this->model->orderBy('terjadi_pada', 'desc')
            ->limit($limit)
            ->get();
    }

    public function existsByEventId(string $eventId): bool
    {
        return $this->model->where('event_id', $eventId)->exists();
    }
}
