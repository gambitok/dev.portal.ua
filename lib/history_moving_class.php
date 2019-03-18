<?php

class HistoryMovingClass {
	
	function loadHistoryList() { $db=new db; $gmanual=new gmanual; $income=new income; $list="";
		$r=$db->query("
		
		select j.*, js.* from J_MOVING_STR js 
		
		left outer join J_MOVING j.id=js.jmoving_id
		
		where js.art_id='100000378';
			
		"); $n=$db->num_rows($r);
								
		for ($i=1;$i<=$n;$i++){
			$type_name="Приходная накладная";
			
			$id=$db->result($r,$i-1,"id");
			$data=$db->result($r,$i-1,"data");
			
			$prefix=$db->result($r,$i-1,"prefix");
			$doc_nom=$db->result($r,$i-1,"doc_nom");
			
			$doc_name="$prefix-$doc_nom";
			
			$storage_id_from=$db->result($r,$i-1,"storage_id_from"); $storage_name_from=$this->getStorageName($storage_id_from);
			$storage_id_to=$db->result($r,$i-1,"storage_id_to"); $storage_name_from=$this->getStorageName($storage_id_from);
			
			$amount_moved=$db->result($r,$i-1,"amount");
								
			$list.="
			<tr>
				<td>$id</td>
				<td>$data</td>
				<td>$type_name</td>
				<td>$doc_name</td>
				<td>$storage_name_from</td>
				<td>$storage_name_to</td>
				<td>$amount_come</td>
				<td>$amount_gone</td>
				<td>$amount_reserved</td>
				<td>$amount_moved</td>
				<td>$address</td>
				<td>$comment</td>
			</tr>";
		}
		return $list;
	}
	
	function getStorageName($storage_id) { $db=new db;
		$r=$db->query("select name from STORAGE where id='$storage_id' limit 1;");
	  	$name=$db->result($r,0,"name");
		return $name;
	}
	
}