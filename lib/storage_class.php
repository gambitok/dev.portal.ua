<?php

class storage {

    function newStorageCard(){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();session_start();$user_id=$_SESSION["media_user_id"];
        $r=$db->query("select max(id) as mid from STORAGE;");$storage_id=0+$db->result($r,0,"mid")+1;
        $dbt->query("insert into STORAGE (`id`,`user_id`) values ('$storage_id','$user_id');");
        $db->query("insert into STORAGE (`id`,`user_id`) values ('$storage_id','$user_id');");
        return $storage_id;
    }

    function show_storage_list(){$db=DbSingleton::getDb();$where="";
        $r=$db->query("select s.*, t2cn.COUNTRY_NAME, t2st.STATE_NAME, t2rg.REGION_NAME, t2ct.CITY_NAME 
        from STORAGE s 
            left outer join T2_COUNTRIES t2cn on t2cn.COUNTRY_ID=s.country 
            left outer join T2_STATE t2st on t2st.STATE_ID=s.state
            left outer join T2_REGION t2rg on t2rg.REGION_ID=s.region
            left outer join T2_CITY t2ct on t2ct.CITY_ID=s.city
        where s.status=1 $where;");$n=$db->num_rows($r);$list="";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"name");
            $full_name=$db->result($r,$i-1,"full_name");
            $country=$db->result($r,$i-1,"COUNTRY_NAME");
            $state=$db->result($r,$i-1,"STATE_NAME");
            $region=$db->result($r,$i-1,"REGION_NAME");
            $city=$db->result($r,$i-1,"CITY_NAME");
            $address=$db->result($r,$i-1,"address");
            $storekeeper=$db->result($r,$i-1,"storekeeper");
            $list.="<tr style='cursor:pointer' onClick='showStorageCard(\"$id\")'>
                <td>$id</td>
                <td>$name</td>
                <td>$full_name</td>
                <td>$country</td>
                <td>$state</td>
                <td>$region</td>
                <td>$city</td>
                <td>$address</td>
                <td>$storekeeper</td>
            </tr>";
        }
        return $list;
    }

