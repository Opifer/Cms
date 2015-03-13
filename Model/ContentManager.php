<?php

namespace Opifer\ContentBundle\Model;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

use Opifer\CrudBundle\Pagination\Paginator;
use Opifer\EavBundle\Form\Type\NestedContentType;
use Opifer\EavBundle\Manager\EavManager;
use Opifer\EavBundle\Entity\NestedValue;
use Opifer\CmsBundle\Entity\Content;

class ContentManager implements ContentManagerInterface
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
     * Constructor
     *
     * @param EntityManagerInterface $em
     * @param FormFactoryInterface   $formFactory
     * @param EavManager             $eavManager
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
     * Get the class
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Get repository
     *
     * @return Doctrine\ORM\EntityRepository
     */
    public function getRepository()
    {
        return $this->em->getRepository($this->getClass());
    }

    /**
     * {@inheritDoc}
     */
    public function getPaginatedByRequest(Request $request)
    {
        $qb = $this->getRepository()->getQueryBuilderFromRequest($request);

        $page = ($request->get('p')) ? $request->get('p') : 1;
        $limit = ($request->get('limit')) ? $request->get('limit') : 25;

        return new Paginator($qb, $limit, $page);
    }

    /**
     * {@inheritDoc}
     */
    public function findOneBySlug($slug)
    {
        return $this->getRepository()->findOneBySlug($slug);
    }
    
    /**
     * Find published content
     *
     * @param string $slug
     *
     * @return ContentInterface
     */
    public function findActiveBySlug($slug)
    {
        return $this->getRepository()->findActiveBySlug($slug);
    }
    
    /**
     * Find published content by alias
     *
     * @param string $alias
     *
     * @return ContentInterface
     */
    public function findActiveByAlias($alias)
    {
        return $this->getRepository()->findActiveByAlias($alias);
    }

    /**
     * {@inheritDoc}
     */
    public function mapNested(ContentInterface $content, Request $request)
    {
        $nested = [];
        foreach ($content->getNestedContentAttributes() as $attribute => $value) {
            $nested = $this->saveNestedForm($attribute, $request);
            foreach ($nested as $nestedContent) {
                $this->em->persist($value);
                $value->addNested($nestedContent);
                $nestedContent->setNestedIn($value);

                $this->save($nestedContent);

                $nested[] = $nestedContent;
            }
        }

        return $nested;
    }

    /**
     * Save nested content forms from request and return the added/updated ids
     *
     * @param Request $request
     *
     * @return array
     */
    public function saveNestedForm($attribute, Request $request)
    {
        $formdata = $request->request->all();

        $oldIds = explode(',', $formdata['eav_nested_content_value_'.$attribute]);
        $ids = [];
        $collection = new ArrayCollection();

        $sortCount = 0;
        foreach ($formdata as $key => $nestedContent) {
            $keys = explode(NestedContentType::NAME_SEPARATOR, $key);

            if (count($keys) < 2 || $keys[1] !== $attribute) {
                continue;
            }

            // In case of a newly added nested content item, the $keys array has
            // a fourth value, which is the form index.
            list($formType, $attribute, $key) = $keys;

            if (is_numeric($key)) {
                // If the key is numeric, it must be the content ID from an existing
                // content item, which has to be updated.
                $nestedContent = $this->getRepository()->find($key);
            } else {
                // If not, $key is a template name for a to-be-created content item.
                $template = $this->em->getRepository($this->templateClass)->findOneByName($key);

                $nestedContent = $this->eavManager->initializeEntity($template);
                $nestedContent->setNestedDefaults();
            }

            // Add an index to the form type name, to avoid same template name conflicts.
            $key = $key.NestedContentType::NAME_SEPARATOR.$keys[3];

            $nestedContentForm = $this->formFactory->create(new NestedContentType($attribute, $key), $nestedContent);
            $nestedContentForm->handleRequest($request);

            $nestedContent->setNestedSort($sortCount);
            $sortCount++;

            // We do not check the standard isValid() method here, cause our form
            // is not actually submitted.
            if (count($nestedContentForm->getErrors(true)) < 1) {
                $nestedContent = $this->save($nestedContent);

                $ids[] = $nestedContent->getId();
                $collection->add($nestedContent);
            } else {
                // @todo show the user a decent error message
                throw new \Exception('Something went wrong while saving nested content. Message: '. $nestedContentForm->getErrors());
            }
        }

        // Remove the deleted items from the database
        $this->remove(array_diff($oldIds, $ids));

        return $collection;
    }

    /**
     * {@inheritDoc}
     */
    public function save(ContentInterface $content)
    {
        $this->em->persist($content);
        $this->em->flush();

        return $content;
    }

    /**
     * {@inheritDoc}
     */
    public function remove($content)
    {
        if (!is_array($content)) {
            $content = [$content];
        }

        $content = $this->getRepository()->findByIds($content);
        foreach ($content as $item) {
            $this->em->remove($item);
        }

        $this->em->flush();
    }

    /**
     * @param Content $content
     * @param NestedValue $nested_in
     */
    public function duplicate(Content $content, NestedValue $nested_in = null)
    {
        //get valueset to clone
        $valueset = $content->getValueSet();

        //clone valueset
        $duplicated_valueset = clone $valueset;

        $this->detachAndPersist($duplicated_valueset);

        //duplicate content
        $duplicated_content = clone $content;
        $duplicated_content->setValueSet($duplicated_valueset);

        if (!is_null($nested_in)) {
            $duplicated_content->setNestedIn($nested_in);
        }

        $this->detachAndPersist($duplicated_content);

        //iterate values, clone each and assign duplicate valueset to it
        foreach ($valueset->getValues() as $value) {

            //skip empty attributes
            if (is_null($value->getId())) continue;

            $duplicated_value = clone ($value);
            $duplicated_value->setValueSet($duplicated_valueset);

            $this->detachAndPersist($duplicated_value);

            //if type nested, find content that has nested_in value same as id of value
            if ($value instanceof \Opifer\EavBundle\Entity\NestedValue) {
                $nested_contents = $this->getRepository()->findby(['nestedIn' => $value->getId()]);

                foreach ($nested_contents as $nested_content) {
                    $this->duplicate($nested_content, $duplicated_value);
                }
            }
        }
        $this->em->flush();

        return $duplicated_content->getId();
    }

    /**
     * For cloning purpose
     * @param Opifer\ContentBundle\Model\Content|Opifer\EavBundle\Model\ValueSet|Opifer\EavBundle\Entity\Value $entity
     */
    private function detachAndPersist($entity)
    {
        $this->em->detach($entity);
        $this->em->persist($entity);
    }
}
