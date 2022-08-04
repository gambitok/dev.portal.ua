<?php

class action_clients {

    function getClientName($client_id){$db=DbSingleton::getDb();
        $r=$db->query("SELECT `full_name` FROM `A_CLIENTS` WHERE `id`='$client_id' LIMIT 1;");
        $name=$db->result($r,0,"full_name");
        return $name;
    }

    function getCategoryName($category_id) {$db=DbSingleton::getDb();
        $r=$db->query("SELECT `mcaption` FROM `manual` WHERE `id`='$category_id' LIMIT 1;");
        $name=$db->result($r,0,"mcaption");
        return $name;
    }

    function getOperPrice($art_id) {$db=DbSingleton::getTokoDb();
        $r=$db->query("SELECT `OPER_PRICE` FROM `T2_ARTICLES_PRICE_STOCK` WHERE `ART_ID`='$art_id' LIMIT 1;");
        $oper_price=$db->result($r,0,"OPER_PRICE");
        return $oper_price;
    }

    function getOperPriceAction($art_id) {$db=DbSingleton::getDb();
        $r=$db->query("SELECT `oper_price` FROM `ACTION_CLIENTS` WHERE `art_id`='$art_id' LIMIT 1;");
        $oper_price=$db->result($r,0,"oper_price");
        return $oper_price;
    }

    function getArtDispl($art_id) {$db=DbSingleton::getTokoDb();
        $r=$db->query("SELECT `ARTICLE_NR_DISPL` FROM `T2_ARTICLES` WHERE `ART_ID`='$art_id' LIMIT 1;");$n=$db->num_rows($r);$ARTICLE_NR_DISPL="";
        if ($n==1){$ARTICLE_NR_DISPL=$db->result($r,0,"ARTICLE_NR_DISPL");}
        return $ARTICLE_NR_DISPL;
    }

    function getActionClientsList() {$db=DbSingleton::getDb();
        $r=$db->query("SELECT * FROM `A_CLIENTS` WHERE `status`=1;"); $n=$db->num_rows($r); $list="";
        for ($i=1;$i<=$n;$i++) {
            $client_id = $db->result($r, $i-1, "id");
            $name = $db->result($r, $i-1, "full_name");
            $list.="<option value='$client_id'>$client_id $name</option>";
        }
        return $list;
    }

    function getClientCategoryList() {$db=DbSingleton::getDb();
        $r=$db->query("SELECT * FROM `manual` WHERE `key`='customers_categories' AND `ison`=1 ORDER BY `id` ASC;"); $n=$db->num_rows($r); $list="";
        for ($i=1;$i<=$n;$i++){
            $category_id=$db->result($r, $i-1, "id");
            $name=$db->result($r, $i-1, "mcaption");
            $list.="<option value='$category_id'>$name</option>";
        }
        return $list;
    }

    function showActionClientsList() {$db=DbSingleton::getDb();
        $r=$db->query("SELECT * FROM `ACTION_CLIENTS`;"); $n=$db->num_rows($r); $list="";
        for ($i=1;$i<=$n;$i++){
            $category_list="";
            $action_id=$db->result($r,$i-1,"id");
            $art_id=$db->result($r,$i-1,"art_id"); $art_name=$this->getArtDispl($art_id);
            $amount=$db->result($r,$i-1,"amount");
            $style="";
            $status_update=$db->result($r,$i-1,"status_update"); if ($status_update==1) $style="background:pink;";
            $status=$db->result($r,$i-1,"status"); if ($status==0) $style="background:lightgoldenrodyellow;";

            $r2=$db->query("SELECT * FROM `ACTION_CLIENTS_CATEGORY` WHERE `action_id`='$action_id' ORDER BY `category_id`;"); $n2=$db->num_rows($r2);
            for ($j=1;$j<=$n2;$j++){
                $category_id=$db->result($r2,$j-1,"category_id");
                $category_name=$this->getCategoryName($category_id);
                $category_list.="$category_name; ";
            }

            $list.="<tr style='cursor:pointer; $style' onClick='showActionClientsCard($action_id);'>
                <td>Акція №$action_id</td>
                <td>$art_name</td>
                <td>$amount</td>
                <td>$category_list</td>
            </tr>";
        }
        return $list;
    }

