<?php

namespace Opifer\MailingListBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use APY\DataGridBundle\Grid\Mapping as Grid;

/**
 * MailingList.
 *
 * @ORM\Table(name="mailing_list")
 * @ORM\Entity(repositoryClass="Opifer\MailingListBundle\Repository\MailingListRepository")
 * @Grid\Source(columns="id, name, displayName, subscriptions.id:count, updatedAt, syncedAt", groupBy={"id"})
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
     * @Grid\Column(title="label.id", size="10", type="number")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     *
     * @Grid\Column(title="label.name")
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="display_name", type="string", length=255, nullable=true)
     *
     * @Grid\Column(title="label.display_name")
     */
    protected $displayName;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Opifer\MailingListBundle\Entity\Subscription", mappedBy="mailingList", cascade={"remove"})
     * @ORM\OrderBy({"createdAt" = "DESC"})
     * 
     * @Grid\Column(field="subscriptions.id:count", title="label.subscriptions")
     **/
    protected $subscriptions;

     /**
      * @var string
      *
      * @ORM\Column(name="provider", type="string", length=255, nullable=true)
      */
     protected $provider;

    /**
     * @var string
     *
     * @ORM\Column(name="remote_list_id", type="string", length=255, nullable=true)
     */
    protected $remoteListId;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime")
     */
    protected $updatedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     */
    protected $deletedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="synced_at", type="datetime", nullable=true)
     */
    protected $syncedAt;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name.
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
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set displayName.
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
     * Get displayName.
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * Set provider.
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
     * Get provider.
     *
     * @return string
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * Set createdAt.
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
     * Get createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt.
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
     * Get updatedAt.
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set deletedAt.
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
     * Get deletedAt.
     *
     * @return \DateTime
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * @return ArrayCollection
     */
    public function getSubscriptions()
    {
        return $this->subscriptions;
    }

    /**
     * @param ArrayCollection $subscriptions
     *
     * @return MailingList
     */
    public function setSubscriptions($subscriptions)
    {
        $this->subscriptions = $subscriptions;
        return $this;
    }

    /**
     * @return string
     */
    public function getRemoteListId()
    {
        return $this->remoteListId;
    }

    /**
     * @param string $remoteListId
     *
     * @return MailingList
     */
    public function setRemoteListId($remoteListId)
    {
        $this->remoteListId = $remoteListId;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getSyncedAt()
    {
        return $this->syncedAt;
    }

    /**
     * @param \DateTime $syncedAt
     *
     * @return MailingList
     */
    public function setSyncedAt($syncedAt)
    {
        $this->syncedAt = $syncedAt;
        return $this;
    }

    
}
