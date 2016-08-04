<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\ContentBundle\Entity\AvatarBlock;
use Opifer\ContentBundle\Block\Service\AbstractBlockService;
use Opifer\ContentBundle\Block\Service\BlockServiceInterface;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Model\BlockInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Opifer\ContentBundle\Model\ContentManagerInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Opifer\ContentBundle\Form\Type\ContentPickerType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Avatar Block Service
 */
class AvatarBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildManageForm($builder, $options);

        // Default panel
        $builder->add(
            $builder->create('default', FormType::Class, ['inherit_data' => true])
                ->add('loginContentItem', ContentPickerType::class, [
                            'label' => 'label.login_content_item',
                        ])
                ->add('registrationContentItem', ContentPickerType::class, [
                            'label' => 'label.register_content_item',
                        ])
        );

        $builder->add(
            $builder->create('properties', FormType::class)
                ->add('loginUrl', TextType::class, [
                    'label' => 'label.login_url',
                ])
                ->add('registrationUrl', TextType::class, [
                    'label' => 'label.registration_url',
                ])
                ->add('id', TextType::class, ['attr' => ['help_text' => 'help.html_id']])
                ->add('extra_classes', TextType::class, ['attr' => ['help_text' => 'help.extra_classes']])
        );
    }

    public function getViewParameters(BlockInterface $block)
    {
        $parameters = [
            'block_service' => $this,
            'block'         => $block,
        ];
        
        return $parameters;
    }

    /**
     * {@inheritDoc}
     */
    public function createBlock()
    {
        return new AvatarBlock;
    }

    /**
     * {@inheritDoc}
     */
    public function getTool(BlockInterface $block = null)
    {
        $tool = new Tool('Avatar', 'avatar');

        $tool->setIcon('account_box')
            ->setDescription('Shows logged user data or login/register button');

        return $tool;
    }
}
