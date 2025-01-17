<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\CustomVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail);
    }
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }
    public function wishlist()
    {
        return $this->hasMany(Wishlist::class);
    }
    public function courses()
    {
        return $this->hasMany(Course::class);
    }
    public function cartitem()
    {
        return $this->hasMany(CartItem::class);
    }
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
    public function mycourses()
    {
        return $this->hasMany(MyCourse::class);
    }



}
