<?php

namespace App\Application\Order\Commands\UpdateOrder\AddItem;

use App\Application\Abstraction\BaseCommandHandler;
use App\Application\Abstraction\ICommand;
use App\Application\Errors\ProductNotFoundExcpetion;
use App\Application\Errors\ProductQuantityTooLowException;
use App\Application\Errors\ServiceNotReachedException;
use App\Application\Gateways\ProductGateway;
use App\Application\Order\Commands\UnpackingOrderItems;
use App\Domain\OrderAggregate\Order;
use App\Domain\OrderAggregate\ValueObjects\OrderId;
use App\Domain\OrderAggregate\ValueObjects\Quantity;
use App\Domain\Shared\Uuid;
use Illuminate\Http\Client\ConnectionException;

final class UpdateOrderAddItemCommandHandler extends BaseCommandHandler
{
    public function __construct(private ProductGateway $productGateway)
    {
        parent::__construct();
    }
    /** @param UpdateOrderAddItemCommand $command */
    protected function Execute(ICommand $command): Uuid|array|null
    {
        $products = $this->retrieveProducts($command->products);

        $orderId = OrderId::fromString($command->orderId);
        $items = UnpackingOrderItems::unpackingOrderItems($orderId, $command->products, $products);
        
        Order::retrieve($orderId->value())
            ->checkStockQuantity($items, $products)
            ->addOrderItems($items)
            ->persist();
        
        return $orderId->getId();
    }


    protected function ClearAggregateState(): void
    {
       
    }

    private function retrieveProducts(array $requestedProducts): ?array
    {
        try {
            $productIds = collect($requestedProducts)->map(fn($value) => $value['product_id'])->all();
            return $this->productGateway->getProductPricesAndQuantities($productIds);
        } catch (ConnectionException $th) {
            throw new ServiceNotReachedException($requestedProducts, 'We’re unable to retrieve product information right now. Please try again in a few minutes.', $th);
        }
    }
}