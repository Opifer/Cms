<?php

namespace Opifer\ContentBundle\Model;

use Doctrine\ORM\EntityManagerInterface;
use Opifer\ContentBundle\Exception\NestedContentFormException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

use Opifer\CrudBundle\Pagination\Paginator;
use Opifer\EavBundle\Form\Type\NestedType;
use Opifer\EavBundle\Manager\EavManager;
use Opifer\EavBundle\Entity\NestedValue;

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
     * @return \Doctrine\ORM\EntityRepository
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
     * Find content by alias
     *
     * @param string $alias
     *
     * @return ContentInterface
     */
    public function findOneByAlias($alias)
    {
        return $this->getRepository()->findOneByAlias($alias);
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
     * Handle the nested content forms
     *
     * @param ContentInterface $content
     * @param Request $request
     *
     * @throws \Exception
     */
    public function handleNestedContentForm(Request $request, ContentInterface $content)
    {
        $this->recursiveContentMapper($request, $content);
    }

    /**
     * Maps the formdata to the related nestedcontent item resursively
     *
     * @param ContentInterface $content
     * @param $request
     * @param int $level
     * @param string $parentKey
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function recursiveContentMapper(Request $request, ContentInterface $content, $level = 1, $parentKey = 'opifer_content')
    {
        $formdata = $request->request->all();

        foreach ($content->getNestedContentAttributes() as $attribute => $value) {

            $relatedformdata = $this->eavManager->getFormDataByLevel($formdata, $attribute, $level, $parentKey);

            // Store the original Ids, so we can check later what items were removed
            $ids = [];
            $oldIds = [];
            if ($formdata[$parentKey.'_valueset_namedvalues_'.$attribute] != '') {
                $oldIds = explode(',', $formdata[$parentKey.'_valueset_namedvalues_'.$attribute]);
            }

            $sort = 0;
            foreach ($relatedformdata as $key => $data) {
                $keys = $this->eavManager->parseNestedTypeName($key);

                $nestedContent = $this->getContentByReference($keys['reference']);
                $nestedContent->setSlug(md5(time() + rand()));

                $form = new NestedType($key);
                $form = $this->formFactory->create($form, $nestedContent);
                $form->handleRequest($request);

                $nestedContent->setNestedSort($sort);
                $sort++;

                // We do not check the standard isValid() method here, because our form
                // is not actually submitted.
                if (count($form->getErrors(true)) < 1) {
                    $this->em->persist($value);
                    $value->addNested($nestedContent);
                    $nestedContent->setNestedIn($value);
                    $this->save($nestedContent);

                    $ids[] = $nestedContent->getId();
                } else {
                    throw new NestedContentFormException('Something went wrong while saving nested content. Message: '. $form->getErrors());
                }

                $this->recursiveContentMapper($request, $nestedContent, $level+1, $key);
            }

            $this->remove(array_diff($oldIds, $ids));
        }

        return true;
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

    /**
     * {@inheritDoc}
     */
    public function save(ContentInterface $content)
    {
        if (!$content->getId()) {
            $this->em->persist($content);
        }

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
     * Duplicate a content item
     *
     * @param ContentInterface $content
     * @param NestedValue $nestedIn
     */
    public function duplicate(ContentInterface $content, NestedValue $nestedIn = null)
    {
        //get valueset to clone
        $valueset = $content->getValueSet();

        //clone valueset
        $duplicatedValueset = clone $valueset;

        $this->detachAndPersist($duplicatedValueset);

        //duplicate content
        $duplicatedContent = clone $content;
        $duplicatedContent->setSlug(null);
        $duplicatedContent->setValueSet($duplicatedValueset);

        if (!is_null($nestedIn)) {
            $duplicatedContent->setNestedIn($nestedIn);
        }

        $this->detachAndPersist($duplicatedContent);

        //iterate values, clone each and assign duplicate valueset to it
        foreach ($valueset->getValues() as $value) {

            //skip empty attributes
            if (is_null($value->getId())) continue;

            $duplicatedValue = clone ($value);
            $duplicatedValue->setValueSet($duplicatedValueset);

            $this->detachAndPersist($duplicatedValue);

            //if type nested, find content that has nested_in value same as id of value
            if ($value instanceof \Opifer\EavBundle\Entity\NestedValue) {
                $nestedContents = $this->getRepository()->findby(['nestedIn' => $value->getId()]);

                foreach ($nestedContents as $nestedContent) {
                    $this->duplicate($nestedContent, $duplicatedValue);
                }
            }
        }
        $this->em->flush();

        return $duplicatedContent->getId();
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
}
