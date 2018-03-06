<?php

namespace Opifer\ContentBundle\Block\Tool;

class Tool
{
    const GROUP_CONTENT = 'Content';
    const GROUP_LAYOUT = 'Layouts';

    /** @var string */
    protected $group = self::GROUP_CONTENT;

    /** @var string */
    protected $name;

    /** @var string */
    protected $service;

    /** @var string */
    protected $description;

    /** @var string */
    protected $icon;

    /** @var array */
    protected $data = array();

    /** @var int */
    protected $sort = 0;

    public function __construct(string $name, string $service)
    {
        $this->name = $name;
        $this->service = $service;
    }

    public function getDataJson() : string
    {
        return json_encode($this->getData());
    }

    public function getGroup() : string
    {
        return $this->group;
    }

    public function setGroup(string $group) : Tool
    {
        $this->group = $group;

        return $this;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function setName(string $name) : Tool
    {
        $this->name = $name;

        return $this;
    }

    public function getService() : string
    {
        return $this->service;
    }

    public function setService(string $service) : Tool
    {
        $this->service = $service;

        return $this;
    }

    public function getDescription() : string
    {
        return $this->description;
    }

    public function setDescription(string $description) : Tool
    {
        $this->description = $description;

        return $this;
    }

    public function getIcon() : string
    {
        return $this->icon;
    }

    public function setIcon(string $icon) : Tool
    {
        $this->icon = $icon;

        return $this;
    }

    public function getData() : array
    {
        return $this->data;
    }

    public function setData(array $data) : Tool
    {
        $this->data = $data;

        return $this;
    }

    public function getSort() : int
    {
        return $this->sort;
    }

    public function setSort(int $sort) : Tool
    {
        $this->sort = $sort;

        return $this;
    }
}
