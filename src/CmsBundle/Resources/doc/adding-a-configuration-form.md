Adding a configuration form
===========================

1. Create a custom form type class
----------------------------------

Make sure it implements `Opifer\CmsBundle\Form\Type\ConfigurationFormTypeInterface`

```php
<?php

namespace AppBundle\Form\Type;

use Opifer\CmsBundle\Form\Type\ConfigurationFormTypeInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

class SettingsType extends AbstractType implements ConfigurationFormTypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('key', TextType::class)
            ->add('anotherKey', IntegerType::class)
        ;
    }

    public function getLabel()
    {
        return 'General';
    }
}
```

2. Register the service
-----------------------

Add the `opifer.configuration_form` tag.

```yml
# app/config/services.yml
settings_type:
    class: AppBundle\Form\Type\SettingsType
    tags:
        - { name: opifer.configuration_form }
```
