<?php

namespace Opifer\ContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Opifer\ContentBundle\Form\DataTransformer\SlugTransformer;
use Opifer\ContentBundle\Form\DataTransformer\IdToContentTransformer;

/**
 * Class ContentDetailsType
 *
 * @package Opifer\ContentBundle\Form\Type
 */
class ContentDetailsType extends AbstractType
{

    /** @var string */
    protected $directoryClass;

    /** @var object */
    protected $contentManager;

    /**
     * Constructor
     *
     * @param string $directoryClass
     * @param object $contentManager
     */
    public function __construct($directoryClass, $contentManager)
    {
        $this->directoryClass = $directoryClass;
        $this->contentManager = $contentManager;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new SlugTransformer();

        // Add the default form fields
        $builder
            ->add('template', 'entity', [
                'class'    => 'OpiferContentBundle:Template',
                'property' => 'displayName',
                'attr'     => [
                    'help_text' => 'label.help.template'
                ],
//                'query_builder' => function(EntityRepository $repository) {
//                    return $repository->createQueryBuilder('c')
//                        ->where('c.objectClass = :objectClass')
//                        ->setParameter('objectClass', $this->contentClass)
//                        ->orderBy('c.displayName', 'ASC');
//                }
            ])
            ->add('title', 'text', [
                'label' => 'form.title',
                'attr'  => [
                    'placeholder' => 'content.form.title.placeholder',
                    'help_text'   => 'content.form.title.help_text',
                ]
            ])
            ->add('description', 'text', [
                'label' => 'form.description',
                'attr'  => [
                    'placeholder' => 'content.form.description.placeholder',
                    'help_text'   => 'content.form.description.help_text',
                ]
            ])
            ->add(
                $builder->create(
                    'slug', 'text', [
                        'attr' => [
                            'placeholder' => 'content.form.slug.placeholder',
                            'help_text'   => 'form.slug.help_text',

                        ]]
                )->addViewTransformer($transformer)
            )
            ->add('directory', 'entity', [
                'class'       => $this->directoryClass,
                'query_builder' => function($er) {
                    return $er->createQueryBuilder('d')
                        ->orderBy('d.root', 'ASC')
                        ->addOrderBy('d.lft', 'ASC');
                },
                'property'    => 'slug',
                'empty_value' => '/',
                'required'    => false,
                'empty_data'  => null,
                'attr'        => [
                    'help_text' => 'content.form.directory.help_text',
                ]
            ])
            ->add('alias', 'text', [
                'attr'        => [
                    'help_text' => 'content.form.alias.help_text',
                ]
            ])
            ->add('active', 'checkbox', [
                'attr' => ['align_with_widget' => true],
            ])
        ;

        $builder->add('save', 'submit', [
            'label' => 'button.submit',
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'opifer_content_details';
    }
}
