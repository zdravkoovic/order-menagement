<?php

namespace App\Application\Errors\Translators;

use App\Application\Errors\ApplicationException;
use App\Application\Errors\Messages\UserErrorMessage;
use App\Infrastructure\Errors\DuplicateDraftOrderByCustomerException;
use Throwable;

final class InfrastructureExceptionTranslator
{
    public static function translate(Throwable $e): ?UserErrorMessage
    {
        return match (true) {
            $e instanceof DuplicateDraftOrderByCustomerException => 
                new UserErrorMessage(
                    'You cannot make another order if you already have one active order session.',
                    422
                ),
            default => null
        };
    }
}