<?php

namespace Lib\ResourceLoader;

use Exception\ConfigException;

/**
 * Class ConfigLoader
 *
 * @package Hatch\Core
 */
class ConfigLoader implements ResourceLoaderInterface
{
    /**
     * @param string $resourceName
     * @return string
     *
     * @throws ConfigException
     */
    public function getResource($resourceName)
    {
        if ($this->hasResource($resourceName)) {
            return $this->loadResource($resourceName);
        } else {
            throw new ConfigException("Env variable $resourceName doesn't exist");
        }
    }

    /**
     * @param $resourceName
     *
     * @return bool
     */
    public function hasResource($resourceName)
    {
        return false !== getenv($resourceName);
    }

    /**
     * @param $resourceName
     *
     * @return string
     */
    private function loadResource($resourceName)
    {
        $option = getenv($resourceName);

        if(is_string($option)) {
            $option = trim($option, '"');
            $option = trim($option, "'");
        }

        //check for json
        $parsed_option = json_decode($option, true);

        return $parsed_option ?: $option;
    }
}
