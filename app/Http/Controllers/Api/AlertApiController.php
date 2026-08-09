<?php

namespace App\Http\Controllers\Api;

use OpenApi\Annotations as OA;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use App\Repositories\Contracts\AlertRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Alerts",
 *     description="Riwayat dan manajemen peringatan bencana user"
 * )
 */
class AlertApiController extends Controller
{
    protected AlertRepositoryInterface $alertRepository;

    public function __construct(AlertRepositoryInterface $alertRepository)
    {
        $this->alertRepository = $alertRepository;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/alerts",
     *     summary="Daftar alert user",
     *     tags={"Alerts"},
     *     security={{"bearerAuth":{}}, {"apiKey":{}}},
     *     @OA\Parameter(name="jenis", in="query", required=false, @OA\Schema(type="string"), description="Filter berdasarkan jenis bencana"),
     *     @OA\Parameter(name="tanggal", in="query", required=false, @OA\Schema(type="string", format="date"), description="Filter berdasarkan tanggal"),
     *     @OA\Parameter(name="status", in="query", required=false, @OA\Schema(type="string", enum={"sent","read","dismissed"})),
     *     @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer", default=15)),
     *     @OA\Response(
     *         response=200,
     *         description="Daftar alert berhasil diambil",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index(Request $request)
    {
        $alerts = $this->alertRepository->getForUserPaginated(
            Auth::id(),
            $request->only(['jenis', 'tanggal', 'status']),
            $request->integer('per_page', 15)
        );

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

    /**
     * @OA\Get(
     *     path="/api/v1/alerts/unread",
     *     summary="Daftar alert yang belum dibaca",
     *     tags={"Alerts"},
     *     security={{"bearerAuth":{}}, {"apiKey":{}}},
     *     @OA\Response(response=200, description="Daftar alert unread")
     * )
     */
    public function unread()
    {
        $alerts = $this->alertRepository->getUnreadForUser(Auth::id(), 10)
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
            'status' => 'success',
            'data' => $alerts,
            'unread_count' => $this->alertRepository->getUnreadCountForUser(Auth::id()),
        ]);
    }

    /**
     * @OA\Patch(
     *     path="/api/v1/alerts/{id}/read",
     *     summary="Tandai alert sebagai sudah dibaca",
     *     tags={"Alerts"},
     *     security={{"bearerAuth":{}}, {"apiKey":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Alert ditandai dibaca"),
     *     @OA\Response(response=403, description="Unauthorized")
     * )
     */
    public function markAsRead(Alert $alert)
    {
        if ($alert->user_id !== Auth::id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized.',
            ], 403);
        }

        $updatedAlert = $this->alertRepository->markAsRead($alert->id, Auth::id());

        return response()->json([
            'status' => 'success',
            'message' => 'Alert ditandai telah dibaca.',
            'data' => $updatedAlert,
        ]);
    }

    /**
     * @OA\Patch(
     *     path="/api/v1/alerts/mark-all-read",
     *     summary="Tandai semua alert sebagai sudah dibaca",
     *     tags={"Alerts"},
     *     security={{"bearerAuth":{}}, {"apiKey":{}}},
     *     @OA\Response(response=200, description="Semua alert ditandai dibaca")
     * )
     */
    public function markAllRead()
    {
        $count = $this->alertRepository->markAllAsReadForUser(Auth::id());

        return response()->json([
            'status' => 'success',
            'message' => "{$count} alert ditandai telah dibaca.",
        ]);
    }
}
