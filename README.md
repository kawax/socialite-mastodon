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

Work in only one domain.

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

## LICENCE
MIT
