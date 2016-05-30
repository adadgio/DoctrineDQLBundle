<?php

namespace Adadgio\DoctrineDQLBundle\Collection;

use Doctrine\Common\Inflector;
use Symfony\Component\PropertyAccess\PropertyAccess;

class IndexedCollection
{
    /**
     * Chooses which method to use to index the collection.
     *
     * @param array  Collection as array
     * @param string Property access string
     * @return array
     */
    public static function indexBy($collection = array(), $propertyAccess)
    {
        if (count($collection) === 0) {
            return array();
        }

        // determine mode automatically
        if (is_array($collection[0])) {
            return self::indexByArrayAccess($propertyAccess, $collection);
        } else if (is_object($collection[0])) {
            return self::indexByEntityGetter($propertyAccess, $collection);
        } else {
            throw new \Exception('Invalid collection, indexed collection must be a collection of arrays or object/entities.');
        }
    }

    /**
     * Indexes the collection by array access.
     *
     * @param string Property access string
     * @param array Collection as array
     * @return array
     */
    private static function indexByArrayAccess($propertyAccess, array $collection = array())
    {
        $dataset = array();
        $accessor = PropertyAccess::createPropertyAccessor();

        foreach ($collection as $row) {
            $index = $accessor->getValue($row, $propertyAccess);
            $dataset[$index] = $row;
        }

        return $dataset;
    }

    /**
     * Indexes the collection by entity getter.
     *
     * @param string Property access string
     * @param array Collection as array
     * @return array
     */
    private static function indexByEntityGetter($propertyAccess, $collection = array())
    {
        $dataset = array();
        $chain = self::getMethodsChain($propertyAccess);

        foreach ($collection as $entity) {

            $index = null;

            // chain all method to get the final index value
            // warning, not to use with relationships!
            foreach ($chain as $propAccess) {
                $get = 'get'.Inflector\Inflector::classify($propAccess);
                $index = $entity->$get();
            }

            $dataset[$index] = $entity;
        }

        return $dataset;
    }

    /**
     * Get a list of methods to try in a chain to get the index value.
     *
     * @param string Property access
     * @return array Method chains (need to be getterized later)
     */
    private static function getMethodsChain($propertyAccess)
    {
        preg_match('~\[([a-z_]+)\](?:\[([a-z_]+)\])?~', $propertyAccess, $matches);

        array_shift($matches);
        return $matches;
    }
}
