<?php

namespace Sauron\Core\Drupal\Project\Source;

use Sauron\Core\Drupal\Project\Entity\Module;
use Sauron\Core\Drupal\Project\Entity\Project;
use Sauron\Core\Drupal\Project\ProjectSource;
use Symfony\Component\Finder\Finder;

/**
 * Fetch a project from source code
 *
 * @author Alan Moreau <moreau.alan@gmail.com>
 */
class Filesystem implements ProjectSource
{

    protected $drupalRootPath = '';

    protected $extensionPaths = array();

    /**
     * Create a new instance of Filesystem
     *
     */
    public function __construct($drupalRootPath, $extensionPaths = array())
    {
        $this->drupalRootPath = $drupalRootPath;
        $this->extensionPaths = $extensionPaths;
    }

    /**
     * Fetch drupal project from source code
     *
     * @return Sauron\Core\Drupal\Project\Entity\Project a Drupal project
     */
    public function getProject()
    {
        $project = new Project();

        if (file_exists($this->drupalRootPath . '/core')) {
            //looks like drupal >= 8 core
            $moduleSystemPath = $this->drupalRootPath . '/core/modules/system';
        }
        else {
            $moduleSystemPath = $this->drupalRootPath . '/modules/system';
        }

        //get core version
        $finder = new Finder();
        $systemInfoFile = $finder->in($moduleSystemPath)->files()->name('system.info*');

        $coreVersion = null;
        foreach ($systemInfoFile as $file) {
            $coreVersion = $this->getVersion($file->getRealpath());
        }

        //get module version
        if ($coreVersion !== null) {
            $project->drupalVersion = $coreVersion;
            $project->coreVersion   = substr($coreVersion, 0, 1) . '.x';

            foreach($this->extensionPaths as $contribPath) {
                $finder = new Finder();
                $moduleFiles = $finder->in($this->drupalRootPath . '/' . $contribPath)->files()->name('/\.info(\.yml)*$/')->depth('== 1');
                foreach ($moduleFiles as $file) {
                    $module = new Module();
                    $module->machineName = $file->getBasename('.info');
                    $module->version = $this->getVersion($file->getRealpath());
                    $project->modules[] = $module;
                }
            }
        }

        return $project;
    }

    /**
     * Retrieve Module version from .info file
     * @param $file
     * @return string version
     */
    protected function getVersion($file)
    {
        $version = null;
        $content = file_get_contents($file);
        preg_match('/version\s*[:=]+\s*[\'"]([a-z0-9+.-]+)/', $content, $matches, PREG_OFFSET_CAPTURE);
        if (isset($matches[1])) {
            $version = $matches[1][0];
        }
        return $version;
    }
}