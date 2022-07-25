<?php declare(strict_types=1);

namespace WitteAutomotive\AmbulanceConnector;

use WitteAutomotive\AmbulanceConnector\Entity\ExaminationType;
use WitteAutomotive\AmbulanceConnector\Entity\ReservationDate;
use WitteAutomotive\AmbulanceConnector\Entity\ReservationTime;
use WitteAutomotive\AmbulanceConnector\Entity\Workplace;
use WitteAutomotive\AmbulanceConnector\Exception\BookingFailedException;
use WitteAutomotive\AmbulanceConnector\Exception\CancelBookingFailedException;
use WitteAutomotive\AmbulanceConnector\Exception\GeneralException;
use WitteAutomotive\AmbulanceConnector\Exception\NoMedicalCheckUpDatesException;
use WitteAutomotive\AmbulanceConnector\Exception\WorkplaceNotFoundException;

/**
 * Class Ambulance
 * @method afterCreateBooking(Ambulance $ambulance, int $reservationId)
 * @method afterCancelBooking(Ambulance $ambulance)
 * @method afterClientCreate(Ambulance $ambulance, int $clientId)
 */
final class Ambulance extends Gateway implements IGateway
{

    protected array $days = [
        1 => "Pondělí",
        2 => "Úterý",
        3 => "Středa",
        4 => "Čtvrtek",
        5 => "Pátek",
        6 => "Sobota",
        7 => "Neděle",
    ];

    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    public function findWorkplaces(): array
    {
        $output = [];
        if ($res = $this->call("vratSeznamPracovist")) {
            $workplaces = $res->getData("seznam");

            foreach ($workplaces as $workplace) {
                // Ignore the gynecology
                if (str_contains($workplace->nazev, 'GYN')) {
                    continue;
                }

                $examinationTypes = array_map(function (array $t) {
                    return new ExaminationType($t["id"], $t["nazev"]);
                }, $workplace->typyVysetreni);

                $controlType = array_key_exists(0, $examinationTypes) ? $examinationTypes[0]->getId() : '001';
                $wp = new Workplace((int)$workplace->id, $workplace->nazev, $examinationTypes, $controlType);
                $output[] = $wp;
            }
        }
        return $output;
    }

    public function getWorkplace(int $id): Workplace
    {
        foreach ($this->findWorkplaces() as $workplace) {
            if ($workplace->getId() === $id) {
                return $workplace;
            }
        }
        throw new WorkplaceNotFoundException("Workplace " . $id . " not found");
    }

    public function findDatesByWorkplace(Workplace $workplace, \DateTimeImmutable $startDate, \DateTimeImmutable $endDate, int $limitPerDay = 30): Workplace
    {
        if ($res = $this->call("vratSeznamTerminuPracoviste", $workplace->getId(), $workplace->getControlType(), $startDate->getTimestamp(), $endDate->getTimestamp())) {
            $dates = $res->getData("seznam");
            $output = [];

            foreach ($dates as $date) {

                // Only available dates
                if ($date->stav === "V") {
                    $dateFrom = $this->createDateTimeFromTimestamp($date->datum_od);
                    $dateTo = $this->createDateTimeFromTimestamp($date->datum_do);
                    $key = $dateFrom->format("Y-m-d");

                    if (!array_key_exists($key, $output)) {
                        $reservationDate = new ReservationDate((int)$date->rozden_id, $dateFrom);
                        $output[$key] = $reservationDate;
                    } else {
                        $reservationDate = $output[$key];
                    }

                    if (count($reservationDate->reservationTimes) <= $limitPerDay) {
                        $reservationDate->reservationTimes[] = new ReservationTime($dateFrom, $dateTo);
                    }
                }
            }

            if (count($output) === 0) {
                throw new NoMedicalCheckUpDatesException("No medical check-up dates are planned in these date for workplace " . $workplace->getId() . " between " . $startDate->format("Y-m-d") . " to " . $endDate->format("Y-m-d"));
            }

            $workplace->setReservationDates(array_values($output));
        }

        return $workplace;
    }

    public function createBooking(Workplace $workplace, \DateTimeImmutable $startTime, \DateTimeImmutable $endTime, int $client, int $calendar, string $text = ''): int
    {
        if ($res = $this->call("rezervujTermin", $client, $workplace->getId(), $calendar, $startTime->getTimestamp(), $endTime->getTimestamp(), $text)) {
            if ($reservationId = (int)$res->getData("rezervace_id")) {
                dump($reservationId);
                return $reservationId;
            }
        }
        throw new BookingFailedException("The reservation was not successful.");
    }

    public function cancelBooking(int $reservationId): void
    {
        if ($this->call("zrusRezervaciTerminu", $reservationId)) {
            return;
        }
        throw new CancelBookingFailedException("The cancellation was not successful.");
    }

    public function createClient(string $firstName, string $lastName, string $email, string $phone = ""): int
    {
        if ($res = $this->call("zalozPacienta", $firstName, $lastName, $phone, $email, null, null, null)) {
            if ($clientId = (int)$res->getData("pacient_id")) {
                return $clientId;
            }
        }
        throw new GeneralException("Creating a client ID in the system failed.");
    }

    private function createDateTimeFromTimestamp(int $timestamp): \DateTimeImmutable
    {
        $dt = new \DateTimeImmutable();
        return $dt->setTimestamp($timestamp);
    }

}
