<?php
namespace conta\Transition;

use conta\Collection\IdCollection;
use conta\Transition\Module;

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

    public function findModulesSettings(
        IdCollection $moduleIds
    ): ModuleSettingCollection
    {
        $statement = $this->findModulesSettingsStatement($moduleIds);
        return $this->extractModulesSettings($statement);
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

    private function findModulesSettingsStatement(
        IdCollection $moduleIds
    ): \PDOStatement
    {
        $query = "SELECT * FROM mdk_entity WHERE id IN ("
            . implode(", ", $moduleIds->getAll()) . ")";
        return $this->pdo->prepare($query);
    }

    private function extractModulesSettings(
        \PDOStatement $statement
    ): ModuleSettingCollection
    {
        $statement->execute();
        $settings = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $collection = new ModuleSettingCollection();
        foreach ($settings as $setting) {
            $collection->add(new ModuleSetting($setting));
        }
        return $collection;
    }
}