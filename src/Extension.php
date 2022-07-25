<?php

namespace WitteAutomotive\AmbulanceConnector;

use Nette;

class Extension extends Nette\DI\CompilerExtension
{
    /** @var array|object */
    protected $config = [
        'uri' => '',
        'token' => null,
    ];

    public function getConfigSchema(): Nette\Schema\Schema
    {
        return Nette\Schema\Expect::structure([
            'uri' => Nette\Schema\Expect::string(),
            'token' => Nette\Schema\Expect::string()->required()
        ]);
    }

    public function loadConfiguration()
    {
        $config = (array)$this->getConfig();
        $builder = $this->getContainerBuilder();

        $builder->addDefinition($this->prefix('penta_hospitals_gateway'))
            ->setFactory(Ambulance::class, [
                $config
            ]);
    }

}
