<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\ContentBundle\Entity\FormBlock;
use Opifer\ContentBundle\Block\Service\AbstractBlockService;
use Opifer\ContentBundle\Block\Service\BlockServiceInterface;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Model\BlockInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Opifer\CmsBundle\Entity\Form;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Opifer\EavBundle\Manager\EavManager;
use Opifer\FormBundle\Model\FormInterface;
use Opifer\FormBundle\Form\Type\PostType;

/**
 * Form Block Service
 */
class FormBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{

    /**
     * @param EngineInterface $templating
     * @param ContainerInterface $container
     * @param EavManager $eavManager
     * @param array $config
     */
    public function __construct(EngineInterface $templating, Container $container, EavManager $eavManager, array $config)
    {
        $this->templating = $templating;
        $this->config = $config;
        $this->container = $container;
        $this->eavManager = $eavManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildManageForm($builder, $options);

        $builder->add(
            $builder->create('default', FormType::class, ['virtual' => true])
                ->add('form', EntityType::class, [
                    'class' => 'OpiferCmsBundle:Form',
                    'choice_label' => 'name',
                    'label' => 'Form',
                    'placeholder' => 'Choose Form'
                ])
        );
    }

    public function getViewParameters(BlockInterface $block)
    {
        $parameters = [
            'block_service' => $this,
            'block'         => $block,
        ];

        if (!empty($parameters['block']->getForm())) {
            $post = $this->eavManager->initializeEntity($parameters['block']->getForm()->getSchema());

            $form = $this->container->get('form.factory')->create(PostType::class, $post, ['form_id' => $parameters['block']->getForm()->getId()]);

            $parameters['block']->formView = $form->createView();
        }

        return $parameters;
    }


    /**
     * {@inheritDoc}
     */
    public function createBlock()
    {
        return new FormBlock;
    }

    /**
     * {@inheritDoc}
     */
    public function getTool(BlockInterface $block = null)
    {
        $tool = new Tool('Form', 'form');

        $tool->setIcon('receipt')
            ->setDescription('Adds form');

        return $tool;
    }
}
