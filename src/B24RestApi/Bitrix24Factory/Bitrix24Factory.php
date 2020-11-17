<?php
namespace conta\B24RestApi\Bitrix24Factory;

use Bitrix24\Bitrix24;
use conta\B24RestApi\AppData\AppData;
use conta\Database\Database;

class Bitrix24Factory
{
    static public function make(
        AppData $appData,
        Database $database,
        string $appRegUrl
    ): Bitrix24
    {
        $appData->setClient($database);
        $appData->setAppRegUrl($appRegUrl);
        $data = $appData->get();
        return self::makeBitrix24($data);
    }

    static public function makeBitrix24(array $data): Bitrix24
    {
        $b24App = new Bitrix24(false, null);
        $b24App
                ->setApplicationScope(explode(',', $data['scope']));
        $b24App->setApplicationId($data['app_id']);
        $b24App->setApplicationSecret($data['app_secret_code']);
        $b24App->setDomain($data['domain']);
        $b24App->setMemberId($data['member_id']);
        $b24App->setAccessToken($data['access_token']);
        return $b24App;
    }
}