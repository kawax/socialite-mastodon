<?php

namespace Revolution\Socialite\Mastodon;

use Illuminate\Support\Facades\Config;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;

class MastodonProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * The scopes being requested.
     *
     * @var array
     */
    protected $scopes = ['read'];

    /**
     * The separating character for the requested scopes.
     *
     * @var string
     */
    protected $scopeSeparator = ' ';

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        $url = Config::get('services.mastodon.domain') . '/oauth/authorize/';

        return $this->buildAuthUrlFromBase($url, $state);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return Config::get('services.mastodon.domain') . '/oauth/token';
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenFields($code)
    {
        return parent::getTokenFields($code) + ['grant_type' => 'authorization_code'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $url = Config::get('services.mastodon.domain') . '/api/v1/accounts/verify_credentials';

        $response = $this->getHttpClient()->get($url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);
        
        $url_parsed = parse_url(Config::get('services.mastodon.domain'));
        
        $userObject = json_decode($response->getBody(), true);

        return array_merge(json_decode($response->getBody(), true), [
            'server' => Config::get('services.mastodon.domain'),
            'userIdentifier' => '@' . $userObject['username'] . '@' . $url_parsed['host']
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            'id'       => $user['id'],
            'nickname' => $user['acct'],
            'name'     => $user['display_name'] ?? '',
            'email'    => '',
            'avatar'   => $user['avatar'] ?? '',
        ]);
    }
}
