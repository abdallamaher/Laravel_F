<?php

namespace App\Models;

use App\Models\Product;
use App\Observers\IngredientObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ingredient extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'max_amount', 'current_amount', 'email_sent'];

    /**
     * Get Products associated with the Ingredient
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_ingredient', 'ingredient_id', 'product_id')
            ->withPivot('amount');
    }

    /**
     * Get the amount of stock left in the Ingredient
     * @return bool
     */
    public function isHalfOrLess(): bool
    {
        return $this->current_amount <= ($this->max_amount * 0.5);
    }

    /**
     * Set the email_sent field to true or false
     * @param $boolean
     * @return bool
     */
    public function setEmailSent($boolean)
    {
        return $this->update(['email_sent' => $boolean]);
    }
}
