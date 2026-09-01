<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use OpenApi\Attributes as OA;

class UserController extends Controller
{

#[OA\Post(
    path: '/api/register',
    summary: 'Registrar usuario',
    description: 'Registra un nuevo usuario y genera un token de autenticacion.',
    tags: ['Authentication'],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['name', 'email', 'password'],
            properties: [
                new OA\Property(
                    property: 'name',
                    type: 'string',
                    example: 'Fernando Gonzalez'
                ),
                new OA\Property(
                    property: 'email',
                    type: 'string',
                    format: 'email',
                    example: 'fernando@example.com'
                ),
                new OA\Property(
                    property: 'password',
                    type: 'string',
                    format: 'password',
                    example: 'Password123'
                ),
                new OA\Property(
                    property: 'password_confirmation',
                    type: 'string',
                    format: 'password',
                    example: 'Password123'
                )
            ]
        )
    ),
    responses: [
        new OA\Response(
            response: 201,
            description: 'Usuario registrado correctamente'
        ),
        new OA\Response(
            response: 422,
            description: 'Datos de validacion incorrectos'
        )
    ]
)]

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
    #[OA\Post(
    path: '/api/login',
    summary: 'Iniciar sesion',
    description: 'Autentica un usuario y genera un token de Sanctum.',
    tags: ['Authentication'],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['email', 'password'],
            properties: [
                new OA\Property(
                    property: 'email',
                    type: 'string',
                    format: 'email',
                    example: 'fernando@example.com'
                ),
                new OA\Property(
                    property: 'password',
                    type: 'string',
                    format: 'password',
                    example: 'Password123'
                )
            ]
        )
    ),
    responses: [
        new OA\Response(
            response: 200,
            description: 'Login exitoso'
        ),
        new OA\Response(
            response: 401,
            description: 'Credenciales incorrectas'
        ),
        new OA\Response(
            response: 422,
            description: 'Datos de validacion incorrectos'
        )
    ]
)]


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
