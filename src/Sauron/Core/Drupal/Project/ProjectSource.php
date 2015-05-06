<?php

namespace Sauron\Core\Drupal\Project;

/**
 * A project can be fetch from different sources : makefile, filesystem, etc.
 *
 * @author Alan Moreau <moreau.alan@gmail.com>
 */
interface ProjectSource
{
    /**
     * Fetch drupal project
     *
     * @return Sauron\Core\Drupal\Project\Entity\Project a Drupal project
     */
    public function getProject();
}