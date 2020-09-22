<?php
namespace conta\Application\Command;

use conta\Application\Request\Request;

class DefaultCommand extends Command
{
    protected function doExecute(Request $request): void
    {
        $request->feedback->addNotice('Default command');
        print $request->feedback->getFeedbackString();
    }
}