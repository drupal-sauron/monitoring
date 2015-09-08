<?php

namespace Sauron\Core\Config;


use Sauron\Core\ConfigLoader;
use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Yaml\Yaml;

/**
 * Handle yaml config file
 *
 * @author Alan Moreau <moreau.alan@gmail.com>
 */
class YamlLoader extends FileLoader implements ConfigLoader
{
    /**
     * @var array hold configuration
     */
    protected $config = NULL;

    /**
     * {@inheritdoc}
     */
    public function load($resource, $type = null)
    {
        $yamlFiles = $this->locator->locate($resource, null, false);
        if (!empty($yamlFiles)) {

            $configValues = Yaml::parse(file_get_contents($yamlFiles[0]));
            $this->config = $configValues;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getParam($key)
    {
        $keys = explode('.', $key);
        $size = count($keys);

        $param = $this->config;
        $value = NULL;
        for($i=0; $i<$size; $i++) {
            $keyPart = $keys[$i];

            if (array_key_exists($keyPart, $param)) {
                $value = $param[$keyPart];
            }
            else {
                $value = NULL;
            }

            if (($i + 1) < $size) {
                $param = $value;
            }
        }

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return is_string($resource) && 'yml' === pathinfo(
            $resource,
            PATHINFO_EXTENSION
        );
    }
}