<?php

class paybox {

    function getUserName($user_id) { $db = DbSingleton::getDb();
        $name = "-";
        $r = $db->query("SELECT `name` FROM `media_users` WHERE `id`='$user_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $name = $db->result($r, 0, "name");
        }
        return $name;
    }

    function show_paybox_list() { $db = DbSingleton::getDb();
        $list = "";
        $r = $db->query("SELECT pb.*, cl.name as client_name, cst.mcaption as doc_type_name 
        FROM `PAY_BOX` pb 
            LEFT JOIN `A_CLIENTS` cl ON cl.id=pb.firm_id
            LEFT JOIN `manual` cst ON cst.id=pb.doc_type_id AND cst.`key`='client_sale_type'
        WHERE pb.status=1 ORDER BY pb.firm_id ASC, pb.id ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r,$i-1,"id");
            $paybox_name = $db->result($r,$i-1,"name");
            $in_use = $db->result($r,$i-1,"in_use");
            $in_use_name = "eye-slash";
            if ($in_use == 1) {
                $in_use_name = "eye";
            }
            $client_name = $db->result($r,$i-1,"client_name");
            $doc_type_name = $db->result($r,$i-1,"doc_type_name");
            $saldo = $this->getPayboxSaldo($id);
            $list .= "<tr style='cursor:pointer' onClick='showPayboxCard(\"$id\",\"$paybox_name\")'>
                <td>$i</td>
                <td align='center'>$doc_type_name</td>
                <td>$paybox_name</td>
                <td align='center'>$client_name</td>
                <td align='center'>$saldo</td>
                <td align='center'><i class='fa fa-$in_use_name'></i></td>
            </tr>";
        }
        return $list;
    }

    function newPayboxCard() { $db = DbSingleton::getDb();
        $r = $db->query("SELECT MAX(`id`) as mid FROM `PAY_BOX`;");
        return 0 + $db->result($r,0,"mid") + 1;
    }

    function showPayboxCard($paybox_id) { $db = DbSingleton::getDb();
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $form = ""; $form_htm = RD . "/tpl/paybox_card.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $r = $db->query("SELECT pb.*, cl.name as firm_name FROM `PAY_BOX` pb 
            LEFT OUTER JOIN `A_CLIENTS` cl on cl.id=pb.firm_id
        WHERE pb.id='$paybox_id' and pb.status=1 LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 0) {
            $r2 = $db->query("SELECT MAX(id) as mid FROM PAY_BOX;");
            $paybox_id = 0 + $db->result($r2,0,"mid") + 1;
            $form = str_replace("{paybox_id}",$paybox_id,$form);
            $form = str_replace("{paybox_name}","",$form);
            $form = str_replace("{paybox_full_name}","",$form);
            $form = str_replace("{firm_id}","",$form);
            $form = str_replace("{firm_name}","",$form);
            $form = str_replace("{doc_type_list}",$this->getDocTypeSelectList(1),$form);
            $form = str_replace("{inuse_checked}","",$form);
            $form = str_replace("{my_user_id}",$user_id,$form);
            $form = str_replace("{my_user_name}",$this->getUserName($user_id),$form);
            $form = str_replace("{oper_visible}"," disabled style=\"display:none;\"",$form);
        }
        if ($n == 1) {
            $paybox_id = $db->result($r,0,"id");
            $name = $db->result($r,0,"name");
            $full_name = $db->result($r,0,"full_name");
            $firm_id = $db->result($r,0,"firm_id");
            $firm_name = $db->result($r,0,"firm_name");
            $doc_type_id = $db->result($r,0,"doc_type_id");
            $in_use = $db->result($r,0,"in_use");
            $inuse_checked = "";
            if ($in_use == 1) {
                $inuse_checked = " checked";
            }
            $form = str_replace("{paybox_id}",$paybox_id,$form);
            $form = str_replace("{paybox_name}",$name,$form);
            $form = str_replace("{paybox_full_name}",$full_name,$form);
            $form = str_replace("{firm_id}",$firm_id,$form);
            $form = str_replace("{firm_name}",$firm_name,$form);
            $form = str_replace("{doc_type_list}",$this->getDocTypeSelectList($doc_type_id),$form);
            $form = str_replace("{inuse_checked}",$inuse_checked,$form);
            $form = str_replace("{my_user_id}",$user_id,$form);
            $form = str_replace("{my_user_name}",$this->getUserName($user_id),$form);
            $saldo = $this->getPayboxSaldo($paybox_id);
            if ($saldo == 0) {
                $form = str_replace("{oper_visible}","",$form);
            } else {
                $form = str_replace("{oper_visible}"," disabled",$form);
            }
        }
        return $form;
    }

    function savePayboxGeneralInfo($paybox_id, $name, $full_name, $firm_id, $doc_type_id, $in_use) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка збереження даних!";
        $paybox_id = $slave->qq($paybox_id); $name = $slave->qq($name); $full_name = $slave->qq($full_name); $firm_id = $slave->qq($firm_id); $doc_type_id = $slave->qq($doc_type_id); $in_use = $slave->qq($in_use);
        if ($paybox_id > 0) {
            $r = $db->query("SELECT * FROM `PAY_BOX` WHERE `id`='$paybox_id';");
            $n = $db->num_rows($r);
            if ($n > 0) {
                $db->query("UPDATE `PAY_BOX` SET `name`='$name', `full_name`='$full_name', `firm_id`='$firm_id', `doc_type_id`='$doc_type_id', `in_use`='$in_use' WHERE `id`='$paybox_id';");
                $answer = 1; $err = "";
            } else {
                $db->query("INSERT INTO `PAY_BOX` (`name`,`full_name`,`firm_id`,`doc_type_id`,`in_use`) VALUES ('$name','$full_name','$firm_id','$doc_type_id','$in_use');");
                $answer = 1; $err = "";
            }
        }
        return array($answer, $err);
    }

    function getDocTypeSelectList($sel_id) { $db = DbSingleton::getDb();
        $list = "<option value=0>Оберіть зі списку</option>";
        $r = $db->query("SELECT `id`, `mcaption` FROM `manual` WHERE `ison`='1' AND `key`='client_sale_type' ORDER BY `mid`, `id` ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r,$i-1,"id");
            $name = $db->result($r,$i-1,"mcaption");
            $sel = "";
            if ($sel_id == $id) {
                $sel = "selected='selected'";
            }
            $list .= "<option value='$id' $sel>$name</option>";
        }
        return $list;
    }

    function showWorkersSelectList($sel_id) { $db = DbSingleton::getDb();
        $list = "";
        $r = $db->query("SELECT * FROM `media_users` ORDER BY `name`, `id` ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r,$i-1,"id");
            $name = $db->result($r,$i-1,"name");
            $sel = "";
            if ($id == $sel_id) {
                $sel = " selected";
            }
            $list .= "<option value='$id' $sel>$name</option>";
        }
        return $list;
    }

    function showPayboxClientList($sel_id) { $db = DbSingleton::getDb();
        $slave = new slave;
        $list = "";
        $form = ""; $form_htm = RD . "/tpl/clients_parrent_tree.htm";
        if (file_exists("$form_htm")){ $form = file_get_contents($form_htm); }
        $r = $db->query("SELECT c.*, ot.name as org_type_name, t2cn.COUNTRY_NAME, t2st.STATE_NAME, t2rg.REGION_NAME, t2ct.CITY_NAME  
        FROM `A_CLIENTS` c 
            LEFT JOIN `A_ORG_TYPE` ot on ot.id=c.org_type 
            LEFT JOIN `T2_COUNTRIES` t2cn on t2cn.COUNTRY_ID=c.country 
            LEFT JOIN `T2_STATE` t2st on t2st.STATE_ID=c.state
            LEFT JOIN `T2_REGION` t2rg on t2rg.REGION_ID=c.region
            LEFT JOIN `T2_CITY` t2ct on t2ct.CITY_ID=c.city
            LEFT JOIN `A_CLIENTS_CATEGORY` cc on cc.client_id=c.id
            LEFT JOIN `A_CATEGORY` ac on ac.id=cc.category_id
        WHERE c.status=1 AND ac.id=3;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r,$i-1,"id");
            $name = $db->result($r,$i-1,"name");
            $org_type_name = $db->result($r,$i-1,"org_type_name");
            $email = $db->result($r,$i-1,"email");
            $phone = $db->result($r,$i-1,"phone");
            $country = $slave->showTableFieldDBT("T2_COUNTRIES","COUNTRY_NAME","COUNTRY_ID",$db->result($r,$i-1,"country"));
            $state = $slave->showTableFieldDBT("T2_STATE","STATE_NAME","STATE_ID",$db->result($r,$i-1,"state"));
            $region = $slave->showTableFieldDBT("T2_REGION","REGION_NAME","REGION_ID",$db->result($r,$i-1,"region"));
            $city = $slave->showTableFieldDBT("T2_CITY","CITY_NAME","CITY_ID",$db->result($r,$i-1,"city"));
            $cur = "";
            $fn = "<i class='fa fa-thumb-tack' onClick='setPayboxClient(\"$id\", \"$name\")'></i>";
            if ($id == $sel_id) {
                $fn = "";
                $cur = "style='background-color:#ccc; disabled:disabled;'";
            }
            $list .= "<tr $cur>
                <td>$fn</td>
                <td>$id</td>
                <td>$org_type_name</td>
                <td>$name</td>
                <td>$country</td>
                <td>$state</td>
                <td>$region</td>
                <td>$city</td>
                <td>$email</td>
                <td>$phone</td>
            </tr>";
        }
        $form = str_replace("{list}",$list,$form);
        return $form;
    }

    function loadPayboxWorkersSaldo($paybox_id) { $db = DbSingleton::getDb();
        $media_users = new media_users;
        $list = "";
        $form = ""; $form_htm = RD . "/tpl/paybox_workers_saldo_list.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $r = $db->query("SELECT SUM(`saldo`) as summ, `cash_id`, `user_id`, `last_update` FROM `B_PAYBOX_BALANS` 
        WHERE `paybox_id`='$paybox_id' GROUP BY `user_id`, `cash_id` ORDER BY `user_id` asc;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $worker_id = $db->result($r,$i-1,"user_id");
            $worker_name = $media_users->getMediaUserName($worker_id);
            $summ = $db->result($r,$i-1,"summ");
            $cash_id = $db->result($r,$i-1,"cash_id");
            $cash_abr = $this->getCashAbr($cash_id);
            $list .= "<tr>
                <td>$i</td>
                <td>$worker_name</td>
                <td>$summ</td>
                <td>$cash_abr</td>
                <td>
                    <button class='btn btn-sm btn-default' onClick='showPayboxWorkerSaldoJournal(\"$paybox_id\", \"$worker_id\", \"$cash_id\");'><i class='fa fa-search'></i></button>
                </td>
            </tr>";
        }
        if ($n == 0) {
            $list = "<tr><td align='center' colspan=5><h3 class='text-center'>Записи відсутні</h3></td></tr>";
        }
        $form = str_replace("{list_saldo}",$list,$form);
        $form = str_replace("{paybox_id}",$paybox_id,$form);
        return $form;
    }

    function showPayboxWorkerSaldoJournal($paybox_id, $user_id, $cash_id) { $db = DbSingleton::getDb();
        $list = "";
        $form = ""; $form_htm = RD . "/tpl/paybox_workers_saldo_journal.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $r = $db->query("SELECT * FROM `B_PAYBOX_JOURNAL` WHERE `paybox_id`='$paybox_id' AND `cash_id`='$cash_id' AND `user_id`='$user_id' ORDER BY `id` DESC LIMIT 0,20;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $saldo_before = $db->result($r,$i-1,"saldo_before");
            $amount = $db->result($r,$i-1,"amount");
            $saldo_after = $db->result($r,$i-1,"saldo_after");
            $cash_id_journal = $db->result($r,$i-1,"cash_id");
            $cash_abr = $this->getCashAbr($cash_id_journal);
            $data = $db->result($r,$i-1,"data");
            $list .= " <tr>
                <td>$i</td>
                <td>$data</td>
                <td>$cash_abr</td>
                <td>$saldo_before</td>
                <td>$amount</td>
                <td>$saldo_after</td>
            </tr>";
        }
        if ($n == 0) {
            $list = "<tr><td align='center' colspan=6><h3 class='text-center'>Записи відсутні</h3></td></tr>";
        }
        $form = str_replace("{list_saldo}",$list,$form);
        $form = str_replace("{paybox_id}",$paybox_id,$form);
        return $form;
    }

    function loadPayboxWorkers($paybox_id) { $db = DbSingleton::getDb();
        $media_users = new media_users;
        $list = "";
        $form = ""; $form_htm = RD . "/tpl/paybox_workers_list.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $r = $db->query("SELECT `id`, `worker_id` FROM `PAY_BOX_WORKERS` WHERE `paybox_id`='$paybox_id' AND `status`='1' ORDER BY `id` ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r,$i-1,"id");
            $worker_id = $db->result($r,$i-1,"worker_id");
            $worker_name = $media_users->getMediaUserName($worker_id);
            $list .= "<tr>
                <td>
                    <button class='btn btn-sm btn-default' onClick='showPayboxWorkerForm(\"$paybox_id\",\"$id\");'><i class='fa fa-edit'></i></button>
                    <button class='btn btn-sm btn-default' onClick='dropPayboxWorker(\"$paybox_id\",\"$id\");'><i class='fa fa-times'></i></button>
                </td>
                <td>$i</td>
                <td>$worker_name</td>
            </tr>";
        }
        if ($n == 0) {
            $list = "<tr><td align='center' colspan=5><h3 class='text-center'>Записи відсутні</h3></td></tr>";
        }
        $form = str_replace("{list_workers}",$list,$form);
        $form = str_replace("{paybox_id}",$paybox_id,$form);
        return $form;
    }

    function showPayboxWorkerForm($paybox_id, $s_id) { $db = DbSingleton::getDb();
        $worker_id = 0;
        $form = ""; $form_htm = RD . "/tpl/paybox_workers_form.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $r = $db->query("SELECT `worker_id` FROM `PAY_BOX_WORKERS` WHERE `id`='$s_id' AND `paybox_id`='$paybox_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $worker_id = $db->result($r,0,"worker_id");
        }
        $form = str_replace("{paybox_id}",$paybox_id,$form);
        $form = str_replace("{s_id}",$s_id,$form);
        $form = str_replace("{workers_list}",$this->showWorkersSelectList($worker_id),$form);
        return $form;
    }

    function savePayboxWorkerForm($paybox_id, $s_id, $worker_id) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка збереження даних!";
        $paybox_id = $slave->qq($paybox_id); $s_id = $slave->qq($s_id); $worker_id = $slave->qq($worker_id);
        if ($paybox_id > 0) {
            if ($s_id == 0) {
                $r = $db->query("SELECT MAX(`id`) as mid FROM `PAY_BOX_WORKERS`;");
                $s_id = 0 + $db->result($r,0,"mid") + 1;
                $db->query("INSERT INTO `PAY_BOX_WORKERS` (`id`,`paybox_id`,`status`) VALUES ('$s_id','$paybox_id','1');");
            }
            if ($s_id > 0) {
                $db->query("UPDATE `PAY_BOX_WORKERS` SET `worker_id`='$worker_id' WHERE `id`='$s_id' AND `paybox_id`='$paybox_id';");
                $answer = 1; $err = "";
            }
        }
        return array($answer, $err);
    }

    function dropPaybox($paybox_id) { $db = DbSingleton::getDb();
        $answer = 0; $err = "Помилка збереження даних!";
        if ($paybox_id > 0) {
            $db->query("UPDATE `PAY_BOX` SET `status`='0' WHERE `id`='$paybox_id';");
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

    function dropPayboxWorker($paybox_id, $s_id) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка збереження даних!";
        $paybox_id = $slave->qq($paybox_id); $s_id = $slave->qq($s_id);
        if ($paybox_id > 0 && $s_id > 0) {
            $db->query("UPDATE `PAY_BOX_WORKERS` SET `status`='0' WHERE `id`='$s_id' AND `paybox_id`='$paybox_id';");
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

    function getPayboxSaldo($paybox_id) { $db = DbSingleton::getDb();
        $saldo = "";
        $r = $db->query("SELECT SUM(`saldo`) as summ, `cash_id` FROM `B_PAYBOX_BALANS` WHERE `paybox_id`='$paybox_id' GROUP BY `cash_id` ORDER BY `cash_id` ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $summ = $db->result($r,$i-1,"summ");
            $cash_id = $db->result($r,$i-1,"cash_id");
            $cash_abr = $this->getCashAbr($cash_id);
            $saldo .= "$summ$cash_abr; ";
        }
        if ($n == 0) {
            $saldo = "0";
        }
        return $saldo;
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

    /* CASH REPORTS */

    function getCashReportsFilters() {
        $form = ""; $form_htm = RD . "/tpl/cash_reports.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $form = str_replace("{date}", date("Y-m-d"), $form);
        $form = str_replace("{paybox_list}", $this->showPayBoxSelect(), $form);
        return $form;
    }

    function showCashListSelect() { $db = DbSingleton::getDb();
        $list = "";
        $r = $db->query("SELECT `id`, `name` FROM `CASH` ORDER BY `name` ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r,$i-1,"id");
            $name = $db->result($r,$i-1,"name");
            $list .= "<option value='$id'>$name</option>";
        }
        return $list;
    }

    function showCashListSelected($sel) { $db = DbSingleton::getDb();
        $list = "<option value='0'>-Не вибрано-</option>";
        $r = $db->query("SELECT `id`, `name` FROM `CASH` ORDER BY `name` ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r,$i-1,"id");
            $name = $db->result($r,$i-1,"name");
            if ($id != $sel) {
                $list .= "<option value='$id'>$name</option>";
            }
        }
        return $list;
    }

    function showPayBoxSelect() { $db = DbSingleton::getDb();
        $list = "";
        $r = $db->query("SELECT `id`, `name` FROM `PAY_BOX` WHERE `status`=1 AND `in_use`=1;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r,$i-1,"id");
            $name = $db->result($r,$i-1,"name");
            $list .= "<option value=$id>$name</option>";
        }
        return $list;
    }

    function getPayBoxName($id) { $db = DbSingleton::getDb();
        $r = $db->query("SELECT `name` FROM `PAY_BOX` WHERE `id`='$id';");
        return $db->result($r, 0, "name");
    }

    function showCashReportsList($date_start, $date_end, $payboxes, $cash_id) { $db = DbSingleton::getDb();
        // КАСА
        $list = ""; $summ_kasa = 0;
        $r = $db->query("SELECT `id`, `paybox_id`, `pay_type_id` FROM `J_PAY` 
		WHERE `paybox_id` in ($payboxes) 
		AND `pay_type_id` in (89,90,91,98)
		AND `data_time`>='$date_start 00:00:00' AND `data_time`<='$date_end 23:59:59'
		AND `cash_id`='$cash_id'
		GROUP BY `paybox_id`;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $paybox_id = $db->result($r,$i-1,"paybox_id");
            $name = $this->getPayBoxName($paybox_id);
            $summ_1 = $this->getSummPayBox($date_start, $date_end, $paybox_id, "89", $cash_id);
            $summ_2 = $this->getSummPayBox($date_start, $date_end, $paybox_id, "90", $cash_id);
            $summ_3 = $this->getSummPayBox($date_start, $date_end, $paybox_id, "98", $cash_id);
            $summ_4 = ($this->getSummPayBox($date_start, $date_end, $paybox_id, "91", $cash_id)) * (-1);
            $summ = $summ_1 + $summ_2 + $summ_3 + $summ_4;
            $summ_prixod = $summ_1 + $summ_2 + $summ_3;
            $summ_vidacha = $summ_4;
            $summ_kasa += $summ;
            $list .= "<tr>
				<td>$name</td>
				<td>$summ_prixod</td>
				<td>$summ_vidacha</td>
				<td>$summ</td>
			</tr>";
        }

        $list .= "<tr style='background:lightgreen;'>
			<td><b>Каси</b></td>
			<td></td>
			<td></td>
			<td>$summ_kasa</td>
		</tr>";

        // RASXODI
        $array_spend = $this->getSpendTypes();
        $summ_vidatki = 0;
        $r = $db->query("SELECT `id`, `paybox_id_from` FROM `J_MONEY_SPEND` 
		WHERE `paybox_id_from` in ($payboxes) 
		AND `data`>='$date_start 00:00:00' AND `data`<='$date_end 23:59:59'
		AND `cash_id`='$cash_id'
		GROUP BY `paybox_id_from`;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $paybox_id = $db->result($r,$i-1,"paybox_id_from");
            $name = $this->getPayBoxName($paybox_id);
            $summ_spend = 0;
            foreach ($array_spend as $spend_type_id) {
                $caption = $this->getSpendTypesCaption($spend_type_id);
                $summ_type = $this->getSummMoneySpendType($date_start, $date_end, $paybox_id, $spend_type_id, $cash_id);
                $summ_spend += $summ_type;
                if ($summ_type > 0)
                    $list .= "<tr>
                        <td>$caption</td>
                        <td>-</td>
                        <td>$summ_type</td>
                        <td>$summ_type</td>
                    </tr>";
            }
            $summ_vidatki += $summ_spend;
            $list .= "<tr>
				<td><b>$name</b></td>
				<td>-</td>
				<td>-</td>
				<td>$summ_spend</td>
			</tr>";
        }
        $list .= "<tr style='background:pink;'>
			<td><b>Видатки</b></td>
			<td></td>
			<td></td>
			<td>$summ_vidatki</td>
		</tr>";

        if ($date_start != null) {
            $date_start = strtotime($date_start);
            $date_convert_start = date('d.m.Y', $date_start);
        } else {
            $date_convert_start = $date_start;
        }

        if ($date_end != null) {
            $date_end = strtotime($date_end);
            $date_convert_end = date('d.m.Y', $date_end);
        } else {
            $date_convert_end = $date_end;
        }

        $form = "";
        if ($cash_id == 1) {
            $form = ""; $form_htm = RD . "/tpl/cash_reports_table.htm";
            if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
            $form = str_replace("{cash_abr}","UAH",$form);
        }
        if ($cash_id == 2) {
            $form = ""; $form_htm = RD . "/tpl/cash_reports_table_usd.htm";
            if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
            $form = str_replace("{cash_abr}","USD",$form);
        }
        if ($cash_id == 3) {
            $form = ""; $form_htm = RD . "/tpl/cash_reports_table_eur.htm";
            if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
            $form = str_replace("{cash_abr}","EUR",$form);
        }

        $form = str_replace("{date_start}",$date_convert_start,$form);
        $form = str_replace("{date_end}",$date_convert_end,$form);
        $form = str_replace("{reports_range}",$list,$form);
        return $form;
    }

    function getSpendTypes() { $db = DbSingleton::getDb();
        $array = [];
        $r = $db->query("SELECT `id` FROM `manual` WHERE `key`='spend_type_id';");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r, $i - 1, "id");
            array_push($array, $id);
        }
        return $array;
    }

    function getSpendTypesCaption($id) { $db = DbSingleton::getDb();
        $r = $db->query("SELECT `mcaption` FROM `manual` WHERE `id`='$id';");
        return $db->result($r, 0, "mcaption");
    }

    function getSummPayBox($date_start, $date_end, $paybox_id, $pay_type_id, $cash_id) { $db = DbSingleton::getDb();
        $r = $db->query("SELECT SUM(`summ`) as pay_summ FROM `J_PAY` 
		WHERE `paybox_id`=$paybox_id 
		AND `pay_type_id`=$pay_type_id
		AND `data_time`>='$date_start 00:00:00' AND `data_time`<='$date_end 23:59:59'
		AND `cash_id`='$cash_id'
		GROUP BY `paybox_id`;");
        return $db->result($r, 0, "pay_summ");
    }

    function getSummMoneySpend($date_start, $date_end, $paybox_id, $cash_id) { $db = DbSingleton::getDb();
        $r = $db->query("SELECT `id`, `paybox_id_from`, SUM(`summ`) as summa FROM `J_MONEY_SPEND` 
		WHERE `paybox_id_from`=$paybox_id
		AND `data`>='$date_start 00:00:00' AND `data`<='$date_end 23:59:59'
		AND `cash_id`='$cash_id'
		GROUP BY `paybox_id_from`;");
        return $db->result($r, 0, "summa");
    }

    function getSummMoneySpendType($date_start, $date_end, $paybox_id, $spend_type_id, $cash_id) { $db = DbSingleton::getDb();
        $r = $db->query("SELECT `id`, `paybox_id_from`, SUM(`summ`) as summa FROM `J_MONEY_SPEND` 
		WHERE `paybox_id_from`=$paybox_id
		AND `data`>='$date_start 00:00:00' AND `data`<='$date_end 23:59:59'
		AND `spend_type_id`='$spend_type_id'
		AND `cash_id`='$cash_id'
		GROUP BY `paybox_id_from`;");
        return $db->result($r, 0, "summa");
    }

    /*
     * paybox convert
     * */
    function showPayboxConvertForm() {
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $form = ""; $form_htm = RD . "/tpl/paybox_convert.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $form = str_replace("{paybox_list}", $this->showPayBoxSelect(), $form);
        $form = str_replace("{cash_list}", $this->showCashListSelect(), $form);
        $form = str_replace("{user_name}", $this->getUserName($user_id), $form);
        $form = str_replace("{paybox_convert_range}", $this->loadPayboxConvertRange(), $form);
        return $form;
    }

    function loadPayboxConvertRange($paybox_id_select = 0, $date_start = 0, $date_end = 0) { $db = DbSingleton::getDb();
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $list = "";
        $where = "";
        if ($paybox_id_select > 0) {
            $where .= " AND `paybox_id`='$paybox_id_select'";
        }
        if ($date_start > 0) {
            $where .= " AND `data`>='$date_start 00:00:00'";

        }
        if ($date_end > 0) {
            $where .= "AND `data`<='$date_end 23:59:59'";
        }
        $r = $db->query("SELECT * FROM `B_PAYBOX_CONVERT` WHERE `user_id`='$user_id' $where;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $paybox_id = $db->result($r, $i - 1, "paybox_id");
            $paybox_name = $this->getPayBoxName($paybox_id);
            $data = $db->result($r, $i - 1, "data");
            $kours_to = $db->result($r, $i - 1, "kours_to");
            $cash_id_from = $db->result($r, $i - 1, "cash_id_from");
            $cash_name_from = $this->getCashAbr($cash_id_from);
            $cash_id_to = $db->result($r, $i - 1, "cash_id_to");
            $cash_name_to= $this->getCashAbr($cash_id_to);
            $price_from = $db->result($r, $i - 1, "price_from");
            $price_to = $db->result($r, $i - 1, "price_to");
            $note = $db->result($r, $i - 1, "note");
            $list .= "<tr>
                <td>$i</td>
                <td>$paybox_name</td>
                <td>$data</td>
                <td>$kours_to</td>
                <td>$price_from $cash_name_from</td>
                <td>$price_to $cash_name_to</td>
                <td>$note</td>
            </tr>";
        }
        return $list;
    }

    function savePayboxConvert($paybox_id, $cash_id_from, $cash_id_to, $kours_to, $price_from, $price_to, $note) { $db = DbSingleton::getDb();
        $answer = 0; $err = "Помилка збереження даних!";
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $minus = (-1) * $price_from;
        $plus = $price_to;
        $kours_from = 0;
        if ($cash_id_to > 0) {
            $kours_from = $this->getKoursInUse($cash_id_to);
        }
        $output = "";

        if ($price_to > 0) {
            $output .= "MINUS: $price_from " . $this->getCashAbr($cash_id_from) . "\n PLUS: $price_to " . $this->getCashAbr($cash_id_to) . "\n PO KURSU $kours_to \n";

            // ===================== B_PAYBOX_CONVERT
            $r = $db->query("SELECT MAX(`id`) as max_id FROM `B_PAYBOX_CONVERT` WHERE 1;");
            $buh_convert_id = $db->result($r, 0, "max_id") + 1;
            $db->query("INSERT INTO `B_PAYBOX_CONVERT` (`id`, `paybox_id`, `user_id`, `cash_id_from`, `cash_id_to`, `kours_from`, `kours_to`, `price_from`, `price_to`, `note`, `status`) VALUES ('$buh_convert_id', '$paybox_id', '$user_id', '$cash_id_from', '$cash_id_to', '$kours_from', '$kours_to', '$price_from', '$price_to', '$note', 1);");

            // ===================== B_PAYBOX_BALANS
            // minus
            $r = $db->query("SELECT * FROM `B_PAYBOX_BALANS` WHERE `paybox_id`='$paybox_id' AND `user_id`='$user_id' AND `cash_id`='$cash_id_from' LIMIT 1;");
            $n = $db->num_rows($r);
            if ($n > 0) {
                $saldo = $db->result($r, 0, "saldo");
                $saldo_minus = $saldo + $minus;
                $output .= "UPDATED: `B_PAYBOX_BALANS` = saldo=$saldo=>$saldo_minus WHERE paybox_id=$paybox_id AND cash_id=$cash_id_from\n";
                $db->query("UPDATE `B_PAYBOX_BALANS` SET `saldo`=`saldo` + $minus WHERE `paybox_id`='$paybox_id' AND `user_id`='$user_id' AND `cash_id`='$cash_id_from' LIMIT 1;");
            } else {
                $output .= "INSERT: `B_PAYBOX_BALANS` = saldo=$minus, paybox_id=$paybox_id, cash_id=$cash_id_from\n";
                $db->query("INSERT INTO `B_PAYBOX_BALANS` (`paybox_id`, `user_id`, `cash_id`, `saldo`) VALUES ('$paybox_id', '$user_id', '$cash_id_from', '$minus');");
            }
            // plus
            $r = $db->query("SELECT * FROM `B_PAYBOX_BALANS` WHERE `paybox_id`='$paybox_id' AND `user_id`='$user_id' AND `cash_id`='$cash_id_to' LIMIT 1;");
            $n = $db->num_rows($r);
            if ($n > 0) {
                $saldo = $db->result($r, 0, "saldo");
                $saldo_plus = $saldo + $plus;
                $output .= "UPDATED: `B_PAYBOX_BALANS` = saldo=$saldo=>$saldo_plus WHERE paybox_id=$paybox_id AND cash_id=$cash_id_to\n";
                $db->query("UPDATE `B_PAYBOX_BALANS` SET `saldo`=`saldo` + $plus WHERE `paybox_id`='$paybox_id' AND `user_id`='$user_id' AND `cash_id`='$cash_id_to' LIMIT 1;");
            } else {
                $output .= "INSERT: `B_PAYBOX_BALANS` = saldo=$plus, paybox_id=$paybox_id, cash_id=$cash_id_to \n";
                $db->query("INSERT INTO `B_PAYBOX_BALANS` (`paybox_id`, `user_id`, `cash_id`, `saldo`) VALUES ('$paybox_id', '$user_id', '$cash_id_to', '$plus');");
            }

            // ===================== B_PAYBOX_JOURNAL
            // minus
            $r = $db->query("SELECT `saldo_after` FROM `B_PAYBOX_JOURNAL` WHERE `paybox_id`='$paybox_id' AND `user_id`='$user_id' AND `cash_id`='$cash_id_from' ORDER BY `data` DESC LIMIT 1;");
            $n = $db->num_rows($r);
            $saldo_before = ($n > 0) ? $db->result($r, 0, "saldo_after") : 0;
            $saldo_after = $saldo_before + $minus;
            $output .= "INSERT: `B_PAYBOX_JOURNAL` = saldo_before=$saldo_before, amount=$minus, saldo_after=$saldo_after, cash_id=$cash_id_from \n";
            $db->query("INSERT INTO `B_PAYBOX_JOURNAL` (`paybox_id`, `user_id`, `saldo_before`, `amount`, `saldo_after`, `cash_id`, `jpay_id`, `buh_income_id`, `buh_convert_id`) VALUES ('$paybox_id', '$user_id', '$saldo_before', '$minus', '$saldo_after', '$cash_id_from', 0, 0, '$buh_convert_id');");
            // plus
            $r = $db->query("SELECT `saldo_after` FROM `B_PAYBOX_JOURNAL` WHERE `paybox_id`='$paybox_id' AND `user_id`='$user_id' AND `cash_id`='$cash_id_to' ORDER BY `data` DESC LIMIT 1;");
            $n = $db->num_rows($r);
            $saldo_before = ($n > 0) ? $db->result($r, 0, "saldo_after") : 0;
            $saldo_after = $saldo_before + $plus;
            $output .= "INSERT: `B_PAYBOX_JOURNAL` = saldo_before=$saldo_before, amount=$plus, saldo_after=$saldo_after, cash_id=$cash_id_to \n";
            $db->query("INSERT INTO `B_PAYBOX_JOURNAL` (`paybox_id`, `user_id`, `saldo_before`, `amount`, `saldo_after`, `cash_id`, `jpay_id`, `buh_income_id`, `buh_convert_id`) VALUES ('$paybox_id', '$user_id', '$saldo_before', '$plus', '$saldo_after', '$cash_id_to', 0, 0, '$buh_convert_id');");
            $answer = 1; $err = "";
        }

        return array($answer, $err, $output);
    }

    function changePayboxConvertSumm($paybox_id, $cash_id_from, $price_from, $cash_id_to, $kours_to) {
        $price_from_max = 0;
        $kours_from = 0;
        $price_to = 0;
        $kours_to_err = 0;
        // get KOURS FROM
        if ($cash_id_to > 0) {
            $kours_from = $this->getKoursInUse($cash_id_to);
        }
        // get KOURS TO ERROR
        if ($kours_from > 0  && $kours_to > 0) {
            $procent = round(abs((($kours_from / $kours_to) - 1) * 100), 2);
            if ($procent > 1) {
                $kours_to_err = 1;
            }
        }
        // get PAYBOX FULL BALANS
        if ($paybox_id > 0 && $cash_id_from > 0) {
            $price_from_max = $this->   getPayboxBalans($paybox_id, $cash_id_from);
        }
        // get SUMM
        if ($paybox_id > 0 && $cash_id_from > 0 && $cash_id_to > 0 && $kours_to > 0) {
            $price_to = $this->getPriceRatingKours($price_from, $cash_id_from, $cash_id_to, $kours_to);
        }
        $kours_from_cap = "";
        if ($kours_from > 0) {
            $kours_1 = $this->getKoursInUse($cash_id_to);
            $kours_2 = $this->getKoursInUse($cash_id_from);
            $kours_cur = round($kours_1 / $kours_2, 2);
            $kours_cur2 = round($kours_2 / $kours_1, 2);
            $kours_from_cap .= " 1 " . $this->getCashAbr($cash_id_to) . " = $kours_cur " . $this->getCashAbr($cash_id_from);
            $kours_from_cap .= "; 1 " . $this->getCashAbr($cash_id_from) . " = $kours_cur2 " . $this->getCashAbr($cash_id_to);
        }
        return array($price_from_max, $kours_from_cap, $price_to, $kours_to_err);
    }

    function getPriceRatingKours($price, $cash_id_from, $cash_id_to, $kours) {
        if ($cash_id_from == $cash_id_to) { $price = $price * 1; }
        if ($cash_id_from == 1 && $cash_id_to == 2) { $price = $price / $kours; }
        if ($cash_id_from == 1 && $cash_id_to == 3) { $price = $price / $kours; }
        if ($cash_id_from == 2 && $cash_id_to == 1) { $price = $price * $kours; }
        if ($cash_id_from == 3 && $cash_id_to == 1) { $price = $price * $kours; }
        if ($cash_id_from == 2 && $cash_id_to == 3) { $price = $price * $kours / $kours; }
        if ($cash_id_from == 3 && $cash_id_to == 2) { $price = $price * $kours / $kours; }
        $price = round($price, 2);
        return $price;
    }

    function getPayboxBalans($paybox_id, $cash_id) { $db = DbSingleton::getDb();
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $r = $db->query("SELECT `saldo` FROM `B_PAYBOX_BALANS` WHERE `paybox_id`='$paybox_id' AND `user_id`='$user_id' AND `cash_id`='$cash_id' LIMIT 1;");
        $n = $db->num_rows($r);
        return ($n > 0) ? $db->result($r, 0, "saldo") : 0;
    }

    function getKoursInUse($cash_id) { $db = DbSingleton::getDb();
        $r = $db->query("SELECT `kours_value` FROM `J_KOURS` WHERE `in_use`=1 AND `cash_id`='$cash_id' LIMIT 1;");
        return round($db->result($r, 0, "kours_value"), 2);
    }

    public function getUserPrroMain($prro_id)
    {
        $server = $login = $pass = $lkey = "";

        $db = DbSingleton::getDb();
        $r = $db->query("SELECT `CB_SERVER` FROM `PRRO_MAIN` WHERE `ID` = $prro_id LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n > 0) {
            $server = $db->result($r, 0, "CB_SERVER");

            $r2 = $db->query("SELECT `CB_LKEY` FROM `PRRO_CASHBOX` WHERE `STATUS` = 1 AND `PRRO_ID` = $prro_id LIMIT 1;");
            $lkey = $db->result($r2, 0, "CB_LKEY");

            $r3 = $db->query("SELECT `CB_LOGIN`, `CB_PASSWORD` FROM `PRRO_CASHIERS` WHERE `STATUS` = 1 AND `PRRO_CB_ID` = $prro_id LIMIT 1;");
            $login  = $db->result($r3, 0, "CB_LOGIN");
            $pass   = $db->result($r3, 0, "CB_PASSWORD");
        }

        return compact("server", "login", "pass", "lkey");
    }

    public function showCheckboxForm()
    {
        $form = ""; $form_htm = RD . "/tpl/checkbox/form.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }

        require(RD . "/checkbox-in-ua-php-sdk/vendor/autoload.php");

        $prro_id = $this->getCbTpoint();
        $dataPrro = $this->getUserPrroMain($prro_id);
        $server = $dataPrro["server"];
        $login  = $dataPrro["login"];
        $pass   = $dataPrro["pass"];
        $lkey   = $dataPrro["lkey"];

        $shifts = $receipts = [];

        if ($prro_id > 0 && $server != "") {
            //        $server = 'https://api.checkbox.in.ua/api/v1';
            //        $login  = 'test_2hww3xtdc';
            //        $pass   = 'test_2hww3xtdc';
            //        $lkey   = 'testa3e8f4fa24b4a2fbdac576b3';

            $config = new \igorbunov\Checkbox\Config([
                \igorbunov\Checkbox\Config::API_URL     => $server,
                \igorbunov\Checkbox\Config::LOGIN       => $login,
                \igorbunov\Checkbox\Config::PASSWORD    => $pass,
                \igorbunov\Checkbox\Config::LICENSE_KEY => $lkey
            ]);

            $api = new \igorbunov\Checkbox\CheckboxJsonApi($config);

            try {
                $api->signInCashier();
            } catch (\igorbunov\Checkbox\Errors\EmptyResponse $e) {
                $answer = 0; $err = "Помилка авторизації";
            }

            if ($api->getCashierShift() === null) {
                try {
                    $api->createShift();
                } catch (Exception $e) {
                    $answer = 0; $err = "Помилка створення зміни";
                }
            }

            $shifts = json_decode(json_encode($api->getShifts(
                new \igorbunov\Checkbox\Models\Shifts\ShiftsQueryParams(
                    [
                        \igorbunov\Checkbox\Models\Shifts\ShiftsQueryParams::STATUS_CLOSED,
                        \igorbunov\Checkbox\Models\Shifts\ShiftsQueryParams::STATUS_OPENED
                    ], // статусы смен
                    false, // desc - сортировка (false or true)
                    100, // limit
                    0 // offset
                )
            )), true);



            $receipts = json_decode(json_encode($api->getReceipts(new \igorbunov\Checkbox\Models\Receipts\ReceiptsQueryParams(
                '', // fiscal code
                '', // serial
                true, // desc - сортировка (false or true)
                100, // limit
                0 // offset
            ))), true);
        }

        $form = str_replace("{cb_shift_range}", $this->getCbShiftList($shifts), $form);
        $form = str_replace("{cb_check_range}", $this->getCbCheckList($prro_id, $receipts), $form);
        $form = str_replace("{cb_prro_main_range}", $this->getCbMainList(), $form);

        $form = str_replace("{cb_prro_cashbox_range}", $this->getCbCheckBoxList(), $form);
        $form = str_replace("{cb_prro_cashier_range}", $this->getCbCashierList(), $form);
        $form = str_replace("{cb_prro_invoice_range}", $this->getCbSaleInvoiceList(), $form);

        $form = str_replace("{cb_register_range}", $this->getCbRegisterList(), $form);

        return $form;
    }

    public function saveCbTpoint($prro_id)
    {
        $db = DbSingleton::getDb();
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $db->query("UPDATE `media_users` SET `prro_status` = $prro_id WHERE `id` = $user_id LIMIT 1;");
        return true;
    }

    public function getCbTpoint()
    {
        $db = DbSingleton::getDb();
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $prro_status = 0;
        $r = $db->query("SELECT `prro_status` FROM `media_users` WHERE `id` = $user_id LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n > 0) {
            $prro_status = $db->result($r, 0, "prro_status");
        }
        return $prro_status;
    }

    public function getCbRegisterList(): string
    {
        $medias_user_id = $_SESSION["media_user_id"];
        $db = DbSingleton::getDb();

        $where = "pc.USER_ID = $medias_user_id";
        if ($medias_user_id == 2 || $medias_user_id == 7) {
            $where = "1";
        }

        $r = $db->query("SELECT pm.* 
        FROM `PRRO_MAIN` pm
            LEFT JOIN `PRRO_CASHIERS` pc ON pc.PRRO_CB_ID = pm.ID
        WHERE $where;");
        $n = $db->num_rows($r);
        $list = "<table class='table table-bordered'>";
        for ($i = 1; $i <= $n; $i++) {
            $prro_id    = $db->result($r, $i - 1, "ID");
            $name       = $db->result($r, $i - 1, "CB_NAME");

            $checked = "";
            if ($prro_id == $this->getCbTpoint()) {
                $checked = "checked='checked'";
            }

            $list .= "<tr>
            <td><input type='radio' name='cash_registers' value='$prro_id' id='$i' $checked>
            <label for='$i'>$name</label></td>
            <td><button onclick='createXReport($prro_id);'>X-звіт</button></td>
            <td><button onclick='openServiceReceipt($prro_id, 1);'>Внесення</button></td>
            <td><button onclick='openServiceReceipt($prro_id, 2);'>Винесення</button></td>
            </tr>";
        }
        $list .= "</table>";

        return $list;
    }

    // doctype 1 - внесення, 2 - винесення
    public function createServiceReceipt($prro_id, $sum = 0, $doc_type_id = 1, $payment_type_id = 1)
    {
        $answer = 0; $err = "";

        $dataPrro = $this->getUserPrroMain($prro_id);

        $server = $dataPrro["server"];
        $login  = $dataPrro["login"];
        $pass   = $dataPrro["pass"];
        $lkey   = $dataPrro["lkey"];

        if ($prro_id > 0 && $server != "") {

            $config = new \igorbunov\Checkbox\Config([
                \igorbunov\Checkbox\Config::API_URL     => $server,
                \igorbunov\Checkbox\Config::LOGIN       => $login,
                \igorbunov\Checkbox\Config::PASSWORD    => $pass,
                \igorbunov\Checkbox\Config::LICENSE_KEY => $lkey
            ]);

            $api = new \igorbunov\Checkbox\CheckboxJsonApi($config);

            $api->signInCashier();

            if (empty($api->getCashierShift())) {
                $api->createShift();
            }

            if ($doc_type_id == 2) {
                $sum = $sum * (-1);
            }

            $sum = $sum * 100;

            try {
                if ($payment_type_id == 1) {
                    $api->createServiceReceipt(
                        new \igorbunov\Checkbox\Models\Receipts\ServiceReceipt(
                            new \igorbunov\Checkbox\Models\Receipts\Payments\CashPaymentPayload($sum)
                        )
                    );
                } else {
                    $api->createServiceReceipt(
                        new \igorbunov\Checkbox\Models\Receipts\ServiceReceipt(
                            new \igorbunov\Checkbox\Models\Receipts\Payments\CardPaymentPayload($sum)
                        )
                    );
                }
                $answer = 1; $err = "";
            } catch (\igorbunov\Checkbox\Errors\NoActiveShift $err) {
                $answer = 0; $err = "Для проведення поточного фіскального чеку на повернення в касі не вистачає коштів. Зробіть службове внесення коштів, або наторгуйте";
            }

        }

        return array($answer, $err);
    }

    // 1 or 2
    public function createXReport($prro_id): array
    {
        $answer = 0; $err = "";
        $dataPrro = $this->getUserPrroMain($prro_id);

        $server = $dataPrro["server"];
        $login  = $dataPrro["login"];
        $pass   = $dataPrro["pass"];
        $lkey   = $dataPrro["lkey"];

        if ($prro_id > 0 && $server !== "") {

            $config = new \igorbunov\Checkbox\Config([
                \igorbunov\Checkbox\Config::API_URL     => $server,
                \igorbunov\Checkbox\Config::LOGIN       => $login,
                \igorbunov\Checkbox\Config::PASSWORD    => $pass,
                \igorbunov\Checkbox\Config::LICENSE_KEY => $lkey
            ]);

            $api = new \igorbunov\Checkbox\CheckboxJsonApi($config);

            try {
                $api->signInCashier();
            } catch (\igorbunov\Checkbox\Errors\EmptyResponse $e) {
                $answer = 0; $err = "Помилка авторизації";
            }

            if ($api->getCashierShift() === null) {
                try {
                    $api->createShift();
                } catch (Exception $e) {
                    $answer = 0; $err = "Помилка створення зміни";
                }
            }

            try {
                $arr = $api->createXReport();
                $arr = json_decode(json_encode($arr), true);
                $x_id = $arr["id"];
                $text = $api->getReportText($x_id);

                $text = iconv("UTF-8", "windows-1251", $text);

                $answer = 1; $err = $text;
            } catch (\igorbunov\Checkbox\Errors\NoActiveShift $err) {
                $answer = 0; $err = "Помилка NoActiveShift";
            } catch (Exception $e) {
                $answer = 0; $err = "Помилка $e";
            }

        }

        return array($answer, $err);
    }

    public function showCheckReport($prro_id, $check_id)
    {
        $answer = 0; $err = "";

        $dataPrro = $this->getUserPrroMain($prro_id);

        $server = $dataPrro["server"];
        $login  = $dataPrro["login"];
        $pass   = $dataPrro["pass"];
        $lkey   = $dataPrro["lkey"];

        if ($prro_id > 0 && $server != "") {

            $config = new \igorbunov\Checkbox\Config([
                \igorbunov\Checkbox\Config::API_URL     => $server,
                \igorbunov\Checkbox\Config::LOGIN       => $login,
                \igorbunov\Checkbox\Config::PASSWORD    => $pass,
                \igorbunov\Checkbox\Config::LICENSE_KEY => $lkey
            ]);

            $api = new \igorbunov\Checkbox\CheckboxJsonApi($config);

            try {
                $api->signInCashier();
            } catch (\igorbunov\Checkbox\Errors\EmptyResponse $e) {
                $answer = 0; $err = "Помилка авторизації";
            }

            if ($api->getCashierShift() === null) {
                try {
                    $api->createShift();
                } catch (Exception $e) {
                    $answer = 0; $err = "Помилка створення зміни";
                }
            }

            try {
                $text = $api->getReceiptText($check_id);

                $text = iconv("UTF-8", "windows-1251", $text);

                $answer = 1; $err = $text;
            } catch (\igorbunov\Checkbox\Errors\NoActiveShift $err) {
                $answer = 0; $err = "Помилка";
            }

        }
        return array($answer, $err);
    }

    public function getCbMainList()
    {
        $dp = new dp();
        $db = DbSingleton::getDb();
        $list = "";

        $r = $db->query("SELECT * FROM `PRRO_MAIN` WHERE `STATUS` = 1;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id         = $db->result($r, $i - 1, "ID");
            $client_id  = $db->result($r, $i - 1, "CLIENT_ID");
            $client_nm  = $dp->getClientName($client_id);
            $tp_id      = $db->result($r, $i - 1, "TPOINT_ID");
            $tp_name    = $dp->getTpointName($tp_id);
            $name       = $db->result($r, $i - 1, "CB_NAME");
            $server     = $db->result($r, $i - 1, "CB_SERVER");
            $login      = $db->result($r, $i - 1, "CB_LOGIN");
            $password   = $db->result($r, $i - 1, "CB_PASSWORD");

            $list .= "
            <tr>
                <td>$id</td>
                <td>$client_nm</td>
                <td>$tp_name</td>
                <td>$name</td>
                <td>$server</td>
                <td>$login</td>
                <td>$password</td>
            </tr>";
        }
        return $list;
    }

    public function getCbCheckBoxList()
    {
        $db = DbSingleton::getDb();
        $list = "";
        $r = $db->query("SELECT * FROM `PRRO_CASHBOX` WHERE `STATUS` = 1;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id         = $db->result($r, $i - 1, "ID");
            $prro_id    = $db->result($r, $i - 1, "PRRO_ID");
            $name       = $db->result($r, $i - 1, "CB_NAME");
            $number     = $db->result($r, $i - 1, "CB_NUMBER");
            $fnumber    = $db->result($r, $i - 1, "CB_FNUMBER");
            $lkey       = $db->result($r, $i - 1, "CB_LKEY");
            $total      = $db->result($r, $i - 1, "TODAY_AMOUNT_UAH");
            $list .= "
            <tr>
                <td>$id</td>
                <td>$prro_id</td>
                <td>$name</td>
                <td>$number</td>
                <td>$fnumber</td>
                <td>$lkey</td>
                <td>$total</td>
            </tr>";
        }
        return $list;
    }

    public function getCbCashierList()
    {
        $db = DbSingleton::getDb();
        $list = "";
        $r = $db->query("SELECT * FROM `PRRO_CASHIERS` WHERE `STATUS` = 1;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id         = $db->result($r, $i - 1, "ID");
            $prro_cb_id = $db->result($r, $i - 1, "PRRO_CB_ID");
            $user_id    = $db->result($r, $i - 1, "USER_ID");
            $user_name  = $this->getUserName($user_id);
            $fname      = $db->result($r, $i - 1, "CB_FNAME");
            $id_key     = $db->result($r, $i - 1, "CB_ID_KEY");
            $pincode    = $db->result($r, $i - 1, "CB_PINCODE");
            $login      = $db->result($r, $i - 1, "CB_LOGIN");
            $password   = $db->result($r, $i - 1, "CB_PASSWORD");
            $list .= "
            <tr>
                <td>$id</td>
                <td>$prro_cb_id</td>
                <td>$user_name</td>
                <td>$fname</td>
                <td>$id_key</td>
                <td>$pincode</td>
                <td>$login</td>
                <td>$password</td>
            </tr>";
        }
        return $list;
    }

    public function getCbSaleInvoiceList()
    {
        $sale_invoice = new sale_invoice();
        $db = DbSingleton::getDb();
        $list = "";
        $r = $db->query("SELECT * FROM `PRRO_SALE_INVOICE` WHERE `STATUS` = 1;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id         = $db->result($r, $i - 1, "ID");
            $check_id   = $db->result($r, $i - 1, "CHECK_ID");
            $invoice_id = $db->result($r, $i - 1, "INVOICE_ID");
            $invoice_nm = $sale_invoice->getSaleInvoiceName($invoice_id);
            $type       = $db->result($r, $i - 1, "TYPE");
            $payment    = $db->result($r, $i - 1, "PAYMENT");
            $fnumber    = $db->result($r, $i - 1, "FNUMBER");
            $number     = $db->result($r, $i - 1, "NUMBER");
            $data       = $db->result($r, $i - 1, "DATA");
            $total      = $db->result($r, $i - 1, "TOTAL");

            $list .= "
            <tr>
                <td>$id</td>
                <td>$check_id</td>
                <td>$invoice_nm</td>
                <td>$type</td>
                <td>$payment</td>
                <td>$fnumber</td>
                <td>$number</td>
                <td>$data</td>
                <td>$total</td>
            </tr>";
        }
        return $list;
    }

    public function getCbCheckList($prro_id, $arr)
    {
        $list = "";

        foreach ($arr["results"] as $val) {
            $payments = [];
            foreach ($val["payments"] as $items) {
                foreach ($items as $item) {
                    $payments[] = iconv("UTF-8", "windows-1251", $item["label"]);
                }
            }
            $payments = implode(" / ", $payments);
            $check_id = $val['id'];
            $list .= "<tr onclick='showCheckReport(\"$prro_id\", \"$check_id\");'>
                <td>" . $check_id . "</td>
                <td>" . 'Продаж' . "</td>
                <td>" . $payments . "</td>
                <td>" . date('Y-m-d H:i:s', strtotime($val['created_at'])) . "</td>
                <td>" . $val['fiscal_code'] . "</td>
                <td>" . $val['shift']['serial'] . "</td>
                <td>" . ($val['total_sum'] / 100) . "</td>
            </tr>";
        }

        return $list;
    }

    public function getCbShiftList($arr)
    {
        $list = "";
        foreach ($arr["results"] as $val) {
            $list .= "<tr>
                <td>" . $val['id'] . "</td>
                <td>" . $val['status'] . "</td>
                <td>" . '-'. "</td>
                <td>" . '-'. "</td>
                <td>" . '-'. "</td>
                <td>" . $val['serial']. "</td>
                <td>" . $val['cash_register']['fiscal_number'] . "</td>
                <td>" . date('Y-m-d H:i:s', strtotime($val['created_at'])) . "</td>
                <td>" . date('Y-m-d H:i:s', strtotime($val['updated_at'])) . "</td>
            </tr>";
        }
        return $list;
    }

}
