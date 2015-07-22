<?php

namespace Opifer\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\EavBundle\Model\MediaInterface;
use Opifer\MediaBundle\Model\Media as BaseMedia;

/**
 * @ORM\MappedSuperclass(repositoryClass="Opifer\MediaBundle\Model\MediaRepository")
 * @ORM\Table(name="media")
 */
class Media extends BaseMedia implements MediaInterface
{
}
