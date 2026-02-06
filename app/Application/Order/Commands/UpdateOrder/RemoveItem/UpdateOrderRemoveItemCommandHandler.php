<?php

namespace App\Application\Order\Commands\UpdateOrder\RemoveItem;

use App\Application\Abstraction\BaseCommandHandler;
use App\Application\Abstraction\ICommand;
use App\Application\Errors\ServiceNotReachedException;
use App\Application\Gateways\ProductGateway;
use App\Application\Order\Commands\UnpackingOrderItems;
use App\Domain\OrderAggregate\Order;
use App\Domain\OrderAggregate\ValueObjects\OrderId;
use App\Domain\Shared\Uuid;
use Illuminate\Http\Client\ConnectionException;

final class UpdateOrderRemoveItemCommandHandler extends BaseCommandHandler
{
    public function __construct(private ProductGateway $productGateway) {
    }

    protected function Execute(ICommand $command): Uuid|array|null
    {
        /** @var UpdateOrderRemoveItemCommand $command */
        $orderId = OrderId::fromString($command->orderId);
        $products = $this->retrieveProducts($command->products);
        $orderlines = UnpackingOrderItems::unpackingOrderItems($orderId, $command->products, $products);

        Order::retrieve($orderId->value())
            ->removeOrderItems($orderlines)
            ->persist();

        return $orderId->getId();   
    }

    protected function ClearAggregateState(): void
    {
    }

    public function retrieveProducts(array $commandProducts): ?array
    {
        try {
            $productIds = collect($commandProducts)->map(fn ($value) => $value['product_id'])->all();
            return $this->productGateway->getProductPricesAndQuantities($productIds);
        } catch (ConnectionException $th) {
            throw new ServiceNotReachedException($commandProducts, "We’re unable to retrieve product information right now. Please try again in a few minutes.", $th);
        }
    }
}