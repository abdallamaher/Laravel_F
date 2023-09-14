<?php

namespace App\Repositories;

use App\Models\Ingredient;


class IngredientRepository
{
    public function __construct(
        private Ingredient $_ingredient
    ) {
    }

    /**
     * Update column of ingredients based on the value.
     *
     * @param int $id
     * @param string $column
     * @param int $value
     * @return bool
     */
    public function findAndUpdate($id, $column, $value): bool
    {
        $this->_ingredient = $this->_ingredient->find($id);
        $this->_ingredient[$column] -= $value;
        return $this->_ingredient->save();
    }
}
