<?php
namespace conta\Transition;

use conta\Collection\Collection;
use conta\Collection\IdCollection;
use conta\Collection\IdMap;

class CompanyCollection extends Collection
{
    public function add(Company $company): void
    {
        array_push($this->collection, $company);
    }

    public function getTitleIds(): IdCollection
    {
        $titleIds = new IdCollection();
        /** @var Company $company */
        foreach ($this->collection as $company) {
            $titleIds->add($company->getTitleId());
        }
        return $titleIds;
    }

    public function getTransitionIds(): IdMap
    {
        $map = new IdMap();
        /** @var Company $company */
        foreach ($this->collection as $company) {
            $b24Id = $company->getTitleId();
            $transitionId = $company->getTransitionId();
            $map->add($b24Id, $transitionId);
        }
        return $map;
    }
}