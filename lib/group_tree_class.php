<?php

class group_tree
{

    function showGroupTreeHeaders()
    {
        $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT `HEAD_ID`, `TEX_RU` FROM `T2_GROUP_TREE_HEAD` WHERE 1;");
        $n = $db->num_rows($r);
        $list = "<ul style='list-style:none; padding:0;'>";
        for ($i = 1; $i <= $n; $i++) {
            $head_id = $db->result($r, $i - 1, "HEAD_ID");
            $text = $db->result($r, $i - 1, "TEX_RU");
            $header_list = $this->getGroupTreeStr($head_id);
            $list .= "<li>
				<div class='tree-head pointer'>
					<i class='fa fa-eye' onclick='showGroupTreeHeadCard(\"$head_id\")'></i> 
					<i class='fa fa-plus' onclick='addGroupTreeHeadStr(\"$head_id\")'></i>
					$head_id. $text
				</div>
				<div class='tree-list dnone'>$header_list</div>
			</li>";
        }
        $list .= "</ul>";
        return $list;
    }

    function addGroupTreeHeadStr($head_id)
    {
        $form = "";
        $form_htm = RD . "/tpl/group_tree_head_str_card.htm";
        if (file_exists("$form_htm")) {
            $form = file_get_contents($form_htm);
        }
        $head_list = $this->getTreeHeadersList($head_id);
        $tree_list = $this->getTreeGroupList();
        $position_list = $this->getPositionList();
        $category_list = $this->getCategoryList($head_id);
        $form = str_replace("{group_id}", "0", $form);
        $form = str_replace("{head_id}", $head_id, $form);
        $form = str_replace("{head_list}", $head_list, $form);
        $form = str_replace("{tree_list}", $tree_list, $form);
        $form = str_replace("{category_list}", $category_list, $form);
        $form = str_replace("{position_list}", $position_list, $form);
        $form = str_replace("{group_img}", "https://toko.ua/images/no-photo.png", $form);
        $form = str_replace("{disp_text_ru}", "", $form);
        $form = str_replace("{disp_text_ua}", "", $form);
        $form = str_replace("{disp_text_en}", "", $form);
        $form = str_replace("{disp_text_link}", "", $form);
        $form = str_replace("{disabled}", "disabled", $form);
        return $form;
    }

    function showGroupTreeHeadStr($id)
    {
        $db = DbSingleton::getTokoDb();
        $form = "";
        $form_htm = RD . "/tpl/group_tree_head_str_card.htm";
        if (file_exists("$form_htm")) {
            $form = file_get_contents($form_htm);
        }
        $r = $db->query("SELECT * FROM `T2_GROUP_TREE_STR` WHERE `ID` = $id LIMIT 1;");
        $str_id = $db->result($r, 0, "STR_ID");
        $head_id = $db->result($r, 0, "HEAD_ID");
        $position = $db->result($r, 0, "POSITION");
        $disp_text_ru = $db->result($r, 0, "TEX_RU");
        $disp_text_ua = $db->result($r, 0, "TEX_UA");
        $disp_text_en = $db->result($r, 0, "TEX_EN");
        $disp_text_link = $db->result($r, 0, "TEX_LINK");
        $cat_id = $db->result($r, 0, "CAT_ID");
        $IMAGES = $db->result($r, 0, "IMAGES");
        $group_img = ($IMAGES == "") ? "https://toko.ua/images/no-photo.png" : "https://toko.ua/uploads/images/group_tree_str/$IMAGES";
        $head_list = $this->getTreeHeadersList($head_id);
        $tree_list = $this->getTreeGroupList($str_id);
        $position_list = $this->getPositionList($position);
        $category_list = $this->getCategoryList($head_id, $cat_id);
        $form = str_replace("{group_id}", $id, $form);
        $form = str_replace("{head_id}", $head_id, $form);
        $form = str_replace("{head_list}", $head_list, $form);
        $form = str_replace("{tree_list}", $tree_list, $form);
        $form = str_replace("{category_list}", $category_list, $form);
        $form = str_replace("{position_list}", $position_list, $form);
        $form = str_replace("{group_img}", $group_img, $form);
        $form = str_replace("{disp_text_ru}", $disp_text_ru, $form);
        $form = str_replace("{disp_text_ua}", $disp_text_ua, $form);
        $form = str_replace("{disp_text_en}", $disp_text_en, $form);
        $form = str_replace("{disp_text_link}", $disp_text_link, $form);
        return $form;
    }

    function showGroupTreeHeadCategory($cat_id)
    {
        $db = DbSingleton::getTokoDb();
        $form = "";
        $form_htm = RD . "/tpl/group_tree_head_category_card.htm";
        if (file_exists("$form_htm")) {
            $form = file_get_contents($form_htm);
        }
        if ($cat_id == "0") {
            $head_list = $this->getTreeHeadersList();
            $position_list = $this->getPositionList();
            $form = str_replace("{cat_id}", $cat_id, $form);
            $form = str_replace("{head_id}", "0", $form);
            $form = str_replace("{head_list}", $head_list, $form);
            $form = str_replace("{position_list}", $position_list, $form);
            $form = str_replace("{disp_text_ru}", "", $form);
            $form = str_replace("{disp_text_ua}", "", $form);
            $form = str_replace("{disp_text_en}", "", $form);
            $form = str_replace("{disabled}", "disabled", $form);
        } else {
            $r = $db->query("SELECT * FROM `T2_GROUP_TREE_CATEGORY` WHERE `CAT_ID` = $cat_id LIMIT 1;");
            $head_id = $db->result($r, 0, "HEAD_ID");
            $position = $db->result($r, 0, "POSITION");
            $disp_text_ru = $db->result($r, 0, "TEX_RU");
            $disp_text_ua = $db->result($r, 0, "TEX_UA");
            $disp_text_en = $db->result($r, 0, "TEX_EN");
            $head_list = $this->getTreeHeadersList($head_id);
            $position_list = $this->getPositionList($position);
            $form = str_replace("{cat_id}", $cat_id, $form);
            $form = str_replace("{head_id}", $head_id, $form);
            $form = str_replace("{head_list}", $head_list, $form);
            $form = str_replace("{position_list}", $position_list, $form);
            $form = str_replace("{disp_text_ru}", $disp_text_ru, $form);
            $form = str_replace("{disp_text_ua}", $disp_text_ua, $form);
            $form = str_replace("{disp_text_en}", $disp_text_en, $form);
        }
        return $form;
    }

    function saveGroupTreeHeadCategoryCard($cat_id, $head_id, $position, $disp_text_ru, $disp_text_ua, $disp_text_en)
    {
        $db = DbSingleton::getTokoDb();
        if ($cat_id == "0") {
            $db->query("INSERT INTO `T2_GROUP_TREE_CATEGORY` (`HEAD_ID`, `POSITION`, `TEX_RU`, `TEX_UA`, `TEX_EN`) VALUES ('$head_id','$position','$disp_text_ru','$disp_text_ua','$disp_text_en');");
        } else {
            $db->query("UPDATE `T2_GROUP_TREE_CATEGORY` SET `HEAD_ID`='$head_id', `POSITION`='$position', `TEX_RU`='$disp_text_ru', `TEX_UA`='$disp_text_ua', `TEX_EN`='$disp_text_en' WHERE `CAT_ID` = $cat_id;");
        }
        $answer = 1; $err = "";
        return array($answer, $err);
    }

