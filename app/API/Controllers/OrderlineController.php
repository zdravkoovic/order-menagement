<?php

namespace App\API\Controllers;

use App\API\Http\Requests\CreateOrderlineRequest;
use App\Application\Abstraction\Bus\ICommandBus;
use App\Application\Orderline\Commands\CreateOrderline\CreateOrderlineCommand;
use App\Domain\OrderlineAggregate\Orderline;
use Illuminate\Http\Request;

class OrderlineController
{
    public function __construct(
      private readonly ICommandBus $commandBus
    ){}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateOrderlineRequest $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validated();

        $result = $this->commandBus->dispatch(new CreateOrderlineCommand(
            $validated['order_id'],
            $validated['product_ids'],
            $validated['quantities'],
        ));

        if($result->success) return response()->json($result->data, $result->httpStatus);
        return response()->json($result->appError, $result->httpStatus);
    }

    /**
     * Display the specified resource.
     */
    public function show(Orderline $orderline)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Orderline $orderline)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Orderline $orderline)
    {
        //
    }
}
