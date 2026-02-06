<?php 

namespace App\Application\Order\Commands\CreateOrder;

use App\Application\Abstraction\IAction;
use App\Application\Abstraction\ICommand;
use App\Domain\Shared\Uuid;

final class CreateOrderCommand implements ICommand, IAction
{
    private string $commandId;

    public function __construct(
        public readonly string $customerId,
        public readonly bool $isGuest,
        public readonly ?array $orderItems = [],
        public readonly ?string $paymentMethod = null,
    ){
        $this->commandId = Uuid::generate();
    }

    public function commandId(): string
    {
        return $this->commandId;
    }

    public function toLogContext(): array
    {
        return [
            'traceId' => $this->commandId,
            'command' => self::class,
            'customer_id' => $this->customerId,
            'is_guest' => $this->isGuest,
            'order_items' => $this->orderItems,
            'payment_method' => $this->paymentMethod
        ];
    }
}