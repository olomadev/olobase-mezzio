<?php

declare(strict_types=1);

namespace Olobase\Mezzio;

use Laminas\InputFilter\InputFilterInterface;

/**
 * @author Oloma <support@oloma.dev>
 *
 * Data manager interface
 */
interface DataManagerInterface
{
    /**
     * Returns to validation errors
     * 
     * @param  InputFilterInterface $inputFilter
     * @return array
     */
    public function setInputFilter(InputFilterInterface $inputFilter);

    /**
     * Returns to database model data
     * 
     * @param  string      $schema    schema class
     * @param  string|null $tablename optional tablename
     * @return array
     */
    public function getSaveData(string $schema, $tablename = null) : array;

    /**
     * Returns to view data
     * 
     * @param  string $schema schema class
     * @param  array  $row    data
     * @return array
     */
    public function getViewData(string $schema, array $row) : array;
}