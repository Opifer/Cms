<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\ContentBundle\Block\Tool\ContentTool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Entity\CarouselBlock;
use Opifer\ContentBundle\Model\BlockInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Carousel Block
 */
class CarouselBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    protected $view = 'OpiferContentBundle:Block:Content/carousel.html.twig';

    /**
     * Constructor.
     *
     * @param EngineInterface $templating
     */
    public function __construct(EngineInterface $templating)
    {
        parent::__construct($templating);
    }

    /**
     * {@inheritDoc}
     */
    public function getName(BlockInterface $block = null)
    {
        return 'Carousel';
    }

    /**
     * {@inheritDoc}
     */
    public function getManageFormTypeName()
    {
        return 'layout';
    }

    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildManageForm($builder, $options);

        // Default panel
        $builder->add(
            $builder->create('properties', FormType::class)
                ->add('id', TextType::class, ['attr' => ['help_text' => 'help.html_id']])
                ->add('extra_classes', TextType::class, ['attr' => ['help_text' => 'help.extra_classes']])
                ->add('show_indicators', ChoiceType::class, [
                    'choices' => ['Yes' => true, 'No' => false],
                    'choices_as_values' => true,
                ])
                ->add('show_controls', ChoiceType::class, [
                    'choices' => ['Yes' => true, 'No' => false],
                    'choices_as_values' => true,
                ])
        );
    }

    /**
     * {@inheritDoc}
     */
    public function createBlock()
    {
        return new CarouselBlock();
    }

    /**
     * {@inheritDoc}
     */
    public function getTool()
    {
        $tool = new ContentTool('Carousel', 'OpiferContentBundle:CarouselBlock');

        $tool->setIcon('view_carousel')
            ->setDescription('Create a carousel of two or more slides');

        return $tool;
    }
}