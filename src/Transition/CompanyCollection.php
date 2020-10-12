<?php
namespace conta\Transition;

use conta\Collection\Collection;

class CompanyCollection extends Collection
{
    public function add(Company $company): void
    {
        array_push($this->collection, $company);
    }
}