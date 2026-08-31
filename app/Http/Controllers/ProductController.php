<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\UpdateProductRequest;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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