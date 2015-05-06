<?php

namespace Sauron\Core\Vcs;

/**
 * Fetch source code from a specific VCS : GIT, SVN, etc.
 *
 * @author Alan Moreau <moreau.alan@gmail.com>
 */
interface VcsFetchable
{
    /**
     * Fetch sources
     *
     * @param $destDir checkout destination folder
     */
    public function fetch($destDir);
}