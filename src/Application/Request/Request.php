<?php
namespace conta\Application\Request;

use conta\Application\Registry;

abstract class Request
{
    const SEPARATOR = "\n";

    const DEFAULT_COMMAND = "Default";

    public Feedback $feedback;

    protected Registry $registry;

    protected array $properties;

    protected array $commands = [self::DEFAULT_COMMAND];

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
     * @param string $path
     * @return string[]
     */
    protected function composeCommands(string $path): array
    {
        $config = $this->registry->getConfig();
        var_dump($_SERVER['DOCUMENT_ROOT']);
        var_dump(getcwd());
        $projectRoot = $config->get('projectRoot');
        $trimmedRoot = str_replace($projectRoot, "", $path);
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
}