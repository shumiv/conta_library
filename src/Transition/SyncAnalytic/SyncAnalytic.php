<?php
namespace conta\Transition\SyncAnalytic;

use Bitrix24\Bitrix24;
use conta\Collection\IdCollection;

class SyncAnalytic
{
    private \PDO $pdo;
    private Bitrix24 $b24;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function setB24(Bitrix24 $b24): void
    {
        $this->b24 = $b24;
    }

    public function updateCrmCompany(IdCollection $titleIds): void
    {
        $crmCompany = new CrmCompany($this->pdo);
        $crmCompany->setB24($this->b24);
        $crmCompany->updateByTitleIds($titleIds);
    }
}