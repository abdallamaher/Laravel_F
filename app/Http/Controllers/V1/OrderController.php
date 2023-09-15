<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Services\OrderService;
use Illuminate\Http\Response;

class OrderController extends Controller
{

    public function __construct(protected OrderService $service)
    {
    }

    /**
     * Store a newly created resource in storage.
     * @param  OrderRequest  $request
     * @return Response
     */
    public function store(OrderRequest $request): Response
    {
        $order = $this->service->store($request->validated());
        return response($order, Response::HTTP_CREATED);
    }
}
