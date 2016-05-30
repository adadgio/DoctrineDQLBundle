<?php

namespace Adadgio\DoctrineDQLBundle\DQL;

class OrderBy
{
    /**
     * @param string alias
     * @param Query builder
     * @param array Order by such as array('field_name' => 'ASC|DESC')
     */
    public static function orderBy($alias, $builder, array $orderBy = array())
    {
        if (empty($orderBy)) {
            return $builder;
        }

        $by = array_keys($orderBy)[0];
        $order = array_values($orderBy)[0];

        $builder
            ->addOrderBy(sprintf('%s.%s', $alias, $by), $order);

        return $builder;
    }
}
