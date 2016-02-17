<?php

namespace Lib\Model;

use Exception\InvalidArgumentException;
use Lib\AbstractService;
use Purekid\Mongodm\Collection;

class AbstractNormalizer extends AbstractService
{
    /**
     * @param mixed $data
     *
     * @return array
     * @throws InvalidArgumentException
     */
    public function normalize($data)
    {
        if (!isset($data)) {
            throw new InvalidArgumentException('Data cannot be null', 500);
        } else {
            if (!(($data instanceof Collection)
                || ($data instanceof AbstractActiveRecord))
            ) {
                throw new InvalidArgumentException(
                    'Data should be instance of ActiveRecord or Collection', 500
                );
            }
        }

        $arrayData = $this->buildArrayData($data);

        $isCollection = $data instanceof Collection;
        $normalizedData = $this->normalizeData($arrayData, $isCollection);

        return $normalizedData;
    }

    /**
     * @param array $data
     * @param bool|false $collection
     *
     * @return array
     */
    protected function normalizeData(array $data, $collection = false)
    {
        if ($collection === true) {
            foreach ($data as &$value) {
                $this->normalizeItem($value);
            }
        } else {
            $this->normalizeItem($data);
        }

        return $data;
    }

    /**
     * @param mixed $data
     *
     * @return array
     */
    private function buildArrayData($data)
    {
        $result = [];

        if ($data instanceof AbstractActiveRecord) {
            //if this is a model unit instance
            $result = $data->getArrayData();
        } else {
            //else this is a collection instance or array
            /** @var AbstractActiveRecord $activeRecord */
            foreach ($data as $activeRecord) {
                array_push($result, $activeRecord->getArrayData());
            }
        }

        return $result;
    }

    protected function normalizeItem(array &$data)
    {
        $this->hideUnnecessaryFields($data);

        $normalizationRules = $this->getNormalizationRulesList();
        $this->normalizeByRulesList($data, $normalizationRules);
    }

    protected function hideUnnecessaryFields(array &$data)
    {
        if (isset($data['_id'])) {
            //changing _id on id
            $data['id'] = $data['_id'] instanceof \MongoId ? $data['_id']->{'$id'} : $data['_id'];

            unset($data['_id']);
        }
    }

    protected function normalizeByRulesList(array &$data, array $rules)
    {
        foreach ($rules as $rule) {
            if (isset($data[$rule['dataKey']])) {
                if (isset($rule['dataType'])) {
                    switch ($rule['dataType']) {
                        case 'single': {
                            if (empty($data[$rule['dataKey']])) {
                                unset($data[$rule['dataKey']]);
                            } else {
                                if (isset($data[$rule['dataKey']]['$id'])
                                    && $data[$rule['dataKey']]['$id'] instanceof
                                    \MongoId
                                ) {
                                    $data[$rule['dataKey']]
                                        = $data[$rule['dataKey']]['$id']->{'$id'};
                                } else {
                                    /** @var AbstractNormalizer $normalizer */
                                    $normalizer
                                        = $this->app[$rule['normalizer']];
                                    $normalizer->normalizeItem(
                                        $data[$rule['dataKey']]
                                    );
                                }
                            }
                            break;
                        }
                        case 'collection': {
                            /** @var AbstractNormalizer $normalizer */
                            $normalizer = $this->app[$rule['normalizer']];
                            foreach ($data[$rule['dataKey']] as &$item) {
                                if (empty($item)) {
                                    unset($item);
                                } else {
                                    if (isset($item['$id'])
                                        && $item['$id'] instanceof \MongoId
                                    ) {
                                        $item = $item['$id']->{'$id'};
                                    } else {
                                        $normalizer->normalizeItem($item);
                                    }
                                }
                            }
                            break;
                        }
                        case 'dateTime': {
                            $data[$rule['dataKey']] = date(
                                'c',
                                $data[$rule['dataKey']]->sec
                            );
                            break;
                        }
                        case 'date': {
                            $date = new \DateTime();
                            $date->setTimestamp($data[$rule['dataKey']]->sec);
                            $data[$rule['dataKey']] = $date->format('Y-m-d');
                            break;
                        }
                    }
                }
            }
        }
    }

    protected function getNormalizationRulesList()
    {
        return [];
    }
}