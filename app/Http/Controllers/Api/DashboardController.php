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
            ->with(['murid', 'kurtiGroup'])
            ->orderByDesc('created_at')
            ->get()
            ->groupBy(fn($item) => $item->murid->name)
            ->map(function ($muridGroup) {
                return $muridGroup->groupBy(fn($item) => $item->kurtiGroup->id)
                    ->map(function ($groupItems) {
                        $group = $groupItems->first()->kurtiGroup;

                        return [
                            'bulan'    => $group->bulan,
                            'group_id' => $group->id,
                            'pekan'    => $group->pekan,
                            'items'    => $groupItems->map(fn($item) => [
                                'id'       => $item->id,
                                'tanggal'  => $item->created_at->format('d M Y'),
                                'status'   => $item->status,
                            ]),
                        ];
                    })->values();
            });
    }

    private function kurtis_fasil($fasil)
    {
        $classroom = $fasil->classrooms()
            ->with(['murid.kurtis.group' => function ($q) {
                $q->orderBy('bulan', 'desc')->orderBy('pekan', 'asc');
            }])
            ->first();

        return collect($classroom->murid)->map(function ($murid) {
            $bulanGroups = $murid->kurtis
                ->groupBy(fn($kurti) => optional($kurti->group)->bulan)
                ->map(function ($itemsByBulan) {
                    $pekanGroups = $itemsByBulan
                        ->groupBy(fn($kurti) => optional($kurti->group)->pekan)
                        ->map(function ($itemsByPekan) {
                            $group = $itemsByPekan->first()->group;

                            return [
                                'group_id' => $group?->id,
                                'pekan'    => $group?->pekan,
                                'items' => $itemsByPekan->map(fn($item) => [
                                    'id'       => $item->id,
                                    'tanggal'  => $item->created_at->format('d M Y'),
                                    'status'   => $item->status,
                                ]),
                            ];
                        })->values();

                    return [
                        'bulan'  => optional($itemsByBulan->first()->group)->bulan,
                        'pekans' => $pekanGroups,
                    ];
                })->values();

            return [
                'murid_id'   => $murid->id,
                'murid_name' => $murid->name,
                'groups'     => $bulanGroups,
            ];
        });
    }
}
