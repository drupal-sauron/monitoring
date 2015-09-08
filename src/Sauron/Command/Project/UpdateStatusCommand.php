<?php

namespace Sauron\Command\Project;

use Sauron\Command\ProjectCommand;
use Sauron\Core\Drupal\Project\Source\DrushMakefile;
use Sauron\Core\Drupal\Project\Source\Filesystem;
use Sauron\Core\Drupal\Project\UpdateStatus\ReportFormatter\ConsoleReportFormatter;
use Sauron\Core\Drupal\Project\UpdateStatus\ReportFormatter\HtmlReportFormatter;
use Sauron\Core\Drupal\Project\UpdateStatus\UpdateStatus;
use Sauron\Core\Transport\Email;
use Sauron\Core\Vcs\VcsFactory;

use Swift_Message;
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
          )
          ->addOption(
              'report',
              null,
              InputOption::VALUE_OPTIONAL,
              'Report type: console|mail'
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
        if ($makefile != NULL) {
            $makeFilePath = $sandboxProjectPath . '/' . $makefile;
            $dm = new DrushMakefile($makeFilePath);
            $project = $dm->getProject();
        }
        else {
            $drupalRoot   = $sandboxProjectPath . '/' . $this->projectConfig->getParam('drupal.drupal_root');
            $contribPaths = $this->projectConfig->getParam('drupal.contrib_paths');

            $dm = new Filesystem($drupalRoot, $contribPaths);
            $project = $dm->getProject();
        }
        $project->name = $this->projectConfig->getParam('project');

        //Retrieve update Status
        $us = new UpdateStatus($project);
        $updateStatus = $us->getUpdateStatus();

        //Report update Status
        $reportType = $input->getOption('report');
        if ($reportType == 'mail') {
            $formatter = new HtmlReportFormatter();
            $html = $formatter->render($project, $updateStatus);

            $email = new Email();
            $email->setConfig($this->config);

            $to = $this->projectConfig->getParam('mail');
            $email->send($to, '[' . $project->name . '] Drupal update status report', $html);
        }
        else {
            $report = new ConsoleReportFormatter();
            $report->render($output, $project, $updateStatus);
        }
    }
}