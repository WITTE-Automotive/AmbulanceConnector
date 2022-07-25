<?php declare(strict_types=1);

namespace WitteAutomotive\AmbulanceConnector;

interface IResponse {

	public function getStatus(): int;
	public function getMessage(): string;
	public function getData($key): mixed;

}
