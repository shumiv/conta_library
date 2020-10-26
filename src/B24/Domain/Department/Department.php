<?php
namespace conta\B24\Domain\Department;

use conta\B24\Domain\Domain;
use conta\B24\Mapper\Mapper;

class Department extends Domain
{
    private int $supervisorId;

    public function __construct(array $settings, Mapper $mapper)
    {
        $this->supervisorId = $settings['UF_HEAD'] ?? -1;
        parent::__construct($settings, $mapper);
    }

    /**
     * @inheritDoc
     */
    public function getAssocKey()
    {
        throw new \Exception('should implement getAssocKey method');
    }

    public function isNull(): bool
    {
        throw new \Exception('should implement isNull method');
    }

    public function getView(): string
    {
        throw new \Exception('should implement getView method');
    }

    public function pushUpdate(): void
    {
        throw new \Exception('should implement pushUpdate method');
    }

    public function getSupervisorId(): int
    {
        return $this->supervisorId;
    }
}