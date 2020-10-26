<?php
namespace conta\B24\Domain\Task;

use conta\B24\Domain\Domain;
use conta\B24\Mapper\Mapper;
use conta\Utility\StringUtility;

class Task extends Domain
{
    use StringUtility;

    const TITLE = 'TITLE';
    const DESCRIPTION = 'DESCRIPTION';
    const RESPONSIBLE_ID = 'RESPONSIBLE_ID';
    const CREATED_BY = 'CREATED_BY';
    const DEADLINE = 'DEADLINE';
    const GROUP_ID = 'GROUP_ID';
    const UF_CRM_TASK = 'UF_CRM_TASK';

    const TASKS_REVIEW_GROUP = 52;

    const COMPANY_PREFIX = "CO_";

    private string $title;
    private string $description;
    private int $responsibleId;
    private int $createdById;
    private string $deadline;
    private int $groupId;
    private array $crm;

    public function __construct(array $settings, Mapper $mapper)
    {
        $this->mapper = $mapper;

        $this->id
            = $settings[self::snakeToCamel('ID')] ?? -1;
        $this->title
            = $settings[self::snakeToCamel(self::TITLE)];
        $this->description
            = $settings[self::snakeToCamel(self::DESCRIPTION)];
        $this->responsibleId
            = $settings[self::snakeToCamel(self::RESPONSIBLE_ID)];
        $this->createdById
            = $settings[self::snakeToCamel(self::CREATED_BY)];
        $this->deadline
            = $settings[self::snakeToCamel(self::DEADLINE)];
        $this->crm
            = $settings[self::snakeToCamel(self::UF_CRM_TASK)];
        $this->groupId
            = $settings[self::snakeToCamel(self::GROUP_ID)];
    }

    /**
     * @inheritDoc
     */
    public function getAssocKey()
    {
        return $this->title;
    }

    public function isNull(): bool
    {
        // TODO: Implement isNull() method.
    }

    public function getView(): string
    {
        return $this->title;
    }

    public function pushUpdate(): void
    {
        throw new Exeption('Should implement pushUpdate method');
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getResponsibleId(): int
    {
        return $this->responsibleId;
    }

    public function getCreatedById(): int
    {
        return $this->createdById;
    }

    public function getDeadline(): string
    {
        return $this->deadline;
    }

    public function getGroupId(): int
    {
        return $this->groupId;
    }

    public function getCrmSettings(): array
    {
        return $this->crm;
    }
}