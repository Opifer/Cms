<?php

namespace Opifer\CmsBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Opifer\CmsBundle\Entity\Content;
use Opifer\CmsBundle\Entity\ValueSet;
use Opifer\ContentBundle\Model\ContentInterface;
use Opifer\ContentBundle\Model\ContentManager as BaseContentManager;
use Opifer\EavBundle\Manager\EavManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ContentManager extends BaseContentManager
{
    /** @var TokenStorage */
    protected $tokenStorage;

    public function __construct(EntityManagerInterface $em, FormFactoryInterface $formFactory, EavManager $eavManager, $class, $templateClass, TokenStorageInterface $tokenStorage)
    {
        parent::__construct($em, $formFactory, $eavManager, $class, $templateClass);

        $this->tokenStorage = $tokenStorage;
    }

    public function save(ContentInterface $content)
    {
        if (!$content->getId()) {
            $content->setAuthor($this->tokenStorage->getToken()->getUser());
        }

        return parent::save($content);
    }

    public function getEntityManager()
    {
        return $this->em;
    }

    /**
     * Creates a ValueSet entity for Content when missing and there is a ContentType set.
     *
     * @param Content $content
     *
     * @return Content
     */
    public function createMissingValueSet(Content $content)
    {
        if ($content->getContentType() !== null && $content->getValueSet() === null)
        {
            $valueSet = new ValueSet();
            $this->em->persist($valueSet);

            $valueSet->setSchema($content->getContentType()->getSchema());
            $content->setValueSet($valueSet);

            $content = $this->save($content);
        }

        return $content;
    }
}
