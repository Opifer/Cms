<?php

namespace Opifer\ExpressionEngine\Constraint;

use Opifer\ExpressionEngine\ConstraintInterface;
use Webmozart\Expression\Expression;
use Webmozart\Expression\Logic\Literal;

class DatePeriod extends Literal implements ConstraintInterface
{
    /**
     * @var string
     */
    protected $date;

    /**
     * Constructor
     *
     * @param string $date
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
        if (!$value instanceof \DatePeriod) {
            throw new \Exception('The value passed to the Date constraint should be an instance of \DateTime');
        }

        foreach ($value as $day) {
            if (strtolower($day->format($this->getDateFormat())) == strtolower($this->date)) {
                return true;
            }
        }

        return false;
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
        return 'datePeriod()';
    }

    public function getLeft($key)
    {
        return $key;
    }
}
