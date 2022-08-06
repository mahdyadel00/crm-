<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler {
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
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception) {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    /**
     * Render an exception into an HTTP response.
     * [NEXTLOOP] modified render method
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception) {

        //generic response
        $response = __('lang.error_request_could_not_be_completed');
        

        /**
         * [SERVER, SYNTAX, LARAVEL EXCEPTIONS]
         * Handle 500 server errors & other critical laravel errors
         * */
        if ($exception instanceof \Symfony\Component\Debug\Exception\FatalErrorException
            || $exception instanceof \Symfony\Component\Debug\Exception\FatalThrowableError
            || $exception instanceof ReflectionException
            || $exception instanceof ModelNotFoundException
            || $exception instanceof ErrorException
        ) {

            if (app()->environment() == 'production') {
                if ($request->ajax()) {
                    $jsondata['notification'] = array('type' => 'error', 'value' => __('lang.application_error'));
                    return response()->json($jsondata, 500);
                } else {
                    return response()->view('errors.500');
                }
            }
            return parent::render($request, $exception);
        }

        /**
         * [DEVELOPER GENERATED HTTP EXCEPTIONS]
         * Handle ajax error messages by displaying a friendly popup notice
         * Server errorswill be handled as per check above
         */
        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {

            //permission denied errors
            switch ($exception->getStatusCode()) {

            //permission denied
            case 403:
                $response = ($exception->getMessage() != '') ? $exception->getMessage() : __('lang.error_no_permission_for_resource');
                $view = 'errors.403';
                break;

            //larevel session timeout
            case 419:
                $response = ($exception->getMessage() != '') ? $exception->getMessage() : __('lang.error_session_timeout');
                $view = 'errors.419';
                break;

            //not found
            case 404:
                $response = ($exception->getMessage() != '') ? $exception->getMessage() : __('lang.error_not_found');
                $view = 'errors.404';
                break;

            //business logic/generic errors
            case 409:
                $response = ($exception->getMessage() != '') ? $exception->getMessage() : __('lang.error_request_could_not_be_completed');
                $view = 'errors.409';
                break;

            default:
                $response = __('lang.error_request_could_not_be_completed');
                $view = 'errors.409';
                break;
            }

            //AJAX RESPONSE
            if ($request->ajax()) {

                //ajax reponse for a notice popup
                $jsondata['notification'] = [
                    'type' => 'error',
                    'value' => $response,
                ];

                //return response - with error code
                return response()->json($jsondata, $exception->getStatusCode());

            }

            //HTTP REPONSE
            $error = array('message' => $response);
            return response()->view($view, compact('error'));
        }

        //[DEVELOPMENT] - debug output
        return parent::render($request, $exception);
    }
}
