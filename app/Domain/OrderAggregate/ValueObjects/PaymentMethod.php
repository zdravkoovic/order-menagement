<?php

namespace App\Domain\OrderAggregate\ValueObjects;

enum PaymentMethod : string
{
    case UNDEFINED = "undefined";
    case PAYPAL = "paypal";
    case CREDIT_CARD = "credit_card";
    case VISA = "visa";
    case MASTER_CARD = "master_card";
    case BITCOIN = "bitcoin";
}