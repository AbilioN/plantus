<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAmericanVisa extends Model
{
    protected $fillable = ['number' , 'date_emission' , 'expiration_date' , 'user_id'];
}
