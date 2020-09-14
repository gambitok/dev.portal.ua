<?php

class module {

	function show_user_info(){
	    session_start(); $user_name=$_SESSION["user_name"]; $slave=new slave;
		$form_htm=RD."/tpl/user_info.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
		$form=str_replace("{user_name}", $user_name, $form);
		$form=str_replace("{data}", $slave->data_word(date("Y-m-d")), $form);
		return $form;
	}

	function show_kours(){
	    $list="";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://api.privatbank.ua/p24api/pubinfo?exchange&coursid=3');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		//curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		$result = curl_exec($ch);
		$xml=simplexml_load_string ($result);
		$ex=$xml->row;
		foreach($ex as $cur){
			foreach($cur as $c){
				$cure=$c["ccy"];
				$val=$c["buy"];
				$list.="$cure: <strong>$val</strong> &nbsp; &nbsp; &nbsp;";
			}
		}
		return $list;
	}

	function show_menu($module_id,$page_id){$db=DbSingleton::getDb();
	    $menu="";if ($module_id=="" && $page_id==""){$module_id=1; $page_id=1;}
		$r=$db->query("SELECT * FROM `module` WHERE `ison`='1' ORDER BY `lenta`, `id` ASC;");$n=$db->num_rows($r);
		for ($i=1;$i<=$n;$i++){
			$id=$db->result($r,$i-1,"id");
			$caption=$db->result($r,$i-1,"caption");
			$link=$db->result($r,$i-1,"link");
			$icon=$db->result($r,$i-1,"icon");
			$active="";if ($id==$module_id){$active=" class=\"active\"";}
			$sub_menu=$this->show_sub_menu($id,$page_id); 
			if ($sub_menu==""){ $menu.="<li $active><a href='/$link'><i class='$icon'></i>$caption</a></li>"; }
			if ($sub_menu!=""){ $menu.="<li $active><a><i class='$icon'></i>$caption<span class='fa arrow'></span></a><ul class='nav nav-second-level'>$sub_menu</ul></li>";}
		}
		return $menu;
	}

