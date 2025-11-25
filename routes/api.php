<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ImportController;

// Route::get('/user', function (Request $request) {
//     return response()->json(['message' => 'List of posts']);
// });

Route::post('update', [ImportController::class, 'update']);
