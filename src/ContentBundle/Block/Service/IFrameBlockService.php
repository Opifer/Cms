<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Entity\IFrameBlock;
use Opifer\ContentBundle\Form\Type\StylesType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Opifer\ContentBundle\Model\BlockInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * iFrame Block Service
 */
class IFrameBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildManageForm($builder, $options);

        $propertiesForm = $builder->get('properties');

        $builder->add(
            $propertiesForm
                ->add('url', TextType::class, [
                    'attr' => [
                        'help_text' => 'help.iframe_url',
                        'tag' => 'general'
                    ],
                    'required' => true,
                    'constraints' => [
                        new NotBlank(),
                    ],
                ])
        )->add(
            $propertiesForm->add('id', TextType::class, ['attr' => ['help_text' => 'help.html_id'],'required' => false])
                ->add('extra_classes', TextType::class, ['attr' => ['help_text' => 'help.extra_classes'],'required' => false])

        );

        $builder->get('properties')
            ->add('width', TextType::class, ['attr' => ['tag' => 'styles']])
            ->add('height', TextType::class, ['attr' => ['tag' => 'styles']]);

        if ($this->config['styles']) {
            $builder->get('properties')
                ->add('styles', StylesType::class, [
                    'choices' => $this->config['styles'],
                ]);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function createBlock()
    {
        return new IFrameBlock;
    }

    /**
     * {@inheritDoc}
     */
    public function getTool(BlockInterface $block = null)
    {
        $tool = new Tool('iFrame', 'iframe');

        $tool->setIcon('web_asset')
            ->setDescription('Include an iframe with url of choice');

        return $tool;
    }

    /**
     * @param BlockInterface $block
     * @return string
     */
    public function getDescription(BlockInterface $block = null)
    {
        return 'Creates an iframe with url of choice';
    }
}
