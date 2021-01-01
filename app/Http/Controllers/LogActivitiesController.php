<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LogActivities;
use Illuminate\Support\Facades\Auth;

class LogActivitiesController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.verify');
    }    

    public function index()
    {
        $log = LogActivities::with([
                                    'users' => function($user){
                                        $user->select('idusers','name','email');
                                    }
                                ])
                                ->where('idusers',Auth::user()->idusers)
                                ->latest()
                                ->get();
                                
        return response()->json(['log' => $log ], 200);

    }
}
