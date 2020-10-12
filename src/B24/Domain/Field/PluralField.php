<?php
namespace conta\B24\Domain\Field;

abstract class PluralField extends Field
{
    protected array $selectedOptions = [];

    protected array $optionsMap;

    public function markOptionSelected(string $option): void
    {
        $option = trim($option);
        $option = $this->optionsMap[$option] ?? $option;
        if (! in_array($option, $this->selectedOptions)) {
            array_push($this->selectedOptions, $option);
            $this->isChanged = true;
        }
    }

    public function setSelectedOptions(array $options): void
    {
        $this->selectedOptions = [];
        foreach ($options as $option) {
            $this->markOptionSelected($option);
        }
    }

    public function resetSelectedOptions(): void
    {
        $this->selectedOptions = [];
    }

    public function getSetting(): array
    {
        return $this->selectedOptions;
    }
}