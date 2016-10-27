<?php

namespace Opifer\ExpressionEngine\Constraint;

use Webmozart\Expression\Expression;
use Webmozart\Expression\Logic\Literal;

/**
 * @deprecated Use Webmozart\Expression\Constraint\Equals instead
 */
class Date extends Literal
{
    /**
     * @var int|string
     */
    protected $date;

    /**
     * Constructor.
     *
     * @param string|int $date
     */
    public function __construct($date)
    {
        $this->date = $date;
    }

    /**
     * {@inheritdoc}
     */
    public function evaluate($value)
    {
        if (!$value instanceof \DateTime) {
            throw new \Exception('The value passed to the Date constraint should be an instance of \DateTime');
        }

        return (strtolower($value->format($this->getDateFormat())) == strtolower($this->date));
    }

    /**
     * Get the date format
     *
     * @return string
     */
    protected function getDateFormat()
    {
        return 'Y-m-d';
    }

    /**
     * {@inheritdoc}
     */
    public function equivalentTo(Expression $other)
    {
        // Since this class is final, we can check with instanceof
        return $other instanceof $this;
    }
    /**
     * {@inheritdoc}
     */
    public function toString()
    {
        return 'date()';
    }
}
