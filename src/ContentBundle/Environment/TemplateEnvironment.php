<?php
/**
 * Created by PhpStorm.
 * User: dylan
 * Date: 22/01/16
 * Time: 11:03
 */

namespace Opifer\ContentBundle\Environment;

use Opifer\ContentBundle\Entity\Template;

class TemplateEnvironment extends Environment
{
    /**
     * @var Template
     */
    protected $template;

    /**
     * {@inheritDoc}
     */
    public function load($id = 0, $version = false)
    {
        $this->template = $this->em->getRepository('OpiferContentBundle:Template')->find($id);

        if ($version) {
            $this->versionMap[$this->template->getBlock()->getId()] = $version;
        }

        if ( ! $this->template) {
            throw $this->createNotFoundException('No template found for id ' . $id);
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    protected function getBlockOwners()
    {
        $blockOwners = [];
        if ($this->template->getBlock()) {
            $blockOwners[] = $this->template->getBlock();
        }

        if ($this->template->getParent()) {
            $blockOwners[] = $this->template->getParent()->getBlock();
        }

        return $blockOwners;
    }

    /**
     * @return array
     */
    public function getViewParameters()
    {
        $parameters = array(
            'template' => $this->template,
        );

        return array_merge(parent::getViewParameters(), $parameters);
    }

    /**
     * @return string
     */
    public function getView()
    {
        return $this->template->getView() ;
    }

    /**
     * @return Template
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param Template $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }


}