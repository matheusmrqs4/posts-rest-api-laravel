<?php

namespace App\Models;

use App\Models\Post;
use App\Models\User;
use App\Models\Comments;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'message',
        'users_id',
        'senders_id',
        'posts_id',
        'comments_id'
    ];

    public function post()
    {
        return $this->belongsTo(Post::class, 'posts_id');
    }

    public function comment()
    {
        return $this->belongsTo(Comments::class, 'comments_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'senders_id');
    }
}
