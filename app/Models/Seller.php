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

    public function getApiResponseAttribute(){
        return [
            'name' => $this->name,
            'email' => $this->email,
            'store_name' => $this->store_name,
            'phone' => $this->phone,
            'gender' => $this->gender,
            'birth_date' => $this->birth_date,
            'photo' => $this->photo,
            'status' => $this->status,
        ];
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'seller_id');
    }
}
