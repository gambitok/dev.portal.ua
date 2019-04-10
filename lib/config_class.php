<?php

class config {
	
	function get_meta_head(){$db=DbSingleton::getDb();
	    $r=$db->query("select * from config limit 0,1;");$n=$db->num_rows($r);
	    $title="";$title_short="";$keywords="";$descr="";$address="";
		if ($n>0){ 
			$title=$db->result($r,0,"title");
			$title_short=$db->result($r,0,"title_short");
			$keywords=$db->result($r,0,"key_words");
			$descr=$db->result($r,0,"descr");
			$address=$db->result($r,0,"address");
			define('SITE_NAME', $address);
		}
		return array($title,$title_short,$keywords,$descr,$address);
	}

	function get_title(){$db=DbSingleton::getDb();
		$r=$db->query("select * from config limit 0,1;");$n=$db->num_rows($r);
		if ($n>0){ 
			define('SITE_NAME', $db->result($r,0,"address"));
			return $db->result($r,0,"title"); 
		}
		if ($n==0){ return "Помилка підключення";}
	}

	function get_site_name(){$db=DbSingleton::getDb(); $address="";
		$r=$db->query("select address from config limit 0,1;");$n=$db->num_rows($r);
		if ($n>0){ $address=$db->result($r,0,"address");}
		return $address;
	}

	function ident_user(){ 
		define('SITE_NAME', $this->get_site_name());
		return SITE_NAME;
	}
	
	function get_link(){ return $_REQUEST["link"];}

	function getDepByLink($link){
		if (substr($link,-1)=="/"){$link=substr($link,0,strlen($link)-1);}
		$deps=explode("/", $link); $dep_up=0;$dep_cur=0;
		list($file_id,$file)=$this->findFileByLink($deps[0]);
		foreach($deps as $dep_id){
			list($file_id,$file)=$this->findFileByLink($dep_id);
			if ($file_id!="1"){
				$dep_id=$this->findIdByLink($dep_id,$dep_cur);
				$dep_up=$dep_cur;$dep_cur=$dep_id;
				break;
				$next_level=$this->checkNextLevel($dep_cur);
				if ($next_level==0){break;}
			}
			if ($file_id=="1"){
				$dep_id=$this->findIdByLink($dep_id,$dep_cur);
				$dep_up=$dep_cur;$dep_cur=$dep_id;
			}
		}
		return array($dep_up,$dep_cur,$file_id,$file);
	}

	function checkNextLevel($dep_up){$db=DbSingleton::getDb();
		$r=$db->query("select count(id) as kol from deps where dep_up='$dep_up';");
		return $db->result($r,0,"kol");
	}

	function findFileByLink($link){$db=DbSingleton::getDb(); $file_id=1;$file="main_page";$module_id="";$page_id="";$module_caption="";
		if (substr($link,-1)=="/"){$link=substr($link,0,strlen($link)-1);} $links=explode("/", $link);$link=$links[0];
		$r=$db->query("select mf.id, mf.file, mp.module as module_id, mp.id as page_id, m.id as mmodule_id, mf.caption as module_caption from module_files mf 
		    left join module_pages mp on (mp.file=mf.id) 
		    left join module m on (m.file=mf.id)
        where mp.link='$link' or m.link='$link' limit 0,1;");$n=$db->num_rows($r);
		if ($n==1){ 
			$file_id = $db->result($r,0,"id");
			$file=$db->result($r,0,"file");
			$module_id=$db->result($r,0,"module_id");
			$module_caption=$db->result($r,0,"module_caption")." - ";
			$page_id=$db->result($r,0,"page_id");
			if ($module_id=="" && $page_id==""){$module_id=$db->result($r,0,"mmodule_id");}
		}
		return array($file_id,$file,$module_id,$page_id,$module_caption);
	}

	function findIdByLink($link,$dep_up){$db=DbSingleton::getDb(); $slave=new slave; $lan=$slave->get_lan();
		if ($dep_up!=""){$where=" and dep_up='$dep_up'";} else $where="";
		$r=$db->query("select id from deps where link='$link' and lang_id='$lan' $where and ison='1' and visible='1' limit 0,1;");$n=$db->num_rows($r);
		if ($n==1){ return $db->result($r,0,"id");}
		else { return 0;}
	}

	function findLinkById($id){$db=DbSingleton::getDb(); //$slave=new slave; $lan=$slave->get_lan();
		$r=$db->query("select link from deps where id='$id' and ison='1' and visible='1' limit 0,1;");$n=$db->num_rows($r);
		if ($n==1){ return $db->result($r,0,"link");}
		else { return "";}
	}

	function getParams($dep,$link){$params="";
		if (substr($link,-1)=="/"){$link=substr($link,0,strlen($link)-1);}
		$deps=explode("/", $link); $ulink="";//$dep_up=0;$dep_cur=0;
		list($file_id,$file)=$this->findFileByLink($deps[0]);
		foreach($deps as $dep_link){$ulink.=$dep_link."/";
			list($file_id,$file)=$this->findFileByLink($dep_link);
			if ($file_id!="1"){
				$dep_id=$this->findIdByLink($dep_link,"");
				if ($dep_id==$dep){ 
					if (substr($ulink,-1)=="/"){$ulink=substr($ulink,0,strlen($ulink)-1);}
					$params=explode("/",str_replace($ulink,"",$link)); 
				} break;
			}
			if ($file_id=="1"){
				$dep_id=$this->findIdByLink($dep_link,"");
				if ($dep_id==$dep){ 
					if (substr($ulink,-1)=="/"){$ulink=substr($ulink,0,strlen($ulink)-1);}
					$params=explode("/",str_replace($ulink,"",$link)); break; 
				}
			}
		}
		return $params;
	}

	function getParams2($dep,$link){if (substr($link,-1)=="/"){$link=substr($link,0,strlen($link)-1);}
		$deps=explode("/", $link); //$ulink=""; $start_from=$this->getDepLink($dep);$start=0;
/*		foreach($deps as $dep_link){
			if ($dep_link!=$start_from and $start==0){$ulink.=$dep_link."/";}
			if ($dep_link==$start_from){$start=1;$link=str_replace($ulink,"",$link);}
			if ($start==1){ $params=explode("/",$link); break;} 
		}
*/		return $deps;
	}

	function getDepLink($id){$db=DbSingleton::getDb();
		$r=$db->query("select link from deps where id='$id' limit 0,1;");$n=$db->num_rows($r);
		if ($n==1){ return $db->result($r,0,"link");}
		else { return "";}
	}

	function showTableForm($tableName,$selId,$tableField){$db=DbSingleton::getDb();$form="";
		$r=$db->query("select `id`, IFNULL(`$tableField`,\"-\") as `$tableField` from `$tableName` where `$tableField`!='' order by id asc;");$n=$db->num_rows($r);
		for ($i=1;$i<=$n;$i++) {$id=$db->result($r,$i-1,"id");
			$form.="<option value='".$id."' ";if ($selId==$id){$form.=" selected='selected'";} $form.=">".$db->result($r,$i-1,"$tableField")."</option>";}
		return $form;
	}

	function showTableCaption($tableName,$selId,$tableField){$db=DbSingleton::getDb();$form="";
		$r=$db->query("select `$tableField` from `$tableName` where id='$selId' limit 0,1;");$n=$db->num_rows($r);
		if ($n==1) {$form=$db->result($r,0,"$tableField");}return $form;
	}

}
