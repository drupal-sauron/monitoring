<?php

namespace Sauron;

use Sauron\Command\Project\UpdateStatusCommand;
use Sauron\Core\ConfigLoader;
use Symfony\Component\Console\Application;

/**
 * Sauron console application
 *
 * @author Alan Moreau <moreau.alan@gmail.com>
 */
class SauronApplication extends Application
{
    /**
     * Create a new instance of Sauron console application
     *
     * @param ConfigLoader $sauronConfig
     * @param string $name
     * @param string $version
     */
    public function __construct(ConfigLoader $sauronConfig, $name = 'UNKNOWN', $version = 'UNKNOWN')
    {
        parent::__construct($name, $version);

        //Add commands
        $updateStatusCommand = new UpdateStatusCommand();
        $updateStatusCommand->setConfig($sauronConfig);
        $this->add($updateStatusCommand);
    }
}