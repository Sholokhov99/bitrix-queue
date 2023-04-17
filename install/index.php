<?php

class queue extends CModule
{
    public function __construct()
    {
        $arModuleVersion = [];
        include(__DIR__ . '/version.php');

        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }

        $this->PARTNER_NAME = "X3Group";
        $this->PARTNER_URI = '';
        $this->MODULE_NAME = '';
        $this->MODULE_DESCRIPTION = '';
    }

    public function DoInstall() {}

    public function DoUninstall() {}

}