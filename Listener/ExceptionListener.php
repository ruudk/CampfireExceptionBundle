<?php

/*
 * This file is part of the RuudkCampfireExceptionBundle package.
 *
 * (c) Ruud Kamphuis <ruudk@mphuis.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ruudk\CampfireExceptionBundle\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Ruudk\CampfireExceptionBundle\Campfire;

class ExceptionListener
{
    protected $campfire;

    public function __construct(Campfire $campfire)
    {
        $this->campfire = $campfire;
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        if ($exception instanceof HttpException)
            return;

        $this->campfire->notifyOnException($exception, $event->getRequest());
    }
}