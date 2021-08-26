<?php

namespace Junker\AdMobSSV;

use EllipticCurve\Ecdsa;
use Illuminate\Http\Request;
use Kevinrob\GuzzleCache\CacheMiddleware;

/**
 * Class AdMobSSV
 *
 * @package Casperlaitw\LaravelAdmobSsv
 */
class AdMobSSV
{
    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    private $request;

    /**
     * AdMob constructor.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->configureCache();
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function validate()
    {
        if (!$this->request->has('key_id') || !$this->request->has('signature'))
            throw new \InvalidArgumentException();

        $publicKey = PublicKey::createPublicKeyFromRequest($this->request);
        $signature = Signature::createFromRequest($this->request);


        $message = '';

        foreach($request->query->all() as $key => $value) {
            if ($key != 'key_id' && $key != 'signature')
                $message .= ($message = '' ?: '&') . "{$key}={$value}";
        }

        return Ecdsa::verify($message, $signature, $publicKey);
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function failed()
    {
        return !$this->validate();
    }

    /**
     * Using Laravel default cache
     */
    protected function configureCache($callback)
    {
        PublicKey::cacheThrough($callback);
    }
}
