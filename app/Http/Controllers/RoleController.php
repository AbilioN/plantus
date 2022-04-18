<?php

namespace App\Http\Controllers;

use App\Models\Roles;
use App\Models\SubRoles;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    //

    public function show(Request $request)
    {
        
        $outputArray = [];
        $roles = Roles::all();
        
        foreach($roles as $key=>$role)
        {
            $subroles = SubRoles::where('role_id', $role->id)->get();
            $role = collect($role);
            $role = $role->except(['created_at', 'updated_at'])->toArray();
            if(count($subroles) > 0)
            {
                $subroles = collect($subroles);
                $role['subroles'] = $subroles->except(['created_at', 'updated_at'])->toArray();
            }

            $outputArray[] = $role;
        }

        return response()->json($outputArray);
    }
}
