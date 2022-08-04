<?php

class CatalogParts
{

    function getCatalogPartsForm() {
        $form_htm=RD."/tpl/catalog_parts/form.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $form=str_replace("{catalog_range}", "", $form);
        $form=str_replace("{select_category_list}", $this->showCatalogCategoryList(), $form);
        $form=str_replace("{select_brand_list}", $this->showCatalogBrandList(), $form);
        return $form;
    }

    /*==== CATALOG TREE ====*/
    function showCatalogTree() { $db=DbSingleton::getTokoDb();
        $menu_det=$tree=""; $lvl=0;
        $td_array=$parent_fare=$position_fare=[];
        $r = $db->query("SELECT * FROM `T2_GROUP_TREE` WHERE `LNG_ID`=16;"); $n = $db->num_rows($r);

        $menu_det.="<div class=\"input-group border0\">
		<input type=\"search\" id=\"my-search\" class=\"my-search form-control\" placeholder=\"Поиск по категориям\" style='margin-bottom: 15px;'>
		</div><ul id=\"my-tree\" class=\"tf-tree\">";

        for ($i=1; $i<=$n; $i++) {
            $str_id = $db->result($r,$i-1,"STR_ID");
            $str_id_parrent = $db->result($r,$i-1,"STR_ID_PARENT");if ($str_id_parrent==""){$str_id_parrent=0;}
            $str_level = $db->result($r,$i-1,"STR_LEVEL");
            $tex_text = $db->result($r,$i-1,"DISP_TEXT");
            $position = $db->result($r,$i-1,"POSITION");
            $child = $this->getTecGroupTreeChilds($str_id);
            $td_array[$i]["id_tree"] = $str_id;
            $td_array[$i]["id_parent"] = $str_id_parrent;
            $td_array[$i]["level"] = $str_level;
            $td_array[$i]["name"] = $tex_text;
            $td_array[$i]["child"] = $child;
            $td_array[$i]["position"] = $position;
        }

        foreach ($td_array as $key=>$row) {
            $parent_fare[$key] = $row['id_parent'];
            $position_fare[$key] = $row['position'];
        }
        array_multisort($parent_fare, SORT_ASC, $position_fare, SORT_DESC, $td_array);

        for ($i=1; $i<=30; $i++) { $lvl+=1;
            foreach ($td_array as $elm) {
                if ($elm["level"]==$lvl) {
                    $str_id2 = $elm["id_tree"];
                    $str = "<li><div>";
                    if ($elm["child"]>0)  { $str.="<a>".$elm["name"]."</a>"; }
                    if ($elm["child"]==0) {
                        $count = $this->getCountArticleT2Tree($str_id2);
                        $str.="<a onclick='chooseSelect2Str($str_id2);'>".$elm["name"]." ($count шт.)</a>";
                    }
                    $str.="</div>";
                    if ($elm["child"]>0) { $str.="\n<ul>\n{p".$elm["id_tree"]."}</ul>\n";} $str.="</li>\n";
                    if ($lvl==2) { $tree.=$str; }
                    if ($lvl>2) { $tree = str_replace("{p".$elm["id_parent"]."}",$str."{p".$elm["id_parent"]."}",$tree); }
                }
            }
        }
        foreach ($td_array as $elm) {
            $tree = str_replace("{p".$elm["id_parent"]."}","",$tree);
            $tree = str_replace("{p".$elm["id_tree"]."}","",$tree);
        }
        $menu_det.=$tree."</ul>";
        return $menu_det;
    }

    function getTecGroupTreeChilds($str_id) { $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT COUNT(`STR_ID`) as kol FROM `T2_GROUP_TREE` WHERE `STR_ID_PARENT`='$str_id';");
        $kol = intval($db->result($r,0,"kol"));
        return $kol;
    }

    /*==== /CATALOG TREE ====*/

    function checkAutoLink($art_id) { $dbt=DbSingleton::getTokoDb();
        $r=$dbt->query("SELECT COUNT(`TYP_ID`) as count_types FROM `T2_LINKS` WHERE `ART_ID`='$art_id';"); $n=$dbt->num_rows($r);
        $count_types = $dbt->result($r, 0, "count_types");
        if ($n==0) $count_types=0;
        return $count_types;
    }

    function saveCatalogParts($group_id, $arts) { $db=DbSingleton::getTokoDb();
        session_start(); $user_id = $_SESSION["media_user_id"];
        $arts = explode(",", $arts); $k = 0;
        foreach ($arts as $art_id) {
            $r = $db->query("SELECT * FROM `T2_TREE_ARTS` WHERE `ART_ID`='$art_id' AND `GROUP_ID`='$group_id' LIMIT 1;"); $n = $db->num_rows($r);
            if ($n==0) {
                $db->query("INSERT INTO `T2_TREE_ARTS` (`GROUP_ID`, `ART_ID`, `USER_ID`) VALUES ($group_id, $art_id, $user_id);");
                $db->query("DELETE FROM `T2_TREE_NEW` WHERE `ART_ID`='$art_id';");
                $k++;
            }
        }
        if ($k>0) {
            $answer = "Додано - $k позицій!";
        } else {
            $answer = "Нічого не додано!";
        }
        return $answer;
    }

