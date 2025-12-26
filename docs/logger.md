# Logger

A logger is a wrapper class that uses a callback function to interact with your log.
The callback function receives the prepared data and writes it to the log.

Also you can set `PSR/LOG` or own implementation of `Psr\Log\LoggerInterface` as logger handler.

|Method|Description|
|:---:|---|
|`set()`|Set logger or callback function.|
|`log()`|Logs with an arbitrary level.|
|`emergency()`|System is unusable.|
|`alert()`|Action must be taken immediately.|
|`critical()`|Critical conditions.|
|`error()`|Runtime errors that do not require immediate action|
|`warning()`|Exceptional occurrences that are not errors.|
|`notice()`|Normal but significant events.|
|`info()`|Interesting events.|
|`debug()`|Detailed debug information.|
