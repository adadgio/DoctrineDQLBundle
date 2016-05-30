<?php

namespace Adadgio\DoctrineDQLBundle\Tests;

use Adadgio\DoctrineDQLBundle\DQL\Where;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Adadgio\DoctrineDQLBundle\Entity\TestEntity;
use Adadgio\DoctrineDQLBundle\Entity\TestEntityRepository;

class WhereTest extends \PHPUnit_Framework_TestCase
{
    private $em;

    public function setUp()
    {
        // self::bootKernel();
        //
        // $this->em = static::$kernel->getContainer()
        //     ->get('doctrine')
        //     ->getManager();
    }
    
    public function testRealCases()
    {
        // $entityA = new TestEntity(3, 'Tom Sawyer', 17, array('one' => 'Hello world'));
        // $entity = $this->getMock(TestEntity::class);
        //
        // $employee->expects($this->once())
        //     ->method('getName')
        //     ->will($this->returnValue('Tom Sawyer'));

        // $mockQb = \Mockery::mock('\Doctrine\ORM\QueryBuilder');
        //
        // $mockEm = \Mockery::mock('\Doctrine\ORM\EntityManager',
        //                   array('getRepository' => new TestEntityRepository(),
        //                         'getClassMetadata' => (object)array('name' => 'aClass'),
        //                         'persist' => null,
        //                         'flush' => null));
    }

    public function testGuessOperatorType()
    {
        $alias = 'e';
        $conditions = array();
        $parameters = array();

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
            'name1($LLIKE)'       => 'bern',
            'name2($RLIKE)'       => 'rnie',
        );

        foreach ($input as $field => $value) {

            $type = Where::scalarStatement($field);
            // $field = $type['field'];
            // $operator = $type['operator'];
            // $parameter = $type['parameter'];

            $fieldName = $type['field'];
            $conditions[$fieldName] = Where::createBuilderCondition($alias, $type, $value);
            $parameters[$fieldName] = Where::createParameterValue($type, $value);
        }

        // print_r($conditions);
        // print_r($parameters);

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

    protected function tearDown()
    {
        // parent::tearDown();
        //
        // $this->em->close();
        // $this->em = null; // avoid memory leaks
    }
}
