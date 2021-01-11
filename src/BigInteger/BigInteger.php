<?php

namespace Richardds\BigInteger;

use GMP;
use DivisionByZeroError;
use LogicException;

class BigInteger
{
    /**
     * @var GMP|resource
     */
    private static $gmp_zero = null;

    /**
     * @var GMP|resource
     */
    private static $gmp_one = null;

    /**
     * GMP resource
     * @var GMP|resource
     */
    protected $gmp = null;

    /**
     * @param string|int $value A string representation of the number
     * @param int $base If 0, the base is determined by the GMP library
     * @return BigInteger
     * @throws LogicException
     */
    public static function from($value, int $base = 10): BigInteger
    {
        if (is_int($value)) {
            return new BigInteger(gmp_init($value, $base));
        }
        if (!is_numeric($value)) {
            throw new LogicException("String value does not represent a number, \$value={$value}");
        }
        return new BigInteger(gmp_init($value, $base));
    }

    /**
     * @param string $buffer
     * @param bool $reverse
     * @return BigInteger
     */
    public static function fromBuffer(string $buffer, bool $reverse = true): BigInteger
    {
        if ($reverse) {
            $buffer = strrev($buffer);
        }
        return new BigInteger(gmp_init(bin2hex($buffer), 16));
    }

    /**
     * @return BigInteger Number which equals to 0
     */
    public static function zero(): BigInteger
    {
        if (is_null(self::$gmp_zero)) {
            self::$gmp_zero = gmp_init(0);
        }
        return new BigInteger(self::$gmp_zero);
    }

    /**
     * @return BigInteger Number which equals to 1
     */
    public static function one(): BigInteger
    {
        if (is_null(self::$gmp_one)) {
            self::$gmp_one = gmp_init(1);
        }
        return new BigInteger(self::$gmp_one);
    }

    /**
     * Calculate factorial
     * @param int $factorial
     * @return BigInteger Factorial
     */
    public static function factorial(int $factorial): BigInteger
    {
        return new BigInteger(gmp_fact($factorial));
    }

    /**
     * BigInteger constructor.
     * @param GMP|resource $gmp_number GMP resource
     */
    private function __construct(GMP $gmp_number)
    {
        $this->gmp = $gmp_number;
    }

    /**
     * @return int Integer representation of the number
     */
    public function toInt(): int
    {
        if ($this->greaterThan(PHP_INT_MAX)) {
            throw new LogicException("The number is greater than PHP_INT_MAX");
        }
        return gmp_intval($this->gmp);
    }

    /**
     * @return string String representation of the number
     */
    public function toString(): string
    {
        return gmp_strval($this->gmp);
    }

    /**
     * @param bool $reverse
     * @return string Buffer representing the number value
     */
    public function toBuffer(bool $reverse = true): string
    {
        $bytes_hex = gmp_strval($this->gmp, 16);

        if (strlen($bytes_hex) & 1) {
            $bytes_hex = '0' . $bytes_hex;
        }

        $buffer = pack('H*', $bytes_hex);
        $buffer = ltrim($buffer, chr(0));

        if ($reverse) {
            return strrev($buffer);
        }

        return $buffer;
    }

    /**
     * @param BigInteger|int|string $rhs
     * @return int A positive value if $this &gt; $rhs, zero if $this = $rhs and a negative value if $this &lt; $rhs
     */
    public function compare($rhs): int
    {
        return gmp_cmp($this->gmp, $rhs instanceof BigInteger ? $rhs->gmp : $rhs);
    }

    /**
     * @param BigInteger|int|string $rhs
     * @return bool True if the number is less than $rhs
     */
    public function lessThan($rhs): bool
    {
        return $this->compare($rhs) < 0;
    }

    /**
     * @param BigInteger|int|string $rhs
     * @return bool True if the number is equal or less than $rhs
     */
    public function lessThanEqual($rhs): bool
    {
        return $this->compare($rhs) <= 0;
    }

    /**
     * @param BigInteger|int|string $rhs
     * @return bool True if the number is equal to $rhs
     */
    public function equal($rhs): bool
    {
        return $this->compare($rhs) == 0;
    }

    /**
     * @param BigInteger|int|string $rhs
     * @return bool True if the number is greater than $rhs
     */
    public function greaterThan($rhs): bool
    {
        return $this->compare($rhs) > 0;
    }

    /**
     * @param BigInteger|int|string $rhs
     * @return bool True if the number is equal or greater than $rhs
     */
    public function greaterThanEqual($rhs): bool
    {
        return $this->compare($rhs) >= 0;
    }

