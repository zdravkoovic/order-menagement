<?php

namespace App\Application\Errors;

use Throwable;

final class ProductQuantityTooLowException extends ApplicationException
{
    public function __construct(int $productId, string $name, int $quantity, string $message = '', int $httpStatus = 422, int $code = 0, ?Throwable $previous = null)
    {
        if($message === '') $message = 'Sorry, but we do not have requested quantity for ' . $name . ' product. We have just ' . $quantity . ' in market.';
        return parent::__construct($message, $httpStatus, $code, $previous);
    }
}