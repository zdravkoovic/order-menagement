<?php

namespace App\API\Controllers;

use App\API\Http\Requests\CreateOrderRequest;
use App\Application\Abstraction\Bus\ICommandBus;
use App\Application\Abstraction\Bus\IQueryBus;
use App\Application\Order\Commands\CreateOrder\CreateOrderCommand;
use App\Application\Order\Queries\GetOrder\ById\GetOrderByIdQuery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController
{
    public function __construct(
        private ICommandBus $commandBus,
        private IQueryBus $queryBus
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
    public function store(CreateOrderRequest $request)
    {
        $validated = $request->validated();
        $command = new CreateOrderCommand(
            $validated['customer_id'],
            $validated['is_guest'],
            $validated['amount'] ?? null,
            $validated['payment_method'] ?? null
        );

        try {
            $result = $this->commandBus->dispatch($command);
            if($result->success) return response()->json(['order_id' => $result->data], $result->httpStatus);
            return response()->json(['error' => $result->appError->message], $result->appError->httpStatus);
        } catch (\Throwable $e) {
            Log::critical('unhandled.command.exception', [
                'exception' => get_class($e), 
                'message' => $e->getMessage(),
                'command_id' => $command->commandId()
            ]);
            throw $e;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $result = $this->queryBus->dispatch(new GetOrderByIdQuery($id));
            if($result->success) return response()->json($result->data, $result->httpStatus);
            return response()->json($result->data, $result->httpStatus);
        } catch (\Throwable $th) {
            throw $th;
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
    public function destroy(string $id)
    {
        //
    }
}
