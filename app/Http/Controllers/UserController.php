<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepository;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function __construct(UserRepository $repo)
    {
        $this->repo = $repo;
    }
    public function create(Request $request)
    { 

        try {   

            $data = $request->all();
            $user = $this->repo->createUser($data);
            return response()->json($user);
        }catch(Exception $e) {
            
            $return = [
                'error' => $e->getMessage(),
            ];
            return response()->json($return, 500);
        }
        
    
    }
}
