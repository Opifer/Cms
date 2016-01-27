[![Build Status](https://travis-ci.org/Opifer/MediaBundle.svg)](https://travis-ci.org/Opifer/MediaBundle)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/7bab65ce-147b-4148-90b2-81ea8454ebf0/mini.png)](https://insight.sensiolabs.com/projects/7bab65ce-147b-4148-90b2-81ea8454ebf0)

MediaBundle
===========

A Symfony Media Manager.
Inspired by SonataMediaBundle's use of Media Providers to add different Media types. 

Note: This bundle is still very much a work in progress, so BC-breaks will happen until the first stable release.

Installation
------------

Install `FOSJsRoutingBundle` according to it's [documentation](https://github.com/FriendsOfSymfony/FOSJsRoutingBundle/blob/master/Resources/doc/index.md)

Add the bundle to your `composer.json`

    composer require opifer/media-bundle dev-master

Register the bundle and its dependencies in `app/AppKernel.php`

```php
public function registerBundles()
{
    $bundles = array(
        // ...
        new JMS\SerializerBundle\JMSSerializerBundle(),
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
 * @ORM\Entity(repositoryClass="Opifer\MediaBundle\Model\MediaRepository")
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

Using the media manager
-----------------------

This bundle comes with an AngularJS media manager included. To use it, you'll need
to include some necessary javascript files and CSS files into your templates.

First, make sure you installed all asset dependencies. You can download them manually, copy the `bower.json` file to your own bundle and run `bower install` or copy the `bower.json` content to your own bower dependencies.

Then, add the dependencies to your templates.

```twig
{% stylesheets
    'bundles/opifermedia/css/dropzone.less'
    'bundles/opifermedia/css/main.less'
    
    filter='less,cssrewrite' %}
    <link rel="stylesheet" href="{{ asset_url }}" />
{% endstylesheets %}

...

{% javascripts
    '@AppBundle/Resources/public/components/ng-file-upload/angular-file-upload-shim.min.js'
    '@AppBundle/Resources/public/components/angular/angular.js'
    '@AppBundle/Resources/public/components/angular-route/angular-route.js'
    '@AppBundle/Resources/public/components/angular-resource/angular-resource.js'
    '@AppBundle/Resources/public/components/ngInfiniteScroll/build/ng-infinite-scroll.js'
    '@AppBundle/Resources/public/components/ng-file-upload/angular-file-upload.min.js'

    '@OpiferMediaBundle/Resources/public/js/dropzone.js'
    '@OpiferMediaBundle/Resources/public/app/modal/modal.js'
    '@OpiferMediaBundle/Resources/public/app/medialibrary/medialibrary.js'
    
    'bundles/fosjsrouting/js/router.js' %}
    <script type="text/javascript" src="{{ asset_url }}"></script>
{% endjavascripts %}
```

Then, create an Angular module that requires the following modules:

```js
'use strict';

angular.module('App', [
    'ngRoute',
    'ngResource',
    'mediaLibrary',
    'angularFileUpload',
]);
```

Make sure you add the angular module in your template by adding the the file to your `{% javascripts %}` list.
And initialize the Angular `App` in your template:

```html
<html ng-app="App">
```

To make the mediamanager accessible in the browser, add the routes to your `routing.yml`:

```yaml
opifer_media:
    resource: "@OpiferMediaBundle/Resources/config/routing.yml"
    prefix:   /admin

_liip_imagine:
    resource: "@LiipImagineBundle/Resources/config/routing.xml"
    options:
        expose: true
```

To use the mediamanager in your own layout, override `OpiferMediaBundle::base.html.twig`:

```twig
{# app/Resources/OpiferMediaBundle/views/base.html.twig #}
{% extends 'base.html.twig' %}

{% block body %}
	{% block opifer_media_body %}{% endblock %}
{% endblock %}

{% block javascripts %}
    {{ parent() }}

    {% block opifer_media_javascripts %}{% endblock %}
{% endblock %}

```

Adding a mediapicker to a form
------------------------------

Create a relationship between the media entity and any other entity. For example, Users must be able to add media to a Content item.

```php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\MediaBundle\Model\MediaInterface;

class Content
{
    /**
     * @var Media
     *
     * @ORM\ManyToOne(targetEntity="Opifer\MediaBundle\Model\MediaInterface")
     * @ORM\JoinColumn(name="media_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $image;
    
    /**
     * Set image
     *
     * @param string $image
     *
     * @return Content
     */
    public function setImage(MediaInterface $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return MediaInterface
     */
    public function getImage()
    {
        return $this->image;
    }
}
```

In your content FormType, add the `mediapicker` form type:

```php
namespace AppBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Opifer\MediaBundle\Form\Type\MediaPickerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ContentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // ...
            ->add('image', MediaPickerType::class, [
                'multiple' => false,
            ])
        ;
    }
}
```


Documentation
-------------

- [Configuration reference](Resources/doc/configuration-reference.md)
- [Media providers](Resources/doc/providers.md)
