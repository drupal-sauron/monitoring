<?php

namespace Sauron\Core\Drupal\Project\Source;

use Sauron\Core\Drupal\Project\Entity\Module;
use Sauron\Core\Drupal\Project\Entity\Project;
use Sauron\Core\Drupal\Project\ProjectSource;

/**
 * Fetch a project from a drush makefile
 *
 * @author Alan Moreau <moreau.alan@gmail.com>
 */
class DrushMakefile implements ProjectSource
{
    /**
     * @var string makefile file path
     */
    protected $makefile = NULL;

    /**
     * Create a new instance of DrushMakefile
     *
     * @param string $makefilePath path to makefile
     */
    public function __construct($makefilePath)
    {
        $this->makefile = $makefilePath;
    }

    /**
     * Retrieve the project from drush makefile
     *
     * @return Project|\Sauron\Core\Drupal\Project\Sauron\Core\Drupal\Project\Entity\Project
     */
    public function getProject()
    {
        $project = new Project();

        $info = make_parse_info_file($this->makefile);

        if (isset($info['core'])) {
            $project->coreVersion = $info['core'];
        }

        if (isset($info['projects']) && array_key_exists('drupal', $info['projects'])) {
            if (isset($info['projects']['drupal']['version'])) {
                $project->drupalVersion = $info['projects']['drupal']['version'];
            }
            else {
                //TODO Get the last drupal version
            }

            foreach($info['projects'] as $extName => $projectInfo) {
                if ($extName != 'drupal') {
                    $module = new Module();
                    $module->machineName = $extName;
                    if (isset($projectInfo['version'])) {
                        $module->version = $project->coreVersion . '-' . $projectInfo['version'];
                    }
                    $project->modules[] = $module;
                }
            }
        }

        return $project;
    }
}