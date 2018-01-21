<?php
namespace Qrawler\ServiceProvider;

use FileFetcher\SimpleFileFetcher;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class FileFetcherServiceProvider implements ServiceProviderInterface
{

    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $app A container instance
     */
    public function register(Container $app)
    {
        $app['file_fetcher.class'] = SimpleFileFetcher::class;
        $app['file_fetcher'] = function () use ($app) {
            return new $app['file_fetcher.class'];
        };
    }
}
