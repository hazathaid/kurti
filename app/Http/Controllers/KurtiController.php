<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Kurti;
use App\Models\User;
use Illuminate\Support\Facades\Log;



class KurtiController extends Controller
{
    public function create(User $murid)
    {
        return view('kurtis.create', compact('murid'));
    }

    public function store(Request $request)
    {
        Log::info('Request store kurti:', $request->all());
        try {
            Log::info('Request store kurti:', $request->all());

            foreach ($request->kurtis as $kurti) {

                Kurti::create([
                    'murid_id'     => $request->murid_id,
                    'classroom_id' => $request->classroom_id,
                    'pekan'        => $kurti['pekan'],
                    'aktivitas'    => $kurti['aktivitas'] ?? null,
                    'amanah_rumah' => $kurti['amanah_rumah'] ?? null,
                    'capaian'      => $kurti['capaian'] ?? null,
                    'created_by'   => Auth::id(),
                ]);
            }

            return redirect()->route('dashboard')
                ->with('success', 'Data kurti berhasil disimpan.');
        } catch (\Exception $e) {
            Log::error('Error store kurti: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->withErrors(['msg' => 'Terjadi kesalahan: '.$e->getMessage()]);
        }
    }

    public function show($muridId, $pekan)
    {
        $murid = User::findOrFail($muridId);
        $user = Auth::user();
        $kurtis = Kurti::where('murid_id', $muridId)
            ->where('pekan', $pekan)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('kurtis.show', compact('murid', 'pekan','kurtis', 'user'));
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
