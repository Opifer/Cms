Media Providers
===============

This bundle (currently) ships with two media providers. An ImageProvider and a 
YoutubeProvider.

To create your own custom provider it must implement `Opifer\MediaBundle\Provider\ProviderInterface`,
but the easiest way to get started is to extend from `Opifer\MediaBundle\Provider\AbstractProvider`,
which already implements this interface.

```php
namespace AppBundle\MediaProvider;

use Opifer\MediaBundle\Provider\AbstractProvider;

class VimeoProvider extends AbstractProvider
{
    // Override any of the methods inside AbstractProvider, to fit your needs.
}
```

To use the provider, register it as a service and tag it with `opifer.media.provider`.

```yaml
app.vimeo_provider:
    class: AppBundle\MediaProvider\VimeoProvider
    tags:
        - { name: opifer.media.provider, alias: vimeo }
```

[Return to the index](../../README.md)
