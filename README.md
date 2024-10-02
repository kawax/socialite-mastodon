# Socialite for Mastodon

https://github.com/mastodon/mastodon

## Requirements
- PHP >= 8.0

> No version restrictions. It may stop working in future versions.

## Install
```
composer require revolution/socialite-mastodon
```

### config/services.php

```php
    'mastodon' => [
        'domain'        => env('MASTODON_DOMAIN'),
        'client_id'     => env('MASTODON_ID'),
        'client_secret' => env('MASTODON_SECRET'),
        'redirect'      => env('MASTODON_REDIRECT'),
        'scope'         => ['read', 'write'],
    ],
```

### .env
```
MASTODON_DOMAIN=https://localhost
MASTODON_ID=
MASTODON_SECRET=
MASTODON_REDIRECT=https://localhost/callback
```

## Create App and get the client_id & client_secret

1. Go to your Mastodon's user preferences page.
2. Go to development page.
3. Create new application.
4. Get `Client key` and `Client secret`

## Usage

### Use one instance
routes/web.php
```php
Route::get('/', [MastodonController::class, 'index']);
Route::get('callback', [MastodonController::class, 'callback']);
```

MastodonController

```php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class MastodonController extends Controller
{
    public function index()
    {
        return Socialite::driver('mastodon')->redirect();
    }

    public function callback()
    {
        $user = Socialite::driver('mastodon')->user();
        dd($user);
    }
}
```

Set scopes
```php
return Socialite::driver('mastodon')
           ->setScopes(config('services.mastodon.scope', ['read']))
           ->redirect();
```

https://docs.joinmastodon.org/api/oauth-scopes/

### Customize domain example

Mastodon API for Laravel
https://github.com/kawax/laravel-mastodon-api

```php
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Revolution\Mastodon\Facades\Mastodon;
use App\Models\Server;

    public function login(Request $request)
    {
        //input domain by user
        $domain = $request->input('domain');

        //get app info. domain, client_id, client_secret ...
        //Server is Eloquent Model
        $server = Server::where('domain', $domain)->first();

        if (empty($server)) {
            //create new app
            $info = Mastodon::domain($domain)->createApp(client_name: 'my-app', redirect_uris: 'https://example.com/callback', scopes: 'read write');

            //save app info
            $server = Server::create([
                'domain'        => $domain,
                'client_id'     => $info['client_id'],
                'client_secret' => $info['client_secret'],
            ]);
        }

        session(['mastodon_domain' => $domain]);

        return Socialite::driver('mastodon')->with(['domain' => $domain, 'client_id' => $server->client_id])->redirect();
    }
    
    public function callback()
    {
        $domain = session('mastodon_domain');
        $server = Server::where('domain', $domain)->first();

        $user = Socialite::driver('mastodon')->with(['domain' => $domain, 'client_id' => $server->client_id, 'client_secret' => $server->client_secret])->user();
        dd($user);
    }
```

## LICENCE
MIT
