<?php

namespace Models\Point;

use Lib\Model\AbstractActiveRecord;

class PointActiveRecord extends AbstractActiveRecord
{
    public static $collection = "Point";

    public static $attrs
        = [
            'name' => ['type' => 'string'],
            'password' => ['type' => 'string']
        ];

    public function getName()
    {
        return $this->__getter('name');
    }

    public function setName($name)
    {
        $this->__setter('name', $name);
    }

    public function getPassword()
    {
        return $this->__getter('password');
    }

    public function setPassword($password)
    {
        $this->__setter('password', $password);
    }
}