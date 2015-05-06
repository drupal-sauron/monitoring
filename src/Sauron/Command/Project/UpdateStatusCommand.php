<?php

namespace Sauron\Command\Project;

use Sauron\Command\ProjectCommand;
use Sauron\Core\Drupal\Project\Source\DrushMakefile;
use Sauron\Core\Drupal\Project\UpdateStatus\ReportFormatter\ConsoleReportFormatter;
use Sauron\Core\Drupal\Project\UpdateStatus\UpdateStatus;
use Sauron\Core\Vcs\VcsFactory;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * A project command to launch update status
 *
 * @author Alan Moreau <moreau.alan@gmail.com>
 */
class UpdateStatusCommand extends ProjectCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this
          ->setName('project:update-status')
          ->setDescription('Launch update status for given project')
          ->addOption(
              'checkout',
              null,
              InputOption::VALUE_NONE,
              'If specified, project is checkout from remote repository'
          );

    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $project = NULL;

        $projectName = $input->getArgument('project');
        $sandboxPath = $this->config->getParam('application.sandbox_path');
        $sandboxProjectPath = rtrim($sandboxPath, '/') . '/' . $projectName;

        // Checkout project if needed
        $isCheckout = $input->getOption('checkout');
        if ($isCheckout) {
            $vcs = $this->projectConfig->getParam('vcs');
            $fetcher = VcsFactory::create(
                $vcs['type'],
                $vcs['url'],
                $vcs['extra_args']
            );

            $fetcher->fetch($sandboxProjectPath);
        }

        // Retrieve project from given project source (Makefile, filesystem, etc.)
        $makefile = $this->projectConfig->getParam('drupal.drupal_makefile');
        if ($makefile) {
            $makeFilePath = $sandboxProjectPath . '/' . $makefile;
            $dm = new DrushMakefile($makeFilePath);
            $project = $dm->getProject();
        }
        else {
            //TODO handle other project source
        }

        $us = new UpdateStatus($project);
        $updateStatus = $us->getUpdateStatus();

        $report = new ConsoleReportFormatter();
        $report->render($output, $project, $updateStatus);
    }
}