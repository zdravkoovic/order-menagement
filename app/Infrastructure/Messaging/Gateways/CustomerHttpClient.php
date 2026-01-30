<?php

namespace App\Infrastructure\Messaging\Gateways;

use App\Application\Gateways\CustomerGateway;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

final class CustomerHttpClient implements CustomerGateway
{
    public function exists(string $customerId): bool
    {
        try {
            $response = Http::get(config('services.customer.uri') . $customerId);
            if($response->status() === 200) {
                return true;
            }
            return false;
        } catch (RequestException $e) {
            throw $e;
        }

    }
}