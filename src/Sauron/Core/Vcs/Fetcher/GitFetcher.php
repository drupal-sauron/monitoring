<?php

namespace Sauron\Core\Vcs\Fetcher;

use Sauron\Core\Vcs\VcsFetchable;

/**
 * Fetch sources from git repository
 *
 * @author Alan Moreau <moreau.alan@gmail.com>
 */
class GitFetcher implements VcsFetchable
{
    /**
     * Create a new instance of git fetcher
     *
     * @param $url git repository
     * @param $extraArgs extra args to pass to git clone command
     */
    public function __construct($url, $extraArgs)
    {
        $this->url       = $url;
        $this->extraArgs = $extraArgs;
    }

    /**
     * Clone it repository to the given folder
     *
     * @param string $destDir
     */
    public function fetch($destDir)
    {
        shell_exec('rm -rf ' . $destDir);
        shell_exec('git clone --depth=1 ' . $this->extraArgs . ' ' . $this->url . ' ' . $destDir);
    }
}