<?php

namespace App\Events;

use App\Models\Comments;
use App\Models\Post;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewNotification implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public $message;
    public $post;
    public $comments;
    public $user;

    public function __construct($message, Post $post, Comments $comments, User $user)
    {
        $this->message = $message;
        $this->post = $post;
        $this->comments = $comments;
        $this->user = $user;
    }

    public function broadcastOn()
    {
        return new Channel('new-notification-channel');
    }

    public function broadcastAs()
    {
        return 'new-notification';
    }
}
