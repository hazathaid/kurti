<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserDevices;


class UserDeviceController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string'
        ]);

        $user  = Auth::user();
        UserDevices::updateOrCreate(
            ['fcm_token' => $request->fcm_token],
            ['user_id' => $user->id],
        );

        return response()->json(['status' => 'success']);
    }
}
