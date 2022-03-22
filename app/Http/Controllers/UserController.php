<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    public function __construct(UserRepository $repo)
    {
        $this->repo = $repo;
    }
    public function create(Request $request)
    { 
        try {   

            $rules = [
                'name' => 'required|string',
                'email' => 'required|email',
                'cpf' => 'required|string|max:11|min:11',
                'password' => 'required|string'
            ];

            $validator = Validator::make($request->all() , $rules);
            if ($validator->fails()) {
                return response()->json(array('errors' => $validator->messages()));
              } else {
              }
            
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
