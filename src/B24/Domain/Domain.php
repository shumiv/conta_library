<?php
namespace conta\B24\Domain;

use conta\B24\Mapper\Mapper;
use conta\B24\Domain\Field\Field;
use conta\B24\Domain\Field\FieldCollection;

abstract class Domain
{
    protected int $id;

    protected Mapper $mapper;

    public function __construct(array $settings, Mapper $mapper)
    {
        $this->mapper = $mapper;
        $this->id = $settings['ID'] ?? -1;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getFields(): FieldCollection
    {
        $fields = new FieldCollection();
        foreach ($this as $property) {
            if ($property instanceof Field) {
                $fields->add($property);
            }
        }
        return $fields;
    }

    public function create(): void
    {
        if ($this->id !== -1) {
            $message = 'there is the id. The task exists already';
            throw new \Exception($message);
        }
        $this->mapper->create($this);
    }

    /**
     * @return string|int to use as key for an associative array
     */
    abstract public function getAssocKey();

    abstract public function isNull(): bool;

    abstract public function getView(): string;

    abstract public function pushUpdate(): void;
}