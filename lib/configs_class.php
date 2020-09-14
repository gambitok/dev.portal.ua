<?php

class configs {

    function showModuleList() { $db=DbSingleton::getDb();
        $r=$db->query("SELECT * FROM `module` WHERE 1;"); $n=$db->num_rows($r); $list="";
        for ($i=1;$i<=$n;$i++) {
            $id=$db->result($r,$i-1,"id");
            $caption=$db->result($r,$i-1,"caption");
            $link=$db->result($r,$i-1,"link");
            $icon=$db->result($r,$i-1,"icon");
            $file=$db->result($r,$i-1,"file");
            $lenta=$db->result($r,$i-1,"lenta");
            $ison=$db->result($r,$i-1,"ison");
            $list.="<tr onclick='showModuleCard($id)'>
                <td>$id</td>
                <td>$caption</td>
                <td>$link</td>
                <td>$icon</td>
                <td>$file</td>
                <td>$lenta</td>
                <td>$ison</td>
            </tr>";
        }
        return $list;
    }

    function showModuleCard($id) { $db=DbSingleton::getDb();
        $form="";$form_htm=RD."/tpl/modules_card.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("SELECT * FROM `module` WHERE `id`='$id' LIMIT 1;");
        $caption=$db->result($r,0,"caption");
        $link=$db->result($r,0,"link");
        $icon=$db->result($r,0,"icon");
        $file=$db->result($r,0,"file");
        $lenta=$db->result($r,0,"lenta");
        $ison=$db->result($r,0,"ison");
        if ($id==0) {
            $caption="";
            $link="";
            $icon="";
            $file=0;
            $lenta=0;
            $ison=0;
        }
        $form=str_replace("{module_id}",$id,$form);
        $form=str_replace("{module_caption}",$caption,$form);
        $form=str_replace("{module_link}",$link,$form);
        $form=str_replace("{module_icon}",$icon,$form);
        $form=str_replace("{module_file}",$file,$form);
        $form=str_replace("{module_lenta}",$lenta,$form);
        $form=str_replace("{module_ison}",$ison,$form);
        return $form;
    }

