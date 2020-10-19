<?php
namespace conta\Transition;

class Company
{
    private string $title;
    private int $transitionId;
    private int $b24Id;

    public function __construct(array $params)
    {
        $this->title = trim($params['name']);
        $this->transitionId = $params['id'];
        $this->b24Id = $params['ex_id'] ?? -1;
    }

    /**
     * @return int -1 if there is no b24 id
     */
    public function getB24Id(): int
    {
        return $this->b24Id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getTitleId(): int
    {
        $integersInTheEnd = '/\d+$/';
        preg_match($integersInTheEnd, $this->title, $matches);
        return $matches[0];
    }

    public function getTransitionId(): int
    {
        return $this->transitionId;
    }
}