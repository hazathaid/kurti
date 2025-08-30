<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kurti;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
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

}
