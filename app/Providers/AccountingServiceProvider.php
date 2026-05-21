<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

/** Accounting Module — DI Bindings */
class AccountingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            \App\Domain\Accounting\Repositories\Contracts\ChartOfAccountRepositoryInterface::class,
            \App\Infrastructure\Accounting\Repositories\ChartOfAccountRepository::class,
        );

        $this->app->bind(
            \App\Domain\Accounting\Repositories\Contracts\GeneralLedgerRepositoryInterface::class,
            \App\Infrastructure\Accounting\Repositories\GeneralLedgerRepository::class,
        );

        $this->app->bind(
            \App\Domain\Accounting\Repositories\Contracts\TrialBalanceRepositoryInterface::class,
            \App\Infrastructure\Accounting\Repositories\TrialBalanceRepository::class,
        );

        $this->app->bind(
            \App\Domain\Accounting\Repositories\Contracts\ClosingEntryRepositoryInterface::class,
            \App\Infrastructure\Accounting\Repositories\ClosingEntryRepository::class,
        );

    }

    public function boot(): void {}
}