    function saveCatalogParts2($group_id, $str_id=0, $brand_id=[], $type_id=0, $text="", $name="", $name_exist="", $name_select=[], $name_exist_select=[], $checked_auto=1) { $db=DbSingleton::getTokoDb();
        session_start(); $user_id = $_SESSION["media_user_id"];

        $arts = [];
        $where_str=""; if ($str_id!=0) $where_str="AND t2t.`STR_ID`='$str_id'";
        $where_text=""; if ($text!="") $where_text="AND t2a.`ARTICLE_NR_DISPL` LIKE '%$text%'";
        $where_name=""; if ($name!="") $where_name="AND t2n.`NAME` LIKE '%$name%'";
        $where_name_exist=""; if ($name_exist!="") $where_name_exist="AND t2n.`NAME_EXIST` LIKE '%$name_exist%'";

        $where_brand="";
        if (!empty($brand_id)) {
            foreach ($brand_id as $key=>$value) {
                $brand_id[$key] = "'".$value."'";
            }
            $brand_id = implode(",", $brand_id);
            $where_brand="AND t2a.`BRAND_ID` IN ($brand_id)";
        }

        $where_name_select="";
        if (!empty($name_select)) {
            foreach ($name_select as $key=>$value) {
                $name_select[$key] = "'".$value."'";
            }
            $name_select = implode(",", $name_select);
            $where_name_select="AND t2n.`NAME` IN ($name_select)";
        }

        $where_name_exist_select="";
        if (!empty($name_exist_select)) {
            foreach ($name_exist_select as $key=>$value) {
                $name_exist_select[$key] = "'".$value."'";
            }
            $name_exist_select = implode(",", $name_exist_select);
            $where_name_exist_select="AND t2n.`NAME_EXIST` IN ($name_exist_select)";
        }

        if ($str_id!=0) {
            $r = $db->query("SELECT t2a.ART_ID
            FROM `T2_TREE` t2t 
                LEFT JOIN `T2_ARTICLES` t2a ON t2a.ART_ID = t2t.ART_ID
                LEFT JOIN `T2_NAMES` t2n ON t2n.ART_ID = t2t.ART_ID
            WHERE 1 $where_str $where_brand $where_text $where_name $where_name_select $where_name_exist $where_name_exist_select
            AND (CASE WHEN t2n.LANG_ID!=NULL THEN t2n.LANG_ID=16 ELSE TRUE END) 
            GROUP BY t2a.ART_ID;");
            $n = $db->num_rows($r);

            for ($i = 1; $i <= $n; $i++) {
                $art_id = $db->result($r, $i - 1, "ART_ID");

                if ($type_id != 0) {
                    if ($type_id == 1) {
                        if (!$this->checkCatalogTreeArts($art_id)) {
                            array_push($arts, $art_id);
                        }
                    }
                    if ($type_id == 2) {
                        if ($this->checkCatalogTreeArts($art_id)) {
                            array_push($arts, $art_id);
                        }
                    }
                } else {
                    array_push($arts, $art_id);
                }

                if (!$checked_auto) {
                    if ($this->checkAutoLink($art_id)) {
                        array_pop($arts);
                    }
                }
            }
        }

        $k=0;
        foreach ($arts as $art_id) {
            $r = $db->query("SELECT * FROM `T2_TREE_ARTS` WHERE `ART_ID`='$art_id' AND `GROUP_ID`='$group_id' LIMIT 1;"); $n = $db->num_rows($r);
            if ($n==0) {
                $db->query("INSERT INTO `T2_TREE_ARTS` (`GROUP_ID`, `ART_ID`, `USER_ID`) VALUES ($group_id, $art_id, $user_id);");
                $db->query("DELETE FROM `T2_TREE_NEW` WHERE `ART_ID`='$art_id';");
                $k++;
            }
        }
        if ($k>0) {
            $answer = "Додано - $k позицій!";
        } else {
            $answer = "Нічого не додано!";
        }
        return $answer;
    }

    function checkCatalogTreeArts($art_id) { $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT * FROM `T2_TREE_ARTS` WHERE `ART_ID`='$art_id' LIMIT 1;"); $n = $db->num_rows($r);
        if ($n==0) {
            return 0;
        } else {
            return 1;
        }
    }

    function showCatalogList($str_id=0, $brand_id=[], $type_id=0, $text="", $name="", $name_exist="", $name_select=[], $name_exist_select=[], $checked_auto=1) { $dbt = DbSingleton::getTokoDb();
        $list = "";
        $arr = [];
        $col = 0;

        $where_str=""; if ($str_id!=0) $where_str="AND t2t.`STR_ID`='$str_id'";
        $where_text=""; if ($text!="") $where_text="AND t2a.`ARTICLE_NR_DISPL` LIKE '%$text%'";
        $where_name=""; if ($name!="") $where_name="AND t2n.`NAME` LIKE '%$name%'";
        $where_name_exist=""; if ($name_exist!="") $where_name_exist="AND t2n.`NAME_EXIST` LIKE '%$name_exist%'";

        $where_brand = "";
        if (!empty($brand_id)) {
            foreach ($brand_id as $key => $value) {
                $brand_id[$key] = "'" . $value . "'";
            }
            $brand_id = implode(",", $brand_id);
            $where_brand = "AND t2a.`BRAND_ID` IN ($brand_id)";
        }

        $where_name_select = "";
        if (!empty($name_select)) {
            foreach ($name_select as $key => $value) {
                $name_select[$key] = "'" . $value . "'";
            }
            $name_select = implode(",", $name_select);
            $where_name_select = "AND t2n.`NAME` IN ($name_select)";
        }

        $where_name_exist_select = "";
        if (!empty($name_exist_select)) {
            foreach ($name_exist_select as $key => $value) {
                $name_exist_select[$key] = "'" . $value . "'";
            }
            $name_exist_select = implode(",", $name_exist_select);
            $where_name_exist_select = "AND t2n.`NAME_EXIST` IN ($name_exist_select)";
        }

        if ($str_id != 0) {
            $r = $dbt->query("SELECT t2a.ART_ID, t2a.BRAND_ID, t2a.ARTICLE_NR_DISPL, t2n.NAME, t2n.NAME_EXIST
            FROM `T2_TREE` t2t 
                LEFT JOIN `T2_ARTICLES` t2a ON t2a.ART_ID = t2t.ART_ID
                LEFT JOIN `T2_NAMES` t2n ON t2n.ART_ID = t2t.ART_ID
            WHERE 1 $where_str $where_brand $where_text $where_name $where_name_select $where_name_exist $where_name_exist_select
            AND (CASE WHEN t2n.LANG_ID!=NULL THEN t2n.LANG_ID=16 ELSE TRUE END) 
            GROUP BY t2a.ART_ID;");
            $n = $dbt->num_rows($r);

            for ($i = 1; $i <= $n; $i++) {
                $art_id = $dbt->result($r, $i - 1, "ART_ID");
                $article_brand_id = $dbt->result($r, $i - 1, "BRAND_ID");
                $article_nr_displ = $dbt->result($r, $i - 1, "ARTICLE_NR_DISPL");
                $article_name = $dbt->result($r, $i - 1, "NAME");
                $article_name_exist = $dbt->result($r, $i - 1, "NAME_EXIST");
                $image = $this->getArticlePhoto($art_id);
                $group_name = $this->getGroupTreeName($art_id);
                $brand_name = $this->getBrandName($article_brand_id);

                if ($type_id!=0) {
                    if ($type_id==1) {
                        if (!$this->checkCatalogTreeArts($art_id)) {
                            $arr[$art_id] = ["image"=>$image, "article_nr_displ"=>$article_nr_displ, "brand_name"=>$brand_name, "article_name"=>$article_name, "article_name_exist"=>$article_name_exist, "group_name"=>$group_name];
                        }
                    }
                    if ($type_id==2) {
                        if ($this->checkCatalogTreeArts($art_id)) {
                            $arr[$art_id] = ["image"=>$image, "article_nr_displ"=>$article_nr_displ, "brand_name"=>$brand_name, "article_name"=>$article_name, "article_name_exist"=>$article_name_exist, "group_name"=>$group_name];
                        }
                    }
                } else {
                    $arr[$art_id] = ["image"=>$image, "article_nr_displ"=>$article_nr_displ, "brand_name"=>$brand_name, "article_name"=>$article_name, "group_name"=>$group_name];
                }

                if (!$checked_auto) {
                    if ($this->checkAutoLink($art_id)) {
                        unset($arr[$art_id]);
                    }
                }
            }

            foreach ($arr as $art_id=>$val) { $col++;
                $image=$val["image"];
                $article_nr_displ=$val["article_nr_displ"];
                $brand_name=$val["brand_name"];
                $article_name=$val["article_name"];
                $article_name_exist=$val["article_name_exist"];
                $group_name=$val["group_name"];
                $list.="<tr>
                    <td>$col</td>
                    <td>
                        <input type=\"checkbox\" id=\"list_check-$art_id\" data-id=\"$art_id\" class=\"list-check\" onchange='showCountChecked();'>
                        <label for=\"list_check-$art_id\">Вибрати</label>
                    </td>
                    <td>$image</td>
                    <td>$article_nr_displ</td>
                    <td>$brand_name</td>
                    <td>$article_name</td>
                    <td>$article_name_exist</td>
                    <td>$group_name</td>
                </tr>";
            }
        }

        if ($col==0) {
            var_dump("ПУСТО");
            $list = "<tr><td colspan='7' align='center'>Не знайдено</td></tr>";
        }

        return array($list, $col);
    }

    function showCatalogPartsCard($art_ids_str) {
        $art_ids_str = trim($art_ids_str, ",");
        $art_ids = explode(",", $art_ids_str);
        $form_htm=RD."/tpl/catalog_parts/card.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $form=str_replace("{art_ids_range}", implode(",", $art_ids), $form);
        $form=str_replace("{art_ids_count}", count($art_ids), $form);
        $form=str_replace("{selected_category}", "НЕ ВИБРАНО!", $form);
        $form=str_replace("{selected_category_list}", $this->showCatalogPartsHeader(), $form);
        return $form;
    }

    function showCatalogPartsCard2() {
        $form_htm=RD."/tpl/catalog_parts/card2.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $form=str_replace("{selected_category}", "НЕ ВИБРАНО!", $form);
        $form=str_replace("{selected_category_list}", $this->showCatalogPartsHeader(), $form);
        return $form;
    }

    function showCatalogCategoryList() { $dbt = DbSingleton::getTokoDb();
        $list = "";
        $r = $dbt->query("SELECT * FROM `T2_GROUP_TREE` WHERE `LNG_ID`=16 ORDER BY `TEX_TEXT` ASC;"); $n=$dbt->num_rows($r);
        for ($i=1; $i<=$n; $i++) {
            $str_id = $dbt->result($r, $i - 1, "STR_ID");
            $str_id_parent = $dbt->result($r, $i - 1, "STR_ID_PARENT");
            $str_name_parent = $this->getStrText($str_id_parent);
            $tex_text = $dbt->result($r, $i - 1, "TEX_TEXT");
            if ($str_id==13879) $ch="selected"; else $ch="";
            $list.="<option value='$str_id' $ch>$tex_text ($str_name_parent)</option>";
        }
        return $list;
    }

    function getCountArticleT2Tree($str_id) { $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT COUNT(`ART_ID`) as count_arts FROM `T2_TREE_NEW` WHERE `STR_ID`='$str_id';"); $n = $db->num_rows($r);
        $count = $db->result($r, 0, "count_arts");
        if ($n==0) $count = 0;
        return $count;
    }

    function getStrText($str_id) { $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT * FROM `T2_GROUP_TREE` WHERE `STR_ID`='$str_id' AND `LNG_ID`=16 LIMIT 1;");
        $tex_text = $db->result($r, 0, "DISP_TEXT");
        return $tex_text;
    }

    function showCatalogPartsHeader() { $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT * FROM `T2_TREE_HEAD`;"); $n = $db->num_rows($r);
        $list = "<ul style='list-style:none; padding:0;'>";
        for ($i=1; $i<=$n; $i++) {
            $head_id = $db->result($r,$i-1,"HEAD_ID");
            $text = $db->result($r,$i-1,"TEX_RU");
            $header_list = $this->showCatalogPartsCategory($head_id);
            $list.="<li>
				<div class='tree-head pointer'>
					$head_id. $text
				</div>
				<div class='tree-list dnone'>$header_list</div>
			</li>";
        }
        $list.="</ul>";
        return $list;
    }

    function showCatalogPartsCategory($head_id) { $db=DbSingleton::getTokoDb();
        $arr=[]; $list="";

		$r=$db->query("SELECT * FROM `T2_TREE_HCG` WHERE `HEAD_ID`='$head_id' ORDER BY `CAT_ID`;"); $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++) {
            $cat_id=$db->result($r,$i-1,"CAT_ID");
            $group_id=$db->result($r,$i-1,"GROUP_ID");
            if (empty($arr[$cat_id])) $arr[$cat_id]=[];
            array_push($arr[$cat_id], $group_id);
        }

        foreach ($arr as $cat_id=>$group_ids) {
            $group_arr=[];
            $cat_name=$this->getCatName($cat_id);
            $list.="<div class=\"tree-category\"><i>$cat_name</i></div>";
            $list.="<ul class=\"group-tree\">";
            foreach ($group_ids as $group_id) {
                $group_name=$this->getGroupName($group_id);
                $group_arr[$group_id] = ["group_id"=>$group_id, "name"=>$group_name];
            }
            usort($group_arr, function($a, $b) {
                return $a['name'] <=> $b['name'];
            });
            foreach ($group_arr as $value) {
                $group_id=$value["group_id"];
                $group_name=$value["name"];
                $list.="<li><a class='pointer' onclick=\"checkCatalogPartsGroup($group_id);\">$group_name</a></li>";
            }
            $list.="</ul>";

        }
        if ($n==0) $list="Пусто";
        return $list;
    }

    function getCatName($cat_id) {$db=DbSingleton::getTokoDb();
        $r=$db->query("SELECT `TEX_RU` FROM `T2_TREE_CAT` WHERE `CAT_ID`='$cat_id' LIMIT 1;");
        $cat_name=$db->result($r,0,"TEX_RU");
        return $cat_name;
    }

    function getGroupName($group_id) {$db=DbSingleton::getTokoDb();
        $r=$db->query("SELECT `TEX_RU` FROM `T2_TREE_GROUP` WHERE `GROUP_ID`='$group_id' LIMIT 1;");
        $group_name=$db->result($r,0,"TEX_RU");
        return $group_name;
    }

    function getGroupReviewed($group_id) { $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT `REVIEWED` FROM `T2_TREE_GROUP` WHERE `GROUP_ID`='$group_id' LIMIT 1;");
        $reviewed = $db->result($r, 0, "REVIEWED");
        return $reviewed;
    }

    function showCatalogBrandList() { $dbt = DbSingleton::getTokoDb();
        $list="";
        $r=$dbt->query("SELECT * FROM `T2_BRANDS` WHERE 1 ORDER BY `BRAND_NAME` ASC;"); $n=$dbt->num_rows($r);
        for ($i=1; $i<=$n; $i++) {
            $brand_id = $dbt->result($r, $i - 1, "BRAND_ID");
            $brand_name = $dbt->result($r, $i - 1, "BRAND_NAME");
            $list.="<option value='$brand_id'>$brand_name</option>";
        }
        return $list;
    }

    function getArticleName($art_id) { $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT * FROM `T2_NAMES` WHERE `ART_ID`='$art_id' AND `LANG_ID`='16' LIMIT 1;");
        $name = $db->result($r, 0, "NAME");
        return $name;
    }

    function getArticlePhoto($art_id) { $db= DbSingleton::getTokoDb();
        $r = $db->query("SELECT * FROM `T2_PHOTOS` WHERE `ART_ID`='$art_id' ORDER BY `MAIN` DESC LIMIT 1;");
        $image_src = $db->result($r, 0, "PHOTO_NAME");
        $image = "<img width='100' height='100' src=\"https://toko.ua/uploads/images/catalogue/$image_src\" style=\"display: block; margin: 0 auto;\">";
        return $image;
    }

    // NOVA _DETAIL
    function getGroupTreeName($art_id) { $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT * FROM `T2_TREE_ARTS` WHERE `ART_ID`='$art_id' LIMIT 1;"); $n = $db->num_rows($r);
        if ($n==0) {
            $tex_text = "-";
        } else {
            $group_id = $db->result($r, 0, "GROUP_ID");
            $tex_text = $this->getGroupName($group_id);
        }
        return $tex_text;
    }

    function checkArts($art_id) { $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT COUNT(`ART_ID`) as count_arts FROM `T2_TREE_ARTS` WHERE `ART_ID`='$art_id' LIMIT 1;");
        $count = $db->result($r, 0, "count_arts");
        return $count;
    }

    function setCatalogPartsBrands($str_id, $type_id) { $dbt = DbSingleton::getTokoDb();
        $arr_brand=[]; $arr_name=[]; $arr_name_exist=[]; $list_brand=""; $list_name=""; $list_name_exist="";
        $r = $dbt->query("SELECT t2a.ART_ID, t2a.BRAND_ID, t2n.NAME, t2n.NAME_EXIST
        FROM `T2_TREE` t2t 
            LEFT JOIN `T2_ARTICLES` t2a ON t2a.ART_ID = t2t.ART_ID
            LEFT JOIN `T2_NAMES` t2n ON t2n.ART_ID = t2t.ART_ID
        WHERE 1 AND t2t.`STR_ID`='$str_id' AND (CASE WHEN t2n.LANG_ID!=NULL THEN t2n.LANG_ID=16 ELSE TRUE END) 
        GROUP BY t2a.ART_ID;");
        $n = $dbt->num_rows($r);

        for ($i=1; $i<=$n; $i++) {
            $art_id = $dbt->result($r, $i - 1, "ART_ID");
            $brand_id = $dbt->result($r, $i - 1, "BRAND_ID");
            $name = $dbt->result($r, $i - 1, "NAME");
            $name_exist = $dbt->result($r, $i - 1, "NAME_EXIST");

            if ($type_id==0) {
                array_push($arr_brand, $brand_id);
                array_push($arr_name, $name);
                array_push($arr_name_exist, $name_exist);
            }

            // НЕ ПРИВЯЗАНІ
            if ($type_id==1) {
                if ($this->checkArts($art_id)==0) {
                    array_push($arr_brand, $brand_id);
                    array_push($arr_name, $name);
                    array_push($arr_name_exist, $name_exist);
                }
            }

            // ПРИВЯЗАНІ
            if ($type_id==2) {
                if ($this->checkArts($art_id)>0) {
                    array_push($arr_brand, $brand_id);
                    array_push($arr_name, $name);
                    array_push($arr_name_exist, $name_exist);
                }
            }
        }
        $arr_brand=array_unique($arr_brand);
        $arr_name=array_unique($arr_name); sort($arr_name);
        $arr_name_exist=array_unique($arr_name_exist); sort($arr_name_exist);

        if (!empty($arr_brand)) $list_brand.="<option value='0'>-Показати всі-</option>";
        foreach ($arr_brand as $brand_id) {
            $brand_name=$this->getBrandName($brand_id);
            $list_brand.="<option value='$brand_id'>$brand_name</option>";
        }

        foreach ($arr_name as $name) {
            $list_name.="<option value='$name'>$name</option>";
        }

        foreach ($arr_name_exist as $name) {
            $list_name_exist.="<option value='$name'>$name</option>";
        }

        unset($arr_name[0]);
        unset($arr_name_exist[0]);

        return array($list_brand, $list_name, $arr_name, $list_name_exist, $arr_name_exist);
    }

    function getArticleNameCount($name_select) { $db=DbSingleton::getTokoDb();
        $count = 0;
        if (!empty($name_select)) {
            foreach ($name_select as $key=>$value) {
                $name_select[$key] = "'".$value."'";
            }
            $name_select = implode(",", $name_select);
            $r = $db->query("SELECT COUNT(`ART_ID`) as count_names FROM `T2_NAMES` WHERE `NAME` IN ($name_select) AND `LANG_ID`=16;");
            $count = $db->result($r, 0, "count_names");
        }
        return $count;
    }

    function setCatalogPartsBrandsName($str_id, $brand_id, $type_id) { $dbt=DbSingleton::getTokoDb();
        $arr_name=[]; $arr_name_exist=[]; $list_name=""; $list_name_exist="";
        $r = $dbt->query("SELECT t2a.ART_ID, t2a.BRAND_ID, t2n.NAME, t2n.NAME_EXIST
        FROM `T2_TREE` t2t 
            LEFT JOIN `T2_ARTICLES` t2a ON t2a.ART_ID = t2t.ART_ID
            LEFT JOIN `T2_NAMES` t2n ON t2n.ART_ID = t2t.ART_ID
        WHERE 1 AND t2t.`STR_ID`='$str_id' AND t2a.`BRAND_ID`='$brand_id' AND (CASE WHEN t2n.LANG_ID!=NULL THEN t2n.LANG_ID=16 ELSE TRUE END) 
        GROUP BY t2a.ART_ID;");
        $n = $dbt->num_rows($r);

        for ($i=1; $i<=$n; $i++) {
            $art_id = $dbt->result($r, $i - 1, "ART_ID");
            $name = $dbt->result($r, $i - 1, "NAME");
            $name_exist = $dbt->result($r, $i - 1, "NAME_EXIST");

            if ($type_id==0) {
                array_push($arr_name, $name);
                array_push($arr_name_exist, $name_exist);
            }

            // НЕ ПРИВЯЗАНІ
            if ($type_id==1) {
                if ($this->checkArts($art_id)==0) {
                    array_push($arr_name, $name);
                    array_push($arr_name_exist, $name_exist);
                }
            }

            // ПРИВЯЗАНІ
            if ($type_id==2) {
                if ($this->checkArts($art_id)>0) {
                    array_push($arr_name, $name);
                    array_push($arr_name_exist, $name_exist);
                }
            }

            array_push($arr_name, $name);
            array_push($arr_name_exist, $name_exist);
        }

        $arr_name=array_unique($arr_name); sort($arr_name);
        $arr_name_exist=array_unique($arr_name_exist); sort($arr_name_exist);

        foreach ($arr_name as $name) {
            $list_name.="<option value='$name'>$name</option>";
        }

        foreach ($arr_name_exist as $name) {
            $list_name_exist.="<option value='$name'>$name</option>";
        }

        return array($list_name, $list_name_exist);
    }

    function getBrandName($id) { $db=DbSingleton::getTokoDb();
        $name="";
        $r=$db->query("SELECT `BRAND_NAME` FROM `T2_BRANDS` WHERE `BRAND_ID`='$id' LIMIT 1;");$n=$db->num_rows($r);
        if ($n==1){	$name=$db->result($r,0,"BRAND_NAME");	}
        return $name;
    }

    function getUserName($user_id){ $db=DbSingleton::getDb();
        $r=$db->query("SELECT `name` FROM `media_users` WHERE `id`='$user_id' LIMIT 1;"); $n=$db->num_rows($r);
        if ($n==1) $name=$db->result($r,0,"name"); else $name="-";
        return $name;
    }

    function showCatalogPartsEditCard() {
        $form_htm=RD."/tpl/catalog_parts/edit.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $form=str_replace("{selected_category_list}", $this->showCatalogPartsHeaderEdit(), $form);
        return $form;
    }

    /*==== EDIT ====*/
    function showCatalogPartsHeaderEdit() { $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT * FROM `T2_TREE_HEAD`;"); $n = $db->num_rows($r);
        $list = "<ul style='list-style:none; padding:0;'>";
        for ($i=1; $i<=$n; $i++){
            $head_id = $db->result($r,$i-1,"HEAD_ID");
            $text = $db->result($r,$i-1,"TEX_RU");
            $header_list = $this->showCatalogPartsCategoryEdit($head_id);
            $list.="<li>
				<div class='tree-head pointer'>
					$head_id. $text
				</div>
				<div class='tree-list dnone'>$header_list</div>
			</li>";
        }
        $list.="</ul>";
        return $list;
    }

    function showCatalogPartsCategoryEdit($head_id) { $db=DbSingleton::getTokoDb();
        $arr = []; $list = "";

        $r = $db->query("SELECT * FROM `T2_TREE_HCG` WHERE `HEAD_ID`='$head_id' ORDER BY `CAT_ID`;"); $n = $db->num_rows($r);
        for ($i=1; $i<=$n; $i++) {
            $cat_id = $db->result($r, $i-1, "CAT_ID");
            $group_id = $db->result($r, $i-1, "GROUP_ID");
            if (empty($arr[$cat_id])) $arr[$cat_id] = [];
            array_push($arr[$cat_id], $group_id);
        }

        foreach ($arr as $cat_id=>$group_ids) {
            $group_arr = [];
            $cat_name = $this->getCatName($cat_id);
            $list.="<div class=\"tree-category\"><i>$cat_name</i></div>";
            $list.="<ul class='group-tree'>";
            foreach ($group_ids as $group_id) {
                $group_name = $this->getGroupName($group_id);
                $group_reviewed = $this->getGroupReviewed($group_id);
                $count_arts = $this->getCatalogLogsCount($group_id);
                $group_arr[$group_id] = ["group_id"=>$group_id, "name"=>$group_name, "reviewed"=>$group_reviewed, "count"=>$count_arts];
            }
            usort($group_arr, function($a, $b) {
                return $a['name'] <=> $b['name'];
            });
            foreach ($group_arr as $key=>$value) {
                $group_id = $value["group_id"];
                $group_name = $value["name"];
                $group_reviewed = $value["reviewed"];
                $count_arts = $value["count"];
                $reviewed = "";
                $history = "";
                if ($group_reviewed>0) $reviewed = "<i class='fa fa-check'></i>";
                if ($count_arts>0) $history = "<a onclick='showCatalogLogs($group_id);'><i class='fa fa-history'></i> Історія ($count_arts шт.)</a>";
                $list.="<li>
                    $reviewed
                    <a onclick='showCatalogPartsAddCard($head_id, $cat_id, $group_id);'>$group_name</a> 
                    <a onclick='untieCatalogGroup($head_id, $cat_id, $group_id)'><i class='fa fa-trash'></i> Відвязати</a> 
                    $history
                </li>";
            }
            $list.="</ul>";
        }
        if ($n==0) $list = "Пусто";
        return $list;
    }

    // UNTIE GROUP FROM CATALOG
    function untieCatalogGroup($head_id, $cat_id, $group_id) { $db = DbSingleton::getTokoDb();
        if ($head_id > 0 && $cat_id > 0 && $group_id > 0) {
            $db->query("DELETE FROM `T2_TREE_HCG` WHERE `HEAD_ID`='$head_id' AND `CAT_ID`='$cat_id' AND `GROUP_ID`='$group_id' LIMIT 1;");
        }
        return true;
    }

    /*==== /EDIT ====*/

    /*==== HISTORY ====*/
    function showCatalogLogs($group_id) { $db = DbSingleton::getTokoDb();
        $form_htm=RD."/tpl/catalog_parts/history.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $list = "";
        $r = $db->query("SELECT COUNT(`ART_ID`) as count_arts, `DATE`, `USER_ID` FROM `T2_TREE_ARTS` WHERE `GROUP_ID`='$group_id' GROUP BY `DATE`, `USER_ID`;"); $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++) {
            $count_arts = $db->result($r, $i - 1, "count_arts");
            $date = $db->result($r, $i - 1, "DATE");
            $user_id = $db->result($r, $i - 1, "USER_ID"); $user_name = $this->getUserName($user_id);
            $list.="<tr>
                <td>$i</td>
                <td>$count_arts</td>
                <td>$date</td>
                <td>$user_name</td>
                <td>
                    <a class='btn btn-danger' onclick=\"dropCatalogPartsArtsGroup($group_id, '$date', $user_id)\"><i class='fa fa-trash'></i> Видалити групу</a>
                    <a class='btn btn-info' onclick=\"showCatalogLogsCard($group_id, '$date', $user_id)\"><i class='fa fa-eye'></i> Показати групу</a>
                </td>
             </tr>";
        }
        $group_name=$this->getGroupName($group_id);
        $title = "$group_name (ID: $group_id)";
        $form=str_replace("{group_id}", $group_id, $form);
        $form=str_replace("{history_title}", $title, $form);
        $form=str_replace("{history_count}", $n, $form);
        $form=str_replace("{history_range}", $list, $form);
        return $form;
    }

    function showCatalogLogsCard($group_id, $date, $user_id) { $db=DbSingleton::getTokoDb();
        $form_htm=RD."/tpl/catalog_parts/history_card.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $list="";
        $r = $db->query("SELECT * FROM `T2_TREE_ARTS` WHERE `GROUP_ID`='$group_id' AND `DATE`='$date' AND `USER_ID`='$user_id';"); $n = $db->num_rows($r);
        for ($i=1; $i<=$n; $i++) {
            $art_id = $db->result($r, $i - 1, "ART_ID");
            $date = $db->result($r, $i - 1, "DATE");
            $user_id = $db->result($r, $i - 1, "USER_ID"); $user_name = $this->getUserName($user_id);
            $list.="<tr>
                <td>$i</td>
                <td>$art_id</td>
                <td>$date</td>
                <td>$user_name</td>
                <td><a class='btn btn-danger' onclick='dropCatalogPartsArts($group_id, $art_id)'><i class='fa fa-trash'></i></a></td>
             </tr>";
        }
        $user_name = $this->getUserName($user_id);
        $title="Історія користувача '$user_name' за $date";
        $form=str_replace("{history_title}", $title, $form);
        $form=str_replace("{history_count}", $n, $form);
        $form=str_replace("{history_range}", $list, $form);
        return $form;
    }

    function showCatalogLogsArt($group_id, $art_id) { $db=DbSingleton::getTokoDb();
        $status = 0; $date = ""; $user_id = 0;
        $r = $db->query("SELECT * FROM `T2_TREE_ARTS` WHERE `GROUP_ID`='$group_id' AND `ART_ID`='$art_id' LIMIT 1;"); $n = $db->num_rows($r);
        if ($n > 0) {
            $status = 1;
            $date = $db->result($r, 0, "DATE");
            $user_id = $db->result($r, 0, "USER_ID");
        }
        return array("status"=>$status, "date"=>$date, "user_id"=>$user_id);
    }

    function getCatalogLogsCount($group_id) { $db=DbSingleton::getTokoDb();
        $r=$db->query("SELECT COUNT(`ART_ID`) as count_arts FROM `T2_TREE_ARTS` WHERE `GROUP_ID`='$group_id';");$n=$db->num_rows($r);
        $count_arts = $db->result($r, 0, "count_arts");
        if ($n==0) $count_arts=0;
        return $count_arts;
    }

    function dropCatalogPartsArtsGroup($group_id, $date, $user_id) { $db=DbSingleton::getTokoDb();
        $r=$db->query("SELECT COUNT(`ART_ID`) as count_arts FROM `T2_TREE_ARTS` WHERE `GROUP_ID`='$group_id' AND `DATE`='$date' AND `USER_ID`='$user_id';"); $n=$db->num_rows($r);
        $count_art=$db->result($r, 0, "count_arts");
        if ($n==0) $count_art=0;
        $db->query("DELETE FROM `T2_TREE_ARTS` WHERE `GROUP_ID`='$group_id' AND `DATE`='$date' AND `USER_ID`='$user_id';");
        return $count_art;
    }

    function dropCatalogPartsArts($group_id, $art_id) { $db=DbSingleton::getTokoDb();
        $db->query("DELETE FROM `T2_TREE_ARTS` WHERE `GROUP_ID`='$group_id' AND `ART_ID`='$art_id' LIMIT 1;");
        return true;
    }

    /*==== /HISTORY ====*/

    /*==== ADD ====*/
    function showCatalogPartsAddCard($head_id, $cat_id, $group_id) {
        $form_htm=RD."/tpl/catalog_parts/add.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $form=str_replace("{select_tree_head}", $this->getCatalogHeadList($head_id), $form);
        $form=str_replace("{select_tree_cat}", $this->getCatalogCatList($cat_id), $form);
        $form=str_replace("{select_tree_group}", $this->getCatalogGroupList($group_id), $form);
        return $form;
    }

    function getCatalogHeadList($head_id_sel = 0) { $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT * FROM `T2_TREE_HEAD` ORDER BY `TEX_RU`;"); $n = $db->num_rows($r);
        $list = "";
        for ($i=1; $i<=$n; $i++) {
            $head_id = $db->result($r, $i-1, "HEAD_ID");
            $text = $db->result($r, $i-1, "TEX_RU");
            if ($head_id==$head_id_sel) $sel = "selected"; else $sel = "";
            $list.="<option value='$head_id' $sel>$text</option>";
        }
        return $list;
    }

    function getCatalogCatList($cat_id_sel = 0) { $db = DbSingleton::getTokoDb();
        $list = "";
        $r = $db->query("SELECT t2c.CAT_ID, t2c.TEX_RU FROM `T2_TREE_CAT` t2c
        GROUP BY t2c.CAT_ID ORDER BY t2c.TEX_RU;"); $n = $db->num_rows($r);
        for ($i=1; $i<=$n; $i++) {
            $cat_id = $db->result($r, $i-1, "CAT_ID");
            $text = $db->result($r, $i-1, "TEX_RU");
            if ($cat_id==$cat_id_sel) $sel = "selected"; else $sel = "";
            $list.="<option value='$cat_id' $sel>$text</option>";
        }
        return $list;
    }

    function getCatalogGroupList($group_id_sel = 0) { $db = DbSingleton::getTokoDb();
        $list = "";
        $r = $db->query("SELECT t2g.GROUP_ID, t2g.TEX_RU FROM `T2_TREE_GROUP` t2g
        GROUP BY t2g.GROUP_ID ORDER BY t2g.TEX_RU;"); $n = $db->num_rows($r);
        for ($i=1; $i<=$n; $i++) {
            $group_id = $db->result($r, $i-1, "GROUP_ID");
            $text = $db->result($r, $i-1, "TEX_RU");
            if ($group_id==$group_id_sel) $sel = "selected"; else $sel = "";
            $list.="<option value='$group_id' $sel>$text</option>";
        }
        return $list;
    }

    function saveCatalogPartsAddCard($head_id, $cat_id, $group_id) { $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT * FROM `T2_TREE_HCG` WHERE `HEAD_ID`='$head_id' AND `CAT_ID`='$cat_id' AND `GROUP_ID`='$group_id' LIMIT 1;"); $n = $db->num_rows($r);
        if ($n==0) {
            $db->query("INSERT INTO `T2_TREE_HCG` (`HEAD_ID`, `CAT_ID`, `GROUP_ID`) VALUES ('$head_id', '$cat_id', '$group_id');");
        }
        return $n;
    }

    function showCatalogItem($type, $item_id) { $db = DbSingleton::getTokoDb();
        $table=""; $table_id=0; $status_auto=0; $description_ru=$description_ua=$description_en=""; $reviewed=0;
        if ($type=="head")  {$table="T2_TREE_HEAD"; $table_id="HEAD_ID";}
        if ($type=="cat")   {$table="T2_TREE_CAT"; $table_id="CAT_ID";}
        if ($type=="group") {$table="T2_TREE_GROUP"; $table_id="GROUP_ID";}
        $r = $db->query("SELECT * FROM `$table` WHERE `$table_id`='$item_id' LIMIT 1;");
        $text_ru = $db->result($r, 0, "TEX_RU");
        $text_ua = $db->result($r, 0, "TEX_UA");
        $text_en = $db->result($r, 0, "TEX_EN");
        $text_link = $db->result($r, 0, "TEX_LINK");
        $status = $db->result($r, 0, "STATUS");
        if ($type=="group") {
            $description_ru = $db->result($r, 0, "DESCRIPTION_RU");
            $description_ua = $db->result($r, 0, "DESCRIPTION_UA");
            $description_en = $db->result($r, 0, "DESCRIPTION_EN");
            $status_auto = $db->result($r, 0, "STATUS_AUTO");
            $reviewed = $db->result($r, 0, "REVIEWED");
        }
        return array("text_ru"=>$text_ru, "text_ua"=>$text_ua, "text_en"=>$text_en, "text_link"=>$text_link, "status"=>$status, "status_auto"=>$status_auto, "reviewed"=>$reviewed, "description_ru"=>$description_ru, "description_ua"=>$description_ua, "description_en"=>$description_en);
    }

    function addCatalogItem($type, $text_ru, $text_ua, $text_en, $text_link, $status, $status_auto, $reviewed, $description_ru, $description_ua, $description_en) { $db = DbSingleton::getTokoDb();
        $table=""; $table_id=0;
        if ($type=="head")  {$table="T2_TREE_HEAD"; $table_id="HEAD_ID";}
        if ($type=="cat")   {$table="T2_TREE_CAT"; $table_id="CAT_ID";}
        if ($type=="group") {$table="T2_TREE_GROUP"; $table_id="GROUP_ID";}
        if ($table!="") {
            $r=$db->query("SELECT MAX(`$table_id`) as mid FROM `$table`;"); $item_id=0+$db->result($r,0,"mid")+1;
            $db->query("INSERT INTO `$table` (`$table_id`, `TEX_RU`, `TEX_UA`, `TEX_EN`, `TEX_LINK`, `STATUS`) VALUES ('$item_id', '$text_ru', '$text_ua', '$text_en', '$text_link', '$status');");
            if ($type=="group") {
                $db->query("UPDATE `$table` SET `STATUS_AUTO`='$status_auto', `REVIEWED`='$reviewed', `DESCRIPTION_RU`='$description_ru', `DESCRIPTION_UA`='$description_ua', `DESCRIPTION_EN`='$description_en' WHERE `$table_id`='$item_id' LIMIT 1;");
            }
        }
        return true;
    }

    function editCatalogItem($type, $item_id, $text_ru, $text_ua, $text_en, $text_link, $status, $status_auto, $reviewed, $description_ru, $description_ua, $description_en) { $db = DbSingleton::getTokoDb();
        $table=""; $table_id=0;
        if ($type=="head")  { $table = "T2_TREE_HEAD"; $table_id = "HEAD_ID"; }
        if ($type=="cat")   { $table = "T2_TREE_CAT"; $table_id = "CAT_ID"; }
        if ($type=="group") { $table = "T2_TREE_GROUP"; $table_id = "GROUP_ID"; }
        if ($table!="") {
            $db->query("UPDATE `$table` SET `TEX_RU`='$text_ru', `TEX_UA`='$text_ua', `TEX_EN`='$text_en', `TEX_LINK`='$text_link', `STATUS`='$status' WHERE `$table_id`='$item_id' LIMIT 1;");
            if ($type=="group") {
                $db->query("UPDATE `$table` SET `STATUS_AUTO`='$status_auto', `REVIEWED`='$reviewed', `DESCRIPTION_RU`='$description_ru', `DESCRIPTION_UA`='$description_ua', `DESCRIPTION_EN`='$description_en' WHERE `$table_id`='$item_id' LIMIT 1;");
            }
        }
        return true;
    }

    function dropCatalogItem($type, $item_id) { $db = DbSingleton::getTokoDb();
        $table = ""; $table_id = 0;
        if ($type=="head")  { $table = "T2_TREE_HEAD"; $table_id = "HEAD_ID"; }
        if ($type=="cat")   { $table = "T2_TREE_CAT"; $table_id = "CAT_ID"; }
        if ($type=="group") { $table = "T2_TREE_GROUP"; $table_id = "GROUP_ID"; }
        $r = $db->query("SELECT `TEX_RU` FROM `$table` WHERE `$table_id`='$item_id' LIMIT 1;");
        $text_ru = $db->result($r, 0, "TEX_RU");
        $db->query("DELETE FROM `$table` WHERE `$table_id`='$item_id' LIMIT 1;");
        return $text_ru;
    }

    /*==== /ADD ====*/

//DELETE FROM `T2_TREE_NEW`
//WHERE `ART_ID` in (SELECT DISTINCT `ART_ID` FROM `T2_TREE_ARTS`)

}