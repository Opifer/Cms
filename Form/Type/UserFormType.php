<?php

namespace Opifer\CmsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserFormType extends AbstractType
{
    /** @var array */
    protected $roles;

    /** @var string */
    protected $userClass;

    /**
     * Constructor.
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
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('email')
            ->add('plainPassword', 'repeated', [
                'type' => 'password',
                'first_options' => ['label' => 'form.password'],
                'second_options' => ['label' => 'form.password_confirmation'],
                'invalid_message' => 'fos_user.password.mismatch',
            ])
            ->add('enabled', 'choice', [
                'choices' => [true => 'Enable', false => 'Disable'],
                'data' => true,
            ])
            ->add('roles', 'choice', [
                'multiple' => true,
                'choices' => $this->flattenRoles($this->roles),
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => $this->userClass,
        ]);
    }

    /**
     * Flatten roles.
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
     * {@inheritdoc}
     *
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
        return 'user_form';
    }
}
