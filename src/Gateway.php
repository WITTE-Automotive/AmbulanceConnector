<?php declare(strict_types=1);

namespace WitteAutomotive\AmbulanceConnector;

use WitteAutomotive\AmbulanceConnector\Exception\ConfigurationException;
use WitteAutomotive\AmbulanceConnector\Exception\GeneralException;
use WitteAutomotive\AmbulanceConnector\Exception\ResponseException;

class Gateway implements IGateway
{
    private string $token;
    private string $uri;
    private ?\SoapClient $soapObject = null;

    public function __construct(array $config)
    {
        $this->token = $config["token"];
        $this->uri = $config["uri"];
    }

    protected function createSoapObject(): void
    {
        if ($this->uri) {
            $this->soapObject = new \SoapClient(null, [
                "location" => $this->uri,
                "uri" => $this->uri,
				'soap_version' => SOAP_1_2,            // Použití verze SOAP 1.2, můžete změnit na SOAP_1_1, pokud je třeba
				'exceptions' => true,                  // Vyvolání výjimek v případě chyb
				'trace' => 1,                          // Povolení ladění, můžete sledovat požadavky a odpovědi
				'cache_wsdl' => WSDL_CACHE_NONE,       // Zakázání cache WSDL
				'stream_context' => stream_context_create([
					'ssl' => [
						'verify_peer' => false,        // Vypnutí ověřování SSL certifikátu
						'verify_peer_name' => false,   // Vypnutí ověřování jména serveru
						'allow_self_signed' => true    // Povolení self-signed certifikátů
					]
				])
            ]);
        } else {
            throw new ConfigurationException("Parameter of URI is missing.");
        }
    }

    public function call($function): Response
    {
        // Dynamic parameters, delete called function
        $args = func_get_args();
        if ($args[0] === $function) {
            unset($args[0]);
        }

        // Add token to the first place of array of parameters for SOAP
        $args[-1] = $this->token;
        ksort($args);

        try {
            $this->createSoapObject();
            if ($res = $this->soapObject->__soapCall($function, $args)) {
                $arrayResponse = (array)$res;
                unset($arrayResponse["status"]);
                unset($arrayResponse["zprava"]);

                $response = new Response((int)$res->status, (string)$res->zprava, $arrayResponse);
                if ($response->getStatus() !== 0) {
                    throw new ResponseException($response->getMessage());
                } else {
                    return $response;
                }
            } else {
                throw new ResponseException("The response is missing. Is server available?");
            }
        } catch (\SoapFault $e) {
            throw new GeneralException("SOAP Fault: " . $e->getMessage());
        }
    }

    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    public function setUri(string $uri): void
    {
        $this->uri = $uri;
    }

}
