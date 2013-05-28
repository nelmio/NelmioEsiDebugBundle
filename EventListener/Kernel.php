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

        // exclude render tags in the WDT and Profiler
        $controller = $request->attributes->get('_controller');
        if ($controller && 'WebProfilerBundle' === substr($controller, 0, 17)) {
             return;
        }

        // do not highlight master responses that are not HTML based
        $contentType = $response->headers->get('Content-Type');
        if (HttpKernelInterface::MASTER_REQUEST === $event->getRequestType()
            && $contentType
            && !preg_match('{^(text/html|application/xhtml+xml)\b}', $contentType)) {
            return;
        }

        // skip non-cacheable responses
        if (!$response->isCacheable()) {
            return;
        }

        $response->setContent(sprintf(
            '
                <div class="esi-debug">
                    <div class="esi-debug-details">
                       ESI: %s, Rendered: %s, Cache-Control: %s
                    </div>
                    %s
                </div>

                <style>
                    .esi-debug {
                        border: 1px solid red;
                    }

                    .esi-debug-details {
                        background: red;
                        position: absolute;
                        color: white;
                    }

                    .esi-debug:hover {
                        background: rgba(255, 0, 0, .25);
                        border-color #CC0000;
                        cursor: pointer;
                    }

                    .esi-debug:hover .esi-debug-details {
                        background: #CC0000;
                        z-index: 9999;
                    }
                </style>
            ',
            $event->getRequest()->getRequestUri(),
            date('Y-m-d H:i:s'),
            $response->headers->get('cache-control'),
            $response->getContent()
        ));
    }
}
