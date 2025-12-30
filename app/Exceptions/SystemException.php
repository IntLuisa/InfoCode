<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;
use PDOException;
use Throwable;

class SystemException extends Exception
{
    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function report(Throwable $exception)
    {
        Log::channel('error')->error($exception->getMessage());
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response
     */
    public function render(Throwable $exception)
    {
        $this->report($exception);

        if (config('app.debug')) {
            return false;
        }

        $message = match (true) {
            $exception instanceof PDOException => 'Erro ao conectar ao banco de dados.',
            default => $exception->getMessage(),
        };

        return response()->view('errors.minimal', ['error' => $exception, 'message' => $message]);
    }
}
