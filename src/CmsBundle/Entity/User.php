<?php

namespace Opifer\CmsBundle\Entity;

use APY\DataGridBundle\Grid\Mapping as GRID;
use Doctrine\ORM\Mapping as ORM;
use Opifer\CmsBundle\Model\User as BaseUser;

/**
 * The User class, extending our base User model.
 *
 * These classes are separated, to make the entity extendable. The User model is
 * an abstract MappedSuperclass which should be extended.
 *
 * @ORM\Entity
 * @ORM\Table(name="users")
 * @GRID\Source(columns="id, username, email")
 */
class User extends BaseUser
{
}
