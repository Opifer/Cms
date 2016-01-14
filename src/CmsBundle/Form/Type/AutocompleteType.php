<?php

namespace Opifer\CmsBundle\Form\Type;

use Doctrine\ORM\EntityManager;
use Opifer\CmsBundle\Form\DataTransformer\PropertyToEntityTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AutocompleteType extends AbstractType
{
    /** @var EntityManager*/
    protected $em;

    /** @var array */
    protected $config;

    /**
     * @param EntityManager $em
     * @param array         $config
     */
    public function __construct(EntityManager $em, array $config)
    {
        $this->em = $em;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $alias = $options['alias'];
        if (false == isset($this->config[$alias])) {
            throw new \Exception(sprintf('No config found for autocomplete alias: %s'), $alias);
        }

        $builder->addViewTransformer(new PropertyToEntityTransformer(
            $this->em,
            $this->config[$alias]
        ), true);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['alias']);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'text';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'opifer_autocomplete';
    }
}
