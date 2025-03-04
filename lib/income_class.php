<?php

class income {

    protected $prefix_new = '��';

    function getIncomeDocNom($income_id) { $db = DbSingleton::getDb();
        session_start();
        $prefix = ""; $doc_nom = "-";
        $r = $db->query("SELECT * FROM `J_INCOME` WHERE `id`='$income_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $prefix = $db->result($r,0,"prefix");
            $doc_nom = $db->result($r,0,"doc_nom");
            if ($doc_nom == 0) {
                $doc_nom = "-";
            }
        }
        return $prefix . $doc_nom;
    }

    function get_doc_prefix($client_id, $prefix_id) { $db = DbSingleton::getDb();
        $prefix = "��";
        $r = $db->query("SELECT `prefix` FROM `A_CLIENTS_DOCUMENT_PREFIX` WHERE `client_id`='$client_id' AND `id`='$prefix_id' AND `status`='1' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $prefix = $db->result($r, 0, "prefix");
        }
        return $prefix;
    }

    function get_doc_client_prefix($client_id) { $db = DbSingleton::getDb();
        $prefix_id = 0; $doc_type_id = 40;
        $r = $db->query("SELECT `id` FROM `A_CLIENTS_DOCUMENT_PREFIX` WHERE `client_id`='$client_id' AND `doc_type_id`='$doc_type_id' AND `status`='1' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $prefix_id = $db->result($r, 0, "id");
        }
        return $prefix_id;
    }

    function get_df_doc_nom_new() { $db = DbSingleton::getDb();
        $r = $db->query("SELECT MAX(`doc_nom`) as mid FROM `J_INCOME` WHERE `oper_status`='30' AND `status`='1' LIMIT 1;");
        return 0 + $db->result($r,0,"mid") + 1;
    }

    function get_client_doc_nom_new($client_id) { $db = DbSingleton::getDb();
        $r = $db->query("SELECT MAX(`doc_nom`) as mid FROM `J_INCOME` WHERE `client_id`='$client_id' AND `status`='1' AND `oper_status`='31' LIMIT 1;");
        return 0 + $db->result($r,0,"mid") + 1;
    }

    function getIncomeClientPrefixDocument($client_id) {
        $slave = new slave;
        $client_id = $slave->qq($client_id);
        $prefix_id = $this->get_doc_client_prefix($client_id);
        $prefix = $this->get_doc_prefix($client_id, $prefix_id);
        $prefix = str_replace("{year}", date("Y"), $prefix);
        $prefix = str_replace("{month}", date("m"), $prefix);
        $prefix = str_replace("{day}", date("d"), $prefix);
        $prefix = str_replace("{rnd010}", rand(0, 10), $prefix);
        $doc_nom = $this->get_client_doc_nom_new($client_id);
        return array($prefix, $doc_nom);
    }

    function newIncomeCard($type_id) { $db = DbSingleton::getDb();
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $r = $db->query("SELECT MAX(`id`) as mid FROM `J_INCOME`;");
        $income_id = 0 + $db->result($r,0,"mid") + 1;
        $doc_nom = $this->get_df_doc_nom_new();
        $db->query("INSERT INTO `J_INCOME` (`id`,`type_id`,`prefix`,`doc_nom`,`user_id`,`data`) VALUES ('$income_id','$type_id','$this->prefix_new','$doc_nom','$user_id',CURDATE());");
        return $income_id;
    }

    function getNBUKours($data, $val) { $db = DbSingleton::getDb();
        $kours = "";
        if ($val == 1) {$val = "usd";}
        if ($val == 2) {$val = "usd";}
        if ($val == 3) {$val = "euro";}
        $r = $db->query("SELECT `$val` FROM `kours` WHERE `data`='$data' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $kours = $db->result($r,0,"$val");
        }
        return $kours;
    }

    function checkJIncomeSTR($income_id) { $db = DbSingleton::getDb();
        $r = $db->query("SELECT * FROM `J_INCOME_STR` WHERE `income_id`=$income_id;");
        $n = $db->num_rows($r);
        return ($n > 0);
    }

    function getMediaUserName($user_id) { $db = DbSingleton::getDb();
        $name = "";
        $r = $db->query("SELECT `name` FROM `media_users` WHERE `id`='$user_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $name = $db->result($r,0,"name");
        }
        return $name;
    }

