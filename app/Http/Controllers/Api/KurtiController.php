<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KurtiGroup;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class KurtiController extends Controller
{
    public function show($muridId, $groupId)
    {
        $group = KurtiGroup::with(['kurtis' => function ($q) use ($muridId) {
            $q->where('murid_id', $muridId)->with('murid');
        }])->findOrFail($groupId);

        $murid = User::findOrFail($muridId);
        $user  = Auth::user();

        return response()->json([
            'status' => 'success',
            'group'  => [
                'id'    => $group->id,
                'bulan' => $group->bulan,
                'pekan' => $group->pekan,
            ],
            'murid'  => [
                'id'   => $murid->id,
                'name' => $murid->name,
            ],
            'kurtis' => $group->kurtis->map(function ($kurti) {
                return [
                    'id'              => $kurti->id,
                    'aktivitas'       => $kurti->aktivitas,
                    'capaian'         => $kurti->capaian,
                    'amanah_rumah'    => $kurti->amanah_rumah,
                    'catatan_orangtua'=> $kurti->catatan_orangtua,
                    'created_at'      => $kurti->created_at->format('Y-m-d H:i:s'),
                ];
            }),
            'user'   => [
                'id'   => $user->id,
                'name' => $user->name,
                'type' => $user->type,
            ],
        ]);
    }
}
