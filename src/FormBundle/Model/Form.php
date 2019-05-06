<?php

namespace Opifer\FormBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use Opifer\CmsBundle\Entity\Locale;
use Opifer\EavBundle\Model\SchemaInterface;

/**
 * @ORM\MappedSuperclass(repositoryClass="Opifer\FormBundle\Model\FormRepository")
 * @JMS\ExclusionPolicy("all")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class Form implements FormInterface
{
    /**
     * @var int
     *
     * @JMS\Expose
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=128)
     */
    protected $name;

    /**
     * @var SchemaInterface
     *
     * @ORM\ManyToOne(targetEntity="Opifer\EavBundle\Model\SchemaInterface", cascade={"persist"})
     * @ORM\JoinColumn(name="schema_id", referencedColumnName="id")
     */
    protected $schema;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Opifer\FormBundle\Model\PostInterface", mappedBy="form")
     * @ORM\OrderBy({"id" = "DESC"})
     */
    protected $posts;

    /**
     * @var string
     *
     * @ORM\Column(name="notification_email", type="string", length=255, nullable=true)
     */
    protected $notificationEmail;

    /**
     * @var bool
     *
     * @ORM\Column(name="requires_confirmation", type="boolean")
     */
    protected $requiresConfirmation = false;

    /**
     * @var string
     *
     * @ORM\Column(name="redirect_url", type="string", length=255, nullable=true)
     */
    protected $redirectUrl;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
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
     * @ORM\Column(name="deletedAt", type="datetime", nullable=true)
     */
    protected $deletedAt;

    /**
     * @var bool
     *
     * @ORM\Column(name="recaptcha_enabled", type="boolean")
     */
    protected $recaptchaEnabled = false;

    /**
     * @Gedmo\Locale
     */
    protected $locale;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->posts = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return SchemaInterface
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * @param SchemaInterface $schema
     *
     * @return Form
     */
    public function setSchema(SchemaInterface $schema)
    {
        $this->schema = $schema;

        return $this;
    }

    /**
     * @param string $notificationEmail
     *
     * @return Form
     */
    public function setNotificationEmail($notificationEmail)
    {
        $this->notificationEmail = $notificationEmail;

        return $this;
    }

    /**
     * @return string
     */
    public function getNotificationEmail()
    {
        return $this->notificationEmail;
    }

    /**
     * @return bool
     */
    public function requiresConfirmation()
    {
        return $this->requiresConfirmation;
    }

    /**
     * @param bool $requiresConfirmation
     *
     * @return Form
     */
    public function setRequiresConfirmation($requiresConfirmation)
    {
        $this->requiresConfirmation = $requiresConfirmation;

        return $this;
    }

    /**
     * @param string $redirectUrl
     *
     * @return Form
     */
    public function setRedirectUrl($redirectUrl)
    {
        $this->redirectUrl = $redirectUrl;

        return $this;
    }

    /**
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    /**
     * @return ArrayCollection
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * @param PostInterface $post
     *
     * @return $this
     */
    public function addPost(PostInterface $post)
    {
        $this->posts[] = $post;

        return $this;
    }

    /**
     * @param PostInterface $post
     *
     * @return $this
     */
    public function removePost(PostInterface $post)
    {
        $this->posts->removeElement($post);

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return \DateTime
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * @param \DateTime $deletedAt
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;
    }

    /**
     * @return bool
     */
    public function isRecaptchaEnabled()
    {
        return $this->recaptchaEnabled;
    }

    /**
     * @param bool $recaptchaEnabled
     *
     * @return Form
     */
    public function setRecaptchaEnabled($recaptchaEnabled)
    {
        $this->recaptchaEnabled = $recaptchaEnabled;

        return $this;
    }

    /**
     * @return Locale
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param Locale $locale
     * @return $this
     */
    public function setLocale(Locale $locale)
    {
        $this->locale = $locale;

        return $this;
    }
}
