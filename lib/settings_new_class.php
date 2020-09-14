<?php

class SettingsNewClass {

    //Language============================================================================================================================

    function showLanguageList() {$db = DbSingleton::getTokoDb();
        $list="";$m = 3;
        $form=""; $form_htm=RD."/tpl/new/language.htm"; if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("SELECT * FROM `new_lang_wd`;"); $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $var=$db->result($r,$i-1,"variable");
            $list.="<tr style='cursor:pointer' onClick='showLanguageCard(\"$id\")'><td>$i</td><td>$var</td>";
            for ($j=1;$j<=$m;$j++) {
                $rs=$db->query("SELECT `caption` FROM `new_lang_wdv` WHERE `lang_id`='$j' AND `wd`='$id';");
                $cap=$db->result($rs,0,"caption");
                $list.="<td>$cap</td>";
            }
            $list.="</tr>";
        }
        $form=str_replace("{lang_range}",$list,$form);
        return $form;
    }

    function loadLanguageList() { $db = DbSingleton::getTokoDb();
        $list="";$m = 3;
        $r=$db->query("SELECT * FROM `new_lang_wd`;"); $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $var=$db->result($r,$i-1,"variable");
            $list.="<tr style='cursor:pointer' onClick='showLanguageCard(\"$id\")'><td>$i</td><td>$var</td>";
            for ($j=1;$j<=$m;$j++) {
                $rs=$db->query("SELECT `caption` FROM `new_lang_wdv` WHERE `lang_id`='$j' AND `wd`='$id';");
                $cap=$db->result($rs,0,"caption");
                $list.="<td>$cap</td>";
            }
            $list.="</tr>";
        }
        return $list;
    }

    function newLanguageCard($lang_var) { $db = DbSingleton::getTokoDb();
        $r=$db->query("SELECT MAX(`id`) as mid FROM `new_lang_wd`;"); $max_id=0+$db->result($r,0,"mid")+1;
        $db->query("INSERT INTO `new_lang_wd` (`id`,`variable`) VALUES ('$max_id','$lang_var');");
        return $max_id;
    }

    function showLanguageCard($id) { $db=DbSingleton::getTokoDb();
        $m=3; $lang_arr=[];
        $form="";$form_htm=RD."/tpl/new/language_card.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("SELECT * FROM `new_lang_wd` WHERE `id`=$id;");
        $lang_var=$db->result($r,0,"variable");
        for ($j=1;$j<=$m;$j++) {
            $rs=$db->query("SELECT `caption` FROM `new_lang_wdv` WHERE `lang_id`='$j' and `wd`='$id';");
            $cap=$db->result($rs,0,"caption");
            array_push($lang_arr,$cap);
        }
        $lang_ru=$lang_arr[0]; $lang_ua=$lang_arr[1]; $lang_eng=$lang_arr[2];
        $form=str_replace("{id}",$id,$form);
        $form=str_replace("{lang_var}",$lang_var,$form);
        $form=str_replace("{lang_ru}",$lang_ru,$form);
        $form=str_replace("{lang_ua}",$lang_ua,$form);
        $form=str_replace("{lang_eng}",$lang_eng,$form);
        return $form;
    }

    function saveLanguage($lang_id,$lang_ru,$lang_ua,$lang_eng) { $db=DbSingleton::getTokoDb();
        $answer=0; $err="Помилка збереження даних!";
        if ($lang_id>0){
            $r=$db->query("SELECT * FROM `new_lang_wdv` WHERE `lang_id`=1 and `wd`=$lang_id;"); $n=$db->num_rows($r);
            if ($n>0) $db->query("UPDATE `new_lang_wdv` SET `caption`='$lang_ru' WHERE `lang_id`=1 AND `wd`=$lang_id;");
            else $db->query("INSERT INTO `new_lang_wdv` (`lang_id`, `wd`, `caption`) VALUES (1,$lang_id,'$lang_ru');");
            $r=$db->query("SELECT * FROM `new_lang_wdv` WHERE `lang_id`=2 and `wd`=$lang_id;"); $n=$db->num_rows($r);
            if ($n>0) $db->query("UPDATE `new_lang_wdv` SET `caption`='$lang_ua' WHERE `lang_id`=2 AND `wd`=$lang_id;");
            else $db->query("INSERT INTO `new_lang_wdv` (`lang_id`, `wd`, `caption`) VALUES (2,$lang_id,'$lang_ua');");
            $r=$db->query("SELECT * FROM `new_lang_wdv` WHERE `lang_id`=3 and `wd`=$lang_id;"); $n=$db->num_rows($r);
            if ($n>0) $db->query("UPDATE `new_lang_wdv` SET `caption`='$lang_eng' WHERE `lang_id`=3 AND `wd`=$lang_id;");
            else $db->query("INSERT INTO `new_lang_wdv` (`lang_id`, `wd`, `caption`) VALUES (3,$lang_id,'$lang_eng');");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function dropLanguage($lang_id) { $db=DbSingleton::getTokoDb();
        $answer=0; $err="Помилка збереження даних!";
        if ($lang_id>0) {
            $db->query("DELETE FROM `new_lang_wd` WHERE `id`='$lang_id';");
            $db->query("DELETE FROM `new_lang_wdv` WHERE `wd`='$lang_id';");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

	//Contacts============================================================================================================================
	
	function getLangCap($lang_id) { $db = DbSingleton::getTokoDb();
		$r=$db->query("SELECT `caption` FROM `new_lang` WHERE `id`='$lang_id';");
		$caption=$db->result($r,0,"caption");
		return $caption;
	}
	
	function showContactsList() { $db = DbSingleton::getTokoDb();
		$form=""; $form_htm=RD."/tpl/new/contacts.htm"; if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}					
		$r=$db->query("SELECT * FROM `contacts_new` WHERE `status`=1;"); $n=$db->num_rows($r); $list="";
		for ($i=1;$i<=$n;$i++){
			$id=$db->result($r,$i-1,"id");
			$title=$db->result($r,$i-1,"title");
			$address=$db->result($r,$i-1,"address");
			$schedule=$db->result($r,$i-1,"schedule");
			$phone=$db->result($r,$i-1,"phone");
			$lang_id=$db->result($r,$i-1,"lang_id"); $lang_id=$this->getLangCap($lang_id);
			$list.="<tr style='cursor:pointer' onClick='showContactsCard(\"$id\")'>
				<td>$title</td>
				<td>$address</td>
				<td>$schedule</td>
				<td>$phone</td>
				<td>$lang_id</td>
			</tr>";
		}
		$form=str_replace("{contacts_range}",$list,$form);
		return $form;
	}
		
	function loadContactsList() { $db = DbSingleton::getTokoDb();
		$r=$db->query("SELECT * FROM `contacts_new` WHERE `status`=1;"); $n=$db->num_rows($r);$list="";
		for ($i=1;$i<=$n;$i++){
			$id=$db->result($r,$i-1,"id");
			$title=$db->result($r,$i-1,"title");
			$address=$db->result($r,$i-1,"address");
			$schedule=$db->result($r,$i-1,"schedule");
			$phone=$db->result($r,$i-1,"phone");
			$list.="<tr style='cursor:pointer' onClick='showContactsCard(\"$id\")'>
				<td>$title</td>
				<td>$address</td>
				<td>$schedule</td>
				<td>$phone</td>
			</tr>";
		}
		return $list;
	}
	
	function newContactsCard($lang_var){ $db = DbSingleton::getTokoDb();
		$r=$db->query("SELECT MAX(`id`) as mid FROM `contacts_new`;"); $max_id=0+$db->result($r,0,"mid")+1;
		$db->query("INSERT INTO `contacts_new` (`id`,`status`,`lang_id`) VALUES ('$max_id',1,'$lang_var');");
		return $max_id;
	}
	
	function showContactsCard($contact_id){ $db=DbSingleton::getTokoDb();
        $form="";$form_htm=RD."/tpl/new/contacts_card.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
		$r=$db->query("SELECT * FROM `contacts_new` WHERE `id`='$contact_id';"); $n=$db->num_rows($r);
		if ($n>0){
			$id=$db->result($r,0,"id");
			$title=$db->result($r,0,"title");
			$address=$db->result($r,0,"address");
			$schedule=$db->result($r,0,"schedule");
			$phone=$db->result($r,0,"phone");
			$form=str_replace("{id}",$id,$form);
			$form=str_replace("{title}",$title,$form);
			$form=str_replace("{address}",$address,$form);
			$form=str_replace("{schedule}",$schedule,$form);
			$form=str_replace("{phone}",$phone,$form);
		}
		return $form;
  	}
	
	function saveContacts($contact_id,$title,$address,$schedule,$phone){ $db=DbSingleton::getTokoDb();
	    $answer=0;$err="Помилка збереження даних!";
		if ($contact_id>0){
			$db->query("UPDATE `contacts_new` set `title`='$title', `address`='$address', `schedule`='$schedule', `phone`='$phone' WHERE `id`='$contact_id';");
			$answer=1;$err="";
		}
		return array($answer,$err);
	}
	
	function dropContacts($contact_id) { $db=DbSingleton::getTokoDb();
	    $answer=0;$err="Помилка збереження даних!";
		if ($contact_id>0) {
			$db->query("DELETE FROM `contacts_new` WHERE `id`='$contact_id';");
			$answer=1;$err="";
		}
		return array($answer,$err);	
    }
	
	//Locations======================================================================================================================
	
	function showLocations() {$db = DbSingleton::getTokoDb();
		$form=""; $form_htm=RD."/tpl/new/locations.htm"; if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}						
		$r=$db->query("SELECT t2c.CITY_NAME, t2r.REGION_NAME, t2s.STATE_NAME 
		FROM `T2_CITY` t2c
			LEFT OUTER JOIN `T2_REGION` t2r on t2r.REGION_ID=t2c.REGION_ID
			LEFT OUTER JOIN `T2_STATE` t2s on t2s.STATE_ID=t2r.STATE_ID;"); $n=$db->num_rows($r); $list="";
		for ($i=1;$i<=$n;$i++){
			$city=$db->result($r,$i-1,"CITY_NAME");
			$region=$db->result($r,$i-1,"REGION_NAME");
			$state=$db->result($r,$i-1,"STATE_NAME");
			$list.="<tr>
				<td>$state</td>
				<td>$region</td>
				<td>$city</td>
			</tr>";
		}
		$form=str_replace("{location_range}",$list,$form);
		return $form;
	}
	
	//Contacts bottom===============================================================================================================
	
	function getStatusCaption($status) {
		$status ? $status_cap="Активний" : $status_cap="Відключений";
		return $status_cap;
	}
	
	function showIcontSelectList($sel_id) { $db = DbSingleton::getTokoDb();
		$r=$db->query("SELECT * FROM `new_icons`;"); $n=$db->num_rows($r); $list="";
		for ($i=1;$i<=$n;$i++){
			$id=$db->result($r,$i-1,"id");
			$name=$db->result($r,$i-1,"name");
			$sel="";if ($id==$sel_id){$sel=" selected";}
			$list.="<option value='$id' $sel>$name</option>";
		}
		return $list;
	}
	
	function getIcon($id) { $db = DbSingleton::getTokoDb();
		$r=$db->query("SELECT * FROM `new_icons` WHERE `id`='$id' LIMIT 1;");
		$name=$db->result($r,0,"name");
		$icon=$db->result($r,0,"icon");
		$full_name="<i class='fa $icon'> $name</i>";				   				   
		return $full_name;
	}
	
	function showContactsBotList() { $db = DbSingleton::getTokoDb();
		$form=""; $form_htm=RD."/tpl/new/contacts_bottom.htm"; if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}						
		$r=$db->query("SELECT * FROM `contacts_bottom_new`;"); $n=$db->num_rows($r); $list="";
		for ($i=1;$i<=$n;$i++){
			$id=$db->result($r,$i-1,"id");
			$text=$db->result($r,$i-1,"text");
			$icon=$db->result($r,$i-1,"icon"); $icon=$this->getIcon($icon);
			$link=$db->result($r,$i-1,"link"); 
			$status=$db->result($r,$i-1,"status"); $status=$this->getStatusCaption($status);
			$list.="<tr style='cursor:pointer' onClick='showContactsBotCard(\"$id\")'>
				<td>$text</td>
				<td>$icon</td>
				<td>$link</td>
				<td>$status</td>
			</tr>";
		}
		$form=str_replace("{contacts_range}",$list,$form);
		return $form;
	}
		
	function loadContactsBotList() { $db = DbSingleton::getTokoDb();
		$r=$db->query("SELECT * FROM `contacts_bottom_new`;"); $n=$db->num_rows($r); $list="";
		for ($i=1;$i<=$n;$i++){
			$id=$db->result($r,$i-1,"id");
			$text=$db->result($r,$i-1,"text");
			$icon=$db->result($r,$i-1,"icon"); $icon=$this->getIcon($icon);
			$link=$db->result($r,$i-1,"link");
			$status=$db->result($r,$i-1,"status"); $status=$this->getStatusCaption($status);
			$list.="<tr style='cursor:pointer' onClick='showContactsBotCard(\"$id\")'>
				<td>$text</td>
				<td>$icon</td>
				<td>$link</td>
				<td>$status</td>
			</tr>";
		}
		return $list;
	}
	
	function newContactsBotCard() { $db = DbSingleton::getTokoDb();
		$r=$db->query("SELECT MAX(`id`) as mid FROM `contacts_bottom_new`;"); $max_id=0+$db->result($r,0,"mid")+1;
		$db->query("INSERT INTO `contacts_bottom_new` (`id`,`status`) VALUES ('$max_id',1);");
		return $max_id;
	}
	
	function showContactsBotCard($contact_id) { $db = DbSingleton::getTokoDb();
        $form="";$form_htm=RD."/tpl/new/contacts_bottom_card.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
		$r=$db->query("SELECT * FROM `contacts_bottom_new` WHERE `id`='$contact_id';"); $n=$db->num_rows($r);
		if ($n>0){
			$id=$db->result($r,0,"id");
			$text=$db->result($r,0,"text");
			$icon=$db->result($r,0,"icon"); $icon_select=$this->showIcontSelectList($icon);
			$link=$db->result($r,0,"link");
			$status=$db->result($r,0,"status"); $status>0 ? $checked=" checked" : $checked="";
			$form=str_replace("{id}",$id,$form);
			$form=str_replace("{text}",$text,$form);
			$form=str_replace("{icon_select}",$icon_select,$form);
			$form=str_replace("{link}",$link,$form);
			$form=str_replace("{status}",$checked,$form);
		}
		return $form;
  	}
	
	function saveContactsBot($contact_id, $text, $icon, $link, $status) { $db = DbSingleton::getTokoDb();
	    $answer=0;$err="Помилка збереження даних!";
		if ($contact_id>0){
			$db->query("UPDATE `contacts_bottom_new` SET `text`='$text', `type_contact`='$icon', `icon`='$icon', `link`='$link', `status`='$status' WHERE `id`='$contact_id';");
			$answer=1;$err="";
		}
		return array($answer,$err);
	}
	
	function dropContactsBot($contact_id) { $db = DbSingleton::getTokoDb();
	    $answer=0;$err="Помилка збереження даних!";
		if ($contact_id>0){
			$db->query("DELETE FROM `contacts_bottom_new` WHERE `id`='$contact_id';");
			$answer=1;$err="";
		}
		return array($answer,$err);	
    }
	
	//News=======================================================================================================================================
	
	function getLangCaption($lang_id) { $db = DbSingleton::getTokoDb();
		$r=$db->query("SELECT `caption` FROM `lang` WHERE `id`='$lang_id' LIMIT 1;");
	    $caption=$db->result($r,0,"caption");
		return $caption;
	}
	
	function showNewsList() { $db = DbSingleton::getTokoDb();
	    $date=date("Y-m-d");$list="";
		$form="";$form_htm=RD."/tpl/new/news.htm"; if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
		$r=$db->query("SELECT * FROM `news` ORDER BY `data` DESC;"); $n=$db->num_rows($r);
		for ($i=1;$i<=$n;$i++){
			$id=$db->result($r,$i-1,"id");
			$lang=$db->result($r,$i-1,"lang_id"); $lang=$this->getLangCaption($lang);
			$caption=$db->result($r,$i-1,"caption");
			$short_desc=$db->result($r,$i-1,"short_desc");
			$data=$db->result($r,$i-1,"data"); $data>$date ? $color_data="style='background:coral'" : $color_data="";
			$status=$db->result($r,$i-1,"status"); $status==true ? $color_status="style='background:lightgreen'" : $color_status="style='background:lightpink'";
			$status=$this->getStatusCaption($status);
			$list.="<tr style='cursor:pointer' onClick='showNewsCard(\"$id\")'>
				<td $color_data>$data</td>
				<td>$lang</td>
				<td>$caption</td>
				<td>$short_desc</td>
				<td $color_status>$status</td>
			</tr>";
		}
		$form=str_replace("{news_range}",$list,$form);
		return $form;
	}
		
	function loadNewsList() { $db = DbSingleton::getTokoDb();
	    $date=date("Y-m-d");$list="";
		$r=$db->query("SELECT * FROM `news` ORDER BY `data` DESC;"); $n=$db->num_rows($r);
		for ($i=1;$i<=$n;$i++){
			$id=$db->result($r,$i-1,"id");
			$lang=$db->result($r,$i-1,"lang_id"); $lang=$this->getLangCaption($lang);
			$caption=$db->result($r,$i-1,"caption");
			$short_desc=$db->result($r,$i-1,"short_desc");
			$data=$db->result($r,$i-1,"data"); $data>$date ? $color_data="style='background:coral'" : $color_data="";
			$status=$db->result($r,$i-1,"status"); $status==true ? $color_status="style='background:lightgreen'" : $color_status="style='background:lightpink'";
			$status=$this->getStatusCaption($status); 
			$list.="<tr style='cursor:pointer' onClick='showNewsCard(\"$id\")'>
				<td $color_data>$data</td>
				<td>$lang</td>
				<td>$caption</td>
				<td>$short_desc</td>
				<td $color_status>$status</td>
			</tr>";
		}
		return $list;
	}
	
	function newNewsCard($lang) { $db = DbSingleton::getTokoDb();
	    $date=date("Y-m-d");
		$r=$db->query("SELECT MAX(`id`) as mid FROM `news`;"); $max_id=0+$db->result($r,0,"mid")+1;
		$db->query("INSERT INTO `news` (`id`,`status`,`data`,`lang_id`) VALUES ('$max_id',0,'$date','$lang');");
		return $max_id;
	}
	
	function showNewsCard($news_id) { $db=DbSingleton::getTokoDb();
        $form="";$form_htm=RD."/tpl/new/news_card.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
		$r=$db->query("SELECT * FROM `news` WHERE `id`='$news_id';"); $n=$db->num_rows($r);
		if ($n>0){
			$id=$db->result($r,0,"id");
			$caption=$db->result($r,0,"caption");
			$lang_id=$db->result($r,0,"lang_id"); $lang_val=$this->getLangCaption($lang_id);
			$short_desc=$db->result($r,0,"short_desc");
			$data=$db->result($r,0,"data");
			$status=$db->result($r,0,"status"); $status>0 ? $checked=" checked" : $checked="";
			$descr=$db->result($r,0,"desc");
			$form=str_replace("{id}",$id,$form);
			$form=str_replace("{caption}",$caption,$form);
			$form=str_replace("{lang_id}",$lang_id,$form);
			$form=str_replace("{lang_val}",$lang_val,$form);
			$form=str_replace("{short}",$short_desc,$form);
			$form=str_replace("{data}",$data,$form);
			$form=str_replace("{descr}",$descr,$form);
			$form=str_replace("{status}",$checked,$form);
			$r2=$db->query("SELECT `id` FROM `news_galery` WHERE `cat`='$id';");
			$file_id=$db->result($r2,0,"id");
			$form=str_replace("{file_id}",$file_id,$form);
		}
		return $form;
  	}
	
	function saveNews($news_id,$caption,$data,$short,$descr,$status) { $db=DbSingleton::getTokoDb();
	    $answer=0;$err="Помилка збереження даних!";
		if ($news_id>0){
			$db->query("UPDATE `news` set `caption`='$caption', `data`='$data', `short_desc`='$short', `desc`='$descr', `status`='$status' WHERE `id`='$news_id';");
			$answer=1;$err="";
		}
		return array($answer,$err);
	}
	
	function dropNews($news_id) { $db=DbSingleton::getTokoDb();
	    $answer=0;$err="Помилка збереження даних!";
		if ($news_id>0) {
			$db->query("DELETE FROM `news` WHERE `id`='$news_id';");
			$answer=1;$err="";
		}
		return array($answer,$err);	
    }
	
	function loadNewsPhoto($news_id,$lang_id) { $db=DbSingleton::getTokoDb();
        $form="";$form_htm=RD."/tpl/new/news_photo_block.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
		$r=$db->query("SELECT * FROM `news_galery` WHERE `cat`='$news_id';"); $n=$db->num_rows($r); $list="";
		for ($i=1;$i<=$n;$i++){
			$file=$db->result($r,$i-1,"id");
			$file_text=$db->result($r,$i-1,"caption"); 
			$link="https://toko.ua/uploads/images/news/$lang_id/$news_id/$file.jpg";
			$block=$form;
			$block=str_replace("{logo_name}",$file_text,$block);
			$block=str_replace("{link}",$link,$block);
			$list.=$block;
		}
		if ($n==0){$list="<h3 class='text-center'>Зображення відсутнє</h3>";}
		return $list;
	}
	
	function deleteNewsLogo($news_id) { $db=DbSingleton::getTokoDb(); 
		$answer=0; $err="Помилка видалення даних!";
		if ($news_id>0){
			$db->query("DELETE FROM `news_galery` WHERE `cat`='$news_id';");
			$answer=1;$err="";
		}
		return array($answer,$err);
	}

	/*==== T_QUESTION ====*/

    function showRequestsList() {
        $form=""; $form_htm=RD."/tpl/new/requests.htm"; if (file_exists("$form_htm")) { $form = file_get_contents($form_htm);}
        $form = str_replace("{requests_range}", $this->loadRequestsList(), $form);
        return $form;
    }

    function loadRequestsList() { $db = DbSingleton::getTokoDb();
        $list = "";
        $r = $db->query("SELECT * FROM `T2_QUESTIONS` ORDER BY `DATA_CREATE` DESC;"); $n = $db->num_rows($r);
        for ($i=1; $i<=$n; $i++) {
            $id = $db->result($r, $i-1, "ID");
            $vin = $db->result($r, $i-1, "VIN");
            $text = $db->result($r, $i-1, "TEXT");
            $data = $db->result($r, $i-1, "DATA_CREATE");
            $data_update = $db->result($r, $i-1, "DATA_UPDATE");
            $status = $db->result($r, $i-1, "STATUS");
            $style = "";
            if ($status>0) $style = "background: pink;";
            $list.="<tr style='cursor:pointer; $style' onClick='showRequestCard(\"$id\")'>
				<td>$id</td>
                <td>380*********</td>
				<td>$vin</td>
				<td>$text</td>
				<td>$data</td>
				<td>$data_update</td>
			</tr>";
        }
        return $list;
    }

    function getMediaUserName($user_id) { $db = DbSingleton::getDb();
        $r = $db->query("SELECT `name` FROM `media_users` WHERE `id`='$user_id' LIMIT 1;"); $n = $db->num_rows($r); $name = "";
        if ($n==1) { $name = $db->result($r, 0, "name"); }
        return $name;
    }

    function showRequestCard($request_id) { $db = DbSingleton::getTokoDb();
        session_start(); $user_id = $_SESSION["media_user_id"];
        $form = ""; $form_htm = RD."/tpl/new/requests_card.htm"; if (file_exists("$form_htm")) { $form = file_get_contents($form_htm);}
        $r = $db->query("SELECT * FROM `T2_QUESTIONS` WHERE `ID`='$request_id';"); $n = $db->num_rows($r);
        if ($n==0) {
            $id = 0;
            $vin = "";
            $phone = "";
            $text = "";
            $data_create = "";
            $data_update = "";
            $user_use = 0;
            $status = 0;
        } else {
            $id = $db->result($r, 0, "ID");
            $vin = $db->result($r, 0, "VIN");
            $phone = $db->result($r, 0, "PHONE");
            $text = $db->result($r, 0, "TEXT");
            $data_create = $db->result($r, 0, "DATA_CREATE");
            $data_update = $db->result($r, 0, "DATA_UPDATE");
            $user_use = $db->result($r, 0, "USER_USE");
            $status = $db->result($r, 0, "STATUS");
        }

        if ($user_id!=$user_use && $user_use>0) {
            $form_htm = RD."/tpl/dp_use_deny.htm"; if (file_exists("$form_htm")) { $form = file_get_contents($form_htm);}
            $form = str_replace("{user_name}",$this->getMediaUserName($user_use),$form);
            $admin_unlock = "";
            if ($user_id==1 || $user_id==2 || $user_id==7) { $admin_unlock = "<button class='btn btn-sm btn-warning' onClick='unlockRequestCard(\"$id\");'><i class='fa fa-unlock'></i> Розблокувати</button>"; }
            $form = str_replace("{admin_unlock}",$admin_unlock,$form);
        }

        $form = str_replace("{request_id}", $id, $form);
        $form = str_replace("{request_vin}", $vin, $form);
        $form = str_replace("{request_phone}", $phone, $form);
        $form = str_replace("{request_text}", $text, $form);
        $form = str_replace("{request_data_create}", $data_create, $form);
        $form = str_replace("{request_data_update}", $data_update, $form);
        $form = str_replace("{reqest_disabled}", ($status) ? "" : "disabled", $form);
        return $form;
    }

    function saveRequest($request_id, $vin, $phone, $text) { $db = DbSingleton::getTokoDb();
        if ($request_id==0) {
            $answer = 0; $err = "Помилка збереження даних!";
        } else {
            $data_update = date("Y-m-d H:i:s");
            $db->query("UPDATE `T2_QUESTIONS` SET `VIN`='$vin', `PHONE`='$phone', `TEXT`='$text', `DATA_UPDATE`='$data_update', `STATUS`='0' WHERE `ID`='$request_id';");
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

    function dropRequest($request_id) { $db = DbSingleton::getTokoDb();
        $answer = 0; $err = "Помилка збереження даних!";
        if ($request_id>0) {
            $db->query("DELETE FROM `T2_QUESTIONS` WHERE `ID`='$request_id' LIMIT 1;");
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

    function closeRequestCard($request_id) { $db = DbSingleton::getTokoDb();
        session_start(); $user_id = $_SESSION["media_user_id"];
        if ($request_id>0 && $user_id>0) {
            $db->query("UPDATE `T2_QUESTIONS` SET `USER_USE`='0' WHERE `ID`='$request_id';");
        }
        $answer = 1;
        return $answer;
    }

    function unlockRequestCard($request_id) { $db = DbSingleton::getTokoDb();
        session_start(); $user_id = $_SESSION["media_user_id"]; $answer = 0;
        if ($user_id==1 || $user_id==2 || $user_id==7) {
            $db->query("UPDATE `T2_QUESTIONS` SET `USER_USE`='0' WHERE `ID`='$request_id';");
            $answer = 1;
        }
        return $answer;
    }

    /*==== T_REVIEWS ====*/

    function showReviewsList() {
        $form=""; $form_htm=RD."/tpl/new/reviews.htm"; if (file_exists("$form_htm")) { $form = file_get_contents($form_htm);}
        $form = str_replace("{reviews_range}", $this->loadReviewsList(), $form);
        return $form;
    }

    function loadReviewsList() { $db = DbSingleton::getTokoDb();
        $list = "";
        $r = $db->query("SELECT * FROM `T2_REVIEWS` ORDER BY `DATA` DESC;"); $n = $db->num_rows($r);
        for ($i=1; $i<=$n; $i++) {
            $id = $db->result($r, $i-1, "ID");
            $title = $db->result($r, $i-1, "TITLE");
            $data = $db->result($r, $i-1, "DATA");
            $status = $db->result($r, $i-1, "STATUS");
            $list.="<tr style='cursor:pointer' onClick='showReviewCard(\"$id\")'>
				<td>$id</td>
				<td>$title</td>
				<td>$data</td>
				<td>$status</td>
			</tr>";
        }
        return $list;
    }

    function showReviewCard($id) { $db = DbSingleton::getTokoDb();
        $form=""; $form_htm=RD."/tpl/new/reviews_card.htm"; if (file_exists("$form_htm")) { $form = file_get_contents($form_htm);}
        $r = $db->query("SELECT * FROM `T2_REVIEWS` WHERE `ID`='$id';"); $n = $db->num_rows($r);
        if ($n==0) {
            $id = 0;
            $title = "";
            $text = "";
            $data = "";
            $status = 0;
            $img = "";
            $disabled = "disabled";
        } else {
            $title = $db->result($r, 0, "TITLE");
            $text = $db->result($r, 0, "TEXT");
            $data = $db->result($r, 0, "DATA");
            $status = $db->result($r, 0, "STATUS");
            $img = $db->result($r, 0, "IMG");
            $disabled = "";
        }
        $form = str_replace("{review_id}", $id, $form);
        $form = str_replace("{review_title}", $title, $form);
        $form = str_replace("{review_text}", $text, $form);
        $form = str_replace("{review_data}", $data, $form);
        $form = str_replace("{review_status}", $status ? "checked" : "", $form);
        $form = str_replace("{review_image}", $img, $form);
        $form = str_replace("{review_remove_disabled}", $disabled, $form);
        return $form;
    }

    function saveReview($review_id, $title, $text, $data, $status) { $db = DbSingleton::getTokoDb();
        // $answer = 0; $err = "Помилка збереження даних!";
        if ($review_id==0) {
            $r = $db->query("SELECT MAX(`ID`) as mid FROM `T2_REVIEWS`;"); $max_id = 0 + $db->result($r, 0, "mid") + 1;
            $db->query("INSERT INTO `T2_REVIEWS` (`ID`, `TITLE`, `DATA`, `STATUS`) VALUES ('$max_id', '$title', '$data', '$status');");
            $db->query('UPDATE `T2_REVIEWS` SET `TEXT`="'.$text.'" WHERE `ID`="'.$max_id.'";');
            $answer = 1; $err = "";
        } else {
            $db->query("UPDATE `T2_REVIEWS` SET `TITLE`='$title', `DATA`='$data', `STATUS`='$status' WHERE `ID`='$review_id';");
            $db->query('UPDATE `T2_REVIEWS` SET `TEXT`="'.$text.'" WHERE `ID`="'.$review_id.'";');
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

    function dropReview($review_id) { $db = DbSingleton::getTokoDb();
        $answer = 0; $err = "Помилка збереження даних!";
        if ($review_id>0) {
            $db->query("DELETE FROM `T2_REVIEWS` WHERE `ID`='$review_id' LIMIT 1;");
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }


}