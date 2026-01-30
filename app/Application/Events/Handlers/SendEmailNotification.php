<?php

namespace App\Application\Events\Handlers;

use App\Domain\OrderAggregate\Events\OrderCreated;

class SendEmailNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderCreated $event): void
    {
        echo "Sending email notification for Order ID: " . $event->order->id->value() . "\n";
    }
}
