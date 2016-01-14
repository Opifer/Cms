<?php

namespace Opifer\CmsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class SettingFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($options['data'] as $setting) {
            $name = $setting->getExtension().'_'.str_replace('.', '_', $setting->getName());
            $builder->add($name, $setting->getType(), [
                'label' => ucfirst(str_replace('.', ' ', $setting->getName())),
                'data' => $setting->getValue(),
                'constraints' => $this->getConstraints($setting),
            ]);
        }
    }

    /**
     * Get Constraints.
     *
     * @var Setting
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
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }

    /**
     * @deprecated
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'setting_form';
    }
}
