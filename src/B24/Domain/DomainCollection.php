<?php
namespace conta\B24\Domain;

use conta\Collection\Collection;


class DomainCollection extends Collection
{
    public function add(Domain $domainObject): void
    {
        array_push($this->collection, $domainObject);
    }

    public function pushUpdates(): void
    {
        /** @var Domain $domain */
        foreach ($this->collection as $domain) {
            $domain->pushUpdate();
        }
    }
}