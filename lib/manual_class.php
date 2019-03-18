<?php
class manual {
	
	function loadManualData($key,$manValue,$manText){$db=new db;$slave=new slave;$list="";$k=0;
		$form_htm=RD."/tpl/manual_list.htm";$form="";if (file_exists("$form_htm")){$form = file_get_contents($form_htm);}
		$r=$db->query("SELECT *	FROM manual where `key`='$key' order by mcaption,id asc;"); $n=$db->num_rows($r);
		for ($i=1;$i<=$n;$i++){$k+=1;
			$id=$db->result($r,$i-1,"id");
			$mid=$db->result($r,$i-1,"mid");
			$mvalue=$db->result($r,$i-1,"mvalue");
			$mvalue_num=$db->result($r,$i-1,"mvalue_num");
			$mcaption=$db->result($r,$i-1,"mcaption");

			$list.="
				<tr onclick='setValue(\"$mid\");'>
					<td>$mid</td>
					<td align='left'><span id='v$mid'>$mcaption</span></td>
					<!--<td align='left'> &nbsp; $mvalue</td>-->
				</tr>";
		}
		$form=str_replace("{list}",$list,$form);
		$form=str_replace("{key}",$key,$form);
		return $form;
	}
	function new_man_id(){$db=new db; $r=$db->query("select max(id) as mid from manual;");$mid=0+$db->result($r,0,"mid")+1;$db->query("insert into manual (id) values('$mid');");return $mid;}
	function get_manMid_id($key){$db=new db; $r=$db->query("select max(mid) as mid from manual where `key`='$key';");$mid=0+$db->result($r,0,"mid")+1;return $mid;}
	
	function AddManualValue($key,$manText){$db=new db;$slave=new slave;
		$mid=$this->new_man_id();$manValue=$this->get_manMid_id($key);$manText=$slave->qq($manText);
		$db->query("update manual set mcaption='$manText', mid='$manValue',`key`='$key' where id='$mid';");
		return array($manValue,$manText);
	}
	
