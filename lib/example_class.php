<?php

class example {

    function showModuleList() { $db=DbSingleton::getDb();
        $r=$db->query("SELECT * FROM `module` WHERE 1;"); $n=$db->num_rows($r); $list="";
        for ($i=1;$i<=$n;$i++) {
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"name");
            $status=$db->result($r,$i-1,"status");
            $list.="<tr onclick='showModuleCard($id)'>
                <td>$id</td>
                <td>$name</td>
                <td>$status</td>
            </tr>";
        }
        return $list;
    }

    function showModuleCard($id) { $db=DbSingleton::getDb();
        $form="";$form_htm=RD."/tpl/modules_card.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("SELECT * FROM `module` WHERE `id`='$id' LIMIT 1;");
        $name=$db->result($r,0,"name");
        $status=$db->result($r,0,"status");
        if ($id==0) {
            $name="";
            $status=0;
        }
        $form=str_replace("{module_id}",$id,$form);
        $form=str_replace("{module_name}",$name,$form);
        $form=str_replace("{module_status}",$status ? "checked" : "",$form);
        return $form;
    }

    function saveModuleCard($id,$name,$status) { $db=DbSingleton::getDb();
        $answer=0;$err="Помилка збереження даних!";
        if ($id>0){
            $db->query("UPDATE `module` SET `name`='$name', `status`='$status' WHERE `id`='$id' LIMIT 1;");
            $answer=1;$err="";
        }
        if ($id==0) {
            $r=$db->query("SELECT MAX(`id`) as mid FROM `module`;"); $id=0+$db->result($r,0,"mid")+1;
            $db->query("INSERT INTO `module` (`id`,`name`,`status`) VALUES ('$id','$name','$status');");
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

}