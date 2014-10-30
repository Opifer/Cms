<?php

namespace Opifer\ContentBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Layout
 *
 * @ORM\MappedSuperclass
 *
 * @JMS\ExclusionPolicy("all")
 */
class Layout implements LayoutInterface
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * 
     * @JMS\Expose
     * @JMS\Type("integer")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=128)
     * @Assert\NotBlank()
     * 
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     * 
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $description;

    /**
     * @var string
     *
     * @ORM\Column(name="filename", type="string", length=128)
     * @Assert\NotBlank()
     * 
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $filename;

    /**
     * @var array
     *
     * @JMS\Expose
     * @JMS\Type("array<string, array<Opifer\ContentBundle\Model\Layout>>")
     */
    protected $placeholders;

    /**
     * @var array
     *
     * @ORM\Column(name="placeholderkeys", type="simple_array", nullable=true)
     */
    protected $placeholderkeys;

    /**
     * @var string
     *
     * @ORM\Column(name="action", type="string", length=128, nullable=true)
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $action;

    /**
     * @var integer
     *
     * @JMS\Expose
     * @JMS\Accessor(getter="getContentId",setter="setContentId")
     * @JMS\Type("integer")
     */
    protected $content;

    /**
     * @ORM\ManyToOne(targetEntity="Opifer\EavBundle\Model\TemplateInterface", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(name="parameterset_id", referencedColumnName="id")
     * @JMS\Expose
     */
    protected $parameterSet;

    /**
     * $parameters is a property that holds the serialized parameter data.
     *
     * @JMS\Expose
     * @JMS\Accessor(getter="getParameters",setter="setParameters")
     * @JMS\Type("array")
     */
    protected $parameters;

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
     * Set name
     *
     * @param  string $name
     * @return Layout
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
     * Set description
     *
     * @param  string $description
     * @return Layout
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set filename
     *
     * @param  string $filename
     * @return Layout
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set action
     *
     * @param  string    $action
     * @return Sublayout
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set content id
     *
     * @param integer $id
     */
    public function setContentId($id)
    {
        $this->content = $id;

        return $this;
    }

    /**
     * Get content id
     *
     * @return integer
     */
    public function getContentId()
    {
        if ($this->content instanceof ContentInterface) {
            return $this->content->getId();
        }

        return null;
    }

    /**
     * Get content
     *
     * @return integer
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Get layouts at
     *
     * @param  string $key
     *
     * @return LayoutInterface
     */
    public function getLayoutsAt($key)
    {
        $placeholders = $this->placeholders;

        if (array_key_exists($key, $placeholders)) {
            return $placeholders[$key];
        }

        return false;
    }

    /**
     *
     * @JMS\PreSerialize
     *
     * @return array
     */
    public function setPlaceholderStubs()
    {
        foreach ($this->placeholderkeys as $placeholder) {
            if (empty($placeholder)) {
                continue;
            }
            $this->placeholders[$placeholder] = array();
        }

        return $this;
    }

    /**
     * Set placeholders
     *
     * @param  array  $placeholders
     * @return Layout
     */
    public function setPlaceholders($placeholders)
    {
        $this->placeholders = $placeholders;

        return $this;
    }

    /**
     * Get placeholders
     *
     * @return array
     */
    public function getPlaceholders()
    {
        return $this->placeholders;
    }

    /**
     * Set placeholderkeys
     *
     * @param  array  $placeholderkeys
     * @return Layout
     */
    public function setPlaceholderkeys($placeholderkeys)
    {
        $this->placeholderkeys = $placeholderkeys;

        return $this;
    }

    /**
     * Get placeholderkeys
     *
     * @return array
     */
    public function getPlaceholderkeys()
    {
        return $this->placeholderkeys;
    }

    /**
     * Set parameterSet
     *
     * @param string $parameterSet
     *
     * @return Sublayout
     */
    public function setParameterSet($parameterSet)
    {
        $this->parameterSet = $parameterSet;

        return $this;
    }

    /**
     * Get parameterSet
     *
     * @return string
     */
    public function getParameterSet()
    {
        return $this->parameterSet;
    }

    /**
     * Set parameters
     *
     * @param string $parameters
     *
     * @return Layout
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * Get parameters
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}
