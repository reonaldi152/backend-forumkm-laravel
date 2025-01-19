<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{
    protected $fillable = [
       
        'name',
        'email',
        'store_name',
        'phone',
        'gender',
        'birth_date',
        'photo',
        'otp_register',
        'email_verified_at',
        'password',
    ];
}
