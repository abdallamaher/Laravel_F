<?php

namespace App\Services;

use App\Models\Order;
use App\Repositories\OrderRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Services\IngredientService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class OrderService
{

    public function __construct(
        private OrderRepository $_orderRepository,
    ) {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param array $data
     * @return Order
     * @throws Exception
     */
    public function store(array $order): Order
    {
        return DB::transaction(function () use ($order) {
            $requiredIngredients = App(ProductService::class)->checkStock($order['products']);
            $orderData = $this->_orderRepository->create($order);
            App(IngredientService::class)->updateStock($requiredIngredients);
            return $orderData;
        });
    }
}
