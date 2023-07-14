<?php

declare(strict_types=1);

namespace Oloma\Php;

use Laminas\Db\Sql\SqlInterface;

/**
 * @author Oloma <support@oloma.dev>
 *
 * Column filters interface
 */
interface ColumnFiltersInterface
{
    /**
     * Reset column filter object
     *
     * @return void
     */
    public function clear();

    /**
     * Set columns
     *
     * @param object $select
     */
    public function setSelect(SqlInterface $select);

    /**
     * Set columns
     *
     * @param object $select
     */
    public function getSelect() : SqlInterface;

    /**
     * Set columns
     *
     * @param array $columns columns
     */
    public function setColumns(array $columns);

    /**
     * Set like columns
     * 
     * @param array $columns
     */
    public function setLikeColumns(array $columns);

    /**
     * Set where columns
     * 
     * @param array $columns
     */
    public function setWhereColumns(array $columns);

    /**
     * Unset columns
     * 
     * @param  array  $columns columns
     */
    public function unsetColumns(array $columns);

    /**
     * Set sql alias : CONCAT(u.firstname ,' ', u.lastname) AS name
     *
     * @param string $name  requested column name
     * @param string $alias
     */
    public function setAlias(string $name, string $alias);

    /**
     * Set parent columns
     * 
     * @param string $parent  parent object
     * @param array  $columns column names
     */
    public function setParentColumns(string $parent, array $columns);

    /**
     * Returns to normalized data
     * 
     * @return array
     */
    public function getRawData(): array;

    /**
     * Set filter data (GET or POST)
     *
     * @param array $data
     */
    public function setData(array $data);

    /**
     * Set date filter for date columns
     * 
     * @param string $dateColumn column name
     * @param string $endDate if exists
     */
    public function setDateFilter($dateColumn, $endDate = null);

    /**
     * Returns to filtered column => value
     *
     * @return array
     */
    public function getColumnData() : array;

    /**
     * Returns to "like" data column => value
     *
     * @return array
     */
    public function getLikeData() : array;

    /**
     * Returns to "where" data column => value
     *
     * @return array
     */
    public function getWhereData() : array;

    /**
     * Returns to unfiltered data
     *
     * @return array
     */
    public function getData() : array;

    /**
     * Returns to filtered order data: [name ASC, email DESC]
     *
     * @return array
     */
    public function getOrderData() : array;

    /**
     * Returns to search data: columns => array('str1', 'str2')
     *
     * @return array
     */
    public function getSearchData() : array;

    /**
     * Returns to true if not empty otherwise false
     *
     * @return boolean
     */
    public function searchDataIsNotEmpty() : bool;

    /**
     * Returns to true if empty otherwise false
     *
     * @return boolean
     */
    public function searchDataEmpty() : bool;

    /**
     * Returns to true if not empty otherwise false
     *
     * @return boolean
     */
    public function likeDataIsEmpty() : bool;

    /**
     * Returns to true if not empty otherwise false
     *
     * @return boolean
     */
    public function likeDataIsNotEmpty() : bool;

    /**
     * Returns to true if empty otherwise false
     *
     * @return boolean
     */
    public function whereDataIsEmpty() : bool;

    /**
     * Returns to true if not empty otherwise false
     *
     * @return boolean
     */
    public function whereDataIsNotEmpty(): bool;

    /**
     * Returns to true if not empty otherwise false
     *
     * @return boolean
     */
    public function orderDataIsNotEmpty() : bool;

    /**
     * Returns to colum names
     *
     * @return array
     */
    public function getColumns() : array;

}