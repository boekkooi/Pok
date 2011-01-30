<?php
namespace Pok\Component\Metadata\Extension\Dependency;

class Collection implements \ArrayAccess, \IteratorAggregate {
    protected $dependencies = array();

    public function offsetExists($offset)
    {
        return isset($this->dependencies[$offset]);
    }

    public function offsetGet($offset)
    {
        if (isset($this->dependencies[$offset])) {
            return $this->dependencies[$offset];
        }

        throw new \LogicException('Invalid source');
    }

    public function offsetSet($offset, $value)
    {
        if (!($value instanceof Dependency)) {
            throw new \LogicException('Excepted instance of Pok\Component\Metadata\Extension\Dependency\Dependency.');
        }

        if ($offset === null) {
            $this->dependencies[] = $value;
        } else {
            $this->dependencies[$offset] = $value;
        }
    }

    public function offsetUnset($offset)
    {
        unset($this->dependencies[$offset]);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->dependencies);
    }

    public function import(array $dependencies) {
        foreach ($dependencies as $dependency) {
            $this[] = $dependency;
        }
        return $this;
    }
}
