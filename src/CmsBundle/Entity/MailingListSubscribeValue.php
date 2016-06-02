<?php

namespace Opifer\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\EavBundle\Entity\BooleanValue;
use Opifer\EavBundle\Model\ValueInterface;

/**
 * MailingList Subscribe Value.
 */
class MailingListSubscribeValue extends BooleanValue implements ValueInterface
{

}
