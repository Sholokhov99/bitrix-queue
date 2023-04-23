<?php

use Bitrix\Main\{
    Application,
    Error,
    Result
};
use Bitrix\Main\Localization\Loc;

/**
 * Мастер установки модуля.
 *
 * @author Daniil Sholokhov <sholokhov.daniil@gmail.com>
 */
class task_queue extends CModule
{
    private int $step;
    private array $errors = [];

    /**
     * Минимальная поддерживаемая версия php.
     */
    public const PHP_MIN_VERSION = "7.4.0";

    var $MODULE_ID = "task.queue";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;

    public function __construct()
    {
        global $step;

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

        $this->step = max(intval($step), 0);
    }

    /**
     * Шаги установки модуля.
     *
     * @return bool
     */
    public function DoInstall(): bool
    {
        global $APPLICATION;

        switch ($this->step) {
            case 0:
            case 1:
                $APPLICATION->IncludeAdminFile('', __DIR__ . DIRECTORY_SEPARATOR . "step1.php");
                break;
            case 2:
                $this->installStepTwo();
                break;
        }

        if (!empty($this->errors)) {
            $APPLICATION->ThrowException(implode('<br>', $this->errors));
            return false;
        }

        return true;
    }

    /**
     * Создание необходимых таблиц, для работы модуля.
     *
     * @return Result
     */
    public function InstallDB(): Result
    {
        return $this->runSQL('install.sql');
    }

    /**
     * Шаги удаления модуля.
     *
     * @return bool
     */
    public function DoUninstall(): bool
    {
        global $APPLICATION;

        switch ($this->step) {
            case 0:
            case 1:
                $APPLICATION->IncludeAdminFile('', __DIR__ . DIRECTORY_SEPARATOR . "unstep1.php");
                break;
            case 2:
                $this->unInstallStepTwo();
                break;
        }

        if (!empty($this->errors)) {
            $APPLICATION->ThrowException(implode('<br>', $this->errors));
            return false;
        }

        return true;
    }

    /**
     * Проверка минимальной версии php.
     *
     * @return bool
     *
     * @version 23.01.02
     * @sine 23.01.02
     */
    public function checkPhpVersion(): bool
    {
        return version_compare(PHP_VERSION, static::PHP_MIN_VERSION) !== -1;
    }

    /**
     * Удаление установленных таблиц.
     *
     * @return Result
     */
    public function UnInstallDB(): Result
    {
        return $this->runSQL('uninstall.sql');
    }

    /**
     * Второй шаг установки модуля.
     *
     * @return void
     */
    protected function installStepTwo(): void
    {
        if ($this->checkPhpVersion()) {
            $result = $this->InstallDB();
            if ($result->isSuccess()) {
                RegisterModule($this->MODULE_ID);
            } else {
                $this->errors = array_merge($this->errors, $result->getErrorMessages());
            }
        } else {
            $this->errors[] = Loc::getMessage('MODULE_INSTALL_ERROR_PHP_V', ['#VERSION#' => static::PHP_MIN_VERSION]);
        }
    }

    /**
     * Второй шаг удаления модуля.
     *
     * @return void
     */
    protected function unInstallStepTwo(): void
    {
        $request = Application::getInstance()->getContext()->getRequest();

        if (empty($request->getQuery('savedata')) || $request->getQuery('savedata') <> 'Y') {
            $result = $this->UnInstallDB();

            if (!$result->isSuccess()) {
                $this->errors = array_merge($this->errors, $result->getErrorMessages());
            }
        }

        if (empty($this->errors)) {
            UnRegisterModule($this->MODULE_ID);
        }
    }

    /**
     * Выполнение SQL команды.
     *
     * @param string $fileName
     * @return Result
     */
    protected function runSQL(string $fileName): Result
    {
        global $DB;

        $result = new Result();

        $filePath = $this->getMysqlScriptsPath() . DIRECTORY_SEPARATOR . $fileName;
        $resultSql = $DB->RunSQLBatch($filePath);

        if ($resultSql !== false) {
            $error = new Error(implode('.', $resultSql));
            $result->addError($error);
        }

        return $result;
    }

    /**
     * Получение пути до места хранения SQL скриптов.
     *
     * @return string
     */
    protected function getMysqlScriptsPath(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . "mysql";
    }
}