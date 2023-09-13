<?php

namespace App\Observers;

use App\Events\IngredientShortage as IngredientShortageEvent;
use App\Models\Ingredient;
use Illuminate\Support\Facades\Log;

class IngredientObserver
{
    /**
     * Handle the Ingredient "updated" event.
     *
     * @param  \App\Models\Ingredient  $ingredient
     * @return void
     */
    public function updated(Ingredient $ingredient)
    {
        $changedFields = $ingredient->getDirty();
        if (
            is_array($changedFields) &&
            array_key_exists('current_amount', $changedFields) &&
            $ingredient->isHalfOrLess() &&
            !$ingredient->email_sent
        ) {
            IngredientShortageEvent::dispatch($ingredient);
        }
    }
}
