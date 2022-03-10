<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = ['path' , 'extension' , 'document_category_id' , 'user_id'];
}
