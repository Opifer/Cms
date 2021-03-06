<?php

namespace Opifer\ContentBundle\Model;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Opifer\ContentBundle\Block\BlockManager;
use Opifer\ContentBundle\Exception\NestedContentFormException;
use Opifer\ContentBundle\Provider\BlockProviderInterface;
use Opifer\EavBundle\Entity\NestedValue;
use Opifer\EavBundle\Form\Type\NestedType;
use Opifer\EavBundle\Manager\EavManager;
use Pagerfanta\Adapter\DoctrineORMAdapter;

use Pagerfanta\Pagerfanta;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class ContentManager implements ContentManagerInterface, BlockProviderInterface
{
    /** @var EntityManager */
    protected $em;

    /** @var FormFactoryInterface */
    protected $formFactory;

    /** @var EavManager */
    protected $eavManager;

    /** @var string */
    protected $class;

    /** @var string */
    protected $templateClass;

    /**
     * Constructor.
     *
     * @param EntityManagerInterface $em
     * @param FormFactoryInterface $formFactory
     * @param EavManager $eavManager
     * @param string $class
     * @param string $templateClass
     *
     * @throws \Exception If the passed content class is no subclass of ContentInterface
     */
    public function __construct(EntityManagerInterface $em, FormFactoryInterface $formFactory, EavManager $eavManager, $class, $templateClass)
    {
        if (!is_subclass_of($class, 'Opifer\ContentBundle\Model\ContentInterface')) {
            throw new \Exception($class .' must implement Opifer\ContentBundle\Model\ContentInterface');
        }

        $this->em = $em;
        $this->formFactory = $formFactory;
        $this->eavManager = $eavManager;
        $this->class = $class;
        $this->templateClass = $templateClass;
    }

    /**
     * {@inheritdoc}
     */
    public function initialize()
    {
        $class = $this->getClass();

        return new $class();
    }

    /**
     * Get the class
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * {@inheritdoc}
     */
    public function getRepository()
    {
        return $this->em->getRepository($this->getClass());
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBySlug($slug)
    {
        return $this->getRepository()->findOneBySlug($slug);
    }

    /**
     * Get the content by a reference
     *
     * If the passed reference is a numeric, it must be the content ID from a
     * to-be-updated content item.
     * If not, the reference must be the template name for a to-be-created
     * content item.
     *
     * @param int|string $reference
     *
     * @return ContentInterface
     */
    public function getContentByReference($reference)
    {
        if (is_numeric($reference)) {
            // If the reference is numeric, it must be the content ID from an existing
            // content item, which has to be updated.
            $nestedContent = $this->getRepository()->find($reference);
        } else {
            // If not, $reference is a template name for a to-be-created content item.
            $template = $this->em->getRepository($this->templateClass)->findOneByName($reference);

            $nestedContent = $this->eavManager->initializeEntity($template);
            $nestedContent->setNestedDefaults();
        }

        return $nestedContent;
    }

    public function updateChildren(ContentInterface $content, $force = false)
    {
        $uow = $this->em->getUnitOfWork();
        $uow->computeChangeSets();

        $changeset = $uow->getEntityChangeSet($content);

        if (isset($changeset['locale']) && $changeset['locale'][0] !== $changeset['locale'][1]) {
            $children = $content->getChildren();
            foreach ($children as $child) {
                $child->setLocale($content->getLocale());

                $this->updateChildren($child);
            }
        }

        if (isset($changeset['parent']) || $force) {
            $children = $content->getChildren();
            foreach ($children as $child) {
                $child->setUpdatedAt(new \DateTime());
                $this->updateChildren($child, true);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function save(ContentInterface $content)
    {
        if (!$content->getId()) {
            $this->em->persist($content);
        } else {
            $this->updateChildren($content);

            $cacheDriver = $this->em->getConfiguration()->getResultCacheImpl();
            $cacheDriver->deleteAll();
        }

        $this->em->flush();

        return $content;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($content)
    {
        if (! is_array($content)) {
            $content = [$content];
        }

        if (! is_object($content[0])) {
            $content = $this->getRepository()->findByIds($content);
        }

        foreach ($content as $item) {
            $this->em->remove($item);
        }

        // Clear the result cache
        $cacheDriver = $this->em->getConfiguration()->getResultCacheImpl();
        $cacheDriver->deleteAll();

        $this->em->flush();
    }

    /**
     * Duplicate a content item
     *
     * @param ContentInterface $content
     *
     * @return ContentInterface $duplicate
     */
    public function duplicate(ContentInterface $content)
    {
        //duplicate content
        $duplicatedContent = clone $content;
        $duplicatedContent->setTitle(sprintf("%s copy %s", $duplicatedContent->getTitle(), date('r')));
        $duplicatedContent->setSlug(null);
        $duplicatedContent->setActive(false);
        $duplicatedContent->setCreatedAt(new \DateTime());

        //get valueset to clone
        if (null !== $valueset = $content->getValueSet()) {
            //clone valueset
            $duplicatedValueset = clone $valueset;

            $this->detachAndPersist($duplicatedValueset);
            $duplicatedContent->setValueSet($duplicatedValueset);
        }

        $duplicatedContent->setBlocks(null);
        $this->detachAndPersist($duplicatedContent);

        if ($valueset) {
            //iterate values, clone each and assign duplicate valueset to it
            foreach ($valueset->getValues() as $value) {

                //skip empty attributes
                if (is_null($value->getId())) continue;

                $duplicatedValue = clone ($value);
                $duplicatedValue->setValueSet($duplicatedValueset);

                $this->detachAndPersist($duplicatedValue);
            }
        }
        $this->em->flush();

        return $duplicatedContent;
    }


    public function getEntityManager()
    {
        return $this->em;
    }

    /**
     * For cloning purpose
     *
     * @param ContentInterface|\Opifer\EavBundle\Model\ValueSetInterface|\Opifer\EavBundle\Entity\Value $entity
     */
    private function detachAndPersist($entity)
    {
        $this->em->detach($entity);
        $this->em->persist($entity);
    }

    /**
     * @inheritDoc
     */
    public function getBlockOwner($id)
    {
        return $this->getRepository()->find($id);
    }
}
