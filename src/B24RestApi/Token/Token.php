<?php
namespace conta\B24RestApi\Token;

use conta\Curl\Curl;
use conta\Database\Database;


class Token
{
    private Curl $curl;
    private Database $database;
    const REQUEST_TOKEN = 'https://%1$s/oauth/token/'
        . '?grant_type=authorization_code'
        . '&client_id=%2$s'
        . '&client_secret=%3$s'
        . '&code=%4$s';
    const REFRESH_TOKEN = 'https://%1$s/oauth/token/'
        . '?grant_type=refresh_token'
        . '&client_id=%2$s'
        . '&client_secret=%3$s'
        . '&refresh_token=%4$s';
    const EXTRACT_APP = <<<QUERY
        SELECT * FROM token WHERE app_reg_url = ? LIMIT 1        
        QUERY;
    const UPDATE_APP = <<<QUERY
        UPDATE token
            SET access_token = ?, expires = ?, refresh_token = ?, 
                refresh_token_date = NOW()
        WHERE app_reg_url = ?
        QUERY;
    const INSERT_APP = <<<QUERY
        INSERT INTO token (
               access_token, expires, scope, domain, app_id,
               app_secret_code, app_reg_url, member_id,
               refresh_token, refresh_token_date
            ) values (
                :accessToken, :expires, :scope, :domain, :appId,
                :appSecretCode, :appRegUrl, :memberId,
                :refreshToken, NOW())
        QUERY;

    public function __construct(array $params)
    {
        $_SESSION['domain'] = $params['domain'];
        $_SESSION['appId'] = $params['appId'];
        $_SESSION['appSecretCode'] = $params['appSecretCode'];
        $_SESSION['appRegUrl'] = $params['appRegUrl'];
        $this->curl = new Curl();
    }

    public function set(): void
    {
        if (! isset($_SESSION['accessToken'])) {
            $this->authentication();
        } elseif ($this->isTokenExpired()) {
            $this->refreshToken();
        }
    }

    public function saveToDatabase(Database $database)
    {
        $this->database = $database;
        if ($this->getAppData()) $this->updateAppData();
        else $this->insertAppData();
    }

    /**
     * returns a data set for Conta\B24RestApi\Bitrix24Factory\Bitrix24Factory::makeBitrix24()
     */
    public function getDataSet(): array
    {
        return [
            "scope" => $_SESSION['scope'],
            "app_id" => $_SESSION['appId'],
            "app_secret_code" => $_SESSION['appSecretCode'],
            "domain" => $_SESSION['domain'],
            "member_id" => $_SESSION['memberId'],
            "access_token" => $_SESSION['accessToken']
        ];
    }

    private function getAppData(): array
    {
        $params = [$_SESSION['appRegUrl']];
        return $this->database->execute(self::EXTRACT_APP, $params);
    }

    private function updateAppData(): void
    {
        $params = $this->getUpdateAppParams();
        $this->database->execute(self::UPDATE_APP, $params);
    }

    private function getUpdateAppParams(): array
    {
        return [
            $_SESSION['accessToken'],
            $_SESSION['expires'],
            $_SESSION['refreshToken'],
            $_SESSION['appRegUrl']
        ];
    }

    private function insertAppData(): void
    {
        $data = $this->getAppDataToInsert();
        $this->database->execute(self::INSERT_APP, $data);
    }

    private function getAppDataToInsert():array
    {
        return [
            ':accessToken' => $_SESSION['accessToken'],
            ':expires' => $_SESSION['expires'],
            ':scope' => $_SESSION['scope'],
            ':domain' => $_SESSION['domain'],
            ':appId' => $_SESSION['appId'],
            ':appSecretCode' => $_SESSION['appSecretCode'],
            ':appRegUrl' => $_SESSION['appRegUrl'],
            ':memberId' => $_SESSION['memberId'],
            ':refreshToken' => $_SESSION['refreshToken']
        ];
    }

    private function authentication(): void
    {
        if (isset($_REQUEST['code'])) {
            $this->setTokenDataSet();
        } else {
            $this->requestCode();
        }
    }

    private function setTokenDataSet(): void
    {
        $_SESSION['domain'] = $_REQUEST['domain'] ?? 'empty';
        $_SESSION['code'] = $_REQUEST['code'];
        $_SESSION['scope'] = $_REQUEST['scope'];
        $_SESSION['serverDomain'] = $_REQUEST['server_domain'];
        $_SESSION['memberId'] = $_REQUEST['member_id'];
        $arAccessParams = $this->requestAccessToken();
        $_SESSION['accessToken'] = $arAccessParams['access_token'] ?? null;
        $_SESSION['refreshToken'] = $arAccessParams['refresh_token'] ?? null;
        $_SESSION['expires'] = $arAccessParams['expires'] ?? null;
        $this->redirect($_SESSION['appRegUrl']);
    }

    private function requestAccessToken(): array
    {
        $requestToken = $this->getRequestTokenUrl();
        try {
            $arAccessParams = json_decode(
                $this->curl->request($requestToken),
                true
            );
        } catch (\Throwable $throwable) {
            $_SESSION['error'] = $throwable->getMessage();
        }
        return $arAccessParams ?? [];
    }

    private function getRequestTokenUrl(): string
    {
        return sprintf(
            self::REQUEST_TOKEN,
            $_SESSION['serverDomain'],
            urlencode($_SESSION['appId']),
            urlencode($_SESSION['appSecretCode']),
            urlencode($_SESSION['code'])
        );
    }

    private function requestCode(): void
    {
        $url = 'https://' . $_SESSION['domain'] . '/oauth/authorize/' .
            '?client_id=' . urlencode($_SESSION['appId']);
        $this->redirect($url);
    }

    private function redirect(string $url): void
    {
        Header("HTTP 302 Found");
        Header("Location: " . $url);
        die();
    }

    private function isTokenExpired(): bool
    {
        return time() > ($_SESSION['expires'] - 60 * 10);
    }

    private function refreshToken(): void
    {
        $refreshToken = $this->getRefreshTokenUrl();
        $new = json_decode(
            $this->curl->request($refreshToken),
            true
        );
        $_SESSION['accessToken'] = $new['access_token'];
        $_SESSION['refreshToken'] = $new['refresh_token'];
        $_SESSION['expires'] = $new['expires'];
    }

    private function getRefreshTokenUrl(): string
    {
        return sprintf(
            self::REFRESH_TOKEN,
            $_SESSION['serverDomain'],
            urlencode($_SESSION['appId']),
            urlencode($_SESSION['appSecretCode']),
            urlencode($_SESSION['refreshToken'])
        );
    }
}