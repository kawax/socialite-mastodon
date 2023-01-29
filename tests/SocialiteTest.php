<?php

namespace Tests;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Laravel\Socialite\Facades\Socialite;
use Mockery as m;
use Revolution\Socialite\Mastodon\MastodonProvider;

class SocialiteTest extends TestCase
{
    public function testInstance()
    {
        $provider = Socialite::driver('mastodon');

        $this->assertInstanceOf(MastodonProvider::class, $provider);
    }

    public function testRedirect()
    {
        $request = Request::create('foo');
        $request->setLaravelSession($session = m::mock('Illuminate\Contracts\Session\Session'));
        $session->shouldReceive('put')->once();

        Config::shouldReceive('get')->once()->with('services.mastodon.domain')->andReturn('http://localhost');

        $provider = new MastodonProvider($request, 'client_id', 'client_secret', 'redirect');
        $response = $provider->redirect();

        $this->assertStringStartsWith('http://localhost', $response->getTargetUrl());
    }
}
