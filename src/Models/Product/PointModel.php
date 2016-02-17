<?php

namespace Models\Product;

use Lib\Model\AbstractCrudModel;

class ProductModel extends AbstractCrudModel
{
    public $modelName = 'product';

    public function getPrimaryActiveRecord()
    {
        return new ProductActiveRecord();
    }
}