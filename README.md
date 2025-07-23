# Symfony Google AdMob Server-side Verification
The library help you to verify Admob callback in server.

## Install
```
composer require bugdetector/symfony-admob-ssv
```


## How to use

```php
use Bugdetector\AdMobSSV\AdMobSSV;
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

### example with public key cache
```php
use Bugdetector\AdMobSSV\AdMobSSV;
use Symfony\Component\HttpFoundation\Request;
use Kevinrob\GuzzleCache\Strategy\PrivateCacheStrategy;
use Kevinrob\GuzzleCache\Storage\DoctrineCacheStorage;
use Doctrine\Common\Cache\FilesystemCache;

public function callback(Request $request) {
    $ssv = new AdMobSSV($request);

    $ssv->setCacheStrategy(
        new PrivateCacheStrategy(
            new DoctrineCacheStorage(
                new FilesystemCache('/tmp/')
            )
        )
    );

    if ($ssv->validate()) {
        // success
    } else {
        // failed
    }
}
```
