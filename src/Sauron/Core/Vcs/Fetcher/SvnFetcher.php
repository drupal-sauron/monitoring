<?php

namespace Sauron\Core\Vcs\Fetcher;

use Sauron\Core\Vcs\VcsFetchable;

/**
 * Subversion fetcher
 *
 * @author Alan Moreau <moreau.alan@gmail.com>
 */
class SvnFetcher implements VcsFetchable
{
    /**
     * Create a new instance of svn fetcher
     *
     * @param $url SVN repository URL
     * @param $extraArgs extra args to pass to checkout command
     */
    public function __construct($url, $extraArgs)
    {
        $this->url = $url;
        $this->extraArgs = $extraArgs;
    }

    /**
     * Checkout source code to given destination folder
     *
     * @param string $destDir
     */
    public function fetch($destDir)
    {
        shell_exec('mkdir -p ' . $destDir);
        shell_exec('svn co --non-interactive --ignore-externals ' . $this->extraArgs . ' ' . $this->url . ' ' . $destDir);
    }
}