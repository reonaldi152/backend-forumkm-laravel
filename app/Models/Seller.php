<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Seller extends Model
{
    use HasFactory, HasApiTokens;
    
    protected $fillable = [
       
        'name',
        'email',
        'store_name',
        'phone',
        'gender',
        'birth_date',
        'photo',
        'otp_register',
        'otp_expired',
        'verified_at',
        'status',
        'password',
    ];
}
