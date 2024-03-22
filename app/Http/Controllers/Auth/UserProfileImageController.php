<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Models\UserProfileImage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserProfileImageController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/profile/upload-image",
     *     summary="Upload profile image",
     *     tags={"Authentication"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="profile_image",
     *                     type="string",
     *                     format="binary",
     *                     description="Profile image file"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profile image added successfully"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function uploadProfileImage(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, "Unauthorized");
        }

        $userProfileImage = $user->userProfileImage;

        if (!$userProfileImage) {
            $userProfileImage = new UserProfileImage();
            $userProfileImage->users_id = $user->id;
        }

        if ($userProfileImage->profile_image) {
            Storage::disk('public')->delete($userProfileImage->profile_image);
        }

        $image = $request->file('profile_image');
        $imagePath = $image->store('profile_images', 'public');

        $userProfileImage->profile_image = $imagePath;
        $userProfileImage->save();

        $user->load('userProfileImage');

        return response()
                    ->json([
                        'data' => [
                            'msg' => 'Profile image added successfully',
                            'user' => $user
                        ]
                    ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/profile/delete-image",
     *     summary="Delete profile image",
     *     tags={"Authentication"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Profile image deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function deleteProfileImage()
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, "Unauthorized");
        }

        $userProfileImage = $user->userProfileImage;

        if ($userProfileImage) {
            Storage::disk('public')->delete($userProfileImage->profile_image);
            $userProfileImage->delete();
        }

        return response()
                    ->json([
                        'msg' => 'Profile image deleted successfully',
                    ]);
    }
}
