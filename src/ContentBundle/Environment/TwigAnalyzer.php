<?php

namespace Opifer\ContentBundle\Environment;

use Opifer\ContentBundle\Model\BlockInterface;
use Symfony\Bridge\Twig\TwigEngine;

class TwigAnalyzer extends TwigEngine
{

    public function findPlaceholders($name)
    {
        $functions = $this->findFunctionNodes($name);

        $placeholders = array();
        $key = -1;

        /** @var \Twig_Node_Expression_Function $function */
        foreach ($functions as $function) {
            if ($function->getAttribute('name') != 'render_placeholder') {
                continue;
            }

            if ($function->getNode('arguments')->count() && $function->getNode('arguments')->getNode('0')->hasAttribute('value')) {
                $key = (int) $function->getNode('arguments')->getNode('0')->getAttribute('value');
            } else {
                $key++;
            }

            $placeholders[] = array('key' => $key, 'lineno' => $function->getLine());
        }

        uasort($placeholders, function($a, $b) {
            return ($a['lineno'] > $b['lineno']);
        });

        return $placeholders;
    }

    public function findFunctionNodes($name)
    {
        $source = $this->environment->getLoader()->getSource($name);
        $tree = $this->environment->parse($this->environment->tokenize($source));

        $list = array();
        $findFunctions = function ($node, array &$list) use (&$findFunctions) {
            if ($node instanceof \Twig_Node_Expression_Function) {
                $list[] = $node;
            }
            if ($node) {
                foreach ($node as $child) {
                    $findFunctions($child, $list);
                }
            }
        };

        $findFunctions($tree, $list);

        return $list;
    }

}
