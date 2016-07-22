# Change caching for a single block

By default we use [HTTP caching](http://symfony.com/doc/current/book/http_cache.html#http-cache-introduction) to cache the complete page until a new `publish` is triggered.

In case a block has dynamic data that should have a different caching behaviour as the rest of the page,
we use [Edge Side Includes](http://symfony.com/doc/current/book/http_cache.html#using-edge-side-includes).

To enable this for your specific block, simply set the protected `esiEnabled` property to `true`:

```php
class MyBlockService extends AbstractBlockService implements BlockServiceInterface
{
    protected $esiEnabled = true;
    
    //...
}
```

The above example will avoid caching the complete block, since a default `Response` object will be created.
To adjust the http cache settings for a specific block, you can override `setResponseHeaders` to define your own.

For example, to set the `Cache-Control` header to cache the block for an hour:

```php
class MyBlockService extends AbstractBlockService implements BlockServiceInterface
{
    protected $esiEnabled = true;
    
    //...
    
    protected function setResponseHeaders(BlockInterface $block, Response $response)
    {
        $response->setSharedMaxAge(3600);
    }
}
```
