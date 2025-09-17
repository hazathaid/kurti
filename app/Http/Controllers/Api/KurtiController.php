<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kurti;
use App\Models\KurtiGroup;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class KurtiController extends Controller
{
    public function show($muridId, $groupId)
    {
        $group = KurtiGroup::with([
            'kurtis' => function($q) use ($muridId) {
                $q->where('murid_id', $muridId)->with('murid');
            }
        ])->findOrFail($groupId);

        $murid = User::findOrFail($muridId);
        $user  = Auth::user();

        return response()->json([
            'group' => [
                'id'     => $group->id,
                'bulan'  => $group->bulan,
                'pekan'  => $group->pekan,
                'kurtis' => $group->kurtis->map(function ($kurti) {
                    return [
                        'id'          => $kurti->id,
                        'aktivitas'   => $kurti->aktivitas,
                        'amanah_rumah'=> $kurti->amanah_rumah,
                        'capaian'     => $kurti->capaian,
                        'catatan_orangtua'      => $kurti->catatan_orangtua ?? null,
                        'murid'       => [
                            'id'   => $kurti->murid->id,
                            'name' => $kurti->murid->name,
                            'current_classroom_id' => $kurti->murid->current_classroom_id,
                        ],
                    ];
                }),
            ],
            'murid' => [
                'id'   => $murid->id,
                'name' => $murid->name,
            ],
            'user' => [
                'id'   => $user->id,
                'name' => $user->name,
            ]
        ]);
    }

    public function updateCatatan(Request $request, $id)
    {
        $kurti = Kurti::findOrFail($id);
        $kurti->catatan_orang_tua = $request->input('catatan_orangtua');
        $kurti->save();

        return response()->json([
            'status' => 'success',
            'data'   => $kurti
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
                'murid_id' => 'required|exists:users,id',
                'classroom_id' => 'required|exists:classrooms,id',
                'kurtis'   => 'required|array|min:1',
                'kurtis.*.aktivitas' => 'required|string|max:255',
                'kurtis.*.amanah_rumah' => 'nullable|string|max:255',
                'kurtis.*.capaian' => 'nullable|string|max:255',
            ]);

            $saved = [];
            foreach ($request->kurtis as $k) {
                $group = KurtiGroup::firstOrCreate([
                    'bulan' => $k['bulan'],
                    'pekan' => $k['pekan'],
                ]);
                $saved[] = Kurti::create([
                    'murid_id'      => $request->murid_id,
                    'kurti_group_id'=> $group->id,
                    'aktivitas'     => $k['aktivitas'],
                    'amanah_rumah'  => $k['amanah_rumah'] ?? null,
                    'capaian'       => $k['capaian'] ?? null,
                    'classroom_id'  => $request->classroom_id,
                    'created_by'    => Auth::id(),
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Semua kurti berhasil dibuat',
                'data' => $saved,
            ]);
    }

}
