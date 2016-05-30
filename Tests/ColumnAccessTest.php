<?php

namespace Adadgio\DoctrineDQLBundle\Tests;

use Adadgio\DoctrineDQLBundle\Accessor\ColumnAccess;
use Adadgio\DoctrineDQLBundle\Tests\Helper\TestEntity;

class ColumnAccessTest extends \PHPUnit_Framework_TestCase
{
    public function testArrayColumnAccess()
    {
        $collection = array(
            array(
                'id' => 34,
                'name' => 'Tom Saywer',
                'age' => 17,
                'tags' => array(
                    'one' => 'dungarees',
                )
            ),
            array(
                'id' => 35,
                'name' => 'Nelson Mandela',
                'age' => 84,
                'tags' => array(
                    'one' => 'beard',
                    'two' => 'glasses',
                )
            )
        );
        
        // test simple value access
        $idsA = ColumnAccess::getColumnValues($collection, '[id]');
        $idsB = ColumnAccess::getColumnValues($collection, 'id');

        $this->assertEquals($idsA, array(34,35));
        $this->assertEquals($idsB, array(34,35));

        // test multi-dimentional value access
        $tagsA = ColumnAccess::getColumnValues($collection, '[tags][one]');
        $tagsB = ColumnAccess::getColumnValues($collection, '[tags][two]');

        $this->assertEquals($tagsA, array('dungarees', 'beard'));
        $this->assertEquals($tagsB, array(null, 'glasses'));
    }

    public function testEntityColumnAccess()
    {
        $collection = new \Doctrine\Common\Collections\ArrayCollection(array(
            new TestEntity(37, 'Martin Super King', 65),
            new TestEntity(39, 'Elwood Jack', 45)
        ));

        $idsA = ColumnAccess::getColumnValues($collection, '[id]');
        $idsB = ColumnAccess::getColumnValues($collection, 'id');

        $this->assertEquals($idsA, array(37,39));
        $this->assertEquals($idsB, array(37,39));

        $namesA = ColumnAccess::getColumnValues($collection, '[name]');
        $namesB = ColumnAccess::getColumnValues($collection, 'name');

        $this->assertEquals($namesA, array('Martin Super King','Elwood Jack'));
        $this->assertEquals($namesB, array('Martin Super King','Elwood Jack'));
    }
}
