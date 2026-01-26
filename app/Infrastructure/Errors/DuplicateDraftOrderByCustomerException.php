<?php

namespace App\Infrastructure\Errors;

class DuplicateDraftOrderByCustomerException extends InfrastructureExceptions
{
    public function __construct(
        ?array $details = [],
        ?string $message = 'One customer may not have multiple orders in draft state.',
        ?int $httpMessage = 400,
        ?int $code = 0
    ) {
        parent::__construct($message, $httpMessage, $code, $details);
    }
}