<?php
namespace conta\B24RestApi\AppData;

use conta\Database\Database;

class AppData
{
    const EXTRACT_APP_DATA = <<<QUERY
        SELECT scope, app_id, app_secret_code, domain, member_id,
               access_token 
        FROM token WHERE app_reg_url = :appRegUrl LIMIT 1
        QUERY;
    private Database $client;
    private string $appRegUrl;
    private array $appData;

    public function setClient(Database $client): void
    {
        $this->client = $client;
    }

    public function setAppRegUrl(string $appRegUrl): void
    {
        $this->appRegUrl = $appRegUrl;
    }

    public function get(): array
    {
        $this->appData ??= $this->extractAppData();
        return $this->appData;
    }

    public function extractAppData(): array
    {
        $params = [":appRegUrl" => $this->appRegUrl];
        return $this->client->execute(self::EXTRACT_APP_DATA, $params)[0];
    }
}
