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

    private ?string $alias = null;

    private ?bool $test = false;

    private function __construct() {}

    public static function init($coin)
    {
        $class = new static();

        $class->abbr = $coin->abbr;
        $class->alias = $coin->alias;
        switch (get_class($coin)) {
            case 'stdClass':
                $class->test = $coin->test;
                break;
            case 'Currency':
                $class->test = $coin->isTestnet();
                break;
        }

        return $class;
    }
}
