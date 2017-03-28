<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\CmsBundle\Entity\Media;
use Opifer\ContentBundle\Block\BlockRenderer;
use Opifer\ContentBundle\Model\ContentManagerInterface;
use Opifer\ContentBundle\Entity\DataViewBlock;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Opifer\MediaBundle\Form\Type\MediaPickerType;
use Opifer\ContentBundle\Form\Type\ContentPickerType;
use Opifer\ContentBundle\Form\Type\ContentListPickerType;
use Opifer\CmsBundle\Form\Type\CKEditorType;
use Opifer\ContentBundle\Model\BlockInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * DataView Block Service.
 */
class DataViewBlockService extends AbstractBlockService implements LayoutBlockServiceInterface, BlockServiceInterface, ToolsetMemberInterface
{
    /** @var EntityManagerInterface */
    protected $em;

    /**
     * @param BlockRenderer           $blockRenderer
     * @param EntityManagerInterface $em
     * @param array $config
     */
    public function __construct(BlockRenderer $blockRenderer, EntityManagerInterface $em, array $config)
    {
        parent::__construct($blockRenderer, $config);

        $this->em = $em;
    }
    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildManageForm($builder, $options);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $block = $event->getData();
            $dataView = $block->getDataView();
            $fields = $dataView->getFields();

            $form = $event->getForm();
            foreach ($fields as $field) {
                $options = [
                    'label' => $field['display_name'],
                ];

                switch ($field['type']) {
                    case 'text':
                        $type = TextType::class;
                        break;
                    case 'select':
                        $type = ChoiceType::class;
                        if (isset($field['options'])) {
                            $choices = [];
                            foreach ($field['options'] as $option) {
                                $choices[$option['value']] = $option['key'];
                            }
                            $options['choices'] = $choices;
                            $options['choices_as_values'] = true;
                        }
                        break;
                    case 'number':
                        $type = NumberType::class;
                        break;
                    case 'textarea':
                        $type = TextareaType::class;
                        break;
                    case 'contentItem':
                        $type = ContentPickerType::class;
                        break;
                    case 'contentItems':
                        $type = ContentListPickerType::class;
                        break;
                    case 'checkbox':
                        $type = CheckboxType::class;
                        break;
                    case 'html':
                        $type = CKEditorType::class;
                        break;
                    case 'media':
                        $type = MediaPickerType::class;
                        $options['to_json'] = true;
                        $options['multiple'] = false;
                        $options['required'] = false;
                        break;
                    default:
                        $type = TextType::class;
                        break;
                }

                $form->get('properties')->add($field['name'], $type, $options);
            }

        });

        // Manually map the media items from the properties to the `medias` property, to store medias as an association
        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $block = $event->getData();
            $properties = $block->getProperties();
            $fields = $block->getDataView()->getFields();

            $ids = [];
            foreach ($fields as $field) {
                if ($field['type'] == 'media') {
                    $ids[] = $properties[$field['name']];
                }
            }

            // TODO: Get repository from MediaManager
            $medias = $this->em->getRepository(Media::class)->findByIds($ids);
            $block->setMedias($medias);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function createBlock($args)
    {
        $block = new DataViewBlock();

        $dataView = $this->em->getRepository('OpiferContentBundle:DataView')->find($args['dataViewId']);
        $block->setDataView($dataView);

        return $block;
    }

    /**
     * {@inheritdoc}
     */
    public function getTool(BlockInterface $block = null)
    {
        if ($block) {
            return $this->getDataViewTool($block->getDataView());
        }

        $dataViews = $this->em->getRepository('OpiferContentBundle:DataView')->findBy(['active' => true]);

        $tools = [];

        foreach ($dataViews as $dataView) {
            $tool = $this->getDataViewTool($dataView);

            array_push($tools, $tool);
        }

        return $tools;
    }

    private function getDataViewTool($dataView)
    {
        $tool = new Tool($dataView->getDisplayName(), 'data_view');

        $tool->setData(['dataViewId' => $dataView->getId()])
            ->setIcon($dataView->getIconType())
            ->setGroup('Dataviews')
            ->setDescription($dataView->getDescription());

        return $tool;
    }

    public function getPlaceholders(BlockInterface $block = null)
    {
        $placeholders = $block->getDataView()->getPlaceholders();

        if (empty($placeholders)) {
            return [];
        }

        return array_map(function ($value) {
            return $value['name'];
        }, $placeholders);
    }
}
