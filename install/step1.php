<?php

use Bitrix\Main\Localization\Loc;

global $APPLICATION;

Loc::loadMessages(__FILE__);

echo CAdminMessage::ShowMessage(array(
    "TYPE" => "ERROR",
    "MESSAGE" => Loc::getMessage('MESSAGE_TITLE'),
    "DETAILS" => Loc::getMessage('MESSAGE_DESCRIPTION'),
    "HTML" => true,
));

?>

<form action="<?= $APPLICATION->GetCurPage() ?>" name="task_queue_install">
    <?= bitrix_sessid_post(); ?>
    <input type="hidden" name="id" value="task.queue">
    <input type="hidden" name="install" value="Y">
    <input type="hidden" name="step" value="2">

    <input type="submit" name="install" value="<?= Loc::getMessage('INSTALL') ?>">
</form>