    function showActionClientsCard($action_id,$sel_art_id=0) {$db=DbSingleton::getDb();
        $select_clients=[];$select_categories=[];$media_users=new media_users;
        $form="";$form_htm=RD."/tpl/action_clients_card.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}

        $r=$db->query("SELECT * FROM `ACTION_CLIENTS` WHERE `id`='$action_id' LIMIT 1;");
        $art_id=$db->result($r,0,"art_id");
        $article_nr_displ=$this->getArtDispl($art_id);
        $amount=$db->result($r,0,"amount");
        $max_amount=$db->result($r,0,"max_amount");
        $price=$db->result($r,0,"price");
        $oper_price=$db->result($r,0,"oper_price");
        $data=$db->result($r,0,"data");
        $return_delay=$db->result($r,0,"return_delay");
        $timestamp=$db->result($r,0,"timestamp");
        $status_update=$db->result($r,0,"status_update");
        $status=$db->result($r,0,"status");
        $user_id=$db->result($r,0,"user_id"); $user_name=$media_users->getMediaUserName($user_id);
        $user_update="$user_name $timestamp";

        //new Card
        if ($action_id==0) {
            $art_id=0;
            $article_nr_displ="";
            if ($sel_art_id>0) {$art_id=$sel_art_id; $article_nr_displ=$this->getArtDispl($art_id);}
            $amount=0;
            $max_amount=0;
            $price=0;
            $oper_price=0;
            $data=date("00-00-00");
            $return_delay=0;
            $status_update=0;
            $status=1;
            session_start();$user_id=$_SESSION["media_user_id"];
            $r2=$db->query("SELECT MAX(`id`) as mid FROM `ACTION_CLIENTS`;");$new_action_id=0+$db->result($r2,0,"mid")+1;
            $db->query("INSERT INTO `ACTION_CLIENTS` (`id`, `art_id`, `amount`, `max_amount`, `price`, `oper_price`, `data`, `user_id`, `status`) 
            VALUES ('$new_action_id', '$art_id', '$amount', '$max_amount', '$price', '$oper_price', '$data', '$user_id', '$status');");
            $action_id=$new_action_id;
        }

        $r=$db->query("SELECT * FROM `ACTION_CLIENTS_LIST` WHERE `action_id`='$action_id';"); $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++) {
            $client_id=$db->result($r,$i-1,"client_id");
            array_push($select_clients,$client_id);
        }

        $r=$db->query("SELECT * FROM `ACTION_CLIENTS_CATEGORY` WHERE `action_id`='$action_id';"); $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++) {
            $category_id=$db->result($r,$i-1,"category_id");
            array_push($select_categories,$category_id);
        }

