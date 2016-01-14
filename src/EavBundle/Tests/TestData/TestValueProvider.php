<?php

namespace Opifer\EavBundle\Tests\TestData;

use Opifer\EavBundle\ValueProvider\AbstractValueProvider;
use Opifer\EavBundle\ValueProvider\ValueProviderInterface;
use Symfony\Component\Form\FormBuilderInterface;

class TestValueProvider extends AbstractValueProvider implements ValueProviderInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('value');
    }

    public function getEntity()
    {
        return 'My\Entity\TestValue';
    }
}
