<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Kurti;
use App\Models\User;
use App\Models\KurtiGroup;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;


class KurtiController extends Controller
{
    public function create(User $murid)
    {
        return view('kurtis.create', compact('murid'));
    }

    public function store(Request $request)
    {
        foreach ($request->kurtis as $kurtiData) {
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
        $kurti->load('group');
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
        $group = KurtiGroup::firstOrCreate([
                'bulan' => $request->bulan,
                'pekan' => $request->pekan,
        ]);
        $kurti->update([
            'kurti_group_id'=> $group->id,
            'aktivitas'    => $request->aktivitas,
            'amanah_rumah' => $request->amanah_rumah,
            'capaian'      => $request->capaian,
        ]);

        return redirect()->route('kurtis.show', [
            'murid' => $kurti->murid_id,
            'group' => $kurti->group->id,
        ])->with('success', 'Data kurti berhasil diperbarui.');

    }

    public function destroy(Kurti $kurti)
    {
        $kurti->load('group');
        $kurtiGroupId = $kurti->kurti_group_id;
        $muridId = $kurti->murid_id;
        $kurti->delete();

        if(Kurti::where('kurti_group_id', $kurtiGroupId)->where('murid_id', $muridId)->count() === 0) {
            return redirect()->route('dashboard')->with('success', 'Data kurti berhasil dihapus.');
        }else{
            return redirect()->route('kurtis.show', [
                'murid' => $kurti->murid_id,
                'group' => $kurtiGroupId,
            ])->with('success', 'Data kurti berhasil dihapus.');
        }
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

    public function downloadPdf($muridId, $groupId)
    {
        // Ambil murid
        $murid = User::findOrFail($muridId);

        // Ambil data dari tabel kurtis sesuai murid + filter pekan & bulan
        $laporan = Kurti::where('murid_id', $muridId)
            ->where('kurti_group_id', $groupId)
            ->get();
        $group = $laporan->first()?->group;
        $classroom = $murid->currentClassroom;

        $pdf = Pdf::loadView('kurtis.pdf', compact('murid','group','laporan','classroom'))->setPaper('a4', 'landscape');

        return $pdf->download("kurti-{$murid->name}-pekan-{$group->pekan}.pdf");
    }

public function rekap()
    {
        $fasil = auth()->user(); // asumsi fasil dari user login

        $classroom = $fasil->classrooms()
            ->with([
                'murid.kurtis.group' => function ($q) {
                    $q->orderBy('bulan', 'desc')
                      ->orderBy('pekan', 'asc');
                },
                'murid.kurtiSubmissions',
            ])
            ->first();

        $groupedByMurid = collect($classroom->murid)->map(function ($murid) {
            $bulanGroups = $murid->kurtis
                ->groupBy(fn($kurti) => optional($kurti->group)->bulan)
                ->map(function ($itemsByBulan) use ($murid) {
                    $pekanGroups = $itemsByBulan
                        ->groupBy(fn($kurti) => optional($kurti->group)->pekan)
                        ->map(function ($itemsByPekan) use ($murid) {
                            $group = $itemsByPekan->first()->group;

                            // cek submission
                            $hasSubmission = $murid->kurtiSubmissions
                                ->where('group_id', $group?->id)
                                ->isNotEmpty();

                            // cek catatan orang tua
                            $totalKurti = $itemsByPekan->count();
                            $filledNotes = $itemsByPekan
                                ->whereNotNull('catatan_orang_tua')
                                ->where('catatan_orang_tua', '!=', '')
                                ->count();

                            if ($hasSubmission || ($totalKurti > 0 && $filledNotes === $totalKurti)) {
                                $status = 'Sudah isi';
                            } elseif ($filledNotes > 0 && $filledNotes < $totalKurti) {
                                $status = 'On progress';
                            } else {
                                $status = 'Belum isi';
                            }

                            return (object) [
                                'group_id' => $group?->id,
                                'bulan'    => $group?->bulan,
                                'pekan'    => $group?->pekan,
                                'status'   => $status,
                            ];
                        })
                        ->values();

                    return (object) [
                        'bulan'  => optional($itemsByBulan->first()->group)->bulan,
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

        return view('kurtis.rekap', compact('groupedByMurid', 'classroom'));
    }

    public function downloadRekap()
    {
        $fasil = auth()->user();

        $classroom = $fasil->classrooms()
            ->with([
                'murid.kurtis.group',
                'murid.kurtiSubmissions',
            ])
            ->first();

        $groupedByMurid = $this->buildRekap($classroom);

        $pdf = Pdf::loadView('kurtis.rekap_pdf', compact('groupedByMurid', 'classroom'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download('rekap-kurti.pdf');
    }

    private function buildRekap($classroom)
    {
        return collect($classroom->murid)->map(function ($murid) {
            $bulanGroups = $murid->kurtis
                ->groupBy(fn($kurti) => optional($kurti->group)->bulan)
                ->map(function ($itemsByBulan) use ($murid) {
                    $pekanGroups = $itemsByBulan
                        ->groupBy(fn($kurti) => optional($kurti->group)->pekan)
                        ->map(function ($itemsByPekan) use ($murid) {
                            $group = $itemsByPekan->first()->group;

                            $hasSubmission = $murid->kurtiSubmissions
                                ->where('group_id', $group?->id)
                                ->isNotEmpty();

                            $totalKurti = $itemsByPekan->count();
                            $filledNotes = $itemsByPekan
                                ->whereNotNull('catatan_orang_tua')
                                ->where('catatan_orang_tua', '!=', '')
                                ->count();

                            if ($hasSubmission || ($totalKurti > 0 && $filledNotes === $totalKurti)) {
                                $status = 'Sudah isi';
                            } elseif ($filledNotes > 0 && $filledNotes < $totalKurti) {
                                $status = 'On progress';
                            } else {
                                $status = 'Belum isi';
                            }

                            return (object) [
                                'group_id' => $group?->id,
                                'bulan'    => $group?->bulan,
                                'pekan'    => $group?->pekan,
                                'status'   => $status,
                            ];
                        })
                        ->values();

                    return (object) [
                        'bulan'  => optional($itemsByBulan->first()->group)->bulan,
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
    }
}
