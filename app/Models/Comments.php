<?php

namespace App\Models;

use App\Models\Post;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class Comments extends Model
{
    use HasFactory;

    protected $fillable = [
        'comment',
        'posts_id',
        'users_id',
    ];

    public function posts()
    {
        return $this->belongsTo(Post::class, 'posts_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function notification()
    {
        return $this->hasMany(Notification::class, 'comments_id');
    }
}
