<?php

declare(strict_types=1);

namespace Olobase\Mezzio;

use ReflectionClass;
use Laminas\InputFilter\InputFilterInterface;
use Olobase\Mezzio\Exception\UncodedObjectIdException;

/**
 * @author Oloma <support@oloma.dev>
 *
 * Entity data manager
 */
class DataManager implements DataManagerInterface
{
    protected $config;
    protected $inputFilter;

    /**
     * Set configurations
     * 
     * @param array $config config
     */
    public function __construct(array $config)
    {
        $this->config = isset($config['data_manager']) ? $config['data_manager'] : array();
    }

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
    public function getSaveData(string $schema, $tablename = null) : array
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
            // Get prop name
            // 
            $name = $prop->getName();
            //
            // Check data
            //
            if (array_key_exists($name, $data)) { // if data has the entity element
                $schemaData[$table][$name] = $this->inputFilter->getValue($name);
            }
            //
            // Detect types
            // 
            $schemaPropComment = $prop->getDocComment();
            $hasArray = (strpos($schemaPropComment, 'type="array"') > 0) ? true : false;
            $hasObject = (strpos($schemaPropComment, "@var object") > 0) ? true : false;
            $isObjectId = (strpos($schemaPropComment, "ObjectId") > 0) ? true : false;
            //
            // Array support
            // 
            // Array example: ['userRoles'] = [[id => "", "name" => ""]] 
            // 
            if ($hasArray) {
                $schemaData[$name] = $this->inputFilter->getValue($name);
                //
                // ObjectId support in array schema
                // 
                foreach($schemaData[$name] as $key => $val) {
                    foreach ($val as $k => $v) {
                        if (! empty($schemaData[$name][$key][$k]['id'])) {
                            $schemaData[$name][$key][$k] = $schemaData[$name][$key][$k]['id'];
                        }
                    }
                }
                unset($schemaData[$table][$name]); // remove from main table
            }
            //
            // Custom object support
            // 
            // Object example: ['userDomain'] = [name => "", url => ""]
            //
            if ($hasObject && false == $isObjectId) {
                $schemaData[$name] = $this->inputFilter->getValue($name);
                unset($schemaData[$table][$name]); // remove from main table
            }
            //
            // ObjectId support
            // 
            // ["id": "ebf6b935-5bd8-46c1-877b-9c758073f278", "name": "Label"]
            // it converts object to string "id"
            //
            if (! empty($schemaData[$table][$name]['id']) && $isObjectId) {
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
        $namespaceArray = explode("\\", $classNamespace);
        $commonName = $this->config['common_schema_module'] ?? null;
        $appName = reset($namespaceArray);

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
                        if (array_key_exists($name, $row)) {
                            $objectRow = json_decode($row[$name], true);
                            if (! is_array($objectRow)) {
                                throw new UncodedObjectIdException(
                                    sprintf(
                                        'The object id field "%s" must be encoded with JSON_OBJECT() function in your sql code.',
                                        $name
                                    )
                                );
                            }
                            $viewData[$name] = $this->getViewData("Common\Schema\ObjectId", $objectRow);
                        } else {
                            $viewData[$name] = null;
                        }
                    } else {
                        if (! empty($row[$name])) {
                            $objectRow = json_decode($row[$name], true);
                        } else {
                            $objectRow = $row;
                        }
                        $viewSchemaClass = $classNamespace."\\".$objectClassName;
                        if (file_exists(PROJECT_ROOT."/src/$appName/src/Schema/".$objectClassName.".php")) {  // look for module schema
                            $viewSchemaClass = $appName."\Schema\\".$objectClassName;
                        } else if (file_exists(PROJECT_ROOT."/src/$commonName/src/Schema/".$objectClassName.".php")) { // look for common schema
                            $viewSchemaClass = $commonName."\Schema\\".$objectClassName;
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
                    $viewData[$name] = (array_key_exists($name, $row) && is_string($row[$name])) ? json_decode($row[$name], true) : null;
                } else if (strpos($schemaPropComment, '@var array')) {
                    $viewData[$name] = array_key_exists($name, $row) ? (array)$row[$name] : array();
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
