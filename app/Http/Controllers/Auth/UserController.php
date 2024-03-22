<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

    /**
     * @OA\Info(
     *     title="MarketPlus API",
     *     description="The API enables users to create an account, log in, create posts (with editing and deletion capabilities), and comment on posts (with editing and deletion capabilities).",
     *     version="1.0.0",
     *     contact={
     *         "url": "https://github.com/matheusmrqs4"
     *     },
     *     license={
     *         "name": "Apache 2.0",
     *         "url": "https://www.apache.org/licenses/LICENSE-2.0.html"
     *     }
     * )
     */

class UserController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/authenticate/login",
     *     summary="User login",
     *     tags={"Authentication"},
     *     operationId="login",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
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
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Invalid credentials"
     *     )
     * )
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = auth('api')->attempt($credentials)) {
            abort(403, "Invalid Credentials");
        }

        $user = auth('api')->user();

        return response()
                    ->json([
                        'data' => [
                            'msg' => 'Login Successfully',
                            'token' => $token,
                            'user' => $user
                        ]
                        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/authenticate/refresh",
     *     summary="Refresh JWT token",
     *     tags={"Authentication"},
     *     operationId="refreshToken",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Token refreshed successfully"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function refresh()
    {
        $token = auth('api')->refresh();

        return response()
                    ->json([
                        'data' => [
                            'msg' => 'Login Successfully',
                            'token' => $token
                        ]
                        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/authenticate/logout",
     *     summary="User logout",
     *     tags={"Authentication"},
     *     operationId="logout",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logout successful"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        Auth::guard('api')->logout();

        return response()
                    ->json([
                        'msg' => 'Logout Successfully'
                    ]);
    }
}
