<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository
{

    public function __construct(
        private Product $_product
    ) {
    }

    /**
     * Get products with relations.
     *
     * @param string $column
     * @param array $Ids
     * @param array $relations (optional)
     * @return object
     */
    public function getWithRelations(string $column, array $Ids, array $relations = []): object
    {
        return $this->_product->whereIn($column, $Ids)->with($relations)->get();
    }
}
