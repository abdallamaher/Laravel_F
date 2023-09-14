<?php

namespace App\Services;

use App\Repositories\IngredientRepository;

class IngredientService
{

    public function __construct(
        private IngredientRepository $_ingredientRepository
    ) {
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
            $this->_ingredientRepository->findAndUpdate($ingredientId, 'current_amount', $requiredAmount);
        }
    }
}
