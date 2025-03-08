<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'code',
        'name',
        'is_public',
        'discount_cashback_type',
        'discount_cashback_value',
        'discount_cashback_max',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'discount_cashback_value' => 'float',
        'discount_cashback_max' => 'float',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    // public function orders()
    // {
    //     return $this->hasMany(Order::class);
    // }

    public function scopeActive($query)
    {
        return $query->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function getUsedCountAttribute()
    {
        return 0;
    }

    public function getApiResponseSellerAttribute()
    {
        return [
            'uuid' => $this->uuid,
            'code' => $this->code,
            'name' => $this->name,
            'used_count' => $this->used_count,
            'is_public' => $this->is_public,
            'voucher_type' => $this->voucher_type,
            'discount_cashback_type' => $this->discount_cashback_type,
            'discount_cashback_value' => $this->discount_cashback_value,
            'discount_cashback_max' => $this->discount_cashback_max,
            'start_date' => $this->start_date->format('Y-m-d H:i:s'),
            'end_date' => $this->end_date->format('Y-m-d H:i:s'),
            'seller' => $this->seller ? $this->seller->api_response_as_seller : null,
        ];
    }

    public function getApiResponseAttribute()
    {
        return [
            'code' => $this->code,
            'name' => $this->name,
            'is_public' => $this->is_public,
            'voucher_type' => $this->voucher_type,
            'discount_cashback_type' => $this->discount_cashback_type,
            'discount_cashback_value' => $this->discount_cashback_value,
            'discount_cashback_max' => $this->discount_cashback_max,
            'start_date' => $this->start_date->format('Y-m-d H:i:s'),
            'end_date' => $this->end_date->format('Y-m-d H:i:s'),
            'seller' => $this->seller ? $this->seller->api_response_as_seller : null,
        ];
    }

}
