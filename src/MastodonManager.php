<?php

namespace Revolution\Socialite\Mastodon;

use Laravel\Socialite\SocialiteManager;

class MastodonManager extends SocialiteManager
{
    protected function createMastodonDriver()
    {
        $config = $this->app['config']['services.mastodon'];

        return $this->buildProvider(MastodonProvider::class, $config);
    }
}
