<?php

namespace Opifer\FormBlockBundle\Block\Service;

use Opifer\FormBlockBundle\Entity\FormBlock;
use Opifer\ContentBundle\Block\BlockRenderer;
use Opifer\ContentBundle\Block\Service\AbstractBlockService;
use Opifer\ContentBundle\Block\Service\BlockServiceInterface;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Model\BlockInterface;
use Opifer\FormBundle\Model\FormManager;
use Opifer\FormBundle\Model\PostInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Opifer\EavBundle\Manager\EavManager;

/**
 * Form Block Service.
 */
class FormBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    /** @var EavManager */
    protected $eavManager;

    /** @var FormManager */
    protected $formManager;

    /** @var bool {@inheritdoc} */
    protected $esiEnabled = false;

    /**
     * @param BlockRenderer $blockRenderer
     * @param EavManager    $eavManager
     * @param FormManager   $formManager
     * @param array         $config
     */
    public function __construct(BlockRenderer $blockRenderer, EavManager $eavManager, FormManager $formManager, array $config)
    {
        parent::__construct($blockRenderer, $config);

        $this->eavManager = $eavManager;
        $this->formManager = $formManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildManageForm($builder, $options);

        $builder->get('default')->add('form', EntityType::class, [
            'class' => 'OpiferCmsBundle:Form',
            'choice_label' => 'name',
            'label' => 'Form',
            'placeholder' => 'Choose Form',
        ]);

        if (isset($this->config['templates'])) {
            $builder->get('properties')->add('template', ChoiceType::class, [
                'label' => 'label.template',
                'placeholder' => 'placeholder.choice_optional',
                'attr' => ['help_text' => 'help.block_template'],
                'choices' => $this->config['templates'],
                'required' => false,
            ]);
        }
    }

    public function getViewParameters(BlockInterface $block)
    {
        $parameters = [
            'block_service' => $this,
            'block' => $block,
        ];

        if (!empty($block->getForm())) {
            /** @var PostInterface $post */
            $post = $this->eavManager->initializeEntity($parameters['block']->getForm()->getSchema());

            $this->prefillPost($post);

            $form = $this->formManager->createForm($block->getForm(), $post);

            $parameters['block']->formView = $form->createView();
        }

        return $parameters;
    }

    /**
     * Allows setting pre filled data on form fields.
     *
     * @param PostInterface $post
     */
    protected function prefillPost(PostInterface $post)
    {
        // Override in child class
    }

    /**
     * {@inheritdoc}
     */
    public function createBlock()
    {
        return new FormBlock();
    }

    /**
     * {@inheritdoc}
     */
    public function getTool(BlockInterface $block = null)
    {
        $tool = new Tool('Form', 'form');

        $tool->setIcon('receipt')
            ->setGroup('Form')
            ->setDescription('Include a form created in Forms');

        return $tool;
    }
}
