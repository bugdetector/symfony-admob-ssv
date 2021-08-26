# Symfony Google AdMob Server-side Verification
The library help you to verify Admob callback in server.

## Install
```
composer require junker/symfony-admob-ssv
```


## How to use

```php
use Junker\AdMobSSV\AdMobSSV;
use Symfony\Component\HttpFoundation\Request;

public function callback(Request $request) {
    $ssv = new AdMobSSV($request);
    if ($ssv->validate()) {
        // success
    } else {
        // failed
    }
}
```
