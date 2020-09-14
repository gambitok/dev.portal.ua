<?php

class paybox {

    function show_paybox_list(){$db=DbSingleton::getDb();
        $r=$db->query("SELECT pb.*, cl.name as client_name, cst.mcaption as doc_type_name 
        FROM `PAY_BOX` pb 
            LEFT OUTER JOIN `A_CLIENTS` cl on cl.id=pb.firm_id
            LEFT OUTER JOIN `manual` cst on cst.id=pb.doc_type_id and cst.`key`='client_sale_type'
        WHERE pb.status=1 ORDER BY pb.firm_id asc, pb.id asc;");$n=$db->num_rows($r);$list="";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $paybox_name=$paybox_name=$db->result($r,$i-1,"name");
            $in_use=$db->result($r,$i-1,"in_use"); $in_use_name="eye-slash"; if ($in_use==1){$in_use_name="eye";}
            $client_name=$db->result($r,$i-1,"client_name");
            $doc_type_name=$db->result($r,$i-1,"doc_type_name");
            $saldo=$this->getPayboxSaldo($id);
            $list.="<tr style='cursor:pointer' onClick='showPayboxCard(\"$id\",\"$paybox_name\")'>
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

    function newPayboxCard(){$db=DbSingleton::getDb();
        $r=$db->query("SELECT MAX(`id`) as mid FROM `PAY_BOX`;");$paybox_id=0+$db->result($r,0,"mid")+1;
        return $paybox_id;
    }

    function showPayboxCard($paybox_id){$db=DbSingleton::getDb();
        session_start();$user_id=$_SESSION["media_user_id"];$user_name=$_SESSION["user_name"];
        $form="";$form_htm=RD."/tpl/paybox_card.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("SELECT pb.*, cl.name as firm_name 
        FROM `PAY_BOX` pb 
            LEFT OUTER JOIN `A_CLIENTS` cl on cl.id=pb.firm_id
        WHERE pb.id='$paybox_id' and pb.status=1 LIMIT 1;");$n=$db->num_rows($r);
        if ($n==0){
            $r2=$db->query("SELECT MAX(id) as mid FROM PAY_BOX;"); $paybox_id=0+$db->result($r2,0,"mid")+1;
            $form=str_replace("{paybox_id}",$paybox_id,$form);
            $form=str_replace("{paybox_name}","",$form);
            $form=str_replace("{paybox_full_name}","",$form);
            $form=str_replace("{firm_id}","",$form);
            $form=str_replace("{firm_name}","",$form);
            $form=str_replace("{doc_type_list}",$this->getDocTypeSelectList(1),$form);
            $form=str_replace("{inuse_checked}","",$form);
            $form=str_replace("{my_user_id}",$user_id,$form);
            $form=str_replace("{my_user_name}",$user_name,$form);
            $form=str_replace("{oper_visible}"," disabled style=\"display:none;\"",$form);
        }
        if ($n==1){
            $paybox_id=$db->result($r,0,"id");
            $name=$db->result($r,0,"name");
            $full_name=$db->result($r,0,"full_name");
            $firm_id=$db->result($r,0,"firm_id");
            $firm_name=$db->result($r,0,"firm_name");
            $doc_type_id=$db->result($r,0,"doc_type_id");
            $in_use=$db->result($r,0,"in_use"); $inuse_checked=""; if ($in_use==1){$inuse_checked=" checked"; }
            $form=str_replace("{paybox_id}",$paybox_id,$form);
            $form=str_replace("{paybox_name}",$name,$form);
            $form=str_replace("{paybox_full_name}",$full_name,$form);
            $form=str_replace("{firm_id}",$firm_id,$form);
            $form=str_replace("{firm_name}",$firm_name,$form);
            $form=str_replace("{doc_type_list}",$this->getDocTypeSelectList($doc_type_id),$form);
            $form=str_replace("{inuse_checked}",$inuse_checked,$form);
            $form=str_replace("{my_user_id}",$user_id,$form);
            $form=str_replace("{my_user_name}",$user_name,$form);
            $saldo=$this->getPayboxSaldo($paybox_id);
            if ($saldo==0)
            $form=str_replace("{oper_visible}","",$form); else
            $form=str_replace("{oper_visible}"," disabled",$form);
        }
        return $form;
    }

    function savePayboxGeneralInfo($paybox_id,$name,$full_name,$firm_id,$doc_type_id,$in_use){$db=DbSingleton::getDb();
        $slave=new slave;$answer=0;$err="Помилка збереження даних!";
        $paybox_id=$slave->qq($paybox_id);$name=$slave->qq($name);$full_name=$slave->qq($full_name);$firm_id=$slave->qq($firm_id);$doc_type_id=$slave->qq($doc_type_id);$in_use=$slave->qq($in_use);
        if ($paybox_id>0){
            $r=$db->query("SELECT * FROM `PAY_BOX` WHERE `id`='$paybox_id';"); $n=$db->num_rows($r);
            if ($n>0) {
                $db->query("UPDATE `PAY_BOX` SET `name`='$name', `full_name`='$full_name', `firm_id`='$firm_id', `doc_type_id`='$doc_type_id', `in_use`='$in_use' WHERE `id`='$paybox_id';");
                $answer=1;$err="";
            } else {
                $db->query("INSERT INTO `PAY_BOX` (`name`,`full_name`,`firm_id`,`doc_type_id`,`in_use`) VALUES ('$name','$full_name','$firm_id','$doc_type_id','$in_use');");
                $answer=1;$err="";
            }
        }
        return array($answer,$err);
    }

    function getDocTypeSelectList($sel_id){$db=DbSingleton::getDb();
        $list="<option value=0>Оберіть зі списку</option>";
        $r=$db->query("SELECT `id`, `mcaption` FROM `manual` WHERE `ison`='1' and `key`='client_sale_type' ORDER BY `mid`, `id` asc;");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"mcaption");
            $sel="";if ($sel_id==$id){$sel="selected='selected'";}
            $list.="<option value='$id' $sel>$name</option>";
        }
        return $list;
    }

