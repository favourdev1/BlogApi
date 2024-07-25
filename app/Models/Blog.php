<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;
    protected $table = 'blogs';
    protected $fillable = ['title', 'description', 'image_url', 'user_id'];


    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    
     // Automatically delete associated posts when a blog is deleted
     protected static function boot()
     {
         parent::boot();
 
         static::deleting(function ($blog) {
             $blog->posts()->delete();
         });
     }

}
