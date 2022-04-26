<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\UsersTeam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeamController extends Controller
{
    
    public function show(Request $request)
    {

        $outputArray = [];
        $teams = Team::all();
        foreach($teams as $team)
        {
            $usersTeam = DB::table('users_teams')
                            ->select(
                                'users.name',
                                'users.email',
                                'users.phone',
                                'users.whatsapp',
                                'roles.role'

                            )
                            ->join('users', 'users.id', '=' , 'users_teams.user_id')
                            ->join('user_roles', 'user_roles.user_id', '=' , 'users.id')
                            ->join('roles', 'user_roles.role_id', '=' ,'roles.id')
                            ->get();
            
            $outputArray[$team->team] = collect($usersTeam)->toArray();
        }

        
        return response()->json($outputArray);

    }
}
