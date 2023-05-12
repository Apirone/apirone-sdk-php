<?php

use Apirone\Invoice\Model\Settings\Currency;
use Apirone\Invoice\Tools\Utils;
?>
<style>
.link {
    transition: .3s;
    color: #5d8ab9;
}

.invoice {
    width: 95%;
    max-width: 35rem;
    padding: 2rem 0;
}

.invoice.invoice__qr-only {
    width: auto;
}

.invoice-expired {
    opacity: .6;
}

.invoice h1 {
    margin: 0;
}

.invoice h1 small {
    font-size: 50%;
    opacity: .6;
}

.merchant {
    margin-top: 0;
}

.info__date {
    color: #a5a5a5;
    margin: 0;
}

.info__amount {
    display: flex;
    font-size: 1.3rem;
    font-weight: 600;
    margin-bottom: 0;
}

.info__amount small {
    opacity: .7;
}

.invoice__body {
    margin-top: 1rem;
}

.invoice__info {
    display: flex;
}

.info {
    margin-left: 2rem;
    flex-grow: 1;
}

.qr__wrapper {
    width: 10rem;
    height: 10rem;
    border: .0625rem solid #f1f1f1;
    padding: 1rem;
    border-radius: 1rem;
    overflow: hidden;
    align-self: center;
}

.qr__wrapper>div {
    width: 100%;
    height: 100%;
}

.qr {
    display: inline-block;
    font-size: 0;
    margin: 0;
    position: relative;
}

.qr__wrapper img.qr__image {
    background-color: #fff;
    border: 0.25rem solid #fff;
    border-radius: 50%;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.25);
    height: 15%;
    left: 50%;
    overflow: hidden;
    position: absolute;
    top: 50%;
    transform: translate(-50%, -50%);
    width: 15%;
}

.address {
    box-sizing: border-box;
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: relative;
    width: 100%;
    min-height: 4rem;
    margin: 1rem 0;
    padding: 0 1rem;
    border: 1px solid#f1f1f1;
    border-radius: 1rem;
}

.address p {
    word-wrap: break-word;
    max-width: 87%;
}

.address__title {
    position: absolute;
    top: -.8rem;
    left: .7rem;
    padding: .3rem;
    font-size: .8rem;
    background-color: #ffffff;
    color: #a5a5a5;
}

.status-data__wrapper {
    position: relative;
    min-height: 8rem;
    width: 100%;
    z-index: 9;
}

.status-data {
    margin: 1rem 0;
    position: absolute;
    width: 100%;
}

.history {
    /* height: 0; */
    overflow-y: hidden;
    padding-top: 1rem;
    transition: .4s;
    transform: translateY(-1.75rem);
}

.status-data.historyFlag .history {
    height: unset;
    transform: translateY(-.75rem);
    border: 1px solid #f1f1f1;
    border-top: none;
    border-radius: 0 0 1rem 1rem;
}

.status-data.historyFlag .history ul {
    padding: 0 1rem 1rem;
    margin-bottom: 0;
    background-color: #ffffff;
}

.history__item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #ffffff;
}

.status-data__wrapper .status {
    display: flex;
    align-items: center;
    position: relative;
    background-color: #f1f1f1;
    border-radius: 1rem;
    padding-right: 1rem;
}

.status-data__wrapper .status__icon {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 4rem;
    width: 4rem;
    margin-right: 2rem;
    border-radius: 1rem;
    background-color: #f1f1f1;
}

.status-data__wrapper .status__text {
    display: flex;
    justify-content: space-between;
    flex-grow: 1;
}

.status-data__wrapper .status__icon span {
    width: 2rem;
    height: 2rem;
}

.status.refresh .status__icon {
    background-color: #5d8ab9;
}

.status.success .status__icon {
    background-color: #30cb83;
}

.status.expired .status__icon {
    background-color: #a5a5a5;
}

.status.warning .status__icon {
    background-color: #f39c12;
}

.status__icon {
    color: #ffffff;
}

.status__qr-only>p {
    display: flex;
    align-items: center;
    justify-content: center;
}

.status__qr-only>p>span {
    display: block;
    width: 1.2rem;
    height: 1.2rem;
    margin-right: .5rem;
}

.status__qr-only.refresh>p {
    color: #5d8ab9;
}

.status__qr-only.success>p {
    color: #30cb83;
}

.status__qr-only.expired>p {
    color: #a5a5a5;
}

.status__qr-only.warning>p {
    color: #f39c12;
}

.status.refresh .svg {
    -webkit-animation: spin 2s linear infinite;
    -moz-animation: spin 2s linear infinite;
    animation: spin 2s linear infinite;
}

.invoice__footer {
    color: #a5a5a5;
}

