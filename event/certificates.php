<?php
$access = new access; $mf = "certificates";
list($accss, $acc_lvl) = $access->check_user_access($mf);
$alg_u = 0;

if ($accss == "1") {
    require_once (RD . "/lib/brands_class.php");
    $brands = new brands;
    $content = str_replace("{work_window}", $brands->showCertificatesForm(), $content);
    if ($alg_u == 0) { //�� ������ ����� �� ��������� � �������
        $content = str_replace("{work_window}", $access->show_access_deny($mf), $content);
    }
}

if ($accss == "0") {
    $content = str_replace("{work_window}", $access->show_access_deny($mf), $content);
}
