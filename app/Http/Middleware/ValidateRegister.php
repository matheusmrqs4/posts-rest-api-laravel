<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ValidateRegister
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
                'name' => 'required|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8|max:255',
                'terms_of_service' => [
                    'required',
                    Rule::in([true])
                ]
            ],
            [
                'name.max' => 'O campo nome ultrapassou o limite de caracteres!',
                'description.max' => 'O campo descrição ultrapassou o limite de caracteres!',
                'email.required' => 'O campo email é obrigatório!',
                'password.required' => 'O campo senha é obrigatório!',
                'password.min' => 'A senha deve conter no mínimo 8 caracteres!',
                'email.email' => 'O campo email deve ser um endereço de email válido!',
                'email.unique' => 'Este email já está sendo usado por outro usuário!',
                'terms_of_service.in' => 'Você deve concordar com os termos de serviço!',
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
