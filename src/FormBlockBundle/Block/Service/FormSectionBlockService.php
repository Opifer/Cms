<?php

namespace Opifer\FormBlockBundle\Block\Service;

use Opifer\FormBlockBundle\Entity\FormSectionBlock;
use Opifer\ContentBundle\Block\BlockRenderer;
use Opifer\ContentBundle\Block\Service\AbstractBlockService;
use Opifer\ContentBundle\Block\Service\BlockServiceInterface;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Model\BlockInterface;
use Opifer\FormBundle\Model\FormManager;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

/**
 * Form Block Service.
 */
class FormSectionBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    /** @var FormManager */
    protected $formManager;

    /** @var bool {@inheritdoc} */
    protected $esiEnabled = true;

    /**
     * @param BlockRenderer $blockRenderer
     * @param FormManager   $formManager
     * @param array         $config
     */
    public function __construct(BlockRenderer $blockRenderer, FormManager $formManager, array $config)
    {
        parent::__construct($blockRenderer, $config);

        $this->formManager = $formManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildManageForm($builder, $options);

        $propertiesForm = $builder->create('properties', FormType::class, ['label' => false, 'attr' => ['widget_col' => 12]]);

        $propertiesForm
            ->add('name', TextType::class)
            ->add('navTitle', TextType::class)
            ->add('description', TextareaType::class)
        ;

        if (isset($this->config['templates'])) {
            //$propertiesForm->add('template', ChoiceType::class, [
            //    'label' => 'label.template',
            //    'placeholder' => 'placeholder.choice_optional',
            //    'attr' => ['help_text' => 'help.block_template'],
            //    'choices' => $this->config['templates'],
            //    'required' => false,
            //]);
        }

        $builder->add(
            $builder->create('default', FormType::class, ['virtual' => true])
                ->add($propertiesForm)
        );
    }

    public function getViewParameters(BlockInterface $block)
    {
        $parameters = [
            'block_service' => $this,
            'block' => $block,
        ];

        return $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function createBlock()
    {
        return new FormSectionBlock();
    }

    /**
     * {@inheritdoc}
     */
    public function getTool(BlockInterface $block = null)
    {
        $tool = new Tool('Form Section', 'form_section');

        $tool->setIcon('input')
            ->setGroup('Form')
            ->setDescription('Include a form field');

        return $tool;
    }
}
