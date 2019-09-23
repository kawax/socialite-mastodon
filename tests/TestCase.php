<?php


namespace Tests;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [\Revolution\Socialite\Mastodon\MastodonServiceProvider::class];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('services.mastodon', [
            'client_id'     => 'test',
            'client_secret' => 'test',
            'redirect'      => 'https://localhost',
        ]);
    }
}
