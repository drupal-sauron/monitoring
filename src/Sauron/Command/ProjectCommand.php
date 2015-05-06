<?php

namespace Sauron\Command;

use Sauron\Core\Config\YamlLoader;
use Sauron\Core\ConfigAware;
use Sauron\Core\ConfigLoader;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Define a base type for command related to a project
 * - A project command has a project name argument
 * - A project command has access to project config and sauron config
 *
 * @author Alan Moreau <moreau.alan@gmail.com>
 */
abstract class ProjectCommand extends Command implements ConfigAware
{

    protected $config;

    protected $projectConfig;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
          ->addArgument('project', InputArgument::REQUIRED, 'Machine name of the project you want to target');
    }

    /**
     * {@inheritdoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $project = $input->getArgument('project');
        $projectsPath = $this->config->getParam('application.projects_path');
        $locator = new FileLocator($projectsPath);
        $this->projectConfig = new YamlLoader($locator);
        $this->projectConfig->load($project . '.yml');

    }

    public function setConfig(ConfigLoader $config)
    {
        $this->config = $config;
    }
}