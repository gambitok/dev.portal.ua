<?php
class gmanual {
	function get_name(){ if ($_POST["name"]==""){return $_GET["name"];} if ($_POST["name"]!=""){return $_POST["name"];} }
	function get_max_gmanual_id(){$db=DbSingleton::getDb();  $r=$db->query("select max(id) as mid from manual;");return $db->result($r,0,"mid")+1; }
	function get_manMid_id($key){$db=DbSingleton::getDb(); $r=$db->query("select max(mid) as mid from manual where `key`='$key';");$mid=0+$db->result($r,0,"mid")+1;return $mid;}

	
	function show_key_list(){	$db=DbSingleton::getDb();$slave=new slave;$mdl=new module;$url=$mdl->get_file_url("Gmanual");session_start();
		$form_htm=RD."/tpl/gmanual_key_list.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
		$r=$db->query("SELECT * FROM manual_keys where status='1' order by caption asc;");$n=$db->num_rows($r);$list="";
		if ($n>0){
			for ($i=1;$i<=$n;$i++){
				$id=$db->result($r,$i-1,"id");
				$gkey=$db->result($r,$i-1,"gkey");
				$caption=$db->result($r,$i-1,"caption");
				
				$op_btn="<a href='$url/edit/$id' class='btn btn-default btn-round' title='Перейменувати довідник'><i class='fa fa-edit'></i></a> 
				<!--<a href='$url/drop/$id' class='btn btn-default btn-round' title='Видалити довідник'><i class='fa fa-trash'></i></a>-->";
				$view_btn="<a href='$url/view/$gkey' class='btn btn-default btn-round' title='Переглянути довідник'><i class='fa fa-folder-open'></i></a>";
				
				$list.="
				<tr align='center'>
					<td align='center'>$i</td>
					<td>$view_btn</td>
					<td align='left'>$caption</td>
					<td>$gkey</td>
					<td>$op_btn</td>
				</tr>";
			}
		}
		if ($n==0){
			$list="<tr><td colspan=12 align='center'><h2>Інформацію про довідники не знайдено</h2></td></tr>";
		}
		$form=str_replace("{ModuleCaption}","Довідники системи",$form);
		$form=str_replace("{OperationCaption}","Реєстр довідників",$form);
		$form=str_replace("{keys_list}",$list,$form);
		$form=str_replace("{alert}",$alert,$form);
		$new_btn="<a href='$url/new' class='btn btn-success btn-round' title='Новий довідник'><i class='fa fa-plus'></i></a>";
		$form=str_replace("{new_button}",$new_btn,$form);
		return $form;
	}
	
