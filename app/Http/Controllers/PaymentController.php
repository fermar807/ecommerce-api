<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
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
