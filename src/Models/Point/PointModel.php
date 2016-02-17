<?php

namespace Models\Point;

use Lib\Model\AbstractCrudModel;

class PointModel extends AbstractCrudModel
{
    public $modelName = 'point';

    public function getPrimaryActiveRecord()
    {
        return new PointActiveRecord();
    }

    public function validateAccess($id, $password)
    {
        /** @var PointActiveRecord $point */
        $point = $this->readOne(['_id' => $id]);

        return $point->getPassword() === $password;
    }
}