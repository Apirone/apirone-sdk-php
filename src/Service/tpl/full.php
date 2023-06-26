
<?php
/**
 * This file is part of the Apirone Invoice library.
 *
 * (c) Alex Zaytseff <alex.zaytseff@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Apirone\Invoice\Service\Utils;

?>

<div id="__apn-invoice" class="invoice-wrapper">
    <div class="invoice<?php echo $loading ? ' loading' : ''; echo $status->title == 'Expired' ? ' invoice-expired' : ''; ?>">
        <div class="invoice__body">
            <div class="invoice__info">
                <div class="qr__wrapper">
                    <div class="skeleton__box">
                    <?php if ($invoice) : ?>
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
                <div class="info">
                <?php if ($loading) : /* Loadig (Skeleton) */?>
                    <h1><?php $t("title"); ?></h1>
                    <p class="skeleton__box info__date"></p>
                    <p class="skeleton__box info__amount"></p>
                <?php else : /* Invice data */?>
                    <?php if($userData) : /* userData */ ?>
                    <div>
                        <?php if($userData->title) : ?>
                        <h1><?php echo $userData->title ?></h1>
                        <?php else : ?>
                        <h1><?php $t("title"); ?> <small><?php echo $invoice->invoice; ?></small></h1>
                        <?php endif; ?>
                        <?php if($userData->merchant) : ?>
                        <p class="merchant">
                            <?php $t("from"); ?>
                            <?php if($userData->url) : ?>
                            <a href="<?php echo $userData->url; ?>" target="_blank" rel="noopener noreferrer" class="link hovered"><?php echo $userData->merchant; ?></a>
                            <?php else : ?>
                            <span><?php echo $userData->merchant; ?></span>
                            <?php endif ?>
                        </p>
                        <?php endif ?>
                    <?php else : /* No userData */ ?>
                    <h1>
                        <?php $t("title"); ?> <small><?php echo $invoice->invoice; ?></small>
                    </h1>
                    <?php endif; /* UserData end */ ?>
                    <p class="skeleton__box info__date"><span><?php $d($invoice->created); ?></span></p>
                    <p class="skeleton__box info__amount">
                    <?php if ($invoice->status == 'partpaid') : ?>
                        <small><?php $t("remainsToPay"); ?> <br></small>
                    <?php endif; ?>
                    <?php if($invoice && $amount && $invoice->status !== 'expired') : ?>
                        <?php $c($amount, 'margin-right: .5rem;'); ?>
                        <span><?php echo $amount . ' ' . strtoupper($invoice->currency); ?></span>
                    <?php endif; ?>
                    </p>
                    </div>
                <?php endif; /* Loading/Invoce end */ ?>
                </div>
                <input id="invoice_id" type="hidden" value="<?php echo $id; ?>">
                <a id="statusUrl" href="<?php echo $statusLink; ?>" style="display: none"></a>
            </div>
            <div>
                <?php if( $status->title == 'Refresh' ) : ?>
                <div class="address">
                    <div class="address__title"><?php $t("paymentAddress"); ?></div>
                    <p class="skeleton__box">
                        <?php if (!$loading && $invoice) : ?>
                        <span><?php echo $invoice->address; ?></span>
                        <?php endif; ?>
                    </p>
                    <?php if (!$loading && $status->title !== 'Expired') : ?>
                        <?php $c($invoice->address); ?>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <?php if(!$loading && $userData) : ?>
                <?php
                    $items = $userData->getItems();
                    $extras = $userData->getExtras();
                    $itemsCount = count($items);
                ?>
                <div class="invoice-table">
                    <table>
                        <?php if($items) : ?>
                        <thead>
                        <tr>
                            <th>#</th>
                            <th><?php $t('item'); ?></th>
                            <th><?php $t('cost'); ?></th>
                            <th><?php $t('qty'); ?></th>
                            <th><?php $t('total'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($items as $key => $item) : ?>
                        <tr>
                            <td><?php echo $key + 1; ?></td>
                            <td><?php echo $item->name ?? $t('Item'); ?></td>
                            <td><?php echo $item->cost ?? ''; ?></td>
                            <td><?php echo $item->qty ?? ''; ?></td>
                            <td><?php echo $item->total ?? ''; ?></td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                        <?php endif; ?>
                    </table>
                    <div class="invoice-table__bottom">
                    <?php if ($userData->subPrice) : ?>
                    <div>
                        <strong><?php $t('subtotal') ?>:</strong>
                        <span><?php echo $userData->subPrice ?></span>
                    </div>
                <?php endif; ?>
                <?php foreach($extras as $item) : ?>
                    <div>
                        <strong><?php echo $item->name ?? '' ;?></strong>
                        <span><?php echo $item->price ?? '' ;?></span>
                    </div>
                <?php endforeach; ?>
                    <?php if ($userData->subPrice) : ?>
                    <div>
                        <strong><?php $t('total') ?>:</strong>
                        <span>
                        <strong><?php echo $userData->subPrice ?? '' ;?></strong>
                        </span>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                <div class="status-data__wrapper">
                    <div class="status-data">
                        <?php $class_skeleton = $status->title == "Loading" ? ' skeleton__box' : ''; ?>
                        <div class="status<?php echo $class_skeleton; ?> <?php echo strtolower($status->title); ?>">
                            <div class="status__icon">
                                <?php if ($status->title !== 'Loading') : ?>
                                <span class="icon-status icon-<?php echo strtolower($status->title); ?>"></span>
                                <?php endif; ?>
                            </div>
                            <div class="status__text">
                                <p><?php $t($status->description); ?></p>
                                <?php if($invoice && $invoice->countdown() > 0) : ?>
                                <input type="hidden" id = "expire" value="<?php echo $invoice->countdown(); ?>">
                                <p id="countdown" class="countdown"></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if ($invoice && $invoice->history) : ?>
                        <div class="history">
                            <input type="hidden" id="statusNum" value="<?php echo $invoice->statusNum(); ?>">
                            <ul>
                                <?php foreach ($invoice->history as $item) : ?>
                                <li>
                                    <?php if ($item->getAmount()) : ?>
                                    <a class="history__item" href="<?php echo Utils::getTransactionLink($currency, $item->getTxid()); ?>" target="_blank">
                                        <span><?php $d($item->getDate()); ?> <span>(<?php echo Utils::exp2dec($item->getAmount() * $currency->getUnitsFactor() ) ?>)</span></span>
                                        <span style="text-align: right;"><?php $t($item->getStatus()); ?></span>
                                    </a>
                                    <?php else : ?>
                                    <span class="history__item">
                                        <span><?php $d($item->getDate()); ?></span>
                                        <span style="text-align: right;"><?php $t($item->getStatus()); ?></span>
                                    </span>
                                    <?php endif; ?>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <button class="btn hovered toggler"><span class="btn__icon"></span></button>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if($invoice && @$invoice->showLinkback()) : ?> 
                <p class="countdown" style="text-align: center;">
                    <?php echo sprintf($t('getBack', false), 15, $invoice->linkback); ?>
                </p>
                <?php endif; ?>
                
                <?php if ($backlink || $logo) : ?>
                <div class="invoice__footer">
                    <?php if ($backlink) : ?>
                    <p>
                        <?php echo sprintf($t('backlink', false), $backlink); ?>
                    </p>
                    <?php endif; ?>
                    <?php if (self::$logo) : ?>
                    <p> Powered by <a href="https://apirone.com/" title="Apirone" class="link hovered">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 578.77 112.4" class="logo" alt="Apirone logo">
                                <g>
                                    <path class="logo__part" d="M110.2,0,68.8,52.3H0L41.4,0ZM90,10H46.3L20,43H63.7Z"/>
                                    <path class="logo__part" d="M68.8,60l41.4,52.4H41.4L0,60ZM63.7,70H20l26.3,33H90Z"/>
                                    <polygon class="logo__part" points="119.3 8 157.7 55.8 119.4 103.8 112.5 95 139.8 61 85.1 61 81 55.8 85.1 50.9 140.9 50.9 112.2 17 119.3 8"/>
                                    <path id="logoName" class="logo__part" d="M243,36.8a6.53,6.53,0,0,1,0,1.4,6.6,6.6,0,0,1-.1,1.4l-3.8,47.5H225.5l-1.6-5.4a39.15,39.15,0,0,1-7.9,4.6,21.3,21.3,0,0,1-8.8,2.1h-3.3a18.33,18.33,0,0,1-4.1-.6,17.26,17.26,0,0,1-4.9-2.1,13.68,13.68,0,0,1-4.2-4.4,16.53,16.53,0,0,1-2.2-7.2,7.39,7.39,0,0,1,0-2.1c0-.8.1-1.7.2-2.9l.3-3.8a41.64,41.64,0,0,1,.8-5.2,13.22,13.22,0,0,1,2.7-5.6,16.45,16.45,0,0,1,6.1-4.5q4.05-1.8,11.1-1.8h14.8l.3-3.1a4.87,4.87,0,0,0,.1-1.2V43a1.77,1.77,0,0,0-.9-1.6,2.82,2.82,0,0,0-1.5-.4H199.4l1.1-14.6h30.3c4.2,0,7.2,1,9.1,3a11.56,11.56,0,0,1,3.1,7.4ZM208.4,60.3a2.52,2.52,0,0,0-1.7.6,2.61,2.61,0,0,0-.9,1.8l-.6,5.2a18.48,18.48,0,0,0-.1,2.3,5.71,5.71,0,0,0,1,2.5c.6.9,1.7,1.3,3.4,1.3a28.83,28.83,0,0,0,6.7-.9,28.36,28.36,0,0,0,6.6-2.3l.7-10.4-15.1-.1ZM309.9,41.8a46.85,46.85,0,0,1,0,5.3l-1.5,19.4c-.5,6.3-2.4,11.3-5.8,15.1s-8.2,5.7-14.5,5.7H267.2l-1.4,17H247.9l6.2-77.9h14.6l1.7,4.5c1.4-.6,3.1-1.2,5-1.9s3.9-1.3,6-1.9,4.2-1,6.3-1.4a32.1,32.1,0,0,1,6-.6,15.41,15.41,0,0,1,6.5,1.3,16.39,16.39,0,0,1,5,3.6,16.94,16.94,0,0,1,3.3,5.3,26.79,26.79,0,0,1,1.4,6.5Zm-17.8,5.1a5.74,5.74,0,0,0-1.3-3.5c-.8-.9-2.2-1.3-4.2-1.3a23.86,23.86,0,0,0-3.1.3l-4.4.6c-1.5.3-3.1.5-4.6.8s-2.8.6-3.8.8l-2.2,26.8h19.4a2.24,2.24,0,0,0,2.5-2.4l1.5-19.2a6.62,6.62,0,0,0,.1-1.5,8.17,8.17,0,0,0,.1-1.4Zm42.7,40.3H316.4l4.9-60.9h18.4l-4.9,60.9Zm6-68.1H321.4L322.3,8h19.4l-.9,11.1Zm44.1,23.1h-2.3a35.72,35.72,0,0,0-8.7,1c-2.8.7-5.2,1.3-7.1,2l-3.4,42H344.9l4.9-60.9h15.1l1.6,5a45.88,45.88,0,0,1,4.4-2.1c1.6-.7,3.3-1.4,5.1-2a53.39,53.39,0,0,1,5.3-1.6,23.4,23.4,0,0,1,5-.6l-1.4,17.2Zm63.8,24.3c-.4,6.1-2.3,11.1-5.6,15s-8.1,5.8-14.5,5.8H404.1c-4.3,0-7.7-1.2-10.1-3.6s-3.8-5.7-4.2-10a7.38,7.38,0,0,1,0-2.1c0-.6.1-1.8.2-3.5l1.5-21a27.19,27.19,0,0,1,1.7-8.2,18.07,18.07,0,0,1,3.9-6.6,17.65,17.65,0,0,1,6.3-4.3,23.43,23.43,0,0,1,8.8-1.6h24.4c4.2,0,7.5,1.4,9.8,4.2a19.23,19.23,0,0,1,4,10.6c0,.6.1,1.3.1,2a13.42,13.42,0,0,1-.1,2.1l-1.7,21.2Zm-16-20.4a3.31,3.31,0,0,0-1.4-2.7,4.86,4.86,0,0,0-3-.9H411.7q-2.25,0-2.4,2.4l-1.4,19a6.15,6.15,0,0,0-.1,1.4v1.3a3.76,3.76,0,0,0,1.2,2.6,4.4,4.4,0,0,0,3.2,1h16.4a2.24,2.24,0,0,0,2.5-2.4l1.5-19.7a4.87,4.87,0,0,0,.1-1.2v-.8Zm84.8-5.9c.1,1,.1,2.1.1,3.2s-.1,2.4-.1,3.7l-3.1,40.1h-18L499.2,51a22.23,22.23,0,0,0,.1-3.5c-.3-3.6-2-5.4-5.2-5.4a48,48,0,0,0-7.6.8c-3,.5-5.7,1.1-8,1.7l-3.6,42.6H457l4.9-60.9h14.6l1.7,4.5c1.8-.8,3.7-1.5,5.7-2.2a63.21,63.21,0,0,1,6.2-1.8c2.1-.5,4.1-.9,6-1.3a36.1,36.1,0,0,1,5.3-.5,29.65,29.65,0,0,1,5,.4,13.74,13.74,0,0,1,5.1,1.9,12,12,0,0,1,4.1,4.5,22.66,22.66,0,0,1,1.9,8.4Zm61.2.1a7.39,7.39,0,0,1,0,2.1c0,.7-.1,1.3-.1,1.8l-.5,4.9a11.84,11.84,0,0,1-4.7,8.5,22.64,22.64,0,0,1-10.1,4.3,22.3,22.3,0,0,1-4.5.6c-1.9.2-3.8.3-5.9.4s-4,.2-5.7.3-3.1.1-3.9.1l-.2,2.6a5.63,5.63,0,0,0,0,1.3l.1.8a5.09,5.09,0,0,0,1.3,3.1c.8.9,2.2,1.4,4.2,1.4s4.4-.1,7-.3,5.2-.5,7.9-.8,5.2-.6,7.6-1.1a63.11,63.11,0,0,0,6.4-1.4l-1.1,13.5a50.67,50.67,0,0,1-6.3,2.2c-2.5.7-5.1,1.3-7.8,1.9s-5.5,1-8.3,1.3a72.41,72.41,0,0,1-7.6.5,48.51,48.51,0,0,1-6-.4,19.14,19.14,0,0,1-6.8-2.1,15.89,15.89,0,0,1-5.7-5c-1.6-2.2-2.6-5.2-2.9-9.2A21.75,21.75,0,0,1,525,69c0-.9.1-1.8.2-2.8l1.5-19.4a29.18,29.18,0,0,1,1.6-7.9,19.89,19.89,0,0,1,3.9-6.6,17.83,17.83,0,0,1,6.4-4.6,23.41,23.41,0,0,1,9.3-1.7h12.9a22.81,22.81,0,0,1,9,1.5,15.42,15.42,0,0,1,5.3,3.6,11.77,11.77,0,0,1,2.7,4.5,31,31,0,0,1,.9,4.7Zm-28.4.7a6.79,6.79,0,0,0-3.4,1.1,4.7,4.7,0,0,0-2.1,3.8l-.7,6.7c.5,0,1.4,0,2.8-.1a36.39,36.39,0,0,0,4.5-.3c1.6-.1,3.1-.3,4.5-.4a30.68,30.68,0,0,0,3.1-.4,6.31,6.31,0,0,0,3.3-1.5,5.82,5.82,0,0,0,1.1-1.8,9.59,9.59,0,0,0,.3-1.5,10.36,10.36,0,0,0,.1-1.6,4.34,4.34,0,0,0-1.3-2.9,4.17,4.17,0,0,0-3.2-1.1Z"/>
                                </g>
                            </svg>
                    </a></p>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>