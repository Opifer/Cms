<?php

namespace Opifer\CmsBundle\Form\Type;

use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Opifer\CmsBundle\Entity\MenuItem;

class MenuType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * Constructor
     *
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('parent', 'entity', [
                'class'       => 'Opifer\CmsBundle\Entity\Menu',
                'query_builder' => function ($er) {
                    return $er->createQueryBuilder('m')
                        ->orderBy('m.root', 'ASC')
                        ->addOrderBy('m.lft', 'ASC');
                },
                'property'    => 'indentedName',
                'required'    => false,
                'empty_value' => $this->translator->trans('(empty)'),
                'empty_data'  => null,
                'attr'        => ['help_text' => $this->translator->trans('menu.form.parent.help_text')]
            ])
            ->add('name', 'text', [
                'attr' => ['help_text' => $this->translator->trans('menu.form.name.help_text')]
            ])
        ;

        if ($options['data'] instanceof MenuItem) {
            $builder
                ->add('content', 'contentpicker')
                ->add('link', 'text', [
                    'attr' => ['help_text' => $this->translator->trans('menu.form.link.help_text')]
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
            'attr'        => [
                'help_text' => $this->translator->trans('menu.form.sort.help_text'),
                'widget_col' => 4,
            ]
        ]);

        $builder->add('save', 'submit', [
            'label' => $this->translator->trans('button.submit')
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'admin_menu';
    }
}
