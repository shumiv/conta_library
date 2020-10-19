<?php
namespace conta\Collection;

class IdMap implements \Iterator
{
    private array $collection = [];
    private array $keys = [];
    private int $pointer = 0;

    public function add(int $key, int $value): void
    {
        $this->collection[$key] = $value;
        $this->keys[] = $key;
    }

    /**
     * @param int $key
     * @return int -1 if there is no such key
     */
    public function get(int $key): int
    {
        return $this->collection[$key] ?? -1;
    }
    /**
     * @inheritDoc
     */
    public function current(): int
    {
        return $this->collection[$this->key()];
    }

    /**
     * @inheritDoc
     */
    public function next()
    {
        $this->pointer++;
    }

    /**
     * @inheritDoc
     */
    public function key(): int
    {
        return $this->keys[$this->pointer];
    }

    /**
     * @inheritDoc
     */
    public function valid()
    {
        return isset($this->keys[$this->pointer]);
    }

    /**
     * @inheritDoc
     */
    public function rewind()
    {
        $this->pointer = 0;
    }
}