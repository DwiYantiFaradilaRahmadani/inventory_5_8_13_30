<?php


use App\Http\Controllers\API\CategorySwaggerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\API\ItemsSwaggerController;
use App\Http\Controllers\API\TransactionSwaggerController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('category', CategoryController::class);
Route::apiResource('items', ItemController::class);
Route::apiResource('users', UserController::class);
Route::apiResource('transaction', TransactionController::class);
Route::apiResource('categoryswagger', CategorySwaggerController::class);
Route::apiResource('itemsswagger', ItemsSwaggerController::class);
Route::apiResource('transactionsswagger', TransactionSwaggerController::class);

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group( [], function () {
    Route::get('category', [CategorySwaggerController::class, 'listCategory']);
});
Route::group( [], function () {
    Route::get('item', [ItemsSwaggerController::class, 'listitem']);
});

Route::get('items-swagger', [ItemsSwaggerController::class, 'index']);
Route::post('items-swagger', [ItemsSwaggerController::class, 'store']);
Route::get('items-swagger/{id}', [ItemsSwaggerController::class, 'show']);
Route::put('items-swagger/{id}', [ItemsSwaggerController::class, 'update']);
Route::delete('items-swagger/{id}', [ItemsSwaggerController::class, 'destroy']);

Route::get('transactions-swagger', [TransactionSwaggerController::class, 'index']);
Route::post('transactions-swagger', [TransactionSwaggerController::class, 'store']);
Route::get('transactions-swagger/{id}', [TransactionSwaggerController::class, 'show']);
Route::put('transactions-swagger/{id}', [TransactionSwaggerController::class, 'update']);
Route::delete('transactions-swagger/{id}', [TransactionSwaggerController::class, 'destroy']);