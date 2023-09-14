<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ProductService
{

    public function __construct(
        private ProductRepository $_productRepository
    ) {
    }

    /**
     * Check if all products exist and have enough stock in ingredients in the database.
     *
     * @param array $products
     * @return array
     * @throws BadRequestHttpException
     */
    public function checkStock(array $products): array
    {
        // Map product id to quantity
        $productQuantity = [];
        foreach ($products as $product) {
            $productQuantity[$product['product_id']] = $product['quantity'];
        }

        // Lazy load product data with ingredients
        $productIds = collect($products)->pluck('product_id')->toArray();
        $products = $this->_productRepository->getWithRelations('id', $productIds, ['ingredients']);

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
}
