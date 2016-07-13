<?php

namespace Opifer\CmsBundle\Block\Service;

use Opifer\CmsBundle\Entity\FormBlock;
use Opifer\ContentBundle\Block\Service\AbstractBlockService;
use Opifer\ContentBundle\Block\Service\BlockServiceInterface;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Model\BlockInterface;
use Opifer\FormBundle\Model\FormManager;
use Opifer\FormBundle\Model\PostInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Opifer\EavBundle\Manager\EavManager;

/**
 * Form Block Service.
 */
class FormBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    protected $eavManager;

    protected $formManager;

    /**
     * @param EngineInterface $templating
     * @param EavManager      $eavManager
     * @param FormManager     $formManager
     * @param array           $config
     */
    public function __construct(EngineInterface $templating, EavManager $eavManager, FormManager $formManager, array $config)
    {
        parent::__construct($templating, $config);

        $this->eavManager = $eavManager;
        $this->formManager = $formManager;
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
                    'placeholder' => 'Choose Form',
                ])
        );
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

            $form = $this->formManager->createForm($block->getForm(), $post);

            $parameters['block']->formView = $form->createView();
        }

        return $parameters;
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
            ->setDescription('Include a form created in Forms');

        return $tool;
    }
}
