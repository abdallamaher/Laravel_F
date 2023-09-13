<?php

namespace App\Services;

use App\Models\Ingredient;
use Exception;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class OrderService
{

    public function __construct(
        private Order $_order,
        private Product $_product,
        private Ingredient $_ingredient
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
            $requiredIngredients = $this->checkStock($order);
            $orderData = $this->_order->create($order);
            $orderData->products()->attach($order['products']);
            $this->updateStock($requiredIngredients);
            return $orderData;
        });
    }

    /**
     * Check if all products exist and have enough stock in ingredients in the database.
     *
     * @param array $order
     * @return array
     * @throws BadRequestHttpException
     */
    public function checkStock(array $order): array
    {
        // Map product id to quantity
        $productQuantity = [];
        foreach ($order['products'] as $product) {
            $productQuantity[$product['product_id']] = $product['quantity'];
        }

        // Lazy load product data with ingredients
        $productIds = collect($order['products'])->pluck('product_id')->toArray();
        $products = $this->_product->whereIn('id', $productIds)->with('ingredients')->get();

        // Calculate required amounts
        $requiredAmount = [];
        $ingredientAmounts = [];
        foreach ($products as &$product) {
            foreach ($product->ingredients as &$ingredient) {
                $ingredientAmounts[$ingredient->id] = $ingredient->current_amount;
                $requiredAmount[$ingredient->id] = (array_key_exists($ingredient->id, $requiredAmount) ? $requiredAmount[$ingredient->id] : 0)
                    +  $ingredient->pivot->amount * $productQuantity[$product->id];
            }
        }

        // Check if enough stock
        foreach ($requiredAmount as $ingredientId => $amount) {
            if ($ingredientAmounts[$ingredientId] < $amount) {
                throw new BadRequestHttpException('Not enough stock');
            }
        }
        return $requiredAmount;
    }

    /**
     * Update the stock of ingredients based on the order.
     *
     * @param array $ingredientRequiredAmount
     * @return void
     */
    public function updateStock(array $requiredIngredients): void
    {
        foreach ($requiredIngredients as $ingredientId => $requiredAmount) {
            $ingredient = $this->_ingredient->find($ingredientId);
            $ingredient->current_amount -= $requiredAmount;
            $ingredient->save();
        }
    }
}
