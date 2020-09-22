<?php
namespace conta\Application\Request;

class CliRequest extends Request
{
    public function init(): void
    {
        $args = $_SERVER['argv'];

        foreach ($args as $arg) {
            if (preg_match("/^commands:(.+)/", $arg, $matches)) {
                $this->commands = $this->composeCommands($matches[1]);
            } else {
                if (strpos($arg, '=')) {
                    [$key, $value] = explode("=", $arg);
                    $this->setProperty($key, $value);
                }
            }
            $this->commands = empty($this->commands) ? "/" : $this->commands;
        }
        print_r($this->commands);
    }
}