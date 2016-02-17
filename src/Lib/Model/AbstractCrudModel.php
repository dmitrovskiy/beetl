<?php

namespace Lib\Model;

use Exception\HttpException\BadRequestHttpException;
use Exception\HttpException\DataNotFoundHttpException;
use Lib\AbstractService;
use Lib\Event\Eventable;
use Purekid\Mongodm\Collection;
use Silex\Application;

abstract class AbstractCrudModel extends AbstractService implements CrudModelInterface
{
    use Eventable;

    public $modelName = 'undefined';

    public function __construct()
    {
        parent::__construct();

        $this->registerEventRange(
            [
                'read',
                'readOne',
                'beforeCreate',
                'afterCreate',
                'beforeUpdate',
                'afterUpdate',
                'beforeSave',
                'afterSave',
                'beforeDelete',
                'afterDelete',
            ]
        );
    }

    /**
     * @return AbstractActiveRecord
     */
    public abstract function getPrimaryActiveRecord();

    /**
     * @param array $data
     *
     * @return AbstractActiveRecord
     */
    public function create(array $data)
    {
        return $this->updateOrCreate(null, $data);
    }

    /**
     * @param array $criteria
     * @param array $sort
     * @param array $fields
     * @param null $limit
     * @param null $skip
     *
     * @return Collection
     */
    public function read(
        array $criteria = [],
        array $sort = [],
        array $fields = [],
        $limit = null,
        $skip = null
    ) {
        $activeRecord = $this->getPrimaryActiveRecord();
        /** @var Collection $data */
        $data = $activeRecord::find($criteria, $sort, $fields, $limit, $skip);

        $eventData = $this->eventData(['data' => $data]);
        $this->emitEvent(
            'read',
            'Found several data',
            $eventData
        );

        return $data;
    }

    /**
     * @param array $criteria
     * @param array $fields
     *
     * @return mixed
     * @throws DataNotFoundHttpException
     */
    public function readOne(
        array $criteria = [],
        array $fields = []
    ) {
        $activeRecord = $this->getPrimaryActiveRecord();
        /** @var mixed $data */
        $data = $activeRecord::one($criteria, $fields);

        //throwing an Exception if data doesn't exist
        if (!isset($data)) {
            throw new DataNotFoundHttpException("{$this->modelName} with such of this criteria doesn't exist");
        }

        $eventData = $this->eventData(['data' => $data]);
        $this->emitEvent('readOne', 'Found single data', $eventData);

        return $data;
    }

    /**
     * @param array $criteria
     * @param array $data
     *
     * @return AbstractActiveRecord
     * @throws \Exception
     */
    public function update(array $criteria = [], array $data)
    {
        $storingData = $this->read($criteria);
        $storingData->map(
            function ($item) use ($data) {
                $item = $this->updateOrCreate($item, $data);

                return $item;
            }
        );

        return $storingData;
    }

    /**
     * @param array $criteria
     * @param array $data
     *
     * @return AbstractActiveRecord
     */
    public function updateOne(array $criteria = [], array $data)
    {
        $activeRecord = $this->readOne($criteria);

        return $this->updateOrCreate($activeRecord, $data);
    }

    /**
     * @param array $criteria
     *
     * @return Collection
     */
    public function delete(array $criteria = [])
    {
        /** @var Collection $data */
        $data = $this->read($criteria);
        $data->each(
            function ($item) {
                $this->hardDelete($item);
            }
        );

        return $data;
    }

    /**
     * @param array $criteria
     *
     * @return AbstractActiveRecord
     */
    public function deleteOne(array $criteria = [])
    {
        $data = $this->readOne($criteria);

        return $this->hardDelete($data);
    }

    /**
     * @param AbstractActiveRecord $activeRecord
     *
     * @return AbstractActiveRecord
     */
    protected function hardDelete($activeRecord)
    {
        $originalData = clone $activeRecord;
        $eventData = $this->eventData(['data' => $originalData]);
        $this->emitEvent('beforeDelete', 'Before delete', $eventData);

        $activeRecord->delete();

        $this->emitEvent('afterDelete', 'After delete', $eventData);
        return $originalData;
    }

    /**
     * @param array $criteria
     *
     * @return int
     */
    public function count(array $criteria = [])
    {
        $activeRecord = $this->getPrimaryActiveRecord();

        return $activeRecord::count($criteria);
    }

    /**
     * @param array $criteria
     *
     * @return bool
     */
    public function has(array $criteria = [])
    {
        return 0 < $this->count($criteria);
    }

    /**
     * @param $activeRecord
     * @param array $data
     *
     * @return AbstractActiveRecord
     * @throws BadRequestHttpException
     * @throws \MongoCursorException
     */
    public function updateOrCreate($activeRecord, array $data = [])
    {
        $isNew = empty($activeRecord);
        $originalData = $isNew ? null : $activeRecord;

        $modifyingActiveRecord = $isNew
            ? $this->getPrimaryActiveRecord() : clone $activeRecord;
        $modifyingActiveRecord->update($data);

        $eventData = $this->eventData(
            ['data' => $modifyingActiveRecord],
            ['data' => $originalData],
            ['data' => $data]
        );

        if ($isNew) {
            $preEventName = 'beforeCreate';
            $preEventMessage = 'Before create';

            $postEventName = 'afterCreate';
            $postEventMessage = 'After create';
        } else {
            $preEventName = 'beforeUpdate';
            $preEventMessage = 'Before update';

            $postEventName = 'afterUpdate';
            $postEventMessage = 'After update';
        }

        $this->emitEvent($preEventName, $preEventMessage, $eventData);
        $this->emitEvent('beforeSave', 'Before save', $eventData);

        try {
            $modifyingActiveRecord->save();
        } catch (\MongoCursorException $e) {
            switch ($e->getCode()) {
                case 11000 :
                case 11001 : {
                    $message = "It seems like {$this->modelName} with similar data already exists";
                    throw new BadRequestHttpException($message, 400, $e);
                }
                default: {
                    throw $e;
                }
            }
        }

        $this->emitEvent('afterSave', 'After save', $eventData);
        $this->emitEvent($postEventName, $postEventMessage, $eventData);

        return $modifyingActiveRecord;
    }

    /**
     * @param array $response
     * @param array $original
     * @param array $addition
     *
     * @return array
     */
    protected function eventData($response = [], $original = [], $addition = [])
    {
        return [
            'response' => $response,
            'original' => $original,
            'addition' => $addition,
        ];
    }
}