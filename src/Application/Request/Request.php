<?php
namespace conta\Application\Request;

use Bitrix24\Bitrix24;
use conta\Application\Registry;
use conta\Application\Feedback\Feedback;
use conta\Database\Database;

abstract class Request
{
    const SEPARATOR = "\n";

    const DEFAULT_COMMAND = "Default";

    public Feedback $feedback;

    protected Registry $registry;

    protected array $properties;

    protected array $commands = [self::DEFAULT_COMMAND];

    protected \PDO $lunaPdo;

    public static function makeRequest(): self
    {
        return isset($_SERVER['REQUEST_METHOD'])
            ? new HttpRequest()
            : new CliRequest();
    }

    public function __construct()
    {
        $this->feedback = new Feedback(static::SEPARATOR);
        $this->registry = Registry::instance();
        $this->init();
    }

    abstract public function init(): void;

    public function getLunaPdo(): \PDO
    {
        $this->lunaPdo ??= $this->initializeLunaPdo();
        return $this->lunaPdo;
    }

    public function getB24(): Bitrix24
    {
        define('APP_ID', 'local.5d9db825b129a0.40155270'); // take it from Bitrix24 after adding a new application
        define('APP_SECRET_CODE', 'l4l8beXJl3hXpo4alku5NWXWc9w8j3dfeEqB4u9yhvE0dvF6wR'); // take it from Bitrix24 after adding a new application
        define('APP_REG_URL', 'https://luna.zemser.ru/front/arm/'); // the same URL you should set when adding a new application in Bitrix24
        define('DOMAIN', 'bitrix.zemser.ru');
        define(
            'TOKEN_PARAMETERS_ARRAY' ,
            [
                'domain' => DOMAIN,
                'appId' => APP_ID,
                'appSecretCode' => APP_SECRET_CODE,
                'appRegUrl' => APP_REG_URL
            ]
        );
        $database = new Database($this->getLunaPdo());
        $appData = new \conta\B24RestApi\AppData\AppData();
        return \conta\B24RestApi\Bitrix24Factory\Bitrix24Factory::make($appData, $database, APP_REG_URL);
    }

    public function getCommands(): array
    {
        return $this->commands;
    }

    public function getCommand(int $index = 0): string
    {
        return $this->commands[$index];
    }

    public function getProperties(): array
    {
        return $this->properties;
    }

    public function getProperty(string $key)
    {
        return $this->properties[$key] ?? null;
    }

    public function setProperty(string $key, $value): void
    {
        $this->properties[$key] = $value;
    }

    /**
     * @return string[] errors, warnings and messages
     */
    public function getFeedback(): array
    {
        return array_merge(
            $this->errors,
            $this->warnings,
            $this->messages
        );
    }

    /**
     * @param $separator string
     * @return string errors, warnings and messages
     */
    public function getFeedbackString(?string $separator = null): string
    {
        if (is_null($separator)) {
            $separator = static::SEPARATOR;
        }
        return implode($separator, $this->getFeedback());
    }

    /**
     * @param string $uri
     * @return string[]
     */
    protected function composeCommands(string $uri): array
    {
        $config = $this->registry->getConfig();
        $projectRoot = $config->get('projectRoot');
        $trimmedRoot = str_replace($projectRoot, "", $uri);
        $trimmedGet = $this->trimGet($trimmedRoot);
        return $this->explodeCommands($trimmedGet);
    }

    private function trimGet(string $uri): string
    {
        $separator = '?';
        if (strpos($uri, $separator) !== false) {
            $uri = explode($separator, $uri)[0];
        }
        return $uri;
    }

    private function explodeCommands(string $uri): array
    {
        $separator = '/';
        $commands = [];
        $exploded = explode($separator, $uri);
        foreach ($exploded as $index => $command) {
            if ($command === "" && $index > 0) {
                continue;
            }
            $commands[] = $this->handleCommandName($command);
        }
        return $commands;
    }

    private function handleCommandName(string $command): string
    {
        return $command === ""
            ? self::DEFAULT_COMMAND
            : $this->toPascalCase($command);
    }

    private function toPascalCase(string $snakeCase): string
    {
        $delimiter = "_";
        $pascalCase = "";
        $snakeCase = mb_strtolower($snakeCase);
        $exploded = explode($delimiter, $snakeCase);
        foreach ($exploded as $index => $word) {
            $pascalCase .= ucfirst($word);
        }
        return $pascalCase;
    }

    private function initializeLunaPdo(): \PDO
    {
        require_once "C:/secure/luna_db_data.php";
        try {
            return new \PDO($luna_db_dsn, $luna_db_username, $luna_db_password);
        } catch(\PDOException $e) {
            echo 'Error: ' . $e->getMessage() . '<br>';
        }
    }
}