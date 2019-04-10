<?php

class unknown_numbers {
	
	function showSupplList() { $db=DbSingleton::getDb();$list="";
		$r=$db->query("select cl.* from A_CLIENTS cl
			left outer join A_CLIENTS_CATEGORY cc on cc.client_id=cl.id
			left outer join A_CLIENTS_STORAGE cs on cs.client_id=cl.id
		where cc.category_id=2 and cs.id>0 group by cs.client_id;"); $n=$db->num_rows($r);						
		for ($i=1;$i<=$n;$i++){
			$suppl_id=$db->result($r,$i-1,"id");
			$suppl_name=$db->result($r,$i-1,"name");
			$list.="<option value='$suppl_id'>$suppl_name</option>";
		}	
		return $list;
	}
	
	function getCashName($cash_id){$db=DbSingleton::getDb();$name="";
		$r=$db->query("select name from CASH where id ='$cash_id' limit 1;");$n=$db->num_rows($r);
		if ($n==1){$name=$db->result($r,0,"name");}
		return $name;
	}
	
	function showNumbersList($suppl_id) { $db=DbSingleton::getTokoDb();$list="";
		$r=$db->query("select * from T2_SUPPL_IMPORT where status=1 and art_id=0 and suppl_id=$suppl_id group by suppl_index, brand;"); $n=$db->num_rows($r);				
		for ($i=1;$i<=$n;$i++){
			$suppl_index=$db->result($r,$i-1,"suppl_index");
			$brand=$db->result($r,$i-1,"brand");
			$price_suppl=$db->result($r,$i-1,"price_suppl");
			$cash_id=$db->result($r,$i-1,"cash_id"); $cash=$this->getCashName($cash_id);
			$kours_usd=$db->result($r,$i-1,"kours_usd");
			$price_usd=$db->result($r,$i-1,"price_usd");
			$client_storage_id=$db->result($r,$i-1,"client_storage_id");
			$stock_suppl=$db->result($r,$i-1,"stock_suppl");
			$data_update=$db->result($r,$i-1,"data_update");
			$return_delay=$db->result($r,$i-1,"return_delay");
			$warranty_info=$db->result($r,$i-1,"warranty_info");
			$list.="<tr>
				<td>$i</td>
				<td>$suppl_index</td>
				<td>$brand</td>
				<th>$price_suppl</th>
				<th>$cash</th>
				<th>$kours_usd</th>
				<th>$price_usd</th>
				<th>$client_storage_id</th>
				<th>$stock_suppl</th>
				<th>$data_update</th>
				<th>$return_delay</th>
				<th>$warranty_info</th>
			</tr>";
		}						
		return $list;
	}

}