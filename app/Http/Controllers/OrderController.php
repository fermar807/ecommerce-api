<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreOrderRequest;
use OpenApi\Attributes as OA;

class OrderController extends Controller
{
    #[OA\Get(
    path: '/api/orders',
    summary: 'Historial de compras',
    description: 'Obtiene las ordenes de compra del usuario autenticado.',
    security: [['sanctum' => []]],
    tags: ['Orders'],
    responses: [
        new OA\Response(
            response: 200,
            description: 'Lista de ordenes'
        ),
        new OA\Response(
            response: 401,
            description: 'No autenticado'
        )
    ]
)]

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
    #[OA\Post(
    path: '/api/orders',
    summary: 'Crear una orden',
    description: 'Crea una orden de compra para el usuario autenticado.',
    security: [['sanctum' => []]],
    tags: ['Orders'],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['items'],
            properties: [
                new OA\Property(
                    property: 'items',
                    type: 'array',
                    description: 'Productos incluidos en la orden',
                    items: new OA\Items(
                        type: 'object',
                        required: ['product_id', 'quantity'],
                        properties: [
                            new OA\Property(
                                property: 'product_id',
                                type: 'integer',
                                example: 1
                            ),
                            new OA\Property(
                                property: 'quantity',
                                type: 'integer',
                                example: 2
                            )
                        ]
                    )
                )
            ]
        )
    ),
    responses: [
        new OA\Response(
            response: 201,
            description: 'Orden creada correctamente'
        ),
        new OA\Response(
            response: 401,
            description: 'No autenticado'
        ),
        new OA\Response(
            response: 422,
            description: 'Datos de validacion incorrectos'
        )
    ]
)]


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

    #[OA\Get(
    path: '/api/orders/{id}',
    summary: 'Consultar una orden',
    description: 'Obtiene el detalle de una orden del usuario autenticado.',
    security: [['sanctum' => []]],
    tags: ['Orders'],
    parameters: [
        new OA\Parameter(
            name: 'id',
            description: 'ID de la orden',
            in: 'path',
            required: true,
            schema: new OA\Schema(type: 'integer'),
            example: 1
        )
    ],
    responses: [
        new OA\Response(
            response: 200,
            description: 'Orden encontrada'
        ),
        new OA\Response(
            response: 401,
            description: 'No autenticado'
        ),
        new OA\Response(
            response: 404,
            description: 'Orden no encontrada'
        )
    ]
)]


    
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
