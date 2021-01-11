# BigInteger
OOP PHP library for fast big integer manipulation made as a wrapper around GMP library.
The library is not converting the numbers into a string but uses GMP resource, which
comes with significant speed improvement compared to other libraries.
### General requirements
- `php-7.2` and higher
- `php-gmp` library

### How to install GMP on Apline Linux
```shell
RUN apk add --update --no-cache gmp
RUN apk add --update --no-cache --virtual .phpize-deps $PHPIZE_DEPS autoconf g++ make gmp-dev && \
    docker-php-ext-install gmp && \
    apk del .phpize-deps $PHPIZE_DEPS autoconf g++ make gmp-dev
```

## Examples
Majority of the methods accept `BigInteger` object, `string` or `int`.
The `BigInteger` object uses GMP resource. Therefore, there is no slow string conversion on every operation.

```php
use Richardds\BigInteger\BigInteger;
use Richardds\BigInteger\Utils as BigIntegerUtils;

$g = BigInteger::fromInt(2);
$e = BigInteger::fromString("<big_integer>");
$m = BigInteger::fromString("<big_integer>");
$c = $g->powMod($e, $m);

$p = BigInteger::fromBuffer("<buffer>");
$z = BigInteger::fromString("<big_integer>");

$x = $g->powMod($p, $m)
       ->mul($z->sub(BigInteger::one()))
       ->mod($m);
       
$a = BigInteger::fromString("<big_integer>");
$b = BigInteger::fromString("<big_integer>");
$gcd = $a->gcd($b);
$gcd = BigIntegerUtils::gcd($a, $b);
```