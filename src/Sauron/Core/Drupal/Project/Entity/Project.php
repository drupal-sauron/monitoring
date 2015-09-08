<?php

namespace Sauron\Core\Drupal\Project\Entity;

/**
 * Represents a Drupal project
 *
 * @author Alan Moreau <moreau.alan@gmail.com>
 */
class Project
{
    /**
     * @var string project name
     */
    public $name   = '';

    /**
     * @var string drupal core version - short format 6.x, 7.x, etc.
     */
    public $coreVersion   = '';

    /**
     * @var string drupal core version - long format 6.x, 7.x, etc.
     */
    public $drupalVersion = '';

    /**
     * @var array a list of theme extensions
     * @see Sauron\Core\Drupal\Project\Entity\Module::THEME
     */
    public $themes        = array();

    /**
     * @var array a list of module extensions
     * @see Sauron\Core\Drupal\Project\Entity\Module::MODULE
     */
    public $modules       = array();

    /**
     * @var array a list of libraries
     */
    public $libraries     = array();

    /**
     * Retrieves project name
     * @return string project name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Retrieves core version (short format)
     * @return string core version
     */
    public function getCoreVersion()
    {
        return $this->coreVersion;
    }

    /**
     * Retrieves core version (long format)
     * @return string core version
     */
    public function getDrupalVersion()
    {
        return $this->drupalVersion;
    }

    /**
     * Retrieves theme extensions
     * @return array a list of extension
     * @see Sauron\Core\Drupal\Project\Entity\Module
     */
    public function getThemes()
    {
        return $this->themes;
    }

    /**
     * Retrieves module extensions
     * @return array a list of extension
     * @see Sauron\Core\Drupal\Project\Entity\Module
     */
    public function getModules()
    {
        return $this->modules;
    }

    /**
     * Retrieves libraries
     */
    public function getLibraries()
    {
        return $this->libraries;
    }
} 