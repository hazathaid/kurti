<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Kurti;
use App\Models\User;
use App\Models\KurtiGroup;
use Illuminate\Support\Facades\Log;



class KurtiController extends Controller
{
    public function create(User $murid)
    {
        return view('kurtis.create', compact('murid'));
    }

    public function store(Request $request)
    {
        foreach ($request->kurtis as $kurtiData) {
            // cari atau buat group berdasarkan bulan & pekan
            $group = KurtiGroup::firstOrCreate([
                'bulan' => $kurtiData['bulan'],
                'pekan' => $kurtiData['pekan'],
            ]);

            Kurti::create([
                'murid_id'      => $request->murid_id,
                'classroom_id'  => $request->classroom_id,
                'kurti_group_id'=> $group->id,
                'aktivitas'     => $kurtiData['aktivitas'] ?? null,
                'amanah_rumah'  => $kurtiData['amanah_rumah'] ?? null,
                'capaian'       => $kurtiData['capaian'] ?? null,
                'created_by'    => Auth::id(),
            ]);
        }

        return redirect()->route('dashboard')
            ->with('success', 'Data kurti berhasil disimpan.');
    }


    public function show($muridId, $groupId)
    {
        $group = KurtiGroup::with(['kurtis' => function($q) use ($muridId) {
            $q->where('murid_id', $muridId)->with('murid');
        }])->findOrFail($groupId);

        $murid = User::findOrFail($muridId);
        $user  = Auth::user();

        return view('kurtis.show', [
            'group'  => $group,
            'murid'  => $murid,
            'kurtis' => $group->kurtis,
            'user'   => $user,
        ]);
    }

    public function edit(Kurti $kurti)
    {
        return view('kurtis.edit', compact('kurti'));
    }

    public function update(Request $request, Kurti $kurti)
    {
        $request->validate([
            'pekan'        => 'required|string|max:50',
            'aktivitas'    => 'nullable|string',
            'amanah_rumah' => 'nullable|string',
            'capaian'      => 'nullable|string',
        ]);

        $kurti->update([
            'bulan'        => $request->bulan,
            'pekan'        => $request->pekan,
            'aktivitas'    => $request->aktivitas,
            'amanah_rumah' => $request->amanah_rumah,
            'capaian'      => $request->capaian,
        ]);

        return redirect()->route('kurtis.show', [
            'murid' => $kurti->murid_id,
            'pekan' => $kurti->pekan,
        ])->with('success', 'Data kurti berhasil diperbarui.');

    }

    public function destroy(Kurti $kurti)
    {
        $kurti->delete();

        return redirect()->route('kurtis.show', [
            'murid' => $kurti->murid_id,
            'pekan' => $kurti->pekan,
        ])->with('success', 'Data kurti berhasil dihapus.');
    }

    public function updateCatatan(Request $request, $id)
    {
        $request->validate([
            'catatan_orang_tua' => 'nullable|string|max:255'
        ]);

        $kurti = Kurti::findOrFail($id);
        $kurti->update([
            'catatan_orang_tua' => $request->catatan_orang_tua,
        ]);

        return back()->with('success', 'Catatan berhasil disimpan!');
    }

}
