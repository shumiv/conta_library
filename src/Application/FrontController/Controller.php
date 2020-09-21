<?php
namespace conta\Application\FrontController;

use conta\Application\Command\CommandResolver;
use conta\Application\Registry;

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
        $command->execute($request);
    }
}