<?php

class sale_invoice {

    public function getKoursData(): array
    {
        $db = DbSingleton::getDb();
        $slave = new slave;
        $usd_to_uah = $eur_to_uah = 0;

        $r = $db->query("SELECT `kours_value` FROM `J_KOURS` WHERE `cash_id` = '2' AND `in_use` = '1' ORDER BY `id` DESC LIMIT 1;");
        $n = (int)$db->num_rows($r);
        if ($n === 1) {
            $usd_to_uah = $slave->to_money(round($db->result($r, 0, "kours_value"), 2));
        }

        $r = $db->query("SELECT `kours_value` FROM `J_KOURS` WHERE `cash_id` = '3' AND `in_use` = '1' ORDER BY `id` DESC LIMIT 1;");
        $n = (int)$db->num_rows($r);
        if ($n === 1) {
            $eur_to_uah = $slave->to_money(round($db->result($r, 0, "kours_value"), 2));
        }

        return array($usd_to_uah, $eur_to_uah);
    }

    public function getSaleInvoiceName($id): string
    {
        $db = DbSingleton::getDb();
        $name = "";
        $r = $db->query("SELECT * FROM `J_SALE_INVOICE` WHERE `status` = 1 AND `id` = '$id' LIMIT 1;");
        $n = (int)$db->num_rows($r);
        if ($n === 1) {
            $name = $db->result($r, 0, "prefix") . "-" . $db->result($r, 0, "doc_nom");
        }
        return $name;
    }

