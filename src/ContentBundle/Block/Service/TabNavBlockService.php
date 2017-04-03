<?php

namespace Opifer\ContentBundle\Block\Service;

use Braincrafted\Bundle\BootstrapBundle\Form\Type\BootstrapCollectionType;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Entity\Block;
use Opifer\ContentBundle\Entity\TabNavBlock;
use Opifer\ContentBundle\Entity\TabsBlock;
use Opifer\ContentBundle\Form\Type\TabType;
use Opifer\ContentBundle\Model\BlockInterface;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class TabNavBlock
 *
 * @package Opifer\ContentBundle\Block
 */
class TabNavBlockService extends AbstractBlockService implements LayoutBlockServiceInterface, BlockServiceInterface, ToolsetMemberInterface
{
    /** @var integer */
    protected $tabCount = 1;

    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildManageForm($builder, $options);

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            array($this, 'onPreSetData')
        );

        $builder->addEventListener(
            FormEvents::SUBMIT,
            array($this, 'onSubmit')
        );

        $builder->addEventListener(
            FormEvents::POST_SET_DATA,
            array($this, 'onPostSetData')
        );

        $propertiesForm = $builder->create('properties', FormType::class)
            ->add('template', ChoiceType::class, [
                    'label'       => 'label.template',
                    'attr'        => ['help_text' => 'help.block_template'],
                    'choices'     => $this->config['templates'],
                    'required'    => true,
                ])
            ->add('tabs', BootstrapCollectionType::class, [
                'label'         => 'label.tabs',
                'allow_add'     => true,
                'allow_delete'  => true,
                'entry_type'    => TabType::class,
                'sub_widget_col'=> 8,
                'button_col'    => 4,
                'attr'          => ['class' => 'sortable-tabnav'],
                'options'       => ['attr' => ['style' => 'inline']],
            ])
            ->add('id', TextType::class, ['attr' => ['help_text' => 'help.html_id']])
            ->add('extra_classes', TextType::class, ['attr' => ['help_text' => 'help.extra_classes']]);

        if ($this->config['styles']) {
            $builder->get('styles')
                ->add('styles', ChoiceType::class, [
                    'label' => 'label.styling',
                    'choices'  => $this->config['styles'],
                    'required' => false,
                    'expanded' => true,
                    'multiple' => true,
                    'attr' => ['help_text' => 'help.html_styles'],
                ]);
        }

        // Default panel
        $builder->add(
            $propertiesForm
        );
    }

    public function onPreSetData(FormEvent $event)
    {
        // block
        $block = $event->getData();
        $this->normalizeTabs($block);

        $event->setData($block);
    }

    public function onPostSetData(FormEvent $event)
    {
        $block = $event->getData();
        $this->normalizeTabs($block);

        $event->setData($block);
    }

    public function onSubmit(FormEvent $event)
    {
        $block = $event->getData();
        $this->normalizeTabs($block);

        $event->setData($block);
    }

    private function normalizeTabs(BlockInterface $block)
    {
        $properties = $block->getProperties();

        if (isset($properties['tabs']) && count($properties['tabs'])) {
            if (isset($properties['tabs'][0]) && !is_array($properties['tabs'][0])) {
                $converted = array();
                $sort = count($properties['tabs']);
                foreach ($properties['tabs'] as $key => $value) {
                    $converted[] = ['label' => $value, 'key' => $key, 'sort' => $sort--];
                }

                $properties['tabs'] = $converted;
            }


            $maxKey = 0;
            $maxSort = 0;
            array_walk($properties['tabs'], function ($tab) use (&$maxKey, &$maxSort) {
                if (isset($tab['key']) && $tab['key'] > $maxKey) {
                    $maxKey = $tab['key'];
                }
                if (isset($tab['sort']) && $tab['sort'] > $maxSort) {
                    $maxSort = $tab['sort'];
                }
            });

            $maxKey++;
            $maxSort++;
            foreach ($properties['tabs'] as &$tab) {
                if (!isset($tab['key']) || $tab['key'] === null || $tab['key'] === "") {
                    $tab['key'] = $maxKey++;
                }
                if (!isset($tab['sort']) || $tab['sort'] === null || $tab['sort'] === "") {
                    $tab['sort'] = $maxSort++;
                }
            }

            uasort($properties['tabs'], function ($a, $b) {
                return $a['sort'] < $b['sort'] ? 1 : 0;
            });

            $block->setProperties($properties);
        }

        return $block;
    }

    public function load(BlockInterface $block)
    {
        parent::load($block);

        $this->normalizeTabs($block);
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
        return new TabNavBlock();
    }

    /**
     * {@inheritDoc}
     */
    public function getTool(BlockInterface $block = null)
    {
        $tool = new Tool($this->getName(), 'tabnav');

        $tool->setIcon('tab')
            ->setGroup(Tool::GROUP_LAYOUT)
            ->setDescription('Inserts tab navigation to control tabbed content');

        return $tool;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(BlockInterface $block = null)
    {
        return 'Tabs';
    }
    /**
     * {@inheritDoc}
     */
    public function getPlaceholders(BlockInterface $block = null)
    {
        $this->normalizeTabs($block);
        $tabs = $block->getTabs();

        $placeholders = array();

        if ($tabs && count($tabs)) {
            foreach ($tabs as $tab) {
                $placeholders[$tab['key']] = $tab['label'];
            }
        }

        return $placeholders;
    }
}
