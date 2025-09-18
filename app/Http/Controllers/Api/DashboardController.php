<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->type === 'orangtua') {
            return response()->json([
                'status' => 'success',
                'data'   => $this->kurtis_orang_tua($user)
            ]);
        } elseif ($user->type === 'fasil') {
            return response()->json([
                'status' => 'success',
                'data'   => $this->kurtis_fasil($user)
            ]);
        }

        return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
    }

    private function kurtis_orang_tua($parent)
    {
        return $parent->anakKurtis()
            ->with(['murid', 'group'])
            ->orderByDesc('created_at')
            ->get()
            ->groupBy(fn($item) => $item->murid->id)
            ->map(function ($muridGroup) {
                $murid = $muridGroup->first()->murid;

                return [
                    'murid_id' => $murid->id,
                    'nama'     => $murid->name,
                    'bulan'    => $muridGroup->first()->group->bulan,

                    // group per group_id (bulan+pekan)
                    'groups'   => $muridGroup
                        ->groupBy(fn($item) => $item->group->id. '-' . $k->murid_id)
                        ->map(function ($groupItems) {
                            $group = $groupItems->first()->group;

                            // hitung status group
                            $filledCount = $groupItems
                                ->whereNotNull('catatan_orang_tua')
                                ->where('catatan_orang_tua', '!=', '')
                                ->count();

                            $status = 'belum ada data';
                            if ($groupItems->isNotEmpty()) {
                                if ($filledCount === 0) {
                                    $status = 'belum diisi';
                                } elseif ($filledCount < $groupItems->count()) {
                                    $status = 'onprogress';
                                } else {
                                    $status = 'done';
                                }
                            }

                            return [
                                'group_id'    => $group->id,
                                'bulan'       => $group->bulan,
                                'pekan'       => $group->pekan,
                                'status'      => $status,

                                // hitungan buat dashboard
                                'total_aktivitas' => $groupItems->count(),
                                'sudah_diisi'     => $filledCount,
                                'belum_diisi'     => $groupItems->count() - $filledCount,

                                'items' => $groupItems->map(fn($item) => [
                                    'id'        => $item->id,
                                    'tanggal'   => $item->created_at->format('Y-m-d'),
                                    'aktivitas' => $item->aktivitas,
                                    'capaian'   => $item->capaian,
                                    'status'    => $item->status_grouped,
                                ])->values(),
                            ];
                        })->values(),
                ];
            })->values();
    }

    private function kurtis_fasil($fasil)
    {
        $classroom = $fasil->currentClassroom;

        $muridList = $classroom->murids()->with('kurtis.group')->get();
        $data = [];

        foreach ($muridList as $murid) {
            $groups = $murid->kurtiGroups ?? collect([]);

            if ($groups->isEmpty()) {
                $data[] = [
                    'classroom' => $classroom->name,
                    'murid_id' => $murid->id,
                    'current_classroom_id' => $classroom->id,
                    'murid_name' => $murid->name,
                    'groups' => [],
                ];
                continue;
            }

            $muridGroups = [];
            foreach ($groups as $group) {
                $pekans = $group->kurtis()
                    ->with('group:id,bulan,pekan')
                    ->get()
                    ->groupBy(fn ($k) => $k->group->bulan . '-' . $k->group->pekan . '-' . $k->murid_id);

                $pekanList = [];
                foreach ($pekans as $key => $pekanItems) {
                    $first = $pekanItems->first();
                    $pekanList[] = [
                        'bulan' => $first->group->bulan,
                        'pekan' => $first->group->pekan,
                        'jumlah' => $pekanItems->count(),
                        'items' => $pekanItems->map(function ($item) {
                            return [
                                'id' => $item->id,
                                'tanggal' => $item->created_at->format('Y-m-d'),
                                'aktivitas' => $item->aktivitas,
                                'capaian' => $item->capaian,
                            ];
                        })->values(),
                    ];
                }

                $muridGroups[] = [
                    'group_id' => $group->id,
                    'pekans' => $pekanList,
                ];
            }

            $data[] = [
                'classroom' => $classroom->name,
                'murid_id' => $murid->id,
                'murid_name' => $murid->name,
                'current_classroom_id' => $classroom->id,
                'groups' => $muridGroups,
            ];
        }

        return response()->json([
            'status' => 'success',
            'data' => array_values($data),
        ]);

    }
}