    public function getJPayName($id): array
    {
        $db = DbSingleton::getDb();
        $name = "";
        $pay_type_id = 0;
        $r = $db->query("SELECT p.*, m.mcaption as pay_type_name 
        FROM `J_PAY` p 
            LEFT OUTER JOIN `manual` m ON (m.id = p.pay_type_id AND m.`key` = 'pay_type_id') 
        WHERE p.status = 1 AND p.id = '$id' LIMIT 1;");
        $n = (int)$db->num_rows($r);
        if ($n === 1) {
            $pay_type_id = $db->result($r, 0, "pay_type_id");
            $name = $db->result($r, 0, "pay_type_name") . " №" . $db->result($r, 0, "doc_nom");
        }
        return array($pay_type_id, $name);
    }

    public function getTpointFullName($id)
    {
        $db = DbSingleton::getDb();
        $name = "";
        $r = $db->query("SELECT `full_name` FROM `T_POINT` WHERE `id` = '$id' LIMIT 1;");
        $n = (int)$db->num_rows($r);
        if ($n === 1) {
            $name = $db->result($r, 0, "full_name");
        }
        return $name;
    }

    public function getMediaUserName($user_id)
    {
        $db = DbSingleton::getDb();
        $name = "";
        $r = $db->query("SELECT `name` FROM `media_users` WHERE `id` = '$user_id' LIMIT 1;");
        $n = (int)$db->num_rows($r);
        if ($n === 1) {
            $name = $db->result($r, 0, "name");
        }
        return $name;
    }

    public function show_sale_invoice_list()
    {
        $db = DbSingleton::getDb();
        session_start();
        $dp = new dp;
        $ses_tpoint_id = $_SESSION["media_tpoint_id"];
        $media_user_id = $_SESSION["media_user_id"];
        $media_role_id = (int)$_SESSION["media_role_id"];

        $form = ""; $form_htm = RD . "/tpl/sale_invoice_range.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $data_cur = date("Y-m-d");
        $summ_uah = $summ_usd = $summ_eur = 0;
        $list = "";

        $where_tpoint = " AND (sv.tpoint_id='$ses_tpoint_id' OR sv.user_id='$media_user_id' OR clc.tpoint_id='$ses_tpoint_id')";
        if ($media_role_id === 1 || $media_role_id === 7) {
            $where_tpoint = "";
        }
        $where = " AND sv.time_stamp>='$data_cur 00:00:00' AND sv.time_stamp<='$data_cur 23:59:59'";

        $r = $db->query("SELECT sv.*, dp.prefix as dp_prefix, dp.doc_nom as dp_nom, dp.order_info_id, t.name as tpoint_name, sl.name as seller_name, cl.name as client_name, dt.mvalue as doc_type_name, ch.abr2 as cash_abr 
        FROM `J_SALE_INVOICE` sv
            LEFT OUTER JOIN `J_DP` dp ON (dp.id = sv.dp_id)
            LEFT OUTER JOIN `CASH` ch ON (ch.id = sv.cash_id)
            LEFT OUTER JOIN `T_POINT` t ON (t.id = sv.tpoint_id)
            LEFT OUTER JOIN `A_CLIENTS` sl ON (sl.id = sv.seller_id)
            LEFT OUTER JOIN `A_CLIENTS` cl ON (cl.id = sv.client_conto_id)
            LEFT OUTER JOIN `A_CLIENTS_CONDITIONS` clc ON (clc.client_id = sv.client_conto_id)
            LEFT OUTER JOIN `manual` dt ON (dt.key = 'client_sale_type' AND dt.id = sv.doc_type_id)
        WHERE sv.status = 1 $where $where_tpoint 
        ORDER BY sv.time_stamp DESC, sv.status_invoice ASC, sv.data_create DESC, sv.prefix ASC, sv.id DESC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id                 = $db->result($r, $i - 1, "id");
            $dp_nom             = $db->result($r, $i - 1, "dp_prefix") . $db->result($r, $i - 1, "dp_nom");
            $prefix             = $db->result($r, $i - 1, "prefix");
            $doc_nom            = $db->result($r, $i - 1, "doc_nom");
            $order_info_id      = $db->result($r, $i - 1, "order_info_id");
            $order_info_text    = $dp->getOrderDeliveryInfo($order_info_id);
            $time_stamp         = $db->result($r, $i - 1, "time_stamp");
            $tpoint_name        = $db->result($r, $i - 1, "tpoint_name");
            $seller_name        = $db->result($r, $i - 1, "seller_name");
            $client_name        = $db->result($r, $i - 1, "client_name");
            $doc_type_name      = $db->result($r, $i - 1, "doc_type_name");
            $summ               = $db->result($r, $i - 1, "summ");
            $summ_debit         = $db->result($r, $i - 1, "summ_debit");
            $cash_id            = (int)$db->result($r, $i - 1, "cash_id");
            $cash_abr           = $db->result($r, $i - 1, "cash_abr");
            $data_pay           = $db->result($r, $i - 1, "data_pay");
            $user_name          = $this->getMediaUserName($db->result($r, $i - 1, "user_id"));
            $dp_note            = $dp->getDpNote($db->result($r, $i - 1, "dp_nom"));

            if ($cash_id === 1) {
                $summ_uah += $summ;
            }
            if ($cash_id === 2) {
                $summ_usd += $summ;
            }
            if ($cash_id === 3) {
                $summ_eur += $summ;
            }
            $summ_cap = (empty($summ_debit)) ? "" : "$summ_debit $cash_abr";

            $list .= "
            <tr id='strStsRow_$i' style='cursor:pointer' align='center' onClick='showSaleInvoiceCard(\"$id\");'>
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
                <td align='right'>$data_pay</td>
                <td align='left'>$user_name</td>
                <td align='left'>$order_info_text</td>
                <td align='center'>$dp_note</td>
            </tr>";
        }

        $form = str_replace(array("{sale_invoice_range}", "{sale_invoice_summ}"), array($list, "$summ_uah UAH / $summ_usd USD / $summ_eur EUR"), $form);

        return $form;
    }

    public function show_sale_invoice_list_filter($data_start, $data_end, $prefix_sel, $doc_nom_sel): array
    {
        $db = DbSingleton::getDb();
        session_start();
        $dp = new dp;
        $summ_uah = $summ_usd = $summ_eur = 0;
        $list = "";
        $ses_tpoint_id = $_SESSION["media_tpoint_id"];
        $media_user_id = $_SESSION["media_user_id"];
        $media_role_id = (int)$_SESSION["media_role_id"];

        $where_tpoint = " AND (sv.tpoint_id='$ses_tpoint_id' OR sv.user_id='$media_user_id' OR clc.tpoint_id='$ses_tpoint_id')";
        if ($media_role_id === 1 || $media_role_id === 7) {
            $where_tpoint = "";
        }

        $where = "";
        if ($data_start !== "" && $data_end !== "") {
            $where = " AND sv.time_stamp>='$data_start 00:00:00' AND sv.time_stamp<='$data_end 23:59:59'";
        }
        if ($prefix_sel !== "" && $doc_nom_sel !== "" && $doc_nom_sel > 0) {
            $where = " AND sv.prefix='$prefix_sel' AND sv.doc_nom='$doc_nom_sel'";
        }
        if ($prefix_sel === "" && $doc_nom_sel !== "" && $doc_nom_sel > 0) {
            $where = " AND sv.doc_nom='$doc_nom_sel'";
        }

        $r = $db->query("SELECT sv.*, dp.prefix as dp_prefix, dp.doc_nom as dp_nom, dp.order_info_id, t.name as tpoint_name, sl.name as seller_name, cl.name as client_name, dt.mvalue as doc_type_name, ch.abr2 as cash_abr 
        FROM `J_SALE_INVOICE` sv
            LEFT OUTER JOIN `J_DP` dp ON (dp.id = sv.dp_id)
            LEFT OUTER JOIN `CASH` ch ON (ch.id = sv.cash_id)
            LEFT OUTER JOIN `T_POINT` t ON (t.id = sv.tpoint_id)
            LEFT OUTER JOIN `A_CLIENTS` sl ON (sl.id = sv.seller_id)
            LEFT OUTER JOIN `A_CLIENTS` cl ON (cl.id = sv.client_conto_id)
            LEFT OUTER JOIN `A_CLIENTS_CONDITIONS` clc ON (clc.client_id = sv.client_conto_id)
            LEFT OUTER JOIN `manual` dt ON (dt.key = 'client_sale_type' AND dt.id = sv.doc_type_id)
        WHERE sv.status = 1 $where $where_tpoint 
        ORDER BY sv.time_stamp DESC, sv.status_invoice ASC, sv.data_create DESC, sv.prefix ASC, sv.id DESC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id                 = $db->result($r, $i - 1, "id");
            $dp_nom             = $db->result($r, $i - 1, "dp_prefix") . $db->result($r, $i - 1, "dp_nom");
            $prefix             = $db->result($r, $i - 1, "prefix");
            $doc_nom            = $db->result($r, $i - 1, "doc_nom");
            $order_info_id      = $db->result($r, $i - 1, "order_info_id");
            $order_info_text    = $dp->getOrderDeliveryInfo($order_info_id);
            $time_stamp         = $db->result($r, $i - 1, "time_stamp");
            $tpoint_name        = $db->result($r, $i - 1, "tpoint_name");
            $seller_name        = $db->result($r, $i - 1, "seller_name");
            $client_name        = $db->result($r, $i - 1, "client_name");
            $doc_type_name      = $db->result($r, $i - 1, "doc_type_name");
            $summ               = $db->result($r, $i - 1, "summ");
            $summ_debit         = $db->result($r, $i - 1, "summ_debit");
            $cash_id            = (int)$db->result($r, $i - 1, "cash_id");
            $cash_abr           = $db->result($r, $i - 1, "cash_abr");
            $data_pay           = $db->result($r, $i - 1, "data_pay");
            $user_name          = $this->getMediaUserName($db->result($r, $i - 1, "user_id"));
            $dp_note            = $dp->getDpNote($db->result($r, $i - 1, "dp_nom"));

            if ($cash_id === 1) {
                $summ_uah += $summ;
            }
            if ($cash_id === 2) {
                $summ_usd += $summ;
            }
            if ($cash_id === 3) {
                $summ_eur += $summ;
            }

            $list .= "
            <tr id='strStsRow_$i' style='cursor:pointer' align='center' onClick='showSaleInvoiceCard(\"$id\");'>
                <td>$i</td>
                <td>$prefix-$doc_nom</td>
                <td align='center'>$time_stamp</td>
                <td>$dp_nom</td>
                <td>$tpoint_name</td>
                <td align='left'>$seller_name</td>
                <td align='left'>$client_name</td>
                <td>$doc_type_name</td>
                <td align='center' style='min-width:80px;'>$summ$cash_abr</td>
                <td align='right'>$summ_debit$cash_abr</td>
                <td align='right'>$data_pay</td>
                <td align='left'>$user_name</td>
                <td align='left'>$order_info_text</td>
                <td align='center'>$dp_note</td>
            </tr>";
        }

        $summ_price = "$summ_uah UAH / $summ_usd USD / $summ_eur EUR";

        return array($list, $summ_price);
    }

    public function checkTaxExist($invoice_id)
    {
        $db = DbSingleton::getDb();
        $tax_id = 0;
        $r = $db->query("SELECT `id` FROM `J_TAX_INVOICE` WHERE `sale_invoice_id` = '$invoice_id' AND `status` = 1 LIMIT 1;");
        $n = (int)$db->num_rows($r);
        if ($n === 1) {
            $tax_id = $db->result($r, 0, "id");
        }
        return $tax_id;
    }

    public function createTaxInvoice($invoice_id): array
    {
        $db = DbSingleton::getDb();
        $cat = new catalogue;
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $tax_id = 0; $answer = 0; $err = "Помилка!";

        $r = $db->query("SELECT `seller_id`, `client_conto_id`, `tpoint_id`, `cash_id`, `summ` FROM `J_SALE_INVOICE` WHERE `id` = '$invoice_id' LIMIT 1;");
        $n = (int)$db->num_rows($r);

        if ($n === 1) {
            $seller_id  = $db->result($r, 0, "seller_id");
            $client_id  = $db->result($r, 0, "client_conto_id");
            $tpoint_id  = $db->result($r, 0, "tpoint_id");
            $cash_id    = $db->result($r, 0, "cash_id");
            $summ       = $db->result($r, 0, "summ");

            $rt = $db->query("SELECT MAX(`id`) as mid FROM `J_TAX_INVOICE`;");
            $tax_id = 0 + $db->result($rt, 0, "mid") + 1;
            $year = date("Y");
            $rt = $db->query("SELECT MAX(`doc_nom`) as mid FROM `J_TAX_INVOICE` WHERE `seller_id` = '$seller_id' AND `data_create` >= '$year-01-01';");
            $tax_nom = 0 + $db->result($rt, 0, "mid") + 1;

            $db->query("INSERT INTO `J_TAX_INVOICE` (`id`, `tax_type_id`, `doc_nom`, `data_create`, `sale_invoice_id`, `tpoint_id`, `seller_id`, `client_id`, `cash_id`, `summ`, `user_id`) 
            VALUES ('$tax_id', '160', '$tax_nom', CURDATE(), '$invoice_id', '$tpoint_id', '$seller_id', '$client_id', '$cash_id', '$summ', '$user_id');");

            $r1 = $db->query("SELECT `art_id`, `amount`, `price_end`, `summ` FROM `J_SALE_INVOICE_STR` WHERE `invoice_id` = '$invoice_id' ORDER BY `id` ASC;");
            $n1 = $db->num_rows($r1);
            for ($i = 1; $i <= $n1; $i++) {
                $art_id     = $db->result($r1, $i - 1, "art_id");
                $amount     = $db->result($r1, $i - 1, "amount");
                $price      = $db->result($r1, $i - 1, "price_end");
                $summ       = $db->result($r1, $i - 1, "summ");
                $zed        = $cat->getArticleZED($art_id);
                $art_name   = $cat->getArticleNameLang($art_id);

                $db->query("INSERT INTO `J_TAX_INVOICE_STR` (`tax_id`, `zed`, `art_id`, `goods_name`, `amount`, `price`, `summ`, `tax_str_nom`) 
                VALUES ('$tax_id', '$zed', '$art_id', '$art_name', '$amount', '$price', '$summ', '$i');");
            }
            $answer = 1; $err = "";
        }

        return array($answer, $err, $tax_id);
    }

    public function showSaleInvoiceCard($invoice_id): array
    {
        $db = DbSingleton::getDb();
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $cat = new catalogue;
        $dp = new dp;
        $list = ""; $prefix = "";
        $doc_nom = 0;
        $form = ""; $form_htm = RD . "/tpl/sale_invoice_card.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm);}

        $r = $db->query("SELECT sv.*, t.name as tpoint_name, sl.name as seller_name, sl.id as seller_id, cl.name as client_name, dt.mcaption as doc_type_name, dt.mvalue as doc_type_abr, ch.abr2 as cash_abr 
        FROM `J_SALE_INVOICE` sv
            LEFT OUTER JOIN `CASH` ch ON (ch.id = sv.cash_id)
            LEFT OUTER JOIN `T_POINT` t ON (t.id = sv.tpoint_id)
            LEFT OUTER JOIN `A_CLIENTS` sl ON (sl.id = sv.seller_id)
            LEFT OUTER JOIN `A_CLIENTS` cl ON (cl.id = sv.client_conto_id)
            LEFT OUTER JOIN `manual` dt ON (dt.key = 'client_sale_type' AND dt.id = sv.doc_type_id)
        WHERE sv.status = 1 AND sv.id = '$invoice_id' LIMIT 1;");
        $n = (int)$db->num_rows($r);

        if ($n === 0) {
            $form_htm = RD . "/tpl/access_deny.htm";
            if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        }

        if ($n === 1) {
            $dp_id          = $db->result($r, 0, "dp_id");
            $prefix         = $db->result($r, 0, "prefix");
            $doc_nom        = $db->result($r, 0, "doc_nom");
            $data_create    = $db->result($r, 0, "data_create");
            $tpoint_name    = $db->result($r, 0, "tpoint_name");
            $seller_name    = $db->result($r, 0, "seller_name");
            $client_id      = $db->result($r, 0, "seller_id");
            $client_name    = $db->result($r, 0, "client_name");
            $doc_type_id    = (int)$db->result($r, 0, "doc_type_id");
            $doc_type_name  = $db->result($r, 0, "doc_type_name");
            $summ           = $db->result($r, 0, "summ");
            $summ_debit     = $db->result($r, 0, "summ_debit");
            $cash_abr       = $db->result($r, 0, "cash_abr");
            $data_pay       = $db->result($r, 0, "data_pay");
            $usd_to_uah     = $db->result($r, 0, "usd_to_uah");
            $eur_to_uah     = $db->result($r, 0, "eur_to_uah");

            [$usd_to_uah_new, $eur_to_uah_new] = $this->getKoursData();

            if ($usd_to_uah == 0) {
                $usd_to_uah = $usd_to_uah_new;
            }

            if ($eur_to_uah == 0) {
                $eur_to_uah = $eur_to_uah_new;
            }

            $form = str_replace("{invoice_id}", $invoice_id, $form);
            $form = str_replace("{data}", $data_create, $form);
            $form = str_replace("{data_pay}", $data_pay, $form);
            $form = str_replace("{prefix}", $prefix, $form);
            $form = str_replace("{doc_nom}", $doc_nom, $form);
            $form = str_replace("{tpoint_name}", $tpoint_name, $form);
            $form = str_replace("{seller_name}", $seller_name, $form);
            $form = str_replace("{dp_note}", $dp->getDpNote($dp_id), $form);
            $form = str_replace("{usd_to_uah}", $usd_to_uah, $form);
            $form = str_replace("{eur_to_uah}", $eur_to_uah, $form);
            $form = str_replace("{client_name}", $client_name, $form);
            $form = str_replace("{doc_type_name}", $doc_type_name, $form);
            $form = str_replace("{invoice_summ}", $summ, $form);
            $form = str_replace("{cash_name}", $cash_abr, $form);
            $form = str_replace("{volume}", 0, $form);
            $form = str_replace("{users_email}", $this->getClientInvoiceCron($invoice_id), $form);
            $form = str_replace("{dp_address_user}", $dp->getDpUserDeliveryData($dp_id), $form);

            $tax_hidden = " hidden";
            if ($doc_type_id === 61) {
                $tax_hidden = ($this->checkTaxExist($invoice_id) > 0) ? " hidden" : "";
            }
            $form = str_replace("{hidden_tax}", $tax_hidden, $form);
            $form = str_replace("{oper_visible}", ($doc_type_id === 61) ? "" : " disabled style=\"display:none;\"" , $form);
            $form = str_replace("{oper_visible_no_61}", ($doc_type_id === 61) ? " disabled style=\"display:none;\"" : "" , $form);
            $form = str_replace("{visible_64}", ($doc_type_id === 64) ? "" : " disabled style=\"display:none;\"", $form);
            $form = str_replace("{style_doc_id}", ($doc_type_id === 64) ? "style='display:none;'" : "", $form);

            /*CHECK BOX*/
            $rr = $db->query("SELECT * FROM `PRRO_SALE_INVOICE` WHERE `INVOICE_ID` = $invoice_id AND `DOC_TYPE_ID` = 1 LIMIT 1;");
            $nn = (int)$db->num_rows($rr);

            $r2 = $db->query("SELECT SUM(`summ_pay`) as sum1 FROM `J_PAY_STR` WHERE `parrent_doc_id` = $invoice_id");
            $sum_pay = $db->result($r2, 0, "sum1");

            $list_add = ($this->checkCBCheck($client_id, $user_id) && ($summ == $sum_pay && $summ_debit == 0) && $nn === 0) ? "" : "none";
            $list_show = ($this->checkCBCheck($client_id, $user_id) && ($summ == $sum_pay && $summ_debit == 0) && $nn > 0) ? "" : "none";

            $form = str_replace(array("{cb_check_visible_add}", "{cb_check_visible_show}"), array($list_add, $list_show), $form);

            $r = $db->query("SELECT `article_nr_displ`, `brand_id`, `amount`, `price`, `price_end`, `discount`, `summ` FROM `J_SALE_INVOICE_STR` 
            WHERE `invoice_id` = '$invoice_id' ORDER BY `id` ASC;");
            $n = $db->num_rows($r);

            for ($i = 1; $i <= $n; $i++) {
                $art_nr_ds  = $db->result($r, $i - 1, "article_nr_displ");
                $brand_id   = $db->result($r, $i - 1, "brand_id");
                $amount     = $db->result($r, $i - 1, "amount");
                $price      = $db->result($r, $i - 1, "price");
                $price_end  = $db->result($r, $i - 1, "price_end");
                $discount   = $db->result($r, $i - 1, "discount");
                $summ       = $db->result($r, $i - 1, "summ");
                $brand_name = $cat->getBrandName($brand_id);

                $list .= "
                <tr align='right'>
                    <td align='left'>$i</td>
                    <td align='left'>$art_nr_ds</td>
                    <td align='center'>$brand_name</td>
                    <td>$amount</td>
                    <td>$price</td>
                    <td>$discount</td>
                    <td>$price_end</td>
                    <td>$summ</td>
                </tr>";
            }
            $form = str_replace("{sale_invoice_str_list}", $list, $form);
        }

        return array($form, "$prefix-$doc_nom");
    }

    public function checkCBCheck($client_id, $user_id): int
    {
        $db = DbSingleton::getDb();

        $r = $db->query("SELECT `ID` FROM `PRRO_MAIN` WHERE `CLIENT_ID` = $client_id LIMIT 1;");
        $n = $db->num_rows($r);

        $r2 = $db->query("SELECT `ID` FROM `PRRO_CASHIERS` WHERE `USER_ID` = $user_id AND `STATUS` = 1 LIMIT 1;");
        $n2 = $db->num_rows($r2);

        if ($n > 0 && $n2 > 0) {
            return 1;
        }

        return 0;
    }

    public function checkCBCheckStr($invoice_id, $doc_type_id = 1): array
    {
        $status = 0;
        $no_names = [];
        $db = DbSingleton::getDb();
        $dbt = DbSingleton::getTokoDb();
        $doc_type_id = (int)$doc_type_id;

        if ($doc_type_id === 2) {
            $r = $db->query("SELECT `art_id`, `article_nr_displ` FROM `J_BACK_CLIENTS_STR` WHERE `back_id` = $invoice_id;");
        } else {
            $r = $db->query("SELECT `art_id`, `article_nr_displ` FROM `J_SALE_INVOICE_STR` WHERE `invoice_id` = $invoice_id;");
        }
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $art_id     = $db->result($r, $i - 1, "art_id");
            $art_nr_ds  = $db->result($r, $i - 1, "article_nr_displ");

            $r2 = $dbt->query("SELECT `NAME` FROM `T2_NAMES` WHERE `ART_ID` = $art_id AND `LANG_ID` = 41 LIMIT 1;");
            $n2 = $db->num_rows($r2);
            if ($n2 === 0) {
                $status++;
                $no_names[] = (string)$art_nr_ds;
            } else {
                $name = $dbt->result($r2, 0, "NAME");
                if ($name === "") {
                    $status++;
                    $no_names[] = (string)$art_nr_ds;
                }
            }
        }

        return array("status" => $status, "nonames" => $no_names);
    }

    public function getArticleName($art_id)
    {
        $db = DbSingleton::getTokoDb();
        $name = "";
        $r = $db->query("SELECT `NAME` FROM `T2_NAMES` WHERE `ART_ID` = $art_id AND `LANG_ID` = 41 LIMIT 1;");
        $n = (int)$db->num_rows($r);
        if ($n === 1) {
            $name = $db->result($r, 0, "NAME");
        }
        return $name;
    }

    public function getBrandName($id)
    {
        $db = DbSingleton::getTokoDb();
        $name = "";
        $r = $db->query("SELECT `BRAND_NAME` FROM `T2_BRANDS` WHERE `BRAND_ID` = $id LIMIT 1;");
        $n = (int)$db->num_rows($r);
        if ($n === 1) {
            $name = $db->result($r, 0, "BRAND_NAME");
        }
        return $name;
    }

    public function getCbCheckStr($invoice_id, $doc_type_id = 1, $cashier_name = "", $department = "", $payment_id = 1, $email = ""): array
    {
        $db = DbSingleton::getDb();
        $answer = 0; $err = "";
        $receipt = [];
        $doc_type_id = (int)$doc_type_id;
        $payment_id = (int)$payment_id;

        if ($doc_type_id === 2) {
            $rstr = $db->query("SELECT `art_id`, `brand_id`, `article_nr_displ`, `amount` * 1000 as `amount1000`, `price` * 100 as `price100` FROM `J_BACK_CLIENTS_STR` WHERE `back_id` = $invoice_id;");
        } else {
            $rstr = $db->query("SELECT `art_id`, `brand_id`, `article_nr_displ`, `amount` * 1000 as `amount1000`, `price_end` * 100 as `price100` FROM `J_SALE_INVOICE_STR` WHERE `invoice_id` = $invoice_id;");
        }
        $nstr = $db->num_rows($rstr);

        if ($nstr > 0) {

            if ($doc_type_id === 2) {
                $r = $db->query("SELECT `summ` * 100 as `sum100` FROM `J_BACK_CLIENTS` WHERE `id` = $invoice_id LIMIT 1;");
            } else {
                $r = $db->query("SELECT `summ` * 100 as `sum100` FROM `J_SALE_INVOICE` WHERE `id` = $invoice_id LIMIT 1;");
            }

            $sum = (int)$db->result($r, 0, "sum100");
            //$sum *= 100;
            //$sum = ceil($sum);

            $arr = [];
            for ($i = 1; $i <= $nstr; $i++) {
                $art_id         = (int)$db->result($rstr, $i - 1, "art_id");
                $article_name   = $this->getArticleName($art_id);
                $brand_id       = (int)$db->result($rstr, $i - 1, "brand_id");
                $brand_name     = $this->getBrandName($brand_id);

                $article_nr_ds  = $db->result($rstr, $i - 1, "article_nr_displ");
                $article_nr_ds  = iconv("windows-1251", "UTF-8", $article_nr_ds);
                $article_nr_ds  = str_replace(str_split('.+\/:*?"<>|!?'), "", $article_nr_ds);

                $article_text   = $article_name . " " . $brand_name;
                $article_text   = iconv("windows-1251", "UTF-8", $article_text);
                $article_text   = str_replace(str_split('.+\/:*?"<>|!?'), "", $article_text);

                $amount         = (int)$db->result($rstr, $i - 1, "amount1000");
                $price          = (int)$db->result($rstr, $i - 1, "price100");

                $return = false;
                if ($doc_type_id === 2) {
                    $return = true;
                }

                //$price *= 100;
                //$amount *= 1000;
                //$price = ceil($price);
                //$amount = ceil($amount);

                $arr[] = new \igorbunov\Checkbox\Models\Receipts\Goods\GoodItemModel(
                    new \igorbunov\Checkbox\Models\Receipts\Goods\GoodModel(
                        $article_nr_ds,
                        $price,
                        $article_text
                    ),
                    $amount,
                    NULL,
                    NULL,
                    $return
                );
            }

            try {
                $answer = 1; $err = "";
                $arr_pay = [];

                if ($payment_id === 1) {
                    $arr_pay[] = new \igorbunov\Checkbox\Models\Receipts\Payments\CashPaymentPayload(
                        $sum
                    );
                }
                if ($payment_id === 2) {
                    $arr_pay[] = new \igorbunov\Checkbox\Models\Receipts\Payments\CardPaymentPayload(
                        $sum
                    );
                }

                $receipt = new \igorbunov\Checkbox\Models\Receipts\SellReceipt(
                    $cashier_name,
                    $department,
                    new \igorbunov\Checkbox\Models\Receipts\Goods\Goods(
                        $arr
                    ),
                    $email,
                    new \igorbunov\Checkbox\Models\Receipts\Payments\Payments(
                        $arr_pay
                    )
                );

            } catch (\igorbunov\Checkbox\Errors\NoActiveShift $err) {
                $answer = 0; $err = "Для проведення поточного фіскального чеку на повернення в касі не вистачає коштів. Зробіть службове внесення коштів, або наторгуйте";
            } catch (Exception $e) {
                $answer = 0; $err = "err";
            }

        }

        return array($answer, $err, $receipt);
    }

    public function showCbCheckForm($invoice_id, $doc_type_id)
    {
        $db = DbSingleton::getDb();
        $doc_type_id = (int)$doc_type_id;

        $form = ""; $form_htm = RD . "/tpl/sale_invoice_checkbox_select.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }

        $list = $list_p = "";

        if ($doc_type_id === 2) {
            $r = $db->query("SELECT * FROM `J_BACK_CLIENTS` WHERE `id` = $invoice_id LIMIT 1;");
            $invoice_id_sel = $db->result($r, 0, "sale_invoice_id");

            $r = $db->query("SELECT * FROM `PRRO_SALE_INVOICE` WHERE `INVOICE_ID` = $invoice_id_sel AND `DOC_TYPE_ID` = 1 LIMIT 1;");
            $n = $db->num_rows($r);
            if ($n > 0) {
                $payment_id_sel = (int)$db->result($r, 0, "PAY_TYPE_ID");
                $email_sel      = $db->result($r, 0, "EMAIL");

                $list = "
                <input type=\"radio\" id=\"email_sel\"
                 name=\"cb_contact\" value=\"$email_sel\" onclick='setEmail(this)'>
                <label for=\"email_sel\">$email_sel</label>
                <br>";
                if ($email_sel === "") {
                    $list = "";
                }

                $payment_name = ($payment_id_sel === 1) ? "Готівка" : "Картка";
                $list_p = "
                <input type=\"radio\" id=\"payment$payment_id_sel\" name=\"cb_payment\" value=\"$payment_id_sel\" checked>
                <label for=\"payment$payment_id_sel\">$payment_name</label>
                <br>";
            }
        }

        if ($doc_type_id === 1) {
            $r = $db->query("SELECT `client_id` FROM `J_SALE_INVOICE` WHERE `id` = $invoice_id LIMIT 1;");
            $client_id = $db->result($r, 0, "client_id");

            $list = "<div>";
            $r = $db->query("SELECT * FROM `A_CLIENTS_USERS` WHERE `client_id` = $client_id");
            $n = $db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $user_id    = $db->result($r, $i - 1, "id");
                $email      = $db->result($r, $i - 1, "email");

                $list .= "
                <input type=\"radio\" id=\"$user_id\"
                 name=\"cb_contact\" value=\"$email\" onclick='setEmail(this)'>
                <label for=\"$user_id\">$email</label>
                <br>";
            }
            $list .= "</div>";

            $list_p = "
            <input type=\"radio\" id=\"payment1\" name=\"cb_payment\" value=\"1\" checked>
            <label for=\"payment1\">Готівка</label>
            <br>
            <input type=\"radio\" id=\"payment2\" name=\"cb_payment\" value=\"2\">
            <label for=\"payment2\">Картка</label>";
        }

        $form = str_replace(array("{radio_email_range}", "{radio_payment_range}"), array($list, $list_p), $form);

        return $form;
    }

    public function addCbCheck($invoice_id, $doc_type_id = 1, $payment_id = 1, $email = ""): array
    {
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $db = DbSingleton::getDb();
        $answer = 0; $err = "";
        $payment_id = (int)$payment_id;

        $checkData = $this->checkCBCheckStr($invoice_id, $doc_type_id);

        if ($checkData["status"] === 0) {

            // 1. API CHECKBOX
            require(RD . "/checkbox-in-ua-php-sdk/vendor/autoload.php");

            $paybox = new paybox();
            $prro_id = $paybox->getCbTpoint();
            $dataPrro = $paybox->getUserPrroMain($prro_id);

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

                $r = $db->query("SELECT `CB_FNAME` FROM `PRRO_CASHIERS` WHERE `USER_ID` = $user_id AND `STATUS` = 1 LIMIT 1;");
                $n = $db->num_rows($r);

                if ($n > 0) {
                    $cashier_name   = $db->result($r, 0, "CB_FNAME");
                    $cashier_name   = iconv("windows-1251", "UTF-8", $cashier_name);
                    $department     = "";

                    [$a, $e, $receipt] = $this->getCbCheckStr($invoice_id, $doc_type_id, $cashier_name, $department, $payment_id, $email);
                    $answer = 0; $err = $e;

                    if ($a === 1) {
                        try {
                            $arr = $api->createSellReceipt($receipt);
                            $arr = json_decode(json_encode($arr), true);

                            $check_id   = $arr["id"];
                            $total      = $arr["total_sum"];
                            $data       = date('Y-m-d H:i:s', strtotime($arr['created_at']));
                            $fnumber    = $arr["fiscal_code"];
                            $number     = $arr["serial"];
                            $payments   = [];

                            foreach ($arr["payments"]["results"] as $item) {
                                $payments[] = iconv("UTF-8", "windows-1251", $item["label"]);
                            }
                            $payments = implode(",", $payments);

                            if ($payment_id === 2) {
                                $payments = "Безготівковий";
                            }

                            // 2. PRRO_SALE_INVOICE
                            $db->query("INSERT INTO `PRRO_SALE_INVOICE` (`CHECK_ID`, `INVOICE_ID`, `DOC_TYPE_ID`, `PAY_TYPE_ID`, `TYPE`, `PAYMENT`, `FNUMBER`, `NUMBER`, `DATA`, `TOTAL`, `EMAIL`, `STATUS`)
                            VALUES ('$check_id', $invoice_id, $doc_type_id, $payment_id, 1, '$payments', '$fnumber', $number, '$data', '$total', '$email', 1);");

                            $answer = 1; $err = "";

                        } catch (\igorbunov\Checkbox\Errors\NoActiveShift $err) {
                            $answer = 0; $err = "Для проведення поточного фіскального чеку на повернення в касі не вистачає коштів. Зробіть службове внесення коштів, або наторгуйте";
                        }
                    }
               }
            }

        } else {
            $nonames    = $checkData["nonames"];
            $nonames    = implode(",", $nonames);
            $answer     = 0;
            $err        = "Артикули: $nonames без українського найменування, спочатку додайте найменування!";
        }

        return array($answer, $err);
    }

    public function showCbCheck($invoice_id, $doc_type_id = 1): array
    {
        $check_id = 0;
        $answer = 0;
        $err = "Не існує чека до вказаної накладної!";
        $db = DbSingleton::getDb();

        $r = $db->query("SELECT `CHECK_ID` FROM `PRRO_SALE_INVOICE` WHERE `INVOICE_ID` = $invoice_id AND `DOC_TYPE_ID` = $doc_type_id LIMIT 1;");
        $n = $db->num_rows($r);

        if ($n > 0) {
            $check_id = $db->result($r, 0, "CHECK_ID");
            $answer = 1; $err = "";
        }

        return array($answer, $err, $check_id);
    }

    public function savePartitionsInvoiceAmount($partition_id, $invoice_amount): bool
    {
        $db = DbSingleton::getDb();
        if ($partition_id > 0) {
            $db->query("UPDATE `J_SALE_INVOICE_PARTITION_STR` SET `invoice_amount` = $invoice_amount WHERE `id` = $partition_id;");
        }
        return true;
    }

    public function getPartitionsInvoiceAmount($partition_id): array
    {
        $db = DbSingleton::getDb();
        $invoice_amount = "";
        $invoice_id     = 0;

        if ($partition_id > 0) {
            $r = $db->query("SELECT `invoice_amount`, `invoice_id` FROM `J_SALE_INVOICE_PARTITION_STR` WHERE `id` = $partition_id LIMIT 1;");
            $invoice_amount = $db->result($r, 0, "invoice_amount");
            $invoice_id     = $db->result($r, 0, "invoice_id");
        }

        return array($partition_id, $invoice_amount, $invoice_id);
    }

    public function loadSaleInvoicePartitions($invoice_id)
    {
        $db = DbSingleton::getDb();
        $income = new income;
        $cat = new catalogue;
        $prev_doc_id = 0;
        $list = ""; $doc_name = "";
        $form = ""; $form_htm = RD . "/tpl/sale_invoice_partitions_list.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }

        $r = $db->query("SELECT ps.*, ap.parrent_type_id, ap.parrent_doc_id 
        FROM `J_SALE_INVOICE_PARTITION_STR` ps
            LEFT OUTER JOIN `T2_ARTICLES_PARTITIONS` ap ON (ap.id = ps.partition_id)
        WHERE ps.status = 1 AND ps.invoice_id = '$invoice_id' 
        ORDER BY ps.id ASC;");
        $n = (int)$db->num_rows($r);

        for ($i = 1; $i <= $n; $i++) {
            $id                 = $db->result($r, $i - 1, "id");
            $parrent_type_id    = (int)$db->result($r, $i - 1, "parrent_type_id");
            $parrent_doc_id     = (int)$db->result($r, $i - 1, "parrent_doc_id");
            $article_nr_displ   = $db->result($r, $i - 1, "article_nr_displ");
            $brand_id           = $db->result($r, $i - 1, "brand_id");
            $partition_amount   = $db->result($r, $i - 1, "partition_amount");
            $invoice_amount     = $db->result($r, $i - 1, "invoice_amount");
            $op_partition       = $db->result($r, $i - 1, "oper_price_partition");
            $price_partition    = $db->result($r, $i - 1, "price_partition");
            $price_buh_uah      = $db->result($r, $i - 1, "price_buh_uah");
            $price_man_uah      = $db->result($r, $i - 1, "price_man_uah");
            $price_invoice      = $db->result($r, $i - 1, "price_invoice");
            $brand_name         = $cat->getBrandName($brand_id);

            if ($i === 1) {
                $doc_name = "";
            }

            if (($parrent_type_id === 1) && $parrent_doc_id !== $prev_doc_id) {
                $doc_name       = "" . $income->getIncomeDocNom($parrent_doc_id);
                $prev_doc_id    = $parrent_doc_id;
            }

            $list .= "
            <tr id='strStsRow_$i' onclick='getPartitionsInvoiceAmount(\"$id\")' style='cursor:pointer;'>
                <td align='center'>$i</td>
                <td align='center'>$doc_name</td>
                <td align='center'>$article_nr_displ</td>
                <td align='center'>$brand_name</td>
                <td align='right'>$partition_amount</td>
                <td align='right'>$invoice_amount</td>
                <td align='right'>$price_partition</td>
                <td align='right'>$op_partition</td>
                <td align='right'>$price_buh_uah</td>
                <td align='right'>$price_man_uah</td>
                <td align='right'>$price_invoice</td>
            </tr>";
        }

        if ($n === 0) {
            $list = "<tr><td colspan=8 align='center'>Записи відсутні</td></tr>";
        }

        $form = str_replace(array("{partitions_list}", "{invoice_id}"), array($list, $invoice_id), $form);

        return $form;
    }

    //===============			MONEY PAY 		==================================

    public function loadSaleInvoiceMoneyPay($invoice_id)
    {
        $db = DbSingleton::getDb();
        $list = "";
        $form = ""; $form_htm = RD . "/tpl/sale_invoice_money_pay_list.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }

        $r = $db->query("SELECT pay.*, pt.mcaption as pay_type_caption, pb.name as paybox_name, c.abr 
        FROM `J_PAY` pay
            LEFT OUTER JOIN `J_PAY_STR` pst ON (pst.pay_id = pay.id)
            LEFT OUTER JOIN `CASH` c ON (c.id = pay.cash_id)
            LEFT OUTER JOIN `T_POINT_PAY_BOX` pb ON (pb.id = pay.paybox_id)
            LEFT OUTER JOIN `manual` pt ON (pt.key = 'pay_type_id' AND pt.id = pay.pay_type_id)
        WHERE pay.status = 1 AND pst.parrent_doc_id = '$invoice_id' 
        GROUP BY pay.id 
        ORDER BY pay.data_time DESC, pay.id DESC;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $data_time  = $db->result($r, $i - 1, "data_time");
            $pay_cap    = $db->result($r, $i - 1, "pay_type_caption");
            $pay_name   = $db->result($r, $i - 1, "paybox_name");
            $doc_nom    = $db->result($r, $i - 1, "doc_nom");
            $summ       = $db->result($r, $i - 1, "summ");
            $cash_name = $db->result($r, $i - 1, "abr");
            $user_name = $this->getMediaUserName($db->result($r, $i - 1, "user_id"));

            $list .= "
            <tr id='strStsRow_$i'>
                <td align='center'>$i</td>
                <td align='center'>$data_time</td>
                <td>$pay_cap</td>
                <td align='center' style='min-width:140px;'>$doc_nom</td>
                <td align='center' style='min-width:140px;'>$pay_name</td>
                <td align='right' style='min-width:120px;'>$summ $cash_name</td>
                <td>$user_name</td>
            </tr>";
        }

        if ($n === 0) {
            $list = "<tr><td colspan=7 align='center'>Документи оплати відсутні</td></tr>";
        }

        $form = str_replace(array("{money_pay_list}", "{invoice_id}"), array($list, $invoice_id), $form);

        return $form;
    }

    public function showSaleInvoceMoneyPayForm($invoice_id, $pay_id)
    {
        $db = DbSingleton::getDb();
        $gmanual = new gmanual;
        $form = ""; $form_htm = RD . "/tpl/sale_invoice_money_pay_form.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }

        $r = $db->query("SELECT `cash_id`, `tpoint_id`, `seller_id`, `summ`, `summ_debit` FROM `J_SALE_INVOICE` WHERE `id` = '$invoice_id' LIMIT 1;");
        $cash_id    = $db->result($r, 0, "cash_id");
        $tpoint_id  = $db->result($r, 0, "tpoint_id");
        $seller_id  = $db->result($r, 0, "seller_id");
        $summ       = $db->result($r, 0, "summ");
        $summ_debit = $db->result($r, 0, "summ_debit");
        $cash_name  = $this->getCashAbr($cash_id);
        $summ_kred  = $summ - ($summ - $summ_debit);

        $cash_kours = "";
        if ((int)$pay_id === 0) {
            $print_pay_disabled = "disabled";
            $cash_kours = "1";
        } else {
            $print_pay_disabled = "";
        }

        $form = str_replace("{invoice_id}", $invoice_id, $form);
        $form = str_replace("{pay_id}", $pay_id, $form);
        $form = str_replace("{doc_cash_id}", $cash_id, $form);
        $form = str_replace("{cash_name}", $cash_name, $form);
        $form = str_replace("{print_pay_disabled}", $print_pay_disabled, $form);
        $form = str_replace("{sale_invoice_summ}", $summ, $form);
        $form = str_replace("{sale_invoice_debit}", $summ_debit, $form);
        $form = str_replace("{sale_invoice_kredit}", $summ_kred, $form);
        $form = str_replace("{cash_kours}", $cash_kours, $form);
        $form = str_replace("{paybox_list}", $this->showTpointPayBoxSelectList($seller_id, $tpoint_id), $form);
        $form = str_replace("{pay_type_list}", $gmanual->showGmanualSelectList('pay_type_id', '89'), $form);
        $form = str_replace("{cash_list}", $this->showCashListSelect($cash_id), $form);
        $form = str_replace("{pay_type_id_disabled}", "disabled", $form);

        return $form;
    }

    public function getCashAbr($cash_id)
    {
        $db = DbSingleton::getDb();
        $name = "";
        $r = $db->query("SELECT `abr` FROM `CASH` WHERE `id` = '$cash_id' LIMIT 1;");
        $n = (int)$db->num_rows($r);
        if ($n === 1) {
            $name = $db->result($r, 0, "abr");
        }
        return $name;
    }

    public function showCashListSelect($sel_id, $ns = ""): string
    {
        $db = DbSingleton::getDb();
        if (empty($ns)) {
            $ns = 1;
        }
        $list = "";

        $r = $db->query("SELECT `id`, `abr`, `name` FROM `CASH` ORDER BY `name` ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id     = $db->result($r, $i - 1, "id");
            $name   = ($ns == 2) ? $db->result($r, $i - 1, "name") : $db->result($r, $i - 1, "abr");
            $sel    = ($sel_id == $id) ? "selected='selected'" : "";
            $list   .= "<option value='$id' $sel>$name</option>";
        }
        return $list;
    }

    public function unlockSaleInvoiceMoneyPayKours($invoice_id, $pay_id): array
    {
        session_start();
        $user_id = (int)$_SESSION["media_user_id"];
        $answer = 0; $err = "Помилка збереження даних!";

        if (empty($invoice_id)) {
            $err = "Не вказано номер накладної для оплати";
            $answer = 0;
        }

        if ($invoice_id > 0 && (int)$pay_id === 0) {
            if ($user_id === 1) {
                $err = "";
                $answer = 1;
            } else {
                $err = "Впевнені?";
                $answer = 0;
            }
        }

        if ($invoice_id > 0 && $pay_id > 0) {
            $err = "Оплату проведено. Зміну курсу заблоковано";
            $answer = 0;
        }

        return array($answer, $err);
    }

    public function showTpointPayBoxSelectList($client_id, $tpoint_id): string
    {
        $db = DbSingleton::getDb();
        $list = "";
        $r = $db->query("SELECT `id`, `name` FROM `T_POINT_PAY_BOX` WHERE `client_id` = '$client_id' AND `tpoint_id` = '$tpoint_id' ORDER BY `name` ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id     = $db->result($r, $i - 1, "id");
            $name   = $db->result($r, $i - 1, "name");
            $sel    = ($i === 1) ? "selected='selected'" : "";
            $list   .= "<option value='$id' $sel>$name</option>";
        }
        return $list;
    }

    public function getCashKoursSaleInvoiceMoneyPay($doc_cash_id, $cash_id)
    {
        $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка!";
        $cash_id = (int)$slave->qq($cash_id);
        $doc_cash_id = (int)$doc_cash_id;
        $kours_value = $kours_usd = $kours_eur = 1;
        if (empty($cash_id)) {
            $answer = 0; $err = "Не вказано валюту";
        }
        if ($cash_id > 0) {
            if ($doc_cash_id === $cash_id) {
                $kours_value = 1;
                $answer = 1; $err = "";
            }
            if ($doc_cash_id !== $cash_id) {
                $r = $db->query("SELECT `kours_value` FROM `J_KOURS` WHERE `cash_id` = '2' AND `in_use` = '1' LIMIT 1;");
                $n = (int)$db->num_rows($r);
                if ($n === 1) {
                    $kours_usd = $db->result($r, 0, "kours_value");
                }

                $r = $db->query("SELECT `kours_value` FROM `J_KOURS` WHERE `cash_id` = '3' AND `in_use` = '1' LIMIT 1;");
                $n = (int)$db->num_rows($r);
                if ($n === 1) {
                    $kours_eur = $db->result($r, 0, "kours_value");
                }

                if ($doc_cash_id === 1 && $cash_id === 1) {$kours_value=1;}
                if ($doc_cash_id === 1 && $cash_id === 2) {$kours_value=$kours_usd;}
                if ($doc_cash_id === 1 && $cash_id === 3) {$kours_value=$kours_eur;}

                if ($doc_cash_id === 2 && $cash_id === 1) {$kours_value=$kours_usd;}
                if ($doc_cash_id === 2 && $cash_id === 2) {$kours_value=1;}
                if ($doc_cash_id === 2 && $cash_id === 3) {$kours_value=round($kours_eur/$kours_usd,2);}

                if ($doc_cash_id === 3 && $cash_id === 1) {$kours_value=$kours_eur;}
                if ($doc_cash_id === 3 && $cash_id === 2) {$kours_value=round($kours_eur/$kours_usd,2);}
                if ($doc_cash_id === 3 && $cash_id === 3) {$kours_value=1;}

                $answer = 1; $err = "";

                if ($n === 0) {
                    $kours_value = 1;
                    $answer = 1; $err = "";
                }
            }
        }
        return array($answer, $err, $kours_value);
    }

    public function saveSaleInvoiceMoneyPay($invoice_id, $pay_id, $kredit, $pay_type_id, $paybox_id, $doc_cash_id, $cash_id, $cash_kours): array
    {
        $db = DbSingleton::getDb();
        $slave = new slave;
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $answer = 0; $err = "Помилка збереження даних!";
        $invoice_client_id = 0;
        $invoice_doc_type_id = 0;
        $invoice_summ = $invoice_summ_debit = 0;
        $doc_nom = 0;
        $invoice_id = $slave->qq($invoice_id);
        $pay_id = (int)$pay_id;
        $cash_id = (int)$cash_id;
        $doc_cash_id = (int)$doc_cash_id;
        if (empty($invoice_id)) {
            $err = "Не вказано номер накладної для оплати"; $answer = 0;
        }

        if ($invoice_id > 0 && $pay_id === 0) {
            $pay_id         = $slave->qq($pay_id);
            $kredit         = $slave->qq($kredit);
            $pay_type_id    = (int)$slave->qq($pay_type_id);
            $paybox_id      = $slave->qq($paybox_id);

            if ($pay_id === 0) {
                $r = $db->query("SELECT MAX(`id`) as mid FROM `J_PAY`;");
                $pay_id = $db->result($r, 0, "mid") + 1;
                $r = $db->query("SELECT MAX(`doc_nom`) as doc_nom FROM `J_PAY` WHERE `paybox_id` = '$paybox_id';");
                $doc_nom = $db->result($r, 0, "doc_nom") + 1;
                $r = $db->query("SELECT * FROM `J_SALE_INVOICE` WHERE `id` = '$invoice_id';");
                $n = (int)$db->num_rows($r);
                if ($n === 1) {
                    $invoice_summ           = $db->result($r, 0, "summ");
                    $invoice_summ_debit     = $db->result($r, 0, "summ_debit");
                    $invoice_doc_type_id    = $db->result($r, 0, "doc_type_id");
                    $invoice_client_id      = $db->result($r, 0, "client_id");
                }
            }

            if ($pay_id > 0 && $kredit > 0 && $pay_type_id === 89 && $paybox_id > 0) {
                [$balans_before] = $this->getClientGeneralSaldo($invoice_client_id);
                $doc_sum_pay = 0;

                if ($doc_cash_id === $cash_id) {
                    $doc_sum_pay = $kredit;
                }

                if ($doc_cash_id !== $cash_id) {
                    if ($doc_cash_id==1 && $cash_id==2){$doc_sum_pay=$cash_kours*$kredit;}
                    if ($doc_cash_id==1 && $cash_id==3){$doc_sum_pay=$cash_kours*$kredit;}

                    if ($doc_cash_id==2 && $cash_id==1){$doc_sum_pay=round($kredit/$cash_kours,2);}
                    if ($doc_cash_id==2 && $cash_id==3){$doc_sum_pay=round($kredit*$cash_kours,2);}

                    if ($doc_cash_id==3 && $cash_id==1){$doc_sum_pay=round($kredit/$cash_kours,2);}
                    if ($doc_cash_id==3 && $cash_id==2){$doc_sum_pay=round($kredit/$cash_kours,2);}
                }

                if ($invoice_summ_debit >= $doc_sum_pay) { // if sum pay less then invoice summ
                    $db->query("INSERT INTO `J_PAY` (`id`,`pay_type_id`,`paybox_id`,`doc_nom`,`cash_id`,`summ`,`user_id`) 
                    VALUES ('$pay_id','$pay_type_id','$paybox_id','$doc_nom','$cash_id','$kredit','$user_id');");
                    $db->query("INSERT INTO `J_PAY_STR` (`pay_id`,`parrent_doc_type_id`,`parrent_doc_id`,`summ_doc`,`doc_cash_id`,`summ_pay`,`pay_cash_id`,`pay_cash_kours`,`pay_cash_summ`) 
                    VALUES ('$pay_id','$invoice_doc_type_id','$invoice_id','$invoice_summ','$doc_cash_id','$doc_sum_pay','$cash_id','$cash_kours','$kredit');");
                    $new_summ_debit = $invoice_summ_debit - $doc_sum_pay;
                    if ($new_summ_debit < 0) {
                        $new_summ_debit = 0;
                    }
                    $db->query("UPDATE `J_SALE_INVOICE` SET `summ_debit`='$new_summ_debit' WHERE `id`='$invoice_id' LIMIT 1;");

                    $balans_after = $balans_before + $doc_sum_pay;
                    $db->query("INSERT INTO `B_CLIENT_BALANS_JOURNAL` (`client_id`,`cash_id`,`balans_before`,`deb_kre`,`summ`,`balans_after`,`doc_type_id`,`doc_id`) 
                    VALUES ('$invoice_client_id','$doc_cash_id','$balans_before','2','$doc_sum_pay','$balans_after','2','$pay_id');");
                    $db->query("UPDATE `B_CLIENT_BALANS` SET `saldo`=`saldo`+$doc_sum_pay, `last_update`=NOW() WHERE `client_id`='$invoice_client_id';");
                }

                if ($invoice_summ_debit < $doc_sum_pay) { // if sum pay more then invoice summ
                    $avans_summ = $doc_sum_pay - $invoice_summ_debit;
                    $kredit2 = $invoice_summ_debit;

                    if ($doc_cash_id !== $cash_id) {
                        if ($doc_cash_id == 1 && $cash_id == 2) {
                            $kredit2 = round($kredit2 / $cash_kours, 2);
                        }
                        if ($doc_cash_id == 1 && $cash_id == 3) {
                            $kredit2 = round($kredit2 / $cash_kours, 2);
                        }
                        if ($doc_cash_id == 2 && $cash_id == 1) {
                            $kredit2 = round($kredit2 * $cash_kours, 2);
                        }
                        if ($doc_cash_id == 2 && $cash_id == 3) {
                            $kredit2 = round($kredit2 / $cash_kours, 2);
                        }
                        if ($doc_cash_id == 3 && $cash_id == 1) {
                            $kredit2 = round($kredit2 * $cash_kours, 2);
                        }
                        if ($doc_cash_id == 3 && $cash_id == 2) {
                            $kredit2 = round($kredit2 * $cash_kours, 2);
                        }
                    }

                    $db->query("INSERT INTO `J_PAY` (`id`,`pay_type_id`,`paybox_id`,`doc_nom`,`cash_id`,`summ`,`user_id`) 
                    VALUES ('$pay_id','$pay_type_id','$paybox_id','$doc_nom','$cash_id','$kredit2','$user_id');");
                    $db->query("INSERT INTO `J_PAY_STR` (`pay_id`,`parrent_doc_type_id`,`parrent_doc_id`,`summ_doc`,`doc_cash_id`,`summ_pay`,`pay_cash_id`,`pay_cash_kours`,`pay_cash_summ`) 
                    VALUES ('$pay_id','$invoice_doc_type_id','$invoice_id','$invoice_summ','$doc_cash_id','$invoice_summ_debit','$cash_id','$cash_kours','$kredit2');");
                    $new_summ_debit = 0;
                    $db->query("UPDATE `J_SALE_INVOICE` SET `summ_debit`='$new_summ_debit' WHERE `id`='$invoice_id' LIMIT 1;");

                    $balans_after = $balans_before + $invoice_summ_debit;
                    $db->query("INSERT INTO `B_CLIENT_BALANS_JOURNAL` (`client_id`,`cash_id`,`balans_before`,`deb_kre`,`summ`,`balans_after`,`doc_type_id`,`doc_id`) 
                    VALUES ('$invoice_client_id','$doc_cash_id','$balans_before','2','$invoice_summ_debit','$balans_after','2','$pay_id');");
                    $db->query("UPDATE `B_CLIENT_BALANS` SET `saldo`=`saldo`+$invoice_summ_debit, `last_update`=NOW() WHERE `client_id`='$invoice_client_id';");
                    // end payment for invoice

                    //creating avans pay
                    $r = $db->query("SELECT MAX(`id`) as mid FROM `J_PAY`;");
                    $pay_id = $db->result($r, 0, "mid") + 1;
                    $r = $db->query("SELECT MAX(`doc_nom`) as doc_nom FROM `J_PAY` WHERE `paybox_id`='$paybox_id';");
                    $doc_nom = $db->result($r, 0, "doc_nom") + 1;

                    $db->query("INSERT INTO `J_PAY` (`id`,`pay_type_id`,`paybox_id`,`doc_nom`,`cash_id`,`summ`,`user_id`) 
                    VALUES ('$pay_id','$pay_type_id','$paybox_id','$doc_nom','$cash_id','$avans_summ','$user_id');");
                    $db->query("INSERT INTO `J_PAY_STR` (`pay_id`,`parrent_doc_type_id`,`parrent_doc_id`,`summ_doc`,`doc_cash_id`,`summ_pay`,`pay_cash_id`,`pay_cash_kours`,`pay_cash_summ`) 
                    VALUES ('$pay_id','0','0','$avans_summ','$cash_id','$avans_summ','$cash_id','1','$avans_summ');");

                    $balans_before = $balans_after;
                    $balans_after = $balans_before + $avans_summ;

                    $db->query("INSERT INTO `B_CLIENT_BALANS_JOURNAL` (`client_id`,`cash_id`,`balans_before`,`deb_kre`,`summ`,`balans_after`,`doc_type_id`,`doc_id`) 
                    VALUES ('$invoice_client_id','$cash_id','$balans_before','2','$avans_summ','$balans_after','3','$pay_id');");
                    $db->query("UPDATE `B_CLIENT_BALANS` SET `saldo`=`saldo`+$avans_summ, `last_update`=NOW() WHERE `client_id`='$invoice_client_id';");
                }
                $answer = 1; $err = "";
            }
        }

        return array($answer, $err, $pay_id);
    }

    public function getClientGeneralSaldo($sel_id): array
    {
        $db = DbSingleton::getDb();
        $saldo = "0";
        $cash_id = 1;
        $r = $db->query("SELECT `saldo`, `cash_id` FROM `B_CLIENT_BALANS` WHERE `client_id` = '$sel_id' LIMIT 1;");
        $n = (int)$db->num_rows($r);
        if ($n === 1) {
            $saldo = $db->result($r, 0, "saldo");
            $cash_id = $db->result($r, 0, "cash_id");
        }
        return array($saldo, $cash_id);
    }

    //===============			MONEY PAY 		==================================

    /*TEST*/
    public function getSellerDetails($client_id, $seller_id): array
    {
        $db = DbSingleton::getDb();
        $r = $db->query("SELECT * FROM `A_CLIENTS_CONDITIONS` WHERE `client_id` = '$client_id' LIMIT 1;");
        $detail_id = $db->result($r, 0, "detail_id");
        if ($detail_id > 0) {
            $r = $db->query("SELECT * FROM `A_CLIENT_DETAILS` WHERE `id` = '$detail_id' LIMIT 1;");
        } else {
            $r = $db->query("SELECT * FROM `A_CLIENT_DETAILS` WHERE `client_id` = '$seller_id' AND `main` = 1 LIMIT 1;");
        }
        $edrpou     = $db->result($r, 0, "edrpou");
        $account    = $db->result($r, 0, "account");
        $bank       = $db->result($r, 0, "bank");
        $mfo        = $db->result($r, 0, "mfo");
        $vat        = $db->result($r, 0, "vytjag");

        return array($edrpou, $account, $bank, $mfo, $vat);
    }

    public function printBarcode($dp_id)
    {
        $list = "";
        $client = new clients();
        $dp = new dp();
        $db = DbSingleton::getDb();
        $dp_id = (int)$dp_id;

        $r = $db->query("SELECT `order_info_id` FROM `J_DP` WHERE `id` = $dp_id LIMIT 1;");
        $order_info_id = $db->result($r, 0, "order_info_id") + 0;

        $r = $db->query("SELECT * FROM `ORDERS_CLIENT_INFO` WHERE `ID` = $order_info_id LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n > 0) {
            $user_id            = $db->result($r, 0, "USER_ID");
            $del_name           = $db->result($r, 0, "DEL_NAME");
            $del_phone          = $db->result($r, 0, "DEL_PHONE");
            $city_id            = $db->result($r, 0, "CITY_ID");
            $delivery_id        = $db->result($r, 0, "DELIVERY_ID");
            $payment_id         = $db->result($r, 0, "PAYMENT_ID");
            $delivery_charge_id = $db->result($r, 0, "DELIVERY_CHARGE_ID");
            $del_street         = $db->result($r, 0, "DEL_STREET");
            $del_house          = $db->result($r, 0, "DEL_HOUSE");
            $del_porch          = $db->result($r, 0, "DEL_PORCH");
            $del_department     = $db->result($r, 0, "DEL_DEPARTMENT");
            $del_dep_text       = $db->result($r, 0, "DEL_DEPARTMENT_TEXT");
            $del_express        = $db->result($r, 0, "DEL_EXPRESS");
            $del_express_info   = $db->result($r, 0, "DEL_EXPRESS_INFO");
            $del_payment_info   = $db->result($r, 0, "DEL_EXPRESS_PAYMENT");

            if ($del_name === "") {
                $del_name = $client->getUserNameById($user_id);
            }
            if ($del_phone === "" || $del_phone === "0" || $del_phone === 0) {
                $del_phone = $client->getUserNameById($user_id, "phone");
            }

            if ($del_name !== "") {
                $list .= "<tr><td>Отримувач: $del_name</td></tr>";
            }
            if ($del_phone !== "") {
                $list .= "<tr><td>Телефон: $del_phone</td></tr>";
            }
            if ($city_id > 0) {
                $city_name = $client->getCityName($city_id);
                $list .= "<tr><td>Місто: $city_name</td></tr>";
            }
            if ($delivery_id > 0) {
                $delivery_name = $dp->getDeliveryName($delivery_id);
                $list .= "<tr><td>Тип доставки: $delivery_name</td></tr>";
            }
            if ($payment_id > 0) {
                $payment_name = $dp->getPaymentName($payment_id);
                $dp_summ = "";
                if ($payment_id === 2 || $payment_id === "2") {
                    $dp_summ = $dp->getDpSumm($dp_id);
                    $dp_summ_express = $dp->getDpExpressPayment($dp_id);
                    if ($dp_summ_express > 0) {
                        $dp_summ = $dp_summ_express;
                    }
                }
                $list .= "<tr><td>Оплата замовлення: $payment_name $dp_summ</td></tr>";
            }
            if ($delivery_charge_id > 0) {
                $delivery_charge_name = $dp->getDeliveryChargeName($delivery_charge_id);
                $list .= "<tr><td>Оплата доставки: $delivery_charge_name</td></tr>";
            }
            if ($del_street !== "") {
                $list .= "<tr><td>Вулиця: $del_street</td></tr>";
            }
            if ($del_house !== "") {
                $list .= "<tr><td>Будинок: $del_house</td></tr>";
            }
            if ($del_porch !== "") {
                $list .= "<tr><td>Під'їзд: $del_porch</td></tr>";
            }
            if ($del_department !== "" && $del_department !== "0" && $del_department !== 0) {
                $list .= "<tr><td>Відділення: $del_dep_text</td></tr>";
            }
            if ($del_express > 0) {
                $del_express_name = $dp->getExpressInfoName($del_express);
                $list .= "<tr><td>Тип ЕД: $del_express_name</td></tr>";
            }
            if ($del_express_info !== "") {
                $list .= "<tr><td>Інформація ЕД: $del_express_info</td></tr>";
            }
            if ($del_payment_info !== "") {
                $list .= "<tr><td>Післяплати сума оплати: $del_express_info</td></tr>";
            }
        }

        $form = "";
        $form_htm = RD . "/tpl/sale_invoice_print_barcode.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $form = str_replace("{delivery_range}", $list, $form);
        $mp = new media_print;
        $mp->print_document_barcode($form, [80, 60]);

        return $form;
    }

    public function printSaleInvoice($invoice_id, $type)
    {
        if (empty($type)) {
            $type = 1;
        }
        $db = DbSingleton::getDb();
        session_start();
        $ses_tpoint_id = $_SESSION["media_tpoint_id"];

        $cat    = new catalogue;
        $slave  = new slave;
        $money  = new toMoney;

        $invoice_summ = 0;
        $form = ""; $list = "";

        $r = $db->query("SELECT sv.*, t.name as tpoint_name, sl.full_name as seller_name, sld.vytjag, sld.edrpou, sld.account, sld.bank, sld.mfo, ot.name as org_type_abr, 
        cl.name as client_name, dt.mcaption as doc_type_name, dp.prefix as dp_prefix, ss.select_id, sv.dp_id, dt.mvalue as doc_type_abr, ch.abr2 as cash_abr, dp.delivery_address 
        FROM `J_SALE_INVOICE` sv
            LEFT OUTER JOIN `CASH` ch on ch.id=sv.cash_id
            LEFT OUTER JOIN `T_POINT` t on t.id=sv.tpoint_id
            LEFT OUTER JOIN `A_CLIENTS` sl on sl.id=sv.seller_id
            LEFT OUTER JOIN `A_CLIENT_DETAILS` sld on (sld.client_id=sv.seller_id and sld.main=1)
            LEFT OUTER JOIN `A_ORG_TYPE` ot on ot.id=sl.org_type
            LEFT OUTER JOIN `J_DP` dp on dp.id=sv.dp_id
            LEFT OUTER JOIN `J_SALE_INVOICE_STORSEL` ss on ss.dp_id=sv.dp_id
            LEFT OUTER JOIN `A_CLIENTS` cl on cl.id=sv.client_conto_id
            LEFT OUTER JOIN `manual` dt on (dt.key='client_sale_type' and dt.id=sv.doc_type_id)
        WHERE sv.status = 1 AND sv.id = '$invoice_id' LIMIT 1;");
        $n = (int)$db->num_rows($r);

        if ($n === 1) {
            $prefix         = $db->result($r, 0, "prefix");
            $doc_nom        = $db->result($r, 0, "doc_nom");
            $data_create    = $db->result($r, 0, "data_create");
            $tpoint_name    = $db->result($r, 0, "tpoint_name");
            $seller_id      = $db->result($r, 0, "seller_id");
            $seller_name    = $db->result($r, 0, "seller_name");
            $org_type_abr   = $db->result($r, 0, "org_type_abr");
            $client_id      = $db->result($r, 0, "client_id");
            $client_cont_id = $db->result($r, 0, "client_conto_id");
            $client_name    = $db->result($r, 0, "client_name");
            $doc_type_id    = (int)$db->result($r, 0, "doc_type_id");

            [$edrpou, $account, $bank, $mfo, $vat] = $this->getSellerDetails($client_cont_id, $seller_id);

            $dp_id          = $db->result($r, 0, "dp_id");
            $doc_type_name  = $db->result($r, 0, "doc_type_name");
            $select_id      = $db->result($r, 0, "select_id");
            $cash_id        = $db->result($r, 0, "cash_id");
            $cash_abr       = $db->result($r, 0, "cash_abr");
            $data_pay       = $db->result($r, 0, "data_pay");
            $del_address    = $db->result($r, 0, "delivery_address");

            $sel_ar[$select_id] = $select_id;

            $r = $db->query("SELECT * FROM `J_SALE_INVOICE_STR` WHERE `invoice_id` = '$invoice_id' ORDER BY `id` ASC;");
            $n = $db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $art_id         = $db->result($r, $i - 1, "art_id");
                $article_name   = $cat->getArticleNameLang($art_id);
                $art_nr_ds      = $db->result($r, $i - 1, "article_nr_displ");
                $brand_id       = $db->result($r, $i - 1, "brand_id");
                $brand_name     = $cat->getBrandName($brand_id);
                $amount         = $db->result($r, $i - 1, "amount");
                $unit           = $this->getUnitArticle($art_id);
                $price_end      = $db->result($r, $i - 1, "price_end");
                $summ           = $db->result($r, $i - 1, "summ");
                $zed            = $this->getZedArticle($art_id);
                //$price_bez      = round($price_end / 1.2, 2);
                //$summ_bez       = $price_bez * $amount;
                $invoice_summ   += $summ;
                //$inv_summ_bez   += $summ_bez;
                $zed_row        = ($doc_type_id === 61) ? "<td align='left'>$zed</td>" : "";

                $list .= "
                <tr>
                    <td align='center'>$i</td>
                    <td align='left' width='120'>$art_nr_ds($brand_name)</td>
                    $zed_row
                    <td align='left' width='250'>$article_name</td>
                    <td align='center'>$unit</td>
                    <td align='center'>$amount</td>
                    <td align='right'>$price_end</td>
                    <td align='right'>$summ</td>
                </tr>";
            }

            $vat_summ = $invoice_summ / 6;

            $storsel_list = "СКВ-";
            foreach ($sel_ar as $slr) {
                $storsel_list .= "$slr ";
            }

            $form = ""; $form_htm = ""; // $max_row = 40;
            $ses_tpoint_name = $this->getTpointFullName($ses_tpoint_id);

            if ($doc_type_id === 64) {$form_htm=RD."/tpl/dp_sale_invoice_print_64.htm";} //БК
            if ($doc_type_id === 63) {$form_htm=RD."/tpl/dp_sale_invoice_print_63.htm";} //ТЧ
            if ($doc_type_id === 61) {$form_htm=RD."/tpl/dp_sale_invoice_print_61.htm";} //БН

            //            if ($n > $max_row && $doc_type_id == 64) {$form_htm=RD."/tpl/dp_sale_invoice_print_64_a4.htm";}
            if ($type == 1 && $doc_type_id === 64) {$form_htm=RD."/tpl/dp_sale_invoice_print_64_a4.htm";}
            if ($type == 2 && $doc_type_id === 64) {$form_htm=RD."/tpl/dp_sale_invoice_print_64.htm";}

            if ($type == 1 && $doc_type_id === 63) {$form_htm=RD."/tpl/dp_sale_invoice_print_63_a4.htm";}
            if ($type == 2 && $doc_type_id === 63) {$form_htm=RD."/tpl/dp_sale_invoice_print_63.htm";}

            if ($type == 2 && $doc_type_id === 61) {$form_htm=RD."/tpl/dp_sale_invoice_print_61_a4.htm";}
            if ($type == 1 && $doc_type_id === 61) {$form_htm=RD."/tpl/dp_sale_invoice_print_61.htm";}

            if (file_exists($form_htm)){ $form = file_get_contents($form_htm);}

            [$mandate_nomber, $mandate_person, $mandate_data, $mandate_seria] = $this->getMandateData($client_id, $data_create);
            [$basis_nomber, $basis_date] = $this->getBasisData($client_id, $data_create);

            $data_create    = date("d.m.Y", strtotime($data_create));
            $data_pay       = date("d.m.Y", strtotime($data_pay));
            $mandate_data   = (!empty($mandate_data)) ? date("d.m.Y", strtotime($mandate_data)) : "";
            $basis_date     = date("d.m.Y", strtotime($basis_date));
            $basis_data     = ($basis_nomber !== "") ? "№$basis_nomber, від $basis_date" : "б/н";

            $vis_61 = "";
            if ($doc_type_id === 61) {
                $vis_61 = " disabled style=\"display:none;\"";
            }

            $form = str_replace("{oper_visible_no_61}",$vis_61,$form);
            $form = str_replace("{curtime}",date("d.m.Y H:i:s"),$form);
            $form = str_replace("{invoice_id}",$invoice_id,$form);
            $form = str_replace("{data}",$data_create,$form);
            $form = str_replace("{data_pay}",$data_pay,$form);
            $form = str_replace("{prefix}",$prefix,$form);
            $form = str_replace("{doc_nom}",$doc_nom,$form);
            $form = str_replace("{tpoint_name}",$tpoint_name,$form);
            $form = str_replace("{seller_name}",$seller_name,$form);
            $form = str_replace("{rr}",$account,$form);
            $form = str_replace("{bank}",$bank,$form);
            $form = str_replace("{mfo}",$mfo,$form);
            $form = str_replace("{dp_name}","ДП-$dp_id",$form);
            $form = str_replace("{dp_sale_invoice_storsel_list}",$storsel_list,$form);
            $form = str_replace("{mandate_person}",$mandate_person,$form);
            $form = str_replace("{mandate_nomber}",$mandate_nomber,$form);
            $form = str_replace("{mandate_data}",$mandate_data,$form);
            $form = str_replace("{mandate_seria}",$mandate_seria,$form);
            $form = str_replace("{basis_data}",$basis_data,$form);
            $form = str_replace("{client_id}",$client_cont_id,$form);
            $form = str_replace("{client_name}",$client_name,$form);
            $form = str_replace("{doc_type_name}",$doc_type_name,$form);
            $form = str_replace("{invoice_summ}",$slave->to_money($invoice_summ),$form);
            $form = str_replace("{invoice_summ_word}",($cash_id == 1) ? $money->num2str($slave->to_money($invoice_summ)) : "$invoice_summ $cash_abr" ,$form);
            $form = str_replace("{vat_summ}",$slave->to_money($vat_summ),$form);
            $form = str_replace("{cash_name}",$cash_abr,$form);
            $form = str_replace("{edrpou}",$edrpou,$form);
            $form = str_replace("{ipn_nom}",$vat,$form);
            $form = str_replace("{org_type_abr}",$org_type_abr,$form);
            $form = str_replace("{cash_abr}",$cash_abr,$form);

            if ($del_address === "") {
                $dp = new dp;
                $del_address = $dp->getDpNote($dp_id);
            }

            $form = str_replace("{delivery_address}", $del_address, $form);
            $form = str_replace("{sale_invoice_str_list}", $list, $form);
            $form = str_replace("{ses_tpoint_name}", $ses_tpoint_name, $form);

            //undefined - 63 - 20 - 40
            $mp = new media_print;
            if ($type == 1 && $doc_type_id === 64) {$mp->print_document($form,"A4");}
            if ($type == 2 && $doc_type_id === 64) {$mp->print_document($form,"A4-L");}
//            if ($n <= $max_row && $doc_type_id == 64) { $mp->print_document($form,"A4-L"); }
//            if ($n > $max_row && $doc_type_id == 64) { $mp->print_document($form,"A4"); }

            if ($type == 1 && $doc_type_id === 63) {$mp->print_document($form, "A4");}
            if ($type == 2 && $doc_type_id === 63) {$mp->print_document($form, "A4-L");}

            if ($type == 2 && $doc_type_id === 61) {$mp->print_document($form,"A4-L");}
            if ($type == 1 && $doc_type_id === 61) {$mp->print_document($form,"A4");}
        }

        return $form;
    }

    public function getUnitArticle($art_id)
    {
        $db = DbSingleton::getTokoDb();
        $abr = "";
        $r = $db->query("SELECT t2u.abr 
        FROM `T2_PACKAGING` t2p 
            LEFT OUTER JOIN `units` t2u on t2u.id=t2p.UNITS_ID
        WHERE t2p.ART_ID = '$art_id' LIMIT 1;");
        $n = (int)$db->num_rows($r);
        if ($n === 1) {
            $abr = $db->result($r, 0, "abr");
        }
        return $abr;
    }

    public function getZedArticle($art_id)
    {
        $db = DbSingleton::getTokoDb();
        $costums_code = "";
        $r = $db->query("SELECT t2s.COSTUMS_CODE 
        FROM `T2_ZED` t2z 
            LEFT OUTER JOIN `T2_COSTUMS` t2s on t2s.COSTUMS_ID=t2z.COSTUMS_ID
        WHERE t2z.ART_ID = '$art_id' LIMIT 1;");
        $n = (int)$db->num_rows($r);
        if ($n === 1) {
            $costums_code = $db->result($r, 0, "COSTUMS_CODE");
        }
        return $costums_code;
    }

    public function getMandateData($client_id, $data): array
    {
        $db = DbSingleton::getDb();
        $number = $seria = $receiver = $data_from = "";

        $r = $db->query("SELECT `number`, `receiver`, `data_from`, `seria` FROM `A_CLIENTS_MANDATE` 
        WHERE `status` = '1' AND `client_id` = '$client_id' AND `data_from` <= '$data' AND `data_to` >= '$data' LIMIT 1;");
        $n = (int)$db->num_rows($r);
        if ($n === 1) {
            $number     = $db->result($r, 0, "number");
            $receiver   = $db->result($r, 0, "receiver");
            $data_from  = $db->result($r, 0, "data_from");
            $seria      = $db->result($r, 0, "seria");
        }
        if ($seria !== "") {
            $seria = "Серія: $seria,";
        }

        return array($number, $receiver, $data_from, $seria);
    }

    public function getBasisData($client_id, $data): array
    {
        $db = DbSingleton::getDb();
        $number = $data_from = "";
        $r = $db->query("SELECT `number`, `data_from` FROM `A_CLIENTS_BASIS` 
        WHERE `status` = '1' AND `client_id` = '$client_id' AND `data_from` <= '$data' AND `data_to` >= '$data' LIMIT 1;");
        $n = (int)$db->num_rows($r);
        if ($n === 1) {
            $number = $db->result($r, 0, "number");
            $data_from = $db->result($r, 0, "data_from");
        }
        return array($number, $data_from);
    }

    public function printSaleInvoiceBuh($invoice_id)
    {
        $db = DbSingleton::getDb();
        $cat = new catalogue;
        $slave = new slave;
        $money = new toMoney;
        $invoice_summ = 0; $storage_id = 0;
        $list = ""; $form = ""; $form_htm = "";

        $r = $db->query("SELECT sv.*, t.name as tpoint_name, sl.name as seller_name, sld.edrpou, ot.name as org_type_abr, 
        cl.name as client_name, dt.mcaption as doc_type_name, dt.mvalue as doc_type_abr, ch.abr2 as cash_abr, dp.delivery_address 
        FROM `J_SALE_INVOICE` sv
            LEFT OUTER JOIN `CASH` ch on ch.id=sv.cash_id
            LEFT OUTER JOIN `T_POINT` t on t.id=sv.tpoint_id
            LEFT OUTER JOIN `A_CLIENTS` sl on sl.id=sv.seller_id
            LEFT OUTER JOIN `A_CLIENT_DETAILS` sld on (sld.client_id=sv.seller_id and sld.main=1)
            LEFT OUTER JOIN `A_ORG_TYPE` ot on ot.id=sl.org_type
            LEFT OUTER JOIN `J_DP` dp on dp.id=sv.dp_id
            LEFT OUTER JOIN `A_CLIENTS` cl on cl.id=sv.client_conto_id
            LEFT OUTER JOIN `manual` dt on dt.key='client_sale_type' AND dt.id=sv.doc_type_id
        WHERE sv.status = 1 AND sv.id = '$invoice_id' LIMIT 1;");
        $n = (int)$db->num_rows($r);

        if ($n === 1) {
            $prefix             = $db->result($r, 0, "prefix");
            $doc_nom            = $db->result($r, 0, "doc_nom");
            $data_create        = $db->result($r, 0, "data_create");
            $tpoint_name        = $db->result($r, 0, "tpoint_name");
            $seller_id          = $db->result($r, 0, "seller_id");
            $client_conto_id    = $db->result($r, 0, "client_conto_id");
            $seller_name        = $db->result($r, 0, "seller_name");
            [$edrpou]           = $this->getSellerDetails($client_conto_id, $seller_id);
            $org_type_abr       = $db->result($r, 0, "org_type_abr");
            $client_name        = $db->result($r, 0, "client_name");
            $doc_type_id        = (int)$db->result($r, 0, "doc_type_id");
            $doc_type_name      = $db->result($r, 0, "doc_type_name");
            $cash_id            = $db->result($r, 0, "cash_id");
            $cash_abr           = $db->result($r, 0, "cash_abr");
            $data_pay           = $db->result($r, 0, "data_pay");
            $delivery_address   = $db->result($r, 0, "delivery_address");

            $r = $db->query("SELECT * FROM `J_SALE_INVOICE_STR` WHERE `invoice_id` = '$invoice_id' ORDER BY `id` ASC;");
            $n = $db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $article_nr_displ   = $db->result($r, $i - 1, "article_nr_displ");
                $brand_id           = $db->result($r, $i - 1, "brand_id");
                $brand_name         = $cat->getBrandName($brand_id);
                $amount             = $db->result($r, $i - 1, "amount");
                $price_end          = $db->result($r, $i - 1, "price_end");
                $summ               = $db->result($r, $i - 1, "summ");
                $invoice_summ       += $summ;
                $list.="<tr>
                    <td align='center'>$i</td>
                    <td align='left'>$storage_id</td>
                    <td align='left'>$article_nr_displ</td>
                    <td align='center'>$brand_name</td>
                    <td align='center'>$amount</td>
                    <td align='right'>$price_end</td>
                    <td align='right'>$summ</td>
                </tr>";
            }

            $form="";
            if ($doc_type_id === 64){$form_htm=RD."/tpl/dp_sale_invoice_print_64.htm";} // БК
            if ($doc_type_id === 63){$form_htm=RD."/tpl/dp_sale_invoice_print_63.htm";} // ТЧ
            if ($doc_type_id === 61){$form_htm=RD."/tpl/dp_sale_invoice_print_61.htm";} // БН
            if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }

            $form = str_replace("{curtime}",date("d/m/Y H:i:s"),$form);
            $form = str_replace("{invoice_id}",$invoice_id,$form);
            $form = str_replace("{data}",$data_create,$form);
            $form = str_replace("{data_pay}",$data_pay,$form);
            $form = str_replace("{prefix}",$prefix,$form);
            $form = str_replace("{doc_nom}",$doc_nom,$form);
            $form = str_replace("{tpoint_name}",$tpoint_name,$form);
            $form = str_replace("{seller_name}",$seller_name,$form);
            $form = str_replace("{client_name}",$client_name,$form);
            $form = str_replace("{doc_type_name}",$doc_type_name,$form);
            $form = str_replace("{invoice_summ}",$slave->to_money($invoice_summ),$form);
            $form = str_replace("{invoice_summ_word}",$cash_id==1 ? $money->num2str($slave->to_money($invoice_summ)) : "$invoice_summ $cash_abr",$form);
            $form = str_replace("{cash_name}",$cash_abr,$form);
            $form = str_replace("{edrpou}",$edrpou,$form);
            $form = str_replace("{org_type_abr}",$org_type_abr,$form);
            $form = str_replace("{cash_abr}",$cash_abr,$form);
            $form = str_replace("{delivery_address}",$delivery_address,$form);
            $form = str_replace("{sale_invoice_str_list}",$list,$form);

            $mp = new media_print;
            if ($doc_type_id === 63){$mp->print_document($form,"A4-L");}
            if ($doc_type_id === 64){$mp->print_document($form,"A4-L");}
            if ($doc_type_id === 61){$mp->print_document($form,"A4-L");}
        }

        return $form;
    }

    public function exportSaleInvoiceExcel($invoice_id, $separator = ""): bool
    {
        $db = DbSingleton::getDb();
        $cat = new catalogue;
        $r = $db->query("SELECT sv.*, sl.name as seller_name, cl.name as client_name
        FROM `J_SALE_INVOICE` sv
            LEFT OUTER JOIN `A_CLIENTS` sl on sl.id=sv.seller_id
            LEFT OUTER JOIN `A_CLIENTS` cl on cl.id=sv.client_conto_id
        WHERE sv.status = 1 AND sv.id = '$invoice_id' LIMIT 1;");
        $n = (int)$db->num_rows($r);

        if ($n === 1) {
            $prefix      = $db->result($r, 0, "prefix");
            $doc_nom     = $db->result($r, 0, "doc_nom");
            $doc_type_id = (int)$db->result($r, 0, "doc_type_id");
            $data_create = $db->result($r, 0, "data_create");
            $seller_name = $db->result($r, 0, "seller_name");
            $client_name = $db->result($r, 0, "client_name");
            $list = array();

            $r = $db->query("SELECT * FROM `J_SALE_INVOICE_STR` WHERE `invoice_id`='$invoice_id' ORDER BY `id` ASC;");
            $n = $db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $art_id             = $db->result($r, $i - 1, "art_id");
                $article_nr_displ   = $db->result($r, $i - 1, "article_nr_displ");
                $brand_id           = $db->result($r, $i - 1, "brand_id");
                $amount             = $db->result($r, $i - 1, "amount");
                $price_end          = $db->result($r, $i - 1, "price_end");
                $summ               = $db->result($r, $i - 1, "summ");
                $zed                = $this->getZedArticle($art_id);
                $article_name       = $cat->getArticleNameLang($art_id);
                $brand_name         = $cat->getBrandName($brand_id);
               // $invoice_summ += $summ;

                if ($separator === "comma") {
                    $price_format = str_replace(".", ",", $price_end);
                    $summ_format = str_replace(".", ",", $summ);
                } else {
                    $price_format = $price_end;
                    $summ_format = $summ;
                }
                if ($doc_type_id === 61) {
                    $list[] = "$i;$article_nr_displ;$brand_name;$zed;$article_name;$amount;$price_format;$summ_format\n";
                } else {
                    $list[] = "$i;$article_nr_displ;$brand_name;$article_name;$amount;$price_format;$summ_format\n";
                }
            }

            $filename = $client_name ."_" . "$prefix-$doc_nom" . "_" . $data_create;
            $filename = str_replace(str_split('"«»'), '', $filename);
            $filename = str_replace("'", "", $filename);
            $filename = str_replace(str_split(" .,/"), "_", $filename);

            if ($doc_type_id === 61) {
                $header = "№п/п;Індекс;Бренд;Код УКТ ЗЕД;Найменування;К-сть;Ціна;Сума\n";
            } else {
                $header = "№п/п;Індекс;Бренд;Найменування;К-сть;Ціна;Сума\n";
            }
            header('Content-Type: text/csv; charset=utf-8');
            header("Content-Disposition: attachment; filename=$filename.csv");
            $output = fopen('php://output', 'wb');
            fputs($output, "Видакова накладна №$prefix-$doc_nom-$client_name від $data_create\n");
            fputs($output, "Продавець: $seller_name\n");
            fputs($output, "Покупець: $client_name\n");
            fputs($output, $header);
            foreach ($list as $row) {
                fwrite($output, $row);
            }
            exit(0);
        }

        return true;
    }

    public function getClientInvoiceCron($invoice_id): string
    {
        $db = DbSingleton::getDb();
        $list = "";
        $r = $db->query("SELECT `email`, `status` FROM `cron_client_invoice` WHERE `doc_id` = '$invoice_id' AND `doc_type` = 1;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $email      = $db->result($r, $i - 1, "email");
            $status     = (int)$db->result($r, $i - 1, "status");
            $status_cap = ($status === 1) ? "(Відправлено)" : "(Очікує на відправку)";
            $list       .= "$email $status_cap<br>";
        }
        if ($n === 0) {
            $list = "У користувачів не має доступу на відправку";
        }
        return $list;
    }

    public function createSaleInvoiceExcel($invoice_id, $separator, $name, $email, $mail_id): string
    {
        $db = DbSingleton::getDb();
        $slave = new slave; $cat = new catalogue;
        $separator = (int)$separator;
        if ($separator === 1) {
            $separator = "point";
        }
        if ($separator === 2) {
            $separator = "comma";
        }
        //$invoice_summ = 0;
        $filename = $doc_name = $data_doc = "";
        $r = $db->query("SELECT sv.*, sl.name as seller_name, cl.name as client_name
        FROM `J_SALE_INVOICE` sv
            LEFT OUTER JOIN `A_CLIENTS` sl ON (sl.id = sv.seller_id)
            LEFT OUTER JOIN `A_CLIENTS` cl ON (cl.id = sv.client_conto_id)
        WHERE sv.status = 1 AND sv.id = '$invoice_id' LIMIT 1;");
        $n = (int)$db->num_rows($r);

        if ($n === 1) {
            $prefix      = $db->result($r, 0, "prefix");
            $doc_nom     = $db->result($r, 0, "doc_nom");
            $data_create = $db->result($r, 0, "data_create");
            $seller_name = $db->result($r, 0, "seller_name");
            $client_name = $db->result($r, 0, "client_name");
            $doc_type_id = (int)$db->result($r, 0, "doc_type_id");
            $doc_name    = "$prefix-$doc_nom";
            $data_doc    = $data_create;
            $list        = array();

            $r = $db->query("SELECT * FROM `J_SALE_INVOICE_STR` WHERE `invoice_id` = '$invoice_id' ORDER BY `id` ASC;");
            $n = $db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $art_id             = $db->result($r, $i - 1, "art_id");
                $article_nr_displ   = $db->result($r, $i - 1, "article_nr_displ");
                $article_name       = $cat->getArticleNameLang($art_id);
                $brand_id           = $db->result($r, $i - 1, "brand_id");
                $brand_name         = $cat->getBrandName($brand_id);
                $amount             = $db->result($r, $i - 1, "amount");
                $price_end          = $db->result($r, $i - 1, "price_end");
                $summ               = $db->result($r, $i - 1, "summ");

                //$invoice_summ += $summ;
                if ($separator === "comma") {
                    $price_format = str_replace(".", ",", $price_end);
                    $summ_format = str_replace(".", ",", $summ);
                } else {
                    $price_format = $price_end;
                    $summ_format = $summ;
                }
                if ($doc_type_id === 61) {
                    $zed = $this->getZedArticle($art_id);
                    $list[] = "$i;$article_nr_displ;$brand_name;$zed;$article_name;$amount;$price_format;$summ_format\n";
                } else {
                    $list[] = "$i;$article_nr_displ;$brand_name;$article_name;$amount;$price_format;$summ_format\n";
                }
            }

            $filename = "$prefix-$doc_nom" . "-vid-" . $data_create;
            $filename = str_replace(str_split('"«»'), '', $filename);
            $filename = str_replace("'", "", $filename);
            $filename = str_replace(str_split(" .,/"), "_", $filename);
            $filename = $slave->translit($filename) . ".csv";

            $fp = fopen("/var/www/portal.myparts.pro/uploads/emails/$filename", "wb");

            fputs($fp, "Продавець: $seller_name\n");
            fputs($fp, "Покупець: $client_name\n");

            foreach ($list as $row) {
                fwrite($fp, $row);
            }
            fclose($fp);
        }

        $this->sendMail($email, $name, $filename, $doc_name, $data_doc, $mail_id);

        return $filename;
    }

    public function sendSaleInvoceMail($invoice_id, $user_id): array
    {
        $db = DbSingleton::getDb();
        $answer = 0; $err = "Error!"; $n = 0;

        if ($invoice_id > 0 && $user_id > 0) {
            $err = "";
            $r = $db->query("SELECT `email`, `name`, `client_id`, `invoice_status` FROM `A_CLIENTS_USERS` WHERE `id` = '$user_id';");
            $n = (int)$db->num_rows($r);

            $email          = $db->result($r, 0, "email");
            $name           = $db->result($r, 0, "name");
            $client_id      = $db->result($r, 0, "client_id");
            $invoice_status = $db->result($r, 0, "invoice_status");

            if ($invoice_status > 0) {
                $rt = $db->query("SELECT MAX(`id`) as mid FROM `A_CLIENTS_INVOICE_MAIL_HISTORY`;");
                $mail_id = 0 + $db->result($rt, 0, "mid") + 1;
                $db->query("INSERT INTO `A_CLIENTS_INVOICE_MAIL_HISTORY` (`id`, `client_id`, `user_id`, `email`, `doc_type`) 
                VALUES ('$mail_id','$client_id','$user_id','$email','1');");
                $this->createSaleInvoiceExcel($invoice_id, $invoice_status, $name, $email, $mail_id);
                $answer = 1;
                $err .= "sent to $email";
            }
        }

        if ($n === 0) {
            $answer = 0; $err = "Not access!";
        }

        return array($answer, $err);
    }

    public function addClientInvoiceCron($invoice_id): string
    {
        $db = DbSingleton::getDb();
        $emails = "";
        $r = $db->query("SELECT * FROM `J_SALE_INVOICE` WHERE `id` = '$invoice_id';");
        $n = $db->num_rows($r);

        if ($n > 0) {
            $client_id = $db->result($r, 0, "client_conto_id");
            $r = $db->query("SELECT * FROM `A_CLIENTS_USERS` WHERE `client_id` = '$client_id';");
            $n = $db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $user_id        = $db->result($r, $i - 1, "id");
                $email          = $db->result($r, $i - 1, "email");
                $invoice_status = $db->result($r, $i - 1, "invoice_status");

                if ($invoice_status > 0) {
                    $db->query("INSERT INTO `cron_client_invoice` (`doc_type`, `doc_id`, `client_id`, `user_id`, `email`) VALUES ('1', '$invoice_id', '$client_id', '$user_id', '$email');");
                }
                $emails .= "$email ";
            }
        }

        return $emails;
    }

    public function sendMail($receiver, $user_name, $filename, $doc_name, $data_doc, $mail_id): bool
    {
        $db = DbSingleton::getDb();
        $path = "https://portal.myparts.pro/uploads/emails/$filename";
        $list = "<p>Доброго дня, $user_name</p>
        <p>У доданому файлі знаходиться видаткова накладна $doc_name від $data_doc</p>
        <p>З повагою ТОКО ГРУП.</p><br>
        <small>ТОКО ГРУП ТОВ, ІПН:403029222256, ЄДРПОУ:40302920</small>";
        $mail = new PHPMailer();
        try {
            $mail->isMail();
            $mail->addReplyTo('noreply@toko.ua', 'TOKO GROUP');
            $mail->addAddress($receiver);
            $mail->Subject = "Расходная накладная компании `TOKO GROUP`";
            $mail->msgHTML($list);
            $mail->addStringAttachment(file_get_contents($path), $filename);
            $mail->action_function = 'callbackAction';
            $mail->send();
            $db->query("UPDATE `A_CLIENTS_INVOICE_MAIL_HISTORY` SET `status` = '1', `filename` = '$filename' WHERE `id` = '$mail_id';");
        } catch (Exception $e) { }

        return true;
    }

}