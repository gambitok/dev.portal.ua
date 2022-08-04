<?php

class back_clients {

    protected $prefix_new = 'В';

    function getSaleInvoiceCap($invoice_id) { $db = DbSingleton::getDb();
        $r = $db->query("SELECT `prefix`, `doc_nom` FROM `J_SALE_INVOICE` WHERE `id` = '$invoice_id' LIMIT 1;");
        return $db->result($r, 0, "prefix") . " - " . $db->result($r, 0, "doc_nom");
    }

    function getUserTpointId($user_id) { $db = DbSingleton::getDb();
        $tpoint_id = 0;
        $r = $db->query("SELECT `tpoint_id` FROM `media_users` WHERE `id` = '$user_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $tpoint_id = $db->result($r, 0, "tpoint_id");
        }
        return $tpoint_id;
    }

    function getClientsList() { $db = DbSingleton::getDb();
        $r = $db->query("SELECT `id`, `name` FROM `A_CLIENTS` WHERE `status`='1';");
        $n = $db->num_rows($r);
        $list = "";
        for ($i = 1; $i <= $n; $i++) {
            $client_id = $db->result($r, $i - 1, "id");
            $name = $db->result($r, $i - 1, "name");
            $list .= "<option value='$client_id'>$client_id - $name</option>";
        }
        return $list;
    }

    function getStoragesList() { $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT `id`, `name` FROM `STORAGE` WHERE `status`='1' ORDER BY `name` ASC;");
        $n = $db->num_rows($r);
        $list = "";
        for ($i = 1; $i <= $n; $i++) {
            $storage_id = $db->result($r, $i - 1, "id");
            $name = $db->result($r, $i - 1, "name");
            $list .= "<option value='$storage_id'>$name</option>";
        }
        return $list;
    }

