<?php

namespace Opifer\CmsBundle\Entity;

use APY\DataGridBundle\Grid\Mapping as GRID;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use Opifer\FormBundle\Model\Form as BaseForm;

/**
 * @ORM\Entity(repositoryClass="Opifer\FormBundle\Model\FormRepository")
 * @ORM\Table(name="form")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @GRID\Source(columns="id, name")
 * @JMS\ExclusionPolicy("all")
 */
class Form extends BaseForm
{
    /**
     * @var int
     *
     * @JMS\Expose
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @GRID\Column(title="Id", size="10", type="number")
     */
    protected $id;
}
