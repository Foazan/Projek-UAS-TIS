<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Art extends Model
{
    protected $table = 'arts';
    protected $fillable = 
    [
    'title',
    'description',
    'image_url',
    'public_id',
    'user_id'
    ];

}