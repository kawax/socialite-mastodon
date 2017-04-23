# Socialite for Mastodon

https://github.com/tootsuite/mastodon

## Install
```
composer require revolution/socialite-mastodon
```

### config/app.php

```
    'providers' => [
        ...
        Revolution\Socialite\Mastodon\MastodonServiceProvider::class,
    ]
```

### config/services.php

```
    'mastodon' => [
        'domain'        => env('MASTODON_DOMAIN', 'https://mastodon.social'),
        'client_id'     => env('MASTODON_ID'),
        'client_secret' => env('MASTODON_SECRET'),
        'redirect'      => env('MASTODON_REDIRECT'),
        //'read', 'write', 'follow'
        'scope'         => ['read'],
    ],
```

### .env
```
MASTODON_DOMAIN=https://mastodon.social
MASTODON_ID=
MASTODON_SECRET=
MASTODON_REDIRECT=https://example.com/callback

```

## Create App and get the client_id & client_secret

Can't create by browser?  
Can't update app info.

```
curl -F "client_name={App Name}" -F "redirect_uris={redirect_url}" -F "scopes=read" https://{domain}/api/v1/apps
```

returns json

```
{
  "id": ,
  "redirect_uri": "",
  "client_id": "",
  "client_secret": ""
}
```


## Usage
routes/web.php
```
Route::get('/', 'MastodonController@index');
Route::get('callback', 'MastodonController@callback');
```

MastodonController

```
<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Socialite;

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

### Customize domain example
```
    public function login(Request $request)
    {
        //input domain by user
        $domain = $request->input('domain');

        //get app info. domain, client_id, client_secret ...
        $server = Server::where('domain', $domain)->first();

        if (!$server) {
            //create new app
            //...

            //save app info
            $server = Server::create([
                'domain'        => $domain,
                'client_id'     => '',
                'client_secret' => '',
            ]);
        }

        //change config
        config(['services.mastodon.domain' => $domain]);
        config(['services.mastodon.client_id' => $server->client_id]);
        config(['services.mastodon.client_secret' => $server->client_secret]);

        session(['mastodon_domain' => $domain]);
        session(['mastodon_server' => $server]);

        return Socialite::driver('mastodon')->redirect();
    }
    
    public function callback()
    {
        $domain = session('mastodon_domain');
        $server = session('mastodon_server');
    
        config(['services.mastodon.domain' => $domain]);
        config(['services.mastodon.client_id' => $server->client_id]);
        config(['services.mastodon.client_secret' => $server->client_secret]);
    
        $user = Socialite::driver('mastodon')->user();
        dd($user);
    }
```


## LICENCE
MIT
