<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Support\Facades\Auth;

class UserAddressRepository 
{

    private UserAddress $model;
    private User $user;
    public function __construct(UserAddress $model)
    {
        $this->model = $model;
        $this->user = Auth::user();
    }

    public function updateUserAddress(array $data)
    {

        // $this->userAlreadyHaveAddress();

        $address =  $this->model->create([
                'user_id' => $this->user->id,
                'street' => $data['street'],
                'number' => $data['number'],
                'cep' => $data['cep'],
                'neighborhood' => $data['neighborhood'],
                'state' => $data['state'],
                'city' => $data['city'],
                'adjunct' => $data['adjunct'],
        ]);

        dd($address);

    }

    public function userAlreadyHaveAddress()
    {
        return  $this->model->where('user_id' , $this->user->id)->first();
    }
}