	function show_gmanual_list($key){$db=DbSingleton::getDb();$slave=new slave;$mdl=new module;$url=$mdl->get_file_url("Gmanual");session_start();
		$form_htm=RD."/tpl/gmanual_list.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}$list="";
		$r=$db->query("SELECT * FROM manual where ison='1' and `key`='$key' order by mcaption asc;"); $n=$db->num_rows($r);
		for ($i=1;$i<=$n;$i++){
			$id=$db->result($r,$i-1,"id");
			$mid=$db->result($r,$i-1,"mid");
			$caption=$db->result($r,$i-1,"mcaption");
			
			$op_btn="<a href='#editItem' data-toggle=\"modal\" data-target=\".ItemModal\" onclick='setModalFormData(\"$id\",\"$caption\")' class='btn btn-default btn-round' title='Змінити значення'><i class='fa fa-edit'></i></a> 
				<!--<a href='$url/drop/$id' class='btn btn-default btn-round' title='Видалити довідник'><i class='fa fa-trash'></i></a>-->";
			$list.="
				<tr>
					<td align='center'>$i</td>
					<td align='center'>$id</td>
					<td>&nbsp; $caption</td>
					<td>$op_btn</td>
				</tr>";
		}
		if ($list==""){$list="<tr><td align='center' colspan='12'><h2>Довідник порожній</h2></td></tr>";}
		$form=str_replace("{list}",$list,$form);
		$form=str_replace("{gkey}",$key,$form);
		$form=str_replace("{ModuleCaption}","Довідники системи",$form);
		$form=str_replace("{GmanualCaption}",$this->getGmanualCaptionByKey($key),$form);
		return $form;
	}
	
	
	function sendGmanualRequest1($gkey,$id,$caption){$db=DbSingleton::getDb();$slave=new slave;$mdl=new module;$url=$mdl->get_file_url("Gmanual");session_start();$label="info";
		if ($caption==""){$label="error"; $message="Не заповнено поле назва змінної";}
		else{$caption=$slave->qq($caption);
			if ($id==0 || $id==""){ $mid=$this->get_manMid_id($gkey); 
				$db->query("insert into manual (`key`,`mid`,`mcaption`) values ('$gkey','$mid','$caption');");
				$message="Нову значення &quot;$caption&quot; успішно додано!";
			}else{
				$db->query("update manual set `mcaption`='$caption' where id='$id';");
				$message="Значення довідника &quot;$caption&quot; успішно відредаговано!";
			}
		}
		return array($message,$label);
	}
	function checkGkey($gkey,$id){$db=DbSingleton::getDb();$slave=new slave;$mdl=new module;$url=$mdl->get_file_url("Gmanual");session_start();$answer=1;
		if ($id>0 || $id!=""){ 
			$r=$db->query("select count(id) as `kol` from manual_keys where id='$id' and gkey='$gkey';");$ex=$db->result($r,0,"kol");
			if ($ex==1){$answer=0;}
			if ($ex==0){$answer=1; $message="Помилка ключа";}
		}
		if ($id==0 || $id==""){ 
			$r=$db->query("select count(id) as `kol` from manual_keys where gkey='$gkey' limit 0,1");$ex=$db->result($r,0,"kol");
			if ($ex==0){$answer=0;}
			if ($ex==1){$answer=1; $message="Вказаний вами ключ вже існує в системі. Придумайте інший.";}
		}
		return array($message,$answer);
	}
	
	
	function showGmanualList($key,$scaption){$db=DbSingleton::getDb();$slave=new slave; 
		$where="";if ($scaption!=""){$where=" and mcaption LIKE '%$scaption%'";}
		$form_htm=RD."/tpl/gmanual_list_content.htm";if (file_exists("$form_htm")){ $form=file_get_contents($form_htm);}$mdl=new module;$url=$mdl->get_file_url("gmanual");
		$r=$db->query("SELECT * FROM manual where ison='1' and `key`='$key' $where order by mcaption asc limit 0,100;");$n=$db->num_rows($r);$list="";
		for ($i=1;$i<=$n;$i++){
			$id=$db->result($r,$i-1,"id");
			$mid=$db->result($r,$i-1,"mid");
			$caption=$db->result($r,$i-1,"mcaption");
			$list.="
				<tr onclick='location.href=\"?$url&w=edit_gmanual&gmanualId=$id\"'>
					<td class='numeric'>$i</td>
					<td>&nbsp; $caption</td>
				</tr>";
		}
		if ($list==""){$list="<tr><td align='center' colspan=2><h2>Інформація відсутня</h2></td></tr>";}
		$form=str_replace("{list}",$list,$form);
		return $form;
	}
	function new_gmanual_form($key){$db=DbSingleton::getDb(); $slave=new slave;$mdl=new module;$url=$mdl->get_file_url("Gmanual");session_start();$label="info";
		$form_htm=RD."/tpl/gmanual_key_form.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
			
		$form=str_replace("{gmanual_id}",0,$form);
		$form=str_replace("{gkey}","",$form);
		$form=str_replace("{caption}","",$form);
		$visibility="";
		$form=str_replace("{back_link}",$url."/",$form);
			
		$form=str_replace("{module}", $slave->get_module(), $form);
		$form=str_replace("{module_page}", $slave->get_module_page(), $form);
		$form=str_replace("{file}", $slave->get_file(), $form);
		if ($n==0){
			$alert="Інформацію про довідник не знайдено";
		}
		$form=str_replace("{ModuleCaption}","Довідники системи",$form);
		$form=str_replace("{OperationCaption}","Створення довідника",$form);
		$form=str_replace("{alert}",$alert,$form);
		$form=str_replace("{visibility}",$visibility,$form);
		return $form;
	}
	function add_gmanual_form(){$db=DbSingleton::getDb(); $slave=new slave; $mdl=new module;$url=$mdl->get_file_url("gmanual");
		$gmanual_id=$this->get_max_gmanual_id();$caption=$slave->qq($_POST["caption"]);$gkey=$slave->qq($_POST["gkey"]);
		
		$db->query("insert into manual_keys (`id`,`gkey`,`caption`) values ('$gmanual_id','$gkey','$caption');");
		
		$form_htm=RD."/tpl/gmanual_save.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
		$message="Довідник \"$caption\" успішно створено";
		
		$form=str_replace("{ModuleCaption}","Довідники системи",$form);
		$form=str_replace("{OperationCaption}","Картка довідника",$form);
		$form=str_replace("{message}",$message,$form);
		$form=str_replace("{back_caption}","До загального реєстру",$form);
		$form=str_replace("{back_url}",$url."/",$form);
		return $form;
	}
	function edit_gmanual_form($gmanual_id){$db=DbSingleton::getDb(); $slave=new slave;$mdl=new module;$url=$mdl->get_file_url("Gmanual");session_start();$label="info";
		$form_htm=RD."/tpl/gmanual_key_form.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
		$r=$db->query("select * from manual_keys where id='$gmanual_id' and status='1';");$n=$db->num_rows($r);
		if ($n>0){
			$mid=$db->result($r,0,"mid");
			$caption=$slave->qqback_in($db->result($r,0,"caption"));
			$gkey=$slave->qqback_in($db->result($r,0,"gkey"));
		 	
			
			$form=str_replace("{gmanual_id}",$gmanual_id,$form);
			$form=str_replace("{gkey}",$gkey,$form);
			$form=str_replace("{caption}",$caption,$form);
			$visibility="";
			$form=str_replace("{back_link}",$url."/view/$org_id",$form);
			
		}
		$form=str_replace("{module}", $slave->get_module(), $form);
		$form=str_replace("{module_page}", $slave->get_module_page(), $form);
		$form=str_replace("{file}", $slave->get_file(), $form);
		if ($n==0){
			$alert="Інформацію про довідник не знайдено";
		}
		$form=str_replace("{ModuleCaption}","Довідники системи",$form);
		$form=str_replace("{OperationCaption}","Картка довідника",$form);
		$form=str_replace("{alert}",$alert,$form);
		$form=str_replace("{visibility}",$visibility,$form);
		return $form;
	}
	
