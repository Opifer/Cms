<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\ContentBundle\Block\BlockRenderer;
use Opifer\ContentBundle\Entity\Block;
use Opifer\ContentBundle\Environment\Environment;
use Opifer\ContentBundle\Model\BlockInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractBlockService implements BlockServiceInterface
{
    const FORM_GROUP_PROPERTIES = 'properties';

    /** @var string */
    protected $editView = 'OpiferContentBundle:Editor:edit_block.html.twig';

    /** @var BlockRenderer */
    protected $blockRenderer;

    /** @var Environment */
    protected $environment;

    /** @var bool */
    protected $esiEnabled = false;

    /**
     * The block configuration.
     *
     * @var array
     */
    protected $config;

    /**
     * @param BlockRenderer $blockRenderer
     * @param array         $config
     */
    public function __construct(BlockRenderer $blockRenderer, array $config)
    {
        $this->blockRenderer = $blockRenderer;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(BlockInterface $block, Response $response = null, array $parameters = [])
    {
        $this->load($block);

        $parameters = array_merge($parameters, $this->getViewParameters($block));

        if ($this->getEnvironment() !== null && $this->getEnvironment()->getBlockMode() === Environment::MODE_MANAGE) {
            $parameters = array_merge($parameters, $this->getManageViewParameters($block));
        }

        return $this->renderResponse($block, $parameters,  $response);
    }

    /**
     * @param BlockInterface $block
     *
     * @return array
     */
    public function getViewParameters(BlockInterface $block)
    {
        $parameters = [
            'block_service' => $this,
            'block' => $block,
        ];

        return $parameters;
    }

    /**
     * @param BlockInterface $block
     * @return array
     */
    public function getManageViewParameters(BlockInterface $block)
    {
        $parameters = [
            'block_view' => $this->getView($block),
            'manage' => true,
            'manage_type' => $this->getManageFormTypeName(),
        ];

        return $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function manage(BlockInterface $block, Response $response = null)
    {
        return $this->execute($block, $response);
    }

    /**
     * @return Environment
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * @param Environment $environment
     *
     * @return BlockServiceInterface
     */
    public function setEnvironment($environment)
    {
        $this->environment = $environment;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(BlockInterface $block = null)
    {
        $class = substr(get_class($this), strrpos(get_class($this), '\\') + 1);
        $shortName = str_replace('BlockService', '', $class);

        return $shortName;
    }

    /**
     * @param BlockInterface $block
     */
    public function preFormSubmit(BlockInterface $block)
    {
    }

    /**
     * @param FormInterface  $form
     * @param BlockInterface $block
     */
    public function postFormSubmit(FormInterface $form, BlockInterface $block)
    {
    }

    /**
     * @param string $view
     *
     * @return $this
     */
    public function setView($view)
    {
        $this->view = $view;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getView(BlockInterface $block)
    {
        return $this->config['view'];
    }

    /**
     * @deprecated There should only be one view from getView
     *
     * {@inheritdoc}
     */
    public function getManageView(BlockInterface $block)
    {
        return $this->getView($block);
    }

    /**
     * {@inheritdoc}.
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
        return $this->blockRenderer;
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
    public function postLoad(BlockInterface $block)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function load(BlockInterface $block)
    {
    }

    public function allowShare(BlockInterface $block)
    {
        return !$block->isShared();
    }

    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /** @var Block $block */
            $block = $event->getData();
            $form = $event->getForm();

            if ($block->isShared()) {
                $form
                    ->add('sharedName', 'text', [
                        'label' => 'block.shared_name.label',
                    ])
                    ->add('sharedDisplayName', 'text', [
                        'label' => 'block.shared_displayname.label',
                    ]);
            }
        });
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     */
    public function configureManageOptions(OptionsResolver $resolver)
    {
    }

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
     * Returns a Response object that can be cache-able.
     *
     * @param BlockInterface $block
     * @param array          $parameters
     * @param Response       $response
     *
     * @return Response
     */
    public function renderResponse(BlockInterface $block, array $parameters = array(), Response $response = null)
    {
        $partial = (isset($parameters['partial'])) ? $parameters['partial'] : false;

        if (!$partial && $this->esiEnabled) {
            if (null === $response) {
                $response = new Response();
            }

            $content = $this->blockRenderer->renderEsi($block);

            return $response->setContent($content);
        }

        $view = $this->getView($block);

        if ($response) {
            $this->setResponseHeaders($response);
        }

        return $this->blockRenderer->render($view, $parameters, $response);
    }

    /**
     * @param Response $response
     */
    protected function setResponseHeaders(Response $response)
    {
        // Override in child class
    }
}
