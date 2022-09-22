<?php

class catalogue {

	public $kol_price_rating = 12;

    public function getTpointName($tpoint_id)
    {
        $db = DbSingleton::getDb();
        $name = "";
        $r = $db->query("SELECT `full_name` FROM `T_POINT` WHERE `id` = $tpoint_id LIMIT 1;");
        $n = (int)$db->num_rows($r);
        if ($n === 1) {
            $name = $db->result($r, 0, "full_name");
        }
        return $name;
    }

    public function getArticleID($artcile_nr_displ, $brand_id): array
    {
        $db = DbSingleton::getTokoDb();
        $art_id = 0; $status = 0;
        $article_nr_search = $this->clearArticle($artcile_nr_displ);
        $r = $db->query("SELECT `ART_ID` FROM `T2_ARTICLES` WHERE `ARTICLE_NR_SEARCH` = '$article_nr_search' AND `BRAND_ID` = $brand_id;");
        $n = (int)$db->num_rows($r);

        if ($n === 1) {
            $art_id = $db->result($r, 0, "ART_ID");
            $status = 1;
        }

        if ($n > 1) {
            $status = 2;
        }

        return array($status, $art_id);
    }

    public function getArticleBrandID($brand_name)
    {
        $db = DbSingleton::getTokoDb();
        $brand_id = 0;
        $r = $db->query("SELECT `BRAND_ID` FROM `T2_BRANDS` WHERE `BRAND_NAME`='$brand_name';");
        $n = (int)$db->num_rows($r);
        if ($n === 1) {
            $brand_id = $db->result($r, 0, "BRAND_ID");
        }
        return $brand_id;
    }

    public function getArticleBrandKind($brand_id)
    {
        $db = DbSingleton::getTokoDb();
        $kind = 0;
        $r = $db->query("SELECT `KIND` FROM `T2_BRANDS` WHERE `BRAND_ID` = $brand_id LIMIT 1;");
        $n = (int)$db->num_rows($r);
        if ($n > 0) {
            $kind = $db->result($r, 0, "KIND");
        }
        return $kind;
    }

    public function clearArticle($art): string
    {
        $art = str_replace(array(" ", "_", "-", ".", "+", "'", "/", '"'), "", $art);
        $art = preg_replace("/[^a-zA-ZА-Яа-я0-9\s]/", "", $art);
        $art = strtolower($art);

        return $art;
    }

    public function getMediaUserName($user_id)
    {
        $db = DbSingleton::getDb();
        $name = "";
        $r = $db->query("SELECT `name` FROM `media_users` WHERE `id` = $user_id LIMIT 1;");
        $n = (int)$db->num_rows($r);
        if ($n === 1) {
            $name = $db->result($r, 0, "name");
        }

        return $name;
    }

    public function getManualCap($id)
    {
        $db = DbSingleton::getDb();
        $r = $db->query("SELECT `mcaption` FROM `manual` WHERE `id` = $id;");
        return $db->result($r, 0, "mcaption");
    }

    public function getArticleJDocsData($doc_type, $doc_id, $art_id): array
    {
        $db = DbSingleton::getDb();
        $prixod = $rasxod = $dvigen = "";
        $name = $prefix = $color = $onclick = "";
        $storage_from = $storage_to = 0;
        $doc_type = (int)$doc_type;

        // INCOME
        if ($doc_type === 1) {
            $r = $db->query("SELECT j.storage_id, j.storage_cells_id, j.client_id, j.client_seller, j.prefix, j.doc_nom, js.amount 
            FROM `J_INCOME` j 
                LEFT OUTER JOIN `J_INCOME_STR` js ON (js.income_id = j.id)
            WHERE j.id = $doc_id AND js.art_id = $art_id;");
            $client_id      = $db->result($r, 0, "client_id"); $client_id = $this->getClientName($client_id);
            $client_seller  = $db->result($r, 0, "client_seller"); $client_seller = $this->getClientName($client_seller);
            $cell_to        = $db->result($r, 0, "storage_cells_id"); $cell_to = $this->getStorageCellName($cell_to);
            $storage_to     = $db->result($r, 0, "storage_id"); $storage_to = $this->getStorageName($storage_to) . " ($cell_to) - $client_id";
            $storage_from   = $client_seller;
            $amount         = $db->result($r, 0, "amount");
            $doc_nom        = $db->result($r, 0, "doc_nom");
            $prefix         = $db->result($r, 0, "prefix") . "-$doc_nom";
            $prixod         = $amount;
            $name           = "Прихідна накладна";
            $color          = "lightblue";
            $onclick        = "showIncomeCard('$doc_id');";
        }

        // MOVING
        if ($doc_type === 2) {
            $cell4 = "";
            $r = $db->query("SELECT j.storage_id_to, j.cell_id_to, j.type_id, j.prefix, j.doc_nom, js.storage_id_from, js.cell_id_from as cell_from, js.cell_id_to as cell_to, js.amount, js.select_id 
            FROM `J_MOVING` j
                LEFT OUTER JOIN `J_MOVING_STR` js on js.jmoving_id=j.id
            WHERE j.id = $doc_id AND js.art_id = $art_id;");
            $type       = $db->result($r, 0, "type_id");
            $select_id  = $db->result($r, 0, "select_id");

            $r2 = $db->query("SELECT `amount`, `cell_id_from` FROM `J_SELECT_STR` WHERE `select_id` = $select_id AND `art_id` = $art_id;");
            $n = (int)$db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $amount         = $db->result($r2, $i - 1, "amount");
                $cell_id_from   = $db->result($r2, $i - 1, "cell_id_from");
                $cell_id_from   = $this->getStorageCellName($cell_id_from);

                if ($n === 1) {
                    $cell4 .= $cell_id_from;
                } else {
                    $cell4 .= " $cell_id_from = $amount шт.;";
                }
            }

            $cell1 = $db->result($r, 0, "cell_from"); $cell1 = $this->getStorageCellName($cell1);
            $cell2 = $db->result($r, 0, "cell_to"); $cell2 = $this->getStorageCellName($cell2);
            $cell3 = $db->result($r, 0, "cell_id_to"); $cell3 = $this->getStorageCellName($cell3);

            if ($type) {
                $cell_to    = $cell3;
                $cell_from  = $cell4;
                $type       = "між складами";
            } else {
                $cell_to    = $cell2;
                $cell_from  = $cell1;
                $type       = "внутрішнє";
            }

            $storage_to     = $db->result($r, 0, "storage_id_to");
            $storage_to     = $this->getStorageName($storage_to) . " ($cell_to)";
            $storage_from   = $db->result($r, 0, "storage_id_from");
            $storage_from   = $this->getStorageName($storage_from) . " ($cell_from)";
            $amount         = $db->result($r, 0, "amount");
            $doc_nom        = $db->result($r, 0, "doc_nom");
            $prefix         = $db->result($r, 0, "prefix") . "-$doc_nom";
            $dvigen         = $amount;
            $name           = "Переміщення ($type)";
            $color          = "";
        }

        // SALE INVOICE
        if ($doc_type === 3) {
            $cells = ""; $type = "";
            $r = $db->query("SELECT j.doc_type_id, j.dp_id, j.client_id, j.prefix, j.doc_nom, js.amount, js.storage_id_from, js.cell_id_from 
            FROM `J_SALE_INVOICE` j
                LEFT OUTER JOIN `J_SALE_INVOICE_STR` js ON (js.invoice_id=j.id)
            WHERE j.id = $doc_id AND js.art_id = $art_id;");
            $n = (int)$db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $amount     = $db->result($r, $i - 1, "amount");
                $cell_from  = $db->result($r, $i - 1, "cell_id_from");
                $cell_from  = $this->getStorageCellName($cell_from);

                if ($n === 1) {
                    $cells = $cell_from;
                } else {
                    $cells .= " $cell_from = $amount шт.;";
                }

                $storage_from   = $db->result($r, $i - 1, "storage_id_from"); $storage_from = $this->getStorageName($storage_from)." ($cells)";
                $doc_type_id    = $db->result($r, $i - 1, "doc_type_id"); $type = $this->getManualCap($doc_type_id);
                $client_id      = $db->result($r, $i - 1, "client_id"); $client_id = $this->getClientName($client_id);
                $storage_to     = $client_id;
                $doc_nom        = $db->result($r, $i - 1, "doc_nom");
                $prefix         = $db->result($r, $i - 1, "prefix")."-$doc_nom";
                $rasxod         += $amount;
            }

            $name       = "Видаткова накладна ($type)";
            $color      = "lightgreen";
            $onclick    = "showSaleInvoiceCard('$doc_id');";
        }

        // BACK CLIENT
        if ($doc_type === 4) {
            $r = $db->query("SELECT j.storage_id, j.client_id, j.prefix, j.doc_nom, js.amount, j.cell_id 
            FROM `J_BACK_CLIENTS` j
                LEFT OUTER JOIN `J_BACK_CLIENTS_STR` js ON (js.back_id = j.id)
            WHERE j.id = $doc_id AND js.art_id = $art_id;");
            $client_id      = $db->result($r, 0, "client_id"); $client_id = $this->getClientName($client_id);
            $storage_from   = $client_id;
            $cell_id        = $db->result($r, 0, "cell_id"); $cell_to = $this->getStorageCellName($cell_id);
            $storage_to     = $db->result($r, 0, "storage_id"); $storage_to = $this->getStorageName($storage_to)." ($cell_to)";
            $amount         = $db->result($r, 0, "amount");
            $doc_nom        = $db->result($r, 0, "doc_nom");
            $prefix         = $db->result($r, 0, "prefix") . "-$doc_nom";
            $prixod         = $amount;
            $name           = "Повернення від клієнта";
            $color          = "pink";
            $onclick        = "showBackClientsCard('$doc_id');";
        }

        // WRITE OFF
        if ($doc_type === 6) {
            $cells = ""; $type = "";
            $r = $db->query("SELECT j.status_write_off, j.dp_id, j.client_id, j.prefix, j.doc_nom, js.amount, js.storage_id_from, js.cell_id_from 
            FROM `J_WRITE_OFF` j
                LEFT OUTER JOIN `J_WRITE_OFF_STR` js ON (js.write_off_id = j.id)
            WHERE j.id = $doc_id AND js.art_id = $art_id;");
            $n = (int)$db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $amount     = $db->result($r, $i - 1, "amount");
                $cell_from  = $db->result($r, $i - 1, "cell_id_from");
                $cell_from  = $this->getStorageCellName($cell_from);

                if ($n === 1) {
                    $cells = $cell_from;
                } else {
                    $cells .= " $cell_from = $amount шт.;";
                }
                $storage_from       = $db->result($r, $i - 1, "storage_id_from"); $storage_from = $this->getStorageName($storage_from) . " ($cells)";
                $status_write_off   = $db->result($r, $i - 1, "status_write_off"); $type = $this->getManualCap($status_write_off);
                $client_id          = $db->result($r, $i - 1, "client_id"); $client_id = $this->getClientName($client_id);
                $storage_to         = $client_id;
                $doc_nom            = $db->result($r, $i - 1, "doc_nom");
                $prefix             = $db->result($r, $i - 1, "prefix") . "-$doc_nom";
                $rasxod             += $amount;
            }

            $name       = "Списання ($type)";
            $color      = "lightyellow";
            $onclick    = "showWriteOffCard('$doc_id');";
        }

        return array($name, $prefix, $color, $prixod, $rasxod, $dvigen, $storage_from, $storage_to, $onclick);
    }

    public function showArticleJDocs($art_id): array
    {
        $db = DbSingleton::getDb();
        $form = "";
        if ($art_id > 0) {
            $list = "";
            $form_htm = RD . "/tpl/catalogue_history_moving.htm";
            if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
            $r = $db->query("SELECT `data`, `doc_type`, `doc_id`, SUM(`amount`) as sum_amount FROM `J_ART_DOCS` 
            WHERE `art_id` = $art_id GROUP BY `art_id`, `doc_id` ORDER BY `data` DESC;");
            $n = (int)$db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $data       = $db->result($r, $i - 1, "data");
                $doc_type   = $db->result($r, $i - 1, "doc_type");
                $doc_id     = $db->result($r, $i - 1, "doc_id");
                [$name, $prefix, $color, $prixod, $rasxod, $dvigen, $storage_from, $storage_to, $onclick] = $this->getArticleJDocsData($doc_type, $doc_id, $art_id);
                $summ       = $prixod - $rasxod;

                $list .= "
                <tr align='center' style='background:$color; cursor: pointer;' onclick=\"$onclick\">
                    <td>$i</td>
                    <td>$data</td>
                    <td>$name</td>
                    <td>$prefix</td>
                    <td>$storage_from</td>
                    <td>$storage_to</td>
                    <td>$prixod</td>
                    <td>$rasxod</td>
                    <td>$dvigen</td>
                    <td>$summ</td>
                </tr>";
            }

            $form = str_replace("{list}", $list, $form);
            [$article_nr_displ, , $brand_name] = $this->getArticleNrDisplBrand($art_id);
            $form = str_replace("{article_nr_displ}", $article_nr_displ . " " . $brand_name, $form);
        }

        return array($form, "Історія переміщення");
    }

    public function showCatNewArticle(): array
    {
        $form = ""; $form_htm = RD . "/tpl/catalogue_new_article_form.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $form = str_replace(array("{goods_list}", "{manuf_list}"), array($this->showGoodsGroupLetterListSelect(""), $this->showManufListSelect("")), $form);

        return array($form, "Генерування нового артиклу номенклатури");
    }

    public function findNewArtNextNum($brand, $group, $sub_group, $manuf)
    {
        $db = DbSingleton::getTokoDb();
        $ar = [];
        $brand = explode("-", $brand); $brand_key = $brand[1];
        $group = explode("-", $group); $group_key = $group[1];
        $sub_group = explode("-", $sub_group); $sub_group_key = $sub_group[1];
        $manuf = explode("-", $manuf); $manuf_key = $manuf[1];
        $ex = $brand_key . "" . $group_key . "" . $sub_group_key . "" . $manuf_key;

        $r = $db->query("SELECT `ARTICLE_NR_SEARCH` FROM `T2_ARTICLES` WHERE `ARTICLE_NR_SEARCH` LIKE '$ex%' ORDER BY `ARTICLE_NR_SEARCH` ASC;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $ar[$i] = substr($db->result($r, $i - 1, "ARTICLE_NR_SEARCH"), 0, 8);
        }
        $num = (int)substr($ar[$n], 4, 3);
        ++$num;

        if (strlen($num) === 1) {
            $num = "00" . $num;
        }
        if (strlen($num) === 2) {
            $num = "0" . $num;
        }

        return $num;
    }

    public function findNewArtID($brand)
    {
        $db = DbSingleton::getTokoDb();
        $adp_from = 1000;
        $adp_to = 10000000;
        $brand = explode("-", $brand);
        $brand_key = $brand[1];
        if ($brand_key === "T") {
            $adp_from = 100000000;
            $adp_to = 1000000000;
        }

        $r = $db->query("SELECT MAX(`ART_ID`) as mid FROM `T2_ARTICLES` WHERE `ART_ID` >= '$adp_from' AND `ART_ID` <= '$adp_to';");
        $mid = $db->result($r, 0, "mid");
        ++$mid;

        return $mid;
    }

    public function checkCatalogueNewArt($num, $art_id): array
    {
        $db = DbSingleton::getTokoDb();
        $answer = 0; $err = "Помилка обробки данних!";
        $r = $db->query("SELECT COUNT(`ART_ID`) as kol FROM `T2_ARTICLES` WHERE `ART_ID` = $art_id;");
        $art_ex = (int)$db->result($r, 0, "kol");
        $r = $db->query("SELECT COUNT(`ARTICLE_NR_SEARCH`) as kol FROM `T2_ARTICLES` WHERE `ARTICLE_NR_SEARCH` = $num;");
        $num_ex = (int)$db->result($r, 0, "kol");

        if ($num_ex === 0 && $art_ex === 0) {
            $answer = 1; $err = "";
        }
        if ($num_ex > 0) {
            $answer = 0; $err = "Індекс існує у базі";
        }
        if ($art_ex > 0) {
            $answer = 0; $err = "ART_ID існує у базі";
        }

        return array($answer, $err);
    }

    public function saveCatalogueNewArt($num, $art_id, $brand, $sub_group): array
    {
        $db = DbSingleton::getTokoDb();
        $new_art_id = "";
        $answer = 0; $err = "Помилка обробки данних!";
        $brand = explode("-", $brand); $brand_id = $brand[0];
        $sub_group = explode("-", $sub_group); $sub_group_id = $sub_group[0];

        if ($art_id !== "" && $num !== "" && $brand > 0 && $sub_group_id > 0) {
            $r = $db->query("SELECT COUNT(`ART_ID`) as kol FROM `T2_ARTICLES` WHERE `ART_ID` = $art_id;");
            $art_ex = (int)$db->result($r, 0, "kol");
            $r = $db->query("SELECT COUNT(`ARTICLE_NR_SEARCH`) as kol FROM `T2_ARTICLES` WHERE `ARTICLE_NR_SEARCH` = '$num';");
            $num_ex = (int)$db->result($r, 0, "kol");
            if ($num_ex > 0) {
                $answer = 0; $err = "Індекс існує у базі";
            }
            if ($art_ex > 0) {
                $answer = 0; $err = "ART_ID існує у базі";
            }
            if ($num_ex === 0 && $art_ex === 0) {
                $num_up = strtoupper($num);
                $db->query("INSERT INTO `T2_ARTICLES` (`ART_ID`, `ARTICLE_NR_DISPL`, `ARTICLE_NR_SEARCH`, `BRAND_ID`) VALUES ('$art_id', '$num_up', '$num_up', '$brand_id');");
                $db->query("INSERT INTO `T2_GOODS_GROUP` (`ART_ID`, `GOODS_GROUP_ID`) VALUES ('$art_id', '$sub_group_id');");
                $db->query("INSERT INTO `T2_CROSS` (`ART_ID`, `SEARCH_NUMBER`, `KIND`, `BRAND_ID`, `DISPLAY_NR`, `RELATION`) VALUES ('$art_id', '$num_up', '0', '$brand_id', '$num_up', '0');");
                $db->query("INSERT INTO `T2_NAMES` (`ART_ID`, `LANG_ID`, `NAME`, `INFO`) VALUES ('$art_id', '16', '" . $this->getGoodsGroupName($sub_group_id) . "', '');");
                $answer = 1; $err = "";
            }
        }

        return array($answer, $err, $new_art_id);
    }

    public function show_catalogue_range($art) {
        $form = ""; $form_htm = RD . "/tpl/catalogue.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $art = str_replace("--", "/", $art);
        [$header_list, $range_list, $list_brand_select] = $this->showArticlesSearchList($art, "", 0);

        $form = str_replace(array("{art}", "{header_list}", "{range_list}", "{list_brand_select}", "{fil4BrandList}", "{fil4SupplList}", "{fil4GoodsGroupList}", "{fil4Top}", "{fil4StokTo}", "{fil4StokFrom}", "{fil2ManufactureList}", "{fil2StrId}", "{fil2StrText}"), array($art, $header_list, $range_list, $list_brand_select, $this->showBrandListSelect(""), $this->showSupplListSelect(""), $this->showGoodsGroupListSelect(""), "", "", "", $this->showManufactureListSelect(""), "", ""), $form);

        return $form;
    }

    public function showCatFieldsViewForm(): array
    {
        $db = DbSingleton::getDb();
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $table_key = "catalogue";
        $list = ""; $lst = array();
        $form = ""; $form_htm = RD . "/tpl/catalogue_fields_view_form.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }

        $r = $db->query("SELECT `id`, `field_name`, `field_key` FROM `CFN_TABLE_FIELDS` WHERE `table_key` = '$table_key' ORDER BY `id` ASC;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r, $i - 1, "id");
            $field_name = $db->result($r, $i - 1, "field_name");
            $field_key = $db->result($r, $i - 1, "field_key");
            [$checked, $pos] = $this->checkCatalogueFieldsUserCheck($user_id, $table_key, $field_key);
            if (empty($pos)) {
                $pos = $i;
            }

            $lst[$pos] = "
            <tr id='usePos_".$id."'>
                <td><span class='glyphicon glyphicon-move'></span></td>
                <td>$i</td>
                <td>$field_name ($field_key)</td>
                <td>
                    <div class=\"switch\">
                        <div class=\"onoffswitch\">
                            <input type=\"checkbox\" $checked class=\"onoffswitch-checkbox\" id=\"use_$id\" value='1'>
                            <label class=\"onoffswitch-label\" for=\"use_$id\">
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

        $form = str_replace(array("{fields_list}", "{kol_fields}", "{table_key}"), array($list, $n, $table_key), $form);

        return array($form, "Налаштування відображення таблиці `Номенклатура`");
    }

    public function showCatFieldsViewDocForm(): array
    {
        $db = DbSingleton::getDb();
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $table_key = "catalogue_doc";
        $list = ""; $lst = array();
        $form = ""; $form_htm = RD . "/tpl/catalogue_fields_view_form.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }

        $r = $db->query("SELECT `id`, `field_name`, `field_key` FROM `CFN_TABLE_FIELDS` WHERE `table_key` = '$table_key' ORDER BY `id` ASC;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r, $i - 1, "id");
            $field_name = $db->result($r, $i - 1, "field_name");
            $field_key = $db->result($r, $i - 1, "field_key");
            [$checked, $pos] = $this->checkCatalogueFieldsUserCheck($user_id, $table_key, $field_key);
            if (empty($pos)) {
                $pos = $i;
            }

            $lst[$pos] = "
            <tr id='usePos_".$id."'>
                <td><span class='glyphicon glyphicon-move'></span></td>
                <td>$i</td>
                <td>$field_name ($field_key)</td>
                <td>
                    <div class=\"switch\">
                        <div class=\"onoffswitch\">
                            <input type=\"checkbox\" $checked class=\"onoffswitch-checkbox\" id=\"use_$id\" value='1'>
                            <label class=\"onoffswitch-label\" for=\"use_$id\">
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

        $form = str_replace(array("{fields_list}", "{kol_fields}", "{table_key}"), array($list, $n, $table_key), $form);

        return array($form, "Налаштування відображення таблиці `Номенклатура`");
    }

    public function checkCatalogueFieldsUserCheck($user_id, $table_key, $field_key): array
    {
        $db = DbSingleton::getDb();
        $ch = "checked"; $field_pos = 0;

        $r = $db->query("SELECT `field_active`, `field_pos` FROM `CFN_USERS_TABLE_CONFIG` 
        WHERE `table_key` = '$table_key' AND `user_id` = '$user_id' AND `field_key` = '$field_key' LIMIT 1;");
        $n = (int)$db->num_rows($r);

        if ($n === 1) {
            $field_active = (int)$db->result($r, 0, "field_active");
            if ($field_active === 0) {
                $ch = "";
            }
            $field_pos = $db->result($r, 0, "field_pos");
        }

        return array($ch, $field_pos);
    }

    public function saveCatalogueFieldsViewForm($kol_fields, $fl_id, $fl_ch, $table_key): array
    {
        $db = DbSingleton::getDb();
        $slave = new slave;
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $answer = 0; $err = "Помилка збереження даних!";
        if ($table_key === "") {
            $table_key = "catalogue";
        }
        $kol_fields = $slave->qq($kol_fields); $fl_id = $slave->qq($fl_id); $fl_ch = $slave->qq($fl_ch);

        if ($kol_fields > 0) {
            $db->query("DELETE FROM `CFN_USERS_TABLE_CONFIG` WHERE `user_id` = '$user_id' AND `table_key` = '$table_key';");
            for ($i = 1; $i <= $kol_fields; $i++) {
                $field_id = $fl_id[$i];
                $field_ch = $fl_ch[$i];
                [$field_name, $field_key] = $this->getFieldInfo($table_key, $field_id);
                $db->query("INSERT INTO `CFN_USERS_TABLE_CONFIG` (`user_id`,`table_key`,`field_name`,`field_key`,`field_active`,`field_pos`) 
                VALUES ('$user_id','$table_key','$field_name','$field_key','$field_ch','$i');");
            }
            $answer = 1; $err = "";
        }

        return array($answer, $err);
    }

    public function getFieldInfo($table_key, $field_id): array
    {
        $db = DbSingleton::getDb();
        $name = ""; $key = "";
        $r = $db->query("SELECT `field_name`, `field_key` FROM `CFN_TABLE_FIELDS` WHERE `table_key` = '$table_key' AND `id` = '$field_id' LIMIT 1;");
        $n = (int)$db->num_rows($r);

        if ($n === 1) {
            $name = $db->result($r, 0, "field_name");
            $key = $db->result($r, 0, "field_key");
        }

        return array($name, $key);
    }

    public function getCatalogueClientViewFieldsData($user_id, $table_key): array
    {
        $db = DbSingleton::getDb();
        if ($table_key === "") {
            $table_key = "catalogue";
        }
        $lst = array();

        $r = $db->query("SELECT `field_name`, `field_key` FROM `CFN_USERS_TABLE_CONFIG` 
        WHERE `table_key` = '$table_key' AND `user_id` = '$user_id' AND `field_active` = '1' ORDER BY `field_pos` ASC, `id` ASC;");
        $n = (int)$db->num_rows($r);
        if ($n === 0) {
            $r = $db->query("SELECT `field_name`, `field_key` FROM `CFN_TABLE_FIELDS` WHERE `table_key` = '$table_key' ORDER BY `id` ASC;");
            $n = (int)$db->num_rows($r);
        }
        if ($n > 0) {
            for ($i = 1; $i <= $n; $i++) {
                $field_name = $db->result($r, $i - 1, "field_name");
                $field_key  = $db->result($r, $i - 1, "field_key");
                $lst[$i]["field_name"]  = $field_name;
                $lst[$i]["field_key"]   = $field_key;
            }
        }

        return array($lst, $n);
    }

    public function showCatalogueBrandSelectList($r)
    {
        $db = DbSingleton::getDb();
        $slave = new slave;
        $list = "";
        $n = (int)$db->num_rows($r);
        $tkey = time();

        $db->query("CREATE TEMPORARY TABLE IF NOT EXISTS `NBRAND_RESULT_$tkey` (`art_id` INT NOT NULL ,`display_nr` VARCHAR( 100 ) NOT NULL ,`name` VARCHAR( 255 ) NOT NULL ,`brand_id` INT NOT NULL ,`brand_name` VARCHAR( 100 ) NOT NULL ,`kol_res` TINYINT NOT NULL) ENGINE = MYISAM ;");

        for ($i = 1; $i <= $n; $i++) {
            $art_id         = $db->result($r, $i - 1, "ART_ID");
            $display_nr     = $db->result($r, $i - 1, "DISPLAY_NR");
            $name           = $slave->qq($db->result($r, $i - 1, "NAME"));
            $brand_id       = $db->result($r, $i - 1, "BRAND_ID");
            $brand_name     = $slave->qq($db->result($r, $i - 1, "BRAND_NAME"));

            $db->query("INSERT INTO `NBRAND_RESULT_$tkey` VALUES ('$art_id','$display_nr','$name','$brand_id','$brand_name','0');");
        }

        $r = $db->query("SELECT * FROM `NBRAND_RESULT_$tkey` ORDER BY `kol_res` DESC;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $art_id         = $db->result($r, $i - 1, "art_id");
            $display_nr     = $db->result($r, $i - 1, "display_nr");
            $display_nr     = str_replace(" ", "", $display_nr);
            $name           = $db->result($r, $i - 1, "name");
            $brand_id       = $db->result($r, $i - 1, "brand_id");
            $brand_name     = $db->result($r, $i - 1, "brand_name");
            $brand_name     = str_replace(" ", "", $brand_name);
            $display_nr2    = str_replace("/", "--", $display_nr);
            $trans_display  = str_replace(array(" ", '"'), "-", $name);

            $list .= "
            <tr style='cursor:pointer;' onClick='location.href=\"/Catalogue/$display_nr2/$brand_id/$art_id/$display_nr-$brand_name-$trans_display\"'>
                <td>$display_nr</td>
                <td>$brand_name</td>
                <td>$name</td>
            </tr>";
        }

        $form = "";
        if ($n > 0) {
            $form_htm = RD . "/tpl/catalogue_brand_select_list.htm";
            if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
            $form = str_replace("{list}", $list, $form);
        }
        $db->query("DROP TEMPORARY TABLE IF EXISTS `NBRAND_RESULT_$tkey`;");

        return $form;
    }

    public function getArtID($art): array
    {
        $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT `ART_ID`, `BRAND_ID` FROM `T2_ARTICLES` WHERE `ARTICLE_NR_SEARCH` = '$art' LIMIT 1;");
        $art_id     = $db->result($r, 0, "ART_ID");
        $brand_id   = $db->result($r, 0, "BRAND_ID");

        return array($art_id, $brand_id);
    }

    public function getOEList($true_art_id): string
    {
        $db = DbSingleton::getTokoDb();
        $ak = array(); $rk = array();
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $list = ""; $range_list = ""; $search_str = "";
        $true_art_id = (int)$true_art_id;
        $query = "SELECT `SEARCH_NUMBER` FROM `T2_CROSS` WHERE `ART_ID` = $true_art_id AND `KIND` = 3 AND `RELATION` = 0 GROUP BY `SEARCH_NUMBER`;";
        $r = $db->query($query);
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $SEARCH_NUMBER = $db->result($r, $i - 1, "SEARCH_NUMBER");
            $search_str .= "'$SEARCH_NUMBER'";
            if ($i < $n) {
                $search_str .= ",";
            }
        }

        if ($search_str !== "") {
            $art_id_arr = [];

            $r = $db->query("SELECT `ART_ID` FROM `T2_ARTICLES` WHERE `ARTICLE_NR_SEARCH` IN ($search_str)");
            $n = (int)$db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $ART_ID     = $db->result($r, $i - 1, "ART_ID");
                $KIND       = 3;
                $RELATION   = 0;
                $art_id_arr[] = $ART_ID;

                if (($ak[$ART_ID] === "") || $KIND === 3) {
                    $ak[$ART_ID] = $KIND;
                }
                if (($rk[$ART_ID] === "") || $RELATION === 0) {
                    $rk[$ART_ID] = $RELATION;
                }
            }

            $art_id_arr = array_unique($art_id_arr);
            $art_id_str = implode(",", $art_id_arr);
            ($art_id_str !== "") ?: $art_id_str = 0;
            $art_id_str = str_replace("'", "", $art_id_str);

            $query = "SELECT t2a.ART_ID, t2a.BRAND_ID, t2a.ARTICLE_NR_DISPL, t2b.BRAND_NAME, t2n.NAME, t2n.INFO, t2bc.BARCODE, t2ic.CROSS as inner_cross, gg.NAME as goods_group_name, u.name as unit_name, cs.COSTUMS_CODE, cc.COUNTRY_NAME
            FROM `T2_ARTICLES` t2a 
                LEFT OUTER JOIN `T2_BRANDS` t2b on t2b.BRAND_ID=t2a.BRAND_ID 
                LEFT OUTER JOIN `T2_NAMES` t2n on t2n.ART_ID=t2a.ART_ID 
                LEFT OUTER JOIN `T2_BARCODES` t2bc on t2bc.ART_ID=t2a.ART_ID 
                LEFT OUTER JOIN `T2_INNER_CROSS` t2ic on t2ic.ART_ID=t2a.ART_ID 
                LEFT OUTER JOIN `T2_GOODS_GROUP` t2gg on t2gg.ART_ID=t2a.ART_ID 
                LEFT OUTER JOIN `GOODS_GROUP` gg on gg.ID=t2gg.GOODS_GROUP_ID 
                LEFT OUTER JOIN `T2_PACKAGING` t2p on t2p.ART_ID=t2a.ART_ID 
                LEFT OUTER JOIN `units` u on u.id=t2p.UNITS_ID 
                LEFT OUTER JOIN `T2_ZED` t2z on t2z.ART_ID=t2a.ART_ID 
                LEFT OUTER JOIN `T2_COUNTRIES` cc on cc.COUNTRY_ID=t2z.COUNTRY_ID 
                LEFT OUTER JOIN `T2_COSTUMS` cs on cs.COSTUMS_ID=t2z.COSTUMS_ID 	
            WHERE t2a.ART_ID IN ($art_id_str)";
            $r = $db->query($query);
            $n = (int)$db->num_rows($r);

            [$fldcnf, $kol_f] = $this->getCatalogueClientViewFieldsData($user_id, "catalogue");
            for ($i = 1; $i <= $kol_f; $i++) {
                $range_list .= "<td onClick='showCatalogueCard(\"{art_id}\")'>{".$fldcnf[$i]["field_key"]."}</td>";
            }
            $lst = array();

            for ($i = 1; $i <= $n; $i++) {
                $art_id = $db->result($r, $i - 1, "ART_ID");
                $kind_id = $ak[$art_id];
                $relation = $rk[$art_id];
                $article_nr_displ = $db->result($r, $i - 1, "ARTICLE_NR_DISPL");
                $brand_name = $db->result($r, $i - 1, "BRAND_NAME");
                $name = $db->result($r, $i - 1, "NAME");
                $info = $db->result($r, $i - 1, "INFO");
                $barcode = $db->result($r, $i - 1, "BARCODE");
                $inner_cross = $db->result($r, $i - 1, "inner_cross");
                $goods_group_name = $db->result($r, $i - 1, "goods_group_name");
                $unit_name = $db->result($r, $i - 1, "unit_name");
                $costums_code = $db->result($r, $i - 1, "COSTUMS_CODE");
                $country_name = $db->result($r, $i - 1, "COUNTRY_NAME");
                $lst[$i]["kind"] = $kind_id;
                $lst[$i]["relation"] = $relation;

                $check_photo = $this->checkPhotoEmpty($art_id);
                if ($check_photo > 0) {
                    $lst[$i]["data"] = "<tr style='cursor:pointer'>
                        <td class='text-center'><button class='btn btn-sm btn-default' onclick='showArtilceGallery(\"$art_id\",\"$article_nr_displ\")'><i class='fa fa-image'></i></button></td>
                        <td class='text-center'>{kind_name}</td>
                    " . $range_list . "</tr>";
                } else {
                    $lst[$i]["data"] = "<tr style='cursor:pointer'>
                        <td class='text-center'></td>
                        <td class='text-center'>{kind_name}</td>
                    " . $range_list . "</tr>";
                }

                $lst[$i]["data"] = str_replace("{art_id}", $art_id, $lst[$i]["data"]);
                $lst[$i]["data"] = str_replace("{article_nr_displ}", $article_nr_displ, $lst[$i]["data"]);
                $lst[$i]["data"] = str_replace("{brand_name}", $brand_name, $lst[$i]["data"]);
                $lst[$i]["data"] = str_replace("{name}", $name, $lst[$i]["data"]);
                $lst[$i]["data"] = str_replace("{info}", $info, $lst[$i]["data"]);
                $lst[$i]["data"] = str_replace("{barcode}", $barcode, $lst[$i]["data"]);
                $lst[$i]["data"] = str_replace("{art_id}", $art_id, $lst[$i]["data"]);
                $lst[$i]["data"] = str_replace("{inner_cross}", $inner_cross, $lst[$i]["data"]);
                $lst[$i]["data"] = str_replace("{goods_group_id}", $goods_group_name, $lst[$i]["data"]);
                $lst[$i]["data"] = str_replace("{units_id}", $unit_name, $lst[$i]["data"]);
                $lst[$i]["data"] = str_replace("{costums_id}", $costums_code, $lst[$i]["data"]);
                $lst[$i]["data"] = str_replace("{country_id}", $country_name, $lst[$i]["data"]);
            }

            $lst_kr = array();
            for ($i = 1; $i <= $n; $i++) {
                $lst_kr[1] .= $lst[$i]["data"];
            }
            $lst_kr[1] = str_replace("{kind_name}", "<i style=\"width: 100%; height: 60px;\" title=\"ОЕ\" class=\"fa fa-opera\"></i>", $lst_kr[1]);
            $list .= $lst_kr[1];
        }

        return $list;
    }

    public function showArticlesSearchList($art, $query_2, $search_type = 0): array
    {
        $db = DbSingleton::getTokoDb();
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $ak = $rk = [];
        $list2 = ""; $r = ""; $query = "";
        $search_type = (int)$search_type;

        [$true_art_id] = $this->getArtID($art);

        $link = gnLink;
        if (substr($link, -1) === "/") {
            $link = substr($link, 0, -1);
        }
        $links = explode("/", $link);
        $art = $this->clearArticle($art);
        $brand_id = $links[2];

        if ($query_2 === "" && $search_type === 0) {
            $n = 0;
            $where_brand = "";
            $group_brand = "GROUP BY t2c.BRAND_ID";

            if ($brand_id > 0) {
                $where_brand = " AND t2c.BRAND_ID = $brand_id";
                $group_brand = "";
            }

            if ($art !== "") {
                $query = "SELECT t2b.BRAND_NAME, IFNULL(t2n.NAME,'') as NAME, t2c.BRAND_ID, t2c.DISPLAY_NR, t2c.ART_ID, t2c.KIND, t2c.RELATION 
                FROM `T2_CROSS` t2c 
                     INNER JOIN `T2_BRANDS` t2b ON (t2b.BRAND_ID = t2c.BRAND_ID)
                     LEFT OUTER JOIN `T2_NAMES` t2n ON (t2n.ART_ID = t2c.ART_ID)
                WHERE t2c.SEARCH_NUMBER = '$art' $where_brand $group_brand 
                ORDER BY t2n.NAME ASC;";
                $r = $db->query($query);
                $n = (int)$db->num_rows($r);
            }

            $one_result = 0;
            if ($n > 1 && empty($brand_id)) {
                $where_brand = "";
                $list2 = $this->showCatalogueBrandSelectList($r);
            }

            if ($n === 1) {
                $query = "SELECT t2b.BRAND_NAME, IFNULL(t2n.NAME,'') as NAME, t2c.BRAND_ID, t2c.DISPLAY_NR, t2c.ART_ID, t2c.KIND, t2c.RELATION 
                FROM `T2_CROSS` t2c 
                     INNER JOIN `T2_BRANDS` t2b ON (t2b.BRAND_ID = t2c.BRAND_ID)
                     LEFT OUTER JOIN `T2_NAMES` t2n ON (t2n.ART_ID = t2c.ART_ID)
                WHERE (t2c.SEARCH_NUMBER = '$art' $where_brand) OR t2c.ART_ID = '$true_art_id' 
                ORDER BY t2n.NAME ASC;";
                $r = $db->query($query);
                $n = (int)$db->num_rows($r);
                $one_result = 1;
            }

            if (($n > 1 && !empty($brand_id)) || $one_result === 1) {
                $ak = [];
                $rk = [];
                $art_id_arr = [];
                $art_id = 0;

                if (!empty($true_art_id)) {
                    $art_id_arr[] = $true_art_id;
                }

                for ($i = 1; $i <= $n; $i++) {
                    $art_id     = (int)$db->result($r, $i - 1, "ART_ID");
                    $KIND       = (int)$db->result($r, $i - 1, "KIND");
                    $RELATION   = (int)$db->result($r, $i - 1, "RELATION");

                    $art_id_arr[] = $art_id;
//                    if (($ak[$ART_ID] === "") || $KIND === 0) {$ak[$ART_ID] = $KIND;}
//                    if (($rk[$ART_ID] === "") || $RELATION === 0) {$rk[$ART_ID] = $RELATION;}

                    if (empty($ak[$art_id]) || empty($KIND)) {
                        $ak[$art_id] = $KIND;
                    }

                    if (empty($ak[$art_id]) || empty($RELATION)) {
                        $ak[$art_id] = $RELATION;
                    }
                }

                $art_id_arr = array_unique($art_id_arr);
                $art_id_str = implode(",", $art_id_arr);
                $art_id_str = str_replace("'", "", $art_id_str);

                if (empty($true_art_id)) {
                    $true_art_id = $art_id;
                }

                $order_by = "";
                if (!empty($true_art_id)) {
                    $order_by = " ORDER BY t2a.ART_ID = $true_art_id DESC;";
                }

                $query = "SELECT t2a.ART_ID, t2a.BRAND_ID, t2a.ARTICLE_NR_DISPL, t2b.BRAND_NAME, IFNULL(t2n.NAME,'') as NAME, t2n.INFO, t2bc.BARCODE, t2ic.CROSS as inner_cross, gg.NAME as goods_group_name, u.name as unit_name, cs.COSTUMS_CODE, cc.COUNTRY_NAME
                FROM `T2_ARTICLES` t2a 
                    LEFT OUTER JOIN `T2_BRANDS` t2b on t2b.BRAND_ID = t2a.BRAND_ID 
                    LEFT OUTER JOIN `T2_NAMES` t2n on t2n.ART_ID = t2a.ART_ID 
                    LEFT OUTER JOIN `T2_BARCODES` t2bc on t2bc.ART_ID = t2a.ART_ID 
                    LEFT OUTER JOIN `T2_INNER_CROSS` t2ic on t2ic.ART_ID = t2a.ART_ID 
                    LEFT OUTER JOIN `T2_GOODS_GROUP` t2gg on t2gg.ART_ID = t2a.ART_ID 
                    LEFT OUTER JOIN `GOODS_GROUP` gg on gg.ID = t2gg.GOODS_GROUP_ID 
                    LEFT OUTER JOIN `T2_PACKAGING` t2p on t2p.ART_ID = t2a.ART_ID 
                    LEFT OUTER JOIN `units` u on u.id = t2p.UNITS_ID 
                    LEFT OUTER JOIN `T2_ZED` t2z on t2z.ART_ID = t2a.ART_ID 
                    LEFT OUTER JOIN `T2_COUNTRIES` cc on cc.COUNTRY_ID = t2z.COUNTRY_ID 
                    LEFT OUTER JOIN `T2_COSTUMS` cs on cs.COSTUMS_ID = t2z.COSTUMS_ID 	
                WHERE t2a.ART_ID in ($art_id_str) AND (CASE WHEN t2n.LANG_ID != NULL THEN t2n.LANG_ID = 16 ELSE TRUE END) 
                GROUP BY t2a.ART_ID
                $order_by";
            }
        }

        if ($query_2 === "" && $search_type === 1) {
            $query = "SELECT t2a.ART_ID, t2a.BRAND_ID, t2a.ARTICLE_NR_DISPL, t2b.BRAND_NAME, IFNULL(t2n.NAME,'') as NAME, t2n.INFO, t2bc.BARCODE, t2ic.CROSS as inner_cross, gg.NAME as goods_group_name, u.name as unit_name, cs.COSTUMS_CODE, cc.COUNTRY_NAME
            FROM `T2_ARTICLES` t2a 
                LEFT OUTER JOIN `T2_BRANDS` t2b on t2b.BRAND_ID=t2a.BRAND_ID 
                LEFT OUTER JOIN `T2_NAMES` t2n on t2n.ART_ID=t2a.ART_ID 
                LEFT OUTER JOIN `T2_BARCODES` t2bc on t2bc.ART_ID=t2a.ART_ID 
                LEFT OUTER JOIN `T2_INNER_CROSS` t2ic on t2ic.ART_ID=t2a.ART_ID 
                LEFT OUTER JOIN `T2_GOODS_GROUP` t2gg on t2gg.ART_ID=t2a.ART_ID 
                LEFT OUTER JOIN `GOODS_GROUP` gg on gg.ID=t2gg.GOODS_GROUP_ID 
                LEFT OUTER JOIN `T2_PACKAGING` t2p on t2p.ART_ID=t2a.ART_ID 
                LEFT OUTER JOIN `units` u on u.id=t2p.UNITS_ID 
                LEFT OUTER JOIN `T2_ZED` t2z on t2z.ART_ID=t2a.ART_ID 
                LEFT OUTER JOIN `T2_COUNTRIES` cc on cc.COUNTRY_ID=t2z.COUNTRY_ID 
                LEFT OUTER JOIN `T2_COSTUMS` cs on cs.COSTUMS_ID=t2z.COSTUMS_ID 
            WHERE t2a.ARTICLE_NR_SEARCH = '$art' OR t2a.ARTICLE_NR_DISPL = '$art';";
        }

        if ($query_2 === "" && $search_type === 2) {
            $query = "SELECT t2a.ART_ID, t2a.BRAND_ID, t2a.ARTICLE_NR_DISPL, t2b.BRAND_NAME, IFNULL(t2n.NAME,'') as NAME, t2n.INFO, t2bc.BARCODE, t2ic.CROSS as inner_cross, gg.NAME as goods_group_name, u.name as unit_name, cs.COSTUMS_CODE, cc.COUNTRY_NAME
            FROM `T2_ARTICLES` t2a 
                LEFT OUTER JOIN `T2_BRANDS` t2b on t2b.BRAND_ID=t2a.BRAND_ID 
                LEFT OUTER JOIN `T2_NAMES` t2n on t2n.ART_ID=t2a.ART_ID 
                LEFT OUTER JOIN `T2_BARCODES` t2bc on t2bc.ART_ID=t2a.ART_ID 
                LEFT OUTER JOIN `T2_INNER_CROSS` t2ic on t2ic.ART_ID=t2a.ART_ID 
                LEFT OUTER JOIN `T2_GOODS_GROUP` t2gg on t2gg.ART_ID=t2a.ART_ID 
                LEFT OUTER JOIN `GOODS_GROUP` gg on gg.ID=t2gg.GOODS_GROUP_ID 
                LEFT OUTER JOIN `T2_PACKAGING` t2p on t2p.ART_ID=t2a.ART_ID 
                LEFT OUTER JOIN `units` u on u.id=t2p.UNITS_ID 
                LEFT OUTER JOIN `T2_ZED` t2z on t2z.ART_ID=t2a.ART_ID 
                LEFT OUTER JOIN `T2_COUNTRIES` cc on cc.COUNTRY_ID=t2z.COUNTRY_ID 
                LEFT OUTER JOIN `T2_COSTUMS` cs on cs.COSTUMS_ID=t2z.COSTUMS_ID 
            WHERE t2bc.BARCODE = '$art';";
        }

        if ($query_2 === "" && $search_type === 3) {
            $query = "SELECT t2a.ART_ID, t2a.BRAND_ID, t2a.ARTICLE_NR_DISPL, t2b.BRAND_NAME, IFNULL(t2n.NAME,'') as NAME, t2n.INFO, t2bc.BARCODE, t2ic.CROSS as inner_cross, gg.NAME as goods_group_name, u.name as unit_name, cs.COSTUMS_CODE, cc.COUNTRY_NAME
            FROM `T2_ARTICLES` t2a 
                LEFT OUTER JOIN `T2_BRANDS` t2b on t2b.BRAND_ID=t2a.BRAND_ID 
                LEFT OUTER JOIN `T2_NAMES` t2n on t2n.ART_ID=t2a.ART_ID 
                LEFT OUTER JOIN `T2_BARCODES` t2bc on t2bc.ART_ID=t2a.ART_ID 
                LEFT OUTER JOIN `T2_INNER_CROSS` t2ic on t2ic.ART_ID=t2a.ART_ID 
                LEFT OUTER JOIN `T2_GOODS_GROUP` t2gg on t2gg.ART_ID=t2a.ART_ID 
                LEFT OUTER JOIN `GOODS_GROUP` gg on gg.ID=t2gg.GOODS_GROUP_ID 
                LEFT OUTER JOIN `T2_PACKAGING` t2p on t2p.ART_ID=t2a.ART_ID 
                LEFT OUTER JOIN `units` u on u.id=t2p.UNITS_ID 
                LEFT OUTER JOIN `T2_ZED` t2z on t2z.ART_ID=t2a.ART_ID 
                LEFT OUTER JOIN `T2_COUNTRIES` cc on cc.COUNTRY_ID=t2z.COUNTRY_ID 
                LEFT OUTER JOIN `T2_COSTUMS` cs on cs.COSTUMS_ID=t2z.COSTUMS_ID 
            WHERE t2a.ART_ID = '$art';";
        }

        if ($query_2 !== "") {
            $query = $query_2;
        }

        $r = $db->query($query);
        $n = (int)$db->num_rows($r);

        $list = "";
        $header_list = "";
        $range_list = "";

        if ($query_2 !== "" || $list2 === "") {
            // сработал внешний фильр или основной поиск с выбором бренда
            [$fldcnf, $kol_f] = $this->getCatalogueClientViewFieldsData($user_id, "catalogue");
            for ($i = 1; $i <= $kol_f; $i++) {
                $header_list .= "<th>" . $fldcnf[$i]["field_name"] . "</th>";
                $range_list .= "<td onClick='showCatalogueCard(\"{art_id}\")'>{" . $fldcnf[$i]["field_key"] . "}</td>";
            }
            $header_list = "<tr align='center'><th data-sortable=\"false\">Фото</th><th data-sortable=\"false\">Тип артикула</th>" . $header_list . "</tr>";
            $lst = array();

            for ($i = 1; $i <= $n; $i++) {
                $art_id             = (int)$db->result($r, $i - 1, "ART_ID");
                $kind_id            = $ak[$art_id];
                $relation           = $rk[$art_id];
                $article_nr_displ   = $db->result($r, $i - 1, "ARTICLE_NR_DISPL");
                $brand_name         = $db->result($r, $i - 1, "BRAND_NAME");
                $name               = $db->result($r, $i - 1, "NAME");
                $info               = $db->result($r, $i - 1, "INFO");
                $barcode            = $db->result($r, $i - 1, "BARCODE");
                $inner_cross        = $db->result($r, $i - 1, "inner_cross");
                $goods_group_name   = $db->result($r, $i - 1, "goods_group_name");
                $unit_name          = $db->result($r, $i - 1, "unit_name");
                $costums_code       = $db->result($r, $i - 1, "COSTUMS_CODE");
                $country_name       = $db->result($r, $i - 1, "COUNTRY_NAME");

                $lst[$i]["kind"] = $kind_id;
                $lst[$i]["relation"] = $relation;
                $check_photo = $this->checkPhotoEmpty($art_id);

                if ($check_photo > 0) {
                    $lst[$i]["data"] = "
                    <tr style='cursor:pointer'>
                    <td class='text-center'><button class='btn btn-sm btn-default' onclick='showArtilceGallery(\"$art_id\",\"$article_nr_displ\")'><i class='fa fa-image'></i></button></td>
                    <td class='text-center'>{kind_name}</td>
                    " . $range_list . "</tr>";
                } else {
                    $lst[$i]["data"] = "
                    <tr style='cursor:pointer'>
                    <td class='text-center'></td>
                    <td class='text-center'>{kind_name}</td>
                    " . $range_list . "</tr>";
                }

                $lst[$i]["data"] = str_replace("{art_id}", $art_id, $lst[$i]["data"]);
                $lst[$i]["data"] = str_replace("{article_nr_displ}", $article_nr_displ, $lst[$i]["data"]);
                $lst[$i]["data"] = str_replace("{brand_name}", $brand_name, $lst[$i]["data"]);
                $lst[$i]["data"] = str_replace("{name}", $name, $lst[$i]["data"]);
                $lst[$i]["data"] = str_replace("{info}", $info, $lst[$i]["data"]);
                $lst[$i]["data"] = str_replace("{barcode}", $barcode, $lst[$i]["data"]);
                $lst[$i]["data"] = str_replace("{art_id}", $art_id, $lst[$i]["data"]);
                $lst[$i]["data"] = str_replace("{inner_cross}", $inner_cross, $lst[$i]["data"]);
                $lst[$i]["data"] = str_replace("{goods_group_id}", $goods_group_name, $lst[$i]["data"]);
                $lst[$i]["data"] = str_replace("{units_id}", $unit_name, $lst[$i]["data"]);
                $lst[$i]["data"] = str_replace("{costums_id}", $costums_code, $lst[$i]["data"]);
                $lst[$i]["data"] = str_replace("{country_id}", $country_name, $lst[$i]["data"]);
            }

            $lst_kr = array();
            for ($i = 1; $i <= $n; $i++) {
                $kind       = $lst[$i]["kind"];
                $relation   = $lst[$i]["relation"];

                if ($kind === 0 && $relation === 0) { $lst_kr[1] .= $lst[$i]["data"]; }
                if ($kind === 1 && $relation === 0) { $lst_kr[2] .= $lst[$i]["data"]; }
                if (($kind === 3 || $kind === 4) && $relation === 0) { $lst_kr[2] .= $lst[$i]["data"]; }
                if (($kind === 3 || $kind === 4) && $relation === 1) { $lst_kr[3] .= $lst[$i]["data"]; }
                if (($kind === 3 || $kind === 4) && $relation === 2) { $lst_kr[4] .= $lst[$i]["data"]; }
                if ($kind === 0 || $relation === 0) { $lst_kr[5] .= $lst[$i]["data"]; }
            }

            if ($lst_kr[1] !== "") {
                $lst_kr[1] = str_replace("{kind_name}", "<i style=\"width: 100%;height: 60px;\" title=\"запитаний артикул\" class=\"fa fa-key\"></i>", $lst_kr[1]);
                $list .= $lst_kr[1];
            }

            if ($lst_kr[2] !== "") {
                $lst_kr[2] = str_replace("{kind_name}", "<i style=\"width: 100%;height: 60px;\" title=\"аналог\" class=\"fa fa-link\"></i>", $lst_kr[2]);
                $list .= $lst_kr[2];
            }

            if ($lst_kr[3] !== "") {
                $lst_kr[3] = str_replace("{kind_name}", "<i style=\"width: 100%;height: 60px;\" title=\"артикул присутні в \" class=\"fa fa-level-down\"></i>", $lst_kr[3]);
                $list .= $lst_kr[3];
            }

            if ($lst_kr[4] !== "") {
                $lst_kr[4] = str_replace("{kind_name}", "<i style=\"width: 100%;height: 60px;\" title=\"артикул включає в себе\" class=\"fa fa-level-up\"></i>", $lst_kr[4]);
                $list .= $lst_kr[4];
            }

            if ($lst_kr[5] !== "") {
                $lst_kr[5] = str_replace("{kind_name}", "<i style=\"width: 100%;height: 60px;\" title=\"інше\" class=\"fa fa-ellipsis-h\"></i>", $lst_kr[5]);
                $list .= $lst_kr[5];
            }

            $list .= $this->getOEList($true_art_id);
        }

        return array($header_list, $list, $list2);
    }

    public function getGoodsGroupName($gg_id)
    {
        $db = DbSingleton::getTokoDb();
        $name = "";
        $r = $db->query("SELECT `NAME` FROM `GOODS_GROUP` WHERE `ID` = $gg_id LIMIT 1;");
        $n = (int)$db->num_rows($r);
        if ($n === 1) {
            $name = $db->result($r, 0, "NAME");
        }

        return $name;
    }

    public function listSubGoodsGroup($gg_id): string
    {
        $db = DbSingleton::getTokoDb();
        $list = "";
        $r = $db->query("SELECT `ID` FROM `GOODS_GROUP` WHERE `PARRENT_ID` = $gg_id ORDER BY `KEY` ASC, `ID` ASC;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r, $i - 1, "ID");
            $list .= "'$id'," . $this->listSubGoodsGroup($id);
        }

        return $list;
    }

    public function showArticlesFil4SearchList($brand_id, $goods_group_id): array
    {
        $where_brand = "";
        $where_goods_group = "";

        if ($brand_id !== "") {
            foreach ($brand_id as $brnd_id) {
                $where_brand .= "'$brnd_id',";
            }
            $where_brand = " AND t2a.BRAND_ID IN (" . substr($where_brand, 0, -1) . ") ";
        }

        if ($goods_group_id !== "") {
            foreach ($goods_group_id as $gg_id) {
                $where_goods_group .= "'$gg_id'," . $this->listSubGoodsGroup($gg_id);
            }
            $where_goods_group = " AND t2gg.GOODS_GROUP_ID IN (" . substr($where_goods_group, 0, -1) . ") ";
        }

        $query = "SELECT t2a.ART_ID, t2a.BRAND_ID, t2a.ARTICLE_NR_DISPL, t2b.BRAND_NAME, t2n.NAME, t2n.INFO, t2bc.BARCODE, t2ic.CROSS as inner_cross, gg.NAME as goods_group_name, u.name as unit_name, cs.COSTUMS_CODE, cc.COUNTRY_NAME
        FROM `T2_ARTICLES` t2a 
            LEFT OUTER JOIN `T2_BRANDS` t2b on t2b.BRAND_ID=t2a.BRAND_ID 
            LEFT OUTER JOIN `T2_NAMES` t2n on t2n.ART_ID=t2a.ART_ID 
            LEFT OUTER JOIN `T2_BARCODES` t2bc on t2bc.ART_ID=t2a.ART_ID 
            LEFT OUTER JOIN `T2_INNER_CROSS` t2ic on t2ic.ART_ID=t2a.ART_ID 
            LEFT OUTER JOIN `T2_GOODS_GROUP` t2gg on t2gg.ART_ID=t2a.ART_ID 
            LEFT OUTER JOIN `GOODS_GROUP` gg on gg.ID=t2gg.GOODS_GROUP_ID 
            LEFT OUTER JOIN `T2_PACKAGING` t2p on t2p.ART_ID=t2a.ART_ID 
            LEFT OUTER JOIN `units` u on u.id=t2p.UNITS_ID 
            LEFT OUTER JOIN `T2_ZED` t2z on t2z.ART_ID=t2a.ART_ID 
            LEFT OUTER JOIN `T2_COUNTRIES` cc on cc.COUNTRY_ID=t2z.COUNTRY_ID 
            LEFT OUTER JOIN `T2_COSTUMS` cs on cs.COSTUMS_ID=t2z.COSTUMS_ID 
        WHERE 1 $where_brand $where_goods_group LIMIT 0,3000;";

        [$header_list, $list, $list_brand_select] = $this->showArticlesSearchList("", $query);

        return array($header_list, $list, $list_brand_select);
    }

    public function showArticlesFil2SearchList($art_ids): array
    {
        $where_artds = "";

        if ($art_ids !== "") {
            $where_artds = " AND t2a.ART_ID IN (" . $art_ids . ") ";
        }

        $query = "SELECT t2a.ART_ID, t2a.BRAND_ID, t2a.ARTICLE_NR_DISPL, t2b.BRAND_NAME, t2n.NAME, t2n.INFO, t2bc.BARCODE, t2ic.CROSS as inner_cross, gg.NAME as goods_group_name, u.name as unit_name, cs.COSTUMS_CODE, cc.COUNTRY_NAME
        FROM `T2_ARTICLES` t2a 
            LEFT OUTER JOIN `T2_BRANDS` t2b on t2b.BRAND_ID=t2a.BRAND_ID 
            LEFT OUTER JOIN `T2_NAMES` t2n on t2n.ART_ID=t2a.ART_ID 
            LEFT OUTER JOIN `T2_BARCODES` t2bc on t2bc.ART_ID=t2a.ART_ID 
            LEFT OUTER JOIN `T2_INNER_CROSS` t2ic on t2ic.ART_ID=t2a.ART_ID 
            LEFT OUTER JOIN `T2_GOODS_GROUP` t2gg on t2gg.ART_ID=t2a.ART_ID 
            LEFT OUTER JOIN `GOODS_GROUP` gg on gg.ID=t2gg.GOODS_GROUP_ID 
            LEFT OUTER JOIN `T2_PACKAGING` t2p on t2p.ART_ID=t2a.ART_ID 
            LEFT OUTER JOIN `units` u on u.id=t2p.UNITS_ID 
            LEFT OUTER JOIN `T2_ZED` t2z on t2z.ART_ID=t2a.ART_ID 
            LEFT OUTER JOIN `T2_COUNTRIES` cc on cc.COUNTRY_ID=t2z.COUNTRY_ID 
            LEFT OUTER JOIN `T2_COSTUMS` cs on cs.COSTUMS_ID=t2z.COSTUMS_ID 
        WHERE 1 $where_artds LIMIT 0,3000;";

        [$header_list, $list, $list_brand_select] = $this->showArticlesSearchList("", $query);

        return array($header_list, $list, $list_brand_select);
    }

    public function getArticleOperPriceGeneralStock($art_id): array
    {
        $db = DbSingleton::getTokoDb();
        $oper_price = 0; $general_stock = 0;
        $r = $db->query("SELECT `OPER_PRICE`, `GENERAL_STOCK` FROM `T2_ARTICLES_PRICE_STOCK` WHERE `ART_ID` = $art_id LIMIT 1;");
        $n = (int)$db->num_rows($r);
        if ($n === 1) {
            $oper_price = $db->result($r, 0, "OPER_PRICE");
            $general_stock = $db->result($r, 0, "GENERAL_STOCK");
        }

        return array($oper_price, $general_stock);
    }

    public function setArticleOperPriceGeneralStock($art_id, $new_oper_price, $new_general_stock): bool
    {
        $db = DbSingleton::getTokoDb(); $dbm = DbSingleton::getDb();
        $r = $db->query("SELECT `ID` FROM `T2_ARTICLES_PRICE_STOCK` WHERE `ART_ID` = $art_id LIMIT 1;");
        $n = (int)$db->num_rows($r);
        if ($n === 1) {
            $db->query("UPDATE `T2_ARTICLES_PRICE_STOCK` SET `OPER_PRICE` = '$new_oper_price', `GENERAL_STOCK` = '$new_general_stock' WHERE `ART_ID` = $art_id;");
        }
        if ($n === 0) {
            $db->query("INSERT INTO `T2_ARTICLES_PRICE_STOCK` (`ART_ID`, `OPER_PRICE`, `GENERAL_STOCK`) VALUES ($art_id, '$new_oper_price', '$new_general_stock');");
        }
        // TOKO ACTIONS
        $dbm->query("UPDATE `ACTION_CLIENTS` SET `status_update` = 1 WHERE `art_id` = $art_id AND `oper_price` != '$new_oper_price';");

        return true;
    }

    public function getArticleNrDisplBrand($art_id): array
    {
        $db = DbSingleton::getTokoDb();
        $article_nr_displ = $article_nr_search = $brand_name = "";
        $brand_id = 0;
        $r = $db->query("SELECT t2a.ART_ID, t2a.BRAND_ID, t2a.ARTICLE_NR_DISPL, t2a.ARTICLE_NR_SEARCH, t2b.BRAND_NAME 
        FROM `T2_ARTICLES` t2a  
            LEFT JOIN `T2_BRANDS` t2b ON (t2b.BRAND_ID = t2a.BRAND_ID) 
        WHERE t2a.ART_ID = $art_id LIMIT 1;");
        $n = (int)$db->num_rows($r);
        if ($n === 1) {
            $brand_id           = $db->result($r, 0, "BRAND_ID");
            $article_nr_displ   = $db->result($r, 0, "ARTICLE_NR_DISPL");
            $brand_name         = $db->result($r, 0, "BRAND_NAME");
            $article_nr_search  = $db->result($r, 0, "ARTICLE_NR_SEARCH");
        }

        return array($article_nr_displ, $brand_id, $brand_name, $article_nr_search);
    }

    public function getArticleName($art_id)
    {
        $db = DbSingleton::getTokoDb();
        $name = "";
        $r = $db->query("SELECT `NAME` FROM `T2_NAMES` WHERE `ART_ID` = $art_id ORDER BY `LANG_ID` ASC LIMIT 1;");
        $n = (int)$db->num_rows($r);
        if ($n === 1) {
            $name = $db->result($r, 0, "NAME");
        }
        return $name;
    }

    public function getArticleNameLang($art_id)
    {
        $db = DbSingleton::getTokoDb();
        $name = "";
        $r = $db->query("SELECT `NAME` FROM `T2_NAMES` WHERE `ART_ID` = $art_id AND `LANG_ID` = 41 LIMIT 1;");
        $n = (int)$db->num_rows($r);
        if ($n === 1) {
            $name = $db->result($r, 0, "NAME");
        } else {
            $r = $db->query("SELECT `NAME` FROM `T2_NAMES` WHERE `ART_ID` = $art_id AND `LANG_ID` = 16 LIMIT 1;");
            $n = (int)$db->num_rows($r);
            if ($n === 1) {
                $name = $db->result($r, 0, "NAME");
            }
        }
        return $name;
    }

    public function getArtNameUkr($art_id)
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

    public function getUniqueNumber($art_id)
    {
        $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT `UNIV_NUMBER` FROM `T2_ARTICLES_UNIV_NUMBER` WHERE `ART_ID` = $art_id LIMIT 1;");
        return $db->result($r, 0, "UNIV_NUMBER");
    }

    public function getArticleStatusExport($art_id): bool
    {
        $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT COUNT(`ID`) as count_arts FROM `T2_ARTICLES_NOT_EXPORT` WHERE `ART_ID` = $art_id;");
        $n = $db->result($r, 0, "count_arts");
        return ($n > 0);
    }

    public function showCatalogueCard($art_sel_id): array
    {
        $db = DbSingleton::getTokoDb();
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $user_name = $_SESSION["user_name"];
        $article_nr_displ = "";
        $form = ""; $form_htm = RD . "/tpl/catalogue_card.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }

        $r = $db->query("SELECT t2a.ART_ID, t2a.BRAND_ID, t2a.ARTICLE_NR_DISPL, t2n.INFO, t2bc.BARCODE, t2ic.CROSS as INNER_CROSS, t2gg.GOODS_GROUP_ID, gg.NAME as GOODS_GROUP_NAME,
        IFNULL(t2n.NAME, '') as NAME, 
        IFNULL(t2ps.OPER_PRICE, 0) as OPER_PRICE, 
        IFNULL(t2ps.GENERAL_STOCK, 0) as GENERAL_STOCK,
        CASE WHEN (t2n.LANG_ID != 16) THEN '' ELSE t2n.NAME END as NAME2
        FROM `T2_ARTICLES` t2a 
            LEFT OUTER JOIN `T2_NAMES` t2n on (t2n.ART_ID = t2a.ART_ID)
            LEFT OUTER JOIN `T2_BARCODES` t2bc on (t2bc.ART_ID = t2a.ART_ID)
            LEFT OUTER JOIN `T2_INNER_CROSS` t2ic on (t2ic.ART_ID = t2a.ART_ID)
            LEFT OUTER JOIN `T2_GOODS_GROUP` t2gg on (t2gg.ART_ID = t2a.ART_ID)
            LEFT OUTER JOIN `GOODS_GROUP` gg ON (gg.ID = t2gg.GOODS_GROUP_ID) 
            LEFT OUTER JOIN `T2_ARTICLES_PRICE_STOCK` t2ps on (t2ps.ART_ID = t2a.ART_ID) 
        WHERE t2a.ART_ID = $art_sel_id AND (CASE WHEN t2n.LANG_ID != NULL THEN t2n.LANG_ID = 16 ELSE TRUE END) 
        ORDER BY t2n.LANG_ID ASC LIMIT 1;");
        $n = (int)$db->num_rows($r);

        if ($n === 0) {
            $form_htm = RD . "/tpl/access_deny.htm";
            if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        }

        if ($n === 1) {
            $art_id             = $db->result($r, 0, "ART_ID");
            $article_nr_displ   = $db->result($r, 0, "ARTICLE_NR_DISPL");
            $brand_id           = $db->result($r, 0, "BRAND_ID");
            $article_name2      = $db->result($r, 0, "NAME2");
            $article_info       = $db->result($r, 0, "INFO");
            $barcode            = $db->result($r, 0, "BARCODE");
            $inner_cross        = $db->result($r, 0, "INNER_CROSS");
            $goods_group_id     = $db->result($r, 0, "GOODS_GROUP_ID");
            $goods_group_name   = $db->result($r, 0, "GOODS_GROUP_NAME");
            $oper_price         = $db->result($r, 0, "OPER_PRICE");
            $general_stock      = $db->result($r, 0, "GENERAL_STOCK");
            $unnumber           = $this->getUniqueNumber($art_id);
            $article_name_ukr   = $this->getArtNameUkr($art_id);

            $form = str_replace("{art_id}", $art_id, $form);
            $form = str_replace("{article_nr_displ}", $article_nr_displ, $form);
            $form = str_replace("{barcode}", $barcode, $form);
            $form = str_replace("{inner_cross}", $inner_cross, $form);
            $form = str_replace("{brand_id}", $brand_id, $form);
            $form = str_replace("{brand_list}", $this->showBrandListSelect($brand_id), $form);
            $form = str_replace("{goods_group_id}", $goods_group_id, $form);
            $form = str_replace("{goods_group_name}", $goods_group_name, $form);
            $form = str_replace("{article_name}", $article_name2, $form);
            $form = str_replace("{article_name_ukr}", $article_name_ukr, $form);
            $form = str_replace("{article_info}", $article_info, $form);
            $form = str_replace("{general_stock}", $general_stock, $form);
            $form = str_replace("{oper_price}", $oper_price, $form);
            $form = str_replace("{my_user_id}", $user_id, $form);
            $form = str_replace("{my_user_name}", $user_name, $form);
            $form = str_replace("{unique_number}", $unnumber, $form);
            $form = str_replace("{price_export_status}", $this->getArticleStatusExport($art_id) ? "checked" : "", $form);
        }

        return array($form, $article_nr_displ);
    }

    public function showArticleCross($art_id_sel)
    {
        $db = DbSingleton::getTokoDb();
        $list = "";
        $form = ""; $form_htm = RD . "/tpl/catalogue_cross_form.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $r = $db->query("SELECT `CROSS` FROM `T2_INNER_CROSS` WHERE `ART_ID` = $art_id_sel LIMIT 1;");
        $cross = $db->result($r, 0, "CROSS");
        $r = $db->query("SELECT `ART_ID` FROM `T2_INNER_CROSS` WHERE `CROSS` = '$cross';");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $art_id = $db->result($r, $i - 1, "ART_ID");
            [$article_nr_displ, , $brand_name] = $this->getArticleNrDisplBrand($art_id);
            [$amount, $reserv_amount] = $this->getArticlesStorage($art_id);

            $list .= "<tr>
                <td>$i</td>
                <td>$art_id</td>
                <td>$article_nr_displ</td>
                <td>$brand_name</td>
                <td>$amount</td>
                <td>$reserv_amount</td>
            </tr>";
        }

        $form = str_replace(array("{cross_range}", "{cross_value}"), array($list, $cross), $form);

        return $form;
    }

    public function saveArticleCross($cross, $new_cross): array
    {
        $db = DbSingleton::getTokoDb();
        $answer = 0; $err = "Помилка збереження даних!";
        if ($cross !== "") {
            $db->query("UPDATE `T2_INNER_CROSS` SET `CROSS` = '$new_cross' WHERE `CROSS` = '$cross';");
            $answer = 1; $err = "";
        }

        return array($answer, $err);
    }

    public function getArticlesStorage($art_id): array
    {
        $db = DbSingleton::getTokoDb();
        $full_amount = $full_reserv_amount = 0;
        $r = $db->query("SELECT `AMOUNT`, `RESERV_AMOUNT` FROM `T2_ARTICLES_STRORAGE` WHERE `ART_ID` = $art_id;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $amount = $db->result($r, $i - 1, "AMOUNT");
            $reserv = $db->result($r, $i - 1, "RESERV_AMOUNT");
            $full_amount += $amount;
            $full_reserv_amount += $reserv;
        }

        return array($full_amount, $full_reserv_amount);
    }

    public function generateBarcode()
    {
        $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT MAX(`BARCODE`) as mid FROM `T2_BARCODES`;");
        return 0 + $db->result($r, 0, "mid") + 1;
    }

    public function saveBarcode($art_id, $barcode): array
    {
        $db = DbSingleton::getTokoDb();
        $answer = 0; $err = "Помилка збереження даних!";
        $r = $db->query("SELECT `ART_ID` FROM `T2_BARCODES` WHERE `BARCODE` = '$barcode' LIMIT 1;");
        $n = (int)$db->num_rows($r);

        if ($n === 0 || $barcode === "") {
            if ($art_id > 0 && (strlen($barcode) === 6 || $barcode === "")) {
                $r1 = $db->query("SELECT `BARCODE` FROM `T2_BARCODES` WHERE `ART_ID` = $art_id LIMIT 1;");
                $n1 = (int)$db->num_rows($r1);
                if ($n1 === 0) {
                    $db->query("INSERT INTO `T2_BARCODES` (`ART_ID`, `BARCODE`) VALUES ($art_id, '$barcode');");
                }
                if ($n1 === 1) {
                    $barcode_db = $db->result($r, 0, "BARCODE");
                    if ($barcode_db !== $barcode || $barcode === "") {
                        $db->query("UPDATE `T2_BARCODES` SET `BARCODE` = '$barcode' WHERE `ART_ID` = $art_id;");
                    }
                }
                $answer = 1; $err = "";
            }
        }

        return array($answer, $err);
    }

    public function saveCatalogueGeneralInfo($art_id, $article_nr_displ, $inner_cross, $brand_id, $goods_group_id, $article_name, $article_info, $article_name_ukr, $unique_number, $export_status): array
    {
        $db = DbSingleton::getTokoDb();
        $slave = new slave;
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $answer = 0; $err = "Помилка збереження даних!";
        $art_id = $slave->qq($art_id);
        $article_nr_displ = $slave->qq($article_nr_displ);
        $inner_cross = $slave->qq($inner_cross);
        $brand_id = $slave->qq($brand_id);
        $goods_group_id = $slave->qq($goods_group_id);
        $article_name = $slave->qq($article_name);
        $article_info = $slave->qq($article_info);

        if ($art_id > 0) {
            $article_nr_search = $this->clearArticle($article_nr_displ);
            $r = $db->query("SELECT `ARTICLE_NR_DISPL`, `BRAND_ID` FROM `T2_ARTICLES` WHERE `ART_ID` = $art_id LIMIT 1;");
            $ARTICLE_NR_DISPL_old = $db->result($r, 0, "ARTICLE_NR_DISPL");
            $BRAND_ID_old = $db->result($r, 0, "BRAND_ID");

            $r = $db->query("SELECT `NAME` FROM `T2_NAMES` WHERE `ART_ID` = $art_id AND `LANG_ID` = 16 LIMIT 1;");
            $NAME_old = $db->result($r, 0, "NAME");
            $r = $db->query("SELECT `NAME` FROM `T2_NAMES` WHERE `ART_ID` = $art_id AND `LANG_ID` = 41 LIMIT 1;");
            $NAME_41_old = $db->result($r, 0, "NAME");

            $db->query("UPDATE `T2_ARTICLES` SET `ARTICLE_NR_DISPL` = '$article_nr_displ', `ARTICLE_NR_SEARCH` = '$article_nr_search', `BRAND_ID` = $brand_id WHERE `ART_ID` = $art_id;");

            // T2_GOODS_GROUP UPDATE
            $r = $db->query("SELECT `GOODS_GROUP_ID` FROM `T2_GOODS_GROUP` WHERE `ART_ID` = $art_id LIMIT 1;");
            $n = (int)$db->num_rows($r);
            if ($n === 0) {
                $db->query("INSERT INTO `T2_GOODS_GROUP` (`ART_ID`, `GOODS_GROUP_ID`) VALUES ($art_id, '$goods_group_id');");
            }
            if ($n === 1) {
                $goods_group_id_db = (int)$db->result($r, 0, "GOODS_GROUP_ID");

                if ($goods_group_id_db !== (int)$goods_group_id) {
                    $db->query("UPDATE `T2_GOODS_GROUP` SET `GOODS_GROUP_ID` = $goods_group_id WHERE `ART_ID` = $art_id;");
                }
            }

            // T2_INNER_CROSS UPDATE
            $r = $db->query("SELECT `CROSS` FROM `T2_INNER_CROSS` WHERE `ART_ID` = $art_id LIMIT 1;");
            $n = (int)$db->num_rows($r);
            $inner_cross_old = $db->result($r, 0, "CROSS");
            if ($n === 0) {
                $db->query("INSERT INTO `T2_INNER_CROSS` (`ART_ID`,`CROSS`) VALUES ($art_id, '$inner_cross');");
            }

            if ($n === 1) {
                $inner_cross_db = $db->result($r, 0, "CROSS");

                if ($inner_cross_db !== $inner_cross) {
                    $db->query("UPDATE `T2_INNER_CROSS` SET `CROSS` = '$inner_cross' WHERE `ART_ID` = $art_id;");
                }
            }

            // T2_ARTICLES_UNIV_NUMBER
            $r = $db->query("SELECT `UNIV_NUMBER` FROM `T2_ARTICLES_UNIV_NUMBER` WHERE `ART_ID` = $art_id LIMIT 1;");
            $n = (int)$db->num_rows($r);
            $unique_number_old = $db->result($r, 0, "UNIV_NUMBER");
            if ($n === 0) {
                $db->query("INSERT INTO `T2_ARTICLES_UNIV_NUMBER` (`ART_ID`, `UNIV_NUMBER`) VALUES ($art_id, '$unique_number');");
            }
            if ($n === 1) {
                $unique_number_db = $db->result($r, 0, "UNIV_NUMBER");

                if ($unique_number_db !== $unique_number) {
                    $db->query("UPDATE `T2_ARTICLES_UNIV_NUMBER` SET `UNIV_NUMBER` = '$unique_number' WHERE `ART_ID` = $art_id;");
                }
            }

            // T2_INNER_CROSS UPDATE
            $r = $db->query("SELECT `NAME`, `INFO` FROM `T2_NAMES` WHERE `ART_ID` = $art_id AND `LANG_ID` = 16 LIMIT 1;");
            $n = (int)$db->num_rows($r);
            if ($n === 0) {
                $db->query("INSERT INTO `T2_NAMES` (`ART_ID`, `LANG_ID`, `NAME`, `INFO`) VALUES ($art_id, 16, \"$article_name\", \"$article_info\");");
            }
            if ($n === 1) {
                $article_name_db = $db->result($r, 0, "NAME");
                $article_info_db = $db->result($r, 0, "INFO");

                if ($article_name_db !== $article_name || $article_info_db !== $article_info) {
                    $db->query("UPDATE `T2_NAMES` SET `NAME` = \"$article_name\", `INFO` = \"$article_info\" WHERE `ART_ID` = $art_id AND `LANG_ID` = 16;");
                }
            }

            // T2_INNER_CROSS UPDATE 41
            $r = $db->query("SELECT `NAME`, `INFO` FROM `T2_NAMES` WHERE `ART_ID` = $art_id AND `LANG_ID` = 41 LIMIT 1;");
            $n = (int)$db->num_rows($r);
            if ($n === 0) {
                $db->query("INSERT INTO `T2_NAMES` (`ART_ID`, `LANG_ID`, `NAME`, `INFO`) VALUES ($art_id, 41, \"$article_name_ukr\", \"$article_info\");");
            }
            if ($n === 1) {
                $article_name_db = $db->result($r, 0, "NAME");
                $article_info_db = $db->result($r, 0, "INFO");

                if ($article_name_db !== $article_name_ukr || $article_info_db !== $article_info) {
                    $db->query("UPDATE `T2_NAMES` SET `NAME` = \"$article_name_ukr\", `INFO` = \"$article_info\" WHERE `ART_ID` = $art_id AND `LANG_ID` = 41;");
                }
            }

            // price export status
            if ($export_status) {
                $db->query("DELETE FROM `T2_ARTICLES_NOT_EXPORT` WHERE `ART_ID` = $art_id;");
                $db->query("INSERT INTO `T2_ARTICLES_NOT_EXPORT` (`ART_ID`) VALUES ($art_id);");
            } else {
                $db->query("DELETE FROM `T2_ARTICLES_NOT_EXPORT` WHERE `ART_ID` = $art_id;");
            }

            // Add history - T2_ARTICLES_LOGS
            $db->query("INSERT INTO `T2_ARTICLES_LOGS` (`user_id`,`art_id`,`article_nr_displ`,`brand_id`,`article_name_ru`,`article_name_ua`,`inner_cross`,`unique_number`,`article_nr_displ_old`,`brand_id_old`,`article_name_ru_old`,`article_name_ua_old`,`inner_cross_old`,`unique_number_old`) 
            VALUES ('$user_id', '$art_id', '$article_nr_displ', '$brand_id', \"$article_name\", \"$article_name_ukr\", '$inner_cross', '$unique_number', '$ARTICLE_NR_DISPL_old', '$BRAND_ID_old', '$NAME_old', '$NAME_41_old', '$inner_cross_old', '$unique_number_old');");
            $answer = 1; $err = "";
        }

        return array($answer, $err);
    }

    public function showArticleLogs($art_id)
    {
        $db = DbSingleton::getTokoDb();
        $form = ""; $form_htm = RD . "/tpl/catalogue_article_logs.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $list = "";
        $r = $db->query("SELECT * FROM `T2_ARTICLES_LOGS` WHERE `art_id` = $art_id;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $data               = $db->result($r, $i - 1, "data");
            $user_id            = $db->result($r, $i - 1, "user_id");
            $article_nr_displ   = $db->result($r, $i - 1, "article_nr_displ");
            $brand_id           = $db->result($r, $i - 1, "brand_id");
            $article_name       = $db->result($r, $i - 1, "article_name_ru");
            $article_name_41    = $db->result($r, $i - 1, "article_name_ua");
            $inner_cross        = $db->result($r, $i - 1, "inner_cross");
            $unique_number      = $db->result($r, $i - 1, "unique_number");
            $article__old       = $db->result($r, $i - 1, "article_nr_displ_old");
            $brand_id_old       = $db->result($r, $i - 1, "brand_id_old");
            $article_name_old   = $db->result($r, $i - 1, "article_name_ru_old");
            $article_41_old     = $db->result($r, $i - 1, "article_name_ua_old");
            $inner_cross_old    = $db->result($r, $i - 1, "inner_cross_old");
            $unique_number_old  = $db->result($r, $i - 1, "unique_number_old");

            $user_name          = $this->getMediaUserName($user_id);
            $brand_name         = $this->getBrandName($brand_id);
            $brand_name_old     = $this->getBrandName($brand_id_old);

            $style_displ        = ($article__old !== $article_nr_displ) ? "style='background:pink;'" : "";
            $style_brand        = ($brand_name_old !== $brand_name) ? "style='background:pink;'" : "";
            $style_name_ru      = ($article_name_old !== $article_name) ? "style='background:pink;'" : "";
            $style_name_ua      = ($article_41_old !== $article_name_41) ? "style='background:pink;'" : "";
            $style_cross        = ($inner_cross_old !== $inner_cross) ? "style='background:pink;'" : "";
            $style_unique       = ($unique_number_old !== $unique_number) ? "style='background:pink;'" : "";

            $list .= "
            <tr>
                <td>$i</td>
                <td>$data</td>
                <td>$user_name</td>
                <td $style_displ>$article__old => $article_nr_displ</td>
                <td $style_brand>$brand_name_old => $brand_name</td>
                <td $style_name_ru>$article_name_old => $article_name</td>
                <td $style_name_ua>$article_41_old => $article_name_41</td>
                <td $style_cross>$inner_cross_old => $inner_cross</td>
                <td $style_unique>$unique_number_old => $unique_number</td>
            </tr>";
        }

        $form = str_replace("{article_logs_range}", $list, $form);

        return $form;
    }

    public function loadArticleCommets($art_id): string
    {
        $db = DbSingleton::getTokoDb();
        $list = "";
        $form = ""; $form_htm = RD . "/tpl/catalogue_comment_block.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $r = $db->query("SELECT `ID`, `USER_ID`, `DATA`, `COMMENT` FROM `T2_ARTICLES_COMMENTS` WHERE `ART_ID` = $art_id ORDER BY `id` DESC;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id         = $db->result($r, $i - 1, "ID");
            $user_id    = $db->result($r, $i - 1, "USER_ID");
            $user_name  = $this->getMediaUserName($user_id);
            $data       = $db->result($r, $i - 1, "DATA");
            $comment    = $db->result($r, $i - 1, "COMMENT");

            $block = $form;
            $block = str_replace(array("{art_id}", "{id}", "{user_id}", "{user_name}", "{data}", "{comment}"), array($art_id, $id, $user_id, $user_name, $data, $comment), $block);
            $list .= $block;
        }

        if ($n === 0) {
            $list = "<h3 class='text-center'>Коментарі відсутні</h3>";
        }

        return $list;
    }

    public function saveArticleComment($art_id, $comment): array
    {
        $db = DbSingleton::getTokoDb();
        $slave = new slave;
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $answer = 0; $err = "Помилка збереження даних!";
        $art_id = $slave->qq($art_id); $comment = $slave->qq($comment);
        if ($art_id > 0 && $comment !== "") {
            $db->query("INSERT INTO `T2_ARTICLES_COMMENTS` (`ART_ID`, `USER_ID`, `COMMENT`) VALUES ('$art_id', '$user_id', '$comment');");
            $answer = 1; $err = "";
        }

        return array($answer, $err);
    }

    public function dropArticleComment($art_id, $comment_id): array
    {
        $db = DbSingleton::getTokoDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка видалення запису!";
        $art_id = $slave->qq($art_id); $comment_id = $slave->qq($comment_id);
        if ($art_id > 0 && $comment_id > 0) {
            $r = $db->query("SELECT * FROM `T2_ARTICLES_COMMENTS` WHERE `ART_ID` = $art_id AND `ID` = $comment_id LIMIT 1;");
            $n = (int)$db->num_rows($r);
            if ($n === 1) {
                $db->query("DELETE FROM `T2_ARTICLES_COMMENTS` WHERE `ART_ID` = $art_id AND `ID` = $comment_id;");
                $answer = 1; $err = "";
            }
        }

        return array($answer, $err);
    }

    public function showArtilceGallery($art_id, $disp_nomber): array
    {
        $db = DbSingleton::getTokoDb();
        $form = ""; $form_htm = RD . "/tpl/catalogue_article_gallery.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $list = ""; $slide_list = "";
        $r = $db->query("SELECT `PHOTO_NAME` FROM `T2_PHOTOS` WHERE `ART_ID` = $art_id AND `ACTIVE` = 1 ORDER BY `PHOTO_NAME` ASC;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $file_name = $db->result($r, $i - 1, "PHOTO_NAME");
            $file_name = trim(preg_replace('/\s\s+/', ' ', $file_name));
            $main_v = "";
            $main_c = "";
            if ($i === 1) {
                $main_v = "active";
                $main_c = "class=\"active\"";
            }
            $link = "https://toko.ua/uploads/images/catalogue/$file_name";
            $list .= "<div class=\"item $main_v\">
                <img alt=\"image\" class=\"img-responsive\" src=\"$link\" align='center'>
                <div class=\"carousel-caption\">
                    <p>$file_name</p>
                </div>
            </div>";
            $slide_list .= "<li data-slide-to=\"" . ($i - 1) . "\" data-target=\"#carouselArticleModal\" $main_c></li>";
        }
        if ($n === 0) {
            $list = "<h3 class='text-center'>Фото відсутні</h3>";
        }

        $form = str_replace(array("{items_list}", "{slide_list}"), array($list, $slide_list), $form);

        return array($form, "Фотогалерея артикула: $disp_nomber");
    }

    public function loadArticleFoto($art_id): string
    {
        $db = DbSingleton::getTokoDb();
        $form = ""; $form_htm = RD . "/tpl/catalogue_foto_block.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $list = "";
        $r = $db->query("SELECT `ID`, `USER_ID`, `PHOTO_NAME`, `DATA`, `MAIN` FROM `T2_PHOTOS` WHERE `ART_ID` = $art_id AND `ACTIVE` = 1 ORDER BY `PHOTO_NAME` ASC;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $file_id    = $db->result($r, $i - 1, "ID");
            $user_id    = $db->result($r, $i - 1, "USER_ID");
            $file_name  = $db->result($r, $i - 1, "PHOTO_NAME");
            $file_name  = trim(preg_replace('/\s\s+/', ' ', $file_name));
            $data       = $db->result($r, $i - 1, "DATA");
            $user_name  = $this->getMediaUserName($user_id);
            $main       = (int)$db->result($r, $i - 1, "MAIN");
            $link       = "https://toko.ua/uploads/images/catalogue/$file_name";
            $main_v     = "<a class=\"btn btn-xs btn-white\" onClick=\"setArticlesFotoMain('$art_id','$file_id')\"><i class=\"fa fa-check\"></i> Основне фото</a>";

            if ($main === 1) {
                $main_v = " <span class=\"btn btn-xs label-primary\"><i class=\"fa fa-check\"></i> Основне фото</span>";
            }

            $block = $form;
            $block = str_replace(array("{file_id}", "{foto_name}", "{file_name}", "{user_name}", "{data}", "{art_id}", "{link}", "{main}"), array($file_id, $file_name, $file_name, $user_name, $data, $art_id, $link, $main_v), $block);
            $list .= $block;
        }

        if ($n === 0) {
            $list = "<h3 class='text-center'>Фото відсутні</h3>";
        }

        return $list;
    }

    public function setArticlesFotoMain($art_id, $file_id): array
    {
        $db = DbSingleton::getTokoDb();
        $answer = 0; $err = "Помилка обробки запиту!";
        $slave = new slave;
        $art_id = $slave->qq($art_id); $file_id = $slave->qq($file_id);
        if ($art_id > 0 && $file_id > 0) {
            $db->query("UPDATE `T2_PHOTOS` SET `MAIN` = '0' WHERE `ART_ID` = $art_id AND `MAIN` = 1;");
            $db->query("UPDATE `T2_PHOTOS` SET `MAIN` = '1' WHERE `ART_ID` = $art_id AND `ID` = $file_id;");
            $answer = 1; $err = "";
        }

        return array($answer, $err);
    }

    public function articlesFotoDropFile($art_id, $file_id): array
    {
        $db = DbSingleton::getTokoDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка видалення файлу!";
        $art_id = $slave->qq($art_id); $file_id = $slave->qq($file_id);

        if ($art_id > 0 && $file_id > 0) {
            $r = $db->query("SELECT `PHOTO_NAME` FROM `T2_PHOTOS` WHERE `ART_ID` = $art_id AND `ID` = $file_id LIMIT 1;");
            $n = (int)$db->num_rows($r);
            if ($n === 1) {
                $file_name = $db->result($r, 0, "PHOTO_NAME");
                if (file_exists(RD . "/uploads/images/catalogue/$file_name")) {
                    unlink(RD . "/uploads/images/catalogue/$file_name");
                }
                $db->query("DELETE FROM `T2_PHOTOS` WHERE `ART_ID` = $art_id AND `ID` = $file_id;");
                $answer = 1; $err = "";
            }
        }

        return array($answer, $err);
    }

    public function loadArticleScheme($template_id, $op): string
    {
        $db = DbSingleton::getTokoDb();
        $form = ""; $form_htm = RD . "/tpl/catalogue_scheme_block.htm";
        if ($op == 1) { $form_htm = RD . "/tpl/catalogue_scheme_view_block.htm"; }
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $list = "";

        $r = $db->query("SELECT `ID`, `USER_ID`, `FILE_NAME`, `DATA`, `NAME` FROM `T2_ARTICLES_SCHEME` WHERE `TEMPLATE_ID` = $template_id ORDER BY `FILE_NAME` ASC;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $file_id    = $db->result($r, $i - 1, "ID");
            $user_id    = $db->result($r, $i - 1, "USER_ID");
            $file_name  = $db->result($r, $i - 1, "FILE_NAME");
            $file_name  = trim(preg_replace('/\s\s+/', ' ', $file_name));
            $data       = $db->result($r, $i - 1, "DATA");
            $user_name  = $this->getMediaUserName($user_id);
            $name       = $db->result($r, $i - 1, "NAME");
            $link       = "http://portal.myparts.pro/cdn/articles_scheme/$template_id/$file_name";
            $file       = "<img src='$link' class='image' alt='$name'>";

            $block = $form;
            $block = str_replace(array("{file_id}", "{file}", "{file_name}", "{scheme_name}", "{user_name}", "{data}", "{template_id}", "{link}"), array($file_id, $file, $file_name, $name, $user_name, $data, $template_id, $link), $block);
            $list .= $block;
        }

        if ($n === 0) {
            $list = "<h3 class='text-center'>Схеми відсутні</h3>";
        }

        return $list;
    }

    public function articlesSchemeDropFile($file_id): array
    {
        $db = DbSingleton::getTokoDb();
        $answer = 0; $err = "Помилка видалення файлу!";
        $slave = new slave;
        $file_id = $slave->qq($file_id);

        if ($file_id > 0) {
            $r = $db->query("SELECT `TEMPLATE_ID`, `FILE_NAME` FROM `T2_ARTICLES_SCHEME` WHERE `ID` = $file_id LIMIT 1;");
            $n = (int)$db->num_rows($r);
            if ($n === 1) {
                unlink(RD . '/cdn/articles_scheme/$template_id/$file_name');
                $db->query("DELETE FROM `T2_ARTICLES_SCHEME` WHERE `ID` = $file_id;");
                $answer = 1; $err = "";
            }
        }

        return array($answer, $err);
    }

    public function showSupplListSelect($sel_id): string
    {
        $db = DbSingleton::getDb();
        $list = "<option value=''></option>";

        $r = $db->query("SELECT c.*, ot.name as org_type_name 
        FROM `A_CLIENTS` c 
            INNER JOIN `A_CLIENTS_CATEGORY` cc ON (cc.client_id = c.id) 
            LEFT OUTER JOIN `A_ORG_TYPE` ot ON (ot.id = c.org_type)
        WHERE c.status = '1' AND cc.category_id = '2' 
        ORDER BY c.name, c.full_name, c.id ASC;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id         = $db->result($r, $i - 1, "id");
            $org_type   = $db->result($r, $i - 1, "org_type_name");
            $name       = $db->result($r, $i - 1, "name");

            if ($name === "") {
                $name = $db->result($r, $i - 1, "full_name");
            }
            $sel = ((int)$sel_id === (int)$id) ? "selected='selected'" : "";
            $list .= "<option value='$id' $sel>$name ($org_type)</option>";
        }

        return $list;
    }

    public function showManufactureListSelect($sel_id): string
    {
        $db = DbSingleton::getTokoDb();
        $list = "<option value=''></option>";
        $r = $db->query("SELECT `MFA_ID`, `MFA_BRAND` FROM `T_manufacturers` ORDER BY `MFA_BRAND` ASC;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id     = $db->result($r, $i - 1, "MFA_ID");
            $name   = $db->result($r, $i - 1, "MFA_BRAND");

            $sel = ((int)$sel_id === (int)$id) ? "selected='selected'" : "";
            $list .= "<option value='$id' $sel>$name</option>";
        }
        return $list;
    }

    public function showModelSelectList($mfa_id, $sel_id): string
    {
        $db = DbSingleton::getTokoDb();
        $list = "<option value=''></option>";
        $r = $db->query("SELECT `MOD_ID`, `TEX_TEXT`, `MOD_PCON_START`, `MOD_PCON_END` FROM `T_models` WHERE `MOD_MFA_ID` = $mfa_id ORDER BY `Model` ASC;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r, $i - 1, "MOD_ID");
            $name = $db->result($r, $i - 1, "TEX_TEXT") . " (";
            $name .= substr($db->result($r, $i - 1, "MOD_PCON_START"), 0, 4) . ".";
            $name .= substr($db->result($r, $i - 1, "MOD_PCON_START"), 4, 2) . "-";
            if (strlen($db->result($r, $i - 1, "MOD_PCON_END")) > 1) {
                $name .= substr($db->result($r, $i - 1, "MOD_PCON_END"), 0, 4) . ".";
                $name .= substr($db->result($r, $i - 1, "MOD_PCON_END"), 4, 2);
            } else {
                $name .= "&infin;";
            }
            $name .= ")";
            $sel = ((int)$sel_id === (int)$id) ? "selected='selected'" : "";
            $list .= "<option value='$id' $sel>$name</option>";
        }
        return $list;
    }

    public function showFilterModificationSelectList($mod_id, $sel_id): string
    {
        $db = DbSingleton::getTokoDb();
        $list = "<option value=''></option>";
        $r = $db->query("SELECT `TYP_ID`, `Fuel`, `TYP_TEXT`, `TYP_PCON_START`, `TYP_PCON_END`, `TYP_HP_FROM`, `TYP_KW_FROM`, `TYP_CCM`, `ENG_Cod` 
        FROM `T_types` 
        WHERE `TYP_MOD_ID` = $mod_id 
        ORDER BY `TYP_SORT` ASC, `TYP_TEXT` ASC;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r, $i - 1, "TYP_ID");
            $name = $db->result($r, $i - 1, "Fuel");
            if ($name !== "") {
                $name .= " | ";
            }
            $name .= $db->result($r, $i - 1, "TYP_TEXT") . " | ";
            $name .= "(" . substr($db->result($r, $i - 1, "TYP_PCON_START"),0,4) . "." . substr($db->result($r, $i - 1, "TYP_PCON_START"),4,2);
            $name .= "-";
            if (strlen($db->result($r, $i - 1, "TYP_PCON_END")) > 1) {
                $name .= substr($db->result($r, $i - 1, "TYP_PCON_END"),0,4) . "." . substr($db->result($r, $i - 1, "TYP_PCON_END"),4,2) . ") | ";
            } else {
                $name .= "&infin;) | ";
            }
            $name .= $db->result($r, $i - 1, "TYP_HP_FROM") . "HP/";
            $name .= $db->result($r, $i - 1, "TYP_KW_FROM") . "kW | ";
            $name .= $db->result($r, $i - 1, "TYP_CCM") . "см<sup>3</sup> | ";
            $name .= $db->result($r, $i - 1, "ENG_Cod");
            $sel = ((int)$sel_id === (int)$id) ? "selected='selected'" : "";
            $list .= "<option value='$id' $sel>$name</option>";
        }

        return $list;
    }

    public function getTecGroupTreeChilds($str_id)
    {
        $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT COUNT(`STR_ID`) as kol FROM `T2_GROUP_TREE` WHERE `STR_ID_PARENT` = '$str_id';");
        return 0 + $db->result($r, 0, "kol");
    }

    public function createTDtree($typ_id)
    {
        $db = DbSingleton::getTokoDb();
        $art_id_str = "0";
        $r = $db->query("SELECT `ART_ID` FROM `T2_LINKS` WHERE `TYP_ID` = '$typ_id' GROUP BY `ART_ID`;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $art_id = $db->result($r, $i - 1, "ART_ID");
            if ($art_id !== "") {
                $art_id_str .= ",$art_id";
            }
        }
        $art_id_str = "0";

        $r = $db->query("SELECT `ART_ID` FROM `T2_ARTICLES` WHERE `ART_ID` IN ($art_id_str);");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $art_id = $db->result($r, $i - 1, "ART_ID");
            $art_id_str .= ",$art_id";
        }

        $str_id_str = "0";
        $str_id_arr = array();

        $r = $db->query("SELECT `STR_ID`, `ART_ID` FROM `T2_TREE` WHERE `ART_ID` IN ($art_id_str);");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $str_id = $db->result($r, $i - 1, "STR_ID");
            $str_id_str .= ",$str_id";
            $art_id = $db->result($r, $i - 1, "ART_ID");
            $str_id_arr[$str_id][$art_id] = $art_id;
        }

        $td_array = array();

        $r = $db->query("SELECT `STR_ID`, `STR_ID_PARENT`, `STR_LEVEL`, `TEX_TEXT` FROM `T2_GROUP_TREE` WHERE `STR_ID` IN ($str_id_str);");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $str_id = $db->result($r, $i - 1, "STR_ID");
            $str_id_parrent = $db->result($r, $i - 1, "STR_ID_PARENT");
            if ($str_id_parrent === "") {
                $str_id_parrent = 0;
            }
            $str_level = $db->result($r, $i - 1, "STR_LEVEL");
            $tex_text = $db->result($r, $i - 1, "TEX_TEXT");
            $child = $this->getTecGroupTreeChilds($str_id);
            $art_ids = implode(",", $str_id_arr[$str_id]);
            $td_array[$i]["id_tree"] = $str_id;
            $td_array[$i]["id_parent"] = $str_id_parrent;
            $td_array[$i]["level"] = $str_level;
            $td_array[$i]["name"] = $tex_text;
            $td_array[$i]["child"] = $child;
            $td_array[$i]["art_ids"] = $art_ids;
        }

        $tree = ""; $lvl = 1;
        for ($i = 1; $i <= 10; $i++) {
            ++$lvl;
            foreach ($td_array as $elm) {
                if ($elm["level"] == $lvl) {
                    $str = "<li><div>";
                    if ($elm["child"] > 0) {
                        $str .= $elm["name"];
                    }
                    if ($elm["child"] == 0) {
                        $str .= "<a href='javascript:setFil2StrInfo(\"".$elm["id_tree"]."\",\"".$elm["name"]."\",\"".$elm["art_ids"]."\")'>".$elm["name"]."</a>";
                    }
                    $str .= "</div>";
                    if ($elm["child"] > 0) {
                        $str .= "\n<ul>\n{p".$elm["id_tree"]."}</ul>\n";
                    }
                    $str .= "</li>\n";
                    if ($lvl === 2) {
                        $tree .= $str;
                    }
                    if ($lvl > 2) {
                        $tree = str_replace("{p" . $elm["id_parent"] . "}", $str . "{p" . $elm["id_parent"] . "}", $tree);
                    }
                }
            }
        }

        foreach ($td_array as $elm) {
            $tree = str_replace(array("{p" . $elm["id_parent"] . "}", "{p" . $elm["id_tree"] . "}"), "", $tree);
        }

        return $tree;
    }

    public function createTDtree_universal(): array
    {
        //($art_id)
        $db = DbSingleton::getTokoDb();
        //$str_id_str = "0";
        $str_id_arr = array();
//        $r = $db->query("SELECT `STR_ID` FROM `T2_TREE` WHERE `ART_ID` IN ($art_id);");
//        $n = (int)$db->num_rows($r);
//        for ($i = 1; $i <= $n; $i++) {
//            $str_id = $db->result($r, $i - 1, "STR_ID");
//            //$str_id_str .= ",$str_id";
//        }
        $td_array = array();

        $r = $db->query("SELECT `STR_ID`, `STR_ID_PARENT`, `STR_LEVEL`, `TEX_TEXT` FROM `T2_GROUP_TREE` WHERE `LNG_ID` = 16;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $str_id = $db->result($r, $i - 1, "STR_ID");
            $str_id_parrent = $db->result($r, $i - 1, "STR_ID_PARENT");
            if ($str_id_parrent === "") {
                $str_id_parrent = 0;
            }
            $str_level = $db->result($r, $i - 1, "STR_LEVEL");
            $tex_text = $db->result($r, $i - 1, "TEX_TEXT");
            $child = $this->getTecGroupTreeChilds($str_id);
            $art_ids = implode(",", $str_id_arr[$str_id]);
            $td_array[$i]["id_tree"] = $str_id;
            $td_array[$i]["id_parent"] = $str_id_parrent;
            $td_array[$i]["level"] = $str_level;
            $td_array[$i]["name"] = $tex_text;
            $td_array[$i]["child"] = $child;
            $td_array[$i]["art_ids"] = $art_ids;
        }

        $tree = ""; $lvl = 1;
        for ($i = 1; $i <= 10; $i++) {
            ++$lvl;
            foreach ($td_array as $elm) {
                if ($elm["level"] == $lvl) {
                    $str = "<li><div>";
                    if ($elm["child"] > 0) {
                        $str .= $elm["name"];
                    }
                    $checked = (in_array($elm["id_tree"], $str_id_arr, true)) ? "checked='checked'" : "";
                    if ($elm["child"] == 0) {
                        $str .= "<a href='javascript:return;'><input type='checkbox' id='{na_tree_".$elm["id_tree"]."}' value='\"".$elm["id_tree"]."\"' $checked>".$elm["name"]."</a>";
                    }
                    $str .= "</div>";
                    if ($elm["child"] > 0) {
                        $str .= "\n<ul>\n{p" . $elm["id_tree"] . "}</ul>\n";
                    }
                    $str .= "</li>\n";
                    if ($lvl === 2) {
                        $tree .= $str;
                    }
                    if ($lvl > 2) {
                        $tree = str_replace("{p" . $elm["id_parent"] . "}", $str . "{p" . $elm["id_parent"] . "}", $tree);
                    }
                }
            }
        }

        $kol_elem = 0;
        foreach ($td_array as $elm) {
            ++$kol_elem;
            $tree = str_replace(array("{p" . $elm["id_parent"] . "}", "{p" . $elm["id_tree"] . "}", "{na_tree_" . $elm["id_tree"] . "}"), array("", "", "na_tree_" . $kol_elem), $tree);
        }

        return array($tree, $kol_elem);
    }

    public function loadFilterGroupTreeList($typ_id): array
    {
        $form = ""; $form_htm = RD . "/tpl/catalogue_tecdoc_group_tree.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $form = str_replace("{menu}", $this->createTDtree($typ_id), $form);

        return array($form, "Оберіть групу запчастин");
    }

    public function loadFilterGroupTreeListSide($typ_id): array
    {
        $form = ""; $form_htm = RD . "/tpl/catalogue_tecdoc_tree_result_form.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $form_tree = ""; $form_htm = RD . "/tpl/catalogue_tecdoc_group_tree.htm";
        if (file_exists($form_htm)) { $form_tree = file_get_contents($form_htm); }
        $form_tree = str_replace("{menu}", $this->createTDtree($typ_id), $form_tree);
        $form_result = ""; $form_htm = RD . "/tpl/catalogue_search_result_table.htm";
        if (file_exists($form_htm)) { $form_result = file_get_contents($form_htm); }
        [$header_list, $range_list,] = $this->showArticlesSearchList("", "", 0);
        $form_result = str_replace(array("{header_list}", "{range_list}"), array($header_list, $range_list), $form_result);

        return array($form, $form_tree, $form_result);
    }

    public function getBrandId($art_id)
    {
        $db = DbSingleton::getTokoDb();
        $brand_id = 0;
        $r = $db->query("SELECT `BRAND_ID` FROM `T2_ARTICLES` WHERE `ART_ID` = $art_id LIMIT 1;");
        $n = (int)$db->num_rows($r);
        if ($n === 1) {
            $brand_id = $db->result($r, 0, "BRAND_ID");
        }
        return $brand_id;
    }

    public function getBrandKind($sel_id)
    {
        $db = DbSingleton::getTokoDb();
        $kind = 4;
        $r = $db->query("SELECT `KIND` FROM `T2_BRANDS` WHERE `BRAND_ID` = $sel_id LIMIT 1;");
        $n = (int)$db->num_rows($r);
        if ($n === 1) {
            $kind = $db->result($r, 0, "KIND");
        }
        return $kind;
    }

    public function getBrandName($sel_id)
    {
        $db = DbSingleton::getTokoDb();
        $brand_name = "";
        $r = $db->query("SELECT `BRAND_NAME` FROM `T2_BRANDS` WHERE `BRAND_ID` = $sel_id LIMIT 1;");
        $n = (int)$db->num_rows($r);
        if ($n === 1) {
            $brand_name = $db->result($r, 0, "BRAND_NAME");
        }
        return $brand_name;
    }

    public function showManufListSelect($sel_id): string
    {
        $db = DbSingleton::getTokoDb();
        $list = "<option value=''></option>";
        $r = $db->query("SELECT `ID`, `KEY`, `NAME` FROM `T2_MANUF` ORDER BY `NAME` ASC;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r, $i - 1, "ID");
            $key = $db->result($r, $i - 1, "KEY");
            $name = $db->result($r, $i - 1, "NAME");
            $sel = ((int)$sel_id === (int)$id) ? "selected='selected'" : "";
            $list .= "<option value='$id-$key' $sel>$name</option>";
        }
        return $list;
    }

    public function showBrandListSelect($sel_id): string
    {
        $db = DbSingleton::getTokoDb();
        $list = "<option value=''></option>";
        $r = $db->query("SELECT `BRAND_ID`, `BRAND_NAME` FROM `T2_BRANDS` ORDER BY `BRAND_NAME` ASC;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $brand_id = $db->result($r, $i - 1, "BRAND_ID");
            $brand_name = $db->result($r, $i - 1, "BRAND_NAME");
            $sel = ((int)$sel_id === (int)$brand_id) ? "selected='selected'" : "";
            $list .= "<option value='$brand_id' $sel>$brand_name</option>";
        }
        return $list;
    }

    public function showUnitsListSelect($sel_id): string
    {
        $db = DbSingleton::getTokoDb();
        $list = "<option value=''></option>";
        $r = $db->query("SELECT `id`, `abr` FROM `units` ORDER BY `name` ASC;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r, $i - 1, "id");
            $name = $db->result($r, $i - 1, "abr");
            $sel = ((int)$sel_id === (int)$id) ? "selected='selected'" : "";
            $list .= "<option value='$id' $sel>$name</option>";
        }
        return $list;
    }

    public function loadRefinementListSelect($group_id): string
    {
        $db = DbSingleton::getTokoDb();
        $list = "<option value=''></option>";
        $r = $db->query("SELECT `NAME`, `KEY` FROM `T2_REFINEMENT` WHERE `GROUP_ID` = $group_id ORDER BY `NAME` ASC, `ID` ASC;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $name = $db->result($r, $i - 1, "NAME");
            $key = $db->result($r, $i - 1, "KEY");
            $list .= "<option value='$key'>$i. $name</option>";
        }
        return $list;
    }

    public function showGoodsGroupLetterListSelect($prnt_id): string
    {
        $db = DbSingleton::getTokoDb();
        $list = "<option value=''></option>";
        $r = $db->query("SELECT `ID`, `NAME`, `KEY` FROM `GOODS_GROUP` WHERE `PARRENT_ID` = $prnt_id ORDER BY `KEY` ASC, `ID` ASC;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r, $i - 1, "ID");
            $name = $db->result($r, $i - 1, "NAME");
            $key = $db->result($r, $i - 1, "KEY");
            $list .= "<option value='$id-$key'>$i. $name</option>";
        }
        return $list;
    }

    public function showGoodsGroupListSelect($sel_id): string
    {
        $db = DbSingleton::getTokoDb();
        $list = "<option value=''></option>";
        $r = $db->query("SELECT `ID`, `NAME` FROM `GOODS_GROUP` WHERE `PARRENT_ID` = 0 ORDER BY `KEY` ASC, `ID` ASC;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r, $i - 1, "ID");
            $name = $db->result($r, $i - 1, "NAME");
            $sel = ((int)$sel_id === (int)$id) ? "selected='selected'" : "";
            $list .= "<option value='$id' style='font-weight:bold' $sel>$i. $name</option>" . $this->showGoodsGroupSubListSelect($id, $sel_id, $i);
        }
        return $list;
    }

    public function showGoodsGroupSubListSelect($parrent_id, $sel_id, $prn_i): string
    {
        $db = DbSingleton::getTokoDb();
        $list = "";
        $r = $db->query("SELECT `ID`, `NAME` FROM `GOODS_GROUP` WHERE `PARRENT_ID` = $parrent_id ORDER BY `KEY` ASC, `ID` ASC;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r, $i - 1, "ID");
            $name = $db->result($r, $i - 1, "NAME");
            $sel = ((int)$sel_id === (int)$id) ? "selected='selected'" : "";
            $list .= "<option value='$id' $sel>$prn_i.$i. $name</option>" . $this->showGoodsGroupSubListSelect($id, $sel_id, "$prn_i.$i");
        }
        return $list;
    }

    public function unlinkCatalogueGoodGroup($art_id, $group_id): array
    {
        $db = DbSingleton::getTokoDb();
        $answer = 0; $err = "Помилка обробки даних";
        $r = $db->query("SELECT * FROM `T2_GOODS_GROUP` WHERE `ART_ID` = $art_id AND `GOODS_GROUP_ID` = $group_id LIMIT 1;");
        $n = (int)$db->num_rows($r);
        if ($n === 1) {
            $db->query("DELETE FROM `T2_GOODS_GROUP` WHERE `ART_ID` = $art_id AND `GOODS_GROUP_ID` = $group_id;");
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

    public function showGoodGroupTree($sel_id)
    {
        $db = DbSingleton::getTokoDb();
        $tree = "";
        $form = ""; $form_htm = RD . "/tpl/catalogue_goods_group_form.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }

        $r = $db->query("SELECT `ID`, `NAME` FROM `GOODS_GROUP` WHERE `PARRENT_ID` = 0 ORDER BY `KEY` ASC, `ID` ASC;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r, $i - 1, "ID");
            $name = $db->result($r, $i - 1, "NAME");
            $sel = ((int)$sel_id === (int)$id) ? " data-jstree='{\"selected\":true}'" : "";
            $tree .= "<li id='$id' $sel>$name" . $this->showGoodGroupSubLevel($id, $sel_id) . "</li>";
        }

        $form = str_replace(array("{tree}", "{goods_group_id}"), array($tree, $sel_id), $form);

        return $form;
    }

    public function showGoodGroupSubLevel($parrent_id, $sel_id): string
    {
        $db = DbSingleton::getTokoDb();
        $tree = "";
        $r = $db->query("SELECT `ID`, `NAME` FROM `GOODS_GROUP` WHERE `PARRENT_ID` = $parrent_id ORDER BY `KEY` ASC, `ID` ASC;");
        $n = (int)$db->num_rows($r);

        if ($n > 0) {
            $tree .= "<ul>";
            for ($i = 1; $i <= $n; $i++) {
                $id = $db->result($r, $i - 1, "ID");
                $name = $db->result($r, $i - 1, "NAME");
                $sel = ((int)$sel_id === (int)$id) ? " data-jstree='{\"selected\":true}'" : "";
                $tree .= "<li id='$id' $sel>$name" . $this->showGoodGroupSubLevel($id, $sel_id) . "</li>";
            }
            $tree .= "</ul>";
        }

        return $tree;
    }

    public function getArticleGoodsGroup($art_id): array
    {
        $db = DbSingleton::getTokoDb();
        $id = 0; $caption = "";
        $r = $db->query("SELECT gg.ID, gg.NAME 
        FROM `GOODS_GROUP` gg 
            INNER JOIN `T2_GOODS_GROUP` t2gg on t2gg.GOODS_GROUP_ID=gg.ID
        WHERE t2gg.ART_ID = $art_id LIMIT 1;");
        $n = (int)$db->num_rows($r);
        if ($n === 1) {
            $id = $db->result($r, 0, "ID");
            $caption = $db->result($r, 0, "NAME");
        }

        return array($id, $caption);
    }

    public function getArticleGoodsGroupTemplateId($art_id, $goods_group_id)
    {
        $db = DbSingleton::getTokoDb();
        $id = 0;
        $r = $db->query("SELECT `TEMPLATE_ID` FROM `T2_GOODS_GROUP_TEMPLATES` WHERE `ART_ID` = $art_id AND `GOODS_GROUP_ID` = $goods_group_id LIMIT 1;");
        $n = (int)$db->num_rows($r);
        if ($n === 1) {
            $id = $db->result($r, 0, "TEMPLATE_ID");
        }
        return $id;
    }

    public function getArticleGoodsGroupTemplateCaption($id)
    {
        $db = DbSingleton::getTokoDb();
        $name = "";
        $r = $db->query("SELECT `NAME` from `GOODS_GROUP_TEMPLATE` WHERE `ID` = $id LIMIT 1;");
        $n = (int)$db->num_rows($r);
        if ($n === 1) {
            $name = $db->result($r, 0, "NAME");
        }
        return $name;
    }

    public function getArticleGoodsGroupTemplateText($id)
    {
        $db = DbSingleton::getTokoDb();
        $name = "";
        $r = $db->query("SELECT `TEXT` FROM `GOODS_GROUP_TEMPLATE` WHERE `ID` = $id LIMIT 1;");
        $n = (int)$db->num_rows($r);
        if ($n === 1) {
            $name = $db->result($r, 0, "TEXT");
        }
        return $name;
    }

    public function getArticleGoodsGroupTemplateDescr($id)
    {
        $db = DbSingleton::getTokoDb();
        $name = "";
        $r = $db->query("SELECT `DESCR` FROM `GOODS_GROUP_TEMPLATE` WHERE `ID` = $id LIMIT 1;");
        $n = (int)$db->num_rows($r);
        if ($n === 1) {
            $name = $db->result($r, 0, "DESCR");
        }
        return $name;
    }

    public function showGoodsGroupTemplateListSelect($goods_group_id, $sel_id): string
    {
        $db = DbSingleton::getTokoDb();
        $list = "";
        $r = $db->query("SELECT `ID`, `NAME` FROM `GOODS_GROUP_TEMPLATE` WHERE `GOODS_GROUP_ID` = $goods_group_id ORDER BY `NAME` ASC, `ID` ASC;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r, $i - 1, "ID");
            $name = $db->result($r, $i - 1, "NAME");
            $sel = ((int)$sel_id === (int)$id) ? "selected='selected'" : "";
            $list .= "<option value='$id' $sel>$name</option>";
        }
        return $list;
    }

    public function getGoodsGroupParamValue($art_id, $param_id)
    {
        $db = DbSingleton::getTokoDb();
        $value = "";
        $r = $db->query("SELECT `PARAM_VALUE` FROM `T2_GOODS_GROUP_PARAMS_VALUE` WHERE `ART_ID` = $art_id AND `PARAM_ID` = $param_id LIMIT 1;");
        $n = (int)$db->num_rows($r);
        if ($n === 1) {
            $value = $db->result($r, 0, "PARAM_VALUE");
        }
        return $value;
    }

    public function getGoodsGroupParamsValueDataList($param_id, $template_id): string
    {
        $db = DbSingleton::getTokoDb();
        $list = "<datalist id='datalist_" . $param_id . "'>";

        $r = $db->query("SELECT `PARAM_VALUE` FROM `T2_GOODS_GROUP_PARAMS_VALUE` 
        WHERE `PARAM_ID` = $param_id AND `TEMPLATE_ID` = $template_id 
        GROUP BY `PARAM_VALUE` ORDER BY `PARAM_VALUE` ASC;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $param_value = $db->result($r, $i - 1, "PARAM_VALUE");
            $list .= "<option value='$param_value'>";
        }
        $list .= "</datalist>";

        return $list;
    }

    public function showGoodsGroupParamsList($art_id, $template_id): string
    {
        $db = DbSingleton::getTokoDb();
        $manual = new manual;
        $list = "";

        $r = $db->query("SELECT `NAME`, `PARAM_ID`, `FIELD_TYPE`, `TYPE` FROM `GOODS_GROUP_TEMPLATE_PARAMS` WHERE `TEMPLATE_ID` = $template_id ORDER BY `PARAM_ID` ASC;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $param_name = $db->result($r, $i - 1, "NAME");
            $param_id = $db->result($r, $i - 1, "PARAM_ID");
            $field_type = (int)$db->result($r, $i - 1, "FIELD_TYPE");
            $type = $db->result($r, $i - 1, "TYPE");
            $param_value = $this->getGoodsGroupParamValue($art_id, $param_id);
            $ftype = "text";
            if ($field_type === 1) {
                $ftype = "text'  min='0' pattern='^[0-9]' onkeypress='return isNumber(event)'  data-bind='value:replyNumber";
                $param_value = str_replace(",", ".", $param_value);
            }
            $tparam = $manual->getManualMCaption("template_param_type", $type);

            $list .= "<tr>
                <td>$i<input type='hidden' id='paramId_$i' value='$param_id'></td>
                <td>$param_name</td>
                <td>" . $manual->getManualMCaption("template_param_field_type", $field_type) . "</td>
                <td><input type='$ftype' class='form-control' list='datalist_" . $param_id . "' id='param_value_" . $param_id . "' value='$param_value'>" . $this->getGoodsGroupParamsValueDataList($param_id, $template_id) . "</td>
                <td>$tparam</td>
            </tr>";
        }
        $list .= "<input type='hidden' id='params_kol' value='$n'>";

        return $list;
    }

    public function showGoodsGroupParamsNameList($template_id): string
    {
        $db = DbSingleton::getTokoDb();
        $manual = new manual;
        $type = 0;
        $list = "";

        $r = $db->query("SELECT `NAME`, `PARAM_ID`, `FIELD_TYPE`, `TYPE` FROM `GOODS_GROUP_TEMPLATE_PARAMS` WHERE `TEMPLATE_ID` = $template_id ORDER BY `PARAM_ID` ASC;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n + 15; $i++) {
            $param_id = 0;
            $param_name = "";
            $field_type = "";

            if ($i <= $n) {
                $param_name = $db->result($r, $i - 1, "NAME");
                $param_id = $db->result($r, $i - 1, "PARAM_ID");
                $field_type = $db->result($r, $i - 1, "FIELD_TYPE");
                $type = $db->result($r, $i - 1, "TYPE");
            }

            $list .= "<tr>
                <td>$i<input type='hidden' id='tmp_param_id_$i' value='$param_id'></td>
                <td><input type='text' class='form-control' id='tmp_param_name_" . $i . "' value='$param_name'></td>
                <td><select class='form-control' size=1 id='tmp_param_field_type_" . $i . "'>" . $manual->showManualSelectList("template_param_field_type", $field_type) . "</select></td>
                <td><select class='form-control' size=1 id='tmp_param_type_" . $i . "'>" . $manual->showManualSelectList("template_param_type", $type) . "</select></td>
            </tr>";
        }

        $list .= "<input type='hidden' id='tmp_params_kol' value='" . ($n + 15) . "'>";

        return $list;
    }

    public function showCatalogueGoodGroupTemplateForm($art_id, $template_id): array
    {
        $form = ""; $form_htm = RD . "/tpl/catalogue_template_form.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $form = str_replace("{art_id}", $art_id, $form);
        $form = str_replace("{template_id}", $template_id, $form);
        $form = str_replace("{template_name}", $this->getArticleGoodsGroupTemplateCaption($template_id), $form);
        $form = str_replace("{template_caption}", $this->getArticleGoodsGroupTemplateText($template_id), $form);
        $form = str_replace("{template_descr}", $this->getArticleGoodsGroupTemplateDescr($template_id), $form);
        [$goods_group_id, $goods_group_caption] = $this->getArticleGoodsGroup($art_id);
        $form = str_replace("{goods_group_id}", $goods_group_id, $form);
        $form = str_replace("{goods_group_caption}", $goods_group_caption, $form);
        $form = str_replace("{params_list}", $this->showGoodsGroupParamsNameList($template_id), $form);
        $form = str_replace("{scheme_list}", $this->loadArticleScheme($template_id, 0), $form);

        return array($form, "Редагування шаблону");
    }

    public function loadArticleParams($art_id) {
        $form = ""; $form_htm = RD. "/tpl/catalogue_params.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $form = str_replace("{art_id}", $art_id, $form);
        [$goods_group_id, $goods_group_caption] = $this->getArticleGoodsGroup($art_id);
        $form = str_replace("{goods_group_id}", $goods_group_id, $form);
        $form = str_replace("{goods_group_caption}", $goods_group_caption, $form);
        $template_id = $this->getArticleGoodsGroupTemplateId($art_id, $goods_group_id);
        $form = str_replace("{goods_group_template_list}", $this->showGoodsGroupTemplateListSelect($goods_group_id, $template_id), $form);
        $form = str_replace("{params_list}", $this->showGoodsGroupParamsList($art_id, $template_id), $form);
        $form = str_replace("{scheme_list}", $this->loadArticleScheme($template_id, 1), $form);

        return $form;
    }

    /*
     * SHOW ARTICLE INFO
     */
    public function addArticleInfo($art_id, $lang_id, $text, $value, $sort): array
    {
        $db = DbSingleton::getTokoDb();
        $answer = 0; $err = "Помилка збереження даних!";
        if ($art_id > 0) {
            $db->query("INSERT INTO `T2_INFO` (`ART_ID`, `LANG_ID`, `TEXT`, `VALUE`, `SORT`) VALUES ('$art_id', '$lang_id', '$text', '$value', '$sort');");
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

    public function loadArticleInfo($art_id)
    {
        $db = DbSingleton::getTokoDb();
        $list = "";
        $form = ""; $form_htm = RD . "/tpl/catalogue_article_info.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }

        $r = $db->query("SELECT `ID`, `TEXT`, `VALUE`, `SORT`, `LANG_ID` FROM `T2_INFO` WHERE `ART_ID` = $art_id ORDER BY `LANG_ID`, `SORT` ASC;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $ID             = $db->result($r, $i - 1, "ID");
            $TEXT           = $db->result($r, $i - 1, "TEXT");
            $VALUE          = $db->result($r, $i - 1, "VALUE");
            $SORT           = $db->result($r, $i - 1, "SORT");
            $LANG_ID        = $db->result($r, $i - 1, "LANG_ID");
            $lang_select    = "<select class='form-control' disabled>" . $this->getFullLangList($LANG_ID) . "</select>";

            $list .= "<div class='row' style='padding-bottom:15px;'>
                <div class='col-lg-2'><input id='info_text-$ID' type='text' value='$TEXT' class='form-control'></div>
                <div class='col-lg-2'><input id='info_value-$ID' type='text' value='$VALUE' class='form-control'></div>
                <div class='col-lg-2'><input id='info_sort-$ID' type='number' value='$SORT' class='form-control'></div>
                <div class='col-lg-2'>$lang_select</div>
                <div class='col-lg-2'>
                    <button class='btn btn-primary' onClick=\"saveArticleInfo('$ID');\"><i class='fa fa-save'></i> Зберегти</button>
                    <button class='btn btn-danger' onClick=\"dropArticleInfo('$ID');\"><i class='fa fa-trash'></i></button>
                </div>
            </div>";
        }

        if ($n === 0) {
            $list = "";
        }

        $form = str_replace(array("{short_cap}", "{article_info}", "{add_info}", "{lang_info}"), array("", $list, "addArticleInfo($art_id);", $this->getFullLangList()), $form);

        return $form;
    }

    public function saveArticleInfo($id, $text, $value, $sort): array
    {
        $db = DbSingleton::getTokoDb();
        $answer = 0; $err = "Помилка збереження даних!";
        if ($id > 0) {
            $db->query("UPDATE `T2_INFO` SET `TEXT` = '$text', `VALUE` = '$value', `SORT` = '$sort' WHERE `ID` = $id LIMIT 1;");
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

    public function dropArticleInfo($id): array
    {
        $db = DbSingleton::getTokoDb();
        $answer = 0; $err = "Помилка збереження даних!";
        if ($id > 0) {
            $db->query("DELETE FROM `T2_INFO` WHERE `ID` = $id LIMIT 1;");
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

    /*
     * SHOW ARTICLE SHORT INFO
     */
    public function addArticleShortInfo($art_id, $lang_id, $text, $value, $sort): array
    {
        $db = DbSingleton::getTokoDb();
        $answer = 0; $err = "Помилка збереження даних!";
        if ($art_id > 0) {
            $db->query("INSERT INTO `T2_SHORT_INFO` (`ART_ID`, `LANG_ID`, `TEXT`, `VALUE`, `SORT`) VALUES ('$art_id', '$lang_id', '$text', '$value', '$sort');");
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

    public function loadArticleShortInfo($art_id)
    {
        $db = DbSingleton::getTokoDb();
        $list = "";
        $form = ""; $form_htm = RD . "/tpl/catalogue_article_info.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }

        $r = $db->query("SELECT `ID`, `TEXT`, `VALUE`, `SORT`, `LANG_ID` FROM `T2_SHORT_INFO` WHERE `ART_ID` = $art_id ORDER BY `LANG_ID`, `SORT` ASC;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $ID             = $db->result($r, $i - 1, "ID");
            $TEXT           = $db->result($r, $i - 1, "TEXT");
            $VALUE          = $db->result($r, $i - 1, "VALUE");
            $SORT           = $db->result($r, $i - 1, "SORT");
            $LANG_ID        = $db->result($r, $i - 1, "LANG_ID");
            $lang_select    = "<select class='form-control' disabled>" . $this->getFullLangList($LANG_ID) . "</select>";

            $list .= "<div class='row' style='padding-bottom:15px;'>
                <div class='col-lg-2'><input id='short_info_text-$ID' type='text' value='$TEXT' class='form-control'></div>
                <div class='col-lg-2'><input id='short_info_value-$ID' type='text' value='$VALUE' class='form-control'></div>
                <div class='col-lg-2'><input id='short_info_sort-$ID' type='number' value='$SORT' class='form-control'></div>
                <div class='col-lg-2'>$lang_select</div>
                <div class='col-lg-2'>
                    <button class='btn btn-primary' onClick=\"saveArticleShortInfo('$ID');\"><i class='fa fa-save'></i> Зберегти</button>
                    <button class='btn btn-danger' onClick=\"dropArticleShortInfo('$ID');\"><i class='fa fa-trash'></i></button>
                </div>
            </div>";
        }

        if ($n === 0) {
            $list = "";
        }

        $form = str_replace(array("{short_cap}", "{article_info}", "{add_info}", "{lang_info}"), array("short_", $list, "addArticleShortInfo($art_id);", $this->getFullLangList()), $form);

        return $form;
    }

    public function saveArticleShortInfo($id, $text, $value, $sort): array
    {
        $db = DbSingleton::getTokoDb();
        $answer = 0; $err = "Помилка збереження даних!";
        if ($id > 0) {
            $db->query("UPDATE `T2_SHORT_INFO` SET `TEXT` = '$text', `VALUE` = '$value', `SORT` = '$sort' WHERE `ID` = $id LIMIT 1;");
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

    public function dropArticleShortInfo($id): array
    {
        $db = DbSingleton::getTokoDb();
        $answer = 0; $err = "Помилка збереження даних!";
        if ($id > 0) {
            $db->query("DELETE FROM `T2_SHORT_INFO` WHERE `ID` = $id LIMIT 1;");
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

    public function getLangList($lang_id = ""): string
    {
        $db = DbSingleton::getTokoDb();
        $list = "";

        $r = $db->query("SELECT `caption`, `lang_tcd` FROM `lang` WHERE `on` = 1;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $caption = $db->result($r, $i - 1, "caption");
            $lang_tcd = $db->result($r, $i - 1, "lang_tcd");
            $selected = ($lang_id == $lang_tcd) ? "selected" : "";
            $list .= "<option value='$lang_tcd' $selected>$caption</option>";
        }

        return $list;
    }

    public function getFullLangList($lang_id = ""): string
    {
        $db = DbSingleton::getTokoDb();
        $list = "";

        $r = $db->query("SELECT `caption`, `lang_tcd` FROM `lang` WHERE 1;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $caption    = $db->result($r, $i - 1, "caption");
            $lang_tcd   = $db->result($r, $i - 1, "lang_tcd");
            $selected   = ((int)$lang_id === (int)$lang_tcd) ? "selected" : "";

            $list .= "<option value='$lang_tcd' $selected>$caption</option>";
        }

        return $list;
    }

    public function loadCatalogueGoodGroupTemplateParams($art_id, $template_id): array
    {
        return array($this->showGoodsGroupParamsList($art_id, $template_id), $this->loadArticleScheme($template_id, 1));
    }

    public function saveCatalogueParams($art_id, $goods_group_id, $template_id, $params_value): array
    {
        $db = DbSingleton::getTokoDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка збереження даних!";
        $art_id = $slave->qq($art_id); $goods_group_id = $slave->qq($goods_group_id); $template_id = $slave->qq($template_id); $params_value = $slave->qq($params_value);

        if ($art_id > 0) {
            $r = $db->query("SELECT * FROM `T2_GOODS_GROUP_TEMPLATES` WHERE `ART_ID` = $art_id AND `GOODS_GROUP_ID` = $goods_group_id LIMIT 1;");
            $n = (int)$db->num_rows($r);
            if ($n === 0) {
                $db->query("INSERT INTO `T2_GOODS_GROUP_TEMPLATES` (`ART_ID`, `GOODS_GROUP_ID`, `TEMPLATE_ID`) VALUES ('$art_id', '$goods_group_id', '$template_id');");
            }
            if ($n === 1) {
                $db_template_id = (int)$db->result($r, 0, "TEMPLATE_ID");

                if ($db_template_id !== (int)$template_id) {
                    $db->query("UPDATE `T2_GOODS_GROUP_TEMPLATES` SET `TEMPLATE_ID`='$template_id' WHERE `ART_ID` = $art_id AND `GOODS_GROUP_ID` = $goods_group_id;");
                    $db->query("DELETE FROM `T2_GOODS_GROUP_PARAMS_VALUE` WHERE `ART_ID` = $art_id AND `TEMPLATE_ID` = $db_template_id;");
                }
            }
            $r2 = $db->query("SELECT `PARAM_ID` FROM `GOODS_GROUP_TEMPLATE_PARAMS` WHERE `TEMPLATE_ID` = $template_id ORDER BY `PARAM_ID` ASC;");
            $n2 = (int)$db->num_rows($r2);
            for ($i = 1; $i <= $n2; $i++) {
                $param_id = $db->result($r2, $i - 1, "PARAM_ID");

                $r = $db->query("SELECT `PARAM_VALUE` FROM `T2_GOODS_GROUP_PARAMS_VALUE` WHERE `ART_ID` = $art_id AND `PARAM_ID` = $param_id LIMIT 1;");
                $n = (int)$db->num_rows($r);
                if ($n === 1) {
                    $value = $db->result($r, 0, "PARAM_VALUE");

                    if ($params_value[$param_id] != $value) {
                        $db->query("UPDATE `T2_GOODS_GROUP_PARAMS_VALUE` SET `TEMPLATE_ID`='$template_id', `PARAM_VALUE`='".$params_value[$param_id]."' WHERE `ART_ID` = $art_id AND `PARAM_ID` = $param_id;");
                    }
                }

                if ($n === 0) {
                    $db->query("INSERT INTO `T2_GOODS_GROUP_PARAMS_VALUE` (`ART_ID`,`TEMPLATE_ID`,`PARAM_ID`,`PARAM_VALUE`) VALUES ('$art_id','$template_id','$param_id','".$params_value[$param_id]."');");
                }

                $answer = 1; $err = "";
            }

            if ($n2 === 0) {
                $answer = 1; $err = "";
            }
        }

        return array($answer, $err);
    }

    public function saveCatalogueParamsTemplate($art_id, $goods_group_id, $template_id, $template_name, $template_caption, $template_descr, $cn, $params_id, $fields_type, $params_name, $params_type): array
    {
        $db = DbSingleton::getTokoDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка збереження даних!";
        $art_id=$slave->qq($art_id); $goods_group_id=$slave->qq($goods_group_id); $template_id=$slave->qq($template_id); $template_name=$slave->qq($template_name); $cn=$slave->qq($cn);
        $fields_type=$slave->qq($fields_type); $params_name=$slave->qq($params_name); $params_type=$slave->qq($params_type);

        if ($art_id > 0) {

            if ((int)$template_id === 0) {
                $r = $db->query("SELECT MAX(`ID`) as mid FROM `GOODS_GROUP_TEMPLATE`;");
                $template_id = $db->result($r, 0, "mid") + 1;
                $db->query("INSERT INTO `GOODS_GROUP_TEMPLATE` (`ID`,`GOODS_GROUP_ID`,`NAME`,`TEXT`,`DESCR`) VALUES ('$template_id','$goods_group_id','$template_name','$template_caption','$template_descr');");
            }

            if ($template_id > 0) {
                $db->query("UPDATE `GOODS_GROUP_TEMPLATE` SET `NAME`='$template_name', `TEXT`='$template_caption', `DESCR`='$template_descr' WHERE `ID`='$template_id' AND `GOODS_GROUP_ID`='$goods_group_id';");
                for ($i = 1; $i <= $cn; $i++) {
                    $param_id   = $params_id[$i];
                    $param_name = $params_name[$i];
                    $field_type = $fields_type[$i];
                    $param_type = $params_type[$i];

                    if ((int)$param_id === 0 && $param_name !== "") {
                        $db->query("INSERT INTO `GOODS_GROUP_TEMPLATE_PARAMS` (`PARAM_ID`,`TEMPLATE_ID`,`NAME`,`FIELD_TYPE`,`TYPE`) VALUES ('','$template_id','$param_name','$field_type','$param_type');");
                    }

                    if ($param_id > 0) {
                        $db->query("UPDATE `GOODS_GROUP_TEMPLATE_PARAMS` SET `NAME`='$param_name', `FIELD_TYPE`='$field_type', `TYPE`='$param_type' WHERE `PARAM_ID` = $param_id AND `TEMPLATE_ID` = $template_id;");
                    }
                }

                $answer = 1; $err = "";
            }
        }

        return array($answer, $err);
    }

    public function showCatalogueAnalogIndexSearch(): array
    {
        $form = ""; $form_htm = RD . "/tpl/catalogue_analog_search.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        return array($form, "Пошук аналогу по індексу");
    }

    public function findCatalogueAnalogIndexSearch($index): string
    {
        $db = DbSingleton::getTokoDb();
        $slave = new slave;
        $list = "";
        $index = $slave->qq($index);

        if ($index !== "") {
            $list = "";

            $r = $db->query("SELECT t2a.ART_ID, t2a.BRAND_ID, t2a.ARTICLE_NR_DISPL, t2b.BRAND_NAME, t2n.NAME 
            FROM `T2_ARTICLES` t2a 
                LEFT OUTER JOIN `T2_BRANDS` t2b on t2b.BRAND_ID=t2a.BRAND_ID 
                LEFT OUTER JOIN `T2_NAMES` t2n on t2n.ART_ID=t2a.ART_ID
            WHERE t2a.ARTICLE_NR_SEARCH='$index' OR t2a.ARTICLE_NR_DISPL='$index';");
            $n = (int)$db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $art_id = $db->result($r, $i - 1, "ART_ID");
                $article_nr_displ = $db->result($r, $i - 1, "ARTICLE_NR_DISPL");
                $brand_id = $db->result($r, $i - 1, "BRAND_ID");
                $brand_name = $db->result($r, $i - 1, "BRAND_NAME");
                $name = $db->result($r, $i - 1, "NAME");

                $list .= "<tr style=\"cursor:pointer;\" onClick='setAnalogSearchIndex(\"$art_id\",\"$article_nr_displ\",\"$brand_id\");'>
                    <td>$art_id</td>
                    <td>$article_nr_displ</td>
                    <td>$brand_name</td>
                    <td>$name</td>
                </tr>";
            }
        }

        return $list;
    }

    public function showCatalogueAnalogForm($art_id, $kind, $relation, $search_number): array
    {
        $db = DbSingleton::getTokoDb();
        $form = ""; $form_htm = RD . "/tpl/catalogue_analog_form.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $form = str_replace("{art_id}", $art_id, $form);
        $form = str_replace("{kind}", $kind, $form);
        $form = str_replace("{relation}", $relation, $form);
        $form = str_replace("{search_number}", $search_number, $form);

        $r = $db->query("SELECT `DISPLAY_NR`, `BRAND_ID` FROM `T2_CROSS` WHERE `ART_ID` = $art_id AND `KIND` = $kind AND `SEARCH_NUMBER` = '$search_number' LIMIT 1;");
        $display_nr = $db->result($r, 0, "DISPLAY_NR");
        $brand_id = $db->result($r, 0, "BRAND_ID");
        $form = str_replace("{display_nr}", $display_nr, $form);
        $form = str_replace("{brand_list}", $this->showBrandListSelect($brand_id), $form);

        return array($form, "Редагування аналогу" . ((int)$kind === 4) ? " інший номер" : " ОЕ номер");
    }

    public function saveCatalogueAnalogForm($art_id, $kind, $relation, $search_number, $display_nr, $brand_id, $art_id2, $index2): array
    {
        $db = DbSingleton::getTokoDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка збереження даних!";
        $art_id=$slave->qq($art_id); $kind=$slave->qq($kind); $relation=$slave->qq($relation); $search_number=$slave->qq($search_number); $display_nr=$slave->qq($display_nr); $brand_id=$slave->qq($brand_id);
        $art_id2=$slave->qq($art_id2); $index2=$slave->qq($index2);

        if ($art_id > 0 && $kind > 0 && $relation >= 0 && $display_nr !== "" && $brand_id > 0) {
            $old_search_number = $search_number;
            $search_number = $this->clearArticle($display_nr);
            $search_number_up = strtoupper($search_number);
            $old_kind = $kind;
            $new_kind = $this->getBrandKind($brand_id);

            if ($new_kind !== $kind && $new_kind > 0) {
                $kind = $new_kind;
            }

            if ($old_search_number === "") {
                $db->query("INSERT INTO `T2_CROSS` (`ART_ID`,`SEARCH_NUMBER`,`KIND`,`BRAND_ID`,`DISPLAY_NR`,`RELATION`) VALUES ('$art_id',N'$search_number_up','$kind','$brand_id',N'$display_nr','$relation');");
            }

            if ($old_search_number !== "") {
                $db->query("UPDATE `T2_CROSS` SET `SEARCH_NUMBER`='$search_number_up', `DISPLAY_NR`='$display_nr', `BRAND_ID`='$brand_id', `KIND`='$kind', `RELATION`='$relation' WHERE `ART_ID` = $art_id AND `SEARCH_NUMBER` = '$old_search_number' AND `KIND` = $old_kind;");
            }

            if ($art_id2 !== "" && $index2 !== "") {
                $er = 0;
                $index2_cl = $this->clearArticle($index2);

                if ((int)$relation === 1) {
                    $relation = 2;
                    $er = 1;
                }
                if ((int)$relation === 2 && $er === 0) {
                    $relation = 1;
                }

                $brand_id = $this->getBrandId($art_id);
                $db->query("INSERT INTO `T2_CROSS` (`ART_ID`,`SEARCH_NUMBER`,`KIND`,`BRAND_ID`,`DISPLAY_NR`,`RELATION`) VALUES ('$art_id2',N'$index2_cl','$kind','$brand_id',N'$index2','$relation');");
            }

            $answer = 1; $err = "";
        }

        return array($answer, $err);
    }

    public function dropCatalogueAnalog($art_id,$kind,$relation,$search_number,$brand_id): array
    {
        $db = DbSingleton::getTokoDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка збереження даних!";
        $art_id=$slave->qq($art_id); $kind=$slave->qq($kind); $relation=$slave->qq($relation); $search_number=$slave->qq($search_number); $brand_id=$slave->qq($brand_id);

        if ($art_id > 0 && $kind > 0 && $relation >= 0 && $search_number !== "" && $brand_id > 0) {
            $db->query("DELETE FROM `T2_CROSS` WHERE `ART_ID` = $art_id AND `SEARCH_NUMBER` = '$search_number' AND `KIND` = $kind AND `BRAND_ID` = $brand_id AND `RELATION` = $relation LIMIT 1;");
            $answer = 1; $err = "";
        }

        return array($answer, $err);
    }

    public function clearCatalogueAnalogArticle($art_id, $kind, $relation): array
    {
        $db = DbSingleton::getTokoDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка збереження даних!";
        $art_id = $slave->qq($art_id); $kind = $slave->qq($kind); $relation = $slave->qq($relation);

        if ($art_id > 0 && $kind > 0 && $relation >= 0) {
            $db->query("DELETE FROM `T2_CROSS` WHERE `ART_ID` = $art_id AND `KIND` = $kind AND `RELATION` = $relation;");
            $answer = 1; $err = "";
        }

        return array($answer, $err);
    }

    public function loadArticleAnalogs($art_id){$db = DbSingleton::getTokoDb();
        $ak1 = $ak2 = $ak3 = $ak4 = $ak5 = "";
        $form = ""; $form_htm = RD . "/tpl/catalogue_analog_block.htm";
        if (file_exists($form_htm)){ $form = file_get_contents($form_htm);}
        $r = $db->query("SELECT t2c.*, t2b.BRAND_NAME 
        FROM `T2_CROSS` t2c 
            LEFT OUTER JOIN `T2_BRANDS` t2b ON (t2b.BRAND_ID=t2c.BRAND_ID)  
        WHERE t2c.ART_ID = $art_id AND t2c.KIND IN (3,4) 
        ORDER BY t2c.KIND ASC;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $search_number = $db->result($r, $i - 1, "SEARCH_NUMBER");
            $display_nr = trim($db->result($r, $i - 1, "DISPLAY_NR"));
            $kind = $db->result($r, $i - 1, "KIND");
            $brand_id = $db->result($r, $i - 1, "BRAND_ID");
            $brand_name = $db->result($r, $i - 1, "BRAND_NAME");
            $relation = $db->result($r, $i - 1, "RELATION");

            $row = "<tr>
                <td>$search_number</td>
                <td><strong>$display_nr</strong></td>
                <td>$brand_name</td>
                <td>$brand_id</td>
                <td align='center'>
                    <button class='btn btn-xs btn-warning btn-bitbucket' onClick='showCatalogueAnalogForm(\"$art_id\",\"$kind\",\"$relation\",\"$search_number\");'><i class='fa fa-edit'></i></button>
                    <button class='btn btn-xs btn-danger btn-bitbucket' onClick='dropCatalogueAnalog(\"$art_id\",\"$kind\",\"$relation\",\"$search_number\",\"$brand_id\",\"$display_nr\");'><i class='fa fa-trash'></i></button>
                </td>
            </tr>";

            if ($kind == 3 && $relation == 0) { $ak1 .= $row; }
            if ($kind == 4 && $relation == 0) { $ak2 .= $row; }
            if (($kind == 3 || $kind == 4) && $relation == 1) { $ak3 .= $row; }
            if (($kind == 3 || $kind == 4) && $relation == 2) { $ak4 .= $row; }
            if (($kind == 3 || $kind == 4) && $relation == 3) { $ak5 .= $row; }
        }

        $form = str_replace(array("{analog_list_1}", "{analog_list_2}", "{analog_list_3}", "{analog_list_4}", "{analog_list_5}", "{art_id}"), array($ak1, $ak2, $ak3, $ak4, $ak5, $art_id), $form);

        return $form;
    }

    public function loadArticleAplicability($art_id)
    {
        $db = DbSingleton::getTokoDb();
        $tab_list = "";
        $typ_id_str = "";

        $form = ""; $form_htm = RD . "/tpl/catalogue_aplicability_block.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }

        $r = $db->query("SELECT `TYP_ID` FROM `T2_LINKS` WHERE `ART_ID` = $art_id;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $typ_id = $db->result($r, $i - 1, "TYP_ID");
            $typ_id_str .= $typ_id;
            if ($i < $n) {
                $typ_id_str .= ",";
            }
        }

        if ($typ_id_str !== "") {
            $r = $db->query("SELECT man.MFA_ID, man.MFA_BRAND 
            FROM `T_types` tt 
                INNER JOIN `T_models` tm on tm.MOD_ID=tt.TYP_MOD_ID 
                INNER JOIN `T_manufacturers` man on man.MFA_ID=tm.MOD_MFA_ID 
            WHERE tt.TYP_ID in ($typ_id_str) 
            GROUP BY man.MFA_ID ORDER BY man.MFA_BRAND ASC;");
            $n = (int)$db->num_rows($r);
            $tab_list = ""; $cont_list = "";
            for ($i = 1; $i <= $n; $i++) {
                $mfa_id = $db->result($r, $i - 1, "MFA_ID");
                $mfa_name = $db->result($r, $i - 1, "MFA_BRAND");
                $tab_list .= "<li><a data-toggle=\"tab\" href=\"#aplic_tab".$mfa_id."\" onClick=\"loadArticleAplicabilityModels('$art_id','$mfa_id');\"><i class=\"fa fa-car\"></i> $mfa_name</a></li>\n";
                $cont_list .= "<div id=\"aplic_tab".$mfa_id."\" class=\"tab-pane\">
                    <div class=\"panel-body\">
                        <div class=\"sk-spinner sk-spinner-wave\"><div class=\"sk-rect1\"></div><div class=\"sk-rect2\"></div><div class=\"sk-rect3\"></div><div class=\"sk-rect4\"></div><div class=\"sk-rect5\"></div></div>
                    </div>
                </div>\n";
            }
        } else {
            $cont_list = "<h3 class='text-center'>Привязка до авто відсутня</h3>";
        }

        $tab_list .= "<a href=\"#aplic_new\" class='btn btn-primary pull-right' onClick=\"loadArticleAplicabilityNew('$art_id');\"><i class=\"fa fa-plus\"></i></a>";
        $form = str_replace(array("{manuf_tab_list}", "{manuf_cont_list}"), array($tab_list, $cont_list), $form);

        return $form;
    }

    /*==== TREE NEW ====*/
    public function loadArticleTree($art_id) {
        $form = ""; $form_htm = RD . "/tpl/catalogue_article_tree.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $form = str_replace(array("{tree_tecdoc}", "{tree_new}", "{tree_exist}"), array($this->getTreeArticleTecdoc($art_id), $this->getTreeArticleNew($art_id), $this->getTreeArticleExist($art_id)), $form);

        return $form;
    }

    public function getTreeArticleExist($art_id): string
    {
        $db = DbSingleton::getTokoDb();
        $list = "<ul>";
        $r = $db->query("SELECT tg.GROUP_ID, tg.TEX_RU 
        FROM `T2_TREE_ARTS_EXIST` ta 
            LEFT JOIN `T2_TREE_GROUP_EXIST` tg ON (tg.GROUP_ID = ta.GROUP_ID)
        WHERE ta.`ART_ID` = $art_id 
        ORDER BY ta.`GROUP_ID` ASC");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $group_id = $db->result($r, $i - 1, "GROUP_ID");
            $tex_text = $db->result($r, $i - 1, "TEX_RU");
            if ($group_id > 0) {
                $list .= "
                <li><a class=\"btn btn-danger btn-xs\" onclick=\"dropArticleTreeExist('$art_id', '$group_id');\"><i class='fa fa-trash'></i></a> $group_id - $tex_text</li>";
            }
        }
        $list .= "</ul>";

        return $list;
    }

    public function saveArticleTreeExist($art_id, $brand_id, $group_id): bool
    {
        $db = DbSingleton::getTokoDb();
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $r = $db->query("SELECT * FROM `T2_TREE_ARTS_EXIST` WHERE `ART_ID` = $art_id AND `GROUP_ID` = $group_id LIMIT 1;");
        $n = (int)$db->num_rows($r);
        if ($n === 0) {
            $db->query("INSERT INTO `T2_TREE_ARTS_EXIST` (`ART_ID`, `BRAND_ID`, `GROUP_ID`, `USER_ID`) VALUES ('$art_id', '$brand_id', '$group_id', '$user_id');");
        }
        return true;
    }

    public function getTreeArticleNew($art_id): string
    {
        $db = DbSingleton::getTokoDb();
        $list = "<ul>";
        $r = $db->query("SELECT tg.GROUP_ID, tg.TEX_RU 
        FROM `T2_TREE_ARTS` tt 
            LEFT JOIN `T2_TREE_GROUP` tg ON (tg.GROUP_ID=tt.GROUP_ID)
        WHERE tt.`ART_ID` = $art_id 
        ORDER BY tt.`GROUP_ID` ASC");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $group_id = (int)$db->result($r, $i - 1, "GROUP_ID");
            $tex_text = $db->result($r, $i - 1, "TEX_RU");

            if ($group_id > 0) {
                $list .= "<li><a class=\"btn btn-danger btn-xs\" onclick=\"dropArticleTreeTecdoc('$art_id', '$group_id');\"><i class='fa fa-trash'></i></a> $group_id - $tex_text</li>";
            }
        }
        $list .= "</ul>";

        return $list;
    }

    public function getTreeArticleTecdoc($art_id): string
    {
        $db = DbSingleton::getTokoDb();
        $list = "<ul>";
        $r = $db->query("SELECT tg.STR_ID, tg.TEX_TEXT, tg.STR_ID_PARENT 
        FROM `T2_TREE` tt 
            LEFT JOIN `T2_GROUP_TREE` tg ON (tg.STR_ID = tt.STR_ID AND tg.LNG_ID = 16)
        WHERE tt.`ART_ID` = $art_id 
        ORDER BY tt.`STR_ID` ASC;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $str_id = $db->result($r, $i - 1, "STR_ID");
            $str_id_parent = $db->result($r, $i - 1, "STR_ID_PARENT");
            $tex_text = $db->result($r, $i - 1, "TEX_TEXT");
            $parent = "";
            if ($str_id_parent != 0) {
                $parent =  "(".$this->getStrName($str_id_parent).")";
            }
            if ($str_id != 0) {
                $list .= "<li><a class=\"btn btn-danger btn-xs\" onclick=\"dropArticleTreeNew('$art_id', '$str_id');\"><i class='fa fa-trash'></i></a> $str_id - $tex_text $parent</li>";
            }
        }
        $list .= "</ul>";

        return $list;
    }

    public function dropArticleTreeTecdoc($art_id, $str_id): bool
    {
        $db = DbSingleton::getTokoDb();
        $db->query("DELETE FROM `T2_TREE` WHERE `ART_ID` = $art_id AND `STR_ID` = $str_id LIMIT 1;");
        return true;
    }

    public function dropArticleTreeNew($art_id, $group_id): bool
    {
        $db = DbSingleton::getTokoDb();
        $db->query("DELETE FROM `T2_TREE_ARTS` WHERE `ART_ID` = $art_id AND `GROUP_ID` = $group_id LIMIT 1;");
        return true;
    }

    public function showArticleTreeTecdoc() {
        $form = ""; $form_htm = RD . "/tpl/catalogue_article_tree_new.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $form = str_replace("{tree_range}", $this->getArticleTreeTecdoc(), $form);
        return $form;
    }

    public function showArticleTreeNew() {
        $form = ""; $form_htm = RD . "/tpl/catalogue_article_tree_new.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $form = str_replace("{tree_range}", $this->getArticleTreeNew(), $form);
        return $form;
    }

    public function showArticleTreeExist() {
        $form = ""; $form_htm = RD . "/tpl/catalogue_article_tree_exist.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $form = str_replace("{tree_range}", $this->getArticleTreeExist(), $form);
        return $form;
    }

    public function dropArticleTreeExist($art_id, $group_id): bool
    {
        $db = DbSingleton::getTokoDb();
        $db->query("DELETE FROM `T2_TREE_ARTS_EXIST` WHERE `ART_ID` = $art_id AND `GROUP_ID` = $group_id LIMIT 1;");
        return true;
    }

    public function getArticleTreeExist(): string
    {
        $db = DbSingleton::getTokoDb();
        $list = "
        <input class=\"text-filter\" type=\"text\" id=\"searchInput\" onkeyup=\"searchCatInput();\" placeholder=\"Пошук по назві\" title=\"{search_by_brand}\">";
        $list .= "
        <ul style='list-style:none; padding:0;'>";

        $r = $db->query("SELECT `HEAD_ID`, `TEX_RU` FROM `T2_TREE_HEAD_EXIST` WHERE 1;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $head_id        = $db->result($r, $i - 1, "HEAD_ID");
            $text           = $db->result($r, $i - 1, "TEX_RU");
            $header_list    = $this->getArticleTreeExistList($head_id);

            $list .= "
            <li>
				<div class='tree-head pointer'>
					$head_id. $text
				</div>
				<div class='tree-list dnone'>$header_list</div>
			</li>";
        }
        $list .= "</ul>";

        return $list;
    }

    public function getArticleTreeExistList($head_id): string
    {
        $db = DbSingleton::getTokoDb();
        $arr = []; $list = "";
        $r = $db->query("SELECT `CAT_ID`, `GROUP_ID` FROM `T2_TREE_HCG_EXIST` WHERE `HEAD_ID` = $head_id ORDER BY `CAT_ID`;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $cat_id     = $db->result($r, $i - 1, "CAT_ID");
            $group_id   = $db->result($r, $i - 1, "GROUP_ID");

            if (empty($arr[$cat_id])) {
                $arr[$cat_id] = [];
            }
            $arr[$cat_id][] = $group_id;
        }

        foreach ($arr as $cat_id => $group_ids) {
            $group_arr = [];
            $cat_name = $this->getCatExistName($cat_id);
            $list .= "
            <div class=\"tree-category\"><i>$cat_name</i></div>";
            $list .= "
            <ul class=\"group-tree\">";

            foreach ($group_ids as $group_id) {
                $group_name = $this->getGroupExistName($group_id);
                $group_arr[$group_id] = ["group_id" => $group_id, "name" => $group_name];
            }

            usort($group_arr, function($a, $b) {
                return $a['name'] <=> $b['name'];
            });

            foreach ($group_arr as $key=>$value) {
                $group_id   = $value["group_id"];
                $group_name = $value["name"];

                $list .= "
                <li><a class='btn btn-primary btn-xs' onclick='saveArticleTreeExist(\"$group_id\")'>Привязати</a> $group_name</li>";
            }
            $list .= "</ul>";
        }

        if ($n === 0) {
            $list = "Пусто";
        }

        return $list;
    }

    public function getArticleTreeNew(): string
    {
        $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT `HEAD_ID`, `TEX_RU` FROM `T2_TREE_HEAD` WHERE 1;");
        $n = (int)$db->num_rows($r);
        $list = "<input class=\"text-filter\" type=\"text\" id=\"searchInput\" onkeyup=\"searchCatInput();\" placeholder=\"Пошук по назві\" title=\"{search_by_brand}\">";
        $list .= "<ul style='list-style:none; padding:0;'>";
        for ($i = 1; $i <= $n; $i++) {
            $head_id = $db->result($r, $i - 1, "HEAD_ID");
            $text = $db->result($r, $i - 1, "TEX_RU");
            $header_list = $this->getArticleTreeNewList($head_id);
            $list .= "<li>
				<div class='tree-head pointer'>
					$head_id. $text
				</div>
				<div class='tree-list dnone'>$header_list</div>
			</li>";
        }
        $list .= "</ul>";

        return $list;
    }

    public function getArticleTreeNewList($head_id): string
    {
        $db = DbSingleton::getTokoDb();
        $arr = []; $list = "";
        $r = $db->query("SELECT `CAT_ID`, `GROUP_ID` FROM `T2_TREE_HCG` WHERE `HEAD_ID` = $head_id ORDER BY `CAT_ID`;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $cat_id = $db->result($r, $i - 1, "CAT_ID");
            $group_id = $db->result($r, $i - 1, "GROUP_ID");
            if (empty($arr[$cat_id])) {
                $arr[$cat_id] = [];
            }
            $arr[$cat_id][] = $group_id;
        }

        foreach ($arr as $cat_id => $group_ids) {
            $group_arr = [];
            $cat_name = $this->getCatName($cat_id);
            $list .= "<div class=\"tree-category\"><i>$cat_name</i></div>";
            $list .= "<ul class='group-tree'>";
            foreach ($group_ids as $group_id) {
                $group_name = $this->getGroupName($group_id);
                $group_arr[$group_id] = ["group_id" => $group_id, "name" => $group_name];
            }
            usort($group_arr, function($a, $b) {
                return $a['name'] <=> $b['name'];
            });
            foreach ($group_arr as $key=>$value) {
                $group_id = $value["group_id"];
                $group_name = $value["name"];
                $list .= "<li><a class='btn btn-primary btn-xs' onclick='saveArticleTreeNew(\"$group_id\")'>Привязати</a> $group_name</li>";
            }
            $list .= "</ul>";
        }

        if ($n === 0) {
            $list = "Пусто";
        }

        return $list;
    }

    public function getArticleTreeTecdoc(): string
    {
        $db = DbSingleton::getTokoDb();
        $lvl = 0;
        $menu_det = $tree = "";
        $td_array = $parent_fare = $position_fare = [];

        $menu_det .= "<div class=\"input-group border0\">
		<input type=\"search\" id=\"my-search\" class=\"my-search form-control\" placeholder=\"Поиск по категориям\" style='margin-bottom: 15px;'>
		</div><ul id=\"my-tree\" class=\"tf-tree\">";

        $r = $db->query("SELECT `STR_ID`, `STR_ID_PARENT`, `STR_LEVEL`, `DISP_TEXT`, `POSITION` FROM `T2_GROUP_TREE` WHERE `LNG_ID` = 16;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $str_id = $db->result($r, $i - 1, "STR_ID");
            $str_id_parrent = $db->result($r, $i - 1, "STR_ID_PARENT");
            if ($str_id_parrent === "") {
                $str_id_parrent = 0;
            }
            $str_level = $db->result($r, $i - 1, "STR_LEVEL");
            $tex_text = $db->result($r, $i - 1, "DISP_TEXT");
            $position = $db->result($r, $i - 1, "POSITION");
            $child = $this->getTecGroupTreeChilds($str_id);
            $td_array[$i]["id_tree"] = $str_id;
            $td_array[$i]["id_parent"] = $str_id_parrent;
            $td_array[$i]["level"] = $str_level;
            $td_array[$i]["name"] = $tex_text;
            $td_array[$i]["child"] = $child;
            $td_array[$i]["position"] = $position;
        }

        foreach ($td_array as $key => $row) {
            $parent_fare[$key] = $row['id_parent'];
            $position_fare[$key] = $row['position'];
        }
        array_multisort($parent_fare, SORT_ASC, $position_fare, SORT_DESC, $td_array);

        for ($i = 1; $i <= 30; $i++) {
            ++$lvl;

            foreach ($td_array as $elm) {
                if ($elm["level"] == $lvl) {
                    $str_id2 = $elm["id_tree"];
                    $str="<li><div>";
                    if ($elm["child" ] >0) {
                        $str .= "<a>" . $elm["name"] . "</a>";
                    }
                    if ($elm["child"] == 0) {
                        $str .= "<a class='btn btn-primary btn-xs' onclick='saveArticleTreeTecdoc(\"$str_id2\");'>Привязати</a> <a>".$elm["name"]."</a>";
                    }
                    $str .= "</div>";
                    if ($elm["child"] > 0) {
                        $str .= "\n<ul>\n{p" . $elm["id_tree"] . "}</ul>\n";
                    }
                    $str .= "</li>\n";
                    if ($lvl === 2) {
                        $tree .= $str;
                    }
                    if ($lvl > 2) {
                        $tree = str_replace("{p".$elm["id_parent"]."}", $str."{p".$elm["id_parent"]."}", $tree);
                    }
                }
            }
        }
        foreach ($td_array as $elm) {
            $tree = str_replace(array("{p" . $elm["id_parent"] . "}", "{p" . $elm["id_tree"] . "}"), "", $tree);
        }

        $menu_det .= $tree . "</ul>";

        return $menu_det;
    }

    public function saveArticleTreeTecdoc($art_id, $str_id): bool
    {
        $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT * FROM `T2_TREE` WHERE `ART_ID` = $art_id AND `STR_ID` = $str_id LIMIT 1;");
        $n = (int)$db->num_rows($r);
        if ($n === 0) {
            $db->query("INSERT INTO `T2_TREE` (`ART_ID`, `STR_ID`) VALUES ('$art_id', '$str_id');");
        }
        return true;
    }

    public function saveArticleTreeNew($art_id, $group_id): bool
    {
        $db = DbSingleton::getTokoDb();
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $r = $db->query("SELECT * FROM `T2_TREE_ARTS` WHERE `ART_ID` = $art_id AND `GROUP_ID` = $group_id LIMIT 1;");
        $n = (int)$db->num_rows($r);
        if ($n === 0) {
            $db->query("INSERT INTO `T2_TREE_ARTS` (`ART_ID`, `GROUP_ID`, `USER_ID`) VALUES ('$art_id', '$group_id', '$user_id');");
        }
        return true;
    }

    public function getStrName($str_id)
    {
        $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT `TEX_TEXT` FROM `T2_GROUP_TREE` WHERE `STR_ID` = $str_id AND `LNG_ID` = 16 LIMIT 1;");
        $n = (int)$db->num_rows($r);
        $str_name = $db->result($r, 0, "TEX_TEXT");
        if ($n === 0) {
            $str_name = "-не визначена-";
        }
        return $str_name;
    }

    public function getCatName($cat_id)
    {
        $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT `TEX_RU` FROM `T2_TREE_CAT` WHERE `CAT_ID` = $cat_id LIMIT 1;");
        return $db->result($r, 0, "TEX_RU");
    }

    public function getGroupName($group_id)
    {
        $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT `TEX_RU` FROM `T2_TREE_GROUP` WHERE `GROUP_ID` = $group_id LIMIT 1;");
        return $db->result($r, 0, "TEX_RU");
    }

    public function getCatExistName($cat_id)
    {
        $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT `TEX_RU` FROM `T2_TREE_CAT_EXIST` WHERE `CAT_ID` = $cat_id LIMIT 1;");
        return $db->result($r, 0, "TEX_RU");
    }

    public function getGroupExistName($group_id)
    {
        $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT `TEX_RU` FROM `T2_TREE_GROUP_EXIST` WHERE `GROUP_ID` = $group_id LIMIT 1;");
        return $db->result($r, 0, "TEX_RU");
    }

    public function getLaIdComment($la_id): string
    {
        $db = DbSingleton::getTokoDb();
        $comment = "";
        $r = $db->query("SELECT `TEXT` FROM `link_notes` WHERE `LA_ID` = '$la_id' AND `LANG_ID` = 16 AND `DISPLAY` = 1;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $comment .= $db->result($r, $i - 1, "TEXT") . "\n";
        }
        return $comment;
    }

    public function loadArticleAplicabilityModels($art_id, $mfa_id)
    {
        $db = DbSingleton::getTokoDb();
        $slave = new slave;
        $list = "";
        $form = ""; $form_htm = RD . "/tpl/catalogue_aplicability_model_block.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $form = str_replace(array("{mfa_id}", "{art_id}"), array($mfa_id, $art_id), $form);
        $typ_id_str = "";
        $la_id_arr = array();

        $r = $db->query("SELECT `TYP_ID`, `LA_ID` FROM `T2_LINKS` WHERE `ART_ID` = $art_id;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $typ_id = $db->result($r, $i - 1, "TYP_ID");
            $typ_id_str .= $typ_id;
            if ($i < $n) {
                $typ_id_str .= ",";
            }
            $la_id = $db->result($r, $i - 1, "LA_ID");
            $la_id_arr[$typ_id] = $la_id;
        }
        if ($typ_id_str === "") {
            $typ_id_str = 0;
        }

        $r = $db->query("SELECT tt.*, tm.TEX_TEXT 
        FROM `T_types` tt 
            INNER JOIN `T_models` tm on tm.MOD_ID=tt.TYP_MOD_ID 
            INNER JOIN `T_manufacturers` man on man.MFA_ID=tm.MOD_MFA_ID 
        WHERE tm.MOD_MFA_ID = $mfa_id AND tt.TYP_ID IN ($typ_id_str) 
        ORDER BY tt.TYP_TEXT ASC;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $typ_id = $db->result($r, $i - 1, "TYP_ID");
            $engine = $db->result($r, $i - 1, "TYP_TEXT");
            $typ_text = $db->result($r, $i - 1, "TYP_MMT_TEXT");
            $fuel = $db->result($r, $i - 1, "Fuel");
            $start = $db->result($r, $i - 1, "TYP_PCON_START");
            if ($start == 0) {
                $start = "";
            }
            if (strlen($start) === 6) {
                $start = substr($start, 0, 4) . "." . substr($start, 4, 2);
            }
            $end = $db->result($r, $i - 1, "TYP_PCON_END");
            if ($end == 0) {
                $end = "";
            }
            if (strlen($end) === 6) {
                $end = substr($end, 0, 4) . "." . substr($end, 4, 2);
            }
            $typ_kw_from = $db->result($r, $i - 1, "TYP_KW_FROM");
            $typ_hp_from = $db->result($r, $i - 1, "TYP_HP_FROM");
            $typ_ccm = $db->result($r, $i - 1, "TYP_CCM");
            $eng_cod = $db->result($r, $i - 1, "ENG_Cod");
            $la_id = $la_id_arr[$typ_id];
            $la_id_comment = $this->getLaIdComment($la_id);
            $list .= "<tr>
                <td>$typ_text</td>
                <td>$engine</td>
                <td>$fuel</td>
                <td>$start - $end</td>
                <td>$typ_kw_from / $typ_hp_from</td>
                <td>$typ_ccm</td>
                <td>$eng_cod</td>
                <td><button class='btn btn-xs btn-warning' onclick=\"showLaIdCommentForm('$art_id','$typ_id');\"><i class='fa fa-edit'></i></button> $la_id_comment</td>
                <td align='center'><button class='btn btn-xs btn-danger btn-bitbucket' title='Відвязати авто' onclick='unlinkArticleAplicabilityModel(\"$mfa_id\",\"$art_id\",\"$typ_id\",\"".$slave->qq($typ_text)."\");'><i class='fa fa-times'></i></button></td>
            </tr>";
        }
        $form = str_replace("{models_list}", $list, $form);

        return $form;
    }

    /*
     * відвязати применяемость
     * */
    public function unlinkArticleAplicabilityModel($art_id, $typ_id)
    {
        $db = DbSingleton::getTokoDb();
        $answer = "Помилка обробки даних";
        if ($art_id > 0 && $typ_id > 0) {
            $db->query("DELETE FROM `T2_LINKS` WHERE `ART_ID`='$art_id' AND `TYP_ID`='$typ_id';");
            $answer = 1;
        }
        return $answer;
    }

    public function clearActicleAplicabilityManuf($art_id, $mfa_id)
    {
        $db = DbSingleton::getTokoDb();
        $answer = "Помилка обробки даних";
        if ($art_id > 0 && $mfa_id > 0) {
            $r = $db->query("SELECT tt.TYP_ID FROM `T_types` tt 
                LEFT JOIN `T_models` tm ON (tm.MOD_ID=tt.TYP_MOD_ID) 
            WHERE tm.MOD_MFA_ID = $mfa_id;");
            $n = (int)$db->num_rows($r);
            if ($n > 0) {
                for ($i = 1; $i <= $n; $i++) {
                    $typ_id = $db->result($r, $i - 1, "TYP_ID");
                    $db->query("DELETE FROM `T2_LINKS` WHERE `ART_ID` = $art_id AND `TYP_ID` = $typ_id;");
                }
                $answer = 1;
            }
        }
        return $answer;
    }

    public function loadArticleAplicabilityNew($art_id, $index): array
    {
        $form = "";
        if ($art_id > 0) {
            $form_htm = RD . "/tpl/catalogue_aplicability_form.htm";
            if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
            [$menu, $menu_kol_elem] = $this->createTDtree_universal();
            $form = str_replace(array("{art_id}", "{display_number}", "{mfa_list}", "{menu}", "{kol_elem}"), array($art_id, $index, $this->loadManufList(), $menu, $menu_kol_elem), $form);
        }

        return array($form, "Привязка до авто");
    }

    public function saveCatalogueAplicabilityForm($art_id, $comment, $typ_array, $str_array): array
    {
        $db = DbSingleton::getTokoDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка збереження даних!";
        $art_id=$slave->qq($art_id); $comment=$slave->qq($comment); $typ_array=$slave->qq($typ_array);

        if ($art_id > 0) {
            $typ_array = explode(",", $typ_array);
            foreach ($typ_array as $typ_id) {
                if ($typ_id > 0 && $typ_id !== "") {
                    $r2 = $db->query("SELECT MAX(`LA_ID`) as mid FROM `link_notes`;");
                    $la_id_new = 0 + $db->result($r2, 0, "mid") + 1;
                    $db->query("INSERT INTO `link_notes` (`LA_ID`,`LANG_ID`,`SORT`,`TEXT_NAME`,`TYPE`,`TEXT`,`DISPLAY`) VALUES ('$la_id_new','16','1','','K','$comment','1');");
                    $db->query("INSERT INTO `T2_LINKS` (`TYP_ID`,`ART_ID`,`LA_ID`) VALUES ('$typ_id','$art_id','$la_id_new');");
                }
            }
            $str_array = explode(",", $str_array);
            foreach ($str_array as $str_id) {
                if ($str_id > 0 && $str_id !== "") {
                    $db->query("INSERT INTO `T2_TREE` (`STR_ID`,`ART_ID`) VALUES ('$str_id','$art_id');");
                }
            }
            $answer = 1; $err = "";
        }

        return array($answer, $err);
    }

    public function showLaIdCommentForm($art_id, $type_id): array
    {
        $db = DbSingleton::getTokoDb();
        $form = "";
        if ($art_id > 0 && $type_id > 0) {
            $form_htm = RD . "/tpl/catalogue_laid_comment_form.htm";
            if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
            $form = str_replace(array("{art_id}", "{type_id}"), array($art_id, $type_id), $form);

            $r = $db->query("SELECT `LA_ID` FROM `T2_LINKS` WHERE `ART_ID` = $art_id AND `TYP_ID` = $type_id;");
            $n = (int)$db->num_rows($r);
            $laIdArr = "0";

            for ($i = 1; $i <= $n; $i++) {
                $la_id = $db->result($r, $i - 1, "LA_ID");
                $laIdArr .= "," . $la_id;
            }

            $r = $db->query("SELECT `LA_ID`, `SORT`, `TEXT_NAME`, `TYPE`, `TEXT` FROM `link_notes` WHERE `LA_ID` IN ($laIdArr) AND `DISPLAY` = '1' AND `LANG_ID` = '16';");
            $n = (int)$db->num_rows($r);
            $list = "";
            for ($i = 1; $i <= $n + 1; $i++) {
                $la_id = 0;
                $sort = ""; $text_name = ""; $type = ""; $text = ""; $button = "";
                if ($i <= $n) {
                    $la_id = $db->result($r, $i - 1, "LA_ID");
                    $sort = $db->result($r, $i - 1, "SORT");
                    $text_name = $db->result($r, $i - 1, "TEXT_NAME");
                    $type = $db->result($r, $i - 1, "TYPE");
                    $text = $db->result($r, $i - 1, "TEXT");
                    $button = "<button class='btn btn-xs btn-danger' onclick='dropLaIdComment(\"$art_id\",\"$type_id\",\"$la_id\",\"$sort\",\"$type\")'><i class='fa fa-trash'></i></botton>";
                }

                $list .= "<div class='form-group'>
                    <input type='hidden' id='la_id_$i' value='$la_id'>
                    <label class='col-sm-1 control-label'>Позиція</label>
                    <div class='col-sm-1'><input class='form-control' type='text' id='sort_$i' value='$sort'></div>
                    <label class='col-sm-1 control-label'>Тип</label>
                    <div class='col-sm-1'><input class='form-control' type='text' placeholder='K' id='type_$i' value='$type'></div>
                    <label class='col-sm-1 control-label'>Параметр</label>
                    <div class='col-sm-2'><input class='form-control' type='text' id='text_name_$i' value='$text_name'></div>
                    <label class='col-sm-1 control-label'>Значення</label>
                    <div class='col-sm-3'><input class='form-control' type='text' id='text_$i' value='$text'></div>
                    <div class='col-sm-1'>$button</div>
                </div>";
            }

            $form = str_replace(array("{list_la_comment}", "{kol_elem}"), array($list, ($n + 1)), $form);
        }

        return array($form, "Коментарі LA_ID");
    }

    public function saveLaIdCommentForm($art_id, $type_id, $kol, $la_ids, $sorts, $types, $text_names, $texts): array
    {
        $db = DbSingleton::getTokoDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка збереження даних!";
        $art_id=$slave->qq($art_id); $type_id=$slave->qq($type_id); $kol=$slave->qq($kol); $la_ids=$slave->qq($la_ids); $sorts=$slave->qq($sorts);
        $types=$slave->qq($types); $text_names=$slave->qq($text_names); $texts=$slave->qq($texts);

        if ($art_id > 0 && $type_id > 0) {
            for ($i = 1; $i <= $kol; $i++) {
                $la_id = $la_ids[$i];
                $sort = $sorts[$i];
                $type = $types[$i];
                $text_name = $text_names[$i];
                $text = $texts[$i];
                if ($la_id !== "" && $la_id > 0) {
                    $db->query("UPDATE `link_notes` SET `SORT`='$sort', `TEXT_NAME`='$text_name', `TYPE`='$type', `TEXT`='$text' WHERE `LA_ID`='$la_id' AND `DISPLAY`='1' AND `LANG_ID`='16';");
                }
                if ((int)$la_id === 0 && $text !== "" && $type !== "" && $sort !== "") {
                    $r2 = $db->query("SELECT MAX(`LA_ID`) as mid FROM `link_notes`;");
                    $la_id_new = 0 + $db->result($r2, 0, "mid") + 1;
                    $db->query("INSERT INTO `link_notes` (`LA_ID`,`LANG_ID`,`SORT`,`TEXT_NAME`,`TYPE`,`TEXT`,`DISPLAY`) VALUES ('$la_id_new','16','$sort','$text_name','$type','$text','1');");
                    $db->query("INSERT INTO `T2_LINKS` (`TYP_ID`,`ART_ID`,`LA_ID`) VALUES ('$type_id','$art_id','$la_id_new');");
                }
            }
            $answer = 1; $err = "";
        }

        return array($answer, $err);
    }

    public function dropLaIdComment($art_id, $type_id, $la_id, $sort, $type): array
    {
        $db = DbSingleton::getTokoDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка збереження даних!";
        $art_id=$slave->qq($art_id); $type_id=$slave->qq($type_id); $la_id=$slave->qq($la_id); $sort=$slave->qq($sort); $type=$slave->qq($type);

        if ($art_id > 0 && $type_id > 0 && $la_id > 0) {
            $db->query("DELETE FROM `link_notes` WHERE `LA_ID`='$la_id' AND `DISPLAY`='1' AND `LANG_ID`='16' AND `SORT`='$sort' AND `TYPE`='$type' LIMIT 1;");
            $answer = 1; $err = "";
        }

        return array($answer, $err);
    }

    public function loadManufList(): string
    {
        $db = DbSingleton::getTokoDb();
        $list = "";
        $r = $db->query("SELECT `MFA_ID`, `MFA_BRAND` FROM `T_manufacturers` WHERE `ACTIVE`=1 ORDER BY `MFA_BRAND` ASC;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $mfa_id = $db->result($r, $i - 1, "MFA_ID");
            $mfa_name = $db->result($r, $i - 1, "MFA_BRAND");
            $list .= "<option value='$mfa_id' onClick='loadAplicabilityModelList(\"$mfa_id\")'>$mfa_name</option>";
        }

        return $list;
    }

    public function loadAplicabilityModelList($mfa_id): string
    {
        $db = DbSingleton::getTokoDb();
        $list = "";
        $r = $db->query("SELECT `MOD_ID`, `TEX_TEXT` FROM `T_models` WHERE `MOD_MFA_ID`='$mfa_id' ORDER BY `TEX_TEXT` ASC;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $mod_id = $db->result($r, $i - 1, "MOD_ID");
            $mod_name = $db->result($r, $i - 1, "TEX_TEXT");
            $list .= "<option value='$mod_id' onClick='loadAplicabilityModificationList(\"$mfa_id\",\"$mod_id\",)'>$mod_name</option>";
        }

        return $list;
    }

    public function loadAplicabilityModificationList($mod_id): string
    {
        $db = DbSingleton::getTokoDb();
        $list = "";
        $r = $db->query("SELECT * FROM `T_types` WHERE `TYP_MOD_ID`='$mod_id' ORDER BY `TYP_MMT_TEXT` ASC;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $typ_id = $db->result($r, $i - 1, "TYP_ID");
            $name = "";
            if ($db->result($r, $i - 1, "Fuel") !== "") {
                $name .= $db->result($r, $i - 1, "Fuel")." | ";
            }
            $name .= $db->result($r, $i - 1, "TYP_TEXT")." | ";
            $name .= "(".substr($db->result($r, $i - 1, "TYP_PCON_START"),0,4).".".substr($db->result($r, $i - 1, "TYP_PCON_START"),4,2);
            $name .= "-";
            if (strlen($db->result($r, $i - 1, "TYP_PCON_END")) > 1) {
                $name .= substr($db->result($r, $i - 1, "TYP_PCON_END"),0,4).".".substr($db->result($r, $i - 1, "TYP_PCON_END"),4,2).") | ";
            } else {
                $name .= "&infin;) | ";
            }
            $name .= $db->result($r, $i - 1, "TYP_HP_FROM")."HP/";
            $name .= $db->result($r, $i - 1, "TYP_KW_FROM")."kW | ";
            $name .= $db->result($r, $i - 1, "TYP_CCM")."см<sup>3</sup> | ";
            $name .= $db->result($r, $i - 1, "ENG_Cod");
            $list .= "<li><input type='checkbox' id='modif$i' value='$typ_id'> $name</li>";
        }

        if ($n > 0) {
            $list = "<li><input id='modif0' value='0' onclick=\"checkModifAll(this)\" type=\"checkbox\"> - Відмітити все</li>".$list."<input type='hidden' id='modif_kol' value='$n'>";
        }

        return $list;
    }

    public function loadArticleLogistic($art_id)
    {
        $db = DbSingleton::getTokoDb();
        $form = ""; $form_htm = RD . "/tpl/catalogue_logistic.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $r = $db->query("SELECT * FROM `T2_PACKAGING` WHERE `ART_ID` = $art_id LIMIT 1;");
        $index_pack = $db->result($r, 0, "INDEX_PACK");
        $height = $db->result($r, 0, "HEIGHT");
        $length = $db->result($r, 0, "LENGTH");
        $width = $db->result($r, 0, "WIDTH");
        $volume = $db->result($r, 0, "VOLUME");
        $weight_netto = $db->result($r, 0, "WEIGHT_NETTO");
        $weight_brutto = $db->result($r, 0, "WEIGHT_BRUTTO");
        $necessary_amount_car = $db->result($r, 0, "NECESSARY_AMOUNT_CAR");
        $units_id = $db->result($r, 0,  "UNITS_ID");
        $multiplicity_package = $db->result($r, 0, "MULTIPLICITY_PACKAGE");
        $shoulder_delivery = $db->result($r, 0, "SHOULDER_DELIVERY");
        $general_quant = $db->result($r, 0, "GENERAL_QUANT");

        $form = str_replace("{art_id}", $art_id, $form);
        $form = str_replace("{index_pack}", $index_pack, $form);
        $form = str_replace("{height}", $height, $form);
        $form = str_replace("{length}", $length, $form);
        $form = str_replace("{width}", $width, $form);
        $form = str_replace("{volume}", $volume, $form);
        $form = str_replace("{weight_netto}", $weight_netto, $form);
        $form = str_replace("{weight_brutto}", $weight_brutto, $form);
        $form = str_replace("{necessary_amount_car}", $necessary_amount_car, $form);
        $form = str_replace("{units_list}", $this->showUnitsListSelect($units_id), $form);
        $form = str_replace("{multiplicity_package}", $multiplicity_package, $form);
        $form = str_replace("{shoulder_delivery}", $shoulder_delivery, $form);
        $form = str_replace("{general_quant}", $general_quant, $form);
        $form = str_replace("{work_pair_list}", $this->showWorkPairForm($art_id), $form);

        return $form;
    }

    public function showWorkPairForm($art_id): string
    {
        $db = DbSingleton::getTokoDb();
        $list = "";
        $r = $db->query("SELECT `PAIR_INDEX` FROM `T2_WORK_PAIR` WHERE `ART_ID` = $art_id;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n + 3; $i++) {
            $pair_index = "";
            if ($i <= $n) {
                $pair_index = $db->result($r, $i - 1, "PAIR_INDEX");
            }
            $list .= "<tr><td><input type='text' id='work_pair_$i' value='$pair_index' class='form-control'></td></tr>";
        }
        $list .= "<input type='hidden' id='work_pair_n' value='" . ($n + 3) . "'>";

        return $list;
    }

    public function saveCatalogueLogistic($art_id, $index_pack, $height, $length, $width, $volume, $weight_netto, $weight_brutto, $necessary_amount_car, $units_id, $multiplicity_package, $shoulder_delivery, $general_quant, $work_pair): array
    {
        $db = DbSingleton::getTokoDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка збереження даних!";
        $art_id=$slave->qq($art_id);$index_pack=$slave->qq($index_pack);$height=$slave->qq($slave->point_valid($height));$length=$slave->qq($slave->point_valid($length));
        $width=$slave->qq($slave->point_valid($width));$volume=$slave->qq($slave->point_valid($volume));$weight_netto=$slave->qq($slave->point_valid($weight_netto));
        $weight_brutto=$slave->qq($slave->point_valid($weight_brutto));$necessary_amount_car=$slave->qq($necessary_amount_car);$units_id=$slave->qq($units_id);
        $multiplicity_package=$slave->qq($multiplicity_package);$shoulder_delivery=$slave->qq($shoulder_delivery);$general_quant=$slave->qq($general_quant);

        if ($art_id > 0) {
            $r = $db->query("SELECT * FROM `T2_PACKAGING` WHERE `ART_ID` = $art_id LIMIT 1;");
            $n = (int)$db->num_rows($r);
            if ($n === 0) {
                $db->query("INSERT INTO `T2_PACKAGING` (`ART_ID`,`INDEX_PACK`,`HEIGHT`,`LENGTH`,`WIDTH`,`VOLUME`,`WEIGHT_NETTO`,`WEIGHT_BRUTTO`,`NECESSARY_AMOUNT_CAR`,`UNITS_ID`,`MULTIPLICITY_PACKAGE`,`SHOULDER_DELIVERY`,`GENERAL_QUANT`) 
                VALUES ('$art_id','$index_pack','$height','$length','$width','$volume','$weight_netto','$weight_brutto','$necessary_amount_car','$units_id','$multiplicity_package','$shoulder_delivery','$general_quant');");
            }
            if ($n === 1) {
                $db->query("UPDATE `T2_PACKAGING` SET `INDEX_PACK`='$index_pack', `HEIGHT`='$height', `LENGTH`='$length', `WIDTH`='$width', `VOLUME`='$volume', `WEIGHT_NETTO`='$weight_netto', `WEIGHT_BRUTTO`='$weight_brutto', 
                `NECESSARY_AMOUNT_CAR`='$necessary_amount_car', `UNITS_ID`='$units_id', `MULTIPLICITY_PACKAGE`='$multiplicity_package', `SHOULDER_DELIVERY`='$shoulder_delivery', `GENERAL_QUANT`='$general_quant' WHERE `ART_ID` = $art_id;");
            }
            if ($work_pair !== "") {
                $db->query("DELETE FROM `T2_WORK_PAIR` WHERE `ART_ID` = $art_id;");
                foreach ($work_pair as $wp) {
                    if ($wp !== "") {
                        $db->query("INSERT INTO `T2_WORK_PAIR` (`ART_ID`,`PAIR_INDEX`) VALUES ('$art_id','$wp');");
                    }
                }
            }
            $answer = 1; $err = "";
        }

        return array($answer, $err);
    }

    public function getCountryAbr($sel_id)
    {
        $db = DbSingleton::getTokoDb();
        $abr = "";
        $r = $db->query("SELECT `ALFA2` FROM `T2_COUNTRIES` WHERE `COUNTRY_ID`='$sel_id' LIMIT 1;");
        $n = (int)$db->num_rows($r);
        if ($n === 1) {
            $abr = $db->result($r, 0, "ALFA2");
        }
        return $abr;
    }

    public function showCountryManual($sel_id)
    {
        $db = DbSingleton::getTokoDb();
        $manual = new manual;
        $list = "";
        $form = ""; $form_htm = RD . "/tpl/catalogue_country_list.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }

        $r = $db->query("SELECT * FROM `T2_COUNTRIES` ORDER BY `COUNTRY_NAME` ASC;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r, $i - 1, "COUNTRY_ID");
            $name = $db->result($r, $i - 1, "COUNTRY_NAME");
            $alfa2 = $db->result($r, $i - 1, "ALFA2");
            $alfa3 = $db->result($r, $i - 1, "ALFA3");
            $duty = $manual->getManualMCaption("DUTY", $db->result($r, $i - 1, "DUTY"));
            $risk = $manual->getManualMCaption("RISK", $db->result($r, $i - 1, "RISK"));

            $sel = "";
            if ((int)$sel_id === (int)$id) {
                $sel = " style='background-color:#d5fdf5'";
            }

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

        $form = str_replace("{list}", $list, $form);

        return $form;
    }

    public function showCountryForm($id): array
    {
        $db = DbSingleton::getTokoDb();
        $manual = new manual;
        $form = ""; $form_htm = RD . "/tpl/catalogue_country_form.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $r = $db->query("SELECT * FROM `T2_COUNTRIES` WHERE `COUNTRY_ID` = $id LIMIT 1;");
        $name = $db->result($r, 0, "COUNTRY_NAME");
        $alfa2 = $db->result($r, 0, "ALFA2");
        $alfa3 = $db->result($r, 0, "ALFA3");
        $duty = $db->result($r, 0, "DUTY");
        $risk = $db->result($r, 0, "RISK");

        $form = str_replace(array("{id}", "{name}", "{alfa2}", "{alfa3}", "{duty}", "{duty_caption}", "{risk}", "{risk_caption}"), array($id, $name, $alfa2, $alfa3, $duty, $manual->getManualMCaption("DUTY", $duty), $risk, $manual->getManualMCaption("RISK", $risk)), $form);

        return array($form, "Форма Країни походження");
    }

    public function saveCatalogueCountryForm($id, $name, $alfa2, $alfa3, $duty, $risk): array
    {
        $db = DbSingleton::getTokoDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка збереження даних!";
        $id=$slave->qq($id); $name=$slave->qq($name); $alfa2=$slave->qq($alfa2); $alfa3=$slave->qq($alfa3); $duty=$slave->qq($duty); $risk=$slave->qq($risk);

        if ($id > 0) {
            $r = $db->query("SELECT * FROM `T2_COUNTRIES` WHERE `COUNTRY_ID` = $id LIMIT 1;");
            $n = (int)$db->num_rows($r);
            if ($n === 0) {
                $db->query("INSERT INTO `T2_COUNTRIES` (`COUNTRY_ID`,`COUNTRY_NAME`,`ALFA2`,`ALFA3`,`DUTY`,`RISK`) VALUES ('$id','$name','$alfa2','$alfa3','$duty','$risk');");
            }
            if ($n === 1) {
                $db->query("UPDATE `T2_COUNTRIES` SET `COUNTRY_NAME`='$name', `ALFA2`='$alfa2', `ALFA3`='$alfa3', `DUTY`='$duty', `RISK`='$risk' WHERE `COUNTRY_ID` = $id;");
            }
            $answer = 1; $err = "";
        }

        if ($id === "" && $name !== "") {
            $db->query("INSERT INTO `T2_COUNTRIES` (`COUNTRY_ID`,`COUNTRY_NAME`,`ALFA2`,`ALFA3`,`DUTY`,`RISK`) VALUES ('$id','$name','$alfa2','$alfa3','$duty','$risk');");
            $answer = 1; $err = "";
        }

        return array($answer, $err);
    }

    public function dropCountry($id): array
    {
        $db = DbSingleton::getTokoDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка видалення запису!";
        $id=$slave->qq($id);
        if ($id > 0) {
            $r = $db->query("SELECT * FROM `T2_COUNTRIES` WHERE `COUNTRY_ID` = $id LIMIT 1;");
            $n = (int)$db->num_rows($r);
            if ($n === 1) {
                $db->query("DELETE FROM `T2_COUNTRIES` WHERE `COUNTRY_ID` = $id;");
                $answer = 1; $err = "";
            }
        }

        return array($answer, $err);
    }

    public function showDocumentCountryManual($sel_id, $pos)
    {
        $db = DbSingleton::getTokoDb();
        $manual = new manual;
        $form = ""; $form_htm = RD . "/tpl/documents_country_list.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $list = "";

        $r = $db->query("SELECT * FROM `T2_COUNTRIES` ORDER BY `COUNTRY_NAME` ASC;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r, $i - 1, "COUNTRY_ID");
            $name = $db->result($r, $i - 1, "COUNTRY_NAME");
            $alfa2 = $db->result($r, $i - 1, "ALFA2");
            $alfa3 = $db->result($r, $i - 1, "ALFA3");
            $duty = $manual->getManualMCaption("DUTY", $db->result($r, $i - 1, "DUTY"));
            $risk = $manual->getManualMCaption("RISK", $db->result($r, $i - 1, "RISK"));
            $sel = "";
            if ((int)$sel_id === (int)$id) {
                $sel = " style='background-color:#d5fdf5'";
            }
            $list .= "<tr onClick='selectCountryDocument(\"$id\",\"$alfa2\")' $sel>
                <td>$id</td>
                <td>$name</td>
                <td>$alfa2</td>
                <td>$alfa3</td>
                <td>$duty</td>
                <td>$risk</td>
            </tr>";
        }

        $form = str_replace(array("{list}", "{pos}"), array($list, $pos), $form);

        return $form;
    }

    public function getCostumsCode($sel_id)
    {
        $db = DbSingleton::getTokoDb();
        $code = "";
        $r = $db->query("SELECT `COSTUMS_CODE` FROM `T2_COSTUMS` WHERE `COSTUMS_ID` = $sel_id LIMIT 1;");
        $n = (int)$db->num_rows($r);
        if ($n === 1) {
            $code = $db->result($r, 0, "COSTUMS_CODE");
        }
        return $code;
    }

    public function showCostumsManual($sel_id)
    {
        $db = DbSingleton::getTokoDb();
        $manual = new manual;
        $list = "";
        $form = ""; $form_htm = RD . "/tpl/catalogue_costums_list.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $r = $db->query("SELECT * FROM `T2_COSTUMS` ORDER BY `COSTUMS_NAME` ASC;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r, $i - 1, "COSTUMS_ID");
            $code = $db->result($r, $i - 1, "COSTUMS_CODE");
            $name = $db->result($r, $i - 1, "COSTUMS_NAME");
            $preferential_rate = $db->result($r, $i - 1, "PREFERENTIAL_RATE");
            $full_rate = $db->result($r, $i - 1, "FULL_RATE");
            $sertification = $manual->getManualMCaption("costums_sertification",$db->result($r, $i - 1, "SERTIFICATION"));
            $gos_standart = $manual->getManualMCaption("costums_gos_standart",$db->result($r, $i - 1, "GOS_STANDART"));
            $type_declaration = $manual->getManualMCaption("costums_type_declaration",$db->result($r, $i - 1, "TYPE_DECLARATION"));
            $sel = "";
            if ((int)$sel_id === (int)$id) {
                $sel = " style='background-color:#d5fdf5'";
            }
            $list .= "<tr class='pointer' onClick='selectCostums(\"$id\",\"$code\")' $sel>
                <td>$code</td>
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

        $form = str_replace("{list}", $list, $form);

        return $form;
    }

    public function showCostumsForm($id): array
    {
        $db = DbSingleton::getTokoDb();
        $manual = new manual;
        $form = ""; $form_htm = RD . "/tpl/catalogue_costums_form.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $r = $db->query("SELECT * FROM `T2_COSTUMS` WHERE `COSTUMS_ID` = $id LIMIT 1;");
        $code = $db->result($r, 0, "COSTUMS_CODE");
        $name = $db->result($r, 0, "COSTUMS_NAME");
        $preferential_rate = $db->result($r, 0, "PREFERENTIAL_RATE");
        $full_rate = $db->result($r, 0, "FULL_RATE");
        $sertification = $db->result($r, 0, "SERTIFICATION");
        $gos_standart = $db->result($r, 0, "GOS_STANDART");
        $type_declaration = $db->result($r, 0, "TYPE_DECLARATION");

        $form = str_replace("{id}", $id, $form);
        $form = str_replace("{code}", $code, $form);
        $form = str_replace("{name}", $name, $form);
        $form = str_replace("{preferential_rate}", $preferential_rate, $form);
        $form = str_replace("{full_rate}", $full_rate, $form);
        $form = str_replace("{sertification}", $sertification, $form);
        $form = str_replace("{sertification_caption}", $manual->getManualMCaption("costums_sertification", $sertification), $form);
        $form = str_replace("{gos_standart}", $gos_standart, $form);
        $form = str_replace("{gos_standart_caption}", $manual->getManualMCaption("costums_gos_standart", $gos_standart), $form);
        $form = str_replace("{type_declaration}", $type_declaration, $form);
        $form = str_replace("{type_declaration_caption}", $manual->getManualMCaption("costums_type_declaration", $type_declaration), $form);

        return array($form, "Форма митного коду УКТЕЗД");
    }

    public function saveCatalogueCostumsForm($id, $code, $name, $preferential_rate, $full_rate, $type_declaration, $sertification, $gos_standart): array
    {
        $db = DbSingleton::getTokoDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка збереження даних!";
        $id=$slave->qq($id); $code=$slave->qq($code); $name=$slave->qq($name); $preferential_rate=$slave->qq($slave->point_valid($preferential_rate));
        $full_rate=$slave->qq($slave->point_valid($full_rate)); $type_declaration=$slave->qq($type_declaration); $sertification=$slave->qq($sertification); $gos_standart=$slave->qq($gos_standart);

        if ($id > 0) {
            $r = $db->query("SELECT * FROM `T2_COSTUMS` WHERE `COSTUMS_ID` = $id LIMIT 1;");
            $n = (int)$db->num_rows($r);
            if ($n === 0) {
                $db->query("INSERT INTO `T2_COSTUMS` (`COSTUMS_ID`,`COSTUMS_CODE`,`COSTUMS_NAME`,`PREFERENTIAL_RATE`,`FULL_RATE`,`SERTIFICATION`,`GOS_STANDART`,`TYPE_DECLARATION`) 
                VALUES ('$id','$code','$name','$preferential_rate','$full_rate','$sertification','$gos_standart','$type_declaration');");
            }
            if ($n === 1) {
                $db->query("UPDATE `T2_COSTUMS` SET `COSTUMS_CODE`='$code', `COSTUMS_NAME`='$name', `PREFERENTIAL_RATE`='$preferential_rate', `FULL_RATE`='$full_rate', `SERTIFICATION`='$sertification', `GOS_STANDART`='$gos_standart', `TYPE_DECLARATION`='$type_declaration' WHERE `COSTUMS_ID` = $id;");
            }
            $answer = 1; $err = "";
        }

        if ($id === "" && $name !== "") {
            $db->query("INSERT INTO `T2_COSTUMS` (`COSTUMS_ID`,`COSTUMS_CODE`,`COSTUMS_NAME`,`PREFERENTIAL_RATE`,`FULL_RATE`,`SERTIFICATION`,`GOS_STANDART`,`TYPE_DECLARATION`) 
            VALUES ('$id','$code','$name','$preferential_rate','$full_rate','$sertification','$gos_standart','$type_declaration');");
            $answer = 1; $err = "";
        }

        return array($answer, $err);
    }

    public function dropCostums($id): array
    {
        $db = DbSingleton::getTokoDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка видалення запису!";
        $id = $slave->qq($id);

        if ($id > 0) {
            $r = $db->query("SELECT * FROM `T2_COSTUMS` WHERE `COSTUMS_ID` = $id LIMIT 1;");
            $n = (int)$db->num_rows($r);
            if ($n === 1) {
                $db->query("DELETE FROM `T2_COSTUMS` WHERE `COSTUMS_ID` = $id;");
                $answer = 1; $err = "";
            }
        }

        return array($answer, $err);
    }

    public function showDocumentCostumsManual($sel_id, $pos)
    {
        $db = DbSingleton::getTokoDb();
        $manual = new manual;
        $list = "";
        $form = ""; $form_htm = RD . "/tpl/documents_costums_list.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $r = $db->query("SELECT * FROM `T2_COSTUMS` ORDER BY `COSTUMS_NAME` ASC;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r, $i - 1, "COSTUMS_ID");
            $code = $db->result($r, $i - 1, "COSTUMS_CODE");
            $name = $db->result($r, $i - 1, "COSTUMS_NAME");
            $preferential_rate = $db->result($r, $i - 1, "PREFERENTIAL_RATE");
            $full_rate = $db->result($r, $i - 1, "FULL_RATE");
            $sertification = $manual->getManualMCaption("costums_sertification",$db->result($r, $i - 1, "SERTIFICATION"));
            $gos_standart = $manual->getManualMCaption("costums_gos_standart",$db->result($r, $i - 1, "GOS_STANDART"));
            $type_declaration = $manual->getManualMCaption("costums_type_declaration",$db->result($r, $i - 1, "TYPE_DECLARATION"));

            $sel = "";
            if ((int)$sel_id === (int)$id) {
                $sel = " style='background-color:#d5fdf5'";
            }

            $list .= "<tr class='pointer' style='cursor:pointer;' onClick='selectCostumsDocument(\"$id\",\"$code\")' $sel>
                <td>$code</td>
                <td>$name</td>
                <td align='right'>$preferential_rate</td>
                <td align='right'>$full_rate</td>
                <td align='center'>$type_declaration</td>
                <td align='center'>$sertification</td>
                <td align='center'>$gos_standart</td>
            </tr>";
        }

        $form = str_replace(array("{list}", "{pos}"), array($list, $pos), $form);

        return $form;
    }

    public function getPriceRatingArray(): array
    {
        $db = DbSingleton::getTokoDb();
        $rating_ar = array();
        $r = $db->query("SELECT `id`, `abr`, `name` FROM `T2_PRICE_RATING` ORDER BY `abr`, `id` ASC;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r, $i - 1, "id");
            $abr = $db->result($r, $i - 1, "abr");
            $name = $db->result($r, $i - 1, "name");
            $rating_ar[$i]["id"] = $id;
            $rating_ar[$i]["abr"] = $abr;
            $rating_ar[$i]["name"] = $name;
        }

        return $rating_ar;
    }

    /*
     * Article Card => ціноутворення
     * */
    public function loadArticlePricing($art_id, $brand_id)
    {
        $db = DbSingleton::getTokoDb();
        $art_id = (int)$art_id;
        $brand_id = (int)$brand_id;
        $form = ""; $form_htm = RD . "/tpl/catalogue_pricing.htm";
        if (file_exists($form_htm)) {
            $form = file_get_contents($form_htm);
        }
        $list_price_rating = "";
        $form = str_replace("{art_id}", $art_id, $form);
        $rating_ar = $this->getPriceRatingArray();
        //$kol_rating = 0;
        foreach ($rating_ar as $rar) {
            //$kol_rating += 1;
            $list_price_rating .= "<th class='text-center' title='" . $rar["name"] . "'>Прайс<br>" . $rar["abr"] . "</th>";
        }
        $form = str_replace("{list_price_rating}", $list_price_rating, $form);
        [, , , $article_nr_search] = $this->getArticleNrDisplBrand($art_id);
        $ak = array();
        $rk = array();
        //        $art_id_str = "";
        $art_id_arr = [];
        $r = $db->query("SELECT t2c.ART_ID, t2c.KIND, t2c.RELATION 
        FROM `T2_CROSS` t2c 
            LEFT OUTER JOIN `T2_BRANDS` t2b ON (t2b.BRAND_ID=t2c.BRAND_ID) 
        WHERE t2c.SEARCH_NUMBER = '$article_nr_search' AND t2c.BRAND_ID = $brand_id 
        ORDER BY t2c.KIND ASC;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $ART_ID = $db->result($r,$i - 1, "ART_ID");
            $KIND = (int)$db->result($r, $i - 1, "KIND");
            $RELATION = (int)$db->result($r, $i - 1, "RELATION");
            //            $art_id_str .= "'$ART_ID'";
            //            if ($i < $n) {
            //                $art_id_str .= ",";
            //            }
            $art_id_arr[] = $ART_ID;
            if (($ak[$ART_ID] === "") || $KIND === 0) {
                $ak[$ART_ID] = $KIND;
            }
            if (($rk[$ART_ID] === "") || $RELATION === 0) {
                $rk[$ART_ID] = $RELATION;
            }
        }
        $art_id_str = implode(",", array_unique($art_id_arr));
        if ($art_id_str === "") {
            $art_id_str = 0;
        }
        //        $art_id_str = str_replace("'", "", $art_id_str);

        $ak0 = $ak1 = $ak2 = $ak3 = $ak4 = "";
        $r = $db->query("SELECT t2a.ART_ID, t2a.BRAND_ID, t2a.ARTICLE_NR_DISPL, t2b.BRAND_NAME 
        FROM `T2_ARTICLES` t2a 
            LEFT JOIN `T2_BRANDS` t2b ON (t2b.BRAND_ID = t2a.BRAND_ID) 
        WHERE t2a.ART_ID IN ($art_id_str);");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $analog_art_id = $db->result($r, $i - 1, "ART_ID");
            $display_nr = trim($db->result($r, $i - 1, "ARTICLE_NR_DISPL"));
            $brand_name = $db->result($r, $i - 1, "BRAND_NAME");
            $kind_id = $ak[$analog_art_id];
            $relation = $rk[$analog_art_id];

            [$article_oper_price, $article_storage_amount] = $this->getArticleOperPriceGeneralStock($analog_art_id);
            if ($article_oper_price > 0) {
                $article_income_amount = 0;
                [$template_price_rating_id, $data_use, $author_id, $minMarkup, $cash_id, $prAr] = $this->getArticlePriceRating($analog_art_id);
                $author_name = $this->getMediaUserName($author_id);
                $template_price_rating_select = $this->showPriceRatingTemplateSelect($analog_art_id, $template_price_rating_id);
                $cash_price_rating_select = $this->showPriceRatingCashSelect($analog_art_id, $cash_id);
                $row2 = "";
                require_once (RD . "/lib/action_clients_class.php");
                $action_clients = new action_clients;
                $action_list = $action_clients->getActionsList($analog_art_id);
                $row = "<tr>
                    <td rowspan=2>
                        <input type='hidden' id='rp_$i' value='$analog_art_id'>
                        <strong>
                            $display_nr<br>
                            $brand_name<br>
                            <button class=\"btn btn-sm btn-primary btn-outline\" type=\"button\" title=\"Історія\" onclick=\"showArticleJDocs('$analog_art_id');\"><i class='fa fa-history'></i></button><br>
                            $action_list 
                        </strong>
                    </td>
                    <td><input type='text' class='form-control input-xs price_numbers nrCell' readonly id='artStorageAmount_$analog_art_id' value='$article_storage_amount'></td>
                    <td>$article_income_amount</td>
                    <td><input type='text' class='form-control input-xs price_numbers nrCell' readonly id='artOperPrice_$analog_art_id' value='$article_oper_price'></td>";

                for ($k = 1; $k <= $this->kol_price_rating; $k++) {
                    $rating_price = $prAr[$k]["price"];
                    $rating_persent = $prAr[$k]["persent"];
                    $row .= "<td><input type='text' class='form-control input-xs price_numbers nrCell' id='artRatingPrice_".$analog_art_id."_$k' value='$rating_price' onKeyup='recalcPRArt(\"$analog_art_id\",\"$k\",\"1\")'></td>";
                    $row2 .= "<td><input type='text' class='form-control input-xs' id='artRatingPersent_".$analog_art_id."_$k' value='$rating_persent' onKeyup='recalcPRArt(\"$analog_art_id\",\"$k\",\"0\")'></td>";
                }

                $row2 = "<tr>
                    <td colspan=3>
                        <table>
                            <tr>
                                <td>Шаблон націнки:</td>
                                <td>$template_price_rating_select %</td>
                            </tr>
                            <tr>
                                <td>Валюта:</td>
                                <td>$cash_price_rating_select</td>
                            </tr>
                        </table>
                    </td>
                    " . $row2 . "
                    <td id='artDataUpdate_".$analog_art_id."'>$data_use</td>
                </tr>";
                $sales = $this->getLineArticleSales($analog_art_id);
                $row .= "
                    <td rowspan=2>
                        <button class='btn btn-xs btn-default btn-block' onClick='saveArticlePriceRating(\"$analog_art_id\");return false;'><i class='fa fa-save'></i></button>
                        <button class='btn btn-xs btn-default btn-block' onClick='showArticlePriceRatingHistory(\"$analog_art_id\")'><i class='fa fa-table'></i></button>
                    </td>
                    <td rowspan=2><a href='#articleSales' onClick='showArticleSales(\"$analog_art_id\")'>$sales</a></td>
                    <td rowspan=2><input type='text' class='form-control input-xs price_numbers nrCell' id='artMinMarkUp_$analog_art_id' value='$minMarkup'></td>
                    <td id='artAuthorName_".$analog_art_id."'>$author_name</td>
                </tr>" . $row2;
                if ($kind_id == 0 && $relation == 0) { $ak0 .= $row; }
                if ($kind_id == 1 && $relation == 0) { $ak1 .= $row; }
                if ($kind_id == 3 && $relation == 0) { $ak2 .= $row; }
                if ($kind_id == 4 && $relation == 0) { $ak3 .= $row; }
            }
        }

        $articles_list = $ak0 . $ak1 . $ak2 . $ak3 . $ak4;
        $form = str_replace("{articles_list}", $articles_list, $form);

        return $form;
    }

    public function saveArticlePriceRating($art_id, $kol_elm, $template_id, $minMarkup, $cash_id, $prc, $prs): array
    {
        $db = DbSingleton::getTokoDb();
        $slave = new slave;
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $answer = 0; $err = "Помилка збереження даних!";
        $art_id = $slave->qq($art_id); $kol_elm = $slave->qq($kol_elm); $template_id = $slave->qq($template_id); $minMarkup = $slave->qq($minMarkup); $prc = $slave->qq($prc); $prs = $slave->qq($prs);

        if ($art_id > 0) {
            $r = $db->query("SELECT * FROM `T2_ARTICLES_PRICE_RATING` WHERE `art_id` = $art_id AND `in_use` = 1 LIMIT 1;");
            $n = (int)$db->num_rows($r);
            if ($n === 1) {
                $db->query("UPDATE `T2_ARTICLES_PRICE_RATING` SET `in_use` = 0 WHERE `art_id` = $art_id AND `in_use` = 1;");
            }
            $query = "INSERT INTO `T2_ARTICLES_PRICE_RATING` (`art_id`,`in_use`,`data_update`,`user_id`,`template_id`,`minMarkup`,`cash_id`";
            for ($i = 1; $i <= $kol_elm; $i++) {
                $query .= ",`price_$i`,`persent_$i`";
            }
            $query .= ") VALUES ('$art_id','1',CURDATE(),'$user_id','$template_id','$minMarkup','$cash_id'";
            for ($i = 1; $i <= $kol_elm; $i++) {
                $price = $prc[$i];
                $percent = $prs[$i];
                $query .= ",'$price','$percent'";
            }
            $query .= ");";
            $db->query($query);
            $dbm = DbSingleton::getDb();
            $dbm->query("UPDATE `ACTION_CLIENTS` SET `status_update` = 1 WHERE `art_id` = $art_id;");
            $answer = 1; $err = "";
        }

        return array($answer, $err, $this->getMediaUserName($user_id), date("Y-m-d"));
    }

    public function getArticlePriceRating($art_id): array
    {
        $db = DbSingleton::getTokoDb();
        $prAr = array();
        $kol_elm = $this->kol_price_rating;
        $data_update = "";
        $template_id = $user_id = $minMarkup = $cash_id = 0;
        $r = $db->query("SELECT * FROM `T2_ARTICLES_PRICE_RATING` WHERE `art_id` = $art_id AND `in_use` = 1 LIMIT 1;");
        $n = (int)$db->num_rows($r);
        if ($n === 1) {
            $user_id = $db->result($r, 0, "user_id");
            $data_update = $db->result($r, 0, "data_update");
            $template_id = $db->result($r, 0, "template_id");
            $minMarkup = $db->result($r, 0, "minMarkup");
            $cash_id = $db->result($r, 0, "cash_id");
            for ($i = 1; $i <= $kol_elm; $i++) {
                $prAr[$i]["price"] = $db->result($r, 0, "price_$i");
                $prAr[$i]["persent"] = $db->result($r, 0, "persent_$i");
            }
        }

        return array($template_id, $data_update, $user_id, $minMarkup, $cash_id, $prAr);
    }

    public function showPriceRatingTemplateSelect($art_id, $sel_id)
    {
        $db = DbSingleton::getTokoDb();
        $list = "";
        $form = "<select id='priceRatingTemplate_".$art_id."' onChange='loadPriceRatingTemplate(\"$art_id\")' class='input-xs' style='width:100px;'><option value=0>-- -- --</option>{list}</select>";
        $r = $db->query("SELECT `id`, `name` FROM `price_rating_template` WHERE `status`='1' ORDER BY `id` ASC;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r, $i - 1, "id");
            $name = $db->result($r, $i - 1, "name");
            $sel = "";
            if ((int)$id === (int)$sel_id) {
                $sel = " selected='selected'";
            }
            $list .= "<option value='$id' $sel>$name</option>";
        }
        $form = str_replace("{list}", $list, $form);

        return $form;
    }

    public function showPriceRatingCashSelect($art_id,$sel_id)
    {
        $db = DbSingleton::getDb();
        $list = "";
        $form = "<select id='priceRatingCash_".$art_id."' class='input-xs' style='width:100px;'><option value=0>-- -- --</option>{list}</select>";

        $r = $db->query("SELECT `id`, `name` FROM `CASH` ORDER BY `id` ASC;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r, $i - 1, "id");
            $name = $db->result($r, $i - 1, "name");
            $sel = "";
            if ((int)$id === (int)$sel_id) {
                $sel = " selected='selected'";
            }
            $list .= "<option value='$id' $sel>$name</option>";
        }
        $form = str_replace("{list}", $list, $form);

        return $form;
    }

    public function loadPriceRatingTemplateStr($sel_id): array
    {
        $db = DbSingleton::getTokoDb();
        $min_markup = 0;
        $kol_val = $this->kol_price_rating;
        $rating = [];
        $answer = 0; $err = "Помилка!";

        $r = $db->query("SELECT * FROM `price_rating_template_str` WHERE `template_id` = $sel_id LIMIT 1;");
        $n = (int)$db->num_rows($r);
        if ($n === 1) {
            $min_markup = $db->result($r, 0, "min_markup");
            for ($i = 0; $i <= $kol_val; $i++) {
                $rating[$i] = $db->result($r, 0, "rating_" . $i);
            }
            $answer = 1; $err = "";
        }

        return array($answer, $err, $min_markup, $kol_val, $rating);
    }

    /*
     * історія ціноутворення
     * */
    public function showArticlePriceRatingHistory($art_id)
    {
        $db = DbSingleton::getTokoDb();
        $dp = new dp;
        $answer = 0; $err = "Помилка індексу";
        $form = ""; $form_htm = RD . "/tpl/catalogue_pricing_history.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        if ($art_id !== "") {
            $list = "";
            $r = $db->query("SELECT * FROM `T2_ARTICLES_PRICE_RATING` WHERE `art_id` = $art_id ORDER BY `data_update` DESC, `id` DESC;");
            $n = (int)$db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $in_use = $db->result($r, $i - 1, "in_use");
                $clr = "";
                if ($in_use == 1) {
                    $clr = " style='background-color:#bce9e0;'";
                }
                $data_update = $db->result($r, $i - 1, "data_update");
                $cash_id = $db->result($r, $i - 1, "cash_id");
                $cash_name = $dp->getCashAbr($cash_id);
                $author = $this->getMediaUserName($db->result($r, $i - 1, "user_id"));
                $template_id = trim($db->result($r, $i - 1, "template_id"));
                $minMarkup = $db->result($r, $i - 1, "minMarkup");
                $list .= "<tr $clr>
                    <td>$data_update</td>
                    <td>$author</td>
                    <td>$template_id</td>
                    <td>$minMarkup</td>
                    <td>$cash_name</td>";
                for ($k = 1; $k <= $this->kol_price_rating; $k++) {
                    $price = $db->result($r, $i - 1, "price_$k");
                    $persent = $db->result($r, $i - 1, "persent_$k");
                    $list .= "<td>$price<br>$persent</td>";
                }
            }
            $form = str_replace(array("{list}", "{kol_elm}"), array($list, $this->kol_price_rating), $form);
            $answer = 1; $err = "";
        }

        return array($answer, $err, $form, "Історія ціноутворення");
    }

    public function getLineArticleSales($art_id): string
    {
        $db = DbSingleton::getTokoDb();
        $list = "";
        if ($art_id !== "") {
            for ($m = 1; $m <= 24; $m++) {
                $month = date("Y-m-00", strtotime("-$m month"));
                $r = $db->query("SELECT SUM(`AMOUNT`) as sum_amount FROM `T2_ARTICLES_SALES` WHERE `art_id`='$art_id' AND `MONTH`='$month' GROUP BY `MONTH`;");
                $n = (int)$db->num_rows($r);
                for ($i = 1; $i <= $n; $i++) {
                    $amount = $db->result($r, $i - 1, "sum_amount");
                    $list .= $amount;
                }
                $list .= ";";
            }
        }
        return $list;
    }

    /*
      * Article Card => ціноутворення => продажі
      * */
    public function showArticleSales($art_id): array
    {
        $db = DbSingleton::getTokoDb();
        $list = ""; $m = 0; $article_displ_nomber = "";
        $answer = 0; $err = "Помилка індексу";
        $block = ""; $block_htm = RD . "/tpl/catalogue_article_sales.htm";
        if (file_exists($block_htm)) { $block = file_get_contents($block_htm); }
        if ($art_id !== "") {
            [$article_displ_nomber,] = $this->getArticleNrDisplBrand($art_id);
            $r1 = $db->query("SELECT `id`, `name` FROM `T_POINT` WHERE `status`='1' ORDER BY `position` ASC;");
            $n1 = $db->num_rows($r1);
            for ($t = 1; $t <= $n1; $t++) {
                $tpoint_id = $db->result($r1, $t - 1, "id");
                $tpoint_name = $db->result($r1, $t - 1, "name");
                $tpoint_block = $block;
                $tpoint_block = str_replace("{tpoint_name}", $tpoint_name, $tpoint_block);
                $month_list = ""; $sale_list = "";

                for ($m = 1; $m <= 36; $m++) {
                    $month = date("Y-m-00", strtotime("-$m month"));
                    $month_list .= "<th style='text-align:center'>" . substr($month, 5, 2) . "<br>" . substr($month, 0, 4) . "</th>";
                    $r = $db->query("SELECT `AMOUNT` FROM `T2_ARTICLES_SALES` WHERE `art_id` = '$art_id' AND `TPOINT_ID` = '$tpoint_id' AND `MONTH` = '$month';");
                    $n = (int)$db->num_rows($r);
                    for ($i = 1; $i <= $n; $i++) {
                        $amount = $db->result($r, $i - 1, "AMOUNT");
                        $style = "";
                        if ($amount > 0) {
                            $style = "background:lightgreen;";
                        }
                        if ($amount < 0) {
                            $style = "background:pink;";
                        }
                        $sale_list .= "<td style='text-align:center; $style'>$amount</td>";
                    }
                    if ($n === 0) {
                        $sale_list .= "<td style='text-align:center'>0</td>";
                    }
                }
                $tpoint_block = str_replace(array("{month_list}", "{sale_list}"), array($month_list, $sale_list), $tpoint_block);
                $list .= $tpoint_block;
            }
            $list = str_replace("{kol_elm}", $m, $list);
            $answer = 1; $err = "";
        }

        return array($answer, $err, $list, "Інформація про продажі артикулу: $article_displ_nomber");
    }

    public function getArticleZED($art_id)
    {
        $db = DbSingleton::getTokoDb();
        $zed = 0;
        $r = $db->query("SELECT t2s.COSTUMS_CODE 
        FROM `T2_ZED` t2z 
            LEFT OUTER JOIN `T2_COSTUMS` t2s ON (t2s.COSTUMS_ID=t2z.COSTUMS_ID)
        WHERE t2z.ART_ID = $art_id LIMIT 1;");
        $n = (int)$db->num_rows($r);
        if ($n === 1) {
            $zed = $db->result($r, 0, "COSTUMS_CODE");
        }
        return $zed;
    }

    public function loadArticleZED($art_id)
    {
        $db = DbSingleton::getTokoDb();
        $form = ""; $form_htm = RD . "/tpl/catalogue_zed.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }

        $r = $db->query("SELECT t2z.*, t2c.COUNTRY_NAME, t2s.COSTUMS_CODE 
        FROM `T2_ZED` t2z 
            LEFT OUTER JOIN `T2_COUNTRIES` t2c ON (t2c.COUNTRY_ID=t2z.COUNTRY_ID)
            LEFT OUTER JOIN `T2_COSTUMS` t2s ON (t2s.COSTUMS_ID=t2z.COSTUMS_ID)
        WHERE t2z.ART_ID = $art_id LIMIT 1;");
        $country_id     = $db->result($r, 0, "COUNTRY_ID");
        $country_name   = $db->result($r, 0, "COUNTRY_NAME");
        $costums_id     = $db->result($r, 0, "COSTUMS_ID");
        $costums_code   = $db->result($r, 0, "COSTUMS_CODE");

        $form = str_replace(array("{art_id}", "{country_id}", "{country_name}", "{costums_id}", "{costums_code}"), array($art_id, $country_id, $country_name, $costums_id, $costums_code), $form);

        return $form;
    }

    public function saveCatalogueZED($art_id, $country_id, $costums_id): array
    {
        $db = DbSingleton::getTokoDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка збереження даних!";
        $art_id     = $slave->qq($art_id);
        $country_id = $slave->qq($country_id);
        $costums_id = $slave->qq($slave->point_valid($costums_id));

        if ($art_id > 0) {
            $r = $db->query("SELECT * FROM `T2_ZED` WHERE `ART_ID`='$art_id' LIMIT 1;");
            $n = (int)$db->num_rows($r);
            if ($n === 0) {
                $db->query("INSERT INTO `T2_ZED` (`ART_ID`,`COUNTRY_ID`,`COSTUMS_ID`) VALUES ('$art_id','$country_id','$costums_id');");
            }
            if ($n === 1) {
                $db->query("UPDATE `T2_ZED` SET `COUNTRY_ID`='$country_id', `COSTUMS_ID`='$costums_id' WHERE `ART_ID`='$art_id';");
            }
            $answer = 1; $err = "";
        }

        return array($answer, $err);
    }

    public function showCatalogueBrandSelectDocumentList($r)
    {
        $db = DbSingleton::getTokoDb();
        $n = (int)$db->num_rows($r);
        $list = ""; $tkey = time();
        $db->query("CREATE TEMPORARY TABLE IF NOT EXISTS `NBRAND_RESULT_$tkey` (`art_id` INT NOT NULL ,`display_nr` VARCHAR( 100 ) NOT NULL ,`name` VARCHAR( 255 ) NOT NULL ,`brand_id` INT NOT NULL ,`brand_name` VARCHAR( 100 ) NOT NULL ,`kol_res` TINYINT NOT NULL) ENGINE = MYISAM ;");
        for ($i = 1; $i <= $n; $i++) {
            $art_id = $db->result($r, $i - 1, "ART_ID");
            $display_nr = $db->result($r, $i - 1, "DISPLAY_NR");
            $name = $db->result($r, $i - 1, "NAME");
            $brand_id = $db->result($r, $i - 1, "BRAND_ID");
            $brand_name = $db->result($r, $i - 1, "BRAND_NAME");
            $kol_res = 0;
            $db->query("INSERT INTO `NBRAND_RESULT_$tkey` VALUES ('$art_id','$display_nr','$name','$brand_id','$brand_name','$kol_res');");
        }

        $r = $db->query("SELECT * FROM `NBRAND_RESULT_$tkey` ORDER BY `kol_res` DESC;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $display_nr = $db->result($r, $i - 1, "display_nr");
            $name = $db->result($r, $i - 1, "name");
            $brand_id = $db->result($r, $i - 1, "brand_id");
            $brand_name = $db->result($r, $i - 1, "brand_name");
            $list .= "
            <tr style='cursor:pointer;' onClick='selectFromList2(\"$brand_id\",\"$display_nr\")'>
                <td>$display_nr</td>
                <td>$brand_name</td>
                <td>$name</td>
            </tr>";
        }

        $form = "";
        if ($n > 0) {
            $form_htm = RD . "/tpl/catalogue_brand_select_list.htm";
            if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
            $form = str_replace("{list}", $list, $form);
        }
        $db->query("DROP TEMPORARY TABLE IF EXISTS `NBRAND_RESULT_$tkey`;");

        return $form;
    }

    public function showArticlesSearchDocumentList($art, $brand_id, $search_type): array
    {
        $db = DbSingleton::getTokoDb();
        $n = 0; $list2 = ""; $r = ""; $query = "";

        if ($search_type == 0) {
            $art = $this->clearArticle($art);
            $where_brand = ""; $group_brand = "GROUP BY t2c.BRAND_ID";
            if ($brand_id !== "" && $brand_id > 0) {
                $where_brand = " AND t2c.BRAND_ID = $brand_id";
                $group_brand = "";
            }
            if ($art !== "") {
                $query = "SELECT t2b.BRAND_NAME, t2n.NAME, t2c.BRAND_ID, t2c.DISPLAY_NR, t2c.ART_ID, t2c.KIND, t2c.RELATION 
                FROM `T2_CROSS` t2c 
                    INNER JOIN `T2_BRANDS` t2b on t2b.BRAND_ID=t2c.BRAND_ID
                    LEFT OUTER JOIN `T2_NAMES` t2n on t2n.ART_ID=t2c.ART_ID
                WHERE t2c.SEARCH_NUMBER = '$art' $where_brand $group_brand 
                ORDER BY t2n.NAME ASC;";
                $r = $db->query($query);
                $n = (int)$db->num_rows($r);
            }
            $one_result = 0;
            if ($n > 1 && ($brand_id === "" || $brand_id == 0)) {
                $where_brand = "";
                $list2 = $this->showCatalogueBrandSelectDocumentList($r);
            }
            if ($n === 1) {
                $query = "SELECT t2b.BRAND_NAME, t2n.NAME, t2c.BRAND_ID, t2c.DISPLAY_NR, t2c.ART_ID, t2c.KIND, t2c.RELATION 
                FROM `T2_CROSS` t2c 
                    INNER JOIN `T2_BRANDS` t2b on t2b.BRAND_ID=t2c.BRAND_ID
                    LEFT OUTER JOIN `T2_NAMES` t2n on t2n.ART_ID=t2c.ART_ID
                WHERE t2c.SEARCH_NUMBER = '$art' $where_brand 
                ORDER BY t2n.NAME ASC;";
                $r = $db->query($query);
                $n = (int)$db->num_rows($r);
                $one_result = 1;
            }
            if (($n > 1 && $brand_id !== "") || $one_result === 1) {
                $ak = array(); $rk = array();
                $art_id_arr = [];
                for ($i = 1; $i <= $n; $i++) {
                    $ART_ID = $db->result($r, $i - 1, "ART_ID");
                    $KIND = (int)$db->result($r, $i - 1, "KIND");
                    $RELATION = (int)$db->result($r, $i - 1, "RELATION");

                    $art_id_arr[] = $ART_ID;
                    if (($ak[$ART_ID] === "") || $KIND === 0) {
                        $ak[$ART_ID] = $KIND;
                    }
                    if (($rk[$ART_ID] === "") || $RELATION === 0) {
                        $rk[$ART_ID] = $RELATION;
                    }
                }

                $art_id_str = implode(",", array_unique($art_id_arr));

                $query = "SELECT t2a.ART_ID, t2a.BRAND_ID, t2a.ARTICLE_NR_DISPL, t2b.BRAND_NAME, t2n.NAME, t2n.INFO, t2bc.BARCODE, t2ic.CROSS as inner_cross, 
                gg.NAME as goods_group_name, u.name as unit_name, cs.COSTUMS_ID, cs.COSTUMS_CODE, cc.COUNTRY_NAME
                FROM `T2_ARTICLES` t2a 
                    LEFT OUTER JOIN `T2_BRANDS` t2b on t2b.BRAND_ID=t2a.BRAND_ID 
                    LEFT OUTER JOIN `T2_NAMES` t2n on t2n.ART_ID=t2a.ART_ID 
                    LEFT OUTER JOIN `T2_BARCODES` t2bc on t2bc.ART_ID=t2a.ART_ID 
                    LEFT OUTER JOIN `T2_INNER_CROSS` t2ic on t2ic.ART_ID=t2a.ART_ID 
                    LEFT OUTER JOIN `T2_GOODS_GROUP` t2gg on t2gg.ART_ID=t2a.ART_ID 
                    LEFT OUTER JOIN `GOODS_GROUP` gg on gg.ID=t2gg.GOODS_GROUP_ID 
                    LEFT OUTER JOIN `T2_PACKAGING` t2p on t2p.ART_ID=t2a.ART_ID 
                    LEFT OUTER JOIN `units` u on u.id=t2p.UNITS_ID 
                    LEFT OUTER JOIN `T2_ZED` t2z on t2z.ART_ID=t2a.ART_ID 
                    LEFT OUTER JOIN `T2_COUNTRIES` cc on cc.COUNTRY_ID=t2z.COUNTRY_ID 
                    LEFT OUTER JOIN `T2_COSTUMS` cs on cs.COSTUMS_ID=t2z.COSTUMS_ID 
                WHERE t2a.ART_ID IN ($art_id_str)";
            }
        }

        if ($search_type == 1) {
            $query = "SELECT t2a.ART_ID, t2a.BRAND_ID, t2a.ARTICLE_NR_DISPL, t2b.BRAND_NAME, t2n.NAME, t2n.INFO, t2bc.BARCODE, t2ic.CROSS as inner_cross, 
            gg.NAME as goods_group_name, u.name as unit_name, cs.COSTUMS_ID, cs.COSTUMS_CODE, cc.COUNTRY_ID, cc.COUNTRY_NAME
            FROM `T2_ARTICLES` t2a 
                LEFT OUTER JOIN `T2_BRANDS` t2b on t2b.BRAND_ID=t2a.BRAND_ID 
                LEFT OUTER JOIN `T2_NAMES` t2n on t2n.ART_ID=t2a.ART_ID 
                LEFT OUTER JOIN `T2_BARCODES` t2bc on t2bc.ART_ID=t2a.ART_ID 
                LEFT OUTER JOIN `T2_INNER_CROSS` t2ic on t2ic.ART_ID=t2a.ART_ID 
                LEFT OUTER JOIN `T2_GOODS_GROUP` t2gg on t2gg.ART_ID=t2a.ART_ID 
                LEFT OUTER JOIN `GOODS_GROUP` gg on gg.ID=t2gg.GOODS_GROUP_ID 
                LEFT OUTER JOIN `T2_PACKAGING` t2p on t2p.ART_ID=t2a.ART_ID 
                LEFT OUTER JOIN `units` u on u.id=t2p.UNITS_ID 
                LEFT OUTER JOIN `T2_ZED` t2z on t2z.ART_ID=t2a.ART_ID 
                LEFT OUTER JOIN `T2_COUNTRIES` cc on cc.COUNTRY_ID=t2z.COUNTRY_ID 
                LEFT OUTER JOIN `T2_COSTUMS` cs on cs.COSTUMS_ID=t2z.COSTUMS_ID 
            WHERE t2a.ARTICLE_NR_SEARCH='$art' OR t2a.ARTICLE_NR_DISPL='$art';";
        }

        if ($search_type == 2) {
            $query = "SELECT t2a.ART_ID, t2a.BRAND_ID, t2a.ARTICLE_NR_DISPL, t2b.BRAND_NAME, t2n.NAME, t2n.INFO, t2bc.BARCODE, t2ic.CROSS as inner_cross, 
            gg.NAME as goods_group_name, u.name as unit_name, cs.COSTUMS_ID, cs.COSTUMS_CODE, cc.COUNTRY_ID, cc.COUNTRY_NAME
            FROM `T2_ARTICLES` t2a 
                LEFT OUTER JOIN `T2_BRANDS` t2b on t2b.BRAND_ID=t2a.BRAND_ID 
                LEFT OUTER JOIN `T2_NAMES` t2n on t2n.ART_ID=t2a.ART_ID 
                LEFT OUTER JOIN `T2_BARCODES` t2bc on t2bc.ART_ID=t2a.ART_ID 
                LEFT OUTER JOIN `T2_INNER_CROSS` t2ic on t2ic.ART_ID=t2a.ART_ID 
                LEFT OUTER JOIN `T2_GOODS_GROUP` t2gg on t2gg.ART_ID=t2a.ART_ID 
                LEFT OUTER JOIN `GOODS_GROUP` gg on gg.ID=t2gg.GOODS_GROUP_ID 
                LEFT OUTER JOIN `T2_PACKAGING` t2p on t2p.ART_ID=t2a.ART_ID 
                LEFT OUTER JOIN `units` u on u.id=t2p.UNITS_ID 
                LEFT OUTER JOIN `T2_ZED` t2z on t2z.ART_ID=t2a.ART_ID 
                LEFT OUTER JOIN `T2_COUNTRIES` cc on cc.COUNTRY_ID=t2z.COUNTRY_ID 
                LEFT OUTER JOIN `T2_COSTUMS` cs on cs.COSTUMS_ID=t2z.COSTUMS_ID 
            WHERE t2bc.BARCODE='$art';";
        }

        $r = $db->query($query);
        $n = (int)$db->num_rows($r);
        $list = ""; $header_list = "";

        if ($list2 === "") {
            // сработал внешний фильр или основной поиск с выбором бренда
            for ($i = 1; $i <= $n; $i++) {
                $art_id = $db->result($r, $i - 1, "ART_ID");
                $brand_id = $db->result($r, $i - 1, "BRAND_ID");
                $article_nr_displ = $db->result($r, $i - 1, "ARTICLE_NR_DISPL");
                $brand_name = $db->result($r, $i - 1, "BRAND_NAME");
                $name = $db->result($r, $i - 1, "NAME");
                $barcode = $db->result($r, $i - 1, "BARCODE");
                $goods_group_name = $db->result($r, $i - 1, "goods_group_name");
                $costums_id = $db->result($r, $i - 1, "COSTUMS_ID");
                $costums_code = $db->result($r, $i - 1, "COSTUMS_CODE");
                $country_id = $db->result($r, $i - 1, "COUNTRY_ID");
                $country_name = $db->result($r, $i - 1, "COUNTRY_NAME");
                $color = "";
                if (strtoupper(trim($art)) == strtoupper($this->clearArticle($article_nr_displ))) {
                    $color = "background:#0a89da; color:#fff;";
                }
                $list .= "
                <tr style='cursor:pointer; $color' onclick='setArticleToDoc(\"$art_id\",\"$article_nr_displ\",\"$brand_id\",\"$brand_name\",\"$costums_id\",\"$costums_code\",\"$country_id\",\"$country_name\")'>
                    <td class='text-center'>$i</td>
                    <td class='text-center'>$article_nr_displ</td>
                    <td class='text-center'>$brand_name</td>
                    <td class='text-center'>$name</td>
                    <td class='text-center'>$barcode</td>
                    <td class='text-center'>$goods_group_name</td>
                    <td class='text-center'>$art_id</td>
                </tr>";
            }
        }

        return array($header_list, $list, $list2);
    }

    public function showArticleStorageCellsRestForm($art_id): array
    {
        $db = DbSingleton::getTokoDb();
        $form = "";

        if ($art_id > 0) {
            $list = "";
            $form_htm = RD . "/tpl/catalogue_storage_rest_list.htm";
            if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
            $r = $db->query("SELECT t2s.*, s.name as storage_name 
            FROM `T2_ARTICLES_STRORAGE` t2s
                LEFT OUTER JOIN `STORAGE` s ON (s.ID=t2s.STORAGE_ID)
            WHERE t2s.ART_ID='$art_id' AND (t2s.amount>0 OR t2s.reserv_amount>0);");
            $n = (int)$db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $amount = $db->result($r, $i - 1, "AMOUNT");
                $reserv_amount = $db->result($r, $i - 1, "RESERV_AMOUNT");
                $storage_id = $db->result($r, $i - 1, "STORAGE_ID");
                $storage_name = $db->result($r, $i - 1, "storage_name");
                $list .= "    
                <tr align='center'>
                    <td>$i</td>
                    <td>$storage_name</td>
                    <td>$amount</td>
                    <td>$reserv_amount</td>
                    <td><button class='btn btn-xs btn-default' title='Переглянути' onClick='viewArticleReservDocs(\"$art_id\",\"$storage_id\");'><i class='fa fa-eye'></i></button></td>
                    <td><button class='btn btn-xs btn-default' title='Переглянути' onClick='viewArticleCellsRest(\"$art_id\",\"$storage_id\");'><i class='fa fa-eye'></i></button></td>
                </tr>";
            }

            $form = str_replace("{list}", $list, $form);
            [$article_nr_displ, , $brand_name] = $this->getArticleNrDisplBrand($art_id);
            $form = str_replace("{article_nr_displ}", $article_nr_displ . " " . $brand_name, $form);
        }

        return array($form, "Наявність на складах");
    }

    public function showArticlePartitionsRestForm($art_id): array
    {
        $db = DbSingleton::getDb();
        $form = ""; $doc_name = $doc_suppl_name = "";
        if ($art_id > 0) {
            $list = "";
            $form_htm = RD . "/tpl/catalogue_partitions_rest_list.htm";
            if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
            $r = $db->query("SELECT * FROM `T2_ARTICLES_PARTITIONS` 
            WHERE `art_id` = $art_id AND `rest` > 0 
            ORDER BY `id` DESC LIMIT 0,1000;");
            $n = (int)$db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $amount             = $db->result($r, $i - 1, "amount");
                $rest               = $db->result($r, $i - 1, "rest");
                $parrent_type_id    = (int)$db->result($r, $i - 1, "parrent_type_id");
                $parrent_doc_id     = $db->result($r, $i - 1, "parrent_doc_id");
                $price              = $db->result($r, $i - 1, "price");
                $oper_price         = $db->result($r, $i - 1, "oper_price");
                $price_buh_uah      = $db->result($r, $i - 1, "price_buh_uah");
                $price_man_uah      = $db->result($r, $i - 1, "price_man_uah");

                if ($parrent_type_id === 1) {
                    $income = new income;
                    $doc_name = "" . $income->getIncomeDocNom($parrent_doc_id);
                    $doc_suppl_name = "" . $income->getIncomeSupplDocNom($parrent_doc_id);
                }

                $list .= "
                <tr align='center'>
                    <td>$i</td>
                    <td>$doc_name</td>
                    <td>$doc_suppl_name</td>
                    <td>$amount</td>
                    <td>$rest</td>
                    <td>$price</td>
                    <td>$oper_price</td>
                    <td>$price_buh_uah</td>
                    <td>$price_man_uah</td>
                </tr>";
            }

            if ($n === 0) {
                $list = "<tr><td colspan='8' align='center'>Записи відсутні</td></tr>";
            }

            $form = str_replace("{list}", $list, $form);
            [$article_nr_displ, , $brand_name] = $this->getArticleNrDisplBrand($art_id);
            $form = str_replace("{article_nr_displ}", $article_nr_displ . " " . $brand_name, $form);
        }

        return array($form, "Наявність по партіям");
    }

    public function getClientName($id)
    {
        $db = DbSingleton::getDb();
        $id = (int)$id;
        $name = "";
        $r = $db->query("SELECT `name` FROM `A_CLIENTS` WHERE `id` = $id LIMIT 1;");
        $n = (int)$db->num_rows($r);
        if ($n === 1) {
            $name = $db->result($r, 0, "name");
        }
        return $name;
    }

    /*
     * переглянути наявність на складах
     * */
    public function viewArticleReservDocs($art_id, $storage_id): array
    {
        $db = DbSingleton::getDb();
        $list = ""; $form = "";
        $answer = 0; $err = "Помилка";
        if ($art_id > 0) {
            $form_htm = RD . "/tpl/catalogue_storage_reserv_list.htm";
            if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
            [$article_nr_displ, , $brand_name] = $this->getArticleNrDisplBrand($art_id);
            $form = str_replace("{article_nr_displ}", $article_nr_displ . " " . $brand_name, $form);

            $r = $db->query("SELECT j.id, j.prefix, j.doc_nom, j.type_id, j.data, SUM(js.amount) as amount, j.user_id 
            FROM `J_MOVING_STR` js 
                LEFT OUTER JOIN `J_MOVING` j ON (j.id = js.jmoving_id) 
            WHERE js.art_id = '$art_id' AND js.status_jmoving IN (44, 45) AND j.status = '1' AND js.amount > 0 AND js.storage_id_from = '$storage_id' AND 
            (j.oper_status = '30' OR (j.oper_status = '31' AND j.status_jmoving = '49' OR j.status_jmoving = '48')) AND j.parrent_type_id = 0 AND j.parrent_doc_id = 0 
            GROUP BY j.id;");
            $n = (int)$db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $prefix = $db->result($r, $i - 1, "prefix");
                $doc_nom = $db->result($r, $i - 1, "doc_nom");
                $data = $db->result($r, $i - 1, "data");
                $amount = $db->result($r, $i - 1, "amount");
                $jmoving_user_id = $db->result($r, $i - 1, "user_id");
                $user_name = $this->getMediaUserName($jmoving_user_id);
                $list .= "
                <tr>
                    <td>$i</td>
                    <td>$prefix-$doc_nom</td>
                    <td>$data</td>
                    <td>$amount</td>
                    <td></td>
                    <td>$user_name</td>
                </tr>";
            }

            $r = $db->query("SELECT dp.id, dp.prefix, dp.doc_nom, dp.data, SUM(dps.amount) as amount, SUM(dps.amount_collect) as amount_collect, dp.user_id, dp.client_id 
            FROM `J_DP_STR` dps 
                LEFT OUTER JOIN `J_DP` dp ON (dp.id = dps.dp_id) 
            WHERE dps.art_id = '$art_id' AND dps.status_dps = 93 AND dp.status = '1' AND dps.amount > 0 AND dps.location_storage_id = '$storage_id' AND (dp.oper_status = '30' OR dp.oper_status = '31') 
            GROUP BY dp.id;");
            $n = (int)$db->num_rows($r);
            //$dp_id_str = "0";
            for ($i = 1; $i <= $n; $i++) {
                //$dp_id = $db->result($r, $i - 1, "id");
                //$dp_id_str .= ",$dp_id";
                $prefix = $db->result($r, $i - 1, "prefix");
                $doc_nom = $db->result($r, $i - 1, "doc_nom");
                $data = $db->result($r, $i - 1, "data");
                $amount = $db->result($r, $i - 1, "amount");
                $amount_dis = $amount;
                $amount_collect = $db->result($r, $i - 1, "amount_collect");
                if ($amount_collect > 0) {
                    $amount_dis = $amount_collect;
                }
                $dp_user_id = $db->result($r, $i - 1, "user_id");
                $user_name = $this->getMediaUserName($dp_user_id);
                $dp_client_id = $db->result($r, $i - 1, "client_id");
                $client_name = $this->getClientName($dp_client_id);
                $list .= "
                <tr>
                    <td>$i</td>
                    <td>$prefix-$doc_nom</td>
                    <td>$data</td>
                    <td align='right'>$amount_dis</td>
                    <td>$client_name</td>
                    <td>$user_name</td>
                </tr>";
            }

            $r = $db->query("SELECT dp.id, dp.prefix, dp.doc_nom, dp.data, SUM(dps.amount) as amount, SUM(dps.amount_collect) as amount_collect, dp.user_id, dp.client_id 
            FROM `J_DP_STR` dps 
                LEFT OUTER JOIN `J_DP` dp ON (dp.id = dps.dp_id) 
            WHERE dps.art_id = $art_id AND dps.status_dps IN (94,95,96) AND dp.status = 1 AND dps.amount > 0 AND dps.location_storage_id = $storage_id AND (dp.oper_status = 30 OR dp.oper_status = 31) 
            GROUP BY dp.id;");
            $n = (int)$db->num_rows($r);
            // ищем в удаленном отборе склад
            for ($i = 1; $i <= $n; $i++) {
                //$dp_id = $db->result($r, $i - 1, "id");
                //$dp_id_str .= ",$dp_id";
                $prefix = $db->result($r, $i - 1, "prefix");
                $doc_nom = $db->result($r, $i - 1, "doc_nom");
                $data = $db->result($r, $i - 1, "data");
                $amount = $db->result($r, $i - 1, "amount");
                $amount_dis = $amount;
                $amount_collect = $db->result($r, $i - 1, "amount_collect");
                if ($amount_collect > 0) {
                    $amount_dis = $amount_collect;
                }
                $dp_user_id = $db->result($r, $i - 1, "user_id");
                $user_name = $this->getMediaUserName($dp_user_id);
                $dp_client_id = $db->result($r, $i - 1, "client_id");
                $client_name = $this->getClientName($dp_client_id);
                $list .= "<tr>
                    <td>$i</td>
                    <td>$prefix-$doc_nom</td>
                    <td>$data</td>
                    <td align='right'>$amount_dis</td>
                    <td>$client_name</td>
                    <td>$user_name</td>
                </tr>";
            }

            // BACK SUPPL
            $r = $db->query("SELECT j.prefix, j.doc_nom, j.data, j.client_id, j.user_id, js.amount 
            FROM `J_BACK_SUPPL_STR` js
                LEFT JOIN `J_BACK_SUPPL` j ON (j.id = js.back_suppl_id)
            WHERE js.art_id = $art_id AND j.storage_id = $storage_id;");
            $n = (int)$db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $prefix = $db->result($r, $i - 1, "prefix");
                $doc_nom = $db->result($r, $i - 1, "doc_nom");
                $data = $db->result($r, $i - 1, "data");
                $client_id = $db->result($r, $i - 1, "client_id");
                $client_name = $this->getClientName($client_id);
                $user_id = $db->result($r, $i - 1, "user_id");
                $user_name = $this->getMediaUserName($user_id);
                $amount = $db->result($r, $i - 1, "amount");
                $list .= "
                <tr>
                    <td>$i</td>
                    <td>$prefix-$doc_nom</td>
                    <td>$data</td>
                    <td align='right'>$amount</td>
                    <td>$client_name</td>
                    <td>$user_name</td>
                </tr>";
            }

            $form = str_replace("{list}", $list, $form);
            $answer = 1; $err = "";
        }

        return array($answer, $err, $form, "Наявність в документах переміщення");
    }

    public function viewArticleCellsRest($art_id, $storage_id): array
    {
        $db = DbSingleton::getDb(); $dbt = DbSingleton::getTokoDb();
        $list = ""; $form = "";
        $answer = 0; $err = "Помилка";

        if ($art_id > 0) {
            $form_htm = RD . "/tpl/catalogue_storage_cells_rest_list.htm";
            if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
            [$article_nr_displ, , $brand_name] = $this->getArticleNrDisplBrand($art_id);
            $form = str_replace("{article_nr_displ}", $article_nr_displ . " " . $brand_name, $form);

            $r = $dbt->query("SELECT t2sc.AMOUNT, t2sc.RESERV_AMOUNT, sc.cell_value 
            FROM `T2_ARTICLES_STRORAGE_CELLS` t2sc 
                LEFT OUTER JOIN `STORAGE_CELLS` sc ON (sc.id=t2sc.STORAGE_CELLS_ID) 
            WHERE t2sc.storage_id = $storage_id AND t2sc.ART_ID = $art_id AND (t2sc.AMOUNT > 0 OR t2sc.RESERV_AMOUNT > 0) 
            ORDER BY sc.cell_value ASC;");
            $n = (int)$db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $amount = $db->result($r, $i - 1, "AMOUNT");
                $reserv_amount = $db->result($r, $i - 1, "RESERV_AMOUNT");
                $cell_value = $db->result($r, $i - 1, "cell_value");
                $list .= "
                <tr>
                    <td>$i</td>
                    <td>$cell_value</td>
                    <td>$amount</td>
                    <td>$reserv_amount</td>
                </tr>";
            }
            $form = str_replace(array("{list}", "{storage_name}"), array($list, $this->getStorageName($storage_id)), $form);
            $answer = 1; $err = "";
        }

        return array($answer, $err, $form, "Наявність у комірках складу");
    }

    public function getStorageName($sel_id)
    {
        $db = DbSingleton::getTokoDb();
        $sel_id = (int)$sel_id;
        $name = "";
        $r = $db->query("SELECT `name` FROM `STORAGE` WHERE `status` = 1 AND `id` = $sel_id LIMIT 1;");
        $n = (int)$db->num_rows($r);
        if ($n === 1) {
            $name = $db->result($r, 0, "name");
        }
        return $name;
    }

    public function getStorageCellName($sel_id)
    {
        $db = DbSingleton::getTokoDb();
        $name = "";
        if ($sel_id > 0) {
            $r = $db->query("SELECT `cell_value` FROM `STORAGE_CELLS` WHERE `status` = 1 AND `id` = $sel_id LIMIT 1;");
            $n = (int)$db->num_rows($r);
            if ($n === 1) {
                $name = $db->result($r, 0, "cell_value");
            }
        }

        return $name;
    }

    public function showCatalogueDonorForm($art_id): array
    {
        $form = ""; $kind_name = "";
        $form_htm = RD . "/tpl/catalogue_donor_form.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $form = str_replace("{art_id}", $art_id, $form);
        [$search_number, ,] = $this->getArticleNrDisplBrand($art_id);
        $form = str_replace(array("{search_number}", "{display_nr}"), array($search_number, ""), $form);

        return array($form, "Імпорт інформації від донора" . $kind_name);
    }

    public function showCatalogueDonorIndexSearch(): array
    {
        $form = ""; $form_htm = RD . "/tpl/catalogue_donor_search.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }

        return array($form, "Пошук аналогу по індексу");
    }

    public function findCatalogueDonorIndexSearch($index): string
    {
        $db = DbSingleton::getTokoDb();
        $slave = new slave;
        $list = "";
        $index = $slave->qq($index);

        if ($index !== "") {
            $r = $db->query("SELECT t2a.ART_ID, t2a.BRAND_ID, t2a.ARTICLE_NR_DISPL, t2b.BRAND_NAME, t2n.NAME 
            FROM `T2_ARTICLES` t2a 
                LEFT OUTER JOIN `T2_BRANDS` t2b ON (t2b.BRAND_ID=t2a.BRAND_ID) 
                LEFT OUTER JOIN `T2_NAMES` t2n ON (t2n.ART_ID=t2a.ART_ID)
            WHERE t2a.ARTICLE_NR_SEARCH = '$index' OR t2a.ARTICLE_NR_DISPL = '$index';");
            $n = (int)$db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $art_id = $db->result($r, $i - 1, "ART_ID");
                $article_nr_displ = $db->result($r, $i - 1, "ARTICLE_NR_DISPL");
                $brand_id = $db->result($r, $i - 1, "BRAND_ID");
                $brand_name = $db->result($r, $i - 1, "BRAND_NAME");
                $name = $db->result($r, $i - 1, "NAME");
                $list .= "
                <tr style=\"cursor:pointer;\" onClick='setDonorSearchIndex(\"$art_id\",\"$article_nr_displ\",\"$brand_id\");'>
                    <td>$art_id</td>
                    <td>$article_nr_displ</td>
                    <td>$brand_name</td>
                    <td>$name</td>
                </tr>";
            }
        }

        return $list;
    }

    public function saveCatalogueDonorForm($art_id, $display_nr, $art_id2, $ch): array
    {
        $db = DbSingleton::getTokoDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка збереження даних!";
        $art_id = $slave->qq($art_id); $art_id2 = $slave->qq($art_id2); $ch = $slave->qq($ch);

        if ($art_id > 0 && $display_nr !== "" && $art_id2 > 0) {

            if ($ch[1] == 1) {
                $r = $db->query("SELECT `SEARCH_NUMBER`, `KIND`, `BRAND_ID`, `RELATION` FROM `T2_CROSS` WHERE `ART_ID` = $art_id2 AND `KIND` = 3 AND `RELATION` = 0;");
                $n = (int)$db->num_rows($r);
                for ($i = 1; $i <= $n; $i++) {
                    $search_number = $db->result($r, $i - 1, "SEARCH_NUMBER");
                    $kind = $db->result($r, $i - 1, "KIND");
                    $brand_id = $db->result($r, $i - 1, "BRAND_ID");
                    $relation = $db->result($r, $i - 1, "RELATION");

                    $r2 = $db->query("SELECT COUNT(`ART_ID`) as kol FROM `T2_CROSS` 
                    WHERE `ART_ID` = $art_id AND `SEARCH_NUMBER` = '$search_number' AND `KIND` = $kind AND `BRAND_ID` = $brand_id AND `RELATION` = $relation;");
                    $ex_row = $db->result($r2, 0, "kol");
                    if ($ex_row == 0) {
                        $db->query("INSERT INTO `T2_CROSS` (`ART_ID`,`SEARCH_NUMBER`,`KIND`,`BRAND_ID`,`DISPLAY_NR`,`RELATION`) VALUES ('$art_id',N'$search_number','$kind','$brand_id',N'$search_number','$relation');");
                    }
                }
            }

            if ($ch[2] == 1) {
                $r = $db->query("SELECT `SEARCH_NUMBER`, `KIND`, `BRAND_ID`, `RELATION` FROM `T2_CROSS` WHERE `ART_ID` = $art_id2 AND `KIND` = 4 AND `RELATION` = 0;");
                $n = (int)$db->num_rows($r);
                for ($i = 1; $i <= $n; $i++) {
                    $search_number = $db->result($r, $i - 1, "SEARCH_NUMBER");
                    $kind = $db->result($r, $i - 1, "KIND");
                    $brand_id = $db->result($r, $i - 1, "BRAND_ID");
                    $relation = $db->result($r, $i - 1, "RELATION");

                    $r2 = $db->query("SELECT COUNT(`ART_ID`) as kol FROM `T2_CROSS` WHERE `ART_ID` = $art_id AND `SEARCH_NUMBER` = '$search_number' AND `KIND` = $kind AND `BRAND_ID` = $brand_id AND `RELATION` = $relation;");
                    $ex_row = $db->result($r2, 0, "kol");
                    if ($ex_row == 0) {
                        $db->query("INSERT INTO `T2_CROSS` (`ART_ID`,`SEARCH_NUMBER`,`KIND`,`BRAND_ID`,`DISPLAY_NR`,`RELATION`) VALUES ('$art_id',N'$search_number','$kind','$brand_id',N'$search_number','$relation');");
                    }
                }
            }

            if ($ch[3] == 1) {
                $r = $db->query("SELECT `SEARCH_NUMBER`, `KIND`, `BRAND_ID`, `RELATION` FROM `T2_CROSS` WHERE `ART_ID` = $art_id2 AND `KIND` IN (3,4) AND `RELATION` = 1;");
                $n = (int)$db->num_rows($r);
                for ($i = 1; $i <= $n; $i++) {
                    $search_number = $db->result($r, $i - 1, "SEARCH_NUMBER");
                    $kind = $db->result($r, $i - 1, "KIND");
                    $brand_id = $db->result($r, $i - 1, "BRAND_ID");
                    $relation = $db->result($r, $i - 1, "RELATION");

                    $r2 = $db->query("SELECT COUNT(`ART_ID`) as kol FROM `T2_CROSS` WHERE `ART_ID` = $art_id AND `SEARCH_NUMBER` = '$search_number' AND `KIND` = $kind AND `BRAND_ID` = $brand_id AND `RELATION` = $relation;");
                    $ex_row = $db->result($r2, 0, "kol");
                    if ($ex_row == 0) {
                        $db->query("INSERT INTO `T2_CROSS` (`ART_ID`,`SEARCH_NUMBER`,`KIND`,`BRAND_ID`,`DISPLAY_NR`,`RELATION`) VALUES ('$art_id',N'$search_number','$kind','$brand_id',N'$search_number','$relation');");
                    }
                }
            }

            if ($ch[4] == 1) {
                $r = $db->query("SELECT `SEARCH_NUMBER`, `KIND`, `BRAND_ID`, `RELATION` FROM `T2_CROSS` WHERE `ART_ID` = $art_id2 AND `KIND` IN (3,4) AND `RELATION` = 2;");
                $n = (int)$db->num_rows($r);
                for ($i = 1; $i <= $n; $i++) {
                    $search_number = $db->result($r, $i - 1, "SEARCH_NUMBER");
                    $kind = $db->result($r, $i - 1, "KIND");
                    $brand_id = $db->result($r, $i - 1, "BRAND_ID");
                    $relation = $db->result($r, $i - 1, "RELATION");

                    $r2 = $db->query("SELECT COUNT(`ART_ID`) as kol FROM `T2_CROSS` WHERE `ART_ID`='$art_id' AND `SEARCH_NUMBER`='$search_number' AND `KIND`='$kind' AND `BRAND_ID`='$brand_id' AND `RELATION`='$relation';");
                    $ex_row = $db->result($r2, 0, "kol");
                    if ($ex_row == 0) {
                        $db->query("INSERT INTO `T2_CROSS` (`ART_ID`,`SEARCH_NUMBER`,`KIND`,`BRAND_ID`,`DISPLAY_NR`,`RELATION`) VALUES ('$art_id',N'$search_number','$kind','$brand_id',N'$search_number','$relation');");
                    }
                }
            }

            if ($ch[5] == 1) {
                $r = $db->query("SELECT `TYP_ID`, `LA_ID` FROM `T2_LINKS` WHERE `ART_ID` = $art_id2;");
                $n = (int)$db->num_rows($r);
                for ($i = 1; $i <= $n; $i++) {
                    $typ_id = $db->result($r, $i - 1, "TYP_ID");
                    $r2 = $db->query("SELECT COUNT(`ART_ID`) as kol FROM `T2_LINKS` WHERE `ART_ID` = $art_id AND `TYP_ID` = $typ_id AND `LA_ID` = 0;");
                    $ex_row = $db->result($r2, 0, "kol");
                    if ($ex_row == 0) {
                        $db->query("INSERT INTO `T2_LINKS` (`ART_ID`,`TYP_ID`,`LA_ID`) VALUES ('$art_id','$typ_id','0');");
                    }
                }
            }

            if ($ch[6] == 1) {
                $r = $db->query("SELECT `STR_ID` FROM `T2_TREE` WHERE `ART_ID` = $art_id2;");
                $n = (int)$db->num_rows($r);
                for ($i = 1; $i <= $n; $i++) {
                    $str_id = $db->result($r, $i - 1, "STR_ID");
                    $r2 = $db->query("SELECT COUNT(`ART_ID`) as kol FROM `T2_TREE` WHERE `ART_ID` = $art_id AND `STR_ID` = $str_id;");
                    $ex_row = $db->result($r2, 0, "kol");
                    if ($ex_row == 0) {
                        $db->query("INSERT INTO `T2_TREE` (`ART_ID`,`STR_ID`) VALUES ('$art_id','$str_id');");
                    }
                }
            }

            if ($ch[7] == 1) {
                $r = $db->query("SELECT `TYP_ID`, `LA_ID` FROM `T2_LINKS` WHERE `ART_ID` = $art_id2;");
                $n = (int)$db->num_rows($r);
                for ($i = 1; $i <= $n; $i++) {
                    $typ_id = $db->result($r, $i - 1, "TYP_ID");
                    $la_id = $db->result($r, $i - 1, "LA_ID");
                    $r2 = $db->query("SELECT MAX(`LA_ID`) as mid FROM `link_notes`;");
                    $new_la_id = $db->result($r2, 0, "mid") + 0;
                    $r2 = $db->query("SELECT `SORT`, `TYPE`, `TEXT_NAME`, `TEXT` FROM `link_notes` WHERE `LA_ID` = $la_id AND `LANG_ID` = 16 AND `DISPLAY` = 1;");
                    $n2 = $db->num_rows($r2);
                    for ($j = 1; $j <= $n2; $j++) {
                        $sort       = $db->result($r2, $j - 1, "SORT");
                        $type       = $db->result($r2, $j - 1, "TYPE");
                        $text_name  = $db->result($r2, $j - 1, "TEXT_NAME");
                        $text       = $db->result($r2, $j - 1, "TEXT");
                        ++$new_la_id;
                        $db->query("INSERT INTO `T2_LINKS` (`ART_ID`, `TYP_ID`, `LA_ID`) VALUES ($art_id, $typ_id, $new_la_id);");
                        $db->query("INSERT INTO `link_notes` (`LA_ID`, `LANG_ID`, `SORT`, `TEXT_NAME`, `TYPE`, `TEXT`, `DISPLAY`) VALUES ('$new_la_id', '16', '$sort', '$text_name', '$type', '$text', '1');");
                    }
                }
            }

            $answer = 1; $err = "";
        }

        return array($answer, $err);
    }

    public function showArticleSearchDocumentForm($brand_id, $article_nr_display, $doc_type, $doc_id)
    {
        $form = ""; $form_htm = RD . "/tpl/catalogue_document.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $form = str_replace("{art}", $article_nr_display, $form);
        $form = str_replace("{brand_id}", $brand_id, $form);
        $form = str_replace("{doc_type}", $doc_type, $form);
        $form = str_replace("{doc_id}", $doc_id, $form);
        [$header_list, $range_list, $list_brand_select] = $this->showArticlesSearchListDoc($article_nr_display, $brand_id, "", 0, $doc_type, $doc_id);
        $form = str_replace("{header_list}", $header_list, $form);
        $form = str_replace("{range_list}", $range_list, $form);
        $form = str_replace("{list_brand_select}", $list_brand_select, $form);
        $form = str_replace("{fil4BrandList}", $this->showBrandListSelect(""), $form);
        $form = str_replace("{fil4SupplList}", $this->showSupplListSelect(""), $form);
        $form = str_replace("{fil4GoodsGroupList}", $this->showGoodsGroupListSelect(""), $form);
        $form = str_replace("{fil4Top}", "", $form);
        $form = str_replace("{fil4StokTo}", "", $form);
        $form = str_replace("{fil4StokFrom}", "", $form);
        $form = str_replace("{fil2ManufactureList}", $this->showManufactureListSelect(""), $form);
        $form = str_replace("{fil2StrId}", "", $form);
        $form = str_replace("{fil2StrText}", "", $form);
        return $form;
    }

    public function getTpointStorageList()
    {
        $db = DbSingleton::getDb();
        $list = 0;
        $week_day = date("N");
        $cur_time = date("H:i:s");
        $r = $db->query("SELECT ps.storage_id 
        FROM `T_POINT_STORAGE` ps 
            LEFT OUTER JOIN `T_POINT_DELIVERY_TIME` pdt ON (pdt.storage_id = ps.storage_id) 
        WHERE ps.status = 1 AND pdt.week_day = '$week_day' AND pdt.time_from <= '$cur_time' AND pdt.time_to >= '$cur_time' 
        ORDER BY pdt.delivery_days ASC;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $list .= "," . $db->result($r, $i - 1, "storage_id");
        }
        return $list;
    }

    public function getOriginalEquipment($art_id): array
    {
        $db = DbSingleton::getTokoDb();

        $arts = $art_id_arr = [];

        $r = $db->query("SELECT `SEARCH_NUMBER`, `BRAND_ID` FROM `T2_CROSS` 
        WHERE `ART_ID` = $art_id AND ((`KIND` = 3 AND `RELATION` = 0) OR (`KIND` IN (3, 4) AND `RELATION` = 1) OR (`KIND` IN (3, 4) AND `RELATION` = 2)) 
        GROUP BY `SEARCH_NUMBER` LIMIT 0,10;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $art_search = $db->result($r, $i - 1, "SEARCH_NUMBER");
            $brand_id   = $db->result($r, $i - 1, "BRAND_ID");
            $arts[$i]   = [
                "search_number" => $art_search,
                "brand_id"      => $brand_id
            ];
        }

        foreach ($arts as $art) {
            $art_search = $art["search_number"];
            $brand_id   = $art["brand_id"];

            $r = $db->query("SELECT `ART_ID` FROM `T2_CROSS` 
            WHERE `SEARCH_NUMBER` = '$art_search' AND `BRAND_ID` = $brand_id AND ((`KIND` = 3 AND `RELATION` = 0) OR (`KIND` = 0 AND `RELATION` = 0));");
            $n = (int)$db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $cross_art_id = $db->result($r, $i - 1, "ART_ID");
                $art_id_arr[] = $cross_art_id;
            }
        }

        return $art_id_arr;
    }

    // ARTICLE SEARCH
    public function showArticlesSearchListDoc($art, $brand_id_sel, $query_2, $search_type, $doc_type, $doc_id): array
    {
        $db = DbSingleton::getTokoDb();
        $slave = new slave; $dp = new dp;
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $art = $this->clearArticle($art);
        [$true_art_id] = $this->getArtID($art);
        if ($brand_id_sel == 0) {
            $brand_id_sel = "";
        }
        $ak = $rk = [];
        $margin_price_lvl = $tpoint_id = $margin_price_suppl_lvl = $client_vat = $price_suppl_lvl = 0;
        $r = ""; $query = "";
        $cash_id = 1; $price = 0; $usd_to_uah = $euro_to_uah = 1;
        $storage_id = $dp_id = 0;
        $function_select_article = $reserv_type_color = "";
        $suppl_id = $amountRestTpoint = $amountRestNotTpoint = 0;
        $warranty_info = $return_delay = $delivery_info = "";
        $doc_type = $slave->qq($doc_type);
        $doc_id = $slave->qq($doc_id);
        $tpoint_storage_list = "0"; $price_lvl = 1; $list2 = ""; $n = 0; $markup_min = 0;

        if ($doc_type === "dp") {
            $dp_id = $doc_id;
            [$price_lvl, $margin_price_lvl, $price_suppl_lvl, $margin_price_suppl_lvl, $client_vat] = $dp->getDpClientPriceLevels($dp_id);
            $tpoint_id = $dp->getDpTpoint($dp_id);
            $tpoint_storage_list = $this->getTpointStorageList();
            $cash_id = $dp->getDpCashId($dp_id);
            [$usd_to_uah, $euro_to_uah] = $dp->getKoursData();
            $markup_min = $dp->getClientMarkupMin($dp->getClientFromDp($dp_id));
        }

        $query_tpl = "SELECT t2a.ART_ID, t2a.BRAND_ID, t2a.ARTICLE_NR_DISPL, t2b.BRAND_NAME, t2n.NAME, t2n.INFO, t2bc.BARCODE, t2apr.minMarkup, t2aps.OPER_PRICE,  
        IFNULL(gg.NAME, '') as goods_group_name, 
        IFNULL(t2si.suppl_id, 0) as suppl_id, 
        IFNULL(s.id, 0) as storage_id, 
        IFNULL(s.name, '') as storage_name, 
        IFNULL(t2apr.price_" . $price_lvl . ", 0) as price, 
        IFNULL(t2si.return_delay, 0) as return_delay, 
        IFNULL(t2si.warranty_info, '') as warranty_info, 
        IFNULL(t2si.price_usd, 0) as price_suppl, 
        IFNULL(t2si.client_storage_id, 0) as suppl_storage_id, 
        IFNULL(t2si.stock_suppl, 0) as suppl_stock
        FROM `T2_ARTICLES` t2a 
            LEFT OUTER JOIN `T2_BRANDS` t2b on t2b.BRAND_ID=t2a.BRAND_ID 
            LEFT OUTER JOIN `T2_NAMES` t2n on t2n.ART_ID=t2a.ART_ID 
            LEFT OUTER JOIN `T2_BARCODES` t2bc on t2bc.ART_ID=t2a.ART_ID 
            LEFT OUTER JOIN `T2_GOODS_GROUP` t2gg on t2gg.ART_ID=t2a.ART_ID 
            LEFT OUTER JOIN `GOODS_GROUP` gg on gg.ID=t2gg.GOODS_GROUP_ID 
            LEFT OUTER JOIN `T2_ARTICLES_STRORAGE` t2asc on (t2asc.ART_ID=t2a.ART_ID and t2asc.STORAGE_ID in ($tpoint_storage_list))
            LEFT OUTER JOIN `T2_ARTICLES_PRICE_RATING` t2apr on (t2apr.art_id=t2a.ART_ID and t2apr.in_use=1)
            LEFT OUTER JOIN `T2_ARTICLES_PRICE_STOCK` t2aps on (t2aps.ART_ID=t2a.ART_ID)
            LEFT OUTER JOIN `T2_SUPPL_IMPORT` t2si on (t2si.art_id=t2a.ART_ID and t2si.status=1)
            LEFT OUTER JOIN `STORAGE` s on (s.id=t2asc.STORAGE_ID and s.status=1) ";

        if ($query_2 === "" && $search_type == 0) {
            $where_brand = "";
            $group_brand = "GROUP BY t2c.BRAND_ID";

            if ($brand_id_sel !== "" && $brand_id_sel > 0) {
                $where_brand = " AND t2c.BRAND_ID = $brand_id_sel";
                $group_brand = "";
            }

            if ($art !== "") {
                $query = "SELECT t2b.BRAND_NAME, t2n.NAME, t2c.BRAND_ID, t2c.DISPLAY_NR, t2c.ART_ID, t2c.KIND, t2c.RELATION
                FROM `T2_CROSS` t2c
                    INNER JOIN `T2_BRANDS` t2b ON (t2b.BRAND_ID = t2c.BRAND_ID)
                    LEFT OUTER JOIN `T2_NAMES` t2n ON (t2n.ART_ID = t2c.ART_ID)
                WHERE t2c.SEARCH_NUMBER = '$art' $where_brand
                $group_brand
                ORDER BY t2n.NAME ASC;";

                $r = $db->query($query);
                $n = (int)$db->num_rows($r);
            }

            $one_result = 0;
            if ($n > 1 && $brand_id_sel === "") {
                $where_brand = "";
                $list2 = $this->showCatalogueBrandSelectListDoc($r);
            }

            if ($n === 1) {
                $query = "SELECT t2b.BRAND_NAME, t2n.NAME, t2c.BRAND_ID, t2c.DISPLAY_NR, t2c.ART_ID, t2c.KIND, t2c.RELATION 
                FROM `T2_CROSS` t2c 
                    INNER JOIN `T2_BRANDS` t2b ON (t2b.BRAND_ID = t2c.BRAND_ID)
                    LEFT OUTER JOIN `T2_NAMES` t2n ON (t2n.ART_ID = t2c.ART_ID)
                WHERE t2c.SEARCH_NUMBER = '$art' $where_brand 
                ORDER BY t2n.NAME ASC;";
                $r = $db->query($query);
                $n = (int)$db->num_rows($r);
                $one_result = 1;
            }

            if (($n > 1 && $brand_id_sel !== "") || $one_result === 1) {
                $ak = array(); $rk = array();
                $art_id_arr = [];
                for ($i = 1; $i <= $n; $i++) {
                    $ART_ID     = $db->result($r, $i - 1, "ART_ID");
                    $KIND       = (int)$db->result($r, $i - 1, "KIND");
                    $RELATION   = (int)$db->result($r, $i - 1, "RELATION");

                    $art_id_arr[] = $ART_ID;
                    if (($ak[$ART_ID] === "") || $KIND === 0) {
                        $ak[$ART_ID] = $KIND;
                    }
                    if (($rk[$ART_ID] === "") || $RELATION === 0) {
                        $rk[$ART_ID] = $RELATION;
                    }
                }

                // OE
                $ro = $db->query("SELECT `ART_ID` FROM `T2_ARTICLES` WHERE `ARTICLE_NR_SEARCH` = '$art'  LIMIT 1;");
                $no = $db->num_rows($ro);
                if ($no > 0) {
                    $art_id             = $db->result($ro, 0, "ART_ID");
                    $where_oe_art_id    = $this->getOriginalEquipment($art_id);
                    $art_id_arr         = array_merge($art_id_arr, $where_oe_art_id);
                }

                $art_id_str = implode(",", array_unique($art_id_arr));
                $query = $query_tpl . "	WHERE t2a.ART_ID IN ($art_id_str) AND t2b.`VISIBLE` = '1' ORDER BY `suppl_id` ASC;";
            }
        }

        if ($query_2 === "" && $search_type == 1) {
            $query = $query_tpl . "	WHERE t2a.ARTICLE_NR_SEARCH = '$art' OR t2a.ARTICLE_NR_DISPL = '$art' AND t2b.`VISIBLE` = '1';";
        }

        if ($query_2 === "" && $search_type == 2) {
            $query = $query_tpl . " WHERE t2bc.BARCODE = '$art' AND t2b.`VISIBLE` = '1';";
        }

        if ($query_2 === "" && $search_type == 3) {
            $query = $query_tpl . " WHERE t2a.ART_ID = '$art' AND t2b.`VISIBLE` ='1';";
        }

        if ($query_2 !== "") {
            $query = $query_2;
        }

        $list = ""; $header_list = "";
        $r = $db->query($query);
        $n = (int)$db->num_rows($r);

        // сработал внешний фильр или основной поиск с выбором бренда
        if ($query_2 !== "" || $list2 === "") {
            [$fldcnf, $kol_f] = $this->getCatalogueClientViewFieldsData($user_id, "catalogue_doc");
            $range_list = "";
            for ($i = 1; $i <= $kol_f; $i++) {
                $header_list .= "<th>" . $fldcnf[$i]["field_name"] . "</th>";
                $onclick = "{function_select_article}";

                // Залишок ТТ
                if ($i == 5) {
                    $onclick = "";
                }

                $range_list .= "<td onClick=\"$onclick\">{" . $fldcnf[$i]["field_key"] . "}</td>";
            }
            $header_list = "<tr align='center'><th data-sortable=\"false\">Фото</th><th data-sortable=\"false\">Тип артикула</th>" . $header_list . "</tr>";

            $sch_table = "search_cat_$user_id";
            $sch_table_result = "search_cat_$user_id"."_result";

            if ($query !== "") {
                $db->query("DROP TABLE IF EXISTS `$sch_table`;");
                $db->query("DROP TABLE IF EXISTS `$sch_table_result`;");

                $db->query("CREATE TEMPORARY TABLE IF NOT EXISTS `$sch_table` AS $query");
                $db->query("ALTER TABLE `$sch_table` 
                    ADD INDEX ( `art_id` );");
                $db->query("ALTER TABLE `$sch_table` 
                    ADD `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST ;");
                $db->query("ALTER TABLE `$sch_table` 
                    ADD `tpoint_stock` MEDIUMINT NOT NULL AFTER `storage_name`, 
                    ADD `tpoint_reserv` MEDIUMINT NOT NULL AFTER `tpoint_stock`, 
                    ADD `not_tpoint_stock` MEDIUMINT NOT NULL AFTER `tpoint_reserv`, 
                    ADD `not_tpoint_reserv` MEDIUMINT NOT NULL AFTER `not_tpoint_stock`, 
                    ADD `delivery_info` VARCHAR( 255 ) NOT NULL AFTER `not_tpoint_reserv`;");
                $db->query("ALTER TABLE `$sch_table` 
                    ADD `kind_id` SMALLINT NOT NULL AFTER `ART_ID`, 
                    ADD `relation` SMALLINT NOT NULL AFTER `kind_id`;");

                $r = $db->query("SELECT `id`, `art_id`, `suppl_id`, `storage_id`, `suppl_storage_id`, `price`, `minMarkup`, `OPER_PRICE`, `price_suppl` FROM `$sch_table`;");
                $n = (int)$db->num_rows($r);
                for ($i = 1; $i <= $n; $i++) {
                    $id         = $db->result($r, $i - 1, "id");
                    $art_id     = $db->result($r, $i - 1, "art_id");
                    $kind_id    = $ak[$art_id];
                    $relation   = $rk[$art_id];
                    $suppl_id   = $db->result($r, $i - 1, "suppl_id");
                    $storage_id = $db->result($r, $i - 1, "storage_id");

                    if ($doc_type === "dp") {

                        if ($suppl_id == 0 || $storage_id > 0) {
                            $price      = $db->result($r, $i - 1, "price");
                            $minMarkup  = $db->result($r, 0, "minMarkup");
                            $OPER_PRICE = $db->result($r, 0, "OPER_PRICE");

                            if ($margin_price_lvl > 0) {
                                $price = $price + round($price * $margin_price_lvl / 100, 2);
                            }
                            if ($margin_price_lvl < 0 && $markup_min == 0) {
                                $price_minus    = $price + ($price * $margin_price_lvl / 100);
                                $oper_limit     = $OPER_PRICE + ($OPER_PRICE * $minMarkup / 100);
                                if ($price_minus >= $oper_limit) {
                                    $price = $price_minus;
                                } elseif (!($oper_limit >= $price)) {
                                    $price = $oper_limit;
                                }
                            }
                            if ($margin_price_lvl < 0 && $markup_min > 0) {
                                $cash_art_id            = $dp->getArticlePriceRatingCash($art_id);
                                $price                  = $dp->getPriceRatingKours($price, $cash_art_id, 2, $usd_to_uah, $euro_to_uah);
                                $proc_price_margin      = $price - ($price * abs($margin_price_lvl) / 100);
                                $proc_oper_price_min    = $OPER_PRICE + ($OPER_PRICE * $markup_min / 100);

                                if ($proc_price_margin >= $proc_oper_price_min) {
                                    $price = $proc_price_margin;
                                } else {
                                    if (!(($proc_price_margin < $proc_oper_price_min) && ($proc_oper_price_min > $price))) {
                                        $price = $proc_oper_price_min;
                                    }
                                }
                                $price = $dp->getPriceRatingKours($price, 2, $cash_art_id, $usd_to_uah, $euro_to_uah);
                            }

                            [$tpoint_stock, $tpoint_reserv] = $dp->getArticleRestTpoint($art_id, $tpoint_id);
                            [$not_tpoint_stock, $not_tpoint_reserv] = $dp->getArticleRestNotTpoint($art_id, $tpoint_id);
                            $delivery_info = $dp->getArticleTpointDeliveryInfo($tpoint_id, $art_id);
                            $db->query("UPDATE `$sch_table` SET `kind_id` = '$kind_id', `relation` = '$relation', `price` = '$price', `tpoint_stock` = '$tpoint_stock', `tpoint_reserv` = '$tpoint_reserv', `not_tpoint_stock` = '$not_tpoint_stock', `not_tpoint_reserv` = '$not_tpoint_reserv', `delivery_info` = '$delivery_info' WHERE `id` = '$id';");

                            if ($tpoint_stock == 0 && $tpoint_reserv == 0 && $price == 0) {
                                $db->query("DELETE FROM `$sch_table` WHERE `id` = '$id' LIMIT 1;");
                            }
                        }

                        if ($suppl_id > 0 && $storage_id == 0) {

                            $suppl_storage_id = $db->result($r, $i - 1, "suppl_storage_id");

                            if ($this->checkSupplStorageAllow($suppl_id, $suppl_storage_id) == 1) {
//                                if ($this->checkSupplStorageTpointAllow($tpoint_id, $suppl_id, $suppl_storage_id) == 1) {

                                    [$price_in_vat, $show_in_vat, $price_add_vat] = $dp->getSupplVatConditions($suppl_id);
                                    $row_del = 0;

                                    if ($row_del == 0) {
                                        $price_suppl = $db->result($r, $i - 1, "price_suppl");
                                        [$suppl_margin_fm, $suppl_delivery_fm, $suppl_margin2_fm] = $dp->getTpointSupplFm($tpoint_id, $suppl_id, $suppl_storage_id, $price_suppl, $price_suppl_lvl);

                                        if ($suppl_margin_fm > 0) {
                                            $price = ($price_suppl + $price_suppl * $suppl_margin_fm / 100) - $price_suppl;

                                            if ($price > $suppl_delivery_fm) {
                                                $price = ($price_suppl + $price_suppl * $suppl_margin_fm / 100);
                                            }
                                            if ($price <= $suppl_delivery_fm) {
                                                $price = $price_suppl + $price_suppl * $suppl_margin2_fm / 100 + $suppl_delivery_fm;
                                            }

                                            //Step 2; Client Margin
                                            if ($margin_price_suppl_lvl > 0 && $margin_price_suppl_lvl !== "") {
                                                $price = $price + $price * $margin_price_suppl_lvl / 100;
                                            }

                                            //Step 3; VAT
                                            if ($client_vat == 1) {
                                                if ($price_in_vat == 0 && $show_in_vat == 1 && $price_add_vat == 1) {
                                                    $price = $price + $price * 20 / 100;
                                                }
                                                if (!($price_in_vat == 1 || $show_in_vat == 1)) {
                                                    $db->query("DELETE FROM `$sch_table` WHERE `id` = '$id';");
                                                    $row_del = 1;
                                                }
                                            }

                                            if ($row_del == 0) {
                                                $amountRestTpoint = ""; $suppl_stock_show = "";
                                                $amountRestNotTpoint = $suppl_stock_show;
                                                $delivery_info = $dp->getTpointSupplDeliveryInfo($tpoint_id, $suppl_id, $suppl_storage_id);
                                                $db->query("UPDATE `$sch_table` SET `kind_id` = '$kind_id', `relation` = '$relation', `price_suppl` = '$price', `price` = '$price', `delivery_info` = '$delivery_info' WHERE `id` = '$id';");
                                            }
                                        } else {
                                            $db->query("DELETE FROM `$sch_table` WHERE `id` = '$id';");
                                        }
                                    }
//                                } else {
//                                    $db->query("DELETE FROM `$sch_table` WHERE `id` = '$id';");
//                                }
                            } else {
                                $db->query("DELETE FROM `$sch_table` WHERE `id` = '$id';");
                            }
                        }
                    }
                }

                $db->query("CREATE TEMPORARY TABLE IF NOT EXISTS `$sch_table_result` AS (SELECT * FROM `$sch_table` ORDER BY `art_id`, `delivery_info`, `price` ASC);");

//                $db->query("DELETE t1 FROM `$sch_table_result` t1
//                INNER JOIN `$sch_table_result` t2
//                WHERE
//                    t1.`id` > t2.`id` AND
//                    t1.`ART_ID` = t2.`ART_ID`;");
                $prev_art_id = 0;
                $r = $db->query("SELECT `id`, `ART_ID` FROM `$sch_table_result`;");
                $n = (int)$db->num_rows($r);
                for ($i = 1; $i <= $n; $i++) {
                    $id     = $db->result($r, $i - 1, "id");
                    $art_id = (int)$db->result($r, $i - 1, "ART_ID");
                    if ($art_id === $prev_art_id) {
                        $db->query("DELETE FROM `$sch_table_result` WHERE `id` = '$id' LIMIT 1;");
                    }
                    if ($art_id !== $prev_art_id) {
                        $prev_art_id = $art_id;
                    }
                }

                $order_by = "";
                if (!empty($true_art_id)) {
                    $order_by = " ART_ID = $true_art_id DESC, ";
                }

                $r = $db->query("SELECT * FROM `$sch_table_result` ORDER BY $order_by `tpoint_stock` DESC, `not_tpoint_stock` DESC;");
                $n = (int)$db->num_rows($r);
            }

            $lst = array();
            for ($i = 1; $i <= $n; $i++) {
                $tpoint_suppl_name  = "";
                $art_id             = $db->result($r, $i - 1, "ART_ID");
                $kind_id            = $db->result($r, $i - 1, "kind_id");
                $relation           = $db->result($r, $i - 1, "relation");
                $brand_id           = $db->result($r, $i - 1, "BRAND_ID");
                $article_nr_displ   = $db->result($r, $i - 1, "ARTICLE_NR_DISPL");
                $brand_name         = $db->result($r, $i - 1, "BRAND_NAME");
                $name               = $db->result($r, $i - 1, "NAME");
                $info               = $db->result($r, $i - 1, "INFO");
                $barcode            = $db->result($r, $i - 1, "BARCODE");
                $goods_group_name   = $db->result($r, $i - 1, "goods_group_name");

                if ($doc_type === "dp") {
                    $suppl_id       = $db->result($r, $i - 1, "suppl_id");
                    $storage_id     = $db->result($r, $i - 1, "storage_id");
                    $delivery_info  = $db->result($r, $i - 1, "delivery_info");

                    if ($suppl_id == 0 || $storage_id > 0) {
                        $price                  = $db->result($r, $i - 1, "price");
                        $tpoint_stock           = $db->result($r, $i - 1, "tpoint_stock");
                        $tpoint_reserv          = $db->result($r, $i - 1, "tpoint_reserv");
                        $not_tpoint_stock       = $db->result($r, $i - 1, "not_tpoint_stock");
                        $not_tpoint_reserv      = $db->result($r, $i - 1, "not_tpoint_reserv");
                        $amountRestTpoint       = "<span class='badge badge-primary'>$tpoint_stock / $tpoint_reserv</span>";
                        $amountRestNotTpoint    = "<span class='badge badge-warning'>$not_tpoint_stock / $not_tpoint_reserv</span>";

                        if ($tpoint_stock == 0 && $tpoint_reserv == 0 && $not_tpoint_stock == 0 && $not_tpoint_reserv == 0) {
                            $amountRestTpoint .= "<div style='margin-top: 15px;'><button class='btn btn-danger btn-xl' onclick=\"showSupplStorageSelectWindow('$art_id','$article_nr_displ','$brand_id','$brand_name','dp','$dp_id');\"><i class='fa fa-support'></i> Залишки по складам</button></div>";
                        }
                    }

                    if ($suppl_id > 0 && $storage_id == 0) {
                        $return_delay       = $db->result($r, $i - 1, "return_delay");
                        $warranty_info      = $db->result($r, $i - 1, "warranty_info");
                        $price              = $db->result($r, $i - 1, "price_suppl");
                        $suppl_stock        = $db->result($r, $i - 1, "stock_suppl");
                        $suppl_stock_show   = $suppl_stock;
                        if ($suppl_stock_show >= 10) {
                            $suppl_stock_show = ">10";
                        }
                        $amountRestTpoint = "";
                        $amountRestNotTpoint = $suppl_stock_show;
                    }
                }

                if ($cash_id == 1) {
                    $price = round($price * $usd_to_uah, 2);
                }
                if ($cash_id == 3) {
                    $price = round($price * $usd_to_uah / $euro_to_uah, 2);
                }
                if ($cash_id == 2) {
                    $price = round($price, 2);
                }

                $client_id = $dp->getDpClientId($dp_id);
                $price_round = $dp->getClientPriceRounding($client_id,$price);

                $lst[$i]["kind"] = $kind_id;
                $lst[$i]["relation"] = $relation;

                $check_photo = $this->checkPhotoEmpty($art_id);
                $img_b = "";
                if ($check_photo > 0) {
                    $img_b = "<button class='btn btn-sm btn-default' onclick='showArtilceGallery(\"$art_id\",\"$article_nr_displ\")'><i class='fa fa-image'></i></button>";
                }

                $suppl_storage_code = 0;
                $lst[$i]["data"] = "<tr style='cursor:pointer'><td class='text-center'>$img_b</td><td class='text-center'>{kind_name}</td>" . $range_list . "</tr>";
                $lst[$i]["data"] = str_replace("{art_id}", $art_id, $lst[$i]["data"]);
                $lst[$i]["data"] = str_replace("{article_nr_displ}", $article_nr_displ, $lst[$i]["data"]);
                $lst[$i]["data"] = str_replace("{brand_name}", $brand_name, $lst[$i]["data"]);
                $lst[$i]["data"] = str_replace("{name}", $name, $lst[$i]["data"]);
                $lst[$i]["data"] = str_replace("{info}", $info, $lst[$i]["data"]);
                $lst[$i]["data"] = str_replace("{barcode}", $barcode, $lst[$i]["data"]);
                $lst[$i]["data"] = str_replace("{goods_group_id}", $goods_group_name, $lst[$i]["data"]);
                $lst[$i]["data"] = str_replace("{suppl_id}", $suppl_id, $lst[$i]["data"]);
                $lst[$i]["data"] = str_replace("{price}", $price . " ($price_round)", $lst[$i]["data"]);
                $lst[$i]["data"] = str_replace("{amountRestTpoint}", $amountRestTpoint, $lst[$i]["data"]);
                $lst[$i]["data"] = str_replace("{amountRestNotTpoint}", $amountRestNotTpoint, $lst[$i]["data"]);
                $lst[$i]["data"] = str_replace("{suppl_storage_code}", $suppl_storage_code, $lst[$i]["data"]);
                $lst[$i]["data"] = str_replace("{warranty_info}", $warranty_info, $lst[$i]["data"]);
                $lst[$i]["data"] = str_replace("{return_delay}", $return_delay, $lst[$i]["data"]);
                $lst[$i]["data"] = str_replace("{delivery_info}", $delivery_info, $lst[$i]["data"]);
                $lst[$i]["data"] = str_replace("{tpoint_suppl}", $tpoint_suppl_name, $lst[$i]["data"]);

                if ($doc_type === "dp") {
                    if ($suppl_id == 0 || $storage_id > 0) {
                        $function_select_article = "setArticleToSelectAmountDp('$art_id','$article_nr_displ','$brand_id','$brand_name','$dp_id');";
                    }
                    if ($suppl_id > 0 && $storage_id == 0) {
                        $function_select_article = "showSupplStorageSelectWindow('$art_id','$article_nr_displ','$brand_id','$brand_name','dp','$dp_id');";
                        $reserv_type_color = "danger";
                    }
                    $lst[$i]["data"] = str_replace("{function_select_article}", $function_select_article, $lst[$i]["data"]);
                    $lst[$i]["data"] = str_replace("{reserv_type_color}", $reserv_type_color, $lst[$i]["data"]);
                }
            }

            $lst_kr = array();
            for ($i = 1; $i <= $n; $i++) {
                $kind = $lst[$i]["kind"];
                $relation = $lst[$i]["relation"];
                if ($kind == 0 && $relation == 0) { $lst_kr[1] .= $lst[$i]["data"]; }
                if ($kind == 1 && $relation == 0) { $lst_kr[2] .= $lst[$i]["data"]; }
                if (($kind == 3 || $kind == 4) && $relation == 0) { $lst_kr[2] .= $lst[$i]["data"]; }
                if (($kind == 3 || $kind == 4) && $relation == 1) { $lst_kr[3] .= $lst[$i]["data"]; }
                if (($kind == 3 || $kind == 4) && $relation == 2) { $lst_kr[4] .= $lst[$i]["data"]; }
                if ($kind === "" || $relation === "") { $lst_kr[5] .= $lst[$i]["data"]; }
            }

            if ($lst_kr[1] !== "") {
                $lst_kr[1] = str_replace("{kind_name}","<i style=\"width: 100%; height: 60px;\" title=\"запитаний артикул\" class=\"fa fa-key\"></i>", $lst_kr[1]);
                $list .= $lst_kr[1];
            }

            if ($lst_kr[2] !== "") {
                $lst_kr[2] = str_replace("{kind_name}","<i style=\"width: 100%; height: 60px;\" title=\"аналог\" class=\"fa fa-link\"></i>", $lst_kr[2]);
                $list .= $lst_kr[2];
            }

            if ($lst_kr[3] !== "") {
                $lst_kr[3] = str_replace("{kind_name}","<i style=\"width: 100%; height: 60px;\" title=\"артикул присутні в\" class=\"fa fa-level-down\"></i>", $lst_kr[3]);
                $list .= $lst_kr[3];
            }

            if ($lst_kr[4] !== "") {
                $lst_kr[4] = str_replace("{kind_name}","<i style=\"width: 100%; height: 60px;\" title=\"артикул включає в себе\" class=\"fa fa-level-up\"></i>", $lst_kr[4]);
                $list .= $lst_kr[4];
            }

            if ($lst_kr[5] !== "") {
                $lst_kr[5] = str_replace("{kind_name}","<i style=\"width: 100%; height: 60px;\" title=\"інше\" class=\"fa fa-ellipsis-h\"></i>", $lst_kr[5]);
                $list .= $lst_kr[5];
            }
        }

        return array($header_list, $list, $list2);
    }

    public function checkSupplStorageAllow($suppl_id, $storage_id)
    {
        $db = DbSingleton::getDb();
        $r = $db->query("SELECT COUNT(`id`) as kol FROM `A_CLIENTS_STORAGE` WHERE `visible` = '1' AND `id` = '$storage_id' AND `client_id` = '$suppl_id' LIMIT 1;");
        return $db->result($r, 0, "kol") + 0;
    }

    public function checkSupplStorageTpointAllow($tpoint_id, $suppl_id, $storage_id)
    {
        $db = DbSingleton::getDb();
        $r = $db->query("SELECT COUNT(`id`) as kol FROM `T_POINT_SUPPL_STORAGE` WHERE `tpoint_id` = '$tpoint_id' AND `storage_id` = '$storage_id' AND `suppl_id` = '$suppl_id' LIMIT 1;");
        return $db->result($r, 0, "kol") + 0;
    }

    public function showSupplStorageSelectWindow($art_id, $article_nr_displ, $doc_type, $doc_id) {
        $form = ""; $form_htm = RD . "/tpl/catalogue_select_suppl_storage_form.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $list = $this->showArticleSupplStorageRestList($art_id, $doc_type, $doc_id);
        $form = str_replace(array("{list}", "{art_id}", "{article_nr_displ}"), array($list, $art_id, $article_nr_displ), $form);

        return $form;
    }

    public function showArticleSupplStorageRestList($art_id_sel, $doc_type, $doc_id): string
    {
        $db = DbSingleton::getTokoDb();
        $slave = new slave; $dp = new dp;
        $price_suppl_lvl = 0;
        $doc_type = $slave->qq($doc_type);
        $doc_id = $slave->qq($doc_id);
        $tpoint_id = 0;
        $price_lvl = $margin_price_lvl = $margin_price_suppl_lvl = $client_vat = 0;
        $cash_id = $usd_to_uah = $euro_to_uah = 1;
        $suppl_id = $suppl_stock = $dp_id = $suppl_storage_id = 0;
        $suppl_storage_code = $amountRestNotTpoint = 0;
        $delivery_info = $return_delay = $warranty_info = "";

        if ($doc_type === "dp") {
            $dp_id = $doc_id;
            [$price_lvl, $margin_price_lvl, $price_suppl_lvl, $margin_price_suppl_lvl, $client_vat] = $dp->getDpClientPriceLevels($dp_id);
            $tpoint_id = $dp->getDpTpoint($dp_id);
            $cash_id = $dp->getDpCashId($dp_id);
            [$usd_to_uah, $euro_to_uah] = $dp->getKoursData();
        }

        $list = "";
        $r = $db->query("SELECT t2a.ART_ID, t2a.BRAND_ID, t2a.ARTICLE_NR_DISPL, t2b.BRAND_NAME, t2n.NAME, t2n.INFO, t2bc.BARCODE, gg.NAME as goods_group_name, t2si.suppl_id, t2si.return_delay, t2si.warranty_info, t2si.price_usd, t2si.client_storage_id, t2si.stock_suppl
        FROM `T2_ARTICLES` t2a 
            LEFT OUTER JOIN `T2_BRANDS` t2b ON (t2b.BRAND_ID = t2a.BRAND_ID) 
            LEFT OUTER JOIN `T2_NAMES` t2n ON (t2n.ART_ID = t2a.ART_ID)
            LEFT OUTER JOIN `T2_BARCODES` t2bc ON (t2bc.ART_ID = t2a.ART_ID) 
            LEFT OUTER JOIN `T2_GOODS_GROUP` t2gg ON (t2gg.ART_ID = t2a.ART_ID) 
            LEFT OUTER JOIN `GOODS_GROUP` gg ON (gg.ID = t2gg.GOODS_GROUP_ID)
            LEFT OUTER JOIN `T2_SUPPL_IMPORT` t2si ON (t2si.art_id = t2a.ART_ID AND t2si.status = 1)
            LEFT OUTER JOIN `T_POINT_SUPPL_STORAGE` pss ON (pss.tpoint_id = '$tpoint_id' AND pss.suppl_id = t2si.suppl_id AND pss.storage_id = t2si.client_storage_id)
        WHERE t2a.ART_ID = $art_id_sel AND t2b.`VISIBLE` = '1' AND t2si.suppl_id > 0 GROUP BY t2a.`ART_ID`, t2si.suppl_id, t2si.client_storage_id;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $del_row            = 0;
            $art_id             = $db->result($r, $i - 1, "ART_ID");
            $brand_id           = $db->result($r, $i - 1, "BRAND_ID");
            $article_nr_displ   = $db->result($r, $i - 1, "ARTICLE_NR_DISPL");
            $brand_name         = $db->result($r, $i - 1, "BRAND_NAME");
            $price              = 0;

            if ($doc_type === "dp") {
                $suppl_id = $db->result($r, $i - 1, "suppl_id");

                if ($suppl_id == 0) {
                    $price = $db->result($r, $i - 1, "price_".$price_lvl);
                    if ($margin_price_lvl > 0) {
                        $price = $price + round($price * $margin_price_lvl / 100, 2);
                    }
                    $storage_id = $db->result($r, $i - 1, "storage_id");
                    $storage_name = $db->result($r, $i - 1, "storage_name");
                    $suppl_storage_code = $storage_name;
                    [$not_tpoint_stock, $not_tpoint_reserv] = $dp->getArticleRestNotTpoint($art_id, $tpoint_id);
                    $amountRestNotTpoint = "<span class='label label-warning'>$not_tpoint_stock/$not_tpoint_reserv</span>";
                    $delivery_info = $dp->getTpointDeliveryInfo($tpoint_id, $storage_id);
                }

                if ($suppl_id > 0) {
                    $return_delay       = $db->result($r, $i - 1, "return_delay");
                    $warranty_info      = $db->result($r, $i - 1, "warranty_info");
                    $suppl_price_usd    = $db->result($r, $i - 1, "price_usd");
                    $suppl_storage_id   = $db->result($r, $i - 1, "client_storage_id");
                    $suppl_storage_name = $this->getSupplStorageName($suppl_storage_id);

//                    if ($this->checkSupplStorageAllow($suppl_id, $suppl_storage_id) == 1) {
//                        if ($this->checkSupplStorageTpointAllow($tpoint_id, $suppl_id, $suppl_storage_id) == 1) {

                            $suppl_stock = $db->result($r, $i - 1, "stock_suppl");
                            [$price_in_vat, $show_in_vat, $price_add_vat] = $dp->getSupplVatConditions($suppl_id);

                            $suppl_storage_code = "$suppl_storage_name (".$suppl_id.".".$suppl_storage_id.")";
                            $price_suppl = $suppl_price_usd;

                            [$suppl_margin_fm, $suppl_delivery_fm, $suppl_margin2_fm] = $dp->getTpointSupplFm($tpoint_id, $suppl_id, $suppl_storage_id, $price_suppl, $price_suppl_lvl);
                            if ($suppl_margin_fm > 0) {
                                $price = ($price_suppl + $price_suppl * $suppl_margin_fm / 100) - $price_suppl;
                                if ($price > $suppl_delivery_fm) { $price = ($price_suppl + $price_suppl * $suppl_margin_fm / 100); }
                                if ($price <= $suppl_delivery_fm) { $price = $price_suppl + $price_suppl * $suppl_margin2_fm / 100 + $suppl_delivery_fm; }
                                if ($margin_price_suppl_lvl > 0 && $margin_price_suppl_lvl !== "") {
                                    $price = $price + $price * $margin_price_suppl_lvl / 100;
                                }
                            }
                            if ($client_vat == 1) {
                                if ($price_in_vat == 0 && $show_in_vat == 1 && $price_add_vat == 1) {
                                    $price = $price + $price * 20 / 100;
                                }
                                if ($price_in_vat == 0 && $show_in_vat == 0) {
                                    $price = 0;
                                    $del_row = 1;
                                }
                            }
                            $suppl_stock_show = $suppl_stock;
                            if ($suppl_stock_show >= 10) {
                                $suppl_stock_show = ">10";
                            }
                            $amountRestNotTpoint = $suppl_stock_show;
                            $delivery_info = $dp->getTpointSupplDeliveryInfo($tpoint_id, $suppl_id, $suppl_storage_id);

//                        } else {
//                            $del_row = 1;
//                        }
//                    } else {
//                        $del_row = 1;
//                    }
                }
            }

            if ($cash_id == 1) {
                $price = round($price * $usd_to_uah, 2);
            }
            if ($cash_id == 3) {
                $price = round($price * $usd_to_uah / $euro_to_uah, 2);
            }
            if ($cash_id == 2) {
                $price = round($price, 2);
            }

            if ($suppl_id > 0 && ($price > 0 || $suppl_stock > 0) && $del_row == 0) {
                $list .= "
                <tr style='cursor:pointer' onClick=\"showDpSupplAmountInputWindow('$art_id','$article_nr_displ','$brand_id','$brand_name','$dp_id','$suppl_id','$suppl_storage_id','$price');\">
                    <td class='text-center'>$i</td>
                    <td class='text-center'>$suppl_storage_code</td>
                    <td class='text-center'>$amountRestNotTpoint</td>
                    <td class='text-center'>$price</td>
                    <td class='text-center'>$delivery_info</td>
                    <td class='text-center'>$return_delay</td>
                    <td class='text-center'>$warranty_info</td>
                </tr>";
            }
        }

        return $list;
    }

    public function getSupplStorageName($suppl_storage_id)
    {
        $db = DbSingleton::getDb();
        $name = "";
        $r = $db->query("SELECT `name` FROM `A_CLIENTS_STORAGE` WHERE `id`='$suppl_storage_id' LIMIT 1;");
        $n = (int)$db->num_rows($r);
        if ($n === 1) {
            $name = $db->result($r, 0, "name");
        }
        return $name;
    }

    public function showCatalogueBrandSelectListDoc($r)
    {
        $db = DbSingleton::getDb();
        $n = (int)$db->num_rows($r);
        $tkey = time(); $list = "";

        $db->query("CREATE TEMPORARY TABLE IF NOT EXISTS `NBRAND_RESULT_$tkey` (`art_id` INT NOT NULL ,`display_nr` VARCHAR( 100 ) NOT NULL ,`name` VARCHAR( 255 ) NOT NULL ,`brand_id` INT NOT NULL ,`brand_name` VARCHAR( 100 ) NOT NULL ,`kol_res` TINYINT NOT NULL) ENGINE = MYISAM ;");
        for ($i = 1; $i <= $n; $i++) {
            $art_id     = $db->result($r, $i - 1, "ART_ID");
            $display_nr = $db->result($r, $i - 1, "DISPLAY_NR");
            $name       = $db->result($r, $i - 1, "NAME");
            $brand_id   = $db->result($r, $i - 1, "BRAND_ID");
            $brand_name = $db->result($r, $i - 1, "BRAND_NAME");
            $kol_res    = 0;
            $db->query("INSERT INTO `NBRAND_RESULT_$tkey` VALUES ('$art_id','$display_nr','$name','$brand_id','$brand_name','$kol_res');");
        }

        $r = $db->query("SELECT * FROM `NBRAND_RESULT_$tkey` ORDER BY `kol_res` DESC;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $display_nr     = $db->result($r, $i - 1, "display_nr");
            $name           = $db->result($r, $i - 1, "name");
            $brand_id       = $db->result($r, $i - 1, "brand_id");
            $brand_name     = $db->result($r, $i - 1, "brand_name");
            $display_nr2    = str_replace("/", "--", $display_nr);
            $list .= "<tr style='cursor:pointer;' onClick='setArticleSearchBrand(\"$display_nr2\",\"$brand_id\");'>
                <td>$display_nr</td>
                <td>$brand_name</td>
                <td>$name</td>
            </tr>";
        }

        $form = "";
        if ($n > 0) {
            $form_htm = RD . "/tpl/catalogue_brand_select_list.htm";
            if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
            $form = str_replace("{list}", $list, $form);
        }
        $db->query("DROP TEMPORARY TABLE IF EXISTS `NBRAND_RESULT_$tkey`;");

        return $form;
    }

    public function checkPhotoEmpty($art_id)
    {
        $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT COUNT(`ID`) as kol FROM `T2_PHOTOS` WHERE `ART_ID`='$art_id' AND `ACTIVE`=1;");

        return $db->result($r, 0, "kol") + 0;
    }

    public function showIndexAddForm()
    {
        $form = ""; $form_htm = RD . "/tpl/catalogue_index_add_form.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $art_id = $this->getMaxIndex();
        $form = str_replace(array("{art_id}", "{brand_list}"), array($art_id, $this->showBrandsSelect()), $form);

        return $form;
    }

    public function getMaxIndex(): int
    {
        $db = DbSingleton::getTokoDb();

        $r = $db->query("SELECT MAX(`ART_ID`) as max_art FROM `T2_ARTICLES` WHERE `ART_ID` > 100000000 AND `ART_ID` < 110000000;");
        $max = (int)$db->result($r, 0, "max_art") + 1;

        $r = $db->query("SELECT COUNT(`ART_ID`) as count_art FROM `T2_CROSS` WHERE `ART_ID` = $max;");
        $n = $db->result($r, 0, "count_art");
        if ($n > 0) {
            while ($n > 0) {
                $max++;
                $r = $db->query("SELECT COUNT(`ART_ID`) as count_art FROM `T2_CROSS` WHERE `ART_ID` = $max;");
                $n = $db->result($r, 0, "count_art");
            }
        }

        return $max;
    }

    public function getMaxSupplIndex(): int
    {
        $db = DbSingleton::getTokoDb();

        $r = $db->query("SELECT MAX(`ART_ID`) as max_art FROM `T2_ARTICLES` WHERE `ART_ID` > 10000000;");
        $max = (int)$db->result($r, 0, "max_art") + 1;

        $r = $db->query("SELECT COUNT(`ART_ID`) as count_art FROM `T2_CROSS` WHERE `ART_ID` = $max;");
        $n = $db->result($r, 0, "count_art");
        if ($n > 0) {
            while ($n > 0) {
                $max++;
                $r = $db->query("SELECT COUNT(`ART_ID`) as count_art FROM `T2_CROSS` WHERE `ART_ID` = $max;");
                $n = $db->result($r, 0, "count_art");
            }
        }

        return $max;
    }

    public function showBrandsSelect(): string
    {
        $db = DbSingleton::getTokoDb();
        $list = "";
        $r = $db->query("SELECT `BRAND_ID`, `BRAND_NAME` FROM `T2_BRANDS` ORDER BY `BRAND_NAME` ASC;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r, $i - 1, "BRAND_ID");
            $name = $db->result($r, $i - 1, "BRAND_NAME");
            $list .= "<option value=".$id.">" . $name . "</option>";
        }
        return $list;
    }

    public function saveIndexArticle($art_id, $article_nr_displ, $brand_id, $article_name, $article_name_ukr, $article_info): array
    {
        $db = DbSingleton::getTokoDb();
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $answer = 0; $err = "Помилка збереження!";

        $r = $db->query("SELECT `ART_ID`, `ARTICLE_NR_DISPL` FROM `T2_ARTICLES` WHERE `ART_ID` = $art_id;");
        $n = (int)$db->num_rows($r);
        if ($n > 0) {
            $article_nr_displ_sel = $db->result($r, 0, "ARTICLE_NR_DISPL");
            $err = "Такий номер індекса уже існує в системі! - $article_nr_displ_sel";
        }

        $r2 = $db->query("SELECT `ART_ID` FROM `T2_ARTICLES` WHERE `ARTICLE_NR_DISPL` = '$article_nr_displ' AND `BRAND_ID` = $brand_id;");
        $n2 = $db->num_rows($r2);
        if ($n2 > 0) {
            $err = "Індекс з такою назвою та брендом вже існує!";
        }

        $article_nr_search = str_replace(str_split('\\/:*?"<>|+-()[]., '), '', $article_nr_displ);
        if ($art_id > 0 && $art_id !== "" && $n == 0 && $n2 == 0) {
            $db->query("INSERT INTO `T2_ARTICLES` (`ART_ID`, `ARTICLE_NR_DISPL`, `ARTICLE_NR_SEARCH`, `BRAND_ID`) 
            VALUES ($art_id, '$article_nr_displ', '$article_nr_search', $brand_id);");
            $db->query("INSERT INTO `T2_CROSS` (`ART_ID`, `SEARCH_NUMBER`, `KIND`, `BRAND_ID`, `DISPLAY_NR`, `RELATION`) 
            VALUES ($art_id, '$article_nr_search', 0, $brand_id, '$article_nr_displ', 0);");
            $db->query("INSERT INTO `T2_NAMES` (`ART_ID`, `LANG_ID`, `NAME`, `INFO`) VALUES ($art_id, '16', \"$article_name\", \"$article_info\");");
            $db->query("INSERT INTO `T2_NAMES` (`ART_ID`, `LANG_ID`, `NAME`, `INFO`) VALUES ($art_id, '41', \"$article_name_ukr\", \"$article_info\");");
            $db->query("INSERT INTO `T2_ARTICLES_LOGS` (`user_id`,`art_id`,`article_nr_displ`,`brand_id`,`article_name_ru`,`article_name_ua`) 
            VALUES ('$user_id', '$art_id', '$article_nr_displ', '$brand_id', \"$article_name\", \"$article_name_ukr\");");
            $answer = 1; $err = "";
        }

        return array($answer, $err);
    }

    public function loadArticleCatalogue($art_id)
    {
        $form = ""; $form_htm = RD . "/tpl/catalogue_article_cat.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $form = str_replace(array("{art_id}", "{params_list}", "{catalogue_template_list}"), array($art_id, $this->loadTemplateList($art_id, 0), $this->getTemplateList(0, $art_id)), $form);

        return $form;
    }

    public function getTemplateList($sel_id = 0, $art_id = 0): string
    {
        $db = DbSingleton::getTokoDb();
        $list = ""; $sel_template = "";
        $r = $db->query("SELECT `TEMPLATE_ID`, `TEMPLATE_NAME` FROM `T2_CATALOGUES_TEMPLATES` WHERE 1;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $template_id = $db->result($r, $i - 1, "TEMPLATE_ID");
            $template_name = $db->result($r, $i - 1, "TEMPLATE_NAME");
            $sel = ($template_id == $sel_id) ? "selected" : "";
            if ($art_id > 0) {
                $rs = $db->query("SELECT * FROM `T2_CATALOGUES_ARTS` WHERE `ART_ID` = $art_id AND `TEMPLATE_ID` = $template_id GROUP BY `TEMPLATE_ID`;");
                $ns = $db->num_rows($rs);
                $sel_template = ($ns > 0) ? "(*)" : "";
            }
            $list .= "<option value='$template_id' $sel>$template_name $sel_template</option>";
        }
        return $list;
    }

    public function getParamsList($template_id, $sel_id = 0): string
    {
        $db = DbSingleton::getTokoDb();
        $list = "";
        if ($template_id > 0) {
            $r = $db->query("SELECT `PARAM_ID`, `PARAM_NAME` FROM `T2_CATALOGUES_PARAMS` WHERE `TEMPLATE_ID` = $template_id;");
            $n = (int)$db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $param_id = $db->result($r, $i - 1, "PARAM_ID");
                $param_name = $db->result($r, $i - 1, "PARAM_NAME");
                $sel = ($param_id == $sel_id) ? "selected" : "";
                $list .= "<option value='$param_id' $sel>$param_name</option>";
            }
        }
        return $list;
    }

    public function getValuesList($template_id, $param_id, $sel_id = 0): string
    {
        $db = DbSingleton::getTokoDb();
        $list = "";
        if ($template_id > 0 && $param_id > 0) {
            $r = $db->query("SELECT `VALUE_ID`, `PARAM_VALUE` FROM `T2_CATALOGUES_VALUES` WHERE `TEMPLATE_ID` = $template_id AND `PARAM_ID` = $param_id;");
            $n = (int)$db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $value_id = $db->result($r, $i - 1, "VALUE_ID");
                $param_value = $db->result($r, $i - 1, "PARAM_VALUE");
                $sel = ($value_id == $sel_id) ? "selected" : "";
                $list .= "<option value='$value_id' $sel>$param_value</option>";
            }
        }
        return $list;
    }

    public function loadTemplateList($art_id, $template_id): string
    {
        $db = DbSingleton::getTokoDb();
        $list = "";
        $where = ($template_id == 0) ? "" : " AND ca.TEMPLATE_ID='$template_id'";

        $r = $db->query("SELECT ca.ID, cp.PARAM_ID, ca.VALUE_ID, cp.PARAM_NAME 
        FROM `T2_CATALOGUES_PARAMS` cp
            LEFT OUTER JOIN `T2_CATALOGUES_ARTS` ca ON (ca.PARAM_ID=cp.PARAM_ID)
        WHERE ca.ART_ID = $art_id $where;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id         = $db->result($r, $i - 1, "ID");
            $param_id   = $db->result($r, $i - 1, "PARAM_ID");
            $value_id   = $db->result($r, $i - 1, "VALUE_ID");
            $param_name = $db->result($r, $i - 1, "PARAM_NAME");

            if ($id > 0) {
                $param_value = $this->loadParamSelectList($param_id, $value_id, $id);
                $btns = "<button class='btn btn-xs btn-primary' onclick='saveCatalogueTemplateParamsValue(\"$id\")' title='Зберегти значення параметра'><i class='fa fa-save'></i></button>";
            } else {
                $param_value = "<select title='Пусто' class='form-control' disabled><option>-Не вибрано-</option></select>";
                $btns = "<button class='btn btn-xs btn-info' onclick='showCatalogueParamValueForm(\"$param_id\")' title='Додати нове значення параметра'><i class='fa fa-plus-square'></i></button>";
            }

            $btns1 = "<button class='btn btn-xs btn-warning' onclick='showCatalogueTemplateParamsForm(\"$param_id\")' title='Редагувати параметр'><i class='fa fa-pencil'></i></button>";
            $btns2 = "
                <button class='btn btn-xs btn-success' onclick='showCatalogueTemplateParamsValueForm(\"$template_id\", \"$param_id\")' title='Додати значення параметра'><i class='fa fa-plus'></i></button>
                $btns
                <button class='btn btn-xs btn-danger' onclick='dropCatalogueTemplateParamsValue(\"$id\")' title='Видалити значення параметра'><i class='fa fa-trash'></i></button>";
            $list .= "
            <tr>
                <td>$i</td>
                <td>$btns1 $param_name</td>
                <td>$param_value</td>
                <td>$btns2</td>
            </tr>";
        }

        return $list;
    }

    public function loadParamSelectList($param_id, $sel_id, $id): string
    {
        $db = DbSingleton::getTokoDb();
        $list = "<select title=\"Параметр\" class=\"form-control\" id=\"catalogue_params_id_$id\"><option value='0'></option>";

        $r = $db->query("SELECT `VALUE_ID`, `PARAM_VALUE` FROM `T2_CATALOGUES_VALUES` WHERE `PARAM_ID` = $param_id;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $value_id = $db->result($r, $i - 1, "VALUE_ID");
            $param_value = $db->result($r, $i - 1, "PARAM_VALUE");
            $sel = ($value_id == $sel_id) ? "selected" : "";
            $list .= "<option value='$value_id' $sel>$param_value</option>";
        }
        $list .= "</select>";

        return $list;
    }

    // TEMPLATE
    public function showCatalogueTemplateForm($template_id)
    {
        $db = DbSingleton::getTokoDb();
        $form = ""; $form_htm = RD . "/tpl/catalogue_article_cat_template.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        if ($template_id > 0) {
            $r = $db->query("SELECT `TEMPLATE_NAME`, `CHILD_STATUS`, `PARENT_ID`, `STATUS` FROM `T2_CATALOGUES_TEMPLATES` WHERE `TEMPLATE_ID` = $template_id LIMIT 1;");
            $template_name  = $db->result($r, 0, "TEMPLATE_NAME");
            $child_status   = $db->result($r, 0, "CHILD_STATUS");
            $parent_id      = $db->result($r, 0, "PARENT_ID");
            $status         = $db->result($r, 0, "STATUS");
            $disable_delete = "";
        } else {
            $template_name  = "";
            $child_status   = 0;
            $parent_id      = 0;
            $status         = 0;
            $disable_delete = "style='display:none;'";
        }

        $form = str_replace(array("{template_id}", "{template_name}", "{child_status}", "{parent_template_list}", "{template_status}", "{disable_delete}"), array($template_id, $template_name, ($child_status) ? "checked" : "", $this->getTemplateList($parent_id), ($status) ? "checked" : "", $disable_delete), $form);

        return $form;
    }

    public function saveCatalogueTemplateForm($template_id, $template_name, $child_status, $parent_id, $template_status): array
    {
        $db = DbSingleton::getTokoDb();

        if ($template_id > 0) {
            $db->query("UPDATE `T2_CATALOGUES_TEMPLATES` SET `TEMPLATE_NAME` = '$template_name', `CHILD_STATUS` = '$child_status', `PARENT_ID` = '$parent_id', `STATUS` = '$template_status' 
            WHERE `TEMPLATE_ID` = $template_id;");
            $answer = 1; $err = "Успішно збережено";
        } else {
            $r = $db->query("SELECT MAX(`TEMPLATE_ID`) as mid FROM `T2_CATALOGUES_TEMPLATES`;");
            $template_id = $db->result($r, 0, "mid") + 1;
            $db->query("INSERT INTO `T2_CATALOGUES_TEMPLATES` (`TEMPLATE_ID`, `TEMPLATE_NAME`, `CHILD_STATUS`, `PARENT_ID`, `STATUS`) 
            VALUES ('$template_id', '$template_name', '$child_status', '$parent_id', '$template_status');");
            $answer = 1; $err = "Успішно додано";
        }

        return array($answer, $err);
    }

    public function dropCatalogueTemplate($template_id): array
    {
        $db = DbSingleton::getTokoDb();
        $answer = 0; $err = "Помилка видалення!";

        if ($template_id > 0) {
            $db->query("DELETE FROM `T2_CATALOGUES_TEMPLATES` WHERE `TEMPLATE_ID` = $template_id;");
            $answer = 1; $err = "";
        }

        return array($answer, $err);
    }

    public function dropCatalogueTemplateArticle($template_id, $art_id): array
    {
        $db = DbSingleton::getTokoDb();
        $answer = 0; $err = "Помилка видалення!";

        if ($template_id > 0) {
            $db->query("DELETE FROM `T2_CATALOGUES_ARTS` WHERE `TEMPLATE_ID` = $template_id AND `ART_ID` = $art_id;");
            $answer = 1; $err = "";
        }

        return array($answer, $err);
    }

    // PARAMS
    public function showCatalogueTemplateParamsForm($param_id, $template_id)
    {
        $db = DbSingleton::getTokoDb();
        $form = ""; $form_htm = RD . "/tpl/catalogue_article_cat_params.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }

        if ($param_id > 0) {
            $r = $db->query("SELECT `TEMPLATE_ID`, `PARAM_NAME` FROM `T2_CATALOGUES_PARAMS` WHERE `PARAM_ID` = $param_id LIMIT 1;");
            $template_id = $db->result($r, 0, "TEMPLATE_ID");
            $param_name = $db->result($r, 0, "PARAM_NAME");
            $template_list = $this->getTemplateList($template_id);
            $disable_delete = "";
        } else {
            $template_list = $this->getTemplateList($template_id);
            $param_name = "";
            $disable_delete = "style='display:none;'";
        }

        $form = str_replace(array("{param_id}", "{template_list}", "{param_name}", "{disable_delete}"), array($param_id, $template_list, $param_name, $disable_delete), $form);

        return $form;
    }

    public function saveCatalogueTemplateParamsForm($param_id, $template_id, $param_name): array
    {
        $db = DbSingleton::getTokoDb();
        $answer = 0; $err = "Помилка збереження!";

        if ($param_id > 0) {
            $r = $db->query("SELECT * FROM `T2_CATALOGUES_PARAMS` WHERE `PARAM_ID` = $param_id LIMIT 1;");
            $n = (int)$db->num_rows($r);
            if ($n > 0) {
                $db->query("UPDATE `T2_CATALOGUES_PARAMS` SET `TEMPLATE_ID`='$template_id', `PARAM_NAME`='$param_name' WHERE `PARAM_ID` = $param_id;");
            } else {
                $db->query("INSERT INTO `T2_CATALOGUES_PARAMS` (`PARAM_ID`, `TEMPLATE_ID`, `PARAM_NAME`) VALUES ('$param_id','$template_id','$param_name');");
            }
            $answer = 1; $err = "";
        }

        if ((int)$param_id === 0) {
            $r = $db->query("SELECT MAX(`PARAM_ID`) as mid FROM `T2_CATALOGUES_PARAMS`;");
            $param_id = $db->result($r, 0, "mid") + 1;
            $db->query("INSERT INTO `T2_CATALOGUES_PARAMS` (`PARAM_ID`, `TEMPLATE_ID`, `PARAM_NAME`) VALUES ('$param_id','$template_id','$param_name');");
            $answer = 1; $err = "";
        }

        return array($answer, $err);
    }

    public function dropCatalogueTemplateParams($param_id): array
    {
        $db = DbSingleton::getTokoDb();
        $answer = 0; $err = "Помилка збереження!";

        if ($param_id > 0) {
            $db->query("DELETE FROM `T2_CATALOGUES_PARAMS` WHERE `PARAM_ID` = $param_id;");
            $answer = 1; $err = "";
        }

        return array($answer, $err);
    }

    // VALUE
    public function showCatalogueTemplateParamsValueForm($template_id)
    {
        $db = DbSingleton::getTokoDb();
        $form = ""; $form_htm = RD . "/tpl/catalogue_article_cat_value.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }

        $r = $db->query("SELECT MAX(`VALUE_ID`) as mid FROM `T2_CATALOGUES_VALUES`;");
        $value_id = $db->result($r, 0, "mid") + 1;
        $template_list = $this->getTemplateList($template_id);
        $params_list = $this->getParamsList($template_id);

        $form = str_replace(array("{value_id}", "{template_list}", "{params_list}", "{param_value}"), array($value_id, $template_list, $params_list, ""), $form);

        return $form;
    }

    public function saveCatalogueTemplateParamsValue($id, $value_id): array
    {
        $db = DbSingleton::getTokoDb();
        $answer = 0; $err = "Помилка збереження!";

        if ($id > 0 && $value_id > 0) {
            $db->query("UPDATE `T2_CATALOGUES_ARTS` SET `VALUE_ID` = '$value_id' WHERE `ID` = '$id';");
            $answer = 1; $err = "";
        }

        return array($answer, $err);
    }

    public function saveCatalogueTemplateParamsValueForm($template_id, $param_id, $param_value): array
    {
        $db = DbSingleton::getTokoDb();
        $answer = 0; $err = "Помилка збереження!";

        if ($template_id > 0 && $param_id > 0) {
            $db->query("INSERT INTO `T2_CATALOGUES_VALUES` (`TEMPLATE_ID`, `PARAM_ID`, `PARAM_VALUE`) VALUES ('$template_id', '$param_id', '$param_value');");
            $answer = 1; $err = "";
        }

        return array($answer, $err);
    }

    public function dropCatalogueTemplateParamsValue($id): array
    {
        $db = DbSingleton::getTokoDb();
        $answer = 0; $err = "Помилка збереження!";

        if ($id > 0) {
            $db->query("DELETE FROM `T2_CATALOGUES_ARTS` WHERE `ID` = '$id';");
            $answer = 1; $err = "";
        }

        return array($answer, $err);
    }

    public function showCatalogueParamValueForm($template_id, $param_id = 0)
    {
        $db = DbSingleton::getTokoDb();
        $form = ""; $form_htm = RD . "/tpl/catalogue_article_cat_params_value.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }

        $r = $db->query("SELECT MAX(`ID`) as mid FROM `T2_CATALOGUES_ARTS`;");
        $id = $db->result($r, 0, "mid") + 1;
        $template_list  = $this->getTemplateList($template_id);
        $params_list    = $this->getParamsList($template_id, $param_id);
        $value_list     = $this->getValuesList($template_id, $param_id);

        $form = str_replace(array("{new_id}", "{value_id}", "{template_list}", "{params_list}", "{value_list}"), array($id, 0, $template_list, $params_list, $value_list), $form);

        return $form;
    }

    public function saveCatalogueParamValueForm($art_id, $template_id, $param_id, $value_id): array
    {
        $db = DbSingleton::getTokoDb();
        $answer = 0; $err = "Помилка збереження!";

        if ($template_id > 0 && $param_id > 0) {
            $db->query("INSERT INTO `T2_CATALOGUES_ARTS` (`ART_ID`, `TEMPLATE_ID`, `PARAM_ID`, `VALUE_ID`) 
            VALUES ('$art_id','$template_id','$param_id','$value_id');");
            $answer = 1; $err = "";
        }

        return array($answer, $err);
    }

    // NON PRICED PAGE
    public function showNonPricedGoods(): string
    {
        $db = DbSingleton::getTokoDb();
        $list = ""; $i = 0;

        $r = $db->query("SELECT t2p.art_id 
        FROM `T2_ARTICLES_PRICE_RATING` t2p
		    LEFT OUTER JOIN `T2_ARTICLES_STOCK` t2s ON (t2s.ART_ID = t2p.ART_ID)
		WHERE t2s.amount > 0 and t2p.price_1 = 0 
		GROUP BY t2p.art_id;");
        $n = (int)$db->num_rows($r);

        if ($n > 0) {
            for ($i = 1; $i <= $n; $i++) {
                $art_id = $db->result($r, $i - 1, "art_id");
                [$article_nr_displ, , $brand_name,] = $this->getArticleNrDisplBrand($art_id);
                $list .= "
                <tr>
					<td>$i</td>
					<td>$art_id</td>
					<td>$article_nr_displ</td>
					<td>$brand_name</td>
				</tr>";
            }
        }

        $maxi = $i;
        $r = $db->query("SELECT `art_id` FROM `T2_SUPPL_IMPORT` WHERE `price_suppl` = 0 AND `stock_suppl` > 0 AND `art_id` != 0 GROUP BY `art_id`;");
        $n = (int)$db->num_rows($r);
        if ($n > 0) {
            for ($i = 1; $i <= $n; $i++) {
                $maxi++;
                $art_id = $db->result($r, $i - 1, "art_id");
                [$article_nr_displ, , $brand_name,] = $this->getArticleNrDisplBrand($art_id);
                $list .= "
                <tr>
					<td>$maxi</td>
					<td>$art_id</td>
					<td>$article_nr_displ</td>
					<td>$brand_name</td>
				</tr>";
            }
        }

        return $list;
    }

    // REPORT SALES PAGE
    public function showReportSales($date, $tpoint): string
    {
        $db = DbSingleton::getTokoDb();
        $where_tpoint = ($tpoint === "0") ? "" : "AND `TPOINT_ID`=$tpoint";
        $where_date = ($date === "0") ? "" : "AND `MONTH`='$date"."-00'";
        $list = "";

        $r = $db->query("SELECT `ART_ID`, `TPOINT_ID`, `AMOUNT` FROM `T2_ARTICLES_SALES` WHERE `art_id` > 0 $where_tpoint $where_date;");
        $n = (int)$db->num_rows($r);
        if ($n > 0) {
            for ($i = 1; $i <= $n; $i++) {
                $art_id = $db->result($r, $i - 1, "ART_ID");
                [$article, $brand_id] = $this->getArticle($art_id);
                $brand          = $this->getBrandName($brand_id);
                $tpoint_id      = $db->result($r, $i - 1, "TPOINT_ID");
                $tpoint_name    = $this->getTpointNameById($tpoint_id);
                $amount         = $db->result($r, $i - 1, "AMOUNT");
                $list .= "
                <tr>
					<td>$i</td>
					<td>$article</td>
					<td>$brand</td>
					<td>$art_id</td>
					<td>$tpoint_name</td>
					<td>$amount</td>
				</tr>";
            }
        }

        return $list;
    }

    public function getTpointNameById($sel_id, $field = "name")
    {
        $db = DbSingleton::getDb();
        $name = "";
        $r = $db->query("SELECT `$field` FROM `T_POINT` WHERE `id` = $sel_id LIMIT 1;");
        $n = (int)$db->num_rows($r);
        if ($n === 1) {
            $name = $db->result($r, 0, "$field");
        }
        return $name;
    }

    public function getArticle($art_id): array
    {
        $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT `ARTICLE_NR_DISPL`, `BRAND_ID` FROM `T2_ARTICLES` WHERE `ART_ID` = $art_id LIMIT 1;");
        $article    = $db->result($r, 0, "ARTICLE_NR_DISPL");
        $brand_id   = $db->result($r, 0, "BRAND_ID");
        return array($article, $brand_id);
    }

    public function getTpointList(): string
    {
        $db = DbSingleton::getDb();
        $list = "<option value='0'>Всі торгові точки</option>";
        $r = $db->query("SELECT `id`, `name`, `full_name` FROM `T_POINT` WHERE `status` = 1 ORDER BY `id` ASC;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r, $i - 1, "id");
            $name = $db->result($r, $i - 1, "name");
            $caption = $db->result($r, $i - 1, "full_name");
            $list .= "<option value='$id'>$caption ($name)</option>";
        }
        return $list;
    }

    /*
     * IMPORT INDEX
     * */
    public function showImportIndexForm()
    {
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $form = ""; $form_htm = RD . "/tpl/import_index/form.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $form = str_replace("{import_content}", $this->loadImportIndex($user_id), $form);
        return $form;
    }

    public function loadImportIndex($user_id)
    {
        $form = ""; $form_htm = RD . "/tpl/import_index/upload.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $header = ""; $header_htm = RD . "/tpl/import_index/header.htm";
        if (file_exists($header_htm)) { $header = file_get_contents($header_htm); }
        [, $csv_file_name, $pre_table] = $this->showCsvPreview($user_id);
        $table = $this->showTablePreview($user_id);

        $form = str_replace("{ibox_header}", ($table === "") ? $header : "", $form);
        $form = str_replace("{records_list}", "<tr><td colspan=10 align='center'>Записи не завантажено</td></tr>", $form);
        $form = str_replace("{import_file_name}", $csv_file_name, $form);
        $form = str_replace("{user_id}", $user_id, $form);
        $form = str_replace("{csv_str_file}", ($table === "") ? $pre_table : "", $form);
        $form = str_replace("{table_str_file}", $table, $form);

        return $form;
    }

    public function showCsvPreview($user_id)
    {
        $db = DbSingleton::getTokoDb();
        $csv_exist = 0;
        $csv_file_name = "Оберіть файл";
        $pre_table = "<h3>Файл не знайдено</h3>";
        $kol_cols = $fn = 0;
        $r = $db->query("SELECT `file_name` FROM `T2_ARTICLES_CSV` WHERE `user_id` = '$user_id' LIMIT 1;");
        $n = (int)$db->num_rows($r);
        if ($n === 1) {
            $file_name = $db->result($r, 0, "file_name");
            $file_path = RD . "/cdn/articles_files/csv/$user_id/$file_name";
            if (file_exists($file_path)) {
                $form = ""; $form_htm = RD . "/tpl/import_index/table.htm";
                if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
                $cols_list = ""; $records_list = "";
                $handle = @fopen($file_path, "r");
                if ($handle) {
                    set_time_limit(0);
                    $max_cols = 0;
                    while (($buffer = fgets($handle, 4096)) !== false) {
                        ++$fn;
                        $buf = explode(";", $buffer);
                        if ($buffer !== "") {
                            if ($fn === 1) {
                                $kol_cols = count($buf);
                            }
                            $buf = str_replace("'", "\'", $buf);
                            $buf = str_replace('"', '\"', $buf);
                            $row = ""; $ex_cols = 0;
                            if ($max_cols < $kol_cols) {
                                $ex_cols = 1; $cols_list = "";
                            }
                            for ($i = 1; $i <= $kol_cols; $i++) {
                                if ($i == 1) {
                                    $row = "<td>$fn</td>";
                                }
                                $row .= "<td>" . trim($buf[$i-1]) . "</td>";
                                if ($ex_cols == 1) {
                                    $sel1 = $sel2 = $sel3 = $sel4 = $sel5 = $sel6 = $sel7 = $sel8 = "";
                                    if ($i == 1) $sel1 = "selected";
                                    if ($i == 2) $sel2 = "selected";
                                    if ($i == 3) $sel3 = "selected";
                                    if ($i == 4) $sel4 = "selected";
                                    if ($i == 5) $sel5 = "selected";
                                    if ($i == 6) $sel6 = "selected";
                                    if ($i == 7) $sel7 = "selected";
                                    if ($i == 8) $sel8 = "selected";
                                    $cols_list .= "<th><select id=\"clm-$i\" size='1'>
                                        <option value='0'>-</option>
                                        <option value='1' $sel1>SUPPL_ART</option>
                                        <option value='2' $sel2>ARTICLE_NR_DISPL</option>
                                        <option value='3' $sel3>BRAND_ID</option>
                                        <option value='4' $sel4>NAME_RU</option>
                                        <option value='5' $sel5>NAME_UA</option>
                                        <option value='6' $sel6>NAME_EN</option>
                                        <option value='7' $sel7>INFO</option>
                                        <option value='8' $sel8>UKTZED</option>
                                    </select></th>";
                                }
                            }
                            if ($row !== "") {
                                $records_list .= "<tr>$row</tr>";
                            }
                        }
                        if ($fn === 30) {
                            break;
                        }
                    }
                    fclose($handle);
                }

                $form = str_replace(array("{user_id}", "{cols_list}", "{kol_cols}", "{records_list}", "{records_list2}"), array($user_id, $cols_list, $kol_cols, $records_list, $this->getImportIndexUnknownList($user_id)["list"]), $form);

                $csv_file_name = $file_name;
                $csv_exist = 1;
                $pre_table = $form;
            }
        }

        return array($csv_exist, $csv_file_name, $pre_table);
    }

    public function getImportIndexUnknownList($user_id): array
    {
        $db = DbSingleton::getTokoDb();
        $list = "";
        $r = $db->query("SELECT `art_id` FROM `T2_ARTICLES_UNKNOWN` WHERE `user_id` = '$user_id';");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $art_id = $db->result($r, $i - 1, "art_id");
            [$article_nr_displ, $brand_id, $name_ru, $name_ua, $name_en, $info, $uktzed] = $this->getArticleExistInfo($art_id);
            $brand_name = $this->getBrandName($brand_id);
            $list .= "
            <tr>
                <td>$i</td>
                <td>$art_id</td>
                <td>$article_nr_displ</td>
                <td>$brand_name ($brand_id)</td>
                <td>$name_ru</td>
                <td>$name_ua</td>
                <td>$name_en</td>
                <td>$info</td>
                <td>$uktzed</td>
            </tr>";
        }

        return array("list" => $list, "count" => $n);
    }

    public function getArticleExistInfo($art_id): array
    {
        $db = DbSingleton::getTokoDb();
        $name_ru = $name_ua = $name_en = $info = ""; $uktzed = 0;
        $r = $db->query("SELECT `ARTICLE_NR_DISPL`, `BRAND_ID` FROM `T2_ARTICLES` WHERE `ART_ID` = $art_id LIMIT 1;");
        $article_nr_displ = $db->result($r, 0, "ARTICLE_NR_DISPL");
        $brand_id = $db->result($r, 0, "BRAND_ID");
        $r = $db->query("SELECT `LANG_ID`, `INFO`, `NAME` FROM `T2_NAMES` WHERE `ART_ID` = $art_id;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $lang_id = (int)$db->result($r, $i - 1, "LANG_ID");
            if ($info === "") {
                $info = $db->result($r, $i - 1, "INFO");
            }
            if ($lang_id === 16) {
                $name_ru = $db->result($r, $i - 1, "NAME");
            }
            if ($lang_id === 41) {
                $name_ua = $db->result($r, $i - 1, "NAME");
            }
            if ($lang_id === 4) {
                $name_en = $db->result($r, $i - 1, "NAME");
            }
        }

        $r = $db->query("SELECT `COSTUMS_ID` FROM `T2_ZED` WHERE `ART_ID` = $art_id LIMIT 1;");
        $n = (int)$db->num_rows($r);
        if ($n > 0) {
            $costums_id = $db->result($r, 0, "COSTUMS_ID");
            $rs = $db->query("SELECT `COSTUMS_CODE` FROM `T2_COSTUMS` WHERE `COSTUMS_ID`='$costums_id' LIMIT 1;");
            $uktzed = $db->result($rs, 0, "COSTUMS_CODE");
        }

        return array($article_nr_displ, $brand_id, $name_ru, $name_ua, $name_en, $info, $uktzed);
    }

    public function saveCsvImportIndex($user_id, $start_row, $kol_cols, $cols)
    {
        $db = DbSingleton::getTokoDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка збереження даних!";
        $fn = 0;
        $user_id = $slave->qq($user_id);
        $start_row = $slave->qq($start_row);
        $kol_cols = $slave->qq($kol_cols);
        $cols = $slave->qq($cols);
        $db->query("DELETE FROM `T2_ARTICLES_IMPORT` WHERE `user_id` = '$user_id';");
        if ($user_id > 0) {
            $r = $db->query("SELECT `file_name` FROM `T2_ARTICLES_CSV` WHERE `user_id` = '$user_id' LIMIT 1;");
            $n = (int)$db->num_rows($r);
            if ($n === 1) {
                $file_name = $db->result($r, 0, "file_name");
                $file_path = RD . "/cdn/articles_files/csv/$user_id/$file_name";
                if (file_exists($file_path)) {
                    $suppl_art = 0;
                    $article_nr_displ = "";
                    $brand_id = 0;
                    $name_ru = "";
                    $name_ua = "";
                    $name_en = "";
                    $info = "";
                    $uktzed = "";
                    for ($i = 1; $i <= $kol_cols; $i++) {
                        if ($cols[$i] == 1) { $suppl_art = $i; }
                        if ($cols[$i] == 2) { $article_nr_displ = $i; }
                        if ($cols[$i] == 3) { $brand_id = $i; }
                        if ($cols[$i] == 4) { $name_ru = $i; }
                        if ($cols[$i] == 5) { $name_ua = $i; }
                        if ($cols[$i] == 6) { $name_en = $i; }
                        if ($cols[$i] == 7) { $info = $i; }
                        if ($cols[$i] == 8) { $uktzed = $i; }
                    }
                    $handle = @fopen($file_path, "r");
                    if ($handle) {
                        set_time_limit(0);
                        while (($buffer = fgets($handle, 4096)) !== false) {
                            ++$fn;
                            $buf = explode(";", $buffer);
                            if (($buffer !== "") && $fn >= $start_row) {
                                $buf = str_replace("'", "\'", $buf);
                                $buf = str_replace('"', '\"', $buf);

                                $suppl_art2 = trim($buf[$suppl_art - 1]);
                                $article_nr_displ2 = trim($buf[$article_nr_displ - 1]);
                                $brand_id2 = trim($buf[$brand_id - 1]);
                                $name_ru2 = trim($buf[$name_ru - 1]);
                                $name_ua2 = trim($buf[$name_ua - 1]);
                                $name_en2 = trim($buf[$name_en - 1]);
                                $info2 = trim($buf[$info - 1]);
                                $uktzed2 = trim($buf[$uktzed - 1]);

                                if ($this->checkArticlesSearch($article_nr_displ2, $brand_id2)) {
                                    $r2 = $db->query("SELECT MAX(`id`) as mid FROM `T2_ARTICLES_UNKNOWN`;");
                                    $unknown_id = 0 + $db->result($r2, 0, "mid") + 1;
                                    $art_id = $this->getArticlesSearch($article_nr_displ2, $brand_id2);
                                    $db->query("INSERT INTO `T2_ARTICLES_UNKNOWN` (`id`, `user_id`, `art_id`, `suppl_art`, `article_nr_displ`, `brand_id`, `name_ru`, `name_ua`, `name_en`, `info`, `uktzed`, `status`) 
                                    VALUES ('$unknown_id', '$user_id', '$art_id', '$suppl_art2', '$article_nr_displ2', '$brand_id2', '$name_ru2', '$name_ua2', '$name_en2', '$info2', '$uktzed2', '1');");
                                } else {
                                    $r3 = $db->query("SELECT * FROM `T2_ARTICLES_IMPORT` WHERE `user_id`='$user_id' AND `article_nr_displ`='$article_nr_displ2' AND `brand_id`='$brand_id2' LIMIT 1;");
                                    $n3 = (int)$db->num_rows($r3);
                                    if ($n3 === 0) {
                                        $r2 = $db->query("SELECT MAX(`id`) as mid FROM `T2_ARTICLES_IMPORT`;");
                                        $import_id = 0 + $db->result($r2, 0, "mid") + 1;
                                        $db->query("INSERT INTO `T2_ARTICLES_IMPORT` (`id`, `user_id`, `suppl_art`, `article_nr_displ`, `brand_id`, `name_ru`, `name_ua`, `name_en`, `info`, `uktzed`, `status`) 
                                        VALUES ('$import_id', '$user_id', '$suppl_art2', '$article_nr_displ2', '$brand_id2', '$name_ru2', '$name_ua2', '$name_en2', '$info2', '$uktzed2', '1');");
                                    }
                                }
                            }
                        }

                        fclose($handle);
                        if (file_exists(RD . "/cdn/articles_files/csv/$user_id/$file_name")) {
                            unlink(RD . "/cdn/articles_files/csv/$user_id/$file_name");
                        }
                        $db->query("DELETE FROM `T2_ARTICLES_CSV` WHERE `user_id`='$user_id';");
                        $answer = 1; $err = "";
                    }
                }
            }
        }

        return array($answer, $err);
    }

    public function showTablePreview($user_id)
    {
        $form = ""; $form_htm = RD . "/tpl/import_index/preload.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm);}
        $form = str_replace("{user_id}", $user_id, $form);
        $tableData = $this->loadTablePreview($user_id);
        $tableUnknownData = $this->getImportIndexUnknownList($user_id);

        $form = str_replace(array("{records_list}", "{records_list_count}", "{records_list2}", "{records_list2_count}"), array($tableData["list"], $tableData["count"], $tableUnknownData["list"], $tableUnknownData["count"]), $form);

        if ($tableData["list"] === "" && $tableUnknownData["list"] === "") {
            $form = "";
        }
        return $form;
    }

    public function loadTablePreview($user_id): array
    {
        $db = DbSingleton::getTokoDb();
        $table = "";
        $r = $db->query("SELECT * FROM `T2_ARTICLES_IMPORT` WHERE `user_id`='$user_id' ORDER BY `status` ASC, `id` ASC;");
        $n = (int)$db->num_rows($r);
        $mas = [];
        for ($i = 1; $i <= $n; $i++) {
            $suppl_art = $db->result($r, $i - 1, "suppl_art");
            $article_nr_displ = $db->result($r, $i - 1, "article_nr_displ");
            $brand_id = $db->result($r, $i - 1, "brand_id");
            $name_ru = $db->result($r, $i - 1, "name_ru");
            $name_ua = $db->result($r, $i - 1, "name_ua");
            $name_en = $db->result($r, $i - 1, "name_en");
            $info = $db->result($r, $i - 1, "info");
            $uktzed = $db->result($r, $i - 1, "uktzed");
            $status = $db->result($r, $i - 1, "status");
            $mas[$i] = compact("suppl_art", "article_nr_displ", "brand_id", "name_ru", "name_ua", "name_en", "info", "uktzed", "status");
        }

        $i = 0; $art_id_max = 0; $art_id_suppl_max = 0;
        foreach ($mas as $val) {
            $i++;
            $suppl_art = $val["suppl_art"];
            $article_nr_displ = $val["article_nr_displ"];
            $brand_id = $val["brand_id"];
            $name_ru = $val["name_ru"];
            $name_ua = $val["name_ua"];
            $name_en = $val["name_en"];
            $info = $val["info"];
            $uktzed = $val["uktzed"];
            [$art_id_cur, $art_id_max, $art_id_suppl_max] = $this->getArtIdMaxList($suppl_art, $art_id_max, $art_id_suppl_max);
            $brand_name = $this->getBrandName($brand_id);
            $table .= "<tr>
                <td>$i</td>
                <td>$art_id_cur</td>
                <td>$suppl_art</td>
                <td>$article_nr_displ</td>
                <td>$brand_name ($brand_id)</td>
                <td>$name_ru</td>
                <td>$name_ua</td>
                <td>$name_en</td>
                <td>$info</td>
                <td>$uktzed</td>
            </tr>";
        }

        $count = count($mas);

        return array("list" => $table, "count" => $count);
    }

    public function getArtIdMaxList($suppl_art, $art_id_max, $art_id_suppl_max): array
    {
        if ($suppl_art == 0) {
            if ($art_id_max == 0) {
                $art_id_max = $this->getMaxIndex();
            } else {
                $art_id_max++;
            }
            $art_id_cur = $art_id_max;
        } else {
            if ($art_id_suppl_max == 0) {
                $art_id_suppl_max = $this->getMaxSupplIndex();
            } else {
                $art_id_suppl_max++;
            }
            $art_id_cur = $art_id_suppl_max;
        }

        return array($art_id_cur, $art_id_max, $art_id_suppl_max);
    }

    public function finishImportIndex($user_id): array
    {
        $db = DbSingleton::getTokoDb();
        $slave = new slave;
        $answer = 0;
        $err = "Помилка збереження даних!";
        $user_id = $slave->qq($user_id);

        if ($user_id > 0) {
            $db->query("DELETE FROM `T2_ARTICLES_UNKNOWN` WHERE `user_id` = '$user_id';");
            $r = $db->query("SELECT * FROM `T2_ARTICLES_IMPORT` WHERE `user_id` = '$user_id';");
            $n = (int)$db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $id = $db->result($r, $i - 1, "id");
                $suppl_art = $db->result($r, $i - 1, "suppl_art");
                $article_nr_displ = $db->result($r, $i - 1, "article_nr_displ");
                $brand_id = $db->result($r, $i - 1, "brand_id");
                $name_ru = $db->result($r, $i - 1, "name_ru");
                $name_ua = $db->result($r, $i - 1, "name_ua");
                $name_en = $db->result($r, $i - 1, "name_en");
                $info = $db->result($r, $i - 1, "info");
                $costums_code = $db->result($r, $i - 1, "uktzed");
                $name_ru = str_replace("'", '"', $name_ru);
                $name_ua = str_replace("'", '"', $name_ua);
                $name_en = str_replace("'", '"', $name_en);
                $info = str_replace("'", '"', $info);
                $this->addArticle($suppl_art, $article_nr_displ, $brand_id, $name_ru, $name_ua, $name_en, $info, $costums_code);
                $db->query("DELETE FROM `T2_ARTICLES_IMPORT` WHERE `id` = $id;");
            }
            $answer = 1; $err = "";
        }

        return array($answer, $err);
    }

    public function clearImportIndex($user_id): array
    {
        $db = DbSingleton::getTokoDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка збереження даних!";
        $user_id = $slave->qq($user_id);
        if ($user_id > 0) {
            $db->query("DELETE FROM `T2_ARTICLES_IMPORT` WHERE `user_id` = $user_id;");
            $db->query("DELETE FROM `T2_ARTICLES_UNKNOWN` WHERE `user_id` = $user_id;");
            $answer = 1; $err = "";
        }

        return array($answer, $err);
    }

    /*
     * true - article exist
     * false - article not exist
     * */
    public function checkArticlesSearch($article_nr_displ, $brand_id): bool
    {
        $db = DbSingleton::getTokoDb();
        $article_nr_search = $this->getFormatAticle($article_nr_displ);
        $brand_id = (int)$brand_id;
        $r = $db->query("SELECT COUNT(`ART_ID`) as count_arts FROM `T2_CROSS` WHERE `SEARCH_NUMBER` = '$article_nr_search' AND `BRAND_ID` = $brand_id AND `KIND` = 0 AND `RELATION` = 0 LIMIT 1;");
        $n = $db->result($r, 0, "count_arts");
        return ($n > 0);
    }

    public function getArticlesSearch($article_nr_displ, $brand_id)
    {
        $db = DbSingleton::getTokoDb();
        $article_nr_search = $this->getFormatAticle($article_nr_displ);
        $r = $db->query("SELECT `ART_ID` FROM `T2_CROSS` WHERE `SEARCH_NUMBER` = '$article_nr_search' AND `BRAND_ID` = $brand_id AND `KIND` = 0 AND `RELATION` = 0 LIMIT 1;");
        return $db->result($r, 0, "ART_ID");
    }

    public function getFormatAticle($name)
    {
        $name = strtolower($name);
        return str_replace(str_split('.,+-\/:*?"<>| '), "", $name);
    }

    /*
     * suppl_art - tinyint(1)
     * */
    public function addArticle($suppl_art, $article_nr_displ, $brand_id, $name_ru, $name_ua, $name_en, $info, $costums_code): bool
    {
        $db = DbSingleton::getTokoDb();
        $article_nr_search = $this->getFormatAticle($article_nr_displ);
        $uktzed = $this->getUKTZED($costums_code);
        if ((int)$suppl_art === 0) {
            $art_id = $this->getMaxIndex();
        } else {
            $art_id = $this->getMaxSupplIndex();
        }
        // ===== t2_articles
        $db->query("INSERT INTO `T2_ARTICLES` (`ART_ID`, `ARTICLE_NR_DISPL`, `ARTICLE_NR_SEARCH`, `BRAND_ID`) VALUES ('$art_id', '$article_nr_displ', '$article_nr_search', '$brand_id');");
        // ===== t2_cross
        $db->query("INSERT INTO `T2_CROSS` (`ART_ID`, `SEARCH_NUMBER`, `KIND`, `BRAND_ID`, `DISPLAY_NR`, `RELATION`) VALUES ('$art_id', '$article_nr_search', '0', '$brand_id', '$article_nr_displ', '0');");
        // ===== t2_names
        if ($name_ru !== "") {
            $db->query("INSERT INTO `T2_NAMES` (`ART_ID`, `LANG_ID`, `NAME`, `INFO`) VALUES ('$art_id', '16', '$name_ru', '$info');");
        }
        if ($name_ua !== "") {
            $db->query("INSERT INTO `T2_NAMES` (`ART_ID`, `LANG_ID`, `NAME`, `INFO`) VALUES ('$art_id', '41', '$name_ua', '$info');");
        }
        if ($name_en !== "") {
            $db->query("INSERT INTO `T2_NAMES` (`ART_ID`, `LANG_ID`, `NAME`, `INFO`) VALUES ('$art_id', '4', '$name_en', '$info');");
        }
        // ===== uktzed
        if ($uktzed > 0) {
            $db->query("INSERT INTO `T2_ZED` (`ART_ID`, `COUNTRY_ID`, `COSTUMS_ID`) VALUES ('$art_id', '0', '$uktzed');");
        }
        return true;
    }

    public function getUKTZED($costums_code)
    {
        $db = DbSingleton::getTokoDb();
        $uktzed = 0;
        $r = $db->query("SELECT `COSTUMS_ID` FROM `T2_COSTUMS` WHERE `COSTUMS_CODE`='$costums_code' LIMIT 1;");
        $n = (int)$db->num_rows($r);
        if ($n > 0) {
            $uktzed = $db->result($r, 0, "COSTUMS_ID");
        }
        return $uktzed;
    }

    /*
     * IMPORT INDEX
     * */
    public function showImportPhotoForm()
    {
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $form = ""; $form_htm = RD . "/tpl/import_photo/form.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $form = str_replace(array("{import_photo}", "{import_photo_csv}", "{user_id}"), array($this->loadImportPhoto($user_id), $this->loadImportPhotoCsv($user_id), $user_id), $form);
        return $form;
    }

    public function loadImportPhoto($user_id): string
    {
        $count = 0; $list = "";
        $imagesDirectory = RD . "/cdn/photos_files/photo/$user_id/";
        if (is_dir($imagesDirectory)) {
            $list = "<div class='photo-div'>";
            $opendirectory = opendir($imagesDirectory);
            while (($image = readdir($opendirectory)) !== false) {
                if (($image === '.') || ($image === '..')) {
                    continue;
                }
                $imgFileType = pathinfo($image,PATHINFO_EXTENSION);
                if (($imgFileType === 'jpg') || ($imgFileType === 'png')) {
                    $imagesDirectory = str_replace("/var/www/", "https://", $imagesDirectory);
                    $count++;
                    $list .= "<div class='photo-div__item'><img src='$imagesDirectory".$image."' alt='$image'></div>";
                }
            }
            closedir($opendirectory);
            $list .= "</div>";
        }

        if ($count === 0 || $list === "") {
            $list = "<h3>Фото не знайдено</h3>";
        }

        return $list;
    }

    public function dropImportPhoto($user_id): bool
    {
        $db = DbSingleton::getTokoDb();
        $dirPath = RD . "/cdn/photos_files/photo/$user_id/";
        if (! is_dir($dirPath)) {
            throw new InvalidArgumentException("$dirPath must be a directory");
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
        $db->query("DELETE FROM `T2_PHOTOS_UPLOAD` WHERE `user_id` = $user_id;");

        return true;
    }

    public function loadImportPhotoCsvList($user_id)
    {
        $db = DbSingleton::getTokoDb();
        $list = "";
        $kol_cols = $fn = 0;
        $r = $db->query("SELECT `file_name` FROM `T2_PHOTOS_CSV_UPLOAD` WHERE `user_id` = $user_id LIMIT 1;");
        $n = (int)$db->num_rows($r);
        if ($n === 1) {
            $file_name = $db->result($r, 0, "file_name");
            $file_path = RD . "/cdn/photos_files/csv/$user_id/$file_name";
            if (file_exists($file_path)) {
                $form = ""; $form_htm = RD . "/tpl/import_photo/table.htm";
                if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
                $cols_list = "";
                $records_list = "";
                $handle = @fopen($file_path, "r");
                if ($handle) {
                    set_time_limit(0);
                    $max_cols = 0;
                    while (($buffer = fgets($handle, 4096)) !== false) {
                        ++$fn;
                        $buf = explode(";", $buffer);
                        if ($buffer !== "") {
                            if ($fn === 1) {
                                $kol_cols = count($buf);
                            }
                            $buf = str_replace("'", "\'", $buf);
                            $buf = str_replace('"', '\"',$buf);
                            $row = ""; $ex_cols = 0;
                            if ($max_cols < $kol_cols) {
                                $ex_cols = 1;
                                $cols_list = "";
                            }
                            for ($i = 1; $i <= $kol_cols; $i++) {
                                if ($i == 1) {
                                    $row = "<td>$fn</td>";
                                }
                                $row .= "<td>" . trim($buf[$i-1]) . "</td>";
                                if ($ex_cols === 1) {
                                    $sel1 = $sel2 = $sel3 = $sel4 = "";
                                    if ($i == 1) $sel1 = "selected";
                                    if ($i == 2) $sel2 = "selected";
                                    if ($i == 3) $sel3 = "selected";
                                    if ($i == 4) $sel4 = "selected";
                                    $cols_list .= "<th><select id=\"clm-$i\" size='1'>
                                        <option value='0'>-</option>
                                        <option value='1' $sel1>ARTICLE_NR_DISPL</option>
                                        <option value='2' $sel2>BRAND_ID</option>
                                        <option value='3' $sel3>PICTURE_NAME</option>
                                        <option value='4' $sel4>MAIN_PICTURE</option>
                                    </select></th>";
                                }
                            }
                            if ($row !== "") {
                                $records_list .= "<tr>$row</tr>";
                            }
                        }
                        if ($fn === 30) {
                            break;
                        }
                    }
                    fclose($handle);
                }
                $form = str_replace(array("{user_id}", "{cols_list}", "{kol_cols}", "{records_list}"), array($user_id, $cols_list, $kol_cols, $records_list), $form);
                $list = $form;
            }
        }

        return $list;
    }

    public function loadImportPhotoCsv($user_id) {
        $form = ""; $form_htm = RD . "/tpl/import_photo/upload.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $pre_table = $this->loadImportPhotoCsvList($user_id);
        [$table, $table_count] = $this->showPhotoTablePreview($user_id);
        $form = str_replace(array("{user_id}", "{csv_str_file}", "{table_str_file}"), array($user_id, ($pre_table !== "") ? $pre_table : (($table_count == 0) ? "<h3>Файл не знайдено</h3>" : ""), ($table_count == 0) ? "" : $table), $form);
        return $form;
    }

    public function showPhotoTablePreview($user_id): array
    {
        $form = ""; $form_htm = RD . "/tpl/import_photo/preload.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm);}
        $form = str_replace("{user_id}", $user_id, $form);
        $tableData = $this->loadPhotoTablePreview($user_id);
        $form = str_replace(array("{records_list}", "{records_list2}", "{records_list3}"), array($tableData["list"], $tableData["list2"], $tableData["list3"]), $form);

        return array($form, $tableData["count"]);
    }

    public function saveImportPhotoCsv($user_id, $start_row, $kol_cols, $cols): array
    {
        $db = DbSingleton::getTokoDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка збереження даних!";
        $fn = 0;
        $user_id = $slave->qq($user_id);
        $start_row = $slave->qq($start_row);
        $kol_cols = $slave->qq($kol_cols);
        $cols = $slave->qq($cols);
        $db->query("DELETE FROM `T2_PHOTOS_IMPORT_UPLOAD` WHERE `user_id` = $user_id;");

        if ($user_id > 0) {
            $r = $db->query("SELECT `file_name` FROM `T2_PHOTOS_CSV_UPLOAD` WHERE `user_id` = $user_id LIMIT 1;");
            $n = (int)$db->num_rows($r);
            if ($n === 1) {
                $file_name = $db->result($r, 0, "file_name");
                $file_path = RD . "/cdn/photos_files/csv/$user_id/$file_name";
                if (file_exists($file_path)) {
                    $article_nr_displ = "";
                    $brand_id = 0;
                    $picture_name = "";
                    $main_picture = 0;
                    for ($i = 1; $i <= $kol_cols; $i++) {
                        if ($cols[$i] == 1) { $article_nr_displ = $i; }
                        if ($cols[$i] == 2) { $brand_id = $i; }
                        if ($cols[$i] == 3) { $picture_name = $i; }
                        if ($cols[$i] == 4) { $main_picture = $i; }
                    }
                    $handle = @fopen($file_path, "r");
                    if ($handle) {
                        set_time_limit(0);
                        while (($buffer = fgets($handle, 4096)) !== false) {
                            ++$fn;
                            $buf = explode(";", $buffer);
                            if (($buffer !== "") && $fn >= $start_row) {
                                $buf = str_replace("'", "\'", $buf);
                                $buf = str_replace('"', '\"', $buf);

                                $article_nr_displ2 = trim($buf[$article_nr_displ - 1]);
                                $brand_id2 = trim($buf[$brand_id - 1]);
                                $picture_name2 = trim($buf[$picture_name - 1]);
                                $main_picture2 = trim($buf[$main_picture - 1]);

                                $art_id = $this->getArticlesSearch($article_nr_displ2, $brand_id2);

                                if ($art_id > 0) {
                                    $status = 1;
                                    if ($this->checkT2Photo($art_id)) {
                                        $status = 0;
                                    }
                                } else {
                                    $status = 0;
                                    $art_id = 0;
                                }

                                $r2 = $db->query("SELECT MAX(`id`) as mid FROM `T2_PHOTOS_IMPORT_UPLOAD`;");
                                $import_id = 0 + $db->result($r2, 0, "mid") + 1;
                                $db->query("INSERT INTO `T2_PHOTOS_IMPORT_UPLOAD` (`id`, `user_id`, `art_id`, `article_nr_displ`, `brand_id`, `picture_name`, `main_picture`, `status`) 
                                VALUES ('$import_id', '$user_id', '$art_id', '$article_nr_displ2', '$brand_id2', '$picture_name2', '$main_picture2', '$status');");
                            }
                        }

                        fclose($handle);
                        if (file_exists(RD . "/cdn/photos_files/csv/$user_id/$file_name")) {
                            unlink(RD . "/cdn/photos_files/csv/$user_id/$file_name");
                        }
                        $db->query("DELETE FROM `T2_PHOTOS_CSV_UPLOAD` WHERE `user_id` = $user_id;");
                        $answer = 1; $err = "";
                    }
                }
            }
        }

        return array($answer, $err);
    }

    public function loadPhotoTablePreview($user_id): array
    {
        $db = DbSingleton::getTokoDb();
        $mas = [];
        $table = $table_exist = $table_unknown = "";

        $r = $db->query("SELECT `art_id`, `article_nr_displ`, `brand_id`, `picture_name`, `main_picture`, `status` FROM `T2_PHOTOS_IMPORT_UPLOAD`
        WHERE `user_id` = $user_id ORDER BY `status` ASC, `id` ASC;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $art_id = (int)$db->result($r, $i - 1, "art_id");
            $article_nr_displ = $db->result($r, $i - 1, "article_nr_displ");
            $brand_id = $db->result($r, $i - 1, "brand_id");
            $picture_name = $db->result($r, $i - 1, "picture_name");
            $main_picture = $db->result($r, $i - 1, "main_picture");
            $status = (int)$db->result($r, $i - 1, "status");
            $mas[$i] = compact("art_id", "article_nr_displ", "brand_id", "picture_name", "main_picture", "status");
        }

        $i1 = $i2 = $i3 = 0;
        foreach ($mas as $val) {
            $art_id = $val["art_id"];
            $article_nr_displ = $val["article_nr_displ"];
            $brand_id = $val["brand_id"];
            $brand_name = $this->getBrandName($brand_id);
            $picture_name = $val["picture_name"];
            $main_picture = $val["main_picture"];
            $status = $val["status"];

            if ($status === 1) {
                $i1++;
                $photo = $this->getImportPhotoUpload($user_id, $picture_name);
                $table .= "<tr>
                    <td>$i1</td>
                    <td>$art_id</td>
                    <td>$article_nr_displ</td>
                    <td>$brand_name ($brand_id)</td>
                    <td>$picture_name</td>
                    <td>$main_picture</td>
                    <td align='center'>$photo</td>
                </tr>";
            }

            if ($status === 0 && $art_id > 0) {
                $i2++;
                $photo = $this->getT2Photo($art_id);
                $new_photo = $this->getImportPhotoUpload($user_id, $picture_name);
                $table_exist .= "<tr>
                    <td>$i2</td>
                    <td>$art_id</td>
                    <td>$article_nr_displ</td>
                    <td>$brand_name ($brand_id)</td>
                    <td>$picture_name</td>
                    <td>$main_picture</td>
                    <td align='center'>$photo</td>
                    <td align='center'>$new_photo</td>
                </tr>";
            }

            if ($status === 0 && $art_id === 0) {
                $i3++;
                $table_unknown .= "<tr>
                    <td>$i3</td>
                    <td>$article_nr_displ</td>
                    <td>$brand_name ($brand_id)</td>
                    <td>$picture_name</td>
                    <td>$main_picture</td>
                </tr>";
            }
        }

        return array("list" => $table, "list2" => $table_exist, "list3" => $table_unknown, "count" => count($mas));
    }

    public function clearImportPhoto($user_id): array
    {
        $db = DbSingleton::getTokoDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка збереження даних!";
        $user_id = $slave->qq($user_id);
        if ($user_id > 0) {
            $db->query("DELETE FROM `T2_PHOTOS_IMPORT_UPLOAD` WHERE `user_id` = $user_id;");
            $answer = 1; $err = "";
        }

        return array($answer, $err);
    }

    public function getImportPhotoUpload($user_id, $photo_name): string
    {
        $db = DbSingleton::getTokoDb();
        $photo = "";
        $r = $db->query("SELECT * FROM `T2_PHOTOS_UPLOAD` WHERE `user_id` = $user_id AND `file_name` LIKE '$photo_name' LIMIT 1;");
        $n = (int)$db->num_rows($r);
        if ($n > 0) {
            $photo = "<img src='https://portal.myparts.pro/cdn/photos_files/photo/$user_id/$photo_name' width='50' alt='$photo_name'>";
        }
        return $photo;
    }

    public function getT2Photo($art_id): string
    {
        $db = DbSingleton::getTokoDb();
        $photo = "";
        $r = $db->query("SELECT `PHOTO_NAME`, `MAIN` FROM `T2_PHOTOS` WHERE `ART_ID` = $art_id AND `ACTIVE` = 1 ORDER BY `MAIN` DESC;");
        $n = (int)$db->num_rows($r);

        if ($n > 0) {
            for ($i = 1; $i <= $n; $i++) {
                $photo_name = $db->result($r, $i - 1, "PHOTO_NAME");
                $main = $db->result($r, $i - 1, "MAIN");
                $style = ($main) ? "green-img" : "";
                $photo .= "<img class='$style' src='https://toko.ua/uploads/images/catalogue/$photo_name' width='50' alt='$photo_name'>";
            }
        }
        return $photo;
    }

    public function checkT2Photo($art_id): bool
    {
        $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT `ID` FROM `T2_PHOTOS` WHERE `ART_ID` = $art_id AND `ACTIVE` = 1;");
        $n = (int)$db->num_rows($r);
        return ($n > 0);
    }

    public function finishImportPhoto($user_id): array
    {
        $db = DbSingleton::getTokoDb();
        $answer = 0; $err = "Помилка збереження даних!";
        if ($user_id > 0) {
            $r = $db->query("SELECT `id`, `art_id`, `picture_name`, `main_picture` FROM `T2_PHOTOS_IMPORT_UPLOAD` WHERE `user_id` = $user_id AND `status` = 1;");
            $n = (int)$db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $id = $db->result($r, $i - 1, "id");
                $art_id = $db->result($r, $i - 1, "art_id");
                $picture_name = $db->result($r, $i - 1, "picture_name");
                $main_picture = $db->result($r, $i - 1, "main_picture");
                $this->addArticlePhoto($user_id, $art_id, $picture_name, $main_picture);
                $db->query("DELETE FROM `T2_PHOTOS_IMPORT_UPLOAD` WHERE `id` = $id;");
            }
            $answer = 1; $err = "";
        }

        return array($answer, $err);
    }

    public function addArticlePhoto($user_id, $art_id, $picture_name, $main_picture): bool
    {
        $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT MAX(`ID`) as max_id FROM `T2_PHOTOS`;");
        $max_id = $db->result($r, 0, "max_id") + 1;
        $db->query("INSERT INTO `T2_PHOTOS` (`ID`, `ART_ID`, `USER_ID`, `PHOTO_NAME`, `MAIN`, `ACTIVE`) VALUES ('$max_id', '$art_id', '$user_id', '$picture_name', '$main_picture', 1);");
        if (file_exists(RD . "/cdn/photos_files/photo/$user_id/$picture_name")) {
            rename(RD . "/cdn/photos_files/photo/$user_id/$picture_name", RD . "/uploads/images/catalogue/$picture_name");
        }

        return true;
    }

    public function dropArticlePhoto($art_id): bool
    {
        $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT `ID`, `PHOTO_NAME` FROM `T2_PHOTOS` WHERE `ART_ID` = $art_id;");
        $n = (int)$db->num_rows($r);
        if ($n > 0) {
            for ($i = 1; $i <= $n; $i++) {
                $file_id = $db->result($r, $i - 1, "ID") + 0;
                $file_name = $db->result($r, $i - 1, "PHOTO_NAME");
                if (file_exists(RD . "/uploads/images/catalogue/$file_name")) {
                    unlink(RD . "/uploads/images/catalogue/$file_name");
                }
                $db->query("DELETE FROM `T2_PHOTOS` WHERE `ART_ID` = $art_id AND `ID` = $file_id;");
            }
        }

        return true;
    }

    public function finishImportExistPhoto($user_id): array
    {
        $db = DbSingleton::getTokoDb();
        $answer = 0; $err = "Помилка збереження даних!";
        if ($user_id > 0) {
            $arts = [];
            $r = $db->query("SELECT `art_id` FROM `T2_PHOTOS_IMPORT_UPLOAD` WHERE `user_id` = $user_id AND `status` = 0 AND `art_id` != 0;");
            $n = (int)$db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $art_id = $db->result($r, $i - 1, "art_id");
                $arts[] = $art_id;
            }
            $arts = array_unique($arts);
            foreach ($arts as $art_id) {
                $this->dropArticlePhoto($art_id);
            }
            $r = $db->query("SELECT `id`, `art_id`, `picture_name`, `main_picture` FROM `T2_PHOTOS_IMPORT_UPLOAD` WHERE `user_id` = $user_id AND `status` = 0 AND `art_id` != 0;");
            $n = (int)$db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $id = $db->result($r, $i - 1, "id");
                $art_id = $db->result($r, $i - 1, "art_id");
                $picture_name = $db->result($r, $i - 1, "picture_name");
                $main_picture = $db->result($r, $i - 1, "main_picture");
                $this->addArticlePhoto($user_id, $art_id, $picture_name, $main_picture);
                $db->query("DELETE FROM `T2_PHOTOS_IMPORT_UPLOAD` WHERE `id` = $id;");
            }
            $answer = 1; $err = "";
        }

        return array($answer, $err);
    }

    /*
     * Import Cross
     * */
    public function showImportCrossForm() {
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $form = ""; $form_htm = RD . "/tpl/import_cross/form.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $form = str_replace(array("{import_cross}", "{user_id}"), array($this->loadImportCross($user_id), $user_id), $form);

        return $form;
    }

    public function loadImportCross($user_id) {
        $form = ""; $form_htm = RD . "/tpl/import_cross/upload.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        // preload
        $pre_table = $this->loadImportCrossList($user_id);
        // table
        [$table, $table_count] = $this->showCrossTablePreview($user_id);
        $form = str_replace(array("{user_id}", "{csv_str_file}", "{table_str_file}"), array($user_id, ($pre_table !== "") ? $pre_table : (($table_count == 0) ? "<h3>Файл не знайдено</h3>" : ""), ($table_count == 0) ? "" : $table), $form);

        return $form;
    }

    public function loadImportCrossList($user_id)
    {
        $db = DbSingleton::getTokoDb();
        $list = "";
        $kol_cols = $fn = 0;
        $r = $db->query("SELECT `file_name` FROM `T2_CROSS_CSV_UPLOAD` WHERE `user_id` = $user_id LIMIT 1;");
        $n = (int)$db->num_rows($r);
        if ($n === 1) {
            $file_name = $db->result($r, 0, "file_name");
            $file_path = RD . "/cdn/cross_files/csv/$user_id/$file_name";
            if (file_exists($file_path)) {
                $form = ""; $form_htm = RD . "/tpl/import_cross/table.htm";
                if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
                $cols_list = ""; $records_list = "";
                $handle = @fopen($file_path, "r");
                if ($handle) {
                    set_time_limit(0);
                    $max_cols = 0;
                    while (($buffer = fgets($handle, 4096)) !== false) {
                        ++$fn;
                        $buf = explode(";", $buffer);
                        if ($buffer !== "") {
                            if ($fn === 1) {
                                $kol_cols = count($buf);
                            }
                            $buf = str_replace("'", "\'", $buf);
                            $buf = str_replace('"', '\"', $buf);

                            $row = ""; $ex_cols = 0;
                            if ($max_cols < $kol_cols) {
                                $ex_cols = 1; $cols_list = "";
                            }
                            for ($i = 1; $i <= $kol_cols; $i++) {
                                if ($i === 1) {
                                    $row = "<td>$fn</td>";
                                }
                                $row .= "<td>" . trim($buf[$i - 1]) . "</td>";
                                if ($ex_cols === 1) {
                                    $sel1 = $sel2 = $sel3 = $sel4 = $sel5 = "";
                                    if ($i == 1) $sel1 = "selected";
                                    if ($i == 2) $sel2 = "selected";
                                    if ($i == 3) $sel3 = "selected";
                                    if ($i == 4) $sel4 = "selected";
                                    if ($i == 5) $sel5 = "selected";
                                    $cols_list .= "<th><select id=\"clm-$i\" size='1'>
                                        <option value='0'>-</option>
                                        <option value='1' $sel1>CROSS_ARTICLE</option>
                                        <option value='2' $sel2>CROSS_BRAND</option>
                                        <option value='3' $sel3>CROSS_RELATION</option>
                                        <option value='4' $sel4>RESULT_ARTICLE</option>
                                        <option value='5' $sel5>RESULT_BRAND</option>
                                    </select></th>";
                                }
                            }
                            if ($row !== "") {
                                $records_list .= "<tr>$row</tr>";
                            }
                        }
                        if ($fn === 30) {
                            break;
                        }
                    }
                    fclose($handle);
                }
                $form = str_replace(array("{user_id}", "{cols_list}", "{kol_cols}", "{records_list}"), array($user_id, $cols_list, $kol_cols, $records_list), $form);
                $list = $form;
            }
        }
        return $list;
    }

    public function saveImportCrossUnknown($user_id): array
    {
        $db = DbSingleton::getTokoDb();
        $answer = 0; $err = "Помилка збереження даних!";

        $db->query("DELETE FROM `T2_CROSS_IMPORT_UPLOAD` WHERE `user_id` = $user_id;");
        $r = $db->query("SELECT COUNT(`id`) as count_ids FROM `T2_CROSS_IMPORT_UPLOAD` WHERE 1;");
        $count_arts = (int)$db->result($r, 0, "count_arts");

        if ($count_arts === 0) {
            $db->query("TRUNCATE TABLE `T2_CROSS_IMPORT_UPLOAD`;");
        }

        if ($user_id > 0) {
            $r = $db->query("SELECT * FROM `T2_CROSS_IMPORT_UPLOAD_UNKNOWN` WHERE `user_id` = '$user_id';");
            $n = (int)$db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $art_id = $db->result($r, $i - 1, "art_id");
                $cross_article = $db->result($r, $i - 1, "cross_article");
                $cross_article_search = $db->result($r, $i - 1, "cross_article_search");
                $cross_brand = $db->result($r, $i - 1, "cross_brand");
                $cross_brand_id = $db->result($r, $i - 1, "cross_brand_id");
                $cross_brand_kind = $db->result($r, $i - 1, "cross_brand_kind");
                $cross_relation = $db->result($r, $i - 1, "cross_relation");
                $result_article = $db->result($r, $i - 1, "result_article");
                $result_brand = $db->result($r, $i - 1, "result_brand");
                $result_brand_id = $db->result($r, $i - 1, "result_brand_id");
                $status = 0;

                if ($result_brand_id > 0 && $cross_brand_id > 0) {
                    [$status, $art_id] = $this->getArticleID($result_article, $result_brand_id);
                }

                $db->query("INSERT INTO `T2_CROSS_IMPORT_UPLOAD` (`user_id`, `art_id`, `cross_article`, `cross_article_search`, `cross_brand`, `cross_brand_id`, `cross_brand_kind`, `cross_relation`, `result_article`, `result_brand`, `result_brand_id`, `status`) 
                VALUES ('$user_id', '$art_id', '$cross_article', '$cross_article_search', '$cross_brand', '$cross_brand_id', '$cross_brand_kind', '$cross_relation', '$result_article', '$result_brand', '$result_brand_id', '$status');");
            }

            $answer = 1; $err = "";
        }

        $db->query("DELETE FROM `T2_CROSS_IMPORT_UPLOAD_UNKNOWN` WHERE `user_id` = $user_id;");
        $r = $db->query("SELECT COUNT(`id`) as count_ids FROM `T2_CROSS_IMPORT_UPLOAD_UNKNOWN` WHERE 1;");
        $count_arts = (int)$db->result($r, 0, "count_arts");
        if ($count_arts === 0) {
            $db->query("TRUNCATE TABLE `T2_CROSS_IMPORT_UPLOAD_UNKNOWN`;");
        }

        return array($answer, $err);
    }

    public function saveImportCross($user_id, $start_row, $kol_cols, $cols): array
    {
        $db = DbSingleton::getTokoDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка збереження даних!";
        $fn = 0;
        $user_id = $slave->qq($user_id);
        $start_row = $slave->qq($start_row);
        $kol_cols = $slave->qq($kol_cols);
        $cols = $slave->qq($cols);

        $db->query("DELETE FROM `T2_CROSS_IMPORT_UPLOAD` WHERE `user_id` = $user_id;");

        $r = $db->query("SELECT COUNT(`id`) as count_ids FROM `T2_CROSS_IMPORT_UPLOAD` WHERE 1;");
        $count_arts = (int)$db->result($r, 0, "count_arts");

        if ($count_arts === 0) {
            $db->query("TRUNCATE TABLE `T2_CROSS_IMPORT_UPLOAD`;");
        }

        if ($user_id > 0) {
            $r = $db->query("SELECT `file_name` FROM `T2_CROSS_CSV_UPLOAD` WHERE `user_id`='$user_id' LIMIT 1;");
            $n = (int)$db->num_rows($r);
            if ($n === 1) {
                $file_name = $db->result($r, 0, "file_name");
                $file_path = RD . "/cdn/cross_files/csv/$user_id/$file_name";
                if (file_exists($file_path)) {
                    $cross_article = "";
                    $cross_brand = "";
                    $cross_relation = 0;
                    $result_article = "";
                    $result_brand = "";

                    for ($i = 1; $i <= $kol_cols; $i++) {
                        if ($cols[$i] == 1) { $cross_article = $i; }
                        if ($cols[$i] == 2) { $cross_brand = $i; }
                        if ($cols[$i] == 3) { $cross_relation = $i; }
                        if ($cols[$i] == 4) { $result_article = $i; }
                        if ($cols[$i] == 5) { $result_brand = $i; }
                    }

                    $handle = @fopen($file_path, "r");
                    if ($handle) {
                        set_time_limit(0);
                        while (($buffer = fgets($handle, 4096)) !== false) {
                            ++$fn;
                            $buf = explode(";", $buffer);
                            if (($buffer !== "") && $fn >= $start_row) {
                                $buf = str_replace("'", "\'", $buf);
                                $buf = str_replace('"', '\"', $buf);

                                $cross_article2 = trim($buf[$cross_article - 1]);
                                $cross_article_search = strtoupper($this->clearArticle($cross_article2));
                                $cross_brand2 = trim($buf[$cross_brand - 1]);
                                $cross_brand_id = $this->getArticleBrandID($cross_brand2);
                                $cross_brand_kind = $this->getArticleBrandKind($cross_brand_id);
                                $cross_relation2 = trim($buf[$cross_relation - 1]);
                                $result_article2 = trim($buf[$result_article - 1]);
                                $result_brand2 = trim($buf[$result_brand - 1]);
                                $result_brand_id = $this->getArticleBrandID($result_brand2);
                                $art_id = 0; $status = 0;

                                if ($result_brand_id > 0 && $cross_brand_id > 0) {
                                    [$status, $art_id] = $this->getArticleID($result_article2, $result_brand_id);
                                }

                                $r2 = $db->query("SELECT MAX(`id`) as mid FROM `T2_CROSS_IMPORT_UPLOAD`;");
                                $import_id = 0 + $db->result($r2, 0, "mid") + 1;
                                $db->query("INSERT INTO `T2_CROSS_IMPORT_UPLOAD` (`id`, `user_id`, `art_id`, `cross_article`, `cross_article_search`, `cross_brand`, `cross_brand_id`, `cross_brand_kind`, `cross_relation`, `result_article`, `result_brand`, `result_brand_id`, `status`) 
                                VALUES ('$import_id', '$user_id', '$art_id', '$cross_article2', '$cross_article_search', '$cross_brand2', '$cross_brand_id', '$cross_brand_kind', '$cross_relation2', '$result_article2', '$result_brand2', '$result_brand_id', '$status');");
                            }
                        }
                        fclose($handle);
                        if (file_exists(RD . "/cdn/cross_files/csv/$user_id/$file_name")) {
                            unlink(RD . "/cdn/cross_files/csv/$user_id/$file_name");
                        }
                        $db->query("DELETE FROM `T2_CROSS_CSV_UPLOAD` WHERE `user_id` = $user_id;");
                        $answer = 1; $err = "";
                    }
                }
            }
        }

        return array($answer, $err);
    }

    public function showCrossTablePreview($user_id): array
    {
        $form = ""; $form_htm = RD . "/tpl/import_cross/preload.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm);}
        $form = str_replace("{user_id}", $user_id, $form);
        $tableData = $this->loadCrossTablePreview($user_id);
        $form = str_replace(array("{records_list}", "{brand_result_select}", "{brand_cross_select}", "{user_id}", "{status_check}"), array($tableData["list"], $this->getUnknownBrands($tableData["brands_result"]), $this->getUnknownBrands($tableData["brands_cross"]), $user_id, $tableData["status_check_end"]), $form);

        return array($form, $tableData["count"]);
    }

    /*
     * type = cross_brand
     * type = result_brand
     * */
    public function setUnknownBrands($user_id, $type, $brand_id_from, $brand_id_to): array
    {
        $db = DbSingleton::getTokoDb();
        $answer = 0; $err = "Помилка!";
        $type_id = $type . "_id";

        if ($brand_id_from !== "" && $brand_id_to > 0 && $type !== "") {
            $r = $db->query("SELECT `id`, `$type`, `result_article` FROM `T2_CROSS_IMPORT_UPLOAD` 
            WHERE `user_id` = $user_id AND `status` = 0 AND `$type_id` = 0 AND `$type` = '$brand_id_from';");
            $n = (int)$db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $id = $db->result($r, $i - 1, "id") + 0;
                $result_article = $db->result($r, $i - 1, "result_article");

                if ($brand_id_to > 0) {
                    $db->query("UPDATE `T2_CROSS_IMPORT_UPLOAD` SET `$type_id` = '$brand_id_to' WHERE `id` = $id LIMIT 1;");

                    $r3 = $db->query("SELECT `result_brand_id`, `cross_brand_id` FROM `T2_CROSS_IMPORT_UPLOAD` WHERE `id` = $id LIMIT 1;");
                    $result_brand_id = $db->result($r3, 0, "result_brand_id");
                    $cross_brand_id = $db->result($r3, 0, "cross_brand_id");

                    if ($type === "cross_brand") {
                        $cross_brand_kind = $this->getArticleBrandKind($cross_brand_id);
                        $db->query("UPDATE `T2_CROSS_IMPORT_UPLOAD` SET `cross_brand_kind` = '$cross_brand_kind' WHERE `id` = $id LIMIT 1;");
                    }

                    $art_id = 0; $status = 0;
                    if ($result_brand_id > 0 && $cross_brand_id > 0) {
                        [$status, $art_id] = $this->getArticleID($result_article, $result_brand_id);
                    }

                    $db->query("UPDATE `T2_CROSS_IMPORT_UPLOAD` SET `art_id` = '$art_id', `status` = '$status' WHERE `id` = $id LIMIT 1;");
                }
            }
            $answer = 1; $err = "";
        }

        return array($answer, $err);
    }

    public function getUnknownBrands($brands = []): string
    {
        $list = "";
        foreach ($brands as $brand) {
            $list .= "<option value='$brand'>$brand</option>";
        }
        return $list;
    }

    public function getUnknownBrandsCatalog($brand): string
    {
        $db = DbSingleton::getTokoDb();
        $list = "<option value='0'>-Не вибрано-</option>";
        $r = $db->query("SELECT `BRAND_ID`, `BRAND_NAME` FROM `T2_BRANDS` WHERE `BRAND_NAME` LIKE '%$brand%' ORDER BY `BRAND_NAME` ASC;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $brand_id   = $db->result($r, $i - 1, "BRAND_ID");
            $brand_name = $db->result($r, $i - 1, "BRAND_NAME");

            $list .= "<option value='$brand_id'>$brand_name ($brand_id)</option>";
        }
        return $list;
    }

    public function getUnknownBrandsAll(): string
    {
        $db = DbSingleton::getTokoDb();
        $list = "<option value='0'>-Не вибрано-</option>";
        $r = $db->query("SELECT `BRAND_ID`, `BRAND_NAME` FROM `T2_BRANDS` ORDER BY `BRAND_NAME` ASC;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $brand_id   = $db->result($r, $i - 1, "BRAND_ID");
            $brand_name = $db->result($r, $i - 1, "BRAND_NAME");

            $list .= "<option value='$brand_id'>$brand_name ($brand_id)</option>";
        }
        return $list;
    }

    /*
     * import cross load data
     * */
    public function loadCrossTablePreview($user_id, $status_check = 0): array
    {
        $db = DbSingleton::getTokoDb();
        $table = "";
        $mas = [];
        $brands_result = $brands_cross = [];

        $r2 = $db->query("SELECT COUNT(`id`) as count_arts FROM `T2_CROSS_IMPORT_UPLOAD` WHERE `user_id` = $user_id;");
        $count_arts = $db->result($r2, 0, "count_arts");

        $r = $db->query("SELECT * FROM `T2_CROSS_IMPORT_UPLOAD` WHERE `user_id` = $user_id ORDER BY `status` ASC, `id` ASC;");
        $n = (int)$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id                     = $db->result($r, $i - 1, "id");
            $art_id                 = $db->result($r, $i - 1, "art_id");
            $cross_article          = $db->result($r, $i - 1, "cross_article");
            $cross_article_search   = $db->result($r, $i - 1, "cross_article_search");
            $cross_brand            = $db->result($r, $i - 1, "cross_brand");
            $cross_brand_id         = (int)$db->result($r, $i - 1, "cross_brand_id");
            $cross_brand_kind       = $db->result($r, $i - 1, "cross_brand_kind");
            $cross_relation         = $db->result($r, $i - 1, "cross_relation");
            $result_article         = $db->result($r, $i - 1, "result_article");
            $result_brand           = $db->result($r, $i - 1, "result_brand");
            $result_brand_id        = (int)$db->result($r, $i - 1, "result_brand_id");
            $status                 = (int)$db->result($r, $i - 1, "status");
            $mas[$i] = compact("id", "art_id", "cross_article", "cross_article_search", "cross_brand", "cross_brand_id", "cross_brand_kind", "cross_relation", "result_article", "result_brand", "result_brand_id", "status");
        }

        $i = 0;
        foreach ($mas as $val) {
            $i++;
            $id = $val["id"];
            $art_id = $val["art_id"];
            $cross_article = $val["cross_article"];
            $cross_article_search = $val["cross_article_search"];
            $cross_brand = $val["cross_brand"];
            $cross_brand_id = $val["cross_brand_id"];
            $cross_brand_kind = $val["cross_brand_kind"];
            $cross_relation = $val["cross_relation"];
            $result_article = $val["result_article"];
            $result_brand = $val["result_brand"];
            $result_brand_id = $val["result_brand_id"];
            $status = $val["status"];
            $style = "";

            if ($status === 0) {
                $style = "style='background:lightcoral;'";
            }
            if ($status === 1) {
                $style = "style='background:lightgreen;'";
            }
            if ($status === 2) {
                $style = "style='background:#f0ad4e; cursor:pointer;' onclick='showClarifyForm(\"$id\");'";
            }
            $status_display = 0;
            if ((int)$status_check === 0) {
                if ($status === 2) {
                    $status_display = 1;
                }
            } else {
                $status_display = 1;
            }
            
            if ($status_display === 1) {
                $table .= "
                    <tr $style>
                        <td>$i</td>
                        <td>$art_id</td>
                        <td>$cross_article</td>
                        <td>$cross_article_search</td>
                        <td>$cross_brand</td>
                        <td>$cross_brand_id</td>
                        <td>$cross_brand_kind</td>
                        <td>$cross_relation</td>
                        <td>$result_article</td>
                        <td>$result_brand</td>
                        <td>$result_brand_id</td>
                        <td>$status</td>
                    </tr>";
            }

            if (!$status) {
                if ($result_brand_id === 0) {
                    $brands_result[] = $result_brand;
                }
                if ($cross_brand_id === 0) {
                    $brands_cross[] = $cross_brand;
                }
            }
        }

        return array(
            "list"              => $table,
            "brands_result"     => array_unique($brands_result),
            "brands_cross"      => array_unique($brands_cross),
            "count"             => $count_arts,
            "status_check_end"  => $status_check
        );
    }

    public function showClarifyForm($import_id): string
    {
        $db = DbSingleton::getTokoDb();
        $r2 = $db->query("SELECT `result_article`, `result_brand_id` FROM `T2_CROSS_IMPORT_UPLOAD` WHERE `id` = $import_id LIMIT 1;");
        $n2 = $db->num_rows($r2);
        if ($n2 > 0) {
            $list = "<ul>";
            $result_article     = $db->result($r2, 0, "result_article");
            $result_brand_id    = $db->result($r2, 0, "result_brand_id");
            $article_nr_search  = $this->clearArticle($result_article);

            $r = $db->query("SELECT `ART_ID`, `ARTICLE_NR_DISPL` FROM `T2_ARTICLES` WHERE `ARTICLE_NR_SEARCH` = '$article_nr_search' AND `BRAND_ID` = $result_brand_id;");
            $n = (int)$db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $art_id     = $db->result($r, $i - 1, "ART_ID");
                $art_nr_ds  = $db->result($r, $i - 1, "ARTICLE_NR_DISPL");

                $list .= "
                <li class='list-group-item' style='font-size: 2em;'>
                    <button class='btn btn-primary' onclick=\"saveClarifyForm('$import_id', '$art_id');\"><i class='fa fa-check'></i> Вибрати</button>
                    <span class='badge text-left'>ART_ID: $art_id</span>
                    <span class='badge text-left'>DISPLAY: $art_nr_ds</span>
                </li>";
            }
            $list .= "</ul>";
        } else {
            $list = "<h3>Нічого не знайдено</h3>";
        }

        return $list;
    }

    public function saveClarifyForm($import_id, $art_id): array
    {
        $db = DbSingleton::getTokoDb();
        $answer = 0; $err = "Помилка";
        if ($import_id > 0 && $art_id > 0) {
            $db->query("UPDATE `T2_CROSS_IMPORT_UPLOAD` SET `art_id` = $art_id, `status` = 1 WHERE `id` = $import_id LIMIT 1;");
            $answer = 1; $err = "";
        }

        return array($answer, $err);
    }

    public function clearImportCross($user_id): array
    {
        $db = DbSingleton::getTokoDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка збереження даних!";
        $user_id = $slave->qq($user_id);
        if ($user_id > 0) {
            $db->query("DELETE FROM `T2_CROSS_IMPORT_UPLOAD` WHERE `user_id` = $user_id;");
            $r = $db->query("SELECT COUNT(`id`) as count_ids FROM `T2_CROSS_IMPORT_UPLOAD` WHERE 1;");
            $count_arts = (int)$db->result($r, 0, "count_arts");
            if ($count_arts === 0) {
                $db->query("TRUNCATE TABLE `T2_CROSS_IMPORT_UPLOAD`;");
            }
            $answer = 1; $err = "";
        }

        return array($answer, $err);
    }

    /*
     * import cross save data
     * */
    public function finishImportCross($user_id): array
    {
        $db = DbSingleton::getTokoDb();
        $answer = 0; $err = "Помилка збереження даних!";
        if ($user_id > 0) {
            $r = $db->query("SELECT * FROM `T2_CROSS_IMPORT_UPLOAD` WHERE `user_id` = $user_id AND `status` = 1;");
            $n = (int)$db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $art_id                 = $db->result($r, $i - 1, "art_id") + 0;
                $cross_brand_id         = $db->result($r, $i - 1, "cross_brand_id") + 0;
                $cross_article          = $db->result($r, $i - 1, "cross_article");
                $cross_article_search   = $db->result($r, $i - 1, "cross_article_search");
                $cross_brand_kind       = $db->result($r, $i - 1, "cross_brand_kind") + 0;
                $cross_relation         = $db->result($r, $i - 1, "cross_relation") + 0;

                $r2 = $db->query("SELECT COUNT(`ART_ID`) as count_arts FROM `T2_CROSS` WHERE `ART_ID` = $art_id AND `SEARCH_NUMBER` = '$cross_article_search' AND `BRAND_ID` = $cross_brand_id AND `KIND` = $cross_brand_kind AND `RELATION` = $cross_relation;");
                $count_arts = (int)$db->result($r2, 0, "count_arts");
                if ($count_arts === 0) {
                    $db->query("INSERT INTO `T2_CROSS` (`ART_ID`, `SEARCH_NUMBER`, `KIND`, `BRAND_ID`, `DISPLAY_NR`, `RELATION`) VALUES ($art_id, '$cross_article_search', $cross_brand_kind, $cross_brand_id, '$cross_article', $cross_relation);");
                }
            }

            $answer = 1; $err = "";

            $db->query("
            INSERT INTO `T2_CROSS_IMPORT_UPLOAD_UNKNOWN` (`user_id`,`art_id`,`cross_article`,`cross_article_search`,`cross_brand`,`cross_brand_id`,`cross_brand_kind`,`cross_relation`,`result_article`,`result_brand`,`result_brand_id`) 
            ( 
                SELECT `user_id`,`art_id`,`cross_article`,`cross_article_search`,`cross_brand`,`cross_brand_id`,`cross_brand_kind`,`cross_relation`,`result_article`,`result_brand`,`result_brand_id` 
                FROM `T2_CROSS_IMPORT_UPLOAD` 
                WHERE `status` = 0 
            )");
            $db->query("DELETE FROM `T2_CROSS_IMPORT_UPLOAD` WHERE `user_id` = $user_id;");

            $r = $db->query("SELECT COUNT(`id`) as count_ids FROM `T2_CROSS_IMPORT_UPLOAD` WHERE 1;");
            $count_arts = (int)$db->result($r, 0, "count_arts");
            if ($count_arts === 0) {
                $db->query("TRUNCATE TABLE `T2_CROSS_IMPORT_UPLOAD`;");
            }
        }

        return array($answer, $err);
    }

}