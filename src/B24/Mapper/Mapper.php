<?php
namespace conta\B24\Mapper;

use Bitrix24\Bitrix24;
use conta\B24\Batch;
use conta\B24\Domain\Domain;
use conta\B24\Domain\DomainCollection;
use conta\Collection\IdCollection;

abstract class Mapper
{
    protected Batch $batch;

    protected Bitrix24 $b24;

    public function __construct(Bitrix24 $b24)
    {
        $this->b24 = $b24;
        $this->batch = new Batch($b24);
    }

    public function findAllById(IdCollection $ids): DomainCollection
    {
        if (count($ids->getAll()) === 0) {
            return new DomainCollection();
        }
        $params = $this->composeParams($ids->getAll());
        $entityData = $this->getList($params);
        return $this->createObjects($entityData);
    }

    public function createObjects(array $entitiesData): DomainCollection
    {
        return $this->doCreateObjects($entitiesData);
    }

    public function createObject(array $fields): Domain
    {
        return $this->doCreateObject($fields);
    }

    private function getList(array $params): array
    {
        return $this->batch->execute(static::GET_LIST, $params);
    }

    private function doCreateObjects(array $entities): DomainCollection
    {
        $companies = new DomainCollection();
        foreach ($entities as $fields) {
            $companies->add($this->CreateObject($fields));
        }
        return $companies;
    }

    abstract public function update(Domain $object): void;

    abstract protected function doCreateObject(array $fields): Domain;

    abstract protected function composeParams(array $ids): array;
}