<?php

/**
 * Мастер установки модуля.
 *
 * @author Daniil Sholokhov <sholokhov.daniil@gmail.com>
 */
class task_queue extends CModule
{
    var $MODULE_ID = "task.queue";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;

    public function __construct()
    {
        $arModuleVersion = [];
        include(__DIR__ . '/version.php');

        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }

        $this->PARTNER_NAME = "Daniil Sholokhov";
        $this->PARTNER_URI = 'https://github.com/Sholokhov99';
        $this->MODULE_NAME = 'Управления очередями';
        $this->MODULE_DESCRIPTION = 'Производит отложенный запуск скриптов в рамках очередности.';
    }

    public function DoInstall() {}

    public function DoUninstall() {}

}