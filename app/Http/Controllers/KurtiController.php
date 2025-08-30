<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Kurti;

class KurtiController extends Controller
{
    public function index()
    {
        $parent = Auth::user();

        $kurtis = $parent->anakKurtis()
            ->with('murid')
            ->orderBy('pekan', 'desc')
            ->get()
            ->groupBy(function ($item) {
                return $item->murid->name; // group per murid
            })
            ->map(function ($group) {
                return $group->groupBy('pekan'); // di dalam murid, group per pekan
            });

            return view('dashboard', compact('kurtis'));
    }
    public function show($muridId, $pekan)
    {
        $kurti = Kurti::with('murid')
            ->where('murid_id', $muridId)
            ->where('pekan', $pekan)
            ->get();

        return view('kurtis.show', compact('kurti'));
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
