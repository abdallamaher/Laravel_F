<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;

class InvalidOrderException extends Exception
{
    public function __construct(protected $message = 'Order not allowed')
    {
        parent::__construct($message);
    }


    /**
     * Report the exception.
     *
     * @return void
     */
    public function report()
    {
        // You can log the exception here if needed.
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        return response(['message' => $this->message], Response::HTTP_BAD_REQUEST);
    }
}
