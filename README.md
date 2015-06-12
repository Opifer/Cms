[![Build Status](https://travis-ci.org/Opifer/CmsBundle.svg)](https://travis-ci.org/Opifer/CmsBundle)

Opifer CmsBundle
================

Installation
------------

Add OpiferCmsBundle to your composer.json:

    $ composer require opifer/cms-bundle "~0.1"

To avoid enabling all required bundles, extend `Opifer\CmsBundle\Kernel\Kernel` in `app/AppKernel.php`:

```php
use Opifer\CmsBundle\Kernel\Kernel;

class AppKernel extends Kernel
{
    /**
     * Register bundles
     *
     * @return array
     */
    public function registerBundles()
    {
        $bundles = [
            // Add the bundles for your own application here
            new AppBundle\AppBundle(),
        ];

        // The parent bundles array must be passed as the first parameter, cause
        // our CmsBundle holds all required config.
        return array_merge(parent::registerBundles(), $bundles);
    }
}

```

To avoid defining all configuration yourself, import the config files from the CmsBundle:

```yaml
# app/config/config.yml
imports:
    - { resource: parameters.yml }
    - { resource: @OpiferCmsBundle/Resources/config/security.yml }
    - { resource: @OpiferCmsBundle/Resources/config/config.yml }

# app/config/config_dev/yml
imports:
    - { resource: config.yml }
    - { resource: @OpiferCmsBundle/Resources/config/config_dev.yml }

# app/config/config_prod.yml
imports:
    - { resource: config.yml }
    - { resource: @OpiferCmsBundle/Resources/config/config_prod.yml }

```

Same goes for the routing:

```yaml
# app/config/routing.yml
opifer_cms:
    resource: "@OpiferCmsBundle/Resources/config/routing/routing.yml"

```

Update your database schema:

    $ php app/console doctrine:schema:create
    
And create a user account:

    $ php app/console fos:user:create --super-admin
    
Now log into the admin panel at `http://localhost/app_dev.php/admin`.
