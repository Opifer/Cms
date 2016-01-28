<?php

namespace Opifer\ContentBundle\Block\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Opifer\ContentBundle\Block\Tool\ContentTool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Entity\NavigationBlock;
use Opifer\ContentBundle\Model\BlockInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Content Collection Block Service
 */
class NavigationBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    /** @var EntityManager */
    protected $em;
    protected $view = 'OpiferContentBundle:Block:Content/list.html.twig';

    /**
     * @param EngineInterface $templating
     * @param EntityManager   $em
     */
    public function __construct(EngineInterface $templating, EntityManager $em)
    {
        parent::__construct($templating);

        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(BlockInterface $block = null)
    {
        return 'Navigation';
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
                //->add('value', 'content_list_picker', [
                //    'label'    => 'collection',
                //    'multiple' => true,
                //    'property' => 'title',
                //    'class'    => 'Opifer\CmsBundle\Entity\Content',
                //    'data'     => $options['data']->getValue()
                //])
        );
    }

    public function load(BlockInterface $block)
    {
        $collection = $this->em->getRepository('OpiferCmsBundle:Content')
            ->createQueryBuilder('c')
            ->where('c.id IN (:ids)')->setParameter('ids', json_decode($block->getValue()))
            ->getQuery()
            ->getResult();

        if ($collection) {
            $block->setCollection($collection);
        }
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

        $tool->setIcon('view_list')
            ->setDescription('');

        return $tool;
    }
}
