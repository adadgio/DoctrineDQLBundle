<?php

namespace Adadgio\DoctrineDQLBundle\Tests;

use Adadgio\DoctrineDQLBundle\Collection\IndexedCollection;
use Adadgio\DoctrineDQLBundle\Tests\Helper\TestEntity;

class IndexedCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testArrayCollection()
    {
        $collection = array(
            array(
                'id' => 37,
                'name' => 'Tom Saywer',
                'age' => 17,
                'tags' => array(
                    'one' => 'dungarees',
                )
            ),
            array(
                'id' => 39,
                'name' => 'Nelson Mandela',
                'age' => 84,
                'tags' => array(
                    'one' => 'beard',
                    'two' => 'glasses',
                )
            )
        );

        $idsA = IndexedCollection::indexBy($collection, '[id]');
        $idsB = IndexedCollection::indexBy($collection, '[tags][one]');

        $this->assertEquals(array_keys($idsA), array(37, 39));
        $this->assertEquals(array_keys($idsB), array('dungarees', 'beard'));
    }

    public function testEntityCollection()
    {
        $collection = new \Doctrine\Common\Collections\ArrayCollection(array(
            new TestEntity(37, 'Martin Super King', 65, array('one' => 'dungarees')),
            new TestEntity(39, 'Elwood Jack', 45, array('one' => 'beard')),
        ));

        $idsA = IndexedCollection::indexBy($collection, '[id]');
        
        $this->assertEquals(array_keys($idsA), array(37, 39));
    }
}
