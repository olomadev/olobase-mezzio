<?php

declare(strict_types=1);

namespace Oloma\Php;

use ReflectionClass;
use Laminas\InputFilter\InputFilterInterface;

/**
 * @author Oloma <support@oloma.dev>
 *
 * Entity data manager
 */
class DataManager implements DataManagerInterface
{
    const ENTITY_OBJECT = 'object';
    const ENTITY_ARRAY = 'array';

    protected $inputFilter;

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        $this->inputFilter = $inputFilter;
    }

    /**
     * Returns to entity array
     * 
     * @param  string $schema      scheme class name
     * @param  array  $entityParts entity classes
     * @return array
     */
    public function getEntityData(string $schema, $entityParts = array()) : array
    {
        $data = $this->inputFilter->getData();
        $entityReflection = new ReflectionClass($schema);
        $entityProperties = $entityReflection->getProperties();

        $entityData = array();
        $entityArray = array();
        $arrayKeys = array();
        $noneEntityKeys = array();
        foreach ($entityProperties as $prop) {
            foreach ($entityParts as $key => $entityClass) {
                $name = $prop->getName();
                $classReflection = new ReflectionClass($entityClass);
                $entityType = $entityClass::ENTITY_TYPE;
                $classProps = $classReflection->getProperties();
                // props
                // 
                foreach ($classProps as $entityProperty) {
                    $entityPropName = (string)$entityProperty->getName();
                    if ($name == $entityPropName) {
                        // if we have new password field we don't want to update the hashed password
                        // 
                        if ($name == 'password' && ! empty($data['newPassword'])) {
                            break;
                        }
                        if (array_key_exists($name, $data)) { // if data has the entity element
                            $entityData[$key][$name] = $this->inputFilter->getValue($entityPropName);
                        }
                    }
                    // prop comments
                    $schemaPropertyComment = $prop->getDocComment();
                    //
                    // ObjectId support
                    // ["id": "ebf6b935-5bd8-46c1-877b-9c758073f278", "name", "blabala"]
                    // it converts object to string "id"
                    //
                    if (! empty($entityData[$key][$name]['id']) && strpos($schemaPropertyComment, "ObjectId") > 0) {
                        $objectIdValue = $entityData[$key][$name]['id'];
                        $entityData[$key][$name] = $objectIdValue;
                    }
                    // Array support e.g. ['userRoles'] = [[id => "", "name" => ""]] 
                    // 
                    if (strpos($schemaPropertyComment, 'type="array"')) {
                        $entityData[$name] = $this->inputFilter->getValue($name);
                    }
                    // Entity object support
                    //
                    if ($entityType == Self::ENTITY_OBJECT && isset($data[$key][$entityPropName])) {
                        $objectData = $this->inputFilter->getValue($key);
                        $entityData[$key][$entityPropName] = $objectData[$entityPropName];
                    }
                    // Entity array support
                    //
                    if (array_key_exists($key, $data) && $entityType == Self::ENTITY_ARRAY) {
                        $arrayKeys[$key][$entityPropName] = $entityPropName;
                    }
                }
            }
        }
        //
        // set no entity column names (find the column names which are defined in the Swagger schema)
        // 
        foreach(array_keys($entityParts) as $tableName) {
            if (array_key_exists($tableName, $entityData)) { // check whether it's defined in entity data
                foreach ($entityProperties as $prop) { // get only swagger schema variables
                    $propName = $prop->getName();
                    if ($this->inputFilter->has($propName) // check the schema has this input key in input filter class
                        && false == array_key_exists($propName, $entityData[$tableName])) {
                        $entityData[$propName] = $this->inputFilter->getValue($propName); // set input value
                    }
                }
            }
        }
        //
        // fill array data with input value
        // 
        foreach ($arrayKeys as $aKey => $aPropArray) {
            $arrayData = $this->inputFilter->getValue($aKey);
            foreach ($arrayData as $indexKey => $dVal) {
                foreach ($aPropArray as $aPropName) {
                    if (array_key_exists($aPropName, $dVal)) {
                        $entityData[$aKey][$indexKey][$aPropName] = $dVal[$aPropName];
                    }
                }
            }
        }
        // add primary id value
        //
        if ($this->inputFilter->has('id')) {
            $entityData['id'] = $this->inputFilter->getValue('id');    
        }
        return $entityData;
    }
}
