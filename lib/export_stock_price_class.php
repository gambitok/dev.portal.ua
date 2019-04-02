<?php

class export_stock_price {
	
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
	
	function exportPrices() {
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=export_prices.csv'); ob_clean();
		$output = fopen('php://output', 'w');
		fputcsv($output, array("ART_ID","ARTICLE_NR_DISPL","ARTICLE_NR_SEARCH","BRAND_ID","BRAND_NAME","PRICE"),$delimiter = ';');
		$array=$this->getArticlesPriceData(); 
		foreach ($array as $fields) {
			fputcsv($output,$fields,$delimiter = ';');
		}
		exit(0);
	}
	
	function getArticlesStockData() {$db=DbSingleton::getTokoDb(); $array=[];
		$r=$db->query("SELECT `T2_ARTICLES`.`ART_ID`, `T2_ARTICLES`.`ARTICLE_NR_DISPL` as `INDEX`, `T2_BRANDS`.`BRAND_NAME` as `BRAND`, `T2_ARTICLES_STRORAGE`.`AMOUNT`, `T2_ARTICLES_STRORAGE`.`RESERV_AMOUNT`, `STORAGE`.`name` as `SKLAD`, `STORAGE`.`full_name`, `T2_INNER_CROSS`.`CROSS`, `T2_ARTICLES_UNIV_NUMBER`.`UNIV_NUMBER`
		FROM `T2_ARTICLES_STRORAGE`
			left join `T2_ARTICLES` on `T2_ARTICLES_STRORAGE`.`ART_ID`=`T2_ARTICLES`.`ART_ID`
			left join `T2_BRANDS` on `T2_ARTICLES`.`BRAND_ID`=`T2_BRANDS`.`BRAND_ID`
			left join `STORAGE` on `STORAGE`.`id`=`T2_ARTICLES_STRORAGE`.`STORAGE_ID`
			left join `T2_INNER_CROSS` ON `T2_ARTICLES`.`ART_ID`=`T2_INNER_CROSS`.`ART_ID`
			left join `T2_ARTICLES_UNIV_NUMBER` ON `T2_ARTICLES`.`ART_ID`=`T2_ARTICLES_UNIV_NUMBER`.`ART_ID`
		ORDER BY  `T2_BRANDS`.`BRAND_NAME` ASC, `T2_ARTICLES`.`ARTICLE_NR_DISPL` ASC");
		$n=$db->num_rows($r);
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
	
	function getArticlesPriceData() {$db=DbSingleton::getTokoDb(); $array=[];
		$r=$db->query("SELECT `T2_ARTICLES`.*, `T2_BRANDS`.`BRAND_NAME`, `T2_ARTICLES_PRICE_RATING`.`price_1` 
		FROM `T2_ARTICLES_PRICE_RATING` 
			LEFT JOIN `T2_ARTICLES` ON `T2_ARTICLES_PRICE_RATING`.`art_id`=`T2_ARTICLES`.`art_id` 
			LEFT JOIN `T2_BRANDS` ON `T2_ARTICLES`.`BRAND_ID`=`T2_BRANDS`.`BRAND_ID`
		WHERE `T2_ARTICLES_PRICE_RATING`.`in_use`=1");
		$n=$db->num_rows($r);
		for ($i=1;$i<=$n;$i++){
			$art_id=$db->result($r,$i-1,"ART_ID");
			$art_displ=$db->result($r,$i-1,"ARTICLE_NR_DISPL");
			$art_search=$db->result($r,$i-1,"ARTICLE_NR_SEARCH");
			$brand_id=$db->result($r,$i-1,"BRAND_ID");
			$brand=$db->result($r,$i-1,"BRAND_NAME");
			$price=$db->result($r,$i-1,"price_1");
			$price=str_replace(".",",",$price);

			$array[$i]=array($art_id,$art_displ,$art_search,$brand_id,$brand,$price);
		}
		return $array;
	}
	
}