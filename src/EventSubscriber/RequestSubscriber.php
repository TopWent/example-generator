<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class RequestSubscriber implements EventSubscriberInterface
{
    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'checkRequest',
        ];
    }

    /**
     * @param RequestEvent $event
     *
     * @throws \Exception
     */
    public function checkRequest(RequestEvent $event)
    {
        $request = $event->getRequest();

        if (0 !== strpos($request->getPathInfo(), '/api')) {
            return;
        }

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'])
            && 'json' !== $request->getContentType()) {
            throw new \Exception('Content-Type is not valid.', 406);
        }
    }
}
