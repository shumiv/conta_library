<?php
namespace conta\B24\Mapper;

use conta\B24\Domain\DepartmentCollection;
use conta\B24\Domain\Domain;
use conta\B24\Domain\Department\Department;

class DepartmentMapper extends Mapper
{
    const GET_LIST = 'department.get';

    public function create(Domain $object): void
    {
        throw new \Exception('should create update method');
    }

    public function update(Domain $object): void
    {
        throw new \Exception('should implement update method');
    }

    protected function doCreateObject(array $fields): Domain
    {
        return new Department($fields, $this);
    }

    protected function composeParams(array $ids): array
    {
        return ['ID' => $ids];
    }

    protected function getEmptyCollection(): DepartmentCollection
    {
        return new DepartmentCollection();
    }
}