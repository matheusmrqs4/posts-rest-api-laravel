<?php

namespace App\Models;

use App\Models\User;
use App\Models\Comments;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'post_images',
        'users_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function comments()
    {
        return $this->hasMany(Comments::class, 'posts_id');
    }

    public function notification()
    {
        return $this->hasOne(Notification::class, 'posts_id');
    }
}
