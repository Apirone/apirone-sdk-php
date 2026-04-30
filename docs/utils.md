# Utils class

The class contains auxiliary functions such as explorer link generation, quantity conversion, input sanitization, etc.

|Method|Description|
|---|---|
|```Utils::loadCoins()```|Loads currencies from the API and returns them as an array of coins.|
|```Utils::getCoin()```|Get currency parameters by abbreviation.|
|```Utils::getCoins()```|Return array of coins from production.|
|```Utils::getExplorerHref()```|Returns the explorer href based on the currency abbr and hash type|
|```Utils::getTransactionLink()```|Return transaction link to explorer|
|```Utils::getAddressLink()```|Return address link to explorer|
|```Utils::getAlias()```|Make currency alias by abbr & name|
|```Utils::getNetworkAndToken()```|Determine currency network & token by abbr|
|```Utils::estimate()```|Calculates estimation|
|```Utils::humanizeAmount()```|Convert to decimal and trim trailing zeros if $zeroTrim set true|
|```Utils::min2cur()```|Convert minor currency value to major|
|```Utils::cur2min()```|Convert major currency value to minor|
|```Utils::isFiatSupported()```|Check is fiat supported by Apirone|
|```Utils::isStableCoin()```|Returns whether the currency is a stablecoin|
|```Utils::isTestnet()```|Checks if a cryptocurrency is a testnet|
|```Utils::sanitize()```|Sanitize text input to prevent XSS & SQL injection|
|```Utils::sendJson()```|Send JSON response|
|```Utils::sendException()```|Send exception message & code as JSON|
