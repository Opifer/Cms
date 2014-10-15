[![Build Status](https://travis-ci.org/Opifer/MediaBundle.svg)](https://travis-ci.org/Opifer/MediaBundle)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/7bab65ce-147b-4148-90b2-81ea8454ebf0/mini.png)](https://insight.sensiolabs.com/projects/7bab65ce-147b-4148-90b2-81ea8454ebf0)

MediaBundle
===========

A Symfony Media Manager.
Inspired by SonataMediaBundle's use of Media Providers to add different Media types. 

Note: This bundle is still very much a work in progress, so BC-breaks will happen until the first stable release.

Installation
------------

Add the bundle to your `composer.json`

    composer require opifer/media-bundle dev-master

Register the bundle in `app/AppKernel.php`

```php
public function registerBundles()
{
    $bundles = array(
        ...
        new JMS\SerializerBundle\JMSSerializerBundle($this),
        new Knp\Bundle\GaufretteBundle\KnpGaufretteBundle(),
        new Liip\ImagineBundle\LiipImagineBundle(),
        new Opifer\MediaBundle\OpiferMediaBundle()
    );
}
```

You should create your own Media entity that extends `Opifer\MediaBundle\Model\Media`.

```php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\MediaBundle\Model\Media as BaseMedia;

/**
 * @ORM\Table(name="media")
 * @ORM\Entity(repositoryClass="Opifer\MediaBundle\Repository\MediaRepository")
 */
class Media extends BaseMedia
{
    // Add custom functionality...
}
```

And reference to it in your `app/config/config.yml`

```yaml
opifer_media:
    media_class: AppBundle\Entity\Media
```

Documentation
-------------

- [Configuration reference](Resources/doc/configuration-reference.md)
- [Media providers](Resources/doc/providers.md)
