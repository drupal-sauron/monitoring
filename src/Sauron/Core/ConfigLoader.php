<?php

namespace Sauron\Core;

/**
 * Defines the behavior of a configuration handler
 *
 * @author Alan Moreau <moreau.alan@gmail.com>
 */
interface ConfigLoader
{
    /**
     * Retrieves param from configuration backend
     *
     * @param string $key parameter key
     * @return string parameter value
     */
    public function getParam($key);
} 