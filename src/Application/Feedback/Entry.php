<?php
namespace conta\Application\Feedback;

class Entry
{
    private string $message;

    private int $type;

    private array $context;

    public function __construct(string $message, int $type, array $context = [])
    {
        $this->message = $message;
        $this->type = $type;
        $this->context = $context;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function getContext(): array
    {
        return $this->context ?? [];
    }
}