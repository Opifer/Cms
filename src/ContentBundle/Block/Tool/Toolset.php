<?php

namespace Opifer\ContentBundle\Block\Tool;

class Toolset
{
    /**
     * @var Tool[]
     */
    protected $tools = array();

    /**
     * @return array
     */
    public function getTools()
    {
        usort($this->tools, function ($a, $b) {
            return strcmp($a->getName(), $b->getName());
        });

        return $this->tools;
    }

    /**
     * @param array $tools
     *
     * @return Toolset
     */
    public function setTools($tools)
    {
        $this->tools = $tools;

        return $this;
    }

    /**
     * @param Tool $tool
     *
     * @return Toolset
     */
    public function addTool(Tool $tool)
    {
        array_push($this->tools, $tool);

        return $this;
    }

    /**
     * @param array $tools
     *
     * @return Toolset
     */
    public function addTools(array $tools)
    {
        foreach ($tools as $tool) {
            $this->addTool($tool);
        }

        return $this;
    }

    /**
     * @return string[]
     */
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
