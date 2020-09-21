<?php

namespace conta\Application\Request;

use conta\Application\Registry;

class HttpRequest extends Request
{
    const SEPARATOR = "<br>";

    public function init(): void
    {
        $this->properties = $_REQUEST; //todo: POST/GET/PUT
        $requestUri = $_SERVER['REQUEST_URI'];
        $this->commands = $this->composeCommands($requestUri);
    }
}