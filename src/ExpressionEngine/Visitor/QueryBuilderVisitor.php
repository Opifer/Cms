<?php

namespace Opifer\ExpressionEngine\Visitor;

use Doctrine\ORM\Query\Expr\Comparison;
use Doctrine\ORM\QueryBuilder;
use Webmozart\Expression\Constraint\In;
use Webmozart\Expression\Constraint\NotEquals;
use Webmozart\Expression\Expression;
use Webmozart\Expression\Logic\AndX;
use Webmozart\Expression\Logic\OrX;
use Webmozart\Expression\Selector\Key;
use Webmozart\Expression\Traversal\ExpressionVisitor;

class QueryBuilderVisitor implements ExpressionVisitor
{
    /** @var QueryBuilder */
    protected $qb;

    /**
     * Keeps the order of hashes. The last hash is always the `current` hash.
     *
     * @var string[]
     */
    protected $hashes = [];

    /**
     * Keeps track of the expressions for each `OrX` or `AndX` Doctrine expression.
     *
     * @var Comparison[]
     */
    protected $map = [];

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
     * Adds `Key` expressions to the query or stores children of OrX and AndX expressions in memory to be added
     * on the leaveExpression for OrX or AndX expressions.
     *
     * {@inheritdoc}
     */
    public function enterExpression(Expression $expr)
    {
        $hash = spl_object_hash($expr);

        if ($expr instanceof Key) {
            if (!count($this->hashes)) {
                $this->qb->andWhere($this->toExpr($expr));
            } else {
                $lastHash = end($this->hashes);

                $this->map[$lastHash][] = $this->toExpr($expr);
            }
        } elseif ($expr instanceof OrX) {
            $this->hashes[] = $hash;
            $this->map[$hash] = [];
        } elseif ($expr instanceof AndX) {
            $this->hashes[] = $hash;
            $this->map[$hash] = [];
        }

        return $expr;
    }

    /**
     * Adds the AndX and OrX Doctrine expressions to the query
     *
     * {@inheritdoc}
     */
    public function leaveExpression(Expression $expr)
    {
        if ($expr instanceof OrX || $expr instanceof AndX) {
            $hash = spl_object_hash($expr);

            if ($expr instanceof OrX) {
                $composite = $this->qb->expr()->orX();
                $composite->addMultiple($this->map[$hash]);
            } else {
                $composite = $this->qb->expr()->andX();
                $composite->addMultiple($this->map[$hash]);
            }

            unset($this->hashes[array_search($hash, $this->hashes)]);

            if (!count($this->hashes)) {
                $this->qb->andWhere($composite);
            } else {
                $lastHash = end($this->hashes);

                $this->map[$lastHash][] = $composite;
            }
        }

        return $expr;
    }

    /**
     * Transforms the \Webmozart\Expression\Expression to a Doctrine \Doctrine\ORM\Query\Expr.
     *
     * @param Key $expr
     *
     * @return Comparison|\Doctrine\ORM\Query\Expr\Func
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
