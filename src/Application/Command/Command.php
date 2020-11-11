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
        try {
            $this->doExecute($request);
        } catch (\Throwable $throwable) {
            $request->feedback->addError($throwable);
        }
    }

    abstract protected function doExecute(Request $request): void;
}