    function show_income_list() { $db = DbSingleton::getDb();
        $gmanual = new gmanual;
        $list = "";
        $data_cur = date("Y-m-d");
        $where = " AND j.data>='$data_cur 00:00:00' AND j.data<='$data_cur 23:59:59'";
        $r = $db->query("SELECT j.*, CASH.name as cash_name, c.name as client_seller_name, c2.name as client_name 
        FROM `J_INCOME` j
            LEFT OUTER JOIN `CASH` on CASH.id=j.cash_id
            LEFT OUTER JOIN `A_CLIENTS` c on c.id=j.client_seller
            LEFT OUTER JOIN `A_CLIENTS` c2 on c2.id=j.client_id
        WHERE j.status=1 $where ORDER BY j.oper_status ASC, j.data DESC, j.prefix ASC, j.doc_nom DESC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r,$i - 1,"id");
            $type_id = $db->result($r,$i - 1,"type_id");
            $user_id = $db->result($r,$i - 1,"user_id");
            $user_name = $this->getMediaUserName($user_id);
            $prefix = $db->result($r,$i - 1,"prefix");
            $doc_nom = $db->result($r,$i - 1,"doc_nom");
            if ($doc_nom == 0) {
                $doc_nom = "-";
            }
            $data = $db->result($r,$i - 1,"data");
            $cash_name = $db->result($r,$i - 1,"cash_name");
            $client_name = $db->result($r,$i - 1,"client_name");
            $client_seller_name = $db->result($r,$i - 1,"client_seller_name");
            $invoice_income = $db->result($r,$i - 1,"invoice_income");
            $invoice_data = $db->result($r,$i - 1,"invoice_data");
            $invoice_summ = $db->result($r,$i - 1,"invoice_summ");
            $oper_status = $gmanual->get_gmanual_caption($db->result($r,$i - 1,"oper_status"));
            $type_icon = "<span class='label label-warning'><i class='fa fa-globe'></i></span>";
            if ($type_id == 0) {
                $type_icon = "<span class='label label-primary'><i class='fa fa-bicycle'></i></span>";
            }
            if ($this->checkJIncomeSTR($id)) {
                $list .= "<tr style='cursor:pointer' onClick='showIncomeCard(\"$id\")'>
                    <td>$type_icon &nbsp; $prefix-$doc_nom</td>
                    <td align='center'>$data</td>
                    <td>$client_seller_name</td>
                    <td>$client_name</td>
                    <td>$invoice_income</td>
                    <td>$invoice_data</td>
                    <td>$invoice_summ</td>
                    <td>$cash_name</td>
                    <td>$oper_status</td>
                    <td>$user_name</td>
                </tr>";
            }
        }
        return $list;
    }

    function show_income_list_filter($data_start, $data_end) { $db = DbSingleton::getDb();
        $gmanual = new gmanual;
        $data_cur = date("Y-m-d");
        if ($data_start != "" && $data_end != "") {
            $where = "AND j.data>='$data_start 00:00:00' AND j.data<='$data_end 23:59:59'";
        } else {
            $where = " AND j.data>='$data_cur 00:00:00' AND j.data<='$data_cur 23:59:59'";
        }
        $list = "";
        $r = $db->query("SELECT j.*, CASH.name as cash_name, c.name as client_seller_name, c2.name as client_name 
        FROM `J_INCOME` j
            LEFT OUTER JOIN CASH on CASH.id=j.cash_id
            LEFT OUTER JOIN A_CLIENTS c on c.id=j.client_seller
            LEFT OUTER JOIN A_CLIENTS c2 on c2.id=j.client_id
        WHERE j.status=1 $where ORDER BY j.oper_status ASC, j.data DESC, j.prefix ASC, j.doc_nom DESC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r,$i - 1,"id");
            $type_id = $db->result($r,$i - 1,"type_id");
            $user_id = $db->result($r,$i - 1,"user_id");
            $user_name = $this->getMediaUserName($user_id);
            $prefix = $db->result($r,$i - 1,"prefix");
            $doc_nom = $db->result($r,$i - 1,"doc_nom");
            if ($doc_nom == 0) {
                $doc_nom = "-";
            }
            $data = $db->result($r,$i - 1,"data");
            $cash_name = $db->result($r,$i - 1,"cash_name");
            $client_name = $db->result($r,$i - 1,"client_name");
            $client_seller_name = $db->result($r,$i - 1,"client_seller_name");
            $invoice_income = $db->result($r,$i - 1,"invoice_income");
            $invoice_data = $db->result($r,$i - 1,"invoice_data");
            $invoice_summ = $db->result($r,$i - 1,"invoice_summ");
            $oper_status = $gmanual->get_gmanual_caption($db->result($r,$i - 1,"oper_status"));
            $type_icon = "<span class='label label-warning'><i class='fa fa-globe'></i></span>";
            if ($type_id == 0) {
                $type_icon = "<span class='label label-primary'><i class='fa fa-bicycle'></i></span>";
            }
            if ($this->checkJIncomeSTR($id)) {
                $list .= "<tr style='cursor:pointer' onClick='showIncomeCard(\"$id\")'>
                    <td>$type_icon &nbsp; $prefix-$doc_nom</td>
                    <td align='center'>$data</td>
                    <td>$client_seller_name</td>
                    <td>$client_name</td>
                    <td>$invoice_income</td>
                    <td>$invoice_data</td>
                    <td>$invoice_summ</td>
                    <td>$cash_name</td>
                    <td>$oper_status</td>
                    <td>$user_name</td>
                </tr>";
            }
        }
        return $list;
    }

    function search_documents_income_list($s_nom) { $db = DbSingleton::getDb();
        $slave = new slave; $gmanual = new gmanual;
        $s_nom = $slave->qq($s_nom);
        $list = "";
        $where = " ORDER BY `id` DESC LIMIT 0,100;";
        if ($s_nom != "") {
            $where = " AND (j.prefix LIKE '%$s_nom%' OR j.doc_nom LIKE '%$s_nom%' OR jj.full_doc LIKE '%$s_nom%')";
        }
        $r = $db->query("SELECT j.*, jj.full_doc, CASH.name as cash_name, c.name as client_seller_name 
        FROM `J_INCOME` j
            LEFT OUTER JOIN `CASH` on CASH.id=j.cash_id
            LEFT OUTER JOIN `A_CLIENTS` c on c.id=j.client_seller
            LEFT OUTER JOIN (SELECT id, CONCAT(prefix,'',doc_nom) as full_doc FROM `J_INCOME` WHERE `status`=1) as jj on jj.id=j.id
        WHERE j.status=1 $where;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r,$i - 1,"id");
            $prefix = $db->result($r,$i - 1,"prefix");
            $doc_nom = $db->result($r,$i - 1,"doc_nom");
            if ($doc_nom == 0) {
                $doc_nom = "-";
            }
            $full_doc = $db->result($r,$i - 1,"full_doc");
            $data = $db->result($r,$i - 1,"data");
            $cash_name = $db->result($r,$i - 1,"cash_name");
            $client_seller_name = $db->result($r,$i - 1,"client_seller_name");
            $invoice_income = $db->result($r,$i - 1,"invoice_income");
            $invoice_data = $db->result($r,$i - 1,"invoice_data");
            $invoice_summ = $db->result($r,$i - 1,"invoice_summ");
            $oper_status = $gmanual->get_gmanual_caption($db->result($r,$i - 1,"oper_status"));
            $list .= "<tr style='cursor:pointer' onClick='setDocumentToForm(\"$id\",\"$prefix$doc_nom\")'>
                <td>$full_doc</td>
                <td align='center'>$data</td>
                <td>$client_seller_name</td>
                <td>$invoice_income</td>
                <td>$invoice_data</td>
                <td>$invoice_summ</td>
                <td>$cash_name</td>
                <td>$oper_status</td>
            </tr>";
        }
        return $list;
    }

    function preNewIncomeCard() {
        $form = ""; $form_htm = RD . "/tpl/income_select_type_form.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        return $form;
    }

    function getIncomeSupplDocNom($income_id) { $db = DbSingleton::getDb();
        session_start();
        $invoice_income = "";
        $r = $db->query("SELECT `invoice_income` FROM `J_INCOME` WHERE `id`='$income_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $invoice_income = $db->result($r,0,"invoice_income");
        }
        return $invoice_income;
    }

    function getKourForDate($cash_id_to, $cash_id_from, $data) { $db = DbSingleton::getDb();
        $kours = 1;
        if ($data == "0000-00-00") {
            $data = date("Y-m-d");
        }
        if ($cash_id_from != $cash_id_to) {
            $r = $db->query("SELECT `kours_value` FROM `J_KOURS` 
            WHERE `cash_id`='$cash_id_from' AND `data_from`<='$data' AND (`data_to`='0000-00-00' OR `data_to`>='$data') AND `in_use` IN (0,1) 
            ORDER BY `id` DESC LIMIT 1;");
            $n = $db->num_rows($r);
            if ($n == 1) {
                $kours = $db->result($r,0,"kours_value");
            }
        }
        return $kours;
    }

    function loadIncomeKours($data) {
        $usd_to_uah = $this->getKourForDate(1, 2, $data);
        $eur_to_uah = $this->getKourForDate(1, 3, $data);
        return array($usd_to_uah, $eur_to_uah);
    }

    function checkInvoiceIncome($invoice_income, $seller, $date) { $db = DbSingleton::getDb();
        $r = $db->query("SELECT `id`, `prefix`, `doc_nom` FROM `J_INCOME` 
        WHERE `invoice_income`='$invoice_income' AND `client_seller`='$seller' AND `invoice_data`='$date' AND `oper_status`='31' LIMIT 1;");
        $n = $db->num_rows($r);
        $income_id = $db->result($r,0,"id");
        $prefix = $db->result($r,0,"prefix");
        $doc_nom = $db->result($r,0,"doc_nom");
        $uktzed_status = 0;
        if ($this->checkIncomeStr($income_id)) {
            $uktzed_status = 1;
        }
        $form = ($n > 0) ? "$prefix-$doc_nom" : "";
        return array($form, $uktzed_status);
    }

    function showIncomeCard($income_id) { $db = DbSingleton::getDb();
        session_start();
        $user_id = $_SESSION["media_user_id"]; $user_name = $_SESSION["user_name"];
        $prefix = ""; $doc_nom = 0;
        $form = ""; $form_htm = RD ."/tpl/income_card.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $r = $db->query("SELECT * FROM `J_INCOME` WHERE `id`='$income_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 0) {
            $form_htm = RD . "/tpl/access_deny.htm";
            if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        }
        if ($n == 1) {
            $cl = new clients;
            $storage_id = $db->result($r,0,"storage_id");
            $storage_cells_id = $db->result($r,0,"storage_cells_id");
            $prefix = $db->result($r,0,"prefix");
            $doc_nom = $db->result($r,0,"doc_nom");
            if ($doc_nom == 0) {
                $doc_nom = "-";
            }
            $type_id = $db->result($r,0,"type_id");
            if ($type_id == 0) {
                $form_htm = RD . "/tpl/income_card_local.htm";
                if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
            }
            $data = $db->result($r,0,"data");
            if ($data == "0000-00-00") {
                $data = "";
            }
            $cash_id = $db->result($r,0,"cash_id");
            $cours_to_uah = $db->result($r,0,"cours_to_uah");
            $cours_to_uah_nbu = $db->result($r,0,"cours_to_uah_nbu");
            $usd_to_uah = $db->result($r,0,"usd_to_uah");
            if ($usd_to_uah == "0.00") {
                $usd_to_uah = $this->getKourForDate($cash_id,2,$data);
            }
            $eur_to_uah = $db->result($r,0,"eur_to_uah");
            if ($eur_to_uah == "0.00") {
                $eur_to_uah = $this->getKourForDate($cash_id,3,$data);
            }
            $invoice_income = $db->result($r,0,"invoice_income");
            $invoice_data = $db->result($r,0,"invoice_data");
            if ($invoice_data == "0000-00-00") {
                $invoice_data = "";
            }
            $invoice_summ = $db->result($r,0,"invoice_summ");
            $summ_end = $db->result($r,0,"summ_end");
            $client_seller = $db->result($r,0,"client_seller");
            $client_id = $db->result($r,0,"client_id");
            $comment = $db->result($r,0,"comment");
            $costums_pd_uah = $db->result($r,0,"costums_pd_uah");
            $costums_pp_uah = $db->result($r,0,"costums_pp_uah");
            $costums_summ_uah = $db->result($r,0,"costums_summ_uah");
            $vat_use = $db->result($r,0,"vat_use");
            $oper_status = $db->result($r,0,"oper_status");

            if ($oper_status == 31) {
                $form = str_replace("{hide_calculate_button}"," disabled style=\"visibility:hidden;\"",$form);
                $form = str_replace("{hide_import_button}"," disabled style=\"visibility:hidden;\"",$form);
                $form = str_replace("{hide_new_row_button}"," disabled style=\"visibility:hidden;\"",$form);
                $form = str_replace("{oper_disabled}"," disabled",$form);
                $form = str_replace("{oper_disabled2}"," ",$form);
            }
            if ($user_id == 1 || $user_id == 7 || $user_id == 8) {
                $form = str_replace("{oper_disabled2}"," ",$form);
            }

            $form = str_replace("{oper_disabled}","",$form);
            $form = str_replace("{oper_disabled2}"," disabled",$form);
            $form = str_replace("{income_id}",$income_id,$form);
            $form = str_replace("{oper_status}",$oper_status,$form);
            $form = str_replace("{doc_prefix}",$prefix,$form);
            $form = str_replace("{type_id}",$type_id,$form);
            $form = str_replace("{data}",date("Y-m-d"),$form);
            $form = str_replace("{cash_list}",$this->showCashListSelect($cash_id, 2),$form);
            $form = str_replace("{cours_to_uah}",$cours_to_uah,$form);
            $form = str_replace("{cours_to_uah_nbu}",$cours_to_uah_nbu,$form);
            $form = str_replace("{usd_to_uah}",$usd_to_uah,$form);
            $form = str_replace("{eur_to_uah}",$eur_to_uah,$form);
            $form = str_replace("{invoice_income}",$invoice_income,$form);
            $form = str_replace("{invoice_data}",$invoice_data,$form);
            $form = str_replace("{invoice_summ}",$invoice_summ,$form);
            $form = str_replace("{summ_end}",$summ_end,$form);
            $form = str_replace("{client_seller}",$client_seller,$form);
            $form = str_replace("{client_seller_name}",$cl->getClientNameById($client_seller,"name"),$form);
            $form = str_replace("{client_id}",$client_id,$form);
            $form = str_replace("{client_name}",$cl->getClientNameById($client_id, "name"),$form);
            $form = str_replace("{comment}",$comment,$form);
            $form = str_replace("{costums_pd_uah}",$costums_pd_uah,$form);
            $form = str_replace("{costums_pp_uah}",$costums_pp_uah,$form);
            $form = str_replace("{costums_summ_uah}",$costums_summ_uah,$form);
            $form = str_replace("{import_checked}","",$form);
            $form = str_replace("{vat_checked}",($vat_use == 1) ? " checked" : "",$form);
            $income_spend_item_summs = $this->getIncomeSpendSumms($income_id);
            $form = str_replace("{income_spend_item_1}",$income_spend_item_summs[1],$form);
            $form = str_replace("{income_spend_item_2}",$income_spend_item_summs[2],$form);
            $form = str_replace("{income_spend_item_3}",$income_spend_item_summs[3],$form);
            $form = str_replace("{income_spend_item_4}",$income_spend_item_summs[4],$form);
            $form = str_replace("{income_spend_item_5}",$income_spend_item_summs[5],$form);
            $form = str_replace("{income_id}",$income_id,$form);
            $form = str_replace("{storage_list}",$this->showStorageSelectList($storage_id),$form);
            $form = str_replace("{storage_cells_list}",$this->showStorageCellsSelectList($storage_id, $storage_cells_id),$form);
            $incomeChildsList = "";
            if ($type_id == 0) {
                $incomeChildsList = $this->showIncomeLocalStrList($income_id, $oper_status);
            }
            if ($type_id == 1) {
                $incomeChildsList = $this->showIncomeImportStrList($income_id, $oper_status);
            }
            $form = str_replace("{incomeChildsList}",$incomeChildsList,$form);
            $form = str_replace("{my_user_id}",$user_id,$form);
            $form = str_replace("{my_user_name}",$user_name,$form);
            list(, $label_comments) = $this->labelCommentsCount($income_id);
            $form = str_replace("{labelCommentsCount}",$label_comments,$form);
            list(, $label_art_unknown) = $this->labelArtUnknownCount($income_id);
            $form = str_replace("{labelArticlesUnKnownCount}",$label_art_unknown,$form);
        }
        return array($form, $prefix . "-" . $doc_nom);
    }

    function showIncomeStrList($income_id, $type_id, $oper_status) {
        $incomeChildsList = "";
        if ($type_id == 0) {
            $incomeChildsList = $this->showIncomeLocalStrList($income_id,$oper_status);
        }
        if ($type_id == 1) {
            $incomeChildsList = $this->showIncomeImportStrList($income_id,$oper_status);
        }
        return $incomeChildsList;
    }

    function showIncomeImportStrList($income_id, $oper_status) { $db = DbSingleton::getDb();
        $slave = new slave; $cat = new catalogue; $manual = new manual;
        $list = "";
        $r = $db->query("SELECT * FROM `J_INCOME_STR` WHERE `income_id`='$income_id' ORDER BY `id` ASC;");
        $n = $db->num_rows($r);
        $kl_rw = $n;
        $kl_rw += 2;
        for ($i = 1; $i <= $kl_rw; $i++) {
            $id="";$art_id="";$article_nr_displ="";$brand_id="";$brand_name="";$country_id="";$country_abr="";$costums_id="";$costums_code="";$amount="";$price_buh_cashin="";
            $weight_netto="";$rate="";$type_declaration_id="";$type_declaration_name="";$price_man_cashin="";$price_man_usd="";$price_buh_uah="";$price_man_uah="";$unknown_id=0;
            if ($i <= $n) {
                $id = $db->result($r,$i - 1,"id");
                $unknown_id = $db->result($r,$i - 1,"unknown_id");
                $art_id = $db->result($r,$i - 1,"art_id");
                $article_nr_displ = $db->result($r,$i - 1,"article_nr_displ");
                $brand_id = $db->result($r,$i - 1,"brand_id");
                $brand_name = $cat->getBrandName($brand_id);
                $country_id = $db->result($r,$i - 1,"country_id");
                $country_abr = $cat->getCountryAbr($country_id);
                $costums_id = $db->result($r,$i - 1,"costums_id");
                $costums_code = $cat->getCostumsCode($costums_id);
                $amount = $db->result($r,$i - 1,"amount");
                $price_buh_cashin = $db->result($r,$i - 1,"price_buh_cashin");
                $weight_netto = $db->result($r,$i - 1,"weight_netto");
                $rate = $db->result($r,$i - 1,"rate");
                $type_declaration_id = $db->result($r,$i - 1,"type_declaration_id");
                $type_declaration_name = $manual->getManualMCaption("costums_type_declaration", $type_declaration_id);
                $price_man_cashin = $db->result($r,$i - 1,"price_man_cashin");
                $price_man_usd = $db->result($r,$i - 1,"price_man_usd");
                $price_buh_uah = $db->result($r,$i - 1,"price_buh_uah");
                $price_man_uah = $db->result($r,$i - 1,"price_man_uah");
            }
            if ($oper_status == 30) {
                $style_row = "";
                if ($unknown_id > 0) {
                    $style_row = " style='background-color:#fbcfcf;'";
                }
                $list .= "
                <tr id='strRow_$i' $style_row>
                    <td>$i<input type='hidden' id='idStr_$i' value='$id'></td>
                    <td style='min-width:140px;'><input type='hidden' id='artIdStr_$i' value='$art_id'>
                        <div class='input-group'>
                            <input class='form-control input-xs' type='text' readonly id='article_nr_displStr_$i' value='$article_nr_displ' placeholder='������ ������'>
                            <span class='input-group-btn'> <button type='button' class='btn btn-xs btn-primary' onClick=\"showIncomeArticleSearchForm('$i','$art_id','$brand_id','$article_nr_displ');\"><i class=\"fa fa-bars\"></i></button> </span>
                        </div>
                    </td>
                    <td style='min-width:120px;'><input type='hidden' id='brandIdStr_$i' value='$brand_id'>
                        <input class='form-control input-xs' type='text' readonly id='brandNameStr_$i' value='$brand_name' placeholder='�����'>
                    </td>
                    <td style='min-width:80px;'><input type='hidden' id='countryIdStr_$i' value='$country_id'>
                        <div class='input-group'>
                            <input class='form-control input-xs' type='text' readonly id='countryAbrStr_$i' value='$country_abr' placeholder='���'>
                            <span class='input-group-btn'> <button type='button' class='btn btn-xs btn-primary' onClick=\"showIncomeCountrySearchForm('$i','$art_id','$country_id');\"><i class=\"fa fa-bars\"></i></button> </span>
                        </div>
                    </td>
                    <td style='min-width:120px;'><input type='hidden' id='costumsIdStr_$i' value='$costums_id'>
                        <div class='input-group'>
                            <input class='form-control input-xs' type='text' readonly id='costumsStr_$i' value='$costums_code' placeholder='���'>
                            <span class='input-group-btn'> <button type='button' class='btn btn-xs btn-primary' onClick=\"showIncomeCostumsSearchForm('$i','$art_id','$costums_id');\"><i class=\"fa fa-bars\"></i></button> </span>
                        </div>
                    </td>
                    <td><input type='text' id='amountStr_$i' value='$amount' class='form-control input-xs numberOnly' autocomplete='off' maxlength='20'></td>
                    <td><input type='text' id='price_buh_cashinStr_$i' value='$price_buh_cashin' class='form-control input-xs numberOnly' autocomplete='off' maxlength='20'></td>
                    <td><input type='text' id='weightNettoStr_$i' value='$weight_netto' class='form-control input-xs text-right numberOnly' autocomplete='off' maxlength='20'></td>
                    <td><input type='text' readonly id='rateStr_$i' value='$rate' class='form-control input-xs text-right'></td>
                    <td>
                        <input type='hidden' id='typeDeclarationIdStr_$i' value='$type_declaration_id'>
                        <input type='text' readonly id='typeDeclarationStr_$i' value='$type_declaration_name' class='form-control input-xs text-right'>
                    </td>
                    <td><input type='text' readonly id='price_man_cashinStr_$i' value='$price_man_cashin' class='form-control input-xs text-right'></td>
                    <td><input type='text' readonly id='price_man_usdStr_$i' value='$price_man_usd' class='form-control input-xs text-right'></td>
                    <td><input type='text' readonly id='price_buh_uahStr_$i' value='$price_buh_uah' class='form-control input-xs text-right'></td>
                    <td><input type='text' readonly id='price_man_uahStr_$i' value='$price_man_uah' class='form-control input-xs text-right'></td>
                    <td><button class='btn btn-xs btn-default' onClick=\"dropIncomeStr('$i','$income_id','$art_id');\"><i class='fa fa-times'></i></button></td>
                </tr>";
            }
            if ($oper_status == 31) {
                if ($article_nr_displ != "") {
                    $list .= "<tr align='right'>
                        <td align='left'>$i</td>
                        <td align='left'>$article_nr_displ</td>
                        <td align='center'>$brand_name</td>
                        <td align='center'>$country_abr</td>
                        <td align='center'>$costums_code</td>
                        <td>".$slave->to_money($amount)."</td>
                        <td>".$slave->to_money($price_buh_cashin)."</td>
                        <td>".$weight_netto."</td>
                        <td>".$rate."</td>
                        <td>".$type_declaration_name."</td>
                        <td>".$slave->to_money($price_man_cashin)."</td>
                        <td>".$slave->to_money($price_man_usd)."</td>
                        <td>".$slave->to_money($price_buh_uah)."</td>
                        <td>".$slave->to_money($price_man_uah)."</td>
                        <td align='center'>-</td>
                    </tr>";
                }
            }
        }
        if ($oper_status == 30) {
            $list = "<input type='hidden' id='kol_row' value='$kl_rw'>
                <tr id='incomeStrNewRow' class='hidden'>
                    <td>nom_i<input type='hidden' id='idStr_' value=''></td>
                    <td style='min-width:140px;'><input type='hidden' id='artIdStr_' value=''>
                        <div class='input-group'>
                            <input class='form-control input-xs' type='text' readonly id='article_nr_displStr_' value='' placeholder='������ ������'>
                            <span class='input-group-btn'> <button type='button' class='btn btn-xs btn-primary' onClick=\"showIncomeArticleSearchForm('i_0','0','0','');\"><i class=\"fa fa-bars\"></i></button> </span>
                        </div>
                    </td>
                    <td style='min-width:120px;'><input type='hidden' id='brandIdStr_' value=''>
                        <input class='form-control input-xs' type='text' readonly id='brandNameStr_' value='' placeholder='�����'>
                    </td>
                    <td style='min-width:80px;'><input type='hidden' id='countryIdStr_' value=''>
                        <div class='input-group'>
                            <input class='form-control input-xs' type='text' readonly id='countryAbrStr_' value='' placeholder='���'>
                            <span class='input-group-btn'> <button type='button' class='btn btn-xs btn-primary' onClick=\"showIncomeCountrySearchForm('i_0','','');\"><i class=\"fa fa-bars\"></i></button> </span>
                        </div>
                    </td>
                    <td style='min-width:120px;'><input type='hidden' id='costumsIdStr_' value=''>
                        <div class='input-group'>
                            <input class='form-control input-xs' type='text' readonly id='costumsStr_' value='' placeholder='���'>
                            <span class='input-group-btn'> <button type='button' class='btn btn-xs btn-primary' onClick=\"showIncomeCostumsSearchForm('i_0','0','0');\"><i class=\"fa fa-bars\"></i></button> </span>
                        </div>
                    </td>
                    <td><input type='text' id='amountStr_' value='' class='form-control input-xs numberOnly' autocomplete='off' maxlength='20'></td>
                    <td><input type='text' id='price_buh_cashinStr_' value='' class='form-control  input-xstext-right numberOnly' autocomplete='off' maxlength='20'></td>
                    <td><input type='text' id='weightNettoStr_' value='' class='form-control input-xs text-right numberOnly' autocomplete='off' maxlength='20'></td>
                    <td><input type='text' readonly id='rateStr_' value='' class='form-control input-xs text-right'></td>
                    <td>
                        <input type='hidden' id='typeDeclarationIdStr_' value=''>
                        <input type='text' readonly id='typeDeclarationStr_' value='' class='form-control input-xs text-right'>
                    </td>
                    <td><input type='text' readonly id='price_man_cashinStr_' value='' class='form-control input-xs text-right'></td>
                    <td><input type='text' readonly id='price_man_usdStr_' value='' class='form-control input-xs text-right'></td>
                    <td><input type='text' readonly id='price_buh_uahStr_' value='' class='form-control input-xs text-right'></td>
                    <td><input type='text' readonly id='price_man_uahStr_' value='' class='form-control input-xs text-right'></td>
                    <td><button class='btn btn-xs btn-default' onClick=\"dropIncomeStr('i_0','0','0');\"><i class='fa fa-times'></i></button></td>
                </tr>" . $list;
        }
        return $list;
    }

    function showIncomeLocalStrList($income_id, $oper_status) { $db = DbSingleton::getDb();
        $slave = new slave; $cat = new catalogue;
        $list = "";
        $r = $db->query("SELECT * FROM `J_INCOME_STR` WHERE `income_id`='$income_id' ORDER BY `id` ASC;");
        $n = $db->num_rows($r);
        $kl_rw = $n;
        $kl_rw += 2;
        for ($i = 1; $i <= $kl_rw; $i++) {
            $id=""; $art_id=""; $article_nr_displ=""; $brand_id=""; $brand_name=""; $costums_id=""; $costums_code=""; $amount="";
            $price_buh_cashin=""; $weight_netto=""; $price_man_cashin=""; $price_man_usd=""; $price_buh_uah=""; $price_man_uah="";
            if ($i <= $n) {
                $id = $db->result($r,$i - 1,"id");
                $art_id = $db->result($r,$i - 1,"art_id");
                $article_nr_displ = $db->result($r,$i - 1,"article_nr_displ");
                $brand_id = $db->result($r,$i - 1,"brand_id");
                $brand_name = $cat->getBrandName($brand_id);
                $costums_id = $db->result($r,$i - 1,"costums_id");
                $costums_code = $cat->getCostumsCode($costums_id);
                $amount = $db->result($r,$i - 1,"amount");
                $price_buh_cashin = $db->result($r,$i - 1,"price_buh_cashin");
                $weight_netto = $db->result($r,$i - 1,"weight_netto");
                $price_man_cashin = $db->result($r,$i - 1,"price_man_cashin");
                $price_man_usd = $db->result($r,$i - 1,"price_man_usd");
                $price_buh_uah = $db->result($r,$i - 1,"price_buh_uah");
                $price_man_uah = $db->result($r,$i - 1,"price_man_uah");
            }
            if ($oper_status == 30) {
                $list .= "<tr id='strRow_$i'>
                    <td>$i<input type='hidden' id='idStr_$i' value='$id'></td>
                    <td style='min-width:140px;'><input type='hidden' id='artIdStr_$i' value='$art_id'>
                        <div class='input-group'>
                            <input class='form-control input-xs' type='text' readonly id='article_nr_displStr_$i' value='$article_nr_displ' placeholder='������ ������'>
                            <span class='input-group-btn'> <button type='button' class='btn btn-xs btn-primary' onClick=\"showIncomeArticleSearchForm('$i','$art_id','$brand_id','$article_nr_displ');\"><i class=\"fa fa-bars\"></i></button> </span>
                        </div>
                    </td>
                    <td style='min-width:120px;'><input type='hidden' id='brandIdStr_$i' value='$brand_id'>
                        <input class='form-control input-xs' type='text' readonly id='brandNameStr_$i' value='$brand_name' placeholder='�����'>
                    </td>
                    <td style='min-width:120px;'><input type='hidden' id='costumsIdStr_$i' value='$costums_id'>
                        <div class='input-group'>
                            <input class='form-control input-xs' type='text' readonly id='costumsStr_$i' value='$costums_code' placeholder='���'>
                            <span class='input-group-btn'> <button type='button' class='btn btn-xs btn-primary' onClick=\"showIncomeCostumsSearchForm('$i','$art_id','$costums_id');\"><i class=\"fa fa-bars\"></i></button> </span>
                        </div>
                    </td>
                    <td><input type='text' id='amountStr_$i' value='$amount' class='form-control input-xs numberOnly'></td>
                    <td><input type='text' id='price_buh_cashinStr_$i' value='$price_buh_cashin' class='form-control input-xs numberOnly'></td>
                    <td><input type='text' id='weightNettoStr_$i' value='$weight_netto' class='form-control input-xs text-right numberOnly'></td>
                    <td><input type='text' readonly id='price_man_cashinStr_$i' value='$price_man_cashin' class='form-control input-xs text-right'></td>
                    <td><input type='text' readonly id='price_man_usdStr_$i' value='$price_man_usd' class='form-control input-xs text-right'></td>
                    <td><input type='text' readonly id='price_buh_uahStr_$i' value='$price_buh_uah' class='form-control input-xs text-right'></td>
                    <td><input type='text' readonly id='price_man_uahStr_$i' value='$price_man_uah' class='form-control input-xs text-right'></td>
                    <td><button class='btn btn-xs btn-default' onClick=\"dropIncomeStr('$i','$income_id','$art_id');\"><i class='fa fa-times'></i></button></td>
                </tr>";
            }
            if ($oper_status == 31) {
                if ($article_nr_displ != "") {
                    $list .= "<tr align='right'>
                        <td align='left'>$i</td>
                        <td align='left'>$article_nr_displ</td>
                        <td align='center'>$brand_name</td>
                        <td align='center'>$costums_code</td>
                        <td>".$slave->to_money($amount)."</td>
                        <td>".$slave->to_money($price_buh_cashin)."</td>
                        <td>".$weight_netto."</td>
                        <td>".$slave->to_money($price_man_cashin)."</td>
                        <td>".$slave->to_money($price_man_usd)."</td>
                        <td>".$slave->to_money($price_buh_uah)."</td>
                        <td>".$slave->to_money($price_man_uah)."</td>
                        <td align='center'>-</td>
                    </tr>";
                }
            }
        }
        if ($oper_status == 30) {
            $list = "<input type='hidden' id='kol_row' value='$kl_rw'>
                <tr id='incomeStrNewRow' class='hidden'>
                    <td>nom_i<input type='hidden' id='idStr_' value=''></td>
                    <td style='min-width:140px;'><input type='hidden' id='artIdStr_' value=''>
                        <div class='input-group'>
                            <input class='form-control input-xs' type='text' readonly id='article_nr_displStr_' value='' placeholder='������ ������'>
                            <span class='input-group-btn'> <button type='button' class='btn btn-xs btn-primary' onClick=\"showIncomeArticleSearchForm('i_0','0','0','');\"><i class=\"fa fa-bars\"></i></button> </span>
                        </div>
                    </td>
                    <td style='min-width:120px;'><input type='hidden' id='brandIdStr_' value=''>
                        <input class='form-control input-xs' type='text' readonly id='brandNameStr_' value='' placeholder='�����'>
                    </td>
                    <td style='min-width:120px;'><input type='hidden' id='costumsIdStr_' value=''>
                        <div class='input-group'>
                            <input class='form-control input-xs' type='text' readonly id='costumsStr_' value='' placeholder='���'>
                            <span class='input-group-btn'> <button type='button' class='btn btn-xs btn-primary' onClick=\"showIncomeCostumsSearchForm('i_0','0','0');\"><i class=\"fa fa-bars\"></i></button> </span>
                        </div>
                    </td>
                    <td><input type='text' id='amountStr_' value='' class='form-control input-xs numberOnly'></td>
                    <td><input type='text' id='price_buh_cashinStr_' value='' class='form-control input-xs text-right numberOnly'></td>
                    <td><input type='text' id='weightNettoStr_' value='' class='form-control input-xs text-right numberOnly'></td>
                    <td><input type='text' readonly id='price_man_cashinStr_' value='' class='form-control input-xs text-right'></td>
                    <td><input type='text' readonly id='price_man_usdStr_' value='' class='form-control input-xs text-right'></td>
                    <td><input type='text' readonly id='price_buh_uahStr_' value='' class='form-control input-xs text-right'></td>
                    <td><input type='text' readonly id='price_man_uahStr_' value='' class='form-control input-xs text-right'></td>
                    <td><button class='btn btn-xs btn-default' onClick=\"dropIncomeStr('i_0','0','0');\"><i class='fa fa-times'></i></button></td>
                </tr>".$list;
        }
        return $list;
    }

    function getArticleName($art_id) { $db = DbSingleton::getTokoDb();
        $name = "";
        $r = $db->query("SELECT * FROM `T2_NAMES` WHERE `ART_ID`='$art_id' AND `LANG_ID`='16' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $name = $db->result($r,0,"NAME");
        }
        return $name;
    }

    function getIncomeInfo($income_id) { $db = DbSingleton::getDb();
        $invoice_income = 0;
        $prefix = $doc_nom = $data = $client_seller = $storage_id = "";
        $r = $db->query("SELECT * FROM `J_INCOME` WHERE `id`='$income_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $prefix = $db->result($r,0,"prefix");
            $doc_nom = $db->result($r,0,"doc_nom");
            $data = $db->result($r,0,"data");
            $client_seller = $db->result($r,0,"client_seller");
            $storage_id = $db->result($r,0,"storage_id");
            $invoice_income = $db->result($r,0,"invoice_income");
        }
        return array($prefix, $doc_nom, $data, $client_seller, $storage_id, $invoice_income);
    }

    function printIncome($income_id) { $db = DbSingleton::getDb();
        $cat = new catalogue;
        $summ_buh = 0; $list = "";
        $form = ""; $form_htm = RD . "/tpl/income_print.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $r = $db->query("SELECT * FROM `J_INCOME_STR` WHERE `income_id`='$income_id' ORDER BY `id` ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $art_id = $db->result($r,$i - 1,"art_id");
            $article_nr_displ = $db->result($r,$i - 1,"article_nr_displ");
            $article_name = $this->getArticleName($art_id);
            $brand_id = $db->result($r,$i - 1,"brand_id");
            $brand_name = $cat->getBrandName($brand_id);
            $amount = $db->result($r,$i - 1,"amount");
            $price_buh_uah = $db->result($r,$i - 1,"price_buh_uah");
            $all_price_buh_uah = $price_buh_uah * $amount;
            $summ_buh += $all_price_buh_uah;
            $list .= "<tr>
                <td align='center'>$i</td>
                <td align='center'>$article_nr_displ</td>
                <td align='center'>$brand_name</td>
                <td align='left'>$article_name</td>
                <td align='center'>$amount</td>
                <td align='right'>$price_buh_uah</td>
                <td align='right'>$all_price_buh_uah</td>
            </tr>";
        }
        $form = str_replace("{ArticlesList}",$list,$form);
        list($prefix, $doc_nom, $data,, $storage_id, $invoice_income) = $this->getIncomeInfo($income_id);
        $form = str_replace("{prefix}",$prefix,$form);
        $form = str_replace("{doc_nom}",$doc_nom,$form);
        $form = str_replace("{data}",$data,$form);
        $form = str_replace("{storage_name}",$this->getStorageName($storage_id),$form);
        $form = str_replace("{curtime}",date("Y-m-d H:i:s"),$form);
        $form = str_replace("{invoice_income}",$invoice_income,$form);
        $form = str_replace("{pData}","",$form);
        $form = str_replace("{qrImage}","<img src='/phpqrcode/qrimage2.php?url=http://portal.myparts.pro/Income/printIn/$income_id/".time()."'>",$form);
        $form = str_replace("{summ_buh}",$summ_buh,$form);
        $mp = new media_print;
        $mp->print_document($form, array(210, 280));
        return $form;
    }

    function saveIncomeCard($income_id, $data, $client_seller, $invoice_income, $cash_id, $client_id, $invoice_data, $cours_to_uah, $cours_to_uah_nbu, $invoice_summ, $usd_to_uah, $eur_to_uah, $costums_pd_uah, $costums_pp_uah, $costums_summ_uah) { $db = DbSingleton::getDb();
        $slave = new slave;
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $answer = 0; $err = "������� ���������� �����!";
        $income_id = $slave->qq($income_id);$data=$slave->qq($data);$client_seller=$slave->qq($client_seller);$invoice_income=$slave->qq($invoice_income);$cash_id=$slave->qq($cash_id);
        $client_id=$slave->qq($client_id);$invoice_data=$slave->qq($invoice_data);$cours_to_uah=$slave->qq($cours_to_uah);$cours_to_uah_nbu=$slave->qq($cours_to_uah_nbu);
        $invoice_summ=$slave->qq($invoice_summ);$usd_to_uah=$slave->qq($usd_to_uah);$eur_to_uah=$slave->qq($eur_to_uah);$costums_pd_uah=$slave->qq($costums_pd_uah);
        $costums_pp_uah=$slave->qq($costums_pp_uah);$costums_summ_uah=$slave->qq($costums_summ_uah);
        if ($income_id > 0) {
            $k = 0;
            $r = $db->query("SELECT `invoice_income` FROM `J_INCOME` WHERE `status`=1 AND `id`!=$income_id;");
            $n = $db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $invoice_income_str = $db->result($r, $i - 1, "invoice_income");
                if ($invoice_income_str == $invoice_income) {
                    $k++;
                }
            }
            if ($k > 0) {
                $answer = 0; $err = "��������� ������� ��������";
            } else {
                $db->query("UPDATE `J_INCOME` SET `client_id`='$client_id', `prefix`='��', `data`='$data', `invoice_income`='$invoice_income', `invoice_data`='$invoice_data', 
                `invoice_summ`='$invoice_summ', `client_seller`='$client_seller', `cash_id`='$cash_id', `cours_to_uah`='$cours_to_uah', `cours_to_uah_nbu`='$cours_to_uah_nbu', 
                `usd_to_uah`='$usd_to_uah', `eur_to_uah`='$eur_to_uah', `summ_end`='$invoice_summ', `costums_pd_uah`='$costums_pd_uah', `costums_pp_uah`='$costums_pp_uah' , 
                `costums_summ_uah`='$costums_summ_uah', `user_id`='$user_id' WHERE `id`='$income_id';");
                $answer = 1; $err = "";
            }
        }
        return array($answer, $err);
    }

    function saveIncomeCardData($income_id, $frm, $tto, $idStr, $artIdStr, $article_nr_displStr, $brandIdStr, $countryIdStr, $costumsIdStr, $amountStr, $price_buh_cashinStr, $weightNettoStr, $rateStr, $typeDeclarationIdStr, $price_man_cashinStr, $price_man_usdStr, $price_buh_uahStr, $price_man_uahStr) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "������� ���������� �����!";
        $income_id = $slave->qq($income_id); $frm=$slave->qq($frm); $tto=$slave->qq($tto);//$type_id
        if ($income_id > 0) {
            $idStr=$slave->qq($idStr);$artIdStr=$slave->qq($artIdStr);$article_nr_displStr=$slave->qq($article_nr_displStr);$brandIdStr=$slave->qq($brandIdStr);$countryIdStr=$slave->qq($countryIdStr);
            $costumsIdStr=$slave->qq($costumsIdStr);$amountStr=$slave->qq($amountStr);$price_buh_cashinStr=$slave->qq($price_buh_cashinStr);$weightNettoStr=$slave->qq($weightNettoStr);
            $rateStr=$slave->qq($rateStr);$typeDeclarationIdStr=$slave->qq($typeDeclarationIdStr);$price_man_cashinStr=$slave->qq($price_man_cashinStr);$price_man_usdStr=$slave->qq($price_man_usdStr);
            $price_buh_uahStr=$slave->qq($price_buh_uahStr);$price_man_uahStr=$slave->qq($price_man_uahStr);
            for ($i = $frm; $i <= $tto; $i++) {
                $idS=$idStr[$i]; $artIdS=$artIdStr[$i]; $article_nr_displS=$article_nr_displStr[$i]; $brandIdS=$brandIdStr[$i]; $countryIdS=$countryIdStr[$i]; 	$costumsIdS=$costumsIdStr[$i]; 	$amountS=$amountStr[$i];
                $price_buh_cashinS=$price_buh_cashinStr[$i]; $weightNettoS=$weightNettoStr[$i]; $rateS=$rateStr[$i]; $typeDeclarationIdS=$typeDeclarationIdStr[$i]; $price_man_cashinS=$price_man_cashinStr[$i];
                $price_man_usdS=$price_man_usdStr[$i]; $price_buh_uahS=$price_buh_uahStr[$i]; $price_man_uahS=$price_man_uahStr[$i];
                if ($idS == "" || $idS == 0) {
                    $r = $db->query("SELECT MAX(`id`) as mid FROM `J_INCOME_STR`;");
                    $idS = 0 + $db->result($r,0,"mid") + 1;
                    $db->query("INSERT INTO `J_INCOME_STR` (`id`,`income_id`) VALUES ('$idS','$income_id');");
                }
                if ($idS > 0) {
                    if ($artIdS != "" && $artIdS > 0 && $article_nr_displS != "") {
                        $db->query("UPDATE `J_INCOME_STR` SET `art_id`='$artIdS', `article_nr_displ`='$article_nr_displS', `brand_id`='$brandIdS', `country_id`='$countryIdS', 
                        `costums_id`='$costumsIdS', `amount`='$amountS', `price_buh_cashin`='$price_buh_cashinS', `weight_netto`='$weightNettoS', `rate`='$rateS', 
                        `type_declaration_id`='$typeDeclarationIdS', `price_man_cashin`='$price_man_cashinS', `price_man_usd`='$price_man_usdS', `price_buh_uah`='$price_buh_uahS', 
                        `price_man_uah`='$price_man_uahS' where id='$idS' and income_id='$income_id';");
                    } else {
                        $db->query("DELETE FROM `J_INCOME_STR` WHERE `id`='$idS' AND `income_id`='$income_id';");
                    }
                }
            }
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

    function checkIncomeStr($income_id) { $db = DbSingleton::getDb();
        $dp = new dp();
        $err = 0;
        $r = $db->query("SELECT `art_id` FROM `J_INCOME_STR` WHERE `income_id`='$income_id';");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $art_id = $db->result($r, $i - 1, "art_id");
            if (!$dp->checkUKTZED($art_id)) {
                $err++;
            }
        }
        return $err;
    }

    /*
     * �������� �������� �������
     * */
    function makeIncomeCardFinish($income_id) { $db = DbSingleton::getDb(); $dbt = DbSingleton::getTokoDb();
        $slave = new slave; $cat = new catalogue;
        $answer = 0; $err = "";
        $income_id = $slave->qq($income_id);
        $r = $db->query("SELECT `oper_status`, `storage_id`, `storage_cells_id`, `client_id`, `import_1c` FROM `J_INCOME` WHERE `id`='$income_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $oper_status = $db->result($r,0,"oper_status");
            $storage_id = $db->result($r,0,"storage_id");
            $storage_cells_id = $db->result($r,0,"storage_cells_id");
            $client_id = $db->result($r,0,"client_id");
            $import_1c = $db->result($r,0,"import_1c");

            if ($storage_id == 0 || $storage_cells_id == 0 || $client_id == 0) {
                $answer = 0; $err = "�� ������� \"����� ���������\" ��� \"������ ���������\" ��� \"�볺��\". �������� �� ���������!";
            }

            if ($storage_id > 0 && $storage_cells_id > 0 && $client_id > 0) {
                if ($oper_status == 30) {
                    list($prefix, $doc_nom) = $this->getIncomeClientPrefixDocument($client_id);
                    $db->query("UPDATE `J_INCOME` SET `oper_status`='31', `prefix`='$prefix', `doc_nom`='$doc_nom' WHERE `id`='$income_id';");
                    $r1 = $db->query("SELECT * FROM `J_INCOME_STR` WHERE `income_id`='$income_id' ORDER BY `id` ASC;");
                    $n1 = $db->num_rows($r1);
                    for ($i = 1; $i <= $n1; $i++) {
                        $art_id = $db->result($r1,$i - 1,"art_id");
                        $amount = $db->result($r1,$i - 1,"amount");
                        $price_man_usd = $db->result($r1,$i - 1,"price_man_usd");
                        $price_buh_uah = $db->result($r1,$i - 1,"price_buh_uah");
                        $price_man_uah = $db->result($r1,$i - 1,"price_man_uah");
                        $country_id = $db->result($r1,$i - 1,"country_id");
                        $costums_id = $db->result($r1,$i - 1,"costums_id");
                        if ($import_1c == 1) {
                            $import_cell_id = $db->result($r1,$i - 1,"import_cell_id");
                            $storage_cells_id = $import_cell_id;
                        }

                        list($oper_price, $general_stock) = $cat->getArticleOperPriceGeneralStock($art_id);
                        $new_oper_price = round((($oper_price * $general_stock) + ($amount * $price_man_usd)) / ($amount + $general_stock), 2);
                        $new_general_stock = $amount + $general_stock;

                        $cat->setArticleOperPriceGeneralStock($art_id, $new_oper_price, $new_general_stock);

                        $slave->addJuornalArtDocs(1, $income_id, $art_id, $amount);

                        $dbt->query("INSERT INTO `T2_ARTICLES_STOCK` (`art_id`,`income_id`,`amount`,`price`,`oper_price`) VALUES ('$art_id','$income_id','$amount','$price_man_usd','$new_oper_price')");
                        /*
                            op_type=1-������, 2-������
                            parrent_type_id=1-��������� ���������, 2-��������� ���������, 3-������� �� ����������, 4-������� ����������
                        */
                        $db->query("INSERT INTO `T2_ARTICLES_PARTITIONS` (`art_id`,`op_type`,`parrent_type_id`,`parrent_doc_id`,`amount`,`rest`,`price`,`oper_price`,`price_buh_uah`,`price_man_uah`) 
                        VALUES ('$art_id','1','1','$income_id','$amount','$amount','$price_man_usd','$new_oper_price','$price_buh_uah','$price_man_uah')");

                        $r2 = $dbt->query("SELECT `AMOUNT` FROM `T2_ARTICLES_STRORAGE_CELLS` WHERE `ART_ID`='$art_id' AND `STORAGE_ID`='$storage_id' AND `STORAGE_CELLS_ID`='$storage_cells_id' LIMIT 1;");
                        $n2 = $dbt->num_rows($r2);
                        if ($n2 == 1) {
                            $amount_ex = $dbt->result($r2, 0, "AMOUNT");
                            $amount_ex += $amount;
                            $dbt->query("UPDATE `T2_ARTICLES_STRORAGE_CELLS` SET `AMOUNT`='$amount_ex' WHERE `ART_ID`='$art_id' AND `STORAGE_ID`='$storage_id' AND `STORAGE_CELLS_ID`='$storage_cells_id' LIMIT 1;");
                        }
                        if ($n2 == 0) {
                            $dbt->query("INSERT INTO `T2_ARTICLES_STRORAGE_CELLS` (`ART_ID`,`AMOUNT`,`STORAGE_ID`,`STORAGE_CELLS_ID`) VALUES ('$art_id','$amount','$storage_id','$storage_cells_id');");
                        }

                        $rs = $dbt->query("SELECT `AMOUNT` as `amount` FROM `T2_ARTICLES_STRORAGE` WHERE `ART_ID`='$art_id' AND `STORAGE_ID`='$storage_id';");
                        $ns = $dbt->num_rows($rs);
                        $ers = 0;
                        if ($ns > 0) {
                            $amount_ex = $db->result($rs, 0, "amount") + 0;
                            $amount_ex += $amount;
                            $ers = 1;
                            $dbt->query("UPDATE `T2_ARTICLES_STRORAGE` SET `AMOUNT`='$amount_ex' WHERE `ART_ID`='$art_id' AND `STORAGE_ID`='$storage_id';");
                        }
                        if ($ns == 0 && $ers == 0) {
                            $dbt->query("INSERT INTO `T2_ARTICLES_STRORAGE` (`ART_ID`,`AMOUNT`,`RESERV_AMOUNT`,`STORAGE_ID`) VALUES ('$art_id','$amount','0','$storage_id');");
                        }
                        $this->saveIncomeZED($art_id, $country_id, $costums_id);
                    }
                    $answer = 1; $err = "";
                } else {
                    $answer = 0; $err = "�������� �����������. ���� ������� ����������.";
                }
            }
        }
        return array($answer, $err);
    }

    function loadIncomeUnknownArticles($income_id) { $db = DbSingleton::getDb();
        $form = ""; $form_htm = RD . "/tpl/income_unknown_articles_list.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $r = $db->query("SELECT * FROM `J_INCOME` WHERE `id`='$income_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 0) {
            $form_htm = RD . "/tpl/access_deny.htm";
            if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        }
        if ($n == 1) {
            list($list, $kol_rows) = $this->showIncomeUnknownStrList($income_id);
            $form = str_replace("{UnknownArticlesList}",$list,$form);
            $form = str_replace("{kol_rows}",$kol_rows,$form);
            $form = str_replace("{income_id}",$income_id,$form);
        }
        return $form;
    }

    function showIncomeUnknownStrList($income_id) { $db = DbSingleton::getDb();
        $cat = new catalogue;
        $list = "";
        $r = $db->query("SELECT * FROM `J_INCOME_STR_UNKNOWN` WHERE `income_id`='$income_id' ORDER BY `id` ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r,$i - 1,"id");
            $art_id = $db->result($r,$i - 1,"art_id");
            $art_id_comment = "";
            if ($art_id == 0) {
                $art_id_comment = "�� ��������� ART_ID! ������� �������� � ���";
            }
            $article_nr_displ = $db->result($r,$i - 1,"article_nr_displ");
            $brand_id = $db->result($r,$i - 1,"brand_id");
            $brand_name = $cat->getBrandName($brand_id);
            $brand_id_comment = "";
            if ($brand_id == 0 || $brand_name == "") {
                $brand_id_comment = "�� ��������� �����!";
            }
            $country_id = $db->result($r,$i - 1,"country_id");
            $country_abr = $cat->getCountryAbr($country_id);
            $country_id_comment = "";
            if ($country_id == 0 || $country_abr == "") {
                $country_id_comment = "�� ��������� �����!";
            }
            $costums_id = $db->result($r,$i - 1,"costums_id");
            $costums_code = $cat->getCostumsCode($costums_id);
            $costums_id_comment = "";
            if ($costums_id == 0 || $costums_code == "") {
                $costums_id_comment = "�� ��������� ������ ���!";
            }
            $amount = $db->result($r,$i - 1,"amount");
            $amount_comment = "";
            if ($amount <= 0) {
                $amount_comment = "�� ��������� ������� ������!";
            }
            $price_buh_cashin = $db->result($r,$i - 1,"price_buh_cashin");
            $price_comment = "";
            if ($price_buh_cashin <= 0) {
                $price_comment = "�� ��������� ���� ������!";
            }
            $weight_netto = $db->result($r,$i - 1,"weight_netto");
            $weight_comment = "";
            if ($weight_netto <= 0) {
                $weight_comment = "�� ��������� ���� ������!";
            }
            $list .= "<tr id='strUnRow_$i'>
                <td><button class='btn btn-xs btn-warning' onClick='checkIncomUnStr(\"$income_id\",\"$i\",\"$id\",\"0\");'><i class='fa fa-refresh'></i></button></td>
                <td>$i<input type='hidden' id='idUnStr_$i' value='$id'></td>
                <td style='min-width:140px;'><input type='hidden' id='artIdUnStr_$i' value='$art_id'>
                    <div class='input-group'>
                        <input class='form-control input-xs' type='text' readonly id='article_nr_displUnStr_$i' value='$article_nr_displ' placeholder='������ ������'>
                        <span class='input-group-btn'> <button type='button' class='btn btn-xs btn-primary' onClick=\"showIncomeArticleSearchForm('$i','$art_id','$brand_id','$article_nr_displ');\"><i class=\"fa fa-bars\"></i></button> </span>
                    </div>$art_id_comment
                </td>
                <td style='min-width:120px;'><input type='hidden' id='brandIdUnStr_$i' value='$brand_id'>
                    <input class='form-control input-xs' type='text' readonly id='brandNameUnStr_$i' value='$brand_name' placeholder='�����'>$brand_id_comment
                </td>
                <td style='min-width:80px;'><input type='hidden' id='countryIdStr_$i' value='$country_id'>
                    <div class='input-group'>
                        <input class='form-control input-xs' type='text' readonly id='countryAbrUnStr_$i' value='$country_abr' placeholder='���'>
                        <span class='input-group-btn'> <button type='button' class='btn btn-xs btn-primary' onClick=\"showIncomeCountrySearchForm('$i','$art_id','$country_id');\"><i class=\"fa fa-bars\"></i></button> </span>
                    </div>$country_id_comment
                </td>
                <td style='min-width:120px;'><input type='hidden' id='costumsIdUnStr_$i' value='$costums_id'>
                    <div class='input-group'>
                        <input class='form-control input-xs' type='text' readonly id='costumsUnStr_$i' value='$costums_code' placeholder='���'>
                        <span class='input-group-btn'> <button type='button' class='btn btn-xs btn-primary' onClick=\"showIncomeCostumsSearchForm('$i','$art_id','$costums_id');\"><i class=\"fa fa-bars\"></i></button> </span>
                    </div>$costums_id_comment
                </td>
                <td><input type='text' id='amountUnStr_$i' value='$amount' class='form-control input-xs numberOnly'>$amount_comment</td>
                <td><input type='text' id='price_buh_cashinUnStr_$i' value='$price_buh_cashin' class='form-control input-xs numberOnly'>$price_comment</td>
                <td><input type='text' id='weightNettoUnStr_$i' value='$weight_netto' class='form-control input-xs text-right numberOnly'>$weight_comment</td>
                <td><button class='btn btn-xs btn-default' onClick='dropIncomUnStr(\"$income_id\",\"$i\",\"$id\")'><i class='fa fa-times'></i></button></td>
            </tr>";
        }
        return array($list, $n);
    }

    function exportIncomeUnStr($income_id) { $db = DbSingleton::getDb();
        $cat = new catalogue;
        $list = array();
        $r = $db->query("SELECT * FROM `J_INCOME_STR_UNKNOWN` WHERE `income_id`='$income_id' ORDER BY `id` ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $comment = "";
            $art_id = $db->result($r,$i - 1,"art_id");
            if ($art_id == 0) {
                $comment .= "�� ��������� ART_ID! ������� �������� � ���";
            }
            $article_nr_displ = $db->result($r,$i - 1,"article_nr_displ");
            $brand_id = $db->result($r,$i - 1,"brand_id");
            $brand_name = $cat->getBrandName($brand_id);
            if ($brand_id == 0 || $brand_name == "") {
                $comment .= "�� ��������� �����!";
            }
            $country_id = $db->result($r,$i - 1,"country_id");
            $country_abr = $cat->getCountryAbr($country_id);
            if ($country_id == 0 || $country_abr == "") {
                $comment .= "�� ��������� �����!";
            }
            $costums_id = $db->result($r,$i - 1,"costums_id");
            $costums_code = $cat->getCostumsCode($costums_id);
            if ($costums_id == 0 || $costums_code == "") {
                $comment .= "�� ��������� ������ ���!";
            }
            $amount = $db->result($r,$i - 1,"amount");
            if ($amount <= 0) {
                $comment .= "�� ��������� ������� ������!";
            }
            $price_buh_cashin = $db->result($r,$i - 1,"price_buh_cashin");
            if ($price_buh_cashin <= 0) {
                $comment .= "�� ��������� ���� ������!";
            }
            $weight_netto = $db->result($r,$i - 1,"weight_netto");
            if ($weight_netto <= 0) {
                $comment .= "�� ��������� ���� ������!";
            }
            $list[$i] = array("$i", "$art_id", "$article_nr_displ", "$brand_name", "$country_abr", "$costums_code", "$amount", "$price_buh_cashin", "$weight_netto", "$comment");
        }
        return $list;
    }

    function getBarcode($art_id) { $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT * FROM `T2_BARCODES` WHERE `ART_ID`='$art_id' LIMIT 1;");
        return $db->result($r,0,"BARCODE");
    }

    function exportIncomeStr($income_id) { $db = DbSingleton::getDb();
        $cat = new catalogue;
        $list = array();
        $r = $db->query("SELECT * FROM `J_INCOME_STR` WHERE `income_id`='$income_id' ORDER BY `id` ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $art_id = $db->result($r,$i - 1,"art_id");
            $article_nr_displ = $db->result($r,$i - 1,"article_nr_displ");
            $amount = $db->result($r,$i - 1,"amount");
            $text = $cat->getArticleName($art_id);
            $barcode = $this->getBarcode($art_id);
            $unique_number = $cat->getUniqueNumber($art_id);
            $list[$i] = array("$article_nr_displ", "$text", "$barcode", "$amount", "$unique_number", "$article_nr_displ");
        }
        return $list;
    }

    function checkIncomUnStr($income_id, $unknown_id, $art_id, $article_nr_displ, $brand_id, $country_id, $costums_id, $amount, $price, $weight) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "";
        $income_id = $slave->qq($income_id); $unknown_id = $slave->qq($unknown_id);
        $r = $db->query("SELECT `type_id`, `oper_status` FROM `J_INCOME` WHERE `id`='$income_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $oper_status = $db->result($r,0,"oper_status");
            $income_type_id = $db->result($r,0,"type_id");
            if ($oper_status == 30) {
                $article_nr_displ = $slave->qq($article_nr_displ);
                $art_id=$slave->qq($art_id); $brand_id=$slave->qq($brand_id); $country_id=$slave->qq($country_id); $costums_id=$slave->qq($costums_id); $amount=$slave->qq($amount); $price=$slave->qq($price); $weight=$slave->qq($weight);
                if (($income_type_id == 1 && ($art_id > 0 && $brand_id > 0 && $country_id > 0 && $costums_id > 0 && $amount > 0 && $price > 0 && $weight > 0)) || ($income_type_id == 0 && ($art_id > 0 && $amount > 0 && $price > 0))) {
                    $db->query("UPDATE `J_INCOME_STR` SET `unknown_id`='0', `art_id`='$art_id', `article_nr_displ`='$article_nr_displ', `brand_id`='$brand_id', `country_id`='$country_id', 
                    `costums_id`='$costums_id', `amount`='$amount', `price_buh_cashin`='$price', `weight_netto`='$weight' WHERE `income_id`='$income_id' AND `unknown_id`='$unknown_id' LIMIT 1;");
                    $db->query("DELETE FROM `J_INCOME_STR_UNKNOWN` WHERE `income_id`='$income_id' AND `id`='$unknown_id' LIMIT 1;");
                    $answer = 1; $err = "";
                } else {
                    $answer = 0; $err = "�� ��������� �� ���� ��� ��������";
                }
            } else {
                $answer = 0; $err = "�������� �����������. ���� ������� ����������.";
            }
        }
        return array($answer, $err);
    }

    function dropIncomUnStr($income_id, $unknown_id) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "";
        $income_id = $slave->qq($income_id); $unknown_id = $slave->qq($unknown_id);
        $r = $db->query("SELECT `oper_status` FROM `J_INCOME` WHERE `id`='$income_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $oper_status = $db->result($r, 0, "oper_status");
            if ($oper_status == 30) {
                $db->query("DELETE FROM `J_INCOME_STR` WHERE `income_id`='$income_id' AND `unknown_id`='$unknown_id' LIMIT 1;");
                $db->query("DELETE FROM `J_INCOME_STR_UNKNOWN` WHERE `income_id`='$income_id' AND `id`='$unknown_id' LIMIT 1;");
                $answer = 1; $err = "";
            } else {
                $answer = 0; $err = "�������� �����������. ���� ������� ����������.";
            }
        }
        return array($answer, $err);
    }

    function dropIncomeStr($income_id, $art_id) {$db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "������� ��������� �����!";
        $income_id = $slave->qq($income_id);
        $r = $db->query("SELECT `oper_status` FROM `J_INCOME` where `id`='$income_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $oper_status = $db->result($r,0,"oper_status");
            if ($oper_status == 30) {
                $db->query("DELETE FROM `J_INCOME_STR` WHERE `income_id`='$income_id' AND `art_id`='$art_id' LIMIT 1;");
                $answer = 1; $err = "";
            } else {
                $answer = 0; $err = "�������� �����������. ���� ������� ����������.";
            }
        }
        return array($answer, $err);
    }

    function clearIncomeStr($income_id) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "";
        $income_id = $slave->qq($income_id);
        $r = $db->query("SELECT `oper_status` FROM `J_INCOME` WHERE `id`='$income_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $oper_status = $db->result($r,0,"oper_status");
            if ($oper_status == 30) {
                $db->query("DELETE FROM `J_INCOME_STR` WHERE `income_id`='$income_id';");
                $db->query("DELETE FROM `J_INCOME_STR_UNKNOWN` WHERE `income_id`='$income_id';");
                $answer = 1; $err = "";
            } else {
                $answer = 0; $err = "�������� �����������. ���� ������� ����������.";
            }
        }
        return array($answer, $err);
    }

    function setIncomeVat($income_id,$vat_use){$db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "";
        $income_id = $slave->qq($income_id); $vat_use = $slave->qq($vat_use);
        $r = $db->query("SELECT `oper_status` FROM `J_INCOME` WHERE `id`='$income_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $oper_status = $db->result($r,0,"oper_status");
            if ($oper_status == 30) {
                $db->query("UPDATE `J_INCOME` SET `vat_use`='$vat_use' WHERE `id`='$income_id';");
                $answer = 1; $err = "";
            } else {
                $answer = 0; $err = "�������� �����������. ���� ������� ����������.";
            }
        }
        return array($answer, $err);
    }

    function showIncomeClientList($sel_id){$db = DbSingleton::getDb();
        $slave = new slave;
        $list = "";
        $form = ""; $form_htm = RD . "/tpl/clients_parrent_tree.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $r = $db->query("SELECT c.*, ot.name as org_type_name, t2cn.COUNTRY_NAME, t2st.STATE_NAME, t2rg.REGION_NAME, t2ct.CITY_NAME  
        FROM `A_CLIENTS` c 
            LEFT OUTER JOIN `A_ORG_TYPE` ot on ot.id=c.org_type 
            LEFT OUTER JOIN `T2_COUNTRIES` t2cn on t2cn.COUNTRY_ID=c.country 
            LEFT OUTER JOIN `T2_STATE` t2st on t2st.STATE_ID=c.state
            LEFT OUTER JOIN `T2_REGION` t2rg on t2rg.REGION_ID=c.region
            LEFT OUTER JOIN `T2_CITY` t2ct on t2ct.CITY_ID=c.city
            LEFT OUTER JOIN `A_CLIENTS_CATEGORY` cc on cc.client_id=c.id
            LEFT OUTER JOIN `A_CATEGORY` ac on ac.id=cc.category_id
        WHERE c.status=1 AND ac.id=3;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r,$i - 1,"id");
            $name = $slave->qqback_js($db->result($r,$i - 1,"name"));
            $org_type_name = $db->result($r,$i - 1,"org_type_name");
            $email = $db->result($r,$i - 1,"email");
            $phone = $db->result($r,$i - 1,"phone");
            $country = $db->result($r,$i - 1,"COUNTRY_NAME");
            $state = $db->result($r,$i - 1,"STATE_NAME");
            $region = $db->result($r,$i - 1,"REGION_NAME");
            $city = $db->result($r,$i - 1,"CITY_NAME");
            $cur = ($id == $sel_id) ? "background-color:#0CF;" : "";
            $base_name = base64_encode(iconv("windows-1251","utf-8",$name));
            $list .= "<tr style='cursor:pointer;$cur' onClick='setIncomeClient(\"$id\", \"$base_name\");'>
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
        $form = str_replace("{list}",$list,$form);
        return $form;
    }

    function unlinkIncomeClient($income_id) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "������� ���������� �����!";
        $income_id = $slave->qq($income_id);
        if ($income_id > 0) {
            $db->query("UPDATE `J_INCOME` SET `client_id`='0' WHERE `id`='$income_id';");
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

    function showIncomeClientSellerList($sel_id) { $db = DbSingleton::getDb();
        $list = "";
        $form = ""; $form_htm = RD . "/tpl/clients_parrent_tree.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $r = $db->query("SELECT c.*, ot.name as org_type_name, t2cn.COUNTRY_NAME, t2st.STATE_NAME, t2rg.REGION_NAME, t2ct.CITY_NAME 
        FROM `A_CLIENTS` c 
            LEFT OUTER JOIN A_ORG_TYPE ot on ot.id=c.org_type 
            LEFT OUTER JOIN T2_COUNTRIES t2cn on t2cn.COUNTRY_ID=c.country 
            LEFT OUTER JOIN T2_STATE t2st on t2st.STATE_ID=c.state
            LEFT OUTER JOIN T2_REGION t2rg on t2rg.REGION_ID=c.region
            LEFT OUTER JOIN T2_CITY t2ct on t2ct.CITY_ID=c.city
            LEFT OUTER JOIN A_CLIENTS_CATEGORY cc on cc.client_id=c.id
            LEFT OUTER JOIN A_CATEGORY ac on ac.id=cc.category_id
        WHERE c.status=1 AND ac.id=2;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r,$i - 1,"id");
            $name = $db->result($r,$i - 1,"name");
            $org_type_name = $db->result($r,$i - 1,"org_type_name");
            $email = $db->result($r,$i - 1,"email");
            $phone = $db->result($r,$i - 1,"phone");
            $country = $db->result($r,$i - 1,"COUNTRY_NAME");
            $state = $db->result($r,$i - 1,"STATE_NAME");
            $region = $db->result($r,$i - 1,"REGION_NAME");
            $city = $db->result($r,$i - 1,"CITY_NAME");
            $fn = "<i class='fa fa-thumb-tack'></i>";
            $cur = ($id == $sel_id) ? "background-color:#0CF;" : "";
            $base_name = base64_encode(iconv("windows-1251", "utf-8", $name));
            $list .= "<tr style='cursor:pointer; $cur' onClick='setIncomeClientSeller(\"$id\", \"$base_name\")'>
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

    function unlinkIncomeClientSeller($income_id) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "������� ���������� �����!";
        $income_id = $slave->qq($income_id);
        if ($income_id > 0) {
            $db->query("UPDATE `J_INCOME` SET `client_seller_id`='0' WHERE `id`='$income_id';");
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

    function showIncomeArticleSearchForm($brand_id, $article_nr_display) {
        $cat = new catalogue;
        $form = ""; $form_htm = RD . "/tpl/income_artilce_search_form.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        list($range_list, $list_brand_select) = $cat->showArticlesSearchDocumentList($article_nr_display, $brand_id, 0);
        $form = str_replace("{article_nr_display}",$article_nr_display,$form);
        $form = str_replace("{range_list}",$range_list,$form);
        $form = str_replace("{list_brand_select}",$list_brand_select,$form);
        return $form;
    }

    function loadIncomeStorage($income_id) { $db = DbSingleton::getDb();
        $form = ""; $form_htm = RD . "/tpl/income_storage_form.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $r = $db->query("SELECT `storage_id`, `storage_cells_id` FROM `J_INCOME` WHERE `id`='$income_id' LIMIT 1;");
        $storage_id = $db->result($r, 0, "storage_id");
        $storage_cells_id = $db->result($r, 0, "storage_cells_id");
        $form = str_replace("{income_id}",$income_id,$form);
        $form = str_replace("{storage_list}",$this->showStorageSelectList($storage_id),$form);
        $form = str_replace("{storage_cells_list}",$this->showStorageCellsSelectList($storage_id, $storage_cells_id),$form);
        return $form;
    }

    function getStorageName($sel_id) { $db = DbSingleton::getTokoDb();
        $name = "";
        $r = $db->query("SELECT `name` FROM `STORAGE` WHERE `status`='1' AND `id`='$sel_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $name = $db->result($r,0,"name");
        }
        return $name;
    }

    function showStorageSelectList($sel_id) { $db = DbSingleton::getTokoDb();
        $list = "<option value=0>������ � ������</option>";
        $r = $db->query("SELECT * FROM `STORAGE` WHERE `status`='1' ORDER BY `name` ASC, `id` ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r,$i - 1,"id");
            $name = $db->result($r,$i - 1,"name");
            $sel = ($sel_id == $id) ? "selected='selected'" : "";
            $list .= "<option value='$id' $sel>$name</option>";
        }
        return $list;
    }

//    function getStorageCellName($sel_id){$db = DbSingleton::getTokoDb();$name="";
//        $r=$db->query("SELECT `cell_value` FROM `STORAGE_CELLS` WHERE `status`='1' AND `id`='$sel_id' LIMIT 1;");$n=$db->num_rows($r);
//        if ($n==1){$name=$db->result($r,0,"cell_value");}
//        return $name;
//    }

    function showStorageCellsSelectList($storage_id, $sel_id) { $db = DbSingleton::getDb();
        $list = "<option value=0>������ � ������</option>";
        $r = $db->query("SELECT * FROM `STORAGE_CELLS` WHERE `status`='1' AND `storage_id`='$storage_id' ORDER BY `cell_value` ASC, `id` ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r,$i - 1,"id");
            $cell_value = $db->result($r,$i - 1,"cell_value");
            $sel = ($sel_id == $id) ? "selected='selected'" : "";
            $list .= "<option value='$id' $sel>$cell_value</option>";
        }
        return $list;
    }

    function checkStorageCellsExist($storage_id) { $db = DbSingleton::getTokoDb();
        $ex = 0;
        $r = $db->query("SELECT COUNT(`id`) as kol FROM `STORAGE_CELLS` WHERE `status`='1' AND `storage_id`='$storage_id';");
        $kol = $db->result($r,0,"kol");
        if ($kol > 0) {
            $ex = 1;
        }
        return $ex;
    }

    function saveIncomeStorage($income_id, $storage_id, $storage_cells_id) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "������� ���������� �����!";
        $income_id = $slave->qq($income_id); $storage_id=$slave->qq($storage_id); $storage_cells_id=$slave->qq($storage_cells_id);
        if ($income_id > 0 && $storage_id > 0) {
            $ex_cells = $this->checkStorageCellsExist($storage_id);
            if (($ex_cells == 1 && $storage_cells_id > 0) || $ex_cells == 0) {
                $db->query("UPDATE `J_INCOME` SET `storage_id`='$storage_id', `storage_cells_id`='$storage_cells_id' WHERE `id`='$income_id';");
                $answer = 1; $err = "";
            }
            if ($ex_cells == 1 && ($storage_cells_id == 0 || $storage_cells_id == "")) {
                $answer = 0; $err = "�� ������� ������ ��������� ��� ������";
            }
        }
        return array($answer, $err);
    }

    function getIncomeSpendSumms($income_id) { $db = DbSingleton::getDb();
        $summs = array();
        $rs = $db->query("SELECT * FROM `SPEND_ITEM` WHERE `ison`='1' ORDER BY `id` ASC;");
        $ns = $db->num_rows($rs);
        for ($is = 1; $is <= $ns; $is++) {
            $spend_item_id = $db->result($rs,$is-1,"id");
            $r = $db->query("SELECT SUM(`summ_uah`) as summ FROM `J_INCOME_SPEND_STR` WHERE `income_id`='$income_id' AND `spend_item_id`='$spend_item_id' AND `status`='1';");
            $summ_str_uah = 0 + $db->result($r,0,"summ");
            $summs[$is] = $summ_str_uah;
        }
        return $summs;
    }

    function loadIncomeSpend($income_id) { $db = DbSingleton::getDb();
        $slave = new slave; $gmanual=new gmanual;
        $list = "";
        $item = ""; $form_htm = RD . "/tpl/income_spend_list.htm";
        if (file_exists("$form_htm")) { $item = file_get_contents($form_htm); }
        $rs = $db->query("SELECT * FROM `SPEND_ITEM` WHERE `ison`='1' ORDER BY `id` ASC;");
        $ns = $db->num_rows($rs);
        for ($is = 1; $is <= $ns; $is++) {
            $spend_item_id = $db->result($rs,$is-1,"id");
            $spend_item_caption = $db->result($rs,$is-1,"name");
            $spend_type_id = $db->result($rs,$is-1,"spend_type_id");
            $spend_type_caption = $gmanual->get_gmanual_caption($spend_type_id);
            $split_type_id = $db->result($rs,$is-1,"split_type_id");
            $split_type_caption = $gmanual->get_gmanual_caption($split_type_id);
            $list .= $item;
            $list = str_replace("{income_id}",$income_id,$list);
            $list = str_replace("{spend_item_id}",$spend_item_id,$list);
            $list = str_replace("{spend_item_caption}",$spend_item_caption,$list);
            $list = str_replace("{spend_type_caption}",$spend_type_caption,$list);
            $list = str_replace("{split_type_caption}",$split_type_caption,$list);

            $r = $db->query("SELECT * FROM `J_INCOME_SPEND_STR` WHERE `income_id`='$income_id' AND `spend_item_id`='$spend_item_id' AND `status`='1' ORDER BY `data` ASC, `id` ASC;");
            $n = $db->num_rows($r);
            $list2 = ""; $summ_str_uah = 0;
            for ($i = 1; $i <= $n; $i++) {
                $str_id = $db->result($r,$i - 1,"id");
                $cash_abr = $this->getCashAbr($db->result($r,$i - 1,"cash_id"));
                $summ_cash = $db->result($r,$i - 1,"summ_cash");
                $kours = $db->result($r,$i - 1,"kours");
                $summ_uah = $db->result($r,$i - 1,"summ_uah");
                $summ_str_uah += $summ_uah;
                $data = $slave->data_word($db->result($r,$i - 1,"data"));
                $caption = $db->result($r,$i - 1,"caption");
                $files = $this->showIncomeSpendItemFiles($income_id, $str_id);
                $list2 .= "<tr align='center'>
                    <td>$i</td>
                    <td>$data</td>
                    <td align='left'>$caption</td>
                    <td>$cash_abr</td>
                    <td align='right'>".$slave->to_money($summ_cash)."</td>
                    <td align='right'>".$slave->to_money($kours)."</td>
                    <td align='right'>".$slave->to_money($summ_uah)."</td>
                    <td>$files</td>
                    <td>
                        <button class='btn btn-sm btn-warning' onClick=\"showIncomeSpendItemRow('$income_id','$spend_item_id','$str_id');\"><i class='fa fa-edit'></i></button>
                        <button class='btn btn-sm btn-default' onClick=\"dropIncomeSpendItemRow('$income_id','$spend_item_id','$str_id');\"><i class='fa fa-times'></i></button>
                    </td>
                </tr>";
            }
            if ($list2 == "") {
                $list2 = "<tr><td colspan=10 align='center'>³������ �������</td></tr>";
            }
            $list = str_replace("{records_list}",$list2,$list);
            $list = str_replace("{summ_str_uah}",$slave->to_money($summ_str_uah),$list);
        }
        return $list;
    }

    function showIncomeSpendItemRow($income_id, $spend_item_id, $str_id) { $db = DbSingleton::getDb();
        $form = ""; $form_htm = RD . "/tpl/income_spend_item_form.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $r = $db->query("SELECT * FROM `J_INCOME_SPEND_STR` WHERE `id`='$str_id' LIMIT 1;");
        $cash_id = $db->result($r,0,"cash_id");
        $summ_cash = $db->result($r,0,"summ_cash");
        $kours = $db->result($r,0,"kours");
        $summ_uah = $db->result($r,0,"summ_uah");
        $data = $db->result($r,0,"data");
        $caption = $db->result($r,0,"caption");
        if ($kours == "") {
            $kours = 1;
        }
        $form = str_replace("{income_id}",$income_id,$form);
        $form = str_replace("{spend_item_id}",$spend_item_id,$form);
        $form = str_replace("{str_id}",$str_id,$form);
        $form = str_replace("{data}",$data,$form);
        $form = str_replace("{caption}",$caption,$form);
        $form = str_replace("{kours}",$kours,$form);
        $form = str_replace("{summ_uah}",$summ_uah,$form);
        $form = str_replace("{summ_cash}",$summ_cash,$form);
        $form = str_replace("{cash_list}",$this->showCashListSelect($cash_id, 1),$form);
        return array($form, "���������� ��� �������");
    }

    function saveIncomeSpendStrForm($income_id, $spend_item_id, $str_id, $caption, $data, $cash_id, $summ_cash, $kours, $summ_uah) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer=0; $err="������� ���������� �����!";
        $income_id = $slave->qq($income_id);$spend_item_id=$slave->qq($spend_item_id);$str_id=$slave->qq($str_id);$caption=$slave->qq($caption);$data=$slave->qq($data);
        $cash_id=$slave->qq($cash_id);$summ_cash=$slave->qq($slave->point_valid($summ_cash));$kours=$slave->qq($slave->point_valid($kours));$summ_uah=$slave->qq($slave->point_valid($summ_uah));
        if ($income_id>0 && $spend_item_id>0) {
            if ($str_id==0 || $str_id=="") {
                $r=$db->query("SELECT MAX(`id`) as mid FROM `J_INCOME_SPEND_STR`;");$str_id=0+$db->result($r,0,"mid")+1;
                $db->query("INSERT INTO `J_INCOME_SPEND_STR` (`id`,`income_id`,`spend_item_id`) VALUES ('$str_id','$income_id','$spend_item_id');");
            }
            $db->query("UPDATE `J_INCOME_SPEND_STR` SET `caption`='$caption', `data`='$data', `cash_id`='$cash_id', `summ_cash`='$summ_cash', `kours`='$kours', `summ_uah`='$summ_uah' WHERE `id`='$str_id' AND `income_id`='$income_id';");
            $answer=1; $err="";
        }
        return array($answer, $err);
    }

    function dropIncomeSpendItemRow($income_id, $str_id) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "������� ���������� �����!";
        $income_id = $slave->qq($income_id); $str_id = $slave->qq($str_id);//$spend_item_id
        if ($income_id > 0 && $str_id > 0) {
            $db->query("UPDATE `J_INCOME_SPEND_STR` SET `status`='0' WHERE `id`='$str_id' AND `income_id`='$income_id';");
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

    function showIncomeSpendItemFiles($income_id, $str_id) { $db = DbSingleton::getDb();
        $list = "<button class='btn btn-sm btn-default' onClick=\"showIncomeSpendItemFileUpload('$income_id','$str_id');\"><i class='fa fa-upload'></i></button>";
        $r = $db->query("SELECT * FROM `J_INCOME_SPEND_FILES` WHERE `str_id`='$str_id' AND `status`='1' ORDER BY `id` ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $name = $db->result($r,$i - 1,"name");
            $file_name = $db->result($r,$i - 1,"file_name");
            $list .= "<a class='btn btn-default btn-sm' href='http://portal.myparts.pro/cdn/incomeSpendFiles/$str_id/$file_name' target='_blank'>$name</a> &nbsp;";
        }
        return $list;
    }

    function getRateTypeDeclarationdocumentPos($costums_id, $country_id) { $db = DbSingleton::getTokoDb();
        $manual = new manual;
        $rate = 0; $type_declaration = ""; $type_declaration_id = 0; $duty = 0;
        $r = $db->query("SELECT `DUTY` FROM `T2_COUNTRIES` WHERE `country_id`='$country_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $duty = $db->result($r,0,"DUTY");
        }
        $r = $db->query("SELECT `PREFERENTIAL_RATE`, `FULL_RATE`, `TYPE_DECLARATION` FROM `T2_COSTUMS` WHERE `costums_id`='$costums_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $preferential_rate = $db->result($r,0,"PREFERENTIAL_RATE");
            $full_rate = $db->result($r,0,"FULL_RATE");
            $rate = $preferential_rate;
            if ($duty == 2) {
                $rate = $full_rate;
            }
            $type_declaration_id = $db->result($r,0,"TYPE_DECLARATION");
            $type_declaration = $manual->getManualMCaption("costums_type_declaration", $type_declaration_id);
        }
        return array($rate, $type_declaration, $type_declaration_id);
    }

    function saveIncomeContactForm($income_id, $contact_id, $contact_name, $contact_post, $contact_con_kol, $con_id, $sotc_cont, $contact_value) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "������� ���������� �����!";
        $income_id = $slave->qq($income_id);$contact_id=$slave->qq($contact_id);$contact_name=$slave->qq($contact_name);$contact_post=$slave->qq($contact_post);$contact_con_kol=$slave->qq($contact_con_kol);
        if ($income_id > 0) {
            if ($contact_id == 0 || $contact_id == "") {
                $r = $db->query("SELECT MAX(`id`) as mid FROM `A_CLIENTS_CONTACTS`;");
                $contact_id = 0 + $db->result($r,0,"mid") + 1;
                $db->query("INSERT INTO `A_CLIENTS_CONTACTS` (`id`,`income_id`) VALUES ('$contact_id','$income_id');");
            }
            $db->query("UPDATE `A_CLIENTS_CONTACTS` SET `name`='$contact_name', `post`='$contact_post' WHERE `id`='$contact_id' AND `income_id`='$income_id';");
            for ($i=1;$i<=$contact_con_kol;$i++) {
                $c_id=$con_id[$i];
                $c_sotc_cont=$sotc_cont[$i];
                $c_contact_value=$contact_value[$i];
                if ($c_id>0 && $c_contact_value=="") {
                    $db->query("DELETE FROM `A_CLIENTS_CONTACTS_CON` WHERE `id`='$c_id';");
                }
                if ($c_id>0 && $c_sotc_cont>0 && $c_contact_value!="") {
                    $db->query("UPDATE `A_CLIENTS_CONTACTS_CON` SET `sotc_cont`='$c_sotc_cont', `contact_value`='$c_contact_value' WHERE `id`='$c_id';");
                }
                if ($c_id==0 && $c_sotc_cont>0 && $c_contact_value!="") {
                    $db->query("INSERT INTO `A_CLIENTS_CONTACTS_CON` (`contact_id`,`sotc_cont`,`contact_value`) VALUES ('$contact_id','$c_sotc_cont','$c_contact_value');");
                }
            }
            $answer=1; $err="";
        }
        return array($answer, $err);
    }

    function dropIncomeContact($income_id, $contact_id) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "������� ���������� �����!";
        $income_id = $slave->qq($income_id); $contact_id = $slave->qq($contact_id);
        if ($income_id > 0 && $contact_id > 0) {
            $db->query("DELETE FROM `A_CLIENTS_CONTACTS` WHERE `id`='$contact_id' AND `income_id`='$income_id';");
            $db->query("DELETE FROM `A_CLIENTS_CONTACTS_CON` WHERE `contact_id`='$contact_id';");
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

    function loadIncomeCommets($income_id) { $db = DbSingleton::getDb();
        $list = "";
        $form = ""; $form_htm = RD . "/tpl/income_comment_block.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $r = $db->query("SELECT cc.*, u.name 
        FROM `J_INCOME_COMMENTS` cc 
            LEFT OUTER JOIN `media_users` u on u.id=cc.USER_ID 
        WHERE cc.income_id='$income_id' ORDER BY cc.id DESC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r,$i - 1,"id");
            $user_id = $db->result($r,$i - 1,"user_id");
            $user_name = $db->result($r,$i - 1,"name");
            $data = $db->result($r,$i - 1,"data");
            $comment = $db->result($r,$i - 1,"comment");
            $block = $form;
            $block = str_replace("{income_id}",$income_id,$block);
            $block = str_replace("{id}",$id,$block);
            $block = str_replace("{user_id}",$user_id,$block);
            $block = str_replace("{user_name}",$user_name,$block);
            $block = str_replace("{data}",$data,$block);
            $block = str_replace("{comment}",$comment,$block);
            $list .= $block;
        }
        if ($n == 0) {
            $list = "<h3 class='text-center'>�������� �������</h3>";
        }
        return $list;
    }

    function saveIncomeComment($income_id, $comment) { $db = DbSingleton::getDb();
        $slave = new slave;
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $answer = 0; $err = "������� ���������� �����!";
        $income_id = $slave->qq($income_id); $comment=$slave->qq($comment);
        if ($income_id > 0 && $comment != "") {
            $db->query("INSERT INTO `J_INCOME_COMMENTS` (`income_id`,`user_id`,`comment`) VALUES ('$income_id','$user_id','$comment');");
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

    function dropIncomeComment($income_id, $comment_id) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer=0; $err="������� ��������� ������!";
        $income_id = $slave->qq($income_id); $comment_id = $slave->qq($comment_id);
        if ($income_id>0 && $comment_id>0) {
            $r=$db->query("SELECT * FROM `J_INCOME_COMMENTS` WHERE `income_id`='$income_id' AND `id`='$comment_id' LIMIT 1;");
            $n=$db->num_rows($r);
            if ($n==1) {
                $db->query("DELETE FROM `J_INCOME_COMMENTS` WHERE `income_id`='$income_id' AND `id`='$comment_id';");
                $answer=1; $err="";
            }
        }
        return array($answer, $err);
    }

    function labelArtUnknownCount($income_id) { $db = DbSingleton::getDb();
        $label = "";
        $r = $db->query("SELECT COUNT(`id`) as kol FROM `J_INCOME_STR_UNKNOWN` WHERE `income_id`='$income_id';");
        $kol = 0 + $db->result($r, 0, "kol");
        if ($kol > 0) {
            $label = "<span class='label label-tab label-info'>$kol</span>";
        }
        return array($kol, $label);
    }

    function labelCommentsCount($income_id) { $db = DbSingleton::getDb();
        $label = "";
        $r = $db->query("SELECT COUNT(`id`) as kol FROM `J_INCOME_COMMENTS` WHERE `income_id`='$income_id';");
        $kol = 0 + $db->result($r, 0, "kol");
        if ($kol > 0) {
            $label = "<span class='label label-tab label-info'>$kol</span>";
        }
        return array($kol, $label);
    }

    function loadIncomeDetailsFile($income_id, $file_type) { $db = DbSingleton::getDb();
        $list = "";
        $form = ""; $form_htm = RD . "/tpl/income_details_file.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $r = $db->query("SELECT cd.*, u.name as user_name 
        FROM `A_CLIENTS_DTLS` `cd` 
            LEFT OUTER JOIN `media_users` u on u.id=`cd`.USER_ID 
        WHERE cd.CLIENT_ID='$income_id' AND cd.FILE_TYPE='$file_type' AND cd.STATUS='1' ORDER BY cd.FILE_NAME ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $file_id=$db->result($r,$i - 1,"ID");
            $file_name=$db->result($r,$i - 1,"FILE_NAME");
            $name=$db->result($r,$i - 1,"NAME");
            $data=$db->result($r,$i - 1,"DATA");
            $user_name=$db->result($r,$i - 1,"user_name");
            $link="http://portal.myparts.pro/cdn/clfiles/$income_id/$file_name";
            $file_view="<div class=\"icon\"><i class=\"fa fa-file\"></i></div>";
            $exten=pathinfo($file_name, PATHINFO_EXTENSION);
            if ($exten=="jpg" || $exten=="jpeg" || $exten=="png" || $exten=="gif" || $exten=="bmp" || $exten=="svg"){
                $file_view="<div class=\"image\"><img alt=\"image\" class=\"img-responsive\" src=\"$link\"></div>";
            }
            $block=$form;
            $block = str_replace("{file_id}",$file_id,$block);
            $block = str_replace("{file_type}",$file_type,$block);
            $block = str_replace("{file_name}",$name,$block);
            $block = str_replace("{user_name}",$user_name,$block);
            $block = str_replace("{data}",$data,$block);
            $block = str_replace("{income_id}",$income_id,$block);
            $block = str_replace("{link}",$link,$block);
            $block = str_replace("{file_view}",$file_view,$block);
            $list.=$block;
        }
        if ($n == 0) {
            $list = "<h3 class='text-center'>����� �������</h3>";
        }
        return $list;
    }

