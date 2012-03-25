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
        $request  = $event->getRequest();

        // Exclude render tags in the WDT and Profiler
        $controller = $request->attributes->get('_controller');
        if (null !== $controller && 'WebProfilerBundle' === substr($controller, 0, 17)) {
             return;
        }

        // Do not highlight master responses that are not HTML based
        $contentType = $response->headers->get('Content-Type');
        if (HttpKernelInterface::MASTER_REQUEST === $event->getRequestType()
            && $contentType
            && !preg_match('{^(text/html|application/xhtml+xml)\b}', $contentType)) {
            return;
        }

        if (!$response->isCacheable()) {
            return;
        }

        $response->setContent('<div class="esi-debug" style="border: 1px solid red !important;">'.
            '<div style="background: red !important; display: block !important; position: fixed; color: #fff !important;" class="esi-debug-details">'.
                'Rendered: '.date('Y-m-d H:i:s').' Cache-Control: '.$response->headers->get('cache-control').'</div>'.
                $response->getContent()
            .'</div>'
        );
    }
}
