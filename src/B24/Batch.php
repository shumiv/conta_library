<?php

namespace conta\B24;

use Bitrix24\Bitrix24;

class Batch
{
    private Bitrix24 $b24App;

    public function __construct(Bitrix24 $b24App)
    {
        $this->b24App = $b24App;
    }

    public function execute(
        string $method,
        array $parameters,
        ?callable $callback = null
    ): array
    {
        $data = [];
        if (! $callback) $callback = $this->getRegularCallback();
        $this->batchCalls($method, $parameters, $data, $callback);
        $this->b24App->processBatchCalls();
        return $data;
    }

    /**
     * @param string $method
     * @param array $parameters
     * @param array $data
     * @param callable|null $callback function(array &$data, array $result):void{}
     */
    private function batchCalls(
        string $method,
        array $parameters,
        array &$data,
        callable $callback
    ): void
    {
        $batchFunction
            = $this->getBatchCallFunction($method, $parameters, $data, $callback);
        $this->b24App->addBatchCall(
            $method,
            $parameters,
            $batchFunction);
    }

    private function getRegularCallback(): callable
    {
        return function (array &$data, array $result): void {
            $entries = array_values($result['result']);
            foreach ($entries as $task) {
                $data[] = $task;
            }
        };
    }

    private function getBatchCallFunction(
        string $method,
        array $parameters,
        array &$data,
        callable $callback
    ): callable
    {
        return function ($result) use ($method, $parameters, &$data, $callback) {
            $callback($data, $result);
            if ((!isset($result['next']))
                || (!$result['next'])) return;
            for ($i = $result['next']; $i < $result['total']; $i += $result['next']) {
                $this->b24App->addBatchCall(
                    $method,
                    array_merge($parameters, ['start' => $i]),
                    function ($result) use (&$data, $callback) {
                        $callback($data, $result);
                    });
            }
        };
    }
}