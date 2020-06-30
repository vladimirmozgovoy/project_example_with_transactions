<?php

namespace App\Exceptions;

use App\Helpers\Errors\CustomErrorHandler;
use App\Helpers\Errors\CustomErrorLogger;
use App\Service\v1\Logs\LogsService;
use Exception;
use http\Env\Request;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
     * @param  \Exception $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        // This will replace our 404 response with
        // a JSON response.
        if ($request->wantsJson() || $exception->getMessage() === 'user_not_verified') {

            if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
//                dd($exception->getTrace());
            }

            return $this->handleApiException($request, $exception);
        }

        return parent::render($request, $exception);
    }


    private function handleApiException($request, Exception $exception)
    {
        $exception = $this->prepareException($exception);

        if ($exception instanceof \Illuminate\Http\Exception\HttpResponseException) {
            $exception = $exception->getResponse();
        }

        if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
            $exception = $this->unauthenticated($request, $exception);
        }

        if ($exception instanceof \Illuminate\Validation\ValidationException) {
            $exception = $this->convertValidationExceptionToResponse($exception, $request);
        }

        if ($exception instanceof Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException) {
            $exception = $exception->getResponse();
        }

        return $this->customApiResponse($request,$exception);
    }
    private function customApiResponse($request,$exception)
    {

        if (method_exists($exception, 'getStatusCode')) {
            $statusCode = $exception->getStatusCode();
        } else if (method_exists($exception, 'getHttpStatusCode')) {
            $statusCode = $exception->getHttpStatusCode();
        } else {
            $statusCode = 500;
        }

        $response = [];
        switch ($statusCode) {
            case 401:
                $response['message'] = 'Unauthorized';
                $response['error'] = 'token_invalid';
                $error_type = null;
                if(method_exists($exception, 'getErrorType')){
                    $error_type = $exception->getErrorType();
                }
                if($error_type == 'invalid_credentials'){
                   $log_service = new LogsService();
                   $client_info_json = $log_service->prepareRequest($request);
                   $log_service->createLog($client_info_json,'ERROR AUTH',false,$response);
                }
                $error = 'token_invalid';
                if(method_exists($exception, 'getMessage')){
                    $error = $exception->getMessage();
                }
                if(method_exists($exception, 'getPayload')){
                    $error = $exception->getPayload();
                }

                $error_type = null;
                if(method_exists($exception, 'getErrorType')){
                    $error_type = $exception->getErrorType();
                }
                if($error_type == 'invalid_credentials'){
                    $error = $error_type;
                }

                CustomErrorLogger::Log('401', ['error' => $error, 'data' => \request()->all()]);
                break;
            case 403:
                $response['message'] = 'Forbidden';
                $response['error'] = 'permission_denied';

                $error = 'permission_denied';
                if(method_exists($exception, 'getMessage')){
                    $error = $exception->getMessage();
                }
                if(method_exists($exception, 'getPayload')){
                    $error = $exception->getPayload();
                }
                CustomErrorLogger::Log('403', ['error' => $error]);
                break;
            case 404:
                $response['message'] = 'Not Found';
                $response['error'] = 'url_not_found';

                $error = 'url_not_found';
                if(method_exists($exception, 'getMessage')){
                    $error = $exception->getMessage();
                }
                if(method_exists($exception, 'getPayload')){
                    $error = $exception->getPayload();
                }
                CustomErrorLogger::Log('404', ['error' => $error]);
                break;
            case 405:
                $response['message'] = 'Method Not Allowed';
                $response['error'] = 'method_not_allowed';
                break;
            case 422:
                $response['message'] = $exception->original['message'];
                $response['errors'] = $exception->original['errors'];
                break;
            case 423:
                $response['message'] = 'User not verified';
                $response['error'] = 'user_not_verified';

                $error = 'user_not_verified';
                if(method_exists($exception, 'getMessage')){
                    $error = $exception->getMessage();
                }
                CustomErrorLogger::Log('423', ['error' => $error, 'data' => \request()->all()]);
                break;
            default:
                //$response['message'] = ($statusCode == 500) ? 'Whoops, looks like something went wrong.' : $exception->getMessage();
                $response['error'] = 'server_error';

                $error = 'server_error';
                if(method_exists($exception, 'getMessage')){
                    $error = $exception->getMessage();
                }
                CustomErrorLogger::Log('500', ['error' => $error, 'data' => \request()->all()]);
                break;
        }

        if (config('app.debug')) {

            $message = [];
            if(method_exists($exception, 'getMessage')){
                $message = $exception->getMessage();
            }
            if(method_exists($exception, 'getPayload')){
                $message = $exception->getPayload();
            }
            $response['message'] = $message;

            $trace = [];
            if(method_exists($exception, 'getTrace')){
                $trace = $exception->getTrace();
            }
            $response['trace'] = $trace;
            $response['code'] = $statusCode;
        }

        $response['status'] = $statusCode;
        return response()->json($response, $statusCode);
    }
}
