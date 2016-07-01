# Change caching for a single block

By default we use [HTTP cache](http://symfony.com/doc/current/book/http_cache.html#http-cache-introduction) to cache the complete page until a new `publish` is triggered.

In case a block has dynamic data that should have a different caching behaviour as the rest of the page,
we use [Edge Side Includes](http://symfony.com/doc/current/book/http_cache.html#using-edge-side-includes).

It's fairly simple. Change your block view to the following:

```twig
{% if partial is not defined or partial == false %}
    {{ render_esi(controller('OpiferContentBundle:Frontend/Block:view', {'id': block.id})) }}
{% else %}
    {# Your block view #}
{% endif %}
```

Then, in your `BlockService`, override the `execute` method to add caching headers for that specific block.
For example, if your block should only be cached for 10 seconds, you could use the following:

```php
public function execute(BlockInterface $block, Response $response = null, array $parameters = [])
{
    $partial = (isset($parameters['partial'])) ? $parameters['partial'] : false;

    $response = parent::execute($block, $response, $parameters);

    if ($partial) {
        $response->setMaxAge(10);
        $response->setSharedMaxAge(10);
    }

    return $response;
}
```