//    function incomeDetailsDropFile($income_id,$file_type,$file_id){$db = DbSingleton::getDb();
//        $slave = new slave;$answer=0;$err="������� ��������� �����!";
//        $income_id = $slave->qq($income_id);$file_id=$slave->qq($file_id);
//        if ($income_id>0 && $file_id>0 && $file_type!=""){
//            $r=$db->query("SELECT `FILE_NAME` FROM `A_CLIENTS_DTLS` WHERE `CLIENT_ID`='$income_id' AND `FILE_TYPE`='$file_type' AND `ID`='$file_id' LIMIT 1;");$n=$db->num_rows($r);
//            if ($n==1){
//                unlink(RD.'/cdn/clfiles/$income_id/$file_name');
//                $db->query("DELETE FROM `A_CLIENTS_DTLS` WHERE `CLIENT_ID`='$income_id' AND `FILE_TYPE`='$file_type' AND `ID`='$file_id';");
//                $answer=1;$err="";
//            }
//        }
//        return array($answer,$err);
//    }

    function loadIncomeCDN($income_id) { $db = DbSingleton::getDb();
        $list = "";
        $form = ""; $form_htm = RD . "/tpl/income_cdn_block.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $r = $db->query("SELECT cc.*, u.name as user_name 
        FROM `J_INCOME_CDN` cc 
            LEFT OUTER JOIN `media_users` u on u.id=cc.USER_ID 
        WHERE cc.income_id='$income_id' AND cc.status='1' ORDER BY cc.file_name ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $file_id=$db->result($r,$i - 1,"id");
            $file_name=$db->result($r,$i - 1,"file_name");
            $name=$db->result($r,$i - 1,"name");
            $data=$db->result($r,$i - 1,"data");
            $user_name=$db->result($r,$i - 1,"user_name");
            $link="http://portal.myparts.pro/cdn/income_files/$income_id/$file_name";
            $file_view="<div class=\"icon\"><i class=\"fa fa-file\"></i></div>";
            $exten=pathinfo($file_name, PATHINFO_EXTENSION);
            if ($exten=="jpg" || $exten=="jpeg" || $exten=="png" || $exten=="gif" || $exten=="bmp" || $exten=="svg"){
                $file_view="<div class=\"image\"><img alt=\"image\" class=\"img-responsive\" src=\"$link\"></div>";
            }
            $block=$form;
            $block = str_replace("{file_id}",$file_id,$block);
            $block = str_replace("{file_name}",$name,$block);
            $block = str_replace("{user_name}",$user_name,$block);
            $block = str_replace("{data}",$data,$block);
            $block = str_replace("{income_id}",$income_id,$block);
            $block = str_replace("{link}",$link,$block);
            $block = str_replace("{file_view}",$file_view,$block);
            $list.=$block;
        }
        if ($n == 0) {
            $list = "<h3 class=\"text-center\">����� �������</h3>";
        }
        return $list;
    }

    function incomeCDNDropFile($income_id, $file_id) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "������� ��������� �����!";
        $income_id = $slave->qq($income_id); $file_id = $slave->qq($file_id);
        if ($income_id > 0 && $file_id > 0) {
            $r = $db->query("SELECT `file_name` FROM `J_INCOME_CDN` WHERE `income_id`='$income_id' AND `id`='$file_id' LIMIT 1;");
            $n = $db->num_rows($r);
            if ($n == 1) {
                //$file_name=$db->result($r,0,"file_name");
                unlink(RD.'/cdn/income_files/$income_id/$file_name');
                $db->query("DELETE FROM `J_INCOME_CDN` WHERE `income_id`='$income_id' AND `id`='$file_id';");
                $answer = 1; $err = "";
            }
        }
        return array($answer, $err);
    }

    function loadArticleFoto($income_id) { $db = DbSingleton::getDb();
        $list="";
        $form="";$form_htm=RD."/tpl/income_foto_block.htm";
        if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("SELECT t2af.*, u.name as user_name 
        FROM `T2_PHOTOS` t2af
            LEFT OUTER JOIN `media_users` u on u.id=t2af.USER_ID 
        WHERE t2af.ART_ID='$income_id' ORDER BY t2af.PHOTO_NAME ASC;");
        $n=$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $file_id=$db->result($r,$i - 1,"ID");
            $file_name=$db->result($r,$i - 1,"PHOTO_NAME");
            $data=$db->result($r,$i - 1,"DATA");
            $user_name=$db->result($r,$i - 1,"user_name");
            $main=$db->result($r,$i - 1,"MAIN");
            $main_v="<a class=\"btn btn-xs btn-white\" onClick=\"setArticlesFotoMain('$income_id','$file_id')\"><i class=\"fa fa-check\"></i> ������� ����</a>";
            if ($main==1){$main_v=" <span class=\"btn btn-xs label-primary\"><i class=\"fa fa-check\"></i> ������� ����</span>";}
            $link="http://portal.myparts.pro/cdn/artfoto/$file_name";
            $block=$form;
            $block = str_replace("{file_id}",$file_id,$block);
            $block = str_replace("{foto_name}",$file_name,$block);
            $block = str_replace("{file_name}",$file_name,$block);
            $block = str_replace("{user_name}",$user_name,$block);
            $block = str_replace("{data}",$data,$block);
            $block = str_replace("{income_id}",$income_id,$block);
            $block = str_replace("{link}",$link,$block);
            $block = str_replace("{main}",$main_v,$block);
            $list.=$block;
        }
        if ($n==0) {
            $list="<h3 class=\"text-center\">���� �������</h3>";
        }
        return $list;
    }

    function setArticlesFotoMain($income_id, $file_id) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer=0; $err="������� ������� ������!";
        $income_id = $slave->qq($income_id); $file_id=$slave->qq($file_id);
        if ($income_id>0 && $file_id>0) {
            $db->query("UPDATE `T2_PHOTOS` SET `MAIN`='0' WHERE `ART_ID`='$income_id' AND `MAIN`='1';");
            $db->query("UPDATE `T2_PHOTOS` SET `MAIN`='1' WHERE `ART_ID`='$income_id' AND `ID`='$file_id';");
            $answer=1; $err="";
        }
        return array($answer, $err);
    }

