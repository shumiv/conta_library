<?php
namespace conta\B24\Mapper;

use conta\B24\Domain\Domain;
use conta\B24\Domain\DomainCollection;
use conta\B24\Domain\User\User;
use conta\Collection\IdCollection;

class UserMapper extends Mapper
{
    const GET_LIST = "user.get";

    public function create(Domain $object): void
    {
        throw new \Exception('should implement create method');
    }

    public function update(Domain $object): void
    {
        throw new \Exception('should implement update method');
    }

    public function findAllByDepartments(
        IdCollection $departmentIds
    ): DomainCollection
    {
        if ($departmentIds->isEmpty()) {
            return $this->getEmptyCollection();
        }
        $params = $this->composeByDepartmentsParams($departmentIds->getAll());
        $entityData = $this->getList($params);
        return $this->createObjects($entityData);
    }

    protected function doCreateObject(array $fields): Domain
    {
        return new User($fields, $this);
    }

    protected function composeParams(array $ids): array
    {
        return ['ID' => $ids];
    }

    /**
     * @param int[] $ids
     * @return array
     */
    protected function composeByDepartmentsParams(array $ids): array
    {
        return [
            'UF_DEPARTMENT' => $ids,
            'ACTIVE' => true,
        ];
    }
}