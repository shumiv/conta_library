<?php
namespace conta\B24\Mapper;

use conta\B24\Domain\Company\Field\EdoField;
use conta\B24\Domain\Domain;
use conta\B24\Domain\DomainCollection;
use conta\B24\Domain\Field\Field;
use conta\B24\Domain\Company\Company;
use conta\Collection\IdCollection;
use conta\Transition\TransitionMapper;
use conta\Transition\CompanyCollection as TransitionCompanyCollection;

class CompanyMapper extends Mapper
{
    const GET_LIST = "crm.company.list";

    const UPDATE = "crm.company.update";

    private TransitionMapper $transitionMapper;

    public function setTransitionMapperDB(\PDO $pdo): void
    {
        $this->transitionMapper = new TransitionMapper($pdo);
    }

    public function update(Domain $domain): void
    {
        $params = $this->composeUpdateParams($domain);
        $this->b24->call(static::UPDATE, $params);
    }

    public function findAllByTitleId(IdCollection $titleIds): DomainCollection
    {
        $companies = $this->transitionMapper->findCompanies($titleIds);
        $ids = $this->composeIds($companies);
        return $this->findAllById($ids);
    }

    protected function doCreateObject(array $fields): Domain
    {
        return new Company($fields, $this);
    }

    protected function composeParams(array $ids): array
    {
        return [
            "order" => ["asc" => "ID"],
            "filter" => ["ID" => $ids],
            "select" => ["ID", "TITLE", EdoField::NAME/*...$this->getFieldsNames()*/],
        ];
    }

    private function getFieldsNames(): array
    {
        $names = [];
        foreach ($this as $property) {
            if ($property instanceof Field) {
                $names[] = $property->getName();
            }
        }
        return $names;
    }

    private function composeUpdateParams(Domain $company): array
    {
        $params = [];
        $params['id'] = $company->getId();
        $params['fields'] = [];
        foreach ($company->getFields() as $field) {
            if (! $field->isChanged()) {
                continue;
            }
            $params['fields'][$field->getName()] = $field->getSetting();
        }
        return $params;
    }

    private function composeIds(
        TransitionCompanyCollection $companies
    ): IdCollection
    {
        $ids = new IdCollection();
        foreach ($companies as $company) {
            $ids->add($company->getB24Id());
        }
        return $ids;
    }
}