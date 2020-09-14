<?php

class export_stock_price {

    function getPriceList() {
        $n=50;$list="";
        for ($i=1;$i<=$n;$i++){
            $ii=$i-1;
            $list.="<option value='$i'>Price $ii</option>";
        }
        return $list;
    }

    function getKoursData() { $db = DbSingleton::getDb();
        $slave=new slave;$usd_to_uah=0;$eur_to_uah=0;
        $r=$db->query("SELECT `kours_value` FROM `J_KOURS` WHERE `cash_id`='2' AND `in_use`='1' ORDER BY `id` DESC LIMIT 1;");$n=$db->num_rows($r);
        if ($n==1) {$usd_to_uah=$slave->to_money(round($db->result($r,0,"kours_value"),2));}
        $r=$db->query("SELECT `kours_value` FROM `J_KOURS` WHERE `cash_id`='3' AND `in_use`='1' ORDER BY `id` DESC LIMIT 1;");$n=$db->num_rows($r);
        if ($n==1) {$eur_to_uah=$slave->to_money(round($db->result($r,0,"kours_value"),2));}
        return array($usd_to_uah,$eur_to_uah);
    }
	
	function exportStocks() {
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=export_stocks.csv'); ob_clean();
		$output = fopen('php://output', 'w');
		fputcsv($output, array("ART_ID","INDEX","BRAND","AMOUNT","RESERV_AMOUNT","SKLAD","FULL_NAME","CROSS","UNIV_NUMBER"),$delimiter = ';');
		$array=$this->getArticlesStockData(); 
		foreach ($array as $fields) {
			fputcsv($output,$fields,$delimiter = ';');
		}
		exit(0);
	}	
	
	function exportPrices($price_select) {
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=export_prices.csv'); ob_clean();
		$output = fopen('php://output', 'w');
		fputcsv($output, array("ART_ID","ARTICLE_NR_DISPL","ARTICLE_NR_SEARCH","BRAND_ID","BRAND_NAME","PRICE","CASH"),$delimiter = ';');
		$array=$this->getArticlesPriceData($price_select);
		foreach ($array as $fields) {
			fputcsv($output,$fields,$delimiter = ';');
		}
		exit(0);
	}

	function exportClients() {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=export_clients.csv'); ob_clean();
        $output = fopen('php://output', 'w');
        fputcsv($output, array("ARTICLE_NR_DISPL","BRAND_NAME","AMOUNT","DELIVERY","PRICE","TITLE","DESCRIPTION","IMAGES"),$delimiter = ';');
        $array=$this->getArticlesClientsData();
        foreach ($array as $fields) {
            fputcsv($output,$fields,$delimiter = ';');
        }
        exit(0);
    }

    function exportSupplClients() {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=export_clients.csv'); ob_clean();
        $output = fopen('php://output', 'w');
        fputcsv($output, array("ARTICLE_NR_DISPL","BRAND_NAME","AMOUNT","DELIVERY","PRICE","TITLE","DESCRIPTION","IMAGES"),$delimiter = ';');
        $array=$this->getSupplArticlesClientsData();
        foreach ($array as $fields) {
            fputcsv($output,$fields,$delimiter = ';');
        }
        exit(0);
    }

    function exportClientsAll() {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=export_clients.csv'); ob_clean();
        $output = fopen('php://output', 'w');
        fputcsv($output, array("ARTICLE_NR_DISPL","BRAND_NAME","AMOUNT","DELIVERY","PRICE","TITLE","DESCRIPTION","IMAGES"),$delimiter = ';');
        $array=$this->getArticlesClientsAll();
        foreach ($array as $fields) {
            fputcsv($output,$fields,$delimiter = ';');
        }
        exit(0);
    }
	
