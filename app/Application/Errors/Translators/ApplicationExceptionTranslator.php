<?php

namespace App\Application\Errors\Translators;

use App\Application\Errors\ApplicationException;
use App\Application\Errors\InvalidRequestException;
use App\Application\Errors\Messages\UserErrorMessage;
use App\Application\Errors\ProductNotFoundExcpetion;
use App\Application\Errors\ProductQuantityTooLowException;
use App\Application\Errors\ServiceNotReachedException;

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
            $e instanceof ProductNotFoundExcpetion =>
                new UserErrorMessage(
                    $e->getMessage(),
                    $e->httpStatus
                ),
            $e instanceof ProductQuantityTooLowException =>
                new UserErrorMessage(
                    $e->getMessage(),
                    $e->httpStatus
                ),
            $e instanceof ServiceNotReachedException =>
                new UserErrorMessage(
                    $e->getMessage(),
                    503
                ),
            default => null
        };
    }
}