<?php

namespace Adadgio\DoctrineDQLBundle\Tests\Helper;

class TestEntity
{
    private $id;
    private $name;
    private $age;
    private $tags;

    public function __construct($id, $name, $age, array $tags = array())
    {
        $this->id = $id;
        $this->name = $name;
        $this->age = $age;
        $this->tags = $tags;
    }
    
    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getAge()
    {
        return $this->age;
    }

    public function getTags()
    {
        return $this->tags;
    }
}
