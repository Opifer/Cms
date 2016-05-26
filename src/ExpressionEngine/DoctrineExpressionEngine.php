<?php

namespace Opifer\ExpressionEngine;

use Doctrine\ORM\EntityManager;
use Opifer\ExpressionEngine\Expression\Expression;
use Opifer\ExpressionEngine\Visitor\QueryBuilderVisitor;
use Webmozart\Expression\Expr;
use Webmozart\Expression\Traversal\ExpressionTraverser;

class DoctrineExpressionEngine extends ExpressionEngine
{
    protected $em;

    public function __construct(EntityManager $em, $configPath = null)
    {
        parent::__construct($configPath);

        $this->em = $em;
    }

    /**
     * @param Expression[] $expressions
     * @param string       $class
     *
     * @return \Doctrine\ORM\QueryBuilder
     *
     * @throws \Exception
     */
    public function toQueryBuilder(array $expressions, $class)
    {
        $expr = $this->buildExpression($expressions);

        $qb = $this->em->getRepository($class)->createQueryBuilder('a');

        $traverser = new ExpressionTraverser();
        $traverser->addVisitor(new QueryBuilderVisitor($qb));
        $traverser->traverse($expr);

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    protected function transform(Expression $expression)
    {
        $constraint = $expression->getConstraint();

        return Expr::key($expression->getSelector(), new $constraint($expression->getValue()));
    }
}
