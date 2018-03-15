<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\ContentBundle\Block\BlockRenderer;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Entity\NavBarBlock;
use Opifer\ContentBundle\Form\Type\ContentListPickerType;
use Opifer\ContentBundle\Model\BlockInterface;
use Opifer\ContentBundle\Model\ContentManagerInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * NavBar Block Service
 */
class NavBarBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    protected $requestStack;

    public function __construct(BlockRenderer $blockRenderer, array $config, RequestStack $requestStack)
    {
        parent::__construct($blockRenderer, $config);
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildManageForm($builder, $options);

        $builder->get('properties')
            ->add('template', ChoiceType::class, [
                'label' => 'label.template',
                'placeholder' => 'placeholder.choice_optional',
                'attr' => [
                    'help_text' => 'help.block_template',
                    'widget_col' => 9,
                    'tag' => 'styles'
                ],
                'choices' => $this->config['templates'],
                'required' => false,
            ])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function createBlock()
    {
        return new NavBarBlock;
    }

    /**
     * {@inheritDoc}
     */
    public function getTool(BlockInterface $block = null)
    {
        $tool = new Tool('Nav bar', 'navbar');

        $tool->setIcon('menu')
            ->setGroup('navigation')
            ->setDescription('A navigation bar');

        return $tool;
    }

    /**
     * @param BlockInterface $block
     * @return string
     */
    public function getDescription(BlockInterface $block = null)
    {
        return 'A navigation bar';
    }
}
