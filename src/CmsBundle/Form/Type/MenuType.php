<?php

namespace Opifer\CmsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Opifer\CmsBundle\Entity\MenuItem;

class MenuType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('parent', 'entity', [
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
            ->add('name', 'text', [
                'attr' => ['help_text' => 'menu.form.name.help_text'],
            ])
        ;

        if ($options['data'] instanceof MenuItem) {
            $builder
                ->add('content', 'contentpicker')
                ->add('link', 'text', [
                    'attr' => ['help_text' => 'menu.form.link.help_text'],
                ])
                ->add('hiddenMobile', 'checkbox', [
                    'label_attr' => ['class' => 'col-lg-offset-2'],
                ])
                ->add('hiddenTabletPortrait', 'checkbox', [
                    'label_attr' => ['class' => 'col-lg-offset-2'],
                ])
                ->add('hiddenTabletLandscape', 'checkbox', [
                    'label_attr' => ['class' => 'col-lg-offset-2'],
                ])
                ->add('hiddenDesktop', 'checkbox', [
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

        $builder->add('sort', 'integer', [
            'attr' => [
                'help_text' => 'menu.form.sort.help_text',
                'widget_col' => 4,
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'menu_form';
    }
}
