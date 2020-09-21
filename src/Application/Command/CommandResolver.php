<?php
namespace conta\Application\Command;

use conta\Application\Request\Request;
use conta\Application\Registry;

class CommandResolver
{
    private static \ReflectionClass $commandReflection;

    private static string $defaultCommand = DefaultCommand::class;

    public function __construct()
    {
        $commandClass = Command::class;
        self::$commandReflection = new \ReflectionClass($commandClass);
    }

    public function getCommand(Request $request): Command
    {
        $registry = Registry::instance();
        $commands = $registry->getCommands();
        $config = $registry->getConfig();
        $namespace = $config->get('defaultCommandsNamespace');
        $command = $request->getCommand();

        $class = $commands->get($command);

        if (is_null($class)) {
            $autogeneratedClass = "{$namespace}{$command}Command";
            if (! class_exists($autogeneratedClass)) {
                $request->feedback->addWarning("path '$command' not matched");
                return new self::$defaultCommand();
            }
            return new $autogeneratedClass;
        }

        if (! class_exists($class)) {
            $request->feedback->addWarning("class '$class' not found");
            return new self::$defaultCommand();
        }

        $reflectionClass = new \ReflectionClass($class);

        if (! $reflectionClass->isSubclassOf(self::$commandReflection)) {
            $request->feedback->addWarning(
                "command '$reflectionClass' is not a Command"
            );
            return new self::$defaultCommand();
        }

        return $reflectionClass->newInstance();
    }
}