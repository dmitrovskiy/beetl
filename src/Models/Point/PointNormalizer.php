<?php

namespace Models\Point;

use Lib\Model\AbstractNormalizer;

class PointNormalizer extends AbstractNormalizer
{
    protected function hideUnnecessaryFields(array &$data)
    {
        parent::hideUnnecessaryFields($data);
        unset($data['password']);
    }
}