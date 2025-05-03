<?php


use App\Http\Controllers\API\CategorySwaggerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\API\ItemsSwaggerController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('category', CategoryController::class);
Route::apiResource('items', ItemController::class);
Route::apiResource('users', UserController::class);
Route::apiResource('transaction', TransactionController::class);
Route::apiResource('categoryswagger', CategorySwaggerController::class);
Route::apiResource('itemsswagger', ItemsSwaggerController::class);

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


Route::group([], function () {
    Route::get('items', [ItemsSwaggerController::class, 'listItem']);
});