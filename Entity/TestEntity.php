<?php

namespace Adadgio\DoctrineDQLBundle\Entity;

class TestEntity
{
    private $id;
    private $name;
    private $age;
    private $tags;

    public function __construct($id = null, $name = null, $age = 0, array $tags = array())
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

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getAge()
    {
        return $this->age;
    }

    public function setAge($age)
    {
        $this->age = $age;

        return $this;
    }

    public function getTags()
    {
        return $this->tags;
    }

    public function setTags(array $tags = array())
    {
        $this->tags = $tags;

        return $this;
    }
}