    function saveModuleCard($id,$caption,$link,$icon,$file,$lenta,$ison) { $db=DbSingleton::getDb();
        $answer=0;$err="Помилка збереження даних!";
        if ($id>0){
            $db->query("UPDATE `module` SET `caption`='$caption', `link`='$link', `icon`='$icon', `file`='$file', `lenta`='$lenta', `ison`='$ison' WHERE `id`='$id' LIMIT 1;");
            $answer=1;$err="";
        }
        if ($id==0) {
            $r=$db->query("SELECT MAX(`id`) as mid FROM `module`;"); $id=0+$db->result($r,0,"mid")+1;
            $db->query("INSERT INTO `module` (`id`,`caption`,`link`,`icon`,`file`,`lenta`,`ison`) VALUES ('$id','$caption','$link','$icon','$file','$lenta','$ison');");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function dropModuleCard($id) { $db=DbSingleton::getDb();
        $answer=0;$err="Помилка збереження даних!";
        if ($id>0) {
            $db->query("DELETE FROM `module` WHERE `id`='$id' LIMIT 1;");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    /*===============pAGE=============================================*/

    function showModulePagesList() {$db=DbSingleton::getDb();
        $r=$db->query("SELECT * FROM `module_pages` WHERE 1;"); $n=$db->num_rows($r); $list="";
        for ($i=1;$i<=$n;$i++) {
            $id=$db->result($r,$i-1,"id");
            $mid=$db->result($r,$i-1,"mid");
            $module=$db->result($r,$i-1,"module");
            $caption=$db->result($r,$i-1,"caption");
            $file=$db->result($r,$i-1,"file");
            $link=$db->result($r,$i-1,"link");
            $list.="<tr onclick='showModulePageCard($id)'>
                <td>$id</td>
                <td>$mid</td>
                <td>$module</td>
                <td>$caption</td>
                <td>$file</td>
                <td>$link</td>
            </tr>";
        }
        return $list;
    }

    function showModulePageCard($id) { $db=DbSingleton::getDb();
        $form="";$form_htm=RD."/tpl/module_pages_card.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("SELECT * FROM `module_pages` WHERE `id`='$id' LIMIT 1;");
        $mid=$db->result($r,0,"mid");
        $module=$db->result($r,0,"module");
        $caption=$db->result($r,0,"caption");
        $file=$db->result($r,0,"file");
        $link=$db->result($r,0,"link");
        if ($id==0) {
            $mid=0;
            $module=0;
            $caption="";
            $file=0;
            $link="";
        }
        $form=str_replace("{page_id}",$id,$form);
        $form=str_replace("{page_mid}",$mid,$form);
        $form=str_replace("{page_module}",$module,$form);
        $form=str_replace("{page_caption}",$caption,$form);
        $form=str_replace("{page_file}",$file,$form);
        $form=str_replace("{page_link}",$link,$form);
        return $form;
    }

    function saveModulePageCard($id,$mid,$module,$caption,$file,$link) { $db=DbSingleton::getDb();
        $answer=0;$err="Помилка збереження даних!";
        if ($id>0){
            $db->query("UPDATE `module_pages` SET `mid`='$mid', `module`='$module', `caption`='$caption', `file`='$file', `link`='$link' WHERE `id`='$id' LIMIT 1;");
            $answer=1;$err="";
        }
        if ($id==0) {
            $r=$db->query("SELECT MAX(`id`) as mid FROM `module_pages`;"); $id=0+$db->result($r,0,"mid")+1;
            $db->query("INSERT INTO `module_pages` (`id`,`mid`,`module`,`caption`,`file`,`link`) VALUES ('$id','$mid','$module','$caption','$file','$link');");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function dropModulePageCard($id) { $db=DbSingleton::getDb();
        $answer=0;$err="Помилка збереження даних!";
        if ($id>0) {
            $db->query("DELETE FROM `module_pages` WHERE `id`='$id' LIMIT 1;");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    /*===============fILE=============================================*/

    function showModuleFilesList() {$db=DbSingleton::getDb();
        $r=$db->query("SELECT * FROM `module_files` WHERE 1;"); $n=$db->num_rows($r); $list="";
        for ($i=1;$i<=$n;$i++) {
            $id=$db->result($r,$i-1,"id");
            $caption=$db->result($r,$i-1,"caption");
            $file=$db->result($r,$i-1,"file");
            $system=$db->result($r,$i-1,"system");
            $list.="<tr onclick='showModuleFileCard($id)'>
                <td>$id</td>
                <td>$caption</td>
                <td>$file</td>
                <td>$system</td>
            </tr>";
        }
        return $list;
    }

    function showModuleFileCard($id) { $db=DbSingleton::getDb();
        $form="";$form_htm=RD."/tpl/module_files_card.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("SELECT * FROM `module_files` WHERE `id`='$id' LIMIT 1;");
        $caption=$db->result($r,0,"caption");
        $file=$db->result($r,0,"file");
        $system=$db->result($r,0,"system");
        if ($id==0) {
            $caption="";
            $file=0;
            $system=0;
        }
        $form=str_replace("{file_id}",$id,$form);
        $form=str_replace("{file_caption}",$caption,$form);
        $form=str_replace("{file_file}",$file,$form);
        $form=str_replace("{file_system}",$system,$form);
        return $form;
    }

    function saveModuleFileCard($id,$caption,$file,$system) { $db=DbSingleton::getDb();
        $answer=0;$err="Помилка збереження даних!";
        if ($id>0){
            $db->query("UPDATE `module_files` SET `caption`='$caption', `file`='$file', `system`='$system' WHERE `id`='$id' LIMIT 1;");
            $answer=1;$err="";
        }
        if ($id==0) {
            $r=$db->query("SELECT MAX(`id`) as mid FROM `module_files`;"); $id=0+$db->result($r,0,"mid")+1;
            $db->query("INSERT INTO `module_files` (`id`,`caption`,`file`,`system`) VALUES ('$id','$caption','$file','$system');");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function dropModuleFileCard($id) { $db=DbSingleton::getDb();
        $answer=0;$err="Помилка збереження даних!";
        if ($id>0) {
            $db->query("DELETE FROM `module_files` WHERE `id`='$id' LIMIT 1;");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    /*===========================================================*/

	function show_configs(){$db=DbSingleton::getDb();$mdl=new module;$url=$mdl->get_file_url(); //$dep_up=$slave->get_dep_up();
        $form="";$form_htm=RD."/tpl/configs_show.htm";
		if (!file_exists("$form_htm")){ $form="Не знайдено файл шаблону"; }
		if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
		$r=$db->query("select * from config order by id desc limit 0,1;");
		$title=$db->result($r,0,"title");
		$key_words=$db->result($r,0,"key_words");
		$description=$db->result($r,0,"description");
		$form=str_replace("{url}","?$url&wn=edit",$form);
		$form=str_replace("{title}",$title,$form);
		$form=str_replace("{key_words}",$key_words,$form);
		$form=str_replace("{description}",$description,$form);
		return $form;
	}
	
	function edit_configs_form(){$db=DbSingleton::getDb(); $slave=new slave;
        $form="";$form_htm=RD."/tpl/configs_form.htm";
		if (!file_exists("$form_htm")){ $form="Не знайдено файл шаблону"; }
		if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
		$r=$db->query("select * from config order by id desc limit 0,1;");
		$title=$db->result($r,0,"title");
		$key_words=$db->result($r,0,"key_words");
		$description=$db->result($r,0,"description");
		$form=str_replace("{module}", $slave->get_module(), $form);
		$form=str_replace("{module_page}", $slave->get_module_page(), $form);
		$form=str_replace("{file}", $slave->get_file(), $form);
		$form=str_replace("{w}", $slave->get_w(), $form);
		$form=str_replace("{dep_up}", $slave->get_dep_up(), $form);
		$form=str_replace("{dep_cur}", $slave->get_dep_cur(), $form);
		$form=str_replace("{wn}", $slave->get_wn(), $form);
		$form=str_replace("{title}",$title,$form);
		$form=str_replace("{key_words}",$key_words,$form);
		$form=str_replace("{description}",$description,$form);
		return $form;
	}
	
	function save_configs_form(){$db=DbSingleton::getDb(); $slave=new slave;
		$title=$slave->qq($_POST["title"]);
		$key_words=$slave->qq($_POST["key_words"]);
		$description=$slave->qq($_POST["description"]);
		$db->query("update config set title='$title', key_words='$key_words', description='$description';");
        $form="";$form_htm=RD."/tpl/save_message.htm";
		if (!file_exists("$form_htm")){ $form="Не знайдено файл шаблону"; }
		if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
		$message="Конфігурацію сайту &quot;$title&quot; успішно збережено";
		$form=str_replace("{message}",$message,$form);
		$form=str_replace("{navigation}","",$form);
		$form=str_replace("{dep_menu}","",$form);
		return $form;
	}

}

