<?php
namespace conta\B24\Domain\Field;

abstract class Field
{
    protected bool $isChanged = false;

    public function getName(): string
    {
        return static::NAME;
    }

    public function isChanged(): bool
    {
        return $this->isChanged;
    }

    abstract public function getSetting();
}