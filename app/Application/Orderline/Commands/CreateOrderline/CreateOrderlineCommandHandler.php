<?php

namespace App\Application\Orderline\Commands\CreateOrderline;

use App\Application\Abstraction\BaseCommandHandler;
use App\Application\Abstraction\ICommand;
use App\Application\Gateways\ProductGateway;
use App\Domain\IAggregateRoot;
use App\Domain\Interfaces\IOrderlineRepository;
use App\Domain\OrderlineAggregate\Orderline;
use App\Domain\Shared\Uuid;

final class CreateOrderlineCommandHandler extends BaseCommandHandler
{
    private ?array $createdOrderlines;

    public function __construct(
        private IOrderlineRepository $orderlines,
        private ProductGateway $productGateway
    ) {}

    /**
     * Undocumented function
     *
     * @param CreateOrderlineCommand $command
     * @return Uuid|int|null
     */
    public function Execute(ICommand $command): Uuid | array | null
    {
        $productIds = $command->productIds;
        $pricesAndQuantities = $this->productGateway->getProductPricesAndQuantities($productIds);
        if($pricesAndQuantities === null) return null;

        $orderlineIds = [];
        
        foreach($pricesAndQuantities as $productId => $data)
        {
            $createdOrderline = Orderline::Create(
                $productId,
                $data['quantity'],
                $data['price'],
                $command->orderId
            );
            $orderlineIds[] = $this->orderlines->save($createdOrderline)->value();
            $this->createdOrderlines[] = $createdOrderline;
        }

        return $orderlineIds;
    }

    public function GetAggregateRoot(): ?IAggregateRoot
    {
        throw $this->createdOrderlines[0];
    }

    protected function ClearAggregateState(): void
    {
        $this->createdOrderlines = [];
    }
}