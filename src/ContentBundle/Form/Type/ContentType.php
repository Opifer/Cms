<?php

namespace Opifer\ContentBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Opifer\ContentBundle\Form\DataTransformer\SlugTransformer;
use Opifer\EavBundle\Form\Type\ValueSetType;

/**
 * Content Form Type
 */
class ContentType extends AbstractType
{
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
                    'help_text' => 'content.form.template.help_text'
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
            ->add('parent', ContentParentType::class, [
                'class' => 'Opifer\CmsBundle\Entity\Content',
                'choice_label' => 'title',
                'required' => false,
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

        // Only add the ValueSetType if a ContentType is set, to avoid persisting empty valuesets.
        if ($options['data']->getValueSet()) {
            $builder->add('valueset', ValueSetType::class);
        }
    }

    /**
     * @deprecated
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * {@inheritDoc}
     */
    public function getBlockPrefix()
    {
        return 'opifer_content_details';
    }
}
