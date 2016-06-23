<?php

namespace Opifer\CmsBundle\Grid;

use APY\DataGridBundle\Grid\Column\TextColumn;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\Comparison;
use Doctrine\ORM\Query\Expr\Composite;
use Doctrine\ORM\QueryBuilder;

class AttributeColumn extends TextColumn
{
    protected $attribute;

    public function __initialize(array $params)
    {
        parent::__initialize($params);

        $this->attribute = $this->getParam('attribute', '');
    }

    public function getFilters($source)
    {
        $filters = parent::getFilters($source);

        return $filters;
    }

    public function addFilterCondition(Composite $sub, QueryBuilder $query)
    {
        $unique = time();
        $q = new Comparison('a.name', Comparison::EQ, '?'.$unique);
        $query->setParameter($unique, $this->attribute);

        $sub->add($q);
    }

    public function getType()
    {
        return 'attribute';
    }
}