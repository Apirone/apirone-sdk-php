<?php
/**
 * This file is part of the Apirone Invoice library.
 *
 * (c) Alex Zaytseff <alex.zaytseff@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

?>
<div id="__apn-invoice" class="invoice-wrapper">
    <div  class="invoice invoice__qr-only">
        <div  class="invoice__body">
            <div  class="invoice__info">
                <div class="qr__wrapper">
                    <div class="skeleton__box">
                    <?php if ($invoice) : ?>
                        <input type="hidden" id = "expire" value="<?php echo $invoice->countdown(); ?>">
                        <?php if ($invoice->status !== 'expired' && $invoice->status !== 'success') : ?>
                        <figure class="qr">
                            <img src="<?php echo Utils::getQrLink($currency, $invoice->address, $amount); ?>" />
                            <span class="qr__logo <?php echo str_replace('@', '-', $invoice->currency); ?>" title="<?php echo $currency->name; ?>"></span>
                        </figure>
                        <?php else : ?>
                        <div class="qr__<?php echo $invoice->status; ?>"></div>
                        <?php endif; ?>
                    <?php endif; ?>
                    </div>
                </div>
            </div>
            <div  class="status status__qr-only skeleton__box <?php echo strtolower($status->title); ?>">
                <?php if ($id !== null) : ?>
                <input id="invoice_id" type="hidden" value="<?php echo $id; ?>">
                <a id="statusUrl" href="<?php echo $statusLink; ?>" style="display: none"></a>
                <?php endif; ?>
                <p >
                    <!-- <span ><svg data-v-0bf15202=""  viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="svg icon_refresh" alt="Refresh icon"><path data-v-0bf15202="" class="refresh__path" d="M14.55 21.67C18.84 20.54 22 16.64 22 12C22 6.48 17.56 2 12 2C5.33 2 2 7.56 2 7.56M2 7.56V3M2 7.56H4.01H6.44" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path data-v-0bf15202="" class="refresh__path" d="M2 12C2 17.52 6.48 22 12 22" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="3 3"></path></svg></span> -->
                    <span class="icon-status icon-<?php echo strtolower($status->title); ?>"></span>
                    <?php $t($status->description); ?>
                </p>
            </div>
        </div>
    </div>
</div>
