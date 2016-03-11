<?php

namespace Opifer\ContentBundle\Block\Tool;

/**
 * Class Toolset
 *
 * @package Opifer\ContentBundle\Block\Tool
 */
class Toolset
{
    /**
     * @var array
     */
    protected $tools = array();

    /**
     * @return array
     */
    public function getTools()
    {
        return $this->tools;
    }

    /**
     * @param array $tools
     * @return Toolbelt
     */
    public function setTools($tools)
    {
        $this->tools = $tools;
        return $this;
    }

    public function addTool(Tool $tool)
    {
        array_push($this->tools, $tool);

        return $this;
    }

    public function addTools(array $tools) {
        foreach ($tools as $tool) {
            $this->addTool($tool);
        }

        return $this;
    }

    public function getGroups()
    {
        $groups = [];

        foreach ($this->tools as $tool) {
            if (!in_array($tool->getGroup(), $groups)) {
                array_push($groups, $tool->getGroup());
            }
        }

        return $groups;
    }

}