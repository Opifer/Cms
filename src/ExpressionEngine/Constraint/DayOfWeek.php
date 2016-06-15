<?php

namespace Opifer\ExpressionEngine\Constraint;

use Webmozart\Expression\Expression;
use Webmozart\Expression\Logic\Literal;

class DayOfWeek extends Literal
{
    /**
     * @var int|string
     */
    protected $day;

    /**
     * IsDayOfWeek constructor.
     *
     * @param string|int $day
     */
    public function __construct($day)
    {
        $this->day = $day;
    }

    /**
     * {@inheritdoc}
     */
    public function evaluate($value)
    {
        if (!$value instanceof \DateTime) {
            throw new \Exception('The value passed to the DayOfWeek constraint should be an instance of \DateTime');
        }

        return (strtolower($value->format($this->getDateFormat())) == strtolower($this->day));
    }

    /**
     * Get the format that matches the type of day.
     *
     * - N: 1 (for Monday) through 7 (for Sunday)
     * - l: Sunday through Saturday
     *
     * @return string
     */
    protected function getDateFormat()
    {
        return (is_numeric($this->day)) ? 'N' : 'l';
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
        return 'dayOfWeek()';
    }
}
