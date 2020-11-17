<?php
namespace conta\B24\Domain;

use conta\B24\Domain\Department\Department;
use conta\Collection\IdCollection;

class DepartmentCollection extends DomainCollection
{
    public function getSupervisorsIds(): IdCollection
    {
        $ids = new IdCollection();
        /** @var Department $department */
        foreach ($this->collection as $department) {
            $ids->add($department->getSupervisorId());
        }
        return $ids;
    }
}