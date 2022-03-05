<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
     protected $fillable = ['user_id' , 'street' , 'number' , 'cep' , 'neighborhood' , 'state' , 'city' ,'adjunct'];
}
