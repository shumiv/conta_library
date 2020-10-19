<?php
namespace conta\Transition\SyncAnalytic;

use Bitrix24\Bitrix24;
use conta\B24\Domain\Company\Company;
use conta\B24\Domain\Domain;
use conta\B24\Domain\DomainCollection;
use conta\B24\Mapper\CompanyMapper;
use conta\Collection\IdCollection;
use conta\Collection\IdMap;
use conta\Transition\CompanyCollection;
use conta\Transition\TransitionMapper;

class CrmCompany
{
    private \PDO $transitionPdo;
    private Bitrix24 $b24;

    public function __construct(\PDO $transitionPdo)
    {
        $this->transitionPdo = $transitionPdo;
    }

    public function setB24(Bitrix24 $b24): void
    {
        $this->b24 = $b24;
    }

    public function updateByTitleIds(IdCollection $titleIds): void
    {
        $companies = $this->extractCompaniesByTitleIds($titleIds);
        $missedTitleIds
            = $this->pickMissedTitleIds($companies, $titleIds);
        $b24Companies = $this->extractB24Companies($missedTitleIds);
        $missedCompanies = $this->extractMissedCompanies($b24Companies);
        $entries = $this->composeSyncAnalyticEntries($b24Companies);
        $this->insertB24IdEntriesToSyncAnalytic($entries);
    }

    private function extractCompaniesByTitleIds(
        IdCollection $titleIds
    ): CompanyCollection
    {
        $mapper = new TransitionMapper($this->transitionPdo);
        return $mapper->findFullCompanies($titleIds);
    }

    private function pickMissedTitleIds(
        CompanyCollection $companies,
        IdCollection $titleIds
    ): IdCollection
    {
        $storedIds = $companies->getTitleIds();
        $missedTitleIds = array_filter(
            $titleIds->getAll(),
            fn($id) => ! $storedIds->contains($id)
        );
        return new IdCollection($missedTitleIds);
    }

    private function extractB24Companies(
        IdCollection $missedTitleIds
    ): DomainCollection
    {
        $mapper = new CompanyMapper($this->b24);
        return $mapper->getListByTitleIds($missedTitleIds);
    }

    private function extractMissedCompanies(
        DomainCollection $b24Companies
    ): DomainCollection
    {
        $titles = $b24Companies->getViews();
        $mapper = new TransitionMapper($this->transitionPdo);
        $companies = $mapper->findCompanies($titles);
        $transitionIds = $companies->getTransitionIds();
        return $this->fillTransitionIds($b24Companies, $transitionIds);
    }

    private function fillTransitionIds(
        DomainCollection $b24Companies,
        IdMap $transitionIds
    ): DomainCollection
    {
        /** @var Company $b24Company */
        foreach ($b24Companies as $b24Company) {
            $titleId = $b24Company->getTitleId();
            $transitionId = $transitionIds->get($titleId);
            if ($transitionId === -1) {
                continue;
            }
            $b24Company->setTransitionId($transitionId);
        }
        return $b24Companies;
    }

    private function composeSyncAnalyticEntries(
        DomainCollection $b24Companies
    ): array
    {
        $entries = [];
        /** @var Company $company */
        foreach ($b24Companies as $company) {
            if (is_null($company->getTransitionId())) {
                continue;
            }
            $entries[] = $this->composeB24SyncAnalyticEntry($company);
        }
        return $entries;
    }

    private function composeB24SyncAnalyticEntry(Company $company): array
    {
        $companyEntity = 2;
        return [
            $company->getTitleId(),
            $company->getTransitionId(),
            $companyEntity,
            $company->getId()
        ];
    }

    private function insertB24IdEntriesToSyncAnalytic(array $entries): void
    {
        $query = "INSERT INTO sync_analytic (sk, luna_entity_id, me_id, ex_id)"
            . " VALUES " . $this->composeValuesString($entries) . ";";
        $statement = $this->transitionPdo->prepare($query);
        $statement->execute();
    }

    private function composeValuesString(array $entries): string
    {
        $set = array_map(
            fn($entry) => $this->composeEntryString($entry),
            $entries
        );
        return implode(', ', $set);
    }

    private function composeEntryString(array $entry): string
    {
        return "(" . implode(', ', $entry) . ")";
    }
}