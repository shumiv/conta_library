<?php

namespace conta\Application\Request;

use conta\Application\Registry;

class HttpRequest extends Request
{
    const SEPARATOR = "<br>";

    public function init(): void
    {
        $this->startSession();
        $this->properties = $_REQUEST; //todo: POST/GET/PUT
        $requestUri = $_SERVER['REQUEST_URI'];
        $this->commands = $this->composeCommands($requestUri);
    }

    private function startSession(): void
    {
        if (session_name() !== $this->appSecretCode)  {
            session_name($this->appSecretCode);
        }
        session_start();
    }
}