	function addNewCity($region_id,$name){$db=new db;$slave=new slave;$id=0;
		$region_id=$slave->qq($region_id);$name=$slave->qq($name);
		$r=$db->query("select max(`CITY_ID`) as mid from T2_CITY;");$id=0+$db->result($r,0,"mid")+1;
		$db->query("insert into T2_CITY (`CITY_ID`,`CITY_NAME`,`REGION_ID`,`ALFA2`,`ALFA3`) value ('$id','$name','$region_id','".strtoupper(substr($name,0,2))."','".strtoupper(substr($name,0,3))."');");
		return $id;
	}
	
	
	function getManualMCaption($key,$mid){$db=new db;$caption="";
		$r=$db->query("select mcaption from manual where `key`='$key' and `mid`='$mid' limit 0,1;");$n=$db->num_rows($r);
		if ($n==1) {$caption=$db->result($r,0,"mcaption");}
		if ($table_type==0){
			if ($key=="workers"){require_once(RD."/lib/workers_class.php"); $workers=new worker; $caption=$workers->getManualCaption($mid);}
			if ($key=="firms"){require_once(RD."/lib/firms_class.php"); $firms=new firm; $caption=$firms->getManualCaption($mid);}
		}
		return $caption;
	}
	function showManualSelectList($gkey,$selId){$db=new db;$form="";
		$r=$db->query("select `mid`,`mcaption` from `manual` where `key`='$gkey' order by mid,id asc;");$n=$db->num_rows($r);
		for ($i=1;$i<=$n;$i++) {$mid=$db->result($r,$i-1,"mid");
			$form.="<option value='".$mid."' ";if ($selId==$mid){$form.=" selected='selected'";} $form.=">".$db->result($r,$i-1,"mcaption")."</option>";}
		return $form;
	}
	function showTableForm($tableName,$selId,$tableField){$db=new db;$form="";
		$r=$db->query("select `id`,`$tableField` from `$tableName` order by id asc;");$n=$db->num_rows($r);
		for ($i=1;$i<=$n;$i++) {$id=$db->result($r,$i-1,"id");
			$form.="<option value='".$id."' ";if ($selId==$id){$form.=" selected='selected'";} $form.=">".$db->result($r,$i-1,"$tableField")."</option>";}
		return $form;
	}
}
class manualD { 
	function loadManualDData($key,$manValue,$manText){$db=new db;$slave=new slave;$list="";$k=0;
		$form_htm=RD."/tpl/manualD_list.htm";$form="";if (file_exists("$form_htm")){$form = file_get_contents($form_htm);}
		$r=$db->query("SELECT *	FROM manual_desc where `key`='$key' order by mcaption,id asc limit 0,100;"); $n=$db->num_rows($r);
		for ($i=1;$i<=$n;$i++){$k+=1;
			$id=$db->result($r,$i-1,"id");
			$mid=$db->result($r,$i-1,"mid");
			$mdesc=strip_tags($db->result($r,$i-1,"mdesc"));$mdesc=substr($mdesc,0,200);
			$mcaption=$db->result($r,$i-1,"mcaption");
			$tr_color="ffffff";if ($k==2){$tr_color="fafafa";$k=0;}
			$list.="
				<tr bgcolor='#$tr_color' class='tRow' align='center'>
					<td width='5%'><img src='images/edit.png' border=0 onclick='editManualDForm(\"$id\")' style='cursor:pointer;'></td>
					<td width='5%' onclick='setValueD(\"$id\");'>$mid</td>
					<td width='20%' align='left' onclick='setValueD(\"$id\");'><span id='v$mid'>$mcaption</span></td>
					<td align='left' onclick='setValueD(\"$id\");'> &nbsp; $mdesc</td>
				</tr>";
		}
		$form=str_replace("{list}",$list,$form);
		$form=str_replace("{key}",$key,$form);
		return $form;
	}
	function showManualDList($key,$SCaption){$db=new db;$slave=new slave; $where="";$form="<table width='100%' border='0'>{list}</table>";
		if ($SCaption!=""){$where=" and mcaption LIKE '%$SCaption%'";}
		$r=$db->query("SELECT *	FROM manual_desc where `key`='$key' $where order by mcaption,id $asc limit 0,100;");	$n=$db->num_rows($r);$list="";$k=0;
		for ($i=1;$i<=$n;$i++){$k+=1;
			$id=$db->result($r,$i-1,"id");
			$mid=$db->result($r,$i-1,"mid");
			$mdesc=strip_tags($db->result($r,$i-1,"mdesc"));$mdesc=substr($mdesc,0,200);
			$mcaption=$db->result($r,$i-1,"mcaption");
			$tr_color="ffffff";if ($k==2){$tr_color="fafafa";$k=0;}
			$list.="
			<tr bgcolor='#$tr_color' class='tRow' align='center'>
				<td width='5%'><img src='images/edit.png' border=0 onclick='editManualDForm(\"$id\")' style='cursor:pointer;'></td>
				<th width='5%' onclick='setValueD(\"$id\");'>$mid</th>
				<td width='20%' onclick='setValueD(\"$id\");' align='left'><span id='v$mid'>$mcaption</span></td>
				<td align='left' onclick='setValueD(\"$id\");'> &nbsp; $mdesc</td>
			</tr>";
		}
		$form=str_replace("{list}",$list,$form);
		return $form;
	}
	function new_man_id(){$db=new db; $r=$db->query("select max(id) as mid from manual_desc;");$mid=0+$db->result($r,0,"mid")+1;$db->query("insert into manual_desc (id) values('$mid');");return $mid;}
	function get_manMid_id($key){$db=new db; $r=$db->query("select max(mid) as mid from manual_desc where `key`='$key';");$mid=0+$db->result($r,0,"mid")+1;return $mid;}
	
