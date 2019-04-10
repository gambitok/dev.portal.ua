<?php

class clients {

    function newClientCard(){$client_id=0;
    //	$r=$db->query("select max(id) as mid from A_CLIENTS;");$client_id=0+$db->result($r,0,"mid")+1;
    //	$db->query("insert into A_CLIENTS (`id`,`user_id`) values ('$client_id','$user_id');");
        return $client_id;
    }

    function checkEmptyClients() {$db=DbSingleton::getDb();
        $r=$db->query("select * from A_CLIENTS where name='' and full_name='';"); $n=$db->num_rows($r);
        if ($n>0) {
            $list="<ul>";
            for ($i=1;$i<=$n;$i++){
                $id=$db->result($r,$i-1,"id");
                $list.="<li><a style='cursor:pointer; font-size:20px;' onClick='showClientCard(\"$id\")'>
                    Контрагент з ID: $id 
                    <i class='fa fa-external-link'></i>
                </a></li>";
            }
            $list.="</ul>";
        } else $list=false;
        return $list;
    }

    function show_clients_list($client_id = 0,$client_name = '',$phone = '',$email = '',$state_id = ''){$db=DbSingleton::getDb();$where="";
        if ($client_name!=""){$where=" and c.name LIKE '%$client_name%'";}
        if ($phone!=""){$where=" and c.phone like '%$phone%'";}
        if ($email!=""){$where=" and c.email like '%$email%'";}
        if ($state_id>0 && $state_id!=""){$where=" and c.state='$state_id'";}
        if ($client_id>0 && $client_id!=""){$where=" and c.id='$client_id'";}

        $r=$db->query("select c.*, ot.name as org_type_name, t2cn.COUNTRY_NAME, t2st.STATE_NAME, t2rg.REGION_NAME, t2ct.CITY_NAME  
        from A_CLIENTS c 
            left outer join A_ORG_TYPE ot on ot.id=c.org_type 
            left outer join T2_COUNTRIES t2cn on t2cn.COUNTRY_ID=c.country 
            left outer join T2_STATE t2st on t2st.STATE_ID=c.state
            left outer join T2_REGION t2rg on t2rg.REGION_ID=c.region
            left outer join T2_CITY t2ct on t2ct.CITY_ID=c.city
        where c.status=1 $where;");$n=$db->num_rows($r);$list="";

        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"name");
            $org_type_name=$db->result($r,$i-1,"org_type_name");
            $email=$db->result($r,$i-1,"email");
            $phone=$db->result($r,$i-1,"phone");
            $country=$db->result($r,$i-1,"COUNTRY_NAME");
            $state=$db->result($r,$i-1,"STATE_NAME");
            $region=$db->result($r,$i-1,"REGION_NAME");
            $city=$db->result($r,$i-1,"CITY_NAME");
            $list.="<tr style='cursor:pointer' onClick='showClientCard(\"$id\")'>
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
        return $list;
    }

    function showClientsParrentTree($sel_id,$prnt_id){$db=DbSingleton::getDb();
        $form="";$form_htm=RD."/tpl/clients_parrent_tree.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select c.*, ot.name as org_type_name, t2cn.COUNTRY_NAME, t2st.STATE_NAME, t2rg.REGION_NAME, t2ct.CITY_NAME  
        from A_CLIENTS c 
            left outer join A_ORG_TYPE ot on ot.id=c.org_type 
            left outer join T2_COUNTRIES t2cn on t2cn.COUNTRY_ID=c.country 
            left outer join T2_STATE t2st on t2st.STATE_ID=c.state
            left outer join T2_REGION t2rg on t2rg.REGION_ID=c.region
            left outer join T2_CITY t2ct on t2ct.CITY_ID=c.city
        where c.status=1 and c.parrent_id=0;");$n=$db->num_rows($r);$list="";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"name");
            $org_type_name=$db->result($r,$i-1,"org_type_name");
            $email=$db->result($r,$i-1,"email");
            $phone=$db->result($r,$i-1,"phone");
            $country=$db->result($r,$i-1,"COUNTRY_NAME");
            $state=$db->result($r,$i-1,"STATE_NAME");
            $region=$db->result($r,$i-1,"REGION_NAME");
            $city=$db->result($r,$i-1,"CITY_NAME");
            $cur="";$fn="<i class='fa fa-thumb-tack' onClick='setClientParrent(\"$id\", \"$name\")'></i>";
            if ($id==$prnt_id){$cur="style='background-color:#FFFF00'";}if ($id==$sel_id){$fn="";$cur="style='background-color:#ccc; disabled:disabled;'";}
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

    function moveClientsRetail($user_retail_id,$client_retail_id){$db=DbSingleton::getDb();$answer=0;$err="Помилка збереження даних!";
        if ($user_retail_id>0) {
            $r=$db->query("select * from A_CLIENTS_USERS_RETAIL where id='$user_retail_id' limit 0,1;"); $n=$db->num_rows($r);
            if ($n>0) {
                $name=$db->result($r,0,"name");
                $email=$db->result($r,0,"email");
                $phone=$db->result($r,0,"phone");
                $country=$db->result($r,0,"country_id");
                $state=$db->result($r,0,"state_id");
                $region=$db->result($r,0,"region_id");
                $city=$db->result($r,0,"city_id");
                $pass=$db->result($r,0,"pass");
                $client_category=$db->result($r,0,"client_category");

                $r=$db->query("select max(id) as mid from A_CLIENTS;"); $client_id=0+$db->result($r,0,"mid")+1;
                $db->query("insert into A_CLIENTS (`id`, `name`, `full_name`, `phone`, `email`, `country`, `state`, `region`, `city`, `client_category`) 
                values ('$client_id','$name','$name','$phone','$email','$country','$state','$region','$city','$client_category');");

                $r=$db->query("select max(id) as mid from A_CLIENTS_USERS;"); $user_id=0+$db->result($r,0,"mid")+1;
                $db->query("insert into A_CLIENTS_USERS (`id`, `client_id`, `name`, `email`, `phone`, `pass`, `status`) values ('$user_id','$client_id','$name','$email','$phone','$pass',1);");

                $this->moveClientsConditionsRetail($client_retail_id,$client_id);

                //галочка 'Клієнт'
                $db->query("insert into A_CLIENTS_CATEGORY (`client_id`,`category_id`) values ('$client_id','1');");

                $db->query("update A_CLIENTS_USERS_RETAIL set `status`=147, `user_id_created`='$user_id', `client_id_created`='$client_id' where `id`='$user_retail_id';");
                $answer=1; $err="";
            }
        }
        return array($answer,$err);
    }

    function moveClientsConditionsRetail($client_retail_id,$client_id) {$db=DbSingleton::getDb();
        $r=$db->query("select * from A_CLIENTS_CONDITIONS where client_id='$client_retail_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $cash_id=$db->result($r,0,"cash_id");
            $country_cash_id=$db->result($r,0,"country_cash_id");
            $credit_cash_id=$db->result($r,0,"credit_cash_id");
            $payment_delay=$db->result($r,0,"payment_delay");
            $credit_limit=$db->result($r,0,"credit_limit");
            $credit_return=$db->result($r,0,"credit_return");
            $price_lvl=$db->result($r,0,"price_lvl");
            $margin_price_lvl=$db->result($r,0,"margin_price_lvl");
            $price_suppl_lvl=$db->result($r,0,"price_suppl_lvl");
            $margin_price_suppl_lvl=$db->result($r,0,"margin_price_suppl_lvl");
            $tpoint_id=$db->result($r,0,"tpoint_id");
            $client_vat=$db->result($r,0,"client_vat");
            $doc_type_id=$db->result($r,0,"doc_type_id");
            $db->query("insert into A_CLIENTS_CONDITIONS (`client_id`, `cash_id`, `country_cash_id`, `credit_cash_id`, `payment_delay`, `credit_limit`, `credit_return`, `price_lvl`, `margin_price_lvl`, `price_suppl_lvl`, `margin_price_suppl_lvl`, `tpoint_id`, `client_vat`, `doc_type_id`) 
            values ('$client_id','$cash_id','$country_cash_id','$credit_cash_id','$payment_delay','$credit_limit','$credit_return','$price_lvl','$margin_price_lvl','$price_suppl_lvl','$margin_price_suppl_lvl','$tpoint_id','$client_vat','$doc_type_id');");
        }
        return;
    }

    function unlinkClientsParrent($client_id){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка збереження даних!";
        $client_id=$slave->qq($client_id);
        if ($client_id>0){
            $db->query("update A_CLIENTS set `parrent_id`='0' where `id`='$client_id';");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function unlinkClientsSubclient($client_id,$subclient_id){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка збереження даних!";
        $client_id=$slave->qq($client_id);$subclient_id=$slave->qq($subclient_id);
        if ($client_id>0 && $subclient_id>0){
            $db->query("update A_CLIENTS set `parrent_id`='0' where `id`='$subclient_id';");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function showCategoryTree($sel_id=null){$db=DbSingleton::getDb();$tree="";//$form="";
        //$form="";$form_htm=RD."/tpl/clients_category_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select * from A_CATEGORY where parrent_id='0' order by id asc;");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"name");
            $sel="";if ($sel_id==$id){$sel=" data-jstree='{\"selected\":true}'";}
            $tree.="<li id='$id' $sel>$name".$this->showCategoryTreeSubLevel($id,$sel_id)."</li>";
        }
        //$form=str_replace("{tree}",$tree,$form);
        //$form=str_replace("{category_id}",$sel_id,$form);
        return $tree;
    }

    function showCategoryTreeSubLevel($parrent_id,$sel_id){$db=DbSingleton::getDb();$tree="";
        $r=$db->query("select * from A_CATEGORY where parrent_id='$parrent_id' order by id asc;");$n=$db->num_rows($r);
        if ($n>0){$tree.="<ul>";
            for ($i=1;$i<=$n;$i++){
                $id=$db->result($r,$i-1,"id");
                $name=$db->result($r,$i-1,"name");
                $sel="";if ($sel_id==$id){$sel=" data-jstree='{\"selected\":true}'";}
                $tree.="<li id='$id' $sel>$name".$this->showCategoryTreeSubLevel($id,$sel_id)."</li>";
            }
            $tree.="</ul>";
        }
        return $tree;
    }

    function checkClientSubclients($sel_id){$db=DbSingleton::getDb();$ar=array();$kol_childs=0;$list="";
        $r=$db->query("select c.*, ot.name as org_type_name from A_CLIENTS c 
            left outer join A_ORG_TYPE ot on ot.id=c.org_type 
        where c.parrent_id='$sel_id' and c.status='1' order by c.id asc;");$n=$db->num_rows($r);

        if ($n>0){$kol_childs=$n;
            for ($i=1;$i<=$n;$i++){
                $id=$db->result($r,$i-1,"id");
                $org_type=$db->result($r,$i-1,"org_type_name");
                $name=$db->result($r,$i-1,"name");
                $email=$db->result($r,$i-1,"email");
                $phone=$db->result($r,$i-1,"phone");
                $ar[$i]["id"]=$id;
                $ar[$i]["org_type"]=$org_type;
                $ar[$i]["name"]=$name;
                $ar[$i]["email"]=$email;
                $ar[$i]["phone"]=$phone;
                $list.="<tr>
                    <td align='center'>$id</td>
                    <td>$org_type</td>
                    <td>$name</td>
                    <td>$email</td>
                    <td>$phone</td>
                    <td align='center'><button type='button' class='btn btn-xs btn-default' id='unlinkSubDropBtn' onClick='unlinkClientsSubclient(\"$sel_id\",\"$id\");'><i class='fa fa-times'></i></button></td>
                </tr>";
            }
        }
        return array($kol_childs,$ar,$list);
    }

    function showClientCard($client_id){$db=DbSingleton::getDb();$slave=new slave;session_start();$user_id=$_SESSION["media_user_id"];$user_name=$_SESSION["user_name"];
        $country=$state=$region=$city="";
        $org_type=$parrent_id=0;$full_name=$name=$email=$phone=$client_category_list=$childsTable=$clds_disabled=$chlds_hidden=$parrent_main_active="";
        $form="";$form_htm=RD."/tpl/clients_card.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select c.*, ot.full_name as ot_full_name from A_CLIENTS c  
            left outer join A_ORG_TYPE ot on ot.id=c.org_type
        where c.id='$client_id' and c.status='1' limit 0,1;");$n=$db->num_rows($r);

        if ($n==0){
            $client_id=0;
        }
        if ($n==1){
            $client_id=$db->result($r,0,"id");
            $org_type=$db->result($r,0,"org_type");
            $full_name=$db->result($r,0,"full_name");
            $name=$db->result($r,0,"name");
            $email=$db->result($r,0,"email");
            $phone=$db->result($r,0,"phone");
            $parrent_id=$db->result($r,0,"parrent_id");
            list($hasChilds,,$childsTable)=$this->checkClientSubclients($client_id);
            $parrent_main_active="";$chlds_hidden="hidden";$clds_disabled="disabled";if ($hasChilds>0){$parrent_main_active=" disabled";$chlds_hidden="";$clds_disabled="";}
            $country=$db->result($r,0,"country");
            $state=$db->result($r,0,"state");
            $region=$db->result($r,0,"region");
            $city=$db->result($r,0,"city");
            $client_category=$db->result($r,0,"client_category");
            $client_category_list=$this->showUserCategorySelectList($client_category);
        }
        $form=str_replace("{client_id}",$client_id,$form);
        $form=str_replace("{org_type_list}",$slave->showSelectList("A_ORG_TYPE","id","name",$org_type),$form);
        $form=str_replace("{client_full_name}",$slave->qqback_in($full_name),$form);
        $form=str_replace("{client_name}",$slave->qqback_in($name),$form);
        $form=str_replace("{email}",$email,$form);
        $form=str_replace("{phone}",$phone,$form);
        $form=str_replace("{parrent_main_active}",$parrent_main_active,$form);
        $form=str_replace("{chlds_hidden}",$chlds_hidden,$form);
        $form=str_replace("{clds_disabled}",$clds_disabled,$form);
        $form=str_replace("{clientChildsList}",$childsTable,$form);
        $form=str_replace("{parrent_id}",$parrent_id,$form);
        $form=str_replace("{parrent_name}",$this->getClientNameById($parrent_id,"name"),$form);
        $form=str_replace("{country_list}",$slave->showSelectList("T2_COUNTRIES","COUNTRY_ID","COUNTRY_NAME",$country),$form);
        $form=str_replace("{state_list}",$slave->showSelectSubList("T2_STATE","COUNTRY_ID","$country","STATE_ID","STATE_NAME",$state),$form);
        $form=str_replace("{region_list}",$slave->showSelectSubList("T2_REGION","STATE_ID","$state","REGION_ID","REGION_NAME",$region),$form);
        $form=str_replace("{city_list}",$slave->showSelectSubListDBM("T2_CITY","REGION_ID","$region","CITY_ID","CITY_NAME",$city),$form);
        $form=str_replace("{category_check_list}",$this->showCategoryCheckList($client_id),$form);
        $form=str_replace("{category_list}",$client_category_list,$form);
        $form=str_replace("{general_saldo}",$this->getClientGeneralSaldo($client_id),$form);
        $form=str_replace("{my_user_id}",$user_id,$form);
        $form=str_replace("{my_user_name}",$user_name,$form);
        return $form;
    }

    function show_clients_retail_list($press = false){ $db=DbSingleton::getDb();
        $r=$db->query("select * from A_CLIENTS_USERS_RETAIL order by client_category desc, data desc, name asc;");$n=$db->num_rows($r);$list="";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $client_id=$db->result($r,$i-1,"client_id"); $client_id=$this->getClientNameById($client_id);
            $client_category=$db->result($r,$i-1,"client_category"); $client_category_name=$this->getManualName($client_category);
            $data=$db->result($r,$i-1,"data");
            $name=$db->result($r,$i-1,"name");
            $email=$db->result($r,$i-1,"email");
            $phone=$db->result($r,$i-1,"phone");
            $city_id=$db->result($r,$i-1,"city_id"); $city_id=$this->getT2City($city_id);
            $status=$db->result($r,$i-1,"status");
            $status_cap=$this->getManualName($status);

            switch($client_category) {
                case 141:  $color="background: pink"; break;
                case 142:  $color="background: #ffff4c"; break;
                case 143:  $color="background: #ffca67"; break;
                case 144:  $color="background: lightgreen"; break;
                case 145:  $color="background: lightblue"; break;
                default:   $color=""; break;
            }

            if($press) {
                $list.="<tr style='cursor:pointer; $color' onClick='showClientRetailCard(\"$id\")'>
                    <td>$id</td>
                    <td>$client_id</td>
                    <td>$client_category_name</td>
                    <td>$data</td>
                    <td>$name</td>
                    <td>$email</td>
                    <td>$phone</td>
                    <td>$city_id</td>
                    <td>$status_cap</td>
                </tr>";
            }

            else {
                if($status!=147) {
                    $list.="<tr style='cursor:pointer; $color' onClick='showClientRetailCard(\"$id\")'>
                        <td>$id</td>
                        <td>$client_id</td>
                        <td>$client_category_name</td>
                        <td>$data</td>
                        <td>$name</td>
                        <td>$email</td>
                        <td>$phone</td>
                        <td>$city_id</td>
                        <td>$status_cap</td>
                    </tr>";
                }
            }
        }
        return $list;
    }

    function showClientRetailCard($user_id){$db=DbSingleton::getDb(); $slave=new slave;
        $form="";$form_htm=RD."/tpl/clients_retail_card.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select * from A_CLIENTS_USERS_RETAIL where id='$user_id' limit 0,1;"); $n=$db->num_rows($r);
        if ($n==0){$form_htm=RD."/tpl/access_deny.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);} }
        if ($n==1){
            $client_id=$db->result($r,0,"client_id");
            $client_category=$db->result($r,0,"client_category");
            $data=$db->result($r,0,"data");
            $name=$db->result($r,0,"name");
            $email=$db->result($r,0,"email");
            $phone=$db->result($r,0,"phone");
            $country=$db->result($r,0,"country_id");
            $state=$db->result($r,0,"state_id");
            $region=$db->result($r,0,"region_id");
            $city=$db->result($r,0,"city_id");
            $pass=$db->result($r,0,"pass");
            $status=$db->result($r,0,"status");

            $user_id_created=$db->result($r,0,"user_id_created"); $user_id_created_name=$this->getUserNameById($user_id_created);
            $client_id_created=$db->result($r,0,"client_id_created"); $client_id_created_name=$this->getClientNameById($client_id_created);
            $move_client_retail="";
            if ($status==147) $move_client_retail="<div class='hr-line-dashed'></div>
            Контрагент: $client_id_created_name (ID:$client_id_created), пользователь: $user_id_created_name (ID:$user_id_created)";

            $form=str_replace("{move_client_retail}",$move_client_retail,$form);
            $form=str_replace("{user_id}",$user_id,$form);
            $retail_client=$this->getClientNameById($client_id);
            $form=str_replace("{client_id}",$client_id,$form);
            $form=str_replace("{client_name}",$retail_client,$form);
            $form=str_replace("{user_category}",$client_category,$form);
            $client_category_list=$this->showUserCategorySelectList($client_category);
            $form=str_replace("{category_list}",$client_category_list,$form);
            $form=str_replace("{user_data}",$data,$form);
            $form=str_replace("{user_name}",$name,$form);
            $form=str_replace("{user_email}",$email,$form);
            $form=str_replace("{user_phone}",$phone,$form);
            $form=str_replace("{user_pass}",$pass,$form);
            $form=str_replace("{country_list}",$slave->showSelectList("T2_COUNTRIES","COUNTRY_ID","COUNTRY_NAME",$country),$form);
            $form=str_replace("{state_list}",$slave->showSelectSubList("T2_STATE","COUNTRY_ID","$country","STATE_ID","STATE_NAME",$state),$form);
            $form=str_replace("{region_list}",$slave->showSelectSubList("T2_REGION","STATE_ID","$state","REGION_ID","REGION_NAME",$region),$form);
            $form=str_replace("{city_list}",$slave->showSelectSubListDBM("T2_CITY","REGION_ID","$region","CITY_ID","CITY_NAME",$city),$form);
            $status_list=$this->showUserStatusSelectList($status);
            $form=str_replace("{status_list}",$status_list,$form);
            if ($status==146 || $status==147) $disabled_status="disabled"; else $disabled_status="";
            $form=str_replace("{disabled_status}",$disabled_status,$form);
        }
        return $form;
    }

    function newClientRetailCard(){$db=DbSingleton::getDb(); $client_id=10;
        $r=$db->query("select max(id) as mid from A_CLIENTS_USERS_RETAIL;");$user_id=0+$db->result($r,0,"mid")+1;
        $db->query("insert into A_CLIENTS_USERS_RETAIL (`id`,`client_id`) values ('$user_id','$client_id');");
        return $user_id;
    }

    function saveClientRetailGeneralInfo($user_id,$user_name,$country_id,$state_id,$region_id,$city_id,$user_category,$user_phone,$user_email,$user_status){$db=DbSingleton::getDb();$answer=0;$err="Помилка збереження даних!";
        if ($user_id>0){
            $db->query("update A_CLIENTS_USERS_RETAIL set `name`='$user_name',`country_id`='$country_id',`state_id`='$state_id',`region_id`='$region_id',`city_id`='$city_id',`client_category`='$user_category',`phone`='$user_phone',`email`='$user_email',`status`='$user_status' where `id`='$user_id';");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function showUserStatusSelectList($sel_id){$db=DbSingleton::getDb();$list="";
        $r=$db->query("select * from manual where `key`='user_retail_status' and ison=1 order by mcaption, id asc;");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"mcaption");
            $sel="";if ($id==$sel_id){$sel=" selected";}
            $list.="<option value='$id' $sel>$name</option>";
        }
        return $list;
    }

    function showUserCategorySelectList($sel_id){$db=DbSingleton::getDb();$list="";;
        $r=$db->query("select * from manual where `key`='customers_categories' and ison=1 order by id asc;");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"mcaption");
            $sel="";if ($id==$sel_id){$sel=" selected";}
            $list.="<option value='$id' $sel>$name</option>";
        }
        return $list;
    }

    function getT2City($id) {$db=DbSingleton::getDb();
        $r=$db->query("select CITY_NAME from T2_CITY where CITY_ID='$id';");
        $city_name=$db->result($r,0,"CITY_NAME");
        return $city_name;
    }

    function loadCityOptions($city_id){$db=DbSingleton::getDb();
        $r=$db->query("select * from T2_CITY order by CITY_NAME asc limit 10;");$n=$db->num_rows($r);$list="";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"CITY_ID");
            $name=$db->result($r,$i-1,"CITY_NAME");
            $sel="";if ($city_id==$id){$sel="selected='selected'";}
            $list.="<option value='$id' $sel>$name</option>";
        }
        return $list;
    }

    function getManualName($key) {$db=DbSingleton::getDb();
        $r=$db->query("select `mcaption` from manual where `id`='$key';");
        $caption=$db->result($r,0,"mcaption");
        return $caption;
    }

    function saveClientGeneralInfo($client_id,$org_type,$name,$full_name,$phone,$email,$parrent_id,$country_id,$state_id,$region_id,$city_id,$c_category_kol,$c_category,$user_category){$db=DbSingleton::getDb();$slave=new slave;session_start();$user_id=$_SESSION["media_user_id"];$answer=0;$err="Помилка збереження даних!";
        $client_id=$slave->qq($client_id);$org_type=$slave->qq($org_type);$name=$slave->qq($name);$full_name=$slave->qq($full_name);$phone=$slave->qq($phone);$email=$slave->qq($email);
        $parrent_id=$slave->qq($parrent_id);$country_id=$slave->qq($country_id);$state_id=$slave->qq($state_id);$city_id=$slave->qq($city_id);$region_id=$slave->qq($region_id);$c_category_kol=$slave->qq($c_category_kol);
        $return_client=0;
        if ($name!="" || $full_name!="") {
            if ($client_id==0) {
                $r=$db->query("select max(id) as mid from A_CLIENTS;");$new_client_id=0+$db->result($r,0,"mid")+1;
                $db->query("insert into A_CLIENTS (`id`,`user_id`) values ('$new_client_id','$user_id');");
                $db->query("update A_CLIENTS set `org_type`='$org_type', `name`='$name', `full_name`='$full_name', `phone`='$phone', `email`='$email', `parrent_id`='$parrent_id', `country`='$country_id', `state`='$state_id', `city`='$city_id', `region`='$region_id', `client_category`='$user_category' where `id`='$new_client_id';");
                $answer=1;$err="";$return_client=$new_client_id;
            }
            if ($client_id>0){
                $db->query("update A_CLIENTS set `org_type`='$org_type', `name`='$name', `full_name`='$full_name', `phone`='$phone', `email`='$email', `parrent_id`='$parrent_id', `country`='$country_id', `state`='$state_id', `city`='$city_id', `region`='$region_id', `client_category`='$user_category' where `id`='$client_id';");
                //A_CLIENTS_CATEGORY UPDATE
                $db->query("delete from A_CLIENTS_CATEGORY where client_id='$client_id';");
                for($i=1;$i<=$c_category_kol;$i++){
                    $cc=$c_category[$i];
                    if ($cc>0){
                        $db->query("insert into A_CLIENTS_CATEGORY (`client_id`,`category_id`) values ('$client_id','$cc');");
                    }
                }
                $answer=1;$err="";$return_client=$client_id;
            }
        }
        else $err="Спершу введіть назву клієнта!";
        return array($answer,$err,$return_client);
    }

    function loadClientDocumentPrefix($client_id){$db=DbSingleton::getDb();$gmanual=new gmanual;
        $form="";$form_htm=RD."/tpl/clients_document_prefix_list.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select * from A_CLIENTS_DOCUMENT_PREFIX where client_id='$client_id' and status='1' order by doc_type_id asc;");$n=$db->num_rows($r);$list="";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $doc_type_id=$db->result($r,$i-1,"doc_type_id");
            $doc_type_caption=$gmanual->get_gmanual_caption($doc_type_id);
            $prefix=$db->result($r,$i-1,"prefix");
            $list.="<tr>
                <td>
                    <button class='btn btn-sm btn-default' onClick='showClientDocumentPrefixForm(\"$client_id\",\"$id\");'><i class='fa fa-edit'></i></button>
                    <button class='btn btn-sm btn-default' onClick='dropClientDocumentPrefix(\"$client_id\",\"$id\",\"$prefix\");'><i class='fa fa-times'></i></button>
                </td>
                <td>$i</td>
                <td>$doc_type_caption</td>
                <td>$prefix</td>
            </tr>";
        }
        if ($n==0){$list="<tr><td align='center' colspan=6><h3 class='text-center'>Записи відсутні</h3></td></tr>";}
        $form=str_replace("{list_prefix}",$list,$form);
        $form=str_replace("{client_id}",$client_id,$form);
        return $form;
    }

