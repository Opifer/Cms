[![Build Status](https://travis-ci.org/Opifer/RedirectBundle.svg)](https://travis-ci.org/Opifer/RedirectBundle)

RedirectBundle
==============

Symfony Bundle to handle dynamic page redirects. Based on the [KunstmaanRedirectBundle](https://github.com/Kunstmaan/KunstmaanRedirectBundle),
but refactored to require less dependencies and ease the overriding of functionality.

Installation
------------

Add OpiferRedirectBundle to your composer.json

```
$ composer require opifer/redirect-bundle "~0.1"
```

And enable the bundle in `app/AppKernel.php`

```php

public function registerBundles()
{
    $bundles = [
        // ...
        new Opifer\RedirectBundle\OpiferRedirectBundle(),
    ];
}
```

Add the Redirect entity

```php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\RedirectBundle\Model\Redirect as BaseRedirect;

/**
 * @ORM\Entity()
 * @ORM\Table(name="redirect")
 */
class Redirect extends BaseRedirect
{
    
}

```

And define it in your config.yml:

```yml
opifer_redirect:
    redirect:
        class: AppBundle\Entity\Redirect
```

Optionally add the routing for the RedirectController:

```yml
opifer_redirect:
    resource: "@OpiferContentBundle/Resources/config/routing.yml"
    prefix: /admin/redirects
```

Add the `RedirectRouter` to your chain router. For example, when you're using [CMFRoutingBundle](https://github.com/symfony-cmf/RoutingBundle), 
add the `opifer.redirect.redirect_router` to the `cmf_routing` config.

```yml
cmf_routing:
    chain:
        routers_by_id:
            opifer.redirect.redirect_router: 200
            router.default: 100
```

Configuration reference
-----------------------

```yml
opifer_redirect:
    redirect:
        class: ~
        manager: opifer.redirect.redirect_manager.default
        view:
            index: OpiferRedirectBundle:Redirect:index.html.twig
            create: OpiferRedirectBundle:Redirect:create.html.twig
            edit: OpiferRedirectBundle:Redirect:edit.html.twig
```
