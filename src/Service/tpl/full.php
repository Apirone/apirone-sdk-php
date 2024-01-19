
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
    <div class="invoice<?php echo $loading ? ' loading' : ''; echo $status->title == 'Expired' ? ' invoice-expired' : ''; ?>">
        <div class="invoice__body">
            <div class="invoice__info">
                <div class="qr__wrapper">
                    <div class="skeleton__box">
                    <?php if ($details) : ?>
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
                <div class="info">
                <?php if ($loading) : /* Loading (Skeleton) */?>
                    <h1><?php $t("title"); ?></h1>
                    <p class="skeleton__box info__date"></p>
                    <p class="skeleton__box info__amount"></p>
                <?php else : /* Invoice data */?>
                    <?php if($userData) : /* userData */ ?>
                    <div>
                        <?php if($userData->title) : ?>
                        <h1><?php echo $userData->title; ?></h1>
                        <?php else : ?>
                        <h1><?php $t("title"); ?> <small><?php echo $details->invoice; ?></small></h1>
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
                        <?php $t("title"); ?> <small><?php echo $details->invoice; ?></small>
                    </h1>
                    <?php endif; /* UserData end */ ?>
                    <p class="skeleton__box info__date"><span><?php $d($details->created); ?></span></p>
                    <p class="skeleton__box info__amount">
                    <?php if ($details->status == 'partpaid') : ?>
                        <small><?php $t("remainsToPay"); ?> <br></small>
                    <?php endif; ?>
                    <?php if($details && $amount) : ?>
                        <span><?php echo $amount . ' ' . strtoupper($details->currency); ?></span>
                        <?php echo ($status->title == 'Refresh') ? $c($amount, 'margin-left: .5rem;', false) : ''; ?>
                    <?php endif; ?>
                    </p>
                    </div>
                <?php endif; /* Loading/Invoice end */ ?>
                </div>
                <input id="invoice_id" type="hidden" value="<?php echo $id; ?>">
                <a id="statusUrl" href="<?php echo $statusLink; ?>" style="display: none"></a>
            </div>
            <div>
                <?php if($status->title == 'Refresh' || $status->title == 'Warning') : ?>
                <div class="address">
                    <div class="address__title"><?php $t("paymentAddress"); ?></div>
                    <p class="skeleton__box">
                        <?php if (!$loading && $details) : ?>
                        <a href="<?php echo Utils::getAddressLink($currency, $details->address); ?>" target="_blank"><?php echo $details->address; ?></a>
                        <?php endif; ?>
                    </p>
                    <?php if (!$loading && $status->title == 'Refresh') : ?>
                        <?php $c($details->address); ?>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <?php if(!$loading && $userData) : ?>
                <?php
                    $items = $userData->getItems();
                    $extras = $userData->getExtras();
                ?>
                <div class="invoice-table">
                    <table>
                        <?php if($items && $items !== null) : ?>
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
                <?php if ($extras) : ?>
                    <?php foreach($extras as $item) : ?>
                    <div>
                        <strong><?php echo $item->name ?? '' ;?></strong>
                        <span><?php echo $item->price ?? '' ;?></span>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                <?php if ($userData->price) : ?>
                    <div>
                        <strong><?php $t('total') ?>:</strong>
                        <span>
                        <strong><?php echo $userData->price ?? '' ;?></strong>
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
                                <?php if($status->description) : ?>
                                <p><?php $t($status->description); ?></p>
                                <?php endif; ?>
                                <?php if($details && $details->timeToExpire() > 0) : ?>
                                <input type="hidden" id = "expire" value="<?php echo $details->timeToExpire(); ?>">
                                <p id="countdown" class="countdown"></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if ($details && $details->history) : ?>
                        <div class="history">
                            <input type="hidden" id="statusNum" value="<?php echo $details->statusNum(); ?>">
                            <ul>
                                <?php foreach ($details->history as $item) : ?>
                                <li>
                                    <?php if ($item->getAmount()) : ?>
                                    <a class="history__item" href="<?php echo Utils::getTransactionLink($currency, $item->getTxid()); ?>" target="_blank">
                                        <span><?php $d($item->getDate()); ?> <span>(<?php echo Utils::exp2dec($item->getAmount() * $currency->getUnitsFactor()) ?>)</span></span>
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
                    <?php if ($note): ?>
                    <div class="status-note">
                        <?php echo $t($note); ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php if($details && @$details->showLinkback()) : ?> 
                <p class="countdown" style="text-align: center;">
                    <?php echo sprintf($t('getBack', false), 15, $l($details->linkback, $details->invoice)); ?>
                </p>
                <?php endif; ?>
                
                <?php if ($backlink || $logo) : ?>
                <div class="invoice__footer">
                    <?php if ($backlink) : ?>
                    <p>
                        <?php echo sprintf($t('backlink', false), $l($backlink, $details ? $details->invoice : '')); ?>
                    </p>
                    <?php endif; ?>
                    <?php if ($logo) : ?>
                    <p> Powered by <a href="https://apirone.com/" title="Apirone" class="link hovered logo"></a></p>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
