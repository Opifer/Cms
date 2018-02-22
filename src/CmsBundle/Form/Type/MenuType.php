<?php

namespace Opifer\CmsBundle\Form\Type;

use Opifer\CmsBundle\Entity\MenuItem;
use Opifer\ContentBundle\Form\Type\ContentPickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class MenuType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('parent', EntityType::class, [
                'class' => 'Opifer\CmsBundle\Entity\Menu',
                'query_builder' => function ($qb) {
                    return $qb->createQueryBuilder('m')
                        ->orderBy('m.root', 'ASC')
                        ->addOrderBy('m.lft', 'ASC');
                },
                'property' => 'indentedName',
                'required' => false,
                'empty_value' => '(empty)',
                'empty_data' => null,
                'attr' => ['help_text' => 'menu.form.parent.help_text'],
            ])
            ->add('name', TextType::class, [
                'attr' => ['help_text' => 'menu.form.name.help_text'],
            ])
        ;

        if ($options['data'] instanceof MenuItem) {
            $builder
                ->add('content', ContentPickerType::class)
                ->add('link', TextType::class, [
                    'attr' => ['help_text' => 'menu.form.link.help_text'],
                ])
                ->add('hiddenMobile', CheckboxType::class, [
                    'label_attr' => ['class' => 'col-lg-offset-2'],
                ])
                ->add('hiddenTabletPortrait', CheckboxType::class, [
                    'label_attr' => ['class' => 'col-lg-offset-2'],
                ])
                ->add('hiddenTabletLandscape', CheckboxType::class, [
                    'label_attr' => ['class' => 'col-lg-offset-2'],
                ])
                ->add('hiddenDesktop', CheckboxType::class, [
                    'label_attr' => ['class' => 'col-lg-offset-2'],
                ])
            ;

            $builder->add(
                $builder->create('parameters', 'admin_menu_parameters', [
                    'label' => false,
                    'attr' => [
                        'align_with_widget' => true,
                        'widget_col' => 6,
                    ],
                ])
            );
        }

        $builder->add('sort', IntegerType::class, [
            'attr' => [
                'help_text' => 'menu.form.sort.help_text',
                'widget_col' => 4,
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'menu_form';
    }
}
