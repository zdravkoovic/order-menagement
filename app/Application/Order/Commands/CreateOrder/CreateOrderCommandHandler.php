<?php

namespace App\Application\Order\Commands\CreateOrder;

use App\Application\Abstraction\BaseCommandHandler;
use App\Application\Abstraction\ICommand;
use App\Application\Errors\InvalidRequestException;
use App\Application\Errors\ProductNotFoundExcpetion;
use App\Application\Errors\ServiceNotReachedException;
use App\Application\Gateways\CustomerGateway;
use App\Application\Gateways\ProductGateway;
use App\Application\Interfaces\IOrderReadRepository;
use App\Application\Order\Commands\UnpackingOrderItems;
use App\Application\Order\ExpirationPolicy\GuestOrderExpirationPolicy;
use App\Application\Order\ExpirationPolicy\RegisteredOrderExpirationPolicy;
use App\Domain\OrderAggregate\Errors\DraftOrderDuplicateByCustomerException;
use App\Domain\OrderAggregate\Order;
use App\Domain\OrderAggregate\ValueObjects\Customer;
use App\Domain\OrderAggregate\ValueObjects\OrderId;
use App\Domain\Shared\Uuid;
use DateTimeImmutable;
use Illuminate\Http\Client\ConnectionException;

final class CreateOrderCommandHandler extends BaseCommandHandler
{
    private ?OrderId $orderId;

    public function __construct(
        private IOrderReadRepository $readRepos,
        private GuestOrderExpirationPolicy $guestOrderExpirationPolicy,
        private RegisteredOrderExpirationPolicy $registeredOrderExpirationPolicy,
        private CustomerGateway $gateway,
        private ProductGateway $productGateway
    ){
        parent::__construct();
    }

    protected function Execute(ICommand $command): Uuid | null
    {
        /** @var CreateOrderCommand $command */

        $products = $this->validateCustomerAndGetItems($command->customerId, $command->orderItems);

        $policy = $command->isGuest
            ? $this->guestOrderExpirationPolicy
            : $this->registeredOrderExpirationPolicy;

        $this->orderId = OrderId::generate();
        $customerId = Customer::fromString($command->customerId);
        $expiresAt = $policy->expiresAt(new DateTimeImmutable());
        $orderItems = UnpackingOrderItems::unpackingOrderItems($this->orderId, $command->orderItems, $products);

        Order::retrieve($this->orderId->value())
            ->createOrder($customerId, $expiresAt, $command->paymentMethod, $orderItems)
            ->persist();
        
        return $this->orderId->getId();
    }
    
    protected function ClearAggregateState(): void
    {
        $this->orderId = null;
    }

    private function validateCustomerAndGetItems(string $customerId, ?array $items): ?array
    {
        if(!Uuid::isValid(Uuid::fromString($customerId))) throw new InvalidRequestException("Customer ID is invalid", 422);
        $this->checkForDraftOrderDuplicate($customerId);
        if (!$items) return null;

        try {
            $productIds = collect($items)->map(fn ($value) => $value['product_id'])->all();
            $products = $this->productGateway->getProductPricesAndQuantities($productIds);
            return $products;
        } catch (ConnectionException $th) {
            throw new ServiceNotReachedException($items, 'Waiting for the service. Please, try later.', $th);
        }
    }

    private function checkForDraftOrderDuplicate(string $customerId): void
    {
        $order = $this->readRepos->findDraftOrderByCustomerId($customerId);
        if($order !== null) throw new DraftOrderDuplicateByCustomerException($order->uuid(), $customerId);
    }
}