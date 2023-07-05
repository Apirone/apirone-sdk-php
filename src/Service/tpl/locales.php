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

$locales = [
    'en' => [
        "title" => "Invoice",
        'from' => 'From',
        'remainsToPay' => 'Remains to pay',
        'paymentAddress' => 'Payment address',
        'getBack' => 'Get back to the site in <span id="linkback-counter">%s</span> seconds or click <a href="%s" id="linkback" class="link hovered">here</a>',
        'backlink' => '<a href="%s" class="link hovered">Back to store</a>',
        'somethingWentWrong' => 'Something went wrong',
        'invalidInvoiceId' => 'Invalid invoice id',
        'notFound' => 'Not Found',
        'waitingForPayment' => 'Waiting for payment',
        'notePayment' => "Please pay the exact amount. Don't include the network fee into the amount. Otherwise the funds do not arrive fully, so the invoice would be considered as partly paid and you should pay the difference.",
        'noteOverpaid' => 'You paid more than it is required. Please contact the seller to refund the difference since the seller is the only owner of this wallet and has all the funds.',
        'statusUpdating' => 'Status updating',
        'paymentAccepted' => 'Payment accepted',
        'paymentExpired' => 'Expired',
        'created' => 'created',
        'partpaid' => 'partpaid',
        'paid' => 'paid',
        'overpaid' => 'overpaid',
        'completed' => 'completed',
        'expired' => 'expired',
        'item' => 'Item',
        'qty' => 'Qty',
        'cost' => 'Cost',
        'total' => 'Total',
        'subtotal' => 'Subtotal',
    ],
    'ru' => [
        'title' => 'Счёт',
        'from' => 'От',
        'remainsToPay' => 'Осталось оплатить',
        'paymentAddress' => 'Платежный адрес',
        'getBack' => 'Вы будете перенаправлены на сайт в течение <span id="linkback-counter">%s</span> секунд или нажмите <a href="%s" id="linkback" class="link hovered">здесь</a>',
        'backlink' => '<a href="%s" class="link hovered">Вернуться в магазин</a>',
        'somethingWentWrong' => 'Что-то пошло не так',
        'invalidInvoiceId' => 'Ничего не найдено',
        'notFound' => 'Ничего не найдено',
        'waitingForPayment' => 'Ожидание оплаты',
        'notePayment' => 'Пожалуйста, оплатите точную сумму. Не включайте плату за сеть в сумму. В противном случае средства не поступят полностью, поэтому счет будет считаться частично оплаченным, и вам придется доплатить разницу.',
        'noteOverpaid' => 'Вы заплатили больше, чем требуется. Пожалуйста, свяжитесь с продавцом, чтобы вернуть разницу, так как продавец является единственным владельцем этого кошелька и имеет все средства.',
        'statusUpdating' => 'Обновление статуса',
        'paymentAccepted' => 'Платеж принят',
        'paymentExpired' => 'Инвойс просрочен',
        'created' => 'создан',
        'partpaid' => 'частично оплачен',
        'paid' => 'оплачен',
        'overpaid' => 'переплачен',
        'completed' => 'завершён',
        'expired' => 'просрочен',
        'item' => 'Наименование',
        'qty' => 'Кол-во',
        'cost' => 'Цена',
        'total' => 'Итого',
        'subtotal' => 'Подытог',
    ],
    'es' => [
        'title' => 'Factura',
        'from' => 'De',
        'remainsToPay' => 'Queda por pagar',
        'paymentAddress' => 'Dirección de pago',
        'getBack' => 'Regrese al sitio en <span id="linkback-counter">%s</span> segundos o haga click <a href="%s" id="linkback" class="link hovered">aquí</a>',
        'backlink' => '<a href="%s" class="link hovered">De vuelta a la tienda</a>',
        'somethingWentWrong' => 'Algo salió mal',
        'invalidInvoiceId' => 'ID de factura no válido',
        'notFound' => 'No encontrada',
        'waitingForPayment' => 'A la espera del pago',
        'notePayment' => 'Por favor pague la cantidad exacta. No incluya la tarifa de red en el monto. De lo contrario, los fondos no llegan en su totalidad, por lo que la factura se consideraría pagada en parte y deberá pagar la diferencia.',
        'noteOverpaid' => 'Pagó más de lo requerido. Comuníquese con el vendedor para reembolsar la diferencia, ya que el vendedor es el único propietario de esta billetera y tiene todos los fondos.',
        'statusUpdating' => 'Actualización de estado',
        'paymentAccepted' => 'Pago aceptado',
        'paymentExpired' => 'Caducada',
        'created' => 'creada',
        'partpaid' => 'mal pagada',
        'paid' => 'pagada',
        'overpaid' => 'sobre pagada',
        'completed' => 'completa',
        'expired' => 'caducada',
        'item' => 'Artículo',
        'qty' => 'Cantidad',
        'cost' => 'Coste',
        'total' => 'Total',
        'subtotal' => 'Subtotal',
    ],
    'fr' => [
        'title' => 'Facture',
        'from' => 'De',
        'remainsToPay' => 'Reste à payer',
        'paymentAddress' => 'Adresse de paiement',
        'getBack' => 'Vous reviendrez sur le site dans <span id="linkback-counter">%s</span> secondes ou cliquez <a href="%s" id="linkback" class="link hovered">ici</a>',
        'backlink' => '<a href="%s" class="link hovered">Retour au magasin</a>',
        'somethingWentWrong' => 'Quelque chose a mal tourné',
        'invalidInvoiceId' => 'ID de facture non valide',
        'notFound' => 'Introuvable',
        'waitingForPayment' => 'Attente du paiement',
        'notePayment' => "Veuillez payer le montant exact. N'incluez pas les frais de réseau dans le montant. Sinon, les fonds n'arrivent pas entièrement, la facture sera donc considérée comme partiellement payée et vous devrez payer la différence.",
        'noteOverpaid' => 'Vous avez payé plus que nécessaire. Veuillez contacter le vendeur pour rembourser la différence car le vendeur est le seul propriétaire de ce portefeuille et dispose de tous les fonds.',
        'statusUpdating' => 'Mise à jour du statut',
        'paymentAccepted' => 'Paiement accepté',
        'paymentExpired' => 'Expirée',
        'created' => 'créée',
        'partpaid' => 'sous-payée',
        'paid' => 'payée',
        'overpaid' => 'trop payée',
        'completed' => 'complétée',
        'expired' => 'expirée',
        'item' => 'Article',
        'qty' => 'Quantité',
        'cost' => 'Coût',
        'total' => 'Total',
        'subtotal' => 'Subtotal',
    ],
    'de' => [
        'title' => 'Rechnung',
        'from' => 'Von',
        'remainsToPay' => 'Bleibt zu zahlen',
        'paymentAddress' => 'Rechnungsadresse',
        'getBack' => 'Gehen Sie zurück zur Website nach <span id="linkback-counter">%s</span> Sekunden oder klicken <a href="%s" id="linkback" class="link hovered">hier</a>',
        'backlink' => '<a href="%s" class="link hovered">Zurück zum Laden</a>',
        'somethingWentWrong' => 'Etwas ist schief gelaufen',
        'invalidInvoiceId' => 'Ungültige Rechnungs-ID',
        'notFound' => 'Nicht gefunden',
        'waitingForPayment' => 'Warten auf Zahlung',
        'notePayment' => 'Bitte zahlen Sie den genauen Betrag. Berücksichtigen Sie nicht die Netzwerkgebühr im Betrag. Andernfalls geht der Betrag nicht vollständig ein, so dass die Rechnung als teilweise bezahlt gilt und Sie die Differenz begleichen müssen.',
        'noteOverpaid' => 'Sie haben mehr bezahlt als nötig. Bitte wenden Sie sich an den Verkäufer, um die Differenz zu erstatten, da der Verkäufer der einzige Eigentümer dieser Brieftasche ist und über das gesamte Guthaben verfügt.',
        'statusUpdating' => 'Statusaktualisierung',
        'paymentAccepted' => 'Zahlung akzeptiert',
        'paymentExpired' => 'Abgelaufen',
        'created' => 'erstellt',
        'partpaid' => 'teilbezahlt',
        'paid' => 'bezahlt',
        'overpaid' => 'überbezahlt',
        'completed' => 'fertig',
        'expired' => 'abgelaufen',
        'item' => 'Artikel',
        'qty' => 'Menge',
        'cost' => 'Preis',
        'total' => 'Gesamt',
        'subtotal' => 'Zwischensumme',
    ],
    'tr' => [
        'title' => 'Fatura',
        'from' => '',
        'remainsToPay' => 'Ödemek için kalır',
        'paymentAddress' => 'Ödeme adresi',
        'getBack' => 'Siteye geri dön sırasında <span id="linkback-counter">%s</span> saniye veya tıkla <a href="%s" id="linkback" class="link hovered">burada</a>',
        'backlink' => '<a href="%s" class="link hovered">Mağazaya geri dön</a>',
        'somethingWentWrong' => 'Bir şeyler yanlış gitti',
        'invalidInvoiceId' => 'Geçersiz fatura ID',
        'notFound' => 'Bulunamadı',
        'waitingForPayment' => 'Bekleyen ödeme',
        'notePayment' => 'Lütfen tam tutarı ödeyin. Ağ ücretini tutara dahil etmeyin. Aksi takdirde fonlar tam olarak ulaşmaz, bu nedenle fatura kısmen ödenmiş olarak kabul edilir ve aradaki farkı ödemeniz gerekir.',
        'noteOverpaid' => 'Gerekenden daha fazlasını ödediniz. Satıcı bu cüzdanın tek sahibi olduğundan ve tüm paraya sahip olduğundan, aradaki farkı iade etmek için lütfen satıcıyla iletişime geçin.',
        'statusUpdating' => 'Durum güncelleme',
        'paymentAccepted' => 'Ödeme kabul edildi',
        'paymentExpired' => 'Süresi doldu',
        'created' => 'yaratıldı',
        'partpaid' => 'kısmen ödendi',
        'paid' => 'ödendi',
        'overpaid' => 'fazla ödendi',
        'completed' => 'tamamlandı',
        'expired' => 'süresi doldu',
        'item' => 'Isim',
        'qty' => 'Miktar',
        'cost' => 'Fiyat',
        'total' => 'Toplam',
        'subtotal' => 'Ara Toplam',
    ],
];