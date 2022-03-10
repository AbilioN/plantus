<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class UserAddress extends Model
{
     // private User $user;

     protected $table = 'user_addresses';
     protected $fillable  = [ 'user_id' , 'street' , 'number' , 'cep' , 'neighborhood' , 'state' , 'city' ,'adjunct'];

     public function __construct()
     {
          // $this->user = Auth::user();
          // $this->user = User::find(1);
     }

     // public function createUserAddress(array $data) {


     //      $this->create([
     //           'user_id' => 1,
     //           'street' => $data['street'],
     //           'number' => $data['number'],
     //           'cep' => $data['cep'],
     //           'neighborhood' => $data['neighborhood'],
     //           'state' => $data['state'],
     //           'city' => $data['city'],
     //           'adjunct' => $data['adjunct'],
     //   ]);
     // }
}
