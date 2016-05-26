<?php

namespace Opifer\ExpressionEngine;

use JMS\Serializer\SerializerBuilder;
use Opifer\ExpressionEngine\Expression\Expression;
use Webmozart\Expression\Expr;
use Webmozart\Expression\Logic\AndX;
use Webmozart\Expression\Selector\Method;

class ExpressionEngine
{
    /** @var string */
    protected $configPath;

    /**
     * Constructor.
     *
     * @param string $configPath
     */
    public function __construct($configPath = null)
    {
        if ($configPath) {
            $this->configPath = $configPath;
        } else {
            $this->configPath = __DIR__.'/Resources/config';
        }
    }

    public function serialize($collection)
    {
        return $this->getSerializer()->serialize($collection, 'json');
    }

    public function deserialize($json)
    {
        return $this->getSerializer()->deserialize($json, "array<Opifer\ExpressionEngine\Expression\Expression>", 'json');
    }

    /**
     * @param Expression[] $expressions
     * @param object       $object
     *
     * @return bool
     */
    public function evaluate(array $expressions, $object)
    {
        $expr = $this->buildExpression($expressions);

        return $expr->evaluate($object);
    }

    /**
     * @param Expression[] $collection
     * @param string       $type
     *
     * @return \Webmozart\Expression\Expression
     */
    protected function buildExpression($collection, $type = AndX::class)
    {
        $expressions = [];

        /** @var Expression $expression */
        foreach ($collection as $expression) {
            if (!$expression instanceof Expression) {
                throw new \Exception(sprintf('Expressions must be of type %s', Expression::class));
            }

            if (count($expression->getChildren())) {
                $expressions[] = $this->buildExpression($expression->getChildren(), $expression->getConstraint());
            } else {
                $expressions[] = $this->transform($expression);
            }
        }

        return new $type($expressions);
    }

    /**
     * Transform the expression to Webmozarts' Expression
     *
     * @param Expression $expression
     * @return \Webmozart\Expression\Expression
     */
    protected function transform(Expression $expression)
    {
        $constraint = $expression->getConstraint();
        $getter = 'get'.ucfirst($expression->getSelector());

        return Expr::method($getter, new $constraint($expression->getValue()));
    }

    /**
     * @return \JMS\Serializer\Serializer
     */
    protected function getSerializer()
    {
        return SerializerBuilder::create()
            ->addMetadataDir($this->configPath)
            ->build();
    }
}
