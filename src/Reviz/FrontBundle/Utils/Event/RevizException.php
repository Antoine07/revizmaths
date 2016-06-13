<?php
namespace Reviz\FrontBundle\Utils\Event;

use Doctrine\ORM\ORMException;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class RevizException
{
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        // You get the exception object from the received event
        $exception = $event->getException();

        if ($exception instanceof ORMException) {
            $message = sprintf(
                'Doctrine exception: %s',
                $exception->getMessage(),
                $exception->getCode()
            );

            $response = new Response();
            $response->setContent($message);
            $response->setStatusCode($exception->getStatusCode());
            $response->headers->replace($exception->getHeaders());
            $event->setResponse($response);
        }


    }
}