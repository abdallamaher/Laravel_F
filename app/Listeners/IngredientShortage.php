<?php

namespace App\Listeners;

use App\Jobs\IngredientShortage as IngredientShortageJob;
use App\Models\Ingredient;
use Illuminate\Support\Facades\Log;

class IngredientShortage
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(protected Ingredient $ingredient)
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        IngredientShortageJob::dispatch($event->ingredient);
    }
}
