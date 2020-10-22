<?php
namespace conta\B24\Domain\Field;

abstract class PluralField extends Field
{
    protected array $selectedOptions = [];

    protected array $optionsMap;

    public function __construct(array $options = [])
    {
        $this->setSelectedOptions($options);
        $this->isChanged = false;
    }

    public function markOptionSelected(string $option): void
    {
        $option = trim($option);
        $option = $this->optionsMap[$option] ?? $option;
        if (! in_array($option, $this->selectedOptions)) {
            array_push($this->selectedOptions, $option);
            $this->isChanged = true;
        }
    }

    public function markOptionNotSelected(string $option): void
    {
        $option = trim($option);
        $option = $this->optionsMap[$option] ?? $option;
        $optionIndex = array_search($option, $this->selectedOptions);
        if ($optionIndex === false) {
            return;
        }
        array_splice($this->selectedOptions, $optionIndex, 1);
        $this->isChanged = true;
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
        $this->isChanged = true;
    }

    public function getSetting(): array
    {
        return $this->selectedOptions;
    }
}