<?php

class group_tree {
	
	function showGroupTreeHeaders() {$db=DbSingleton::getTokoDb();
        $r=$db->query("select * from T2_GROUP_TREE_HEAD;"); $n=$db->num_rows($r);
        $list="<ul style='list-style:none; padding:0;'>";
        for ($i=1;$i<=$n;$i++){
            $HEAD_ID=$db->result($r,$i-1,"HEAD_ID");
            $TEX_TEXT=$db->result($r,$i-1,"TEX_RU");
            $header_list=$this->getGroupTreeStr($HEAD_ID);
            $list.="<li>
				<div class='tree-head pointer'>
					<i class='fa fa-eye' onclick='showGroupTreeHeadCard($HEAD_ID)'></i> 
					<i class='fa fa-plus' onclick='addGroupTreeHeadStr($HEAD_ID)'></i>
					$HEAD_ID. $TEX_TEXT 
				</div>
				<div class='tree-list dnone'>$header_list</div>
			</li>";
        }
        $list.="</ul>";
        return $list;
	}

    function addGroupTreeHeadStr($HEAD_ID) {
        $form_htm=RD."/tpl/group_tree_head_str_card.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $head_list=$this->getTreeHeadersList($HEAD_ID);
        $tree_list=$this->getTreeGroupList();
        $position_list=$this->getPositionList();
        $category_list=$this->getCategoryList($HEAD_ID);
        $form=str_replace("{group_id}","0",$form);
        $form=str_replace("{head_id}",$HEAD_ID,$form);
        $form=str_replace("{head_list}",$head_list,$form);
        $form=str_replace("{tree_list}",$tree_list,$form);
        $form=str_replace("{category_list}",$category_list,$form);
        $form=str_replace("{position_list}",$position_list,$form);
        $form=str_replace("{disp_text_ru}","",$form);
        $form=str_replace("{disp_text_ua}","",$form);
        $form=str_replace("{disp_text_en}","",$form);
        $form=str_replace("{disabled}","disabled",$form);
        return $form;
    }