    function showClientDocumentPrefixForm($client_id,$prefix_id){$db=DbSingleton::getDb();$gmanual=new gmanual;$prefix="";$doc_type_id=0;
        $form="";$form_htm=RD."/tpl/clients_document_prefix_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select * from A_CLIENTS_DOCUMENT_PREFIX where id='$prefix_id' and client_id='$client_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $doc_type_id=$db->result($r,0,"doc_type_id");
            $prefix=$db->result($r,0,"prefix");
        }
        $form=str_replace("{client_id}",$client_id,$form);
        $form=str_replace("{prefix_id}",$prefix_id,$form);
        $form=str_replace("{doc_type_list}",$gmanual->showGmanualSelectList("doc_type_id",$doc_type_id),$form);
        $form=str_replace("{prefix}",$prefix,$form);
        return $form;
    }

    function saveClientDocumentPrefixForm($client_id,$prefix_id,$doc_type_id,$prefix){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка збереження даних!";
        $client_id=$slave->qq($client_id);$prefix_id=$slave->qq($prefix_id);$doc_type_id=$slave->qq($doc_type_id);$prefix=$slave->qq($prefix);
        if ($client_id>0 && $doc_type_id>0 && $prefix!=""){
            $r=$db->query("select count(id) as kol from A_CLIENTS_DOCUMENT_PREFIX where doc_type_id='$doc_type_id' and client_id='$client_id' and id<>'$prefix_id' and status='1';");$doc_type_ex=$db->result($r,0,"kol");
            if ($doc_type_ex>0){
                $answer=0;$err="Обраний Вами тип документу вже має префікс! Відредагуйте існуючий префікс за потреби";
            }
            if ($doc_type_ex==0 ){
                if ($prefix_id==0 || $prefix_id==""){
                    $r=$db->query("select max(id) as mid from A_CLIENTS_DOCUMENT_PREFIX;");$prefix_id=0+$db->result($r,0,"mid")+1;
                    $db->query("insert into A_CLIENTS_DOCUMENT_PREFIX (id,client_id) values ('$prefix_id','$client_id');");
                }
                $db->query("update A_CLIENTS_DOCUMENT_PREFIX set prefix='$prefix', `doc_type_id`='$doc_type_id' where id='$prefix_id' and client_id='$client_id';");
                $answer=1;$err="";
            }
        }else{$answer=0;$err="Не заповнені усі поля!";}
        return array($answer,$err);
    }

    function dropClientDocumentPrefix($client_id,$prefix_id){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка збереження даних!";
        $client_id=$slave->qq($client_id);$prefix_id=$slave->qq($prefix_id);
        if ($client_id>0 && $prefix_id>0){
            $db->query("update A_CLIENTS_DOCUMENT_PREFIX set status='0' where id='$prefix_id' and client_id='$client_id';");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function loadClientUsers($client_id){$db=DbSingleton::getDb();
        $form="";$form_htm=RD."/tpl/clients_users_list.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select cc.* from A_CLIENTS_USERS cc 
        where cc.client_id='$client_id' and cc.status='1' order by cc.name asc;");$n=$db->num_rows($r);$list="";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"name");
            $email=$db->result($r,$i-1,"email");
            $phone=$db->result($r,$i-1,"phone");
            $pass=$db->result($r,$i-1,"pass");
            $list.="<tr>
                <td>
                    <button class='btn btn-sm btn-default' onClick='showClientUserForm(\"$client_id\",\"$id\");'><i class='fa fa-edit'></i></button>
                    <button class='btn btn-sm btn-default' onClick='dropClientUser(\"$client_id\",\"$id\",\"$name\");'><i class='fa fa-times'></i></button>
                </td>
                <td>$id</td>
                <td>$name</td>
                <td>$email</td>
                <td>$phone</td>
                <td>$pass</td>
            </tr>";
        }
        if ($n==0){$list="<tr><td align='center' colspan=6><h3 class='text-center'>Користувачі відсутні</h3></td></tr>";}
        $form=str_replace("{list_users}",$list,$form);
        $form=str_replace("{client_id}",$client_id,$form);
        return $form;
    }

    function showClientUserForm($client_id,$user_id){$db=DbSingleton::getDb();
        $form="";$form_htm=RD."/tpl/clients_user_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select * from A_CLIENTS_USERS where id='$user_id' limit 0,1;");
        $user_id=$db->result($r,0,"id");
        $name=$db->result($r,0,"name");
        $email=$db->result($r,0,"email");
        $phone=$db->result($r,0,"phone");
        $pass=$db->result($r,0,"pass");
        $main=$db->result($r,0,"main");$main_ch="";if ($main==1){$main_ch=" checked='checked'";}
        $price=$db->result($r,0,"price_status");$price_ch="";if ($price==1){$price_ch=" checked='checked'";}
        $export=$db->result($r,0,"export_status");$export_ch="";if ($export==1){$export_ch=" checked='checked'";}
        $form=str_replace("{client_id}",$client_id,$form);
        $form=str_replace("{user_id}",$user_id,$form);
        $form=str_replace("{name}",$name,$form);
        $form=str_replace("{email}",$email,$form);
        $form=str_replace("{phone}",$phone,$form);
        $form=str_replace("{pass}",$pass,$form);
        $form=str_replace("{main_ch}",$main_ch,$form);
        $form=str_replace("{price_ch}",$price_ch,$form);
        $form=str_replace("{export_ch}",$export_ch,$form);
        return $form;
    }

    function saveClientUserForm($client_id,$user_id,$name,$email,$phone,$pass,$main,$price,$export){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка збереження даних!";
        $client_id=$slave->qq($client_id);$user_id=$slave->qq($user_id);$name=$slave->qq($name);$email=$slave->qq($email);$phone=$slave->qq($phone);$pass=$slave->qq($pass);$main=$slave->qq($main);$price=$slave->qq($price); $export=$slave->qq($export);
        if ($client_id>0){
            $r=$db->query("select count(id) as kol from A_CLIENTS_USERS where phone='$phone' and id!='$user_id' and status='1';");$phone_ex=$db->result($r,0,"kol");
            $r=$db->query("select count(id) as kol from A_CLIENTS_USERS where email='$email' and id!='$user_id' and status='1';");$email_ex=$db->result($r,0,"kol");
            if ($email_ex>0){
                $answer=0;$err="Вказаний Вами Email належить іншому користувачу";
            }
            if ($phone_ex>0){
                $answer=0;$err="Вказаний Вами телефон належить іншому користувачу";
            }
            if ($email_ex==0 && $phone_ex==0){
                if ($user_id==0 || $user_id==""){
                    $r=$db->query("select max(id) as mid from A_CLIENTS_USERS;");$user_id=0+$db->result($r,0,"mid")+1;
                    $db->query("insert into A_CLIENTS_USERS (id,client_id) values ('$user_id','$client_id');");
                }
                if ($main==1){$db->query("update A_CLIENTS_USERS set `main`='0' where id='$user_id' and client_id='$client_id';");}
                $db->query("update A_CLIENTS_USERS set name='$name', `email`='$email', `phone`='$phone', `pass`='$pass', `main`='$main', `price_status`='$price', `export_status`='$export' where id='$user_id' and client_id='$client_id';");
                $answer=1;$err="";
            }
        }
        return array($answer,$err);
    }

    function dropClientUser($client_id,$user_id){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка збереження даних!";
        $client_id=$slave->qq($client_id);$user_id=$slave->qq($user_id);
        if ($client_id>0 && $user_id>0){
            $db->query("update A_CLIENTS_USERS set status='0' where id='$user_id' and client_id='$client_id';");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function loadClientContacts($client_id){$db=DbSingleton::getDb();
        $form="";$form_htm=RD."/tpl/clients_contacts_list.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select cc.* from A_CLIENTS_CONTACTS cc 
        where cc.client_id='$client_id' order by cc.name asc;");$n=$db->num_rows($r);$list="";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"name");
            $post=$db->result($r,$i-1,"post");
            $con=$this->getContactCon($id);
            $list.="<tr>
                <td>
                    <button class='btn btn-sm btn-default' onClick='showClientContactForm(\"$client_id\",\"$id\");'><i class='fa fa-edit'></i></button>
                    <button class='btn btn-sm btn-default' onClick='dropClientContact(\"$client_id\",\"$id\",\"$name\");'><i class='fa fa-times'></i></button>
                </td>
                <td>$name</td>
                <td>$post</td>
                <td>$con</td>
            </tr>";
        }
        if ($n==0){$list="<tr><td align='center'><h3 class='text-center'>Контакти відсутні</h3></td></tr>";}
        $form=str_replace("{list_contacts}",$list,$form);
        $form=str_replace("{client_id}",$client_id,$form);
        return $form;
    }

    function getContactCon($contact_id){$db=DbSingleton::getDb();$manual=new manual;
        $r=$db->query("select cc.* from A_CLIENTS_CONTACTS_CON cc 
        where cc.contact_id='$contact_id' order by cc.id asc;");$n=$db->num_rows($r);$list="<ul>";
        for ($i=1;$i<=$n;$i++){
            $sotc_cont=$manual->getManualMCaption("sotc_cont",$db->result($r,$i-1,"sotc_cont"));
            $contact_value=$db->result($r,$i-1,"contact_value");
            $list.="<li>$sotc_cont : $contact_value</li>";
        }$list.="</ul>";
        return $list;
    }

    function getContactConForm($contact_id){$db=DbSingleton::getDb();$manual=new manual;
        $r=$db->query("select cc.* from A_CLIENTS_CONTACTS_CON cc 
        where cc.contact_id='$contact_id' order by cc.id asc;");$n=$db->num_rows($r);$list="";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $sotc_cont=$db->result($r,$i-1,"sotc_cont");
            $contact_value=$db->result($r,$i-1,"contact_value");
            $list.="<div class='row'>
                <div class='col-sm-1'>$i<input type='hidden' id='con_id_$i' value='$id'></div>
                <div class='col-sm-4'><select class='form-control' size=1 id='sotc_cont_$i'><option value='0'></option>".$manual->showManualSelectList("sotc_cont",$sotc_cont)."</select></div>
                <div class='col-sm-7'><input type='text' class='form-control' id='contact_value_$i' value='$contact_value'></div>
            </div>";
        }
        for ($i=$n+1;$i<=$n+3;$i++){
            $list.="<div class='row'>
                <div class='col-sm-1'>$i<input type='hidden' id='con_id_$i' value='0'></div>
                <div class='col-sm-4'><select class='form-control' size=1 id='sotc_cont_$i'><option value='0'></option>".$manual->showManualSelectList("sotc_cont",0)."</select></div>
                <div class='col-sm-7'><input type='text' class='form-control' id='contact_value_$i' value=''></div>
            </div>";
        }
        $list.="<input type='hidden' id='contact_con_kol' value='".($n+3)."'>";
        return $list;
    }

    function showClientContactForm($client_id,$contact_id){$db=DbSingleton::getDb();
        $form="";$form_htm=RD."/tpl/clients_contacts_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select * from A_CLIENTS_CONTACTS where id='$contact_id' limit 0,1;");
        $contact_id=$db->result($r,0,"id");
        $name=$db->result($r,0,"name");
        $post=$db->result($r,0,"post");
        $form=str_replace("{client_id}",$client_id,$form);
        $form=str_replace("{contact_id}",$contact_id,$form);
        $form=str_replace("{contact_name}",$name,$form);
        $form=str_replace("{contact_post}",$post,$form);
        $form=str_replace("{con_list}",$this->getContactConForm($contact_id),$form);
        return $form;
    }

    function saveClientContactForm($client_id,$contact_id,$contact_name,$contact_post,$contact_con_kol,$con_id,$sotc_cont,$contact_value){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка збереження даних!";
        $client_id=$slave->qq($client_id);$contact_id=$slave->qq($contact_id);$contact_name=$slave->qq($contact_name);$contact_post=$slave->qq($contact_post);$contact_con_kol=$slave->qq($contact_con_kol);
        if ($client_id>0){
            if ($contact_id==0 || $contact_id==""){
                $r=$db->query("select max(id) as mid from A_CLIENTS_CONTACTS;");$contact_id=0+$db->result($r,0,"mid")+1;
                $db->query("insert into A_CLIENTS_CONTACTS (id,client_id) values ('$contact_id','$client_id');");
            }
            $db->query("update A_CLIENTS_CONTACTS set name='$contact_name', `post`='$contact_post' where id='$contact_id' and client_id='$client_id';");
            for ($i=1;$i<=$contact_con_kol;$i++){
                $c_id=$con_id[$i];
                $c_sotc_cont=$sotc_cont[$i];
                $c_contact_value=$contact_value[$i];
                if ($c_id>0  && $c_contact_value==""){ $db->query("delete from A_CLIENTS_CONTACTS_CON where id='$c_id';"); }
                if ($c_id>0  && $c_sotc_cont>0 && $c_contact_value!=""){ $db->query("update A_CLIENTS_CONTACTS_CON set sotc_cont='$c_sotc_cont', contact_value='$c_contact_value' where id='$c_id';"); }
                if ($c_id==0  && $c_sotc_cont>0 && $c_contact_value!=""){ $db->query("insert into A_CLIENTS_CONTACTS_CON (`contact_id`,`sotc_cont`,`contact_value`) values ('$contact_id','$c_sotc_cont','$c_contact_value');"); }
            }
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function dropClientContact($client_id,$contact_id){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка збереження даних!";
        $client_id=$slave->qq($client_id);$contact_id=$slave->qq($contact_id);
        if ($client_id>0 && $contact_id>0){
            $db->query("delete from A_CLIENTS_CONTACTS where id='$contact_id' and client_id='$client_id';");
            $db->query("delete from A_CLIENTS_CONTACTS_CON where contact_id='$contact_id';");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function loadClientCommets($client_id){$db=DbSingleton::getDb();
        $form="";$form_htm=RD."/tpl/clients_comment_block.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select cc.*, u.name from A_CLIENTS_COMMENTS cc 
            left outer join media_users u on u.id=cc.USER_ID 
        where cc.CLIENT_ID='$client_id' order by cc.id desc;");$n=$db->num_rows($r);$list="";

        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"ID");
            $user_id=$db->result($r,$i-1,"USER_ID");
            $user_name=$db->result($r,$i-1,"name");
            $data=$db->result($r,$i-1,"DATA");
            $comment=$db->result($r,$i-1,"COMMENT");
            $block=$form;
            $block=str_replace("{client_id}",$client_id,$block);
            $block=str_replace("{id}",$id,$block);
            $block=str_replace("{user_id}",$user_id,$block);
            $block=str_replace("{user_name}",$user_name,$block);
            $block=str_replace("{data}",$data,$block);
            $block=str_replace("{comment}",$comment,$block);
            $list.=$block;
        }
        if ($n==0){$list="<h3 class='text-center'>Коментарі відсутні</h3>";}
        return $list;
    }

    function saveClientComment($client_id,$comment){$db=DbSingleton::getDb();$slave=new slave;session_start();$user_id=$_SESSION["media_user_id"];$answer=0;$err="Помилка збереження даних!";
        $client_id=$slave->qq($client_id);$comment=$slave->qq($comment);
        if ($client_id>0 && $comment!=""){
            $db->query("insert into A_CLIENTS_COMMENTS (`CLIENT_ID`,`USER_ID`,`COMMENT`) values ('$client_id','$user_id','$comment');");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function dropClientComment($client_id,$comment_id){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка видалення запису!";
        $client_id=$slave->qq($client_id);$comment_id=$slave->qq($comment_id);
        if ($client_id>0 && $comment_id>0){
            $r=$db->query("select * from A_CLIENTS_COMMENTS where CLIENT_ID='$client_id' and ID='$comment_id' limit 0,1;");$n=$db->num_rows($r);
            if ($n==1){
                $db->query("delete from A_CLIENTS_COMMENTS where CLIENT_ID='$client_id' and ID='$comment_id';");
                $answer=1;$err="";
            }
        }
        return array($answer,$err);
    }

    function loadClientsDetailsFile($client_id,$file_type){$db=DbSingleton::getDb();
        $form="";$form_htm=RD."/tpl/clients_details_file.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select cd.*, u.name as user_name from A_CLIENTS_DTLS `cd` 
            left outer join media_users u on u.id=`cd`.USER_ID 
        where cd.CLIENT_ID='$client_id' and cd.FILE_TYPE='$file_type' and cd.STATUS='1' order by cd.FILE_NAME asc;");$n=$db->num_rows($r);$list="";

        for ($i=1;$i<=$n;$i++){
            $file_id=$db->result($r,$i-1,"ID");
            $file_name=$db->result($r,$i-1,"FILE_NAME");
            $name=$db->result($r,$i-1,"NAME");
            $data=$db->result($r,$i-1,"DATA");
            $user_name=$db->result($r,$i-1,"user_name");
            $link="https://portal.myparts.pro/cdn/clfiles/$client_id/$file_name";
            $file_view="<div class=\"icon\"><i class=\"fa fa-file\"></i></div>";
            $exten=pathinfo($file_name, PATHINFO_EXTENSION);
            if ($exten=="jpg" || $exten=="jpeg" || $exten=="png" || $exten=="gif" || $exten=="bmp" || $exten=="svg"){
                $file_view="<div class=\"image\"><img alt=\"image\" class=\"img-responsive\" src=\"$link\"></div>";
            }
            $block=$form;
            $block=str_replace("{file_id}",$file_id,$block);
            $block=str_replace("{file_type}",$file_type,$block);
            $block=str_replace("{file_name}",$name,$block);
            $block=str_replace("{user_name}",$user_name,$block);
            $block=str_replace("{data}",$data,$block);
            $block=str_replace("{client_id}",$client_id,$block);
            $block=str_replace("{link}",$link,$block);
            $block=str_replace("{file_view}",$file_view,$block);
            $list.=$block;
        }
        if ($n==0){$list="<h3 class='text-center'>Файли відсутні</h3>";}
        return $list;
    }

    function clientsDetailsDropFile($client_id,$file_type,$file_id){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка видалення файлу!";
        $client_id=$slave->qq($client_id);$file_id=$slave->qq($file_id);
        if ($client_id>0 && $file_id>0 && $file_type!=""){
            $r=$db->query("select FILE_NAME from A_CLIENTS_DTLS 
            where CLIENT_ID='$client_id' and FILE_TYPE='$file_type' and ID='$file_id' limit 0,1;");$n=$db->num_rows($r);
            if ($n==1){
                unlink(RD.'/cdn/clfiles/$client_id/$file_name');
                $db->query("delete from A_CLIENTS_DTLS where CLIENT_ID='$client_id' and FILE_TYPE='$file_type' and ID='$file_id';");
                $answer=1;$err="";
            }
        }
        return array($answer,$err);
    }

    function loadClientCDN($client_id){$db=DbSingleton::getDb();
        $form="";$form_htm=RD."/tpl/clients_cdn_block.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select cc.*, u.name as user_name from A_CLIENTS_CDN cc 
            left outer join media_users u on u.id=cc.USER_ID 
        where cc.CLIENT_ID='$client_id' and cc.STATUS='1' order by cc.FILE_NAME asc;");$n=$db->num_rows($r);$list="";

        for ($i=1;$i<=$n;$i++){
            $file_id=$db->result($r,$i-1,"ID");
            $file_name=$db->result($r,$i-1,"FILE_NAME");
            $name=$db->result($r,$i-1,"NAME");
            $data=$db->result($r,$i-1,"DATA");
            $user_name=$db->result($r,$i-1,"user_name");
            $link="https://portal.myparts.pro/cdn/clfiles/$client_id/$file_name";
            $file_view="<div class=\"icon\"><i class=\"fa fa-file\"></i></div>";
            $exten=pathinfo($file_name, PATHINFO_EXTENSION);
            if ($exten=="jpg" || $exten=="jpeg" || $exten=="png" || $exten=="gif" || $exten=="bmp" || $exten=="svg"){
                $file_view="<div class=\"image\"><img alt=\"image\" class=\"img-responsive\" src=\"$link\"></div>";
            }
            $block=$form;
            $block=str_replace("{file_id}",$file_id,$block);
            $block=str_replace("{file_name}",$name,$block);
            $block=str_replace("{user_name}",$user_name,$block);
            $block=str_replace("{data}",$data,$block);
            $block=str_replace("{client_id}",$client_id,$block);
            $block=str_replace("{link}",$link,$block);
            $block=str_replace("{file_view}",$file_view,$block);
            $list.=$block;
        }
        if ($n==0){$list="<h3 class='text-center'>Файли відсутні</h3>";}
        return $list;
    }

    function clientsCDNDropFile($client_id,$file_id){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка видалення файлу!";
        $client_id=$slave->qq($client_id);$file_id=$slave->qq($file_id);
        if ($client_id>0 && $file_id>0){
            $r=$db->query("select FILE_NAME from A_CLIENTS_CDN where CLIENT_ID='$client_id' and ID='$file_id' limit 0,1;");$n=$db->num_rows($r);
            if ($n==1){
                unlink(RD.'/cdn/clfiles/$client_id/$file_name');
                $db->query("delete from A_CLIENTS_CDN where CLIENT_ID='$client_id' and ID='$file_id';");
                $answer=1;$err="";
            }
        }
        return array($answer,$err);
    }

    function loadArticleFoto($client_id){$db=DbSingleton::getDb();
        $form="";$form_htm=RD."/tpl/clients_foto_block.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select t2af.*, u.name as user_name from T2_PHOTOS t2af
            left outer join media_users u on u.id=t2af.USER_ID 
        where t2af.ART_ID='$client_id' order by t2af.PHOTO_NAME asc;");$n=$db->num_rows($r);$list="";

        for ($i=1;$i<=$n;$i++){
            $file_id=$db->result($r,$i-1,"ID");
            $file_name=$db->result($r,$i-1,"PHOTO_NAME");
            $data=$db->result($r,$i-1,"DATA");
            $user_name=$db->result($r,$i-1,"user_name");
            $main=$db->result($r,$i-1,"MAIN");
            $main_v="<a class=\"btn btn-xs btn-white\" onClick=\"setArticlesFotoMain('$client_id','$file_id')\"><i class=\"fa fa-check\"></i> Основне фото</a>";
            if ($main==1){$main_v=" <span class=\"btn btn-xs label-primary\"><i class=\"fa fa-check\"></i> Основне фото</span>";}
            $link="https://portal.myparts.pro/cdn/artfoto/$file_name";
            $block=$form;
            $block=str_replace("{file_id}",$file_id,$block);
            $block=str_replace("{foto_name}",$file_name,$block);
            $block=str_replace("{file_name}",$file_name,$block);
            $block=str_replace("{user_name}",$user_name,$block);
            $block=str_replace("{data}",$data,$block);
            $block=str_replace("{client_id}",$client_id,$block);
            $block=str_replace("{link}",$link,$block);
            $block=str_replace("{main}",$main_v,$block);
            $list.=$block;
        }
        if ($n==0){$list="<h3 class='text-center'>Фото відсутні</h3>";}
        return $list;
    }

    function setArticlesFotoMain($client_id,$file_id){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка обробки запиту!";
        $client_id=$slave->qq($client_id);$file_id=$slave->qq($file_id);
        if ($client_id>0 && $file_id>0){
            $db->query("update T2_PHOTOS set MAIN='0' where ART_ID='$client_id' and MAIN='1';");
            $db->query("update T2_PHOTOS set MAIN='1' where ART_ID='$client_id' and ID='$file_id';");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function articlesFotoDropFile($client_id,$file_id){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка видалення файлу!";
        $client_id=$slave->qq($client_id);$file_id=$slave->qq($file_id);
        if ($client_id>0 && $file_id>0){
            $r=$db->query("select PHOTO_NAME from T2_PHOTOS where ART_ID='$client_id' and ID='$file_id' limit 0,1;");$n=$db->num_rows($r);
            if ($n==1){
                unlink(RD.'/cdn/artfoto/$PHOTO_NAME');
                $db->query("delete from T2_PHOTOS where ART_ID='$client_id' and ID='$file_id';");
                $answer=1;$err="";
            }
        }
        return array($answer,$err);
    }

    function getClientGeneralSaldo($sel_id){$db=DbSingleton::getDb();$saldo="0 грн";
        $r=$db->query("select `saldo`, cash_id from B_CLIENT_BALANS where client_id='$sel_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $saldo=$db->result($r,0,"saldo");
            $cash_id=$db->result($r,0,"cash_id");
            $cash_abr=$this->getCashAbr($cash_id);
            $saldo.=" $cash_abr";
        }
        return $saldo;
    }

    function getCashAbr($sel_id){$db=DbSingleton::getDb();$name="грн";
        $r=$db->query("select abr from CASH where id='$sel_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$name=$db->result($r,0,"abr");}
        return $name;
    }

    function getClientNameById($sel_id, $field="name"){$db=DbSingleton::getDb();$name="";
        $r=$db->query("select `$field` from A_CLIENTS where id='$sel_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$name=$db->result($r,0,"$field");}
        return $name;
    }

    function getUserNameById($sel_id, $field="name"){$db=DbSingleton::getDb();$name="";
        $r=$db->query("select `$field` from A_CLIENTS_USERS where id='$sel_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$name=$db->result($r,0,"$field");}
        return $name;
    }

    function loadStateSelectList($country_id,$sel_id){$slave=new slave;
        $list=$slave->showSelectSubList("T2_STATE","COUNTRY_ID","$country_id","STATE_ID","STATE_NAME",$sel_id);
        return $list;
    }

    function loadRegionSelectList($state_id,$sel_id){$slave=new slave;
        $list=$slave->showSelectSubList("T2_REGION","STATE_ID","$state_id","REGION_ID","REGION_NAME",$sel_id);
        return $list;
    }

    function loadCitySelectList($region_id,$sel_id){$db=DbSingleton::getDb();$slave=new slave;
        $r=$db->query("select * from T2_CITY where REGION_ID='$region_id' order by CITY_NAME asc;");$n=$db->num_rows($r);$list="";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"CITY_ID");
            $name=$db->result($r,$i-1,"CITY_NAME");
            $sel="";if ($sel_id==$id){$sel="selected='selected'";}
            $list.="<option value='$id' $sel>$name</option>";
        }
        return $slave->showSelectSubListDBM("T2_CITY","REGION_ID","$region_id","CITY_ID","CITY_NAME",$sel_id);
    //	return "<option value='NEW'>Добавити населений пункт</option>".$slave->showSelectSubListDBM("T2_CITY","REGION_ID","$region_id","CITY_ID","CITY_NAME",$sel_id);
    }

    function showCategoryCheckList($client_id){$db=DbSingleton::getDb();$list="";
        $r=$db->query("select * from A_CATEGORY where parrent_id=0 order by id asc;");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){ $client_disabled="";
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"name");
            $sel=$this->checkClientCategorySelect($client_id,$id);
            $ch="";if ($sel==1){$ch=" checked=''";}
            if ($i==1) { ($this->checkSaleInvoiceClients($client_id)) || ($this->checkJPayClients($client_id)) ? $client_disabled="disabled" : $client_disabled="";
            }
            $list.="<label><input $client_disabled type='checkbox' class='i-checks' id='c_category_$i' value='$id' $ch> - $name;</label> ";
        }$list.="<input type='hidden' id='c_category_kol' value='$n'>";
        return $list;
    }

    function checkClientCategorySelect($client_id,$category_id){$db=DbSingleton::getDb();$ch=0;
        $r=$db->query("select category_id from A_CLIENTS_CATEGORY where client_id='$client_id' and category_id='$category_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$ch=1;}
        return $ch;
    }

    function showTpointListSelect($sel_id){$db=DbSingleton::getDb();$list="<option value='0'>-- Оберіть зі списку --</option>";
        $r=$db->query("select * from T_POINT order by name asc;");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"name");
            $sel="";if ($sel_id==$id){$sel="selected='selected'";}
            $list.="<option value='$id' $sel>$name</option>";
        }
        return $list;
    }

    function showPriceLvlListSelect($sel_id){$list="";
        for ($i=0;$i<=200;$i++){
            $sel="";if ($sel_id==$i){$sel="selected='selected'";}
            $list.="<option value='$i' $sel>$i</option>";
        }
        return $list;
    }

    function showCashListSelect($sel_id,$ns){$db=DbSingleton::getDb();if ($ns==""){$ns=1;}
        $r=$db->query("select * from CASH order by name asc;");$n=$db->num_rows($r);$list="";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"abr");
            if ($ns==2){ $name=$db->result($r,$i-1,"name");}
            $sel="";if ($sel_id==$id){$sel="selected='selected'";}
            $list.="<option value='$id' $sel>$name</option>";
        }
        return $list;
    }

    function getCashDataArray(){$db=DbSingleton::getDb();$dat=array();
        $r=$db->query("select * from CASH order by name asc;");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");$dat[$i]["id"]=$id;
            $abr=$db->result($r,$i-1,"abr");$dat[$i]["abr"]=$abr;
            $abr2=$db->result($r,$i-1,"abr2");$dat[$i]["abr2"]=$abr2;
            $name=$db->result($r,$i-1,"name");$dat[$i]["name"]=$name;
        }
        return $dat;
    }

    function showGoodGroupTree($sel_id){$db=DbSingleton::getDb();$tree="";
        $form="";$form_htm=RD."/tpl/clients_goods_group_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select * from GOODS_GROUP where PARRENT_ID='0' order by NAME asc;");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"ID");
            $name=$db->result($r,$i-1,"NAME");
            $sel="";if ($sel_id==$id){$sel=" data-jstree='{\"selected\":true}'";}
            $tree.="<li id='$id' $sel>$name".$this->showGoodGroupSubLevel($id,$sel_id)."</li>";
        }
        $form=str_replace("{tree}",$tree,$form);
        $form=str_replace("{goods_group_id}",$sel_id,$form);
        return $form;
    }

    function showGoodGroupSubLevel($parrent_id,$sel_id){$db=DbSingleton::getDb();$tree="";
        $r=$db->query("select * from GOODS_GROUP where PARRENT_ID='$parrent_id' order by NAME asc;");$n=$db->num_rows($r);
        if ($n>0){$tree.="<ul>";
            for ($i=1;$i<=$n;$i++){
                $id=$db->result($r,$i-1,"ID");
                $name=$db->result($r,$i-1,"NAME");
                $sel="";if ($sel_id==$id){$sel=" data-jstree='{\"selected\":true}'";}
                $tree.="<li id='$id' $sel>$name".$this->showGoodGroupSubLevel($id,$sel_id)."</li>";
            }
            $tree.="</ul>";
        }
        return $tree;
    }

    function loadClientSupplConditions($client_id){$db=DbSingleton::getDb();$gmanual=new gmanual;
        $form="";$form_htm=RD."/tpl/clients_suppl_conditions.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select * from A_CLIENTS_SUPPL_CONDITIONS where client_id='$client_id' limit 0,1;");

        $prepayment=$db->result($r,0,"prepayment"); $prepayment_checked=""; if ($prepayment==1){$prepayment_checked=" checked='checked'";}
        $prepay_all=$db->result($r,0,"prepay_all"); $prepay_all_checked=""; $prepay_summ_disabled=""; if ($prepay_all==1){$prepay_all_checked=" checked='checked'"; $prepay_summ_disabled="readonly";}
        $prepay_summ=$db->result($r,0,"prepay_summ");
        $prepay_type=$db->result($r,0,"prepay_type");$prepay_persent_readonly=""; if ($prepay_type==65){$prepay_persent_readonly=" readonly";}
        $prepay_persent=$db->result($r,0,"prepay_persent");
        $form=str_replace("{client_id}",$client_id,$form);
        $form=str_replace("{prepayment_checked}",$prepayment_checked,$form);
        $form=str_replace("{prepay_all_checked}",$prepay_all_checked,$form);
        $form=str_replace("{prepay_summ_disabled}",$prepay_summ_disabled,$form);
        $form=str_replace("{prepay_summ}",$prepay_summ,$form);
        $form=str_replace("{prepay_type_list}",$gmanual->showGmanualSelectList("prepay_type",$prepay_type),$form);
        $form=str_replace("{prepay_persent_readonly}",$prepay_persent_readonly,$form);
        $form=str_replace("{prepay_persent}",$prepay_persent,$form);
        return $form;
    }

    function saveClientSupplConditions($client_id,$prepayment,$prepay_all,$prepay_summ,$prepay_type,$prepay_persent){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка збереження даних!";
        $client_id=$slave->qq($client_id);$prepayment=$slave->qq($prepayment);$prepay_all=$slave->qq($prepay_all);$prepay_summ=$slave->qq($prepay_summ);$prepay_type=$slave->qq($prepay_type);$prepay_persent=$slave->qq($prepay_persent);
        if ($client_id>0){
            $r=$db->query("select * from `A_CLIENTS_SUPPL_CONDITIONS` where `client_id`='$client_id' limit 0,1;");$n=$db->num_rows($r);
            if ($n==0){
                $db->query("insert into `A_CLIENTS_SUPPL_CONDITIONS` (`client_id`,`prepayment`,`prepay_all`,`prepay_summ`,`prepay_type`,`prepay_persent`) values ('$client_id', '$prepayment','$prepay_all','$prepay_summ','$prepay_type','$prepay_persent');");
            }
            if ($n==1){
                $db->query("update `A_CLIENTS_SUPPL_CONDITIONS` set `prepayment`='$prepayment', `prepay_all`='$prepay_all', `prepay_summ`='$prepay_summ', `prepay_type`='$prepay_type', `prepay_persent`='$prepay_persent' where `client_id`='$client_id';");
            }
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function getDocTypeSelectList($sel_id){$db=DbSingleton::getDb();$list="<option value=0>Оберіть зі списку</option>";
        $r=$db->query("select id, mcaption from `manual` where ison='1' and `key`='client_sale_type' order by mid, id asc;");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"mcaption");
            $sel="";if ($sel_id==$id){$sel="selected='selected'";}
            $list.="<option value='$id' $sel>$name</option>";
        }
        return $list;
    }

    function loadClientConditions($client_id){$db=DbSingleton::getDb();
        $form="";$form_htm=RD."/tpl/clients_conditions.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select * from A_CLIENTS_CONDITIONS where client_id='$client_id' limit 0,1;");

        $cash_id=$db->result($r,0,"cash_id");
        $country_cash_id=$db->result($r,0,"country_cash_id");
        $credit_cash_id=$db->result($r,0,"credit_cash_id");
        $payment_delay=$db->result($r,0,"payment_delay");
        $credit_limit=$db->result($r,0,"credit_limit");
        $credit_return=$db->result($r,0,"credit_return");
        $price_lvl=$db->result($r,0,"price_lvl");
        $margin_price_lvl=$db->result($r,0,"margin_price_lvl");
        $price_suppl_lvl=$db->result($r,0,"price_suppl_lvl");
        $margin_price_suppl_lvl=$db->result($r,0,"margin_price_suppl_lvl");
        $tpoint_id=$db->result($r,0,"tpoint_id");
        $client_vat=$db->result($r,0,"client_vat");$client_vat_checked=""; if ($client_vat==1){$client_vat_checked=" checked='checked'";}
        $doc_type_id=$db->result($r,0,"doc_type_id");

        $form=str_replace("{client_id}",$client_id,$form);
        $form=str_replace("{payment_delay}",$payment_delay,$form);
        $form=str_replace("{credit_limit}",$credit_limit,$form);
        $form=str_replace("{credit_return}",$credit_return,$form);
        $form=str_replace("{cash_list}",$this->showCashListSelect($cash_id,1),$form);
        $form=str_replace("{country_cash_list}",$this->showCashListSelect($country_cash_id,1),$form);
        $form=str_replace("{credit_cash_list}",$this->showCashListSelect($credit_cash_id,1),$form);
        $form=str_replace("{price_lvl_list}",$this->showPriceLvlListSelect($price_lvl),$form);
        $form=str_replace("{margin_price_lvl}",$margin_price_lvl,$form);
        $form=str_replace("{price_suppl_lvl_list}",$this->showPriceLvlListSelect($price_suppl_lvl),$form);
        $form=str_replace("{margin_price_suppl_lvl}",$margin_price_suppl_lvl,$form);
        $form=str_replace("{tpoint_list}",$this->showTpointListSelect($tpoint_id),$form);
        $form=str_replace("{client_vat_checked}",$client_vat_checked,$form);
        $form=str_replace("{doc_type_list}",$this->getDocTypeSelectList($doc_type_id),$form);

        ($this->checkSaleInvoiceClients($client_id)) || ($this->checkJPayClients($client_id)) ? $form=str_replace("{cash_disabled}","disabled",$form) : $form=str_replace("{cash_disabled}","",$form);
        return $form;
    }


    function saveClientConditions($client_id,$cash_id,$country_cash_id,$price_lvl,$margin_price_lvl,$price_suppl_lvl,$margin_price_suppl_lvl,$tpoint_id,$client_vat,$payment_delay,$credit_limit,$credit_cash_id,$credit_return,$doc_type_id){$db=DbSingleton::getDb();$slave=new slave;session_start();$user_id=$_SESSION["media_user_id"];$answer=0;$err="Помилка збереження даних!";
        $client_id=$slave->qq($client_id);$cash_id=$slave->qq($cash_id);$country_cash_id=$slave->qq($country_cash_id);$price_lvl=$slave->qq($price_lvl);
        $margin_price_lvl=$slave->qq($margin_price_lvl);$price_suppl_lvl=$slave->qq($price_suppl_lvl);$margin_price_suppl_lvl=$slave->qq($margin_price_suppl_lvl);
        $tpoint_id=$slave->qq($tpoint_id);$client_vat=$slave->qq($client_vat);$payment_delay=$slave->qq($payment_delay);$credit_limit=$slave->qq($slave->point_valid($credit_limit));
        $credit_cash_id=$slave->qq($credit_cash_id);$credit_return=$slave->qq($credit_return);$doc_type_id=$slave->qq($doc_type_id);

        if ($client_id>0){
            //T2_PACKAGING UPDATE
            $r=$db->query("select * from `A_CLIENTS_CONDITIONS` where `client_id`='$client_id' limit 0,1;");$n=$db->num_rows($r);
            if ($n==0){
                $db->query("insert into `A_CLIENTS_CONDITIONS` (`client_id`,`cash_id`,`country_cash_id`,`price_lvl`,`margin_price_lvl`,`price_suppl_lvl`,`margin_price_suppl_lvl`,`tpoint_id`,`client_vat`,`payment_delay`,`credit_limit`,`credit_cash_id`,`credit_return`,`doc_type_id`) values ('$client_id','$cash_id','$country_cash_id','$price_lvl','$margin_price_lvl','$price_suppl_lvl','$margin_price_suppl_lvl','$tpoint_id','$client_vat','$payment_delay','$credit_limit','$credit_cash_id','$credit_return','$doc_type_id');");
            }
            if ($n==1){
                $db->query("update `A_CLIENTS_CONDITIONS` set `cash_id`='$cash_id', `country_cash_id`='$country_cash_id', `price_lvl`='$price_lvl', `margin_price_lvl`='$margin_price_lvl', `price_suppl_lvl`='$price_suppl_lvl', `margin_price_suppl_lvl`='$margin_price_suppl_lvl', `tpoint_id`='$tpoint_id', `client_vat`='$client_vat',`payment_delay`='$payment_delay', `credit_limit`='$credit_limit', `credit_cash_id`='$credit_cash_id', `credit_return`='$credit_return', `doc_type_id`='$doc_type_id' where `client_id`='$client_id';");

                $db->query("insert into `A_CLIENTS_CONDITIONS_HISTORY` (`client_id`,`cash_id`,`country_cash_id`,`price_lvl`,`margin_price_lvl`,`price_suppl_lvl`,`margin_price_suppl_lvl`,`tpoint_id`,`client_vat`,`payment_delay`,`credit_limit`,`credit_cash_id`,`credit_return`,`doc_type_id`,`user_id`) values ('$client_id','$cash_id','$country_cash_id','$price_lvl','$margin_price_lvl','$price_suppl_lvl','$margin_price_suppl_lvl','$tpoint_id','$client_vat','$payment_delay','$credit_limit','$credit_cash_id','$credit_return','$doc_type_id','$user_id');");
            }
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function showClientConditionsHistory($client_id) {$db=DbSingleton::getDb(); $list="";
        $r=$db->query("select * from A_CLIENTS_CONDITIONS where client_id='$client_id';");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $cash_id=$db->result($r,$i-1,"cash_id"); $cash_id=$this->getCashName($cash_id);
            $country_cash_id=$db->result($r,$i-1,"country_cash_id"); $country_cash_id=$this->getCashName($country_cash_id);
            $credit_cash_id=$db->result($r,$i-1,"credit_cash_id"); $credit_cash_id=$this->getCashName($credit_cash_id);
            $payment_delay=$db->result($r,$i-1,"payment_delay");
            $credit_limit=$db->result($r,$i-1,"credit_limit");
            $credit_return=$db->result($r,$i-1,"credit_return");
            $price_lvl=$db->result($r,$i-1,"price_lvl");
            $margin_price_lvl=$db->result($r,$i-1,"margin_price_lvl");
            $price_suppl_lvl=$db->result($r,$i-1,"price_suppl_lvl");
            $margin_price_suppl_lvl=$db->result($r,$i-1,"margin_price_suppl_lvl");
            $tpoint_id=$db->result($r,$i-1,"tpoint_id"); $tpoint_id=$this->getTpointName($tpoint_id);
            $client_vat=$db->result($r,$i-1,"client_vat");
            $doc_type_id=$db->result($r,$i-1,"doc_type_id"); $doc_type_id=$this->getDocTypeName($doc_type_id);
            $list.="<tr style='background:pink;'>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>$cash_id</td>
                <td>$country_cash_id</td>
                <td>$credit_cash_id</td>
                <td>$price_lvl</td>
                <td>$margin_price_lvl</td>
                <td>$payment_delay</td>
                <td>$credit_limit</td>
                <td>$price_suppl_lvl</td>
                <td>$margin_price_suppl_lvl</td>
                <td>$credit_return</td>
                <td>$tpoint_id</td>
                <td>$client_vat</td>
                <td>$doc_type_id</td>
            </tr>";
        }

        $r=$db->query("select * from A_CLIENTS_CONDITIONS_HISTORY where client_id='$client_id' order by data desc;");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $data=$db->result($r,$i-1,"data");
            $user_id=$db->result($r,$i-1,"user_id"); $user_id=$this->getMediaUserName($user_id);
            $cash_id=$db->result($r,$i-1,"cash_id"); $cash_id=$this->getCashName($cash_id);
            $country_cash_id=$db->result($r,$i-1,"country_cash_id"); $country_cash_id=$this->getCashName($country_cash_id);
            $credit_cash_id=$db->result($r,$i-1,"credit_cash_id"); $credit_cash_id=$this->getCashName($credit_cash_id);
            $payment_delay=$db->result($r,$i-1,"payment_delay");
            $credit_limit=$db->result($r,$i-1,"credit_limit");
            $credit_return=$db->result($r,$i-1,"credit_return");
            $price_lvl=$db->result($r,$i-1,"price_lvl");
            $margin_price_lvl=$db->result($r,$i-1,"margin_price_lvl");
            $price_suppl_lvl=$db->result($r,$i-1,"price_suppl_lvl");
            $margin_price_suppl_lvl=$db->result($r,$i-1,"margin_price_suppl_lvl");
            $tpoint_id=$db->result($r,$i-1,"tpoint_id"); $tpoint_id=$this->getTpointName($tpoint_id);
            $client_vat=$db->result($r,$i-1,"client_vat");
            $doc_type_id=$db->result($r,$i-1,"doc_type_id"); $doc_type_id=$this->getDocTypeName($doc_type_id);
            if ($i==1) $color="style='background:lightgreen;'"; else $color="";
            $list.="<tr $color>
                <td>$i</td>
                <td>$data</td>
                <td>$user_id</td>
                <td>$cash_id</td>
                <td>$country_cash_id</td>
                <td>$credit_cash_id</td>
                <td>$price_lvl</td>
                <td>$margin_price_lvl</td>
                <td>$payment_delay</td>
                <td>$credit_limit</td>
                <td>$price_suppl_lvl</td>
                <td>$margin_price_suppl_lvl</td>
                <td>$credit_return</td>
                <td>$tpoint_id</td>
                <td>$client_vat</td>
                <td>$doc_type_id</td>
            </tr>";
        }
        $form="";$form_htm=RD."/tpl/clients_conditions_history.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $form=str_replace("{client_conditions_history_range}",$list,$form);
        return $form;
    }

    function getDocTypeName($doc_type){$db=DbSingleton::getDb();
        $r=$db->query("select mcaption from manual where id='$doc_type' limit 1;");
        $mcaption=$db->result($r,0,"mcaption");
        return $mcaption;
    }

    function getTpointName($tpoint_id){$db=DbSingleton::getDb();
        $r=$db->query("select name from T_POINT where id='$tpoint_id' limit 1;");
        $name=$db->result($r,0,"name");
        return $name;
    }

    function getCashName($cash_id){$db=DbSingleton::getDb();
        $r=$db->query("select abr from CASH where id='$cash_id' limit 1;");
        $name=$db->result($r,0,"abr");
        return $name;
    }

    function getMediaUserName($user_id){$db=DbSingleton::getDb();
        $r=$db->query("select name from media_users where id='$user_id' limit 1;");
        $name=$db->result($r,0,"name");
        return $name;
    }

    function loadClientDetails($client_id){$db=DbSingleton::getDb();
        $form="";$form_htm=RD."/tpl/clients_details.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select * from A_CLIENT_DETAILS where client_id='$client_id' limit 0,1;");

        $address_jur=$db->result($r,0,"address_jur");
        $address_fakt=$db->result($r,0,"address_fakt");
        $edrpou=$db->result($r,0,"edrpou");
        $svidotctvo=$db->result($r,0,"svidotctvo");
        $vytjag=$db->result($r,0,"vytjag");
        $vat=$db->result($r,0,"vat");
        $bank=$db->result($r,0,"bank");
        $mfo=$db->result($r,0,"mfo");
        $account=$db->result($r,0,"account");
        $not_resident=$db->result($r,0,"not_resident");$not_resident_ch="";$nr_details_disabled=" disabled='disabled'";
        if ($not_resident==1){$not_resident_ch=" checked='checked'";$nr_details_disabled="";}
        $nr_details=$db->result($r,0,"nr_details");
        $buh_name=$db->result($r,0,"buh_name");
        $buh_edrpou=$db->result($r,0,"buh_edrpou");

        $form=str_replace("{client_id}",$client_id,$form);
        $form=str_replace("{address_jur}",$address_jur,$form);
        $form=str_replace("{address_fakt}",$address_fakt,$form);
        $form=str_replace("{edrpou}",$edrpou,$form);
        $form=str_replace("{svidotctvo}",$svidotctvo,$form);
        $form=str_replace("{vytjag}",$vytjag,$form);
        $form=str_replace("{vat}",$vat,$form);
        $form=str_replace("{bank}",$bank,$form);
        $form=str_replace("{mfo}",$mfo,$form);
        $form=str_replace("{account}",$account,$form);
        $form=str_replace("{not_resident_checked}",$not_resident_ch,$form);
        $form=str_replace("{nr_details}",$nr_details,$form);
        $form=str_replace("{nr_details_disabled}",$nr_details_disabled,$form);
        $form=str_replace("{buh_name}",$buh_name,$form);
        $form=str_replace("{buh_edrpou}",$buh_edrpou,$form);
        return $form;
    }

    function saveClientDetails($client_id,$address_jur,$address_fakt,$edrpou,$svidotctvo,$vytjag,$vat,$mfo,$bank,$account,$not_resident,$nr_details,$buh_name,$buh_edrpou){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка збереження даних!";
        $client_id=$slave->qq($client_id);$address_jur=$slave->qq($address_jur);$address_fakt=$slave->qq($address_fakt);$edrpou=$slave->qq($edrpou);$svidotctvo=$slave->qq($svidotctvo);$vytjag=$slave->qq($vytjag);$vat=$slave->qq($vat);$mfo=$slave->qq($mfo);$bank=$slave->qq($bank);$account=$slave->qq($account);$not_resident=$slave->qq($not_resident);$nr_details=$slave->qq($nr_details);
        $buh_name=$slave->qq($buh_name);$buh_edrpou=$slave->qq($buh_edrpou);
        if ($client_id>0){
            //T2_PACKAGING UPDATE
            $r=$db->query("select * from `A_CLIENT_DETAILS` where `client_id`='$client_id' limit 0,1;");$n=$db->num_rows($r);
            if ($n==0){
                $db->query("insert into A_CLIENT_DETAILS (`client_id`,`address_jur`,`address_fakt`,`edrpou`,`svidotctvo`,`vytjag`,`vat`,`mfo`,`bank`,`account`,`not_resident`,`nr_details`) values ('$client_id','$address_jur','$address_fakt','$edrpou','$svidotctvo','$vytjag','$vat','$mfo','$bank','$account','$not_resident','$nr_details');");
            }
            if ($n==1){
                $db->query("update A_CLIENT_DETAILS set `address_jur`='$address_jur', `address_fakt`='$address_fakt', `edrpou`='$edrpou', `svidotctvo`='$svidotctvo', `vytjag`='$vytjag', `vat`='$vat', `mfo`='$mfo', `bank`='$bank', `account`='$account', `not_resident`='$not_resident', `nr_details`='$nr_details', `buh_name`='$buh_name',`buh_edrpou`='$buh_edrpou' where `client_id`='$client_id';");
            }
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function showWorkPairForm($client_id){$db=DbSingleton::getDb();$list="";
        $r=$db->query("select PAIR_INDEX from T2_WORK_PAIR where ART_ID='$client_id';");$n=$db->num_rows($r);
        for ($i=1;$i<=$n+3;$i++){
            $pair_index="";
            if ($i<=$n){$pair_index=$db->result($r,$i-1,"PAIR_INDEX");}
            $list.="<tr><td><input type='text' id='work_pair_$i' value='$pair_index' class='form-control'></td></tr>";
        }
        $list.="<input type='hidden' id='work_pair_n' value='".($n+3)."'>";
        return $list;
    }

    function saveclientsLogistic($client_id,$index_pack,$height,$length,$width,$volume,$weight_netto,$weight_brutto,$necessary_amount_car,$units_id,$multiplicity_package,$shoulder_delivery,$general_quant,$work_pair){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка збереження даних!";
        $client_id=$slave->qq($client_id);$index_pack=$slave->qq($index_pack);$height=$slave->qq($slave->point_valid($height));$length=$slave->qq($slave->point_valid($length));$width=$slave->qq($slave->point_valid($width));$volume=$slave->qq($slave->point_valid($volume));$weight_netto=$slave->qq($slave->point_valid($weight_netto));$weight_brutto=$slave->qq($slave->point_valid($weight_brutto));$necessary_amount_car=$slave->qq($necessary_amount_car);$units_id=$slave->qq($units_id);
        $multiplicity_package=$slave->qq($multiplicity_package);$shoulder_delivery=$slave->qq($shoulder_delivery);$general_quant=$slave->qq($general_quant);
        if ($client_id>0){
            //T2_PACKAGING UPDATE
            $r=$db->query("select * from `T2_PACKAGING` where `ART_ID`='$client_id' limit 0,1;");$n=$db->num_rows($r);
            if ($n==0){
                $db->query("insert into T2_PACKAGING (`ART_ID`,`INDEX_PACK`,`HEIGHT`,`LENGTH`,`WIDTH`,`VOLUME`,`WEIGHT_NETTO`,`WEIGHT_BRUTTO`,`NECESSARY_AMOUNT_CAR`,`UNITS_ID`,`MULTIPLICITY_PACKAGE`,`SHOULDER_DELIVERY`,`GENERAL_QUANT`) values ('$client_id','$index_pack','$height','$length','$width','$volume','$weight_netto','$weight_brutto','$necessary_amount_car','$units_id','$multiplicity_package','$shoulder_delivery','$general_quant');");
            }
            if ($n==1){
                $db->query("update T2_PACKAGING set `INDEX_PACK`='$index_pack', `HEIGHT`='$height', `LENGTH`='$length', `WIDTH`='$width', `VOLUME`='$volume', `WEIGHT_NETTO`='$weight_netto', `WEIGHT_BRUTTO`='$weight_brutto', `NECESSARY_AMOUNT_CAR`='$necessary_amount_car', `UNITS_ID`='$units_id', `MULTIPLICITY_PACKAGE`='$multiplicity_package', `SHOULDER_DELIVERY`='$shoulder_delivery', `GENERAL_QUANT`='$general_quant' where `ART_ID`='$client_id';");
            }
            if ($work_pair!=""){
                $db->query("delete from T2_WORK_PAIR where ART_ID='$client_id';");
                foreach ($work_pair as $wp){
                    if ($wp!=""){
                        $db->query("insert into T2_WORK_PAIR  (`ART_ID`,`PAIR_INDEX`) values ('$client_id','$wp');");
                    }
                }
            }
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function showCountryManual($sel_id){$db=DbSingleton::getDb();$manual=new manual;
        $form="";$form_htm=RD."/tpl/clients_country_list.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select * from T2_COUNTRIES order by COUNTRY_NAME asc;");$n=$db->num_rows($r);$list="";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"COUNTRY_ID");
            $name=$db->result($r,$i-1,"COUNTRY_NAME");
            $alfa2=$db->result($r,$i-1,"ALFA2");
            $alfa3=$db->result($r,$i-1,"ALFA3");
            $duty=$manual->getManualMCaption("DUTY",$db->result($r,$i-1,"DUTY"));
            $risk=$manual->getManualMCaption("RISK",$db->result($r,$i-1,"RISK"));
            $sel="";if ($sel_id==$id){$sel=" style='background-color:#d5fdf5'";}
            $list.="<tr onClick='selectCountry(\"$id\",\"$name\")' $sel>
                <td>$id</td>
                <td>$name</td>
                <td>$alfa2</td>
                <td>$alfa3</td>
                <td>$duty</td>
                <td>$risk</td>
                <td align='center'>
                    <button class='btn btn-xs btn-warning' onClick=\"showCountryForm('$id');\"><i class='fa fa-edit'></i></button>
                    <button class='btn btn-xs btn-default' onClick=\"dropCountry('$id');\"><i class='fa fa-trash'></i></button>
                </td>
            </tr>";
        }
        $form=str_replace("{list}",$list,$form);
        return $form;
    }

    function showCountryForm($id){$db=DbSingleton::getDb();$manual=new manual;
        $form="";$form_htm=RD."/tpl/clients_country_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select * from T2_COUNTRIES where COUNTRY_ID='$id' limit 0,1;");
        $name=$db->result($r,0,"COUNTRY_NAME");
        $alfa2=$db->result($r,0,"ALFA2");
        $alfa3=$db->result($r,0,"ALFA3");
        $duty=$db->result($r,0,"DUTY");
        $risk=$db->result($r,0,"RISK");
        $form=str_replace("{id}",$id,$form);
        $form=str_replace("{name}",$name,$form);
        $form=str_replace("{alfa2}",$alfa2,$form);
        $form=str_replace("{alfa3}",$alfa3,$form);
        $form=str_replace("{duty}",$duty,$form);
        $form=str_replace("{duty_caption}",$manual->getManualMCaption("DUTY",$duty),$form);
        $form=str_replace("{risk}",$risk,$form);
        $form=str_replace("{risk_caption}",$manual->getManualMCaption("RISK",$risk),$form);
        return array($form,"Форма Країни походження");
    }

    function saveclientsCountryForm($id,$name,$alfa2,$alfa3,$duty,$risk){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка збереження даних!";
        $id=$slave->qq($id);$name=$slave->qq($name);$alfa2=$slave->qq($alfa2);$alfa3=$slave->qq($alfa3);$duty=$slave->qq($duty);$risk=$slave->qq($risk);
        if ($id>0){
            $r=$db->query("select * from `T2_COUNTRIES` where `COUNTRY_ID`='$id' limit 0,1;");$n=$db->num_rows($r);
            if ($n==0){
                $db->query("insert into T2_COUNTRIES (`COUNTRY_ID`,`COUNTRY_NAME`,`ALFA2`,`ALFA3`,`DUTY`,`RISK`) values ('$id','$name','$alfa2','$alfa3','$duty','$risk');");
            }
            if ($n==1){
                $db->query("update T2_COUNTRIES set `COUNTRY_NAME`='$name', `ALFA2`='$alfa2', `ALFA3`='$alfa3', `DUTY`='$duty', `RISK`='$risk' where `COUNTRY_ID`='$id';");
            }
            $answer=1;$err="";
        }
        if ($id=="" && $name!=""){
            $db->query("insert into T2_COUNTRIES (`COUNTRY_ID`,`COUNTRY_NAME`,`ALFA2`,`ALFA3`,`DUTY`,`RISK`) values ('$id','$name','$alfa2','$alfa3','$duty','$risk');"); $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function showCostumsManual($sel_id){$db=DbSingleton::getDb();$manual=new manual;
        $form="";$form_htm=RD."/tpl/clients_costums_list.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select * from T2_COSTUMS order by COSTUMS_NAME asc;");$n=$db->num_rows($r);$list="";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"COSTUMS_ID");
            $name=$db->result($r,$i-1,"COSTUMS_NAME");
            $preferential_rate=$db->result($r,$i-1,"PREFERENTIAL_RATE");
            $full_rate=$db->result($r,$i-1,"FULL_RATE");
            $sertification=$manual->getManualMCaption("costums_sertification",$db->result($r,$i-1,"SERTIFICATION"));
            $gos_standart=$manual->getManualMCaption("costums_gos_standart",$db->result($r,$i-1,"GOS_STANDART"));
            $type_declaration=$manual->getManualMCaption("costums_type_declaration",$db->result($r,$i-1,"TYPE_DECLARATION"));
            $sel="";if ($sel_id==$id){$sel=" style='background-color:#d5fdf5'";}
            $list.="<tr onClick='selectCostums(\"$id\",\"$name\")' $sel>
                <td>$id</td>
                <td>$name</td>
                <td align='right'>$preferential_rate</td>
                <td align='right'>$full_rate</td>
                <td align='center'>$type_declaration</td>
                <td align='center'>$sertification</td>
                <td align='center'>$gos_standart</td>
                <td align='center'>
                    <button class='btn btn-xs btn-warning' onClick=\"showCostumsForm('$id');\"><i class='fa fa-edit'></i></button>
                    <button class='btn btn-xs btn-default' onClick=\"dropCostums('$id');\"><i class='fa fa-trash'></i></button>
                </td>
            </tr>";
        }
        $form=str_replace("{list}",$list,$form);
        return $form;
    }

    function showCostumsForm($id){$db=DbSingleton::getDb();$manual=new manual;
        $form="";$form_htm=RD."/tpl/clients_costums_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select * from T2_COSTUMS where COSTUMS_ID='$id' limit 0,1;");
        $name=$db->result($r,0,"COSTUMS_NAME");
        $preferential_rate=$db->result($r,0,"PREFERENTIAL_RATE");
        $sertification=$db->result($r,0,"SERTIFICATION");
        $gos_standart=$db->result($r,0,"GOS_STANDART");
        $type_declaration=$db->result($r,0,"TYPE_DECLARATION");

        $form=str_replace("{id}",$id,$form);
        $form=str_replace("{name}",$name,$form);
        $form=str_replace("{preferential_rate}",$preferential_rate,$form);
        $form=str_replace("{sertification}",$sertification,$form);
        $form=str_replace("{sertification_caption}",$manual->getManualMCaption("costums_sertification",$sertification),$form);
        $form=str_replace("{gos_standart}",$gos_standart,$form);
        $form=str_replace("{gos_standart_caption}",$manual->getManualMCaption("costums_gos_standart",$gos_standart),$form);
        $form=str_replace("{type_declaration}",$type_declaration,$form);
        $form=str_replace("{type_declaration_caption}",$manual->getManualMCaption("costums_type_declaration",$type_declaration),$form);
        return array($form,"Форма митного коду УКТЕЗД");
    }

    function saveclientsCostumsForm($id,$name,$preferential_rate,$full_rate,$type_declaration,$sertification,$gos_standart){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка збереження даних!";
        $id=$slave->qq($id);$name=$slave->qq($name);$preferential_rate=$slave->qq($slave->point_valid($preferential_rate));$full_rate=$slave->qq($slave->point_valid($full_rate));$type_declaration=$slave->qq($type_declaration);$sertification=$slave->qq($sertification);$gos_standart=$slave->qq($gos_standart);
        if ($id>0){
            $r=$db->query("select * from `T2_COSTUMS` where `COSTUMS_ID`='$id' limit 0,1;");$n=$db->num_rows($r);
            if ($n==0){
                $db->query("insert into T2_COSTUMS (`COSTUMS_ID`,`COSTUMS_NAME`,`PREFERENTIAL_RATE`,`FULL_RATE`,`SERTIFICATION`,`GOS_STANDART`,`TYPE_DECLARATION`) values ('$id','$name','$preferential_rate','$full_rate','$sertification','$gos_standart','$type_declaration');");
            }
            if ($n==1){
                $db->query("update T2_COSTUMS set `COSTUMS_NAME`='$name', `PREFERENTIAL_RATE`='$preferential_rate', `FULL_RATE`='$full_rate', `SERTIFICATION`='$sertification', `GOS_STANDART`='$gos_standart', `TYPE_DECLARATION`='$type_declaration' where `COSTUMS_ID`='$id';");
            }
            $answer=1;$err="";
        }
        if ($id=="" && $name!=""){
            $db->query("insert into T2_COSTUMS (`COSTUMS_ID`,`COSTUMS_NAME`,`PREFERENTIAL_RATE`,`FULL_RATE`,`SERTIFICATION`,`GOS_STANDART`,`TYPE_DECLARATION`) values ('$id','$name','$preferential_rate','$full_rate','$sertification','$gos_standart','$type_declaration');"); $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function loadArticleZED($client_id){$db=DbSingleton::getDb();
        $form="";$form_htm=RD."/tpl/clients_zed.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select t2z.*, t2c.COUNTRY_NAME, t2s.COSTUMS_NAME from T2_ZED t2z 
            left outer join T2_COUNTRIES t2c on t2c.COUNTRY_ID=t2z.COUNTRY_ID
            left outer join T2_COSTUMS t2s on t2s.COSTUMS_ID=t2z.COSTUMS_ID
        where t2z.ART_ID='$client_id' limit 0,1;");;

        $country_id=$db->result($r,0,"COUNTRY_ID");
        $country_name=$db->result($r,0,"COUNTRY_NAME");
        $costums_id=$db->result($r,0,"COSTUMS_ID");
        $costums_name=$db->result($r,0,"COSTUMS_NAME");
        $form=str_replace("{client_id}",$client_id,$form);
        $form=str_replace("{country_id}",$country_id,$form);
        $form=str_replace("{country_name}",$country_name,$form);
        $form=str_replace("{costums_id}",$costums_id,$form);
        $form=str_replace("{costums_name}",$costums_name,$form);
        return $form;
    }
    function saveclientsZED($client_id,$country_id,$costums_id){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка збереження даних!";
        $client_id=$slave->qq($client_id);$country_id=$slave->qq($country_id);$costums_id=$slave->qq($slave->point_valid($costums_id));
        if ($client_id>0){
            //T2_ZED UPDATE
            $r=$db->query("select * from `T2_ZED` where `ART_ID`='$client_id' limit 0,1;");$n=$db->num_rows($r);
            if ($n==0){
                $db->query("insert into T2_ZED (`ART_ID`,`COUNTRY_ID`,`COSTUMS_ID`) values ('$client_id','$country_id','$costums_id');");
            }
            if ($n==1){
                $db->query("update T2_ZED set `COUNTRY_ID`='$country_id', `COSTUMS_ID`='$costums_id' where `ART_ID`='$client_id';");
            }
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function loadClientStorage($client_id){$db=DbSingleton::getDb();
        $form="";$form_htm=RD."/tpl/clients_storage_list.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select cc.*, t2cn.COUNTRY_NAME, t2st.STATE_NAME, t2rg.REGION_NAME, t2ct.CITY_NAME from A_CLIENTS_STORAGE cc
            left outer join T2_COUNTRIES t2cn on t2cn.COUNTRY_ID=cc.country 
            left outer join T2_STATE t2st on t2st.STATE_ID=cc.state
            left outer join T2_REGION t2rg on t2rg.REGION_ID=cc.region
            left outer join T2_CITY t2ct on t2ct.CITY_ID=cc.city
        where cc.client_id='$client_id' and cc.status='1';");$n=$db->num_rows($r);$list="";

        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"name");
            $contact_person=$db->result($r,$i-1,"contact_person");
            $email=$db->result($r,$i-1,"email");
            $phone=$db->result($r,$i-1,"phone");
            $country=$db->result($r,$i-1,"COUNTRY_NAME");
            $state=$db->result($r,$i-1,"STATE_NAME");
            $region=$db->result($r,$i-1,"REGION_NAME");
            $city=$db->result($r,$i-1,"CITY_NAME");
            $vis=$db->result($r,$i-1,"visible");
            if ($vis==1){$vis=" checked=''";} else $vis=""; // onClick='dropClientStorage(\"$client_id\",\"$id\",\"$city\");' ???
            $list.="<tr>
                <td>
                    <button class='btn btn-sm btn-default' onClick='showClientStorageForm(\"$client_id\",\"$id\");'><i class='fa fa-edit'></i></button>
                    <button class='btn btn-sm btn-default'><i class='fa fa-times'></i></button>
                </td>
                <td>$id</td>
                <td>$name</td>
                <td>$country $state $region $city</td>
                <td>$email</td>
                <td>$phone</td>
                <td>$contact_person</td>
                <td style='text-align: center;'><input id='$i' type='checkbox' name='check[$i]' class='check_cl' disabled='false' $vis/></td>
            </tr>";
        }
        if ($n==0){$list="<tr><td align='center' colspan=7><h3 class='text-center'>Склади відсутні</h3></td></tr>";}
        $form=str_replace("{list_storage}",$list,$form);
        $form=str_replace("{client_id}",$client_id,$form);
        return $form;
    }

    function loadClientDocuments($client_id) {
        $form_mandate=$this->loadClientMandate($client_id);
        $form_basis=$this->loadClientBasis($client_id);
        return array($form_mandate,$form_basis);
    }

    function loadClientMandate($client_id){$db=DbSingleton::getDb();
        $form="";$form_htm=RD."/tpl/clients_mandate_list.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select * from A_CLIENTS_MANDATE where client_id='$client_id' and status='1';");$n=$db->num_rows($r);$list="";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $number=$db->result($r,$i-1,"number");
            $seria=$db->result($r,$i-1,"seria");
            $receiver=$db->result($r,$i-1,"receiver");
            $data_from=$db->result($r,$i-1,"data_from");
            $data_to=$db->result($r,$i-1,"data_to");
            $list.="<tr>
                <td>
                    <button class='btn btn-sm btn-default' onClick='showClientMandateForm(\"$client_id\",\"$id\");'><i class='fa fa-edit'></i></button>
                    <button class='btn btn-sm btn-default' onClick='dropClientMandate(\"$client_id\",\"$id\");'><i class='fa fa-times'></i></button>
                </td>
                <td>$id</td>
                <td>$number</td>
                <td>$seria</td>
                <td>$receiver</td>
                <td>$data_from</td>
                <td>$data_to</td>
            </tr>";
        }
        if ($n==0){$list="<tr><td align='center' colspan=6><h3 class='text-center'>Доручення відсутні</h3></td></tr>";}
        $form=str_replace("{list_mandate}",$list,$form);
        $form=str_replace("{client_id}",$client_id,$form);
        return $form;
    }

    function showClientMandateForm($client_id,$mandate_id){$db=DbSingleton::getDb();
        $form="";$form_htm=RD."/tpl/clients_mandate_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select * from A_CLIENTS_MANDATE where id='$mandate_id' limit 0,1;");
        $number=$db->result($r,0,"number");
        $seria=$db->result($r,0,"seria");
        $receiver=$db->result($r,0,"receiver");
        $data_from=$db->result($r,0,"data_from");
        $data_to=$db->result($r,0,"data_to");
        $form=str_replace("{client_id}",$client_id,$form);
        $form=str_replace("{mandate_id}",$mandate_id,$form);
        $form=str_replace("{number}",$number,$form);
        $form=str_replace("{seria}",$seria,$form);
        $form=str_replace("{receiver}",$receiver,$form);
        $form=str_replace("{data_from}",$data_from,$form);
        $form=str_replace("{data_to}",$data_to,$form);
        return array($form,"Доручення контрагента");
    }

    function saveClientMandateForm($client_id,$mandate_id,$number,$seria,$receiver,$data_from,$data_to){$db=DbSingleton::getDb(); $slave=new slave;
        $answer=0;$err="Помилка збереження даних!";
        $client_id=$slave->qq($client_id);$mandate_id=$slave->qq($mandate_id);
        if ($client_id>0 && $mandate_id>0){
            $r=$db->query("select * from `A_CLIENTS_MANDATE` where `id`='$mandate_id' limit 0,1;");$n=$db->num_rows($r);
            if ($n==0){
                $db->query("insert into A_CLIENTS_MANDATE (`client_id`,`number`,`seria`,`receiver`,`data_from`,`data_to`,`status`) values ('$client_id','$number','$seria','$receiver','$data_from','$data_to','1');");
            }
            if ($n==1){
                $db->query("update A_CLIENTS_MANDATE set `number`='$number', `seria`='$seria', `receiver`='$receiver', `data_from`='$data_from', `data_to`='$data_to' where `id`='$mandate_id' and client_id='$client_id';");
            }
            $answer=1;$err="";
        }
        if ($mandate_id=="" || $mandate_id=="0"){
            $db->query("insert into A_CLIENTS_MANDATE (`client_id`,`number`,`seria`,`receiver`,`data_from`,`data_to`,`status`) values ('$client_id','$number','$seria','$receiver','$data_from','$data_to','1');");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function dropClientMandate($client_id,$mandate_id){$db=DbSingleton::getDb(); $slave=new slave;
        $answer=0;$err="Помилка збереження даних!";
        $client_id=$slave->qq($client_id);$mandate_id=$slave->qq($mandate_id);
        if ($client_id>0 && $mandate_id>0){
            $db->query("update A_CLIENTS_MANDATE set status='0' where id='$mandate_id' and client_id='$client_id';");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function loadClientBasis($client_id){$db=DbSingleton::getDb();
        $form="";$form_htm=RD."/tpl/clients_basis_list.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select * from A_CLIENTS_BASIS where client_id='$client_id' and status='1';");$n=$db->num_rows($r);$list="";

        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $number=$db->result($r,$i-1,"number");
            $data_from=$db->result($r,$i-1,"data_from");
            $data_to=$db->result($r,$i-1,"data_to");
            $list.="<tr>
                <td>
                    <button class='btn btn-sm btn-default' onClick='showClientBasisForm(\"$client_id\",\"$id\");'><i class='fa fa-edit'></i></button>
                    <button class='btn btn-sm btn-default' onClick='dropClientBasis(\"$client_id\",\"$id\");'><i class='fa fa-times'></i></button>
                </td>
                <td>$id</td>
                <td>$number</td>
                <td>$data_from</td>
                <td>$data_to</td>
            </tr>";
        }
        if ($n==0){$list="<tr><td align='center' colspan=6><h3 class='text-center'>Підстави відсутні</h3></td></tr>";}
        $form=str_replace("{list_basis}",$list,$form);
        $form=str_replace("{client_id}",$client_id,$form);
        return $form;
    }

    function showClientBasisForm($client_id,$basis_id){$db=DbSingleton::getDb();
        $form="";$form_htm=RD."/tpl/clients_basis_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select * from A_CLIENTS_BASIS where id='$basis_id' limit 0,1;");
        $number=$db->result($r,0,"number");
        $data_from=$db->result($r,0,"data_from");
        $data_to=$db->result($r,0,"data_to");
        $form=str_replace("{client_id}",$client_id,$form);
        $form=str_replace("{basis_id}",$basis_id,$form);
        $form=str_replace("{number}",$number,$form);
        $form=str_replace("{data_from}",$data_from,$form);
        $form=str_replace("{data_to}",$data_to,$form);
        return array($form,"Підстава контрагента");
    }

    function saveClientBasisForm($client_id,$basis_id,$number,$data_from,$data_to){$db=DbSingleton::getDb(); $slave=new slave;
        $answer=0;$err="Помилка збереження даних!";
        $client_id=$slave->qq($client_id);$basis_id=$slave->qq($basis_id);
        if ($client_id>0 && $basis_id>0){
            $r=$db->query("select * from `A_CLIENTS_BASIS` where `id`='$basis_id' limit 0,1;");$n=$db->num_rows($r);
            if ($n==0){
                $db->query("insert into A_CLIENTS_BASIS (`client_id`,`number`,`data_from`,`data_to`,`status`) values ('$client_id','$number','$data_from','$data_to','1');");
            }
            if ($n==1){
                $db->query("update A_CLIENTS_BASIS set `number`='$number', `data_from`='$data_from', `data_to`='$data_to' where `id`='$basis_id' and client_id='$client_id';");
            }
            $answer=1;$err="";
        }
        if ($basis_id=="" || $basis_id=="0"){
            $db->query("insert into A_CLIENTS_BASIS (`client_id`,`number`,`data_from`,`data_to`,`status`) values ('$client_id','$number','$data_from','$data_to','1');");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function dropClientBasis($client_id,$basis_id){$db=DbSingleton::getDb(); $slave=new slave;
        $answer=0;$err="Помилка збереження даних!";
        $client_id=$slave->qq($client_id);$basis_id=$slave->qq($basis_id);
        if ($client_id>0 && $basis_id>0){
            $db->query("update A_CLIENTS_BASIS set status='0' where id='$basis_id' and client_id='$client_id';");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function showClientStorageForm($client_id,$storage_id){$db=DbSingleton::getDb();$slave=new slave;
        $form="";$form_htm=RD."/tpl/clients_storage_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select * from A_CLIENTS_STORAGE where id='$storage_id' limit 0,1;");
        $name=$db->result($r,0,"name");
        $contact_person=$db->result($r,0,"contact_person");
        $email=$db->result($r,0,"email");
        $phone=$db->result($r,0,"phone");
        $country=$db->result($r,0,"country");
        $state=$db->result($r,0,"state");
        $region=$db->result($r,0,"region");
        $city=$db->result($r,0,"city");
        $visible=$db->result($r,0,"visible");
        if ($visible==1){$visible=" checked='checked'";}
        $form=str_replace("{client_id}",$client_id,$form);
        $form=str_replace("{storage_id}",$storage_id,$form);
        $form=str_replace("{name}",$name,$form);
        $form=str_replace("{contact_person}",$contact_person,$form);
        $form=str_replace("{email}",$email,$form);
        $form=str_replace("{phone}",$phone,$form);
        $form=str_replace("{country_list}",$slave->showSelectList("T2_COUNTRIES","COUNTRY_ID","COUNTRY_NAME",$country),$form);
        $form=str_replace("{state_list}",$slave->showSelectSubList("T2_STATE","COUNTRY_ID","$country","STATE_ID","STATE_NAME",$state),$form);
        $form=str_replace("{region_list}",$slave->showSelectSubList("T2_REGION","STATE_ID","$state","REGION_ID","REGION_NAME",$region),$form);
        $form=str_replace("{city_list}",$slave->showSelectSubList("T2_CITY","REGION_ID","$region","CITY_ID","CITY_NAME",$city),$form);
        $form=str_replace("{client_checked}",$visible,$form);
        return array($form,"Карта складу контрагента");
    }

    function saveClientStorageForm($client_id,$storage_id,$name,$email,$phone,$contact_person,$country,$state,$region,$city,$client_visible){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка збереження даних!";
        $client_id=$slave->qq($client_id);$storage_id=$slave->qq($storage_id);$name=$slave->qq($name);$email=$slave->qq($email);$phone=$slave->qq($phone);$contact_person=$slave->qq($contact_person);$country=$slave->qq($country);$state=$slave->qq($state);$region=$slave->qq($region);$city=$slave->qq($city);
        if ($client_id>0 && $storage_id>0){
            $r=$db->query("select * from `A_CLIENTS_STORAGE` where `id`='$storage_id' limit 0,1;");$n=$db->num_rows($r);
            if ($n==0){
                $db->query("insert into A_CLIENTS_STORAGE (`client_id`,`name`,`country`,`state`,`region`,`city`,`contact_person`,`email`,`phone`,`status`,`visible`) values ('$client_id','$name','$country','$state','$region','$city','$contact_person','$email','$phone','1','$client_visible');");
            }
            if ($n==1){
                $db->query("update A_CLIENTS_STORAGE set `name`='$name', `country`='$country', `state`='$state', `region`='$region', `city`='$city', `contact_person`='$contact_person', `email`='$email', `phone`='$phone', `visible`='$client_visible' where `id`='$storage_id' and client_id='$client_id';");
            }
            $answer=1;$err="";
        }
        if ($storage_id=="" && $name!=""){
            $db->query("insert into A_CLIENTS_STORAGE (`client_id`,`name`,`country`,`state`,`region`,`city`,`contact_person`,`email`,`phone`,`visible`,`status`) values ('$client_id','$name','$country','$state','$region','$city','$contact_person','$email','$phone','$client_visible','1');"); $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function getClientCash($client_id){$db=DbSingleton::getDb();$cash_id=1;
        $r=$db->query("select cash_id from A_CLIENTS_CONDITIONS where client_id='$client_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$cash_id=$db->result($r,0,"cash_id");}
        return $cash_id;
    }

    function showClientGeneralSaldoForm($client_id){$db=DbSingleton::getDb();$form="";$balans_after=0;
        if ($client_id>0){
            $data_from=date("Y-m-01");$data_to=date("Y-m-t"); //current_month
            $form_htm=RD."/tpl/client_general_saldo_list.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
            //$saldo_start=0;
            $saldo_end=0; //default value
            $client_cash_id=$this->getClientCash($client_id);
            list($saldo_start,$saldo_cash_id,)=$this->getClientBalansPeriodStart($client_id,$client_cash_id,$data_from,0);

            $saldo_data_start=date("Y-m-01");
            $form=str_replace("{saldo_start}",$saldo_start."".$this->getCashAbr($saldo_cash_id),$form);
            $form=str_replace("{saldo_data_start}",$saldo_data_start,$form);

            $r=$db->query("select b.*, mc.abr as cash_name, pmc.abr from B_CLIENT_BALANS_JOURNAL b 
                left outer join CASH mc on mc.id=b.cash_id 
                left outer join CASH pmc on pmc.id=b.pay_cash_id 
            where b.client_id='$client_id' and b.data>='$data_from 00:00:00' and b.data<='$data_to 23:59:59' group by b.doc_type_id, b.doc_id order by b.id asc;");$n=$db->num_rows($r);$list="";

            if ($n>0){ $sale_invoice=new sale_invoice; $back_clients=new back_clients;//$pay=new pay;
                for ($i=1;$i<=$n;$i++){
                    $data=$db->result($r,$i-1,"data");
                    $cash_name=$db->result($r,$i-1,"cash_name");
                    $summ=round($db->result($r,$i-1,"summ"),2);
                    $deb_kre=$db->result($r,$i-1,"deb_kre");
                    $balans_before=$db->result($r,$i-1,"balans_before");
                    $balans_after=$db->result($r,$i-1,"balans_after");
                    $doc_type_id=$db->result($r,$i-1,"doc_type_id"); // 1-Видаткова (SaleInvoice); 2- Оплата(Pay); 3-Автооплата(PayAuto)
                    $doc_id=$db->result($r,$i-1,"doc_id");
                    $pay_cash_name=$db->result($r,$i-1,"pmc.abr");
                    $pay_summ=$db->result($r,$i-1,"pay_summ");
                    $document_name="";$function="";
                    if ($doc_type_id==1){ $document_name=$sale_invoice->getSaleInvoiceName($doc_id);$function="showSaleInvoiceCard(\"$doc_id\");"; }
                    if ($doc_type_id==2){ //????????????????????? change summ to 0 if avans pay
                        list($jpay_doc_type_id,$document_name)=$sale_invoice->getJPayName($doc_id); $function="viewJpayMoneyPay(\"$doc_id\")";
                        if ($jpay_doc_type_id==99) {$summ="";}
                    }
                    if ($doc_type_id==3){ list(,$document_name)=$sale_invoice->getJPayName($doc_id); }
                    if ($doc_type_id==5){ $document_name=$back_clients->getBackClientsName($doc_id); }

                    $debit="";$kredit="";
                    if ($deb_kre==1){
                        $debit=$summ;
                        $saldo_end-=$debit;
                    }
                    if ($deb_kre==2){
                        $kredit=$summ;
                        $saldo_end+=$kredit;
                    }
                    //if ($i==$n){$saldo_data_end=substr($data,0,10);}
                    $list.="<tr align='center'>
                        <td>$i</td>
                        <td>$data</td>
                        <td>$cash_name</td>
                        <td>$balans_before</td>
                        <td>$debit</td>
                        <td>$kredit</td>
                        <td>$balans_after</td>
                        <td>$document_name</td>
                        <td>$pay_summ $pay_cash_name</td>
                        <td><button class='btn btn-xs btn-default' title='Переглянути' onClick='$function'><i class='fa fa-eye'></i></button></td>
                    </tr>";
                }
                $saldo_end=round($balans_after,2);
            }

            if ($n==0){$list="<tr><td colspan='8' align='center'>Документи відсутні</td></tr>"; $saldo_end=$saldo_start;}
            $form=str_replace("{list}",$list,$form);
            $form=str_replace("{saldo_end}",$saldo_end."".$this->getCashAbr($saldo_cash_id),$form);
            $saldo_data_end=date("Y-m-d");
            $form=str_replace("{saldo_data_end}",$saldo_data_end,$form);
            $form=str_replace("{client_id}",$client_id,$form);
        }
        return array($form,"Взаєморозрахунки з контрагентом");
    }

    function getSaldoEnd($client_id){$db=DbSingleton::getDb();$saldo_start=$balans_after=0;
        if ($client_id>0){
            $data_from=date("Y-m-01");$data_to=date("Y-m-t");
            //$saldo_start=0;
            $saldo_end=0;
            $client_cash_id=$this->getClientCash($client_id);
            list($saldo_start,$saldo_cash_id,)=$this->getClientBalansPeriodStart($client_id,$client_cash_id,$data_from,0);

            $r=$db->query("select b.*, mc.abr as cash_name, pmc.abr from B_CLIENT_BALANS_JOURNAL b 
                left outer join CASH mc on mc.id=b.cash_id 
                left outer join CASH pmc on pmc.id=b.pay_cash_id 
            where b.client_id='$client_id' and b.data>='$data_from 00:00:00' and b.data<='$data_to 23:59:59' group by b.doc_type_id, b.doc_id order by b.id asc;");$n=$db->num_rows($r);

            if ($n>0){
                $sale_invoice=new sale_invoice; //$back_clients=new back_clients;
                for ($i=1;$i<=$n;$i++){
                    //$id=$db->result($r,$i-1,"id");
                    //$data=$db->result($r,$i-1,"data");
                    //$cash_id=$db->result($r,$i-1,"cash_id");
                    //$cash_name=$db->result($r,$i-1,"cash_name");
                    $summ=round($db->result($r,$i-1,"summ"),2);
                    $deb_kre=$db->result($r,$i-1,"deb_kre");
                    //$balans_before=$db->result($r,$i-1,"balans_before");
                    //$balans_after=$db->result($r,$i-1,"balans_after");
                    $doc_type_id=$db->result($r,$i-1,"doc_type_id");
                    $doc_id=$db->result($r,$i-1,"doc_id");
                    //$pay_cash_name=$db->result($r,$i-1,"pmc.abr");
                    //$pay_summ=$db->result($r,$i-1,"pay_summ");
                    //$document_name="";$function="";
                   // if ($doc_type_id==1){ $document_name=$sale_invoice->getSaleInvoiceName($doc_id);/*$function="showSaleInvoiceCard(\"$doc_id\");";*/ }
                    if ($doc_type_id==2){
                        list($jpay_doc_type_id,)=$sale_invoice->getJPayName($doc_id); //$function="viewJpayMoneyPay(\"$doc_id\")";
                        if ($jpay_doc_type_id==99) {$summ="";}
                    }
                    //if ($doc_type_id==3){ list($jpay_doc_type_id,$document_name)=$sale_invoice->getJPayName($doc_id); }
                    //if ($doc_type_id==5){ $document_name=$back_clients->getBackClientsName($doc_id); }

                    //$debit="";$kredit="";
                    if ($deb_kre==1){
                        $debit=$summ;
                        $saldo_end-=$debit;
                    }
                    if ($deb_kre==2){
                        $kredit=$summ;
                        $saldo_end+=$kredit;
                    }
                }
                //$saldo_end=round($balans_after,2);
            }

            //if($saldo_end<0) $saldo_cap="<span style='color:red;'>(борг)</span>";
            //if($saldo_end>0) $saldo_cap="<span style='color:limegreen;'>(предоплата)</span>";
            //$saldo_end=$saldo_end." ".$this->getCashAbr($saldo_cash_id)." ".$saldo_cap;
            $saldo_cap="";
            //if($saldo_start<0) $saldo_cap="<span style='color:red;'>(борг)</span>";
            //if($saldo_start>0) $saldo_cap="<span style='color:limegreen;'>(предоплата)</span>";
            $saldo_start=$saldo_start." ".$this->getCashAbr($saldo_cash_id)." ".$saldo_cap;
        }
        return $saldo_start;
    }

    function filterClientGeneralSaldoForm($client_id,$data_from,$data_to){$db=DbSingleton::getDb();
        $list="";$client_saldo_start=$client_saldo_end=$client_saldo_data_start=$client_saldo_data_end=0;
        if ($client_id>0){ //current_month
            //$saldo_data_start=substr($data_from,0,-2)."01";$saldo_start=0;
            $saldo_data_end=$data_to;$saldo_end=0; //default value
            $client_cash_id=$this->getClientCash($client_id);

            list($saldo_start,$saldo_cash_id,)=$this->getClientBalansPeriodStart($client_id,$client_cash_id,$data_from,0);

            $client_saldo_data_start=$data_from;
            $client_saldo_data_end=$saldo_data_end;

            $r=$db->query("select b.*, mc.abr as cash_name, pmc.abr from B_CLIENT_BALANS_JOURNAL b 
                left outer join CASH mc on mc.id=b.cash_id 
                left outer join CASH pmc on pmc.id=b.pay_cash_id 
            where b.client_id='$client_id' and b.data>='$data_from 00:00:00' and b.data<='$data_to 23:59:59' group by b.doc_type_id, b.doc_id order by b.id asc;");$n=$db->num_rows($r);$list="";

            if ($n>0){
                $sale_invoice=new sale_invoice; $back_clients=new back_clients;//$pay=new pay;
                for ($i=1;$i<=$n;$i++){
                    //$id=$db->result($r,$i-1,"id");
                    $data=$db->result($r,$i-1,"data");
                    //$cash_id=$db->result($r,$i-1,"cash_id");
                    $cash_name=$db->result($r,$i-1,"cash_name");
                    $summ=round($db->result($r,$i-1,"summ"),2);
                    $deb_kre=$db->result($r,$i-1,"deb_kre");
                    $balans_before=$db->result($r,$i-1,"balans_before");
                    $balans_after=$db->result($r,$i-1,"balans_after");
                    $doc_type_id=$db->result($r,$i-1,"doc_type_id"); // 1-Видаткова (SaleInvoice); 2- Оплата(Pay); 3-Автооплата(PayAuto)
                    $doc_id=$db->result($r,$i-1,"doc_id");
                    $pay_cash_name=$db->result($r,$i-1,"pmc.abr");
                    $pay_summ=$db->result($r,$i-1,"pay_summ");
                    $document_name="";$function="";
                    if ($doc_type_id==1){ $document_name=$sale_invoice->getSaleInvoiceName($doc_id);$function="showSaleInvoiceCard(\"$doc_id\");"; }
                    if ($doc_type_id==2){ //????????????????????? change summ to 0 if avans pay
                        list($jpay_doc_type_id,$document_name)=$sale_invoice->getJPayName($doc_id); $function="viewJpayMoneyPay(\"$doc_id\")";
                        if ($jpay_doc_type_id==99) {$summ="";}
                    }
                    if ($doc_type_id==3){ list(,$document_name)=$sale_invoice->getJPayName($doc_id); }
                    if ($doc_type_id==5){ $document_name=$back_clients->getBackClientsName($doc_id); }

                    $debit="";$kredit="";
                    if ($deb_kre==1){
                        $debit=$summ;
                        $saldo_end-=$debit;
                    }
                    if ($deb_kre==2){
                        $kredit=$summ;
                        $saldo_end+=$kredit;
                    }
                    if ($i==$n){
                        //$saldo_data_end=substr($data,0,10);
                        $client_saldo_end=$balans_after."".$this->getCashAbr($saldo_cash_id);
                    }
                    if ($i==1){
                        $client_saldo_start=$balans_before."".$this->getCashAbr($saldo_cash_id);
                    }
                    $list.="<tr align='center'>
                        <td>$i</td>
                        <td>$data</td>
                        <td>$cash_name</td>
                        <td>$balans_before</td>
                        <td>$debit</td>
                        <td>$kredit</td>
                        <td>$balans_after</td>
                        <td>$document_name</td>
                        <td>$pay_summ $pay_cash_name</td>
                        <td><button class='btn btn-xs btn-default' title='Переглянути' onClick='$function'><i class='fa fa-eye'></i></button></td>
                    </tr>";
                }
                //$saldo_end=round($balans_after,2);
            }
            if ($n==0){
                $list="<tr><td colspan='8' align='center'>Документи відсутні</td></tr>";
                //$client_saldo_end=$balans_after."".$this->getCashAbr($saldo_cash_id);
                $client_saldo_start=$client_saldo_end=$saldo_start."".$this->getCashAbr($saldo_cash_id);
            }
        }
        return array($list,$client_saldo_start,$client_saldo_end,$client_saldo_data_start,$client_saldo_data_end);
    }

    function getClientBalansPeriodStart($client_id,$cash_id,$data_from,$recursion){$db=DbSingleton::getDb();$saldo_start=0;$saldo_data_start=$data_from;
        $r=$db->query("select * from B_CLIENT_BALANS_PERIOD where client_id='$client_id' and data_start='".date("Y-m-01",strtotime($data_from))."' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $saldo_start=$db->result($r,0,"saldo_start");
            $cash_id=$db->result($r,0,"cash_id");
            $saldo_data_start=$db->result($r,0,"data_start");
        }
        if ($n==0){
            $recursion+=1;
            if ($recursion<12){
                $data_from=date("Y-m-01",strtotime("$data_from -1 month"));
                list($saldo_start,,$saldo_data_start)=$this->getClientBalansPeriodStart($client_id,$cash_id,$data_from,$recursion);
            }
            else{ $data_main_start=date("Y-m-01",strtotime("$data_from"));
                $db->query("insert into B_CLIENT_BALANS_PERIOD (`client_id`,`cash_id`,`saldo_start`,`data_start`,`active`) values ('$client_id','$cash_id','0','$data_main_start','1');");
                $data_plus_month=date("Y-m-d", strtotime("$data_main_start +1 month"));
                $data_from=date("Y-m-01",strtotime("$data_plus_month"));
                 $recursion-=2;
                list($saldo_start,,$saldo_data_start)=$this->getClientBalansPeriodStart($client_id,$cash_id,$data_from,$recursion);
            }
        }
        return array($saldo_start,$cash_id,$saldo_data_start);
    }

    function getSaleInvoceProlog($client_id,$date_search) { $db=DbSingleton::getDb();$list="";$today=date("Y-m-d");session_start();$user_id=$_SESSION["media_user_id"];
        if ($date_search=="" || $date_search==0) $date_search=$today; $users=new users;
        $users_credit=$users->getUsersAccessCredit($user_id);
        $r=$db->query("select * from J_SALE_INVOICE where client_conto_id='$client_id' and data_pay<='$date_search' and summ_debit!=0;"); $n=$db->num_rows($r);

        for ($i=1;$i<=$n;$i++){
            $prefix=$db->result($r,$i-1,"prefix");
            $doc_nom=$db->result($r,$i-1,"doc_nom");
            $data_create=$db->result($r,$i-1,"data_create");
            $data_pay=$db->result($r,$i-1,"data_pay");
            $list.="<tr align='center'>
                <td>$i</td>
                <td>$prefix-$doc_nom</td>
                <td>$data_create</td>
                <td>$data_pay</td>
            </tr>";
        }
        $form="";$form_htm=RD."/tpl/sale_invoice_prolog.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        if ($n==0){$list="<tr><td align='center' colspan=5><h3 class='text-center'>Записи відсутні</h3></td></tr>";}
        $form=str_replace("{prolog_range}",$list,$form);
        $form=str_replace("{date_search}",$date_search,$form);
        $form=str_replace("{disabled}","disabled='disabled'",$form);
        $form=str_replace("{disabled2}","",$form);
        $form=str_replace("{prolog_days}",$users_credit,$form);
        $form=str_replace("{data_pay}","",$form);
        return $form;
    }

    function getSaleInvocePrologHistory($client_id) { $db=DbSingleton::getDb();$list="";$users=new users;
        $r=$db->query("select jp.*, j.prefix, j.doc_nom from J_SALE_INVOICE_PROLONGATION jp
            left outer join J_SALE_INVOICE j on (j.id=jp.invoice_id)
        where jp.client_id='$client_id';"); $n=$db->num_rows($r);

        for ($i=1;$i<=$n;$i++){
            $user_id=$db->result($r,$i-1,"user_id");
            $user_name=$users->getMediaUserName($user_id);
            $prefix=$db->result($r,$i-1,"prefix");
            $doc_nom=$db->result($r,$i-1,"doc_nom");
            $data_create=$db->result($r,$i-1,"date_pay_start");
            $data_pay=$db->result($r,$i-1,"date_pay_new");
            $list.="<tr align='center'>
                <td>$i</td>
                <td>$prefix-$doc_nom</td>
                <td>$user_name</td>
                <td>$data_create</td>
                <td>$data_pay</td>
            </tr>";
        }
        $form="";$form_htm=RD."/tpl/sale_invoice_prolog_history.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        if ($n==0){$list="<tr><td align='center' colspan=5><h3 class='text-center'>Записи відсутні</h3></td></tr>";}
        $form=str_replace("{prolog_range}",$list,$form);
        return $form;
    }

    function checkSaleInvoceProlog($client_id,$date_start,$date_new) { $db=DbSingleton::getDb();$list="";session_start();$user_id=$_SESSION["media_user_id"];$users=new users;
        $users_credit=$users->getUsersAccessCredit($user_id);
        $r=$db->query("select * from J_SALE_INVOICE where client_id='$client_id' and data_pay<='$date_start' and summ_debit!=0;"); $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){$date_pay_start="";
            $prefix=$db->result($r,$i-1,"prefix");
            $doc_nom=$db->result($r,$i-1,"doc_nom");
            $data_create=$db->result($r,$i-1,"data_create");
            $data_pay=$db->result($r,$i-1,"data_pay");
            $data_pay_start=$db->result($r,$i-1,"data_pay_start");

            if ($date_pay_start=="" || $date_pay_start=="0000-00-00") $datetime1 = new DateTime($data_pay); else $datetime1 = new DateTime($data_pay_start);
            $datetime2 = new DateTime($date_new);
            $interval = $datetime1->diff($datetime2);
            $dec = $interval->format('%a');

            if($users_credit>=$dec) $style=""; else $style="style='background:pink;	'";
            $list.="<tr align='center' $style>
                <td>$i</td>
                <td>$prefix-$doc_nom</td>
                <td>$data_create</td>
                <td>$data_pay</td>
            </tr>";
        }
        $form="";$form_htm=RD."/tpl/sale_invoice_prolog.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        if ($n==0){$list="<tr><td align='center' colspan=5><h3 class='text-center'>Записи відсутні</h3></td></tr>";}
        $form=str_replace("{prolog_range}",$list,$form);
        $form=str_replace("{date_search}",$date_start,$form);
        $form=str_replace("{disabled}","",$form);
        $form=str_replace("{disabled2}","disabled='disabled'",$form);
        $form=str_replace("{prolog_days}",$users_credit,$form);
        $form=str_replace("{data_pay}",$date_new,$form);
        return $form;
    }

    function editSaleInvoceProlog($client_id,$date_start,$date_new) {$db=DbSingleton::getDb();$answer=0;$err="Помилка збереження даних!";session_start();$user_id=$_SESSION["media_user_id"];$users=new users;
        $users_credit=$users->getUsersAccessCredit($user_id);
        if ($client_id>0) {
            $r=$db->query("select * from J_SALE_INVOICE where client_id='$client_id' and data_pay<='$date_start' and summ_debit!=0;"); $n=$db->num_rows($r);
            for ($i=1;$i<=$n;$i++){
                $id=$db->result($r,$i-1,"id");
                $date_pay_start=$db->result($r,$i-1,"data_pay_start");
                $data_pay=$db->result($r,$i-1,"data_pay");

                if ($date_pay_start=="" || $date_pay_start=="0000-00-00") $datetime1 = new DateTime($data_pay); else $datetime1 = new DateTime($date_pay_start);
                $datetime2 = new DateTime($date_new);
                $interval = $datetime1->diff($datetime2);
                $dec = $interval->format('%a');

                if($users_credit>=$dec) {
                    if ($date_pay_start=="" || $date_pay_start=="0000-00-00") {$set=", data_pay_start='$data_pay' "; $date_pay_start=$data_pay;} else $set="";
                    $db->query("update J_SALE_INVOICE set data_pay='$date_new' $set where id='$id';");
                    $db->query("insert into J_SALE_INVOICE_PROLONGATION (invoice_id,client_id,user_id,date_pay_start,date_pay_new) values ('$id','$client_id','$user_id','$date_pay_start','$date_new');");
                }
            }
            $answer=1; $err="";
        }
        return array($answer,$err);
    }

    function checkSaleInvoiceClients($client_id) {$db=DbSingleton::getDb();
        $r=$db->query("select count(id) as kol from J_SALE_INVOICE where client_id='$client_id';");
        $kol=$db->result($r,0,"kol");
        $kol>0 ? $res=true : $res=false;
        return $res;
    }

    function checkJPayClients($client_id) {$db=DbSingleton::getDb();
        $r=$db->query("select count(id) as kol from J_PAY where client_id='$client_id';");
        $kol=$db->result($r,0,"kol");
        $kol>0 ? $res=true : $res=false;
        return $res;
    }

    function printGeneralSaldoList($client_id,$data_from,$data_to) {$db=DbSingleton::getDb();$form="";$balans_after=0;
        if ($client_id>0){ // $data_from=date("Y-m-01");$data_to=date("Y-m-t"); //current_month
            $form_htm=RD."/tpl/clients_print_saldo.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
            $saldo_end=0; //default value
            $client_cash_id=$this->getClientCash($client_id);
            list($saldo_start,$saldo_cash_id,)=$this->getClientBalansPeriodStart($client_id,$client_cash_id,$data_from,0);

            $saldo_data_start=$data_from;
            $form=str_replace("{saldo_start}",$saldo_start."".$this->getCashAbr($saldo_cash_id),$form);
            $form=str_replace("{saldo_data_start}",$saldo_data_start,$form);

            $r=$db->query("select b.*, mc.abr as cash_name, pmc.abr from B_CLIENT_BALANS_JOURNAL b 
                left outer join CASH mc on mc.id=b.cash_id 
                left outer join CASH pmc on pmc.id=b.pay_cash_id 
            where b.client_id='$client_id' and b.data>='$data_from 00:00:00' and b.data<='$data_to 23:59:59' group by b.doc_type_id, b.doc_id order by b.id asc;");$n=$db->num_rows($r);$list="";
            if ($n>0){
                $sale_invoice=new sale_invoice; $back_clients=new back_clients;//$pay=new pay;
                for ($i=1;$i<=$n;$i++){
                    //$id=$db->result($r,$i-1,"id");
                    $data=$db->result($r,$i-1,"data");
                    //$cash_id=$db->result($r,$i-1,"cash_id");
                    $cash_name=$db->result($r,$i-1,"cash_name");
                    $summ=round($db->result($r,$i-1,"summ"),2);
                    $deb_kre=$db->result($r,$i-1,"deb_kre");
                    $balans_before=$db->result($r,$i-1,"balans_before");
                    $balans_after=$db->result($r,$i-1,"balans_after");
                    $doc_type_id=$db->result($r,$i-1,"doc_type_id"); // 1-Видаткова (SaleInvoice); 2- Оплата(Pay); 3-Автооплата(PayAuto)
                    $doc_id=$db->result($r,$i-1,"doc_id");
                    $pay_cash_name=$db->result($r,$i-1,"pmc.abr");
                    $pay_summ=$db->result($r,$i-1,"pay_summ");
                    $document_name="";
                    if ($doc_type_id==1){ $document_name=$sale_invoice->getSaleInvoiceName($doc_id);/*$function="showSaleInvoiceCard(\"$doc_id\");"; */}
                    if ($doc_type_id==2){ //????????????????????? change summ to 0 if avans pay
                        list($jpay_doc_type_id,$document_name)=$sale_invoice->getJPayName($doc_id);/* $function="viewJpayMoneyPay(\"$doc_id\")";*/
                        if ($jpay_doc_type_id==99) {$summ="";}
                    }
                    if ($doc_type_id==3){ list(,$document_name)=$sale_invoice->getJPayName($doc_id); }
                    if ($doc_type_id==5){ $document_name=$back_clients->getBackClientsName($doc_id); }

                    $debit="";$kredit="";
                    if ($deb_kre==1){
                        $debit=$summ;
                        $saldo_end-=$debit;
                    }
                    if ($deb_kre==2){
                        $kredit=$summ;
                        $saldo_end+=$kredit;
                    }
                    //if ($i==$n){$saldo_data_end=substr($data,0,10);}
                    $list.="<tr align='center'>
                        <td>$i</td>
                        <td>$data</td>
                        <td>$cash_name</td>
                        <td>$balans_before</td>
                        <td>$debit</td>
                        <td>$kredit</td>
                        <td>$balans_after</td>
                        <td>$document_name</td>
                        <td>$pay_summ $pay_cash_name</td>
                    </tr>";
                }
                $saldo_end=round($balans_after,2);
            }

            if ($n==0){$list="<tr><td colspan='8' align='center'>Документи відсутні</td></tr>";}
            $form=str_replace("{list}",$list,$form);
            $form=str_replace("{saldo_end}",$saldo_end."".$this->getCashAbr($saldo_cash_id),$form);
            $saldo_data_end=$data_to;
            $form=str_replace("{saldo_data_end}",$saldo_data_end,$form);
            $form=str_replace("{client_id}",$client_id,$form);
        }
        $mp=new media_print;
        $mp->print_document($form,"A4-L");
        return $form;
    }

}

