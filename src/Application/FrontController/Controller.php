<?php
namespace conta\Application\FrontController;

use conta\Application\Command\CommandResolver;
use conta\Application\Registry;
use conta\Application\Request\Request;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Controller
{
    private Registry $registry;

    private function __construct()
    {
        $this->registry = Registry::instance();
    }

    public static function run(): void
    {
        $instance = new Controller();
        $instance->init();
        $instance->handleRequest();
    }

    private function init(): void
    {
        $this->registry->getApplicationHelper()->init();
    }

    private function handleRequest(): void
    {
        $request = $this->registry->getRequest();
        $resolver = new CommandResolver();
        $command = $resolver->getCommand($request);
        $root = 'C:/www' . $this->registry->getConfig()->get('projectRoot');
        $request->setProjectRoot($root);
        $command->execute($request);
        $this->log($request);
    }

    private function log(Request $request): void
    {
        $log = new Logger('frontController');
        $handler = new StreamHandler('log.log', Logger::WARNING);
        $log->pushHandler($handler);
        $feedback = $request->feedback->getEntries();
        foreach ($feedback as $entry) {
            $log->log(
                $entry->getType(),
                $entry->getMessage(),
                $entry->getContext()
            );
        }
    }
}