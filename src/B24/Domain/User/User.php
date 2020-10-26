<?php
namespace conta\B24\Domain\User;

use conta\B24\Domain\Domain;
use conta\B24\Domain\Person;
use conta\B24\Mapper\Mapper;

class User extends Domain
{
    use Person;

    private string $name;
    private string $secondName;
    private string $lastName;
    private int $departmentId;
    private int $supervisorId;

    public function __construct(array $settings, Mapper $mapper)
    {
        $this->name = $settings['NAME'] ?? "";
        $this->secondName = $settings['SECOND_NAME'] ?? "";
        $this->lastName = $settings['LAST_NAME'] ?? "";
        $this->departmentId = $settings['UF_DEPARTMENT'][0] ?? -1;
        parent::__construct($settings, $mapper);
    }

    /**
     * @inheritDoc
     */
    public function getAssocKey()
    {
        return $this->id;
    }

    public function isNull(): bool
    {
        return ! isset($this->id);
    }

    public function getView(): string
    {
        return $this->getFullName();
    }

    public function pushUpdate(): void
    {
        throw new \Exception('should implement pushUpdate method');
    }

    public function setSupervisorId(int $id): void
    {
        $this->supervisorId = $id;
    }

    public function getSupervisorId(): int
    {
        return $this->supervisorId;
    }

    public function getDepartmentId(): int
    {
        return $this->departmentId;
    }
}