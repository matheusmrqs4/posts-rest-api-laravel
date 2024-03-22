<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class PasswordResetController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/password/reset-link",
     *     summary="Send reset password link to user's email",
     *     tags={"Password Reset"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"email"},
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     format="email",
     *                     description="User's email"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Reset link sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Reset Link sent to your email!"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Unable to send reset link"
     *     )
     * )
    */
    public function sendResetLink(Request $request)
    {
            $validator = Validator::make(
                $request->all(),
                [
                'email' => 'required|email|exists:users,email'
                ]
            );

        if ($validator->fails()) {
            return response()
                        ->json([
                            'error' => $validator->errors()
                        ], 422);
        }

            $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            $userEmail = $request->input('email');
            $user = User::where('email', $userEmail)->first();

            if (!$user) {
                return response()
                            ->json([
                                'message' => 'User not found.'
                            ], 404);
            }

            $token = Password::createToken($user);

            Mail::to($userEmail)->send(new ResetPasswordMail($user, $token));

            return response()
                        ->json([
                            'message' => 'Reset Link sent to your email!'
                        ], 200);
        } else {
            return response()
                        ->json([
                            'message' => 'Unable to send Reset Link to your email!'
                        ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/password/reset",
     *     summary="Reset user's password",
     *     tags={"Password Reset"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"email", "password", "password_confirmation", "token"},
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     format="email",
     *                     description="User's email"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                     format="password",
     *                     description="New password"
     *                 ),
     *                 @OA\Property(
     *                     property="password_confirmation",
     *                     type="string",
     *                     format="password",
     *                     description="Confirmation of new password"
     *                 ),
     *                 @OA\Property(
     *                     property="token",
     *                     type="string",
     *                     description="Reset token received via email"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password reset successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Password reset successfully."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Unable to reset password"
     *     )
     * )
    */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|confirmed|min:8',
            'token' => 'required'
            ]
        );

        if ($validator->fails()) {
            return response()
                        ->json([
                            'error' => $validator->errors()
                        ], 422);
        }

        $response = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = bcrypt($password);
                $user->save();

                DB::table('password_reset_tokens')->where('email', $user->email)->delete();
            }
        );

        return $response === Password::PASSWORD_RESET
        ? response()
                ->json([
                    'message' => 'Password reset successfully.'
                    ])
        : response()
                ->json([
                    'message' => 'Unable to reset password.'
                ], 500);
    }
}
