<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;

class UserRegisterController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/authenticate/register",
     *     summary="User registration",
     *     tags={"Authentication"},
     *     operationId="register",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     description="User name"
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     description="User email"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                     description="User password"
     *                 ),
     *                 @OA\Property(
     *                     property="terms_of_service",
     *                     type="boolean",
     *                     description="User agrees to terms of service"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User registered successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function register(Request $request, User $user)
    {
        $termsOfService = $request->input('terms_of_service');

        if (empty($termsOfService)) {
            return response()
                        ->json([
                            'error' => 'Você deve concordar com os termos de serviço.'
                        ], 400);
        }

        $userData = $request->only('name', 'email', 'password');
        $userData['password'] = bcrypt($userData['password']);
        $userData['terms_of_service'] = $termsOfService;

        $user = $user->create($userData) ?? abort(500, "Error to Create new User");

        $token = JWTAuth::attempt(['email' => $userData['email'], 'password' => $request->input('password')]);

        return response()
                    ->json([
                        'data' => [
                            'msg' => "Successfully",
                            'user' => $user,
                            'token' => $token
                        ]
                ], 200);
    }
}
