<?php

namespace Sauron\Core\Drupal\Project\Source;

use Sauron\Core\Drupal\Project\ProjectSource;
use Sauron\Core\Drupal\Project\Sauron;

/**
 *
 * @author Alan Moreau <moreau.alan@gmail.com>
 */
class Filesystem implements ProjectSource
{
    /**
     * Create a new instance of Filesystem
     *
     */
    public function __construct()
    {
    }

    /**
     * Fetch drupal project from source code
     *
     * @return Sauron\Core\Drupal\Project\Entity\Project a Drupal project
     */
    public function getProject()
    {
        // TODO: Implement getProject() method.
    }
}