<?php

class storage_stock {
	
	function getStorageName($sel_id){$db=DbSingleton::getTokoDb();$name="";
		$r=$db->query("select name from `STORAGE` where status='1' and id='$sel_id' limit 0,1;");$n=$db->num_rows($r);
		if ($n==1){$name=$db->result($r,0,"name");}
		return $name;	
	}
	
	function getStorageCellsName($cell_id,$storage_id) {
		$db=DbSingleton::getTokoDb();$name="";
		$r=$db->query("select cell_value from `STORAGE_CELLS` where status='1' and id='$cell_id' and storage_id='$storage_id' limit 1;");
		$n=$db->num_rows($r);
		if ($n==1){$name=$db->result($r,0,"cell_value");}
		return $name;	
	}
	
	function getArtDispl($art_id) {$db=DbSingleton::getTokoDb();$ARTICLE_NR_DISPL="";
		$r=$db->query("select ARTICLE_NR_DISPL from `T2_ARTICLES` where ART_ID='$art_id' limit 1;");$n=$db->num_rows($r);
		if ($n==1){$ARTICLE_NR_DISPL=$db->result($r,0,"ARTICLE_NR_DISPL");}
		return $ARTICLE_NR_DISPL;	
	}
	
	function showStorageStock() { $db=DbSingleton::getTokoDb(); $list=$list2="";
		$form_htm=RD."/tpl/storage_stock.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
		$r=$db->query("
			SELECT st.ART_ID, st.STORAGE_ID, (st.AMOUNT+st.RESERV_AMOUNT) as SUMM_STORAGE,    

			(SELECT sum(cl.AMOUNT+cl.RESERV_AMOUNT)
			FROM T2_ARTICLES_STRORAGE_CELLS cl 
			WHERE cl.ART_ID=st.ART_ID and cl.STORAGE_ID=st.STORAGE_ID) SUMM_CELL

			FROM T2_ARTICLES_STRORAGE st

			WHERE (st.AMOUNT+st.RESERV_AMOUNT)!=(SELECT sum(cl.AMOUNT+cl.RESERV_AMOUNT)
			 									FROM T2_ARTICLES_STRORAGE_CELLS cl 
			 									WHERE cl.ART_ID=st.ART_ID and cl.STORAGE_ID=st.STORAGE_ID)"); $n=$db->num_rows($r);
								 
		for ($i=1;$i<=$n;$i++){
			$ART_ID=$db->result($r,$i-1,"ART_ID"); $ARTICLE_NR_DISPL=$this->getArtDispl($ART_ID);
			$STORAGE_ID=$db->result($r,$i-1,"STORAGE_ID"); $STORAGE_NAME=$this->getStorageName($STORAGE_ID);
			$SUMM_STORAGE=$db->result($r,$i-1,"SUMM_STORAGE");
			$SUMM_CELL=$db->result($r,$i-1,"SUMM_CELL");
			$list.="<tr>
				<td>$i</td>
				<td>$ART_ID</td>
				<td>$ARTICLE_NR_DISPL</td>
				<td>$STORAGE_NAME</td>
				<td>$SUMM_STORAGE</td>
				<td>$SUMM_CELL</td>
			</tr>";
		}		
								 
		$r=$db->query("
			SELECT ps.ART_ID, ps.GENERAL_STOCK, 

			(SELECT sum(st.AMOUNT+st.RESERV_AMOUNT)
			FROM T2_ARTICLES_STRORAGE st
			WHERE st.ART_ID=ps.ART_ID) SUMM_STORAGE

			FROM T2_ARTICLES_PRICE_STOCK ps

			WHERE ps.GENERAL_STOCK!=(SELECT sum(st.AMOUNT+st.RESERV_AMOUNT)
			 						FROM T2_ARTICLES_STRORAGE st
			 						WHERE st.ART_ID=ps.ART_ID)
		"); $n=$db->num_rows($r);
								 
		for ($i=1;$i<=$n;$i++){
			$ART_ID=$db->result($r,$i-1,"ART_ID"); $ARTICLE_NR_DISPL=$this->getArtDispl($ART_ID);
			$GENERAL_STOCK=$db->result($r,$i-1,"GENERAL_STOCK");
			$SUMM_STORAGE=$db->result($r,$i-1,"SUMM_STORAGE");
			$list2.="<tr>
				<td>$i</td>
				<td>$ART_ID</td>
				<td>$ARTICLE_NR_DISPL</td>
				<td>$GENERAL_STOCK</td>
				<td>$SUMM_STORAGE</td>
			</tr>";
		}
		$form=str_replace("{storage_stock_range}",$list,$form);								  
		$form=str_replace("{storage_stock_general_range}",$list2,$form);								  
		return $form;
	}
	
}