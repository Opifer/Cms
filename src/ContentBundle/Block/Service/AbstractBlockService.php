<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\ContentBundle\Block\BlockRenderer;
use Opifer\ContentBundle\Entity\Block;
use Opifer\ContentBundle\Environment\Environment;
use Opifer\ContentBundle\Form\Type\DisplayLogicType;
use Opifer\ContentBundle\Model\BlockInterface;
use Opifer\ExpressionEngine\Form\Type\ExpressionEngineType;
use Opifer\ExpressionEngine\Prototype\AndXPrototype;
use Opifer\ExpressionEngine\Prototype\Choice;
use Opifer\ExpressionEngine\Prototype\EventPrototype;
use Opifer\ExpressionEngine\Prototype\NumberPrototype;
use Opifer\ExpressionEngine\Prototype\OrXPrototype;
use Opifer\ExpressionEngine\Prototype\PrototypeCollection;
use Opifer\ExpressionEngine\Prototype\SelectPrototype;
use Opifer\ExpressionEngine\Prototype\TextPrototype;
use Opifer\FormBlockBundle\Entity\ChoiceFieldBlock;
use Opifer\FormBlockBundle\Entity\NumberFieldBlock;
use Opifer\FormBlockBundle\Entity\RangeFieldBlock;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
        $partial = (isset($parameters['partial'])) ? $parameters['partial'] : false;

        if (!$partial && $this->isEsiEnabled($block)) {
            if (null === $response) {
                $response = new Response();
            }

            $content = $this->blockRenderer->renderEsi($block);

            return $response->setContent($content);
        }

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
            'children' => $this->getEnvironment() ? $this->getEnvironment()->getBlockChildren($block) : [],
        ];

        return $parameters;
    }

    /**
     * @param BlockInterface $block
     *
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
        $builder->add(
            $builder->create('default', FormType::class, ['inherit_data' => true])
                ->add('name', TextType::class, [
                    'label' => 'label.name',
                    'attr' => [
                        'help_text' => 'help.block_name',
                        'tag' => 'settings'
                    ],
                    'required' => false
                ])
                ->add('displayName', TextType::class, [
                    'label' => 'label.display_name',
                    'attr' => [
                        'help_text' => 'help.block_display_name',
                        'tag' => 'settings'
                    ],
                    'required' => false
                ])
                ->add('active', ChoiceType::class, [
                    'label' => 'label.active',
                    'attr' => [
                        'help_text' => 'help.block_active',
                        'tag' => 'settings'
                    ],
                    'choices' => [
                        'Active' => true,
                        'Inactive' => false,
                    ],
                ])
        )->add(
            $builder->create('properties', FormType::class, ['label' => false, 'attr' => ['widget_col' => 12]])
        );

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /** @var Block $block */
            $block = $event->getData();
            $form = $event->getForm();

            $form->get('properties')
                ->add('displayLogic', DisplayLogicType::class, [
                    'block' => $block,
                    'required' => false
                ])
            ;

            if ($block->isShared()) {
                $form
                    ->add('sharedName', 'text', [
                        'label' => 'block.shared_name.label',
                    ])
                    ->add('sharedDisplayName', 'text', [
                        'label' => 'block.shared_displayname.label',
                    ])
                ;
            }
        });
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
        $view = $this->getView($block);

        if ($response) {
            $this->setResponseHeaders($block, $response);
        }

        return $this->blockRenderer->render($view, $parameters, $response);
    }

    /**
     * Allows defining custom headers in case of edge side includes
     *
     * @param BlockInterface $block
     * @param Response       $response
     */
    protected function setResponseHeaders(BlockInterface $block, Response $response)
    {
        // Override in child class
    }

    /**
     * Returns if ESI is enabled on the block service
     *
     * @return bool
     */
    public function isEsiEnabled(BlockInterface $block)
    {
        return $this->esiEnabled;
    }

    /**
     * @param BlockInterface $block
     * @return string
     */
    public function getDescription(BlockInterface $block = null)
    {
        return '';
    }
}
