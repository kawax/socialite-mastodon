<?php

namespace Revolution\Socialite\Mastodon;

use Laravel\Socialite\SocialiteServiceProvider;
use Laravel\Socialite\Contracts\Factory;

use Socialite;

class MastodonServiceProvider extends SocialiteServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the service provider.
     *
     * @return void
     */
    public function boot()
    {
        Socialite::extend('mastodon', function ($app) {
            $config = $this->app['config']['services.mastodon'];

            return Socialite::buildProvider(MastodonProvider::class, $config);
        });
    }
}
