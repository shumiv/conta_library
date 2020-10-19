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

    /**
     * @param string[] $titles
     * @return CompanyCollection
     */
    public function findCompanies(array $titles): CompanyCollection
    {
        $statement = $this->findCompaniesStatement($titles);
        return $this->extractCompanies($statement);
    }

    public function findFullCompanies(IdCollection $titleIds): CompanyCollection
    {
        $statement = $this->findFullCompaniesStatement($titleIds);
        return $this->extractCompanies($statement);
    }

    /**
     * @param string[] $titles
     * @return \PDOStatement
     */
    private function findCompaniesStatement(array $titles): \PDOStatement
    {
        $wrapedTitles = $this->wrapTitlesQuotes($titles);
        $query = "SELECT company.id, company.name FROM company "
            . "WHERE company.name IN ("
            . implode(", ", $wrapedTitles) . ")";
        return $this->pdo->prepare($query);
    }

    /**
     * @param string[] $titles
     * @return string[]
     */
    private function wrapTitlesQuotes(array $titles): array
    {
        return array_map(
            fn($title) => '"' . trim($title) . '"',
            $titles
        );
    }

    /**
     * @param IdCollection $titleIds
     * @return \PDOStatement
     */
    private function findFullCompaniesStatement(
        IdCollection $titleIds
    ): \PDOStatement
    {
        $query = "SELECT company.id, company.name, sa.luna_entity_id,"
            . " sa.ex_id, sa.sk"
            . " FROM company LEFT JOIN sync_analytic AS sa ON"
            . " company.id = sa.luna_entity_id"
            . " WHERE sa.me_id = 2 AND sa.sk IN ("
           . implode(", ", $titleIds->getAll()) . ")";
        return $this->pdo->prepare($query);
    }

    private function extractCompanies(
        \PDOStatement $statement
    ): CompanyCollection
    {
        $statement->execute();
        $companies = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $collection = new CompanyCollection();
        foreach ($companies as $company) {
            $collection->add(new Company($company));
        }
        return $collection;
    }
}