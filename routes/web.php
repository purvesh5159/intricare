<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CustomFieldController;


Route::get('/', [ContactController::class, 'index']);

Route::get('/contacts/{id}', [ContactController::class, 'show']);

Route::get('/get-contacts', [ContactController::class, 'fetch']);
Route::post('/contacts', [ContactController::class, 'store']);

Route::get('/contacts/{master}/diff/{secondary}', [ContactController::class, 'diff']);
Route::post('/contacts/merge', [ContactController::class, 'merge']);
Route::put('/contacts/{id}', [ContactController::class, 'update']);
Route::delete('/contacts/{id}', [ContactController::class, 'destroy']);

Route::get('/custom-fields', [CustomFieldController::class, 'index']);
Route::post('/custom-fields', [CustomFieldController::class, 'store']);
Route::delete('/custom-fields/{id}', [CustomFieldController::class, 'destroy']);

