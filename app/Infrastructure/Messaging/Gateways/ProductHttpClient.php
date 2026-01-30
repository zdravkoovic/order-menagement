<?php

namespace App\Infrastructure\Messaging\Gateways;

use App\Application\Gateways\ProductGateway;
use App\Application\Orderline\DTOs\ProductPricesAndQuantities;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Nette\NotImplementedException;
use Throwable;

final class ProductHttpClient implements ProductGateway
{
    public function existAll(array $productIds): bool
    {
        throw new NotImplementedException("Must be implemented in the product service first");
    }

    public function exists(int $productId): bool
    {
        $response = Http::get(config('services.product.uri') . $productId);

        if($response->status() === 200) {
            return true;
        }
        return false;
    }

    public function getProductPricesAndQuantities(array $productIds): ?array
    {
        $response = Http::post(config('services.product.uri')  . "/prices&quantities", ["productIds" => $productIds]);
        if($response->successful()) {
            $data = $response['products'];
            /** @var array $data */
            $result = [];
            foreach($data as $product) {
                /** @var ProductPricesAndQuantities $product */
                $result[$product['productId']] = ["price" => $product['price'], "quantity" => $product['quantity']];
            }
            return $result;
        }
        return null;
    }
}
