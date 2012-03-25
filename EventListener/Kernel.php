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

        // skip web debug toolbar
        if ('_wdt' === $event->getRequest()->attributes->get('_route')) {
            return;
        }

        // skip non-html responses
        $contentType = $response->headers->get('Content-Type');
        if ($contentType && !preg_match('{^(text/html|application/xhtml+xml)\b}', $contentType)) {
            return;
        }

        // skip non-cacheable responses
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
