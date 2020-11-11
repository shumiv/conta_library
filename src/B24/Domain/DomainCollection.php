<?php
namespace conta\B24\Domain;

use Bitrix24\Departments\Department;
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

    public function getViews(): array
    {
        return array_map(
            fn($domain) => $domain->getView(),
            $this->collection
        );
    }

    /**
     * @return Domain[]
     */
    public function getAsAssoc(): array
    {
        $assoc = [];
        /** @var Domain $domain */
        foreach ($this->collection as $domain) {
            if ($domain->isNull()) {
                continue;
            }
            $assoc[$domain->getAssocKey()] = $domain;
        }
        return $assoc;
    }

    public function getViewParams(): array
    {
        $params = [];
        /** @var Domain $domain */
        foreach ($this->collection as $domain) {
            if ($domain->isNull()) {
                continue;
            }
            $assoc[] = $domain->getViewParams();
        }
        return $assoc;
    }
}