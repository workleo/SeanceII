<?php
namespace TestStorage;
class TestClass
{
  private $name;
  private $count;


    public function __construct()
    {
        $this->name='TestClass';
        $this->count=0;
    }


    public function getName(): string
    {
        return $this->name;
    }


    public function setName(string $name)
    {
        $this->name = 'TestClass.'.$name;
    }


    public function getCount(): int
    {
        return $this->count;
    }


    public function setCount(int $count)
    {
        $this->count = $count;
    }


}