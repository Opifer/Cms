<?php

namespace Opifer\ContentBundle\Form\Type;

use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

use Opifer\EavBundle\Form\Type\ValueSetType;
use Opifer\ContentBundle\Form\DataTransformer\SlugTransformer;

class ContentType extends AbstractType
{
    /** @var Symfony\Bundle\FrameworkBundle\Translation\Translator */
    protected $translator;

    /** @var string */
    protected $directoryClass;
    
    /** @var string */
    protected $contentClass;

    /**
     * Constructor
     *
     * @param Translator $translator
     */
    public function __construct(TranslatorInterface $translator, $directoryClass, $contentClass)
    {
        $this->translator = $translator;
        $this->directoryClass = $directoryClass;
        $this->contentClass = $contentClass;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $content = $builder->getData();
        
        $transformer = new SlugTransformer();
        
        // Add the default form fields
        $builder
            ->add('title', 'text', [
                'label' => $this->translator->trans('form.title'),
                'attr'  => [
                    'placeholder' => $this->translator->trans('content.form.title.placeholder')
                ]
            ])
            ->add('description', 'text', [
                'label' => $this->translator->trans('form.description'),
                'attr'  => [
                    'placeholder' => $this->translator->trans('content.form.description.placeholder')
                ]
            ])
            ->add(
                $builder->create(
                    'slug', 'text', [
                    'attr' => [
                        'placeholder' => $this->translator->trans('content.form.slug.placeholder'),
                        'help_text'   => $this->translator->trans('form.slug.help_text'),

                    ]]
                )->addViewTransformer($transformer)
            )
            ->add('directory', 'entity', [
                'class'       => $this->directoryClass,
                'property'    => 'name',
                'empty_value' => '/',
                'required'    => false,
                'empty_data'  => null,
                'attr'        => [
                    'help_text' => $this->translator->trans('content.form.directory.help_text')
                ]
            ])
            ->add('aliasOf', 'entity', [
                'class'       => $this->contentClass,
                'query_builder' => function(EntityRepository $er) use ($content) {
                    $return = $er->createQueryBuilder('u');
                    
                    if($content->getId()) {
                        $return->where('u.id != :id')
                            ->setParameter('id', $content->getId());
                    }
                    
                    return $return;
                },
                'property'    => 'title',
                'empty_value' => 'none',
                'required'    => false,
                'empty_data'  => null,
            ])
            ->add('active', 'checkbox')
        ;
        
        if(!$content->getAliasOf()) {
            $builder->add('valueset', 'opifer_valueset');
        }

        // Add advanced fields only on the advanced option page.
        if ($options['mode'] == 'advanced') {
            $builder->add('realPresentation', 'presentationeditor', [
                'label' => $this->translator->trans('form.presentation'),
                'attr'  => ['align_with_widget' => true]
            ]);
        }

        $builder->add('save', 'submit', [
            'label' => $this->translator->trans('content.form.submit')
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'mode' => 'simple',
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'opifer_content';
    }
}
