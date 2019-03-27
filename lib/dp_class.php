<?php

class dp {

    protected $prefix_new = 'ДП';

    function getDpNote($dp_id) { $db=DbSingleton::getDb();
        $r=$db->query("select * from J_DP_NOTE where dp_id='$dp_id' limit 0,1;"); $n=$db->num_rows($r);
        if ($n>0) $text=$db->result($r,0,"text"); else $text="";
        return $text;
    }

    function setDpNote($dp_id,$text) { $db=DbSingleton::getDb();session_start();$user=$_SESSION["media_user_id"]; $err="Помилка збереження даних!";
        $r=$db->query("select text from J_DP_NOTE where dp_id='$dp_id' limit 0,1;"); $n=$db->num_rows($r);
        if ($n>0) {
            $db->query("update J_DP_NOTE set text='$text', client='$user' where dp_id='$dp_id';");
        }
        else {
            $db->query("insert into J_DP_NOTE (dp_id, text, client) values ('$dp_id', '$text', '$user');");
        }
        $answer=1;
        return array($answer,$err);
    }

    function dropDpNote($dp_id) { $db=DbSingleton::getDb(); $err="Помилка видалення даних!";
        $db->query("delete from J_DP_NOTE where dp_id='$dp_id';");
        $answer=1;
        return array($answer,$err);
    }