	function show_sub_menu($module_id,$page_id){$db=DbSingleton::getDb();
		$r=$db->query("SELECT mp.id, mp.caption, mf.file, mp.link 
		FROM `module_pages` mp 
		    INNER JOIN `module_files` mf on (mf.id=mp.file) 
        WHERE mp.module='$module_id' ORDER BY mp.id ASC;");$n=$db->num_rows($r);$menu="";
		for ($i=1;$i<=$n;$i++){
			$id=$db->result($r,$i-1,"id");
			$caption=$db->result($r,$i-1,"caption");
			$link=$db->result($r,$i-1,"link");
			$active="";if ($id==$page_id){$active=" class=\"active\"";}
			$menu.="<li $active><a href='/$link'>$caption</a></li>";
		}
		return $menu;
	}

	function get_module_caption($module){$db=DbSingleton::getDb();
		$r=$db->query("SELECT `caption` FROM `module` WHERE `id`='$module';");$n=$db->num_rows($r);
		if ($n>0){ return $db->result($r,0,"caption");}	else { return "";}
	}

	function get_module_file($file,$var){$db=DbSingleton::getDb();$r="";
		if ($var==1){ $r=$db->query("SELECT `file` FROM `module_files` WHERE `id`='$file';");}
		if ($var==2){ $r=$db->query("SELECT `file` FROM `module_files` WHERE `file`='$file';");}
		$n=$db->num_rows($r);if ($n>0){ return $db->result($r,0,"file");} else { return "";}
	}

	function get_module_file_cap($file){$db=DbSingleton::getDb();
		$r=$db->query("SELECT `caption` FROM `module_files` WHERE `id`='$file';");$n=$db->num_rows($r);
		if ($n>0){ return $db->result($r,0,"caption");}else { return "";}
	}

	function show_file_form($file){$db=DbSingleton::getDb();
		$r=$db->query("SELECT * FROM `module_files` WHERE `system`='1' ORDER BY `id` ASC;");$n=$db->num_rows($r);
		$form="<select name='dep_file' id='dep_file' size=1 style='width:400px;'>";
		for ($i=1;$i<=$n;$i++){
			$id=$db->result($r,$i-1,"id");
			$caption=$db->result($r,$i-1,"caption");
			if ($id==$file){ $form.="<option value='$id' selected>$caption</option>";}
			if ($id!=$file){ $form.="<option value='$id'>$caption</option>";}
		}
		$form.="</form>";
		return $form;
	}
	
	function show_dep_menu(){$db=DbSingleton::getDb();
	    $url=$this->get_url();
		$menu="
		<script type=\"text/javascript\" src=\"js/tree_menu/cooltree.js\"></script>
		<style>	.treeNode { text-decoration: none; color: black; font: 8pt tahoma;} </style>
		<script>
		var left=document.getElementById(\"dep_menu\").style.left-1;
		var top=document.getElementById(\"dep_menu\").style.top;
		var TREE_FORMAT = [	left,	top,	true, [\"images/collapsed_button.gif\", \"images/expanded_button.gif\", \"images/blank.gif\"], [16, 16, 16],	false,	[\"\", \"\", \"\"],	[0, 0],	[0, 16, 32, 48, 64, 80, 96, 112, 128],	\"\",	\"treeNode\",	[],	false,	[0, 0]];
		</script>
		<script>
		var TREE_NODES = [";
		$r=$db->query("SELECT * FROM `deps` WHERE `dep_up`='0' ORDER BY `lenta`, `id` ASC;");$n=$db->num_rows($r);$_SESSION["k"]=0;
		for ($i=1;$i<=$n;$i++){
			$id=$db->result($r,$i-1,"id");
			$dep_up=$db->result($r,$i-1,"dep_up");
			$caption=$db->result($r,$i-1,"caption");
			$file=$db->result($r,$i-1,"file");
            //	if ($i>1){$menu.=",,,,,\n";}
			$menu.="[\"$caption\", \"?$url&dep_up=$dep_up&dep_cur=$id&file=$file\", null";
			$rmenu=$this->get_sub_menu($id);
			if ($rmenu=="" and $i==$n){$menu.="]]\n";}
			if ($rmenu=="" and $i<$n){$menu.="],\n";}
			if ($rmenu!=""){$menu.=",\n".$rmenu;}
			$k=$_SESSION["k"];
			if ($k>0){
				$sub=substr($menu,strlen($menu)-2,1);
				if ($sub==","){$menu=substr($menu,0,strlen($menu)-3);}
				for ($j=1;$j<=$k;$j++) {$menu.="]";}if ($i<$n){$menu.=",";} $_SESSION["k"]=0;
			}
		}
		$menu=substr($menu,0,strlen($menu)-1);
		$menu.="];
		</script>
		<script>
		var tree1 = new COOLjsTree(\"tree1\", TREE_NODES, TREE_FORMAT);
		tree1.collapseAll();
		</script>";
		return $menu;
	}

	function get_sub_menu($id){	$db=DbSingleton::getDb();
	    session_start();$url=$this->get_url();$smenu="";
		$r=$db->query("SELECT * FROM `deps` WHERE `dep_up`='$id' ORDER BY `lenta`, `id` ASC;");$n=$db->num_rows($r);
		if ($n>0){$_SESSION["k"]+=1;}
		for ($i=1;$i<=$n;$i++){
			$cur_id=$db->result($r,$i-1,"id");
			$dep_up=$db->result($r,$i-1,"dep_up");
			$caption=$db->result($r,$i-1,"caption");
			$file=$db->result($r,$i-1,"file");
			$smenu.="[\"$caption\", \"?$url&dep_up=$dep_up&dep_cur=$cur_id&file=$file\", null";
			$rmenu=$this->get_sub_menu($cur_id);
			if ($rmenu=="" and $i==$n){$smenu.="]";$_SESSION["k"]-=1;$smenu.="],\n";}
			if ($rmenu=="" and $i<$n){$smenu.="],\n";}
			if ($rmenu!=""){$smenu.=",\n".$rmenu;}
		}
		return $smenu;
	}

	function get_url(){
		$url=$_SERVER["QUERY_STRING"];
		if (stristr($url,"&dep_up=")){ $url=ereg_replace("&dep_up=([0-9])*","",$url); }
		if (stristr($url,"&dep_cur=")){ $url=ereg_replace("&dep_cur=([0-9])*","",$url); }	
		if (stristr($url,"&cur_id=")){ $url=ereg_replace("&cur_id=([0-9])*","",$url); }	
		if (stristr($url,"&w=")){ $url=ereg_replace("&w=([a-z_])*","",$url); }	
		return $url;
	}

	function get_file_url($file=""){
        $url=$_SERVER["QUERY_STRING"];
		if (stristr($url,"&file=") === FALSE and $file!=""){$db=DbSingleton::getDb();
			$r=$db->query("SELECT mp.link as link2, m.link 
			FROM `module_files` mf 
			    LEFT OUTER JOIN module_pages mp ON ( mf.id = mp.file ) 
			    LEFT OUTER JOIN module m ON ( m.file = mf.id ) 
            WHERE mf.file = '$file';");$n=$db->num_rows($r);
			if ($n==1){
				$link=$db->result($r,0,"link");
				if ($link==""){$link=$db->result($r,0,"link2");}
				$url=SITE_NAME."/".$link;
			}
		}
		return $url;
	}

	function get_file_url2($file){
	    $url=$_SERVER["QUERY_STRING"];
		if (stristr($url,"&file=") === FALSE and $file!=""){
			$db=DbSingleton::getDb();
            $r=$db->query("SELECT mp.module, mp.file 
            FROM `module_pages` mp 
                INNER JOIN `module_files` mf on (mf.id=mp.file) 
            WHERE mf.file='$file';"); $n=$db->num_rows($r);
            if ($n==1){
                $url="module=".$db->result($r,0,"module")."&module_page=".$db->result($r,0,"file")."&file=".$file.$url;
            }
		}
		if (stristr($url,"&wn=")){ $url=ereg_replace("&wn=([a-z0-9_])*","",$url); }
		if (stristr($url,"&w=")){ $url=ereg_replace("&w=([a-z0-9_])*","",$url); }
		if (stristr($url,"&conf=")){ $url=ereg_replace("&conf=([a-z_])*","",$url); }
		if (stristr($url,"&var=")){ $url=ereg_replace("&var=([a-z0-9_])*","",$url); }
		if (stristr($url,"&cat_id=")){ $url=ereg_replace("&cat_id=([a-z0-9_])*","",$url); }
		if (stristr($url,"&top_id=")){ $url=ereg_replace("&top_id=([a-z0-9_])*","",$url); }
		if (stristr($url,"&clientId=")){ $url=ereg_replace("&clientId=([a-z0-9_])*","",$url); }
		if (stristr($url,"&docId=")){ $url=ereg_replace("&docId=([a-z0-9_])*","",$url); }
		if (stristr($url,"&firm_id=")){ $url=ereg_replace("&firm_id=([a-z0-9_])*","",$url); }
		if (stristr($url,"&department_id=")){ $url=ereg_replace("&department_id=([a-z0-9_])*","",$url); }
		if (stristr($url,"&users_id=")){ $url=ereg_replace("&users_id=([a-z0-9_])*","",$url); }
		if (stristr($url,"&ingrid_id=")){ $url=ereg_replace("&ingrid_id=([a-z0-9_])*","",$url); }
		if (stristr($url,"&calculation_id=")){ $url=ereg_replace("&calculation_id=([a-z0-9_])*","",$url); }
		if (stristr($url,"&catalogue_id=")){ $url=ereg_replace("&catalogue_id=([a-z0-9_])*","",$url); }
		if (stristr($url,"&journal_id=")){ $url=ereg_replace("&journal_id=([a-z0-9_])*","",$url); }
		if (stristr($url,"&journal_pay_id=")){ $url=ereg_replace("&journal_pay_id=([a-z0-9_])*","",$url); }
		return $url;
	}

}
