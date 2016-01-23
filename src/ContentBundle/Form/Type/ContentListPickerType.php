<?php

namespace Opifer\ContentBundle\Form\Type;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ManagerRegistry;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\PersistentCollection;
use Opifer\ContentBundle\Entity\Block;
use Opifer\ContentBundle\Entity\BlockContent;
use Opifer\ContentBundle\Model\Content;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Multi Content Picker Form Type
 */
class ContentListPickerType extends AbstractType
{
    /** @var array */
    protected $options;

    /**
     * Builds the form and transforms the model data.
     *
     * A collection of Content entities is selected, while they have to be persisted as BlockContent,
     * since the many-to-many relation between Blocks and Content has to be versioned too.
     * The transformer changes the Content collection to a BlockContent collection and vice-versa.
     *
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->options = $options;

        $builder->addModelTransformer(new CallbackTransformer(
            function ($original) {
                if (!$original instanceof PersistentCollection) {
                    return null;
                }

                $collection = new ArrayCollection();
                foreach ($original as $blockContent) {
                    $collection->add($blockContent->getContent());
                }

                return $collection;
            },
            function ($submitted) {
                if (!$submitted instanceof ArrayCollection) {
                    return null;
                }

                $collection = new ArrayCollection();
                foreach ($submitted as $content) {
                    if (!$blockContent = $this->hasContent($content)) {
                        $blockContent = new BlockContent();
                        $blockContent->setContent($content);
                    }

                    $collection->add($blockContent);
                }

                return $collection;
            }
        ));
    }

    /**
     * Checks whether the submitted content is already part of the original collection.
     *
     * @param  Content $content
     * @return bool
     */
    protected function hasContent(Content $content)
    {
        foreach ($this->options['data'] as $blockContent) {
            if ($content->getId() == $blockContent->getContent()->getId()) {
                return $blockContent;
            }
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return 'entity';
    }

    /**
     * {@inheritDoc}
     *
     * @deprecated
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * {@inheritDoc}
     */
    public function getBlockPrefix()
    {
        return 'content_list_picker';
    }
}
