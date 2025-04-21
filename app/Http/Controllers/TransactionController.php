<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class TransactionController extends Controller
{
   
    public function index()
    {
        $transactions = Transaction::with(['item', 'user'])->get();
        return response()->json($transactions, 200);
    }

   
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

            
            if ($request->tipe_transaksi == 'masuk') {
                
                $item->Stock += $request->jumlah;
            } elseif ($request->tipe_transaksi == 'keluar') {
               
                if ($item->Stock < $request->jumlah) {
                    return response()->json([
                        'message' => 'Stok tidak cukup.',
                    ], 400);
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

  
    public function show($id)
    {
        $transaction = Transaction::with(['item', 'user'])->find($id);

        if (!$transaction) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        return response()->json($transaction, 200);
    }

  
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
                
                if ($request->tipe_transaksi == 'masuk') {
                    $item->Stock += $request->jumlah;
                } elseif ($request->tipe_transaksi == 'keluar') {
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

   
    public function destroy($id)
    {
        $transaction = Transaction::find($id);

        if (!$transaction) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

       
        $item = Item::find($transaction->item_id);

        
        if ($transaction->tipe_transaksi == 'masuk') {
            $item->Stock -= $transaction->jumlah; 
        } elseif ($transaction->tipe_transaksi == 'keluar') {
            $item->Stock += $transaction->jumlah; 
        }

       
        $transaction->delete();
        $item->save();

        return response()->json(['message' => 'Transaksi berhasil dihapus'], 200);
    }
}
