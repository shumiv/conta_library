<?php
namespace conta\Application\Command;

use conta\Application\Request\Request;

abstract class Command
{
    final public function __construct()
    {
        /* there is nothing here */
    }

    public function execute(Request $request): void
    {
        $this->doExecute($request);
    }

    abstract protected function doExecute(Request $request): void;
}