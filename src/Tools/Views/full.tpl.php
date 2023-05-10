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

.qr__image {
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
    height: 0;
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
    color: rgba(0, 0, 0, 0);
}

.loading p.skeleton__box {
    width: 100%;
    height: 2rem;
}

.loading .skeleton__box>span {
    opacity: 0;
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
</style>
<div class="invoice-wrapper">
    <div
        :class="[
            'invoice',
            { loading },
            { 'invoice-expired': status.title === 'Expired' },
            { 'invoice__qr-only': qrOnly },
        ]"
    >
        <div class="invoice__body">
            <div class="invoice__info">
                <div class="qr__wrapper">
                    <div class="skeleton__box">
                        <figure class="qr" v-if="!loading && status.title !== 'Expired'">
                            <qr-code :text="data.qr" error-level="H" />
                            <img
                                class="qr__image"
                                :src="`${publicPath}img/currencies/${data.currency}.svg`"
                                :alt="data.currency"
                            />
                        </figure>
                    </div>
                </div>
                <div class="info" v-if="!qrOnly">
                    <div v-if="userData">
                        <h1 v-if="userData.data">{{ userData.data }}</h1>
                        <h1 v-else>
                            {{ $t("title") }} <small v-if="data.invoice">({{ data.invoice }})</small>
                        </h1>
                        <p class="merchant" v-if="userData.merchant">
                            {{ $t("from") }}
                            <a v-if="userData.url" :href="userData.url" target="_blank" rel="noopener noreferrer"
                                class="link hovered">{{ userData.merchant }}</a>
                            <span v-else>{{ userData.merchant }}</span>
                        </p>
                    </div>
                    <h1 v-else>
                        {{ $t("title") }} <small v-if="data.invoice">({{ data.invoice }})</small>
                    </h1>
                    <p class="skeleton__box info__date"><span>{{ data.created }}</span></p>
                    <p class="skeleton__box info__amount">
                        <w-copy
                            v-if="!loading && status.title !== 'Expired' && data?.amountData?.isNumber"
                            :text="data.amountData.value"
                            style="margin-right: .5rem;"
                        />
                        <small v-if="remainsToPay">{{ $t("remainsToPay") }} <br></small>
                        <span>{{ data.amount }}</span>
                    </p>
                </div>
            </div>
            <div v-if="!qrOnly">
                <div class="address">
                    <div class="address__title">{{ $t("paymentAddress") }}</div>
                    <p class="skeleton__box">
                        <span v-if="status.title !== 'Expired'">{{ data.address }}</span>
                    </p>
                    <w-copy :text="data.address" v-if="!loading && status.title !== 'Expired'" />
                </div>
                <div class="status-data__wrapper">
                    <div :class="['status-data', { historyFlag }]">
                        <div :class="['status', { skeleton__box: status.title !== 'Warning' }, `${status.title.toLowerCase()}`]">
                            <div class="status__icon">
                                <span v-if="status.title !== 'Loading'">
                                    <component :is="`${status.title}Icon`" />
                                </span>
                            </div>
                            <div class="status__text">
                                <p>{{ status.description }}</p>
                                <p v-if="expire && status.title !== 'Success'" class="countdown">{{ expire }}</p>
                            </div>
                        </div>
                        <div class="history" v-if="data.history">
                            <ul>
                                <li class="history__item" v-for="item, id in data.history" :key="id">
                                    <span>
                                        {{ (new Date(`${item.date}+00:00`)).toLocaleString() }}
                                        <span v-if="item.amount">({{ (item.amount * factor).toFixed(8) }})</span>
                                    </span>
                                    <span style="text-align: right;">
                                        {{ $te(`statuses.${item.status}`) ? $t(`statuses.${item.status}`) : item.status }}
                                    </span>
                                </li>
                            </ul>
                        </div>
                        <w-btn @click="historyFlag = !historyFlag" class="toggler">
                            <ArrowIcon />
                        </w-btn>
                    </div>
                </div>
                <p v-if="linkbackCounter && !embed" class="countdown" style="text-align: center;">
                    {{ $t("getBack.part1") }} {{ linkbackCounter }} {{ $t("getBack.part2") }} <a :href="linkback" class="link hovered">{{ $t("getBack.part3") }}</a>
                </p>
                <div v-if="userData">
                    <ItemsTable :data="userData"/>
                </div>
                <div class="invoice__footer" v-if="!embed">
                    <p>
                        Powered by
                        <a href="https://apirone.com/" title="Apirone" class="link hovered">
                            <Logo />
                        </a>
                    </p>
                </div>
            </div>
            <div v-else :class="['status', 'status__qr-only', { skeleton__box: status.title !== 'Warning' }, `${status.title.toLowerCase()}`]">
                <p>
                    <span v-if="status.title !== 'Loading'">
                        <component :is="`${status.title}Icon`" />
                    </span>
                    {{ status.description }}
                </p>
            </div>
        </div>
    </div>
</div>