<?php

namespace Opifer\ContentBundle\Block;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Opifer\ContentBundle\Model\BlockInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class AbstractBlockService
 *
 * @package Opifer\ContentBundle\Block
 */
abstract class AbstractBlockService
{
    /** @var string */
    protected $view;

    /** @var string */
    protected $manageView = 'OpiferContentBundle:Block:manage.html.twig';

    /** @var string */
    protected $editView = 'OpiferContentBundle:PageManager:edit_block.html.twig';

    /** @var EngineInterface */
    protected $templating;

    const FORM_GROUP_PROPERTIES = 'properties';

    /**
     * @param EngineInterface $templating
     */
    public function __construct(EngineInterface $templating)
    {
        $this->templating = $templating;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(BlockInterface $block, Response $response = null)
    {
        return $this->renderResponse($this->getView(), array(
            'block_service'  => $this,
            'block'          => $block,
        ), $response);
    }

    /**
     * {@inheritdoc}
     */
    public function manage(BlockInterface $block, Response $response = null)
    {
        return $this->renderResponse($this->getManageView(), array(
            'block_service'  => $this,
            'block'          => $block,
            'block_view'     => $this->getView(),
            'block_mode'     => 'manage',
            'manage_type'    => $this->getManageFormTypeName(),
        ), $response);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(BlockInterface $block = null)
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * {@inheritdoc}
     */
    public function getManageView()
    {
        return $this->manageView;
    }

    /**
     * {@inheritView}
     */
    public function getEditView()
    {
        return $this->editView;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplating()
    {
        return $this->templating;
    }

    /**
     * {@inheritdoc}
     */
    public function prePersist(BlockInterface $block)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function postPersist(BlockInterface $block)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate(BlockInterface $block)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function postUpdate(BlockInterface $block)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function preRemove(BlockInterface $block)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function postRemove(BlockInterface $block)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function load(BlockInterface $block)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('save', 'submit', [
                'label' => 'button.submit'
            ]);
    }

//    public function buildPropertiesForm(FormBuilderInterface $builder, array $options)
//    {
//        return $builder->create('properties', 'form')
//                        ->add('id', 'text')
//                        ->add('extra_classes', 'text');
//    }

    /**
     * BlockAdapterFormType calls this method to get the name of the FormType.
     *
     * @return string
     */
    public function getManageFormTypeName()
    {
        return 'default';
    }

    /**
     * Creates a new Block
     *
     * @return BlockInterface
     */
    public abstract function createBlock();

    /**
     * Returns a Response object that can be cache-able.
     *
     * @param string   $view
     * @param array    $parameters
     * @param Response $response
     *
     * @return Response
     */
    public function renderResponse($view, array $parameters = array(), Response $response = null)
    {
        return $this->getTemplating()->renderResponse($view, $parameters, $response);
    }
}