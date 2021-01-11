<?php

namespace Richardds\BigInteger;

use Exception;

class Utils
{
    /**
     * Table of already calculated factorials
     * @var BigInteger[]
     */
    private static $factorials_cache = [];

    /**
     * Calculate or retrieve cached factorial
     * @param int $factorial
     * @return BigInteger Factorial
     */
    public static function cachedFactorial(int $factorial): BigInteger
    {
        if (array_key_exists($factorial, self::$factorials_cache)) {
            return self::$factorials_cache[$factorial];
        }
        $factorial_bi = BigInteger::factorial($factorial);
        self::$factorials_cache[$factorial] = $factorial_bi;
        return $factorial_bi;
    }

    /**
     * @param BigInteger|string|int $a
     * @param BigInteger|string|int $b
     * @return BigInteger GCD of $a and $b
     */
    public static function gcd($a, $b): BigInteger
    {
        if ($a instanceof BigInteger) {
            return $a->gcd($b);
        }
        return BigInteger::from($a)->gcd($b);
    }

    /**
     * @param int $size Size in bytes
     * @return BigInteger Cryptographically secure pseudo-random number of given size
     * @throws Exception
     * @see random_bytes
     */
    public static function random(int $size): BigInteger
    {
        return BigInteger::fromBuffer(random_bytes($size), false);
    }
}
