<?php
namespace conta\Transition;

class Company
{
    private int $b24Id;

    public function __construct(array $params)
    {
        $this->b24Id = $params['ex_id'];
    }

    public function getB24Id(): int
    {
        return $this->b24Id;
    }
}