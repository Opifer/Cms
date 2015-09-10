<?php

namespace Opifer\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Opifer\FormBundle\Model\Form as BaseForm;

/**
 * @ORM\Entity()
 * @ORM\Table(name="form")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class Form extends BaseForm
{

}
