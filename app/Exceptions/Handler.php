<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        HttpException::class,
        ModelNotFoundException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        return parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        if ($e instanceof ModelNotFoundException) {
            $e = new NotFoundHttpException($e->getMessage(), $e);
        } else {
            if ($request->wantsJson())
    		{
    			// Define the response
    			$response = [
    				'errors' => 'Sorry, something went wrong.',
    				'message' => $e->getMessage()
    			];

    			// If the app is in debug mode
    			if (config('app.debug'))
    			{
    				// Add the exception class name, message and stack trace to response
    				$response['exception'] = get_class($e); // Reflection might be better here
    				$response['trace'] = $e->getTrace();
    			}


    			// Default response of 400
    			$status = 400;

    			// If this exception is an instance of HttpException
    			if ($this->isHttpException($e))
    			{
    				// Grab the HTTP status code from the Exception
    				$status = $e->getStatusCode();
    			}

                \Log::error($e);

    			// Return a JSON response with the response array and status code
    			return response()->json($response, $status);
    		}
        }

        return parent::render($request, $e);
    }
}
