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
     * @return \Junker\AdMobSSV\Signature
     */
    public static function createFromRequest(Request $request)
    {
        return self::create($request->query->get('signature'));
    }

    /**
     * @param $signature
     *
     * @return \Junker\AdMobSSV\Signature
     */
    public static function create($signature)
    {
        return \Junker\AdMobSSV\Signature::fromBase64(str_replace(['-', '_'], ['+', '/'], $signature));
    }
}
