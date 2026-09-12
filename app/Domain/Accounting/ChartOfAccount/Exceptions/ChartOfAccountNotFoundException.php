<?php

declare(strict_types=1);

namespace App\Domain\Accounting\ChartOfAccount\Exceptions;

use DomainException;

final class ChartOfAccountNotFoundException extends DomainException
{
    public static function withId(int $id): self
    {
        return new self("ChartOfAccount with ID {$id} not found.", 404);
    }
}
