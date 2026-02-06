<?php

namespace App\Infrastructure\Providers;

use App\Application\Abstraction\Bus\ICommandBus;
use App\Application\Abstraction\Bus\IQueryBus;
use App\Application\Gateways\CustomerGateway;
use App\Application\Gateways\ProductGateway;
use App\Application\Interfaces\IOrderReadRepository;
use App\Application\Provider\CommandMap;
use App\Application\Provider\QueryMap;
use App\Infrastructure\Interfaces\OrderReadRepository;
use App\Infrastructure\Messaging\Bus\CommandBus;
use App\Infrastructure\Messaging\Bus\Middlewares\LoggingMiddleware;
use App\Infrastructure\Messaging\Bus\Middlewares\ResultDataTransformMiddleware;
use App\Infrastructure\Messaging\Bus\Middlewares\TransactionMiddleware;
use App\Infrastructure\Messaging\Bus\QueryBus;
use App\Infrastructure\Messaging\Gateways\CustomerHttpClient;
use App\Infrastructure\Messaging\Gateways\ProductHttpClient;
use Illuminate\Support\ServiceProvider;

final class InfrastructureServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(IOrderReadRepository::class, OrderReadRepository::class);
        // $this->app->bind(IOrderlineRepository::class, OrderlineRepository::class);

        $this->app->singleton(ICommandBus::class, function ($app) {
            $map = [];
            
            foreach(CommandMap::MAP as $command => $handlerClass) {
                $map[$command] = $app->make($handlerClass);
            }
            $middleware = [
                $app->make(LoggingMiddleware::class),
                $app->make(TransactionMiddleware::class),
                $app->make(ResultDataTransformMiddleware::class)
            ];

            return new CommandBus(
                $map,
                $middleware
            );
        });

        $this->app->singleton(IQueryBus::class, function ($app) {
            $map = [];

            foreach(QueryMap::MAP as $query => $handlerClass) {
                $map[$query] = $app->make($handlerClass);
            }

            $middleware = [
                $app->make(LoggingMiddleware::class),
                $app->make(ResultDataTransformMiddleware::class)
            ];

            return new QueryBus(
                $map,
                $middleware
            );
        });

        $this->app->bind(ProductGateway::class, ProductHttpClient::class);
        $this->app->bind(CustomerGateway::class, CustomerHttpClient::class);
        
    }
}