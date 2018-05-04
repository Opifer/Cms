<?php

namespace Opifer\ExpressionEngine\Constraint;

use Webmozart\Expression\Expression;
use Webmozart\Expression\Logic\Literal;
use Opifer\ExpressionEngine\SelectQueryStatement;

class DayOfWeek extends Literal implements SelectQueryStatement
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
     * Returns the compared value.
     *
     * @return mixed The compared value.
     */
    public function getComparedValue()
    {
        switch($this->day) {
            case 'monday'; return 2;
            case 'tuesday'; return 3;
            case 'wednesday'; return 4;
            case 'thursday'; return 5;
            case 'friday'; return 6;
            case 'saturday'; return 7;
            case 'sunday'; return 1;
            default: return 0;
        }
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

    public function selectArgument($key)
    {
        return sprintf("DAYOFWEEK(%s)", $key);
    }
}
