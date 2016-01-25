<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\ContentBundle\Block\Tool\ContainerTool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Model\BlockInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

/**
 * Class ColumnBlockService
 *
 * @package Opifer\ContentBundle\Block
 */
class ContainerBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    protected $wrapper = "container";


    /** @var string */
    protected $view = 'OpiferContentBundle:Block:Layout/layout.html.twig';

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


        $propertiesForm = $builder->create('properties', 'form')
            ->add('id', 'text', ['attr' => ['help_text' => 'help.html_id']])
            ->add('extra_classes', 'text', ['attr' => ['help_text' => 'help.extra_classes']]);

        $builder->add($propertiesForm);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $block = $event->getData();

            $form = $event->getForm();

            if ($block->getWrapper() == 'section') {
                $styles = ['row-space-top-2', 'row-space-top-4', 'row-space-top-8', 'row-space-2', 'row-space-4', 'row-space-8', 'light', 'dark'];
                $form->get('properties')->add('styles', 'choice', [
                    'label' => 'label.styling',
                    'choices'  => array_combine($styles, $styles),
                    'required' => false,
                    'expanded' => true,
                    'multiple' => true,
                    'attr' => ['help_text' => 'help.html_styles'],
                ]);
            }

            $form->get('properties')->add(
                'container_size',
                'choice',
                [
                    'label' => 'label.container_sizing',
                    'choices' => ['fluid' => 'label.container_fluid', '' => 'label.container_fixed', 'smooth' => 'label.container_smooth'],
                    'required' => true,
                    'attr' => ['help_text' => 'help.container_sizing'],
                ]
            );
        });
    }

    /**
     * {@inheritDoc}
     */
    public function configureManageOptions(OptionsResolver $resolver)
    {
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
        return new ColumnBlock();
    }

    /**
     * {@inheritDoc}
     */
    public function getName(BlockInterface $block = null)
    {
        return ucfirst($this->wrapper);
    }

    /**
     * {@inheritDoc}
     */
    public function getTool()
    {
        $tool = new ContainerTool($this->getName(), 'OpiferContentBundle:ContainerBlock');

        $tool->setData(['wrapper' => $this->wrapper])
            ->setIcon('crop_landscape')
            ->setDescription('Wrapping element ' . $this->wrapper . ' to hold columns or content in');

        return $tool;
    }

    /**
     * @return string
     */
    public function getWrapper()
    {
        return $this->wrapper;
    }

    /**
     * @param string $wrapper
     */
    public function setWrapper($wrapper)
    {
        $this->wrapper = $wrapper;
    }

}