<?php

namespace Revolution\Socialite\Mastodon;

use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Facades\Socialite;

class MastodonServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the service provider.
     *
     * @return void
     */
    public function boot()
    {
        Socialite::extend('mastodon', function ($app) {
            $config = $app['config']['services.mastodon'];

            return Socialite::buildProvider(MastodonProvider::class, $config);
        });
    }
}
