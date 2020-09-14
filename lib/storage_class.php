<?php

class storage {

    function getStorageName($storage_id) {$db=DbSingleton::getDb();
        $r=$db->query("SELECT `name` FROM `STORAGE` WHERE `status`='1' AND `id`='$storage_id' LIMIT 1;");$n=$db->num_rows($r);
        if ($n==1){$storage_name=$db->result($r,0,"name");} else {$storage_name="-";}
        return $storage_name;
    }

    function getStorageCellsName($cell_id,$storage_id) { $db=DbSingleton::getTokoDb();
        $r=$db->query("SELECT `cell_value` FROM `STORAGE_CELLS` WHERE `status`='1' AND `id`='$cell_id' AND `storage_id`='$storage_id' LIMIT 1;");
        $n=$db->num_rows($r); $name="";
        if ($n==1){$name=$db->result($r,0,"cell_value");}
        return $name;
    }

    function newStorageCard(){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();
        session_start();$user_id=$_SESSION["media_user_id"];
        $r=$db->query("SELECT MAX(`id`) as mid FROM `STORAGE`;");$storage_id=0+$db->result($r,0,"mid")+1;
        $dbt->query("INSERT INTO `STORAGE` (`id`,`user_id`) VALUES ('$storage_id','$user_id');");
        $db->query("INSERT INTO `STORAGE` (`id`,`user_id`) VALUES ('$storage_id','$user_id');");
        return $storage_id;
    }

