<?php

namespace App\Models\Address;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'province_id',
        'external_id',
        'name',
    ];

    public function province()
    {
        return $this->belongsTo(\App\Models\Address\Province::class);
    }

    public function getApiResponseAttribute()
    {
        return [
            'uuid' => $this->uuid,
            'province' => $this->province->only(['uuid', 'name']),
            'external_id' => $this->external_id,
            'name' => $this->name,
        ];
    }

    protected static function booted()
    {
        static::creating(function ($city) {
            $city->uuid = Str::uuid();
        });
    }
}
