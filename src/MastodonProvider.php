<?php

namespace Revolution\Socialite\Mastodon;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
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
    protected $scopes = ['read', 'write'];

    /**
     * The separating character for the requested scopes.
     *
     * @var string
     */
    protected $scopeSeparator = ' ';

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state): string
    {
        $url = $this->domain().'/oauth/authorize/';

        return $this->buildAuthUrlFromBase($url, $state);
    }

    /**
     * {@inheritdoc}
     */
    protected function getCodeFields($state = null): array
    {
        $fields = parent::getCodeFields($state);

        unset($fields['client_secret']);

        return $this->updateFields($fields);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl(): string
    {
        return $this->domain().'/oauth/token';
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenFields($code): array
    {
        return $this->updateFields(parent::getTokenFields($code));
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $url = $this->domain().'/api/v1/accounts/verify_credentials';

        $response = $this->getHttpClient()->get($url, [
            'headers' => [
                'Authorization' => 'Bearer '.$token,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user): User
    {
        $url_host = parse_url($domain = $this->domain(), PHP_URL_HOST);

        return (new User)->setRaw($user)->map([
            'id' => $user['id'],
            'nickname' => $user['username'],
            'name' => $user['display_name'] ?? '',
            'email' => '',
            'avatar' => $user['avatar'] ?? '',
            'server' => $domain,
            'user_identifier' => '@'.$user['username'].'@'.$url_host,
            'acct' => $user['username'].'@'.$url_host,
        ]);
    }

    protected function domain(): string
    {
        return Str::rtrim($this->parameters['domain'] ?? Config::get('services.mastodon.domain'), '/');
    }

    protected function updateFields($fields): array
    {
        unset($fields['domain']);

        return $fields;
    }
}
