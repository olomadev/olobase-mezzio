<?php

declare(strict_types=1);

namespace Oloma\Php;

use Laminas\InputFilter\InputFilterInterface;

/**
 * @author Oloma <support@oloma.dev>
 *
 * Data (entity) manager interface
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
     * Returns to entity array
     * 
     * @param  string $schema      scheme class name
     * @param  array  $entityParts entity classes
     * @return array
     */
    public function getEntityData(string $schema, $entityParts = array()) : array;
}