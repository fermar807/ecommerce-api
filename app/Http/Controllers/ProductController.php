<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\UpdateProductRequest;
use OpenApi\Attributes as OA;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    

    #[OA\Get( 
        path: '/api/products', 
        summary: 'Listar productos', 
        description: 'Obtiene todos los productos activos del catalogo.', 
        tags: ['Products'], 
        responses: [ 
            new OA\Response( 
                response: 200, 
                description: 'Lista de productos'
                 )
             ] )
        ]

    public function index()
    {
        try {
            // Obtener todos los productos
            $products = Product::all();

            return response()->json(
                [
                    'data' => $products
                ]
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
    path: '/api/products',
    summary: 'Crear un producto',
    description: 'Crea un nuevo producto en el catalogo.',
    security: [['sanctum' => []]],
    tags: ['Products'],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['name', 'price', 'stock'],
            properties: [
                new OA\Property(
                    property: 'name',
                    type: 'string',
                    example: 'Laptop Lenovo'
                ),
                new OA\Property(
                    property: 'description',
                    type: 'string',
                    example: 'Laptop para trabajo'
                ),
                new OA\Property(
                    property: 'price',
                    type: 'number',
                    format: 'float',
                    example: 850.00
                ),
                new OA\Property(
                    property: 'stock',
                    type: 'integer',
                    example: 10
                )
            ]
        )
    ),
    responses: [
        new OA\Response(
            response: 201,
            description: 'Producto creado correctamente'
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
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        try {            

            // Crear un nuevo producto
            $product = Product::create([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'stock' => $request->stock
            ]);

            return response()->json(
                [
                    'message' => 'Producto creado correctamente',
                    'data' => $product
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

    #[OA\Get(
    path: '/api/products/{id}',
    summary: 'Obtener un producto',
    description: 'Obtiene un producto especifico por su ID.',
    tags: ['Products'],
    parameters: [
        new OA\Parameter(
            name: 'id',
            description: 'ID del producto',
            in: 'path',
            required: true,
            schema: new OA\Schema(type: 'integer')
        )
    ],
    responses: [
        new OA\Response(
            response: 200,
            description: 'Producto encontrado'
        ),
        new OA\Response(
            response: 404,
            description: 'Producto no encontrado'
        )
    ]
)]


    /**
     * Display the specified resource.
     */
    public function show(String $id)
    {
        try {
            // Buscar el producto por su ID
            $product = Product::findOrFail($id);

            return response()->json(
                [
                    'data' => $product
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

    #[OA\Put(
    path: '/api/products/{id}',
    summary: 'Actualizar un producto',
    description: 'Actualiza los datos de un producto existente.',
    security: [['sanctum' => []]],
    tags: ['Products'],
    parameters: [
        new OA\Parameter(
            name: 'id',
            description: 'ID del producto',
            in: 'path',
            required: true,
            schema: new OA\Schema(type: 'integer')
        )
    ],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['name', 'price', 'stock'],
            properties: [
                new OA\Property(
                    property: 'name',
                    type: 'string',
                    example: 'Laptop Lenovo ThinkPad'
                ),
                new OA\Property(
                    property: 'description',
                    type: 'string',
                    example: 'Laptop actualizada'
                ),
                new OA\Property(
                    property: 'price',
                    type: 'number',
                    format: 'float',
                    example: 950.00
                ),
                new OA\Property(
                    property: 'stock',
                    type: 'integer',
                    example: 15
                )
            ]
        )
    ),
    responses: [
        new OA\Response(
            response: 200,
            description: 'Producto actualizado correctamente'
        ),
        new OA\Response(
            response: 401,
            description: 'No autenticado'
        ),
        new OA\Response(
            response: 404,
            description: 'Producto no encontrado'
        ),
        new OA\Response(
            response: 422,
            description: 'Datos de validacion incorrectos'
        )
    ]
)]


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, String $id)
    {
        try {
            // Buscar el producto por su ID
            $product = Product::findOrFail($id);
            
            // Actualizar el producto
            $product->update([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'stock' => $request->stock
            ]);

            return response()->json(
                [
                    'message' => 'Producto actualizado correctamente',
                    'data' => $product
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

    #[OA\Delete(
    path: '/api/products/{id}',
    summary: 'Eliminar un producto',
    description: 'Realiza un soft delete del producto.',
    security: [['sanctum' => []]],
    tags: ['Products'],
    parameters: [
        new OA\Parameter(
            name: 'id',
            description: 'ID del producto',
            in: 'path',
            required: true,
            schema: new OA\Schema(type: 'integer')
        )
    ],
    responses: [
        new OA\Response(
            response: 200,
            description: 'Producto eliminado correctamente'
        ),
        new OA\Response(
            response: 401,
            description: 'No autenticado'
        ),
        new OA\Response(
            response: 404,
            description: 'Producto no encontrado'
        )
    ]
)]


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(String $id)
    {
        try {
            // Buscar el producto por su ID
            $product = Product::findOrFail($id);

            // Eliminar el producto utilizando SoftDelete
            $product->delete();

            return response()->json(
                [
                    'message' => 'Producto eliminado correctamente'
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

    #[OA\Post(
    path: '/api/products/{id}/restore',
    summary: 'Restaurar un producto',
    description: 'Restaura un producto eliminado mediante soft delete.',
    security: [['sanctum' => []]],
    tags: ['Products'],
    parameters: [
        new OA\Parameter(
            name: 'id',
            description: 'ID del producto eliminado',
            in: 'path',
            required: true,
            schema: new OA\Schema(type: 'integer')
        )
    ],
    responses: [
        new OA\Response(
            response: 200,
            description: 'Producto restaurado correctamente'
        ),
        new OA\Response(
            response: 401,
            description: 'No autenticado'
        ),
        new OA\Response(
            response: 404,
            description: 'Producto eliminado no encontrado'
        )
    ]
)]


    /**
     * Restore a soft deleted product.
     */
    public function restore(String $id)
    {
        try {
            // Buscar el producto eliminado
            $product = Product::onlyTrashed()->findOrFail($id);

            // Restaurar el producto
            $product->restore();

            return response()->json(
                [
                    'message' => 'Producto restaurado correctamente',
                    'data' => $product
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