<?php

namespace Junker\AdMobSSV;

use Eastwest\Json\Json;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Symfony\Component\HttpFoundation\Request;
use Kevinrob\GuzzleCache\CacheMiddleware;
use Kevinrob\GuzzleCache\Strategy\CacheStrategyInterface;
use InvalidArgumentException;

/**
 * Class PublicKey
 *
 * @package Junker\AdMobSSV
 */
class PublicKey
{
    /**
     * @var string
     */
    const PUBLIC_KEY_URL = 'https://www.gstatic.com/admob/reward/verifier-keys.json';
    /**
     * @var
     */
    private $keyId;

    /**
     * @var array
     */
    private $keyMap = [];

    /**
     * @var
     */
    public static $cacheStrategy;

    /**
     * PublicKey constructor.
     *
     * @param null $id
     */
    public function __construct($id = null)
    {
        if ($id) {
            $this->setKeyId($id);
        }
        $this->fetchPublicKeys();
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setKeyId($id)
    {
        $this->keyId = (int)$id;

        return $this;
    }

    /**
     *
     */
    public function fetchPublicKeys()
    {
        $client = new Client($this->buildOptions());
        $response = $client->request('GET', self::PUBLIC_KEY_URL);

        return $this->keyMap = Json::decode($response->getBody()->getContents(), true);
    }

    /**
     * @param Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Junker\AdMobSSV\PublicKey
     * @throws \Exception
     */
    public static function createEcdsaPublicKeyFromRequest(Request $request)
    {
        return self::createEcdsaPublicKey($request->query->get('key_id'));
    }

    /**
     * @param $keyId
     *
     * @return \EllipticCurve\PublicKey
     * @throws \Exception
     */
    public static function createEcdsaPublicKey($keyId)
    {
        $key = new self($keyId);
        return $key->getKey();
    }

    /**
     * @return \EllipticCurve\PublicKey
     * @throws \Exception
     */
    public function getKey()
    {
        if (!$this->keyId) {
            throw new InvalidArgumentException('Missing key id');
        }

        $keymaps = $this->keyMap['keys'] ?? [];

        foreach($keymaps as $keymap) {
            if ($keymap['keyId'] == $this->keyId)
                return \EllipticCurve\PublicKey::fromPem($keymap['pem']);
        }

        throw new Exception('Missing public key');
    }

    /**
     * Register a callback that is for caching the response
     *
     * @param callable $callback
     */
    public static function setCacheStrategy(CacheStrategyInterface $strategy)
    {
        static::$cacheStrategy = $strategy;
    }

    /**
     * @return ?HandlerStack
     */
    protected function buildCacheMiddlewareStack()
    {
        if (static::$cacheStrategy) {
            $stack = HandlerStack::create();
            $stack->push(new CacheMiddleware(static::$cacheStrategy), 'cache');

            return $stack;
        }

        return null;
    }

    /**
     * Build guzzle client options
     * @return array
     */
    private function buildOptions()
    {
        $options = [];
        if ($handler = $this->buildCacheMiddlewareStack()) {
            $options['handler'] = $handler;
        }

        return $options;
    }
}
