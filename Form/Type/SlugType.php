<?php
namespace Opifer\CmsBundle\Form\Type;

use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Opifer\CmsBundle\Form\DataTransformer\SlugTransformer;

/**
 * Slug form type
 */
class SlugType extends AbstractType
{
    /**
     * @var  Symfony\Bundle\FrameworkBundle\Translation\Translator
     */
    protected $translator;

    /**
     * Constructor
     *
     * @param Translator $translator
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
        $transformer = new SlugTransformer();
        $builder->addModelTransformer($transformer);
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'label' => $this->translator->trans('form.slug.label'),
            'attr'  => [
                'help_text' => $this->translator->trans('form.slug.help_text'),
            ],
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return 'text';
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'slug';
    }
}
