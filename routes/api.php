<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\CategorySwaggerController;
use App\Http\Controllers\API\TransactionSwaggerController;
use App\Http\Controllers\API\UserSwaggerController;
use App\Http\Controllers\API\ItemsSwaggerController;

use App\Http\Controllers\Auth\RegisterSwaggerController;
use App\Http\Controllers\Auth\LoginSwaggerController;
use App\Http\Controllers\Auth\LogoutSwaggerController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Semua endpoint yang berhubungan dengan data penting diproteksi 
| menggunakan middleware 'auth:sanctum'. Hanya register & login 
| yang bisa diakses tanpa token. Setelah login, user mendapatkan 
| token untuk mengakses API. Saat logout, token dihapus.
|
*/

// Endpoint bebas diakses
Route::post('/register', RegisterSwaggerController::class);
Route::post('/login', LoginSwaggerController::class);

// Semua route yang membutuhkan login
Route::middleware('auth:sanctum')->group(function () {
    
    // Get data user yang sedang login
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Logout (hapus token)
    Route::post('/logout', LogoutSwaggerController::class)->middleware('auth:sanctum');


    // API Resource (CRUD) dengan proteksi token
    Route::apiResource('category', CategorySwaggerController::class);
    Route::apiResource('item', ItemsSwaggerController::class);
    Route::apiResource('users', UserSwaggerController::class);
    Route::apiResource('transaction', TransactionSwaggerController::class);

    // Tambahan route manual yang juga diproteksi
    Route::get('transactions-swagger', [TransactionSwaggerController::class, 'index']);
    Route::post('transactions-swagger', [TransactionSwaggerController::class, 'store']);
    Route::get('transactions-swagger/{id}', [TransactionSwaggerController::class, 'show']);
    Route::put('transactions-swagger/{id}', [TransactionSwaggerController::class, 'update']);
    Route::delete('transactions-swagger/{id}', [TransactionSwaggerController::class, 'destroy']);

    Route::get('/transaction/user/{user_id}', [TransactionSwaggerController::class, 'getByUser']);
    Route::get('transaction', [TransactionSwaggerController::class, 'getAllData']);
});