        $form=str_replace("{action_id}",$action_id,$form);
        $form=str_replace("{art_id}",$art_id,$form);
        $form=str_replace("{article_nr_displ}",$article_nr_displ,$form);
        $form=str_replace("{amount}",$amount,$form);
        $form=str_replace("{max_amount}",$max_amount,$form);
        $form=str_replace("{price}",$price,$form);
        $form=str_replace("{oper_price}",$oper_price,$form);
        $form=str_replace("{actual_oper_price}",$status_update ? "<span class='red-bg'><i class='fa fa-question'></i> Актуальна операційна ціна - ".$this->getOperPrice($art_id)." </span>" : "",$form);
        $form=str_replace("{action_data}",$data,$form);
        $form=str_replace("{return_delay}",$return_delay,$form);
        $form=str_replace("{user_update}",$user_update,$form);
        $form=str_replace("{status_disable}",!$status,$form);
        $form=str_replace("{status_cap}",$status ? "Відключити" : "Включити",$form);
        $form=str_replace("{clients_list}",$this->getActionClientsList(),$form);
        $form=str_replace("{category_list}",$this->getClientCategoryList(),$form);
        return array($form,$select_clients,$select_categories,$action_id);
    }

    function saveActionClients($action_id,$art_id,$client_list,$amount,$max_amount,$price,$action_data,$category_list,$return_delay) {$db=DbSingleton::getDb();
        $answer=0;$err="Помилка додання акції!";
        session_start();$user_id=$_SESSION["media_user_id"];

        if ($art_id>0 && $amount>0 && $price>0) {
            if ($this->getOperPriceAction($art_id)==0) $oper_price=$this->getOperPrice($art_id); else $oper_price=$this->getOperPriceAction($art_id);

            if ($action_id==0) {
                $r2=$db->query("SELECT MAX(`id`) as mid FROM `ACTION_CLIENTS`;");$new_action_id=0+$db->result($r2,0,"mid")+1;
                $action_id=$new_action_id;
            }

            $db->query("DELETE FROM `ACTION_CLIENTS_LIST` WHERE `action_id`='$action_id';");
            $db->query("DELETE FROM `ACTION_CLIENTS_CATEGORY` WHERE `action_id`='$action_id';");

            $db->query("UPDATE `ACTION_CLIENTS` SET `art_id`='$art_id', `amount`='$amount', `max_amount`='$max_amount', `price`='$price', `oper_price`='$oper_price', `data`='$action_data',
            `return_delay`='$return_delay', `user_id`='$user_id', `status_update`='0', `status`='1' WHERE `id`='$action_id';");

            foreach ($client_list as $client_id) {
                $db->query("INSERT INTO `ACTION_CLIENTS_LIST` (`action_id`,`art_id`,`client_id`) VALUES ('$action_id','$art_id','$client_id');");
            }

            foreach ($category_list as $category_id) {
                $db->query("INSERT INTO `ACTION_CLIENTS_CATEGORY` (`action_id`,`art_id`,`category_id`) VALUES ('$action_id','$art_id','$category_id');");
            }

            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function dropActionClients($action_id) {$db=DbSingleton::getDb();
        $answer=0;$err="Помилка видалення акції!";
        if ($action_id>0) {
            $db->query("DELETE FROM `ACTION_CLIENTS` WHERE `id`='$action_id';");
            $db->query("DELETE FROM `ACTION_CLIENTS_LIST` WHERE `action_id`='$action_id';");
            $db->query("DELETE FROM `ACTION_CLIENTS_CATEGORY` WHERE `action_id`='$action_id';");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function disableActionClients($action_id,$status) {$db=DbSingleton::getDb();
        $answer=0;$err="Помилка оновлення акції!";
        if ($action_id>0) {
            session_start();$user_id=$_SESSION["media_user_id"];
            $db->query("UPDATE `ACTION_CLIENTS` SET `user_id`='$user_id', `status`='$status' WHERE `id`='$action_id';");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function getActionsList($art_id) {$db=DbSingleton::getDb();
        $r=$db->query("SELECT * FROM `ACTION_CLIENTS` WHERE `art_id`='$art_id';"); $n=$db->num_rows($r); $list="";
        for ($i=1;$i<=$n;$i++) {
            $action_id = $db->result($r, $i-1, "id");
            $price = $db->result($r, $i-1, "price");
            $amount = $db->result($r, $i-1, "amount");
            // $status_update = $db->result($r, $i-1, "status_update");
            // $status = $db->result($r, $i-1, "status");
            $data = $db->result($r, $i-1, "data");
            $cur_data = date("Y-m-d");
            $true_clr="";
            if ($cur_data<=$data) $true_clr="color:lightcoral!important;";
            // if($status_update) $true_clr="color:lightcoral!important;";
            // if(!$status) $true_clr="color:#f8ac59!important;";
            $list.="
            <a title=\"Акція №$action_id - $price$ ($amount шт.)\" class='btn btn-sm btn-success btn-outline tooltips' data-toggle=\"tooltip\" data-placement=\"bottom\">
                <i onClick=\"showActionClientsCard($action_id);\" class=\"fa fa-gift\" style=\"$true_clr\"></i>
            </a>
            <br>";
        }
        if ($n==0) $list="<a title=\"Нова акція\" class=\"btn btn-sm btn-success btn-outline tooltips\"  data-toggle=\"tooltip\" data-placement=\"bottom\" onclick=\"showActionClientsCard('0',$art_id);\">
            <i class=\"fa fa-plus\"></i>
        </a>";
        return $list;
    }

    function checkActionStr($art_id,$client_id) {$db=DbSingleton::getDb();
        $categories=[];$actions=[];
        $r=$db->query("SELECT * FROM `A_CLIENTS` WHERE `id`='$client_id';"); $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++) {
            $category_id = $db->result($r, $i-1, "client_category");
            array_push($categories,$category_id);
        }
        $categories=implode(",",$categories);

        $r=$db->query("SELECT * FROM `ACTION_CLIENTS` WHERE `art_id`='$art_id';"); $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++) {
            $action_id=$db->result($r, $i-1, "id");
            $r2=$db->query("SELECT * FROM `ACTION_CLIENTS_LIST` WHERE `action_id`='$action_id' AND `client_id`='$client_id';"); $n2=$db->num_rows($r2);
            if ($n2>0) array_push($actions,$action_id);
            if ($categories!="") {
                $r3=$db->query("SELECT * FROM `ACTION_CLIENTS_CATEGORY` WHERE `action_id`='$action_id' AND `category_id` IN ($categories);"); $n3=$db->num_rows($r3);
                if ($n3>0) array_push($actions,$action_id);
            }
        }

        $actions=implode(",",$actions);

        if ($actions=="") return false; else {
            $r=$db->query("SELECT * FROM `ACTION_CLIENTS` WHERE `id` IN ($actions) LIMIT 1;");
            $amount=$db->result($r,0,"amount");
            $max_amount=$db->result($r,0,"max_amount");
            $price=$db->result($r,0,"price");
            $data=$db->result($r,0,"data");
            if ($this->checkActionAmount($art_id,$max_amount,$data)) return array($amount,$price);
            else return false;
        }
    }

    function checkActionAmount($art_id,$max_amount,$data) {$db=DbSingleton::getDb(); $dbt=DbSingleton::getTokoDb();
        $data_today=date("Y-m-d"); $all_amount=0;

        $r=$dbt->query("SELECT * FROM `T2_ARTICLES_STRORAGE` WHERE `ART_ID`='$art_id';"); $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++) {
            $amount = $db->result($r, $i - 1, "AMOUNT");
            $all_amount+=$amount;
        }

        $r=$db->query("SELECT * FROM `J_DP_STR` WHERE `art_id`='$art_id' AND `status_dps`='93';"); $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++) {
            $amount = $db->result($r, $i - 1, "amount");
            $all_amount+=$amount;
        }

        $data>=$data_today && $all_amount>$max_amount ? $result=true : $result=false;

        return $result;
    }

    function showSearchIndexForm() {
        $form="";$form_htm=RD."/tpl/action_clients_search.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $form=str_replace("{article_displ_list}","<tr align=\"center\"><td colspan=\"10\">Ничего не найдено</td></tr>",$form);
        return $form;
    }

    function searchArticleDispl($article_nr_displ,$brand_id) { $db=DbSingleton::getTokoDb();
        $brands=""; $type_search=0; $form="<tr align=\"center\"><td colspan=\"10\">Ничего не найдено</td></tr>";
        $where_brand="";$group_brand="GROUP BY t2c.BRAND_ID";
        if ($brand_id!="" && $brand_id>0){$where_brand=" AND t2c.BRAND_ID='$brand_id'";}
        $query="SELECT t2b.BRAND_NAME, t2n.NAME, t2c.BRAND_ID, t2c.DISPLAY_NR, t2c.ART_ID, t2c.KIND, t2c.RELATION
        FROM `T2_CROSS` t2c
             INNER JOIN `T2_BRANDS` t2b on t2b.BRAND_ID=t2c.BRAND_ID
             LEFT OUTER JOIN `T2_NAMES` t2n on t2n.ART_ID=t2c.ART_ID
        WHERE (t2c.SEARCH_NUMBER='$article_nr_displ' OR t2c.DISPLAY_NR='$article_nr_displ') $where_brand $group_brand ORDER BY t2n.NAME ASC;";

        $r=$db->query($query); $n=$db->num_rows($r);

        if ($n==0) {
            //no result
            $type_search=0;
            $form="<tr align=\"center\"><td colspan=\"10\">Ничего не найдено</td></tr>";
        }

        if ($n==1) {
            //search form
            $type_search=1;
            $brands="";
            $art_id=$db->result($r,0,"ART_ID");
            $display_nr=$db->result($r,0,"DISPLAY_NR");
            $brand_name=$db->result($r,0,"BRAND_NAME");
            $name=$db->result($r,0,"NAME");
            $form="<tr onclick=\"setActionArtID('$art_id','$display_nr')\" style=\"cursor: pointer;\">
                <td>$display_nr</td>
                <td>$brand_name</td>
                <td>$name</td>
                <td>$art_id</td>
            </tr>";
        }

        if ($n>1) {
            //brand list
            $type_search=2;
            $form="";
            $brands="<ul class='list-unstyled'>";
            for ($i=1; $i<=$n; $i++) {
                $brand_id=$db->result($r,$i-1,"BRAND_ID");
                $brand_name=$db->result($r,$i-1,"BRAND_NAME");
                $brands.="<li onclick=\"searchArticleDispl('$brand_id')\" style=\"cursor: pointer;\">$brand_name ($brand_id)</li>";
            }
            $brands.="</ul>";
        }

        return array($form,$type_search,$brands);
    }

}