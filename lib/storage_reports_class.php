<?php

class storage_reports {
	
	function exportStorageReports($storages) { $db=DbSingleton::getTokoDb(); $list=[];
		if($storages=="" || $storages==0) $storages_list=$this->getStoragesIds(); else $storages_list=$storages;
		$storages_array=explode(",",$storages_list);
		$list[0]=array("ART_ID","Brand","Index");
		foreach ($storages_array as $storage_id) {
			$storage_name=$this->getStorageName($storage_id);
			array_push($list[0],"$storage_name - AMOUNT");	
			array_push($list[0],"$storage_name - RESERV");	
		}

		$r=$db->query("select t2s.STORAGE_ID, t2s.AMOUNT, t2s.RESERV_AMOUNT, t2s.ART_ID, t2a.ARTICLE_NR_DISPL, t2a.BRAND_ID 
		from T2_ARTICLES_STRORAGE t2s
		    left outer join T2_ARTICLES t2a on t2a.ART_ID=t2s.ART_ID;"); $n=$db->num_rows($r);
		for ($i=1;$i<=$n;$i++){
			$art_id=$db->result($r,$i-1,"ART_ID"); 																			  
			$brand_id=$db->result($r,$i-1,"BRAND_ID"); $brand=$this->getBrandName($brand_id);																		  
			$article=$db->result($r,$i-1,"ARTICLE_NR_DISPL");
			$list[$i]=array($art_id,$brand,$article);
			$AMOUNT=$RESERV_AMOUNT=0;
			foreach ($storages_array as $storage_id) {
				$rs=$db->query("select * from T2_ARTICLES_STRORAGE where ART_ID='$art_id' and STORAGE_ID='$storage_id' limit 1;"); $ns=$db->num_rows($rs);
				if ($ns>0) {
					$AMOUNT=$db->result($rs,0,"AMOUNT");
					$RESERV_AMOUNT=$db->result($rs,0,"RESERV_AMOUNT");
				}
				array_push($list[$i],$AMOUNT);
				array_push($list[$i],$RESERV_AMOUNT);
			}
		}
		return $list;
	}
	
	function getBrandName($id){$db=DbSingleton::getTokoDb();
		$r=$db->query("select BRAND_NAME from T2_BRANDS where BRAND_ID='$id' limit 1;");
		$name=$db->result($r,0,"BRAND_NAME");	
		return $name;	
	}
	
	function getStorageName($storage_id){$db=DbSingleton::getTokoDb(); $slave=new slave;
		$r=$db->query("select name from STORAGE where id='$storage_id' and status=1 limit 1");
	  	$name=$db->result($r,0,"name");
		$name=$slave->translit($name);
	  	return $name;
	}
	
	function getStorages() {$db=DbSingleton::getTokoDb(); $list="";
		$r=$db->query("select * from STORAGE where status=1;"); $n=$db->num_rows($r); 					
		for ($i=1;$i<=$n;$i++){
			$id=$db->result($r,$i-1,"id");
			$name=$db->result($r,$i-1,"name");
			$list.="
			<option value='$id'>$id - $name</option>";
		}
		return $list;
	}
	
	function getStoragesIds() {$db=DbSingleton::getTokoDb(); $storages=[];
		$r=$db->query("select * from STORAGE where status=1;"); $n=$db->num_rows($r); 					
		for ($i=1;$i<=$n;$i++){
			$id=$db->result($r,$i-1,"id");
			array_push($storages,$id);
		}
		$storages=implode(",",$storages);
		return $storages;
	}

}
