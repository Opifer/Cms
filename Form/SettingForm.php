<?php

namespace Opifer\CmsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class SettingForm extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($options['data'] as $setting) {
            $name = $setting->getExtension().'_'.str_replace('.', '_', $setting->getName());
            $builder->add($name, $setting->getType(), [
                'label' => ucfirst(str_replace('.', ' ', $setting->getName())),
                'data' => $setting->getValue(),
                'constraints' => $this->getConstraints($setting)
            ]);
        }

        $builder->add('Update settings', 'submit');
    }

    /**
     * Get Constraints
     *
     * @var    Setting  $setting
     *
     * @return array
     */
    public function getConstraints($setting)
    {
        $constraints = array();
        $constraints[] = new NotBlank();

        if (!$min = $setting->getMin()) {
            $min = null;
        }

        if (!$max = $setting->getMax()) {
            $max = null;
        }

        if (($max || $min) && $setting->getType() != 'integer') {
            $constraints[] = new Length(['min' => $min, 'max' => $max]);
        }

        return $constraints;
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => null,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'setting_form';
    }
}