    function getCellsList($sel_id = 0) { $db = DbSingleton::getTokoDb();
        if ($sel_id != 0) {
            $where = "AND `storage_id`='$sel_id'";
            $list = "";
            $r = $db->query("SELECT `id`, `cell_value` FROM `STORAGE_CELLS` WHERE `status`='1' $where ORDER BY `id` ASC;");
            $n = $db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $cell_id = $db->result($r, $i - 1, "id");
                $name = $db->result($r, $i - 1, "cell_value");
                $list .= "<option value='$cell_id'>$name</option>";
            }
        } else {
            $list = "<option value='0'>-Не вибрано-</option>";
        }
        return $list;
    }

    function getBrandIdByArtId($art_id) { $db = DbSingleton::getTokoDb();
        $brand_id = 0;
        $r = $db->query("SELECT `BRAND_ID` FROM `T2_ARTICLES` WHERE `ART_ID` = '$art_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $brand_id = $db->result($r, 0, "BRAND_ID");
        }
        return $brand_id;
    }

    function getBrandName($brand_id) { $db = DbSingleton::getTokoDb();
        $name = "";
        $r = $db->query("SELECT `BRAND_NAME` FROM `T2_BRANDS` WHERE `BRAND_ID` = '$brand_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $name = $db->result($r, 0, "BRAND_NAME");
        }
        return $name;
    }

    function getTpointName($id) { $db = DbSingleton::getDb();
        $name = "";
        $r = $db->query("SELECT `name` FROM `T_POINT` WHERE `id`='$id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $name = $db->result($r, 0, "name");
        }
        return $name;
    }

    function getClientName($id) { $db = DbSingleton::getDb();
        $name = "";
        $r = $db->query("SELECT `name` FROM `A_CLIENTS` WHERE `id`='$id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $name = $db->result($r, 0, "name");
        }
        return $name;
    }

    function labelCommentsCount($back_id) { $db = DbSingleton::getDb();
        $label = "";
        $r = $db->query("SELECT COUNT(`id`) as kol FROM `J_BACK_CLIENTS_COMMENTS` WHERE `back_id` = '$back_id';");
        $kol = 0 + $db->result($r, 0, "kol");
        if ($kol > 0) {
            $label = "<span class='label label-tab label-info'>$kol</span>";
        }
        return array($kol, $label);
    }

    function getMediaUserName($user_id) { $db = DbSingleton::getDb();
        $name = "";
        $r = $db->query("SELECT `name` FROM `media_users` WHERE `id` = '$user_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $name = $db->result($r, 0, "name");
        }
        return $name;
    }

    function get_df_doc_nom_new($sale_invoice_id, $seller_id) { $db = DbSingleton::getDb();
        $doc_nom = 0; $doc_type_id = 0;
        $r = $db->query("SELECT `doc_type_id` FROM `J_SALE_INVOICE` WHERE `id` = '$sale_invoice_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $doc_type_id = $db->result($r, 0, "doc_type_id");
            $year = date("Y");
            $r1 = $db->query("SELECT MAX(`doc_nom`) as doc_nom FROM `J_BACK_CLIENTS` 
            WHERE `seller_id`='$seller_id' AND `oper_status`='30' AND `doc_type_id`='$doc_type_id' AND `status`='1' AND `data`>='$year-01-01' LIMIT 1;");
            $doc_nom = 0 + $db->result($r1, 0, "doc_nom") + 1;
        }
        return array($doc_nom, $doc_type_id);
    }

    function getSaleInvoiceData($invoice_id) { $db = DbSingleton::getDb();
        $r = $db->query("SELECT `usd_to_uah`, `eur_to_uah`, `user_id` FROM `J_SALE_INVOICE` WHERE `id` = '$invoice_id' LIMIT 1;");
        $usd_to_uah = $db->result($r, 0, "usd_to_uah");
        $eur_to_uah = $db->result($r, 0, "eur_to_uah");
        $user_create = $db->result($r, 0, "user_id");
        return array($usd_to_uah, $eur_to_uah, $user_create);
    }

    function getBackClientsName($back_id) { $db = DbSingleton::getDb();
        $prefix = ""; $doc_nom = 0;
        $r = $db->query("SELECT `prefix`, `doc_nom` FROM `J_BACK_CLIENTS` WHERE `id` = '$back_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $prefix = $db->result($r, 0, "prefix");
            $doc_nom = $db->result($r, 0, "doc_nom");
        }
        return $prefix . "-" . $doc_nom;
    }

    function getTpointLocalStorage($tpoint_id) { $db = DbSingleton::getDb();
        $storage_id = 0;
        $r = $db->query("SELECT `storage_id` FROM `T_POINT_STORAGE` WHERE `tpoint_id` = '$tpoint_id' AND `default` = '1' AND `status` = '1' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $storage_id = $db->result($r, 0, "storage_id");
        }
        if ($n == 0) {
            $r = $db->query("SELECT `storage_id` FROM `T_POINT_STORAGE` WHERE `tpoint_id` = '$tpoint_id' AND `local` = '41' AND `status` = '1' LIMIT 1;");
            $n = $db->num_rows($r);
            if ($n == 1) {
                $storage_id = $db->result($r, 0, "storage_id");
            }
        }
        return $storage_id;
    }

    function newBackClientsCard() { $db = DbSingleton::getDb();
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $ses_tpoint_id = $_SESSION["media_tpoint_id"];
        $storage_id = $this->getTpointLocalStorage($ses_tpoint_id);
        $r = $db->query("SELECT MAX(`id`) as mid FROM `J_BACK_CLIENTS`;");
        $back_id = 0 + $db->result($r, 0, "mid") + 1;
        $db->query("INSERT INTO `J_BACK_CLIENTS` (`id`,`prefix`,`doc_nom`,`user_id`,`data`,`tpoint_id`,`storage_id`) VALUES ('$back_id','$this->prefix_new','0','$user_id',CURDATE(),'$ses_tpoint_id','$storage_id');");
        return $back_id;
    }

    function show_back_clients_list() { $db = DbSingleton::getDb();
        $gmanual = new gmanual;
        session_start();
        $ses_tpoint_id = $_SESSION["media_tpoint_id"];
        $media_user_id = $_SESSION["media_user_id"];
        $where = " AND j.tpoint_id='$ses_tpoint_id' AND j.status_back!=0";
        if ($media_user_id == 1 || $media_user_id == 2 || $media_user_id == 7) {
            $where = " AND j.status_back!=0";
        }
        $data_cur = date("Y-m-d");
        $data_old = date('Y-m-d', strtotime('-7 day', strtotime($data_cur)));
        $where_date = " AND j.data>='$data_old 00:00:00' AND j.data<='$data_cur 23:59:59'";
        $list = "";
        $r = $db->query("SELECT j.*, t.name as tpoint_name, CASH.name as cash_name, c.name as client_name, si.prefix as sale_prefix, si.doc_nom as sale_doc_nom 
        FROM `J_BACK_CLIENTS` j
            LEFT OUTER JOIN `T_POINT` t on t.id=j.tpoint_id
            LEFT OUTER JOIN `A_CLIENTS` c on c.id=j.client_id
            LEFT OUTER JOIN `CASH` on CASH.id=j.cash_id
            LEFT OUTER JOIN `J_SALE_INVOICE` si on si.id=j.sale_invoice_id
        WHERE j.status=1 $where $where_date 
        ORDER BY j.status_back ASC, j.id DESC LIMIT 0,500;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r,$i - 1,"id");
            $prefix = $db->result($r,$i - 1,"prefix");
            $doc_nom = $db->result($r,$i - 1,"doc_nom");
            $client_name = $db->result($r,$i - 1,"client_name");
            $cash_name = $db->result($r,$i - 1,"cash_name");
            $summ = $db->result($r,$i - 1,"summ");
            $sale_nom = $db->result($r,$i - 1,"sale_prefix") . "-" . $db->result($r,$i - 1,"sale_doc_nom");
            $data = $db->result($r,$i - 1,"data");
            $user_name = $this->getMediaUserName($db->result($r,$i - 1,"user_id"));
            $status_back = $db->result($r,$i - 1,"status_back");
            $status_back_name = $gmanual->get_gmanual_caption($status_back);
            $function = "showBackClientsCard(\"$id\")";
            $list .= "<tr style='cursor:pointer' onClick='$function'>
                <td>$prefix-$doc_nom</td>
                <td align='center'>$data</td>
                <td>$client_name</td>
                <td>$cash_name</td>
                <td>$summ</td>
                <td>$sale_nom</td>
                <td>$user_name</td>
                <td>$status_back_name</td>
            </tr>";
        }
        return $list;
    }

    function show_back_clients_list_filter($data_start, $data_end) { $db = DbSingleton::getDb();
        $gmanual = new gmanual;
        session_start();
        $ses_tpoint_id = $_SESSION["media_tpoint_id"];
        $media_user_id = $_SESSION["media_user_id"];
        $data_cur = date("Y-m-d");
        $where = " AND j.tpoint_id='$ses_tpoint_id' AND j.status_back!=0";
        if ($media_user_id == 1 || $media_user_id == 2 || $media_user_id == 7) {
            $where = " AND j.status_back!=0";
        }
        if ($data_start != "" && $data_end != "") {
            $where_date = "AND j.data>='$data_start 00:00:00' AND j.data<='$data_end 23:59:59'";
        } else {
            $where_date = " AND j.data>='$data_cur 00:00:00' AND j.data<='$data_cur 23:59:59'";
        }
        $list = "";
        $r = $db->query("SELECT j.*, t.name as tpoint_name, CASH.name as cash_name, c.name as client_name, si.prefix as sale_prefix, si.doc_nom as sale_doc_nom 
        FROM `J_BACK_CLIENTS` j
            LEFT OUTER JOIN `T_POINT` t on t.id=j.tpoint_id
            LEFT OUTER JOIN `A_CLIENTS` c on c.id=j.client_id
            LEFT OUTER JOIN `CASH` on CASH.id=j.cash_id
            LEFT OUTER JOIN `J_SALE_INVOICE` si on si.id=j.sale_invoice_id
        WHERE j.status=1 $where $where_date 
        ORDER BY j.status_back ASC, j.id DESC LIMIT 0,500;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r, $i - 1, "id");
            $prefix = $db->result($r, $i - 1, "prefix");
            $doc_nom = $db->result($r, $i - 1, "doc_nom");
            $client_name = $db->result($r, $i - 1, "client_name");
            $cash_name = $db->result($r, $i - 1, "cash_name");
            $summ = $db->result($r, $i - 1, "summ");
            $sale_nom = $db->result($r, $i - 1, "sale_prefix") . "-" . $db->result($r, $i - 1, "sale_doc_nom");
            $data = $db->result($r, $i - 1, "data");
            $user_name = $this->getMediaUserName($db->result($r, $i - 1, "user_id"));
            $status_back = $db->result($r, $i - 1, "status_back");
            $status_back_name = $gmanual->get_gmanual_caption($status_back);
            $function = "showBackClientsCard(\"$id\")";
            $list .= "<tr style='cursor:pointer' onClick='$function'>
                <td>$prefix-$doc_nom</td>
                <td align='center'>$data</td>
                <td>$client_name</td>
                <td>$cash_name</td>
                <td>$summ</td>
                <td>$sale_nom</td>
                <td>$user_name</td>
                <td>$status_back_name</td>
            </tr>";
        }
        return $list;
    }

    function getKoursData() { $db = DbSingleton::getDb();
        $slave = new slave;
        $usd_to_uah = 0;
        $eur_to_uah = 0;
        $r = $db->query("SELECT `kours_value` FROM `J_KOURS` WHERE `cash_id` = '2' and `in_use` = '1' ORDER BY `id` DESC LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $usd_to_uah = $slave->to_money(round($db->result($r, 0, "kours_value"), 2));
        }
        $r = $db->query("SELECT `kours_value` FROM `J_KOURS` WHERE `cash_id` = '3' and `in_use` = '1' ORDER BY `id` DESC LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $eur_to_uah = $slave->to_money(round($db->result($r, 0, "kours_value"), 2));
        }
        return array($usd_to_uah, $eur_to_uah);
    }

    function getSaleInvoiceName($id) { $db = DbSingleton::getDb();
        $name = "";
        $r = $db->query("SELECT `prefix`, `doc_nom` FROM `J_SALE_INVOICE` WHERE `id` = '$id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $name = $db->result($r, 0, "prefix") . "-" . $db->result($r, 0, "doc_nom");
        }
        return $name;
    }

    function getSaleInvoicePrefix($id) { $db = DbSingleton::getDb();
        $prefix = "";
        $r = $db->query("SELECT `prefix` FROM `J_SALE_INVOICE` WHERE `id` = '$id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $prefix = $db->result($r, 0, "prefix");
        }
        return $prefix;
    }

    function getSaleInvoiceSumm2($id) { $db = DbSingleton::getDb();
        $summ = 0;
        $summ_debit = 0;
        $r = $db->query("SELECT `summ`, `summ_debit` FROM `J_SALE_INVOICE` WHERE `id`='$id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $summ = $db->result($r, 0, "summ");
            $summ_debit = $db->result($r, 0, "summ_debit");
        }
        return array($summ, $summ_debit);
    }

    function showBackClientsCard($back_id) { $db = DbSingleton::getDb();
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $user_name = $_SESSION["user_name"];
        $prefix = "";
        $doc_nom = 0;
        $form = ""; $form_htm = RD . "/tpl/back_clients_card.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }

        $r = $db->query("SELECT * FROM `J_BACK_CLIENTS` WHERE `id`='$back_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 0) {
            $form_htm = RD . "/tpl/access_deny.htm";
            if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        }
        if ($n == 1) {
            $this->updateBackClientsSumm($back_id);
            $prefix = $db->result($r, 0, "prefix");
            $doc_nom = $db->result($r, 0, "doc_nom");
            $user_use = $db->result($r, 0, "user_use");
            if ($user_id != $user_use && $user_use > 0) {
                $form_htm = RD . "/tpl/back_clients_use_deny.htm";
                if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
                $form = str_replace("{user_name}", $this->getMediaUserName($user_use), $form);
                $admin_unlock = "";
                if ($user_id == 1 || $user_id == 2) {
                    $admin_unlock = "<button class='btn btn-sm btn-warning' onClick='unlockBackClientsCard(\"$back_id\");'><i class='fa fa-unlock'></i> Розблокувати</button>";
                }
                $form = str_replace("{admin_unlock}", $admin_unlock, $form);
            }
            if ($user_id == $user_use || $user_use == 0) {
                $data = $db->result($r, 0, "data");
                if ($data == "0000-00-00") {
                    $data = date("Y-m-d");
                }
                $cash_id = $db->result($r, 0, "cash_id");
                $cash_name = $this->getCashAbr($cash_id);
                $sale_invoice_id = $db->result($r, 0, "sale_invoice_id");
                $sale_invoice_name = $this->getSaleInvoiceName($sale_invoice_id);
                $tpoint_id = $db->result($r, 0, "tpoint_id");
                $tpoint_name = $this->getTpointName($tpoint_id);
                $client_id = $db->result($r, 0, "client_id");
                $client_name = $this->getClientName($client_id);
                $storage_id = $db->result($r, 0, "storage_id");
                $storage_list = $this->showStorageSelectListByTpoint($tpoint_id, $storage_id);
                $cell_id = $db->result($r, 0, "cell_id");
                $cells_list = $this->showStorageCellsList($storage_id, $cell_id);
                $usd_to_uah = $db->result($r, 0, "usd_to_uah");
                $eur_to_uah = $db->result($r, 0, "eur_to_uah");
                list($usd_to_uah_new, $eur_to_uah_new) = $this->getKoursData();
                if ($usd_to_uah != $usd_to_uah_new) { $usd_to_uah = $usd_to_uah_new; }
                if ($eur_to_uah != $eur_to_uah_new) { $eur_to_uah = $eur_to_uah_new; }
                $summ = $db->result($r, 0, "summ");
                $status_back = $db->result($r, 0, "status_back");

                if ($status_back == 103) {
                    $form = str_replace("{hide_new_row_button}"," disabled style=\"visibility:hidden;\"",$form);
                    $form = str_replace("{oper_disabled}"," disabled",$form);
                    $form = str_replace("{oper_disabled2}"," disabled",$form);
                    $form = str_replace("{oper_disabled3}"," ",$form);
                }

                $form = str_replace("{back_id}",$back_id,$form);
                $form = str_replace("{data}",$data,$form);
                $form = str_replace("{cash_id}",$cash_id,$form);
                $form = str_replace("{cash_name}",$cash_name,$form);
                $form = str_replace("{back_summ}",$summ,$form);
                $form = str_replace("{sale_invoice_id}",$sale_invoice_id,$form);
                $form = str_replace("{sale_invoice_name}",$sale_invoice_name,$form);
                $form = str_replace("{storage_id}",$storage_id,$form);
                $form = str_replace("{storage_list}",$storage_list,$form);
                $form = str_replace("{cells_list}",$cells_list,$form);
                $form = str_replace("{tpoint_id}",$tpoint_id,$form);
                $form = str_replace("{tpoint_name}",$tpoint_name,$form);
                $form = str_replace("{client_id}",$client_id,$form);
                $form = str_replace("{client_name}",str_replace('"',"",$client_name),$form);
                $form = str_replace("{usd_to_uah}",$usd_to_uah,$form);
                $form = str_replace("{eur_to_uah}",$eur_to_uah,$form);
                $form = str_replace("{comment}","",$form);
                $form = str_replace("{status_back_id}",$status_back,$form);
                $form = str_replace("{users_email}",$this->getBackClientCron($back_id),$form);

                list($BackClientsChildsList, $kol_str_row) = $this->showBackClientsStrList($back_id, $status_back, $sale_invoice_id);
                $form = str_replace("{BackClientsChildsList}",$BackClientsChildsList,$form);
                $form = str_replace("{kol_str_row}",$kol_str_row,$form);

                if ($status_back == 102 && $kol_str_row > 0) {
                    $form = str_replace("{oper_disabled}"," disabled",$form);
                    $form = str_replace("{oper_disabled2}"," ",$form);
                    $form = str_replace("{oper_disabled3}"," disabled",$form);
                }
                if ($status_back == 102 && $kol_str_row == 0) {
                    $form = str_replace("{oper_disabled}","",$form);
                    $form = str_replace("{oper_disabled2}"," disabled",$form);
                    $form = str_replace("{oper_disabled3}"," disabled",$form);
                }
                $form = str_replace("{oper_disabled}","",$form);
                $form = str_replace("{oper_disabled2}","disabled",$form);
                $form = str_replace("{hide_new_row_button}","",$form);
                $form = str_replace("{my_user_id}",$user_id,$form);
                $form = str_replace("{my_user_name}",$user_name,$form);

                list(, $label_comments) = $this->labelCommentsCount($back_id);
                $form = str_replace("{labelCommentsCount}",$label_comments,$form);
                $form = str_replace("{labelArticlesUnKnownCount}","",$form);

                $this->setBackClientsCardUserAccess($back_id, $user_id);

                $rr = $db->query("SELECT * FROM `PRRO_SALE_INVOICE` WHERE `INVOICE_ID` = $back_id AND `DOC_TYPE_ID` = 2 LIMIT 1;");
                $nn = $db->num_rows($rr);
                $form = str_replace("{cb_check_visible_add}", ($this->checkCBBackCheck($sale_invoice_id) && $nn == 0) ? "" : "none", $form);
                $form = str_replace("{cb_check_visible_show}", ($this->checkCBBackCheck($sale_invoice_id) && $nn > 0) ? "" : "none", $form);
            }
        }
        return array($form, $prefix . "-" . $doc_nom);
    }

    function checkCBBackCheck($sale_invoice_id)
    {
        $db = DbSingleton::getDb();
        $r = $db->query("SELECT `INVOICE_ID` FROM `PRRO_SALE_INVOICE` WHERE `INVOICE_ID` = $sale_invoice_id AND `DOC_TYPE_ID` = 1 LIMIT 1;");
        $n = $db->num_rows($r);
        return ($n > 0);
    }

    function unlockBackClientsCard($back_id) {
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $answer = 0;
        if ($user_id == 1 || $user_id == 2) {
            $db = DbSingleton::getDb();
            $db->query("UPDATE `J_BACK_CLIENTS` SET `user_use`='0' WHERE `id`='$back_id';");
            $answer = 1;
        }
        return $answer;
    }

    function closeBackClientsCard($back_id) {
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $this->unsetBackClientsCardUserAccess($back_id, $user_id);
        return 1;
    }

    function setBackClientsCardUserAccess($back_id, $user_id) { $db = DbSingleton::getDb();
        if ($back_id > 0 && $user_id > 0) {
            $db->query("UPDATE `J_BACK_CLIENTS` SET `user_use`='$user_id' WHERE `id`='$back_id';");
        }
        return true;
    }

    function unsetBackClientsCardUserAccess($back_id, $user_id) { $db = DbSingleton::getDb();
        if ($back_id > 0 && $user_id > 0) {
            $db->query("UPDATE `J_BACK_CLIENTS` SET `user_use`='0' WHERE `id`='$back_id';");
        }
        return true;
    }

    function clearBackClientsStr($back_id) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "";
        $back_id = $slave->qq($back_id);
        $r = $db->query("SELECT `oper_status`, `status_back` FROM `J_BACK_CLIENTS` WHERE `id`='$back_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $oper_status = $db->result($r, 0, "oper_status");
            $status_back = $db->result($r, 0, "status_back");
            if ($oper_status == 30 && $status_back == 102) {
                $db->query("DELETE FROM `J_BACK_CLIENTS_STR` WHERE `back_id`='$back_id' LIMIT 1;");
                $db->query("UPDATE `J_BACK_CLIENTS` SET `summ`=0 WHERE `id`='$back_id' LIMIT 1;");
                $answer = 1; $err = "";
            } else {
                $answer = 0; $err = "Документ заблоковано. Зміни вносити заборонено.";
            }
        }
        return array($answer, $err);
    }

    function setBackClientsClient($back_id, $client_id) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0;  $err = "Помилка збереження даних!";
        $client_name = "";
        $back_id = $slave->qq($back_id);
        $client_id = $slave->qq($client_id);
        if ($back_id > 0 && $client_id > 0) {
            $db->query("UPDATE `J_BACK_CLIENTS` SET `client_id`='$client_id' WHERE `id`='$back_id';");
            $answer = 1; $err = "";
            $client_name = $this->getClientName($client_id);
        }
        return array($answer, $err, $client_name);
    }

    function showBackClientsClientList($sel_id) { $db = DbSingleton::getDb();
        $form = "";
        $form_htm = RD . "/tpl/clients_parrent_tree.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        session_start();
        $user_tpoint_id = $this->getUserTpointId($_SESSION["media_user_id"]);
        $user_tpoint_name = $this->getTpointName($user_tpoint_id);
        $list = "";
        $r = $db->query("SELECT c.*, ot.name as org_type_name, t2cn.COUNTRY_NAME, t2st.STATE_NAME, t2rg.REGION_NAME, t2ct.CITY_NAME, acc.tpoint_id, tp.name as tpoint_name   
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
        WHERE c.status=1 AND ac.id>0 
        GROUP BY c.id;");
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
            $cur = "";
            if ($id == $sel_id) {
                $cur = "background-color:#0CF;";
            }
            $fn = " onClick='setBackClientsClient(\"$id\",\"$user_tpoint_id\",\"$user_tpoint_name\")'";
            $list .= "<tr style='$cur cursor:pointer;' $fn>
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

    function unlinkBackClientsClient($back_id) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка збереження даних!";
        $back_id = $slave->qq($back_id);
        if ($back_id > 0) {
            $db->query("UPDATE `J_BACK_CLIENTS` SET `client_id`='0' WHERE `id`='$back_id';");
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

    function getSaleInvoiceCashData($id) { $db = DbSingleton::getDb();
        $cash_id = 1;
        $usd_to_uah = $eur_to_uah = 1;
        $r = $db->query("SELECT `cash_id`, `usd_to_uah`, `eur_to_uah` FROM `J_SALE_INVOICE` WHERE `id`='$id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $cash_id = $db->result($r, 0, "cash_id");
            $usd_to_uah = $db->result($r, 0, "usd_to_uah");
            $eur_to_uah = $db->result($r, 0, "eur_to_uah");
        }
        return array($cash_id, $usd_to_uah, $eur_to_uah);
    }

    function getBackClientSaleInvoiceStr($sis_id, $art_id) { $db = DbSingleton::getDb();
        $back_amount = $back_price = $back_summ = 0;
        $r = $db->query("SELECT SUM(`amount`) as back_amount, SUM(`price`) as back_price, SUM(`summ`) as back_summ FROM `J_BACK_CLIENTS_STR` 
        WHERE `art_id`='$art_id' AND `sale_invoice_str_id`='$sis_id' AND `status`=1;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $back_amount += $db->result($r,$i - 1,"back_amount");
            $back_price += $db->result($r,$i - 1,"back_price");
            $back_summ += $db->result($r,$i - 1,"back_summ");
        }
        return array($back_amount, $back_price, $back_summ);
    }

    function showSaleInvoiceArticleSearchForm($si_id, $si_str_id) { $db = DbSingleton::getDb();
        $form = ""; $form_htm = RD . "/tpl/back_clients_articles_list.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $list = "";
        $r = $db->query("SELECT * FROM `J_SALE_INVOICE_STR` WHERE `status`=1 AND `invoice_id`='$si_id' ORDER BY `id` ASC;");
        $n = $db->num_rows($r);
        if ($n > 0) {
            for ($i = 1; $i <= $n; $i++) {
                $sis_id = $db->result($r,$i - 1,"id");
                $art_id = $db->result($r,$i - 1,"art_id");
                $article_nr_displ = $db->result($r,$i - 1,"article_nr_displ");
                $brand_id = $db->result($r,$i - 1,"brand_id");
                $brand_name = $this->getBrandName($brand_id);
                $amount = $db->result($r,$i - 1,"amount");
                $price = $db->result($r,$i - 1,"price_end");
                $summ = $db->result($r,$i - 1,"summ");
                list($back_amount,, $back_summ) = $this->getBackClientSaleInvoiceStr($sis_id, $art_id);
                $max_back = $amount - $back_amount;
                $cur = "";
                if ($si_str_id == $sis_id) {
                    $cur = "background-color:#FFFF00;";
                }
                $fn = " onClick='showBackClientsArticleAmountWindow(\"$art_id\", \"$article_nr_displ\", \"$brand_name\", \"$amount\", \"$price\", \"$summ\",\"$sis_id\",\"$max_back\")'";
                if ($max_back <= 0) {
                    $cur = "background-color:#ebebeb;";
                    $fn = "";
                }
                $list .= "<tr style='$cur cursor:pointer;' $fn>
                    <td>$i</td>
                    <td>$article_nr_displ</td>
                    <td align='center'>$brand_name</td>
                    <td>$amount</td>
                    <td align='right'>$price</td>
                    <td align='right'>$summ</td>
                    <td>$back_amount</td>
                    <td>$back_summ</td>
                </tr>";
            }
        }
        $form = str_replace("{list}",$list,$form);
        return $form;
    }

    function showBackClientsArticleAmountWindow($art_id, $back_id) {
        $form = ""; $form_htm = RD . "/tpl/back_clients_articles_amount_window.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $form = str_replace("{art_id}", $art_id, $form);
        $amount = $this->getBackClientsArticleAmountBack($art_id, $back_id);
        $form = str_replace("{amount}", $amount, $form);
        return $form;
    }

    function getBackClientsArticleAmountBack($art_id, $back_id) { $db = DbSingleton::getDb();
        $amount = 0;
        $r = $db->query("SELECT SUM(`amount`) as back_amount FROM `J_BACK_CLIENTS_STR` WHERE `status`=1 AND `back_id`='$back_id' AND `art_id`='$art_id';");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $amount = $db->result($r, 0, "back_amount") + 0;
        }
        return $amount;
    }

    function showBackClientsSaleInvoiceList($client_id, $si_id) { $db = DbSingleton::getDb();
        $gmanual = new gmanual;
        $list = "";
        $form = ""; $form_htm = RD . "/tpl/back_clients_sale_invoice_tree.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm);}
        $r = $db->query("SELECT sv.*, dp.prefix as dp_prefix, dp.doc_nom as dp_nom, t.name as tpoint_name, sl.name as seller_name, cl.name as client_name, dt.mvalue as doc_type_name,ch.abr2 as cash_abr2 
        FROM `J_SALE_INVOICE` sv
            LEFT OUTER JOIN J_DP dp on dp.id=sv.dp_id
            LEFT OUTER JOIN CASH ch on ch.id=sv.cash_id
            LEFT OUTER JOIN T_POINT t on t.id=sv.tpoint_id
            LEFT OUTER JOIN A_CLIENTS sl on sl.id=sv.seller_id
            LEFT OUTER JOIN A_CLIENTS cl on cl.id=sv.client_conto_id
            LEFT OUTER JOIN manual dt on dt.key='client_sale_type' and dt.id=sv.doc_type_id
        WHERE sv.status=1 AND sv.client_conto_id='$client_id' AND sv.status_invoice='86' ORDER BY sv.status_invoice DESC, sv.data_pay DESC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r,$i - 1,"id");
            $dp_nom = $db->result($r,$i - 1,"dp_prefix") . $db->result($r,$i - 1,"dp_nom");
            $prefix = $db->result($r,$i - 1,"prefix");
            $doc_nom = $db->result($r,$i - 1,"doc_nom");
            $data_create = $db->result($r,$i - 1,"data_create");
            $tpoint_name = $db->result($r,$i - 1,"tpoint_name");
            $seller_id = $db->result($r,$i - 1,"seller_id");
            $seller_name = $db->result($r,$i - 1,"seller_name");
            $client_name = $db->result($r,$i - 1,"client_name");
            $doc_type_name = $db->result($r,$i - 1,"doc_type_name");
            $summ = $db->result($r,$i - 1,"summ");
            $cash_id = $db->result($r,$i - 1,"cash_id");
            $cash_abr = $db->result($r,$i - 1,"cash_abr2");
            $data_pay = $db->result($r,$i - 1,"data_pay");
            $status_select = $db->result($r,$i - 1,"status_select");
            $status_select_cap = $gmanual->get_gmanual_caption($status_select);
            $cur = "";
            if ($id == $si_id) {
                $cur = "background-color:#FFFF00;";
            }
            $fn = " onClick='setBackClientsSaleInvoice(\"$id\", \"$prefix-$doc_nom\", \"$cash_id\", \"$cash_abr\", \"$seller_id\")'";
            $list .= "<tr style='$cur cursor:pointer;' $fn>
                <td>$i</td>
                <td>$prefix-$doc_nom</td>
                <td align='center'>$data_create</td>
                <td>$dp_nom</td>
                <td>$tpoint_name</td>
                <td align='left'>$seller_name</td>
                <td align='left'>$client_name</td>
                <td>$doc_type_name</td>
                <td align='center' style='min-width:80px;'>$summ$cash_abr</td>
                <td align='right'></td>
                <td align='right'>$data_pay</td>
                <td align='center'>$status_select_cap</td>
            </tr>";
        }
        $form = str_replace("{list}",$list,$form);
        return $form;
    }

    function setBackClientsSaleInvoice($back_id, $si_id, $cash_id, $seller_id) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка збереження даних!";
        $back_id = $slave->qq($back_id);
        $si_id = $slave->qq($si_id);
        if ($back_id > 0 && $si_id > 0) {
            list($doc_nom, $doc_type_id) = $this->get_df_doc_nom_new($si_id, $seller_id);
            list($usd_to_uah, $eur_to_uah, $user_create) = $this->getSaleInvoiceData($si_id);
            $db->query("UPDATE `J_BACK_CLIENTS` SET `sale_invoice_id`='$si_id', `doc_nom`='$doc_nom', `doc_type_id`='$doc_type_id', `cash_id`='$cash_id', 
            `usd_to_uah`='$usd_to_uah', `eur_to_uah`='$eur_to_uah', `user_create`='$user_create', `seller_id`='$seller_id' WHERE `id`='$back_id';");
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

    function showBackClientsTpointList($sel_id) { $db = DbSingleton::getDb();
        $list = "";
        $form = ""; $form_htm = RD . "/tpl/tpoint_tree.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm);}
        $r = $db->query("SELECT t.*, t2cn.COUNTRY_NAME, t2st.STATE_NAME, t2rg.REGION_NAME, t2ct.CITY_NAME
        FROM `T_POINT` t 
            LEFT OUTER JOIN `T2_COUNTRIES` t2cn on t2cn.COUNTRY_ID=t.country 
            LEFT OUTER JOIN `T2_STATE` t2st on t2st.STATE_ID=t.state
            LEFT OUTER JOIN `T2_REGION` t2rg on t2rg.REGION_ID=t.region
            LEFT OUTER JOIN `T2_CITY` t2ct on t2ct.CITY_ID=t.city
        WHERE t.status=1;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r,$i - 1,"id");
            $name = $db->result($r,$i - 1,"name");
            $state = $db->result($r,$i - 1,"STATE_NAME");
            $region = $db->result($r,$i - 1,"REGION_NAME");
            $city = $db->result($r,$i - 1,"CITY_NAME");
            $address = $db->result($r,$i - 1,"address");
            $chief = $db->result($r,$i - 1,"chief");
            $worker_name = $this->getMediaUserName($chief);
            $cur = "";
            if ($id == $sel_id) {
                $cur = "background-color:#0CF;";
            }
            $fn = " onClick='setBackClientsTpoint(\"$id\", \"$name\")'";
            $list .= "<tr style='$cur cursor:pointer;' $fn>
                <td>$id</td>
                <td>$name</td>
                <td>$state</td>
                <td>$region</td>
                <td>$city</td>
                <td>$address</td>
                <td>$worker_name</td>
            </tr>";
        }
        $form = str_replace("{list}",$list,$form);
        return $form;
    }

    function unlinkBackClientsTpoint($back_id) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка збереження даних!";
        $back_id = $slave->qq($back_id);
        if ($back_id > 0) {
            $db->query("UPDATE `J_BACK_CLIENTS` SET `tpoint_id`='0' WHERE `id`='$back_id';");
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

    function showBackClientsStrList($back_id, $status_back, $si_id) { $db = DbSingleton::getDb();
        $slave = new slave; $cat = new catalogue;
        $list = "";
        if ($status_back == "") { $status_back = 102; }
        $r = $db->query("SELECT * FROM `J_BACK_CLIENTS_STR` WHERE `back_id`='$back_id' ORDER BY `id` ASC;");
        $n = $db->num_rows($r);
        $kl_rw = $n;
        $summ_back = 0;
        for ($i = 1; $i <= $kl_rw; $i++) {
            $id = $db->result($r, $i - 1, "id");
            $si_str_id = $db->result($r, $i - 1, "sale_invoice_str_id");
            $art_id = $db->result($r, $i - 1, "art_id");
            $article_nr_displ = $db->result($r, $i - 1, "article_nr_displ");
            $brand_id = $db->result($r, $i - 1, "brand_id");
            $brand_name = $cat->getBrandName($brand_id);
            $amount = $db->result($r, $i - 1, "amount");
            $price = $slave->to_money($db->result($r, $i - 1, "price"));
            $summ = $slave->to_money($db->result($r, $i - 1, "summ"));
            $summ_back += $summ;
            if ($status_back == 102) {
                $disabled = "";
                if ($status_back != 102 && $status_back > 0) {
                    $disabled = " disabled";
                }
                $list .= "<tr id='strRow_$i'>
                    <td>$i<input type='hidden' id='idStr_$i' value='$id'></td>
                    <td style='min-width:140px;'><input type='hidden' id='artIdStr_$i' value='$art_id'>
                        <div class='input-group'>
                            <input class='form-control input-xs' type='text' readonly id='article_nr_displStr_$i' value='$article_nr_displ' placeholder='Індекс товару'>
                            <span class='input-group-btn'> 
                                <button type='button' class='btn btn-xs btn-info $disabled' $disabled onClick=\"showSaleInvoiceArticleSearchForm('$i','$si_str_id','$art_id','$back_id','$si_id');\"><i class=\"fa fa-bars\"></i></button>
                            </span>
                        </div>
                        <span class='hidden'>$article_nr_displ</span>
                    </td>
                    <td style='min-width:100px;'><input type='hidden' id='brandIdStr_$i' value='$brand_id'>
                        <input class='form-control input-xs' type='text' readonly id='brandNameStr_$i' value='$brand_name' placeholder='Бренд'>
                        <span class='hidden'>$brand_name</span>
                    </td>
                    <td>
                        <input type='text' id='amountStr_$i' readonly value='$amount' class='form-control input-xs numberOnly'>
                        <span class='hidden'>$amount</span>
                    </td>
                    <td>
                        <input type='text' id='priceStr_$i' readonly value='$price' class='form-control input-xs numberOnlyLong'>
                        <span class='hidden'>$price</span>
                    </td>
                    <td>
                        <input type='text' id='summStr_$i' readonly value='$summ' class='form-control input-xs numberOnlyLong'>
                    </td>
                    <td><button class='btn btn-xs btn-default $disabled' $disabled onClick='dropBackClientsStr(\"$i\",\"$back_id\",\"$id\");'><i class='fa fa-times'></i></button></td>
                </tr>";
            }
            if ($status_back == 103) {
                if ($article_nr_displ != "") {
                    $list .= "<tr>
                        <td>$i<input type='hidden' id='idStr_$i' value='$id'></td>
                        <td style='min-width:140px;'>$article_nr_displ</td>
                        <td style='min-width:120px;'>$brand_name</td>
                        <td>$amount</td>
                        <td>$price</td>
                        <td>$summ</td>
                        <td></td>
                    </tr>";
                }
            }
        }
        if ($status_back == 102) {
            $list = "<tr id='bcStrNewRow' class='hidden'>
                <td>nom_i<input type='hidden' id='idStr_0' value=''></td>
                <td style='min-width:140px;'><input type='hidden' id='artIdStr_0' value=''>
                    <div class='input-group'>
                        <input class='form-control input-xs' type='text' readonly id='article_nr_displStr_0' value='' placeholder='Індекс товару'>
                        <span class='input-group-btn'> 
                            <button type='button' class='btn btn-xs btn-info' onClick=\"showSaleInvoiceArticleSearchForm('i_0','0','0','$back_id','$si_id');\"><i class=\"fa fa-bars\"></i></button>
                        </span>
                    </div>
                    <span class='hidden'></span>
                </td>
                <td style='min-width:100px;'><input type='hidden' id='brandIdStr_0' value=''>
                    <input class='form-control input-xs' type='text' readonly id='brandNameStr_0' value='' placeholder='Бренд'>
                    <span class='hidden'></span>
                </td>
                <td>
                    <div class='input-group'>
                        <input type='text' id='amountStr_0' readonly value='' class='form-control input-xs numberOnly'>
                    </div>
                    <span class='hidden'></span>
                </td>
                <td>
                    <input type='text' id='priceStr_0' readonly value='' class='form-control input-xs numberOnlyLong'>
                    <span class='hidden'></span>
                </td>
                <td>
                    <input type='text' id='summStr_0' readonly value='' class='form-control input-xs numberOnlyLong'>
                </td>
                <td></td>
            </tr>" . $list;
        }
        return array($list, $kl_rw);
    }

    function saveBackClientsCard($back_id, $data_pay, $cash_id, $back_clients_summ, $doc_type_id, $tpoint_id, $client_id, $client_conto_id, $delivery_type_id, $carrier_id, $delivery_address) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка збереження даних!";
        $back_id=$slave->qq($back_id); $data_pay=$slave->qq($data_pay); $cash_id=$slave->qq($cash_id); $back_clients_summ=$slave->qq($back_clients_summ); $doc_type_id=$slave->qq($doc_type_id);
        $tpoint_id=$slave->qq($tpoint_id); $client_id=$slave->qq($client_id); $client_conto_id=$slave->qq($client_conto_id); $delivery_type_id=$slave->qq($delivery_type_id);
        $carrier_id=$slave->qq($carrier_id); $delivery_address=$slave->qq($delivery_address);
        if ($back_id > 0) {
            $db->query("UPDATE `J_BACK_CLIENTS` SET `doc_type_id`='$doc_type_id', `tpoint_id`='$tpoint_id', `client_id`='$client_id', `client_conto_id`='$client_conto_id', `data_pay`='$data_pay', 
            `cash_id`='$cash_id', `summ`='$back_clients_summ', `delivery_type_id`='$delivery_type_id', `carrier_id`='$carrier_id', `delivery_address`='$delivery_address' WHERE `id`='$back_id';");
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

    function saveBackClientsCardData($back_id) {
        $slave = new slave;
        $answer = 0; $err = "Помилка збереження даних!";
        $back_id = $slave->qq($back_id);
        if ($back_id > 0) {
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

    function setArticleToBackClients($back_id, $si_id, $sis_id, $art_id, $article_nr_displ, $amount_back) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка збереження даних!";
        $back_id = $slave->qq($back_id); $si_id = $slave->qq($si_id); $sis_id = $slave->qq($sis_id);
        $bcs_id = 0; $back_clients_summ = 0; $sis_price = 0;
        if ($back_id > 0 && $si_id > 0 && $sis_id > 0) {
            $art_id = $slave->qq($art_id);
            $article_nr_displ = $slave->qq($article_nr_displ);
            $amount_back = $slave->qq($amount_back);
            $bcs_amountEx = 0; $sis_amount = 0;
            $r = $db->query("SELECT `id`, `amount` FROM `J_BACK_CLIENTS_STR` WHERE `back_id`='$back_id' AND `sale_invoice_str_id`='$sis_id' AND `art_id`='$art_id' AND `status`='1' LIMIT 1;");
            $n = $db->num_rows($r);
            if ($n == 1) {
                $bcs_id = $db->result($r, 0, "id");
                $bcs_amountEx = $db->result($r, 0, "amount");
            }
            $r1 = $db->query("SELECT `amount`, `price_end` FROM `J_SALE_INVOICE_STR` WHERE `invoice_id`='$si_id' AND `id`='$sis_id' AND `art_id`='$art_id' AND `status`='1' LIMIT 1;");
            $n1 = $db->num_rows($r1);
            if ($n1 == 1) {
                $sis_amount = $db->result($r1,0,"amount");
                $sis_price = $db->result($r1,0,"price_end");
            }
            $max_back = 0;
            if ($n > 0 && $n1 > 0 && $bcs_amountEx > 0 && $sis_amount > 0) {
                $max_back = $sis_amount - $bcs_amountEx;
            }
            if ($n == 0 && $n1 > 0 && $sis_amount > 0) {
                $max_back = $sis_amount;
            }

            if ($max_back < $amount_back) {
                $answer = 0; $err = "Кількість для повернення ВЖЕ більша за можливу! (максимально: $max_back)";
            }
            if ($max_back > 0 && $max_back >= $amount_back) {
                if ($n == 0) {
                    $brand_id = $this->getBrandIdByArtId($art_id);
                    $r = $db->query("SELECT MAX(`id`) as mid FROM `J_BACK_CLIENTS_STR`;");
                    $bcs_id = 0 + $db->result($r, 0, "mid") + 1;
                    $db->query("INSERT INTO `J_BACK_CLIENTS_STR` (`id`,`back_id`,`sale_invoice_str_id`,`art_id`,`article_nr_displ`,`brand_id`,`price`) 
                    VALUES ('$bcs_id','$back_id','$sis_id','$art_id','$article_nr_displ','$brand_id','$sis_price');");
                }
                if ($bcs_id > 0) {
                    $amount_back_update = $bcs_amountEx + $amount_back;
                    $sis_summ = round($amount_back_update * $sis_price, 2);
                    $db->query("UPDATE `J_BACK_CLIENTS_STR` SET `amount`='$amount_back_update', `summ`='$sis_summ' WHERE `id`='$bcs_id' AND `back_id`='$back_id' AND `sale_invoice_str_id`='$sis_id' LIMIT 1;");

                    $rs = $db->query("SELECT SUM(`summ`) as back_summ FROM `J_BACK_CLIENTS_STR` WHERE `back_id`='$back_id' and `status`='1';");
                    $back_clients_summ = $db->result($rs,0,"back_summ") + 0;
                    $db->query("UPDATE `J_BACK_CLIENTS` SET `summ`='$back_clients_summ' WHERE `id`='$back_id';");
                    $answer = 1; $err = "";
                }
            }
        }
        return array($answer, $err, $back_clients_summ);
    }

    function getClientCashConditions($client_id) { $db = DbSingleton::getDb();
        $cash_id = 0;
        $credit_cash_id = 0;
        $r = $db->query("SELECT `cash_id`, `credit_cash_id` FROM `A_CLIENTS_CONDITIONS` WHERE `client_id`='$client_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $cash_id = $db->result($r, 0, "cash_id");
            $credit_cash_id = $db->result($r, 0, "credit_cash_id");
        }
        return array($cash_id, $credit_cash_id);
    }

    function getClientOrgType($client_id) { $db = DbSingleton::getDb();
        $org_type = 0;
        $r = $db->query("SELECT `org_type` FROM `A_CLIENTS` WHERE `id`='$client_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $org_type = $db->result($r, 0, "org_type");
        }
        return $org_type;
    }

    function getCashName($cash_id) { $db = DbSingleton::getDb();
        $name = "";
        $r = $db->query("SELECT `name` FROM `CASH` WHERE `id`='$cash_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $name = $db->result($r, 0, "name");
        }
        return $name;
    }

    function getBackClientsClientContoCash($client_id) { $db = DbSingleton::getDb();
        $cash_id = 1;
        $answer = 0; $err = "Помилка";
        $r = $db->query("SELECT `cash_id` FROM `A_CLIENTS_CONDITIONS` WHERE `client_id`='$client_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $cash_id = $db->result($r, 0, "cash_id");
            $answer = 1; $err = "";
        }
        return array($answer, $err, $cash_id);
    }

    function getBackClientsClientDocType($client_id) { $db = DbSingleton::getDb();
        $doc_type_id = 64;
        $answer = 0; $err = "Помилка";
        $r = $db->query("SELECT `doc_type_id` FROM `A_CLIENTS_CONDITIONS` WHERE `client_id`='$client_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $doc_type_id = $db->result($r, 0, "doc_type_id");
            $answer = 1; $err = "";
        }
        return array($answer, $err, $doc_type_id);
    }

    function getClientPaymentDelay($client_id) { $db = DbSingleton::getDb();
        $data_pay = date("Y-m-d");
        $answer = 0; $err = "Помилка";
        $r = $db->query("SELECT `payment_delay` FROM `A_CLIENTS_CONDITIONS` WHERE `client_id`='$client_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $payment_delay = $db->result($r, 0, "payment_delay");
            $data_pay = date("Y-m-d",strtotime("+$payment_delay day", strtotime($data_pay)));
            $answer = 1; $err = "";
        }
        return array($answer, $err, $data_pay);
    }

    function changeBackClientsCash($back_id, $cash_id) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка збереження даних!";
        $back_id = $slave->qq($back_id); $cash_id = $slave->qq($cash_id);
        if ($back_id > 0) {
            $r = $db->query("SELECT `oper_status`, `client_conto_id` FROM `J_BACK_CLIENTS` WHERE `id`='$back_id' LIMIT 1;");
            $n = $db->num_rows($r);
            if ($n == 1) {
                $oper_status = $db->result($r, 0, "oper_status");
                if ($oper_status == 30) {
                    $client_conto_id = $db->result($r, 0, "client_conto_id");
                    $org_type = $this->getClientOrgType($client_conto_id);
                    list($client_cash_id,) = $this->getClientCashConditions($client_conto_id);
                    if ($client_cash_id == $cash_id || $org_type == 0 || $org_type == 1) {
                        $db->query("UPDATE `J_BACK_CLIENTS` SET cash_id='$cash_id' WHERE `id`='$back_id';");
                        $this->updateBackClientsPriceCash($back_id);
                        $answer = 1; $err = "";
                    } else {
                        $answer = 0; $err = "Валюта розрахунку клієнта " . $this->getCashName($client_cash_id) . ". Змініть кінцевого платника на того кому дозволено розрахунок у валюті " . $this->getCashName($cash_id);
                    }
                } else {
                    $answer = 0; $err = "Документ заблоковано. Зміни вносити заборонено.";
                }
            }
        }
        return array($answer, $err);
    }

    function updateBackClientsPriceCash($back_id) { $db = DbSingleton::getDb();
        $answer = 0;
        $r = $db->query("SELECT * FROM `J_BACK_CLIENTS` WHERE `id`='$back_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $oper_status = $db->result($r, 0, "oper_status");
            if ($oper_status == 30) {
                $cash_id = $db->result($r, 0, "cash_id");
                list($usd_to_uah, $eur_to_uah) = $this->getKoursData();
                $summ_back = 0;
                $r = $db->query("SELECT * FROM `J_BACK_CLIENTS_STR` WHERE `back_id`='$back_id' ORDER BY `id` ASC;");
                $n = $db->num_rows($r);
                for ($i = 1; $i <= $n; $i++) {
                    $amount = $db->result($r,$i - 1,"amount");
                    $price_end = $db->result($r,$i - 1,"price_end");
                    if ($cash_id == 1) {
                        $price_end = round($price_end * $usd_to_uah, 2);
                    }
                    if ($cash_id == 3) {
                        $price_end = round($price_end * $usd_to_uah / $eur_to_uah, 2);
                    }
                    $summ = $amount * $price_end;
                    $summ_back += $summ;
                }
                $db->query("UPDATE `J_BACK_CLIENTS` SET `summ`='$summ_back' WHERE `id`='$back_id' LIMIT 1;");
                $answer = 1;
            } else {
                $answer = 0;
            }
        }
        return $answer;
    }

    function dropBackClientsStr($back_id, $back_str_id) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка індексу";
        $back_id = $slave->qq($back_id);
        $back_str_id = $slave->qq($back_str_id);
        $back_clients_summ = 0;
        $r = $db->query("SELECT `oper_status`, `status`, `status_back` FROM `J_BACK_CLIENTS` WHERE `id`='$back_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $status = $db->result($r, 0, "status");
            $oper_status = $db->result($r, 0, "oper_status");
            $status_back = $db->result($r, 0, "status_back");
            if ($oper_status == 30 && $status == 1 && $status_back == 102) {
                $r1 = $db->query("SELECT * FROM `J_BACK_CLIENTS_STR` WHERE `id`='$back_str_id' LIMIT 1;");
                $n1 = $db->num_rows($r1);
                if ($n1 == 1) {
                    $db->query("DELETE FROM `J_BACK_CLIENTS_STR` WHERE `id`='$back_str_id' AND `back_id`='$back_id' LIMIT 1;");
                    $back_clients_summ = $this->updateBackClientsSumm($back_id);
                    $answer = 1; $err = "";
                }
            } else {
                $answer = 0; $err = "Видалення заблоковано. Повернення вже прийнято.";
            }
        }
        return array($answer, $err, $back_clients_summ);
    }

    function updateBackClientsSumm($back_id) { $db = DbSingleton::getDb();
        $slave = new slave;
        $sum = 0;
        $r = $db->query("SELECT * FROM `J_BACK_CLIENTS_STR` WHERE `back_id`='$back_id';");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $str_id = $db->result($r,$i - 1,"id");
            $price = $db->result($r,$i - 1,"price");
            $amount = $db->result($r,$i - 1,"amount");
            $summ = round($amount * $price,2);
            $summ_db = $slave->to_money($db->result($r,$i - 1,"summ"));
            if ($summ_db != $summ) {
                $summ_db = $summ;
                $db->query("UPDATE `J_BACK_CLIENTS_STR` SET `summ`='$summ_db' WHERE `id`='$str_id' LIMIT 1;");
            }
            $sum += $summ_db;
        }
        if ($n > 0) {
            $db->query("UPDATE `J_BACK_CLIENTS` SET `summ`='$sum' WHERE `id`='$back_id' AND `oper_status`='30' AND `status`='1';");
        }
        return $sum;
    }

    function makeBackClientsCardFinish() {
        $answer = 0; $err = "";
        return array($answer, $err);
    }

    function getBackClientsCashId($back_id) { $db = DbSingleton::getDb();
        $cash_id = 2;
        $r = $db->query("SELECT `cash_id` FROM `J_BACK_CLIENTS` WHERE `id`='$back_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $cash_id = $db->result($r, 0, "cash_id");
        }
        return $cash_id;
    }

    function getBackClientsClient($back_id) { $db = DbSingleton::getDb();
        $client_conto_id = 0;
        $r = $db->query("SELECT `client_id`, `client_conto_id` FROM `J_BACK_CLIENTS` WHERE `id`='$back_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $client_id = $db->result($r, 0, "client_id");
            $client_conto_id = $db->result($r, 0, "client_conto_id");
            if ($client_conto_id == 0 && $client_id > 0) {
                $client_conto_id = $client_id;
            }
        }
        return $client_conto_id;
    }

    function getArticleInBackClients($art_id, $back_id) { $db = DbSingleton::getDb();
        $r = $db->query("SELECT SUM(`amount`) as amount FROM `J_BACK_CLIENTS_STR` WHERE `art_id`='$art_id' AND `back_id`='$back_id';");
        return 0 + $db->result($r, 0, "amount");
    }

    function getArticleRemoteStorageAmount($art_id, $cur_storage_id) { $db=DbSingleton::getTokoDb();
        $r = $db->query("SELECT SUM(`AMOUNT`) as amount, SUM(`RESERV_AMOUNT`) as reserv FROM `T2_ARTICLES_STRORAGE` WHERE `art_id`='$art_id' AND `STORAGE_ID`!='$cur_storage_id';");
        return 0 + $db->result($r, 0, "amount") - $db->result($r, 0, "reserv");
    }

    function setArticleToSelectAmountBackClients($art_id, $back_id) {
        $form = ""; $form_htm = RD . "/tpl/back_clients_select_amount_article_form.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $form = str_replace("{back_clients_rest_storage_list}",$this->showArticleRestStorageSelectList($art_id,$back_id),$form);
        $form = str_replace("{art_id}",$art_id,$form);
        return $form;
    }

    function showBackClientsArticleAmountChange($art_id, $back_clients_str_id) { $db = DbSingleton::getDb();
        $dp = new dp;
        $form = ""; $form_htm = RD . "/tpl/back_clients_select_amount_article_change_form.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $r = $db->query("SELECT * FROM `J_BACK_CLIENTS_STR` WHERE `id`='$back_clients_str_id' AND `status_back`='93' LIMIT 1");
        $article_nr_displ = $db->result($r, 0, "article_nr_displ");
        $brand_id = $db->result($r, 0, "brand_id");
        $brand_name = $this->getBrandName($brand_id);
        $amount = $db->result($r, 0, "amount");
        $storage_id = $db->result($r, 0, "storage_id_from");
        list($info, $max_moving) = $dp->showArticleRestStorageSelectText($art_id, $storage_id, $amount);
        $form = str_replace("{storage_name}",$this->getStorageName($storage_id),$form);
        $form = str_replace("{amountRestText}",$info,$form);
        $form = str_replace("{max_moving}",$max_moving,$form);
        $form = str_replace("{cur_amount}",$amount,$form);
        $form = str_replace("{back_clients_str_id}",$back_clients_str_id,$form);
        return array($form, $article_nr_displ, $brand_name);
    }

    function getBackClientsTpoint($back_id) { $db = DbSingleton::getDb();
        $tpoint_id = 0;
        $r = $db->query("SELECT `tpoint_id` FROM `J_BACK_CLIENTS` WHERE `id`='$back_id' LIMIT 1");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $tpoint_id = $db->result($r, 0, "tpoint_id");
        }
        return $tpoint_id;
    }

    function getArticleStorageAmountBackClients($art_id, $back_id, $storage_id) { $db = DbSingleton::getDb();
        $amount = 0;
        $r = $db->query("SELECT `amount` FROM `J_BACK_CLIENTS_STR` 
        WHERE `back_id`='$back_id' AND `status_back`='93' AND `storage_id_from`='$storage_id' AND `art_id`='$art_id' LIMIT 1");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $amount = $db->result($r, 0, "amount");
        }
        return $amount;
    }

    function getArticleSupplStorageAmountBackClients($art_id, $back_id, $suppl_id, $suppl_storage_id) { $db = DbSingleton::getDb();
        $amount = 0;
        $r = $db->query("SELECT `amount` FROM `J_BACK_CLIENTS_STR` 
        WHERE `back_id`='$back_id' AND `status_back`='93' AND `suppl_id`='$suppl_id' AND `suppl_storage_id`='$suppl_storage_id' AND `art_id`='$art_id' LIMIT 1");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $amount = $db->result($r, 0, "amount");
        }
        return $amount;
    }

    function showArticleRestStorageSelectList($art_id, $back_id) { $db = DbSingleton::getTokoDb();
        $dp = new dp;
        $tpoint_id = $this->getBackClientsTpoint($back_id);
        $list = "";
        $r = $db->query("SELECT s.id, s.name, t2as.AMOUNT, t2as.RESERV_AMOUNT FROM `STORAGE` s 
            LEFT OUTER JOIN `T2_ARTICLES_STRORAGE` t2as on t2as.STORAGE_ID=s.id 
        WHERE s.status='1' AND t2as.ART_ID='$art_id' ORDER BY s.name ASC, s.id ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r,$i - 1,"id");
            $name = $db->result($r,$i - 1,"name");
            $amount = $db->result($r,$i - 1,"AMOUNT");
            $reserv_amount = $db->result($r,$i - 1,"RESERV_AMOUNT");
            $cur_amount = $this->getArticleStorageAmountBackClients($art_id, $back_id, $id);
            $reserv_amount_rest = $reserv_amount - $cur_amount;
            $delivery_info = $dp->getTpointDeliveryInfo($tpoint_id, $id);
            if ($amount != 0 || $cur_amount != 0 || $reserv_amount_rest != 0) {
                $list .= "<tr onClick=\"showBackClientsArticleAmountWindow('$art_id','$id');\" style='cursor:pointer'>
                    <td>$i <input type='hidden' id='storage_amount_id' value='$id'></td>
                    <td>$name</td>
                    <td>$amount</td>
                    <td>$cur_amount</td>
                    <td>$reserv_amount_rest</td>
                    <td>$delivery_info</td>
                </tr>";
            }
        }
        return $list;
    }

    function showBackClientsAmountInputWindow($art_id, $back_id, $storage_id) {
        $form = ""; $form_htm = RD . "/tpl/back_clients_amount_window.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $form = str_replace("{art_id}",$art_id,$form);
        $amount = $this->getArticleStorageAmountBackClients($art_id, $back_id, $storage_id);
        $form = str_replace("{amount}",$amount,$form);
        return $form;
    }

    function showBackClientsSupplAmountInputWindow($art_id, $article_nr_displ, $brand_id, $back_id, $suppl_id, $suppl_storage_id, $price) {
        $dp = new dp; $cat = new catalogue;
        $form = ""; $form_htm = RD . "/tpl/back_clients_amount_suppl_window.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $form = str_replace("{art_id}",$art_id,$form);
        $amount = $this->getArticleSupplStorageAmountBackClients($art_id, $back_id, $suppl_id, $suppl_storage_id);
        $form = str_replace("{amount}",$amount,$form);
        $form = str_replace("{price}",$price,$form);
        $summ = $amount * $price;
        $form = str_replace("{summ}",$summ,$form);
        $form = str_replace("{article_nr_displ}",$article_nr_displ,$form);
        $form = str_replace("{brand_id}",$brand_id,$form);
        $form = str_replace("{suppl_id}",$suppl_id,$form);
        $suppl_storage_name = $cat->getSupplStorageName($suppl_storage_id);
        $form = str_replace("{suppl_storage_id}",$suppl_storage_id,$form);
        $form = str_replace("{suppl_storage_code}",$suppl_storage_name . " ($suppl_id.$suppl_storage_id)",$form);
        $form = str_replace("{suppl_delivery_info}", $dp->getTpointSupplDeliveryInfo($this->getBackClientsTpoint($back_id), $suppl_id, $suppl_storage_id), $form);
        return $form;
    }

    function showArticleRestStorageCellsList($art_id, $storage_id) { $db = DbSingleton::getTokoDb();
        $list = "<option value='0'>-- Оберіть зі списку --</option>";
        $r = $db->query("SELECT sc.id, sc.cell_value, t2asc.AMOUNT, t2asc.RESERV_AMOUNT, t2as.AMOUNT as AMOUNT_STORAGE, t2as.RESERV_AMOUNT as RESERV_AMOUNT_STORAGE
        FROM `STORAGE_CELLS` sc
            LEFT OUTER JOIN `T2_ARTICLES_STRORAGE_CELLS` t2asc ON ( t2asc.STORAGE_CELLS_ID = sc.id )
            LEFT OUTER JOIN `T2_ARTICLES_STRORAGE` t2as ON ( t2as.STORAGE_ID = sc.storage_id )
        WHERE sc.status='1' AND t2asc.ART_ID='$art_id' AND t2as.ART_ID='$art_id' AND sc.storage_id='$storage_id' ORDER BY sc.cell_value ASC, sc.id ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r,$i - 1,"id");
            $name = $db->result($r,$i - 1,"cell_value");
            $amount = $db->result($r,$i - 1,"AMOUNT");
            $reserv_amount = $db->result($r,$i - 1,"RESERV_AMOUNT");
            $amount_storage = $db->result($r,$i - 1,"AMOUNT_STORAGE");
            $reserv_amount_storage = $db->result($r,$i - 1,"RESERV_AMOUNT_STORAGE");
            if ($amount > $amount_storage) {
                $amount = $amount_storage;
                $reserv_amount = $reserv_amount_storage;
            }
            $max_moving = $amount;
            if ($reserv_amount != 0 || $amount != 0) {
                $list .= "<option value='$id' data-max-mov='$max_moving' data-cellId-mov='0'>$name | Залишок: $amount; Резерв: $reserv_amount; </option>";
            }
        }
        return $list;
    }

    function showStorageCellsList($storage_id, $exclude_id = "") { $db = DbSingleton::getTokoDb();
        $list = "<option value='0'>-- Оберіть зі списку --</option>";
        $r = $db->query("SELECT `id`, `cell_value` FROM `STORAGE_CELLS` WHERE `status`='1' AND `storage_id`='$storage_id' ORDER BY `cell_value` ASC, `id` ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r, $i - 1, "id");
            $name = $db->result($r, $i - 1, "cell_value");
            $checked = ($id == $exclude_id) ? "selected" : "";
            $list .= "<option value='$id' $checked>$name</option>";
        }
        return $list;
    }

    function getArticleName($art_id) { $db = DbSingleton::getTokoDb();
        $name = "";
        $r = $db->query("SELECT `NAME` FROM `T2_NAMES` WHERE `ART_ID`='$art_id' AND `LANG_ID`='16' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $name = $db->result($r, 0, "NAME");
        }
        return $name;
    }

    function getArticleWightVolume($art_id) { $db = DbSingleton::getTokoDb();
        $weight = 0; $volume = 0; $weight2 = 0;
        $r = $db->query("SELECT `VOLUME`, `WEIGHT_BRUTTO`, `WEIGHT_NETTO` FROM `T2_PACKAGING` WHERE `ART_ID`='$art_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $weight = $db->result($r, 0, "WEIGHT_BRUTTO");
            $weight2 = $db->result($r, 0, "WEIGHT_NETTO");
            $volume = $db->result($r, 0, "VOLUME");
        }
        return array($weight, $volume, $weight2);
    }

    function getArticleReservType($tpoint_id, $storage_id) { $db = DbSingleton::getTokoDb();
        $reserv_type_id = 68;
        $r = $db->query("SELECT `local` FROM `T_POINT_STORAGE` WHERE `tpoint_id`='$tpoint_id' AND `status`='1' AND `storage_id`='$storage_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $local = $db->result($r, 0, "local");
            if ($local == 41) {
                $reserv_type_id = 67;
            }
        }
        if ($n == 0) {
            $reserv_type_id = 68;
        }
        return $reserv_type_id;
    }

    function getArticleRestTpoint($art_id, $tpoint_id) { $db = DbSingleton::getTokoDb();
        $stock = 0; $reserv = 0; $storage_id = 0;
        $r = $db->query("SELECT SUM(t2as.`AMOUNT`) as stock, SUM(t2as.`RESERV_AMOUNT`) as reserv, t2as.STORAGE_ID 
        FROM `T2_ARTICLES_STRORAGE` t2as 
            LEFT OUTER JOIN `T_POINT_STORAGE` tps on tps.storage_id=t2as.STORAGE_ID 
        WHERE t2as.ART_ID='$art_id' AND tps.`tpoint_id`='$tpoint_id' AND tps.status='1';");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $stock += $db->result($r,$i - 1,"stock");
            $reserv += $db->result($r,$i - 1,"reserv");
            $storage_id = $db->result($r, 0, "STORAGE_ID");
        }
        return array($stock, $reserv, $storage_id);
    }

    function getArticleRestNotTpoint($art_id, $tpoint_id) { $db = DbSingleton::getTokoDb();
        $stock = 0; $reserv = 0; $storage_id = 0;
        $r = $db->query("SELECT SUM(t2as.`AMOUNT`) as stock, SUM(t2as.`RESERV_AMOUNT`) as reserv, t2as.STORAGE_ID 
        FROM `T2_ARTICLES_STRORAGE` t2as 
            LEFT OUTER JOIN `T_POINT_STORAGE` tps on tps.storage_id=t2as.STORAGE_ID 
        WHERE t2as.ART_ID='$art_id' AND tps.`tpoint_id`!='$tpoint_id' AND tps.status='1';");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $stock += $db->result($r, $i - 1, "stock");
            $reserv += $db->result($r, $i - 1, "reserv");
            $storage_id = $db->result($r, 0, "STORAGE_ID");
        }
        return array($stock, $reserv, $storage_id);
    }

    function getArticleRestStorage($art_id, $storage_id) { $db = DbSingleton::getTokoDb();
        $stock = 0; $reserv = 0;
        if ($storage_id == "") {
            $storage_id = 0;
        }
        $r = $db->query("SELECT SUM(`AMOUNT`) as stock, SUM(`RESERV_AMOUNT`) as reserv FROM `T2_ARTICLES_STRORAGE` 
        WHERE `ART_ID`='$art_id' AND `STORAGE_ID`='$storage_id';");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $stock += $db->result($r, $i - 1, "stock");
            $reserv += $db->result($r, $i - 1, "reserv");
        }
        return array($stock, $reserv);
    }

    function getArticleRestStorageCell($art_id, $storage_id, $cell_id) { $db = DbSingleton::getTokoDb();
        $stock = 0; $reserv = 0;
        if ($storage_id == "") {
            $storage_id = 0;
        }
        if ($cell_id == "") {
            $cell_id = 0;
        }
        $r = $db->query("SELECT `AMOUNT` as stock, `RESERV_AMOUNT` as reserv FROM `T2_ARTICLES_STRORAGE_CELLS` 
        WHERE `ART_ID`='$art_id' AND `STORAGE_ID`='$storage_id' AND `STORAGE_CELLS_ID`='$cell_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $stock = $db->result($r, 0, "stock");
            $reserv = $db->result($r, 0, "reserv");
        }
        return array($stock, $reserv);
    }

    function loadback_clientsStorage($back_id) { $db = DbSingleton::getDb();
        $form = ""; $form_htm = RD . "/tpl/back_clients_storage_form.htm";
        if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r = $db->query("SELECT `storage_id`, `storage_cells_id` FROM `J_INCOME` WHERE `id`='$back_id' LIMIT 1;");
        $storage_id = $db->result($r, 0, "storage_id");
        $storage_cells_id = $db->result($r, 0, "storage_cells_id");
        $form = str_replace("{back_id}",$back_id,$form);
        $form = str_replace("{storage_list}",$this->showStorageSelectList($storage_id),$form);
        $form = str_replace("{storage_cells_list}",$this->showStorageCellsSelectList($storage_id, $storage_cells_id),$form);
        return $form;
    }

    function getStorageName($sel_id) { $db = DbSingleton::getTokoDb();
        $name = "";
        $r = $db->query("SELECT `name` FROM `STORAGE` WHERE `status`='1' AND `id`='$sel_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $name = $db->result($r, 0, "name");
        }
        return $name;
    }

    function getCellName($sel_id) { $db = DbSingleton::getTokoDb();
        $name = "";
        $r = $db->query("SELECT `cell_value` FROM `STORAGE_CELLS` WHERE `status`='1' AND `id`='$sel_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $name = $db->result($r, 0, "cell_value");
        }
        return $name;
    }

    function setBackClientsTpointStorage($back_id, $tpoint_id, $storage_id) { $db = DbSingleton::getDb();
        $db->query("UPDATE `J_BACK_CLIENTS` SET `tpoint_id`='$tpoint_id', `storage_id`='$storage_id' WHERE `id`='$back_id' LIMIT 1;");
        return "";
    }

    function setBackClientsStorageCell($back_id, $cell_id) { $db = DbSingleton::getDb();
        $db->query("UPDATE `J_BACK_CLIENTS` SET `cell_id`='$cell_id' WHERE `id`='$back_id' LIMIT 1;");
        return "";
    }

    function showStorageSelectListByTpoint($tpoint_id, $sel_id) { $db = DbSingleton::getTokoDb();
        $list = "<option value=0>Оберіть зі списку</option>";
        $r = $db->query("SELECT s.`id`, s.`name` FROM `STORAGE` s 
            LEFT OUTER JOIN `T_POINT_STORAGE` t on t.storage_id=s.id 
        WHERE s.status='1' AND t.status=1 AND (t.local=41 OR t.local=42) AND t.tpoint_id='$tpoint_id' ORDER BY s.name, s.id ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r, $i - 1, "id");
            $name = $db->result($r, $i - 1, "name");
            $sel = ($sel_id == $id) ? "selected='selected'" : "";
            $list .= "<option value='$id' $sel>$name</option>";
        }
        return $list;
    }

    function showStorageSelectList($sel_id, $cells_only = 0) { $db = DbSingleton::getTokoDb();
        $list = "<option value=0>Оберіть зі списку</option>";
        $query = "SELECT `id`, `name` FROM `STORAGE` WHERE `status`='1' ORDER BY `name`, `id` ASC;";
        if ($cells_only == 1) {
            $query = "SELECT s.`id`, s.`name` FROM `STORAGE` s 
                INNER JOIN `STORAGE_STR` ss on ss.storage_id=s.id 
            WHERE s.status='1' GROUP BY ss.storage_id ORDER BY s.name, s.id ASC;";
        }
        $r = $db->query($query);
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r,$i - 1,"id");
            $name = $db->result($r,$i - 1,"name");
            $sel = ($sel_id == $id) ? "selected='selected'" : "";
            $list .= "<option value='$id' $sel>$name</option>";
        }
        return $list;
    }

    function showStorageCellsSelectList($storage_id, $sel_id) { $db = DbSingleton::getTokoDb();
        $list = "<option value=0>Оберіть зі списку</option>";
        $cells_show = 1;
        $r = $db->query("SELECT `id`, `cell_value` FROM `STORAGE_CELLS` WHERE `status`='1' AND `storage_id`='$storage_id' ORDER BY `cell_value`, `id` ASC;");
        $n = $db->num_rows($r);
        if ($n == 0) { $cells_show = 0; }
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r,$i - 1,"id");
            $cell_value = $db->result($r,$i - 1,"cell_value");
            $sel = ($sel_id == $id) ? "selected='selected'" : "";
            $list .= "<option value='$id' $sel>$cell_value</option>";
        }
        return array($list, $cells_show);
    }

    function getCashAbr($sel_id) { $db = DbSingleton::getDb();
        $name = "грн";
        $r = $db->query("SELECT `abr` FROM `CASH` WHERE `id`='$sel_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $name = $db->result($r, 0, "abr");
        }
        return $name;
    }

    function loadBackClientsCommets($back_id) { $db = DbSingleton::getDb();
        $list = "";
        $form = ""; $form_htm = RD . "/tpl/back_clients_comment_block.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $r = $db->query("SELECT cc.*, u.name 
        FROM `J_BACK_CLIENTS_COMMENTS` cc 
            LEFT OUTER JOIN `media_users` u on u.id=cc.USER_ID 
        WHERE cc.back_id='$back_id' ORDER BY `id` DESC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r,$i - 1,"id");
            $user_id = $db->result($r,$i - 1,"user_id");
            $user_name = $db->result($r,$i - 1,"name");
            $data = $db->result($r,$i - 1,"data");
            $comment = $db->result($r,$i - 1,"comment");
            $block = $form;
            $block = str_replace("{back_id}",$back_id,$block);
            $block = str_replace("{id}",$id,$block);
            $block = str_replace("{user_id}",$user_id,$block);
            $block = str_replace("{user_name}",$user_name,$block);
            $block = str_replace("{data}",$data,$block);
            $block = str_replace("{comment}",$comment,$block);
            $list .= $block;
        }
        if ($n == 0) {
            $list = "<h3 class='text-center'>Коментарі відсутні</h3>";
        }
        return $list;
    }

    function saveBackClientsComment($back_id, $comment) { $db = DbSingleton::getDb();
        $slave = new slave;
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $answer = 0; $err = "Помилка збереження даних!";
        $back_id = $slave->qq($back_id);
        $comment = $slave->qq($comment);
        if ($back_id > 0 && $comment != "") {
            $db->query("INSERT INTO `J_BACK_CLIENTS_COMMENTS` (`back_id`,`user_id`,`comment`) VALUES ('$back_id','$user_id','$comment');");
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

    function dropBackClientsComment($back_id, $comment_id) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка видалення запису!";
        $back_id = $slave->qq($back_id);
        $comment_id = $slave->qq($comment_id);
        if ($back_id > 0 && $comment_id > 0) {
            $r = $db->query("SELECT * FROM `J_BACK_CLIENTS_COMMENTS` WHERE `back_id`='$back_id' AND `id`='$comment_id' LIMIT 1;");
            $n = $db->num_rows($r);
            if ($n == 1) {
                $db->query("DELETE FROM `J_BACK_CLIENTS_COMMENTS` WHERE `back_id`='$back_id' AND `id`='$comment_id';");
                $answer = 1; $err = "";
            }
        }
        return array($answer, $err);
    }

    function loadBackClientsCDN($back_id) { $db = DbSingleton::getDb();
        $list = "";
        $form = ""; $form_htm = RD . "/tpl/back_clients_cdn_block.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $r = $db->query("SELECT cc.*, u.name as user_name 
        FROM `J_BACK_CLIENTS_CDN` cc 
            LEFT OUTER JOIN `media_users` u on u.id=cc.USER_ID 
        WHERE cc.back_id='$back_id' AND cc.status='1' ORDER BY cc.file_name ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $file_id = $db->result($r,$i - 1,"id");
            $file_name = $db->result($r,$i - 1,"file_name");
            $name = $db->result($r,$i - 1,"name");
            $data = $db->result($r,$i - 1,"data");
            $user_name = $db->result($r,$i - 1,"user_name");
            $link = "http://portal.myparts.pro/cdn/back_clients_files/$back_id/$file_name";
            $file_view = "<div class=\"icon\"><i class=\"fa fa-file\"></i></div>";
            $exten = pathinfo($file_name, PATHINFO_EXTENSION);
            if ($exten == "jpg" || $exten == "jpeg" || $exten == "png" || $exten == "gif" || $exten == "bmp" || $exten == "svg") {
                $file_view = "<div class=\"image\"><img alt=\"image\" class=\"img-responsive\" src=\"$link\"></div>";
            }
            $block = $form;
            $block = str_replace("{file_id}",$file_id,$block);
            $block = str_replace("{file_name}",$name,$block);
            $block = str_replace("{user_name}",$user_name,$block);
            $block = str_replace("{data}",$data,$block);
            $block = str_replace("{back_id}",$back_id,$block);
            $block = str_replace("{link}",$link,$block);
            $block = str_replace("{file_view}",$file_view,$block);
            $list .= $block;
        }
        if ($n == 0) {
            $list = "<h3 class='text-center'>Файли відсутні</h3>";
        }
        return $list;
    }

    function BackClientsCDNDropFile($back_id, $file_id) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка видалення файлу!";
        $back_id = $slave->qq($back_id);
        $file_id = $slave->qq($file_id);
        if ($back_id > 0 && $file_id > 0) {
            $r = $db->query("SELECT `FILE_NAME` FROM `J_BACK_CLIENTS_CDN` WHERE `back_id`='$back_id' AND `id`='$file_id' LIMIT 1;");
            $n = $db->num_rows($r);
            if ($n == 1) {
                $file_name = $db->result($r, 0, "FILE_NAME");
                unlink(RD . "/cdn/back_clients_files/$back_id/$file_name");
                $db->query("DELETE FROM `J_BACK_CLIENTS_CDN` WHERE `back_id`='$back_id' AND `id`='$file_id';");
                $answer = 1; $err = "";
            }
        }
        return array($answer, $err);
    }

    /*======================================================================================*/

    function updateStockFromStorage($art_id, $storage_id_from, $cell_id_from, $cell_use, $amount) { $dbt = DbSingleton::getTokoDb();
        $r = $dbt->query("SELECT `RESERV_AMOUNT` FROM `T2_ARTICLES_STRORAGE` WHERE `ART_ID`='$art_id' AND `STORAGE_ID`='$storage_id_from' LIMIT 1;");
        $n = $dbt->num_rows($r);
        $er = 1;
        if ($n == 1) {
            $t2s_reserv_amount = $dbt->result($r, 0, "RESERV_AMOUNT");
            if ($amount <= $t2s_reserv_amount) {
                $t2s_reserv_amount = $t2s_reserv_amount - $amount;
                $dbt->query("UPDATE `T2_ARTICLES_STRORAGE` SET `RESERV_AMOUNT`='$t2s_reserv_amount' WHERE `ART_ID`='$art_id' AND `STORAGE_ID`='$storage_id_from' LIMIT 1;");
                if ($cell_use == 1) {
                    $r1 = $dbt->query("SELECT `RESERV_AMOUNT` FROM `T2_ARTICLES_STRORAGE_CELLS` WHERE `ART_ID`='$art_id' AND `STORAGE_ID`='$storage_id_from' AND `STORAGE_CELLS_ID`='$cell_id_from' LIMIT 1;");
                    $n1 = $dbt->num_rows($r1);
                    if ($n1 == 1) {
                        $t2sc_reserv_amount = $dbt->result($r1, 0, "RESERV_AMOUNT");
                        if ($amount > 0) {
                            $t2sc_reserv_amount = $t2sc_reserv_amount - $amount;
                            $dbt->query("UPDATE `T2_ARTICLES_STRORAGE_CELLS` SET `RESERV_AMOUNT`='$t2sc_reserv_amount' WHERE `ART_ID`='$art_id' AND `STORAGE_ID`='$storage_id_from' AND `STORAGE_CELLS_ID`='$cell_id_from' LIMIT 1;");
                        }
                    }
                }
            }
            $er = 0;
        }
        return $er;
    }

    function updateStockToStorage($art_id, $storage_id_to, $cell_id_to, $cell_use, $amount) { $dbt = DbSingleton::getTokoDb();
        $r = $dbt->query("SELECT `AMOUNT` FROM `T2_ARTICLES_STRORAGE` WHERE `ART_ID`='$art_id' AND `STORAGE_ID`='$storage_id_to' LIMIT 1;");
        $n = $dbt->num_rows($r);
        $er = 1;
        if ($n == 0) {
            $dbt->query("INSERT INTO `T2_ARTICLES_STRORAGE` (`ART_ID`,`AMOUNT`,`RESERV_AMOUNT`,`STORAGE_ID`) VALUES ('$art_id','$amount','0','$storage_id_to');");
            if ($cell_use == 1) {
                $dbt->query("INSERT INTO `T2_ARTICLES_STRORAGE_CELLS` (`ART_ID`,`AMOUNT`,`RESERV_AMOUNT`,`STORAGE_ID`,`STORAGE_CELLS_ID`) VALUES ('$art_id','$amount','0','$storage_id_to','$cell_id_to');");
            }
            $er = 0;
        }
        if ($n == 1) {
            $t2s_amount = $dbt->result($r, 0, "AMOUNT");
            if ($amount > 0) {
                $t2s_amount = $t2s_amount + $amount;
                $dbt->query("UPDATE `T2_ARTICLES_STRORAGE` SET `AMOUNT`='$t2s_amount' WHERE `ART_ID`='$art_id' AND `STORAGE_ID`='$storage_id_to' LIMIT 1;");
                if ($cell_use == 1) {
                    $r1 = $dbt->query("SELECT `AMOUNT` FROM `T2_ARTICLES_STRORAGE_CELLS` WHERE `ART_ID`='$art_id' AND `STORAGE_ID`='$storage_id_to' AND `STORAGE_CELLS_ID`='$cell_id_to' LIMIT 1;");
                    $n1 = $dbt->num_rows($r1);
                    if ($n1 == 0) {
                        $dbt->query("INSERT INTO `T2_ARTICLES_STRORAGE_CELLS` (`ART_ID`,`AMOUNT`,`RESERV_AMOUNT`,`STORAGE_ID`,`STORAGE_CELLS_ID`) VALUES ('$art_id','$amount','0','$storage_id_to','$cell_id_to');");
                    }
                    if ($n1 == 1) {
                        $t2sc_amount = $dbt->result($r1, 0, "AMOUNT");
                        if ($amount > 0) {
                            $t2sc_amount = $t2sc_amount + $amount;
                            $dbt->query("UPDATE `T2_ARTICLES_STRORAGE_CELLS` SET `AMOUNT`='$t2sc_amount' WHERE `ART_ID`='$art_id' AND `STORAGE_ID`='$storage_id_to' AND `STORAGE_CELLS_ID`='$cell_id_to' LIMIT 1;");
                        }
                    }
                }
                $er = 0;
            }
        }
        return $er;
    }

    function updateStockFromStorageLocal($art_id, $storage_id_from, $cell_id_from, $cell_id_to, $amount) { $dbt = DbSingleton::getTokoDb();
        $r = $dbt->query("SELECT `AMOUNT`, `RESERV_AMOUNT` FROM `T2_ARTICLES_STRORAGE` WHERE `ART_ID`='$art_id' AND `STORAGE_ID`='$storage_id_from' LIMIT 1;");
        $n = $dbt->num_rows($r);
        $er = 1;
        if ($n == 1) {
            $t2s_amount = $dbt->result($r, 0, "AMOUNT");
            $t2s_reserv_amount = $dbt->result($r, 0, "RESERV_AMOUNT");
            if ($amount <= $t2s_reserv_amount) {
                $t2s_reserv_amount = $t2s_reserv_amount - $amount;
                $t2s_amount = $t2s_amount + $amount;
                $dbt->query("UPDATE `T2_ARTICLES_STRORAGE` SET `RESERV_AMOUNT`='$t2s_reserv_amount',`AMOUNT`='$t2s_amount' WHERE `ART_ID`='$art_id' AND `STORAGE_ID`='$storage_id_from' LIMIT 1;");
                $r1 = $dbt->query("SELECT `RESERV_AMOUNT` FROM `T2_ARTICLES_STRORAGE_CELLS` WHERE `ART_ID`='$art_id' AND `STORAGE_ID`='$storage_id_from' AND `STORAGE_CELLS_ID`='$cell_id_from' LIMIT 1;");
                $n1 = $dbt->num_rows($r1);
                if ($n1 == 1) {
                    $t2sc_reserv_amount = $dbt->result($r1,0,"RESERV_AMOUNT");
                    if ($amount > 0) {
                        $t2sc_reserv_amount = $t2sc_reserv_amount - $amount;
                        $dbt->query("UPDATE `T2_ARTICLES_STRORAGE_CELLS` SET `RESERV_AMOUNT`='$t2sc_reserv_amount' WHERE `ART_ID`='$art_id' AND `STORAGE_ID`='$storage_id_from' AND `STORAGE_CELLS_ID`='$cell_id_from' LIMIT 1;");
                    }
                }
                $r2 = $dbt->query("SELECT `AMOUNT` FROM `T2_ARTICLES_STRORAGE_CELLS` WHERE `ART_ID`='$art_id' AND `STORAGE_ID`='$storage_id_from' AND `STORAGE_CELLS_ID`='$cell_id_to' LIMIT 1;");
                $n2 = $dbt->num_rows($r2);
                if ($n2 == 0) {
                    $dbt->query("INSERT INTO `T2_ARTICLES_STRORAGE_CELLS` (`ART_ID`,`AMOUNT`,`RESERV_AMOUNT`,`STORAGE_ID`,`STORAGE_CELLS_ID`) VALUES ('$art_id','$amount','0','$storage_id_from','$cell_id_to');");
                }
                if ($n2 == 1) {
                    $t2sc_amount2 = $dbt->result($r2,0,"AMOUNT");
                    if ($amount > 0) {
                        $t2sc_amount2 = $t2sc_amount2 + $amount;
                        $dbt->query("UPDATE `T2_ARTICLES_STRORAGE_CELLS` SET `AMOUNT`='$t2sc_amount2' WHERE `ART_ID`='$art_id' AND `STORAGE_ID`='$storage_id_from' AND `STORAGE_CELLS_ID`='$cell_id_to' LIMIT 1;");
                    }
                }
            }
            $er = 0;
        }
        return $er;
    }

    function getStorageDefaultCell($storage_id) { $db = DbSingleton::getDb();
        $cell_use = 0; $cell_id = 0;
        $r = $db->query("SELECT `id` FROM `STORAGE_CELLS` WHERE `storage_id`='$storage_id' AND `status`='1' AND `default`='1' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $cell_use = 1;
            $cell_id = $db->result($r, 0, "id");
        }
        if ($n == 0) {
            $r = $db->query("SELECT `id` FROM `STORAGE_CELLS` WHERE `storage_id`='$storage_id' AND `status`='1' ORDER BY `id` ASC LIMIT 1;");
            $n = $db->num_rows($r);
            if ($n == 1) {
                $cell_use = 1;
                $cell_id = $db->result($r, 0, "id");
            }
        }
        return array($cell_use, $cell_id);
    }

    function getSaleInvoiceDocType($id) { $db = DbSingleton::getDb();
        $r = $db->query("SELECT `doc_type_id` FROM `J_SALE_INVOICE` WHERE `id`='$id' LIMIT 1;");
        $n = $db->num_rows($r);
        $doc_type_id = 0;
        if ($n == 1) {
            $doc_type_id = $db->result($r, 0, "doc_type_id");
        }
        return $doc_type_id;
    }

    function getSellerId($id) { $db = DbSingleton::getDb();
        $r = $db->query("SELECT `seller_id` FROM `J_SALE_INVOICE` WHERE `id`='$id' LIMIT 1;");
        $n = $db->num_rows($r);
        $seller_id = 0;
        if ($n == 1) {
            $seller_id = $db->result($r, 0, "seller_id");
        }
        return $seller_id;
    }

    function acceptBackClients($back_id) { $db = DbSingleton::getDb(); $dbt = DbSingleton::getTokoDb();
        $jpay = new jpay; $slave = new slave; $cat = new catalogue; $dp = new dp;
        $answer = 0; $err = "Помилка обробки даних!";
        $back_id = $slave->qq($back_id);
        $art_id = 0; $income_id = 0;
        $data_now = date("Y-m-d");
        if ($back_id > 0) {
            $r = $db->query("SELECT * FROM `J_BACK_CLIENTS` WHERE `id` = '$back_id' AND `status` = '1' LIMIT 1;");
            $n = $db->num_rows($r);
            if ($n == 1) {
                $prefix = $db->result($r, 0, "prefix");
                $status_back = $db->result($r, 0, "status_back");
                $summ_back = $db->result($r, 0, "summ");
                $sale_invoice_id = $db->result($r, 0, "sale_invoice_id");
                $storage_id_back = $db->result($r, 0, "storage_id");
                $cell_id_back = $db->result($r, 0, "cell_id");
                $client_id = $db->result($r, 0, "client_id");
                $doc_type_id = $db->result($r, 0, "doc_type_id");
                $seller_id = $this->getSellerId($sale_invoice_id);

                if ($summ_back > 0 && $status_back == 102) {

                    $new_prefix = $dp->getSellerPrefixDocNom($seller_id, $doc_type_id)[0];

                    $db->query("UPDATE `J_BACK_CLIENTS` SET `status_back`='103', `prefix`='$prefix".$new_prefix."', `data`='$data_now' 
                    WHERE `id`='$back_id' AND `status`='1' AND `status_back`=102 LIMIT 1;");

                    // возвращаем финансы
                    list(, $summ_debit) = $this->getSaleInvoiceSumm2($sale_invoice_id);
                    $summ_avans = $summ_debit - $summ_back;

                    list($balans_before, $balans_before_cash_id) = $jpay->getClientGeneralSaldo($client_id);
                    if ($summ_avans > 0) {
                        // делаем меньше задолженность по накладной
                        // делаем меньше задолженость по балансу
                        // не трогаем авансы
                        $db->query("UPDATE `J_SALE_INVOICE` SET `summ_debit`=`summ_debit`-$summ_back WHERE `id`='$sale_invoice_id' LIMIT 1;");

                        $balans_after = $balans_before + $summ_back;

                        $db->query("INSERT INTO `B_CLIENT_BALANS_JOURNAL` (`client_id`, `cash_id`, `balans_before`, `deb_kre`, `summ`, `balans_after`, `doc_type_id`, `doc_id`, `pay_cash_id`, `pay_summ`) 
                        VALUES ('$client_id', '$balans_before_cash_id', '$balans_before', '2', '".abs($summ_back)."', '$balans_after', '5', '$back_id', '$balans_before_cash_id', '$summ_back');");

                        $db->query("UPDATE `B_CLIENT_BALANS` SET `saldo`=saldo+$summ_back, `last_update`=NOW() WHERE `client_id`='$client_id';");
                    }
                    if ($summ_avans <= 0) {
                        // делаем задолженность по накладной = 0
                        // делаем меньше задолженость по балансу
                        // создаем аванс
                        $db->query("UPDATE `J_SALE_INVOICE` SET `summ_debit`=0 WHERE `id`='$sale_invoice_id' LIMIT 1;");

                        $balans_after = $balans_before + $summ_back;
                        $db->query("INSERT INTO `B_CLIENT_BALANS_JOURNAL` (`client_id`, `cash_id`, `balans_before`, `deb_kre`, `summ`, `balans_after`, `doc_type_id`, `doc_id`, `pay_cash_id`, `pay_summ`) 
                        VALUES ('$client_id', '$balans_before_cash_id', '$balans_before', '2', '".abs($summ_back)."', '$balans_after', '5', '$back_id', '$balans_before_cash_id', '$summ_back');");

                        $db->query("UPDATE `B_CLIENT_BALANS` SET `saldo`=`saldo`+$summ_back, `last_update`=NOW() WHERE `client_id`='$client_id';");
                        $jpay->updateClientAvans($client_id, $balans_before_cash_id, abs($summ_avans));
                    }

                    // возвращаем товар на склад физически
                    if ($cell_id_back > 0) {
                        $cell_use = 1;
                    } else {
                        list($cell_use, $cell_id_back) = $this->getStorageDefaultCell($storage_id_back);
                    }

                    $r1 = $db->query("SELECT * FROM `J_BACK_CLIENTS_STR` WHERE `back_id`='$back_id' ORDER BY `id` ASC;");
                    $n1 = $db->num_rows($r1);
                    for ($i1 = 1; $i1 <= $n1; $i1++) {
                        $back_str_id = $db->result($r1, $i1 - 1, "id");
                        $art_id_back = $db->result($r1, $i1 - 1, "art_id");
                        $amount_back = $db->result($r1, $i1 - 1, "amount");
                        $back_si_str_id = $db->result($r1, $i1 - 1, "sale_invoice_str_id");

                        // RETURN TO STORAGE
                        $rs = $dbt->query("SELECT `AMOUNT` FROM `T2_ARTICLES_STRORAGE` WHERE `ART_ID`='$art_id_back' AND `STORAGE_ID`='$storage_id_back' LIMIT 1;");
                        $ns = $dbt->num_rows($rs);
                        if ($ns == 1) {
                            $dbt->query("UPDATE `T2_ARTICLES_STRORAGE` SET `AMOUNT`=`AMOUNT`+$amount_back WHERE `ART_ID`='$art_id_back' AND `STORAGE_ID`='$storage_id_back' LIMIT 1");
                            if ($cell_use == 1) {
                                $rsc = $dbt->query("SELECT `AMOUNT` FROM `T2_ARTICLES_STRORAGE_CELLS` WHERE `ART_ID`='$art_id_back' AND `STORAGE_ID`='$storage_id_back' AND `STORAGE_CELLS_ID`='$cell_id_back' LIMIT 1;");
                                $nsc = $dbt->num_rows($rsc);
                                if ($nsc == 1) {
                                    $dbt->query("UPDATE `T2_ARTICLES_STRORAGE_CELLS` SET `AMOUNT`=`AMOUNT`+$amount_back WHERE `ART_ID`='$art_id_back' AND `STORAGE_ID`='$storage_id_back' AND `STORAGE_CELLS_ID`='$cell_id_back' LIMIT 1");
                                }
                                if ($nsc == 0) {
                                    $dbt->query("INSERT INTO `T2_ARTICLES_STRORAGE_CELLS` (`ART_ID`,`AMOUNT`,`RESERV_AMOUNT`,`STORAGE_ID`,`STORAGE_CELLS_ID`) VALUES ('$art_id_back','$amount_back','0','$storage_id_back','$cell_id_back');");
                                }
                            }
                        }
                        if ($ns == 0) {
                            $dbt->query("INSERT INTO `T2_ARTICLES_STRORAGE` (`ART_ID`,`AMOUNT`,`RESERV_AMOUNT`,`STORAGE_ID`) VALUES ('$art_id_back','$amount_back','0','$storage_id_back');");
                            if ($cell_use == 1) {
                                $rsc = $dbt->query("SELECT `AMOUNT` FROM `T2_ARTICLES_STRORAGE_CELLS` WHERE `ART_ID`='$art_id_back' AND `STORAGE_ID`='$storage_id_back' AND `STORAGE_CELLS_ID`='$cell_id_back' LIMIT 1;");
                                $nsc = $dbt->num_rows($rsc);
                                if ($nsc == 1) {
                                    $dbt->query("UPDATE `T2_ARTICLES_STRORAGE_CELLS` SET `AMOUNT`=`AMOUNT`+$amount_back WHERE `ART_ID`='$art_id_back' AND `STORAGE_ID`='$storage_id_back' AND `STORAGE_CELLS_ID`='$cell_id_back' LIMIT 1");
                                }
                                if ($nsc == 0) {
                                    $dbt->query("INSERT INTO `T2_ARTICLES_STRORAGE_CELLS` (`ART_ID`,`AMOUNT`,`RESERV_AMOUNT`,`STORAGE_ID`,`STORAGE_CELLS_ID`) 
                                    VALUES ('$art_id_back','$amount_back','0','$storage_id_back','$cell_id_back');");
                                }
                            }
                        }
                        $dbt->query("UPDATE `T2_ARTICLES_PRICE_STOCK` SET `GENERAL_STOCK`=`GENERAL_STOCK`+$amount_back WHERE `ART_ID`='$art_id_back' LIMIT 1;");

                        $slave->addJuornalArtDocs(4, $back_id, $art_id_back, $amount_back);

                        // Возвращаем товар в партии
                        $rp = $db->query("SELECT * FROM `J_SALE_INVOICE_PARTITION_STR` 
                        WHERE `invoice_id` = '$sale_invoice_id' AND `invoice_str_id` = '$back_si_str_id' AND `art_id` = '$art_id_back' 
                        ORDER BY `id` DESC;");
                        $np = $db->num_rows($rp);
                        if ($np > 0) {
                            $amount_back_partition = $amount_back;
                            for ($ip = 1; $ip <= $np; $ip++) {
                                if ($amount_back_partition > 0) {
                                    $op = 0;
                                    $partition_str_id = $db->result($rp, $ip - 1, "id");
                                    $pratition_article_nr_displ = $db->result($rp, $ip - 1, "article_nr_displ");
                                    $pratition_brand_id = $db->result($rp, $ip - 1, "brand_id");
                                    $partition_id = $db->result($rp, $ip - 1, "partition_id");
                                    $partition_amount = $db->result($rp, $ip - 1, "partition_amount");
                                    $oper_price_partition = $db->result($rp, $ip - 1, "oper_price_partition");
                                    $price_partition = $db->result($rp, $ip - 1, "price_partition");
                                    $price_buh_uah = $db->result($rp, $ip - 1, "price_buh_uah");
                                    $price_man_uah = $db->result($rp, $ip - 1, "price_man_uah");
                                    $price_invoice = $db->result($rp, $ip - 1, "price_invoice");

                                    $ri = $db->query("SELECT `parrent_doc_id` FROM `T2_ARTICLES_PARTITIONS` WHERE `id`='$partition_id' LIMIT 1;");
                                    $ni = $db->num_rows($ri);
                                    if ($ni == 1) {
                                        $income_id = $db->result($ri, 0, "parrent_doc_id");
                                    }

                                    if ($amount_back_partition > $partition_amount) {
                                        $db->query("UPDATE `T2_ARTICLES_PARTITIONS` SET `rest`=`rest`+$partition_amount WHERE `id`='$partition_id' LIMIT 1;");
                                        $amount_back_partition -= $partition_amount;
                                        $op = 1;
                                        $db->query("UPDATE `J_SALE_INVOICE_PARTITION_STR` SET `partition_amount`=`partition_amount`-$partition_amount WHERE `id`='$partition_str_id';");
                                        list($oper_price, $general_stock) = $cat->getArticleOperPriceGeneralStock($art_id_back);
                                        $price_man_usd = $this->getArticlePriceManUsd($art_id_back, $income_id);
                                        $new_oper_price = round((($oper_price * $general_stock) + ($partition_amount * $price_man_usd)) / ($partition_amount + $general_stock), 2);
                                        $new_general_stock = $partition_amount + $general_stock;
                                        $cat->setArticleOperPriceGeneralStock($art_id, $new_oper_price, $new_general_stock);
                                        if ($amount_back_partition > 0 && $partition_amount > 0) {
                                            $db->query("INSERT INTO `J_BACK_CLIENTS_PARTITION_STR` (`partition_id`,`back_id`,`back_str_id`,`art_id`,`article_nr_displ`,`brand_id`,`partition_amount`,`oper_price_partition`,`price_partition`,`price_buh_uah`,`price_man_uah`,`price_invoice`,`status`) 
                                            VALUES ($partition_id,$back_id,$back_str_id,$art_id_back,'$pratition_article_nr_displ',$pratition_brand_id,$partition_amount,$oper_price_partition,$price_partition,$price_buh_uah,$price_man_uah,$price_invoice,1);");
                                        }
                                    }

                                    if ($amount_back_partition <= $partition_amount && $op == 0) {
                                        $db->query("UPDATE `T2_ARTICLES_PARTITIONS` SET `rest`=`rest`+$amount_back_partition WHERE `id`='$partition_id' LIMIT 1;");
                                        $db->query("UPDATE `J_SALE_INVOICE_PARTITION_STR` SET `partition_amount`=`partition_amount`-$amount_back_partition WHERE `id`='$partition_str_id';");
                                        list($oper_price, $general_stock) = $cat->getArticleOperPriceGeneralStock($art_id_back);
                                        $price_man_usd = $this->getArticlePriceManUsd($art_id_back, $income_id);
                                        $new_oper_price = round((($oper_price * $general_stock) + ($amount_back_partition * $price_man_usd)) / ($amount_back_partition + $general_stock), 2);
                                        $new_general_stock = $amount_back_partition + $general_stock;
                                        $cat->setArticleOperPriceGeneralStock($art_id, $new_oper_price, $new_general_stock);
                                        if ($amount_back_partition > 0) {
                                            $db->query("INSERT INTO `J_BACK_CLIENTS_PARTITION_STR` (`partition_id`,`back_id`,`back_str_id`,`art_id`,`article_nr_displ`,`brand_id`,`partition_amount`,`oper_price_partition`,`price_partition`,`price_buh_uah`,`price_man_uah`,`price_invoice`,`status`) 
                                            VALUES ($partition_id,$back_id,$back_str_id,$art_id_back,'$pratition_article_nr_displ',$pratition_brand_id,$amount_back_partition,$oper_price_partition,$price_partition,$price_buh_uah,$price_man_uah,$price_invoice,1);");
                                        }
                                        $amount_back_partition -= $partition_amount;
                                    }
                                }
                            }
                        }
                    }
                    $this->createBackClientsTax($back_id);
                    $this->addBackClientCron($back_id);
                    $answer = 1; $err = "";
                }
            }
        }
        return array($answer, $err);
    }

    function getArticlePriceManUsd($art_id, $income_id) { $db = DbSingleton::getDb();
        $r = $db->query("SELECT `price_man_usd` FROM `J_INCOME_STR` WHERE `art_id`='$art_id' AND `income_id`='$income_id' LIMIT 1;");
        $n = $db->num_rows($r);
        $price_man_usd = 0;
        if ($n == 1) {
            $price_man_usd = $db->result($r, 0, "price_man_usd");
        }
        return $price_man_usd;
    }

    function findTaxBySaleInvoiceId($sale_invoice_id) { $db = DbSingleton::getDb();
        // добавив умову на tax_type_id=160, т.к. має брати документ-основу
        $r = $db->query("SELECT `id`, `seller_id`, `client_id`, `tpoint_id` 
        FROM `J_TAX_INVOICE` 
        WHERE `sale_invoice_id` = '$sale_invoice_id' AND `tax_type_id` = '160' 
        ORDER BY `id` DESC LIMIT 1;");
        $tax_id = $db->result($r, 0, "id");
        $seller_id = $db->result($r, 0, "seller_id");
        $client_id = $db->result($r, 0, "client_id");
        $tpoint_id = $db->result($r, 0, "tpoint_id");
        return array($tax_id, $seller_id, $client_id, $tpoint_id);
    }

    function createBackClientsTax($back_id) { $db = DbSingleton::getDb();
        $cat = new catalogue;
        $slave = new slave;
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $answer = 0; $err = "Помилка";
        $tax_id = 0;
        $r = $db->query("SELECT j.*, sl.full_name as seller_name, sld.edrpou, sld.account, sld.vat, sld.bank, sld.mfo, ot.name as org_type_abr, ch.abr2 as cash_abr, si.doc_type_id as sale_doc_type_id 
        FROM `J_BACK_CLIENTS` j
            LEFT OUTER JOIN `J_SALE_INVOICE` si on si.id=j.sale_invoice_id
            LEFT OUTER JOIN `A_CLIENTS` sl on sl.id=si.seller_id
            LEFT OUTER JOIN `A_CLIENT_DETAILS` sld on (sld.client_id=si.seller_id AND sld.main=1)
            LEFT OUTER JOIN `A_ORG_TYPE` ot on ot.id=sl.org_type
            LEFT OUTER JOIN `CASH` ch on ch.id=j.cash_id				
        WHERE j.id='$back_id' AND j.status_back=103 AND j.status=1 LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $sale_doc_type_id = $db->result($r, 0, "sale_doc_type_id");
            if ($sale_doc_type_id == 61) {
                $sale_invoice_id = $db->result($r, 0, "sale_invoice_id");
                $cash_id = $db->result($r, 0, "cash_id");
                $data = $db->result($r, 0, "data");
                $summ = $db->result($r, 0, "summ");
                $summ *= -1;
                $tax_type_id = 161;
                list($tax_to_back_id, $seller_id, $client_id, $tpoint_id) = $this->findTaxBySaleInvoiceId($sale_invoice_id);

                $r1 = $db->query("SELECT MAX(`id`) as mid FROM `J_TAX_INVOICE`;");
                $tax_id = $db->result($r1, 0, "mid") + 1;
                $year = date("Y");
                $r1 = $db->query("SELECT MAX(`doc_nom`) as doc_nom FROM `J_TAX_INVOICE` WHERE `seller_id`='$seller_id' AND `tax_type_id`='$tax_type_id' AND `data_create`>='$year-01-01';");
                $doc_nom = $db->result($r1, 0, "doc_nom") + 1;

                $db->query("INSERT INTO `J_TAX_INVOICE` (`id`, `tax_type_id`, `doc_nom`, `tax_to_back_id`, `sale_invoice_id`, `back_id`, `tpoint_id`, `seller_id`, `client_id`, `data_create`, `cash_id`, `user_id`, `status_tax`, `summ`, `status`) 
                VALUES ('$tax_id', '$tax_type_id', '$doc_nom', '$tax_to_back_id', '$sale_invoice_id', '$back_id', '$tpoint_id', '$seller_id', '$client_id', '$data', '$cash_id', '$user_id', 155, '$summ', 1);");

                $r = $db->query("SELECT * FROM `J_BACK_CLIENTS_STR` WHERE `back_id` = '$back_id' ORDER BY `id` ASC;");
                $n = $db->num_rows($r);
                for ($i = 1; $i <= $n; $i++) {
                    $sale_invoice_str_id = $db->result($r,$i - 1,"sale_invoice_str_id");
                    $art_id = $db->result($r,$i - 1,"art_id");
                    $zed = $cat->getArticleZED($art_id);
                    $article_name = $cat->getArticleNameLang($art_id);
                    $amount = intval($db->result($r,$i - 1,"amount"));
                    $amount *= -1;
                    $price = $slave->to_money($db->result($r,$i - 1,"price"));
                    $summ = $slave->to_money($db->result($r,$i - 1,"summ"));
                    $summ *= -1;
                    $tax_nom = $this->getTaxInvoiceStrNom($tax_to_back_id, $art_id);
                    $db->query("INSERT INTO `J_TAX_INVOICE_STR` (`tax_id`, `zed`, `art_id`, `goods_name`, `amount`, `price`, `summ`, `tax_str_id`, `tax_str_nom`, `sale_invoice_str_id`) 
                    VALUES ('$tax_id', '$zed', '$art_id', '$article_name', '$amount', '$price', '$summ', '$sale_invoice_str_id', '$tax_nom', '0');");
                }
                $answer = 1; $err = "";
            }
        }
        return array($answer, $err, $tax_id);
    }

    function getTaxInvoiceStrNom($tax_id, $art_id) { $db = DbSingleton::getDb();
        $r = $db->query("SELECT `tax_str_nom` FROM `J_TAX_INVOICE_STR` WHERE `tax_id`='$tax_id' AND `art_id`='$art_id' LIMIT 1;");
        return $db->result($r, 0, "tax_str_nom");
    }

    /*===============			Print BACKCLIENTS 		==================================*/

    function printBackClientsN1($back_id) { $db = DbSingleton::getDb();
        $slave = new slave; $gmanual = new gmanual; $money = new toMoney; $cat = new catalogue;
        $form = $form_htm = $list = "";
        $r = $db->query("SELECT j.*, sl.full_name as seller_name, sld.edrpou, sld.account, sld.vytjag, sld.bank, sld.mfo, ot.name as org_type_abr, ch.abr2 as cash_abr 
        FROM `J_BACK_CLIENTS` j
            LEFT OUTER JOIN `J_SALE_INVOICE` si on si.id=j.sale_invoice_id
            LEFT OUTER JOIN `A_CLIENTS` sl on sl.id=si.seller_id
            LEFT OUTER JOIN `A_CLIENT_DETAILS` sld on (sld.client_id=si.seller_id AND sld.main=1)
            LEFT OUTER JOIN `A_ORG_TYPE` ot on ot.id=sl.org_type
            LEFT OUTER JOIN `CASH` ch on ch.id=j.cash_id        
        WHERE j.id = '$back_id' AND j.status_back = 103 AND j.status = 1 LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 0) {
            $form_htm = RD . "/tpl/access_deny.htm";
            if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        }
        if ($n == 1) {
            $sale_invoice_id = $db->result($r, 0, "sale_invoice_id");
            $doc_type_id = $this->getSaleInvoiceDocType($sale_invoice_id);
            $form = "";
            if ($doc_type_id == 61) $form_htm = RD . "/tpl/back_clients_print_n1.htm"; // БН
            if ($doc_type_id == 63) $form_htm = RD . "/tpl/back_clients_print_n2.htm"; // ТЧ
            if ($doc_type_id == 64) $form_htm = RD . "/tpl/back_clients_print_n3.htm"; // БК
            if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
            $prefix = $db->result($r, 0, "prefix");
            $doc_nom = $db->result($r, 0, "doc_nom");
            $data = $db->result($r, 0, "data");
            if ($data == "0000-00-00") {
                $data = date("d.m.Y");
            }
            $data_format = date("d.m.Y", strtotime($data));
            $cash_id = $db->result($r, 0, "cash_id");
            $tpoint_id = $db->result($r, 0, "tpoint_id");
            $client_id = $db->result($r, 0, "client_id");
            $storage_id = $db->result($r, 0, "storage_id"); ;
            $summ = $db->result($r, 0, "summ");
            $status_back = $db->result($r, 0, "status_back");
            $seller_name = $db->result($r, 0, "seller_name");
            $edrpou = $db->result($r, 0, "edrpou");
            $org_type_abr = $db->result($r, 0, "org_type_abr");
            $cash_abr = $db->result($r, 0, "cash_abr");
            $account = $db->result($r, 0, "account");
            $bank = $db->result($r, 0, "bank");
            $mfo = $db->result($r, 0, "mfo");
            $vat = $db->result($r, 0, "vytjag");

            $form = str_replace("{curtime}",date("d.m.Y H:i:s"),$form);
            $form = str_replace("{seller_name}",$seller_name,$form);
            $form = str_replace("{edrpou}",$edrpou,$form);
            $form = str_replace("{org_type_abr}",$org_type_abr,$form);
            $form = str_replace("{back_id}",$back_id,$form);
            $form = str_replace("{prefix}",$prefix,$form);
            $form = str_replace("{doc_nom}",$doc_nom,$form);
            $form = str_replace("{data}",$data_format,$form);
            $form = str_replace("{cash_name}", $this->getCashAbr($cash_id), $form);
            $form = str_replace("{back_summ}",$summ,$form);
            $form = str_replace("{back_summ_word}",$money->num2str($summ),$form);
            $form = str_replace("{sale_invoice_name}", $this->getSaleInvoiceName($sale_invoice_id), $form);
            $form = str_replace("{storage_name}", $this->getStorageName($storage_id), $form);
            $form = str_replace("{tpoint_name}", $this->getTpointName($tpoint_id), $form);
            $form = str_replace("{client_id}",$client_id,$form);
            $form = str_replace("{client_name}", $this->getClientName($client_id), $form);
            $form = str_replace("{status_back_cap}",$gmanual->get_gmanual_caption($status_back),$form);
            $form = str_replace("{cash_abr}",$cash_abr,$form);
            $form = str_replace("{rr}",$account,$form);
            $form = str_replace("{bank}",$bank,$form);
            $form = str_replace("{mfo}",$mfo,$form);
            $form = str_replace("{ipn_nom}",$vat,$form);
            $vat_summ = $summ / 6;
            $form = str_replace("{vat_summ}", round($vat_summ, 2), $form);
            $form = str_replace("{invoice_summ_word}", $money->num2str($summ), $form);

            $r = $db->query("SELECT * FROM `J_BACK_CLIENTS_STR` WHERE `back_id` = '$back_id' ORDER BY `id` ASC;");
            $n = $db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $art_id = $db->result($r,$i - 1,"art_id");
                $article_nr_displ = $db->result($r,$i - 1,"article_nr_displ");
                $brand_id = $db->result($r,$i - 1,"brand_id");
                $amount = intval($db->result($r,$i - 1,"amount"));
                $price = $slave->to_money($db->result($r,$i - 1,"price"));
                $summ = $slave->to_money($db->result($r,$i - 1,"summ"));
                $article_name = $cat->getArticleNameLang($art_id);
                $brand_name = $cat->getBrandName($brand_id);
                $unit = $this->getUnitArticle($art_id);
                $list .= "<tr>
                    <td align='center'>$i</td>
                    <td align='left'>$article_nr_displ ($brand_name)</td>
                    <td align='left'>$article_name</td>
                    <td align='center'>$unit</td>
                    <td align='center'>$amount</td>
                    <td align='right'>$price</td>
                    <td align='right'>$summ</td>
                </tr>";
            }
            $form = str_replace("{list}", $list, $form);
            $mp = new media_print;
            $mp->print_document($form, "A4-L");
        }
        return $form;
    }

    function getUnitArticle($art_id) { $db = DbSingleton::getTokoDb();
        $abr = "";
        $r = $db->query("SELECT t2u.abr 
        FROM `T2_PACKAGING` t2p 
            LEFT JOIN `units` t2u on t2u.id=t2p.UNITS_ID
        WHERE t2p.ART_ID='$art_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $abr = $db->result($r, 0, "abr");
        }
        return $abr;
    }

    function loadBackClientsPartition($back_id) { $db = DbSingleton::getDb();
        $cat = new catalogue; $income = new income;
        $list = ""; $doc_name = 0; $prev_doc_id = 0;
        $form = ""; $form_htm = RD . "/tpl/back_clients_partitions_list.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $r = $db->query("SELECT ps.*, ap.parrent_type_id, ap.parrent_doc_id 
        FROM `J_BACK_CLIENTS_PARTITION_STR` ps
            LEFT OUTER JOIN `T2_ARTICLES_PARTITIONS` ap on (ap.id=ps.partition_id)
        WHERE ps.status=1 AND ps.back_id='$back_id' 
        ORDER BY ps.id ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            if ($i == 1) {
                $doc_name = "";
            }
            $parrent_type_id = $db->result($r, $i - 1, "parrent_type_id");
            $parrent_doc_id = $db->result($r, $i - 1, "parrent_doc_id");
            $article_nr_displ = $db->result($r, $i - 1, "article_nr_displ");
            $brand_id = $db->result($r, $i - 1, "brand_id");
            $partition_amount = $db->result($r, $i - 1, "partition_amount");
            $oper_price_partition = $db->result($r, $i - 1, "oper_price_partition");
            $price_partition = $db->result($r, $i - 1, "price_partition");
            $price_buh_uah = $db->result($r, $i - 1, "price_buh_uah");
            $price_man_uah = $db->result($r, $i - 1, "price_man_uah");
            $price_invoice = $db->result($r, $i - 1, "price_invoice");
            $brand_name = $cat->getBrandName($brand_id);
            if ($parrent_type_id == 1) {
                if ($parrent_doc_id != $prev_doc_id) {
                    $doc_name = "" . $income->getIncomeDocNom($parrent_doc_id);
                    $prev_doc_id = $parrent_doc_id;
                }
            }
            $list .= "<tr id='strStsRow_$i'>
                <td align='center'>$i</td>
                <td align='center'>$doc_name</td>
                <td align='center'>$article_nr_displ</td>
                <td align='center'>$brand_name</td>
                <td align='right'>$partition_amount</td>
                <td align='right'>$price_partition</td>
                <td align='right'>$oper_price_partition</td>
                <td align='right'>$price_buh_uah</td>
                <td align='right'>$price_man_uah</td>
                <td align='right'>$price_invoice</td>
            </tr>";
        }
        if ($n == 0) {
            $list = "<tr><td colspan=11 align='center'>Записи відсутні</td></tr>";
        }
        $form = str_replace("{partitions_list}", $list, $form);
        $form = str_replace("{back_id}", $back_id, $form);
        return $form;
    }

    function exportBackClientsExcel($back_id, $separator = "") { $db = DbSingleton::getDb();
        $cat = new catalogue;
        $invoice_summ = 0;
        $r = $db->query("SELECT sv.*, sl.name as seller_name, cl.name as client_name
        FROM `J_BACK_CLIENTS` sv
            LEFT OUTER JOIN `A_CLIENTS` sl on sl.id=sv.seller_id
            LEFT OUTER JOIN `A_CLIENTS` cl on cl.id=sv.client_id
        WHERE sv.status = 1 AND sv.id = '$back_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $prefix = $db->result($r, 0, "prefix");
            $doc_nom = $db->result($r, 0, "doc_nom");
            $data_create = $db->result($r, 0, "data");
            $seller_name = $db->result($r, 0, "seller_name");
            $client_name = $db->result($r, 0, "client_name");
            $list = array();
            $r = $db->query("SELECT * FROM `J_BACK_CLIENTS_STR` WHERE `back_id` = '$back_id' ORDER BY `id` ASC;");
            $n = $db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $art_id = $db->result($r,$i - 1,"art_id");
                $article_nr_displ = $db->result($r,$i - 1,"article_nr_displ");
                $article_name = $cat->getArticleNameLang($art_id);
                $brand_id = $db->result($r,$i - 1,"brand_id");
                $brand_name = $cat->getBrandName($brand_id);
                $amount = $db->result($r,$i - 1,"amount");
                $price_end = $db->result($r,$i - 1,"price");
                $summ = $db->result($r,$i - 1,"summ");
                $invoice_summ += $summ;
                if ($separator == "comma") {
                    $price_format = str_replace(".", ",", $price_end);
                    $summ_format = str_replace(".", ",", $summ);
                } else {
                    $price_format = $price_end;
                    $summ_format = $summ;
                }
                array_push($list,"$i;$article_nr_displ;$brand_name;$article_name;$amount;$price_format;$summ_format\n");
            }

            $filename = "$client_name" . "_" . "$prefix-$doc_nom" . "_" . "$data_create";
            $filename = str_replace(str_split('"«»'), '', $filename);
            $filename = str_replace("'", "", $filename);
            $filename = str_replace(str_split(" .,/"), "_", $filename);

            $header = "№п/п;Індекс;Бренд;Найменування;К-сть;Ціна;Сума\n";
            header('Content-Type: text/csv; charset=utf-8');
            header("Content-Disposition: attachment; filename=$filename.csv");
            $output = fopen('php://output', 'w');
            $nakladna = "Накладна на повернення №$prefix-$doc_nom-$client_name від $data_create\n";
            fputs($output, $nakladna);
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

    function getBackClientCron($back_id) { $db = DbSingleton::getDb();
        $r = $db->query("SELECT `email`, `status` FROM `cron_client_invoice` WHERE `doc_id` = '$back_id' AND `doc_type` = 2;");
        $n = $db->num_rows($r);
        $list = "";
        for ($i = 1; $i <= $n; $i++) {
            $email = $db->result($r, $i - 1, "email");
            $status = $db->result($r, $i - 1, "status");
            $status_cap = ($status == 1) ? "(Відправлено)" : "(Очікує на відправку)";
            $list .= "$email $status_cap<br>";
        }
        if ($n == 0) {
            $list = "У користувачів не має доступу на відправку";
        }
        return $list;
    }

    function createBackClientExcel($back_id, $separator, $name, $email, $mail_id) { $db = DbSingleton::getDb();
        $slave = new slave; $cat = new catalogue;
        $invoice_summ = 0;
        $filename = $doc_name = $data_doc = "";
        $r = $db->query("SELECT sv.*, sl.name as seller_name, cl.name as client_name
        FROM `J_BACK_CLIENTS` sv
            LEFT OUTER JOIN `A_CLIENTS` sl ON (sl.id = sv.seller_id)
            LEFT OUTER JOIN `A_CLIENTS` cl ON (cl.id = sv.client_id)
        WHERE sv.status = 1 AND sv.id = '$back_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $prefix = $db->result($r, 0, "prefix");
            $doc_nom = $db->result($r, 0, "doc_nom");
            $data_create = $db->result($r, 0, "data");
            $seller_name = $db->result($r, 0, "seller_name");
            $client_name = $db->result($r, 0, "client_name");
            $doc_name = "$prefix-$doc_nom";
            $data_doc = "$data_create";
            $list = array();
            $r = $db->query("SELECT * FROM `J_BACK_CLIENTS_STR` WHERE `back_id` = '$back_id' ORDER BY `id` ASC;");
            $n = $db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $art_id = $db->result($r, $i - 1, "art_id");
                $article_nr_displ = $db->result($r, $i - 1, "article_nr_displ");
                $article_name = $cat->getArticleNameLang($art_id);
                $brand_id = $db->result($r, $i - 1, "brand_id");
                $brand_name = $cat->getBrandName($brand_id);
                $amount = $db->result($r, $i - 1, "amount");
                $price_end = $db->result($r, $i - 1, "price");
                $summ = $db->result($r, $i - 1, "summ");
                $invoice_summ += $summ;
                if ($separator == 2) { // comma
                    $price_format = str_replace(".", ",", $price_end);
                    $summ_format = str_replace(".", ",", $summ);
                } else { // point
                    $price_format = $price_end;
                    $summ_format = $summ;
                }
                array_push($list, "$i;$article_nr_displ;$brand_name;$article_name;$amount;$price_format;$summ_format\n");
            }

            $filename = "$prefix-$doc_nom" . "-vid-" . "$data_create";
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

        $this->sendMailBack($email, $name, $filename, $doc_name, $data_doc, $mail_id);

        return $filename;
    }

    function sendBackClientsMail($back_id, $user_id) { $db = DbSingleton::getDb();
        $answer = 0; $err = "Error!"; $n = 0;
        if ($back_id > 0 && $user_id > 0) {
            $r = $db->query("SELECT `email`, `name`, `client_id`, `invoice_status` FROM `A_CLIENTS_USERS` WHERE `id` = '$user_id';");
            $n = $db->num_rows($r);
            $err = "";
            $email = $db->result($r, 0, "email");
            $name = $db->result($r, 0, "name");
            $client_id = $db->result($r, 0, "client_id");
            $invoice_status = $db->result($r, 0, "invoice_status");
            if ($invoice_status > 0) {
                $rt = $db->query("SELECT MAX(`id`) as mid FROM `A_CLIENTS_INVOICE_MAIL_HISTORY`;");
                $mail_id = 0 + $db->result($rt,0,"mid") + 1;
                $db->query("INSERT INTO `A_CLIENTS_INVOICE_MAIL_HISTORY` (`id`, `client_id`, `user_id`, `email`, `doc_type`) 
                VALUES ('$mail_id', '$client_id', '$user_id', '$email', '2');");
                $this->createBackClientExcel($back_id, $invoice_status, $name, $email, $mail_id);
                $answer = 1;
                $err = "Sent to $email";
            }
        }
        if ($n == 0) {
            $answer = 0; $err = "Not access!";
        }
        return array($answer, $err);
    }

    function addBackClientCron($back_id) { $db = DbSingleton::getDb();
        $r = $db->query("SELECT `client_id` FROM `J_BACK_CLIENTS` WHERE `id` = '$back_id';");
        $n = $db->num_rows($r);
        $emails = "";
        if ($n > 0) {
            $client_id = $db->result($r, 0, "client_id");
            $r = $db->query("SELECT `id`, `email`, `invoice_status` FROM `A_CLIENTS_USERS` WHERE `client_id` = '$client_id';");
            $n = $db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $user_id = $db->result($r, $i - 1, "id");
                $email = $db->result($r, $i - 1, "email");
                $invoice_status = $db->result($r, $i - 1, "invoice_status");
                if ($invoice_status > 0) {
                    $db->query("INSERT INTO `cron_client_invoice` (`doc_type`, `doc_id`, `client_id`, `user_id`, `email`) VALUES ('2', '$back_id', '$client_id', '$user_id', '$email');");
                }
                $emails .= "$email ";
            }
        }
        return $emails;
    }

    function sendMailBack($receiver, $user_name, $filename, $doc_name, $data_doc, $mail_id) { $db = DbSingleton::getDb();
        $path = "https://portal.myparts.pro/uploads/emails/$filename";
        $list = "<p>Доброго дня, $user_name</p>
        <p>У доданому файлі знаходиться накладна на повернення $doc_name від $data_doc</p>
        <p>З повагою ТОКО ГРУП.</p><br>
        <small>ТОКО ГРУП ТОВ, ІПН:403029222256, ЄДРПОУ:40302920</small>";
        $mail = new PHPMailer();
        try {
            $mail->isMail();
            $mail->addReplyTo('noreply@toko.ua', 'TOKO GROUP');
            $mail->addAddress($receiver);
            $mail->Subject = "Возвратная накладная компании `TOKO GROUP`";
            $mail->msgHTML($list);
            $mail->addStringAttachment(file_get_contents($path), "$filename");
            $mail->action_function = 'callbackAction';
            $mail->send();
            $db->query("UPDATE `A_CLIENTS_INVOICE_MAIL_HISTORY` SET `status`='1', `filename`='$filename' WHERE `id`='$mail_id';");
        } catch (Exception $e) { }
        return true;
    }

    /*=================================BUH BACK=================================*/

    function show_buh_back_clients_list() { $db = DbSingleton::getDb();
        $gmanual = new gmanual;
        $summ_uah = 0;
        $list = "";
        $form = ""; $form_htm = RD . "/tpl/buh_back_range.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $data_cur = date("Y-m-d");
        $data_old = date('Y-m-d', strtotime('-7 day', strtotime($data_cur)));
        $where_date = " AND j.data>='$data_old 00:00:00' AND j.data<='$data_cur 23:59:59'";
        $r = $db->query("SELECT j.*, t.name as tpoint_name, CASH.name as cash_name, c.name as client_name, si.prefix as sale_prefix, si.doc_nom as sale_doc_nom 
        FROM `J_BACK_CLIENTS` j
            LEFT OUTER JOIN `T_POINT` t on t.id=j.tpoint_id
            LEFT OUTER JOIN `A_CLIENTS` c on c.id=j.client_id
            LEFT OUTER JOIN `CASH` on CASH.id=j.cash_id
            LEFT OUTER JOIN `J_SALE_INVOICE` si on si.id=j.sale_invoice_id
        WHERE j.status = 1 $where_date AND si.doc_type_id = 61 
        ORDER BY j.id DESC LIMIT 0,500;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r,$i - 1,"id");
            $prefix = $db->result($r,$i - 1,"prefix");
            $doc_nom = $db->result($r,$i - 1,"doc_nom");
            $client_name = $db->result($r,$i - 1,"client_name");
            $cash_name = $db->result($r,$i - 1,"cash_name");
            $summ = $db->result($r,$i - 1,"summ");
            $sale_nom = $db->result($r,$i - 1,"sale_prefix") . "-" . $db->result($r,$i - 1,"sale_doc_nom");
            $data = $db->result($r,$i - 1,"data");
            $user_name = $this->getMediaUserName($db->result($r,$i - 1,"user_id"));
            $status_back = $db->result($r,$i - 1,"status_back");
            $status_back_name = $gmanual->get_gmanual_caption($status_back);
            $summ_pdv = round($summ / 6, 2);
            $function = "showBackClientsCard(\"$id\")";
            $summ_uah += $summ;
            $list .= "<tr style='cursor:pointer' onClick='$function'>
                <td>$prefix - $doc_nom</td>
                <td align='center'>$data</td>
                <td>$client_name</td>
                <td>$cash_name</td>
                <td>$summ</td>
                <td>$summ_pdv</td>
                <td>$sale_nom</td>
                <td>$user_name</td>
                <td>$status_back_name</td>
            </tr>";
        }
        $form = str_replace("{buh_back_range}", $list, $form);
        $form = str_replace("{buh_back_summ}", "$summ_uah UAH", $form);
        return $form;
    }

    function show_buh_back_clients_list_filter($data_start, $data_end) { $db = DbSingleton::getDb();
        $gmanual = new gmanual;
        $list = "";
        $data_cur = date("Y-m-d");
        $summ_uah = 0;
        if ($data_start != "" && $data_end != "") {
            $where_date = "AND j.data>='$data_start 00:00:00' AND j.data<='$data_end 23:59:59'";
        } else {
            $where_date = " AND j.data>='$data_cur 00:00:00' AND j.data<='$data_cur 23:59:59'";
        }
        $r = $db->query("SELECT j.*, t.name as tpoint_name, CASH.name as cash_name, c.name as client_name, si.prefix as sale_prefix, si.doc_nom as sale_doc_nom 
        FROM `J_BACK_CLIENTS` j
            LEFT OUTER JOIN `T_POINT` t on t.id=j.tpoint_id
            LEFT OUTER JOIN `A_CLIENTS` c on c.id=j.client_id
            LEFT OUTER JOIN `CASH` on CASH.id=j.cash_id
            LEFT OUTER JOIN `J_SALE_INVOICE` si on si.id=j.sale_invoice_id
        WHERE j.status = 1 $where_date AND si.doc_type_id = 61 
        ORDER BY j.id DESC LIMIT 0,500;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r,$i - 1,"id");
            $prefix = $db->result($r,$i - 1,"prefix");
            $doc_nom = $db->result($r,$i - 1,"doc_nom");
            $client_name = $db->result($r,$i - 1,"client_name");
            $cash_name = $db->result($r,$i - 1,"cash_name");
            $summ = $db->result($r,$i - 1,"summ");
            $sale_nom = $db->result($r,$i - 1,"sale_prefix") . "-" . $db->result($r,$i - 1,"sale_doc_nom");
            $data = $db->result($r,$i - 1,"data");
            $user_name = $this->getMediaUserName($db->result($r,$i - 1,"user_id"));
            $status_back = $db->result($r,$i - 1,"status_back");
            $status_back_name = $gmanual->get_gmanual_caption($status_back);
            $summ_pdv = round($summ / 6, 2);
            $function = "showBackClientsCard(\"$id\")";
            $summ_uah += $summ;
            $list .= "<tr style='cursor:pointer' onClick='$function'>
                <td>$prefix - $doc_nom</td>
                <td align='center'>$data</td>
                <td>$client_name</td>
                <td>$cash_name</td>
                <td>$summ</td>
                <td>$summ_pdv</td>
                <td>$sale_nom</td>
                <td>$user_name</td>
                <td>$status_back_name</td>
            </tr>";
        }
        return array($list, "$summ_uah UAH");
    }

    /*============================== IMPORT ==============================*/

    function loadBackImport() {
        $form = ""; $form_htm = RD . "/tpl/back_import_str_form.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $header = ""; $header_htm = RD . "/tpl/dp_import_str_header.htm";
        if (file_exists("$header_htm")) { $header = file_get_contents($header_htm); }
        list(, $csv_file_name, $pre_table) = $this->showCsvPreview();
        $table = $this->showTablePreview();
        $form = str_replace("{ibox_header}", ($table == "") ? $header : "", $form);
        $form = str_replace("{records_list}", "<tr><td colspan=10>Записи не завантажено</td></tr>", $form);
        $form = str_replace("{import_file_name}", $csv_file_name, $form);
        $form = str_replace("{csv_str_file}",($table == "") ? $pre_table : "", $form);
        $form = str_replace("{table_str_file}", $table, $form);
        return $form;
    }

    function showCsvPreview() { $db = DbSingleton::getDb();
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $csv_exist = 0;
        $csv_file_name = "Оберіть файл";
        $pre_table = "<h3>Файл не знайдено</h3>";
        $kol_cols = $fn = 0;
        $r = $db->query("SELECT `file_name` FROM `J_BACK_CSV` WHERE `user_id`='$user_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $file_name = $db->result($r, 0, "file_name");
            $file_path = RD . "/cdn/back_files/csv/$user_id/$file_name";
            if (file_exists($file_path)) {
                $form = ""; $form_htm = RD . "/tpl/back_import_str_file.htm";
                if (file_exists("$form_htm")) { $form = file_get_contents($form_htm);}
                $cols_list = "";
                $records_list = "";
                $handle = @fopen($file_path, "r");
                if ($handle) {
                    set_time_limit(0);
                    $max_cols = 0;
                    while (($buffer = fgets($handle, 4096)) !== false) {
                        $fn += 1;
                        $buf = explode(";", $buffer);
                        if ($buffer != "") {
                            if ($fn == 1) {
                                $kol_cols = count($buf);
                            }
                            $buf = str_replace("'", "\'", $buf);
                            $buf = str_replace('"', '\"', $buf);
                            $row = "";
                            $ex_cols = 0;
                            if ($max_cols < $kol_cols) {
                                $ex_cols = 1;
                                $cols_list = "";
                            }
                            for ($i = 1; $i <= $kol_cols; $i++) {
                                if ($i == 1) {
                                    $row = "<td>$fn</td>";
                                }
                                $row .= "<td>" . trim($buf[$i - 1]) . "</td>";
                                if ($ex_cols == 1) {
                                    $sel1 = $sel2 = $sel3 = "";
                                    if ($i == 1) $sel1 = "selected";
                                    if ($i == 2) $sel2 = "selected";
                                    if ($i == 3) $sel3 = "selected";
                                    $cols_list .= "<th><select id=\"clm-$i\" size='1'>
                                        <option value='0'>-</option>
                                        <option value='1' $sel1>Індекс</option>
                                        <option value='2' $sel2>Бренд</option>
                                        <option value='3' $sel3>Кількість</option>
                                    </select></th>";
                                }
                            }
                            if ($row != "") {
                                $records_list .= "<tr>$row</tr>";
                            }
                        }
                        if ($fn == 30) {
                            break;
                        }
                    }
                    fclose($handle);
                }
                $form = str_replace("{user_id}", $user_id, $form);
                $form = str_replace("{cols_list}", $cols_list, $form);
                $form = str_replace("{records_list}", $records_list, $form);
                $form = str_replace("{kol_cols}", $kol_cols, $form);
                $csv_file_name = $file_name;
                $csv_exist = 1;
                $pre_table = $form;
            }
        }
        return array($csv_exist, $csv_file_name, $pre_table);
    }

    function showTablePreview() {
        $dp = new dp();
        $form = ""; $form_htm = RD . "/tpl/back_import_str_csv.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $form = str_replace("{csv_brands}", $this->getCsvIndexBrands(), $form);
        $form = str_replace("{storage_title}", $dp->showStorageFieldsTitle(), $form);
        $table = $this->loadTablePreview();
        $form = str_replace("{records_list}", $table, $form);
        $form = str_replace("{clients_list}", $this->getClientsList(), $form);
        $form = str_replace("{storages_list}", $this->getStoragesList(), $form);
        if ($table == "") {
            $form = "";
        }
        return $form;
    }

    function loadTablePreview($brands = 0) { $db = DbSingleton::getDb(); $dbt = DbSingleton::getTokoDb();
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $table = "";
        $where_arts = "";
        $db->query("UPDATE `J_BACK_IMPORT` SET `selected` = 0 WHERE `user_id` = '$user_id';");
        if ($brands != 0) {
            $arts = [];
            $r = $db->query("SELECT `article_nr_displ` FROM `J_BACK_IMPORT` WHERE `user_id` = '$user_id' AND `status` = 0;");
            $n = $db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $article_nr_displ = $db->result($r, $i - 1, "article_nr_displ");
                array_push($arts,"'" . $article_nr_displ . "'");
            }
            $arts = array_unique($arts);
            $arts = implode(",", $arts);

            if ($arts != "") {
                $new_arts = [];
                $r = $dbt->query("SELECT `DISPLAY_NR`, `SEARCH_NUMBER` FROM `T2_CROSS` WHERE (`DISPLAY_NR` IN ($arts) OR `SEARCH_NUMBER` IN ($arts)) AND `BRAND_ID` = '$brands';");
                $n = $db->num_rows($r);
                for ($i = 1; $i <= $n; $i++) {
                    $article_nr_displ = $db->result($r, $i - 1, "DISPLAY_NR");
                    $article_nr_search = $db->result($r, $i - 1, "SEARCH_NUMBER");
                    array_push($new_arts,"'" . $article_nr_displ . "'");
                    array_push($new_arts,"'" . $article_nr_search . "'");
                }
                $new_arts = array_unique($new_arts);
                $where_arts = "AND `article_nr_displ` IN (" . implode(",", $new_arts) . ")";
            }
        }

        $mas = [];
        $r = $db->query("SELECT * FROM `J_BACK_IMPORT` WHERE `user_id` = '$user_id' $where_arts ORDER BY `status` ASC, `id` ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r, $i - 1, "id");
            $article_nr_displ = $db->result($r, $i - 1, "article_nr_displ");
            $amount = $db->result($r, $i - 1, "amount");
            $art_id = $db->result($r, $i - 1, "art_id");
            $brand_id = $db->result($r, $i - 1, "brand_id");
            $status = $db->result($r, $i - 1, "status");
            $brand_name = $this->getBrandName($brand_id);
            $mas[$i] = [
                "id" => $id,
                "art_id" => $art_id,
                "article_nr_displ" => $article_nr_displ,
                "brand_id" => $brand_id,
                "brand_name" => $brand_name,
                "amount" => $amount,
                "status" => $status,
            ];
        }

        usort($mas, "sortStyle");
        $i = 0;
        foreach ($mas as $val) {
            $i++;
            $status = $val["status"];
            $style = ($status) ? "style='background:lightgreen;'" : "style='background:pink;'";
            $id = $val["id"];
            $article_nr_displ = $val["article_nr_displ"];
            $amount = $val["amount"];
            $art_id = $val["art_id"];
            ($art_id != 0) ?: $art_id = "Невідомо";
            $brand_id = $val["brand_id"];
            $brand_name = $val["brand_name"];
            ($brand_name != "") ?: $brand_name = "Не визначено";
            $table .= "<tr $style>
                <td>$i</td>
                <td>$article_nr_displ</td>
                <td>$amount</td>
                <td>$art_id</td>
                <td>$brand_id ($brand_name)</td>
            </tr>";
            if ($brands != 0) {
                $db->query("UPDATE `J_BACK_IMPORT` SET `selected` = 1 WHERE `id` = '$id';");
            }
        }
        return $table;
    }

    function getCsvIndexBrands() { $db = DbSingleton::getDb(); $dbt = DbSingleton::getTokoDb();
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $brands = []; $list = "";
        $r = $db->query("SELECT `article_nr_displ` FROM `J_BACK_IMPORT` WHERE `user_id` = '$user_id' AND `status` = 0;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $article_nr_displ = $db->result($r, $i - 1, "article_nr_displ");
            $r2 = $dbt->query("SELECT `BRAND_ID` FROM `T2_ARTICLES` WHERE `ARTICLE_NR_DISPL` = '$article_nr_displ' OR `ARTICLE_NR_SEARCH` = '$article_nr_displ';");
            $n2 = $dbt->num_rows($r2);
            for ($j = 1; $j <= $n2; $j++) {
                $brand_id = $dbt->result($r2, $j - 1, "BRAND_ID");
                array_push($brands, $brand_id);
            }
        }
        $brands = array_unique($brands);
        foreach ($brands as $brand_id) {
            $brand_name = $this->getBrandName($brand_id);
            $list .= "<option value='$brand_id'>$brand_name</option>";
        }
        return $list;
    }

    function saveCsvBackImport($start_row, $kol_cols, $cols) { $db = DbSingleton::getDb();
        $dp = new dp(); $slave = new slave;
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $fn = 0;
        $answer = 0; $err = "Помилка збереження даних!";
        $start_row = $slave->qq($start_row);
        $kol_cols = $slave->qq($kol_cols);
        $cols = $slave->qq($cols);
        $db->query("DELETE FROM `J_BACK_IMPORT` WHERE `user_id`='$user_id';");
        if ($user_id > 0) {
            $r = $db->query("SELECT `file_name` FROM `J_BACK_CSV` WHERE `user_id`='$user_id' LIMIT 1;");
            $n = $db->num_rows($r);
            if ($n == 1) {
                $file_name = $db->result($r, 0, "file_name");
                $file_path = RD . "/cdn/back_files/csv/$user_id/$file_name";
                if (file_exists($file_path)) {
                    $index = 0; $brand = 0; $amount = 0;
                    for ($i = 1; $i <= $kol_cols; $i++) {
                        if ($cols[$i] == 1) { $index = $i; }
                        if ($cols[$i] == 2) { $brand = $i; }
                        if ($cols[$i] == 3) { $amount = $i; }
                    }
                    $handle = @fopen($file_path, "r");
                    if ($handle) {
                        set_time_limit(0);
                        while (($buffer = fgets($handle, 4096)) !== false) {
                            $fn += 1;
                            $buf = explode(";", $buffer);
                            if ($buffer != "") {
                                if ($fn >= $start_row) {
                                    $buf = str_replace("'", "\'", $buf);
                                    $buf = str_replace('"', '\"', $buf);
                                    $ind = trim($buf[$index - 1]);
                                    $brnd = trim($buf[$brand - 1]);
                                    $amnt = trim($buf[$amount - 1]);
                                    $amnt = str_replace(",", ".", $amnt);
                                    $amnt = str_replace(" ", "", $amnt);
                                    $brand_id = $dp->getBrandId($brnd);
                                    $art_id = $dp->getArtId($ind, $brand_id);
                                    if ($brand_id != 0 && $art_id != 0) {
                                        $status = 1;
                                    } else {
                                        $brand_id = 0;
                                        $art_id = 0;
                                        $status = 0;
                                    }
                                    $r2 = $db->query("SELECT MAX(`id`) as mid FROM `J_BACK_IMPORT`;");
                                    $import_id = 0 + $db->result($r2,0,"mid") + 1;
                                    $rs = $db->query("SELECT * FROM `J_BACK_IMPORT` WHERE `user_id`='$user_id' AND `art_id`='$art_id' LIMIT 1;");
                                    $ns = $db->num_rows($rs);
                                    if ($ns > 0 && $art_id > 0) {
                                        $db->query("UPDATE `J_BACK_IMPORT` SET `amount`=`amount`+$amnt WHERE `user_id`='$user_id' AND `art_id`='$art_id' LIMIT 1;");
                                    } else {
                                        $db->query("INSERT INTO `J_BACK_IMPORT` (`id`,`user_id`,`art_id`,`article_nr_displ`,`brand_id`,`amount`,`status`) 
                                        VALUES ('$import_id','$user_id','$art_id','$ind','$brand_id','$amnt','$status');");
                                    }
                                }
                            }
                        }
                        fclose($handle);
                        if (file_exists(RD . "/cdn/dp_files/csv/$user_id/$file_name")) {
                            unlink(RD . "/cdn/dp_files/csv/$user_id/$file_name");
                        }
                        $db->query("DELETE FROM `J_BACK_CSV` WHERE `user_id`='$user_id';");
                        $answer = 1; $err = "";
                    }
                }
            }
        }
        return array($answer, $err);
    }

    /*
     * assign brands
     * */
    function saveTablePreviewBack($brands = 0) { $db = DbSingleton::getDb();
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $dp = new dp();
        $answer = 0; $err = "Помилка збереження даних!";
        if ($brands != 0) {
            $r = $db->query("SELECT `id`, `article_nr_displ` FROM `J_BACK_IMPORT` WHERE `user_id`='$user_id' AND `status`=0 AND `selected`=1;");
            $n = $db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $id = $db->result($r, $i - 1, "id");
                $article_nr_displ = $db->result($r, $i - 1, "article_nr_displ");
                $brand_id = $brands;
                $art_id = $dp->getArtId($article_nr_displ, $brand_id);
                if ($art_id != 0 && $brand_id != 0) {
                    $db->query("UPDATE `J_BACK_IMPORT` SET `brand_id`='$brand_id', `art_id`='$art_id', `status`=1 WHERE `id`='$id';");
                }
            }
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

    /*
     * pre assign brands
     * */
    function loadTablePreviewBack($brands = 0) { $db = DbSingleton::getDb(); $dbt = DbSingleton::getTokoDb();
        $dp = new dp();
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $table = "";
        $where_arts = "";
        $db->query("UPDATE `J_BACK_IMPORT` SET `selected`=0 WHERE `user_id`='$user_id';");
        if ($brands != 0) {
            $arts = [];
            $r = $db->query("SELECT `article_nr_displ` FROM `J_BACK_IMPORT` WHERE `user_id`='$user_id' AND `status`=0;");
            $n = $db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $article_nr_displ = $db->result($r, $i - 1, "article_nr_displ");
                array_push($arts, "'" . $article_nr_displ . "'");
            }
            $arts = array_unique($arts);
            $arts = implode(",", $arts);

            if ($arts != "") {
                $new_arts = [];
                $r = $dbt->query("SELECT `DISPLAY_NR`, `SEARCH_NUMBER` FROM `T2_CROSS` WHERE (`DISPLAY_NR` IN ($arts) OR `SEARCH_NUMBER` IN ($arts)) AND `BRAND_ID` = '$brands';");
                $n = $db->num_rows($r);
                for ($i = 1; $i <= $n; $i++) {
                    $article_nr_displ = $db->result($r, $i - 1, "DISPLAY_NR");
                    $article_nr_search = $db->result($r, $i - 1, "SEARCH_NUMBER");
                    array_push($new_arts,"'" . $article_nr_displ . "'");
                    array_push($new_arts,"'" . $article_nr_search . "'");
                }
                $new_arts = array_unique($new_arts);
                $where_arts = "AND `article_nr_displ` IN (" . implode(",", $new_arts) . ")";
            }
        }

        $r = $db->query("SELECT * FROM `J_BACK_IMPORT` WHERE `user_id` = '$user_id' $where_arts ORDER BY `status` ASC, `id` ASC;");
        $n = $db->num_rows($r);
        $mas = [];
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r, $i - 1, "id");
            $article_nr_displ = $db->result($r, $i - 1, "article_nr_displ");
            $amount = $db->result($r, $i - 1, "amount");
            $art_id = $db->result($r, $i - 1, "art_id");
            $brand_id = $db->result($r, $i - 1, "brand_id");
            $status = $db->result($r, $i - 1, "status");
            $brand_name = $this->getBrandName($brand_id);
            list($full_amount, $storage_info) = $dp->getDpStockInfo($art_id);
            if ($status) {
                $style = 1;
            } else {
                $style = 3;
            }
            if ($full_amount < $amount && $status) {
                $style = 2;
            }

            $mas[$i] = [
                "id" => $id,
                "art_id" => $art_id,
                "article_nr_displ" => $article_nr_displ,
                "brand_id" => $brand_id,
                "brand_name" => $brand_name,
                "amount" => $amount,
                "storage_info" => $storage_info,
                "status" => $status,
                "style" => $style
            ];
        }

        usort($mas,"sortStyle");
        $i = 0;
        foreach ($mas as $val) {
            $i++;
            $style = $val["style"];
            $st = "";
            if ($style == 1) $st = "style='background:lightgreen;'";
            if ($style == 2) $st = "style='background:lightyellow;'";
            if ($style == 3) $st = "style='background:pink;'";
            $id = $val["id"];
            $article_nr_displ = $val["article_nr_displ"];
            $amount = $val["amount"];
            $art_id = $val["art_id"];
            ($art_id != 0) ?: $art_id = "Невідомо";
            $brand_id = $val["brand_id"];
            $brand_name = $val["brand_name"];
            ($brand_name != "") ?: $brand_name = "Не визначено";
            $storage_info = $val["storage_info"];
            ($storage_info != "") ?: $storage_info = "Пусто";

            $table .= "<tr $st>
                <td>$i</td>
                <td>$article_nr_displ</td>
                <td>$amount</td>
                <td>$art_id</td>
                <td>$brand_id ($brand_name)</td>
                <td>$storage_info</td>
            </tr>";
            if ($brands != 0) {
                $db->query("UPDATE `J_BACK_IMPORT` SET `selected` = 1 WHERE `id` = '$id';");
            }
        }
        return $table;
    }

    /*
     * clear data BACK IMPORT
     * */
    function clearBackImport() { $db = DbSingleton::getDb();
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $answer = 0;
        $err = "Помилка збереження даних!";
        if ($user_id > 0) {
            $db->query("DELETE FROM `J_BACK_IMPORT` WHERE `user_id` = '$user_id';");
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

    /*
     * save data BACK IMPORT
     * */
    function finishBackImport($client_id, $storage_id, $cell_id) { $db = DbSingleton::getDb();
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $tpoint_id = $this->getUserTpointId($user_id);
        $answer = 1; $err = "Помилка збереження даних!";
        $mas = [];
        $known = [];

        $r = $db->query("SELECT * FROM `J_BACK_IMPORT` WHERE `user_id` = '$user_id';");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $amount = $db->result($r, $i - 1, "amount");
            $art_id = $db->result($r, $i - 1, "art_id");
            $article_nr_displ = $db->result($r, $i - 1, "article_nr_displ");
            $brand_id = $db->result($r, $i - 1, "brand_id");
            $import_status = $db->result($r, $i - 1, "status");
            if ($import_status) {
                $mas[] = compact("art_id", "amount", "article_nr_displ", "brand_id");
            }
        }

        foreach ($this->getSaleInvoiceList($client_id, $mas) as $val) {
            $invoice_id = $val["invoice_id"];
            $invoice_str_id = $val["invoice_str_id"];
            $art_id = $val["art_id"];
            $amount = $val["amount"];
            $article_nr_displ = $val["article_nr_displ"];
            $brand_id = $val["brand_id"];
            $price_end = $val["price_end"];
            $import_status = $val["status"];
            if ($import_status) {
                $known[$invoice_id][] = compact("art_id", "amount", "article_nr_displ", "brand_id", "price_end", "invoice_str_id");
            }
        }

        $invoice_count = 0;
        foreach ($known as $invoice_id => $item) {
            $back_summ = 0;
            $r = $db->query("SELECT MAX(`id`) as mid FROM `J_BACK_CLIENTS`;");
            $back_id = 0 + $db->result($r, 0, "mid") + 1;
            $seller_id = $this->getSellerId($invoice_id);
            list($doc_nom) = $this->get_df_doc_nom_new($invoice_id, $seller_id);
            list($doc_type_id, $cash_id, $usd_to_uah, $eur_to_uah) = $this->getSaleInvoiceInfo($invoice_id);

            $db->query("INSERT INTO `J_BACK_CLIENTS` (`id`, `prefix`, `doc_nom`, `doc_type_id`, `cash_id`, `usd_to_uah`, `eur_to_uah`, `sale_invoice_id`, `client_id`, `seller_id`, `user_id`, `user_create`, `data`, `tpoint_id`, `storage_id`, `cell_id`, `status_back`)
            VALUES ('$back_id', '$this->prefix_new', '$doc_nom', '$doc_type_id', '$cash_id', '$usd_to_uah', '$eur_to_uah', '$invoice_id', '$client_id', '$seller_id', '$user_id', '$user_id', CURDATE(), '$tpoint_id', '$storage_id', '$cell_id', '102');");
            foreach ($item as $val) {
                $art_id = $val["art_id"];
                $amount = $val["amount"];
                $article_nr_displ = $val["article_nr_displ"];
                $brand_id = $val["brand_id"];
                $price = $val["price_end"];
                $invoice_str_id = $val["invoice_str_id"];
                $summ = round($amount * $price, 2);
                $db->query("INSERT INTO `J_BACK_CLIENTS_STR` (`back_id`, `sale_invoice_str_id`, `art_id`, `article_nr_displ`, `brand_id`, `amount`, `price`, `summ`, `status`)
                VALUES ('$back_id', '$invoice_str_id', '$art_id', '$article_nr_displ', '$brand_id', '$amount', '$price', '$summ', 1);");
                $back_summ += $summ;
            }
            $back_summ = round($back_summ, 2);
            $db->query("UPDATE `J_BACK_CLIENTS` SET `summ` = '$back_summ' WHERE `id` = '$back_id' LIMIT 1;");

            $invoice_count++;
        }
        return array($answer, $err, $invoice_count);
    }

    function getSaleInvoiceInfo($invoice_id) { $db = DbSingleton::getDb();
        $r = $db->query("SELECT `doc_type_id`, `cash_id`, `usd_to_uah`, `eur_to_uah` FROM `J_SALE_INVOICE` WHERE `id` = '$invoice_id' LIMIT 1;");
        $doc_typ_id = $db->result($r, 0, "doc_type_id");
        $cash_id    = $db->result($r, 0, "cash_id");
        $usd_to_uah = $db->result($r, 0, "usd_to_uah");
        $eur_to_uah = $db->result($r, 0, "eur_to_uah");
        return array($doc_typ_id, $cash_id, $usd_to_uah, $eur_to_uah);
    }

    /*
     * Отримати список J_SALE_INVOICE через PARTITION
     * */
    function getSaleInvoiceList($client_id, $art_ids) { $db = DbSingleton::getDb();
        $mas = [];
        foreach ($art_ids as $val) {
            $art_id = $val["art_id"];
            $article_nr_displ = $val["article_nr_displ"];
            $brand_id = $val["brand_id"];
            $amount = $val["amount"];
            $invoices = [];
            $n = 1;
            while ($amount > 0 && $n > 0) {
                $where = "";
                if (!empty($invoices)) {
                    $invoices_str = implode(",", $invoices);
                    $where = "AND js.id NOT IN ($invoices_str)";
                }
                $r = $db->query("SELECT j.id as invoice_id, js.amount, js.id as invoice_str_id, js.price_end, SUM(jp.partition_amount) as sum_parts
                FROM `J_SALE_INVOICE` j
                    LEFT JOIN `J_SALE_INVOICE_STR` js ON js.invoice_id = j.id
                    LEFT JOIN `J_SALE_INVOICE_PARTITION_STR` jp ON jp.invoice_str_id = js.id
                WHERE j.client_id = '$client_id' AND js.art_id = '$art_id' $where
                GROUP BY js.id
                ORDER BY j.id DESC LIMIT 1;");
                $n = $db->num_rows($r);
                $invoice_id = $db->result($r, 0, "invoice_id");
                $invoice_str_id = $db->result($r, 0, "invoice_str_id");
                $amount_str = $db->result($r, 0, "sum_parts"); // проверка на возвраты
                $price_end = $db->result($r, 0, "price_end");
                if ($amount < $amount_str) {
                    $amount_str = $amount;
                }
                if ($amount_str > 0) {
                    $mas[] = [
                        "art_id" => $art_id,
                        "amount" => $amount_str,
                        "article_nr_displ" => $article_nr_displ,
                        "brand_id" => $brand_id,
                        "price_end" => $price_end,
                        "invoice_id" => $invoice_id,
                        "invoice_str_id" => $invoice_str_id,
                        "status" => 1
                    ];
                }
                $invoices[] = $invoice_str_id;
                $amount -= $amount_str;
            }
            if ($amount > 0) {
                $mas[] = [
                    "art_id" => $art_id,
                    "amount" => $amount,
                    "article_nr_displ" => $article_nr_displ,
                    "brand_id" => $brand_id,
                    "status" => 0
                ];
            }
        }

        return $mas;
    }

    /*
     * check back invoice
     * */
    function checkBackArticles($client_id, $storage_id, $cell_id) { $db = DbSingleton::getDb();
        $form = ""; $form_htm = RD . "/tpl/back_import_str_invoice.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $list_unknown = $list_invoice = "";
        $mas = $known = $unknown = [];

        $r = $db->query("SELECT `amount`, `art_id`, `article_nr_displ`, `brand_id`, `status` FROM `J_BACK_IMPORT` WHERE `user_id` = '$user_id';");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $amount = $db->result($r, $i - 1, "amount");
            $art_id = $db->result($r, $i - 1, "art_id");
            $article_nr_displ = $db->result($r, $i - 1, "article_nr_displ");
            $brand_id = $db->result($r, $i - 1, "brand_id");
            $status = $db->result($r, $i - 1, "status");
            if ($status) {
                $mas[] = compact("art_id", "amount", "article_nr_displ", "brand_id");
            } else {
                $unknown[] = compact("art_id", "amount", "article_nr_displ", "brand_id");
            }
        }

        foreach ($this->getSaleInvoiceList($client_id, $mas) as $val) {
            $invoice_id = $val["invoice_id"];
            $invoice_str_id = $val["invoice_str_id"];
            $art_id = $val["art_id"];
            $amount = $val["amount"];
            $article_nr_displ = $val["article_nr_displ"];
            $brand_id = $val["brand_id"];
            $status = $val["status"];
            if ($status) {
                $known[$invoice_id][] = compact("art_id", "amount", "article_nr_displ", "brand_id", "invoice_str_id");
            } else {
                $unknown[] = compact("art_id", "amount", "article_nr_displ", "brand_id");
            }
        }

        // Видаткові накладні
        $i = 0;
        foreach ($known as $invoice_id => $item) {
            $i++;
            $str = $this->getSaleInvoiceCap($invoice_id) . " \n invoice_id: $invoice_id";
            $list_invoice .= "<tr><td colspan='6' class='row-invoice'>$i. $str</td></tr>";

            foreach ($item as $val) {
                $art_id = $val["art_id"];
                $amount = $val["amount"];
                $article_nr_displ = $val["article_nr_displ"];
                $brand_id = $val["brand_id"];
                $brand_name = $this->getBrandName($brand_id);
                $invoice_str_id = $val["invoice_str_id"];
                $storage = $this->getStorageName($storage_id);
                $cell = $this->getCellName($cell_id);
                $invoice_storage = "$storage $cell";

                $list_invoice .= "
                <tr>
                    <td>$article_nr_displ</td>
                    <td>$amount</td>
                    <td>$art_id</td>
                    <td>$brand_name</td>
                    <td>$invoice_str_id</td>
                    <td>$invoice_storage</td>
                </tr>";
            }
        }

        // Не визначені артикули
        $i = 0;
        foreach ($unknown as $val) {
            $i++;
            $art_id     = $val["art_id"];
            $amount     = $val["amount"];
            $art_nr_ds  = $val["article_nr_displ"];
            $brand_id   = $val["brand_id"];
            $brand_name = $this->getBrandName($brand_id);

            $list_unknown .= "
            <tr>
                <td>$i</td>
                <td><button class='btn btn-primary' onclick='showArticleJDocs(\"$art_id\");'><i class='fa fa-history'></i></button> $art_nr_ds</td>
                <td>$amount</td>
                <td>$art_id</td>
                <td>$brand_name</td>
            </tr>";
        }

        $form = str_replace("{records_list_unknown}", $list_unknown, $form);
        $form = str_replace("{records_list_invoice}", $list_invoice, $form);
        $form = str_replace("{client_name}", $this->getClientName($client_id), $form);
        $form = str_replace("{client_id_sel}", $client_id, $form);
        $form = str_replace("{storage_id_sel}", $storage_id, $form);
        $form = str_replace("{cell_id_sel}", $cell_id, $form);

        return $form;
    }

    /*
     * export articles
     * */
    function exportBackImportArtilces($client_id, $storage_id, $cell_id, $status) {
//        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Type: text/csv; charset=windows-1251');
        header('Content-Disposition: attachment; filename=export.csv');
//        ob_clean();
        $output = fopen('php://output', 'w');
        list($title, $array) = $this->getExportedArticles($client_id, $storage_id, $cell_id, $status);
        fputcsv($output, $title, $delimiter = ';');
        foreach ($array as $fields) {
            fputcsv($output, $fields, $delimiter = ';');
        }
        exit(0);
        return true;
    }

    /*
     * status = 0 - UNKNOWN
     * status = 1 - KNOWN
     * */
    function getExportedArticles($client_id, $storage_id, $cell_id, $status) { $db = DbSingleton::getDb();
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $array = [];
        $mas = [];
        $known = [];
        $unknown = [];

        $r = $db->query("SELECT * FROM `J_BACK_IMPORT` WHERE `user_id` = '$user_id';");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $amount             = $db->result($r, $i - 1, "amount");
            $art_id             = $db->result($r, $i - 1, "art_id");
            $article_nr_displ   = $db->result($r, $i - 1, "article_nr_displ");
            $brand_id           = $db->result($r, $i - 1, "brand_id");
            $import_status      = $db->result($r, $i - 1, "status");

            if ($import_status) {
                $mas[] = compact("art_id", "amount", "article_nr_displ", "brand_id");
            } else {
                $unknown[] = compact("art_id", "amount", "article_nr_displ", "brand_id");
            }
        }

        foreach ($this->getSaleInvoiceList($client_id, $mas) as $val) {
            $invoice_id         = $val["invoice_id"];
            $invoice_str_id     = $val["invoice_str_id"];
            $art_id             = $val["art_id"];
            $amount             = $val["amount"];
            $article_nr_displ   = $val["article_nr_displ"];
            $brand_id           = $val["brand_id"];
            $import_status      = $val["status"];

            if ($import_status) {
                $known[$invoice_id][] = compact("art_id", "amount", "article_nr_displ", "brand_id", "invoice_str_id");
            } else {
                $unknown[] = compact("art_id", "amount", "article_nr_displ", "brand_id");
            }
        }

        if ($status == 0) {
            $title = [
                "ARTICLE_NR_DISPL",
                "AMOUNT",
                "ART_ID",
                "BRAND_NAME"
            ];
            foreach ($unknown as $val) {
                $art_id     = $val["art_id"];
                $amount     = $val["amount"];
                $art_nr_ds  = $val["article_nr_displ"];
                $brand_id   = $val["brand_id"];
                $brand_name = $this->getBrandName($brand_id);
                $array[]    = [$art_nr_ds, $amount, $art_id, $brand_name];
            }
        } else {
            $title = [
                "INVOICE",
                "ARTICLE_NR_DISPL",
                "AMOUNT",
                "ART_ID",
                "BRAND_NAME",
                "INVOICE_STR",
                "STORAGE"
            ];
            foreach ($known as $invoice_id => $item) {
                $str = $this->getSaleInvoiceCap($invoice_id) . " \n invoice_id: $invoice_id";

                foreach ($item as $val) {
                    $art_id     = $val["art_id"];
                    $amount     = $val["amount"];
                    $art_nr_ds  = $val["article_nr_displ"];
                    $brand_id   = $val["brand_id"];
                    $brand_name = $this->getBrandName($brand_id);
                    $inv_str_id = $val["invoice_str_id"];
                    $storage    = $this->getStorageName($storage_id);
                    $cell       = $this->getCellName($cell_id);
                    $inv_st     = "$storage $cell";
                    $array[]    = [$str, $art_nr_ds, $amount, $art_id, $brand_name, $inv_str_id, $inv_st];
                }
            }
        }
        return array($title, $array);
    }

}
