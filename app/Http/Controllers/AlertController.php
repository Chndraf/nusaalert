<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Http\Traits\ApiResponseTrait;
use App\Repositories\Contracts\AlertRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlertController extends Controller
{
    use ApiResponseTrait;

    protected AlertRepositoryInterface $alertRepository;

    public function __construct(AlertRepositoryInterface $alertRepository)
    {
        $this->alertRepository = $alertRepository;
    }

    public function index(Request $request)
    {
        $alerts = $this->alertRepository->getForUserPaginated(
            Auth::id(),
            $request->only(['jenis', 'tanggal']),
            15
        );

        if ($this->wantsJson($request)) {
            return response()->json([
                'status' => 'success',
                'data' => $alerts->items(),
                'meta' => [
                    'total' => $alerts->total(),
                    'page' => $alerts->currentPage(),
                    'per_page' => $alerts->perPage(),
                    'last_page' => $alerts->lastPage(),
                ],
            ]);
        }

        return view('alerts.index', compact('alerts'));
    }

    public function markAsRead(Request $request, $id)
    {
        $alert = $this->alertRepository->find($id);

        if (!$alert || $alert->user_id !== Auth::id()) {
            if ($this->wantsJson($request)) {
                return response()->json(['status' => 'error', 'message' => 'Unauthorized.'], 403);
            }
            abort(403);
        }

        $updatedAlert = $this->alertRepository->markAsRead($id, Auth::id());

        return $this->respondWithSuccessOrBack($request, 'Alert ditandai telah dibaca.', ['alert' => $updatedAlert]);
    }

    public function markAllRead(Request $request)
    {
        $count = $this->alertRepository->markAllAsReadForUser(Auth::id());

        return $this->respondWithSuccessOrBack($request, "Semua alert ditandai telah dibaca. ({$count} alert)");
    }

    /**
     * AJAX endpoint: return latest unread alerts for notification popup
     */
    public function latestAlerts()
    {
        $alerts = $this->alertRepository->getUnreadForUser(Auth::id(), 5)
            ->map(fn($a) => [
                'id' => $a->id,
                'jenis' => $a->bencana->jenis_bencana ?? 'unknown',
                'wilayah' => $a->bencana->wilayah ?? '',
                'magnitude' => $a->bencana->magnitude,
                'jarak_km' => $a->jarak_km,
                'lokasi_nama' => $a->lokasi->nama_lokasi ?? '',
                'created_at' => $a->created_at->diffForHumans(),
            ]);

        return response()->json([
            'alerts' => $alerts,
            'count' => $alerts->count(),
        ]);
    }
}
