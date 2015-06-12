<?php

namespace Opifer\CmsBundle\ValueProvider;

use Symfony\Component\Form\FormBuilderInterface;
use Opifer\EavBundle\ValueProvider\AbstractValueProvider;
use Opifer\EavBundle\ValueProvider\ValueProviderInterface;

/**
 * Address Value Provider
 *
 * @author Rick van Laarhoven <r.vanlaarhoven@opifer.nl>
 */
class AddressValueProvider extends AbstractValueProvider implements ValueProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('address', 'google_address', [
            'label' => $options['attribute']->getDisplayName(),
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getEntity()
    {
        return 'Opifer\CmsBundle\Entity\AddressValue';
    }
}
