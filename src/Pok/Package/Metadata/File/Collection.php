<?php
namespace Pok\Component\Metadata\Extension\File;

class Collection implements \ArrayAccess, \IteratorAggregate {
    protected $files = array();

    public function offsetExists($offset)
    {
        return isset($this->files[$offset]);
    }

    public function offsetGet($offset)
    {
        if (isset($this->files[$offset])) {
            return $this->files[$offset];
        }

        throw new \LogicException('Invalid source');
    }

    public function offsetSet($offset, $file)
    {
        if (!($file instanceof File)) {
            throw new \LogicException('Excepted instance of Pok\Component\Metadata\Extension\File\File.');
        }

        $this->files[$file->getSource()] = $file;
    }

    public function offsetUnset($offset)
    {
        unset($this->files[$offset]);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->files);
    }
}