	function save_gmanual_form(){$db=DbSingleton::getDb(); $slave=new slave; $mdl=new module;$url=$mdl->get_file_url("gmanual");
		$gmanual_id=$_POST["gmanual_id"];$caption=$slave->qq($_POST["caption"]);$gkey=$slave->qq($_POST["gkey"]);
		
		$db->query("update manual_keys set caption='$caption' where id='$gmanual_id' and gkey='$gkey';");
		
		$form_htm=RD."/tpl/gmanual_save.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
		$message="Інформацію про довідник успішно збережено";
		
		$form=str_replace("{ModuleCaption}","Довідники системи",$form);
		$form=str_replace("{OperationCaption}","Картка довідника",$form);
		$form=str_replace("{message}",$message,$form);
		$form=str_replace("{back_caption}","До загального реєстру",$form);
		$form=str_replace("{back_url}",$url."/",$form);
		return $form;
	}
	function DropGmanual($key,$gmanual_id){$db=DbSingleton::getDb();$answer="";$db->query("update manual set ison='0' where id='$gmanual_id';");$answer=1;return $answer;}
	
	function getGmanualCaptionByKey($key){$db=DbSingleton::getDb();$caption="";
		$r=$db->query("select caption from manual_keys where gkey='$key' limit 0,1;");$n=$db->num_rows($r);
		if ($n==1) {$caption=$db->result($r,0,"caption");}
		return $caption;
	}
	function get_gmanual_caption($gmanual){$db=DbSingleton::getDb();$caption="";
		$r=$db->query("select mcaption from manual where id='$gmanual' limit 0,1;");$n=$db->num_rows($r);
		if ($n==1) {$caption=$db->result($r,0,"mcaption");}
		return $caption;
	}
	function get_gmanual_cell($gmanual,$cellName){$db=DbSingleton::getDb();$value="";
		$r=$db->query("select `$cellName` from gmanual where id='$gmanual' limit 0,1;");$n=$db->num_rows($r);
		if ($n==1) {$value=$db->result($r,0,"$cellName");}
		return $value;
	}
	function showGmanualSelectList($gkey,$selId){$db=DbSingleton::getDb();$form="";
		$r=$db->query("select `id`,`mcaption` from `manual` where `key`='$gkey' order by mcaption,id asc;");$n=$db->num_rows($r);
		for ($i=1;$i<=$n;$i++) {$id=$db->result($r,$i-1,"id");
			$form.="<option value='".$id."' ";if ($selId==$id){$form.=" selected='selected'";} $form.=">".$db->result($r,$i-1,"mcaption")."</option>";}
		return $form;
	}
	function showTableForm($tableName,$selId,$tableField){$db=DbSingleton::getDb();$form="";
		$r=$db->query("select `id`,`$tableField` from `$tableName` order by id asc;");$n=$db->num_rows($r);
		for ($i=1;$i<=$n;$i++) {$id=$db->result($r,$i-1,"id");
			$form.="<option value='".$id."' ";if ($selId==$id){$form.=" selected='selected'";} $form.=">".$db->result($r,$i-1,"$tableField")."</option>";}
		return $form;
	}
	function show_navigation($item_id,$nav_menu){ $mdl=new module;$url=$mdl->get_file_url("gmanual");$nav_menu="<a class='navigation' href='?$url'>Контрагенти</a>";return $nav_menu;}
	function show_menu($w){$slave=new slave; $mdl=new module; $url=$mdl->get_file_url("gmanual");
		$menu="
		<div class='sideMenu' onclick='location.href=\"?$url\";'>Загальний реєстр</div><br />
		<div class='sideMenu' onclick='location.href=\"?$url&w=new_gmanual\";'>Нове значення</div><br />
		";
		return $menu;
	}
	
