<?php

namespace Models\Product;

use Lib\Model\AbstractActiveRecord;

class ProductActiveRecord extends AbstractActiveRecord
{
    public static $collection = "Product";

    public static $attrs
        = [
            'name' => ['type' => 'string'],
            'description' => ['type' => 'string'],
            'features' => ['type' => 'string'],
            'point' => [
                'model' => 'Models\Point\PointActiveRecord',
                'type' => 'reference'
            ]
        ];

    public function getName()
    {
        return $this->__getter('name');
    }

    public function setName($name)
    {
        $this->__setter('name', $name);
    }

    public function getDescription()
    {
        return $this->__getter('description');
    }

    public function setDescription($description)
    {
        $this->__setter('description', $description);
    }

    public function getFeatures()
    {
        return $this->__getter('features');
    }

    public function setFeatures($features)
    {
        $this->__setter('features', $features);
    }

    public function getPoint()
    {
        return $this->__getter('point');
    }

    public function setPoint($point)
    {
        $this->__setter('point', $point);
    }
}