<?php

namespace App\Mail;

use App\Models\Ingredient;
use Illuminate\Mail\Mailable;

class IngredientShortage extends Mailable
{
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(protected Ingredient $ingredient)
    {
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->from('abdallamaher89@gmail.com')
            ->to('merchant@email.com')
            ->subject('Warning : ' . ucWords($this->ingredient->name) . ' Reached below its half level')
            ->with([
                'name' => ucWords($this->ingredient->name),
                'max_amount' => $this->ingredient->max_amount,
                'current_amount' => $this->ingredient->current_amount
            ])
            ->view('emails.ingredient');
    }
}
