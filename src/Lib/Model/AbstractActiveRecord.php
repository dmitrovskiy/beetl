<?php

namespace Lib\Model;

use Application\Application;
use Purekid\Mongodm\Model as BaseActiveRecord;

abstract class AbstractActiveRecord extends BaseActiveRecord
{
    public static $useType = false;

    /** @var Application  */
    protected $app;

    public function __construct(
        array $data = [],
        $mapFields = false,
        $exists = false
    ) {
        parent::__construct($data, $mapFields, $exists);

        $this->app = Application::getInstance();
    }

    /**
     * @param bool $recursive
     * @param int  $deep
     *
     * @return array
     */
    public function getArrayData($recursive = true, $deep = 1)
    {
        $data = clone $this;
        $resultData = $data->toArray(['_type'], $recursive, $deep);

        return $resultData;
    }

    public function validate()
    {
    }

    protected function __preSave()
    {
        $result = parent::__preSave();
        $this->validate();

        return $result;
    }
}
