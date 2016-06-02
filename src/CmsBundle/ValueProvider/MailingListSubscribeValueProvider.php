<?php

namespace Opifer\CmsBundle\ValueProvider;

use Opifer\CmsBundle\Entity\MailingListSubscribeValue;
use Opifer\EavBundle\ValueProvider\BooleanValueProvider;
use Opifer\EavBundle\ValueProvider\ValueProviderInterface;
use Opifer\MailingListBundle\Form\Type\MailingListsType;
use Symfony\Component\Form\FormBuilderInterface;

class MailingListSubscribeValueProvider extends BooleanValueProvider implements ValueProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildParametersForm(FormBuilderInterface $builder, array $options = null)
    {
        $builder->add('mailingLists', MailingListsType::class, [
            'required' => true,
            'label' => 'label.mailinglist',
            'attr' => ['help_text' => 'help.subscribe_mailinglist'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return MailingListSubscribeValue::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return 'Mailing list subscribe';
    }
}
