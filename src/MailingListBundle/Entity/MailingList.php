<?php
namespace Opifer\MailingListBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use APY\DataGridBundle\Grid\Mapping as GRID;
/**
 * MailingList
 *
 * @ORM\Table(name="mailing_list")
 * @ORM\Entity(repositoryClass="Opifer\MailingListBundle\Repository\MailingListRepository")
 * @GRID\Source(columns="id, name, displayName")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class MailingList
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @GRID\Column(title="label.id", size="10", type="number")
     */
    protected $id;
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     *
     * @GRID\Column(title="label.name")
     */
    protected $name;
    /**
     * @var string
     *
     * @ORM\Column(name="displayName", type="string", length=255, nullable=true)
     *
     * @GRID\Column(title="label.display_name")
     */
    protected $displayName;
    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Opifer\MailingListBundle\Entity\Subscription", mappedBy="mailingList", cascade={"remove"})
     * @ORM\OrderBy({"createdAt" = "DESC"})
     **/
    protected $subscriptions;
    /**
      * @var string
      *
      * @ORM\Column(name="provider", type="string", length=255)
      *
      */
     protected $provider;
    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="createdAt", type="datetime", nullable=true)
     */
    protected $createdAt;
    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updatedAt", type="datetime")
     */
    protected $updatedAt;
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deletedAt", type="datetime", nullable=true)
     */
    protected $deletedAt;
    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * Set name
     *
     * @param string $name
     *
     * @return MailingList
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * Set displayName
     *
     * @param string $displayName
     *
     * @return MailingList
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
        return $this;
    }
    /**
     * Get displayName
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }
    /**
     * Set provider
     *
     * @param string $provider
     *
     * @return MailingList
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;
        return $this;
    }
    /**
     * Get provider
     *
     * @return string
     */
    public function getProvider()
    {
        return $this->provider;
    }
    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return MailingList
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }
    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return MailingList
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
    /**
     * Set deletedAt
     *
     * @param \DateTime $deletedAt
     *
     * @return MailingList
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;
        return $this;
    }
    /**
     * Get deletedAt
     *
     * @return \DateTime
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }
}