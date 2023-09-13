<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Models\Ingredient;
use App\Mail\IngredientShortage as IngredientShortageEmail;
use Exception;
use Illuminate\Support\Facades\Log;

class IngredientShortage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 60;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(protected Ingredient $ingredient)
    {
        //
    }

    public function shouldSkipJob()
    {
        $this->ingredient->refresh();
        return $this->ingredient->email_sent;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->shouldSkipJob()) {
            return;
        }
        Mail::send(new IngredientShortageEmail($this->ingredient));
        $this->ingredient->setEmailSent(true);
    }

    /**
     * The job failed to process.
     *
     * @param Exception $exception
     */
    public function failed(Exception $exception): void
    {
        Log::error('Ingredient shortage email failed for ingredient ', [$this->ingredient]);
    }
}
