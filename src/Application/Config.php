<?php
namespace conta\Application;

class Config
{
    private array $content;

    /**
     * Config constructor.
     * @param string[] $content
     */
    public function __construct(array $content)
    {
        $this->content = $content;
    }

    public function get(string $key)
    {
        return $this->content[$key] ?? null;
    }

    public function set(string $key, string $value): void
    {
        $this->content[$key] = $value;
    }
}