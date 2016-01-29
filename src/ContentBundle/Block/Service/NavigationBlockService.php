<?php

namespace Opifer\ContentBundle\Block\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Opifer\ContentBundle\Block\Tool\ContentTool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Entity\NavigationBlock;
use Opifer\ContentBundle\Form\Type\ContentTreePickerType;
use Opifer\ContentBundle\Model\BlockInterface;
use Opifer\ContentBundle\Model\ContentManagerInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Navigation Block Service
 */
class NavigationBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    /** @var ContentManagerInterface */
    protected $contentManager;

    /**
     * Constructor
     *
     * @param EngineInterface         $templating
     * @param ContentManagerInterface $contentManager
     * @param array                   $config
     */
    public function __construct(EngineInterface $templating, ContentManagerInterface $contentManager, array $config)
    {
        parent::__construct($templating, $config);

        $this->contentManager = $contentManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildManageForm($builder, $options);

        // Default panel
        $builder->add(
            $builder->create('default', 'form', ['virtual' => true])
                ->add('value', ContentTreePickerType::class)
        );
    }

    /**
     * @param BlockInterface $block
     */
    public function load(BlockInterface $block)
    {
        /** @var NavigationBlock $block */
        $array = json_decode($block->getValue(), true);
        if (!$array) {
            return;
        }

        $ids = $this->gatherIds($array);

        $collection = $this->contentManager->getRepository()
            ->createQueryBuilder('c')
            ->where('c.id IN (:ids)')->setParameter('ids', $ids)
            ->getQuery()
            ->getResult();

        if ($collection) {
            $block->setTree($collection);
        }
    }

    /**
     * @param array $array
     * @param array $ids
     * @return array
     */
    protected function gatherIds(array $array, array $ids = array())
    {
        foreach ($array as $item) {
            $ids[] = $item['id'];
            if (isset($item['children']) && count($item['children'])) {
                $this->gatherIds($item['children'], $ids);
            }
        }

        return $ids;
    }

    /**
     * {@inheritDoc}
     */
    public function createBlock()
    {
        return new NavigationBlock();
    }

    /**
     * {@inheritDoc}
     */
    public function getTool()
    {
        $tool = new ContentTool('Navigation', 'OpiferContentBundle:NavigationBlock');

        $tool->setIcon('menu')
            ->setDescription('Generates a simple page navigation');

        return $tool;
    }
}
