<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ValidateLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
                'password' => 'required|min:6|max:255',
            ],
            [
                'email.required' => 'O campo email é obrigatório!',
                'password.required' => 'O campo senha é obrigatório!',
                'email.email' => 'O campo email deve ser um endereço de email válido!',
            ]
        );

        if ($validator->fails()) {
            return response()
                        ->json([
                            'errors' => $validator->errors(),
                        ], 400);
        }

        return $next($request);
    }
}
