<?php

namespace Opifer\ContentBundle\Model;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

use Opifer\EavBundle\Form\Type\NestedContentType;
use Opifer\EavBundle\Manager\EavManager;

class ContentManager
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
     * Save nested content forms from request and return the added/updated ids
     *
     * @param Request $request
     *
     * @return array
     */
    public function saveNestedForm($attribute, Request $request)
    {
        $formdata = $request->request->all();
        $oldIds = explode(',', $formdata['eav_nested_content_value']);
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

                // In case of newly added nested content, we need to add an index
                // to the form type name, to avoid same template name conflicts.
                $key = $key.NestedContentType::NAME_SEPARATOR.$keys[3];
            }

            $nestedContentForm = $this->formFactory->create(new NestedContentType($attribute, $key), $nestedContent);
            $nestedContentForm->handleRequest($request);

            $nestedContent->setNestedSort($sortCount);
            $sortCount++;

            if ($nestedContentForm->isValid()) {
                $this->em->persist($nestedContent);
                $this->em->flush();

                $ids[] = $nestedContent->getId();
                $collection->add($nestedContent);
            } else {
                // @todo show the user a decent error message
                throw new \Exception('Something went wrong while saving nested content.');
            }
        }

        // Remove the deleted items from the database
        $this->remove(array_diff($oldIds, $ids));

        return $collection;
    }

    /**
     * Save content
     *
     * @param  ContentInterface $content
     *
     * @return ContentInterface
     */
    public function save(ContentInterface $content)
    {
        $this->em->persist($content);
        $this->em->flush();

        return $content;
    }

    /**
     * Remove content
     *
     * @param array|integer $content
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
}
