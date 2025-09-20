<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KurtiSubmission;
use App\Models\User;
use App\Models\KurtiGroup;

class KurtiSubmissionController extends Controller
{

    public function store(Request $request)
    {
        $request->validate([
            'murid_id' => 'required|exists:users,id',
            'kurti_group_id' => 'required|exists:kurti_groups,id',
            'file_path' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $path = $request->file('file_path')->store('kurti_submissions', 'public');

        KurtiSubmission::create([
            'murid_id' => $request->murid_id,
            'kurti_group_id' => $request->kurti_group_id,
            'file_path' => $path,
        ]);

        return redirect()->route('kurtis.show', [
            'murid' => $request->murid_id,
            'group' => $request->kurti_group_id,
            ])->with('success', 'Foto berhasil diupload.');
    }
}
