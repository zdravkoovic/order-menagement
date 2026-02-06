<?php

namespace App\API\Controllers;

use App\API\Http\Requests\CreateOrderlineRequest;
use App\API\Http\Requests\CreateOrderRequest;
use App\API\Http\Requests\RemoveOrderItemRequest;
use App\Application\Abstraction\Bus\ICommandBus;
use App\Application\Abstraction\Bus\IQueryBus;
use App\Application\Order\Commands\CreateOrder\CreateOrderCommand;
use App\Application\Order\Commands\DeleteOrder\DeleteOrderCommand;
use App\Application\Order\Commands\UpdateOrder\AddItem\UpdateOrderAddItemCommand;
use App\Application\Order\Commands\UpdateOrder\RemoveItem\UpdateOrderRemoveItemCommand;
use App\Application\Order\Queries\GetOrder\ById\GetOrderByIdQuery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class OrderController
{
    public function __construct(
        private readonly ICommandBus $commandBus,
        private readonly IQueryBus $queryBus
    ){}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateOrderRequest $request): JsonResponse
    {
        $validated = $request->validated();
        
        $command = new CreateOrderCommand(
            $validated['customer_id'], $validated['is_guest'],
            $validated['products'] ?? [], $validated['payment_method'] ?? null
        );

        try {
            $result = $this->commandBus->dispatch($command);
            if($result->success) return response()->json(['order_id' => $result->data], $result->httpStatus);
            return response()->json(['error' => $result->appError->message], $result->appError->httpStatus);
        } catch (\Throwable) {
            return response()->json("Server problems", 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeOrderItem(string $orderId, CreateOrderlineRequest $request): JsonResponse
    {
        $validated = $request->validated();
        
        $command = new UpdateOrderAddItemCommand($orderId, $validated['products']);

        try {
            $result = $this->commandBus->dispatch($command);
            if($result->success) return response()->json(['order_id' => $result->data], $result->httpStatus);
            return response()->json(['error' => $result->appError->message], $result->appError->httpStatus);
        } catch (\Throwable) {
            return response()->json("Server problems", 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $result = $this->queryBus->dispatch(new GetOrderByIdQuery($id));
            if($result->success) return response()->json($result->data, $result->httpStatus);
            return response()->json($result->data, $result->httpStatus);
        } catch (Throwable) {
            return response()->json("Server problems", 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $result = $this->commandBus->dispatch(new DeleteOrderCommand($id));
        if($result->success) return response()->json($result, 204);
        return response()->json($result->appError, $result->httpStatus);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroyOrderItem(string $orderId, RemoveOrderItemRequest $orderItems): JsonResponse
    {
        $validated = $orderItems->validated();
        $products = $validated['products'];

        $result = $this->commandBus->dispatch(new UpdateOrderRemoveItemCommand($orderId, $products));
        if($result->success) return response()->json($result, 204);
        return response()->json($result->appError, $result->httpStatus);
    }
}
