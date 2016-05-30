<?php

namespace Adadgio\DoctrineDQLBundle\Collection;

use Doctrine\Common\Inflector\Inflector;
use Symfony\Component\PropertyAccess\PropertyAccess;

class ColumnAccess
{
    /**
     * Get collection column values.
     * @param  mixed  Array or ArrayCollection of arrays or objects
     * @param  string Property access path like "[property]"
     * @return array  Dataset of column values
     */
    public static function getColumnValues($collection, $propertyAccess)
    {
        $dataset = array();

        foreach ($collection as $row) {
            if (is_object($row)) {

                // get column value by entity getter
                $getter = self::accessorToGetter($propertyAccess);
                $dataset[] = $row->$getter();

            } else if (is_array($row)){

                $accessor = PropertyAccess::createPropertyAccessor();

                // access column value by array property access
                $propertyAccess = self::accessorToNormalizedAccessor($propertyAccess);
                $dataset[] = $accessor->getValue($row, $propertyAccess);

            } else {
                throw new \Exception('ColumnAccess unacceptable type. Collection items must be arrays or entities.');
            }
        }

        return $dataset;
    }

    /**
     * Translates a property access path to a getter
     * @param  string Property access path like "[property]"
     * @return string Entity getter like "getProperty"
     */
    private static function accessorToGetter($propertyAccess)
    {
        $columnName = str_replace(array('[', ']'), '', $propertyAccess);
        return 'get'.Inflector::classify($columnName);
    }

    /**
     * TFormats a one dimention property access event if no brackets are used.
     * @param  string Property access path like "[property]" or "property"
     * @return string Property access path like "[property]"
     */
    private static function accessorToNormalizedAccessor($propertyAccess)
    {
        if (strpos($propertyAccess, '[') > -1 && strpos($propertyAccess, ']')) {
            return $propertyAccess;
        } else {
            return sprintf('[%s]', $propertyAccess);
        }
    }
}
