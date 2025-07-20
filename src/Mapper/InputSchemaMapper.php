<?php

namespace Olobase\Mezzio\Mapper;

use ReflectionClass;
use Laminas\InputFilter\InputFilterInterface;
use OpenApi\Attributes\Property as OAProperty;
use Olobase\Mezzio\Attribute\MapField;

class InputSchemaMapper
{
    public function map(InputFilterInterface $inputFilter, string $schema, ?string $tablename = null): array
    {
        $data = $inputFilter->getData();
        $reflection = new ReflectionClass($schema);
        $properties = $reflection->getProperties();

        $table = $tablename ?? strtolower(rtrim($reflection->getShortName(), 'save'));
        $schemaData = [];

        foreach ($properties as $prop) {
            $propertyName = $prop->getName();
            $attributes = $prop->getAttributes(MapField::class);
            $mapField = $attributes[0]?->newInstance();
            $type = $mapField?->type ?? 'string';

            // OA\Property(property="is_active") → 'is_active'
            $oaAttrs = $prop->getAttributes(OAProperty::class);
            $columnName = $propertyName;
            if (isset($oaAttrs[0])) {
                $column = $oaAttrs[0]->newInstance();
                $columnName = $column->property ?? $propertyName;
            }

            if (!array_key_exists($columnName, $data)) {
                continue;
            }

            $value = $inputFilter->getValue($columnName);

            match ($type) {
                'array' => $schemaData[$propertyName] = $value,
                'object' => $schemaData[$propertyName] = $value,
                'objectId' => $schemaData[$table][$propertyName] = $value['id'] ?? null,
                default => $schemaData[$table][$propertyName] = $value,
            };

            if (in_array($type, ['array', 'object'])) {
                unset($schemaData[$table][$propertyName]);
            }
        }

        if ($inputFilter->has('id')) {
            $schemaData['id'] = $inputFilter->getValue('id');
        }

        return $schemaData;
    }
}
