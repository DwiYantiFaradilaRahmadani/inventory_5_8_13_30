<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::all();

        return response()->json([
            'status' => 200,
            'message' => 'Items retrieved successfully.',
            'data' => $items
        ], 200);
    }

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