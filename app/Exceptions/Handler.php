<?php

namespace App\Exceptions;

use App\Http\Responses\Response;
use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

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
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if($exception instanceof UserEmailAlreadyExistsException)
        {
            return Response::badRequest($exception->getMessage());
            // return response()->json([
            //     'success' => false,
            //     'error' => $exception->getMessage()
            // ], 
            // 400,
            // ['Content-type' => 'application/json;charset=UTF-8' , 'Charset' => 'utf-8'], JSON_UNESCAPED_UNICODE
            // );
        }
        return parent::render($request, $exception);
    }
}
