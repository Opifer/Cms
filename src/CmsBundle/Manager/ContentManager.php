<?php

namespace Opifer\CmsBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Opifer\ContentBundle\Model\ContentInterface;
use Opifer\ContentBundle\Model\ContentManager as BaseContentManager;
use Opifer\EavBundle\Manager\EavManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class ContentManager extends BaseContentManager
{
    /** @var TokenStorage */
    protected $tokenStorage;

    public function __construct(EntityManagerInterface $em, FormFactoryInterface $formFactory, EavManager $eavManager, $class, $templateClass, TokenStorage $tokenStorage)
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
}