	function printGmanualCard($id){$db=DbSingleton::getDb(); $slave=new slave;
		$form_htm=RD."/tpl/gmanualCardPrint.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
		$r=$db->query("select c.*,ct.caption as ctype_caption,ot.caption as otype_caption FROM gmanual c left outer join gmanual_type ct on (ct.id=c.gmanual_type) left outer join org_type ot on (ot.id=c.org_type) where c.id='$id' and c.ison='1' limit 0,1;");$n=$db->num_rows($r);
		if ($n==1){
			$gmanual_type=$db->result($r,0,"ctype_caption");
			$org_type=$db->result($r,0,"otype_caption");
			$caption=$slave->qqback($db->result($r,0,"caption"));
			$chief=$slave->qqback($db->result($r,0,"chief"));
			$person=$slave->qqback($db->result($r,0,"person"));
			$address1=$slave->qqback($db->result($r,0,"address1"));
			$address2=$slave->qqback($db->result($r,0,"address2"));
			$email=$slave->qqback($db->result($r,0,"email"));
			$phone1=$slave->qqback($db->result($r,0,"phone1"));
			$phone2=$slave->qqback($db->result($r,0,"phone2"));
			$edrpou=$slave->qqback($db->result($r,0,"edrpou"));
			$reg_nomber=$slave->qqback($db->result($r,0,"reg_nomber"));
			$reg_data=$slave->qqback($db->result($r,0,"reg_data"));
			$reg_org=$slave->qqback($db->result($r,0,"reg_org"));
			$bank_rr=$slave->qqback($db->result($r,0,"bank_rr"));
			$bank_mfo=$slave->qqback($db->result($r,0,"bank_mfo"));
			$bank_caption=$slave->qqback($db->result($r,0,"bank_caption"));
			$comment=$slave->qqback($db->result($r,0,"comment"));
		}
		$form=str_replace("{gmanual_id}", $id, $form);
		$form=str_replace("{gmanual_type}", $gmanual_type, $form);
		$form=str_replace("{org_type}", $org_type, $form);
		$form=str_replace("{caption}", $caption, $form);
		$form=str_replace("{chief}", $chief, $form);
		$form=str_replace("{person}", $person, $form);
		$form=str_replace("{comment}", $comment, $form);
		$form=str_replace("{address1}", $address1, $form);
		$form=str_replace("{address2}", $address2, $form);
		$form=str_replace("{phone1}", $phone1, $form);
		$form=str_replace("{phone2}", $phone2, $form);
		$form=str_replace("{email}", $email, $form);
		$form=str_replace("{edrpou}", $edrpou, $form);
		$form=str_replace("{reg_nomber}", $reg_nomber, $form);
		$form=str_replace("{reg_data}", $reg_data, $form);
		$form=str_replace("{reg_org}", $reg_org, $form);
		$form=str_replace("{bank_rr}", $bank_rr, $form);
		$form=str_replace("{bank_mfo}", $bank_mfo, $form);
		$form=str_replace("{bank_caption}", $bank_caption, $form);
		$form=str_replace("{comment}", $comment, $form);
		return $form;
	}
	function getManualList($filter){$db=DbSingleton::getDb();$list="";$where="";if ($filter!=""){$where=" and caption like '%$filter%' ";}$k=0;
		$r=$db->query("SELECT * FROM gmanual where ison='1' $where order by caption asc limit 0,50;");$n=$db->num_rows($r);
		for ($i=1;$i<=$n;$i++){$k++;
			$id=$db->result($r,$i-1,"id");
			$caption=$db->result($r,$i-1,"caption");
			
			$tr_color="ffffff";if ($k==2){$tr_color="fafafa";$k=0;}
			$list.="
				<tr bgcolor='#$tr_color' class='tRow' align='left' onclick='setCheckbox(\"m$id\");' ondblclick='setValue(\"$id\");'>
					<th align='center' width='2%' bgcolor='#efefef'><input type='checkbox' id='m$id' value='1'></th>
					<th align='center' width='2%'>$i</th>
					<td><span id='v$id'>$caption</span></td>
				</tr>";
		}
		return $list;
	}
	function getManualCaption($id){$db=DbSingleton::getDb();$slave=new slave;$caption="";$r=$db->query("SELECT caption FROM gmanual where id='$id' limit 0,1;");$n=$db->num_rows($r);if ($n==1){$caption=$slave->qqback_in($db->result($r,0,"caption"));}return $caption;}
	
	function getFileCaption($key){$db=DbSingleton::getDb();$slave=new slave;$caption="";$r=$db->query("SELECT caption FROM module_files where file='$key' limit 0,1;");$n=$db->num_rows($r);if ($n==1){$caption=$slave->qqback_in($db->result($r,0,"caption"));}return $caption;}
	
}
?>