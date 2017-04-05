<?php

namespace Opifer\ContentBundle\Form\Type;

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
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Filter Form Type.
 */
class DisplayLogicType extends ExpressionEngineType
{
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $defaultPrototypes = $this->getDisplayLogicPrototypes($options['block']);

        $options['prototypes'] = array_merge($options['prototypes'], $defaultPrototypes);

        parent::buildView($view, $form, $options);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefined(['block']);
        $resolver->setDefaults([
            'label' => 'label.display_logic',
            'prototypes' => [],
            'attr' => [
                'help_text' => 'help.display_logic'
            ]
        ]);
    }

    /**
     * @param BlockInterface $block
     *
     * @return \Opifer\ExpressionEngine\Prototype\Prototype[]
     */
    protected function getDisplayLogicPrototypes(BlockInterface $block)
    {
        $collection = new PrototypeCollection([
            new OrXPrototype(),
            new AndXPrototype(),
            new EventPrototype('click_event', 'Click Event', 'event.type.click'),
            new TextPrototype('dom_node_id', 'DOM Node Id', 'node.id')
        ]);

        $owner = $block->getOwner();
        if ($owner) {
            // Avoid trying to add display logic for blocks when the current block has no owner (e.g. shared blocks)
            $blockChoices = [];
            foreach ($owner->getBlocks() as $member) {
                try {
                    $properties = $member->getProperties();

                    if ($member instanceof ChoiceFieldBlock) {
                        if (empty($member->getName())) {
                            continue;
                        }
                        if (!isset($properties['options'])) {
                            continue;
                        }
                        $choices = [];
                        foreach ($properties['options'] as $option) {
                            if (empty($option['key'])) {
                                continue;
                            }
                            $choices[] = new Choice($option['key'], $option['value']);
                        }
                        $collection->add(new SelectPrototype($member->getName(), $properties['label'], $member->getName(), $choices));
                    } elseif ($member instanceof NumberFieldBlock || $member instanceof RangeFieldBlock) {
                        if (empty($member->getName())) {
                            continue;
                        }

                        $collection->add(new NumberPrototype($member->getName(), $properties['label'], $member->getName()));
                    }

                    if (!empty($member->getName())) {
                        $blockChoices[] = new Choice($member->getName(), $member->getName());
                    }
                } catch (\Exception $e) {
                    // Avoid throwing exceptions here for now, since e.g. duplicate namesthis will cause all blocks to be uneditable.
                }
            }

            $collection->add(new SelectPrototype('block_name', 'Block Name', 'block.name', $blockChoices));
        }

        return $collection->all();
    }
}
