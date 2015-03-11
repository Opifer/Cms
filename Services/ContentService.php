<?php

namespace Opifer\ContentBundle\Services;

use Opifer\ContentBundle\Model\ContentManager;
use Doctrine\ORM\EntityManager;

/**
 * class ContentDuplicator
 *
 * @author Vladimir Djuricic
 */
class ContentService
{
    private $content_manager;
    private $em;

    /**
     *
     * @param ContentManager $content_manager
     * @param EntityManager $doctrine
     */
    public function __construct(ContentManager $content_manager, EntityManager $doctrine)
    {
        $this->em = $doctrine;
        $this->content_manager = $content_manager;
    }
    
    /**
     * @param Opifer\CmsBundle\Entity\Content $content
     * @param \Opifer\EavBundle\Entity\Value $nested_in
     */
    public function duplicate($content, $nested_in=null)
    {
        $contentManager = $this->content_manager;

        //get valueset to clone
        $valueset = $content->getValueSet();

        //clone valueset
        $duplicated_valueset = clone $valueset;

        $this->em->detach($duplicated_valueset);
        $this->em->persist($duplicated_valueset);
        $this->em->flush();

        //duplicate content
        $duplicated_content = clone $content;
        $duplicated_content->setValueSet($duplicated_valueset);

        if (!is_null($nested_in)) {
            $duplicated_content->setNestedIn($nested_in);
        }

        $this->em->detach($duplicated_content);
        $this->em->persist($duplicated_content);
        $this->em->flush();

        //iterate values, clone each and assign duplicate valueset to it
        foreach ($valueset->getValues() as $value) {

            //skip empty attributes
            if (is_null($value->getId())) continue;

            $duplicated_value = clone ($value);

            $duplicated_value->setValueSet($duplicated_valueset);

            $this->em->detach($duplicated_value);
            $this->em->persist($duplicated_value);
            $this->em->flush();

            //if type nested, find content that has nested_in value same as id of value
            if ($value instanceof \Opifer\EavBundle\Entity\NestedValue) {
                $nested_contents = $contentManager->getRepository()->findby(['nestedIn' => $value->getId()]);

                foreach ($nested_contents as $nested_content) {
                    $this->duplicate($nested_content, $duplicated_value);
                }
            }
        }

        return $duplicated_content->getId();
    }
}