<?php
namespace conta\B24\Domain;

trait Person
{
    private function getFullName(): string
    {
        $fullName = "";
        $fullName .= isset($this->name) ? " $this->name " : "";
        $fullName .= isset($this->secondName) ? " $this->secondName" : "";
        $fullName .= isset($this->lastName) ? " $this->lastName " : "";
        return $fullName;
    }
}