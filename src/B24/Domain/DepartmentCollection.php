<?php
namespace conta\B24\Domain;

use conta\B24\Domain\Department\Department;
use conta\Collection\IdCollection;

class DepartmentCollection extends DomainCollection
{
    public function getManagersIds(): IdCollection
    {
        $ids = new IdCollection();
        /** @var Department $department */
        foreach ($this->collection as $department) {
            $ids->merge($department->getManagersIds());
        }
        return $ids;
    }
}