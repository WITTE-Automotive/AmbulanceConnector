<?php declare(strict_types=1);

namespace WitteAutomotive\AmbulanceConnector\Entity;

class Workplace
{

    private int $id;
    private string $title;
    private string $controlType;
    private array $examinations;
    private array $reservationDates;

    public function __construct(int $id, string $title = "", array $examinations = [], string $controlType = '001')
    {
        $this->id = $id;
        $this->title = $title;
        $this->examinations = $examinations;
        $this->reservationDates = [];
        $this->controlType = $controlType;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getExaminations(): array
    {
        return $this->examinations;
    }

    public function getControlType(): string
    {
        return $this->controlType;
    }

    public function getReservationDates(): array
    {
        return $this->reservationDates;
    }

    public function setReservationDates(array $reservationDates)
    {
        $this->reservationDates = $reservationDates;
    }

}
