<?php

namespace Opifer\ContentBundle\Block\Service;

use Braincrafted\Bundle\BootstrapBundle\Form\Type\BootstrapCollectionType;
use Opifer\ContentBundle\Block\Tool\LayoutTool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Entity\TabsBlock;
use Opifer\ContentBundle\Model\BlockInterface;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\HttpFoundation\Response;

/**
 * Class TabNavBlock
 *
 * @package Opifer\ContentBundle\Block
 */
class TabNavBlockService extends AbstractBlockService implements LayoutBlockServiceInterface, BlockServiceInterface, ToolsetMemberInterface
{
    /** @var integer */
    protected $tabCount = 1;

    /**
     * {@inheritdoc}
     */
    public function execute(BlockInterface $block, Response $response = null)
    {
        $parameters = array(
            'block_service'  => $this,
            'block'          => $block,
        );

        return $this->renderResponse($this->getView($block), $parameters, $response);
    }

    /**
     * {@inheritdoc}
     */
    public function manage(BlockInterface $block, Response $response = null)
    {
        return $this->renderResponse($this->getManageView($block), array(
            'block_service'  => $this,
            'block'          => $block,
            'block_view'     => $this->getView($block),
            'manage_type'    => $this->getManageFormTypeName(),
        ), $response);
    }

    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildManageForm($builder, $options);

        $propertiesForm = $builder->create('properties', FormType::class)
            ->add('template', ChoiceType::class, [
                    'label'       => 'label.template',
                    'attr'        => ['help_text' => 'help.block_template'],
                    'choices'     => $this->config['templates'],
                    'required'    => true,
                ])
            ->add('tabs', BootstrapCollectionType::class, [
                'label'         => 'label.tabs',
                'allow_add'     => true,
                'allow_delete'  => true,
                'entry_type'    => TextType::class
            ])
            ->add('id', TextType::class, ['attr' => ['help_text' => 'help.html_id']])
            ->add('extra_classes', TextType::class, ['attr' => ['help_text' => 'help.extra_classes']]);

        if ($this->config['styles']) {
            $propertiesForm
                ->add('styles', ChoiceType::class, [
                    'label' => 'label.styling',
                    'choices'  => $this->config['styles'],
                    'required' => false,
                    'expanded' => true,
                    'multiple' => true,
                    'attr' => ['help_text' => 'help.html_styles'],
                ]);
        }

        // Default panel
        $builder->add(
            $propertiesForm
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getManageFormTypeName()
    {
        return 'layout';
    }

    /**
     * {@inheritDoc}
     */
    public function createBlock()
    {
        return new TabNavBlock();
    }

    /**
     * {@inheritDoc}
     */
    public function getTool()
    {
        $tool = new LayoutTool($this->getName(), 'OpiferContentBundle:TabNavBlock');

        $tool->setIcon('tab')
            ->setDescription('Inserts tab navigation to control tabbed content.');

        return $tool;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(BlockInterface $block = null)
    {
        return 'Tabs';
    }
    /**
     * {@inheritDoc}
     */
    public function getPlaceholders(BlockInterface $block = null)
    {
        $tabs = $block->getTabs();

        return ($tabs && count($tabs)) ? $tabs : array();
    }
}