.invoice__footer p {
    text-align: center;
}

.invoice__footer .logo {
    width: 5rem;
    transform: translateY(2px);
}

.loading .skeleton__box {
    position: relative;
    overflow: hidden;
    background-color:#f1f1f1;
    border-radius: 1rem;
    /* color: rgba(0, 0, 0, 0); */
}

.loading p.skeleton__box {
    width: 100%;
    height: 2rem;
}

.loading .skeleton__box>span {
    /* opacity: 0; */
    opacity: 0.5;
}

.loading .skeleton__box::after {
    position: absolute;
    inset: 0;
    transform: translateX(-100%);
    background-image: linear-gradient(
    90deg,
    rgba(255, 255, 255, 0) 0,
    rgba(255, 255, 255, 0.2) 20%,
    rgba(255, 255, 255, 0.5) 60%,
    rgba(255, 255, 255, 0)
    );
    animation: shimmer 2s infinite;
    content: '';
}

table {
    box-sizing: border-box;
    text-indent: initial;
    border-spacing: 2px;
    border-color: grey;
    border-collapse: collapse;
    display: table;
    overflow-x: auto;
    width: 100%;
}

thead {
    display: table-header-group;
    vertical-align: middle;
    border-color: inherit;
}

tr {
    display: table-row;
    vertical-align: inherit;
    border-color: inherit;
    border-top: 1px solid rgb(223, 226, 229);
}

td, th {
    border-width: 1px;
    border-style: solid;
    border-color: rgb(223, 226, 229);
    border-image: initial;
    padding: 0.6em 1em;
    text-align: left;
}

th {
    display: table-cell;
    vertical-align: inherit;
    font-weight: bold;
}

.invoice__footer {
    color: #a5a5a5;
}

.invoice__footer p {
    text-align: center;
}

.countdown {
    font-weight: bold;
}

@-moz-keyframes spin { 
    100% { -moz-transform: rotate(-360deg); } 
}

@-webkit-keyframes spin { 
    100% { -webkit-transform: rotate(-360deg); } 
}

@keyframes spin { 
    100% { 
        -webkit-transform: rotate(-360deg); 
        transform: rotate(-360deg); 
    } 
}

@keyframes shimmer {
    100% {
        transform: translateX(100%);
    }
}

@media screen and (max-width: 540px) {
    .invoice__info {
        flex-direction: column;
    }

    .info {
        order: -1;
        margin-left: 0;
    }
}

@media screen and (max-width: 400px) {
    .status__icon {
        margin-right: 1rem;
    }
}
</style>

<style>
.qr__wrapper img {
    width: 100%;
    height: 100%;
}

.qr__wrapper canvas {
    max-width: 100%;
}

.invoice__footer .logo {
    width: 5rem;
    transform: translateY(2px);
}

.btn__copy {
    margin-left: 2rem;
}

.status__icon .svg, .status__qr-only>p .svg {
    width: 100%;
    height: 100%;
}

.loading .toggler {
    display: none !important;
}

body .status-data .toggler {
    position: absolute;
    top: 100%;
    right: 1rem;
    margin-top: -1rem;
    border-radius: 0 0 0.3rem 0.3rem;
    border-color: #f1f1f1;
    background-color: #f1f1f1;
    padding: 0 0.5rem;
}

body .status-data.historyFlag .toggler {
    margin-top: -.75rem;
}

body .status-data .toggler .btn__icon {
    transform: rotate(180deg);
}

body .status-data.historyFlag .toggler .btn__icon {
    transform: rotate(0deg);
}
.invoice-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        min-height: 100vh;
}

.btn {
    display: flex;
    align-items: center;
    position: relative;
    -webkit-appearance: none;
    outline: none;
    font: inherit;
    color: inherit;
    padding: .625rem;
    margin: 0;
    box-sizing: border-box;
    text-decoration: none;
    cursor: pointer;
    transition: .3s;
    background-color: #fff;
    border: .0625rem solid #5d8ab9;
    border-radius: .625rem;
}

.btn:disabled {
    cursor: default;
    opacity: .6;
}

.btn_filled {
    color: #fff;
    background-color: #5d8ab9;
}

.btn_loading .btn__text {
    opacity: 0;
}

.btn__loader {
    position: absolute !important;
    opacity: 0;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) scale(.4);
}

.btn_loading .btn__loader {
    opacity: 1;
}
</style>

<style>
.btn_filled .lds .lds__elem {
    background: #fff;
}

.btn .btn__icon {
    display: inline-block;
    width: 1.25rem;
    height: 1.25rem;
}

</style>

