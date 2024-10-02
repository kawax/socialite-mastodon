<?php

namespace Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Mockery as m;
use Revolution\Socialite\Mastodon\MastodonProvider;

class SocialiteTest extends TestCase
{
    protected function tearDown(): void
    {
        m::close();

        parent::tearDown();
    }

    public function test_instance()
    {
        $provider = Socialite::driver('mastodon');

        $this->assertInstanceOf(MastodonProvider::class, $provider);
    }

    public function test_redirect()
    {
        $request = Request::create('foo');
        $request->setLaravelSession($session = m::mock('Illuminate\Contracts\Session\Session'));
        $session->shouldReceive('put')->once();

        $provider = new MastodonProvider($request, 'client_id', 'client_secret', 'redirect');
        $response = $provider->redirect();

        $this->assertStringStartsWith('http://localhost', $response->getTargetUrl());
    }

    public function test_user()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'access_token' => 'access_token',
            ])),
            new Response(200, [], json_encode([
                'id' => 'id',
                'username' => 'username',
            ])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $provider = m::mock(MastodonProvider::class)->makePartial();
        $provider->shouldAllowMockingProtectedMethods();
        $provider->shouldReceive('hasInvalidState')->andReturnFalse();
        $provider->shouldReceive('getCode')->andReturn('code');

        $user = $provider->setHttpClient($client)
            ->with(['domain' => 'http://test', 'client_id' => 'foo', 'client_secret' => 'foo'])
            ->user();

        $this->assertSame('id', $user->id);
        $this->assertSame('username', $user->nickname);
    }

    public function test_with()
    {
        $request = Request::create('foo');
        $request->setLaravelSession($session = m::mock('Illuminate\Contracts\Session\Session'));
        $session->shouldReceive('put')->once();

        $provider = new MastodonProvider($request, 'client_id', 'client_secret', 'redirect');
        $response = $provider->with(['domain' => 'http://test', 'client_id' => 'foo'])->redirect();

        $this->assertStringStartsWith('http://test', $response->getTargetUrl());
    }
}
