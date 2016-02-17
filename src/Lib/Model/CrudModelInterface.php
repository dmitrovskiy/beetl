<?php

namespace Lib\Model;

use Purekid\Mongodm\Collection;

interface CrudModelInterface
{
    /**
     * @param array $data
     *
     * @return AbstractActiveRecord
     */
    function create(array $data);

    /**
     * @param array $criteria
     * @param array $sort
     * @param array $fields
     * @param null $limit
     * @param null $skip
     *
     * @return Collection
     */
    function read(array $criteria = [], array $sort = [], array $fields = [], $limit = null, $skip = null);

    /**
     * @param array $criteria
     * @param array $fields
     *
     * @return AbstractActiveRecord
     */
    function readOne(array $criteria = [], array $fields = []);

    /**
     * @param array $criteria
     * @param array $data
     *
     * @return Collection
     */
    function update(array $criteria = [], array $data);

    /**
     * @param array $criteria
     * @param array $data
     *
     * @return AbstractActiveRecord
     */
    function updateOne(array $criteria = [], array $data);

    /**
     * @param array $criteria
     *
     * @return AbstractActiveRecord
     */
    function delete(array $criteria = []);

    /**
     * @param array $criteria
     *
     * @return AbstractActiveRecord
     */
    function deleteOne(array $criteria = []);

    /**
     * @param array $criteria
     *
     * @return int
     */
    function count(array $criteria = []);

    /**
     * @param array $criteria
     *
     * @return bool
     */
    function has(array $criteria = []);
}
