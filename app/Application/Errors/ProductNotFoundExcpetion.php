<?php

namespace App\Application\Errors;

final class ProductNotFoundExcpetion extends ApplicationException
{
    /** @param int[] $requestedProductIds */
    public function __construct(private array $requestedProductIds, ?string $message = 'Product not found.', ?int $httpStatus = 422) {
        parent::__construct($message, $httpStatus);
    }
}