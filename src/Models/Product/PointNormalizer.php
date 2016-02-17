<?php

namespace Models\Product;

use Lib\Model\AbstractNormalizer;

class ProductNormalizer extends AbstractNormalizer
{
    protected function getNormalizationRulesList()
    {
        $normalizationRules = [
            [
                'normalizer' => '\Models\Point\PointNormalizer',
                'dataKey' => 'point',
                'dataType' => 'single'
            ]
        ];

        return array_merge(
            $normalizationRules,
            parent::getNormalizationRulesList()
        );
    }
}