<?php

namespace Sauron\Core\Drupal\Project\UpdateStatus\ReportFormatter;

use Sauron\Core\Drupal\Project\Entity\Module;
use Sauron\Core\Drupal\Project\Entity\Project;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Render an update status report to the console interface
 *
 * @author Alan Moreau <moreau.alan@gmail.com>
 */
class ConsoleReportFormatter
{
    /**
     * @var string styles use to colorize line according to the extension status
     */
    CONST UNSUPPORTED_STYLE = '<fg=white;bg=cyan>%s</fg=white;bg=cyan>';
    CONST INFO_STYLE        = '<info>%s</info>';
    CONST BUG_STYLE         = '<comment>%s</comment>';
    CONST SECURITY_STYLE    = '<error>%s</error>';

    /**
     * Render the report
     *
     * @param OutputInterface $output
     * @param Project $project the project related to update status
     * @param $updateStatus the update status
     */
    public function render(OutputInterface $output, Project $project, $updateStatus)
    {
        $table = new Table($output);
        $table->setHeaders(
            array('Module', 'Installed version', 'Last security update version', 'Last bug fix version')
        );

        //Drupal core
        if (isset($updateStatus['drupal'])) {
            $coreRow = $this->getRow($updateStatus['drupal']);
            $table->addRow($coreRow);
        }

        $table->addRow(new TableSeparator());

        foreach($project->getModules() as $module) {
            if (isset($updateStatus['modules'][$module->machineName])) {
                $updateStatusEntry = $updateStatus['modules'][$module->machineName];
                $row = $this->getRow($updateStatusEntry, $module);
                $table->addRow($row);
            }
        }

        $table->render();
    }

    /**
     * Retrieves table row according to extension update status
     *
     * @param $updateStatusEntry
     * @param Module $module
     * @return array
     */
    protected function getRow($updateStatusEntry, Module $module = NULL)
    {
        $moduleName                = '';
        $installedVersion          = $updateStatusEntry['current_version'];
        $lastBugFixVersion         = $updateStatusEntry['last_bug_fix_version'];
        $lastSecurityUpdateVersion = $updateStatusEntry['last_security_fix_version'];

        if ($module === NULL) {
            $moduleName = 'Drupal';
        }
        else {
            $moduleName = $module->name;
        }

        $style = self::INFO_STYLE;
        if ($updateStatusEntry['current_rank'] == 0) {
            $style = self::UNSUPPORTED_STYLE;
        }
        else if ($updateStatusEntry['current_rank'] > 1
            && $updateStatusEntry['last_security_rank'] != 0
            && $updateStatusEntry['last_security_rank'] < $updateStatusEntry['current_rank']) {
            $style = self::SECURITY_STYLE;
        }
        else if ($updateStatusEntry['current_rank'] > 1
            && $updateStatusEntry['last_bug_rank'] != 0
            && $updateStatusEntry['last_bug_rank'] < $updateStatusEntry['current_rank']) {
            $style = self::BUG_STYLE;
        }

        return array(
            sprintf($style, $moduleName),
            sprintf($style, $installedVersion),
            sprintf($style, $lastSecurityUpdateVersion),
            sprintf($style, $lastBugFixVersion)
        );
    }
} 