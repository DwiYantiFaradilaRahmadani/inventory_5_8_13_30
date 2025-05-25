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
use App\Http\Controllers\UserSwaggerController;


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




Route::get('/item', [ItemsSwaggerController::class, 'index']);
Route::post('/item', [ItemsSwaggerController::class, 'store']);
Route::get('/item/{id}', [ItemsSwaggerController::class, 'show']);
Route::put('/item/{id}', [ItemsSwaggerController::class, 'update']);
Route::delete('/item/{id}', [ItemsSwaggerController::class, 'destroy']);
Route::get('/item/search', [ItemsSwaggerController::class, 'search']);

Route::get('transactions-swagger', [TransactionSwaggerController::class, 'index']);
Route::post('transactions-swagger', [TransactionSwaggerController::class, 'store']);
Route::get('transactions-swagger/{id}', [TransactionSwaggerController::class, 'show']);
Route::put('transactions-swagger/{id}', [TransactionSwaggerController::class, 'update']);
Route::delete('transactions-swagger/{id}', [TransactionSwaggerController::class, 'destroy']);
Route::get('/transaction/user/{user_id}', [TransactionSwaggerController::class, 'getByUser']);

Route::get('users-swagger', [UserSwaggerController::class, 'index']);
Route::post('users-swagger', [UserSwaggerController::class, 'store']);
Route::get('users-swagger/{id}', [UserSwaggerController::class, 'show']);
Route::put('users-swagger/{id}', [UserSwaggerController::class, 'update']);
Route::delete('users-swagger/{id}', [UserSwaggerController::class, 'destroy']);
Route::get('/users/search', [UserSwaggerController::class, 'search']);

// check auth
//nambahin aja