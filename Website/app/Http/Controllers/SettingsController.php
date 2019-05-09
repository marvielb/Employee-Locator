<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserSetting;
use Illuminate\Support\Facades\DB;
use Auth;

class SettingsController extends Controller
{
    //
    public function ShowSettings()
    {
        return view('pages.settings');
    }

    public function SetToken(Request $request)
    {
        $token = $request->input('token');

        $id = Auth::id();
        return DB::table('user_settings')
                            ->where('user_id', $id)
                            ->update(['device_token' => $token]);
        /*
        $user = UserSetting::where('user_id', $id)->first();
        if(!$user)
        {
            return 'user is null';
        }
        //$user->user_id = $id;
        $user->device_token = $token;
        $user->save();
        return $user;*/
    }
}
