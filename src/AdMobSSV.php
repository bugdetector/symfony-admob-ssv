<?php

namespace Junker\AdMobSSV;

use EllipticCurve\Ecdsa;
use \Symfony\Component\HttpFoundation\Request;
use Kevinrob\GuzzleCache\CacheMiddleware;
use Kevinrob\GuzzleCache\Strategy\CacheStrategyInterface;

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
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function validate()
    {
        if (!$this->request->query->has('key_id') || !$this->request->query->has('signature'))
            throw new \InvalidArgumentException();

        $ecdsaPublicKey = PublicKey::createEcdsaPublicKeyFromRequest($this->request);
        $ecdsaSignature = Signature::createEcdsaSignatureFromRequest($this->request);

        $message = '';

        foreach($this->request->query->all() as $key => $value) {
            if ($key != 'key_id' && $key != 'signature')
                $message .= ($message == '' ? '' : '&') . "{$key}={$value}";
        }

        return Ecdsa::verify($message, $ecdsaSignature, $ecdsaPublicKey);
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
     * Set cache Strategy
     */
    protected function setCacheStrategy(CacheStrategyInterface $strategy)
    {
        PublicKey::setCacheStrategy($strategy);
    }
}