    function getMediaUserName($user_id){$db=DbSingleton::getDb();$name="";
        $r=$db->query("select name from media_users where id='$user_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$name=$db->result($r,0,"name");}
        return $name;
    }

    function get_doc_prefix($client_id,$prefix_id){ $db=DbSingleton::getDb();$prefix="Дф-";
        $r=$db->query("select prefix from A_CLIENTS_DOCUMENT_PREFIX where client_id='$client_id' and id='$prefix_id' and status='1' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$prefix=$db->result($r,0,"prefix");}
        return $prefix;
    }

    function get_dp_prefix($dp_id){ $db=DbSingleton::getDb();$prefix="ПР";
        $r=$db->query("select type_id from J_DP where id='$dp_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$type_id=$db->result($r,0,"type_id"); if ($type_id==0){$prefix="В-ПР";}}
        return $prefix;
    }

    function get_doc_client_prefix($client_id){ $db=DbSingleton::getDb();$prefix_id=0;$doc_type_id=40;
        $r=$db->query("select id from A_CLIENTS_DOCUMENT_PREFIX where client_id='$client_id' and doc_type_id='$doc_type_id' and status='1' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$prefix_id=$db->result($r,0,"id");}
        return $prefix_id;
    }

    function get_df_doc_nom_new(){ $db=DbSingleton::getDb();
        $r=$db->query("select max(doc_nom) as doc_nom from J_DP where oper_status='30' and status='1' limit 0,1;");$doc_nom=0+$db->result($r,0,"doc_nom")+1;
        return $doc_nom;
    }

    function getDpName($dp_id){$db=DbSingleton::getDb();
        $r=$db->query("select * from J_DP where id='$dp_id' limit 0,1;");
        $prefix=$db->result($r,0,"prefix");
        $doc_nom=$db->result($r,0,"doc_nom");
        return $prefix."-".$doc_nom;
    }

    function getDpClientName($dp_id){$db=DbSingleton::getDb();$client_name="";
        $r=$db->query("select client_id from J_DP where id='$dp_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $client_id=$db->result($r,0,"client_id");$client_name=$this->getClientName($client_id);
        }
        return $client_name;
    }

    function getTpointAddress($tpoint_id){$db=DbSingleton::getDb();$address="";
        $r=$db->query("select full_name, address from T_POINT where id='$tpoint_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){ $address=$db->result($r,0,"full_name")." ".$db->result($r,0,"address"); }
        return $address;
    }

    function getDpListFilter($key) {$db=DbSingleton::getDb();$list="";
        if ($key=="tpoint") {
            $r=$db->query("select * from T_POINT where status=1;");$n=$db->num_rows($r);
            for ($i=1; $i<=$n; $i++){
                $id=$db->result($r,$i-1,"id");
                $name=$db->result($r,$i-1,"full_name");
                $list.="<option value='$id'>$name</option>";
            }
        }
        if ($key=="user") {
            $r=$db->query("select * from media_users;");$n=$db->num_rows($r);
            for ($i=1; $i<=$n; $i++){
                $id=$db->result($r,$i-1,"id");
                $name=$db->result($r,$i-1,"name");
                $list.="<option value='$id'>$name</option>";
            }
        }
        if ($key=="status") {
            $r=$db->query("select * from manual where `key`='status_dp' or `key`='status_dps';");$n=$db->num_rows($r);
            for ($i=1; $i<=$n; $i++){
                $id=$db->result($r,$i-1,"id");
                $name=$db->result($r,$i-1,"mcaption");
                if ($id!=80 && $id!=93)
                $list.="<option value='$id'>$name</option>";
            }
        }
        return $list;
    }

    function newDpCard(){$db=DbSingleton::getDb();session_start();$user_id=$_SESSION["media_user_id"];
        $r=$db->query("select max(id) as mid from J_DP;");$dp_id=0+$db->result($r,0,"mid")+1;
        $doc_nom=$this->get_df_doc_nom_new();
        $db->query("insert into J_DP (`id`,`prefix`,`doc_nom`,`user_id`,`data`) values ('$dp_id','$this->prefix_new','$doc_nom','$user_id',CURDATE());");
        return $dp_id;
    }

    function newDpFromDp($from_dp_id,$tpoint_id,$dp_storage_id_from){$db=DbSingleton::getDb();$dp_id=0;
        $r=$db->query("select * from J_DP where id='$from_dp_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $doc_type_id=$db->result($r,0,"doc_type_id");
            $client_id=$db->result($r,0,"client_id");
            $client_conto_id=$db->result($r,0,"client_conto_id");
            $data_pay=$db->result($r,0,"data_pay");
            $cash_id=$db->result($r,0,"cash_id");
            $usd_to_uah=$db->result($r,0,"usd_to_uah");
            $eur_to_uah=$db->result($r,0,"eur_to_uah");
            $vat_use=$db->result($r,0,"vat_use");
            $delivery_type_id=$db->result($r,0,"delivery_type_id");
            $carrier_id=$db->result($r,0,"carrier_id");
            $delivery_address=$db->result($r,0,"delivery_address");
            $dp_user_id=$db->result($r,0,"user_id");

            $r=$db->query("select max(id) as mid from J_DP;");$dp_id=0+$db->result($r,0,"mid")+1;
            $doc_nom=$this->get_df_doc_nom_new();
            $db->query("insert into J_DP (`id`, `prefix`, `doc_nom`, `user_id`, `data`, `doc_type_id`, `tpoint_id`, `client_id`, `client_conto_id`, `data_pay`, `cash_id`, `usd_to_uah`, `eur_to_uah`, `vat_use`, `delivery_type_id`, `carrier_id`, `delivery_address`) values ('$dp_id', '$this->prefix_new', '$doc_nom', '$dp_user_id' ,CURDATE(), '$doc_type_id', '$tpoint_id', '$client_id', '$client_conto_id', '$data_pay', '$cash_id', '$usd_to_uah', '$eur_to_uah', '$vat_use', '$delivery_type_id', '$carrier_id', '$delivery_address');");

            $r1=$db->query("select id from orders_new where dp_id='$from_dp_id' limit 0,1;");$n1=$db->num_rows($r1);
            if ($n1==1){
                $order_id=$db->result($r1,0,"id");
                if ($order_id>0){
                    $db->query("update orders_new set dp_id='$from_dp_id,$dp_id' where id='$order_id';");
                }
            }
        }
        return $dp_id;
    }

    function show_dp_list($status=null){$db=DbSingleton::getDb();$gmanual=new gmanual;session_start(); $ses_tpoint_id=$_SESSION["media_tpoint_id"]; $media_user_id=$_SESSION["media_user_id"];$media_role_id=$_SESSION["media_role_id"];
        $where=" and (j.tpoint_id='$ses_tpoint_id' or j.user_id='$media_user_id') and ((j.status_dp!=0 and j.summ>0) || (j.status_dp=81)) "; $limit="limit 0,500";
        if (!$status) $where_status=" and status_dp!=81 "; else $where_status=""; $percent=0;
        if ($media_user_id==1){$where=" and j.status_dp!=0";$limit="";} if ($media_role_id==1){$where=" and ((j.status_dp!=0 and j.summ>0) || (j.status_dp=81)) "; $limit="";}

        $r=$db->query("select j.*, t.name as tpoint_name, dt.mvalue as doc_type_name, CASH.name as cash_name, c.name as client_name, dlv.mcaption as delivery_type_name 
        from J_DP j
            left outer join manual dt on (dt.key='client_sale_type' and dt.id=j.doc_type_id)
            left outer join T_POINT t on t.id=j.tpoint_id
            left outer join A_CLIENTS c on c.id=j.client_conto_id
            left outer join CASH on CASH.id=j.cash_id
            left outer join manual dlv on (dlv.key='delivery_type' and dlv.id=j.delivery_type_id)
        where j.status=1 $where $where_status order by j.id desc $limit;");$n=$db->num_rows($r);$list="";

        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $doc_type_name=$db->result($r,$i-1,"doc_type_name");
            $dp_note=$this->getDpNote($id);
            $prefix=$db->result($r,$i-1,"prefix");
            $doc_nom=$db->result($r,$i-1,"doc_nom");
            $tpoint_name=$db->result($r,$i-1,"tpoint_name");
            $client_name=$db->result($r,$i-1,"client_name");
            $cash_name=$db->result($r,$i-1,"cash_name");
            $summ=$db->result($r,$i-1,"summ");
            $delivery_type_name=$db->result($r,$i-1,"delivery_type_name");
            $data=$db->result($r,$i-1,"data");
            $user_name=$this->getMediaUserName($db->result($r,$i-1,"user_id"));
            $status_dp=$db->result($r,$i-1,"status_dp");
            if ($status_dp!=80){$status_dp_name=$gmanual->get_gmanual_caption($status_dp);} else {$status_dp_name=$this->getDpStrStatus($id);}

            $color=" background:{color};";$clr="";
            if ($status_dp==79){$clr="pink";}
            if ($status_dp==80){$clr=$this->getStatusDP($id);}
            if ($status_dp==81){$clr="";}
            $color=str_replace("{color}",$clr,$color);

            $function="showDpCard(\"$id\")";
            $list.="<tr style='cursor:pointer; $color' onClick='$function'>
                <td>$doc_type_name</td>
                <td>$tpoint_name</td>
                <td>$prefix - $doc_nom</td>
                <td align='center'>$data</td>
                <td>$client_name</td>
                <td>$cash_name</td>
                <td>$summ</td>
                <td>$delivery_type_name</td>
                <td>$user_name</td>
                <td>$dp_note</td>
                <td title='$status_dp'>$status_dp_name</td>
                <td>$percent</td>
            </tr>";
        }
        return $list;
    }

    function getStatusDP($dp_id) {$db=DbSingleton::getDb(); $color="lightyellow"; $k=0;
        $r=$db->query("select * from J_DP_STR where dp_id='$dp_id';");
        $n=$db->num_rows($r);
        if ($n>0) {
            for ($i=1;$i<=$n;$i++){
                $status_dps=$db->result($r,$i-1,"status_dps");
                if ($status_dps==96) $k++;
            }
        }
        if ($k>0) $color="lightgreen";
        return $color;
    }

    function show_dp_list_filter($status,$filstatus,$filauthor,$filtpoint){$db=DbSingleton::getDb();$gmanual=new gmanual;session_start();$ses_tpoint_id=$_SESSION["media_tpoint_id"];$media_user_id=$_SESSION["media_user_id"];$media_role_id=$_SESSION["media_role_id"];
        $where=" and (j.tpoint_id='$ses_tpoint_id' or j.user_id='$media_user_id') and j.status_dp!=0 and j.summ>0 ";
        if (!$status) $where_status=" and status_dp!=81 "; else $where_status="";
        if ($media_user_id==1){$where=" and j.status_dp!=0";}if ($media_role_id==1){$where=" and j.status_dp!=0 and j.summ>0 ";}
        $where_filter="";$percent=0;

        if ($filstatus!="0")
            if ($filstatus>"81") {
                $where_filter.=" and jstr.status_dps='$filstatus'";
            }
            else $where_filter.=" and j.status_dp='$filstatus'";

        if ($filauthor!="0") $where_filter.=" and j.user_id='$filauthor'";
        if ($filtpoint!="0") $where_filter.=" and j.tpoint_id='$filtpoint'";
        $r=$db->query("select j.*, t.name as tpoint_name, dt.mvalue as doc_type_name, CASH.name as cash_name, c.name as client_name, dlv.mcaption as delivery_type_name 
        from J_DP j
            left outer join J_DP_STR jstr on jstr.dp_id=j.id 
            left outer join manual dt on (dt.key='client_sale_type' and dt.id=j.doc_type_id)
            left outer join T_POINT t on t.id=j.tpoint_id
            left outer join A_CLIENTS c on c.id=j.client_conto_id
            left outer join CASH on CASH.id=j.cash_id
            left outer join manual dlv on (dlv.key='delivery_type' and dlv.id=j.delivery_type_id)
        where j.status=1 $where $where_status $where_filter group by j.id order by j.id desc limit 0,500;");$n=$db->num_rows($r);$list="";

        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $doc_type_name=$db->result($r,$i-1,"doc_type_name");
            $dp_note=$this->getDpNote($id);
            $prefix=$db->result($r,$i-1,"prefix");
            $doc_nom=$db->result($r,$i-1,"doc_nom");
            $tpoint_name=$db->result($r,$i-1,"tpoint_name");
            $client_name=$db->result($r,$i-1,"client_name");
            $cash_name=$db->result($r,$i-1,"cash_name");
            $summ=$db->result($r,$i-1,"summ");
            $delivery_type_name=$db->result($r,$i-1,"delivery_type_name");
            $data=$db->result($r,$i-1,"data");
            $user_name=$this->getMediaUserName($db->result($r,$i-1,"user_id"));
            $status_dp=$db->result($r,$i-1,"status_dp");
            if ($status_dp!=80){$status_dp_name=$gmanual->get_gmanual_caption($status_dp);} else {$status_dp_name=$this->getDpStrStatus($id);}

            $color=" background:{color};";$clr="";
            if ($status_dp==79){$clr="pink";}
            if ($status_dp==80){$clr=$this->getStatusDP($id);}
            if ($status_dp==81){$clr="";}
            $color=str_replace("{color}",$clr,$color);

            $function="showDpCard(\"$id\")";
            $list.="<tr style='cursor:pointer; $color' onClick='$function'>
                <td>$doc_type_name</td>
                <td>$tpoint_name</td>
                <td>$prefix - $doc_nom</td>
                <td align='center'>$data</td>
                <td>$client_name</td>
                <td>$cash_name</td>
                <td>$summ</td>
                <td>$delivery_type_name</td>
                <td>$user_name</td>
                <td>$dp_note</td>
                <td title='$status_dp'>$status_dp_name</td>
                <td>$percent</td>
            </tr>";
        }
        return $list;
    }

    function getKoursData(){$db=DbSingleton::getDb();$slave=new slave;$usd_to_uah=0;$eur_to_uah=0;
        $r=$db->query("select kours_value from J_KOURS where cash_id='2' and in_use='1' order by id desc limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$usd_to_uah=$slave->to_money(round($db->result($r,0,"kours_value"),2));}
        $r=$db->query("select kours_value from J_KOURS where cash_id='3' and in_use='1' order by id desc limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$eur_to_uah=$slave->to_money(round($db->result($r,0,"kours_value"),2));}
        return array($usd_to_uah,$eur_to_uah);
    }

    function showDpCard($dp_id){$db=DbSingleton::getDb();$gmanual=new gmanual; session_start();$user_id=$_SESSION["media_user_id"];$user_name=$_SESSION["user_name"];
        $form="";$form_htm=RD."/tpl/dp_card.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);} $comment=""; $cells_show=""; $type_name=""; $label_art_unknown="";
        $prefix=""; $doc_nom=0;
        $r=$db->query("select * from J_DP j where j.id='$dp_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==0){$form_htm=RD."/tpl/access_deny.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);} }
        if ($n==1){ $this->updateDpSumm($dp_id);
            $doc_type_id =$db->result($r,0,"doc_type_id");$doc_type_list=$this->getDocTypeSelectList($doc_type_id);
            $prefix=$db->result($r,0,"prefix");
            $doc_nom=$db->result($r,0,"doc_nom");
            $user_use=$db->result($r,0,"user_use");
            if ($user_id!=$user_use && $user_use>0){
                $form_htm=RD."/tpl/dp_use_deny.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
                $form=str_replace("{user_name}",$this->getMediaUserName($user_use),$form);
                $admin_unlock="";
                if ($user_id==1 || $user_id==2){$admin_unlock="<button class='btn btn-sm btn-warning' onClick='unlockDpCard(\"$dp_id\");'><i class='fa fa-unlock'></i> Розблокувати</button>";}
                $form=str_replace("{admin_unlock}",$admin_unlock,$form);
            }
            if ($user_id==$user_use || $user_use==0){
                $data=$db->result($r,0,"data");if ($data=="0000-00-00"){$data="";}
                $cash_id=$db->result($r,0,"cash_id");$cash_list=$this->showCashListSelect($cash_id,1);
                $data_pay=$db->result($r,0,"data_pay");
                $type_id=$db->result($r,0,"type_id");if ($type_id==1){$type_name="Між складами";}
                $tpoint_id=$db->result($r,0,"tpoint_id"); $tpoint_name=$this->getTpointName($tpoint_id);
                $client_id=$db->result($r,0,"client_id");$client_name=$this->getClientName($client_id);
                $client_conto_id=$db->result($r,0,"client_conto_id");$client_conto_list=$this->getClientContoSelectList($client_id,$client_conto_id);
                $usd_to_uah=$db->result($r,0,"usd_to_uah");
                $eur_to_uah=$db->result($r,0,"eur_to_uah");

                list($usd_to_uah_new,$eur_to_uah_new)=$this->getKoursData();
    //			if($usd_to_uah!=$usd_to_uah_new){$usd_to_uah=$usd_to_uah_new;}
    //			if($eur_to_uah!=$eur_to_uah_new){$eur_to_uah=$eur_to_uah_new;}

                $summ=$db->result($r,0,"summ");
                $delivery_type_id=$db->result($r,0,"delivery_type_id");$delivery_type_list=$gmanual->showGmanualSelectList("delivery_type",$delivery_type_id);
                $carrier_dis=""; if ($delivery_type_id!=60){$carrier_dis="disabled hidden";}
                $carrier_id=$db->result($r,0,"carrier_id");$carrier_list=$this->getCarrierSelectList($carrier_id);
                $delivery_address=$db->result($r,0,"delivery_address");
                $volume=$db->result($r,0,"volume");
                $weight=$db->result($r,0,"weight");
                $status_dp=$db->result($r,0,"status_dp");

                if ($status_dp==79) {
                    $usd_to_uah=$usd_to_uah_new;
                    $eur_to_uah=$eur_to_uah_new;
                } else {
                    if($usd_to_uah==0){$usd_to_uah=$usd_to_uah_new;}
                    if($eur_to_uah==0){$eur_to_uah=$eur_to_uah_new;}
                }
                if ($status_dp==80){
                    $form=str_replace("{hide_new_row_button}"," disabled style=\"visibility:hidden;\"",$form);
                    $form=str_replace("{oper_disabled}"," disabled",$form);
                    $form=str_replace("{oper_disabled2}"," ",$form);
                }
                if ($status_dp<82){
                    list($a1,$a1,$data_pay)=$this->getClientPaymentDelay($client_conto_id);
                }
                $form=str_replace("{oper_disabled}","",$form);
                $form=str_replace("{oper_disabled2}","disabled",$form);
                $form=str_replace("{hide_new_row_button}","",$form);
                $form=str_replace("{dp_id}",$dp_id,$form);
                $form=str_replace("{data_pay}",$data_pay,$form);
                $form=str_replace("{doc_type_list}",$doc_type_list,$form);

                $dp_storage_list=$this->getDpStorageList();
                $form=str_replace("{dp_storage_list}",$dp_storage_list,$form);
                $client=new clients;

                $balans_client=$client->getClientGeneralSaldo($client_id);
                if ($client_id!=$client_conto_id && $client_conto_id!=0)
                $balans_conto="<b>Баланс юридичної особи: </b>".$client->getClientGeneralSaldo($client_conto_id);
                else $balans_conto="";

                $form=str_replace("{balans_client}",$balans_client,$form);
                $form=str_replace("{balans_conto}",$balans_conto,$form);
                $form=str_replace("{cash_id}",$cash_id,$form);
                $form=str_replace("{cash_list}",$cash_list,$form);
                $form=str_replace("{tpoint_id}",$tpoint_id,$form);
                $form=str_replace("{tpoint_name}",$tpoint_name,$form);
                $form=str_replace("{client_id}",$client_id,$form);
                $form=str_replace("{client_name}",$client_name,$form);
                $form=str_replace("{client_conto_id}",$client_conto_id,$form);
                $form=str_replace("{client_conto_list}",$client_conto_list,$form);
                $form=str_replace("{dp_summ}",$summ,$form);
                $form=str_replace("{usd_to_uah}",$usd_to_uah,$form);
                $form=str_replace("{eur_to_uah}",$eur_to_uah,$form);
                $form=str_replace("{delivery_type_list}",$delivery_type_list,$form);
                $form=str_replace("{carrier_dis}",$carrier_dis,$form);
                $form=str_replace("{dp_note}",$this->getDpNote($dp_id),$form);
                $form=str_replace("{carrier_list}",$carrier_list,$form);
                $form=str_replace("{delivery_address}",$delivery_address,$form);
                $form=str_replace("{data}",$data,$form);
                $form=str_replace("{type_id}",$type_id,$form);
                $form=str_replace("{type_name}",$type_name,$form);
                $form=str_replace("{weight}",$weight,$form);
                $form=str_replace("{volume}",$volume,$form);
                $form=str_replace("{comment}",$comment,$form);
                $form=str_replace("{cells_show}",$cells_show,$form);
                $form=str_replace("{ses_user_discount}",$_SESSION["user_discount"],$form);

                $dpLocalListCount=$this->dpStorselCount($dp_id);
                $dpRemoteListCount=$this->dpJmovingCount($dp_id);

                $form=str_replace("{dpLocalListCount}",$dpLocalListCount,$form);
                $form=str_replace("{dpRemoteListCount}",$dpRemoteListCount,$form);

                list($dpChildsList,$kol_str_row)=$this->showDpStrList($dp_id,$status_dp,$client_id,$cash_id,$usd_to_uah,$eur_to_uah);
                $form=str_replace("{dpChildsList}",$dpChildsList,$form);
                $form=str_replace("{kol_str_row}",$kol_str_row,$form);

                $form=str_replace("{weight}",$weight,$form);
                $form=str_replace("{volume}",$volume,$form);
                $storage_to_disabled=""; if ($status_dp!=79){$storage_to_disabled=" disabled";}
                $form=str_replace("{storage_to_disabled}",$storage_to_disabled,$form);
                $form=str_replace("{my_user_id}",$user_id,$form);
                $form=str_replace("{my_user_name}",$user_name,$form);

                list($kol_comments,$label_comments)=$this->labelCommentsCount($dp_id);
                $form=str_replace("{labelCommentsCount}",$label_comments,$form);
                $form=str_replace("{labelArticlesUnKnownCount}",$label_art_unknown,$form);

                if ($doc_type_id=="61") $form=str_replace("{oper_visible}","",$form); else
                $form=str_replace("{oper_visible}"," disabled style=\"visibility:hidden;\"",$form);
                $form=str_replace("{site_order_label}",$this->getDistinctOrdersItems($dp_id),$form);
                $this->setdpCardUserAccess($dp_id,$user_id);
            }
        }
        return array($form,$prefix."-".$doc_nom);
    }

    function getDpStorageList() {$db=DbSingleton::getDb(); $list="";
        $r=$db->query("select * from STORAGE;");$n=$db->num_rows($r);
        for ($i=1; $i<=$n; $i++){
            $name=$db->result($r,$i-1,"name");
            $list.="<option value='$i'>$name</option>";
        }
        return $list;
    }

    function unlockDpCard($dp_id){session_start();$user_id=$_SESSION["media_user_id"];$answer=0;
        if ($user_id==1 || $user_id==2){$db=DbSingleton::getDb();
            $db->query("update J_DP set user_use='0' where id='$dp_id';");
            $answer=1;
        }
        return $answer;
    }

    function closeDpCard($dp_id){session_start();$user_id=$_SESSION["media_user_id"];$
        $this->unsetDpCardUserAccess($dp_id,$user_id);
        $answer=1;
        return $answer;
    }

    function setDpCardUserAccess($dp_id,$user_id){$db=DbSingleton::getDb();
        if($dp_id>0 && $user_id>0){
            $db->query("update J_DP set user_use='$user_id' where id='$dp_id';");
        }
        return;
    }

    function unsetDpCardUserAccess($dp_id,$user_id){$db=DbSingleton::getDb();
        if($dp_id>0 && $user_id>0){
            $db->query("update J_DP set user_use='0' where id='$dp_id';");
        }
        return;
    }

    function clearDpStr($dp_id){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;$answer=0;$err="";
        $dp_id=$slave->qq($dp_id);
        $r=$db->query("select oper_status from J_DP where id='$dp_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $oper_status=$db->result($r,0,"oper_status");
            if ($oper_status==30) {

                $r1=$db->query("select * from J_DP_STR where dp_id='$dp_id';");$n1=$db->num_rows($r1);
                for ($i1=1; $i1<=$n1; $i1++){
                    $dp_str_id=$db->result($r1,$i1-1,"id");
                    $status_dps_str=$db->result($r1,$i1-1,"status_dps");
                    if ($status_dps_str==93){
                        $art_id=$db->result($r1,$i1-1,"art_id");
                        $amount=$db->result($r1,$i1-1,"amount");
                        $storage_id_from=$db->result($r1,$i1-1,"storage_id_from");

                        $rs=$dbt->query("select * from T2_ARTICLES_STRORAGE where ART_ID='$art_id' and STORAGE_ID='$storage_id_from' limit 0,1;");$ns=$dbt->num_rows($rs);
                        if ($ns==1){
                            $reserv_amount_s=$dbt->result($rs,0,"RESERV_AMOUNT");
                            $amount_s=$dbt->result($rs,0,"AMOUNT");
                            $reserv_amount_s-=$amount;
                            $amount_s+=$amount;
                            $dbt->query("update T2_ARTICLES_STRORAGE set `AMOUNT`='$amount_s', `RESERV_AMOUNT`='$reserv_amount_s' where ART_ID='$art_id' and STORAGE_ID='$storage_id_from' limit 1;");
                            $db->query("delete from J_DP_STR where id='$dp_str_id' and dp_id='$dp_id' limit 1;");
                            $this->updateDpWeightVolume($dp_id);
                            $dp_summ=$this->updateDpSumm($dp_id);
                        }
                        $db->query("delete from J_DP_STR where id='$dp_str_id' and dp_id='$dp_id' limit 1;");
                    }
                }
                $db->query("update J_DP set summ=0 where id='$dp_id' limit 1;");
                $answer=1;$err="";
            } else {$answer=0;$err="Документ заблоковано. Зміни вносити заборонено.";}
        }
        return array($answer,$err);
    }

    function setDpClient($dp_id,$client_id){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка збереження даних!";
        $dp_id=$slave->qq($dp_id);$client_id=$slave->qq($client_id);
        if ($dp_id>0 && $client_id>0){
            $db->query("update J_DP set `client_id`='$client_id',`client_conto_id`='$client_id' where `id`='$dp_id';");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function showDpClientList($sel_id){$db=DbSingleton::getDb();
        $form="";$form_htm=RD."/tpl/clients_parrent_tree.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select c.*,ot.name as org_type_name, t2cn.COUNTRY_NAME, t2st.STATE_NAME, t2rg.REGION_NAME, t2ct.CITY_NAME,acc.tpoint_id, tp.name as tpoint_name   
        from A_CLIENTS c 
            left outer join A_ORG_TYPE ot on ot.id=c.org_type 
            left outer join T2_COUNTRIES t2cn on t2cn.COUNTRY_ID=c.country 
            left outer join T2_STATE t2st on t2st.STATE_ID=c.state
            left outer join T2_REGION t2rg on t2rg.REGION_ID=c.region
            left outer join T2_CITY t2ct on t2ct.CITY_ID=c.city
            left outer join A_CLIENTS_CATEGORY cc on cc.client_id=c.id
            left outer join A_CATEGORY ac on ac.id=cc.category_id
            left outer join A_CLIENTS_CONDITIONS acc on acc.client_id=c.id 
            left outer join T_POINT tp on tp.id=acc.tpoint_id 
        where c.status=1 and ac.id>0 group by c.id;");$n=$db->num_rows($r);$list="";

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
            $tpoint_id=$db->result($r,$i-1,"tpoint_id");
            $fname=str_replace('"',"`",$name);
            $cur=""; $fn=" onClick='setDpClient(\"$id\", \"$fname\",\"$tpoint_id\",\"tpoint_name\")'";
            //$fn=" onClick='setDpClient(\"$id\", \"".base64_encode(iconv("windows-1251","utf-8",$name))."\",\"$tpoint_id\",\"$tpoint_name\")'";
            //if ($id==$prnt_id){$cur="background-color:#FFFF00;";}
            if ($id==$sel_id){$cur="background-color:#0CF;";}
            $list.="<tr style='$cur cursor:pointer;' $fn>
                <td></td>
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

    function getDpTpointInfo($client_id) {$db=DbSingleton::getDb();
        $r=$db->query("select c.*, acc.tpoint_id, tp.name as tpoint_name from A_CLIENTS c 
            left outer join A_CLIENTS_CONDITIONS acc on acc.client_id=c.id 
            left outer join T_POINT tp on tp.id=acc.tpoint_id 
        where c.id='$client_id' group by c.id limit 0,1;");
        $name=$db->result($r,0,"name");
        $tpoint_id=$db->result($r,0,"tpoint_id");
        $tpoint_name=$db->result($r,0,"tpoint_name");
        $fname=str_replace('"',"`",$name);
        return array($fname,$tpoint_id,$tpoint_name);
    }

    function filterDpClientsList($sel_id,$client_id,$client_name,$phone,$email,$state_id){$db=DbSingleton::getDb();$where="";
        if ($client_id>0 && $client_id!=""){$where.=" and c.id='$client_id'";}
        if ($client_name!=""){$where.=" and c.name like '%$client_name%'";}
        if ($phone!=""){$where.=" and c.phone like '%$phone%'";}
        if ($email!=""){$where.=" and c.email like '%$email%'";}
        if ($state_id>0 && $state_id!=""){$where.=" and c.state='$state_id'";}

        $r=$db->query("select c.*,ot.name as org_type_name, t2cn.COUNTRY_NAME, t2st.STATE_NAME, t2rg.REGION_NAME, t2ct.CITY_NAME,acc.tpoint_id, tp.name as tpoint_name   
        from A_CLIENTS c 
            left outer join A_ORG_TYPE ot on ot.id=c.org_type 
            left outer join T2_COUNTRIES t2cn on t2cn.COUNTRY_ID=c.country 
            left outer join T2_STATE t2st on t2st.STATE_ID=c.state
            left outer join T2_REGION t2rg on t2rg.REGION_ID=c.region
            left outer join T2_CITY t2ct on t2ct.CITY_ID=c.city
            left outer join A_CLIENTS_CATEGORY cc on cc.client_id=c.id
            left outer join A_CATEGORY ac on ac.id=cc.category_id
            left outer join A_CLIENTS_CONDITIONS acc on acc.client_id=c.id 
            left outer join T_POINT tp on tp.id=acc.tpoint_id 
        where c.status=1 and ac.id>0 $where group by c.id;");$n=$db->num_rows($r);$list="n=$n";

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
            $tpoint_id=$db->result($r,$i-1,"tpoint_id");
            $cur="";$fn=" onClick='setDpClient(\"$id\", \"".base64_encode(iconv("windows-1251","utf-8",$name))."\",\"$tpoint_id\",\"tpoint_name\")'";
            //if ($id==$prnt_id){$cur="background-color:#FFFF00;";}
            if ($id==$sel_id){$cur="background-color:#0CF;";}
            $list.="<tr style='$cur cursor:pointer;' $fn>
                <td></td>
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

    function unlinkDpClient($dp_id){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка збереження даних!";
        $dp_id=$slave->qq($dp_id);
        if ($dp_id>0){
            $db->query("update J_DP set `client_id`='0' where `id`='$dp_id';");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function showDpTpointList($sel_id){$db=DbSingleton::getDb();
        $form="";$form_htm=RD."/tpl/tpoint_tree.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select t.*, t2cn.COUNTRY_NAME, t2st.STATE_NAME, t2rg.REGION_NAME, t2ct.CITY_NAME
        from T_POINT t 
            left outer join T2_COUNTRIES t2cn on t2cn.COUNTRY_ID=t.country 
            left outer join T2_STATE t2st on t2st.STATE_ID=t.state
            left outer join T2_REGION t2rg on t2rg.REGION_ID=t.region
            left outer join T2_CITY t2ct on t2ct.CITY_ID=t.city
        where t.status=1;");$n=$db->num_rows($r);$list="";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"name");
            $state=$db->result($r,$i-1,"STATE_NAME");
            $region=$db->result($r,$i-1,"REGION_NAME");
            $city=$db->result($r,$i-1,"CITY_NAME");
            $address=$db->result($r,$i-1,"address");
            $chief=$db->result($r,$i-1,"chief");
            $worker_name=$this->getMediaUserName($chief);
            $cur="";$fn=" onClick='setDpTpoint(\"$id\", \"$name\")'";
            //if ($id==$prnt_id){$cur="background-color:#FFFF00;";}
            if ($id==$sel_id){$cur="background-color:#0CF;";}
            $list.="<tr style='$cur cursor:pointer;' $fn>
                <td>$id</td>
                <td>$name</td>
                <td>$state</td>
                <td>$region</td>
                <td>$city</td>
                <td>$address</td>
                <td>$worker_name</td>
            </tr>";
        }
        $form=str_replace("{list}",$list,$form);
        return $form;
    }

    function unlinkDpTpoint($dp_id){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка збереження даних!";
        $dp_id=$slave->qq($dp_id);
        if ($dp_id>0){
            $db->query("update J_DP set `tpoint_id`='0' where `id`='$dp_id';");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function checkDpSelectAllStatus($dp_id,$statusdp){$db=DbSingleton::getDb();$ex=1;
        $r=$db->query("select id,status_dp from J_DP_SELECT where dp_id='$dp_id' and status='1';");$n=$db->num_rows($r);
        if ($n==0){$ex=0;}
        for ($i=1;$i<=$n;$i++){
            $status_dp=$db->result($r,$i-1,"status_dp");
            if ($status_dp!=$statusdp){$ex=0; $i=$n+1;}
        }
        $r=$db->query("select id,status_dp from J_DP where id='$dp_id' and status='1' limit 0,1;");$n=$db->num_rows($r);
        if ($n==0){$ex=0;}
        if ($n==1){
            $status_dp=$db->result($r,0,"status_dp");
            if ($status_dp==79){$ex=0;}
        }
        return $ex;
    }

    function getClientArticleMaxDiscount($art_id,$client_id,$price){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();session_start();$max_manager_discount=$_SESSION["user_discount"];$max_discount_persent=0;$max_discount_price=0;
        $r=$dbt->query("select t2aps.OPER_PRICE, t2apr.minMarkup 
        from T2_ARTICLES_PRICE_STOCK t2aps 
            left outer join T2_ARTICLES_PRICE_RATING t2apr on (t2apr.art_id=t2aps.ART_ID)
        where t2aps.ART_ID='$art_id' and in_use='1' limit 0,1;");$n=$dbt->num_rows($r);
        if ($n==1){
            $oper_price=$db->result($r,0,"OPER_PRICE");
            $minMarkup=$db->result($r,0,"minMarkup");
            if ($minMarkup<=0){
                $max_discount_persent=0;$max_discount_price=$price;
            }
            if ($minMarkup>0){
                $p1=$oper_price+round(($oper_price*$minMarkup/100),2);
                $p2=round($price-$price*$max_manager_discount/100,2);
                if ($p2>$p1){
                    $max_discount_persent=$max_manager_discount;
                    $max_discount_price=round($price-$price*$max_discount_persent/100,2);
                }
                if ($p2<=$p1){
                    $max_discount=round(($p1/$price-1)*100*(-1),2);
                    $max_discount_price=round($price-$price*$max_discount/100,2);
                    $max_discount_persent=$max_discount;
                }
            }
        }
        return array($max_discount_persent,$max_discount_price);
    }

    function getIncomeStatusDpsName($art_id,$amount,$suppl_id){$db=DbSingleton::getDb();$inc_status_name="Передано у замовлення постачальнику";
        $rs=$db->query("select instr.art_id,instr.amount, inc.storage_id, inc.storage_cells_id,inc.time_stamp from J_INCOME_STR instr 
        join J_INCOME inc on instr.income_id=inc.id
        where inc.status=1 and inc.oper_status=31 and inc.client_seller='$suppl_id' and instr.art_id='$art_id' order by inc.id desc limit 0,3;");$ns=$db->num_rows($rs);
        if ($ns>0){$inc_status_name="Надійшло від постачальника ";
            for ($is=1;$is<=$ns;$is++){
                //$inc_art_id=$db->result($rs,$is-1,"art_id");
                $inc_amount=$db->result($rs,$is-1,"amount");
                $storage_id=$db->result($rs,$is-1,"storage_id");
                $storage_cells_id=$db->result($rs,$is-1,"storage_cells_id");
                $inc_data=$db->result($rs,$is-1,"time_stamp");
                $inc_status_name.=" $inc_amount"."шт. $inc_data на скл.".$this->getStorageName($storage_id)." ".$this->getStorageCellName($storage_cells_id);
            }
        }
        return $inc_status_name;
    }

    function getDpStrStatus($dp_id){$db=DbSingleton::getDb();$status_name="";
        $r=$db->query("select j.*, dps.mcaption as status_dps_name 
        from J_DP_STR j 
            left outer join manual dps on (dps.id=j.status_dps and dps.key='status_dps') 
        where j.dp_id='$dp_id' group by j.status_dps order by j.status_dps desc;");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $status_id=$db->result($r,$i-1,"status_dps");//$inc_status_name="";
            if ($status_id==139){
                $art_id=$db->result($r,$i-1,"art_id");
                $suppl_id=$db->result($r,$i-1,"suppl_id");
                $amount=$db->result($r,$i-1,"amount");
                //$suppl_storage_id=$db->result($r,$i-1,"suppl_storage_id");
                $inc_status_name=$this->getIncomeStatusDpsName($art_id,$amount,$suppl_id);
                $status_name.="$i)".$inc_status_name;
            }
            else{
                $status_name.="$i)".$db->result($r,$i-1,"status_dps_name");
            }
            if ($i>0 && $i<$n){$status_name.="<br>";}
        }
        return $status_name;
    }

    function showDpStrList($dp_id,$status_dp,$client_id,$cash_id,$usd_to_uah,$euro_to_uah){$db=DbSingleton::getDb();$slave=new slave;$list="";//$cat=new catalogue;
        if ($status_dp==""){$status_dp=79;} if ($client_id==""){$client_id=$this->getDpClient($dp_id);}
        if ($cash_id==""){$cash_id=$this->getDpCashId($dp_id);} if ($usd_to_uah=="" || $euro_to_uah==""){list($usd_to_uah,$euro_to_uah)=$this->getKoursData();}
        $tpoint_id=$this->getDpTpoint($dp_id);$amount_bug_info="";$delivery_info="";

        $r=$db->query("select j.*, m.mcaption as reserv_type_caption, s.name as storage_name, dps.mcaption as status_dps_name 
        from J_DP_STR j 
            left outer join manual m on (m.id= j.reserv_type_id and m.key='reserv_type') 
            left outer join manual dps on (dps.id=j.status_dps and dps.key='status_dps') 
            left outer join STORAGE s on (s.id= j.storage_id_from) 
        where j.dp_id='$dp_id' order by j.id asc;");$n=$db->num_rows($r);$kl_rw=$n;$sum_weight=0;$sum_volume=0;$summ_dp=0;

        for ($i=1;$i<=$kl_rw;$i++){
            $id=$db->result($r,$i-1,"id");$dp_str_id=$id;
            $reserv_type_id=$db->result($r,$i-1,"reserv_type_id");$reserv_type_color="primary";if ($reserv_type_id==68){$reserv_type_color="warning";}if ($reserv_type_id==69){$reserv_type_color="danger";}
            //$reserv_type_caption=$db->result($r,$i-1,"reserv_type_caption");
            $storage_id_from=$db->result($r,$i-1,"storage_id_from");
            $storage_name=$db->result($r,$i-1,"storage_name");

            $suppl_id=$db->result($r,$i-1,"suppl_id");
            $suppl_storage_id=$db->result($r,$i-1,"suppl_storage_id");
            if ($suppl_id>0){$storage_name=$suppl_id.".".$suppl_storage_id;}

            $art_id=$db->result($r,$i-1,"art_id");
            $article_nr_displ=$db->result($r,$i-1,"article_nr_displ");
            $brand_id=$db->result($r,$i-1,"brand_id");$brand_name=$this->getBrandName($brand_id);
            $amount=$db->result($r,$i-1,"amount");$amount_dp=$amount;
            $amount_collect=$db->result($r,$i-1,"amount_collect");
            $amount_bug_db=$db->result($r,$i-1,"amount_bug");$amount_bug=0;
            if ($amount_collect>0 || $amount_bug_db!=0){$amount_dp=$amount_collect;}
            if ($amount_collect>0){	$amount_bug=$amount-$amount_collect;}
            if ($amount_collect==0 && $amount_bug_db>0){$amount_bug=$amount_bug_db;}
            $price=$slave->to_money($db->result($r,$i-1,"price"));
            $price_end=$slave->to_money($db->result($r,$i-1,"price_end"));
            $discount=$db->result($r,$i-1,"discount");
            $summ=$slave->to_money($db->result($r,$i-1,"summ"));
            $status_dps=$db->result($r,$i-1,"status_dps");
            $inc_status_name="";if ($status_dps==139){
                $inc_status_name=$this->getIncomeStatusDpsName($art_id,$amount,$suppl_id);
            }
            $status_dps_name=$db->result($r,$i-1,"status_dps_name").$inc_status_name;

            list($max_discount_persent,$max_discount_price)=$this->getClientArticleMaxDiscount($art_id,$client_id,$price);
            if ($cash_id==1){
                $price=round($price*$usd_to_uah,2); $price_end=round($price_end*$usd_to_uah,2); $summ=round($amount_dp*$price_end,2); $max_discount_price=round($max_discount_price*$usd_to_uah,2);
            }
            if ($cash_id==3){
                $price=round($price*$usd_to_uah/$euro_to_uah,2); $price_end=round($price_end*$usd_to_uah/$euro_to_uah,2); $summ=round($amount_dp*$price_end,2); $max_discount_price=round($max_discount_price*$usd_to_uah/$euro_to_uah,2);
            }
            $summ_dp+=$summ;
            if ($suppl_id==0){$delivery_info=$this->getTpointDeliveryInfo($tpoint_id,$storage_id_from);}
            if ($suppl_id>0){$delivery_info=$this->getTpointSupplDeliveryInfo($tpoint_id,$suppl_id,$suppl_storage_id);}

            if ($status_dp==79){
                list($weight,$volume)=$this->getArticleWightVolume($art_id);
                $sum_weight+=($weight*$amount); $sum_volume+=($volume*$amount);
                $disabled="";if ($status_dp!=79 && $status_dp>0){$disabled=" disabled";}
                if ($suppl_id==0){$function_amount_change="setArticleToSelectAmountDp('$art_id','$article_nr_displ','$brand_id','$brand_name','$dp_id');";}
                if ($suppl_id>0){$function_amount_change="showDpSupplAmountInputWindow('$art_id','$article_nr_displ','$brand_id','$brand_name','$dp_id','$suppl_id','$suppl_storage_id','$price');";}

                $list.="<tr id='strRow_$i'>
                    <td style='text-align: center;'><input id='$i' type='checkbox' name='check[$i]' class='check_dp'/></td>
                    <td>$i<input type='hidden' id='idStr_$i' value='$id'></td>
                    <td style='min-width:140px;'><input type='hidden' id='artIdStr_$i' value='$art_id'>
                        <div class='input-group'>
                            <input class='form-control input-xs' type='text' readonly id='article_nr_displStr_$i' value='$article_nr_displ' placeholder='Індекс товару'>
                            <span class='input-group-btn'> 
                                <button type='button' class='btn btn-xs btn-info $disabled' $disabled onClick=\"showArticleSearchDocumentForm('$i','$art_id','$brand_id','$article_nr_displ','dp','$dp_id');\"><i class=\"fa fa-bars\"></i></button>
                            </span>
                        </div>
                        <span class='hidden'>$article_nr_displ</span>
                    </td>
                    <td style='min-width:100px;'><input type='hidden' id='brandIdStr_$i' value='$brand_id'>
                        <input class='form-control input-xs' type='text' readonly id='brandNameStr_$i' value='$brand_name' placeholder='Бренд'>
                        <span class='hidden'>$brand_name</span>
                    </td>
                    <td>
                        <div class='input-group'>
                            <input type='text' id='amountStr_$i' readonly value='$amount' class='form-control input-xs numberOnly' autocomplete='off'>
                            <span class='input-group-btn'> <button type='button' class='btn btn-xs btn-primary $disabled' $disabled onClick=\"$function_amount_change\"><i class=\"fa fa-bars\"></i></button></span>
                        </div>
                        <span class='hidden'>$amount</span>
                    </td>
                    <td>$amount_collect</td>
                    <td title='$amount_bug_info'>$amount_bug</td>
                    <td>
                        <input type='text' id='priceStr_$i' readonly value='$price' class='form-control input-xs numberOnlyLong' autocomplete='off'>
                        <span class='hidden'>$price</span>
                    </td>
                    <td>
                        <input type='text' id='discountStr_$i' value='$discount' class='form-control input-xs numberOnlyLong' autocomplete='off' title='$max_discount_persent"."% ($max_discount_price)' onChange=\"calculateDiscountPrice('$i')\">
                        <input type='hidden' id='maxDiscountPersentStr_$i' value='$max_discount_persent'>
                        <input type='hidden' id='maxDiscountPriceStr_$i' value='$max_discount_price'>
                        <span class='hidden'>$discount</span>
                    </td>
                    <td>
                        <input type='text' id='priceEndStr_$i' value='$price_end' class='form-control input-xs numberOnlyLong' autocomplete='off' title='$max_discount_persent"."% ($max_discount_price)' onChange=\"calculateDiscountPersent('$i')\">
                        <span class='hidden'>$price_end</span>
                    </td>
                    <td>
                        <input type='text' id='summStr_$i' readonly value='$summ' class='form-control input-xs numberOnlyLong' autocomplete='off'>
                        <span class='hidden'>$brand_name</span>
                    </td>
                    <td><span class='label label-$reserv_type_color'>$storage_name</span></td>
                    <td><small>$delivery_info</small></td>
                    <td>$status_dps_name ($status_dps)</td>
                    <td><button class='btn btn-xs btn-default $disabled' $disabled onClick='dropDpStr(\"$i\",\"$dp_id\",\"$id\");'><i class='fa fa-times'></i></button></td>
                </tr>";
            }
            if ($status_dp>79){
                if ($article_nr_displ!=""){$link_btn="";
                    if ($status_dps==139 && $inc_status_name!=""){
                        $link_btn=" <button type='button' class='btn btn-xs btn-danger' onClick=\"showSupplToLocalChangeForm('$i','$art_id','$brand_id','$article_nr_displ','$dp_id','$dp_str_id');\"><i class=\"fa fa-link\"></i></button>";
                    }
                    $list.="<tr>
                        <td style='text-align: center;'></td>
                        <td>$i<input type='hidden' id='idStr_$i' value='$id'></td>
                        <td style='min-width:140px;'>$article_nr_displ $link_btn</td>
                        <td style='min-width:120px;'>$brand_name</td>
                        <td>$amount</td>
                        <td>$amount_collect</td>
                        <td>$amount_bug</td>
                        <td>$price</td>
                        <td>$discount</td>
                        <td>$price_end</td>
                        <td>$summ</td>
                        <td><span class='label label-$reserv_type_color'>$storage_name</span></td>
                        <td><small>$delivery_info</small></td>
                        <td>$status_dps_name</td>
                        <td></td>
                    </tr>";
                }
            }
        }
        if ($status_dp==79){
            $list="
                <tr id='dpStrNewRow' class='hidden'>
                    <td style='text-align: center;'><input id='i_0' type='checkbox' name='check[i_0]' class='checkit'/></td>
                    <td>nom_i<input type='hidden' id='idStr_0' value=''></td>
                    <td style='min-width:140px;'><input type='hidden' id='artIdStr_0' value=''>
                        <div class='input-group'>
                            <input class='form-control input-xs' type='text' readonly id='article_nr_displStr_0' value='' placeholder='Індекс товару'>
                            <span class='input-group-btn'> 
        
                                <button type='button' class='btn btn-xs btn-info' onClick=\"showArticleSearchDocumentForm('i_0','0','0','','dp','$dp_id');\"><i class=\"fa fa-bars\"></i></button>
                            </span>
                        </div>
                    </td>
                    <td style='min-width:120px;'><input type='hidden' id='brandIdStr_0' value=''>
                        <input class='form-control input-xs' type='text' readonly id='brandNameStr_0' value='' placeholder='Бренд'>
                    </td>
                    <td>
                        <input type='text' id='amountStr_0' value='' readonly class='form-control input-xs numberOnly' autocomplete='off' maxlength=''  min='1' max=''>
                    </td>
                    <td>-</td>
                    <td>-</td>
                    <td>
                        <input type='text' id='priceStr_0' readonly value='0' class='form-control input-xs numberOnly' autocomplete='off'>
                    </td>
                    <td>
                        <input type='text' id='discountStr_0' readonly value='0' class='form-control input-xs numberOnly' autocomplete='off'>
                    </td>
                    <td>
                        <input type='text' id='priceEndStr_0' readonly value='0' class='form-control input-xs numberOnly' autocomplete='off'>
                    </td>
                    <td>
                        <input type='text' id='summStr_0' readonly value='0' class='form-control input-xs numberOnly' autocomplete='off'>
                    </td>
                    <td><span class='label label-0'></span></td>
                    <td></td>
                    <td></td>
                    <td><button class='btn btn-xs btn-default'><i class='fa fa-times'></i></button></td>
                </tr>".$list;
                // onClick='dropIncomStr("i_0","0");'
        }
        if ($sum_weight!=0 && $sum_volume!=0 && $status_dp=='79'){
            $db->query("update J_DP set `weight`='$sum_weight', `volume`='$sum_volume' where id='$dp_id' and status_dp='79';");
        }
        if ($status_dp==79 || $status_dp==80){
            $db->query("update J_DP set `summ`='$summ_dp' where id='$dp_id' and status_dp in (79,80);");
        }
        return array($list,$kl_rw);
    }

    function saveDpCard($dp_id,$data_pay,$cash_id,$dp_summ,$doc_type_id,$tpoint_id,$client_id,$client_conto_id,$delivery_type_id,$carrier_id,$delivery_address){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка збереження даних!";
        $dp_id=$slave->qq($dp_id);$data_pay=$slave->qq($data_pay);$cash_id=$slave->qq($cash_id);$dp_summ=$slave->qq($dp_summ);$doc_type_id=$slave->qq($doc_type_id);$tpoint_id=$slave->qq($tpoint_id);$client_id=$slave->qq($client_id);$client_conto_id=$slave->qq($client_conto_id);$delivery_type_id=$slave->qq($delivery_type_id);$carrier_id=$slave->qq($carrier_id);$delivery_address=$slave->qq($delivery_address);
        if ($dp_id>0){
            //$this->check_doc_prefix_nom($income_id,$client_id);
            $db->query("update J_DP set `doc_type_id`='$doc_type_id', `tpoint_id`='$tpoint_id', `client_id`='$client_id', `client_conto_id`='$client_conto_id', `data_pay`='$data_pay', `cash_id`='$cash_id', `summ`='$dp_summ', `delivery_type_id`='$delivery_type_id', `carrier_id`='$carrier_id', `delivery_address`='$delivery_address' where `id`='$dp_id';");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function saveDpCardData($dp_id,$cash_id,$frm,$tto,$idStr,$artIdStr,$article_nr_displStr,$brandIdStr,$amountStr,$priceStr,$priceEndStr,$discountStr,$summStr){$slave=new slave;$answer=0;$err="Помилка збереження даних!";
        $dp_id=$slave->qq($dp_id);//$frm=$slave->qq($frm);$tto=$slave->qq($tto);$cash_id=$slave->qq($cash_id);
        if ($dp_id>0){
            /*$idStr=$slave->qq($idStr);$artIdStr=$slave->qq($artIdStr);$article_nr_displStr=$slave->qq($article_nr_displStr);$brandIdStr=$slave->qq($brandIdStr);$amountStr=$slave->qq($amountStr);$priceStr=$slave->qq($priceStr);$discountStr=$slave->qq($discountStr);
            for($i=$frm;$i<=$tto;$i++){
                $idS=$idStr[$i]; $artIdS=$artIdStr[$i]; $article_nr_displS=$article_nr_displStr[$i]; $brandIdS=$brandIdStr[$i]; $amountS=$amountStr[$i]; $priceS=$priceStr[$i]; $priceEndS=$priceEndStr[$i]; $discountS=$discountStr[$i];$summS=$summStr[$i];
                if ($idS=="" || $idS==0){
                    $r=$db->query("select max(id) as mid from J_DP_STR;");$idS=0+$db->result($r,0,"mid")+1;
                    $db->query("insert into J_DP_STR (`id`,`dp_id`) values ('$idS','$dp_id');");
                }
                if ($idS>0){
                    if ($artIdS!="" && $artIdS>0 && $article_nr_displS!=""){
                        $db->query("update J_DP_STR set `art_id`='$artIdS', `article_nr_displ`='$article_nr_displS', `brand_id`='$brandIdS', `amount`='$amountS', `price`='$priceS', `price_end`='$priceEndS', `discount`='$discountS',`summ`='$summS' where id='$idS' and dp_id='$dp_id';");
                    }else{
                        $db->query("delete from J_DP_STR where id='$idS' and dp_id='$dp_id';");
                    }
                }
            }
            */
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function updateDpStrPrice($dp_id,$str_id,$discount_new,$cash_id){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка збереження даних!";
        $dp_id=$slave->qq($dp_id);$str_id=$slave->qq($str_id);
        if ($dp_id>0 && $str_id>0 && $discount_new!=""){
            $discount_new=$slave->qq($discount_new);
            $r=$db->query("select * from J_DP_STR where dp_id='$dp_id' and id='$str_id' limit 0,1;");$n=$db->num_rows($r);
            if ($n==1){
                $amount=$db->result($r,0,"amount");
                $price=$db->result($r,0,"price");
                $discount=$db->result($r,0,"discount");
                if ($discount!=$discount_new){
                    $discount=$discount_new;
                    $price_end=round($price-$price*$discount/100,2);
                    $summ=round($price_end*$amount,2);
                    $db->query("update J_DP_STR set `discount`='$discount', `price_end`='$price_end', `summ`='$summ' where id='$str_id' and dp_id='$dp_id';");
                }
            }
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function setArticleToDp($dp_id,$tpoint_id,$artIdStr,$article_nr_displStr,$brandIdStr,$storageIdStr,$amountStr){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;
        $answer=0;$err="Помилка збереження даних!";
        $dp_id=$slave->qq($dp_id);$tpoint_id=$slave->qq($tpoint_id);$idS=0;$discountEx=0;$rr_amount=0;$rr_reserv=0;$amountEx=0;

        if ($dp_id>0){
            $artIdS=$slave->qq($artIdStr);$article_nr_displS=$slave->qq($article_nr_displStr);$brandIdS=$slave->qq($brandIdStr);$amountS=$slave->qq($amountStr);$storageIdS=$slave->qq($storageIdStr);
            $r=$db->query("select id,amount,discount from J_DP_STR where dp_id='$dp_id' and art_id='$artIdS' and `storage_id_from`='$storageIdS' and status_dps='93' limit 0,1;");$n=$db->num_rows($r);
            if ($n==1){
                $idS=$db->result($r,0,"id");
                $amountEx=$db->result($r,0,"amount");
                $discountEx=$db->result($r,0,"discount");
            }
            list($info,$max_moving,$rest_amount)=$this->showArticleRestStorageSelectText($artIdS,$storageIdS,$amountS,$amountEx);

            if ($amountS>$max_moving && $rest_amount<=0){$answer=0;$err="Кількість для переміщення ВЖЕ більша за залишок! (максимально: $max_moving)";}
            if ($amountS<=$max_moving){
                $can_drop=0;//$no_unreserv=1;
                if ($idS=="" || $idS==0){
                    $reserv_type_id=$this->getArticleReservType($tpoint_id,$storageIdS);
                    $r=$db->query("select max(id) as mid from J_DP_STR;");$idS=0+$db->result($r,0,"mid")+1;
                    $db->query("insert into J_DP_STR (`id`,`dp_id`,`reserv_type_id`) values ('$idS','$dp_id','$reserv_type_id');");
                    $rr_reserv=0;$amountEx=0; $can_drop=1;//$no_unreserv=0;
                }
                if ($idS>0){
                    if ($artIdS!="" && $artIdS>0 && $article_nr_displS!=""){
                        $article_price=$this->getArticlePrice($artIdS,$dp_id);
                        if ($article_price>0){
                            $price_end=round($article_price-$article_price*$discountEx/100,2);
                            $summ=round($price_end*$amountEx,2);
                            $reserv_type_id=$this->getArticleReservType($tpoint_id,$storageIdS);
                            $db->query("update J_DP_STR set `art_id`='$artIdS', `article_nr_displ`='$article_nr_displS', `brand_id`='$brandIdS', `amount`='$amountS', `storage_id_from`='$storageIdS', `location_storage_id`='$storageIdS', `price`='$article_price', `price_end`='$price_end', `summ`='$summ', `reserv_type_id`='$reserv_type_id' where id='$idS' and dp_id='$dp_id';");
                            //$db->query("update J_DP set status_dp='93' where id='$dp_id';");
                            list($weight,$volume,$empty_kol)=$this->updateDpWeightVolume($dp_id);
                            $dp_summ=$this->updateDpSumm($dp_id);
                            $rr=$dbt->query("select * from T2_ARTICLES_STRORAGE where ART_ID='$artIdS' and STORAGE_ID='$storageIdS' limit 0,1;");$nr=$dbt->num_rows($rr);
                            if ($nr==1){
                                $rr_amount=$dbt->result($rr,0,"AMOUNT");
                                $rr_reserv=$dbt->result($rr,0,"RESERV_AMOUNT");
                            }
                            $rr_amount=$rr_amount+$amountEx-$amountS;$rr_reserv=$rr_reserv+$amountS-$amountEx;
                            $dbt->query("update T2_ARTICLES_STRORAGE set AMOUNT='$rr_amount', `RESERV_AMOUNT`='$rr_reserv' where ART_ID='$artIdS' and STORAGE_ID ='$storageIdS';");
                            list($empty_kol,$label_empty)=$this->labelArtEmptyCount($dp_id,$empty_kol);
                            $answer=1;$err="";
                        }else{
                            $answer=0;$err="Ціна товару не визначена. Зверніться до відповідального менеджера";
                            if ($can_drop==1){
                                $db->query("delete from J_DP_STR where `id`='$idS' and `dp_id`='$dp_id' limit 1;");
                            }
                        }
                    }
                }
            }
            //if ($answer==0){$err="Кількість для переміщення ВЖЕ БІЛЬША ЗА ЗАЛИШОК!3";}
        }
        return array($answer,$err,$idS,$weight,$volume,$empty_kol,$label_empty,$dp_summ);
    }

    function setArticleSupplToDp($dp_id,$tpoint_id,$artIdStr,$article_nr_displStr,$brandIdStr,$supplIdStr,$supplStorageIdStr,$amountStr){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка збереження даних!";
        $dp_id=$slave->qq($dp_id);$idS="";$discountEx=0;
        if ($dp_id>0){
            $artIdS=$slave->qq($artIdStr);$article_nr_displS=$slave->qq($article_nr_displStr);$brandIdS=$slave->qq($brandIdStr);$amountS=$slave->qq($amountStr);$supplIdS=$slave->qq($supplIdStr);$supplStorageIdS=$slave->qq($supplStorageIdStr);
            $amountEx=0;
            $r=$db->query("select id,amount,discount from J_DP_STR where dp_id='$dp_id' and art_id='$artIdS' and `suppl_id`='$supplIdS' and `suppl_storage_id`='$supplStorageIdS' and status_dps='93' limit 0,1;");$n=$db->num_rows($r);
            if ($n==1){
                $idS=$db->result($r,0,"id");
                $amountEx=$db->result($r,0,"amount");
                $discountEx=$db->result($r,0,"discount");
            }
    //		list($info,$max_moving,$rest_amount)=$this->showArticleRestStorageSelectText($artIdS,$storageIdS,$amountS,$amountEx);
    //		if ($amountS>$max_moving && $rest_amount<=0){$answer=0;$err="Кількість для переміщення ВЖЕ більша за залишок! (максимально: $max_moving)";}
    //		if ($amountS<=$max_moving){
                $can_drop=0;//$no_unreserv=1;
                if ($idS=="" || $idS==0){
                    $reserv_type_id=69;//$this->getArticleReservType($tpoint_id,$storageIdS);
                    $r=$db->query("select max(id) as mid from J_DP_STR;");$idS=0+$db->result($r,0,"mid")+1;
                    $db->query("insert into J_DP_STR (`id`,`dp_id`,`reserv_type_id`) values ('$idS','$dp_id','$reserv_type_id');");
                    $amountEx=0; $can_drop=1;//$no_unreserv=0;$rr_reserv=0;
                }
                if ($idS>0){
                    if ($artIdS!="" && $supplIdS>0 && $article_nr_displS!=""){
                        $article_price=$this->getArticleSupplPrice($artIdS,$dp_id,$supplIdS,$supplStorageIdS);
                        if ($article_price>0){
                            $price_end=round($article_price-$article_price*$discountEx/100,2);
                            $summ=round($price_end*$amountEx,2);
                            $reserv_type_id=69;//$this->getArticleReservType($tpoint_id,$storageIdS);
                            $db->query("update J_DP_STR set `art_id`='$artIdS', `article_nr_displ`='$article_nr_displS', `brand_id`='$brandIdS', `amount`='$amountS', `suppl_id`='$supplIdS', `suppl_storage_id`='$supplStorageIdS', `price`='$article_price', `price_end`='$price_end', `summ`='$summ', `reserv_type_id`='$reserv_type_id' where id='$idS' and dp_id='$dp_id';");
                            //$db->query("update J_DP set status_dps='93' where id='$dp_id';");
                            list($weight,$volume,$empty_kol)=$this->updateDpWeightVolume($dp_id);
                            $dp_summ=$this->updateDpSumm($dp_id);

                            list($empty_kol,$label_empty)=$this->labelArtEmptyCount($dp_id,$empty_kol);
                            $answer=1;$err="";
                        }else{
                            $answer=0;$err="Ціна товару не визначена. Зверніться до відповідального менеджера";
                            if ($can_drop==1){
                                $db->query("delete from J_DP_STR where `id`='$idS' and `dp_id`='$dp_id' limit 1;");
                            }
                        }
                    }
                }
            //}
            //if ($answer==0){$err="Кількість для переміщення ВЖЕ БІЛЬША ЗА ЗАЛИШОК!3";}
        }
        return array($answer,$err,$idS,$weight,$volume,$empty_kol,$label_empty,$dp_summ);
    }

    function getClientCashConditions($client_id){$db=DbSingleton::getDb();$cash_id=0;$credit_cash_id=0;
        $r=$db->query("select cash_id,credit_cash_id from A_CLIENTS_CONDITIONS where client_id ='$client_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $cash_id=$db->result($r,0,"cash_id");
            $credit_cash_id=$db->result($r,0,"credit_cash_id");
        }
        return array($cash_id,$credit_cash_id);
    }

    function getClientOrgType($client_id){$db=DbSingleton::getDb();$org_type=0;
        $r=$db->query("select org_type from A_CLIENTS where id ='$client_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$org_type=$db->result($r,0,"org_type");}
        return $org_type;
    }

    function getCashName($cash_id){$db=DbSingleton::getDb();$name="";
        $r=$db->query("select name from CASH where id ='$cash_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$name=$db->result($r,0,"name");}
        return $name;
    }

    function getDpClientContoCash($client_id){$db=DbSingleton::getDb();$cash_id=1;$answer=0;$err="Помилка";
        $r=$db->query("select cash_id from A_CLIENTS_CONDITIONS where client_id ='$client_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$cash_id=$db->result($r,0,"cash_id");$answer=1;$err="";}
        return array($answer,$err,$cash_id);
    }

    function getDpClientDocType($client_id){$db=DbSingleton::getDb();$doc_type_id=64;$answer=0;$err="Помилка";
        $r=$db->query("select doc_type_id from A_CLIENTS_CONDITIONS where client_id ='$client_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$doc_type_id=$db->result($r,0,"doc_type_id");$answer=1;$err="";}
        return array($answer,$err,$doc_type_id);
    }

    function getClientPaymentDelay($client_id){$db=DbSingleton::getDb();$data_pay=date("Y-m-d");$answer=0;$err="Помилка";
        $r=$db->query("select * from A_CLIENTS_CONDITIONS where client_id='$client_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $payment_delay=$db->result($r,0,"payment_delay");
            $data_pay=date("Y-m-d",strtotime("+$payment_delay day", strtotime($data_pay)));
            $answer=1;$err="";
        }
        return array($answer,$err,$data_pay);
    }

    function changeDpCash($dp_id,$cash_id){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка збереження даних!";
        $dp_id=$slave->qq($dp_id);$cash_id=$slave->qq($cash_id);
        if ($dp_id>0){
            $r=$db->query("select oper_status,client_conto_id from J_DP where id='$dp_id' limit 0,1;");$n=$db->num_rows($r);
            if ($n==1){
                $oper_status=$db->result($r,0,"oper_status");
                if ($oper_status==30) {
                    $client_conto_id=$db->result($r,0,"client_conto_id");
                    $org_type=$this->getClientOrgType($client_conto_id);
                    list($client_cash_id,$credit_cash_id)=$this->getClientCashConditions($client_conto_id);
                    if ($client_cash_id==$cash_id || $org_type==0 || $org_type==1){
                        $db->query("update J_DP set cash_id='$cash_id' where id='$dp_id';");
                        $this->updateDpPriceCash($dp_id);
                        $answer=1;$err="";
                    }else{$answer=0;$err="Валюта розрахунку клієнта ".$this->getCashName($client_cash_id).". Змініть кінцевого платника на того кому дозволено розрахунок у валюті ".$this->getCashName($cash_id);}
                } else {$answer=0;$err="Документ заблоковано. Зміни вносити заборонено.";}
            }
        }
        return array($answer,$err);
    }

    function updateDpPriceCash($dp_id){$db=DbSingleton::getDb();$answer=0;
        $r=$db->query("select * from J_DP where id='$dp_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $oper_status=$db->result($r,0,"oper_status");
            if ($oper_status==30) {
                //$client_conto_id=$db->result($r,0,"client_conto_id");
                $cash_id=$db->result($r,0,"cash_id");
                list($usd_to_uah,$eur_to_uah)=$this->getKoursData();

                $r=$db->query("select * from J_DP_STR where dp_id='$dp_id' order by id asc;");$n=$db->num_rows($r);$summ_dp=0;
                for ($i=1;$i<=$n;$i++){
                    //$art_id=$db->result($r,$i-1,"art_id");
                    $amount=$db->result($r,$i-1,"amount");
                    $price=$db->result($r,$i-1,"price");
                    $price_end=$db->result($r,$i-1,"price_end");
                    //$discount=$db->result($r,$i-1,"discount");
                    //$summ=$db->result($r,$i-1,"summ");
                    if ($cash_id==1){$price=round($price*$usd_to_uah,2); $price_end=round($price_end*$usd_to_uah,2); }
                    if ($cash_id==3){$price=round($price*$usd_to_uah/$eur_to_uah,2); $price_end=round($price_end*$usd_to_uah/$eur_to_uah,2); }
                    $summ=$amount*$price_end;
                    $summ_dp+=$summ;
                }
                $db->query("update J_DP set `summ`='$summ_dp' where id='$dp_id' limit 1;");
                $answer=1;
            } else {$answer=0;}
        }
        return $answer;
    }

    function getArticlePrice($art_id,$dp_id){$dbt=DbSingleton::getTokoDb();$price=0;
        if ($dp_id>0 && $art_id!=""){
            list($price_lvl,$margin_price_lvl,$price_suppl_lvl,$margin_price_suppl_lvl,$client_vat)=$this->getDpClientPriceLevels($dp_id);
            $query="select t2apr.price_".$price_lvl.", t2si.price_usd as suppl_price_usd
            from T2_ARTICLES t2a 
                left outer join T2_ARTICLES_PRICE_RATING t2apr on (t2apr.art_id=t2a.ART_ID)
                left outer join T2_SUPPL_IMPORT t2si on (t2si.art_id=t2a.ART_ID)
            where t2a.ART_ID='$art_id' and t2apr.in_use='1' limit 0,1;";
            $r=$dbt->query($query);$n=$dbt->num_rows($r);
            if ($n==1){
                $price=$dbt->result($r,0,"price_".$price_lvl);
                if ($margin_price_lvl>0){
                    $price=$price+round($price*$margin_price_lvl/100,2);
                }
            }
        }
        return $price;
    }

    function getArticleSupplPrice($art_id,$dp_id,$suppl_id,$suppl_storage_id){$dbt=DbSingleton::getTokoDb();$price=0;
        if ($dp_id>0 && $art_id!=""){
            list($price_lvl,$margin_price_lvl,$price_suppl_lvl,$margin_price_suppl_lvl,$client_vat)=$this->getDpClientPriceLevels($dp_id);
            $query="select t2si.price_usd from T2_ARTICLES t2a 
            left outer join T2_SUPPL_ARTICLES_IMPORT t2sai on (t2sai.art_id=t2a.ART_ID)
            left outer join T2_SUPPL_IMPORT t2si on (t2si.art_id=t2sai.art_id and t2si.suppl_id=t2sai.suppl_id and t2si.status=1)
            where t2a.ART_ID = '$art_id' and t2sai.suppl_id='$suppl_id' limit 0,1;";
            $r=$dbt->query($query);$n=$dbt->num_rows($r);
            if ($n==1){
                $suppl_price_usd=$dbt->result($r,0,"price_usd");
                list($price_in_vat,$show_in_vat,$price_add_vat)=$this->getSupplVatConditions($suppl_id);
                $price_suppl=$suppl_price_usd;
                $tpoint_id=$this->getDpTpoint($dp_id);
                //Step 1;
                list($suppl_margin_fm,$suppl_delivery_fm,$suppl_margin2_fm)=$this->getTpointSupplFm($tpoint_id,$suppl_id,$suppl_storage_id,$price_suppl,$price_suppl_lvl);
                if ($suppl_margin_fm>0){
                    $price=($price_suppl+$price_suppl*$suppl_margin_fm/100)-$price_suppl;
                    if ($price>$suppl_delivery_fm){
                        $price=($price_suppl+$price_suppl*$suppl_margin_fm/100);
                    }
                    if ($price<=$suppl_delivery_fm){
                        $price=$price_suppl+$price_suppl*$suppl_margin2_fm/100+$suppl_delivery_fm;
                    }
                    //Step 2; Client Margin
                    if ($margin_price_suppl_lvl>0 && $margin_price_suppl_lvl!=""){
                        $price=$price+$price*$margin_price_suppl_lvl/100;
                    }
                    //Step 3; VAT
                    //$price_in_vat,$show_in_vat,$price_add_vat
                    if ($client_vat==1){
                        if ($price_in_vat==0 && $show_in_vat==1 && $price_add_vat==1){
                            $price=$price+$price*20/100;
                        }
                        if ($price_in_vat==0 && $show_in_vat==0){
                            $price=0;
                        }
                    }
                }
                $price=round($price,2);
            }
        }
        return $price;
    }

    function changeArticleToDp($dp_id,$dp_str_id,$amount_change){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;$answer=0;$err="Помилка збереження даних!";
        $dp_id=$slave->qq($dp_id);$weight=$volume=0;
        if ($dp_id>0){
            $dp_str_id=$slave->qq($dp_str_id);$amount_change=$slave->qq($amount_change);
            $r=$db->query("select amount,art_id,storage_id_from from J_DP_STR where dp_id='$dp_id' and id='$dp_str_id' and status_dps='93' limit 0,1;");
            $amountEx=$db->result($r,0,"amount");
            $art_id=$db->result($r,0,"art_id");
            $storage_id_from=$db->result($r,0,"storage_id_from");
            $db->query("update J_DP_STR set `amount`='$amount_change' where id='$dp_str_id' and dp_id='$dp_id' limit 1;");
            list($weight,$volume,$empty_kol)=$this->updateDpWeightVolume($dp_id);
            $this->updateDpSumm($dp_id);
            $rr=$dbt->query("select * from T2_ARTICLES_STRORAGE where ART_ID='$art_id' and STORAGE_ID ='$storage_id_from' limit 0,1;");$nr=$dbt->num_rows($rr);
            if ($nr==1){
                $rr_amount=$dbt->result($rr,0,"AMOUNT");
                $rr_reserv=$dbt->result($rr,0,"RESERV_AMOUNT");
                $rr_amount=$rr_amount+$amountEx-$amount_change;
                $rr_reserv=$rr_reserv-$amountEx+$amount_change;
                $dbt->query("update T2_ARTICLES_STRORAGE set AMOUNT='$rr_amount', `RESERV_AMOUNT`='$rr_reserv' where ART_ID='$art_id' and STORAGE_ID ='$storage_id_from';");
            }
            $answer=1;$err="";
        }
        return array($answer,$err,$weight,$volume);
    }

    function dropDpStr($dp_id,$dp_str_id){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;$answer=0;$err="Помилка індексу";
        $dp_id=$slave->qq($dp_id);$dp_summ="";
        $r=$db->query("select oper_status,status from J_DP where id='$dp_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $status=$db->result($r,0,"status");
            $oper_status=$db->result($r,0,"oper_status");
            if ($oper_status==30 && ($status==1 ||$status==1)){
                $r1=$db->query("select * from J_DP_STR where id='$dp_str_id' limit 0,1;");$n1=$db->num_rows($r1);
                if ($n1==1){
                    $status_dps_str=$db->result($r1,0,"status_dps");
                    if ($status_dps_str==93){
                        $art_id=$db->result($r1,0,"art_id");
                        $amount=$db->result($r1,0,"amount");
                        $storage_id_from=$db->result($r1,0,"storage_id_from");
                        $suppl_id=$db->result($r1,0,"suppl_id");
                        if ($art_id==0){
                            $db->query("delete from J_DP_STR where id='$dp_str_id' and dp_id='$dp_id' limit 1;");
                            $this->updateDpWeightVolume($dp_id);
                            $dp_summ=$this->updateDpSumm($dp_id);
                            $answer=1;$err="";
                        }
                        if ($art_id>0 && $suppl_id>0){
                            $db->query("delete from J_DP_STR where id='$dp_str_id' and dp_id='$dp_id' limit 1;");
                            $this->updateDpWeightVolume($dp_id);
                            $dp_summ=$this->updateDpSumm($dp_id);
                            $answer=1;$err="";
                        }
                        if ($art_id>0 && $suppl_id==0){
                            $rs=$dbt->query("select * from T2_ARTICLES_STRORAGE where ART_ID='$art_id' and STORAGE_ID='$storage_id_from' limit 0,1;");$ns=$dbt->num_rows($rs);
                            if ($ns==1){
                                $reserv_amount_s=$dbt->result($rs,0,"RESERV_AMOUNT");
                                $amount_s=$dbt->result($rs,0,"AMOUNT");
                                $reserv_amount_s-=$amount;
                                $amount_s+=$amount;
                                $dbt->query("update T2_ARTICLES_STRORAGE set `AMOUNT`='$amount_s', `RESERV_AMOUNT`='$reserv_amount_s' where ART_ID='$art_id' and STORAGE_ID='$storage_id_from' limit 1;");
                                $db->query("delete from J_DP_STR where id='$dp_str_id' and dp_id='$dp_id' limit 1;");
                                $this->updateDpWeightVolume($dp_id);
                                $dp_summ=$this->updateDpSumm($dp_id);
                                $answer=1;$err="";
                            }
                        }
                    }else {$answer=0;$err="Видалення заблоковано. Відбір передано в роботу.";}
                }
            }else {$answer=0;$err="Видалення заблоковано. Замовлення передано в роботу.";}
        }
        return array($answer,$err,$dp_summ);
    }

    function updateDpSumm($dp_id){$db=DbSingleton::getDb();$slave=new slave;$sum=0;
        $cash_id=$this->getDpCashId($dp_id);list($usd_to_uah,$eur_to_uah)=$this->getKoursData();
        $r=$db->query("select * from J_DP_STR where dp_id='$dp_id';");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $str_id=$db->result($r,$i-1,"id");
            $price_end=$db->result($r,$i-1,"price_end"); $price_cash=$price_end;
            if ($cash_id==1){ $price_cash=round($price_cash*$usd_to_uah,2);}
            if ($cash_id==3){ $price_cash=round($price_cash*$usd_to_uah/$eur_to_uah,2); }

            $amount=$db->result($r,$i-1,"amount");$amount_dp=$amount;
            $amount_collect=$db->result($r,$i-1,"amount_collect");
            $amount_bug=$db->result($r,$i-1,"amount_bug");
            if ($amount_collect>0 || $amount_bug>0){$amount_dp=$amount_collect;}

            $summ_cash=round($amount_dp*$price_cash,2);
            $summ=$slave->to_money($db->result($r,$i-1,"summ"));
            if ($summ_cash!=$summ){$summ=$summ_cash;
                $db->query("update J_DP_STR set `summ`='$summ' where id='$str_id' limit 1;");
            }
            $sum=$sum+$summ_cash;
        }
        if ($n>0){
            $db->query("update J_DP set `summ`='$sum' where id='$dp_id' and oper_status='30' and status='1';");
            if ($sum==0){
                $db->query("update J_DP set `status_dp`='81' where id='$dp_id';");
            }
        }
        return $sum;
    }

    function updateDpWeightVolume($dp_id){$db=DbSingleton::getDb();$sum_weight=0;$sum_volume=0;$empty_kol=0;
        $r=$db->query("select art_id, amount from J_DP_STR where dp_id='$dp_id';");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $art_id=$db->result($r,$i-1,"art_id");
            $amount=$db->result($r,$i-1,"amount");
            list($weight,$volume)=$this->getArticleWightVolume($art_id);
            if ($weight==0 || $volume==0){$empty_kol+=1;}
            if ($weight>0 && $amount>0){$sum_weight+=($weight*$amount);}
            if ($volume>0 && $amount>0){$sum_volume+=($volume*$amount);}
        }
        if ($n>0){ $db->query("update J_DP set `weight`='$sum_weight', `volume`='$sum_volume' where id='$dp_id' and oper_status='30' and status='1';"); }
        return array($sum_weight,$sum_volume,$empty_kol);
    }

    function makeDpCardFinish($dp_id){$answer=0;$err="";
        //$dp_id=$slave->qq($dp_id);
        /*$r=$db->query("select oper_status,storage_id,storage_cells_id from J_INCOME where id='$dp_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $oper_status=$db->result($r,0,"oper_status");
            $storage_id=$db->result($r,0,"storage_id");
            $storage_cells_id=$db->result($r,0,"storage_cells_id");
            if ($storage_id==0 || $storage_cells_id==0){$answer=0;$err="Не вказано \"Склад зберігання\" або \"Комірка зберігання\". Накладну не проведено!";}
            if ($storage_id>0 && $storage_cells_id>0){
                if ($oper_status==30) {
                    $db->query("update J_INCOME set oper_status='31' where id='$dp_id';");
                    /* 				make calculation dp  */

        /*			$r1=$db->query("select * from J_INCOME_STR where dp_id='$dp_id' order by id asc;");$n1=$db->num_rows($r1);
                    for ($i=1;$i<=$n1;$i++){
                        $art_id=$db->result($r1,$i-1,"art_id");
                        $amount=$db->result($r1,$i-1,"amount");
                        $price_man_usd=$db->result($r1,$i-1,"price_man_usd");

                        list($oper_price,$general_stock)=$cat->getArticleOperPriceGeneralStock($art_id);
                        $new_oper_price=round((($oper_price*$general_stock)+($amount*$price_man_usd))/($amount+$general_stock),2);
                        $new_general_stock=$amount+$general_stock;

                        $cat->setArticleOperPriceGeneralStock($art_id,$new_oper_price,$new_general_stock);
    //					$db->query("insert into T2_ARTICLES_STRORAGE_CELLS (`ART_ID`,`AMOUNT`,`INCOME_ID`,`STORAGE_ID`,`STORAGE_CELLS_ID`) values ('$art_id','$amount','$dp_id','$storage_id','$storage_cells_id');");
                        $db->query("insert into T2_ARTICLES_STRORAGE (`ART_ID`,`AMOUNT`,`INCOME_ID`,`STORAGE_ID`) values ('$art_id','$amount','$dp_id','$storage_id');");
                    }

                    /* 				end calculation dp  */
        /*			$answer=1;$err="";
                } else {$answer=0;$err="Накладну заблоковано. Зміни вносити заборонено.";}
            }
        }*/
        return array($answer,$err);
    }

    function showDpLocalAutoCellForm($dp_id,$storage_id_to){
        $form="";$form_htm=RD."/tpl/dp_local_auto_cell_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $form=str_replace("{dp_id}",$dp_id,$form);
        list($cells_list,$cs)=$this->showStorageCellsSelectList($storage_id_to,0);
        $form=str_replace("{cells_list_from}",$cells_list,$form);
        $form=str_replace("{storage_name_to}",$this->getStorageName($storage_id_to),$form);
        $form=str_replace("{storage_id_to}",$storage_id_to,$form);
        return $form;
    }

    function saveDpLocalAutoCell($dp_id,$storage_id_to,$cell_id_from){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;$catalogue=new catalogue;$answer=0;$err="Помилка збереження даних!";$kol_row=0;
        $dp_id=$slave->qq($dp_id);$storage_id_to=$slave->qq($storage_id_to);$cell_id_from=$slave->qq($cell_id_from);$amountEx=0; $rr_amount=$rr_reserv=0;$no_row=0;
        if ($dp_id>0 && $storage_id_to>0 && $cell_id_from>0){
            $db->query("update J_DP set storage_id_to='$storage_id_to' where id='$dp_id';");
            $rc=$dbt->query("select * from T2_ARTICLES_STRORAGE_CELLS where STORAGE_ID='$storage_id_to' and STORAGE_CELLS_ID='$cell_id_from';");$nc=$dbt->num_rows($rc);
            for ($ic=1;$ic<=$nc;$ic++){
                $art_id=$dbt->result($rc,$ic-1,"ART_ID");
                $amountS=$dbt->result($rc,$ic-1,"AMOUNT");
                list($article_nr_displ,$brand_id,$brand_name)=$catalogue->getArticleNrDisplBrand($art_id);
                $idS="";
                $r=$db->query("select id,amount from J_DP_STR where dp_id='$dp_id' and art_id='$art_id' and `storage_id_from`='$storage_id_to' and status_dps='93' limit 0,1;");$n=$db->num_rows($r);
                if ($n==1){
                    $idS=$db->result($r,0,"id");
                    $amountEx=$db->result($r,0,"amount");
                }
                //$no_unreserv=1;
                if ($idS=="" || $idS==0){
                    $r=$db->query("select max(id) as mid from J_DP_STR;");$idS=0+$db->result($r,0,"mid")+1;
                    $db->query("insert into J_DP_STR (`id`,`dp_id`) values ('$idS','$dp_id');");
                    $rr_reserv=0;$amountEx=0;//$no_unreserv=0;
                }
                if ($idS>0){
                    if ($art_id!="" && $art_id>0 && $article_nr_displ!=""){$kol_row+=1;
                        $amountEx+=$amountS;
                        $db->query("update J_DP_STR set `art_id`='$art_id', `article_nr_displ`='$article_nr_displ', `brand_id`='$brand_id', `amount`='$amountEx', `storage_id_from`='$storage_id_to', `cell_id_from`='$cell_id_from' where id='$idS' and dp_id='$dp_id' limit 1;");
                        $db->query("update J_DP set status_dp='79' where id='$dp_id' limit 1;");

                        list($weight,$volume,$empty_kol)=$this->updatedpWeightVolume($dp_id);
                        $rr=$dbt->query("select * from T2_ARTICLES_STRORAGE_CELLS where ART_ID='$art_id' and STORAGE_ID ='$storage_id_to' and STORAGE_CELLS_ID='$cell_id_from' limit 0,1;");$nr=$dbt->num_rows($rr);
                        if ($nr==1){
                            $rr_amount=$dbt->result($rr,0,"AMOUNT");
                            $rr_reserv=$dbt->result($rr,0,"RESERV_AMOUNT");
                        }
                        $rr_amount-=$amountS;$rr_reserv+=$amountS;
                        $dbt->query("update T2_ARTICLES_STRORAGE_CELLS set AMOUNT='$rr_amount', `RESERV_AMOUNT`='$rr_reserv' where ART_ID='$art_id' and STORAGE_ID ='$storage_id_to' and STORAGE_CELLS_ID='$cell_id_from' limit 1;");

                        $rr=$dbt->query("select * from T2_ARTICLES_STRORAGE where ART_ID='$art_id' and STORAGE_ID ='$storage_id_to' limit 0,1;");$nr=$dbt->num_rows($rr);
                        if ($nr==1){
                            $rr_amount=$dbt->result($rr,0,"AMOUNT");
                            $rr_reserv=$dbt->result($rr,0,"RESERV_AMOUNT");
                        }
                        $rr_amount-=$amountS;$rr_reserv+=$amountS;
                        $dbt->query("update T2_ARTICLES_STRORAGE set AMOUNT='$rr_amount', `RESERV_AMOUNT`='$rr_reserv' where ART_ID='$art_id' and STORAGE_ID ='$storage_id_to' limit 1;");
                    }
                }
            }
            $answer=1;$err=""; $no_row=1; if ($kol_row>0){$no_row=0;}
        }
        return array($answer,$err,$no_row);
    }

    function clearDpLocalAutoCellForm($dp_id){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;$answer=0;$err="Помилка індексу";
        $dp_id=$slave->qq($dp_id);
        $r=$db->query("select oper_status,status_dp from J_DP where id='$dp_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $status_dp=$db->result($r,0,"status_dp");
            $oper_status=$db->result($r,0,"oper_status");
            if ($oper_status==30 && ($status_dp==79 ||$status_dp==45)){
                $r1=$db->query("select * from J_DP_STR where dp_id='$dp_id';");$n1=$db->num_rows($r1);
                for ($i1=1;$i1<=$n1;$i1++){
                    $dp_str_id=$db->result($r1,$i1-1,"id");
                    $status_dps_str=$db->result($r1,$i1-1,"status_dps");
                    if ($status_dps_str==93){
                        $art_id=$db->result($r1,$i1-1,"art_id");
                        $amount=$db->result($r1,$i1-1,"amount");
                        $storage_id_from=$db->result($r1,$i1-1,"storage_id_from");
                        $cell_id_from=$db->result($r1,$i1-1,"cell_id_from");

                        $rs=$dbt->query("select * from T2_ARTICLES_STRORAGE where ART_ID='$art_id' and STORAGE_ID='$storage_id_from' limit 0,1;");$ns=$dbt->num_rows($rs);
                        if ($ns==1){
                            $reserv_amount_s=$dbt->result($rs,0,"RESERV_AMOUNT");
                            $amount_s=$dbt->result($rs,0,"AMOUNT");
                            $reserv_amount_s-=$amount;
                            $amount_s+=$amount;
                            $dbt->query("update T2_ARTICLES_STRORAGE set `AMOUNT`='$amount_s', `RESERV_AMOUNT`='$reserv_amount_s' where ART_ID='$art_id' and STORAGE_ID='$storage_id_from' limit 1;");
                        }
                        $rs=$dbt->query("select * from T2_ARTICLES_STRORAGE_CELLS where ART_ID='$art_id' and STORAGE_ID='$storage_id_from' and STORAGE_CELLS_ID='$cell_id_from' limit 0,1;");$ns=$dbt->num_rows($rs);
                        if ($ns==1){
                            $reserv_amount_s=$dbt->result($rs,0,"RESERV_AMOUNT");
                            $amount_s=$dbt->result($rs,0,"AMOUNT");
                            $reserv_amount_s-=$amount;
                            $amount_s+=$amount;
                            $dbt->query("update T2_ARTICLES_STRORAGE_CELLS set `AMOUNT`='$amount_s', `RESERV_AMOUNT`='$reserv_amount_s' where ART_ID='$art_id' and STORAGE_ID='$storage_id_from' and STORAGE_CELLS_ID='$cell_id_from' limit 1;");
                        }
                        $db->query("delete from J_DP_STR where id='$dp_str_id' and dp_id='$dp_id' limit 1;");
                        $this->updatedpWeightVolume($dp_id);
                        $answer=1;$err="";

                    }else {$answer=0;$err="Видалення заблоковано. Відбір передано в роботу.";}
                }
            }else {$answer=0;$err="Видалення заблоковано. Переміщення передано в роботу.";}
        }
        return array($answer,$err);
    }

    function showDpArticleSearchForm($art_id,$brand_id,$article_nr_display,$dp_id,$tpoint_id){
        $form="";$form_htm=RD."/tpl/dp_artilce_search_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        list($range_list,$list_brand_select)=$this->showArticlesSearchDocumentList($article_nr_display,$brand_id,0,$dp_id,$tpoint_id);
        $form=str_replace("{article_nr_display}",$article_nr_display,$form);
        $form=str_replace("{range_list}",$range_list,$form);
        $form=str_replace("{list_brand_select}",$list_brand_select,$form);
        return $form;
    }

//    function showDpArticleLocalSearchForm($art_id,$brand_id,$article_nr_display,$dp_id,$storage_id_from){
//        $form="";$form_htm=RD."/tpl/dp_artilce_local_search_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
//        list($range_list,$list_brand_select)=$this->showArticlesLocalSearchDocumentList($article_nr_display,$brand_id,0,$dp_id,$storage_id_from);
//        $form=str_replace("{article_nr_display}",$article_nr_display,$form);
//        $form=str_replace("{range_list}",$range_list,$form);
//        $form=str_replace("{list_brand_select}",$list_brand_select,$form);
//        return $form;
//    }

    function getDpCashId($dp_id){$db=DbSingleton::getDb();$cash_id=2;
        $r=$db->query("select cash_id  from J_DP where id='$dp_id' limit 0,1;"); $n=$db->num_rows($r);
        if ($n==1){	$cash_id=$db->result($r,0,"cash_id");}
        return $cash_id;
    }

    function getDpClient($dp_id){$db=DbSingleton::getDb();$client_conto_id=0;
        $r=$db->query("select client_id,client_conto_id  from J_DP where id='$dp_id' limit 0,1;"); $n=$db->num_rows($r);
        if ($n==1){
            $client_id=$db->result($r,0,"client_id");
            $client_conto_id=$db->result($r,0,"client_conto_id");
            if ($client_conto_id==0 && $client_id>0){$client_conto_id=$client_id;}
        }
        return $client_conto_id;
    }

    function getDpClientPriceLevels($dp_id){$db=DbSingleton::getDb();$price_lvl=0;$margin_price_lvl=0;$price_suppl_lvl=0;$margin_price_suppl_lvl=0;$client_vat=0;
        $r=$db->query("select client_id,client_conto_id  from J_DP where id='$dp_id' limit 0,1;"); $n=$db->num_rows($r);
        if ($n==1){
            $client_id=$db->result($r,0,"client_id");
            $client_conto_id=$db->result($r,0,"client_conto_id");
            if ($client_conto_id==0 && $client_id>0){$client_conto_id=$client_id;}
            if ($client_conto_id>0){
                $r1=$db->query("select * from A_CLIENTS_CONDITIONS where client_id='$client_conto_id' limit 0,1;"); $n1=$db->num_rows($r1);
                if ($n1==1){
                    $price_lvl=$db->result($r1,0,"price_lvl")+1;
                    $margin_price_lvl=$db->result($r1,0,"margin_price_lvl");
                    $price_suppl_lvl=$db->result($r1,0,"price_suppl_lvl")+1;
                    $margin_price_suppl_lvl=$db->result($r1,0,"margin_price_suppl_lvl");
                    $client_vat=$db->result($r1,0,"client_vat");
                }
            }
        }
        return array($price_lvl,$margin_price_lvl,$price_suppl_lvl,$margin_price_suppl_lvl,$client_vat);
    }

    function showArticlesSearchDocumentList($art,$brand_id,$search_type,$dp_id,$tpoint_id){$db=DbSingleton::getTokoDb();$cat=new catalogue;
        list($price_lvl,$margin_price_lvl,$price_suppl_lvl,$margin_price_suppl_lvl,$client_vat)=$this->getDpClientPriceLevels($dp_id);$n=0;$list2="";$suppl_storage_id=0;
        if ($search_type==""){$search_type=1;}
        if ($search_type==0){
            $art=$cat->clearArticle($art);
            $where_brand="";$group_brand="group by t2c.BRAND_ID"; if ($brand_id!="" && $brand_id>0){$where_brand=" and t2c.BRAND_ID='$brand_id'"; $group_brand="";}
            if ($art!=""){
                $query="select t2b.BRAND_NAME, t2n.NAME,t2c.BRAND_ID, t2c.DISPLAY_NR, t2c.ART_ID, t2c.KIND, t2c.RELATION from T2_CROSS t2c 
                     inner join T2_BRANDS t2b on t2b.BRAND_ID=t2c.BRAND_ID
                     left outer join T2_NAMES t2n on t2n.ART_ID=t2c.ART_ID
                 where  t2c.SEARCH_NUMBER = '$art' $where_brand $group_brand order by t2n.NAME asc;";
                 $r=$db->query($query);$n=$db->num_rows($r);
            }
            $one_result=0;
            if ($n>1 && ($brand_id=="" || $brand_id==0)){ $where_brand="";
                $list2=$cat->showCatalogueBrandSelectDocumentList($r,$art);
            }
            if ($n==1){
                $query="select t2b.BRAND_NAME, t2n.NAME,t2c.BRAND_ID, t2c.DISPLAY_NR, t2c.ART_ID, t2c.KIND, t2c.RELATION from T2_CROSS t2c 
                     inner join T2_BRANDS t2b on t2b.BRAND_ID=t2c.BRAND_ID
                     left outer join T2_NAMES t2n on t2n.ART_ID=t2c.ART_ID
                 where  t2c.SEARCH_NUMBER = '$art' $where_brand order by t2n.NAME asc;";
                $r=$db->query($query);$n=$db->num_rows($r);$one_result=1;
            }
            if (($n>1 && $brand_id!="") || $one_result==1){$ak=array();$rk=array();
                $art_id_str="";
                for ($i=1;$i<=$n;$i++){
                    $ART_ID=$db->result($r,$i-1,"ART_ID");
                    $KIND=$db->result($r,$i-1,"KIND");
                    $RELATION=$db->result($r,$i-1,"RELATION");
                    $art_id_str.="'$ART_ID'";if ($i<$n){$art_id_str.=",";}
                    if (($ak[$ART_ID]=="") || $KIND==0){$ak[$ART_ID]=$KIND;}
                    if (($rk[$ART_ID]=="") || $RELATION==0){$rk[$ART_ID]=$RELATION;}
                }
                $query="select t2a.ART_ID,t2a.BRAND_ID,t2a.ARTICLE_NR_DISPL, t2b.BRAND_NAME, t2n.NAME, t2n.INFO, t2bc.BARCODE, gg.NAME as goods_group_name, SUM(`t2asc`.`AMOUNT`) as stock, SUM(`t2asc`.`RESERV_AMOUNT`) as reserv, s.name as storage_name,s.id as storage_id, t2apr.price_".$price_lvl.", t2sai.suppl_id, t2sai.return_delay, t2sai.warranty_info , t2si.price_usd, t2si.client_storage_id, t2si.stock_suppl
                from T2_ARTICLES t2a 
                    left outer join T2_BRANDS t2b on t2b.BRAND_ID=t2a.BRAND_ID 
                    left outer join T2_NAMES t2n on t2n.ART_ID=t2a.ART_ID 
                    left outer join T2_BARCODES t2bc on t2bc.ART_ID=t2a.ART_ID 
                    left outer join T2_GOODS_GROUP t2gg on t2gg.ART_ID=t2a.ART_ID 
                    left outer join GOODS_GROUP gg on gg.ID=t2gg.GOODS_GROUP_ID 
                    left outer join T2_ARTICLES_STRORAGE t2asc on t2asc.ART_ID=t2a.ART_ID 
                    left outer join T2_ARTICLES_PRICE_RATING t2apr on (t2apr.art_id=t2a.ART_ID and t2apr.in_use=1)
                    left outer join T2_SUPPL_ARTICLES_IMPORT t2sai on (t2sai.art_id=t2a.ART_ID)
                    left outer join T2_SUPPL_IMPORT t2si on (t2si.art_id=t2sai.art_id and t2si.suppl_id=t2sai.suppl_id and t2si.status=1)
                    left outer join STORAGE s on s.id=t2asc.STORAGE_ID
                where t2a.ART_ID in ($art_id_str) and t2b.`VISIBLE`='1'";
            }
        }
        if ($search_type==1){
            $query="select t2a.ART_ID,t2a.BRAND_ID,t2a.ARTICLE_NR_DISPL, t2b.BRAND_NAME, t2n.NAME, t2n.INFO, t2bc.BARCODE, gg.NAME as goods_group_name, t2apr.price_".$price_lvl.", t2sai.suppl_id, t2sai.return_delay, t2sai.warranty_info, t2si.price_usd, t2si.client_storage_id, t2si.stock_suppl
            from T2_ARTICLES t2a 
                left outer join T2_BRANDS t2b on t2b.BRAND_ID=t2a.BRAND_ID 
                left outer join T2_NAMES t2n on t2n.ART_ID=t2a.ART_ID 
                left outer join T2_BARCODES t2bc on t2bc.ART_ID=t2a.ART_ID 
                left outer join T2_GOODS_GROUP t2gg on t2gg.ART_ID=t2a.ART_ID 
                left outer join GOODS_GROUP gg on gg.ID=t2gg.GOODS_GROUP_ID 
                left outer join T2_ARTICLES_PRICE_RATING t2apr on (t2apr.art_id=t2a.ART_ID and t2apr.in_use=1)
                left outer join T2_SUPPL_ARTICLES_IMPORT t2sai on (t2sai.art_id=t2a.ART_ID)
                left outer join T2_SUPPL_IMPORT t2si on (t2si.art_id=t2sai.art_id and t2si.suppl_id=t2sai.suppl_id and t2si.status=1)
            where t2a.ARTICLE_NR_SEARCH='$art' or t2a.ARTICLE_NR_DISPL='$art' and t2b.`VISIBLE`='1';";
        }
        if ($search_type==2){
            $query="select t2a.ART_ID,t2a.BRAND_ID,t2a.ARTICLE_NR_DISPL, t2b.BRAND_NAME, t2n.NAME, t2n.INFO, t2bc.BARCODE, gg.NAME as goods_group_name, SUM(`t2asc`.`AMOUNT`) as stock, SUM(`t2asc`.`RESERV_AMOUNT`) as reserv, s.name as storage_name, s.id as storage_id, t2apr.price_".$price_lvl.", t2sai.suppl_id, t2sai.return_delay, t2sai.warranty_info, t2si.price_usd, t2si.client_storage_id, t2si.stock_suppl
            from T2_ARTICLES t2a 
                left outer join T2_BRANDS t2b on t2b.BRAND_ID=t2a.BRAND_ID 
                left outer join T2_NAMES t2n on t2n.ART_ID=t2a.ART_ID 
                left outer join T2_BARCODES t2bc on t2bc.ART_ID=t2a.ART_ID 
                left outer join T2_GOODS_GROUP t2gg on t2gg.ART_ID=t2a.ART_ID 
                left outer join GOODS_GROUP gg on gg.ID=t2gg.GOODS_GROUP_ID 
                left outer join T2_ARTICLES_STRORAGE t2asc on t2asc.ART_ID=t2a.ART_ID 
                left outer join T2_ARTICLES_PRICE_RATING t2apr on (t2apr.art_id=t2a.ART_ID and t2apr.in_use=1)
                left outer join T2_SUPPL_ARTICLES_IMPORT t2sai on (t2sai.art_id=t2a.ART_ID)
                left outer join T2_SUPPL_IMPORT t2si on (t2si.art_id=t2sai.art_id and t2si.suppl_id=t2sai.suppl_id and t2si.status=1)
                left outer join STORAGE s on s.id=t2asc.STORAGE_ID
            where t2bc.BARCODE='$art' and t2b.`VISIBLE`='1';";
        }
        $r=$db->query($query);$n=$db->num_rows($r);$list="";
        if ($list2==""){  // сработал внешний фильр или основной поиск с выбором бренда
            for ($i=1;$i<=$n;$i++){$amountRestNotTpoint="";$amountRestTpoint="";$price=0;$suppl_storage_code=0;
                $art_id=$db->result($r,$i-1,"ART_ID");
                //$kind_id=$ak[$art_id];
                //$relation=$rk[$art_id];
                $brand_id=$db->result($r,$i-1,"BRAND_ID");
                $article_nr_displ=$db->result($r,$i-1,"ARTICLE_NR_DISPL");
                $brand_name=$db->result($r,$i-1,"BRAND_NAME");
                $name=$db->result($r,$i-1,"NAME");
                //$info=$db->result($r,$i-1,"INFO");
                $barcode=$db->result($r,$i-1,"BARCODE");
                $goods_group_name=$db->result($r,$i-1,"goods_group_name");

                $suppl_id=$db->result($r,$i-1,"suppl_id");
                if ($suppl_id==0){
                    $price=$db->result($r,$i-1,"price_".$price_lvl);
                    if ($margin_price_lvl>0){
                        $price=$price+round($price*$margin_price_lvl/100,2);
                    }
//                    $storage_id=$db->result($r,$i-1,"storage_id");$storage_name=$db->result($r,$i-1,"storage_name");
//                    $cell_id=$db->result($r,$i-1,"cell_id");$cell_name=$db->result($r,$i-1,"cell_name");
//                    $stock=$db->result($r,$i-1,"stock");
//                    $reserv=$db->result($r,$i-1,"reserv");
//                    $dp_amount=$this->getArticleInDp($art_id,$dp_id);
                    //$rem_amount=$this->getArticleRemoteStorageAmount($art_id,$storage_id);
                    list($tpoint_stock,$tpoint_reserv)=$this->getArticleRestTpoint($art_id,$tpoint_id); $amountRestTpoint="$tpoint_stock/$tpoint_reserv";
                    //list($tpoint2_stock,$tpoint2_reserv)=$this->getArticleRestNotTpoint($art_id,$tpoint_id);
                }
                if ($suppl_id>0){
                    //$return_delay=$db->result($r,$i-1,"return_delay");
                    //$warranty_info=$db->result($r,$i-1,"warranty_info");
                    $suppl_price_usd=$db->result($r,$i-1,"price_usd");
                    $suppl_storage_id=$db->result($r,$i-1,"client_storage_id");
                    $suppl_stock=$db->result($r,$i-1,"stock_suppl");

                    list($price_in_vat,$show_in_vat,$price_add_vat)=$this->getSupplVatConditions($suppl_id);

                    $suppl_storage_code=$suppl_id.".".$suppl_storage_id;
                    $price_suppl=$suppl_price_usd;
                    //T_POINT_SUPPL_FM
                    //Step 1;
                    list($suppl_margin_fm,$suppl_delivery_fm,$suppl_margin2_fm)=$this->getTpointSupplFm($tpoint_id,$suppl_id,$suppl_storage_id,$price_suppl,$price_suppl_lvl);
                    if ($suppl_margin_fm>0){
                        $price=($price_suppl+$price_suppl*$suppl_margin_fm/100)-$price_suppl;
                        if ($price>$suppl_delivery_fm){
                            $price=($price_suppl+$price_suppl*$suppl_margin_fm/100);
                        }
                        if ($price<=$suppl_delivery_fm){
                            $price=$price_suppl+$price_suppl*$suppl_margin2_fm/100+$suppl_delivery_fm;
                        }
                        //Step 2; Client Margin
                        if ($margin_price_suppl_lvl>0 && $margin_price_suppl_lvl!=""){
                            $price=$price+$price*$margin_price_suppl_lvl/100;
                        }
                        //Step 3; VAT
                        //$price_in_vat,$show_in_vat,$price_add_vat
                        if ($client_vat==1){
                            if ($price_in_vat==0 && $show_in_vat==1 && $price_add_vat==1){
                                $price=$price+$price*20/100;
                            }
                            if ($price_in_vat==0 && $show_in_vat==0){
                                $price=0;
                            }
                        }
                    }
                    $price=round($price,2);
                    $suppl_stock_show=$suppl_stock;
                    if ($suppl_stock_show>=10){$suppl_stock_show=">10";}
                    $amountRestNotTpoint="$suppl_stock_show / ";
                }

                if ($suppl_id==0 || $suppl_id>0 && $price>0){
                    $function="setArticleToSelectAmountDp(\"$art_id\",\"$article_nr_displ\",\"$brand_id\",\"$brand_name\",\"$dp_id\")";
                    if ($suppl_id>0){$function="showDpSupplAmountInputWindow(\"$art_id\",\"$article_nr_displ\",\"$brand_id\",\"$brand_name\",\"$dp_id\",\"$suppl_id\",\"$suppl_storage_id\",\"$price\")";}
                    $list.="<tr style='cursor:pointer'  onclick='$function'>
                        <td class='text-center'>$article_nr_displ</td>
                        <td class='text-center'>$brand_name</td>
                        <td class='text-center'>$name</td>
                        <td class='text-center'>$price</td>
                        <td class='text-center'>$amountRestTpoint</td>
                        <td class='text-right'>$amountRestNotTpoint</td>
                        <td class='text-center'>$suppl_storage_code</td>
                        <td class='text-center'>$barcode</td>
                        <td class='text-center'>$goods_group_name</td>
                        <td class='text-center'>$art_id</td>
                    </tr>";
                }
            }
        }
        return array($list,$list2);
    }

    function getSupplVatConditions($suppl_id){$db=DbSingleton::getDb();$price_in_vat=0;$show_in_vat=0;$price_add_vat=0;
        $query="select * from A_CLIENTS_VAT_CONDITIONS where client_id='$suppl_id' limit 0,1;";
        $r=$db->query($query);$n=$db->num_rows($r);
        if ($n==1){
            $price_in_vat=$db->result($r,0,"price_in_vat");
            $show_in_vat=$db->result($r,0,"show_in_vat");
            $price_add_vat=$db->result($r,0,"price_add_vat");
        }
        return array($price_in_vat,$show_in_vat,$price_add_vat);
    }

    function getTpointSupplFm($tpoint_id,$suppl_id,$suppl_storage_id,$price_suppl,$price_suppl_lvl){$dbt=DbSingleton::getTokoDb();$margin=0;$delivery=0;$margin2=0;
        $query="select `margin`,`delivery`,`margin2` from T_POINT_SUPPL_FM where tpoint_id='$tpoint_id' and suppl_id='$suppl_id' and suppl_storage_id='$suppl_storage_id' and price_from<='$price_suppl' and price_to>='$price_suppl' and price_rating_id='$price_suppl_lvl' limit 0,1;";
        $r=$dbt->query($query);$n=$dbt->num_rows($r);
        if ($n==1){
            $margin=$dbt->result($r,0,"margin");
            $delivery=$dbt->result($r,0,"delivery");
            $margin2=$dbt->result($r,0,"margin2");
        }
        return array($margin,$delivery,$margin2);
    }

    function getArticleInDp($art_id,$dp_id){$db=DbSingleton::getDb();
        $r=$db->query("select sum(amount) as amount from J_DP_STR where art_id='$art_id' and dp_id='$dp_id';");$amount=0+$db->result($r,0,"amount");
        return $amount;
    }

    function getArticleRemoteStorageAmount($art_id,$cur_storage_id){$db=DbSingleton::getTokoDb();
        $r=$db->query("select sum(AMOUNT) as amount, sum(RESERV_AMOUNT) as reserv from T2_ARTICLES_STRORAGE where art_id='$art_id' and STORAGE_ID!='$cur_storage_id';");
        $amount=0+$db->result($r,0,"amount")-$db->result($r,0,"reserv");
        return $amount;
    }

    function setArticleToSelectAmountDp($art_id,$dp_id){
        $form="";$form_htm=RD."/tpl/dp_select_amount_article_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $form=str_replace("{dp_rest_storage_list}",$this->showArticleRestStorageSelectList($art_id,$dp_id),$form);
        $form=str_replace("{art_id}",$art_id,$form);
        return $form;
    }

    function showDpArticleAmountChange($art_id,$dp_str_id,$amount){$db=DbSingleton::getDb();
        $form="";$form_htm=RD."/tpl/dp_select_amount_article_change_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select * from J_DP_STR where id='$dp_str_id' and status_dps='93' limit 0,1");
        $article_nr_displ=$db->result($r,0,"article_nr_displ");
        $brand_id=$db->result($r,0,"brand_id");$brand_name=$this->getBrandName($brand_id);
        $amount=$db->result($r,0,"amount");
        $storage_id=$db->result($r,0,"storage_id_from");
        list($info,$max_moving)=$this->showArticleRestStorageSelectText($art_id,$storage_id,$amount);
        $form=str_replace("{storage_name}",$this->getStorageName($storage_id),$form);
        $form=str_replace("{amountRestText}",$info,$form);
        $form=str_replace("{max_moving}",$max_moving,$form);
        $form=str_replace("{cur_amount}",$amount,$form);
        $form=str_replace("{dp_str_id}",$dp_str_id,$form);
        return array($form,$article_nr_displ,$brand_name);
    }

    function showArticleRestStorageSelectText($art_id,$storage_id,$input_amount,$amountEx=null){$db=DbSingleton::getTokoDb();$info="";$max_moving=$amount=0;
        $r=$db->query("select s.id,s.name,t2as.AMOUNT,t2as.RESERV_AMOUNT from STORAGE s 
            inner join T2_ARTICLES_STRORAGE t2as on t2as.STORAGE_ID=s.id 
        where s.status='1' and t2as.ART_ID='$art_id' and t2as.STORAGE_ID='$storage_id' order by s.name asc, s.id asc;");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $amount=$db->result($r,$i-1,"AMOUNT");
            $reserv_amount=$db->result($r,$i-1,"RESERV_AMOUNT");
            $max_moving=$amount+$amountEx;
            $info="Залишок: $amount | Резерв: $reserv_amount<br>У поточному записі: $input_amount";
        }
        return array($info,$max_moving,$amount);
    }

    function showArticleRestStorageCellSelectText($art_id,$dp_id,$cur_amount,$cell_id,$storage_id_from){$db=DbSingleton::getTokoDb();$info="";
        $reserv_amount=$reserv_amount_storage=0;$max_moving=$amount=0;
        $query="select sc.id,sc.cell_value,t2asc.AMOUNT,t2asc.RESERV_AMOUNT,t2as.AMOUNT as AMOUNT_STORAGE,t2as.RESERV_AMOUNT as RESERV_AMOUNT_STORAGE  
        from STORAGE_CELLS sc 
            inner join T2_ARTICLES_STRORAGE_CELLS t2asc on t2asc.STORAGE_CELLS_ID=sc.id 
            left outer join T2_ARTICLES_STRORAGE t2as on t2as.STORAGE_ID=sc.storage_id 
        where sc.status='1' and t2asc.ART_ID='$art_id' and t2as.ART_ID='$art_id' and t2asc.STORAGE_CELLS_ID='$cell_id' order by sc.cell_value asc,sc.id asc;";
        $r=$db->query($query);$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $amount=$db->result($r,$i-1,"AMOUNT");
            $reserv_amount+=$db->result($r,$i-1,"RESERV_AMOUNT");
            $amount_storage=$db->result($r,$i-1,"AMOUNT_STORAGE");
            $reserv_amount_storage+=$db->result($r,$i-1,"RESERV_AMOUNT_STORAGE");
            if ($amount>$amount_storage){$amount=$amount_storage; $reserv_amount=$reserv_amount_storage;}
            $max_moving=$amount+$cur_amount;
            $info="Залишок: $amount | Резерв: $reserv_amount<br>У поточному записі: $cur_amount";
        }
        return array($info,$max_moving,$amount);
    }

    function getDpTpoint($dp_id){$db=DbSingleton::getDb();$tpoint_id=0;
        $r=$db->query("select tpoint_id from J_DP where id='$dp_id' limit 0,1");$n=$db->num_rows($r);
        if ($n==1){ $tpoint_id=$db->result($r,0,"tpoint_id"); }
        return $tpoint_id;
    }

    function getArticleTpointDeliveryInfo($tpoint_id,$art_id){$db=DbSingleton::getTokoDb();$slave=new slave; $info="Не вказано"; $week_day=date("N");$cur_time=date("H:i:s");
        $query="select dt.* from T_POINT_DELIVERY_TIME dt 
            left outer join STORAGE s on s.id=dt.storage_id
            left outer join T2_ARTICLES_STRORAGE t2as on t2as.STORAGE_ID=s.id
        where dt.status='1' and dt.tpoint_id='$tpoint_id' and dt.week_day='$week_day' and dt.time_from<='$cur_time' and dt.time_to>='$cur_time' and s.status='1' and t2as.ART_ID='$art_id' order by dt.delivery_days asc limit 0,1;";
        $r=$db->query($query);$n=$db->num_rows($r);
        if ($n==1){
            $delivery_days=$db->result($r,0,"delivery_days");
            $time_from_del=substr($db->result($r,0,"time_from_del"),0,-3);
            $time_to_del=substr($db->result($r,0,"time_to_del"),0,-3);
            $week=$db->result($r,0,"week_day"); $week+=$delivery_days;
            $week=date('N', strtotime(' + '.$delivery_days.' days'));
            $week_day_short=$slave->get_weekday_abr($week);
            $date_del=date('d.m', strtotime(' + '.$delivery_days.' days'));
            $info="$delivery_days - дн. $date_del ($week_day_short)<br>з $time_from_del до $time_to_del";
        }
        return $info;
    }

    function getArticleTpointSupplDeliveryInfo($tpoint_id,$art_id){$db=DbSingleton::getTokoDb(); $slave=new slave; $info="Не вказано"; $week_day=date("N");$cur_time=date("H:i:s");
        $query="select dt.* from T_POINT_SUPPL_DELIVERY_TIME dt 
            left outer join T2_SUPPL_IMPORT t2si on (t2si.suppl_id=dt.suppl_id and t2si.client_storage_id=dt.suppl_storage_id)
        where dt.status='1' and dt.tpoint_id='$tpoint_id' and t2si.art_id='$art_id' and t2si.status=1 and dt.week_day='$week_day' and dt.time_from<='$cur_time' and dt.time_to>='$cur_time' and t2si.price_usd>0 order by dt.delivery_days limit 0,1;";
        $r=$db->query($query);$n=$db->num_rows($r);
        if ($n==1){
            $delivery_days=$db->result($r,0,"delivery_days");
            $time_from_del=substr($db->result($r,0,"time_from_del"),0,-3);
            $time_to_del=substr($db->result($r,0,"time_to_del"),0,-3);
            $week=$db->result($r,0,"week_day"); $week+=$delivery_days;
            $week=date('N', strtotime(' + '.$delivery_days.' days'));
            $week_day_short=$slave->get_weekday_abr($week);
            $date_del=date('d.m', strtotime(' + '.$delivery_days.' days'));
            $info="$delivery_days - дн. $date_del ($week_day_short)<br>з $time_from_del до $time_to_del";
        }
        return $info;
    }

    function getArticleTpointSupplPriceUsdDeliveryInfo($tpoint_id,$art_id){$db=DbSingleton::getTokoDb(); $price=0; $info="Не вказано"; $week_day=date("N");$cur_time=date("H:i:s"); $slave=new slave;
        $query="select t2si.price_usd,dt.* from T_POINT_SUPPL_DELIVERY_TIME dt 
            left outer join T2_SUPPL_IMPORT t2si on (t2si.suppl_id=dt.suppl_id and t2si.client_storage_id=dt.suppl_storage_id)
        where dt.status='1' and dt.tpoint_id='$tpoint_id' and t2si.art_id='$art_id' and t2si.status=1 and t2si.price_usd>0 and dt.week_day='$week_day' and dt.time_from<='$cur_time' and dt.time_to>='$cur_time' order by t2si.price_usd,dt.delivery_days,dt.giveout_time asc limit 0,1;";
        $r=$db->query($query);$n=$db->num_rows($r);
        if ($n==1){
            $price=$db->result($r,0,"price_usd");
            $delivery_days=$db->result($r,0,"delivery_days");
            $time_from_del=substr($db->result($r,0,"time_from_del"),0,-3);
            $time_to_del=substr($db->result($r,0,"time_to_del"),0,-3);
            $week=$db->result($r,0,"week_day"); $week+=$delivery_days;
            $week=date('N', strtotime(' + '.$delivery_days.' days'));
            $week_day_short=$slave->get_weekday_abr($week);
            $date_del=date('d.m', strtotime(' + '.$delivery_days.' days'));
            $info="$delivery_days - дн. $date_del ($week_day_short)<br>з $time_from_del до $time_to_del";
        }
        return array($price,$info);
    }

    function getTpointDeliveryInfo($tpoint_id,$storage_id){$db=DbSingleton::getDb(); $info="Не вказано"; $week_day=date("N");$cur_time=date("H:i:s");$slave=new slave;
        $r=$db->query("select delivery_days, week_day, time_from_del, time_to_del from T_POINT_DELIVERY_TIME where status='1' and tpoint_id='$tpoint_id' and storage_id='$storage_id' and week_day='$week_day' and time_from<='$cur_time' and time_to>='$cur_time' order by delivery_days asc limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $delivery_days=$db->result($r,0,"delivery_days");
            $time_from_del=substr($db->result($r,0,"time_from_del"),0,-3);
            $time_to_del=substr($db->result($r,0,"time_to_del"),0,-3);
            $week=$db->result($r,0,"week_day"); $week+=$delivery_days;
            $week=date('N', strtotime(' + '.$delivery_days.' days'));
            $week_day_short=$slave->get_weekday_abr($week);
            $date_del=date('d.m', strtotime(' + '.$delivery_days.' days'));
            $info="$delivery_days - дн. $date_del ($week_day_short)<br>з $time_from_del до $time_to_del";
        }
        return $info;
    }

    function getTpointSupplDeliveryInfo($tpoint_id,$suppl_id,$suppl_storage_id){$db=DbSingleton::getDb();$slave=new slave; $info="Не вказано"; $week_day=date("N");$cur_time=date("H:i:s");
        $r=$db->query("select delivery_days, week_day, time_from_del, time_to_del 
        from T_POINT_SUPPL_DELIVERY_TIME 
        where status='1' and tpoint_id='$tpoint_id' and suppl_storage_id='$suppl_storage_id' and suppl_id='$suppl_id' and week_day='$week_day' and time_from<='$cur_time' and time_to>='$cur_time' limit 0,1;");
        $n=$db->num_rows($r);
        if ($n==1){
            $delivery_days=$db->result($r,0,"delivery_days");
            $time_from_del=substr($db->result($r,0,"time_from_del"),0,-3);
            $time_to_del=substr($db->result($r,0,"time_to_del"),0,-3);
            $week=$db->result($r,0,"week_day"); $week+=$delivery_days;
            $week=date('N', strtotime(' + '.$delivery_days.' days'));
            $week_day_short=$slave->get_weekday_abr($week);
            $date_del=date('d.m', strtotime(' + '.$delivery_days.' days'));
            $info="$delivery_days - дн. $date_del ($week_day_short) з $time_from_del до $time_to_del";
        }
        return $info;
    }

    function getArticleStorageAmountDp($art_id,$dp_id,$storage_id){$db=DbSingleton::getDb();$amount=0;
        $r=$db->query("select amount from J_DP_STR where dp_id='$dp_id' and status_dps='93' and storage_id_from='$storage_id' and art_id='$art_id' limit 0,1");$n=$db->num_rows($r);
        if ($n==1){ $amount=$db->result($r,0,"amount"); }
        return $amount;
    }

    function getArticleSupplStorageAmountDp($art_id,$dp_id,$suppl_id,$suppl_storage_id){$db=DbSingleton::getDb();$amount=0;
        $r=$db->query("select amount from J_DP_STR where dp_id='$dp_id' and status_dps='93' and suppl_id='$suppl_id' and suppl_storage_id='$suppl_storage_id' and art_id='$art_id' limit 0,1");$n=$db->num_rows($r);
        if ($n==1){ $amount=$db->result($r,0,"amount"); }
        return $amount;
    }

    function showArticleRestStorageSelectList($art_id,$dp_id){$db=DbSingleton::getTokoDb();$list="";
        $where=""; $tpoint_id=$this->getDpTpoint($dp_id);
        $query="select s.id,s.name,t2as.AMOUNT, t2as.RESERV_AMOUNT from STORAGE s 
            left outer join T2_ARTICLES_STRORAGE t2as on t2as.STORAGE_ID=s.id 
        where s.status='1' and t2as.ART_ID='$art_id' $where order by s.name asc,s.id asc;";
        $r=$db->query($query);$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"name");
            $amount=$db->result($r,$i-1,"AMOUNT");
            $reserv_amount=$db->result($r,$i-1,"RESERV_AMOUNT");
            $cur_amount=$this->getArticleStorageAmountDp($art_id,$dp_id,$id);
            $reserv_amount_rest=$reserv_amount-$cur_amount;
            $delivery_info=$this->getTpointDeliveryInfo($tpoint_id,$id);
            if ($amount!=0 || $cur_amount!=0 || $reserv_amount_rest!=0){
                $list.="<tr onClick=\"showDpAmountInputWindow('$art_id','$id');\" style='cursor:pointer'>
                    <td>$i <input type='hidden' id='storage_amount_id' value='$id'></td>
                    <td>$name</td>
                    <td>$amount</td>
                    <td>$cur_amount</td>
                    <td>$reserv_amount_rest</td>
                    <td>$delivery_info</td>
                </tr>";
            }
        }
        return $list;
    }

    function showDpAmountInputWindow($art_id,$dp_id,$storage_id){
        $form="";$form_htm=RD."/tpl/dp_amount_window.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $form=str_replace("{art_id}",$art_id,$form);
        $amount=$this->getArticleStorageAmountDp($art_id,$dp_id,$storage_id);
        $form=str_replace("{amount}",$amount,$form);
        return $form;
    }

    function showDpSupplAmountInputWindow($art_id,$article_nr_displ,$brand_id,$dp_id,$suppl_id,$suppl_storage_id,$price){
        $form="";$form_htm=RD."/tpl/dp_amount_suppl_window.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $form=str_replace("{art_id}",$art_id,$form);
        $amount=$this->getArticleSupplStorageAmountDp($art_id,$dp_id,$suppl_id,$suppl_storage_id);
        require_once RD."/lib/catalogue_class.php";$cat=new catalogue;
        $form=str_replace("{amount}",$amount,$form);
        $form=str_replace("{price}",$price,$form);
        $summ=$amount*$price;
        $form=str_replace("{summ}",$summ,$form);
        $form=str_replace("{article_nr_displ}",$article_nr_displ,$form);
        $form=str_replace("{brand_id}",$brand_id,$form);
        $form=str_replace("{suppl_id}",$suppl_id,$form);
        $suppl_storage_name=$cat->getSupplStorageName($suppl_storage_id);
        $form=str_replace("{suppl_storage_id}",$suppl_storage_id,$form);
        $form=str_replace("{suppl_storage_code}",$suppl_storage_name." ($suppl_id.$suppl_storage_id)",$form);
        $form=str_replace("{suppl_delivery_info}",$this->getTpointSupplDeliveryInfo($this->getDpTpoint($dp_id),$suppl_id,$suppl_storage_id),$form);
        return $form;
    }

    function showArticleRestStorageCellsList($art_id,$storage_id){$db=DbSingleton::getTokoDb();$list="<option value='0'>-- Оберіть зі списку --</option>";
        $query="SELECT sc.id, sc.cell_value, t2asc.AMOUNT, t2asc.RESERV_AMOUNT,t2as.AMOUNT as AMOUNT_STORAGE, t2as.RESERV_AMOUNT as RESERV_AMOUNT_STORAGE
        FROM STORAGE_CELLS sc
            LEFT OUTER JOIN T2_ARTICLES_STRORAGE_CELLS t2asc ON ( t2asc.STORAGE_CELLS_ID = sc.id )
            LEFT OUTER JOIN T2_ARTICLES_STRORAGE t2as ON ( t2as.STORAGE_ID = sc.storage_id )
        WHERE sc.status = '1' AND t2asc.ART_ID = '$art_id' AND t2as.ART_ID = '$art_id' AND sc.storage_id='$storage_id' ORDER BY sc.cell_value ASC , sc.id ASC;";
        $r=$db->query($query);$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"cell_value");
            $amount=$db->result($r,$i-1,"AMOUNT");
            $reserv_amount=$db->result($r,$i-1,"RESERV_AMOUNT");
            $amount_storage=$db->result($r,$i-1,"AMOUNT_STORAGE");
            $reserv_amount_storage=$db->result($r,$i-1,"RESERV_AMOUNT_STORAGE");
            if ($amount>$amount_storage){$amount=$amount_storage; $reserv_amount=$reserv_amount_storage;}
            $max_moving=$amount;
            if ($reserv_amount!=0 || $amount!=0){
                $list.="<option value='$id' data-max-mov='$max_moving' data-cellId-mov='0'>$name | Залишок: $amount; Резерв: $reserv_amount; </option>";
            }
        }
        return $list;
    }

    function showStorageCellsList($storage_id,$exclude_id){$db=DbSingleton::getTokoDb();$list="<option value='0'>-- Оберіть зі списку --</option>";
        $query=" SELECT id, cell_value FROM STORAGE_CELLS WHERE status = '1' AND storage_id='$storage_id' AND id<>'$exclude_id'  ORDER BY cell_value ASC , id ASC;";
        $r=$db->query($query);$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"cell_value");
            $list.="<option value='$id'>$name</option>";
        }
        return $list;
    }

    function getArticleName($art_id){$db=DbSingleton::getTokoDb();$name="";
        $r=$db->query("select * from T2_NAMES where ART_ID='$art_id' and `LANG_ID`='41' limit 1;");$n=$db->num_rows($r);
        if ($n==1){
            $name=$db->result($r,0,"NAME");
        }
        if($n==0) {
            $r=$db->query("select * from T2_NAMES where ART_ID='$art_id' and `LANG_ID`='16' limit 1;");
            $name=$db->result($r,0,"NAME");
        }
        return $name;
    }

    function getArticleWightVolume($art_id){$db=DbSingleton::getTokoDb();$weight=0;$volume=0;$weight2=0;
        $r=$db->query("select VOLUME,WEIGHT_BRUTTO,WEIGHT_NETTO from T2_PACKAGING where ART_ID='$art_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $weight=$db->result($r,0,"WEIGHT_BRUTTO");
            $weight2=$db->result($r,0,"WEIGHT_NETTO");
            $volume=$db->result($r,0,"VOLUME");
        }
        return array($weight,$volume,$weight2);
    }

    function getArticleReservType($tpoint_id,$storage_id){$db=DbSingleton::getTokoDb();$reserv_type_id=68;
        $r=$db->query("select * from T_POINT_STORAGE where `tpoint_id`='$tpoint_id' and status='1' and `storage_id`='$storage_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $local=$db->result($r,0,"local");
            if ($local==41){$reserv_type_id=67;}
        }if ($n==0){$reserv_type_id=68;}
        return $reserv_type_id;
    }

    function getArticleRestTpoint($art_id,$tpoint_id){$db=DbSingleton::getTokoDb();$stock=0;$reserv=0;$storage_id=0;
        $r=$db->query("select SUM(t2as.`AMOUNT`) as stock, SUM(t2as.`RESERV_AMOUNT`) as reserv, t2as.STORAGE_ID 
        from T2_ARTICLES_STRORAGE t2as 
            left outer join T_POINT_STORAGE tps on tps.storage_id=t2as.STORAGE_ID 
        where t2as.ART_ID='$art_id' and tps.`tpoint_id`='$tpoint_id' and tps.status='1';");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $stock+=$db->result($r,$i-1,"stock");
            $reserv+=$db->result($r,$i-1,"reserv");
            $storage_id=$db->result($r,0,"STORAGE_ID");
        }
        return array($stock,$reserv,$storage_id);
    }

    function getArticleRestNotTpoint($art_id,$tpoint_id){$db=DbSingleton::getTokoDb();$stock=0;$reserv=0;$storage_id=0;
        $r=$db->query("select SUM(t2as.`AMOUNT`) as stock, SUM(t2as.`RESERV_AMOUNT`) as reserv, t2as.STORAGE_ID 
        from T2_ARTICLES_STRORAGE t2as 
            left outer join T_POINT_STORAGE tps on tps.storage_id=t2as.STORAGE_ID 
        where t2as.ART_ID='$art_id' and tps.`tpoint_id`!='$tpoint_id' and tps.status='1';");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $stock+=$db->result($r,$i-1,"stock");
            $reserv+=$db->result($r,$i-1,"reserv");
            $storage_id=$db->result($r,0,"STORAGE_ID");
        }
        return array($stock,$reserv,$storage_id);
    }

    function getArticleRestStorage($art_id,$storage_id){$db=DbSingleton::getTokoDb();$stock=0;$reserv=0;if ($storage_id==""){$storage_id=0;}
        $r=$db->query("select SUM(`AMOUNT`) as stock, SUM(`RESERV_AMOUNT`) as reserv from T2_ARTICLES_STRORAGE where ART_ID='$art_id' and `STORAGE_ID`='$storage_id';");
        $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $stock+=$db->result($r,$i-1,"stock");
            $reserv+=$db->result($r,$i-1,"reserv");
        }
        return array($stock,$reserv);
    }

    function getArticleRestStorageCell($art_id,$storage_id,$cell_id){$db=DbSingleton::getTokoDb();
        $stock=0;$reserv=0;if ($storage_id==""){$storage_id=0;}if ($cell_id==""){$cell_id=0;}
        $r=$db->query("select `AMOUNT` as stock, `RESERV_AMOUNT` as reserv from T2_ARTICLES_STRORAGE_CELLS where ART_ID='$art_id' and `STORAGE_ID`='$storage_id' and `STORAGE_CELLS_ID`='$cell_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $stock=$db->result($r,0,"stock");
            $reserv=$db->result($r,0,"reserv");
        }
        return array($stock,$reserv);
    }

    function loaddpStorage($dp_id){$db=DbSingleton::getDb();
        $form="";$form_htm=RD."/tpl/dp_storage_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select storage_id,storage_cells_id from J_INCOME where `id`='$dp_id' limit 0,1;");
        $storage_id=$db->result($r,0,"storage_id");
        $storage_cells_id=$db->result($r,0,"storage_cells_id");
        $form=str_replace("{dp_id}",$dp_id,$form);
        $form=str_replace("{storage_list}",$this->showStorageSelectList($storage_id),$form);
        $form=str_replace("{storage_cells_list}",$this->showStorageCellsSelectList($storage_id,$storage_cells_id),$form);
        return $form;
    }

    function getStorageName($sel_id){$db=DbSingleton::getTokoDb();$name="";
        $r=$db->query("select name from `STORAGE` where status='1' and id='$sel_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$name=$db->result($r,0,"name");}
        return $name;
    }

    function showStorageSelectList($sel_id,$cells_only=0){$db=DbSingleton::getTokoDb();$list="<option value=0>Оберіть зі списку</option>";
        $query="select * from `STORAGE` where status='1' order by name,id asc;";
        if ($cells_only==1){
            $query="select s.* from `STORAGE` s inner join STORAGE_STR ss on ss.storage_id=s.id where s.status='1' group by ss.storage_id order by s.name,s.id asc;";
        }
        $r=$db->query($query);$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"name");
            $sel="";if ($sel_id==$id){$sel="selected='selected'";}
            $list.="<option value='$id' $sel>$name</option>";
        }
        return $list;
    }

    function getStorageCellName($sel_id){$db=DbSingleton::getTokoDb();$name="";
        $r=$db->query("select cell_value from `STORAGE_CELLS` where status='1' and id='$sel_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$name=$db->result($r,0,"cell_value");}
        return $name;
    }

    function showStorageCellsSelectList($storage_id,$sel_id){$db=DbSingleton::getTokoDb();$list="<option value=0>Оберіть зі списку</option>";$cells_show=1;
        $r=$db->query("select * from `STORAGE_CELLS` where status='1' and storage_id='$storage_id' order by cell_value,id asc;");$n=$db->num_rows($r);
        if ($n==0){$cells_show=0;}
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $cell_value=$db->result($r,$i-1,"cell_value");
            $sel="";if ($sel_id==$id){$sel="selected='selected'";}
            $list.="<option value='$id' $sel>$cell_value</option>";
        }
        return array($list,$cells_show);
    }

    function getCashAbr($sel_id){$db=DbSingleton::getDb();$name="грн";
        $r=$db->query("select abr from CASH where id='$sel_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$name=$db->result($r,0,"abr");}
        return $name;
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

    function getDocTypeSelectList($sel_id){$db=DbSingleton::getDb();$list="<option value=0>Оберіть зі списку</option>";
        $r=$db->query("select id,mcaption from `manual` where ison='1' and `key`='client_sale_type' order by mid,id asc;");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"mcaption");
            $sel="";if ($sel_id==$id){$sel="selected='selected'";}
            $list.="<option value='$id' $sel>$name</option>";
        }
        return $list;
    }

    function getCarrierSelectList($sel_id){$db=DbSingleton::getDb();$list="";
        $r=$db->query("select id,mcaption from `manual` where `key`='carrier_id';");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"mcaption");
            $sel="";if ($sel_id==$id){$sel="selected='selected'";}
            $list.="<option value='$id' $sel>$name</option>";
        }
        return $list;
    }

    function getClientContoSelectList($client_id,$sel_id){$db=DbSingleton::getDb();$list="";
        if ($client_id>0){
            $r=$db->query("select id,name from `A_CLIENTS` where status='1' and (parrent_id='$client_id' or id='$client_id' or id='$sel_id') order by name,id asc;");$n=$db->num_rows($r);
            for ($i=1;$i<=$n;$i++){
                $id=$db->result($r,$i-1,"id");
                $name=$db->result($r,$i-1,"name");
                $sel="";if ($sel_id==$id){$sel="selected='selected'";}
                $list.="<option value='$id' $sel>$name</option>";
            }
        }
        return $list;
    }

    function saveDpStorage($dp_id,$storage_id,$storage_cells_id){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка збереження даних!";
        $dp_id=$slave->qq($dp_id);$storage_id=$slave->qq($storage_id);$storage_cells_id=$slave->qq($storage_cells_id);
        if ($dp_id>0 && $storage_id>0 && $storage_cells_id>0){
            $db->query("update J_INCOME set storage_id='$storage_id', `storage_cells_id`='$storage_cells_id' where id='$dp_id';");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function getRateTypeDeclarationdocumentPos($costums_id,$country_id){$db=DbSingleton::getDb();$manual=new manual;$rate=0;$type_declaration="";$type_declaration_id=0;$duty=0;
        $r=$db->query("select DUTY from T2_COUNTRIES where country_id='$country_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $duty=$db->result($r,0,"DUTY");
        }
        $r=$db->query("select PREFERENTIAL_RATE,FULL_RATE,TYPE_DECLARATION from T2_COSTUMS where costums_id='$costums_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $preferential_rate=$db->result($r,0,"PREFERENTIAL_RATE");
            $full_rate=$db->result($r,0,"FULL_RATE");
            $rate=$preferential_rate; if ($duty==2){$rate=$full_rate;}
            $type_declaration_id=$db->result($r,0,"TYPE_DECLARATION");
            $type_declaration=$manual->getManualMCaption("costums_type_declaration",$type_declaration_id);
        }
        return array($rate,$type_declaration,$type_declaration_id);
    }

    function loadDpCommets($dp_id){$db=DbSingleton::getDb();
        $form="";$form_htm=RD."/tpl/dp_comment_block.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select cc.*,u.name from J_DP_COMMENTS cc 
            left outer join media_users u on u.id=cc.USER_ID 
        where cc.dp_id='$dp_id' order by id desc;");$n=$db->num_rows($r);$list="";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $user_id=$db->result($r,$i-1,"user_id");
            $user_name=$db->result($r,$i-1,"name");
            $data=$db->result($r,$i-1,"data");
            $comment=$db->result($r,$i-1,"comment");
            $block=$form;
            $block=str_replace("{dp_id}",$dp_id,$block);
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

    function saveDpComment($dp_id,$comment){$db=DbSingleton::getDb();
        $slave=new slave;session_start();$user_id=$_SESSION["media_user_id"];$answer=0;$err="Помилка збереження даних!";
        $dp_id=$slave->qq($dp_id);$comment=$slave->qq($comment);
        if ($dp_id>0 && $comment!=""){
            $db->query("insert into J_DP_COMMENTS (`dp_id`,`user_id`,`comment`) values ('$dp_id','$user_id','$comment');");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function dropDpComment($dp_id,$comment_id){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка видалення запису!";
        $dp_id=$slave->qq($dp_id);$comment_id=$slave->qq($comment_id);
        if ($dp_id>0 && $comment_id>0){
            $r=$db->query("select * from J_DP_COMMENTS where dp_id='$dp_id' and id='$comment_id' limit 0,1;");$n=$db->num_rows($r);
            if ($n==1){
                $db->query("delete from J_DP_COMMENTS where dp_id='$dp_id' and id='$comment_id';");
                $answer=1;$err="";
            }
        }
        return array($answer,$err);
    }

    function loadDpCDN($dp_id){$db=DbSingleton::getDb();
        $form="";$form_htm=RD."/tpl/dp_cdn_block.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select cc.*,u.name as user_name from J_DP_CDN cc 
            left outer join media_users u on u.id=cc.USER_ID 
            where cc.dp_id='$dp_id' and cc.status='1' order by cc.file_name asc;");$n=$db->num_rows($r);$list="";
            for ($i=1;$i<=$n;$i++){
                $file_id=$db->result($r,$i-1,"id");
                $file_name=$db->result($r,$i-1,"file_name");
                $name=$db->result($r,$i-1,"name");
                $data=$db->result($r,$i-1,"data");
                $user_name=$db->result($r,$i-1,"user_name");
                $link="http://cdn.myparts.pro/dp_files/$dp_id/$file_name";
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
                $block=str_replace("{dp_id}",$dp_id,$block);
                $block=str_replace("{link}",$link,$block);
                $block=str_replace("{file_view}",$file_view,$block);
                $list.=$block;
            }
            if ($n==0){$list="<h3 class='text-center'>Файли відсутні</h3>";}
            return $list;
    }

    function dpCDNDropFile($dp_id,$file_id){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка видалення файлу!";
        $dp_id=$slave->qq($dp_id);$file_id=$slave->qq($file_id);
        if ($dp_id>0 && $file_id>0){
            $r=$db->query("select FILE_NAME from J_DP_CDN where dp_id='$dp_id' and id='$file_id' limit 0,1;");$n=$db->num_rows($r);
            if ($n==1){
                unlink(RD.'/cdn/dp_files/$dp_id/$file_name');
                $db->query("delete from J_DP_CDN where dp_id='$dp_id' and id='$file_id';");
                $answer=1;$err="";
            }
        }
        return array($answer,$err);
    }

    function loadStateSelectList($country_id,$sel_id){$slave=new slave;
        $list=$slave->showSelectSubList("T2_STATE","COUNTRY_ID","$country_id","STATE_ID","STATE_NAME",$sel_id);
        return $list;
    }

    function loadRegionSelectList($state_id,$sel_id){$slave=new slave;
        return $slave->showSelectSubList("T2_REGION","STATE_ID","$state_id","REGION_ID","REGION_NAME",$sel_id);
    }

    function loadCitySelectList($region_id,$sel_id){$slave=new slave;
        return "<option value='NEW'>Добавити населений пункт</option>".$slave->showSelectSubList("T2_CITY","REGION_ID","$region_id","CITY_ID","CITY_NAME",$sel_id);
    }

    function showCategoryCheckList($dp_id){$db=DbSingleton::getDb();$list="";
        $r=$db->query("select  * from A_CATEGORY where parrent_id=0 order by id asc;");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"name");
            $sel=$this->checkdpCategorySelect($dp_id,$id);
            $ch="";if ($sel==1){$ch=" checked=''";}
            $list.="<label><input type='checkbox' class='i-checks' id='c_category_$i' value='$id' $ch> - $name;</label> ";
        }$list.="<input type='hidden' id='c_category_kol' value='$n'>";
        return $list;
    }

    function checkdpCategorySelect($dp_id,$category_id){$db=DbSingleton::getDb();$ch=0;
        $r=$db->query("select category_id from A_CLIENTS_CATEGORY where dp_id='$dp_id' and category_id='$category_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$ch=1;}
        return $ch;
    }

    function showMovingOpListSelect($sel_id){$db=DbSingleton::getDb();$list="";
        $r=$db->query("select * from J_DP_OP where in_show='1' order by id asc;");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"name");
            $sel="";if ($sel_id==$id){$sel="selected='selected'";}
            $list.="<option value='$id' $sel>$name</option>";
        }
        return $list;
    }

    function showdpDocumentList($dp_id,$dp_op_id,$document_id){$income=new income;$form="";$document_list="";
        if ($dp_op_id==1){
            $form_htm=RD."/tpl/dp_documents_list.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
            $document_list=$income->search_documents_income_list("");
        }
        $form=str_replace("{documents_list}",$document_list,$form);
        $form=str_replace("{dp_id}",$dp_id,$form);
        $form=str_replace("{dp_op_id}",$dp_op_id,$form);
        return array($form,"Реєстр документів основи");
    }

    function finddpDocumentsSearch($dp_id,$dp_op_id,$s_nom){$income=new income;$document_list="";
        if ($dp_op_id==1){$document_list=$income->search_documents_income_list($s_nom);}
        return $document_list;
    }

    function getArtIdByBarcode($barcode){$db=DbSingleton::getTokoDb();$art_id=0;
        $r=$db->query("select ART_ID from T2_BARCODES where BARCODE='$barcode' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){	$art_id=$db->result($r,0,"ART_ID");	}
        return $art_id;
    }

    function getArtId($code,$brand_id){$db=DbSingleton::getTokoDb();$slave=new slave;$cat=new catalogue;$id=0;
        $code=$slave->qq($code); $code=$cat->clearArticle($code);
        $r=$db->query("select ART_ID from T2_ARTICLES where ARTICLE_NR_SEARCH='$code' and BRAND_ID='$brand_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){	$id=$db->result($r,0,"ART_ID");	}
        return $id;
    }

    function getCostumsId($code){$db=DbSingleton::getTokoDb();$slave=new slave;$id=0; $code=$slave->qq($code);
        $r=$db->query("select COSTUMS_ID from T2_COSTUMS where COSTUMS_CODE='$code' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){	$id=$db->result($r,0,"COSTUMS_ID");	}
        return $id;
    }

    function getCountryId($code){$db=DbSingleton::getTokoDb();$slave=new slave;$id=0; $code=$slave->qq($code);
        $r=$db->query("select COUNTRY_ID from T2_COUNTRIES where COUNTRY_NAME='$code' or `ALFA2`='$code' or `ALFA3`='$code' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){	$id=$db->result($r,0,"COUNTRY_ID");	}
        return $id;
    }
    function getBrandId($code){$db=DbSingleton::getTokoDb();$slave=new slave;$id=0; $code=$slave->qq($code);
        $r=$db->query("select BRAND_ID from T2_BRANDS where BRAND_NAME='$code' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){	$id=$db->result($r,0,"BRAND_ID");	}
        return $id;
    }

    function getBrandName($id){$db=DbSingleton::getTokoDb();$name="";
        $r=$db->query("select BRAND_NAME from T2_BRANDS where BRAND_ID='$id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){	$name=$db->result($r,0,"BRAND_NAME");	}
        return $name;
    }

    function getTpointName($id){$db=DbSingleton::getDb();$name="";
        $r=$db->query("select name from T_POINT where id='$id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){	$name=$db->result($r,0,"name");	}
        return $name;
    }

    function getClientName($id){$db=DbSingleton::getDb();$name="";
        $r=$db->query("select name from A_CLIENTS where id='$id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){	$name=$db->result($r,0,"name");	}
        return $name;
    }

    function showWorkPairForm($dp_id){$db=DbSingleton::getDb();$list="";
        $r=$db->query("select PAIR_INDEX from T2_WORK_PAIR where ART_ID='$dp_id';");$n=$db->num_rows($r);
        for ($i=1;$i<=$n+3;$i++){
            $pair_index="";
            if ($i<=$n){$pair_index=$db->result($r,$i-1,"PAIR_INDEX");}
            $list.="<tr><td><input type='text' id='work_pair_$i' value='$pair_index' class='form-control'></td></tr>";
        }$list.="<input type='hidden' id='work_pair_n' value='".($n+3)."'>";
        return $list;
    }

    function labelArtEmptyCount($dp_id,$kol){$label="";
        if ($kol==0 || $kol==""){
            list($weight,$volume,$kol)=$this->updateDpWeightVolume($dp_id);
        }
        if ($kol>0){$label="<span class='label label-tab label-info'>$kol</span>";}
        return array($kol,$label);
    }

    function labelCommentsCount($dp_id){$db=DbSingleton::getDb();$label="";
        $r=$db->query("select count(id) as kol from J_DP_COMMENTS where dp_id='$dp_id';");$kol=0+$db->result($r,0,"kol");
        if ($kol>0){$label="<span class='label label-tab label-info'>$kol</span>";}
        return array($kol,$label);
    }

    function loaddpUnknownArticles($dp_id){$db=DbSingleton::getDb();
        $form="";$form_htm=RD."/tpl/dp_unknown_articles_list.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select * from J_DP j where j.id='$dp_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==0){$form_htm=RD."/tpl/access_deny.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);} }
        if ($n==1){
            list($list,$kol_rows)=$this->showdpUnknownStrList($dp_id);
            $form=str_replace("{UnknownArticlesList}",$list,$form);
            $form=str_replace("{kol_rows}",$kol_rows,$form);
            $form=str_replace("{dp_id}",$dp_id,$form);
        }
        return $form;
    }

    function showdpUnknownStrList($dp_id){$db=DbSingleton::getDb();$list="";
        $r=$db->query("select * from J_DP_STR where dp_id='$dp_id' group by art_id order by id asc;");$n=$db->num_rows($r);$empty_kol=0;
        for ($i=1;$i<=$n;$i++){
            $art_id=$db->result($r,$i-1,"art_id");//$art_id_comment="";if ($art_id==0){$art_id_comment="Не визначено ART_ID! Артикул відсутній у базі";}
            $article_nr_displ=$db->result($r,$i-1,"article_nr_displ");
            $brand_id=$db->result($r,$i-1,"brand_id");$brand_name=$this->getBrandName($brand_id);
            list($weight_brutto,$volume,$weight_netto)=$this->getArticleWightVolume($art_id);
            if ($weight_brutto==0 || $volume==0 || $weight_netto==0){$empty_kol+=1;
                $list.="<tr id='strUnRow_$i'>
                    <td><button class='btn btn-xs btn-warning' onClick='checkdpUnStr(\"$dp_id\",\"$i\",\"$art_id\");'><i class='fa fa-refresh'></i></button></td>
                    <td>$i</td>
                    <td style='min-width:140px;'><input type='hidden' id='artIdUnStr_$i' value='$art_id'><input type='hidden' id='article_nr_displUnStr_$i' value='$article_nr_displ'>$article_nr_displ</td>
                    <td style='min-width:120px;'>$brand_name</td>
                    <td><input type='text' id='volumeUnStr_$i' value='$volume' class='form-control input-xs numberOnlyLong'></td>
                    <td><input type='text' id='weightNettoUnStr_$i' value='$weight_netto' class='form-control input-xs numberOnlyLong'></td>
                    <td><input type='text' id='weightBruttoUnStr_$i' value='$weight_brutto' class='form-control input-xs text-right numberOnlyLong'></td>
                </tr>";
            }
        }
        return array($list,$empty_kol);
    }

    function checkdpUnStr($dp_id,$art_id,$volume,$weight,$weight2){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;$answer=0;$err="";
        $dp_id=$slave->qq($dp_id);$art_id=$slave->qq($art_id);
        $r=$db->query("select oper_status from J_DP where id='$dp_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $oper_status=$db->result($r,0,"oper_status");
            if ($oper_status==30) {
                $volume=$slave->qq($volume);$weight=$slave->qq($weight);$weight2=$slave->qq($weight2);
                if ($art_id>0 && $volume>0 && $weight>0 && $weight2>0){
                    $rs=$db->query("select art_id from `T2_PACKAGING` where art_id='$art_id' limit 0,1;");$ns=$dbt->num_rows($rs);
                    if ($ns==1){ $dbt->query("update `T2_PACKAGING` set `VOLUME`='$volume', `WEIGHT_NETTO`='$weight', `WEIGHT_BRUTTO`='$weight2' where ART_ID='$art_id' limit 1;");	}
                    else{ $dbt->query("insert into `T2_PACKAGING` (`ART_ID`,`VOLUME`,`WEIGHT_NETTO`,`WEIGHT_BRUTTO`) values ('$art_id','$volume','$weight','$weight2');"); }
                    $answer=1;$err="";
                }else {$answer=0;$err="Не заповнені всі поля для артикулу";}
            } else {$answer=0;$err="Переміщення заблоковано. Зміни вносити заборонено.";}
        }
        return array($answer,$err);
    }

    function getTpointDataByStorage($storage_id){$db=DbSingleton::getDb(); $tpoint_id=0;$loc_type_id=0;
        $r=$db->query("select `tpoint_id`,`local` from T_POINT_STORAGE where storage_id='$storage_id' order by id asc limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){	$tpoint_id=$db->result($r,0,"tpoint_id"); $loc_type_id=$db->result($r,0,"local");}
        return array($tpoint_id,$loc_type_id);
    }

    function getdpInfo($dp_id){$db=DbSingleton::getDb();
        $r=$db->query("select * from J_DP where id='$dp_id' limit 0,1;");;
        $prefix=$db->result($r,0,"prefix");
        $doc_nom=$db->result($r,0,"doc_nom");
        $data=$db->result($r,0,"data");
        $storage_id_to=$db->result($r,0,"storage_id_to");$storage_name_to=$this->getStorageName($storage_id_to);
        $comment=$db->result($r,0,"comment");
        return array($prefix,$doc_nom,$data,$storage_id_to,$storage_name_to,$comment);
    }

    function addJuornalRecord($dp_id,$select_id,$status_dp){$db=DbSingleton::getDb();session_start();$user_id=$_SESSION["media_user_id"];
        $db->query("insert into J_DP_SELECT_JOURNAL (`dp_id`,`select_id`,`user_id`,`status_dp`) values ('$dp_id','$select_id','$user_id','$dp_id');");
        return;
    }

    function getdpSelectJournalRecords($dp_id,$select_id){$db=DbSingleton::getDb();$data=array();
        $r=$db->query("select * from J_DP_SELECT_JOURNAL where `dp_id`='$dp_id' and `select_id`='$select_id' order by id asc;"); $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $status_dp=$db->result($r,$i-1,"status_dp");
            $datatime=$db->result($r,$i-1,"datatime");
            $data[$status_dp]=$datatime;
        }
        return $data;
    }

    //======================================================================================

    function startDpExecute($dp_id){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка індексу!";$suppl_ex=0;
        $dp_id=$slave->qq($dp_id);
        if ($dp_id>0){
            $r=$db->query("select tpoint_id,status_dp from J_DP where id='$dp_id' limit 0,1;");$n=$db->num_rows($r);
            if ($n==1){
                $status_dp=$db->result($r,0,"status_dp");
                $tpoint_id=$db->result($r,0,"tpoint_id");
                if ($status_dp==80){
                    $answer=0;$err="Документ уже передано в роботу!";
                }
                if ($status_dp==79){
                    $db->query("update J_DP set status_dp='80' where id='$dp_id' limit 1;");

                    list($usd_to_uah,$eur_to_uah)=$this->getKoursData();
                    $db->query("update `J_DP` set `usd_to_uah`='$usd_to_uah', `eur_to_uah`='$eur_to_uah' where id='$dp_id' limit 1;");

                    $suppl_ex=$this->checkRemoteStorage($dp_id,$tpoint_id);
                    if ($suppl_ex==0){
                        $this->makeDpJmovingStorselPreorder($dp_id,41);
                    }
                    $answer=1;$err="";
                }
            }
        }
        return array($answer,$err,$suppl_ex);
    }

    function checkRemoteStorage($dp_id,$tpoint_id){$db=DbSingleton::getDb();$suppl_ex=0;
        $r=$db->query("select COUNT(ds.id) as kol from J_DP_STR ds left outer join T_POINT_STORAGE ps on ps.storage_id=ds.storage_id_from 
        where ds.dp_id='$dp_id' and ((ps.tpoint_id='$tpoint_id' and `local`='42') or (ps.tpoint_id!='$tpoint_id')) ;");
        $kol_rem_str=$db->result($r,0,"kol");
        if ($kol_rem_str>0){$suppl_ex=1;}
        return $suppl_ex;
    }

    function getStorageToTpointLocal($tpoint_id,$storage_id){$db=DbSingleton::getDb();$local=42;
        $r=$db->query("select `local` from T_POINT_STORAGE where tpoint_id='$tpoint_id' and storage_id='$storage_id' and status='1' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$local=$db->result($r,0,"local");}
        return $local;
    }

    function createStorsel($dp_id,$tpoint_id,$storage_id){$db=DbSingleton::getDb();$cur_date=date("Y-m-d H:i:s");
        session_start();$user_id=$_SESSION["media_user_id"];
        $rm=$db->query("select max(id) as mid from J_SELECT;");$select_id=0+$db->result($rm,0,"mid")+1;
        $db->query("insert into J_SELECT (`id`,`parrent_doc_type_id`,`parrent_doc_id`,`data_create`,`tpoint_id`,`storage_id`,`user_create`) values ('$select_id','2','$dp_id','$cur_date','$tpoint_id','$storage_id','$user_id');");
        return $select_id;
    }

    function makeDpJmovingStorselPreorder($dp_id,$local){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$jmoving=new jmoving;session_start();$user_id=$_SESSION["media_user_id"];
        $tpoint_id=$this->getDpTpoint($dp_id);$cell_use=0;
        $r=$db->query("select storage_id_from from J_DP_STR ds where ds.dp_id='$dp_id' and status_dps='93' group by storage_id_from;");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $storage_id_from=$db->result($r,$i-1,"storage_id_from");
            if ($storage_id_from>0){
                $storage_local=$this->getStorageToTpointLocal($tpoint_id,$storage_id_from);
                if ($storage_local==41){
                    $r1=$db->query("select * from J_DP_STR ds where ds.dp_id='$dp_id' and status_dps='93' and storage_id_from='$storage_id_from';");$n1=$db->num_rows($r1);
                    if ($n1>0){
                        //create storsel;
                        $select_id=$this->createStorsel($dp_id,$tpoint_id,$storage_id_from);$s_volume=0;$s_weight_netto=0;$s_amount=0;$s_articles_amount=0;
                        for ($i1=1;$i1<=$n1;$i1++){
                            $id=$db->result($r1,$i1-1,"id");
                            $art_id=$db->result($r1,$i1-1,"art_id");
                            $article_nr_displ=$db->result($r1,$i1-1,"article_nr_displ");
                            $brand_id=$db->result($r1,$i1-1,"brand_id");
                            $amount=$db->result($r1,$i1-1,"amount");
                            //$price=$db->result($r1,$i1-1,"price");
                            //$price_end=$db->result($r1,$i1-1,"price_end");
                            //$discount=$db->result($r1,$i1-1,"discount");
                            //$summ=$db->result($r1,$i1-1,"summ");
                            $weight_netto=$db->result($r1,$i1-1,"weight_netto");
                            $volume=$db->result($r1,$i1-1,"volume");
                            $s_amount+=$amount;	$s_articles_amount+=1; $s_volume+=$volume;$s_weight_netto+=$weight_netto;
                            //$db->query("insert into J_SELECT_STR (`select_id`,`art_id`,`article_nr_displ`,`brand_id`,`amount`,`storage_id_from`) values ('$select_id','$art_id','$article_nr_displ','$brand_id','$amount','$storage_id_from');");
                            $db->query("update J_DP_STR set status_dps='94' where id='$id' limit 1;"); // обновляем статус записи что бы понимать на каком этапе находится артикул;

                            $rsc=$dbt->query("select * from `T2_ARTICLES_STRORAGE_CELLS` where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_from';");$nsc=$dbt->num_rows($rsc);
                            if ($nsc==0){
                                $r2=$db->query("select max(id) as mid from J_SELECT_STR;");$str_id=0+$db->result($r2,0,"mid")+1;
                                $db->query("insert into J_SELECT_STR (`id`,`select_id`,`art_id`,`article_nr_displ`,`brand_id`,`amount`,`storage_id_from`) values ('$str_id','$select_id','$art_id','$article_nr_displ','$brand_id','$amount','$storage_id_from');");
                                $db->query("update J_DP_STR set cur_select_str_id='$str_id' where id='$id' and dp_id='$dp_id' limit 1;");
                            }
                            if ($nsc>0){
                                for ($isc=1;$isc<=$nsc;$isc++){ $er=0;
                                    $amount_sc=$dbt->result($rsc,$isc-1,"AMOUNT");
                                    $reserv_amount_sc=$dbt->result($rsc,$isc-1,"RESERV_AMOUNT");
                                    $storage_cells_id_sc=$dbt->result($rsc,$isc-1,"STORAGE_CELLS_ID");

                                    if ($amount_sc>=$amount && $amount_sc>0){$isc=$nsc+1;$er=1;
                                        $amount_sc-=$amount;
                                        $reserv_amount_sc+=$amount;
                                        $r2=$db->query("select max(id) as mid from J_SELECT_STR;");$str_id=0+$db->result($r2,0,"mid")+1;
                                        $db->query("insert into J_SELECT_STR (`id`,`select_id`,`art_id`,`article_nr_displ`,`brand_id`,`amount`,`storage_id_from`,`cell_id_from`) values ('$str_id','$select_id','$art_id','$article_nr_displ','$brand_id','$amount','$storage_id_from','$storage_cells_id_sc');");
                                        $db->query("update J_DP_STR set cur_select_str_id='$str_id' where id='$id' and dp_id='$dp_id' limit 1;");
                                        $dbt->query("update `T2_ARTICLES_STRORAGE_CELLS` set `AMOUNT`='$amount_sc', `RESERV_AMOUNT`='$reserv_amount_sc' where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_from' and `STORAGE_CELLS_ID`='$storage_cells_id_sc' limit 1;");
                                    }
                                    if ($amount_sc<$amount && $amount_sc>0 && $er==0){
                                        $amount-=$amount_sc;
                                        $reserv_amount_sc+=$amount_sc;
                                        $db->query("insert into J_SELECT_STR (`select_id`,`art_id`,`article_nr_displ`,`brand_id`,`amount`,`storage_id_from`,`cell_id_from`) values ('$select_id','$art_id','$article_nr_displ','$brand_id','$amount_sc','$storage_id_from','$storage_cells_id_sc');");
                                        $dbt->query("update `T2_ARTICLES_STRORAGE_CELLS` set `AMOUNT`='0', `RESERV_AMOUNT`='$reserv_amount_sc' where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_from' and `STORAGE_CELLS_ID`='$storage_cells_id_sc' limit 1;");
                                    }
                                }
                            }
                        }
                        $db->query("update J_SELECT set `amount`='$s_amount', `articles_amount`='$s_articles_amount',`volume`='$s_volume', `weight_netto`='$s_weight_netto' where id='$select_id' limit 1;");
                    }
                }
                if ($storage_local==42 && $local==41){
                    $r1=$db->query("select * from J_DP_STR ds where ds.dp_id='$dp_id' and status_dps='93' and storage_id_from='$storage_id_from';");$n1=$db->num_rows($r1);
                    if ($n1>0){
                        //create jmoving;
                        $jmoving_id=$jmoving->newJmovingCard(1);$s_volume=0;$s_weight_netto=0;
                        $storage_id_to=$this->getTpointStorageLocal($tpoint_id);
                        list($cell_use_to,$cell_id_to)=$this->getStorageCellsData($storage_id_to);
                        //parrent_type_id: 1-dp,2-vozvrat;
                        $db->query("update J_MOVING set parrent_type_id='1', parrent_doc_id='$dp_id', storage_id_to='$storage_id_to', cell_use='$cell_use', `cell_id_to`='$cell_id_to' where id='$jmoving_id' limit 1;");
                        for ($i1=1;$i1<=$n1;$i1++){
                            $id=$db->result($r1,$i1-1,"id");
                            $art_id=$db->result($r1,$i1-1,"art_id");
                            $article_nr_displ=$db->result($r1,$i1-1,"article_nr_displ");
                            $brand_id=$db->result($r1,$i1-1,"brand_id");
                            $amount=$db->result($r1,$i1-1,"amount");
                            //$price=$db->result($r1,$i1-1,"price");
                            //$price_end=$db->result($r1,$i1-1,"price_end");
                            //$discount=$db->result($r1,$i1-1,"discount");
                            //$summ=$db->result($r1,$i1-1,"summ");
                            $weight_netto=$db->result($r1,$i1-1,"weight_netto");
                            $volume=$db->result($r1,$i1-1,"volume");
                            $s_volume+=$volume;$s_weight_netto+=$weight_netto;
                            $db->query("insert into J_MOVING_STR (`jmoving_id`,`art_id`,`article_nr_displ`,`brand_id`,`amount`,`storage_id_from`) values ('$jmoving_id','$art_id','$article_nr_displ','$brand_id','$amount','$storage_id_from');");
                            $db->query("update J_DP_STR set status_dps='95' where id='$id' limit 1;"); // обновляем статус записи что бы понимать на каком этапе находится артикул;
                        }
                        $jmoving->startJmovingStorageSelect($jmoving_id);
                        $jmoving->makesJmovingStorageSelect($jmoving_id);
                    }
                }
                if ($storage_local==42 && $local==42){
                    $r11=$db->query("select * from J_DP_STR where dp_id='$dp_id';");
                    $r1=$db->query("select * from J_DP_STR ds where ds.dp_id='$dp_id' and status_dps='93' and storage_id_from='$storage_id_from';");$n1=$db->num_rows($r1);
                    if ($n1>0){
                        //get storage tpoint_id
                        list($storage_tpoint_id,$storage_tpoint_id_local)=$this->getTpointDataByStorage($storage_id_from);
                        //creating new dp from current dp
                        $storage_dp_id=$this->newDpFromDp($dp_id,$storage_tpoint_id,$storage_id_from);
                        //update summ weight volume of dp_id
                        $this->updateDpSumm($dp_id); $this->updateDpWeightVolume($dp_id);
                        //update summ weight volume of storage_dp_id
                        $this->updateDpSumm($storage_dp_id); $this->updateDpWeightVolume($storage_dp_id);
                        //move dp_str_id t new dp_id;
                        $db->query("update J_DP_STR set dp_id='$storage_dp_id' where dp_id='$dp_id' and status_dps='93' and storage_id_from='$storage_id_from';");
                        //update dp status
                        $db->query("update J_DP set status_dp='80' where id='$storage_dp_id' limit 1;");
                        //set dp kours
                        list($usd_to_uah,$eur_to_uah)=$this->getKoursData();
                        $db->query("update `J_DP` set `usd_to_uah`='$usd_to_uah', `eur_to_uah`='$eur_to_uah' where `id`='$dp_id' limit 1;");
                        /* create local storsel */
                        $r2=$db->query("select * from J_DP_STR ds where ds.dp_id='$storage_dp_id' and status_dps='93' and storage_id_from='$storage_id_from';");$n2=$db->num_rows($r2);
                        if ($n2>0){
                            $select_id=$this->createStorsel($storage_dp_id,$storage_tpoint_id,$storage_id_from);$s_volume=0;$s_weight_netto=0;$s_amount=0;$s_articles_amount=0;
                            for ($i2=1;$i2<=$n2;$i2++){
                                $id=$db->result($r2,$i2-1,"id");
                                $art_id=$db->result($r2,$i2-1,"art_id");
                                $article_nr_displ=$db->result($r2,$i2-1,"article_nr_displ");
                                $brand_id=$db->result($r2,$i2-1,"brand_id");
                                $amount=$db->result($r2,$i2-1,"amount");
                                //$price=$db->result($r2,$i2-1,"price");
                                //$price_end=$db->result($r2,$i2-1,"price_end");
                                //$discount=$db->result($r2,$i2-1,"discount");
                                //$summ=$db->result($r2,$i2-1,"summ");
                                $weight_netto=$db->result($r2,$i2-1,"weight_netto");
                                $volume=$db->result($r2,$i2-1,"volume");
                                $s_amount+=$amount;	$s_articles_amount+=1; $s_volume+=$volume;$s_weight_netto+=$weight_netto;
                                $db->query("update J_DP_STR set status_dps='94' where id='$id' limit 1;"); // обновляем статус записи что бы понимать на каком этапе находится артикул;

                                $rsc=$dbt->query("select * from `T2_ARTICLES_STRORAGE_CELLS` where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_from';");$nsc=$dbt->num_rows($rsc);
                                if ($nsc==0){
                                    $r3=$db->query("select max(id) as mid from J_SELECT_STR;");$str_id=0+$db->result($r3,0,"mid")+1;
                                    $db->query("insert into J_SELECT_STR (`id`,`select_id`,`art_id`,`article_nr_displ`,`brand_id`,`amount`,`storage_id_from`) values ('$str_id','$select_id','$art_id','$article_nr_displ','$brand_id','$amount','$storage_id_from');");
                                    $db->query("update J_DP_STR set cur_select_str_id='$str_id' where id='$id' and dp_id='$storage_dp_id' limit 1;");
                                }
                                if ($nsc>0){
                                    for ($isc=1;$isc<=$nsc;$isc++){ $er=0;
                                        $amount_sc=$dbt->result($rsc,$isc-1,"AMOUNT");
                                        $reserv_amount_sc=$dbt->result($rsc,$isc-1,"RESERV_AMOUNT");
                                        $storage_cells_id_sc=$dbt->result($rsc,$isc-1,"STORAGE_CELLS_ID");

                                        if ($amount_sc>=$amount && $amount_sc>0){$isc=$nsc+1;$er=1;
                                            $amount_sc-=$amount;
                                            $reserv_amount_sc+=$amount;
                                            $r3=$db->query("select max(id) as mid from J_SELECT_STR;");$str_id=0+$db->result($r3,0,"mid")+1;
                                            $db->query("insert into J_SELECT_STR (`id`,`select_id`,`art_id`,`article_nr_displ`,`brand_id`,`amount`,`storage_id_from`,`cell_id_from`) values ('$str_id','$select_id','$art_id','$article_nr_displ','$brand_id','$amount','$storage_id_from','$storage_cells_id_sc');");
                                            $db->query("update J_DP_STR set cur_select_str_id='$str_id' where id='$id' and dp_id='$storage_dp_id' limit 1;");
                                            $dbt->query("update `T2_ARTICLES_STRORAGE_CELLS` set `AMOUNT`='$amount_sc', `RESERV_AMOUNT`='$reserv_amount_sc' where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_from' and `STORAGE_CELLS_ID`='$storage_cells_id_sc' limit 1;");
                                        }
                                        if ($amount_sc<$amount && $amount_sc>0 && $er==0){
                                            $amount-=$amount_sc;
                                            $reserv_amount_sc+=$amount_sc;
                                            $db->query("insert into J_SELECT_STR (`select_id`,`art_id`,`article_nr_displ`,`brand_id`,`amount`,`storage_id_from`,`cell_id_from`) values ('$select_id','$art_id','$article_nr_displ','$brand_id','$amount_sc','$storage_id_from','$storage_cells_id_sc');");
                                            $dbt->query("update `T2_ARTICLES_STRORAGE_CELLS` set `AMOUNT`='0', `RESERV_AMOUNT`='$reserv_amount_sc' where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_from' and `STORAGE_CELLS_ID`='$storage_cells_id_sc' limit 1;");
                                        }
                                    }
                                }
                            }
                            $db->query("update J_SELECT set `amount`='$s_amount', `articles_amount`='$s_articles_amount',`volume`='$s_volume', `weight_netto`='$s_weight_netto' where id='$select_id' limit 1;");
                        }
                        $this->updateDpSumm($storage_dp_id); $this->updateDpWeightVolume($storage_dp_id);
                    }
                    //if ($n11==1){
                        //$db->query("update J_DP set tpoint_id='$storage_tpoint_id' where id='$dp_id' limit 1;");
                        //$local=41;$i-=1;
                    //}
                }
            }
            if($storage_id_from==0){ $dp_cash_id=$this->getDpCashId($dp_id);
                $r1=$db->query("select * from J_DP_STR ds where ds.dp_id='$dp_id' and status_dps='93' and storage_id_from='$storage_id_from' and suppl_id>0;");$n1=$db->num_rows($r1);
                if ($n1>0){
                    for ($i1=1;$i1<=$n1;$i1++){
                        $id=$db->result($r1,$i1-1,"id");
                        $suppl_id=$db->result($r1,$i1-1,"suppl_id");
                        $suppl_storage_id=$db->result($r1,$i1-1,"suppl_storage_id");
                        $art_id=$db->result($r1,$i1-1,"art_id");
                        $article_nr_displ=$db->result($r1,$i1-1,"article_nr_displ");
                        $brand_id=$db->result($r1,$i1-1,"brand_id");$brand_name=$this->getBrandName($brand_id);
                        $amount=$db->result($r1,$i1-1,"amount");
                        //$price=$db->result($r1,$i1-1,"price");
                        $price_end=$db->result($r1,$i1-1,"price_end");
                        //$discount=$db->result($r1,$i1-1,"discount");
                        //$summ=$db->result($r1,$i1-1,"summ");
                        //$weight_netto=$db->result($r1,$i1-1,"weight_netto");
                        //$volume=$db->result($r1,$i1-1,"volume");
                        $suppl_art_id=$article_nr_displ; //temporary
                        $db->query("insert into J_DP_SUPPL_ORDER (`dp_id`,`dp_str_id`,`art_id`,`article_nr_displ`,`SUPPL_ART_ID`,`brand_id`,`brand_name`,`amount`,`price`,`cash_id`,`suppl_id`,`suppl_storage_id`,`tpoint_id`,`media_user_id`) values ('$dp_id','$id','$art_id','$article_nr_displ','$suppl_art_id','$brand_id','$brand_name','$amount','$price_end','$dp_cash_id','$suppl_id','$suppl_storage_id','$tpoint_id','$user_id');"); // обновляем статус записи что бы
                        $db->query("update J_DP_STR set status_dps='139' where id='$id' limit 1;"); // обновляем статус записи что бы понимать на каком этапе находится артикул;
                    }
                }
            }
        }
        $answer=1;$err="";
        return array($answer,$err);
    }

    function getTpointStorageLocal($tpoint_id){$db=DbSingleton::getDb();$storage_id=0;
        $r=$db->query("select storage_id from T_POINT_STORAGE where  tpoint_id='$tpoint_id' and `local`='41' and default='1' and status='1' limit 0,1");$n=$db->num_rows($r);
        if ($n==1){$storage_id=$db->result($r,0,"storage_id");}
        if ($n==0){
            $r=$db->query("select storage_id from T_POINT_STORAGE where  tpoint_id='$tpoint_id' and `local`='41' and status='1' limit 0,1");$n=$db->num_rows($r);
            if ($n==1){$storage_id=$db->result($r,0,"storage_id");}
        }
        return $storage_id;
    }

    function getStorageCellsData($storage_id){$db=DbSingleton::getDb();$cell_use=0;$cell_id=0;
        $r=$db->query("select id from STORAGE_CELLS where  storage_id='$storage_id' and `default`='1' and status='1' limit 0,1");$n=$db->num_rows($r);
        if ($n==1){$cell_use=1;$cell_id=$db->result($r,0,"id");}
        if ($n==0){
            $r1=$db->query("select id from STORAGE_CELLS where  storage_id='$storage_id' and `default`='0' and status='1' limit 0,1");$n1=$db->num_rows($r1);
            if ($n1==1){$cell_use=1;$cell_id=$db->result($r1,0,"id");}
        }
        return array($cell_use,$cell_id);
    }

    function loadDpJmoving($dp_id){$db=DbSingleton::getDb();$gmanual=new gmanual;$list="";
        $form="";$form_htm=RD."/tpl/dp_jmoving_list.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select j.*, s.name as storage_name, sc.storage_id, sc.`cell_value` 
        from J_MOVING j
            left outer join STORAGE s on s.id=j.storage_id_to
            left outer join STORAGE_CELLS sc on sc.id=j.cell_id_to
        where j.status=1 and j.parrent_type_id='1' and j.parrent_doc_id='$dp_id' order by j.status_jmoving asc, j.data desc, j.doc_nom desc, j.id desc;");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $type_id=$db->result($r,$i-1,"type_id");$type_name="<i class='fa fa-inbox'></i> Внутрішнє переміщення";if ($type_id==1){$type_name="<i class='fa fa-truck'></i> Між складами";}
            $prefix=$db->result($r,$i-1,"prefix");
            $doc_nom=$db->result($r,$i-1,"doc_nom");
            $storage_id_to=$db->result($r,$i-1,"storage_id_to");
            if ($storage_id_to==0){$storage_id_to=$db->result($r,$i-1,"storage_id");}
            $storage_name=$db->result($r,$i-1,"storage_name");
            if ($storage_name==""){$storage_name=$this->getStorageName($storage_id_to);}
            $cell_value=$db->result($r,$i-1,"cell_value");
            $data=$db->result($r,$i-1,"data");
            $user_name=$this->getMediaUserName($db->result($r,$i-1,"user_id"));
            $status_jmoving=$gmanual->get_gmanual_caption($db->result($r,$i-1,"status_jmoving"));
            $function="showJmovingCard(\"$id\")";
            if ($type_id==0){$function="showJmovingCardLocal(\"$id\")";}
            $list.="<tr style='cursor:pointer' onClick='$function'>
                <td>$type_name</td>
                <td>$prefix - $doc_nom</td>
                <td align='center'>$data</td>
                <td>$storage_name $cell_value</td>
                <td>$user_name</td>
                <td align='center'>
                    <button class='btn btn-xs btn-primary' onClick='viewDpJmoving(\"$dp_id\",\"$id\",\"$status_jmoving\");'><i class='fa fa-eye'></i></button>
                    <button class='btn btn-xs btn-primary' onClick='printStorselView(\"$id\");'><i class='fa fa-print'></i></button>
                </td>
                <td>$status_jmoving</td>
            </tr>";
        }
        if ($n==0){$list="<tr><td colspan=6>Віддалені переміщення відсутні</td></tr>";}
        $form=str_replace("{jmoving_list}",$list,$form);
        return $form;
    }

    function dpStorselCount($dp_id) {$db=DbSingleton::getDb();
        $r=$db->query("select sel.*, s.name as storage_name, t.name as tpoint_name from J_SELECT sel
            left outer join T_POINT t on t.id=sel.tpoint_id
            left outer join STORAGE s on s.id=sel.storage_id
        where sel.status=1 and sel.parrent_doc_type_id='2' and sel.parrent_doc_id='$dp_id' order by sel.status_select asc, sel.data_create desc, sel.id desc;");
        $n=$db->num_rows($r); if ($n==0) $n="";
        return $n;
    }

    function dpJmovingCount($dp_id) {$db=DbSingleton::getDb();
        $r=$db->query("select j.*, s.name as storage_name, sc.storage_id, sc.`cell_value` from J_MOVING j
            left outer join STORAGE s on s.id=j.storage_id_to
            left outer join STORAGE_CELLS sc on sc.id=j.cell_id_to
        where j.status=1 and j.parrent_type_id='1' and j.parrent_doc_id='$dp_id' order by j.status_jmoving asc, j.data desc, j.doc_nom desc, j.id desc;");
        $n=$db->num_rows($r); if ($n==0) $n="";
        return $n;
    }

    function loadDpStorsel($dp_id){$db=DbSingleton::getDb();$gmanual=new gmanual;$list="";$loc_type_name="";
        $form="";$form_htm=RD."/tpl/dp_storsel_list.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select sel.*, s.name as storage_name, t.name as tpoint_name from J_SELECT sel
            left outer join T_POINT t on t.id=sel.tpoint_id
            left outer join STORAGE s on s.id=sel.storage_id
        where sel.status=1 and sel.parrent_doc_type_id='2' and sel.parrent_doc_id='$dp_id' order by sel.status_select asc, sel.data_create desc, sel.id desc;"); $n=$db->num_rows($r);

        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $tpoint_name=$db->result($r,$i-1,"tpoint_name");
            $storage_name=$db->result($r,$i-1,"storage_name");
            $articles_amount=$db->result($r,$i-1,"articles_amount");
            $amount=$db->result($r,$i-1,"amount");
            $volume=$db->result($r,$i-1,"volume");
            $weight_netto=$db->result($r,$i-1,"weight_netto");
            $weight_brutto=$db->result($r,$i-1,"weight_brutto");
            $status_select=$db->result($r,$i-1,"status_select");
            $status_select_cap=$gmanual->get_gmanual_caption($status_select);

            $list.="<tr id='strStsRow_$i'>
                <td>$i</td>
                <td style='min-width:140px;'>СкВ-$id</td>
                <td style='min-width:140px;'>$tpoint_name</td>
                <td style='min-width:120px;'>$storage_name</td>
                <td style='min-width:80px;'>$loc_type_name</td>
                <td align='center' style='min-width:80px;'>$articles_amount</td>
                <td align='center' style='min-width:80px;'>$amount</td>
                <td align='right'>$volume</td>
                <td align='right'>$weight_netto</td>
                <td align='right'>$weight_brutto</td>
                <td align='center'>
                    <button class='btn btn-xs btn-primary' onClick='viewDpStorageSelect(\"$dp_id\",\"$id\",\"$status_select\");'><i class='fa fa-eye'></i></button>
                    <button class='btn btn-xs btn-primary' onClick='printStorselView(\"$id\");'><i class='fa fa-print'></i></button>
                </td>
                <td align='center'>$status_select_cap</td>
            </tr>";
        }
        if ($n==0){$list="<tr><td colspan=6>Локальні відбори відсутні</td></tr>";}
        $form=str_replace("{storsel_list}",$list,$form);
        return $form;
    }

    function viewDpStorageSelect($dp_id,$select_id,$select_status){$db=DbSingleton::getDb();$storsel=new storsel;$gmanual=new gmanual;$list="";
        $form="";$form_htm=RD."/tpl/dp_storage_select_view.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select * from J_SELECT_STR where select_id='$select_id' order by id asc;");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $art_id=$db->result($r,$i-1,"art_id");
            $article_nr_displ=$db->result($r,$i-1,"article_nr_displ");
            $brand_id=$db->result($r,$i-1,"brand_id");$brand_name=$this->getBrandName($brand_id);
            $amount=$db->result($r,$i-1,"amount");
            $storage_id_from=$db->result($r,$i-1,"storage_id_from");
            $storage_name_from=$this->getStorageName($storage_id_from);
            $amount_barcodes=$db->result($r,$i-1,"amount_barcodes");
            $amount_barcodes_noscan=$db->result($r,$i-1,"amount_barcodes_noscan"); $amount_accept=$amount_barcodes+$amount_barcodes_noscan;
            $select_bug_list=$this->getStorageSelectBugList($dp_id,$select_id,$art_id,$id);
            $amount_bug=$db->result($r,$i-1,"amount_bug");
            $list.="<tr align='right'>
                <td align='left'>$i</td>
                <td align='left'>$article_nr_displ</td>
                <td align='center'>$brand_name</td>
                <td>$storage_name_from</td>
                <td>$amount</td>
                <td>$amount_accept</td>
                <td>$amount_bug</td>
                <td>$select_bug_list</td>
            </tr>";
        }
        list($select_nom,$data_create,$data_start,$data_collect,$storage_id,$storage_name,$articles_amount,$amount,$volume,$weight_netto,$weight_brutto)=$storsel->getStorselInfo($select_id);
        $form=str_replace("{select_id}",$select_id,$form);
        $form=str_replace("{data_create}",$data_create,$form);
        $form=str_replace("{data_start}",$data_start,$form);
        $form=str_replace("{data_collect}",$data_collect,$form);
        $form=str_replace("{volume}",$volume,$form);
        $form=str_replace("{weight_netto}",$weight_netto,$form);
        $form=str_replace("{weight_brutto}",$weight_brutto,$form);
        $form=str_replace("{ArticlesList}",$list,$form);
        return array($form,"Структура складського відбору № СкВ-$select_id; Статус відбору: ".$gmanual->get_gmanual_caption($select_status));
    }

    function getStorageSelectBugList($dp_id,$select_id,$art_id,$str_id){$db=DbSingleton::getDb();$manual=new manual;$list="";
        $r=$db->query("select * from J_SELECT_STR_BUG where select_id='$select_id' and art_id='$art_id' and str_id='$str_id' order by id asc;");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $storage_select_bug=$db->result($r,$i-1,"storage_select_bug");
            $amount_bug=$db->result($r,$i-1,"amount_bug");
            $storage_select_bug_name=$manual->getManualMCaption("storage_select_bug",$storage_select_bug);
            $list.="$amount_bug"."шт. - $storage_select_bug_name";if ($i<$n){$list.="<br>";}
        }
        return $list;
    }

    function loadDpSaleInvoice($dp_id){$db=DbSingleton::getDb();$list="";
        $form="";$form_htm=RD."/tpl/dp_sale_invoice_list.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select sv.*, t.name as tpoint_name, sl.name as seller_name, cl.name as client_name, dt.mvalue as doc_type_name, ch.abr2 as cash_abr 
        from J_SALE_INVOICE sv
            left outer join CASH ch on ch.id=sv.cash_id
            left outer join T_POINT t on t.id=sv.tpoint_id
            left outer join A_CLIENTS sl on sl.id=sv.seller_id
            left outer join A_CLIENTS cl on cl.id=sv.client_conto_id
            left outer join manual dt on dt.key='client_sale_type' and dt.id=sv.doc_type_id
        where sv.status=1 and sv.dp_id='$dp_id' order by sv.status_invoice asc, sv.data_create asc, sv.id asc;");$n=$db->num_rows($r);

        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $prefix=$db->result($r,$i-1,"prefix");
            $doc_nom=$db->result($r,$i-1,"doc_nom");
            $data_create=$db->result($r,$i-1,"data_create");
            $tpoint_name=$db->result($r,$i-1,"tpoint_name");
            $seller_name=$db->result($r,$i-1,"seller_name");
            $client_name=$db->result($r,$i-1,"client_name");
            $doc_type_name=$db->result($r,$i-1,"doc_type_name");
            $summ=$db->result($r,$i-1,"summ");
            $summ_debit=$db->result($r,$i-1,"summ_debit");
            $cash_abr=$db->result($r,$i-1,"cash_abr");
            $data_pay=$db->result($r,$i-1,"data_pay");

            $list.="<tr id='strStsRow_$i' align='center'>
                <td>$i</td>
                <td>$prefix-$doc_nom</td>
                <td align='center'>$data_create</td>
                <td>$tpoint_name</td>
                <td align='left'>$seller_name</td>
                <td align='left'>$client_name</td>
                <td>$doc_type_name</td>
                <td align='center' style='min-width:80px;'>$summ$cash_abr</td>
                <td align='right'>$summ_debit$cash_abr</td>
                <td align='right'>$data_pay</td>
                <td align='center'>
                    <button class='btn btn-xs btn-primary' onClick='viewDpSaleInvoice(\"$dp_id\",\"$id\");'><i class='fa fa-eye'></i></button>
                    <button class='btn btn-xs btn-warning' onClick='openSaleInvoice(\"$id\");'><i class='fa fa-external-link-square'></i></button>
                </td>
            </tr>";
        }
        if ($n==0){$list="<tr><td colspan=11 align='center'>Накладні відсутні</td></tr>";}
        $form=str_replace("{sale_invoice_list}",$list,$form);
        return $form;
    }

    function showDpStorselForSaleInvoice($dp_id){$db=DbSingleton::getDb();$gmanual=new gmanual;$list="";$loc_type_name="";
        $form="";$form_htm=RD."/tpl/dp_storsel_list_for_sale_invoice.htm";	if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}

        $r=$db->query("select select_id from J_SALE_INVOICE_STORSEL where dp_id='$dp_id';");$n=$db->num_rows($r);$sel_use="0";
        for ($i=1;$i<=$n;$i++){	$sel_use.=",".$db->result($r,$i-1,"select_id");	}

        $r=$db->query("select sel.*, s.name as storage_name, t.name as tpoint_name from J_SELECT sel
            left outer join T_POINT t on t.id=sel.tpoint_id
            left outer join STORAGE s on s.id=sel.storage_id
        where sel.status=1 and sel.parrent_doc_type_id='2' and sel.parrent_doc_id='$dp_id' and status_select='85' and sel.id not in ($sel_use) order by sel.status_select asc, sel.data_create desc, sel.id desc;");$n=$db->num_rows($r);

        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            //$data_create=$db->result($r,$i-1,"data_create");
            //$tpoint_id=$db->result($r,$i-1,"tpoint_id");
            $tpoint_name=$db->result($r,$i-1,"tpoint_name");
            //$storage_id=$db->result($r,$i-1,"storage_id");
            $storage_name=$db->result($r,$i-1,"storage_name");
            $articles_amount=$db->result($r,$i-1,"articles_amount");
            $amount=$db->result($r,$i-1,"amount");
            $volume=$db->result($r,$i-1,"volume");
            $weight_netto=$db->result($r,$i-1,"weight_netto");
            $weight_brutto=$db->result($r,$i-1,"weight_brutto");
            //$user_name=$this->getMediaUserName($db->result($r,$i-1,"user_id"));
            $status_select=$db->result($r,$i-1,"status_select");
            $status_select_cap=$gmanual->get_gmanual_caption($status_select);
            $list.="<tr id='strStsRow_$i'>
                <td><input type='checkbox' class='ch_dp_sts' id='dp_strosel_$i' value='$id' checked></td>
                <td>$i</td>
                <td style='min-width:140px;'>СкВ-$id</td>
                <td style='min-width:140px;'>$tpoint_name</td>
                <td style='min-width:120px;'>$storage_name</td>
                <td style='min-width:80px;'>$loc_type_name</td>
                <td align='center' style='min-width:80px;'>$articles_amount</td>
                <td align='center' style='min-width:80px;'>$amount</td>
                <td align='right'>$volume</td>
                <td align='right'>$weight_netto</td>
                <td align='right'>$weight_brutto</td>
                <td align='center'><button class='btn btn-xs btn-primary' onClick='viewDpStorageSelect(\"$dp_id\",\"$id\",\"$status_select\");'><i class='fa fa-eye'></i></button></td>
                <td align='center'>$status_select_cap</td>
            </tr>";
        }
        if ($n==0){$list="<tr><td colspan=13 align=center>Відбори відсутні</td></tr>";}
        $form=str_replace("{storsel_list}",$list,$form);
        $form=str_replace("{dp_id}",$dp_id,$form);
        $r=$db->query("select sel.*, s.name as storage_name, t.name as tpoint_name from J_SELECT sel
            left outer join T_POINT t on t.id=sel.tpoint_id
            left outer join STORAGE s on s.id=sel.storage_id
        where sel.status=1 and sel.parrent_doc_type_id='2' and sel.parrent_doc_id='$dp_id' and sel.id not in ($sel_use);");$kol_storsel=$db->num_rows($r);
        $form=str_replace("{kol_storsel}",$kol_storsel,$form);
        return $form;
    }

    function checkClientSaleInvoiceDataPayLimit($client_id){$db=DbSingleton::getDb(); $doc_ex=0;
        $r=$db->query("select count(`id`) as `kol` from `J_SALE_INVOICE` where `client_conto_id`='$client_id' and `status_invoice`='86' and `data_pay` < CURDATE() and summ_debit>0;");$kol=$db->result($r,0,"kol");
        if ($kol>0){$doc_ex=1;}
        return $doc_ex;
    }

    function checkClientCreditLimitBeforeSaleInvoice($dp_id,$kol_storsel,$ar_storsel){$db=DbSingleton::getDb();$client_saldo=0;$credit_limit=0;$summ_all=0;$datapay_limit=0;
        $r=$db->query("select * from J_DP where id='$dp_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            //$doc_type_id=$db->result($r,0,"doc_type_id");
            //$tpoint_id=$db->result($r,0,"tpoint_id");
            //$client_id=$db->result($r,0,"client_id");
            $client_conto_id=$db->result($r,0,"client_conto_id");
            $cash_id=$db->result($r,0,"cash_id");
            $usd_to_uah=$db->result($r,0,"usd_to_uah");
            $eur_to_uah=$db->result($r,0,"eur_to_uah");
            //$summ=$db->result($r,0,"summ");
            //$vat_use=$db->result($r,0,"vat_use");
            $status_dp=$db->result($r,0,"status_dp");

            list($usd_to_uah_new,$eur_to_uah_new)=$this->getKoursData();

            if ($status_dp==79) {
                $usd_to_uah=$usd_to_uah_new;
                $eur_to_uah=$eur_to_uah_new;
            } else {
                if($usd_to_uah==0){$usd_to_uah=$usd_to_uah_new;}
                if($eur_to_uah==0){$eur_to_uah=$eur_to_uah_new;}
            }

            list($a1,$a1,$data_pay)=$this->getClientPaymentDelay($client_conto_id);

            $r=$db->query("select max(id) as mid from J_SALE_INVOICE;");$invoice_id=0+$db->result($r,0,"mid")+1;
            $ai=0;$select_str="0";
            for ($i=1;$i<=$kol_storsel;$i++){
                if ($ar_storsel[$i]!="" && $ar_storsel[$i]>0){$ai+=1;
                    $select_str.=",".$ar_storsel[$i];
                }
            }

            $r2=$db->query("select jss.amount_collect, jds.*, jss.storage_id_from as storage_id_from2, jss.cell_id_from as cell_id_from2
            from J_SELECT_STR jss 
                left outer join J_SELECT js on js.id=jss.select_id 
                left outer join J_DP jd on (jd.id=js.parrent_doc_id and js.parrent_doc_type_id=2)
                left outer join J_DP_STR jds on jds.dp_id=jd.id 
            where jss.select_id in ($select_str) and jds.art_id=jss.art_id GROUP BY jds.art_id, storage_id_from2, cell_id_from2;");$n2=$db->num_rows($r2);$summ_all=0;

            for ($i2=1;$i2<=$n2;$i2++){
                $amount2=$db->result($r2,$i2-1,"amount_collect");
                $price_end2=$db->result($r2,$i2-1,"price_end");
                $summ2=$amount2*$price_end2;

                $summ_all+=$summ2;
            }
            if ($cash_id==1){$summ_all=round($summ_all*$usd_to_uah,2);}
            if ($cash_id==3){$summ_all=round($summ_all*$usd_to_uah/$eur_to_uah,2);}

            list($client_saldo,$client_saldo_cash_id)=$this->getClientGeneralSaldo($client_conto_id);
            $credit_limit=$this->getClientCreditLimit($client_conto_id);
            $datapay_limit=$this->checkClientSaleInvoiceDataPayLimit($client_conto_id);
        }
        return array($client_saldo,$credit_limit,$summ_all,$datapay_limit);
    }

    function getClientCreditLimit($client_id){$db=DbSingleton::getDb();$credit_limit=0;
        $r=$db->query("select credit_limit from A_CLIENTS_CONDITIONS where client_id='$client_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $credit_limit=$db->result($r,0,"credit_limit");
        }
        return $credit_limit;
    }

    function viewDpDatapayLimitSaleInvoice($dp_id){$db=DbSingleton::getDb();$list="";
        $form="";$form_htm=RD."/tpl/dp_sale_invoice_list_data_pay.htm";	if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $client_id=$this->getDpClient($dp_id);
//        $r=$db->query("select * from `J_SALE_INVOICE` where `client_conto_id`='$client_id' and `status_invoice`='86' and `data_pay` < CURDATE();");
//        $n=$db->num_rows($r);$sel_use="0";

        $r=$db->query("select sv.*, dp.prefix as dp_prefix, dp.doc_nom as dp_nom, t.name as tpoint_name, sl.name as seller_name, cl.name as client_name, dt.mvalue as doc_type_name,ch.abr2 as cash_abr from J_SALE_INVOICE sv
            left outer join J_DP dp on dp.id=sv.dp_id
            left outer join CASH ch on ch.id=sv.cash_id
            left outer join T_POINT t on t.id=sv.tpoint_id
            left outer join A_CLIENTS sl on sl.id=sv.seller_id
            left outer join A_CLIENTS cl on cl.id=sv.client_conto_id
            left outer join manual dt on dt.key='client_sale_type' and dt.id=sv.doc_type_id
        where sv.status=1 and sv.`client_conto_id`='$client_id' and sv.`status_invoice`='86' and sv.`data_pay` < CURDATE() order by sv.data_pay asc, sv.data_create asc;");
        $n=$db->num_rows($r); $client_name="";

        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $dp_nom=$db->result($r,$i-1,"dp_prefix").$db->result($r,$i-1,"dp_nom");
            $prefix=$db->result($r,$i-1,"prefix");
            $doc_nom=$db->result($r,$i-1,"doc_nom");
            $data_create=$db->result($r,$i-1,"data_create");
            $tpoint_name=$db->result($r,$i-1,"tpoint_name");
            $seller_name=$db->result($r,$i-1,"seller_name");
            $client_name=$db->result($r,$i-1,"client_name");
            $doc_type_name=$db->result($r,$i-1,"doc_type_name");
            $summ=$db->result($r,$i-1,"summ");
            $cash_abr=$db->result($r,$i-1,"cash_abr");
            $data_pay=$db->result($r,$i-1,"data_pay");
            $list.="<tr id='strStsRow_$i' style='cursor:pointer' align='center' onClick='showSaleInvoiceCard(\"$id\");'>
                <td>$i</td>
                <td>$prefix-$doc_nom</td>
                <td align='center'>$data_create</td>
                <td>$dp_nom</td>
                <td>$tpoint_name</td>
                <td align='left'>$seller_name</td>
                <td align='left'>$client_name</td>
                <td>$doc_type_name</td>
                <td align='center' style='min-width:80px;'>$summ$cash_abr</td>
                <td align='right'>$data_pay</td>
            </tr>";
        }
        if ($n==0){$list="<tr><td colspan=13 align=center>Накладні відсутні</td></tr>";}
        $form=str_replace("{list}",$list,$form);
        return array($form,"Список протермінованих накладних: $client_name");
    }

    function sendDpStorselToSaleInvoice($dp_id,$kol_storsel,$ar_storsel){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();
        $slave=new slave;$answer=0;$err="Помилка індексу!";$sale_invoice_nom=0; session_start();$user_id=$_SESSION["media_user_id"];$cat=new catalogue; $dp_id=$slave->qq($dp_id);
        if ($dp_id>0){
            $kol_storsel=$slave->qq($kol_storsel);
            if ($kol_storsel>0){
                list($client_saldo,$client_credit_limit,$sale_storsell_summ,$datapay_limit)=$this->checkClientCreditLimitBeforeSaleInvoice($dp_id,$kol_storsel,$ar_storsel);

                if ($sale_storsell_summ<=0){
                    $db->query("update J_DP set status_dp='81' where id='$dp_id';");
                    $answer=1;$err="Всі товари у ДП відхилено. Накладну не створено. Документ передано у архів.";
                }

                if ($datapay_limit>0){
                    $answer=2;$err="Поточне відвантаження НЕ можливе! У контрагента наявні просрочені документи. Відобразити список просрочених документів?";
                }

                if ($datapay_limit==0 && $sale_storsell_summ>0){

                if ($client_saldo==0 || ($client_saldo>0 && $sale_storsell_summ<$client_saldo+$client_credit_limit) || ($client_saldo<0 && $sale_storsell_summ<$client_credit_limit-abs($client_saldo))) { //////CHECK NOW

                $r=$db->query("select doc_type_id from J_DP where id='$dp_id' limit 1;");
                $doc_type_id_check=$db->result($r,0,"doc_type_id");
                if ($doc_type_id_check>0) {

                    $r=$db->query("select max(id) as mid from J_SALE_INVOICE;");$invoice_id=0+$db->result($r,0,"mid")+1; $sale_invoice_nom=$invoice_id; //Временно
                    $db->query("insert into J_SALE_INVOICE (`id`,`dp_id`) values ('$invoice_id','$dp_id');");$ai=0;
                    for ($i=1;$i<=$kol_storsel;$i++){
                        if ($ar_storsel[$i]!="" && $ar_storsel[$i]>0){$ai+=1;
                            $db->query("insert into J_SALE_INVOICE_STORSEL (`dp_id`,`invoice_id`,`select_id`) values ('$dp_id','$invoice_id','".$ar_storsel[$i]."');");
                        }
                    }

                    $r=$db->query("select * from J_DP where id='$dp_id' limit 0,1;");$n=$db->num_rows($r);
                    if ($n==1){
                        $doc_type_id=$db->result($r,0,"doc_type_id");
                        $tpoint_id=$db->result($r,0,"tpoint_id");
                        $client_id=$db->result($r,0,"client_id");
                        $client_conto_id=$db->result($r,0,"client_conto_id");
                        $cash_id=$db->result($r,0,"cash_id");
                        $usd_to_uah=$db->result($r,0,"usd_to_uah");
                        $eur_to_uah=$db->result($r,0,"eur_to_uah");
                        $summ=$db->result($r,0,"summ");
                        $vat_use=$db->result($r,0,"vat_use");
                        $delivery_type_id=$db->result($r,0,"delivery_type_id");
                        $carrier_id=$db->result($r,0,"carrier_id");
                        $delivery_address=$db->result($r,0,"delivery_address");
                        $status_dp=$db->result($r,0,"status_dp");

                        list($usd_to_uah_new,$eur_to_uah_new)=$this->getKoursData();

                        if ($status_dp==79) {
                            $usd_to_uah=$usd_to_uah_new;
                            $eur_to_uah=$eur_to_uah_new;
                        } else {
                            if($usd_to_uah==0){$usd_to_uah=$usd_to_uah_new;}
                            if($eur_to_uah==0){$eur_to_uah=$eur_to_uah_new;}
                        }

                        $seller_id=$this->getSellerId($tpoint_id,$doc_type_id);
                        list($seller_prefix,$seller_doc_nom)=$this->getSellerPrefixDocNom($seller_id,$doc_type_id);
                        list($a1,$a1,$data_pay)=$this->getClientPaymentDelay($client_conto_id);
                        $data_create=date("Y-m-d");

                        $db->query("update J_SALE_INVOICE set `prefix`='$seller_prefix', `doc_nom`='$seller_doc_nom', `tpoint_id`='$tpoint_id', `seller_id`='$seller_id', `client_id`='$client_id', `client_conto_id`='$client_conto_id', `doc_type_id`='$doc_type_id', `data_create`='$data_create', `data_create`='$data_create', `data_pay`='$data_pay', `cash_id`='$cash_id', `usd_to_uah`='$usd_to_uah', `eur_to_uah`='$eur_to_uah',  `vat_use`='$vat_use', `delivery_type_id`='$delivery_type_id', `carrier_id`='$carrier_id', `delivery_address`='$delivery_address', `user_id`='$user_id' where id='$invoice_id' limit 1;");

                        $r3=$db->query("select * from J_SALE_INVOICE_STORSEL sis where sis.invoice_id='$invoice_id' and sis.dp_id='$dp_id' and sis.status='1' order by id asc;"); $n3=$db->num_rows($r3);$select_str="0";
                        for ($i3=1;$i3<=$n3;$i3++){
                            $select_str.=",".$db->result($r3,$i3-1,"select_id");
                        }
                        /* TAX INVOICE */
                        $tax_id=0;
                        if ($doc_type_id==61){
                            $rt=$db->query("select max(id) as mid from J_TAX_INVOICE;");$tax_id=0+$db->result($rt,0,"mid")+1;
                            $rt=$db->query("select max(doc_nom) as mid from J_TAX_INVOICE where seller_id='$seller_id';");$tax_nom=0+$db->result($rt,0,"mid")+1;
                            $db->query("insert into J_TAX_INVOICE (`id`,`tax_type_id`,`doc_nom`,`data_create`,`sale_invoice_id`,`tpoint_id`,`seller_id`,`client_id`,`cash_id`,`summ`,`user_id`) values ('$tax_id','160','$tax_nom',CURDATE(),'$invoice_id','$tpoint_id','$seller_id','$client_conto_id','$cash_id','$summ','$user_id');");
                        }

                        $r2=$db->query("select jss.amount_collect, jds.*, jss.storage_id_from as storage_id_from2, jss.cell_id_from as cell_id_from2
                        from J_SELECT_STR jss 
                            left outer join J_SELECT js on js.id=jss.select_id 
                            left outer join J_DP jd on (jd.id=js.parrent_doc_id and js.parrent_doc_type_id=2)
                            left outer join J_DP_STR jds on jds.dp_id=jd.id 
                        where jss.select_id in ($select_str) and jds.art_id=jss.art_id and jds.status_dps!=97 GROUP BY jds.art_id, storage_id_from2, cell_id_from2;");
                        $n2=$db->num_rows($r2);$summ_all=0;

                        $db->query("update J_SELECT set status_select='127' where id in ($select_str);");

                        for ($i2=1;$i2<=$n2;$i2++){
                            $art_id2=$db->result($r2,$i2-1,"art_id");
                            $article_nr_displ2=$db->result($r2,$i2-1,"article_nr_displ");
                            $brand_id2=$db->result($r2,$i2-1,"brand_id");
                            $amount2=$db->result($r2,$i2-1,"amount_collect");
                            $price2=$db->result($r2,$i2-1,"price");
                            $price_end2=$db->result($r2,$i2-1,"price_end");
                            $discount2=$db->result($r2,$i2-1,"discount");
                            //$summ2=$amount2*$price_end2;
                            $storage_id_from2=$db->result($r2,$i2-1,"storage_id_from2");
                            $cell_id_from2=$db->result($r2,$i2-1,"cell_id_from2");
                            $dp_str_id=$db->result($r2,$i2-1,"jds.id");

                            $db->query("update J_DP_STR set status_dps='97' where id='$dp_str_id' limit 1;");
                            if ($amount2>0){
                                if ($cash_id==1){
                                    $price2_cash=round($price2*$usd_to_uah,2);
                                    $price_end2_cash=round($price_end2*$usd_to_uah,2);
                                    $summ2_cash=round($price_end2_cash*$amount2,2);
                                    //$discount2_cash=round($discount2*$usd_to_uah,2);
                                    $discount2_cash=$discount2;
                                }
                                if ($cash_id==2){
                                    $price2_cash=$price2;
                                    $price_end2_cash=$price_end2;
                                    $summ2_cash=round($price_end2_cash*$amount2,2);
                                    $discount2_cash=$discount2;
                                    //$discount2_cash=$discount2;
                                }
                                if ($cash_id==3){
                                    $price2_cash=round($price2*$usd_to_uah/$eur_to_uah,2);
                                    $price_end2_cash=round($price_end2*$usd_to_uah/$eur_to_uah,2);
                                    $summ2_cash=round($price_end2_cash*$amount2,2);
                                    //$discount2_cash=round($discount2*$usd_to_uah/$eur_to_uah,2);
                                    $discount2_cash=$discount2;
                                }
                                $rsi=$db->query("select max(id) as mid from J_SALE_INVOICE_STR;");$invoice_str_id=0+$db->result($rsi,0,"mid")+1;
                                $db->query("insert into J_SALE_INVOICE_STR (`id`,`invoice_id`,`art_id`,`article_nr_displ`,`brand_id`,`amount`,`price`,`price_end`,`discount`,`summ`,`storage_id_from`,`cell_id_from`) values ('$invoice_str_id','$invoice_id','$art_id2','$article_nr_displ2','$brand_id2','$amount2','$price2_cash','$price_end2_cash','$discount2_cash','$summ2_cash','$storage_id_from2','$cell_id_from2');");

                                if ($tax_id>0){
                                    $zed=$cat->getArticleZED($art_id2);$art_name=$cat->getArticleNameLang($art_id2);
                                    $db->query("insert into J_TAX_INVOICE_STR (`tax_id`,`zed`,`art_id`,`goods_name`,`amount`,`price`,`summ`) values ('$tax_id','$zed','$art_id2','$art_name','$amount2','$price_end2_cash','$summ2_cash');");
                                }

                                $slave->addJuornalArtDocs(3,$invoice_id,$art_id2,$amount2);

                                $this->writeOffPartitions($invoice_id,$invoice_str_id,$art_id2,$article_nr_displ2,$brand_id2,$amount2,$price_end2);

                                $rr=$dbt->query("select * from T2_ARTICLES_STRORAGE where ART_ID='$art_id2' and STORAGE_ID ='$storage_id_from2' limit 0,1;");$nr=$dbt->num_rows($rr);
                                if ($nr==1){
                                    $rr_reserv=$dbt->result($rr,0,"RESERV_AMOUNT");  $rr_reserv-=$amount2;
                                    $dbt->query("update T2_ARTICLES_STRORAGE set `RESERV_AMOUNT`='$rr_reserv' where ART_ID='$art_id2' and STORAGE_ID ='$storage_id_from2';");
                                }

                                $rr=$dbt->query("select * from T2_ARTICLES_STRORAGE_CELLS where ART_ID='$art_id2' and STORAGE_ID ='$storage_id_from2' and STORAGE_CELLS_ID='$cell_id_from2' limit 0,1;");$nr=$dbt->num_rows($rr);
                                if ($nr==1){
                                    $rr_reserv=$dbt->result($rr,0,"RESERV_AMOUNT");  	$rr_reserv-=$amount2;
                                    $dbt->query("update T2_ARTICLES_STRORAGE_CELLS set `RESERV_AMOUNT`='$rr_reserv' where ART_ID='$art_id2' and STORAGE_ID ='$storage_id_from2' and STORAGE_CELLS_ID='$cell_id_from2';");
                                }
                                $dbt->query("update T2_ARTICLES_PRICE_STOCK set `GENERAL_STOCK`=(`GENERAL_STOCK`-'$amount2') where ART_ID='$art_id2' limit 1;");

                                $summ_all+=$summ2_cash;
                            }
                        }
                        if ($ai==$kol_storsel){
                            $ra=$db->query("select count(id) as kol from J_DP_STR where dp_id='$dp_id' and status_dps!='97';");
                            $ra_kol=$db->result($ra,0,"kol");
                            if ($ra_kol==0){ $db->query("update J_DP set `status_dp`='81' where id='$dp_id' limit 1;"); }
                            //$db->query("update J_DP_STR set status_dps='97' where dp_id='$dp_id';");
                        }
                        //if ($cash_id==1){$summ_all=round($summ_all*$usd_to_uah,2);}
                        //if ($cash_id==3){$summ_all=round($summ_all*$usd_to_uah/$eur_to_uah,2);}
                        $db->query("update J_SALE_INVOICE set `summ`='$summ_all', `summ_debit`='$summ_all' where id='$invoice_id' limit 1;");

                        list($balans_before,$balans_before_cash_id)=$this->getClientGeneralSaldo($client_conto_id);
                        $balans_after=$balans_before-$summ_all;
                        $db->query("insert into B_CLIENT_BALANS_JOURNAL (`client_id`,`cash_id`,`balans_before`,`deb_kre`,`summ`,`balans_after`,`doc_type_id`,`doc_id`) values ('$client_conto_id','$cash_id','$balans_before','1','$summ_all','$balans_after','1','$invoice_id');");

                        $this->updateClientBalans($client_conto_id,$cash_id,$summ_all);
                        $answer=1;$err="";
                    }
                } else { $answer=0;$err="Виберіть тип документу спочатку"; }
                }
                else {
                    list($client_cash_id,$c)=$this->getClientCashConditions($this->getDpClient($dp_id));
                    $cash_name=$this->getCashAbr($client_cash_id);
                    //$cl_saldo=abs($client_saldo)-$client_credit_limit;
                    $cl_pp=$sale_storsell_summ+abs($client_saldo)-$client_credit_limit;
                    $answer=0;$err="Ліміт кредиту: $client_credit_limit$cash_name; борг/баланс: ".abs($client_saldo)."$cash_name. Відвантаження на сумму: $sale_storsell_summ$cash_name НЕ можливе, для поточного відвантаження внесіть в касу як мінімум $cl_pp$cash_name, або проведіть не оплачені документи";
                }
                }
            }
        }
        $rsel=$db->query("select prefix,doc_nom from J_SALE_INVOICE where id='$sale_invoice_nom' limit 0,1;"); $nsel=$db->num_rows($rsel); $sale_invoice_prefix=0;
        if ($nsel>0) {
            $prefix=$db->result($rsel,0,"prefix");
            $doc_nom=$db->result($rsel,0,"doc_nom");
            $sale_invoice_prefix="$prefix-$doc_nom";
        }
        return array($answer,$err,$sale_invoice_nom,$sale_invoice_prefix);
    }

    function writeOffPartitions($invoice_id,$invoice_str_id,$art_id,$article_nr_displ,$brand_id,$amount_invoice,$price_invoice){$db=DbSingleton::getDb();$cat=new catalogue;
        $r=$db->query("select * from T2_ARTICLES_PARTITIONS where art_id='$art_id' and rest>0 and status='1' order by id asc;");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            //$parrent_type_id=$db->result($r,$i-1,"parrent_type_id");
            //$parrent_doc_id=$db->result($r,$i-1,"parrent_doc_id");
            $rest=$db->result($r,$i-1,"rest");
            $price=$db->result($r,$i-1,"price");
            list($oper_price,$g)=$cat->getArticleOperPriceGeneralStock($art_id);
            $price_buh_uah=$db->result($r,$i-1,"price_buh_uah");
            $price_man_uah=$db->result($r,$i-1,"price_man_uah");
            if ($amount_invoice<=$rest){
                $new_rest=$rest-$amount_invoice;
                $db->query("update T2_ARTICLES_PARTITIONS set rest='$new_rest' where id='$id' limit 1;");
                $db->query("insert into J_SALE_INVOICE_PARTITION_STR (`partition_id`,`invoice_id`,`invoice_str_id`,`art_id`,`article_nr_displ`,`brand_id`,`partition_amount`,`invoice_amount`,`oper_price_partition`,`price_partition`,`price_buh_uah`,`price_man_uah`,`price_invoice`) values ('$id','$invoice_id','$invoice_str_id','$art_id','$article_nr_displ','$brand_id','$amount_invoice','$amount_invoice','$oper_price','$price','$price_buh_uah','$price_man_uah','$price_invoice');");
                $i=$n+1;
            }
            if ($amount_invoice>$rest){
                $new_rest=0;
                $amount_invoice=$amount_invoice-$rest;
                $db->query("update T2_ARTICLES_PARTITIONS set rest='$new_rest' where id='$id' limit 1;");
                $db->query("insert into J_SALE_INVOICE_PARTITION_STR (`partition_id`,`invoice_id`,`invoice_str_id`,`art_id`,`article_nr_displ`,`brand_id`,`partition_amount`,`invoice_amount`,`oper_price_partition`,`price_partition`,`price_buh_uah`,`price_man_uah`,`price_invoice`) values ('$id','$invoice_id','$invoice_str_id','$art_id','$article_nr_displ','$brand_id','$rest','$rest','$oper_price','$price','$price_buh_uah','$price_man_uah','$price_invoice');");
            }
        }
        return;
    }

    function getClientGeneralSaldo($sel_id){$db=DbSingleton::getDb();$saldo="0";$cash_id=1;
        $r=$db->query("select `saldo`,cash_id from B_CLIENT_BALANS where client_id='$sel_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $saldo=$db->result($r,0,"saldo");
            $cash_id=$db->result($r,0,"cash_id");
        }
        return array($saldo,$cash_id);
    }

    function getSellerId($tpoint_id,$doc_type_id){$db=DbSingleton::getDb();$seller_id=0;
        //$sale_type=array(61=>86, 62=>87, 63=>87, 64=>88);
        $r=$db->query("select `client_id` from T_POINT_CLIENTS where tpoint_id='$tpoint_id' and sale_type='$doc_type_id' and in_use='1' and status='1' order by id asc limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$seller_id=$db->result($r,0,"client_id");}
        return $seller_id;
    }

    function getSellerPrefixDocNom($seller_id,$doc_type_id){$db=DbSingleton::getDb();$prefix="";
        $sale_type=array(61=>86, 62=>87, 63=>87, 64=>88);$sale_type_id=$sale_type[$doc_type_id];
        $r=$db->query("select `prefix` from A_CLIENTS_DOCUMENT_PREFIX where client_id='$seller_id' and doc_type_id='$sale_type_id' and status='1' order by id asc limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$prefix=$db->result($r,0,"prefix");}
        $prefix=str_replace("{year}",date("Y"),$prefix);
        $prefix=str_replace("{month}",date("m"),$prefix);
        $prefix=str_replace("{day}",date("d"),$prefix);
        $prefix=str_replace("{rnd010}",rand(0,10),$prefix);
        $r=$db->query("select IFNULL( max( doc_nom ) , 0 ) AS doc_nom from J_SALE_INVOICE where seller_id='$seller_id' and doc_type_id='$doc_type_id' and status='1';");$doc_nom=0+$db->result($r,0,"doc_nom")+1;
        return array($prefix,$doc_nom);
    }

    function updateClientBalans($client_conto_id,$cash_id,$summ){$db=DbSingleton::getDb();
        $r=$db->query("select * from B_CLIENT_BALANS where client_id='$client_conto_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==0){
            $db->query("insert into B_CLIENT_BALANS (`client_id`,`cash_id`) values ('$client_conto_id','$cash_id');");$n=1;
        }
        if ($n==1){
            $db->query("update B_CLIENT_BALANS set saldo=saldo-'$summ', cash_id='$cash_id', last_update=NOW() where client_id='$client_conto_id';");$n=1;
        }
        return;
    }

    function viewDpSaleInvoice($dp_id,$invoice_id){$db=DbSingleton::getDb();$gmanual=new gmanual;
        $prefix=$doc_type_name=$status_invoice_cap=""; $doc_nom=0;$volume=0;$list="";
        $form="";$form_htm=RD."/tpl/dp_sale_invoice_view.htm";	if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select sv.*, t.name as tpoint_name, sl.name as seller_name, cl.name as client_name, dt.mcaption as doc_type_name, dt.mvalue as doc_type_abr,ch.abr2 as cash_abr from J_SALE_INVOICE sv
            left outer join CASH ch on ch.id=sv.cash_id
            left outer join T_POINT t on t.id=sv.tpoint_id
            left outer join A_CLIENTS sl on sl.id=sv.seller_id
            left outer join A_CLIENTS cl on cl.id=sv.client_conto_id
            left outer join manual dt on dt.key='client_sale_type' and dt.id=sv.doc_type_id
        where sv.status=1 and sv.dp_id='$dp_id' and sv.id='$invoice_id' limit 0,1;");$n=$db->num_rows($r);

        if ($n==1){
            $prefix=$db->result($r,0,"prefix");
            $doc_nom=$db->result($r,0,"doc_nom");
            $data_create=$db->result($r,0,"data_create");
            $tpoint_name=$db->result($r,0,"tpoint_name");
            $seller_name=$db->result($r,0,"seller_name");
            $client_name=$db->result($r,0,"client_name");
            $doc_type_name=$db->result($r,0,"doc_type_name");
            $summ=$db->result($r,0,"summ");
            $cash_abr=$db->result($r,0,"cash_abr");
            $data_pay=$db->result($r,0,"data_pay");
            $status_invoice=$db->result($r,0,"status_invoice");
            $status_invoice_cap=$gmanual->get_gmanual_caption($status_invoice);
//            $usd_to_uah=$db->result($r,0,"usd_to_uah");
//            $eur_to_uah=$db->result($r,0,"eur_to_uah");
//
//            list($usd_to_uah_new,$eur_to_uah_new)=$this->getKoursData();
//            if($usd_to_uah!=$usd_to_uah_new){$usd_to_uah=$usd_to_uah_new;}
//            if($eur_to_uah!=$eur_to_uah_new){$eur_to_uah=$eur_to_uah_new;}
            $form=str_replace("{invoice_id}",$invoice_id,$form);
            $form=str_replace("{data}",$data_create,$form);
            $form=str_replace("{data_pay}",$data_pay,$form);
            $form=str_replace("{prefix}",$prefix,$form);
            $form=str_replace("{doc_nom}",$doc_nom,$form);
            $form=str_replace("{tpoint_name}",$tpoint_name,$form);
            $form=str_replace("{seller_name}",$seller_name,$form);
            $form=str_replace("{client_name}",$client_name,$form);
            $form=str_replace("{doc_type_name}",$doc_type_name,$form);
            $form=str_replace("{invoice_summ}",$summ,$form);
            $form=str_replace("{cash_name}",$cash_abr,$form);
            $form=str_replace("{volume}",$volume,$form);

            $r=$db->query("select * from J_SALE_INVOICE_STR where invoice_id='$invoice_id' order by id asc;");$n=$db->num_rows($r);
            for ($i=1;$i<=$n;$i++){
                $article_nr_displ=$db->result($r,$i-1,"article_nr_displ");
                $brand_id=$db->result($r,$i-1,"brand_id");$brand_name=$this->getBrandName($brand_id);
                $amount=$db->result($r,$i-1,"amount");
                $price=$db->result($r,$i-1,"price");
                $price_end=$db->result($r,$i-1,"price_end");
                $discount=$db->result($r,$i-1,"discount");
                $summ=$db->result($r,$i-1,"summ");
                /*
                if ($cash_id==1){
                    $price=round($price*$usd_to_uah,2); $price_end=round($price_end*$usd_to_uah,2); $summ=round($summ*$usd_to_uah,2); $discount=round($discount*$usd_to_uah,2);
                }
                if ($cash_id==3){
                    $price=round($price*$usd_to_uah/$euro_to_uah,2); $price_end=round($price_end*$usd_to_uah/$euro_to_uah,2); $summ=round($summ*$usd_to_uah/$euro_to_uah,2); $discount_price=round($discount_price*$usd_to_uah/$euro_to_uah,2);
                }*/
                $list.="<tr align='right'>
                    <td align='left'>$i</td>
                    <td align='left'>$article_nr_displ</td>
                    <td align='center'>$brand_name</td>
                    <td>$amount</td>
                    <td>$price</td>
                    <td>$discount</td>
                    <td>$price_end</td>
                    <td>$summ</td>
                </tr>";
            }
            $form=str_replace("{sale_invoice_str_list}",$list,$form);
        }
        return array($form,"№ $prefix-$doc_nom; вид:$doc_type_name; Статус: ".$status_invoice_cap);
    }

    function getClientInfo($client_id) {$db=DbSingleton::getDb();
        $r=$db->query("select sl.full_name as seller_name, sl.phone, sld.edrpou, sld.account, sld.bank, sld.mfo, sld.vytjag, sld.address_fakt as seller_address
        from A_CLIENTS sl 
            left outer join A_CLIENT_DETAILS sld on sld.client_id=sl.id
        where sl.id='$client_id' limit 1;");
        $seller_name=$db->result($r,0,"seller_name");
        $seller_address=$db->result($r,0,"seller_address");
        $edrpou=$db->result($r,0,"edrpou");
        $account=$db->result($r,0,"account");
        $bank=$db->result($r,0,"bank");
        $mfo=$db->result($r,0,"mfo");
        $vat=$db->result($r,0,"vytjag");
        $phone=$db->result($r,0,"phone");
        if ($phone=="") $phone="не вказано";
        return array($seller_name,$seller_address,$edrpou,$account,$bank,$mfo,$vat,$phone);
    }

    function printDpJournal($dp_id) {$db=DbSingleton::getDb();$money=new toMoney;$slave=new slave;
        $invoice_summ=0; $list="";
        $form=""; $form_htm=RD."/tpl/dp_journal_print.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select dp.*, cl.full_name as client_name, cld.address_fakt as client_address from J_DP dp
            left outer join A_CLIENTS cl on cl.id=dp.client_conto_id
            left outer join A_CLIENT_DETAILS cld on cld.client_id=dp.client_conto_id		
        where dp.id='$dp_id' limit 0,1;");
        $prefix=$db->result($r,0,"prefix");
        $doc_nom=$db->result($r,0,"doc_nom");
        $doc_type_id=$db->result($r,0,"doc_type_id");
        $tpoint_id=$db->result($r,0,"tpoint_id");
        $client_name=$db->result($r,0,"client_name");
        $client_address=$db->result($r,0,"client_address");
        $seller_id=$this->getSellerId($tpoint_id,$doc_type_id);
        list($seller_name,$seller_address,$edrpou,$account,$bank,$mfo,$vat,$phone)=$this->getClientInfo($seller_id);

        $r=$db->query("select * from J_DP_STR where dp_id='$dp_id';"); $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $art_id=$db->result($r,$i-1,"art_id");$article_name=$this->getArticleName($art_id);
            $article_nr_displ=$db->result($r,$i-1,"article_nr_displ");
            $brand_id=$db->result($r,$i-1,"brand_id");$brand_name=$this->getBrandName($brand_id);
            $amount=intval($db->result($r,$i-1,"amount"));
            $unit=$this->getUnitArticle($art_id);
            $summ=$db->result($r,$i-1,"summ"); //summa v grn
            $price_end=$summ/$amount;
            $invoice_summ+=$summ;
            $price_end=$slave->to_money($price_end);
            $summ=$slave->to_money($summ);
            $list.="<tr>
                <td align='center'>$i</td>
                <td align='left'>$article_nr_displ ($brand_name)</td>
                <td align='left'>$article_name</td>
                <td align='center'>$unit</td>
                <td align='center'>$amount</td>
                <td align='right'>$price_end</td>
                <td align='right'>$summ</td>
            </tr>";
        }
        $vat_summ=$invoice_summ/6;
        $form=str_replace("{curtime}",date("d.m.Y H:i:s"),$form);
        $form=str_replace("{prefix}",$prefix,$form);
        $form=str_replace("{doc_nom}",$doc_nom,$form);
        $form=str_replace("{data}",date("d.m.Y"),$form);
        $form=str_replace("{client_name}",$client_name,$form);
        $form=str_replace("{seller_name}",$seller_name,$form);
        $form=str_replace("{dp_str_list}",$list,$form);
        $form=str_replace("{invoice_summ}",$slave->to_money($invoice_summ),$form);
        $form=str_replace("{invoice_summ_word}",$money->num2str($slave->to_money($invoice_summ)),$form);
        $form=str_replace("{vat_summ}",$slave->to_money($vat_summ),$form);
        $form=str_replace("{edrpou}",$edrpou,$form);
        $form=str_replace("{rr}",$account,$form);
        $form=str_replace("{bank}",$bank,$form);
        $form=str_replace("{mfo}",$mfo,$form);
        $form=str_replace("{ipn_nom}",$vat,$form);
        $form=str_replace("{client_address}",$client_address,$form);
        $form=str_replace("{seller_address}",$seller_address,$form);
        $form=str_replace("{phone_client}",$phone,$form);
        $mp=new media_print; $mp->print_document($form,"A4");
        return $form;
    }

    function printDpSaleInvoice($dp_id) {$db=DbSingleton::getDb();$money=new toMoney;$slave=new slave;
        $invoice_summ=0; $list="";
        $form=""; $form_htm=RD."/tpl/dp_sale_invoice_print_non_cash.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select dp.*, cl.full_name as client_name, sl.full_name as seller_name, sl.phone, sld.edrpou, sld.account, sld.bank, sld.mfo, sld.vytjag, sld.address_fakt as seller_address, cld.address_fakt as client_address 
        from J_SALE_INVOICE dp
            left outer join A_CLIENTS cl on cl.id=dp.client_id
            left outer join A_CLIENT_DETAILS cld on cld.client_id=dp.client_id
            left outer join A_CLIENTS sl on sl.id=dp.seller_id
            left outer join A_CLIENT_DETAILS sld on sld.client_id=dp.seller_id
        where dp.id='$dp_id' limit 0,1;");
        $prefix=$db->result($r,0,"prefix");
        $doc_nom=$db->result($r,0,"doc_nom");
        $client_name=$db->result($r,0,"client_name");
        $seller_name=$db->result($r,0,"seller_name");
        $edrpou=$db->result($r,0,"edrpou");
        $account=$db->result($r,0,"account");
        $bank=$db->result($r,0,"bank");
        $mfo=$db->result($r,0,"mfo");
        $vat=$db->result($r,0,"vytjag");
        $seller_address=$db->result($r,0,"seller_address");
        $client_address=$db->result($r,0,"client_address");
        $phone=$db->result($r,0,"phone");
        if ($phone=="") $phone="не вказано";

        $r=$db->query("select * from J_SALE_INVOICE_STR where invoice_id='$dp_id';"); $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $art_id=$db->result($r,$i-1,"art_id");$article_name=$this->getArticleName($art_id);
            $article_nr_displ=$db->result($r,$i-1,"article_nr_displ");
            $brand_id=$db->result($r,$i-1,"brand_id");$brand_name=$this->getBrandName($brand_id);
            $amount=intval($db->result($r,$i-1,"amount"));
            $unit=$this->getUnitArticle($art_id);
            $price_end=$db->result($r,$i-1,"price_end");
            $summ=$price_end*$amount;
            $invoice_summ+=$summ;
            $price_end=$slave->to_money($price_end);
            $summ=$slave->to_money($summ);
            $list.="<tr>
                <td align='center'>$i</td>
                <td align='left'>$article_nr_displ ($brand_name)</td>
                <td align='left'>$article_name</td>
                <td align='center'>$unit</td>
                <td align='center'>$amount</td>
                <td align='right'>$price_end</td>
                <td align='right'>$summ</td>
            </tr>";
        }
        $vat_summ=$invoice_summ/6;
        $form=str_replace("{curtime}",date("d.m.Y H:i:s"),$form);
        $form=str_replace("{prefix}",$prefix,$form);
        $form=str_replace("{doc_nom}",$doc_nom,$form);
        $form=str_replace("{data}",date("d.m.Y"),$form);
        $form=str_replace("{client_name}",$client_name,$form);
        $form=str_replace("{seller_name}",$seller_name,$form);
        $form=str_replace("{dp_str_list}",$list,$form);
        $form=str_replace("{invoice_summ}",$slave->to_money($invoice_summ),$form);
        $form=str_replace("{invoice_summ_word}",$money->num2str($slave->to_money($invoice_summ)),$form);
        $form=str_replace("{vat_summ}",$slave->to_money($vat_summ),$form);
        $form=str_replace("{edrpou}",$edrpou,$form);
        $form=str_replace("{rr}",$account,$form);
        $form=str_replace("{bank}",$bank,$form);
        $form=str_replace("{mfo}",$mfo,$form);
        $form=str_replace("{ipn_nom}",$vat,$form);
        $form=str_replace("{client_address}",$client_address,$form);
        $form=str_replace("{seller_address}",$seller_address,$form);
        $form=str_replace("{phone_client}",$phone,$form);
        $mp=new media_print; $mp->print_document($form,"A4");
        return $form;
    }

    function getUnitArticle($art_id) {$db=DbSingleton::getTokoDb(); $abr="";
        $r=$db->query("select t2u.abr from T2_PACKAGING t2p 
        left outer join units t2u on t2u.id=t2p.UNITS_ID
        where t2p.ART_ID='$art_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $abr=$db->result($r,0,"abr");
        }
        return $abr;
    }

    function printSaleInvoice($invoice_id){$db=DbSingleton::getDb();$slave=new slave;$money=new toMoney;$invoice_summ=0;
        $list="";$sel_ar=[];$form="";$address_send="";$volume=0;
        $r=$db->query("select sv.*, t.name as tpoint_name, sl.name as seller_name, sld.edrpou, ot.name as org_type_abr, cl.name as client_name, dt.mcaption as doc_type_name, dt.mvalue as doc_type_abr,ch.abr2 as cash_abr 
        from J_SALE_INVOICE sv
            left outer join CASH ch on ch.id=sv.cash_id
            left outer join T_POINT t on t.id=sv.tpoint_id
            left outer join A_CLIENTS sl on sl.id=sv.seller_id
            left outer join A_CLIENT_DETAILS sld on sld.client_id=sv.seller_id
            left outer join A_ORG_TYPE ot on ot.id=sl.org_type
            left outer join A_CLIENTS cl on cl.id=sv.client_conto_id
            left outer join manual dt on dt.key='client_sale_type' and dt.id=sv.doc_type_id
        where sv.status=1 and sv.id='$invoice_id' limit 0,1;");$n=$db->num_rows($r);

        if ($n==1){
            $prefix=$db->result($r,0,"prefix");
            $doc_nom=$db->result($r,0,"doc_nom");
            $data_create=$db->result($r,0,"data_create");
            $tpoint_name=$db->result($r,0,"tpoint_name");
            $seller_name=$db->result($r,0,"seller_name");
            $edrpou=$db->result($r,0,"edrpou");
            $org_type_abr=$db->result($r,0,"org_type_abr");
            $client_name=$db->result($r,0,"client_name");
            $doc_type_id=$db->result($r,0,"doc_type_id");
            $doc_type_name=$db->result($r,0,"doc_type_name");
            $cash_abr=$db->result($r,0,"cash_abr");
            $data_pay=$db->result($r,0,"data_pay");
            $delivery_address=$db->result($r,0,"delivery_address");
//            $usd_to_uah=$db->result($r,0,"usd_to_uah");
//            $eur_to_uah=$db->result($r,0,"eur_to_uah");
//            list($usd_to_uah_new,$eur_to_uah_new)=$this->getKoursData();
//            if($usd_to_uah!=$usd_to_uah_new){$usd_to_uah=$usd_to_uah_new;}
//            if($eur_to_uah!=$eur_to_uah_new){$eur_to_uah=$eur_to_uah_new;}

            $r=$db->query("select sis.*, ss.storage_id_from, ss.select_id from J_SALE_INVOICE_STR sis 
                left outer join J_SALE_INVOICE_STORSEL iss on iss.invoice_id=sis.invoice_id
                left outer join J_SELECT_STR ss on ss.select_id=iss.select_id
            where sis.invoice_id='$invoice_id' group by sis.id order by sis.id asc;");$n=$db->num_rows($r);

            for ($i=1;$i<=$n;$i++){
                $art_id=$db->result($r,$i-1,"art_id");$article_name=$this->getArticleName($art_id);
                $article_nr_displ=$db->result($r,$i-1,"article_nr_displ");
                $brand_id=$db->result($r,$i-1,"brand_id");$brand_name=$this->getBrandName($brand_id);
                $amount=$db->result($r,$i-1,"amount");
                $storage_id=$db->result($r,$i-1,"storage_id_from");
                $select_id=$db->result($r,$i-1,"select_id"); $sel_ar[$select_id]=$select_id;
                $price_end=$db->result($r,$i-1,"price_end");
                $summ=$db->result($r,$i-1,"summ");
                /*
                if ($cash_id==1){
                    $price=round($price*$usd_to_uah,2); $price_end=round($price_end*$usd_to_uah,2); $summ=round($summ*$usd_to_uah,2); $discount=round($discount*$usd_to_uah,2);
                }
                if ($cash_id==3){
                    $price=round($price*$usd_to_uah/$euro_to_uah,2); $price_end=round($price_end*$usd_to_uah/$euro_to_uah,2); $summ=round($summ*$usd_to_uah/$euro_to_uah,2); $discount_price=round($discount_price*$usd_to_uah/$euro_to_uah,2);
                }*/
                $invoice_summ+=$summ;
                $list.="<tr>
                    <td align='center'>$i</td>
                    <td align='center'>$storage_id</td>
                    <td align='left'>$article_nr_displ</td>
                    <td align='center'>$brand_name</td>
                    <td align='left'>$article_name</td>
                    <td align='center'>$amount</td>
                    <td align='center'>$price_end</td>
                    <td align='center'>$summ</td>
                </tr>";
            }
            $storsel_list="СКВ-";
            foreach($sel_ar as $slr){
                $storsel_list.="$slr/";
            }

            $form_htm="";
            if ($doc_type_id==64){$form_htm=RD."/tpl/dp_sale_invoice_print_64.htm";}
            if ($doc_type_id==63){$form_htm=RD."/tpl/dp_sale_invoice_print_63.htm";}
            if ($doc_type_id==61){$form_htm=RD."/tpl/dp_sale_invoice_print_61.htm";}
            if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}

            $form=str_replace("{curtime}",date("d/m/Y H:i:s"),$form);
            $form=str_replace("{invoice_id}",$invoice_id,$form);
            $form=str_replace("{data}",$data_create,$form);
            $form=str_replace("{data_pay}",$data_pay,$form);
            $form=str_replace("{prefix}",$prefix,$form);
            $form=str_replace("{doc_nom}",$doc_nom,$form);
            $form=str_replace("{tpoint_name}",$tpoint_name,$form);
            $form=str_replace("{seller_name}",$seller_name,$form);
            $form=str_replace("{client_name}",$client_name,$form);
            $form=str_replace("{doc_type_name}",$doc_type_name,$form);
            $form=str_replace("{invoice_summ}",$slave->to_money($invoice_summ),$form);
            $form=str_replace("{invoice_summ_word}",$money->num2str($invoice_summ),$form);
            $form=str_replace("{cash_name}",$cash_abr,$form);
            $form=str_replace("{address_send}",$address_send,$form);
            $form=str_replace("{edrpou}",$edrpou,$form);
            $form=str_replace("{org_type_abr}",$org_type_abr,$form);
            $form=str_replace("{cash_abr}",$cash_abr,$form);
            $form=str_replace("{volume}",$volume,$form);
            $form=str_replace("{storsel_list}",$storsel_list,$form);
            $form=str_replace("{sale_invoice_str_list}",$list,$form);
            $form=str_replace("{delivery_address}",$delivery_address,$form);
            //"Формування друкованої форми"
            $mp=new media_print;
            if ($doc_type_id==63){$mp->print_document($form,"A4-L");}
            if ($doc_type_id==64){$mp->print_document($form,array(210,280));}
            if ($doc_type_id==61){$mp->print_document($form,"A4-L");}
        }
        return $form;
    }

    //======================================================================================

    function updateStockFromStorage($art_id,$storage_id_from,$cell_id_from,$cell_use,$amount){$dbt=DbSingleton::getTokoDb(); $er=1;
        $r=$dbt->query("select `RESERV_AMOUNT` from T2_ARTICLES_STRORAGE where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_from' limit 0,1;");$n=$dbt->num_rows($r);
        if ($n==1){
            $t2s_reserv_amount=$dbt->result($r,0,"RESERV_AMOUNT");
            if ($amount<=$t2s_reserv_amount){
                $t2s_reserv_amount=$t2s_reserv_amount-$amount;
                $dbt->query("update T2_ARTICLES_STRORAGE set `RESERV_AMOUNT`='$t2s_reserv_amount' where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_from' limit 1;");
                if ($cell_use==1){
                    $r1=$dbt->query("select `RESERV_AMOUNT` from T2_ARTICLES_STRORAGE_CELLS where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_from' and `STORAGE_CELLS_ID`='$cell_id_from' limit 0,1;");$n1=$dbt->num_rows($r1);
                    if ($n1==1){
                        $t2sc_reserv_amount=$dbt->result($r1,0,"RESERV_AMOUNT");
                        if ($amount>0){
                            $t2sc_reserv_amount=$t2sc_reserv_amount-$amount;
                            $dbt->query("update T2_ARTICLES_STRORAGE_CELLS set `RESERV_AMOUNT`='$t2sc_reserv_amount' where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_from' and `STORAGE_CELLS_ID`='$cell_id_from' limit 1;");
                        }
                    }
                }
            }
            $er=0;
        }
        return $er;
    }

    function updateStockToStorage($art_id,$storage_id_to,$cell_id_to,$cell_use,$amount){$dbt=DbSingleton::getTokoDb(); $er=1;
        $r=$dbt->query("select `AMOUNT` from T2_ARTICLES_STRORAGE where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_to' limit 0,1;");$n=$dbt->num_rows($r);
        if ($n==0){
            $dbt->query("insert into T2_ARTICLES_STRORAGE (`ART_ID`,`AMOUNT`,`RESERV_AMOUNT`,`STORAGE_ID`) values ('$art_id','$amount','0','$storage_id_to');");
            if ($cell_use==1){
                $dbt->query("insert into T2_ARTICLES_STRORAGE_CELLS (`ART_ID`,`AMOUNT`,`RESERV_AMOUNT`,`STORAGE_ID`,`STORAGE_CELLS_ID`) values ('$art_id','$amount','0','$storage_id_to','$cell_id_to');");
            }
            $er=0;
        }
        if ($n==1){
            $t2s_amount=$dbt->result($r,0,"AMOUNT");
            if ($amount>0){ $t2s_amount=$t2s_amount+$amount;
                $dbt->query("update T2_ARTICLES_STRORAGE set `AMOUNT`='$t2s_amount' where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_to' limit 1;");
                if ($cell_use==1){
                    $r1=$dbt->query("select `AMOUNT` from T2_ARTICLES_STRORAGE_CELLS where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_to' and `STORAGE_CELLS_ID`='$cell_id_to' limit 0,1;");$n1=$dbt->num_rows($r1);
                    if ($n1==0){
                        $dbt->query("insert into T2_ARTICLES_STRORAGE_CELLS (`ART_ID`,`AMOUNT`,`RESERV_AMOUNT`,`STORAGE_ID`,`STORAGE_CELLS_ID`) values ('$art_id','$amount','0','$storage_id_to','$cell_id_to');");
                    }
                    if ($n1==1){
                        $t2sc_amount=$dbt->result($r1,0,"AMOUNT");
                        if ($amount>0){
                            $t2sc_amount=$t2sc_amount+$amount;
                            $dbt->query("update T2_ARTICLES_STRORAGE_CELLS set `AMOUNT`='$t2sc_amount' where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_to' and `STORAGE_CELLS_ID`='$cell_id_to' limit 1;");
                        }
                    }
                }
                $er=0;
            }
        }
        return $er;
    }

    function updateStockFromStorageLocal($art_id,$storage_id_from,$cell_id_from,$cell_id_to,$amount){$dbt=DbSingleton::getTokoDb(); $er=1;
        $r=$dbt->query("select `AMOUNT`,`RESERV_AMOUNT` from T2_ARTICLES_STRORAGE where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_from' limit 0,1;");$n=$dbt->num_rows($r);
        if ($n==1){
            $t2s_amount=$dbt->result($r,0,"AMOUNT");
            $t2s_reserv_amount=$dbt->result($r,0,"RESERV_AMOUNT");
            if ($amount<=$t2s_reserv_amount){
                $t2s_reserv_amount=$t2s_reserv_amount-$amount;
                $t2s_amount=$t2s_amount+$amount;
                $dbt->query("update T2_ARTICLES_STRORAGE set `RESERV_AMOUNT`='$t2s_reserv_amount',`AMOUNT`='$t2s_amount' where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_from' limit 1;");

                $r1=$dbt->query("select `RESERV_AMOUNT` from T2_ARTICLES_STRORAGE_CELLS where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_from' and `STORAGE_CELLS_ID`='$cell_id_from' limit 0,1;");$n1=$dbt->num_rows($r1);
                if ($n1==1){
                    $t2sc_reserv_amount=$dbt->result($r1,0,"RESERV_AMOUNT");
                    if ($amount>0){
                        $t2sc_reserv_amount=$t2sc_reserv_amount-$amount;
                        $dbt->query("update T2_ARTICLES_STRORAGE_CELLS set `RESERV_AMOUNT`='$t2sc_reserv_amount' where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_from' and `STORAGE_CELLS_ID`='$cell_id_from' limit 1;");
                    }
                }
                $r2=$dbt->query("select `AMOUNT` from T2_ARTICLES_STRORAGE_CELLS where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_from' and `STORAGE_CELLS_ID`='$cell_id_to' limit 0,1;");$n2=$dbt->num_rows($r2);
                    if ($n2==0){
                        $dbt->query("insert into T2_ARTICLES_STRORAGE_CELLS (`ART_ID`,`AMOUNT`,`RESERV_AMOUNT`,`STORAGE_ID`,`STORAGE_CELLS_ID`) values ('$art_id','$amount','0','$storage_id_from','$cell_id_to');");
                    }
                    if ($n2==1){
                        $t2sc_amount2=$dbt->result($r2,0,"AMOUNT");
                        if ($amount>0){
                            $t2sc_amount2=$t2sc_amount2+$amount;
                            $dbt->query("update T2_ARTICLES_STRORAGE_CELLS set `AMOUNT`='$t2sc_amount2' where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_from' and `STORAGE_CELLS_ID`='$cell_id_to' limit 1;");
                        }
                    }
            }
            $er=0;
        }
        return $er;
    }

    //===============			MONEY PAY 		==================================

    function loadDpMoneyPay($dp_id){$db=DbSingleton::getDb();$list="";
        $form="";$form_htm=RD."/tpl/dp_money_pay_list.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select id from J_SALE_INVOICE where status=1 and dp_id='$dp_id';");$n=$db->num_rows($r);$invoice_str="0";
        for ($i=1;$i<=$n;$i++){ $invoice_str.=",".$db->result($r,$i-1,"id"); }
        $r=$db->query("select pay.*, pt.mcaption as pay_type_caption, pb.name as paybox_name, c.abr, pst.summ_pay, pst.doc_cash_id, cd.abr as d_abr, pst.pay_cash_kours 
        from J_PAY pay
            left outer join J_PAY_STR pst on pst.pay_id=pay.id
            left outer join CASH c on c.id=pay.cash_id
            left outer join CASH cd on cd.id=pst.doc_cash_id
            left outer join T_POINT_PAY_BOX pb on pb.id=pay.paybox_id
            left outer join manual pt on (pt.key='pay_type_id' and pt.id=pay.pay_type_id)
        where pay.status=1 and pst.parrent_doc_id in ($invoice_str) and pst.parrent_doc_type_id!=0 group by pay.id order by pay.data_time desc, pay.id desc;"); $n=$db->num_rows($r);

        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $data_time=$db->result($r,$i-1,"data_time");
            $pay_type_caption=$db->result($r,$i-1,"pay_type_caption");
            $paybox_name=$db->result($r,$i-1,"paybox_name");
            $doc_nom=$db->result($r,$i-1,"doc_nom");
            $summ=$db->result($r,$i-1,"summ");
            $summ_pay=$db->result($r,$i-1,"summ_pay");
            $cash_name=$db->result($r,$i-1,"abr");
            $doc_cash_name=$db->result($r,$i-1,"d_abr");
            $user_name=$this->getMediaUserName($db->result($r,$i-1,"user_id"));
            $pay_cash_kours=$db->result($r,$i-1,"pay_cash_kours");

            $list.="<tr id='strStsRow_$i' onClick='viewDpMoneyPay(\"$dp_id\",\"$id\");'>
                <td align='center'>$i</td>
                <td align='center'>$data_time</td>
                <td>$pay_type_caption</td>
                <td align='center' style='min-width:140px;'>$doc_nom</td>
                <td align='center' style='min-width:140px;'>$paybox_name</td>
                <td align='right' style='min-width:120px;'>$summ $cash_name</td>
                <td align='right' style='min-width:120px;'>$summ_pay $doc_cash_name</td>
                <td align='right'>$pay_cash_kours</td>
                <td>$user_name</td>
            </tr>";
        }
        if ($n==0){$list="<tr><td colspan=9 align='center'><h3>Документи оплати відсутні</h3></td></tr>";}
        $form=str_replace("{money_pay_list}",$list,$form);
        return $form;
    }

    //===============			MONEY PAY 		==================================

    //===============			Orders From Site	==============================

    function countSupplCoopSite() { $db=DbSingleton::getDb();
        $r=$db->query("select count(id) as kilk from J_SUPPLIERS_COOPERATION where status=166;");
        $count=$db->result($r,0,"kilk");
        if ($count>0) $back="background: red;"; else $back="";
        return array($count,$back);
    }

    function countReportOverdrafts() { $db=DbSingleton::getDb();
        $date_cur=date("Y-m-d"); $where=" and sv.data_pay<'$date_cur'"; $tpoint=$this->getTpointbyUser();
        $where_tpoint=" and sv.tpoint_id=$tpoint ";
        $media_role_id=$_SESSION["media_role_id"];
        if ($media_role_id=="1") $where_tpoint="";
        $r=$db->query("select sv.* from J_SALE_INVOICE sv where sv.status=1 and sv.summ_debit>0 $where $where_tpoint;"); $count=$db->num_rows($r);
        if ($count>0) $back="background: red;"; else $back="";
        return array($count,$back);
    }

    function getTpointbyUser() { $db=DbSingleton::getDb(); $tpoint=1;
        $user_id=$_SESSION["media_user_id"];
        $r=$db->query("select tpoint_id from media_users where id='$user_id';"); $n=$db->num_rows($r);
        if ($n>0) $tpoint=$db->result($r,0,"tpoint_id");
        return $tpoint;
    }

    function countOrdersSite() { $db=DbSingleton::getDb(); $user_id=$_SESSION["media_user_id"]; $ses_tpoint_id=$_SESSION["media_tpoint_id"];
        $where=" and tpoint_id='$ses_tpoint_id'"; if ($user_id==1 || $user_id==2 || $user_id==7) {$where="";}
        $r=$db->query("select count(id) as kilk from orders_new where status=1 and dp_id='' $where;");
        $count=$db->result($r,0,"kilk");
        if ($count>0) $back="background: red;"; else $back="";
        return array($count,$back);
    }

    function countUsersSite() { $db=DbSingleton::getDb();
        $r=$db->query("select count(id) as kilk_ret from A_CLIENTS_USERS_RETAIL where status=145 and client_category=140;");
        $kilk_ret=$db->result($r,0,"kilk_ret");
        $r=$db->query("select count(id) as kilk_mag from A_CLIENTS_USERS_RETAIL where status=145 and client_category!=140;");
        $kilk_mag=$db->result($r,0,"kilk_mag");
        $count="$kilk_mag / $kilk_ret";
        if ($kilk_mag>0) $back="background: red;"; else $back="";
        return array($count,$back);
    }

    function showOrdersSite() {$press="";$data=date("Y-m-d");
        $form="";$form_htm=RD."/tpl/dp_orders_site_list.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $list=$this->showOrderSiteRange($press,$data,$data);
        $form=str_replace("{orders_site_list}",$list,$form);
        $form=str_replace("{data_start}",$data,$form);
        $form=str_replace("{data_end}",$data,$form);
        return $form;
    }

    function showOrderSiteRange($press,$data_start,$data_end) {$db=DbSingleton::getDb();session_start(); $user_id=$_SESSION["media_user_id"];$ses_tpoint_id=$_SESSION["media_tpoint_id"]; $data_cur=date("Y-m-d");
        $where=" and o.tpoint_id='$ses_tpoint_id'";if ($user_id==1 || $user_id==2 || $user_id==7){$where="";} $where_status=""; $list="";
        if (!$press) $where_status="and o.status=1 and o.dp_id=''"; else {
            if ($data_start!='' && $data_end!='') $where="and o.data>='$data_start 00:00:00' and o.data<='$data_end 23:59:59'";
            else $where=" and o.data>='$data_cur 00:00:00' and o.data<='$data_cur 23:59:59'";
        }
        $r=$db->query("select o.*, t.name as tpoint_name, c.name as client_name, cr.name as retail_name, cit.CITY_NAME as region_name, csh.abr, dt.mcaption as delivery_type_caption 
        from orders_new o
           left outer join T_POINT t on (t.id=o.tpoint_id)
           left outer join A_CLIENTS c on (c.id=o.client_id)
           left outer join A_CLIENTS_USERS cr on (cr.id=o.client_user_id)
           left outer join T2_CITY cit on (cit.CITY_ID=o.region)
           left outer join CASH csh on (csh.id=o.cash_id)
           left outer join manual dt on (dt.id=o.delivery and dt.`key`='delivery_type')
        where o.id!=0 $where_status $where order by o.dp_id;");$n=$db->num_rows($r);

        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $dp_id=$db->result($r,$i-1,"dp_id");
            $status=$db->result($r,$i-1,"status");
            $data=$db->result($r,$i-1,"data");
            $client_id=$db->result($r,$i-1,"client_id");$client_name=$db->result($r,$i-1,"client_name");
            $client_user_id=$db->result($r,$i-1,"client_user_id");
            $retail_name=$this->getClientUserName($client_id,$client_user_id);
            $tpoint_name=$db->result($r,$i-1,"tpoint_name");
            $cash_id=$db->result($r,$i-1,"cash_id");$cash_abr=$db->result($r,$i-1,"csh.abr");
            $name=$db->result($r,$i-1,"name");
            $email=$db->result($r,$i-1,"email");
            $phone=$db->result($r,$i-1,"phone");
            $region_name=$db->result($r,$i-1,"region_name");
            $address=$db->result($r,$i-1,"address");
            $delivery=$db->result($r,$i-1,"delivery");
            $delivery_type=$db->result($r,$i-1,"delivery_type_caption");
            $price_summ=$db->result($r,$i-1,"price_summ"); $price_summ=$this->getKoursFromUAH($price_summ,$cash_id);
            $color='';
            if ($dp_id!='') $color='background:lightgreen;';
            if ($status==0) $color='background:pink;';
            if ($name!="") $name="($name)"; else $name="";

            $list.="<tr id='strStsRow_$i' onClick='showOrdersSiteCard(\"$id\");' style='cursor:pointer; $color'>
                <td align='center'>$i</td>
                <td align='center'>$tpoint_name</td>
                <td align='center'>$id</td>
                <td align='center'>$data</td>
                <td>$client_name</td>
                <td>$retail_name $name</td>
                <td style='min-width:140px;'>$phone $email</td>
                <td>$region_name $address</td>
                <td align='right' style='min-width:120px;'>$delivery_type $delivery</td>
                <td align='right'>$price_summ $cash_abr</td>
            </tr>";
        }
        return $list;
    }

    function getKours($val) { $db=DbSingleton::getDb();
        $r=$db->query("select kours_value from J_KOURS where cash_id=2 and in_use=1 limit 0,1;");
        $usd=number_format($db->result($r,0,"kours_value"), 2, '.', '');
        $r=$db->query("select kours_value from J_KOURS where cash_id=3 and in_use=1 limit 0,1;");
        $euro=number_format($db->result($r,0,"kours_value"), 2, '.', '');
        $val=="dollar" ? $result=$usd : ($val=="euro" ? $result=$euro : $result=0);
        return $result;
    }

    function getKoursFromUAH($price,$cur) {
        if ($cur==2) {$price=$price/$this->getKours("dollar"); $price=number_format($price, 2, '.', ''); } else
        if ($cur==3) {$price=$price/$this->getKours("euro"); $price=number_format($price, 2, '.', ''); } else
        $price=number_format($price, 2, '.', '');
        return $price;
    }

    function getClientUserName($client_id,$user_id) {$db=DbSingleton::getDb();
        $r=$db->query("select client_id,name from A_CLIENTS_USERS where id='$user_id' and client_id='$client_id' limit 1;"); $n=$db->num_rows($r);
        if ($n>0) {$name=$db->result($r,0,"name");} else {
            $r=$db->query("select name from A_CLIENTS_USERS_RETAIL where id='$user_id' and client_id='$client_id' limit 1;");
            $name=$db->result($r,0,"name");
        }
        return $name;
    }

    function showOrdersSiteCard($order_id){$db=DbSingleton::getDb();$slave=new slave;$gmanual=new gmanual;
        $order_id=$slave->qq($order_id);$list="";$data="";
        $form="";$form_htm=RD."/tpl/dp_orders_site_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select o.*, t.name as tpoint_name, c.name as client_name, cr.name as retail_name, cit.CITY_NAME as region_name, csh.abr, dt.mcaption as delivery_type_caption
        from orders_new o
            left outer join T_POINT t on (t.id=o.tpoint_id)
            left outer join A_CLIENTS c on (c.id=o.client_id)
            left outer join A_CLIENTS_USERS_RETAIL cr on (cr.id=o.client_user_id)
            left outer join T2_CITY cit on (cit.CITY_ID=o.region)
            left outer join CASH csh on (csh.id=o.cash_id)
            left outer join manual dt on (dt.id=o.delivery and dt.`key`='delivery_type')
        where o.id='$order_id' limit 1;");$n=$db->num_rows($r);

        if ($n==0){$form="<h2 align='center'>Замволення відсутнє або передано в роботу</h2>";}
        if ($n==1){
            $id=$db->result($r,0,"id");
            $data=$db->result($r,0,"data");
            $client_id=$db->result($r,0,"client_id");$client_name=$db->result($r,0,"client_name");
            $client_user_id=$db->result($r,0,"client_user_id");
            $retail_name=$this->getClientUserName($client_id,$client_user_id);
            $tpoint_id=$db->result($r,0,"tpoint_id");
            $cash_abr=$db->result($r,0,"csh.abr");
            $name=$db->result($r,0,"name");
            $delivery_type=$db->result($r,0,"delivery_type_caption");
            $delivery_info=$db->result($r,0,"delivery_info");
            $price_summ=$db->result($r,0,"price_summ");
            $payment=$db->result($r,0,"payment");
            $payment_info=$db->result($r,0,"payment_info");

            $form=str_replace("{data}",$data,$form);
            $form=str_replace("{order_id}",$id,$form);
            $form=str_replace("{name}",$name,$form);
            $form=str_replace("{cash_abr}",$cash_abr,$form);
            $form=str_replace("{summ}",$price_summ,$form);
            $form=str_replace("{retail_name}",$retail_name,$form);
            $form=str_replace("{client_name}",$client_name,$form);
            $form=str_replace("{tpoint_name}",$this->getTpointName($tpoint_id),$form);
            $form=str_replace("{delivery}",$delivery_type,$form);
            $form=str_replace("{delivery_info}",$delivery_info,$form);
            $form=str_replace("{payment}",$gmanual->get_gmanual_caption($payment),$form);
            $form=str_replace("{payment_info}",$payment_info,$form);

            $r1=$db->query("select * from orders_str_new where order_id='$order_id';");$n1=$db->num_rows($r1);
            for ($i=1;$i<=$n1;$i++){
                $suppl_id=$db->result($r1,$i-1,"suppl_id");
                $suppl_name=$this->getClientName($suppl_id);
                $art_id=$db->result($r1,$i-1,"art_id");
                $article_nr_displ=$db->result($r1,$i-1,"article_nr_displ");
                $brand_name=$db->result($r1,$i-1,"brand_name");
                $amount=$db->result($r1,$i-1,"amount");
                $price=$db->result($r1,$i-1,"price");
                $summ=$db->result($r1,$i-1,"summ");
                $list.="<tr>
                    <td>$i</td>
                    <td>$suppl_name</td>
                    <td>$art_id</td>
                    <td>$article_nr_displ</td>
                    <td>$brand_name</td>
                    <td>$amount</td>
                    <td>$price</td>
                    <td>$summ</td>
                </tr>";
            }
            $form=str_replace("{order_str_list}",$list,$form);
        }
        return array($form,"Замволення з сайту № $order_id від ".$slave->data_word($data));
    }

    function deleteOrderSite($order_id) {$db=DbSingleton::getDb();$answer=0;$err="Помилка!";
        if ($order_id>0){
            $db->query("update orders_new set status=0 where id='$order_id';");
            $answer=1; $err="";
        }
        return array($answer,$err);
    }

    function createDpFromOrder($order_id){$db=DbSingleton::getDb();$slave=new slave;session_start();$answer=0;$err="Помилка!";$dp_id=0;
        $order_id=$slave->qq($order_id);
        if ($order_id>0){
            $r=$db->query("select o.*, t.name as tpoint_name, c.name as client_name, cr.name as retail_name, cit.CITY_NAME as region_name, csh.abr, dt.mcaption as delivery_type_caption
            from orders_new o
                left outer join T_POINT t on (t.id=o.tpoint_id)
                left outer join A_CLIENTS c on (c.id=o.client_id)
                left outer join A_CLIENTS_USERS_RETAIL cr on (cr.id=o.client_user_id)
                left outer join T2_CITY cit on (cit.CITY_ID=o.region)
                left outer join CASH csh on (csh.id=o.cash_id)
                left outer join manual dt on (dt.id=o.delivery and dt.`key`='delivery_type')
            where o.status=1 and o.id='$order_id' limit 0,1;");$n=$db->num_rows($r);

            if ($n==1){
                $client_id=$db->result($r,0,"client_id");
                $tpoint_id=$db->result($r,0,"tpoint_id");
                $cash_id=$db->result($r,0,"cash_id");
                $delivery=$db->result($r,0,"delivery");
                $delivery_info=$db->result($r,0,"delivery_info");
                $price_summ=$db->result($r,0,"price_summ");
                $carrier_id=$db->result($r,0,"carrier_id");

                $dp_id=$this->newDpCard();
                if ($dp_id>0){
                    list(,,$doc_type)=$this->getDpClientDocType($client_id);
                    $db->query("update orders_new set dp_id='$dp_id' where id='$order_id';");
                    $db->query("update J_DP set tpoint_id='$tpoint_id', client_id='$client_id', client_conto_id='$client_id', cash_id='$cash_id', summ='$price_summ', delivery_type_id='$delivery', carrier_id='$carrier_id', doc_type_id='$doc_type', delivery_address='$delivery_info' where id='$dp_id' limit 1;");

                    $r1=$db->query("select * from orders_str_new where order_id='$order_id';");$n1=$db->num_rows($r1);
                    for ($i=1;$i<=$n1;$i++){
                        $str_id=$db->result($r1,$i-1,"id");
                        $suppl_id=$db->result($r1,$i-1,"suppl_id");
                        $art_id=$db->result($r1,$i-1,"art_id");
                        $article_nr_displ=$db->result($r1,$i-1,"article_nr_displ");
                        $brand_id=$db->result($r1,$i-1,"brand_id");
                        $amount=$db->result($r1,$i-1,"amount");
                        $storage_id=$db->result($r1,$i-1,"storage_id");

                        $dp_str_id=0;$ans=0;
                        if ($suppl_id==0){
                            list($ans,$er1r,$dp_str_id)=$this->setArticleToDp($dp_id,$tpoint_id,$art_id,$article_nr_displ,$brand_id,$storage_id,$amount);
                        }
                        if ($suppl_id!=0){
                            list($ans,$er1r,$dp_str_id)=$this->setArticleSupplToDp($dp_id,$tpoint_id,$art_id,$article_nr_displ,$brand_id,$suppl_id,$storage_id,$amount);
                        }
                        if ($dp_str_id>0 && $ans==1){
                            $db->query("update orders_str_new set dp_str_id='$dp_str_id' where id='$str_id';");
                        }
                    }
                    $answer=1;$err="";
                }
            }
        }
        return array($answer,$err,$dp_id);
    }

    function getDpArticleAmount($str_id,$art_id){$db=DbSingleton::getDb(); $amount=0;
        $r=$db->query("select amount from J_DP_STR where id='$str_id' and art_id='$art_id';");$n=$db->num_rows($r);
        if ($n==1){$amount=$db->result($r,0,"amount");}
        return $amount;
    }

    function loadDpSiteOrder($dp_id){$db=DbSingleton::getDb();$slave=new slave;$gmanual=new gmanual;session_start();
        $dp_id=$slave->qq($dp_id);$form="";$list="";
        if ($dp_id==0){$form="<h2 align='center'>Замволення відсутнє або ще не передано в роботу</h2>";}
        if ($dp_id>0){
            $form_htm=RD."/tpl/dp_orders_site_view.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
            $r=$db->query("select o.*, t.name as tpoint_name, c.name as client_name, cr.name as retail_name, cit.CITY_NAME as region_name, csh.abr, dt.mcaption as delivery_type_caption
            from orders_new o
                left outer join T_POINT t on (t.id=o.tpoint_id)
                left outer join A_CLIENTS c on (c.id=o.client_id)
                left outer join A_CLIENTS_USERS_RETAIL cr on (cr.id=o.client_user_id)
                left outer join T2_CITY cit on (cit.CITY_ID=o.region)
                left outer join CASH csh on (csh.id=o.cash_id)
                left outer join manual dt on (dt.id=o.delivery and dt.`key`='delivery_type')
            where o.status=1 and o.dp_id='$dp_id' limit 0,1;");$n=$db->num_rows($r);

            if ($n==0){$form="<h2 align='center'>Замволення відсутнє або ще не передано в роботу</h2>";}
            if ($n==1){
                $order_id=$db->result($r,0,"id");
                $data=$db->result($r,0,"data");$client_name=$db->result($r,0,"client_name");
                $retail_name=$db->result($r,0,"retail_name");
                $tpoint_id=$db->result($r,0,"tpoint_id");
                $cash_abr=$db->result($r,0,"csh.abr");
                $name=$db->result($r,0,"name");
                $delivery_type=$db->result($r,0,"delivery_type_caption");
                $delivery_info=$db->result($r,0,"delivery_info");
                $price_summ=$db->result($r,0,"price_summ");
                $payment=$db->result($r,0,"payment");
                $payment_info=$db->result($r,0,"payment_info");

                $form=str_replace("{data}",$data,$form);
                $form=str_replace("{order_id}",$order_id,$form);
                $form=str_replace("{name}",$name,$form);
                $form=str_replace("{cash_abr}",$cash_abr,$form);
                $form=str_replace("{summ}",$price_summ,$form);
                $form=str_replace("{retail_name}",$retail_name,$form);
                $form=str_replace("{client_name}",$client_name,$form);
                $form=str_replace("{tpoint_name}",$this->getTpointName($tpoint_id),$form);
                $form=str_replace("{delivery}",$delivery_type,$form);
                $form=str_replace("{delivery_info}",$delivery_info,$form);
                $form=str_replace("{payment}",$gmanual->get_gmanual_caption($payment),$form);
                $form=str_replace("{payment_info}",$payment_info,$form);

                $r1=$db->query("select * from orders_str_new where order_id='$order_id';");$n1=$db->num_rows($r1);
                for ($i=1;$i<=$n1;$i++){
                    $dp_str_id=$db->result($r1,$i-1,"dp_str_id");
                    $suppl_id=$db->result($r1,$i-1,"suppl_id");$suppl_name=$this->getClientName($suppl_id);
                    $art_id=$db->result($r1,$i-1,"art_id");
                    $article_nr_displ=$db->result($r1,$i-1,"article_nr_displ");
                    $brand_name=$db->result($r1,$i-1,"brand_name");
                    $dp_str_amount=$this->getDpArticleAmount($dp_str_id,$art_id);
                    $amount=$db->result($r1,$i-1,"amount");
                    $price=$db->result($r1,$i-1,"price");
                    $summ=$db->result($r1,$i-1,"summ");
                    $color=""; if ($dp_str_amount!=$amount){$color=" style='background-color:#ffe000;'";}
                    $list.="<tr $color>
                        <td>$i</td>
                        <td>$suppl_name</td>
                        <td>$art_id</td>
                        <td>$article_nr_displ</td>
                        <td>$brand_name</td>
                        <td>$dp_str_amount</td>
                        <td>$amount</td>
                        <td>$price</td>
                        <td>$summ</td>
                    </tr>";
                }
                $form=str_replace("{order_str_list}",$list,$form);
            }
        }
        return $form;
    }

    function getDistinctOrdersItems($dp_id){$db=DbSingleton::getDb();$slave=new slave;session_start();$amount="";$dp_id=$slave->qq($dp_id);
        if ($dp_id>0){
            $r1=$db->query("SELECT * FROM (
                SELECT os.amount, ifnull( dps.amount, 0 ) AS dp_amount
                FROM orders_str_new os
                LEFT OUTER JOIN orders_new o ON o.id = os.order_id
                LEFT OUTER JOIN J_DP_STR dps ON dps.id = os.dp_str_id
                WHERE o.dp_id = '$dp_id'
                ) AS res
            WHERE amount <> dp_amount;");$n1=$db->num_rows($r1);
            if ($n1>0){$amount="<span class='label label-tab label-warning'>$n1</span>";}
        }
        return $amount;
    }

    function showSupplToLocalChangeForm($art_id,$brand_id,$dp_id,$dp_str_id){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave; session_start();
        $art_id=$slave->qq($art_id);$dp_id=$slave->qq($dp_id);
        $form="";$form_htm=RD."/tpl/dp_suppl_to_local_change_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        if ($dp_id>0 && $art_id>0){
            $r1=$db->query("select * from orders_str_new where dp_str_id='$dp_str_id' and art_id='$art_id';");$n1=$db->num_rows($r1);
            if ($n1==0){// замовлення не з сайту. шукаємо замовлення у ДП
                $r1=$db->query("select * from J_DP_STR where id='$dp_str_id' and art_id='$art_id';");$n1=$db->num_rows($r1);
            }
            if ($n1==1){
                $suppl_id=$db->result($r1,0,"suppl_id");$suppl_name=$this->getClientName($suppl_id);
                $article_nr_displ=$db->result($r1,0,"article_nr_displ");
                $brand_id=$db->result($r1,0,"brand_id");$brand_name=$this->getBrandName($brand_id);
                $dp_str_amount=$this->getDpArticleAmount($dp_str_id,$art_id);
                $amount=$db->result($r1,0,"amount");
                $price=$db->result($r1,0,"price");
                $form=str_replace("{dp_id}",$dp_id,$form);
                $form=str_replace("{dp_str_id}",$dp_str_id,$form);
                $form=str_replace("{suppl_id}",$suppl_id,$form);
                $form=str_replace("{suppl_name}",$suppl_name,$form);
                $form=str_replace("{article_nr_displ}",$article_nr_displ,$form);
                $form=str_replace("{art_id}",$art_id,$form);
                $form=str_replace("{brand_name}",$brand_name,$form);
                $form=str_replace("{amount}",$amount,$form);
                $form=str_replace("{amount_order}",$dp_str_amount,$form);
                $stock_list="";

                $r=$dbt->query("select t2sc.AMOUNT,t2sc.RESERV_AMOUNT,sc.id as cell_id, sc.cell_value,s.id as storage_id, s.name as storage_name 
                from T2_ARTICLES_STRORAGE_CELLS t2sc 
                    left outer join STORAGE_CELLS sc on (sc.id=t2sc.STORAGE_CELLS_ID) 
                    left outer join STORAGE s on s.ID=sc.STORAGE_ID
                where t2sc.ART_ID='$art_id' and (t2sc.AMOUNT>0 or t2sc.RESERV_AMOUNT>0) order by sc.cell_value  asc;");$n=$dbt->num_rows($r);
                for ($i=1;$i<=$n;$i++){
                    $amount=$dbt->result($r,$i-1,"AMOUNT");
                    $reserv_amount=$dbt->result($r,$i-1,"RESERV_AMOUNT");
                    $cell_id=$dbt->result($r,$i-1,"cell_id");
                    $cell_value=$dbt->result($r,$i-1,"cell_value");
                    $storage_id=$dbt->result($r,$i-1,"storage_id");
                    $storage_name=$dbt->result($r,$i-1,"storage_name");
                    $stock_list.="<tr>
                        <td>$i</td>
                        <td>$storage_name</td>
                        <td>$cell_value</td>
                        <td>$amount</td>
                        <td>$reserv_amount</td>
                        <td><input type='text' class='form-input' data-storage='$storage_id' data-cell='$cell_id' id='stlca_$i' data-maxvalue='$amount' data-price='$price' value='0'></td>
                    </tr>";
                }
                $form=str_replace("{stock_list}",$stock_list,$form);
                $form=str_replace("{stock_n}",$n,$form);
            }
            if ($n1==0){$form="<h3 align='center'>Заказ клієнта не знайдено.</h3>";}
        }
        return array($form,"Відбір індексу з приходу від постачальника");
    }

    function saveDpSupplToLocalChangeForm($dp_id,$dp_str_id,$art_id,$amount,$price,$storage_id,$cell_id){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave; session_start(); $answer=0; $err="Помилка!";
        $rr_amount=$rr_reserv=0;
        $dp_id=$slave->qq($dp_id);$dp_str_id=$slave->qq($dp_str_id);$art_id=$slave->qq($art_id);$amount=$slave->qq($amount);$price=$slave->qq($price);$storage_id=$slave->qq($storage_id);
        if ($art_id>0 && $amount>0 && $dp_str_id>0 && $storage_id>0){
            // check if article still exist in cell
            $r1=$dbt->query("select AMOUNT,RESERV_AMOUNT from T2_ARTICLES_STRORAGE where ART_ID='$art_id' and STORAGE_ID='$storage_id' and AMOUNT>='$amount' limit 0,1;"); $n1=$dbt->num_rows($r1); $stor_ex=0;print "n1=$n1\n;";
            if ($n1==1){
                $rr_amount=$dbt->result($r1,0,"AMOUNT");
                $rr_reserv=$dbt->result($r1,0,"RESERV_AMOUNT");
                $stor_ex=1;
            }
            if ($stor_ex==1){
                $tpoint_id=$this->getDpTpoint($dp_id);
                $reserv_type_id=$this->getArticleReservType($tpoint_id,$storage_id);
                $summ=round($price*$amount,2);
                $db->query("update J_DP_STR set `amount`='$amount', `summ`='$summ', `storage_id_from`='$storage_id', `location_storage_id`='$storage_id', `reserv_type_id`='$reserv_type_id', status_dps='93' where id='$dp_str_id' and dp_id='$dp_id';");
                //?????$db->query("update J_DP set status_dps='93' where id='$dp_id';");
                list($weight,$volume,$empty_kol)=$this->updateDpWeightVolume($dp_id);
                $dp_summ=$this->updateDpSumm($dp_id);

                $rr_amount=$rr_amount-$amount;$rr_reserv=$rr_reserv+$amount;
                $dbt->query("update T2_ARTICLES_STRORAGE set AMOUNT='$rr_amount', `RESERV_AMOUNT`='$rr_reserv' where ART_ID='$art_id' and STORAGE_ID ='$storage_id';");
                //list($empty_kol,$label_empty)=$this->labelArtEmptyCount($dp_id,$empty_kol);
                $db->query("update J_DP set `status_dp`='79' where id='$dp_id';");
                $answer=1;$err="";
            }
        }
        return array($answer,$err);
    }

}