    function dropGroupTreeHeadCategory($cat_id)
    {
        $db = DbSingleton::getTokoDb();
        $answer = 0;
        $err = "Помилка видалення даних!";
        if ($cat_id > 0) {
            $db->query("DELETE FROM `T2_GROUP_TREE_CATEGORY` WHERE `CAT_ID` = $cat_id;");
            $answer = 1;
            $err = "";
        }
        return array($answer, $err);
    }

    function saveGroupTreeHeadStrCard($id, $head_id, $str_id, $position, $category, $disp_text_ru, $disp_text_ua, $disp_text_en, $disp_text_link)
    {
        $db = DbSingleton::getTokoDb();
        if ($id == "0") {
            $db->query("INSERT INTO `T2_GROUP_TREE_STR` (`HEAD_ID`, `STR_ID`, `POSITION`, `CAT_ID`, `TEX_RU`, `TEX_UA`, `TEX_EN`, `TEX_LINK`) VALUES ('$head_id','$str_id','$position','$category','$disp_text_ru','$disp_text_ua','$disp_text_en','$disp_text_link');");
        } else {
            $db->query("UPDATE `T2_GROUP_TREE_STR` SET `HEAD_ID`='$head_id', `STR_ID`='$str_id', `POSITION`='$position', `CAT_ID`='$category', `TEX_RU`='$disp_text_ru', `TEX_UA`='$disp_text_ua', `TEX_EN`='$disp_text_en', `TEX_LINK`='$disp_text_link' WHERE `ID` = $id;");
        }
        $answer = 1; $err = "";
        return array($answer, $err);
    }

