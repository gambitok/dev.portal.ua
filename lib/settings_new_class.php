<?php

class SettingsNewClass {
	
	//contacts============================================================================================================================
	
	function getLangCap($lang_id) {$db = DbSingleton::getTokoDb();
		$r=$db->query("select caption from new_lang where id='$lang_id';");
		$caption=$db->result($r,0,"caption");
		return $caption;
	}
	
	function showContactsList() { $db = DbSingleton::getTokoDb();$list="";
		$form=""; $form_htm=RD."/tpl/new/contacts.htm"; if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}					
		$r=$db->query("select * from contacts_new where status=1;"); $n=$db->num_rows($r);						
		for ($i=1;$i<=$n;$i++){
			$id=$db->result($r,$i-1,"id");
			$title=$db->result($r,$i-1,"title");
			$address=$db->result($r,$i-1,"address");
			$schedule=$db->result($r,$i-1,"schedule");
			$phone=$db->result($r,$i-1,"phone");
			$lang_id=$db->result($r,$i-1,"lang_id"); $lang_id=$this->getLangCap($lang_id);
			$list.="<tr style='cursor:pointer' onClick='showContactsCard(\"$id\")'>";
				$list.="<td>$title</td>";
				$list.="<td>$address</td>";
				$list.="<td>$schedule</td>";
				$list.="<td>$phone</td>";
				$list.="<td>$lang_id</td>";
			$list.="</tr>";
		}
		$form=str_replace("{contacts_range}",$list,$form);
		return $form;
	}
		
	function loadContactsList() { $db = DbSingleton::getTokoDb();$list="";
		$r=$db->query("select * from contacts_new where status=1;"); $n=$db->num_rows($r);						
		for ($i=1;$i<=$n;$i++){
			$id=$db->result($r,$i-1,"id");
			$title=$db->result($r,$i-1,"title");
			$address=$db->result($r,$i-1,"address");
			$schedule=$db->result($r,$i-1,"schedule");
			$phone=$db->result($r,$i-1,"phone");
			$list.="<tr style='cursor:pointer' onClick='showContactsCard(\"$id\")'>";
				$list.="<td>$title</td>";
				$list.="<td>$address</td>";
				$list.="<td>$schedule</td>";
				$list.="<td>$phone</td>";
			$list.="</tr>";
		}
		return $list;
	}
	
	function newContactsCard($lang_var){ $db = DbSingleton::getTokoDb();
		$r=$db->query("select max(id) as mid from contacts_new;"); $max_id=0+$db->result($r,0,"mid")+1;
		$db->query("insert into contacts_new (`id`,`status`,`lang_id`) values ('$max_id',1,'$lang_var');");
		return $max_id;
	}
	
	function showContactsCard($contact_id){ $db=DbSingleton::getTokoDb();
        $form="";$form_htm=RD."/tpl/new/contacts_card.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
		$r=$db->query("select * from contacts_new where id='$contact_id';"); $n=$db->num_rows($r);
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
	
	function saveContacts($contact_id,$title,$address,$schedule,$phone){ $db=DbSingleton::getTokoDb(); $answer=0;$err="Помилка збереження даних!";
		if ($contact_id>0){
			$db->query("update contacts_new set `title`='$title', `address`='$address', `schedule`='$schedule', `phone`='$phone' where `id`='$contact_id';");
			$answer=1;$err="";
		}
		return array($answer,$err);
	}
	
	function dropContacts($contact_id) { $db=DbSingleton::getTokoDb(); $answer=0;$err="Помилка збереження даних!";
		if ($contact_id>0) {
			$db->query("delete from contacts_new where id='$contact_id';");	
			$answer=1;$err="";
		}
		return array($answer,$err);	
    }

	//language============================================================================================================================
	
	function showLanguageList() {$db = DbSingleton::getTokoDb();$list="";
		$form=""; $form_htm=RD."/tpl/new/language.htm"; if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
		$m = 3; 
		$r=$db->query("select * from new_lang_wd");
		$n=$db->num_rows($r);
		for ($i=1;$i<=$n;$i++){
			$id=$db->result($r,$i-1,"id");
			$var=$db->result($r,$i-1,"variable");
			$list.="<tr style='cursor:pointer' onClick='showLanguageCard(\"$id\")'><td>$var</td>";
			for ($j=1;$j<=$m;$j++) {
				$rs=$db->query("select caption from new_lang_wdv where lang_id='$j' and wd='$id';");
				$cap=$db->result($rs,0,"caption");
				$list.="<td>$cap</td>";
			}
			$list.="</tr>";
		}
		$form=str_replace("{lang_range}",$list,$form);
		return $form;
	}
	
	function loadLanguageList() {$db = DbSingleton::getTokoDb(); $list="";
		$m = 3; 
		$r=$db->query("select * from new_lang_wd");
		$n=$db->num_rows($r);
		for ($i=1;$i<=$n;$i++){
			$id=$db->result($r,$i-1,"id");
			$var=$db->result($r,$i-1,"variable");
			$list.="<tr style='cursor:pointer' onClick='showLanguageCard(\"$id\")'><td>$var</td>";
			for ($j=1;$j<=$m;$j++) {
				$rs=$db->query("select caption from new_lang_wdv where lang_id='$j' and wd='$id';");
				$cap=$db->result($rs,0,"caption");
				$list.="<td>$cap</td>";
			}
			$list.="</tr>";
		}
		return $list;
	}
		
	function newLanguageCard($lang_var){ $db = DbSingleton::getTokoDb();
		$r=$db->query("select max(id) as mid from new_lang_wd;"); $max_id=0+$db->result($r,0,"mid")+1;
		$db->query("insert into new_lang_wd (`id`,`variable`) values ('$max_id','$lang_var');");
		return $max_id;
	}
	
	function showLanguageCard($id){ $db=DbSingleton::getTokoDb();
		$form_htm=RD."/tpl/new/language_card.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}						
		$m=3; $lang_arr=[];
		$r=$db->query("select * from new_lang_wd where id=$id");
		$lang_var=$db->result($r,0,"variable");
		for ($j=1;$j<=$m;$j++) {
			$rs=$db->query("select caption from new_lang_wdv where lang_id='$j' and wd='$id';");
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
	
	function saveLanguage($lang_id,$lang_var,$lang_ru,$lang_ua,$lang_eng){ $db=DbSingleton::getTokoDb(); $answer=0; $err="Помилка збереження даних!";										  
		if ($lang_id>0){
			$r=$db->query("select * from new_lang_wdv where lang_id=1 and wd=$lang_id;"); $n=$db->num_rows($r);
				if ($n>0) $db->query("update new_lang_wdv set caption='$lang_ru' where lang_id=1 and wd=$lang_id;");
				else $db->query("insert into new_lang_wdv (lang_id,wd,caption) values (1,$lang_id,'$lang_ru');");
			$r=$db->query("select * from new_lang_wdv where lang_id=2 and wd=$lang_id;"); $n=$db->num_rows($r);
				if ($n>0) $db->query("update new_lang_wdv set caption='$lang_ua' where lang_id=2 and wd=$lang_id;");
				else $db->query("insert into new_lang_wdv (lang_id,wd,caption) values (2,$lang_id,'$lang_ua');");
			$r=$db->query("select * from new_lang_wdv where lang_id=3 and wd=$lang_id;"); $n=$db->num_rows($r);
				if ($n>0) $db->query("update new_lang_wdv set caption='$lang_eng' where lang_id=3 and wd=$lang_id;");
				else $db->query("insert into new_lang_wdv (lang_id,wd,caption) values (3,$lang_id,'$lang_eng');");
			$answer=1;$err="";
		}
		return array($answer,$err);
	}
	
	function dropLanguage($lang_id) { $db=DbSingleton::getTokoDb(); $answer=0; $err="Помилка збереження даних!";
		if ($lang_id>0) {
			$db->query("delete from new_lang_wd where id='$lang_id';");	
			$db->query("delete from new_lang_wdv where wd='$lang_id';");	
			$answer=1;$err="";
		}
		return array($answer,$err);	
    }
	
	//locations======================================================================================================================
	
	function showLocations() {$db = DbSingleton::getTokoDb();$list="";
		$form=""; $form_htm=RD."/tpl/new/locations.htm"; if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}						
		$r=$db->query("select t2c.CITY_NAME, t2r.REGION_NAME, t2s.STATE_NAME from T2_CITY t2c
			left outer join T2_REGION t2r on t2r.REGION_ID=t2c.REGION_ID
			left outer join T2_STATE t2s on t2s.STATE_ID=t2r.STATE_ID");
		$n=$db->num_rows($r);
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
	
	function showIcontSelectList($sel_id){$db=DbSingleton::getTokoDb(); $list="";;
		$r=$db->query("select * from new_icons;"); $n=$db->num_rows($r);
		for ($i=1;$i<=$n;$i++){
			$id=$db->result($r,$i-1,"id");
			$name=$db->result($r,$i-1,"name");
			$sel="";if ($id==$sel_id){$sel=" selected";}
			$list.="<option value='$id' $sel>$name</option>";
		}
		return $list;
	}
	
	function getIcon($id) {$db=DbSingleton::getTokoDb();
		$r=$db->query("select * from new_icons where id='$id' limit 1;");
		$name=$db->result($r,0,"name");
		$icon=$db->result($r,0,"icon");
		$full_name="<i class='fa $icon'> $name</i>";				   				   
		return $full_name;
	}
	
	function showContactsBotList() { $db = DbSingleton::getTokoDb();$list="";
		$form=""; $form_htm=RD."/tpl/new/contacts_bottom.htm"; if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}						
		$r=$db->query("select * from contacts_bottom_new;"); $n=$db->num_rows($r);							
		for ($i=1;$i<=$n;$i++){
			$id=$db->result($r,$i-1,"id");
			$text=$db->result($r,$i-1,"text");
			$icon=$db->result($r,$i-1,"icon"); $icon=$this->getIcon($icon);
			$link=$db->result($r,$i-1,"link"); 
			$status=$db->result($r,$i-1,"status"); $status=$this->getStatusCaption($status);
			$list.="<tr style='cursor:pointer' onClick='showContactsBotCard(\"$id\")'>";
				$list.="<td>$text</td>";
				$list.="<td>$icon</td>";
				$list.="<td>$link</td>";
				$list.="<td>$status</td>";
			$list.="</tr>";
		}
		$form=str_replace("{contacts_range}",$list,$form);
		return $form;
	}
		
	function loadContactsBotList() { $db = DbSingleton::getTokoDb();$list="";
		$r=$db->query("select * from contacts_bottom_new;"); $n=$db->num_rows($r);							
		for ($i=1;$i<=$n;$i++){
			$id=$db->result($r,$i-1,"id");
			$text=$db->result($r,$i-1,"text");
			$icon=$db->result($r,$i-1,"icon"); $icon=$this->getIcon($icon);
			$link=$db->result($r,$i-1,"link");
			$status=$db->result($r,$i-1,"status"); $status=$this->getStatusCaption($status);
			$list.="<tr style='cursor:pointer' onClick='showContactsBotCard(\"$id\")'>";
				$list.="<td>$text</td>";
				$list.="<td>$icon</td>";
				$list.="<td>$link</td>";
				$list.="<td>$status</td>";
			$list.="</tr>";
		}
		return $list;
	}
	
	function newContactsBotCard(){ $db = DbSingleton::getTokoDb();
		$r=$db->query("select max(id) as mid from contacts_bottom_new;"); $max_id=0+$db->result($r,0,"mid")+1;
		$db->query("insert into contacts_bottom_new (`id`,`status`) values ('$max_id',1);");
		return $max_id;
	}
	
	function showContactsBotCard($contact_id){ $db=DbSingleton::getTokoDb();
        $form="";$form_htm=RD."/tpl/new/contacts_bottom_card.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
		$r=$db->query("select * from contacts_bottom_new where id='$contact_id';"); $n=$db->num_rows($r);
		if ($n>0){
			$id=$db->result($r,0,"id");
			$text=$db->result($r,0,"text");
			$icon=$db->result($r,0,"icon"); $icon_select=$this->showIcontSelectList($icon);
			$link=$db->result($r,0,"link");
			$status=$db->result($r,0,"status");
			$checked="";if ($status>0){$checked=" checked";}
			$form=str_replace("{id}",$id,$form);
			$form=str_replace("{text}",$text,$form);
			$form=str_replace("{icon_select}",$icon_select,$form);
			$form=str_replace("{link}",$link,$form);
			$form=str_replace("{status}",$checked,$form);
		}
		return $form;
  	}
	
	function saveContactsBot($contact_id,$text,$icon,$link,$status){ $db=DbSingleton::getTokoDb(); $answer=0;$err="Помилка збереження даних!";
		if ($contact_id>0){
			$db->query("update contacts_bottom_new set `text`='$text', `icon`='$icon', `link`='$link', `status`='$status' where `id`='$contact_id';");
			$answer=1;$err="";
		}
		return array($answer,$err);
	}
	
	function dropContactsBot($contact_id) { $db=DbSingleton::getTokoDb(); $answer=0;$err="Помилка збереження даних!";
		if ($contact_id>0) {
			$db->query("delete from contacts_bottom_new where id='$contact_id';");	
			$answer=1;$err="";
		}
		return array($answer,$err);	
    }
	
	//news=======================================================================================================================================
	
	function getLangCaption($lang_id) {$db = DbSingleton::getTokoDb();
		$r=$db->query("select caption from lang where id='$lang_id' limit 1;");
	    $caption=$db->result($r,0,"caption");
		return $caption;
	}
	
	function showNewsList() { $db = DbSingleton::getTokoDb(); $date=date("Y-m-d");$list="";
		$form=""; $form_htm=RD."/tpl/new/news.htm"; if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}						
		$r=$db->query("select * from news order by data desc;"); $n=$db->num_rows($r);							
		for ($i=1;$i<=$n;$i++){
			$id=$db->result($r,$i-1,"id");
			$lang=$db->result($r,$i-1,"lang_id"); $lang=$this->getLangCaption($lang);
			$caption=$db->result($r,$i-1,"caption");
			$short_desc=$db->result($r,$i-1,"short_desc");
			$data=$db->result($r,$i-1,"data");
			if ($data>$date) $color_data="style='background:coral'"; else $color_data="";
			$status=$db->result($r,$i-1,"status"); 
			if ($status) $color_status="style='background:lightgreen'"; else $color_status="style='background:lightpink'";
			$status=$this->getStatusCaption($status); 
			$list.="<tr style='cursor:pointer' onClick='showNewsCard(\"$id\")'>";
				$list.="<td $color_data>$data</td>";
				$list.="<td>$lang</td>";
				$list.="<td>$caption</td>";
				$list.="<td>$short_desc</td>";
				$list.="<td $color_status>$status</td>";
			$list.="</tr>";
		}
		$form=str_replace("{news_range}",$list,$form);
		return $form;
	}
		
	function loadNewsList() { $db = DbSingleton::getTokoDb(); $date=date("Y-m-d");$list="";
		$r=$db->query("select * from news order by data desc;"); $n=$db->num_rows($r);						
		for ($i=1;$i<=$n;$i++){
			$id=$db->result($r,$i-1,"id");
			$lang=$db->result($r,$i-1,"lang_id"); $lang=$this->getLangCaption($lang);
			$caption=$db->result($r,$i-1,"caption");
			$short_desc=$db->result($r,$i-1,"short_desc");
			$data=$db->result($r,$i-1,"data");
			if ($data>$date) $color_data="style='background:coral'"; else $color_data="";
			$status=$db->result($r,$i-1,"status"); 
			if ($status) $color_status="style='background:lightgreen'"; else $color_status="style='background:lightpink'";
			$status=$this->getStatusCaption($status); 
			$list.="<tr style='cursor:pointer' onClick='showNewsCard(\"$id\")'>";
				$list.="<td $color_data>$data</td>";
				$list.="<td>$lang</td>";
				$list.="<td>$caption</td>";
				$list.="<td>$short_desc</td>";
				$list.="<td $color_status>$status</td>";
			$list.="</tr>";
		}
		return $list;
	}
	
	function newNewsCard($lang){ $db = DbSingleton::getTokoDb(); $date=date("Y-m-d");
		$r=$db->query("select max(id) as mid from news;"); $max_id=0+$db->result($r,0,"mid")+1;
		$db->query("insert into news (`id`,`status`,`data`,`lang_id`) values ('$max_id',0,'$date','$lang');");
		return $max_id;
	}
	
	function showNewsCard($news_id){ $db=DbSingleton::getTokoDb();
        $form="";$form_htm=RD."/tpl/new/news_card.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
		$r=$db->query("select * from news where id='$news_id';"); $n=$db->num_rows($r);
		if ($n>0){
			$id=$db->result($r,0,"id");
			$caption=$db->result($r,0,"caption");
			$lang_id=$db->result($r,0,"lang_id"); $lang_val=$this->getLangCaption($lang_id);
			$short_desc=$db->result($r,0,"short_desc");
			$data=$db->result($r,0,"data");
			$status=$db->result($r,0,"status");
			$descr=$db->result($r,0,"desc");
			$checked=""; if($status>0){$checked=" checked";}
			$form=str_replace("{id}",$id,$form);
			$form=str_replace("{caption}",$caption,$form);
			$form=str_replace("{lang_id}",$lang_id,$form);
			$form=str_replace("{lang_val}",$lang_val,$form);
			$form=str_replace("{short}",$short_desc,$form);
			$form=str_replace("{data}",$data,$form);
			$form=str_replace("{descr}",$descr,$form);
			$form=str_replace("{status}",$checked,$form);
			$r2=$db->query("select id from news_galery where cat='$id'");
			$file_id=$db->result($r2,0,"id");
			$form=str_replace("{file_id}",$file_id,$form);
		}
		return $form;
  	}
	
	function saveNews($news_id,$caption,$data,$short,$descr,$status){ $db=DbSingleton::getTokoDb(); $answer=0;$err="Помилка збереження даних!";
		if ($news_id>0){
			$db->query("update news set `caption`='$caption', `data`='$data', `short_desc`='$short', `desc`='$descr', `status`='$status' where `id`='$news_id';");
			$answer=1;$err="";
		}
		return array($answer,$err);
	}
	
	function dropNews($news_id) { $db=DbSingleton::getTokoDb(); $answer=0;$err="Помилка збереження даних!";
		if ($news_id>0) {
			$db->query("delete from news where id='$news_id';");	
			$answer=1;$err="";
		}
		return array($answer,$err);	
    }
	
	function loadNewsPhoto($news_id,$lang_id) { $db=DbSingleton::getTokoDb();
		$form_htm=RD."/tpl/new/news_photo_block.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
		$r=$db->query("select * from news_galery where cat='$news_id';");
		$n=$db->num_rows($r);$list="";
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
			$db->query("delete from news_galery where cat='$news_id';");
			$answer=1;$err="";
		}
		return array($answer,$err);
	}

}