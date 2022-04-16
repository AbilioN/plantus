<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubRoles extends Model
{
    //

    protected $fillable = ['sub_role'];

    protected $hidden = ['created_at' , 'updated_at'];


}
