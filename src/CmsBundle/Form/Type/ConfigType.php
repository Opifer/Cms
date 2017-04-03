<?php

namespace Opifer\CmsBundle\Form\Type;

use JMS\Serializer\SerializerInterface;
use Opifer\CmsBundle\DependencyInjection\ConfigurationFormRegistry;
use Opifer\CmsBundle\Manager\ConfigManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;

class ConfigType extends AbstractType
{
    /**
     * @var ConfigurationFormRegistry
     */
    private $registry;

    /**
     * @var ConfigManager
     */
    private $configManager;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * Constructor
     *
     * @param ConfigurationFormRegistry $registry
     * @param ConfigManager $configManager
     * @param SerializerInterface $serializer
     */
    public function __construct(ConfigurationFormRegistry $registry, ConfigManager $configManager, SerializerInterface $serializer)
    {
        $this->registry = $registry;
        $this->configManager = $configManager;
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $forms = $this->registry->getForms();

        /** @var ConfigurationFormTypeInterface $form */
        foreach ($forms as $name => $form) {
            $builder->add(
                $builder
                    ->create($name, get_class($form), [
                        'label' => $form->getLabel(),
                        'data' => $this->configManager->keyValues($form)
                    ])
                    ->addModelTransformer(new CallbackTransformer(
                        function ($original) {
                            $transformed = [];
                            foreach ($original as $key => $object) {
                                $transformed[$key] = $object->Value;
                            }

                            return $transformed;
                        },
                        function ($submitted) {
                            $transformed = [];
                            foreach ($submitted as $key => $value) {
                                $object = new \stdClass();
                                $object->Value = $value;

                                $transformed[$key] = $object;
                            }

                            return $transformed;
                        }
                    ))
            );
        }
    }
}
