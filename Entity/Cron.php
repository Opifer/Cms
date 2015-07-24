<?php

namespace Opifer\CmsBundle\Entity;

use Cron\CronExpression;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Opifer\CrudBundle\Annotation as Opifer;

/**
 * Cron job
 *
 * @ORM\Entity(repositoryClass="Opifer\CmsBundle\Repository\CronRepository")
 * @ORM\Table(name="cron")
 */
class Cron
{
    /** State if job is inserted, and might be started. */
    const STATE_PENDING = 'pending';

    /** State if job was never started, and will never be started. */
    const STATE_CANCELED = 'canceled';

    /** State if job was started and has not exited, yet. */
    const STATE_RUNNING = 'running';

    /** State if job exists with a successful exit code. */
    const STATE_FINISHED = 'finished';

    /** State if job exits with a non-successful exit code. */
    const STATE_FAILED = 'failed';

    /** State if job exceeds its configured maximum runtime. */
    const STATE_TERMINATED = 'terminated';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="command", type="string", length=255)
     * @Opifer\Form(editable=true)
     */
    protected $command;

    /**
     * @var string
     *
     * @ORM\Column(name="expression", type="string", length=255)
     * @Opifer\Form(editable=true)
     */
    protected $expression;

    /**
     * @var integer
     *
     * @ORM\Column(name="priority", type="integer")
     * @Opifer\Form(editable=true)
     */
    protected $priority;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", length=20)
     */
    protected $state = self::STATE_PENDING;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="started_at", type="datetime", nullable=true)
     */
    protected $startedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ended_at", type="datetime", nullable=true)
     */
    protected $endedAt;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set command
     *
     * @param  string $command
     * @return Cron
     */
    public function setCommand($command)
    {
        $this->command = $command;

        return $this;
    }

    /**
     * Get command
     *
     * @return string
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * Set expression
     *
     * @param string $expression
     *
     * @return Cron
     */
    public function setExpression($expression)
    {
        $this->expression = $expression;

        return $this;
    }

    /**
     * Get expression
     *
     * @return string
     */
    public function getExpression()
    {
        return $this->expression;
    }

    /**
     * Set priority
     *
     * @param integer $priority
     *
     * @return Cron
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return integer
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set state
     *
     * @param boolean $state
     */
    public function setState($newState)
    {
        if ($newState === $this->state) {
            return;
        }

        switch ($newState) {
            case self::STATE_RUNNING:
                $this->startedAt = new \DateTime();
                break;

            case self::STATE_FINISHED:
            case self::STATE_FAILED:
            case self::STATE_TERMINATED:
                $this->endedAt = new \DateTime();
                break;

            default:
                break;
        }

        $this->state = $newState;

        return $this;
    }

    /**
     * Get state
     *
     * @return boolean
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Is cron running
     *
     * @return boolean
     */
    public function isRunning()
    {
        return self::STATE_RUNNING === $this->state;
    }

    /**
     * Set startedAt
     *
     * @param \DateTime $startedAt
     *
     * @return Cron
     */
    public function setStartedAt($startedAt)
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    /**
     * Get startedAt
     *
     * @return \DateTime
     */
    public function getStartedAt()
    {
        return $this->startedAt;
    }

    /**
     * Set endedAt
     *
     * @param \DateTime $endedAt
     *
     * @return Cron
     */
    public function setEndedAt($endedAt)
    {
        $this->endedAt = $endedAt;

        return $this;
    }

    /**
     * Get endedAt
     *
     * @return \DateTime
     */
    public function getEndedAt()
    {
        return $this->endedAt;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Cron
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Determine if the cron is due to run based on the current date or a
     * specific date.  This method assumes that the current number of
     * seconds are irrelevant, and should be called once per minute.
     *
     * @param string|\DateTime $currentTime Relative calculation date
     *
     * @return bool Returns TRUE if the cron is due to run or FALSE if not
     */
    public function isDue($currentTime = 'now')
    {
        return $this->getCronExpression()->isDue($currentTime);
    }

    /**
     * Get a next run date relative to the current date or a specific date
     *
     * @param string|\DateTime $currentTime      Relative calculation date
     * @param int              $nth              Number of matches to skip before returning a
     *                                           matching next run date. 0, the default, will return the current
     *                                           date and time if the next run date falls on the current date and
     *                                           time. Setting this value to 1 will skip the first match and go to
     *                                           the second match. Setting this value to 2 will skip the first 2
     *                                           matches and so on.
     * @param bool             $allowCurrentDate Set to TRUE to return the current date if
     *                                           it matches the cron expression.
     *
     * @return \DateTime
     *
     * @throws \RuntimeException on too many iterations
     */
    public function getNextRunDate($currentTime = 'now', $nth = 0, $allowCurrentDate = false)
    {
        return $this->getCronExpression()->getNextRunDate($currentTime, $nth, $allowCurrentDate);
    }

    /**
     * Get a previous run date relative to the current date or a specific date
     *
     * @param string|\DateTime $currentTime      Relative calculation date
     * @param int              $nth              Number of matches to skip before returning
     * @param bool             $allowCurrentDate Set to TRUE to return the
     *                                           current date if it matches the cron expression
     *
     * @return \DateTime
     *
     * @throws \RuntimeException on too many iterations
     *
     * @see Cron\CronExpression::getNextRunDate
     */
    public function getPreviousRunDate($currentTime = 'now', $nth = 0, $allowCurrentDate = false)
    {
        return $this->getCronExpression()->getPreviousRunDate($currentTime, $nth, $allowCurrentDate);
    }

    /**
     * Get multiple run dates starting at the current date or a specific date
     *
     * @param int              $total            Set the total number of dates to calculate
     * @param string|\DateTime $currentTime      Relative calculation date
     * @param bool             $invert           Set to TRUE to retrieve previous dates
     * @param bool             $allowCurrentDate Set to TRUE to return the
     *                                           current date if it matches the cron expression
     *
     * @return array Returns an array of run dates
     */
    public function getMultipleRunDates($total, $currentTime = 'now', $invert = false, $allowCurrentDate = false)
    {
        return $this->getCronExpression()->getMultipleRunDates($total, $currentTime, $invert, $allowCurrentDate);
    }

    /**
     * Get the CronExpression
     *
     * Handles the heavy lifting by calculating run dates and checking if an
     * expression is due.
     *
     * @see https://github.com/mtdowling/cron-expression
     *
     * @return \Cron\CronExpression
     */
    private function getCronExpression()
    {
        if ($this->expression) {
            return CronExpression::factory($this->expression);
        }

        return false;
    }

    /**
     * Create a string to identify this cron
     *
     * @return string
     */
    public function __toString()
    {
        return sprintf('Cron(id = %s, command = "%s")', $this->id, $this->command);
    }
}