	function getArticlesStockData() { $db=DbSingleton::getTokoDb();
		$r=$db->query("SELECT t2a.`ART_ID`, t2a.`ARTICLE_NR_DISPL` as `INDEX`, t2b.`BRAND_NAME` as `BRAND`, t2s.`AMOUNT`, t2s.`RESERV_AMOUNT`, 
		st.`name` as `SKLAD`, st.`full_name`, t2ic.`CROSS`, t2au.`UNIV_NUMBER`
		FROM `T2_ARTICLES_STRORAGE` t2s
			LEFT JOIN `T2_ARTICLES` t2a ON t2s.`ART_ID`=t2a.`ART_ID`
			LEFT JOIN `T2_BRANDS` t2b ON t2a.`BRAND_ID`=t2b.`BRAND_ID`
			LEFT JOIN `STORAGE` st ON st.`id`=t2s.`STORAGE_ID`
			LEFT JOIN `T2_INNER_CROSS` t2ic ON t2a.`ART_ID`=t2ic.`ART_ID`
			LEFT JOIN `T2_ARTICLES_UNIV_NUMBER` t2au ON t2a.`ART_ID`=t2au.`ART_ID`
		ORDER BY t2b.`BRAND_NAME` ASC, t2a.`ARTICLE_NR_DISPL` ASC"); $n=$db->num_rows($r); $array=[];
		for ($i=1;$i<=$n;$i++){
			$art_id=$db->result($r,$i-1,"ART_ID");
			$index=$db->result($r,$i-1,"INDEX");
			$brand=$db->result($r,$i-1,"BRAND");
			$amount=$db->result($r,$i-1,"AMOUNT");
			$reserv_amount=$db->result($r,$i-1,"RESERV_AMOUNT");
			$sklad=$db->result($r,$i-1,"SKLAD");
			$full_name=$db->result($r,$i-1,"full_name");
			$cross=$db->result($r,$i-1,"CROSS");
			$univ_number=$db->result($r,$i-1,"UNIV_NUMBER");
			$array[$i]=array($art_id,$index,$brand,$amount,$reserv_amount,$sklad,$full_name,$cross,$univ_number);	
		}
		return $array;
	}
	
	function getArticlesPriceData($price_select) { $db=DbSingleton::getTokoDb();
	    $array=[];$price_val="price_$price_select"; $cash_to=2;//usd
		$r=$db->query("SELECT t2a.*, t2b.`BRAND_NAME`, t2pr.`$price_val`, t2pr.`cash_id` 
		FROM `T2_ARTICLES_PRICE_RATING` t2pr
			LEFT JOIN `T2_ARTICLES` t2a ON t2pr.`art_id`=t2a.`art_id` 
			LEFT JOIN `T2_BRANDS` t2b ON t2a.`BRAND_ID`=t2b.`BRAND_ID`
		WHERE t2pr.`in_use`=1;"); $n=$db->num_rows($r);
		for ($i=1;$i<=$n;$i++) {
			$art_id=$db->result($r,$i-1,"ART_ID");
			$art_displ=$db->result($r,$i-1,"ARTICLE_NR_DISPL");
			$art_search=$db->result($r,$i-1,"ARTICLE_NR_SEARCH");
			$brand_id=$db->result($r,$i-1,"BRAND_ID");
			$brand=$db->result($r,$i-1,"BRAND_NAME");
			$price=$db->result($r,$i-1,"$price_val");
			$cash_id=$db->result($r,$i-1,"cash_id"); $cash_name=$this->getCashName($cash_id);
		    $price=$this->getPriceRatingKours($price,$cash_id,$cash_to);
			$price=str_replace(".",",",$price);
			$array[$i]=array($art_id,$art_displ,$art_search,$brand_id,$brand,$price,$cash_name);
		}
		return $array;
	}

	function getPriceRatingKours($price,$cash_id_from,$cash_id_to) {
        list($usd_to_uah,$eur_to_uah)=$this->getKoursData();
        if ($cash_id_from==$cash_id_to) {$price=$price*1;}
        if ($cash_id_from==1 && $cash_id_to==2) {$price=$price/$usd_to_uah;}
        if ($cash_id_from==1 && $cash_id_to==3) {$price=$price/$eur_to_uah;}
        if ($cash_id_from==2 && $cash_id_to==1) {$price=$price*$usd_to_uah;}
        if ($cash_id_from==3 && $cash_id_to==1) {$price=$price*$eur_to_uah;}
        if ($cash_id_from==2 && $cash_id_to==3) {$price=$price*$usd_to_uah/$eur_to_uah;}
        if ($cash_id_from==3 && $cash_id_to==2) {$price=$price*$eur_to_uah/$usd_to_uah;}
        $price=round($price,2);
        return $price;
    }

    function getCashName($cash_id){$db=DbSingleton::getDb();$name="";
        $r=$db->query("SELECT `name` FROM `CASH` WHERE `id`='$cash_id' LIMIT 1;");$n=$db->num_rows($r);
        if ($n==1){$name=$db->result($r,0,"name");}
        return $name;
    }

	function getArticlesPhoto($art_id) { $db=DbSingleton::getTokoDb();
        $r=$db->query("SELECT * FROM `T2_PHOTOS` WHERE `ART_ID`='$art_id' AND `ACTIVE`=1 ORDER BY `PHOTO_NAME` ASC;"); $n=$db->num_rows($r); $link="";
        if ($n>7) $n=7; // max count of images
        for ($i=1;$i<=$n;$i++) {
            $photo_name = $db->result($r, $i-1, "PHOTO_NAME");
            $link.="https://toko.ua/uploads/images/catalogue/$photo_name";
            if ($i!=$n) $link.="|";
        }
        return $link;
    }

    function getArticlesDelivery($tpoint_id,$storage_id) { $db = DbSingleton::getTokoDb();
        $week_day=date("N"); $cur_time=date("H:i:s");
        $r=$db->query("SELECT `delivery_days`, `week_day`, `time_from_del`, `time_to_del` 
        FROM `T_POINT_DELIVERY_TIME`
        WHERE `status`='1' AND `tpoint_id`='$tpoint_id' AND `storage_id`='$storage_id' AND `week_day`='$week_day' AND `time_from`<='$cur_time' 
        AND `time_to`>='$cur_time' ORDER BY `delivery_days` ASC LIMIT 1;");$n=$db->num_rows($r);
        if ($n==1){ $delivery_days=$db->result($r,0,"delivery_days"); } else $delivery_days=0;
        return $delivery_days;
    }

    function getSupplList($art_id) { $dbt=DbSingleton::getTokoDb();
        $r=$dbt->query("SELECT * FROM `T2_SUPPL_IMPORT` WHERE `art_id`='$art_id';"); $n=$dbt->num_rows($r); $suppl_list="";
        for ($i=1;$i<=$n;$i++) {
            $suppl_id=$dbt->result($r,$i-1,"suppl_id");
            $suppl_list.=$suppl_id;
            if ($i!=$n) $suppl_list.=",";
        }
        return $suppl_list;
    }

    function getArticlesClientsAll() {
        $arr1 = $this->getArticlesClientsData();
        $arr2 = $this->getSupplArticlesClientsData();
        $arr3 = array_merge($arr1,$arr2);
        return $arr3;
    }

    function getArticlesClientsData() { $db=DbSingleton::getDb(); $dbt=DbSingleton::getTokoDb();
        $client_id=930; $array=[];
        $r=$db->query("SELECT * FROM `A_CLIENTS_CONDITIONS` WHERE `client_id`='$client_id' LIMIT 1;");
        $tpoint_id=$db->result($r,0,"tpoint_id");

        $r=$dbt->query("SELECT * FROM `T_POINT_STORAGE` WHERE `tpoint_id`='$tpoint_id';"); $n=$dbt->num_rows($r); $storage_str="";
        for($i=1;$i<=$n;$i++) {
            $storage_id=$dbt->result($r,$i-1,"storage_id");
            $storage_str.="$storage_id";
            if ($i!=$n) $storage_str.=",";
        }

        $r=$dbt->query("SELECT * FROM `T_POINT_STORAGE` WHERE `tpoint_id`!='$tpoint_id';"); $n=$dbt->num_rows($r); $storage_none_str="";
        for($i=1;$i<=$n;$i++) {
            $storage_id=$dbt->result($r,$i-1,"storage_id");
            $storage_none_str.="$storage_id";
            if ($i!=$n) $storage_none_str.=",";
        }

        $r=$dbt->query("SELECT t2s.`ART_ID`, t2a.`ARTICLE_NR_DISPL`, t2b.`BRAND_NAME`, t2n.`NAME` as TITLE, t2n.`INFO` as DESCRIPTION
	    FROM `T2_ARTICLES_STRORAGE` t2s
			LEFT JOIN `T2_NAMES` t2n ON t2n.`ART_ID`=t2s.`ART_ID`
			LEFT JOIN `T2_ARTICLES` t2a ON t2a.`ART_ID`=t2s.`ART_ID`
			LEFT JOIN `T2_BRANDS` t2b ON t2a.`BRAND_ID`=t2b.`BRAND_ID`
		WHERE t2n.`LANG_ID`=16 GROUP BY t2a.`ART_ID`;"); $n=$dbt->num_rows($r);
        for ($i=1;$i<=$n;$i++) {
            $art_id=$dbt->result($r,$i-1,"ART_ID");
            $article_nr_displ=$dbt->result($r,$i-1,"ARTICLE_NR_DISPL");
            $brand_name=$dbt->result($r,$i-1,"BRAND_NAME");
            $price=$this->getArticlePrice($art_id);
            list($usd_to_uah,)=$this->getKoursData();
            $price*=$usd_to_uah; $price=round($price,2);
            $title=$dbt->result($r,$i-1,"TITLE");
            $descr=$dbt->result($r,$i-1,"DESCRIPTION");
            $descr=trim($descr," ");
            $descr=trim($descr,"\n");
            $descr=trim($descr,"\r");
            $descr=str_replace("\n", "", $descr);
            $descr=str_replace("\r", "", $descr);
            $images=$this->getArticlesPhoto($art_id);

            $article_nr_displ = $this->getClearArticle($article_nr_displ);
            $title = mb_convert_encoding($title, "utf-8", "windows-1251"); $title=$this->remove_utf8_bom($title);
            $descr = mb_convert_encoding($descr, "utf-8", "windows-1251"); $descr=$this->remove_utf8_bom($descr);

            $r2=$dbt->query("SELECT SUM(`AMOUNT`) as storage_amount FROM `T2_ARTICLES_STRORAGE` 
            WHERE `ART_ID`='$art_id' AND `STORAGE_ID` IN ($storage_str) GROUP BY `ART_ID`;"); $n2=$dbt->num_rows($r2);
            if ($n2>0) {
                $amount=$dbt->result($r2,0,"storage_amount");
                $delivery=$this->getMinDeliveryTime($tpoint_id,$storage_str);
            } else {
                $r3=$dbt->query("SELECT SUM(`AMOUNT`) as storage_amount FROM `T2_ARTICLES_STRORAGE` 
                WHERE `ART_ID`='$art_id' AND `STORAGE_ID` IN ($storage_none_str) GROUP BY `ART_ID`;"); $n3=$dbt->num_rows($r3);
                if ($n3>0) {
                    $amount=$dbt->result($r3,0,"storage_amount");
                    $delivery=$this->getMinDeliveryTime($tpoint_id,$storage_none_str);
                } else {
                    $amount=0;$delivery=0;
                }
            }

            if ($amount>=10) $amount=10;
            $amount=intval($amount);

            if ($amount>0 && $price>0) $array[$i]=array("$article_nr_displ","$brand_name","$amount","$delivery","$price","$title","$descr","$images");
        }
        return $array;
    }

    function getSupplArticlesClientsData() { $db=DbSingleton::getDb(); $dbt=DbSingleton::getTokoDb();
        $client_id=930; $array=[];
        $r=$db->query("SELECT * FROM `A_CLIENTS_CONDITIONS` WHERE `client_id`='$client_id' LIMIT 1;");
        $tpoint_id=$db->result($r,0,"tpoint_id");

        $r=$dbt->query("SELECT * FROM `T_POINT_STORAGE` WHERE `tpoint_id`='$tpoint_id';"); $n=$dbt->num_rows($r); $storage_str="";
        for($i=1;$i<=$n;$i++) {
            $storage_id=$dbt->result($r,$i-1,"storage_id");
            $storage_str.="$storage_id";
            if ($i!=$n) $storage_str.=",";
        }

        $r=$dbt->query("SELECT * FROM `T_POINT_STORAGE` WHERE `tpoint_id`!='$tpoint_id';"); $n=$dbt->num_rows($r); $storage_none_str="";
        for($i=1;$i<=$n;$i++) {
            $storage_id=$dbt->result($r,$i-1,"storage_id");
            $storage_none_str.="$storage_id";
            if ($i!=$n) $storage_none_str.=",";
        }

        $r=$dbt->query("SELECT t2si.`art_id`, t2si.`suppl_id`, t2si.`suppl_index`, t2si.`brand`, t2n.`NAME` as `TITLE`, t2n.`INFO` as `DESCRIPTION`, 
        t2si.`client_storage_id`, SUM(t2si.`stock_suppl`) as `storage_amount`
        FROM `T2_SUPPL_IMPORT` t2si
            LEFT JOIN `T2_NAMES` t2n ON (t2n.`ART_ID`=t2si.`art_id`)
        WHERE t2n.`LANG_ID`=16 AND t2si.`art_id`>0 AND t2si.`status`=1
        GROUP BY t2si.`art_id`;"); $n=$dbt->num_rows($r);
        for ($i=1;$i<=$n;$i++) {
            $art_id=$dbt->result($r,$i-1,"art_id");
            $suppl_id=$dbt->result($r,$i-1,"suppl_id");
            $suppl_storage_id=$dbt->result($r,$i-1,"client_storage_id");
            $article_nr_displ=$dbt->result($r,$i-1,"suppl_index");
            $brand_name=$dbt->result($r,$i-1,"brand");
            $price=$this->getArticleSupplPrice($art_id,$suppl_id,$suppl_storage_id);
            list($usd_to_uah,)=$this->getKoursData();
            $price*=$usd_to_uah; $price=round($price,2);
            $title=$dbt->result($r,$i-1,"TITLE");
            $descr=$dbt->result($r,$i-1,"DESCRIPTION");
            $descr=trim($descr," ");
            $descr=trim($descr,"\n");
            $descr=trim($descr,"\r");
            $descr=str_replace("\n", "", $descr);
            $descr=str_replace("\r", "", $descr);
            $images=$this->getArticlesPhoto($art_id);
            $amount=$dbt->result($r,$i-1,"storage_amount");

            $article_nr_displ = $this->getClearArticle($article_nr_displ);
            $title = mb_convert_encoding($title, "utf-8", "windows-1251"); $title=$this->remove_utf8_bom($title);
            $descr = mb_convert_encoding($descr, "utf-8", "windows-1251"); $descr=$this->remove_utf8_bom($descr);

            $suppl_list=$this->getSupplList($art_id);
            $delivery=$this->getMinDeliverySupplTime($tpoint_id,$suppl_list);

            if ($amount>=10) $amount=10;
            $amount=intval($amount);

            if ($amount>0 && $price>0) $array[$i]=array("$article_nr_displ","$brand_name","$amount","$delivery","$price","$title","$descr","$images");
        }
        return $array;
    }

    function getArticlePrice($art_id) { $dbt = DbSingleton::getTokoDb();
        $client_id=930; $price=0;
        list($price_lvl,$margin_price_lvl,,,)=$this->getDpClientPriceLevels($client_id);
        $r=$dbt->query("SELECT t2apr.price_$price_lvl, t2apr.minMarkup, t2aps.OPER_PRICE, t2si.price_usd as suppl_price_usd
        FROM `T2_ARTICLES` t2a 
            LEFT OUTER JOIN `T2_ARTICLES_PRICE_RATING` t2apr ON (t2apr.art_id=t2a.ART_ID)
            LEFT OUTER JOIN `T2_ARTICLES_PRICE_STOCK` t2aps ON (t2aps.ART_ID=t2a.ART_ID)
            LEFT OUTER JOIN `T2_SUPPL_IMPORT` t2si ON (t2si.art_id=t2a.ART_ID)
        WHERE t2a.ART_ID='$art_id' AND t2apr.in_use='1' LIMIT 1;"); $n=$dbt->num_rows($r);
        if ($n==1) {
            $price=$dbt->result($r,0,"price_".$price_lvl);
            $minMarkup=$dbt->result($r,0,"minMarkup");
            $OPER_PRICE=$dbt->result($r,0,"OPER_PRICE");
            $float_price=floatval($price);
            if ($margin_price_lvl>0){
                $price=$float_price+round($price*$margin_price_lvl/100,2);
            }
            if ($margin_price_lvl<0){
                $price_minus=$price+($price*$margin_price_lvl/100);
                $oper_limit=$OPER_PRICE+($OPER_PRICE*$minMarkup/100);
                if ($price_minus>=$oper_limit) $price=$price_minus;
                else if ($oper_limit>=$price) true;
                else $price=$oper_limit;
            }
        }
        return $price;
    }

    function getArticleSupplPrice($art_id,$suppl_id,$suppl_storage_id) { $dbt = DbSingleton::getTokoDb();
        $tpoint=2; $price=0; $client_id=930;
        list(,,$price_suppl_lvl,$margin_price_suppl_lvl,$client_vat)=$this->getDpClientPriceLevels($client_id);
        $r=$dbt->query("SELECT t2si.price_usd 
        FROM `T2_ARTICLES` t2a 
            LEFT OUTER JOIN `T2_SUPPL_ARTICLES_IMPORT` t2sai ON (t2sai.art_id=t2a.ART_ID)
            LEFT OUTER JOIN `T2_SUPPL_IMPORT` t2si ON (t2si.art_id=t2sai.art_id AND t2si.suppl_id=t2sai.suppl_id AND t2si.status=1)
        WHERE t2a.ART_ID='$art_id' AND t2sai.suppl_id='$suppl_id' LIMIT 1;"); $n=$dbt->num_rows($r);
        if ($n==1){
            $suppl_price_usd=floatval($dbt->result($r,0,"price_usd"));
            list($price_in_vat,$show_in_vat,$price_add_vat)=$this->getSupplVatConditions($suppl_id);
            $price_suppl=$suppl_price_usd;
            list($suppl_margin_fm,$suppl_delivery_fm,$suppl_margin2_fm)=$this->getTpointSupplFm($tpoint,$suppl_id,$suppl_storage_id,$price_suppl,$price_suppl_lvl);
            if ($suppl_margin_fm>0){
                $price=($price_suppl+$price_suppl*$suppl_margin_fm/100)-$price_suppl;
                if ($price>$suppl_delivery_fm){
                    $price=($price_suppl+$price_suppl*$suppl_margin_fm/100);
                }
                if ($price<=$suppl_delivery_fm){
                    $price=$price_suppl+$price_suppl*$suppl_margin2_fm/100+$suppl_delivery_fm;
                }
                if ($margin_price_suppl_lvl>0 && $margin_price_suppl_lvl!=""){
                    $price=$price+$price*$margin_price_suppl_lvl/100;
                }
                if ($client_vat==1){
                    if ($price_in_vat==0 && $show_in_vat==1 && $price_add_vat==1){
                        $price=$price+$price*20/100;
                    }
                    if ($price_in_vat==0 && $show_in_vat==0){
                        $price=0;
                    }
                }
            }
            $price=round($price,2);
        }
        return $price;
    }

    function getDpClientPriceLevels($client) { $db = DbSingleton::getDb();
        $price_lvl=$margin_price_lvl=$price_suppl_lvl=$margin_price_suppl_lvl=$client_vat=0;
        $r=$db->query("SELECT * FROM `A_CLIENTS_CONDITIONS` WHERE `client_id`='$client' LIMIT 1;"); $n=$db->num_rows($r);
        if ($n==1){
            $price_lvl=$db->result($r,0,"price_lvl"); $price_lvl++;
            $margin_price_lvl=$db->result($r,0,"margin_price_lvl");
            $price_suppl_lvl=$db->result($r,0,"price_suppl_lvl"); $price_suppl_lvl++;
            $margin_price_suppl_lvl=$db->result($r,0,"margin_price_suppl_lvl");
            $client_vat=$db->result($r,0,"client_vat");
        }
        return array($price_lvl,$margin_price_lvl,$price_suppl_lvl,$margin_price_suppl_lvl,$client_vat);
    }

    function getSupplVatConditions($suppl_id) { $db = DbSingleton::getDb();
        $price_in_vat=$show_in_vat=$price_add_vat=0;
        $r=$db->query("SELECT * FROM `A_CLIENTS_VAT_CONDITIONS` WHERE `client_id`='$suppl_id' LIMIT 1;"); $n=$db->num_rows($r);
        if ($n==1){
            $price_in_vat=$db->result($r,0,"price_in_vat");
            $show_in_vat=$db->result($r,0,"show_in_vat");
            $price_add_vat=$db->result($r,0,"price_add_vat");
        }
        return array($price_in_vat,$show_in_vat,$price_add_vat);
    }

    function getTpointSupplFm($tpoint_id,$suppl_id,$suppl_storage_id,$price_suppl,$price_suppl_lvl) { $db = DbSingleton::getTokoDb();
        $margin=$delivery=$margin2=0;
        $r=$db->query("SELECT `margin`, `delivery`, `margin2` FROM `T_POINT_SUPPL_FM` 
        WHERE `tpoint_id`='$tpoint_id' AND `suppl_id`='$suppl_id' AND `suppl_storage_id`='$suppl_storage_id' AND `price_from`<='$price_suppl' 
        AND `price_to`>='$price_suppl' AND `price_rating_id`='$price_suppl_lvl' LIMIT 1;");$n=$db->num_rows($r);
        if ($n==1){
            $margin=$db->result($r,0,"margin");
            $delivery=$db->result($r,0,"delivery");
            $margin2=$db->result($r,0,"margin2");
        }
        return array($margin,$delivery,$margin2);
    }

    function getMinDeliveryTime($tpoint_id,$storage_str) { $db = DbSingleton::getTokoDb();
        $cur_time = "09:00:00";
        $r=$db->query("SELECT MIN(`delivery_days`) as min_delivery FROM `T_POINT_DELIVERY_TIME`
        WHERE `status`='1' AND `tpoint_id`='$tpoint_id' AND `storage_id` IN ($storage_str) AND `week_day`=WEEKDAY(CURDATE())+1 AND `time_from`<='$cur_time' 
        AND `time_to`>='$cur_time' ORDER BY `delivery_days` ASC LIMIT 1;");$n=$db->num_rows($r);
        if ($n==1) { $delivery_days=$db->result($r,0,"min_delivery"); } else $delivery_days=0;
        return $delivery_days;
    }

    function getMinDeliverySupplTime($tpoint_id,$suppl_list) { $db = DbSingleton::getTokoDb();
        $cur_time = "09:00:00";
        $r=$db->query("SELECT MIN(`delivery_days`) as min_delivery FROM `T_POINT_SUPPL_DELIVERY_TIME`
        WHERE `status`='1' AND `tpoint_id`='$tpoint_id' AND `suppl_id` IN ($suppl_list) AND `week_day`=WEEKDAY(CURDATE())+1 AND `time_from`<='$cur_time'
        AND `time_to`>='$cur_time' ORDER BY `delivery_days` ASC LIMIT 1;");$n=$db->num_rows($r);
        if ($n==1) { $delivery_days=$db->result($r,0,"min_delivery"); } else $delivery_days=0;
        return $delivery_days;
    }

    function remove_utf8_bom($text) {
        $bom = pack('H*','EFBBBF');
        $text = preg_replace("/^$bom/", '', $text);
        return $text;
    }

    function getClearArticle($article_nr_displ) {
        $article_nr_displ=str_replace(str_split(" ,.+-/\'()&"),"",$article_nr_displ);
        return $article_nr_displ;
    }

}