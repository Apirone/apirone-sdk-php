<?php

/**
 * This file is part of the Apirone SDK.
 *
 * (c) Alex Zaytseff <alex.zaytseff@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Apirone\SDK\Model\Settings;

use Apirone\SDK\Model\AbstractModel;
use Apirone\SDK\Model\Settings\Currency;

class Network extends AbstractModel{

    private Currency $currency;

    private array $tokens = [];

    public function __get($name)
    {
        if (in_array($name, ['currency', 'tokens'])) {
            return $this->{$name};
        }

        return $this->currency->{$name};
    }

    public function __call($name, $value)
    {
        return call_user_func_array(array($this->currency, $name), $value);
    }

    private function __construct(Currency &$currency)
    {
        $this->currency = $currency;
    }

    /**
     * Create a network instance

     * @param mixed $json
     * @return $this
     * @return static
     */

    public static function init(Currency &$currency)
    {
        $class = new static($currency);

        return $class;
    }

    public function token(Currency &$currency)
    {
        $this->tokens[$currency->abbr] = $currency;
    }

}
