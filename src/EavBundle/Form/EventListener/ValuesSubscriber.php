<?php

namespace Opifer\EavBundle\Form\EventListener;

use Opifer\EavBundle\Entity\Value;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Opifer\EavBundle\Form\Type\ValueType;

/**
 * Values Subscriber
 *
 * Maps the eav values to the right form fields
 */
class ValuesSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
        ];
    }

    /**
     * Listens to the PRE_SET_DATA event and adds form fields dynamically.
     *
     * @param FormEvent $event
     *
     * @return void
     */
    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        $fields = $form->getConfig()->getOption('fields');

        if (null === $data || '' === $data) {
            $data = [];
        }

        if (!is_array($data) && !($data instanceof \Traversable && $data instanceof \ArrayAccess)) {
            throw new UnexpectedTypeException($data, 'array or (\Traversable and \ArrayAccess)');
        }
        // Sorting values so that they display in sorted order of the attributes
        uasort($data, function ($a, $b) {
            return $a->getAttribute()->getSort() > $b->getAttribute()->getSort();
        });

        /**
         * @var string $name
         * @var Value  $value
         */
        foreach ($data as $name => $value) {
            //Do not add fields when there not in fields value when giving.
            if (!empty($fields) && !in_array($name, $fields)) {
                continue;
            }
            // Do not add fields dynamically if they've already been set statically.
            // This allows us to override the formtypes from inside the form type
            // that's calling this subscriber.
            if ($form->has($name)) {
                continue;
            }
            $form->add($name, ValueType::class, [
                'label' => $value->getAttribute()->getDisplayName(),
                'required' => $value->getAttribute()->getRequired(),
                'attribute' => $value->getAttribute(),
                'entity' => get_class($value),
                'value' => $value,
                'attr' => ['help_text' => $value->getAttribute()->getDescription()]
            ]);
        }
    }
}
