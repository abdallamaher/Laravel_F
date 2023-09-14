<?php

namespace App\Repositories;

use App\Models\Order;
use Exception;

class OrderRepository
{

    public function __construct(
        private Order $_order
    ) {
    }

    /**
     * Create a new order.
     *
     * @param array $order
     * @return Order
     * @throws Exception
     */
    public function create(array $order): Order | Exception
    {
        $orderData = $this->_order->create($order);
        $orderData->products()->attach($order['products']);
        return $orderData;
    }
}
