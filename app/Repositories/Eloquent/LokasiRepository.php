<?php

namespace App\Repositories\Eloquent;

use App\Models\Lokasi;
use App\Repositories\Contracts\LokasiRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class LokasiRepository extends BaseRepository implements LokasiRepositoryInterface
{
    public function __construct(Lokasi $model)
    {
        parent::__construct($model);
    }

    public function getForUser(int $userId): Collection
    {
        return $this->model->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function createForUser(int $userId, array $attributes): Lokasi
    {
        $attributes['user_id'] = $userId;
        return $this->model->create($attributes);
    }

    public function getActive(): Collection
    {
        return $this->model->where('is_active', true)->get();
    }

    public function toggleActive(int $lokasiId): ?Lokasi
    {
        $lokasi = $this->find($lokasiId);
        if ($lokasi) {
            $lokasi->update(['is_active' => !$lokasi->is_active]);
            return $lokasi->fresh();
        }
        return null;
    }
}
