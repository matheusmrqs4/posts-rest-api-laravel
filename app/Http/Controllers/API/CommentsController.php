<?php

namespace App\Http\Controllers\API;

use App\Models\Post;
use App\Models\User;
use App\Models\Comments;
use Illuminate\Http\Request;
use App\Events\NewNotification;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\API\NotificationController;

class CommentsController extends Controller
{
    private $comments;
    private $notification;

    public function __construct(Comments $comments, NotificationController $notification)
    {
        $this->comments = $comments;
        $this->notification = $notification;
        $this->middleware('validate.comment')->only(['store', 'update']);
    }

    /**
     * @OA\Post(
     *     path="/api/posts/{post}/comments",
     *     summary="Create a new comment on a post",
     *     tags={"Comments"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="post",
     *         in="path",
     *         description="ID of the post to comment on",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"comment"},
     *                 @OA\Property(
     *                     property="comment",
     *                     type="string",
     *                     description="Content of the comment"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Comment created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="msg",
     *                     type="string",
     *                     example="Comment created successfully"
     *                 ),
     *                 @OA\Property(
     *                     property="comment",
     *                     required={"id", "comment", "user"},
     *                     @OA\Property(
     *                         property="id",
     *                         type="integer",
     *                         format="int64"
     *                     ),
     *                     @OA\Property(
     *                         property="comment",
     *                         type="string"
     *                     ),
     *                     @OA\Property(
     *                         property="user",
     *                         required={"id", "name", "email"},
     *                         @OA\Property(
     *                             property="id",
     *                             type="integer",
     *                             format="int64"
     *                         ),
     *                         @OA\Property(
     *                             property="name",
     *                             type="string"
     *                         ),
     *                         @OA\Property(
     *                             property="email",
     *                             type="string",
     *                             format="email"
     *                         )
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="user",
     *                     required={"id", "name", "email"},
     *                     @OA\Property(
     *                         property="id",
     *                         type="integer",
     *                         format="int64"
     *                     ),
     *                     @OA\Property(
     *                         property="name",
     *                         type="string"
     *                     ),
     *                     @OA\Property(
     *                         property="email",
     *                         type="string",
     *                         format="email"
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
    public function store(Request $request, Post $post)
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, "Unauthorized");
        }

        $commentData = [
            'comment' => $request->input('comment'),
            'posts_id' => $post->id,
            'users_id' => $user->id
        ];

        $comments = Comments::create($commentData);

        if ($user->id !== $post->user->id) {
            broadcast(new NewNotification('Novo Comentário Recebido', $post, $comments, $user));
            $this->notification->saveNotifications($post, $comments, $user);
        }

        return response()
                    ->json([
                        'data' => [
                            'msg' => 'Comment created successfully',
                            'comment' => $comments,
                            'user' => $user
                        ]
                    ]);
    }

    /**
     * @OA\Put(
     *     path="/api/comments/{comment}",
     *     summary="Update a comment",
     *     tags={"Comments"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="comment",
     *         in="path",
     *         description="ID of the comment to update",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"comment"},
     *                 @OA\Property(
     *                     property="comment",
     *                     type="string",
     *                     description="Updated content of the comment"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Comment updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="msg",
     *                     type="string",
     *                     example="Comment updated successfully"
     *                 ),
     *                 @OA\Property(
     *                     property="comment",
     *                     required={"id", "comment", "user"},
     *                     @OA\Property(
     *                         property="id",
     *                         type="integer",
     *                         format="int64"
     *                     ),
     *                     @OA\Property(
     *                         property="comment",
     *                         type="string"
     *                     ),
     *                     @OA\Property(
     *                         property="user",
     *                         required={"id", "name", "email"},
     *                         @OA\Property(
     *                             property="id",
     *                             type="integer",
     *                             format="int64"
     *                         ),
     *                         @OA\Property(
     *                             property="name",
     *                             type="string"
     *                         ),
     *                         @OA\Property(
     *                             property="email",
     *                             type="string",
     *                             format="email"
     *                         )
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="user",
     *                     required={"id", "name", "email"},
     *                     @OA\Property(
     *                         property="id",
     *                         type="integer",
     *                         format="int64"
     *                     ),
     *                     @OA\Property(
     *                         property="name",
     *                         type="string"
     *                     ),
     *                     @OA\Property(
     *                         property="email",
     *                         type="string",
     *                         format="email"
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
    public function update(Request $request, Comments $comments)
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, "Unauthorized");
        }

        $comments->update($request->all());

        return response()
                    ->json($comments);
    }

    /**
     * @OA\Delete(
     *     path="/api/comments/{comment}",
     *     summary="Delete a comment",
     *     tags={"Comments"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="comment",
     *         in="path",
     *         description="ID of the comment to delete",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Comment deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="msg",
     *                     type="string",
     *                     example="Comment deleted successfully"
     *                 ),
     *                 @OA\Property(
     *                     property="user",
     *                     type="object",
     *                     @OA\Property(
     *                         property="id",
     *                         type="integer",
     *                         format="int64"
     *                     ),
     *                     @OA\Property(
     *                         property="name",
     *                         type="string"
     *                     ),
     *                     @OA\Property(
     *                         property="email",
     *                         type="string",
     *                         format="email"
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
    public function destroy(Comments $comments)
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, "Unauthorized");
        }

        $comments->delete();

        return response()
                    ->json([
                        'data' => [
                            'msg' => 'Comment deleted successfully',
                            'user' => $user
                        ]
                    ]);
    }
}
