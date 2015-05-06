<?php

namespace Sauron\Core;

/**
 * Allow a class to receive configuration
 *
 * @author Alan Moreau <moreau.alan@gmail.com>
 */
interface ConfigAware
{
    /**
     * Inject config to the class
     *
     * @param ConfigLoader $config
     */
    public function setConfig(ConfigLoader $config);
} 