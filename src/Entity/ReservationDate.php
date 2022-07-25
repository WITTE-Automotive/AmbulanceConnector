<?php declare(strict_types=1);

namespace WitteAutomotive\AmbulanceConnector\Entity;

class ReservationDate
{
    public int $calendar;
    public string $name;
    public string $title;
    public \DateTimeImmutable $date;
    public array $reservationTimes = [];

    protected array $days = [
        1 => "Pondělí",
        2 => "Úterý",
        3 => "Středa",
        4 => "Čtvrtek",
        5 => "Pátek",
        6 => "Sobota",
        7 => "Neděle",
    ];

    public function __construct(int $calendar, \DateTimeImmutable $date)
    {
        $this->calendar = $calendar;
        $this->name = $this->days[$date->format("N")];
        $this->title = sprintf("%s, %s",$this->name, $date->format("d. m. Y"));
        $this->date = $date;
    }

    public function getCalendar(): int
    {
        return $this->calendar;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function getReservationTimes(): array
    {
        return $this->reservationTimes;
    }

    public function setReservationTimes(array $reservationTimes): void
    {
        $this->reservationTimes = $reservationTimes;
    }

}
