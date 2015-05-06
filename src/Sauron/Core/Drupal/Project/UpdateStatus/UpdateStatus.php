<?php

namespace Sauron\Core\Drupal\Project\UpdateStatus;

use Sauron\Core\Drupal\Project\Entity\Module;
use Sauron\Core\Drupal\Project\Entity\Project;

use Sauron\UpdatesDrupalOrg\ReleaseHistory\ReleaseHistoryClient;

/**
 * Compute project update status.
 *
 * @author Alan Moreau <moreau.alan@gmail.com>
 */
class UpdateStatus
{
    /**
     * @ver string type of release
     */
    CONST BUG_FIX      = 'Bug fixes';
    CONST SECURITY_FIX = 'Security update';

    /**
     * @ver string type of extension
     */
    CONST PROJECT_TYPE_THEME  = 'Themes';
    CONST PROJECT_TYPE_MODULE = 'Modules';

    /**
     * @var Project the given drupal project
     */
    protected $project            = NULL;

    /**
     * @var ReleaseHistoryClient ws client to fetch update releases history
     */
    private $releaseHistoryClient = NULL;

    /**
     * Create a new instance of UpdateStatus
     *
     * @param Project $project target project
     */
    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    /**
     * Generates an update status for the drupal project
     *
     * @return array
     *   [
     *      'drupal' => [
     *          'last_security_fix_version' => '',
     *          'last_security_rank' => '',
     *          'last_bug_fix_version' => '',
     *          'last_bug_rank' => '',
     *          'last_recommended' => '',
     *          'current_rank' => '',
     *          'current_version' => ''
     *      ],
     *     'modules' => [
     *         '<module_machine_name>' => [
     *              'last_security_fix_version' => '',
     *              'last_security_rank' => '',
     *              'last_bug_fix_version' => '',
     *              'last_bug_rank' => '',
     *              'last_recommended' => '',
     *              'current_rank' => '',
     *              'current_version' => ''
     *          ],
     *     ]
     *   ]
     */
    public function getUpdateStatus()
    {
        $updateStatus  = array(
            'drupal' => NULL,
            'modules' => array()
        );

        $modules = $this->project->getModules();

        $this->releaseHistoryClient = new ReleaseHistoryClient();

        $updateStatus['drupal'] = $this->getCoreStatus();
        foreach($modules as $module) {
            $moduleStatusInfo = $this->getModuleStatus($module);
            $updateStatus['modules'][$module->machineName] = $moduleStatusInfo;
        }

        return $updateStatus;
    }

    /**
     * Return core update status
     *
     * @return array update status
     */
    protected function getCoreStatus()
    {
        $statusInfo = array();
        $releases = $this->releaseHistoryClient->getReleases('drupal', $this->project->getCoreVersion());

        $basicInfo = $this->extractBasicInfo($releases);

        if (isset($releases['releases']) && !empty($releases['releases']['release'])) {
            $statusInfo = $this->getStatusInfo($this->project->getDrupalVersion(), $basicInfo['recommended_major'],
                $releases['releases']['release']);
        }

        return $statusInfo;
    }

    /**
     * Return module update status
     *
     * @return array update status
     */
    protected function getModuleStatus(Module $module)
    {
        $statusInfo = array();
        $releases = $this->releaseHistoryClient->getReleases($module->machineName, $this->project->getCoreVersion());

        $basicInfo = $this->extractBasicInfo($releases);

        $module->name = $releases['title'];
        $module->type = $basicInfo['type'];

        $recommendedVersionMajor = $basicInfo['recommended_major'];
        if (isset($releases['releases']) && !empty($releases['releases']['release'])) {
            $statusInfo = $this->getStatusInfo($module->version, $recommendedVersionMajor, $releases['releases']['release']);
        }

        return $statusInfo;
    }

