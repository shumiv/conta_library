<?php
namespace conta\Transition;

use conta\Collection\IdCollection;

class TransitionMapper
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findCompanies(IdCollection $titleIds): CompanyCollection
    {
        $query = $this->findCompaniesStatement($titleIds);
        $query->execute();
        $companies = $query->fetchAll();
        $collection = new CompanyCollection();
        foreach ($companies as $company) {
            $collection->add(new Company($company));
        }
        return $collection;
    }

    private function findCompaniesStatement(IdCollection $titleIds)
    {
        $query = "SELECT * FROM sync_analytic WHERE me_id = 2 AND sk IN ("
           . implode(", ", $titleIds->getAll()) . ")";
        return $this->pdo->prepare($query);
    }
}