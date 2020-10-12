<?php
namespace conta\B24\Domain\Company\Field;

use conta\B24\Domain\Field\PluralField;

class EdoField extends PluralField
{
    const NAME = "UF_CRM_1594888675";

    public function __construct()
    {
        $this->optionsMap = [
            "ФИРМА ЗЕМЛЯ - СЕРВИС ООО" => "4830", //ФЗС
            "ГК ЗС" => "4832", //ГК
            "КП-Сервис" => "4831", //КПС
            "К+Калуга" => "4833", //КПК
            "КОМПАНИЯ" => "4838", //КЗС
        ];
    }
}