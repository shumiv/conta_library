<?php
namespace conta\Application\Feedback;

class EntryCollection implements \Iterator
{
    /**
     * @var Entry[]
     */
    protected array $entries = [];

    protected int $total = 0;

    protected int $pointer = 0;

    public function add(Entry $entry): void
    {
        array_push($this->entries, $entry);
        $this->total++;
    }

    public function clear(int $type = null): void
    {
        if (is_null($type)) {
            $this->clearAll();
        } else {
            $this->clearType($type);
        }
    }

    public function current(): ?Entry
    {
        return $this->getEntry();
    }

    public function next(): void
    {
        $entry = $this->getEntry();
        if (! is_null($entry)) {
            $this->pointer++;
        }
    }

    public function key(): int
    {
        return $this->pointer;
    }

    public function valid(): bool
    {
        return ! is_null($this->current());
    }

    public function rewind(): void
    {
        $this->pointer = 0;
    }

    private function getEntry(): ?Entry
    {
        return $this->entries[$this->pointer] ?? null;
    }

    private function clearAll(): void
    {
        $this->entries = [];
        $this->total = 0;
        $this->pointer = 0;
    }

    private function clearType(int $type): void
    {
        $this->entries = array_filter(
            $this->entries,
            fn($entry) => $entry->getType !== $type
        );
    }
}