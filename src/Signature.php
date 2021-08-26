<?php

namespace Junker\AdMobSSV;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class Signature
 *
 * @package \Junker\AdMobSSV\Signature
 */
class Signature
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \EllipticCurve\Signature
     */
    public static function createEcdsaSignatureFromRequest(Request $request)
    {
        return self::createEcdsaSignature($request->query->get('signature'));
    }

    /**
     * @param $signature
     *
     * @return \EllipticCurve\AdMobSSV\Signature
     */
    public static function createEcdsaSignature(string $signature)
    {
        return \EllipticCurve\Signature::fromBase64(str_replace(['-', '_'], ['+', '/'], $signature));
    }
}
