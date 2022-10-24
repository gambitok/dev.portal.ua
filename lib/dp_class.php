<?php

use LisDev\Delivery\NovaPoshtaApi2;

class dp
{

    private static $langVariables;
    private static $langNames;

    protected $prefix_new = 'ДП';
    public $vat_percent = 20;

    function getArtId($code, $brand_id)
    {
        $db = DbSingleton::getTokoDb();
        $slave = new slave;
        $cat = new catalogue;
        $id = 0;
        $code = $cat->clearArticle($slave->qq($code));
        $r = $db->query("SELECT `ART_ID` FROM `T2_ARTICLES` WHERE `ARTICLE_NR_SEARCH` = '$code' AND `BRAND_ID` = $brand_id LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $id = $db->result($r, 0, "ART_ID");
        }
        return $id;
    }

    function getBrandId($code)
    {
        $db = DbSingleton::getTokoDb();
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

    function getBrandName($id)
    {
        $db = DbSingleton::getTokoDb();
        $name = "";
        $r = $db->query("SELECT `BRAND_NAME` FROM `T2_BRANDS` WHERE `BRAND_ID` = $id LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $name = $db->result($r, 0, "BRAND_NAME");
        }
        return $name;
    }

    function getTpointName($id)
    {
        $db = DbSingleton::getDb();
        $name = "";
        $r = $db->query("SELECT `name` FROM `T_POINT` WHERE `id` = $id LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $name = $db->result($r, 0, "name");
        }
        return $name;
    }

    function getClientName($client_id)
    {
        $db = DbSingleton::getDb();
        $name = "";
        $r = $db->query("SELECT `name` FROM `A_CLIENTS` WHERE `id` = $client_id LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $name = $db->result($r, 0, "name");
        }
        return $name;
    }

    function getArticleDispl($art_id)
    {
        $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT `ARTICLE_NR_DISPL` FROM `T2_ARTICLES` WHERE `ART_ID` = $art_id LIMIT 1;");
        return $db->result($r, 0, "ARTICLE_NR_DISPL");
    }

    function getDpNote($dp_id)
    {
        $db = DbSingleton::getDb();
        $text = "";
        $r = $db->query("SELECT `text` FROM `J_DP_NOTE` WHERE `dp_id` = $dp_id LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n > 0) {
            $text = $db->result($r, 0, "text");
        }
        return $text;
    }

    function setDpNote($dp_id, $text)
    {
        $db = DbSingleton::getDb();
        session_start();
        $user_id = $_SESSION["media_user_id"] + 0;
        $answer = 0;
        $err = "Помилка збереження даних!";
        if ($dp_id > 0) {
            $r = $db->query("SELECT `text` FROM `J_DP_NOTE` WHERE `dp_id` = $dp_id LIMIT 1;");
            $n = $db->num_rows($r);
            if ($n > 0) {
                $db->query("UPDATE `J_DP_NOTE` SET `text` = '$text', `client` = $user_id WHERE `dp_id` = $dp_id;");
            } else {
                $db->query("INSERT INTO `J_DP_NOTE` (`dp_id`, `text`, `client`) VALUES ($dp_id, '$text', $user_id);");
            }
            $answer = 1;
            $err = "";
        }
        return array($answer, $err);
    }

    function dropDpNote($dp_id)
    {
        $db = DbSingleton::getDb();
        $answer = 0;
        $err = "Помилка видалення даних!";
        if ($dp_id > 0) {
            $db->query("DELETE FROM `J_DP_NOTE` WHERE `dp_id` = $dp_id;");
            $answer = 1;
            $err = "";
        }
        return array($answer, $err);
    }

    function getMediaUserName($user_id)
    {
        $db = DbSingleton::getDb();
        $name = "";
        $user_id = (int)$user_id;
        $r = $db->query("SELECT `name` FROM `media_users` WHERE `id` = $user_id LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $name = $db->result($r, 0, "name");
        }
        return $name;
    }

    function get_df_doc_nom_new()
    {
        $db = DbSingleton::getDb();
        $r = $db->query("SELECT MAX(`doc_nom`) as doc_nom FROM `J_DP` WHERE `oper_status` = 30 AND `status` = 1 LIMIT 1;");
        return 0 + $db->result($r, 0, "doc_nom") + 1;
    }

    function getDpName($dp_id)
    {
        $db = DbSingleton::getDb();
        $r = $db->query("SELECT `prefix`, `doc_nom` FROM `J_DP` WHERE `id` = $dp_id LIMIT 1;");
        $prefix = $db->result($r, 0, "prefix");
        $doc_nom = $db->result($r, 0, "doc_nom");
        return $prefix . "-" . $doc_nom;
    }

    function getDpClientName($dp_id)
    {
        $db = DbSingleton::getDb();
        $client_name = "";
        $r = $db->query("SELECT `client_id` FROM `J_DP` WHERE `id` = $dp_id LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $client_name = $this->getClientName($db->result($r, 0, "client_id"));
        }
        return $client_name;
    }

    function getDpClientId($dp_id)
    {
        $db = DbSingleton::getDb();
        $client_id = 0;
        $r = $db->query("SELECT `client_id` FROM `J_DP` WHERE `id` = $dp_id LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $client_id = $db->result($r, 0, "client_id");
        }
        return $client_id;
    }

    function getTpointAddress($tpoint_id)
    {
        $db = DbSingleton::getDb();
        $address = "";
        $r = $db->query("SELECT `full_name`, `address` FROM `T_POINT` WHERE `id` = $tpoint_id LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $address = $db->result($r, 0, "full_name") . " " . $db->result($r, 0, "address");
        }
        return $address;
    }

    /*
     * Journal DP FILTERS
     * */
    function getDpListFilter($key)
    {
        $db = DbSingleton::getDb();
        $list = "";

        if ($key === "tpoint") {
            $r = $db->query("SELECT `id`, `full_name` FROM `T_POINT` WHERE `status` = 1;");
            $n = $db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $id = $db->result($r, $i - 1, "id");
                $name = $db->result($r, $i - 1, "full_name");
                $list .= "<option value='$id'>$name</option>";
            }
        }

        if ($key === "user") {
            $r = $db->query("SELECT `id`, `name` FROM `media_users` WHERE 1;");
            $n = $db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $id = $db->result($r, $i - 1, "id");
                $name = $db->result($r, $i - 1, "name");
                $list .= "<option value='$id'>$name</option>";
            }
        }

        if ($key === "status") {
            $r = $db->query("SELECT `id`, `mcaption` FROM `manual` WHERE `key` = 'status_dp' OR `key` = 'status_dps';");
            $n = $db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $id = $db->result($r, $i - 1, "id");
                $name = $db->result($r, $i - 1, "mcaption");
                if ($id != 80 && $id != 93) {
                    $list .= "<option value='$id'>$name</option>";
                }
            }
        }

        if ($key === "client_type") {
            $r = $db->query("SELECT `id`, `mcaption` FROM `manual` WHERE `key` = 'customers_categories';");
            $n = $db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $id = $db->result($r, $i - 1, "id");
                $name = $db->result($r, $i - 1, "mcaption");
                $list .= "<option value='$id'>$name</option>";
            }
        }

        return $list;
    }

    function newDpCard()
    {
        $db = DbSingleton::getDb();
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $r = $db->query("SELECT MAX(`id`) as mid FROM `J_DP`;");
        $dp_id = 0 + $db->result($r, 0, "mid") + 1;
        $doc_nom = $this->get_df_doc_nom_new();
        $db->query("INSERT INTO `J_DP` (`id`, `prefix`, `doc_nom`, `user_id`, `data`) VALUES ($dp_id, '$this->prefix_new', '$doc_nom', '$user_id', CURDATE());");
        return $dp_id;
    }

    function newDpFromDp($from_dp_id, $tpoint_id, $dp_note = "")
    {
        $db = DbSingleton::getDb();
        $dp_id = 0;
        $r = $db->query("SELECT * FROM `J_DP` WHERE `id` = $from_dp_id LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $doc_type_id = $db->result($r, 0, "doc_type_id");
            $client_id = $db->result($r, 0, "client_id");
            $client_conto_id = $db->result($r, 0, "client_conto_id");
            $data_pay = $db->result($r, 0, "data_pay");
            $cash_id = $db->result($r, 0, "cash_id");
            $usd_to_uah = $db->result($r, 0, "usd_to_uah");
            $eur_to_uah = $db->result($r, 0, "eur_to_uah");
            $vat_use = $db->result($r, 0, "vat_use");
            $delivery_type_id = $db->result($r, 0, "delivery_type_id");
            $carrier_id = $db->result($r, 0, "carrier_id");
            $dp_user_id = $db->result($r, 0, "user_id");

            $r = $db->query("SELECT MAX(`id`) as mid FROM `J_DP`;");
            $dp_id = 0 + $db->result($r, 0, "mid") + 1;
            $doc_nom = $this->get_df_doc_nom_new();
            $db->query("INSERT INTO `J_DP` (`id`, `prefix`, `doc_nom`, `user_id`, `data`, `doc_type_id`, `tpoint_id`, `client_id`, `client_conto_id`, `data_pay`, `cash_id`, `usd_to_uah`, `eur_to_uah`, `vat_use`, `delivery_type_id`, `carrier_id`, `delivery_address`) 
            VALUES ($dp_id, '$this->prefix_new', '$doc_nom', '$dp_user_id', CURDATE(), '$doc_type_id', '$tpoint_id', '$client_id', '$client_conto_id', '$data_pay', '$cash_id', '$usd_to_uah', '$eur_to_uah', '$vat_use', '$delivery_type_id', '$carrier_id', '');");

            $delivery_note = $this->getDpNote($from_dp_id);
            $delivery_note = ($delivery_note === "") ? $dp_note : $delivery_note;
            if ($delivery_note !== "") {
                $this->setDpNote($dp_id, $delivery_note);
            }

            $r1 = $db->query("SELECT `id` FROM `orders_new` WHERE `dp_id` = '$from_dp_id' LIMIT 1;");
            $n1 = $db->num_rows($r1);
            if ($n1 == 1) {
                $order_id = $db->result($r1, 0, "id");
                if ($order_id > 0) {
                    $db->query("UPDATE `orders_new` SET `dp_id` = '$from_dp_id,$dp_id' WHERE `id` = $order_id;");
                }
            }
        }

        return $dp_id;
    }

    function getStatusDP($dp_id)
    {
        $db = DbSingleton::getDb();
        $k = 0;
        $r = $db->query("SELECT `status_dps` FROM `J_DP_STR` WHERE `dp_id` = $dp_id;");
        $n = $db->num_rows($r);
        if ($n > 0) {
            for ($i = 1; $i <= $n; $i++) {
                $status_dps = (int)$db->result($r, $i - 1, "status_dps");
                if ($status_dps === 96) {
                    $k++;
                }
            }
        }

        return ($k > 0) ? "lightgreen" : "lightyellow";
    }

    function getDpAccessStatus($media_user_id)
    {
        $db = DbSingleton::getDb();
        $r = $db->query("SELECT `access_dp` FROM `media_users` WHERE `id` = $media_user_id LIMIT 1;");
        return $db->result($r, 0, "access_dp");
    }

    function getDpPauseAccessStatus($media_user_id)
    {
        $db = DbSingleton::getDb();
        $r = $db->query("SELECT `access_dp_pause` FROM `media_users` WHERE `id` = $media_user_id LIMIT 1;");
        return $db->result($r, 0, "access_dp_pause");
    }

    function setDpPauseAccessStatus($media_user_id, $access_dp_pause)
    {
        $db = DbSingleton::getDb();
        $access_dp_pause = (int)!$access_dp_pause;
        $db->query("UPDATE `media_users` SET `access_dp_pause` = $access_dp_pause WHERE `id` = $media_user_id LIMIT 1");
        return $access_dp_pause;
    }

    function show_dp_list()
    {
        $db = DbSingleton::getDb();
        $gmanual = new gmanual;
        $list = "";
        session_start();
        $ses_tpoint_id = $_SESSION["media_tpoint_id"];
        $media_user_id = $_SESSION["media_user_id"];
        $media_role_id = $_SESSION["media_role_id"];
        $where = " AND (j.tpoint_id = '$ses_tpoint_id' OR j.user_id = '$media_user_id') AND ((j.status_dp != 0 AND j.summ > 0) OR (j.status_dp = 81)) ";
        $limit = "LIMIT 0,500";
        $where_status = " AND j.status_dp != 81 ";
        if ($media_user_id == 1) {
            $where = " AND j.status_dp != 0";
            $limit = "";
        }
        if ($media_role_id == 1) {
            $where = " AND ((j.status_dp != 0 and j.summ > 0) OR (j.status_dp = 81)) ";
            $limit = "";
        }
        $r = $db->query("SELECT j.*, t.name as tpoint_name, dt.mvalue as doc_type_name, CASH.name as cash_name, c.name as client_name, dlv.mcaption as delivery_type_name 
        FROM `J_DP` j
            LEFT OUTER JOIN `manual` dt ON (dt.key = 'client_sale_type' AND dt.id = j.doc_type_id)
            LEFT OUTER JOIN `T_POINT` t ON (t.id = j.tpoint_id)
            LEFT OUTER JOIN `A_CLIENTS` c ON (c.id = j.client_conto_id)
            LEFT OUTER JOIN `CASH` ON (CASH.id = j.cash_id)
            LEFT OUTER JOIN `manual` dlv ON (dlv.key = 'delivery_type' AND dlv.id = j.delivery_type_id)
        WHERE j.status = 1 $where $where_status 
        ORDER BY j.id DESC $limit;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r, $i - 1, "id");
            $order_info_id = $db->result($r, $i - 1, "order_info_id");
            $del_info = $this->getOrderDeliveryInfo($order_info_id);
            $doc_type_name = $db->result($r, $i - 1, "doc_type_name");
            $dp_note = $this->getDpNote($id);
            $doc_nom = $db->result($r, $i - 1, "doc_nom");
            $tpoint_name = $db->result($r, $i - 1, "tpoint_name");
            $client_id = $db->result($r, $i - 1, "client_conto_id");
            $client_name = $db->result($r, $i - 1, "client_name");
            $cash_name = $db->result($r, $i - 1, "cash_name");
            $summ = $db->result($r, $i - 1, "summ");
            $data = $db->result($r, $i - 1, "data");
            $user_name = $this->getMediaUserName($db->result($r, $i - 1, "user_id"));
            $status_dp = $db->result($r, $i - 1, "status_dp");
            $status_processed = $db->result($r, $i - 1, "status_processed");
            $status_dp_name = ($status_dp !== 80 && $status_dp !== "80") ? $gmanual->get_gmanual_caption($status_dp) : $this->getDpStrStatus($id);
            $dp_note_cap = ($dp_note === "") ? "<i class='fa fa-minus'></i>" : "<i class='fa fa-plus' title='$dp_note'></i>";

            if ($this->getDpAccessStatus($media_user_id)) {
                $dp_note_cap = $dp_note;
            }

            $clr = "";
            if ($status_dp == 79) {
                $clr = "pink";
                if ($status_processed == 1) {
                    $clr = "white";
                }
            }
            if ($status_dp == 80) {
                $clr = $this->getStatusDP($id);
            }

            $client_image = $this->getClientTypeImage($client_id);

            $list .= "<tr style='cursor: pointer; background: $clr;' onClick='showDpCard(\"$id\")'>
                <td>$data</td>
                <td>$tpoint_name</td>
                <td>$doc_type_name</td>
                <td>$doc_nom</td>
                <td>$client_image $client_name</td>
                <td>$summ $cash_name</td>
                <td>$dp_note_cap</td>
                <td>$del_info</td>
                <td>$user_name</td>
                <td title='$status_dp'>$status_dp_name</td>
            </tr>";
        }

        return $list;
    }

    function show_dp_list_filter($status, $filstatus, $filauthor, $filtpoint, $filclienttype, $fildpname)
    {
        $db = DbSingleton::getDb();
        $gmanual = new gmanual;
        $list = "";
        session_start();
        $ses_tpoint_id = $_SESSION["media_tpoint_id"];
        $media_user_id = $_SESSION["media_user_id"];
        $media_role_id = $_SESSION["media_role_id"];
        $where = " AND (j.tpoint_id = '$ses_tpoint_id' OR j.user_id = '$media_user_id') AND j.status_dp != 0 AND j.summ > 0 ";
        $where_status = (!$status) ? " AND j.status_dp != 81 " : "";
        $where_filter = "";

        if ($media_user_id == 1) {
            $where = " AND j.status_dp != 0";
        }
        if ($media_role_id == 1) {
            $where = " AND j.status_dp != 0 AND j.summ > 0 ";
        }
        if ($filstatus != "0") {
            if ($filstatus > "81") {
                $where_filter .= " AND jstr.status_dps = '$filstatus'";
            } else {
                $where_filter .= " AND j.status_dp = '$filstatus'";
            }
        }
        if ($filauthor != "0") {
            $where_filter .= " AND j.user_id = '$filauthor'";
        }
        if ($filtpoint != "0") {
            $where_filter .= " AND j.tpoint_id = '$filtpoint'";
        }
        if ($filclienttype != "0") {
            $where_filter .= " AND c.client_category = '$filclienttype'";
        }
        if ($fildpname !== "") {
            $where_filter .= " AND j.`doc_nom` LIKE '%$fildpname%'";
        }

        $r = $db->query("SELECT j.*, t.name as tpoint_name, dt.mvalue as doc_type_name, CASH.name as cash_name, c.name as client_name, dlv.mcaption as delivery_type_name 
        FROM `J_DP` j
            LEFT OUTER JOIN `J_DP_STR` jstr ON (jstr.dp_id = j.id) 
            LEFT OUTER JOIN `manual` dt ON (dt.key = 'client_sale_type' AND dt.id = j.doc_type_id)
            LEFT OUTER JOIN `T_POINT` t ON (t.id = j.tpoint_id)
            LEFT OUTER JOIN `A_CLIENTS` c ON (c.id = j.client_conto_id)
            LEFT OUTER JOIN `CASH` ON (CASH.id = j.cash_id)
            LEFT OUTER JOIN `manual` dlv ON (dlv.key = 'delivery_type' AND dlv.id = j.delivery_type_id)
        WHERE j.status = 1 $where $where_status $where_filter 
        GROUP BY j.id 
        ORDER BY j.id DESC 
        LIMIT 0,500;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r, $i - 1, "id");
            $del_info = $this->getOrderDeliveryInfo($db->result($r, $i - 1, "order_info_id"));
            $doc_type_name = $db->result($r, $i - 1, "doc_type_name");
            $dp_note = $this->getDpNote($id);
            $doc_nom = $db->result($r, $i - 1, "doc_nom");
            $tpoint_name = $db->result($r, $i - 1, "tpoint_name");
            $client_id = $db->result($r, $i - 1, "client_conto_id");
            $client_image = $this->getClientTypeImage($client_id);
            $client_name = $db->result($r, $i - 1, "client_name");
            $cash_name = $db->result($r, $i - 1, "cash_name");
            $summ = $db->result($r, $i - 1, "summ");
            $data = $db->result($r, $i - 1, "data");
            $user_name = $this->getMediaUserName($db->result($r, $i - 1, "user_id"));
            $status_dp = $db->result($r, $i - 1, "status_dp");
            $status_dp_name = ($status_dp != 80) ? $gmanual->get_gmanual_caption($status_dp) : $this->getDpStrStatus($id);
            $dp_note_cap = ($dp_note === "") ? "<i class='fa fa-minus'></i>" : "<i class='fa fa-plus' title='$dp_note'></i>";
            if ($this->getDpAccessStatus($media_user_id)) {
                $dp_note_cap = $dp_note;
            }

            $clr = "";
            if ($status_dp == 79) {
                $clr = "pink";
            }
            if ($status_dp == 80) {
                $clr = $this->getStatusDP($id);
            }
            if ($status_dp == 81) {
                $clr = "";
            }

            $list .= "<tr style='cursor: pointer; background: $clr;' onClick='showDpCard(\"$id\")'>
                <td>$data</td>
                <td>$tpoint_name</td>
                <td>$doc_type_name</td>
                <td>$doc_nom</td>
                <td>$client_image $client_name</td>
                <td>$summ $cash_name</td>
                <td>$dp_note_cap</td>
                <td>$del_info</td>
                <td>$user_name</td>
                <td title='$status_dp'>$status_dp_name</td>
            </tr>";
        }

        return $list;
    }

    /*
     * Тип клієнта (зображення)
     * */
    function getClientTypeImage($client_id)
    {
        $db = DbSingleton::getDb();
        $client = new clients;
        $image = $image_name = "";
        $r = $db->query("SELECT `client_category` FROM `A_CLIENTS` WHERE `id` = $client_id LIMIT 1;");
        $client_category = $db->result($r, 0, "client_category");

        switch ($client_category) {
            case 140:
            {
                $image_name = "retail.png";
                break;
            }
            case 141:
            {
                $image_name = "shop.png";
                break;
            }
            case 142:
            {
                $image_name = "internet_shop.png";
                break;
            }
            case 143:
            {
                $image_name = "service_station.png";
                break;
            }
            case 144:
            {
                $image_name = "service_station_and_shop.png";
                break;
            }
        }

        if ($client_category > 0) {
            $client_category_name = $client->getManualName($client_category);
            $image = "<img src='/images/journal_dp/$image_name' alt='$client_category_name' width='32' height='32' style='margin-right: 10px;'>";
        }

        return $image;
    }

    function getKoursData()
    {
        $db = DbSingleton::getDb();
        $slave = new slave;
        $usd_to_uah = 0;
        $eur_to_uah = 0;
        $r = $db->query("SELECT `kours_value` FROM `J_KOURS` WHERE `cash_id` = '2' AND `in_use` = '1' ORDER BY `id` DESC LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $usd_to_uah = $slave->to_money(round($db->result($r, 0, "kours_value"), 2));
        }
        $r = $db->query("SELECT `kours_value` FROM `J_KOURS` WHERE `cash_id` = '3' AND `in_use` = '1' ORDER BY `id` DESC LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $eur_to_uah = $slave->to_money(round($db->result($r, 0, "kours_value"), 2));
        }

        return array($usd_to_uah, $eur_to_uah);
    }

    /*
     * show DP card
     * */
    function showDpCard($dp_id)
    {
        $db = DbSingleton::getDb();
        $client = new clients;
        $gmanual = new gmanual;
        $doc_nom = 0;
        $prefix = $comment = $cells_show = $label_art_unknown = "";
        session_start();
        $user_id = $_SESSION["media_user_id"];

        $form = "";
        $form_htm = RD . "/tpl/dp_card.htm";
        if (file_exists($form_htm)) {
            $form = file_get_contents($form_htm);
        }
        $r = $db->query("SELECT * FROM `J_DP` WHERE `id` = $dp_id LIMIT 1;");
        $n = $db->num_rows($r);

        if ($n == 0) {
            $form_htm = RD . "/tpl/access_deny.htm";
            if (file_exists($form_htm)) {
                $form = file_get_contents($form_htm);
            }
        }

        if ($n == 1) {
            $this->updateDpSumm($dp_id);
            $doc_type_id = $db->result($r, 0, "doc_type_id");
            $prefix = $db->result($r, 0, "prefix");
            $doc_nom = $db->result($r, 0, "doc_nom");
            $user_use = $db->result($r, 0, "user_use");

            if ($user_id != $user_use && $user_use > 0) {
                $form_htm = RD . "/tpl/dp_use_deny.htm";
                if (file_exists($form_htm)) {
                    $form = file_get_contents($form_htm);
                }
                $form = str_replace("{user_name}", $this->getMediaUserName($user_use), $form);
                $admin_unlock = "";
                if (in_array($user_id, [1, 2, 7], true)) {
                    $admin_unlock = "<button class='btn btn-sm btn-warning' onClick='unlockDpCard(\"$dp_id\");'><i class='fa fa-unlock'></i> Розблокувати</button>";
                }
                $form = str_replace("{admin_unlock}", $admin_unlock, $form);
            }

            if ($user_id == $user_use || $user_use == 0) {
                $data               = $db->result($r, 0, "data");
                $data               = ($data == "0000-00-00") ? "" : $data;
                $cash_id            = $db->result($r, 0, "cash_id");
                $data_pay           = $db->result($r, 0, "data_pay");
                $type_id            = $db->result($r, 0, "type_id");
                $tpoint_id          = $db->result($r, 0, "tpoint_id");
                $client_id          = $db->result($r, 0, "client_id");
                $client_conto_id    = $db->result($r, 0, "client_conto_id");
                $usd_to_uah         = $db->result($r, 0, "usd_to_uah");
                $eur_to_uah         = $db->result($r, 0, "eur_to_uah");
                $summ               = $db->result($r, 0, "summ");
                $delivery_type_id   = $db->result($r, 0, "delivery_type_id");
                $carrier_id         = $db->result($r, 0, "carrier_id");
                $volume             = $db->result($r, 0, "volume");
                $weight             = $db->result($r, 0, "weight");
                $status_dp          = $db->result($r, 0, "status_dp");
                $order_info_id      = $db->result($r, 0, "order_info_id");
                $status_processed   = $db->result($r, 0, "status_processed");

                $form = $this->getOrderInfoBlock($form, $order_info_id, $client_conto_id);

                [$usd_to_uah_new, $eur_to_uah_new] = $this->getKoursData();
                if ($status_dp == 79) {
                    $usd_to_uah = $usd_to_uah_new;
                    $eur_to_uah = $eur_to_uah_new;
                } else {
                    if ($usd_to_uah == 0) {
                        $usd_to_uah = $usd_to_uah_new;
                    }
                    if ($eur_to_uah == 0) {
                        $eur_to_uah = $eur_to_uah_new;
                    }
                }
                if ($status_dp == 80) {
                    $form = str_replace("{hide_new_row_button}", " disabled style=\"visibility:hidden;\"", $form);
                    $form = str_replace("{oper_disabled}", " disabled", $form);
                    $form = str_replace("{oper_disabled2}", " ", $form);

                    $media_role_id = $_SESSION["media_role_id"];
                    if ($media_role_id == 1 || $media_role_id == 7) {
                        $form = str_replace("{oper_disabled3}", " ", $form);
                    }
                }
                if ($status_dp < 82) {
                    [, , $data_pay] = $this->getClientPaymentDelay($client_conto_id);
                }

                $form = str_replace("{oper_disabled}", "", $form);
                $form = str_replace("{oper_disabled2}", "disabled", $form);
                $form = str_replace("{oper_disabled3}", "disabled", $form);
                $form = str_replace("{hide_new_row_button}", "", $form);
                $form = str_replace("{dp_id}", $dp_id, $form);
                $form = str_replace("{data_pay}", $data_pay, $form);
                $form = str_replace("{doc_type_list}", $this->getDocTypeSelectList($doc_type_id), $form);
                $form = str_replace("{dp_storage_list}", $this->getDpStorageList(), $form);

                if ($client_id != $client_conto_id && $client_conto_id != 0) {
                    $balans_display = "";
                    [$balans_conto, $cash_conto_abr] = $client->getClientGeneralSaldo($client_conto_id);
                    $balans_conto_style = ($balans_conto >= 0) ? "info" : "danger";
                } else {
                    $balans_display = "hide";
                    $balans_conto = "";
                    $cash_conto_abr = "";
                    $balans_conto_style = "";
                }

                $form = str_replace("{delivery_user_saved}", $this->getDeliveryUserSaved($client_id, $dp_id), $form);

                [$balans_client, $cash_abr] = $client->getClientGeneralSaldo($client_id);
                $form = str_replace("{balans_client}", "$balans_client $cash_abr", $form);
                $form = str_replace("{balans_style}", ($balans_client >= 0) ? "info" : "danger", $form);
                $form = str_replace("{balans_conto}", "$balans_conto $cash_conto_abr", $form);
                $form = str_replace("{balans_display}", $balans_display, $form);
                $form = str_replace("{balans_conto_style}", $balans_conto_style, $form);
                $form = str_replace("{cash_id}", $cash_id, $form);
                $form = str_replace("{cash_list}", $this->showCashListSelect($cash_id), $form);
                $form = str_replace("{tpoint_id}", $tpoint_id, $form);
                $form = str_replace("{tpoint_name}", $this->getTpointName($tpoint_id), $form);
                $form = str_replace("{client_id}", $client_id, $form);
                $form = str_replace("{client_name}", $this->getClientName($client_id), $form);
                $form = str_replace("{client_conto_id}", $client_conto_id, $form);
                $form = str_replace("{client_conto_list}", $this->getClientContoSelectList($client_id, $client_conto_id), $form);
                $form = str_replace("{dp_summ}", $summ, $form);
                $form = str_replace("{usd_to_uah}", $usd_to_uah, $form);
                $form = str_replace("{eur_to_uah}", $eur_to_uah, $form);
                $form = str_replace("{delivery_type_list}", $gmanual->showGmanualSelectList("delivery_type", $delivery_type_id), $form);
                $form = str_replace("{carrier_dis}", ($delivery_type_id == 60) ? "" : "disabled hidden", $form);
                $form = str_replace("{dp_note}", $this->getDpNote($dp_id), $form);
                $form = str_replace("{carrier_list}", $this->getCarrierSelectList($carrier_id), $form);
                $form = str_replace("{delivery_short_data}", $this->getDpUserDelivery($dp_id), $form);
                $form = str_replace("{data}", $data, $form);
                $form = str_replace("{type_id}", $type_id, $form);
                $form = str_replace("{weight}", $weight, $form);
                $form = str_replace("{volume}", $volume, $form);
                $form = str_replace("{comment}", $comment, $form);
                $form = str_replace("{cells_show}", $cells_show, $form);
                $form = str_replace("{ses_user_discount}", $_SESSION["user_discount"], $form);
                $form = str_replace("{oper_disabled2}", ($client_id == 875) ? " disabled" : "", $form); // СПИСАНИЕ
                $form = str_replace("{dpLocalListCount}", $this->dpStorselCount($dp_id), $form);
                $form = str_replace("{dpRemoteListCount}", $this->dpJmovingCount($dp_id), $form);

                [$dpChildsList, $kol_str_row] = $this->showDpStrList($dp_id, $status_dp, $client_id, $cash_id, $usd_to_uah, $eur_to_uah);
                $form = str_replace("{dpChildsList}", $dpChildsList, $form);
                $form = str_replace("{kol_str_row}", $kol_str_row, $form);
                $form = str_replace("{weight}", $weight, $form);
                $form = str_replace("{volume}", $volume, $form);
                $form = str_replace("{storage_to_disabled}", ($status_dp == 79) ? "" : " disabled", $form);
                $form = str_replace("{my_user_id}", $user_id, $form);
                $form = str_replace("{my_user_name}", $this->getMediaUserName($user_id), $form);
                $form = str_replace("{labelCommentsCount}", $this->labelCommentsCount($dp_id)[1], $form);
                $form = str_replace("{labelUnknownsCount}", $this->labelUnknownsCount($dp_id), $form);
                $form = str_replace("{labelArticlesUnKnownCount}", $label_art_unknown, $form);
                $form = str_replace("{oper_visible}", ($delivery_type_id == "61") ? "" : " disabled style=\"display:none;\"", $form);
                $form = str_replace("{oper_visible4}", ($delivery_type_id == "118" || $status_dp == "80" || $status_dp == "79") ? "" : " disabled style=\"display:none;\"", $form);
                $form = str_replace("{site_order_label}", $this->getDistinctOrdersItems($dp_id), $form);

                if ($status_dp == 79) {
                    if ($status_processed == 1) {
                        $processed_checked = "checked";
                    } else {
                        $processed_checked = "";
                    }

                    $processed_label = "<label class='col-lg-3 control-label' for='processed_card'>Обробка</label>
                        <div class='col-lg-2' style='padding: 0;'>
                            <input title='Статус' type='checkbox' class='js-switch' data-size='small' id='processed_card' value='1' $processed_checked />
                        </div>";
                } else {
                    $processed_label = "";
                }

                $form = str_replace("{processed_label}", $processed_label, $form);
                $this->setdpCardUserAccess($dp_id, $user_id);
            }
        }

        return array($form, $prefix . "-" . $doc_nom, $this->labelUnknownsCount($dp_id));
    }

    function getClientWebUsers($client_id)
    {
        $users = [];
        $db = DbSingleton::getDb();
        $r = $db->query("SELECT `id` FROM `A_CLIENTS_USERS` WHERE `client_id` = $client_id;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $user_id = $db->result($r, $i - 1, "id");
            $users[] = $user_id;
        }
        return $users;
    }

    function getDeliveryUserSaved($client_id, $dp_id)
    {
        $client = new clients();
        $list = "";
        $db = DbSingleton::getDb();
        $users = $this->getClientWebUsers($client_id);
        if (!empty($users)) {
            $users_str = implode(",", $users);
            $r = $db->query("SELECT * FROM `ORDERS_CLIENT_INFO` WHERE `CLIENT_ID` = $client_id AND `USER_ID` IN ($users_str) AND `STATUS` = 1;");
            $n = $db->num_rows($r);
            if ($n > 0) {
                $r2 = $db->query("SELECT `order_info_id` FROM `J_DP` WHERE `id` = $dp_id LIMIT 1;");
                $order_info_id_sel = $db->result($r2, 0, "order_info_id");

                $list .= "<ul>";
                for ($i = 1; $i <= $n; $i++) {
                    $id                 = $db->result($r, $i - 1, "ID");
                    $user_id            = $db->result($r, $i - 1, "USER_ID");
                    $city_id            = $db->result($r, $i - 1, "CITY_ID");
                    $delivery_id        = $db->result($r, $i - 1, "DELIVERY_ID");
                    $payment_id         = $db->result($r, $i - 1, "PAYMENT_ID");
                    $street             = $db->result($r, $i - 1, "DEL_STREET");
                    $house              = $db->result($r, $i - 1, "DEL_HOUSE");
                    $porch              = $db->result($r, $i - 1, "DEL_PORCH");
                    $department_text    = $db->result($r, $i - 1, "DEL_DEPARTMENT_TEXT");
                    $express            = $db->result($r, $i - 1, "DEL_EXPRESS");
                    $express_info       = $db->result($r, $i - 1, "DEL_EXPRESS_INFO");
                    $express_payment    = $db->result($r, $i - 1, "DEL_EXPRESS_PAYMENT");
                    $user_name          = $client->getUserNameById($user_id);
                    $city_name          = $this->getCityName($city_id);
                    $delivery_text      = $this->getDeliveryCaption($delivery_id);
                    $payment_text       = $this->getPaymentCaption($payment_id);
                    $delivery_info      = $this->getDeliveryInfoCaption($delivery_id, $street, $house, $porch, $department_text, $express, $express_info, $express_payment);
                    $style              = ($id == $order_info_id_sel) ? "style='border: 1px solid #23c6c8; padding: 5px; margin: 10px 0;'" : "";

                    $list .= "
                    <li $style>
                        <a onclick='setClientOrderInfo(\"$id\")'>
                            $i. $user_name / $city_name / $delivery_text $delivery_info / $payment_text
                        </a>
                        <a onclick='dropClientOrderInfo(\"$id\")'>
                            <i class='fa fa-times'></i>
                        </a>
                    </li>";
                }
                $list .= "</ul>";
            }
        }

        return $this->replaceLang($list);
    }

    function dropClientOrderInfo($order_info_id, $dp_id)
    {
        $answer = 0; $err = "Помилка видалення!";
        if ($order_info_id > 0) {
            $db = DbSingleton::getDb();
            $db->query("DELETE FROM `ORDERS_CLIENT_INFO` WHERE `ID` = $order_info_id LIMIT 1;");
            $dp_id = (int)$dp_id;
            $db->query("UPDATE `J_DP` SET `order_info_id` = 0 WHERE `id` = $dp_id LIMIT 1;");
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

    public function setClientOrderInfo($order_info_id, $dp_id)
    {
        $client = new clients();
        $db = DbSingleton::getDb();
        $r = $db->query("SELECT * FROM `ORDERS_CLIENT_INFO` WHERE `ID` = $order_info_id AND `STATUS` = 1;");
        $n = $db->num_rows($r);
        if ($n > 0) {
            $dp_id = (int)$dp_id;
            $db->query("UPDATE `J_DP` SET `order_info_id` = $order_info_id WHERE `id` = $dp_id LIMIT 1;");
        }
        $user_id            = $db->result($r, 0, "USER_ID");
        $city_id            = $db->result($r, 0, "CITY_ID");
        $delivery_id        = $db->result($r, 0, "DELIVERY_ID");
        $payment_id         = $db->result($r, 0, "PAYMENT_ID");
        $delivery_charge_id = $db->result($r, 0, "DELIVERY_CHARGE_ID");
        $street             = $db->result($r, 0, "DEL_STREET");
        $house              = $db->result($r, 0, "DEL_HOUSE");
        $porch              = $db->result($r, 0, "DEL_PORCH");
        $department         = $db->result($r, 0, "DEL_DEPARTMENT");
        $express            = $db->result($r, 0, "DEL_EXPRESS");
        $express_info       = $db->result($r, 0, "DEL_EXPRESS_INFO");
        $express_payment    = $db->result($r, 0, "DEL_EXPRESS_PAYMENT");
        $recipient_name     = $db->result($r, 0, "DEL_NAME");
        $recipient_name     = ($recipient_name !== "") ? $recipient_name : $client->getUserNameById($user_id);
        $recipient_phone    = $db->result($r, 0, "DEL_PHONE");
        $recipient_phone    = ($recipient_phone !== "" && $recipient_phone > 0) ? $recipient_phone : $client->getUserNameById($user_id, "phone");
        $delivery_info      = [
            "street"            => $street,
            "house"             => $house,
            "porch"             => $porch,
            "department"        => $department,
            "express"           => $express,
            "express_info"      => $express_info,
            "express_payment"   => $express_payment
        ];

        return
            array(
                "user_id"               => $user_id,
                "city_id"               => $city_id,
                "delivery_id"           => $delivery_id,
                "payment_id"            => $payment_id,
                "delivery_charge_id"    => $delivery_charge_id,
                "delivery_info"         => $delivery_info,
                "recipient_name"        => $recipient_name,
                "recipient_phone"       => $recipient_phone
            );
    }

    /*
     * get delivery info captions
     * */
    public function getDeliveryInfoCaption($delivery_id, $street, $house, $porch, $department_text, $express, $express_info, $express_payment)
    {
        $info = "";
        switch ($delivery_id) {
            case 3:
            {
                $info = "{address_cap}: {street_cap} $street, {house_cap} $house";
                break;
            }
            case 2:
            case 5:
            {
                if ($porch !== "") {
                    $porch = ", {entrance_cap} $porch";
                }
                $info = "{address_cap}: {street_cap} $street, {house_cap} $house $porch";
                break;
            }
            case 4:
            case 6:
            {
                $info = $department_text;
                break;
            }
            case 7:
            {
                $delivery_express_text = $this->getDepartmentExpressName($express);
                $info = "{delivery_type_7}: $delivery_express_text, {department_cap}: $express_info";
                break;
            }
            case 1:
            default:
            {
                break;
            }
        }
        if ($express_payment !== "") {
            $info .= ". {delivery_type_payment}: $express_payment";
        }

        return $info;
    }

    /*
     * get language name
     * from code value
     * */
    public function getLanguageName($code)
    {
        $db = DbSingleton::getTokoDb();
        if (self::$langNames === null) {
            $r = $db->query("SELECT l.caption, lw.variable 
            FROM `new_lang_wdv` l
                LEFT OUTER JOIN `new_lang_wd` lw ON (lw.id = l.wd)
            WHERE l.lang_id = 1;");
            $result = mysqli_fetch_all($r, MYSQLI_ASSOC);
            self::$langNames = array_column($result, 'caption', 'variable');
        }
        return self::$langNames[$code];
    }

    /*
     * replace language text
     * */
    public function replaceLang($cont)
    {
        $db = DbSingleton::getTokoDb();
        if (self::$langVariables === null) {
            $r = $db->query("SELECT `variable` FROM `new_lang_wd`;");
            self::$langVariables = array_column(mysqli_fetch_all($r), 0);
        }
        foreach (self::$langVariables as $langVariable) {
            $cont = str_replace("{" . $langVariable . "}", $this->getLanguageName($langVariable), $cont);
        }
        return $cont;
    }

    /*
     * get department express name
     * from DELIVERY_ID
     * */
    public function getDepartmentExpressName($delivery_id)
    {
        $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT `TEXT` FROM `T2_DELIVERY_EXPRESS` WHERE `ID` = $delivery_id LIMIT 1;");
        return $db->result($r, 0, "TEXT");
    }

    /*
     * get delivery caption
     * */
    public function getDeliveryCaption($delivery_id)
    {
        $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT `TEXT` FROM `T2_DELIVERY` WHERE `ID` = $delivery_id LIMIT 1;");
        return $db->result($r, 0, "TEXT");
    }

    /*
     * get payment caption
     * */
    public function getPaymentCaption($payment_id)
    {
        $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT `TEXT` FROM `T2_PAYMENT` WHERE `ID` = $payment_id LIMIT 1;");
        return $db->result($r, 0, "TEXT");
    }

    function labelUnknownsCount($dp_id) { $db = DbSingleton::getDb();
        $r = $db->query("SELECT COUNT(`id`) as count_ids FROM `J_DP_STR_UNKNOWN` WHERE `dp_id` = $dp_id;");
        $amount = $db->result($r, 0, "count_ids") + 0;
        if ($amount == 0) {
            $amount = "";
        }
        return $amount;
    }

    function getDpStorageList() { $db = DbSingleton::getDb();
        $list = "";
        $r = $db->query("SELECT `name` FROM `STORAGE`;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $name = $db->result($r, $i - 1, "name");
            $list .= "<option value='$i'>$name</option>";
        }
        return $list;
    }

    function unlockDpCard($dp_id) { $db = DbSingleton::getDb();
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $answer = 0;
        if ($user_id == 1 || $user_id == 2) {
            $db->query("UPDATE `J_DP` SET `user_use` = '0' WHERE `id` = $dp_id;");
            $answer = 1;
        }
        return $answer;
    }

    function closeDpCard($dp_id) {
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $this->unsetDpCardUserAccess($dp_id, $user_id);
        return 1;
    }

    function setDpCardUserAccess($dp_id, $user_id) { $db = DbSingleton::getDb();
        if ($dp_id > 0 && $user_id > 0) {
            $db->query("UPDATE `J_DP` SET `user_use` = '$user_id' WHERE `id` = $dp_id;");
        }
        return true;
    }

    function unsetDpCardUserAccess($dp_id, $user_id) { $db = DbSingleton::getDb();
        if ($dp_id > 0 && $user_id > 0) {
            $db->query("UPDATE `J_DP` SET `user_use` = '0' WHERE `id` = $dp_id;");
        }
        return true;
    }

    function clearDpStr($dp_id) { $db = DbSingleton::getDb(); $dbt = DbSingleton::getTokoDb();
        $slave = new slave;
        $answer = 0; $err = "";
        $dp_id = $slave->qq($dp_id);
        $r = $db->query("SELECT `oper_status` FROM `J_DP` WHERE `id` = $dp_id LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $oper_status = $db->result($r, 0, "oper_status");
            if ($oper_status == 30) {
                $r1 = $db->query("SELECT `id`, `status_dps`, `art_id`, `amount`, `storage_id_from` FROM `J_DP_STR` WHERE `dp_id` = $dp_id;");
                $n1 = $db->num_rows($r1);
                for ($i1 = 1; $i1 <= $n1; $i1++) {
                    $dp_str_id      = $db->result($r1, $i1 - 1, "id");
                    $status_dps_str = $db->result($r1, $i1 - 1, "status_dps");
                    if ($status_dps_str == 93) {
                        $art_id             = $db->result($r1, $i1 - 1, "art_id");
                        $amount             = $db->result($r1, $i1 - 1, "amount");
                        $storage_id_from    = $db->result($r1, $i1 - 1, "storage_id_from");
                        $rs = $dbt->query("SELECT `AMOUNT`, `RESERV_AMOUNT` FROM `T2_ARTICLES_STRORAGE` WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id_from LIMIT 1;");
                        $ns = $dbt->num_rows($rs);
                        if ($ns == 1) {
                            $reserv_amount_s    = $dbt->result($rs, 0, "RESERV_AMOUNT");
                            $amount_s           = $dbt->result($rs, 0, "AMOUNT");
                            $reserv_amount_s    -= $amount;
                            $amount_s           += $amount;
                            $dbt->query("UPDATE `T2_ARTICLES_STRORAGE` SET `AMOUNT` = '$amount_s', `RESERV_AMOUNT` = '$reserv_amount_s' WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id_from LIMIT 1;");
                            $db->query("DELETE FROM `J_DP_STR` WHERE `id` = $dp_str_id AND `dp_id` = $dp_id LIMIT 1;");
                            $this->updateDpWeightVolume($dp_id);
                            $this->updateDpSumm($dp_id);
                        }
                        $db->query("DELETE FROM `J_DP_STR` WHERE `id` = $dp_str_id AND `dp_id` = $dp_id LIMIT 1;");
                    }
                }
                $db->query("UPDATE `J_DP` SET `summ` = 0 WHERE `id` = $dp_id LIMIT 1;");
                $answer = 1; $err = "";
            } else {
                $answer = 0; $err = "Документ заблоковано. Зміни вносити заборонено.";
            }
        }

        return array($answer, $err);
    }

    function setDpClient($dp_id, $client_id) { $db = DbSingleton::getDb();
        $slave = new slave;
        $dp_id = $slave->qq($dp_id);
        $client_id = $slave->qq($client_id);
        $answer = 0; $err = "Помилка збереження даних!";
        if ($dp_id > 0 && $client_id > 0) {
            $db->query("UPDATE `J_DP` SET `client_id` = '$client_id', `client_conto_id` = '$client_id' WHERE `id` = $dp_id;");
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

    function showDpClientList($sel_id) { $db = DbSingleton::getDb();
        $list = "";
        $form = ""; $form_htm = RD . "/tpl/clients_parrent_tree.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }

        $r = $db->query("SELECT c.*, ot.name as org_type_name, t2cn.COUNTRY_NAME, t2st.STATE_NAME, t2rg.REGION_NAME, t2ct.CITY_NAME, acc.tpoint_id, tp.name as tpoint_name   
        FROM `A_CLIENTS` c 
            LEFT OUTER JOIN `A_ORG_TYPE` ot ON (ot.id = c.org_type) 
            LEFT OUTER JOIN `T2_COUNTRIES` t2cn ON (t2cn.COUNTRY_ID = c.country) 
            LEFT OUTER JOIN `T2_STATE` t2st ON (t2st.STATE_ID = c.state)
            LEFT OUTER JOIN `T2_REGION` t2rg ON (t2rg.REGION_ID = c.region)
            LEFT OUTER JOIN `T2_CITY` t2ct ON (t2ct.CITY_ID = c.city)
            LEFT OUTER JOIN `A_CLIENTS_CATEGORY` cc ON (cc.client_id = c.id)
            LEFT OUTER JOIN `A_CATEGORY` ac ON (ac.id = cc.category_id)
            LEFT OUTER JOIN `A_CLIENTS_CONDITIONS` acc ON (acc.client_id = c.id) 
            LEFT OUTER JOIN `T_POINT` tp ON (tp.id = acc.tpoint_id)
        WHERE c.status = 1 AND ac.id > 0 
        GROUP BY c.id;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id             = $db->result($r, $i - 1, "id");
            $name           = $db->result($r, $i - 1, "name");
            $org_type_name  = $db->result($r, $i - 1, "org_type_name");
            $email          = $db->result($r, $i - 1, "email");
            $phone          = $db->result($r, $i - 1, "phone");
            $country        = $db->result($r, $i - 1, "COUNTRY_NAME");
            $state          = $db->result($r, $i - 1, "STATE_NAME");
            $region         = $db->result($r, $i - 1, "REGION_NAME");
            $city           = $db->result($r, $i - 1, "CITY_NAME");
            $tpoint_id      = $db->result($r, $i - 1, "tpoint_id");
            $fname          = str_replace('"', "`", $name);
            $cur            = ($id == $sel_id) ? "background-color:#0CF;" : "";

            $list .= "
            <tr style='$cur cursor:pointer;' onClick='setDpClient(\"$id\", \"$fname\", \"$tpoint_id\", \"tpoint_name\")'>
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

        $form = str_replace("{list}", $list, $form);

        return $form;
    }

    function getDpTpointInfo($client_id) { $db = DbSingleton::getDb();
        $r = $db->query("SELECT c.*, acc.tpoint_id, tp.name as tpoint_name 
        FROM `A_CLIENTS` c 
            LEFT OUTER JOIN `A_CLIENTS_CONDITIONS` acc ON (acc.client_id = c.id)
            LEFT OUTER JOIN `T_POINT` tp ON (tp.id = acc.tpoint_id)
        WHERE c.id = '$client_id' 
        GROUP BY c.id LIMIT 1;");
        $name           = $db->result($r, 0, "name");
        $tpoint_id      = $db->result($r, 0, "tpoint_id");
        $tpoint_name    = $db->result($r, 0, "tpoint_name");
        $fname          = str_replace('"', "`", $name);

        return array($fname, $tpoint_id, $tpoint_name);
    }

    function filterDpClientsList($sel_id, $client_id, $client_name, $phone, $email, $state_id) { $db = DbSingleton::getDb();
        $where = "";
        if ($client_id > 0 && $client_id !== "") {$where .= " AND c.id='$client_id'";}
        if ($client_name !== "") {$where .= " AND c.name LIKE '%$client_name%'";}
        if ($phone !== "") {$where .= " AND c.phone LIKE '%$phone%'";}
        if ($email !== "") {$where .= " AND c.email LIKE '%$email%'";}
        if ($state_id > 0 && $state_id !== "") {$where .= " AND c.state='$state_id'";}

        $r = $db->query("SELECT c.*, ot.name as org_type_name, t2cn.COUNTRY_NAME, t2st.STATE_NAME, t2rg.REGION_NAME, t2ct.CITY_NAME, acc.tpoint_id, tp.name as tpoint_name   
        FROM `A_CLIENTS` c 
            LEFT OUTER JOIN `A_ORG_TYPE` ot ON ot.id=c.org_type 
            LEFT OUTER JOIN `T2_COUNTRIES` t2cn ON t2cn.COUNTRY_ID=c.country 
            LEFT OUTER JOIN `T2_STATE` t2st ON t2st.STATE_ID=c.state
            LEFT OUTER JOIN `T2_REGION` t2rg ON t2rg.REGION_ID=c.region
            LEFT OUTER JOIN `T2_CITY` t2ct ON t2ct.CITY_ID=c.city
            LEFT OUTER JOIN `A_CLIENTS_CATEGORY` cc ON cc.client_id=c.id
            LEFT OUTER JOIN `A_CATEGORY` ac ON ac.id=cc.category_id
            LEFT OUTER JOIN `A_CLIENTS_CONDITIONS` acc ON acc.client_id=c.id 
            LEFT OUTER JOIN `T_POINT` tp ON tp.id=acc.tpoint_id 
        WHERE c.status = 1 AND ac.id > 0 $where 
        GROUP BY c.id;");
        $n = $db->num_rows($r);
        $list = "n=$n";
        for ($i = 1; $i <= $n; $i++) {
            $id             = $db->result($r, $i - 1, "id");
            $name           = $db->result($r, $i - 1, "name");
            $org_type_name  = $db->result($r, $i - 1, "org_type_name");
            $client_email   = $db->result($r, $i - 1, "email");
            $client_phone   = $db->result($r, $i - 1, "phone");
            $country        = $db->result($r, $i - 1, "COUNTRY_NAME");
            $state          = $db->result($r, $i - 1, "STATE_NAME");
            $region         = $db->result($r, $i - 1, "REGION_NAME");
            $city           = $db->result($r, $i - 1, "CITY_NAME");
            $tpoint_id      = $db->result($r, $i - 1, "tpoint_id");
            $cur            = ($id == $sel_id) ? "background-color:#0CF;" : "";

            $list .= "
            <tr style='$cur cursor:pointer;' onClick='setDpClient(\"$id\", \"".base64_encode(iconv("windows-1251","utf-8",$name))."\", \"$tpoint_id\", \"tpoint_name\")'>
                <td></td>
                <td>$id</td>
                <td>$org_type_name</td>
                <td>$name</td>
                <td>$country</td>
                <td>$state</td>
                <td>$region</td>
                <td>$city</td>
                <td>$client_email</td>
                <td>$client_phone</td>
            </tr>";
        }

        return $list;
    }

    function unlinkDpClient($dp_id) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err =" Помилка збереження даних!";
        $dp_id = $slave->qq($dp_id);
        if ($dp_id > 0) {
            $db->query("UPDATE `J_DP` SET `client_id` = 0 WHERE `id` = $dp_id;");
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

    function unlinkDpTpoint($dp_id) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка збереження даних!";
        $dp_id = $slave->qq($dp_id);
        if ($dp_id > 0) {
            $db->query("UPDATE `J_DP` SET `tpoint_id` = 0 WHERE `id` = $dp_id;");
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

    function showDpTpointList($sel_id) { $db = DbSingleton::getDb();
        $list = "";
        $form = ""; $form_htm = RD . "/tpl/tpoint_tree.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $r = $db->query("SELECT t.`id`, t.`name`, t.`address`, t.`chief`, t2st.`STATE_NAME`, t2rg.`REGION_NAME`, t2ct.`CITY_NAME` 
        FROM `T_POINT` t 
            LEFT JOIN `T2_STATE` t2st ON (t2st.STATE_ID = t.state)
            LEFT JOIN `T2_REGION` t2rg ON (t2rg.REGION_ID = t.region)
            LEFT JOIN `T2_CITY` t2ct ON (t2ct.CITY_ID = t.city)
        WHERE t.status = 1;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id         = $db->result($r, $i - 1, "id");
            $name       = $db->result($r, $i - 1, "name");
            $address    = $db->result($r, $i - 1, "address");
            $user_id    = $db->result($r, $i - 1, "chief");
            $state      = $db->result($r, $i - 1, "STATE_NAME");
            $region     = $db->result($r, $i - 1, "REGION_NAME");
            $city       = $db->result($r, $i - 1, "CITY_NAME");
            $user_name  = $this->getMediaUserName($user_id);
            $cur        = ($id == $sel_id) ? "background-color:#0CF;" : "";
            $list .= "
            <tr style='$cur cursor:pointer;' onClick='setDpTpoint(\"$id\", \"$name\")'>
                <td>$id</td>
                <td>$name</td>
                <td>$state</td>
                <td>$region</td>
                <td>$city</td>
                <td>$address</td>
                <td>$user_name</td>
            </tr>";
        }

        $form = str_replace("{list}", $list, $form);

        return $form;
    }

    function getClientArticleMaxDiscount($art_id, $price) { $db = DbSingleton::getDb(); $dbt = DbSingleton::getTokoDb();
        session_start();
        $max_manager_discount = $_SESSION["user_discount"];
        $max_discount_persent = $max_discount_price = 0;
        $r = $dbt->query("SELECT t2aps.OPER_PRICE, t2apr.minMarkup
        FROM `T2_ARTICLES_PRICE_STOCK` t2aps 
            LEFT JOIN `T2_ARTICLES_PRICE_RATING` t2apr ON (t2apr.art_id = t2aps.ART_ID)
        WHERE t2aps.ART_ID = $art_id AND t2apr.in_use = 1 LIMIT 1;");
        $n= $dbt->num_rows($r);
        if ($n == 1) {
            $oper_price = $db->result($r, 0, "OPER_PRICE");
            $minMarkup = $db->result($r, 0, "minMarkup");
            if ($minMarkup <= 0) {
                $max_discount_persent = 0;
                $max_discount_price = $price;
            }
            if ($minMarkup > 0) {
                $p1 = $oper_price + round(($oper_price * $minMarkup / 100), 2);
                $p2 = round($price - $price * $max_manager_discount / 100, 2);
                if ($p2 > $p1) {
                    $max_discount_persent = $max_manager_discount;
                    $max_discount_price = round($price - $price * $max_discount_persent / 100, 2);
                }
                if ($p2 <= $p1) {
                    $max_discount = round(($p1 / $price - 1) * 100 * (-1), 2);
                    $max_discount_price = round($price - $price * $max_discount / 100, 2);
                    $max_discount_persent = $max_discount;
                }
            }
        }

        return array($max_discount_persent, $max_discount_price);
    }

    function getIncomeStatusDpsName($art_id, $suppl_id) { $db = DbSingleton::getDb();
        $inc_status_name = "Передано у замовлення постачальнику";
        $rs = $db->query("SELECT js.amount, j.storage_id, j.storage_cells_id, j.time_stamp 
        FROM `J_INCOME_STR` js 
            JOIN `J_INCOME` j ON (js.income_id = j.id)
        WHERE j.status = 1 AND j.oper_status = 31 AND j.client_seller = $suppl_id AND js.art_id = $art_id 
        ORDER BY j.id DESC 
        LIMIT 0,3;");
        $ns = $db->num_rows($rs);
        if ($ns > 0) {
            $inc_status_name = "Надійшло від постачальника ";
            for ($is = 1; $is <= $ns; $is++) {
                $inc_amount         = $db->result($rs, $is - 1, "amount");
                $storage_id         = $db->result($rs, $is - 1, "storage_id");
                $storage_cells_id   = $db->result($rs, $is - 1, "storage_cells_id");
                $inc_data           = $db->result($rs, $is - 1, "time_stamp");
                $inc_status_name    .= " $inc_amount" . "шт. $inc_data на скл." . $this->getStorageName($storage_id) . " " . $this->getStorageCellName($storage_cells_id);
            }
        }

        return $inc_status_name;
    }

    function getDpStrStatus($dp_id) { $db = DbSingleton::getDb();
        $status_name = "";
        $r = $db->query("SELECT j.status_dps, j.art_id, j.suppl_id, dps.mcaption as status_dps_name 
        FROM `J_DP_STR` j
            LEFT JOIN `manual` dps ON (dps.id = j.status_dps AND dps.key = 'status_dps') 
        WHERE j.dp_id = $dp_id 
        GROUP BY j.status_dps 
        ORDER BY j.status_dps DESC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $status_id = $db->result($r, $i - 1, "status_dps");
            if ($status_id == 139) {
                $art_id             = $db->result($r, $i - 1, "art_id") + 0;
                $suppl_id           = $db->result($r, $i - 1, "suppl_id") + 0;
                $inc_status_name    = $this->getIncomeStatusDpsName($art_id, $suppl_id);
                $status_name        .= "$i)" . $inc_status_name;
            } else {
                $status_name        .= "$i)" . $db->result($r, $i - 1, "status_dps_name");
            }
            if ($i > 0 && $i < $n) {
                $status_name .= "<br>";
            }
        }

        return $status_name;
    }

    function showDpStrList($dp_id, $status_dp, $client_id, $cash_id, $usd_to_uah, $euro_to_uah) { $db = DbSingleton::getDb();
        $slave = new slave;
        $list = $function_amount_change = $suppl_card = $amount_bug_info = $delivery_info = "";
        $sum_weight = $sum_volume = $summ_dp = 0;
        if ($status_dp === "") {
            $status_dp = 79;
        }
        if ($client_id === "") {
            $client_id = $this->getDpClient($dp_id);
        }
        if ($cash_id === "") {
            $cash_id = $this->getDpCashId($dp_id);
        }
        if ($usd_to_uah === "" || $euro_to_uah === "") {
            [$usd_to_uah, $euro_to_uah] = $this->getKoursData();
        }
        $tpoint_id = $this->getDpTpoint($dp_id);
        $r = $db->query("SELECT j.*, m.mcaption as reserv_type_caption, s.name as storage_name, s2.name as location_storage_name, dps.mcaption as status_dps_name 
        FROM `J_DP_STR` j 
            LEFT JOIN `manual` m ON (m.id = j.reserv_type_id AND m.key = 'reserv_type') 
            LEFT JOIN `manual` dps ON (dps.id = j.status_dps AND dps.key = 'status_dps') 
            LEFT JOIN `STORAGE` s ON (s.id = j.storage_id_from) 
            LEFT JOIN `STORAGE` s2 ON (s2.id = j.location_storage_id) 
        WHERE j.dp_id = $dp_id 
        ORDER BY j.id ASC;");
        $n = $db->num_rows($r);
        $kl_rw = $n;
        for ($i = 1; $i <= $kl_rw; $i++) {
            $id                 = $db->result($r, $i - 1, "id");
            $dp_str_id          = $id;
            $reserv_type_id     = (int)$db->result($r, $i - 1, "reserv_type_id");
            $reserv_type_color  = "primary";
            if ($reserv_type_id === 68) {
                $reserv_type_color = "warning";
            }
            if ($reserv_type_id === 69) {
                $reserv_type_color = "danger";
            }
            $storage_id_from        = $db->result($r, $i - 1, "storage_id_from");
            $location_storage_id    = $db->result($r, $i - 1, "location_storage_id");
            $location_storage_name  = $db->result($r, $i - 1, "location_storage_name");
            $location_storage_name  = ($location_storage_id == $storage_id_from) ? "" : "=> $location_storage_name";
            $storage_name           = $db->result($r, $i - 1, "storage_name");
            $suppl_id               = $db->result($r, $i - 1, "suppl_id") + 0;
            $suppl_storage_id       = $db->result($r, $i - 1, "suppl_storage_id");
            if ($suppl_id > 0) {
                $storage_name = $suppl_id . "." . $suppl_storage_id;
            }
            $art_id                 = $db->result($r, $i - 1, "art_id") + 0;
            $article_nr_displ       = $db->result($r, $i - 1, "article_nr_displ");
            $brand_id               = $db->result($r, $i - 1, "brand_id");
            $brand_name             = $this->getBrandName($brand_id);
            $amount                 = $db->result($r, $i - 1, "amount");
            $amount_dp              = $amount;
            $amount                 = (int)$amount;
            $amount_collect         = $db->result($r, $i - 1, "amount_collect");
            $amount_bug_db          = $db->result($r, $i - 1, "amount_bug");
            $amount_bug = 0;
            if ($amount_collect > 0 || $amount_bug_db != 0) {
                $amount_dp = $amount_collect;
            }
            if ($amount_collect > 0) {
                $amount_bug = $amount - $amount_collect;
            }
            if ($amount_collect == 0 && $amount_bug_db > 0) {
                $amount_bug = $amount_bug_db;
            }
            $amount_collect         = (int)$amount_collect;
            $amount_bug             = (int)$amount_bug;
            $price                  = $slave->to_money($db->result($r, $i - 1, "price"));
            $rating_price           = $db->result($r, $i - 1, "price");
            $price_end              = $slave->to_money($db->result($r, $i - 1, "price_end"));
            $discount               = $db->result($r, $i - 1, "discount");
            $status_dps             = $db->result($r, $i - 1, "status_dps");
            $return_delay           = $db->result($r, $i - 1, "return_delay");
            $inc_status_name        = ($status_dps == 139) ? $this->getIncomeStatusDpsName($art_id, $suppl_id) : "";
            $status_dps_name        = $db->result($r, $i - 1, "status_dps_name") . $inc_status_name;
            $suppl_card             = ($suppl_id > 0) ? "<button class='btn btn-xs btn-default btn-danger' onclick='showDpSupplInfo(\"$suppl_id\", \"$suppl_storage_id\");'><i class='fa fa-info'></i> Постачальник</button>" : "";

            // NEW CASH ALGO
            $cash_id_to     = $cash_id;
            $cash_id_from   = $this->getArticlePriceRatingCash($art_id);
            $price          = $this->getPriceRatingKours($price, $cash_id_from, $cash_id_to, $usd_to_uah, $euro_to_uah);
            $price_end      = $this->getPriceRatingKours($price_end, $cash_id_from, $cash_id_to, $usd_to_uah, $euro_to_uah);

            if ($cash_id == 1) {
                $price      = $this->getClientPriceRounding($this->getDpClient($dp_id), $price);
                $price_end  = $this->getClientPriceRounding($this->getDpClient($dp_id), $price_end);
            }

            [$max_discount_persent, $max_discount_price] = $this->getClientArticleMaxDiscount($art_id, $rating_price);

            $max_discount_price = $this->getPriceRatingKours($max_discount_price, $cash_id_from, $cash_id_to, $usd_to_uah, $euro_to_uah);

            $summ = round($amount_dp * $price_end, 2);

            /*new!!!*/
            $summ_dp += $summ;
            if ($suppl_id == 0) {
                $delivery_info = $this->getTpointDeliveryInfo($tpoint_id, $storage_id_from);
            }
            if ($suppl_id > 0) {
                $delivery_info = $this->getTpointSupplDeliveryInfo($tpoint_id, $suppl_id, $suppl_storage_id);
            }

            if ($status_dp == 79) {
                [$weight, $volume] = $this->getArticleWightVolume($art_id);
                $sum_weight += ($weight * $amount);
                $sum_volume += ($volume * $amount);
                $disabled = "";
                if ($status_dp != 79 && $status_dp > 0) {
                    $disabled = " disabled";
                }
                if ($suppl_id == 0) {
                    $function_amount_change = "setArticleToSelectAmountDp('$art_id','$article_nr_displ','$brand_id','$brand_name','$dp_id');";
                }
                if ($suppl_id > 0) {
                    $function_amount_change = "showDpSupplAmountInputWindow('$art_id','$article_nr_displ','$brand_id','$brand_name','$dp_id','$suppl_id','$suppl_storage_id','$price');";
                }

                require_once (RD . "/lib/action_clients_class.php");
                $action_clients = new action_clients;
                if (!($action_clients->checkActionStr($art_id, $client_id))) {
                    $action = "";
                } else {
                    [$action_amount, $action_price] = $action_clients->checkActionStr($art_id, $client_id);
                    $title = "Ціна - $action_price $, від $action_amount шт.";
                    $action = "<br><i class='fa fa-gift tooltips' style='font-size: 2em; color: lightcoral;' title=\"$title\" data-toggle=\"tooltip\" data-placement=\"bottom\"></i>";
                }

                $list .= "
                <tr id='strRow_$i'>
                    <td style='text-align: center;'>
                        <input id='$i' type='checkbox' name='check[$i]' class='check_dp'>
                        $action
                    </td>
                    <td>$i<input type='hidden' id='idStr_$i' value='$id'></td>
                    <td style='min-width:140px;'><input type='hidden' id='artIdStr_$i' value='$art_id'>
                        <div class='input-group'>
                            <input class='form-control input-xs' type='text' readonly id='article_nr_displStr_$i' value='$article_nr_displ' placeholder='Індекс товару'>
                            <span class='input-group-btn'> 
                                <button type='button' class='btn btn-xs btn-info $disabled' $disabled onClick=\"showArticleSearchDocumentForm('$i','$art_id','$brand_id','$article_nr_displ','dp','$dp_id');\"><i class=\"fa fa-bars\"></i></button>
                            </span>
                        </div>
                        <span class='hidden'>$article_nr_displ</span>
                        $suppl_card
                    </td>
                    <td style='min-width:100px;'><input type='hidden' id='brandIdStr_$i' value='$brand_id'>
                        <input class='form-control input-xs' type='text' readonly id='brandNameStr_$i' value='$brand_name' placeholder='Бренд'>
                        <span class='hidden'>$brand_name</span>
                    </td>
                    <td>
                        <div class='input-group' style='width: 75px;'>
                            <input type='text' id='amountStr_$i' readonly value='$amount' class='form-control input-xs numberOnly' autocomplete='off'>
                            <span class='input-group-btn'> <button type='button' class='btn btn-xs btn-primary $disabled' $disabled onClick=\"$function_amount_change\"><i class=\"fa fa-bars\"></i></button></span>
                        </div>
                        <span class='hidden'>$amount</span>
                    </td>
                    <td>$amount_collect</td>
                    <td title='$amount_bug_info'>$amount_bug</td>
                    <td>
                        <input type='text' id='priceStr_$i' readonly value='$price' class='form-control input-xs numberOnlyLong' autocomplete='off'>
                        <span class='hidden'>$price</span>
                    </td>
                    <td>
                        <input type='text' id='discountStr_$i' value='$discount' class='form-control input-xs numberOnlyLong' autocomplete='off' title='$max_discount_persent"."% ($max_discount_price)' onChange=\"calculateDiscountPrice('$i')\">
                        <input type='hidden' id='maxDiscountPersentStr_$i' value='$max_discount_persent'>
                        <input type='hidden' id='maxDiscountPriceStr_$i' value='$max_discount_price'>
                        <span class='hidden'>$discount</span>
                    </td>
                    <td>
                        <input type='text' id='priceEndStr_$i' value='$price_end' class='form-control input-xs numberOnlyLong' autocomplete='off' title='$max_discount_persent"."% ($max_discount_price)' onChange=\"calculateDiscountPersent('$i')\">
                        <span class='hidden'>$price_end</span>
                    </td>
                    <td>
                        <input type='text' id='summStr_$i' readonly value='$summ' class='form-control input-xs numberOnlyLong' autocomplete='off'>
                        <span class='hidden'>$brand_name</span>
                    </td>
                    <td><span class='label label-$reserv_type_color'>$storage_name $location_storage_name</span></td>
                    <td><small>$delivery_info</small></td>
                    <td>$return_delay</td>
                    <td>$status_dps_name ($status_dps)</td>
                    <td><button class='btn btn-xs btn-default $disabled' $disabled onClick='dropDpStr(\"$i\",\"$dp_id\",\"$id\");'><i class='fa fa-times'></i></button></td>
                </tr>";
            }

            if (($status_dp > 79) && $article_nr_displ !== "") {
                $link_btn = "";
                if ($status_dps == 139 && $inc_status_name !== "") {
                    $link_btn = " 
                    <button type='button' class='btn btn-xs btn-danger' onClick=\"showSupplToLocalChangeForm('$i','$art_id','$article_nr_displ','$dp_id','$dp_str_id');\"><i class=\"fa fa-link\"></i></button>";
                }
                $list .= "
                <tr>
                    <td style='text-align: center;'></td>
                    <td>$i<input type='hidden' id='idStr_$i' value='$id'></td>
                    <td style='min-width:140px;'>$article_nr_displ $link_btn $suppl_card</td>
                    <td style='min-width:120px;'>$brand_name</td>
                    <td>$amount</td>
                    <td>$amount_collect</td>
                    <td>$amount_bug</td>
                    <td>$price</td>
                    <td>$discount</td>
                    <td>$price_end</td>
                    <td>$summ</td>
                    <td><span class='label label-$reserv_type_color'>$storage_name $location_storage_name</span></td>
                    <td><small>$delivery_info</small></td>
                    <td>$return_delay</td>
                    <td>$status_dps_name</td>
                    <td></td>
                </tr>";
            }
        }

        if ($status_dp == 79) {
            $list = "
            <tr id='dpStrNewRow' class='hidden'>
                <td style='text-align: center;'><input id='i_0' type='checkbox' name='check[i_0]' class='checkit'/></td>
                <td>nom_i<input type='hidden' id='idStr_0' value=''></td>
                <td style='min-width:140px;'><input type='hidden' id='artIdStr_0' value=''>
                    <div class='input-group'>
                        <input class='form-control input-xs' type='text' readonly id='article_nr_displStr_0' value='' placeholder='Індекс товару'>
                        <span class='input-group-btn'> 
                            <button type='button' class='btn btn-xs btn-info' onClick=\"showArticleSearchDocumentForm('i_0','0','0','','dp','$dp_id');\"><i class=\"fa fa-bars\"></i></button>
                        </span>
                    </div>
                    $suppl_card
                </td>
                <td style='min-width:120px;'><input type='hidden' id='brandIdStr_0' value=''>
                    <input class='form-control input-xs' type='text' readonly id='brandNameStr_0' value='' placeholder='Бренд'>
                </td>
                <td>
                    <input type='text' id='amountStr_0' value='' readonly class='form-control input-xs numberOnly' autocomplete='off' maxlength=''  min='1' max=''>
                </td>
                <td>-</td>
                <td>-</td>
                <td>
                    <input type='text' id='priceStr_0' readonly value='0' class='form-control input-xs numberOnly' autocomplete='off'>
                </td>
                <td>
                    <input type='text' id='discountStr_0' readonly value='0' class='form-control input-xs numberOnly' autocomplete='off'>
                </td>
                <td>
                    <input type='text' id='priceEndStr_0' readonly value='0' class='form-control input-xs numberOnly' autocomplete='off'>
                </td>
                <td>
                    <input type='text' id='summStr_0' readonly value='0' class='form-control input-xs numberOnly' autocomplete='off'>
                </td>
                <td><span class='label label-0'></span></td>
                <td></td>
                <td></td>
                <td></td>
                <td><button class='btn btn-xs btn-default'><i class='fa fa-times'></i></button></td>
            </tr>" . $list;
        }

        if ($sum_weight != 0 && $sum_volume != 0 && $status_dp == '79') {
            $db->query("UPDATE `J_DP` SET `weight` = '$sum_weight', `volume` = '$sum_volume' WHERE `id` = $dp_id AND `status_dp` = 79;");
        }

        if ($status_dp == 79 || $status_dp == 80) {
            $db->query("UPDATE `J_DP` SET `summ` = '$summ_dp' WHERE `id` = $dp_id AND `status_dp` IN (79, 80);");
        }

        return array($list, $kl_rw);
    }

    function saveDpCard($dp_id, $cash_id, $dp_summ, $doc_type_id, $tpoint_id, $client_id, $client_conto_id, $delivery_type_id, $carrier_id, $processed_status) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка збереження даних!";
        $dp_id = $slave->qq($dp_id); $cash_id = $slave->qq($cash_id); $dp_summ = $slave->qq($dp_summ); $doc_type_id = $slave->qq($doc_type_id); $tpoint_id = $slave->qq($tpoint_id);
        $client_id = $slave->qq($client_id); $client_conto_id = $slave->qq($client_conto_id); $delivery_type_id = $slave->qq($delivery_type_id); $carrier_id = $slave->qq($carrier_id); $processed_status = $slave->qq($processed_status);
        if ($dp_id > 0) {
            $db->query("UPDATE `J_DP` SET `doc_type_id` = '$doc_type_id', `tpoint_id` = '$tpoint_id', `client_id` = '$client_id', `client_conto_id` = '$client_conto_id', `cash_id` = '$cash_id', `summ` = '$dp_summ', `delivery_type_id` = '$delivery_type_id', `carrier_id` = '$carrier_id', `status_processed` = '$processed_status' WHERE `id` = $dp_id;");
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

    function saveDpCardData($dp_id) {
        $slave = new slave;
        $answer = 0; $err = "Помилка збереження даних!";
        $dp_id = $slave->qq($dp_id);
        if ($dp_id > 0) {
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

    function roundingDpDiscount($dp_id, $price_end, $amount) {
        $client_id = $this->getDpClient($dp_id);
        $cash_id = $this->getDpCashId($dp_id);
        if ($cash_id == 1) {
            $price = $this->getClientPriceRounding($client_id, $price_end);
        } else {
            $price = $price_end;
        }
        $summ = $price * $amount;
        return array($price, $summ);
    }

    function updateDpStrPrice($dp_id, $dp_str_id, $discount_new) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка збереження даних!";
        $dp_id = $slave->qq($dp_id); $dp_str_id = $slave->qq($dp_str_id);
        if ($dp_id > 0 && $dp_str_id > 0 && $discount_new !== "") {
            $discount_new = $slave->qq($discount_new);
            $r = $db->query("SELECT `amount`, `price`, `discount` FROM `J_DP_STR` WHERE `dp_id` = $dp_id AND `id` = $dp_str_id LIMIT 1;");
            $n = $db->num_rows($r);
            if ($n == 1) {
                $amount     = $db->result($r, 0, "amount");
                $price      = $db->result($r, 0, "price");
                $discount   = $db->result($r, 0, "discount");
                if ($discount != $discount_new) {
                    $discount   = $discount_new;
                    $price_end  = round($price - $price * $discount / 100, 2);
                    $summ       = round($price_end * $amount, 2);
                    $db->query("UPDATE `J_DP_STR` SET `discount` = '$discount', `price_end` = '$price_end', `summ` = '$summ' WHERE `id` = $dp_str_id AND `dp_id` = $dp_id;");
                }
            }
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

    /*
     * Передати в ДП рядок
     * */
    function setArticleToDp($dp_id, $tpoint_id, $artIdStr, $article_nr_displStr, $brandIdStr, $storageIdStr, $amountStr, $status_action, $order_price, $discount, $return_delay) { $db = DbSingleton::getDb(); $dbt = DbSingleton::getTokoDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка збереження даних!";
        $weight = $volume = $empty_kol = $label_empty = $dp_summ = "";
        $idS = $discountEx = $rr_amount = $rr_reserv = $amountEx = 0;
        $dp_id      = $slave->qq($dp_id);
        $tpoint_id  = $slave->qq($tpoint_id);
        $all_time   = "";

        if ($dp_id > 0) {
            $artIdS     = $slave->qq($artIdStr);
            $art_nr_ds  = $slave->qq($article_nr_displStr);
            $brandIdS   = $slave->qq($brandIdStr);
            $amountS    = $slave->qq($amountStr);
            $storageIdS = $slave->qq($storageIdStr);
            $dp_id      = (int)$dp_id;
            $artIdS     = (int)$artIdS;
            $storageIdS = (int)$storageIdS;

            $r = $db->query("SELECT `id`, `amount`, `discount` FROM `J_DP_STR` 
            WHERE `dp_id` = $dp_id AND `art_id` = $artIdS AND `storage_id_from` = $storageIdS AND `status_dps` = 93 LIMIT 1;");
            $n = $db->num_rows($r);
            if ($n == 1) {
                $idS        = $db->result($r, 0, "id");
                $amountEx   = $db->result($r, 0, "amount");
                $discountEx = $db->result($r, 0, "discount");
            }

            [, $max_moving, $rest_amount] = $this->showArticleRestStorageSelectText($artIdS, $storageIdS, $amountS, $amountEx);

            if ($amountS > $max_moving && $rest_amount <= 0) {
                $answer = 0; $err = "Кількість для переміщення ВЖЕ більша за залишок! (максимально: $max_moving)";
            }

            if ($amountS <= $max_moving) {
                $can_drop = 0;
                if ($idS === "" || $idS == 0) {
                    $reserv_type_id = $this->getArticleReservType($tpoint_id, $storageIdS);
                    $r = $db->query("SELECT MAX(`id`) as mid FROM `J_DP_STR`;");
                    $idS = 0 + $db->result($r, 0, "mid") + 1;
                    $db->query("INSERT INTO `J_DP_STR` (`id`, `dp_id`, `reserv_type_id`) VALUES ('$idS', '$dp_id', '$reserv_type_id');");
                    $rr_reserv = 0; $amountEx = 0; $can_drop = 1;
                }
                if (($idS > 0) && $artIdS !== "" && $artIdS > 0 && $art_nr_ds !== "") {
                    // price in USD
                    if ($status_action > 0) {
                        $article_price = $order_price;
                    } else {
                        $article_price = $this->getArticlePrice($artIdS, $dp_id);
                    }

                    if ($article_price > 0) {
                        if ($status_action > 0) {
                            $price_end = $order_price;
                        } else {
                            $return_delay = 0;
                            if ($discount > 0) {
                                $discountEx = $discount;
                            }
                            $price_end = round($article_price - $article_price * $discountEx / 100, 2);
                        }

                        $summ = round($price_end * $amountEx, 2);

                        $reserv_type_id = $this->getArticleReservType($tpoint_id, $storageIdS);

                        $db->query("UPDATE `J_DP_STR` SET `art_id` = $artIdS, `article_nr_displ` = '$art_nr_ds', `brand_id` = $brandIdS, `amount` = '$amountS', `storage_id_from` = $storageIdS, 
                        `location_storage_id` = $storageIdS, `price` = '$article_price', `price_end` = '$price_end', `summ` = '$summ', `reserv_type_id` = $reserv_type_id, `status_action` = '$status_action', 
                        `return_delay` = '$return_delay', `discount` = '$discount' WHERE `id` = $idS AND `dp_id` = $dp_id;");

                        [$weight, $volume, $empty_kol] = $this->updateDpWeightVolume($dp_id);

                        $dp_summ = $this->updateDpSumm($dp_id);

                        $rr = $dbt->query("SELECT `AMOUNT`, `RESERV_AMOUNT` FROM `T2_ARTICLES_STRORAGE` WHERE `ART_ID` = $artIdS AND `STORAGE_ID` = $storageIdS LIMIT 1;");
                        $nr = $dbt->num_rows($rr);
                        if ($nr == 1) {
                            $rr_amount = $dbt->result($rr, 0, "AMOUNT");
                            $rr_reserv = $dbt->result($rr, 0, "RESERV_AMOUNT");
                        }
                        $rr_amount = $rr_amount + $amountEx - $amountS;
                        $rr_reserv = $rr_reserv + $amountS - $amountEx;
                        $dbt->query("UPDATE `T2_ARTICLES_STRORAGE` SET `AMOUNT` = '$rr_amount', `RESERV_AMOUNT` = '$rr_reserv' WHERE `ART_ID` = $artIdS AND `STORAGE_ID` = $storageIdS;");

                        [$empty_kol, $label_empty] = $this->labelArtEmptyCount($dp_id, $empty_kol);
                        $answer = 1; $err = "";

                    } else {
                        $answer = 0; $err = "Ціна товару `$article_nr_displStr` не визначена. Зверніться до відповідального менеджера";
                        if ($can_drop == 1) {
                            $db->query("DELETE FROM `J_DP_STR` WHERE `id` = $idS AND `dp_id` = $dp_id LIMIT 1;");
                        }
                    }
                }
            }
        }

        return array($answer, $err, $idS, $weight, $volume, $empty_kol, $label_empty, $dp_summ, $all_time);
    }

    /*
     * Передати в ДП рядок по постачальнику
     * */
    function setArticleSupplToDp($dp_id, $artIdStr, $article_nr_displStr, $brandIdStr, $supplIdStr, $supplStorageIdStr, $amountStr, $status_action, $order_price, $discount, $return_delay) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка збереження даних!";
        $dp_id = $slave->qq($dp_id);
        $discountEx = 0;
        $idS = $weight = $volume = $empty_kol = $label_empty = $dp_summ = "";

        if ($dp_id > 0) {
            $artIdS             = $slave->qq($artIdStr);
            $article_nr_displS  = $slave->qq($article_nr_displStr);
            $brandIdS           = $slave->qq($brandIdStr);
            $amountS            = $slave->qq($amountStr);
            $supplIdS           = $slave->qq($supplIdStr);
            $supplStorageIdS    = $slave->qq($supplStorageIdStr);
            $amountEx           = 0;
            $dp_id              = (int)$dp_id;
            $artIdS             = (int)$artIdS;
            $supplIdS           = (int)$supplIdS;
            $supplStorageIdS    = (int)$supplStorageIdS;

            $r = $db->query("SELECT `id`, `amount`, `discount` FROM `J_DP_STR` 
            WHERE `dp_id` = $dp_id AND `art_id` = $artIdS AND `suppl_id` = $supplIdS AND `suppl_storage_id` = $supplStorageIdS AND `status_dps` = 93 LIMIT 1;");
            $n = $db->num_rows($r);

            if ($n == 1) {
                $idS = $db->result($r, 0, "id");
                $amountEx = $db->result($r, 0, "amount");
                $discountEx = $db->result($r, 0, "discount");
            }

            $can_drop = 0;
            if ($idS === "" || $idS == 0) {
                $r = $db->query("SELECT MAX(`id`) as mid FROM `J_DP_STR`;");
                $idS = 0 + $db->result($r, 0, "mid") + 1;
                $db->query("INSERT INTO `J_DP_STR` (`id`, `dp_id`, `reserv_type_id`) VALUES ($idS, $dp_id, 69);");
                $amountEx = 0;
                $can_drop = 1;
            }

            if (($idS > 0) && $artIdS !== "" && $supplIdS > 0 && $article_nr_displS !== "") {

                if ($status_action > 0) {
                    $article_price = $order_price;
                } else {
                    $article_price = $this->getArticleSupplPrice($artIdS, $dp_id, $supplIdS, $supplStorageIdS);
                }

                // BUG
                if (($article_price == 0) && $supplIdS > 0) {
                    $article_price = $order_price;
                }

                if ($article_price > 0) {
                    if ($status_action > 0) {
                        $price_end = $order_price;
                    } else {
                        $return_delay = 0;
                        if($discount > 0) {
                            $discountEx = $discount;
                        }
                        $price_end = round($article_price - $article_price * $discountEx / 100, 2);
                    }

                    $summ = round($price_end * $amountEx, 2);
                    $db->query("UPDATE `J_DP_STR` SET `art_id` = $artIdS, `article_nr_displ` = '$article_nr_displS', `brand_id` = $brandIdS, `amount` = '$amountS', `suppl_id` = $supplIdS, 
                    `suppl_storage_id` = $supplStorageIdS, `price` = '$article_price', `price_end` = '$price_end', `summ` = '$summ', `reserv_type_id` = 69, `return_delay` = '$return_delay', `discount` = '$discount'
                    WHERE `id` = $idS AND `dp_id` = $dp_id;");
                    [$weight, $volume, $empty_kol] = $this->updateDpWeightVolume($dp_id);
                    $dp_summ = $this->updateDpSumm($dp_id);
                    [$empty_kol, $label_empty] = $this->labelArtEmptyCount($dp_id, $empty_kol);
                    $answer = 1; $err = "";
                } else {
                    $answer = 0; $err = "Ціна товару `$article_nr_displStr` не визначена. Зверніться до відповідального менеджера";
                    if ($can_drop == 1) {
                        $db->query("DELETE FROM `J_DP_STR` WHERE `id` = $idS AND `dp_id` = $dp_id LIMIT 1;");
                    }
                }
            }
        }

        return array($answer, $err, $idS, $weight, $volume, $empty_kol, $label_empty, $dp_summ);
    }

    function getClientCashConditions($client_id) { $db = DbSingleton::getDb();
        $cash_id = 0; $credit_cash_id = 0;
        $r = $db->query("SELECT `cash_id`, `credit_cash_id` FROM `A_CLIENTS_CONDITIONS` WHERE `client_id` = '$client_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $cash_id = $db->result($r, 0, "cash_id");
            $credit_cash_id = $db->result($r, 0, "credit_cash_id");
        }
        return array($cash_id, $credit_cash_id);
    }

    function getClientOrgType($client_id) { $db = DbSingleton::getDb();
        $org_type = 0;
        $r = $db->query("SELECT `org_type` FROM `A_CLIENTS` WHERE `id` = '$client_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $org_type = $db->result($r, 0, "org_type");
        }
        return $org_type;
    }

    function getCashName($cash_id) { $db = DbSingleton::getDb();
        $name = "";
        $r = $db->query("SELECT `name` FROM `CASH` WHERE `id` = '$cash_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $name = $db->result($r, 0, "name");
        }
        return $name;
    }

    function getDpClientContoCash($client_id) { $db = DbSingleton::getDb();
        $cash_id = 1;
        $answer = 0; $err = "Помилка";
        $r = $db->query("SELECT `cash_id` FROM `A_CLIENTS_CONDITIONS` WHERE `client_id` = '$client_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $cash_id = $db->result($r, 0, "cash_id");
            $answer = 1; $err = "";
        }
        return array($answer, $err, $cash_id);
    }

    function getDpClientDocType($client_id) { $db = DbSingleton::getDb();
        $doc_type_id = 64;
        $answer = 0; $err = "Помилка";
        $r = $db->query("SELECT `doc_type_id` FROM `A_CLIENTS_CONDITIONS` WHERE `client_id` = '$client_id' LIMIT 1;");
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
        $r = $db->query("SELECT * FROM `A_CLIENTS_CONDITIONS` WHERE `client_id` = '$client_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $payment_delay = $db->result($r, 0, "payment_delay");
            $data_pay = date("Y-m-d", strtotime("+$payment_delay day", strtotime($data_pay)));
            $answer = 1; $err = "";
        }
        return array($answer, $err, $data_pay);
    }

    function changeDpCash($dp_id, $cash_id) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка збереження даних!";
        $dp_id = $slave->qq($dp_id); $cash_id = $slave->qq($cash_id);
        if ($dp_id > 0) {
            $r = $db->query("SELECT `oper_status`, `client_conto_id` FROM `J_DP` WHERE `id` = $dp_id LIMIT 1;");
            $n = $db->num_rows($r);
            if ($n == 1) {
                $oper_status = $db->result($r, 0, "oper_status");
                if ($oper_status == 30) {
                    $client_conto_id = $db->result($r, 0, "client_conto_id");
                    $org_type = $this->getClientOrgType($client_conto_id);
                    [$client_cash_id,] = $this->getClientCashConditions($client_conto_id);
                    if ($client_cash_id == $cash_id || $org_type == 0 || $org_type == 1) {
                        $db->query("UPDATE `J_DP` SET `cash_id` = '$cash_id' WHERE `id` = $dp_id;");
                        $this->updateDpPriceCash($dp_id);
                        $answer = 1; $err = "";
                    } else {
                        $answer = 0; $err = "Валюта розрахунку клієнта " . $this->getCashName($client_cash_id).". Змініть кінцевого платника на того кому дозволено розрахунок у валюті " . $this->getCashName($cash_id);
                    }
                } else {
                    $answer = 0; $err = "Документ заблоковано. Зміни вносити заборонено.";
                }
            }
        }
        return array($answer, $err);
    }

    function updateDpPriceCash($dp_id) { $db = DbSingleton::getDb();
        $r = $db->query("SELECT `oper_status` FROM `J_DP` WHERE `id` = $dp_id LIMIT 1;");
        $n = $db->num_rows($r);
        $answer = 0;
        if ($n == 1) {
            $oper_status = $db->result($r, 0, "oper_status");
            if ($oper_status == 30) {
                $r = $db->query("SELECT `amount`, `price_end` FROM `J_DP_STR` WHERE `dp_id` = '$dp_id' ORDER BY `id` ASC;");
                $n = $db->num_rows($r);
                $summ_dp = 0;
                for ($i = 1; $i <= $n; $i++) {
                    $amount = $db->result($r, $i - 1, "amount");
                    $price_end = $db->result($r, $i - 1, "price_end");
                    $summ = $amount * $price_end;
                    $summ_dp += $summ;
                }
                $db->query("UPDATE `J_DP` SET `summ` = '$summ_dp' WHERE `id` = $dp_id LIMIT 1;");
                $answer = 1;
            } else {
                $answer = 0;
            }
        }
        return $answer;
    }

    function getClientMarkupMin($client_id) { $db = DbSingleton::getDb();
        $r = $db->query("SELECT `markup_min` FROM `A_CLIENTS_CONDITIONS` WHERE `client_id` = '$client_id' LIMIT 1;");
        return $db->result($r, 0, "markup_min");
    }

    function getClientFromDp($dp_id) { $db = DbSingleton::getDb();
        $r = $db->query("SELECT `client_conto_id` FROM `J_DP` WHERE `id` = $dp_id LIMIT 1;");
        return $db->result($r, 0, "client_conto_id");
    }

    function getArticlePrice($art_id, $dp_id) { $dbt = DbSingleton::getTokoDb();
        [$usd_to_uah, $eur_to_uah] = $this->getKoursData();
        $price = 0;

        if ($dp_id > 0 && $art_id !== "") {
            $markup_min = $this->getClientMarkupMin($this->getClientFromDp($dp_id));
            [$price_lvl, $margin_price_lvl, , ,] = $this->getDpClientPriceLevels($dp_id);

            $r = $dbt->query("SELECT t2apr.price_".$price_lvl.", t2apr.cash_id, t2apr.minMarkup, t2aps.OPER_PRICE
            FROM `T2_ARTICLES` t2a 
                LEFT OUTER JOIN `T2_ARTICLES_PRICE_RATING` t2apr ON (t2apr.art_id = t2a.ART_ID)
                LEFT OUTER JOIN `T2_ARTICLES_PRICE_STOCK` t2aps ON (t2aps.ART_ID = t2a.ART_ID)
            WHERE t2a.ART_ID = '$art_id' AND t2apr.in_use='1' LIMIT 1;");
            $n = $dbt->num_rows($r);

            if ($n == 1) {
                $price = $dbt->result($r, 0, "price_" . $price_lvl);
                $minMarkup = $dbt->result($r, 0, "minMarkup");
                $OPER_PRICE = $dbt->result($r, 0, "OPER_PRICE");
                $cash_id = $dbt->result($r, 0, "cash_id");
                $OPER_PRICE = $this->getPriceRatingKours($OPER_PRICE, 2, $cash_id, $usd_to_uah, $eur_to_uah); // OPER_PRICE USD => cash_id

                if ($margin_price_lvl > 0) {
                    $price += round($price * $margin_price_lvl / 100, 2);
                }
                if ($margin_price_lvl < 0 && $markup_min == 0) {
                    $price_minus = $price + ($price * $margin_price_lvl / 100);
                    $oper_limit = $OPER_PRICE + ($OPER_PRICE * $minMarkup / 100);

                    if ($price_minus >= $oper_limit) {
                        $price = $price_minus;
                    } elseif ($oper_limit >= $price) {
                        true;
                    } else {
                        $price = $oper_limit;
                    }
                }

                if ($margin_price_lvl < 0 && $markup_min > 0) {
                    $price = $this->getPriceRatingKours($price, $cash_id, 2, $usd_to_uah, $eur_to_uah);
                    $proc_price_margin = $price - ($price * abs($margin_price_lvl) / 100);
                    $proc_oper_price_min = $OPER_PRICE + ($OPER_PRICE * $markup_min / 100);

                    if ($proc_price_margin >= $proc_oper_price_min) {
                        $price = $proc_price_margin;
                    } else if (($proc_price_margin < $proc_oper_price_min) && ($proc_oper_price_min > $price)) {
                        true;
                    } else {
                        $price = $proc_oper_price_min;
                    }

                    $price = $this->getPriceRatingKours($price, 2, $cash_id, $usd_to_uah, $eur_to_uah);
                }
            }
        }
        return $price;
    }

    function getArticleSupplPrice($art_id, $dp_id, $suppl_id, $suppl_storage_id) { $dbt = DbSingleton::getTokoDb();
        $price = 0;

        $art_id = (int)$art_id;
        $suppl_id = (int)$suppl_id;

        if ($dp_id > 0 && $art_id > 0) {
            [, , $price_suppl_lvl, $margin_price_suppl_lvl, $client_vat] = $this->getDpClientPriceLevels($dp_id);

            $r = $dbt->query("SELECT t2si.price_usd 
            FROM `T2_ARTICLES` t2a 
                LEFT OUTER JOIN `T2_SUPPL_IMPORT` t2si ON (t2si.art_id = t2a.ART_ID AND t2si.status = 1)
            WHERE t2a.ART_ID = $art_id AND t2si.suppl_id = $suppl_id LIMIT 1;");
            $n = $dbt->num_rows($r);

            if ($n == 1) {
                $suppl_price_usd = $dbt->result($r, 0, "price_usd");
                [$price_in_vat, $show_in_vat, $price_add_vat] = $this->getSupplVatConditions($suppl_id);
                $price_suppl = $suppl_price_usd;
                $tpoint_id = $this->getDpTpoint($dp_id);

                //Step 1;
                [$suppl_margin_fm, $suppl_delivery_fm, $suppl_margin2_fm] = $this->getTpointSupplFm($tpoint_id, $suppl_id, $suppl_storage_id, $price_suppl, $price_suppl_lvl);

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

                    //Step 3; VAT //$price_in_vat,$show_in_vat,$price_add_vat
                    if ($client_vat == 1) {

                        if ($price_in_vat == 0 && $show_in_vat == 1 && $price_add_vat == 1) {
                            $price = $price + $price * $this->vat_percent / 100;
                        }

                        if ($price_in_vat == 0 && $show_in_vat == 0) {
                            $price = 0;
                        }

                    }
                }

                $price = round($price, 2);
            }
        }

        return $price;
    }

    function dropDpStr($dp_id, $dp_str_id) { $db = DbSingleton::getDb(); $dbt = DbSingleton::getTokoDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка індексу";
        $dp_id = $slave->qq($dp_id); $dp_summ = "";

        $r = $db->query("SELECT `oper_status`, `status` FROM `J_DP` WHERE `id` = $dp_id LIMIT 1;");
        $n = $db->num_rows($r);

        if ($n == 1) {
            $status = $db->result($r, 0, "status");
            $oper_status = $db->result($r, 0, "oper_status");

            if ($oper_status == 30 && $status == 1) {
                $r1 = $db->query("SELECT * FROM `J_DP_STR` WHERE `id` = '$dp_str_id' LIMIT 1;");
                $n1 = $db->num_rows($r1);

                if ($n1 == 1) {
                    $status_dps_str = $db->result($r1, 0, "status_dps");

                    if ($status_dps_str == 93) {
                        $art_id = $db->result($r1, 0, "art_id");
                        $amount = $db->result($r1, 0, "amount");
                        $storage_id_from = $db->result($r1, 0, "storage_id_from");
                        $suppl_id = $db->result($r1, 0, "suppl_id");

                        if ($art_id == 0) {
                            $db->query("DELETE FROM `J_DP_STR` WHERE `id` = '$dp_str_id' AND `dp_id` = '$dp_id' LIMIT 1;");
                            $this->updateDpWeightVolume($dp_id);
                            $dp_summ = $this->updateDpSumm($dp_id);
                            $answer = 1; $err = "";
                        }

                        if ($art_id > 0 && $suppl_id > 0) {
                            $db->query("DELETE FROM `J_DP_STR` WHERE `id` = '$dp_str_id' AND `dp_id` = '$dp_id' LIMIT 1;");
                            $this->updateDpWeightVolume($dp_id);
                            $dp_summ = $this->updateDpSumm($dp_id);
                            $answer = 1; $err = "";
                        }

                        if ($art_id > 0 && $suppl_id == 0) {
                            $rs = $dbt->query("SELECT `RESERV_AMOUNT`, `AMOUNT` FROM `T2_ARTICLES_STRORAGE` WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id_from LIMIT 1;");
                            $ns = $dbt->num_rows($rs);

                            if ($ns == 1) {
                                $reserv_amount_s = $dbt->result($rs, 0, "RESERV_AMOUNT");
                                $amount_s = $dbt->result($rs, 0, "AMOUNT");
                                $reserv_amount_s -= $amount;
                                $amount_s += $amount;
                                $dbt->query("UPDATE `T2_ARTICLES_STRORAGE` SET `AMOUNT` = '$amount_s', `RESERV_AMOUNT` = '$reserv_amount_s' 
                                WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id_from LIMIT 1;");
                                $db->query("DELETE FROM `J_DP_STR` WHERE `id` = '$dp_str_id' AND `dp_id` = '$dp_id' LIMIT 1;");
                                $this->updateDpWeightVolume($dp_id);
                                $dp_summ = $this->updateDpSumm($dp_id);
                                $answer = 1; $err = "";
                            }
                        }
                    } else {
                        $answer = 0; $err = "Видалення заблоковано. Відбір передано в роботу.";
                    }
                }
            } else {
                $answer = 0; $err = "Видалення заблоковано. Замовлення передано в роботу.";
            }
        }

        return array($answer, $err, $dp_summ);
    }

    function getPriceRatingKours($price, $cash_id_from, $cash_id_to, $usd_to_uah, $eur_to_uah) {
        if ($cash_id_from == $cash_id_to) {
            $price *= 1;
        }
        if ($cash_id_from == 1 && $cash_id_to == 2) {
            $price /= $usd_to_uah;
        }
        if ($cash_id_from == 1 && $cash_id_to == 3) {
            $price /= $eur_to_uah;
        }
        if ($cash_id_from == 2 && $cash_id_to == 1) {
            $price *= $usd_to_uah;
        }
        if ($cash_id_from == 3 && $cash_id_to == 1) {
            $price *= $eur_to_uah;
        }
        if ($cash_id_from == 2 && $cash_id_to == 3) {
            $price = $price * $usd_to_uah / $eur_to_uah;
        }
        if ($cash_id_from == 3 && $cash_id_to == 2) {
            $price = $price * $eur_to_uah / $usd_to_uah;
        }
        $price = round($price, 2);

        return $price;
    }

    function getDpSumm($dp_id) { $db = DbSingleton::getDb();
        $dp_summ = 0;
        $r = $db->query("SELECT `summ` FROM `J_DP` WHERE `id` = $dp_id LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n > 0) {
            $dp_summ = $db->result($r, 0, "summ");
        }
        return $dp_summ;
    }

    function updateDpSumm($dp_id) { $db = DbSingleton::getDb();
        $slave = new slave;
        $doc_sum = 0; $cash_id = $this->getDpCashId($dp_id);
        [$usd_to_uah, $eur_to_uah] = $this->getKoursData();

        $r = $db->query("SELECT * FROM `J_DP_STR` WHERE `dp_id`='$dp_id';");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $str_id         = $db->result($r, $i - 1, "id");
            $art_id         = $db->result($r, $i - 1, "art_id");
            $price_end      = $db->result($r, $i - 1, "price_end");
            $price_cash     = $price_end;
            $cash_id_to     = $cash_id;
            $cash_id_from   = $this->getArticlePriceRatingCash($art_id);
            $price_cash     = $this->getPriceRatingKours($price_cash, $cash_id_from, $cash_id_to, $usd_to_uah, $eur_to_uah);
            if ($cash_id == 1) {
                $price_cash = $this->getClientPriceRounding($this->getDpClient($dp_id), $price_cash);
            }
            $amount         = $db->result($r, $i - 1, "amount");
            $amount_dp      = $amount;
            $amount_collect = $db->result($r, $i - 1, "amount_collect");
            $amount_bug     = $db->result($r, $i - 1, "amount_bug");
            if ($amount_collect > 0 || $amount_bug > 0) {
                $amount_dp = $amount_collect;
            }
            $summ_cash      = $amount_dp * $price_cash;
            $summ_cash      = round($summ_cash, 2);

            $summ = $slave->to_money($db->result($r, $i - 1, "summ"));
            if ($summ_cash != $summ) {
                $summ = $summ_cash;
                $db->query("UPDATE `J_DP_STR` SET `summ` = '$summ' WHERE `id` = '$str_id' LIMIT 1;");
            }

            $doc_sum += $summ_cash;
        }

        if ($n > 0) {
            $db->query("UPDATE `J_DP` SET `summ` = '$doc_sum' WHERE `id` = $dp_id AND `oper_status` = '30' AND `status` = '1';");
            if ($doc_sum == 0) {
                $db->query("UPDATE `J_DP` SET `status_dp` = '81' WHERE `id` = $dp_id;");
            }
        }

        return $doc_sum;
    }

    function updateDpWeightVolume($dp_id) { $db = DbSingleton::getDb();
        $sum_weight = 0; $sum_volume = 0; $empty_kol = 0;
        $dp_id = (int)$dp_id;
        $r = $db->query("SELECT `art_id`, `amount` FROM `J_DP_STR` WHERE `dp_id` = $dp_id;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $art_id = $db->result($r, $i - 1, "art_id");
            $amount = $db->result($r, $i - 1, "amount");
            [$weight, $volume] = $this->getArticleWightVolume($art_id);
            if ($weight == 0 || $volume == 0) {
                ++$empty_kol;
            }
            if ($weight > 0 && $amount > 0) {
                $sum_weight += ($weight * $amount);
            }
            if ($volume > 0 && $amount > 0) {
                $sum_volume += ($volume * $amount);
            }
        }
        if ($n > 0) {
            $db->query("UPDATE `J_DP` SET `weight` = '$sum_weight', `volume` = '$sum_volume' WHERE `id` = $dp_id AND `oper_status` = '30' AND `status` = '1';");
        }

        return array($sum_weight, $sum_volume, $empty_kol);
    }

    function getDpCashId($dp_id) { $db = DbSingleton::getDb();
        $cash_id = 2;
        $r = $db->query("SELECT `cash_id` FROM `J_DP` WHERE `id` = $dp_id LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $cash_id = $db->result($r, 0, "cash_id");
        }
        return $cash_id;
    }

    function getDpClient($dp_id) { $db = DbSingleton::getDb();
        $client_conto_id = 0;
        $r = $db->query("SELECT `client_id`, `client_conto_id` FROM `J_DP` WHERE `id` = $dp_id LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $client_id          = $db->result($r, 0, "client_id");
            $client_conto_id    = $db->result($r, 0, "client_conto_id");
            if ($client_conto_id == 0 && $client_id > 0) {
                $client_conto_id = $client_id;
            }
        }
        return $client_conto_id;
    }

    function getDpClientPriceLevels($dp_id) { $db = DbSingleton::getDb();
        $price_lvl = 0; $margin_price_lvl = 0; $price_suppl_lvl = 0; $margin_price_suppl_lvl = 0; $client_vat = 0;

        $r = $db->query("SELECT `client_id`, `client_conto_id` FROM `J_DP` WHERE `id` = $dp_id LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $client_id          = $db->result($r, 0, "client_id");
            $client_conto_id    = $db->result($r, 0, "client_conto_id");
            if ($client_conto_id == 0 && $client_id > 0) {
                $client_conto_id = $client_id;
            }
            if ($client_conto_id > 0) {
                $r1 = $db->query("SELECT * FROM `A_CLIENTS_CONDITIONS` WHERE `client_id` = '$client_conto_id' LIMIT 1;");
                $n1 = $db->num_rows($r1);
                if ($n1 == 1) {
                    $price_lvl              = $db->result($r1, 0, "price_lvl") + 1;
                    $margin_price_lvl       = $db->result($r1, 0, "margin_price_lvl");
                    $price_suppl_lvl        = $db->result($r1, 0, "price_suppl_lvl") + 1;
                    $margin_price_suppl_lvl = $db->result($r1, 0, "margin_price_suppl_lvl");
                    $client_vat             = $db->result($r1, 0, "client_vat");
                }
            }
        }

        return array($price_lvl, $margin_price_lvl, $price_suppl_lvl, $margin_price_suppl_lvl, $client_vat);
    }

    function showArticlesSearchDocumentList($art, $brand_id_sel, $search_type, $dp_id, $tpoint_id)
    {
        $db = DbSingleton::getTokoDb();
        $cat = new catalogue;
        $r = ""; $query = "";
        [$price_lvl, $margin_price_lvl, $price_suppl_lvl, $margin_price_suppl_lvl, $client_vat] = $this->getDpClientPriceLevels($dp_id);
        $n = 0; $list2 = ""; $suppl_storage_id = 0;

        $true_art_id = $this->getArtID($art, $brand_id_sel);

        $order_by = "";
        if (!empty($true_art_id)) {
            $order_by = " ORDER BY t2a.ART_ID = $true_art_id DESC;";
        }

        if ($search_type === "") {
            $search_type = 1;
        }

        if ($search_type == 0) {
            $art = $cat->clearArticle($art);
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
                WHERE t2c.SEARCH_NUMBER = '$art' $where_brand $group_brand 
                ORDER BY t2n.NAME ASC;";
                $r = $db->query($query);
                $n = $db->num_rows($r);
            }

            $one_result = 0;
            if ($n > 1 && ($brand_id_sel === "" || $brand_id_sel == 0)) {
                $where_brand = "";
                $list2 = $cat->showCatalogueBrandSelectDocumentList($r);
            }

            if ($n == 1) {
                $query = "SELECT t2b.BRAND_NAME, t2n.NAME, t2c.BRAND_ID, t2c.DISPLAY_NR, t2c.ART_ID, t2c.KIND, t2c.RELATION 
                FROM `T2_CROSS` t2c 
                     INNER JOIN `T2_BRANDS` t2b ON (t2b.BRAND_ID = t2c.BRAND_ID)
                     LEFT OUTER JOIN `T2_NAMES` t2n ON (t2n.ART_ID = t2c.ART_ID)
                WHERE t2c.SEARCH_NUMBER = '$art' $where_brand 
                ORDER BY t2n.NAME ASC;";
                $r = $db->query($query);
                $n = $db->num_rows($r);
                $one_result = 1;
            }

            if (($n > 1 && $brand_id_sel !== "") || $one_result == 1) {
                $ak = array(); $rk = array();
                $art_id_str = "";
                for ($i = 1; $i <= $n; $i++) {
                    $ART_ID     = $db->result($r, $i - 1, "ART_ID");
                    $KIND       = $db->result($r, $i - 1, "KIND");
                    $RELATION   = $db->result($r, $i - 1, "RELATION");
                    $art_id_str .= "'$ART_ID'";

                    if ($i < $n) {$art_id_str .= ",";}
                    if (($ak[$ART_ID] === "") || $KIND == 0) {$ak[$ART_ID] = $KIND;}
                    if (($rk[$ART_ID] === "") || $RELATION == 0) {$rk[$ART_ID] = $RELATION;}
                }

                $query = "SELECT t2a.ART_ID, t2a.BRAND_ID, t2a.ARTICLE_NR_DISPL, t2b.BRAND_NAME, IFNULL(t2n.NAME,'') as NAME, t2n.INFO, t2bc.BARCODE, gg.NAME as goods_group_name, SUM(`t2asc`.`AMOUNT`) as stock, 
                SUM(`t2asc`.`RESERV_AMOUNT`) as reserv, s.name as storage_name, s.id as storage_id, t2apr.price_".$price_lvl.", t2si.suppl_id, t2si.return_delay, t2si.warranty_info, 
                t2si.price_usd, t2si.client_storage_id, t2si.stock_suppl
                FROM `T2_ARTICLES` t2a 
                    LEFT OUTER JOIN T2_BRANDS t2b on t2b.BRAND_ID=t2a.BRAND_ID 
                    LEFT OUTER JOIN T2_NAMES t2n on t2n.ART_ID=t2a.ART_ID 
                    LEFT OUTER JOIN T2_BARCODES t2bc on t2bc.ART_ID=t2a.ART_ID 
                    LEFT OUTER JOIN T2_GOODS_GROUP t2gg on t2gg.ART_ID=t2a.ART_ID 
                    LEFT OUTER JOIN GOODS_GROUP gg on gg.ID=t2gg.GOODS_GROUP_ID 
                    LEFT OUTER JOIN T2_ARTICLES_STRORAGE t2asc on t2asc.ART_ID=t2a.ART_ID 
                    LEFT OUTER JOIN T2_ARTICLES_PRICE_RATING t2apr on (t2apr.art_id=t2a.ART_ID and t2apr.in_use=1)
                    LEFT OUTER JOIN `T2_SUPPL_IMPORT` t2si ON (t2si.art_id=t2a.ART_ID AND t2si.status=1)
                    LEFT OUTER JOIN STORAGE s on s.id=t2asc.STORAGE_ID
                WHERE t2a.ART_ID IN ($art_id_str) AND t2b.`VISIBLE` = '1' AND (CASE WHEN t2n.LANG_ID != NULL THEN t2n.LANG_ID = 16 ELSE TRUE END) GROUP BY t2a.ART_ID $order_by";
            }
        }

        if ($search_type == 1) {
            $query = "SELECT t2a.ART_ID, t2a.BRAND_ID, t2a.ARTICLE_NR_DISPL, t2b.BRAND_NAME, t2n.NAME, t2n.INFO, t2bc.BARCODE, gg.NAME as goods_group_name, t2apr.price_".$price_lvl.", t2si.suppl_id, 
            t2si.return_delay, t2si.warranty_info, t2si.price_usd, t2si.client_storage_id, t2si.stock_suppl
            FROM `T2_ARTICLES` t2a 
                LEFT OUTER JOIN T2_BRANDS t2b on t2b.BRAND_ID=t2a.BRAND_ID 
                LEFT OUTER JOIN T2_NAMES t2n on t2n.ART_ID=t2a.ART_ID 
                LEFT OUTER JOIN T2_BARCODES t2bc on t2bc.ART_ID=t2a.ART_ID 
                LEFT OUTER JOIN T2_GOODS_GROUP t2gg on t2gg.ART_ID=t2a.ART_ID 
                LEFT OUTER JOIN GOODS_GROUP gg on gg.ID=t2gg.GOODS_GROUP_ID 
                LEFT OUTER JOIN T2_ARTICLES_PRICE_RATING t2apr on (t2apr.art_id=t2a.ART_ID and t2apr.in_use=1)
                LEFT OUTER JOIN `T2_SUPPL_IMPORT` t2si ON (t2si.art_id=t2a.ART_ID AND t2si.status=1)
            WHERE t2a.ARTICLE_NR_SEARCH='$art' OR t2a.ARTICLE_NR_DISPL='$art' AND t2b.`VISIBLE`='1' AND (CASE WHEN t2n.LANG_ID != NULL THEN t2n.LANG_ID = 16 ELSE TRUE END) GROUP BY t2a.ART_ID $order_by;";
        }

        if ($search_type == 2) {
            $query = "SELECT t2a.ART_ID, t2a.BRAND_ID, t2a.ARTICLE_NR_DISPL, t2b.BRAND_NAME, t2n.NAME, t2n.INFO, t2bc.BARCODE, gg.NAME as goods_group_name, SUM(`t2asc`.`AMOUNT`) as stock, 
            SUM(`t2asc`.`RESERV_AMOUNT`) as reserv, s.name as storage_name, s.id as storage_id, t2apr.price_".$price_lvl.", t2si.suppl_id, t2si.return_delay, t2si.warranty_info, t2si.price_usd, 
            t2si.client_storage_id, t2si.stock_suppl
            FROM `T2_ARTICLES` t2a 
                LEFT OUTER JOIN T2_BRANDS t2b on t2b.BRAND_ID=t2a.BRAND_ID 
                LEFT OUTER JOIN T2_NAMES t2n on t2n.ART_ID=t2a.ART_ID 
                LEFT OUTER JOIN T2_BARCODES t2bc on t2bc.ART_ID=t2a.ART_ID 
                LEFT OUTER JOIN T2_GOODS_GROUP t2gg on t2gg.ART_ID=t2a.ART_ID 
                LEFT OUTER JOIN GOODS_GROUP gg on gg.ID=t2gg.GOODS_GROUP_ID 
                LEFT OUTER JOIN T2_ARTICLES_STRORAGE t2asc on t2asc.ART_ID=t2a.ART_ID 
                LEFT OUTER JOIN T2_ARTICLES_PRICE_RATING t2apr on (t2apr.art_id=t2a.ART_ID and t2apr.in_use=1)
                LEFT OUTER JOIN `T2_SUPPL_IMPORT` t2si ON (t2si.art_id=t2a.ART_ID AND t2si.status=1)
                LEFT OUTER JOIN STORAGE s on s.id=t2asc.STORAGE_ID
            WHERE t2bc.BARCODE='$art' AND t2b.`VISIBLE`='1' AND (CASE WHEN t2n.LANG_ID != NULL THEN t2n.LANG_ID = 16 ELSE TRUE END) GROUP BY t2a.ART_ID $order_by;";
        }

        $r = $db->query($query);
        $n = $db->num_rows($r);
        $list = "";
        if ($list2 === "") {
            // сработал внешний фильр или основной поиск с выбором бренда
            for ($i = 1; $i <= $n; $i++) {
                $amountRestNotTp    = $amountRestTp = "";
                $price              = $suppl_storage_code = 0;
                $art_id             = $db->result($r, $i - 1, "ART_ID");
                $brand_id           = $db->result($r, $i - 1, "BRAND_ID");
                $article_nr_displ   = $db->result($r, $i - 1, "ARTICLE_NR_DISPL");
                $brand_name         = $db->result($r, $i - 1, "BRAND_NAME");
                $name               = $db->result($r, $i - 1, "NAME");
                $barcode            = $db->result($r, $i - 1, "BARCODE");
                $goods_group_name   = $db->result($r, $i - 1, "goods_group_name");
                $suppl_id           = $db->result($r, $i - 1, "suppl_id");
                if ($suppl_id == 0) {
                    $price = $db->result($r, $i - 1, "price_".$price_lvl);
                    if ($margin_price_lvl > 0) {
                        $price += round($price * $margin_price_lvl / 100, 2);
                    }
                    [$tpoint_stock, $tpoint_reserv] = $this->getArticleRestTpoint($art_id, $tpoint_id);
                    $amountRestTp = "$tpoint_stock/$tpoint_reserv";
                }
                if ($suppl_id > 0) {
                    $suppl_price_usd = $db->result($r, $i - 1, "price_usd");
                    $suppl_storage_id = $db->result($r, $i - 1, "client_storage_id");
                    $suppl_stock = $db->result($r, $i - 1, "stock_suppl");

                    [$price_in_vat, $show_in_vat, $price_add_vat] = $this->getSupplVatConditions($suppl_id);

                    $suppl_storage_code = $suppl_id . "." . $suppl_storage_id;
                    $price_suppl = $suppl_price_usd;
                    //Step 1;
                    [$suppl_margin_fm, $suppl_delivery_fm, $suppl_margin2_fm] = $this->getTpointSupplFm($tpoint_id, $suppl_id, $suppl_storage_id, $price_suppl, $price_suppl_lvl);
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
                        //Step 3; VAT //$price_in_vat,$show_in_vat,$price_add_vat
                        if ($client_vat == 1) {
                            if ($price_in_vat == 0 && $show_in_vat == 1 && $price_add_vat == 1) {
                                $price = $price + $price * $this->vat_percent / 100;
                            }
                            if ($price_in_vat == 0 && $show_in_vat == 0) {
                                $price = 0;
                            }
                        }
                    }
                    $price = round($price, 2);
                    $suppl_stock_show = $suppl_stock;
                    if ($suppl_stock_show >= 10) {
                        $suppl_stock_show = ">10";
                    }
                    $amountRestNotTp = "$suppl_stock_show / ";
                }

                if ($suppl_id == 0 || ($suppl_id > 0 && $price > 0)) {
                    $function = "setArticleToSelectAmountDp(\"$art_id\",\"$article_nr_displ\",\"$brand_id\",\"$brand_name\",\"$dp_id\")";
                    if ($suppl_id > 0) {
                        $function = "showDpSupplAmountInputWindow(\"$art_id\",\"$article_nr_displ\",\"$brand_id\",\"$brand_name\",\"$dp_id\",\"$suppl_id\",\"$suppl_storage_id\",\"$price\")";
                    }
                    $list .= "<tr style='cursor:pointer' onclick='$function'>
                        <td class='text-center'>$article_nr_displ</td>
                        <td class='text-center'>$brand_name</td>
                        <td class='text-center'>$name</td>
                        <td class='text-center'>$price</td>
                        <td class='text-center'>$amountRestTp</td>
                        <td class='text-right'>$amountRestNotTp</td>
                        <td class='text-center'>$suppl_storage_code</td>
                        <td class='text-center'>$barcode</td>
                        <td class='text-center'>$goods_group_name</td>
                        <td class='text-center'>$art_id</td>
                    </tr>";
                }
            }
        }

        return array($list, $list2);
    }

    function getSupplVatConditions($suppl_id) { $db = DbSingleton::getDb();
        $price_in_vat = 0; $show_in_vat = 0; $price_add_vat = 0;
        $r = $db->query("SELECT `price_in_vat`, `show_in_vat`, `price_add_vat` FROM `A_CLIENTS_VAT_CONDITIONS` WHERE `client_id`='$suppl_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $price_in_vat   = $db->result($r, 0, "price_in_vat");
            $show_in_vat    = $db->result($r, 0, "show_in_vat");
            $price_add_vat  = $db->result($r, 0, "price_add_vat");
        }
        return array($price_in_vat, $show_in_vat, $price_add_vat);
    }

    function getTpointSupplFm($tpoint_id, $suppl_id, $suppl_storage_id, $price_suppl, $price_suppl_lvl) { $dbt = DbSingleton::getTokoDb();
        $margin = 0; $delivery = 0; $margin2 = 0;
        $r = $dbt->query("SELECT `margin`, `delivery`, `margin2` 
        FROM `T_POINT_SUPPL_FM` 
        WHERE `tpoint_id` = '$tpoint_id' AND `suppl_id` = '$suppl_id' AND `suppl_storage_id` = '$suppl_storage_id' 
        AND `price_from` <= '$price_suppl' AND `price_to` >= '$price_suppl' AND `price_rating_id` = '$price_suppl_lvl' LIMIT 1;");
        $n = $dbt->num_rows($r);
        if ($n == 1) {
            $margin     = $dbt->result($r, 0, "margin");
            $delivery   = $dbt->result($r, 0, "delivery");
            $margin2    = $dbt->result($r, 0, "margin2");
        }
        return array($margin, $delivery, $margin2);
    }

    function setArticleToSelectAmountDp($art_id, $dp_id) {
        $form = ""; $form_htm = RD . "/tpl/dp_select_amount_article_form.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $form = str_replace(array("{dp_rest_storage_list}", "{art_id}"), array($this->showArticleRestStorageSelectList($art_id, $dp_id), $art_id), $form);

        return $form;
    }

    function showArticleRestStorageSelectText($art_id, $storage_id, $input_amount, $amountEx = null) { $db = DbSingleton::getTokoDb();
        $info = ""; $max_moving = $amount = 0;
        $r = $db->query("SELECT s.id, s.name, t2as.AMOUNT, t2as.RESERV_AMOUNT 
        FROM `STORAGE` s 
            INNER JOIN `T2_ARTICLES_STRORAGE` t2as ON (t2as.STORAGE_ID = s.id) 
        WHERE s.status = '1' AND t2as.ART_ID = '$art_id' AND t2as.STORAGE_ID = '$storage_id' 
        ORDER BY s.name ASC, s.id ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $amount         = $db->result($r, $i - 1, "AMOUNT");
            $reserv_amount  = $db->result($r, $i - 1, "RESERV_AMOUNT");
            $max_moving     = $amount + $amountEx;
            $info           = "Залишок: $amount | Резерв: $reserv_amount<br>У поточному записі: $input_amount";
        }

        return array($info, $max_moving, $amount);
    }

    function getDpTpoint($dp_id) { $db = DbSingleton::getDb();
        $tpoint_id = 0;
        $r = $db->query("SELECT `tpoint_id` FROM `J_DP` WHERE `id` = $dp_id LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $tpoint_id = $db->result($r, 0, "tpoint_id");
        }
        return $tpoint_id;
    }

    function getArticleTpointDeliveryInfo($tpoint_id, $art_id) { $db = DbSingleton::getTokoDb();
        $slave = new slave;
        $info = "Не вказано";
        $week_day = date("N");
        $cur_time = date("H:i:s");
        $r = $db->query("SELECT dt.* 
        FROM `T_POINT_DELIVERY_TIME` dt 
            LEFT OUTER JOIN `STORAGE` s ON (s.id = dt.storage_id)
            LEFT OUTER JOIN `T2_ARTICLES_STRORAGE` t2as ON (t2as.STORAGE_ID = s.id)
        WHERE dt.status = '1' AND dt.tpoint_id = '$tpoint_id' AND dt.week_day = '$week_day' AND dt.time_from <= '$cur_time' AND dt.time_to >= '$cur_time' AND s.status = '1' AND t2as.ART_ID = '$art_id' 
        ORDER BY dt.delivery_days ASC LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $delivery_days  = $db->result($r, 0, "delivery_days");
            $time_from_del  = substr($db->result($r, 0, "time_from_del"), 0, -3);
            $time_to_del    = substr($db->result($r, 0, "time_to_del"), 0, -3);
            $week           = date('N', strtotime(' + '.$delivery_days.' days'));
            $week_day_short = $slave->get_weekday_abr($week);
            $date_del       = date('d.m', strtotime(' + '.$delivery_days.' days'));
            $info           = "$delivery_days - дн. $date_del ($week_day_short)<br>з $time_from_del до $time_to_del";
        }

        return $info;
    }

    function getTpointDeliveryInfo($tpoint_id, $storage_id) { $db = DbSingleton::getDb();
        $slave = new slave;
        $info = "Не вказано";
        $week_day = date("N");
        $cur_time = date("H:i:s");
        $r = $db->query("SELECT `delivery_days`, `week_day`, `time_from_del`, `time_to_del` 
        FROM `T_POINT_DELIVERY_TIME` 
        WHERE `status` = '1' AND `tpoint_id` = '$tpoint_id' AND `storage_id` = '$storage_id' AND `week_day` = '$week_day' AND `time_from` <= '$cur_time' AND `time_to` >= '$cur_time' 
        ORDER BY `delivery_days` ASC LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $delivery_days  = $db->result($r, 0, "delivery_days");
            $time_from_del  = substr($db->result($r, 0, "time_from_del"), 0, -3);
            $time_to_del    = substr($db->result($r, 0, "time_to_del"), 0, -3);
            $week           = date('N', strtotime(' + '.$delivery_days.' days'));
            $week_day_short = $slave->get_weekday_abr($week);
            $date_del       = date('d.m', strtotime(' + '.$delivery_days.' days'));
            $info           = "$delivery_days - дн. $date_del ($week_day_short)<br>з $time_from_del до $time_to_del";
        }

        return $info;
    }

    function getTpointSupplDeliveryInfo($tpoint_id, $suppl_id, $suppl_storage_id) { $db = DbSingleton::getDb();
        $slave = new slave;
        $info = "Не вказано";
        $week_day = date("N");
        $cur_time = date("H:i:s");
        $r = $db->query("SELECT `delivery_days`, `week_day`, `time_from_del`, `time_to_del` 
        FROM `T_POINT_SUPPL_DELIVERY_TIME`
        WHERE `status` = '1' AND `tpoint_id` = '$tpoint_id' AND `suppl_storage_id` = '$suppl_storage_id' AND `suppl_id` = '$suppl_id' AND `week_day` = '$week_day' AND `time_from` <= '$cur_time' AND `time_to` >= '$cur_time' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $delivery_days  = $db->result($r, 0, "delivery_days");
            $time_from_del  = substr($db->result($r, 0, "time_from_del"), 0, -3);
            $time_to_del    = substr($db->result($r, 0, "time_to_del"), 0, -3);
            $week           = date('N', strtotime(' + '.$delivery_days.' days'));
            $week_day_short = $slave->get_weekday_abr($week);
            $date_del       = date('d.m', strtotime(' + '.$delivery_days.' days'));
            $info           = "$delivery_days - дн. $date_del ($week_day_short) з $time_from_del до $time_to_del";
        }

        return $info;
    }

    function getArticleStorageAmountDp($art_id, $dp_id, $storage_id) { $db = DbSingleton::getDb();
        $amount = 0;
        $r = $db->query("SELECT `amount` FROM `J_DP_STR` 
        WHERE `dp_id` = '$dp_id' AND `status_dps` = 93 AND `storage_id_from` = '$storage_id' AND `art_id` = '$art_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $amount = $db->result($r, 0, "amount");
        }

        return $amount;
    }

    function getArticleSupplStorageAmountDp($art_id, $dp_id, $suppl_id, $suppl_storage_id) { $db = DbSingleton::getDb();
        $amount = 0;
        $r = $db->query("SELECT `amount` FROM `J_DP_STR` 
        WHERE `dp_id` = '$dp_id' AND `status_dps` = 93 AND `suppl_id` = '$suppl_id' AND `suppl_storage_id` = '$suppl_storage_id' AND `art_id` = '$art_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $amount = $db->result($r, 0, "amount");
        }

        return $amount;
    }

    function showArticleRestStorageSelectList($art_id, $dp_id) { $db = DbSingleton::getTokoDb();
        $list = "";
        $tpoint_id = $this->getDpTpoint($dp_id);
        $r = $db->query("SELECT s.id, s.name, t2as.AMOUNT, t2as.RESERV_AMOUNT 
        FROM `STORAGE` s 
            LEFT OUTER JOIN `T2_ARTICLES_STRORAGE` t2as ON (t2as.STORAGE_ID = s.id) 
        WHERE s.status = '1' AND t2as.ART_ID = '$art_id' 
        ORDER BY s.name ASC, s.id ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id             = $db->result($r, $i - 1, "id");
            $name           = $db->result($r, $i - 1, "name");
            $amount         = $db->result($r, $i - 1, "AMOUNT");
            $reserv_amount  = $db->result($r, $i - 1, "RESERV_AMOUNT");
            $cur_amount     = $this->getArticleStorageAmountDp($art_id, $dp_id, $id);
            $rs_amount_rest = $reserv_amount - $cur_amount;
            $style          = ($cur_amount > 0) ? "style='background:pink;'" : "";
            $delivery_info  = $this->getTpointDeliveryInfo($tpoint_id,$id);
            $amount         = (int)$amount;
            $cur_amount     = (int)$cur_amount;
            $rs_amount_rest = (int)$rs_amount_rest;
            if ($amount != 0 || $cur_amount != 0 || $rs_amount_rest != 0) {
                $list .= "<tr onClick=\"showDpAmountInputWindow('$art_id','$id');\" style='cursor:pointer'>
                    <td>$i <input type='hidden' id='storage_amount_id' value='$id'></td>
                    <td>$name</td>
                    <td>$amount</td>
                    <td $style>$cur_amount</td>
                    <td>$rs_amount_rest</td>
                    <td>$delivery_info</td>
                </tr>";
            }
        }

        return $list;
    }

    function showDpAmountInputWindow($art_id, $dp_id, $storage_id) {
        $form = ""; $form_htm = RD . "/tpl/dp_amount_window.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $form = str_replace("{art_id}", $art_id, $form);
        $amount = (int)$this->getArticleStorageAmountDp($art_id, $dp_id, $storage_id);
        $form = str_replace("{amount}", ($amount === 0) ? "" : $amount, $form);
        return $form;
    }

    function showDpSupplAmountInputWindow($art_id, $article_nr_displ, $brand_id, $dp_id, $suppl_id, $suppl_storage_id, $price) {
        $cat = new catalogue;
        $form = ""; $form_htm = RD . "/tpl/dp_amount_suppl_window.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $amount = $this->getArticleSupplStorageAmountDp($art_id, $dp_id, $suppl_id, $suppl_storage_id);
        $form = str_replace("{art_id}", $art_id, $form);
        $form = str_replace("{amount}", $amount, $form);
        $form = str_replace("{price}", $price, $form);
        $form = str_replace("{summ}", $amount * $price, $form);
        $form = str_replace("{article_nr_displ}", $article_nr_displ, $form);
        $form = str_replace("{brand_id}", $brand_id, $form);
        $form = str_replace("{brand_name}", $this->getBrandName($brand_id), $form);
        $form = str_replace("{suppl_id}", $suppl_id, $form);
        $form = str_replace("{suppl_storage_id}", $suppl_storage_id, $form);
        $form = str_replace("{suppl_storage_code}", $cat->getSupplStorageName($suppl_storage_id) . " ($suppl_id.$suppl_storage_id)", $form);
        $form = str_replace("{suppl_delivery_info}", $this->getTpointSupplDeliveryInfo($this->getDpTpoint($dp_id), $suppl_id, $suppl_storage_id), $form);

        return $form;
    }

    function showArticleRestStorageCellsList($art_id, $storage_id) { $db = DbSingleton::getTokoDb();
        $list = "<option value='0'>-- Оберіть зі списку --</option>";
        $r = $db->query("SELECT sc.id, sc.cell_value, t2asc.AMOUNT, t2asc.RESERV_AMOUNT, t2as.AMOUNT as AMOUNT_STORAGE, t2as.RESERV_AMOUNT as RESERV_AMOUNT_STORAGE
        FROM `STORAGE_CELLS` sc
            LEFT OUTER JOIN `T2_ARTICLES_STRORAGE_CELLS` t2asc ON ( t2asc.STORAGE_CELLS_ID = sc.id )
            LEFT OUTER JOIN `T2_ARTICLES_STRORAGE` t2as ON ( t2as.STORAGE_ID = sc.storage_id )
        WHERE sc.status = '1' AND t2asc.ART_ID = '$art_id' AND t2as.ART_ID = '$art_id' AND sc.storage_id = '$storage_id' 
        ORDER BY sc.cell_value ASC, sc.id ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id                     = $db->result($r, $i - 1, "id");
            $name                   = $db->result($r, $i - 1, "cell_value");
            $amount                 = $db->result($r, $i - 1, "AMOUNT");
            $reserv_amount          = $db->result($r, $i - 1, "RESERV_AMOUNT");
            $amount_storage         = $db->result($r, $i - 1, "AMOUNT_STORAGE");
            $reserv_amount_storage  = $db->result($r, $i - 1, "RESERV_AMOUNT_STORAGE");
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

    function showStorageCellsList($storage_id, $exclude_id) { $db = DbSingleton::getTokoDb();
        $list = "<option value='0'>-- Оберіть зі списку --</option>";
        $r = $db->query("SELECT `id`, `cell_value` 
        FROM `STORAGE_CELLS` 
        WHERE `status` = '1' AND `storage_id` = '$storage_id' AND `id` <> '$exclude_id' 
        ORDER BY `cell_value` ASC, `id` ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id     = $db->result($r, $i - 1, "id");
            $name   = $db->result($r, $i - 1, "cell_value");
            $list   .= "<option value='$id'>$name</option>";
        }
        return $list;
    }

    function getArticleName($art_id) { $db = DbSingleton::getTokoDb();
        $name = "";
        $r = $db->query("SELECT `NAME` FROM `T2_NAMES` WHERE `ART_ID` = $art_id AND `LANG_ID` = 41 LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $name = $db->result($r, 0, "NAME");
        }
        if ($n == 0) {
            $r = $db->query("SELECT `NAME` FROM `T2_NAMES` WHERE `ART_ID` = $art_id AND `LANG_ID` = 16 LIMIT 1;");
            $name = $db->result($r, 0, "NAME");
        }
        return $name;
    }

    function getArticleWightVolume($art_id) { $db = DbSingleton::getTokoDb();
        $art_id = (int)$art_id;
        $weight = 0; $volume = 0; $weight2 = 0;
        $r = $db->query("SELECT `VOLUME`, `WEIGHT_BRUTTO`, `WEIGHT_NETTO` FROM `T2_PACKAGING` WHERE `ART_ID` = $art_id LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $weight     = $db->result($r, 0, "WEIGHT_BRUTTO");
            $weight2    = $db->result($r, 0, "WEIGHT_NETTO");
            $volume     = $db->result($r, 0, "VOLUME");
        }
        return array($weight, $volume, $weight2);
    }

    function getArticleReservType($tpoint_id, $storage_id) { $db = DbSingleton::getTokoDb();
        $reserv_type_id = 68;
        $r = $db->query("SELECT `local` FROM `T_POINT_STORAGE` WHERE `tpoint_id` = '$tpoint_id' AND `status` = '1' AND `storage_id` = '$storage_id' LIMIT 1;");
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
            LEFT OUTER JOIN `T_POINT_STORAGE` tps ON (tps.storage_id = t2as.STORAGE_ID) 
        WHERE t2as.ART_ID = '$art_id' AND tps.`tpoint_id` = '$tpoint_id' AND tps.status = '1';");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $stock      += $db->result($r, $i - 1, "stock");
            $reserv     += $db->result($r, $i - 1, "reserv");
            $storage_id = $db->result($r, 0, "STORAGE_ID");
        }
        return array($stock, $reserv, $storage_id);
    }

    function getArticleRestNotTpoint($art_id, $tpoint_id) { $db = DbSingleton::getTokoDb();
        $stock = 0; $reserv = 0; $storage_id = 0;
        $r = $db->query("SELECT SUM(t2as.`AMOUNT`) as stock, SUM(t2as.`RESERV_AMOUNT`) as reserv, t2as.STORAGE_ID 
        FROM `T2_ARTICLES_STRORAGE` t2as 
            LEFT OUTER JOIN `T_POINT_STORAGE` tps ON (tps.storage_id = t2as.STORAGE_ID) 
        WHERE t2as.ART_ID = '$art_id' AND tps.`tpoint_id` != '$tpoint_id' AND tps.status = '1';");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $stock      += $db->result($r, $i - 1, "stock");
            $reserv     += $db->result($r, $i - 1, "reserv");
            $storage_id = $db->result($r, 0, "STORAGE_ID");
        }
        return array($stock, $reserv, $storage_id);
    }

    function getArticleRestStorage($art_id, $storage_id) { $db = DbSingleton::getTokoDb();
        $stock = 0; $reserv = 0;
        if ($storage_id === "") {
            $storage_id = 0;
        }
        $r = $db->query("SELECT SUM(`AMOUNT`) as stock, SUM(`RESERV_AMOUNT`) as reserv 
        FROM `T2_ARTICLES_STRORAGE` 
        WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $stock  += $db->result($r, $i - 1, "stock");
            $reserv += $db->result($r, $i - 1, "reserv");
        }
        return array($stock, $reserv);
    }

    function getArticleRestStorageCell($art_id, $storage_id, $cell_id) { $db = DbSingleton::getTokoDb();
        $stock = 0; $reserv = 0;
        if ($storage_id === "") {
            $storage_id = 0;
        }
        if ($cell_id === "") {
            $cell_id = 0;
        }
        $r = $db->query("SELECT `AMOUNT` as stock, `RESERV_AMOUNT` as reserv 
        FROM `T2_ARTICLES_STRORAGE_CELLS` 
        WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id AND `STORAGE_CELLS_ID` = $cell_id LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $stock  = $db->result($r, 0, "stock");
            $reserv = $db->result($r, 0, "reserv");
        }
        return array($stock, $reserv);
    }

    function getStorageName($sel_id) { $db = DbSingleton::getTokoDb();
        $name = "";
        $r = $db->query("SELECT `name` FROM `STORAGE` WHERE `status` = '1' AND `id` = '$sel_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $name = $db->result($r, 0, "name");
        }
        return $name;
    }

    function showStorageSelectList($sel_id, $cells_only = 0) { $db = DbSingleton::getTokoDb();
        $list = "<option value=0>Оберіть зі списку</option>";
        $query = "SELECT * FROM `STORAGE` WHERE `status`='1' ORDER BY `name`, `id` ASC;";
        if ($cells_only == 1) {
            $query = "SELECT s.* FROM `STORAGE` s 
                INNER JOIN `STORAGE_STR` ss ON ss.storage_id=s.id 
            WHERE s.status='1' GROUP BY ss.storage_id ORDER BY s.name, s.id ASC;";
        }
        $r = $db->query($query);
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id     = $db->result($r, $i - 1, "id");
            $name   = $db->result($r, $i - 1, "name");
            $sel    = ($sel_id == $id) ? "selected='selected'" : "";
            $list   .= "<option value='$id' $sel>$name</option>";
        }
        return $list;
    }

    function getStorageCellName($sel_id) { $db = DbSingleton::getTokoDb();
        $name = "";
        $r = $db->query("SELECT `cell_value` FROM `STORAGE_CELLS` WHERE `status` = '1' AND `id` = '$sel_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $name = $db->result($r, 0, "cell_value");
        }
        return $name;
    }

    function showStorageCellsSelectList($storage_id, $sel_id) { $db = DbSingleton::getTokoDb();
        $list = "<option value=0>Оберіть зі списку</option>";
        $cells_show = 1;
        $r = $db->query("SELECT `id`, `cell_value` FROM `STORAGE_CELLS` WHERE `status` = '1' AND `storage_id` = '$storage_id' ORDER BY `cell_value`, `id` ASC;");
        $n = $db->num_rows($r);
        if ($n == 0) {
            $cells_show = 0;
        }
        for ($i = 1; $i <= $n; $i++) {
            $id     = $db->result($r, $i - 1, "id");
            $cell   = $db->result($r, $i - 1, "cell_value");
            $sel    = ($sel_id == $id) ? "selected='selected'" : "";
            $list   .= "<option value='$id' $sel>$cell</option>";
        }
        return array($list, $cells_show);
    }

    function getCashAbr($sel_id) { $db = DbSingleton::getDb();
        $name = "грн";
        $r = $db->query("SELECT `abr` FROM `CASH` WHERE `id` = '$sel_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $name = $db->result($r, 0, "abr");
        }
        return $name;
    }

    function showCashListSelect($sel_id) { $db = DbSingleton::getDb();
        $list = "";
        $r = $db->query("SELECT `id`, `abr` FROM `CASH` ORDER BY `name` ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id     = $db->result($r, $i - 1, "id");
            $name   = $db->result($r, $i - 1, "abr");
            $sel    = ($sel_id == $id) ? "selected='selected'" : "";
            $list   .= "<option value='$id' $sel>$name</option>";
        }
        return $list;
    }

    function getDocTypeSelectList($sel_id) { $db = DbSingleton::getDb();
        $list = "<option value=0>Оберіть зі списку</option>";
        $r = $db->query("SELECT `id`, `mcaption` FROM `manual` WHERE `ison` = '1' AND `key` = 'client_sale_type' ORDER BY `mid`, `id` ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id     = $db->result($r, $i - 1, "id");
            $name   = $db->result($r, $i - 1, "mcaption");
            $sel    = ($sel_id == $id) ? "selected='selected'" : "";
            $list   .= "<option value='$id' $sel>$name</option>";
        }
        return $list;
    }

    function getCarrierSelectList($sel_id) { $db = DbSingleton::getDb();
        $list = "";
        $r = $db->query("SELECT `id`, `mcaption` FROM `manual` WHERE `key` = 'carrier_id';");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id     = $db->result($r, $i - 1, "id");
            $name   = $db->result($r, $i - 1, "mcaption");
            $sel    = ($sel_id == $id) ? "selected='selected'" : "";
            $list   .= "<option value='$id' $sel>$name</option>";
        }
        return $list;
    }

    function getClientContoSelectList($client_id, $sel_id) { $db = DbSingleton::getDb();
        $list = "";
        if ($client_id > 0) {
            $r = $db->query("SELECT `id`, `name` 
            FROM `A_CLIENTS` 
            WHERE `status`='1' AND (`parrent_id` = '$client_id' OR `id` = '$client_id' OR `id` = '$sel_id') 
            ORDER BY `name`, `id` ASC;");
            $n = $db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $id     = $db->result($r, $i - 1, "id");
                $name   = $db->result($r, $i - 1, "name");
                $sel    = ($sel_id == $id) ? "selected='selected'" : "";
                $list   .= "<option value='$id' $sel>$name</option>";
            }
        }
        return $list;
    }

    function loadDpCommets($dp_id) { $db = DbSingleton::getDb();
        $form = ""; $form_htm = RD . "/tpl/dp_comment_block.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $list = "";
        $r = $db->query("SELECT cc.*, u.name 
        FROM `J_DP_COMMENTS` cc 
            LEFT OUTER JOIN `media_users` u ON (u.id = cc.USER_ID) 
        WHERE cc.dp_id = '$dp_id' 
        ORDER BY cc.id DESC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id         = $db->result($r, $i - 1, "id");
            $user_id    = $db->result($r, $i - 1, "user_id");
            $user_name  = $db->result($r, $i - 1, "name");
            $data       = $db->result($r, $i - 1, "data");
            $comment    = $db->result($r, $i - 1, "comment");

            $block = $form;
            $block = str_replace("{dp_id}", $dp_id, $block);
            $block = str_replace("{id}", $id, $block);
            $block = str_replace("{user_id}", $user_id, $block);
            $block = str_replace("{user_name}", $user_name, $block);
            $block = str_replace("{data}", $data, $block);
            $block = str_replace("{comment}", $comment, $block);
            $list .= $block;
        }
        if ($n == 0) {
            $list = "<h3 class='text-center'>Коментарі відсутні</h3>";
        }
        return $list;
    }

    function saveDpComment($dp_id, $comment) { $db = DbSingleton::getDb();
        $slave = new slave;
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $answer = 0; $err = "Помилка збереження даних!";
        $dp_id = $slave->qq($dp_id); $comment = $slave->qq($comment);
        if ($dp_id > 0 && $comment !== "") {
            $db->query("INSERT INTO `J_DP_COMMENTS` (`dp_id`,`user_id`,`comment`) VALUES ('$dp_id','$user_id','$comment');");
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

    function dropDpComment($dp_id, $comment_id) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка видалення запису!";
        $dp_id = $slave->qq($dp_id); $comment_id = $slave->qq($comment_id);
        if ($dp_id > 0 && $comment_id > 0) {
            $r = $db->query("SELECT * FROM `J_DP_COMMENTS` WHERE `dp_id` = '$dp_id' AND `id` = '$comment_id' LIMIT 1;");
            $n = $db->num_rows($r);
            if ($n == 1) {
                $db->query("DELETE FROM `J_DP_COMMENTS` WHERE `dp_id` = '$dp_id' AND `id` = '$comment_id';");
                $answer = 1; $err = "";
            }
        }
        return array($answer, $err);
    }

    function loadDpCDN($dp_id) { $db = DbSingleton::getDb();
        $form = ""; $form_htm = RD . "/tpl/dp_cdn_block.htm";
        if (file_exists($form_htm)){$form = file_get_contents($form_htm);}
        $list = "";
        $r = $db->query("SELECT cc.*, u.name as user_name 
        FROM `J_DP_CDN` cc 
            LEFT OUTER JOIN `media_users` u ON (u.id = cc.USER_ID)
        WHERE cc.dp_id = '$dp_id' AND cc.status = '1' 
        ORDER BY cc.file_name ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $file_id    = $db->result($r, $i - 1, "id");
            $file_name  = $db->result($r, $i - 1, "file_name");
            $name       = $db->result($r, $i - 1, "name");
            $data       = $db->result($r, $i - 1, "data");
            $user_name  = $db->result($r, $i - 1, "user_name");
            $link       = "http://portal.myparts.pro/cdn/dp_files/$dp_id/$file_name";
            $file_view  = "<div class=\"icon\"><i class=\"fa fa-file\"></i></div>";
            $exten      = pathinfo($file_name, PATHINFO_EXTENSION);
            if ($exten === "jpg" || $exten === "jpeg" || $exten === "png" || $exten === "gif" || $exten === "bmp" || $exten === "svg") {
                $file_view = "<div class=\"image\"><img alt=\"image\" class=\"img-responsive\" src=\"$link\"></div>";
            }
            $block = $form;
            $block = str_replace(array("{file_id}", "{file_name}", "{user_name}", "{data}", "{dp_id}", "{link}", "{file_view}"), array($file_id, $name, $user_name, $data, $dp_id, $link, $file_view), $block);
            $list .= $block;
        }
        if ($n == 0) {
            $list = "<h3 class='text-center'>Файли відсутні</h3>";
        }
        return $list;
    }

    function dpCDNDropFile($dp_id, $file_id) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка видалення файлу!";
        $dp_id = $slave->qq($dp_id);
        $file_id = $slave->qq($file_id);
        if ($dp_id > 0 && $file_id > 0) {
            $r = $db->query("SELECT `FILE_NAME` FROM `J_DP_CDN` WHERE `dp_id` = '$dp_id' AND `id` = '$file_id' LIMIT 1;");
            $n = $db->num_rows($r);
            if ($n == 1) {
                unlink(RD . '/cdn/dp_files/$dp_id/$file_name');
                $db->query("DELETE FROM `J_DP_CDN` WHERE `dp_id` = '$dp_id' AND `id` = '$file_id';");
                $answer = 1; $err = "";
            }
        }
        return array($answer, $err);
    }

    function loadStateSelectList($country_id, $sel_id) {
        $slave = new slave;
        return $slave->showSelectSubList("T2_STATE", "COUNTRY_ID", "$country_id", "STATE_ID", "STATE_NAME", $sel_id);
    }

    function loadRegionSelectList($state_id, $sel_id) {
        $slave = new slave;
        return $slave->showSelectSubList("T2_REGION", "STATE_ID", "$state_id", "REGION_ID", "REGION_NAME", $sel_id);
    }

    function loadCitySelectList($region_id, $sel_id){
        $slave = new slave;
        return "<option value='NEW'>Добавити населений пункт</option>".$slave->showSelectSubList("T2_CITY", "REGION_ID", "$region_id", "CITY_ID", "CITY_NAME", $sel_id);
    }

    function checkdpCategorySelect($dp_id, $category_id) { $db = DbSingleton::getDb();
        $r = $db->query("SELECT `category_id` FROM `A_CLIENTS_CATEGORY` WHERE `dp_id` = '$dp_id' AND `category_id` = '$category_id' LIMIT 1;");
        $n = $db->num_rows($r);
        $ch = 0;
        if ($n == 1) {
            $ch = 1;
        }
        return $ch;
    }

    function labelArtEmptyCount($dp_id, $kol) {
        $label = "";
        if ($kol === 0 || $kol === "") {
            [, , $kol] = $this->updateDpWeightVolume($dp_id);
        }
        if ($kol > 0) {
            $label = "<span class='label label-tab label-info'>$kol</span>";
        }
        return array($kol, $label);
    }

    function labelCommentsCount($dp_id) { $db = DbSingleton::getDb();
        $r = $db->query("SELECT COUNT(`id`) as kol FROM `J_DP_COMMENTS` WHERE `dp_id` = '$dp_id';");
        $kol = 0 + $db->result($r, 0, "kol");
        $label = "";
        if ($kol > 0) {
            $label = "<span class='label label-tab label-info'>$kol</span>";
        }
        return array($kol, $label);
    }

    function getTpointDataByStorage($storage_id) { $db = DbSingleton::getDb();
        $tpoint_id = 0; $loc_type_id = 0;
        $r = $db->query("SELECT `tpoint_id`, `local` FROM `T_POINT_STORAGE` WHERE `storage_id` = '$storage_id' ORDER BY `id` ASC LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $tpoint_id      = $db->result($r, 0, "tpoint_id");
            $loc_type_id    = $db->result($r, 0, "local");
        }
        return array($tpoint_id, $loc_type_id);
    }

    function getdpInfo($dp_id) { $db = DbSingleton::getDb();
        $r = $db->query("SELECT `prefix`, `doc_nom`, `data`, `storage_id_to`, `comment` FROM `J_DP` WHERE `id` = $dp_id LIMIT 1;");
        $prefix             = $db->result($r, 0, "prefix");
        $doc_nom            = $db->result($r, 0, "doc_nom");
        $data               = $db->result($r, 0, "data");
        $storage_id_to      = $db->result($r, 0, "storage_id_to");
        $storage_name_to    = $this->getStorageName($storage_id_to);
        $comment            = $db->result($r, 0, "comment");
        return array($prefix, $doc_nom, $data, $storage_id_to, $storage_name_to, $comment);
    }

    function checkUKTZED($art_id) { $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT * FROM `T2_ZED` WHERE `ART_ID` = $art_id AND `COSTUMS_ID` > 0 LIMIT 1;");
        $n = $db->num_rows($r);
        return ($n > 0);
    }

    function checkUKRNAME($art_id) { $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT * FROM `T2_NAMES` WHERE `ART_ID` = $art_id AND `LANG_ID` = 41 LIMIT 1;");
        $n = $db->num_rows($r);
        return ($n > 0);
    }

    function getArticleInfo($art_id) { $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT t2a.ARTICLE_NR_DISPL, t2a.ARTICLE_NR_SEARCH, t2b.BRAND_NAME 
        FROM `T2_ARTICLES` t2a 
            LEFT JOIN `T2_BRANDS` t2b ON (t2b.BRAND_ID = t2a.BRAND_ID)
        WHERE t2a.ART_ID = $art_id LIMIT 1;");
        $n = $db->num_rows($r);
        $article_nr_displ   = $db->result($r, 0, "ARTICLE_NR_DISPL");
        $article_nr_search  = $db->result($r, 0, "ARTICLE_NR_SEARCH");
        $brand_name         = $db->result($r, 0, "BRAND_NAME");
        return ($n > 0) ? "<a href='https://portal.myparts.pro/Catalogue/$article_nr_search'>$article_nr_displ $brand_name</a>" : "";
    }

    function checkDpStr($dp_id) { $db = DbSingleton::getDb();
        $err = 0;
        $arr = [];
        $r = $db->query("SELECT `art_id` FROM `J_DP_STR` WHERE `dp_id` = $dp_id;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $art_id = $db->result($r, $i - 1, "art_id");
            if (!$this->checkUKTZED($art_id)) {
                $arr["zed"][] = $art_id;
                $err++;
            }
            if (!$this->checkUKRNAME($art_id)) {
                $arr["ukr"][] = $art_id;
                $err++;
            }
        }
        return array($err, $arr);
    }

    function showDPDocErrorForm($dp_id) {
        [, $arr] = $this->checkDpStr($dp_id);
        $list = "";
        if (!empty($arr)) {
            foreach ($arr as $key => $value) {
                if ($key === "zed") {
                    $list .= "<div><h3>Відсутній код УКТЗЕД в артикулах:</h3><ul>";
                }
                if ($key === "ukr") {
                    $list .= "<div><h3>Відсутнє укр найменування в артикулах:</h3><ul>";
                }
                foreach ($value as $art_id) {
                    $info = $this->getArticleInfo($art_id);
                    $list .= "<li>$info</li>";
                }
                if ($key === "zed") {
                    $list .= "</ul></div>";
                }
                if ($key === "ukr") {
                    $list .= "</ul></div>";
                }
            }
        }
        return $list;
    }

    /*
     * передати в роботу (в складський відбір)
     * */
    function startDpExecute($dp_id)
    {
        $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка індексу!";
        $suppl_ex = 0; $doc_err = 0;
        $dp_id = $slave->qq($dp_id);
        if ($dp_id > 0) {
            $r = $db->query("SELECT `tpoint_id`, `status_dp`, `doc_type_id` FROM `J_DP` WHERE `id` = $dp_id LIMIT 1;");
            $n = $db->num_rows($r);
            if ($n == 1) {
                $status_dp      = $db->result($r, 0, "status_dp");
                $tpoint_id      = $db->result($r, 0, "tpoint_id");
                $doc_type_id    = $db->result($r, 0, "doc_type_id");
                if ($status_dp == 80) {
                    $answer = 0; $err = "Документ уже передано в роботу!";
                }
                if ($status_dp == 79) {
                    // безготівковий з пдв
                    if ($doc_type_id == 61) {
                        // перевірка на уктзед і найменування
                        [$doc_err] = $this->checkDpStr($dp_id);
                    }
                    [$usd_to_uah, $eur_to_uah] = $this->getKoursData();
                    $db->query("UPDATE `J_DP` SET `status_dp` = '80', `usd_to_uah` = '$usd_to_uah', `eur_to_uah` = '$eur_to_uah' WHERE `id` = $dp_id LIMIT 1;");
                    $suppl_ex = $this->checkRemoteStorage($dp_id, $tpoint_id);
                    if ($suppl_ex == 0) {
                        $this->makeDpJmovingStorselPreorder($dp_id, 41);
                    }
                    $answer = 1; $err = "";
                }
            }
        }

        return array($answer, $err, $suppl_ex, $doc_err);
    }

    function checkDpSaleInvoice($dp_id)
    {
        $db = DbSingleton::getDb();
        $doc_err = 0;
        $r = $db->query("SELECT `doc_type_id` FROM `J_DP` WHERE `id` = $dp_id LIMIT 1;");
        $doc_type_id = $db->result($r, 0, "doc_type_id");
        if ($doc_type_id == 61) {
            [$doc_err] = $this->checkDpStr($dp_id);
        }

        return ($doc_err > 0);
    }

    function checkRemoteStorage($dp_id, $tpoint_id)
    {
        $db = DbSingleton::getDb();
        $suppl_ex = 0;
        $r = $db->query("SELECT COUNT(ds.id) as kol 
        FROM `J_DP_STR` ds 
            LEFT OUTER JOIN `T_POINT_STORAGE` ps ON (ps.storage_id=ds.storage_id_from) 
        WHERE ds.dp_id = '$dp_id' AND ((ps.tpoint_id = '$tpoint_id' AND ps.`local` = '42') OR (ps.tpoint_id != '$tpoint_id'));");
        $kol_rem_str = $db->result($r, 0, "kol");
        if ($kol_rem_str > 0) {
            $suppl_ex = 1;
        }
        return $suppl_ex;
    }

    // 42 - локальне переміщення
    // 41 - між скдалами
    function getStorageToTpointLocal($tpoint_id, $storage_id)
    {
        $db = DbSingleton::getDb();
        $local = 42;
        $r = $db->query("SELECT `local` FROM `T_POINT_STORAGE` WHERE `tpoint_id` = '$tpoint_id' AND `storage_id` = '$storage_id' AND `status` = '1' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $local = $db->result($r, 0, "local");
        }
        return $local;
    }

    function createStorsel($dp_id, $tpoint_id, $storage_id)
    {
        $db = DbSingleton::getDb();
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $cur_date = date("Y-m-d H:i:s");
        $rm = $db->query("SELECT MAX(`id`) as mid FROM `J_SELECT`;");
        $select_id = 0 + $db->result($rm, 0, "mid") + 1;
        $db->query("INSERT INTO `J_SELECT` (`id`,`parrent_doc_type_id`,`parrent_doc_id`,`data_create`,`tpoint_id`,`storage_id`,`user_create`) VALUES ('$select_id','2','$dp_id','$cur_date','$tpoint_id','$storage_id','$user_id');");
        return $select_id;
    }

    /*
     * Передати ДП в складський відбір
     * в переміщення
     * */
    function makeDpJmovingStorselPreorder($dp_id, $local, $dp_note = "")
    {
        $db = DbSingleton::getDb();
        $dbt = DbSingleton::getTokoDb();
        $jmoving = new jmoving;
        $slave = new slave;
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $tpoint_id = $this->getDpTpoint($dp_id);
        $cell_use = 0;
        $dp_id = $slave->qq($dp_id);

        $r = $db->query("SELECT `storage_id_from` FROM `J_DP_STR` WHERE `dp_id` = $dp_id AND `status_dps` = 93 GROUP BY `storage_id_from`;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $storage_id_from = $db->result($r, $i - 1, "storage_id_from") + 0;

            if ($storage_id_from > 0) {
                $storage_local = $this->getStorageToTpointLocal($tpoint_id, $storage_id_from);

                if ($storage_local == 41) {
                    $r1 = $db->query("SELECT * FROM `J_DP_STR` WHERE `dp_id` = $dp_id AND `status_dps` = 93 AND `storage_id_from` = $storage_id_from;");
                    $n1 = $db->num_rows($r1);

                    if ($n1 > 0) {
                        // create STORSEL
                        $select_id = $this->createStorsel($dp_id, $tpoint_id, $storage_id_from);
                        $s_volume = 0; $s_weight_netto = 0; $s_amount = 0; $s_articles_amount = 0;

                        for ($i1 = 1; $i1 <= $n1; $i1++) {
                            $id = $db->result($r1, $i1 - 1, "id") + 0;
                            $parrent_doc_type_id = 1; // J_DP
                            $parrent_doc_id = $id; //J_DP_STR ID
                            $art_id = $db->result($r1, $i1 - 1, "art_id");
                            $article_nr_displ = $db->result($r1, $i1 - 1, "article_nr_displ");
                            $brand_id = $db->result($r1, $i1 - 1, "brand_id");
                            $amount = $db->result($r1, $i1 - 1, "amount");
                            $weight_netto = $db->result($r1, $i1 - 1, "weight_netto");
                            $volume = $db->result($r1, $i1 - 1, "volume");
                            $s_amount += $amount;
                            ++$s_articles_amount;
                            $s_volume += $volume;
                            $s_weight_netto += $weight_netto;

                            // обновляем статус записи что бы понимать на каком этапе находится артикул;
                            $db->query("UPDATE `J_DP_STR` SET `status_dps` = 94 WHERE `id` = $id LIMIT 1;");

                            $rsc = $dbt->query("SELECT `AMOUNT`, `RESERV_AMOUNT`, `STORAGE_CELLS_ID` FROM `T2_ARTICLES_STRORAGE_CELLS` WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id_from;");
                            $nsc = $dbt->num_rows($rsc);

                            if ($nsc == 0) {
                                $r2 = $db->query("SELECT MAX(`id`) as mid FROM `J_SELECT_STR`;");
                                $str_id = 0 + $db->result($r2, 0, "mid") + 1;
                                $db->query("INSERT INTO `J_SELECT_STR` (`id`, `select_id`, `art_id`, `article_nr_displ`, `brand_id`, `amount`, `storage_id_from`, `parrent_doc_type_id`, `parrent_doc_id`) 
                                VALUES ($str_id, '$select_id', '$art_id', '$article_nr_displ', '$brand_id', '$amount', '$storage_id_from', '$parrent_doc_type_id', '$parrent_doc_id');");
                                $db->query("UPDATE `J_DP_STR` SET `cur_select_str_id` = $str_id WHERE `id` = $id AND `dp_id` = $dp_id LIMIT 1;");
                            }

                            if ($nsc > 0) {
                                for ($isc = 1; $isc <= $nsc; $isc++) {
                                    $er = 0;
                                    $amount_sc = $dbt->result($rsc, $isc - 1, "AMOUNT");
                                    $reserv_amount_sc = $dbt->result($rsc, $isc - 1, "RESERV_AMOUNT");
                                    $storage_cells_id_sc = $dbt->result($rsc, $isc - 1, "STORAGE_CELLS_ID");

                                    if ($amount_sc >= $amount && $amount_sc > 0) {
                                        $isc = $nsc + 1;
                                        $er = 1;
                                        $amount_sc -= $amount;
                                        $reserv_amount_sc += $amount;
                                        $r2 = $db->query("SELECT MAX(`id`) as mid FROM `J_SELECT_STR`;");
                                        $str_id = 0 + $db->result($r2, 0, "mid") + 1;
                                        $db->query("INSERT INTO `J_SELECT_STR` (`id`,`select_id`,`art_id`,`article_nr_displ`,`brand_id`,`amount`,`storage_id_from`,`cell_id_from`,`parrent_doc_type_id`,`parrent_doc_id`) 
                                        VALUES ('$str_id','$select_id','$art_id','$article_nr_displ','$brand_id','$amount','$storage_id_from','$storage_cells_id_sc','$parrent_doc_type_id','$parrent_doc_id');");
                                        $db->query("UPDATE `J_DP_STR` SET `cur_select_str_id` = '$str_id' WHERE `id` = '$id' AND `dp_id` = '$dp_id' LIMIT 1;");
                                        $dbt->query("UPDATE `T2_ARTICLES_STRORAGE_CELLS` SET `AMOUNT` = '$amount_sc', `RESERV_AMOUNT` = '$reserv_amount_sc' 
                                        WHERE `ART_ID` = '$art_id' AND `STORAGE_ID` = '$storage_id_from' AND `STORAGE_CELLS_ID` = '$storage_cells_id_sc' LIMIT 1;");
                                    }

                                    if ($amount_sc < $amount && $amount_sc > 0 && $er === 0) {
                                        $amount -= $amount_sc;
                                        $reserv_amount_sc += $amount_sc;
                                        $db->query("INSERT INTO `J_SELECT_STR` (`select_id`,`art_id`,`article_nr_displ`,`brand_id`,`amount`,`storage_id_from`,`cell_id_from`,`parrent_doc_type_id`,`parrent_doc_id`) 
                                        VALUES ('$select_id','$art_id','$article_nr_displ','$brand_id','$amount_sc','$storage_id_from','$storage_cells_id_sc','$parrent_doc_type_id','$parrent_doc_id');");
                                        $dbt->query("UPDATE `T2_ARTICLES_STRORAGE_CELLS` SET `AMOUNT` = '0', `RESERV_AMOUNT` = '$reserv_amount_sc' 
                                        WHERE `ART_ID` = '$art_id' AND `STORAGE_ID` = '$storage_id_from' AND `STORAGE_CELLS_ID` = '$storage_cells_id_sc' LIMIT 1;");
                                    }
                                }
                            }
                        }
                        $db->query("UPDATE `J_SELECT` SET `amount` = '$s_amount', `articles_amount` = '$s_articles_amount', `volume` = '$s_volume', `weight_netto` = '$s_weight_netto' WHERE `id` = '$select_id' LIMIT 1;");
                    }
                }

                if ($storage_local == 42 && $local == 41) {
                    $r1 = $db->query("SELECT * FROM `J_DP_STR` WHERE `dp_id` = $dp_id AND `status_dps` = 93 AND `storage_id_from` = '$storage_id_from';");
                    $n1 = $db->num_rows($r1);

                    if ($n1 > 0) {
                        //create JMOVING
                        $jmoving_id     = $jmoving->newJmovingCard(1);
                        $s_volume       = 0;
                        $s_weight_netto = 0;
                        $storage_id_to  = $this->getTpointStorageLocal($tpoint_id);
                        $cell_id_to     = $this->getStorageCellsData($storage_id_to);
                        // parrent_type_id: 1-dp, 2-vozvrat;
                        $db->query("UPDATE `J_MOVING` SET `parrent_type_id` = '1', `parrent_doc_id` = '$dp_id', `storage_id_to` = '$storage_id_to', `cell_use` = '$cell_use', `cell_id_to` = '$cell_id_to' WHERE `id` = '$jmoving_id' LIMIT 1;");
                        for ($i1 = 1; $i1 <= $n1; $i1++) {
                            $id = $db->result($r1, $i1 - 1, "id") + 0;
                            $art_id = $db->result($r1, $i1 - 1, "art_id");
                            $article_nr_displ = $db->result($r1, $i1 - 1, "article_nr_displ");
                            $brand_id = $db->result($r1, $i1 - 1, "brand_id");
                            $amount = $db->result($r1, $i1 - 1, "amount");
                            $weight_netto = $db->result($r1, $i1 - 1, "weight_netto");
                            $volume = $db->result($r1, $i1 - 1, "volume");
                            $s_volume += $volume;
                            $s_weight_netto += $weight_netto;
                            $db->query("INSERT INTO `J_MOVING_STR` (`jmoving_id`,`art_id`,`article_nr_displ`,`brand_id`,`amount`,`storage_id_from`) VALUES ('$jmoving_id','$art_id','$article_nr_displ','$brand_id','$amount','$storage_id_from');");
                            // обновляем статус записи что бы понимать на каком этапе находится артикул;
                            $db->query("UPDATE `J_DP_STR` SET `status_dps` = 95 WHERE `id` = $id LIMIT 1;");
                        }
                        $jmoving->startJmovingStorageSelect($jmoving_id);
                        $jmoving->makesJmovingStorageSelect($jmoving_id);
                    }
                }

                if ($storage_local == 42 && $local == 42) {
                    //$r11=$db->query("select * from J_DP_STR where dp_id='$dp_id';");
                    $r1 = $db->query("SELECT * FROM `J_DP_STR` WHERE `dp_id` = $dp_id AND `status_dps` = 93 AND `storage_id_from` = '$storage_id_from';");
                    $n1 = $db->num_rows($r1);

                    if ($n1 > 0) {
                        //get storage tpoint_id
                        [$storage_tpoint_id,] = $this->getTpointDataByStorage($storage_id_from);
                        //creating new dp from current dp
                        $storage_dp_id = $this->newDpFromDp($dp_id, $storage_tpoint_id, $dp_note); //$storage_id_from
                        //update summ weight volume of dp_id
                        $this->updateDpSumm($dp_id);
                        $this->updateDpWeightVolume($dp_id);
                        //update summ weight volume of storage_dp_id
                        $this->updateDpSumm($storage_dp_id);
                        $this->updateDpWeightVolume($storage_dp_id);
                        //move dp_str_id t new dp_id;
                        $db->query("UPDATE `J_DP_STR` SET `dp_id` = '$storage_dp_id' WHERE `dp_id` = $dp_id AND `status_dps` = 93 AND `storage_id_from` = '$storage_id_from';");
                        //update dp status
                        $db->query("UPDATE `J_DP` SET `status_dp` = 80 WHERE `id` = '$storage_dp_id' LIMIT 1;");
                        //set dp kours
                        [$usd_to_uah, $eur_to_uah] = $this->getKoursData();
                        $db->query("UPDATE `J_DP` SET `usd_to_uah` = '$usd_to_uah', `eur_to_uah` = '$eur_to_uah' WHERE `id` = $dp_id LIMIT 1;");
                        // create LOCAL STORSEL
                        $r2 = $db->query("SELECT * FROM `J_DP_STR` WHERE `dp_id` = '$storage_dp_id' AND `status_dps` = 93 AND `storage_id_from` = '$storage_id_from';");
                        $n2 = $db->num_rows($r2);

                        if ($n2 > 0) {
                            $select_id = $this->createStorsel($storage_dp_id, $storage_tpoint_id, $storage_id_from);
                            $s_volume = 0; $s_weight_netto = 0; $s_amount = 0; $s_articles_amount = 0;
                            for ($i2 = 1; $i2 <= $n2; $i2++) {
                                $id = $db->result($r2, $i2 - 1, "id") + 0;
                                $art_id = $db->result($r2, $i2 - 1, "art_id");
                                $article_nr_displ = $db->result($r2, $i2 - 1, "article_nr_displ");
                                $brand_id = $db->result($r2, $i2 - 1, "brand_id");
                                $amount = $db->result($r2, $i2 - 1, "amount");
                                $weight_netto = $db->result($r2, $i2 - 1, "weight_netto");
                                $volume = $db->result($r2, $i2 - 1, "volume");
                                $s_amount += $amount;
                                ++$s_articles_amount;
                                $s_volume += $volume;
                                $s_weight_netto += $weight_netto;
                                $parrent_doc_type_id = 1; // J_DP
                                $parrent_doc_id = $id; //J_DP_STR ID
                                // обновляем статус записи что бы понимать на каком этапе находится артикул;
                                $db->query("UPDATE `J_DP_STR` SET `status_dps` = 94 WHERE `id` = $id LIMIT 1;");

                                $rsc=$dbt->query("SELECT * FROM `T2_ARTICLES_STRORAGE_CELLS` WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id_from;");
                                $nsc = $dbt->num_rows($rsc);

                                if ($nsc == 0) {
                                    $r3 = $db->query("SELECT MAX(`id`) as mid FROM `J_SELECT_STR`;");
                                    $str_id = 0 + $db->result($r3, 0, "mid") + 1;
                                    $db->query("INSERT INTO `J_SELECT_STR` (`id`,`select_id`,`art_id`,`article_nr_displ`,`brand_id`,`amount`,`storage_id_from`,`parrent_doc_type_id`,`parrent_doc_id`) 
                                    VALUES ('$str_id','$select_id','$art_id','$article_nr_displ','$brand_id','$amount','$storage_id_from','$parrent_doc_type_id','$parrent_doc_id');");
                                    $db->query("UPDATE `J_DP_STR` SET `cur_select_str_id`='$str_id' WHERE `id`='$id' AND `dp_id`='$storage_dp_id' LIMIT 1;");
                                }

                                if ($nsc > 0) {
                                    for ($isc = 1; $isc <= $nsc; $isc++) {
                                        $er = 0;
                                        $amount_sc = $dbt->result($rsc, $isc - 1, "AMOUNT");
                                        $reserv_amount_sc = $dbt->result($rsc, $isc - 1, "RESERV_AMOUNT");
                                        $storage_cells_id_sc = $dbt->result($rsc, $isc - 1, "STORAGE_CELLS_ID");

                                        if ($amount_sc >= $amount && $amount_sc > 0) {
                                            $isc = $nsc + 1; $er = 1;
                                            $amount_sc -= $amount;
                                            $reserv_amount_sc += $amount;
                                            $r3 = $db->query("SELECT MAX(`id`) as mid FROM `J_SELECT_STR`;");
                                            $str_id = 0 + $db->result($r3, 0, "mid") + 1;
                                            $db->query("INSERT INTO `J_SELECT_STR` (`id`,`select_id`,`art_id`,`article_nr_displ`,`brand_id`,`amount`,`storage_id_from`,`cell_id_from`,`parrent_doc_type_id`,`parrent_doc_id`) 
                                            VALUES ('$str_id','$select_id','$art_id','$article_nr_displ','$brand_id','$amount','$storage_id_from','$storage_cells_id_sc','$parrent_doc_type_id','$parrent_doc_id');");
                                            $db->query("UPDATE `J_DP_STR` SET `cur_select_str_id`='$str_id' WHERE `id`='$id' and `dp_id`='$storage_dp_id' LIMIT 1;");
                                            $dbt->query("UPDATE `T2_ARTICLES_STRORAGE_CELLS` SET `AMOUNT`='$amount_sc', `RESERV_AMOUNT`='$reserv_amount_sc' 
                                            WHERE `ART_ID`='$art_id' AND `STORAGE_ID`='$storage_id_from' AND `STORAGE_CELLS_ID`='$storage_cells_id_sc' LIMIT 1;");
                                        }

                                        if ($amount_sc < $amount && $amount_sc > 0 && $er === 0) {
                                            $amount -= $amount_sc;
                                            $reserv_amount_sc += $amount_sc;
                                            $db->query("INSERT INTO `J_SELECT_STR` (`select_id`,`art_id`,`article_nr_displ`,`brand_id`,`amount`,`storage_id_from`,`cell_id_from`,`parrent_doc_type_id`,`parrent_doc_id`) 
                                            VALUES ('$select_id','$art_id','$article_nr_displ','$brand_id','$amount_sc','$storage_id_from','$storage_cells_id_sc','$parrent_doc_type_id','$parrent_doc_id');");
                                            $dbt->query("UPDATE `T2_ARTICLES_STRORAGE_CELLS` SET `AMOUNT`='0', `RESERV_AMOUNT`='$reserv_amount_sc' 
                                            WHERE `ART_ID`='$art_id' AND `STORAGE_ID`='$storage_id_from' AND `STORAGE_CELLS_ID`='$storage_cells_id_sc' LIMIT 1;");
                                        }
                                    }
                                }
                            }

                            $db->query("UPDATE `J_SELECT` SET `amount` = '$s_amount', `articles_amount` = '$s_articles_amount', `volume` = '$s_volume', `weight_netto` = '$s_weight_netto' 
                            WHERE `id` = '$select_id' LIMIT 1;");
                        }

                        $this->updateDpSumm($storage_dp_id);
                        $this->updateDpWeightVolume($storage_dp_id);
                    }
                }
            }

            if ($storage_id_from == 0) {
                $dp_cash_id = $this->getDpCashId($dp_id);
                $r1 = $db->query("SELECT `id`, `suppl_id`, `suppl_storage_id`, `art_id`, `article_nr_displ`, `brand_id`, `amount`, `price_end` FROM `J_DP_STR` 
                WHERE `dp_id` = $dp_id AND `status_dps` = 93 AND `storage_id_from` = '$storage_id_from' AND `suppl_id` > 0;");
                $n1 = $db->num_rows($r1);
                if ($n1 > 0) {
                    for ($i1 = 1; $i1 <= $n1; $i1++) {
                        $id             = $db->result($r1, $i1 - 1, "id") + 0;
                        $suppl_id       = $db->result($r1, $i1 - 1, "suppl_id");
                        $suppl_st_id    = $db->result($r1, $i1 - 1, "suppl_storage_id");
                        $art_id         = $db->result($r1, $i1 - 1, "art_id");
                        $art_nr_ds      = $db->result($r1, $i1 - 1, "article_nr_displ");
                        $brand_id       = $db->result($r1, $i1 - 1, "brand_id");
                        $brand_name     = $this->getBrandName($brand_id);
                        $amount         = $db->result($r1, $i1 - 1, "amount");
                        $price_end      = $db->result($r1, $i1 - 1, "price_end");
                        $suppl_art_id   = $art_nr_ds; //temporary

                        $db->query("INSERT INTO `J_DP_SUPPL_ORDER` (`dp_id`,`dp_str_id`,`art_id`,`article_nr_displ`,`SUPPL_ART_ID`,`brand_id`,`brand_name`,`amount`,`price`,`cash_id`,`suppl_id`,`suppl_storage_id`,`tpoint_id`,`media_user_id`) 
                        VALUES ('$dp_id','$id','$art_id','$art_nr_ds','$suppl_art_id','$brand_id','$brand_name','$amount','$price_end','$dp_cash_id','$suppl_id','$suppl_st_id','$tpoint_id','$user_id');"); // обновляем статус записи что бы
                        // обновляем статус записи что бы понимать на каком этапе находится артикул;
                        $db->query("UPDATE `J_DP_STR` SET `status_dps` = 139 WHERE `id` = $id LIMIT 1;");
                    }
                }
            }
        }
        
        $answer = 1; $err = "";

        return array($answer, $err);
    }

    /*
     * Достати дефолтний склад
     * */
    function getTpointStorageLocal($tpoint_id)
    {
        $db = DbSingleton::getDb();
        $storage_id = 0;
        $r = $db->query("SELECT `storage_id` FROM `T_POINT_STORAGE` WHERE `tpoint_id` = '$tpoint_id' AND `local` = '41' AND `default` = '1' AND `status` = '1' LIMIT 1;");
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

    /*
     * Достати дефолтну ячейку
     * */
    function getStorageCellsData($storage_id)
    {
        $db = DbSingleton::getDb();
        $cell_id = 0;
        $r = $db->query("SELECT `id` FROM `STORAGE_CELLS` WHERE `storage_id` = $storage_id AND `default` = 1 AND `status` = 1 LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $cell_id = $db->result($r, 0, "id");
        }
        if ($n == 0) {
            $r1 = $db->query("SELECT `id` FROM `STORAGE_CELLS` WHERE `storage_id` = $storage_id AND `default` = 0 AND `status` = 1 LIMIT 1;");
            $n1 = $db->num_rows($r1);
            if ($n1 == 1) {
                $cell_id = $db->result($r1, 0, "id");
            }
        }
        return $cell_id;
    }

    function getSelectFromJmoving($doc_nom) { $db = DbSingleton::getDb();
        $r = $db->query("SELECT `id` FROM `J_SELECT` WHERE `parrent_doc_type_id` = 1 AND `parrent_doc_id` = $doc_nom LIMIT 1;");
        $n = $db->num_rows($r);
        return ($n > 0) ? $db->result($r, 0, "id") : "";
    }

    /*
     * Віддалене переміщення
     */
    function loadDpJmoving($dp_id) { $db = DbSingleton::getDb();
        $gmanual = new gmanual;
        $form = ""; $form_htm = RD . "/tpl/dp_jmoving_list.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $list = "";
        $r = $db->query("SELECT j.*, s.`name` as storage_name, sc.`storage_id`, sc.`cell_value` 
        FROM `J_MOVING` j
            LEFT OUTER JOIN `STORAGE` s ON (s.id = j.storage_id_to)
            LEFT OUTER JOIN `STORAGE_CELLS` sc ON (sc.id = j.cell_id_to)
        WHERE j.status = 1 AND j.parrent_type_id = '1' AND j.parrent_doc_id = '$dp_id' 
        ORDER BY j.status_jmoving ASC, j.data DESC, j.doc_nom DESC, j.id DESC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r, $i - 1, "id");
            $select_id = $this->getSelectFromJmoving($id);
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
            if ($storage_name === "") {
                $storage_name = $this->getStorageName($storage_id_to);
            }
            $cell_value = $db->result($r, $i - 1, "cell_value");
            $data = $db->result($r, $i - 1, "data");
            $user_name = $this->getMediaUserName($db->result($r, $i - 1, "user_id"));
            $status_jmoving = $gmanual->get_gmanual_caption($db->result($r, $i - 1, "status_jmoving"));
            $function = ($type_id == 0) ? "showJmovingCardLocal(\"$id\")" : "showJmovingCard(\"$id\")";

            $rd = $db->query("SELECT `doc_type_id` FROM `J_DP` WHERE `id` = $dp_id LIMIT 1;");
            $doc_type_id = $db->result($rd, 0, "doc_type_id");
            if ($doc_type_id == 63) {
                $print_id = $select_id;
            } else {
                $print_id = $id;
            }
            if ($print_id == "") {
                $print_id = $id;
            }

            $onclick = "printStorselView(\"$print_id\");";
            if ($doc_type_id == 64) {
                $onclick = "printStorselView(\"$select_id\");";
            }

            $list .= "
            <tr style='cursor: pointer' onClick='$function'>
                <td>$type_name</td>
                <td>$prefix - $doc_nom</td>
                <td align='center'>$data</td>
                <td>$storage_name $cell_value</td>
                <td>$user_name</td>
                <td align='center'>
                    <button class='btn btn-xs btn-primary' onClick='$onclick'><i class='fa fa-print'></i></button>
                </td>
                <td>$status_jmoving</td>
            </tr>";
        }
        if ($n == 0) {
            $list = "<tr><td colspan=7 align='center'>Віддалені переміщення відсутні</td></tr>";
        }
        $form = str_replace("{jmoving_list}", $list, $form);

        return $form;
    }

    function dpStorselCount($dp_id) { $db = DbSingleton::getDb();
        $r = $db->query("SELECT sel.*, s.name as storage_name, t.name as tpoint_name 
        FROM `J_SELECT` sel
            LEFT OUTER JOIN `T_POINT` t ON (t.id = sel.tpoint_id)
            LEFT OUTER JOIN `STORAGE` s ON (s.id = sel.storage_id)
        WHERE sel.status = 1 AND sel.parrent_doc_type_id = '2' AND sel.parrent_doc_id = '$dp_id' 
        ORDER BY sel.status_select ASC, sel.data_create DESC, sel.id DESC;");
        $n = $db->num_rows($r);
        if ($n == 0) {
            $n = "";
        }
        return $n;
    }

    function dpJmovingCount($dp_id) { $db = DbSingleton::getDb();
        $r = $db->query("SELECT j.*, s.name as storage_name, sc.storage_id, sc.`cell_value` 
        FROM `J_MOVING` j
            LEFT OUTER JOIN `STORAGE` s ON (s.id = j.storage_id_to)
            LEFT OUTER JOIN `STORAGE_CELLS` sc ON (sc.id = j.cell_id_to)
        WHERE j.status = 1 AND j.parrent_type_id = '1' AND j.parrent_doc_id = '$dp_id' 
        ORDER BY j.status_jmoving ASC, j.data DESC, j.doc_nom DESC, j.id DESC;");
        $n = $db->num_rows($r);
        if ($n == 0) {
            $n = "";
        }
        return $n;
    }

    function loadDpStorsel($dp_id) { $db = DbSingleton::getDb();
        $gmanual = new gmanual;
        $list = ""; $loc_type_name = "";
        $form = ""; $form_htm = RD . "/tpl/dp_storsel_list.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $r = $db->query("SELECT sel.*, s.name as storage_name, t.name as tpoint_name 
        FROM `J_SELECT` sel
            LEFT OUTER JOIN `T_POINT` t ON (t.id = sel.tpoint_id)
            LEFT OUTER JOIN `STORAGE` s ON (s.id = sel.storage_id)
        WHERE sel.status = 1 AND sel.parrent_doc_type_id = '2' AND sel.parrent_doc_id = '$dp_id' 
        ORDER BY sel.status_select ASC, sel.data_create DESC, sel.id DESC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id             = $db->result($r, $i - 1, "id");
            $select_id      = $this->getSelectFromJmoving($id);
            $tpoint_name    = $db->result($r, $i - 1, "tpoint_name");
            $storage_name   = $db->result($r, $i - 1, "storage_name");
            $article_amount = $db->result($r, $i - 1, "articles_amount");
            $amount         = $db->result($r, $i - 1, "amount");
            $volume         = $db->result($r, $i - 1, "volume");
            $weight_netto   = $db->result($r, $i - 1, "weight_netto");
            $weight_brutto  = $db->result($r, $i - 1, "weight_brutto");
            $status_select  = $db->result($r, $i - 1, "status_select");
            $status_sel_cap = $gmanual->get_gmanual_caption($status_select);

            $rd = $db->query("SELECT `doc_type_id` FROM `J_DP` WHERE `id` = $dp_id LIMIT 1;");
            $doc_type_id = $db->result($rd, 0, "doc_type_id");
            if ($doc_type_id == 63) {
                $print_id = $select_id;
            } else {
                $print_id = $id;
            }
            if ($print_id === "") {
                $print_id = $id;
            }
            $list .= "
            <tr id='strStsRow_$i'>
                <td>$i</td>
                <td style='min-width:140px;'>СкВ-$id</td>
                <td style='min-width:140px;'>$tpoint_name</td>
                <td style='min-width:120px;'>$storage_name</td>
                <td style='min-width:80px;'>$loc_type_name</td>
                <td align='center' style='min-width:80px;'>$article_amount</td>
                <td align='center' style='min-width:80px;'>$amount</td>
                <td align='right'>$volume</td>
                <td align='right'>$weight_netto</td>
                <td align='right'>$weight_brutto</td>
                <td align='center'>
                    <button class='btn btn-xs btn-primary' onClick='viewDpStorageSelect(\"$dp_id\",\"$id\",\"$status_select\");'><i class='fa fa-eye'></i></button>
                    <button class='btn btn-xs btn-primary' onClick='printStorselView(\"$print_id\");'><i class='fa fa-print'></i></button>
                </td>
                <td align='center'>$status_sel_cap</td>
            </tr>";
        }
        if ($n == 0) {
            $list = "<tr><td colspan=12 align='center'>Локальні відбори відсутні</td></tr>";
        }

        $form = str_replace("{storsel_list}", $list, $form);

        return $form;
    }

    function viewDpStorageSelect($dp_id, $select_id, $select_status) { $db = DbSingleton::getDb();
        $storsel = new storsel;
        $gmanual = new gmanual;
        $list = "";
        $form = ""; $form_htm = RD . "/tpl/dp_storage_select_view.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $r = $db->query("SELECT * FROM `J_SELECT_STR` WHERE `select_id` = '$select_id' ORDER BY `id` ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r, $i - 1, "id");
            $art_id = $db->result($r, $i - 1, "art_id");
            $article_nr_displ = $db->result($r, $i - 1, "article_nr_displ");
            $brand_id = $db->result($r, $i - 1, "brand_id");
            $amount = $db->result($r, $i - 1, "amount");
            $storage_id_from = $db->result($r, $i - 1, "storage_id_from");
            $storage_name_from = $this->getStorageName($storage_id_from);
            $amount_barcodes = $db->result($r, $i - 1, "amount_barcodes");
            $amount_barcodes_noscan = $db->result($r, $i - 1, "amount_barcodes_noscan");
            $amount_bug = $db->result($r, $i - 1, "amount_bug");
            $amount_accept = $amount_barcodes + $amount_barcodes_noscan;
            $select_bug_list = $this->getStorageSelectBugList($select_id, $art_id, $id);
            $brand_name = $this->getBrandName($brand_id);
            $list .= "
            <tr align='right'>
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
        [, $data_create, $data_start, $data_collect, , , , , $volume, $weight_netto, $weight_brutto] = $storsel->getStorselInfo($select_id);
        $form = str_replace("{select_id}", $select_id, $form);
        $form = str_replace("{storage_select_dp}", $dp_id, $form);
        $form = str_replace("{data_create}", $data_create, $form);
        $form = str_replace("{data_start}", $data_start, $form);
        $form = str_replace("{data_collect}", $data_collect, $form);
        $form = str_replace("{volume}", $volume, $form);
        $form = str_replace("{weight_netto}", $weight_netto, $form);
        $form = str_replace("{weight_brutto}", $weight_brutto, $form);
        $form = str_replace("{ArticlesList}", $list, $form);

        return array($form, "Структура складського відбору № СкВ-$select_id; Статус відбору: " . $gmanual->get_gmanual_caption($select_status));
    }

    function getStorageSelectBugList($select_id, $art_id, $str_id) { $db = DbSingleton::getDb();
        $manual = new manual;
        $list = "";
        $r = $db->query("SELECT * FROM `J_SELECT_STR_BUG` WHERE `select_id` = '$select_id' AND `art_id` = '$art_id' AND `str_id` = '$str_id' ORDER BY `id` ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $storage_select_bug = $db->result($r, $i - 1, "storage_select_bug");
            $amount_bug = $db->result($r, $i - 1, "amount_bug");
            $storage_select_bug_name = $manual->getManualMCaption("storage_select_bug", $storage_select_bug);
            $list .= $amount_bug . "шт. - $storage_select_bug_name";
            if ($i < $n) {
                $list .= "<br>";
            }
        }
        return $list;
    }

    function loadDpSaleInvoice($dp_id) { $db = DbSingleton::getDb();
        $list = "";
        $form = ""; $form_htm = RD . "/tpl/dp_sale_invoice_list.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $r = $db->query("SELECT sv.*, t.name as tpoint_name, sl.name as seller_name, cl.name as client_name, dt.mvalue as doc_type_name, ch.abr2 as cash_abr 
        FROM `J_SALE_INVOICE` sv
            LEFT OUTER JOIN `CASH` ch ON (ch.id = sv.cash_id)
            LEFT OUTER JOIN `T_POINT` t ON (t.id = sv.tpoint_id)
            LEFT OUTER JOIN `A_CLIENTS` sl ON (sl.id = sv.seller_id)
            LEFT OUTER JOIN `A_CLIENTS` cl ON (cl.id = sv.client_conto_id)
            LEFT OUTER JOIN `manual` dt ON (dt.key = 'client_sale_type' AND dt.id = sv.doc_type_id)
        WHERE sv.status = 1 AND sv.dp_id = '$dp_id' 
        ORDER BY sv.status_invoice ASC, sv.data_create ASC, sv.id ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id             = $db->result($r, $i - 1, "id");
            $prefix         = $db->result($r, $i - 1, "prefix");
            $doc_nom        = $db->result($r, $i - 1, "doc_nom");
            $data_create    = $db->result($r, $i - 1, "data_create");
            $tpoint_name    = $db->result($r, $i - 1, "tpoint_name");
            $seller_name    = $db->result($r, $i - 1, "seller_name");
            $client_name    = $db->result($r, $i - 1, "client_name");
            $doc_type_name  = $db->result($r, $i - 1, "doc_type_name");
            $summ           = $db->result($r, $i - 1, "summ");
            $summ_debit     = $db->result($r, $i - 1, "summ_debit");
            $cash_abr       = $db->result($r, $i - 1, "cash_abr");
            $data_pay       = $db->result($r, $i - 1, "data_pay");
            $list .= "
            <tr id='strStsRow_$i' align='center'>
                <td>$i</td>
                <td>$prefix-$doc_nom</td>
                <td align='center'>$data_create</td>
                <td>$tpoint_name</td>
                <td align='left'>$seller_name</td>
                <td align='left'>$client_name</td>
                <td>$doc_type_name</td>
                <td align='center' style='min-width:80px;'>$summ$cash_abr</td>
                <td align='right'>$summ_debit$cash_abr</td>
                <td align='right'>$data_pay</td>
                <td align='center'>
                    <button class='btn btn-xs btn-primary' onClick='viewDpSaleInvoice(\"$dp_id\",\"$id\");'><i class='fa fa-eye'></i></button>
                    <button class='btn btn-xs btn-warning' onClick='openSaleInvoice(\"$id\");'><i class='fa fa-external-link-square'></i></button>
                </td>
            </tr>";
        }
        if ($n == 0) {
            $list = "<tr><td colspan=11 align='center'>Накладні відсутні</td></tr>";
        }
        $form = str_replace("{sale_invoice_list}", $list, $form);
        return $form;
    }

    /*
     * показати форму скл відборів для формування видаткової накладної
     * */
    function showDpStorselForSaleInvoice($dp_id) { $db = DbSingleton::getDb();
        $gmanual = new gmanual;
        $list = ""; $loc_type_name = "";
        $form = ""; $form_htm = RD . "/tpl/dp_storsel_list_for_sale_invoice.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $sel_use = "0";
        $r = $db->query("SELECT `select_id` FROM `J_SALE_INVOICE_STORSEL` WHERE `dp_id` = '$dp_id';");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $sel_use .= "," . $db->result($r, $i - 1, "select_id");
        }
        $r = $db->query("SELECT sel.*, s.name as storage_name, t.name as tpoint_name 
        FROM `J_SELECT` sel
            LEFT OUTER JOIN `T_POINT` t ON (t.id = sel.tpoint_id)
            LEFT OUTER JOIN `STORAGE` s ON (s.id = sel.storage_id)
        WHERE sel.status = 1 AND sel.parrent_doc_type_id = 2 AND sel.parrent_doc_id = '$dp_id' AND sel.status_select = '85' AND sel.id NOT IN ($sel_use) 
        ORDER BY sel.status_select ASC, sel.data_create DESC, sel.id DESC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id             = $db->result($r, $i - 1, "id");
            $tpoint_name    = $db->result($r, $i - 1, "tpoint_name");
            $storage_name   = $db->result($r, $i - 1, "storage_name");
            $article_amount = $db->result($r, $i - 1, "articles_amount");
            $amount         = $db->result($r, $i - 1, "amount");
            $volume         = $db->result($r, $i - 1, "volume");
            $weight_netto   = $db->result($r, $i - 1, "weight_netto");
            $weight_brutto  = $db->result($r, $i - 1, "weight_brutto");
            $status_select  = $db->result($r, $i - 1, "status_select");
            $status_sel_cap = $gmanual->get_gmanual_caption($status_select);
            $list .= "
            <tr id='strStsRow_$i'>
                <td><input type='checkbox' class='ch_dp_sts' id='dp_strosel_$i' value='$id' checked></td>
                <td>$i</td>
                <td style='min-width:140px;'>СкВ-$id</td>
                <td style='min-width:140px;'>$tpoint_name</td>
                <td style='min-width:120px;'>$storage_name</td>
                <td style='min-width:80px;'>$loc_type_name</td>
                <td align='center' style='min-width:80px;'>$article_amount</td>
                <td align='center' style='min-width:80px;'>$amount</td>
                <td align='right'>$volume</td>
                <td align='right'>$weight_netto</td>
                <td align='right'>$weight_brutto</td>
                <td align='center'><button class='btn btn-xs btn-primary' onClick='viewDpStorageSelect(\"$dp_id\",\"$id\",\"$status_select\");'><i class='fa fa-eye'></i></button></td>
                <td align='center'>$status_sel_cap</td>
            </tr>";
        }
        if ($n == 0) {
            $list = "<tr><td colspan=13 align='center'>Відбори відсутні</td></tr>";
        }
        $form = str_replace(array("{storsel_list}", "{dp_id}"), array($list, $dp_id), $form);

        $r = $db->query("SELECT sel.*, s.name as storage_name, t.name as tpoint_name 
        FROM `J_SELECT` sel
            LEFT OUTER JOIN `T_POINT` t ON (t.id = sel.tpoint_id)
            LEFT OUTER JOIN `STORAGE` s ON (s.id = sel.storage_id)
        WHERE sel.status = 1 AND sel.parrent_doc_type_id = 2 AND sel.parrent_doc_id = '$dp_id' AND sel.id NOT IN ($sel_use);");
        $kol_storsel = $db->num_rows($r);
        $form = str_replace("{kol_storsel}", $kol_storsel, $form);

        return $form;
    }

    function showDpStorselForWriteOff($dp_id) { $db = DbSingleton::getDb();
        $gmanual = new gmanual;
        $list = ""; $loc_type_name = "";
        $form = ""; $form_htm = RD . "/tpl/dp_storsel_list_for_write_off.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $sel_use = "0";
        $r = $db->query("SELECT `select_id` FROM `J_WRITE_OFF_STORSEL` WHERE `dp_id` = '$dp_id';");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $sel_use .= "," . $db->result($r, $i - 1, "select_id");
        }
        $r = $db->query("SELECT sel.*, s.name as storage_name, t.name as tpoint_name 
        FROM `J_SELECT` sel
            LEFT OUTER JOIN `T_POINT` t ON (t.id = sel.tpoint_id)
            LEFT OUTER JOIN `STORAGE` s ON (s.id = sel.storage_id)
        WHERE sel.status = 1 AND sel.parrent_doc_type_id = 2 AND sel.parrent_doc_id = '$dp_id' AND sel.status_select = '85' AND sel.id NOT IN ($sel_use) 
        ORDER BY sel.status_select ASC, sel.data_create DESC, sel.id DESC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id             = $db->result($r, $i - 1, "id");
            $tpoint_name    = $db->result($r, $i - 1, "tpoint_name");
            $storage_name   = $db->result($r, $i - 1, "storage_name");
            $article_amount = $db->result($r, $i - 1, "articles_amount");
            $amount         = $db->result($r, $i - 1, "amount");
            $volume         = $db->result($r, $i - 1, "volume");
            $weight_netto   = $db->result($r, $i - 1, "weight_netto");
            $weight_brutto  = $db->result($r, $i - 1, "weight_brutto");
            $status_select  = $db->result($r, $i - 1, "status_select");
            $status_sel_cap = $gmanual->get_gmanual_caption($status_select);
            $list .= "
            <tr id='strStsRow_$i'>
                <td><input type='checkbox' class='ch_dp_sts' id='dp_strosel_$i' value='$id' checked></td>
                <td>$i</td>
                <td style='min-width:140px;'>СкВ-$id</td>
                <td style='min-width:140px;'>$tpoint_name</td>
                <td style='min-width:120px;'>$storage_name</td>
                <td style='min-width:80px;'>$loc_type_name</td>
                <td align='center' style='min-width:80px;'>$article_amount</td>
                <td align='center' style='min-width:80px;'>$amount</td>
                <td align='right'>$volume</td>
                <td align='right'>$weight_netto</td>
                <td align='right'>$weight_brutto</td>
                <td align='center'><button class='btn btn-xs btn-primary' onClick='viewDpStorageSelect(\"$dp_id\",\"$id\",\"$status_select\");'><i class='fa fa-eye'></i></button></td>
                <td align='center'>$status_sel_cap</td>
            </tr>";
        }
        if ($n == 0) {
            $list = "<tr><td colspan=13 align=center>Відбори відсутні</td></tr>";
        }
        $form = str_replace(array("{storsel_list}", "{dp_id}"), array($list, $dp_id), $form);

        $status_write_off_select = "";
        $r = $db->query("SELECT * FROM `manual` WHERE `key`='write_off';");
        $n = $db->num_rows($r);
         for ($i = 1; $i <= $n; $i++) {
             $id    = $db->result($r, $i - 1, "id");
             $name  = $db->result($r, $i - 1, "mcaption");
             $status_write_off_select .= "<option value='$id'>$name</option>";
         }
        $form = str_replace("{status_write_off_select}", $status_write_off_select, $form);

        $r = $db->query("SELECT sel.*, s.name as storage_name, t.name as tpoint_name 
        FROM `J_SELECT` sel
            LEFT OUTER JOIN `T_POINT` t ON (t.id = sel.tpoint_id)
            LEFT OUTER JOIN `STORAGE` s ON (s.id = sel.storage_id)
        WHERE sel.status = 1 AND sel.parrent_doc_type_id = 2 AND sel.parrent_doc_id = '$dp_id' AND sel.id NOT IN ($sel_use);");
        $kol_storsel = $db->num_rows($r);
        $form = str_replace("{kol_storsel}", $kol_storsel, $form);

        return $form;
    }

    function checkClientSaleInvoiceDataPayLimit($client_id) { $db = DbSingleton::getDb();
        $doc_ex = 0;
        $r = $db->query("SELECT COUNT(`id`) as `kol` FROM `J_SALE_INVOICE` WHERE `client_conto_id` = '$client_id' AND `status_invoice` = '86' AND `data_pay` < CURDATE() AND `summ_debit` > 0;");
        $kol = $db->result($r, 0, "kol");
        if ($kol > 0) {
            $doc_ex = 1;
        }
        return $doc_ex;
    }

    function checkClientCreditLimitBeforeSaleInvoice($dp_id, $kol_storsel, $ar_storsel) { $db = DbSingleton::getDb();
        $client_saldo = 0; $credit_limit = 0; $summ_all = 0; $datapay_limit = 0;
        $r = $db->query("SELECT * FROM `J_DP` WHERE `id` = $dp_id LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $client_conto_id = $db->result($r, 0, "client_conto_id");
            $cash_id    = $db->result($r, 0, "cash_id");
            $usd_to_uah = $db->result($r, 0, "usd_to_uah");
            $eur_to_uah = $db->result($r, 0, "eur_to_uah");
            $status_dp  = $db->result($r, 0, "status_dp");

            [$usd_to_uah_new, $eur_to_uah_new] = $this->getKoursData();
            if ($status_dp == 79) {
                $usd_to_uah = $usd_to_uah_new;
                $eur_to_uah = $eur_to_uah_new;
            } else {
                if ($usd_to_uah == 0) {
                    $usd_to_uah = $usd_to_uah_new;
                }
                if ($eur_to_uah == 0) {
                    $eur_to_uah = $eur_to_uah_new;
                }
            }

            $select_str = "0";
            for ($i = 1; $i <= $kol_storsel; $i++) {
                if ($ar_storsel[$i] !== "" && $ar_storsel[$i] > 0) {
                    $select_str .= ",".$ar_storsel[$i];
                }
            }

            $summ_all = 0;
            $r2 = $db->query("SELECT jss.amount_collect as amount_col, jds.*, jss.storage_id_from as storage_id_from2, jss.cell_id_from as cell_id_from2
            FROM `J_SELECT_STR` jss 
                LEFT OUTER JOIN `J_SELECT` js ON (js.id = jss.select_id) 
                LEFT OUTER JOIN `J_DP` jd ON (jd.id = js.parrent_doc_id AND js.parrent_doc_type_id = 2)
                LEFT OUTER JOIN `J_DP_STR` jds ON (jds.dp_id = jd.id) 
            WHERE jss.select_id IN ($select_str) AND jds.art_id = jss.art_id 
            GROUP BY jds.art_id, storage_id_from2, cell_id_from2;");
            $n2 = $db->num_rows($r2);
            for ($i2 = 1; $i2 <= $n2; $i2++) {
                $amount2    = $db->result($r2, $i2 - 1, "amount_col");
                $art_id2    = $db->result($r2, $i2 - 1, "art_id");
                $price_end2 = $db->result($r2, $i2 - 1, "price_end");
                $sel_dp_id  = $db->result($r2, $i2 - 1, "dp_id");
                $client_id  = $this->getDpClient($sel_dp_id);
                $cash_id_to = $cash_id;
                $cash_id_fr = $this->getArticlePriceRatingCash($art_id2);

                $price_end2_cash = $this->getPriceRatingKours($price_end2, $cash_id_fr, $cash_id_to, $usd_to_uah, $eur_to_uah);
                if ($cash_id == 1) {
                    $price_end2_cash = $this->getClientPriceRounding($client_id, $price_end2_cash);
                }
                $summ2 = round($amount2 * $price_end2_cash, 2);
                $summ_all += $summ2;
            }

            [$client_saldo] = $this->getClientGeneralSaldo($client_conto_id);
            $credit_limit       = $this->getClientCreditLimit($client_conto_id);
            $datapay_limit      = $this->checkClientSaleInvoiceDataPayLimit($client_conto_id);
        }

        return array($client_saldo, $credit_limit, $summ_all, $datapay_limit);
    }

    function getClientCreditLimit($client_id)
    {
        $db = DbSingleton::getDb();
        $credit_limit = 0;
        $r = $db->query("SELECT `credit_limit` FROM `A_CLIENTS_CONDITIONS` WHERE `client_id` = '$client_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $credit_limit = $db->result($r, 0, "credit_limit");
        }
        return $credit_limit;
    }

    function viewDpDatapayLimitSaleInvoice($dp_id)
    {
        $db = DbSingleton::getDb();
        $form = ""; $form_htm = RD . "/tpl/dp_sale_invoice_list_data_pay.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $client_id = $this->getDpClient($dp_id);
        $list = $client_name = "";
        $r = $db->query("SELECT sv.*, dp.prefix as dp_prefix, dp.doc_nom as dp_nom, t.name as tpoint_name, sl.name as seller_name, cl.name as client_name, dt.mvalue as doc_type_name, ch.abr2 as cash_abr 
        FROM `J_SALE_INVOICE` sv
            LEFT OUTER JOIN `J_DP` dp ON dp.id=sv.dp_id
            LEFT OUTER JOIN `CASH` ch ON ch.id=sv.cash_id
            LEFT OUTER JOIN `T_POINT` t ON t.id=sv.tpoint_id
            LEFT OUTER JOIN `A_CLIENTS` sl ON sl.id=sv.seller_id
            LEFT OUTER JOIN `A_CLIENTS` cl ON cl.id=sv.client_conto_id
            LEFT OUTER JOIN `manual` dt ON dt.key='client_sale_type' AND dt.id=sv.doc_type_id
        WHERE sv.status=1 AND sv.`client_conto_id`='$client_id' AND sv.`status_invoice`='86' AND sv.`data_pay`<CURDATE() 
        ORDER BY sv.data_pay ASC, sv.data_create ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id             = $db->result($r, $i - 1, "id");
            $dp_nom         = $db->result($r, $i - 1, "dp_prefix") . $db->result($r, $i - 1, "dp_nom");
            $prefix         = $db->result($r, $i - 1, "prefix");
            $doc_nom        = $db->result($r, $i - 1, "doc_nom");
            $data_create    = $db->result($r, $i - 1, "data_create");
            $tpoint_name    = $db->result($r, $i - 1, "tpoint_name");
            $seller_name    = $db->result($r, $i - 1, "seller_name");
            $client_name    = $db->result($r, $i - 1, "client_name");
            $doc_type_name  = $db->result($r, $i - 1, "doc_type_name");
            $summ           = $db->result($r, $i - 1, "summ");
            $cash_abr       = $db->result($r, $i - 1, "cash_abr");
            $data_pay       = $db->result($r, $i - 1, "data_pay");
            $list .= "
            <tr id='strStsRow_$i' style='cursor:pointer' align='center' onClick='showSaleInvoiceCard(\"$id\");'>
                <td>$i</td>
                <td>$prefix-$doc_nom</td>
                <td align='center'>$data_create</td>
                <td>$dp_nom</td>
                <td>$tpoint_name</td>
                <td align='left'>$seller_name</td>
                <td align='left'>$client_name</td>
                <td>$doc_type_name</td>
                <td align='center' style='min-width:80px;'>$summ $cash_abr</td>
                <td align='right'>$data_pay</td>
            </tr>";
        }
        if ($n == 0) {
            $list = "<tr><td colspan=13 align='center'>Накладні відсутні</td></tr>";
        }
        $form = str_replace("{list}", $list, $form);

        return array($form, "Список протермінованих накладних: $client_name");
    }

    function checkLimitBeforeSaleInvoice($dp_id, $kol_storsel, $ar_storsel) { $db = DbSingleton::getDb();
        $summ_all = 0;
        $r = $db->query("SELECT * FROM `J_DP` WHERE `id` = $dp_id LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $cash_id    = $db->result($r, 0, "cash_id");
            $usd_to_uah = $db->result($r, 0, "usd_to_uah");
            $eur_to_uah = $db->result($r, 0, "eur_to_uah");
            $status_dp  = $db->result($r, 0, "status_dp");
            [$usd_to_uah_new, $eur_to_uah_new] = $this->getKoursData();
            if ($status_dp == 79) {
                $usd_to_uah = $usd_to_uah_new;
                $eur_to_uah = $eur_to_uah_new;
            } else {
                if ($usd_to_uah == 0) { $usd_to_uah = $usd_to_uah_new; }
                if ($eur_to_uah == 0) { $eur_to_uah = $eur_to_uah_new; }
            }
            $select_str = "0";
            for ($i = 1; $i <= $kol_storsel; $i++) {
                if ($ar_storsel[$i] !== "" && $ar_storsel[$i] > 0) {
                    $select_str .= "," . $ar_storsel[$i];
                }
            }
            $summ_all = 0;
            $r2 = $db->query("SELECT jss.amount_collect, jds.*
            FROM `J_SELECT_STR` jss 
                LEFT OUTER JOIN `J_SELECT` js ON (js.id=jss.select_id) 
                LEFT OUTER JOIN `J_DP` jd ON (jd.id=js.parrent_doc_id AND js.parrent_doc_type_id=2)
                LEFT OUTER JOIN `J_DP_STR` jds ON (jds.dp_id=jd.id) 
            WHERE jss.select_id IN ($select_str) AND jds.art_id=jss.art_id 
            GROUP BY jds.art_id, jss.storage_id_from, jss.cell_id_from;");
            $n2 = $db->num_rows($r2);
            for ($i2 = 1; $i2 <= $n2; $i2++) {
                $amount2    = $db->result($r2, $i2 - 1, "amount_collect");
                $price_end2 = $db->result($r2, $i2-1, "price_end");
                $sel_dp_id  = $db->result($r2, $i2 - 1, "dp_id");
                if ($cash_id == 1) {
                    $client_id  = $this->getDpClient($sel_dp_id);
                    $price_end2 = round($price_end2 * $usd_to_uah, 2);
                    $price_end2 = $this->getClientPriceRounding($client_id, $price_end2);
                }
                $summ2      = $amount2 * $price_end2;
                $summ_all   += $summ2;
            }
            if ($cash_id == 3) {
                $summ_all = round($summ_all * $usd_to_uah / $eur_to_uah, 2);
            }
        }

        return $summ_all;
    }

    /*
     * Створити списання
     * */
    function sendDpStorselToWriteOff($dp_id, $kol_storsel, $ar_storsel, $status_write_off) { $db = DbSingleton::getDb(); $dbt = DbSingleton::getTokoDb();
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $slave = new slave;
        $answer = 0; $err = "Помилка індексу!";
        $write_off_nom = 0;
        $dp_id = $slave->qq($dp_id); $kol_storsel = $slave->qq($kol_storsel);

        if (($dp_id > 0) && $kol_storsel > 0) {
            $sale_storsell_summ = $this->checkLimitBeforeSaleInvoice($dp_id, $kol_storsel, $ar_storsel);

            if ($sale_storsell_summ <= 0) {
                $db->query("UPDATE `J_DP` SET `status_dp`='81' WHERE `id` = $dp_id;");
                $answer = 1; $err = "Всі товари у ДП відхилено. Списання не виконано. Документ передано у архів.";
            }

            $r = $db->query("SELECT MAX(`id`) as mid FROM `J_WRITE_OFF`;");
            $write_off_id = 0 + $db->result($r, 0, "mid") + 1;
            $write_off_nom = $write_off_id;
            $db->query("INSERT INTO `J_WRITE_OFF` (`id`,`dp_id`,`status_write_off`) VALUES ('$write_off_id','$dp_id','$status_write_off');");
            $ai = 0;
            for ($i = 1; $i <= $kol_storsel; $i++) {
                if ($ar_storsel[$i] !== "" && $ar_storsel[$i] > 0) {
                    ++$ai;
                    $db->query("INSERT INTO `J_WRITE_OFF_STORSEL` (`dp_id`,`write_off_id`,`select_id`) VALUES ('$dp_id','$write_off_id','".$ar_storsel[$i]."');");
                }
            }

            $r = $db->query("SELECT * FROM `J_DP` WHERE `id` = $dp_id LIMIT 1;");
            $n = $db->num_rows($r);
            if ($n == 1) {
                $doc_type_id        = $db->result($r, 0, "doc_type_id");
                $tpoint_id          = $db->result($r, 0, "tpoint_id");
                $client_id          = $db->result($r, 0, "client_id");
                $client_conto_id    = $db->result($r, 0, "client_conto_id");
                $cash_id            = $db->result($r, 0, "cash_id");
                $usd_to_uah         = $db->result($r, 0, "usd_to_uah");
                $eur_to_uah         = $db->result($r, 0, "eur_to_uah");
                $vat_use            = $db->result($r, 0, "vat_use");
                $delivery_type_id   = $db->result($r, 0, "delivery_type_id");
                $carrier_id         = $db->result($r, 0, "carrier_id");
                $delivery_address   = $db->result($r, 0, "delivery_address");
                $status_dp          = $db->result($r, 0, "status_dp");

                list($usd_to_uah_new, $eur_to_uah_new) = $this->getKoursData();

                if ($status_dp == 79) {
                    $usd_to_uah = $usd_to_uah_new;
                    $eur_to_uah = $eur_to_uah_new;
                } else {
                    if ($usd_to_uah == 0) {
                        $usd_to_uah = $usd_to_uah_new;
                    }
                    if ($eur_to_uah == 0) {
                        $eur_to_uah = $eur_to_uah_new;
                    }
                }

                $seller_id = $this->getSellerId($tpoint_id, $doc_type_id);
                //list(,$seller_doc_nom)=$this->getSellerPrefixDocNom($seller_id,$doc_type_id);
                list(,, $data_pay) = $this->getClientPaymentDelay($client_conto_id);
                $data_create = date("Y-m-d");
                $seller_prefix = "СП";
                $seller_doc_nom = $write_off_id;

                $db->query("UPDATE `J_WRITE_OFF` SET `prefix`='$seller_prefix', `doc_nom`='$seller_doc_nom', `tpoint_id`='$tpoint_id', `seller_id`='$seller_id', 
                `client_id`='$client_id', `client_conto_id`='$client_conto_id', `doc_type_id`='$doc_type_id', `data_create`='$data_create', `data_create`='$data_create', 
                `data_pay`='$data_pay', `cash_id`='$cash_id', `usd_to_uah`='$usd_to_uah', `eur_to_uah`='$eur_to_uah', `vat_use`='$vat_use', `delivery_type_id`='$delivery_type_id', 
                `carrier_id`='$carrier_id', `delivery_address`='$delivery_address', `user_id`='$user_id' 
                WHERE `id`='$write_off_id' LIMIT 1;");

                $r3 = $db->query("SELECT * FROM `J_WRITE_OFF_STORSEL` 
                WHERE `write_off_id`='$write_off_id' AND `dp_id`='$dp_id' AND `status`='1' 
                ORDER BY `id` ASC;");
                $n3 = $db->num_rows($r3);
                $select_str = "0";
                for ($i3 = 1; $i3 <= $n3; $i3++) {
                    $select_str .= "," . $db->result($r3, $i3 - 1, "select_id");
                }

                $r2 = $db->query("SELECT jds.*, jss.amount_collect as amount_collect2, jss.storage_id_from as storage_id_from2, jss.cell_id_from as cell_id_from2
                FROM `J_SELECT_STR` jss 
                    LEFT OUTER JOIN J_SELECT js ON (js.id = jss.select_id) 
                    LEFT OUTER JOIN J_DP jd ON (jd.id = js.parrent_doc_id AND js.parrent_doc_type_id = 2)
                    LEFT OUTER JOIN J_DP_STR jds ON (jds.dp_id = jd.id AND jds.cur_select_str_id = jss.id) 
                WHERE jss.select_id IN ($select_str) AND jds.art_id = jss.art_id AND jds.status_dps != 97;");
                $n2 = $db->num_rows($r2);
                $summ_all = 0;

                $db->query("UPDATE `J_SELECT` SET `status_select` = '127' WHERE `id` IN ($select_str);");

                for ($i2 = 1; $i2 <= $n2; $i2++) {
                    $dp_str_id          = $db->result($r2, $i2 - 1, "id");
                    $art_id2            = $db->result($r2, $i2 - 1, "art_id");
                    $article_nr_displ2  = $db->result($r2, $i2 - 1, "article_nr_displ");
                    $brand_id2          = $db->result($r2, $i2 - 1, "brand_id");
                    $amount2            = $db->result($r2, $i2 - 1, "amount_collect2");
                    $price2             = $db->result($r2, $i2 - 1, "price");
                    $price_end2         = $db->result($r2, $i2 - 1, "price_end");
                    $discount2          = $db->result($r2, $i2 - 1, "discount");
                    $storage_id_from2   = $db->result($r2, $i2 - 1, "storage_id_from2");
                    $cell_id_from2      = $db->result($r2, $i2 - 1, "cell_id_from2");

                    $db->query("UPDATE `J_DP_STR` SET `status_dps` = 97 WHERE `id` = '$dp_str_id' LIMIT 1;");
                    if ($amount2 > 0) {
                        $cash_id_to         = $cash_id;
                        $cash_id_from       = $this->getArticlePriceRatingCash($art_id2);
                        $price2_cash        = $this->getPriceRatingKours($price2, $cash_id_from, $cash_id_to, $usd_to_uah, $eur_to_uah);
                        $price_end2_cash    = $this->getPriceRatingKours($price_end2, $cash_id_from, $cash_id_to, $usd_to_uah, $eur_to_uah);
                        $discount2_cash     = $discount2;
                        if ($cash_id == 1) {
                            $price2_cash        = $this->getClientPriceRounding($client_conto_id, $price2_cash);
                            $price_end2_cash    = $this->getClientPriceRounding($client_conto_id, $price_end2_cash);
                        }
                        $summ2_cash         = round($amount2 * $price_end2_cash, 2);

                        $rsi = $db->query("SELECT MAX(`id`) as mid FROM `J_WRITE_OFF_STR`;");
                        $write_off_str_id = 0 + $db->result($rsi, 0, "mid") + 1;
                        $db->query("INSERT INTO `J_WRITE_OFF_STR` (`id`,`write_off_id`,`art_id`,`article_nr_displ`,`brand_id`,`amount`,`price`,`price_end`,`discount`,`summ`,`storage_id_from`,`cell_id_from`) 
                        VALUES ('$write_off_str_id','$write_off_id','$art_id2','$article_nr_displ2','$brand_id2','$amount2','$price2_cash','$price_end2_cash','$discount2_cash','$summ2_cash','$storage_id_from2','$cell_id_from2');");

                        $slave->addJuornalArtDocs(6, $write_off_id, $art_id2, $amount2);

                        $this->setWriteOffPartitions($write_off_id, $write_off_str_id, $art_id2, $article_nr_displ2, $brand_id2, $amount2, $price_end2);

                        $rr = $dbt->query("SELECT `RESERV_AMOUNT` FROM `T2_ARTICLES_STRORAGE` WHERE `ART_ID` = $art_id2 AND `STORAGE_ID` = $storage_id_from2 LIMIT 1;");
                        $nr = $dbt->num_rows($rr);
                        if ($nr == 1) {
                            $rr_reserv = $dbt->result($rr, 0, "RESERV_AMOUNT");
                            $rr_reserv -= $amount2;
                            $dbt->query("UPDATE `T2_ARTICLES_STRORAGE` SET `RESERV_AMOUNT`='$rr_reserv' WHERE `ART_ID` = $art_id2 AND `STORAGE_ID` = $storage_id_from2;");
                        }

                        $rr = $dbt->query("SELECT `RESERV_AMOUNT` FROM `T2_ARTICLES_STRORAGE_CELLS` WHERE `ART_ID` = $art_id2 AND `STORAGE_ID` = $storage_id_from2 AND `STORAGE_CELLS_ID` = $cell_id_from2 LIMIT 1;");
                        $nr = $dbt->num_rows($rr);
                        if ($nr == 1) {
                            $rr_reserv = $dbt->result($rr, 0, "RESERV_AMOUNT");
                            $rr_reserv -= $amount2;
                            $dbt->query("UPDATE `T2_ARTICLES_STRORAGE_CELLS` SET `RESERV_AMOUNT`='$rr_reserv' WHERE `ART_ID` = $art_id2 AND `STORAGE_ID` = $storage_id_from2 AND `STORAGE_CELLS_ID` = $cell_id_from2;");
                        }
                        $dbt->query("UPDATE `T2_ARTICLES_PRICE_STOCK` SET `GENERAL_STOCK`=(`GENERAL_STOCK`-'$amount2') WHERE `ART_ID` = $art_id2 LIMIT 1;");

                        $summ_all += $summ2_cash;
                    }
                }

                if ($ai == $kol_storsel) {
                    //
                    $ra = $db->query("SELECT COUNT(`id`) as kol FROM `J_DP_STR` WHERE `dp_id` = $dp_id AND `status_dps` != 97 AND `status_dps` != 170;");
                    $ra_kol = $db->result($ra, 0, "kol");
                    if ($ra_kol == 0) {
                        $db->query("UPDATE `J_DP` SET `status_dp` = 81 WHERE `id` = $dp_id LIMIT 1;");
                    }
                }

                $db->query("UPDATE `J_WRITE_OFF` SET `summ` = '$summ_all', `summ_debit` = '$summ_all' WHERE `id` = '$write_off_id' LIMIT 1;");

                $answer = 1; $err = "";
            }
        }

        $write_off_prefix = 0;
        $rsel = $db->query("SELECT `prefix`, `doc_nom` FROM `J_WRITE_OFF` WHERE `id` = $write_off_nom LIMIT 1;");
        $nsel = $db->num_rows($rsel);

        if ($nsel > 0) {
            $prefix = $db->result($rsel, 0, "prefix");
            $doc_nom = $db->result($rsel, 0, "doc_nom");
            $write_off_prefix = "$prefix-$doc_nom";
        }

        return array($answer, $err, $write_off_nom, $write_off_prefix);
    }

    /*
     * СФОРМУВАТИ видаткову накладну
     * */
    function sendDpStorselToSaleInvoice($dp_id, $kol_storsel, $ar_storsel) { $db = DbSingleton::getDb(); $dbt = DbSingleton::getTokoDb();
        $sale_invoice = new sale_invoice; $slave = new slave; $cat = new catalogue;
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $answer = 0; $err = "Помилка індексу!";
        $dp_id = $slave->qq($dp_id); $kol_storsel = $slave->qq($kol_storsel);
        $sale_invoice_nom = $price2_cash = $price_end2_cash = $discount2_cash = $summ2_cash = 0;

        // check UKTZED
        $check = 0;
        if ($this->checkDpSaleInvoice($dp_id)) {
            $answer = 0; $err = "Спочатку вкажіть всі номера УКТЗЕД або заповніть українське найменування артикула!";
            $check = 1;
        }

        if ($dp_id > 0 && $check == 0 && $kol_storsel > 0) {
            [$client_saldo, $client_credit_limit, $sale_storsell_summ, $datapay_limit] = $this->checkClientCreditLimitBeforeSaleInvoice($dp_id, $kol_storsel, $ar_storsel);

            if ($sale_storsell_summ <= 0) {
                $db->query("UPDATE `J_DP` SET `status_dp` = '81' WHERE `id` = $dp_id;");
                $answer = 1; $err = "Всі товари у ДП відхилено. Накладну не створено. Документ передано у архів.";
            }

            if ($datapay_limit > 0) {
                $answer = 2; $err = "Поточне відвантаження НЕ можливе! У контрагента наявні просрочені документи. Відобразити список просрочених документів?";
            }

            if ($datapay_limit == 0 && $sale_storsell_summ > 0) {

                if ($client_saldo == 0 || ($client_saldo > 0 && $sale_storsell_summ < $client_saldo + $client_credit_limit) || ($client_saldo < 0 && $sale_storsell_summ < $client_credit_limit - abs($client_saldo))) {

                    $r = $db->query("SELECT `doc_type_id` FROM `J_DP` WHERE `id` = $dp_id LIMIT 1;");
                    $doc_type_id_check = $db->result($r, 0, "doc_type_id");

                    if ($doc_type_id_check > 0) {

                        $r = $db->query("SELECT MAX(`id`) as mid FROM `J_SALE_INVOICE`;");
                        $invoice_id = 0 + $db->result($r, 0, "mid") + 1;
                        $sale_invoice_nom = $invoice_id; // Временно

                        $db->query("INSERT INTO `J_SALE_INVOICE` (`id`, `dp_id`) VALUES ('$invoice_id', '$dp_id');");
                        $ai = 0;
                        for ($i = 1; $i <= $kol_storsel; $i++) {
                            if ($ar_storsel[$i] != "" && $ar_storsel[$i] > 0) {
                                $ai += 1;
                                $db->query("INSERT INTO `J_SALE_INVOICE_STORSEL` (`dp_id`, `invoice_id`, `select_id`) VALUES ('$dp_id', '$invoice_id', '".$ar_storsel[$i]."');");
                            }
                        }

                        $r = $db->query("SELECT * FROM `J_DP` WHERE `id` = $dp_id LIMIT 1;");
                        $n = $db->num_rows($r);
                        if ($n == 1) {
                            $doc_type_id        = $db->result($r, 0, "doc_type_id");
                            $tpoint_id          = $db->result($r, 0, "tpoint_id");
                            $client_id          = $db->result($r, 0, "client_id");
                            $client_conto_id    = $db->result($r, 0, "client_conto_id");
                            $cash_id            = $db->result($r, 0, "cash_id");
                            $usd_to_uah         = $db->result($r, 0, "usd_to_uah");
                            $eur_to_uah         = $db->result($r, 0, "eur_to_uah");
                            $summ               = $db->result($r, 0, "summ");
                            if ($cash_id == 1) {
                                $summ = $this->getClientPriceRounding($client_conto_id, $summ);
                            }
                            $vat_use            = $db->result($r, 0, "vat_use");
                            $delivery_type_id   = $db->result($r, 0, "delivery_type_id");
                            $carrier_id         = $db->result($r, 0, "carrier_id");
                            $delivery_address   = $db->result($r, 0, "delivery_address");
                            $status_dp          = $db->result($r, 0, "status_dp");

                            list($usd_to_uah_new, $eur_to_uah_new) = $this->getKoursData();

                            if ($status_dp == 79) {
                                $usd_to_uah = $usd_to_uah_new;
                                $eur_to_uah = $eur_to_uah_new;
                            } else {
                                if ($usd_to_uah == 0) {
                                    $usd_to_uah = $usd_to_uah_new;
                                }
                                if ($eur_to_uah == 0) {
                                    $eur_to_uah = $eur_to_uah_new;
                                }
                            }

                            $seller_id = $this->getSellerId($tpoint_id, $doc_type_id);
                            list($seller_prefix, $seller_doc_nom) = $this->getSellerPrefixDocNom($seller_id, $doc_type_id);
                            list(,, $data_pay) = $this->getClientPaymentDelay($client_conto_id);
                            $data_create = date("Y-m-d");

                            $db->query("UPDATE `J_SALE_INVOICE` SET `prefix` = '$seller_prefix', `doc_nom` = '$seller_doc_nom', `tpoint_id` = '$tpoint_id', `seller_id` = '$seller_id', 
                            `client_id` = '$client_id', `client_conto_id` = '$client_conto_id', `doc_type_id` = '$doc_type_id', `data_create` = '$data_create', `data_pay` = '$data_pay', 
                            `cash_id` = '$cash_id', `usd_to_uah` = '$usd_to_uah', `eur_to_uah` = '$eur_to_uah', `vat_use` = '$vat_use', `delivery_type_id` = '$delivery_type_id', 
                            `carrier_id` = '$carrier_id', `delivery_address` = '$delivery_address', `user_id` = '$user_id' 
                            WHERE `id` = '$invoice_id' LIMIT 1;");

                            $r3 = $db->query("SELECT * FROM `J_SALE_INVOICE_STORSEL`
                            WHERE `invoice_id` = '$invoice_id' AND `dp_id` = $dp_id AND `status` = '1' 
                            ORDER BY `id` ASC;");
                            $n3 = $db->num_rows($r3);
                            $select_str = "0";
                            for ($i3 = 1; $i3 <= $n3; $i3++) {
                                $select_str .= "," . $db->result($r3, $i3 - 1, "select_id");
                            }

                            $tax_id = 0;
                            if ($doc_type_id == 61) {
                                $rt = $db->query("SELECT MAX(`id`) as mid FROM `J_TAX_INVOICE`;");
                                $tax_id = 0 + $db->result($rt, 0, "mid") + 1;
                                $year = date("Y");
                                $rt = $db->query("SELECT MAX(`doc_nom`) as mid FROM `J_TAX_INVOICE` WHERE `seller_id` = '$seller_id' AND `data_create` >= '$year-01-01';");
                                $tax_nom = 0 + $db->result($rt, 0, "mid") + 1;
                                $db->query("INSERT INTO `J_TAX_INVOICE` (`id`, `tax_type_id`, `doc_nom`, `data_create`, `sale_invoice_id`, `tpoint_id`, `seller_id`, `client_id`, `cash_id`, `summ`, `user_id`) 
                                VALUES ('$tax_id', '160', '$tax_nom', CURDATE(), '$invoice_id', '$tpoint_id', '$seller_id', '$client_conto_id', '$cash_id', '$summ', '$user_id');");
                            }

                            $r2 = $db->query("SELECT jds.*, jss.amount_collect as amount_collect2, jss.storage_id_from as storage_id_from2, jss.cell_id_from as cell_id_from2,
                            jss.parrent_doc_type_id, jss.parrent_doc_id
                            FROM `J_SELECT_STR` jss 
                                LEFT OUTER JOIN `J_SELECT` js ON (js.id = jss.select_id) 
                                LEFT OUTER JOIN `J_DP` jd ON (jd.id = js.parrent_doc_id AND js.parrent_doc_type_id = 2)
                                LEFT OUTER JOIN `J_DP_STR` jds ON (jds.dp_id = jd.id AND (jss.parrent_doc_id = jds.id OR jss.parrent_doc_id = 0)) 
                            WHERE jss.select_id IN ($select_str) AND jds.art_id = jss.art_id AND jds.status_dps != 97
                            GROUP BY jds.art_id, jds.suppl_id, jss.storage_id_from, jss.cell_id_from, jds.reserv_type_id;");
                            $n2 = $db->num_rows($r2);
                            $summ_all = 0;
                            // GROUP BY jds.art_id, jss.storage_id_from, jss.cell_id_from
                            // AND jds.cur_select_str_id=jss.id

                            $db->query("UPDATE `J_SELECT` SET `status_select` = '127' WHERE `id` IN ($select_str);");

                            for ($i2 = 1; $i2 <= $n2; $i2++) {
                                $art_id2                = $db->result($r2, $i2 - 1, "art_id");
                                $suppl_id2              = $db->result($r2, $i2 - 1, "suppl_id");
                                $where_suppl            = ($suppl_id2 > 0) ? "AND `suppl_id`='$suppl_id2'" : "";
                                $article_nr_displ2      = $db->result($r2, $i2 - 1, "article_nr_displ");
                                $brand_id2              = $db->result($r2, $i2 - 1, "brand_id");
                                $amount2                = $db->result($r2, $i2 - 1, "amount_collect2");
                                $price2                 = $db->result($r2, $i2 - 1, "price");
                                $price_end2             = $db->result($r2, $i2 - 1, "price_end");
                                $discount2              = $db->result($r2, $i2 - 1, "discount");
                                $storage_id_from2       = $db->result($r2, $i2 - 1, "storage_id_from2");
                                $cell_id_from2          = $db->result($r2, $i2 - 1, "cell_id_from2");
                                $reserv_type_id         = $db->result($r2, $i2 - 1, "reserv_type_id");
                                $parrent_doc_type_id    = $db->result($r2, $i2 - 1, "parrent_doc_type_id");  // $parrent_doc_type_id = 1 - J_DP
                                $parrent_doc_id         = $db->result($r2, $i2 - 1, "parrent_doc_id");            // $parrent_doc_type_id = 2 - J_MOVING

                                $where_storage = "AND `storage_id_from`='$storage_id_from2'";
                                if ($reserv_type_id == 68) {
                                    $storage_id_from2 = $db->result($r2, $i2 - 1, "location_storage_id");
                                    $where_storage = "AND `location_storage_id`='$storage_id_from2'";
                                }

                                // якщо віддалене переміщення - беремо склад LOCATION, інакше - беремо склад з Скл Відбору
                                // помилка зі статусом при вибитті документа через dp_str_id - коли однакові індекси з різних складів

                                $rt = $db->query("SELECT `id` FROM `J_DP_STR` WHERE `dp_id` = '$dp_id' AND `art_id` = $art_id2 $where_suppl $where_storage LIMIT 1;"); // по старому
                                $dp_str_id = $db->result($rt, 0, "id");

                                // якщо записано DP
                                if ($parrent_doc_type_id > 0 && $parrent_doc_id > 0) {
                                    $dp_str_id = $parrent_doc_id;
                                }

                                $db->query("UPDATE `J_DP_STR` SET `status_dps` = 97 WHERE `id` = $dp_str_id LIMIT 1;");

                                if ($amount2 > 0) {
                                    $cash_id_to         = $cash_id;
                                    $cash_id_from       = $this->getArticlePriceRatingCash($art_id2);
                                    $price2_cash        = $this->getPriceRatingKours($price2, $cash_id_from, $cash_id_to, $usd_to_uah, $eur_to_uah);
                                    $price_end2_cash    = $this->getPriceRatingKours($price_end2, $cash_id_from, $cash_id_to, $usd_to_uah, $eur_to_uah);
                                    $discount2_cash     = $discount2;
                                    if ($cash_id == 1) {
                                        $price2_cash        = $this->getClientPriceRounding($client_conto_id, $price2_cash);
                                        $price_end2_cash    = $this->getClientPriceRounding($client_conto_id, $price_end2_cash);
                                    }
                                    $summ2_cash         = round($amount2 * $price_end2_cash, 2);

                                    $rsi = $db->query("SELECT MAX(`id`) as mid FROM `J_SALE_INVOICE_STR`;");
                                    $invoice_str_id = 0 + $db->result($rsi, 0, "mid") + 1;
                                    $db->query("INSERT INTO `J_SALE_INVOICE_STR` (`id`, `invoice_id`, `art_id`, `article_nr_displ`, `brand_id`, `amount`, `price`, `price_end`, `discount`, `summ`, `storage_id_from`, `cell_id_from`) 
                                    VALUES ('$invoice_str_id', '$invoice_id', '$art_id2', '$article_nr_displ2', '$brand_id2', '$amount2', '$price2_cash', '$price_end2_cash', '$discount2_cash', '$summ2_cash', '$storage_id_from2', '$cell_id_from2');");

                                    if ($tax_id > 0) {
                                        $zed = $cat->getArticleZED($art_id2);
                                        $art_name = $cat->getArticleNameLang($art_id2);
                                        $db->query("INSERT INTO `J_TAX_INVOICE_STR` (`tax_id`, `zed`, `art_id`, `goods_name`, `amount`, `price`, `summ`) 
                                        VALUES ('$tax_id', '$zed', '$art_id2', '$art_name', '$amount2', '$price_end2_cash', '$summ2_cash');");
                                    }

                                    $slave->addJuornalArtDocs(3, $invoice_id, $art_id2, $amount2);

                                    $this->writeOffPartitions($invoice_id, $invoice_str_id, $art_id2, $article_nr_displ2, $brand_id2, $amount2, $price_end2);

                                    $rr = $dbt->query("SELECT `RESERV_AMOUNT` FROM `T2_ARTICLES_STRORAGE` WHERE `ART_ID` = $art_id2 AND `STORAGE_ID` = $storage_id_from2 LIMIT 1;");
                                    $nr = $dbt->num_rows($rr);
                                    if ($nr == 1) {
                                        $rr_reserv = $dbt->result($rr, 0, "RESERV_AMOUNT");
                                        $rr_reserv -= $amount2;
                                        $dbt->query("UPDATE `T2_ARTICLES_STRORAGE` SET `RESERV_AMOUNT`='$rr_reserv' WHERE `ART_ID` = $art_id2 AND `STORAGE_ID` = $storage_id_from2;");
                                    }

                                    $rr = $dbt->query("SELECT `RESERV_AMOUNT` FROM `T2_ARTICLES_STRORAGE_CELLS` WHERE `ART_ID` = $art_id2 AND `STORAGE_ID` = $storage_id_from2 AND `STORAGE_CELLS_ID` = $cell_id_from2 LIMIT 1;");
                                    $nr = $dbt->num_rows($rr);
                                    if ($nr == 1) {
                                        $rr_reserv = $dbt->result($rr, 0, "RESERV_AMOUNT");
                                        $rr_reserv -= $amount2;
                                        $dbt->query("UPDATE `T2_ARTICLES_STRORAGE_CELLS` SET `RESERV_AMOUNT` = '$rr_reserv' WHERE `ART_ID` = $art_id2 AND `STORAGE_ID` = $storage_id_from2 AND `STORAGE_CELLS_ID` = $cell_id_from2;");
                                    }
                                    $dbt->query("UPDATE `T2_ARTICLES_PRICE_STOCK` SET `GENERAL_STOCK` = (`GENERAL_STOCK` - '$amount2') WHERE `ART_ID` = $art_id2 LIMIT 1;");

                                    $summ_all += $summ2_cash;
                                }
                            }
                            if ($ai == $kol_storsel) {
                                $ra = $db->query("SELECT COUNT(`id`) as kol FROM `J_DP_STR` WHERE `dp_id` = $dp_id AND `status_dps` != 97 AND `status_dps` != 170;");
                                $ra_kol = $db->result($ra, 0, "kol");
                                if ($ra_kol == 0) {
                                    $db->query("UPDATE `J_DP` SET `status_dp` = 81 WHERE `id` = $dp_id LIMIT 1;");
                                }
                                //$db->query("update J_DP_STR set status_dps='97' where dp_id='$dp_id';");
                            }
                            //if ($cash_id==1){$summ_all=round($summ_all*$usd_to_uah,2);}
                            //if ($cash_id==3){$summ_all=round($summ_all*$usd_to_uah/$eur_to_uah,2);}
                            $db->query("UPDATE `J_SALE_INVOICE` SET `summ` = '$summ_all', `summ_debit` = '$summ_all' WHERE `id` = $invoice_id LIMIT 1;");

                            list($balans_before) = $this->getClientGeneralSaldo($client_conto_id);
                            $balans_after = $balans_before - $summ_all;
                            $db->query("INSERT INTO `B_CLIENT_BALANS_JOURNAL` (`client_id`, `cash_id`, `balans_before`, `deb_kre`, `summ`, `balans_after`, `doc_type_id`, `doc_id`) 
                            VALUES ('$client_conto_id', '$cash_id', '$balans_before', '1', '$summ_all', '$balans_after', '1', '$invoice_id');");

                            $this->updateClientBalans($client_conto_id, $cash_id, $summ_all);
                            $answer = 1; $err = "";
                            //add to cron client invoice
                            $sale_invoice->addClientInvoiceCron($invoice_id);
                        }
                    } else {
                        $answer = 0; $err = "Виберіть тип документу спочатку";
                    }
                } else {
                    list($client_cash_id,) = $this->getClientCashConditions($this->getDpClient($dp_id));
                    $cash_name = $this->getCashAbr($client_cash_id);
                    $cl_pp = $sale_storsell_summ + abs($client_saldo) - $client_credit_limit;
                    $answer = 0; $err = "Ліміт кредиту: $client_credit_limit $cash_name; борг/баланс: " . abs($client_saldo) . " $cash_name. Відвантаження на сумму: $sale_storsell_summ $cash_name НЕ можливе, для поточного відвантаження внесіть в касу як мінімум $cl_pp $cash_name, або проведіть не оплачені документи";
                }
            }
        }
        $rsel = $db->query("SELECT `prefix`, `doc_nom`, `doc_type_id` FROM `J_SALE_INVOICE` WHERE `id` = $sale_invoice_nom LIMIT 1;");
        $nsel = $db->num_rows($rsel);
        $sale_invoice_prefix = 0;
        $sale_invoice_doc_type_id = 0;
        if ($nsel > 0) {
            $prefix = $db->result($rsel, 0, "prefix");
            $doc_nom = $db->result($rsel, 0, "doc_nom");
            $sale_invoice_doc_type_id = $db->result($rsel, 0, "doc_type_id");
            $sale_invoice_prefix = "$prefix-$doc_nom";
        }

        return array($answer, $err, $sale_invoice_nom, $sale_invoice_prefix, $sale_invoice_doc_type_id);
    }

    function setWriteOffPartitions($write_off_id, $write_off_str_id, $art_id, $article_nr_displ, $brand_id, $amount_invoice, $price_invoice)
    {
        $db = DbSingleton::getDb();
        $cat = new catalogue;
        $r = $db->query("SELECT * FROM `T2_ARTICLES_PARTITIONS` WHERE `art_id` = '$art_id' AND `rest` > 0 AND `status` = '1' ORDER BY `id` ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id                 = $db->result($r, $i - 1, "id");
            $rest               = $db->result($r, $i - 1, "rest");
            $price              = $db->result($r, $i - 1, "price");
            $price_buh_uah      = $db->result($r, $i - 1, "price_buh_uah");
            $price_man_uah      = $db->result($r, $i - 1, "price_man_uah");
            [$oper_price] = $cat->getArticleOperPriceGeneralStock($art_id);
            if ($amount_invoice <= $rest) {
                $new_rest = $rest - $amount_invoice;
                $db->query("UPDATE `T2_ARTICLES_PARTITIONS` SET `rest`='$new_rest' WHERE `id`='$id' LIMIT 1;");
                $db->query("INSERT INTO `J_WRITE_OFF_PARTITION_STR` (`partition_id`,`write_off_id`,`write_off_str_id`,`art_id`,`article_nr_displ`,`brand_id`,`partition_amount`,`invoice_amount`,`oper_price_partition`,`price_partition`,`price_buh_uah`,`price_man_uah`,`price_invoice`) 
                VALUES ('$id','$write_off_id','$write_off_str_id','$art_id','$article_nr_displ','$brand_id','$amount_invoice','$amount_invoice','$oper_price','$price','$price_buh_uah','$price_man_uah','$price_invoice');");
                $i = $n + 1;
            }
            if ($amount_invoice > $rest) {
                $new_rest = 0;
                $amount_invoice -= $rest;
                $db->query("UPDATE `T2_ARTICLES_PARTITIONS` SET `rest`='$new_rest' WHERE `id`='$id' LIMIT 1;");
                $db->query("INSERT INTO `J_WRITE_OFF_PARTITION_STR` (`partition_id`,`write_off_id`,`write_off_str_id`,`art_id`,`article_nr_displ`,`brand_id`,`partition_amount`,`invoice_amount`,`oper_price_partition`,`price_partition`,`price_buh_uah`,`price_man_uah`,`price_invoice`) 
                VALUES ('$id','$write_off_id','$write_off_str_id','$art_id','$article_nr_displ','$brand_id','$rest','$rest','$oper_price','$price','$price_buh_uah','$price_man_uah','$price_invoice');");
            }
        }

        return true;
    }

    function writeOffPartitions($invoice_id, $invoice_str_id, $art_id, $article_nr_displ, $brand_id, $amount_invoice, $price_invoice)
    {
        $db = DbSingleton::getDb();
        $cat = new catalogue;
        // price_invoice in USD
        $r = $db->query("SELECT * FROM `T2_ARTICLES_PARTITIONS` WHERE `art_id`='$art_id' AND `rest`>0 AND `status`='1' ORDER BY `id` ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id                 = $db->result($r, $i - 1, "id");
            $rest               = $db->result($r, $i - 1, "rest");
            $price              = $db->result($r, $i - 1, "price");
            $price_buh_uah      = $db->result($r, $i - 1, "price_buh_uah");
            $price_man_uah      = $db->result($r, $i - 1, "price_man_uah");
            [$oper_price,] = $cat->getArticleOperPriceGeneralStock($art_id);
            if ($amount_invoice <= $rest) {
                $new_rest = $rest - $amount_invoice;
                $db->query("UPDATE `T2_ARTICLES_PARTITIONS` SET `rest`='$new_rest' WHERE `id`='$id' LIMIT 1;");
                $db->query("INSERT INTO `J_SALE_INVOICE_PARTITION_STR` (`partition_id`,`invoice_id`,`invoice_str_id`,`art_id`,`article_nr_displ`,`brand_id`,`partition_amount`,`invoice_amount`,`oper_price_partition`,`price_partition`,`price_buh_uah`,`price_man_uah`,`price_invoice`) 
                VALUES ('$id','$invoice_id','$invoice_str_id','$art_id','$article_nr_displ','$brand_id','$amount_invoice','$amount_invoice','$oper_price','$price','$price_buh_uah','$price_man_uah','$price_invoice');");
                $i = $n + 1;
            }
            if ($amount_invoice > $rest) {
                $new_rest = 0;
                $amount_invoice -= $rest;
                $db->query("UPDATE `T2_ARTICLES_PARTITIONS` SET `rest`='$new_rest' WHERE `id`='$id' LIMIT 1;");
                $db->query("INSERT INTO `J_SALE_INVOICE_PARTITION_STR` (`partition_id`,`invoice_id`,`invoice_str_id`,`art_id`,`article_nr_displ`,`brand_id`,`partition_amount`,`invoice_amount`,`oper_price_partition`,`price_partition`,`price_buh_uah`,`price_man_uah`,`price_invoice`) 
                VALUES ('$id','$invoice_id','$invoice_str_id','$art_id','$article_nr_displ','$brand_id','$rest','$rest','$oper_price','$price','$price_buh_uah','$price_man_uah','$price_invoice');");
            }
        }

        return true;
    }

    function getClientGeneralSaldo($sel_id) { $db = DbSingleton::getDb();
        $saldo = "0"; $cash_id = 1;
        $r = $db->query("SELECT `saldo`, `cash_id` FROM `B_CLIENT_BALANS` WHERE `client_id`='$sel_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $saldo      = $db->result($r, 0, "saldo");
            $cash_id    = $db->result($r, 0, "cash_id");
        }
        return array($saldo, $cash_id);
    }

    function getSellerId($tpoint_id, $doc_type_id) { $db = DbSingleton::getDb();
        $seller_id = 0;
        $r = $db->query("SELECT `client_id` FROM `T_POINT_CLIENTS` 
        WHERE `tpoint_id`='$tpoint_id' AND `sale_type`='$doc_type_id' AND `in_use`='1' AND `status`='1' ORDER BY `id` ASC LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $seller_id = $db->result($r, 0, "client_id");
        }
        return $seller_id;
    }

    function getSellerPrefixDocNom($seller_id, $doc_type_id) { $db = DbSingleton::getDb();
        $sale_type = array(61 => 86, 62 => 87, 63 => 87, 64 => 88);
        $sale_type_id = $sale_type[$doc_type_id];
        $year_today = date("Y");
        $prefix = "";
        $r = $db->query("SELECT `prefix` FROM `A_CLIENTS_DOCUMENT_PREFIX` 
        WHERE `client_id`='$seller_id' AND `doc_type_id`='$sale_type_id' AND `status`='1' ORDER BY `id` ASC LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $prefix = $db->result($r, 0, "prefix");
        }
        $prefix = str_replace(array("{year}", "{month}", "{day}", "{rnd010}"), array(date("Y"), date("m"), date("d"), rand(0, 10)), $prefix);

        $r = $db->query("SELECT IFNULL(MAX(`doc_nom`), 0) AS doc_nom FROM `J_SALE_INVOICE` 
        WHERE `seller_id`='$seller_id' AND `doc_type_id`='$doc_type_id' AND `status`='1' AND `data_create`>='$year_today-01-01';");
        $doc_nom = 0 + $db->result($r, 0, "doc_nom") + 1;

        return array($prefix, $doc_nom);
    }

    function updateClientBalans($client_conto_id, $cash_id, $summ) { $db = DbSingleton::getDb();
        $r = $db->query("SELECT * FROM `B_CLIENT_BALANS` WHERE `client_id` = '$client_conto_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 0) {
            $db->query("INSERT INTO `B_CLIENT_BALANS` (`client_id`, `cash_id`) VALUES ('$client_conto_id', '$cash_id');");
            $n = 1;
        }
        if ($n == 1) {
            $db->query("UPDATE `B_CLIENT_BALANS` SET `saldo` = saldo - '$summ', `cash_id` = '$cash_id', `last_update` = NOW() WHERE `client_id` = '$client_conto_id';");
        }

        return true;
    }

    function viewDpSaleInvoice($dp_id, $invoice_id) { $db = DbSingleton::getDb();
        $gmanual = new gmanual;
        $prefix = $doc_type_name = "";
        $doc_nom = 0; $volume = 0; $status_invoice = 0; $list = "";
        $form = ""; $form_htm = RD . "/tpl/dp_sale_invoice_view.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $r = $db->query("SELECT sv.*, t.name as tpoint_name, sl.name as seller_name, cl.name as client_name, dt.mcaption as doc_type_name, dt.mvalue as doc_type_abr, ch.abr2 as cash_abr 
        FROM `J_SALE_INVOICE` sv
            LEFT OUTER JOIN `CASH` ch ON ch.id=sv.cash_id
            LEFT OUTER JOIN `T_POINT` t ON t.id=sv.tpoint_id
            LEFT OUTER JOIN `A_CLIENTS` sl ON sl.id=sv.seller_id
            LEFT OUTER JOIN `A_CLIENTS` cl ON cl.id=sv.client_conto_id
            LEFT OUTER JOIN `manual` dt ON dt.key='client_sale_type' AND dt.id=sv.doc_type_id
        WHERE sv.status = 1 AND sv.dp_id = '$dp_id' AND sv.id = '$invoice_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $prefix         = $db->result($r, 0, "prefix");
            $doc_nom        = $db->result($r, 0, "doc_nom");
            $data_create    = $db->result($r, 0, "data_create");
            $tpoint_name    = $db->result($r, 0, "tpoint_name");
            $seller_name    = $db->result($r, 0, "seller_name");
            $client_name    = $db->result($r, 0, "client_name");
            $doc_type_name  = $db->result($r, 0, "doc_type_name");
            $summ           = $db->result($r, 0, "summ");
            $cash_abr       = $db->result($r, 0, "cash_abr");
            $data_pay       = $db->result($r, 0, "data_pay");
            $status_invoice = $db->result($r, 0, "status_invoice");

            $form = str_replace("{invoice_id}", $invoice_id, $form);
            $form = str_replace("{data}", $data_create, $form);
            $form = str_replace("{data_pay}", $data_pay, $form);
            $form = str_replace("{prefix}", $prefix, $form);
            $form = str_replace("{doc_nom}", $doc_nom, $form);
            $form = str_replace("{tpoint_name}", $tpoint_name, $form);
            $form = str_replace("{seller_name}", $seller_name, $form);
            $form = str_replace("{client_name}", $client_name, $form);
            $form = str_replace("{doc_type_name}", $doc_type_name, $form);
            $form = str_replace("{invoice_summ}", $summ, $form);
            $form = str_replace("{cash_name}", $cash_abr, $form);
            $form = str_replace("{volume}", $volume, $form);

            $r = $db->query("SELECT * FROM `J_SALE_INVOICE_STR` WHERE `invoice_id` = '$invoice_id' ORDER BY `id` ASC;");
            $n = $db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $art_nr_ds  = $db->result($r, $i - 1, "article_nr_displ");
                $brand_id   = $db->result($r, $i - 1, "brand_id");
                $brand_name = $this->getBrandName($brand_id);
                $amount     = $db->result($r, $i - 1, "amount");
                $price      = $db->result($r, $i - 1, "price");
                $price_end  = $db->result($r, $i - 1, "price_end");
                $discount   = $db->result($r, $i - 1, "discount");
                $summ       = $db->result($r, $i - 1, "summ");
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

        return array($form, "№ $prefix-$doc_nom; вид:$doc_type_name; Статус: " . $gmanual->get_gmanual_caption($status_invoice));
    }

    function getClientInfo($client_id)
    {
        $db = DbSingleton::getDb();
        $r = $db->query("SELECT sl.full_name as seller_name, sl.phone, sld.edrpou, sld.account, sld.bank, sld.mfo, sld.vytjag, sld.address_fakt as seller_address 
        FROM `A_CLIENTS` sl 
            LEFT OUTER JOIN `A_CLIENT_DETAILS` sld ON (sld.client_id = sl.id AND sld.main = 1)
        WHERE sl.id = '$client_id' LIMIT 1;");
        $seller_name    = $db->result($r, 0, "seller_name");
        $seller_adr     = $db->result($r, 0, "seller_address");
        $edrpou         = $db->result($r, 0, "edrpou");
        $account        = $db->result($r, 0, "account");
        $bank           = $db->result($r, 0, "bank");
        $mfo            = $db->result($r, 0, "mfo");
        $vat            = $db->result($r, 0, "vytjag");
        $phone          = $db->result($r, 0, "phone");
        if ($phone === "") {
            $phone = "не вказано";
        }
        return array($seller_name, $seller_adr, $edrpou, $account, $bank, $mfo, $vat, $phone);
    }

    /*
     * Друкувати рахунок ДП
     * */
    public function printDpJournal($dp_id, $type_id)
    {
        $db = DbSingleton::getDb();

        $type_id = (int)$type_id;
        if (empty($type_id)) {
            $type_id = 1;
        }

        $money = new toMoney;
        $slave = new slave;
        $invoice_summ = 0;
        $list = "";

        $r = $db->query("SELECT dp.*, cl.full_name as client_name, cld.address_fakt as client_address 
        FROM `J_DP` dp
            LEFT OUTER JOIN `A_CLIENTS` cl ON (cl.id = dp.client_conto_id)
            LEFT OUTER JOIN `A_CLIENT_DETAILS` cld ON (cld.client_id = dp.client_conto_id AND cld.main = 1)		
        WHERE dp.id = '$dp_id' LIMIT 1;");
        $client_id      = $db->result($r, 0, "client_id");
        $prefix         = $db->result($r, 0, "prefix");
        $doc_nom        = $db->result($r, 0, "doc_nom");
        $doc_type_id    = (int)$db->result($r, 0, "doc_type_id");
        $tpoint_id      = $db->result($r, 0, "tpoint_id");
        $client_name    = $db->result($r, 0, "client_name");
        $client_address = $db->result($r, 0, "client_address");
        $usd_to_uah     = (int)$db->result($r, 0, "usd_to_uah");
        $eur_to_uah     = $db->result($r, 0, "eur_to_uah");
        $seller_id      = $this->getSellerId($tpoint_id, $doc_type_id);
        [$seller_name, $seller_address, $edrpou, $account, $bank, $mfo, $vat, $phone] = $this->getClientInfo($seller_id);

        if ($usd_to_uah === 0) {
            [$usd_to_uah, $eur_to_uah] = $this->getKoursData();
        }

        $r = $db->query("SELECT * FROM `J_DP_STR` WHERE `dp_id` = $dp_id;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $art_id         = $db->result($r, $i - 1, "art_id");
            $art_nr_ds      = $db->result($r, $i - 1, "article_nr_displ");
            $brand_id       = $db->result($r, $i - 1, "brand_id");
            $amount         = (int)$db->result($r, $i - 1, "amount");
            $price_end      = $db->result($r, $i - 1, "price_end");
            $article_name   = $this->getArticleName($art_id);
            $brand_name     = $this->getBrandName($brand_id);
            $unit           = $this->getUnitArticle($art_id);

            $cash_id_from = (int)$this->getArticlePriceRatingCash($art_id);
            if ($cash_id_from === 1) {
                $price_end = $this->getClientPriceRounding($client_id, $price_end);
            } elseif ($cash_id_from === 2) {
                $price_end *= $usd_to_uah;
                $price_end = $this->getClientPriceRounding($client_id, $price_end);
            } elseif ($cash_id_from === 3) {
                $price_end *= $eur_to_uah;
                $price_end = $this->getClientPriceRounding($client_id, $price_end);
            }

            $summ = $price_end * $amount;
            $invoice_summ += $summ;

            $text = "$art_nr_ds ($brand_name)";
            if ($type_id === 2) {
                $text = $brand_name;
            }

            $list .= "<tr>
                <td align='center'>$i</td>
                <td align='left'>$text</td>
                <td align='left'>$article_name</td>
                <td align='center'>$unit</td>
                <td align='center'>$amount</td>
                <td align='right'>$price_end </td>
                <td align='right'>$summ</td>
            </tr>";
        }

        $vat_summ = $invoice_summ / 6;
        $art_text = "Індекс/Бренд";
        if ($type_id === 2) {
            $art_text = "Бренд";
        }

        if ($doc_type_id === 61) {
            $form = ""; $form_htm = RD . "/tpl/dp_journal_print.htm";
            if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        } else {
            $form = ""; $form_htm = RD . "/tpl/dp_journal_print_2.htm";
            if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        }

        $form = str_replace("{curtime}",date("d.m.Y H:i:s"), $form);
        $form = str_replace("{prefix}", $prefix, $form);
        $form = str_replace("{article_brand_text}", $art_text, $form);
        $form = str_replace("{doc_nom}", $doc_nom, $form);
        $form = str_replace("{data}",date("d.m.Y"), $form);
        $form = str_replace("{client_name}", $client_name, $form);
        $form = str_replace("{seller_name}", $seller_name, $form);
        $form = str_replace("{dp_str_list}", $list, $form);
        $form = str_replace("{invoice_summ}", $slave->to_money($invoice_summ), $form);
        $form = str_replace("{invoice_summ_word}", $money->num2str($slave->to_money($invoice_summ)), $form);
        $form = str_replace("{vat_summ}", $slave->to_money($vat_summ), $form);
        $form = str_replace("{edrpou}", $edrpou, $form);
        $form = str_replace("{rr}", $account, $form);
        $form = str_replace("{bank}", $bank, $form);
        $form = str_replace("{mfo}", $mfo, $form);
        $form = str_replace("{ipn_nom}", $vat, $form);
        $form = str_replace("{client_address}", $client_address, $form);
        $form = str_replace("{seller_address}", $seller_address, $form);
        $form = str_replace("{phone_client}", $phone, $form);

        $mp = new media_print;
        $mp->print_document($form, "A4");

        return $form;
    }

    function printDpSaleInvoice($dp_id) { $db = DbSingleton::getDb();
        $money = new toMoney; $slave = new slave; $sale_invoice = new sale_invoice;
        $dp_id = (int)$dp_id;
        $invoice_summ = 0; $list = "";
        $form = ""; $form_htm = RD . "/tpl/dp_sale_invoice_print_non_cash.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $r = $db->query("SELECT dp.*, cl.full_name as client_name, sl.full_name as seller_name, 
        sl.phone, sld.edrpou, sld.account, sld.bank, sld.mfo, sld.vytjag, sld.address_fakt as seller_address, cld.address_fakt as client_address 
        FROM `J_SALE_INVOICE` dp
            LEFT OUTER JOIN `A_CLIENTS` cl on (cl.id=dp.client_id)
            LEFT OUTER JOIN `A_CLIENT_DETAILS` cld on (cld.client_id=dp.client_id and cld.main=1)
            LEFT OUTER JOIN `A_CLIENTS` sl on (sl.id=dp.seller_id)
            LEFT OUTER JOIN `A_CLIENT_DETAILS` sld on (sld.client_id=dp.seller_id and sld.main=1)
        WHERE dp.id = $dp_id LIMIT 1;");
        $prefix             = $db->result($r, 0, "prefix");
        $doc_nom            = $db->result($r, 0, "doc_nom");
        $data_create        = $db->result($r, 0, "data_create");
        $data_create        = date("d.m.Y", strtotime($data_create));
        $client_name        = $db->result($r, 0, "client_name");
        $seller_id          = $db->result($r, 0, "seller_id");
        $client_conto_id    = $db->result($r, 0, "client_conto_id");
        $seller_name        = $db->result($r, 0, "seller_name");
        $seller_address     = $db->result($r, 0, "seller_address");
        $client_address     = $db->result($r, 0, "client_address");
        $phone              = $db->result($r, 0, "phone");
        [$edrpou, $account, $bank, $mfo, $vat] = $sale_invoice->getSellerDetails($client_conto_id, $seller_id);
        if ($phone === "") {
            $phone = "не вказано";
        }

        $r = $db->query("SELECT * FROM `J_SALE_INVOICE_STR` WHERE `invoice_id` = $dp_id;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $art_id         = $db->result($r, $i - 1, "art_id");
            $article_name   = $this->getArticleName($art_id);
            $art_nr_ds      = $db->result($r, $i - 1, "article_nr_displ");
            $brand_id       = $db->result($r, $i - 1, "brand_id");
            $brand_name     = $this->getBrandName($brand_id);
            $amount         = (int)$db->result($r, $i - 1, "amount");
            $unit           = $this->getUnitArticle($art_id);
            $price_end      = $db->result($r, $i - 1, "price_end");
            $summ           = $price_end * $amount;
            $invoice_summ   += $summ;
            $price_end      = $slave->to_money($price_end);
            $summ           = $slave->to_money($summ);
            $list .= "<tr>
                <td align='center'>$i</td>
                <td align='left'>$art_nr_ds ($brand_name)</td>
                <td align='left'>$article_name</td>
                <td align='center'>$unit</td>
                <td align='center'>$amount</td>
                <td align='right'>$price_end</td>
                <td align='right'>$summ</td>
            </tr>";
        }
        $vat_summ = $invoice_summ / 6;
        $form = str_replace("{curtime}", date("d.m.Y H:i:s"), $form);
        $form = str_replace("{prefix}", $prefix, $form);
        $form = str_replace("{doc_nom}", $doc_nom, $form);
        $form = str_replace("{data}", $data_create, $form);
        $form = str_replace("{client_name}", $client_name, $form);
        $form = str_replace("{seller_name}", $seller_name, $form);
        $form = str_replace("{dp_str_list}", $list, $form);
        $form = str_replace("{invoice_summ}", $slave->to_money($invoice_summ), $form);
        $form = str_replace("{invoice_summ_word}", $money->num2str($slave->to_money($invoice_summ)), $form);
        $form = str_replace("{vat_summ}", $slave->to_money($vat_summ), $form);
        $form = str_replace("{edrpou}", $edrpou, $form);
        $form = str_replace("{rr}", $account, $form);
        $form = str_replace("{bank}", $bank, $form);
        $form = str_replace("{mfo}", $mfo, $form);
        $form = str_replace("{ipn_nom}", $vat, $form);
        $form = str_replace("{client_address}", $client_address, $form);
        $form = str_replace("{seller_address}", $seller_address, $form);
        $form = str_replace("{phone_client}", $phone, $form);
        $mp = new media_print;
        $mp->print_document($form, "A4");

        return $form;
    }

    function getUnitArticle($art_id)
    {
        $db = DbSingleton::getTokoDb();
        $abr = "";
        $r = $db->query("SELECT t2u.abr 
        FROM `T2_PACKAGING` t2p 
            LEFT OUTER JOIN `units` t2u ON (t2u.id = t2p.UNITS_ID)
        WHERE t2p.ART_ID = '$art_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $abr = $db->result($r, 0, "abr");
        }
        return $abr;
    }

    function printSaleInvoice($invoice_id)
    {
        $db = DbSingleton::getDb();
        $slave = new slave; $money = new toMoney;
        $sel_ar = [];
        $volume = 0; $invoice_summ = 0;
        $list = ""; $form = ""; $address_send = "";

        $r = $db->query("SELECT sv.*, t.name as tpoint_name, sl.name as seller_name, sld.edrpou, ot.name as org_type_abr, 
        cl.name as client_name, dt.mcaption as doc_type_name, dt.mvalue as doc_type_abr, ch.abr2 as cash_abr 
        FROM `J_SALE_INVOICE` sv
            LEFT OUTER JOIN `CASH` ch ON ch.id=sv.cash_id
            LEFT OUTER JOIN `T_POINT` t ON t.id=sv.tpoint_id
            LEFT OUTER JOIN `A_CLIENTS` sl ON sl.id=sv.seller_id
            LEFT OUTER JOIN `A_CLIENT_DETAILS` sld ON (sld.client_id=sv.seller_id AND sld.main=1)
            LEFT OUTER JOIN `A_ORG_TYPE` ot ON ot.id=sl.org_type
            LEFT OUTER JOIN `A_CLIENTS` cl ON cl.id=sv.client_conto_id
            LEFT OUTER JOIN `manual` dt ON dt.key='client_sale_type' AND dt.id=sv.doc_type_id
        WHERE sv.status = 1 AND sv.id = '$invoice_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $prefix             = $db->result($r, 0, "prefix");
            $doc_nom            = $db->result($r, 0, "doc_nom");
            $data_create        = $db->result($r, 0, "data_create");
            $tpoint_name        = $db->result($r, 0, "tpoint_name");
            $seller_name        = $db->result($r, 0, "seller_name");
            $edrpou             = $db->result($r, 0, "edrpou");
            $org_type_abr       = $db->result($r, 0, "org_type_abr");
            $client_name        = $db->result($r, 0, "client_name");
            $doc_type_id        = $db->result($r, 0, "doc_type_id");
            $doc_type_name      = $db->result($r, 0, "doc_type_name");
            $cash_abr           = $db->result($r, 0, "cash_abr");
            $data_pay           = $db->result($r, 0, "data_pay");
            $dp_delivery_adr    = $db->result($r, 0, "delivery_address");
            $dp_id              = $db->result($r, 0, "dp_id");
            $delivery_address   = $this->getDpUserDelivery($dp_id);
            if ($delivery_address == "") {
                $delivery_address = $dp_delivery_adr;
            }

            $r = $db->query("SELECT sis.*, ss.storage_id_from, ss.select_id 
            FROM `J_SALE_INVOICE_STR` sis 
                LEFT OUTER JOIN `J_SALE_INVOICE_STORSEL` iss on iss.invoice_id=sis.invoice_id
                LEFT OUTER JOIN `J_SELECT_STR` ss on ss.select_id=iss.select_id
            WHERE sis.invoice_id='$invoice_id' 
            GROUP BY sis.id 
            ORDER BY sis.id ASC;");
            $n = $db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $art_id         = $db->result($r, $i - 1, "art_id");
                $article_name   = $this->getArticleName($art_id);
                $art_nr_ds      = $db->result($r, $i - 1, "article_nr_displ");
                $brand_id       = $db->result($r, $i - 1, "brand_id");
                $brand_name     = $this->getBrandName($brand_id);
                $amount         = $db->result($r, $i - 1, "amount");
                $storage_id     = $db->result($r, $i - 1, "storage_id_from");
                $select_id      = $db->result($r, $i - 1, "select_id");
                $price_end      = $db->result($r, $i - 1, "price_end");
                $summ           = $db->result($r, $i - 1, "summ");
                $invoice_summ   += $summ;
                $list .= "
                <tr>
                    <td align='center'>$i</td>
                    <td align='center'>$storage_id</td>
                    <td align='left'>$art_nr_ds</td>
                    <td align='center'>$brand_name</td>
                    <td align='left'>$article_name</td>
                    <td align='center'>$amount</td>
                    <td align='center'>$price_end</td>
                    <td align='center'>$summ</td>
                </tr>";
                $sel_ar[$select_id] = $select_id;
            }
            $storsel_list = "СКВ-";
            foreach ($sel_ar as $slr) {
                $storsel_list .= "$slr/";
            }

            $form_htm = "";
            if ($doc_type_id == 64) {$form_htm = RD . "/tpl/dp_sale_invoice_print_64.htm";}
            if ($doc_type_id == 63) {$form_htm = RD . "/tpl/dp_sale_invoice_print_63.htm";}
            if ($doc_type_id == 61) {$form_htm = RD . "/tpl/dp_sale_invoice_print_61.htm";}
            if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }

            $form = str_replace("{curtime}", date("d/m/Y H:i:s"), $form);
            $form = str_replace("{invoice_id}", $invoice_id, $form);
            $form = str_replace("{data}", $data_create, $form);
            $form = str_replace("{data_pay}", $data_pay, $form);
            $form = str_replace("{prefix}", $prefix, $form);
            $form = str_replace("{doc_nom}", $doc_nom, $form);
            $form = str_replace("{tpoint_name}", $tpoint_name, $form);
            $form = str_replace("{seller_name}", $seller_name, $form);
            $form = str_replace("{client_name}", $client_name, $form);
            $form = str_replace("{doc_type_name}", $doc_type_name, $form);
            $form = str_replace("{invoice_summ}", $slave->to_money($invoice_summ), $form);
            $form = str_replace("{invoice_summ_word}", $money->num2str($invoice_summ), $form);
            $form = str_replace("{cash_name}", $cash_abr, $form);
            $form = str_replace("{address_send}", $address_send, $form);
            $form = str_replace("{edrpou}", $edrpou, $form);
            $form = str_replace("{org_type_abr}", $org_type_abr, $form);
            $form = str_replace("{cash_abr}", $cash_abr, $form);
            $form = str_replace("{volume}", $volume, $form);
            $form = str_replace("{storsel_list}", $storsel_list, $form);
            $form = str_replace("{sale_invoice_str_list}", $list, $form);
            $form = str_replace("{delivery_address}", $delivery_address, $form);

            $mp = new media_print;
            if ($doc_type_id == 63) {$mp->print_document($form, "A4-L");}
            if ($doc_type_id == 64) {$mp->print_document($form, array(210, 280));}
            if ($doc_type_id == 61) {$mp->print_document($form, "A4-L");}
        }

        return $form;
    }

    function printWriteOff($write_off_id)
    {
        $db = DbSingleton::getDb();
        $slave = new slave; $money = new toMoney;
        $sel_ar = [];
        $list = ""; $form = ""; $address_send = "";
        $volume = 0; $invoice_summ = 0;
        $r = $db->query("SELECT sv.*, t.name as tpoint_name, sl.name as seller_name, sld.edrpou, ot.name as org_type_abr, 
        cl.name as client_name, dt.mcaption as doc_type_name, dt.mvalue as doc_type_abr, ch.abr2 as cash_abr 
        FROM `J_WRITE_OFF` sv
            LEFT OUTER JOIN `CASH` ch ON ch.id=sv.cash_id
            LEFT OUTER JOIN `T_POINT` t ON t.id=sv.tpoint_id
            LEFT OUTER JOIN `A_CLIENTS` sl ON sl.id=sv.seller_id
            LEFT OUTER JOIN `A_CLIENT_DETAILS` sld ON (sld.client_id=sv.seller_id AND sld.main=1)
            LEFT OUTER JOIN `A_ORG_TYPE` ot ON ot.id=sl.org_type
            LEFT OUTER JOIN `A_CLIENTS` cl ON cl.id=sv.client_conto_id
            LEFT OUTER JOIN `manual` dt ON (dt.key='client_sale_type' AND dt.id=sv.doc_type_id)
        WHERE sv.status = 1 AND sv.id = '$write_off_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $prefix             = $db->result($r, 0, "prefix");
            $doc_nom            = $db->result($r, 0, "doc_nom");
            $data_create        = $db->result($r, 0, "data_create");
            $tpoint_name        = $db->result($r, 0, "tpoint_name");
            $seller_name        = $db->result($r, 0, "seller_name");
            $edrpou             = $db->result($r, 0, "edrpou");
            $org_type_abr       = $db->result($r, 0, "org_type_abr");
            $client_name        = $db->result($r, 0, "client_name");
            $doc_type_id        = $db->result($r, 0, "doc_type_id");
            $doc_type_name      = $db->result($r, 0, "doc_type_name");
            $cash_abr           = $db->result($r, 0, "cash_abr");
            $data_pay           = $db->result($r, 0, "data_pay");
            $dp_delivery_adr    = $db->result($r, 0, "delivery_address");
            $dp_id              = $db->result($r, 0, "dp_id");
            $delivery_address   = $this->getDpUserDelivery($dp_id);
            if ($delivery_address === "") {
                $delivery_address = $dp_delivery_adr;
            }

            $r = $db->query("SELECT sis.*, ss.storage_id_from, ss.select_id 
            FROM `J_WRITE_OFF_STR` sis 
                LEFT OUTER JOIN `J_WRITE_OFF_STORSEL` iss ON (iss.write_off_id = sis.write_off_id)
                LEFT OUTER JOIN `J_SELECT_STR` ss ON (ss.select_id = iss.select_id)
            WHERE sis.write_off_id = '$write_off_id' 
            GROUP BY sis.id 
            ORDER BY sis.id ASC;");
            $n = $db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $art_id         = $db->result($r, $i - 1, "art_id");
                $article_name   = $this->getArticleName($art_id);
                $art_ns_ds      = $db->result($r, $i - 1, "article_nr_displ");
                $brand_id       = $db->result($r, $i - 1, "brand_id");
                $brand_name     = $this->getBrandName($brand_id);
                $amount         = $db->result($r, $i - 1, "amount");
                $storage_id     = $db->result($r, $i - 1, "storage_id_from");
                $select_id      = $db->result($r, $i - 1, "select_id");
                $price_end      = $db->result($r, $i - 1, "price_end");
                $summ           = $db->result($r, $i - 1, "summ");
                $invoice_summ   += $summ;
                $list .= "<tr>
                    <td align='center'>$i</td>
                    <td align='center'>$storage_id</td>
                    <td align='left'>$art_ns_ds</td>
                    <td align='center'>$brand_name</td>
                    <td align='left'>$article_name</td>
                    <td align='center'>$amount</td>
                    <td align='center'>$price_end</td>
                    <td align='center'>$summ</td>
                </tr>";
                $sel_ar[$select_id] = $select_id;
            }

            $storsel_list = "СКВ-";
            foreach ($sel_ar as $slr) {
                $storsel_list .= "$slr/";
            }

            $form_htm = "";
            if ($doc_type_id == 64) {$form_htm = RD . "/tpl/dp_sale_invoice_print_64.htm";}
            if ($doc_type_id == 63) {$form_htm = RD . "/tpl/dp_sale_invoice_print_63.htm";}
            if ($doc_type_id == 61) {$form_htm = RD . "/tpl/dp_sale_invoice_print_61.htm";}
            if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }

            $form = str_replace("{curtime}", date("d/m/Y H:i:s"), $form);
            $form = str_replace("{invoice_id}", $write_off_id, $form);
            $form = str_replace("{data}", $data_create, $form);
            $form = str_replace("{data_pay}", $data_pay, $form);
            $form = str_replace("{prefix}", $prefix, $form);
            $form = str_replace("{doc_nom}", $doc_nom, $form);
            $form = str_replace("{tpoint_name}", $tpoint_name, $form);
            $form = str_replace("{seller_name}", $seller_name, $form);
            $form = str_replace("{client_name}", $client_name, $form);
            $form = str_replace("{doc_type_name}", $doc_type_name, $form);
            $form = str_replace("{invoice_summ}", $slave->to_money($invoice_summ), $form);
            $form = str_replace("{invoice_summ_word}", $money->num2str($invoice_summ), $form);
            $form = str_replace("{cash_name}", $cash_abr, $form);
            $form = str_replace("{address_send}", $address_send, $form);
            $form = str_replace("{edrpou}", $edrpou, $form);
            $form = str_replace("{org_type_abr}", $org_type_abr, $form);
            $form = str_replace("{cash_abr}", $cash_abr, $form);
            $form = str_replace("{volume}", $volume, $form);
            $form = str_replace("{storsel_list}", $storsel_list, $form);
            $form = str_replace("{sale_invoice_str_list}", $list, $form);
            $form = str_replace("{delivery_address}", $delivery_address, $form);

            $mp = new media_print;
            if ($doc_type_id == 63) {$mp->print_document($form, "A4-L");}
            if ($doc_type_id == 64) {$mp->print_document($form, array(210, 280));}
            if ($doc_type_id == 61) {$mp->print_document($form, "A4-L");}
        }

        return $form;
    }

    function updateStockFromStorage($art_id, $storage_id_from, $cell_id_from, $cell_use, $amount)
    {
        $dbt = DbSingleton::getTokoDb();
        $er = 1;
        $r = $dbt->query("SELECT `RESERV_AMOUNT` FROM `T2_ARTICLES_STRORAGE` WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id_from LIMIT 1;");
        $n = $dbt->num_rows($r);
        if ($n == 1) {
            $t2s_reserv_amount = $dbt->result($r, 0, "RESERV_AMOUNT");
            if ($amount <= $t2s_reserv_amount) {
                $t2s_reserv_amount -= $amount;
                $dbt->query("UPDATE `T2_ARTICLES_STRORAGE` SET `RESERV_AMOUNT` = '$t2s_reserv_amount' WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id_from LIMIT 1;");
                if ($cell_use == 1) {
                    $r1 = $dbt->query("SELECT `RESERV_AMOUNT` FROM `T2_ARTICLES_STRORAGE_CELLS` WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id_from AND `STORAGE_CELLS_ID` = $cell_id_from LIMIT 1;");
                    $n1 = $dbt->num_rows($r1);
                    if ($n1 == 1) {
                        $t2sc_reserv_amount = $dbt->result($r1, 0, "RESERV_AMOUNT");
                        if ($amount > 0) {
                            $t2sc_reserv_amount -= $amount;
                            $dbt->query("UPDATE `T2_ARTICLES_STRORAGE_CELLS` SET `RESERV_AMOUNT` = '$t2sc_reserv_amount' WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id_from AND `STORAGE_CELLS_ID` = $cell_id_from LIMIT 1;");
                        }
                    }
                }
            }
            $er = 0;
        }

        return $er;
    }

    function updateStockToStorage($art_id, $storage_id_to, $cell_id_to, $cell_use, $amount)
    {
        $dbt = DbSingleton::getTokoDb();
        $er = 1;
        $r = $dbt->query("SELECT `AMOUNT` FROM `T2_ARTICLES_STRORAGE` WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id_to LIMIT 1;");
        $n = $dbt->num_rows($r);
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
                $t2s_amount += $amount;
                $dbt->query("UPDATE `T2_ARTICLES_STRORAGE` SET `AMOUNT` = '$t2s_amount' WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id_to LIMIT 1;");
                if ($cell_use == 1) {
                    $r1 = $dbt->query("SELECT `AMOUNT` FROM `T2_ARTICLES_STRORAGE_CELLS` WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id_to AND `STORAGE_CELLS_ID` = $cell_id_to LIMIT 1;");
                    $n1 = $dbt->num_rows($r1);
                    if ($n1 == 0) {
                        $dbt->query("INSERT INTO `T2_ARTICLES_STRORAGE_CELLS` (`ART_ID`,`AMOUNT`,`RESERV_AMOUNT`,`STORAGE_ID`,`STORAGE_CELLS_ID`) VALUES ('$art_id','$amount','0','$storage_id_to','$cell_id_to');");
                    }
                    if ($n1 == 1) {
                        $t2sc_amount = $dbt->result($r1, 0, "AMOUNT");
                        if ($amount > 0) {
                            $t2sc_amount += $amount;
                            $dbt->query("UPDATE `T2_ARTICLES_STRORAGE_CELLS` SET `AMOUNT` = '$t2sc_amount' WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id_to AND `STORAGE_CELLS_ID` = $cell_id_to LIMIT 1;");
                        }
                    }
                }
                $er = 0;
            }
        }

        return $er;
    }

    function updateStockFromStorageLocal($art_id, $storage_id_from, $cell_id_from, $cell_id_to, $amount)
    {
        $dbt = DbSingleton::getTokoDb();
        $er = 1;
        $r = $dbt->query("SELECT `AMOUNT`, `RESERV_AMOUNT` FROM `T2_ARTICLES_STRORAGE` WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id_from LIMIT 1;");
        $n = $dbt->num_rows($r);
        if ($n == 1) {
            $t2s_amount = $dbt->result($r, 0, "AMOUNT");
            $t2s_reserv_amount = $dbt->result($r, 0, "RESERV_AMOUNT");
            if ($amount <= $t2s_reserv_amount) {
                $t2s_reserv_amount -= $amount;
                $t2s_amount += $amount;
                $dbt->query("UPDATE `T2_ARTICLES_STRORAGE` SET `RESERV_AMOUNT`='$t2s_reserv_amount', `AMOUNT`='$t2s_amount' WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id_from LIMIT 1;");
                $r1 = $dbt->query("SELECT `RESERV_AMOUNT` FROM `T2_ARTICLES_STRORAGE_CELLS` WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id_from AND `STORAGE_CELLS_ID` = $cell_id_from LIMIT 1;");
                $n1 = $dbt->num_rows($r1);
                if ($n1 == 1) {
                    $t2sc_reserv_amount = $dbt->result($r1, 0, "RESERV_AMOUNT");
                    if ($amount > 0) {
                        $t2sc_reserv_amount -= $amount;
                        $dbt->query("UPDATE `T2_ARTICLES_STRORAGE_CELLS` SET `RESERV_AMOUNT`='$t2sc_reserv_amount' WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id_from AND `STORAGE_CELLS_ID` = $cell_id_from LIMIT 1;");
                    }
                }
                $r2 = $dbt->query("SELECT `AMOUNT` FROM `T2_ARTICLES_STRORAGE_CELLS` WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id_from AND `STORAGE_CELLS_ID` = $cell_id_to LIMIT 1;");
                $n2 = $dbt->num_rows($r2);
                if ($n2 == 0) {
                    $dbt->query("INSERT INTO `T2_ARTICLES_STRORAGE_CELLS` (`ART_ID`,`AMOUNT`,`RESERV_AMOUNT`,`STORAGE_ID`,`STORAGE_CELLS_ID`) VALUES ('$art_id','$amount','0','$storage_id_from','$cell_id_to');");
                }
                if ($n2 == 1) {
                    $t2sc_amount2 = $dbt->result($r2, 0, "AMOUNT");
                    if ($amount > 0) {
                        $t2sc_amount2 += $amount;
                        $dbt->query("UPDATE `T2_ARTICLES_STRORAGE_CELLS` SET `AMOUNT`='$t2sc_amount2' WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id_from AND `STORAGE_CELLS_ID` = $cell_id_to LIMIT 1;");
                    }
                }
            }
            $er = 0;
        }
        return $er;
    }

    /*
     * MONEY PAY
     * */
    function loadDpMoneyPay($dp_id)
    {
        $db = DbSingleton::getDb();
        $form = ""; $form_htm = RD . "/tpl/dp_money_pay_list.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }

        $r = $db->query("SELECT `id` FROM `J_SALE_INVOICE` WHERE `status` = 1 AND `dp_id` = $dp_id;");
        $n = $db->num_rows($r);
        $invoice_str = "0";
        for ($i = 1; $i <= $n; $i++) {
            $invoice_str .= "," . $db->result($r, $i - 1, "id");
        }

        $list = "";
        $r = $db->query("SELECT pay.*, pt.mcaption as pay_type_caption, pb.name as paybox_name, c.abr, pst.summ_pay, pst.doc_cash_id, cd.abr as d_abr, pst.pay_cash_kours 
        FROM `J_PAY` pay
            LEFT OUTER JOIN `J_PAY_STR` pst ON (pst.pay_id = pay.id)
            LEFT OUTER JOIN `CASH` c ON (c.id = pay.cash_id)
            LEFT OUTER JOIN `CASH` cd ON (cd.id = pst.doc_cash_id)
            LEFT OUTER JOIN `T_POINT_PAY_BOX` pb ON (pb.id = pay.paybox_id)
            LEFT OUTER JOIN `manual` pt ON (pt.key = 'pay_type_id' AND pt.id = pay.pay_type_id)
        WHERE pay.status = 1 AND pst.parrent_doc_id IN ($invoice_str) AND pst.parrent_doc_type_id != 0 
        GROUP BY pay.id 
        ORDER BY pay.data_time DESC, pay.id DESC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id                 = $db->result($r, $i - 1, "id");
            $data_time          = $db->result($r, $i - 1, "data_time");
            $pay_type_caption   = $db->result($r, $i - 1, "pay_type_caption");
            $paybox_name        = $db->result($r, $i - 1, "paybox_name");
            $doc_nom            = $db->result($r, $i - 1, "doc_nom");
            $summ               = $db->result($r, $i - 1, "summ");
            $summ_pay           = $db->result($r, $i - 1, "summ_pay");
            $cash_name          = $db->result($r, $i - 1, "abr");
            $doc_cash_name      = $db->result($r, $i - 1, "d_abr");
            $user_name          = $this->getMediaUserName($db->result($r, $i - 1, "user_id"));
            $pay_cash_kours     = $db->result($r, $i - 1, "pay_cash_kours");
            $list .= "
            <tr id='strStsRow_$i' onClick='loadDpMoneyPay(\"$dp_id\",\"$id\");'>
                <td align='center'>$i</td>
                <td align='center'>$data_time</td>
                <td>$pay_type_caption</td>
                <td align='center' style='min-width:140px;'>$doc_nom</td>
                <td align='center' style='min-width:140px;'>$paybox_name</td>
                <td align='right' style='min-width:120px;'>$summ $cash_name</td>
                <td align='right' style='min-width:120px;'>$summ_pay $doc_cash_name</td>
                <td align='right'>$pay_cash_kours</td>
                <td>$user_name</td>
            </tr>";
        }
        if ($n == 0) {
            $list = "<tr><td colspan=9 align='center'><h3>Документи оплати відсутні</h3></td></tr>";
        }

        $form = str_replace("{money_pay_list}", $list, $form);

        return $form;
    }

    /*
     * Orders From Site
     * */
    function countT2Requests()
    {
        $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT COUNT(`ID`) as kilk FROM `T2_QUESTIONS` WHERE `STATUS` = 1;");
        $count = $db->result($r, 0, "kilk");
        $back = ($count > 0) ? "style='background: red;'" : "";
        return array($count, $back);
    }

    function countSupplCoopSite()
    {
        $db = DbSingleton::getDb();
        $r = $db->query("SELECT COUNT(`id`) as kilk FROM `J_SUPPLIERS_COOPERATION` WHERE `status` = 166;");
        $count = $db->result($r, 0, "kilk");
        $back = ($count > 0) ? "style='background: red;'" : "";
        return array($count, $back);
    }

    function countReportOverdrafts()
    {
        $db = DbSingleton::getDb();
        session_start();
        $media_role_id = $_SESSION["media_role_id"];
        $date_cur = date("Y-m-d");
        $where = " AND `data_pay`<'$date_cur'";
        $tpoint = $this->getTpointbyUser();
        $where_tpoint = " AND `tpoint_id`=$tpoint ";
        if ($media_role_id == "1") {
            $where_tpoint = "";
        }
        $r = $db->query("SELECT COUNT(`id`) as count_ids FROM `J_SALE_INVOICE` WHERE `status` = 1 AND `summ_debit` > 0 $where $where_tpoint;");
        $count = $db->result($r, 0, "count_ids");
        $back = ($count > 0) ? "style='background: #f8ac59;'" : "";
        return array($count, $back);
    }

    function getTpointbyUser()
    {
        $db = DbSingleton::getDb();
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $tpoint = 1;
        $r = $db->query("SELECT `tpoint_id` FROM `media_users` WHERE `id` = '$user_id';");
        $n = $db->num_rows($r);
        if ($n > 0) {
            $tpoint = $db->result($r, 0, "tpoint_id");
        }
        return $tpoint;
    }

    function countOrdersSite()
    {
        $db = DbSingleton::getDb();
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $ses_tpoint_id = $_SESSION["media_tpoint_id"];
        $where = " AND `tpoint_id` = '$ses_tpoint_id'";
        if ($user_id == 1 || $user_id == 2 || $user_id == 7) {
            $where = "";
        }
        $r = $db->query("SELECT COUNT(`id`) as count_ids FROM `orders_new` WHERE `status` = 1 AND `dp_id` = '' $where;");
        $count = $db->result($r, 0, "count_ids");
        $back = ($count > 0) ? "style='background: red;'" : "";
        return array($count, $back);
    }

    function countUsersSite()
    {
        $db = DbSingleton::getDb();
        $r = $db->query("SELECT COUNT(`id`) as kilk_ret FROM `A_CLIENTS_USERS_RETAIL` WHERE `status` = 145 AND `client_category` = 140;");
        $kilk_ret = $db->result($r, 0, "kilk_ret");
        $r = $db->query("SELECT COUNT(`id`) as kilk_mag FROM `A_CLIENTS_USERS_RETAIL` WHERE `status` = 145 AND `client_category` != 140;");
        $kilk_mag = $db->result($r, 0, "kilk_mag");
        $count = "$kilk_mag / $kilk_ret";
        $back = ($kilk_mag > 0) ? "style='background: red;'" : "";
        return array($count, $back);
    }

    function getKours($val)
    {
        $db = DbSingleton::getDb();
        $r = $db->query("SELECT `kours_value` FROM `J_KOURS` WHERE `cash_id` = 2 AND `in_use` = 1 LIMIT 1;");
        $usd = number_format($db->result($r, 0, "kours_value"), 2, '.', '');
        $r = $db->query("SELECT `kours_value` FROM `J_KOURS` WHERE `cash_id` = 3 AND `in_use` = 1 LIMIT 1;");
        $euro = number_format($db->result($r, 0, "kours_value"), 2, '.', '');
        return (($val === "dollar") ? $usd : ($val === "euro" ? $euro : 0));
    }

    function getKoursFromUAH($price, $cur)
    {
        if ($cur == 2) {
            $price /= $this->getKours("dollar");
            $price = number_format($price, 2, '.', '');
        } elseif ($cur == 3) {
            $price /= $this->getKours("euro");
            $price = number_format($price, 2, '.', '');
        } else {
            $price = number_format($price, 2, '.', '');
        }
        return $price;
    }

    function getClientUserName($client_id, $user_id)
    {
        $db = DbSingleton::getDb();
        $r = $db->query("SELECT `client_id`, `name` FROM `A_CLIENTS_USERS` WHERE `id` = '$user_id' AND `client_id` = '$client_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n > 0) {
            $name = $db->result($r, 0, "name");
        } else {
            $r = $db->query("SELECT `name` FROM `A_CLIENTS_USERS_RETAIL` WHERE `id` = '$user_id' AND `client_id` = '$client_id' LIMIT 1;");
            $name = $db->result($r, 0, "name");
        }
        return $name;
    }

    function showOrdersSite() {
        $press = "";
        $data = date("Y-m-d");
        $form = ""; $form_htm = RD . "/tpl/dp_orders_site_list.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $list = $this->showOrderSiteRange($press, $data, $data);
        $form = str_replace(array("{orders_site_list}", "{data_start}", "{data_end}"), array($list, $data, $data), $form);
        return $form;
    }

    function getUserTpointId($user_id)
    {
        $db = DbSingleton::getDb();
        $tpoint_id = 0;
        $r = $db->query("SELECT `tpoint_id` FROM `media_users` WHERE `id` = '$user_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $tpoint_id = $db->result($r, 0, "tpoint_id");
        }
        return $tpoint_id;
    }

    function showOrderSiteRange($press, $data_start, $data_end)
    {
        $db = DbSingleton::getDb();
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $ses_tpoint_id = $this->getUserTpointId($user_id);
        $data_cur = date("Y-m-d");
        $list = "";
        $where = "";
        $where_status = "";
        if (!$press) {
            $where_status = "AND o.status=1 AND o.dp_id=''";
        } elseif ($data_start !== "" && $data_end !== "") {
            $where = "AND o.data>='$data_start 00:00:00' AND o.data<='$data_end 23:59:59'";
        } else {
            $where = " AND o.data>='$data_cur 00:00:00' AND o.data<='$data_cur 23:59:59'";
        }
        $r = $db->query("SELECT o.*, t.name as tpoint_name, c.client_category as client_cat, c.name as client_name, cr.name as retail_name, cit.CITY_NAME as region_name, csh.abr as cash_abr, dt.mcaption as delivery_type_caption 
        FROM `orders_new` o
           LEFT OUTER JOIN `T_POINT` t ON (t.id=o.tpoint_id)
           LEFT OUTER JOIN `A_CLIENTS` c ON (c.id=o.client_id)
           LEFT OUTER JOIN `A_CLIENTS_USERS` cr ON (cr.id=o.client_user_id)
           LEFT OUTER JOIN `T2_CITY` cit ON (cit.CITY_ID=o.region)
           LEFT OUTER JOIN `CASH` csh ON (csh.id=o.cash_id)
           LEFT OUTER JOIN `manual` dt ON (dt.id=o.delivery AND dt.`key`='delivery_type')
        WHERE o.id != 0 $where_status $where 
        ORDER BY o.dp_id;");
        $n = $db->num_rows($r);
        if ($n > 0) {
            for ($i = 1; $i <= $n; $i++) {
                $id             = $db->result($r, $i - 1, "id");
                $dp_id          = $db->result($r, $i - 1, "dp_id");
                $status         = $db->result($r, $i - 1, "status");
                $data           = $db->result($r, $i - 1, "data");
                $client_id      = $db->result($r, $i - 1, "client_id");
                $client_name    = $db->result($r, $i - 1, "client_name");
                $client_image   = $this->getClientTypeImage($client_id);
                $client_user_id = $db->result($r, $i - 1, "client_user_id");
                $retail_name    = $this->getClientUserName($client_id, $client_user_id);
                $tpoint_id      = $db->result($r, $i - 1, "tpoint_id");
                $tpoint_name    = $db->result($r, $i - 1, "tpoint_name");
                $cash_id        = $db->result($r, $i - 1, "cash_id");
                $cash_abr       = $db->result($r, $i - 1, "cash_abr");
                $name           = $db->result($r, $i - 1, "name");
                $email          = $db->result($r, $i - 1, "email");
                $phone          = $db->result($r, $i - 1, "phone");
                $region_name    = $db->result($r, $i - 1, "region_name");
                $address        = $db->result($r, $i - 1, "address");
                $delivery       = $db->result($r, $i - 1, "delivery");
                $delivery_type  = $db->result($r, $i - 1, "delivery_type_caption");
                $price_summ     = $db->result($r, $i - 1, "price_summ");
                $price_summ     = $this->getKoursFromUAH($price_summ, $cash_id);
                $color = "";
                if ($dp_id !== "") {
                    $color = 'background:lightgreen;';
                }
                if ($status == 0) {
                    $color = 'background:pink;';
                }
                if ($name !== "") {
                    $name = "($name)";
                } else {
                    $name = "";
                }
                //($client_cat == 140) ||
                if (($tpoint_id == $ses_tpoint_id) || ($user_id == 1 || $user_id == 2 || $user_id == 7)) {
                    $list .= "<tr id='strStsRow_$i' onClick='showOrdersSiteCard(\"$id\");' style='cursor:pointer; $color'>
                        <td align='center'>$i</td>
                        <td align='center'>$tpoint_name</td>
                        <td align='center'>$id</td>
                        <td align='center'>$data</td>
                        <td>$client_image $client_name</td>
                        <td>$retail_name $name</td>
                        <td style='min-width:140px;'>$phone $email</td>
                        <td>$region_name $address</td>
                        <td align='right' style='min-width:120px;'>$delivery_type $delivery</td>
                        <td align='right'>$price_summ $cash_abr</td>
                    </tr>";
                }
            }
        }

        return $list;
    }

    function showOrdersSiteCard($order_id)
    {
        $db = DbSingleton::getDb();
        $slave = new slave; $gmanual = new gmanual;
        $order_id = $slave->qq($order_id);
        $list = ""; $data = "";
        $form = ""; $form_htm = RD."/tpl/dp_orders_site_form.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }

        $r = $db->query("SELECT o.*, t.name as tpoint_name, c.name as client_name, cr.name as retail_name, cit.CITY_NAME as region_name, csh.abr as cash_abr, dt.mcaption as delivery_type_caption
        FROM `orders_new` o
            LEFT OUTER JOIN `T_POINT` t ON (t.id = o.tpoint_id)
            LEFT OUTER JOIN `A_CLIENTS` c ON (c.id = o.client_id)
            LEFT OUTER JOIN `A_CLIENTS_USERS_RETAIL` cr ON (cr.id = o.client_user_id)
            LEFT OUTER JOIN `T2_CITY` cit ON (cit.CITY_ID = o.region)
            LEFT OUTER JOIN `CASH` csh ON (csh.id = o.cash_id)
            LEFT OUTER JOIN `manual` dt ON (dt.id = o.delivery AND dt.`key` = 'delivery_type')
        WHERE o.id = $order_id LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 0) {
            $form = "<h2 align='center'>Замовлення відсутнє або передано в роботу</h2>";
        }
        if ($n == 1) {
            $id             = $db->result($r, 0, "id");
            $data           = $db->result($r, 0, "data");
            $client_id      = $db->result($r, 0, "client_id");
            $client_name    = $db->result($r, 0, "client_name");
            $client_user_id = $db->result($r, 0, "client_user_id");
            $tpoint_id      = $db->result($r, 0, "tpoint_id");
            $cash_abr       = $db->result($r, 0, "cash_abr");
            $name           = $db->result($r, 0, "name");
            $delivery_type  = $db->result($r, 0, "delivery_type_caption");
            $delivery_info  = $db->result($r, 0, "delivery_info");
            $price_summ     = $db->result($r, 0, "price_summ");
            $payment        = $db->result($r, 0, "payment");
            $payment_info   = $db->result($r, 0, "payment_info");
            $address        = $db->result($r, 0, "address");
            $city           = $db->result($r, 0, "region_name");
            $comment        = $db->result($r, 0, "comment");
            $order_info_id  = $db->result($r, 0, "order_info_id");

            $form = $this->getOrderInfoForm($form, $order_info_id);
            $form = str_replace("{data}", $data, $form);
            $form = str_replace("{order_id}", $id, $form);
            $form = str_replace("{name}", $name, $form);
            $form = str_replace("{cash_abr}", $cash_abr, $form);
            $form = str_replace("{summ}", $price_summ, $form);
            $form = str_replace("{retail_name}", $this->getClientUserName($client_id, $client_user_id), $form);
            $form = str_replace("{client_name}", $client_name, $form);
            $form = str_replace("{tpoint_name}", $this->getTpointName($tpoint_id), $form);
            $form = str_replace("{delivery}", $delivery_type, $form);
            $form = str_replace("{delivery_info}", $comment . " " . $delivery_info, $form);
            $form = str_replace("{payment}", $gmanual->get_gmanual_caption($payment), $form);
            $form = str_replace("{payment_info}", $payment_info, $form);
            $form = str_replace("{address}", $address, $form);
            $form = str_replace("{city_name}", $city, $form);

            $r1 = $db->query("SELECT * FROM `orders_str_new` WHERE `order_id` = '$order_id';");
            $n1 = $db->num_rows($r1);
            for ($i = 1; $i <= $n1; $i++) {
                $suppl_id       = $db->result($r1, $i - 1, "suppl_id");
                $suppl_name     = $this->getClientName($suppl_id);
                $art_id         = $db->result($r1, $i - 1, "art_id");
                $brand_id       = $db->result($r1, $i - 1, "brand_id");
                $amount         = (int)$db->result($r1, $i - 1, "amount");
                $price          = $db->result($r1, $i - 1, "price");
                $discount       = $db->result($r1, $i - 1, "discount");
                $summ           = $db->result($r1, $i - 1, "summ");
                $status_action  = $db->result($r1, $i - 1, "status_action");
                $art_nr_ds      = $this->getArticleDispl($art_id);
                $brand_name     = $this->getBrandName($brand_id);
                $action_cap     = ($status_action > 0) ? "<i class='fa fa-box-open' title='Акційна ціна'></i>" : "";
                $list .= "
                <tr>
                    <td>$i</td>
                    <td>$art_id</td>
                    <td>$art_nr_ds</td>
                    <td>$brand_name</td>
                    <td>$suppl_name</td>
                    <td>$amount</td>
                    <td>$price $action_cap</td>
                    <td>$discount</td>
                    <td>$summ</td>
                </tr>";
            }
            $form = str_replace("{order_str_list}", $list, $form);
        }

        return array($form, "Замовлення з сайту № $order_id від " . $slave->data_word($data));
    }

    function getCityName($city_id)
    {
        $dbt = DbSingleton::getTokoDb();
        $city_id = (int)$city_id;
        $r = $dbt->query("SELECT `CITY_NAME` FROM `T2_CITY` WHERE `CITY_ID` = '$city_id' LIMIT 1;");
        return $dbt->result($r, 0, "CITY_NAME");
    }

    function getDeliveryName($delivery_id)
    {
        $db = DbSingleton::getTokoDb();
        $delivery_id = (int)$delivery_id;
        $r = $db->query("SELECT `DESCRIPTION` FROM `T2_DELIVERY` WHERE `ID`='$delivery_id' LIMIT 1;");
        return $db->result($r, 0, "DESCRIPTION");
    }

    function getPaymentName($payment_id)
    {
        $dbt = DbSingleton::getTokoDb();
        $payment_id = (int)$payment_id;
        $r = $dbt->query("SELECT `DESCRIPTION` FROM `T2_PAYMENT` WHERE `ID` = $payment_id LIMIT 1;");
        return $dbt->result($r, 0, "DESCRIPTION");
    }

    function getDeliveryChargeName($delivery_charge_id)
    {
        $db = DbSingleton::getDb();
        $delivery_charge_id = (int)$delivery_charge_id;
        $r = $db->query("SELECT `mcaption` FROM `manual` WHERE `id` = $delivery_charge_id LIMIT 1;");
        $n = $db->num_rows($r);
        return ($n > 0) ? $db->result($r, 0, "mcaption") : "Не вибрано";
    }

    function getExpressInfoName($express)
    {
        $db = DbSingleton::getTokoDb();
        $express = (int)$express;
        $r = $db->query("SELECT `DESCRIPTION` FROM `T2_DELIVERY_EXPRESS` WHERE `ID` = $express LIMIT 1;");
        return $db->result($r, 0, "DESCRIPTION");
    }

    function getClientUserInfo($user_id)
    {
        $db = DbSingleton::getDb();
        $user_info = "";
        $r = $db->query("SELECT `name`, `phone` FROM `A_CLIENTS_USERS` WHERE `id` = '$user_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n > 0) {
            $name = $db->result($r, 0, "name");
            $phone = $db->result($r, 0, "phone");
            $user_info = "$name, тел. $phone";
        }
        return $user_info;
    }

    function getDpExpressPayment($dp_id)
    {
        $db = DbSingleton::getDb();
        $express_payment = 0;
        $dp_id = (int)$dp_id;
        $r = $db->query("SELECT * FROM `J_DP` WHERE `id` = $dp_id LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n > 0) {
            $order_info_id = $db->result($r, 0, "order_info_id") + 0;
            $clientData = $this->getOrdersClientInfoData($order_info_id);
            if (!empty($clientData)) {
                $express_payment    = $clientData["DEL_EXPRESS_PAYMENT"];
            }
        }

        return $express_payment;
    }

    function getDpUserDeliveryData($dp_id)
    {
        $client = new clients();
        $form = "<label class=\"control-label\">Доставка не вказана</label>";
        $db = DbSingleton::getDb();
        $r = $db->query("SELECT * FROM `J_DP` WHERE `id` = $dp_id LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n > 0) {
            $order_info_id = $db->result($r, 0, "order_info_id") + 0;
            $clientData = $this->getOrdersClientInfoData($order_info_id);
            if (!empty($clientData)) {
                $user_id            = $clientData["USER_ID"]; $user_name = $client->getUserNameById($user_id);
                $del_name           = ($clientData["DEL_NAME"] !== "") ? $clientData["DEL_NAME"] : $client->getUserNameById($clientData["USER_ID"]);
                $del_phone          = ($clientData["DEL_PHONE"] != 0) ? $clientData["DEL_PHONE"] : $client->getUserNameById($clientData["USER_ID"], "phone");
                $city_id            = $clientData["CITY_ID"]; $city_name = $this->getCityName($city_id);
                $delivery_id        = (int)$clientData["DELIVERY_ID"]; $delivery_name = $this->getDeliveryName($delivery_id);
                $payment_id         = (int)$clientData["PAYMENT_ID"]; $payment_name = $this->getPaymentName($payment_id);
                $delivery_charge_id = (int)$clientData["DELIVERY_CHARGE_ID"]; $delivery_charge_name = $this->getDeliveryChargeName($delivery_charge_id);
                $department         = $clientData["DEL_DEPARTMENT_TEXT"];
                $express            = (int)$clientData["DEL_EXPRESS"]; $express_text = $this->getExpressInfoName($express);
                $express_info       = $clientData["DEL_EXPRESS_INFO"];
                $express_payment    = $clientData["DEL_EXPRESS_PAYMENT"];
                $street             = $clientData["DEL_STREET"];
                $house              = $clientData["DEL_HOUSE"];
                $porch              = $clientData["DEL_PORCH"];

                $form = "<ul class='form-delivery'>";

                if ($user_id > 0) {
                    $form .= "<li>
                        <label class=\"control-label\" for=\"\">Веб-користувач:</label>
                        <input class=\"form-control\" disabled type=\"text\" value=\"$user_name\">
                    </li>";
                }
                if ($del_name !== "") {
                    $form .= "<li>
                        <label class=\"control-label\" for=\"\">Отримувач:</label>
                        <input class=\"form-control\" disabled type=\"text\" value=\"$del_name\">
                    </li>";
                }
                if ($del_phone !== "") {
                    $form .= "<li>
                        <label class=\"control-label\" for=\"\">Телефон (отримувача):</label>
                        <input class=\"form-control\" disabled type=\"text\" value=\"$del_phone\">
                    </li>";
                }
                if ($city_id > 0) {
                    $form .= "<li>
                        <label class=\"control-label\" for=\"\">Місто:</label>
                        <input class=\"form-control\" disabled type=\"text\" value=\"$city_name\">
                    </li>";
                }
                if ($delivery_id > 0) {
                    $form .= "<li>
                        <label class=\"control-label\" for=\"\">Тип доставки:</label>
                        <input class=\"form-control\" disabled type=\"text\" value=\"$delivery_name\">
                    </li>";
                }
                if ($payment_id > 0) {
                    $form .= "<li>
                        <label class=\"control-label\" for=\"\">Тип оплати:</label>
                        <input class=\"form-control\" disabled type=\"text\" value=\"$payment_name\">
                    </li>";
                }
                if ($delivery_charge_id > 0) {
                    $form .= "<li>
                        <label class=\"control-label\" for=\"\">Плата за доставку:</label>
                        <input class=\"form-control\" disabled type=\"text\" value=\"$delivery_charge_name\">
                    </li>";
                }
                if ($house !== "") {
                    $address = "вул. $street";
                    if ($house !== "" && $house != "0") {
                        $address .= ", буд. $house";
                    }
                    if ($porch !== "" && $porch != "0") {
                        $address .= ", п. $porch";
                    }
                    $form .= "<li>
                        <label class=\"control-label\" for=\"\">Адреса:</label>
                        <input class=\"form-control\" disabled type=\"text\" value=\"$address\">
                    </li>";
                }
                if ($department !== "" && $department != "0") {
                    $form .= "<li>
                        <label class=\"control-label\" for=\"\">Відділення:</label>
                        <input class=\"form-control\" disabled type=\"text\" value=\"$department\">
                    </li>";
                }
                if ($express_text !== "") {
                    $form .= "<li>
                        <label class=\"control-label\" for=\"\">Тип експрес доставки:</label>
                        <input class=\"form-control\" disabled type=\"text\" value=\"$express_text\">
                    </li>";
                }
                if ($express_info !== "") {
                    $form .= "<li>
                        <label class=\"control-label\" for=\"\">Інформація експрес доставки:</label>
                        <input class=\"form-control\" disabled type=\"text\" value=\"$express_info\">
                    </li>";
                }
                if ($express_payment !== "") {
                    $form .= "<li>
                        <label class=\"control-label\" for=\"\">Оплата сума:</label>
                        <input class=\"form-control\" disabled type=\"text\" value=\"$express_payment\">
                    </li>";
                }

                $form .= "</ul>";
            }
        }
        return $form;
    }

    function getDpUserDelivery($dp_id)
    {
        $db = DbSingleton::getDb(); $dbt = DbSingleton::getTokoDb();
        $text = "";
        $r = $db->query("SELECT `order_info_id` FROM `J_DP` WHERE `id` = $dp_id LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n > 0) {
            $order_info_id  = $db->result($r, 0, "order_info_id") + 0;
            $clientData     = $this->getOrdersClientInfoData($order_info_id);

            if (!empty($clientData)) {
                $user_id        = $clientData["USER_ID"];
                $city_id        = $clientData["CITY_ID"];
                $payment_id     = (int)$clientData["PAYMENT_ID"];
                $delivery_id    = (int)$clientData["DELIVERY_ID"];
                $street         = $clientData["DEL_STREET"];
                $house          = $clientData["DEL_HOUSE"];
                $porch          = $clientData["DEL_PORCH"];
                $department     = $clientData["DEL_DEPARTMENT_TEXT"];
                $express        = (int)$clientData["DEL_EXPRESS"];
                $express_info   = $clientData["DEL_EXPRESS_INFO"];
                $delivery_info  = "";

                if ($delivery_id > 0) {
                    $r2 = $dbt->query("SELECT `DESCRIPTION` FROM `T2_DELIVERY` WHERE `ID` = $delivery_id LIMIT 1;");
                    $delivery_info = $dbt->result($r2, 0, "DESCRIPTION");
                }
                // kurer
                if ($delivery_id === 2) {
                    $delivery_info .= " - вул. $street, буд. $house, п. $porch";
                }
                // kurer STO
                if ($delivery_id === 3) {
                    $delivery_info .= " - вул. $street, буд. $house";
                }
                // NP
                if ($delivery_id === 4) {
                    $delivery_info .= " - $department";
                }
                // NP kurer
                if ($delivery_id === 5) {
                    $delivery_info .= " - вул. $street, буд. $house, п. $porch";
                }
                // UP
                if ($delivery_id === 6) {
                    $delivery_info .= " - $department";
                }
                // Express
                if ($delivery_id === 7) {
                    $express_name = $this->getExpressInfoName($express);
                    $delivery_info .= " - $express_name: $express_info";
                }

                $user_info      = $this->getClientUserInfo($user_id);
                $city_name      = $this->getCityName($city_id);
                $payment_name   = $this->getPaymentName($payment_id);

                $text = "клієнт: $user_info";
                if ($city_name !== "" && $delivery_info !== "") {
                    $text .= ", доставка: $city_name $delivery_info";
                }
                if ($payment_name !== "") {
                    $text .= ", оплата: $payment_name";
                }
            }
        }

        return $text;
    }

    function getOrderInfoForm($form, $order_info_id)
    {
        $client = new clients;
        $clientData = $this->getOrdersClientInfoData($order_info_id);

        $form = str_replace("{order_user}", $client->getUserNameById($clientData["USER_ID"]), $form);
        $form = str_replace("{order_del_name}", ($clientData["DEL_NAME"] != "") ? $clientData["DEL_NAME"] : $client->getUserNameById($clientData["USER_ID"]), $form);
        $form = str_replace("{order_del_phone}", ($clientData["DEL_PHONE"] != "" && $clientData["DEL_PHONE"] != 0) ? $clientData["DEL_PHONE"] : $client->getUserNameById($clientData["USER_ID"], "phone"), $form);

        $form = str_replace("{order_city}", $client->getCityName($clientData["CITY_ID"]), $form);
        $form = str_replace("{order_delivery}", $this->getDeliveryName($clientData["DELIVERY_ID"]), $form);
        $form = str_replace("{order_payment}", $this->getPaymentName($clientData["PAYMENT_ID"]), $form);

        $form = str_replace("{order_del_street}", $clientData["DEL_STREET"], $form);
        $form = str_replace("{order_del_house}", $clientData["DEL_HOUSE"], $form);
        $form = str_replace("{order_del_porch}", $clientData["DEL_PORCH"], $form);

        $form = str_replace("{order_del_department}", $clientData["DEL_DEPARTMENT_TEXT"], $form);
        $form = str_replace("{order_del_express}", $client->getExpressText($clientData["DEL_EXPRESS"]), $form);
        $form = str_replace("{order_del_express_info}", $clientData["DEL_EXPRESS_INFO"], $form);
        $form = str_replace("{order_del_express_payment}", $clientData["DEL_EXPRESS_PAYMENT"], $form);

        return $form;
    }

    function getOrderDeliveryInfo($order_info_id)
    {
        $db = DbSingleton::getDb();
        $dbt = DbSingleton::getTokoDb();
        $text = "";
        $r = $db->query("SELECT `DELIVERY_ID`, `DEL_EXPRESS`, `DEL_EXPRESS_INFO` FROM `ORDERS_CLIENT_INFO` WHERE `ID` = $order_info_id LIMIT 1;");
        $delivery_id = (int)$db->result($r, 0, "DELIVERY_ID");

        if ($delivery_id > 0) {
            $r2 = $dbt->query("SELECT `DESCRIPTION_MIN` FROM `T2_DELIVERY` WHERE `ID` = $delivery_id LIMIT 1;");
            $text = $dbt->result($r2, 0, "DESCRIPTION_MIN");
        }

        if ($delivery_id === 7) {
            $express        = $db->result($r, 0, "DEL_EXPRESS") + 0;
            $express_name   = $this->getExpressInfoName($express);
            $express_info   = $db->result($r, 0, "DEL_EXPRESS_INFO");
            $text           .= " - $express_name: $express_info";
        }

        return $text;
    }

    function getOrdersClientInfoData($order_info_id)
    {
        $db = DbSingleton::getDb();
        $r = $db->query("SELECT * FROM `ORDERS_CLIENT_INFO` WHERE `ID` = $order_info_id LIMIT 1;");
        return mysqli_fetch_assoc($r);
    }

    function getOrderInfoBlock($form, $order_info_id, $client_conto_id)
    {
        $client = new clients;
        $clientData = $this->getOrdersClientInfoData($order_info_id);

        $client_id = $clientData["CLIENT_ID"];
        if (empty($clientData)) {
            $client_id = $client_conto_id;
        }

        $form = str_replace("{order_info_id}", $order_info_id, $form);
        $form = str_replace("{order_user_list}", $client->getUserList($client_id, $clientData["USER_ID"]), $form);
        $form = str_replace("{order_city_list}", $client->getCityList($clientData["CITY_ID"]), $form);
        $form = str_replace("{order_delivery_list}", $client->getDeliveryList($clientData["DELIVERY_ID"]), $form);
        $form = str_replace("{order_payment_list}", $client->getPaymentList($clientData["PAYMENT_ID"]), $form);
        $form = str_replace("{delivery_charge_list}", $client->getDeliveryChargeList($clientData["DELIVERY_CHARGE_ID"]), $form);

        $form = str_replace("{order_del_street}", $clientData["DEL_STREET"], $form);
        $form = str_replace("{order_del_house}", $clientData["DEL_HOUSE"], $form);
        $form = str_replace("{order_del_porch}", $clientData["DEL_PORCH"], $form);

        $form = str_replace("{department_ref}", $clientData["DEL_DEPARTMENT"], $form);
        $form = str_replace("{order_del_express_info}", $clientData["DEL_EXPRESS_INFO"], $form);
        $form = str_replace("{order_del_express_payment}", $clientData["DEL_EXPRESS_PAYMENT"], $form);

        $form = str_replace("{order_del_department_list}", $client->getDepartmentList($clientData["DEL_DEPARTMENT"], $clientData["DEL_DEPARTMENT_TEXT"]), $form);
        $form = str_replace("{order_del_express_list}", $client->getExpressList($clientData["DEL_EXPRESS"]), $form);

        $form = str_replace("{order_del_name}", ($clientData["DEL_NAME"] != "") ? $clientData["DEL_NAME"] : $client->getUserNameById($clientData["USER_ID"]), $form);
        $form = str_replace("{order_del_phone}", ($clientData["DEL_PHONE"] != "" && $clientData["DEL_PHONE"] != 0) ? $clientData["DEL_PHONE"] : $client->getUserNameById($clientData["USER_ID"], "phone"), $form);

        return $form;
    }

    function saveDpOrderInfo($dp_id, $order_info_id, $client_id, $user_id, $city_id, $delivery_id, $payment_id, $delivery_charge_id, $del_name, $del_phone, $street, $house, $porch, $department, $department_text, $express, $express_info, $express_payment)
    {
        $db = DbSingleton::getDb();
        $r = $db->query("SELECT * FROM `ORDERS_CLIENT_INFO` WHERE `ID` = $order_info_id LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 0) {
            $r2 = $db->query("SELECT * FROM `ORDERS_CLIENT_INFO` WHERE `CLIENT_ID` = $client_id AND `USER_ID` = $user_id AND `DELIVERY_ID` = $delivery_id AND `PAYMENT_ID` = $payment_id AND `CITY_ID` = $city_id LIMIT 1;");
            $n2 = $db->num_rows($r2);
            if ($n2 == 0) {
                $r2 = $db->query("SELECT MAX(`ID`) as mid FROM `ORDERS_CLIENT_INFO`;");
                $max = 0 + $db->result($r2, 0, "mid") + 1;
                $db->query("INSERT INTO `ORDERS_CLIENT_INFO` (`ID`, `CLIENT_ID`, `USER_ID`, `CITY_ID`, `DELIVERY_ID`, `PAYMENT_ID`, `DELIVERY_CHARGE_ID`, `DEL_NAME`, `DEL_PHONE`, `DEL_STREET`, `DEL_HOUSE`, `DEL_PORCH`, `DEL_DEPARTMENT`, `DEL_DEPARTMENT_TEXT`, `DEL_EXPRESS`, `DEL_EXPRESS_INFO`, `DEL_EXPRESS_PAYMENT`)
                VALUES ($max, $client_id, $user_id, $city_id, $delivery_id, $payment_id, $delivery_charge_id, '$del_name', '$del_phone', '$street', '$house', '$porch', '$department', '$department_text', $express, '$express_info', '$express_payment');");
                $db->query("UPDATE `J_DP` SET `order_info_id` = $max WHERE `id` = $dp_id LIMIT 1;");
                $answer = "Додано нову інформацію про доставку!";
            } else {
                $answer = "Запис вже існує!";
            }
        } else {
            $db->query("UPDATE `ORDERS_CLIENT_INFO` SET `CITY_ID` = $city_id, `DELIVERY_ID` = $delivery_id, `PAYMENT_ID` = $payment_id, `DELIVERY_CHARGE_ID` = $delivery_charge_id, `DEL_NAME` = '$del_name', `DEL_PHONE` = '$del_phone', `DEL_STREET` = '$street', `DEL_HOUSE` = '$house', `DEL_PORCH` = '$porch', `DEL_DEPARTMENT` = '$department', `DEL_DEPARTMENT_TEXT` = '$department_text', `DEL_EXPRESS` = $express, `DEL_EXPRESS_INFO` = '$express_info', `DEL_EXPRESS_PAYMENT` = '$express_payment' WHERE `ID` = $order_info_id LIMIT 1;");
            $answer = "Оновлено інформацію про доставку!";
        }
        return $answer;
    }

    function getCityVal($search_text)
    {
        $db = DbSingleton::getTokoDb();
        $lang_id = 1;
        $mas = [];
        $postfix = "";
        if ($lang_id == 1 || $lang_id == 3) {
            $postfix = "_RU";
        }
        //$r = $db->query("SELECT * FROM `T2_LOCATION` WHERE `CITY_NAME_CLEAR$postfix` LIKE \"$search_text%\" ORDER BY `CITY_NAME$postfix`;");

        $r = $db->query("SELECT * FROM `T2_LOCATION` 
        WHERE `CITY_NAME_CLEAR` LIKE \"$search_text%\" OR `CITY_NAME_CLEAR_RU` LIKE \"$search_text%\" ORDER BY `CITY_NAME$postfix`;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $city_id        = $db->result($r, $i - 1, "CITY_ID");
            $city_name      = $db->result($r, $i - 1, "CITY_NAME");
            $city_name_ru   = $db->result($r, $i - 1, "CITY_NAME_RU");
            $region_name    = $db->result($r, $i - 1, "REGION_NAME");
            $region_name_ru = $db->result($r, $i - 1, "REGION_NAME_RU");
            $state_name     = $db->result($r, $i - 1, "STATE_NAME");
            $state_name_ru  = $db->result($r, $i - 1, "STATE_NAME_RU");
            $value_foo      = "$city_name ($state_name обл., $region_name р-он) - $city_name_ru ($state_name_ru обл., $region_name_ru р-он)";
            $city_cap       = "$city_name ($state_name обл., $region_name р-он)";

            if ($lang_id === 1 || $lang_id === 3) {
                $city_cap = "$city_name_ru ($state_name_ru обл., $region_name_ru р-он)";
            }

            $mas[$i]        = ["id" => $city_id, "value" => $value_foo, "data-foo" => $city_cap];
        }

        return $mas;
    }

    function setCityNPVal($city_id)
    {
        $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT `CITY_NAME_CLEAR`, `NEWPOST_AREA` FROM `T2_LOCATION` WHERE `CITY_ID` = $city_id LIMIT 1;");
        $city_name  = $db->result($r, 0, "CITY_NAME_CLEAR");
        $state_name = $db->result($r, 0, "NEWPOST_AREA");

        $list = "";
        //$r = $db->query('SELECT `CITY_REF`, `CITY_NAME`, `AREA_NAME` FROM `T2_CITY_NOVA` WHERE `CITY_NAME` LIKE "' . $city_name . '%" AND `AREA_NAME` LIKE "' . $state_name . '%";');
        $r = $db->query("SELECT `CITY_REF`, `CITY_NAME`, `AREA_NAME` FROM `T2_CITY_NOVA` WHERE `CITY_NAME` LIKE \"$city_name%\" AND `AREA_NAME` LIKE \"$state_name%\";");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $ref        = $db->result($r, $i - 1, "CITY_REF");
            $name       = $db->result($r, $i - 1, "CITY_NAME");
            $area_name  = $db->result($r, $i - 1, "AREA_NAME");
            $list       .= "
            <option value='$ref'>$name ($area_name)</option>";
        }

        return $list;
    }

    function setCityDepartments($city_ref, $department_ref)
    {
        $list_up = "<option value='0'>{not_chosen}</option>";
        $list_np = $this->getNovaPoshtaWarehousesSelect($city_ref, $department_ref);
        return array($list_np, $list_up);
    }

    function getNovaPoshtaWarehousesSelect($ref, $department_ref)
    {
        $list = "<option value=\"0\">-Не вибрано-</option>";
        $np = new NovaPoshtaApi2('e52c020f392e0da179684b87cdbbbf05');
        $arr = $np->getWarehouses($ref)['data'];
        foreach ($arr as $val) {
            $name = iconv("UTF-8", "windows-1251", $val["Description"]);
            $war_ref = $val["Ref"];
            if ($war_ref == $department_ref) {
                $sel = "selected";
            } else {
                $sel = "";
            }
            $list .= "<option value='$war_ref' $sel>$name</option>";
        }

        return $list;
    }

    function deleteOrderSite($order_id)
    {
        $db = DbSingleton::getDb();
        $answer = 0; $err = "Помилка видалення!";
        if ($order_id > 0) {
            $db->query("UPDATE `orders_new` SET `status` = 0 WHERE `id` = $order_id;");
            $answer = 1; $err = "";
        }

        return array($answer, $err);
    }

    /*
     * створити ДП з замовлення сайту
     * */
    function createDpFromOrder($order_id)
    {
        $db = DbSingleton::getDb();
        $slave = new slave;
        $order_id = $slave->qq($order_id);
        $answer = 0; $err = "Помилка замовлення!";
        $dp_id = 0;
        if ($order_id > 0) {
            $r = $db->query("SELECT o.*, t.name as tpoint_name, c.name as client_name, cr.name as retail_name, cit.CITY_NAME as region_name, csh.abr as cash_abr, dt.mcaption as delivery_type_caption
            FROM `orders_new` o
                LEFT JOIN `T_POINT` t ON (t.id = o.tpoint_id)
                LEFT JOIN `A_CLIENTS` c ON (c.id = o.client_id)
                LEFT JOIN `A_CLIENTS_USERS_RETAIL` cr ON (cr.id = o.client_user_id)
                LEFT JOIN `T2_CITY` cit ON (cit.CITY_ID = o.region)
                LEFT JOIN `CASH` csh ON (csh.id = o.cash_id)
                LEFT JOIN `manual` dt ON (dt.id = o.delivery AND dt.`key` = 'delivery_type')
            WHERE o.status = 1 AND o.id = $order_id LIMIT 1;");
            $n = $db->num_rows($r);
            if ($n == 0) {
                $err = "Замовлення було видалено!";
            }
            if ($n == 1) {
                $client_id      = $db->result($r, 0, "client_id");
                $tpoint_id      = $db->result($r, 0, "tpoint_id");
                $cash_id        = $db->result($r, 0, "cash_id");
                $delivery       = $db->result($r, 0, "delivery");
                $delivery_info  = $db->result($r, 0, "delivery_info");
                $address        = $db->result($r, 0, "address");
                $payment_info   = $db->result($r, 0, "payment_info");
                $name           = $db->result($r, 0, "name");
                $phone          = $db->result($r, 0, "phone");
                $comment        = $db->result($r, 0, "comment");
                $price_summ     = $db->result($r, 0, "price_summ");
                $carrier_id     = $db->result($r, 0, "carrier_id");
                $order_info_id  = $db->result($r, 0, "order_info_id");
                $del_note       = "$comment - $name, tel. $phone, $delivery_info, $address, $payment_info";
                $dp_id          = $this->newDpCard();
                if ($dp_id > 0) {
                    [, , $doc_type] = $this->getDpClientDocType($client_id);
                    $db->query("UPDATE `orders_new` SET `dp_id` = $dp_id WHERE `id` = $order_id;");
                    $db->query("UPDATE `J_DP` SET `order_info_id` = $order_info_id, `tpoint_id` = '$tpoint_id', `client_id` = '$client_id', `client_conto_id` = '$client_id', `cash_id` = '$cash_id', `summ` = '$price_summ', `delivery_type_id` = '$delivery', `carrier_id` = '$carrier_id', `doc_type_id` = '$doc_type', `delivery_address` = '$del_note' WHERE `id` = $dp_id LIMIT 1;");
                    $dp_note = $this->getDpNote($dp_id);
                    if ($delivery_info !== "" && $dp_note === "") {
                        $this->setDpNote($dp_id, $del_note);
                    }

                    $r1 = $db->query("SELECT `id`, `suppl_id`, `art_id`, `brand_id`, `amount`, `storage_id`, `status_action`, `price`, `discount` FROM `orders_str_new` WHERE `order_id` = $order_id;");
                    $n1 = $db->num_rows($r1);
                    $ans_err = 0;
                    for ($i = 1; $i <= $n1; $i++) {
                        $str_id         = $db->result($r1, $i - 1, "id") + 0;
                        $suppl_id       = $db->result($r1, $i - 1, "suppl_id") + 0;
                        $art_id         = $db->result($r1, $i - 1, "art_id");
                        $brand_id       = $db->result($r1, $i - 1, "brand_id");
                        $amount         = $db->result($r1, $i - 1, "amount");
                        $storage_id     = $db->result($r1, $i - 1, "storage_id");
                        $status_action  = $db->result($r1, $i - 1, "status_action");
                        $order_price    = $db->result($r1, $i - 1, "price");
                        $discount       = $db->result($r1, $i - 1, "discount");
                        $art_nr_ds      = $this->getArticleDispl($art_id);

                        [$usd_to_uah,] = $this->getKoursData();
                        $order_price = $this->getClientPriceRounding($client_id, $order_price);
                        $order_price = round($order_price / $usd_to_uah, 2); // UAH to USD

                        $dp_str_id = 0; $ans = 0; $er = "";

                        if ($suppl_id == 0) {
                            [$ans, $er, $dp_str_id] = $this->setArticleToDp($dp_id, $tpoint_id, $art_id, $art_nr_ds, $brand_id, $storage_id, $amount, $status_action, $order_price, $discount, 0);
                        }

                        if ($suppl_id > 0) {
                            [$ans, $er, $dp_str_id] = $this->setArticleSupplToDp($dp_id, $art_id, $art_nr_ds, $brand_id, $suppl_id, $storage_id, $amount, $status_action, $order_price, $discount, 0);
                        }

                        if ($dp_str_id > 0 && $ans == 1) {
                            $db->query("UPDATE `orders_str_new` SET `dp_str_id` = $dp_str_id WHERE `id` = $str_id;");
                        }

                        if ($ans == 0 || $ans === "") {
                            $ans_err++;
                        }
                        $err .= "\n" . $er;
                    }
                    $answer = 1; //$err = "";
                    if ($ans_err == $n1) {
                        $answer = 0;
                    }
                }
            }
        }

        return array($answer, $err, $dp_id);
    }

    function getArticlePriceRatingCash($art_id) { $db = DbSingleton::getTokoDb();
        $cash_id = 2;
        $r = $db->query("SELECT `cash_id` FROM `T2_ARTICLES_PRICE_RATING` WHERE `art_id` = $art_id AND `in_use` = 1 LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n > 0) {
            $cash_id = $db->result($r, 0, "cash_id");
        }
        if (empty($cash_id)) {
            $db->query("UPDATE `T2_ARTICLES_PRICE_RATING` SET `cash_id` = 2 WHERE `art_id` = $art_id AND `in_use` = 1 LIMIT 1;");
            $cash_id = 2;
        }
        return $cash_id;
    }

    function getClientPriceRounding($client_id, $price) { $db = DbSingleton::getDb();
        if ($client_id > 0) {
            $r = $db->query("SELECT `rounding_price` FROM `A_CLIENTS` WHERE `id` = $client_id;");
            $n = $db->num_rows($r);
            if ($n > 0) {
                $rounding_price = $db->result($r, 0, "rounding_price");
                if ($rounding_price == 0) {
                    $price = round($price, 2);
                }
                if ($rounding_price == 1) {
                    $price = round($price * 100, -1) / 100;
                }
                if ($rounding_price == 2) {
                    $price = round($price);
                }
            }
        }
        return $price;
    }

    function getDpArticleAmount($str_id, $art_id) { $db = DbSingleton::getDb();
        $amount = 0;
        $r = $db->query("SELECT `amount` FROM `J_DP_STR` WHERE `id` = $str_id AND `art_id` = $art_id;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $amount = $db->result($r, 0, "amount");
        }
        return $amount;
    }

    function loadDpSiteOrder($dp_id) { $db = DbSingleton::getDb();
        $slave = new slave; $gmanual = new gmanual;
        $dp_id = $slave->qq($dp_id);
        $form = ""; $list = "";
        if ($dp_id == 0) {
            $form = "<h2 align='center'>Замовлення відсутнє або ще не передано в роботу</h2>";
        }
        if ($dp_id > 0) {
            $form_htm = RD . "/tpl/dp_orders_site_view.htm";
            if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
            $r = $db->query("SELECT o.*, t.name as tpoint_name, c.name as client_name, cr.name as retail_name, cit.CITY_NAME as region_name, csh.abr as cash_abr, dt.mcaption as delivery_type_caption
            FROM `orders_new` o
                LEFT OUTER JOIN `T_POINT` t ON (t.id = o.tpoint_id)
                LEFT OUTER JOIN `A_CLIENTS` c ON (c.id = o.client_id)
                LEFT OUTER JOIN `A_CLIENTS_USERS_RETAIL` cr ON (cr.id = o.client_user_id)
                LEFT OUTER JOIN `T2_CITY` cit ON (cit.CITY_ID = o.region)
                LEFT OUTER JOIN `CASH` csh ON (csh.id = o.cash_id)
                LEFT OUTER JOIN `manual` dt ON (dt.id = o.delivery AND dt.`key` = 'delivery_type')
            WHERE o.status = 1 AND o.dp_id = $dp_id LIMIT 1;");
            $n = $db->num_rows($r);
            if ($n == 0) {
                $form = "<h2 align='center'>Замовлення відсутнє або ще не передано в роботу</h2>";
            }
            if ($n == 1) {
                $order_id       = $db->result($r, 0, "id");
                $data           = $db->result($r, 0, "data");
                $client_name    = $db->result($r, 0, "client_name");
                $retail_name    = $db->result($r, 0, "retail_name");
                $tpoint_id      = $db->result($r, 0, "tpoint_id");
                $cash_abr       = $db->result($r, 0, "cash_abr");
                $name           = $db->result($r, 0, "name");
                $delivery_type  = $db->result($r, 0, "delivery_type_caption");
                $delivery_info  = $db->result($r, 0, "delivery_info");
                $price_summ     = $db->result($r, 0, "price_summ");
                $payment        = $db->result($r, 0, "payment");
                $payment_info   = $db->result($r, 0, "payment_info");
                $address        = $db->result($r, 0, "address");

                $form = str_replace("{data}", $data, $form);
                $form = str_replace("{order_id}", $order_id, $form);
                $form = str_replace("{name}", $name, $form);
                $form = str_replace("{cash_abr}", $cash_abr, $form);
                $form = str_replace("{summ}", $price_summ, $form);
                $form = str_replace("{retail_name}", $retail_name, $form);
                $form = str_replace("{client_name}", $client_name, $form);
                $form = str_replace("{tpoint_name}", $this->getTpointName($tpoint_id), $form);
                $form = str_replace("{delivery}", $delivery_type, $form);
                $form = str_replace("{delivery_info}", $delivery_info, $form);
                $form = str_replace("{payment}", $gmanual->get_gmanual_caption($payment), $form);
                $form = str_replace("{payment_info}", $payment_info, $form);
                $form = str_replace("{address}", $address, $form);

                $r1 = $db->query("SELECT * FROM `orders_str_new` WHERE `order_id` = $order_id;");
                $n1 = $db->num_rows($r1);
                for ($i = 1; $i <= $n1; $i++) {
                    $dp_str_id          = $db->result($r1, $i - 1, "dp_str_id");
                    $suppl_id           = $db->result($r1, $i - 1, "suppl_id");
                    $art_id             = $db->result($r1, $i - 1, "art_id");
                    $brand_id           = $db->result($r1, $i - 1, "brand_id");
                    $amount             = $db->result($r1, $i - 1, "amount");
                    $price              = $db->result($r1, $i - 1, "price");
                    $summ               = $db->result($r1, $i - 1, "summ");
                    $suppl_name         = $this->getClientName($suppl_id);
                    $article_nr_displ   = $this->getArticleDispl($art_id);
                    $brand_name         = $this->getBrandName($brand_id);
                    $dp_str_amount      = $this->getDpArticleAmount($dp_str_id, $art_id);
                    $color              = ($dp_str_amount != $amount) ? " style='background-color:#ffe000;'" : "";
                    $list .= "
                    <tr $color>
                        <td>$i</td>
                        <td>$suppl_name</td>
                        <td>$art_id</td>
                        <td>$article_nr_displ</td>
                        <td>$brand_name</td>
                        <td>$dp_str_amount</td>
                        <td>$amount</td>
                        <td>$price</td>
                        <td>$summ</td>
                    </tr>";
                }
                $form = str_replace("{order_str_list}", $list, $form);
            }
        }

        return $form;
    }

    function getDistinctOrdersItems($dp_id) { $db = DbSingleton::getDb();
        $slave = new slave;
        $amount = "";
        $dp_id = $slave->qq($dp_id);
        if ($dp_id > 0) {
            $r1 = $db->query("SELECT * FROM (
                SELECT os.amount, IFNULL( dps.amount, 0 ) AS dp_amount
                FROM `orders_str_new` os
                    LEFT OUTER JOIN `orders_new` o ON (o.id = os.order_id)
                    LEFT OUTER JOIN `J_DP_STR` dps ON (dps.id = os.dp_str_id)
                WHERE o.dp_id = $dp_id
                ) AS res
            WHERE `amount` <> `dp_amount`;");
            $n1 = $db->num_rows($r1);
            if ($n1 > 0) {
                $amount = "<span class='label label-tab label-warning'>$n1</span>";
            }
        }

        return $amount;
    }

    /*
     * ORDERS FROM SITE
     * */
    function showSupplToLocalChangeForm($art_id, $dp_id, $dp_str_id) { $db = DbSingleton::getDb(); $dbt = DbSingleton::getTokoDb();
        $slave = new slave;
        $art_id = $slave->qq($art_id);
        $dp_id = $slave->qq($dp_id);
        $form = ""; $form_htm = RD. "/tpl/dp_suppl_to_local_change_form.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }

        if ($dp_id > 0 && $art_id > 0) {
            $r1 = $db->query("SELECT `suppl_id`, `brand_id`, `amount`, `price` FROM `orders_str_new` WHERE `dp_str_id` = $dp_str_id AND `art_id` = $art_id;");
            $n1 = $db->num_rows($r1);
            if ($n1 == 0) {
                $r1 = $db->query("SELECT `suppl_id`, `brand_id`, `amount`, `price` FROM `J_DP_STR` WHERE `id` = $dp_str_id AND `art_id` = $art_id;");
                $n1 = $db->num_rows($r1);
            }
            if ($n1 > 0) {
                $suppl_id   = $db->result($r1, 0, "suppl_id");
                $brand_id   = $db->result($r1, 0, "brand_id");
                $amount     = $db->result($r1, 0, "amount");
                $price      = $db->result($r1, 0, "price");
                $form = str_replace("{dp_id}", $dp_id, $form);
                $form = str_replace("{dp_str_id}", $dp_str_id, $form);
                $form = str_replace("{suppl_id}", $suppl_id, $form);
                $form = str_replace("{suppl_name}", $this->getClientName($suppl_id), $form);
                $form = str_replace("{article_nr_displ}", $this->getArticleDispl($art_id), $form);
                $form = str_replace("{art_id}", $art_id, $form);
                $form = str_replace("{brand_name}", $this->getBrandName($brand_id), $form);
                $form = str_replace("{amount}", $amount, $form);
                $form = str_replace("{amount_order}", $this->getDpArticleAmount($dp_str_id, $art_id), $form);

                $stock_list = "";
                $r = $dbt->query("SELECT t2sc.AMOUNT, t2sc.RESERV_AMOUNT, sc.id as cell_id, sc.cell_value, s.id as storage_id, s.name as storage_name 
                FROM `T2_ARTICLES_STRORAGE_CELLS` t2sc 
                    LEFT OUTER JOIN `STORAGE_CELLS` sc ON (sc.id = t2sc.STORAGE_CELLS_ID) 
                    LEFT OUTER JOIN `STORAGE` s ON (s.ID = sc.STORAGE_ID)
                WHERE t2sc.ART_ID = $art_id AND (t2sc.AMOUNT > 0 OR t2sc.RESERV_AMOUNT > 0) 
                ORDER BY sc.cell_value ASC;");
                $n = $dbt->num_rows($r);
                for ($i = 1; $i <= $n; $i++) {
                    $amount         = $dbt->result($r, $i - 1, "AMOUNT");
                    $reserv_amount  = $dbt->result($r, $i - 1, "RESERV_AMOUNT");
                    $cell_id        = $dbt->result($r, $i - 1, "cell_id");
                    $cell_value     = $dbt->result($r, $i - 1, "cell_value");
                    $storage_id     = $dbt->result($r, $i - 1, "storage_id");
                    $storage_name   = $dbt->result($r, $i - 1, "storage_name");
                    $stock_list .= "
                    <tr>
                        <td>$i</td>
                        <td>$storage_name</td>
                        <td>$cell_value</td>
                        <td>$amount</td>
                        <td>$reserv_amount</td>
                        <td><input type='text' class='form-input' data-storage='$storage_id' data-cell='$cell_id' id='stlca_$i' data-maxvalue='$amount' data-price='$price' value='0'></td>
                    </tr>";
                }

                $form = str_replace(array("{stock_list}", "{stock_n}"), array($stock_list, $n), $form);
            }
            if ($n1 == 0) {
                $form = "<h3 align='center'>Заказ клієнта не знайдено.</h3>";
            }
        }

        return array($form, "Відбір індексу з приходу від постачальника");
    }

    /*
     * кнопка ВІДІБРАТИ в передпродажу
     * */
    function saveDpSupplToLocalChangeForm($dp_id, $dp_str_id, $art_id, $amount, $price, $storage_id) { $db = DbSingleton::getDb(); $dbt = DbSingleton::getTokoDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка!";
        $rr_amount = $rr_reserv = 0;
        $dp_id      = $slave->qq($dp_id);
        $dp_str_id  = $slave->qq($dp_str_id);
        $art_id     = $slave->qq($art_id);
        $amount     = $slave->qq($amount);
        $price      = $slave->qq($price);
        $storage_id = $slave->qq($storage_id);

        if ($art_id > 0 && $amount > 0 && $dp_str_id > 0 && $storage_id > 0) {
            // check if article still exist in cell
            $r1 = $dbt->query("SELECT `AMOUNT`, `RESERV_AMOUNT` FROM `T2_ARTICLES_STRORAGE` WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id AND `AMOUNT` >= $amount LIMIT 1;");
            $n1 = $dbt->num_rows($r1);
            $stor_ex = 0;
            // print "n1=$n1\n;";
            if ($n1 == 1) {
                $rr_amount = $dbt->result($r1, 0, "AMOUNT");
                $rr_reserv = $dbt->result($r1, 0, "RESERV_AMOUNT");
                $stor_ex = 1;
            }
            if ($stor_ex == 1) {
                $tpoint_id = $this->getDpTpoint($dp_id);
                $reserv_type_id = $this->getArticleReservType($tpoint_id, $storage_id);
                $summ = round($price * $amount, 2);
                $db->query("UPDATE `J_DP_STR` SET `amount` = '$amount', `summ` = '$summ', `storage_id_from` = '$storage_id', `location_storage_id` = '$storage_id', `reserv_type_id` = '$reserv_type_id', `status_dps` = 93 WHERE `id` = $dp_str_id AND `dp_id` = $dp_id;");
                $this->updateDpWeightVolume($dp_id);
                $this->updateDpSumm($dp_id);
                $rr_amount = $rr_amount - $amount;
                $rr_reserv = $rr_reserv + $amount;
                $dbt->query("UPDATE `T2_ARTICLES_STRORAGE` SET `AMOUNT` = '$rr_amount', `RESERV_AMOUNT` = '$rr_reserv' WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id;");
                $db->query("UPDATE `J_DP` SET `status_dp` = 79 WHERE `id` = $dp_id;");
                $answer = 1; $err = "";
            }
        }

        return array($answer, $err);
    }

    /*
     * DP IMPORT
     * */
    function loadDpImport($dp_id) {
        $form = ""; $form_htm = RD . "/tpl/dp_import_str_form.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $header = ""; $header_htm = RD . "/tpl/dp_import_str_header.htm";
        if (file_exists("$header_htm")) { $header = file_get_contents($header_htm); }
        [, $csv_file_name, $pre_table] = $this->showCsvPreview($dp_id);
        $table = $this->showTablePreview($dp_id);
        $form = str_replace("{ibox_header}", ($table == "") ? $header : "", $form);
        $form = str_replace("{records_list}", "<tr><td colspan=10 align='center'>Записи не завантажено</td></tr>", $form);
        $form = str_replace("{import_file_name}", $csv_file_name, $form);
        $form = str_replace("{dp_id}", $dp_id, $form);
        $form = str_replace("{csv_str_file}",($table == "") ? $pre_table : "", $form);
        $form = str_replace("{table_str_file}", $table, $form);

        return $form;
    }

    function getCsvIndexBrands($dp_id) { $db = DbSingleton::getDb(); $dbt = DbSingleton::getTokoDb();
        $brands = [];
        $list = "";

        $r = $db->query("SELECT `article_nr_displ` FROM `J_DP_IMPORT` WHERE `dp_id` = $dp_id AND `status` = 0;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $article_nr_displ = $db->result($r, $i - 1, "article_nr_displ");
            $format_article_nr_displ = $this->getFormatArticle($article_nr_displ);
            $r2 = $dbt->query("SELECT `BRAND_ID` FROM `T2_ARTICLES` WHERE `ARTICLE_NR_SEARCH` = '$article_nr_displ' OR `ARTICLE_NR_SEARCH` = '$format_article_nr_displ';");
            $n2 = $dbt->num_rows($r2);
            for ($j = 1; $j <= $n2; $j++) {
                $brand_id = $dbt->result($r2, $j - 1, "BRAND_ID");
                $brands[] = $brand_id;
            }
        }
        $brands = array_unique($brands);
        foreach ($brands as $brand_id) {
            $brand_name = $this->getBrandName($brand_id);
            $list .= "<option value='$brand_id'>$brand_name</option>";
        }

        return $list;
    }

    function showTablePreview($dp_id) {
        $form = ""; $form_htm = RD . "/tpl/csv_str_dp_import.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm);}
        $form = str_replace("{dp_id}", $dp_id, $form);
        $form = str_replace("{csv_brands}", $this->getCsvIndexBrands($dp_id), $form);
        $form = str_replace("{storage_title}", $this->showStorageFieldsTitle(), $form);
        $table = $this->loadTablePreview($dp_id);
        $form = str_replace("{records_list}", $table, $form);
        if ($table === "") {
            $form = "";
        }

        return $form;
    }

    function getDpStockInfo($art_id) { $db = DbSingleton::getTokoDb();
        $list = "";
        $full_amount = 0;
        if ($art_id > 0) {
            $r = $db->query("SELECT `AMOUNT`, `STORAGE_ID` FROM `T2_ARTICLES_STRORAGE` WHERE `ART_ID` = $art_id;");
            $n = $db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $amount         = (int)$db->result($r, $i - 1, "AMOUNT");
                $storage_name   = $this->getStorageName($db->result($r, $i - 1, "STORAGE_ID"));
                if ($amount > 0) {
                    $list .= "<span style='font-weight: bold;'>$storage_name</span>($amount); ";
                }
                $full_amount += $amount;
            }
        }
        $list = trim($list, "; ");
        return array($full_amount, $list);
    }

    function moveArticlesStorage($dp_id, $art_id, $article_nr_displ, $brand_id, $amount, $storages) { $db = DbSingleton::getDb(); $dbt = DbSingleton::getTokoDb();
        $storage_str = [];
        $result = false;
        usort($storages, "cmpStorages");
        foreach ($storages as $value) {
            $storage_str[] = $value["storage_id"];
        }
        $storage_str = implode(",", $storage_str);
        $where_storages = "";
        if ($storage_str !== "") {
            $where_storages = "AND `STORAGE_ID` IN ($storage_str)";
        }

        $r = $dbt->query("SELECT SUM(`AMOUNT`) as summ_amount FROM `T2_ARTICLES_STRORAGE` WHERE `ART_ID` = $art_id $where_storages;");
        $summ = $dbt->result($r, 0, "summ_amount");

        $r2 = $db->query("SELECT `tpoint_id` FROM `J_DP` WHERE `id` = $dp_id LIMIT 1;");
        $tpoint_id = $db->result($r2, 0, "tpoint_id");

        $article_price = $this->getArticlePrice($art_id, $dp_id);

        if ($summ < $amount && $summ > 0) {
            $bug_amount = $amount - $summ;
            $amount = $summ;
            $r2 = $db->query("SELECT MAX(`id`) as mid FROM `J_DP_STR_UNKNOWN`;");
            $unknown_id = 0 + $db->result($r2, 0, "mid") + 1;
            $db->query("INSERT INTO `J_DP_STR_UNKNOWN` (`id`, `dp_id`, `art_id`, `article_nr_displ`, `brand_id`, `amount`, `bug_amount`, `caption`)
            VALUES ('$unknown_id', '$dp_id', '$art_id', '$article_nr_displ', '$brand_id', '$amount', '$bug_amount', 'Завелика кількість');");
        }

        if ($summ > 0) {
            if ($article_price > 0) {
                $full_amount = $amount;
                foreach ($storages as $val) {
                    $storage_id = $val["storage_id"];
                    $r = $dbt->query("SELECT `AMOUNT` FROM `T2_ARTICLES_STRORAGE` WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id LIMIT 1;");
                    $cur_amount = $dbt->result($r, 0, "AMOUNT");
                    if ($cur_amount > 0) {
                        if ($cur_amount <= $full_amount) {
                            $cut_amount = $cur_amount;
                        } else {
                            $cut_amount = $full_amount;
                        }

                        $price_end = round($article_price, 2);
                        $summ = round($price_end * $cut_amount, 2);
                        $reserv_type_id = $this->getArticleReservType($tpoint_id, $storage_id);

                        $rs = $db->query("SELECT * FROM `J_DP_STR` WHERE `dp_id` = $dp_id AND `art_id` = $art_id AND `storage_id_from` = $storage_id AND `status_dps` = 93;");
                        $ns = $db->num_rows($rs);
                        if ($ns > 0) {
                            $db->query("UPDATE `J_DP_STR` SET `amount` = `amount` + $amount, `summ` = `summ` + $summ WHERE `dp_id` = $dp_id AND `art_id` = $art_id AND `storage_id_from` = $storage_id AND `status_dps` = 93;");
                        } else {
                            $db->query("INSERT INTO `J_DP_STR` (`dp_id`, `art_id`, `article_nr_displ`, `brand_id`, `amount`, `storage_id_from`, `location_storage_id`, `reserv_type_id`, `price`, `price_end`, `summ`, `status_dps`) 
                            VALUES ('$dp_id', '$art_id', '$article_nr_displ', '$brand_id', '$cut_amount', '$storage_id', '$storage_id', '$reserv_type_id', '$article_price', '$price_end', '$summ', 93);");
                        }
                        $dbt->query("UPDATE `T2_ARTICLES_STRORAGE` SET `AMOUNT` = `AMOUNT` - $cut_amount, `RESERV_AMOUNT` = `RESERV_AMOUNT` + $cut_amount WHERE `ART_ID` = $art_id AND `STORAGE_ID` = $storage_id LIMIT 1;");
                        $full_amount -= $cut_amount;
                        if ($full_amount == 0) {
                            break;
                        }
                    }
                }
                $result = true;
            } else {
                $result = false;
                $r2 = $db->query("SELECT MAX(`id`) as mid FROM `J_DP_STR_UNKNOWN`;");
                $unknown_id = 0 + $db->result($r2, 0, "mid") + 1;
                $db->query("INSERT INTO `J_DP_STR_UNKNOWN` (`id`, `dp_id`, `art_id`, `article_nr_displ`, `brand_id`, `amount`, `bug_amount`, `caption`)
                VALUES ('$unknown_id', '$dp_id', '$art_id', '$article_nr_displ', '$brand_id', '0', '$amount', 'Артикул без ціни');");
            }
        } else {
            $r2 = $db->query("SELECT MAX(`id`) as mid FROM `J_DP_STR_UNKNOWN`;");
            $unknown_id = 0 + $db->result($r2, 0, "mid") + 1;
            $db->query("INSERT INTO `J_DP_STR_UNKNOWN` (`id`, `dp_id`, `art_id`, `article_nr_displ`, `brand_id`, `amount`, `bug_amount`, `caption`)
            VALUES ('$unknown_id', '$dp_id', '$art_id', '$article_nr_displ', '$brand_id', '0', '$amount', 'Позиції немає в наявності');");
        }

        return $result;
    }

    function loadTablePreview($dp_id, $brands = 0) { $db = DbSingleton::getDb(); $dbt = DbSingleton::getTokoDb();
        $table = ""; $where_arts = "";
        $db->query("UPDATE `J_DP_IMPORT` SET `selected` = 0 WHERE `dp_id` = $dp_id;");
        if ($brands != 0) {
            $arts = [];
            $r = $db->query("SELECT `article_nr_displ` FROM `J_DP_IMPORT` WHERE `dp_id` = $dp_id AND `status` = 0;");
            $n = $db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $art_nr_ds = $db->result($r, $i - 1, "article_nr_displ");
                $arts[] = "'" . $art_nr_ds . "'";
            }
            $arts = array_unique($arts);
            $arts = implode(",", $arts);

            if ($arts !== "") {
                $new_arts = [];
                $r = $dbt->query("SELECT `DISPLAY_NR`, `SEARCH_NUMBER` FROM `T2_CROSS` WHERE (`DISPLAY_NR` IN ($arts) OR `SEARCH_NUMBER` IN ($arts)) AND `BRAND_ID`='$brands';");
                $n = $db->num_rows($r);
                for ($i = 1; $i <= $n; $i++) {
                    $art_nr_ds = $db->result($r, $i - 1, "DISPLAY_NR");
                    $article_nr_search = $db->result($r, $i - 1, "SEARCH_NUMBER");
                    $new_arts[] = "'" . $art_nr_ds . "'";
                    $new_arts[] = "'" . $article_nr_search . "'";
                }
                $new_arts = array_unique($new_arts);
                $where_arts = "AND `article_nr_displ` IN (" . implode(",", $new_arts) . ")";
            }
        }

        $mas = [];
        $r = $db->query("SELECT `id`, `article_nr_displ`, `amount`, `art_id`, `brand_id`, `status` FROM `J_DP_IMPORT` WHERE `dp_id` = $dp_id $where_arts ORDER BY `status` ASC, `id` ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id         = $db->result($r, $i - 1, "id");
            $art_nr_ds  = $db->result($r, $i - 1, "article_nr_displ");
            $amount     = $db->result($r, $i - 1, "amount");
            $art_id     = $db->result($r, $i - 1, "art_id");
            $brand_id   = $db->result($r, $i - 1, "brand_id");
            $status     = $db->result($r, $i - 1, "status");
            $brand_name = $this->getBrandName($brand_id);
            [$full_amount, $storage_info] = $this->getDpStockInfo($art_id);
            if ($status) {
                $style = 1;
            } else {
                $style = 3;
            }
            if ($full_amount < $amount && $status) {
                $style = 2;
            }
            $mas[$i] = [
                "id"            => $id,
                "art_id"        => $art_id,
                "art_nr_ds"     => $art_nr_ds,
                "brand_id"      => $brand_id,
                "brand_name"    => $brand_name,
                "amount"        => $amount,
                "storage_info"  => $storage_info,
                "status"        => $status,
                "style"         => $style
            ];
        }

        usort($mas, "sortStyle");
        $i = 0;
        foreach ($mas as $val) {
            $i++;
            $style      = $val["style"];
            $st         = "";
            if ($style == 1) {
                $st = "style='background:lightgreen;'";
            }
            if ($style == 2) {
                $st = "style='background:lightyellow;'";
            }
            if ($style == 3) {
                $st = "style='background:pink;'";
            }
            $id         = $val["id"];
            $art_nr_ds  = $val["art_nr_ds"];
            $amount     = $val["amount"];
            $art_id     = $val["art_id"];
            $art_id     = ($art_id != 0) ? $art_id : "Невідомо";
            $brand_id   = $val["brand_id"];
            $brand_name = $val["brand_name"];
            $brand_name = ($brand_name !== "") ? $brand_name : "Не визначено";
            $storage_info = $val["storage_info"];
            $storage_info = ($storage_info !== "") ? $storage_info : "Пусто";
            $table .= "
            <tr $st>
                <td>$i</td>
                <td>$art_nr_ds</td>
                <td>$amount</td>
                <td>$art_id</td>
                <td>$brand_id ($brand_name)</td>
                <td>$storage_info</td>
            </tr>";

            if ($brands != 0) {
                $db->query("UPDATE `J_DP_IMPORT` SET `selected` = 1 WHERE `id` = $id;");
            }
        }

        return $table;
    }

    function showStorageFieldsTitle() { $db = DbSingleton::getDb();
        session_start();
        $user_id = $_SESSION["media_user_id"] + 0;
        $list = "";
        $r = $db->query("SELECT * FROM `CFN_USERS_STORAGE_CONFIG` WHERE `user_id` = $user_id;");
        $n = $db->num_rows($r);
        if ($n == 0) {
            $user_id = 0;
        }

        $r = $db->query("SELECT `storage_id` FROM `CFN_USERS_STORAGE_CONFIG` WHERE `user_id` = $user_id AND `field_active` = 1 ORDER BY `field_pos` ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $storage_id     = $db->result($r, $i - 1, "storage_id");
            $storage_name   = $this->getStorageName($storage_id);
            $list           .= "$i - $storage_name \n";
        }

        return $list;
    }

    /*
     * show storage configuration form for user
     * */
    function showStorageFieldsViewForm() { $db = DbSingleton::getDb();
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $form = ""; $form_htm = RD . "/tpl/storage_fields_view_form.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }

        $r = $db->query("SELECT * FROM `CFN_USERS_STORAGE_CONFIG` WHERE `user_id` = $user_id;");
        $n = $db->num_rows($r);
        if ($n == 0) {
            $user_id = 0;
        }

        $r = $db->query("SELECT `storage_id`, `field_active`, `field_pos` FROM `CFN_USERS_STORAGE_CONFIG` WHERE `user_id` = $user_id ORDER BY `id` ASC;");
        $n = $db->num_rows($r);
        $list = ""; $lst = array();
        for ($i = 1; $i <= $n; $i++) {
            $storage_id     = $db->result($r, $i - 1, "storage_id");
            $field_active   = $db->result($r, $i - 1, "field_active");
            $checked        = ($field_active == 0) ? "" : "checked";
            $pos            = $db->result($r, $i - 1, "field_pos");
            $storage_name   = $this->getStorageName($storage_id);
            if ($pos == 0) {
                $pos = $i;
            }
            $lst[$pos] = "<tr id='usePos_".$storage_id."'>
                <td><span class='glyphicon glyphicon-move'></span></td>
                <td>$i</td>
                <td>$storage_name ($storage_id)</td>
                <td>
                    <div class=\"switch\">
                        <div class=\"onoffswitch\">
                            <input type=\"checkbox\" $checked class=\"onoffswitch-checkbox\" id=\"use_$storage_id\" value='1'>
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

        $form = str_replace(array("{fields_list}", "{kol_fields}"), array($list, $n), $form);

        return $form;
    }

    /*
     * save storage configuration for user
     * */
    function saveStorageFieldsViewForm($kol_fields, $fl_id, $fl_ch) { $db = DbSingleton::getDb();
        $slave = new slave;
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $answer = 0; $err = "Помилка збереження даних!";
        $kol_fields = $slave->qq($kol_fields); $fl_id = $slave->qq($fl_id); $fl_ch = $slave->qq($fl_ch);
        if ($kol_fields > 0) {
            $db->query("DELETE FROM `CFN_USERS_STORAGE_CONFIG` WHERE `user_id` = $user_id;");
            for ($i = 1; $i <= $kol_fields; $i++) {
                $storage_id = $fl_id[$i];
                $field_ch = $fl_ch[$i];
                $db->query("INSERT INTO `CFN_USERS_STORAGE_CONFIG` (`user_id`, `storage_id`, `field_active`, `field_pos`) 
                VALUES ('$user_id', '$storage_id', '$field_ch', '$i');");
            }
            $answer = 1; $err = "";
        }

        return array($answer, $err);
    }

    function saveTablePreview($dp_id, $brands = 0) { $db = DbSingleton::getDb();
        $answer = 0; $err = "Помилка збереження даних!";
        if ($dp_id > 0 && $brands != 0) {
            $r = $db->query("SELECT `id`, `article_nr_displ` FROM `J_DP_IMPORT` WHERE `dp_id` = $dp_id AND `status` = 0 AND `selected` = 1;");
            $n = $db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $id = $db->result($r, $i - 1, "id") + 0;
                $article_nr_displ = $db->result($r, $i - 1, "article_nr_displ");
                $brand_id = $brands;
                $art_id = $this->getArtId($article_nr_displ, $brand_id);
                if ($art_id != 0 && $brand_id != 0) {
                    $db->query("UPDATE `J_DP_IMPORT` SET `brand_id` = $brand_id, `art_id` = $art_id, `status` = 1 WHERE `id` = $id;");
                }
            }
            $answer = 1; $err = "";
        }

        return array($answer, $err);
    }

    function getFormatArticle($str) {
        $str = str_replace(str_split('.,+-\/:*?"<>| '), "", $str);
        $str = str_replace("'", "", $str);
        $str = str_replace("`", "", $str);
        $str = str_replace(",", "", $str);
        $str = str_replace('"', "", $str);
        $str = str_replace("%20", " ", $str);
        $str = str_replace("%22", "", $str);
        $str = str_replace("%27", "", $str);
        $str = str_replace("%60", "", $str);
        $str = str_replace("&nbsp;", "", $str);
        $str = str_replace("&rsquo;", "", $str);

        return $str;
    }

    function showCsvPreview($dp_id) { $db = DbSingleton::getDb();
        $csv_exist = 0;
        $csv_file_name = "Оберіть файл";
        $pre_table = "<h3>Файл не знайдено</h3>";
        $kol_cols = $fn = 0;
        $r = $db->query("SELECT `file_name` FROM `J_DP_CSV` WHERE `dp_id` = $dp_id LIMIT 1;");
        $n = $db->num_rows($r);

        if ($n == 1) {
            $file_name = $db->result($r, 0, "file_name");
            $file_path = RD . "/cdn/dp_files/csv/$dp_id/$file_name";
            if (file_exists($file_path)) {
                $form = ""; $form_htm = RD . "/tpl/csv_str_dp_file.htm";
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
                                    $cols_list .= "<th><select id=\"clm-$i\" size='1'>
                                        <option value='0'>-</option>
                                        <option value='1'>Індекс</option>
                                        <option value='2'>Бренд</option>
                                        <option value='3'>Кількість</option>
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
                $form = str_replace("{dp_id}", $dp_id, $form);
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

    function clearDpImport($dp_id) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка збереження даних!";
        $dp_id = $slave->qq($dp_id);
        if ($dp_id > 0) {
            $db->query("DELETE FROM `J_DP_IMPORT` WHERE `dp_id` = $dp_id;");
            // clear table
            $r = $db->query("SELECT COUNT(`id`) as count_ids FROM `J_DP_IMPORT` WHERE 1;");
            $count_arts = $db->result($r, 0, "count_arts");
            if ($count_arts == 0) {
                $db->query("TRUNCATE TABLE `J_DP_IMPORT`;");
            }
            $answer = 1; $err = "";
        }

        return array($answer, $err);
    }

    function saveCsvDpImport($dp_id, $start_row, $kol_cols, $cols) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка збереження даних!"; $message = "Окей";
        $fn = 0;
        $dp_id = $slave->qq($dp_id); $start_row = $slave->qq($start_row); $kol_cols = $slave->qq($kol_cols); $cols = $slave->qq($cols);
        $db->query("DELETE FROM `J_DP_IMPORT` WHERE `dp_id` = $dp_id;");

        // clear table
        $r = $db->query("SELECT COUNT(`id`) as count_ids FROM `J_DP_IMPORT` WHERE 1;");
        $count_arts = $db->result($r, 0, "count_arts");
        if ($count_arts == 0) {
            $db->query("TRUNCATE TABLE `J_DP_IMPORT`;");
        }

        if ($dp_id > 0) {
            $r = $db->query("SELECT `file_name` FROM `J_DP_CSV` WHERE `dp_id` = $dp_id LIMIT 1;");
            $n = $db->num_rows($r);
            if ($n == 1) {
                $file_name = $db->result($r, 0, "file_name");
                $file_path = RD . "/cdn/dp_files/csv/$dp_id/$file_name";
                if (file_exists($file_path)) {
                    $index = 0;
                    $brand = 0;
                    $amount = 0;
                    for ($i = 1; $i <= $kol_cols; $i++) {
                        if ($cols[$i] == 1) {$index = $i;}
                        if ($cols[$i] == 2) {$brand = $i;}
                        if ($cols[$i] == 3) {$amount = $i;}
                    }
                    $handle = @fopen($file_path, "r");
                    if ($handle) {
                        set_time_limit(0);
                        while (($buffer = fgets($handle, 4096)) !== false) {
                            ++$fn;
                            $buf = explode(";", $buffer);
                            if ($buffer !== "") {
                                if ($fn >= $start_row) {
                                    $buf = str_replace("'", "\'", $buf);
                                    $buf = str_replace('"', '\"', $buf);
                                    $ind = trim($buf[$index - 1]);
                                    $brnd = trim($buf[$brand - 1]);
                                    $amnt = trim($buf[$amount - 1]);
                                    $amnt = str_replace(",", ".", $amnt);
                                    $amnt = str_replace(" ", "", $amnt);
                                    $brand_id = $this->getBrandId($brnd);
                                    $art_id = $this->getArtId($ind, $brand_id);
                                    if ($ind === "") {
                                        $message = "Зупинено на $fn ряді!";
                                        break;
                                    }
                                    if ($brand_id != 0 && $art_id != 0) {
                                        $status = 1;
                                    } else {
                                        $brand_id = $art_id = $status = 0;
                                    }
                                    $rs = $db->query("SELECT * FROM `J_DP_IMPORT` WHERE `dp_id` = $dp_id AND `art_id` = $art_id LIMIT 1;");
                                    $ns = $db->num_rows($rs);
                                    if ($ns > 0 && $art_id > 0) {
                                        $db->query("UPDATE `J_DP_IMPORT` SET `amount` = `amount` + $amnt WHERE `dp_id` = $dp_id AND `art_id` = $art_id LIMIT 1;");
                                    } else {
                                        $db->query("INSERT INTO `J_DP_IMPORT` (`dp_id`, `art_id`, `article_nr_displ`, `brand_id`, `amount`, `status`) 
                                        VALUES ($dp_id, $art_id, '$ind', '$brand_id', '$amnt', '$status');");
                                    }
                                }
                            }
                            if ($fn > 1000) {
                                $message = "Завантажено 1000 шт.";
                                break;
                            }
                        }
                        fclose($handle);
                        if (file_exists(RD."/cdn/dp_files/csv/$dp_id/$file_name")) {
                            unlink(RD."/cdn/dp_files/csv/$dp_id/$file_name");
                        }
                        $db->query("DELETE FROM `J_DP_CSV` WHERE `dp_id` = $dp_id;");
                        $answer = 1; $err = "";
                    }
                }
            }
        }

        return array($answer, $err, $message);
    }

    function getUserStorages() { $db = DbSingleton::getDb();
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $storages = [];

        $r = $db->query("SELECT * FROM `CFN_USERS_STORAGE_CONFIG` WHERE `user_id` = $user_id;");
        $n = $db->num_rows($r);
        if ($n == 0) {
            $user_id = 0;
        }

        $r = $db->query("SELECT `storage_id`, `field_pos` FROM `CFN_USERS_STORAGE_CONFIG` WHERE `user_id` = $user_id AND `field_active` = 1 ORDER BY `field_pos` ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $storage_id     = $db->result($r, $i - 1, "storage_id");
            $position       = $db->result($r, $i - 1, "field_pos");
            $storages[$i]   = compact("storage_id", "position");
        }

        return $storages;
    }

    function finishDpImport($dp_id) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка збереження даних!";
        $dp_id = $slave->qq($dp_id);

        if ($dp_id > 0) {
            $db->query("DELETE FROM `J_DP_STR_UNKNOWN` WHERE `dp_id` = $dp_id;");
            $r = $db->query("SELECT `id`, `article_nr_displ`, `amount`, `art_id`, `brand_id`, `status` FROM `J_DP_IMPORT` WHERE `dp_id` = $dp_id;");
            $n = $db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $id         = $db->result($r, $i - 1, "id");
                $art_nr_ds  = $db->result($r, $i - 1, "article_nr_displ");
                $amount     = $db->result($r, $i - 1, "amount");
                $art_id     = $db->result($r, $i - 1, "art_id") + 0;
                $brand_id   = $db->result($r, $i - 1, "brand_id") + 0;
                $status     = $db->result($r, $i - 1, "status");

                if ($status) {
                    $storages = $this->getUserStorages();
                    $this->moveArticlesStorage($dp_id, $art_id, $art_nr_ds, $brand_id, $amount, $storages);
                } else {
                    $r2 = $db->query("SELECT MAX(`id`) as mid FROM `J_DP_STR_UNKNOWN`;");
                    $unknown_id = 0 + $db->result($r2, 0, "mid") + 1;
                    $db->query("INSERT INTO `J_DP_STR_UNKNOWN` (`id`,`dp_id`,`art_id`,`article_nr_displ`,`brand_id`,`amount`,`bug_amount`,`caption`)
                    VALUES ($unknown_id, $dp_id, $art_id, '$art_nr_ds', $brand_id, '0', '$amount', 'Не було ART_ID чи BRAND_ID');");
                }
                $db->query("DELETE FROM `J_DP_IMPORT` WHERE `id` = $id;");
            }
            $answer = 1; $err = "";
        }

        return array($answer, $err);
    }

    /*
     * UNKNOWN ARTICLES
    */
    function loadDpUnknownArticles($dp_id) { $db = DbSingleton::getDb();
        $form = ""; $form_htm = RD . "/tpl/dp_unknown_articles_list.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }

        $r = $db->query("SELECT * FROM `J_DP` WHERE `id` = $dp_id LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 0) {
            $form_htm = RD . "/tpl/access_deny.htm";
            if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        }
        if ($n == 1) {
            list($list, $kol_rows) = $this->showDpUnknownStrList($dp_id);
            $form = str_replace("{UnknownArticlesList}", $list, $form);
            $form = str_replace("{kol_rows}", $kol_rows, $form);
            $form = str_replace("{dp_id}", $dp_id, $form);
        }

        return $form;
    }

    function showDpUnknownStrList($dp_id) { $db = DbSingleton::getDb();
        $empty_kol = 0; $list = "";

        $r = $db->query("SELECT `art_id`, `article_nr_displ`, `brand_id`, `amount`, `bug_amount`, `caption` FROM `J_DP_STR_UNKNOWN` 
        WHERE `dp_id` = $dp_id ORDER BY `id` ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $art_id = $db->result($r, $i - 1, "art_id");
            $art_nr_ds  = $db->result($r, $i - 1, "article_nr_displ");
            $brand_id   = $db->result($r, $i - 1, "brand_id");
            $brand_name = $this->getBrandName($brand_id);
            $amount     = $db->result($r, $i - 1, "amount");
            $bug_amount = $db->result($r, $i - 1, "bug_amount");
            $caption    = $db->result($r, $i - 1, "caption");
            $empty_kol++;
            $list .= "<tr id='strUnRow_$i'>
                <td><button class='btn btn-xs btn-warning'><i class='fa fa-refresh'></i></button></td>
                <td>$i</td>
                <td><input type='hidden' id='artIdUnStr_$i' value='$art_id'><input type='hidden' id='article_nr_displUnStr_$i' value='$art_nr_ds'>$art_nr_ds</td>
                <td>$brand_name</td>
                <td><input type='text' id='amountUnStr_$i' value='$amount' class='form-control input-xs numberOnlyLong'></td>
                <td><input type='text' id='bugAmountUnStr_$i' value='$bug_amount' class='form-control input-xs numberOnlyLong'></td>
                <td>$caption</td>
            </tr>";
        }

        return array($list, $empty_kol);
    }

    function clearDpUnknown($dp_id) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "Помилка збереження даних!";
        $dp_id = $slave->qq($dp_id);

        if ($dp_id > 0) {
            $db->query("DELETE FROM `J_DP_STR_UNKNOWN` WHERE `dp_id` = $dp_id;");
            $answer = 1; $err = "";
        }

        return array($answer, $err);
    }

    /*
     * EXPORT DP CARD
     * */
    function exportDpCard($dp_id, $type_id) { $db = DbSingleton::getDb();
        $list = [];
        $slave = new slave;
        $cash_id = $this->getDpCashId($dp_id);
        [$usd_to_uah, $euro_to_uah] = $this->getKoursData();

        $r = $db->query("SELECT j.*, m.mcaption as reserv_type_caption, s.name as storage_name, dps.mcaption as status_dps_name 
        FROM `J_DP_STR` j 
            LEFT OUTER JOIN `manual` m ON (m.id = j.reserv_type_id AND m.key = 'reserv_type') 
            LEFT OUTER JOIN `manual` dps ON (dps.id = j.status_dps AND dps.key = 'status_dps') 
            LEFT OUTER JOIN `STORAGE` s ON (s.id = j.storage_id_from) 
        WHERE j.dp_id = $dp_id
        ORDER BY j.id ASC;");
        $n = $db->num_rows($r);
        $kl_rw = $n;
        for ($i = 1; $i <= $kl_rw; $i++) {
            $art_id         = $db->result($r, $i - 1, "art_id");
            $art_nr_ds      = $db->result($r, $i - 1, "article_nr_displ");
            $brand_id       = $db->result($r, $i - 1, "brand_id");
            $amount         = $db->result($r, $i - 1, "amount");
            $text           = $this->getArticleName($art_id);
            $brand_name     = $this->getBrandName($brand_id);
            $amount_dp      = $amount;
            $amount_collect = $db->result($r, $i - 1, "amount_collect");
            $amount_bug_db  = $db->result($r, $i - 1, "amount_bug");

            if ($amount_collect > 0 || $amount_bug_db != 0) {
                $amount_dp  = $amount_collect;
            }
            $price          = $slave->to_money($db->result($r, $i - 1, "price"));
            $price_end      = $slave->to_money($db->result($r, $i - 1, "price_end"));
            $summ           = $slave->to_money($db->result($r, $i - 1, "summ"));

            if ($cash_id == 1) {
                $price      = round($price * $usd_to_uah, 2);
                $price_end  = round($price_end * $usd_to_uah, 2);
                $summ       = round($amount_dp * $price_end, 2);
            }
            if ($cash_id == 3) {
                $price      = round($price * $usd_to_uah / $euro_to_uah, 2);
                $price_end  = round($price_end * $usd_to_uah / $euro_to_uah, 2);
                $summ       = round($amount_dp * $price_end, 2);
            }
            if ($type_id == 2) {
                $price      = str_replace(".", ",", "$price");
                $price_end  = str_replace(".", ",", "$price_end");
                $summ       = str_replace(".", ",", "$summ");
            }
            $list[$i] = array($art_nr_ds, $brand_name, $text, $amount, $price, $price_end, $summ);
        }

        return $list;
    }

    /*
     * DP Combine
     * */
    function showCombineDpForm()
    {
        $db = DbSingleton::getDb();
        session_start();
        $ses_tpoint_id = $_SESSION["media_tpoint_id"];
        $media_user_id = $_SESSION["media_user_id"];
        $media_role_id = $_SESSION["media_role_id"];
        $form = ""; $form_htm = RD."/tpl/dp/combine.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $list = "";
        $where = " AND (`tpoint_id` = '$ses_tpoint_id' OR `user_id` = '$media_user_id') AND ((`status_dp` != 0 AND `summ` > 0) OR (`status_dp` = 81)) ";
        $limit = "LIMIT 0,500";
        if ($media_user_id == 1) {
            $where = " AND `status_dp` != 0";
            $limit = "";
        }
        if ($media_role_id == 1) {
            $where = " AND ((`status_dp` != 0 AND `summ` > 0) OR (`status_dp` = 81)) ";
            $limit = "";
        }

        $r = $db->query("SELECT `id`, `prefix`, `doc_nom`, `user_use` FROM `J_DP` WHERE `status_dp` = 79 $where $limit;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $dp_id      = $db->result($r, $i - 1, "id");
            $prefix     = $db->result($r, $i - 1, "prefix");
            $doc_nom    = $db->result($r, $i - 1, "doc_nom");
            $user_use   = $db->result($r, $i - 1, "user_use");
            $doc_name   = "$prefix - $doc_nom";
            $disabled   = "";

            if ($user_use != 0 && $user_use != $media_user_id) {
                $disabled = "disabled";
                $user_name = $this->getMediaUserName($user_use);
                $doc_name .= " ($user_name)";
            }

            $list .= "<option value='$dp_id' $disabled>$doc_name</option>";
        }

        $form = str_replace(array("{select_main_dp}", "{select_cross_dp}", "{user_id}"), array($list, "", $media_user_id), $form);

        return $form;
    }

    public function getCombineDpCrossList($main_dp_id)
    {
        $db = DbSingleton::getDb();
        session_start();
        $gmanual = new gmanual;
        $ses_tpoint_id = $_SESSION["media_tpoint_id"];
        $media_user_id = $_SESSION["media_user_id"];
        $media_role_id = $_SESSION["media_role_id"];
        $list = "Схожих не знайдено!";

        if ($main_dp_id > 0) {
            $r = $db->query("SELECT `client_id`, `client_conto_id`, `tpoint_id`, `doc_type_id` FROM `J_DP` WHERE `id` = $main_dp_id LIMIT 1;");
            $client_id          = $db->result($r, 0, "client_id");
            $client_conto_id    = $db->result($r, 0, "client_conto_id");
            $tpoint_id          = $db->result($r, 0, "tpoint_id");
            $doc_type_id        = $db->result($r, 0, "doc_type_id");

            $where = " AND (`tpoint_id` = '$ses_tpoint_id' OR `user_id` = '$media_user_id') AND ((`status_dp` != 0 AND `summ` > 0) OR (`status_dp` = 81)) ";
            $limit = "LIMIT 0,500";

            if ($media_user_id == 1) {
                $where = " AND `status_dp` != 0";
                $limit = "";
            }

            if ($media_role_id == 1) {
                $where = " AND ((`status_dp` != 0 AND `summ` > 0) OR (`status_dp` = 81)) ";
                $limit = "";
            }

            $rs = $db->query("SELECT `id`, `prefix`, `doc_nom`, `user_use`, `order_info_id`, `status_dp`
            FROM `J_DP` 
            WHERE `id` != $main_dp_id AND `status_dp` = 79 AND `client_id` = $client_id AND `client_conto_id` = $client_conto_id AND `tpoint_id` = $tpoint_id AND `doc_type_id` = $doc_type_id
            $where $limit;");
            $ns = $db->num_rows($rs);

            if ($ns > 0) {
                $list = "<table class='table-combine'>";

                for ($i = 1; $i <= $ns; $i++) {
                    $dp_id      = $db->result($rs, $i - 1, "id");
                    $prefix     = $db->result($rs, $i - 1, "prefix");
                    $doc_nom    = $db->result($rs, $i - 1, "doc_nom");
                    $user_use   = $db->result($rs, $i - 1, "user_use");
                    $order_info_id   = $db->result($rs, $i - 1, "order_info_id");
                    $status_dp   = $db->result($rs, $i - 1, "status_dp");

                    $order_info_text = $this->getOrderDeliveryInfo($order_info_id);
                    $doc_name   = "$prefix - $doc_nom";
                    $disabled   = "";

                    if ($user_use > 0 && (int)$user_use !== (int)$media_user_id) {
                        $disabled = "disabled";
                        $user_name = $this->getMediaUserName($user_use);
                        $doc_name .= " ($user_name)";
                    }

                    $dp_note = $this->getDpNote($dp_id);
                    $status_dp_name = ($status_dp !== 80 && $status_dp !== "80") ? $gmanual->get_gmanual_caption($status_dp) : $this->getDpStrStatus($dp_id);

                    $list .= "
                    <tr>
                        <td>
                            <input type='checkbox' name='dp_cross_list' id='$dp_id' $disabled>
                        </td>
                        <td>
                            <label for='$dp_id'>$doc_name</label>
                        </td>
                        <td>
                            $status_dp_name
                        </td>
                        <td>
                            $dp_note
                        </td>
                        <td>
                            $order_info_text
                        </td>
                    </tr>";
                }

                $list .= "</table>";
            }
        }

        return $list;
    }

    public function saveCombineDpCross($main_dp_id, $cross_dp_ids): array
    {
        $db = DbSingleton::getDb();
        //$cross_dp_ids = implode(",", $cross_dp_ids);
        $answer = 0; $err = "Error";

        if (!empty($cross_dp_ids) && $main_dp_id > 0) {
            //$db->query("UPDATE `J_DP_STR` SET `dp_id` = $main_dp_id WHERE `dp_id` IN ($cross_dp_ids);");

            $user_uses = [];

            foreach ($cross_dp_ids as $dp_id) {
                $r = $db->query("SELECT `user_use` FROM `J_DP` WHERE `id` = $dp_id LIMIT 1;");
                $n = $db->num_rows($r);
                if ($n > 0) {
                    $user_use = $db->result($r, 0, "user_use");
                    if ($user_use > 0) {
                        $user_uses[] = $dp_id;
                    } else {
                        $db->query("UPDATE `J_DP_STR` SET `dp_id` = $main_dp_id WHERE `dp_id` = $dp_id;");
                        $db->query("UPDATE `J_DP` SET `status_dp` = 81 WHERE `id` = $dp_id LIMIT 1;");
                        $this->updateDpWeightVolume($main_dp_id);
                        $this->updateDpSumm($main_dp_id);
                    }
                }
            }

            if (count($cross_dp_ids) > count($user_uses)) {
                $answer = 1;
            } else {
                $answer = 0;
            }

            $err = "";
            if (!empty($user_uses)) {
                $err = "Не оброблені ДП: ";
                foreach ($user_uses as $dp_id) {
                    $err .= $dp_id . "; ";
                }
            }

        }

        return array($answer, $err);
    }

    public function showDeliveryCard($dp_id)
    {
        $db = DbSingleton::getDb();

        $form = "";
        $form_htm = RD . "/tpl/dp_card_delivery.htm";
        if (file_exists($form_htm)) {
            $form = file_get_contents($form_htm);
        }

        $r = $db->query("SELECT `client_id`, `client_conto_id`, `order_info_id` FROM `J_DP` WHERE `id` = $dp_id LIMIT 1;");
        $client_id          = $db->result($r, 0, "client_id");
        $client_conto_id    = $db->result($r, 0, "client_conto_id");
        $order_info_id      = $db->result($r, 0, "order_info_id");

        $form = str_replace("{delivery_user_saved}", $this->getDeliveryUserSaved($client_id, $dp_id), $form);
        $form = $this->getOrderInfoBlock($form, $order_info_id, $client_conto_id);

        return $form;
    }

    public function dropDeliveryCard($dp_id): array
    {
        $db = DbSingleton::getDb();
        $answer = 0; $err = "Помилка видалення!";
        if ($dp_id > 0) {
            $db->query("UPDATE `J_DP` SET `order_info_id` = 0 WHERE `id` = $dp_id LIMIT 1;");
            $answer = 1; $err = "";
        }

        return array($answer, $err);
    }

}

function cmpStorages($a, $b) {
    if ($a["position"] == $b["position"]) return 0;
    return $a["position"] > $b["position"] ? 1 : -1;
}

function sortStyle($a, $b) {
    if ($a["style"] == $b["style"]) return 0;
    return $a["style"] < $b["style"] ? 1 : -1;
}
