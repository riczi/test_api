<?php
declare(strict_types=1);

namespace App\EventListener;

use App\Exception\ApiException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if (!preg_match('/^\/api/',$event->getRequest()->server->get('REQUEST_URI'))) {
            return;
        }

        $exception = $event->getException();

        if($exception instanceof ApiException) {
            $message = $exception->getMessage();
        } else {
            if ($_SERVER['APP_ENV'] == 'prod') {
                $message = 'API_ERROR';
            } else {
                $message = $exception->getMessage();
            }
        }

        $response = new JsonResponse(
            [
                'status' => 'error',
                'msg' => $message,
            ]
        );

        if ($exception->getCode()) {

            $response->setStatusCode($exception->getCode());

        } else if ($exception instanceof HttpExceptionInterface) {

            $response->setStatusCode($exception->getStatusCode());

        } else {

            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $event->setResponse($response);
    }
}
