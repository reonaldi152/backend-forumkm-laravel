<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Panel;
use App\Models\Review;
use App\Models\Product;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->roles == 'admin';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'name',
        'email',
        'roles',
        'phone',
        'gender',
        'birth_date',
        'photo',
        'otp_register',
        'email_verified_at',
        'password',
        'social_media_provider',
        'social_media_id',
        'address',
        'link_gmaps'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getApiResponseAttribute()
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'photo_url' => $this->photo_url,
            'username' => $this->username,
            'phone' => $this->phone,
            'gender' => $this->gender,
            'birth_date' => $this->birth_date,
            'address' => $this->address,
            'link_gmaps' => $this->link_gmaps
        ];
    }

    public function getApiResponseAsSellerAttribute()
    {
        $productIds = $this->products()->pluck('id');

        return [
            'username' => $this->username,
            'store_name' => $this->store_name,
            'photo_url' => $this->photo_url,
            'product_count' => $this->products()->count(),
            'rating_count' => Review::whereIn('product_id', $productIds)->count(),
            'join_date' => $this->created_at->diffForHumans(),
            'send_form' => optional($this->addresses()->where('is_default', true)->first())->getApiResponseAttribute()
        ];
    }

    public function getApiResponseAsBuyerAttribute()
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'photo_url' => $this->photo_url,
            'username' => $this->username,
            'phone' => $this->phone,
        ];
    }

    public function getPhotoUrlAttribute()
    {
        if (is_null($this->photo)) {
            return null;
        }

        return asset('storage/' . $this->photo);
    }

    public function addresses()
    {
        return $this->hasMany(\App\Models\Address\Address::class);
    }

}
