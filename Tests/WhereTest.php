<?php

namespace Adadgio\DoctrineDQLBundle\Tests;

use Adadgio\DoctrineDQLBundle\Common\Where;

class WhereTest extends \PHPUnit_Framework_TestCase
{
    public function testGuessOperatorType()
    {
        $alias = 'e';
        $conditions = array();

        $input = array(
            'category($IN)'     => array('ONE', 'TWO'),
            'published'         => 1,
            'updated_at($>=)'   => '2016-01-01',
            'truc($NOT IN)'     => array('THREE', 'FOUR'),
            'created_at($<)'    => '2016-01-01',
            'name($LIKE)'       => 'bernie',
            'roles($NOT LIKE)'  => 'ROLE_USER',
            'ref($IS)'          => 'NULL',
            'ref2($IS)'         => 'NOT NULL',
            'date_at($BETWEEN)' => array('2016-01-01 05:00:00', '2016-04-25 10:30:00'),
        );

        foreach ($input as $field => $value) {

            $type = Where::scalarStatement($field);
            // $field = $type['field'];
            // $operator = $type['operator'];
            // $parameter = $type['parameter'];

            $fieldName = $type['field'];
            $conditions[$fieldName] = Where::createBuilderCondition($alias, $type, $value);
        }

        $this->assertArraySubset(array('category' => 'e.category IN (:category)'), $conditions);
        $this->assertArraySubset(array('published' => 'e.published = :published'), $conditions);
        $this->assertArraySubset(array('updated_at' => 'e.updated_at >= :updated_at'), $conditions);
        $this->assertArraySubset(array('truc' => 'e.truc NOT IN (:truc)'), $conditions);
        $this->assertArraySubset(array('created_at' => 'e.created_at < :created_at'), $conditions);

        $this->assertArraySubset(array('ref' => 'e.ref IS NULL'), $conditions);
        $this->assertArraySubset(array('ref2' => 'e.ref2 IS NOT NULL'), $conditions);
        $this->assertArraySubset(array('name' => 'e.name LIKE :name'), $conditions);
        $this->assertArraySubset(array('roles' => 'e.roles NOT LIKE :roles'), $conditions);
        $this->assertArraySubset(array('date_at' => 'e.date_at BETWEEN :date_at1 AND :date_at2'), $conditions);
    }
}
