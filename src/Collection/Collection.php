<?php

namespace conta\Collection;

abstract class Collection implements \Iterator
{
    protected array $collection = [];

    protected int $pointer = 0;

    public function current()
    {
        return $this->collection[$this->pointer];
    }

    public function getAll(): array
    {
        return $this->collection;
    }

    public function next()
    {
        $this->pointer++;
    }

    public function key()
    {
        return $this->pointer;
    }

    public function valid()
    {
        return isset($this->collection[$this->pointer]);
    }

    public function rewind()
    {
        $this->pointer = 0;
    }
}