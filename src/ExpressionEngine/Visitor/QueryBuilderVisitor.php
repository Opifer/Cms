<?php

namespace Opifer\ExpressionEngine\Visitor;

use Doctrine\ORM\QueryBuilder;
use Webmozart\Expression\Constraint\In;
use Webmozart\Expression\Constraint\NotEquals;
use Webmozart\Expression\Expression;
use Webmozart\Expression\Selector\Key;
use Webmozart\Expression\Traversal\ExpressionVisitor;

class QueryBuilderVisitor implements ExpressionVisitor
{
    /** @var QueryBuilder */
    protected $qb;

    /**
     * Constructor.
     *
     * @param QueryBuilder $qb
     */
    public function __construct(QueryBuilder $qb)
    {
        $this->qb = $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function enterExpression(Expression $expr)
    {
        if ($expr instanceof Key) {
            $this->qb->andWhere($this->toExpr($expr));
        }

        return $expr;
    }

    /**
     * {@inheritdoc}
     */
    public function leaveExpression(Expression $expr)
    {
        //
    }

    /**
     * Transforms the \Webmozart\Expression\Expression to a Doctrine \Doctrine\ORM\Query\Expr.
     *
     * @param Key $expr
     *
     * @return \Doctrine\ORM\Query\Expr\Comparison|\Doctrine\ORM\Query\Expr\Func
     */
    protected function toExpr(Key $expr)
    {
        $left = $expr->getKey();

        if (strpos($left, '.') !== false) {
            $this->shouldJoin($left);
        } else {
            $left = $this->getRootAlias().'.'.$left;
        }

        $comparator = $expr->getExpression();
        $right = $comparator->getComparedValue();

        if ($comparator instanceof NotEquals) {
            return $this->qb->expr()->neq($left, $right);
        } elseif ($comparator instanceof In) {
            if (is_array($right)) {
                return $this->qb->expr()->in($left, $right);
            } else {
                return $this->qb->expr()->like($left, '%'.$right.'%');
            }
        }

        return $this->qb->expr()->eq($left, $right);
    }

    /**
     * Strips the key parts and creates a join if it does not exist yet.
     *
     * @param string $key
     */
    protected function shouldJoin($key)
    {
        $parts = explode('.', $key);

        if (!in_array($parts[0], $this->qb->getAllAliases())) {
            $this->qb->leftJoin($this->getRootAlias().'.'.$parts[0], $parts[0]);
        }
    }

    /**
     * Returns the root alias
     *
     * @return string
     */
    protected function getRootAlias()
    {
        $rootAliases = $this->qb->getRootAliases();

        return $rootAliases[0];
    }
}
