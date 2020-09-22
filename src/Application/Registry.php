<?php
namespace conta\Application;

use conta\Application\Request\Request;

class Registry
{
    private static self $instance;

    private ApplicationHelper $applicationHelper;

    private Request $request;

    private Config $config;

    private Config $commands;

    private function __construct()
    {
        /* there is nothing here */
    }

    public static function instance(): self
    {
        return self::$instance ??= new self();
    }

    public function getApplicationHelper(): ApplicationHelper
    {
        return $this->applicationHelper ??= new ApplicationHelper();
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getConfig(): Config
    {
        return $this->config;
    }

    public function getCommands(): Config
    {
        return $this->commands;
    }

    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }

    public function setConfig(Config $config): void
    {
        $this->config = $config;
    }

    public function setCommands(Config $commands): void
    {
        $this->commands = $commands;
    }
}