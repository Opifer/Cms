<?php

namespace Opifer\ContentBundle\Designer;


use Opifer\ContentBundle\Model\TemplateManager;
use Symfony\Component\Routing\RouterInterface;
use Doctrine\ORM\EntityManagerInterface;

class TemplateContext extends AbstractDesignContext
{

    /**
     * @var EntityManager
     */
    protected $em;

    public function __construct(RouterInterface $router, EntityManagerInterface $em)
    {
        $this->router = $router;
        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    public function load($id = 0, $version = 1)
    {
        $this->subject = $this->em->getRepository('OpiferContentBundle:Template')->find($id);
        $this->version = $version;

        if ( ! $this->subject) {
            throw $this->createNotFoundException('No template found for id ' . $id);
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getTitle()
    {
        return $this->subject->getDisplayName();
    }

    /**
     * {@inheritDoc}
     */
    public function getCaption()
    {
        return 'base.template';
    }

    /**
     * {@inheritDoc}
     */
    public function getPropertiesUrl()
    {
        return '';
    }

    /**
     * {@inheritDoc}
     */
    public function getCancelUrl()
    {
        return $this->router->generate('opifer_content_template_index');
    }

    /**
     * {@inheritDoc}
     */
    public function getCanvasUrl()
    {
        return $this->router->generate('opifer_content_contenteditor_view', ['id' => $this->subject->getBlock()->getId(), 'version' => $this->version]);
    }


    public function saveSubject()
    {
        $this->em->flush();

        return $this;
    }
}