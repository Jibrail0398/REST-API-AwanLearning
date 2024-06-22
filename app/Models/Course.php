<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'price',
        'image',
        'instructor_id',
        'pre_vidio',
        'category_id',
        'level_id',
        'status',
    ];
    protected $hidden = ['pivot'];

    public function owner()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'course_category', 'course_id', 'category_id');
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function level()
    {
        return $this->belongsTo(Level::class);
    }
    public function wishlist()
    {
        return $this->hasMany(Wishlist::class);
    }
    public function cartitem()
    {
        return $this->hasMany(cartitem::class);
    }
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
    public function mycourses()
    {
        return $this->hasMany(MyCourse::class);
    }
    public function contents()
    {
        return $this->hasMany(Content::class);
    }public function requirements()
    {
        return $this->hasMany(Requirement::class);
    }


}
