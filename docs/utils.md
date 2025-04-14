# Utils class

The class contains auxiliary functions such as explorer link generation, QR-code generation, quantity conversion, input sanitization, etc.


|Method|Description|
|---|---|
|```Utils::currency()```|Gets currency object from API|
|```Utils::getTransactionLink()```|Return transaction link to explorer|
|```Utils::getAddressLink()```| Return address link to explorer|
|```Utils::renderQr()```|Return base64 encoded QR-code PNG string|
|```Utils::humanizeAmount()```|Convert to decimal and trim trailing zeros if $zeroTrim set true|
|```Utils::min2cur()```|Convert minor currency value to major|
|```Utils::cur2min()```|Convert major currency value to minor|
|```Utils::fiatTocrypto()```|Convert fiat amount to crypto|
|```Utils::fiatTocryptos()```|Converts fiat amount to crypto for array of cryptos|
|```Utils::isFiatSupported()```|Check is fiat supported by Apirone|
|```Utils::sanitize()```|Sanitize text input to prevent XSS & SQL injection|
|```Utils::sendJson()```|Send JSON response|
