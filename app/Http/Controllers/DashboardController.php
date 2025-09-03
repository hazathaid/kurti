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

    private function kurtis_orang_tua($parent){
        $kurtis = $parent->anakKurtis()
            ->with('murid')
            ->orderBy('pekan', 'desc')
            ->get()
            ->groupBy(fn($item) => $item->murid->name) // group per murid
            ->map(function ($muridGroup) {
                return $muridGroup->groupBy('pekan')
                    ->map(function ($pekanGroup) {
                        return (object) [
                            'pekan'      => $pekanGroup->first()->pekan,
                            'created_at' => $pekanGroup->first()->created_at,
                            'items'      => $pekanGroup,
                        ];
                    });
            });

        return view('dashboard.orangtua', compact('kurtis'));
    }

    private function kurtis_fasil($fasil){
        $classroom = $fasil->classrooms()
            ->with(['murid.kurtis' => function ($q) {
                $q->orderBy('pekan', 'desc');
            }])
            ->first();
            $groupedByMurid = collect($classroom->murid)
                ->map(function ($murid) {
                    return (object) [
                        'murid_id'   => $murid->id,
                        'murid_name' => $murid->name,
                        'pekan'      => $murid->kurtis
                            ->groupBy('pekan')
                            ->map(function ($pekanGroup) {
                                return (object) [
                                    'pekan'      => $pekanGroup->first()->pekan,
                                    'created_at' => $pekanGroup->first()->created_at,
                                    'items'      => $pekanGroup,
                                ];
                            }),
                    ];
                });

        return view('dashboard.fasil', compact('classroom', 'groupedByMurid'));
    }

}
