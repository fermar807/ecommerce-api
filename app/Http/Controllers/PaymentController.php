<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class PaymentController extends Controller
{
    #[OA\Post(
    path: '/api/orders/{orderId}/payment',
    summary: 'Procesar pago de una orden',
    description: 'Registra el pago de una orden de compra.',
    security: [['sanctum' => []]],
    tags: ['Payments'],
    parameters: [
        new OA\Parameter(
            name: 'orderId',
            description: 'ID de la orden a pagar',
            in: 'path',
            required: true,
            schema: new OA\Schema(type: 'integer'),
            example: 1
        )
    ],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['payment_method'],
            properties: [
                new OA\Property(
                    property: 'payment_method',
                    type: 'string',
                    example: 'card'
                )
            ]
        )
    ),
    responses: [
        new OA\Response(
            response: 201,
            description: 'Pago procesado correctamente'
        ),
        new OA\Response(
            response: 401,
            description: 'No autenticado'
        ),
        new OA\Response(
            response: 404,
            description: 'Orden no encontrada'
        ),
        new OA\Response(
            response: 409,
            description: 'La orden ya tiene un pago registrado'
        ),
        new OA\Response(
            response: 422,
            description: 'Datos de validacion incorrectos'
        )
    ]
)]
    /**
     * Crear un pago para una orden.
     */
    public function store(Request $request, String $orderId)
    {
        try {

            // Buscar la orden
            $order = Order::findOrFail($orderId);

            // Validar los datos
            $request->validate([
                'payment_method' => 'required|string|max:50',
            ]);

            // Verificar si la orden ya tiene un pago 
            $existingPayment = Payment::where('order_id', $order->id)->first(); 
            
            if ($existingPayment) { 
                return response()->json(
                     [ 'message' => 'La orden ya tiene un pago registrado', 
                     'data' => $existingPayment ], 
                     409 ); }

            // Crear el pago
            $payment = Payment::create([
                'order_id' => $order->id,
                'amount' => $order->total,
                'status' => 'paid',
                'payment_method' => $request->payment_method,
            ]);

            return response()->json(
                [
                    'message' => 'Pago procesado correctamente',
                    'data' => $payment,
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
     * Consultar el pago de una orden.
     */
    public function show(String $orderId)
    {
        try {

            // Buscar el pago asociado a la orden
            $payment = Payment::where('order_id', $orderId)->firstOrFail();

            return response()->json(
                [
                    'data' => $payment
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
