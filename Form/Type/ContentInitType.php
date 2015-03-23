<?php

namespace Opifer\ContentBundle\Form\Type;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Routing\RouterInterface;

class ContentInitType extends AbstractType
{
    /** @var  TranslatorInterface */
    protected $translator;

    /** @var \Symfony\Component\Routing\RouterInterface */
    protected $router;

    /** @var string */
    protected $templateClass;

    /** @var string */
    protected $contentClass;

    /**
     * Constructor
     *
     * @param TranslatorInterface $translator
     * @param RouterInterface     $router
     * @param string              $templateClass
     * @param string              $contentClass
     */
    public function __construct(TranslatorInterface $translator, RouterInterface $router, $templateClass, $contentClass)
    {
        $this->translator = $translator;
        $this->router = $router;
        $this->templateClass = $templateClass;
        $this->contentClass = $contentClass;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('template', 'entity', [
                'class'    => $this->templateClass,
                'property' => 'displayName',
                'attr'     => [
                    'help_text' => $this->translator->trans('content.form.template.help_text')
                ],
                'query_builder' => function(EntityRepository $repository) {
                    return $repository->createQueryBuilder('c')
                        ->where('c.objectClass = :objectClass')
                        ->setParameter('objectClass', $this->contentClass)
                        ->orderBy('c.displayName', 'ASC');
                }
            ])
            ->add('save', 'submit', [
                'label' => ucfirst($this->translator->trans('content.form.init.submit'))
            ])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'opifer_content_init';
    }
}
