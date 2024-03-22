<?php

namespace App\Http\Controllers\API;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    private $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
        $this->middleware('validate.post')->only(['store', 'update']);
    }

    /**
     * @OA\Get(
     *     path="/api/post",
     *     summary="Get all posts",
     *     tags={"Posts"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Returns all posts with related user and comments",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="posts",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(
     *                             property="id",
     *                             type="integer"
     *                         ),
     *                         @OA\Property(
     *                             property="description",
     *                             type="string"
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
    public function index()
    {
        $posts = Post::with([
            'user.userProfileImage',
            'comments' => function ($query) {
                $query->with('user');
            },
        ])->get();

        return response()
                    ->json([
                        'data' => [
                            'posts' => $posts,
                        ],
                    ]);
    }

    /**
     * @OA\Post(
     *     path="/api/post",
     *     summary="Create a new Post",
     *     tags={"Posts"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="description",
     *                     type="string",
     *                     description="Post description"
     *                 ),
     *                 @OA\Property(
     *                     property="post_images",
     *                     type="file",
     *                     description="Post image"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="msg",
     *                     type="string",
     *                     example="Post created successfully"
     *                 ),
     *                 @OA\Property(
     *                     property="post",
     *                     type="object",
     *                     @OA\Property(
     *                         property="id",
     *                         type="integer"
     *                     ),
     *                     @OA\Property(
     *                         property="description",
     *                         type="string"
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

        if ($request->hasFile('post_images')) {
            $image = $request->file('post_images');
            $imageName = 'post_' . time() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('post_images', $imageName, 'public');

            if ($post->post_images) {
                Storage::disk('public')->delete($post->post_images);
            }
        }

        $postData = [
        'description' => $request->input('description'),
        'users_id' => $user->id,
        'post_images' => $imagePath ?? null
        ];

        $post = $this->post->create($postData);

        return response()->json([
                            'data' => [
                                'msg' => 'Post created successfully',
                                'post' => $post,
                                'user' => $user
                            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/post/{post}",
     *     summary="Get a post by ID",
     *     tags={"Posts"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="post",
     *         in="path",
     *         description="ID of the post",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Returns the post with related user and comments",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="post",
     *                     type="object",
     *                     @OA\Property(
     *                         property="id",
     *                         type="integer"
     *                     ),
     *                     @OA\Property(
     *                         property="description",
     *                         type="string"
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post not found"
     *     )
     * )
    */
    public function show(Post $post)
    {
        $post = Post::with([
            'user.userProfileImage',
            'comments' => function ($query) {
                $query->with('user');
            },
        ])->findOrFail($post->id);

        return response()
                    ->json([
                        'data' => [
                            'post' => $post
                        ]
                    ]);
    }

    /**
     * @OA\Get(
     *     path="/api/post/search/{query}",
     *     summary="Search posts by description",
     *     tags={"Posts"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="query",
     *         in="path",
     *         description="Search query string",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Returns posts matching the search query",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="post",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(
     *                             property="id",
     *                             type="integer"
     *                         ),
     *                         @OA\Property(
     *                             property="description",
     *                             type="string"
     *                         ),
     *                         @OA\Property(
     *                             property="user",
     *                             type="object",
     *                             @OA\Property(
     *                                 property="id",
     *                                 type="integer"
     *                             ),
     *                             @OA\Property(
     *                                 property="username",
     *                                 type="string"
     *                             ),
     *                             @OA\Property(
     *                                 property="email",
     *                                 type="string",
     *                                 format="email"
     *                             )
     *                         ),
     *                         @OA\Property(
     *                             property="comments",
     *                             type="array",
     *                             @OA\Items(
     *                                 type="object",
     *                                 @OA\Property(
     *                                     property="id",
     *                                     type="integer"
     *                                 ),
     *                                 @OA\Property(
     *                                     property="comment",
     *                                     type="string"
     *                                 ),
     *                                 @OA\Property(
     *                                     property="user",
     *                                     type="object",
     *                                     @OA\Property(
     *                                         property="id",
     *                                         type="integer"
     *                                     ),
     *                                     @OA\Property(
     *                                         property="username",
     *                                         type="string"
     *                                     ),
     *                                     @OA\Property(
     *                                         property="email",
     *                                         type="string",
     *                                         format="email"
     *                                     )
     *                                 )
     *                             )
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
    public function search($query)
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, "Unauthorized");
        }

        $posts = Post::with([
        'user',
        'comments' => function ($query) {
            $query->with('user');
        },
        ])
        ->where('description', 'LIKE', "%$query%")
        ->get();

        return response()
                    ->json([
                        'data' => [
                            'post' => $posts
                        ]
                        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/post/{post}",
     *     summary="Update a post",
     *     tags={"Posts"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="post",
     *         in="path",
     *         description="ID of the post",
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
     *                 type="object",
     *                 @OA\Property(
     *                     property="description",
     *                     type="string",
     *                     description="Updated post description"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="msg",
     *                     type="string",
     *                     example="Post updated successfully"
     *                 ),
     *                 @OA\Property(
     *                     property="post",
     *                     type="object",
     *                     @OA\Property(
     *                         property="id",
     *                         type="integer"
     *                     ),
     *                     @OA\Property(
     *                         property="description",
     *                         type="string"
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="user",
     *                     type="object",
     *                     @OA\Property(
     *                         property="id",
     *                         type="integer"
     *                     ),
     *                     @OA\Property(
     *                         property="username",
     *                         type="string"
     *                     ),
     *                     @OA\Property(
     *                         property="email",
     *                         type="string"
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post not found"
     *     )
     * )
    */
    public function update(Request $request, Post $post)
    {
        $user = Auth::user();

        if ($user->id !== $post->users_id) {
            abort(403, "Unauthorized");
        }

        $post->description = $request->description ?? $post->description;
        $post->save();

        return response()
                    ->json([
                        'data' => [
                            'msg' => 'Post updated successfully',
                            'post' => $post,
                            'user' => $user
                        ]
                    ]);
    }


    /**
     * @OA\Delete(
     *     path="/api/post/{post}",
     *     summary="Delete a post",
     *     tags={"Posts"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="post",
     *         in="path",
     *         description="ID of the post",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="msg",
     *                     type="string",
     *                     example="Post deleted successfully"
     *                 ),
     *                 @OA\Property(
     *                     property="user",
     *                     type="object",
     *                     @OA\Property(
     *                         property="id",
     *                         type="integer",
     *                         example="1"
     *                     ),
     *                     @OA\Property(
     *                         property="username",
     *                         type="string",
     *                         example="john_doe"
     *                     ),
     *                     @OA\Property(
     *                         property="email",
     *                         type="string",
     *                         format="email",
     *                         example="john@example.com"
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post not found"
     *     )
     * )
    */
    public function destroy(Post $post)
    {
        $user = Auth::user();

        if ($user->id !== $post->users_id) {
            abort(403, "Unauthorized");
        }

        if ($post->post_images) {
            Storage::disk('public')->delete($post->post_images);
        }

        $post->delete();

        return response()
                    ->json([
                        'data' => [
                            'msg' => 'Post deleted successfully',
                            'user' => $user
                        ]
                    ]);
    }
}
