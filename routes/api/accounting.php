<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Presentation\Http\Controllers\Accounting\ChartOfAccountController;


Route::prefix('accounting')->name('accounting.')->group(function () {

    // ChartOfAccount
    Route::get('chart-of-accounts/export/list-pdf',    [ChartOfAccountController::class, 'exportPdf'])->name('chart-of-accounts.export.pdf');
    Route::get('chart-of-accounts/export/excel',       [ChartOfAccountController::class, 'exportExcel'])->name('chart-of-accounts.export.excel');
    Route::apiResource('chart-of-accounts', ChartOfAccountController::class);
    Route::get('chart-of-accounts/{chartOfAccount}/pdf',        [ChartOfAccountController::class, 'downloadPdf'])->name('chart-of-accounts.pdf');

});
