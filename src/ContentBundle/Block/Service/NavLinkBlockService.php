<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\ContentBundle\Block\BlockRenderer;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Entity\NavLinkBlock;
use Opifer\ContentBundle\Form\Type\NavLinkType;
use Opifer\ContentBundle\Model\BlockInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Nav Link Block Service.
 */
class NavLinkBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    /** @var RouterInterface */
    protected $router;

    /**
     * Constructor.
     *
     * @param BlockRenderer   $blockRenderer
     * @param RouterInterface $router
     * @param array           $config
     */
    public function __construct(BlockRenderer $blockRenderer, RouterInterface $router, array $config)
    {
        parent::__construct($blockRenderer, $config);

        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildManageForm($builder, $options);

        $builder->get('default')
            ->add('value', NavLinkType::class, [
                'label' => 'Link',
                'attr' => [
                    'help_text' => 'Either select a content item or define your own custom URL'
                ]
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getViewParameters(BlockInterface $block)
    {
        $parameters = parent::getViewParameters($block);
        $parameters['url'] = $this->getUrl($block);
        $parameters['is_sub_nav'] = ($block->getParent() instanceof NavLinkBlock) ? true : false;

        return $parameters;
    }

    /**
     * @param BlockInterface $block
     *
     * @return string
     */
    protected function getUrl(BlockInterface $block)
    {
        if (substr($block->getValue(), 0, 4) == 'http') {
            return $block->getValue();
        }

        return $this->generateUrl($block);
    }

    /**
     * @param BlockInterface $block
     *
     * @return string
     */
    protected function generateUrl(BlockInterface $block)
    {
        return $this->router->generate('_content', ['slug' => $block->getValue()]);
    }

    /**
     * {@inheritdoc}
     */
    public function createBlock()
    {
        return new NavLinkBlock();
    }

    /**
     * {@inheritdoc}
     */
    public function getTool(BlockInterface $block = null)
    {
        $tool = new Tool('Nav link', 'navlink');

        $tool->setIcon('link')
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
