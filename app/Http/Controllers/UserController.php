<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidCpfException;
use App\Exceptions\UserCpfAlreadyExistsException;
use App\Exceptions\UserEmailAlreadyExistsException;
use App\Http\Requests\CreateUserRequest;
use App\Http\Responses\Response;
use App\Models\UserRoles;
use App\Repositories\UserRepository;
use App\Services\CpfValidator;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\UsersTeam;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{

    public function __construct(UserRepository $repo)
    {
        $this->repo = $repo;
    }
    public function create(Request $request)
    { 

        DB::beginTransaction();
        

        $rules = [
            'name' => 'required|string',
            'email' => 'required|email',
            'cpf' => 'required|string|max:11|min:11',
            'password' => 'required|string'
        ];
        $message = [
            'name.required' => 'é necessário informar o nome',
            'email.required' => 'é necessário informar o email',
            'cpf.required' => 'é necessário informar o cpf',
            'password.required' => 'é necessário informar o password',

        ];

        $validator = Validator::make($request->all() , $rules , $message);

        if($validator->fails()) {
            return Response::badRequest($validator->errors());
        }
        


        $data = $request->all();

        $user = User::where(['cpf' => $data['cpf']])->first();
        if($user)
        {
            
            throw new UserCpfAlreadyExistsException('Já existe usuário para este cpf');
            // return Response::badRequest(['error' => 'Já existe usuário para este cpf']);

        }

        $user = User::where(['email' => $data['email']])->first();
        if($user)
        {

            throw new UserEmailAlreadyExistsException('Já existe usuário para este email');
        }


        $cpfValido = CpfValidator::validateCpf($data['cpf']);
        if($cpfValido)
        {

            $password = Hash::make($data['password']);

            $cpf = CpfValidator::removeMask($data['cpf']);


            $createdUser = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'cpf' => $cpf,
                'password' => $password,
            ]);


            if($createdUser)
            {
                $userTeam = UsersTeam::firstOrCreate([
                    'team_id' => 1, //por enquanto so tem o plantus, quando houver outros necessario mudar aqui
                    'user_id' => $createdUser->id,
                ]);
                
                if(isset($data['role_id']))
                {
                    UserRoles::create([
                        'user_id' => $createdUser->id,
                        'role_id' => $data['role_id'],
                        'sub_role_id' => null
                    ]);
                }

                $responseArray = collect($createdUser)->toArray();
                DB::commit();
                return Response::success($responseArray);
            }


        }else{
            throw new InvalidCpfException('Cpf Inválido');
        }
        DB::rollback();
    }
}
