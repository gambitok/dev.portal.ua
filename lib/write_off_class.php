<?php

class write_off {

    function show_write_off_list() { $db = DbSingleton::getDb();
        $gmanual = new gmanual; $media_users = new media_users;
        session_start();
        $ses_tpoint_id = $_SESSION["media_tpoint_id"]; $media_user_id = $_SESSION["media_user_id"]; $media_role_id = $_SESSION["media_role_id"];
        $form = ""; $form_htm = RD."/tpl/write_off_range.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $data_cur = date("Y-m-d");
        $summ_uah = $summ_usd = $summ_eur = 0;
        $list = "";
        $where_tpoint = ($media_role_id == 1 || $media_role_id == 7) ? "" : " AND (sv.tpoint_id='$ses_tpoint_id' OR sv.user_id='$media_user_id' OR clc.tpoint_id='$ses_tpoint_id')";

        $r = $db->query("SELECT sv.*, dp.prefix as dp_prefix, dp.doc_nom as dp_nom, t.name as tpoint_name, sl.name as seller_name, cl.name as client_name, ch.abr2 as cash_abr, sum(sp.oper_price_partition) as price_summ
        FROM `J_WRITE_OFF` sv
            LEFT OUTER JOIN `J_DP` dp on dp.id=sv.dp_id
            LEFT OUTER JOIN `CASH` ch on ch.id=sv.cash_id
            LEFT OUTER JOIN `T_POINT` t on t.id=sv.tpoint_id
            LEFT OUTER JOIN `A_CLIENTS` sl on sl.id=sv.seller_id
            LEFT OUTER JOIN `A_CLIENTS` cl on cl.id=sv.client_conto_id
            LEFT OUTER JOIN `A_CLIENTS_CONDITIONS` clc on clc.client_id=sv.client_conto_id
            LEFT OUTER JOIN `manual` dt on (dt.key='client_sale_type' and dt.id=sv.doc_type_id)
            LEFT OUTER JOIN `J_WRITE_OFF_PARTITION_STR` sp on sp.write_off_id=sv.id 
        WHERE sv.status=1 AND sv.time_stamp>='$data_cur 00:00:00' AND sv.time_stamp<='$data_cur 23:59:59' $where_tpoint 
        GROUP BY sp.write_off_id 
        ORDER BY sv.time_stamp DESC, sv.status_invoice ASC, sv.data_create DESC, sv.prefix ASC, sv.id DESC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r,$i-1,"id");
            $dp_nom = $db->result($r,$i-1,"dp_prefix")."-".$db->result($r,$i-1,"dp_nom");
            $prefix = $db->result($r,$i-1,"prefix");
            $doc_nom = $db->result($r,$i-1,"doc_nom");
            $time_stamp = $db->result($r,$i-1,"time_stamp");
            $tpoint_name = $db->result($r,$i-1,"tpoint_name");
            $client_name = $db->result($r,$i-1,"client_name");
            $summ = $db->result($r,$i-1,"price_summ");
            $cash_id = $db->result($r,$i-1,"cash_id");
            $cash_abr = $db->result($r,$i-1,"cash_abr");
            $user_name = $media_users->getMediaUserName($db->result($r,$i-1,"user_id"));
            $status_write_off = $db->result($r,$i-1,"status_write_off");
            $status_write_off_cap = $gmanual->get_gmanual_caption($status_write_off);
            if ($cash_id == 1) {
                $summ_uah += $summ;
            }
            if ($cash_id == 2) {
                $summ_usd += $summ;
            }
            if ($cash_id == 3) {
                $summ_eur += $summ;
            }
            $list .= "<tr id='strStsRow_$i' style='cursor:pointer' align='center' onClick='showWriteOffCard(\"$id\");'>
                <td>$i</td>
                <td>$prefix-$doc_nom</td>
                <td align='center'>$time_stamp</td>
                <td>$dp_nom</td>
                <td>$tpoint_name</td>
                <td align='left'>$client_name</td>
                <td align='center' style='min-width:80px;'>$summ $cash_abr</td>
                <td align='left'>$user_name</td>
                <td align='center'>$status_write_off_cap</td>
            </tr>";
        }
        $form = str_replace("{write_off_range}",$list,$form);
        $form = str_replace("{write_off_summ}","$summ_uah UAH / $summ_usd USD / $summ_eur EUR",$form);
        return $form;
    }

    function filterWriteOffList($data_start, $data_end) { $db = DbSingleton::getDb();
        $gmanual = new gmanual; $media_users = new media_users;
        session_start();
        $ses_tpoint_id = $_SESSION["media_tpoint_id"]; $media_user_id = $_SESSION["media_user_id"]; $media_role_id = $_SESSION["media_role_id"];
        $data_cur = date("Y-m-d");
        $summ_uah = $summ_usd = $summ_eur = 0;
        $list = "";
        $where_tpoint = ($media_role_id == 1 || $media_role_id == 7) ? "" : " AND (sv.tpoint_id='$ses_tpoint_id' OR sv.user_id='$media_user_id' OR clc.tpoint_id='$ses_tpoint_id')";
        if ($data_start != "" && $data_end != "") {
            $where = " AND sv.time_stamp>='$data_start 00:00:00' AND sv.time_stamp<='$data_end 23:59:59'";
        } else {
            $where = " AND sv.time_stamp>='$data_cur 00:00:00' AND sv.time_stamp<='$data_cur 23:59:59'";
        }
        $r = $db->query("SELECT sv.*, dp.prefix as dp_prefix, dp.doc_nom as dp_nom, t.name as tpoint_name, sl.name as seller_name, cl.name as client_name, ch.abr2 as cash_abr 
        FROM `J_WRITE_OFF` sv
            LEFT OUTER JOIN `J_DP` dp on dp.id=sv.dp_id
            LEFT OUTER JOIN `CASH` ch on ch.id=sv.cash_id
            LEFT OUTER JOIN `T_POINT` t on t.id=sv.tpoint_id
            LEFT OUTER JOIN `A_CLIENTS` sl on sl.id=sv.seller_id
            LEFT OUTER JOIN `A_CLIENTS` cl on cl.id=sv.client_conto_id
            LEFT OUTER JOIN `A_CLIENTS_CONDITIONS` clc on clc.client_id=sv.client_conto_id
            LEFT OUTER JOIN `manual` dt on dt.key='client_sale_type' and dt.id=sv.doc_type_id
        WHERE sv.status=1 $where $where_tpoint ORDER BY sv.time_stamp DESC, sv.status_invoice ASC, sv.data_create DESC, sv.prefix ASC, sv.id DESC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r,$i-1,"id");
            $dp_nom = $db->result($r,$i-1,"dp_prefix")."-".$db->result($r,$i-1,"dp_nom");
            $prefix = $db->result($r,$i-1,"prefix");
            $doc_nom = $db->result($r,$i-1,"doc_nom");
            $time_stamp = $db->result($r,$i-1,"time_stamp");
            $tpoint_name = $db->result($r,$i-1,"tpoint_name");
            $client_name = $db->result($r,$i-1,"client_name");
            $summ = $db->result($r,$i-1,"summ");
            $cash_id = $db->result($r,$i-1,"cash_id");
            $cash_abr = $db->result($r,$i-1,"cash_abr");
            $user_name = $media_users->getMediaUserName($db->result($r,$i-1,"user_id"));
            $status_write_off = $db->result($r,$i-1,"status_write_off");
            $status_write_off_cap = $gmanual->get_gmanual_caption($status_write_off);
            if ($cash_id == 1) {
                $summ_uah += $summ;
            }
            if ($cash_id == 2) {
                $summ_usd += $summ;
            }
            if ($cash_id == 3) {
                $summ_eur += $summ;
            }
            $list .= "<tr id='strStsRow_$i' style='cursor:pointer' align='center' onClick='showWriteOffCard(\"$id\");'>
                <td>$i</td>
                <td>$prefix-$doc_nom</td>
                <td align='center'>$time_stamp</td>
                <td>$dp_nom</td>
                <td>$tpoint_name</td>
                <td align='left'>$client_name</td>
                <td align='center' style='min-width:80px;'>$summ $cash_abr</td>
                <td align='left'>$user_name</td>
                <td align='center'>$status_write_off_cap</td>
            </tr>";
        }
        $summ_price = "$summ_uah UAH / $summ_usd USD / $summ_eur EUR";
        return array($list, $summ_price);
    }

    function showWriteOffCard($write_off_id){$db = DbSingleton::getDb();
        $cat = new catalogue; $sale_invoice = new sale_invoice; $gmanual = new gmanual; $dp = new dp;
        $list = ""; $prefix = ""; $doc_nom = 0;
        $form = ""; $form_htm = RD."/tpl/write_off_card.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $r = $db->query("SELECT sv.*, t.name as tpoint_name, sl.name as seller_name, cl.name as client_name, dt.mcaption as doc_type_name, dt.mvalue as doc_type_abr, ch.abr2 as cash_abr 
        FROM `J_WRITE_OFF` sv
            LEFT OUTER JOIN `CASH` ch on ch.id=sv.cash_id
            LEFT OUTER JOIN `T_POINT` t on t.id=sv.tpoint_id
            LEFT OUTER JOIN `A_CLIENTS` sl on sl.id=sv.seller_id
            LEFT OUTER JOIN `A_CLIENTS` cl on cl.id=sv.client_conto_id
            LEFT OUTER JOIN `manual` dt on dt.key='client_sale_type' AND dt.id=sv.doc_type_id
        WHERE sv.status=1 AND sv.id='$write_off_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 0) {
            $form_htm = RD . "/tpl/access_deny.htm";
            if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        }
        if ($n == 1) {
            $dp_id = $db->result($r,0,"dp_id");
            $prefix = $db->result($r,0,"prefix");
            $doc_nom = $db->result($r,0,"doc_nom");
            $data_create = $db->result($r,0,"data_create");
            $tpoint_name = $db->result($r,0,"tpoint_name");
            $seller_name = $db->result($r,0,"seller_name");
            $client_name = $db->result($r,0,"client_name");
            $doc_type_id = $db->result($r,0,"doc_type_id");
            $summ = $db->result($r,0,"summ");
            $cash_abr = $db->result($r,0,"cash_abr");
            $usd_to_uah = $db->result($r,0,"usd_to_uah");
            $eur_to_uah = $db->result($r,0,"eur_to_uah");
            $status_write_off = $db->result($r,0,"status_write_off");

            list($usd_to_uah_new, $eur_to_uah_new) = $sale_invoice->getKoursData();
            if ($usd_to_uah == 0) {
                $usd_to_uah = $usd_to_uah_new;
            }
            if ($eur_to_uah == 0) {
                $eur_to_uah = $eur_to_uah_new;
            }

            $form = str_replace("{write_off_id}",$write_off_id,$form);
            $form = str_replace("{data}",$data_create,$form);
            $form = str_replace("{prefix}",$prefix,$form);
            $form = str_replace("{doc_nom}",$doc_nom,$form);
            $form = str_replace("{tpoint_name}",$tpoint_name,$form);
            $form = str_replace("{seller_name}",$seller_name,$form);
            $form = str_replace("{dp_note}",$dp->getDpNote($dp_id),$form);
            $form = str_replace("{usd_to_uah}",$usd_to_uah,$form);
            $form = str_replace("{eur_to_uah}",$eur_to_uah,$form);
            $form = str_replace("{client_name}",$client_name,$form);
            $form = str_replace("{doc_type_name}",$gmanual->get_gmanual_caption($status_write_off),$form);
            $form = str_replace("{invoice_summ}",$summ,$form);
            $form = str_replace("{cash_name}",$cash_abr,$form);
            $form = str_replace("{volume}",0,$form);
            $form = str_replace("{style_doc_id}",($doc_type_id == 64) ? "style='display:none;'" : "",$form);
            $form = str_replace("{oper_visible}",($doc_type_id == "61") ? "" : " disabled style=\"display:none;\"",$form);

            $tax_hidden = " hidden";
            if ($doc_type_id == "61") {
                $tax_hidden = ($sale_invoice->checkTaxExist($write_off_id) > 0) ? " hidden" : "";
            }
            $form = str_replace("{hidden_tax}",$tax_hidden,$form);

            $r = $db->query("SELECT * FROM `J_WRITE_OFF_STR` WHERE `write_off_id`='$write_off_id' ORDER BY `id` ASC;");
            $n = $db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $article_nr_displ = $db->result($r,$i-1,"article_nr_displ");
                $brand_id = $db->result($r,$i-1,"brand_id");
                $brand_name = $cat->getBrandName($brand_id);
                $amount = $db->result($r,$i-1,"amount");
                $price = $db->result($r,$i-1,"price");
                $summ = $db->result($r,$i-1,"summ");
                $list .= "<tr align='right'>
                    <td align='left'>$i</td>
                    <td align='left'>$article_nr_displ</td>
                    <td align='center'>$brand_name</td>
                    <td>$amount</td>
                    <td>$price</td>
                    <td>$summ</td>
                </tr>";
            }
            $form = str_replace("{sale_invoice_str_list}",$list,$form);
        }
        return array($form, "$prefix-$doc_nom");
    }

    function loadWriteOffPartitions($write_off_id){$db = DbSingleton::getDb();
        $income = new income; $cat = new catalogue;
        $prev_doc_id = 0; $list = ""; $doc_name = "";
        $form = ""; $form_htm = RD."/tpl/write_off_partitions_list.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $r = $db->query("SELECT ps.*, ap.parrent_type_id, ap.parrent_doc_id 
        FROM `J_WRITE_OFF_PARTITION_STR` ps
            LEFT OUTER JOIN `T2_ARTICLES_PARTITIONS` ap ON (ap.id=ps.partition_id)
        WHERE ps.status=1 AND ps.write_off_id='$write_off_id' ORDER BY ps.id ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            if ($i == 1) { $doc_name = ""; }
            $parrent_type_id = $db->result($r,$i-1,"parrent_type_id");
            $parrent_doc_id = $db->result($r,$i-1,"parrent_doc_id");
            $article_nr_displ = $db->result($r,$i-1,"article_nr_displ");
            $brand_id = $db->result($r,$i-1,"brand_id");
            $brand_name = $cat->getBrandName($brand_id);
            $partition_amount = $db->result($r,$i-1,"partition_amount");
            $invoice_amount = $db->result($r,$i-1,"invoice_amount");
            $oper_price_partition = $db->result($r,$i-1,"oper_price_partition");
            $price_partition = $db->result($r,$i-1,"price_partition");
            $price_buh_uah = $db->result($r,$i-1,"price_buh_uah");
            $price_man_uah = $db->result($r,$i-1,"price_man_uah");
            $price_invoice = $db->result($r,$i-1,"price_invoice");
            if ($parrent_type_id == 1) {
                if ($parrent_doc_id != $prev_doc_id) {
                    $doc_name = "" . $income->getIncomeDocNom($parrent_doc_id);
                    $prev_doc_id = $parrent_doc_id;
                }
            }
            $list .= "<tr id='strStsRow_$i' style='cursor:pointer;'>
                <td align='center'>$i</td>
                <td align='center'>$doc_name</td>
                <td align='center'>$article_nr_displ</td>
                <td align='center'>$brand_name</td>
                <td align='right'>$partition_amount</td>
                <td align='right'>$invoice_amount</td>
                <td align='right'>$price_partition</td>
                <td align='right'>$oper_price_partition</td>
                <td align='right'>$price_buh_uah</td>
                <td align='right'>$price_man_uah</td>
                <td align='right'>$price_invoice</td>
            </tr>";
        }
        if ($n == 0) {
            $list = "<tr><td colspan=8 align='center'>Записи відсутні</td></tr>";
        }
        $form = str_replace("{partitions_list}",$list,$form);
        $form = str_replace("{write_off_id}",$write_off_id,$form);
        return $form;
    }

}