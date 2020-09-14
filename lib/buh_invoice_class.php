<?php

class buh_invoice {

    function show_sale_invoice_list(){$db=DbSingleton::getDb();
        $gmanual=new gmanual; $media_users=new media_users;
        $form="";$form_htm=RD."/tpl/buh_invoice_range.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $data_cur=date("Y-m-d"); $summ_uah=$summ_usd=$summ_eur=0; $sales=[];$list="";
        $where=" AND sv.time_stamp>='$data_cur 00:00:00' AND sv.time_stamp<='$data_cur 23:59:59'";

        $r=$db->query("SELECT sv.*, dp.prefix as dp_prefix, dp.doc_nom as dp_nom, t.name as tpoint_name, sl.name as seller_name, cl.name as client_name, dt.mvalue as doc_type_name, ch.abr2 as cash_abr 
        FROM `J_SALE_INVOICE` sv
            LEFT OUTER JOIN `J_DP` dp on dp.id=sv.dp_id
            LEFT OUTER JOIN `CASH` ch on ch.id=sv.cash_id
            LEFT OUTER JOIN `T_POINT` t on t.id=sv.tpoint_id
            LEFT OUTER JOIN `A_CLIENTS` sl on sl.id=sv.seller_id
            LEFT OUTER JOIN `A_CLIENTS` cl on cl.id=sv.client_conto_id
            LEFT OUTER JOIN `manual` dt on dt.key='client_sale_type' AND dt.id=sv.doc_type_id
        WHERE sv.status=1 $where AND sv.doc_type_id=61 ORDER BY sv.time_stamp DESC, sv.status_invoice ASC, sv.data_create DESC, sv.prefix ASC, sv.id DESC;");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id"); array_push($sales,$id);
            $dp_nom=$db->result($r,$i-1,"dp_prefix").$db->result($r,$i-1,"dp_nom");
            $prefix=$db->result($r,$i-1,"prefix");
            $doc_nom=$db->result($r,$i-1,"doc_nom");
            $time_stamp=$db->result($r,$i-1,"time_stamp");
            $tpoint_name=$db->result($r,$i-1,"tpoint_name");
            $seller_name=$db->result($r,$i-1,"seller_name");
            $client_name=$db->result($r,$i-1,"client_name");
            $doc_type_name=$db->result($r,$i-1,"doc_type_name");
            $summ=$db->result($r,$i-1,"summ");
            $summ_debit=$db->result($r,$i-1,"summ_debit");
            $cash_id=$db->result($r,$i-1,"cash_id");
            $cash_abr=$db->result($r,$i-1,"cash_abr");
            $data_pay=$db->result($r,$i-1,"data_pay");
            $user_name=$media_users->getMediaUserName($db->result($r,$i-1,"user_id"));
            $status_select=$db->result($r,$i-1,"status_select");
            $status_select_cap=$gmanual->get_gmanual_caption($status_select);

            if ($cash_id==1) $summ_uah+=$summ; if ($cash_id==2) $summ_usd+=$summ; if ($cash_id==3) $summ_eur+=$summ;
            if ($summ_debit==0) $summ_cap=""; else $summ_cap="$summ_debit $cash_abr";
            $summ_pdv=round($summ/6,2);

            $list.="<tr id='strStsRow_$i' style='cursor:pointer' align='center' onClick='showSaleInvoiceCard(\"$id\");'>
                <td >$i</td>
                <td>$prefix-$doc_nom</td>
                <td align='center'>$time_stamp</td>
                <td>$dp_nom</td>
                <td>$tpoint_name</td>
                <td align='left'>$seller_name</td>
                <td align='left'>$client_name</td>
                <td>$doc_type_name</td>
                <td align='center' style='min-width:80px;'>$summ $cash_abr</td>
                <td align='right'>$summ_cap</td>
                <td align='right'>$summ_pdv $cash_abr</td>
                <td align='right'>$data_pay</td>
                <td align='left'>$user_name</td>
                <td align='center'>$status_select_cap</td>
            </tr>";
        }
        $form=str_replace("{sale_invoice_range}",$list,$form);
        $form=str_replace("{sale_invoice_summ}","$summ_uah UAH / $summ_usd USD / $summ_eur EUR",$form);
        return $form;
    }

    function show_sale_invoice_list_filter($data_start,$data_end){$db=DbSingleton::getDb();
        $gmanual=new gmanual; $media_users=new media_users;
        $data_cur=date("Y-m-d"); $summ_uah=$summ_usd=$summ_eur=0; $list="";
        if ($data_start!='' && $data_end!='') $where=" AND sv.time_stamp>='$data_start 00:00:00' AND sv.time_stamp<='$data_end 23:59:59' AND sv.doc_type_id=61"; else
        $where=" AND sv.time_stamp>='$data_cur 00:00:00' AND sv.time_stamp<='$data_cur 23:59:59' AND sv.doc_type_id=61";

        $r=$db->query("SELECT sv.*, dp.prefix as dp_prefix, dp.doc_nom as dp_nom, t.name as tpoint_name, sl.name as seller_name, cl.name as client_name, dt.mvalue as doc_type_name, ch.abr2 as cash_abr 
        FROM `J_SALE_INVOICE` sv
            LEFT OUTER JOIN `J_DP` dp on dp.id=sv.dp_id
            LEFT OUTER JOIN `CASH` ch on ch.id=sv.cash_id
            LEFT OUTER JOIN `T_POINT` t on t.id=sv.tpoint_id
            LEFT OUTER JOIN `A_CLIENTS` sl on sl.id=sv.seller_id
            LEFT OUTER JOIN `A_CLIENTS` cl on cl.id=sv.client_conto_id
            LEFT OUTER JOIN `manual` dt on dt.key='client_sale_type' AND dt.id=sv.doc_type_id
        WHERE sv.status=1 $where ORDER BY sv.time_stamp DESC, sv.status_invoice ASC, sv.data_create DESC, sv.prefix ASC, sv.id DESC;"); $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $dp_nom=$db->result($r,$i-1,"dp_prefix").$db->result($r,$i-1,"dp_nom");
            $prefix=$db->result($r,$i-1,"prefix");
            $doc_nom=$db->result($r,$i-1,"doc_nom");
            $time_stamp=$db->result($r,$i-1,"time_stamp");
            $tpoint_name=$db->result($r,$i-1,"tpoint_name");
            $seller_name=$db->result($r,$i-1,"seller_name");
            $client_name=$db->result($r,$i-1,"client_name");
            $doc_type_name=$db->result($r,$i-1,"doc_type_name");
            $summ=$db->result($r,$i-1,"summ");
            $summ_debit=$db->result($r,$i-1,"summ_debit");
            $cash_id=$db->result($r,$i-1,"cash_id");
            $cash_abr=$db->result($r,$i-1,"cash_abr");
            $data_pay=$db->result($r,$i-1,"data_pay");
            $user_name=$media_users->getMediaUserName($db->result($r,$i-1,"user_id"));
            $status_select=$db->result($r,$i-1,"status_select");
            $status_select_cap=$gmanual->get_gmanual_caption($status_select);

            if ($cash_id==1) $summ_uah+=$summ; if ($cash_id==2) $summ_usd+=$summ; if ($cash_id==3) $summ_eur+=$summ;
            if ($summ_debit==0) $summ_cap=""; else $summ_cap="$summ_debit $cash_abr";
            $summ_pdv=round($summ/6,2);

            $list.="<tr id='strStsRow_$i' style='cursor:pointer' align='center' onClick='showSaleInvoiceCard(\"$id\");'>
                <td>$i</td>
                <td>$prefix-$doc_nom</td>
                <td align='center'>$time_stamp</td>
                <td>$dp_nom</td>
                <td>$tpoint_name</td>
                <td align='left'>$seller_name</td>
                <td align='left'>$client_name</td>
                <td>$doc_type_name</td>
                <td align='center' style='min-width:80px;'>$summ $cash_abr</td>
                <td align='right'>$summ_cap</td>
                <td align='right'>$summ_pdv $cash_abr</td>
                <td align='right'>$data_pay</td>
                <td align='left'>$user_name</td>
                <td align='center'>$status_select_cap</td>
            </tr>";
        }
        $summ_price="$summ_uah UAH / $summ_usd USD / $summ_eur EUR";
        return array($list,$summ_price);
    }

    function checkTaxExist($invoice_id){$db=DbSingleton::getDb();
        $r=$db->query("SELECT `id` FROM `J_TAX_INVOICE` WHERE `sale_invoice_id`='$invoice_id' AND `status`=1 LIMIT 1;");$n=$db->num_rows($r);$tax_id=0;
        if ($n==1){ $tax_id=$db->result($r,0,"id"); }
        return $tax_id;
    }

    function createTaxInvoice($invoice_id){$db=DbSingleton::getDb();
        $cat=new catalogue;
        session_start();$user_id=$_SESSION["media_user_id"];
        $tax_id=0;$answer=0;$err="Помилка!";
        $r=$db->query("SELECT * FROM `J_SALE_INVOICE` WHERE `id`='$invoice_id' LIMIT 1"); $n=$db->num_rows($r);
        if ($n==1){
            $seller_id=$db->result($r,0,"seller_id");
            $client_id=$db->result($r,0,"client_conto_id");
            $tpoint_id=$db->result($r,0,"tpoint_id");
            $cash_id=$db->result($r,0,"cash_id");
            $summ=$db->result($r,0,"summ");

            $rt=$db->query("SELECT MAX(`id`) as mid FROM `J_TAX_INVOICE`;");$tax_id=0+$db->result($rt,0,"mid")+1;
            $rt=$db->query("SELECT MAX(`doc_nom`) as mid FROM `J_TAX_INVOICE` WHERE `seller_id`='$seller_id';");$tax_nom=0+$db->result($rt,0,"mid")+1;
            $db->query("INSERT INTO `J_TAX_INVOICE` (`id`,`tax_type_id`,`doc_nom`,`data_create`,`sale_invoice_id`,`tpoint_id`,`seller_id`,`client_id`,`cash_id`,`summ`,`user_id`) 
            VALUES ('$tax_id','160','$tax_nom',CURDATE(),'$invoice_id','$tpoint_id','$seller_id','$client_id','$cash_id','$summ','$user_id');");

            $r1=$db->query("SELECT * FROM `J_SALE_INVOICE_STR` WHERE `invoice_id`='$invoice_id' ORDER BY `id` ASC;");$n1=$db->num_rows($r1);
            for ($i=1;$i<=$n1;$i++){
                $art_id=$db->result($r1,$i-1,"art_id");
                $amount=$db->result($r1,$i-1,"amount");
                $price=$db->result($r1,$i-1,"price_end");
                $summ=$db->result($r1,$i-1,"summ");
                $zed=$cat->getArticleZED($art_id);$art_name=$cat->getArticleNameLang($art_id);

                $db->query("INSERT INTO `J_TAX_INVOICE_STR` (`tax_id`,`zed`,`art_id`,`goods_name`,`amount`,`price`,`summ`) 
                VALUES ('$tax_id','$zed','$art_id','$art_name','$amount','$price','$summ');");
            }
            $answer=1;$err="";
        }
        return array($answer,$err,$tax_id);
    }

    function showSaleInvoiceCard($invoice_id){$db=DbSingleton::getDb();
        $cat=new catalogue;$list="";$prefix="";$doc_nom=0;
        $form="";$form_htm=RD."/tpl/sale_invoice_card.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}

        $r=$db->query("SELECT sv.*, t.name as tpoint_name, sl.name as seller_name, cl.name as client_name, dt.mcaption as doc_type_name, dt.mvalue as doc_type_abr, ch.abr2 as cash_abr 
        FROM `J_SALE_INVOICE` sv
            LEFT OUTER JOIN `CASH` ch on ch.id=sv.cash_id
            LEFT OUTER JOIN `T_POINT` t on t.id=sv.tpoint_id
            LEFT OUTER JOIN `A_CLIENTS` sl on sl.id=sv.seller_id
            LEFT OUTER JOIN `A_CLIENTS` cl on cl.id=sv.client_conto_id
            LEFT OUTER JOIN `manual` dt on dt.key='client_sale_type' AND dt.id=sv.doc_type_id
        WHERE sv.status=1 AND sv.id='$invoice_id' LIMIT 1;");$n=$db->num_rows($r);

        if ($n==0){$form_htm=RD."/tpl/access_deny.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);} }
        if ($n==1){
            $prefix=$db->result($r,0,"prefix");
            $doc_nom=$db->result($r,0,"doc_nom");
            $data_create=$db->result($r,0,"data_create");
            $tpoint_name=$db->result($r,0,"tpoint_name");
            $seller_name=$db->result($r,0,"seller_name");
            $client_name=$db->result($r,0,"client_name");
            $doc_type_id=$db->result($r,0,"doc_type_id");
            $doc_type_name=$db->result($r,0,"doc_type_name");
            $summ=$db->result($r,0,"summ");
            $cash_abr=$db->result($r,0,"cash_abr");
            $data_pay=$db->result($r,0,"data_pay");

            $form=str_replace("{invoice_id}",$invoice_id,$form);
            $form=str_replace("{data}",$data_create,$form);
            $form=str_replace("{data_pay}",$data_pay,$form);
            $form=str_replace("{prefix}",$prefix,$form);
            $form=str_replace("{doc_nom}",$doc_nom,$form);
            $form=str_replace("{tpoint_name}",$tpoint_name,$form);
            $form=str_replace("{seller_name}",$seller_name,$form);
            $form=str_replace("{client_name}",$client_name,$form);
            $form=str_replace("{doc_type_name}",$doc_type_name,$form);
            $form=str_replace("{invoice_summ}",$summ,$form);
            $form=str_replace("{cash_name}",$cash_abr,$form);
            $form=str_replace("{volume}",0,$form);

            $tax_hidden=" hidden";
            if ($doc_type_id=="61") {
                $form=str_replace("{oper_visible}","",$form);
                $tax_hidden=""; if ($this->checkTaxExist($invoice_id)>0){ $tax_hidden=" hidden"; }
            } else {$form=str_replace("{oper_visible}"," disabled style=\"display:none;\"",$form); }

            if ($doc_type_id==64) {$style_doc_id="style='display:none;'";} else {$style_doc_id="";}
            $form=str_replace("{style_doc_id}",$style_doc_id,$form);
            $form=str_replace("{hidden_tax}",$tax_hidden,$form);

            $r=$db->query("SELECT * FROM `J_SALE_INVOICE_STR` WHERE `invoice_id`='$invoice_id' ORDER BY `id` ASC;");$n=$db->num_rows($r);
            for ($i=1;$i<=$n;$i++){
                $article_nr_displ=$db->result($r,$i-1,"article_nr_displ");
                $brand_id=$db->result($r,$i-1,"brand_id");$brand_name=$cat->getBrandName($brand_id);
                $amount=$db->result($r,$i-1,"amount");
                $price=$db->result($r,$i-1,"price");
                $price_end=$db->result($r,$i-1,"price_end");
                $discount=$db->result($r,$i-1,"discount");
                $summ=$db->result($r,$i-1,"summ");

                $list.="<tr align='right'>
                    <td align='left'>$i</td>
                    <td align='left'>$article_nr_displ</td>
                    <td align='center'>$brand_name</td>
                    <td>$amount</td>
                    <td>$price</td>
                    <td>$discount</td>
                    <td>$price_end</td>
                    <td>$summ</td>
                </tr>";
            }
            $form=str_replace("{sale_invoice_str_list}",$list,$form);
        }
        return array($form,"$prefix-$doc_nom");
    }

    /*==============================================*/

    function getBuhIncomeList() { $db=DbSingleton::getDb();
        $media_users=new media_users;
        $r=$db->query("SELECT * FROM `J_BUH_INCOME` WHERE 1;"); $n=$db->num_rows($r); $list="";
        for ($i=1;$i<=$n;$i++) {
            $id=$db->result($r,$i-1,"ID");
            $text=$db->result($r,$i-1,"TEXT");
            $pay_id=$db->result($r,$i-1,"PAY_ID");
            $cash_id=$db->result($r,$i-1,"CASH_ID");
            $state_id=$db->result($r,$i-1,"STATE_ID");
            $summ=$db->result($r,$i-1,"SUMM");
            $user_id=$db->result($r,$i-1,"USER_ID");
            $data=$db->result($r,$i-1,"DATA");
            list($pay_name, $cash_name, $state_name) = $this->getBuhIncomeValues($pay_id, $cash_id, $state_id);
            $user_name=$media_users->getMediaUserName($user_id);
            $list.="<tr style='cursor: pointer' onclick=\"showBuhIncomeCard($id);\">
                <td>$id</td>
                <td>$text</td>
                <td>$pay_name</td>
                <td>$cash_name</td>
                <td>$state_name</td>
                <td>$summ</td>
                <td>$user_name</td>
                <td>$data</td>
            </tr>";
        }
        return $list;
    }

    function showBuhIncomeCard($buh_income_id) { $db=DbSingleton::getDb();
        $media_users=new media_users;
        session_start();$media_user_id=$_SESSION["media_user_id"];
        $form_htm=RD."/tpl/buh_income_card.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("SELECT * FROM `J_BUH_INCOME` WHERE `ID`='$buh_income_id' LIMIT 1;"); $n=$db->num_rows($r);
        if ($n==0) {
            $text="";
            $pay_id=0;
            $cash_id=0;
            $state_id=0;
            $summ=0;
            $user_id=$media_user_id;
            $data=date("Y-m-d H:i:s");
            $status=1;
            $disabled="disabled";
        } else {
            $text=$db->result($r,0,"TEXT");
            $pay_id=$db->result($r,0,"PAY_ID");
            $cash_id=$db->result($r,0,"CASH_ID");
            $state_id=$db->result($r,0,"STATE_ID");
            $summ=$db->result($r,0,"SUMM");
            $user_id=$db->result($r,0,"USER_ID");
            $data=$db->result($r,0,"DATA");
            $status=$db->result($r,0,"STATUS");
            $disabled="disabled";
        }
        $form=str_replace("{buh_income_id}", $buh_income_id, $form);
        $form=str_replace("{buh_income_text}", $text, $form);
        $form=str_replace("{buh_income_pay_id}", $this->getPayboxList($pay_id), $form);
        $form=str_replace("{buh_income_cash_id}", $this->getCashList($cash_id), $form);
        $form=str_replace("{buh_income_state_id}", $this->getStateIncome($state_id), $form);
        $form=str_replace("{buh_income_summ}", $summ, $form);
        $form=str_replace("{buh_income_user_id}", $user_id, $form);
        $form=str_replace("{buh_income_user_name}", $user_name=$media_users->getMediaUserName($user_id), $form);
        $form=str_replace("{buh_income_data}", $data, $form);
        $form=str_replace("{buh_income_status}", $status ? "checked='checked'" : "", $form);
        $form=str_replace("{drop_disabled}", $disabled, $form);
        return $form;
    }

    function saveBuhIncomeCard($buh_income_id, $text, $pay_id, $cash_id, $state_id, $summ, $user_id, $data, $status) { $db=DbSingleton::getDb();
        $answer=0;$err="Помилка!";
        if ($buh_income_id>0) {
            $db->query("UPDATE `J_BUH_INCOME` SET `PAY_ID`='$pay_id', `TEXT`='$text', `CASH_ID`='$cash_id', `STATE_ID`='$state_id', `SUMM`='$summ', `USER_ID`='$user_id', `DATA`='$data', `STATUS`='$status'
            WHERE `ID`='$buh_income_id' LIMIT 1;");
            $this->updatePayboxBalans($buh_income_id, $pay_id, $cash_id, $summ, $user_id);
            $answer=1;$err="";
        } elseif ($buh_income_id==0) {
            $r=$db->query("SELECT MAX(`ID`) as mid FROM `J_BUH_INCOME`;");$buh_income_id=0+$db->result($r,0,"mid")+1;
            $db->query("INSERT INTO `J_BUH_INCOME` (`ID`, `TEXT`, `PAY_ID`, `CASH_ID`, `STATE_ID`, `SUMM`, `USER_ID`, `DATA`, `STATUS`) 
            VALUES ('$buh_income_id', '$text', '$pay_id', '$cash_id', '$state_id', '$summ', '$user_id', '$data', '$status');");
            $this->updatePayboxBalans($buh_income_id, $pay_id, $cash_id, $summ, $user_id);
            $answer=1;$err="";
        }
        return array($answer, $err);
    }

    function updatePayboxBalans($buh_income_id, $paybox_id, $cash_id, $summ, $user_id) { $db=DbSingleton::getDb();
        $r=$db->query("SELECT COUNT(`id`) as kol FROM `B_PAYBOX_BALANS` WHERE `paybox_id`='$paybox_id' AND `cash_id`='$cash_id' AND `user_id`='$user_id';");
        $ex=$db->result($r,0,"kol");
        if ($ex==0){
            $db->query("INSERT INTO `B_PAYBOX_BALANS` (`paybox_id`,`saldo`,`cash_id`,`user_id`) VALUES ('$paybox_id','$summ','$cash_id','$user_id');");
        }
        if ($ex>0){
            $db->query("UPDATE `B_PAYBOX_BALANS` SET `saldo`=`saldo`+$summ WHERE `paybox_id`='$paybox_id' AND `cash_id`='$cash_id' AND `user_id`='$user_id' LIMIT 1;");
        }
        $r=$db->query("SELECT * FROM `B_PAYBOX_JOURNAL` WHERE `paybox_id`='$paybox_id' AND `cash_id`='$cash_id' AND `user_id`='$user_id' ORDER BY `id` DESC LIMIT 1;");
        $n=$db->num_rows($r); $saldo_before=0;
        if ($n==1){
            $saldo_before=$db->result($r,0,"saldo_after");
        }
        $saldo_after=round($saldo_before+$summ,2);
        $db->query("INSERT INTO `B_PAYBOX_JOURNAL` (`paybox_id`,`user_id`,`saldo_before`,`amount`,`saldo_after`,`cash_id`,`jpay_id`,`buh_income_id`,`buh_convert_id`) 
        VALUES ('$paybox_id','$user_id','$saldo_before','$summ','$saldo_after','$cash_id','0','$buh_income_id','0');");
        return;
    }

//    function dropBuhIncomeCard($buh_income_id) { $db=DbSingleton::getDb();
//        $answer=0;$err="Помилка!";
//        if ($buh_income_id>0) {
//            $db->query("DELETE FROM `J_BUH_INCOME` WHERE `ID`='$buh_income_id' LIMIT 1;");
//            $answer=1;$err="";
//        }
//        return array($answer, $err);
//    }

    function getPayboxList($paybox_id=0) { $db=DbSingleton::getDb();
        $r=$db->query("SELECT * FROM `PAY_BOX` WHERE `STATUS`=1;"); $n=$db->num_rows($r); $list="";
        for ($i=1;$i<=$n;$i++) {
            $id = $db->result($r, $i - 1, "id");
            $name = $db->result($r, $i - 1, "name");
            if ($id==$paybox_id) $sel="selected='selected'"; else $sel="";
            $list.="<option value='$id' $sel>$name</option>";
        }
        return $list;
    }

    function getCashList($cash_id=0) { $db=DbSingleton::getDb();
        $r=$db->query("SELECT * FROM `CASH`;"); $n=$db->num_rows($r); $list="";
        for ($i=1;$i<=$n;$i++) {
            $id = $db->result($r, $i - 1, "id");
            $name = $db->result($r, $i - 1, "abr");
            if ($id==$cash_id) $sel="selected='selected'"; else $sel="";
            $list.="<option value='$id' $sel>$name</option>";
        }
        return $list;
    }

    function getStateIncome($state_id=0) { $db=DbSingleton::getDb();
        $r=$db->query("SELECT * FROM `manual` WHERE `key`='state_income';"); $n=$db->num_rows($r); $list="";
        for ($i=1;$i<=$n;$i++) {
            $id = $db->result($r, $i - 1, "id");
            $name = $db->result($r, $i - 1, "mcaption");
            if ($id==$state_id) $sel="selected='selected'"; else $sel="";
            $list.="<option value='$id' $sel>$name</option>";
        }
        return $list;
    }

    function getBuhIncomeValues($pay_id, $cash_id, $state_id) { $db=DbSingleton::getDb();
        $r=$db->query("SELECT * FROM `PAY_BOX` WHERE `id`='$pay_id';");
        $pay_name = $db->result($r, 0, "name");
        $r=$db->query("SELECT * FROM `CASH` WHERE `id`='$cash_id';");
        $cash_name = $db->result($r, 0, "abr");
        $r=$db->query("SELECT * FROM `manual` WHERE `id`='$state_id';");
        $state_name = $db->result($r, 0, "mcaption");
        return array($pay_name, $cash_name, $state_name);
    }

    function getCashName($cash_id) { $db=DbSingleton::getDb();
        $r=$db->query("SELECT * FROM `CASH` WHERE `id`='$cash_id';");
        $cash_name = $db->result($r, 0, "abr");
        return $cash_name;
    }

    function getPayboxName($pay_id) { $db=DbSingleton::getDb();
        $r=$db->query("SELECT * FROM `PAY_BOX` WHERE `id`='$pay_id';");
        $name = $db->result($r, 0, "name");
        return $name;
    }

    function getSummCash($summ, $cash_id, $usd_to_uah, $eur_to_uah, $to_cash) {
        $summary=$summ;
        if ($to_cash==1) {
            if ($cash_id==2) $summary=$summ*$usd_to_uah;
            if ($cash_id==3) $summary=$summ*$eur_to_uah;
        }
        if ($to_cash==2) {
            if ($cash_id==1) $summary=$summ/$usd_to_uah;
            if ($cash_id==3) $summary=($summ*$usd_to_uah)/$eur_to_uah;
        }
        if ($to_cash==3) {
            if ($cash_id==1) $summary=$summ/$eur_to_uah;
            if ($cash_id==2) $summary=($summ*$usd_to_uah)/$eur_to_uah;
        }
        return round($summary,2);
    }

    function getBuhConvertList() { $db=DbSingleton::getDb();
        $media_users=new media_users;
        $r=$db->query("SELECT * FROM `J_BUH_CONVERT` WHERE 1;"); $n=$db->num_rows($r); $list="";
        for ($i=1;$i<=$n;$i++) {
            $id=$db->result($r,$i-1,"ID");
            $text=$db->result($r,$i-1,"TEXT");
            $pay_id=$db->result($r,$i-1,"PAY_ID"); $pay_name = $this->getPayboxName($pay_id);
            $cash_id_pay=$db->result($r,$i-1,"CASH_ID_PAY"); $cash_id_pay_name = $this->getCashName($cash_id_pay);
            $cash_id_to=$db->result($r,$i-1,"CASH_ID_TO"); $cash_id_to_name = $this->getCashName($cash_id_to);
            $usd=$db->result($r,$i-1,"KOURS_USD");
            $eur=$db->result($r,$i-1,"KOURS_EUR");
            $summ=$db->result($r,$i-1,"SUMM");
            $user_id=$db->result($r,$i-1,"USER_ID");
            $data=$db->result($r,$i-1,"DATA");
            $user_name=$media_users->getMediaUserName($user_id);
            $list.="<tr style='cursor: pointer' onclick=\"showBuhConvertCard($id);\">
                <td>$id</td>
                <td>$text</td>
                <td>$pay_name</td>
                <td>$cash_id_pay_name</td>
                <td>$cash_id_to_name</td>
                <td>$usd</td>
                <td>$eur</td>
                <td>$summ</td>
                <td>$user_name</td>
                <td>$data</td>
            </tr>";
        }
        return $list;
    }

    function showBuhConvertCard($buh_convert_id) { $db=DbSingleton::getDb();
        $media_users=new media_users;
        session_start();$media_user_id=$_SESSION["media_user_id"];
        $form_htm=RD."/tpl/buh_convert_card.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("SELECT * FROM `J_BUH_CONVERT` WHERE `ID`='$buh_convert_id' LIMIT 1;"); $n=$db->num_rows($r);
        if ($n==0) {
            $text="";
            $pay_id=0;
            $cash_id_pay=0;
            $cash_id_to=0;
            $usd=0;
            $eur=0;
            $summ=0;
            $user_id=$media_user_id;
            $data=date("Y-m-d H:i:s");
            $status=1;
        } else {
            $text=$db->result($r,0,"TEXT");
            $pay_id=$db->result($r,0,"PAY_ID");
            $cash_id_pay=$db->result($r,0,"CASH_ID_PAY");
            $cash_id_to=$db->result($r,0,"CASH_ID_TO");
            $usd=$db->result($r,0,"KOURS_USD");
            $eur=$db->result($r,0,"KOURS_EUR");
            $summ=$db->result($r,0,"SUMM");
            $user_id=$db->result($r,0,"USER_ID");
            $data=$db->result($r,0,"DATA");
            $status=$db->result($r,0,"STATUS");
        }
        $form=str_replace("{buh_convert_id}", $buh_convert_id, $form);
        $form=str_replace("{buh_convert_text}", $text, $form);
        $form=str_replace("{buh_convert_pay_id}", $this->getPayUser($user_id, $pay_id), $form);
        $form=str_replace("{buh_convert_cash_id_pay}", $this->getPayCashList($pay_id, $user_id, $cash_id_pay), $form);
        $form=str_replace("{buh_convert_cash_id_to}", $this->getCashList($cash_id_to), $form);
        $form=str_replace("{buh_convert_kours_usd}", $usd, $form);
        $form=str_replace("{buh_convert_kours_eur}", $eur, $form);
        $form=str_replace("{buh_convert_summ}", $summ, $form);
        $form=str_replace("{buh_convert_user_id}", $user_id, $form);
        $form=str_replace("{buh_convert_user_name}", $user_name=$media_users->getMediaUserName($user_id), $form);
        $form=str_replace("{buh_convert_data}", $data, $form);
        $form=str_replace("{buh_convert_status}", $status ? "checked='checked'" : "", $form);
        return $form;
    }

    function saveBuhConvertCard($buh_convert_id, $text, $pay_id, $cash_id_pay, $cash_id_to, $usd, $eur, $summ, $user_id, $data, $status) { $db=DbSingleton::getDb();
        $answer=0;$err="Помилка!";
        if ($buh_convert_id>0) {
            $db->query("UPDATE `J_BUH_CONVERT` SET `TEXT`='$text', `PAY_ID`='$pay_id', `CASH_ID_PAY`='$cash_id_pay', `CASH_ID_TO`='$cash_id_to', `KOURS_USD`='$usd', `KOURS_EUR`='$eur', `SUMM`='$summ', `USER_ID`='$user_id', `DATA`='$data', `STATUS`='$status'
            WHERE `ID`='$buh_convert_id' LIMIT 1;");
            $answer=1;$err="";
        } elseif ($buh_convert_id==0) {
            $r=$db->query("SELECT MAX(`ID`) as mid FROM `J_BUH_CONVERT`;"); $buh_convert_id=0+$db->result($r,0,"mid")+1;
            $db->query("INSERT INTO `J_BUH_CONVERT` (`ID`, `TEXT`, `PAY_ID`, `CASH_ID_PAY`, `CASH_ID_TO`, `KOURS_USD`, `KOURS_EUR`, `SUMM`, `USER_ID`, `DATA`, `STATUS`) 
            VALUES ('$buh_convert_id', '$text', '$pay_id', '$cash_id_pay', '$cash_id_to', '$usd', '$eur', '$summ', '$user_id', '$data', '$status');");
            $res=$this->convertPayboxBalans($buh_convert_id, $pay_id, $cash_id_pay, $cash_id_to, $usd, $eur, $summ, $user_id);
            if ($res) {
                $answer=1;$err="";
            } else {
                $answer=0;$err="something wrong!";
            }
        }
        return array($answer, $err);
    }

    function convertPayboxBalans($buh_convert_id, $paybox_id, $cash_id_pay, $cash_id_to, $usd, $eur, $summ, $user_id) { $db=DbSingleton::getDb();
        $r=$db->query("SELECT COUNT(`id`) as kol FROM `B_PAYBOX_BALANS` WHERE `paybox_id`='$paybox_id' AND `cash_id`='$cash_id_pay' AND `user_id`='$user_id';"); $ex=$db->result($r,0,"kol");
        $summ_cash = $this->getSummCash($summ, $cash_id_pay, $usd, $eur, $cash_id_to);
        if ($ex==0){
            return false;
        }
        if ($ex>0) {
            $db->query("UPDATE `B_PAYBOX_BALANS` SET `saldo`=`saldo`-$summ WHERE `paybox_id`='$paybox_id' AND `cash_id`='$cash_id_pay' AND `user_id`='$user_id' LIMIT 1;");

            $r=$db->query("SELECT * FROM `B_PAYBOX_BALANS` WHERE `paybox_id`='$paybox_id' AND `cash_id`='$cash_id_to' AND `user_id`='$user_id' LIMIT 1;"); $n=$db->num_rows($r);
            if ($n==0) {
                $db->query("INSERT INTO `B_PAYBOX_BALANS` (`paybox_id`, `user_id`, `saldo`, `cash_id`)
                VALUES ('$paybox_id', '$user_id', '$summ_cash', '$cash_id_to');");
            } else {
                $db->query("UPDATE `B_PAYBOX_BALANS` SET `saldo`=`saldo`+$summ_cash WHERE `paybox_id`='$paybox_id' AND `cash_id`='$cash_id_pay' AND `user_id`='$user_id' LIMIT 1;");
            }

            // JOURNAL FROM
            $saldo_before=0; $summ*=-1;
            $r=$db->query("SELECT * FROM `B_PAYBOX_JOURNAL` WHERE `paybox_id`='$paybox_id' AND `cash_id`='$cash_id_pay' AND `user_id`='$user_id' ORDER BY `id` DESC LIMIT 1;"); $n=$db->num_rows($r);
            if ($n==1) { $saldo_before=$db->result($r,0,"saldo_after"); }
            $saldo_after=round($saldo_before+$summ,2);
            $db->query("INSERT INTO `B_PAYBOX_JOURNAL` (`paybox_id`,`user_id`,`saldo_before`,`amount`,`saldo_after`,`cash_id`,`jpay_id`,`buh_income_id`,`buh_convert_id`)
            VALUES ('$paybox_id','$user_id','$saldo_before','$summ','$saldo_after','$cash_id_pay','0','0','$buh_convert_id');");
            // JOURNAL TO
            $saldo_before=0;
            $r=$db->query("SELECT * FROM `B_PAYBOX_JOURNAL` WHERE `paybox_id`='$paybox_id' AND `cash_id`='$cash_id_to' AND `user_id`='$user_id' ORDER BY `id` DESC LIMIT 1;"); $n=$db->num_rows($r);
            if ($n==1) { $saldo_before=$db->result($r,0,"saldo_after"); }
            $saldo_after=round($saldo_before+$summ_cash,2);
            $db->query("INSERT INTO `B_PAYBOX_JOURNAL` (`paybox_id`,`user_id`,`saldo_before`,`amount`,`saldo_after`,`cash_id`,`jpay_id`,`buh_income_id`,`buh_convert_id`)
            VALUES ('$paybox_id','$user_id','$saldo_before','$summ_cash','$saldo_after','$cash_id_to','0','0','$buh_convert_id');");
        }
        return true;
    }

    function getPayUser($user_id, $pay_id=0) { $db=DbSingleton::getDb();
        $r=$db->query("SELECT * FROM `B_PAYBOX_BALANS` pb
            LEFT OUTER JOIN `PAY_BOX` p ON p.id=pb.paybox_id
        WHERE p.`STATUS`=1 AND pb.`user_id`='$user_id' GROUP BY pb.`paybox_id`;"); $n=$db->num_rows($r); $list="";
        for ($i=1;$i<=$n;$i++) {
            $id = $db->result($r, $i - 1, "id");
            $name = $db->result($r, $i - 1, "name");
            if ($id==$pay_id) $sel="selected='selected'"; else $sel="";
            $list.="<option value='$id' $sel>$name</option>";
        }
        return $list;
    }

    function getPayCashList($pay_id, $user_id, $cash_id=0) { $db=DbSingleton::getDb();
        $r=$db->query("SELECT pb.cash_id, cs.abr, cs.name, pb.saldo FROM `B_PAYBOX_BALANS` pb 
            LEFT OUTER JOIN `CASH` cs ON cs.id = pb.cash_id
        WHERE pb.`paybox_id`='$pay_id' AND pb.`user_id`='$user_id';"); $n=$db->num_rows($r); $list="";
        for ($i=1;$i<=$n;$i++) {
            $cash = $db->result($r, $i - 1, "cash_id");
            $cash_name = $db->result($r, $i - 1, "name");
            $cash_abr = $db->result($r, $i - 1, "abr");
            $saldo = $db->result($r, $i - 1, "saldo");
            if ($cash==$cash_id) $sel="selected='selected'"; else $sel="";
            $list.="<option value='$cash' $sel>$cash_name - $saldo $cash_abr</option>";
        }
        return $list;
    }

}
