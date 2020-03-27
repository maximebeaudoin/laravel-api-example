<?php

namespace App\Exceptions;

use EllipseSynergie\ApiResponse\Contracts\Response;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param \Throwable $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Throwable $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        /** @var Response $response */
        $response = app(Response::class);

        switch (true) {

            // When a entity wasn't found in database
            case $exception instanceof ModelNotFoundException:
                return $response->errorNotFound($exception->getMessage());
                break;

            // Request validation error
            case $exception instanceof ValidatorException:
                return $response->setStatusCode(SymfonyResponse::HTTP_UNPROCESSABLE_ENTITY)->withError(
                    $exception->errors()->toArray(),
                    'VALIDATION-FAILED'
                );
                break;

            //Authentication failed
            case $exception instanceof AuthenticationException:
                return $response->errorUnauthorized($exception->getMessage());
                break;
        }

        return parent::render($request, $exception);
    }
}
