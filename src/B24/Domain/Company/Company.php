<?php
namespace conta\B24\Domain\Company;

use conta\B24\Domain\Company\Field\EdoField;
use conta\B24\Domain\Domain;
use conta\B24\Mapper\CompanyMapper;

class Company extends Domain
{
    private string $title;
    protected EdoField $edo;

    /** @var int -1 if there is no title id */
    private int $titleId;
    private int $transitionId;

    public function __construct(array $settings, CompanyMapper $mapper)
    {
        $this->title = $settings['TITLE'] ?? "";
        $this->titleId = $this->findTitleId($this->title);
        $this->edo = $this->composeEdoField($settings);
        parent::__construct($settings, $mapper);
    }

    public function getView(): string
    {
        return $this->title;
    }

    public function addEdo(string $option): void
    {
        $this->edo->markOptionSelected($option);
    }

    public function resetEdo(): void
    {
        $this->edo->resetSelectedOptions();
    }

    public function getTitleId(): int
    {
        return $this->titleId;
    }

    public function getTransitionId(): ?int
    {
        return $this->transitionId ?? null;
    }

    public function setTransitionId(int $transitionId): void
    {
        $this->transitionId = $transitionId;
    }

    public function pushUpdate(): void
    {
        $this->mapper->update($this);
    }

    public function isNull(): bool
    {
        return $this->title === "";
    }

    private function findTitleId(string $title): int
    {
        preg_match("/\d+$/", $title, $match);
        return $match[0] ?? -1;
    }

    private function composeEdoField(array $settings): EdoField
    {
        $edoSettings = $settings[EdoField::NAME];
        $edoSettings = $edoSettings ? $edoSettings : [];
        $edo = new EdoField();
        $edo->setSelectedOptions($edoSettings);
        return $edo;
    }
}