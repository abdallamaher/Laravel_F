<?php

namespace App\Models;

use App\Models\Order;
use App\Models\Ingredient;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * Get Ingredients associated with the Product
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class, 'product_ingredient', 'product_id', 'ingredient_id')
            ->withPivot('amount');
    }

    /**
     * Get Orders associated with the Product
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_product', 'product_id', 'order_id')
            ->withPivot('quantity');
    }
}
