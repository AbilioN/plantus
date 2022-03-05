<?php

namespace App\Repositories;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;

class UserRepository
{

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function createUser(array $data) : User
    {

        $user = $this->model->findByCpf($data['cpf']);
        if($user)
        {
            throw new Exception('Já existe usuário para este cpf');
        }

        
        $cpfValido = $this->validarCpf($data['cpf']);

        if($cpfValido)
        {
        
            $password = Hash::make($data['password']);
            $cpf = preg_replace( '/[^0-9]/is', '', $data['cpf'] );


            return $this->model->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'cpf' => $cpf,
                'password' => $password,
            ]);

        
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