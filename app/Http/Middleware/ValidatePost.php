<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ValidatePost
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
                'description' => 'required|max:255',
                'post_images' => 'image|mimes:jpeg,jpg,png,gif|max:2048',
            ],
            [
                'description.max' => 'O campo descrição ultrapassou o limite de caracteres!',
                'post_images.max' => 'A imagem ultrapassou o tamanho permitido (Máximo 2MB)!',
                'description.required' => 'O campo descrição é obrigatório!',
                'post_images.image' => 'O arquivo deve ser uma imagem!',
                'post_images.mimes' => 'A imagem deve ser nos formatos: jpeg, jpg, png ou gif!',
                'post_images.max' => 'A imagem não pode ter mais de 2MB!',
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
