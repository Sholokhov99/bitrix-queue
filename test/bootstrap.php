<?php

define("NOT_CHECK_PERMISSIONS", true);
define("NO_AGENT_CHECK", true);
$_SERVER["DOCUMENT_ROOT"] = dirname(__DIR__, 4);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

\Bitrix\Main\Loader::includeModule('tasks.queue');