    /**
     * Extract basic information out of releases history :
     * - link
     * - Recommended major version
     * - Module type
     *
     * @return array info
     */
    protected function extractBasicInfo($releases)
    {
        $info = array();
        $info['link'] = $releases['link'];

        if (!isset($releases['recommended_major'])) {
            $info['recommended_major'] = $releases['default_major'];
        }
        else {
            $info['recommended_major'] = $releases['recommended_major'];
        }

        $extensionTypes = $this->extractTerm('Projects', $releases);
        if (in_array(self::PROJECT_TYPE_MODULE, $extensionTypes)) {
            $info['type'] = Module::MODULE;
        }
        else if (in_array(self::PROJECT_TYPE_THEME, $extensionTypes)) {
            $info['type'] = Module::THEME;
        }

        return $info;
    }

    /**
     * The most important method in this class, it retrieves update status through history releases
     *
     * @param $currentVersion current version of extension
     * @param $recommendedVersionMajor the recommended version major
     * @param $releases the releases history fetched from ws
     *
     * @return array
     *      [
     *          'last_security_fix_version' => '',
     *          'last_security_rank' => '',
     *          'last_bug_fix_version' => '',
     *          'last_bug_rank' => '',
     *          'last_recommended' => '',
     *          'current_rank' => '',
     *          'current_version' => ''
     *      ]
     */
    protected function getStatusInfo($currentVersion, $recommendedVersionMajor, $releases)
    {
        $updateInfoFound = FALSE;

        $lastBugFixVersion       = '';
        $lastSecurityFixVersion  = '';
        $lastRecommended         = '';

        $lastBugRank      = 0;
        $lastSecurityRank = 0;
        $currentRank      = 0;

        $rank = 1;
        do {
            if (isset($releases['name'])) {
                $release = $releases;
                $updateInfoFound = TRUE;
            }
            else {
                $release = array_shift($releases);
            }

            $versionMajor = $release['version_major'];
            $releaseVersion = $release['version'];

            //TODO handle exception
            if ($releaseVersion == $currentVersion) {
                $currentRank = $rank;
            }

            if ($versionMajor == $recommendedVersionMajor) {
                if ($lastRecommended == '') {
                    $lastRecommended = $releaseVersion;
                }

                $releaseTypes = $this->extractTerm('Release type', $release);
                if (in_array(self::BUG_FIX, $releaseTypes) && $lastBugFixVersion == '') {
                    $lastBugFixVersion = $releaseVersion;
                    $lastBugRank = $rank;
                }
                if (in_array(self::SECURITY_FIX, $releaseTypes) && $lastSecurityFixVersion == '') {
                    $lastSecurityFixVersion = $releaseVersion;
                    $lastSecurityRank = $rank;
                }
            }

            //no need to go further if we have enough info
            if ($currentRank > 0 && $lastBugFixVersion != '' && $lastSecurityFixVersion != '') {
                $updateInfoFound = TRUE;
            }
            else {
                $rank++;
            }
        }
        while(!empty($releases) && !$updateInfoFound);

        $statusInfo = array(
            'last_security_fix_version' => $lastSecurityFixVersion,
            'last_security_rank' => $lastSecurityRank,
            'last_bug_fix_version' => $lastBugFixVersion,
            'last_bug_rank' => $lastBugRank,
            'last_recommended' => $lastRecommended,
            'current_rank' => $currentRank,
            'current_version' => $currentVersion
        );

        return $statusInfo;
    }

    /**
     * Extract a term value from a list of terms (terms are parts of ws response)
     *
     * @param $termName the needed term name
     * @param $source part of ws response containing terms
     * @return array a list of term values
     */
    protected function extractTerm($termName, $source)
    {
        $foundTerms = array();

        if (isset($source['terms'])) {

            foreach ($source['terms'] as $terms) {
                $term = array();
                if (isset($terms['name'])) {
                    $term[] = $terms;
                }
                else {
                    $term = $terms;
                }

                foreach ($term as $t) {
                    if ($t['name'] == $termName) {
                        $foundTerms[] = $t['value'];
                    }
                }
            }

        }

        return $foundTerms;
    }
} 