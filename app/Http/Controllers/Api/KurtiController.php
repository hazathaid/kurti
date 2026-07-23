<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kurti;
use App\Models\KurtiGroup;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class KurtiController extends Controller
{
    public function show($muridId, $groupId)
    {
        $user = Auth::user();
        $murid = User::whereKey($muridId)
            ->where('type', 'murid')
            ->firstOrFail();

        if ($user->type === 'orangtua') {
            abort_unless($user->anak()->whereKey($murid->id)->exists(), 403);
        } elseif ($user->type === 'fasil') {
            abort_unless(
                $user->current_classroom_id !== null
                && (string) $murid->current_classroom_id === (string) $user->current_classroom_id,
                403
            );
        } else {
            abort(403);
        }

        $group = KurtiGroup::with([
            'kurtis' => function($q) use ($muridId) {
                $q->where('murid_id', $muridId)->with('murid');
            }
        ])->whereKey($groupId)
            ->whereHas('kurtis', fn ($query) => $query->where('murid_id', $muridId))
            ->firstOrFail();

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
                        'catatan_orangtua'      => $kurti->catatan_orang_tua,
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
        $user = Auth::user();

        abort_unless($user->type === 'orangtua', 403);

        $kurti = Kurti::findOrFail($id);

        abort_unless($user->anak()->whereKey($kurti->murid_id)->exists(), 403);

        $validated = $request->validate([
            'catatan_orangtua' => 'present|nullable|string|max:255',
        ]);

        $kurti->catatan_orang_tua = $validated['catatan_orangtua'];
        $kurti->save();

        app(NotificationController::class)->sendToUsers(
            [$kurti->created_by],
            'Catatan orang tua diperbarui',
            "Catatan untuk {$kurti->murid->name} telah disimpan.",
            [
                'muridId' => $kurti->murid_id,
                'groupId' => $kurti->kurti_group_id,
            ],
        );

        return response()->json([
            'status' => 'success',
            'data'   => $kurti
        ]);
    }


    public function store(Request $request)
    {
        $user = Auth::user();

        abort_unless($user->type === 'fasil', 403);

        $validated = $request->validate([
            'murid_id' => ['required', 'integer', 'exists:users,id'],
            'classroom_id' => ['required', 'integer', 'exists:classrooms,id'],
            'kurtis' => ['required', 'array', 'min:1'],
            'kurtis.*.bulan' => ['required', 'date_format:Y-m'],
            'kurtis.*.pekan' => ['required', 'integer', 'between:1,5'],
            'kurtis.*.aktivitas' => ['required', 'string', 'max:255'],
            'kurtis.*.amanah_rumah' => ['nullable', 'string', 'max:255'],
            'kurtis.*.capaian' => ['nullable', 'string', 'max:255'],
        ]);

        $classroomId = $user->current_classroom_id;

        abort_unless(
            $classroomId !== null
            && (string) $validated['classroom_id'] === (string) $classroomId,
            403
        );

        abort_unless(
            User::whereKey($validated['murid_id'])
                ->where('type', 'murid')
                ->where('current_classroom_id', $classroomId)
                ->exists(),
            403
        );

        $saved = DB::transaction(function () use ($validated) {
            $saved = [];

            foreach ($validated['kurtis'] as $k) {
                $group = KurtiGroup::firstOrCreate([
                    'bulan' => $k['bulan'],
                    'pekan' => $k['pekan'],
                ]);
                $saved[] = Kurti::create([
                    'murid_id' => $validated['murid_id'],
                    'kurti_group_id' => $group->id,
                    'aktivitas' => $k['aktivitas'],
                    'amanah_rumah' => $k['amanah_rumah'] ?? null,
                    'capaian' => $k['capaian'] ?? null,
                    'classroom_id' => $validated['classroom_id'],
                    'created_by' => Auth::id(),
                ]);
            }

            return $saved;
        });

        $parentIds = User::findOrFail($validated['murid_id'])
            ->orangTua()
            ->pluck('users.id')
            ->all();

        foreach (collect($saved)->unique('kurti_group_id') as $kurti) {
            app(NotificationController::class)->sendToUsers(
                $parentIds,
                'Kurti baru tersedia',
                'Aktivitas Kurti baru telah ditambahkan.',
                [
                    'muridId' => $kurti->murid_id,
                    'groupId' => $kurti->kurti_group_id,
                ],
            );
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Semua kurti berhasil dibuat',
            'data' => $saved,
        ]);
    }

}
