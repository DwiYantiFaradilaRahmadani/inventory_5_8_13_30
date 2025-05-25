<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class TransactionSwaggerController extends Controller
{
    /**
 * @OA\Schema(
 *     schema="Transaction",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="item_id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=2),
 *     @OA\Property(property="nama_barang", type="string", example="Laptop"),
 *     @OA\Property(property="tanggal_transaksi", type="string", format="date", example="2024-05-01"),
 *     @OA\Property(property="tipe_transaksi", type="string", enum={"masuk", "keluar"}, example="masuk"),
 *     @OA\Property(property="jumlah", type="integer", example=10)
 * )
 */

    /**
     * @OA\Get(
     *     path="/transaction",
     *     tags={"Transaction"},
     *     summary="Get all transactions",
     *     @OA\Response(
     *         response=200,
     *         description="List of transactions",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Transaction"))
     *     )
     * )
     */
    /**
     * @OA\Schema(
     *     schema="Transaction",
     *     type="object",
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="item_id", type="integer", example=1),
     *     @OA\Property(property="user_id", type="integer", example=2),
     *     @OA\Property(property="nama_barang", type="string", example="Laptop"),
     *     @OA\Property(property="tanggal_transaksi", type="string", format="date", example="2024-05-01"),
     *     @OA\Property(property="tipe_transaksi", type="string", enum={"masuk", "keluar"}, example="masuk"),
     *     @OA\Property(property="jumlah", type="integer", example=10)
     * )
     */

    public function index()
    {
        $transactions = Transaction::with(['item', 'user'])->get();
        return response()->json($transactions, 200);
    }

    /**
     * @OA\Get(
     *     path="/transaction/user/{user_id}",
     *     tags={"Transaction"},
     *     summary="Get transactions by user ID",
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         required=true,
     *         description="ID of the user",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of transactions by user",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Transaction"))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found or no transactions"
     *     )
     * )
     */
    public function getByUser($user_id)
    {
        $transactions = Transaction::with(['item', 'user'])
            ->where('user_id', $user_id)
            ->get();

        if ($transactions->isEmpty()) {
            return response()->json(['message' => 'Transaksi tidak ditemukan untuk user ini.'], 404);
        }

        return response()->json($transactions, 200);
    }

    /**
     * @OA\Post(
     *     path="/transaction",
     *     tags={"Transaction"},
     *     summary="Create a new transaction",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"item_id", "user_id", "nama_barang", "tanggal_transaksi", "tipe_transaksi", "jumlah"},
     *             @OA\Property(property="item_id", type="integer", example=1),
     *             @OA\Property(property="user_id", type="integer", example=2),
     *             @OA\Property(property="nama_barang", type="string", example="Laptop"),
     *             @OA\Property(property="tanggal_transaksi", type="string", format="date", example="2024-05-01"),
     *             @OA\Property(property="tipe_transaksi", type="string", enum={"masuk", "keluar"}, example="masuk"),
     *             @OA\Property(property="jumlah", type="integer", example=10)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Transaction created",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Transaksi berhasil dibuat."),
     *             @OA\Property(property="transaction", type="object", ref="#/components/schemas/Transaction")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'user_id' => 'required|exists:users,id',
            'nama_barang' => 'required|string|max:255',
            'tanggal_transaksi' => 'required|date',
            'tipe_transaksi' => 'required|in:masuk,keluar',
            'jumlah' => 'required|integer|min:1',
        ]);

        try {
            $item = Item::find($request->item_id);

            if ($request->tipe_transaksi === 'masuk') {
                $item->Stock += $request->jumlah;
            } elseif ($request->tipe_transaksi === 'keluar') {
                if ($item->Stock < $request->jumlah) {
                    return response()->json(['message' => 'Stok tidak cukup.'], 400);
                }
                $item->Stock -= $request->jumlah;
            }

            $item->save();

            $transaction = Transaction::create($request->only([
                'item_id', 'user_id', 'nama_barang', 'tanggal_transaksi', 'tipe_transaksi', 'jumlah'
            ]));

            return response()->json([
                'message' => 'Transaksi berhasil dibuat.',
                'transaction' => $transaction
            ], 201);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Pembuatan transaksi gagal.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/transaction/{id}",
     *     tags={"Transaction"},
     *     summary="Get transaction by ID",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Transaction found",
     *         @OA\JsonContent(ref="#/components/schemas/Transaction")
     *     ),
     *     @OA\Response(response=404, description="Transaction not found")
     * )
     */
    public function show($id)
    {
        $transaction = Transaction::with(['item', 'user'])->find($id);

        if (!$transaction) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        return response()->json($transaction, 200);
    }

    /**
     * @OA\Put(
     *     path="/transaction/{id}",
     *     tags={"Transaction"},
     *     summary="Update transaction by ID",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="item_id", type="integer", example=1),
     *             @OA\Property(property="user_id", type="integer", example=2),
     *             @OA\Property(property="nama_barang", type="string", example="Laptop Baru"),
     *             @OA\Property(property="tanggal_transaksi", type="string", format="date", example="2024-05-02"),
     *             @OA\Property(property="tipe_transaksi", type="string", enum={"masuk", "keluar"}, example="keluar"),
     *             @OA\Property(property="jumlah", type="integer", example=5)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Transaction updated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Transaksi berhasil diperbarui."),
     *             @OA\Property(property="transaction", ref="#/components/schemas/Transaction")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Transaction not found")
     * )
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'item_id' => 'sometimes|exists:items,id',
            'user_id' => 'sometimes|exists:users,id',
            'nama_barang' => 'sometimes|string|max:255',
            'tanggal_transaksi' => 'sometimes|date',
            'tipe_transaksi' => 'sometimes|in:masuk,keluar',
            'jumlah' => 'sometimes|integer|min:1',
        ]);

        try {
            $transaction = Transaction::find($id);

            if (!$transaction) {
                return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
            }

            $item = Item::find($transaction->item_id);

            if ($request->tipe_transaksi && $request->tipe_transaksi != $transaction->tipe_transaksi) {
                if ($request->tipe_transaksi === 'masuk') {
                    $item->Stock += $request->jumlah;
                } elseif ($request->tipe_transaksi === 'keluar') {
                    if ($item->Stock < $request->jumlah) {
                        return response()->json(['message' => 'Stok tidak cukup untuk transaksi keluar.'], 400);
                    }
                    $item->Stock -= $request->jumlah;
                }
            }

            $transaction->fill($request->only([
                'item_id', 'user_id', 'nama_barang', 'tanggal_transaksi', 'tipe_transaksi', 'jumlah'
            ]));

            $transaction->save();
            $item->save();

            return response()->json([
                'message' => 'Transaksi berhasil diperbarui.',
                'transaction' => $transaction
            ], 200);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Update transaksi gagal.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/transaction/{id}",
     *     tags={"Transaction"},
     *     summary="Delete transaction by ID",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Transaction deleted",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Transaksi berhasil dihapus"))
     *     ),
     *     @OA\Response(response=404, description="Transaction not found")
     * )
     */
    public function destroy($id)
    {
        $transaction = Transaction::find($id);

        if (!$transaction) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        $item = Item::find($transaction->item_id);

        if ($transaction->tipe_transaksi === 'masuk') {
            $item->Stock -= $transaction->jumlah;
        } elseif ($transaction->tipe_transaksi === 'keluar') {
            $item->Stock += $transaction->jumlah;
        }

        $transaction->delete();
        $item->save();

        return response()->json(['message' => 'Transaksi berhasil dihapus'], 200);
    }
}