    /**
     * @param BigInteger|int|string $left
     * @param BigInteger|int|string $right
     * @param bool $exclusive If True the interval is exclusive ($left, $right)
     * @return bool True if the number is between the interval [$left, $right]
     */
    public function between($left, $right, bool $exclusive = false): bool
    {
        if ($exclusive) {
            return !($this->lessThanEqual($left) || $this->greaterThanEqual($right));
        }
        return !($this->lessThan($left) || $this->greaterThan($right));
    }

    /**
     * @param BigInteger|int|string $rhs
     * @return BigInteger Addition result
     */
    public function add($rhs): BigInteger
    {
        return new BigInteger(gmp_add($this->gmp, $rhs instanceof BigInteger ? $rhs->gmp : $rhs));
    }

    /**
     * @param BigInteger|int|string $rhs
     * @return BigInteger Subtraction result
     */
    public function sub($rhs): BigInteger
    {
        return new BigInteger(gmp_sub($this->gmp, $rhs instanceof BigInteger ? $rhs->gmp : $rhs));
    }

    /**
     * @param BigInteger|int|string $rhs
     * @return BigInteger Multiple
     */
    public function mul($rhs): BigInteger
    {
        return new BigInteger(gmp_mul($this->gmp, $rhs instanceof BigInteger ? $rhs->gmp : $rhs));
    }

    /**
     * @param BigInteger|int|string $rhs
     * @param int $round
     * @return BigInteger The proportion to $rhs
     */
    public function div($rhs, int $round = 0): BigInteger
    {
        if ($rhs->equal(self::zero())) {
            throw new DivisionByZeroError("Division by zero");
        }
        return new BigInteger(gmp_div($this->gmp, $rhs instanceof BigInteger ? $rhs->gmp : $rhs, $round));
    }

    /**
     * @param BigInteger|int|string $rhs
     * @param int $round
     * @return BigInteger The quotient after division by $rhs
     */
    public function div_q($rhs, int $round = 0): BigInteger
    {
        if ($rhs->equal(self::zero())) {
            throw new DivisionByZeroError("Division by zero");
        }
        return new BigInteger(gmp_div_q($this->gmp, $rhs instanceof BigInteger ? $rhs->gmp : $rhs, $round));
    }

    /**
     * @param BigInteger|int|string $rhs
     * @param int $round
     * @return BigInteger The remainder after division by $rhs
     */
    public function div_r($rhs, int $round = 0): BigInteger
    {
        if ($rhs->equal(self::zero())) {
            throw new DivisionByZeroError("Division by zero");
        }
        return new BigInteger(gmp_div_q($this->gmp, $rhs instanceof BigInteger ? $rhs->gmp : $rhs, $round));
    }

    /**
     * @param BigInteger|int|string $rhs
     * @return BigInteger The remainder after division by $rhs
     */
    public function mod($rhs): BigInteger
    {
        return new BigInteger(gmp_mod($this->gmp, $rhs instanceof BigInteger ? $rhs->gmp : $rhs));
    }

    /**
     * @param BigInteger|int|string $exponent
     * @return BigInteger Power to exponent
     */
    public function pow($exponent): BigInteger
    {
        return new BigInteger(gmp_pow($this->gmp, $exponent instanceof BigInteger ? $exponent->gmp : $exponent));
    }

    /**
     * @param BigInteger|int|string $exponent
     * @param BigInteger|int|string $modulus
     * @return BigInteger Power to exponent in modulo
     */
    public function powMod($exponent, $modulus): BigInteger
    {
        return new BigInteger(gmp_powm(
            $this->gmp,
            $exponent instanceof BigInteger ? $exponent->gmp : $exponent,
                $modulus instanceof BigInteger ? $modulus->gmp : $modulus
            )
        );
    }

    /**
     * @return BigInteger Square root of the number
     */
    public function sqrt(): BigInteger
    {
        return new BigInteger(gmp_sqrt($this->gmp));
    }

    /**
     * @return BigInteger Absolute value of the number
     */
    public function abs(): BigInteger
    {
        return new BigInteger(gmp_abs($this->gmp));
    }

    /**
     * @return BigInteger Negated value of the number
     */
    public function negate(): BigInteger
    {
        return new BigInteger(gmp_neg($this->gmp));
    }

    public function gcd($rhs): BigInteger
    {
        return new BigInteger(gmp_gcd($this->gmp, $rhs));
    }

}
