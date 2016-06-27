<?php

namespace Opifer\CmsBundle\Grid;

use APY\DataGridBundle\Grid\Column\Column;
use APY\DataGridBundle\Grid\Filter;
use APY\DataGridBundle\Grid\Rows;
use APY\DataGridBundle\Grid\Row;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Tools\Pagination\Paginator;
use APY\DataGridBundle\Grid\Source\Entity as BaseEntity;

class Entity extends BaseEntity
{
    /**
     * @param \APY\DataGridBundle\Grid\Column\Column[] $columns
     * @param int $page Page Number
     * @param int $limit Rows Per Page
     * @param int $gridDataJunction  Grid data junction
     * @return \APY\DataGridBundle\Grid\Rows
     */
    public function execute($columns, $page = 0, $limit = 0, $maxResults = null, $gridDataJunction = Column::DATA_CONJUNCTION)
    {
        $this->query = $this->getQueryBuilder();
        $this->querySelectfromSource = clone $this->query;

        $bindIndex = 123;
        $serializeColumns = array();
        $where = $gridDataJunction === Column::DATA_CONJUNCTION ? $this->query->expr()->andx() : $this->query->expr()->orx();

        $columnsById = array();
        foreach ($columns as $column) {
            $columnsById[$column->getId()] = $column;
        }

        foreach ($columns as $column) {
            // If a column is a manual field, ie a.col*b.col as myfield, it is added to select from user.
            if($column->getIsManualField() === false) {
                $fieldName = $this->getFieldName($column, true);
                $this->query->addSelect($fieldName);
                $this->querySelectfromSource->addSelect($fieldName);
            }

            if ($column->isSorted()) {
                if ($column->getType() === 'join') {
                    $this->query->resetDQLPart('orderBy');
                    foreach($column->getJoinColumns() as $columnName) {
                        $this->query->addOrderBy($this->getFieldName($columnsById[$columnName]), $column->getOrder());
                    }
                } else {
                    $this->query->orderBy($this->getFieldName($column), $column->getOrder());
                }
            }

            if ($column->isFiltered()) {
                // Some attributes of the column can be changed in this function
                $filters = $column->getFilters('entity');

                $isDisjunction = $column->getDataJunction() === Column::DATA_DISJUNCTION;

                $hasHavingClause = $column->hasDQLFunction() || $column->getIsAggregate();

                $sub = $isDisjunction ? $this->query->expr()->orx() : ($hasHavingClause ? $this->query->expr()->andx() : $where);

                /** @var Filter $filter */
                foreach ($filters as $filter) {
                    $operator = $this->normalizeOperator($filter->getOperator());

                    $columnForFilter = ($column->getType() !== 'join') ? $column : $columnsById[$filter->getColumnName()];

                    $fieldName = $this->getFieldName($columnForFilter, false);
                    $bindIndexPlaceholder = "?$bindIndex";
                    if (in_array($filter->getOperator(), array(Column::OPERATOR_LIKE,Column::OPERATOR_RLIKE,Column::OPERATOR_LLIKE,Column::OPERATOR_NLIKE,))) {
                        $fieldName = "LOWER($fieldName)";
                        $bindIndexPlaceholder = "LOWER($bindIndexPlaceholder)";
                    }

                    $q = $this->query->expr()->$operator($fieldName, $bindIndexPlaceholder);

                    if ($filter->getOperator() == Column::OPERATOR_NLIKE || $filter->getOperator() == Column::OPERATOR_NSLIKE) {
                        $q = $this->query->expr()->not($q);
                    }

                    $sub->add($q);

                    if ($filter->getValue() !== null) {
                        $this->query->setParameter($bindIndex++, $this->normalizeValue($filter->getOperator(), $filter->getValue()));
                    }
                }

                if (method_exists($column, 'addFilterCondition')) {
                    $column->addFilterCondition($sub, $this->query);
                }

                if ($hasHavingClause) {
                    $this->query->andHaving($sub);
                } elseif ($isDisjunction) {
                    $where->add($sub);
                }
            }

            if ($column->getType() === 'array') {
                $serializeColumns[] = $column->getId();
            }
        }

        if ($where->count()> 0) {
            //Using ->andWhere here to make sure we preserve any other where clauses present in the query builder
            //the other where clauses may have come from an external builder
            $this->query->andWhere($where);
        }

        foreach ($this->joins as $alias => $field) {
            if (null !== $field['type'] && strtolower($field['type']) === 'inner') {
                $join = 'join';
            } else {
                $join = 'leftJoin';
            }

            $this->query->$join($field['field'], $alias);
            $this->querySelectfromSource->$join($field['field'], $alias);
        }

        if ($page > 0) {
            $this->query->setFirstResult($page * $limit);
        }

        if ($limit > 0) {
            if ($maxResults !== null && ($maxResults - $page * $limit < $limit)) {
                $limit = $maxResults - $page * $limit;
            }

            $this->query->setMaxResults($limit);
        } elseif ($maxResults !== null) {
            $this->query->setMaxResults($maxResults);
        }

        if (!empty($this->groupBy)) {
            $this->query->resetDQLPart('groupBy');
            $this->querySelectfromSource->resetDQLPart('groupBy');

            foreach ($this->groupBy as $field) {
                $this->query->addGroupBy($this->getGroupByFieldName($field));
                $this->querySelectfromSource->addGroupBy($this->getGroupByFieldName($field));
            }
        }

        //call overridden prepareQuery or associated closure
        $this->prepareQuery($this->query);
        $hasJoin = $this->checkIfQueryHasFetchJoin($this->query);

        $query = $this->query->getQuery();
        foreach ($this->hints as $hintKey => $hintValue) {
            $query->setHint($hintKey, $hintValue);
        }

        $items = new Paginator($query, $hasJoin);

        $repository = $this->manager->getRepository($this->entityName);

        // Force the primary field to get the entity in the manipulatorRow
        $primaryColumnId = null;
        foreach ($columns as $column) {
            if ($column->isPrimary()) {
                $primaryColumnId = $column->getId();

                break;
            }
        }

        // hydrate result
        $result = new Rows();

        foreach ($items as $item) {
            $row = new Row();

            foreach ($item as $key => $value) {
                $key = str_replace('::', '.', $key);

                if (in_array($key, $serializeColumns) && is_string($value)) {
                    $value = unserialize($value);
                }

                $row->setField($key, $value);
            }

            $row->setPrimaryField($primaryColumnId);

            //Setting the representative repository for entity retrieving
            $row->setRepository($repository);

            //call overridden prepareRow or associated closure
            if (($modifiedRow = $this->prepareRow($row)) != null) {
                $result->addRow($modifiedRow);
            }
        }

        return $result;
    }

    /**
     * @param QueryBuilder $qb
     * @return boolean
     */
    protected function checkIfQueryHasFetchJoin(QueryBuilder $qb)
    {
        $join = $qb->getDqlPart('join');

        if (empty($join)) {
            return false;
        }

        foreach ($join[$this->getTableAlias()] as $join) {
            if ($join->getJoinType() === Join::INNER_JOIN || $join->getJoinType() === Join::LEFT_JOIN) {
                return true;
            }
        }

        return false;
    }
}
