<?php declare(strict_types=1);

namespace WitteAutomotive\AmbulanceConnector;

use WitteAutomotive\AmbulanceConnector\Exception\InvalidArgumentException;

class Response implements IResponse
{
	private int $status;
	private string $message;
	private array $data = [];

	public function __construct(int $status = 200, string $message = "", array $data = [])
	{
		$this->status = $status;
		$this->message = $message;
		$this->data = $data;
	}

	public function getStatus(): int
	{
		return $this->status;
	}

	public function getMessage(): string
	{
		return $this->message;
	}

	/**
	 * @param null $key
	 * @return array|mixed
	 * @throws InvalidArgumentException
	 */
	public function getData(mixed $key = null): mixed
	{
		if($key) {
			if(array_key_exists($key, $this->data)) {
				return $this->data[$key];
			} else {
				throw new InvalidArgumentException("This key is not exists in data of response.");
			}
		} else {
			return $this->data;
		}
	}

}
