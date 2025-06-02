<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Log;

class CategorySwaggerController extends Controller
{
    /**
 * @OA\Get(
 *     path="/category",
 *     tags={"Category"},
 *     summary="Get all categories",
 *     @OA\Parameter(
 *         name="q",
 *         in="query",
 *         required=false,
 *         description="Search query for category name",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="message", type="string", example="Categories retrieved successfully."),
 *             @OA\Property(property="data", type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="name", type="string", example="Fiction")
 *                 )
 *             )
 *         )
 *     )
 * )
 */
public function index(Request $request)
{
    $query = $request->query('q');
    Log::info("query fetched" . $query);
    if ($query) {
        $categories = Category::where('name', 'like', '%' . $query . '%')->get();

        return response()->json([
            'status' => 200,
            'message' => 'Categories results retrieved successfully with query.',
            'data' => $categories
        ], 200);
    }

    $categories = Category::all();

    return response()->json([
        'status' => 200,
        'message' => 'Categories retrieved successfully.',
        'data' => $categories
    ], 200);
}

    /**
     * @OA\Post(
     *     path="/category",
     *     tags={"Category"},
     *     summary="Create a new category",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Fiction")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Category created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=201),
     *             @OA\Property(property="message", type="string", example="Category created successfully."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Fiction")
     *             )
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);

        $category = Category::create($request->all());

        return response()->json([
            'status' => 201,
            'message' => 'Category created successfully.',
            'data' => $category
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/category/{id}",
     *     tags={"Category"},
     *     summary="Get category by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Category retrieved successfully."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Fiction")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Category not found")
     * )
     */
    public function show($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'status' => 404,
                'message' => 'Category not found.',
                'data' => null
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Category retrieved successfully.',
            'data' => $category
        ], 200);
    }

    /**
     * @OA\Put(
     *     path="/category/{id}",
     *     tags={"Category"},
     *     summary="Update category by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Updated Fiction")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Category updated successfully."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Updated Fiction")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Category not found")
     * )
     */
    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'status' => 404,
                'message' => 'Category not found.',
                'data' => null
            ], 404);
        }

        $request->validate(['name' => 'string|max:255']);
        $category->update($request->all());

        return response()->json([
            'status' => 200,
            'message' => 'Category updated successfully.',
            'data' => $category
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/category/{id}",
     *     tags={"Category"},
     *     summary="Delete category by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Category deleted successfully."),
     *             @OA\Property(property="data", type="string", example=null)
     *         )
     *     ),
     *     @OA\Response(response=404, description="Category not found")
     * )
     */
    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category){
            return response()->json([
                'status' => 404,
                'message' => 'Category not found.',
                'data' => null
            ], 404);
        }

        $category->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Category deleted successfully.',
            'data' => null
        ], 200);
    }
    /**
 * @OA\Get(
 *     path="/category/search",
 *     tags={"Category"},
 *     summary="Search categories by name",
 *     @OA\Parameter(
 *         name="q",
 *         in="query",
 *         required=true,
 *         description="Search query for category name",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Search results retrieved successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="message", type="string", example="Search results retrieved successfully."),
 *             @OA\Property(property="data", type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="name", type="string", example="Fiction")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(response=400, description="Search query is required")
 * )
 */
// public function search(Request $request)
// {
//     $query = $request->query('q');

//     if (!$query) {
//         return response()->json([
//             'status' => 400,
//             'message' => 'Search query is required.',
//             'data' => []
//         ], 400);
//     }

//     $categories = Category::where('name', 'like', '%' . $query . '%')->get();

//     return response()->json([
//         'status' => 200,
//         'message' => 'Search results retrieved successfully.',
//         'data' => $categories
//     ], 200);
// }

}


