<?php

class jpay {

protected $prefix_new = 'ДП';

    function getSaleInvoiceName($id){$db=DbSingleton::getDb();
        $name="";
        $r=$db->query("SELECT * FROM `J_SALE_INVOICE` WHERE `status`=1 AND `id`='$id' LIMIT 1;");
        $n=$db->num_rows($r);
        if ($n==1) {
            $name=$db->result($r,0,"prefix")."-".$db->result($r,0,"doc_nom");
        }
        return $name;
    }

    function getClientName($id){$db=DbSingleton::getDb();
        $name="";
        $r=$db->query("SELECT `name` FROM `A_CLIENTS` WHERE `id`='$id' LIMIT 1;");
        $n=$db->num_rows($r);
        if ($n==1) {
            $name=$db->result($r,0,"name");
        }
        return $name;
    }

    function getJpayDocTypeSelect() {$db=DbSingleton::getDb();
        $list="";
        $r=$db->query("SELECT * FROM `manual` WHERE `key`='pay_type_id';");
        $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $mcaption=$db->result($r,$i-1,"mcaption");
            $list.="<option value='$id'>$mcaption</option>";
        }
        return $list;
    }

    function getJpayNameSelect() {$db=DbSingleton::getDb();
        $list="";
        $r=$db->query("SELECT * FROM `PAY_BOX` WHERE `in_use`=1 AND `status`=1;");
        $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $full_name=$db->result($r,$i-1,"full_name");
            $list.="<option value='$id'>$full_name</option>";
        }
        return $list;
    }

    function filterJPayList($data_start, $data_end, $doc_type, $jpay_name) { $db = DbSingleton::getDb();
        $where=""; $limit="LIMIT 0,1000";
        if ($where!=""){$limit="";}
        if($doc_type>0) $where_doc="AND j.pay_type_id='$doc_type'"; else $where_doc="";
        if($jpay_name>0) $where_pay="AND j.paybox_id='$jpay_name'"; else $where_pay="";

        $data_cur=date("Y-m-d"); $list="";
        if ($data_start!="" && $data_end!="") $where_date="AND j.data_time>='$data_start 00:00:00' AND j.data_time<='$data_end 23:59:59'";
        else $where_date=" AND j.data_time>='$data_cur 00:00:00' AND j.data_time<='$data_cur 23:59:59'";

        $r=$db->query("SELECT j.*, CASH.name as cash_name, m.mcaption as pay_type_name, p.name as paybox_name, p.full_name as paybox_full_name, mu.name as user_name, j.client_id, cl.full_name as client_name 
        FROM `J_PAY` j
            LEFT OUTER JOIN `CASH` on CASH.id=j.cash_id
            LEFT OUTER JOIN `manual` m on (m.id=j.pay_type_id and m.`key`='pay_type_id')
            LEFT OUTER JOIN `PAY_BOX` p on p.id=j.paybox_id
            LEFT OUTER JOIN `media_users` mu on mu.id=j.user_id
            LEFT OUTER JOIN `A_CLIENTS` cl on cl.id=j.client_id
        WHERE j.status=1 $where $where_doc $where_pay $where_date ORDER BY j.id DESC $limit;");
        $n=$db->num_rows($r);
        for ($i=1; $i<=$n; $i++) {
            $id=$db->result($r,$i-1,"id");
            $pay_type_name=$db->result($r,$i-1,"pay_type_name");
            $paybox_name=$db->result($r,$i-1,"paybox_name");
            if ($paybox_name==""){$paybox_name=$db->result($r,$i-1,"paybox_full_name");}
            $data_time=$db->result($r,$i-1,"data_time");
            $doc_nom=$db->result($r,$i-1,"doc_nom");
            $cash_name=$db->result($r,$i-1,"cash_name");
            $summ=$db->result($r,$i-1,"summ");
            $user_name=$db->result($r,$i-1,"user_name");
            $client_id=$db->result($r,$i-1,"client_id");
            $client_name=$db->result($r,$i-1,"client_name");
            $parrent_doc_name=$this->getJpayParrentDocName($id,$doc_nom);
            if ($data_time!=null) {
                $data_time = strtotime($data_time);
                $date_convert=date('d-m-Y H:i:s', $data_time);
            } else {
                $date_convert=$data_time;
            }
            $function="viewJpayMoneyPay(\"$id\")";
            $list.="<tr style='cursor:pointer' onClick='$function'>
                <td>$pay_type_name</td>
                <td>$paybox_name</td>
                <td align='center'>$date_convert</td>
                <td align='center'>$doc_nom</td>
                <td>$cash_name</td>
                <td>$summ</td>
                <td>$parrent_doc_name</td>
                <td>$user_name</td>
                <td>$client_id</td>
                <td>$client_name</td>
            </tr>";
        }
        return $list;
    }

    function show_jpay_list() { $db = DbSingleton::getDb();
        $where=""; $limit="LIMIT 0,1000";
        if ($where!=""){$limit="";} $list="";
        $r=$db->query("SELECT j.*, CASH.name as cash_name, m.mcaption as pay_type_name, p.name as paybox_name, p.full_name as paybox_full_name, mu.name as user_name, j.client_id, cl.full_name as client_name 
        FROM `J_PAY` j
            LEFT OUTER JOIN CASH on CASH.id=j.cash_id
            LEFT OUTER JOIN manual m on (m.id=j.pay_type_id and m.`key`='pay_type_id')
            LEFT OUTER JOIN PAY_BOX p on p.id=j.paybox_id
            LEFT OUTER JOIN media_users mu on mu.id=j.user_id
            LEFT OUTER JOIN A_CLIENTS cl on cl.id=j.client_id
        WHERE j.status=1 $where ORDER BY j.id DESC $limit;");
        $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++) {
            $id=$db->result($r,$i-1,"id");
            $pay_type_name=$db->result($r,$i-1,"pay_type_name");
            $paybox_name=$db->result($r,$i-1,"paybox_name");
            if ($paybox_name==""){$paybox_name=$db->result($r,$i-1,"paybox_full_name");}
            $data_time=$db->result($r,$i-1,"data_time");
            $doc_nom=$db->result($r,$i-1,"doc_nom");
            $cash_name=$db->result($r,$i-1,"cash_name");
            $summ=$db->result($r,$i-1,"summ");
            $user_name=$db->result($r,$i-1,"user_name");
            $client_id=$db->result($r,$i-1,"client_id");
            $client_name=$db->result($r,$i-1,"client_name");
            $parrent_doc_name=$this->getJpayParrentDocName($id,$doc_nom);
            if ($data_time!=null) $date_convert=date("d-m-Y H:i:s", strtotime($data_time)); else $date_convert=$data_time;
            $function="viewJpayMoneyPay(\"$id\")";
            $list.="<tr style='cursor:pointer' onClick='$function'>
                <td>$pay_type_name</td>
                <td>$paybox_name</td>
                <td align='center'>$date_convert</td>
                <td align='center'>$doc_nom</td>
                <td>$cash_name</td>
                <td>$summ</td>
                <td>$parrent_doc_name</td>
                <td>$user_name</td>
                <td>$client_id</td>
                <td>$client_name</td>
            </tr>";
        }
        return $list;
    }

    function getJpayParrentDocName($pay_id, $doc_nom) { $db=DbSingleton::getDb();
        $list="";
        $r1=$db->query("SELECT * FROM `J_PAY_STR` WHERE `pay_id`='$pay_id' ORDER BY `id` ASC;");
        $n1=$db->num_rows($r1);
        for ($i1=1;$i1<=$n1;$i1++){
            $parrent_doc_type_id=$db->result($r1,$i1-1,"parrent_doc_type_id");
            $parrent_doc_id=$db->result($r1,$i1-1,"parrent_doc_id");
            $parent_doc_name="Вн №".$this->getSaleInvoiceName($parrent_doc_id);
            if ($parrent_doc_type_id==98){ $parent_doc_name="Ав №$doc_nom"; }
            $list.=$parent_doc_name;
            if ($i1<$n1){$list.="\n";}
        }
        return $list;
    }

    function getKoursData() { $db=DbSingleton::getDb();
        $slave=new slave;$usd_to_uah=0;$eur_to_uah=0;
        $r=$db->query("SELECT `kours_value` FROM `J_KOURS` WHERE `cash_id`='2' AND `in_use`='1' ORDER BY `id` DESC LIMIT 1;");
        $n=$db->num_rows($r);
        if ($n==1){$usd_to_uah=$slave->to_money(round($db->result($r,0,"kours_value"),2));}
        $r=$db->query("SELECT `kours_value` FROM `J_KOURS` WHERE `cash_id`='3' AND `in_use`='1' ORDER BY `id` DESC LIMIT 1;");
        $n=$db->num_rows($r);
        if ($n==1){$eur_to_uah=$slave->to_money(round($db->result($r,0,"kours_value"),2));}
        return array($usd_to_uah,$eur_to_uah);
    }

    function loadJpayCashBoxList($client_id, $doc_type_id, $seller_id) {
        return $this->showPayBoxSelectList(0, $doc_type_id, $seller_id);
    }

    function showJpayAvansMoneyPayForm() {
        $gmanual=new gmanual;
        $form="";$form_htm=RD."/tpl/jpay_avans_money_pay_form.htm";
        if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $summ=$summ_debit=$summ_kredit=$client_balans_avans=0;
        $print_pay_disabled="disabled"; $cash_kours="1";$cash_id=1;$cash_name=$this->getCashName($cash_id);
        $form=str_replace("{doc_cash_id}",$cash_id,$form);
        $form=str_replace("{cash_name}",$cash_name,$form);
        $form=str_replace("{doc_cash_name}",$cash_name,$form);
        $form=str_replace("{sale_invoice_summ}",$summ,$form);
        $form=str_replace("{sale_invoice_debit}",$summ_debit,$form);
        $form=str_replace("{avans_kredit}",$summ_kredit,$form);
        $form=str_replace("{client_balans_avans}",$client_balans_avans,$form);
        $form=str_replace("{cash_kours}",$cash_kours,$form);
        $form=str_replace("{paybox_list}",$this->showPayBoxSelectList(0,0,0),$form);
        $form=str_replace("{pay_type_list}",$gmanual->showGmanualSelectList('pay_type_id','99'),$form);
        $form=str_replace("{cash_list}",$this->showCashListSelect($cash_id),$form);
        $form=str_replace("{pay_type_id_disabled}",$print_pay_disabled,$form);
        return $form;
    }

    function showJpayAutoMoneyPayForm() {
        $gmanual=new gmanual;
        $form="";$form_htm=RD."/tpl/jpay_auto_money_pay_form.htm";
        if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $summ=$summ_debit=$summ_kredit=$client_balans_avans=0;
        $print_pay_disabled="disabled"; $cash_kours="1";$cash_id=1;$cash_name=$this->getCashName($cash_id);
        $form=str_replace("{doc_cash_id}",$cash_id,$form);
        $form=str_replace("{cash_name}",$cash_name,$form);
        $form=str_replace("{doc_cash_name}",$cash_name,$form);
        $form=str_replace("{sale_invoice_summ}",$summ,$form);
        $form=str_replace("{sale_invoice_debit}",$summ_debit,$form);
        $form=str_replace("{sale_invoice_kredit}",$summ_kredit,$form);
        $form=str_replace("{cash_kours}",$cash_kours,$form);
        $form=str_replace("{paybox_list}",$this->showPayBoxSelectList(0,0,0),$form);
        $form=str_replace("{pay_type_list}",$gmanual->showGmanualSelectList('pay_type_id','90'),$form);
        $form=str_replace("{cash_list}",$this->showCashListSelect($cash_id),$form);
        $form=str_replace("{pay_type_id_disabled}",$print_pay_disabled,$form);
        return $form;
    }

    function showJpayMoneyPayForm() {
        $gmanual=new gmanual;
        $form="";$form_htm=RD."/tpl/jpay_money_pay_form.htm";
        if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $summ=$summ_debit=$summ_kredit=$client_balans_avans=0;$invoice_id=$pay_id=0;
        $print_pay_disabled="disabled"; $cash_kours="1";$cash_id=1;$cash_name=$this->getCashName($cash_id);
        $form=str_replace("{invoice_id}",$invoice_id,$form);
        $form=str_replace("{pay_id}",$pay_id,$form);
        $form=str_replace("{doc_cash_id}",$cash_id,$form);
        $form=str_replace("{cash_name}",$cash_name,$form);
        $form=str_replace("{doc_cash_name}",$cash_name,$form);
        $form=str_replace("{print_pay_disabled}",$print_pay_disabled,$form);
        $form=str_replace("{sale_invoice_summ}",$summ,$form);
        $form=str_replace("{sale_invoice_debit}",$summ_debit,$form);
        $form=str_replace("{sale_invoice_kredit}",$summ_kredit,$form);
        $form=str_replace("{cash_kours}",$cash_kours,$form);
        $form=str_replace("{paybox_list}",$this->showPayBoxSelectList(0,0,0),$form);
        $form=str_replace("{pay_type_list}",$gmanual->showGmanualSelectList('pay_type_id','89'),$form);
        $form=str_replace("{cash_list}",$this->showCashListSelect($cash_id),$form);
        $form=str_replace("{pay_type_id_disabled}","disabled",$form);
        return $form;
    }

    function viewJpayMoneyPay($pay_id) { $db=DbSingleton::getDb();
        $slave=new slave;
        session_start();
        $summ_kredit=$parent_doc_name=$pay_cash_kours=0;
        $form="";$form_htm=RD."/tpl/jpay_money_pay_view.htm";
        if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("SELECT jp.*, mu.name as user_name, pt.mcaption as pay_type_name, tpb.name as paybox_name 
        FROM `J_PAY` jp 
            LEFT OUTER JOIN `media_users` mu on mu.id=jp.user_id
            LEFT OUTER JOIN `manual` pt on pt.id=jp.pay_type_id
            LEFT OUTER JOIN `PAY_BOX` tpb on tpb.id=jp.paybox_id
        WHERE jp.id='$pay_id' LIMIT 1;");
        $pay_type_name=$db->result($r,0,"pay_type_name");
        $paybox_name=$db->result($r,0,"paybox_name");
        $data_time=$db->result($r,0,"data_time");
        $doc_nom=$db->result($r,0,"doc_nom");
        $client_id=$db->result($r,0,"client_id");$client_name=$this->getClientName($client_id);
        $cash_id=$db->result($r,0,"cash_id");$cash_name=$this->getCashAbr($cash_id);
        $summ=$db->result($r,0,"summ");
        $user_name=$db->result($r,0,"user_name");

        $str_list="";
        $r1=$db->query("SELECT * FROM `J_PAY_STR` WHERE `pay_id`='$pay_id' ORDER BY `id` ASC;");
        $n1=$db->num_rows($r1);
        for ($i1=1;$i1<=$n1;$i1++){
            $parrent_doc_type_id=$db->result($r1,$i1-1,"parrent_doc_type_id");
            $parrent_doc_id=$db->result($r1,$i1-1,"parrent_doc_id");
            $summ_doc=$db->result($r1,$i1-1,"summ_doc");
            $doc_cash_id=$db->result($r1,$i1-1,"doc_cash_id");$doc_cash_name=$this->getCashAbr($doc_cash_id);
            $str_summ_pay=$db->result($r1,$i1-1,"summ_pay");
            $pay_cash_id=$db->result($r1,$i1-1,"pay_cash_id");$pay_cash_name=$this->getCashAbr($pay_cash_id);
            $pay_cash_kours=$db->result($r1,$i1-1,"pay_cash_kours");
            $pay_cash_summ=$db->result($r1,$i1-1,"pay_cash_summ");
            $parent_doc_name="Видаткова накладна №".$this->getSaleInvoiceName($parrent_doc_id);
            if ($parrent_doc_type_id==98){ $parent_doc_name="Авансовий платіж №$doc_nom"; }
            $str_list.="<tr>
                <td style='text-align:center'>$i1</td>
                <td style='text-align:center'>$parent_doc_name</td>
                <td style='text-align:center'>$summ_doc</td>
                <td style='text-align:center'>$str_summ_pay</td>
                <td style='text-align:center'>$doc_cash_name</td>
                <td style='text-align:center'>$pay_cash_kours</td>
                <td style='text-align:center'>$pay_cash_summ $pay_cash_name</td>
                <td style='text-align:center'>$user_name</td>
            </tr>";
        }

        if ($pay_id==0){$form_htm=RD."/tpl/access_deny.htm";
        if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);} }
        $form=str_replace("{pay_id}",$pay_id,$form);
        $form=str_replace("{parent_doc_name}",$parent_doc_name,$form);
        $form=str_replace("{client_name}",$client_name,$form);
        $form=str_replace("{doc_cash_id}",$cash_id,$form);
        $form=str_replace("{pay_type_name}",$pay_type_name,$form);
        $form=str_replace("{cash_name}",$cash_name,$form);
        $form=str_replace("{doc_cash_name}",$cash_name,$form);
        $form=str_replace("{doc_name}",$doc_nom,$form);
        $form=str_replace("{doc_data}",$slave->data_word($data_time),$form);
        $form=str_replace("{summ}",$summ,$form);
        $form=str_replace("{sale_invoice_kredit}",$summ_kredit,$form);
        $form=str_replace("{cash_kours}",$pay_cash_kours,$form);
        $form=str_replace("{paybox_name}",$paybox_name,$form);
        $form=str_replace("{user_name}",$user_name,$form);
        $form=str_replace("{jpay_str_list}",$str_list,$form);
        return $form;
    }

    function showJpayAvansForm() {
        $gmanual=new gmanual;
        $form="";$form_htm=RD."/tpl/jpay_avans_pay_form.htm";
        if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $print_pay_disabled="disabled"; $cash_kours="1";$cash_id=1;$cash_name=$this->getCashName($cash_id);
        $form=str_replace("{doc_cash_id}",$cash_id,$form);
        $form=str_replace("{cash_name}",$cash_name,$form);
        $form=str_replace("{doc_cash_name}",$cash_name,$form);
        $form=str_replace("{sale_invoice_kredit}",0,$form);
        $form=str_replace("{cash_kours}",$cash_kours,$form);
        $form=str_replace("{paybox_list}",$this->showPayBoxSelectList(0,0,0),$form);
        $form=str_replace("{pay_type_list}",$gmanual->showGmanualSelectList('pay_type_id','98'),$form);
        $form=str_replace("{cash_list}",$this->showCashListSelect($cash_id),$form);
        $form=str_replace("{pay_type_id_disabled}",$print_pay_disabled,$form);
        return $form;
    }

    function showJpayMoneyBackForm() {
        $gmanual=new gmanual;
        $form="";$form_htm=RD."/tpl/jpay_money_back_form.htm";
        if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $summ=$summ_debit=$summ_kredit=$client_balans_avans=0;
        $invoice_id=$pay_id=0;
        $print_pay_disabled="disabled"; $cash_kours="1";$cash_id=1;$cash_name=$this->getCashName($cash_id);
        $form=str_replace("{invoice_id}",$invoice_id,$form);
        $form=str_replace("{pay_id}",$pay_id,$form);
        $form=str_replace("{doc_cash_id}",$cash_id,$form);
        $form=str_replace("{cash_name}",$cash_name,$form);
        $form=str_replace("{doc_cash_name}",$cash_name,$form);
        $form=str_replace("{print_pay_disabled}",$print_pay_disabled,$form);
        $form=str_replace("{sale_invoice_summ}",$summ,$form);
        $form=str_replace("{sale_invoice_debit}",$summ_debit,$form);
        $form=str_replace("{sale_invoice_kredit}",$summ_kredit,$form);
        $form=str_replace("{cash_kours}",$cash_kours,$form);
        $form=str_replace("{avans_debit}",0,$form);
        $form=str_replace("{client_balans_avans}",0,$form);
        $form=str_replace("{paybox_balans}","---",$form);
        $form=str_replace("{paybox_list}",$this->showPayBoxUserSelectList(0),$form);
        $form=str_replace("{pay_type_list}",$gmanual->showGmanualSelectList('pay_type_id','91'),$form);
        $form=str_replace("{cash_list}",$this->showCashListSelect($cash_id),$form);
        $form=str_replace("{pay_type_id_disabled}","disabled",$form);
        return $form;
    }

    function showPayBoxSelectList($paybox_id, $doc_type_id, $seller_id) { $db=DbSingleton::getDb();
        session_start();
        $user_id=$_SESSION["media_user_id"];
        $list="";
        $where_seller="";
        if ($seller_id>0){$where_seller=" and pb.firm_id='$seller_id'";}
        $r=$db->query("SELECT pb.* FROM `PAY_BOX` pb 
            LEFT OUTER JOIN `PAY_BOX_WORKERS` pbw ON pbw.paybox_id=pb.id 
        WHERE pbw.worker_id='$user_id' AND pb.doc_type_id='$doc_type_id' $where_seller AND pbw.status=1 AND pb.status=1 AND pb.in_use=1 ORDER BY pb.name ASC;");
        $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"name");
            $list.="<option value='$id'";
            if ($i==1){$list.=" selected";}
            $list.=">$name</option>";
        }
        return $list;
    }

    function showPayBoxUserSelectList($paybox_id) { $db=DbSingleton::getDb();
        session_start();
        $user_id=$_SESSION["media_user_id"];$list="";
        $r=$db->query("SELECT pb.* FROM `PAY_BOX` pb 
            LEFT OUTER JOIN `PAY_BOX_WORKERS` pbw ON pbw.paybox_id=pb.id 
        WHERE pbw.worker_id='$user_id' AND pbw.status=1 AND pb.status=1 AND pb.in_use=1 ORDER BY pb.name ASC;");
        $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"name");
            $list.="<option value='$id'";if ($id==$paybox_id){$list.=" selected";}$list.=">$name</option>";
        }
        return $list;
    }

    function getPayBoxUserBalans($paybox_id,$user_id,$cash_id) { $db=DbSingleton::getDb();
        $saldo=0;
        $r=$db->query("SELECT `saldo` FROM `B_PAYBOX_BALANS` WHERE `user_id`='$user_id' AND `paybox_id`='$paybox_id' AND `cash_id`='$cash_id' LIMIT 1;");
        $n=$db->num_rows($r);
        if ($n==1){
            $saldo=$db->result($r,0,"saldo");
        }
        return $saldo;
    }

    function getPayBoxBalans($paybox_id){$db=DbSingleton::getDb();
        session_start();
        $user_id=$_SESSION["media_user_id"];
        $list="---";
        $r=$db->query("SELECT * FROM `B_PAYBOX_BALANS` WHERE `user_id`='$user_id' AND `paybox_id`='$paybox_id' ORDER BY `id` ASC;");
        $n=$db->num_rows($r);
        if ($n>0){
            $list="";
            for ($i=1;$i<=$n;$i++){
                $saldo=$db->result($r,$i-1,"saldo");
                $cash_id=$db->result($r,$i-1,"cash_id");
                $cash_abr=$this->getCashAbr($cash_id);
                $last_update=$db->result($r,$i-1,"last_update");
                $list.="<strong>$saldo $cash_abr</strong>-$last_update<br>";
            }
        }
        return $list;
    }

    function showCashListSelect($sel_id, $ns=""){$db=DbSingleton::getDb();
        $list="";
        if ($ns==""){$ns=1;}
        $r=$db->query("SELECT * FROM `CASH` ORDER BY `name` ASC;");
        $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"abr");
            if ($ns==2){ $name=$db->result($r,$i-1,"name");}
            $sel="";if ($sel_id==$id){$sel="selected='selected'";}
            $list.="<option value='$id' $sel>$name</option>";
        }
        return $list;
    }

    function getCashAbr($cash_id){$db=DbSingleton::getDb();
        $name="";
        $r=$db->query("SELECT `abr` FROM `CASH` WHERE `id`='$cash_id' LIMIT 1;");
        $n=$db->num_rows($r);
        if ($n==1){$name=$db->result($r,0,"abr");}
        return $name;
    }

    function getCashName($cash_id){$db=DbSingleton::getDb();
        $name="";
        $r=$db->query("SELECT `name` FROM `CASH` WHERE `id`='$cash_id' LIMIT 1;");
        $n=$db->num_rows($r);
        if ($n==1){$name=$db->result($r,0,"name");}
        return $name;
    }

    function showJpayClientList($sel_id){$db=DbSingleton::getDb();
        $form="";$form_htm=RD."/tpl/clients_parrent_tree.htm";
        if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $list="";
        $r=$db->query("SELECT c.*, ot.name as org_type_name, t2cn.COUNTRY_NAME, t2st.STATE_NAME, t2rg.REGION_NAME, t2ct.CITY_NAME, acc.tpoint_id, tp.name as tpoint_name 
        FROM `A_CLIENTS` c 
            LEFT OUTER JOIN `A_ORG_TYPE` ot on ot.id=c.org_type 
            LEFT OUTER JOIN `T2_COUNTRIES` t2cn on t2cn.COUNTRY_ID=c.country 
            LEFT OUTER JOIN `T2_STATE` t2st on t2st.STATE_ID=c.state
            LEFT OUTER JOIN `T2_REGION` t2rg on t2rg.REGION_ID=c.region
            LEFT OUTER JOIN `T2_CITY` t2ct on t2ct.CITY_ID=c.city
            LEFT OUTER JOIN `A_CLIENTS_CATEGORY` cc on cc.client_id=c.id
            LEFT OUTER JOIN `A_CATEGORY` ac on ac.id=cc.category_id
            LEFT OUTER JOIN `A_CLIENTS_CONDITIONS` acc on acc.client_id=c.id 
            LEFT OUTER JOIN `T_POINT` tp on tp.id=acc.tpoint_id 
        WHERE c.status=1 AND ac.id>0 GROUP BY c.id;");
        $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"name");
            $org_type_name=$db->result($r,$i-1,"org_type_name");
            $email=$db->result($r,$i-1,"email");
            $phone=$db->result($r,$i-1,"phone");
            $country=$db->result($r,$i-1,"COUNTRY_NAME");
            $state=$db->result($r,$i-1,"STATE_NAME");
            $region=$db->result($r,$i-1,"REGION_NAME");
            $city=$db->result($r,$i-1,"CITY_NAME");
            $cur="";$fn=" onClick='setJpayClient(\"$id\", \"".base64_encode(iconv("windows-1251","utf-8",$name))."\")'";
            if ($id==$sel_id){$cur="background-color:#0CF;";}
            $list.="<tr style='$cur cursor:pointer;' $fn>
                <td></td>
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

    function unlinkJpayClient($pay_id){$db=DbSingleton::getDb();
        $slave=new slave;
        $answer=0;$err="Помилка збереження даних!";
        $pay_id=$slave->qq($pay_id);
        if ($pay_id>0){
            $db->query("UPDATE `J_PAY` SET `client_id`='0' WHERE `id`='$pay_id';");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function showJpayClientSaleInvoiceList($client_id){$db=DbSingleton::getDb();
        $form="";$form_htm=RD."/tpl/jpay_client_sale_invoice_list.htm";
        if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $list="";
        $r=$db->query("SELECT sv.*, t.name as tpoint_name, sl.name as seller_name, cl.name as client_name, dt.mvalue as doc_type_name, ch.abr2 as cash_abr 
        FROM `J_SALE_INVOICE` sv
            LEFT OUTER JOIN `CASH` ch on ch.id=sv.cash_id
            LEFT OUTER JOIN `T_POINT` t on t.id=sv.tpoint_id
            LEFT OUTER JOIN `A_CLIENTS` sl on sl.id=sv.seller_id
            LEFT OUTER JOIN `A_CLIENTS` cl on cl.id=sv.client_conto_id
            LEFT OUTER JOIN `manual` dt on dt.key='client_sale_type' and dt.id=sv.doc_type_id
        WHERE sv.status=1 and sv.client_conto_id='$client_id' and sv.summ_debit>0 
        ORDER BY sv.status_invoice ASC, sv.data_create ASC, sv.id ASC;");
        $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $prefix=$db->result($r,$i-1,"prefix");
            $doc_nom=$db->result($r,$i-1,"doc_nom");
            $data_create=$db->result($r,$i-1,"data_create");
            $tpoint_id=$db->result($r,$i-1,"tpoint_id");
            $tpoint_name=$db->result($r,$i-1,"tpoint_name");
            $seller_id=$db->result($r,$i-1,"seller_id");
            $seller_name=$db->result($r,$i-1,"seller_name");
            $client_name=$db->result($r,$i-1,"client_name");
            $doc_type_id=$db->result($r,$i-1,"doc_type_id");
            $doc_type_name=$db->result($r,$i-1,"doc_type_name");
            $summ=$db->result($r,$i-1,"summ");
            $summ_debit=$db->result($r,$i-1,"summ_debit");
            $cash_id=$db->result($r,$i-1,"cash_id");
            $cash_abr=$db->result($r,$i-1,"cash_abr");
            $data_pay=$db->result($r,$i-1,"data_pay");

            $list.="<tr align='center' style='cursor:pointer;' onClick='setJpaySaleInvoice(\"$id\", \"$prefix-$doc_nom\", \"$summ\", \"$summ_debit\",\"$tpoint_id\",\"$doc_type_id\",\"$cash_id\",\"$cash_abr\",\"$seller_id\")'>
                <td>$i</td>
                <td>$prefix-$doc_nom</td>
                <td align='center'>$data_create</td>
                <td>$tpoint_name</td>
                <td align='left'>$seller_name</td>
                <td align='left'>$client_name</td>
                <td>$doc_type_name</td>
                <td align='center' style='min-width:80px;'>$summ$cash_abr</td>
                <td align='center' style='min-width:80px;'>$summ_debit$cash_abr</td>
                <td align='right'>$data_pay</td>
            </tr>";
        }
        if ($n==0){$list="<tr><td colspan=11 align='center'>Накладні відсутні</td></tr>";}
        $form=str_replace("{sale_invoice_list}",$list,$form);
        return $form;
    }

    function loadJpayClientSaleInvoiceUnpayedList($client_id){$db=DbSingleton::getDb();
        $list="";$summ_balans=0;$tpoint_id=$doc_type_id=0;
        list($cash_id,$cash_name)=$this->getJpayClientDocCashId($client_id);
        list($client_balans_avans,)=$this->getClientBalansAvans($client_id);
        $m_seller_id=0;
        $r=$db->query("SELECT sv.*, t.name as tpoint_name, sl.name as seller_name, cl.name as client_name, dt.mvalue as doc_type_name 
        FROM `J_SALE_INVOICE` sv
            LEFT OUTER JOIN `T_POINT` t on t.id=sv.tpoint_id
            LEFT OUTER JOIN `A_CLIENTS` sl on sl.id=sv.seller_id
            LEFT OUTER JOIN `A_CLIENTS` cl on cl.id=sv.client_conto_id
            LEFT OUTER JOIN `manual` dt on dt.key='client_sale_type' and dt.id=sv.doc_type_id
        WHERE sv.status=1 AND sv.client_conto_id='$client_id' AND sv.summ_debit>0 
        ORDER BY sv.status_invoice ASC, sv.data_pay ASC, sv.data_create ASC, sv.id ASC;");
        $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $prefix=$db->result($r,$i-1,"prefix");
            $doc_nom=$db->result($r,$i-1,"doc_nom");
            $data_create=$db->result($r,$i-1,"data_create");
            $tpoint_id=$db->result($r,$i-1,"tpoint_id");
            $tpoint_name=$db->result($r,$i-1,"tpoint_name");
            $seller_id=$db->result($r,$i-1,"seller_id");
            if ($i==1){$m_seller_id=$seller_id;}
            $seller_name=$db->result($r,$i-1,"seller_name");
            $client_name=$db->result($r,$i-1,"client_name");
            $doc_type_id=$db->result($r,$i-1,"doc_type_id");
            $doc_type_name=$db->result($r,$i-1,"doc_type_name");
            $summ=$db->result($r,$i-1,"summ");
            $summ_debit=$db->result($r,$i-1,"summ_debit"); $summ_balans+=$summ_debit;
            $data_pay=$db->result($r,$i-1,"data_pay");

            $list.="<tr align='center' style='cursor:pointer;'>
                <td>$i</td>
                <td>$prefix-$doc_nom</td>
                <td align='center'>$data_create</td>
                <td>$tpoint_name</td>
                <td align='left'>$seller_name</td>
                <td align='left'>$client_name</td>
                <td>$doc_type_name</td>
                <td align='center' style='min-width:80px;'>$summ$cash_name</td>
                <td align='center' style='min-width:80px;'>$summ_debit$cash_name</td>
                <td align='right'>$data_pay</td>
            </tr>";
        }
        if ($n==0){$list="<tr><td colspan='11' align='center'>Не оплачені накладні відсутні</td></tr>";}
        return array($list,$summ_balans,$cash_id,$cash_name,$tpoint_id,$doc_type_id,$client_balans_avans,$m_seller_id);
    }

    function saveJpayMoneyPay($invoice_id,$kredit,$pay_type_id,$paybox_id,$doc_cash_id,$cash_id,$cash_kours) { $db=DbSingleton::getDb();
        $slave=new slave;
        session_start();
        $user_id=$_SESSION["media_user_id"];
        $answer=0;$err="Помилка збереження даних!";
        $invoice_id=$slave->qq($invoice_id);
        $invoice_client_id=0;$pay_id=0;$invoice_doc_type_id=$invoice_summ_debit=$invoice_summ=0;

        if ($invoice_id==0 || $invoice_id==""){ $err="Не вказано номер накладної для оплати";$answer=0; }

        if ($invoice_id>0 && $pay_id==0){
            $pay_id=$slave->qq($pay_id);$kredit=$slave->qq($kredit);$pay_type_id=$slave->qq($pay_type_id);$paybox_id=$slave->qq($paybox_id);

            if ($pay_id==0){
                $r=$db->query("SELECT MAX(`id`) as mid FROM `J_PAY`;");
                $pay_id=$db->result($r,0,"mid")+1;
                $r=$db->query("SELECT MAX(`doc_nom`) as doc_nom FROM `J_PAY` WHERE `paybox_id`='$paybox_id';");
                $doc_nom=$db->result($r,0,"doc_nom")+1;

                $r=$db->query("SELECT * FROM `J_SALE_INVOICE` WHERE `id`='$invoice_id';");
                $n=$db->num_rows($r);
                if ($n==1){
                    $invoice_summ=$db->result($r,0,"summ");
                    $invoice_summ_debit=$db->result($r,0,"summ_debit");
                    $invoice_doc_type_id=$db->result($r,0,"doc_type_id");
                    $invoice_client_id=$db->result($r,0,"client_conto_id");
                }

                $db->query("INSERT INTO `J_PAY` (`id`,`pay_type_id`,`paybox_id`,`doc_nom`,`client_id`,`cash_id`,`summ`,`user_id`) 
                VALUES ('$pay_id','$pay_type_id','$paybox_id','$doc_nom','$invoice_client_id','$cash_id','$kredit','$user_id');");

                $this->updatePayboxBalans($paybox_id,1,$cash_id,$kredit,$user_id,$pay_id);
            }

            if ($pay_id>0 && $kredit>0 && $pay_type_id==89 && $paybox_id>0){
                list($balans_before,)=$this->getClientGeneralSaldo($invoice_client_id);
                $doc_sum_pay=0;
                if ($doc_cash_id==$cash_id){$doc_sum_pay=$kredit;}

                if ($doc_cash_id!=$cash_id){
                    if ($doc_cash_id==1 && $cash_id==2){$doc_sum_pay=$cash_kours*$kredit;}
                    if ($doc_cash_id==1 && $cash_id==3){$doc_sum_pay=$cash_kours*$kredit;}
                    if ($doc_cash_id==2 && $cash_id==1){$doc_sum_pay=round($kredit/$cash_kours,2);}
                    if ($doc_cash_id==2 && $cash_id==3){$doc_sum_pay=round($kredit*$cash_kours,2);}
                    if ($doc_cash_id==3 && $cash_id==1){$doc_sum_pay=round($kredit/$cash_kours,2);}
                    if ($doc_cash_id==3 && $cash_id==2){$doc_sum_pay=round($kredit/$cash_kours,2);}
                }

                $balans_after=$balans_before+$doc_sum_pay;
                $db->query("INSERT INTO `B_CLIENT_BALANS_JOURNAL` (`client_id`,`cash_id`,`balans_before`,`deb_kre`,`summ`,`balans_after`,`doc_type_id`,`doc_id`,`pay_cash_id`,`pay_summ`) 
                VALUES ('$invoice_client_id','$doc_cash_id','$balans_before','2','$doc_sum_pay','$balans_after','2','$pay_id','$cash_id','$kredit');");
                $db->query("UPDATE `B_CLIENT_BALANS` SET `saldo`='$balans_after', `last_update`=NOW() WHERE `client_id`='$invoice_client_id';");

                if ($invoice_summ_debit>=$doc_sum_pay){ // if sum pay less then invoice summ
                    $db->query("INSERT INTO `J_PAY_STR` (`pay_id`,`parrent_doc_type_id`,`parrent_doc_id`,`summ_doc`,`doc_cash_id`,`summ_pay`,`pay_cash_id`,`pay_cash_kours`,`pay_cash_summ`) 
                    VALUES ('$pay_id','$invoice_doc_type_id','$invoice_id','$invoice_summ','$doc_cash_id','$doc_sum_pay','$cash_id','$cash_kours','$kredit');");
                    $new_summ_debit=$invoice_summ_debit-$doc_sum_pay;
                    if ($new_summ_debit<0){$new_summ_debit=0;}
                    $db->query("UPDATE `J_SALE_INVOICE` SET `summ_debit`='$new_summ_debit' WHERE `id`='$invoice_id' LIMIT 1;");
                    //$balans_after=$balans_before+$doc_sum_pay;
                    //$db->query("INSERT INTO B_CLIENT_BALANS_JOURNAL (`client_id`,`cash_id`,`balans_before`,`deb_kre`,`summ`,`balans_after`,`doc_type_id`,`doc_id`) VALUES ('$invoice_client_id','$doc_cash_id','$balans_before','2','$doc_sum_pay','$balans_after','2','$pay_id');");
                    //$db->query("UPDATE B_CLIENT_BALANS SET saldo=`saldo`+$doc_sum_pay, last_update=NOW() where client_id='$invoice_client_id';");
                }

                if ($invoice_summ_debit<$doc_sum_pay){ // if sum pay more then invoice summ

                    $avans_summ=$doc_sum_pay-$invoice_summ_debit;
                    $kredit2=$invoice_summ_debit;
                    /*
                    if ($doc_cash_id!=$cash_id){
                        if ($doc_cash_id==1 && $cash_id==2){$kredit2=round($kredit2/$cash_kours,2);}
                        if ($doc_cash_id==1 && $cash_id==3){$kredit2=round($kredit2/$cash_kours,2);}
                        if ($doc_cash_id==2 && $cash_id==1){$kredit2=round($kredit2*$cash_kours,2);}
                        if ($doc_cash_id==2 && $cash_id==3){$kredit2=round($kredit2/$cash_kours,2);}
                        if ($doc_cash_id==3 && $cash_id==1){$kredit2=round($kredit2*$cash_kours,2);}
                        if ($doc_cash_id==3 && $cash_id==2){$kredit2=round($kredit2*$cash_kours,2);}
                    }*/

                    /*  **updated
                        avans doc_cash_id = client_cash_id,
                        avans_summ = avans_summ(kours_val),
                        avans_cash_kours = kours_val,
                        updateClientAvans with client_cash_id
                    */

                    $rcl=$db->query("SELECT `cash_id` FROM `A_CLIENTS_CONDITIONS` WHERE `client_id`='$invoice_client_id' LIMIT 1;");
                    $ncl=$db->num_rows($rcl);
                    if ($ncl>0) $client_cash_id=$db->result($rcl,0,"cash_id"); else $client_cash_id=1;

                    $avans_cash_kours=1; // default UAH
                    list($usd_to_uah,$eur_to_uah)=$this->getKoursData();

                    if ($client_cash_id!=$cash_id){
                        if ($client_cash_id==1 && $cash_id==2){$avans_cash_kours=$usd_to_uah; $avans_summ=round($avans_summ/$usd_to_uah,2);}
                        if ($client_cash_id==1 && $cash_id==3){$avans_cash_kours=$eur_to_uah; $avans_summ=round($avans_summ/$eur_to_uah,2);}
                        if ($client_cash_id==2 && $cash_id==1){$avans_cash_kours=$usd_to_uah; $avans_summ=round($avans_summ*$usd_to_uah,2);}
                        if ($client_cash_id==2 && $cash_id==3){$avans_cash_kours=round($eur_to_uah/$usd_to_uah,2); $avans_summ=round($avans_summ*($eur_to_uah/$usd_to_uah),2);}
                        if ($client_cash_id==3 && $cash_id==1){$avans_cash_kours=$eur_to_uah; $avans_summ=round($avans_summ*$eur_to_uah,2);}
                        if ($client_cash_id==3 && $cash_id==2){$avans_cash_kours=round($usd_to_uah/$eur_to_uah,2); $avans_summ=round($avans_summ*($usd_to_uah/$eur_to_uah),2);}
                    }

                    $db->query("INSERT INTO `J_PAY_STR` (`pay_id`,`parrent_doc_type_id`,`parrent_doc_id`,`summ_doc`,`doc_cash_id`,`summ_pay`,`pay_cash_id`,`pay_cash_kours`,`pay_cash_summ`) 
                    VALUES ('$pay_id','$invoice_doc_type_id','$invoice_id','$invoice_summ','$doc_cash_id','$invoice_summ_debit','$cash_id','$cash_kours','$kredit2');");
                    $new_summ_debit=0;
                    $db->query("UPDATE `J_SALE_INVOICE` SET `summ_debit`='$new_summ_debit' where `id`='$invoice_id' LIMIT 1;");

                    $db->query("INSERT INTO `J_PAY_STR` (`pay_id`,`parrent_doc_type_id`,`parrent_doc_id`,`summ_doc`,`doc_cash_id`,`summ_pay`,`pay_cash_id`,`pay_cash_kours`,`pay_cash_summ`) 
                    VALUES ('$pay_id','98','$pay_id','$avans_summ','$client_cash_id','$avans_summ','$cash_id','$avans_cash_kours','$avans_summ');");

                    $this->updateClientAvans($invoice_client_id,$client_cash_id,$avans_summ);
                    //$db->query("INSERT INTO B_CLIENT_BALANS_JOURNAL (`client_id`,`cash_id`,`balans_before`,`deb_kre`,`summ`,`balans_after`,`doc_type_id`,`doc_id`) VALUES ('$invoice_client_id','$cash_id','$balans_before','2','$avans_summ','$balans_after','3','$pay_id');");
                    //$db->query("UPDATE B_CLIENT_BALANS SET saldo=`saldo`+$avans_summ, last_update=NOW() where client_id='$invoice_client_id';");
                }
                $answer=1;$err="";
            }
        }
        return array($answer,$err,$pay_id);
    }

    function saveJpayMoneyBackPay($client_id,$avans_debit,$pay_type_id,$paybox_id,$cash_id,$cash_kours,$doc_cash_id){$db=DbSingleton::getDb();
        $slave=new slave;
        session_start();
        $user_id=$_SESSION["media_user_id"];
        $answer=0;$err="Помилка збереження даних!";
        $client_id=$slave->qq($client_id);$pay_id=0;
        if ($client_id==0 || $client_id==""){
            $err="Не вказано клієнта";$answer=0;
        }
        if ($client_id>0 && $pay_id==0){
            $avans_debit=$slave->qq(str_replace(",",".",$avans_debit));$pay_type_id=$slave->qq($pay_type_id);$paybox_id=$slave->qq($paybox_id);
            $cash_id=$slave->qq($cash_id);$cash_kours=$slave->qq($cash_kours);$doc_cash_id=$slave->qq($doc_cash_id);

            list($avans_balans,)=$this->getClientBalansAvans($client_id);
            $doc_sum_back=0;
            if ($doc_cash_id==$cash_id){$doc_sum_back=$avans_debit;}
            if ($doc_cash_id!=$cash_id){
                if ($doc_cash_id==1 && $cash_id==2){$doc_sum_back=$cash_kours*$avans_debit;}
                if ($doc_cash_id==1 && $cash_id==3){$doc_sum_back=$cash_kours*$avans_debit;}
                if ($doc_cash_id==2 && $cash_id==1){$doc_sum_back=round($avans_debit/$cash_kours,2);}
                if ($doc_cash_id==2 && $cash_id==3){$doc_sum_back=round($avans_debit*$cash_kours,2);}
                if ($doc_cash_id==3 && $cash_id==1){$doc_sum_back=round($avans_debit/$cash_kours,2);}
                if ($doc_cash_id==3 && $cash_id==2){$doc_sum_back=round($avans_debit/$cash_kours,2);}
            }
            $paybox_saldo=$this->getPayBoxUserBalans($paybox_id,$user_id,$cash_id);

            if ($avans_balans<$doc_sum_back){$answer=0;$err="Помилка! Сума авансу менша за суму списання.";}
            if ($avans_balans>=$doc_sum_back){
                if ($paybox_saldo<$avans_debit){$answer=0;$err="Помилка! Залишок в касі менший за суму списання.";}
                if ($paybox_saldo>=$avans_debit){
                    $r=$db->query("select max(`id`) as mid from `J_PAY`;");
                    $pay_id=$db->result($r,0,"mid")+1;
                    $r=$db->query("select max(`doc_nom`) as doc_nom from `J_PAY` where `paybox_id`='$paybox_id';");
                    $doc_nom=$db->result($r,0,"doc_nom")+1;
                    $db->query("INSERT INTO `J_PAY` (`id`,`pay_type_id`,`paybox_id`,`doc_nom`,`client_id`,`cash_id`,`summ`,`user_id`) 
                    VALUES ('$pay_id','$pay_type_id','$paybox_id','$doc_nom','$client_id','$cash_id','$avans_debit','$user_id');");

                    $this->updatePayboxBalans($paybox_id,2,$cash_id,$avans_debit,$user_id,$pay_id);
                    $this->updateClientAvans($client_id,$doc_cash_id,"-$doc_sum_back");
                    list($balans_before,)=$this->getClientGeneralSaldo($client_id);

                    $balans_after=$balans_before-$doc_sum_back;
                    $db->query("INSERT INTO `B_CLIENT_BALANS_JOURNAL` (`client_id`,`cash_id`,`balans_before`,`deb_kre`,`summ`,`balans_after`,`doc_type_id`,`doc_id`,`pay_cash_id`,`pay_summ`) 
                    VALUES ('$client_id','$doc_cash_id','$balans_before','1','$doc_sum_back','$balans_after','2','$pay_id','$cash_id','$avans_debit');");
                    $db->query("UPDATE `B_CLIENT_BALANS` SET `saldo`='$balans_after', `last_update`=NOW() where `client_id`='$client_id';");
                    $db->query("INSERT INTO `J_PAY_STR` (`pay_id`,`parrent_doc_type_id`,`parrent_doc_id`,`summ_doc`,`doc_cash_id`,`summ_pay`,`pay_cash_id`,`pay_cash_kours`,`pay_cash_summ`) 
                    VALUES ('$pay_id','91','0','$avans_debit','$doc_cash_id','$doc_sum_back','$cash_id','$cash_kours','$avans_debit');");
                    $answer=1;$err="";
                }
            }
        }
        return array($answer, $err, $pay_id);
    }

    function getClientBalansAvans($sel_id) { $db=DbSingleton::getDb();
        $saldo="0";$cash_id=1;
        $r=$db->query("SELECT `saldo_avans`, `cash_id` FROM `B_CLIENT_AVANS` WHERE `client_id`='$sel_id' LIMIT 1;");
        $n=$db->num_rows($r);
        if ($n==1){
            $saldo=$db->result($r,0,"saldo_avans");
            $cash_id=$db->result($r,0,"cash_id");
        }
        return array($saldo, $cash_id);
    }

    function getClientGeneralSaldo($sel_id){$db=DbSingleton::getDb();
        $saldo="0";
        list($cash_id,,)=$this->getJpayClientDocCashId($sel_id);
        $r=$db->query("SELECT `saldo`, `cash_id` FROM `B_CLIENT_BALANS` WHERE `client_id`='$sel_id' LIMIT 1;");
        $n=$db->num_rows($r);
        if ($n==1){
            $saldo=$db->result($r,0,"saldo");
            $cash_id=$db->result($r,0,"cash_id");
        }
        if ($n==0){$db->query("INSERT INTO `B_CLIENT_BALANS` (`client_id`, `saldo`, `cash_id`) VALUES ('$sel_id',0,$cash_id);");}
        return array($saldo,$cash_id);
    }

    function getJpayClientDocCashId($sel_id){$db=DbSingleton::getDb();
        $cash_name="Гривня";$cash_id=1;$doc_type_id=0;
        $r=$db->query("SELECT `cash_id`, `doc_type_id` FROM `A_CLIENTS_CONDITIONS` WHERE `client_id`='$sel_id' LIMIT 1;");
        $n=$db->num_rows($r);
        if ($n==1){
            $cash_id=$db->result($r,0,"cash_id");
            $doc_type_id=$db->result($r,0,"doc_type_id");
            $cash_name=$this->getCashAbr($cash_id);
        }
        return array($cash_id,$cash_name,$doc_type_id);
    }

    function saveJpayAutoMoneyPay($client_id,$kredit_gbl,$pay_type_id,$paybox_id,$cash_id,$cash_kours,$doc_cash_id){$db=DbSingleton::getDb();
        $slave=new slave;
        session_start();
        $user_id=$_SESSION["media_user_id"];
        $answer=0;$err="Помилка збереження даних!";
        $client_id=$slave->qq($client_id);$kredit_gbl=$slave->qq($kredit_gbl);$cash_id=$slave->qq($cash_id);$cash_kours=$slave->qq($cash_kours);$doc_cash_id=$slave->qq($doc_cash_id);
        $doc_sum_pay=0;$pay_id=0;

        if ($client_id==0 || $client_id==""){
            $err="Не вказано клієнта для оплати";$answer=0;
        }

        if ($client_id>0 && $kredit_gbl>0){
            $pay_type_id=$slave->qq($pay_type_id);$paybox_id=$slave->qq($paybox_id);

            $r=$db->query("SELECT MAX(`id`) as mid FROM `J_PAY`;");$pay_id=$db->result($r,0,"mid")+1;
            $r=$db->query("SELECT MAX(`doc_nom`) as doc_nom FROM `J_PAY` WHERE `paybox_id`='$paybox_id';");
            $doc_nom=$db->result($r,0,"doc_nom")+1;

            $db->query("INSERT INTO `J_PAY` (`id`,`pay_type_id`,`paybox_id`,`doc_nom`,`client_id`,`cash_id`,`summ`,`user_id`) 
            VALUES ('$pay_id','$pay_type_id','$paybox_id','$doc_nom','$client_id','$cash_id','$kredit_gbl','$user_id');");

            $this->updatePayboxBalans($paybox_id,1,$cash_id,$kredit_gbl,$user_id,$pay_id);

            $ra=$db->query("SELECT * FROM `J_SALE_INVOICE` WHERE `client_conto_id`='$client_id' AND `summ_debit`>0 AND `status`='1' ORDER BY `data_pay` ASC;");
            $na=$db->num_rows($ra);

            for ($ia=1;$ia<=$na;$ia++){
                $invoice_id=$db->result($ra,$ia-1,"id");
                $invoice_summ=$db->result($ra,$ia-1,"summ");
                $invoice_summ_debit=$db->result($ra,$ia-1,"summ_debit");
                $invoice_doc_type_id=$db->result($ra,$ia-1,"doc_type_id");

                if ($ia==1){
                    if ($doc_cash_id==$cash_id){$doc_sum_pay=$kredit_gbl;}
                    if ($doc_cash_id!=$cash_id){
                        if ($doc_cash_id==1 && $cash_id==2){$doc_sum_pay=$cash_kours*$kredit_gbl;}
                        if ($doc_cash_id==1 && $cash_id==3){$doc_sum_pay=$cash_kours*$kredit_gbl;}
                        if ($doc_cash_id==2 && $cash_id==1){$doc_sum_pay=round($kredit_gbl/$cash_kours,2);}
                        if ($doc_cash_id==2 && $cash_id==3){$doc_sum_pay=round($kredit_gbl*$cash_kours,2);}
                        if ($doc_cash_id==3 && $cash_id==1){$doc_sum_pay=round($kredit_gbl/$cash_kours,2);}
                        if ($doc_cash_id==3 && $cash_id==2){$doc_sum_pay=round($kredit_gbl/$cash_kours,2);}
                    }

                    if ($doc_sum_pay>0){
                        list($balans_before,)=$this->getClientGeneralSaldo($client_id);
                        $balans_after=$balans_before+$doc_sum_pay;
                        $db->query("INSERT INTO `B_CLIENT_BALANS_JOURNAL` (`client_id`,`cash_id`,`balans_before`,`deb_kre`,`summ`,`balans_after`,`doc_type_id`,`doc_id`,`pay_cash_id`,`pay_summ`) 
                        VALUES ('$client_id','$doc_cash_id','$balans_before','2','$doc_sum_pay','$balans_after','2','$pay_id','$cash_id','$kredit_gbl');");
                        $db->query("UPDATE `B_CLIENT_BALANS` SET `saldo`=`saldo`+$doc_sum_pay, `last_update`=NOW() WHERE `client_id`='$client_id';");
                    }

                }

                if ($doc_sum_pay>0){
                    if ($pay_id>0 && $doc_sum_pay>0 && $paybox_id>0){

                        if ($invoice_summ_debit>=$doc_sum_pay){ // if sum pay less then invoice summ
                            $db->query("INSERT INTO `J_PAY_STR` (`pay_id`,`parrent_doc_type_id`,`parrent_doc_id`,`summ_doc`,`doc_cash_id`,`summ_pay`,`pay_cash_id`,`pay_cash_kours`,`pay_cash_summ`) 
                            VALUES ('$pay_id','$invoice_doc_type_id','$invoice_id','$invoice_summ','$doc_cash_id','$doc_sum_pay','$cash_id','$cash_kours','$doc_sum_pay');"); //$kredit_gbl
                            $new_summ_debit=$invoice_summ_debit-$doc_sum_pay;
                            if ($new_summ_debit<0){$new_summ_debit=0;}
                            $db->query("UPDATE `J_SALE_INVOICE` SET `summ_debit`='$new_summ_debit' WHERE `id`='$invoice_id' LIMIT 1;");
                            $doc_sum_pay=0;
                        }

                        if ($invoice_summ_debit<$doc_sum_pay && $doc_sum_pay>0){ // if sum pay more then invoice summ
                            $kredit2=$invoice_summ_debit; // summ of current document
                            if ($doc_cash_id!=$cash_id){
                                if ($doc_cash_id==1 && $cash_id==2){$kredit2=round($kredit2/$cash_kours,2);}
                                if ($doc_cash_id==1 && $cash_id==3){$kredit2=round($kredit2/$cash_kours,2);}
                                if ($doc_cash_id==2 && $cash_id==1){$kredit2=round($kredit2*$cash_kours,2);}
                                if ($doc_cash_id==2 && $cash_id==3){$kredit2=round($kredit2/$cash_kours,2);}
                                if ($doc_cash_id==3 && $cash_id==1){$kredit2=round($kredit2*$cash_kours,2);}
                                if ($doc_cash_id==3 && $cash_id==2){$kredit2=round($kredit2*$cash_kours,2);}
                            }
                            $db->query("INSERT INTO `J_PAY_STR` (`pay_id`,`parrent_doc_type_id`,`parrent_doc_id`,`summ_doc`,`doc_cash_id`,`summ_pay`,`pay_cash_id`,`pay_cash_kours`,`pay_cash_summ`)
                            VALUES ('$pay_id','$invoice_doc_type_id','$invoice_id','$invoice_summ','$doc_cash_id','$invoice_summ_debit','$cash_id','$cash_kours','$kredit2');");
                            $new_summ_debit=0;
                            $db->query("UPDATE `J_SALE_INVOICE` SET `summ_debit`='$new_summ_debit' WHERE `id`='$invoice_id' LIMIT 1;");
                            $doc_sum_pay-=$invoice_summ_debit;
                        }
                    }
                }
            }
            //CHECK IF AVANS
            if ($na>0 && $doc_sum_pay>0){
                $avans_summ=$doc_sum_pay;
                $db->query("INSERT INTO `J_PAY_STR` (`pay_id`,`parrent_doc_type_id`,`parrent_doc_id`,`summ_doc`,`doc_cash_id`,`summ_pay`,`pay_cash_id`,`pay_cash_kours`,`pay_cash_summ`) 
                VALUES ('$pay_id','98','$pay_id','$avans_summ','$doc_cash_id','$avans_summ','$cash_id','1','$avans_summ');");
                $this->updateClientAvans($client_id,$doc_cash_id,$avans_summ);
            }
            $answer=1;$err="";
        }
        return array($answer,$err,$pay_id);
    }

    function saveJpayAvansPay($client_id,$kredit_gbl,$pay_type_id,$paybox_id,$cash_id,$cash_kours,$doc_cash_id){$db=DbSingleton::getDb();
        $slave=new slave;
        session_start();
        $user_id=$_SESSION["media_user_id"];
        $answer=0;$err="Помилка збереження даних!";
        $client_id=$slave->qq($client_id);$kredit_gbl=$slave->qq($kredit_gbl);$cash_id=$slave->qq($cash_id);$cash_kours=$slave->qq($cash_kours);$doc_cash_id=$slave->qq($doc_cash_id);$pay_id=0;
        if ($client_id==0 || $client_id==""){
            $err="Не вказано клієнта для авансу";$answer=0;
        }
        if ($client_id>0 && $kredit_gbl>0){
            $pay_type_id=$slave->qq($pay_type_id);$paybox_id=$slave->qq($paybox_id);
            list($balans_before,)=$this->getClientGeneralSaldo($client_id);

            $r=$db->query("SELECT MAX(`id`) as mid FROM `J_PAY`;");
            $pay_id=$db->result($r,0,"mid")+1;
            $r=$db->query("SELECT MAX(`doc_nom`) as doc_nom FROM `J_PAY` WHERE `paybox_id`='$paybox_id';");
            $doc_nom=$db->result($r,0,"doc_nom")+1;

            $db->query("INSERT INTO `J_PAY` (`id`,`pay_type_id`,`paybox_id`,`doc_nom`,`client_id`,`cash_id`,`summ`,`user_id`) 
            VALUES ('$pay_id','$pay_type_id','$paybox_id','$doc_nom','$client_id','$cash_id','$kredit_gbl','$user_id');");

            $this->updatePayboxBalans($paybox_id,1,$cash_id,$kredit_gbl,$user_id,$pay_id);

            $avans_summ=$kredit_gbl;
            if ($avans_summ>0){ //creating avans pay
                if ($doc_cash_id!=$cash_id){
                    if ($doc_cash_id==1 && $cash_id==2){$avans_summ=round($avans_summ*$cash_kours,2);}
                    if ($doc_cash_id==1 && $cash_id==3){$avans_summ=round($avans_summ*$cash_kours,2);}
                    if ($doc_cash_id==2 && $cash_id==1){$avans_summ=round($avans_summ/$cash_kours,2);}
                    if ($doc_cash_id==2 && $cash_id==3){$avans_summ=round($avans_summ*$cash_kours,2);}
                    if ($doc_cash_id==3 && $cash_id==1){$avans_summ=round($avans_summ/$cash_kours,2);}
                    if ($doc_cash_id==3 && $cash_id==2){$avans_summ=round($avans_summ/$cash_kours,2);}
                }

                $db->query("INSERT INTO `J_PAY_STR` (`pay_id`,`parrent_doc_type_id`,`parrent_doc_id`,`summ_doc`,`doc_cash_id`,`summ_pay`,`pay_cash_id`,`pay_cash_kours`,`pay_cash_summ`) 
                VALUES ('$pay_id','98','$pay_id','$avans_summ','$cash_id','$avans_summ','$cash_id','1','$avans_summ');");

                $balans_after=$balans_before+$avans_summ;

                $db->query("INSERT INTO `B_CLIENT_BALANS_JOURNAL` (`client_id`,`cash_id`,`balans_before`,`deb_kre`,`summ`,`balans_after`,`doc_type_id`,`doc_id`,`pay_cash_id`,`pay_summ`) 
                VALUES ('$client_id','$doc_cash_id','$balans_before','2','$avans_summ','$balans_after','3','$pay_id','$cash_id','$kredit_gbl');");

                $db->query("UPDATE `B_CLIENT_BALANS` SET saldo=`saldo`+$avans_summ, `last_update`=NOW() WHERE `client_id`='$client_id';");
                $r3=$db->query("SELECT `id` FROM `B_CLIENT_BALANS` WHERE `client_id`='$client_id';");
                $n3=$db->num_rows($r3);
                if ($n3==0){$db->query("INSERT INTO `B_CLIENT_BALANS` (`client_id`, `saldo`, `cash_id`) VALUES ('$client_id', '$avans_summ', '$cash_id');");}

                $this->updateClientAvans($client_id,$doc_cash_id,$avans_summ);
            }
            $answer=1;$err="";
        }
        return array($answer,$err,$pay_id);
    }

    function saveJpayAvansMoneyPay($client_id,$kredit_gbl,$pay_type_id,$doc_cash_id){$db=DbSingleton::getDb();
        $slave=new slave;
        session_start();
        $user_id=$_SESSION["media_user_id"];
        $answer=0;$err="Помилка збереження даних!";
        $client_id=$slave->qq($client_id);$kredit_gbl=$slave->qq($kredit_gbl);$doc_cash_id=$slave->qq($doc_cash_id);$doc_sum_pay=0;$pay_id=0;
        if ($client_id==0 || $client_id==""){
            $err="Не вказано клієнта для оплати";$answer=0;
        }
        if ($client_id>0 && $kredit_gbl>0){
            $pay_type_id=$slave->qq($pay_type_id);
            $r=$db->query("SELECT MAX(`id`) as mid FROM `J_PAY`;");
            $pay_id=$db->result($r,0,"mid")+1;
            $r=$db->query("SELECT MAX(`doc_nom`) as doc_nom FROM `J_PAY` WHERE `paybox_id`='0' AND `pay_type_id`=99;");
            $doc_nom=$db->result($r,0,"doc_nom")+1;

            $db->query("INSERT INTO `J_PAY` (`id`,`pay_type_id`,`doc_nom`,`client_id`,`cash_id`,`summ`,`user_id`) 
            VALUES ('$pay_id','$pay_type_id','$doc_nom','$client_id','$doc_cash_id','$kredit_gbl','$user_id');");

            list($balans_before,)=$this->getClientGeneralSaldo($client_id);
            $ra=$db->query("SELECT * FROM `J_SALE_INVOICE` WHERE `client_conto_id`='$client_id' AND `summ_debit`>0 AND `status`='1' ORDER BY `data_pay` ASC;");
            $na=$db->num_rows($ra);
            for ($ia=1;$ia<=$na;$ia++){
                $invoice_id=$db->result($ra,$ia-1,"id");
                $invoice_summ=$db->result($ra,$ia-1,"summ");
                $invoice_summ_debit=$db->result($ra,$ia-1,"summ_debit");
                $invoice_doc_type_id=$db->result($ra,$ia-1,"doc_type_id");
                if ($ia==1){
                    $doc_sum_pay=$kredit_gbl;
                }
                if ($pay_id>0 && $doc_sum_pay>0){
                    if ($invoice_summ_debit>=$doc_sum_pay){ // if sum pay less then invoice summ
                        $db->query("INSERT INTO `J_PAY_STR` (`pay_id`,`parrent_doc_type_id`,`parrent_doc_id`,`summ_doc`,`doc_cash_id`,`summ_pay`,`pay_cash_id`,`pay_cash_kours`,`pay_cash_summ`) 
                        VALUES ('$pay_id','$invoice_doc_type_id','$invoice_id','$invoice_summ','$doc_cash_id','$doc_sum_pay','$doc_cash_id','1','$doc_sum_pay');");
                        $new_summ_debit=$invoice_summ_debit-$doc_sum_pay;
                        if ($new_summ_debit<0){$new_summ_debit=0;}
                        $db->query("UPDATE `J_SALE_INVOICE` SET `summ_debit`='$new_summ_debit' WHERE `id`='$invoice_id' LIMIT 1;");
                        //$balans_after=$balans_before;//+$doc_sum_pay;
                        $db->query("INSERT INTO `B_CLIENT_BALANS_JOURNAL` (`client_id`,`cash_id`,`balans_before`,`deb_kre`,`summ`,`balans_after`,`doc_type_id`,`doc_id`) 
                        VALUES ('$client_id','$doc_cash_id','$balans_before','2','$doc_sum_pay','$balans_before','2','$pay_id');");
                        //$db->query("UPDATE B_CLIENT_BALANS SET saldo=`saldo`-$doc_sum_pay, last_update=NOW() where client_id='$client_id';");
                        //$this->updateClientBalans($client_id,$balans_before_cash_id,$doc_sum_pay);
                        $this->updateClientAvans($client_id,$doc_cash_id,$doc_sum_pay*-1);
                        $doc_sum_pay=0;
                    }
                    if ($invoice_summ_debit<$doc_sum_pay){ //if sum pay more then invoice summ
                        //$kredit2=$invoice_summ_debit; //summ of current document
                        $db->query("INSERT INTO `J_PAY_STR` (`pay_id`,`parrent_doc_type_id`,`parrent_doc_id`,`summ_doc`,`doc_cash_id`,`summ_pay`,`pay_cash_id`,`pay_cash_kours`,`pay_cash_summ`) 
                        VALUES ('$pay_id','$invoice_doc_type_id','$invoice_id','$invoice_summ','$doc_cash_id','$invoice_summ_debit','$doc_cash_id','1','$invoice_summ_debit');");
                        $new_summ_debit=0;
                        $db->query("UPDATE `J_SALE_INVOICE` SET `summ_debit`='$new_summ_debit' WHERE `id`='$invoice_id' LIMIT 1;");

                        //$balans_after=$balans_before;//+$invoice_summ_debit;
                        $db->query("INSERT INTO `B_CLIENT_BALANS_JOURNAL` (`client_id`,`cash_id`,`balans_before`,`deb_kre`,`summ`,`balans_after`,`doc_type_id`,`doc_id`) 
                        VALUES ('$client_id','$doc_cash_id','$balans_before','2','$invoice_summ_debit','$balans_before','2','$pay_id');");
                        //$db->query("UPDATE B_CLIENT_BALANS SET saldo=`saldo`-$invoice_summ_debit, last_update=NOW() where client_id='$client_id';");
                        $doc_sum_pay-=$invoice_summ_debit;
                        $this->updateClientAvans($client_id,$doc_cash_id,$invoice_summ_debit*-1);
                    }
                }
            }
            $answer=1;$err="";
        }
        return array($answer,$err,$pay_id);
    }

    function updatePayboxBalans($paybox_id,$deb_kre,$cash_id,$summ,$user_id,$jpay_id){$db=DbSingleton::getDb();
        $r=$db->query("SELECT COUNT(`id`) as kol FROM `B_PAYBOX_BALANS` WHERE `paybox_id`='$paybox_id' AND `cash_id`='$cash_id' AND `user_id`='$user_id';");
        $ex=$db->result($r,0,"kol");
        if ($deb_kre==2){ $summ=$summ*-1; }
        if ($ex==0){
            $db->query("INSERT INTO `B_PAYBOX_BALANS` (`paybox_id`,`saldo`,`cash_id`,`user_id`) VALUES ('$paybox_id','$summ','$cash_id','$user_id');");
        }
        if ($ex>0){
            $db->query("UPDATE `B_PAYBOX_BALANS` SET `saldo`=saldo+$summ WHERE `paybox_id`='$paybox_id' AND `cash_id`='$cash_id' AND `user_id`='$user_id' LIMIT 1;");
        }
        $r=$db->query("SELECT * FROM `B_PAYBOX_JOURNAL` WHERE `paybox_id`='$paybox_id' AND `cash_id`='$cash_id' AND `user_id`='$user_id' ORDER BY `id` DESC LIMIT 1;");
        $n=$db->num_rows($r);
        $saldo_before=0;
        if ($n==1){
            $saldo_before=$db->result($r,0,"saldo_after");
        }
        $sald_after=round($saldo_before+$summ,2);
        $db->query("INSERT INTO `B_PAYBOX_JOURNAL` (`paybox_id`,`user_id`,`saldo_before`,`amount`,`saldo_after`,`cash_id`,`jpay_id`) 
        VALUES ('$paybox_id','$user_id','$saldo_before','$summ','$sald_after','$cash_id','$jpay_id');");
        return true;
    }

    function updateClientAvans($client_id,$cash_id,$summ){$db=DbSingleton::getDb();
        $r=$db->query("SELECT COUNT(`id`) as kol FROM `B_CLIENT_AVANS` WHERE `client_id`='$client_id' AND `cash_id`='$cash_id';");
        $ex=$db->result($r,0,"kol");
        if ($ex==0){
            $db->query("INSERT INTO `B_CLIENT_AVANS` (`client_id`,`saldo_avans`,`cash_id`) VALUES ('$client_id','$summ','$cash_id');");
        }
        if ($ex>0){
            $db->query("UPDATE `B_CLIENT_AVANS` SET `saldo_avans`=saldo_avans+$summ WHERE `client_id`='$client_id' AND `cash_id`='$cash_id' LIMIT 1;");
        }
        return true;
    }

}