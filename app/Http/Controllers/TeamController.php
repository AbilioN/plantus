<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    
    public function show(Request $request)
    {
        $teams = Team::all();
        dd($teams);
    }
}
