<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     title="User",
 *     required={"id", "name", "email"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *     @OA\Property(property="username", type="string", example="johndoe123"),

 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T00:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-01T01:00:00Z")
 * )
 */
class UserSwaggerController extends Controller
{
     use HasApiTokens, Notifiable;
    /**
     * @OA\Get(
     *     path="/users",
     *     tags={"Users"},
     *     summary="Get all users or search by name/email",
     *     @OA\Parameter(
     *         name="q",
     *         in="query",
     *         required=false,
     *         description="Search query for name or email",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Users retrieved successfully."),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(ref="#/components/schemas/User")
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $query = $request->query('q');
        Log::info("User search query: " . $query);

        if ($query) {
            $users = User::where('name', 'like', '%' . $query . '%')
                ->orWhere('email', 'like', '%' . $query . '%')
                ->get();

            return response()->json([
                'status' => 200,
                'message' => 'Users retrieved successfully with query.',
                'data' => $users
            ], 200);
        }

        $users = User::all();

        return response()->json([
            'status' => 200,
            'message' => 'Users retrieved successfully.',
            'data' => $users
        ], 200);
    }

   /**
     * @OA\Post(
     *     path="/users",
     *     tags={"Users"},
     *     security={{"bearerAuth":{}}},
     *     summary="Create a new user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "username", "email", "password"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="username", type="string", example="johndoe123"),
     *             @OA\Property(property="email", type="string", example="john@example.com"),
     *             @OA\Property(property="password", type="string", example="secret123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User created",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User berhasil dibuat."),
     *             @OA\Property(property="user", ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=500, description="Server error")
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

    try {
        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            // password disimpan tanpa bcrypt (plain text)
            'password' => bcrypt($request->password),
        ]);

        return response()->json([
            'message' => 'User berhasil dibuat.',
            'user' => $user,
        ], 201);
    } catch (QueryException $e) {
        return response()->json([
            'message' => 'Gagal membuat user.',
            'error' => $e->getMessage(),
        ], 500);
    }
}


    /**
     * @OA\Get(
     *     path="/users/{id}",
     *     tags={"Users"},
     *     security={{"bearerAuth":{}}},
     *     summary="Get user by ID",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="User found", @OA\JsonContent(ref="#/components/schemas/User")),
     *     @OA\Response(response=404, description="User not found")
     * )
     */
    public function show($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        return response()->json($user, 200);
    }

    /**
 * @OA\Put(
 *     path="/users/{id}",
 *     tags={"Users"},
 *     security={{"bearerAuth":{}}},
 *     summary="Update user by ID",
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string", example="Jane Doe"),
 *             @OA\Property(property="email", type="string", example="jane@example.com"),
 *             @OA\Property(property="password", type="string", example="newpass456")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User updated",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="User berhasil diperbarui."),
 *             @OA\Property(property="user", ref="#/components/schemas/User")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="User tidak ditemukan")
 *         )
 *     )
 * )
 */
public function update(Request $request, $id)
{
    $user = User::find($id);
    if (!$user) {
        return response()->json(['message' => 'User tidak ditemukan'], 404);
    }

    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $id,
        'password' => 'nullable|string|min:6',
    ]);

    $user->fill($request->only(['name', 'email']));
    if ($request->filled('password')) {
        $user->password = bcrypt($request->password);
    }
    $user->save();

    return response()->json([
        'message' => 'User berhasil diperbarui.',
        'user' => $user,
    ], 200);
}


    /**
     * @OA\Delete(
     *     path="/users/{id}",
     *     tags={"Users"},
     *     security={{"bearerAuth":{}}},
     *     summary="Delete user by ID",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="User deleted",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="User berhasil dihapus"))
     *     ),
     *     @OA\Response(response=404, description="User not found")
     * )
     */
    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        $user->delete();
        return response()->json(['message' => 'User berhasil dihapus'], 200);
    }

    
}