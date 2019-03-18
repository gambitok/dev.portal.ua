<?php

class group_tree {
	
	function addStrHeader($str_id,$head_id) {$db=DbSingleton::getTokoDb();
		$db->query("insert into T2_GROUP_TREE_CROSS (STR_ID, HEAD_ID)
		values ('$str_id', '$head_id');");
		return $true;	
	}
	
	function dropStrHeader($str_id,$head_id) {$db=DbSingleton::getTokoDb();
		$db->query("delete from T2_GROUP_TREE_CROSS 
		where STR_ID='$str_id' and HEAD_ID='$head_id';");
		return $true;	
	}
	
	function getStrHeaders($str_id) {$db=DbSingleton::getTokoDb(); $list="<ul>";
		$r=$db->query("select * from T2_GROUP_TREE_CROSS where STR_ID='$str_id';"); $n=$db->num_rows($r); 
        for ($i=1;$i<=$n;$i++){
            $HEAD_ID=$db->result($r,$i-1,"HEAD_ID");
			$r2=$db->query("select * from T2_GROUP_TREE_HEAD where HEAD_ID='$HEAD_ID' and LNG_ID=16 limit 1;");
			$TEX_TEXT=$db->result($r2,0,"TEX_TEXT");
			$list.="<li><a onclick='dropStrHeader($HEAD_ID);'>$HEAD_ID. $TEX_TEXT <i class='fa fa-times'></i></a></li>";
		}
		$list.="</ul>";
		if ($n==0) $list="������ �� �������";
		return $list;
	}
	
	function getStrHeadersList($str_id) {$db=DbSingleton::getTokoDb(); $list="<option value='0'>-�� �������-</option>"; $headers=[];
		$r=$db->query("select * from T2_GROUP_TREE_CROSS where STR_ID='$str_id'"); $n=$db->num_rows($r); 
		for ($i=1;$i<=$n;$i++){
			$HEAD_ID=$db->result($r,$i-1,"HEAD_ID");
			array_push($headers,$HEAD_ID);
		}
		$headers_str=implode(",",$headers);
		if ($headers_str!="") $where="and HEAD_ID not in ($headers_str)"; else $where="";
										 
		$r=$db->query("select * from T2_GROUP_TREE_HEAD where LNG_ID=16 $where;"); $n=$db->num_rows($r); 
        for ($i=1;$i<=$n;$i++){
            $HEAD_ID=$db->result($r,$i-1,"HEAD_ID");
            $TEX_TEXT=$db->result($r,$i-1,"TEX_TEXT");
			$list.="<option value='$HEAD_ID'>$HEAD_ID - $TEX_TEXT</option>";
		}
		if ($n==0) $list="������ �� �������";
		return $list;
	}
	
	function showGroupTreeHeaders() {$db=DbSingleton::getTokoDb();
		$r=$db->query("select * from T2_GROUP_TREE_HEAD where LNG_ID=16;"); $n=$db->num_rows($r); 
		$list="<ul style='list-style:none; padding:0;'>";
        for ($i=1;$i<=$n;$i++){
            $HEAD_ID=$db->result($r,$i-1,"HEAD_ID");
            $TEX_TEXT=$db->result($r,$i-1,"TEX_TEXT");
			$header_list=$this->getGroupTreeStr($HEAD_ID);
			$list.="<li>
				<div class='tree-head pointer'><i class='fa fa-eye' onclick='showGroupTreeHeadCard($HEAD_ID)'></i> $HEAD_ID. $TEX_TEXT</div>
				<div class='tree-list dnone'>$header_list</div>
			</li>";
		}
		$list.="</ul>";
		return $list;
	}
	
	function getGroupTreeStr($HEAD_ID) {$db=DbSingleton::getTokoDb();
		$r=$db->query("select cs.STR_ID, tr.DISP_TEXT from T2_GROUP_TREE_CROSS cs 
		left outer join T2_GROUP_TREE tr on tr.STR_ID=cs.STR_ID and tr.LNG_ID=16
		where cs.HEAD_ID='$HEAD_ID';"); $n=$db->num_rows($r); 
		$list="<ul>";
		for ($i=1;$i<=$n;$i++){
            $STR_ID=$db->result($r,$i-1,"STR_ID");
            $DISP_TEXT=$db->result($r,$i-1,"DISP_TEXT");
			$list.="<li>$DISP_TEXT</li>";
		}
		$list.="</ul>";
		if ($n==0) $list="�����";
		return $list;
	}
	
	function showGroupTreeHead($head_id) {$db=DbSingleton::getTokoDb();								  
		if ($head_id==0 || $head_id=='0') {
			$r1=$db->query("select max(HEAD_ID) as max_head from T2_GROUP_TREE_HEAD;");
			$head_id=0+$db->result($r1,0,"max_head")+1;
			$disp_text_ru=$disp_text_ua=$disp_text_en="";
		}									  
		$form_htm=RD."/tpl/group_tree_head_card.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
		$r=$db->query("select * from T2_GROUP_TREE_HEAD where HEAD_ID='$head_id';"); $n=$db->num_rows($r); 							  
		for ($i=1;$i<=$n;$i++){
			$LNG_ID=$db->result($r,$i-1,"LNG_ID");
			$TEX_TEXT=$db->result($r,$i-1,"TEX_TEXT");
			if ($LNG_ID==16) { $disp_text_ru=$TEX_TEXT; }
			if ($LNG_ID==41) { $disp_text_ua=$TEX_TEXT; }
			if ($LNG_ID==4)  { $disp_text_en=$TEX_TEXT; }
		}									 
		$form=str_replace("{head_id}",$head_id,$form);								 
		$form=str_replace("{disp_text_ru}",$disp_text_ru,$form);
		$form=str_replace("{disp_text_ua}",$disp_text_ua,$form);
		$form=str_replace("{disp_text_en}",$disp_text_en,$form);						  
		return $form;
	}
	
	function saveGroupTreeHead($head_id, $disp_text_ru, $disp_text_ua, $disp_text_en) {$db=DbSingleton::getTokoDb();
		$answer=0; $err="������� ���������� �����!"; 
		if ($head_id>0){
			$r1=$db->query("select * from T2_GROUP_TREE_HEAD where `HEAD_ID`='$head_id' and `LNG_ID`=16;"); $n1=$db->num_rows($r1);
			if ($n1>0) { $db->query("update T2_GROUP_TREE_HEAD set `TEX_TEXT`='$disp_text_ru' where `HEAD_ID`='$head_id' and `LNG_ID`=16;"); } 
			else { $db->query("insert into T2_GROUP_TREE_HEAD (`HEAD_ID`,`TEX_TEXT`,`LNG_ID`) values ('$head_id', '$disp_text_ru', '16');"); }
			
			$r2=$db->query("select * from T2_GROUP_TREE_HEAD where `HEAD_ID`='$head_id' and `LNG_ID`=41;"); $n2=$db->num_rows($r2);
			if ($n2>0) { $db->query("update T2_GROUP_TREE_HEAD set `TEX_TEXT`='$disp_text_ua' where `HEAD_ID`='$head_id' and `LNG_ID`=41;"); } 
			else { $db->query("insert into T2_GROUP_TREE_HEAD (`HEAD_ID`,`TEX_TEXT`,`LNG_ID`) values ('$head_id', '$disp_text_ua', '41');"); }
			
			$r3=$db->query("select * from T2_GROUP_TREE_HEAD where `HEAD_ID`='$head_id' and `LNG_ID`=4;"); $n3=$db->num_rows($r3);
			if ($n3>0) { $db->query("update T2_GROUP_TREE_HEAD set `TEX_TEXT`='$disp_text_en' where `HEAD_ID`='$head_id' and `LNG_ID`=4;"); } 
			else { $db->query("insert into T2_GROUP_TREE_HEAD (`HEAD_ID`,`TEX_TEXT`,`LNG_ID`) values ('$head_id', '$disp_text_en', '4');"); }
			
			$answer=1;$err="";
		} 
		return array($answer,$err);
	}
	
	function dropGroupTreeHead($head_id) {$db=DbSingleton::getTokoDb();
		$answer=0; $err="������� ���������� �����!"; 
		if ($head_id>0) {
			$db->query("delete from T2_GROUP_TREE_HEAD where HEAD_ID='$head_id';");
			$answer=1;$err="";
		}
	  	return array($answer,$err);
	}
	
	function showGroupTree() {$db=DbSingleton::getTokoDb();
	  	$menu_det=$tree="";	
		$form_htm=RD."/tpl/group_tree.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
		$r=$db->query("select * from T2_GROUP_TREE where LNG_ID=16;"); $n=$db->num_rows($r); 
							  				  
		$menu_det.="<div class=\"input-group border0\">
		<input type=\"search\" id=\"my-search\" class=\"my-search\" placeholder=\"����� �� ����������\">
		</div><ul id=\"my-tree\" class=\"tf-tree\">";

        for ($i=1;$i<=$n;$i++){
            $str_id=$db->result($r,$i-1,"STR_ID");
            $str_id_parrent=$db->result($r,$i-1,"STR_ID_PARENT");if ($str_id_parrent==""){$str_id_parrent=0;}
            $str_level=$db->result($r,$i-1,"STR_LEVEL");
            $tex_text=$db->result($r,$i-1,"DISP_TEXT");
            $position=$db->result($r,$i-1,"POSITION");
            $child=$this->getTecGroupTreeChilds($str_id);
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

        for ($i=1;$i<=30;$i++) { $lvl+=1;
            foreach ($td_array as $elm) {
                if ($elm["level"]==$lvl) {
                    
				    $str_id2 = $elm["id_tree"];
                    $str_id_parrent2 = $elm["id_parent"];
                    $str_level2 = $elm["level"];
					
					$str="<li><div>";
					
                    if ($elm["child"]>0)  {$str.="<i class='fa fa-eye' onclick='showGroupTreeCard($str_id2);'></i> <a>".$elm["name"]."</a>";}
                    if ($elm["child"]==0) {$str.="<a onclick='showGroupTreeCard($str_id2);'>".$elm["name"]."</a>";} $str.="</div>";
                    if ($elm["child"]>0)  {$str.="\n<ul>\n{p".$elm["id_tree"]."}</ul>\n";} $str.="</li>\n";
					
                    if ($lvl==2) {$tree.=$str;}
                    if ($lvl>2)  {$tree=str_replace("{p".$elm["id_parent"]."}",$str."{p".$elm["id_parent"]."}",$tree);}
                }
            }
        }
							  
        foreach ($td_array as $elm){
            $tree=str_replace("{p".$elm["id_parent"]."}","",$tree);
            $tree=str_replace("{p".$elm["id_tree"]."}","",$tree);
        }
        $menu_det.=$tree."</ul>";					  
							  					  
		$form=str_replace("{group_tree_range}",$menu_det,$form);
		$form=str_replace("{group_tree_head}",$this->showGroupTreeHeaders(),$form);
		return $form;
	}
	
	function showGroupTreeCard($str_id) {$db=DbSingleton::getTokoDb();
		$form_htm=RD."/tpl/group_tree_card.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
		$r=$db->query("select * from T2_GROUP_TREE where STR_ID='$str_id';"); $n=$db->num_rows($r); 
		for ($i=1;$i<=$n;$i++){
			$STR_ID_PARENT=$db->result($r,$i-1,"STR_ID_PARENT");
			$LNG_ID=$db->result($r,$i-1,"LNG_ID");
			$TEX_TEXT=$db->result($r,$i-1,"TEX_TEXT");
			$DISP_TEXT=$db->result($r,$i-1,"DISP_TEXT");
			$POSITION=$db->result($r,$i-1,"POSITION");
			if ($LNG_ID==16) { $tex_text_ru=$TEX_TEXT; $disp_text_ru=$DISP_TEXT; }
			if ($LNG_ID==41) { $tex_text_ua=$TEX_TEXT; $disp_text_ua=$DISP_TEXT; }
			if ($LNG_ID==4)  { $tex_text_en=$TEX_TEXT; $disp_text_en=$DISP_TEXT; }
		}								 
		$form=str_replace("{str_id}",$str_id,$form);
		$form=str_replace("{position}",$POSITION,$form);								 
		$form=str_replace("{tex_text_ru}",$tex_text_ru,$form);
		$form=str_replace("{tex_text_ua}",$tex_text_ua,$form);
		$form=str_replace("{tex_text_en}",$tex_text_en,$form);								 
		$form=str_replace("{disp_text_ru}",$disp_text_ru,$form);
		$form=str_replace("{disp_text_ua}",$disp_text_ua,$form);
		$form=str_replace("{disp_text_en}",$disp_text_en,$form);								 
		$form=str_replace("{position_list}",$this->showParrentPositions($STR_ID,$STR_ID_PARENT),$form);								 
		$form=str_replace("{head_cross}",$this->getStrHeaders($str_id),$form);
		$form=str_replace("{head_list}",$this->getStrHeadersList($str_id),$form);					  
		return $form;
	}
	
	function saveGroupTreeCard($str_id, $position, $disp_text_ru, $disp_text_ua, $disp_text_en) {$db=DbSingleton::getTokoDb();
		$answer=0; $err="������� ���������� �����!"; $position=intval($position);
		if ($str_id>0){
			$db->query("update T2_GROUP_TREE set `POSITION`='$position' where `STR_ID`='$str_id';");
			$db->query("update T2_GROUP_TREE set `DISP_TEXT`='$disp_text_ru' where `STR_ID`='$str_id' and `LNG_ID`=16;");
			$db->query("update T2_GROUP_TREE set `DISP_TEXT`='$disp_text_ua' where `STR_ID`='$str_id' and `LNG_ID`=41;");
			$db->query("update T2_GROUP_TREE set `DISP_TEXT`='$disp_text_en' where `STR_ID`='$str_id' and `LNG_ID`=4;");
			$answer=1;$err="";
		} 
		return array($answer,$err);
	}
	
	function showParrentPositions($str_id,$parent_id) {$db=DbSingleton::getTokoDb(); $list="";
		$r=$db->query("select count(STR_ID) as kol from T2_GROUP_TREE where `STR_ID_PARENT`='$parent_id' and LNG_ID=16");
		$r2=$db->query("select `POSITION` from T2_GROUP_TREE where `STR_ID`='$str_id' and LNG_ID=16"); $position=$db->result($r2,0,"POSITION");
		$kol=intval($db->result($r,0,"kol"));
		for ($i=0;$i<$kol;$i++){	
			if ($i==$position) $selected="selected='selected'"; else $selected="";
			$list.="<option value='$i' $selected>$i</option>";
		}
		return $list;
	}
	
	function getTecGroupTreeChilds($str_id) {$db=DbSingleton::getTokoDb();
        $r=$db->query("SELECT count(STR_ID) as kol FROM `T2_GROUP_TREE` where `STR_ID_PARENT`='$str_id';");
        $kol=intval($db->result($r,0,"kol"));
        return $kol;
    }
	
}