<style>
.btn__copy {
    -webkit-appearance: none;
    outline: none;
    font: inherit;
    color: inherit;
    display: flex;
    border: 0;
    background-color: #fff;
    align-items: center;
    margin: 0;
    padding: 0;
    color: rgba(93, 138, 185, 1);
    cursor: copy;
    transition: .3s;
}

.btn__copy-title {
    margin-left: .3rem;
}

.btn__copy:disabled {
    cursor: default;
    opacity: .6;
}

.btn__copy-img {
    transform: translateY(.125rem);
    width: 1rem;
    height: 1rem;
}
</style>

<style>
.btn__copy-img.icon_copy-success {
    color: green;
}
</style>

<style>
table {
  border-collapse: collapse;
  width: 100%;
  margin-bottom: 20px;
}

th, td {
  padding: 8px;
  text-align: left;
}

tbody td:not(:first-child), thead th:not(:first-child) {
  text-align: right;
}

th {
  background-color: rgba(241, 241, 241, .5);
  font-weight: bold;
}

tbody tr:nth-child(even) {
  background-color: rgba(241, 241, 241, .5);
}

tfoot td {
  border-top: 1px solid rgba(241, 241, 241, .5);
  text-align: right;
}

tfoot tr:first-child {
  border-top: 1px solid rgba(241, 241, 241, .5);
}

tfoot td:first-child {
  font-weight: bold;
}

.invoice-table__no-items tfoot td:first-child {
  text-align: left;
}

tfoot tr:last-child td:last-child {
  font-weight: bold;
}

table tfoot:only-child td {
  text-align: right;
}
</style>
<script type="text/javascript">
// var userLang = navigator.language || navigator.userLanguage; 
// console.log("The language is: " + userLang);

// console.log(Intl.DateTimeFormat().resolvedOptions().timeZone)
// var offset = new Date().getTimezoneOffset();
// console.log(offset/-60);
// if offset equals -60 then the time zone offset is UTC+01
</script>

