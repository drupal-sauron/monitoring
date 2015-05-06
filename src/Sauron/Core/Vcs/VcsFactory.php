<?php

namespace Sauron\Core\Vcs;

use Sauron\Core\Vcs\Fetcher\GitFetcher;
use Sauron\Core\Vcs\Fetcher\SvnFetcher;

use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * A factory to retrieve vcs fetcher from configuration
 *
 * @author Alan Moreau <moreau.alan@gmail.com>
 */
class VcsFactory
{
    /**
     * @var string VCS types
     */
    CONST GIT = 'git';
    CONST SVN = 'svn';

    /**
     * Create a new instance of VcsFetchable according to given type
     *
     * @param $type see supported VCS type
     * @param $url repository URL
     * @param string $extraArgs extra args to pass to VCS checkout command
     * @return VcsFetchable
     */
    public static function create($type, $url, $extraArgs = '')
    {
        $fetcher = NULL;
        if ($type == self::GIT) {
            $fetcher = new GitFetcher($url, $extraArgs);
        }
        else if ($type == self::SVN){
            $fetcher = new SvnFetcher($url, $extraArgs);
        }
        else {
            throw new Exception('Unsupported VCS : ' . $type);
        }

        return $fetcher;
    }

} 