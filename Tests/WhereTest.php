<?php

namespace Adadgio\DoctrineDQLBundle\Tests;

use Adadgio\DoctrineDQLBundle\DQL\Where;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Adadgio\DoctrineDQLBundle\Entity\TestEntity;
use Adadgio\DoctrineDQLBundle\Entity\TestEntityRepository;

class WhereTest extends \PHPUnit_Framework_TestCase
{
    public function testGuessOperatorType()
    {
        $alias = 'e';

        $options = array(
            'category($IN)'     => array(2,3),
            'published'         => 1,
            'updated_at($>=)'   => '2016-01-01',
            'truc($NOT IN)'     => array(4,5),
            'created_at($<)'    => '2016-01-01',
            'name($LIKE)'       => 'bernie',
            'roles($NOT LIKE)'  => 'ROLE_USER',
            'ref($IS)'          => 'NULL',
            'ref2($IS)'         => 'NOT NULL',
            'date_at($BETWEEN)' => array('2016-01-01 05:00:00', '2016-04-25 10:30:00'),
            'e.name($LLIKE)'       => 'bern',
            'b.name($RLIKE)'       => 'rnie',
            '($OR)' => array(
                'age($>)'  => 45,
                'age($<=)' => 20
            )
        );

        $where = new Where('e', $options);
        $conditions = $where->getConditions();

        $statement = $conditions[0]->getStatement();
        $parameter = $conditions[0]->getValue();
        $this->assertEquals($statement, 'e.category IN (:category0)');
        $this->assertEquals($parameter, '2,3');

        $statement = $conditions[1]->getStatement();
        $parameter = $conditions[1]->getValue();
        $this->assertEquals($statement, 'e.published = :published1');
        $this->assertEquals($parameter, 1);

        $statement = $conditions[2]->getStatement();
        $parameter = $conditions[2]->getValue();
        $this->assertEquals($statement, 'e.updated_at >= :updated_at2');
        $this->assertEquals($parameter, '2016-01-01');
        
        $statement = $conditions[3]->getStatement();
        $parameter = $conditions[3]->getValue();
        $this->assertEquals($statement, 'e.truc NOT IN (:truc3)');
        $this->assertEquals($parameter, '4,5');
    }
}
