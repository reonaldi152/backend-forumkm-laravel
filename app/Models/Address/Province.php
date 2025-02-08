<?php

namespace App\Models\Address;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Province extends Model
{
    use HasFactory;

    protected $fillable = [
        'external_id',
        'name',
    ];

    protected static function booted()
    {
        static::creating(function ($province) {
            $province->uuid = Str::uuid();
        });
    }
}
