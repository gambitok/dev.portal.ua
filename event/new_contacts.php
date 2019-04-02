<?php
require_once RD.'/lib/settings_new_class.php';
$settings_new=new SettingsNewClass;
$content=str_replace("{work_window}", $settings_new->showContactsList(), $content);

