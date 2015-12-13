<?php

namespace Opifer\CmsBundle\Model;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\AdvancedEncoderBundle\Security\Encoder\EncoderAwareInterface;
use FOS\UserBundle\Model\GroupInterface;
use FOS\UserBundle\Model\User as FOSUser;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\MappedSuperclass
 *
 * @UniqueEntity("username")
 * @UniqueEntity("email")
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 *
 * Implements the EncoderAwareInterface to allow different password encoders for
 * legacy or new users.
 */
abstract class User extends FOSUser implements EncoderAwareInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
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
     * @ORM\Column(name="facebook_id", type="string", length=255, nullable=true)
     */
    protected $facebookId;

    /**
     * @ORM\Column(name="facebook_access_token", type="string", length=255, nullable=true)
     */
    protected $facebookAccessToken;

    /**
     * @var string
     *
     * @ORM\Column(name="google_id", type="string", length=255, nullable=true)
     */
    protected $googleId;

    /**
     * @ORM\Column(name="google_access_token", type="string", length=255, nullable=true)
     */
    protected $googleAccessToken;

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
     * @ORM\OneToMany(targetEntity="Opifer\CmsBundle\Entity\Content", mappedBy="author")
     */
    protected $contents;

    /**
     * @ORM\Column(name="encoder", type="string", length=255)
     */
    protected $encoder = 'default';

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

    public function serialize()
    {
        return serialize(array($this->facebookId, parent::serialize()));
    }

    public function unserialize($data)
    {
        list($this->facebookId, $parentData) = unserialize($data);
        parent::unserialize($parentData);
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
     * Get expires at
     *
     * @return DateTime
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * Get credentials expire at
     *
     * @return DateTime
     */
    public function getCredentialsExpireAt()
    {
        return $this->credentialsExpireAt;
    }

    /**
     * @param string $facebookId
     *
     * @return User
     */
    public function setFacebookId($facebookId)
    {
        $this->facebookId = $facebookId;

        return $this;
    }

    /**
     * Get Facebook ID
     *
     * @return string
     */
    public function getFacebookId()
    {
        return $this->facebookId;
    }

    /**
     * @param string $facebookAccessToken
     *
     * @return User
     */
    public function setFacebookAccessToken($facebookAccessToken)
    {
        $this->facebookAccessToken = $facebookAccessToken;

        return $this;
    }

    /**
     * Get FacebookAccessToken
     *
     * @return string
     */
    public function getFacebookAccessToken()
    {
        return $this->facebookAccessToken;
    }

    /**
     * @param string $googleId
     *
     * @return void
     */
    public function setGoogleId($googleId)
    {
        $this->googleId = $googleId;

        return $this;
    }

    /**
     * Get google ID
     *
     * @return string
     */
    public function getGoogleId()
    {
        return $this->googleId;
    }

    /**
     * @param string $googleAccessToken
     *
     * @return void
     */
    public function setGoogleAccessToken($googleAccessToken)
    {
        $this->googleAccessToken = $googleAccessToken;
    }

    /**
     * Get googleAccessToken
     *
     * @return string
     */
    public function getGoogleAccessToken()
    {
        return $this->googleAccessToken;
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
     * Set encoder
     *
     * @param string $encoder
     */
    public function setEncoder($encoder)
    {
        $this->encoder = $encoder;

        return $this;
    }

    public function getEncoder()
    {
        return $this->encoder;
    }

    /**
     * Get encoder name
     *
     * This is part of the EncoderAwareInterface
     *
     * @return string
     */
    public function getEncoderName()
    {
        return $this->encoder;
    }

    /**
     * Get created at
     *
     * @return datetime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Get updated at
     *
     * @return datetime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set deletedAt
     *
     * @param  \DateTime $deletedAt
     * @return File
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
