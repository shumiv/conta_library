<?php
namespace conta\B24\Mapper;

use conta\B24\Domain\Domain;
use conta\B24\Domain\User\User;

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

    protected function doCreateObject(array $fields): Domain
    {
        return new User($fields, $this);
    }

    protected function composeParams(array $ids): array
    {
        return ['ID' => $ids];
    }
}