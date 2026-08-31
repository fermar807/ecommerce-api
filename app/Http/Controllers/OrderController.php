<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreOrderRequest;

class OrderController extends Controller
{
    /**
     * Obtener las órdenes del usuario autenticado.
     */
    public function index(Request $request)
    {
        try {
            // Obtener el usuario autenticado
            $user = $request->user();

            // Obtener sus órdenes con sus productos
            $orders = Order::with('items.product')
                ->where('user_id', $user->id)
                ->get();

            return response()->json(
                [
                    'data' => $orders
                ],
                200
            );

        } catch (\Exception $error) {
            return response()->json(
                [
                    'message' => $error->getMessage()
                ],
                500
            );
        }
    }

    /**
     * Crear una nueva orden.
     */
    public function store(StoreOrderRequest $request)
    {
        try {
            

            // Obtener el usuario autenticado
            $user = $request->user();

            // Crear la orden
            $order = DB::transaction(function () use ($request, $user) {

                $order = Order::create([
                    'user_id' => $user->id,
                    'status' => 'pending',
                    'total' => 0
                ]);

                $total = 0;

                foreach ($request->items as $item) {

                    // Buscar el producto
                    $product = Product::findOrFail($item['product_id']);

                    // Verificar que exista suficiente stock 
                    if ($product->stock < $item['quantity']) 
                        { 
                            throw new \Exception( "Stock insuficiente para el producto: {$product->name}" );
                         }

                    // Calcular subtotal
                    $subtotal = $product->price * $item['quantity'];

                    // Crear detalle
                    $order->items()->create([
                        'product_id' => $product->id,
                        'quantity' => $item['quantity'],
                        'price' => $product->price,
                        'subtotal' => $subtotal
                    ]);

                    $product->decrement('stock', $item['quantity']);

                    // Acumular total
                    $total += $subtotal;
                }

                // Actualizar total de la orden
                $order->update([
                    'total' => $total
                ]);

                return $order;
            });

            // Cargar los detalles de la orden
            $order->load('items.product');

            return response()->json(
                [
                    'message' => 'Orden creada correctamente',
                    'data' => $order
                ],
                201
            );

        } catch (\Exception $error) {
            return response()->json(
                [
                    'message' => $error->getMessage()
                ],
                500
            );
        }
    }

    
    public function show(Request $request, String $id)
    {
        try {
            // Obtener el usuario autenticado
            $user = $request->user();

            // Buscar la orden perteneciente al usuario
            $order = Order::with('items.product')
                ->where('user_id', $user->id)
                ->findOrFail($id);

            return response()->json(
                [
                    'data' => $order
                ],
                200
            );

        } catch (\Exception $error) {
            return response()->json(
                [
                    'message' => $error->getMessage()
                ],
                500
            );
        }
    }
}
