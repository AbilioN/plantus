<?php

namespace App\Repositories;

use App\Exceptions\InvalidUserRoleException;
use App\Exceptions\UserCpfAlreadyExistsException;
use App\Exceptions\UserEmailAlreadyExistsException;
use App\Http\Responses\Response;
use App\User;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Hash;

class UserRepository
{

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function createUser(array $data)
    {

        $user = $this->model->findByCpf($data['cpf']);
        if($user)
        {
            return Response::badRequest('Já existe usuário para este cpf');
        }
        $user = $this->model->where('email', $data['email'])->first();
        if($user)
        {
            return Response::badRequest('Já existe usuário para este email');

        }

        // if(!isset($data['role_id']))
        // {
        //     throw new InvalidUserRoleException('role_id não encontrado');
        // }
        
        $cpfValido = $this->validarCpf($data['cpf']);
        if($cpfValido)
        {
        
            $password = Hash::make($data['password']);

            $cpf = preg_replace( '/[^0-9]/is', '', $data['cpf'] );

            

            try{    
                return $this->model->create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'cpf' => $cpf,
                    'password' => $password,
                    // 'role_id' => $data['role_id'],
                ]);

            
    
            }catch(Exception $e)
            {
                Response::badRequest($e->getMessage());
            }
   
        
        }

        
    }

    function validarCPF($cpf) {
 
        // Extrai somente os números
        $cpf = preg_replace( '/[^0-9]/is', '', $cpf );
         
        // Verifica se foi informado todos os digitos corretamente
        if (strlen($cpf) != 11) {
            return false;
        }
    
        // Verifica se foi informada uma sequência de digitos repetidos. Ex: 111.111.111-11
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }
    
        // Faz o calculo para validar o CPF
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }
        return true;
    
    }
}