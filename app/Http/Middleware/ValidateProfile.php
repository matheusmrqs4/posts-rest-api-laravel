<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ValidateProfile
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
                'bio' => 'max:255',
                'city' => 'min:2',
                'contact' => 'max:255',
                'image_profile' => 'image|mimes:jpeg,jpg,png,gif|max:2048',
            ],
            [
                'bio.max' => 'O campo bio ultrapassou o limite de caracteres!',
                'contact.max' => 'O campo contato ultrapassou o limite de caracteres!',
                'image_profile.max' => 'A imagem ultrapassou o tamanho permitido (Máximo 2MB)!',
                'city.min' => 'O campo cidade deve ter no mínimo :min caracteres.',
                'image_profile.image' => 'O arquivo deve ser uma imagem.',
                'image_profile.mimes' => 'A imagem deve ser dos tipos: jpeg, jpg, png ou gif.',
                'image_profile.max' => 'A imagem não pode ter mais de 2 MB.',
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
