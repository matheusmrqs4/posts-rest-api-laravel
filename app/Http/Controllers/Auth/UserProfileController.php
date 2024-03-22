<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UserProfileController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/user/me",
     *     summary="Get current user profile",
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Returns the current user profile with profile image and posts",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="id",
     *                 type="integer",
     *                 example="1"
     *             ),
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 example="John Doe"
     *             ),
     *             @OA\Property(
     *                 property="email",
     *                 type="string",
     *                 example="john@example.com"
     *             ),
     *             @OA\Property(
     *                 property="bio",
     *                 type="string",
     *                 example="Software developer passionate about creating innovative solutions."
     *             ),
     *             @OA\Property(
     *                 property="city",
     *                 type="string",
     *                 example="New York"
     *             ),
     *             @OA\Property(
     *                 property="contact",
     *                 type="string",
     *                 example="john@example.com"
     *             ),
     *             @OA\Property(
     *                 property="user_profile_image",
     *                 type="object",
     *                 properties={
     *                     @OA\Property(
     *                         property="id",
     *                         type="integer",
     *                         example="1"
     *                     ),
     *                     @OA\Property(
     *                         property="users_id",
     *                         type="integer",
     *                         example="1"
     *                     ),
     *                     @OA\Property(
     *                         property="profile_image",
     *                         type="string",
     *                         example="profile_images/profile123.jpg"
     *                     )
     *                 }
     *             ),
     *             @OA\Property(
     *                 property="posts",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     properties={
     *                         @OA\Property(
     *                             property="id",
     *                             type="integer",
     *                             example="1"
     *                         ),
     *                         @OA\Property(
     *                             property="user_id",
     *                             type="integer",
     *                             example="1"
     *                         ),
     *                         @OA\Property(
     *                             property="title",
     *                             type="string",
     *                             example="Hello World"
     *                         ),
     *                         @OA\Property(
     *                             property="body",
     *                             type="string",
     *                             example="This is a post body."
     *                         )
     *                     }
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
    public function me()
    {
        $user = auth()->user();
        $userWithProfileImageAndPosts = $user->load(['userProfileImage', 'posts']);

        return response()->json($userWithProfileImageAndPosts);
    }

    /**
     * @OA\Get(
     *     path="/api/user/{user}",
     *     summary="Get user profile by ID",
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         description="ID of the user",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Returns the user profile with profile image and posts",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="id",
     *                 type="integer",
     *                 example="1"
     *             ),
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 example="John Doe"
     *             ),
     *             @OA\Property(
     *                 property="email",
     *                 type="string",
     *                 example="john@example.com"
     *             ),
     *             @OA\Property(
     *                 property="bio",
     *                 type="string",
     *                 example="Software developer passionate about creating innovative solutions."
     *             ),
     *             @OA\Property(
     *                 property="city",
     *                 type="string",
     *                 example="New York"
     *             ),
     *             @OA\Property(
     *                 property="contact",
     *                 type="string",
     *                 example="john@example.com"
     *             ),
     *             @OA\Property(
     *                 property="user_profile_image",
     *                 type="object",
     *                 properties={
     *                     @OA\Property(
     *                         property="id",
     *                         type="integer",
     *                         example="1"
     *                     ),
     *                     @OA\Property(
     *                         property="users_id",
     *                         type="integer",
     *                         example="1"
     *                     ),
     *                     @OA\Property(
     *                         property="profile_image",
     *                         type="string",
     *                         example="profile_images/profile123.jpg"
     *                     )
     *                 }
     *             ),
     *             @OA\Property(
     *                 property="posts",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     properties={
     *                         @OA\Property(
     *                             property="id",
     *                             type="integer",
     *                             example="1"
     *                         ),
     *                         @OA\Property(
     *                             property="user_id",
     *                             type="integer",
     *                             example="1"
     *                         ),
     *                         @OA\Property(
     *                             property="title",
     *                             type="string",
     *                             example="Hello World"
     *                         ),
     *                         @OA\Property(
     *                             property="body",
     *                             type="string",
     *                             example="This is a post body."
     *                         )
     *                     }
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
    public function profile(User $user)
    {
        $userWithProfileImageAndPosts = $user->load(['userProfileImage', 'posts']);

        return response()->json($userWithProfileImageAndPosts);
    }

    /**
     * @OA\Put(
     *     path="/api/profile/update",
     *     summary="Update user profile",
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="bio",
     *                     type="string",
     *                     description="User biography"
     *                 ),
     *                 @OA\Property(
     *                     property="city",
     *                     type="string",
     *                     description="User city"
     *                 ),
     *                 @OA\Property(
     *                     property="contact",
     *                     type="string",
     *                     description="User contact information"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profile updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="msg",
     *                     type="string",
     *                     example="Profile updated successfully"
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
     *                         property="name",
     *                         type="string",
     *                         example="John Doe"
     *                     ),
     *                     @OA\Property(
     *                         property="email",
     *                         type="string",
     *                         example="john@example.com"
     *                     ),
     *                     @OA\Property(
     *                         property="bio",
     *                         type="string",
     *                         example="Software developer passionate about creating innovative solutions."
     *                     ),
     *                     @OA\Property(
     *                         property="city",
     *                         type="string",
     *                         example="New York"
     *                     ),
     *                     @OA\Property(
     *                         property="contact",
     *                         type="string",
     *                         example="john@example.com"
     *                     ),
     *                     @OA\Property(
     *                         property="user_profile_image",
     *                         type="object",
     *                         properties={
     *                             @OA\Property(
     *                                 property="id",
     *                                 type="integer",
     *                                 example="1"
     *                             ),
     *                             @OA\Property(
     *                                 property="users_id",
     *                                 type="integer",
     *                                 example="1"
     *                             ),
     *                             @OA\Property(
     *                                 property="profile_image",
     *                                 type="string",
     *                                 example="profile_images/profile123.jpg"
     *                             )
     *                         }
     *                     ),
     *                     @OA\Property(
     *                         property="posts",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             properties={
     *                                 @OA\Property(
     *                                     property="id",
     *                                     type="integer",
     *                                     example="1"
     *                                 ),
     *                                 @OA\Property(
     *                                     property="user_id",
     *                                     type="integer",
     *                                     example="1"
     *                                 ),
     *                                 @OA\Property(
     *                                     property="title",
     *                                     type="string",
     *                                     example="Hello World"
     *                                 ),
     *                                 @OA\Property(
     *                                     property="body",
     *                                     type="string",
     *                                     example="This is a post body."
     *                                 )
     *                             }
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
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, "Unauthorized");
        }

        $userData = $request->only('bio', 'city', 'contact');

        $user->update($userData);

        return response()
                    ->json([
                        'data' => [
                            'msg' => 'Profile updated successfully',
                            'user' => $user
                        ]
                    ], 200);
    }
}
