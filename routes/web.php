<?php

use App\Filament\Pages\Persuratan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\AnggaranEditorController;
use App\Http\Controllers\UniversalLoginController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/subbagrenmin/anggaran/editor/{id}', [AnggaranEditorController::class, 'edit']);
Route::post('/subbagrenmin/anggaran/editor/callback/{id}', [AnggaranEditorController::class, 'callback']);
Route::get('/subbagrenmin/anggaran/view/{id}', [AnggaranEditorController::class, 'view']);
Route::get('/subbagrenmin/anggaran/download/{id}', [AnggaranEditorController::class, 'download']);

