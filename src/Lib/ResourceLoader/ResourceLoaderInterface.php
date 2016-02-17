<?php

namespace Lib\ResourceLoader;

interface ResourceLoaderInterface
{
    /**
     * @param string $resourceName
     *
     * @return mixed
     */
    function getResource($resourceName);

    /**
     * @param $resourceName
     *
     * @return bool
     */
    function hasResource($resourceName);
}