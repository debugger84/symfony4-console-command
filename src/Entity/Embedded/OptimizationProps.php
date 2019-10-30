<?php


namespace App\Entity\Embedded;

use App\Entity\Enum\EventType;
use App\Entity\Event;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable()
 */
class OptimizationProps
{
    /**
     * the minimum of occurrences of sourceEvent, if a publisher has less sourceEvents
     * that the threshold, then she should not be blacklisted
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     */
    private $threshold;

    /**
     * @var EventType
     *
     * @ORM\Column(name="source_event_type", length=50, nullable=false)
     */
    private $sourceEvent;

    /**
     * @var EventType
     *
     * @ORM\Column(name="measured_event_type", length=50, nullable=false)
     */
    private $measuredEvent;

    /**
     * the minimum ratio of sourceEvent occurrences to measuredEvent occurrences in percents
     * @var float
     * @ORM\Column(type="float", nullable=false)
     */
    private $ratioThreshold;

    /**
     * @return int
     */
    public function getThreshold(): int
    {
        return $this->threshold;
    }

    /**
     * @param int $threshold
     * @return OptimizationProps
     */
    public function setThreshold(int $threshold): OptimizationProps
    {
        $this->threshold = $threshold;
        return $this;
    }

    /**
     * @return EventType
     */
    public function getSourceEvent(): EventType
    {
        return new EventType($this->sourceEvent);
    }

    /**
     * @param EventType $sourceEvent
     * @return OptimizationProps
     */
    public function setSourceEvent(EventType $sourceEvent): OptimizationProps
    {
        $this->sourceEvent = $sourceEvent->getValue();
        return $this;
    }

    /**
     * @return EventType
     */
    public function getMeasuredEvent(): EventType
    {
        return new EventType($this->measuredEvent);
    }

    /**
     * @param EventType $measuredEvent
     * @return OptimizationProps
     */
    public function setMeasuredEvent(EventType $measuredEvent): OptimizationProps
    {
        $this->measuredEvent = $measuredEvent->getValue();
        return $this;
    }

    /**
     * @return float
     */
    public function getRatioThreshold(): float
    {
        return $this->ratioThreshold;
    }

    /**
     * @param float $ratioThreshold
     * @return OptimizationProps
     */
    public function setRatioThreshold(float $ratioThreshold): OptimizationProps
    {
        if ($ratioThreshold < 0 || $ratioThreshold > 100) {
            throw new \InvalidArgumentException('Ratio should be from 0 to 100');
        }
        $this->ratioThreshold = $ratioThreshold;
        return $this;
    }
}