//    function articlesFotoDropFile($income_id,$file_id){$db = DbSingleton::getDb();
//        $slave = new slave;$answer=0;$err="������� ��������� �����!";
//        $income_id = $slave->qq($income_id);$file_id=$slave->qq($file_id);
//        if ($income_id>0 && $file_id>0){
//            $r=$db->query("SELECT `PHOTO_NAME` FROM `T2_PHOTOS` WHERE `ART_ID`='$income_id' AND `ID`='$file_id' LIMIT 1;");$n=$db->num_rows($r);
//            if ($n==1){
//                unlink(RD.'/cdn/artfoto/$PHOTO_NAME');
//                $db->query("DELETE FROM `T2_PHOTOS` WHERE `ART_ID`='$income_id' AND `ID`='$file_id';");
//                $answer=1;$err="";
//            }
//        }
//        return array($answer,$err);
//    }

//    function getIncomeNameById($sel_id, $field="name"){$db = DbSingleton::getDb();$name="";
//        $r=$db->query("SELECT `$field` FROM `A_CLIENTS` WHERE `id`='$sel_id' LIMIT 1;");$n=$db->num_rows($r);
//        if ($n==1){$name=$db->result($r,0,"$field");}
//        return $name;
//    }

//    function loadStateSelectList($country_id,$sel_id){$slave = new slave;
//        $list=$slave->showSelectSubList("T2_STATE","COUNTRY_ID","$country_id","STATE_ID","STATE_NAME",$sel_id);
//        return $list;
//    }
//
//    function loadRegionSelectList($state_id,$sel_id){$slave = new slave;
//        return $slave->showSelectSubList("T2_REGION","STATE_ID","$state_id","REGION_ID","REGION_NAME",$sel_id);
//    }
//
//    function loadCitySelectList($region_id,$sel_id){$slave = new slave;
//        return "<option value='NEW'>�������� ��������� �����</option>".$slave->showSelectSubList("T2_CITY","REGION_ID","$region_id","CITY_ID","CITY_NAME",$sel_id);
//    }

