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

class Coin extends AbstractModel
{
    private ?string $abbr = null;

    private ?string $name = null;

    private ?string $alias = null;

    private function __construct() {}

    public static function init(Currency $currency)
    {
        $coin = new static();

        $coin->abbr = $currency->abbr;
        $coin->name = $coin->name;
        $coin->abbr = $coin->abbr;

        return $coin;
    }
}
