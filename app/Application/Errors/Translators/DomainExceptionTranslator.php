<?php

namespace App\Application\Errors\Translators;

use App\Application\Errors\ApplicationException;
use App\Application\Errors\Messages\UserErrorMessage;
use App\Domain\OrderAggregate\Errors\DraftOrderDuplicateByCustomerException;
use App\Domain\OrderAggregate\Errors\InvalidOrderStateException;
use App\Domain\OrderAggregate\Errors\OrderExpirationTimeViolated;
use App\Domain\OrderAggregate\Errors\OrderItemMissmatchException;
use App\Domain\OrderAggregate\Errors\OrderNotFoundException;
use App\Domain\OrderAggregate\Errors\PaymentMethodUndefinedException;
use App\Domain\OrderAggregate\Errors\QuantityIsViolatedException;
use App\Domain\OrderAggregate\Errors\ReferenceUndefinedException;
use App\Domain\OrderAggregate\Errors\TotalAmountViolationException;
use Throwable;

final class DomainExceptionTranslator 
{
    public static function translate(Throwable $e): ?UserErrorMessage
    {
        return match (true) {
            $e instanceof PaymentMethodUndefinedException => 
                new UserErrorMessage(
                    'Payment method is required to complete your order.',
                    422
                ),
            $e instanceof TotalAmountViolationException => 
                new UserErrorMessage(
                    'You must have at least one ordered product to be able to order.',
                    422
                ),
            $e instanceof OrderExpirationTimeViolated =>
                new UserErrorMessage(
                    'Your order is expired. Create new one.',
                    409
                ),
            $e instanceof ReferenceUndefinedException => 
                new UserErrorMessage(
                    'Order reference is missing and the operation cannot be completed.',
                    422
                ),
            $e instanceof DraftOrderDuplicateByCustomerException => 
                new UserErrorMessage(
                    $e->getMessage(),
                    422
                ),
            $e instanceOf InvalidOrderStateException =>
                new UserErrorMessage(
                    $e->getMessage(),
                    422
                ),
            $e instanceof OrderNotFoundException =>
                new UserErrorMessage(
                    $e->getMessage(),
                    422
                ),
            $e instanceof OrderItemMissmatchException =>
                new UserErrorMessage(
                    $e->getMessage(),
                    422
                ),
            default => null,
        };
    }
}