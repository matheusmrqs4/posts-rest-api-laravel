<?php

namespace App\Http\Controllers\API;

use App\Models\Post;
use App\Models\User;
use App\Models\Comments;
use App\Models\Notification;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/notifications",
     *     summary="Save notifications",
     *     tags={"Notifications"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"post_id", "comment_id"},
     *                 @OA\Property(
     *                     property="post_id",
     *                     type="integer",
     *                     description="ID of the post related to the notification"
     *                 ),
     *                 @OA\Property(
     *                     property="comment_id",
     *                     type="integer",
     *                     description="ID of the comment related to the notification"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Notifications saved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="message",
     *                     type="string",
     *                     example="Notifications saved successfully!"
     *                 ),
     *                 @OA\Property(
     *                     property="notification",
     *                     type="object",
     *                     @OA\Property(
     *                         property="id",
     *                         type="integer",
     *                         format="int64"
     *                     ),
     *                     @OA\Property(
     *                         property="message",
     *                         type="string"
     *                     ),
     *                     @OA\Property(
     *                         property="posts_id",
     *                         type="integer",
     *                         format="int64"
     *                     ),
     *                     @OA\Property(
     *                         property="comments_id",
     *                         type="integer",
     *                         format="int64"
     *                     ),
     *                     @OA\Property(
     *                         property="users_id",
     *                         type="integer",
     *                         format="int64"
     *                     ),
     *                     @OA\Property(
     *                         property="senders_id",
     *                         type="integer",
     *                         format="int64"
     *                     ),
     *                     @OA\Property(
     *                         property="created_at",
     *                         type="string",
     *                         format="date-time"
     *                     ),
     *                     @OA\Property(
     *                         property="updated_at",
     *                         type="string",
     *                         format="date-time"
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
    */
    public function saveNotifications(Post $post, Comments $comments, User $user)
    {
        $notification = new Notification();
        $notification->message = 'Novo Comentário Recebido';
        $notification->posts_id = $post->id;
        $notification->comments_id = $comments->id;
        $notification->users_id = $post->user->id;
        $notification->senders_id = $user->id;
        $notification->save();

        return response()
                    ->json([
                        'data' => [
                            'message' => 'Notifications saved successfully!',
                            'notification' => $notification
                        ]
                    ]);
    }

    /**
     * @OA\Get(
     *     path="/api/notifications",
     *     summary="Get user notifications",
     *     tags={"Notifications"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="User notifications retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="notifications",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(
     *                             property="id",
     *                             type="integer",
     *                             format="int64"
     *                         ),
     *                         @OA\Property(
     *                             property="message",
     *                             type="string"
     *                         ),
     *                         @OA\Property(
     *                             property="posts_id",
     *                             type="integer",
     *                             format="int64"
     *                         ),
     *                         @OA\Property(
     *                             property="comments_id",
     *                             type="integer",
     *                             format="int64"
     *                         ),
     *                         @OA\Property(
     *                             property="users_id",
     *                             type="integer",
     *                             format="int64"
     *                         ),
     *                         @OA\Property(
     *                             property="senders_id",
     *                             type="integer",
     *                             format="int64"
     *                         ),
     *                         @OA\Property(
     *                             property="created_at",
     *                             type="string",
     *                             format="date-time"
     *                         ),
     *                         @OA\Property(
     *                             property="updated_at",
     *                             type="string",
     *                             format="date-time"
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
    */
    public function getNotifications()
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, "Unauthorized");
        }

        $notifications = Notification::whereHas('post', function ($query) use ($user) {
            $query->where('users_id', $user->id);
        })->with('post', 'comment', 'user', 'sender')->get();

        return response()
                    ->json([
                        'data' => [
                            'notifications' => $notifications
                        ]
                    ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/notifications",
     *     summary="Delete user notifications",
     *     tags={"Notifications"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Notifications deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Notifications deleted successfully!"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function deleteNotifications()
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, "Unauthorized");
        }

        $user->notifications()->delete();

        return response()
                    ->json([
                        'message' => 'Notifications deleted successfully!'
                        ]);
    }
}
