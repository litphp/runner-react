Bolt runner powered by ReactPHP
===============================

### Usage

+ Write a php file calling `ReactRunner::run` with your configuration, then run it with php

(see _example)

By default, the listening host + port is read from environment variable `LISTEN`, and fallback to 8080. This can be overridden via configuration

Both `\React\Http\StreamServer` and `\React\Http\Server` is supported, use following configration entry to switch

```php
// C::join(ReactRunner::class, 'server') => C::alias(StreamingServer::class),
C::join(ReactRunner::class, 'server') => C::alias(Server::class),
```
