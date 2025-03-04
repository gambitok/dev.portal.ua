<?php

class tpoint {

    function newTpointCard() { $db = DbSingleton::getDb(); $dbt = DbSingleton::getTokoDb();
        session_start();
        $user_id=$_SESSION["media_user_id"];
        $r=$db->query("SELECT MAX(`id`) as mid FROM `T_POINT`;");
        $tpoint_id=0+$db->result($r,0,"mid")+1;
        $db->query("INSERT INTO `T_POINT` (`id`,`user_id`) VALUES ('$tpoint_id','$user_id');");
        $dbt->query("INSERT INTO `T_POINT` (`id`,`user_id`) VALUES ('$tpoint_id','$user_id');");
        return $tpoint_id;
    }

    function show_tpoint_list() { $db = DbSingleton::getTokoDb();
        $media_users=new media_users;
        $list="";
        $r=$db->query("SELECT t.*, t2cn.COUNTRY_NAME, t2st.STATE_NAME, t2rg.REGION_NAME, t2ct.CITY_NAME
        FROM `T_POINT` t 
            LEFT OUTER JOIN T2_COUNTRIES t2cn on t2cn.COUNTRY_ID=t.country 
            LEFT OUTER JOIN T2_STATE t2st on t2st.STATE_ID=t.state
            LEFT OUTER JOIN T2_REGION t2rg on t2rg.REGION_ID=t.region
            LEFT OUTER JOIN T2_CITY t2ct on t2ct.CITY_ID=t.city
        where t.status=1;");
        $n=$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"name");
            $state=$db->result($r,$i-1,"STATE_NAME");
            $region=$db->result($r,$i-1,"REGION_NAME");
            $city=$db->result($r,$i-1,"CITY_NAME");
            $address=$db->result($r,$i-1,"address");
            $chief=$db->result($r,$i-1,"chief");
            $worker_name=$media_users->getMediaUserName($chief);
            $clients_list=$this->loadTpointClientsShortList($id);
            $storage_list=$this->loadTpointStorageShortList($id);
            $list.="<tr style='cursor:pointer' onClick='showTpointCard(\"$id\")'>
                <td>$id</td>
                <td>$name</td>
                <td>$state</td>
                <td>$region</td>
                <td>$city</td>
                <td>$address</td>
                <td>$worker_name</td>
                <td>$clients_list</td>
                <td>$storage_list</td>
            </tr>";
        }
        return $list;
    }

    function showTpointCard($tpoint_id) { $db = DbSingleton::getTokoDb();
        $slave=new slave;
        session_start();
        $user_id=$_SESSION["media_user_id"]; $user_name=$_SESSION["user_name"];
        $form="";$form_htm=RD."/tpl/tpoint_card.htm";
        if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("SELECT * FROM `T_POINT` WHERE `id`='$tpoint_id' LIMIT 1;");
        $n=$db->num_rows($r);
        if ($n==0){
            $form_htm=RD."/tpl/access_deny.htm";
            if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        }
        if ($n==1){
            $tpoint_id=$db->result($r,0,"id");
            $name=$db->result($r,0,"name");
            $full_name=$db->result($r,0,"full_name");
            $chief=$db->result($r,0,"chief");
            $country=$db->result($r,0,"country");
            $state=$db->result($r,0,"state");
            $region=$db->result($r,0,"region");
            $city=$db->result($r,0,"city");
            $address=$db->result($r,0,"address");
            $form=str_replace("{tpoint_id}",$tpoint_id,$form);
            $form=str_replace("{tpoint_name}",$name,$form);
            $form=str_replace("{tpoint_full_name}",$full_name,$form);
            $form=str_replace("{workers_list}",$this->showWorkersSelectList($chief),$form);
            $form=str_replace("{address}",$address,$form);
            $form=str_replace("{country_list}",$slave->showSelectList("T2_COUNTRIES","COUNTRY_ID","COUNTRY_NAME",$country),$form);
            $form=str_replace("{state_list}",$slave->showSelectSubList("T2_STATE","COUNTRY_ID","$country","STATE_ID","STATE_NAME",$state),$form);
            $form=str_replace("{region_list}",$slave->showSelectSubList("T2_REGION","STATE_ID","$state","REGION_ID","REGION_NAME",$region),$form);
            $form=str_replace("{city_list}",$slave->showSelectSubList("T2_CITY","REGION_ID","$region","CITY_ID","CITY_NAME",$city),$form);
            $form=str_replace("{my_user_id}",$user_id,$form);
            $form=str_replace("{my_user_name}",$user_name,$form);
        }
        return $form;
    }

    function deleteTpoint($tpoint_id) { $db = DbSingleton::getTokoDb();
        $answer=0; $err="������� ���������� �����!";
        if($tpoint_id>0) {
            $db->query("UPDATE `T_POINT` SET `status`=0 WHERE `id`='$tpoint_id';");
            $answer=1; $err="";
        }
        return array($answer, $err);
    }

    function saveTpointGeneralInfo($tpoint_id,$name,$full_name,$address,$chief,$country_id,$state_id,$region_id,$city_id) { $db = DbSingleton::getDb();$dbt = DbSingleton::getTokoDb();
        $slave=new slave;
        $answer=0; $err="������� ���������� �����!";
        $tpoint_id=$slave->qq($tpoint_id);$name=$slave->qq($name);$full_name=$slave->qq($full_name);$address=$slave->qq($address);$chief=$slave->qq($chief);
        $country_id=$slave->qq($country_id);$state_id=$slave->qq($state_id);$city_id=$slave->qq($city_id);$region_id=$slave->qq($region_id);
        if ($tpoint_id>0) {
            $db->query("UPDATE `T_POINT` SET `name`='$name',status='1', `full_name`='$full_name', `address`='$address', `chief`='$chief', `country`='$country_id', `state`='$state_id', `city`='$city_id', `region`='$region_id' WHERE `id`='$tpoint_id';");
            $dbt->query("UPDATE `T_POINT` SET `name`='$name', status='1', `full_name`='$full_name', `address`='$address', `chief`='$chief', `country`='$country_id', `state`='$state_id', `city`='$city_id', `region`='$region_id' WHERE `id`='$tpoint_id';");
            $answer=1; $err="";
        }
        return array($answer, $err);
    }

    function showWorkersSelectList($sel_id) { $db = DbSingleton::getDb();
        $list="";
        $r=$db->query("SELECT * FROM `media_users` ORDER BY `name`, `id` ASC;");
        $n=$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"name");
            $sel="";if ($id==$sel_id){$sel=" selected";}
            $list.="<option value='$id' $sel>$name</option>";
        }
        return $list;
    }

    function showPriceRatingSelectList($sel_id) {
        $list="";$n=12;
        for ($i = 1; $i <= $n; $i++) {
            $sel="";if ($i==$sel_id){$sel=" selected";}
            $list.="<option value='$i' $sel>����� ".($i-1)."</option>";
        }
        return $list;
    }

    function showPriceRatingName($sel_id) {
        $name="����� ";
        if ($sel_id>0){$name.=($sel_id-1);}
        return $name;
    }

    function loadTpointStorageShortList($tpoint_id) { $db = DbSingleton::getTokoDb();
        $gmanual=new gmanual;
        $list="";
        $r=$db->query("SELECT ps.*, s.name as storage_name 
        FROM `T_POINT_STORAGE` ps 
            LEFT OUTER JOIN `STORAGE` s on s.id=ps.storage_id 
        WHERE ps.tpoint_id='$tpoint_id' AND ps.status='1' ORDER BY ps.id ASC;");
        $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++) {
            $storage_name=$db->result($r,$i-1,"storage_name");
            $local=$gmanual->get_gmanual_caption($db->result($r,$i-1,"local"));
            $list.="$i) $local - $storage_name<br>";
        }
        if ($n==0){$list="������ �������";}
        return $list;
    }

    function loadTpointStorage($tpoint_id) { $db = DbSingleton::getTokoDb();
        $gmanual=new gmanual;
        $list="";
        $form="";$form_htm=RD."/tpl/tpoint_storage_list.htm";
        if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("SELECT ps.*, s.name as storage_name, s.full_name as storage_full_name 
        FROM `T_POINT_STORAGE` ps 
            LEFT OUTER JOIN `STORAGE` s on s.id=ps.storage_id 
        WHERE ps.tpoint_id='$tpoint_id' AND ps.status='1' ORDER BY ps.id ASC;");
        $n=$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id=$db->result($r,$i-1,"id");
            $storage_name=$db->result($r,$i-1,"storage_name");
            $storage_full_name=$db->result($r,$i-1,"storage_full_name");
            $local=$gmanual->get_gmanual_caption($db->result($r,$i-1,"local"));
            $default=$db->result($r,$i-1,"default");
            $def_cap="-";if ($default==1){$def_cap="<i class='fa fa-check'></i>";}
            $list.="<tr>
                <td>
                    <button class='btn btn-sm btn-default' onClick='showTpointStorageForm(\"$tpoint_id\",\"$id\");'><i class='fa fa-edit'></i></button>
                    <button class='btn btn-sm btn-default' onClick='dropTpointStorage(\"$tpoint_id\",\"$id\");'><i class='fa fa-times'></i></button>
                </td>
                <td>$i</td>
                <td>$storage_name</td>
                <td>$storage_full_name</td>
                <td>$local</td>
                <td>$def_cap</td>
            </tr>";
        }
        if ($n==0){$list="<tr><td align='center' colspan=5><h3 class='text-center'>������ �������</h3></td></tr>";}
        $form=str_replace("{list_storage}",$list,$form);
        $form=str_replace("{tpoint_id}",$tpoint_id,$form);
        return $form;
    }

    function showTpointStorageForm($tpoint_id, $s_id) { $db = DbSingleton::getTokoDb();
        $gmanual=new gmanual;
        $form="";$form_htm=RD."/tpl/tpoint_storage_form.htm";
        if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("SELECT * FROM `T_POINT_STORAGE` WHERE `id`='$s_id' AND `tpoint_id`='$tpoint_id' LIMIT 1;");
        $storage_id=$db->result($r,0,"storage_id");
        $local=$db->result($r,0,"local");
        $default=$db->result($r,0,"default");
        $def_ch="";if ($default==1){$def_ch=" checked='checked'";}
        $form=str_replace("{tpoint_id}",$tpoint_id,$form);
        $form=str_replace("{s_id}",$s_id,$form);
        $form=str_replace("{storage_list}",$this->showTpointStorageSelectList($storage_id),$form);
        $form=str_replace("{local_list}",$gmanual->showGmanualSelectList("storage_local",$local),$form);
        $form=str_replace("{default_ch}",$def_ch,$form);
        return $form;
    }

    function showTpointSupplStorageForm($tpoint_id, $s_id) { $db = DbSingleton::getDb();
        $form="";$form_htm=RD."/tpl/tpoint_suppl_storage_form.htm";
        if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("SELECT * FROM `T_POINT_SUPPL_STORAGE` WHERE `id`='$s_id' AND `tpoint_id`='$tpoint_id' LIMIT 1;");
        $suppl_id=$db->result($r,0,"suppl_id");
        $storage_id=$db->result($r,0,"storage_id");
        $form=str_replace("{suppl_list}",$this->showTpointSupplSelectList($suppl_id),$form);
        $form=str_replace("{storage_list}",$this->showTpointSupplStorageSelectList($suppl_id,$storage_id),$form);
        $form=str_replace("{tpoint_id}",$tpoint_id,$form);
        $form=str_replace("{s_id}",$s_id,$form);
        return $form;
    }

    function dropTpointSupplStorageForm($tpoint_id, $s_id) { $db = DbSingleton::getDb(); $dbt = DbSingleton::getTokoDb();
        $slave=new slave;
        $answer=0; $err="������� ���������� �����!";
        $tpoint_id=$slave->qq($tpoint_id); $s_id=$slave->qq($s_id);
        if ($tpoint_id>0 && $s_id>0){
            $db->query("DELETE FROM `T_POINT_SUPPL_STORAGE` WHERE `id`='$s_id' AND `tpoint_id`='$tpoint_id';");
            $dbt->query("DELETE FROM `T_POINT_SUPPL_STORAGE` WHERE `id`='$s_id' AND `tpoint_id`='$tpoint_id';");
            $answer=1;$err="";
        }
        return array($answer, $err);
    }

    function loadSupplStorageList($suppl_id, $sel_id) { $db = DbSingleton::getDb();
        $list = "<option value='0'> -- ������ � ������ --</option>";
        $r = $db->query("SELECT * FROM `A_CLIENTS_STORAGE` WHERE `client_id`='$suppl_id' AND `status`='1' ORDER BY `name` ASC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r,$i-1,"id");
            $name = $db->result($r,$i-1,"name");
            $sel = "";
            if ($id==$sel_id){$sel=" selected";}
            $list.="<option value='$id' $sel>$name</option>";
        }
        return $list;
    }

    function saveTpointSupplStorageForm($tpoint_id,$s_id,$storage_id,$suppl_id) { $db = DbSingleton::getDb();$dbt = DbSingleton::getTokoDb();
        $slave=new slave;
        $answer=0;$err="������� ���������� �����!";
        $tpoint_id=$slave->qq($tpoint_id);$s_id=$slave->qq($s_id);$storage_id=$slave->qq($storage_id);$suppl_id=$slave->qq($suppl_id);
        if ($tpoint_id>0) {
            if ($s_id==0) {
                $r=$db->query("SELECT MAX(`id`) as mid FROM `T_POINT_SUPPL_STORAGE`;");$s_id=0+$db->result($r,0,"mid")+1;
                $db->query("INSERT INTO `T_POINT_SUPPL_STORAGE` (id,tpoint_id,storage_id,suppl_id) VALUES ('$s_id','$tpoint_id','$storage_id','$suppl_id');");
                $dbt->query("INSERT INTO `T_POINT_SUPPL_STORAGE` (id,tpoint_id,storage_id,suppl_id) VALUES ('$s_id','$tpoint_id','$storage_id','$suppl_id');");
            }
            if ($s_id>0) {
                $db->query("UPDATE `T_POINT_SUPPL_STORAGE` SET `storage_id`='$storage_id', `suppl_id`='$suppl_id' WHERE `id`='$s_id' AND `tpoint_id`='$tpoint_id';");
                $dbt->query("UPDATE `T_POINT_SUPPL_STORAGE` SET `storage_id`='$storage_id', `suppl_id`='$suppl_id' WHERE `id`='$s_id' AND `tpoint_id`='$tpoint_id';");
                $answer=1; $err="";
            }
        } else {
            $answer=0;
        }
        return array($answer, $err);
    }

    function saveTpointStorageForm($tpoint_id,$s_id,$storage_id,$local,$default) { $db = DbSingleton::getDb();$dbt = DbSingleton::getTokoDb();
        $slave=new slave;
        $answer=0; $err="������� ���������� �����!";
        $tpoint_id=$slave->qq($tpoint_id);$s_id=$slave->qq($s_id);$storage_id=$slave->qq($storage_id);$local=$slave->qq($local);$default=$slave->qq($default);
        if ($tpoint_id>0) {
            if ($s_id==0) {
                $r=$db->query("SELECT MAX(`id`) as mid FROM `T_POINT_STORAGE`;");$s_id=0+$db->result($r,0,"mid")+1;
                $db->query("INSERT INTO T_POINT_STORAGE (id,tpoint_id,`default`,status) VALUES ('$s_id','$tpoint_id','$default','1');");
                $dbt->query("INSERT INTO T_POINT_STORAGE (id,tpoint_id,`default`,status) VALUES ('$s_id','$tpoint_id','$default','1');");
            }
            if  ($s_id>0) {
                $db->query("UPDATE `T_POINT_STORAGE` SET `storage_id`='$storage_id', `local`='$local', `default`='$default' WHERE `id`='$s_id' AND `tpoint_id`='$tpoint_id';");
                $dbt->query("UPDATE `T_POINT_STORAGE` SET `storage_id`='$storage_id', `local`='$local', `default`='$default' WHERE `id`='$s_id' AND `tpoint_id`='$tpoint_id';");
                $answer=1; $err="";
            }
        } else {
            $answer=0;
        }
        return array($answer, $err);
    }

    function dropTpointStorage($tpoint_id, $s_id) { $db = DbSingleton::getDb(); $dbt = DbSingleton::getTokoDb();
        $slave=new slave;
        $answer=0; $err="������� ���������� �����!";
        $tpoint_id=$slave->qq($tpoint_id);$s_id=$slave->qq($s_id);
        if ($tpoint_id>0 && $s_id>0) {
            $db->query("UPDATE `T_POINT_STORAGE` SET `status`='0' WHERE `id`='$s_id' AND `tpoint_id`='$tpoint_id';");
            $dbt->query("UPDATE `T_POINT_STORAGE` SET `status`='0' WHERE `id`='$s_id' AND `tpoint_id`='$tpoint_id';");
            $answer=1; $err="";
        }
        return array($answer, $err);
    }

    function loadTpointClientsShortList($tpoint_id) { $db = DbSingleton::getDb();
        $list="";
        $r=$db->query("SELECT * FROM `T_POINT_CLIENTS` WHERE `tpoint_id`='$tpoint_id' AND `status`='1' ORDER BY `id` ASC;");
        $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++) {
            $client_id=$db->result($r,$i-1,"client_id");
            $client_name=$this->getAClientName($client_id);
            $vat_use=$db->result($r,$i-1,"vat_use");
            $vat="��� ���";
            if ($vat_use==1){$vat="� ���";}
            $list.="$i) $client_name: $vat<br>";
        }
        if ($n==0){$list="������ �������";}
        return $list;
    }

    function loadTpointClients($tpoint_id) { $db = DbSingleton::getDb();
        $gmanual=new gmanual;
        $form="";$form_htm=RD."/tpl/tpoint_clients_list.htm";
        if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $list="";
        $r=$db->query("SELECT * FROM `T_POINT_CLIENTS` WHERE `tpoint_id`='$tpoint_id' AND `status`='1' ORDER BY `id` ASC;");
        $n=$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id=$db->result($r,$i-1,"id");
            $client_id=$db->result($r,$i-1,"client_id");
            $client_name=$this->getAClientName($client_id);
            $sale_type=$gmanual->get_gmanual_caption($db->result($r,$i-1,"sale_type"));
            $tax_credit=$db->result($r,$i-1,"tax_credit");
            $in_use=$db->result($r,$i-1,"in_use");
            $in_use_cap="-";
            if ($in_use==1){$in_use_cap="<i class='fa fa-eye'></i>";}
            $list.="<tr>
                <td>
                    <button class='btn btn-sm btn-default' onClick='showTpointClientsForm(\"$tpoint_id\",\"$id\");'><i class='fa fa-edit'></i></button>
                    <button class='btn btn-sm btn-default' onClick='dropTpointClients(\"$tpoint_id\",\"$id\");'><i class='fa fa-times'></i></button>
                </td>
                <td>$i</td>
                <td>$client_name</td>
                <td>$sale_type</td>
                <td>$tax_credit</td>
                <td>$in_use_cap</td>
            </tr>";
        }
        if ($n==0) {
            $list="<tr><td align='center' colspan=5><h3 class='text-center'>������ �������</h3></td></tr>";
        }
        $form=str_replace("{list_clients}",$list,$form);
        $form=str_replace("{tpoint_id}",$tpoint_id,$form);
        return $form;
    }

    function showTpointClientsForm($tpoint_id, $s_id) { $db = DbSingleton::getDb();
        $gmanual=new gmanual;
        $form="";$form_htm=RD."/tpl/tpoint_clients_form.htm";
        if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("SELECT * FROM `T_POINT_CLIENTS` WHERE `id`='$s_id' AND `tpoint_id`='$tpoint_id' LIMIT 1;");
        $client_id=$db->result($r,0,"client_id"); $client_name=$this->getAClientName($client_id);
        $sale_type=$db->result($r,0,"sale_type");
        $tax_credit=$db->result($r,0,"tax_credit");
        $tax_inform=$db->result($r,0,"tax_inform");
        $in_use=$db->result($r,0,"in_use"); $inuse_checked="";if ($in_use==1){$inuse_checked=" checked";}
        $form=str_replace("{tpoint_id}",$tpoint_id,$form);
        $form=str_replace("{s_id}",$s_id,$form);
        $form=str_replace("{client_id}",$client_id,$form);
        $form=str_replace("{client_name}",$client_name,$form);
        $form=str_replace("{sale_type_list}",$gmanual->showGmanualSelectList("client_sale_type",$sale_type),$form);
        $form=str_replace("{tax_credit}",$tax_credit,$form);
        $form=str_replace("{tax_inform}",$tax_inform,$form);
        $form=str_replace("{inuse_checked}",$inuse_checked,$form);
        return $form;
    }

    function saveTpointClientsForm($tpoint_id,$s_id,$client_id,$sale_type,$tax_credit,$tax_inform,$in_use) { $db = DbSingleton::getDb();
        $slave=new slave;
        $answer=0;$err="������� ���������� �����!";
        $tpoint_id=$slave->qq($tpoint_id);$s_id=$slave->qq($s_id);$client_id=$slave->qq($client_id);$sale_type=$slave->qq($sale_type);
        $tax_credit=$slave->qq($tax_credit);$tax_inform=$slave->qq($tax_inform);$in_use=$slave->qq($in_use);
        if ($tpoint_id>0) {
            if ($s_id==0) {
                $r=$db->query("SELECT MAX(`id`) as mid FROM `T_POINT_CLIENTS`;");
                $s_id=0+$db->result($r,0,"mid")+1;
                $db->query("INSERT INTO `T_POINT_CLIENTS` (`id`,`tpoint_id`,`status`) VALUES ('$s_id','$tpoint_id','1');");
            }
            if  ($s_id>0) {
                if ($in_use==1) {
                    $db->query("UPDATE `T_POINT_CLIENTS` SET `in_use`='0' WHERE `in_use`='1' and `tpoint_id`='$tpoint_id' and `sale_type`='$sale_type';");
                }
                $db->query("UPDATE `T_POINT_CLIENTS` SET `client_id`='$client_id', `sale_type`='$sale_type', `tax_credit`='$tax_credit', `tax_inform`='$tax_inform', `in_use`='$in_use' WHERE `id`='$s_id' AND `tpoint_id`='$tpoint_id';");
                $answer=1; $err="";
            }
        } else {
            $answer=0;
        }
        return array($answer, $err);
    }

    function dropTpointClients($tpoint_id, $s_id) { $db = DbSingleton::getDb();
        $slave=new slave;
        $answer=0; $err="������� ���������� �����!";
        $tpoint_id=$slave->qq($tpoint_id);$s_id=$slave->qq($s_id);
        if ($tpoint_id>0 && $s_id>0){
            $db->query("UPDATE `T_POINT_CLIENTS` SET `status`='0' WHERE `id`='$s_id' AND `tpoint_id`='$tpoint_id';");
            $answer=1; $err="";
        }
        return array($answer, $err);
    }

    function loadTpointWorkers($tpoint_id) { $db = DbSingleton::getTokoDb();
        $media_users=new media_users;
        $form="";$form_htm=RD."/tpl/tpoint_workers_list.htm";
        if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $list="";
        $r=$db->query("SELECT * FROM `T_POINT_WORKERS` WHERE `tpoint_id`='$tpoint_id' AND `status`='1' ORDER BY `id` ASC;");
        $n=$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id=$db->result($r,$i-1,"id");
            $worker_id=$db->result($r,$i-1,"media_user_id");
            $worker_name=$media_users->getMediaUserName($worker_id);
            $list.="<tr>
                <td>
                    <button class='btn btn-sm btn-default' onClick='showTpointWorkersForm(\"$tpoint_id\",\"$id\");'><i class='fa fa-edit'></i></button>
                    <button class='btn btn-sm btn-default' onClick='dropTpointWorkers(\"$tpoint_id\",\"$id\");'><i class='fa fa-times'></i></button>
                </td>
                <td>$i</td>
                <td>$worker_name</td>
            </tr>";
        }
        if ($n==0) {
            $list="<tr><td align='center' colspan=5><h3 class='text-center'>������ �������</h3></td></tr>";
        }
        $form=str_replace("{list_workers}",$list,$form);
        $form=str_replace("{tpoint_id}",$tpoint_id,$form);
        return $form;
    }

    function showTpointWorkersForm($tpoint_id,$s_id) { $db = DbSingleton::getTokoDb();
        $form="";$form_htm=RD."/tpl/tpoint_workers_form.htm";
        if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("SELECT * FROM `T_POINT_WORKERS` WHERE `id`='$s_id' AND `tpoint_id`='$tpoint_id' LIMIT 1;");
        $media_user_id=$db->result($r,0,"media_user_id");
        $form=str_replace("{tpoint_id}",$tpoint_id,$form);
        $form=str_replace("{s_id}",$s_id,$form);
        $form=str_replace("{workers_list}",$this->showWorkersSelectList($media_user_id),$form);
        return $form;
    }

    function saveTpointWorkersForm($tpoint_id,$s_id,$worker_id) { $db = DbSingleton::getDb(); $dbt = DbSingleton::getTokoDb();
        $slave=new slave;
        $answer=0;$err="������� ���������� �����!";
        $tpoint_id=$slave->qq($tpoint_id);$s_id=$slave->qq($s_id);$worker_id=$slave->qq($worker_id);
        if ($tpoint_id>0) {
            if ($s_id==0) {
                $r=$db->query("SELECT MAX(`id`) as mid FROM `T_POINT_WORKERS`;");
                $s_id=0+$db->result($r,0,"mid")+1;
                $db->query("INSERT INTO `T_POINT_WORKERS` (`id`,`tpoint_id`,`status`) VALUES ('$s_id','$tpoint_id','1');");
                $dbt->query("INSERT INTO `T_POINT_WORKERS` (`id`,`tpoint_id`,`status`) VALUES ('$s_id','$tpoint_id','1');");
            }
            if  ($s_id>0) {
                $db->query("UPDATE `T_POINT_WORKERS` SET `media_user_id`='$worker_id' WHERE `id`='$s_id' AND `tpoint_id`='$tpoint_id';");
                $dbt->query("UPDATE `T_POINT_WORKERS` SET `media_user_id`='$worker_id' WHERE `id`='$s_id' AND `tpoint_id`='$tpoint_id';");
                $answer=1; $err="";
            }
        } else {
            $answer=0;
        }
        return array($answer, $err);
    }

    function dropTpointWorkers($tpoint_id, $s_id) { $db = DbSingleton::getTokoDb(); $dbt = DbSingleton::getTokoDb();
        $slave=new slave;
        $answer=0; $err="������� ���������� �����!";
        $tpoint_id=$slave->qq($tpoint_id);$s_id=$slave->qq($s_id);
        if ($tpoint_id>0 && $s_id>0) {
            $db->query("UPDATE `T_POINT_WORKERS` SET `status`='0' WHERE `id`='$s_id' AND `tpoint_id`='$tpoint_id';");
            $dbt->query("UPDATE `T_POINT_WORKERS` SET `status`='0' WHERE `id`='$s_id' AND `tpoint_id`='$tpoint_id';");
            $answer=1; $err="";
        }
        return array($answer, $err);
    }

    function showTpointClientList($sel_id) { $db = DbSingleton::getDb();
        $slave=new slave;
        $form="";$form_htm=RD."/tpl/clients_parrent_tree.htm";
        if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $list="";
        $r=$db->query("SELECT c.*, ot.name as org_type_name, t2cn.COUNTRY_NAME, t2st.STATE_NAME, t2rg.REGION_NAME, t2ct.CITY_NAME  
        FROM `A_CLIENTS` c 
            LEFT OUTER JOIN A_ORG_TYPE ot on ot.id=c.org_type 
            LEFT OUTER JOIN T2_COUNTRIES t2cn on t2cn.COUNTRY_ID=c.country 
            LEFT OUTER JOIN T2_STATE t2st on t2st.STATE_ID=c.state
            LEFT OUTER JOIN T2_REGION t2rg on t2rg.REGION_ID=c.region
            LEFT OUTER JOIN T2_CITY t2ct on t2ct.CITY_ID=c.city
            LEFT OUTER JOIN A_CLIENTS_CATEGORY cc on cc.client_id=c.id
            LEFT OUTER JOIN A_CATEGORY ac on ac.id=cc.category_id
        WHERE c.status=1 AND ac.id=3;");
        $n=$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"name");
            $org_type_name=$db->result($r,$i-1,"org_type_name");
            $email=$db->result($r,$i-1,"email");
            $phone=$db->result($r,$i-1,"phone");
            $country=$slave->showTableFieldDBT("T2_COUNTRIES","NAME","ID",$db->result($r,$i-1,"country"));
            $state=$slave->showTableFieldDBT("T2_STATE","NAME","ID",$db->result($r,$i-1,"state"));
            $region=$slave->showTableFieldDBT("T2_REGION","NAME","ID",$db->result($r,$i-1,"region"));
            $city=$slave->showTableFieldDBT("T2_CITY","NAME","ID",$db->result($r,$i-1,"city"));
            $cur="";$fn="<i class='fa fa-thumb-tack' onClick='setTpointClient(\"$id\", \"$name\")'></i>";
            if ($id==$sel_id){$fn="";$cur="style='background-color:#ccc; disabled:disabled;'";}
            $list.="<tr $cur>
                <td>$fn</td>
                <td>$id</td>
                <td>$org_type_name</td>
                <td>$name</td>
                <td>$country</td>
                <td>$state</td>
                <td>$region</td>
                <td>$city</td>
                <td>$email</td>
                <td>$phone</td>
            </tr>";
        }
        $form=str_replace("{list}",$list,$form);
        return $form;
    }

    function showTpointSupplSelectList($sel_id) { $db = DbSingleton::getDb();
        $list="";
        $r=$db->query("SELECT c.* FROM `A_CLIENTS` c 
            LEFT OUTER JOIN `A_CLIENTS_CATEGORY` cc on cc.client_id=c.id 
        WHERE c.status='1' AND cc.category_id='2';");
        $n=$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"name");
            $sel="";if ($id==$sel_id){$sel=" selected";}
            $list.="<option value='$id' $sel>$name</option>";
        }
        return $list;
    }

    function showTpointSupplStorageSelectList($suppl_id, $sel_id) { $db = DbSingleton::getDb();
        $list="";
        $r=$db->query("SELECT * FROM `A_CLIENTS_STORAGE` WHERE `status`='1' AND `client_id`='$suppl_id' ORDER BY `name`, `id` ASC;");
        $n=$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"name");
            $sel="";if ($id==$sel_id){$sel=" selected";}
            $list.="<option value='$id' $sel>$name</option>";
        }
        return $list;
    }

    function showTpointStorageSelectList($sel_id) { $db = DbSingleton::getTokoDb();
        $list="";
        $r=$db->query("SELECT * FROM `STORAGE` WHERE `status`='1' ORDER BY `name`, `id` ASC;");
        $n=$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"name");
            $sel="";if ($id==$sel_id){$sel=" selected";}
            $list.="<option value='$id' $sel>$name</option>";
        }
        return $list;
    }

    function getTpointNameById($sel_id, $field="name"){ $db = DbSingleton::getTokoDb();
        $name="";
        $r=$db->query("SELECT `$field` FROM `A_CLIENTS` WHERE `id`='$sel_id' LIMIT 1;");
        $n=$db->num_rows($r);
        if ($n==1) {
            $name=$db->result($r,0,"$field");
        }
        return $name;
    }

    function loadStateSelectList($country_id, $sel_id) {
        $slave=new slave;
        return $slave->showSelectSubList("T2_STATE","COUNTRY_ID","$country_id","STATE_ID","STATE_NAME",$sel_id);
    }

    function loadRegionSelectList($state_id, $sel_id) {
        $slave=new slave;
        return $slave->showSelectSubList("T2_REGION","STATE_ID","$state_id","REGION_ID","REGION_NAME",$sel_id);
    }

    function loadCitySelectList($region_id, $sel_id) {
        $slave=new slave;
        return "<option value='NEW'>�������� ��������� �����</option>".$slave->showSelectSubList("T2_CITY","REGION_ID","$region_id","CITY_ID","CITY_NAME",$sel_id);
    }

    function getAClientName($client_id) { $db = DbSingleton::getDb();
        $name="";
        $r=$db->query("SELECT `name` FROM `A_CLIENTS` WHERE `id`='$client_id' LIMIT 1;");
        $n=$db->num_rows($r);
        if ($n==1) {
            $name=$db->result($r,0,"name");
        }
        return $name;
    }

    function loadTpointDeliveryTime($tpoint_id) { $db = DbSingleton::getTokoDb();
        $slave=new slave;
        $list="";
        $form="";$form_htm=RD."/tpl/tpoint_delivery_time_list.htm";
        if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("SELECT pd.*, s.name as storage_name 
        FROM `T_POINT_DELIVERY_TIME` pd 
            LEFT OUTER JOIN `STORAGE` s on s.id=pd.storage_id 
        WHERE pd.tpoint_id='$tpoint_id' AND pd.status='1' ORDER BY pd.storage_id ASC, pd.week_day ASC, pd.time_from ASC;");
        $n=$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id=$db->result($r,$i-1,"id");
            $storage_name=$db->result($r,$i-1,"storage_name");
            $week_day=$slave->get_weekday_name($db->result($r,$i-1,"week_day"));
            $time_from=substr($db->result($r,$i-1,"time_from"),0,-3);
            $time_to=substr($db->result($r,$i-1,"time_to"),0,-3);
            $delivery_days=$db->result($r,$i-1,"delivery_days");
            $time_from_del=substr($db->result($r,$i-1,"time_from_del"),0,-3);
            $time_to_del=substr($db->result($r,$i-1,"time_to_del"),0,-3);
            $week_day_short=$slave->get_weekday_abr($db->result($r,$i-1,"week_day"));
            $date_del=date('d.m', strtotime(' + '.$delivery_days.' days'));
            $giveout_client_info="$date_del ($week_day_short) � $time_from_del �� $time_to_del";
            $list.="<tr>
                <td>
                    <button class='btn btn-sm btn-default' onClick='showTpointDeliveryForm(\"$tpoint_id\",\"$id\");'><i class='fa fa-edit'></i></button>
                    <button class='btn btn-sm btn-default' onClick='dropTpointDelivery(\"$tpoint_id\",\"$id\");'><i class='fa fa-times'></i></button>
                </td>
                <td>$i</td>
                <td>$storage_name</td>
                <td>$week_day</td>
                <td>$time_from</td>
                <td>$time_to</td>
                <td>$delivery_days</td>
                <td>$time_from_del</td>
                <td>$time_to_del</td>
                <td>$giveout_client_info</td>
            </tr>";
        }
        if ($n==0) {
            $list="<tr><td align='center' colspan='9'><h3 class='text-center'>������ �������</h3></td></tr>";
        }
        $form=str_replace("{list_delivery}",$list,$form);
        $form=str_replace("{tpoint_id}",$tpoint_id,$form);
        return $form;
    }
    function showTpointDeliveryForm($tpoint_id, $s_id) { $db = DbSingleton::getTokoDb();
        $slave=new slave;
        session_start();
        $media_user_id=$_SESSION["media_user_id"];
        $form="";$form_htm=RD."/tpl/tpoint_delivery_form.htm";
        if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("SELECT * FROM `T_POINT_DELIVERY_TIME` WHERE `id`='$s_id' AND `tpoint_id`='$tpoint_id' LIMIT 1;");
        $storage_id=$db->result($r,0,"storage_id");
        $week_day=$db->result($r,0,"week_day");
        $time_from=substr($db->result($r,0,"time_from"),0,-3);
        $time_to=substr($db->result($r,0,"time_to"),0,-3);
        $giveout_time=$db->result($r,0,"giveout_time");
        $delivery_days=$db->result($r,0,"delivery_days");
        $time_from_del=substr($db->result($r,0,"time_from_del"),0,-3);
        $time_to_del=substr($db->result($r,0,"time_to_del"),0,-3);
        $form=str_replace("{tpoint_id}",$tpoint_id,$form);
        $form=str_replace("{s_id}",$s_id,$form);
        $form=str_replace("{storage_list}",$this->showTpointStorageSelectList($storage_id),$form);
        $form=str_replace("{week_day_list}",$slave->showWeekdaySelectList($week_day),$form);
        $form=str_replace("{time_from}",$time_from,$form);
        $form=str_replace("{time_to}",$time_to,$form);
        $form=str_replace("{delivery_days}",$delivery_days,$form);
        $form=str_replace("{giveout_time}",$giveout_time,$form);
        $form=str_replace("{time_from_del}",$time_from_del,$form);
        $form=str_replace("{time_to_del}",$time_to_del,$form);
        $form=str_replace("{workers_list}",$this->showWorkersSelectList($media_user_id),$form);
        return $form;
    }

    function saveTpointDeliveryForm($tpoint_id,$s_id,$storage_id,$week_day,$time_from,$time_to,$delivery_days,$giveout_time,$time_from_del,$time_to_del) { $db = DbSingleton::getDb(); $dbt = DbSingleton::getTokoDb();
        $slave=new slave;
        $answer=0;$err="������� ���������� �����!";
        $tpoint_id=$slave->qq($tpoint_id);$s_id=$slave->qq($s_id);$storage_id=$slave->qq($storage_id);$week_day=$slave->qq($week_day);$time_from=$slave->qq($time_from);$time_to=$slave->qq($time_to);$delivery_days=$slave->qq($delivery_days);$giveout_time=$slave->qq($giveout_time);
        if ($tpoint_id>0) {
            if ($s_id==0) {
                $r=$db->query("SELECT MAX(`id`) as mid FROM `T_POINT_DELIVERY_TIME`;");$s_id=0+$db->result($r,0,"mid")+1;
                $db->query("INSERT INTO `T_POINT_DELIVERY_TIME` (id,tpoint_id,status) VALUES ('$s_id','$tpoint_id','1');");
                $dbt->query("INSERT INTO `T_POINT_DELIVERY_TIME` (id,tpoint_id,status) VALUES ('$s_id','$tpoint_id','1');");
            }
            if ($s_id>0) {
                $db->query("UPDATE `T_POINT_DELIVERY_TIME` SET `storage_id`='$storage_id', week_day='$week_day', time_from='$time_from', time_to='$time_to', delivery_days='$delivery_days', giveout_time='$giveout_time', time_from_del='$time_from_del', time_to_del='$time_to_del' WHERE `id`='$s_id' AND `tpoint_id`='$tpoint_id';");
                $dbt->query("UPDATE `T_POINT_DELIVERY_TIME` SET `storage_id`='$storage_id', week_day='$week_day', time_from='$time_from', time_to='$time_to', delivery_days='$delivery_days', giveout_time='$giveout_time', time_from_del='$time_from_del', time_to_del='$time_to_del' WHERE `id`='$s_id' AND `tpoint_id`='$tpoint_id';");
                $answer=1; $err="";
            }
        } else {
            $answer=0;
        }
        return array($answer, $err);
    }

    function dropTpointDelivery($tpoint_id, $s_id) { $db = DbSingleton::getTokoDb(); $dbt = DbSingleton::getTokoDb();
        $slave=new slave;
        $answer=0;$err="������� ���������� �����!";
        $tpoint_id=$slave->qq($tpoint_id);$s_id=$slave->qq($s_id);
        if ($tpoint_id>0 && $s_id>0){
            $db->query("UPDATE `T_POINT_DELIVERY_TIME` SET `status`='0' WHERE `id`='$s_id' AND `tpoint_id`='$tpoint_id';");
            $dbt->query("UPDATE `T_POINT_DELIVERY_TIME` SET `status`='0' WHERE `id`='$s_id' AND `tpoint_id`='$tpoint_id';");
            $answer=1; $err="";
        }
        return array($answer, $err);
    }

    function loadTpointSupplDeliveryTime($tpoint_id) { $db = DbSingleton::getDb();
        $slave=new slave;
        $list="";
        $form=""; $form_htm=RD."/tpl/tpoint_suppl_delivery_time_list.htm";
        if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("SELECT pd.*, cs.name as storage_name, c.name as client_name 
        FROM `T_POINT_SUPPL_DELIVERY_TIME` pd 
            LEFT OUTER JOIN A_CLIENTS_STORAGE cs on cs.id=pd.suppl_storage_id
            LEFT OUTER JOIN A_CLIENTS c on c.id=cs.client_id
        WHERE pd.tpoint_id='$tpoint_id' and pd.status='1' ORDER BY pd.suppl_storage_id ASC, pd.week_day ASC, pd.time_from ASC;");
        $n=$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id=$db->result($r,$i-1,"id");
            $client_name=$db->result($r,$i-1,"client_name");
            $storage_name=$db->result($r,$i-1,"storage_name");
            $week_day=$slave->get_weekday_name($db->result($r,$i-1,"week_day"));
            $time_from=substr($db->result($r,$i-1,"time_from"),0,-3);
            $time_to=substr($db->result($r,$i-1,"time_to"),0,-3);
            $delivery_days=$db->result($r,$i-1,"delivery_days");
            // $dd_word="����� ".$delivery_days." ��. ";if ($delivery_days==0){$dd_word="��������";}if ($delivery_days==1){$dd_word="������";}
            $time_from_del=substr($db->result($r,$i-1,"time_from_del"),0,-3);
            $time_to_del=substr($db->result($r,$i-1,"time_to_del"),0,-3);
            $week_day_short=$slave->get_weekday_abr($db->result($r,$i-1,"week_day"));
            $date_del=date('d.m', strtotime(' + '.$delivery_days.' days'));
            $giveout_client_info="$date_del ($week_day_short)<br>� $time_from_del �� $time_to_del";
            $list.="<tr>
                <td>
                    <button class='btn btn-sm btn-default' onClick='showTpointSupplDeliveryForm(\"$tpoint_id\",\"$id\");'><i class='fa fa-edit'></i></button>
                    <button class='btn btn-sm btn-default' onClick='dropTpointSupplDelivery(\"$tpoint_id\",\"$id\");'><i class='fa fa-times'></i></button>
                </td>
                <td>$i</td>
                <td>$client_name</td>
                <td>$storage_name</td>
                <td>$week_day</td>
                <td>$time_from</td>
                <td>$time_to</td>
                <td>$delivery_days</td>
                <td>$time_from_del</td>
                <td>$time_to_del</td>
                <td>$giveout_client_info</td>
            </tr>";
        }
        if ($n==0) {
            $list="<tr><td align='center' colspan='9'><h3 class='text-center'>������ �������</h3></td></tr>";
        }
        $form=str_replace("{list_delivery}",$list,$form);
        $form=str_replace("{tpoint_id}",$tpoint_id,$form);
        return $form;
    }

    function loadTpointSupplStorage($tpoint_id) { $db = DbSingleton::getDb();
        $form="";$form_htm=RD."/tpl/tpoint_suppl_storage_list.htm";
        if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $list="";
        $r=$db->query("SELECT pss.*, st.name as storage_name, cc.name as suppl_name 
        FROM `T_POINT_SUPPL_STORAGE` pss 
            LEFT JOIN `A_CLIENTS_STORAGE` st on st.id=pss.storage_id 
            LEFT JOIN `A_CLIENTS` cc on cc.id=pss.suppl_id 
        WHERE pss.tpoint_id='$tpoint_id' ORDER BY pss.id ASC;");
        $n=$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id=$db->result($r,$i-1,"id");
            $suppl_name=$db->result($r,$i-1,"suppl_name");
            $storage_name=$db->result($r,$i-1,"storage_name");
            $list.="<tr>
                <td>
                    <button class='btn btn-sm btn-default' onClick='showTpointSupplStorageForm(\"$tpoint_id\",\"$id\");'><i class='fa fa-edit'></i></button>
                    <button class='btn btn-sm btn-default' onClick='dropTpointSupplStorageForm(\"$tpoint_id\",\"$id\");'><i class='fa fa-times'></i></button>
                </td>
                <td>$i</td>
                <td>$suppl_name</td>
                <td>$storage_name</td>
            </tr>";
        }
        if ($n==0) {
            $list="<tr><td align='center' colspan=5><h3 class='text-center'>������ �������</h3></td></tr>";
        }
        $form=str_replace("{list_storage}",$list,$form);
        $form=str_replace("{tpoint_id}",$tpoint_id,$form);
        return $form;
    }

    function showTpointSupplDeliveryForm($tpoint_id, $s_id) { $db = DbSingleton::getTokoDb();
        $slave=new slave;
        session_start();
        $media_user_id=$_SESSION["media_user_id"];
        $form=""; $form_htm=RD."/tpl/tpoint_suppl_delivery_form.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $r=$db->query("SELECT * FROM `T_POINT_SUPPL_DELIVERY_TIME` WHERE `id`='$s_id' AND `tpoint_id`='$tpoint_id' LIMIT 1;");
        $suppl_id=$db->result($r,0,"suppl_id");
        $suppl_storage_id=$db->result($r,0,"suppl_storage_id");
        $week_day=$db->result($r,0,"week_day");
        $time_from=substr($db->result($r,0,"time_from"),0,-3);
        $time_to=substr($db->result($r,0,"time_to"),0,-3);
        $delivery_days=$db->result($r,0,"delivery_days");
        $giveout_time=$db->result($r,0,"giveout_time");
        $time_from_del=substr($db->result($r,0,"time_from_del"),0,-3);
        $time_to_del=substr($db->result($r,0,"time_to_del"),0,-3);
        $form=str_replace("{tpoint_id}",$tpoint_id,$form);
        $form=str_replace("{s_id}",$s_id,$form);
        $form=str_replace("{suppl_list}",$this->showTpointSupplSelectList($suppl_id),$form);
        $form=str_replace("{suppl_storage_list}",$this->showTpointSupplStorageSelectList($suppl_id,$suppl_storage_id),$form);
        $form=str_replace("{week_day_list}",$slave->showWeekdaySelectList($week_day),$form);
        $form=str_replace("{time_from}",$time_from,$form);
        $form=str_replace("{time_to}",$time_to,$form);
        $form=str_replace("{delivery_days}",$delivery_days,$form);
        $form=str_replace("{giveout_time}",$giveout_time,$form);
        $form=str_replace("{time_from_del}",$time_from_del,$form);
        $form=str_replace("{time_to_del}",$time_to_del,$form);
        $form=str_replace("{workers_list}",$this->showWorkersSelectList($media_user_id),$form);
        return $form;
    }

    function saveTpointSupplDeliveryForm($tpoint_id,$s_id,$suppl_id,$suppl_storage_id,$week_day,$time_from,$time_to,$delivery_days,$giveout_time,$time_from_del,$time_to_del) { $db = DbSingleton::getDb(); $dbt = DbSingleton::getTokoDb();
        $slave=new slave;
        $answer=0; $err="������� ���������� �����!";
        $tpoint_id=$slave->qq($tpoint_id);$s_id=$slave->qq($s_id);$suppl_id=$slave->qq($suppl_id);$suppl_storage_id=$slave->qq($suppl_storage_id);$week_day=$slave->qq($week_day);$time_from=$slave->qq($time_from);$time_to=$slave->qq($time_to);$delivery_days=$slave->qq($delivery_days);$giveout_time=$slave->qq($giveout_time);
        if ($tpoint_id>0) {
            if ($s_id==0) {
                $r=$db->query("SELECT MAX(`id`) as mid FROM `T_POINT_SUPPL_DELIVERY_TIME`;");$s_id=0+$db->result($r,0,"mid")+1;
                $db->query("INSERT INTO `T_POINT_SUPPL_DELIVERY_TIME` (id,tpoint_id,status) VALUES ('$s_id','$tpoint_id','1');");
                $dbt->query("INSERT INTO `T_POINT_SUPPL_DELIVERY_TIME` (id,tpoint_id,status) VALUES ('$s_id','$tpoint_id','1');");
            }
            if  ($s_id>0) {
                $db->query("UPDATE `T_POINT_SUPPL_DELIVERY_TIME` SET `suppl_id`='$suppl_id', suppl_storage_id='$suppl_storage_id', week_day='$week_day', time_from='$time_from', time_to='$time_to', delivery_days='$delivery_days', giveout_time='$giveout_time', time_from_del='$time_from_del', time_to_del='$time_to_del' WHERE `id`='$s_id' AND `tpoint_id`='$tpoint_id';");
                $dbt->query("UPDATE `T_POINT_SUPPL_DELIVERY_TIME` SET `suppl_id`='$suppl_id', suppl_storage_id='$suppl_storage_id', week_day='$week_day', time_from='$time_from', time_to='$time_to', delivery_days='$delivery_days', giveout_time='$giveout_time', time_from_del='$time_from_del', time_to_del='$time_to_del' WHERE `id`='$s_id' AND `tpoint_id`='$tpoint_id';");
                $answer=1; $err="";
            }
        } else {
            $answer=0;
        }
        return array($answer, $err);
    }

    function dropTpointSupplDelivery($tpoint_id, $s_id) { $db = DbSingleton::getTokoDb(); $dbt = DbSingleton::getTokoDb();
        $slave=new slave;
        $answer=0; $err="������� ���������� �����!";
        $tpoint_id=$slave->qq($tpoint_id); $s_id=$slave->qq($s_id);
        if ($tpoint_id>0 && $s_id>0) {
            $db->query("UPDATE `T_POINT_SUPPL_DELIVERY_TIME` SET `status`='0' WHERE `id`='$s_id' and `tpoint_id`='$tpoint_id';");
            $dbt->query("UPDATE `T_POINT_SUPPL_DELIVERY_TIME` SET `status`='0' WHERE `id`='$s_id' and `tpoint_id`='$tpoint_id';");
            $answer=1; $err="";
        }
        return array($answer, $err);
    }

    function loadTpointSupplFm($tpoint_id) { $db = DbSingleton::getDb();
        $list="";
        $form=""; $form_htm=RD."/tpl/tpoint_suppl_fm_list.htm";
        if (file_exists("$form_htm")) { $form = file_get_contents($form_htm); }
        $r=$db->query("SELECT pd.*, cs.name as storage_name, c.name as client_name 
        FROM `T_POINT_SUPPL_FM` pd 
            LEFT OUTER JOIN A_CLIENTS_STORAGE cs on cs.id=pd.suppl_storage_id
            LEFT OUTER JOIN A_CLIENTS c on c.id=pd.suppl_id
        WHERE pd.tpoint_id='$tpoint_id' AND pd.status='1' ORDER BY pd.suppl_id ASC, pd.suppl_storage_id ASC, pd.price_rating_id ASC, pd.price_from ASC;");
        $n=$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id=$db->result($r,$i-1,"id");
            $client_name=$db->result($r,$i-1,"client_name");
            $storage_name=$db->result($r,$i-1,"storage_name");
            $price_rating_name=$this->showPriceRatingName($db->result($r,$i-1,"price_rating_id"));
            $price_from=$db->result($r,$i-1,"price_from");
            $price_to=$db->result($r,$i-1,"price_to");
            $margin=$db->result($r,$i-1,"margin");
            $delivery=$db->result($r,$i-1,"delivery");
            $margin2=$db->result($r,$i-1,"margin2");
            $list.="<tr>
                <td>
                    <button class='btn btn-sm btn-default' onClick='showTpointSupplFmForm(\"$tpoint_id\",\"$id\");'><i class='fa fa-edit'></i></button>
                    <button class='btn btn-sm btn-default' onClick='dropTpointSupplFm(\"$tpoint_id\",\"$id\");'><i class='fa fa-times'></i></button>
                </td>
                <td>$i</td>
                <td>$client_name</td>
                <td>$storage_name</td>
                <td>$price_rating_name</td>
                <td>$price_from</td>
                <td>$price_to</td>
                <td>$margin</td>
                <td>$delivery</td>
                <td>$margin2</td>
            </tr>";
        }
        if ($n==0) {
            $list="<tr><td align='center' colspan='9'><h3 class='text-center'>������ �������</h3></td></tr>";
        }
        $form=str_replace("{list_suppl_fm}",$list,$form);
        $form=str_replace("{tpoint_id}",$tpoint_id,$form);
        return $form;
    }

    function showTpointSupplFmForm($tpoint_id, $s_id) { $db = DbSingleton::getTokoDb();
        session_start();
        $media_user_id=$_SESSION["media_user_id"];
        $form="";$form_htm=RD."/tpl/tpoint_suppl_fm_form.htm";
        if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("SELECT * FROM `T_POINT_SUPPL_FM` WHERE `id`='$s_id' AND `tpoint_id`='$tpoint_id' LIMIT 1;");
        $suppl_id=$db->result($r,0,"suppl_id");
        $suppl_storage_id=$db->result($r,0,"suppl_storage_id");
        $price_rating_id=$db->result($r,0,"price_rating_id");
        $price_from=$db->result($r,0,"price_from");
        $price_to=$db->result($r,0,"price_to");
        $margin=$db->result($r,0,"margin");
        $delivery=$db->result($r,0,"delivery");
        $margin2=$db->result($r,0,"margin2");
        $form=str_replace("{tpoint_id}",$tpoint_id,$form);
        $form=str_replace("{s_id}",$s_id,$form);
        $form=str_replace("{suppl_list}",$this->showTpointSupplSelectList($suppl_id),$form);
        $form=str_replace("{suppl_storage_list}",$this->showTpointSupplStorageSelectList($suppl_id,$suppl_storage_id),$form);
        $form=str_replace("{price_rating_list}",$this->showPriceRatingSelectList($price_rating_id),$form);
        $form=str_replace("{price_from}",$price_from,$form);
        $form=str_replace("{price_to}",$price_to,$form);
        $form=str_replace("{margin}",$margin,$form);
        $form=str_replace("{delivery}",$delivery,$form);
        $form=str_replace("{margin2}",$margin2,$form);
        $form=str_replace("{workers_list}",$this->showWorkersSelectList($media_user_id),$form);
        return $form;
    }

    function saveTpointSupplFmForm($tpoint_id, $s_id, $suppl_id, $suppl_storage_id, $price_rating_id, $price_from, $price_to, $margin, $delivery, $margin2) { $db = DbSingleton::getDb(); $dbt = DbSingleton::getTokoDb();
        $slave=new slave;
        $answer=0; $err="������� ���������� �����!";
        $tpoint_id=$slave->qq($tpoint_id);$s_id=$slave->qq($s_id);$suppl_id=$slave->qq($suppl_id);$suppl_storage_id=$slave->qq($suppl_storage_id);$price_rating_id=$slave->qq($price_rating_id);$price_from=$slave->qq($price_from);$price_to=$slave->qq($price_to);$margin=$slave->qq($margin);$delivery=$slave->qq($delivery);$margin2=$slave->qq($margin2);
        if ($tpoint_id>0) {
            if ($s_id==0) {
                $r=$db->query("SELECT MAX(`id`) as mid FROM `T_POINT_SUPPL_FM`;");$s_id=0+$db->result($r,0,"mid")+1;
                $db->query("INSERT INTO `T_POINT_SUPPL_FM` (id,tpoint_id,status) VALUES ('$s_id','$tpoint_id','1');");
                $dbt->query("INSERT INTO `T_POINT_SUPPL_FM` (id,tpoint_id,status) VALUES ('$s_id','$tpoint_id','1');");
            }
            if  ($s_id>0) {
                $db->query("UPDATE `T_POINT_SUPPL_FM` SET `suppl_id`='$suppl_id', `suppl_storage_id`='$suppl_storage_id', price_rating_id='$price_rating_id', price_from='$price_from', price_to='$price_to', margin='$margin', delivery='$delivery', margin2='$margin2' WHERE `id`='$s_id' AND `tpoint_id`='$tpoint_id';");
                $dbt->query("UPDATE `T_POINT_SUPPL_FM` SET `suppl_id`='$suppl_id', `suppl_storage_id`='$suppl_storage_id', price_rating_id='$price_rating_id', price_from='$price_from', price_to='$price_to', margin='$margin', delivery='$delivery', margin2='$margin2' WHERE `id`='$s_id' AND `tpoint_id`='$tpoint_id';");
                $answer=1; $err="";
            }
        } else {
            $answer=0;
        }
        return array($answer, $err);
    }

    function dropTpointSupplFm($tpoint_id, $s_id) { $db = DbSingleton::getTokoDb(); $dbt = DbSingleton::getTokoDb();
        $slave=new slave;
        $answer=0; $err="������� ���������� �����!";
        $tpoint_id=$slave->qq($tpoint_id);$s_id=$slave->qq($s_id);
        if ($tpoint_id>0 && $s_id>0) {
            $db->query("UPDATE `T_POINT_SUPPL_FM` SET `status`='0' WHERE `id`='$s_id' AND `tpoint_id`='$tpoint_id';");
            $dbt->query("UPDATE `T_POINT_SUPPL_FM` SET `status`='0' WHERE `id`='$s_id' AND `tpoint_id`='$tpoint_id';");
            $answer=1; $err="";
        }
        return array($answer, $err);
    }

    function getCashName($cash_id) { $db = DbSingleton::getDb();
        $name="";
        $r=$db->query("SELECT `name` FROM `CASH` WHERE `id`='$cash_id' LIMIT 1;");
        $n=$db->num_rows($r);
        if ($n==1) {
            $name=$db->result($r,0,"name");
        }
        return $name;
    }

    function showCashListSelect($sel_id, $ns) { $db = DbSingleton::getDb();
        if ($ns==""){$ns=1;}
        $list="";
        $r=$db->query("SELECT * FROM `CASH` ORDER BY `name` ASC;");
        $n=$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"abr");
            if ($ns==2) {
                $name=$db->result($r,$i-1,"name");
            }
            $sel="";
            if ($sel_id==$id){$sel="selected='selected'";}
            $list.="<option value='$id' $sel>$name</option>";
        }
        return $list;
    }

    function loadTpointPayBox($tpoint_id) { $db = DbSingleton::getDb();
        $form="";$form_htm=RD."/tpl/tpoint_pay_box_list.htm";
        if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $list="";
        $r=$db->query("SELECT * FROM `T_POINT_PAY_BOX` WHERE `tpoint_id`='$tpoint_id' AND `status`='1' ORDER BY `id` ASC;");
        $n=$db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id=$db->result($r,$i-1,"id");
            $client_id=$db->result($r,$i-1,"client_id");
            $client_name=$this->getAClientName($client_id);
            $name=$db->result($r,$i-1,"name");
            $in_use=$db->result($r,$i-1,"in_use"); $in_use_cap="-";if ($in_use==1){$in_use_cap="<i class='fa fa-eye'></i>";}
            $list.="<tr>
                <td>
                    <button class='btn btn-sm btn-default' onClick='showTpointPayBoxForm(\"$tpoint_id\",\"$id\");'><i class='fa fa-edit'></i></button>
                    <button class='btn btn-sm btn-default' onClick='dropTpointPayBox(\"$tpoint_id\",\"$id\");'><i class='fa fa-times'></i></button>
                </td>
                <td>$i</td>
                <td>$client_name</td>
                <td>$name</td>
                <td>$in_use_cap</td>
            </tr>";
        }
        if ($n==0) {
            $list="<tr><td align='center' colspan=6><h3 class='text-center'>������ �������</h3></td></tr>";
        }
        $form=str_replace("{list_pay_box}",$list,$form);
        $form=str_replace("{tpoint_id}",$tpoint_id,$form);
        return $form;
    }

    function showTpointPayBoxForm($tpoint_id, $s_id) { $db = DbSingleton::getDb();
        $form="";$form_htm=RD."/tpl/tpoint_pay_box_form.htm";
        if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("SELECT * FROM `T_POINT_PAY_BOX` WHERE `id`='$s_id' AND `tpoint_id`='$tpoint_id' LIMIT 1;");
        $client_id=$db->result($r,0,"client_id");
        $client_name=$this->getAClientName($client_id);
        $name=$db->result($r,0,"name");
        $in_use=$db->result($r,0,"in_use");
        $inuse_checked="";
        if ($in_use==1){$inuse_checked=" checked";}
        $form=str_replace("{tpoint_id}",$tpoint_id,$form);
        $form=str_replace("{s_id}",$s_id,$form);
        $form=str_replace("{client_id}",$client_id,$form);
        $form=str_replace("{client_name}",$client_name,$form);
        $form=str_replace("{name}",$name,$form);
        $form=str_replace("{inuse_checked}",$inuse_checked,$form);
        return $form;
    }

    function saveTpointPayBoxForm($tpoint_id, $s_id, $client_id, $name, $in_use) { $db = DbSingleton::getDb();
        $slave = new slave;
        $answer = 0; $err = "������� ���������� �����!";
        $tpoint_id=$slave->qq($tpoint_id); $s_id=$slave->qq($s_id); $client_id=$slave->qq($client_id); $name=$slave->qq($name); $in_use=$slave->qq($in_use);
        if ($tpoint_id > 0) {
            if ($s_id == 0) {
                $r = $db->query("SELECT MAX(`id`) as mid FROM `T_POINT_PAY_BOX`;");
                $s_id = 0 + $db->result($r,0,"mid") + 1;
                $db->query("INSERT INTO `T_POINT_PAY_BOX` (`id`,`tpoint_id`,`status`) VALUES ('$s_id','$tpoint_id','1');");
            }
            if ($s_id > 0) {
                $db->query("UPDATE `T_POINT_PAY_BOX` SET `client_id`='$client_id', `name`='$name', `in_use`='$in_use' WHERE `id`='$s_id' AND `tpoint_id`='$tpoint_id';");
                $answer = 1; $err = "";
            }
        } else {
            $answer = 0;
        }
        return array($answer, $err);
    }

    function dropTpointPayBox($tpoint_id, $s_id) { $db = DbSingleton::getDb();
        $slave=new slave;
        $answer=0; $err="������� ���������� �����!";
        $tpoint_id=$slave->qq($tpoint_id);$s_id=$slave->qq($s_id);
        if ($tpoint_id>0 && $s_id>0) {
            $db->query("UPDATE `T_POINT_PAY_BOX` SET `status`='0' WHERE `id`='$s_id' AND `tpoint_id`='$tpoint_id';");
            $answer=1; $err="";
        }
        return array($answer, $err);
    }

}
