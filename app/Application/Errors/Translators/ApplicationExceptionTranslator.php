<?php

namespace App\Application\Errors\Translators;

use App\Application\Errors\ApplicationException;
use App\Application\Errors\InvalidRequestException;
use App\Application\Errors\Messages\UserErrorMessage;

final class ApplicationExceptionTranslator
{
    public static function translate(ApplicationException $e) : ?UserErrorMessage
    {
        return match (true)
        {
            $e instanceof InvalidRequestException =>
                new UserErrorMessage(
                    $e->getMessage(),
                    $e->httpStatus
                ),
            default => null
        };
    }
}