	function AddManualD($key,$caption,$desc){$db=new db;$slave=new slave;
		$mid=$this->new_man_id();$manValue=$this->get_manMid_id($key);$caption=$slave->qq($caption);$desc=$slave->qq($desc);
		$db->query("update manual_desc set mcaption='$caption', mdesc='$desc', mid='$manValue',`key`='$key' where id='$mid';");
		return "ok";
	}
	function saveManualD($id,$key,$caption,$desc){$db=new db;$slave=new slave;
		$caption=$slave->qq($caption);$desc=$slave->qq($desc);
		$db->query("update manual_desc set mcaption='$caption', mdesc='$desc' where id='$id';");
		return "ok";
	}
	function setValueD($id){$db=new db;$caption="";$desc="";
		$r=$db->query("select mcaption,mdesc from manual_desc where `id`='$id' limit 0,1;");$n=$db->num_rows($r);
		if ($n==1) {$caption=$db->result($r,0,"mcaption");$desc=$db->result($r,0,"mdesc");}
		return array($caption,$desc);
	}
	function getManualDMCaption($key,$id){$db=new db;$caption="";
		$r=$db->query("select mcaption from manual_desc where `key`='$key' and `id`='$id' limit 0,1;");$n=$db->num_rows($r);
		if ($n==1) {$caption=$db->result($r,0,"mcaption");}
		return $caption;
	}
}
class manualK { 
	function loadManualKData($key,$manValue,$text,$filter){$db=new db;$slave=new slave;$list="";$k=0;
		$form_htm=RD."/tpl/manualK_list.htm";$form="";if (file_exists("$form_htm")){$form = file_get_contents($form_htm);}
		$where="";if ($filter!=""){$where=" and znos_from<='$filter' and znos_to>='$filter'";}
		$r=$db->query("SELECT *	FROM manual_constructiv where `key`='$key' $where order by mid,mcaption,id asc limit 0,100;"); $n=$db->num_rows($r);
		for ($i=1;$i<=$n;$i++){$k+=1;
			$id=$db->result($r,$i-1,"id");
			$mid=$db->result($r,$i-1,"mid");
			$mcaption=$db->result($r,$i-1,"mcaption");
			$mdesc=strip_tags($db->result($r,$i-1,"mdesc"));
			$znos_from=$db->result($r,$i-1,"znos_from");
			$znos_to=$db->result($r,$i-1,"znos_to");
			$tr_color="ffffff";if ($k==2){$tr_color="fafafa";$k=0;}
			$list.="
				<tr bgcolor='#$tr_color' class='tRow' align='center'>
					<td width='5%'><input type='checkbox' id='constructiv_$i' value='$id'></td>
					<td width='5%'>$mid</td>
					<td width='40%' align='left'><span id='contr_cap_$i'>$mcaption</span></td>
					<td align='left'> &nbsp; $mdesc</td>
					<td width='5%'>$znos_from</td>
					<td width='5%'>$znos_to</td>
				</tr>";
		}
		$form=str_replace("{list}",$list,$form);
		$form=str_replace("{key}",$key,$form);
		$form=str_replace("{kolKonstr}",$n,$form);
		return $form;
	}
	function setValueD($id){$db=new db;$caption="";$desc="";
		$r=$db->query("select mcaption,mdesc from manual_constructiv where `id`='$id' limit 0,1;");$n=$db->num_rows($r);
		if ($n==1) {$caption=$db->result($r,0,"mcaption");$desc=$db->result($r,0,"mdesc");}
		return array($caption,$desc);
	}
	function getManualKMCaption($key,$id){$db=new db;$caption="";if ($id==""){$id=0;}$id=str_replace(";",",",$id);if (substr($id,-1)==","){$id=substr($id,0,-1);}
		$r=$db->query("select mcaption from manual_constructiv where `key`='$key' and `id` IN ($id);");$n=$db->num_rows($r);
		for ($i=1;$i<=$n;$i++) {$caption.=$db->result($r,$i-1,"mcaption").";";}
		return $caption;
	}
	function getManualKMDesc($key,$id){$db=new db;$desc="";if ($id==""){$id=0;}$id=str_replace(";",",",$id);if (substr($id,-1)==","){$id=substr($id,0,-1);}
		$r=$db->query("select mdesc from manual_constructiv where `key`='$key' and `id` IN ($id);");$n=$db->num_rows($r);
		for ($i=1;$i<=$n;$i++) {$desc.=$db->result($r,$i-1,"mdesc").";";}
		return $desc;
	}
}
class manualP {
	function loadManualPData($key,$manValue,$manText,$parrKey,$parrKeyId){$db=new db;$slave=new slave;$list="";$k=0;if ($parrKeyId==""){$parrKeyId=0;}
		$form_htm=RD."/tpl/manualP_list.htm";$form="";if (file_exists("$form_htm")){$form = file_get_contents($form_htm);}
		$r=$db->query("SELECT *	FROM manual where `key`='$key' and parrentKey='$parrKey' and parrentKeyId='$parrKeyId' order by mcaption,id asc limit 0,100;"); $n=$db->num_rows($r);
		for ($i=1;$i<=$n;$i++){$k+=1;
			$id=$db->result($r,$i-1,"id");
			$mid=$db->result($r,$i-1,"mid");
			$mvalue=$db->result($r,$i-1,"mvalue");
			$mvalue_num=$db->result($r,$i-1,"mvalue_num");
			$mcaption=$db->result($r,$i-1,"mcaption");

			$tr_color="ffffff";if ($k==2){$tr_color="fafafa";$k=0;}
			$list.="
				<tr bgcolor='#$tr_color' class='tRow' align='center' onclick='setValue(\"$mid\");'>
					<td width='7%' bgcolor='#efefef'></td>
					<td width='2%'>$mid</td>
					<td align='left'><span id='v$mid'>$mcaption</span></td>
					<td width='10%' align='left'> &nbsp; $mvalue</td>
				</tr>";
		}
		$form=str_replace("{list}",$list,$form);
		$form=str_replace("{key}",$key,$form);
		$form=str_replace("{parrKey}",$parrKey,$form);
		return $form;
	}
	function showManualPList($key,$SCaption,$parrKey,$parrKeyId){$db=new db;$slave=new slave; $where="";$form="<table width='100%' border='0'>{list}</table>";
		if ($SCaption!=""){$where=" and mcaption LIKE '%$SCaption%'";}if ($parrKeyId==""){$parrKeyId=0;}
		$r=$db->query("SELECT *	FROM manual where `key`='$key' and parrentKey='$parrKey' and parrentKeyId='$parrKeyId' $where order by mcaption,id $asc limit 0,100;");	$n=$db->num_rows($r);$list="";$k=0;
		for ($i=1;$i<=$n;$i++){$k+=1;
			$id=$db->result($r,$i-1,"id");
			$mid=$db->result($r,$i-1,"mid");
			$mvalue=$db->result($r,$i-1,"mvalue");
			$mvalue_num=$db->result($r,$i-1,"mvalue_num");
			$mcaption=$db->result($r,$i-1,"mcaption");
			$tr_color="ffffff";if ($k==2){$tr_color="fafafa";$k=0;}
			$list.="
			<tr bgcolor='#$tr_color' class='tRow' align='center' onclick='setValue(\"$mid\");'>
				<th width='7%' bgcolor='#efefef'></th>
				<th width='2%'>$mid</th>
				<td align='left'><span id='v$mid'>$mcaption</span></td>
				<td width='10%' align='left'> &nbsp; $mvalue</td>
			</tr>";
		}
		$form=str_replace("{list}",$list,$form);
		return $form;
	}
	function new_man_id(){$db=new db; $r=$db->query("select max(id) as mid from manual;");$mid=0+$db->result($r,0,"mid")+1;$db->query("insert into manual (id) values('$mid');");return $mid;}
	function get_manMid_id($key){$db=new db; $r=$db->query("select max(mid) as mid from manual where `key`='$key';");$mid=0+$db->result($r,0,"mid")+1;return $mid;}
	function AddManualPValue($key,$manText,$parrKey,$parrKeyId){$db=new db;$slave=new slave;
		$mid=$this->new_man_id();$manValue=$this->get_manMid_id($key);$manText=$slave->qq($manText);
		$db->query("update manual set mcaption='$manText', mid='$manValue',`key`='$key', parrentKey='$parrKey', parrentKeyId='$parrKeyId' where id='$mid';");
		return array($manValue,$manText);
	}
	function getManualMCaption($key,$mid){$db=new db;$caption="";
		$r=$db->query("select mcaption from manual where `key`='$key' and `mid`='$mid' limit 0,1;");$n=$db->num_rows($r);
		if ($n==1) {$caption=$db->result($r,0,"mcaption");}
		return $caption;
	}
}
class manualManager {
	function loadManualManagerData($manValue,$manText,$bankOc){$db=new db;$slave=new slave;$list="";$k=0;if ($bankOc==""){$bankOc=0;}
		$form_htm=RD."/tpl/manualManager_list.htm";$form="";if (file_exists("$form_htm")){$form = file_get_contents($form_htm);}
		$r=$db->query("SELECT *	FROM bid_manager where `bankOc`='$bankOc' order by name,id asc limit 0,100;"); $n=$db->num_rows($r);
		for ($i=1;$i<=$n;$i++){$k+=1;
			$id=$db->result($r,$i-1,"id");
			$name=$db->result($r,$i-1,"name");
			$phone=$db->result($r,$i-1,"phone");
			$email=$db->result($r,$i-1,"email");
			$city=$db->result($r,$i-1,"city");
			$persent=$db->result($r,$i-1,"persent");

			$tr_color="ffffff";if ($k==2){$tr_color="fafafa";$k=0;}
			$list.="
				<tr bgcolor='#$tr_color' class='tRow' align='center' onclick='setManualManager(\"$id\");'>
					<td width='2%'>$i</td>
					<td align='left'><span id='v$id'>$name</span></td>
					<td width='10%' align='left'> &nbsp; $city</td>
					<td width='10%' align='left'> &nbsp; $phone</td>
					<td width='10%' align='left'> &nbsp; $email</td>
					<td width='10%' align='left'> &nbsp; $persent</td>
				</tr>";
		}
		$form=str_replace("{list}",$list,$form);
		return $form;
	}
	function showManualManagerList($SCaption,$bankOc){$db=new db;$slave=new slave; $where="";$form="<table width='100%' border='0'>{list}</table>";
		if ($SCaption!=""){$where=" and name LIKE '%$SCaption%'";}
		$r=$db->query("SELECT *	FROM bid_manager where `bankOc`='$bankOc' $where order by name,id asc limit 0,100;");	$n=$db->num_rows($r);$list="";$k=0;
		for ($i=1;$i<=$n;$i++){$k+=1;
			$id=$db->result($r,$i-1,"id");
			$name=$db->result($r,$i-1,"name");
			$phone=$db->result($r,$i-1,"phone");
			$email=$db->result($r,$i-1,"email");
			$city=$db->result($r,$i-1,"city");
			$persent=$db->result($r,$i-1,"persent");

			$tr_color="ffffff";if ($k==2){$tr_color="fafafa";$k=0;}
			$list.="
				<tr bgcolor='#$tr_color' class='tRow' align='center' onclick='setManualManager(\"$id\");'>
					<td width='2%'>$i</td>
					<td align='left'><span id='v$id'>$name</span></td>
					<td width='10%' align='left'> &nbsp; $city</td>
					<td width='10%' align='left'> &nbsp; $phone</td>
					<td width='10%' align='left'> &nbsp; $email</td>
					<td width='10%' align='left'> &nbsp; $persent</td>
				</tr>";
		}
		$form=str_replace("{list}",$list,$form);
		return $form;
	}
	function new_man_id(){$db=new db; $r=$db->query("select max(id) as mid from bid_manager;");$mid=0+$db->result($r,0,"mid")+1;$db->query("insert into bid_manager (id) values('$mid');");return $mid;}
	function AddManualManagerValue($name,$city,$phone,$email,$persent,$bankOc){$db=new db;$slave=new slave;
		$mid=$this->new_man_id();$name=$slave->qq($name);$city=$slave->qq($city);
		$db->query("update bid_manager set name='$name', city='$city', phone='$phone', email='$email', persent='$persent', `bankOc`='$bankOc' where id='$mid';");
		return array($mid,$name);
	}
	function getManualManagerCaption($id){$db=new db;$name="";
		$r=$db->query("select name from bid_manager where `id`='$id' limit 0,1;");$n=$db->num_rows($r);
		if ($n==1) {$name=$db->result($r,0,"name");}
		return $name;
	}
}
?>