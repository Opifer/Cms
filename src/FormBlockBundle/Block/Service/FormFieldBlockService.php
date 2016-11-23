<?php

namespace Opifer\FormBlockBundle\Block\Service;

use Braincrafted\Bundle\BootstrapBundle\Form\Type\BootstrapCollectionType;
use Doctrine\ORM\EntityRepository;
use Opifer\ContentBundle\Form\Type\BlockPickerType;
use Opifer\FormBlockBundle\Form\Type\FormFieldValidationType;
use Opifer\ContentBundle\Block\BlockRenderer;
use Opifer\ContentBundle\Block\Service\AbstractBlockService;
use Opifer\ContentBundle\Block\Service\BlockServiceInterface;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Model\BlockInterface;
use Opifer\FormBundle\Model\FormManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;

/**
 * Form Block Service.
 */
abstract class FormFieldBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    /** @var FormManager */
    protected $formManager;

    /** @var bool {@inheritdoc} */
    protected $esiEnabled = true;

    /**
     * @param BlockRenderer $blockRenderer
     * @param FormManager   $formManager
     * @param array         $config
     */
    public function __construct(BlockRenderer $blockRenderer, FormManager $formManager, array $config)
    {
        parent::__construct($blockRenderer, $config);

        $this->formManager = $formManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildManageForm($builder, $options);

        $propertiesForm = $builder->create('properties', FormType::class)
            ->add('label', TextType::class)
            ->add('helpText', TextareaType::class, [
                'required' => false,
            ])
            ->add('validation', BootstrapCollectionType::class, [
                'required' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'type' => FormFieldValidationType::class,
            ])
            ->add('formula', TextAreaType::class, [
                'required' => false,
                'label' => 'label.formula',
                'attr' => [
                    'help_text' => 'help_text.formula',
                ],
            ])
            ->add('help_modal', BlockPickerType::class, [
                'required' => false,
                'label' => 'label.help_modal',
            ])
        ;

        $builder->add(
            $builder->create('default', FormType::class, ['inherit_data' => true])
                ->add('name', TextType::class, ['label' => 'label.name', 'attr' => ['help_text' => 'help.block_name']])
        )->add(
            $propertiesForm
        );
    }

    public function getViewParameters(BlockInterface $block)
    {
        $parameters = [
            'block_service' => $this,
            'block' => $block,
        ];

        return $parameters;
    }
}
