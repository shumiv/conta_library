<?php
namespace conta\B24\Domain\Field;

use conta\Collection\Collection;

class FieldCollection extends Collection
{
    public function add(Field $field): void
    {
        array_push($this->collection, $field);
    }

    public function current(): Field
    {
        return parent::current();
    }
}