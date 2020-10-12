<?php
declare(strict_types=1);

namespace conta\Collection;

class IdCollection extends Collection
{
    /**
     * IntegerCollection constructor.
     * @param int[] $integers
     */
    public function __construct(array $integers = [])
    {
        foreach ($integers as $integer) {
            $this->add($integer);
        }
    }

    public function add(int $id): void
    {
        if ($id <= 0) {
            return;
        }
        if (in_array($id, $this->collection)) {
            return;
        }
        array_push($this->collection, $id);
    }
}