<div class="invoice-wrapper">
    <div class="invoice<?php echo $loading ? ' loading' : ''; ?>">
        <div class="invoice__body">
            <div class="invoice__info">
                <div class="qr__wrapper">
                    <div class="skeleton__box">
                        <?php if (!$loading && $invoice->status !== 'expired') : ?>
                        <figure class="qr">
                            <img src="<?php echo Utils::getQrLink($currency, $invoice->address, 0); ?>" />
                            <img class="qr__image" src="https://apirone.com/static/img2/<?php echo $invoice->currency; ?>.svg" alt="data.currency" />
                        </figure>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="info">
                <?php if ($loading) : /* Loadig (Skeleton) */?>
                    <h1><?php $t("title"); ?></h1>
                    <p class="skeleton__box info__date"></p>
                    <p class="skeleton__box info__amount">
                <?php else : /* Invice data */?>
                    <?php if($userData) : /* userData */ ?>
                    <div>
                        <h1><?php $t("title"); ?> <small><?php echo $invoice->invoice; ?></small></h1>
                        <?php if($userData && $userData->merchant) : ?>
                        <p class="merchant" v-if="userData.merchant">
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
                    <p class="skeleton__box info__date"><span><?php echo $this->datefmt($invoice->created); ?></span></p>
                    <p class="skeleton__box info__amount">
                        <!-- <w-copy
                            v-if="!loading && status.title !== 'Expired' && data?.amountData?.isNumber"
                            :text="data.amountData.value"
                            style="margin-right: .5rem;"
                        /> -->
                        <?php if($invoice->remains() > 0) : ?>
                        <small v-if="remainsToPay"><?php $t("remainsToPay"); ?> <br></small>
                        <span><?php echo Utils::min2cur($invoice->remains(), $currency->getUnitsFactor()); ?></span>
                        <?php endif; ?>
                    </p>
                    </div>
                <?php endif; /* Loading/Invoce end */ ?>
                </div>
            </div>
            <div>
                <div class="address">
                    <div class="address__title"><?php $t("paymentAddress"); ?></div>
                    <p class="skeleton__box">
                        <?php if (!$loading && $invoice->status !== 'expired') : ?>
                        <span><?php echo $invoice->address; ?></span>
                        <?php endif; ?>
                    </p>
                    <w-copy :text="data.address" v-if="!loading && status.title !== 'Expired'" />
                </div>
                <div class="status-data__wrapper">
                    <div class="status-data historyFlag">
                        <div class="status skeleton__box status.title.toLowerCase">
                            <div class="status__icon">
                                <span v-if="status.title !== 'Loading'">
                                    <component :is="`${status.title}Icon`" />
                                </span>
                            </div>
                            <div class="status__text">
                                <p>status.description</p>
                                <p v-if="expire && status.title !== 'Success'" class="countdown"><?php echo $invoice->countdown(); ?></p>
                            </div>
                        </div>
                        <?php if (!$loading && $invoice->history) : ?>
                        <div class="history">
                            <ul>
                                <?php foreach ($invoice->history as $item) : ?>
                                <li class="history__item">
                                    <span>
                                        <?php echo $this->datefmt($item->date); ?>
                                        <?php if ($item->amount) : ?>
                                        <span><?php echo ($item->amount * $currency->{'units-fator'} ) ?></span>
                                        <?php endif; ?>
                                    </span>
                                    <span style="text-align: right;">
                                        <?php $t($item->status); ?>
                                    </span>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>
                        <button class="btn hovered toggler"><div class="lds btn__loader"><div class="lds__elem"></div><div class="lds__elem"></div><div class="lds__elem"></div><div class="lds__elem"></div><div class="lds__elem"></div><div class="lds__elem"></div><div class="lds__elem"></div><div class="lds__elem"></div><div class="lds__elem"></div><div class="lds__elem"></div><div class="lds__elem"></div><div class="lds__elem"></div></div><!--v-if--><span data-v-6aa818a0="" class="btn__icon"><svg data-v-7ea0aa26="" data-v-c1f1971a="" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="svg icon_arrow" alt="Arrow icon"><path data-v-7ea0aa26="" class="arrow__path" d="M19.9201 15.0499L13.4001 8.52989C12.6301 7.75989 11.3701 7.75989 10.6001 8.52989L4.08008 15.0499" stroke="#292D32" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path></svg></span></button>
                        <!-- <button class="btn__copy">
                            <input type="hidden" readonly="" value="39kVwDUFHdAoFP6yCVGQ9zhNMSQwpsYqwd">
                            <span role="img">
                                <svg viewBox="0 0 24 24" fill="#ccc" xmlns="http://www.w3.org/2000/svg" class="svg icon_copy btn__copy-img" alt="Copy icon"><path class="copy__path" d="M16 12.9V17.1C16 20.6 14.6 22 11.1 22H6.9C3.4 22 2 20.6 2 17.1V12.9C2 9.4 3.4 8 6.9 8H11.1C14.6 8 16 9.4 16 12.9Z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path class="copy__path" d="M22 6.9V11.1C22 14.6 20.6 16 17.1 16H16V12.9C16 9.4 14.6 8 11.1 8H8V6.9C8 3.4 9.4 2 12.9 2H17.1C20.6 2 22 3.4 22 6.9Z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                            </span>
                        </button> -->
                    </div>
                </div>
                <?php if(@$invoice->showLinkback) : /* TODO: LINKBACK HANDLER */?> 
                <p class="countdown" style="text-align: center;">
                    <?php echo sprintf($t('getBack', false), 15, $invoice->linkback); ?>
                </p>
                <?php endif; ?>
                <?php if(!$loading && $userData) : ?>
                <?php
                    $items = $userData->getItems();
                    $extras = $userData->getExtras();
                    $itemsCount = count($items);
                ?>
                <div>
                <table class="<?php  echo $items ? '' : 'invoice-table__no-items'; ?>">
                    <?php if($items) : ?>
                    <thead>
                    <tr>
                        <th>#</th>
                        <th><?php $t('Item'); ?></th>
                        <th><?php $t('Cost'); ?></th>
                        <th><?php $t('Qty'); ?></th>
                        <th><?php $t('Total'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($items as $item) : ?>
                    <tr>
                        <td><?php //pa($item->getName()); ?></td>
                        <td><?php echo $item->name ?? $t('Item'); ?></td>
                        <td><?php echo $item->cost ?? ''; ?></td>
                        <td><?php echo $item->qty ?? ''; ?></td>
                        <td><?php echo $item->total ?? ''; ?></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                    <?php endif; ?>
                    <?php if ($userData->subPrice) : ?>
                    <tr>
                        <td colspan="4"><?php $t('Subtotal') ?>:</td>
                        <td><?php echo $userData->subPrice ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php foreach($extras as $item) : ?>
                    <tr>
                        <td colspan="4"><?php echo $item->name ?? '' ;?>:</td>
                        <td><?php echo $item->price ?? '' ;?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if ($userData->Price) : ?>
                    <tr>
                        <td colspan="4"><?php $t('Total') ?>:</td>
                        <td><?php echo $userData->price ?></td>
                    </tr>
                    <?php endif; ?>
                    </tfoot>
                </table>
                </div>
                <?php endif; ?>
                __BACKLINK_WILL_BE_HERE__
                <?php if ($this->showLogo) : ?>
                <div class="invoice__footer">
                    <p>
                        Powered by
                        <a href="https://apirone.com/" title="Apirone" class="link hovered">
                            <Logo />
                        </a>
                    </p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>