<?php

namespace App\Http\Controllers;

use App\Exceptions\FailToCreateException;
use App\Http\Requests\GetTravelsRequest;
use App\Http\Requests\RequestStoreTravelOrder;
use App\Http\Resources\TravelOrderResource;
use App\Models\TravelOrder;
use App\Service\TravelOrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class TravelOrderController extends Controller
{
    public function __construct(private TravelOrderService $service) {}

    public function getTravelOrders(GetTravelsRequest $request): AnonymousResourceCollection
    {
        $filters = $request->validated();
        $paginatedTravelOrders = $this->service->getPaginated($filters);
        return TravelOrderResource::collection($paginatedTravelOrders);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @throws FailToCreateException
     */
    public function store(RequestStoreTravelOrder $request): JsonResponse
    {
        $travelOrder = $this->service->store($request->validated());
        return response()->json([
            'message' => 'Travel Order created successfully.',
            'travel_order' => $travelOrder,
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(TravelOrder $travelOrder) : TravelOrderResource
    {
        return new TravelOrderResource($travelOrder);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TravelOrder $travelOrder)
    {
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TravelOrder $travelOrder)
    {
        //
    }
}
