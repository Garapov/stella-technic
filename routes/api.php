<?php

use App\Http\Controllers\Api\ImportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return response()->json(['message' => 'List of posts']);
// });

Route::post('1s/update', [ImportController::class, 'update'])->middleware('auth:sanctum');
