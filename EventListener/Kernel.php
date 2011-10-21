<?php

/*
 * This file is part of the NelmioEsiDebugBundle.
 *
 * (c) Nelmio <hello@nelm.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nelmio\EsiDebugBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class Kernel
{
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $response = $event->getResponse();

        if ('_wdt' === $event->getRequest()->attributes->get('_route')) {
            return;
        }

        if (!preg_match('{^text/html\b}', $response->headers->get('Content-Type'))) {
            return;
        }

        $cc = $response->headers->get('cache-control');
        if (!$cc) {
            return;
        }

        if ('_internal' !== $event->getRequest()->attributes->get('_route') && 'no-cache' === $cc) {
            return;
        }

        $response->setContent('<div class="esi-debug" style="border: 1px solid red !important;">'.
            '<div style="background: red !important; display: block !important; position: fixed; color: #fff !important;" class="esi-debug-details">Rendered: '.date('Y-m-d H:i:s').' Cache-Control: '.$cc.'</div>'.
            $response->getContent()
            .'</div>'
        );
    }
}