    function deleteStorage($storage_id) {$db=DbSingleton::getDb();
        $answer=0;$err="Помилка збереження даних!";
        if($storage_id>0) {
            $db->query("update STORAGE set status=0 where id='$storage_id';");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function loadStorageUsers($storage_id) {$db=DbSingleton::getDb(); $list="";
        $tpoint_id=$this->getTpointFromStorage($storage_id);
        $r=$db->query("select u.* from media_users u 
            left outer join media_users_storage us on (us.user_id=u.id)
        where u.tpoint_id='$tpoint_id' and us.storage_id='$storage_id';");
        $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"name");
            $user="<a onclick='dropUserStorage($id,$storage_id)'>$name</a><br>";
            $list.=$user;
        }
        $list_tp="";
        $r=$db->query("select * from media_users where tpoint_id='$tpoint_id';"); $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"name");
            $storage_name=$this->getMediaStorage($id);
            $user="<a onclick='setUserStorage($id,$storage_id)'>$id. $name ($storage_name)</a><br>";
            $list_tp.=$user;
        }
        if ($list=="") $list="Пусто"; if ($list_tp=="") $list_tp="Пусто";
        $form="";$form_htm=RD."/tpl/storage_users_list.htm";if (file_exists("$form_htm")){$form = file_get_contents($form_htm);}
        $form=str_replace("{users_list}",$list,$form);
        $form=str_replace("{users_list_tp}",$list_tp,$form);
        return $form;
    }

    function getTpointFromStorage($storage_id) {$db=DbSingleton::getDb();
        $r=$db->query("select tpoint_id from T_POINT_STORAGE where storage_id='$storage_id' limit 1;");
        $tpoint_id=$db->result($r,0,"tpoint_id");
        return $tpoint_id;
    }

    function getMediaStorage($user_id) {$db=DbSingleton::getDb();$storages="";
        $r=$db->query("select * from media_users_storage where user_id='$user_id';"); $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $storage_id=$db->result($r,$i-1,"storage_id");
            $storage_name=$this->getStorageName($storage_id);
            $storages.=$storage_name." ";
        }
        return $storages;
    }

    function getStorageName($storage_id) {$db=DbSingleton::getDb();
        $r=$db->query("select name from STORAGE where id='$storage_id' limit 1;");
        $storage_name=$db->result($r,0,"name");
        if ($storage_name=="") $storage_name="-";
        return $storage_name;
    }

    function setUserStorage($user_id,$storage_id,$status) {$db=DbSingleton::getDb();
        if ($status=="0") {
            $db->query("delete from media_users_storage where user_id='$user_id' and storage_id='$storage_id';");
        }
        else {
            $r=$db->query("select * from media_users_storage where user_id='$user_id' and storage_id='$storage_id';"); $n=$db->num_rows($r);
            if($n>0) $db->query("update media_users_storage set storage_id='$storage_id' where user_id='$user_id' and storage_id='$storage_id';");
            else $db->query("insert into media_users_storage (user_id,storage_id) values ('$user_id','$storage_id');");
        }
        $answer=1;$err="";
        return array($answer,$err);
    }

    function showStorageCard($storage_id){$db=DbSingleton::getTokoDb();$slave=new slave;session_start();$user_id=$_SESSION["media_user_id"];$user_name=$_SESSION["user_name"];
        $form="";$form_htm=RD."/tpl/storage_card.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select s.* from STORAGE s where s.id='$storage_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==0){$form_htm=RD."/tpl/access_deny.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);} }
        if ($n==1){
            $storage_id=$db->result($r,0,"id");
            $name=$db->result($r,0,"name");
            $full_name=$db->result($r,0,"full_name");
            $storekeeper=$db->result($r,0,"storekeeper");
            $country=$db->result($r,0,"country");
            $state=$db->result($r,0,"state");
            $region=$db->result($r,0,"region");
            $city=$db->result($r,0,"city");
            $address=$db->result($r,0,"address");
            $order_by=$db->result($r,0,"order_by");
            $form=str_replace("{storage_id}",$storage_id,$form);
            $form=str_replace("{storage_name}",$name,$form);
            $form=str_replace("{storage_full_name}",$full_name,$form);
            $form=str_replace("{storekeeper}",$storekeeper,$form);
            $form=str_replace("{address}",$address,$form);$parrent_id=0;
            $form=str_replace("{parrent_name}",$this->getStorageNameById($parrent_id,"name"),$form);
            $form=str_replace("{country_list}",$slave->showSelectList("T2_COUNTRIES","COUNTRY_ID","COUNTRY_NAME",$country),$form);
            $form=str_replace("{state_list}",$slave->showSelectSubList("T2_STATE","COUNTRY_ID","$country","STATE_ID","STATE_NAME",$state),$form);
            $form=str_replace("{region_list}",$slave->showSelectSubList("T2_REGION","STATE_ID","$state","REGION_ID","REGION_NAME",$region),$form);
            $form=str_replace("{city_list}",$slave->showSelectSubList("T2_CITY","REGION_ID","$region","CITY_ID","CITY_NAME",$city),$form);
            $form=str_replace("{order_list}",$this->showOrderSelectList("storage_sort",$order_by),$form);
            $form=str_replace("{my_user_id}",$user_id,$form);
            $form=str_replace("{my_user_name}",$user_name,$form);
        }
        return $form;
    }

    function showOrderSelectList($gkey,$selId){$db=DbSingleton::getDb();$form="";
        $r=$db->query("select `id`,`mcaption` from `manual` where `key`='$gkey' order by mcaption,id asc;");$n=$db->num_rows($r);
        for ($i=0;$i<$n;$i++) {
            $form.="<option value='".$i."' ";if ($selId==$i){$form.=" selected='selected'";}
            $form.=">".$db->result($r,$i,"mcaption")."</option>";
        }
        return $form;
    }

    function saveStorageGeneralInfo($storage_id,$name,$full_name,$address,$storekeeper,$country_id,$state_id,$region_id,$city_id,$order_by){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;
        $answer=0;$err="Помилка збереження даних!";
        $storage_id=$slave->qq($storage_id);$name=$slave->qq($name);$full_name=$slave->qq($full_name);$address=$slave->qq($address);$storekeeper=$slave->qq($storekeeper);
        $country_id=$slave->qq($country_id);$state_id=$slave->qq($state_id);$city_id=$slave->qq($city_id);$region_id=$slave->qq($region_id);
        if ($storage_id>0){
            $db->query("update STORAGE set `name`='$name', `full_name`='$full_name', `address`='$address', `storekeeper`='$storekeeper', `country`='$country_id', `state`='$state_id', `city`='$city_id', `region`='$region_id', `order_by`='$order_by' where `id`='$storage_id';");
            $dbt->query("update STORAGE set `name`='$name', `full_name`='$full_name', `address`='$address', `storekeeper`='$storekeeper', `country`='$country_id', `state`='$state_id', `city`='$city_id', `region`='$region_id', `order_by`='$order_by' where `id`='$storage_id';");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function loadStorageDetails($storage_id){$db=DbSingleton::getTokoDb();
        $form="";$form_htm=RD."/tpl/storage_details_list.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select * from STORAGE_STR where storage_id='$storage_id' and status='1' order by param_id,id asc;");$n=$db->num_rows($r);$list="";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $param_id=$db->result($r,$i-1,"param_id");
            $param_name=$this->getStorageParamTypeName($param_id);
            $param_value=$db->result($r,$i-1,"param_value");
            $list.="<tr>
                <td>
                    <button class='btn btn-sm btn-default' onClick='showStorageDetailsForm(\"$storage_id\",\"$id\");'><i class='fa fa-edit'></i></button>
                    <button class='btn btn-sm btn-default' onClick='dropStorageDetails(\"$storage_id\",\"$id\");'><i class='fa fa-times'></i></button>
                </td>
                <td>$i</td>
                <td>$param_name</td>
                <td>$param_value</td>
            </tr>";
        }
        if ($n==0){$list="<tr><td align='center' colspan=6><h3 class='text-center'>Записи відсутні</h3></td></tr>";}
        $form=str_replace("{list_details}",$list,$form);
        $form=str_replace("{storage_id}",$storage_id,$form);
        return $form;
    }

    function showStorageDetailsForm($storage_id,$str_id){$db=DbSingleton::getTokoDb();
        $form="";$form_htm=RD."/tpl/storage_details_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select * from STORAGE_STR where id='$str_id' and storage_id='$storage_id' limit 0,1;");
        $str_id=$db->result($r,0,"id");
        $param_id=$db->result($r,0,"param_id");
        $form=str_replace("{storage_id}",$storage_id,$form);
        $form=str_replace("{storage_str_id}",$str_id,$form);
        $form=str_replace("{param_type_list}",$this->showStorageParamTypeSelectList($param_id),$form);
        return $form;
    }

    function getStorageParamData($param_id){$db=DbSingleton::getTokoDb();
        $r=$db->query("select * from STORAGE_PARAMS where id='$param_id' limit 0,1;");
        $name=$db->result($r,0,"param_name");
        $param_type=$db->result($r,0,"param_type");
        $field_key=$db->result($r,0,"field_key");
        $code_length=$db->result($r,0,"code_length");
        return array($name,$param_type,$field_key,$code_length);
    }

    function getStorageParamTypeName($param_id){$db=DbSingleton::getTokoDb();$name="";
        $r=$db->query("select param_name from STORAGE_PARAMS where id='$param_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$name=$db->result($r,0,"param_name");}
        return $name;
    }

    function showStorageParamTypeSelectList($sel_id){$db=DbSingleton::getTokoDb();$list="";;
        $r=$db->query("select * from STORAGE_PARAMS order by id asc;");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $param_id=$db->result($r,$i-1,"id");
            $param_name=$db->result($r,$i-1,"param_name");
            $param_type=$db->result($r,$i-1,"param_type");
            $code_length=$db->result($r,$i-1,"code_length");
            $sel="";if ($param_id==$sel_id){$sel=" selected";}
            $list.="<option value='$param_id' $sel>$param_name $param_type | $code_length(символи)</option>";
        }
        return $list;
    }

    function saveStorageDetailsForm($storage_id,$storage_str_id,$param_id){ $db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;
        $answer=0;$err="Помилка збереження даних!";
        $storage_id=$slave->qq($storage_id);$storage_str_id=$slave->qq($storage_str_id);$param_id=$slave->qq($param_id);
        if ($storage_id>0 && $param_id>0){
            $r=$db->query("select count(id) as kol from STORAGE_STR where storage_id='$storage_id' and param_id='$param_id' and id<>'$storage_str_id' and status='1';");$param_ex=$db->result($r,0,"kol");
            if ($param_ex>0){
                $answer=0;$err="Обраний Вами тип зберігання на складі вже присвоєно Вашому склдау!";
            }
            if ($param_ex==0){
                if ($storage_str_id==0 || $storage_str_id==""){
                    $r=$db->query("select max(id) as mid from STORAGE_STR;");$storage_str_id=0+$db->result($r,0,"mid")+1;
                    $db->query("insert into STORAGE_STR (id,storage_id,param_id) values ('$storage_str_id','$storage_id','$param_id');");
                    $dbt->query("insert into STORAGE_STR (id,storage_id,param_id) values ('$storage_str_id','$storage_id','$param_id');");
                }
                $db->query("update STORAGE_STR set param_id='$param_id' where id='$storage_str_id' and storage_id='$storage_id';");
                $dbt->query("update STORAGE_STR set param_id='$param_id' where id='$storage_str_id' and storage_id='$storage_id';");
                $answer=1;$err="";
            }
        }else{$answer=0;$err="Не обрано тип зберігання на складі!$storage_id,$storage_str_id,$param_id;";}
        return array($answer,$err);
    }

    function dropStorageDetails($storage_id,$storage_str_id){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;
        $answer=0;$err="Помилка збереження даних!";
        $storage_id=$slave->qq($storage_id);$storage_str_id=$slave->qq($storage_str_id);
        if ($storage_id>0 && $storage_str_id>0){
            $db->query("update STORAGE_STR set status='0' where id='$storage_str_id' and storage_id='$storage_id';");
            $dbt->query("update STORAGE_STR set status='0' where id='$storage_str_id' and storage_id='$storage_id';");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function loadStorageCells($storage_id){$db=DbSingleton::getTokoDb();
        $form="";$form_htm=RD."/tpl/storage_cells_list.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select * from STORAGE_CELLS where storage_id='$storage_id' and status='1' order by id asc;");$n=$db->num_rows($r);$list="";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $default=$db->result($r,$i-1,"default");$default_cap="-"; if ($default==1){$default_cap="По замовчуванню";}
            $cell_value=$db->result($r,$i-1,"cell_value");
            $list.="<tr>
                <td>
                    <button class='btn btn-sm btn-default' onClick='showStorageCellsForm(\"$storage_id\",\"$id\");'><i class='fa fa-edit'></i></button>
                    <button class='btn btn-sm btn-default' onClick='dropStorageCells(\"$storage_id\",\"$id\",\"$cell_value\");'><i class='fa fa-times'></i></button>
                </td>
                <td>$i</td>
                <td>$cell_value</td>
                <td>$default_cap</td>
            </tr>";
        }
        if ($n==0){$list="<tr><td align='center' colspan=6><h3 class='text-center'>Записи відсутні</h3></td></tr>";}
        $form=str_replace("{list_cells}",$list,$form);
        $form=str_replace("{storage_id}",$storage_id,$form);
        return $form;
    }

    function showStorageCellsForm($storage_id,$cells_id){$db=DbSingleton::getTokoDb();
        $form="";$form_htm=RD."/tpl/storage_cells_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select * from STORAGE_CELLS where id='$cells_id' and storage_id='$storage_id' limit 0,1;");
        //$str_arr=$db->result($r,0,"str_arr");
        $cell_value=$db->result($r,0,"cell_value");
        $default=$db->result($r,0,"default");
        $def_ch="";if ($default==1){$def_ch="checked";}
        list($str_arr_str,$str_arr,$str_kol)=$this->showStorageStrArr($storage_id,$cell_value);
        $form=str_replace("{storage_id}",$storage_id,$form);
        $form=str_replace("{cells_id}",$cells_id,$form);
        $form=str_replace("{str_arr_str}",$str_arr_str,$form);
        $form=str_replace("{str_arr}",$str_arr,$form);
        $form=str_replace("{str_kol}",$str_kol,$form);
        $form=str_replace("{def_ch}",$def_ch,$form);
        return $form;
    }

    function showStorageStrArr($storage_id,$cell_value){$db=DbSingleton::getTokoDb();$str_arr_str=$arr="";$cell_value=explode("|",$cell_value);
        $r=$db->query("select * from STORAGE_STR where storage_id='$storage_id' and status='1' order by param_id asc;");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $str_id=$db->result($r,$i-1,"id");
            $param_id=$db->result($r,$i-1,"param_id");
            list($param_name,$param_type,,$code_length)=$this->getStorageParamData($param_id);
            $str_arr_str.="<span class='btn btn-primary dim' disabled type='button' style='margin-right: 5px;'>$param_name</span>";
            $arr.="<div class='col-sm-2'>
                <input type='hidden' id='cell_str_id_$i' value='$str_id'>
                <input type='hidden' id='cell_param_id_$i' value='$param_id'>
                <input class='form-control' type='text' maxlength='$code_length' title='$param_type' id='cell_vl_$i' value='".$cell_value[$i-1]."' required>
            </div>";
        }
        $kol=$n;
        return array($str_arr_str,$arr,$kol);
    }

    function saveStorageCellsForm($storage_id,$cells_id,$str_kol,$cell_param_ids,$cell_vls,$def_ch){ $db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave; $answer=0;$err="Помилка збереження даних!";
        $storage_id=$slave->qq($storage_id);$cells_id=$slave->qq($cells_id);$str_kol=$slave->qq($str_kol);$cell_param_ids=$slave->qq($cell_param_ids);$cell_vls=$slave->qq($cell_vls);$def_ch=$slave->qq($def_ch);
        if ($storage_id>0){
            if ($cells_id==0 || $cells_id==""){
                $r=$db->query("select max(id) as mid from STORAGE_CELLS;");$cells_id=0+$db->result($r,0,"mid")+1;
                $db->query("insert into STORAGE_CELLS (`id`,`storage_id`,`status`) values ('$cells_id','$storage_id','0');");
                $dbt->query("insert into STORAGE_CELLS (`id`,`storage_id`,`status`) values ('$cells_id','$storage_id','0');");
            }
            if ($cells_id>0){$cell_value="";$str_arr="";
                for ($i=1;$i<=$str_kol;$i++){
                    $cell_value.=$cell_vls[$i];
                    $str_arr.=$cell_param_ids[$i];
                    if ($i<$str_kol){$cell_value.="|";$str_arr.="|";}
                }
                if ($def_ch==1){
                    $db->query("update STORAGE_CELLS set `default`='0' where storage_id='$storage_id';");
                    $dbt->query("update STORAGE_CELLS set `default`='0' where storage_id='$storage_id';");
                }
                $db->query("update STORAGE_CELLS set str_arr='$str_arr', `cell_value`='$cell_value', `status`='1', `default`='$def_ch' where id='$cells_id' and storage_id='$storage_id';");
                $dbt->query("update STORAGE_CELLS set str_arr='$str_arr', `cell_value`='$cell_value', `status`='1', `default`='$def_ch' where id='$cells_id' and storage_id='$storage_id';");
                $answer=1;$err="";
            }
        }
        else {
            $storage_str_id=$param_id=0;
            $answer=0;$err="Не обрано тип зберігання на складі!$storage_id,$storage_str_id,$param_id;";
        }
        return array($answer,$err);
    }

    function dropStorageCells($storage_id,$cells_id){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;$answer=0;$err="Помилка збереження даних!";
        $storage_id=$slave->qq($storage_id);$cells_id=$slave->qq($cells_id);
        if ($storage_id>0 && $cells_id>0){
            $db->query("update STORAGE_CELLS set status='0' where id='$cells_id' and storage_id='$storage_id';");
            $dbt->query("update STORAGE_CELLS set status='0' where id='$cells_id' and storage_id='$storage_id';");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function getStorageNameById($sel_id, $field="name"){$db=DbSingleton::getDb();$name="";
        $r=$db->query("select `$field` from A_CLIENTS where id='$sel_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$name=$db->result($r,0,"$field");}
        return $name;
    }

    function loadStateSelectList($country_id,$sel_id){$slave=new slave;
        $list=$slave->showSelectSubList("T2_STATE","COUNTRY_ID","$country_id","STATE_ID","STATE_NAME",$sel_id);
    //		$form=str_replace("{region_list}",$slave->showSelectSubList("T2_REGION","STATE_ID","$state","REGION_ID","REGION_NAME",$city),$form);
    //		$form=str_replace("{city_list}",$slave->showSelectSubList("T2_CITY","REGION_ID","$region","CITY_ID","CITY_NAME",$city),$form);
        /*$r=$db->query("select * from T2_STATE where COUNTRY_ID='$country_id' order by STATE_NAME asc;");$n=$db->num_rows($r);$list="";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"STATE_ID");
            $name=$db->result($r,$i-1,"STATE_NAME");
            $sel="";if ($sel_id==$id){$sel="selected='selected'";}
            $list.="<option value='$id' $sel>$name</option>";
        }*/
        return $list;
    }

    function loadRegionSelectList($state_id,$sel_id){$slave=new slave;
        /*
        $r=$db->query("select * from T2_REGION where STATE_ID='$state_id' order by REGION_NAME asc;");$n=$db->num_rows($r);$list="";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"REGION_ID");
            $name=$db->result($r,$i-1,"REGION_NAME");
            $sel="";if ($sel_id==$id){$sel="selected='selected'";}
            $list.="<option value='$id' $sel>$name</option>";
        }*/
        return $slave->showSelectSubList("T2_REGION","STATE_ID","$state_id","REGION_ID","REGION_NAME",$sel_id);
    }

    function loadCitySelectList($region_id,$sel_id){$slave=new slave;//$list="";
    /*	$r=$db->query("select * from T2_CITY where REGION_ID='$region_id' order by CITY_NAME asc;");$n=$db->num_rows($r);$list="";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"CITY_ID");
            $name=$db->result($r,$i-1,"CITY_NAME");
            $sel="";if ($sel_id==$id){$sel="selected='selected'";}
            $list.="<option value='$id' $sel>$name</option>";
        }
        */
        return "<option value='NEW'>Добавити населений пункт</option>".$slave->showSelectSubList("T2_CITY","REGION_ID","$region_id","CITY_ID","CITY_NAME",$sel_id);
    }

}
