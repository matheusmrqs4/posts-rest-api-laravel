<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ValidateComments
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
                'comment' => 'required|max:255',
            ],
            [
                'required' => 'O campo :attribute é obrigatório!',
                'max' => 'O campo :attribute ultrapassou o limite de caracteres!',
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
