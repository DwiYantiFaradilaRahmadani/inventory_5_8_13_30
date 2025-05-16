<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Item;

class ItemsSwaggerController extends Controller
{
    /**
     * @OA\Get(
     *     path="/item",
     *     tags={"Item"},
     *     summary="Get all items",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Items retrieved successfully."),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(ref="#/components/schemas/Item")  <!-- Referensi schema Item -->
     *             )
     *         )
     *     )
     * )
     */
    /**
 * @OA\Schema(
 *     schema="Item",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="category_id", type="integer", example=2),
 *     @OA\Property(property="Nama_Barang", type="string", example="Laptop"),
 *     @OA\Property(property="Stock", type="integer", example=50),
 *     @OA\Property(property="Satuan", type="string", example="Unit")
 * )
 */
    public function index()
    {
        $items = Item::all();

        return response()->json([
            'status' => 200,
            'message' => 'Items retrieved successfully.',
            'data' => $items
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/item",
     *     tags={"Item"},
     *     summary="Create a new item",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"category_id", "Nama_Barang", "Stock", "Satuan"},
     *             @OA\Property(property="category_id", type="integer", example=1),
     *             @OA\Property(property="Nama_Barang", type="string", example="Laptop"),
     *             @OA\Property(property="Stock", type="integer", example=50),
     *             @OA\Property(property="Satuan", type="string", example="Unit")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Item created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=201),
     *             @OA\Property(property="message", type="string", example="Item created successfully."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="category_id", type="integer", example=1),
     *                 @OA\Property(property="Nama_Barang", type="string", example="Laptop"),
     *                 @OA\Property(property="Stock", type="integer", example=50),
     *                 @OA\Property(property="Satuan", type="string", example="Unit")
     *             )
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'Nama_Barang' => 'required|string|max:255',
            'Stock' => 'required|integer|min:0',
            'Satuan' => 'required|string|max:100'
        ]);

        $item = Item::create($request->all());

        return response()->json([
            'status' => 201,
            'message' => 'Item created successfully.',
            'data' => $item
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/item/{id}",
     *     tags={"Item"},
     *     summary="Get item by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Item retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Item retrieved successfully."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="category_id", type="integer", example=2),
     *                 @OA\Property(property="Nama_Barang", type="string", example="Laptop"),
     *                 @OA\Property(property="Stock", type="integer", example=50),
     *                 @OA\Property(property="Satuan", type="string", example="Unit")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Item not found")
     * )
     */
    public function show($id)
    {
        $item = Item::find($id);

        if (!$item) {
            return response()->json([
                'status' => 404,
                'message' => 'Item not found.',
                'data' => null
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Item retrieved successfully.',
            'data' => $item
        ], 200);
    }

    /**
     * @OA\Put(
     *     path="/item/{id}",
     *     tags={"Item"},
     *     summary="Update item by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="category_id", type="integer", example=2),
     *             @OA\Property(property="Nama_Barang", type="string", example="Updated Laptop"),
     *             @OA\Property(property="Stock", type="integer", example=60),
     *             @OA\Property(property="Satuan", type="string", example="Piece")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Item updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Item updated successfully."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="category_id", type="integer", example=2),
     *                 @OA\Property(property="Nama_Barang", type="string", example="Updated Laptop"),
     *                 @OA\Property(property="Stock", type="integer", example=60),
     *                 @OA\Property(property="Satuan", type="string", example="Piece")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Item not found")
     * )
     */
    public function update(Request $request, $id)
    {
        $item = Item::find($id);

        if (!$item) {
            return response()->json([
                'status' => 404,
                'message' => 'Item not found.',
                'data' => null
            ], 404);
        }

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'Nama_Barang' => 'string|max:255',
            'Stock' => 'integer|min:0',
            'Satuan' => 'string|max:100'
        ]);

        $item->update($request->all());

        return response()->json([
            'status' => 200,
            'message' => 'Item updated successfully.',
            'data' => $item
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/item/{id}",
     *     tags={"Item"},
     *     summary="Delete item by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Item deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Item deleted successfully."),
     *             @OA\Property(property="data", type="string", example=null)
     *         )
     *     ),
     *     @OA\Response(response=404, description="Item not found")
     * )
     */
    public function destroy($id)
    {
        $item = Item::find($id);

        if (!$item) {
            return response()->json([
                'status' => 404,
                'message' => 'Item not found.',
                'data' => null
            ], 404);
        }

        $item->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Item deleted successfully.',
            'data' => null
        ], 200);
    }
}
