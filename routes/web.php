<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaxCutPdfController;

Route::get('/', function () {
    return redirect('/admin');
});

Route::middleware(['web'])->group(function () {
    Route::get('/tax-cut/{taxCut}/pdf', [TaxCutPdfController::class, 'generate'])->name('tax-cut.pdf');
    Route::get('/tax-cut/{taxCut}/download', [TaxCutPdfController::class, 'download'])->name('tax-cut.download');
});
