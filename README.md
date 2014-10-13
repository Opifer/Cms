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

        // The mediabundle has be loaded before KnpGaufretteBundle & LiipImagineBundle
        new Opifer\MediaBundle\OpiferMediaBundle(),
        new Knp\Bundle\GaufretteBundle\KnpGaufretteBundle(),
        new Liip\ImagineBundle\LiipImagineBundle()
    );
}
```

Import `config.yml` in your `app\config\config.yml`

```yaml
imports:
    - { resource: parameters.yml }
    - { resource: @OpiferMediaBundle/Resources/config/config.yml }
    - { resource: security.yml }
```

Creating a media provider
-------------------------

All media providers must implement the `Opifer\MediaBundle\Provider\ProviderInterface`.
The easiest way to get started is to extend from `Opifer\MediaBundle\Provider\AbstractProvider`,
which already implements most of the required methods.

To register the provider, create a service. Tag it with `opifer.media.provider` and give it an `alias`.

```yaml
acme.media.provider.youtube:
    class: Opifer\MediaBundle\Provider\YoutubeProvider
    tags:
        - { name: opifer.media.provider, alias: youtube }
```
