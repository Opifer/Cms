<?php

namespace Opifer\MediaBundle\Tests;

use Doctrine\ORM\Mapping as ORM;
use Opifer\MediaBundle\Model\Media as BaseMedia;

/**
 * @ORM\Entity(repositoryClass="Opifer\MediaBundle\Repository\MediaRepository")
 * @ORM\Table(name="media")
 */
class Media extends BaseMedia
{

}
