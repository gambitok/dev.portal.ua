<?php

class jmoving {

    protected $prefix_new = 'ДФ';

    function getMediaUserName($user_id) { $db = DbSingleton::getDb();
        $r = $db->query("SELECT `name` FROM `media_users` WHERE `id` = $user_id LIMIT 1;");
        $n = $db->num_rows($r);
        $name = "";
        if ($n == 1) {
            $name = $db->result($r, 0, "name");
        }
        return $name;
    }

    function getTpointName($storage_id) { $db = DbSingleton::getDb();
        $r = $db->query("SELECT `tpoint_id` FROM `T_POINT_STORAGE` WHERE `storage_id` = $storage_id;");
        $tpoint_id = $db->result($r, 0, "tpoint_id");
        $r = $db->query("SELECT `name` FROM `T_POINT` WHERE `id` = $tpoint_id;");
        return $db->result($r, 0, "name");
    }

    function getArtIdByBarcode($barcode) { $db = DbSingleton::getTokoDb();
        $art_id = 0;
        $r = $db->query("SELECT `ART_ID` FROM `T2_BARCODES` WHERE `BARCODE` = '$barcode' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $art_id = $db->result($r, 0, "ART_ID");
        }
        return $art_id;
    }

    function getArtId($code, $brand_id) { $db = DbSingleton::getTokoDb();
        $slave = new slave;
        $id = 0;
        $code = $slave->qq($code); $code = $this->clearArticle($code);
        $r = $db->query("SELECT `ART_ID` FROM `T2_ARTICLES` WHERE `ARTICLE_NR_SEARCH` = '$code' AND `BRAND_ID` = $brand_id LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $id = $db->result($r, 0, "ART_ID");
        }
        return $id;
    }

    function getBrandId($code) { $db = DbSingleton::getTokoDb();
        $slave = new slave;
        $id = 0;
        $code = $slave->qq($code);
        $r = $db->query("SELECT `BRAND_ID` FROM `T2_BRANDS` WHERE `BRAND_NAME` = '$code' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $id = $db->result($r, 0, "BRAND_ID");
        }
        return $id;
    }

    function getBrandName($id) { $db = DbSingleton::getTokoDb();
        $name = "";
        $r = $db->query("SELECT `BRAND_NAME` FROM `T2_BRANDS` WHERE `BRAND_ID` = $id LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $name = $db->result($r, 0, "BRAND_NAME");
        }
        return $name;
    }

    function get_jmoving_prefix($jmoving_id) { $db = DbSingleton::getDb();
        $prefix = "ПР";
        $r = $db->query("SELECT `type_id` FROM `J_MOVING` WHERE `id` = $jmoving_id LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $type_id = $db->result($r, 0, "type_id");
            if ($type_id == 0) {
                $prefix = "В-ПР";
            }
        }
        return $prefix;
    }

    function get_df_doc_nom_new() { $db = DbSingleton::getDb();
        $r = $db->query("SELECT MAX(`doc_nom`) as doc_nom FROM `J_MOVING` WHERE `oper_status`='30' LIMIT 1;");
        return 0 + $db->result($r, 0, "doc_nom") + 1;
    }

    function newJmovingCard($type_id) { $db = DbSingleton::getDb();
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $r = $db->query("SELECT MAX(`id`) as mid FROM `J_MOVING`;");
        $jmoving_id = 0 + $db->result($r,0,"mid") + 1;
        $doc_nom = $this->get_df_doc_nom_new();
        $db->query("INSERT INTO `J_MOVING` (`id`,`type_id`,`prefix`,`doc_nom`,`user_id`,`data`) VALUES ('$jmoving_id','$type_id','$this->prefix_new','$doc_nom','$user_id',CURDATE());");
        return $jmoving_id;
    }

    function preNewJmovingCard() {
        $form = ""; $form_htm = RD . "/tpl/jmoving_select_type_form.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        return $form;
    }

    function getJmovingData($jmoving_id) { $db = DbSingleton::getDb();
        $r = $db->query("SELECT * FROM `J_MOVING` WHERE `id` = $jmoving_id LIMIT 1;");
        $prefix = $db->result($r, 0, "prefix");
        $doc_nom = $db->result($r, 0, "doc_nom");
        $storage_id_to = $db->result($r, 0, "storage_id_to");
        $cell_id_to = $db->result($r, 0, "cell_id_to");
        $type_id = $db->result($r, 0, "type_id");
        return array($type_id, $prefix, $doc_nom, $storage_id_to, $cell_id_to,);
    }

    function getJmovingStorage($jmoving_id) { $db = DbSingleton::getDb();
        $r = $db->query("SELECT `storage_id_to` FROM `J_MOVING` WHERE `id` = $jmoving_id LIMIT 1;");
        return $db->result($r, 0, "storage_id_to");
    }

    function getJmovingAccess($user_id, $storage_id) { $db = DbSingleton::getDb();
        $users = new users;
        $super_user = $users->getSuperUser($user_id);
        $r = $db->query("SELECT * FROM `media_users_storage` WHERE `user_id` = $user_id AND `storage_id` = $storage_id;");
        $n = $db->num_rows($r);
        return ($n > 0 || $super_user);
    }

    function getJmovingName($jmoving_id) { $db = DbSingleton::getDb();
        $r = $db->query("SELECT `prefix`, `doc_nom` FROM `J_MOVING` WHERE `id` = $jmoving_id LIMIT 1;");
        $prefix = $db->result($r, 0, "prefix");
        $doc_nom = $db->result($r, 0, "doc_nom");
        return $prefix . "-" . $doc_nom;
    }

    function getJmovingNote($jmoving_id) { $db = DbSingleton::getDb();
        $text = "";
        $r = $db->query("SELECT `comment` FROM `J_MOVING` WHERE `id` = $jmoving_id LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n > 0) {
            $text = $db->result($r, 0, "comment");
        }
        return $text;
    }

    function getJmovingStatusId($jmoving_id) { $db = DbSingleton::getDb();
        $status_jmoving = 0;
        $r = $db->query("SELECT `status_jmoving` FROM `J_MOVING` WHERE `id` = $jmoving_id LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $status_jmoving = $db->result($r, 0, "status_jmoving");
        }
        return $status_jmoving;
    }

    function getJmovingInfo($jmoving_id) { $db = DbSingleton::getDb();
        $r = $db->query("SELECT * FROM `J_MOVING` WHERE `id` = $jmoving_id LIMIT 1;");
        $prefix = $db->result($r, 0, "prefix");
        $doc_nom = $db->result($r, 0, "doc_nom");
        $data = $db->result($r, 0, "data");
        $storage_id_to = $db->result($r, 0, "storage_id_to");
        $storage_name_to = $this->getStorageName($storage_id_to);
        $comment = $db->result($r, 0, "comment");
        $parrent_type_id = $db->result($r, 0, "parrent_type_id");
        $parrent_doc_id = $db->result($r, 0, "parrent_doc_id");
        return array($prefix, $doc_nom, $data, $storage_id_to, $storage_name_to, $comment, $parrent_type_id, $parrent_doc_id);
    }

    function checkJmovingStructure($jmoving_id) { $db = DbSingleton::getDb();
        $r = $db->query("SELECT * FROM `J_MOVING_STR` WHERE `jmoving_id` = $jmoving_id;");
        $n = $db->num_rows($r);
        return ($n > 0);
    }

    function statusStorage($user_id, $storage_id, $jmoving_id) { $db = DbSingleton::getDb();
        $users = new users;
        $super_user = $users->getSuperUser($user_id);
        // склад призначення
        $r = $db->query("SELECT * FROM `media_users_storage` WHERE `user_id`='$user_id' AND `storage_id`='$storage_id';");
        $n = $db->num_rows($r);
        $status = ($n > 0);
        // склад відбору
        if (!$status) {
            $r = $db->query("SELECT `storage_id_from` FROM `J_MOVING_STR` WHERE `jmoving_id` = $jmoving_id LIMIT 1;");
            $storage_id_from = $db->result($r, 0, "storage_id_from");
            $r2 = $db->query("SELECT * FROM `media_users_storage` WHERE `user_id`='$user_id' AND `storage_id`='$storage_id_from';");
            $n = $db->num_rows($r2);
            $status = ($n > 0);
        }
        // автор
        $rj = $db->query("SELECT `user_id` FROM `J_MOVING` WHERE `id` = $jmoving_id LIMIT 1;");
        $j_user = $db->result($rj, 0, "user_id");
        if ($j_user == $user_id) {
            $status = true;
        }
        // адміністратор
        if ($super_user) {
            $status = true;
        }
        return $status;
    }

    function show_jmoving_list($press = null) { $db = DbSingleton::getDb();
        $gmanual = new gmanual; $income = new income;
        session_start();
        $media_user_id = $_SESSION["media_user_id"];
        $list = "";
        $r = $db->query("SELECT j.*, s.name as storage_name, sc.storage_id, sc.`cell_value` 
        FROM `J_MOVING` j
            LEFT OUTER JOIN `STORAGE` s on s.id=j.storage_id_to
            LEFT OUTER JOIN `STORAGE_CELLS` sc on sc.id=j.cell_id_to
            LEFT OUTER JOIN `T_POINT_STORAGE` t on t.storage_id=j.storage_id_to
            LEFT OUTER JOIN `manual` man on man.id=j.status_jmoving
        WHERE 1
        ORDER BY man.mid ASC, j.data DESC, j.doc_nom DESC, j.id DESC 
        LIMIT 0,500;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r, $i - 1, "id");
            $type_id = $db->result($r, $i - 1, "type_id");
            $type_name = "<i class='fa fa-inbox'></i> Внутрішнє переміщення";
            if ($type_id == 1) {
                $type_name = "<i class='fa fa-truck'></i> Між складами";
            }
            $prefix = $db->result($r, $i - 1, "prefix");
            $doc_nom = $db->result($r, $i - 1, "doc_nom");
            $storage_id_to = $db->result($r, $i - 1, "storage_id_to");
            if ($storage_id_to == 0) {
                $storage_id_to = $db->result($r, $i - 1, "storage_id");
            }
            $storage_name = $db->result($r, $i - 1, "storage_name");
            if ($storage_name == "") {
                $storage_name = $income->getStorageName($storage_id_to);
            }
            $cell_value = $db->result($r, $i - 1, "cell_value");
            $data = $db->result($r, $i - 1, "data");
            $user_id = $db->result($r, $i - 1, "user_id");
            $user_name = $this->getMediaUserName($user_id);
            $status = $db->result($r, $i - 1, "status_jmoving");
            $st = $db->result($r, $i - 1, "status");
            if ($st == 0) {
                $status = 106;
            }
            $status_jmoving = $gmanual->get_gmanual_caption($status);
            $function = "showJmovingCard(\"$id\")";
            if ($type_id == 0) {
                $function = "showJmovingCardLocal(\"$id\")";
            }
            $statud = (!$press) ? $this->checkJmovingStructure($id) : true;
            if ($statud) {
                if ($this->statusStorage($media_user_id, $storage_id_to, $id)) {
                    $list .= "<tr style='cursor:pointer' onClick='$function'>
                        <td>$id</td>
                        <td>$type_name</td>
                        <td>$prefix - $doc_nom</td>
                        <td align='center'>$data</td>
                        <td>$storage_name $cell_value</td>
                        <td>$user_name</td>
                        <td>$status_jmoving</td>
                    </tr>";
                }
            }
        }
        return $list;
    }

    function filterJmovingList($name, $data_from, $data_to, $status_jmoving) { $db = DbSingleton::getDb();
        $gmanual = new gmanual; $income = new income;
        session_start();
        $ses_tpoint_id = $_SESSION["media_tpoint_id"];
        $media_user_id = $_SESSION["media_user_id"];
        $press = false;
        $where_filter = "";
        $where = " AND (t.tpoint_id='$ses_tpoint_id' OR j.user_id=$media_user_id) ";
        if ($media_user_id == 1 || $media_user_id == 7) {
            $where = "";
        }
        if ($name > 0 && $name != "") {
            $where_filter .= " AND j.id='$name'";
        }
        if ($data_from > 0 && $data_from != "") {
            $where_filter .= " AND j.data>=$data_from ";
        }
        if ($data_to > 0 && $data_to != "") {
            $where_filter .= " AND j.data<=$data_to ";
        }
        if ($status_jmoving > 0 && $status_jmoving != "") {
            $where_filter .= " AND j.status_jmoving='$status_jmoving'";
        }

        $list = "";
        $r = $db->query("SELECT j.*, s.name as storage_name, sc.storage_id, sc.`cell_value` 
        FROM `J_MOVING` j
            LEFT OUTER JOIN `STORAGE` s on s.id=j.storage_id_to
            LEFT OUTER JOIN `STORAGE_CELLS` sc on sc.id=j.cell_id_to
            LEFT OUTER JOIN `T_POINT_STORAGE` t on t.storage_id=j.storage_id_to
            LEFT OUTER JOIN `manual` man on man.id=j.status_jmoving
        WHERE j.id > 0 $where $where_filter 
        ORDER BY man.mid ASC, j.data DESC, j.doc_nom DESC, j.id DESC 
        LIMIT 0,500;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r, $i - 1, "id");
            $type_id = $db->result($r, $i - 1, "type_id");
            $type_name = "<i class='fa fa-inbox'></i> Внутрішнє переміщення";
            if ($type_id == 1) {
                $type_name = "<i class='fa fa-truck'></i> Між складами";
            }
            $prefix = $db->result($r, $i - 1, "prefix");
            $doc_nom = $db->result($r, $i - 1, "doc_nom");
            $storage_id_to = $db->result($r, $i - 1, "storage_id_to");
            if ($storage_id_to == 0) {
                $storage_id_to = $db->result($r, $i - 1, "storage_id");
            }
            $storage_name = $db->result($r, $i - 1, "storage_name");
            if ($storage_name == "") {
                $storage_name = $income->getStorageName($storage_id_to);
            }
            $cell_value = $db->result($r, $i - 1, "cell_value");
            $data = $db->result($r, $i - 1, "data");
            $user_name = $this->getMediaUserName($db->result($r,$i-1,"user_id"));
            $status = $db->result($r, $i - 1, "status_jmoving");
            $st = $db->result($r, $i - 1, "status");
            if ($st == 0) {
                $status = 106;
            }
            $status_jmoving = $gmanual->get_gmanual_caption($status);
            $function = "showJmovingCard(\"$id\")";
            if ($type_id == 0) {
                $function = "showJmovingCardLocal(\"$id\")";
            }
            $statud = (!$press) ? $this->checkJmovingStructure($id) : true;

            if ($statud) {
                $list .= "<tr style='cursor:pointer' onClick='$function'>
                    <td>$id</td>
                    <td>$type_name</td>
                    <td>$prefix - $doc_nom</td>
                    <td align='center'>$data</td>
                    <td>$storage_name $cell_value</td>
                    <td>$user_name</td>
                    <td>$status_jmoving</td>
                </tr>";
            }
        }
        return $list;
    }

    function getKoursUSD() { $db = DbSingleton::getDb();
        $r = $db->query("SELECT `kours_value` FROM `J_KOURS` WHERE `cash_id` = 2 AND `in_use` = 1 LIMIT 1;");
        return number_format($db->result($r,0,"kours_value"), 2, '.', '');
    }

    function getFullPriceArt($art_id) { $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT `price_1` FROM `T2_ARTICLES_PRICE_RATING` WHERE `art_id` = $art_id LIMIT 1;");
        return $db->result($r, 0, "price_1");
    }

    function getJMovingFullPrice($jmoving_id) { $db = DbSingleton::getDb(); $dbt = DbSingleton::getTokoDb();
        $summ = 0;
        $r = $db->query("SELECT `art_id`, `amount` FROM `J_MOVING_STR` WHERE `jmoving_id` = $jmoving_id;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $art_id = $db->result($r, $i - 1, "art_id");
            $amount = $db->result($r, $i - 1, "amount");
            $r1 = $dbt->query("SELECT `cash_id` FROM `T2_ARTICLES_PRICE_RATING` WHERE `art_id` = $art_id AND `in_use` = 1 LIMIT 1;");
            $cash_id = $dbt->result($r1, 0, "cash_id");
            $koursUSD = 1;
            if ($cash_id == 2) {
                $koursUSD = $this->getKoursUSD();
            }
            $price = $this->getFullPriceArt($art_id);
            $price_art = $amount * $price * $koursUSD;
            $summ += $price_art;
        }
        return $summ;
    }

    function dropJmovingCard($jmoving_id) { $db = DbSingleton::getDb();
        $db->query("UPDATE `J_MOVING` SET `status_jmoving` = 57 WHERE `id` = $jmoving_id LIMIT 1;");
        return true;
    }

    function showJmovingCard($jmoving_id) { $db = DbSingleton::getDb();
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $user_name = $_SESSION["user_name"];
        $doc_nom = 0;
        $prefix = $data_accepting = $data_accepted = $type_name = "";
        $form = ""; $form_htm = RD . "/tpl/jmoving_card.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }

        $r = $db->query("SELECT * FROM `J_MOVING` WHERE `id` = $jmoving_id LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 0) {
            $form_htm = RD . "/tpl/access_deny.htm";
            if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        }
        if ($n == 1) {
            $type_id = $db->result($r, 0, "type_id");
            if ($type_id == 1) {
                $type_name = "Між складами";
            }
            $prefix=$db->result($r,0,"prefix");
            $doc_nom=$db->result($r,0,"doc_nom");
            $user_use=$db->result($r,0,"user_use");
            $data_create=$db->result($r,0,"time_stamp");
            $user_accepting=$db->result($r,0,"user_accepting");
            $user_accepted=$db->result($r,0,"user_accepted");
            $user_data_accepting=$db->result($r,0,"user_data_accepting");
            $user_data_accepted=$db->result($r,0,"user_data_accepted");
            if ($user_accepting != "" && $user_data_accepting != "0000-00-00 00:00:00") {
                $data_accepting = "Приймається: {$this->getMediaUserName($user_accepting)}, $user_data_accepting";
            }
            if ($user_accepted != "" && $user_data_accepted != "0000-00-00 00:00:00") {
                $data_accepted = "Прийнято: {$this->getMediaUserName($user_accepted)}, $user_data_accepted";
            }

            if ($user_id != $user_use && $user_use > 0) {
                $form_htm = RD . "/tpl/jmoving_use_deny.htm";
                if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
                $form = str_replace("{user_name}", $this->getMediaUserName($user_use), $form);
            }
            if ($user_id == $user_use || $user_use == 0) {
                $weight=$db->result($r,0,"weight");
                $volume=$db->result($r,0,"volume");
                $comment=$db->result($r,0,"comment");
                $storage_id_to=$db->result($r,0,"storage_id_to");
                $cell_use=$db->result($r,0,"cell_use");
                $cell_id_to=$db->result($r,0,"cell_id_to");
                $data=$db->result($r,0,"data");
                if ($data=="0000-00-00") {$data="";}
                $status_jmoving=$db->result($r,0,"status_jmoving");
                $oper_status=$db->result($r,0,"oper_status");
                if ($oper_status == 31) {
                    $form = str_replace("{hide_new_row_button}"," disabled style=\"visibility:hidden;\"",$form);
                    $form = str_replace("{oper_disabled}"," disabled",$form);
                }
                $form = str_replace("{oper_disabled}","",$form);
                $form = str_replace("{hide_new_row_button}","",$form);
                $form = str_replace("{disabled106}", ($status_jmoving == 106) ? " readonly " : "", $form);
                $form = str_replace("{disabledHide}", ($status_jmoving == 106) ? "disabled style='display:none'" : "", $form);
                $form = str_replace("{jmoving_id}",$jmoving_id,$form);
                $form = str_replace("{data}",$data,$form);
                $form = str_replace("{data_create}",$data_create,$form);
                $form = str_replace("{type_id}",$type_id,$form);
                $form = str_replace("{type_name}",$type_name,$form);
                $form = str_replace("{weight}",$weight,$form);
                $form = str_replace("{volume}",$volume,$form);
                $form = str_replace("{comment}",$comment,$form);
                $form = str_replace("{storage_list}",$this->showStorageSelectList($storage_id_to),$form);
                $form = str_replace("{cells_show}",($cell_use == 1) ? "" : "hidden",$form);
                $form = str_replace("{data_accepting}",$data_accepting,$form);
                $form = str_replace("{data_accepted}",$data_accepted,$form);
                $form = str_replace("{storage_cells_list}",$this->showStorageCellsSelectList($storage_id_to, $cell_id_to)[0],$form);

                list($jmovingChildsList, $kol_art_str) = $this->showJmovingStrList($jmoving_id, $oper_status, $storage_id_to);
                $form = str_replace("{jmovingChildsList}",$jmovingChildsList,$form);
                $storage_to_disabled = "";
                if ($status_jmoving != 44) {
                    $storage_to_disabled = " disabled";
                }
                if ($kol_art_str > 0) {
                    $storage_to_disabled = " disabled";
                }
                $form = str_replace("{storage_to_disabled}",$storage_to_disabled,$form);
                $form = str_replace("{my_user_id}",$user_id,$form);
                $form = str_replace("{my_user_name}",$user_name,$form);

                $form = str_replace("{labelCommentsCount}",$this->labelCommentsCount($jmoving_id),$form);
                list(,$label_art_unknown) = $this->labelArtEmptyCount($jmoving_id, 0);
                $form = str_replace("{labelArticlesUnKnownCount}",$label_art_unknown,$form);
                $form = str_replace("{labelArticlesUnKnownStorageCount}",$this->loadJmovingStorageCount($jmoving_id),$form);

                $disabled48 = "disabled";
                if ($this->checkStorselAllStatus($jmoving_id)==1 && ($status_jmoving<48 || $status_jmoving==107)) {
                    // status В дорозі
                    $disabled48 = "";
                }
                $form = str_replace("{disabled48}",$disabled48,$form);
                $form = str_replace("{print_allow}",($status_jmoving == 48 || $status_jmoving == 57) ? "" : "not-active",$form);
                $form = str_replace("{disabled49}",($status_jmoving == 48 || $status_jmoving == 49) ? "" : "disabled hidden",$form);
                $form = str_replace("{disabled_user}",$this->getJmovingAccess($user_id, $storage_id_to) ? "" : "disabled hidden",$form);
                $form = str_replace("{disabled_row}",($status_jmoving > 44) ? "disabled hidden" : "",$form);
                $status_type = (($status_jmoving == 44 || $status_jmoving >= 48)) ? "disabled style='display:none'" : "";
                if ($type_id == 0) {
                    $status_type = "disabled style='display:none'";
                }
                $form = str_replace("{disabled100}", $status_type, $form);
                $form = str_replace("{jmoving_fullprice}", number_format($this->getJMovingFullPrice($jmoving_id), 2, '.', '')." грн", $form);
                if ($storage_id_to == 0 && $status_jmoving != 44) {
                    $form = str_replace("{disable_storage}", "disabled", $form);
                    $form = str_replace("{disable_tab}", "", $form);
                } else {
                    $form = str_replace("{disable_storage}", "", $form);
                    $form = str_replace("{disable_tab}", "tab", $form);
                }
                $this->setJmovingCardUserAccess($jmoving_id, $user_id);
            }
        }
        $form = str_replace("{status_jmoving_delete}",($user_id == 8) ? "" : "disabled",$form);
        return array($form, $prefix . "-" . $doc_nom);
    }

    /*
     * анулювати переміщення
     * */
    function cancelJmoving($jmoving_id) { $db = DbSingleton::getDb(); $dbt = DbSingleton::getTokoDb();
        $answer = 0; $err = "Помилка збереження даних!";
        $select_id = 0;
        $r = $db->query("SELECT * FROM `J_MOVING` WHERE `id` = $jmoving_id LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n > 0) {
            $r = $db->query("SELECT `art_id`, `select_id` FROM `J_MOVING_STR` WHERE `jmoving_id` = $jmoving_id;");
            $n = $db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $art_id = $db->result($r, $i - 1, "art_id");
                $select_id = $db->result($r, $i - 1, "select_id");

                $rstr = $db->query("SELECT * FROM `J_SELECT_STR` WHERE `select_id` = $select_id AND `art_id` = $art_id;");
                $nstr = $db->num_rows($rstr);
                for ($j = 1; $j <= $nstr; $j++) {
                    $storage_id_from = $db->result($rstr, $j - 1, "storage_id_from");
                    $cell_id_from = $db->result($rstr, $j - 1, "cell_id_from");
                    $count = floatval($db->result($rstr, $j - 1, "amount")) - floatval($db->result($rstr, $j - 1, "amount_bug"));
                    // повернення на склади
                    $rt1 = $dbt->query("SELECT  `AMOUNT`, `RESERV_AMOUNT` FROM `T2_ARTICLES_STRORAGE` WHERE `art_id` = $art_id AND `storage_id` = $storage_id_from LIMIT 1;");
                    $nt1 = $dbt->num_rows($rt1);
                    if ($nt1 > 0) {
                        $amount = $dbt->result($rt1, 0, "AMOUNT");
                        $amount_res = $dbt->result($rt1, 0, "RESERV_AMOUNT");
                        $amount_new = intval($amount) + intval($count);
                        $amount_res_new = intval($amount_res) - intval($count);
                        $dbt->query("UPDATE `T2_ARTICLES_STRORAGE` SET `AMOUNT`='$amount_new', `RESERV_AMOUNT`='$amount_res_new' WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id_from LIMIT 1;");
                    }
                    // повернення на комірки
                    $rt2 = $dbt->query("SELECT `AMOUNT`, `RESERV_AMOUNT` FROM `T2_ARTICLES_STRORAGE_CELLS` WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id_from AND `STORAGE_CELLS_ID` = $cell_id_from LIMIT 1;");
                    $nt2 = $dbt->num_rows($rt2);
                    if ($nt2 > 0) {
                        $amount = $dbt->result($rt2, 0, "AMOUNT");
                        $amount_res = $dbt->result($rt2, 0, "RESERV_AMOUNT");
                        $amount_new = intval($amount) + intval($count);
                        $amount_res_new = intval($amount_res) - intval($count);
                        $dbt->query("UPDATE `T2_ARTICLES_STRORAGE_CELLS` SET `AMOUNT`='$amount_new', `RESERV_AMOUNT`='$amount_res_new' WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id_from AND `STORAGE_CELLS_ID` = $cell_id_from LIMIT 1;");
                    }
                }
            }
            // видалення J_MOVING_SELECT_STR
            $db->query("UPDATE `J_MOVING_SELECT_STR` SET `status` = 0 WHERE `jmoving_id` = $jmoving_id;");
            // видалення J_SELECT, J_SELECT_STR
            $db->query("UPDATE `J_SELECT_STR` SET `status` = 0 WHERE `select_id` = $select_id;");
            $db->query("UPDATE `J_SELECT` SET `status` = 0 WHERE `parrent_doc_id` = $jmoving_id;");
            // видалення J_MOVING
            $db->query("UPDATE `J_MOVING` SET `status` = 0, `status_jmoving` = 106 WHERE `id` = $jmoving_id;");
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

    function showJmovingCardLocal($jmoving_id) { $db = DbSingleton::getDb();
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $user_name = $_SESSION["user_name"];
        $prefix = ""; $doc_nom = 0;
        $form = ""; $form_htm = RD . "/tpl/jmoving_card_local.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $r = $db->query("SELECT * FROM `J_MOVING` WHERE `id` = $jmoving_id LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 0) {
            $form_htm = RD . "/tpl/access_deny.htm";
            if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        }
        if ($n == 1) {
            $type_id = $db->result($r,0,"type_id");
            $type_name = "Внутрішнє переміщення";
            $prefix = $db->result($r,0,"prefix");
            $doc_nom = $db->result($r,0,"doc_nom");
            $user_use = $db->result($r,0,"user_use");
            $data_create = $db->result($r,0,"time_stamp");
            if ($user_id != $user_use && $user_use > 0) {
                $form_htm = RD . "/tpl/jmoving_use_deny.htm";
                if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
                $form = str_replace("{user_name}",$this->getMediaUserName($user_use),$form);
            }
            if ($user_id == $user_use || $user_use == 0) {
                $weight=$db->result($r,0,"weight");
                $volume=$db->result($r,0,"volume");
                $comment=$db->result($r,0,"comment");

                $storage_id_to=$db->result($r,0,"storage_id_to");
                $data=$db->result($r,0,"data");
                if ($data == "0000-00-00") {
                    $data = "";
                }
                $status_jmoving=$db->result($r,0,"status_jmoving");
                $oper_status=$db->result($r,0,"oper_status");
                if ($oper_status == 31) {
                    $form = str_replace("{hide_new_row_button}"," disabled style=\"visibility:hidden;\"",$form);
                    $form = str_replace("{oper_disabled}"," disabled",$form);
                }
                $form = str_replace("{oper_disabled}","",$form);
                $form = str_replace("{hide_new_row_button}","",$form);
                $form = str_replace("{jmoving_id}",$jmoving_id,$form);
                $form = str_replace("{data}",$data,$form);
                $form = str_replace("{data_create}",$data_create,$form);
                $form = str_replace("{type_id}",$type_id,$form);
                $form = str_replace("{type_name}",$type_name,$form);
                $form = str_replace("{weight}",$weight,$form);
                $form = str_replace("{volume}",$volume,$form);
                $form = str_replace("{comment}",$comment,$form);
                $form = str_replace("{storage_list}",$this->showStorageSelectList($storage_id_to, 1),$form);
                list($jmovingChildsList, $kol_art_str) = $this->showJmovingStrLocalList($jmoving_id, $oper_status);
                $form = str_replace("{jmovingChildsList}",$jmovingChildsList,$form);
                $storage_to_disabled = "";
                if ($status_jmoving != 44) {
                    $storage_to_disabled = " disabled";
                }
                if ($kol_art_str > 0) {
                    $storage_to_disabled = " disabled";
                }
                $form = str_replace("{storage_to_disabled}",$storage_to_disabled,$form);
                $form = str_replace("{my_user_id}",$user_id,$form);
                $form = str_replace("{my_user_name}",$user_name,$form);

                $form = str_replace("{labelCommentsCount}",$this->labelCommentsCount($jmoving_id),$form);
                list(,$label_art_unknown) = $this->labelArtEmptyCount($jmoving_id, 0);
                $form = str_replace("{labelArticlesUnKnownCount}",$label_art_unknown,$form);
                $form = str_replace("{disabled48}", ($this->checkJmovingSelectAllStatus($jmoving_id, 47) == 1 && $status_jmoving != 57) ? "" : "disabled", $form);
                $form = str_replace("{disabled49}", ($this->checkJmovingSelectAllStatus($jmoving_id, 48) == 1 && $status_jmoving != 57) ? "" : "disabled hidden", $form);
                $storsel_count = $this->showJmovingSkladStorageSelectListLocal($jmoving_id,$status_jmoving)[1];
                $form = str_replace("{labelArticlesUnKnownStorageCount}",($storsel_count != 0) ? $storsel_count : "",$form);

                if ($storage_id_to == 0 && $status_jmoving != 44) {
                    $form = str_replace("{disable_storage}","disabled",$form);
                    $form = str_replace("{disable_tab}","",$form);
                } else {
                    $form = str_replace("{disable_storage}","",$form);
                    $form = str_replace("{disable_tab}","tab",$form);
                }
                $this->setJmovingCardUserAccess($jmoving_id, $user_id);
            }
        }
        return array($form, $prefix . "-" . $doc_nom);
    }

    function closeJmovingCard($jmoving_id) {
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $this->unsetJmovingCardUserAccess($jmoving_id, $user_id);
        return 1;
    }

    function setJmovingCardUserAccess($jmoving_id, $user_id) { $db = DbSingleton::getDb();
        if ($jmoving_id > 0 && $user_id > 0) {
            $db->query("UPDATE `J_MOVING` SET `user_use`='$user_id' WHERE `id` = $jmoving_id;");
        }
        return true;
    }

    function unsetJmovingCardUserAccess($jmoving_id, $user_id) { $db = DbSingleton::getDb();
        if ($jmoving_id > 0 && $user_id > 0) {
            $db->query("UPDATE `J_MOVING` SET `user_use`='0' WHERE `id` = $jmoving_id;");
        }
        return true;
    }

    function checkStorselAllStatus($jmoving_id) { $db = DbSingleton::getDb();
        $ex = 1;
        $r = $db->query("SELECT `status_select` FROM `J_SELECT` WHERE `parrent_doc_type_id`='1' AND `parrent_doc_id`='$jmoving_id' AND `status`='1';");
        $n = $db->num_rows($r);
        if ($n == 0) {
            $ex = 0;
        }
        for ($i = 1; $i <= $n; $i++) {
            $status_select = $db->result($r, $i - 1, "status_select");
            if ($status_select != 85) {
                $ex = 0;
                $i = $n + 1;
            }
        }
        return $ex;
    }

    function checkJmovingSelectAllStatus($jmoving_id, $statusJmoving) { $db = DbSingleton::getDb();
        $ex = 1;
        $r = $db->query("SELECT `id`, `status_jmoving` FROM `J_MOVING_SELECT` WHERE `jmoving_id` = $jmoving_id AND `status` = '1';");
        $n = $db->num_rows($r);
        if ($n == 0) {
            $ex = 0;
        }
        for ($i = 1; $i <= $n; $i++) {
            $status_jmoving = $db->result($r, $i - 1, "status_jmoving");
            if ($status_jmoving != $statusJmoving) {
                $ex = 0;
                $i = $n + 1;
            }
        }
        $r = $db->query("SELECT `status_jmoving` FROM `J_MOVING` WHERE `id` = $jmoving_id AND `status` = '1' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 0) {
            $ex = 0;
        }
        if ($n == 1) {
            $status_jmoving = $db->result($r, 0, "status_jmoving");
            if ($status_jmoving == 44) {
                $ex = 0;
            }
        }
        return $ex;
    }

    function showJmovingStrList($jmoving_id, $oper_status, $storage_id_to) { $db = DbSingleton::getDb();
        $slave = new slave;
        $list = "";
        $amount_barcodes = $amount_barcodes_noscan = $amount_bug = 0;
        if ($oper_status == "") {
            $oper_status = 30;
        }
        $r = $db->query("SELECT * FROM `J_MOVING_STR` WHERE `jmoving_id` = $jmoving_id ORDER BY `id` ASC;");
        $n = $db->num_rows($r);
        $kl_rw = $n; $sum_weight = 0; $sum_volume = 0;
        for ($i = 1; $i <= $kl_rw; $i++) {
            $status_jmoving=0;
            $id=""; $art_id=""; $article_nr_displ=""; $brand_id=""; $brand_name=""; $amount=""; $storage_id_from=""; $storage_name_from=""; $max_stok="";
            if ($i <= $n) {
                $id=$db->result($r,$i-1,"id");
                $art_id=$db->result($r,$i-1,"art_id");
                $article_nr_displ=$db->result($r,$i-1,"article_nr_displ");
                $brand_id=$db->result($r,$i-1,"brand_id");
                $brand_name=$this->getBrandName($brand_id);
                $amount=$db->result($r,$i-1,"amount");
                $amount_barcodes=$db->result($r,$i-1,"amount_barcodes");
                $amount_barcodes_noscan=$db->result($r,$i-1,"amount_barcodes_noscan");
                $amount_bug=$db->result($r,$i-1,"amount_bug");
                $storage_id_from=$db->result($r,$i-1,"storage_id_from");
                $storage_name_from=$this->getStorageName($storage_id_from);
                $status_jmoving=$db->result($r,$i-1,"status_jmoving");
            }
            if ($oper_status == 30) {
                list($weight, $volume) = $this->getArticleWightVolume($art_id);
                $sum_weight += ($weight * $amount);
                $sum_volume += ($volume * $amount);
                $disabled = "";
                if ($status_jmoving != 44 && $status_jmoving > 0) {
                    $disabled = " disabled";
                }
                if ($status_jmoving == 106) {
                    $disabled = " disabled";
                }
                $list .= "<tr id='strRow_$i'>
                    <td>$i<input type='hidden' id='idStr_$i' value='$id'></td>
                    <td style='min-width:140px;'><input type='hidden' id='artIdStr_$i' value='$art_id'>
                        <div class='input-group'>
                            <input class='form-control input-xs' type='text' readonly id='article_nr_displStr_$i' value='$article_nr_displ' placeholder='Індекс товару'>
                            <span class='input-group-btn'><button type='button' class='btn btn-xs btn-primary $disabled' $disabled onClick=\"showJmovingArticleSearchForm('$i','$art_id','$brand_id','$article_nr_displ','$jmoving_id','$storage_id_to');\"><i class=\"fa fa-bars\"></i></button> </span>
                        </div>
                    </td>
                    <td style='min-width:120px;'><input type='hidden' id='brandIdStr_$i' value='$brand_id'>
                        <input class='form-control input-xs' type='text' readonly id='brandNameStr_$i' value='$brand_name' placeholder='Бренд'>
                    </td>
                    <td style='min-width:120px;'>
                        <input type='hidden' id='storageIdFrom_$i' value='$storage_id_from'>
                        <input type='text' readonly id='storageNameFrom_$i' value='$storage_name_from' class='form-control input-xs'>
                    </td>
                    <td>
                        <input type='hidden' id='max_stock_$i' value='$max_stok'>
                        <div class='input-group'>
                            <input type='text' id='amountStr_$i' readonly value='$amount' class='form-control input-xs numberOnly' autocomplete='off'>
                            <span class='input-group-btn'> <button type='button' class='btn btn-xs btn-primary $disabled' $disabled onClick=\"showJmovingArticleAmountChange('$i','$id','$art_id','$amount');\"><i class=\"fa fa-bars\"></i></button></span>
                        </div>
                    </td>
                    <td></td>
                    <td></td>
                    <td><button class='btn btn-xs btn-default $disabled' $disabled onClick='dropJmovingStr(\"$i\",\"$jmoving_id\",\"$id\");'><i class='fa fa-times'></i></button></td>
                </tr>";
            }
            if ($oper_status == 31) {
                if ($article_nr_displ != "") {
                    $list .= "<tr align='center'>
                        <td>$i</td>
                        <td align='left'>$article_nr_displ</td>
                        <td>$brand_name</td>
                        <td>$storage_name_from</td>
                        <td align='right'>".$slave->to_money($amount)."</td>
                        <td align='right'>".$slave->to_money($amount_barcodes+$amount_barcodes_noscan)."</td>
                        <td align='right'>".$slave->to_money($amount_bug)."</td>
                        <td></td>
                    </tr>";
                }
            }
        }
        if ($oper_status == 30) {
            $list = "<input type='hidden' id='kol_row' value='$kl_rw'>
                <tr id='jmovingStrNewRow' class='hidden'>
                    <td>nom_i<input type='hidden' id='idStr_0' value=''></td>
                    <td style='min-width:140px;'><input type='hidden' id='artIdStr_0' value=''>
                        <div class='input-group'>
                            <input class='form-control input-xs' type='text' readonly id='article_nr_displStr_0' value='' placeholder='Індекс товару'>
                            <span class='input-group-btn'> <button type='button' class='btn btn-xs btn-primary' onClick=\"showJmovingArticleSearchForm('i_0','0','0','','$jmoving_id','$storage_id_to');\"><i class=\"fa fa-bars\"></i></button> </span>
                        </div>
                    </td>
                    <td style='min-width:120px;'><input type='hidden' id='brandIdStr_0' value=''>
                        <input class='form-control input-xs' type='text' readonly id='brandNameStr_0' value='' placeholder='Бренд'>
                    </td>
                    <td style='min-width:120px;'>
                        <input type='hidden' id='storageIdFrom_0' value=''>
                        <input type='text' readonly id='storageNameFrom_0' value='' class='form-control input-xs'>
                    </td>
                    <td>
                        <input type='hidden' id='max_stock_0' value=''>
                        <input type='text' id='amountStr_0' value='' readonly class='form-control input-xs numberOnly' autocomplete='off' maxlength=''  min='1' max=''>
                    </td>
                    <td></td>
                    <td></td>
                    <td><button class='btn btn-xs btn-default' onClick='dropJmovingStr(\"i_0\",\"0\");'><i class='fa fa-times'></i></button></td>
                </tr>".$list;
        }
        if ($sum_weight != 0 && $sum_volume != 0 && $oper_status == '30') {
            $db->query("UPDATE `J_MOVING` SET `weight` = '$sum_weight', `volume` = '$sum_volume' WHERE `id` = $jmoving_id AND `oper_status` = '30';");
        }
        return array($list, $n);
    }

    function saveJmovingCard($jmoving_id, $jmoving_op_id, $data, $storage_id_to, $cell_id_to, $comment) { $db = DbSingleton::getDb();
        $slave = new slave;
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $answer = 0; $err = "Помилка збереження даних!";
        $jmoving_id=$slave->qq($jmoving_id); $jmoving_op_id=$slave->qq($jmoving_op_id); $data=$slave->qq($data); $storage_id_to=$slave->qq($storage_id_to); $cell_id_to=$slave->qq($cell_id_to); $comment=$slave->qq($comment);
        if ($jmoving_id == 0 || $jmoving_id == "") {
            $r = $db->query("SELECT MAX(`id`) as mid FROM `J_MOVING`;");
            $jmoving_id = 0 + $db->result($r, 0, "mid") + 1;
            $db->query("INSERT INTO `J_MOVING` (`id`,`jmoving_op_id`,`user_id`) VALUES ('$jmoving_id','$jmoving_op_id','$user_id');");
        }
        if ($jmoving_id > 0) {
            $db->query("UPDATE `J_MOVING` SET `data`='$data', `comment`='$comment', `storage_id_to`='$storage_id_to', `cell_id_to`='$cell_id_to' WHERE `id` = $jmoving_id;");
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

    function saveJmovingCardLocal($jmoving_id, $jmoving_op_id, $data, $storage_id_to, $comment, $kol_row, $idStr, $artIdStr, $cellIdToStr) { $db = DbSingleton::getDb();
        $slave = new slave;
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $answer = 0; $err = "Помилка збереження даних!";
        $jmoving_id=$slave->qq($jmoving_id); $jmoving_op_id=$slave->qq($jmoving_op_id); $data=$slave->qq($data); $storage_id_to=$slave->qq($storage_id_to); $comment=$slave->qq($comment); $kol_row=$slave->qq($kol_row);
        if ($jmoving_id == 0 || $jmoving_id == "") {
            $r = $db->query("SELECT MAX(`id`) as mid FROM `J_MOVING`;");
            $jmoving_id = 0 + $db->result($r, 0, "mid") + 1;
            $db->query("INSERT INTO `J_MOVING` (`id`,`jmoving_op_id`,`user_id`) VALUES ('$jmoving_id','$jmoving_op_id','$user_id');");
        }
        if ($jmoving_id > 0) {
            $db->query("UPDATE `J_MOVING` SET `data`='$data', `comment`='$comment', `storage_id_to`='$storage_id_to' WHERE `id` = $jmoving_id;");
            for ($i = 1; $i <= $kol_row; $i++) {
                $idS = $idStr[$i]; $artIdS = $artIdStr[$i]; $cellIdToS = $cellIdToStr[$i];
                if (($idS == "" || $idS == 0) && ($artIdS != "" && $artIdS > 0)) {
                    $r = $db->query("SELECT MAX(`id`) as mid FROM `J_MOVING_STR`;");
                    $idS = 0 + $db->result($r, 0, "mid") + 1;
                    $db->query("INSERT INTO `J_MOVING_STR` (`id`, `jmoving_id`) VALUES ('$idS', '$jmoving_id');");
                }
                if ($idS > 0 && $artIdS != "" && $artIdS > 0) {
                    $db->query("UPDATE `J_MOVING_STR` SET `cell_id_to` = '$cellIdToS' WHERE `id` = '$idS' AND `jmoving_id` = $jmoving_id;");
                }
            }
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

    function showJmovingStrLocalList($jmoving_id, $oper_status) { $db = DbSingleton::getDb();
        $slave = new slave;
        $list = "";
        if ($oper_status == "") {
            $oper_status = 30;
        }
        $sum_weight=0; $sum_volume=0; $storage_id_from=0;
        $r = $db->query("SELECT * FROM `J_MOVING_STR` WHERE `jmoving_id` = $jmoving_id ORDER BY `id` ASC;");
        $n = $db->num_rows($r);
        $kl_rw=$n;
        for ($i=1;$i<=$kl_rw;$i++) {
            $status_jmoving=0;
            $id="";$art_id="";$article_nr_displ="";$brand_id="";$brand_name="";$amount="";$cell_id_from="";$cell_name_from="";$cell_name_to="";$max_stok="";$cell_to_select_list="";
            if ($i <= $n) {
                $id = $db->result($r,$i-1,"id");
                $art_id = $db->result($r,$i-1,"art_id");
                $article_nr_displ = $db->result($r,$i-1,"article_nr_displ");
                $brand_id = $db->result($r,$i-1,"brand_id");
                $brand_name = $this->getBrandName($brand_id);
                $amount = $db->result($r,$i-1,"amount");
                $storage_id_from = $db->result($r,$i-1,"storage_id_from");
                $cell_id_from = $db->result($r,$i-1,"cell_id_from");
                $cell_name_from = $this->getStorageCellName($cell_id_from);
                $cell_id_to = $db->result($r,$i-1,"cell_id_to");
                list($cell_to_select_list,) = $this->showStorageCellsSelectList($storage_id_from, $cell_id_to);
                $cell_name_to = $this->getStorageCellName($cell_id_to);
                $status_jmoving = $db->result($r,$i-1,"status_jmoving");
            }
            if ($oper_status == 30) {
                list($weight, $volume) = $this->getArticleWightVolume($art_id);
                $sum_weight += ($weight * $amount);
                $sum_volume += ($volume * $amount);
                $disabled = ($status_jmoving != 44 && $status_jmoving > 0) ? " disabled" : "";
                $list .= "<tr id='strRow_$i'>
                    <td>$i<input type='hidden' id='idStr_$i' value='$id'></td>
                    <td style='min-width:140px;'><input type='hidden' id='artIdStr_$i' value='$art_id'>
                        <input class='form-control input-xs' type='text' readonly id='article_nr_displStr_$i' value='$article_nr_displ' placeholder='Індекс товару'>
                    </td>
                    <td style='min-width:120px;'><input type='hidden' id='brandIdStr_$i' value='$brand_id'>
                        <input class='form-control input-xs' type='text' readonly id='brandNameStr_$i' value='$brand_name' placeholder='Бренд'>
                    </td>
                    <td style='min-width:120px;'>
                        <input type='hidden' id='storageIdFrom_$i' value='$storage_id_from' >
                        <input type='hidden' id='cellIdFrom_$i' value='$cell_id_from' >
                        <input type='text' readonly id='cellNameFrom_$i' value='$cell_name_from' class='form-control input-xs'>
                    </td>
                    <td>
                        <input type='hidden' id='max_stock_$i' value='$max_stok'>
                        <div class='input-group'>
                            <input type='text' id='amountStr_$i' readonly value='$amount' class='form-control input-xs numberOnly' autocomplete='off'>
                            <span class='input-group-btn'> <button type='button' class='btn btn-xs btn-primary $disabled' $disabled onClick=\"showJmovingArticleAmountLocalChange('$i','$id','$art_id','$amount');\"><i class=\"fa fa-bars\"></i></button></span>
                        </div>
                    </td>
                    <td style='min-width:120px;'>
                        <select size='1' class='form-control input-xs $disabled' $disabled id='cellIdTo_$i' style='width:100%'>$cell_to_select_list</select>
                    </td>
                    <td><button class='btn btn-xs btn-default $disabled' $disabled onClick='dropJmovingLocalStr(\"$i\",\"$jmoving_id\",\"$id\");'><i class='fa fa-times'></i></button></td>
                </tr>";
            }
            if ($oper_status == 31) {
                if ($article_nr_displ != "") {
                    $list .= "<tr align='center'>
                        <td>$i</td>
                        <td align='left'>$article_nr_displ</td>
                        <td>$brand_name</td>
                        <td>$cell_name_from</td>
                        <td align='right'>" . $slave->to_money($amount) . "</td>
                        <td>$cell_name_to</td>
                        <td></td>
                    </tr>";
                }
            }
        }
        if ($oper_status == 30) {
            $list = "<input type='hidden' id='kol_row' value='$kl_rw'>
                <tr id='jmovingStrNewRow' class='hidden'>
                    <td>nom_i<input type='hidden' id='idStr_0' value=''></td>
                    <td style='min-width:140px;'><input type='hidden' id='artIdStr_0' value=''>
                        <div class='input-group'>
                            <input class='form-control input-xs' type='text' readonly id='article_nr_displStr_0' value='' placeholder='Індекс товару'>
                            <span class='input-group-btn'> <button type='button' class='btn btn-xs btn-primary' onClick=\"showJmovingArticleLocalSearchForm('i_0','0','0','','$jmoving_id','$storage_id_from');\"><i class=\"fa fa-bars\"></i></button> </span>
                        </div>
                    </td>
                    <td style='min-width:120px;'><input type='hidden' id='brandIdStr_0' value=''>
                        <input class='form-control input-xs' type='text' readonly id='brandNameStr_0' value='' placeholder='Бренд'>
                    </td>
                    <td style='min-width:120px;'>
                        <input type='hidden' id='storageIdFrom_0' value=''>
                        <input type='hidden' id='cellIdFrom_0' value=''>
                        <input type='text' readonly id='cellNameFrom_0' value='' class='form-control input-xs'>
                    </td>
                    <td>
                        <input type='hidden' id='max_stock_0' value=''>
                        <div class='input-group'>
                            <input type='text' id='amountStr_0' readonly value='' class='form-control input-xs numberOnly' autocomplete='off'>
                            <span class='input-group-btn'> <button type='button' class='btn btn-xs btn-primary' onClick=\"showJmovingArticleAmountLocalChange('i_0','0','','0');\"><i class=\"fa fa-bars\"></i></button></span>
                        </div>
                    </td>
                    <td style='min-width:120px;'>
                        <select size='1' class='form-control input-xs' id='cellIdTo_0' style='width:100%'></select>
                    </td>
                    <td><button class='btn btn-xs btn-default' onClick='dropJmovingLocalStr(\"i_0\",\"$jmoving_id\",\"0\");'><i class='fa fa-times'></i></button></td>
                </tr>" . $list;
        }
        if ($sum_weight != 0 && $sum_volume != 0 && $oper_status == '30') {
            $db->query("UPDATE `J_MOVING` SET `weight` = '$sum_weight', `volume` = '$sum_volume' WHERE `id` = $jmoving_id AND `oper_status` = '30';");
        }
        return array($list, $n);
    }

    function setArticleToJmoving($jmoving_id, $idStr, $artIdStr, $article_nr_displStr, $brandIdStr, $storageIdFromStr, $cellIdFromStr, $amountStr) { $db = DbSingleton::getDb(); $dbt = DbSingleton::getTokoDb();
        $slave = new slave;
        $answer = 0; $err = "Змінилася кількість. Закрийте вікно введення даних і повторіть ще раз!";
        $jmoving_id = $slave->qq($jmoving_id);
        $rr_amount = $rr_reserv = $empty_kol = $idS = $weight = $volume = 0; $label_empty = "";
        if ($jmoving_id > 0) {
            $idS=$slave->qq($idStr); $artIdS=$slave->qq($artIdStr); $article_nr_displS=$slave->qq($article_nr_displStr);
            $brandIdS=$slave->qq($brandIdStr); $amountS=$slave->qq($amountStr); $storageIdFromS=$slave->qq($storageIdFromStr); $cellIdFromS=$slave->qq($cellIdFromStr);
            list(, $max_moving, $rest_amount) = $this->showArticleRestStorageSelectText($artIdS, $amountS, $storageIdFromS);
            if ($amountS <= $max_moving && $rest_amount == 0) {
                $answer = 0; $err = "Змінилася кількість. Закрийте вікно введення даних і повторіть ще раз!";
            }
            if ($amountS > $max_moving && $rest_amount <= 0) {
                $answer = 0; $err = "Змінилася кількість. Закрийте вікно введення даних і повторіть ще раз!";
            }
            if ($amountS <= $max_moving && $rest_amount > 0) {
                $amountEx = 0;
                $r = $db->query("SELECT `id`, `amount` FROM `J_MOVING_STR` WHERE `jmoving_id` = $jmoving_id AND `art_id` = $artIdS AND `storage_id_from` = $storageIdFromS AND `status_jmoving` = '44' LIMIT 1;");$n = $db->num_rows($r);
                if ($n == 1) {
                    $idS = $db->result($r, 0, "id");
                    $amountEx = $db->result($r, 0, "amount");
                }
                if ($idS == "" || $idS == 0) {
                    $r = $db->query("SELECT MAX(`id`) as mid FROM `J_MOVING_STR`;");
                    $idS = 0 + $db->result($r, 0, "mid") + 1;
                    $db->query("INSERT INTO `J_MOVING_STR` (`id`,`jmoving_id`) VALUES ($idS, $jmoving_id);");
                    $rr_reserv = 0; $amountEx = 0;
                }
                if ($idS > 0) {
                    if ($artIdS != "" && $artIdS > 0 && $article_nr_displS != "") {
                        $amountEx += $amountS;
                        $db->query("UPDATE `J_MOVING_STR` SET `art_id`='$artIdS', `article_nr_displ`='$article_nr_displS', `brand_id`='$brandIdS', `amount`='$amountEx', `storage_id_from`='$storageIdFromS', `cell_id_from`='$cellIdFromS' WHERE `id` = $idS AND `jmoving_id` = $jmoving_id;");
                        $db->query("UPDATE `J_MOVING` SET `status_jmoving`='44' WHERE `id` = $jmoving_id;");

                        list($weight, $volume, $empty_kol) = $this->updateJmovingWeightVolume($jmoving_id);
                        $rr = $dbt->query("SELECT `AMOUNT`, `RESERV_AMOUNT` FROM `T2_ARTICLES_STRORAGE` WHERE `ART_ID` = $artIdS AND `STORAGE_ID` = $storageIdFromS LIMIT 1;");
                        $nr = $dbt->num_rows($rr);
                        if ($nr == 1) {
                            $rr_amount = $dbt->result($rr, 0, "AMOUNT");
                            $rr_reserv = $dbt->result($rr, 0, "RESERV_AMOUNT");
                        }
                        $rr_amount -= $amountS;
                        $rr_reserv += $amountS;
                        $dbt->query("UPDATE `T2_ARTICLES_STRORAGE` SET `AMOUNT`='$rr_amount', `RESERV_AMOUNT`='$rr_reserv' WHERE `ART_ID` = $artIdS AND `STORAGE_ID` = $storageIdFromS;");
                    }
                }
                list($empty_kol, $label_empty) = $this->labelArtEmptyCount($jmoving_id, $empty_kol);
                $answer = 1; $err = "";
            }
        }
        return array($answer, $err, $idS, "сток: $rr_amount | резерв: $rr_reserv", $weight, $volume, $empty_kol, $label_empty);
    }

    function setArticleToJmovingLocal($jmoving_id, $idStr, $artIdStr, $article_nr_displStr, $brandIdStr, $storageId, $cell_from_move, $cell_to_move, $amountStr) { $db = DbSingleton::getDb(); $dbt = DbSingleton::getTokoDb();
        $slave = new slave;
        $answer = 0; $err = "Змінилася кількість. Закрийте вікно введення даних і повторіть ще раз!";
        $jmoving_id=$slave->qq($jmoving_id);
        $rr_amount=$rr_reserv=$empty_kol=$idS=$weight=$volume=0;$label_empty="";
        if ($jmoving_id>0){
            $idS=$slave->qq($idStr);$artIdS=$slave->qq($artIdStr);$article_nr_displS=$slave->qq($article_nr_displStr);$brandIdS=$slave->qq($brandIdStr);
            $amountS=$slave->qq($amountStr);$storageIdS=$slave->qq($storageId);$cell_from_moveS=$slave->qq($cell_from_move);$cell_to_moveS=$slave->qq($cell_to_move);
            list(,$max_moving,$rest_amount)=$this->showArticleRestStorageCellSelectText($artIdS,$amountS,$cell_from_moveS);
            if ($amountS<=$max_moving && $rest_amount==0){$answer = 0; $err = "Змінилася кількість. Закрийте вікно введення даних і повторіть ще раз!";}
            if ($amountS>$max_moving && $rest_amount<=0){$answer = 0; $err = "Змінилася кількість. Закрийте вікно введення даних і повторіть ще раз!";}
            if ($amountS<=$max_moving && $rest_amount>0){
                $amountEx=0;
                $r = $db->query("SELECT `id`, `amount` FROM `J_MOVING_STR` WHERE `jmoving_id` = $jmoving_id AND `art_id`='$artIdS' AND `storage_id_from`='$storageIdS' AND `cell_id_from`='$cell_from_moveS' AND `cell_id_to`='$cell_to_moveS' AND `status_jmoving`='44' LIMIT 1;");$n = $db->num_rows($r);
                if ($n == 1){
                    $idS=$db->result($r,0,"id");
                    $amountEx=$db->result($r,0,"amount");
                }
                if ($idS=="" || $idS==0){
                    $r = $db->query("SELECT MAX(`id`) as mid FROM `J_MOVING_STR`;");
                    $idS=0+$db->result($r,0,"mid")+1;
                    $db->query("INSERT INTO `J_MOVING_STR` (`id`,`jmoving_id`) VALUES ('$idS','$jmoving_id');");
                    $rr_reserv=0;$amountEx=0;
                }
                if ($idS>0){
                    if ($artIdS!="" && $artIdS>0 && $article_nr_displS!=""){
                        $amountEx+=$amountS;
                        $db->query("UPDATE `J_MOVING_STR` SET `art_id`='$artIdS', `article_nr_displ`='$article_nr_displS', `brand_id`='$brandIdS', `amount`='$amountEx', `storage_id_from`='$storageId', `storage_id_to`='$storageId', `cell_id_from`='$cell_from_moveS', `cell_id_to`='$cell_to_moveS' WHERE `id`='$idS' AND `jmoving_id` = $jmoving_id;");
                        $db->query("UPDATE `J_MOVING` SET `status_jmoving`='44' WHERE `id` = $jmoving_id;");

                        list($weight,$volume,$empty_kol)=$this->updateJmovingWeightVolume($jmoving_id);

                        //STORAGE SET RESERV
                        $rr=$dbt->query("SELECT * FROM `T2_ARTICLES_STRORAGE` WHERE `ART_ID`='$artIdS' AND `STORAGE_ID`='$storageIdS' LIMIT 1;");
                        $nr=$dbt->num_rows($rr);
                        if ($nr==1){
                            $rr_amount=$dbt->result($rr,0,"AMOUNT");
                            $rr_reserv=$dbt->result($rr,0,"RESERV_AMOUNT");
                        }
                        $rr_amount-=$amountS;$rr_reserv+=$amountS;
                        $dbt->query("UPDATE `T2_ARTICLES_STRORAGE` SET `AMOUNT`='$rr_amount', `RESERV_AMOUNT`='$rr_reserv' WHERE `ART_ID`='$artIdS' AND `STORAGE_ID`='$storageIdS';");

                        //CELL SET RESERV
                        $rr=$dbt->query("SELECT * FROM `T2_ARTICLES_STRORAGE_CELLS` WHERE `ART_ID`='$artIdS' AND `STORAGE_ID`='$storageIdS' AND `STORAGE_CELLS_ID`='$cell_from_moveS' LIMIT 1;");
                        $nr=$dbt->num_rows($rr);
                        if ($nr==1){
                            $rr_amount=$dbt->result($rr,0,"AMOUNT");
                            $rr_reserv=$dbt->result($rr,0,"RESERV_AMOUNT");
                        }
                        $rr_amount-=$amountS;$rr_reserv+=$amountS;
                        $dbt->query("UPDATE `T2_ARTICLES_STRORAGE_CELLS` SET `AMOUNT`='$rr_amount', `RESERV_AMOUNT`='$rr_reserv' 
                        WHERE `ART_ID`='$artIdS' AND `STORAGE_ID`='$storageIdS' AND `STORAGE_CELLS_ID`='$cell_from_moveS';");
                    }
                }
                list($empty_kol,$label_empty)=$this->labelArtEmptyCount($jmoving_id,$empty_kol);
                $answer = 1; $err = "";
            }
        }
        return array($answer,$err,$idS,"сток: $rr_amount | резерв: $rr_reserv",$weight,$volume,$empty_kol,$label_empty);
    }

    function changeArticleToJmoving($jmoving_id,$jmoving_str_id,$amount_change) { $db = DbSingleton::getDb();$dbt = DbSingleton::getTokoDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка збереження даних!";
        $jmoving_id=$slave->qq($jmoving_id);
        $weight=$volume=0;
        if ($jmoving_id>0) {
            $jmoving_str_id=$slave->qq($jmoving_str_id);$amount_change=$slave->qq($amount_change);
            $r = $db->query("SELECT `amount`, `art_id`, `storage_id_from` FROM `J_MOVING_STR` WHERE `jmoving_id` = $jmoving_id AND `id` = $jmoving_str_id AND `status_jmoving`='44' LIMIT 1;");
            $n = $db->num_rows($r);
            if ($n == 1) {
                $amountEx=$db->result($r,0,"amount");
                $art_id=$db->result($r,0,"art_id");
                $storage_id_from=$db->result($r,0,"storage_id_from");
                $amountS=$amount_change;
                list(,$max_moving,$rest_amount)=$this->showArticleRestStorageSelectText($art_id,$amountS,$storage_id_from);
                if ($amountS<=$max_moving && $rest_amount==0){$answer = 0; $err = "Кількість для переміщення вже більша за залишок!";}
                if ($amountS>$max_moving && $rest_amount<=0){$answer = 0; $err = "Кількість для переміщення вже більша за залишок!";}
                if ($amountS<=($max_moving+$amountEx)){
                    $db->query("UPDATE `J_MOVING_STR` SET `amount`='$amount_change' WHERE `id` = $jmoving_str_id AND `jmoving_id` = $jmoving_id LIMIT 1;");
                    list($weight,$volume,)=$this->updateJmovingWeightVolume($jmoving_id);
                    $rr=$dbt->query("SELECT * FROM `T2_ARTICLES_STRORAGE` WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id_from LIMIT 1;");
                    $nr=$dbt->num_rows($rr);
                    if ($nr==1){
                        $rr_amount=$dbt->result($rr,0,"AMOUNT");
                        $rr_reserv=$dbt->result($rr,0,"RESERV_AMOUNT");
                        $rr_amount=$rr_amount+$amountEx-$amount_change;
                        $rr_reserv=$rr_reserv-$amountEx+$amount_change;
                        $dbt->query("UPDATE `T2_ARTICLES_STRORAGE` SET `AMOUNT`='$rr_amount', `RESERV_AMOUNT`='$rr_reserv' WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id_from;");
                    }
                    $answer = 1; $err = "";
                }
            }
        }
        return array($answer, $err, $weight, $volume);
    }

    function changeArticleToJmovingLocal($jmoving_id, $jmoving_str_id, $amount_change) { $db = DbSingleton::getDb(); $dbt = DbSingleton::getTokoDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка збереження даних!";
        $jmoving_id = $slave->qq($jmoving_id);
        $weight = $volume = 0;
        if ($jmoving_id > 0) {
            $jmoving_str_id=$slave->qq($jmoving_str_id); $amount_change=$slave->qq($amount_change);
            $r = $db->query("SELECT `amount`, `art_id`, `storage_id_from`, `cell_id_from` FROM `J_MOVING_STR` WHERE `jmoving_id` = $jmoving_id AND `id` = $jmoving_str_id AND `status_jmoving`='44' LIMIT 1;");
            $n = $db->num_rows($r);
            if ($n == 1) {
                $amountEx=$db->result($r,0,"amount");
                $art_id=$db->result($r,0,"art_id");
                $storage_id_from=$db->result($r,0,"storage_id_from");
                $cell_id_from=$db->result($r,0,"cell_id_from");

                list(, $max_moving, $rest_amount) = $this->showArticleRestStorageCellSelectText($art_id, $amount_change, $cell_id_from);

                if ($amount_change<=$max_moving && $rest_amount<0) {
                    $answer = 0; $err = "Змінилася кількість. Закрийте вікно введення даних і повторіть ще раз!";
                }
                if ($amount_change>$max_moving && $rest_amount<=0) {
                    $answer = 0; $err = "Змінилася кількість. Закрийте вікно введення даних і повторіть ще раз!";
                }
                if ($amount_change<=$max_moving && $rest_amount>=0) {
                    $db->query("UPDATE `J_MOVING_STR` SET `amount`='$amount_change' WHERE `id` = $jmoving_str_id AND `jmoving_id` = $jmoving_id LIMIT 1;");
                    list($weight, $volume) = $this->updateJmovingWeightVolume($jmoving_id);
                    $rr = $dbt->query("SELECT `AMOUNT`, `RESERV_AMOUNT` FROM `T2_ARTICLES_STRORAGE` WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id_from LIMIT 1;");
                    $nr = $dbt->num_rows($rr);
                    if ($nr == 1) {
                        $rr_amount = $dbt->result($rr, 0, "AMOUNT");
                        $rr_reserv = $dbt->result($rr, 0, "RESERV_AMOUNT");
                        $rr_amount = $rr_amount + $amountEx - $amount_change;
                        $rr_reserv = $rr_reserv - $amountEx + $amount_change;
                        $dbt->query("UPDATE `T2_ARTICLES_STRORAGE` SET `AMOUNT`='$rr_amount', `RESERV_AMOUNT`='$rr_reserv' WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id_from;");
                    }
                    $rr = $dbt->query("SELECT * FROM `T2_ARTICLES_STRORAGE_CELLS` WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id_from AND `STORAGE_CELLS_ID` = $cell_id_from LIMIT 1;");
                    $nr = $dbt->num_rows($rr);
                    if ($nr==1) {
                        $rr_amount=$dbt->result($rr, 0, "AMOUNT");
                        $rr_reserv=$dbt->result($rr, 0, "RESERV_AMOUNT");
                        $rr_amount=$rr_amount+$amountEx-$amount_change;
                        $rr_reserv=$rr_reserv-$amountEx+$amount_change;
                        $dbt->query("UPDATE `T2_ARTICLES_STRORAGE_CELLS` SET `AMOUNT`='$rr_amount', `RESERV_AMOUNT`='$rr_reserv' 
                        WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id_from AND `STORAGE_CELLS_ID` = $cell_id_from;");
                    }
                    $answer = 1; $err = "";
                }
            }
        }
        return array($answer, $err, $weight, $volume);
    }

    function dropJmovingStr($jmoving_id, $jmoving_str_id) { $db = DbSingleton::getDb(); $dbt = DbSingleton::getTokoDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка індексу";
        $jmoving_id = $slave->qq($jmoving_id);
        $r = $db->query("SELECT `oper_status`, `status_jmoving` FROM `J_MOVING` WHERE `id` = $jmoving_id LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $status_jmoving = $db->result($r, 0, "status_jmoving");
            $oper_status = $db->result($r, 0, "oper_status");
            if ($oper_status == 30 && ($status_jmoving == 44 ||$status_jmoving == 45)) {
                $r1 = $db->query("SELECT `status_jmoving`, `art_id`, `amount`, `storage_id_from` FROM `J_MOVING_STR` WHERE `id` = $jmoving_str_id LIMIT 1;");
                $n1 = $db->num_rows($r1);
                if ($n1 == 1) {
                    $status_jmoving_str = $db->result($r1, 0, "status_jmoving");
                    if ($status_jmoving_str == 44) {
                        $art_id = $db->result($r1, 0, "art_id");
                        $amount = $db->result($r1, 0, "amount");
                        $storage_id_from = $db->result($r1, 0, "storage_id_from");
                        $rs = $dbt->query("SELECT * FROM `T2_ARTICLES_STRORAGE` WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id_from LIMIT 1;");
                        $ns = $dbt->num_rows($rs);
                        if ($ns == 1) {
                            $reserv_amount_s = $dbt->result($rs, 0, "RESERV_AMOUNT");
                            $amount_s = $dbt->result($rs, 0, "AMOUNT");
                            $reserv_amount_s -= $amount;
                            $amount_s += $amount;
                            $dbt->query("UPDATE `T2_ARTICLES_STRORAGE` SET `AMOUNT` = '$amount_s', `RESERV_AMOUNT` = '$reserv_amount_s' WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id_from LIMIT 1;");
                            $db->query("DELETE FROM `J_MOVING_STR` WHERE `id` = $jmoving_str_id AND `jmoving_id` = $jmoving_id LIMIT 1;");
                            $this->updateJmovingWeightVolume($jmoving_id);
                            $answer = 1; $err = "";
                        }
                    } else {
                        $answer = 0; $err = "Видалення заблоковано. Відбір передано в роботу.";
                    }
                }
            } else {
                $answer = 0; $err = "Видалення заблоковано. Переміщення передано в роботу.";
            }
        }
        return array($answer, $err);
    }

    function dropJmovingLocalStr($jmoving_id, $jmoving_str_id) { $db = DbSingleton::getDb(); $dbt = DbSingleton::getTokoDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка індексу";
        $jmoving_id = $slave->qq($jmoving_id);
        $r = $db->query("SELECT `oper_status`, `status_jmoving` FROM `J_MOVING` WHERE `id` = $jmoving_id LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $status_jmoving = $db->result($r, 0, "status_jmoving");
            $oper_status = $db->result($r, 0, "oper_status");
            if ($oper_status == 30 && ($status_jmoving == 44 || $status_jmoving == 45)) {
                $r1 = $db->query("SELECT `status_jmoving`, `art_id`, `amount`, `storage_id_from`, `cell_id_from` FROM `J_MOVING_STR` WHERE `id` = $jmoving_str_id LIMIT 1;");
                $n1 = $db->num_rows($r1);
                if ($n1 == 1) {
                    $status_jmoving_str = $db->result($r1, 0, "status_jmoving");
                    if ($status_jmoving_str == 44) {
                        $art_id = $db->result($r1, 0, "art_id");
                        $amount = $db->result($r1, 0, "amount");
                        $storage_id_from = $db->result($r1, 0, "storage_id_from");
                        $cell_id_from = $db->result($r1, 0, "cell_id_from");
                        $rs = $dbt->query("SELECT `RESERV_AMOUNT`, `AMOUNT` FROM `T2_ARTICLES_STRORAGE` WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id_from LIMIT 1;");
                        $ns = $dbt->num_rows($rs);
                        if ($ns == 1) {
                            $reserv_amount_s=$dbt->result($rs, 0, "RESERV_AMOUNT");
                            $amount_s = $dbt->result($rs, 0, "AMOUNT");
                            $reserv_amount_s -= $amount;
                            $amount_s += $amount;
                            $dbt->query("UPDATE `T2_ARTICLES_STRORAGE` SET `AMOUNT`='$amount_s', `RESERV_AMOUNT`='$reserv_amount_s' WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id_from LIMIT 1;");
                        }
                        $rs = $dbt->query("SELECT `RESERV_AMOUNT`, `AMOUNT` FROM `T2_ARTICLES_STRORAGE_CELLS` WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id_from AND `STORAGE_CELLS_ID` = $cell_id_from LIMIT 1;");
                        $ns = $dbt->num_rows($rs);
                        if ($ns == 1) {
                            $reserv_amount_s=$dbt->result($rs, 0, "RESERV_AMOUNT");
                            $amount_s = $dbt->result($rs, 0, "AMOUNT");
                            $reserv_amount_s -= $amount;
                            $amount_s += $amount;
                            $dbt->query("UPDATE `T2_ARTICLES_STRORAGE_CELLS` SET `AMOUNT`='$amount_s', `RESERV_AMOUNT`='$reserv_amount_s' WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id_from AND `STORAGE_CELLS_ID` = $cell_id_from LIMIT 1;");
                        }
                        $db->query("DELETE FROM `J_MOVING_STR` WHERE `id` = $jmoving_str_id AND `jmoving_id` = $jmoving_id LIMIT 1;");
                        $this->updateJmovingWeightVolume($jmoving_id);
                        $answer = 1; $err = "";
                    } else {
                        $answer = 0; $err = "Видалення заблоковано. Відбір передано в роботу.";
                    }
                }
            } else {
                $answer = 0; $err = "Видалення заблоковано. Переміщення передано в роботу.";
            }
        }
        return array($answer, $err);
    }

    function updateJmovingWeightVolume($jmoving_id) { $db = DbSingleton::getDb();
        $art_ar = array();
        $sum_weight = 0; $sum_volume = 0; $empty_kol = 0;
        $r = $db->query("SELECT `art_id`, `amount` FROM `J_MOVING_STR` WHERE `jmoving_id` = $jmoving_id;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $art_id = $db->result($r, $i - 1, "art_id");
            $amount = $db->result($r, $i - 1, "amount");
            list($weight, $volume) = $this->getArticleWightVolume($art_id);
            if ($weight == 0 || $volume == 0) {
                if (!in_array($art_id, $art_ar)) {
                    $empty_kol += 1;
                    array_push($art_ar, $art_id);
                }
            }
            if ($weight > 0 && $amount > 0) {
                $sum_weight += ($weight * $amount);
            }
            if ($volume > 0 && $amount > 0) {
                $sum_volume += ($volume * $amount);
            }
        }
        if ($n > 0) {
            $db->query("UPDATE `J_MOVING` SET `weight` = '$sum_weight', `volume` = '$sum_volume' WHERE `id` = $jmoving_id AND `oper_status` = '30' AND `status` = '1';");
        }
        return array($sum_weight, $sum_volume, $empty_kol);
    }

    function makeJmovingCardFinish() {
        $answer = 0; $err = "";
        return array($answer, $err);
    }

    function showJmovingLocalAutoCellForm($jmoving_id, $storage_id_to)
    {
        $form = ""; $form_htm = RD . "/tpl/jmoving_local_auto_cell_form.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $form = str_replace("{jmoving_id}", $jmoving_id, $form);
        list($cells_list,)      = $this->showStorageCellsSelectList($storage_id_to, 0);
        list($cells_list_to,)   = $this->showStorageCellsSelectList($storage_id_to, 0);

        $form = str_replace("{cells_list_from}", $cells_list, $form);
        $form = str_replace("{cells_list_from2}", $this->showStorageCellsSelecedtList($jmoving_id, $storage_id_to), $form);
        $form = str_replace("{cells_list_to}", $cells_list_to, $form);
        $form = str_replace("{storage_name_to}", $this->getStorageName($storage_id_to), $form);
        $form = str_replace("{storage_id_to}", $storage_id_to, $form);
        return $form;
    }

    function showStorageCellsSelecedtList($jmoving_id, $storage_id)
    {
        $db = DbSingleton::getDb();
        $dbt = DbSingleton::getTokoDb();

        $list = "<option value='0'>Оберіть зі списку</option>";
        $cells = [];
        $r = $db->query("SELECT `cell_id_from` FROM `J_MOVING_STR` WHERE `jmoving_id` = $jmoving_id;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $cell_id_from   = $db->result($r, $i - 1, "cell_id_from");
            $cells[]        = $cell_id_from;
        }
        $cells = array_unique($cells);

        $r = $dbt->query("SELECT * FROM `STORAGE_CELLS` WHERE `status`='1' AND `storage_id`='$storage_id' ORDER BY `cell_value` ASC, `id` ASC;");
        $n = $dbt->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id         = $dbt->result($r, $i - 1, "id");
            $cell_value = $dbt->result($r, $i - 1, "cell_value");

            $sel = "";
            if (in_array($id, $cells)) {
                $sel = "selected='selected'";
            }
            $list       .= "
            <option value='$id' $sel>$cell_value</option>";
        }
        return $list;
    }

    function saveJmovingLocalAutoCell2($jmoving_id, $storage_id_to, $cell_ids_from = [], $cell_id_to = 0)
    {
        $db = DbSingleton::getDb();
        $dbt = DbSingleton::getTokoDb();
        $slave = new slave;

        $jmoving_id     = $slave->qq($jmoving_id);
        $storage_id_to  = $slave->qq($storage_id_to);
        $cell_id_to     = $slave->qq($cell_id_to);

        $kol_row = $amountEx = $rr_amount = $rr_reserv = 0;

        $count_ids = count($cell_ids_from);
        $count_ids_err = [];
        $count_ids_art = [];

        foreach ($cell_ids_from as $cell_id_from) {

            if ($cell_id_from == $cell_id_to) {
                $count_ids_err[] = $cell_id_from;
            }

            if ($jmoving_id > 0 && $storage_id_to > 0 && $cell_id_from > 0 && ($cell_id_from != $cell_id_to)) {

                $db->query("UPDATE `J_MOVING` SET `storage_id_to` = $storage_id_to WHERE `id` = $jmoving_id;");

                $rc = $dbt->query("SELECT `ART_ID`, `AMOUNT` FROM `T2_ARTICLES_STRORAGE_CELLS` WHERE `STORAGE_ID` = $storage_id_to AND `STORAGE_CELLS_ID` = $cell_id_from;");
                $nc = $dbt->num_rows($rc);
                for ($ic = 1; $ic <= $nc; $ic++) {
                    $art_id     = $dbt->result($rc, $ic - 1, "ART_ID");
                    $amountS    = $dbt->result($rc, $ic - 1, "AMOUNT");

                    list($article_nr_displ, $brand_id,) = $this->getArticleNrDisplBrand($art_id);

                    $r = $db->query("SELECT `id`, `amount` FROM `J_MOVING_STR` WHERE `jmoving_id` = $jmoving_id AND `art_id` = $art_id AND `storage_id_from` = $storage_id_to AND `status_jmoving`='44' LIMIT 1;");
                    $n = $db->num_rows($r);

                    $idS = "";
                    if ($n == 1) {
                        $idS        = $db->result($r, 0, "id");
                        $amountEx   = $db->result($r, 0, "amount");
                    }

                    if ($idS == "" || $idS == 0) {
                        $r = $db->query("SELECT MAX(`id`) as mid FROM `J_MOVING_STR`;");
                        $idS = 0 + $db->result($r, 0, "mid") + 1;
                        $db->query("INSERT INTO `J_MOVING_STR` (`id`, `jmoving_id`) VALUES ($idS, $jmoving_id);");

                        $rr_reserv  = 0;
                        $amountEx   = 0;
                    }

                    if ($idS > 0) {
                        if ($art_id != "" && $art_id > 0 && $article_nr_displ != "") {
                            $kol_row    += 1;
                            $amountEx   += $amountS;
                            $db->query("UPDATE `J_MOVING_STR` SET `art_id` = $art_id, `article_nr_displ` = '$article_nr_displ', `brand_id` = $brand_id, `amount` = '$amountEx', `storage_id_from` = $storage_id_to, `cell_id_from` = $cell_id_from, `cell_id_to` = $cell_id_to WHERE `id` = $idS AND `jmoving_id` = $jmoving_id LIMIT 1;");
                            $db->query("UPDATE `J_MOVING` SET `status_jmoving` = '44' WHERE `id` = $jmoving_id LIMIT 1;");

                            $this->updateJmovingWeightVolume($jmoving_id);

                            $rr = $dbt->query("SELECT `AMOUNT`, `RESERV_AMOUNT` FROM `T2_ARTICLES_STRORAGE_CELLS` WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id_to AND `STORAGE_CELLS_ID` = $cell_id_from LIMIT 1;");
                            $nr = $dbt->num_rows($rr);

                            if ($nr == 1) {
                                $rr_amount = $dbt->result($rr, 0, "AMOUNT");
                                $rr_reserv = $dbt->result($rr, 0, "RESERV_AMOUNT");
                            }
                            $rr_amount -= $amountS;
                            $rr_reserv += $amountS;

                            $dbt->query("UPDATE `T2_ARTICLES_STRORAGE_CELLS` SET `AMOUNT` = '$rr_amount', `RESERV_AMOUNT` = '$rr_reserv' WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id_to AND `STORAGE_CELLS_ID` = $cell_id_from LIMIT 1;");

                            $rr = $dbt->query("SELECT `AMOUNT`, `RESERV_AMOUNT` FROM `T2_ARTICLES_STRORAGE` WHERE `ART_ID`='$art_id' AND `STORAGE_ID`='$storage_id_to' LIMIT 1;");
                            $nr = $dbt->num_rows($rr);

                            if ($nr == 1) {
                                $rr_amount = $dbt->result($rr, 0, "AMOUNT");
                                $rr_reserv = $dbt->result($rr, 0, "RESERV_AMOUNT");
                            }
                            $rr_amount -= $amountS;
                            $rr_reserv += $amountS;

                            $dbt->query("UPDATE `T2_ARTICLES_STRORAGE` SET `AMOUNT` = '$rr_amount', `RESERV_AMOUNT` = '$rr_reserv' WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id_to LIMIT 1;");
                        }
                    }
                }

                if ($kol_row == 0) {
                    $count_ids_art[] = $cell_id_from;
                }

            }
        }

        $answer = 1; $err = ""; $text = "";

        if (count($count_ids_art) == $count_ids) {
            $answer = 0;
            $err = "Немає товару на переміщення!\n";
        }

        if (count($count_ids_err) == $count_ids) {
            $answer = 0;
            $err = "Виберіть різні комірки для переміщення та розміщення!\n";
        }

        if ($count_ids == 0) {
            $answer = 0;
            $err = "Виберіть комірки розміщення!\n";
        }

        if ($count_ids_art > 0) {
            foreach ($count_ids_art as $cf) {
                $cfn = $this->getStorageCellName($cf);
                $text .= "В комірці '$cfn' не має товару\n";
            }
        }

        if ($count_ids_err > 0) {
            foreach ($count_ids_err as $cf) {
                $cfn = $this->getStorageCellName($cf);
                $text .= "Комірка '$cfn' співпадає з коміркою переміщення\n";
            }
        }

        return array($answer, $err, $text);
    }

    function saveJmovingLocalAutoCell($jmoving_id, $storage_id_to, $cell_id_from, $cell_id_to)
    {
        $db = DbSingleton::getDb();
        $dbt = DbSingleton::getTokoDb();
        $slave = new slave;

        $jmoving_id     = $slave->qq($jmoving_id);
        $storage_id_to  = $slave->qq($storage_id_to);
        $cell_id_from   = $slave->qq($cell_id_from);
        $cell_id_to     = $slave->qq($cell_id_to);

        $answer = 0; $err = "Помилка збереження даних!";
        $kol_row = $amountEx = $rr_amount = $rr_reserv = $no_row = 0;

        if ($cell_id_from == $cell_id_to) {
            $err = "Виберіть різні комірки для переміщення та розміщення!";
        }
        if ($jmoving_id > 0 && $storage_id_to > 0 && $cell_id_from > 0 && ($cell_id_from != $cell_id_to)) {
            $db->query("UPDATE `J_MOVING` SET `storage_id_to` = $storage_id_to WHERE `id` = $jmoving_id;");
            $rc = $dbt->query("SELECT `ART_ID`, `AMOUNT` FROM `T2_ARTICLES_STRORAGE_CELLS` WHERE `STORAGE_ID` = $storage_id_to AND `STORAGE_CELLS_ID` = $cell_id_from;");
            $nc = $dbt->num_rows($rc);
            for ($ic = 1; $ic <= $nc; $ic++) {
                $art_id     = $dbt->result($rc, $ic - 1, "ART_ID");
                $amountS    = $dbt->result($rc, $ic - 1, "AMOUNT");

                list($article_nr_displ, $brand_id,) = $this->getArticleNrDisplBrand($art_id);
                $idS = "";
                $r = $db->query("SELECT `id`, `amount` FROM `J_MOVING_STR` WHERE `jmoving_id` = $jmoving_id AND `art_id` = $art_id AND `storage_id_from` = $storage_id_to AND `status_jmoving`='44' LIMIT 1;");
                $n = $db->num_rows($r);
                if ($n == 1) {
                    $idS        = $db->result($r, 0, "id");
                    $amountEx   = $db->result($r, 0, "amount");
                }
                if ($idS == "" || $idS == 0) {
                    $r = $db->query("SELECT MAX(`id`) as mid FROM `J_MOVING_STR`;");
                    $idS = 0 + $db->result($r, 0, "mid") + 1;
                    $db->query("INSERT INTO `J_MOVING_STR` (`id`, `jmoving_id`) VALUES ($idS, $jmoving_id);");

                    $rr_reserv  = 0;
                    $amountEx   = 0;
                }
                if ($idS > 0) {
                    if ($art_id != "" && $art_id > 0 && $article_nr_displ != "") {
                        $kol_row    += 1;
                        $amountEx   += $amountS;
                        $db->query("UPDATE `J_MOVING_STR` SET `art_id` = $art_id, `article_nr_displ` = '$article_nr_displ', `brand_id` = $brand_id, `amount` = '$amountEx', `storage_id_from` = $storage_id_to, `cell_id_from` = $cell_id_from, `cell_id_to` = $cell_id_to WHERE `id` = $idS AND `jmoving_id` = $jmoving_id LIMIT 1;");
                        $db->query("UPDATE `J_MOVING` SET `status_jmoving` = '44' WHERE `id` = $jmoving_id LIMIT 1;");

                        $this->updateJmovingWeightVolume($jmoving_id);

                        $rr = $dbt->query("SELECT `AMOUNT`, `RESERV_AMOUNT` FROM `T2_ARTICLES_STRORAGE_CELLS` WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id_to AND `STORAGE_CELLS_ID` = $cell_id_from LIMIT 1;");
                        $nr = $dbt->num_rows($rr);
                        if ($nr == 1) {
                            $rr_amount = $dbt->result($rr, 0, "AMOUNT");
                            $rr_reserv = $dbt->result($rr, 0, "RESERV_AMOUNT");
                        }
                        $rr_amount -= $amountS;
                        $rr_reserv += $amountS;
                        $dbt->query("UPDATE `T2_ARTICLES_STRORAGE_CELLS` SET `AMOUNT` = '$rr_amount', `RESERV_AMOUNT` = '$rr_reserv' WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id_to AND `STORAGE_CELLS_ID` = $cell_id_from LIMIT 1;");

                        $rr = $dbt->query("SELECT `AMOUNT`, `RESERV_AMOUNT` FROM `T2_ARTICLES_STRORAGE` WHERE `ART_ID`='$art_id' AND `STORAGE_ID`='$storage_id_to' LIMIT 1;");
                        $nr = $dbt->num_rows($rr);
                        if ($nr == 1) {
                            $rr_amount = $dbt->result($rr, 0, "AMOUNT");
                            $rr_reserv = $dbt->result($rr, 0, "RESERV_AMOUNT");
                        }
                        $rr_amount -= $amountS;
                        $rr_reserv += $amountS;
                        $dbt->query("UPDATE `T2_ARTICLES_STRORAGE` SET `AMOUNT` = '$rr_amount', `RESERV_AMOUNT` = '$rr_reserv' WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id_to LIMIT 1;");
                    }
                }
            }
            $answer = 1; $err = ""; $no_row = 1;
            if ($kol_row > 0) {
                $no_row = 0;
            }
        }
        return array($answer,$err,$no_row);
    }

    function clearJmovingLocalAutoCellForm($jmoving_id)
    {
        $db = DbSingleton::getDb();
        $dbt = DbSingleton::getTokoDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка індексу";
        $jmoving_id = $slave->qq($jmoving_id);

        $r = $db->query("SELECT `oper_status`, `status_jmoving` FROM `J_MOVING` WHERE `id` = $jmoving_id LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $status_jmoving = $db->result($r, 0, "status_jmoving");
            $oper_status = $db->result($r, 0, "oper_status");
            if ($oper_status == 30 && ($status_jmoving == 44 ||$status_jmoving == 45)) {
                $r1 = $db->query("SELECT `id`, `status_jmoving`, `art_id`, `amount`, `storage_id_from`, `cell_id_from` FROM `J_MOVING_STR` WHERE `jmoving_id` = $jmoving_id;");
                $n1 = $db->num_rows($r1);
                for ($i1 = 1; $i1 <= $n1; $i1++) {
                    $jmoving_str_id = $db->result($r1, $i1 - 1, "id");
                    $status_jmoving_str = $db->result($r1, $i1 - 1, "status_jmoving");
                    if ($status_jmoving_str == 44) {
                        $art_id = $db->result($r1, $i1 - 1, "art_id");
                        $amount = $db->result($r1, $i1 - 1, "amount");
                        $storage_id_from = $db->result($r1, $i1 - 1, "storage_id_from");
                        $cell_id_from = $db->result($r1, $i1 - 1, "cell_id_from");
                        $rs = $dbt->query("SELECT `RESERV_AMOUNT`, `AMOUNT` FROM `T2_ARTICLES_STRORAGE` WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id_from LIMIT 1;");
                        $ns = $dbt->num_rows($rs);
                        if ($ns == 1) {
                            $reserv_amount_s = $dbt->result($rs, 0, "RESERV_AMOUNT");
                            $amount_s = $dbt->result($rs, 0, "AMOUNT");
                            $reserv_amount_s -= $amount;
                            $amount_s += $amount;
                            $dbt->query("UPDATE `T2_ARTICLES_STRORAGE` SET `AMOUNT` = '$amount_s', `RESERV_AMOUNT` = '$reserv_amount_s' WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id_from LIMIT 1;");
                        }
                        $rs = $dbt->query("SELECT `RESERV_AMOUNT`, `AMOUNT` FROM `T2_ARTICLES_STRORAGE_CELLS` WHERE `ART_ID` = $art_id and `STORAGE_ID` = $storage_id_from AND `STORAGE_CELLS_ID` = $cell_id_from LIMIT 1;");
                        $ns = $dbt->num_rows($rs);
                        if ($ns == 1) {
                            $reserv_amount_s = $dbt->result($rs, 0, "RESERV_AMOUNT");
                            $amount_s = $dbt->result($rs, 0, "AMOUNT");
                            $reserv_amount_s -= $amount;
                            $amount_s += $amount;
                            $dbt->query("UPDATE `T2_ARTICLES_STRORAGE_CELLS` SET `AMOUNT` = '$amount_s', `RESERV_AMOUNT` = '$reserv_amount_s' WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id_from AND `STORAGE_CELLS_ID` = $cell_id_from LIMIT 1;");
                        }
                        $db->query("DELETE FROM `J_MOVING_STR` WHERE `id` = $jmoving_str_id AND `jmoving_id` = $jmoving_id LIMIT 1;");
                        $this->updateJmovingWeightVolume($jmoving_id);
                        $answer = 1; $err = "";
                    } else {
                        $answer = 0; $err = "Видалення заблоковано. Відбір передано в роботу.";
                    }
                }
            } else {
                $answer = 0; $err = "Видалення заблоковано. Переміщення передано в роботу.";
            }
        }
        return array($answer, $err);
    }

    function showJmovingArticleSearchForm($brand_id, $article_nr_display, $jmoving_id) {
        $form = ""; $form_htm = RD . "/tpl/jmoving_artilce_search_form.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $range_list = "";
        $list_brand_select = "";
        if ($article_nr_display != "") {
            list($range_list, $list_brand_select) = $this->showArticlesSearchDocumentList($article_nr_display, $brand_id, 0, $jmoving_id);
        }
        $form = str_replace("{article_nr_display}", $article_nr_display, $form);
        $form = str_replace("{range_list}", $range_list, $form);
        $form = str_replace("{list_brand_select}", $list_brand_select, $form);
        return $form;
    }

    function showJmovingArticleLocalSearchForm($brand_id, $article_nr_display, $jmoving_id, $storage_id_from) {
        $form = ""; $form_htm = RD . "/tpl/jmoving_artilce_local_search_form.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $range_list = "";
        $list_brand_select = "";
        if ($article_nr_display != "") {
            list($range_list, $list_brand_select) = $this->showArticlesLocalSearchDocumentList($article_nr_display, $brand_id, 0, $jmoving_id, $storage_id_from);
        }
        $form = str_replace("{article_nr_display}", $article_nr_display, $form);
        $form = str_replace("{range_list}", $range_list, $form);
        $form = str_replace("{list_brand_select}", $list_brand_select, $form);
        return $form;
    }

    function showArticlesSearchDocumentList($art, $brand_id_sel, $search_type, $jmoving_id) { $db = DbSingleton::getTokoDb();
        $n = 0;
        $list2 = ""; $r = ""; $query = "";
        if ($search_type == "") {
            $search_type = 1;
        }
        if ($search_type == 0) {
            $art = $this->clearArticle($art);
            $where_brand = "";
            $group_brand = "GROUP BY t2c.BRAND_ID";
            if ($brand_id_sel != "" && $brand_id_sel > 0) {
                $where_brand = " AND t2c.BRAND_ID = $brand_id_sel";
                $group_brand = "";
            }
            if ($art != "") {
                $query = "SELECT t2b.BRAND_NAME, t2n.NAME, t2c.BRAND_ID, t2c.DISPLAY_NR, t2c.ART_ID, t2c.KIND, t2c.RELATION 
                FROM `T2_CROSS` t2c 
                    INNER JOIN `T2_BRANDS` t2b on t2b.BRAND_ID=t2c.BRAND_ID
                    LEFT OUTER JOIN `T2_NAMES` t2n on t2n.ART_ID=t2c.ART_ID
                WHERE t2c.SEARCH_NUMBER = '$art' $where_brand $group_brand 
                ORDER BY t2n.NAME ASC;";
                $r = $db->query($query);
                $n = $db->num_rows($r);
            }
            $one_result = 0;
            if ($n > 1 && ($brand_id_sel == "" || $brand_id_sel == 0)) {
                $where_brand = "";
                $list2 = $this->showCatalogueBrandSelectDocumentList($r);
            }
            if ($n == 1) {
                $query = "SELECT t2b.BRAND_NAME, t2n.NAME, t2c.BRAND_ID, t2c.DISPLAY_NR, t2c.ART_ID, t2c.KIND, t2c.RELATION 
                FROM `T2_CROSS` t2c 
                    INNER JOIN `T2_BRANDS` t2b on t2b.BRAND_ID=t2c.BRAND_ID
                    LEFT OUTER JOIN `T2_NAMES` t2n on t2n.ART_ID=t2c.ART_ID
                WHERE t2c.SEARCH_NUMBER='$art' $where_brand 
                ORDER BY t2n.NAME ASC;";
                $r = $db->query($query);
                $n = $db->num_rows($r);
                $one_result = 1;
            }
            if (($n > 1 && $brand_id_sel != "") || $one_result == 1) {
                $ak = array();
                $rk = array();
                $art_id_str = "";
                for ($i = 1; $i <= $n; $i++) {
                    $ART_ID = $db->result($r,$i-1,"ART_ID");
                    $KIND = $db->result($r,$i-1,"KIND");
                    $RELATION = $db->result($r,$i-1,"RELATION");
                    $art_id_str .= "'$ART_ID'";
                    if ($i < $n) {$art_id_str.=",";}
                    if (($ak[$ART_ID] == "") || $KIND == 0) {$ak[$ART_ID] = $KIND;}
                    if (($rk[$ART_ID] == "") || $RELATION == 0) {$rk[$ART_ID] = $RELATION;}
                }
                $query = "SELECT t2a.ART_ID, t2a.BRAND_ID, t2a.ARTICLE_NR_DISPL, t2b.BRAND_NAME, t2n.NAME, t2n.INFO, t2bc.BARCODE, gg.NAME as goods_group_name, SUM(`t2asc`.`AMOUNT`) as stock, SUM(`t2asc`.`RESERV_AMOUNT`) as reserv, s.name as storage_name, s.id as storage_id
                FROM `T2_ARTICLES` t2a 
                    LEFT OUTER JOIN `T2_BRANDS` t2b on t2b.BRAND_ID=t2a.BRAND_ID 
                    LEFT OUTER JOIN `T2_NAMES` t2n on t2n.ART_ID=t2a.ART_ID 
                    LEFT OUTER JOIN `T2_BARCODES` t2bc on t2bc.ART_ID=t2a.ART_ID 
                    LEFT OUTER JOIN `T2_GOODS_GROUP` t2gg on t2gg.ART_ID=t2a.ART_ID 
                    LEFT OUTER JOIN `GOODS_GROUP` gg on gg.ID=t2gg.GOODS_GROUP_ID 
                    LEFT OUTER JOIN `T2_ARTICLES_STRORAGE` t2asc on t2asc.ART_ID=t2a.ART_ID 
                    LEFT OUTER JOIN `STORAGE` s on s.id=t2asc.STORAGE_ID
                WHERE t2a.ART_ID in ($art_id_str)";
            }
        }
        if ($search_type == 1) {
            $query = "SELECT t1.ART_ID, t1.BRAND_ID, t1.ARTICLE_NR_DISPL, t1.BRAND_NAME, t1.NAME, t1.INFO, t1.BARCODE, t1.goods_group_name as goods_group_name, t1.storage_name as storage_name, t1.storage_id as storage_id, SUM(t1.stock) as stock, SUM(t1.reserv) as reserv FROM (
                SELECT t2a.ART_ID, t2a.BRAND_ID, t2a.ARTICLE_NR_DISPL, t2b.BRAND_NAME, t2n.NAME, t2n.INFO, t2bc.BARCODE, gg.NAME as goods_group_name, s.name as storage_name, s.id as storage_id, `t2asc`.`AMOUNT` as stock, `t2asc`.`RESERV_AMOUNT` as reserv
                FROM `T2_ARTICLES` t2a 
                    LEFT OUTER JOIN `T2_BRANDS` t2b on t2b.BRAND_ID=t2a.BRAND_ID 
                    LEFT OUTER JOIN `T2_NAMES` t2n on t2n.ART_ID=t2a.ART_ID 
                    LEFT OUTER JOIN `T2_BARCODES` t2bc on t2bc.ART_ID=t2a.ART_ID 
                    LEFT OUTER JOIN `T2_GOODS_GROUP` t2gg on t2gg.ART_ID=t2a.ART_ID 
                    LEFT OUTER JOIN `GOODS_GROUP` gg on gg.ID=t2gg.GOODS_GROUP_ID 
                    LEFT OUTER JOIN `T2_ARTICLES_STRORAGE` t2asc on t2asc.ART_ID=t2a.ART_ID 
                    LEFT OUTER JOIN `STORAGE` s on s.id=t2asc.STORAGE_ID
                WHERE (t2a.ARTICLE_NR_SEARCH = '$art' OR t2a.ARTICLE_NR_DISPL = '$art') AND (`t2asc`.`AMOUNT` > 0 OR `t2asc`.`RESERV_AMOUNT` > 0) 
                GROUP BY s.id
            ) as t1 GROUP BY t1.BRAND_ID;";
        }
        if ($search_type == 2) {
            $query = "SELECT t2a.ART_ID, t2a.BRAND_ID, t2a.ARTICLE_NR_DISPL, t2b.BRAND_NAME, t2n.NAME, t2n.INFO, t2bc.BARCODE, gg.NAME as goods_group_name, SUM(`t2asc`.`AMOUNT`) as stock, SUM(`t2asc`.`RESERV_AMOUNT`) as reserv, s.name as storage_name, s.id as storage_id
            FROM `T2_ARTICLES` t2a 
                LEFT OUTER JOIN `T2_BRANDS` t2b on t2b.BRAND_ID=t2a.BRAND_ID 
                LEFT OUTER JOIN `T2_NAMES` t2n on t2n.ART_ID=t2a.ART_ID 
                LEFT OUTER JOIN `T2_BARCODES` t2bc on t2bc.ART_ID=t2a.ART_ID 
                LEFT OUTER JOIN `T2_GOODS_GROUP` t2gg on t2gg.ART_ID=t2a.ART_ID 
                LEFT OUTER JOIN `GOODS_GROUP` gg on gg.ID=t2gg.GOODS_GROUP_ID 
                LEFT OUTER JOIN `T2_ARTICLES_STRORAGE` t2asc on t2asc.ART_ID=t2a.ART_ID 
                LEFT OUTER JOIN `STORAGE` s on s.id=t2asc.STORAGE_ID
            WHERE t2bc.BARCODE = '$art';";
        }

        $r = $db->query($query);
        $n = $db->num_rows($r);
        $list = "";
        if ($list2 == "") {
            // сработал внешний фильр или основной поиск с выбором бренда
            for ($i = 1; $i <= $n; $i++) {
                $art_id = $db->result($r,$i-1,"ART_ID");
                $brand_id = $db->result($r,$i-1,"BRAND_ID");
                $article_nr_displ = $db->result($r,$i-1,"ARTICLE_NR_DISPL");
                $brand_name = $db->result($r,$i-1,"BRAND_NAME");
                $name = $db->result($r,$i-1,"NAME");
                $barcode = $db->result($r,$i-1,"BARCODE");
                $goods_group_name = $db->result($r,$i-1,"goods_group_name");
                $storage_id = $db->result($r,$i-1,"storage_id");
                $storage_name = $db->result($r,$i-1,"storage_name");
                $cell_id = $db->result($r,$i-1,"cell_id");
                $cell_name = $db->result($r,$i-1,"cell_name");
                $stock = $db->result($r,$i-1,"stock");
                $reserv = $db->result($r,$i-1,"reserv");
                $jmoving_amount = $this->getArticleInJmoving($art_id, $jmoving_id);
                $amountRest = "сток: $stock | резерв: $reserv | у поточному відборі: $jmoving_amount";

                $list .= "<tr style='cursor:pointer'>
                    <td class='text-center'><button class='btn btn-sm btn-default' onclick='setArticleToSelectAmountJmoving(\"$art_id\",\"$article_nr_displ\",\"$brand_id\",\"$brand_name\",\"$storage_id\",\"$storage_name\",\"$cell_id\",\"$cell_name\",\"$stock\",\"$amountRest\")'><i class='fa fa-plus'></i></button></td>
                    <td class='text-center'>$article_nr_displ</td>
                    <td class='text-center'>$brand_name</td>
                    <td class='text-center'>$name</td>
                    <td class='text-center'>$barcode</td>
                    <td class='text-center'>$goods_group_name</td>
                    <td class='text-right'>$amountRest</td>
                    <td class='text-center'>$art_id</td>
                </tr>";
            }
        }
        return array($list, $list2);
    }

    function showArticlesLocalSearchDocumentList($art, $brand_id_sel, $search_type, $jmoving_id, $storage_id_from) { $db = DbSingleton::getTokoDb();
        $n = 0;
        $list2 = ""; $r = ""; $query = "";

        if ($search_type === "") {
            $search_type = 1;
        }

        if ($search_type == 0) {
            $art = $this->clearArticle($art);
            $where_brand = "";
            $group_brand = "GROUP BY t2c.BRAND_ID";

            if ($brand_id_sel > 0 && $brand_id_sel !== "") {
                $where_brand = " AND t2c.BRAND_ID='$brand_id_sel'";
                $group_brand = "";
            }

            if ($art !== "") {
                $r = $db->query("SELECT t2b.BRAND_NAME, t2n.NAME, t2c.BRAND_ID, t2c.DISPLAY_NR, t2c.ART_ID, t2c.KIND, t2c.RELATION 
                FROM `T2_CROSS` t2c 
                    INNER JOIN `T2_BRANDS` t2b on t2b.BRAND_ID=t2c.BRAND_ID
                    LEFT OUTER JOIN `T2_NAMES` t2n on t2n.ART_ID=t2c.ART_ID
                WHERE t2c.SEARCH_NUMBER='$art' $where_brand $group_brand 
                ORDER BY t2n.NAME ASC;");
                $n = $db->num_rows($r);
            }

            $one_result = 0;
            if ($n > 1 && ($brand_id_sel === "" || $brand_id_sel == 0)) {
                $where_brand = "";
                $list2 = $this->showCatalogueBrandSelectDocumentList($r);
            }

            if ($n == 1) {
                $query = "SELECT t2b.BRAND_NAME, t2n.NAME, t2c.BRAND_ID, t2c.DISPLAY_NR, t2c.ART_ID, t2c.KIND, t2c.RELATION 
                FROM `T2_CROSS` t2c 
                    INNER JOIN T2_BRANDS t2b on t2b.BRAND_ID=t2c.BRAND_ID
                    LEFT OUTER JOIN T2_NAMES t2n on t2n.ART_ID=t2c.ART_ID
                WHERE t2c.SEARCH_NUMBER='$art' $where_brand 
                ORDER BY t2n.NAME ASC;";
                $r = $db->query($query);
                $n = $db->num_rows($r);
                $one_result = 1;
            }

            if (($n > 1 && $brand_id_sel !== "") || $one_result == 1) {
                $ak = array();
                $rk = array();
                $art_id_str = "";
                for ($i = 1; $i <= $n; $i++) {
                    $ART_ID     = $db->result($r,$i-1,"ART_ID");
                    $KIND       = $db->result($r,$i-1,"KIND");
                    $RELATION   = $db->result($r,$i-1,"RELATION");
                    $art_id_str .= "'$ART_ID'";
                    if ($i < $n) { $art_id_str .= ","; }
                    if (($ak[$ART_ID] === "") || $KIND == 0) { $ak[$ART_ID] = $KIND; }
                    if (($rk[$ART_ID] === "") || $RELATION == 0) { $rk[$ART_ID] = $RELATION; }
                }

                $query = "SELECT t2a.ART_ID,t2a.BRAND_ID,t2a.ARTICLE_NR_DISPL, t2b.BRAND_NAME, t2n.NAME, t2n.INFO, t2bc.BARCODE, gg.NAME as goods_group_name, SUM(`t2asc`.`AMOUNT`) as stock, SUM(`t2asc`.`RESERV_AMOUNT`) as reserv, s.name as storage_name, s.id as storage_id
                FROM `T2_ARTICLES` t2a 
                    LEFT OUTER JOIN T2_BRANDS t2b on t2b.BRAND_ID=t2a.BRAND_ID 
                    LEFT OUTER JOIN T2_NAMES t2n on t2n.ART_ID=t2a.ART_ID 
                    LEFT OUTER JOIN T2_BARCODES t2bc on t2bc.ART_ID=t2a.ART_ID 
                    LEFT OUTER JOIN T2_GOODS_GROUP t2gg on t2gg.ART_ID=t2a.ART_ID 
                    LEFT OUTER JOIN GOODS_GROUP gg on gg.ID=t2gg.GOODS_GROUP_ID 
                    LEFT OUTER JOIN T2_ARTICLES_STRORAGE t2asc on (t2asc.ART_ID=t2a.ART_ID and t2asc.STORAGE_ID='$storage_id_from') 
                    LEFT OUTER JOIN STORAGE s on s.id=t2asc.STORAGE_ID
                WHERE t2a.ART_ID in ($art_id_str)";
            }
        }

        if ($search_type == 1) {
            //added group by art_id
            $query = "SELECT t2a.ART_ID, t2a.BRAND_ID, t2a.ARTICLE_NR_DISPL, t2b.BRAND_NAME, t2bc.BARCODE, gg.NAME as goods_group_name, SUM(`t2asc`.`AMOUNT`) as stock, SUM(`t2asc`.`RESERV_AMOUNT`) as reserv, s.name as storage_name, s.id as storage_id 
            FROM `T2_ARTICLES` t2a 
                LEFT JOIN `T2_BRANDS` t2b on t2b.BRAND_ID=t2a.BRAND_ID 
                LEFT JOIN `T2_BARCODES` t2bc on t2bc.ART_ID=t2a.ART_ID 
                LEFT JOIN `T2_GOODS_GROUP` t2gg on t2gg.ART_ID=t2a.ART_ID 
                LEFT JOIN `GOODS_GROUP` gg on gg.ID=t2gg.GOODS_GROUP_ID 
                LEFT JOIN `T2_ARTICLES_STRORAGE` t2asc on (t2asc.ART_ID=t2a.ART_ID and t2asc.STORAGE_ID='1') 
                LEFT JOIN `STORAGE` s on s.id=t2asc.STORAGE_ID 
            WHERE t2a.ARTICLE_NR_SEARCH='$art' or t2a.ARTICLE_NR_DISPL='$art' 
            GROUP BY t2a.ART_ID";
            //
            //HAVING (stock > 0 OR reserv > 0);
        }

        if ($search_type == 2) {
            $query = "SELECT t2a.ART_ID, t2a.BRAND_ID, t2a.ARTICLE_NR_DISPL, t2b.BRAND_NAME, t2n.NAME, t2n.INFO, t2bc.BARCODE, gg.NAME as goods_group_name, SUM(`t2asc`.`AMOUNT`) as stock, SUM(`t2asc`.`RESERV_AMOUNT`) as reserv, s.name as storage_name, s.id as storage_id
            FROM `T2_ARTICLES` t2a 
                LEFT OUTER JOIN T2_BRANDS t2b on t2b.BRAND_ID=t2a.BRAND_ID 
                LEFT OUTER JOIN T2_NAMES t2n on t2n.ART_ID=t2a.ART_ID 
                LEFT OUTER JOIN T2_BARCODES t2bc on t2bc.ART_ID=t2a.ART_ID 
                LEFT OUTER JOIN T2_GOODS_GROUP t2gg on t2gg.ART_ID=t2a.ART_ID 
                LEFT OUTER JOIN GOODS_GROUP gg on gg.ID=t2gg.GOODS_GROUP_ID 
                LEFT OUTER JOIN T2_ARTICLES_STRORAGE t2asc on (t2asc.ART_ID=t2a.ART_ID and t2asc.STORAGE_ID='$storage_id_from')
                LEFT OUTER JOIN STORAGE s on s.id=t2asc.STORAGE_ID
            WHERE t2bc.BARCODE='$art';";
        }

        $r = $db->query($query);
        $n = $db->num_rows($r);
        $list = "";

        if ($list2 === "") {
            // сработал внешний фильр или основной поиск с выбором бренда
            for ($i = 1; $i <= $n; $i++) {
                $art_id             = $db->result($r,$i-1,"ART_ID");
                $brand_id           = $db->result($r,$i-1,"BRAND_ID");
                $article_nr_displ   = $db->result($r,$i-1,"ARTICLE_NR_DISPL");
                $brand_name         = $db->result($r,$i-1,"BRAND_NAME");
                //$name               = $db->result($r,$i-1,"NAME");
                $name               = $this->getArticleName($art_id);
                $barcode            = $db->result($r,$i-1,"BARCODE");
                $goods_group_name   = $db->result($r,$i-1,"goods_group_name");
                $storage_id         = $db->result($r,$i-1,"storage_id");
                $storage_name       = $db->result($r,$i-1,"storage_name");
                $cell_id            = $db->result($r,$i-1,"cell_id");
                $cell_name          = $db->result($r,$i-1,"cell_name");
                $stock              = $db->result($r,$i-1,"stock");
                $reserv             = $db->result($r,$i-1,"reserv");
                $jmoving_amount     = $this->getArticleInJmoving($art_id, $jmoving_id);
                $amountRest         = "сток на складі: $stock | резерв: $reserv | у поточному відборі: $jmoving_amount";

                $list .= "<tr style='cursor:pointer'>
                    <td class='text-center'><button class='btn btn-sm btn-default' onclick='setArticleToSelectAmountJmovingLocal(\"$art_id\",\"$article_nr_displ\",\"$brand_id\",\"$brand_name\",\"$storage_id\",\"$storage_name\",\"$cell_id\",\"$cell_name\",\"$stock\",\"$amountRest\")'><i class='fa fa-plus'></i></button></td>
                    <td class='text-center'>$article_nr_displ</td>
                    <td class='text-center'>$brand_name</td>
                    <td class='text-center'>$name</td>
                    <td class='text-center'>$barcode</td>
                    <td class='text-center'>$goods_group_name</td>
                    <td class='text-right'>$amountRest</td>
                    <td class='text-center'>$art_id</td>
                </tr>";
            }
        }

        return array($list, $list2);
    }

    function getArticleInJmoving($art_id, $jmoving_id) { $db = DbSingleton::getDb();
        $r = $db->query("SELECT SUM(`amount`) as amount FROM `J_MOVING_STR` WHERE `art_id` = $art_id AND `jmoving_id` = $jmoving_id;");
        return 0 + $db->result($r, 0, "amount");
    }

    function setArticleToSelectAmountJmoving($art_id, $storage_id) {
        $form = ""; $form_htm = RD . "/tpl/jmoving_select_amount_article_form.htm";
        if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $form = str_replace("{storage_list}", $this->showArticleRestStorageSelectList($art_id, $storage_id), $form);
        return $form;
    }

    function setArticleToSelectAmountJmovingLocal($art_id, $storage_id) {
        $form = ""; $form_htm = RD . "/tpl/jmoving_local_select_amount_article_form.htm";
        if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $form = str_replace("{cells_list_from}", $this->showArticleRestStorageCellsList($art_id, $storage_id), $form);
        $form = str_replace("{cells_list_to}", $this->showStorageCellsList($storage_id), $form);
        return $form;
    }

    function showJmovingArticleAmountChange($art_id, $jmoving_str_id) { $db = DbSingleton::getDb();
        $form = ""; $form_htm = RD . "/tpl/jmoving_select_amount_article_change_form.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $r = $db->query("SELECT `article_nr_displ`, `brand_id`, `amount`, `storage_id_from` FROM `J_MOVING_STR` WHERE `id` = $jmoving_str_id AND `status_jmoving` = 44 LIMIT 1;");
        $article_nr_displ = $db->result($r, 0, "article_nr_displ");
        $brand_id = $db->result($r, 0, "brand_id");
        $amount = $db->result($r, 0, "amount");
        $storage_id = $db->result($r, 0, "storage_id_from");
        list($info, $max_moving) = $this->showArticleRestStorageSelectText($art_id, $amount, $storage_id);
        $form = str_replace("{storage_name}", $this->getStorageName($storage_id), $form);
        $form = str_replace("{amountRestText}", $info, $form);
        $form = str_replace("{max_moving}", $max_moving, $form);
        $form = str_replace("{cur_amount}", $amount, $form);
        $form = str_replace("{jmoving_str_id}", $jmoving_str_id, $form);
        return array($form, $article_nr_displ, $this->getBrandName($brand_id));
    }

    function showJmovingArticleAmountLocalChange($art_id, $jmoving_str_id) { $db = DbSingleton::getDb();
        $form = ""; $form_htm = RD . "/tpl/jmoving_local_select_amount_article_change_form.htm";
        if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r = $db->query("SELECT `article_nr_displ`, `brand_id`, `amount`, `storage_id_from`, `cell_id_from` FROM `J_MOVING_STR` WHERE `id` = $jmoving_str_id AND `status_jmoving` = 44 LIMIT 1;");
        $article_nr_displ = $db->result($r, 0, "article_nr_displ");
        $brand_id = $db->result($r, 0, "brand_id");
        $amount = $db->result($r, 0, "amount");
        $storage_id = $db->result($r, 0, "storage_id_from");
        $cell_id = $db->result($r, 0, "cell_id_from");
        list($info, $max_moving) = $this->showArticleRestStorageCellSelectText($art_id, $amount, $cell_id);
        $form = str_replace("{storage_name}", $this->getStorageName($storage_id), $form);
        $form = str_replace("{cell_name}", $this->getStorageCellName($cell_id), $form);
        $form = str_replace("{storage_id_from}", $storage_id, $form);
        $form = str_replace("{cell_id_from}", $cell_id, $form);
        $form = str_replace("{amountRestText}", $info, $form);
        $form = str_replace("{max_moving}", $max_moving, $form);
        $form = str_replace("{cur_amount}", $amount, $form);
        $form = str_replace("{jmoving_str_id}", $jmoving_str_id, $form);
        return array($form, $article_nr_displ, $this->getBrandName($brand_id));
    }

    function showArticleRestStorageSelectText($art_id, $cur_amount, $storage_id = null) { $db = DbSingleton::getTokoDb();
        $reserv_amount=$reserv_amount_storage=$max_moving=$amount=0;
        $info = "";
        $where_storage = ($storage_id == "" || $storage_id == 0) ? "" : " AND t2as.STORAGE_ID = $storage_id ";
        $r = $db->query("SELECT s.id, s.name, t2as.AMOUNT, t2as.RESERV_AMOUNT 
        FROM `STORAGE` s 
            INNER JOIN `T2_ARTICLES_STRORAGE` t2as on t2as.STORAGE_ID=s.id 
        WHERE s.status = 1 AND t2as.ART_ID = $art_id $where_storage 
        ORDER BY s.name ASC, s.id ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $amount = $db->result($r, $i - 1, "AMOUNT");
            $reserv_amount += $db->result($r, $i - 1, "RESERV_AMOUNT");
            $max_moving = $amount;
            $info = "Залишок: $amount | Резерв: $reserv_amount<br>У поточному записі: $cur_amount";
        }
        return array($info, $max_moving, $amount);
    }

    function showArticleRestStorageCellSelectText($art_id, $cur_amount, $cell_id) { $db = DbSingleton::getTokoDb();
        $reserv_amount = $reserv_amount_storage = $max_moving = $amount = 0;
        $info = "";
        $r = $db->query("SELECT sc.id, sc.cell_value, t2asc.AMOUNT, t2asc.RESERV_AMOUNT, t2as.AMOUNT as AMOUNT_STORAGE, t2as.RESERV_AMOUNT as RESERV_AMOUNT_STORAGE 
        FROM `STORAGE_CELLS` sc 
            INNER JOIN `T2_ARTICLES_STRORAGE_CELLS` t2asc on t2asc.STORAGE_CELLS_ID=sc.id 
            LEFT OUTER JOIN `T2_ARTICLES_STRORAGE` t2as on t2as.STORAGE_ID=sc.storage_id 
        WHERE sc.status = 1 AND t2asc.ART_ID = $art_id AND t2as.ART_ID = $art_id AND t2asc.STORAGE_CELLS_ID = $cell_id 
        ORDER BY sc.cell_value ASC, sc.id ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $amount = $db->result($r, $i - 1, "AMOUNT");
            $reserv_amount += $db->result($r, $i - 1, "RESERV_AMOUNT");
            $amount_storage = $db->result($r, $i - 1, "AMOUNT_STORAGE");
            $reserv_amount_storage += $db->result($r, $i - 1, "RESERV_AMOUNT_STORAGE");
            if ($amount > $amount_storage) {
                $amount = $amount_storage;
                $reserv_amount = $reserv_amount_storage;
            }
            $max_moving = $amount + $cur_amount;
            $info = "Залишок: $amount | Резерв: $reserv_amount<br>У поточному записі: $cur_amount";
        }
        return array($info, $max_moving, $amount);
    }

    function showArticleRestStorageSelectList($art_id, $storage_id) { $db = DbSingleton::getTokoDb();
        $list = "<option value='0'>-- Оберіть зі списку --</option>";
        $r = $db->query("SELECT s.id, s.name, t2as.AMOUNT, t2as.RESERV_AMOUNT 
        FROM `STORAGE` s 
            LEFT OUTER JOIN `T2_ARTICLES_STRORAGE` t2as on t2as.STORAGE_ID=s.id 
        WHERE s.status = 1 AND t2as.ART_ID = $art_id
        ORDER BY s.name ASC, s.id ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"name");
            $amount=$db->result($r,$i-1,"AMOUNT");
            $reserv_amount=$db->result($r,$i-1,"RESERV_AMOUNT");
            $max_moving=$amount;
            if ($id == $storage_id) {
                $sel = " disabled";
                $t = "Сам себя..? ";
            } else {
                $sel = "";
                $t = "";
            }
            if ($amount != 0 || $reserv_amount != 0) {
                $list.="<option value='$id' data-max-mov='$max_moving' data-cellId-mov='0' $sel>$name $t | Залишок: $amount; Резерв: $reserv_amount; </option>";
            }
        }
        return $list;
    }

    function getArticlesStorageAmount($art_id, $storage_id) { $db = DbSingleton::getTokoDb();
        $amount = $reserv = 0;
        $r = $db->query("SELECT `AMOUNT`, `RESERV_AMOUNT` FROM `T2_ARTICLES_STRORAGE` WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n > 0) {
            $amount = $db->result($r, 0, "AMOUNT");
            $reserv = $db->result($r, 0, "RESERV_AMOUNT");
        }
        return compact("amount", "reserv");
    }

    function getArticlesStorageCellsAmount($art_id, $storage_id, $cell_id) { $db = DbSingleton::getTokoDb();
        $amount = $reserv = 0;
        $r = $db->query("SELECT `AMOUNT`, `RESERV_AMOUNT` FROM `T2_ARTICLES_STRORAGE_CELLS` WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id AND `STORAGE_CELLS_ID` = $cell_id LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n > 0) {
            $amount = $db->result($r, 0, "AMOUNT");
            $reserv = $db->result($r, 0, "RESERV_AMOUNT");
        }
        return compact("amount", "reserv");
    }

    function showArticleRestStorageCellsList($art_id, $storage_id) { $db = DbSingleton::getTokoDb();
        $list = "<option value='0'>-- Оберіть зі списку --</option>";
        $r = $db->query("SELECT `id`, `cell_value` FROM `STORAGE_CELLS` WHERE `id` IN (
            SELECT `STORAGE_CELLS_ID` 
            FROM `T2_ARTICLES_STRORAGE_CELLS`
            WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id
        ) AND `status` = 1");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $cell_id = $db->result($r,$i-1,"id");
            $name = $db->result($r,$i-1,"cell_value");
            $cellsData = $this->getArticlesStorageCellsAmount($art_id, $storage_id, $cell_id);
            $amount = $cellsData["amount"];
            $reserv_amount = $cellsData["reserv"];
            $max_moving = $amount;
            if ($reserv_amount != 0 || $amount != 0) {
                $list .= "<option value='$cell_id' data-max-mov='$max_moving' data-cellId-mov='0'>$name | Залишок: $amount; Резерв: $reserv_amount; </option>";
            }
        }
        return $list;
    }

    function showStorageCellsList($storage_id, $exclude_id = 0) { $db = DbSingleton::getTokoDb();
        $list = "<option value='0'>-- Оберіть зі списку --</option>";
        $r = $db->query("SELECT `id`, `cell_value` FROM `STORAGE_CELLS` WHERE `status` = 1 AND `storage_id` = $storage_id AND `id` <> $exclude_id ORDER BY `cell_value` ASC, `id` ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r, $i - 1, "id");
            $name = $db->result($r, $i - 1, "cell_value");
            $list .= "<option value='$id'>$name</option>";
        }
        return $list;
    }

    function getArticleName($art_id) { $db = DbSingleton::getTokoDb();
        $name = "";
        $r = $db->query("SELECT `NAME` FROM `T2_NAMES` WHERE `ART_ID` = $art_id AND `LANG_ID` = 16 LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $name = $db->result($r, 0, "NAME");
        }
        return $name;
    }

    function getArticleWightVolume($art_id) { $db = DbSingleton::getTokoDb();
        $weight = 0; $volume = 0; $weight2 = 0;
        $r = $db->query("SELECT `VOLUME`, `WEIGHT_BRUTTO`, `WEIGHT_NETTO` FROM `T2_PACKAGING` WHERE `ART_ID` = $art_id LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $weight = $db->result($r, 0, "WEIGHT_BRUTTO");
            $weight2 = $db->result($r, 0, "WEIGHT_NETTO");
            $volume = $db->result($r, 0, "VOLUME");
        }
        return array($weight, $volume, $weight2);
    }

    function loadJmovingStorage($jmoving_id) { $db = DbSingleton::getDb();
        $form = ""; $form_htm = RD . "/tpl/jmoving_storage_form.htm";
        if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r = $db->query("SELECT `storage_id`, `storage_cells_id` FROM `J_INCOME` WHERE `id` = $jmoving_id LIMIT 1;");
        $storage_id = $db->result($r, 0, "storage_id");
        $storage_cells_id = $db->result($r, 0, "storage_cells_id");
        $form = str_replace("{jmoving_id}", $jmoving_id, $form);
        $form = str_replace("{storage_list}", $this->showStorageSelectList($storage_id), $form);
        $form = str_replace("{storage_cells_list}", $this->showStorageCellsSelectList($storage_id, $storage_cells_id), $form);
        return $form;
    }

    function getStorageName($sel_id) { $db = DbSingleton::getTokoDb();
        $name="";
        $r = $db->query("SELECT `name` FROM `STORAGE` WHERE `status` = 1 AND `id` = $sel_id LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $name = $db->result($r, 0, "name");
        }
        return $name;
    }

    function showStorageSelectList($sel_id, $cells_only = 0) { $db = DbSingleton::getTokoDb();
        $list = "<option value=0>Оберіть зі списку</option>";
        $query = "SELECT `id`, `name` FROM `STORAGE` WHERE `status`='1' ORDER BY `name` ASC, `id` ASC;";
        if ($cells_only == 1) {
            $query = "SELECT s.id, s.name FROM `STORAGE` s 
                INNER JOIN `STORAGE_STR` ss ON (ss.storage_id=s.id)
            WHERE s.status = 1 
            GROUP BY ss.storage_id 
            ORDER BY s.name ASC, s.id ASC;";
        }
        $r = $db->query($query);
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r, $i - 1, "id");
            $name = $db->result($r, $i - 1, "name");
            $sel = "";
            if ($sel_id == $id) {
                $sel = "selected='selected'";
            }
            $list .= "<option value='$id' $sel>$name</option>";
        }
        return $list;
    }

    function getStorageCellName($sel_id) { $db = DbSingleton::getTokoDb();
        $name = "";
        $r = $db->query("SELECT `cell_value` FROM `STORAGE_CELLS` WHERE `status`='1' AND `id`='$sel_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $name = $db->result($r, 0, "cell_value");
        }
        return $name;
    }

    function showStorageCellsSelectList($storage_id, $sel_id) { $db = DbSingleton::getTokoDb();
        $list = "<option value=0>Оберіть зі списку</option>";
        $cells_show = 1;
        $r = $db->query("SELECT * FROM `STORAGE_CELLS` WHERE `status`='1' AND `storage_id`='$storage_id' ORDER BY `cell_value` ASC, `id` ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r,$i-1,"id");
            $cell_value = $db->result($r,$i-1,"cell_value");
            $default = $db->result($r,$i-1,"default");
            $sel=""; if ($sel_id==$id) {$sel="selected='selected'";}
            if ($sel_id==0 && $default==1) {$sel="selected='selected'";}
            $list .= "<option value='$id' $sel>$cell_value</option>";
        }
        if ($n == 0) {
            $cells_show = 0;
        }
        return array($list, $cells_show);
    }

    function saveJmovingStorage($jmoving_id, $storage_id, $storage_cells_id) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка збереження даних!";
        $jmoving_id = $slave->qq($jmoving_id); $storage_id = $slave->qq($storage_id); $storage_cells_id = $slave->qq($storage_cells_id);
        if ($jmoving_id > 0 && $storage_id > 0 && $storage_cells_id > 0) {
            $db->query("UPDATE `J_INCOME` SET `storage_id`='$storage_id', `storage_cells_id`='$storage_cells_id' WHERE `id` = $jmoving_id;");
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

    function loadJmovingCommets($jmoving_id) { $db = DbSingleton::getDb();
        $list="";
        $form = ""; $form_htm = RD . "/tpl/jmoving_comment_block.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $r = $db->query("SELECT cc.*, u.name 
        FROM `J_MOVING_COMMENTS` cc 
            LEFT OUTER JOIN `media_users` u ON (u.id=cc.USER_ID) 
        WHERE cc.jmoving_id='$jmoving_id' ORDER BY `id` DESC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id=$db->result($r,$i-1,"id");
            $user_id=$db->result($r,$i-1,"user_id");
            $user_name=$db->result($r,$i-1,"name");
            $data=$db->result($r,$i-1,"data");
            $comment=$db->result($r,$i-1,"comment");
            $block=$form;
            $block=str_replace("{jmoving_id}",$jmoving_id,$block);
            $block=str_replace("{id}",$id,$block);
            $block=str_replace("{user_id}",$user_id,$block);
            $block=str_replace("{user_name}",$user_name,$block);
            $block=str_replace("{data}",$data,$block);
            $block=str_replace("{comment}",$comment,$block);
            $list.=$block;
        }
        if ($n == 0) {
            $list="<h3 class='text-center'>Коментарі відсутні</h3>";
        }
        return $list;
    }

    function saveJmovingComment($jmoving_id, $comment) { $db = DbSingleton::getDb();
        $slave = new slave;
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $answer = 0; $err = "Помилка збереження даних!";
        $jmoving_id=$slave->qq($jmoving_id); $comment=$slave->qq($comment);
        if ($jmoving_id > 0 && $comment != "") {
            $db->query("INSERT INTO `J_MOVING_COMMENTS` (`jmoving_id`,`user_id`,`comment`) VALUES ('$jmoving_id','$user_id','$comment');");
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

    function dropJmovingComment($jmoving_id, $comment_id) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка видалення запису!";
        $jmoving_id=$slave->qq($jmoving_id); $comment_id=$slave->qq($comment_id);
        if ($jmoving_id > 0 && $comment_id > 0) {
            $r = $db->query("SELECT * FROM `J_MOVING_COMMENTS` WHERE `jmoving_id` = $jmoving_id AND `id`='$comment_id' LIMIT 1;");
            $n = $db->num_rows($r);
            if ($n == 1) {
                $db->query("DELETE FROM `J_MOVING_COMMENTS` WHERE `jmoving_id` = $jmoving_id AND `id`='$comment_id';");
                $answer = 1; $err = "";
            }
        }
        return array($answer, $err);
    }

    function loadJmovingCDN($jmoving_id) { $db = DbSingleton::getDb();
        $list="";
        $form = ""; $form_htm = RD . "/tpl/jmoving_cdn_block.htm";
        if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r = $db->query("SELECT cc.*, u.name as user_name 
        FROM `J_MOVING_CDN` cc 
            LEFT OUTER JOIN `media_users` u on u.id=cc.USER_ID 
        WHERE cc.jmoving_id='$jmoving_id' and cc.status='1' ORDER BY cc.file_name ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $file_id=$db->result($r,$i-1,"id");
            $file_name=$db->result($r,$i-1,"file_name");
            $name=$db->result($r,$i-1,"name");
            $data=$db->result($r,$i-1,"data");
            $user_name=$db->result($r,$i-1,"user_name");
            $link="http://portal.myparts.pro/cdn/jmoving_files/$jmoving_id/$file_name";
            $file_view="<div class=\"icon\"><i class=\"fa fa-file\"></i></div>";
            $exten=pathinfo($file_name, PATHINFO_EXTENSION);
            if ($exten=="jpg" || $exten=="jpeg" || $exten=="png" || $exten=="gif" || $exten=="bmp" || $exten=="svg") {
                $file_view="<div class=\"image\"><img alt=\"image\" class=\"img-responsive\" src=\"$link\"></div>";
            }
            $block=$form;
            $block=str_replace("{file_id}",$file_id,$block);
            $block=str_replace("{file_name}",$name,$block);
            $block=str_replace("{user_name}",$user_name,$block);
            $block=str_replace("{data}",$data,$block);
            $block=str_replace("{jmoving_id}",$jmoving_id,$block);
            $block=str_replace("{link}",$link,$block);
            $block=str_replace("{file_view}",$file_view,$block);
            $list.=$block;
        }
        if ($n == 0) {
            $list="<h3 class='text-center'>Файли відсутні</h3>";
        }
        return $list;
    }

    function jmovingCDNDropFile($jmoving_id, $file_id) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка видалення файлу!";
        $jmoving_id = $slave->qq($jmoving_id); $file_id = $slave->qq($file_id);
        if ($jmoving_id > 0 && $file_id > 0) {
            $r = $db->query("SELECT `file_name` FROM `J_MOVING_CDN` WHERE `jmoving_id` = $jmoving_id AND `id`='$file_id' LIMIT 1;");
            $n = $db->num_rows($r);
            if ($n == 1) {
                $file_name=$db->result($r,0,"file_name");
                unlink(RD."/cdn/jmoving_files/$jmoving_id/$file_name");
                $db->query("DELETE FROM `J_MOVING_CDN` WHERE `jmoving_id` = $jmoving_id AND `id`='$file_id';");
                $answer = 1; $err = "";
            }
        }
        return array($answer, $err);
    }

    function showJmovingDocumentList($jmoving_id, $jmoving_op_id) {
        $income = new income;
        $form = ""; $document_list = "";
        if ($jmoving_op_id == 1) {
            $form_htm = RD . "/tpl/jmoving_documents_list.htm";
            if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
            $document_list = $income->search_documents_income_list("");
        }
        $form = str_replace("{documents_list}", $document_list, $form);
        $form = str_replace("{jmoving_id}", $jmoving_id, $form);
        $form = str_replace("{jmoving_op_id}", $jmoving_op_id, $form);
        return array($form, "Реєстр документів основи");
    }

    function findJmovingDocumentsSearch($jmoving_op_id, $s_nom) {
        $income = new income;
        return ($jmoving_op_id == 1) ? $income->search_documents_income_list($s_nom) : "";
    }

    function labelArtEmptyCount($jmoving_id, $kol) {
        $label = "";
        if ($kol == 0 || $kol == "") {
            list(,, $kol) = $this->updateJmovingWeightVolume($jmoving_id);
        }
        if ($kol > 0) {
            $label = "<span class='label label-tab label-info'>$kol</span>";
        }
        return array($kol,$label);
    }

    function labelCommentsCount($jmoving_id) { $db = DbSingleton::getDb();
        $r = $db->query("SELECT COUNT(`id`) as kol FROM `J_MOVING_COMMENTS` WHERE `jmoving_id` = $jmoving_id;");
        $kol = 0 + $db->result($r, 0, "kol");
        $label = "";
        if ($kol > 0) {
            $label = "<span class='label label-tab label-info'>$kol</span>";
        }
        return $label;
    }

    function loadJmovingUnknownArticles($jmoving_id) { $db = DbSingleton::getDb();
        $form = ""; $form_htm = RD . "/tpl/jmoving_unknown_articles_list.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $r = $db->query("SELECT * FROM `J_MOVING` WHERE `id` = $jmoving_id LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 0) {
            $form_htm=RD."/tpl/access_deny.htm";
            if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        }
        if ($n == 1) {
            list($list, $kol_rows) = $this->showJmovingUnknownStrList($jmoving_id);
            $form = str_replace("{UnknownArticlesList}",$list,$form);
            $form = str_replace("{kol_rows}",$kol_rows,$form);
            $form = str_replace("{jmoving_id}",$jmoving_id,$form);
        }
        return $form;
    }

    function showJmovingUnknownStrList($jmoving_id) { $db = DbSingleton::getDb();
        $empty_kol = 0; $list = "";
        $r = $db->query("SELECT * FROM `J_MOVING_STR` WHERE `jmoving_id` = $jmoving_id GROUP BY `art_id` ORDER BY `id` ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $art_id = $db->result($r,$i-1,"art_id");
            $article_nr_displ = $db->result($r,$i-1,"article_nr_displ");
            $brand_id = $db->result($r,$i-1,"brand_id");
            $brand_name = $this->getBrandName($brand_id);
            list($weight_brutto, $volume, $weight_netto) = $this->getArticleWightVolume($art_id);
            if ($weight_brutto == 0 || $volume == 0 || $weight_netto == 0) {
                $empty_kol += 1;
                $list .= "<tr id='strUnRow_$i'>
                    <td><button class='btn btn-xs btn-warning' onClick='checkJmovingUnStr(\"$jmoving_id\",\"$i\",\"$art_id\");'><i class='fa fa-refresh'></i></button></td>
                    <td>$i</td>
                    <td style='min-width:140px;'><input type='hidden' id='artIdUnStr_$i' value='$art_id'><input type='hidden' id='article_nr_displUnStr_$i' value='$article_nr_displ'>$article_nr_displ</td>
                    <td style='min-width:120px;'>$brand_name</td>
                    <td><input type='text' id='volumeUnStr_$i' value='$volume' class='form-control input-xs numberOnlyLong'></td>
                    <td><input type='text' id='weightNettoUnStr_$i' value='$weight_netto' class='form-control input-xs numberOnlyLong'></td>
                    <td><input type='text' id='weightBruttoUnStr_$i' value='$weight_brutto' class='form-control input-xs text-right numberOnlyLong'></td>
                </tr>";
            }
        }
        return array($list, $empty_kol);
    }

    function checkJmovingUnStr($jmoving_id, $art_id, $volume, $weight, $weight2) { $db = DbSingleton::getDb(); $dbt = DbSingleton::getTokoDb();
        $slave = new slave;
        $answer = 0; $err = "";
        $jmoving_id = $slave->qq($jmoving_id); $art_id = $slave->qq($art_id);
        $r = $db->query("SELECT `oper_status` FROM `J_MOVING` WHERE `id` = $jmoving_id LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $oper_status = $db->result($r, 0, "oper_status");
            if ($oper_status == 30) {
                $volume=$slave->qq($volume); $weight=$slave->qq($weight); $weight2=$slave->qq($weight2);
                if ($art_id > 0 && $volume > 0 && $weight > 0 && $weight2 > 0) {
                    $rs = $db->query("SELECT `art_id` FROM `T2_PACKAGING` WHERE `art_id`='$art_id' LIMIT 1;");
                    $ns = $dbt->num_rows($rs);
                    if ($ns == 1) {
                        $dbt->query("UPDATE `T2_PACKAGING` SET `VOLUME`='$volume', `WEIGHT_NETTO`='$weight', `WEIGHT_BRUTTO`='$weight2' WHERE `ART_ID`='$art_id' LIMIT 1;");
                    } else {
                        $dbt->query("INSERT INTO `T2_PACKAGING` (`ART_ID`,`VOLUME`,`WEIGHT_NETTO`,`WEIGHT_BRUTTO`) VALUES ('$art_id','$volume','$weight','$weight2');");
                    }
                    $answer = 1; $err = "";
                } else {
                    $answer = 0; $err = "Не заповнені всі поля для артикулу";
                }
            } else {
                $answer = 0; $err = "Переміщення заблоковано. Зміни вносити заборонено.";
            }
        }
        return array($answer, $err);
    }

    function newJmoving($type_id, $art_id, $storage_id_to, $status_jmoving, $oper_status, $jmov) { $db = DbSingleton::getDb();
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $r = $db->query("SELECT MAX(`id`) as mid FROM `J_MOVING`;");
        $jmoving_id = 0 + $db->result($r, 0, "mid") + 1;
        $doc_nom = $this->get_df_doc_nom_new();
        $db->query("INSERT INTO `J_MOVING` (`id`,`type_id`,`prefix`,`doc_nom`,`user_id`,`data`,`storage_id_to`,`status_jmoving`,`oper_status`) 
        VALUES ('$jmoving_id','$type_id','$this->prefix_new','$doc_nom','$user_id',CURDATE(),'$storage_id_to','$status_jmoving','$oper_status');");
        $db->query("UPDATE `J_MOVING_STR` SET `jmoving_id` = $jmoving_id WHERE `jmoving_id` = $jmov AND `art_id` = $art_id;");
        return $jmoving_id;
    }

    function startJmovingStorageSelect($jmoving_id) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "";
        $jmoving_id = $slave->qq($jmoving_id);
        $r = $db->query("SELECT `oper_status`, `status_jmoving`, `storage_id_to` FROM `J_MOVING` WHERE `id` = $jmoving_id LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $oper_status = $db->result($r, 0, "oper_status");
            $status_jmoving = $db->result($r, 0, "status_jmoving");
            $storage_id_to = $db->result($r, 0, "storage_id_to");
            if ($storage_id_to == 0) {
                $answer = 0; $err = "Не зазначено склад переміщення.";
            }
            if ($status_jmoving > 47 || $oper_status > 30) {
                $answer = 0; $err = "Переміщення заблоковано. Зміни вносити заборонено.";
            }
            if ($oper_status == 30 && $status_jmoving >= 44 && $status_jmoving <= 47 && $storage_id_to > 0) {
                $r1 = $db->query("SELECT `storage_id_from` FROM `J_MOVING_STR` WHERE `jmoving_id` = $jmoving_id AND `status_jmoving`='44' GROUP BY `storage_id_from`, `cell_id_from` ORDER BY `storage_id_from` ASC;");
                $n1 = $db->num_rows($r1);
                if ($n1 == 0) {
                    $answer = 0; $err = "Відсутній товар для створення відбору";
                }
                if ($n1 > 0) {
                    for ($i = 1; $i <= $n1; $i++) {
                        $storage_id_from = $db->result($r1, $i - 1, "storage_id_from");
                        list($tpoint_id, $loc_type_id) = $this->getTpointDataByStorage($storage_id_from);
                        $sum_art_amount = 0;
                        $sum_amount = 0;
                        $sum_volume = 0;
                        $sum_weight_netto = 0;
                        $sum_weight_brutto = 0;

                        $rm = $db->query("SELECT MAX(`id`) as mid FROM `J_MOVING_SELECT_TEMP`;");
                        $select_id = 0 + $db->result($rm, 0, "mid") + 1;
                        $db->query("INSERT INTO `J_MOVING_SELECT_TEMP` (`id`,`jmoving_id`,`tpoint_id`,`storage_id`,`loc_type_id`,`status_jmoving`) 
                        VALUES ('$select_id','$jmoving_id','$tpoint_id','$storage_id_from','$loc_type_id','44');");

                        $ra = $db->query("SELECT * FROM `J_MOVING_STR` WHERE `jmoving_id` = $jmoving_id AND `storage_id_from` = $storage_id_from AND `status_jmoving`='44';");
                        $na = $db->num_rows($ra);
                        for ($a = 1; $a <= $na; $a++) {
                            $art_id = $db->result($ra, $a-1, "art_id");
                            $article_nr_displ = $db->result($ra, $a-1, "article_nr_displ");
                            $brand_id = $db->result($ra, $a-1, "brand_id");
                            $amount = $db->result($ra, $a-1, "amount");
                            $cell_id_from = $db->result($ra, $a-1, "cell_id_from");

                            list($weight_brutto, $volume, $weight_netto) = $this->getArticleWightVolume($art_id);
                            $sum_amount += $amount;
                            $sum_art_amount += 1;
                            $sum_volume += ($volume * $amount);
                            $sum_weight_netto += ($weight_netto * $amount);
                            $sum_weight_brutto += ($weight_brutto * $amount);

                            $db->query("INSERT INTO `J_MOVING_SELECT_STR_TEMP` (`jmoving_id`,`select_id`,`art_id`,`article_nr_displ`,`brand_id`,`amount`,`storage_id_from`,`cell_id_from`) 
                            VALUES ('$jmoving_id','$select_id','$art_id','$article_nr_displ','$brand_id','$amount','$storage_id_from','$cell_id_from');");
                        }
                        $db->query("UPDATE `J_MOVING_SELECT_TEMP` SET `articles_amount`='$sum_art_amount', `amount`='$sum_amount', `volume`='$sum_volume', `weight_netto`='$sum_weight_netto', `weight_brutto`='$sum_weight_brutto' WHERE `id`='$select_id' AND '$jmoving_id'='$jmoving_id';");
                    }
                    $answer = 1; $err = "";
                }
            }
        }
        return array($answer, $err);
    }

    function startJmovingStorageSelectLocal($jmoving_id)
    {
        $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "";
        $jmoving_id = $slave->qq($jmoving_id);
        $r = $db->query("SELECT `oper_status`, `status_jmoving`, `storage_id_to` FROM `J_MOVING` WHERE `id` = $jmoving_id LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $oper_status = $db->result($r,0,"oper_status");
            $status_jmoving = $db->result($r,0,"status_jmoving");
            $storage_id_to = $db->result($r,0,"storage_id_to");
            if ($storage_id_to == 0) {
                $answer = 0; $err = "Не зазначено склад переміщення.";
            }
            if ($status_jmoving > 47 || $oper_status > 30) {
                $answer = 0; $err = "Переміщення заблоковано. Зміни вносити заборонено.";
            }
            if ($oper_status == 30 && $status_jmoving >= 44 && $status_jmoving <= 47 && $storage_id_to > 0) {
                $r1 = $db->query("SELECT `storage_id_from`, `cell_id_from` FROM `J_MOVING_STR` 
                WHERE `jmoving_id` = $jmoving_id AND `status_jmoving`='44' 
                GROUP BY `cell_id_from` ORDER BY `cell_id_from` ASC;");
                $n1 = $db->num_rows($r1);
                if ($n1 == 0) {
                    $answer = 0; $err = "Відсутній товар для створення відбору";
                }
                if ($n1 > 0) {
                    for ($i = 1; $i <= $n1; $i++) {
                        $storage_id_from = $db->result($r1,$i-1,"storage_id_from");
                        $cell_id_from = $db->result($r1,$i-1,"cell_id_from");
                        list($tpoint_id,) = $this->getTpointDataByStorage($storage_id_from);
                        $loc_type_id = 1;
                        $sum_art_amount = 0;
                        $sum_amount = 0;
                        $sum_volume = 0;
                        $sum_weight_netto = 0;
                        $sum_weight_brutto = 0;

                        $rm = $db->query("SELECT MAX(`id`) as mid FROM `J_MOVING_LOCAL_SELECT_TEMP`;");
                        $select_id = 0 + $db->result($rm, 0, "mid") + 1;
                        $db->query("INSERT INTO `J_MOVING_LOCAL_SELECT_TEMP` (`id`,`jmoving_id`,`tpoint_id`,`storage_id`,`loc_type_id`,`status_jmoving`) 
                        VALUES ('$select_id','$jmoving_id','$tpoint_id','$storage_id_from','$loc_type_id','44');");

                        $ra = $db->query("SELECT * FROM `J_MOVING_STR` WHERE `jmoving_id` = $jmoving_id AND `storage_id_from` = $storage_id_from AND `cell_id_from` = $cell_id_from AND `status_jmoving`='44';");
                        $na = $db->num_rows($ra);
                        for ($a = 1; $a <= $na; $a++) {
                            $art_id = $db->result($ra,$a-1,"art_id");
                            $article_nr_displ = $db->result($ra,$a-1,"article_nr_displ");
                            $brand_id = $db->result($ra,$a-1,"brand_id");
                            $amount = $db->result($ra,$a-1,"amount");
                            $cell_id_to = $db->result($ra,$a-1,"cell_id_to");

                            list($weight_brutto, $volume, $weight_netto) = $this->getArticleWightVolume($art_id);
                            $sum_amount += $amount;
                            $sum_art_amount += 1;
                            $sum_volume += ($volume * $amount);
                            $sum_weight_netto += ($weight_netto * $amount);
                            $sum_weight_brutto += ($weight_brutto * $amount);

                            $db->query("INSERT INTO `J_MOVING_LOCAL_SELECT_STR_TEMP` (`jmoving_id`,`select_id`,`art_id`,`article_nr_displ`,`brand_id`,`amount`,`storage_id_from`,`cell_id_from`,`storage_id_to`,`cell_id_to`) 
                            VALUES ('$jmoving_id','$select_id','$art_id','$article_nr_displ','$brand_id','$amount','$storage_id_from','$cell_id_from','$storage_id_from','$cell_id_to');");
                        }
                        $db->query("UPDATE `J_MOVING_LOCAL_SELECT_TEMP` SET `articles_amount`='$sum_art_amount',`amount`='$sum_amount',`volume`='$sum_volume',`weight_netto`='$sum_weight_netto',`weight_brutto`='$sum_weight_brutto' WHERE `id`='$select_id' AND '$jmoving_id'='$jmoving_id';");
                    }
                    $answer = 1; $err = "";
                }
            }
        }
        return array($answer, $err);
    }

    /*
     * передати в роботу (скл відбір)
     * */
    function makesJmovingStorageSelect($jmoving_id) { $db = DbSingleton::getDb(); $dbt = DbSingleton::getTokoDb();
        $slave = new slave;
        $answer = 0; $err = "";
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $jmoving_id = $slave->qq($jmoving_id);
        $storage_id_from = 0;
        $r = $db->query("SELECT `oper_status`, `status_jmoving`, `storage_id_to` FROM `J_MOVING` WHERE `id` = $jmoving_id LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $oper_status = $db->result($r, 0, "oper_status");
            $status_jmoving = $db->result($r, 0, "status_jmoving");
            $storage_id_to = $db->result($r, 0, "storage_id_to");
            if ($storage_id_to == 0) {
                $answer = 0; $err = "Не зазначено склад переміщення.";
            }
            if ($status_jmoving > 47 || $oper_status > 30) {
                $answer = 0; $err = "Переміщення заблоковано. Зміни вносити заборонено.";
            }
            if ($oper_status == 30 && $status_jmoving >= 44 && $status_jmoving <= 47 && $storage_id_to > 0) {
                $db->query("UPDATE `J_MOVING` SET `status_jmoving`='45' WHERE `id` = $jmoving_id;");

                $rm = $db->query("SELECT MAX(`id`) as mid FROM `J_SELECT`;");
                $select_id = 0 + $db->result($rm,0,"mid");

                $rm = $db->query("SELECT * FROM `J_MOVING_SELECT_TEMP` WHERE `jmoving_id` = $jmoving_id AND `status_jmoving`='44';");
                $nm = $db->num_rows($rm);
                for ($im = 1; $im <= $nm; $im++) {
                    $select_id += 1;
                    $select_id_t = $db->result($rm,$im-1,"id");
                    $tpoint_id = $db->result($rm,$im-1,"tpoint_id");
                    $storage_id = $db->result($rm,$im-1,"storage_id");
                    $articles_amount = $db->result($rm,$im-1,"articles_amount");
                    $amount = $db->result($rm,$im-1,"amount");
                    $volume = $db->result($rm,$im-1,"volume");
                    $weight_netto = $db->result($rm,$im-1,"weight_netto");
                    $weight_brutto = $db->result($rm,$im-1,"weight_brutto");
                    $cur_date = date("Y-m-d H:i:s");
                    $db->query("INSERT INTO `J_SELECT` (`id`,`parrent_doc_type_id`,`parrent_doc_id`,`data_create`,`tpoint_id`,`storage_id`,`articles_amount`,`amount`,`volume`,`weight_netto`,`weight_brutto`,`status_select`,`user_create`) 
                    VALUES ('$select_id','1','$jmoving_id','$cur_date','$tpoint_id','$storage_id','$articles_amount','$amount','$volume','$weight_netto','$weight_brutto','82','$user_id');");
                    $db->query("DELETE FROM `J_MOVING_SELECT_TEMP` WHERE `jmoving_id` = $jmoving_id AND `id`='$select_id_t';");

                    $this->addJuornalRecord($jmoving_id, $select_id, $status_jmoving);

                    $rm2 = $db->query("SELECT * FROM `J_MOVING_SELECT_STR_TEMP` WHERE `jmoving_id` = $jmoving_id AND `select_id`='$select_id_t';");
                    $nm2 = $db->num_rows($rm2);
                    for ($im2 = 1; $im2 <= $nm2; $im2++) {
                        $id2 = $db->result($rm2,$im2-1,"id");
                        $art_id = $db->result($rm2,$im2-1,"art_id");
                        $article_nr_displ = $db->result($rm2,$im2-1,"article_nr_displ");
                        $brand_id = $db->result($rm2,$im2-1,"brand_id");
                        $amount = $db->result($rm2,$im2-1,"amount");
                        $storage_id_from = $db->result($rm2,$im2-1,"storage_id_from");
                        $parrent_doc_type_id = 0; // J_MOVING
                        $parrent_doc_id = 0; // J_MOVING_STR ID

                        $rsc = $dbt->query("SELECT * FROM `T2_ARTICLES_STRORAGE_CELLS` WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id_from;");
                        $nsc = $dbt->num_rows($rsc);
                        if ($nsc > 0) {
                            for ($isc = 1; $isc <= $nsc; $isc++) {
                                $er = 0;
                                $amount_sc = $dbt->result($rsc,$isc-1,"AMOUNT");
                                $reserv_amount_sc = $dbt->result($rsc,$isc-1,"RESERV_AMOUNT");
                                $storage_cells_id_sc = $dbt->result($rsc,$isc-1,"STORAGE_CELLS_ID");
                                if ($amount_sc >= $amount && $amount_sc > 0) {
                                    $isc = $nsc + 1;
                                    $er = 1;
                                    $amount_sc -= $amount;
                                    $reserv_amount_sc += $amount;
                                    $db->query("INSERT INTO `J_SELECT_STR` (`select_id`,`art_id`,`article_nr_displ`,`brand_id`,`amount`,`storage_id_from`,`cell_id_from`,`status`,`parrent_doc_type_id`,`parrent_doc_id`) 
                                    VALUES ('$select_id','$art_id','$article_nr_displ','$brand_id','$amount','$storage_id_from','$storage_cells_id_sc',1,'$parrent_doc_type_id','$parrent_doc_id');");
                                    $dbt->query("UPDATE `T2_ARTICLES_STRORAGE_CELLS` SET `AMOUNT`='$amount_sc', `RESERV_AMOUNT`='$reserv_amount_sc' 
                                    WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id_from AND `STORAGE_CELLS_ID` = $storage_cells_id_sc LIMIT 1;");
                                }
                                if ($amount_sc < $amount && $amount_sc > 0 && $er == 0) {
                                    $amount -= $amount_sc;
                                    $reserv_amount_sc += $amount_sc;
                                    $db->query("INSERT INTO `J_SELECT_STR` (`select_id`,`art_id`,`article_nr_displ`,`brand_id`,`amount`,`storage_id_from`,`cell_id_from`,`status`,`parrent_doc_type_id`,`parrent_doc_id`) 
                                    VALUES ('$select_id','$art_id','$article_nr_displ','$brand_id','$amount_sc','$storage_id_from','$storage_cells_id_sc',1,'$parrent_doc_type_id','$parrent_doc_id');");
                                    $dbt->query("UPDATE `T2_ARTICLES_STRORAGE_CELLS` SET `AMOUNT`='0', `RESERV_AMOUNT`='$reserv_amount_sc' 
                                    WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id_from AND `STORAGE_CELLS_ID` = $storage_cells_id_sc LIMIT 1;");
                                }
                            }
                        }
                        if ($nsc == 0) {
                            $rsc2 = $dbt->query("SELECT `AMOUNT` FROM `T2_ARTICLES_STRORAGE` WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id_from LIMIT 1;");
                            $nsc2 = $dbt->num_rows($rsc2);
                            if ($nsc2 == 1) {
                                $amount_sc = $dbt->result($rsc2, 0, "AMOUNT");
                                if ($amount_sc >= $amount && $amount_sc > 0) {
                                    $db->query("INSERT INTO `J_SELECT_STR` (`select_id`,`art_id`,`article_nr_displ`,`brand_id`,`amount`,`storage_id_from`,`parrent_doc_type_id`,`parrent_doc_id`) 
                                    VALUES ('$select_id','$art_id','$article_nr_displ','$brand_id','$amount','$storage_id_from','$parrent_doc_type_id','$parrent_doc_id');");
                                }
                            }
                        }
                        $db->query("DELETE FROM `J_MOVING_SELECT_STR_TEMP` WHERE `jmoving_id` = $jmoving_id AND `id` = $id2;");
                    }
                    $db->query("UPDATE `J_MOVING_STR` SET `status_jmoving`='45', `select_id`='$select_id' 
                    WHERE `jmoving_id` = $jmoving_id AND `storage_id_from` = $storage_id_from AND `status_jmoving`='44';");
                }
                $answer = 1; $err = "";
            }
        }
        return array($answer, $err);
    }

    function makesJmovingStorageSelectLocal($jmoving_id) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "";
        $jmoving_id=$slave->qq($jmoving_id);$storage_id_from=$cell_id_from=0;
        $r = $db->query("SELECT `oper_status`, `status_jmoving`, `storage_id_to` FROM `J_MOVING` WHERE `id` = $jmoving_id LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $oper_status=$db->result($r,0,"oper_status");
            $status_jmoving=$db->result($r,0,"status_jmoving");
            $storage_id_to=$db->result($r,0,"storage_id_to");
            if ($storage_id_to==0) {
                $answer = 0; $err = "Не зазначено склад переміщення.";
            }
            if ($status_jmoving>47 || $oper_status>30) {
                $answer = 0; $err = "Переміщення заблоковано. Зміни вносити заборонено.";
            }
            if ($oper_status==30 && $status_jmoving>=44 && $status_jmoving<=47 && $storage_id_to>0) {
                $db->query("UPDATE `J_MOVING` SET `status_jmoving`='45' WHERE `id` = $jmoving_id;");
                $rm=$db->query("SELECT MAX(`id`) as mid FROM `J_MOVING_SELECT`;");
                $select_id=0+$db->result($rm,0,"mid");
                $rm=$db->query("SELECT * FROM `J_MOVING_LOCAL_SELECT_TEMP` WHERE `jmoving_id` = $jmoving_id AND `status_jmoving`='44';");
                $nm=$db->num_rows($rm);
                for ($im=1;$im<=$nm;$im++) {
                    $select_id+=1;
                    $select_id_t=$db->result($rm,$im-1,"id");
                    $tpoint_id=$db->result($rm,$im-1,"tpoint_id");
                    $storage_id=$db->result($rm,$im-1,"storage_id");
                    $loc_type_id=$db->result($rm,$im-1,"loc_type_id");
                    $articles_amount=$db->result($rm,$im-1,"articles_amount");
                    $amount=$db->result($rm,$im-1,"amount");
                    $volume=$db->result($rm,$im-1,"volume");
                    $weight_netto=$db->result($rm,$im-1,"weight_netto");
                    $weight_brutto=$db->result($rm,$im-1,"weight_brutto");
                    $cur_date=date("Y-m-d");
                    $db->query("INSERT INTO `J_MOVING_SELECT` (`id`,`jmoving_id`,`data`,`tpoint_id`,`storage_id`,`loc_type_id`,`articles_amount`,`amount`,`volume`,`weight_netto`,`weight_brutto`,`status_jmoving`) 
                    VALUES ('$select_id','$jmoving_id','$cur_date','$tpoint_id','$storage_id','$loc_type_id','$articles_amount','$amount','$volume','$weight_netto','$weight_brutto','45');");
                    $db->query("DELETE FROM `J_MOVING_LOCAL_SELECT_TEMP` WHERE `jmoving_id` = $jmoving_id AND `id`='$select_id_t';");

                    $this->addJuornalRecord($jmoving_id, $select_id, $status_jmoving);

                    $rm2=$db->query("SELECT * FROM `J_MOVING_LOCAL_SELECT_STR_TEMP` WHERE `jmoving_id` = $jmoving_id AND `select_id` = $select_id_t;");
                    $nm2=$db->num_rows($rm2);
                    for ($im2=1;$im2<=$nm2;$im2++) {
                        $id2=$db->result($rm2,$im2-1,"id");
                        $art_id=$db->result($rm2,$im2-1,"art_id");
                        $article_nr_displ=$db->result($rm2,$im2-1,"article_nr_displ");
                        $brand_id=$db->result($rm2,$im2-1,"brand_id");
                        $amount=$db->result($rm2,$im2-1,"amount");
                        $storage_id_from=$db->result($rm2,$im2-1,"storage_id_from");
                        $cell_id_from=$db->result($rm2,$im2-1,"cell_id_from");
                        $cell_id_to=$db->result($rm2,$im2-1,"cell_id_to");

                        $db->query("INSERT INTO `J_MOVING_SELECT_STR` (`jmoving_id`,`select_id`,`art_id`,`article_nr_displ`,`brand_id`,`amount`,`storage_id_from`,`cell_id_from`,`storage_id_to`,`cell_id_to`,`status`) 
                        VALUES ('$jmoving_id','$select_id','$art_id','$article_nr_displ','$brand_id','$amount','$storage_id_from','$cell_id_from','$storage_id_from','$cell_id_to',1);");

                        $db->query("DELETE FROM `J_MOVING_LOCAL_SELECT_STR_TEMP` WHERE `jmoving_id` = $jmoving_id AND `id` = $id2;");
                    }
                    $db->query("UPDATE `J_MOVING_STR` SET `status_jmoving`='45', `select_id`='$select_id' 
                    WHERE `jmoving_id` = $jmoving_id AND `storage_id_from` = $storage_id_from AND `cell_id_from` = $cell_id_from AND `status_jmoving`='44';");
                }
                $answer = 1; $err = "";
            }
        }
        return array($answer, $err);
    }

    function clearJmovingStorageSelect($jmoving_id){$db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "";
        $jmoving_id=$slave->qq($jmoving_id);
        $r = $db->query("SELECT `oper_status`, `status_jmoving`, `storage_id_to` FROM `J_MOVING` WHERE `id` = $jmoving_id LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $oper_status=$db->result($r,0,"oper_status");
            $status_jmoving=$db->result($r,0,"status_jmoving");
            $storage_id_to=$db->result($r,0,"storage_id_to");
            if ($storage_id_to==0) {
                $answer = 0; $err = "Не зазначено склад переміщення.";
            }
            if ($status_jmoving>47 || $oper_status>30) {
                $answer = 0; $err = "Переміщення заблоковано. Зміни вносити заборонено.";
            }
            if ($oper_status==30 && $status_jmoving>=44 && $status_jmoving<=47 && $storage_id_to>0) {
                $db->query("DELETE FROM `J_MOVING_SELECT_TEMP` WHERE `jmoving_id` = $jmoving_id AND `status_jmoving`='44';");
                $db->query("DELETE FROM `J_MOVING_SELECT_STR_TEMP` WHERE `jmoving_id` = $jmoving_id;");
                $answer = 1; $err = "";
            }
        }
        return array($answer, $err);
    }

    function clearJmovingStorageSelectLocal($jmoving_id) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "";
        $jmoving_id = $slave->qq($jmoving_id);
        $r = $db->query("SELECT `oper_status`, `status_jmoving`, `storage_id_to` FROM `J_MOVING` WHERE `id` = $jmoving_id LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $oper_status=$db->result($r,0,"oper_status");
            $status_jmoving=$db->result($r,0,"status_jmoving");
            $storage_id_to=$db->result($r,0,"storage_id_to");
            if ($storage_id_to == 0) {
                $answer = 0; $err = "Не зазначено склад переміщення.";
            }
            if ($status_jmoving > 47 || $oper_status > 30) {
                $answer = 0; $err = "Переміщення заблоковано. Зміни вносити заборонено.";
            }
            if ($oper_status == 30 && $status_jmoving >= 44 && $status_jmoving <= 47 && $storage_id_to > 0) {
                $db->query("DELETE FROM `J_MOVING_LOCAL_SELECT_TEMP` WHERE `jmoving_id` = $jmoving_id AND `status_jmoving`='44';");
                $db->query("DELETE FROM `J_MOVING_LOCAL_SELECT_STR_TEMP` WHERE `jmoving_id` = $jmoving_id;");
                $answer = 1; $err = "";
            }
        }
        return array($answer, $err);
    }

    function loadJmovingStorageSelect($jmoving_id, $jmoving_status) { $db = DbSingleton::getDb();
        $form = ""; $form_htm = RD . "/tpl/jmoving_storage_select_list.htm";
        if ($jmoving_status == 45) {
            $form_htm = RD . "/tpl/jmoving_storage_select_list_finish.htm";
        }
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $r = $db->query("SELECT `status_jmoving` FROM `J_MOVING` WHERE `id` = $jmoving_id LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 0) {
            $form_htm = RD . "/tpl/access_deny.htm";
            if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        }
        if ($n == 1) {
            list($list, $kol_rows) = $this->showJmovingSkladStorageSelectList($jmoving_id, $jmoving_status);
            $form = str_replace("{SkladStorageSelectList}",$list,$form);
            $form = str_replace("{kol_rows}",$kol_rows,$form);
            $form = str_replace("{jmoving_id}",$jmoving_id,$form);
            if ($kol_rows == 1) {
                $kol_status2 = " disabled ";
                $kol_status1 = "";
            } else {
                $kol_status1 = " disabled ";
                $kol_status2 = "";
            }
            $form = str_replace("{kol_status1}",$kol_status1,$form);
            $form = str_replace("{kol_status2}",$kol_status2,$form);
        }
        return $form;
    }

    function loadJmovingStorageCount($jmoving_id) { $db = DbSingleton::getDb();
        $r = $db->query("SELECT sel.*, s.name as storage_name, t.name as tpoint_name 
        FROM `J_SELECT` sel
            LEFT OUTER JOIN `T_POINT` t on t.id=sel.tpoint_id
            LEFT OUTER JOIN `STORAGE` s on s.id=sel.storage_id
        WHERE sel.status=1 and sel.parrent_doc_type_id='1' and sel.parrent_doc_id='$jmoving_id' 
        ORDER BY sel.status_select ASC, sel.data_create DESC, sel.id DESC;");
        $n = $db->num_rows($r);
        if ($n == 0) {
            $r = $db->query("SELECT ms.*, p.name as tpoint_name, s.name as storage_name, ml.mcaption as loc_type_name, mt.mcaption as status_jmoving_name 
            FROM `J_MOVING_SELECT_TEMP` ms 
                LEFT OUTER JOIN `T_POINT` p on p.id=ms.tpoint_id
                LEFT OUTER JOIN `STORAGE` s on s.id=ms.storage_id
                LEFT OUTER JOIN `manual` ml on ml.id=ms.loc_type_id
                LEFT OUTER JOIN `manual` mt on mt.id=ms.status_jmoving
            WHERE ms.jmoving_id='$jmoving_id' and ms.status_jmoving='44' and ms.status='1' ORDER BY ms.id ASC;");
            $n = $db->num_rows($r);
        }
        ($n != 0) ?: $n = "";
        return $n;
    }

    function showJmovingSkladStorageSelectList($jmoving_id, $jmoving_status) { $db = DbSingleton::getDb();
        $gmanual = new gmanual;
        $list = ""; $n = 0;
        if ($jmoving_status == 44) {
            $r = $db->query("SELECT ms.*, p.name as tpoint_name, s.name as storage_name, ml.mcaption as loc_type_name, mt.mcaption as status_jmoving_name 
            FROM `J_MOVING_SELECT_TEMP` ms 
                LEFT OUTER JOIN `T_POINT` p on p.id=ms.tpoint_id
                LEFT OUTER JOIN `STORAGE` s on s.id=ms.storage_id
                LEFT OUTER JOIN `manual` ml on ml.id=ms.loc_type_id
                LEFT OUTER JOIN `manual` mt on mt.id=ms.status_jmoving
            WHERE ms.jmoving_id='$jmoving_id' AND ms.status_jmoving='$jmoving_status' AND ms.status='1' ORDER BY ms.id ASC;");
            $n = $db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $id=$db->result($r,$i-1,"id");
                $tpoint_name=$db->result($r,$i-1,"tpoint_name");
                $storage_name=$db->result($r,$i-1,"storage_name");
                $loc_type_name=$db->result($r,$i-1,"loc_type_name");
                $articles_amount=$db->result($r,$i-1,"articles_amount");
                $amount=$db->result($r,$i-1,"amount");
                $volume=$db->result($r,$i-1,"volume");
                $weight_netto=$db->result($r,$i-1,"weight_netto");
                $weight_brutto=$db->result($r,$i-1,"weight_brutto");
                $status_jmoving=$db->result($r,$i-1,"status_jmoving");
                $status_jmoving_name=$db->result($r,$i-1,"status_jmoving_name");
                $list .= "<tr id='strStsRow_$i'>
                    <td><span class='select_id' id='$id'>$i</span></td>
                    <td style='min-width:140px;'>$tpoint_name</td>
                    <td style='min-width:120px;'>$storage_name</td>
                    <td style='min-width:80px;'>$loc_type_name</td>
                    <td align='center' style='min-width:80px;'>$articles_amount</td>
                    <td align='center' style='min-width:80px;'>$amount</td>
                    <td align='right'>$volume</td>
                    <td align='right'>$weight_netto</td>
                    <td align='right'>$weight_brutto</td>
                    <td align='center'><button class='btn btn-xs btn-warning' onClick='cutJmovingStorage(\"$jmoving_id\",\"$id\");'><i class='fa fa-cut'></i></button></td>
                    <td align='center'><button class='btn btn-xs btn-primary' onClick='viewJmovingStorageSelect(\"$jmoving_id\",\"$id\",\"$status_jmoving\");'><i class='fa fa-eye'></i></button></td>
                    <td align='center'><button class='btn btn-xs btn-danger' onClick='dropJmovingStorageSelect(\"$jmoving_id\",\"$id\");'><i class='fa fa-trash'></i></button></td>
                    <td align='center'>$status_jmoving_name</td>
                </tr>";
            }
        }
        if ($jmoving_status > 44) {
            $loc_type_name = "";
            $r = $db->query("SELECT sel.*, s.name as storage_name, t.name as tpoint_name 
            FROM `J_SELECT` sel
                LEFT OUTER JOIN `T_POINT` t on t.id=sel.tpoint_id
                LEFT OUTER JOIN `STORAGE` s on s.id=sel.storage_id
            WHERE sel.status=1 AND sel.parrent_doc_type_id='1' AND sel.parrent_doc_id='$jmoving_id' ORDER BY sel.status_select ASC, sel.data_create DESC, sel.id DESC;");
            $n = $db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $id=$db->result($r,$i-1,"id");
                $tpoint_name=$db->result($r,$i-1,"tpoint_name");
                $storage_name=$db->result($r,$i-1,"storage_name");
                $articles_amount=$db->result($r,$i-1,"articles_amount");
                $amount=$db->result($r,$i-1,"amount");
                $volume=$db->result($r,$i-1,"volume");
                $weight_netto=$db->result($r,$i-1,"weight_netto");
                $weight_brutto=$db->result($r,$i-1,"weight_brutto");
                $status_select=$db->result($r,$i-1,"status_select");
                $status_select_cap=$gmanual->get_gmanual_caption($status_select);
                $list .= "<tr id='strStsRow_$i'>
                    <td><span class='select_id' id='$id'>$i</span></td>
                    <td style='min-width:140px;'>СкВ-$id</td>
                    <td style='min-width:140px;'>$tpoint_name</td>
                    <td style='min-width:120px;'>$storage_name</td>
                    <td style='min-width:80px;'>$loc_type_name</td>
                    <td align='center' style='min-width:80px;'>$articles_amount</td>
                    <td align='center' style='min-width:80px;'>$amount</td>
                    <td align='right'>$volume</td>
                    <td align='right'>$weight_netto</td>
                    <td align='right'>$weight_brutto</td>
                    <td align='center'><button class='btn btn-xs btn-primary' onClick='viewJmovingStorageSelect(\"$jmoving_id\",\"$id\",\"$status_select\");'><i class='fa fa-eye'></i></button></td>
                    <td align='center'>$status_select_cap</td>
                </tr>";
            }
        }
        return array($list, $n);
    }

    function loadJmovingStorageSelectLocal($jmoving_id, $jmoving_status) { $db = DbSingleton::getDb();
        $form = ""; $form_htm = RD . "/tpl/jmoving_local_storage_select_list.htm";
        if ($jmoving_status == 45) {
            $form_htm = RD . "/tpl/jmoving_local_storage_select_list_finish.htm";
        }
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $r = $db->query("SELECT `status_jmoving` FROM `J_MOVING` WHERE `id` = $jmoving_id LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 0) {
            $form_htm = RD . "/tpl/access_deny.htm";
            if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        }
        if ($n == 1) {
            list($list, $kol_rows) = $this->showJmovingSkladStorageSelectListLocal($jmoving_id, $jmoving_status);
            $form = str_replace("{SkladStorageSelectList}", $list, $form);
            $form = str_replace("{kol_rows}", $kol_rows, $form);
            $form = str_replace("{jmoving_id}", $jmoving_id, $form);
            $form = str_replace("{kol_status1}", ($kol_rows == 1) ? "" : " disabled ", $form);
            $form = str_replace("{kol_status2}", ($kol_rows == 1) ? " disabled " : "", $form);
        }
        return $form;
    }

    function showJmovingSkladStorageSelectListLocal($jmoving_id, $jmoving_status) { $db = DbSingleton::getDb();
        $list="";
        $tmp="J_MOVING_SELECT"; $where_status="and ms.status_jmoving='$jmoving_status'";
        if ($jmoving_status==44) { $tmp="J_MOVING_LOCAL_SELECT_TEMP"; }
        if ($jmoving_status>44) { $where_status="and ms.status_jmoving in (45,46,47,48)"; }
        $r = $db->query("SELECT ms.*, p.name as tpoint_name, s.name as storage_name, mt.mcaption as status_jmoving_name 
        FROM $tmp ms 
            LEFT OUTER JOIN `T_POINT` p on p.id=ms.tpoint_id
            LEFT OUTER JOIN `STORAGE` s on s.id=ms.storage_id
            LEFT OUTER JOIN `manual` mt on mt.id=ms.status_jmoving
        WHERE ms.jmoving_id='$jmoving_id' $where_status and ms.status='1' ORDER BY ms.id asc;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id=$db->result($r,$i-1,"id");
            $tpoint_name=$db->result($r,$i-1,"tpoint_name");
            $storage_name=$db->result($r,$i-1,"storage_name");
            $articles_amount=$db->result($r,$i-1,"articles_amount");
            $amount=$db->result($r,$i-1,"amount");
            $volume=$db->result($r,$i-1,"volume");
            $weight_netto=$db->result($r,$i-1,"weight_netto");
            $weight_brutto=$db->result($r,$i-1,"weight_brutto");
            $status_jmoving=$db->result($r,$i-1,"status_jmoving");
            $status_jmoving_name=$db->result($r,$i-1,"status_jmoving_name");
            $list .= "<tr id='strStsRow_$i'><td>$i</td>";
            if ($jmoving_status > 44) {
                $list .= "<td style='min-width:140px;'>СкВн-$id</td>";
            }
            $list .= "<td style='min-width:140px;'>$tpoint_name</td>
                <td style='min-width:120px;'>$storage_name</td>
                <td align='center' style='min-width:80px;'>$articles_amount</td>
                <td align='center' style='min-width:80px;'>$amount</td>
                <td align='right'>$volume</td>
                <td align='right'>$weight_netto</td>
                <td align='right'>$weight_brutto</td>";
                $list .= "<td align='center'><button class='btn btn-xs btn-primary' onClick='viewJmovingStorageSelectLocal(\"$jmoving_id\",\"$id\",\"$status_jmoving\");'><i class='fa fa-eye'></i></button></td>";
                if ($jmoving_status == 44) {
                    $list .= "<td align='center'><button class='btn btn-xs btn-danger' onClick='dropJmovingStorageSelectLocal(\"$jmoving_id\",\"$id\");'><i class='fa fa-trash'></i></button></td>";
                }
            $list .= "<td align='center'>$status_jmoving_name</td></tr>";
        }
        return array($list, $n);
    }

    function getTpointDataByStorage($storage_id) { $db = DbSingleton::getDb();
        $tpoint_id = 0; $loc_type_id = 0;
        $r = $db->query("SELECT `tpoint_id`, `local` FROM `T_POINT_STORAGE` WHERE `storage_id`='$storage_id' ORDER BY `id` ASC LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $tpoint_id = $db->result($r,0,"tpoint_id");
            $loc_type_id = $db->result($r,0,"local");
        }
        return array($tpoint_id, $loc_type_id);
    }

    function viewJmovingStorageSelect($jmoving_id, $select_id, $jmoving_status) { $db = DbSingleton::getDb();
        $gmanual=new gmanual;
        $select_status=0; $form=""; $list="";
        if ($jmoving_status==44) {
            $form_htm=RD."/tpl/jmoving_storage_select_view.htm"; $tmp="_TEMP";
            $disabled46=" disabled";$disabled47=" disabled";$disabled48=" disabled";
            if ($jmoving_status==45){$disabled46=" ";}
            if ($jmoving_status==46){$disabled47=" ";}
            if ($jmoving_status==47 || $jmoving_status==48){
                $disabled48=" ";
                $form_htm=RD."/tpl/jmoving_storage_select_view_finish.htm";
            }
            if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }

            $r = $db->query("SELECT * FROM `J_MOVING_SELECT_STR$tmp` WHERE `jmoving_id` = $jmoving_id AND `select_id` = $select_id ORDER BY `id` ASC;");
            $n = $db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $id=$db->result($r,$i-1,"id");
                $art_id=$db->result($r,$i-1,"art_id");
                $article_nr_displ=$db->result($r,$i-1,"article_nr_displ");
                $brand_id=$db->result($r,$i-1,"brand_id");
                $brand_name=$this->getBrandName($brand_id);
                $amount=$db->result($r,$i-1,"amount");
                $storage_id_from=$db->result($r,$i-1,"storage_id_from");
                $storage_name_from=$this->getStorageName($storage_id_from);
                $list47="";
                if ($jmoving_status==47 || $jmoving_status==48) {
                    $amount_barcodes=$db->result($r,$i-1,"amount_barcodes");
                    $amount_barcodes_noscan=$db->result($r,$i-1,"amount_barcodes_noscan");
                    $amount_accept=$amount_barcodes+$amount_barcodes_noscan;
                    $select_bug_list=$this->getStorageSelectBugList($jmoving_id,$select_id,$art_id,$id);
                    $amount_bug=$db->result($r,$i-1,"amount_bug");
                    $list47="<td>$amount_accept</td>
                    <td>$amount_bug</td>
                    <td>$select_bug_list</td>";
                }
                $list.="<tr align='right'>
                    <td align='left'>$i</td>
                    <td align='left'>$article_nr_displ</td>
                    <td align='center'>$brand_name</td>
                    <td>$storage_name_from</td>
                    <td>$amount</td>
                    $list47
                </tr>";
            }
            list(,,,,,,,,,,,$select_datatime)=$this->getJmovingSkladStorageSelectInfo($jmoving_id, $select_id);
            $form = str_replace("{select_start}",$select_datatime,$form);
            $form = str_replace("{ArticlesList}",$list,$form);
            $form = str_replace("{select_id}",$select_id,$form);
            $form = str_replace("{jmoving_id}",$jmoving_id,$form);
            $form = str_replace("{disabled46}",$disabled46,$form);
            $form = str_replace("{disabled47}",$disabled47,$form);
            $form = str_replace("{disabled48}",$disabled48,$form);
            $form = str_replace("{preview-hidden}",($jmoving_status == 44) ? "hidden disabled" : "",$form);
            $data_records=$this->getJmovingSelectJournalRecords($jmoving_id, $select_id);
            $form = str_replace("{data_46}",$data_records[46],$form);
            $form = str_replace("{data_52}",$data_records[52],$form);
            $form = str_replace("{data_47}",$data_records[47],$form);
            $form = str_replace("{data_48}",$data_records[48],$form);
        }
        if ($jmoving_status>44) {
            $storsel=new storsel; $gmanual=new gmanual;
            $select_status=$jmoving_status;
            $form_htm=RD."/tpl/jmoving_storage_select_view_finish.htm";
            if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
            $r = $db->query("SELECT * FROM `J_SELECT_STR` WHERE `select_id` = $select_id AND `status`=1 ORDER BY `id` ASC;");
            $n = $db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $id=$db->result($r,$i-1,"id");
                $art_id=$db->result($r,$i-1,"art_id");
                $article_nr_displ=$db->result($r,$i-1,"article_nr_displ");
                $brand_id=$db->result($r,$i-1,"brand_id");$brand_name=$this->getBrandName($brand_id);
                $amount=$db->result($r,$i-1,"amount");
                $storage_id_from=$db->result($r,$i-1,"storage_id_from");
                $storage_name_from=$this->getStorageName($storage_id_from);
                $amount_barcodes=$db->result($r,$i-1,"amount_barcodes");
                $amount_barcodes_noscan=$db->result($r,$i-1,"amount_barcodes_noscan");
                $amount_accept=$amount_barcodes+$amount_barcodes_noscan;
                $select_bug_list=$this->getStorageSelectBugList($jmoving_id,$select_id,$art_id,$id);
                $amount_bug=$db->result($r,$i-1,"amount_bug");
                $list.="<tr align='right'>
                    <td align='left'>$i</td>
                    <td align='left'>$article_nr_displ</td>
                    <td align='center'>$brand_name</td>
                    <td>$storage_name_from</td>
                    <td>$amount</td>
                    <td>$amount_accept</td>
                    <td>$amount_bug</td>
                    <td>$select_bug_list</td>
                </tr>";
            }
            list(, $data_create, $data_start, $data_collect,,,,, $volume, $weight_netto, $weight_brutto) = $storsel->getStorselInfo($select_id);
            $form = str_replace("{select_id}",$select_id,$form);
            $form = str_replace("{data_create}",$data_create,$form);
            $form = str_replace("{data_start}",$data_start,$form);
            $form = str_replace("{data_collect}",$data_collect,$form);
            $form = str_replace("{volume}",$volume,$form);
            $form = str_replace("{weight_netto}",$weight_netto,$form);
            $form = str_replace("{weight_brutto}",$weight_brutto,$form);
            $form = str_replace("{ArticlesList}",$list,$form);
        }
        return array($form, "Структура складського відбору № СкВ-$select_id; Статус відбору: " . $gmanual->get_gmanual_caption($select_status));
    }

    function viewJmovingStorageSelectLocal($jmoving_id, $select_id, $jmoving_status) { $db = DbSingleton::getDb();
        $list="";
        $form = ""; $form_htm = RD . "/tpl/jmoving_local_storage_select_view.htm";
        $tmp = "J_MOVING_SELECT_STR";
        if ($jmoving_status == 44) {
            $tmp = "J_MOVING_LOCAL_SELECT_STR_TEMP";
        }
        $disabled46 = " disabled"; $disabled47 = " disabled"; $disabled48 = " disabled";
        if ($jmoving_status == 45) {$disabled46 = " ";}
        if ($jmoving_status == 46) {$disabled47 = " ";}
        if ($jmoving_status == 47 || $jmoving_status == 48) {
            $disabled48 = " ";
            $form_htm = RD . "/tpl/jmoving_local_storage_select_view_finish.htm";
        }
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }

        $r = $db->query("SELECT `article_nr_displ`, `brand_id`, `amount`, `cell_id_from`, `cell_id_to` FROM `$tmp` WHERE `jmoving_id` = $jmoving_id AND `select_id` = $select_id ORDER BY `id` ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $article_nr_displ = $db->result($r, $i - 1, "article_nr_displ");
            $brand_id = $db->result($r, $i - 1, "brand_id");
            $amount = $db->result($r, $i - 1, "amount");
            $cell_id_from = $db->result($r, $i - 1, "cell_id_from");
            $cell_id_to = $db->result($r, $i - 1, "cell_id_to");
            $brand_name = $this->getBrandName($brand_id);
            $cell_name_from = $this->getStorageCellName($cell_id_from);
            $cell_name_to = $this->getStorageCellName($cell_id_to);
            $list .= "<tr align='right'>
                <td align='left'>$i</td>
                <td align='left'>$article_nr_displ</td>
                <td align='center'>$brand_name</td>
                <td>$cell_name_from</td>
                <td>$cell_name_to</td>
                <td>$amount</td>
            </tr>";
        }
        list(,,,,,,,,,,, $select_datatime) = $this->getJmovingSkladStorageSelectInfoLocal($jmoving_id, $select_id);
        $form = str_replace("{select_start}",$select_datatime,$form);
        $form = str_replace("{ArticlesList}",$list,$form);
        $form = str_replace("{select_id}",$select_id,$form);
        $form = str_replace("{jmoving_id}",$jmoving_id,$form);
        $form = str_replace("{disabled46}",$disabled46,$form);
        $form = str_replace("{disabled47}",$disabled47,$form);
        $form = str_replace("{disabled48}",$disabled48,$form);
        $form = str_replace("{preview-hidden}",($jmoving_status == 44) ? "hidden disabled" : "",$form);
        $data_records = $this->getJmovingSelectJournalRecords($jmoving_id, $select_id);
        $form = str_replace("{data_46}",$data_records[46],$form);
        $form = str_replace("{data_52}",$data_records[52],$form);
        $form = str_replace("{data_47}",$data_records[47],$form);
        $form = str_replace("{data_48}",$data_records[48],$form);
        return array($form, "Структура складського відбору № СкВн-$select_id");
    }

    function dropJmovingStorageSelect($jmoving_id, $select_id) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка видалення запису!";
        $jmoving_id = $slave->qq($jmoving_id); $select_id = $slave->qq($select_id);
        if ($jmoving_id > 0 && $select_id > 0) {
            $r = $db->query("SELECT COUNT(`id`) as kol FROM `J_MOVING_SELECT_STR_TEMP` WHERE `jmoving_id` = $jmoving_id AND `select_id` = $select_id;");
            $kol = $db->result($r,0,"kol");
            if ($kol > 0) {
                $db->query("DELETE FROM `J_MOVING_SELECT_STR_TEMP` WHERE `jmoving_id` = $jmoving_id AND `select_id` = $select_id;");
                $db->query("DELETE FROM `J_MOVING_SELECT_TEMP` WHERE `jmoving_id` = $jmoving_id AND `id` = $select_id;");
                $db->query("DELETE FROM `J_MOVING_STR` WHERE `jmoving_id` = $jmoving_id AND `select_id` = 0;");
                $answer = 1; $err = "";
            }
        }
        return array($answer, $err);
    }

    function dropJmovingStorageSelectLocal($jmoving_id, $select_id) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка видалення запису!";
        $jmoving_id = $slave->qq($jmoving_id); $select_id = $slave->qq($select_id);
        if ($jmoving_id > 0 && $select_id > 0) {
            $r = $db->query("SELECT COUNT(`id`) as kol FROM `J_MOVING_LOCAL_SELECT_STR_TEMP` WHERE `jmoving_id` = $jmoving_id AND `select_id` = $select_id;");
            $kol = $db->result($r, 0, "kol");
            if ($kol > 0) {
                $db->query("DELETE FROM `J_MOVING_LOCAL_SELECT_STR_TEMP` WHERE `jmoving_id` = $jmoving_id AND `select_id` = $select_id;");
                $db->query("DELETE FROM `J_MOVING_LOCAL_SELECT_TEMP` WHERE `jmoving_id` = $jmoving_id AND `id` = $select_id;");
                $db->query("DELETE FROM `J_MOVING_STR` WHERE `jmoving_id` = $jmoving_id AND `select_id` = 0;");
                $answer = 1; $err = "";
            }
        }
        return array($answer, $err);
    }

    function collectJmovingStorageSelect($jmoving_id, $select_id) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка обробки запису!";
        $jmoving_id = $slave->qq($jmoving_id); $select_id = $slave->qq($select_id);
        if ($jmoving_id > 0 && $select_id > 0) {
            $r = $db->query("SELECT `status_jmoving` FROM `J_MOVING_SELECT` WHERE `jmoving_id` = $jmoving_id AND `id` = $select_id LIMIT 1;");
            $n = $db->num_rows($r);
            if ($n == 1) {
                $status_jmoving = $db->result($r, 0, "status_jmoving");
                if ($status_jmoving == 45) {
                    $db->query("UPDATE `J_MOVING_SELECT` SET `status_jmoving` = 46 WHERE `jmoving_id` = $jmoving_id AND `id` = $select_id LIMIT 1;");
                    $this->addJuornalRecord($jmoving_id, $select_id, 46);
                    $answer = 1; $err = "";
                }
            }
        }
        return array($answer, $err);
    }

    function collectJmovingStorageSelectLocal($jmoving_id, $select_id) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка обробки запису!";
        $jmoving_id = $slave->qq($jmoving_id); $select_id = $slave->qq($select_id);
        if ($jmoving_id > 0 && $select_id > 0) {
            $r = $db->query("SELECT `status_jmoving` FROM `J_MOVING_SELECT` WHERE `jmoving_id` = $jmoving_id AND `id` = $select_id LIMIT 1;");
            $n = $db->num_rows($r);
            if ($n == 1) {
                $status_jmoving = $db->result($r, 0, "status_jmoving");
                if ($status_jmoving == 45) {
                    $db->query("UPDATE `J_MOVING_SELECT` SET `status_jmoving` = 46 WHERE `jmoving_id` = $jmoving_id AND `id` = $select_id LIMIT 1;");
                    $this->addJuornalRecord($jmoving_id,$select_id, 46);
                    $answer = 1; $err = "";
                }
            }
        }
        return array($answer, $err);
    }

    function cutJmovingStorageAll($jmoving_id, $select, $comment) { $db = DbSingleton::getDb();
        $slave = new slave;
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $answer = 0; $err = "Помилка обробки даних!";
        $ids = []; $comment_sent = "Передано в ID: ";
        $jmoving_id = $slave->qq($jmoving_id);
        foreach ($select as $select_id) {
            $r = $db->query("SELECT COUNT(`id`) as kol FROM `J_MOVING_SELECT_STR_TEMP` WHERE `jmoving_id` = $jmoving_id AND `select_id` = $select_id;");
            $kol = $db->result($r, 0, "kol");
            if ($kol > 0) {
                $rm = $db->query("SELECT MAX(`id`) as mid FROM `J_MOVING`;");
                $new_jmoving_id = 0 + $db->result($rm, 0, "mid") + 1;
                $rm = $db->query("SELECT * FROM `J_MOVING` WHERE `id` = $jmoving_id LIMIT 1;");
                $nm = $db->num_rows($rm);
                if ($nm == 1) {
                    $prefix = $db->result($rm, 0, "prefix");
                    $type_id = $db->result($rm, 0, "type_id");
                    $doc_nom = $this->get_df_doc_nom_new();
                    $data = date("Y-m-d");
                    $storage_id_to = $db->result($rm, 0, "storage_id_to");
                    $cell_use = $db->result($rm, 0, "cell_use");
                    $cell_id_to = $db->result($rm, 0, "cell_id_to");

                    $db->query("INSERT INTO `J_MOVING` (`id`,`prefix`,`doc_nom`,`type_id`,`data`,`storage_id_to`,`cell_use`,`cell_id_to`,`user_id`,`comment`) 
                    VALUES ('$new_jmoving_id','$prefix','$doc_nom','$type_id','$data','$storage_id_to','$cell_use','$cell_id_to','$user_id','$comment');");

                    $rs = $db->query("SELECT * FROM `J_MOVING_SELECT_STR_TEMP` WHERE `jmoving_id` = $jmoving_id AND `select_id` = $select_id ORDER BY `id` ASC;");
                    $ns = $db->num_rows($rs);
                    for ($is = 1; $is <= $ns; $is++) {
                        $art_id = $db->result($rs, $is - 1, "art_id");
                        $article_nr_displ = $db->result($rs, $is - 1, "article_nr_displ");
                        $brand_id = $db->result($rs, $is - 1, "brand_id");
                        $amount = $db->result($rs, $is - 1, "amount");
                        $storage_id_from = $db->result($rs, $is - 1, "storage_id_from");
                        $cell_id_from = $db->result($rs, $is - 1, "cell_id_from");
                        $db->query("INSERT INTO `J_MOVING_STR` (`jmoving_id`,`art_id`,`article_nr_displ`,`brand_id`,`amount`,`storage_id_from`,`cell_id_from`,`user_id`) 
                        VALUES ('$new_jmoving_id','$art_id','$article_nr_displ','$brand_id','$amount','$storage_id_from','$cell_id_from','$user_id');");
                    }
                    $comment_sent .= $new_jmoving_id . " ";
                    $this->updateJmovingWeightVolume($jmoving_id);
                    $this->updateJmovingWeightVolume($new_jmoving_id);
                    $answer = 1; $err = "";
                    array_push($ids, $new_jmoving_id);
                }
            }
        }
        $db->query("UPDATE `J_MOVING` SET `status` = 0, `status_jmoving` = 106, `comment` = '$comment_sent' WHERE `id` = $jmoving_id;");
        $db->query("UPDATE `J_MOVING_STR` SET `status_jmoving` = 106 WHERE `jmoving_id` = $jmoving_id AND `status_jmoving` = 44;");
        return array($answer, $err, $ids);
    }

    function cutJmovingStorage($jmoving_id, $select_id) { $db = DbSingleton::getDb();
        $slave = new slave;
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $answer = 0; $err = "Помилка обробки даних!";
        $jmoving_id=$slave->qq($jmoving_id); $select_id=$slave->qq($select_id);
        if ($jmoving_id > 0 && $select_id > 0) {
            $r = $db->query("SELECT COUNT(`id`) as kol FROM `J_MOVING_SELECT_STR_TEMP` WHERE `jmoving_id` = $jmoving_id AND `select_id` = $select_id;");
            $kol = $db->result($r,0,"kol");
            if ($kol > 0) {
                $rm = $db->query("SELECT MAX(`id`) as mid FROM `J_MOVING`;");
                $new_jmoving_id = 0 + $db->result($rm,0,"mid") + 1;
                $rm = $db->query("SELECT * FROM `J_MOVING` WHERE `id` = $jmoving_id LIMIT 1;");
                $nm = $db->num_rows($rm);
                if ($nm == 1) {
                    $prefix = $db->result($rm,0,"prefix");
                    $type_id = $db->result($rm,0,"type_id");
                    $doc_nom = $this->get_df_doc_nom_new();
                    $data = date("Y-m-d");
                    $storage_id_to = $db->result($rm,0,"storage_id_to");
                    $cell_use = $db->result($rm,0,"cell_use");
                    $cell_id_to = $db->result($rm,0,"cell_id_to");
                    $db->query("INSERT INTO `J_MOVING` (`id`,`prefix`,`doc_nom`,`type_id`,`data`,`storage_id_to`,`cell_use`,`cell_id_to`,`user_id`) 
                    VALUES ('$new_jmoving_id','$prefix','$doc_nom','$type_id','$data','$storage_id_to','$cell_use','$cell_id_to','$user_id');");
                    $rs = $db->query("SELECT * FROM `J_MOVING_SELECT_STR_TEMP` WHERE `jmoving_id` = $jmoving_id AND `select_id` = $select_id ORDER BY `id` ASC;");
                    $ns = $db->num_rows($rs);
                    for ($is = 1; $is <= $ns; $is++) {
                        $art_id = $db->result($rs,$is-1,"art_id");
                        $article_nr_displ = $db->result($rs,$is-1,"article_nr_displ");
                        $brand_id = $db->result($rs,$is-1,"brand_id");
                        $amount = $db->result($rs,$is-1,"amount");
                        $storage_id_from = $db->result($rs,$is-1,"storage_id_from");
                        $cell_id_from = $db->result($rs,$is-1,"cell_id_from");
                        $db->query("INSERT INTO `J_MOVING_STR` (`jmoving_id`,`art_id`,`article_nr_displ`,`brand_id`,`amount`,`storage_id_from`,`cell_id_from`,`user_id`) 
                        VALUES ('$new_jmoving_id','$art_id','$article_nr_displ','$brand_id','$amount','$storage_id_from','$cell_id_from','$user_id');");
                    }
                    $db->query("DELETE FROM `J_MOVING_STR` WHERE `jmoving_id` = $jmoving_id AND `select_id`='0';");
                    $db->query("DELETE FROM `J_MOVING_SELECT_STR_TEMP` WHERE `jmoving_id` = $jmoving_id AND `select_id` = $select_id;");
                    $db->query("DELETE FROM `J_MOVING_SELECT_TEMP` WHERE `jmoving_id` = $jmoving_id AND `id`='$select_id';");
                    $this->updateJmovingWeightVolume($jmoving_id);
                    $this->updateJmovingWeightVolume($new_jmoving_id);
                    $answer = 1; $err = "";
                }
            }
        }
        return array($answer, $err);
    }

    function getJmovingSkladStorageSelectInfo($jmoving_id, $select_id) { $db = DbSingleton::getDb();
        $r = $db->query("SELECT ms.*, p.name as tpoint_name, s.name as storage_name, ml.mcaption as loc_type_name, mt.mcaption as status_jmoving_name, j.storage_id_to, j.comment 
        FROM `J_MOVING_SELECT` ms 
            LEFT OUTER JOIN `J_MOVING` j on j.id=ms.jmoving_id
            LEFT OUTER JOIN `T_POINT` p on p.id=ms.tpoint_id
            LEFT OUTER JOIN `STORAGE` s on s.id=ms.storage_id
            LEFT OUTER JOIN `manual` ml on ml.id=ms.loc_type_id
            LEFT OUTER JOIN `manual` mt on mt.id=ms.status_jmoving
        WHERE ms.jmoving_id='$jmoving_id' AND ms.id='$select_id' AND ms.status='1' 
        ORDER BY ms.id ASC LIMIT 1;");
        $data=$db->result($r,0,"data");
        $datatime=$db->result($r,0,"datatime");
        $storage_id=$db->result($r,0,"storage_id");
        $storage_name=$db->result($r,0,"storage_name");
        $storage_id_to=$db->result($r,0,"storage_id_to");
        $storage_name_to=$this->getStorageName($storage_id_to);
        $articles_amount=$db->result($r,0,"articles_amount");
        $amount=$db->result($r,0,"amount");
        $volume=$db->result($r,0,"volume");
        $weight_netto=$db->result($r,0,"weight_netto");
        $weight_brutto=$db->result($r,0,"weight_brutto");
        $comment=$db->result($r,0,"comment");
        return array("СКВ-$select_id/$storage_name", $data, $storage_id, $storage_name, $storage_name_to, $articles_amount, $amount, $volume, $weight_netto, $weight_brutto, $comment, $datatime);
    }

    function getJmovingSkladStorageSelectInfoLocal($jmoving_id, $select_id) { $db = DbSingleton::getDb();
        $r = $db->query("SELECT ms.*, p.name as tpoint_name, s.name as storage_name, mt.mcaption as status_jmoving_name, j.storage_id_to, j.comment 
        FROM `J_MOVING_SELECT` ms 
            LEFT OUTER JOIN `J_MOVING` j on j.id=ms.jmoving_id
            LEFT OUTER JOIN `T_POINT` p on p.id=ms.tpoint_id
            LEFT OUTER JOIN `STORAGE` s on s.id=ms.storage_id
            LEFT OUTER JOIN `manual` mt on mt.id=ms.status_jmoving
        WHERE ms.jmoving_id='$jmoving_id' AND ms.id='$select_id' AND ms.status='1' 
        ORDER BY ms.id ASC LIMIT 1;");
        $data = $db->result($r,0,"data");
        $datatime = $db->result($r,0,"datatime");
        $storage_id = $db->result($r,0,"storage_id");
        $storage_name = $db->result($r,0,"storage_name");
        $storage_id_to = $db->result($r,0,"storage_id_to");
        $storage_name_to = $this->getStorageName($storage_id_to);
        $articles_amount = $db->result($r,0,"articles_amount");
        $amount = $db->result($r,0,"amount");
        $volume = $db->result($r,0,"volume");
        $weight_netto = $db->result($r,0,"weight_netto");
        $weight_brutto = $db->result($r,0,"weight_brutto");
        $comment = $db->result($r,0,"comment");
        return array("СкВн-$select_id/$storage_name", $data, $storage_id, $storage_name, $storage_name_to, $articles_amount, $amount, $volume, $weight_netto, $weight_brutto, $comment, $datatime);
    }

    function printJmovingStorageSelect($jmoving_id, $select_id) { $db = DbSingleton::getDb();
        $slave = new slave;
        session_start();
        $user_name = $_SESSION["user_name"];
        $list = "";
        $form = ""; $form_htm = RD . "/tpl/jmoving_storage_select_print.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $r = $db->query("SELECT * FROM `J_MOVING_SELECT_STR` WHERE `jmoving_id` = $jmoving_id AND `select_id` = $select_id ORDER BY `id` ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $art_id = $db->result($r,$i-1,"art_id");
            $article_nr_displ = $db->result($r,$i-1,"article_nr_displ");
            $brand_id = $db->result($r,$i-1,"brand_id");
            $amount = $db->result($r,$i-1,"amount");
            $cell_id_from = $db->result($r,$i-1,"cell_id_from");
            $article_name = $this->getArticleName($art_id);
            $brand_name = $this->getBrandName($brand_id);
            $cell_name_from = $this->getStorageCellName($cell_id_from);
            $list .= "<tr>
                <td align='center'>$i</td>
                <td align='center'>$cell_name_from</td>
                <td align='center'>$article_nr_displ</td>
                <td align='center'>$brand_name</td>
                <td align='center'>$amount</td>
                <td align='left'>$article_name</td>
                <td>&nbsp;</td>
            </tr>";
        }
        $form = str_replace("{ArticlesList}",$list,$form);
        $form = str_replace("{select_id}",$select_id,$form);
        $form = str_replace("{jmoving_id}",$jmoving_id,$form);
        list($select_nom, $select_data,, $storage_name, $storage_name_to, $articles_amount, $amount, $volume, $weight_netto, $weight_brutto, $jmoving_comment,) = $this->getJmovingSkladStorageSelectInfo($jmoving_id, $select_id);
        $form = str_replace("{select_nom}",$select_nom,$form);
        $form = str_replace("{select_data}",$select_data,$form);
        $form = str_replace("{storage_from}",$storage_name,$form);
        $form = str_replace("{storage_to}",$storage_name_to,$form);
        $form = str_replace("{articles_amount}",$articles_amount,$form);
        $form = str_replace("{amount}",$amount,$form);
        $form = str_replace("{volume}",$volume,$form);
        $form = str_replace("{weight_netto}",$weight_netto,$form);
        $form = str_replace("{weight_brutto}",$weight_brutto,$form);
        $form = str_replace("{jmoving_comment}",$jmoving_comment,$form);
        $form = str_replace("{user_name}",$user_name,$form);
        $form = str_replace("{curtime}",date("Y-m-d H:i:s"),$form);
        $form = str_replace("{pData}",$slave->data_word(""),$form);
        $form = str_replace("{qrImage}","<img src='/phpqrcode/qrimage2.php?url=http://portal.myparts.pro/Jmoving/printJmS1/$jmoving_id/$select_id/".time()."'>",$form);
        $this->addJuornalRecord($jmoving_id, $select_id, 52);
        $mp = new media_print;
        $mp->print_document($form, array(210, 280));
        return $form;
    }

    function printJmovingStorageSelectLocal($jmoving_id, $select_id) { $db = DbSingleton::getDb();
        $slave = new slave;
        session_start();
        $user_name = $_SESSION["user_name"];
        $list = "";
        $form = ""; $form_htm = RD . "/tpl/jmoving_local_storage_select_print.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $r = $db->query("SELECT * FROM `J_MOVING_SELECT_STR` WHERE `jmoving_id` = $jmoving_id AND `select_id` = $select_id ORDER BY `id` ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $art_id = $db->result($r,$i-1,"art_id");
            $article_nr_displ = $db->result($r,$i-1,"article_nr_displ");
            $article_name = $this->getArticleName($art_id);
            $brand_id = $db->result($r,$i-1,"brand_id");
            $amount = $db->result($r,$i-1,"amount");
            $cell_id_from = $db->result($r,$i-1,"cell_id_from");
            $cell_id_to = $db->result($r,$i-1,"cell_id_to");
            $brand_name = $this->getBrandName($brand_id);
            $cell_name_from = $this->getStorageCellName($cell_id_from);
            $cell_name_to = $this->getStorageCellName($cell_id_to);
            $list .= "<tr>
                <td align='center'>$i</td>
                <td align='center'>$cell_name_from</td>
                <td align='center'>$article_nr_displ</td>
                <td align='center'>$brand_name</td>
                <td align='center'>$amount</td>
                <td align='center'>$cell_name_to</td>
                <td align='left'>$article_name</td>
                <td>&nbsp;</td>
            </tr>";
        }
        $form = str_replace("{ArticlesList}",$list,$form);
        $form = str_replace("{select_id}",$select_id,$form);
        $form = str_replace("{jmoving_id}",$jmoving_id,$form);
        list($select_nom, $select_data,, $storage_name, $storage_name_to, $articles_amount, $amount, $volume, $weight_netto, $weight_brutto, $jmoving_comment,) = $this->getJmovingSkladStorageSelectInfo($jmoving_id, $select_id);
        $form = str_replace("{select_nom}",$select_nom,$form);
        $form = str_replace("{select_data}",$select_data,$form);
        $form = str_replace("{storage_from}",$storage_name,$form);
        $form = str_replace("{storage_to}",$storage_name_to,$form);
        $form = str_replace("{articles_amount}",$articles_amount,$form);
        $form = str_replace("{amount}",$amount,$form);
        $form = str_replace("{volume}",$volume,$form);
        $form = str_replace("{weight_netto}",$weight_netto,$form);
        $form = str_replace("{weight_brutto}",$weight_brutto,$form);
        $form = str_replace("{jmoving_comment}",$jmoving_comment,$form);
        $form = str_replace("{user_name}",$user_name,$form);
        $form = str_replace("{curtime}",date("Y-m-d H:i:s"),$form);
        $form = str_replace("{pData}",$slave->data_word(""),$form);
        $form = str_replace("{qrImage}","<img src='/phpqrcode/qrimage2.php?url=http://portal.myparts.pro/Jmoving/printJmS1/$jmoving_id/$select_id/".time()."'>",$form);
        $this->addJuornalRecord($jmoving_id,$select_id,52);
        $mp = new media_print;
        $mp->print_document($form, array(210, 280));
        return $form;
    }

    function addJuornalRecord($jmoving_id, $select_id, $status_jmoving) { $db = DbSingleton::getDb();
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $db->query("INSERT INTO `J_MOVING_SELECT_JOURNAL` (`jmoving_id`,`select_id`,`user_id`,`status_jmoving`,`status_moving`) VALUES ('$jmoving_id','$select_id','$user_id','$jmoving_id','$status_jmoving');");
        return true;
    }

    function getJmovingSelectJournalRecords($jmoving_id, $select_id) { $db = DbSingleton::getDb();
        $data = array();
        $r = $db->query("SELECT `status_jmoving`, `datatime` FROM `J_MOVING_SELECT_JOURNAL` WHERE `jmoving_id` = $jmoving_id AND `select_id` = $select_id ORDER BY `id` ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $status_jmoving = $db->result($r, $i - 1, "status_jmoving");
            $datatime = $db->result($r, $i - 1, "datatime");
            $data[$status_jmoving] = $datatime;
        }
        return $data;
    }

    function showJmovingStorageSelectBarcodeForm($jmoving_id, $select_id) { $db = DbSingleton::getDb();
        $list = "";
        $form = ""; $form_htm = RD . "/tpl/jmoving_storage_select_barcode_list.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $r = $db->query("SELECT * FROM `J_MOVING_SELECT_STR` WHERE `jmoving_id` = $jmoving_id AND `select_id` = $select_id ORDER BY `id` ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r,$i-1,"id");
            $art_id = $db->result($r,$i-1,"art_id");
            $article_nr_displ = $db->result($r,$i-1,"article_nr_displ");
            $article_name = $this->getArticleName($art_id);
            $brand_id = $db->result($r,$i-1,"brand_id");
            $brand_name = $this->getBrandName($brand_id);
            $amount = $db->result($r,$i-1,"amount");
            $amount_barcodes = $db->result($r,$i-1,"amount_barcodes");
            $amount_barcodes_noscan = $db->result($r,$i-1,"amount_barcodes_noscan");
            $amount_bug = $db->result($r,$i-1,"amount_bug");
            $dif_amount_barcodes = $amount - $amount_barcodes - $amount_barcodes_noscan - $amount_bug;
            if ($dif_amount_barcodes < 0) {
                $dif_amount_barcodes = 0;
            }
            $storage_select_list = $this->getStorageSelectBugList($jmoving_id, $select_id, $art_id,$id);
            $list .= "<tr>
                <td align='center'>$i</td>
                <td align='center' id='amrai_$id'>$article_nr_displ</td>
                <td align='center' id='amrab_$id'>$brand_name</td>
                <td align='left' id='amran_$id'>$article_name</td>
                <td align='center'>$amount</td>
                <td align='center' id='amr_$id'>$amount_barcodes</td>
                <td align='center' id='amrd_$id'>$dif_amount_barcodes</td>
                <td align='center' id='amrns_$id'>$amount_barcodes_noscan</td>
                <td align='center'><button class='btn btn-xs btn-default' onclick='showJmovingStorageSelectNoscanForm(\"$jmoving_id\",\"$select_id\",\"$art_id\",\"$id\");' title='Фіксація без сканування'><i class='fa fa-cube'></i></button></td>
                <td align='center'><button class='btn btn-xs btn-danger' onclick='showJmovingStorageSelectBugForm(\"$jmoving_id\",\"$select_id\",\"$id\");' title='відхилення/брак/недостача'><i class='fa fa-bug'></i></button></td>
                <td align='center' id='ambg_$id'>$amount_bug</td>
                <td id='ssbug_$id'>$storage_select_list</td>
            </tr>";
        }
        $form = str_replace("{ArticlesList}",$list,$form);
        $form = str_replace("{select_id}",$select_id,$form);
        $form = str_replace("{jmoving_id}",$jmoving_id,$form);
        list($select_nom, $select_data,,,,,,,,, $jmoving_comment,) = $this->getJmovingSkladStorageSelectInfo($jmoving_id, $select_id);
        $form = str_replace("{select_nom}",$select_nom,$form);
        $form = str_replace("{select_data}",$select_data,$form);
        $form = str_replace("{jmoving_comment}",$jmoving_comment,$form);
        $form = str_replace("{user_name}","",$form);
        $form = str_replace("{curtime}",date("Y-m-d H:i:s"),$form);
        $answer = 1; $err = "";
        return array($answer, $err, $form, "Пакування товару по штрих-кодам");
    }

    function saveJmovingStorageSelectBarcodeForm($jmoving_id, $select_id, $barcode) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка індексу!!";
        $jmoving_id=$slave->qq($jmoving_id); $select_id=$slave->qq($select_id); $barcode=$slave->qq($barcode);
        $id = $amount_barcodes = $dif_amount_barcodes = 0;
        if ($jmoving_id > 0 && $select_id > 0 && $barcode != "") {
            $art_id = $this->getArtIdByBarcode($barcode);
            $r = $db->query("SELECT * FROM `J_MOVING_SELECT_STR` WHERE `jmoving_id` = $jmoving_id AND `select_id` = $select_id AND `art_id`='$art_id' AND amount>amount_barcodes LIMIT 1;");
            $n = $db->num_rows($r);
            if ($n == 1) {
                $id = $db->result($r,0,"id");
                $amount = $db->result($r,0,"amount");
                $amount_barcodes = $db->result($r,0,"amount_barcodes");
                $amount_barcodes_noscan = $db->result($r,0,"amount_barcodes_noscan");
                $amount_bug = $db->result($r,0,"amount_bug");
                if ($amount > ($amount_barcodes + $amount_barcodes_noscan + $amount_bug)) {
                    $amount_barcodes += 1;
                    $dif_amount_barcodes = $amount - $amount_barcodes - $amount_barcodes_noscan - $amount_bug;
                    $db->query("UPDATE `J_MOVING_SELECT_STR` SET `amount_barcodes`='$amount_barcodes' WHERE `id`='$id' LIMIT 1;");
                    $answer = 1; $err = "";
                }
            }
        } else {
            $answer = 0; $err = "Помилка штрих-коду";
        }
        return array($answer, $err, $id, $amount_barcodes, $dif_amount_barcodes);
    }

    function finishJmovingStorageSelectBarcodeForm($jmoving_id, $select_id) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка індексу!!";
        $jmoving_id=$slave->qq($jmoving_id); $select_id=$slave->qq($select_id);
        $id = 0;
        if ($jmoving_id > 0 && $select_id > 0) {
            $r = $db->query("SELECT * FROM `J_MOVING_SELECT_STR` WHERE `jmoving_id` = $jmoving_id AND `select_id` = $select_id AND `amount`>(amount_barcodes+amount_barcodes_noscan+amount_bug);");
            $n = $db->num_rows($r);
            if ($n > 0) {
                $answer = 0; $err = "Не завершено перевірку по штрих-кодам";
            }
            if ($n == 0) {
                $r = $db->query("SELECT (SUM(amount_barcodes)+SUM(amount_barcodes_noscan)) as new_amount 
                FROM `J_MOVING_SELECT_STR` WHERE `jmoving_id` = $jmoving_id AND `select_id` = $select_id;");
                $n = $db->num_rows($r);
                if ($n == 1) {
                    $new_amount = $db->result($r,0,"new_amount");
                    $db->query("UPDATE `J_MOVING_SELECT` SET `status_jmoving`='47', `amount`='$new_amount' WHERE `jmoving_id` = $jmoving_id AND `id`='$select_id' LIMIT 1;");
                    $r1 = $db->query("SELECT `art_id`, SUM(amount_barcodes+amount_barcodes_noscan) as amount_js 
                    FROM `J_MOVING_SELECT_STR` WHERE `jmoving_id` = $jmoving_id AND `select_id` = $select_id;");
                    $n1 = $db->num_rows($r1);
                    for ($i = 1; $i <= $n1; $i++) {
                        $art_id = $db->result($r1,$i-1,"art_id");
                        $amount_js = $db->result($r1,$i-1,"amount_js");
                        $db->query("UPDATE `J_MOVING_STR` SET `amount`='$amount_js' WHERE `jmoving_id` = $jmoving_id AND `select_id` = $select_id AND `art_id`='$art_id' LIMIT 1;");
                    }
                }
                $this->addJuornalRecord($jmoving_id, $select_id, 47);
                $answer = 1; $err = "";
            }
        } else {
            $answer = 0; $err = "Помилка штрих-коду";
        }
        return array($answer, $err, $id, 47);
    }

    function finishJmovingLocalStorageSelect($jmoving_id, $select_id) { $db = DbSingleton::getDb();
        $slave = new slave;
        $jmoving_id = $slave->qq($jmoving_id); $select_id = $slave->qq($select_id);
        $id = 0; $new_amount = 0;
        if ($jmoving_id > 0 && $select_id > 0) {
            $db->query("UPDATE `J_MOVING_SELECT` SET `status_jmoving`='47', `amount`='$new_amount' WHERE `jmoving_id` = $jmoving_id AND `id`='$select_id' LIMIT 1;");
            $this->addJuornalRecord($jmoving_id, $select_id, 47);
            $answer = 1; $err = "";
        } else {
            $answer = 0; $err = "Помилка документу";
        }
        return array($answer, $err, $id, 47);
    }

    function checkJmovingAmountToTruck($jmoving_id) { $db = DbSingleton::getDb();
        // Передано в роботу
        $r = $db->query("SELECT SUM(`amount`) as sum_amount FROM `J_MOVING_STR` WHERE `jmoving_id` = $jmoving_id AND `status_jmoving`='45';");
        return $db->result($r, 0, "sum_amount");
    }

    function setJmovingSendTruck($jmoving_id) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка індексу!!";
        $jmoving_id = $slave->qq($jmoving_id);
        if ($jmoving_id > 0) {
            $jmoving_amount = $this->checkJmovingAmountToTruck($jmoving_id);
            if ($jmoving_amount > 0) {
                $prefix = $this->get_jmoving_prefix($jmoving_id);
                $r = $db->query("SELECT MAX(`doc_nom`) as doc_nom FROM `J_MOVING` WHERE `oper_status`='31' AND `status`='1' AND `type_id`='1' LIMIT 1;");
                $doc_nom = 0 + $db->result($r,0,"doc_nom") + 1;
                $db->query("UPDATE `J_MOVING` SET `status_jmoving`='48', `prefix`='$prefix', `doc_nom`='$doc_nom', `oper_status`='31' WHERE `id` = $jmoving_id AND `status`='1';");
                // В дорозі
                $this->addJuornalRecord($jmoving_id, 0, 48);
                $r3 = $db->query("SELECT `select_id` FROM `J_MOVING_STR` WHERE `jmoving_id` = $jmoving_id;");
                $n3 = $db->num_rows($r3);
                $select_str = "0";
                for ($i3 = 1; $i3 <= $n3; $i3++) {
                    $select_str .= "," . $db->result($r3, $i3 - 1, "select_id");
                }
                $db->query("UPDATE `J_SELECT` SET `status_select`='127' WHERE `id` IN ($select_str);");
                // Передано в накладну
                $answer = 1; $err = "";
            }
            if ($jmoving_amount <= 0) {
                $answer = 0; $err = "У переміщенні немає товару.\n Переміщення без товару не відправляється";
            }
        }
        $id = $amount_barcodes = $dif_amount_barcodes = 0;
        return array($answer, $err, $id, $amount_barcodes, $dif_amount_barcodes);
    }

    function printJmovingTruckList($jmoving_id) { $db = DbSingleton::getDb();
        $slave = new slave; $storsel = new storsel; $dp = new dp;
        $storsel_prefix = $storsel->storsel_prefix;
        session_start();
        $user_name = $_SESSION["user_name"];
        $storsels = []; $list = "";
        $storage_id_from = $select_id = 0;
        $storage_name_from = $tpoint_name = $tpoint_address = "";
        $form = ""; $form_htm = RD . "/tpl/jmoving_truck_print.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $r = $db->query("SELECT * FROM `J_MOVING_STR` WHERE `jmoving_id` = $jmoving_id ORDER BY `id` ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $art_id=$db->result($r,$i-1,"art_id");
            $select_id=$db->result($r,$i-1,"select_id");
            array_push($storsels, "$storsel_prefix-$select_id");
            $article_nr_displ=$db->result($r,$i-1,"article_nr_displ");
            $article_name=$this->getArticleName($art_id);
            $brand_id=$db->result($r,$i-1,"brand_id");
            $brand_name=$this->getBrandName($brand_id);
            $amount=$db->result($r,$i-1,"amount");
            $storage_id_from=$db->result($r,$i-1,"storage_id_from");
            $storage_name_from=$this->getStorageName($storage_id_from);
            if ($amount > 0) {
                $list .= "<tr>
                    <td align='center'>$i</td>
                    <td align='center'>$storage_name_from</td>
                    <td align='center'>$article_nr_displ</td>
                    <td align='center'>$brand_name</td>
                    <td align='center'>$amount</td>
                    <td align='left'>$article_name</td>
                    <td>&nbsp;</td>
                </tr>";
            }
        }
        $form = str_replace("{ArticlesList}",$list,$form);
        $form = str_replace("{jmoving_id}",$jmoving_id,$form);

        list($prefix, $doc_nom, $data, $storage_id_to, $storage_name_to,, $parrent_type_id, $parrent_doc_id) = $this->getJmovingInfo($jmoving_id);
        $dp_info = "Без замовлення";

        if ($parrent_type_id == 1) {
            list($dp_prefix, $dp_doc_nom, $dp_data,,,) = $dp->getdpInfo($parrent_doc_id);
            $dp_info = "$dp_prefix-$dp_doc_nom/$dp_data";
            $tpoint_id = $dp->getDpTpoint($parrent_doc_id);
            $tpoint_name = $dp->getTpointName($tpoint_id);
            $tpoint_address = $dp->getTpointAddress($tpoint_id);
        }
        $tp_to = $this->getTpointName($storage_id_to);
        $tp_from = $this->getTpointName($storage_id_from);
        $form = str_replace("{storage_to}",$storage_name_to,$form);
        $form = str_replace("{storage_from}",$storage_name_from,$form);
        $form = str_replace("{tp_to}",$tp_to,$form);
        $form = str_replace("{tp_from}",$tp_from,$form);
        $form = str_replace("{jmoving_prefix_doc_nom}",$prefix."-".$doc_nom,$form);
        $form = str_replace("{jmoving_data}",$slave->data_word($data),$form);
        $storsels = array_unique($storsels);
        $storsel_list = implode(",", $storsels);
        $form = str_replace("{tpoint_name}",$tpoint_name,$form);
        $form = str_replace("{tpoint_address}",$tpoint_address,$form);$jmoving_comment="";
        $form = str_replace("{jmoving_comment}",$jmoving_comment,$form);
        $form = str_replace("{user_name}",$user_name,$form);
        $form = str_replace("{curtime}",date("Y-m-d H:i:s"),$form);
        $form = str_replace("{storsel_list}",$storsel_list,$form);
        $form = str_replace("{dp_info}",$dp_info,$form);
        $form = str_replace("{pData}",$slave->data_word(""),$form);
        $form = str_replace("{qrImage}","<img src='/phpqrcode/qrimage2.php?url=http://portal.myparts.pro/Jmoving/printJmSTP/$jmoving_id/$select_id/".time()."'>",$form);
        $mp = new media_print;
        $mp->print_document($form, array(210, 280));
        return $form;
    }

    function printJmovingStorageSelectTruckList($jmoving_id, $select_id) { $db = DbSingleton::getDb();
        $slave = new slave;
        session_start();
        $user_name = $_SESSION["user_name"];
        $list = "";
        $form = ""; $form_htm = RD . "/tpl/jmoving_storage_select_truck_print.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $r = $db->query("SELECT * FROM `J_MOVING_SELECT_STR` WHERE `jmoving_id` = $jmoving_id AND `select_id` = $select_id ORDER BY `id` ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $art_id = $db->result($r,$i-1,"art_id");
            $article_nr_displ = $db->result($r,$i-1,"article_nr_displ");
            $article_name = $this->getArticleName($art_id);
            $brand_id = $db->result($r,$i-1,"brand_id");
            $brand_name = $this->getBrandName($brand_id);
            $amount = $db->result($r,$i-1,"amount");
            $cell_id_from = $db->result($r,$i-1,"cell_id_from");
            $cell_name_from = $this->getStorageCellName($cell_id_from);
            $list .= "<tr>
                <td align='center'>$i</td>
                <td align='center'>$cell_name_from</td>
                <td align='center'>$article_nr_displ</td>
                <td align='center'>$brand_name</td>
                <td align='center'>$amount</td>
                <td align='left'>$article_name</td>
                <td>&nbsp;</td>
            </tr>";
        }
        $form = str_replace("{ArticlesList}",$list,$form);
        $form = str_replace("{select_id}",$select_id,$form);
        $form = str_replace("{jmoving_id}",$jmoving_id,$form);
        list($select_nom, $select_data,,, $storage_name_to, $articles_amount, $amount, $volume, $weight_netto, $weight_brutto, $jmoving_comment,) = $this->getJmovingSkladStorageSelectInfo($jmoving_id, $select_id);
        $form = str_replace("{select_nom}",$select_nom,$form);
        $form = str_replace("{select_data}",$select_data,$form);
        $form = str_replace("{storage_to}",$storage_name_to,$form);
        $form = str_replace("{articles_amount}",$articles_amount,$form);
        $form = str_replace("{amount}",$amount,$form);
        $form = str_replace("{volume}",$volume,$form);
        $form = str_replace("{weight_netto}",$weight_netto,$form);
        $form = str_replace("{weight_brutto}",$weight_brutto,$form);
        $form = str_replace("{jmoving_comment}",$jmoving_comment,$form);
        $form = str_replace("{user_name}",$user_name,$form);
        $form = str_replace("{curtime}",date("Y-m-d H:i:s"),$form);$pData="";
        $form = str_replace("{pData}",$slave->data_word($pData),$form);
        $form = str_replace("{qrImage}","<img src='/phpqrcode/qrimage2.php?url=http://portal.myparts.pro/Jmoving/printJmSTP/$jmoving_id/$select_id/".time()."'>",$form);
        $mp = new media_print;
        $mp->print_document($form, array(210, 280));
        return $form;
    }

    function showJmovingStorageSelectBugForm($jmoving_id, $select_id, $str_id) { $db = DbSingleton::getDb();
        $manual = new manual;
        $answer = 0; $err = "Помилка індексу";
        $form = ""; $form_htm = RD . "/tpl/jmoving_storage_select_bug_form.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $r = $db->query("SELECT * FROM `J_MOVING_SELECT_STR` WHERE `jmoving_id` = $jmoving_id AND `select_id` = $select_id AND `id`='$str_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $art_id = $db->result($r,0,"art_id");
            $article_nr_displ = $db->result($r,0,"article_nr_displ");
            $article_name = $this->getArticleName($art_id);
            $brand_id = $db->result($r,0,"brand_id");
            $brand_name = $this->getBrandName($brand_id);
            $amount = $db->result($r,0,"amount");
            $amount_barcodes = $db->result($r,0,"amount_barcodes");
            $dif_amount_barcodes = $amount - $amount_barcodes;
            $form = str_replace("{select_id}",$select_id,$form);
            $form = str_replace("{jmoving_id}",$jmoving_id,$form);
            $form = str_replace("{str_id}",$str_id,$form);
            $form = str_replace("{article_name}",$article_name,$form);
            $form = str_replace("{brand_name}",$brand_name,$form);
            $form = str_replace("{article_nr_displ}",$article_nr_displ,$form);
            $form = str_replace("{dif_amount_barcode}",$dif_amount_barcodes,$form);
            $form = str_replace("{amount}",$amount,$form);
            $form = str_replace("{bugs_list}",$manual->showManualSelectList("storage_select_bug", ""),$form);
            $answer = 1; $err = "";
        }
        return array($answer, $err, $form, "");
    }

    function saveJmovingStorageSelectBugForm($jmoving_id, $select_id, $str_id, $storage_select_bug, $amount_bug) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка індексу!!";
        $storage_select_bug_list = "";
        $id = $dif_amount_barcodes = $new_amount_bug = $amount_barcodes = $amount_barcodes_noscan = 0;
        $jmoving_id=$slave->qq($jmoving_id);$select_id=$slave->qq($select_id);$str_id=$slave->qq($str_id);$storage_select_bug=$slave->qq($storage_select_bug);$amount_bug=$slave->qq($amount_bug);
        if ($jmoving_id > 0 && $select_id > 0 && $str_id > 0 && $storage_select_bug > 0 && $amount_bug > 0) {
            $r = $db->query("SELECT * FROM `J_MOVING_SELECT_STR` WHERE `jmoving_id` = $jmoving_id AND `select_id` = $select_id AND `id`='$str_id' LIMIT 1;");
            $n = $db->num_rows($r);
            if ($n == 1) {
                $id = $db->result($r,0,"id");
                $art_id = $db->result($r,0,"art_id");
                $article_nr_displ = $db->result($r,0,"article_nr_displ");
                $amount = $db->result($r,0,"amount");
                $amount_barcodes = $db->result($r,0,"amount_barcodes");
                $amount_barcodes_noscan = $db->result($r,0,"amount_barcodes_noscan");
                $amount_bug_ex = $db->result($r,0,"amount_bug");
                $storage_id_from = $db->result($r,0,"storage_id_from");
                $cell_id_from = $db->result($r,0,"cell_id_from");
                $cell_use = 0;
                if ($cell_id_from > 0) {
                    $cell_use = 1;
                }
                $ex_dif_amount = $amount - $amount_bug;

                if ($ex_dif_amount < $amount_bug_ex) {
                    $answer = 0; $err = "Кількість відхилення не відповідає обліковій кількості";
                }
                if ($ex_dif_amount >= $amount_bug_ex) {
                    $new_amount_bug = $amount_bug + $amount_bug_ex;
                    $noscan_am = $amount - $amount_bug_ex - $amount_barcodes - $amount_barcodes_noscan - $amount_bug;

                    if ($noscan_am < 0) {
                        $amount_barcodes_noscan = $amount_barcodes_noscan + $noscan_am;
                    }
                    if ($amount_barcodes_noscan < 0) {
                        $amount_barcodes = $amount_barcodes + $amount_barcodes_noscan;
                        $amount_barcodes_noscan = 0;
                    }
                    if ($amount_barcodes < 0) {
                        $amount_barcodes = 0;
                    }

                    $db->query("UPDATE `J_MOVING_SELECT_STR` SET `amount_bug`='$new_amount_bug', `amount_barcodes`='$amount_barcodes', `amount_barcodes_noscan`='$amount_barcodes_noscan' WHERE `id`='$id' LIMIT 1;");
                    $dif_amount_barcodes = $amount - $amount_barcodes - $amount_barcodes_noscan - $new_amount_bug;
                    if ($dif_amount_barcodes < 0) {
                        $dif_amount_barcodes = 0;
                    }

                    $db->query("INSERT INTO `J_MOVING_SELECT_STR_BUG` (`jmoving_id`,`select_id`,`art_id`,`str_id`,`article_nr_displ`,`storage_select_bug`,`amount_bug`) 
                    VALUES ('$jmoving_id','$select_id','$art_id','$str_id','$article_nr_displ','$storage_select_bug','$amount_bug');");

                    // ОБНОВИТЬ РЕЗЕРВЫ ПОСЛЕ ФИКСАЦИИ ОТКЛОНЕНИЯ
                    $this->updateStockStorageBug($art_id, $storage_id_from, $cell_id_from, $cell_use, $amount_bug);

                    $storage_select_bug_list = $this->getStorageSelectBugList($jmoving_id, $select_id, $art_id, $id);
                    $answer = 1; $err = "";
                }
            }
        } else {
            $answer = 0; $err = "Помилка штрих-коду";
        }
        return array($answer, $err, $id, $storage_select_bug_list, $dif_amount_barcodes, $new_amount_bug, $amount_barcodes, $amount_barcodes_noscan);
    }

    function getStorageSelectBugList($jmoving_id, $select_id, $art_id, $str_id) { $db = DbSingleton::getDb();
        $manual = new manual;
        $list = "";
        $r = $db->query("SELECT * FROM `J_MOVING_SELECT_STR_BUG` WHERE `jmoving_id` = $jmoving_id AND `select_id` = $select_id AND `art_id`='$art_id' AND `str_id`='$str_id' ORDER BY `id` ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $storage_select_bug = $db->result($r,$i-1,"storage_select_bug");
            $amount_bug = $db->result($r,$i-1,"amount_bug");
            $storage_select_bug_name = $manual->getManualMCaption("storage_select_bug", $storage_select_bug);
            $list .= "$amount_bug"."шт. - $storage_select_bug_name";
            if ($i < $n) {
                $list .= "<br>";
            }
        }
        return $list;
    }

    function getJmovingBugList($jmoving_id, $art_id) { $db = DbSingleton::getDb();
        $manual = new manual;
        $list = "";
        $r = $db->query("SELECT * FROM `J_MOVING_STR_BUG` WHERE `jmoving_id` = $jmoving_id AND `art_id`='$art_id' ORDER BY `id` ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $storage_select_bug = $db->result($r,$i-1,"storage_select_bug");
            $amount_bug = $db->result($r,$i-1,"amount_bug");
            $storage_select_bug_name = $manual->getManualMCaption("storage_select_bug", $storage_select_bug);
            $list .= "$amount_bug"."шт. - $storage_select_bug_name";
            if ($i < $n) {
                $list .= "<br>";
            }
        }
        return $list;
    }

    function getJmovingBugListTrue($jmoving_str) { $db = DbSingleton::getDb();
        $manual = new manual;
        $list = "";
        $r = $db->query("SELECT * FROM `J_MOVING_STR_BUG` WHERE `str_id`='$jmoving_str' ORDER BY `id` ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $storage_select_bug = $db->result($r,$i-1,"storage_select_bug");
            $amount_bug = $db->result($r,$i-1,"amount_bug");
            $storage_select_bug_name = $manual->getManualMCaption("storage_select_bug", $storage_select_bug);
            $list .= "$amount_bug" . "шт. - $storage_select_bug_name";
            if ($i < $n) {
                $list .= "<br>";
            }
        }
        return $list;
    }

    function addStatusJmoving($jmoving_id, $status) { $db = DbSingleton::getDb();
        $user_id = $_SESSION["media_user_id"];
        $data = date("Y-m-d H:i:sa");
        if ($jmoving_id > 0) {
            $r = $db->query("SELECT * FROM `J_MOVING` WHERE `id` = $jmoving_id LIMIT 1;");
            $user_accepting = $db->result($r,0,"user_accepting");
            $user_accepted = $db->result($r,0,"user_accepted");
            if ($status == 1 && $user_accepting == 0) {
                // приймається
                $db->query("UPDATE `J_MOVING` SET `user_accepting`='$user_id', `user_data_accepting`='$data' WHERE `id` = $jmoving_id;");
            }
            if ($status == 2 && $user_accepted == 0) {
                // прийнято
                $db->query("UPDATE `J_MOVING` SET `user_accepted`='$user_id', `user_data_accepted`='$data' WHERE `id` = $jmoving_id;");
            }
        }
        return true;
    }

    /*
     * Прийняти переміщення
     * */
    function showJmovingStorageAcceptForm($jmoving_id) { $db = DbSingleton::getDb();
        session_start();
        $user_name = $_SESSION["user_name"];
        $form = ""; $form_htm = RD . "/tpl/jmoving_accept_barcode_list.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $this->setJmovingStorageAcceptStart($jmoving_id);
        $list = "";
        $r = $db->query("SELECT * FROM `J_MOVING_STR` WHERE `jmoving_id` = $jmoving_id ORDER BY `id` ASC;");
        $n = $db->num_rows($r);
        $this->addStatusJmoving($jmoving_id, 1);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r,$i-1,"id");
            $art_id = $db->result($r,$i-1,"art_id");
            $article_nr_displ = $db->result($r,$i-1,"article_nr_displ");
            $article_name = $this->getArticleName($art_id);
            $brand_id = $db->result($r,$i-1,"brand_id");
            $brand_name = $this->getBrandName($brand_id);
            $amount = $db->result($r,$i-1,"amount");
            $amount_barcodes = $db->result($r,$i-1,"amount_barcodes");
            $amount_barcodes_noscan = $db->result($r,$i-1,"amount_barcodes_noscan");
            $amount_bug = $db->result($r,$i-1,"amount_bug");
            $dif_amount_barcodes = $amount - $amount_barcodes - $amount_barcodes_noscan;
            $bug_list = $this->getJmovingBugListTrue($id);
            $list .= "<tr>
                <td align='center'>$i</td>
                <td align='center' id='amrai_$id'>$article_nr_displ</td>
                <td align='center' id='amrab_$id'>$brand_name</td>
                <td align='left' id='amran_$id'>$article_name</td>
                <td align='center'>$amount</td>
                <td align='center' id='amr_$id'>$amount_barcodes</td>
                <td align='center' id='amrd_$id'>$dif_amount_barcodes</td>
                <td align='center' id='amrns_$id'>$amount_barcodes_noscan</td>
                <td id='amrd_$id' align='center'><button class='btn btn-xs btn-default' onclick='showJmovingAcceptNoscanForm(\"$jmoving_id\",\"$art_id\",\"$id\");' title='Фіксація без сканування'><i class='fa fa-cube'></i></button></td>
                <td id='amrd_$id' align='center'><button class='btn btn-xs btn-danger' onclick='showJmovingAcceptBugForm(\"$jmoving_id\",\"$id\");' title='відхилення/брак/недостача'><i class='fa fa-bug'></i></button></td>
                <td align='center' id='ambg_$id'>$amount_bug</td>
                <td id='ssbug_$id'>$bug_list</td>
            </tr>";
        }
        $form = str_replace("{ArticlesList}",$list,$form);
        $form = str_replace("{jmoving_id}",$jmoving_id,$form);
        $form = str_replace("{jmoving_nom}","",$form);
        $form = str_replace("{jmoving_data}","",$form);
        $form = str_replace("{jmoving_comment}","",$form);
        $form = str_replace("{user_name}",$user_name,$form);
        $form = str_replace("{curtime}",date("Y-m-d H:i:s"),$form);
        $answer = 1; $err = "";
        return array($answer, $err, $form, "Отримання товару по штрих-кодам");
    }

    function scanJmovingAcceptForm($jmoving_id) { $db = DbSingleton::getDb();
        $answer = 0; $err = "Помилка індексу!";
        if ($jmoving_id > 0) {
            $db->query("UPDATE `J_MOVING_STR` SET `amount_barcodes_noscan`=`amount`, `amount_barcodes`=0 WHERE `jmoving_id` = $jmoving_id;");
            $answer = 1; $err = "";
        }
        return array($answer,$err);
    }

    function setJmovingStorageAcceptStart($jmoving_id) { $db = DbSingleton::getDb();
        $slave = new slave;
        $jmoving_id = $slave->qq($jmoving_id);
        if ($jmoving_id > 0) {
            $r = $db->query("SELECT `status_jmoving` FROM `J_MOVING` WHERE `id` = $jmoving_id LIMIT 1;");
            $n = $db->num_rows($r);
            if ($n == 1) {
                $status_jmoving = $db->result($r, 0, "status_jmoving");
                if ($status_jmoving == 48) {
                    $db->query("UPDATE `J_MOVING` SET `status_jmoving`='49' WHERE `id` = $jmoving_id LIMIT 1;");
                }
            }
        }
        return true;
    }

    function saveJmovingAcceptBarcodeForm($jmoving_id, $barcode) { $db = DbSingleton::getDb();
        $slave = new slave; $storsel = new storsel();
        $barcode_info = ($barcode == "") ? "" : $storsel->getBarcodeInfo($barcode);
        $answer = 0; $err = "Помилка індексу! Штрих-коду '$barcode' ($barcode_info) немає у відборі";
        $jmoving_id=$slave->qq($jmoving_id); $barcode=$slave->qq($barcode);
        $id = $amount_barcodes = $dif_amount_barcodes = 0;
        if ($jmoving_id > 0 && $barcode != "") {
            $art_id = $this->getArtIdByBarcode($barcode);
            $r = $db->query("SELECT * FROM `J_MOVING_STR` WHERE `jmoving_id` = $jmoving_id AND `art_id`='$art_id' AND `amount`>`amount_barcodes` LIMIT 1;");
            $n = $db->num_rows($r);
            if ($n == 1) {
                $id=$db->result($r,0,"id");
                $amount=$db->result($r,0,"amount");
                $amount_barcodes=$db->result($r,0,"amount_barcodes");
                $amount_barcodes_noscan=$db->result($r,0,"amount_barcodes_noscan");
                $ex = $amount_barcodes + $amount_barcodes_noscan;
                if ($amount > $ex) {
                    $amount_barcodes += 1;
                    $dif_amount_barcodes = $amount - $amount_barcodes - $amount_barcodes_noscan;
                    $db->query("UPDATE `J_MOVING_STR` SET `amount_barcodes`='$amount_barcodes' WHERE `id`='$id' LIMIT 1;");
                    $answer = 1; $err = "";
                }
            }
        } else {
            $answer = 0; $err = "Помилка штрих-коду";
        }
        return array($answer, $err, $id, $amount_barcodes, $dif_amount_barcodes);
    }

    function showJmovingStorageSelectNoscanForm($jmoving_id, $select_id, $str_id) { $db = DbSingleton::getDb();
        $answer = 0; $err = "Помилка індексу";
        $form = ""; $form_htm = RD . "/tpl/jmoving_storage_select_noscan_form.htm";
        if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r = $db->query("SELECT * FROM `J_MOVING_SELECT_STR` WHERE `jmoving_id` = $jmoving_id AND `select_id` = $select_id AND `id`='$str_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $art_id = $db->result($r,0,"art_id");
            $article_nr_displ = $db->result($r,0,"article_nr_displ");
            $article_name = $this->getArticleName($art_id);
            $brand_id = $db->result($r,0,"brand_id");
            $brand_name = $this->getBrandName($brand_id);
            $amount = $db->result($r,0,"amount");
            $amount_barcodes = $db->result($r,0,"amount_barcodes");
            $amount_barcodes_noscan = $db->result($r,0,"amount_barcodes_noscan");
            $dif_amount_barcodes = $amount - $amount_barcodes - $amount_barcodes_noscan;
            $form = str_replace("{jmoving_id}",$jmoving_id,$form);
            $form = str_replace("{select_id}",$select_id,$form);
            $form = str_replace("{str_id}",$str_id,$form);
            $form = str_replace("{art_id}",$art_id,$form);
            $form = str_replace("{article_name}",$article_name,$form);
            $form = str_replace("{brand_name}",$brand_name,$form);
            $form = str_replace("{article_nr_displ}",$article_nr_displ,$form);
            $form = str_replace("{dif_amount_barcode}",$dif_amount_barcodes,$form);
            $form = str_replace("{amount_barcodes_noscan}",$amount_barcodes_noscan,$form);
            $answer = 1; $err = "";
        }
        return array($answer, $err, $form, "");
    }

    function saveJmovingStorageSelectNoscanForm($jmoving_id, $select_id, $art_id, $str_id, $amount_barcode_noscan) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка індексу!!";
        $jmoving_id=$slave->qq($jmoving_id);$select_id=$slave->qq($select_id);$art_id=$slave->qq($art_id);$str_id=$slave->qq($str_id);$amount_barcode_noscan=$slave->qq($amount_barcode_noscan);
        $id = $dif_amount_barcodes = $new_amount_barcode_noscan = 0;
        if ($jmoving_id > 0 && $select_id > 0 && $art_id > 0 && $str_id > 0 && $amount_barcode_noscan > 0) {
            $r = $db->query("SELECT * FROM `J_MOVING_SELECT_STR` WHERE `jmoving_id` = $jmoving_id AND `art_id`='$art_id' AND `id`='$str_id' LIMIT 1;");
            $n = $db->num_rows($r);
            if ($n == 1) {
                $id = $db->result($r,0,"id");
                $amount = $db->result($r,0,"amount");
                $amountBarcodes = $db->result($r,0,"amount_barcodes");
                $amountBarcodesNoscan = $db->result($r,0,"amount_barcodes_noscan");
                $amountBug = $db->result($r,0,"amount_bug");
                $ex_dif_amount = ($amount - $amountBarcodes - $amountBarcodesNoscan - $amountBug);
                if ($ex_dif_amount < $amount_barcode_noscan) {
                    $answer = 0; $err = "Кількість не відповідає обліковій кількості $ex_dif_amount";
                }
                if ($ex_dif_amount >= $amount_barcode_noscan) {
                    $new_amount_barcode_noscan = $amount_barcode_noscan + $amountBarcodesNoscan;
                    $dif_amount_barcodes = $amount - $amountBarcodes - $new_amount_barcode_noscan - $amountBug;
                    $db->query("UPDATE `J_MOVING_SELECT_STR` SET `amount_barcodes_noscan`='$new_amount_barcode_noscan' WHERE `id`='$id' LIMIT 1;");
                    $answer = 1; $err = "";
                }
            }
        } else {
            $answer = 0; $err = "Помилка штрих-коду";
        }
        return array($answer, $err, $id, $dif_amount_barcodes, $new_amount_barcode_noscan);
    }

    function showJmovingAcceptNoscanForm($jmoving_id, $str_id) { $db = DbSingleton::getDb();
        $answer = 0; $err = "Помилка індексу";
        $form = ""; $form_htm = RD . "/tpl/jmoving_accept_noscan_form.htm";
        if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r = $db->query("SELECT * FROM `J_MOVING_STR` WHERE `jmoving_id` = $jmoving_id AND `id`='$str_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $art_id=$db->result($r,0,"art_id");
            $article_nr_displ=$db->result($r,0,"article_nr_displ"); $article_name=$this->getArticleName($art_id);
            $brand_id=$db->result($r,0,"brand_id");$brand_name=$this->getBrandName($brand_id);
            $amount=$db->result($r,0,"amount");
            $amount_barcodes=$db->result($r,0,"amount_barcodes");
            $amount_barcodes_noscan=$db->result($r,0,"amount_barcodes_noscan");
            $dif_amount_barcodes=$amount-$amount_barcodes-$amount_barcodes_noscan;
            $form = str_replace("{jmoving_id}",$jmoving_id,$form);
            $form = str_replace("{str_id}",$str_id,$form);
            $form = str_replace("{art_id}",$art_id,$form);
            $form = str_replace("{article_name}",$article_name,$form);
            $form = str_replace("{brand_name}",$brand_name,$form);
            $form = str_replace("{article_nr_displ}",$article_nr_displ,$form);
            $form = str_replace("{dif_amount_barcode}",$dif_amount_barcodes,$form);
            $form = str_replace("{amount_barcodes_noscan}",$amount_barcodes_noscan,$form);
            $answer = 1; $err = "";
        }
        return array($answer, $err, $form, "");
    }

    function saveJmovingAcceptNoscanForm($jmoving_id, $art_id, $str_id, $amount_barcode_noscan) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка індексу!!";
        $jmoving_id=$slave->qq($jmoving_id); $art_id=$slave->qq($art_id); $str_id=$slave->qq($str_id); $amount_barcode_noscan=$slave->qq($amount_barcode_noscan);
        $id = $dif_amount_barcodes = $new_amount_barcode_noscan = 0;
        if ($jmoving_id > 0 && $art_id > 0 && $str_id > 0 && $amount_barcode_noscan > 0) {
            $r = $db->query("SELECT * FROM `J_MOVING_STR` WHERE `jmoving_id` = $jmoving_id AND `art_id`='$art_id' AND `id`='$str_id' LIMIT 1;");
            $n = $db->num_rows($r);
            if ($n == 1) {
                $id = $db->result($r,0,"id");
                $amount = $db->result($r,0,"amount");
                $amountBarcodes = $db->result($r,0,"amount_barcodes");
                $amountBarcodesNoscan = $db->result($r,0,"amount_barcodes_noscan");
                $ex_dif_amount = $amount - $amountBarcodes - $amountBarcodesNoscan;
                if ($ex_dif_amount < $amount_barcode_noscan) {
                    $answer = 0; $err = "Кількість не відповідає обліковій кількості $ex_dif_amount";
                }
                if ($ex_dif_amount >= $amount_barcode_noscan) {
                    $new_amount_barcode_noscan = $amountBarcodesNoscan + $amount_barcode_noscan;
                    $dif_amount_barcodes = $amount - $amountBarcodes - $new_amount_barcode_noscan;
                    $db->query("UPDATE `J_MOVING_STR` SET `amount_barcodes_noscan`='$new_amount_barcode_noscan' WHERE `id`='$id' LIMIT 1;");
                    $answer = 1; $err = "";
                }
            }
        } else {
            $answer = 0; $err = "Помилка штрих-коду";
        }
        return array($answer, $err, $id, $dif_amount_barcodes, $new_amount_barcode_noscan);
    }

    function showJmovingAcceptBugForm($jmoving_id, $str_id) { $db = DbSingleton::getDb();
        $manual = new manual;
        $answer = 0; $err = "Помилка індексу";
        $form = ""; $form_htm = RD . "/tpl/jmoving_accept_bug_form.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $r = $db->query("SELECT * FROM `J_MOVING_STR` WHERE `jmoving_id` = $jmoving_id AND `id`='$str_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $art_id = $db->result($r,0,"art_id");
            $article_nr_displ = $db->result($r,0,"article_nr_displ");
            $article_name = $this->getArticleName($art_id);
            $brand_id = $db->result($r,0,"brand_id");
            $brand_name = $this->getBrandName($brand_id);
            $amount = $db->result($r,0,"amount");
            $amount_barcodes = $db->result($r,0,"amount_barcodes");
            $dif_amount_barcodes = $amount - $amount_barcodes;
            $form = str_replace("{jmoving_id}",$jmoving_id,$form);
            $form = str_replace("{str_id}",$str_id,$form);
            $form = str_replace("{article_name}",$article_name,$form);
            $form = str_replace("{brand_name}",$brand_name,$form);
            $form = str_replace("{article_nr_displ}",$article_nr_displ,$form);
            $form = str_replace("{dif_amount_barcode}",$dif_amount_barcodes,$form);
            $form = str_replace("{amount}",$amount,$form);
            $form = str_replace("{bugs_list}",$manual->showManualSelectList("storage_select_bug", ""),$form);
            $answer = 1; $err = "";
        }
        return array($answer, $err, $form, "");
    }

    function checkJmovingBugs($jmoving_id) { $db = DbSingleton::getDb();
        $kol = 0;
        $r = $db->query("SELECT * FROM `J_MOVING_STR` WHERE `jmoving_id` = $jmoving_id;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $amount_bug = $db->result($r, $i - 1, "amount_bug");
            $kol += intval($amount_bug);
        }
        return ($kol > 0);
    }

    function saveJmovingAcceptBugForm($jmoving_id, $str_id, $storage_select_bug, $amount_bug) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка індексу!!";
        $jmoving_id=$slave->qq($jmoving_id); $str_id=$slave->qq($str_id); $storage_select_bug=$slave->qq($storage_select_bug); $amount_bug=$slave->qq($amount_bug);
        $id = $dif_amount_barcodes = $new_amount_bug = 0; $bug_list = "";
        if ($jmoving_id > 0 && $str_id > 0 && $storage_select_bug > 0 && $amount_bug > 0) {
            $r = $db->query("SELECT * FROM `J_MOVING_STR` WHERE `jmoving_id` = $jmoving_id AND `id`='$str_id' LIMIT 1;");
            $n = $db->num_rows($r);
            if ($n == 1) {
                $id = $db->result($r,0,"id");
                $amount = $db->result($r,0,"amount");
                $amount_barcodes = $db->result($r,0,"amount_barcodes");
                $amount_barcodes_noscan = $db->result($r,0,"amount_barcodes_noscan");
                $amount_bug_ex = $db->result($r,0,"amount_bug");
                $ex_dif_amount = $amount - $amount_barcodes;
                if ($ex_dif_amount != $amount_bug) {
                    $answer = 0; $err = "Кількість відхилення не відповідає обліковій кількості $ex_dif_amount";
                }
                if ($ex_dif_amount >= ($amount_bug + $amount_bug_ex)) {
                    $dif_amount_barcodes = $amount - $amount_barcodes - $amount_barcodes_noscan - $amount_bug - $amount_bug_ex;
                    $new_amount_bug = $amount_bug + $amount_bug_ex;
                    $db->query("UPDATE `J_MOVING_STR` SET `amount_bug`='$new_amount_bug' WHERE `id`='$id' LIMIT 1;");
                    $db->query("INSERT INTO `J_MOVING_STR_BUG` (`jmoving_id`,`art_id`,`str_id`,`article_nr_displ`,`storage_select_bug`,`amount_bug`) 
                    VALUES ('$jmoving_id','0','$id','','$storage_select_bug','$amount_bug');");
                    //$bug_list=$this->getJmovingBugList($jmoving_id,$art_id);
                    $bug_list = $this->getJmovingBugListTrue($id);
                    $answer = 1; $err = "";
                }
            }
        } else {
            $answer = 0; $err = "Помилка відхилення";
        }
        return array($answer, $err, $id, $bug_list, $dif_amount_barcodes, $new_amount_bug);
    }

    /*
     * Завершити сканування
     * */
    function finishJmovingAcceptForm($jmoving_id) { $db = DbSingleton::getDb(); $dbt = DbSingleton::getTokoDb();
        $slave = new slave; $dp = new dp;
        $answer = 0; $err = "Помилка індексу!!";
        $jmoving_id = $slave->qq($jmoving_id);
        $id = $storage_id_to = $cell_id_to = $cell_use_to = 0;
        $rr_amount = $rr_reserv = 0;
        $er_to = $er_from = 0;
        $parrent_doc_id = $parrent_type_id = 0;
        $select_id = $storage_id_from = 0;
        if ($jmoving_id > 0) {
            $rc = $db->query("SELECT * FROM `J_MOVING_STR` WHERE `jmoving_id` = $jmoving_id AND `amount`>(amount_barcodes+amount_barcodes_noscan);");
            $nc = $db->num_rows($rc);
            if ($nc > 0) {
                $answer = 0; $err = "Не завершено перевірку по штрих-кодам";
            }
            if ($nc == 0) {
                $this->addStatusJmoving($jmoving_id, 2);
                $r = $db->query("SELECT * FROM `J_MOVING` WHERE `id` = $jmoving_id LIMIT 1;");
                $n = $db->num_rows($r);
                if ($n == 1) {
                    $storage_id_to = $db->result($r,0,"storage_id_to");
                    $cell_use_to = $db->result($r,0,"cell_use");
                    $cell_id_to = $db->result($r,0,"cell_id_to");
                    if ($cell_id_to > 0) {
                        $cell_use_to = 1;
                    }
                    $parrent_type_id = $db->result($r,0,"parrent_type_id");
                    $parrent_doc_id = $db->result($r,0,"parrent_doc_id");
                }

                if ($storage_id_to > 0) {
                    $rs = $db->query("SELECT ss.* FROM `J_SELECT_STR` ss 
                        LEFT OUTER JOIN `J_SELECT` sel on sel.id=ss.select_id
                    WHERE sel.parrent_doc_type_id='1' AND sel.parrent_doc_id='$jmoving_id';");
                    $ns = $db->num_rows($rs);
                    for ($is = 1; $is <= $ns; $is++) {
                        $art_id = $db->result($rs,$is-1,"art_id");
                        $select_id = $db->result($rs,$is-1,"select_id");
                        $amount = $db->result($rs,$is-1,"amount_collect");
                        $storage_id_from = $db->result($rs,$is-1,"storage_id_from");
                        $cell_id_from = $db->result($rs,$is-1,"cell_id_from");
                        $cell_use_from = 0;
                        if ($cell_id_from > 0) {
                            $cell_use_from = 1;
                        }

                        $slave->addJuornalArtDocs(2, $jmoving_id, $art_id, $amount);

                        $er_from = $this->updateStockFromStorage($art_id, $storage_id_from, $cell_id_from, $cell_use_from, $amount);
                        if ($er_from == 0) {
                            $er_to = $this->updateStockToStorage($art_id, $storage_id_to, $cell_id_to, $cell_use_to, $amount);
                        }
                        if ($er_to == 1 || $er_from == 1) {
                            $is += 1;
                            $answer = 0; $err = "Помилка оновлення залишків на складі";
                        }
                    }
                }
                if ($er_to == 0 && $er_from == 0) {
                    $db->query("UPDATE `J_MOVING` SET `status_jmoving`='57', `data`=CURDATE() WHERE `id` = $jmoving_id LIMIT 1;");
                    $this->addJuornalRecord($jmoving_id, $select_id, 57);
                    $answer = 1; $err = "";
                    if ($parrent_doc_id > 0 && $parrent_type_id > 0) {
                        if ($parrent_type_id == 1) {
                            // JMOVING FOR DP, create local storsel
                            $dp_id = $parrent_doc_id;
                            $tpoint_id = $dp->getDpTpoint($dp_id);
                            $select_id = $dp->createStorsel($dp_id, $tpoint_id, $storage_id_to);
                            $s_volume = 0;
                            $s_weight_netto = 0;
                            $s_amount = 0;
                            $s_articles_amount = 0;

                            $r1 = $db->query("SELECT * FROM `J_MOVING_STR` WHERE `jmoving_id` = $jmoving_id;");
                            $n1 = $db->num_rows($r1);
                            for ($i1 = 1; $i1 <= $n1; $i1++) {
                                $id = $db->result($r1,$i1-1,"id");
                                $art_id = $db->result($r1,$i1-1,"art_id");
                                $article_nr_displ = $db->result($r1,$i1-1,"article_nr_displ");
                                $brand_id = $db->result($r1,$i1-1,"brand_id");
                                $amount = $db->result($r1,$i1-1,"amount");
                                $amount_bug = $db->result($r1,$i1-1,"amount_bug");
                                $parrent_doc_type_id = 2; // J_MOVING
                                // $parrent_doc_id=$id; // J_MOVING_STR ID
                                $amount_to_storsel = $amount - $amount_bug;
                                $s_amount += $amount_to_storsel;
                                $s_articles_amount += 1;
                                $rt2 = $db->query("SELECT MAX(`id`) as mid FROM `J_SELECT_STR`;");
                                $str_id = 0 + $db->result($rt2,0,"mid") + 1;

                                $db->query("UPDATE `J_DP_STR` SET `cur_select_str_id`='$str_id', `amount_collect`='$amount_to_storsel', `location_storage_id`='$storage_id_to', `status_dps`='94' 
                                WHERE `art_id`='$art_id' AND `dp_id`='$dp_id' AND `storage_id_from`='$storage_id_from' LIMIT 1;");

                                $rt = $db->query("SELECT `id` FROM `J_DP_STR` WHERE `art_id`='$art_id' AND `dp_id`='$dp_id' AND `storage_id_from`='$storage_id_from' LIMIT 1;");
                                $dp_str_id = $db->result($rt,0,"id");
                                $parrent_doc_id = $dp_str_id; // J_DP_STR ID

                                $db->query("INSERT INTO `J_SELECT_STR` (`id`,`select_id`,`art_id`,`article_nr_displ`,`brand_id`,`amount`,`storage_id_from`,`cell_id_from`,`status`,`parrent_doc_type_id`,`parrent_doc_id`) 
                                VALUES ('$str_id','$select_id','$art_id','$article_nr_displ','$brand_id','$amount_to_storsel','$storage_id_to','$cell_id_to',1,'$parrent_doc_type_id','$parrent_doc_id');");

                                $slave->addJuornalArtDocs(2, $jmoving_id, $art_id, $amount_to_storsel);

                                // move art_id to reserv
                                $rr = $dbt->query("SELECT * FROM `T2_ARTICLES_STRORAGE` WHERE `ART_ID`='$art_id' AND `STORAGE_ID`='$storage_id_to' LIMIT 1;");
                                $nr = $dbt->num_rows($rr);
                                if ($nr == 1) {
                                    $rr_amount = $dbt->result($rr,0,"AMOUNT");
                                    $rr_reserv = $dbt->result($rr,0,"RESERV_AMOUNT");
                                }
                                $rr_amount -= $amount_to_storsel;
                                $rr_reserv += $amount_to_storsel;
                                $dbt->query("UPDATE `T2_ARTICLES_STRORAGE` SET `AMOUNT`='$rr_amount', `RESERV_AMOUNT`='$rr_reserv' WHERE `ART_ID`='$art_id' AND `STORAGE_ID`='$storage_id_to';");

                                // CELL SET RESERV
                                if ($cell_use_to == 1) {
                                    $rr = $dbt->query("SELECT * FROM `T2_ARTICLES_STRORAGE_CELLS` WHERE `ART_ID`='$art_id' AND `STORAGE_ID`='$storage_id_to' AND `STORAGE_CELLS_ID`='$cell_id_to' LIMIT 1;");
                                    $nr = $dbt->num_rows($rr);
                                    if ($nr == 1) {
                                        $rr_amount = $dbt->result($rr,0,"AMOUNT");
                                        $rr_reserv = $dbt->result($rr,0,"RESERV_AMOUNT");
                                    }
                                    $rr_amount -= $amount_to_storsel;
                                    $rr_reserv += $amount_to_storsel;
                                    $dbt->query("UPDATE `T2_ARTICLES_STRORAGE_CELLS` SET `AMOUNT`='$rr_amount', `RESERV_AMOUNT`='$rr_reserv' 
                                    WHERE `ART_ID`='$art_id' AND `STORAGE_ID`='$storage_id_to' AND `STORAGE_CELLS_ID`='$cell_id_to';");
                                }
                            }
                            $db->query("UPDATE `J_SELECT` SET `amount`='$s_amount', `articles_amount`='$s_articles_amount', `volume`='$s_volume', `weight_netto`='$s_weight_netto' WHERE `id`='$select_id' LIMIT 1;");
                        }
                    }
                }
            }
        } else {
            $answer = 0; $err = "Помилка штрих-коду";
        }
        return array($answer, $err, $id);
    }

    function finishJmovingLocalAcceptForm($jmoving_id) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка індексу!!";
        $id = 0;
        $jmoving_id = $slave->qq($jmoving_id);
        $storage_id_to = $select_id = $er_from = 0;
        if ($jmoving_id > 0) {
            $r = $db->query("SELECT * FROM `J_MOVING` WHERE `id` = $jmoving_id LIMIT 1;");
            $n = $db->num_rows($r);
            if ($n == 1) {
                $storage_id_to = $db->result($r,0,"storage_id_to");
            }
            if ($storage_id_to > 0) {
                $rs = $db->query("SELECT js.*, jss.cell_id_from as cell_id_from2, jss.cell_id_to as cell_id_to2 
                FROM `J_MOVING_STR` js 
                    LEFT OUTER JOIN `J_MOVING_SELECT_STR` jss on (jss.select_id=js.select_id)
                WHERE js.jmoving_id='$jmoving_id' AND jss.jmoving_id='$jmoving_id' GROUP BY js.id;");
                $ns = $db->num_rows($rs);
                for ($is = 1; $is <= $ns; $is++) {
                    $art_id = $db->result($rs,$is-1,"art_id");
                    $select_id = $db->result($rs,$is-1,"select_id");
                    $amount = $db->result($rs,$is-1,"amount");
                    $cell_id_from2 = $db->result($rs,$is-1,"cell_id_from");
                    $cell_id_to2 = $db->result($rs,$is-1,"cell_id_to");
                    $slave->addJuornalArtDocs(2, $jmoving_id, $art_id, $amount);
                    $er_from = $this->updateStockFromStorageLocal($art_id, $storage_id_to, $cell_id_from2, $cell_id_to2, $amount);
                }
            }
            if ($er_from == 0) {
                $prefix = $this->get_jmoving_prefix($jmoving_id);
                $db->query("UPDATE `J_MOVING` SET `status_jmoving`='57', `oper_status`='31' WHERE `id` = $jmoving_id LIMIT 1;");
                $r = $db->query("SELECT MAX(`doc_nom`) as doc_nom FROM `J_MOVING` WHERE `oper_status`='31' AND `status`='1' AND `type_id`='0' AND `prefix`='В-ПР' AND `status_jmoving`='57' LIMIT 1;");
                $doc_nom = 0 + $db->result($r,0,"doc_nom") + 1;
                $db->query("UPDATE `J_MOVING` SET `prefix`='$prefix', `doc_nom`='$doc_nom', `data`=CURDATE() WHERE `id` = $jmoving_id LIMIT 1;");
                $this->addJuornalRecord($jmoving_id, $select_id, 57);
                $answer = 1; $err = "";
            }
        } else {
            $answer = 0; $err = "Помилка штрих-коду";
        }
        return array($answer, $err, $id);
    }

    function updateStockStorageBug($art_id, $storage_id_from, $cell_id_from, $cell_use, $amount) { $dbt = DbSingleton::getTokoDb();
        $er = 1;
        $r = $dbt->query("SELECT `AMOUNT`, `RESERV_AMOUNT` FROM `T2_ARTICLES_STRORAGE` WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id_from LIMIT 1;");
        $n = $dbt->num_rows($r);
        if ($n == 1) {
            $t2s_reserv_amount = $dbt->result($r,0,"RESERV_AMOUNT");
            $t2s_amount = $dbt->result($r,0,"AMOUNT");
            if ($amount <= $t2s_reserv_amount) {
                $t2s_reserv_amount = $t2s_reserv_amount - $amount;
                $t2s_amount = $t2s_amount + $amount;
                $dbt->query("UPDATE `T2_ARTICLES_STRORAGE` SET `RESERV_AMOUNT`='$t2s_reserv_amount', `AMOUNT`='$t2s_amount' 
                WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id_from LIMIT 1;");
                if ($cell_use == 1) {
                    $r1 = $dbt->query("SELECT `AMOUNT`, `RESERV_AMOUNT` FROM `T2_ARTICLES_STRORAGE_CELLS` 
                    WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id_from AND `STORAGE_CELLS_ID` = $cell_id_from LIMIT 1;");
                    $n1 = $dbt->num_rows($r1);
                    if ($n1 == 1) {
                        $t2sc_reserv_amount = $dbt->result($r1,0,"RESERV_AMOUNT");
                        $t2sc_amount = $dbt->result($r1,0,"AMOUNT");
                        if ($amount > 0) {
                            $t2sc_reserv_amount = $t2sc_reserv_amount - $amount;
                            $t2sc_amount = $t2sc_amount + $amount;
                            $dbt->query("UPDATE `T2_ARTICLES_STRORAGE_CELLS` SET `RESERV_AMOUNT`='$t2sc_reserv_amount', `AMOUNT`='$t2sc_amount' 
                            WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id_from AND `STORAGE_CELLS_ID` = $cell_id_from LIMIT 1;");
                        }
                    }
                }
                $er = 0;
            }
        }
        return $er;
    }

    function updateStockFromStorage($art_id, $storage_id_from, $cell_id_from, $cell_use, $amount) { $dbt = DbSingleton::getTokoDb();
        $dbt->query("UPDATE `T2_ARTICLES_STRORAGE` SET `RESERV_AMOUNT`=`RESERV_AMOUNT` - $amount 
        WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id_from LIMIT 1;");
        if ($cell_use == 1) {
            $dbt->query("UPDATE `T2_ARTICLES_STRORAGE_CELLS` SET `RESERV_AMOUNT`=`RESERV_AMOUNT` - $amount 
            WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id_from AND `STORAGE_CELLS_ID` = $cell_id_from LIMIT 1;");
        }
        return 0;
    }

    function updateStockToStorage($art_id, $storage_id_to, $cell_id_to, $cell_use, $amount) { $dbt = DbSingleton::getTokoDb();
        $er = 1;
        $r = $dbt->query("SELECT `AMOUNT` FROM `T2_ARTICLES_STRORAGE` WHERE `ART_ID`='$art_id' AND `STORAGE_ID`='$storage_id_to' LIMIT 1;");
        $n = $dbt->num_rows($r);
        if ($n == 0) {
            $dbt->query("INSERT INTO `T2_ARTICLES_STRORAGE` (`ART_ID`,`AMOUNT`,`RESERV_AMOUNT`,`STORAGE_ID`) VALUES ('$art_id','$amount','0','$storage_id_to');");
            if ($cell_use == 1) {
                $dbt->query("INSERT INTO `T2_ARTICLES_STRORAGE_CELLS` (`ART_ID`,`AMOUNT`,`RESERV_AMOUNT`,`STORAGE_ID`,`STORAGE_CELLS_ID`) VALUES ('$art_id','$amount','0','$storage_id_to','$cell_id_to');");
            }
            $er = 0;
        }
        if ($n == 1) {
            $dbt->query("UPDATE `T2_ARTICLES_STRORAGE` SET `AMOUNT`=`AMOUNT` + $amount WHERE `ART_ID`='$art_id' AND `STORAGE_ID`='$storage_id_to' LIMIT 1;");
            if ($cell_use == 1) {
                $r1 = $dbt->query("SELECT `AMOUNT` FROM `T2_ARTICLES_STRORAGE_CELLS` WHERE `ART_ID`='$art_id' AND `STORAGE_ID`='$storage_id_to' AND `STORAGE_CELLS_ID`='$cell_id_to' LIMIT 1;");
                $n1 = $dbt->num_rows($r1);
                if ($n1 == 0) {
                    $dbt->query("INSERT INTO `T2_ARTICLES_STRORAGE_CELLS` (`ART_ID`,`AMOUNT`,`RESERV_AMOUNT`,`STORAGE_ID`,`STORAGE_CELLS_ID`) VALUES ('$art_id','$amount','0','$storage_id_to','$cell_id_to');");
                }
                if ($n1 == 1) {
                    $dbt->query("UPDATE `T2_ARTICLES_STRORAGE_CELLS` SET `AMOUNT`=`AMOUNT` + $amount WHERE `ART_ID`='$art_id' AND `STORAGE_ID`='$storage_id_to' AND `STORAGE_CELLS_ID`='$cell_id_to' LIMIT 1;");
                }
            }
            $er = 0;
        }
        return $er;
    }

    function updateStockFromStorageLocal($art_id, $storage_id_from, $cell_id_from, $cell_id_to, $amount) { $dbt = DbSingleton::getTokoDb();
        $er = 1;
        $r = $dbt->query("SELECT `AMOUNT`, `RESERV_AMOUNT` FROM `T2_ARTICLES_STRORAGE` WHERE `ART_ID` = $art_id and `STORAGE_ID` = $storage_id_from LIMIT 1;");
        $n = $dbt->num_rows($r);
        if ($n == 1) {
            $t2s_amount = $dbt->result($r,0,"AMOUNT");
            $t2s_reserv_amount = $dbt->result($r,0,"RESERV_AMOUNT");
            if ($amount <= $t2s_reserv_amount) {
                $t2s_reserv_amount = $t2s_reserv_amount - $amount;
                $t2s_amount = $t2s_amount + $amount;
                $dbt->query("UPDATE `T2_ARTICLES_STRORAGE` SET `RESERV_AMOUNT`='$t2s_reserv_amount',`AMOUNT`='$t2s_amount' WHERE `ART_ID` = $art_id and `STORAGE_ID` = $storage_id_from LIMIT 1;");
                $r1 = $dbt->query("SELECT `RESERV_AMOUNT` FROM `T2_ARTICLES_STRORAGE_CELLS` WHERE `ART_ID` = $art_id and `STORAGE_ID` = $storage_id_from and `STORAGE_CELLS_ID` = $cell_id_from LIMIT 1;");
                $n1 = $dbt->num_rows($r1);
                if ($n1 == 1) {
                    $t2sc_reserv_amount = $dbt->result($r1,0,"RESERV_AMOUNT");
                    if ($amount > 0) {
                        $t2sc_reserv_amount = $t2sc_reserv_amount - $amount;
                        $dbt->query("UPDATE `T2_ARTICLES_STRORAGE_CELLS` SET `RESERV_AMOUNT`='$t2sc_reserv_amount' WHERE `ART_ID` = $art_id and `STORAGE_ID` = $storage_id_from and `STORAGE_CELLS_ID` = $cell_id_from LIMIT 1;");
                    }
                }
                $r2 = $dbt->query("SELECT `AMOUNT` FROM `T2_ARTICLES_STRORAGE_CELLS` WHERE `ART_ID` = $art_id and `STORAGE_ID` = $storage_id_from and `STORAGE_CELLS_ID`='$cell_id_to' LIMIT 1;");$n2=$dbt->num_rows($r2);
                if ($n2 == 0) {
                    $dbt->query("INSERT INTO `T2_ARTICLES_STRORAGE_CELLS` (`ART_ID`,`AMOUNT`,`RESERV_AMOUNT`,`STORAGE_ID`,`STORAGE_CELLS_ID`) VALUES ('$art_id','$amount','0','$storage_id_from','$cell_id_to');");
                }
                if ($n2 == 1) {
                    $t2sc_amount2 = $dbt->result($r2,0,"AMOUNT");
                    if ($amount > 0) {
                        $t2sc_amount2 = $t2sc_amount2 + $amount;
                        $dbt->query("UPDATE `T2_ARTICLES_STRORAGE_CELLS` SET `AMOUNT`='$t2sc_amount2' WHERE `ART_ID` = $art_id and `STORAGE_ID` = $storage_id_from and `STORAGE_CELLS_ID`='$cell_id_to' LIMIT 1;");
                    }
                }
            }
            $er = 0;
        }
        return $er;
    }

    function showSelectStatusList() { $db = DbSingleton::getDb();
        $list = "";
        $r = $db->query("SELECT * FROM `manual` WHERE `key`='status_jmoving' ORDER BY `mid` ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r,$i-1,"id");
            $mcaption = $db->result($r,$i-1,"mcaption");
            $list .= "<option value='$id'>$mcaption</option>";
        }
        return $list;
    }

    function separateJmovingByDefect($jmoving_id) { $db = DbSingleton::getDb();
        $bugs = [];
        $bug1 = $bug2 = $bug3 = $bug4 = 0;
        $storage_id_from = 0;
        $jmoving1 = $jmoving2 = $jmoving3 = $jmoving4 = 0;
        list($type_id, $prefix, $doc_nom, $storage_id_to, $cell_id_to) = $this->getJmovingData($jmoving_id);
        $r = $db->query("SELECT jb.storage_select_bug as bug, jb.amount_bug as bug_count, js.* 
        FROM `J_MOVING_STR_BUG` jb
            LEFT OUTER JOIN `J_MOVING_STR` js on (js.id=jb.str_id) 	
        WHERE jb.jmoving_id='$jmoving_id';");
        $n = $db->num_rows($r);
        if ($n > 0) {
            for ($i = 1; $i <= $n; $i++) {
                $storage_select_bug=$db->result($r,$i-1,"bug");
                $amount_bug=$db->result($r,$i-1,"bug_count");
                $art_id=$db->result($r,$i-1,"art_id");
                $article_nr_displ=$db->result($r,$i-1,"article_nr_displ");
                $brand_id=$db->result($r,$i-1,"brand_id");
                $amount=$db->result($r,$i-1,"amount");
                $storage_id_from=$db->result($r,$i-1,"storage_id_from");
                $cell_id_from=$db->result($r,$i-1,"cell_id_from");

                if ($storage_select_bug == 1) $bug1++;
                if ($storage_select_bug == 2) $bug2++;
                if ($storage_select_bug == 3) $bug3++;
                if ($storage_select_bug == 4) $bug4++;

                $bugs[$i] = ["storage_select_bug" => $storage_select_bug, "art_id" => $art_id, "article_nr_displ" => $article_nr_displ, "brand_id" => $brand_id, "amount" => $amount, "storage_id_to" => $storage_id_to, "cell_id_to" => $cell_id_to, "amount_bug"=>$amount_bug];

                $this->storageJmovingDefect($art_id, $storage_id_to, $storage_id_from, $amount_bug);
                $this->cellsJmovingDefect($art_id, $storage_id_to, $storage_id_from, $cell_id_to, $cell_id_from, $amount_bug);
            }

            if ($bug1 > 0) {
                $comment = "`Брак` згідно переміщення $prefix-$doc_nom";
                $jmoving1 = $this->insertIntoJmoving($type_id, $storage_id_from, $cell_id_to, $comment);
            }
            if ($bug2 > 0) {
                $comment = "`Недостача` згідно переміщення $prefix-$doc_nom";
                $jmoving2 = $this->insertIntoJmoving($type_id, $storage_id_from, $cell_id_to, $comment);
            }
            if ($bug3 > 0) {
                $comment = "`Пересорт` згідно переміщення $prefix-$doc_nom";
                $jmoving3 = $this->insertIntoJmoving($type_id, $storage_id_from, $cell_id_to, $comment);
            }
            if ($bug4 > 0) {
                $comment = "`Відмова клієнта` згідно переміщення $prefix-$doc_nom";
                $jmoving4 = $this->insertIntoJmoving($type_id, $storage_id_from, $cell_id_to, $comment);
            }

            for ($i = 1; $i <= $n; $i++) {
                if ($bugs[$i]["storage_select_bug"] == 1) {
                    $this->insertIntoJmovingStr($jmoving1, $bugs[$i]["art_id"], $bugs[$i]["article_nr_displ"], $bugs[$i]["brand_id"], $bugs[$i]["storage_id_to"], $bugs[$i]["cell_id_to"], $bugs[$i]["amount_bug"]);
                }
                if ($bugs[$i]["storage_select_bug"] == 2) {
                    $this->insertIntoJmovingStr($jmoving2, $bugs[$i]["art_id"], $bugs[$i]["article_nr_displ"], $bugs[$i]["brand_id"], $bugs[$i]["storage_id_to"], $bugs[$i]["cell_id_to"], $bugs[$i]["amount_bug"]);
                }
                if ($bugs[$i]["storage_select_bug"] == 3) {
                    $this->insertIntoJmovingStr($jmoving3, $bugs[$i]["art_id"], $bugs[$i]["article_nr_displ"], $bugs[$i]["brand_id"], $bugs[$i]["storage_id_to"], $bugs[$i]["cell_id_to"], $bugs[$i]["amount_bug"]);
                }
                if ($bugs[$i]["storage_select_bug"] == 4) {
                    $this->insertIntoJmovingStr($jmoving4, $bugs[$i]["art_id"], $bugs[$i]["article_nr_displ"], $bugs[$i]["brand_id"], $bugs[$i]["storage_id_to"], $bugs[$i]["cell_id_to"], $bugs[$i]["amount_bug"]);
                }
            }

            $db->query("UPDATE `J_MOVING` SET `status_jmoving`=57 WHERE `id` = $jmoving_id;");
            $answer = 1; $err = "";
        } else {
            $answer = 0; $err = "Помилка розділення по відхиленням!";
        }
        return array($answer, $err);
    }

    function insertIntoJmoving($type_id, $storage_id_from, $cell_id_to, $comment) { $db = DbSingleton::getDb();
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $r = $db->query("SELECT MAX(`id`) as mid FROM `J_MOVING`;");
        $jmoving_id = 0 + $db->result($r,0,"mid") + 1;
        $doc_nom = $this->get_df_doc_nom_new();
        $prefix = $this->prefix_new;
        $db->query("INSERT INTO `J_MOVING` (`id`,`type_id`,`prefix`,`doc_nom`,`user_id`,`data`,`storage_id_to`,`cell_id_to`,`status_jmoving`,`oper_status`,`comment`) 
        VALUES ('$jmoving_id','$type_id','$prefix','$doc_nom','$user_id',CURDATE(),'$storage_id_from','$cell_id_to','44','30','$comment');");
        return $jmoving_id;
    }

    function insertIntoJmovingStr($jmoving_id, $art_id, $article_nr_displ, $brand_id, $storage_id_to, $cell_id_from, $amount_bug) { $db = DbSingleton::getDb();
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $rstr = $db->query("SELECT * FROM `J_MOVING_STR` WHERE `jmoving_id` = $jmoving_id AND `art_id`='$art_id';");
        $nstr = $db->num_rows($rstr);
        $old_amount_bug = $db->result($rstr,0,"amount_bug");
        $id = 0;
        if ($nstr > 0) {
            $new_amount_bug = intval($old_amount_bug) + intval($amount_bug);
            $db->query("UPDATE `J_MOVING_STR` SET `amount_bug`='$new_amount_bug' WHERE `jmoving_id` = $jmoving_id AND `art_id`='$art_id';");
        }
        else {
            $r = $db->query("SELECT MAX(`id`) as mid FROM `J_MOVING_STR`;");
            $id = 0 + $db->result($r,0,"mid") + 1;
            $db->query("INSERT INTO `J_MOVING_STR` (`id`,`jmoving_id`,`art_id`,`article_nr_displ`,`brand_id`,`amount`,`storage_id_from`,`cell_id_from`,`user_id`) 
            VALUES ('$id','$jmoving_id','$art_id','$article_nr_displ','$brand_id','$amount_bug','$storage_id_to','$cell_id_from','$user_id');");
        }
        return $id;
    }

    function storageJmovingDefect($art_id, $storage_to_id, $storage_from_id, $reserv) { $dbt = DbSingleton::getTokoDb();
        $r1 = $dbt->query("SELECT * FROM `T2_ARTICLES_STRORAGE` WHERE `ART_ID`='$art_id' AND `STORAGE_ID`='$storage_from_id' LIMIT 1;");
        $reserv_amount1 = floatval($dbt->result($r1, 0, "RESERV_AMOUNT"));
        $nerezerv = $reserv_amount1 - floatval($reserv);

        $r2 = $dbt->query("SELECT * FROM `T2_ARTICLES_STRORAGE` WHERE `ART_ID`='$art_id' AND `STORAGE_ID`='$storage_to_id' LIMIT 1;");
        $amount2 = floatval($dbt->result($r2,0,"AMOUNT"));
        $reserv_amount2 = floatval($dbt->result($r2,0,"RESERV_AMOUNT"));
        $amount = $amount2 + floatval($nerezerv);
        $reserv_amount = $reserv_amount2 + floatval($reserv);

        $dbt->query("UPDATE `T2_ARTICLES_STRORAGE` SET `RESERV_AMOUNT`=0 WHERE `ART_ID`='$art_id' AND `STORAGE_ID`='$storage_from_id';");

        $res = $dbt->query("SELECT * FROM `T2_ARTICLES_STRORAGE` WHERE `ART_ID`='$art_id' AND `STORAGE_ID`='$storage_to_id';");
        $n = $dbt->num_rows($res);

        if ($n == 0) {
            $dbt->query("INSERT INTO `T2_ARTICLES_STRORAGE` (`ART_ID`,`AMOUNT`,`RESERV_AMOUNT`,`STORAGE_ID`) VALUES ('$art_id','$amount','$reserv_amount','$storage_to_id');");
        } else {
            $dbt->query("UPDATE `T2_ARTICLES_STRORAGE` SET `AMOUNT`='$amount', `RESERV_AMOUNT`='$reserv_amount' WHERE `ART_ID`='$art_id' AND `STORAGE_ID`='$storage_to_id';");
        }
    }

    function cellsJmovingDefect($art_id, $storage_to_id, $storage_from_id, $cell_to_id, $cell_from_id, $reserv) { $dbt = DbSingleton::getTokoDb();
        $r1 = $dbt->query("SELECT * FROM `T2_ARTICLES_STRORAGE_CELLS` WHERE ART_ID='$art_id' AND STORAGE_ID='$storage_from_id' AND STORAGE_CELLS_ID='$cell_from_id' LIMIT 1;");
        $reserv_amount1 = floatval($dbt->result($r1, 0, "RESERV_AMOUNT"));
        $nerezerv = $reserv_amount1 - floatval($reserv);

        $r2 = $dbt->query("SELECT * FROM `T2_ARTICLES_STRORAGE_CELLS` WHERE ART_ID='$art_id' AND STORAGE_ID='$storage_to_id' AND STORAGE_CELLS_ID='$cell_to_id' LIMIT 1;");
        $amount2 = floatval($dbt->result($r2, 0, "AMOUNT"));
        $reserv_amount2 = floatval($dbt->result($r2,0,"RESERV_AMOUNT"));
        $amount = $amount2 + floatval($nerezerv);
        $reserv_amount = $reserv_amount2 + floatval($reserv);

        $dbt->query("UPDATE `T2_ARTICLES_STRORAGE_CELLS` SET `RESERV_AMOUNT`=0 WHERE `ART_ID`='$art_id' AND `STORAGE_ID`='$storage_from_id' AND `STORAGE_CELLS_ID`='$cell_from_id';");

        $res = $dbt->query("SELECT * FROM `T2_ARTICLES_STRORAGE_CELLS` WHERE ART_ID='$art_id' AND STORAGE_ID='$storage_to_id' AND STORAGE_CELLS_ID='$cell_to_id';");
        $n = $dbt->num_rows($res);

        if ($n == 0) {
            $dbt->query("INSERT INTO `T2_ARTICLES_STRORAGE_CELLS` (ART_ID,AMOUNT,RESERV_AMOUNT,STORAGE_ID,STORAGE_CELLS_ID) 
            VALUES ('$art_id','$amount','$reserv_amount','$storage_to_id','$cell_to_id');");
        }

        $amount += $reserv_amount;
        if ($n == 0) {
            $dbt->query("INSERT INTO `T2_ARTICLES_STRORAGE_CELLS` (ART_ID,AMOUNT,RESERV_AMOUNT,STORAGE_ID,STORAGE_CELLS_ID) VALUES ('$art_id','$amount','0','$storage_to_id','$cell_to_id');");
        } else {
            $dbt->query("UPDATE `T2_ARTICLES_STRORAGE_CELLS` SET `AMOUNT`='$amount', `RESERV_AMOUNT`='0' WHERE `ART_ID`='$art_id' AND `STORAGE_ID`='$storage_to_id' AND `STORAGE_CELLS_ID`='$cell_to_id';");
        }
    }

    function getArticleNrDisplBrand($art_id) { $db = DbSingleton::getTokoDb();
        $brand_id = 0;
        $article_nr_displ = $article_nr_search = $brand_name = "";
        $r = $db->query("SELECT t2a.ART_ID, t2a.BRAND_ID, t2a.ARTICLE_NR_DISPL, t2a.ARTICLE_NR_SEARCH, t2b.BRAND_NAME 
        FROM `T2_ARTICLES` t2a  
            LEFT OUTER JOIN `T2_BRANDS` t2b on t2b.BRAND_ID=t2a.BRAND_ID 
        WHERE t2a.ART_ID='$art_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $brand_id = $db->result($r,0,"BRAND_ID");
            $article_nr_displ = $db->result($r,0,"ARTICLE_NR_DISPL");
            $brand_name = $db->result($r,0,"BRAND_NAME");
            $article_nr_search = $db->result($r,0,"ARTICLE_NR_SEARCH");
        }
        return array($article_nr_displ, $brand_id, $brand_name, $article_nr_search);
    }

    function clearArticle($art) {
        $art=str_replace(" ","",$art);
        $art=str_replace("_","",$art);
        $art=str_replace("-","",$art);
        $art=str_replace(".","",$art);
        $art=str_replace("+","",$art);
        $art=str_replace("'","",$art);
        $art=str_replace("/","",$art);
        $art=str_replace('"',"",$art);
        $art=preg_replace ("/[^a-zA-ZА-Яа-я0-9\s]/","",$art);
        $art=strtolower($art);
        return $art;
    }

    function showCatalogueBrandSelectDocumentList($r) { $db = DbSingleton::getTokoDb();
        $n = $db->num_rows($r);
        $tkey = time();
        $list = ""; $form = "";
        $db->query("CREATE TEMPORARY TABLE IF NOT EXISTS `NBRAND_RESULT_$tkey` (`art_id` INT NOT NULL ,`display_nr` VARCHAR( 100 ) NOT NULL ,`name` VARCHAR( 255 ) NOT NULL ,`brand_id` INT NOT NULL ,`brand_name` VARCHAR( 100 ) NOT NULL ,`kol_res` TINYINT NOT NULL) ENGINE = MYISAM ;");
        for ($i = 1; $i <= $n; $i++) {
            $art_id = $db->result($r,$i-1,"ART_ID");
            $display_nr = $db->result($r,$i-1,"DISPLAY_NR");
            $name = $db->result($r,$i-1,"NAME");
            $brand_id = $db->result($r,$i-1,"BRAND_ID");
            $brand_name = $db->result($r,$i-1,"BRAND_NAME");
            $kol_res = 0;
            $db->query("INSERT INTO `NBRAND_RESULT_$tkey` VALUES ('$art_id','$display_nr','$name','$brand_id','$brand_name','$kol_res');");
        }
        $r = $db->query("SELECT * FROM `NBRAND_RESULT_$tkey` ORDER BY `kol_res` DESC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $display_nr = $db->result($r,$i-1,"display_nr");
            $name = $db->result($r,$i-1,"name");
            $brand_id = $db->result($r,$i-1,"brand_id");
            $brand_name = $db->result($r,$i-1,"brand_name");
            $list .= "<tr style='cursor:pointer;' onClick='selectFromList2(\"$brand_id\",\"$display_nr\")'>
                <td>$display_nr</td>
                <td>$brand_name</td>
                <td>$name</td>
            </tr>";
        }
        if ($n > 0) {
            $form_htm = RD . "/tpl/catalogue_brand_select_list.htm";
            if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
            $form = str_replace("{list}",$list,$form);
        }
        $db->query("DROP TEMPORARY TABLE IF EXISTS `NBRAND_RESULT_$tkey`;");
        return $form;
    }

    /*====IMPORT */

    function loadJmovingImport($jmoving_id) {
        $form = ""; $form_htm = RD . "/tpl/jmoving_import_str_form.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $header = ""; $header_htm = RD . "/tpl/jmoving_import_str_header.htm";
        if (file_exists("$header_htm")) { $header = file_get_contents($header_htm); }
        list(, $csv_file_name, $pre_table) = $this->showCsvPreview($jmoving_id);
        $table = $this->showTablePreview($jmoving_id);
        $form = str_replace("{ibox_header}",($table == "") ? $header : "",$form);
        $form = str_replace("{records_list}","<tr><td colspan=10 align='center'>Записи не завантажено</td></tr>",$form);
        $form = str_replace("{import_file_name}",$csv_file_name,$form);
        $form = str_replace("{jmoving_id}",$jmoving_id,$form);
        $form = str_replace("{csv_str_file}",($table == "") ? $pre_table : "",$form);
        $form = str_replace("{table_str_file}",$table,$form);
        return $form;
    }

    function getCsvIndexBrands($jmoving_id) { $db = DbSingleton::getDb(); $dbt = DbSingleton::getTokoDb();
        $brands = []; $list = "";
        $r = $db->query("SELECT * FROM `J_MOVING_IMPORT` WHERE `jmoving_id` = $jmoving_id AND `status`=0;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $article_nr_displ = $db->result($r, $i - 1, "article_nr_displ");
            $r2 = $dbt->query("SELECT * FROM `T2_ARTICLES` WHERE `ARTICLE_NR_DISPL`='$article_nr_displ' OR `ARTICLE_NR_SEARCH`='$article_nr_displ';");
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

    function showTablePreview($jmoving_id) {
        $form = ""; $form_htm = RD . "/tpl/csv_str_jmoving_import.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $form = str_replace("{jmoving_id}",$jmoving_id,$form);
        $form = str_replace("{csv_brands}",$this->getCsvIndexBrands($jmoving_id),$form);
        $form = str_replace("{storage_title}",$this->showStorageFieldsTitle($jmoving_id),$form);
        $table = $this->loadTablePreview($jmoving_id);
        $form = str_replace("{records_list}",$table,$form);
        if ($table == "") {
            $form = "";
        }
        return $form;
    }

    function loadTablePreview($jmoving_id, $brands = 0) { $db = DbSingleton::getDb(); $dbt = DbSingleton::getTokoDb();
        $table = ""; $where_arts = ""; $mas = [];
        $db->query("UPDATE `J_MOVING_IMPORT` SET `selected`=0 WHERE `jmoving_id` = $jmoving_id;");
        if ($brands != 0) {
            $arts = [];
            $r = $db->query("SELECT * FROM `J_MOVING_IMPORT` WHERE `jmoving_id` = $jmoving_id AND `status`=0;");
            $n = $db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $article_nr_displ = $db->result($r, $i - 1, "article_nr_displ");
                array_push($arts, "'".$article_nr_displ."'");
            }
            $arts = array_unique($arts);
            $arts = implode(",", $arts);

            if ($arts != "") {
                $new_arts = [];
                $r = $dbt->query("SELECT * FROM `T2_CROSS` WHERE (`DISPLAY_NR` IN ($arts) OR `SEARCH_NUMBER` IN ($arts)) AND `BRAND_ID`='$brands';");
                $n = $db->num_rows($r);
                for ($i = 1; $i <= $n; $i++) {
                    $article_nr_displ = $db->result($r, $i - 1, "DISPLAY_NR");
                    $article_nr_search = $db->result($r, $i - 1, "SEARCH_NUMBER");
                    array_push($new_arts, "'".$article_nr_displ."'");
                    array_push($new_arts, "'".$article_nr_search."'");
                }
                $new_arts = array_unique($new_arts);
                $where_arts = "AND `article_nr_displ` IN (".implode(",",$new_arts).")";
            }
        }

        $r = $db->query("SELECT * FROM `J_MOVING_IMPORT` WHERE `jmoving_id` = $jmoving_id $where_arts ORDER BY `status` ASC, `id` ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r, $i - 1, "id");
            $article_nr_displ = $db->result($r, $i - 1, "article_nr_displ");
            $amount = $db->result($r, $i - 1, "amount");
            $art_id = $db->result($r, $i - 1, "art_id");
            $brand_id = $db->result($r, $i - 1, "brand_id");
            $brand_name=$this->getBrandName($brand_id);
            $status = $db->result($r, $i - 1, "status");
            list($full_amount, $storage_info) = $this->getJmovingStockInfo($art_id,$jmoving_id);
            if ($status) $style=1; else $style=3;
            if ($full_amount<$amount && $status) $style=2;
            $mas[$i] = ["id" => $id, "art_id" => $art_id, "article_nr_displ" => $article_nr_displ, "brand_id" => $brand_id, "brand_name" => $brand_name, "amount" => $amount, "storage_info" => $storage_info, "status" => $status, "style" => $style];
        }

        usort($mas, "sortJmStyle");
        $i = 0;
        foreach ($mas as $val) {
            $i++;
            $style = $val["style"]; $st = "";
            if ($style == 1) {
                $st = "style='background:lightgreen;'";
            }
            if ($style == 2) {
                $st = "style='background:lightyellow;'";
            }
            if ($style == 3) {
                $st = "style='background:pink;'";
            }
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
                $db->query("UPDATE `J_MOVING_IMPORT` SET `selected`=1 WHERE `id`='$id';");
            }
        }
        return $table;
    }

    function saveTablePreview($jmoving_id, $brands = 0) { $db = DbSingleton::getDb();
        $answer = 0; $err = "Помилка збереження даних!";
        if ($jmoving_id > 0 && $brands != 0) {
            $r = $db->query("SELECT * FROM `J_MOVING_IMPORT` WHERE `jmoving_id` = $jmoving_id AND `status`=0 AND `selected`=1;");
            $n = $db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $id = $db->result($r, $i - 1, "id");
                $article_nr_displ = $db->result($r, $i - 1, "article_nr_displ");
                $brand_id = $brands;
                $art_id = $this->getArtId($article_nr_displ, $brand_id);
                if ($art_id != 0 && $brand_id != 0) {
                    $db->query("UPDATE `J_MOVING_IMPORT` SET `brand_id`='$brand_id', `art_id`='$art_id', `status`=1 WHERE `id`='$id';");
                }
            }
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

    function clearJmovingImport($jmoving_id) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка збереження даних!";
        $jmoving_id = $slave->qq($jmoving_id);
        if ($jmoving_id > 0) {
            $db->query("DELETE FROM `J_MOVING_IMPORT` WHERE `jmoving_id` = $jmoving_id;");
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

    function saveCsvJmovingImport($jmoving_id, $start_row, $kol_cols, $cols) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка збереження даних!";
        $fn = 0;
        $jmoving_id=$slave->qq($jmoving_id); $start_row=$slave->qq($start_row); $kol_cols=$slave->qq($kol_cols); $cols=$slave->qq($cols);
        $db->query("DELETE FROM `J_MOVING_IMPORT` WHERE `jmoving_id` = $jmoving_id;");
        if ($jmoving_id > 0) {
            $r = $db->query("SELECT * FROM `J_MOVING_CSV` WHERE `jmoving_id` = $jmoving_id LIMIT 1;");
            $n = $db->num_rows($r);
            if ($n == 1) {
                $file_name = $db->result($r,0,"file_name");
                $file_path = RD . "/cdn/jmoving_files/csv/$jmoving_id/$file_name";
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
                            $fn+=1;
                            $buf=explode(";", $buffer);
                            if ($buffer!="") {
                                if ($fn>=$start_row) {
                                    $buf=str_replace("'","\'",$buf);
                                    $buf=str_replace('"','\"',$buf);
                                    $ind=trim($buf[$index-1]);
                                    $brnd=trim($buf[$brand-1]);
                                    $amnt=trim($buf[$amount-1]);
                                    $amnt=str_replace(",",".",$amnt);
                                    $amnt=str_replace(" ","",$amnt);
                                    $brand_id=$this->getBrandId($brnd);
                                    $art_id=$this->getArtId($ind,$brand_id);
                                    if ($brand_id!=0 && $art_id!=0) {
                                        $status=1;
                                    } else {
                                        $brand_id=0;
                                        $art_id=0;
                                        $status=0;
                                    }
                                    $r2=$db->query("SELECT MAX(`id`) as mid FROM `J_MOVING_IMPORT`;");
                                    $import_id=0+$db->result($r2,0,"mid")+1;
                                    $rs=$db->query("SELECT * FROM `J_MOVING_IMPORT` WHERE `jmoving_id` = $jmoving_id AND `art_id`='$art_id' LIMIT 1;");
                                    $ns=$db->num_rows($rs);
                                    if ($ns>0 && $art_id>0) {
                                        $db->query("UPDATE `J_MOVING_IMPORT` SET `amount`=`amount`+$amnt WHERE `jmoving_id` = $jmoving_id AND `art_id`='$art_id' LIMIT 1;");
                                    } else {
                                        $db->query("INSERT INTO `J_MOVING_IMPORT` (`id`,`jmoving_id`,`art_id`,`article_nr_displ`,`brand_id`,`amount`,`status`) 
                                        VALUES ('$import_id','$jmoving_id','$art_id','$ind','$brand_id','$amnt','$status');");
                                    }
                                }
                            }
                        }
                        fclose($handle);
                        if (file_exists(RD."/cdn/jmoving_files/csv/$jmoving_id/$file_name")) {
                            unlink(RD."/cdn/jmoving_files/csv/$jmoving_id/$file_name");
                        }
                        $db->query("DELETE FROM `J_MOVING_CSV` WHERE `jmoving_id` = $jmoving_id;");
                        $answer = 1; $err = "";
                    }
                }
            }
        }
        return array($answer, $err);
    }

    function getJmovingStockInfo($art_id, $jmoving_id) { $db = DbSingleton::getTokoDb();
        $list = ""; $full_amount = 0;
        if ($art_id > 0) {
            $r = $db->query("SELECT * FROM `T2_ARTICLES_STRORAGE` WHERE `ART_ID`='$art_id';");
            $n = $db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $amount = $db->result($r, $i - 1, "AMOUNT");
                $storage_id = $db->result($r, $i - 1, "STORAGE_ID");
                $storage_name = $this->getStorageName($storage_id);
                $amount = intval($amount);
                if (in_array($storage_id, $this->getUserActiveStorages($jmoving_id))) {
                    $list .= "<span style='font-weight: bold;'>$storage_name</span>($amount); ";
                }
                $full_amount += $amount;
            }
        }
        $list = trim($list, "; ");
        return array($full_amount, $list);
    }

    function getUserActiveStorages($jmoving_id) { $db = DbSingleton::getDb();
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $storages = [];
        $r = $db->query("SELECT * FROM `CFN_USERS_STORAGE_CONFIG` WHERE `user_id`=$user_id;");
        $n = $db->num_rows($r);
        if ($n == 0) {
            $user_id = 0;
        }
        $storage_id_to = $this->getJmovingStorage($jmoving_id);
        $r = $db->query("SELECT * FROM `CFN_USERS_STORAGE_CONFIG` WHERE `user_id`='$user_id' AND `field_active`=1 AND `storage_id`!='$storage_id_to';");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $storage_id = $db->result($r, $i - 1, "storage_id");
            array_push($storages, $storage_id);
        }
        return $storages;
    }

    function moveArticlesStorage($jmoving_id,$art_id,$article_nr_displ,$brand_id,$amount,$storages) { $db = DbSingleton::getDb(); $dbt = DbSingleton::getTokoDb();
        $storage_str = [];
        $result = false;
        usort($storages, "cmpJmStorages");
        foreach ($storages as $value) {
            array_push($storage_str, $value["storage_id"]);
        }
        $storage_str = implode(",", $storage_str);
        if ($storage_str != "") {
            $where_storages = "AND `STORAGE_ID` IN ($storage_str)";
        } else {
            $where_storages = "";
        }

        $r = $dbt->query("SELECT SUM(`AMOUNT`) as summ_amount FROM `T2_ARTICLES_STRORAGE` WHERE `ART_ID`='$art_id' $where_storages;");
        $summ = $dbt->result($r, 0, "summ_amount");

        if ($summ < $amount && $summ > 0) {
            $bug_amount = $amount - $summ;
            $amount = $summ;
            $r2 = $db->query("SELECT MAX(`id`) as mid FROM `J_MOVING_STR_UNKNOWN_ARTS`;");
            $unknown_id = 0 + $db->result($r2,0,"mid") + 1;
            $db->query("INSERT INTO `J_MOVING_STR_UNKNOWN_ARTS` (`id`,`jmoving_id`,`art_id`,`article_nr_displ`,`brand_id`,`amount`,`bug_amount`,`caption`)
            VALUES ('$unknown_id','$jmoving_id','$art_id','$article_nr_displ','$brand_id','$amount','$bug_amount','Завелика кількість');");
        }

        if ($summ > 0) {
            $full_amount = $amount;
            foreach ($storages as $val) {
                $storage_id = $val["storage_id"];
                $r = $dbt->query("SELECT * FROM `T2_ARTICLES_STRORAGE` WHERE `ART_ID`='$art_id' AND `STORAGE_ID`='$storage_id' LIMIT 1;");
                $cur_amount = $dbt->result($r,0,"AMOUNT");
                if ($cur_amount > 0) {
                    if ($cur_amount <= $full_amount) {
                        $cut_amount = $cur_amount;
                    } else {
                        $cut_amount = $full_amount;
                    }

                    $db->query("INSERT INTO `J_MOVING_STR` (`jmoving_id`,`art_id`,`article_nr_displ`,`brand_id`,`amount`,`storage_id_from`,`status_jmoving`) 
                    VALUES ('$jmoving_id','$art_id','$article_nr_displ','$brand_id','$cut_amount','$storage_id','44');");

                    $dbt->query("UPDATE `T2_ARTICLES_STRORAGE` SET AMOUNT=AMOUNT-$cut_amount, RESERV_AMOUNT=RESERV_AMOUNT+$cut_amount WHERE `ART_ID`='$art_id' AND `STORAGE_ID`='$storage_id' LIMIT 1;");
                    $full_amount = $full_amount - $cut_amount;
                    if ($full_amount == 0) {
                        break;
                    }
                }
            }
            $result = true;
        } else {
            $r2 = $db->query("SELECT MAX(`id`) as mid FROM `J_MOVING_STR_UNKNOWN_ARTS`;");
            $unknown_id = 0 + $db->result($r2, 0, "mid") + 1;
            $db->query("INSERT INTO `J_MOVING_STR_UNKNOWN_ARTS` (`id`,`jmoving_id`,`art_id`,`article_nr_displ`,`brand_id`,`amount`,`bug_amount`,`caption`)
            VALUES ('$unknown_id','$jmoving_id','$art_id','$article_nr_displ','$brand_id','0','$amount','Позиції немає в наявності');");
        }
        return $result;
    }

    function showStorageFieldsTitle($jmoving_id) {$db = DbSingleton::getDb();
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $list = "";
        $r = $db->query("SELECT * FROM `CFN_USERS_STORAGE_CONFIG` WHERE `user_id`=$user_id;");
        $n = $db->num_rows($r);
        if ($n == 0) {
            $user_id = 0;
        }
        $r = $db->query("SELECT * FROM `CFN_USERS_STORAGE_CONFIG` WHERE `user_id`=$user_id AND `field_active`=1 ORDER BY `field_pos` ASC;");
        $n = $db->num_rows($r);
        $storage_id_to = $this->getJmovingStorage($jmoving_id);
        for ($i = 1; $i <= $n; $i++) {
            $storage_id = $db->result($r,$i-1,"storage_id");
            $storage_name = $this->getStorageName($storage_id);
            if ($storage_id != $storage_id_to) {
                $list .= "$i - $storage_name \n";
            }
        }
        return $list;
    }

    function showJmovingStorageFieldsViewForm($jmoving_id) { $db = DbSingleton::getDb();
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $form = ""; $form_htm = RD . "/tpl/jmoving_storage_fields_view_form.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $r = $db->query("SELECT * FROM `CFN_USERS_STORAGE_CONFIG` WHERE `user_id`='$user_id';");
        $n = $db->num_rows($r);
        if ($n == 0) {
            $user_id = 0;
        }
        $storage_id_to = $this->getJmovingStorage($jmoving_id);
        $r = $db->query("SELECT * FROM `CFN_USERS_STORAGE_CONFIG` WHERE `user_id`='$user_id' ORDER BY `id` ASC;");
        $n = $db->num_rows($r);
        $list = "";
        $lst = array();
        for ($i = 1; $i <= $n; $i++) {
            $storage_id = $db->result($r,$i-1,"storage_id");
            $field_active = $db->result($r,$i-1,"field_active");
            $checked = ($field_active == 0) ? "" : "checked";
            $pos = $db->result($r,$i-1,"field_pos");
            $storage_name = $this->getStorageName($storage_id);
            if ($pos == 0) {
                $pos = $i;
            }
            if ($storage_id_to == $storage_id) {
                $checked = "";
                $disabled = "disabled";
                $cap = " (Склад призначення!)";
            } else {
                $disabled = "";
                $cap = "";
            }

            $lst[$pos] = "<tr id='usePos_".$storage_id."'>
                <td><span class='glyphicon glyphicon-move'></span></td>
                <td>$i</td>
                <td>$storage_name ($storage_id) $cap</td>
                <td>
                    <div class=\"switch\">
                        <div class=\"onoffswitch\">
                            <input type=\"checkbox\" $checked class=\"onoffswitch-checkbox\" $disabled id=\"use_$storage_id\" value='1'>
                            <label class=\"onoffswitch-label\" for=\"use_$storage_id\">
                                <span class=\"onoffswitch-inner\"></span>
                                <span class=\"onoffswitch-switch\"></span>
                            </label>
                        </div>
                    </div>
                </td>
            </tr>";
        }
        for ($i = 1; $i <= $n; $i++) {
            $list .= $lst[$i];
        }
        $form = str_replace("{fields_list}",$list,$form);
        $form = str_replace("{kol_fields}",$n,$form);
        return $form;
    }

    function saveJmovingStorageFieldsViewForm($kol_fields, $fl_id, $fl_ch) { $db = DbSingleton::getDb();
        $slave = new slave;
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $answer = 0; $err = "Помилка збереження даних!";
        $kol_fields = $slave->qq($kol_fields); $fl_id = $slave->qq($fl_id); $fl_ch = $slave->qq($fl_ch);
        if ($kol_fields > 0) {
            $db->query("DELETE FROM `CFN_USERS_STORAGE_CONFIG` WHERE `user_id`='$user_id';");
            for ($i = 1; $i <= $kol_fields; $i++) {
                $storage_id = $fl_id[$i];
                $field_ch = $fl_ch[$i];
                $db->query("INSERT INTO `CFN_USERS_STORAGE_CONFIG` (`user_id`,`storage_id`,`field_active`,`field_pos`) 
                VALUES ('$user_id','$storage_id','$field_ch','$i');");
            }
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

    function showCsvPreview($jmoving_id) { $db = DbSingleton::getDb();
        $csv_exist = 0;
        $csv_file_name = "Оберіть файл";
        $pre_table = "<h3>Файл не знайдено</h3>";
        $kol_cols = 0; $fn = 0;
        $r = $db->query("SELECT * FROM `J_MOVING_CSV` WHERE `jmoving_id` = $jmoving_id LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $file_name = $db->result($r,0,"file_name");
            $file_path = RD . "/cdn/jmoving_files/csv/$jmoving_id/$file_name";
            if (file_exists($file_path)) {
                $form = ""; $form_htm = RD . "/tpl/csv_str_jmoving_file.htm";
                if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
                $cols_list = ""; $records_list = "";
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
                            $row = ""; $ex_cols = 0;
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
                                    $cols_list .= "<th><select id=\"clm-$i\" size='1'>
                                        <option value='0'>-</option>
                                        <option value='1'>Індекс</option>
                                        <option value='2'>Бренд</option>
                                        <option value='3'>Кількість</option>
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
                $form = str_replace("{jmoving_id}",$jmoving_id,$form);
                $form = str_replace("{cols_list}",$cols_list,$form);
                $form = str_replace("{records_list}",$records_list,$form);
                $form = str_replace("{kol_cols}",$kol_cols,$form);
                $csv_file_name = $file_name; $csv_exist = 1; $pre_table = $form;
            }
        }
        return array($csv_exist, $csv_file_name, $pre_table);
    }

    function finishJmovingImport($jmoving_id) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка збереження даних!";
        $jmoving_id = $slave->qq($jmoving_id);
        if ($jmoving_id > 0) {
            $db->query("DELETE FROM `J_MOVING_STR_UNKNOWN_ARTS` WHERE `jmoving_id` = $jmoving_id;");
            $r = $db->query("SELECT * FROM `J_MOVING_IMPORT` WHERE `jmoving_id` = $jmoving_id;");
            $n = $db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $id = $db->result($r, $i - 1, "id");
                $article_nr_displ = $db->result($r, $i - 1, "article_nr_displ");
                $amount = $db->result($r, $i - 1, "amount");
                $art_id = $db->result($r, $i - 1, "art_id");
                $brand_id = $db->result($r, $i - 1, "brand_id");
                $status = $db->result($r, $i - 1, "status");
                if ($status) {
                    $storages = $this->getUserStorages($jmoving_id);
                    $this->moveArticlesStorage($jmoving_id, $art_id, $article_nr_displ, $brand_id, $amount, $storages);
                } else {
                    $r2 = $db->query("SELECT MAX(`id`) as mid FROM `J_MOVING_STR_UNKNOWN_ARTS`;");
                    $unknown_id = 0 + $db->result($r2, 0, "mid") + 1;
                    $db->query("INSERT INTO `J_MOVING_STR_UNKNOWN_ARTS` (`id`,`jmoving_id`,`art_id`,`article_nr_displ`,`brand_id`,`amount`,`bug_amount`,`caption`)
                    VALUES ('$unknown_id','$jmoving_id','$art_id','$article_nr_displ','$brand_id','0','$amount','Не було ART_ID чи BRAND_ID');");
                }
                $db->query("DELETE FROM `J_MOVING_IMPORT` WHERE `id`='$id';");
            }
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

    function getUserStorages($jmoving_id) { $db = DbSingleton::getDb();
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $storages = [];
        $r = $db->query("SELECT * FROM `CFN_USERS_STORAGE_CONFIG` WHERE `user_id`=$user_id;");
        $n = $db->num_rows($r);
        if ($n == 0) {
            $user_id = 0;
        }
        $storage_id_to = $this->getJmovingStorage($jmoving_id);
        $r = $db->query("SELECT * FROM `CFN_USERS_STORAGE_CONFIG` WHERE `user_id`=$user_id AND `field_active`=1 AND `storage_id`!='$storage_id_to' ORDER BY `field_pos` ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $storage_id = $db->result($r,$i-1,"storage_id");
            $position = $db->result($r,$i-1,"field_pos");
            $storages[$i] = ["storage_id" => $storage_id, "position" => $position];
        }
        return $storages;
    }

    /*
     * UNKNOWN
     * */
    function loadJmovingUnknown($jmoving_id) { $db = DbSingleton::getDb();
        $form = ""; $form_htm = RD . "/tpl/jmoving_unknown_list.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $r = $db->query("SELECT * FROM `J_MOVING` WHERE `id` = $jmoving_id LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 0) {
            $form_htm = RD . "/tpl/access_deny.htm";
            if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        }
        if ($n == 1) {
            list($list, $kol_rows) = $this->showJmovingUnknownList($jmoving_id);
            $form = str_replace("{UnknownArticlesList}",$list,$form);
            $form = str_replace("{kol_rows}",$kol_rows,$form);
            $form = str_replace("{jmoving_id}",$jmoving_id,$form);
        }
        return $form;
    }

    function showJmovingUnknownList($jmoving_id) { $db = DbSingleton::getDb();
        $empty_kol = 0; $list = "";
        $r = $db->query("SELECT * FROM `J_MOVING_STR_UNKNOWN_ARTS` WHERE `jmoving_id` = $jmoving_id ORDER BY `id` ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $art_id = $db->result($r,$i-1,"art_id");
            $article_nr_displ = $db->result($r,$i-1,"article_nr_displ");
            $brand_id = $db->result($r,$i-1,"brand_id");
            $brand_name = $this->getBrandName($brand_id);
            $amount = $db->result($r,$i-1,"amount");
            $bug_amount = $db->result($r,$i-1,"bug_amount");
            $caption = $db->result($r,$i-1,"caption");
            $empty_kol += 1;
            $list .= "<tr id='strUnRow_$i'>
                <td><button class='btn btn-xs btn-warning'><i class='fa fa-refresh'></i></button></td>
                <td>$i</td>
                <td><input type='hidden' id='artIdUnStr_$i' value='$art_id'><input type='hidden' id='article_nr_displUnStr_$i' value='$article_nr_displ'>$article_nr_displ</td>
                <td>$brand_name</td>
                <td><input type='text' id='amountUnStr_$i' value='$amount' class='form-control input-xs numberOnlyLong'></td>
                <td><input type='text' id='bugAmountUnStr_$i' value='$bug_amount' class='form-control input-xs numberOnlyLong'></td>
                <td>$caption</td>
            </tr>";
        }
        return array($list, $empty_kol);
    }

    function clearJmovingUnknown($jmoving_id) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка збереження даних!";
        $jmoving_id = $slave->qq($jmoving_id);
        if ($jmoving_id > 0) {
            $db->query("DELETE FROM `J_MOVING_STR_UNKNOWN_ARTS` WHERE `jmoving_id` = $jmoving_id;");
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

    /*
     * Консолідування переміщень
     * */
    function getUserJmovingForm($storage_id, $jmoving_arr) { $db = DbSingleton::getDb();
        $list = "";
        $r = $db->query("SELECT js.*, j.prefix, j.doc_nom FROM `J_MOVING_STR` js 
            LEFT JOIN `J_MOVING` j ON j.id = js.jmoving_id
        WHERE j.`storage_id_to`='$storage_id' AND j.`status_jmoving` IN (48, 49)
        GROUP BY j.id;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $jmoving_id = $db->result($r, $i - 1, "jmoving_id");
            $prefix = $db->result($r, $i - 1, "prefix");
            $doc_nom = $db->result($r, $i - 1, "doc_nom");
            $checked = (in_array($jmoving_id, $jmoving_arr)) ? "checked" : "";
            $list .= "<div>
                <input type=\"checkbox\" $checked id=\"$jmoving_id\" name=\"jmovings\">
                <label for=\"$jmoving_id\">$prefix - $doc_nom</label>
            </div>";
        }
        return $list;
    }

    function getJmovingDocList($jmoving_arr) { $db = DbSingleton::getDb();
        $arr = [];
        foreach ($jmoving_arr as $jmoving_id) {
            $r = $db->query("SELECT `doc_nom`, `prefix` FROM `J_MOVING` WHERE `id` = $jmoving_id LIMIT 1;");
            $doc_name = $db->result($r, 0, "prefix") . " - " . $db->result($r, 0, "doc_nom");
            array_push($arr, $doc_name);
        }
        $arr = array_unique($arr);
        return implode(", ", $arr);
    }

    function consolidateJmoving($storage_id) { $db = DbSingleton::getDb();
        session_start();
        $list = "";
        $media_user_id = $_SESSION["media_user_id"];
        $jmoving_arr = [];
        $where_jmoving = ""; $jmoving_selected = "";
        $r = $db->query("SELECT `jmoving_id` FROM `J_MOVING_USE` WHERE `user_id`='$media_user_id';");
        $nom = $db->num_rows($r);
        if ($nom > 0) {
            for ($i = 1; $i <= $nom; $i++) {
                $jmoving_id = $db->result($r, $i - 1, "jmoving_id");
                array_push($jmoving_arr, $jmoving_id);
            }
            $jmoving_str = implode(",", $jmoving_arr);
            $where_jmoving = "AND j.`id` IN ($jmoving_str)";
            $jmoving_selected = $this->getJmovingDocList($jmoving_arr);
        }

        $use_list = $this->getUserJmovingForm($storage_id, $jmoving_arr);

        $form = ""; $form_htm = RD . "/tpl/jmoving_consolidate_form.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $scan_form = ""; $scan_form_htm = RD . "/tpl/jmoving_scan_form.htm";
        if (file_exists("$scan_form_htm")) { $scan_form = file_get_contents($scan_form_htm); }
        $use_form = ""; $use_form_htm = RD . "/tpl/jmoving_use_form.htm";
        if (file_exists("$use_form_htm")) { $use_form = file_get_contents($use_form_htm); }

        $r = $db->query("SELECT js.*, j.prefix, j.doc_nom FROM `J_MOVING_STR` js 
            LEFT JOIN `J_MOVING` j ON j.id = js.jmoving_id
        WHERE j.`storage_id_to`='$storage_id' AND j.`status_jmoving` IN (48, 49) $where_jmoving;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $jmoving_str_id = $db->result($r, $i - 1, "id");
            $jmoving_cap = $db->result($r, $i - 1, "prefix") . " - " . $db->result($r, $i - 1, "doc_nom");
            $art_id = $db->result($r, $i - 1, "art_id");
            $article_nr_displ = $db->result($r, $i - 1, "article_nr_displ");
            $brand_id = $db->result($r, $i - 1, "brand_id");
            $brand_name = $this->getBrandName($brand_id);
            $article_name = $this->getArticleName($art_id);
            $amount = $db->result($r, $i - 1, "amount");
            $amount_barcodes = $db->result($r, $i - 1, "amount_barcodes");
            $amount_barcodes_noscan = $db->result($r, $i - 1, "amount_barcodes_noscan");
            $amount_bug = $db->result($r, $i - 1, "amount_bug");
            $dif_amount_barcodes = $amount - $amount_barcodes - $amount_barcodes_noscan - $amount_bug;
            if ($dif_amount_barcodes < 0) {
                $dif_amount_barcodes = 0;
            }
            $bug_list = $this->getJmovingBugListTrue($jmoving_str_id);
            $list .= "<tr>
                <td align='center'>$i</td>
                <td align='center'>$jmoving_cap</td>
                <td align='center'>$article_nr_displ</td>
                <td align='center'>$brand_name</td>
                <td align='left'>$article_name</td>
                <td align='center'>$amount</td>
                <td align='center'>$amount_barcodes</td>
                <td align='center'>$dif_amount_barcodes</td>
                <td align='center'>$amount_barcodes_noscan</td>
                <td align='center'>$amount_bug</td>
                <td>$bug_list</td>
            </tr>";
        }
        if ($nom == 0) {
            $scan_form = "";
        } else {
            $use_form = "";
        }
        $scan_form = str_replace("{articles_range}", $list, $scan_form);
        $scan_form = str_replace("{jmoving_selected}", $jmoving_selected, $scan_form);
        $use_form = str_replace("{jmoving_use_list}", $use_list, $use_form);
        $form = str_replace("{jmoving_scan_form}", $scan_form, $form);
        $form = str_replace("{jmoving_use_form}", $use_form, $form);
        $form = str_replace("{user_id}", $media_user_id, $form);
        return $form;
    }

    function clearJmovingUse($user_id) { $db = DbSingleton::getDb();
        $r = $db->query("SELECT * FROM `J_MOVING_USE` WHERE `user_id`='$user_id';");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $jmoving_id = $db->result($r, $i - 1, "jmoving_id");
            $db->query("UPDATE `J_MOVING` SET `user_use`=0 WHERE `id` = $jmoving_id LIMIT 1;");
        }
        $db->query("DELETE FROM `J_MOVING_USE` WHERE `user_id`='$user_id';");
        return true;
    }

    function saveUserJmoving($user_id, $jmoving_arr) { $db = DbSingleton::getDb();
        $answer = 0; $err = "Помилка вибору! Одне з переміщень уже використовується іншим користувачем!";
        if (!empty($jmoving_arr)) {
            foreach ($jmoving_arr as $jmoving_id) {
                $r = $db->query("SELECT `user_use` FROM `J_MOVING` WHERE `id` = $jmoving_id LIMIT 1;");
                $n = $db->num_rows($r);
                if ($n > 0) {
                    $user_use = $db->result($r, 0, "user_use");
                    if ($user_use != $user_id && $user_use != 0) {
                        $answer = 0; $err = "Помилка вибору! Одне з переміщень уже використовується іншим користувачем!";
                        break;
                    }
                }
                $r = $db->query("SELECT MAX(`id`) as maxim FROM `J_MOVING_USE` WHERE 1 LIMIT 1;");
                $max = $db->result($r, 0, "maxim") + 1;
                $db->query("INSERT INTO `J_MOVING_USE` (`id`, `user_id`, `jmoving_id`) VALUES ('$max', '$user_id', '$jmoving_id');");
                $db->query("UPDATE `J_MOVING` SET `user_use`='$user_id', `status_jmoving`=49 WHERE `id` = $jmoving_id LIMIT 1;");
                $answer = 1; $err = "";
            }
        }
        return array($answer, $err);
    }

    function saveConsolidateJmovingAcceptBarcodeForm($barcode) { $db = DbSingleton::getDb();
        session_start();
        $media_user_id = $_SESSION["media_user_id"];
        $slave = new slave; $storsel = new storsel();
        $barcode_info = ($barcode == "") ? "" : $storsel->getBarcodeInfo($barcode);
        $answer = 0; $err = "Помилка індексу! Штрих-коду '$barcode' ($barcode_info) немає у відборі";
        $barcode = $slave->qq($barcode);
        if ($barcode != "") {
            $art_id = $this->getArtIdByBarcode($barcode);
            $r = $db->query("SELECT js.id, js.jmoving_id, js.amount, js.amount_barcodes, js.amount_barcodes_noscan 
            FROM `J_MOVING_USE` ju 
                LEFT JOIN `J_MOVING_STR` js ON js.jmoving_id=ju.jmoving_id
            WHERE ju.`user_id`='$media_user_id' AND js.`art_id`='$art_id' AND js.`amount`>`amount_barcodes` LIMIT 1;");
            $n = $db->num_rows($r);
            if ($n == 1) {
                $id = $db->result($r, 0, "id");
                $amount = $db->result($r, 0, "amount");
                $amount_barcodes = $db->result($r, 0, "amount_barcodes");
                $amount_barcodes_noscan = $db->result($r, 0, "amount_barcodes_noscan");
                $ex = $amount_barcodes + $amount_barcodes_noscan;
                if ($amount > $ex) {
                    $amount_barcodes += 1;
                    $db->query("UPDATE `J_MOVING_STR` SET `amount_barcodes`='$amount_barcodes' WHERE `id`='$id' LIMIT 1;");
                    $answer = 1; $err = "";
                }
            }
        } else {
            $answer = 0; $err = "Помилка штрих-коду";
        }
        return array($answer, $err);
    }

}

function cmpJmStorages($a, $b) {
    if ($a["position"] == $b["position"]) return 0;
    return $a["position"] > $b["position"] ? 1 : -1;
}

function sortJmStyle($a, $b) {
    if ($a["style"] == $b["style"]) return 0;
    return $a["style"] < $b["style"] ? 1 : -1;
}
