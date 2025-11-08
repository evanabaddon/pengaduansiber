<?php

use App\Filament\Pages\Persuratan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\AnggaranEditorController;
use App\Http\Controllers\UniversalLoginController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use App\Http\Controllers\OnlyOfficeController;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/subbagrenmin/anggaran/editor/{id}', [AnggaranEditorController::class, 'edit']);
Route::post('/subbagrenmin/anggaran/editor/callback/{id}', [AnggaranEditorController::class, 'callback']);
Route::get('/subbagrenmin/anggaran/view/{id}', [AnggaranEditorController::class, 'view']);
Route::get('/subbagrenmin/anggaran/download/{id}', [AnggaranEditorController::class, 'download']);
// Route::get('anggaran/{id}/convert-pdf', [AnggaranEditorController::class, 'convertToPdf'])->name('anggaran.convertPdf');
Route::get('anggaran/{id}/preview', function($id){
    $record = \App\Models\Anggaran::findOrFail($id);
    return view('filament.subbagrenmin.pages.anggaran-preview', compact('record'));
})->name('anggaran.preview');
Route::get('anggaran/{id}/convert-pdf', [AnggaranEditorController::class, 'convertExcelToPdf'])->name('anggaran.convertPdf');
Route::get('/onlyoffice/{surat}', [OnlyOfficeController::class, 'edit'])->name('onlyoffice.edit');
Route::get('/onlyoffice/{surat}/download', [OnlyOfficeController::class, 'download'])->name('onlyoffice.download');
Route::post('/onlyoffice/callback/{surat}', [OnlyOfficeController::class, 'callback'])
    ->name('onlyoffice.callback');