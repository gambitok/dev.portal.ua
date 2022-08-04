<?php

class kours {

    function newKoursCard() { $db = DbSingleton::getDb();
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $r = $db->query("SELECT MAX(`id`) as mid FROM `J_KOURS`;");
        $kours_id = 0 + $db->result($r,0,"mid") + 1;
        $db->query("INSERT INTO `J_KOURS` (`id`,`user_id`,`in_use`) VALUES ('$kours_id','$user_id','2');");
        return $kours_id;
    }

    function show_kours_list() { $db = DbSingleton::getDb();
        $list = "";
        $r = $db->query("SELECT k.*, c.name, c.symbol FROM `J_KOURS` k
            LEFT OUTER JOIN `CASH` c on c.id=k.cash_id 
        WHERE k.in_use IN (0,1) ORDER BY k.in_use DESC, k.id ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $name = $db->result($r,$i-1,"name");
            $symbol = $db->result($r,$i-1,"symbol");
            $kours_value = $db->result($r,$i-1,"kours_value");
            $data_from = $db->result($r,$i-1,"data_from");
            $data_to = $db->result($r,$i-1,"data_to");
            if ($data_to == "0000-00-00 00:00:00") {
                $data_to = "поточний курс";
            }
            $in_use = $db->result($r,$i-1,"in_use");
            $inuse = "";
            if ($in_use == 1) {
                $inuse = "діючий";
            }
            $list .= "<tr>
                <td>$inuse</td>
                <td>$name ($symbol)</td>
                <td>$kours_value</td>
                <td>$data_from</td>
                <td>$data_to</td>
            </tr>";
        }
        return $list;
    }

    function showKoursCard($kours_id) { $db = DbSingleton::getDb();
        $form = ""; $form_htm = RD . "/tpl/kours_card.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $r = $db->query("SELECT * FROM `J_KOURS` WHERE `id`='$kours_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 0) {
            $form_htm = RD . "/tpl/access_deny.htm";
            if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        }
        if ($n == 1) {
            $kours_id = $db->result($r,0,"id");
            $form = str_replace("{kours_id}",$kours_id,$form);
            $form = str_replace("{cash_list}",$this->showCashSelectList(1),$form);
        }
        return $form;
    }

    function saveKoursForm($kours_id, $kours_value, $cash_id) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка збереження даних!";
        $kours_id = $slave->qq($kours_id); $kours_value = $slave->qq($kours_value); $cash_id = $slave->qq($cash_id);
        if ($kours_id > 0) {
            $data_from = date("Y-m-d H:i:s");
            $data_to = $data_from;
            if (strpos($kours_value, ',') !== false) {
                $kours_value = floatval(str_replace(',', '.', str_replace('.', '', $kours_value)));
            }
            $db->query("UPDATE `J_KOURS` SET `in_use`='0', `data_to`='$data_to' WHERE `cash_id`='$cash_id' AND `in_use`='1';");
            $db->query("UPDATE `J_KOURS` SET `kours_value`='$kours_value', `cash_id`='$cash_id', `data_from`='$data_from', `in_use`='1' WHERE `id`='$kours_id';");
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

    function showCashSelectList($sel_id) { $db = DbSingleton::getDb();
        $list = "";
        $r = $db->query("SELECT * FROM `CASH` ORDER BY `name`, `id` ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r,$i-1,"id");
            $name = $db->result($r,$i-1,"name")." (".$db->result($r,$i-1,"symbol").")";
            $sel = "";
            if ($id == $sel_id) {
                $sel = " selected";
            }
            $list .= "<option value='$id' $sel>$name</option>";
        }
        return $list;
    }

    function getCashAbr($cash_id) { $db = DbSingleton::getDb();
        $name = "";
        $r = $db->query("SELECT `abr` FROM `CASH` WHERE `id`='$cash_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $name = $db->result($r,0,"abr");
        }
        return $name;
    }

    function getCashName($cash_id) { $db = DbSingleton::getDb();
        $name = "";
        $r = $db->query("SELECT `name` FROM `CASH` WHERE `id`='$cash_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $name = $db->result($r,0,"name");
        }
        return $name;
    }

    function getKoursData() { $db = DbSingleton::getDb();
        $slave = new slave;
        $usd_to_uah = 0; $eur_to_uah = 0;
        $r = $db->query("SELECT `kours_value` FROM `J_KOURS` WHERE `cash_id`='2' AND `in_use`='1' ORDER BY `id` DESC LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $usd_to_uah = $slave->to_money(round($db->result($r,0,"kours_value"),2));
        }
        $r = $db->query("SELECT `kours_value` FROM `J_KOURS` WHERE `cash_id`='3' AND `in_use`='1' ORDER BY `id` DESC LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $eur_to_uah = $slave->to_money(round($db->result($r,0,"kours_value"),2));
        }
        return array($usd_to_uah, $eur_to_uah);
    }

}
