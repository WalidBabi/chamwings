<?php

use App\Http\Controllers\CustomerSegmentationController;
use App\Http\Controllers\testcontroller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/hello', [testcontroller::class, 'test']);
Route::post('/run-segmentation', [CustomerSegmentationController::class, 'runSegmentation']);
Route::get('/segmentation-results', [CustomerSegmentationController::class, 'getLatestResults']);