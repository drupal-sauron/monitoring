<?php

namespace Sauron\Core\Drupal\Project\Entity;

/**
 * Represents a Drupal extension
 *
 * @author Alan Moreau <moreau.alan@gmail.com>
 */
class Module
{
    /**
     * Defines extension type
     */
    CONST MODULE = 'module';
    CONST THEME  = 'theme';

    /**
     * @var string extension machine name
     */
    public $machineName = '';

    /**
     * @var string extension name
     */
    public $name        = '';

    /**
     * @var string extension version
     */
    public $version     = '';

    /**
     * @var string extension type
     */
    public $type        = '';
}