    function showGroupTreeHeadStr($GROUP_ID) {$db=DbSingleton::getTokoDb();
        $form_htm=RD."/tpl/group_tree_head_str_card.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select * from T2_GROUP_TREE_HEAD_STR where GROUP_ID='$GROUP_ID' limit 1;");
        $STR_ID=$db->result($r,0,"STR_ID");
        $HEAD_ID=$db->result($r,0,"HEAD_ID");
        $POSITION=$db->result($r,0,"POSITION");
        $disp_text_ru=$db->result($r,0,"TEX_RU");
        $disp_text_ua=$db->result($r,0,"TEX_UA");;
        $disp_text_en=$db->result($r,0,"TEX_EN");
        $CAT_ID=$db->result($r,0,"CAT_ID");
        $head_list=$this->getTreeHeadersList($HEAD_ID);
        $tree_list=$this->getTreeGroupList($STR_ID);
        $position_list=$this->getPositionList($POSITION);
        $category_list=$this->getCategoryList($HEAD_ID,$CAT_ID);
        $form=str_replace("{group_id}",$GROUP_ID,$form);
        $form=str_replace("{head_id}",$HEAD_ID,$form);
        $form=str_replace("{head_list}",$head_list,$form);
        $form=str_replace("{tree_list}",$tree_list,$form);
        $form=str_replace("{category_list}",$category_list,$form);
        $form=str_replace("{position_list}",$position_list,$form);
        $form=str_replace("{disp_text_ru}",$disp_text_ru,$form);
        $form=str_replace("{disp_text_ua}",$disp_text_ua,$form);
        $form=str_replace("{disp_text_en}",$disp_text_en,$form);
        return $form;
    }

    function showGroupTreeHeadCategory($CAT_ID) {$db=DbSingleton::getTokoDb();
        $form_htm=RD."/tpl/group_tree_head_category_card.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        if ($CAT_ID=="0") {
            $head_list=$this->getTreeHeadersList();
            $position_list=$this->getPositionList();
            $form=str_replace("{cat_id}",$CAT_ID,$form);
            $form=str_replace("{head_id}","0",$form);
            $form=str_replace("{head_list}",$head_list,$form);
            $form=str_replace("{position_list}",$position_list,$form);
            $form=str_replace("{disp_text_ru}","",$form);
            $form=str_replace("{disp_text_ua}","",$form);
            $form=str_replace("{disp_text_en}","",$form);
            $form=str_replace("{disabled}","disabled",$form);
        } else {
            $r=$db->query("select * from T2_GROUP_TREE_HEAD_CAT where CAT_ID='$CAT_ID' limit 1;");
            $HEAD_ID=$db->result($r,0,"HEAD_ID");
            $POSITION=$db->result($r,0,"POSITION");
            $disp_text_ru=$db->result($r,0,"TEX_RU");
            $disp_text_ua=$db->result($r,0,"TEX_UA");;
            $disp_text_en=$db->result($r,0,"TEX_EN");
            $head_list=$this->getTreeHeadersList($HEAD_ID);
            $position_list=$this->getPositionList($POSITION);
            $form=str_replace("{cat_id}",$CAT_ID,$form);
            $form=str_replace("{head_id}",$HEAD_ID,$form);
            $form=str_replace("{head_list}",$head_list,$form);
            $form=str_replace("{position_list}",$position_list,$form);
            $form=str_replace("{disp_text_ru}",$disp_text_ru,$form);
            $form=str_replace("{disp_text_ua}",$disp_text_ua,$form);
            $form=str_replace("{disp_text_en}",$disp_text_en,$form);
        }
        return $form;
    }

    function saveGroupTreeHeadCategoryCard($cat_id,$head_id,$position,$disp_text_ru,$disp_text_ua,$disp_text_en) {$db=DbSingleton::getTokoDb();
        // $answer=0;$err="Помилка збереження даних!";
        if ($cat_id=="0") {
            $db->query("insert into T2_GROUP_TREE_HEAD_CAT (HEAD_ID,POSITION,TEX_RU,TEX_UA,TEX_EN) values ('$head_id','$position','$disp_text_ru','$disp_text_ua','$disp_text_en');");
        } else {
            $db->query("update T2_GROUP_TREE_HEAD_CAT set HEAD_ID='$head_id',POSITION='$position',TEX_RU='$disp_text_ru',TEX_UA='$disp_text_ua',TEX_EN='$disp_text_en' where CAT_ID=$cat_id;");
        }
        $answer=1;$err="";
        return array($answer,$err);
    }

    function dropGroupTreeHeadCategory($cat_id) {$db=DbSingleton::getTokoDb();
        $answer=0;$err="Помилка видалення даних!";
        if ($cat_id>0) {
            $db->query("delete from T2_GROUP_TREE_HEAD_CAT where CAT_ID='$cat_id';");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function saveGroupTreeHeadStrCard($group_id,$head_id,$str_id,$position,$category,$disp_text_ru,$disp_text_ua,$disp_text_en) {$db=DbSingleton::getTokoDb();
        // $answer=0;$err="Помилка збереження даних!";
        if ($group_id=="0") {
            $db->query("insert into T2_GROUP_TREE_HEAD_STR (HEAD_ID,STR_ID,POSITION,CAT_ID,TEX_RU,TEX_UA,TEX_EN) values ('$head_id','$str_id','$position','$category','$disp_text_ru','$disp_text_ua','$disp_text_en');");
        } else {
            $db->query("update T2_GROUP_TREE_HEAD_STR set HEAD_ID='$head_id',STR_ID='$str_id',POSITION='$position',CAT_ID='$category',TEX_RU='$disp_text_ru',TEX_UA='$disp_text_ua',TEX_EN='$disp_text_en' where GROUP_ID=$group_id;");
        }
        $answer=1;$err="";
        return array($answer,$err);
    }

    function dropGroupTreeHeadStr($group_id) {$db=DbSingleton::getTokoDb();
        $answer=0;$err="Помилка видалення даних!";
        if ($group_id>0) {
            $db->query("delete from T2_GROUP_TREE_HEAD_STR where GROUP_ID='$group_id';");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function getTreeHeadersList($ch="0") {$db=DbSingleton::getTokoDb(); $list="<option value='0'>-Не выбрано-</option>";
        $r=$db->query("select * from T2_GROUP_TREE_HEAD;"); $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $HEAD_ID=$db->result($r,$i-1,"HEAD_ID");
            $TEX_TEXT=$db->result($r,$i-1,"TEX_RU");
            if ($ch==$HEAD_ID) $selected="selected"; else $selected="";
            $list.="<option value='$HEAD_ID' $selected>$HEAD_ID - $TEX_TEXT</option>";
        }
        return $list;
    }

    function getTreeGroupList($ch="0") {$db=DbSingleton::getTokoDb(); $list="<option value='0'>-Не выбрано-</option>";
        $r=$db->query("select * from T2_GROUP_TREE where LNG_ID=16;"); $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $STR_ID=$db->result($r,$i-1,"STR_ID");
            $DISP_TEXT=$db->result($r,$i-1,"DISP_TEXT");
            if ($ch==$STR_ID) $selected="selected"; else $selected="";
            $list.="<option value='$STR_ID' $selected>$STR_ID - $DISP_TEXT</option>";
        }
        return $list;
    }

    function getCategoryList($HEAD_ID,$ch="0") {$db=DbSingleton::getTokoDb(); $list="<option value='0'>-Не выбрано-</option>";
        $r=$db->query("select * from T2_GROUP_TREE_HEAD_CAT where HEAD_ID=$HEAD_ID;"); $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $CAT_ID=$db->result($r,$i-1,"CAT_ID");
            $TEX_TEXT=$db->result($r,$i-1,"TEX_RU");
            if ($ch==$CAT_ID) $selected="selected"; else $selected="";
            $list.="<option value='$CAT_ID' $selected>$CAT_ID - $TEX_TEXT</option>";
        }
        return $list;
    }

    function getPositionList($ch="0") {$list="<option value='0'>-Не выбрано-</option>";
        $n=100;
        for ($i=1;$i<=$n;$i++){
            if ($ch==$i) $selected="selected"; else $selected="";
            $list.="<option value='$i' $selected>$i</option>";
        }
        return $list;
    }
	
	function getGroupTreeStr($HEAD_ID) {$db=DbSingleton::getTokoDb(); $arr=[]; $list="";
        $r=$db->query("select cs.* from T2_GROUP_TREE_HEAD_STR cs 
            left outer join T2_GROUP_TREE_HEAD_CAT cat on cat.CAT_ID=cs.CAT_ID
		where cs.HEAD_ID='$HEAD_ID' order by cat.POSITION, cs.POSITION;"); $n=$db->num_rows($r);

        for ($i=1;$i<=$n;$i++) {
            $CAT_ID=$db->result($r,$i-1,"CAT_ID");
            $GROUP_ID=$db->result($r,$i-1,"GROUP_ID");
            $DISP_TEXT=$db->result($r,$i-1,"TEX_RU");
            $POSITION=$db->result($r,$i-1,"POSITION");
            $arr[$CAT_ID][$i]=["text"=>$DISP_TEXT, "group"=>$GROUP_ID, "position"=>$POSITION];
        }

        foreach ($arr as $key=>$value) {
            $cat_name=$this->getCatName($key);
            $cat_pos=$this->getCatPos($key);
            $list.="<div class=\"tree-category\"><i class='fa fa-pencil pointer' onclick='showGroupTreeHeadCategory($key)'> $cat_name ($cat_pos)</i></div>"; $list.="<ul>";
            foreach ($value as $v) {
                $tex=$v["text"];
                $group=$v["group"];
                $position=$v["position"];
                $list.="<li><i class='fa fa-pencil pointer' onclick='showGroupTreeHeadStr($group);'> $tex ($position)</i></li>";
            }
            $list.="</ul>";
        }
        if ($n==0) $list="Пусто";
        return $list;
	}

    function getCatName($CAT_ID) {$db=DbSingleton::getTokoDb();
        $r=$db->query("select TEX_RU from T2_GROUP_TREE_HEAD_CAT where CAT_ID='$CAT_ID' limit 1;");
        $CAT_NAME=$db->result($r,0,"TEX_RU");
        return $CAT_NAME;
    }

    function getCatPos($CAT_ID) {$db=DbSingleton::getTokoDb();
        $r=$db->query("select POSITION from T2_GROUP_TREE_HEAD_CAT where CAT_ID='$CAT_ID' limit 1;");
        $CAT_POSITION=$db->result($r,0,"POSITION");
        return $CAT_POSITION;
    }
	
	function showGroupTreeHead($head_id) {$db=DbSingleton::getTokoDb();
        $form_htm=RD."/tpl/group_tree_head_card.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        if ($head_id=="0") {
            $r1=$db->query("select max(HEAD_ID) as max_head from T2_GROUP_TREE_HEAD;");
            $head_id=0+$db->result($r1,0,"max_head")+1;
            $disp_text_ru=$disp_text_ua=$disp_text_en="";
        } else {
            $r=$db->query("select * from T2_GROUP_TREE_HEAD where HEAD_ID='$head_id' limit 1;");
            $disp_text_ru=$db->result($r,0,"TEX_RU");
            $disp_text_ua=$db->result($r,0,"TEX_UA");
            $disp_text_en=$db->result($r,0,"TEX_EN");
        }
        $form=str_replace("{head_id}",$head_id,$form);
        $form=str_replace("{disp_text_ru}",$disp_text_ru,$form);
        $form=str_replace("{disp_text_ua}",$disp_text_ua,$form);
        $form=str_replace("{disp_text_en}",$disp_text_en,$form);
        return $form;
	}
	
	function saveGroupTreeHead($head_id, $disp_text_ru, $disp_text_ua, $disp_text_en) {$db=DbSingleton::getTokoDb();
        $answer=0;$err="Помилка збереження даних!";
        if ($head_id>0){
            $r=$db->query("select * from T2_GROUP_TREE_HEAD where `HEAD_ID`='$head_id';"); $n=$db->num_rows($r);
            if ($n>0) {
                $db->query("update T2_GROUP_TREE_HEAD set `TEX_RU`='$disp_text_ru',`TEX_UA`='$disp_text_ua',`TEX_EN`='$disp_text_en' where `HEAD_ID`='$head_id';");
            }
            else {
                $db->query("insert into T2_GROUP_TREE_HEAD (`HEAD_ID`,`TEX_RU`,`TEX_UA`,`TEX_EN`) values ('$head_id', '$disp_text_ru', '$disp_text_ua', '$disp_text_en');");
            }
            $answer=1;$err="";
        }
        return array($answer,$err);
	}
	
	function dropGroupTreeHead($head_id) {$db=DbSingleton::getTokoDb();
        $answer=0;$err="Помилка видалення даних!";
        if ($head_id>0) {
            $db->query("delete from T2_GROUP_TREE_HEAD where HEAD_ID='$head_id';");
            $answer=1;$err="";
        }
        return array($answer,$err);
	}
	
	function showGroupTree() {$db=DbSingleton::getTokoDb();
        $menu_det=$tree=""; $lvl=0; $td_array=$parent_fare=$position_fare=[];
        $form_htm=RD."/tpl/group_tree.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select * from T2_GROUP_TREE where LNG_ID=16;"); $n=$db->num_rows($r);

        $menu_det.="<div class=\"input-group border0\">
		<input type=\"search\" id=\"my-search\" class=\"my-search\" placeholder=\"Поиск по категориям\">
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
	
	function showGroupTreeCard($STR_ID) {$db=DbSingleton::getTokoDb();
        $POSITION=$STR_ID_PARENT=0;$tex_text_ru=$tex_text_ua=$tex_text_en=$disp_text_ru=$disp_text_ua=$disp_text_en="";
        $form_htm=RD."/tpl/group_tree_card.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select * from T2_GROUP_TREE where STR_ID='$STR_ID';"); $n=$db->num_rows($r);
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
        $form=str_replace("{str_id}",$STR_ID,$form);
        $form=str_replace("{position}",$POSITION,$form);
        $form=str_replace("{tex_text_ru}",$tex_text_ru,$form);
        $form=str_replace("{tex_text_ua}",$tex_text_ua,$form);
        $form=str_replace("{tex_text_en}",$tex_text_en,$form);
        $form=str_replace("{disp_text_ru}",$disp_text_ru,$form);
        $form=str_replace("{disp_text_ua}",$disp_text_ua,$form);
        $form=str_replace("{disp_text_en}",$disp_text_en,$form);
        $form=str_replace("{position_list}",$this->showParrentPositions($STR_ID,$STR_ID_PARENT),$form);
        return $form;
	}
	
	function saveGroupTreeCard($str_id, $position, $disp_text_ru, $disp_text_ua, $disp_text_en) {$db=DbSingleton::getTokoDb();
        $answer=0;$err="Помилка збереження даних!"; $position=intval($position);
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