    function dropGroupTreeHeadStr($id)
    {
        $db = DbSingleton::getTokoDb();
        $answer = 0;
        $err = "Помилка видалення даних!";
        if ($id > 0) {
            $db->query("DELETE FROM `T2_GROUP_TREE_STR` WHERE `ID` = $id;");
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

    function getTreeHeadersList($ch = "0")
    {
        $db = DbSingleton::getTokoDb();
        $list = "<option value='0'>-Не выбрано-</option>";
        $r = $db->query("SELECT `HEAD_ID`, `TEX_RU` FROM `T2_GROUP_TREE_HEAD` WHERE 1;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $head_id = $db->result($r, $i - 1, "HEAD_ID");
            $text = $db->result($r, $i - 1, "TEX_RU");
            $selected = ($ch == $head_id) ? "selected" : "";
            $list .= "<option value='$head_id' $selected>$head_id - $text</option>";
        }
        return $list;
    }

    function getTreeGroupList($ch = "0")
    {
        $db = DbSingleton::getTokoDb();
        $list = "";
        $r = $db->query("SELECT `STR_ID`, `DISP_TEXT` FROM `T2_GROUP_TREE` WHERE `LNG_ID` = 16;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $str_id = $db->result($r, $i - 1, "STR_ID");
            $text = $db->result($r, $i - 1, "DISP_TEXT");
            $selected = ($ch == $str_id) ? "selected" : "";
            $list .= "<option value='$str_id' $selected>$str_id - $text</option>";
        }
        return $list;
    }

    function getCategoryList($HEAD_ID, $ch = "0")
    {
        $db = DbSingleton::getTokoDb();
        $list = "<option value='0'>-Не выбрано-</option>";
        $r = $db->query("SELECT `CAT_ID`, `TEX_RU` FROM `T2_GROUP_TREE_CATEGORY` WHERE `HEAD_ID` = $HEAD_ID;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $cat_id = $db->result($r, $i - 1, "CAT_ID");
            $text = $db->result($r, $i - 1, "TEX_RU");
            $selected = ($ch == $cat_id) ? "selected" : "";
            $list .= "<option value='$cat_id' $selected>$cat_id - $text</option>";
        }
        return $list;
    }

    function getPositionList($ch = "0")
    {
        $list = "<option value='0'>-Не выбрано-</option>";
        $n = 100;
        for ($i = 1; $i <= $n; $i++) {
            $selected = ($ch == $i) ? "selected" : "";
            $list .= "<option value='$i' $selected>$i</option>";
        }
        return $list;
    }

    function getGroupTreeStr($head_id)
    {
        $db = DbSingleton::getTokoDb();
        $arr = [];
        $list = "";
        $r = $db->query("SELECT cs.* 
        FROM `T2_GROUP_TREE_STR` cs 
            LEFT OUTER JOIN `T2_GROUP_TREE_CATEGORY` cat on cat.CAT_ID=cs.CAT_ID
		WHERE cs.HEAD_ID = $head_id 
		ORDER BY cat.POSITION, cs.POSITION;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $ID = $db->result($r, $i - 1, "ID");
            $cat_id = $db->result($r, $i - 1, "CAT_ID");
            $DISP_TEXT = $db->result($r, $i - 1, "TEX_RU");
            $position = $db->result($r, $i - 1, "POSITION");
            $IMAGES = $db->result($r, $i - 1, "IMAGES");
            $arr[$cat_id][$i] = ["text" => $DISP_TEXT, "group" => $ID, "position" => $position, "images" => $IMAGES];
        }
        foreach ($arr as $key => $value) {
            $cat_name = $this->getCatName($key);
            $cat_pos = $this->getCatPos($key);
            $list .= "<div class=\"tree-category\"><i class='fa fa-pencil pointer' onclick='showGroupTreeHeadCategory(\"$key\")'> $cat_name ($cat_pos)</i></div>";
            $list .= "<ul>";
            foreach ($value as $v) {
                $tex = $v["text"];
                $group = $v["group"];
                $position = $v["position"];
                $images = $v["images"];
                $img_icon = ($images == "") ? "<i class='fa fa-eye-slash'></i>" : "";
                $list .= "<li><i class='fa fa-pencil pointer' onclick='showGroupTreeHeadStr(\"$group\");'> $tex ($position) $img_icon </i></li>";
            }
            $list .= "</ul>";
        }
        if ($n == 0) {
            $list = "Пусто";
        }
        return $list;
    }

    function getCatName($cat_id)
    {
        $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT `TEX_RU` FROM `T2_GROUP_TREE_CATEGORY` WHERE `CAT_ID` = $cat_id LIMIT 1;");
        return $db->result($r, 0, "TEX_RU");
    }

    function getCatPos($cat_id)
    {
        $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT `POSITION` FROM `T2_GROUP_TREE_CATEGORY` WHERE `CAT_ID` = $cat_id LIMIT 1;");
        return $db->result($r, 0, "POSITION");
    }

    function showGroupTreeHead($head_id)
    {
        $db = DbSingleton::getTokoDb();
        $form = "";
        $form_htm = RD . "/tpl/group_tree_head_card.htm";
        if (file_exists("$form_htm")) {
            $form = file_get_contents($form_htm);
        }
        if ($head_id == "0") {
            $r1 = $db->query("SELECT MAX(`HEAD_ID`) as max_head FROM `T2_GROUP_TREE_HEAD`;");
            $head_id = 0 + $db->result($r1, 0, "max_head") + 1;
            $disp_text_ru = $disp_text_ua = $disp_text_en = "";
            $head_status = 0;
            $head_img = "https://toko.ua/images/no-photo.png";
        } else {
            $r = $db->query("SELECT * FROM `T2_GROUP_TREE_HEAD` WHERE `HEAD_ID` = $head_id LIMIT 1;");
            $disp_text_ru = $db->result($r, 0, "TEX_RU");
            $disp_text_ua = $db->result($r, 0, "TEX_UA");
            $disp_text_en = $db->result($r, 0, "TEX_EN");
            $images = $db->result($r, 0, "IMAGES");
            $head_img = ($images == "") ? "https://toko.ua/images/no-photo.png" : "https://toko.ua/uploads/images/group_tree_head/$images";
            $head_status = $db->result($r, 0, "STATUS");
        }
        $form = str_replace("{head_id}", $head_id, $form);
        $form = str_replace("{disp_text_ru}", $disp_text_ru, $form);
        $form = str_replace("{disp_text_ua}", $disp_text_ua, $form);
        $form = str_replace("{disp_text_en}", $disp_text_en, $form);
        $form = str_replace("{head_image}", $head_img, $form);
        $form = str_replace("{head_status}", ($head_status) ? "checked" : "", $form);
        return $form;
    }

    function saveGroupTreeHead($head_id, $disp_text_ru, $disp_text_ua, $disp_text_en, $head_status)
    {
        $db = DbSingleton::getTokoDb();
        $answer = 0;
        $err = "Помилка збереження даних!";
        $head_status = ($head_status) ? "1" : "0";
        if ($head_id > 0) {
            $r = $db->query("SELECT * FROM `T2_GROUP_TREE_HEAD` WHERE `HEAD_ID` = $head_id;");
            $n = $db->num_rows($r);
            if ($n > 0) {
                $db->query("UPDATE `T2_GROUP_TREE_HEAD` SET `TEX_RU` = '$disp_text_ru', `TEX_UA` = '$disp_text_ua', `TEX_EN` = '$disp_text_en', `STATUS` = $head_status WHERE `HEAD_ID` = $head_id;");
            } else {
                $db->query("INSERT INTO `T2_GROUP_TREE_HEAD` (`HEAD_ID`, `TEX_RU`, `TEX_UA`, `TEX_EN`) VALUES ('$head_id', '$disp_text_ru', '$disp_text_ua', '$disp_text_en', '$head_status');");
            }
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

    function dropGroupTreeHead($head_id)
    {
        $db = DbSingleton::getTokoDb();
        $answer = 0;
        $err = "Помилка видалення даних!";
        if ($head_id > 0) {
            $db->query("DELETE FROM `T2_GROUP_TREE_HEAD` WHERE `HEAD_ID` = $head_id;");
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

    function showGroupTree()
    {
        $db = DbSingleton::getTokoDb();
        $menu_det = $tree = "";
        $lvl = 0;
        $td_array = $parent_fare = $position_fare = [];
        $form = "";
        $form_htm = RD . "/tpl/group_tree.htm";
        if (file_exists("$form_htm")) {
            $form = file_get_contents($form_htm);
        }

        $menu_det .= "<div class=\"input-group border0\">
		<input type=\"search\" id=\"my-search\" class=\"my-search form-control\" placeholder=\"Поиск по категориям\" style='margin-bottom: 15px;'>
		</div><ul id=\"my-tree\" class=\"tf-tree\">";

        $r = $db->query("SELECT * FROM `T2_GROUP_TREE` WHERE `LNG_ID` = 16;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $str_id = $db->result($r, $i - 1, "STR_ID");
            $str_id_parrent = $db->result($r, $i - 1, "STR_ID_PARENT");
            if ($str_id_parrent == "") {
                $str_id_parrent = 0;
            }
            $str_level = $db->result($r, $i - 1, "STR_LEVEL");
            $tex_text = $db->result($r, $i - 1, "DISP_TEXT");
            $position = $db->result($r, $i - 1, "POSITION");
            $child = $this->getTecGroupTreeChilds($str_id);
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

        for ($i = 1; $i <= 30; $i++) {
            $lvl += 1;
            foreach ($td_array as $elm) {
                if ($elm["level"] == $lvl) {
                    $str_id2 = $elm["id_tree"];
                    $str = "<li><div>";
                    if ($elm["child"] > 0) {
                        $str .= "<i class='fa fa-eye' onclick='showGroupTreeCard(\"$str_id2\");'></i> <a>" . $elm["name"] . "</a>";
                    }
                    if ($elm["child"] == 0) {
                        $str .= "<a onclick='showGroupTreeCard(\"$str_id2\");'>" . $elm["name"] . "</a>";
                    }
                    $str .= "</div>";
                    if ($elm["child"] > 0) {
                        $str .= "\n<ul>\n{p" . $elm["id_tree"] . "}</ul>\n";
                    }
                    $str .= "</li>\n";
                    if ($lvl == 2) {
                        $tree .= $str;
                    }
                    if ($lvl > 2) {
                        $tree = str_replace("{p" . $elm["id_parent"] . "}", $str . "{p" . $elm["id_parent"] . "}", $tree);
                    }
                }
            }
        }
        foreach ($td_array as $elm) {
            $tree = str_replace("{p" . $elm["id_parent"] . "}", "", $tree);
            $tree = str_replace("{p" . $elm["id_tree"] . "}", "", $tree);
        }
        $menu_det .= $tree . "</ul>";

        $form = str_replace("{group_tree_range}", $menu_det, $form);
        $form = str_replace("{group_tree_head}", $this->showGroupTreeHeaders(), $form);
        return $form;
    }

    function showGroupTreeCard($STR_ID)
    {
        $db = DbSingleton::getTokoDb();
        $POSITION = $STR_ID_PARENT = 0;
        $tex_text_ru = $tex_text_ua = $tex_text_en = $disp_text_ru = $disp_text_ua = $disp_text_en = "";
        $form = "";
        $form_htm = RD . "/tpl/group_tree_card.htm";
        if (file_exists("$form_htm")) {
            $form = file_get_contents($form_htm);
        }
        $r = $db->query("SELECT * FROM `T2_GROUP_TREE` WHERE `STR_ID` = $STR_ID;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $STR_ID_PARENT = $db->result($r, $i - 1, "STR_ID_PARENT");
            $LNG_ID = $db->result($r, $i - 1, "LNG_ID");
            $TEX_TEXT = $db->result($r, $i - 1, "TEX_TEXT");
            $DISP_TEXT = $db->result($r, $i - 1, "DISP_TEXT");
            $POSITION = $db->result($r, $i - 1, "POSITION");
            if ($LNG_ID == 16) {
                $tex_text_ru = $TEX_TEXT;
                $disp_text_ru = $DISP_TEXT;
            }
            if ($LNG_ID == 41) {
                $tex_text_ua = $TEX_TEXT;
                $disp_text_ua = $DISP_TEXT;
            }
            if ($LNG_ID == 4) {
                $tex_text_en = $TEX_TEXT;
                $disp_text_en = $DISP_TEXT;
            }
        }
        $form = str_replace("{str_id}", $STR_ID, $form);
        $form = str_replace("{position}", $POSITION, $form);
        $form = str_replace("{tex_text_ru}", $tex_text_ru, $form);
        $form = str_replace("{tex_text_ua}", $tex_text_ua, $form);
        $form = str_replace("{tex_text_en}", $tex_text_en, $form);
        $form = str_replace("{disp_text_ru}", $disp_text_ru, $form);
        $form = str_replace("{disp_text_ua}", $disp_text_ua, $form);
        $form = str_replace("{disp_text_en}", $disp_text_en, $form);
        $form = str_replace("{position_list}", $this->showParrentPositions($STR_ID, $STR_ID_PARENT), $form);
        return $form;
    }

    function saveGroupTreeCard($str_id, $position, $disp_text_ru, $disp_text_ua, $disp_text_en)
    {
        $db = DbSingleton::getTokoDb();
        $answer = 0;
        $err = "Помилка збереження даних!";
        $position = intval($position);
        if ($str_id > 0) {
            $db->query("UPDATE `T2_GROUP_TREE` SET `POSITION`='$position' WHERE `STR_ID`='$str_id';");
            $db->query("UPDATE `T2_GROUP_TREE` SET `DISP_TEXT`='$disp_text_ru' WHERE `STR_ID`='$str_id' AND `LNG_ID`=16;");
            $db->query("UPDATE `T2_GROUP_TREE` SET `DISP_TEXT`='$disp_text_ua' WHERE `STR_ID`='$str_id' AND `LNG_ID`=41;");
            $db->query("UPDATE `T2_GROUP_TREE` SET `DISP_TEXT`='$disp_text_en' WHERE `STR_ID`='$str_id' AND `LNG_ID`=4;");
            $answer = 1;
            $err = "";
        }
        return array($answer, $err);
    }

    function showParrentPositions($str_id, $parent_id)
    {
        $db = DbSingleton::getTokoDb();
        $list = "";
        $r = $db->query("SELECT COUNT(`STR_ID`) as kol FROM `T2_GROUP_TREE` WHERE `STR_ID_PARENT`='$parent_id' AND `LNG_ID`=16;");
        $r2 = $db->query("SELECT `POSITION` FROM `T2_GROUP_TREE` WHERE `STR_ID`='$str_id' AND `LNG_ID`=16;");
        $position = $db->result($r2, 0, "POSITION");
        $kol = intval($db->result($r, 0, "kol"));
        for ($i = 0; $i < $kol; $i++) {
            $selected = ($i == $position) ? "selected='selected'" : "";
            $list .= "<option value='$i' $selected>$i</option>";
        }
        return $list;
    }

    function getTecGroupTreeChilds($str_id)
    {
        $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT COUNT(`STR_ID`) as kol FROM `T2_GROUP_TREE` WHERE `STR_ID_PARENT` = $str_id;");
        return intval($db->result($r, 0, "kol"));
    }

    function dropUploadPhotoForm($type_id, $id)
    {
        $db = DbSingleton::getTokoDb();
        $answer = 0;
        $err = "Помилка збереження даних!";
        $myFile = "";
        if ($id > 0) {
            if ($type_id == "group") {
                $r = $db->query("SELECT `IMAGES` FROM `T2_GROUP_TREE_STR` WHERE `ID`='$id';");
                $filename = $db->result($r, 0, "IMAGES");
                $db->query("UPDATE `T2_GROUP_TREE_STR` SET `IMAGES`='' WHERE `ID`='$id';");
                $myFile = RD . "/uploads/images/group_tree_str/$filename";
            }
            if ($type_id == "head") {
                $r = $db->query("SELECT `IMAGES` FROM `T2_GROUP_TREE_HEAD` WHERE `HEAD_ID`='$id';");
                $filename = $db->result($r, 0, "IMAGES");
                $db->query("UPDATE `T2_GROUP_TREE_HEAD` SET `IMAGES`='' WHERE `HEAD_ID`='$id';");
                $myFile = RD . "/uploads/images/group_tree_head/$filename";
            }
            unlink($myFile);
            $answer = 1;
            $err = "";
        }
        return array($answer, $err);
    }

    function showUploadDropzone($type_id, $str_id)
    {
        $form = "";
        $form_htm = RD . "/tpl/dropzone_upload_form.htm";
        if (file_exists("$form_htm")) {
            $form = file_get_contents($form_htm);
        }
        $form = str_replace("{group_type_id}", $type_id, $form);
        $form = str_replace("{group_str_id}", $str_id, $form);
        return $form;
    }

    /*==============================================================================*/

    function showTreeConsForm()
    {
        $form = "";
        $form_htm = RD . "/tpl/tree_constructor/form.htm";
        if (file_exists("$form_htm")) {
            $form = file_get_contents($form_htm);
        }
        $form = str_replace("{form_range}", $this->loadTreeCons(), $form);
        $form = str_replace("{form_header}", $this->loadTreeConsHeader(), $form);
        $form = str_replace("{form_view}", $this->loadTreeConsView(), $form);
        $form = str_replace("{form_cron_content}", $this->loadCatalogExist(), $form);
        return $form;
    }

    function getSelectHeaders($headers = [])
    {
        $db = DbSingleton::getTokoDb();
        $list = "<option value='0'>-Не вибрано-</option>";
        $r = $db->query("SELECT * FROM `T2_TREE_HEAD_EXIST` WHERE `STATUS`=1;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $head_id = $db->result($r, $i - 1, "HEAD_ID");
            $text = $db->result($r, $i - 1, "TEX_RU");
            if (!in_array($head_id, $headers)) {
                $list .= "<option value='$head_id'>$text</option>";
            }
        }
        return $list;
    }

    function getMaxPosition($head_id)
    {
        $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT MAX(`COL`) as max_col, MAX(`ROW`) as max_row FROM `T2_TREE_CONSTRUCTOR_STR` WHERE `HEAD_ID`='$head_id';");
        $max_col = $db->result($r, 0, "max_col") + 1;
        $max_row = $db->result($r, 0, "max_row") + 1;
        return array($max_col, $max_row);
    }

    function loadTreeConsView()
    {
        $db = DbSingleton::getTokoDb();
        $form = "";
        $form_htm = RD . "/tpl/tree_constructor/view.htm";
        if (file_exists("$form_htm")) {
            $form = file_get_contents($form_htm);
        }
        $list = "";
        $r = $db->query("SELECT * FROM `T2_TREE_CONSTRUCTOR` WHERE 1 ORDER BY `POSITION` ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $head_id = $db->result($r, $i - 1, "HEAD_ID");
            $head_name = $this->getHeadRowName($head_id);
            $list .= "<li>
                <a onclick='loadTreeConsViewList(\"$head_id\")'>$head_name</a>
            </li>";
        }
        $form = str_replace("{view_range}", $list, $form);
        $form = str_replace("{view_hide_range}", $this->loadTreeConsViewList(), $form);
        return $form;
    }

    function loadTreeConsViewList($head_id = 1)
    {
        $db = DbSingleton::getTokoDb();
        $list = "";
        $arr = [];
        $r = $db->query("SELECT * FROM `T2_TREE_CONSTRUCTOR_STR` WHERE `HEAD_ID`='$head_id' ORDER BY `COL` ASC, `ROW` ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $cat_id = $db->result($r, $i - 1, "CAT_ID");
            $col = $db->result($r, $i - 1, "COL");
            $row = $db->result($r, $i - 1, "ROW");
            $arr[$col][$row] = $cat_id;
        }
        list($max_col,) = $this->getMaxPosition($head_id);
        if ($n > 0) {
            $list = "<div class='tree-block'>";
            foreach ($arr as $col_id => $rows) {
                $list .= "<div class='tree-block__col' style='width: calc(100% / $max_col)'>";
                foreach ($rows as $row_id => $cat_id) {
                    $cat_name = $this->getCatRowName($cat_id);
                    $group_list = $this->getTreeConsGroupList($head_id, $cat_id);
                    $icon = "";
                    if ($cat_id == 0) {
                        $icon = "<i class='fa fa-circle' style='margin-right: 5px; color: #f44438'></i>";
                    }
                    $list .= "<div>
                        <div class='tree-item'>
                            <div class='tree-item-title'>
                                <button class='btn btn-xs' onclick='moveTreeConsCat(\"$head_id\",\"$cat_id\",\"up\");'><i class='fa fa-arrow-circle-up'></i></button>
                                <button class='btn btn-xs' onclick='moveTreeConsCat(\"$head_id\",\"$cat_id\",\"down\");'><i class='fa fa-arrow-circle-down'></i></button>
                                <button class='btn btn-xs' onclick='moveTreeConsCat(\"$head_id\",\"$cat_id\",\"left\");'><i class='fa fa-arrow-circle-left'></i></button>
                                <button class='btn btn-xs' onclick='moveTreeConsCat(\"$head_id\",\"$cat_id\",\"right\");'><i class='fa fa-arrow-circle-right'></i></button>
                                <br>
                                $icon$cat_name
                            </div>
                            <div class='tree-item-list'>$group_list</div>
                        </div>
                    </div>";
                }
                $list .= "</div>";
            }
            $list .= "</div>";
        }
        return $list;
    }

    function getTreeConsGroupList($head_id, $cat_id)
    {
        $db = DbSingleton::getTokoDb();
        $list = "";
        $r = $db->query("SELECT he.* FROM `T2_TREE_HCG_EXIST` he 
            LEFT JOIN `T2_TREE_GROUP_EXIST` ge ON ge.GROUP_ID = he.GROUP_ID
        WHERE he.`HEAD_ID`='$head_id' AND he.`CAT_ID`='$cat_id' AND ge.STATUS=1;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $group_id   = $db->result($r, $i - 1, "GROUP_ID");
            $group_name = $this->getGroupRowName($group_id);
            $list       .= "<div class='tree-item-list__element'>
                <a href='/'>$group_name</a>
            </div>";
        }
        if ($cat_id == 0) {
            $r = $db->query("SELECT he.* FROM `T2_TREE_HCG_EXIST` he 
                LEFT JOIN `T2_TREE_GROUP_EXIST` ge ON ge.GROUP_ID = he.GROUP_ID
            WHERE he.`HEAD_ID`='$head_id' AND he.`POPULAR`=1 AND ge.STATUS=1;");
            $n = $db->num_rows($r);
            for ($i = 1; $i <= $n; $i++) {
                $group_id   = $db->result($r, $i - 1, "GROUP_ID");
                $group_name = $this->getGroupRowName($group_id);
                $list       .= "<div class='tree-item-list__element'>
                    <a href='/'>$group_name</a>
                </div>";
            }
        }
        return $list;
    }

    function loadTreeConsHeader()
    {
        $db = DbSingleton::getTokoDb();
        $headers = [];
        $r = $db->query("SELECT * FROM `T2_TREE_CONSTRUCTOR` WHERE 1;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $head_id = $db->result($r, $i - 1, "HEAD_ID");
            array_push($headers, $head_id);
        }
        $form = "";
        $form_htm = RD . "/tpl/tree_constructor/header.htm";
        if (file_exists("$form_htm")) {
            $form = file_get_contents($form_htm);
        }
        $form = str_replace("{select_head}", $this->getSelectHeaders($headers), $form);
        return $form;
    }

    function checkHeadPopular($head_id)
    {
        $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT * FROM `T2_TREE_HCG_EXIST` WHERE `HEAD_ID`='$head_id' AND `POPULAR`=1 LIMIT 1;");
        $n = $db->num_rows($r);
        return ($n > 0);
    }

    function addHeadPopular($head_id)
    {
        $db = DbSingleton::getTokoDb();
        if ($this->checkHeadCatPopular($head_id)) {
            $db->query("DELETE FROM `T2_TREE_CONSTRUCTOR_STR` WHERE `HEAD_ID`='$head_id' AND `CAT_ID`=0 LIMIT 1;");
        } else {
            $db->query("INSERT INTO `T2_TREE_CONSTRUCTOR_STR` (`HEAD_ID`, `CAT_ID`, `COL`, `ROW`) VALUES ('$head_id', '0', '0', '0');");
        }
        return true;
    }

    function checkHeadCatPopular($head_id)
    {
        $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT * FROM `T2_TREE_CONSTRUCTOR_STR` WHERE `HEAD_ID`='$head_id' AND `CAT_ID`=0 LIMIT 1;");
        $n = $db->num_rows($r);
        return ($n > 0);
    }

    function loadTreeCons()
    {
        $db = DbSingleton::getTokoDb();
        $list = "";
        $r = $db->query("SELECT * FROM `T2_TREE_CONSTRUCTOR` WHERE 1 ORDER BY `POSITION` ASC;");
        $n = $db->num_rows($r);
        if ($n > 0) {
            $list .= "<div class='tree-row'>";
            for ($i = 1; $i <= $n; $i++) {
                $head_id = $db->result($r, $i - 1, "HEAD_ID");
                $head_name = $this->getHeadRowName($head_id);
                $check_form = "";
                if ($this->checkHeadPopular($head_id)) {
                    $checked = "";
                    if ($this->checkHeadCatPopular($head_id)) {
                        $checked = "checked='checked'";
                    }
                    $check_form = "<input class='btn-check' $checked type='checkbox' onchange='addHeadPopular(\"$head_id\")'>";
                }
                $list .= "<div class='tree-col'>
                <button class='btn btn-xs btn-info' onclick='addTreeConsCatForm(\"$head_id\")'><i class='fa fa-plus'></i></button>
                <button class='btn btn-xs btn-danger' onclick='dropTreeConsColumn(\"$head_id\")'><i class='fa fa-trash'></i></button>
                <button class='btn btn-xs' onclick='moveTreeConsColumn(\"$head_id\", 0)'><i class='fa fa-arrow-circle-left'></i></button>
                <button class='btn btn-xs' onclick='moveTreeConsColumn(\"$head_id\", 1)'><i class='fa fa-arrow-circle-right'></i></button>
                $check_form
                <h3>$head_name</h3>";
                $list .= $this->getTreeConsCat($head_id);
                $list .= "</div>";
            }
            $list .= "</div>";
        }
        return $list;
    }

    function getTreeConsCat($head_id)
    {
        $db = DbSingleton::getTokoDb();
        $list = "";
        $r = $db->query("SELECT * FROM `T2_TREE_CONSTRUCTOR_STR` WHERE `HEAD_ID`='$head_id' ORDER BY `COL` ASC, `ROW` ASC;");
        $n = $db->num_rows($r);
        if ($n > 0) {
            $list .= "<div>";
            for ($i = 1; $i <= $n; $i++) {
                $cat_id = $db->result($r, $i - 1, "CAT_ID");
                $cat_name = $this->getCatRowName($cat_id);
                $col = $db->result($r, $i - 1, "COL");
                $row = $db->result($r, $i - 1, "ROW");
                $list .= "<h4>
                    <button class='btn btn-xs' onclick='dropTreeConsCat(\"$head_id\",\"$cat_id\")'><i class='fa fa-minus'></i></button> 
                    <input type='number' id='cat_col_$cat_id' value='$col' style='width: 30px;'>
                    <input type='number' id='cat_row_$cat_id' value='$row' style='width: 30px;'>
                    <button class='btn btn-xs btn-primary' onclick='saveTreeConsCatPos(\"$head_id\",\"$cat_id\")'><i class='fa fa-save'></i></button>
                    $cat_name
                </h4>";
            }
            $list .= "</div>";
        }
        return $list;
    }

    function getHeadRowName($head_id)
    {
        $db = DbSingleton::getTokoDb();
        $name = "";
        $r = $db->query("SELECT * FROM `T2_TREE_HEAD_EXIST` WHERE `HEAD_ID`='$head_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n > 0) {
            $name = $db->result($r, 0, "TEX_RU");
        }
        return $name;
    }

    function getCatRowName($cat_id)
    {
        $db = DbSingleton::getTokoDb();
        $name = "";
        $r = $db->query("SELECT * FROM `T2_TREE_CAT_EXIST` WHERE `CAT_ID`='$cat_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n > 0) {
            $name = $db->result($r, 0, "TEX_RU");
        }
        if ($cat_id == 0) {
            $name = "Популярные товары";
        }
        return $name;
    }

    public function getGroupRowLink($group_id)
    {
        $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT `TEX_LINK` FROM `T2_TREE_GROUP_EXIST` WHERE `GROUP_ID`='$group_id' LIMIT 1;");
        return $db->result($r, 0, "TEX_LINK");
    }

    function getGroupRowName($group_id)
    {
        $db = DbSingleton::getTokoDb();
        $name = "";
        $r = $db->query("SELECT * FROM `T2_TREE_GROUP_EXIST` WHERE `GROUP_ID`='$group_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n > 0) {
            $name = $db->result($r, 0, "TEX_RU");
        }
        return $name;
    }

    function getGroupRowStatus($group_id)
    {
        $db = DbSingleton::getTokoDb();
        $status = 0; $status_auto = 0;
        $r = $db->query("SELECT `STATUS`, `STATUS_AUTO` FROM `T2_TREE_GROUP_EXIST` WHERE `GROUP_ID`='$group_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n > 0) {
            $status = $db->result($r, 0, "STATUS");
            $status_auto = $db->result($r, 0, "STATUS_AUTO");
        }
        return array($status, $status_auto);
    }

    function addTreeConsColumn($head_id)
    {
        $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT MAX(`POSITION`) as max_pos FROM `T2_TREE_CONSTRUCTOR` WHERE 1;");
        $max_pos = $db->result($r, 0, "max_pos") + 1;
        $db->query("INSERT INTO `T2_TREE_CONSTRUCTOR` (`HEAD_ID`, `POSITION`) VALUES ('$head_id', '$max_pos');");
        $answer = 1;
        $err = "";
        return array($answer, $err);
    }

    function dropTreeConsColumn($head_id)
    {
        $db = DbSingleton::getTokoDb();
        $db->query("DELETE FROM `T2_TREE_CONSTRUCTOR` WHERE `HEAD_ID`='$head_id' LIMIT 1;");
        $db->query("DELETE FROM `T2_TREE_CONSTRUCTOR_STR` WHERE `HEAD_ID`='$head_id';");
        $r = $db->query("SELECT * FROM `T2_TREE_CONSTRUCTOR` ORDER BY `POSITION` ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $head_id_sel = $db->result($r, $i - 1, "HEAD_ID");
            $db->query("UPDATE `T2_TREE_CONSTRUCTOR` SET `POSITION`='$i' WHERE `HEAD_ID`='$head_id_sel' LIMIT 1;");
        }
        $answer = 1;
        $err = "";
        return array($answer, $err);
    }

    function moveTreeConsColumn($head_id, $status)
    {
        $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT COUNT(`HEAD_ID`) as max_count FROM `T2_TREE_CONSTRUCTOR` WHERE 1;");
        $max_count = $db->result($r, 0, "max_count");
        $r = $db->query("SELECT * FROM `T2_TREE_CONSTRUCTOR` WHERE `HEAD_ID`='$head_id' LIMIT 1;");
        $position_old = $db->result($r, 0, "POSITION");
        if ($status == 0) {
            $position_new = $position_old - 1;
        } else {
            $position_new = $position_old + 1;
        }
        if ($position_new > 0 && $position_new <= $max_count) {
            $r = $db->query("SELECT * FROM `T2_TREE_CONSTRUCTOR` WHERE `POSITION`='$position_new' LIMIT 1;");
            $head_id_old = $db->result($r, 0, "HEAD_ID");
            $db->query("UPDATE `T2_TREE_CONSTRUCTOR` SET `POSITION`='$position_new' WHERE `HEAD_ID`='$head_id' LIMIT 1;");
            $db->query("UPDATE `T2_TREE_CONSTRUCTOR` SET `POSITION`='$position_old' WHERE `HEAD_ID`='$head_id_old' LIMIT 1;");
        }
        return true;
    }

    function addTreeConsCatForm($head_id)
    {
        $form = "";
        $form_htm = RD . "/tpl/tree_constructor/add.htm";
        if (file_exists("$form_htm")) {
            $form = file_get_contents($form_htm);
        }
        $form = str_replace("{select_cat}", $this->getTreeCatRowList($head_id), $form);
        $form = str_replace("{head_id}", $head_id, $form);
        return $form;
    }

    function getTreeCatRowList($head_id)
    {
        $db = DbSingleton::getTokoDb();
        $list = "";
        $cats = [];
        $r = $db->query("SELECT * FROM `T2_TREE_CONSTRUCTOR_STR` WHERE `HEAD_ID`='$head_id';");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $cat_id = $db->result($r, $i - 1, "CAT_ID");
            array_push($cats, $cat_id);
        }
        $r = $db->query("SELECT * FROM `T2_TREE_HCG_EXIST` WHERE `HEAD_ID`='$head_id' GROUP BY `CAT_ID` ORDER BY `POPULAR` DESC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $cat_id = $db->result($r, $i - 1, "CAT_ID");
            if (!in_array($cat_id, $cats)) {
                $cat_name = $this->getCatRowName($cat_id);
                $list .= "<option value='$cat_id'>$cat_name</option>";
            }
        }
        return $list;
    }

    function addTreeConsCat($head_id, $cat_id, $cat_col, $cat_row)
    {
        $db = DbSingleton::getTokoDb();
        $db->query("INSERT INTO `T2_TREE_CONSTRUCTOR_STR` (`HEAD_ID`, `CAT_ID`, `COL`, `ROW`) VALUES ('$head_id', '$cat_id', '$cat_col', '$cat_row');");
        $answer = 1;
        $err = "";
        return array($answer, $err);
    }

    function dropTreeConsCat($head_id, $cat_id)
    {
        $db = DbSingleton::getTokoDb();
        $db->query("DELETE FROM `T2_TREE_CONSTRUCTOR_STR` WHERE `HEAD_ID`='$head_id' AND `CAT_ID`='$cat_id' LIMIT 1;");
        $answer = 1;
        $err = "";
        return array($answer, $err);
    }

    function saveTreeConsCatPos($head_id, $cat_id, $cat_col, $cat_row)
    {
        $db = DbSingleton::getTokoDb();
        $db->query("UPDATE `T2_TREE_CONSTRUCTOR_STR` SET `COL`='$cat_col', `ROW`='$cat_row' WHERE `HEAD_ID`='$head_id' AND `CAT_ID`='$cat_id' LIMIT 1;");
        $answer = 1;
        $err = "";
        return array($answer, $err);
    }

    function moveTreeConsCat($head_id, $cat_id, $status)
    {
        $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT * FROM `T2_TREE_CONSTRUCTOR_STR` WHERE `HEAD_ID`='$head_id' AND `CAT_ID`='$cat_id' LIMIT 1;");
        $current_id = $db->result($r, 0, "ID");
        $current_col = $db->result($r, 0, "COL");
        $current_row = $db->result($r, 0, "ROW");
        $logs = "";
        if ($status == "up") {
            // row
            $r = $db->query("SELECT MAX(`ROW`) as swap_row FROM `T2_TREE_CONSTRUCTOR_STR` WHERE `HEAD_ID` = '$head_id' AND `ROW` < '$current_row' AND `COL` = '$current_col' LIMIT 1;");
            $swap_row = $db->result($r, 0, "swap_row");

            $r = $db->query("SELECT `ID` FROM `T2_TREE_CONSTRUCTOR_STR` WHERE `HEAD_ID` = '$head_id' AND `ROW` = '$swap_row' AND `COL` = '$current_col' LIMIT 1;");
            $swap_id = $db->result($r, 0, "ID");

            $logs .= "$swap_id -> $current_row \n $current_id -> $swap_row";
            if ($swap_row >= 0 && $swap_id > 0) {
                $db->query("UPDATE `T2_TREE_CONSTRUCTOR_STR` SET `ROW` = '$swap_row' WHERE `ID`='$current_id' LIMIT 1;");
                $db->query("UPDATE `T2_TREE_CONSTRUCTOR_STR` SET `ROW` = '$current_row' WHERE `ID`='$swap_id' LIMIT 1;");
            }
        }
        if ($status == "down") {
            // row
            $r = $db->query("SELECT MIN(`ROW`) as swap_row FROM `T2_TREE_CONSTRUCTOR_STR` WHERE `HEAD_ID` = '$head_id' AND `ROW` > '$current_row' AND `COL` = '$current_col' LIMIT 1;");
            $swap_row = $db->result($r, 0, "swap_row");

            $r = $db->query("SELECT `ID` FROM `T2_TREE_CONSTRUCTOR_STR` WHERE `HEAD_ID` = '$head_id' AND `ROW` = '$swap_row' AND `COL` = '$current_col' LIMIT 1;");
            $swap_id = $db->result($r, 0, "ID");

            $logs .= "$swap_id -> $current_row \n $current_id -> $swap_row";
            if ($swap_row >= 0 && $swap_id > 0) {
                $db->query("UPDATE `T2_TREE_CONSTRUCTOR_STR` SET `ROW` = '$swap_row' WHERE `ID`='$current_id' LIMIT 1;");
                $db->query("UPDATE `T2_TREE_CONSTRUCTOR_STR` SET `ROW` = '$current_row' WHERE `ID`='$swap_id' LIMIT 1;");
            }
        }
        if ($status == "left") {
            // col
            if ($current_col > 0) {
                $swap_col = $current_col - 1;
                $r = $db->query("SELECT MAX(`ROW`) as swap_row FROM `T2_TREE_CONSTRUCTOR_STR` WHERE `HEAD_ID` = '$head_id' AND `COL` = $swap_col LIMIT 1;");
                $swap_row = $db->result($r, 0, "swap_row") + 1;
                $db->query("UPDATE `T2_TREE_CONSTRUCTOR_STR` SET `ROW` = '$swap_row', `COL` = '$swap_col' WHERE `ID` = '$current_id' LIMIT 1;");
            }
        }
        if ($status == "right") {
            // col
            $swap_col = $current_col + 1;
            $r = $db->query("SELECT MAX(`ROW`) as swap_row FROM `T2_TREE_CONSTRUCTOR_STR` WHERE `HEAD_ID` = '$head_id' AND `COL` = $swap_col LIMIT 1;");
            $swap_row = $db->result($r, 0, "swap_row") + 1;
            $db->query("UPDATE `T2_TREE_CONSTRUCTOR_STR` SET `ROW` = '$swap_row', `COL` = '$swap_col' WHERE `ID` = '$current_id' LIMIT 1;");
        }
        return $logs;
    }

    /*=Catalog Exist=========================================================================================*/

    public function loadCatalogExist($status = 0)
    {
        return $this->showPartsForm($status);
    }

    /*
     * show `Parts` catalog
     * */
    public function showPartsForm($status = 0)
    {
        $form = "";
        $form_htm = RD . "/tpl/tree_constructor/cron_form.htm";
        if (file_exists("$form_htm")) {
            $form = file_get_contents($form_htm);
        }
        $list = $this->showGroupExistList($status);
        $form = str_replace("{parts_name}", "{spare_parts_catalog_cap}", $form);
        $form = str_replace("{parts_list}", $list, $form);
        return $form;
    }

    /*
     * get TREE HCG LIST
     * */
    public function getGroupExistList($status)
    {
        $db = DbSingleton::getTokoDb();
        $arr = [];
        $r = $db->query("SELECT `HEAD_ID`, `CAT_ID`, `GROUP_ID` FROM `T2_TREE_HCG_EXIST` WHERE 1;");
        if ($status) {
            $r = $db->query("SELECT cs.`HEAD_ID`, cs.`CAT_ID`, he.`GROUP_ID` 
            FROM `T2_TREE_CONSTRUCTOR_STR` cs
                LEFT JOIN `T2_TREE_HCG_EXIST` he ON (he.HEAD_ID = cs.HEAD_ID AND he.CAT_ID = cs.CAT_ID)
                LEFT JOIN `T2_TREE_GROUP_EXIST` ge ON (ge.GROUP_ID = he.GROUP_ID)
            WHERE cs.`CAT_ID` > 0 AND ge.`STATUS` = 1;");
        }
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $head_id = $db->result($r, $i - 1, "HEAD_ID");
            $cat_id = $db->result($r, $i - 1, "CAT_ID");
            $group_id = $db->result($r, $i - 1, "GROUP_ID");
            if (empty($arr[$head_id])) {
                $arr[$head_id] = [];
            }
            if (empty($arr[$head_id][$cat_id])) {
                $arr[$head_id][$cat_id] = [];
            }
            $arr[$head_id][$cat_id][] = $group_id;
        }
        return $arr;
    }

    /*
     * show TREE HCG LIST
     * */
    public function showGroupExistList($status)
    {
        $list = "";
        $arr = $this->getGroupExistList($status);
        $list .= "<table class='table'>
        <thead>
            <tr>
                <th>Name</th>
                <th>Details</th>
                <th>Manufacturers</th>
                <th>Status</th>
                <th>Status auto</th>
            </tr>
        </thead>";
        foreach ($arr as $head_id => $cats) {
            $head_name = $this->getHeadRowName($head_id);
            $list .= "<tr><td colspan='5'><span style='font-weight: bold; font-size: 20px; color: black;'>$head_name</span></td></tr>";
            foreach ($cats as $cat_id => $groups) {
                $cat_name = $this->getCatRowName($cat_id);
                $list .= "<tr><td colspan='5'><span style='font-weight: bold; font-size: 18px; color: blue;'>$cat_name</span></td></tr>";
                foreach ($groups as $group_id) {
                    $group_name = $this->getGroupRowName($group_id);
                    $group_link = $this->getGroupRowLink($group_id);
                    list($status_hidden, $status_auto) = $this->getGroupRowStatus($group_id);
                    $check = $this->checkTable($group_id);
                    if ($check > 0) {
                        $check_form = "<span class='span-grey'><i class='fa fa-edit'></i> UPDATE</span>";
                        $col = "($check)";
                    } else {
                        $check_form = "<span class='span-red'><i class='fa fa-download'></i> CREATE</span>";
                        $col = "";
                    }
                    $check_mfa = $this->checkTableMfa($group_id);
                    if ($check_mfa > 0) {
                        $check_mfa_form = "<span class='span-grey'><i class='fa fa-edit'></i> UPDATE</span>";
                        $col_mfa = "($check_mfa)";
                    } else {
                        $check_mfa_form = "<span class='span-red'><i class='fa fa-download'></i> CREATE</span>";
                        $col_mfa = "";
                    }
                    $list .= "<tr>
                        <td><a onclick='showGroupExistCard(\"$group_id\")'>$group_name</a></td>
                        <td>
                            <div>
                                <a href='https://toko.ua/catalog_exist/init/$group_link/'>$check_form</a>   
                                <a href='https://toko.ua/catalog_exist/show/$group_link/'>$col</a>
                            </div>
                        </td>
                        <td>
                            <div>
                                <a href='https://toko.ua/catalog_exist/init_mfa/$group_link/'>$check_mfa_form</a>
                                <a href='https://toko.ua/catalog_exist/show_mfa/$group_link/'>$col_mfa</a>
                            </div>
                        </td>
                        <td>
                            <input id='status_group_$group_id' type='text' value='$status_hidden' title='status' style='width: 50px;'>
                            <button class='btn btn-xs btn-info' onclick='saveGroupStatus(\"0\", \"$group_id\");'><i class='fa fa-save'></i></button>
                        </td>
                        <td>
                            <input id='status_auto_group_$group_id' type='text' value='$status_auto' title='status auto' style='width: 50px;'>
                            <button class='btn btn-xs btn-info' onclick='saveGroupStatus(\"1\", \"$group_id\");'><i class='fa fa-save'></i></button>
                        </td>
                    </tr>";
                }
            }
        }
        $list .= "</table>";
        return $list;
    }

    public function saveGroupStatus($type, $group_id, $status)
    {
        $db = DbSingleton::getTokoDb();
        $status = intval($status);
        $type_col = "STATUS";
        if ($type == 1) {
            $type_col = "STATUS_AUTO";
        }
        $db->query("UPDATE `T2_TREE_GROUP_EXIST` SET `$type_col`='$status' WHERE `GROUP_ID`='$group_id' LIMIT 1;");
        return $status;
    }

    /*
     * check exist of group table
     * */
    public function checkTable($group_id)
    {
        $dbc = DbSingleton::getTokoCacheDb();
        $table = "EX_TABLE_TREE_$group_id";
        $r = $dbc->query("SHOW TABLES LIKE '$table';");
        $n = $dbc->num_rows($r);
        if ($n > 0) {
            $r = $dbc->query("SELECT COUNT(`art_id`) as col_arts FROM `$table` WHERE 1;");
            $n = $dbc->result($r, 0, "col_arts");
        }
        return $n;
    }

    public function checkTableMfa($group_id)
    {
        $dbc = DbSingleton::getTokoCacheDb();
        $table = "EX_TABLE_TREE_MFA_$group_id";
        $r = $dbc->query("SHOW TABLES LIKE '$table';");
        $n = $dbc->num_rows($r);
        if ($n > 0) {
            $r = $dbc->query("SELECT COUNT(`art_id`) as col_arts FROM `$table` WHERE 1;");
            $n = $dbc->result($r, 0, "col_arts");
        }
        return $n;
    }

    /*
     * show Group Info
     * */
    public function showGroupExistCard($group_id)
    {
        $db = DbSingleton::getTokoDb();
        $form = "";
        $form_htm = RD . "/tpl/tree_constructor/cron_card.htm";
        if (file_exists("$form_htm")) {
            $form = file_get_contents($form_htm);
        }
        $text_ru = $text_ua = $text_en = "";
        $one_ru = $one_ua = $one_en = "";
        $h1_ru = $h1_ua = $h1_en = "";
        $descr_ru = $descr_ua = $descr_en = "";
        $text_link = "";
        $status = $status_auto = 0;
        $review_list = "";

        $r = $db->query("SELECT * FROM `T2_TREE_GROUP_EXIST` WHERE `GROUP_ID`='$group_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n > 0) {
            $text_ru = $db->result($r, 0, "TEX_RU");
            $text_ua = $db->result($r, 0, "TEX_UA");
            $text_en = $db->result($r, 0, "TEX_EN");
            $one_ru = $db->result($r, 0, "ONE_RU");
            $one_ua = $db->result($r, 0, "ONE_UA");
            $one_en = $db->result($r, 0, "ONE_EN");
            $h1_ru = $db->result($r, 0, "H1_RU");
            $h1_ua = $db->result($r, 0, "H1_UA");
            $h1_en = $db->result($r, 0, "H1_EN");
            $descr_ru = $db->result($r, 0, "DESCRIPTION_RU");
            $descr_ua = $db->result($r, 0, "DESCRIPTION_UA");
            $descr_en = $db->result($r, 0, "DESCRIPTION_EN");
            $text_link = $db->result($r, 0, "TEX_LINK");
            $status = $db->result($r, 0, "STATUS");
            $status_auto = $db->result($r, 0, "STATUS_AUTO");
            $review_list = $this->getGroupReviewsList($group_id);
        }

        $form = str_replace("{group_id}", $group_id, $form);
        $form = str_replace("{text_ru}", $text_ru, $form);
        $form = str_replace("{text_ua}", $text_ua, $form);
        $form = str_replace("{text_en}", $text_en, $form);
        $form = str_replace("{one_ru}", $one_ru, $form);
        $form = str_replace("{one_ua}", $one_ua, $form);
        $form = str_replace("{one_en}", $one_en, $form);
        $form = str_replace("{h1_ru}", $h1_ru, $form);
        $form = str_replace("{h1_ua}", $h1_ua, $form);
        $form = str_replace("{h1_en}", $h1_en, $form);
        $form = str_replace("{descr_ru}", $descr_ru, $form);
        $form = str_replace("{descr_ua}", $descr_ua, $form);
        $form = str_replace("{descr_en}", $descr_en, $form);
        $form = str_replace("{text_link}", $text_link, $form);
        $form = str_replace("{group_status}", $status, $form);
        $form = str_replace("{group_status_auto}", $status_auto, $form);
        $form = str_replace("{review_list}", $review_list, $form);

        return $form;
    }

    public function getGroupReviewsList($group_id)
    {
        $db = DbSingleton::getTokoDb();
        $reviews = [];
        $r = $db->query("SELECT * FROM `T2_GROUP_REVIEW` WHERE `GROUP_ID`='$group_id';");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $review_id = $db->result($r, $i - 1, "REVIEW_ID");
            array_push($reviews, $review_id);
        }

        $list = "";
        $r = $db->query("SELECT `ID`, `TITLE_RU` FROM `T2_REVIEWS` WHERE 1;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $review_id = $db->result($r, $i - 1, "ID");
            $review_name = $db->result($r, $i - 1, "TITLE_RU");
            if (in_array($review_id, $reviews)) {
                $sel = "selected='selected'";
            } else {
                $sel = "";
            }
            $list .= "<option value='$review_id' $sel>$review_name</option>";
        }

        return $list;
    }

    public function saveGroupExistCard($group_id, $text_ru, $text_ua, $text_en, $one_ru, $one_ua, $one_en, $h1_ru, $h1_ua, $h1_en, $desc_ru, $descr_ua, $descr_en, $text_link, $status, $status_auto, $reviews)
    {
        $db = DbSingleton::getTokoDb();
        $answer = 0; $err = "Помилка збереження!";
        if ($group_id > 0) {
            $db->query("UPDATE `T2_TREE_GROUP_EXIST` SET 
                `TEX_RU`='$text_ru', `TEX_UA`='$text_ua', `TEX_EN`='$text_en', 
                `ONE_RU`='$one_ru', `ONE_UA`='$one_ua', `ONE_EN`='$one_en', 
                `H1_RU`='$h1_ru', `H1_UA`='$h1_ua', `H1_EN`='$h1_en', 
                `DESCRIPTION_RU`='$desc_ru', `DESCRIPTION_UA`='$descr_ua', `DESCRIPTION_EN`='$descr_en', 
                `TEX_LINK`='$text_link', `STATUS`='$status', `STATUS_AUTO`='$status_auto' 
            WHERE `GROUP_ID`='$group_id' LIMIT 1;");
            $db->query("DELETE FROM `T2_GROUP_REVIEW` WHERE `GROUP_ID`='$group_id';");
            if (!empty($reviews)) {
                foreach ($reviews as $review_id) {
                    $db->query("INSERT INTO `T2_GROUP_REVIEW` (`GROUP_ID`, `REVIEW_ID`) VALUES ('$group_id', '$review_id');");
                }
            }
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

}