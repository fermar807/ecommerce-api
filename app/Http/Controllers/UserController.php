<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Register a new user.
     */
    public function register(Request $request)
    {
        try {
            // Validar los datos
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8'
            ]);

            // Crear el usuario
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            // Generar token
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json(
                [
                    'message' => 'Usuario registrado correctamente',
                    'data' => $user,
                    'token' => $token
                ],
                201
            );

        } catch (\Exception $error) {
            return response()->json(
                [
                    'message' => $error->getMessage()
                ]
            );
        }
    }

    /**
     * Login user.
     */
    public function login(Request $request)
    {
        try {
            // Validar los datos
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string'
            ]);

            // Buscar usuario
            $user = User::where('email', $request->email)->first();

            // Verificar credenciales
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json(
                    [
                        'message' => 'Credenciales incorrectas'
                    ],
                    401
                );
            }

            // Generar token
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json(
                [
                    'message' => 'Login exitoso',
                    'data' => $user,
                    'token' => $token
                ],
                200
            );

        } catch (\Exception $error) {
            return response()->json(
                [
                    'message' => $error->getMessage()
                ]
            );
        }
    }
}