    function showWorkersSelectList($sel_id){$db=DbSingleton::getDb();
        $r=$db->query("SELECT * FROM `media_users` ORDER BY `name`, `id` ASC;");$n=$db->num_rows($r);$list="";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"name");
            $sel="";if ($id==$sel_id){$sel=" selected";}
            $list.="<option value='$id' $sel>$name</option>";
        }
        return $list;
    }

    function showPayboxClientList($sel_id){$db=DbSingleton::getDb();
        $slave=new slave;$form="";$form_htm=RD."/tpl/clients_parrent_tree.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("SELECT c.*, ot.name as org_type_name, t2cn.COUNTRY_NAME, t2st.STATE_NAME, t2rg.REGION_NAME, t2ct.CITY_NAME  
        FROM `A_CLIENTS` c 
            LEFT OUTER JOIN A_ORG_TYPE ot on ot.id=c.org_type 
            LEFT OUTER JOIN T2_COUNTRIES t2cn on t2cn.COUNTRY_ID=c.country 
            LEFT OUTER JOIN T2_STATE t2st on t2st.STATE_ID=c.state
            LEFT OUTER JOIN T2_REGION t2rg on t2rg.REGION_ID=c.region
            LEFT OUTER JOIN T2_CITY t2ct on t2ct.CITY_ID=c.city
            LEFT OUTER JOIN A_CLIENTS_CATEGORY cc on cc.client_id=c.id
            LEFT OUTER JOIN A_CATEGORY ac on ac.id=cc.category_id
        WHERE c.status=1 and ac.id=3;");$n=$db->num_rows($r);$list="";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"name");
            $org_type_name=$db->result($r,$i-1,"org_type_name");
            $email=$db->result($r,$i-1,"email");
            $phone=$db->result($r,$i-1,"phone");
            $country=$slave->showTableFieldDBT("T2_COUNTRIES","COUNTRY_NAME","COUNTRY_ID",$db->result($r,$i-1,"country"));
            $state=$slave->showTableFieldDBT("T2_STATE","STATE_NAME","STATE_ID",$db->result($r,$i-1,"state"));
            $region=$slave->showTableFieldDBT("T2_REGION","REGION_NAME","REGION_ID",$db->result($r,$i-1,"region"));
            $city=$slave->showTableFieldDBT("T2_CITY","CITY_NAME","CITY_ID",$db->result($r,$i-1,"city"));
            $cur="";$fn="<i class='fa fa-thumb-tack' onClick='setPayboxClient(\"$id\", \"$name\")'></i>";
            if ($id==$sel_id){$fn="";$cur="style='background-color:#ccc; disabled:disabled;'";}
            $list.="<tr $cur>
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
        $form=str_replace("{list}",$list,$form);
        return $form;
    }

    function loadPayboxWorkersSaldo($paybox_id){$db=DbSingleton::getDb();
        $media_users=new media_users;
        $form="";$form_htm=RD."/tpl/paybox_workers_saldo_list.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("SELECT sum(`saldo`) as summ, `cash_id`, `user_id`, `last_update` FROM `B_PAYBOX_BALANS` 
        WHERE `paybox_id`='$paybox_id' GROUP BY `user_id`, `cash_id` ORDER BY `user_id` asc;");$n=$db->num_rows($r);$list="";
        for ($i=1;$i<=$n;$i++){
            $worker_id=$db->result($r,$i-1,"user_id");$worker_name=$media_users->getMediaUserName($worker_id);
            $summ=$db->result($r,$i-1,"summ");
            $cash_id=$db->result($r,$i-1,"cash_id");$cash_abr=$this->getCashAbr($cash_id);
            $list.="<tr>
                <td>$i</td>
                <td>$worker_name</td>
                <td>$summ</td>
                <td>$cash_abr</td>
                <td>
                    <button class='btn btn-sm btn-default' onClick='showPayboxWorkerSaldoJournal(\"$paybox_id\",\"$worker_id\",\"$cash_id\");'><i class='fa fa-search'></i></button>
                </td>
            </tr>";
        }
        if ($n==0){$list="<tr><td align='center' colspan=5><h3 class='text-center'>Записи відсутні</h3></td></tr>";}
        $form=str_replace("{list_saldo}",$list,$form);
        $form=str_replace("{paybox_id}",$paybox_id,$form);
        return $form;
    }

    function showPayboxWorkerSaldoJournal($paybox_id,$user_id,$cash_id){$db=DbSingleton::getDb();
        $form="";$form_htm=RD."/tpl/paybox_workers_saldo_journal.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("SELECT * FROM `B_PAYBOX_JOURNAL` WHERE `paybox_id`='$paybox_id' AND `cash_id`='$cash_id' AND `user_id`='$user_id' ORDER BY `id` DESC LIMIT 0,20;");$n=$db->num_rows($r);$list="";
        for ($i=1;$i<=$n;$i++){
            $saldo_before=$db->result($r,$i-1,"saldo_before");
            $amount=$db->result($r,$i-1,"amount");
            $saldo_after=$db->result($r,$i-1,"saldo_after");
            $cash_id=$db->result($r,$i-1,"cash_id");$cash_abr=$this->getCashAbr($cash_id);
            $data=$db->result($r,$i-1,"data");
            $list.=" <tr>
                <td>$i</td>
                <td>$data</td>
                <td>$cash_abr</td>
                <td>$saldo_before</td>
                <td>$amount</td>
                <td>$saldo_after</td>
            </tr>";
        }
        if ($n==0){$list="<tr><td align='center' colspan=6><h3 class='text-center'>Записи відсутні</h3></td></tr>";}
        $form=str_replace("{list_saldo}",$list,$form);
        $form=str_replace("{paybox_id}",$paybox_id,$form);
        return $form;
    }

    function loadPayboxWorkers($paybox_id){$db=DbSingleton::getDb();
        $media_users=new media_users;
        $form="";$form_htm=RD."/tpl/paybox_workers_list.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("SELECT * FROM `PAY_BOX_WORKERS` WHERE `paybox_id`='$paybox_id' and `status`='1' ORDER BY `id` asc;");$n=$db->num_rows($r);$list="";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $worker_id=$db->result($r,$i-1,"worker_id");
            $worker_name=$media_users->getMediaUserName($worker_id);
            $list.="<tr>
                <td>
                    <button class='btn btn-sm btn-default' onClick='showPayboxWorkerForm(\"$paybox_id\",\"$id\");'><i class='fa fa-edit'></i></button>
                    <button class='btn btn-sm btn-default' onClick='dropPayboxWorker(\"$paybox_id\",\"$id\");'><i class='fa fa-times'></i></button>
                </td>
                <td>$i</td>
                <td>$worker_name</td>
            </tr>";
        }
        if ($n==0){$list="<tr><td align='center' colspan=5><h3 class='text-center'>Записи відсутні</h3></td></tr>";}
        $form=str_replace("{list_workers}",$list,$form);
        $form=str_replace("{paybox_id}",$paybox_id,$form);
        return $form;
    }

    function showPayboxWorkerForm($paybox_id,$s_id){$db=DbSingleton::getDb();
        $worker_id=0;
        $form="";$form_htm=RD."/tpl/paybox_workers_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("SELECT * FROM `PAY_BOX_WORKERS` WHERE `id`='$s_id' AND `paybox_id`='$paybox_id' LIMIT 1;");$n=$db->num_rows($r);
        if ($n==1){
            $worker_id=$db->result($r,0,"worker_id");
        }
        $form=str_replace("{paybox_id}",$paybox_id,$form);
        $form=str_replace("{s_id}",$s_id,$form);
        $form=str_replace("{workers_list}",$this->showWorkersSelectList($worker_id),$form);
        return $form;
    }

    function savePayboxWorkerForm($paybox_id,$s_id,$worker_id){ $db=DbSingleton::getDb();
        $slave=new slave;$answer=0;$err="Помилка збереження даних!";
        $paybox_id=$slave->qq($paybox_id);$s_id=$slave->qq($s_id);$worker_id=$slave->qq($worker_id);
        if ($paybox_id>0){
            if ($s_id==0){
                $r=$db->query("SELECT MAX(`id`) as mid FROM `PAY_BOX_WORKERS`;");$s_id=0+$db->result($r,0,"mid")+1;
                $db->query("INSERT INTO `PAY_BOX_WORKERS` (`id`,`paybox_id`,`status`) VALUES ('$s_id','$paybox_id','1');");
            }
            if ($s_id>0){
                $db->query("UPDATE `PAY_BOX_WORKERS` SET `worker_id`='$worker_id' WHERE `id`='$s_id' AND `paybox_id`='$paybox_id';");
                $answer=1;$err="";
            }
        } else {$answer=0;}
        return array($answer,$err);
    }

    function dropPaybox($paybox_id){$db=DbSingleton::getDb();
        $answer=0;$err="Помилка збереження даних!";
        if ($paybox_id>0){
            $db->query("UPDATE `PAY_BOX` SET `status`='0' WHERE `id`='$paybox_id';");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function dropPayboxWorker($paybox_id,$s_id){$db=DbSingleton::getDb();
        $slave=new slave;$answer=0;$err="Помилка збереження даних!";
        $paybox_id=$slave->qq($paybox_id);$s_id=$slave->qq($s_id);
        if ($paybox_id>0 && $s_id>0){
            $db->query("UPDATE `PAY_BOX_WORKERS` SET `status`='0' WHERE `id`='$s_id' AND `paybox_id`='$paybox_id';");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function getPayboxSaldo($paybox_id){$db=DbSingleton::getDb();
        $r=$db->query("SELECT SUM(`saldo`) as summ, `cash_id` FROM `B_PAYBOX_BALANS` WHERE `paybox_id`='$paybox_id' GROUP BY `cash_id` ORDER BY `cash_id` ASC;");$n=$db->num_rows($r);$saldo="";
        if ($n==0){ $saldo="0";}
        for ($i=1;$i<=$n;$i++){
            $summ=$db->result($r,$i-1,"summ");
            $cash_id=$db->result($r,$i-1,"cash_id");$cash_abr=$this->getCashAbr($cash_id);
            $saldo.="$summ$cash_abr; ";
        }
        return $saldo;
    }

    function getCashAbr($cash_id){$db=DbSingleton::getDb();
        $r=$db->query("SELECT `abr` FROM `CASH` WHERE `id`='$cash_id' LIMIT 1;");$n=$db->num_rows($r);$name="";
        if ($n==1){$name=$db->result($r,0,"abr");}
        return $name;
    }

    /* CASH REPORTS */

    function getCashReportsFilters() {
        $form_htm=RD."/tpl/cash_reports.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $form=str_replace("{date}",date("Y-m-d"),$form);
        $form=str_replace("{paybox_list}", $this->showPayBoxSelect(), $form);
        return $form;
    }

    function showPayBoxSelect() { $db=DbSingleton::getDb();
        $r=$db->query("SELECT * FROM `PAY_BOX` WHERE `status`=1 AND `in_use`=1;"); $n=$db->num_rows($r);$list="";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"name");
            $list.="<option value=$id>$name</option>";
        }
        return $list;
    }

    function getPayBoxName($id) { $db=DbSingleton::getDb();
        $r=$db->query("SELECT `name` FROM `PAY_BOX` WHERE `id`='$id';");
        $name=$db->result($r,0,"name");
        return $name;
    }

    function showCashReportsList($date_start,$date_end,$payboxes,$cash_id) { $db=DbSingleton::getDb();
        // КАСА
        $list=""; $summ_kasa=0;
        $r=$db->query("SELECT `id`, `paybox_id`, `pay_type_id` FROM `J_PAY` 
		WHERE `paybox_id` in ($payboxes) 
		AND `pay_type_id` in (89,90,91,98)
		AND `data_time`>='$date_start 00:00:00' AND `data_time`<='$date_end 23:59:59'
		AND `cash_id`='$cash_id'
		GROUP BY `paybox_id`;"); $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $paybox_id=$db->result($r,$i-1,"paybox_id");
            $name=$this->getPayBoxName($paybox_id);
            $summ_1=$this->getSummPayBox($date_start,$date_end,$paybox_id,"89",$cash_id);
            $summ_2=$this->getSummPayBox($date_start,$date_end,$paybox_id,"90",$cash_id);
            $summ_3=$this->getSummPayBox($date_start,$date_end,$paybox_id,"98",$cash_id);
            $summ_4=($this->getSummPayBox($date_start,$date_end,$paybox_id,"91",$cash_id))*(-1);
            $summ=$summ_1+$summ_2+$summ_3+$summ_4;
            $summ_prixod=$summ_1+$summ_2+$summ_3;
            $summ_vidacha=$summ_4;
            $summ_kasa+=$summ;
            $list.="<tr>
				<td>$name</td>
				<td>$summ_prixod</td>
				<td>$summ_vidacha</td>
				<td>$summ</td>
			</tr>";
        }

        $list.="<tr style='background:lightgreen;'>
			<td><b>Каси</b></td>
			<td></td>
			<td></td>
			<td>$summ_kasa</td>
		</tr>";

        // RASXODI
        $array_spend=$this->getSpendTypes(); $summ_vidatki=0;
        $r=$db->query("SELECT `id`, `paybox_id_from` FROM `J_MONEY_SPEND` 
		WHERE `paybox_id_from` in ($payboxes) 
		AND `data`>='$date_start 00:00:00' AND `data`<='$date_end 23:59:59'
		AND `cash_id`='$cash_id'
		GROUP BY `paybox_id_from`;"); $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $paybox_id=$db->result($r,$i-1,"paybox_id_from");
            $name=$this->getPayBoxName($paybox_id);
            $summ_spend=0;
            foreach ($array_spend as $spend_type_id) {
                $caption=$this->getSpendTypesCaption($spend_type_id);
                $summ_type=$this->getSummMoneySpendType($date_start,$date_end,$paybox_id,$spend_type_id,$cash_id);
                $summ_spend+=$summ_type;
                if ($summ_type>0)
                    $list.="<tr>
                        <td>$caption</td>
                        <td>-</td>
                        <td>$summ_type</td>
                        <td>$summ_type</td>
                    </tr>";
            }

            $summ_vidatki+=$summ_spend;
            $list.="<tr>
				<td><b>$name</b></td>
				<td>-</td>
				<td>-</td>
				<td>$summ_spend</td>
			</tr>";
        }

        $list.="<tr style='background:pink;'>
			<td><b>Видатки</b></td>
			<td></td>
			<td></td>
			<td>$summ_vidatki</td>
		</tr>";

        if ($date_start!=null) {
            $date_start = strtotime($date_start);
            $date_convert_start=date('d.m.Y', $date_start); } else $date_convert_start=$date_start;

        if ($date_end!=null) {
            $date_end = strtotime($date_end);
            $date_convert_end=date('d.m.Y', $date_end); } else $date_convert_end=$date_end;

        $form="";
        if ($cash_id==1) {
            $form_htm=RD."/tpl/cash_reports_table.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
            $form=str_replace("{cash_abr}","UAH",$form);
        }
        if ($cash_id==2) {
            $form_htm=RD."/tpl/cash_reports_table_usd.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
            $form=str_replace("{cash_abr}","USD",$form);
        }
        if ($cash_id==3) {
            $form_htm=RD."/tpl/cash_reports_table_eur.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
            $form=str_replace("{cash_abr}","EUR",$form);
        }

        $form=str_replace("{date_start}",$date_convert_start,$form);
        $form=str_replace("{date_end}",$date_convert_end,$form);
        $form=str_replace("{reports_range}",$list,$form);
        return $form;
    }

    function getSpendTypes() { $db=DbSingleton::getDb();
        $array=[];
        $r=$db->query("SELECT `id` FROM `manual` WHERE `key`='spend_type_id';"); $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            array_push($array,$id);
        }
        return $array;
    }

    function getSpendTypesCaption($id) { $db=DbSingleton::getDb();
        $r=$db->query("SELECT * FROM `manual` WHERE `id`='$id';");
        $caption=$db->result($r,0,"mcaption");
        return $caption;
    }

    function getSummPayBox($date_start,$date_end,$paybox_id,$pay_type_id,$cash_id) { $db=DbSingleton::getDb();
        $r=$db->query("SELECT SUM(`summ`) as pay_summ FROM `J_PAY` 
		WHERE `paybox_id`=$paybox_id 
		AND `pay_type_id`=$pay_type_id
		AND `data_time`>='$date_start 00:00:00' AND `data_time`<='$date_end 23:59:59'
		AND `cash_id`='$cash_id'
		GROUP BY `paybox_id`;");
        $summ=$db->result($r,0,"pay_summ");
        return $summ;
    }

    function getSummMoneySpend($date_start,$date_end,$paybox_id,$cash_id) { $db=DbSingleton::getDb();
        $r=$db->query("SELECT `id`, `paybox_id_from`, SUM(`summ`) as summa FROM `J_MONEY_SPEND` 
		WHERE `paybox_id_from`=$paybox_id
		AND `data`>='$date_start 00:00:00' AND `data`<='$date_end 23:59:59'
		AND `cash_id`='$cash_id'
		GROUP BY `paybox_id_from`;");
        $summ=$db->result($r,0,"summa");
        return $summ;
    }

    function getSummMoneySpendType($date_start,$date_end,$paybox_id,$spend_type_id,$cash_id) { $db=DbSingleton::getDb();
        $r=$db->query("SELECT `id`, `paybox_id_from`, SUM(`summ`) as summa FROM `J_MONEY_SPEND` 
		WHERE `paybox_id_from`=$paybox_id
		AND `data`>='$date_start 00:00:00' AND `data`<='$date_end 23:59:59'
		AND `spend_type_id`='$spend_type_id'
		AND `cash_id`='$cash_id'
		GROUP BY `paybox_id_from`;");
        $summ=$db->result($r,0,"summa");
        return $summ;
    }

}
