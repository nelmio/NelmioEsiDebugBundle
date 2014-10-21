# NelmioEsiDebugBundle

## About

The NelmioEsiDebugBundle shows you caching information around ESI requests for debugging
purposes.

## Features

* Visual wrapping of ESI requests
* A link in the Web Debug Toolbar to hide the wrapper divs

## Screenshot

<img src="https://raw.github.com/nelmio/NelmioEsiDebugBundle/master/Resources/doc/img.png" />

## Configuration

If you want to disable the wrapper divs without disabling the bundle, you can do it via the configuration:

    nelmio_esi_debug:
        enabled: false

## Installation (Symfony 2.1+)

Require the `nelmio/esi-debug-bundle` package in your composer.json and update your dependencies.

    $ composer require nelmio/esi-debug-bundle

Add the NelmioEsiDebugBundle to your application's kernel:

    public function registerBundles()
    {
        $bundles = array(
            ...
            new Nelmio\EsiDebugBundle\NelmioEsiDebugBundle(),
            ...
        );
        ...
    }

## License

Released under the MIT License, see LICENSE.
