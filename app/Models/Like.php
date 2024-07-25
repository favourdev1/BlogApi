<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;
    
   protected $fillable = ['post_id', 'user_id'];

    /**
     * Get the post that owns the like.
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

}
