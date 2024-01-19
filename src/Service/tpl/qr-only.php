<?php
/**
 * This file is part of the Apirone SDK.
 *
 * (c) Alex Zaytseff <alex.zaytseff@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Apirone\SDK\Service\Utils;
?>
<div id="__apn-invoice" class="invoice-wrapper">
    <div  class="invoice<?php echo $loading ? ' loading' : ''; ?> invoice__qr-only">
        <div  class="invoice__body">
            <div  class="invoice__info">
                <div class="qr__wrapper">
                    <div class="skeleton__box">
                    <?php if ($details) : ?>
                        <input type="hidden" id="statusNum" value="<?php echo $details->statusNum(); ?>">
                        <?php if ($status->title == 'Refresh') : ?>
                        <figure class="qr">
                            <img src="<?php echo Utils::renderQr($currency, $details->address, $amount); ?>" />
                            <span class="qr__logo <?php echo str_replace('@', '-', $details->currency); ?>" title="<?php echo $currency->name; ?>"></span>
                        </figure>
                        <?php else : ?>
                        <div class="qr__<?php echo strtolower($status->title); ?>"></div>
                        <?php endif; ?>
                    <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="status status__qr-only skeleton__box">
                <input id="invoice_id" type="hidden" value="<?php echo $id; ?>">
                <a id="statusUrl" href="<?php echo $statusLink; ?>" style="display: none"></a>
            </div>
            <div class="status__qr-only <?php echo strtolower($status->title); ?>">
                <p>
                    <span class="icon-status icon-<?php echo strtolower($status->title); ?>"></span>
                    <?php $t($status->description); ?>
                </p>
            </div>
        </div>
    </div>
</div>
