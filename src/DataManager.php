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
    protected $inputFilter;

    /**
     * Returns to validation errors
     * 
     * @param  InputFilterInterface $inputFilter
     * @return array
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        $this->inputFilter = $inputFilter;
    }

    /**
     * Returns to database model data
     * 
     * @param  string      $schema    schema class
     * @param  string|null $tablename optional tablename
     * @return array
     */
    public function getSaveData(string $schema, string $tablename = null) : array
    {
        $data = $this->inputFilter->getData();
        $reflection = new ReflectionClass($schema);
        $schemaProperties = $reflection->getProperties();
        $table = $tablename;      
        if ($tablename == null) { // create auto table name
            $schemaClassName = strtolower($reflection->getShortName());
            $schemaClassName = rtrim($schemaClassName, "save");    
            $table = $schemaClassName;      
        } 
        $schemaData = array();
        foreach ($schemaProperties as $prop) {
            //
            // get prop name
            // 
            $name = $prop->getName();
            //
            // one dimensional data
            //
            if (array_key_exists($name, $data)) { // if data has the entity element
                $schemaData[$table][$name] = $this->inputFilter->getValue($name);
            }
            //
            // get prop comments
            // 
            $schemaPropComment = $prop->getDocComment();

            // Object and Array support
            // 
            // Object support: ['userDomain'] = [name => "", url => ""]
            // Array support: ['userRoles'] = [[id => "", "name" => ""]] 
            // 
            if (strpos($schemaPropComment, "@var object") > 0 
                || strpos($schemaPropComment, 'type="array"')) {
                $schemaData[$name] = $this->inputFilter->getValue($name);
                unset($schemaData[$table][$name]); // remove from main table
            }
            //
            // ObjectId support
            // ["id": "ebf6b935-5bd8-46c1-877b-9c758073f278", "name": "Label"]
            // it converts object to string "id"
            //
            if (! empty($schemaData[$table][$name]['id']) && strpos($schemaPropComment, "ObjectId") > 0) {
                $schemaData[$table][$name] = $schemaData[$table][$name]['id'];
            }
        }
        // add primary id value
        //
        if ($this->inputFilter->has('id')) {
            $schemaData['id'] = $this->inputFilter->getValue('id');    
        }
        return $schemaData;
    }

    /**
     * Returns to view data
     * 
     * @param  string $schema schema class
     * @param  array  $row    data
     * @return array
     */
    public function getViewData(string $schema, array $row) : array
    {
        $viewData = array();
        $reflection = new ReflectionClass($schema);
        $classNamespace = $reflection->getNamespaceName();
        $schemaProperties = $reflection->getProperties();
        foreach ($schemaProperties as $prop) {
            //
            // get prop name
            // 
            $name = $prop->getName();
            //
            // get prop comments
            // 
            $schemaPropComment = $prop->getDocComment();
            //
            // search for direct objects
            //
            if (strpos($schemaPropComment, '@var object') > 0) {
                preg_match('#".*?"#', $schemaPropComment, $matches);
                if (! empty($matches[0])) {
                    $matchedStr = trim($matches[0], '"');
                    $exp = explode("/" ,$matchedStr);
                    $objectClassName = end($exp);
                    if ($objectClassName == 'ObjectId') {
                        $namespaceArray = explode("\\", $classNamespace);
                        $appName = reset($namespaceArray);
                        $objectRow = json_decode($row[$name], true);
                        $viewData[$name] = $this->getViewData($appName."\Schema\ObjectId", $objectRow);
                    } else {
                        if (! empty($row[$name])) {
                            $objectRow = json_decode($row[$name], true);
                        } else {
                            $objectRow = $row;
                        }
                        $viewSchemaClass = $classNamespace."\\".$objectClassName;
                        if (file_exists(PROJECT_ROOT."/src/$appName/src/Schema/".$objectClassName.".php")) {  // look for common schema
                            $viewSchemaClass = $appName."\Schema\\".$objectClassName;
                        }
                        $viewData[$name] = $this->getViewData($viewSchemaClass, $objectRow);
                    }
                }
            } else {
                if (strpos($schemaPropComment, '@var string') > 0) {
                    $viewData[$name] = array_key_exists($name, $row) ? (string)$row[$name] : null;
                } else if (strpos($schemaPropComment, '@var integer') > 0) {
                    $viewData[$name] = array_key_exists($name, $row) ? (int)$row[$name] : null;
                } else if (strpos($schemaPropComment, '@var number') > 0) {
                    $viewData[$name] = array_key_exists($name, $row) ? $this->getNumeric($row[$name]) : null;
                } else if (strpos($schemaPropComment, '@var boolean') > 0) {
                    $viewData[$name] = array_key_exists($name, $row) ? (bool)$row[$name] : null;
                } else if (strpos($schemaPropComment, "ObjectId") > 0) {
                    $viewData[$name] = array_key_exists($name, $row) ? json_decode($row[$name], true) : null;
                } else if (strpos($schemaPropComment, 'type="array"')) {
                    $viewData[$name] = array_key_exists($name, $row) ? (array)$row[$name] : null;
                }
            }
        }
        return $viewData;
    }

    /**
     * Numeric helper
     * 
     * @param  mixed $val value
     * @return number
     */
    protected function getNumeric($val)
    {
        if (is_numeric($val)) {
            return $val + 0;
        }
        return 0;
    }

}
