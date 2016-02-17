<?php

namespace Application;

class ConfigList
{
    public function getConfigList()
    {
        return [
            'DB_HOST',
            'DB_NAME',
            'DB_OPTIONS'
        ];
    }
}