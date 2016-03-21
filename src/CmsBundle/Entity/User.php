<?php

namespace Opifer\CmsBundle\Entity;

use APY\DataGridBundle\Grid\Mapping as GRID;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\GroupInterface;
use FOS\UserBundle\Model\User as FOSUser;
use Gedmo\Mapping\Annotation as Gedmo;
use Opifer\MediaBundle\Model\MediaInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\MappedSuperclass
 * @ORM\Entity
 *
 * @UniqueEntity("username")
 * @UniqueEntity("email")
 * @ORM\Entity()
 * @ORM\Table(name="users")
 * @GRID\Source(columns="id, username, email")
 */
class User extends FOSUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @GRID\Column(title="Id", size="10", type="number")
     */
    protected $id;

    /**
     * @var string
     *
     * ORM mapping is handled by the BaseUser
     */
    protected $username;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=255, nullable=true)
     */
    protected $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=255, nullable=true)
     */
    protected $lastName;

    /**
     * @var MediaInterface
     *
     * @ORM\ManyToOne(targetEntity="Opifer\MediaBundle\Model\MediaInterface")
     * @ORM\JoinColumn(name="avatar_id", referencedColumnName="id")
     */
    protected $avatar;

    /**
     * @var string
     *
     * ORM mapping is handled by the BaseUser
     *
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    protected $email;

    /**
     * @var string
     *
     * Plain password. Used for model validation. Must not be persisted.
     */
    protected $plainPassword;

    /**
     * @var string
     *
     * @ORM\Column(name="company_id", type="string", length=255, nullable=true)
     */
    protected $company;

    /**
     * @var string
     *
     * @ORM\Column(name="job_position", type="string", length=255, nullable=true)
     */
    protected $jobPosition;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Opifer\CmsBundle\Entity\Group")
     * @ORM\JoinTable(name="users_groups",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    protected $groups;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Opifer\ContentBundle\Model\ContentInterface", mappedBy="author")
     */
    protected $contents;

    /**
     * Created at
     *
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * Updated at
     *
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
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->groups = new ArrayCollection();
        $this->contents = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set first name
     *
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get first name
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set last name
     *
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get last name
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Get the full name
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->firstName.' '.$this->lastName;
    }

    /**
     * @return MediaInterface
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * @param MediaInterface $avatar
     * @return User
     */
    public function setAvatar(MediaInterface $avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param string $company
     * @return User
     */
    public function setCompany($company)
    {
        $this->company = $company;
        return $this;
    }

    /**
     * @return string
     */
    public function getJobPosition()
    {
        return $this->jobPosition;
    }

    /**
     * @param string $jobPosition
     * @return User
     */
    public function setJobPosition($jobPosition)
    {
        $this->jobPosition = $jobPosition;
        return $this;
    }

    /**
     * Get expires at
     *
     * @return \DateTime
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * Get credentials expire at
     *
     * @return \DateTime
     */
    public function getCredentialsExpireAt()
    {
        return $this->credentialsExpireAt;
    }

    /**
     * Add groups
     *
     * @param \Opifer\CmsBundle\Entity\Group $groups
     *
     * @return User
     */
    public function addGroup(GroupInterface $groups)
    {
        $this->groups[] = $groups;

        return $this;
    }

    /**
     * Remove groups
     *
     * @param \Opifer\CmsBundle\Entity\Group $groups
     * @return $this
     */
    public function removeGroup(GroupInterface $groups)
    {
        $this->groups->removeElement($groups);

        return $this;
    }

    /**
     * Get groups
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * Returns all related content items
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * Get created at
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Get updated at
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
     * @param  \DateTime $deletedAt
     * @return $this
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