    function show_storage_list(){$db=DbSingleton::getDb();
        $r=$db->query("SELECT s.*, t2cn.COUNTRY_NAME, t2st.STATE_NAME, t2rg.REGION_NAME, t2ct.CITY_NAME 
        FROM `STORAGE` s 
            LEFT OUTER JOIN `T2_COUNTRIES` t2cn on t2cn.COUNTRY_ID=s.country 
            LEFT OUTER JOIN `T2_STATE` t2st on t2st.STATE_ID=s.state
            LEFT OUTER JOIN `T2_REGION` t2rg on t2rg.REGION_ID=s.region
            LEFT OUTER JOIN `T2_CITY` t2ct on t2ct.CITY_ID=s.city
        WHERE s.status=1;");$n=$db->num_rows($r);$list="";
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
            $db->query("UPDATE `STORAGE` SET `status`=0 WHERE `id`='$storage_id';");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function loadStorageUsers($storage_id) {$db=DbSingleton::getDb();
        $tpoint_id=$this->getTpointFromStorage($storage_id);
        $r=$db->query("SELECT u.* FROM `media_users` u 
            LEFT OUTER JOIN `media_users_storage` us on (us.user_id=u.id)
        WHERE u.tpoint_id='$tpoint_id' AND us.storage_id='$storage_id';"); $n=$db->num_rows($r); $list="";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"name");
            $user="<a onclick='dropUserStorage($id,$storage_id)'>$name</a><br>";
            $list.=$user;
        }
        $list_tp="";
        $r=$db->query("SELECT * FROM `media_users` WHERE `tpoint_id`='$tpoint_id';"); $n=$db->num_rows($r);
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
        $r=$db->query("SELECT `tpoint_id` FROM `T_POINT_STORAGE` WHERE `storage_id`='$storage_id' LIMIT 1;");
        $tpoint_id=$db->result($r,0,"tpoint_id");
        return $tpoint_id;
    }

    function getMediaStorage($user_id) {$db=DbSingleton::getDb();
        $r=$db->query("SELECT * FROM `media_users_storage` WHERE `user_id`='$user_id';"); $n=$db->num_rows($r); $storages="";
        for ($i=1;$i<=$n;$i++){
            $storage_id=$db->result($r,$i-1,"storage_id");
            $storage_name=$this->getStorageName($storage_id);
            $storages.=$storage_name." ";
        }
        return $storages;
    }

    function setUserStorage($user_id,$storage_id,$status) {$db=DbSingleton::getDb();
        if ($status=="0") {
            $db->query("DELETE FROM `media_users_storage` WHERE `user_id`='$user_id' AND `storage_id`='$storage_id';");
        } else {
            $r=$db->query("SELECT * FROM `media_users_storage` WHERE `user_id`='$user_id' AND `storage_id`='$storage_id';"); $n=$db->num_rows($r);
            if ($n>0) $db->query("UPDATE `media_users_storage` SET `storage_id`='$storage_id' WHERE `user_id`='$user_id' AND `storage_id`='$storage_id';");
            else $db->query("INSERT INTO `media_users_storage` (`user_id`,`storage_id`) VALUES ('$user_id','$storage_id');");
        }
        $answer=1;$err="";
        return array($answer,$err);
    }

    function showStorageCard($storage_id){$db=DbSingleton::getTokoDb();
        $slave=new slave;session_start();$user_id=$_SESSION["media_user_id"];$user_name=$_SESSION["user_name"];
        $form="";$form_htm=RD."/tpl/storage_card.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("SELECT * FROM `STORAGE` WHERE `id`='$storage_id' LIMIT 1;");$n=$db->num_rows($r);
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

    function showOrderSelectList($gkey,$selId){$db=DbSingleton::getDb();
        $r=$db->query("SELECT `id`, `mcaption` FROM `manual` WHERE `key`='$gkey' ORDER BY `mcaption`, `id` ASC;");$n=$db->num_rows($r);$form="";
        for ($i=0;$i<$n;$i++) {
            $form.="<option value='".$i."' ";if ($selId==$i){$form.=" selected='selected'";}
            $form.=">".$db->result($r,$i,"mcaption")."</option>";
        }
        return $form;
    }

    function saveStorageGeneralInfo($storage_id,$name,$full_name,$address,$storekeeper,$country_id,$state_id,$region_id,$city_id,$order_by){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();
        $slave=new slave;$answer=0;$err="Помилка збереження даних!";
        $storage_id=$slave->qq($storage_id);$name=$slave->qq($name);$full_name=$slave->qq($full_name);$address=$slave->qq($address);$storekeeper=$slave->qq($storekeeper);
        $country_id=$slave->qq($country_id);$state_id=$slave->qq($state_id);$city_id=$slave->qq($city_id);$region_id=$slave->qq($region_id);
        if ($storage_id>0){
            $db->query("UPDATE `STORAGE` SET `name`='$name', `full_name`='$full_name', `address`='$address', `storekeeper`='$storekeeper', `country`='$country_id', `state`='$state_id', `city`='$city_id', `region`='$region_id', `order_by`='$order_by' WHERE `id`='$storage_id';");
            $dbt->query("UPDATE `STORAGE` SET `name`='$name', `full_name`='$full_name', `address`='$address', `storekeeper`='$storekeeper', `country`='$country_id', `state`='$state_id', `city`='$city_id', `region`='$region_id', `order_by`='$order_by' WHERE `id`='$storage_id';");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function loadStorageDetails($storage_id){$db=DbSingleton::getTokoDb();
        $form="";$form_htm=RD."/tpl/storage_details_list.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("SELECT * FROM `STORAGE_STR` WHERE `storage_id`='$storage_id' AND `status`='1' ORDER BY `param_id`, `id` ASC;");$n=$db->num_rows($r);$list="";
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
        $r=$db->query("SELECT * FROM `STORAGE_STR` WHERE `id`='$str_id' AND `storage_id`='$storage_id' LIMIT 1;");
        $str_id=$db->result($r,0,"id");
        $param_id=$db->result($r,0,"param_id");
        $form=str_replace("{storage_id}",$storage_id,$form);
        $form=str_replace("{storage_str_id}",$str_id,$form);
        $form=str_replace("{param_type_list}",$this->showStorageParamTypeSelectList($param_id),$form);
        return $form;
    }

    function getStorageParamData($param_id){$db=DbSingleton::getTokoDb();
        $r=$db->query("SELECT * FROM `STORAGE_PARAMS` WHERE `id`='$param_id' LIMIT 1;");
        $name=$db->result($r,0,"param_name");
        $param_type=$db->result($r,0,"param_type");
        $field_key=$db->result($r,0,"field_key");
        $code_length=$db->result($r,0,"code_length");
        return array($name,$param_type,$field_key,$code_length);
    }

    function getStorageParamTypeName($param_id){$db=DbSingleton::getTokoDb();
        $r=$db->query("SELECT `param_name` FROM `STORAGE_PARAMS` WHERE `id`='$param_id' LIMIT 1;");$n=$db->num_rows($r);$name="";
        if ($n==1){$name=$db->result($r,0,"param_name");}
        return $name;
    }

    function showStorageParamTypeSelectList($sel_id){$db=DbSingleton::getTokoDb();
        $r=$db->query("SELECT * FROM `STORAGE_PARAMS` ORDER BY `id` ASC;");$n=$db->num_rows($r);$list="";
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

    function saveStorageDetailsForm($storage_id,$storage_str_id,$param_id){ $db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();
        $slave=new slave;$answer=0;$err="Помилка збереження даних!";
        $storage_id=$slave->qq($storage_id);$storage_str_id=$slave->qq($storage_str_id);$param_id=$slave->qq($param_id);
        if ($storage_id>0 && $param_id>0){
            $r=$db->query("SELECT COUNT(`id`) as kol FROM `STORAGE_STR` WHERE `storage_id`='$storage_id' AND `param_id`='$param_id' AND `id`<>'$storage_str_id' AND `status`='1';");
            $param_ex=$db->result($r,0,"kol");
            if ($param_ex>0){
                $answer=0;$err="Обраний Вами тип зберігання на складі вже присвоєно Вашому склдау!";
            }
            if ($param_ex==0){
                if ($storage_str_id==0 || $storage_str_id==""){
                    $r=$db->query("SELECT MAX(`id`) as mid FROM `STORAGE_STR`;");$storage_str_id=0+$db->result($r,0,"mid")+1;
                    $db->query("INSERT INTO `STORAGE_STR` (`id`,`storage_id`,`param_id`) VALUES ('$storage_str_id','$storage_id','$param_id');");
                    $dbt->query("INSERT INTO `STORAGE_STR` (`id`,`storage_id`,`param_id`) VALUES ('$storage_str_id','$storage_id','$param_id');");
                }
                $db->query("UPDATE `STORAGE_STR` SET `param_id`='$param_id' WHERE `id`='$storage_str_id' AND `storage_id`='$storage_id';");
                $dbt->query("UPDATE `STORAGE_STR` SET `param_id`='$param_id' WHERE `id`='$storage_str_id' AND `storage_id`='$storage_id';");
                $answer=1;$err="";
            }
        }else{$answer=0;$err="Не обрано тип зберігання на складі!$storage_id,$storage_str_id,$param_id;";}
        return array($answer,$err);
    }

    function dropStorageDetails($storage_id,$storage_str_id){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();
        $slave=new slave;$answer=0;$err="Помилка збереження даних!";
        $storage_id=$slave->qq($storage_id);$storage_str_id=$slave->qq($storage_str_id);
        if ($storage_id>0 && $storage_str_id>0){
            $db->query("UPDATE `STORAGE_STR` SET `status`='0' WHERE `id`='$storage_str_id' AND `storage_id`='$storage_id';");
            $dbt->query("UPDATE `STORAGE_STR` SET `status`='0' WHERE `id`='$storage_str_id' AND `storage_id`='$storage_id';");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function loadStorageCells($storage_id){$db=DbSingleton::getTokoDb();
        $form="";$form_htm=RD."/tpl/storage_cells_list.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("SELECT * FROM `STORAGE_CELLS` WHERE `storage_id`='$storage_id' AND `status`='1' ORDER BY `id` ASC;");$n=$db->num_rows($r);$list="";
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
        $r=$db->query("SELECT * FROM `STORAGE_CELLS` WHERE `id`='$cells_id' AND `storage_id`='$storage_id' LIMIT 1;");
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

    function showStorageStrArr($storage_id,$cell_value){$db=DbSingleton::getTokoDb();
        $str_arr_str=$arr="";$cell_value=explode("|",$cell_value);
        $r=$db->query("SELECT * FROM `STORAGE_STR` WHERE `storage_id`='$storage_id' AND `status`='1' ORDER BY `param_id` ASC;");$n=$db->num_rows($r);
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

    function saveStorageCellsForm($storage_id,$cells_id,$str_kol,$cell_param_ids,$cell_vls,$def_ch){ $db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();
        $slave=new slave; $answer=0;$err="Помилка збереження даних!";
        $storage_id=$slave->qq($storage_id);$cells_id=$slave->qq($cells_id);$str_kol=$slave->qq($str_kol);
        $cell_param_ids=$slave->qq($cell_param_ids);$cell_vls=$slave->qq($cell_vls);$def_ch=$slave->qq($def_ch);
        if ($storage_id>0){
            if ($cells_id==0 || $cells_id==""){
                $r=$db->query("SELECT MAX(`id`) as mid FROM `STORAGE_CELLS`;");$cells_id=0+$db->result($r,0,"mid")+1;
                $db->query("INSERT INTO `STORAGE_CELLS` (`id`,`storage_id`,`status`) VALUES ('$cells_id','$storage_id','0');");
                $dbt->query("INSERT INTO `STORAGE_CELLS` (`id`,`storage_id`,`status`) VALUES ('$cells_id','$storage_id','0');");
            }
            if ($cells_id>0){$cell_value="";$str_arr="";
                for ($i=1;$i<=$str_kol;$i++){
                    $cell_value.=$cell_vls[$i];
                    $str_arr.=$cell_param_ids[$i];
                    if ($i<$str_kol){$cell_value.="|";$str_arr.="|";}
                }
                if ($def_ch==1){
                    $db->query("UPDATE `STORAGE_CELLS` SET `default`='0' WHERE `storage_id`='$storage_id';");
                    $dbt->query("UPDATE `STORAGE_CELLS` SET `default`='0' WHERE `storage_id`='$storage_id';");
                }
                $db->query("UPDATE `STORAGE_CELLS` SET `str_arr`='$str_arr', `cell_value`='$cell_value', `status`='1', `default`='$def_ch' WHERE `id`='$cells_id' AND `storage_id`='$storage_id';");
                $dbt->query("UPDATE `STORAGE_CELLS` SET `str_arr`='$str_arr', `cell_value`='$cell_value', `status`='1', `default`='$def_ch' WHERE `id`='$cells_id' AND `storage_id`='$storage_id';");
                $answer=1;$err="";
            }
        }
        else {
            $storage_str_id=$param_id=0;
            $answer=0;$err="Не обрано тип зберігання на складі!$storage_id,$storage_str_id,$param_id;";
        }
        return array($answer,$err);
    }

    function dropStorageCells($storage_id,$cells_id){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();
        $slave=new slave;$answer=0;$err="Помилка збереження даних!";
        $storage_id=$slave->qq($storage_id);$cells_id=$slave->qq($cells_id);
        if ($storage_id>0 && $cells_id>0){
            $db->query("UPDATE `STORAGE_CELLS` SET `status`='0' WHERE `id`='$cells_id' AND `storage_id`='$storage_id';");
            $dbt->query("UPDATE `STORAGE_CELLS` SET `status`='0' WHERE `id`='$cells_id' AND `storage_id`='$storage_id';");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function getStorageNameById($sel_id, $field="name"){$db=DbSingleton::getDb();
        $r=$db->query("SELECT `$field` FROM `A_CLIENTS` WHERE `id`='$sel_id' LIMIT 1;");$n=$db->num_rows($r);$name="";
        if ($n==1){$name=$db->result($r,0,"$field");}
        return $name;
    }

//    function loadStateSelectList($country_id,$sel_id){$slave=new slave;
//        $list=$slave->showSelectSubList("T2_STATE","COUNTRY_ID","$country_id","STATE_ID","STATE_NAME",$sel_id);
//        return $list;
//    }
//
//    function loadRegionSelectList($state_id,$sel_id){$slave=new slave;
//        return $slave->showSelectSubList("T2_REGION","STATE_ID","$state_id","REGION_ID","REGION_NAME",$sel_id);
//    }
//
//    function loadCitySelectList($region_id,$sel_id){$slave=new slave;
//        return "<option value='NEW'>Добавити населений пункт</option>".$slave->showSelectSubList("T2_CITY","REGION_ID","$region_id","CITY_ID","CITY_NAME",$sel_id);
//    }

    function getBarcode($art_id) { $db=DbSingleton::getTokoDb();
        $r=$db->query("SELECT * FROM `T2_BARCODES` WHERE `ART_ID`='$art_id' LIMIT 1;");
        $barcode=$db->result($r,0,"BARCODE");
        return $barcode;
    }

    function getStorageAmount($storage_id, $art_id) { $db=DbSingleton::getTokoDb();
        $amount=0;$reserv=0;
        $r=$db->query("SELECT * FROM `T2_ARTICLES_STRORAGE` WHERE `STORAGE_ID`='$storage_id' AND `ART_ID`='$art_id';"); $n=$db->num_rows($r);
        if ($n>0) {
            $amount = $db->result($r, 0, "AMOUNT");
            $reserv = $db->result($r, 0, "RESERV_AMOUNT");
        }
        return array($amount,$reserv);
    }

    function getStorageList($tpoind_id) { $db=DbSingleton::getTokoDb();
        if ($tpoind_id>0) $where=" AND `id`='$tpoind_id'"; else $where="";
        $r=$db->query("SELECT * FROM `STORAGE` WHERE `status`=1 $where;"); $n=$db->num_rows($r); $list="";
        for ($i=1;$i<=$n;$i++) {
            $storage_id = $db->result($r,$i-1,"id");
            $tpoint_name = $db->result($r,$i-1,"name");
            $storage_name = $db->result($r,$i-1,"full_name");
            $list.="<option value='$storage_id'>($tpoint_name) $storage_name</option>";
        }
        return $list;
    }

    function getStorageCellsList($tpoind_id) { $db=DbSingleton::getTokoDb();
        if ($tpoind_id>0) $where=" AND s.`id`='$tpoind_id'"; else $where="";
        $r=$db->query("SELECT sc.*, s.`name` as `storage_name` FROM `STORAGE` s 
            LEFT JOIN `STORAGE_CELLS` sc ON sc.storage_id=s.id
        WHERE sc.`status`=1 $where;"); $n=$db->num_rows($r); $list="";
        for ($i=1;$i<=$n;$i++) {
            $cell_id = $db->result($r,$i-1,"id");
            $cell_name = $db->result($r,$i-1,"cell_value");
            $storage_name = $db->result($r,$i-1,"storage_name");
            if ($cell_id==1197) $selected = "selected"; else $selected = "";
            $list.="<option value='$cell_id' $selected>$cell_name ($storage_name)</option>";
        }
        return $list;
    }

    function loadStorageCellsList($cell_id) { $db=DbSingleton::getTokoDb();
        $cat = new catalogue;
        $r=$db->query("SELECT * FROM `T2_ARTICLES_STRORAGE_CELLS` WHERE `STORAGE_CELLS_ID`='$cell_id';"); $n=$db->num_rows($r); $list="";
        for ($i=1;$i<=$n;$i++){
            $art_id=$db->result($r,$i-1,"ART_ID");
            $storage_id=$db->result($r,$i-1,"STORAGE_ID");
            list($name,,$brand_name,) = $cat->getArticleNrDisplBrand($art_id);
            $amount=$db->result($r,$i-1,"AMOUNT");
            $reserv=$db->result($r,$i-1,"RESERV_AMOUNT");
            list($st_amount,$st_reserv)=$this->getStorageAmount($storage_id,$art_id);
            $summ = $st_amount + $st_reserv;
            if ($amount>0 && $st_amount>0) {
                $list.="<tr>
                    <td>$i</td>
                    <td>$art_id</td>
                    <td>$name</td>
                    <td>$brand_name</td>
                    <td>$amount</td>
                    <td>$reserv</td>
                    <td>$st_amount</td>
                    <td>$st_reserv</td>
                    <td>$summ</td>
                </tr>";
            }
        }
        return $list;
    }

    function loadStorageAllCellList($storage_id) { $db=DbSingleton::getTokoDb();
        $cat = new catalogue;
        $r=$db->query("SELECT * FROM `T2_ARTICLES_STRORAGE_CELLS` WHERE `STORAGE_ID`='$storage_id';"); $n=$db->num_rows($r); $list="";
        for ($i=1;$i<=$n;$i++){
            $art_id=$db->result($r,$i-1,"ART_ID");
            $cell_id=$db->result($r,$i-1,"STORAGE_CELLS_ID"); $cell_name=$this->getStorageCellsName($cell_id,$storage_id);
            list($name,,$brand_name,) = $cat->getArticleNrDisplBrand($art_id);
            $amount=$db->result($r,$i-1,"AMOUNT");
            $reserv=$db->result($r,$i-1,"RESERV_AMOUNT");
            list($st_amount,$st_reserv)=$this->getStorageAmount($storage_id,$art_id);
            $summ = $st_amount + $st_reserv;
            $list.="<tr>
                <td>$cell_name ($cell_id)</td>
                <td>$art_id</td>
                <td>$name</td>
                <td>$brand_name</td>
                <td>$amount</td>
                <td>$reserv</td>
                <td>$st_amount</td>
                <td>$st_reserv</td>
                <td>$summ</td>
            </tr>";
        }
        return $list;
    }

    function loadStorageAllList($storage_id) { $db=DbSingleton::getTokoDb();
        $cat = new catalogue;
        $r=$db->query("SELECT * FROM `T2_ARTICLES_STRORAGE` WHERE `STORAGE_ID`='$storage_id';"); $n=$db->num_rows($r); $list="";
        for ($i=1;$i<=$n;$i++){
            $art_id=$db->result($r,$i-1,"ART_ID");
            list($name,,$brand_name,) = $cat->getArticleNrDisplBrand($art_id);
            $amount=$db->result($r,$i-1,"AMOUNT");
            $reserv=$db->result($r,$i-1,"RESERV_AMOUNT");
            $summ = $amount + $reserv;
            if ($amount>0 || $reserv>0) {
                    $list .= "<tr>
                    <td>$i</td>
                    <td>$art_id</td>
                    <td>$name</td>
                    <td>$brand_name</td>
                    <td>-</td>
                    <td>-</td>
                    <td>$amount</td>
                    <td>$reserv</td>
                    <td>$summ</td>
                </tr>";
            }
        }
        return $list;
    }

    /*EXPORT 1*/

    function exportStorageCellsList($cell_id) {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=export_stocks.csv'); ob_clean();
        $output = fopen('php://output', 'w');
        fputcsv($output, array("ART_ID","INDEX","BRAND","AMOUNT","RESERV","STORAGE AMOUNT","STORAGE RESERV"),$delimiter = ';');
        $array = $this->getStorageCells($cell_id);
        foreach ($array as $fields) {
            fputcsv($output,$fields,$delimiter = ';');
        }
        exit(0);
    }

    function getStorageCells($cell_id) { $db=DbSingleton::getTokoDb();
        $cat = new catalogue; $array=[];
        $r=$db->query("SELECT * FROM `T2_ARTICLES_STRORAGE_CELLS` WHERE `STORAGE_CELLS_ID`='$cell_id';"); $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $art_id=$db->result($r,$i-1,"ART_ID");
            $storage_id=$db->result($r,$i-1,"STORAGE_ID");
            list($name,,$brand_name,) = $cat->getArticleNrDisplBrand($art_id);
            $amount=$db->result($r,$i-1,"AMOUNT");
            $reserv=$db->result($r,$i-1,"RESERV_AMOUNT");
            list($st_amount,$st_reserv)=$this->getStorageAmount($storage_id,$art_id);
            if ($amount>0 && $st_amount>0) {
                $array[$i] = array($art_id, $name, $brand_name, $amount, $reserv, $st_amount, $st_reserv);
            }
        }
        return $array;
    }

    /*EXPORT 2*/

    function exportStorageList($storage_id) {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=export_stocks.csv'); ob_clean();
        $output = fopen('php://output', 'w');
        fputcsv($output, array("ART_ID","INDEX","BRAND","AMOUNT","RESERV","STORAGE CELLS","STORAGE AMOUNT","STORAGE RESERV","UKTZED"), $delimiter = ';');
        $array = $this->getStorageAllCellList($storage_id);
        foreach ($array as $fields) {
            fputcsv($output,$fields,$delimiter = ';');
        }
        exit(0);
    }

    function getStorageAllCellList($storage_id) { $db=DbSingleton::getTokoDb();
        $cat = new catalogue; $array=[];
        $r=$db->query("SELECT * FROM `T2_ARTICLES_STRORAGE_CELLS` WHERE `STORAGE_ID`='$storage_id';"); $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $art_id=$db->result($r,$i-1,"ART_ID");
            $cell_id=$db->result($r,$i-1,"STORAGE_CELLS_ID"); $cell_name=$this->getStorageCellsName($cell_id,$storage_id);
            list($name,,$brand_name,) = $cat->getArticleNrDisplBrand($art_id);
            $amount=$db->result($r,$i-1,"AMOUNT");
            $reserv=$db->result($r,$i-1,"RESERV_AMOUNT");
            $uktzed=$this->getArticleZED($art_id);
            list($st_amount,$st_reserv)=$this->getStorageAmount($storage_id,$art_id);
            $array[$i] = array($art_id, $name, $brand_name, $amount, $reserv, $cell_name, $st_amount, $st_reserv, $uktzed);
        }
        return $array;
    }

    function getArticleZED($art_id){ $db=DbSingleton::getTokoDb();
        $r=$db->query("SELECT t2s.COSTUMS_CODE 
        FROM `T2_ZED` t2z 
            LEFT OUTER JOIN `T2_COSTUMS` t2s ON (t2s.COSTUMS_ID=t2z.COSTUMS_ID)
        WHERE t2z.ART_ID='$art_id' LIMIT 1;");$n=$db->num_rows($r);$zed=0;
        if ($n==1){$zed=$db->result($r,0,"COSTUMS_CODE");}
        return $zed;
    }

    /*EXPORT 3*/

    function exportStorageAllList($storage_id) {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=export_stocks.csv'); ob_clean();
        $output = fopen('php://output', 'w');
        fputcsv($output, array("ART_ID","INDEX","BRAND","BARCODE","AMOUNT","RESERV","SUMM"), $delimiter = ';');
        $array = $this->getStorageAllList($storage_id);
        foreach ($array as $fields) {
            fputcsv($output,$fields,$delimiter = ';');
        }
        exit(0);
    }

    function getStorageAllList($storage_id) { $db=DbSingleton::getTokoDb();
        $cat = new catalogue; $array=[];
        $r=$db->query("SELECT * FROM `T2_ARTICLES_STRORAGE` WHERE `STORAGE_ID`='$storage_id';"); $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $art_id=$db->result($r,$i-1,"ART_ID");
            list($name,,$brand_name,) = $cat->getArticleNrDisplBrand($art_id);
            $barcode=$this->getBarcode($art_id);
            $amount=$db->result($r,$i-1,"AMOUNT");
            $reserv=$db->result($r,$i-1,"RESERV_AMOUNT");
            $summ = $amount + $reserv;
            if ($amount>0 || $reserv>0) {
                $array[$i] = array($art_id, $name, $brand_name, $barcode, $amount, $reserv, $summ);
            }
        }
        return $array;
    }

    // STORAGE DUPLICATES

    function showStorageDuplicates() { $db=DbSingleton::getTokoDb();
        $list=$list2=$list3=$list4=$list5=$list6="";
        $form_htm=RD."/tpl/storage_duplicates.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}

        $r=$db->query("SELECT `ART_ID`, `STORAGE_ID`, COUNT(`ART_ID`) as COUNT_ART 
		FROM `T2_ARTICLES_STRORAGE` GROUP BY `ART_ID`, `STORAGE_ID` HAVING COUNT(`ART_ID`)>1;"); $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $ART_ID=$db->result($r,$i-1,"ART_ID"); $ARTICLE_NR_DISPL=$this->getArtDispl($ART_ID);
            $STORAGE_ID=$db->result($r,$i-1,"STORAGE_ID"); $STORAGE_NAME=$this->getStorageName($STORAGE_ID);
            $COUNT_ART=$db->result($r,$i-1,"COUNT_ART");
            $list.="<tr>
				<td>$i</td>
				<td>$ART_ID</td>
				<td>$ARTICLE_NR_DISPL</td>
				<td>$STORAGE_NAME</td>
				<td>$COUNT_ART</td>
			</tr>";
        }

        $r=$db->query("SELECT `ART_ID`, `STORAGE_ID`, `STORAGE_CELLS_ID`, COUNT(`ART_ID`) as COUNT_ART 
		FROM `T2_ARTICLES_STRORAGE_CELLS` GROUP BY `ART_ID`, `STORAGE_ID`, `STORAGE_CELLS_ID` HAVING COUNT(`ART_ID`)>1;"); $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $ART_ID=$db->result($r,$i-1,"ART_ID"); $ARTICLE_NR_DISPL=$this->getArtDispl($ART_ID);
            $STORAGE_ID=$db->result($r,$i-1,"STORAGE_ID"); $STORAGE_NAME=$this->getStorageName($STORAGE_ID);
            $STORAGE_CELLS_ID=$db->result($r,$i-1,"STORAGE_CELLS_ID"); $STORAGE_CELLS_NAME=$this->getStorageCellsName($STORAGE_CELLS_ID,$STORAGE_ID);
            $COUNT_ART=$db->result($r,$i-1,"COUNT_ART");
            $list2.="<tr>
				<td>$i</td>
				<td>$ART_ID</td>
				<td>$ARTICLE_NR_DISPL</td>
				<td>$STORAGE_NAME</td>
				<td>$STORAGE_CELLS_NAME</td>
				<td>$COUNT_ART</td>
			</tr>";
        }

        $r=$db->query("
        SELECT st.ART_ID, st.STORAGE_ID, (st.AMOUNT+st.RESERV_AMOUNT) as SUMM_STORAGE,    
			(SELECT SUM(cl.AMOUNT+cl.RESERV_AMOUNT)
			FROM `T2_ARTICLES_STRORAGE_CELLS` cl 
			WHERE cl.ART_ID=st.ART_ID AND cl.STORAGE_ID=st.STORAGE_ID) SUMM_CELL
        FROM `T2_ARTICLES_STRORAGE` st
        WHERE (st.AMOUNT+st.RESERV_AMOUNT)!=(
			    SELECT SUM(cl.AMOUNT+cl.RESERV_AMOUNT)
                FROM `T2_ARTICLES_STRORAGE_CELLS` cl 
                WHERE cl.ART_ID=st.ART_ID AND cl.STORAGE_ID=st.STORAGE_ID)"); $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $ART_ID=$db->result($r,$i-1,"ART_ID"); $ARTICLE_NR_DISPL=$this->getArtDispl($ART_ID);
            $STORAGE_ID=$db->result($r,$i-1,"STORAGE_ID"); $STORAGE_NAME=$this->getStorageName($STORAGE_ID);
            $SUMM_STORAGE=$db->result($r,$i-1,"SUMM_STORAGE");
            $SUMM_CELL=$db->result($r,$i-1,"SUMM_CELL");
            $list3.="<tr>
				<td>$i</td>
				<td>$ART_ID</td>
				<td>$ARTICLE_NR_DISPL</td>
				<td>$STORAGE_NAME</td>
				<td>$SUMM_STORAGE</td>
				<td>$SUMM_CELL</td>
			</tr>";
        }

        $r=$db->query("
        SELECT ps.ART_ID, ps.GENERAL_STOCK, 
			(SELECT SUM(st.AMOUNT+st.RESERV_AMOUNT)
			FROM `T2_ARTICLES_STRORAGE` st
			WHERE st.ART_ID=ps.ART_ID) SUMM_STORAGE
        FROM `T2_ARTICLES_PRICE_STOCK` ps
        WHERE ps.GENERAL_STOCK!=(
			    SELECT SUM(st.AMOUNT+st.RESERV_AMOUNT)
                FROM `T2_ARTICLES_STRORAGE` st
                WHERE st.ART_ID=ps.ART_ID)"); $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $ART_ID=$db->result($r,$i-1,"ART_ID"); $ARTICLE_NR_DISPL=$this->getArtDispl($ART_ID);
            $GENERAL_STOCK=$db->result($r,$i-1,"GENERAL_STOCK");
            $SUMM_STORAGE=$db->result($r,$i-1,"SUMM_STORAGE");
            $list4.="<tr>
				<td>$i</td>
				<td>$ART_ID</td>
				<td>$ARTICLE_NR_DISPL</td>
				<td>$GENERAL_STOCK</td>
				<td>$SUMM_STORAGE</td>
			</tr>";
        }

        $r=$db->query("
        SELECT ps.ART_ID, ps.GENERAL_STOCK, 
			(SELECT SUM(st.rest)
			FROM myparts_dba.`T2_ARTICLES_PARTITIONS` st
			WHERE st.art_id=ps.ART_ID) SUMM_STORAGE
        FROM `T2_ARTICLES_PRICE_STOCK` ps
        WHERE ps.GENERAL_STOCK!=(
			    SELECT SUM(st.rest)
                FROM myparts_dba.`T2_ARTICLES_PARTITIONS` st
                WHERE st.art_id=ps.ART_ID)"); $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $ART_ID=$db->result($r,$i-1,"ART_ID"); $ARTICLE_NR_DISPL=$this->getArtDispl($ART_ID);
            $GENERAL_STOCK=$db->result($r,$i-1,"GENERAL_STOCK");
            $SUMM_STORAGE=$db->result($r,$i-1,"SUMM_STORAGE");
            $list5.="<tr>
				<td>$i</td>
				<td>$ART_ID</td>
				<td>$ARTICLE_NR_DISPL</td>
				<td>$GENERAL_STOCK</td>
				<td>$SUMM_STORAGE</td>
			</tr>";
        }

        $r=$db->query("SELECT `ART_ID`, `STORAGE_ID`, `STORAGE_CELLS_ID`, `AMOUNT`, `RESERV_AMOUNT` FROM `T2_ARTICLES_STRORAGE_CELLS` WHERE `AMOUNT`<0 OR `RESERV_AMOUNT`<0;"); $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $ART_ID=$db->result($r,$i-1,"ART_ID"); $ARTICLE_NR_DISPL=$this->getArtDispl($ART_ID);
            $STORAGE_ID=$db->result($r,$i-1,"STORAGE_ID"); $STORAGE_NAME=$this->getStorageName($STORAGE_ID);
            $STORAGE_CELLS_ID=$db->result($r,$i-1,"STORAGE_CELLS_ID"); $STORAGE_CELLS_NAME=$this->getStorageCellsName($STORAGE_CELLS_ID,$STORAGE_ID);
            $AMOUNT=$db->result($r,$i-1,"AMOUNT");
            $RESERV_AMOUNT=$db->result($r,$i-1,"RESERV_AMOUNT");
            $list6.="<tr>
				<td>$i</td>
				<td>$ART_ID</td>
				<td>$ARTICLE_NR_DISPL</td>
				<td>$STORAGE_NAME</td>
				<td>$STORAGE_CELLS_NAME</td>
				<td>$AMOUNT</td>
				<td>$RESERV_AMOUNT</td>
			</tr>";
        }

        $form=str_replace("{storage_duplicates_range}",$list,$form);
        $form=str_replace("{storage_duplicates_cells_range}",$list2,$form);
        $form=str_replace("{storage_stock_range}",$list3,$form);
        $form=str_replace("{storage_stock_general_range}",$list4,$form);
        $form=str_replace("{partition_stock_general_range}",$list5,$form);
        $form=str_replace("{storage_minus}",$list6,$form);
        return $form;
    }

    function getCountDocs($art_id, $storage_id) { $db=DbSingleton::getDb();
        $full_amount=0; $doc_name="";
        if ($art_id>0){
            $r=$db->query("SELECT j.id, j.prefix, j.doc_nom, j.type_id, j.data, SUM(js.amount) as amount, j.user_id 
            FROM `J_MOVING_STR` js 
                LEFT OUTER JOIN `J_MOVING` j ON (j.id=js.jmoving_id) 
            WHERE js.art_id='$art_id' AND js.status_jmoving IN (44,45) AND j.status='1' AND js.amount>0 AND js.storage_id_from='$storage_id' AND 
            (j.oper_status='30' OR (j.oper_status='31' AND j.status_jmoving='49' OR j.status_jmoving='48')) AND j.parrent_type_id=0 AND j.parrent_doc_id=0 
            GROUP BY j.id;");$n=$db->num_rows($r);
            for ($i=1;$i<=$n;$i++){
                $amount=$db->result($r,$i-1,"amount");
                $prefix=$db->result($r,$i-1,"prefix");
                $doc_nom=$db->result($r,$i-1,"doc_nom");
                $full_amount+=$amount;
                $doc_name.=" $prefix-$doc_nom";
            }
            $r=$db->query("SELECT dp.id, dp.prefix, dp.doc_nom, dp.data, SUM(dps.amount) as amount, SUM(dps.amount_collect) as amount_collect, dp.user_id, dp.client_id 
            FROM `J_DP_STR` dps 
                LEFT OUTER JOIN `J_DP` dp ON (dp.id=dps.dp_id) 
            WHERE dps.art_id='$art_id' AND dps.status_dps=93 AND dp.status='1' AND dps.amount>0 AND dps.location_storage_id='$storage_id' AND (dp.oper_status='30' OR dp.oper_status='31') 
            GROUP BY dp.id;");$n=$db->num_rows($r);
            for ($i=1;$i<=$n;$i++){
                $amount=$db->result($r,$i-1,"amount");$amount_dis=$amount;
                $amount_collect=$db->result($r,$i-1,"amount_collect");if ($amount_collect>0){$amount_dis=$amount_collect;}
                $prefix=$db->result($r,$i-1,"prefix");
                $doc_nom=$db->result($r,$i-1,"doc_nom");
                $full_amount+=$amount_dis;
                $doc_name.=" $prefix-$doc_nom";
            }
            $r=$db->query("SELECT dp.id, dp.prefix, dp.doc_nom, dp.data, SUM(dps.amount) as amount, SUM(dps.amount_collect) as amount_collect, dp.user_id, dp.client_id 
            FROM `J_DP_STR` dps 
                LEFT OUTER JOIN `J_DP` dp ON (dp.id=dps.dp_id) 
            WHERE dps.art_id='$art_id' AND dps.status_dps IN (94,95,96) AND dp.status='1' AND dps.amount>0 AND dps.location_storage_id='$storage_id' AND (dp.oper_status='30' OR dp.oper_status='31') 
            GROUP BY dp.id;");$n=$db->num_rows($r);
            for ($i=1;$i<=$n;$i++){
                $amount=$db->result($r,$i-1,"amount");$amount_dis=$amount;
                $amount_collect=$db->result($r,$i-1,"amount_collect");if ($amount_collect>0){$amount_dis=$amount_collect;}
                $prefix=$db->result($r,$i-1,"prefix");
                $doc_nom=$db->result($r,$i-1,"doc_nom");
                $full_amount+=$amount_dis;
                $doc_name.=" $prefix-$doc_nom";
            }
        }
        return array($full_amount,$doc_name);
    }

    function getArtDispl($art_id) {$db=DbSingleton::getTokoDb();
        $r=$db->query("SELECT `ARTICLE_NR_DISPL` FROM `T2_ARTICLES` WHERE `ART_ID`='$art_id' LIMIT 1;");$n=$db->num_rows($r);$ARTICLE_NR_DISPL="";
        if ($n==1){$ARTICLE_NR_DISPL=$db->result($r,0,"ARTICLE_NR_DISPL");}
        return $ARTICLE_NR_DISPL;
    }

}
