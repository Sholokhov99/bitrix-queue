<?php

use Bitrix\Main\Localization\Loc;

global $APPLICATION;

IncludeModuleLangFile(__FILE__);
?>

<form action="<?= $APPLICATION->GetCurPage(); ?>">
    <?= bitrix_sessid_post(); ?>
    <input type="hidden" name="lang" value="<?= LANG ?>">
    <input type="hidden" name="id" value="task.queue">
    <input type="hidden" name="uninstall" value="Y">
    <input type="hidden" name="step" value="2">
    <p>
        <input type="checkbox" name="savedata" id="savedata" value="Y" checked>
        <label for="savedata">
            <?= Loc::getMessage('MOD_UNINST_SAVE_TABLES'); ?>
        </label>
    </p>
    <input type="submit" name="inst" value="<?= Loc::getMessage('MOD_UNINST_DEL'); ?>">
</form>
