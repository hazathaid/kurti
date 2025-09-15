<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kurti;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if($user->type === 'orangtua') {
            return $this->kurtis_orang_tua($user);
        }elseif($user->type === 'fasil') {
            return $this->kurtis_fasil($user);
        }
    }

    private function kurtis_orang_tua($parent)
    {
        $anakIds = $parent->anak()->pluck('users.id');

        $kurtis = Kurti::whereIn('murid_id', $anakIds)
            ->with(['murid', 'group'])
            ->orderByDesc('created_at')
            ->get()
            ->groupBy(fn($item) => $item->murid->name) // group per murid
            ->map(function ($muridGroup) {
                return $muridGroup
                    ->groupBy(fn($item) => $item->group->bulan) // group per bulan
                    ->map(function ($bulanGroup) {
                        return $bulanGroup
                            ->groupBy(fn($item) => $item->group->id) // group per pekan (pakai group id)
                            ->map(function ($pekanItems) {
                                $group = $pekanItems->first()->group;
                                return (object) [
                                    'group_id' => $group->id,
                                    'bulan'    => $group->bulan,
                                    'pekan'    => $group->pekan,
                                    'items'    => $pekanItems,
                                ];
                            });
                    });
            });
        return view('dashboard.orangtua', compact('kurtis'));
    }

    private function kurtis_fasil($fasil)
    {
        $classroom = $fasil->classrooms()
            ->with(['murid.kurtis.group' => function ($q) {
                $q->orderBy('bulan', 'desc')
                ->orderBy('pekan', 'asc');
            }])
            ->first();

        $groupedByMurid = collect($classroom->murid)->map(function ($murid) {
            // group by bulan (ambil dari relasi group)
            $bulanGroups = $murid->kurtis
                ->groupBy(fn($kurti) => optional($kurti->group)->bulan)
                ->map(function ($itemsByBulan) {
                    // dalam setiap bulan, group by pekan
                    $pekanGroups = $itemsByBulan
                        ->groupBy(fn($kurti) => optional($kurti->group)->pekan)
                        ->map(function ($itemsByPekan) {
                            $group = $itemsByPekan->first()->group;

                            return (object) [
                                'group_id' => $group?->id,
                                'pekan'    => $group?->pekan,
                                'items' => $itemsByPekan,
                            ];
                        })
                        ->values();

                    return (object) [
                        'bulan' => optional($itemsByBulan->first()->group)->bulan,
                        'pekans' => $pekanGroups,
                    ];
                })
                ->values();

            return (object) [
                'murid_id'   => $murid->id,
                'murid_name' => $murid->name,
                'groups'     => $bulanGroups,
            ];
        });

        return view('dashboard.fasil', compact('classroom', 'groupedByMurid'));
    }


}
