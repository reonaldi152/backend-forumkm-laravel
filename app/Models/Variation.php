<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Variation extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name',
        'values',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getApiResponseAttribute()
    {
        return [
            'name' => $this->name,
            'values' => json_decode($this->values),
        ];
    }

    public function setValuesAttribute($value)
    {
        $this->attributes['values'] = json_encode($value);
    }

}
