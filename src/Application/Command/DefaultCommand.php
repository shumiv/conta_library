<?php
namespace conta\Application\Command;

use conta\Application\Request\Request;

class DefaultCommand extends Command
{
    protected function doExecute(Request $request): void
    {
        $request->addMessage('Default command');
        print $request->getFeedbackString();
    }
}