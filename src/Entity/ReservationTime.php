<?php declare(strict_types=1);

namespace WitteAutomotive\AmbulanceConnector\Entity;

class ReservationTime
{
    public string $title;
    public \DateTimeImmutable $startTime;
    public \DateTimeImmutable $endTime;

    public function __construct(\DateTimeImmutable $startTime, \DateTimeImmutable $endTime)
    {
        $this->title = sprintf("%s - %s", $startTime->format("H:i"), $endTime->format("H:i"));
        $this->startTime = $startTime;
        $this->endTime = $endTime;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getStartTime(): \DateTimeImmutable
    {
        return $this->startTime;
    }

    public function getEndTime(): \DateTimeImmutable
    {
        return $this->endTime;
    }

}
