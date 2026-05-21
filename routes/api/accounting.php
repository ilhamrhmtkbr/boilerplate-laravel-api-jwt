<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Presentation\Http\Controllers\Accounting\ChartOfAccountController;
use App\Presentation\Http\Controllers\Accounting\GeneralLedgerController;
use App\Presentation\Http\Controllers\Accounting\TrialBalanceController;
use App\Presentation\Http\Controllers\Accounting\ClosingEntryController;


Route::prefix('accounting')->name('accounting.')->group(function () {

    // ChartOfAccount
    Route::get('chart-of-accounts/export/list-pdf',    [ChartOfAccountController::class, 'exportPdf'])->name('chart-of-accounts.export.pdf');
    Route::get('chart-of-accounts/export/excel',       [ChartOfAccountController::class, 'exportExcel'])->name('chart-of-accounts.export.excel');
    Route::apiResource('chart-of-accounts', ChartOfAccountController::class);
    Route::get('chart-of-accounts/{chartOfAccount}/pdf',        [ChartOfAccountController::class, 'downloadPdf'])->name('chart-of-accounts.pdf');

    // GeneralLedger
    Route::get('general-ledgers/export/list-pdf',    [GeneralLedgerController::class, 'exportPdf'])->name('general-ledgers.export.pdf');
    Route::get('general-ledgers/export/excel',       [GeneralLedgerController::class, 'exportExcel'])->name('general-ledgers.export.excel');
    Route::apiResource('general-ledgers', GeneralLedgerController::class);
    Route::get('general-ledgers/{generalLedger}/pdf',        [GeneralLedgerController::class, 'downloadPdf'])->name('general-ledgers.pdf');

    // TrialBalance
    Route::get('trial-balances/export/list-pdf',    [TrialBalanceController::class, 'exportPdf'])->name('trial-balances.export.pdf');
    Route::get('trial-balances/export/excel',       [TrialBalanceController::class, 'exportExcel'])->name('trial-balances.export.excel');
    Route::apiResource('trial-balances', TrialBalanceController::class);
    Route::get('trial-balances/{trialBalance}/pdf',        [TrialBalanceController::class, 'downloadPdf'])->name('trial-balances.pdf');

    // ClosingEntry
    Route::get('closing-entries/export/list-pdf',    [ClosingEntryController::class, 'exportPdf'])->name('closing-entries.export.pdf');
    Route::get('closing-entries/export/excel',       [ClosingEntryController::class, 'exportExcel'])->name('closing-entries.export.excel');
    Route::apiResource('closing-entries', ClosingEntryController::class);
    Route::get('closing-entries/{closingEntry}/pdf',        [ClosingEntryController::class, 'downloadPdf'])->name('closing-entries.pdf');

});
