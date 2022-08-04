<?php

class storage_reports {

    function getBrandName($id){$db=DbSingleton::getTokoDb();
        $r=$db->query("SELECT `BRAND_NAME` FROM `T2_BRANDS` WHERE `BRAND_ID`='$id' LIMIT 1;");
        return $db->result($r,0,"BRAND_NAME");
    }

    function getStorages() {$db=DbSingleton::getTokoDb();
        $list="";
        $r=$db->query("SELECT * FROM `STORAGE` WHERE `status`=1;");
        $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"name");
            $list.="
			<option value='$id'>$id - $name</option>";
        }
        return $list;
    }

    function getStoragesIds() {$db=DbSingleton::getTokoDb();
        $storages=[];
        $r=$db->query("SELECT * FROM `STORAGE` WHERE `status`=1;");
        $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            array_push($storages,$id);
        }
        $storages=implode(",",$storages);
        return $storages;
    }
	
	function exportStorageReports($storages) { $db=DbSingleton::getTokoDb();
	    $list=[]; $storage=new storage;
		if($storages=="" || $storages==0) $storages_list=$this->getStoragesIds(); else $storages_list=$storages;
		$storages_array=explode(",",$storages_list);
		$list[0]=array("ART_ID","Brand","Index");
		foreach ($storages_array as $storage_id) {
			$storage_name=$storage->getStorageName($storage_id);
			array_push($list[0],"$storage_name - AMOUNT");	
			array_push($list[0],"$storage_name - RESERV");	
		}

		$r=$db->query("SELECT t2s.STORAGE_ID, t2s.AMOUNT, t2s.RESERV_AMOUNT, t2s.ART_ID, t2a.ARTICLE_NR_DISPL, t2a.BRAND_ID 
		FROM `T2_ARTICLES_STRORAGE` t2s
		    LEFT OUTER JOIN `T2_ARTICLES` t2a on t2a.ART_ID=t2s.ART_ID;");
		$n=$db->num_rows($r);
		for ($i=1;$i<=$n;$i++){
			$art_id=$db->result($r,$i-1,"ART_ID"); 																			  
			$brand_id=$db->result($r,$i-1,"BRAND_ID");
			$brand=$this->getBrandName($brand_id);
			$article=$db->result($r,$i-1,"ARTICLE_NR_DISPL");
			$list[$i]=array($art_id,$brand,$article);
			$AMOUNT=$RESERV_AMOUNT=0;
			foreach ($storages_array as $storage_id) {
				$rs=$db->query("SELECT * FROM `T2_ARTICLES_STRORAGE` WHERE `ART_ID`='$art_id' AND `STORAGE_ID`='$storage_id' LIMIT 1;");
				$ns=$db->num_rows($rs);
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

}
