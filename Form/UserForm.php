<?php

namespace Opifer\CmsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserForm extends AbstractType
{
    /** @var array */
    protected $roles;

    /** @var string */
    protected $userClass;

    /**
     * Constructor
     *
     * @param array  $roles
     * @param string $userClass
     */
    public function __construct(array $roles, $userClass)
    {
        $this->roles = $roles;
        $this->userClass = $userClass;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('email')
            ->add('plainPassword', 'repeated', array(
                'type' => 'password',
                'first_options' => ['label' => 'form.password'],
                'second_options' => ['label' => 'form.password_confirmation'],
                'invalid_message' => 'fos_user.password.mismatch',
            ))
            ->add('enabled', 'choice', [
                'choices' => [true => 'Enable', false => 'Disable'],
                'data' => true
            ])
            ->add('roles', 'choice', [
                'multiple' => true,
                'choices' => $this->flattenRoles($this->roles)
            ])
            ->add('save', 'submit');
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->userClass,
        ));
    }

    /**
     * Flatten roles
     *
     * @param array $data
     *
     * @return array
     */
    public function flattenRoles($data)
    {
        $result = array();
        foreach ($data as $key => $value) {
            if (substr($key, 0, 4) === 'ROLE') {
                $result[$key] = $key;
            }
            if (is_array($value)) {
                $tmpresult = $this->flattenRoles($value);
                if (count($tmpresult) > 0) {
                    $result = array_merge($result, $tmpresult);
                }
            } else {
                $result[$value] = $value;
            }
        }

        return array_unique($result);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'user_form';
    }
}
