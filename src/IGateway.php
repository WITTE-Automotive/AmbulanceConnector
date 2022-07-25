<?php declare(strict_types=1);

namespace WitteAutomotive\AmbulanceConnector;

interface IGateway {

	public function setToken(string $token): void;
	public function setUri(string $uri): void;

}
