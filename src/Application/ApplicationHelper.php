<?php
namespace conta\Application;

use conta\Application\Request\Request;

class ApplicationHelper
{
    private string $config = "./config.ini";

    private Registry $registry;

    public function __construct()
    {
        $this->registry = Registry::instance();
    }

    public function init(): void
    {
        $this->setupConfig();

        $request = Request::makeRequest();
        $this->registry->setRequest($request);
    }

    private function setupConfig(): void
    {
        if (! file_exists($this->config)) {
            throw new \Exception("Could not find options file");
        }
        $options = parse_ini_file($this->config, true);

        $config = new Config($options['config']);
        $this->registry->setConfig($config);

        $commands = new Config($options['commands']);
        $this->registry->setCommands($commands);
    }
}