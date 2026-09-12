<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

/** Accounting Module — DI Bindings (write-side repository interfaces only) */
class AccountingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            \App\Domain\Accounting\ChartOfAccount\Repositories\ChartOfAccountRepositoryInterface::class,
            \App\Infrastructure\Accounting\ChartOfAccount\Persistence\Eloquent\EloquentChartOfAccountRepository::class,
        );

    }

    public function boot(): void {}
}
