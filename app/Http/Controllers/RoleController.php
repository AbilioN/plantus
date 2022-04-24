<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidSubRoleException;
use App\Http\Responses\Response;
use App\Models\Roles;
use App\Models\SubRoles;
use App\User;
use App\Models\UserRoles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    public function insert(Request $request, $user_id = null)
    {
        if($user_id)
        {
            $user = User::find($user_id);
        }else{
            $user = Auth::user();
        }

        $data = $request->all();

        $role = Roles::find($data['role_id'])->first();

        if(isset($data['sub_role_id']))
        {
            $subrole = SubRoles::where('role_id', $role->id)->get();
            if(!$subrole)
            {
                throw new InvalidSubRoleException('Não encontrado atuação para esta categoria');
            }

            $subrole = $subrole->id;
        
        }else{
            $subrole = null;
        }


        $createdUserRole = UserRoles::create([
                'user_id' => $user->id,
                'role_id' => $role->id,
                'sub_role_id' => $subrole
        ]);

        if($createdUserRole)
        {
            $responseArray = collect($createdUserRole)->toArray();
            Response::success($responseArray);
        }
        
    }
}
