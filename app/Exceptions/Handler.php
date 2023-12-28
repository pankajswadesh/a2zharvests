<?php

namespace App\Exceptions;

use Auth;
use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\URL;

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
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
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
        
        $url=URL::previous();
       
        if($this->isHttpException($exception))
        {
            if ( isset(Auth::user()->id) && (Auth::user()->hasRole(['admin']))) {
               ;
                switch ($exception->getStatusCode()) {
                    // not found
                    case 401:
                          return response()->view('admin.errors.401',compact('url'));
                        break;
                    // not found
                    case 403:
                        return response()->view('admin.errors.403',compact('url'));
                        break;
                    // not found
                    case 404:
//                        dd($url);
                        return response()->view('admin.errors.404',compact('url'));
                        break;
                    // internal error
                    case 500:
                        return response()->view('admin.errors.500',compact('url'));
                        break;
                    default:
                        return $this->renderHttpException($exception);
                        break;
                }
            }else{
                  
                switch ($exception->getStatusCode())
                {
                    case 401:
                        return response()->view('admin.errors.401',compact('url'));
                        break;
                    // not found
                    case 403:
                        return response()->view('admin.errors.403',compact('url'));
                        break;
                    // not found
                    case 404:
//                        dd($url);
                        return response()->view('admin.errors.404',compact('url'));
                        break;
                    // internal error
                    case 500:
                        return response()->view('admin.errors.500',compact('url'));
                        break;
                    case 405:
                        return response()->view('admin.errors.404',compact('url'));
                        break;
                    default:
                        return $this->renderHttpException($exception);
                        break;
                }
            }
        }
        else
        {
            
            return parent::render($request, $exception);
            // return redirect()->route('server_error');
        }
    }

}
