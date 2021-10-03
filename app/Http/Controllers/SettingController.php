<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class SettingController extends Controller
{
    //
    public function history()
    {
        $user = auth()->user();
        $history = $user->histories;
        return view('history',compact('history'));
    }

    public function individual($startDate,$endDate)
    {
        $user = auth()->user();
        $history = $user->histories->where('status','success')->whereBetween('created_at', [$startDate, $endDate])->sum('cpi');
        return $history;
        // return view('history',compact('history'));

    }

    public function money($id,$startDate,$endDate)
    {
        $user = User::findOrFail($id);
        $history = $user->histories->where('status','success')->whereBetween('created_at', [$startDate, $endDate])->all();
        // return $history;
        // return view('history',compact('history'));
        foreach ($history as $key => $value) {
            # code...
            echo $value['uuid']."<br>";
        }

    }
}
