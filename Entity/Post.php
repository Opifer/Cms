<?php

namespace Opifer\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use Opifer\EavBundle\Model\EntityInterface;
use Opifer\EavBundle\Model\ValueSetInterface;
use Opifer\EavBundle\Model\TemplateInterface;
use Opifer\FormBundle\Model\Post as BasePost;

/**
 * @ORM\Entity()
 * @ORM\Table(name="post")
 * @JMS\ExclusionPolicy("all")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class Post extends BasePost implements EntityInterface
{

}
