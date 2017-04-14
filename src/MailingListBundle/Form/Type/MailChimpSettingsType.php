<?php

namespace Opifer\MailingListBundle\Form\Type;

use Opifer\CmsBundle\Form\Type\ConfigurationFormTypeInterface;
use Opifer\MailingListBundle\Provider\MailChimpProvider;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Settings Type
 */
class MailChimpSettingsType extends AbstractType implements ConfigurationFormTypeInterface
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(MailChimpProvider::API_KEY_SETTING, TextType::class, [
                'label' => 'label.mailchimp_api_key',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public static function getFields()
    {
        return [
            MailChimpProvider::API_KEY_SETTING
        ];
    }

    public function getLabel()
    {
        return 'Mailing lists';
    }
}
