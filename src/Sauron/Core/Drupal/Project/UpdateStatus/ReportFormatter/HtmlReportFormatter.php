<?php

namespace Sauron\Core\Drupal\Project\UpdateStatus\ReportFormatter;

use Sauron\Core\Drupal\Project\Entity\Module;
use Sauron\Core\Drupal\Project\Entity\Project;

/**
 * Render an update status report in HTML
 *
 * @author Alan Moreau <moreau.alan@gmail.com>
 */
class HtmlReportFormatter
{
    /**
     * @var array column's names
     */
    private static $headers = array('Module', 'Installed version', 'Last security update version', 'Last bug fix version');

    /**
     * @var string styles use to colorize line according to the extension status
     */
    CONST UNSUPPORTED_STYLE = '<td style="border : 1px solid black" bgcolor="#EDEDED">%s</td>';
    CONST INFO_STYLE        = '<td style="border : 1px solid black" bgcolor="#DDFFDD">%s</td>';
    CONST BUG_STYLE         = '<td style="border : 1px solid black" bgcolor="#FFFFDD">%s</td>';
    CONST SECURITY_STYLE    = '<td style="border : 1px solid black" bgcolor="#FFCCCC">%s</td>';

    /**
     * Render the report
     *
     * @param OutputInterface $output
     * @param Project $project the project related to update status
     * @param $updateStatus the update status
     */
    public function render(Project $project, $updateStatus)
    {

        $coreTable  = '<h1>This is the update status report of your project ' . $project->name . '</h1>';
        $coreTable .= '<table style="border-collapse: collapse; border : 1px solid black">';
        $coreTable .= '<tr style="border : 1px solid black">' . $this->getHeader() . '</tr>';

        //Drupal core
        if (isset($updateStatus['drupal'])) {
            $coreTable .= $this->getRow($updateStatus['drupal']);
        }

        $coreTable .= '</table>';

        $modulesTable  = '<table style=" border-collapse: collapse; border : 1px solid black">';
        $modulesTable .= '<tr style="border : 1px solid black">' . $this->getHeader() . '</tr>';

        foreach($project->getModules() as $module) {
            if (isset($updateStatus['modules'][$module->machineName])) {
                $updateStatusEntry = $updateStatus['modules'][$module->machineName];
                $modulesTable .= $this->getRow($updateStatusEntry, $module);
            }
        }

        $modulesTable .= '</table>';

        return $coreTable . '<br>' . $modulesTable;
    }

    /**
     * Return HTML table header
     *
     * @return string
     */
    protected function getHeader()
    {
        $headers = self::$headers;
        array_walk($headers, function(&$value) {
            $value = '<th style="border : 1px solid black">' . $value . '</th>';
        });
        return implode('', $headers);
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

        return '<tr>' .
            sprintf($style, $moduleName) .
            sprintf($style, $installedVersion) .
            sprintf($style, $lastSecurityUpdateVersion) .
            sprintf($style, $lastBugFixVersion) .
        '<tr>';
    }
} 