//    function showCategoryCheckList($income_id){$db = DbSingleton::getDb();$list="";
//        $r=$db->query("SELECT * FROM `A_CATEGORY` WHERE `parrent_id`=0 ORDER BY `id` ASC;");$n=$db->num_rows($r);
//        for ($i = 1; $i <= $n; $i++) {
//            $id=$db->result($r,$i- 1 ,"id");
//            $name=$db->result($r,$i- 1 ,"name");
//            $sel=$this->checkIncomeCategorySelect($income_id,$id);
//            $ch="";if ($sel==1){$ch=" checked=''";}
//            $list.="<label><input type='checkbox' class='i-checks' id='c_category_$i' value='$id' $ch> - $name;</label> ";
//        }
//        $list.="<input type='hidden' id='c_category_kol' value='$n'>";
//        return $list;
//    }

//    function checkIncomeCategorySelect($income_id,$category_id){$db = DbSingleton::getDb();$ch=0;
//        $r=$db->query("SELECT `category_id` FROM `A_CLIENTS_CATEGORY` WHERE `income_id`='$income_id' AND `category_id`='$category_id' LIMIT 1;");$n=$db->num_rows($r);
//        if ($n==1){$ch=1;}
//        return $ch;
//    }

//    function showPriceLvlListSelect($sel_id){$list="";
//        for ($i=1;$i<=200;$i++){
//            $sel="";if ($sel_id==$i){$sel="selected='selected'";}
//            $list.="<option value='$i' $sel>$i</option>";
//        }
//        return $list;
//    }

    function getCashAbr($sel_id) { $db = DbSingleton::getDb();
        $name = "���";
        $r = $db->query("SELECT `abr` FROM `CASH` WHERE `id`='$sel_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $name = $db->result($r,0,"abr");
        }
        return $name;
    }

    function showCashListSelect($sel_id, $ns) { $db = DbSingleton::getDb();
        $list="";
        if ($ns==""){$ns=1;}
        $r=$db->query("SELECT * FROM `CASH` ORDER BY `name` ASC;");
        $n=$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id=$db->result($r,$i - 1,"id");
            $name=$db->result($r,$i - 1,"abr");
            if ($ns==2){$name=$db->result($r,$i - 1,"name");}
            $sel="";
            if ($sel_id==$id){$sel="selected='selected'";}
            $list.="<option value='$id' $sel>$name</option>";
        }
        return $list;
    }

    function showGoodGroupTree($sel_id) { $db = DbSingleton::getDb();
        $tree = "";
        $form = ""; $form_htm = RD . "/tpl/income_goods_group_form.htm";
        if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r = $db->query("SELECT * FROM `GOODS_GROUP` WHERE `PARRENT_ID`='0' ORDER BY `NAME` ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r,$i - 1,"ID");
            $name = $db->result($r,$i - 1,"NAME");
            $sel = ($sel_id==$id) ? " data-jstree='{\"selected\":true}'" : "";
            $tree .= "<li id='$id' $sel>$name".$this->showGoodGroupSubLevel($id,$sel_id)."</li>";
        }
        $form = str_replace("{tree}",$tree,$form);
        $form = str_replace("{goods_group_id}",$sel_id,$form);
        return $form;
    }

    function showGoodGroupSubLevel($parrent_id, $sel_id) { $db = DbSingleton::getDb();
        $tree = "";
        $r = $db->query("SELECT * FROM `GOODS_GROUP` WHERE `PARRENT_ID`='$parrent_id' ORDER BY `NAME` ASC;");
        $n = $db->num_rows($r);
        if ($n > 0) {
            $tree .= "<ul>";
            for ($i = 1; $i <= $n; $i++) {
                $id = $db->result($r,$i - 1,"ID");
                $name = $db->result($r,$i - 1,"NAME");
                $sel = ($sel_id==$id) ? " data-jstree='{\"selected\":true}'" : "";
                $tree .= "<li id='$id' $sel>$name".$this->showGoodGroupSubLevel($id,$sel_id)."</li>";
            }
            $tree .= "</ul>";
        }
        return $tree;
    }

    function showImportIncomeStrCSVform($income_id) {
        $form="";$form_htm=RD."/tpl/income_import_str_form.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        list(,,$pre_table)=$this->showCsvPreview($income_id);
        $form = str_replace("{records_list}","<tr><td colspan=10 align='center'>������ �� �����������</td></tr>",$form);
        $form = str_replace("{import_file_name}","������ ����",$form);
        $form = str_replace("{income_id}",$income_id,$form);
        $form = str_replace("{csv_str_file}",$pre_table,$form);
        return array($form, "������ �������� �������");
    }

    function showCsvPreview($income_id) { $db = DbSingleton::getDb();
        $csv_exist=0;$kol_cols=0;$fn=0;
        $csv_file_name="������ ����";$pre_table="<h3 align='center'>������ �������</h3>";
        $r=$db->query("SELECT * FROM `J_INCOME_CSV` WHERE `income_id`='$income_id' LIMIT 1;");
        $n=$db->num_rows($r);
        if ($n==1) {
            $file_name=$db->result($r,0,"file_name");
            $file_path=RD."/cdn/income_files/csv/$income_id/$file_name";
            if (file_exists($file_path)){
                $form="";$form_htm=RD."/tpl/csv_str_file.htm";
                if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
                $cols_list=""; $records_list="";
                $handle = @fopen($file_path, "r");
                if ($handle) {
                    set_time_limit(0); $max_cols=0;
                    while (($buffer = fgets($handle, 4096)) !== false) {
                        $fn+=1;
                        $buf=explode(";",$buffer);
                        if ($buffer!=""){
                            if ($fn==1){$kol_cols=count($buf);}
                            $buf=str_replace("'","\'",$buf);
                            $buf=str_replace('"','\"',$buf);
                            $row="";$ex_cols=0;
                            if ($max_cols<$kol_cols) {
                                $ex_cols=1;
                                $cols_list="";
                            }
                            for ($i=1;$i<=$kol_cols;$i++) {
                                if ($i==1) {
                                    $row="<td>$fn</td>";
                                }
                                $row.="<td>".trim($buf[$i- 1 ])."</td>";
                                if ($ex_cols==1) {
                                    $cols_list.="<th><select id=\"clm-$i\" size='1'><option value='0'>-</option><option value='1'>������</option><option value='2'>�����</option><option value='3'>�����</option><option value='4'>������ ���</option><option value='5'>ʳ������</option><option value='6'>ֳ��</option><option value='7'>����</option></select></th>";
                                }
                            }
                            if ($row!="") {
                                $records_list.="<tr>$row</tr>";
                            }
                        }
                        if ($fn==30) {
                            break;
                        }
                    }
                    fclose($handle);
                }
                $form = str_replace("{income_id}",$income_id,$form);
                $form = str_replace("{cols_list}",$cols_list,$form);
                $form = str_replace("{records_list}",$records_list,$form);
                $form = str_replace("{kol_cols}",$kol_cols,$form);
                $csv_file_name=$file_name;$csv_exist=1;$pre_table=$form;
            }
        }
        return array($csv_exist, $csv_file_name, $pre_table);
    }

    function finishCsvImport($income_id, $start_row, $kol_cols, $cols) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer=0;$err="������� ���������� �����!";
        $fn=0;
        $income_id = $slave->qq($income_id);$start_row=$slave->qq($start_row);$kol_cols=$slave->qq($kol_cols);$cols=$slave->qq($cols);
        if ($income_id>0){
            $r=$db->query("SELECT * FROM `J_INCOME_CSV` WHERE `income_id`='$income_id' LIMIT 1;");
            $n=$db->num_rows($r);
            if ($n==1){
                $file_name=$db->result($r,0,"file_name");
                $file_path=RD."/cdn/income_files/csv/$income_id/$file_name";
                if (file_exists($file_path)){
                    $index=0;$brand=0;$country=0;$custum_code=0;$amount=0;$price=0;$weight=0;
                    for ($i=1;$i<=$kol_cols;$i++){
                        if ($cols[$i]==1){$index=$i;}
                        if ($cols[$i]==2){$brand=$i;}
                        if ($cols[$i]==3){$country=$i;}
                        if ($cols[$i]==4){$custum_code=$i;}
                        if ($cols[$i]==5){$amount=$i;}
                        if ($cols[$i]==6){$price=$i;}
                        if ($cols[$i]==7){$weight=$i;}
                    }
                    $handle = @fopen($file_path, "r");
                    if ($handle) { //$db->query("delete from catalogue_price where provider='$provider';");
                        set_time_limit(0);//$max_cols=0;
                        while (($buffer = fgets($handle, 4096)) !== false) {
                            $fn+=1;
                            $buf=explode(";",$buffer);
                            if ($buffer!=""){
                                if ($fn>=$start_row){
                                    $buf=str_replace("'","\'",$buf);$buf=str_replace('"','\"',$buf);
                                    $ind=trim($buf[$index-1]);
                                    $brnd=trim($buf[$brand-1]);
                                    $cntr=trim($buf[$country-1]);
                                    $cstm=trim($buf[$custum_code-1]);$cstm=str_replace(" ","",$cstm);
                                    $amnt=trim($buf[$amount-1]);$amnt=str_replace(",",".",$amnt);$amnt=str_replace(" ","",$amnt);
                                    $prc=trim($buf[$price-1]);$prc=str_replace(",",".",$prc);$prc=str_replace(" ","",$prc);
                                    $wght=trim($buf[$weight-1]);$wght=str_replace(",",".",$wght);$wght=str_replace(" ","",$wght);
                                    $costum_id=$this->getCostumsId($cstm);
                                    $country_id=$this->getCountryId($cntr);
                                    $brand_id=$this->getBrandId($brnd);
                                    $art_id=$this->getArtId($ind,$brand_id);
                                    list($rate,,$type_declaration_id)=$this->getRateTypeDeclarationdocumentPos($costum_id,$country_id);
                                    $unknown_id=0;
                                    if ($art_id==0 || $art_id=="" || $brand_id==0 || $brand_id=="" || $country_id==0 || $country_id=="" || $costum_id=="" || $costum_id==0){
                                        $r2=$db->query("SELECT max(`id`) as mid FROM `J_INCOME_STR_UNKNOWN`;");
                                        $unknown_id=0+$db->result($r2,0,"mid")+1;
                                        $db->query("INSERT INTO `J_INCOME_STR_UNKNOWN` (`id`,`income_id`,`art_id`,`article_nr_displ`,`brand_id`,`country_id`,`costums_id`,`amount`,`price_buh_cashin`,`weight_netto`) 
                                        VALUES ('$unknown_id','$income_id','$art_id','$ind','$brand_id','$country_id','$costum_id','$amnt','$prc','$wght');");
                                    }
                                    $db->query("INSERT INTO `J_INCOME_STR` (`income_id`,`unknown_id`,`art_id`,`article_nr_displ`,`brand_id`,`country_id`,`costums_id`,`amount`,`price_buh_cashin`,`weight_netto`,`rate`,`type_declaration_id`) 
                                    VALUES ('$income_id','$unknown_id','$art_id','$ind','$brand_id','$country_id','$costum_id','$amnt','$prc','$wght','$rate','$type_declaration_id');");
                                }
                            }
                        }
                        fclose($handle);
                        if (file_exists(RD."/cdn/income_files/csv/$income_id/$file_name")) {
                            unlink(RD."/cdn/income_files/csv/$income_id/$file_name");
                        }
                        $db->query("DELETE FROM `J_INCOME_CSV` WHERE `income_id`='$income_id';");
                        $answer=1;$err="";
                    }
                }
            }
        }
        return array($answer, $err);
    }

    function getArtId($code, $brand_id) { $db = DbSingleton::getTokoDb();
        $slave = new slave; $cat = new catalogue;
        $id=0; $code=$slave->qq($code); $code=$cat->clearArticle($code);
        $r=$db->query("SELECT `ART_ID` FROM `T2_ARTICLES` WHERE `ARTICLE_NR_SEARCH`='$code' AND `BRAND_ID`='$brand_id' LIMIT 1;");
        $n=$db->num_rows($r);
        if ($n==1) {
            $id=$db->result($r,0,"ART_ID");
        }
        return $id;
    }

    function getCostumsId($code) { $db = DbSingleton::getTokoDb();
        $slave = new slave;
        $id=0; $code=$slave->qq($code);
        $r=$db->query("SELECT `COSTUMS_ID` FROM `T2_COSTUMS` WHERE `COSTUMS_CODE`='$code' LIMIT 1;");
        $n=$db->num_rows($r);
        if ($n==1) {
            $id=$db->result($r,0,"COSTUMS_ID");
        }
        return $id;
    }

    function getCountryId($code) { $db = DbSingleton::getTokoDb();
        $slave = new slave;
        $id=0; $code=$slave->qq($code);
        $r=$db->query("SELECT `COUNTRY_ID` FROM `T2_COUNTRIES` WHERE `COUNTRY_NAME`='$code' OR (`ALFA2`='$code' AND `ALFA2`!='') OR (`ALFA3`='$code' AND `ALFA3`!='') LIMIT 1;");
        $n=$db->num_rows($r);
        if ($n==1) {
            $id=$db->result($r,0,"COUNTRY_ID");
        }
        return $id;
    }

    function getBrandId($code) { $db = DbSingleton::getTokoDb();
        $slave = new slave;
        $id=0; $code=$slave->qq($code);
        $r=$db->query("SELECT `BRAND_ID` FROM `T2_BRANDS` WHERE `BRAND_NAME`='$code' LIMIT 1;");
        $n=$db->num_rows($r);
        if ($n==1) {
            $id=$db->result($r,0,"BRAND_ID");
        }
        return $id;
    }

    function saveIncomeConditions($income_id, $cash_id, $country_cash_id, $price_lvl, $payment_delay, $credit_limit, $credit_cash_id, $credit_return) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "������� ���������� �����!";
        $income_id = $slave->qq($income_id); $cash_id=$slave->qq($cash_id); $country_cash_id=$slave->qq($country_cash_id); $price_lvl=$slave->qq($price_lvl); $payment_delay=$slave->qq($payment_delay);
        $credit_limit=$slave->qq($slave->point_valid($credit_limit)); $credit_cash_id=$slave->qq($credit_cash_id); $credit_return=$slave->qq($credit_return);
        if ($income_id > 0) {
            $r = $db->query("SELECT * FROM `A_CLIENTS_CONDITIONS` WHERE `income_id`='$income_id' LIMIT 1;");
            $n = $db->num_rows($r);
            if ($n == 0) {
                $db->query("INSERT INTO `A_CLIENTS_CONDITIONS` (`income_id`,`cash_id`,`country_cash_id`,`price_lvl`,`payment_delay`,`credit_limit`,`credit_cash_id`,`credit_return`) 
                VALUES ('$income_id','$cash_id','$country_cash_id','$price_lvl','$payment_delay','$credit_limit','$credit_cash_id','$credit_return');");
            }
            if ($n == 1) {
                $db->query("UPDATE `A_CLIENTS_CONDITIONS` SET `cash_id`='$cash_id', `country_cash_id`='$country_cash_id', `price_lvl`='$price_lvl', `payment_delay`='$payment_delay', `credit_limit`='$credit_limit', `credit_cash_id`='$credit_cash_id', `credit_return`='$credit_return' WHERE `income_id`='$income_id';");
            }
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

    function loadIncomeDetails($income_id) { $db = DbSingleton::getDb();
        $form=""; $form_htm=RD."/tpl/income_details.htm";
        if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("SELECT * FROM `A_CLIENT_DETAILS` WHERE `income_id`='$income_id' LIMIT 1;");
        $address_jur=$db->result($r,0,"address_jur");
        $address_fakt=$db->result($r,0,"address_fakt");
        $edrpou=$db->result($r,0,"edrpou");
        $svidotctvo=$db->result($r,0,"svidotctvo");
        $vytjag=$db->result($r,0,"vytjag");
        $vat=$db->result($r,0,"vat");
        $bank=$db->result($r,0,"bank");
        $mfo=$db->result($r,0,"mfo");
        $account=$db->result($r,0,"account");
        $not_resident=$db->result($r,0,"not_resident");
        $not_resident_ch = "";
        $nr_details_disabled = " disabled='disabled'";
        if ($not_resident == 1) {
            $not_resident_ch = " checked='checked'";
            $nr_details_disabled = "";
        }
        $nr_details = $db->result($r, 0, "nr_details");
        $form = str_replace("{income_id}",$income_id,$form);
        $form = str_replace("{address_jur}",$address_jur,$form);
        $form = str_replace("{address_fakt}",$address_fakt,$form);
        $form = str_replace("{edrpou}",$edrpou,$form);
        $form = str_replace("{svidotctvo}",$svidotctvo,$form);
        $form = str_replace("{vytjag}",$vytjag,$form);
        $form = str_replace("{vat}",$vat,$form);
        $form = str_replace("{bank}",$bank,$form);
        $form = str_replace("{mfo}",$mfo,$form);
        $form = str_replace("{account}",$account,$form);
        $form = str_replace("{not_resident_checked}",$not_resident_ch,$form);
        $form = str_replace("{nr_details}",$nr_details,$form);
        $form = str_replace("{nr_details_disabled}",$nr_details_disabled,$form);
        return $form;
    }

    function saveIncomeDetails($income_id,$address_jur,$address_fakt,$edrpou,$svidotctvo,$vytjag,$vat,$mfo,$bank,$account,$not_resident,$nr_details) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "������� ���������� �����!";
        $income_id = $slave->qq($income_id);$address_jur=$slave->qq($address_jur);$address_fakt=$slave->qq($address_fakt);$edrpou=$slave->qq($edrpou);$svidotctvo=$slave->qq($svidotctvo);
        $vytjag=$slave->qq($vytjag);$vat=$slave->qq($vat);$mfo=$slave->qq($mfo);$bank=$slave->qq($bank);$account=$slave->qq($account);$not_resident=$slave->qq($not_resident);$nr_details=$slave->qq($nr_details);
        if ($income_id > 0) {
            $r = $db->query("SELECT * FROM `A_CLIENT_DETAILS` WHERE `income_id`='$income_id' LIMIT 1;");
            $n = $db->num_rows($r);
            if ($n == 0) {
                $db->query("INSERT INTO `A_CLIENT_DETAILS` (`income_id`,`address_jur`,`address_fakt`,`edrpou`,`svidotctvo`,`vytjag`,`vat`,`mfo`,`bank`,`account`,`not_resident`,`nr_details`) 
                VALUES ('$income_id','$address_jur','$address_fakt','$edrpou','$svidotctvo','$vytjag','$vat','$mfo','$bank','$account','$not_resident','$nr_details');");
            }
            if ($n == 1) {
                $db->query("UPDATE `A_CLIENT_DETAILS` SET `address_jur`='$address_jur', `address_fakt`='$address_fakt', `edrpou`='$edrpou', `svidotctvo`='$svidotctvo', `vytjag`='$vytjag', `vat`='$vat', `mfo`='$mfo', `bank`='$bank', `account`='$account', `not_resident`='$not_resident', `nr_details`='$nr_details' WHERE `income_id`='$income_id';");
            }
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

//    function saveincomeLogistic($income_id,$index_pack,$height,$length,$width,$volume,$weight_netto,$weight_brutto,$necessary_amount_car,$units_id,$multiplicity_package,$shoulder_delivery,$general_quant,$work_pair){$db = DbSingleton::getDb();
//        $slave = new slave;$answer=0;$err="������� ���������� �����!";
//        $income_id = $slave->qq($income_id);$index_pack=$slave->qq($index_pack);$height=$slave->qq($slave->point_valid($height));$length=$slave->qq($slave->point_valid($length));
//        $width=$slave->qq($slave->point_valid($width));$volume=$slave->qq($slave->point_valid($volume));$weight_netto=$slave->qq($slave->point_valid($weight_netto));
//        $weight_brutto=$slave->qq($slave->point_valid($weight_brutto));$necessary_amount_car=$slave->qq($necessary_amount_car);$units_id=$slave->qq($units_id);
//        $multiplicity_package=$slave->qq($multiplicity_package);$shoulder_delivery=$slave->qq($shoulder_delivery);$general_quant=$slave->qq($general_quant);
//        if ($income_id>0){
//            $r=$db->query("SELECT * FROM `T2_PACKAGING` WHERE `ART_ID`='$income_id' LIMIT 1;");$n=$db->num_rows($r);
//            if ($n==0){
//                $db->query("INSERT INTO `T2_PACKAGING` (`ART_ID`,`INDEX_PACK`,`HEIGHT`,`LENGTH`,`WIDTH`,`VOLUME`,`WEIGHT_NETTO`,`WEIGHT_BRUTTO`,`NECESSARY_AMOUNT_CAR`,`UNITS_ID`,`MULTIPLICITY_PACKAGE`,`SHOULDER_DELIVERY`,`GENERAL_QUANT`)
//                VALUES ('$income_id','$index_pack','$height','$length','$width','$volume','$weight_netto','$weight_brutto','$necessary_amount_car','$units_id','$multiplicity_package','$shoulder_delivery','$general_quant');");
//            }
//            if ($n==1){
//                $db->query("UPDATE `T2_PACKAGING` SET `INDEX_PACK`='$index_pack', `HEIGHT`='$height', `LENGTH`='$length', `WIDTH`='$width', `VOLUME`='$volume', `WEIGHT_NETTO`='$weight_netto', `WEIGHT_BRUTTO`='$weight_brutto',
//                `NECESSARY_AMOUNT_CAR`='$necessary_amount_car', `UNITS_ID`='$units_id', `MULTIPLICITY_PACKAGE`='$multiplicity_package', `SHOULDER_DELIVERY`='$shoulder_delivery', `GENERAL_QUANT`='$general_quant' WHERE `ART_ID`='$income_id';");
//            }
//            if ($work_pair!=""){
//                $db->query("DELETE FROM `T2_WORK_PAIR` WHERE `ART_ID`='$income_id';");
//                foreach ($work_pair as $wp){
//                    if ($wp!=""){
//                        $db->query("INSERT INTO `T2_WORK_PAIR` (`ART_ID`,`PAIR_INDEX`) VALUES ('$income_id','$wp');");
//                    }
//                }
//            }
//            $answer=1;$err="";
//        }
//        return array($answer,$err);
//    }

    function showCountryManual($sel_id) { $db = DbSingleton::getDb();
        $manual = new manual;
        $list = "";
        $form = ""; $form_htm = RD . "/tpl/income_country_list.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $r = $db->query("SELECT * FROM `T2_COUNTRIES` ORDER BY `COUNTRY_NAME` ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r,$i - 1,"COUNTRY_ID");
            $name = $db->result($r,$i - 1,"COUNTRY_NAME");
            $alfa2 = $db->result($r,$i - 1,"ALFA2");
            $alfa3 = $db->result($r,$i - 1,"ALFA3");
            $duty = $manual->getManualMCaption("DUTY",$db->result($r,$i - 1,"DUTY"));
            $risk = $manual->getManualMCaption("RISK",$db->result($r,$i - 1,"RISK"));
            $sel = ($sel_id == $id) ? " style='background-color:#d5fdf5'" : "";
            $list .= "<tr onClick='selectCountry(\"$id\",\"$name\")' $sel>
                <td>$id</td>
                <td>$name</td>
                <td>$alfa2</td>
                <td>$alfa3</td>
                <td>$duty</td>
                <td>$risk</td>
                <td align='center'>
                    <button class='btn btn-xs btn-warning' onClick=\"showCountryForm('$id');\"><i class='fa fa-edit'></i></button>
                    <button class='btn btn-xs btn-default' onClick=\"dropCountry('$id');\"><i class='fa fa-trash'></i></button>
                </td>
            </tr>";
        }
        $form = str_replace("{list}",$list,$form);
        return $form;
    }

    function showCountryForm($id) { $db = DbSingleton::getDb();
        $manual = new manual;
        $form = ""; $form_htm = RD . "/tpl/income_country_form.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $r = $db->query("SELECT * FROM `T2_COUNTRIES` WHERE `COUNTRY_ID`='$id' LIMIT 1;");
        $name = $db->result($r,0,"COUNTRY_NAME");
        $alfa2 = $db->result($r,0,"ALFA2");
        $alfa3 = $db->result($r,0,"ALFA3");
        $duty = $db->result($r,0,"DUTY");
        $risk = $db->result($r,0,"RISK");
        $form = str_replace("{id}",$id,$form);
        $form = str_replace("{name}",$name,$form);
        $form = str_replace("{alfa2}",$alfa2,$form);
        $form = str_replace("{alfa3}",$alfa3,$form);
        $form = str_replace("{duty}",$duty,$form);
        $form = str_replace("{duty_caption}",$manual->getManualMCaption("DUTY",$duty),$form);
        $form = str_replace("{risk}",$risk,$form);
        $form = str_replace("{risk_caption}",$manual->getManualMCaption("RISK",$risk),$form);
        return array($form, "����� ����� ����������");
    }

//    function saveincomeCountryForm($id,$name,$alfa2,$alfa3,$duty,$risk){$db = DbSingleton::getDb();
//        $slave = new slave;$answer=0;$err="������� ���������� �����!";
//        $id=$slave->qq($id);$name=$slave->qq($name);$alfa2=$slave->qq($alfa2);$alfa3=$slave->qq($alfa3);$duty=$slave->qq($duty);$risk=$slave->qq($risk);
//        if ($id>0){
//            $r=$db->query("SELECT * FROM `T2_COUNTRIES` WHERE `COUNTRY_ID`='$id' LIMIT 1;");$n=$db->num_rows($r);
//            if ($n==0){
//                $db->query("INSERT INTO `T2_COUNTRIES` (`COUNTRY_ID`,`COUNTRY_NAME`,`ALFA2`,`ALFA3`,`DUTY`,`RISK`) VALUES ('$id','$name','$alfa2','$alfa3','$duty','$risk');");
//            }
//            if ($n==1){
//                $db->query("UPDATE `T2_COUNTRIES` SET `COUNTRY_NAME`='$name', `ALFA2`='$alfa2', `ALFA3`='$alfa3', `DUTY`='$duty', `RISK`='$risk' WHERE `COUNTRY_ID`='$id';");
//            }
//            $answer=1;$err="";
//        }
//        if ($id=="" && $name!=""){
//            $db->query("INSERT INTO `T2_COUNTRIES` (`COUNTRY_ID`,`COUNTRY_NAME`,`ALFA2`,`ALFA3`,`DUTY`,`RISK`) VALUES ('$id','$name','$alfa2','$alfa3','$duty','$risk');"); $answer=1;$err="";
//        }
//        return array($answer,$err);
//    }

    function showCostumsManual($sel_id) { $db = DbSingleton::getDb();
        $manual = new manual;
        $list = "";
        $form = ""; $form_htm = RD . "/tpl/income_costums_list.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $r = $db->query("SELECT * FROM `T2_COSTUMS` ORDER BY `COSTUMS_NAME` ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r,$i - 1,"COSTUMS_ID");
            $name = $db->result($r,$i - 1,"COSTUMS_NAME");
            $preferential_rate = $db->result($r,$i - 1,"PREFERENTIAL_RATE");
            $full_rate = $db->result($r,$i - 1,"FULL_RATE");
            $sertification = $manual->getManualMCaption("costums_sertification",$db->result($r,$i - 1,"SERTIFICATION"));
            $gos_standart = $manual->getManualMCaption("costums_gos_standart",$db->result($r,$i - 1,"GOS_STANDART"));
            $type_declaration = $manual->getManualMCaption("costums_type_declaration",$db->result($r,$i - 1,"TYPE_DECLARATION"));
            $sel = ($sel_id == $id) ? " style='background-color:#d5fdf5'" : "";
            $list .= "<tr onClick='selectCostums(\"$id\",\"$name\")' $sel>
                <td>$id</td>
                <td>$name</td>
                <td align='right'>$preferential_rate</td>
                <td align='right'>$full_rate</td>
                <td align='center'>$type_declaration</td>
                <td align='center'>$sertification</td>
                <td align='center'>$gos_standart</td>
                <td align='center'>
                    <button class='btn btn-xs btn-warning' onClick=\"showCostumsForm('$id');\"><i class='fa fa-edit'></i></button>
                    <button class='btn btn-xs btn-default' onClick=\"dropCostums('$id');\"><i class='fa fa-trash'></i></button>
                </td>
            </tr>";
        }
        $form = str_replace("{list}",$list,$form);
        return $form;
    }

    function showCostumsForm($id) { $db = DbSingleton::getDb();
        $manual = new manual;
        $form = ""; $form_htm = RD . "/tpl/income_costums_form.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $r = $db->query("SELECT * FROM `T2_COSTUMS` WHERE `COSTUMS_ID`='$id' LIMIT 1;");
        $name = $db->result($r,0,"COSTUMS_NAME");
        $preferential_rate = $db->result($r,0,"PREFERENTIAL_RATE");
        $sertification = $db->result($r,0,"SERTIFICATION");
        $gos_standart = $db->result($r,0,"GOS_STANDART");
        $type_declaration = $db->result($r,0,"TYPE_DECLARATION");
        $form = str_replace("{id}",$id,$form);
        $form = str_replace("{name}",$name,$form);
        $form = str_replace("{preferential_rate}",$preferential_rate,$form);
        $form = str_replace("{sertification}",$sertification,$form);
        $form = str_replace("{sertification_caption}",$manual->getManualMCaption("costums_sertification",$sertification),$form);
        $form = str_replace("{gos_standart}",$gos_standart,$form);
        $form = str_replace("{gos_standart_caption}",$manual->getManualMCaption("costums_gos_standart",$gos_standart),$form);
        $form = str_replace("{type_declaration}",$type_declaration,$form);
        $form = str_replace("{type_declaration_caption}",$manual->getManualMCaption("costums_type_declaration",$type_declaration),$form);
        return array($form, "����� ������� ���� ������");
    }

//    function saveincomeCostumsForm($id,$name,$preferential_rate,$full_rate,$type_declaration,$sertification,$gos_standart){$db = DbSingleton::getDb();
//        $slave = new slave;$answer=0;$err="������� ���������� �����!";
//        $id=$slave->qq($id);$name=$slave->qq($name);$preferential_rate=$slave->qq($slave->point_valid($preferential_rate));$full_rate=$slave->qq($slave->point_valid($full_rate));
//        $type_declaration=$slave->qq($type_declaration);$sertification=$slave->qq($sertification);$gos_standart=$slave->qq($gos_standart);
//        if ($id>0){
//            $r=$db->query("SELECT * FROM `T2_COSTUMS` WHERE `COSTUMS_ID`='$id' LIMIT 1;");$n=$db->num_rows($r);
//            if ($n==0){
//                $db->query("INSERT INTO `T2_COSTUMS` (`COSTUMS_ID`,`COSTUMS_NAME`,`PREFERENTIAL_RATE`,`FULL_RATE`,`SERTIFICATION`,`GOS_STANDART`,`TYPE_DECLARATION`)
//                VALUES ('$id','$name','$preferential_rate','$full_rate','$sertification','$gos_standart','$type_declaration');");
//            }
//            if ($n==1){
//                $db->query("UPDATE `T2_COSTUMS` SET `COSTUMS_NAME`='$name', `PREFERENTIAL_RATE`='$preferential_rate', `FULL_RATE`='$full_rate', `SERTIFICATION`='$sertification', `GOS_STANDART`='$gos_standart', `TYPE_DECLARATION`='$type_declaration' WHERE `COSTUMS_ID`='$id';");
//            }
//            $answer=1;$err="";
//        }
//        if ($id=="" && $name!=""){
//            $db->query("INSERT INTO `T2_COSTUMS` (`COSTUMS_ID`,`COSTUMS_NAME`,`PREFERENTIAL_RATE`,`FULL_RATE`,`SERTIFICATION`,`GOS_STANDART`,`TYPE_DECLARATION`)
//            VALUES ('$id','$name','$preferential_rate','$full_rate','$sertification','$gos_standart','$type_declaration');");
//            $answer=1;$err="";
//        }
//        return array($answer,$err);
//    }

//    function loadArticleZED($income_id){$db = DbSingleton::getDb();
//        $form="";$form_htm=RD."/tpl/income_zed.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
//        $r=$db->query("SELECT t2z.*, t2c.COUNTRY_NAME, t2s.COSTUMS_NAME
//        FROM `T2_ZED` t2z
//            LEFT OUTER JOIN `T2_COUNTRIES` t2c on t2c.COUNTRY_ID=t2z.COUNTRY_ID
//            LEFT OUTER JOIN `T2_COSTUMS` t2s on t2s.COSTUMS_ID=t2z.COSTUMS_ID
//        WHERE t2z.ART_ID='$income_id' LIMIT 1;");
//        $country_id=$db->result($r,0,"COUNTRY_ID");
//        $country_name=$db->result($r,0,"COUNTRY_NAME");
//        $costums_id=$db->result($r,0,"COSTUMS_ID");
//        $costums_name=$db->result($r,0,"COSTUMS_NAME");
//        $form = str_replace("{income_id}",$income_id,$form);
//        $form = str_replace("{country_id}",$country_id,$form);
//        $form = str_replace("{country_name}",$country_name,$form);
//        $form = str_replace("{costums_id}",$costums_id,$form);
//        $form = str_replace("{costums_name}",$costums_name,$form);
//        return $form;
//    }

    function getCostumsName($costums_id) { $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT `COSTUMS_CODE` FROM `T2_COSTUMS` WHERE `COSTUMS_ID`='$costums_id' LIMIT 1;");
        return $db->result($r,0,"COSTUMS_CODE");
    }

    function checkArticleZed($art_id) { $db = DbSingleton::getTokoDb();
        $result = false;
        if ($art_id > 0) {
            $r = $db->query("SELECT * FROM `T2_ZED` WHERE `ART_ID`='$art_id' LIMIT 1;");
            $n = $db->num_rows($r);
            $costums_id = $db->result($r,0,"COSTUMS_ID");
            $costums_name = $this->getCostumsName($costums_id);
            if ($n > 0) {
                $result = $costums_name;
            }
        }
        return $result;
    }

    function saveIncomeZED($art_id, $country_id, $costums_id) { $db = DbSingleton::getTokoDb();
        $answer = 0; $err = "������� ���������� �����!";
        if ($art_id > 0) {
            $r = $db->query("SELECT * FROM `T2_ZED` WHERE `ART_ID`='$art_id' LIMIT 1;");
            $n = $db->num_rows($r);
            if ($n == 0) {
                $db->query("INSERT INTO `T2_ZED` (`ART_ID`,`COUNTRY_ID`,`COSTUMS_ID`) VALUES ('$art_id','$country_id','$costums_id');");
            }
            if ($n == 1) {
                $db->query("UPDATE `T2_ZED` SET `COUNTRY_ID`='$country_id', `COSTUMS_ID`='$costums_id' WHERE `ART_ID`='$art_id';");
            }
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

    function saveArticleZed($art_id, $costums_id) { $db = DbSingleton::getTokoDb();
        $answer = 0; $err = "������� ���������� �����!";
        if ($art_id > 0) {
            $r = $db->query("SELECT * FROM `T2_ZED` WHERE `ART_ID`='$art_id' LIMIT 1;");
            $n = $db->num_rows($r);
            if ($n == 0) {
                $db->query("INSERT INTO `T2_ZED` (`ART_ID`,`COSTUMS_ID`) VALUES ('$art_id','$costums_id');");
            }
            if ($n == 1) {
                $db->query("UPDATE `T2_ZED` SET `COSTUMS_ID`='$costums_id' WHERE `ART_ID`='$art_id';");
            }
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

    function setIncomeBarcodes($income_id) { $db = DbSingleton::getDb(); $dbt = DbSingleton::getTokoDb();
        $r = $db->query("SELECT * FROM `J_INCOME_STR` WHERE `income_id`='$income_id';");
        $n = $db->num_rows($r);
        if ($n > 0) {
            $list = "<h3>��������� �����-����:</h3>
            <table class=\"table\">
                <thead><tr>
                    <th>#</th>
                    <th>ART_ID</th>
                    <th>�����-���</th>
                </tr></thead>";
            $num = 0;
            for ($i = 1; $i <= $n; $i++) {
                $art_id = $db->result($r, $i - 1, "art_id");
                $r2 = $dbt->query("SELECT * FROM `T2_BARCODES` WHERE `ART_ID`='$art_id' LIMIT 1;");
                $n2 = $dbt->num_rows($r2);
                if ($n2 == 0) {
                    $num++;
                    $r3 = $dbt->query("SELECT MAX(`BARCODE`) as mid FROM `T2_BARCODES`;");
                    $barcode = 0 + $db->result($r3,0,"mid") + 1;
                    $dbt->query("INSERT INTO `T2_BARCODES` (`ART_ID`,`BARCODE`) VALUES ('$art_id','$barcode');");
                    $list .= "<tr>
                        <td>$i</td>
                        <td>$art_id</td> 
                        <td>$barcode</td>
                    </tr>";
                }
            }
            if ($num == 0) {
                $list = "<h3>�����-���� ��� ���������� �� ��������!</h3>";
            }
        } else {
            $list = "<h3>³������ ���� ��� ����������!</h3>";
        }
        return $list;
    }

    function setIncomePrices($income_id) { $db = DbSingleton::getDb(); $dbt = DbSingleton::getTokoDb();
        $arts = "";
        $r = $db->query("SELECT * FROM `J_INCOME_STR` WHERE `income_id`='$income_id';");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $art_id = $db->result($r, $i - 1, "art_id");
            $r2 = $dbt->query("SELECT * FROM `T2_ARTICLES_PRICE_RATING` WHERE `art_id`='$art_id' AND `in_use`=1 LIMIT 1;");
            $r3 = $dbt->query("SELECT * FROM `T2_ARTICLES_PRICE_STOCK` WHERE `ART_ID`='$art_id' LIMIT 1;");
            $oper_price = $dbt->result($r3, 0, "OPER_PRICE");
            $cash_id = $dbt->result($r2, 0, "cash_id");
            $cash_name = $this->getCashAbr($cash_id);
            if ($cash_id == "") {
                $cash_name = "-";
            }
            $arts .= "<tr><td>$art_id</td><td>$oper_price</td><td>$cash_name</td>";
            for ($j = 0; $j <= 11; $j++) {
                $col = $j + 1;
                $price = $dbt->result($r2,0,"price_$col");
                $arts.="<td>$price</td>";
            }
            $arts .= "</tr>";
        }

        $headers = "<th>ART_ID</th><th>OPER PRICE</th><th>������</th>";
        $inputs = "<th>-</th><th>-</th><th>-</th>";
        for ($i = 0; $i <= 11; $i++) {
            $headers .= "<th>����� $i</th>";
            $inputs .= "<th><input type=\"text\" class=\"form-control input-xs price_numbers nrCell\" id=\"artRatingPersent_$i\" value=\"0\" style=\"text-align: right;\"></th>";
        }

        $list = "<h2>������ �������</h2><hr>
        <div class='row'>
            <div class='col-lg-1'>
                <label for=\"minMarkup\" class=\"control-label\">̳�. �������: </label>
            </div>    
            <div class='col-lg-1'>
                <input id=\"minMarkup\" class=\"form-control price_numbers\" type=\"text\" value=\"0\" style=\"text-align: right;\">
            </div>    
            <div class='col-lg-2'>
                <button class=\"btn btn-warning\" onclick=\"showPreArticlePriceRating('$income_id');\"><i class='fa fa-check-square'></i> ���������</button>    
            </div>    
            <div class='col-lg-1'>
                <label for=\"price_cash_id\" class=\"control-label\">������ </label>
            </div>  
            <div class='col-lg-2'>
                <select class='form-control' name='price_cash_id' id='price_cash_id'>
                    <option value='0'>�� ������������</option>
                    <option value='1'>������</option>
                    <option value='2'>�����</option>
                    <option value='3'>����</option>
                </select>
            </div>
        </div>
        <hr>
        <table class=\"table table-striped table-hover table-bordered\">
            <thead>
                <tr>$headers</tr>
                <tr>$inputs</tr>
            </thead>
            <tbody id=\"table_body\">
                $arts
            </tbody>
        </table>";

        $list .= "
        <div class='row'>
            <div class='col-lg-offset-10 col-lg-2 text-right'>
                <button type=\"button\" class=\"btn btn-primary\" onclick=\"saveIncomeArticlePriceRating('$income_id')\"><i class='fa fa-save'></i> ��������</button>
                <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\" aria-hidden=\"true\">�������</button>
            </div>
        </div>";

        return $list;
    }

    function showPreArticlePriceRating($income_id, $price_cash_id, $prs) { $db = DbSingleton::getDb(); $dbt = DbSingleton::getTokoDb();
        $arts = "";
        $r = $db->query("SELECT * FROM `J_INCOME_STR` WHERE `income_id`='$income_id';");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $art_id = $db->result($r, $i - 1, "art_id");
            $r2 = $dbt->query("SELECT * FROM `T2_ARTICLES_PRICE_RATING` WHERE `art_id`='$art_id' AND `in_use`=1 LIMIT 1;");
            $n2 = $db->num_rows($r2);
            $cash_id = $dbt->result($r2,0,"cash_id");
            $cash_name = $this->getCashAbr($cash_id);
            if ($cash_id == "") {
                $cash_name = "-";
            }
            $r3 = $dbt->query("SELECT * FROM `T2_ARTICLES_PRICE_STOCK` WHERE `ART_ID`='$art_id' LIMIT 1;");
            $oper_price = $dbt->result($r3,0,"OPER_PRICE");
            if ($n2 == 0 && $price_cash_id > 0) {
                $cash_name = $this->getCashAbr($price_cash_id);
            }
            $arts .= "<tr><td>$art_id</td><td>$oper_price</td><td>$cash_name</td>";
            $pred_price = 0;
            for ($j = 0; $j <= 11; $j++) {
                $percent = $prs[$j];
                if ($j == 0) {
                    $price = $oper_price;
                    if ($n2 == 0 && $price_cash_id > 0) {
                        $price = $this->getSummCash($price, 2, $price_cash_id);
                    }
                    $pred_price = round($price + ($price * $percent / 100), 2);
                } else {
                    $price = $pred_price;
                    $pred_price = round($price + ($price * $percent / 100), 2);
                }
                $arts .= "<td>$pred_price</td>";
            }
            $arts .= "</tr>";
        }
        return $arts;
    }

    function saveIncomeArticlePriceRating($income_id, $price_cash_id, $minMarkup, $prs) { $db = DbSingleton::getDb(); $dbt = DbSingleton::getTokoDb();
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $rin = $db->query("SELECT * FROM `J_INCOME_STR` WHERE `income_id`='$income_id';");
        $nin = $db->num_rows($rin);
        for ($ii = 1; $ii <= $nin; $ii++) {
            $art_id = $db->result($rin, $ii-1, "art_id");
            if ($art_id > 0) {
                $r3 = $dbt->query("SELECT * FROM `T2_ARTICLES_PRICE_STOCK` WHERE `ART_ID`='$art_id' LIMIT 1;");
                $oper_price = $dbt->result($r3,0,"OPER_PRICE");
                $pred_price = 0; $prc = [];

                $r = $dbt->query("SELECT * FROM `T2_ARTICLES_PRICE_RATING` WHERE `art_id`='$art_id' AND `in_use`='1' LIMIT 1;");
                $n = $dbt->num_rows($r);
                for ($j = 0; $j <= 11; $j++) {
                    $percent = $prs[$j];
                    if ($j == 0) {
                        $price = $oper_price;
                        if ($n == 0 && $price_cash_id > 0) {
                            $price = $this->getSummCash($price, 2, $price_cash_id);
                        }
                        $pred_price = round($price + ($price * $percent / 100), 2);
                    } else {
                        $price = $pred_price;
                        $pred_price = round($price + ($price * $percent / 100), 2);
                    }
                    $prc[$j] = $pred_price;
                }

                $default_cash_id = 2;
                if ($n == 0 && $price_cash_id > 0) {
                    $default_cash_id = $price_cash_id;
                }

                if ($n == 1) {
                    $dbt->query("UPDATE `T2_ARTICLES_PRICE_RATING` SET `in_use`='0' WHERE `art_id`='$art_id' AND `in_use`='1';");
                }
                $query = "INSERT INTO `T2_ARTICLES_PRICE_RATING` (`art_id`,`in_use`,`cash_id`,`data_update`,`user_id`,`template_id`,`minMarkup`";

                for ($ip = 0; $ip <= 11; $ip++) {
                    $ipp = $ip + 1;
                    $query .= ",`price_$ipp`,`persent_$ipp`";
                }

                $query .= ") VALUES ('$art_id','1','$default_cash_id',CURDATE(),'$user_id','0','$minMarkup'";
                for ($i = 0; $i <= 11; $i++) {
                    $price = $prc[$i];
                    $percent = $prs[$i];
                    $query .= ",'$price','$percent'";
                }
                $query .= ");";

                $dbt->query($query);
            }
        }
        return "done";
    }

    function getSummCash($summ, $cash_id, $to_cash) {
        $kours = new kours;
        list($usd_to_uah, $eur_to_uah) = $kours->getKoursData();
        $summary = $summ;
        if ($to_cash == 1) {
            if ($cash_id == 2) {
                $summary = $summ * $usd_to_uah;
            }
            if ($cash_id == 3) {
                $summary = $summ * $eur_to_uah;
            }
        }
        if ($to_cash == 2) {
            if ($cash_id == 1) {
                $summary = $summ / $usd_to_uah;
            }
            if ($cash_id == 3) {
                $summary = ($summ * $usd_to_uah) / $eur_to_uah;
            }
        }
        if ($to_cash == 3) {
            if ($cash_id == 1) {
                $summary = $summ / $eur_to_uah;
            }
            if ($cash_id == 2) {
                $summary = ($summ * $usd_to_uah) / $eur_to_uah;
            }
        }
        if ($cash_id == $to_cash) {
            $summary = $summ;
        }
        return round($summary, 2);
    }

    function recalculateMadeIncome($income_id) { $db = DbSingleton::getDb(); $dbt = DbSingleton::getTokoDb();
        $usd_to_uah = $this->getIncomeKoursUSD($income_id);
        $r = $db->query("SELECT * FROM `J_INCOME_STR` WHERE `income_id`='$income_id';");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $income_str_id = $db->result($r, $i - 1, "id");
            $art_id = $db->result($r, $i - 1, "art_id");
            $price_buh_cashin = $db->result($r, $i - 1, "price_buh_cashin");

            $price_uah = $price_buh_cashin;
            $price_pda = $price_buh_cashin + ($price_buh_cashin * 0.2);
            $price_usd = $price_pda / $usd_to_uah;

            $db->query("UPDATE `J_INCOME_STR` SET `price_man_cashin`='$price_pda', `price_man_usd`='$price_usd', `price_buh_uah`='$price_uah', `price_man_uah`='$price_pda'
            WHERE `id`='$income_str_id' LIMIT 1;");

            $r2 = $db->query("SELECT * FROM `T2_ARTICLES_PARTITIONS` WHERE `art_id`='$art_id' ORDER BY `id` DESC LIMIT 1;");
            $partition_id = $db->result($r2,0,"id");
            $amount = $db->result($r2,0,"amount");
            $oper_price = $db->result($r2,0,"oper_price");
            $general_stock = $this->getGeneralStock($art_id);

            if ($oper_price == 0) {
                $new_oper_price = round($price_usd, 2);
            } else {
                $new_oper_price = (($oper_price * $general_stock / ($general_stock - $amount)) * ($general_stock - $amount) + $amount * $price_usd) / $general_stock;
            }

            $dbt->query("UPDATE `T2_ARTICLES_PRICE_STOCK` SET `OPER_PRICE`='$new_oper_price' WHERE `ART_ID`='$art_id' LIMIT 1;");

            $price_uah = round($price_uah, 2);
            $price_pda = round($price_pda, 2);
            $price_usd = round($price_usd, 2);

            $db->query("UPDATE `T2_ARTICLES_PARTITIONS` SET `oper_price`='$new_oper_price', `price`='$price_usd', `price_buh_uah`='$price_uah', `price_man_uah`='$price_pda' WHERE `id`='$partition_id' LIMIT 1;");
        }

        return true;
    }

    function getGeneralStock($art_id) { $dbt = DbSingleton::getTokoDb();
        $r = $dbt->query("SELECT `GENERAL_STOCK` FROM `T2_ARTICLES_PRICE_STOCK` WHERE `ART_ID`='$art_id' LIMIT 1;");
        return $dbt->result($r, 0, "GENERAL_STOCK");
    }

    function getIncomeKoursUSD($income_id) { $db = DbSingleton::getDb();
        $r = $db->query("SELECT `usd_to_uah` FROM `J_INCOME` WHERE `id`='$income_id' LIMIT 1;");
        return $db->result($r, 0, "usd_to_uah");
    }

    function getCellName($sel_id) { $db = DbSingleton::getTokoDb();
        $name = "";
        $r = $db->query("SELECT `cell_value` FROM `STORAGE_CELLS` WHERE `status`='1' AND `id`='$sel_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $name = $db->result($r,0,"cell_value");
        }
        return $name;
    }

    /*
     * ���������� �������
     * */
    function backIncome($income_id) { $db = DbSingleton::getDb(); $dbt = DbSingleton::getTokoDb();
        $r = $db->query("SELECT * FROM `J_INCOME` WHERE `id`='$income_id' LIMIT 1;");
        $storage_id = $db->result($r, 0, "storage_id");
        $cell_id = $db->result($r, 0, "storage_cells_id");
        $rom = $db->query("SELECT * FROM `J_INCOME_STR` WHERE `income_id`='$income_id';");
        $nomer = $db->num_rows($rom);
        for ($i = 1; $i <= $nomer; $i++) {
            $art_id = $db->result($rom, $i - 1, "art_id");
            $amount = $db->result($rom, $i - 1, "amount");
            $this->backArticlesStorage($art_id, $amount, $storage_id, $cell_id);
            $dbt->query("DELETE FROM `T2_ARTICLES_STOCK` WHERE `art_id`='$art_id' AND `income_id`='$income_id' LIMIT 1;");
            $this->backArticlesPriceStock($income_id, $art_id, $amount);
            $db->query("DELETE FROM `J_ART_DOCS` WHERE `art_id`='$art_id' AND `doc_type`='1' AND `doc_id`='$income_id' LIMIT 1;");
            $db->query("DELETE FROM `T2_ARTICLES_PARTITIONS` WHERE `art_id`='$art_id' AND `parrent_doc_id`='$income_id' LIMIT 1;");
        }
        $db->query("UPDATE `J_INCOME` SET `status`='30' WHERE `id`='$income_id' LIMIT 1;");
        return true;
    }

    function backArticlesPriceStock($income_id, $art_id, $amount) { $dbt = DbSingleton::getTokoDb();
        $r = $dbt->query("SELECT * FROM `T2_ARTICLES_PRICE_STOCK` WHERE `ART_ID`='$art_id' LIMIT 1;");
        $n = $dbt->num_rows($r);
        if ($n > 0) {
            $oper_price = $dbt->result($r, 0, "OPER_PRICE");
            if ($oper_price == 0) {
                $dbt->query("DELETE FROM `T2_ARTICLES_PRICE_STOCK` WHERE `ART_ID`='$art_id' LIMIT 1;");
            } else {
                $r = $dbt->query("SELECT `oper_price` FROM `T2_ARTICLES_STOCK` WHERE `art_id`='$art_id' AND `income_id`='$income_id' ORDER BY `id` DESC LIMIT 1;");
                $n = $dbt->num_rows($r);
                if ($n > 0) {
                    $old_oper_price = $dbt->result($r, 0, "oper_price");
                    $dbt->query("UPDATE `T2_ARTICLES_PRICE_STOCK` SET `OPER_PRICE`=$old_oper_price, `GENERAL_STOCK`=`GENERAL_STOCK`-$amount WHERE `ART_ID`='$art_id' LIMIT 1;");
                } else {
                    $dbt->query("DELETE FROM `T2_ARTICLES_PRICE_STOCK` WHERE `ART_ID`='$art_id' LIMIT 1;");
                }
            }
        }
        return true;
    }

    function backArticlesStorage($art_id, $amount, $storage_id, $cell_id) { $dbt = DbSingleton::getTokoDb();
        $r = $dbt->query("SELECT * FROM `T2_ARTICLES_STRORAGE` WHERE `ART_ID`='$art_id' AND `STORAGE_ID`='$storage_id' LIMIT 1;");
        $n = $dbt->num_rows($r);
        if ($n > 0) {
            $dbt->query("UPDATE `T2_ARTICLES_STRORAGE` SET `AMOUNT`=`AMOUNT`-$amount WHERE `ART_ID`='$art_id' AND `STORAGE_ID`='$storage_id' LIMIT 1;");
        }
        $r = $dbt->query("SELECT * FROM `T2_ARTICLES_STRORAGE_CELLS` WHERE `ART_ID`='$art_id' AND `STORAGE_ID`='$storage_id' AND `STORAGE_CELLS_ID`='$cell_id' LIMIT 1;");
        $n = $dbt->num_rows($r);
        if ($n > 0) {
            $dbt->query("UPDATE `T2_ARTICLES_STRORAGE_CELLS` SET `AMOUNT`=`AMOUNT`-$amount WHERE `ART_ID`='$art_id' AND `STORAGE_ID`='$storage_id' AND `STORAGE_CELLS_ID`='$cell_id' LIMIT 1;");
        }
        return true;
    }

}
