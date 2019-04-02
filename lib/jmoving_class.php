<?php

class jmoving {

    protected $prefix_new = 'ДФ';

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

    function get_jmoving_prefix($jmoving_id){ $db=DbSingleton::getDb();$prefix="ПР";
        $r=$db->query("select type_id from J_MOVING where id='$jmoving_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$type_id=$db->result($r,0,"type_id"); if ($type_id==0){$prefix="В-ПР";}}
        return $prefix;
    }

    function get_doc_client_prefix($client_id){ $db=DbSingleton::getDb();$prefix_id=0;$doc_type_id=40;
        $r=$db->query("select id from A_CLIENTS_DOCUMENT_PREFIX where client_id='$client_id' and doc_type_id='$doc_type_id' and status='1' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$prefix_id=$db->result($r,0,"id");}
        return $prefix_id;
    }

    function get_df_doc_nom_new(){ $db=DbSingleton::getDb();
        $r=$db->query("select max(doc_nom) as doc_nom from J_MOVING where oper_status='30' limit 0,1;");$doc_nom=0+$db->result($r,0,"doc_nom")+1;
        return $doc_nom;
    }

    function newJmovingCard($type_id){$db=DbSingleton::getDb();session_start();$user_id=$_SESSION["media_user_id"];
        $r=$db->query("select max(id) as mid from J_MOVING;");$jmoving_id=0+$db->result($r,0,"mid")+1;
        $doc_nom=$this->get_df_doc_nom_new();
        $db->query("insert into J_MOVING (`id`,`type_id`,`prefix`,`doc_nom`,`user_id`,`data`) values ('$jmoving_id','$type_id','$this->prefix_new','$doc_nom','$user_id',CURDATE());");
        return $jmoving_id;
    }

    function getJmovingName($jmoving_id){$db=DbSingleton::getDb();
        $r=$db->query("select * from J_MOVING where id='$jmoving_id' limit 0,1;");
        $prefix=$db->result($r,0,"prefix");
        $doc_nom=$db->result($r,0,"doc_nom");
        return $prefix."-".$doc_nom;
    }

    function getJmovingStatusId($jmoving_id){$db=DbSingleton::getDb();$status_jmoving=0;
        $r=$db->query("select status_jmoving from J_MOVING where id='$jmoving_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){	$status_jmoving=$db->result($r,0,"status_jmoving");}
        return $status_jmoving;
    }

    function checkJmovingStructure($jmoving_id) {$db = DbSingleton::getDb();
        $r=$db->query("select * from J_MOVING_STR where jmoving_id='$jmoving_id';"); $n=$db->num_rows($r);
        $n>0 ? $result=true : $result=false;
        return $result;
    }

    function statusStorage($user_id,$storage_id,$status_jmoving,$jmoving_id) {$db=DbSingleton::getDb();require_once RD.'/lib/users_class.php'; $users=new users; $super_user=$users->getSuperUser($user_id);
        //склад призначення
        $r=$db->query("select * from media_users_storage where user_id='$user_id' and storage_id='$storage_id';"); $n=$db->num_rows($r);
        $n>0 ? $status=true : $status=false;
        //склад відбору
        if (!$status) {
            $r=$db->query("select storage_id_from from J_MOVING_STR where jmoving_id=$jmoving_id limit 1;");
            $storage_id_from=$db->result($r,0,"storage_id_from");
            $r2=$db->query("select * from media_users_storage where user_id='$user_id' and storage_id='$storage_id_from';"); $n=$db->num_rows($r2);
            if ($n>0) $status=true; else $status=false;
        }
        //автор
        $rj=$db->query("select user_id from J_MOVING where id=$jmoving_id limit 1;");
        $j_user=$db->result($rj,0,"user_id");
        if ($j_user==$user_id) $status=true;
        //адміністратор
        if ($super_user) $status=true;
        return $status;
    }

    function show_jmoving_list($press = null){$db=DbSingleton::getDb();$gmanual=new gmanual;$income=new income;session_start();$media_user_id=$_SESSION["media_user_id"];
        $r=$db->query("select j.*, s.name as storage_name, sc.storage_id, sc.`cell_value` from J_MOVING j
            left outer join STORAGE s on s.id=j.storage_id_to
            left outer join STORAGE_CELLS sc on sc.id=j.cell_id_to
            left outer join T_POINT_STORAGE t on t.storage_id=j.storage_id_to
            left outer join manual man on man.id=j.status_jmoving
        order by man.mid asc, j.data desc, j.doc_nom desc, j.id desc limit 0,500;");$n=$db->num_rows($r);$list="";

        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $type_id=$db->result($r,$i-1,"type_id");$type_name="<i class='fa fa-inbox'></i> Внутрішнє переміщення";
            if ($type_id==1){$type_name="<i class='fa fa-truck'></i> Між складами";}
            $prefix=$db->result($r,$i-1,"prefix");
            $doc_nom=$db->result($r,$i-1,"doc_nom");
            $storage_id_to=$db->result($r,$i-1,"storage_id_to");
            if ($storage_id_to==0){$storage_id_to=$db->result($r,$i-1,"storage_id");}
            $storage_name=$db->result($r,$i-1,"storage_name");
            if ($storage_name==""){$storage_name=$income->getStorageName($storage_id_to);}
            //$cells_id_to=$db->result($r,$i-1,"cells_id_to");
            $cell_value=$db->result($r,$i-1,"cell_value");
            $data=$db->result($r,$i-1,"data");
            $user_id=$db->result($r,$i-1,"user_id");
            $user_name=$this->getMediaUserName($user_id);
            $status=$db->result($r,$i-1,"status_jmoving");
            $st=$db->result($r,$i-1,"status"); if ($st==0) $status=106;
            $status_jmoving=$gmanual->get_gmanual_caption($status);
            //$oper_status=$gmanual->get_gmanual_caption($db->result($r,$i-1,"oper_status"));
            $function="showJmovingCard(\"$id\")";
            if ($type_id==0){$function="showJmovingCardLocal(\"$id\")";}
            if (!$press) $statud=$this->checkJmovingStructure($id); else $statud=true;
            if ($statud) {
                if ($this->statusStorage($media_user_id,$storage_id_to,$status,$id)) {
                    $list.="<tr style='cursor:pointer' onClick='$function'>
                        <td>$id</td>
                        <td>$type_name</td>
                        <td>$prefix - $doc_nom</td>
                        <td align='center'>$data</td>
                        <td>$storage_name $cell_value</td>
                        <td>$user_name</td>
                        <td>$status_jmoving</td>
                    </tr>";
                }
            }
        }
        return $list;
    }

    function filterJmovingList($name,$data_from,$data_to,$status_jmoving){$db=DbSingleton::getDb();$gmanual=new gmanual;$income=new income;session_start();$ses_tpoint_id=$_SESSION["media_tpoint_id"];$media_user_id=$_SESSION["media_user_id"];
        $press=false;$where_filter="";
        $where=" and (t.tpoint_id='$ses_tpoint_id' or j.user_id=$media_user_id) ";
        if ($media_user_id==1 || $media_user_id==7){$where="";}
        if ($name>0 && $name!=""){$where_filter.=" and j.id='$name'";}
        if ($data_from>0 && $data_from!=""){$where_filter.=" and j.data>=$data_from ";}
        if ($data_to>0 && $data_to!=""){$where_filter.=" and j.data<=$data_to ";}
        if ($status_jmoving>0 && $status_jmoving!=""){$where_filter.=" and j.status_jmoving='$status_jmoving'";}
        $r=$db->query("select j.*, s.name as storage_name, sc.storage_id, sc.`cell_value` from J_MOVING j
            left outer join STORAGE s on s.id=j.storage_id_to
            left outer join STORAGE_CELLS sc on sc.id=j.cell_id_to
            left outer join T_POINT_STORAGE t on t.storage_id=j.storage_id_to
            left outer join manual man on man.id=j.status_jmoving
        where j.id>0 $where $where_filter order by man.mid asc, j.data desc, j.doc_nom desc, j.id desc limit 0,500;");$n=$db->num_rows($r);$list="";

        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $type_id=$db->result($r,$i-1,"type_id");$type_name="<i class='fa fa-inbox'></i> Внутрішнє переміщення";
            if ($type_id==1){$type_name="<i class='fa fa-truck'></i> Між складами";}
            $prefix=$db->result($r,$i-1,"prefix");
            $doc_nom=$db->result($r,$i-1,"doc_nom");
            $storage_id_to=$db->result($r,$i-1,"storage_id_to");
            if ($storage_id_to==0){$storage_id_to=$db->result($r,$i-1,"storage_id");}
            $storage_name=$db->result($r,$i-1,"storage_name");
            if ($storage_name==""){$storage_name=$income->getStorageName($storage_id_to);}
            //$cells_id_to=$db->result($r,$i-1,"cells_id_to");
            $cell_value=$db->result($r,$i-1,"cell_value");
            $data=$db->result($r,$i-1,"data");
            $user_name=$this->getMediaUserName($db->result($r,$i-1,"user_id"));
            $status=$db->result($r,$i-1,"status_jmoving");
            $st=$db->result($r,$i-1,"status"); if ($st==0) $status=106;
            $status_jmoving=$gmanual->get_gmanual_caption($status);
            //$oper_status=$gmanual->get_gmanual_caption($db->result($r,$i-1,"oper_status"));
            $function="showJmovingCard(\"$id\")";
            if ($type_id==0){$function="showJmovingCardLocal(\"$id\")";}
            if (!$press) $statud=$this->checkJmovingStructure($id); else $statud=true;

            if ($statud) {
                $list.="<tr style='cursor:pointer' onClick='$function'>
                    <td>$id</td>
                    <td>$type_name</td>
                    <td>$prefix - $doc_nom</td>
                    <td align='center'>$data</td>
                    <td>$storage_name $cell_value</td>
                    <td>$user_name</td>
                    <td>$status_jmoving</td>
                </tr>";
            }
        }
        return $list;
    }

    function preNewJmovingCard(){
        $form="";$form_htm=RD."/tpl/jmoving_select_type_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        return $form;
    }

    function getKoursUSD() {$db=DbSingleton::getDb();
        $r=$db->query("select kours_value from J_KOURS where cash_id=2 and in_use=1 limit 0,1;");
        $usd=number_format($db->result($r,0,"kours_value"), 2, '.', '');
        return $usd;
    }

    function getPriceArt($art_id) {$db=DbSingleton::getTokoDb();
        $r=$db->query("select price_1 from T2_ARTICLES_PRICE_RATING where art_id='$art_id' limit 0,1;");
        $price=$db->result($r,0,"price_1");
        return $price;
    }

    function getJMovingFullPrice($jmoving_id) {$db=DbSingleton::getDb(); $summ=0;
        $r=$db->query("select * from J_MOVING_STR where jmoving_id='$jmoving_id';"); $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $art_id=$db->result($r,$i-1,"art_id");
            $amount=$db->result($r,$i-1,"amount");
            $koursUSD=$this->getKoursUSD();
            $price=$this->getPriceArt($art_id);
            $price_art=$amount*$price*$koursUSD;
            $summ+=$price_art;
        }
        return $summ;
    }

    function showJmovingCard($jmoving_id){$db=DbSingleton::getDb();session_start();$user_id=$_SESSION["media_user_id"];$user_name=$_SESSION["user_name"];
        $prefix=""; $doc_nom=0; $data_accepting=$data_accepted=$type_name="";
        $form="";$form_htm=RD."/tpl/jmoving_card.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select * from J_MOVING j where j.id='$jmoving_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==0){$form_htm=RD."/tpl/access_deny.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);} }
        if ($n==1){
            $type_id=$db->result($r,0,"type_id");if ($type_id==1){$type_name="Між складами";}
            $prefix=$db->result($r,0,"prefix");
            $doc_nom=$db->result($r,0,"doc_nom");
            $user_use=$db->result($r,0,"user_use");
            $data_create=$db->result($r,0,"time_stamp");
            $user_accepting=$db->result($r,0,"user_accepting"); $user_accepting=$this->getMediaUserName($user_accepting);
            $user_accepted=$db->result($r,0,"user_accepted"); $user_accepted=$this->getMediaUserName($user_accepted);
            $user_data_accepting=$db->result($r,0,"user_data_accepting");
            $user_data_accepted=$db->result($r,0,"user_data_accepted");
            if ($user_accepting!="") $data_accepting="Приймається: $user_accepting, $user_data_accepting";
            if ($user_accepted!="") $data_accepted="Прийнято: $user_accepted, $user_data_accepted";

            if ($user_id!=$user_use && $user_use>0){
                $form_htm=RD."/tpl/jmoving_use_deny.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
                $form=str_replace("{user_name}",$this->getMediaUserName($user_use),$form);
            }
            if ($user_id==$user_use || $user_use==0){
                $weight=$db->result($r,0,"weight");
                $volume=$db->result($r,0,"volume");
                $comment=$db->result($r,0,"comment");
                $storage_id_to=$db->result($r,0,"storage_id_to");
                $cell_use=$db->result($r,0,"cell_use");
                $cell_id_to=$db->result($r,0,"cell_id_to"); $cells_show="hidden";if ($cell_use==1){$cells_show="";}
                $data=$db->result($r,0,"data");if ($data=="0000-00-00"){$data="";}
                $status_jmoving=$db->result($r,0,"status_jmoving");
                $oper_status=$db->result($r,0,"oper_status");
                if ($oper_status==31){
                    $form=str_replace("{hide_new_row_button}"," disabled style=\"visibility:hidden;\"",$form);
                    $form=str_replace("{oper_disabled}"," disabled",$form);
                }
                $form=str_replace("{oper_disabled}","",$form);
                $form=str_replace("{hide_new_row_button}","",$form);

                if ($status_jmoving==106) $disabled106=" readonly "; else $disabled106="";
                $form=str_replace("{disabled106}",$disabled106,$form);

                $disabledHide=""; if ($status_jmoving==106){$disabledHide="disabled style='display:none'";}
                $form=str_replace("{disabledHide}",$disabledHide,$form);
                $form=str_replace("{jmoving_id}",$jmoving_id,$form);
                $form=str_replace("{data}",$data,$form);
                $form=str_replace("{data_create}",$data_create,$form);
                $form=str_replace("{type_id}",$type_id,$form);
                $form=str_replace("{type_name}",$type_name,$form);
                $form=str_replace("{weight}",$weight,$form);
                $form=str_replace("{volume}",$volume,$form);
                $form=str_replace("{comment}",$comment,$form);
                $form=str_replace("{storage_list}",$this->showStorageSelectList($storage_id_to),$form);
                $form=str_replace("{cells_show}",$cells_show,$form);
                $form=str_replace("{data_accepting}",$data_accepting,$form);
                $form=str_replace("{data_accepted}",$data_accepted,$form);
                $form=str_replace("{storage_cells_list}",$this->showStorageCellsSelectList($storage_id_to,$cell_id_to)[0],$form);

                list($jmovingChildsList,$kol_art_str)=$this->showJmovingStrList($jmoving_id,$oper_status,$storage_id_to);
                $form=str_replace("{jmovingChildsList}",$jmovingChildsList,$form);
                $storage_to_disabled=""; if ($status_jmoving!=44){$storage_to_disabled=" disabled";}
                if ($kol_art_str>0){$storage_to_disabled=" disabled"; }
                $form=str_replace("{storage_to_disabled}",$storage_to_disabled,$form);
                $form=str_replace("{my_user_id}",$user_id,$form);
                $form=str_replace("{my_user_name}",$user_name,$form);

                list($kol_comments,$label_comments)=$this->labelCommentsCount($jmoving_id);
                $form=str_replace("{labelCommentsCount}",$label_comments,$form);
                list($kol_art_unknown,$label_art_unknown)=$this->labelArtEmptyCount($jmoving_id,0);
                $form=str_replace("{labelArticlesUnKnownCount}",$label_art_unknown,$form);
                //$storage_count=0;
                $storage_count=$this->loadJmovingStorageCount($jmoving_id);
                $form=str_replace("{labelArticlesUnKnownStorageCount}",$storage_count,$form);

                if ($status_jmoving==48 || $status_jmoving==57) $print_allow=""; else $print_allow="not-active";
                //status В дорозі
                $disabled48="disabled"; if ($this->checkStorselAllStatus($jmoving_id)==1 and ($status_jmoving<48 || $status_jmoving==107)){$disabled48="";}
                //if ($this->checkStorselAllStatus($jmoving_id)==1 and $status_jmoving) $disabled48="";
                $form=str_replace("{disabled48}",$disabled48,$form);
                $form=str_replace("{print_allow}",$print_allow,$form);
                $disabled49="disabled hidden"; if ($status_jmoving==48 || $status_jmoving==49){$disabled49="";}
                $form=str_replace("{disabled49}",$disabled49,$form);

                $disabled_user="disabled hidden"; if ($this->getJmovingAccess($user_id,$storage_id_to)){$disabled_user="";}
                $form=str_replace("{disabled_user}",$disabled_user,$form);

                if ($status_jmoving>44) $disabled_row="disabled hidden"; else $disabled_row="";
                $form=str_replace("{disabled_row}",$disabled_row,$form);

                $disabled100=""; if ($status_jmoving==44 || $status_jmoving>=48){$disabled100="disabled style='display:none'";}
                $form=str_replace("{disabled100}",$disabled100,$form);

                $form=str_replace("{jmoving_fullprice}",number_format($this->getJMovingFullPrice($jmoving_id), 2, '.', '')." грн",$form);

                $this->setJmovingCardUserAccess($jmoving_id,$user_id);
            }
        }
        return array($form,$prefix."-".$doc_nom);
    }

    function getJmovingAccess($user_id,$storage_id) {$db=DbSingleton::getDb();
        $users=new users; $super_user=$users->getSuperUser($user_id);
        $r=$db->query("select * from media_users_storage where user_id='$user_id' and storage_id='$storage_id';");$n=$db->num_rows($r);
        if ($n>0 || $super_user) return true; else return false;
    }

    function cancelJmoving($jmoving_id) {$db=DbSingleton::getDb(); $dbt=DbSingleton::getTokoDb();$answer=0;$err="Помилка збереження даних!";$select_id=0;
        $r=$db->query("select * from J_MOVING j where j.id='$jmoving_id' limit 0,1;"); $n=$db->num_rows($r);
        if ($n>0) {
            $r=$db->query("select * from J_MOVING_STR where jmoving_id='$jmoving_id';"); $n=$db->num_rows($r);
            for ($i=1;$i<=$n;$i++){
                $art_id=$db->result($r,$i-1,"art_id");
                $select_id=$db->result($r,$i-1,"select_id");

                $rstr=$db->query("select * from J_SELECT_STR where select_id='$select_id' and art_id='$art_id';"); $nstr=$db->num_rows($rstr);
                for ($j=1;$j<=$nstr;$j++){
                    $storage_id_from=$db->result($rstr,$j-1,"storage_id_from");
                    $cell_id_from=$db->result($rstr,$j-1,"cell_id_from");
                    $count = floatval($db->result($rstr,$j-1,"amount")) - floatval($db->result($rstr,$j-1,"amount_bug"));
                    //1
                    $rt1=$dbt->query("select * from T2_ARTICLES_STRORAGE where art_id='$art_id' and storage_id='$storage_id_from' limit 1;"); $nt1=$dbt->num_rows($rt1);
                    if ($nt1>0) {
                        $amount=$dbt->result($rt1,0,"AMOUNT");
                        $amount_res=$dbt->result($rt1,0,"RESERV_AMOUNT");
                        $amount_new=intval($amount)+intval($count);
                        $amount_res_new=intval($amount_res)-intval($count);
                        $dbt->query("update T2_ARTICLES_STRORAGE set AMOUNT='$amount_new', RESERV_AMOUNT='$amount_res_new' where ART_ID='$art_id' and STORAGE_ID='$storage_id_from' limit 1;");
                    }
                    //2
                    $rt2=$dbt->query("select * from T2_ARTICLES_STRORAGE_CELLS where art_id='$art_id' and storage_id='$storage_id_from' and STORAGE_CELLS_ID='$cell_id_from' limit 1;"); $nt2=$dbt->num_rows($rt2);
                    if ($nt2>0) {
                        $amount=$dbt->result($rt2,0,"AMOUNT");
                        $amount_res=$dbt->result($rt2,0,"RESERV_AMOUNT");
                        $amount_new=intval($amount)+intval($count);
                        $amount_res_new=intval($amount_res)-intval($count);
                        $dbt->query("update T2_ARTICLES_STRORAGE_CELLS set AMOUNT='$amount_new', RESERV_AMOUNT='$amount_res_new' where ART_ID='$art_id' and STORAGE_ID='$storage_id_from' and STORAGE_CELLS_ID='$cell_id_from' limit 1;");
                    }
                }
            }
            //3
            $db->query("update J_MOVING_SELECT_STR set status=0 where jmoving_id='$jmoving_id';");
            //4
            $db->query("update J_SELECT_STR set status=0 where select_id='$select_id';");
            $db->query("update J_SELECT set status=0 where parrent_doc_id='$jmoving_id';");
            //5
            $db->query("update J_MOVING set status=0, status_jmoving=106 where id='$jmoving_id';");
            $answer=1; $err="";
        }
        return array($answer,$err);
    }

    function showJmovingCardLocal($jmoving_id){$db=DbSingleton::getDb();session_start();$user_id=$_SESSION["media_user_id"];$user_name=$_SESSION["user_name"];
        $prefix=""; $doc_nom=0;
        $form="";$form_htm=RD."/tpl/jmoving_card_local.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select * from J_MOVING j where j.id='$jmoving_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==0){$form_htm=RD."/tpl/access_deny.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);} }
        if ($n==1){
            $type_id=$db->result($r,0,"type_id");$type_name="Внутрішнє переміщення";
            $prefix=$db->result($r,0,"prefix");
            $doc_nom=$db->result($r,0,"doc_nom");
            $user_use=$db->result($r,0,"user_use");
            $data_create=$db->result($r,0,"time_stamp");
            if ($user_id!=$user_use && $user_use>0){
                $form_htm=RD."/tpl/jmoving_use_deny.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
                $form=str_replace("{user_name}",$this->getMediaUserName($user_use),$form);
            }
            if ($user_id==$user_use || $user_use==0){
                $weight=$db->result($r,0,"weight");
                $volume=$db->result($r,0,"volume");
                $comment=$db->result($r,0,"comment");

                $storage_id_to=$db->result($r,0,"storage_id_to");
                $data=$db->result($r,0,"data");if ($data=="0000-00-00"){$data="";}
                $status_jmoving=$db->result($r,0,"status_jmoving");
                $oper_status=$db->result($r,0,"oper_status");
                if ($oper_status==31){
                    $form=str_replace("{hide_new_row_button}"," disabled style=\"visibility:hidden;\"",$form);
                    $form=str_replace("{oper_disabled}"," disabled",$form);
                }
                $form=str_replace("{oper_disabled}","",$form);
                $form=str_replace("{hide_new_row_button}","",$form);
                $form=str_replace("{jmoving_id}",$jmoving_id,$form);
                $form=str_replace("{data}",$data,$form);
                $form=str_replace("{data_create}",$data_create,$form);
                $form=str_replace("{type_id}",$type_id,$form);
                $form=str_replace("{type_name}",$type_name,$form);
                $form=str_replace("{weight}",$weight,$form);
                $form=str_replace("{volume}",$volume,$form);
                $form=str_replace("{comment}",$comment,$form);
                $form=str_replace("{storage_list}",$this->showStorageSelectList($storage_id_to,1),$form);
                list($jmovingChildsList,$kol_art_str)=$this->showJmovingStrLocalList($jmoving_id,$oper_status);
                $form=str_replace("{jmovingChildsList}",$jmovingChildsList,$form);

                $storage_to_disabled=""; if ($status_jmoving!=44){$storage_to_disabled=" disabled";}
                if ($kol_art_str>0){$storage_to_disabled=" disabled"; }
                $form=str_replace("{storage_to_disabled}",$storage_to_disabled,$form);
                $form=str_replace("{my_user_id}",$user_id,$form);
                $form=str_replace("{my_user_name}",$user_name,$form);

                list($kol_comments,$label_comments)=$this->labelCommentsCount($jmoving_id);
                $form=str_replace("{labelCommentsCount}",$label_comments,$form);
                list($kol_art_unknown,$label_art_unknown)=$this->labelArtEmptyCount($jmoving_id,0);
                $form=str_replace("{labelArticlesUnKnownCount}",$label_art_unknown,$form);
                $disabled48="disabled"; if ($this->checkJmovingSelectAllStatus($jmoving_id,47)==1 && $status_jmoving!=57){$disabled48="";}
                $form=str_replace("{disabled48}",$disabled48,$form);
                $disabled49="disabled hidden"; if ($this->checkJmovingSelectAllStatus($jmoving_id,48)==1 && $status_jmoving!=57){$disabled49="";}
                $form=str_replace("{disabled49}",$disabled49,$form);

                $this->setJmovingCardUserAccess($jmoving_id,$user_id);
            }
        }
        return array($form,$prefix."-".$doc_nom);
    }

    function closeJmovingCard($jmoving_id){session_start();$user_id=$_SESSION["media_user_id"];
        $this->unsetJmovingCardUserAccess($jmoving_id,$user_id);
        $answer=1;
        return $answer;
    }

    function setJmovingCardUserAccess($jmoving_id,$user_id){$db=DbSingleton::getDb();
        if($jmoving_id>0 && $user_id>0){
            $db->query("update J_MOVING set user_use='$user_id' where id='$jmoving_id';");
        }
        return;
    }

    function unsetJmovingCardUserAccess($jmoving_id,$user_id){$db=DbSingleton::getDb();
        if($jmoving_id>0 && $user_id>0){
            $db->query("update J_MOVING set user_use='0' where id='$jmoving_id';");
        }
        return;
    }

    function checkStorselAllStatus($jmoving_id){$db=DbSingleton::getDb();$ex=1;
        $r=$db->query("select id, status_select from J_SELECT where parrent_doc_type_id='1' and parrent_doc_id='$jmoving_id' and status='1';");$n=$db->num_rows($r);
        if ($n==0){$ex=0;}
        for ($i=1;$i<=$n;$i++){
            $status_select=$db->result($r,$i-1,"status_select");
            if ($status_select!=85){$ex=0; $i=$n+1;}
        }
        return $ex;
    }

    function checkJmovingSelectAllStatus($jmoving_id,$statusJmoving){$db=DbSingleton::getDb();$ex=1;
        $r=$db->query("select id, status_jmoving from J_MOVING_SELECT where jmoving_id='$jmoving_id' and status='1';");$n=$db->num_rows($r);
        if ($n==0){$ex=0;}
        for ($i=1;$i<=$n;$i++){
            $status_jmoving=$db->result($r,$i-1,"status_jmoving");
            if ($status_jmoving!=$statusJmoving){$ex=0; $i=$n+1;}
        }
        $r=$db->query("select id, status_jmoving from J_MOVING where id='$jmoving_id' and status='1' limit 0,1;");$n=$db->num_rows($r);
        if ($n==0){$ex=0;}
        if ($n==1){
            $status_jmoving=$db->result($r,0,"status_jmoving");
            if ($status_jmoving==44){
                $ex=0;//$i=$n+1;
            }
        }
        return $ex;
    }

    function showJmovingStrList($jmoving_id,$oper_status,$storage_id_to){$db=DbSingleton::getDb();$slave=new slave;$cat=new catalogue;
    $list=""; $amount_barcodes=$amount_barcodes_noscan=$amount_bug=0;
    if ($oper_status==""){$oper_status=30;}
        $r=$db->query("select * from J_MOVING_STR where jmoving_id='$jmoving_id' order by id asc;");$n=$db->num_rows($r);$kl_rw=$n;$sum_weight=0;$sum_volume=0;
        for ($i=1;$i<=$kl_rw;$i++){
            $id="";$art_id="";$article_nr_displ="";$brand_id="";$brand_name="";$amount="";$storage_id_from="";$storage_name_from="";$max_stok="";$status_jmoving=0;
            if ($i<=$n){
                $id=$db->result($r,$i-1,"id");
                $art_id=$db->result($r,$i-1,"art_id");
                $article_nr_displ=$db->result($r,$i-1,"article_nr_displ");
                $brand_id=$db->result($r,$i-1,"brand_id");$brand_name=$cat->getBrandName($brand_id);
                $amount=$db->result($r,$i-1,"amount");
                $amount_barcodes=$db->result($r,$i-1,"amount_barcodes");
                $amount_barcodes_noscan=$db->result($r,$i-1,"amount_barcodes_noscan");
                $amount_bug=$db->result($r,$i-1,"amount_bug");
                $storage_id_from=$db->result($r,$i-1,"storage_id_from");
                $storage_name_from=$this->getStorageName($storage_id_from);
                //$cell_id_from=$db->result($r,$i-1,"cell_id_from");
                //$cell_name_from=$this->getStorageCellName($cell_id_from);
                //list($stokRest,$reservRest)=$this->getArticleRestStorageCell($art_id,$storage_id_from,$cell_id_from);
                //list($stokRest,$reservRest)=$this->getArticleRestStorage($art_id,$storage_id_from);
                //$amountRest="сток: $stokRest | резерв: $reservRest";
                $status_jmoving=$db->result($r,$i-1,"status_jmoving");
            }
            if ($oper_status==30){
                list($weight,$volume)=$this->getArticleWightVolume($art_id);
                $sum_weight+=($weight*$amount); $sum_volume+=($volume*$amount);
                $disabled="";if ($status_jmoving!=44 && $status_jmoving>0){$disabled=" disabled";}
                if ($status_jmoving==106){$disabled=" disabled";}

                $list.="<tr id='strRow_$i'>
                    <td>$i<input type='hidden' id='idStr_$i' value='$id'></td>
                    <td style='min-width:140px;'><input type='hidden' id='artIdStr_$i' value='$art_id'>
                        <div class='input-group'>
                            <input class='form-control input-xs' type='text' readonly id='article_nr_displStr_$i' value='$article_nr_displ' placeholder='Індекс товару'>
                            <span class='input-group-btn'><button type='button' class='btn btn-xs btn-primary $disabled' $disabled onClick=\"showJmovingArticleSearchForm('$i','$art_id','$brand_id','$article_nr_displ','$jmoving_id','$storage_id_to');\"><i class=\"fa fa-bars\"></i></button> </span>
                        </div>
                    </td>
                    <td style='min-width:120px;'><input type='hidden' id='brandIdStr_$i' value='$brand_id'>
                        <input class='form-control input-xs' type='text' readonly id='brandNameStr_$i' value='$brand_name' placeholder='Бренд'>
                    </td>
                    <td style='min-width:120px;'>
                        <input type='hidden' id='storageIdFrom_$i' value='$storage_id_from'>
                        <input type='text' readonly id='storageNameFrom_$i' value='$storage_name_from' class='form-control input-xs'>
                    </td>
                    <td>
                        <input type='hidden' id='max_stock_$i' value='$max_stok'>
                        <div class='input-group'>
                            <input type='text' id='amountStr_$i' readonly value='$amount' class='form-control input-xs numberOnly' autocomplete='off'>
                            <span class='input-group-btn'> <button type='button' class='btn btn-xs btn-primary $disabled' $disabled onClick=\"showJmovingArticleAmountChange('$i','$id','$art_id','$amount');\"><i class=\"fa fa-bars\"></i></button></span>
                        </div>
                    </td>
                    <td></td>
                    <td></td>
                    <td><button class='btn btn-xs btn-default $disabled' $disabled onClick='dropJmovingStr(\"$i\",\"$jmoving_id\",\"$id\");'><i class='fa fa-times'></i></button></td>
                </tr>";
            }
            if ($oper_status==31){
                if ($article_nr_displ!=""){
                    $list.="<tr align='center'>
                        <td>$i</td>
                        <td align='left'>$article_nr_displ</td>
                        <td>$brand_name</td>
                        <td>$storage_name_from</td>
                        <td align='right'>".$slave->to_money($amount)."</td>
                        <td align='right'>".$slave->to_money($amount_barcodes+$amount_barcodes_noscan)."</td>
                        <td align='right'>".$slave->to_money($amount_bug)."</td>
                        <td></td>
                    </tr>";
                }
            }
        }
        if ($oper_status==30){
            $list="<input type='hidden' id='kol_row' value='$kl_rw'>
                <tr id='jmovingStrNewRow' class='hidden'>
                    <td>nom_i<input type='hidden' id='idStr_0' value=''></td>
                    <td style='min-width:140px;'><input type='hidden' id='artIdStr_0' value=''>
                        <div class='input-group'>
                            <input class='form-control input-xs' type='text' readonly id='article_nr_displStr_0' value='' placeholder='Індекс товару'>
                            <span class='input-group-btn'> <button type='button' class='btn btn-xs btn-primary' onClick=\"showJmovingArticleSearchForm('i_0','0','0','','$jmoving_id','$storage_id_to');\"><i class=\"fa fa-bars\"></i></button> </span>
                        </div>
                    </td>
                    <td style='min-width:120px;'><input type='hidden' id='brandIdStr_0' value=''>
                        <input class='form-control input-xs' type='text' readonly id='brandNameStr_0' value='' placeholder='Бренд'>
                    </td>
                    <td style='min-width:120px;'>
                        <input type='hidden' id='storageIdFrom_0' value='' >
                        <input type='text' readonly id='storageNameFrom_0' value='' class='form-control input-xs'>
                    </td>
                    <td>
                        <input type='hidden' id='max_stock_0' value=''>
                        <input type='text' id='amountStr_0' value='' readonly class='form-control input-xs numberOnly' autocomplete='off' maxlength=''  min='1' max=''>
                    </td>
                    <td></td>
                    <td></td>
                    <td><button class='btn btn-xs btn-default' onClick='dropIncomStr(\"i_0\",\"0\");'><i class='fa fa-times'></i></button></td>
                </tr>".$list;
        }
        if ($sum_weight!=0 && $sum_volume!=0 && $oper_status=='30'){
            $db->query("update J_MOVING set `weight`='$sum_weight', `volume`='$sum_volume' where id='$jmoving_id' and oper_status='30';");
        }
        return array($list,$n);
    }

    function saveJmovingCard($jmoving_id,$jmoving_op_id,$data,$storage_id_to,$cell_id_to,$comment,$kol_row,$idStr,$artIdStr,$article_nr_displStr,$brandIdStr,$storageIdFromStr,$cellIdFromStr,$amountStr){$db=DbSingleton::getDb();$slave=new slave;session_start();$user_id=$_SESSION["media_user_id"];$answer=0;$err="Помилка збереження даних!";
        $jmoving_id=$slave->qq($jmoving_id);$jmoving_op_id=$slave->qq($jmoving_op_id);$data=$slave->qq($data);$storage_id_to=$slave->qq($storage_id_to);$cell_id_to=$slave->qq($cell_id_to);
        $comment=$slave->qq($comment);
        if ($jmoving_id==0 || $jmoving_id==""){
            $r=$db->query("select max(id) as mid from J_MOVING;");$jmoving_id=0+$db->result($r,0,"mid")+1;
            $db->query("insert into J_MOVING (`id`,`jmoving_op_id`,`user_id`) values ('$jmoving_id','$jmoving_op_id','$user_id');");
        }
        if ($jmoving_id>0){
            $db->query("update J_MOVING set `data`='$data', `comment`='$comment', `storage_id_to`='$storage_id_to', `cell_id_to`='$cell_id_to' where `id`='$jmoving_id';");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function saveJmovingCardLocal($jmoving_id,$jmoving_op_id,$data,$storage_id_to,$comment,$kol_row,$idStr,$artIdStr,$cellIdToStr){$db=DbSingleton::getDb();$slave=new slave;session_start();$user_id=$_SESSION["media_user_id"];$answer=0;$err="Помилка збереження даних!";
        $jmoving_id=$slave->qq($jmoving_id);$jmoving_op_id=$slave->qq($jmoving_op_id);$data=$slave->qq($data);$storage_id_to=$slave->qq($storage_id_to);$comment=$slave->qq($comment);$kol_row=$slave->qq($kol_row);
        if ($jmoving_id==0 || $jmoving_id==""){
            $r=$db->query("select max(id) as mid from J_MOVING;");$jmoving_id=0+$db->result($r,0,"mid")+1;
            $db->query("insert into J_MOVING (`id`,`jmoving_op_id`,`user_id`) values ('$jmoving_id','$jmoving_op_id','$user_id');");
        }
        if ($jmoving_id>0){
            $db->query("update J_MOVING set `data`='$data', `comment`='$comment', `storage_id_to`='$storage_id_to' where `id`='$jmoving_id';");
            for($i=1;$i<=$kol_row;$i++){
                $idS=$idStr[$i]; $artIdS=$artIdStr[$i]; $cellIdToS=$cellIdToStr[$i];
                if (($idS=="" || $idS==0) && ($artIdS!="" && $artIdS>0)){
                    $r=$db->query("select max(id) as mid from J_MOVING_STR;");$idS=0+$db->result($r,0,"mid")+1;
                    $db->query("insert into J_MOVING_STR (`id`,`jmoving_id`) values ('$idS','$jmoving_id');");
                }
                if ($idS>0 && $artIdS!="" && $artIdS>0){
                    $db->query("update J_MOVING_STR set `cell_id_to`='$cellIdToS' where id='$idS' and jmoving_id='$jmoving_id';");
                }
            }
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function showJmovingStrLocalList($jmoving_id,$oper_status){$db=DbSingleton::getDb();$slave=new slave;$cat=new catalogue;$list="";if ($oper_status==""){$oper_status=30;}
        $r=$db->query("select * from J_MOVING_STR where jmoving_id='$jmoving_id' order by id asc;");$n=$db->num_rows($r);
        $kl_rw=$n;$sum_weight=0;$sum_volume=0;$storage_id_from=0;
        for ($i=1;$i<=$kl_rw;$i++){
            $id="";$art_id="";$article_nr_displ="";$brand_id="";$brand_name="";$amount="";$cell_id_from="";$cell_name_from="";$cell_name_to="";$max_stok="";$status_jmoving=0;$cell_to_select_list="";
            if ($i<=$n){
                $id=$db->result($r,$i-1,"id");
                $art_id=$db->result($r,$i-1,"art_id");
                $article_nr_displ=$db->result($r,$i-1,"article_nr_displ");
                $brand_id=$db->result($r,$i-1,"brand_id");$brand_name=$cat->getBrandName($brand_id);
                $amount=$db->result($r,$i-1,"amount");
                $storage_id_from=$db->result($r,$i-1,"storage_id_from");
                $cell_id_from=$db->result($r,$i-1,"cell_id_from");$cell_name_from=$this->getStorageCellName($cell_id_from);
                $cell_id_to=$db->result($r,$i-1,"cell_id_to");
                list($cell_to_select_list,$cs)=$this->showStorageCellsSelectList($storage_id_from,$cell_id_to);
                $cell_name_to=$this->getStorageCellName($cell_id_to);
                $status_jmoving=$db->result($r,$i-1,"status_jmoving");
            }
            if ($oper_status==30){
                list($weight,$volume)=$this->getArticleWightVolume($art_id);
                $sum_weight+=($weight*$amount); $sum_volume+=($volume*$amount);
                $disabled="";if ($status_jmoving!=44 && $status_jmoving>0){$disabled=" disabled";}
                $list.="<tr id='strRow_$i'>
                    <td>$i<input type='hidden' id='idStr_$i' value='$id'></td>
                    <td style='min-width:140px;'><input type='hidden' id='artIdStr_$i' value='$art_id'>
                        <!--<div class='input-group'>-->
                            <input class='form-control input-xs' type='text' readonly id='article_nr_displStr_$i' value='$article_nr_displ' placeholder='Індекс товару'>
                            <!--<span class='input-group-btn'> <button type='button' class='btn btn-xs btn-primary $disabled' $disabled onClick=\"showJmovingArticleLocalSearchForm('$i','$art_id','$brand_id','$article_nr_displ','$jmoving_id','$storage_id_from');\"><i class=\"fa fa-bars\"></i></button> </span>
                        </div>-->
                    </td>
                    <td style='min-width:120px;'><input type='hidden' id='brandIdStr_$i' value='$brand_id'>
                        <input class='form-control input-xs' type='text' readonly id='brandNameStr_$i' value='$brand_name' placeholder='Бренд'>
                    </td>
                    <td style='min-width:120px;'>
                        <input type='hidden' id='storageIdFrom_$i' value='$storage_id_from' >
                        <input type='hidden' id='cellIdFrom_$i' value='$cell_id_from' >
                        <input type='text' readonly id='cellNameFrom_$i' value='$cell_name_from' class='form-control input-xs'>
                    </td>
                    <td>
                        <input type='hidden' id='max_stock_$i' value='$max_stok'>
                        <div class='input-group'>
                            <input type='text' id='amountStr_$i' readonly value='$amount' class='form-control input-xs numberOnly' autocomplete='off'>
                            <span class='input-group-btn'> <button type='button' class='btn btn-xs btn-primary $disabled' $disabled onClick=\"showJmovingArticleAmountLocalChange('$i','$id','$art_id','$amount');\"><i class=\"fa fa-bars\"></i></button></span>
                        </div>
                    </td>
                    <td style='min-width:120px;'>
                        <select size='1' class='form-control input-xs $disabled' $disabled id='cellIdTo_$i' style='width:100%'>$cell_to_select_list</select>
                    </td>
                    <td><button class='btn btn-xs btn-default $disabled' $disabled onClick='dropJmovingLocalStr(\"$i\",\"$jmoving_id\",\"$id\");'><i class='fa fa-times'></i></button></td>
                </tr>";
            }
            if ($oper_status==31){
                if ($article_nr_displ!=""){
                    $list.="<tr align='center'>
                        <td>$i</td>
                        <td align='left'>$article_nr_displ</td>
                        <td>$brand_name</td>
                        <td>$cell_name_from</td>
                        <td align='right'>".$slave->to_money($amount)."</td>
                        <td>$cell_name_to</td>
                        <td></td>
                    </tr>";
                }
            }
        }
        if ($oper_status==30){
            $list="<input type='hidden' id='kol_row' value='$kl_rw'>
                <tr id='jmovingStrNewRow' class='hidden'>
                    <td>nom_i<input type='hidden' id='idStr_0' value=''></td>
                    <td style='min-width:140px;'><input type='hidden' id='artIdStr_0' value=''>
                        <div class='input-group'>
                            <input class='form-control input-xs' type='text' readonly id='article_nr_displStr_0' value='' placeholder='Індекс товару'>
                            <span class='input-group-btn'> <button type='button' class='btn btn-xs btn-primary' onClick=\"showJmovingArticleLocalSearchForm('i_0','0','0','','$jmoving_id','$storage_id_from');\"><i class=\"fa fa-bars\"></i></button> </span>
                        </div>
                    </td>
                    <td style='min-width:120px;'><input type='hidden' id='brandIdStr_0' value=''>
                        <input class='form-control input-xs' type='text' readonly id='brandNameStr_0' value='' placeholder='Бренд'>
                    </td>
                    <td style='min-width:120px;'>
                        <input type='hidden' id='storageIdFrom_0' value=''>
                        <input type='hidden' id='cellIdFrom_0' value=''>
                        <input type='text' readonly id='cellNameFrom_0' value='' class='form-control input-xs'>
                    </td>
                    <td>
                        <input type='hidden' id='max_stock_0' value=''>
                        <div class='input-group'>
                            <input type='text' id='amountStr_0' readonly value='' class='form-control input-xs numberOnly' autocomplete='off'>
                            <span class='input-group-btn'> <button type='button' class='btn btn-xs btn-primary' onClick=\"showJmovingArticleAmountLocalChange('i_0','0','','0');\"><i class=\"fa fa-bars\"></i></button></span>
                        </div>
                    </td>
                    <td style='min-width:120px;'>
                        <select size='1' class='form-control input-xs' id='cellIdTo_0' style='width:100%'></select>
                    </td>
                    <td><button class='btn btn-xs btn-default' onClick='dropJmovingLocalStr(\"i_0\",\"$jmoving_id\",\"0\");'><i class='fa fa-times'></i></button></td>
                </tr>".$list;
        }
        if ($sum_weight!=0 && $sum_volume!=0 && $oper_status=='30'){
            $db->query("update J_MOVING set `weight`='$sum_weight', `volume`='$sum_volume' where id='$jmoving_id' and oper_status='30';");
        }
        return array($list,$n);
    }

    function setArticleToJmoving($jmoving_id,$idStr,$artIdStr,$article_nr_displStr,$brandIdStr,$storageIdFromStr,$cellIdFromStr,$amountStr){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;$answer=0;$err="Змінилася кількість. Закрийте вікно введення даних і повторіть ще раз!";
        $jmoving_id=$slave->qq($jmoving_id); $rr_amount=$rr_reserv=$empty_kol=$idS=$weight=$volume=0;$label_empty="";
        if ($jmoving_id>0){
            $idS=$slave->qq($idStr);$artIdS=$slave->qq($artIdStr);$article_nr_displS=$slave->qq($article_nr_displStr);$brandIdS=$slave->qq($brandIdStr);$amountS=$slave->qq($amountStr);$storageIdFromS=$slave->qq($storageIdFromStr);$cellIdFromS=$slave->qq($cellIdFromStr);
            list($info,$max_moving,$rest_amount)=$this->showArticleRestStorageSelectText($artIdS,$jmoving_id,$amountS,$storageIdFromS);
            if ($amountS<=$max_moving && $rest_amount==0){$answer=0;$err="Змінилася кількість. Закрийте вікно введення даних і повторіть ще раз!";}
            if ($amountS>$max_moving && $rest_amount<=0){$answer=0;$err="Змінилася кількість. Закрийте вікно введення даних і повторіть ще раз!";}
            if ($amountS<=$max_moving && $rest_amount>0){
                $amountEx=0;
                $r=$db->query("select id,amount from J_MOVING_STR where jmoving_id='$jmoving_id' and art_id='$artIdS' and `storage_id_from`='$storageIdFromS' and status_jmoving='44' limit 0,1;");$n=$db->num_rows($r);
                if ($n==1){
                    $idS=$db->result($r,0,"id");
                    $amountEx=$db->result($r,0,"amount");
                }
                if ($idS=="" || $idS==0){
                    $r=$db->query("select max(id) as mid from J_MOVING_STR;");$idS=0+$db->result($r,0,"mid")+1;
                    $db->query("insert into J_MOVING_STR (`id`,`jmoving_id`) values ('$idS','$jmoving_id');");
                    $rr_reserv=0;$amountEx=0;
                }
                if ($idS>0){
                    if ($artIdS!="" && $artIdS>0 && $article_nr_displS!=""){
                        $amountEx+=$amountS;
                        $db->query("update J_MOVING_STR set `art_id`='$artIdS', `article_nr_displ`='$article_nr_displS', `brand_id`='$brandIdS', `amount`='$amountEx', `storage_id_from`='$storageIdFromS', `cell_id_from`='$cellIdFromS' where id='$idS' and jmoving_id='$jmoving_id';");
                        $db->query("update J_MOVING set status_jmoving='44' where id='$jmoving_id';");

                        list($weight,$volume,$empty_kol)=$this->updateJmovingWeightVolume($jmoving_id);
                        $rr=$dbt->query("select * from T2_ARTICLES_STRORAGE where ART_ID='$artIdS' and STORAGE_ID ='$storageIdFromS' limit 0,1;");$nr=$dbt->num_rows($rr);
                        if ($nr==1){
                            $rr_amount=$dbt->result($rr,0,"AMOUNT");
                            $rr_reserv=$dbt->result($rr,0,"RESERV_AMOUNT");
                        }
                        $rr_amount-=$amountS;$rr_reserv+=$amountS;
                        $dbt->query("update T2_ARTICLES_STRORAGE set AMOUNT='$rr_amount', `RESERV_AMOUNT`='$rr_reserv' where ART_ID='$artIdS' and STORAGE_ID ='$storageIdFromS';");
                    }
                }
                list($empty_kol,$label_empty)=$this->labelArtEmptyCount($jmoving_id,$empty_kol);
                $answer=1;$err="";
            }
        }
        return array($answer,$err,$idS,"сток: $rr_amount | резерв: $rr_reserv",$weight,$volume,$empty_kol,$label_empty);
    }

    function setArticleToJmovingLocal($jmoving_id,$idStr,$artIdStr,$article_nr_displStr,$brandIdStr,$storageId,$cell_from_move,$cell_to_move,$amountStr){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;$answer=0;$err="Змінилася кількість. Закрийте вікно введення даних і повторіть ще раз!";
        $jmoving_id=$slave->qq($jmoving_id); $rr_amount=$rr_reserv=$empty_kol=$idS=$weight=$volume=0;$label_empty="";
        if ($jmoving_id>0){
            $idS=$slave->qq($idStr);$artIdS=$slave->qq($artIdStr);$article_nr_displS=$slave->qq($article_nr_displStr);$brandIdS=$slave->qq($brandIdStr);$amountS=$slave->qq($amountStr);$storageIdS=$slave->qq($storageId);$cell_from_moveS=$slave->qq($cell_from_move);$cell_to_moveS=$slave->qq($cell_to_move);
            list($info,$max_moving,$rest_amount)=$this->showArticleRestStorageCellSelectText($artIdS,$jmoving_id,$amountS,$cell_from_moveS,$storageIdS);
            if ($amountS<=$max_moving && $rest_amount==0){$answer=0;$err="Змінилася кількість. Закрийте вікно введення даних і повторіть ще раз!";}
            if ($amountS>$max_moving && $rest_amount<=0){$answer=0;$err="Змінилася кількість. Закрийте вікно введення даних і повторіть ще раз!";}
            if ($amountS<=$max_moving && $rest_amount>0){
                $amountEx=0;
                $r=$db->query("select id,amount from J_MOVING_STR where jmoving_id='$jmoving_id' and art_id='$artIdS' and `storage_id_from`='$storageIdS' and `cell_id_from`='$cell_from_moveS' and `cell_id_to`='$cell_to_moveS' and status_jmoving='44' limit 0,1;");$n=$db->num_rows($r);
                if ($n==1){
                    $idS=$db->result($r,0,"id");
                    $amountEx=$db->result($r,0,"amount");
                }
                if ($idS=="" || $idS==0){
                    $r=$db->query("select max(id) as mid from J_MOVING_STR;");$idS=0+$db->result($r,0,"mid")+1;
                    $db->query("insert into J_MOVING_STR (`id`,`jmoving_id`) values ('$idS','$jmoving_id');");
                    $rr_reserv=0;$amountEx=0;
                }
                if ($idS>0){
                    if ($artIdS!="" && $artIdS>0 && $article_nr_displS!=""){
                        $amountEx+=$amountS;
                        $db->query("update J_MOVING_STR set `art_id`='$artIdS', `article_nr_displ`='$article_nr_displS', `brand_id`='$brandIdS', `amount`='$amountEx', `storage_id_from`='$storageId', `storage_id_to`='$storageId', `cell_id_from`='$cell_from_moveS', `cell_id_to`='$cell_to_moveS' where id='$idS' and jmoving_id='$jmoving_id';");
                        $db->query("update J_MOVING set status_jmoving='44' where id='$jmoving_id';");

                        list($weight,$volume,$empty_kol)=$this->updateJmovingWeightVolume($jmoving_id);

                        //STORAGE SET RESERV
                        $rr=$dbt->query("select * from T2_ARTICLES_STRORAGE where ART_ID='$artIdS' and STORAGE_ID ='$storageIdS' limit 0,1;");$nr=$dbt->num_rows($rr);
                        if ($nr==1){
                            $rr_amount=$dbt->result($rr,0,"AMOUNT");
                            $rr_reserv=$dbt->result($rr,0,"RESERV_AMOUNT");
                        }
                        $rr_amount-=$amountS;$rr_reserv+=$amountS;
                        $dbt->query("update T2_ARTICLES_STRORAGE set AMOUNT='$rr_amount', `RESERV_AMOUNT`='$rr_reserv' where ART_ID='$artIdS' and STORAGE_ID ='$storageIdS';");

                        //CELL SET RESERV
                        $rr=$dbt->query("select * from T2_ARTICLES_STRORAGE_CELLS where ART_ID='$artIdS' and STORAGE_ID ='$storageIdS' and STORAGE_CELLS_ID='$cell_from_moveS' limit 0,1;");$nr=$dbt->num_rows($rr);
                        if ($nr==1){
                            $rr_amount=$dbt->result($rr,0,"AMOUNT");
                            $rr_reserv=$dbt->result($rr,0,"RESERV_AMOUNT");
                        }
                        $rr_amount-=$amountS;$rr_reserv+=$amountS;
                        $dbt->query("update T2_ARTICLES_STRORAGE_CELLS set AMOUNT='$rr_amount', `RESERV_AMOUNT`='$rr_reserv' where ART_ID='$artIdS' and STORAGE_ID ='$storageIdS' and STORAGE_CELLS_ID='$cell_from_moveS';");
                    }
                }
                list($empty_kol,$label_empty)=$this->labelArtEmptyCount($jmoving_id,$empty_kol);
                $answer=1;$err="";
            }
        }
        return array($answer,$err,$idS,"сток: $rr_amount | резерв: $rr_reserv",$weight,$volume,$empty_kol,$label_empty);
    }

    function changeArticleToJmoving($jmoving_id,$jmoving_str_id,$amount_change){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;$answer=0;$err="Помилка збереження даних!";
        $jmoving_id=$slave->qq($jmoving_id);$weight=$volume=0;
        if ($jmoving_id>0){
            $jmoving_str_id=$slave->qq($jmoving_str_id);$amount_change=$slave->qq($amount_change);
            $r=$db->query("select amount, art_id, storage_id_from from J_MOVING_STR where jmoving_id='$jmoving_id' and id='$jmoving_str_id' and status_jmoving='44' limit 0,1;");$n=$db->num_rows($r);
            if ($n==1){
                $amountEx=$db->result($r,0,"amount");
                $art_id=$db->result($r,0,"art_id");
                $storage_id_from=$db->result($r,0,"storage_id_from");
                $amountS=$amount_change;
                list($info,$max_moving,$rest_amount)=$this->showArticleRestStorageSelectText($art_id,$jmoving_id,$amountS);
                if ($amountS<=$max_moving && $rest_amount==0){$answer=0;$err="Кількість для переміщення вже більша за залишок!";}
                if ($amountS>$max_moving && $rest_amount<=0){$answer=0;$err="Кількість для переміщення вже більша за залишок!";}
                if ($amountS<=($max_moving+$amountEx)){
                    $db->query("update J_MOVING_STR set `amount`='$amount_change' where id='$jmoving_str_id' and jmoving_id='$jmoving_id' limit 1;");
                    list($weight,$volume,$empty_kol)=$this->updateJmovingWeightVolume($jmoving_id);
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
            }
        }
        return array($answer,$err,$weight,$volume);
    }

    function changeArticleToJmovingLocal($jmoving_id,$jmoving_str_id,$amount_change){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;$answer=0;$err="Помилка збереження даних!";
        $jmoving_id=$slave->qq($jmoving_id);$weight=$volume=0;
        if ($jmoving_id>0){
            $jmoving_str_id=$slave->qq($jmoving_str_id);$amount_change=$slave->qq($amount_change);
            $r=$db->query("select amount, art_id, storage_id_from, cell_id_from from J_MOVING_STR where jmoving_id='$jmoving_id' and id='$jmoving_str_id' and status_jmoving='44' limit 0,1;");$n=$db->num_rows($r);
            if ($n==1){
                $amountEx=$db->result($r,0,"amount");
                $art_id=$db->result($r,0,"art_id");
                $storage_id_from=$db->result($r,0,"storage_id_from");
                $cell_id_from=$db->result($r,0,"cell_id_from");

                list($info,$max_moving,$rest_amount)=$this->showArticleRestStorageCellSelectText($art_id,$jmoving_id,$amount_change,$cell_id_from,$storage_id_from);

                if ($amount_change<=$max_moving && $rest_amount<0){$answer=0;$err="Змінилася кількість. Закрийте вікно введення даних і повторіть ще раз!";}
                if ($amount_change>$max_moving && $rest_amount<=0){$answer=0;$err="Змінилася кількість. Закрийте вікно введення даних і повторіть ще раз!";}
                if ($amount_change<=$max_moving && $rest_amount>=0){
                    $db->query("update J_MOVING_STR set `amount`='$amount_change' where id='$jmoving_str_id' and jmoving_id='$jmoving_id' limit 1;");
                    list($weight,$volume,$empty_kol)=$this->updateJmovingWeightVolume($jmoving_id);
                    $rr=$dbt->query("select * from T2_ARTICLES_STRORAGE where ART_ID='$art_id' and STORAGE_ID ='$storage_id_from' limit 0,1;");$nr=$dbt->num_rows($rr);
                    if ($nr==1){
                        $rr_amount=$dbt->result($rr,0,"AMOUNT");
                        $rr_reserv=$dbt->result($rr,0,"RESERV_AMOUNT");
                        $rr_amount=$rr_amount+$amountEx-$amount_change;
                        $rr_reserv=$rr_reserv-$amountEx+$amount_change;
                        $dbt->query("update T2_ARTICLES_STRORAGE set AMOUNT='$rr_amount', `RESERV_AMOUNT`='$rr_reserv' where ART_ID='$art_id' and STORAGE_ID ='$storage_id_from';");
                    }
                    $rr=$dbt->query("select * from T2_ARTICLES_STRORAGE_CELLS where ART_ID='$art_id' and STORAGE_ID ='$storage_id_from' and STORAGE_CELLS_ID ='$cell_id_from' limit 0,1;");$nr=$dbt->num_rows($rr);
                    if ($nr==1){
                        $rr_amount=$dbt->result($rr,0,"AMOUNT");
                        $rr_reserv=$dbt->result($rr,0,"RESERV_AMOUNT");
                        $rr_amount=$rr_amount+$amountEx-$amount_change;
                        $rr_reserv=$rr_reserv-$amountEx+$amount_change;
                        $dbt->query("update T2_ARTICLES_STRORAGE_CELLS set AMOUNT='$rr_amount', `RESERV_AMOUNT`='$rr_reserv' where ART_ID='$art_id' and STORAGE_ID ='$storage_id_from' and STORAGE_CELLS_ID ='$cell_id_from';");
                    }
                    $answer=1;$err="";
                }
            }
        }
        return array($answer,$err,$weight,$volume);
    }

    function dropJmovingStr($jmoving_id,$jmoving_str_id){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;$answer=0;$err="Помилка індексу";
        $jmoving_id=$slave->qq($jmoving_id);
        $r=$db->query("select oper_status, status_jmoving from J_MOVING where id='$jmoving_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $status_jmoving=$db->result($r,0,"status_jmoving");
            $oper_status=$db->result($r,0,"oper_status");
            if ($oper_status==30 && ($status_jmoving==44 ||$status_jmoving==45)){
                $r1=$db->query("select * from J_MOVING_STR where id='$jmoving_str_id' limit 0,1;");$n1=$db->num_rows($r1);
                if ($n1==1){
                    $status_jmoving_str=$db->result($r1,0,"status_jmoving");
                    if ($status_jmoving_str==44){
                        $art_id=$db->result($r1,0,"art_id");
                        $amount=$db->result($r1,0,"amount");
                        $storage_id_from=$db->result($r1,0,"storage_id_from");

                        $rs=$dbt->query("select * from T2_ARTICLES_STRORAGE where ART_ID='$art_id' and STORAGE_ID='$storage_id_from' limit 0,1;");$ns=$dbt->num_rows($rs);
                        if ($ns==1){
                            $reserv_amount_s=$dbt->result($rs,0,"RESERV_AMOUNT");
                            $amount_s=$dbt->result($rs,0,"AMOUNT");
                            $reserv_amount_s-=$amount;
                            $amount_s+=$amount;
                            $dbt->query("update T2_ARTICLES_STRORAGE set `AMOUNT`='$amount_s', `RESERV_AMOUNT`='$reserv_amount_s' where ART_ID='$art_id' and STORAGE_ID='$storage_id_from' limit 1;");
                            $db->query("delete from J_MOVING_STR where id='$jmoving_str_id' and jmoving_id='$jmoving_id' limit 1;");
                            $this->updateJmovingWeightVolume($jmoving_id);
                            $answer=1;$err="";
                        }
                    }else {$answer=0;$err="Видалення заблоковано. Відбір передано в роботу.";}
                }
            }else {$answer=0;$err="Видалення заблоковано. Переміщення передано в роботу.";}
        }
        return array($answer,$err);
    }

    function dropJmovingLocalStr($jmoving_id,$jmoving_str_id){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;$answer=0;$err="Помилка індексу";
        $jmoving_id=$slave->qq($jmoving_id);
        $r=$db->query("select oper_status, status_jmoving from J_MOVING where id='$jmoving_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $status_jmoving=$db->result($r,0,"status_jmoving");
            $oper_status=$db->result($r,0,"oper_status");
            if ($oper_status==30 && ($status_jmoving==44 ||$status_jmoving==45)){
                $r1=$db->query("select * from J_MOVING_STR where id='$jmoving_str_id' limit 0,1;");$n1=$db->num_rows($r1);
                if ($n1==1){
                    $status_jmoving_str=$db->result($r1,0,"status_jmoving");
                    if ($status_jmoving_str==44){
                        $art_id=$db->result($r1,0,"art_id");
                        $amount=$db->result($r1,0,"amount");
                        $storage_id_from=$db->result($r1,0,"storage_id_from");
                        $cell_id_from=$db->result($r1,0,"cell_id_from");

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
                        $db->query("delete from J_MOVING_STR where id='$jmoving_str_id' and jmoving_id='$jmoving_id' limit 1;");
                        $this->updateJmovingWeightVolume($jmoving_id);
                        $answer=1;$err="";
                    }else
                        {$answer=0;$err="Видалення заблоковано. Відбір передано в роботу.";}
                }
            }else
                {$answer=0;$err="Видалення заблоковано. Переміщення передано в роботу.";}
        }
        return array($answer,$err);
    }

    function updateJmovingWeightVolume($jmoving_id){$db=DbSingleton::getDb();$sum_weight=0;$sum_volume=0;$empty_kol=0;$art_ar=array();
        $r=$db->query("select art_id, amount from J_MOVING_STR where jmoving_id='$jmoving_id';");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $art_id=$db->result($r,$i-1,"art_id");
            $amount=$db->result($r,$i-1,"amount");
            list($weight,$volume)=$this->getArticleWightVolume($art_id);
            if ($weight==0 || $volume==0){
                if (!in_array($art_id,$art_ar)){
                    $empty_kol+=1;
                    array_push($art_ar,$art_id);
                }
            }
            if ($weight>0 && $amount>0){$sum_weight+=($weight*$amount);}
            if ($volume>0 && $amount>0){$sum_volume+=($volume*$amount);}
        }
        if ($n>0){
            $db->query("update J_MOVING set `weight`='$sum_weight', `volume`='$sum_volume' where id='$jmoving_id' and oper_status='30' and status='1';");
        }
        return array($sum_weight,$sum_volume,$empty_kol);
    }

    function makeJmovingCardFinish($jmoving_id){$answer=0;$err="";
        //$db=DbSingleton::getDb();$slave=new slave;$cat=new catalogue;
        //$jmoving_id=$slave->qq($jmoving_id);
        /*$r=$db->query("select oper_status,storage_id,storage_cells_id from J_INCOME where id='$jmoving_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $oper_status=$db->result($r,0,"oper_status");
            $storage_id=$db->result($r,0,"storage_id");
            $storage_cells_id=$db->result($r,0,"storage_cells_id");
            if ($storage_id==0 || $storage_cells_id==0){$answer=0;$err="Не вказано \"Склад зберігання\" або \"Комірка зберігання\". Накладну не проведено!";}
            if ($storage_id>0 && $storage_cells_id>0){
                if ($oper_status==30) {
                    $db->query("update J_INCOME set oper_status='31' where id='$jmoving_id';");
   			        $r1=$db->query("select * from J_INCOME_STR where jmoving_id='$jmoving_id' order by id asc;");$n1=$db->num_rows($r1);
                    for ($i=1;$i<=$n1;$i++){
                        $art_id=$db->result($r1,$i-1,"art_id");
                        $amount=$db->result($r1,$i-1,"amount");
                        $price_man_usd=$db->result($r1,$i-1,"price_man_usd");
                        list($oper_price,$general_stock)=$cat->getArticleOperPriceGeneralStock($art_id);
                        $new_oper_price=round((($oper_price*$general_stock)+($amount*$price_man_usd))/($amount+$general_stock),2);
                        $new_general_stock=$amount+$general_stock;
                        $cat->setArticleOperPriceGeneralStock($art_id,$new_oper_price,$new_general_stock);
    					//$db->query("insert into T2_ARTICLES_STRORAGE_CELLS (`ART_ID`,`AMOUNT`,`INCOME_ID`,`STORAGE_ID`,`STORAGE_CELLS_ID`) values ('$art_id','$amount','$jmoving_id','$storage_id','$storage_cells_id');");
                        $db->query("insert into T2_ARTICLES_STRORAGE (`ART_ID`,`AMOUNT`,`INCOME_ID`,`STORAGE_ID`) values ('$art_id','$amount','$jmoving_id','$storage_id');");
                    }
                $answer=1;$err="";
                } else {$answer=0;$err="Накладну заблоковано. Зміни вносити заборонено.";}
            }
        }*/
        return array($answer,$err);
    }

    function showJmovingLocalAutoCellForm($jmoving_id,$storage_id_to){
        $form="";$form_htm=RD."/tpl/jmoving_local_auto_cell_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $form=str_replace("{jmoving_id}",$jmoving_id,$form);
        list($cells_list,$cs)=$this->showStorageCellsSelectList($storage_id_to,0);
        list($cells_list_to,$cs_to)=$this->showStorageCellsSelectList($storage_id_to,0);
        $form=str_replace("{cells_list_from}",$cells_list,$form);
        $form=str_replace("{cells_list_to}",$cells_list_to,$form);
        $form=str_replace("{storage_name_to}",$this->getStorageName($storage_id_to),$form);
        $form=str_replace("{storage_id_to}",$storage_id_to,$form);
        return $form;
    }

    function saveJmovingLocalAutoCell($jmoving_id,$storage_id_to,$cell_id_from,$cell_id_to){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;$catalogue=new catalogue;
        $jmoving_id=$slave->qq($jmoving_id);$storage_id_to=$slave->qq($storage_id_to);$cell_id_from=$slave->qq($cell_id_from);$cell_id_to=$slave->qq($cell_id_to);
        $answer=0;$err="Помилка збереження даних!";$kol_row=$amountEx=$rr_amount=$rr_reserv=$no_row=0;
        if ($cell_id_from==$cell_id_to) $err="Виберіть різні комірки для переміщення та розміщення!";
        if ($jmoving_id>0 && $storage_id_to>0 && $cell_id_from>0 && ($cell_id_from!=$cell_id_to)){
            $db->query("update J_MOVING set storage_id_to='$storage_id_to' where id='$jmoving_id';");
            $rc=$dbt->query("select * from T2_ARTICLES_STRORAGE_CELLS where STORAGE_ID='$storage_id_to' and STORAGE_CELLS_ID='$cell_id_from';");$nc=$dbt->num_rows($rc);
            for ($ic=1;$ic<=$nc;$ic++){
                $art_id=$dbt->result($rc,$ic-1,"ART_ID");
                $amountS=$dbt->result($rc,$ic-1,"AMOUNT");
                list($article_nr_displ,$brand_id,$brand_name)=$catalogue->getArticleNrDisplBrand($art_id); $idS="";
                $r=$db->query("select id,amount from J_MOVING_STR where jmoving_id='$jmoving_id' and art_id='$art_id' and `storage_id_from`='$storage_id_to' and status_jmoving='44' limit 0,1;");$n=$db->num_rows($r);
                if ($n==1){
                    $idS=$db->result($r,0,"id");
                    $amountEx=$db->result($r,0,"amount");
                }
                if ($idS=="" || $idS==0){
                    $r=$db->query("select max(id) as mid from J_MOVING_STR;");$idS=0+$db->result($r,0,"mid")+1;
                    $db->query("insert into J_MOVING_STR (`id`,`jmoving_id`) values ('$idS','$jmoving_id');");
                    $rr_reserv=0;$amountEx=0;
                }
                if ($idS>0){
                    if ($art_id!="" && $art_id>0 && $article_nr_displ!=""){$kol_row+=1;
                        $amountEx+=$amountS;
                        $db->query("update J_MOVING_STR set `art_id`='$art_id', `article_nr_displ`='$article_nr_displ', `brand_id`='$brand_id', `amount`='$amountEx', `storage_id_from`='$storage_id_to', `cell_id_from`='$cell_id_from', `cell_id_to`='$cell_id_to' where id='$idS' and jmoving_id='$jmoving_id' limit 1;");
                        $db->query("update J_MOVING set status_jmoving='44' where id='$jmoving_id' limit 1;");

                        list($weight,$volume,$empty_kol)=$this->updateJmovingWeightVolume($jmoving_id);

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

    function clearJmovingLocalAutoCellForm($jmoving_id){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;$answer=0;$err="Помилка індексу";
        $jmoving_id=$slave->qq($jmoving_id);
        $r=$db->query("select oper_status,status_jmoving from J_MOVING where id='$jmoving_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $status_jmoving=$db->result($r,0,"status_jmoving");
            $oper_status=$db->result($r,0,"oper_status");
            if ($oper_status==30 && ($status_jmoving==44 ||$status_jmoving==45)){
                $r1=$db->query("select * from J_MOVING_STR where jmoving_id='$jmoving_id';");$n1=$db->num_rows($r1);
                for ($i1=1;$i1<=$n1;$i1++){
                    $jmoving_str_id=$db->result($r1,$i1-1,"id");
                    $status_jmoving_str=$db->result($r1,$i1-1,"status_jmoving");
                    if ($status_jmoving_str==44){
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
                        $db->query("delete from J_MOVING_STR where id='$jmoving_str_id' and jmoving_id='$jmoving_id' limit 1;");
                        $this->updateJmovingWeightVolume($jmoving_id);
                        $answer=1;$err="";
                    }else {$answer=0;$err="Видалення заблоковано. Відбір передано в роботу.";}
                }
            }else {$answer=0;$err="Видалення заблоковано. Переміщення передано в роботу.";}
        }
        return array($answer,$err);
    }

    function showJmovingArticleSearchForm($art_id,$brand_id,$article_nr_display,$jmoving_id,$storage_id_to){
        $form="";$form_htm=RD."/tpl/jmoving_artilce_search_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        list($range_list,$list_brand_select)=$this->showArticlesSearchDocumentList($article_nr_display,$brand_id,0,$jmoving_id,$storage_id_to);
        $form=str_replace("{article_nr_display}",$article_nr_display,$form);
        $form=str_replace("{range_list}",$range_list,$form);
        $form=str_replace("{list_brand_select}",$list_brand_select,$form);
        return $form;
    }

    function showJmovingArticleLocalSearchForm($art_id,$brand_id,$article_nr_display,$jmoving_id,$storage_id_from){
        $form="";$form_htm=RD."/tpl/jmoving_artilce_local_search_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        list($range_list,$list_brand_select)=$this->showArticlesLocalSearchDocumentList($article_nr_display,$brand_id,0,$jmoving_id,$storage_id_from);
        $form=str_replace("{article_nr_display}",$article_nr_display,$form);
        $form=str_replace("{range_list}",$range_list,$form);
        $form=str_replace("{list_brand_select}",$list_brand_select,$form);
        return $form;
    }

    function showArticlesSearchDocumentList($art,$brand_id,$search_type,$jmoving_id,$storage_id_to){$db=DbSingleton::getTokoDb();$cat=new catalogue;$n=0;$list2="";$r="";$query="";
        if ($search_type==""){$search_type=1;}
        if ($search_type==0){
            $art=$cat->clearArticle($art);
            $where_brand="";$group_brand="group by t2c.BRAND_ID"; if ($brand_id!="" && $brand_id>0){$where_brand=" and t2c.BRAND_ID='$brand_id'"; $group_brand="";}
            if ($art!=""){
                $query="select t2b.BRAND_NAME, t2n.NAME,t2c.BRAND_ID, t2c.DISPLAY_NR, t2c.ART_ID, t2c.KIND, t2c.RELATION 
                from T2_CROSS t2c 
                    inner join T2_BRANDS t2b on t2b.BRAND_ID=t2c.BRAND_ID
                    left outer join T2_NAMES t2n on t2n.ART_ID=t2c.ART_ID
                where t2c.SEARCH_NUMBER = '$art' $where_brand $group_brand order by t2n.NAME asc;";
                $r=$db->query($query);$n=$db->num_rows($r);
            }
            $one_result=0;
            if ($n>1 && ($brand_id=="" || $brand_id==0)){ $where_brand="";
                $list2=$cat->showCatalogueBrandSelectDocumentList($r,$art);
            }
            if ($n==1){
                $query="select t2b.BRAND_NAME, t2n.NAME,t2c.BRAND_ID, t2c.DISPLAY_NR, t2c.ART_ID, t2c.KIND, t2c.RELATION 
                from T2_CROSS t2c 
                    inner join T2_BRANDS t2b on t2b.BRAND_ID=t2c.BRAND_ID
                    left outer join T2_NAMES t2n on t2n.ART_ID=t2c.ART_ID
                where t2c.SEARCH_NUMBER = '$art' $where_brand order by t2n.NAME asc;";
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
                $query="select t2a.ART_ID,t2a.BRAND_ID,t2a.ARTICLE_NR_DISPL, t2b.BRAND_NAME, t2n.NAME, t2n.INFO, t2bc.BARCODE, gg.NAME as goods_group_name, SUM(`t2asc`.`AMOUNT`) as stock, SUM(`t2asc`.`RESERV_AMOUNT`) as reserv, s.name as storage_name,s.id as storage_id
                from T2_ARTICLES t2a 
                    left outer join T2_BRANDS t2b on t2b.BRAND_ID=t2a.BRAND_ID 
                    left outer join T2_NAMES t2n on t2n.ART_ID=t2a.ART_ID 
                    left outer join T2_BARCODES t2bc on t2bc.ART_ID=t2a.ART_ID 
                    left outer join T2_GOODS_GROUP t2gg on t2gg.ART_ID=t2a.ART_ID 
                    left outer join GOODS_GROUP gg on gg.ID=t2gg.GOODS_GROUP_ID 
                    left outer join T2_ARTICLES_STRORAGE t2asc on t2asc.ART_ID=t2a.ART_ID 
                    left outer join STORAGE s on s.id=t2asc.STORAGE_ID
                where t2a.ART_ID in ($art_id_str)";
            }
        }
        if ($search_type==1){
            $query="select t2a.ART_ID,t2a.BRAND_ID,t2a.ARTICLE_NR_DISPL, t2b.BRAND_NAME, t2n.NAME, t2n.INFO, t2bc.BARCODE, gg.NAME as goods_group_name, SUM(`t2asc`.`AMOUNT`) as stock, SUM(`t2asc`.`RESERV_AMOUNT`) as reserv, s.name as storage_name, s.id as storage_id
            from T2_ARTICLES t2a 
                left outer join T2_BRANDS t2b on t2b.BRAND_ID=t2a.BRAND_ID 
                left outer join T2_NAMES t2n on t2n.ART_ID=t2a.ART_ID 
                left outer join T2_BARCODES t2bc on t2bc.ART_ID=t2a.ART_ID 
                left outer join T2_GOODS_GROUP t2gg on t2gg.ART_ID=t2a.ART_ID 
                left outer join GOODS_GROUP gg on gg.ID=t2gg.GOODS_GROUP_ID 
                left outer join T2_ARTICLES_STRORAGE t2asc on t2asc.ART_ID=t2a.ART_ID 
                left outer join STORAGE s on s.id=t2asc.STORAGE_ID
            where t2a.ARTICLE_NR_SEARCH='$art' or t2a.ARTICLE_NR_DISPL='$art';";
        }
        if ($search_type==2){
            $query="select t2a.ART_ID,t2a.BRAND_ID,t2a.ARTICLE_NR_DISPL, t2b.BRAND_NAME, t2n.NAME, t2n.INFO, t2bc.BARCODE, gg.NAME as goods_group_name, SUM(`t2asc`.`AMOUNT`) as stock, SUM(`t2asc`.`RESERV_AMOUNT`) as reserv, s.name as storage_name, s.id as storage_id
            from T2_ARTICLES t2a 
                left outer join T2_BRANDS t2b on t2b.BRAND_ID=t2a.BRAND_ID 
                left outer join T2_NAMES t2n on t2n.ART_ID=t2a.ART_ID 
                left outer join T2_BARCODES t2bc on t2bc.ART_ID=t2a.ART_ID 
                left outer join T2_GOODS_GROUP t2gg on t2gg.ART_ID=t2a.ART_ID 
                left outer join GOODS_GROUP gg on gg.ID=t2gg.GOODS_GROUP_ID 
                left outer join T2_ARTICLES_STRORAGE t2asc on t2asc.ART_ID=t2a.ART_ID 
                left outer join STORAGE s on s.id=t2asc.STORAGE_ID
            where t2bc.BARCODE='$art';";
        }

        $r=$db->query($query);$n=$db->num_rows($r);$list="";
        if ($list2==""){  // сработал внешний фильр или основной поиск с выбором бренда
            for ($i=1;$i<=$n;$i++){
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
                $storage_id=$db->result($r,$i-1,"storage_id");$storage_name=$db->result($r,$i-1,"storage_name");
                $cell_id=$db->result($r,$i-1,"cell_id");$cell_name=$db->result($r,$i-1,"cell_name");
                $stock=$db->result($r,$i-1,"stock");
                $reserv=$db->result($r,$i-1,"reserv");
                $jmoving_amount=$this->getArticleInJmoving($art_id,$jmoving_id);
                $amountRest="сток: $stock | резерв: $reserv | у поточному відборі: $jmoving_amount";

                $list.="<tr style='cursor:pointer'>
                    <td class='text-center'><button class='btn btn-sm btn-default' onclick='setArticleToSelectAmountJmoving(\"$art_id\",\"$article_nr_displ\",\"$brand_id\",\"$brand_name\",\"$storage_id\",\"$storage_name\",\"$cell_id\",\"$cell_name\",\"$stock\",\"$amountRest\")'><i class='fa fa-plus'></i></button></td>
                    <td class='text-center'>$article_nr_displ</td>
                    <td class='text-center'>$brand_name</td>
                    <td class='text-center'>$name</td>
                    <td class='text-center'>$barcode</td>
                    <td class='text-center'>$goods_group_name</td>
                    <!--<td class='text-center'>$storage_name</td>
                    <td class='text-center'>$cell_name</td>-->
                    <td class='text-right'>$amountRest</td>
                    <td class='text-center'>$art_id</td>
                </tr>";
            }
        }
        return array($list,$list2);
    }

    function showArticlesLocalSearchDocumentList($art,$brand_id,$search_type,$jmoving_id,$storage_id_from){$db=DbSingleton::getTokoDb();$cat=new catalogue;$n=0;$list2="";$r="";$query="";
        if ($search_type==""){$search_type=1;}
        if ($search_type==0){
            $art=$cat->clearArticle($art);
            $where_brand="";$group_brand="group by t2c.BRAND_ID"; if ($brand_id!="" && $brand_id>0){$where_brand=" and t2c.BRAND_ID='$brand_id'"; $group_brand="";}
            if ($art!=""){
                $query="select t2b.BRAND_NAME, t2n.NAME, t2c.BRAND_ID, t2c.DISPLAY_NR, t2c.ART_ID, t2c.KIND, t2c.RELATION 
                from T2_CROSS t2c 
                    inner join T2_BRANDS t2b on t2b.BRAND_ID=t2c.BRAND_ID
                    left outer join T2_NAMES t2n on t2n.ART_ID=t2c.ART_ID
                where t2c.SEARCH_NUMBER='$art' $where_brand $group_brand order by t2n.NAME asc;";
                $r=$db->query($query);$n=$db->num_rows($r);
            }
            $one_result=0;
            if ($n>1 && ($brand_id=="" || $brand_id==0)){ $where_brand="";
                $list2=$cat->showCatalogueBrandSelectDocumentList($r,$art);
            }
            if ($n==1){
                $query="select t2b.BRAND_NAME, t2n.NAME, t2c.BRAND_ID, t2c.DISPLAY_NR, t2c.ART_ID, t2c.KIND, t2c.RELATION 
                from T2_CROSS t2c 
                    inner join T2_BRANDS t2b on t2b.BRAND_ID=t2c.BRAND_ID
                    left outer join T2_NAMES t2n on t2n.ART_ID=t2c.ART_ID
                where t2c.SEARCH_NUMBER='$art' $where_brand order by t2n.NAME asc;";
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
                $query="select t2a.ART_ID,t2a.BRAND_ID,t2a.ARTICLE_NR_DISPL, t2b.BRAND_NAME, t2n.NAME, t2n.INFO, t2bc.BARCODE, gg.NAME as goods_group_name, SUM(`t2asc`.`AMOUNT`) as stock, SUM(`t2asc`.`RESERV_AMOUNT`) as reserv, s.name as storage_name,s.id as storage_id
                from T2_ARTICLES t2a 
                    left outer join T2_BRANDS t2b on t2b.BRAND_ID=t2a.BRAND_ID 
                    left outer join T2_NAMES t2n on t2n.ART_ID=t2a.ART_ID 
                    left outer join T2_BARCODES t2bc on t2bc.ART_ID=t2a.ART_ID 
                    left outer join T2_GOODS_GROUP t2gg on t2gg.ART_ID=t2a.ART_ID 
                    left outer join GOODS_GROUP gg on gg.ID=t2gg.GOODS_GROUP_ID 
                    left outer join T2_ARTICLES_STRORAGE t2asc on (t2asc.ART_ID=t2a.ART_ID and  t2asc.STORAGE_ID='$storage_id_from') 
                    left outer join STORAGE s on s.id=t2asc.STORAGE_ID
                where t2a.ART_ID in ($art_id_str)";
            }
        }
        if ($search_type==1){//added group by art_id
            $query="select t2a.ART_ID, t2a.BRAND_ID, t2a.ARTICLE_NR_DISPL, t2b.BRAND_NAME, t2n.NAME, t2n.INFO, t2bc.BARCODE, gg.NAME as goods_group_name, SUM(`t2asc`.`AMOUNT`) as stock, SUM(`t2asc`.`RESERV_AMOUNT`) as reserv, s.name as storage_name, s.id as storage_id
            from T2_ARTICLES t2a 
                left outer join T2_BRANDS t2b on t2b.BRAND_ID=t2a.BRAND_ID 
                left outer join T2_NAMES t2n on t2n.ART_ID=t2a.ART_ID 
                left outer join T2_BARCODES t2bc on t2bc.ART_ID=t2a.ART_ID 
                left outer join T2_GOODS_GROUP t2gg on t2gg.ART_ID=t2a.ART_ID 
                left outer join GOODS_GROUP gg on gg.ID=t2gg.GOODS_GROUP_ID 
                left outer join T2_ARTICLES_STRORAGE t2asc on (t2asc.ART_ID=t2a.ART_ID and t2asc.STORAGE_ID='$storage_id_from')
                left outer join STORAGE s on s.id=t2asc.STORAGE_ID
            where t2a.ARTICLE_NR_SEARCH='$art' or t2a.ARTICLE_NR_DISPL='$art' group by t2a.ART_ID;";
        }
        if ($search_type==2){
            $query="select t2a.ART_ID, t2a.BRAND_ID, t2a.ARTICLE_NR_DISPL, t2b.BRAND_NAME, t2n.NAME, t2n.INFO, t2bc.BARCODE, gg.NAME as goods_group_name, SUM(`t2asc`.`AMOUNT`) as stock, SUM(`t2asc`.`RESERV_AMOUNT`) as reserv, s.name as storage_name, s.id as storage_id
            from T2_ARTICLES t2a 
                left outer join T2_BRANDS t2b on t2b.BRAND_ID=t2a.BRAND_ID 
                left outer join T2_NAMES t2n on t2n.ART_ID=t2a.ART_ID 
                left outer join T2_BARCODES t2bc on t2bc.ART_ID=t2a.ART_ID 
                left outer join T2_GOODS_GROUP t2gg on t2gg.ART_ID=t2a.ART_ID 
                left outer join GOODS_GROUP gg on gg.ID=t2gg.GOODS_GROUP_ID 
                left outer join T2_ARTICLES_STRORAGE t2asc on (t2asc.ART_ID=t2a.ART_ID and  t2asc.STORAGE_ID='$storage_id_from')
                left outer join STORAGE s on s.id=t2asc.STORAGE_ID
            where t2bc.BARCODE='$art';";
        }
        $r=$db->query($query);$n=$db->num_rows($r);$list="";
        if ($list2==""){  // сработал внешний фильр или основной поиск с выбором бренда
            for ($i=1;$i<=$n;$i++){
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
                $storage_id=$db->result($r,$i-1,"storage_id");$storage_name=$db->result($r,$i-1,"storage_name");
                $cell_id=$db->result($r,$i-1,"cell_id");$cell_name=$db->result($r,$i-1,"cell_name");
                $stock=$db->result($r,$i-1,"stock");
                $reserv=$db->result($r,$i-1,"reserv");
                $jmoving_amount=$this->getArticleInJmoving($art_id,$jmoving_id);
                $amountRest="сток на складі: $stock | резерв: $reserv | у поточному відборі: $jmoving_amount";

                $list.="<tr style='cursor:pointer'>
                    <td class='text-center'><button class='btn btn-sm btn-default' onclick='setArticleToSelectAmountJmovingLocal(\"$art_id\",\"$article_nr_displ\",\"$brand_id\",\"$brand_name\",\"$storage_id\",\"$storage_name\",\"$cell_id\",\"$cell_name\",\"$stock\",\"$amountRest\")'><i class='fa fa-plus'></i></button></td>
                    <td class='text-center'>$article_nr_displ</td>
                    <td class='text-center'>$brand_name</td>
                    <td class='text-center'>$name</td>
                    <td class='text-center'>$barcode</td>
                    <td class='text-center'>$goods_group_name</td>
                    <!--<td class='text-center'>$storage_name</td>
                    <td class='text-center'>$cell_name</td>-->
                    <td class='text-right'>$amountRest</td>
                    <td class='text-center'>$art_id</td>
                </tr>";
            }
        }
        return array($list,$list2);
    }

    function getArticleInJmoving($art_id,$jmoving_id){$db=DbSingleton::getDb();
        $r=$db->query("select sum(amount) as amount from J_MOVING_STR where art_id='$art_id' and jmoving_id='$jmoving_id';");$amount=0+$db->result($r,0,"amount");
        return $amount;
    }

    function setArticleToSelectAmountJmoving($art_id,$storage_id,$storage_id_to){
        $form=""; $form_htm=RD."/tpl/jmoving_select_amount_article_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $form=str_replace("{storage_list}",$this->showArticleRestStorageSelectList($art_id,$storage_id,$storage_id_to),$form);
        return $form;
    }

    function setArticleToSelectAmountJmovingLocal($art_id,$storage_id){
        $form=""; $form_htm=RD."/tpl/jmoving_local_select_amount_article_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $form=str_replace("{cells_list_from}",$this->showArticleRestStorageCellsList($art_id,$storage_id),$form);
        $form=str_replace("{cells_list_to}",$this->showStorageCellsList($storage_id),$form);
        return $form;
    }

    function showJmovingArticleAmountChange($art_id,$jmoving_str_id,$amount){$db=DbSingleton::getDb();
        $form="";$form_htm=RD."/tpl/jmoving_select_amount_article_change_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select * from J_MOVING_STR where id='$jmoving_str_id' and status_jmoving='44' limit 0,1");
        $jmoving_id=$db->result($r,0,"jmoving_id");
        $article_nr_displ=$db->result($r,0,"article_nr_displ");
        $brand_id=$db->result($r,0,"brand_id");$brand_name=$this->getBrandName($brand_id);
        $amount=$db->result($r,0,"amount");
        $storage_id=$db->result($r,0,"storage_id_from");
        list($info,$max_moving)=$this->showArticleRestStorageSelectText($art_id,$jmoving_id,$amount);
        $form=str_replace("{storage_name}",$this->getStorageName($storage_id),$form);
        $form=str_replace("{amountRestText}",$info,$form);
        $form=str_replace("{max_moving}",$max_moving,$form);
        $form=str_replace("{cur_amount}",$amount,$form);
        $form=str_replace("{jmoving_str_id}",$jmoving_str_id,$form);
        return array($form,$article_nr_displ,$brand_name);
    }

    function showJmovingArticleAmountLocalChange($art_id,$jmoving_str_id,$amount){$db=DbSingleton::getDb();
        $form="";$form_htm=RD."/tpl/jmoving_local_select_amount_article_change_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select * from J_MOVING_STR where id='$jmoving_str_id' and status_jmoving='44' limit 0,1");
        $jmoving_id=$db->result($r,0,"jmoving_id");
        $article_nr_displ=$db->result($r,0,"article_nr_displ");
        $brand_id=$db->result($r,0,"brand_id");$brand_name=$this->getBrandName($brand_id);
        $amount=$db->result($r,0,"amount");
        $storage_id=$db->result($r,0,"storage_id_from");
        $cell_id=$db->result($r,0,"cell_id_from");$storage_id_from=0;
        list($info,$max_moving)=$this->showArticleRestStorageCellSelectText($art_id,$jmoving_id,$amount,$cell_id,$storage_id_from);
        $form=str_replace("{storage_name}",$this->getStorageName($storage_id),$form);
        $form=str_replace("{cell_name}",$this->getStorageCellName($cell_id),$form);
        $form=str_replace("{storage_id_from}",$storage_id,$form);
        $form=str_replace("{cell_id_from}",$cell_id,$form);
        $form=str_replace("{amountRestText}",$info,$form);
        $form=str_replace("{max_moving}",$max_moving,$form);
        $form=str_replace("{cur_amount}",$amount,$form);
        $form=str_replace("{jmoving_str_id}",$jmoving_str_id,$form);
        return array($form,$article_nr_displ,$brand_name);
    }

    function showArticleRestStorageSelectText($art_id,$jmoving_id,$cur_amount,$storage_id=null){$db=DbSingleton::getTokoDb();$info="";
        $reserv_amount=$reserv_amount_storage=0;$max_moving=$amount=0;
        if ($storage_id=="" || $storage_id==0) $where_storage=""; else $where_storage=" and t2as.STORAGE_ID='$storage_id' ";
        $r=$db->query("select s.id, s.name, t2as.AMOUNT, t2as.RESERV_AMOUNT from STORAGE s 
            inner join T2_ARTICLES_STRORAGE t2as on t2as.STORAGE_ID=s.id 
        where s.status='1' and t2as.ART_ID='$art_id' $where_storage order by s.name asc,s.id asc;");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $amount=$db->result($r,$i-1,"AMOUNT");
            $reserv_amount+=$db->result($r,$i-1,"RESERV_AMOUNT");
            //$max_moving=$amount+$cur_amount;
            $max_moving=$amount;
            $info="Залишок: $amount | Резерв: $reserv_amount<br>У поточному записі: $cur_amount";
        }
        return array($info,$max_moving,$amount);
    }

    function showArticleRestStorageCellSelectText($art_id,$jmoving_id,$cur_amount,$cell_id,$storage_id_from){$db=DbSingleton::getTokoDb();$info="";
        $reserv_amount=$reserv_amount_storage=0;$max_moving=$amount=0;
        $query="select sc.id, sc.cell_value, t2asc.AMOUNT, t2asc.RESERV_AMOUNT, t2as.AMOUNT as AMOUNT_STORAGE, t2as.RESERV_AMOUNT as RESERV_AMOUNT_STORAGE 
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

    function showArticleRestStorageSelectList($art_id,$storage_id,$storage_id_to){$db=DbSingleton::getTokoDb();$list="<option value='0'>-- Оберіть зі списку --</option>";
        $where="";//if($storage_id>0){$where=" and t2as.STORAGE_ID!='$storage_id'";}
        //if ($storage_id_to>0){$where=" and t2as.STORAGE_ID!='$storage_id'";}
        $query="select s.id, s.name, t2as.AMOUNT, t2as.RESERV_AMOUNT from STORAGE s 
            left outer join T2_ARTICLES_STRORAGE t2as on t2as.STORAGE_ID=s.id 
        where s.status='1' and t2as.ART_ID='$art_id' $where order by s.name asc,s.id asc;";
        $r=$db->query($query);$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"name");
            $amount=$db->result($r,$i-1,"AMOUNT");
            $reserv_amount=$db->result($r,$i-1,"RESERV_AMOUNT");
            $max_moving=$amount;
            $sel="";//if ($id==$sel_id){$sel=" selected='selected'";}$t='';
            if ($id==$storage_id){$sel.=" disabled"; $t="Сам себя..? ";}
            if($amount!=0 || $reserv_amount!=0) {
                $list.="<option value='$id' data-max-mov='$max_moving' data-cellId-mov='0' $sel>$name $t | Залишок: $amount; Резерв: $reserv_amount; </option>";
            }
        }
        return $list;
    }

    function showArticleRestStorageCellsList($art_id,$storage_id){$db=DbSingleton::getTokoDb();$list="<option value='0'>-- Оберіть зі списку --</option>";
        $query="SELECT sc.id, sc.cell_value, t2asc.AMOUNT, t2asc.RESERV_AMOUNT, t2as.AMOUNT as AMOUNT_STORAGE, t2as.RESERV_AMOUNT as RESERV_AMOUNT_STORAGE
        FROM STORAGE_CELLS sc
            LEFT OUTER JOIN T2_ARTICLES_STRORAGE_CELLS t2asc ON ( t2asc.STORAGE_CELLS_ID = sc.id )
            LEFT OUTER JOIN T2_ARTICLES_STRORAGE t2as ON ( t2as.STORAGE_ID = sc.storage_id )
        WHERE sc.status='1' AND t2asc.ART_ID='$art_id' AND t2as.ART_ID='$art_id' AND sc.storage_id='$storage_id' ORDER BY sc.cell_value ASC, sc.id ASC;";
        $r=$db->query($query);$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"cell_value");
            $amount=$db->result($r,$i-1,"AMOUNT");
            $reserv_amount=$db->result($r,$i-1,"RESERV_AMOUNT");
            //$amount_storage=$db->result($r,$i-1,"AMOUNT_STORAGE");
            //$reserv_amount_storage=$db->result($r,$i-1,"RESERV_AMOUNT_STORAGE");
            //if ($amount>$amount_storage){$amount=$amount_storage; $reserv_amount=$reserv_amount_storage;}
            $max_moving=$amount;
            if ($reserv_amount!=0 || $amount!=0){
                $list.="<option value='$id' data-max-mov='$max_moving' data-cellId-mov='0'>$name | Залишок: $amount; Резерв: $reserv_amount; </option>";
            }
        }
        return $list;
    }

    function showStorageCellsList($storage_id,$exclude_id=null){$db=DbSingleton::getTokoDb();$list="<option value='0'>-- Оберіть зі списку --</option>";
        $query="SELECT id, cell_value FROM STORAGE_CELLS WHERE status='1' AND storage_id='$storage_id' AND id<>'$exclude_id'  ORDER BY cell_value ASC , id ASC;";
        $r=$db->query($query);$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"cell_value");
            $list.="<option value='$id'>$name</option>";
        }
        return $list;
    }

    function getArticleName($art_id){$db=DbSingleton::getTokoDb(); $name="";
        $r=$db->query("select * from T2_NAMES where ART_ID='$art_id' and `LANG_ID`='16' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $name=$db->result($r,0,"NAME");
        }
        return $name;
    }

    function getArticleWightVolume($art_id){$db=DbSingleton::getTokoDb();$weight=0;$volume=0;$weight2=0;
        $r=$db->query("select VOLUME, WEIGHT_BRUTTO, WEIGHT_NETTO from T2_PACKAGING where ART_ID='$art_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $weight=$db->result($r,0,"WEIGHT_BRUTTO");
            $weight2=$db->result($r,0,"WEIGHT_NETTO");
            $volume=$db->result($r,0,"VOLUME");
        }
        return array($weight,$volume,$weight2);
    }

    function getArticleRestStorage($art_id,$storage_id){$db=DbSingleton::getTokoDb();$stock=0;$reserv=0;if ($storage_id==""){$storage_id=0;}
        $r=$db->query("select SUM(`AMOUNT`) as stock, SUM(`RESERV_AMOUNT`) as reserv from T2_ARTICLES_STRORAGE where ART_ID='$art_id' and `STORAGE_ID`='$storage_id';");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $stock+=$db->result($r,$i-1,"stock");
            $reserv+=$db->result($r,$i-1,"reserv");
        }
        return array($stock,$reserv);
    }

    function getArticleRestStorageCell($art_id,$storage_id,$cell_id){$db=DbSingleton::getTokoDb();$stock=0;$reserv=0;if ($storage_id==""){$storage_id=0;}if ($cell_id==""){$cell_id=0;}
        $r=$db->query("select `AMOUNT` as stock, `RESERV_AMOUNT` as reserv from T2_ARTICLES_STRORAGE_CELLS 
        where ART_ID='$art_id' and `STORAGE_ID`='$storage_id' and `STORAGE_CELLS_ID`='$cell_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $stock=$db->result($r,0,"stock");
            $reserv=$db->result($r,0,"reserv");
        }
        return array($stock,$reserv);
    }

    function loadJmovingStorage($jmoving_id){$db=DbSingleton::getDb();
        $form="";$form_htm=RD."/tpl/jmoving_storage_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select storage_id, storage_cells_id from J_INCOME where `id`='$jmoving_id' limit 0,1;");
        $storage_id=$db->result($r,0,"storage_id");
        $storage_cells_id=$db->result($r,0,"storage_cells_id");
        $form=str_replace("{jmoving_id}",$jmoving_id,$form);
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
            $query="select s.* from `STORAGE` s 
                inner join STORAGE_STR ss on ss.storage_id=s.id 
            where s.status='1' group by ss.storage_id order by s.name,s.id asc;";
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
            $default=$db->result($r,$i-1,"default");
            $sel="";if ($sel_id==$id){$sel="selected='selected'";}if ($sel_id==0 && $default==1) {$sel="selected='selected'";}
            $list.="<option value='$id' $sel>$cell_value</option>";
        }
        return array($list,$cells_show);
    }

    function saveJmovingStorage($jmoving_id,$storage_id,$storage_cells_id){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка збереження даних!";
        $jmoving_id=$slave->qq($jmoving_id);$storage_id=$slave->qq($storage_id);$storage_cells_id=$slave->qq($storage_cells_id);
        if ($jmoving_id>0 && $storage_id>0 && $storage_cells_id>0){
            $db->query("update J_INCOME set storage_id='$storage_id', `storage_cells_id`='$storage_cells_id' where id='$jmoving_id';");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function getRateTypeDeclarationdocumentPos($costums_id,$country_id){$db=DbSingleton::getDb();$manual=new manual;$rate=0;$type_declaration="";$type_declaration_id=0;$duty=0;
        $r=$db->query("select DUTY from T2_COUNTRIES where country_id='$country_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $duty=$db->result($r,0,"DUTY");
        }
        $r=$db->query("select PREFERENTIAL_RATE, FULL_RATE, TYPE_DECLARATION from T2_COSTUMS where costums_id='$costums_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $preferential_rate=$db->result($r,0,"PREFERENTIAL_RATE");
            $full_rate=$db->result($r,0,"FULL_RATE");
            $rate=$preferential_rate; if ($duty==2){$rate=$full_rate;}
            $type_declaration_id=$db->result($r,0,"TYPE_DECLARATION");
            $type_declaration=$manual->getManualMCaption("costums_type_declaration",$type_declaration_id);
        }
        return array($rate,$type_declaration,$type_declaration_id);
    }

    function loadJmovingCommets($jmoving_id){$db=DbSingleton::getDb();
        $form="";$form_htm=RD."/tpl/jmoving_comment_block.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select cc.*, u.name from J_MOVING_COMMENTS cc 
            left outer join media_users u on u.id=cc.USER_ID 
        where cc.jmoving_id='$jmoving_id' order by id desc;");$n=$db->num_rows($r);$list="";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $user_id=$db->result($r,$i-1,"user_id");
            $user_name=$db->result($r,$i-1,"name");
            $data=$db->result($r,$i-1,"data");
            $comment=$db->result($r,$i-1,"comment");
            $block=$form;
            $block=str_replace("{jmoving_id}",$jmoving_id,$block);
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

    function saveJmovingComment($jmoving_id,$comment){$db=DbSingleton::getDb();$slave=new slave;session_start();$user_id=$_SESSION["media_user_id"];$answer=0;$err="Помилка збереження даних!";
        $jmoving_id=$slave->qq($jmoving_id);$comment=$slave->qq($comment);
        if ($jmoving_id>0 && $comment!=""){
            $db->query("insert into J_MOVING_COMMENTS (`jmoving_id`,`user_id`,`comment`) values ('$jmoving_id','$user_id','$comment');");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function dropJmovingComment($jmoving_id,$comment_id){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка видалення запису!";
        $jmoving_id=$slave->qq($jmoving_id);$comment_id=$slave->qq($comment_id);
        if ($jmoving_id>0 && $comment_id>0){
            $r=$db->query("select * from J_MOVING_COMMENTS where jmoving_id='$jmoving_id' and id='$comment_id' limit 0,1;");$n=$db->num_rows($r);
            if ($n==1){
                $db->query("delete from J_MOVING_COMMENTS where jmoving_id='$jmoving_id' and id='$comment_id';");
                $answer=1;$err="";
            }
        }
        return array($answer,$err);
    }

    function loadJmovingCDN($jmoving_id){$db=DbSingleton::getDb();
        $form="";$form_htm=RD."/tpl/jmoving_cdn_block.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select cc.*, u.name as user_name from J_MOVING_CDN cc 
            left outer join media_users u on u.id=cc.USER_ID 
        where cc.jmoving_id='$jmoving_id' and cc.status='1' order by cc.file_name asc;");$n=$db->num_rows($r);$list="";
        for ($i=1;$i<=$n;$i++){
            $file_id=$db->result($r,$i-1,"id");
            $file_name=$db->result($r,$i-1,"file_name");
            $name=$db->result($r,$i-1,"name");
            $data=$db->result($r,$i-1,"data");
            $user_name=$db->result($r,$i-1,"user_name");
            $link="http://cdn.myparts.pro/jmoving_files/$jmoving_id/$file_name";
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
            $block=str_replace("{jmoving_id}",$jmoving_id,$block);
            $block=str_replace("{link}",$link,$block);
            $block=str_replace("{file_view}",$file_view,$block);
            $list.=$block;
        }
        if ($n==0){$list="<h3 class='text-center'>Файли відсутні</h3>";}
        return $list;
    }

    function jmovingCDNDropFile($jmoving_id,$file_id){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка видалення файлу!";
        $jmoving_id=$slave->qq($jmoving_id);$file_id=$slave->qq($file_id);
        if ($jmoving_id>0 && $file_id>0){
            $r=$db->query("select FILE_NAME from J_MOVING_CDN where jmoving_id='$jmoving_id' and id='$file_id' limit 0,1;");$n=$db->num_rows($r);
            if ($n==1){
                unlink(RD.'/cdn/jmoving_files/$jmoving_id/$file_name');
                $db->query("delete from J_MOVING_CDN where jmoving_id='$jmoving_id' and id='$file_id';");
                $answer=1;$err="";
            }
        }
        return array($answer,$err);
    }

    function getJmovingNameById($sel_id, $field="name"){$db=DbSingleton::getDb();$name="";
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
    /*  $r=$db->query("select * from T2_REGION where STATE_ID='$state_id' order by REGION_NAME asc;");$n=$db->num_rows($r);$list="";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"REGION_ID");
            $name=$db->result($r,$i-1,"REGION_NAME");
            $sel="";if ($sel_id==$id){$sel="selected='selected'";}
            $list.="<option value='$id' $sel>$name</option>";
        }*/
        return $slave->showSelectSubList("T2_REGION","STATE_ID","$state_id","REGION_ID","REGION_NAME",$sel_id);
    }

    function loadCitySelectList($region_id,$sel_id){$slave=new slave;
    /*	$r=$db->query("select * from T2_CITY where REGION_ID='$region_id' order by CITY_NAME asc;");$n=$db->num_rows($r);$list="";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"CITY_ID");
            $name=$db->result($r,$i-1,"CITY_NAME");
            $sel="";if ($sel_id==$id){$sel="selected='selected'";}
            $list.="<option value='$id' $sel>$name</option>";
        }*/
        return "<option value='NEW'>Добавити населений пункт</option>".$slave->showSelectSubList("T2_CITY","REGION_ID","$region_id","CITY_ID","CITY_NAME",$sel_id);
    }

    function showCategoryCheckList($jmoving_id){$db=DbSingleton::getDb();$list="";
        $r=$db->query("select * from A_CATEGORY where parrent_id=0 order by id asc;");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"name");
            $sel=$this->checkJmovingCategorySelect($jmoving_id,$id);
            $ch="";if ($sel==1){$ch=" checked=''";}
            $list.="<label><input type='checkbox' class='i-checks' id='c_category_$i' value='$id' $ch> - $name;</label> ";
        }
        $list.="<input type='hidden' id='c_category_kol' value='$n'>";
        return $list;
    }

    function checkJmovingCategorySelect($jmoving_id,$category_id){$db=DbSingleton::getDb();
        $r=$db->query("select category_id from A_CLIENTS_CATEGORY where jmoving_id='$jmoving_id' and category_id='$category_id' limit 0,1;");$n=$db->num_rows($r);
        $n==1 ? $ch=1 : $ch=0;
        return $ch;
    }

    function showMovingOpListSelect($sel_id){$db=DbSingleton::getDb();$list="";
        $r=$db->query("select * from J_MOVING_OP where in_show='1' order by id asc;");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"name");
            $sel="";if ($sel_id==$id){$sel="selected='selected'";}
            $list.="<option value='$id' $sel>$name</option>";
        }
        return $list;
    }

    function showJmovingDocumentList($jmoving_id,$jmoving_op_id,$document_id){$income=new income;
        $form=""; $document_list="";
        if ($jmoving_op_id==1){
            $form_htm=RD."/tpl/jmoving_documents_list.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
            $document_list=$income->search_documents_income_list("");
        }
        $form=str_replace("{documents_list}",$document_list,$form);
        $form=str_replace("{jmoving_id}",$jmoving_id,$form);
        $form=str_replace("{jmoving_op_id}",$jmoving_op_id,$form);
        return array($form,"Реєстр документів основи");
    }

    function findJmovingDocumentsSearch($jmoving_id,$jmoving_op_id,$s_nom){$income=new income;
        $jmoving_op_id==1 ? $document_list=$income->search_documents_income_list($s_nom) : $document_list="";
        return $document_list;
    }

    function getArtIdByBarcode($barcode){$db=DbSingleton::getTokoDb();$art_id=0;
        $r=$db->query("select ART_ID from T2_BARCODES where BARCODE='$barcode' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){	$art_id=$db->result($r,0,"ART_ID");	}
        return $art_id;
    }

    function getArtId($code,$brand_id){$db=DbSingleton::getTokoDb();$slave=new slave;$cat=new catalogue;$id=0; $code=$slave->qq($code); $code=$cat->clearArticle($code);
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

    function showWorkPairForm($jmoving_id){$db=DbSingleton::getDb();$list="";
        $r=$db->query("select PAIR_INDEX from T2_WORK_PAIR where ART_ID='$jmoving_id';");$n=$db->num_rows($r);
        for ($i=1;$i<=$n+3;$i++){
            $pair_index="";
            if ($i<=$n){$pair_index=$db->result($r,$i-1,"PAIR_INDEX");}
            $list.="<tr><td><input type='text' id='work_pair_$i' value='$pair_index' class='form-control'></td></tr>";
        }
        $list.="<input type='hidden' id='work_pair_n' value='".($n+3)."'>";
        return $list;
    }

    function labelArtEmptyCount($jmoving_id,$kol){$label="";
        if ($kol==0 || $kol==""){
            list($weight,$volume,$kol)=$this->updateJmovingWeightVolume($jmoving_id);
        }
        if ($kol>0){$label="<span class='label label-tab label-info'>$kol</span>";}
        return array($kol,$label);
    }

    function labelCommentsCount($jmoving_id){$db=DbSingleton::getDb();$label="";
        $r=$db->query("select count(id) as kol from J_MOVING_COMMENTS where jmoving_id='$jmoving_id';");$kol=0+$db->result($r,0,"kol");
        if ($kol>0){$label="<span class='label label-tab label-info'>$kol</span>";}
        return array($kol,$label);
    }

    function loadJmovingUnknownArticles($jmoving_id){$db=DbSingleton::getDb();
        $form="";$form_htm=RD."/tpl/jmoving_unknown_articles_list.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select * from J_MOVING j where j.id='$jmoving_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==0){$form_htm=RD."/tpl/access_deny.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);} }
        if ($n==1){
            list($list,$kol_rows)=$this->showJmovingUnknownStrList($jmoving_id);
            $form=str_replace("{UnknownArticlesList}",$list,$form);
            $form=str_replace("{kol_rows}",$kol_rows,$form);
            $form=str_replace("{jmoving_id}",$jmoving_id,$form);
        }
        return $form;
    }

    function showJmovingUnknownStrList($jmoving_id){$db=DbSingleton::getDb();$cat=new catalogue;$list="";
        $r=$db->query("select * from J_MOVING_STR where jmoving_id='$jmoving_id' group by art_id order by id asc;");$n=$db->num_rows($r);$empty_kol=0;
        for ($i=1;$i<=$n;$i++){
            $art_id=$db->result($r,$i-1,"art_id");
            $article_nr_displ=$db->result($r,$i-1,"article_nr_displ");
            $brand_id=$db->result($r,$i-1,"brand_id");$brand_name=$cat->getBrandName($brand_id);
            list($weight_brutto,$volume,$weight_netto)=$this->getArticleWightVolume($art_id);
            if ($weight_brutto==0 || $volume==0 || $weight_netto==0){$empty_kol+=1;
                $list.="<tr id='strUnRow_$i'>
                    <td><button class='btn btn-xs btn-warning' onClick='checkJmovingUnStr(\"$jmoving_id\",\"$i\",\"$art_id\");'><i class='fa fa-refresh'></i></button></td>
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

    function checkJmovingUnStr($jmoving_id,$art_id,$volume,$weight,$weight2){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;$answer=0;$err="";
        $jmoving_id=$slave->qq($jmoving_id);$art_id=$slave->qq($art_id);
        $r=$db->query("select oper_status from J_MOVING where id='$jmoving_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $oper_status=$db->result($r,0,"oper_status");
            if ($oper_status==30) {
                $volume=$slave->qq($volume);$weight=$slave->qq($weight);$weight2=$slave->qq($weight2);
                if ($art_id>0 && $volume>0 && $weight>0 && $weight2>0){
                    $rs=$db->query("select art_id from `T2_PACKAGING` where art_id='$art_id' limit 0,1;");$ns=$dbt->num_rows($rs);
                    if ($ns==1){ $dbt->query("update `T2_PACKAGING` set `VOLUME`='$volume', `WEIGHT_NETTO`='$weight', `WEIGHT_BRUTTO`='$weight2' where ART_ID='$art_id' limit 1;");	}
                    else{ $dbt->query("insert into `T2_PACKAGING` (`ART_ID`,`VOLUME`,`WEIGHT_NETTO`,`WEIGHT_BRUTTO`) values ('$art_id','$volume','$weight','$weight2');"); }
                    $answer=1;$err="";
                } else {$answer=0;$err="Не заповнені всі поля для артикулу";}
            } else {$answer=0;$err="Переміщення заблоковано. Зміни вносити заборонено.";}
        }
        return array($answer,$err);
    }

    function newJmoving($type_id,$art_id,$storage_id_to,$status_jmoving,$oper_status,$jmov) {$db=DbSingleton::getDb(); session_start(); $user_id=$_SESSION["media_user_id"];
        $r=$db->query("select max(id) as mid from J_MOVING;");$jmoving_id=0+$db->result($r,0,"mid")+1;
        $doc_nom=$this->get_df_doc_nom_new();
        $db->query("insert into J_MOVING (`id`,`type_id`,`prefix`,`doc_nom`,`user_id`,`data`,`storage_id_to`,`status_jmoving`,`oper_status`) values ('$jmoving_id','$type_id','$this->prefix_new','$doc_nom','$user_id',CURDATE(),'$storage_id_to','$status_jmoving','$oper_status');");
        $db->query("update J_MOVING_STR set jmoving_id='$jmoving_id' where jmoving_id='$jmov' and art_id='$art_id';");
        return $jmoving_id;
    }

    function startJmovingStorageSelect($jmoving_id){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="";
        $jmoving_id=$slave->qq($jmoving_id);
        $r=$db->query("select oper_status, status_jmoving, storage_id_to from J_MOVING where id='$jmoving_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $oper_status=$db->result($r,0,"oper_status");
            $status_jmoving=$db->result($r,0,"status_jmoving");
            $storage_id_to=$db->result($r,0,"storage_id_to");
            if ($storage_id_to==0){$answer=0;$err="Не зазначено склад переміщення.";}
            if ($status_jmoving>47 || $oper_status>30){$answer=0;$err="Переміщення заблоковано. Зміни вносити заборонено.";}
            if ($oper_status==30 && $status_jmoving>=44 && $status_jmoving<=47 && $storage_id_to>0) {
                //*****$db->query("update J_MOVING set status_jmoving='45' where id='$jmoving_id';");
                $r1=$db->query("select storage_id_from from J_MOVING_STR where jmoving_id='$jmoving_id' and status_jmoving='44' group by storage_id_from,cell_id_from order by storage_id_from asc;");$n1=$db->num_rows($r1);
                if ($n1==0){ $answer=0;$err="Відсутній товар для створення відбору";}
                if($n1>0){
                    for ($i=1;$i<=$n1;$i++){
                        $storage_id_from=$db->result($r1,$i-1,"storage_id_from");
                        list($tpoint_id,$loc_type_id)=$this->getTpointDataByStorage($storage_id_from);
                        $sum_art_amount=0;$sum_amount=0;$sum_volume=0;$sum_weight_netto=0;$sum_weight_brutto=0;

                        $rm=$db->query("select max(id) as mid from J_MOVING_SELECT_TEMP;");$select_id=0+$db->result($rm,0,"mid")+1;
                        $db->query("insert into J_MOVING_SELECT_TEMP (`id`,`jmoving_id`,`tpoint_id`,`storage_id`,`loc_type_id`,`status_jmoving`) values ('$select_id','$jmoving_id','$tpoint_id','$storage_id_from','$loc_type_id','44');");

                        $ra=$db->query("select * from J_MOVING_STR where jmoving_id='$jmoving_id' and storage_id_from='$storage_id_from' and status_jmoving='44';");$na=$db->num_rows($ra);
                        for ($a=1;$a<=$na;$a++){
                            $art_id=$db->result($ra,$a-1,"art_id");
                            $article_nr_displ=$db->result($ra,$a-1,"article_nr_displ");
                            $brand_id=$db->result($ra,$a-1,"brand_id");
                            $amount=$db->result($ra,$a-1,"amount");
                            $cell_id_from=$db->result($ra,$a-1,"cell_id_from");

                            list($weight_brutto,$volume,$weight_netto)=$this->getArticleWightVolume($art_id);
                            $sum_amount+=$amount;$sum_art_amount+=1;$sum_volume+=($volume*$amount);$sum_weight_netto+=($weight_netto*$amount);$sum_weight_brutto+=($weight_brutto*$amount);

                            $db->query("insert into J_MOVING_SELECT_STR_TEMP (`jmoving_id`,`select_id`,`art_id`,`article_nr_displ`,`brand_id`,`amount`,`storage_id_from`,`cell_id_from`) values ('$jmoving_id','$select_id','$art_id','$article_nr_displ','$brand_id','$amount','$storage_id_from','$cell_id_from');");
                        }
                        //$ra=$db->query("select count(art_id) as art_amount, SUM(amount) as amount from J_MOVING_STR where jmoving_id='$jmoving_id' and storage_id_from='$storage_id_from';");
                        //$sum_art_amount=$db->result($ra,0,"art_amount")+0;
                        //$sum_amount=$db->result($ra,0,"amount")+0;
                        $db->query("update J_MOVING_SELECT_TEMP set `articles_amount`='$sum_art_amount',`amount`='$sum_amount',`volume`='$sum_volume',`weight_netto`='$sum_weight_netto',`weight_brutto`='$sum_weight_brutto' where id='$select_id' and '$jmoving_id'='$jmoving_id';");
                        //****$db->query("update J_MOVING_STR set status_jmoving='45', select_id='$select_id' where jmoving_id='$jmoving_id' and storage_id_from='$storage_id_from'  and status_jmoving='44';");
                    }
                    $answer=1;$err="";
                }
            }
        }
        return array($answer,$err);
    }

    function startJmovingStorageSelectLocal($jmoving_id){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="";
        $jmoving_id=$slave->qq($jmoving_id);
        $r=$db->query("select oper_status, status_jmoving, storage_id_to from J_MOVING where id='$jmoving_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $oper_status=$db->result($r,0,"oper_status");
            $status_jmoving=$db->result($r,0,"status_jmoving");
            $storage_id_to=$db->result($r,0,"storage_id_to");
            if ($storage_id_to==0){$answer=0;$err="Не зазначено склад переміщення.";}
            if ($status_jmoving>47 || $oper_status>30){$answer=0;$err="Переміщення заблоковано. Зміни вносити заборонено.";}
            if ($oper_status==30 && $status_jmoving>=44 && $status_jmoving<=47 && $storage_id_to>0) {
                $r1=$db->query("select storage_id_from, cell_id_from from J_MOVING_STR where jmoving_id='$jmoving_id' and status_jmoving='44' group by cell_id_from order by cell_id_from asc;");$n1=$db->num_rows($r1);
                if ($n1==0){ $answer=0;$err="Відсутній товар для створення відбору";}
                if($n1>0){
                    for ($i=1;$i<=$n1;$i++){
                        $storage_id_from=$db->result($r1,$i-1,"storage_id_from");
                        $cell_id_from=$db->result($r1,$i-1,"cell_id_from");
                        list($tpoint_id,$loc_type_id)=$this->getTpointDataByStorage($storage_id_from);$loc_type_id=1;
                        $sum_art_amount=0;$sum_amount=0;$sum_volume=0;$sum_weight_netto=0;$sum_weight_brutto=0;

                        $rm=$db->query("select max(id) as mid from J_MOVING_LOCAL_SELECT_TEMP;");$select_id=0+$db->result($rm,0,"mid")+1;
                        $db->query("insert into J_MOVING_LOCAL_SELECT_TEMP (`id`,`jmoving_id`,`tpoint_id`,`storage_id`,`loc_type_id`,`status_jmoving`) values ('$select_id','$jmoving_id','$tpoint_id','$storage_id_from','$loc_type_id','44');");

                        $ra=$db->query("select * from J_MOVING_STR where jmoving_id='$jmoving_id' and storage_id_from='$storage_id_from' and cell_id_from='$cell_id_from' and status_jmoving='44';");$na=$db->num_rows($ra);
                        for ($a=1;$a<=$na;$a++){
                            $art_id=$db->result($ra,$a-1,"art_id");
                            $article_nr_displ=$db->result($ra,$a-1,"article_nr_displ");
                            $brand_id=$db->result($ra,$a-1,"brand_id");
                            $amount=$db->result($ra,$a-1,"amount");
                            $cell_id_to=$db->result($ra,$a-1,"cell_id_to");

                            list($weight_brutto,$volume,$weight_netto)=$this->getArticleWightVolume($art_id);
                            $sum_amount+=$amount;$sum_art_amount+=1;$sum_volume+=($volume*$amount);$sum_weight_netto+=($weight_netto*$amount);$sum_weight_brutto+=($weight_brutto*$amount);

                            $db->query("insert into J_MOVING_LOCAL_SELECT_STR_TEMP (`jmoving_id`,`select_id`,`art_id`,`article_nr_displ`,`brand_id`,`amount`,`storage_id_from`,`cell_id_from`,`storage_id_to`,`cell_id_to`) values ('$jmoving_id','$select_id','$art_id','$article_nr_displ','$brand_id','$amount','$storage_id_from','$cell_id_from','$storage_id_from','$cell_id_to');");
                        }
                        $db->query("update J_MOVING_LOCAL_SELECT_TEMP set `articles_amount`='$sum_art_amount',`amount`='$sum_amount',`volume`='$sum_volume',`weight_netto`='$sum_weight_netto',`weight_brutto`='$sum_weight_brutto' where id='$select_id' and '$jmoving_id'='$jmoving_id';");
                    }
                    $answer=1;$err="";
                }
            }
        }
        return array($answer,$err);
    }

    function makesJmovingStorageSelect($jmoving_id){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;
        $answer=0;$err="";session_start();$user_id=$_SESSION["media_user_id"]; $jmoving_id=$slave->qq($jmoving_id);$storage_id_from=0;
        $r=$db->query("select oper_status, status_jmoving, storage_id_to from J_MOVING where id='$jmoving_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $oper_status=$db->result($r,0,"oper_status");
            $status_jmoving=$db->result($r,0,"status_jmoving");
            $storage_id_to=$db->result($r,0,"storage_id_to");
            if ($storage_id_to==0){$answer=0;$err="Не зазначено склад переміщення.";}
            if ($status_jmoving>47 || $oper_status>30){$answer=0;$err="Переміщення заблоковано. Зміни вносити заборонено.";}
            if ($oper_status==30 && $status_jmoving>=44 && $status_jmoving<=47 && $storage_id_to>0) {
                $db->query("update J_MOVING set status_jmoving='45' where id='$jmoving_id';");

                $rm=$db->query("select max(id) as mid from J_SELECT;");$select_id=0+$db->result($rm,0,"mid");

                $rm=$db->query("select * from J_MOVING_SELECT_TEMP where jmoving_id='$jmoving_id' and status_jmoving='44';");$nm=$db->num_rows($rm);
                for ($im=1;$im<=$nm;$im++){ $select_id+=1;
                    $select_id_t=$db->result($rm,$im-1,"id");
                    $tpoint_id=$db->result($rm,$im-1,"tpoint_id");
                    $storage_id=$db->result($rm,$im-1,"storage_id");
                    $articles_amount=$db->result($rm,$im-1,"articles_amount");
                    $amount=$db->result($rm,$im-1,"amount");
                    $volume=$db->result($rm,$im-1,"volume");
                    $weight_netto=$db->result($rm,$im-1,"weight_netto");
                    $weight_brutto=$db->result($rm,$im-1,"weight_brutto");
                    $cur_date=date("Y-m-d H:i:s");
                    $db->query("insert into J_SELECT (`id`,`parrent_doc_type_id`,`parrent_doc_id`,`data_create`,`tpoint_id`,`storage_id`,`articles_amount`,`amount`,`volume`,`weight_netto`,`weight_brutto`,`status_select`,`user_create`) values ('$select_id','1','$jmoving_id','$cur_date','$tpoint_id','$storage_id','$articles_amount','$amount','$volume','$weight_netto','$weight_brutto','82','$user_id');");
                    $db->query("delete from J_MOVING_SELECT_TEMP where jmoving_id='$jmoving_id' and id='$select_id_t';");

                    $this->addJuornalRecord($jmoving_id,$select_id,$status_jmoving);

                    $rm2=$db->query("select * from J_MOVING_SELECT_STR_TEMP where jmoving_id='$jmoving_id' and select_id='$select_id_t';");$nm2=$db->num_rows($rm2);
                    for ($im2=1;$im2<=$nm2;$im2++){
                        $id2=$db->result($rm2,$im2-1,"id");
                        $art_id=$db->result($rm2,$im2-1,"art_id");
                        $article_nr_displ=$db->result($rm2,$im2-1,"article_nr_displ");
                        $brand_id=$db->result($rm2,$im2-1,"brand_id");
                        $amount=$db->result($rm2,$im2-1,"amount");
                        $storage_id_from=$db->result($rm2,$im2-1,"storage_id_from");

                        $rsc=$dbt->query("select * from `T2_ARTICLES_STRORAGE_CELLS` where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_from';");$nsc=$dbt->num_rows($rsc);
                        if ($nsc>0){
                            for ($isc=1;$isc<=$nsc;$isc++){ $er=0;
                                $amount_sc=$dbt->result($rsc,$isc-1,"AMOUNT");
                                $reserv_amount_sc=$dbt->result($rsc,$isc-1,"RESERV_AMOUNT");
                                $storage_cells_id_sc=$dbt->result($rsc,$isc-1,"STORAGE_CELLS_ID");

                                if ($amount_sc>=$amount && $amount_sc>0){$isc=$nsc+1;$er=1;
                                    $amount_sc-=$amount;
                                    $reserv_amount_sc+=$amount;
                                    $db->query("insert into J_SELECT_STR (`select_id`,`art_id`,`article_nr_displ`,`brand_id`,`amount`,`storage_id_from`,`cell_id_from`,`status`) values ('$select_id','$art_id','$article_nr_displ','$brand_id','$amount','$storage_id_from','$storage_cells_id_sc',1);");
                                    $dbt->query("update `T2_ARTICLES_STRORAGE_CELLS` set `AMOUNT`='$amount_sc', `RESERV_AMOUNT`='$reserv_amount_sc' where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_from' and `STORAGE_CELLS_ID`='$storage_cells_id_sc' limit 1;");
                                }
                                if ($amount_sc<$amount && $amount_sc>0 && $er==0){
                                    $amount-=$amount_sc;
                                    $reserv_amount_sc+=$amount_sc;
                                    $db->query("insert into J_SELECT_STR (`select_id`,`art_id`,`article_nr_displ`,`brand_id`,`amount`,`storage_id_from`,`cell_id_from`,`status`) values ('$select_id','$art_id','$article_nr_displ','$brand_id','$amount_sc','$storage_id_from','$storage_cells_id_sc',1);");
                                    $dbt->query("update `T2_ARTICLES_STRORAGE_CELLS` set `AMOUNT`='0', `RESERV_AMOUNT`='$reserv_amount_sc' where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_from' and `STORAGE_CELLS_ID`='$storage_cells_id_sc' limit 1;");
                                }
                            }
                        }
                        if ($nsc==0){
                            $rsc2=$dbt->query("select * from `T2_ARTICLES_STRORAGE` where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_from' limit 0,1;");$nsc2=$dbt->num_rows($rsc2);
                            if ($nsc2==1){
                                $amount_sc=$dbt->result($rsc2,0,"AMOUNT");
                                //$reserv_amount_sc=$dbt->result($rsc2,0,"RESERV_AMOUNT");
                                if ($amount_sc>=$amount && $amount_sc>0){
                                    //$amount_sc-=$amount;
                                    //$reserv_amount_sc+=$amount;
                                    $db->query("insert into J_SELECT_STR (`select_id`,`art_id`,`article_nr_displ`,`brand_id`,`amount`,`storage_id_from`) values ('$select_id','$art_id','$article_nr_displ','$brand_id','$amount','$storage_id_from');");
                                    //$dbt->query("update `T2_ARTICLES_STRORAGE` set `AMOUNT`='$amount_sc', `RESERV_AMOUNT`='$reserv_amount_sc' where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_from' limit 1;");
                                }
                            }
                        }
                        $db->query("delete from J_MOVING_SELECT_STR_TEMP  where jmoving_id='$jmoving_id' and id='$id2';");
                    }
                    $db->query("update J_MOVING_STR set status_jmoving='45', select_id='$select_id' where jmoving_id='$jmoving_id' and storage_id_from='$storage_id_from'  and status_jmoving='44';");
                }
                $answer=1;$err="";
            }
        }
        return array($answer,$err);
    }

    /*function makesJmovingStorageSelect($jmoving_id){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;$cat=new catalogue;$answer=0;$err="";session_start();$user_id=$_SESSION["media_user_id"];
        $jmoving_id=$slave->qq($jmoving_id);
        $r=$db->query("select oper_status,status_jmoving,storage_id_to from J_MOVING where id='$jmoving_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $oper_status=$db->result($r,0,"oper_status");
            $status_jmoving=$db->result($r,0,"status_jmoving");
            $storage_id_to=$db->result($r,0,"storage_id_to");
            if ($storage_id_to==0){$answer=0;$err="Не зазначено склад переміщення.";}
            if ($status_jmoving>47 || $oper_status>30){$answer=0;$err="Переміщення заблоковано. Зміни вносити заборонено.";}
            if ($oper_status==30 && $status_jmoving>=44 && $status_jmoving<=47 && $storage_id_to>0) {
                $db->query("update J_MOVING set status_jmoving='45' where id='$jmoving_id';");


                $rm=$db->query("select max(id) as mid from J_MOVING_SELECT;");$select_id=0+$db->result($rm,0,"mid");

                $rm=$db->query("select * from J_MOVING_SELECT_TEMP where jmoving_id='$jmoving_id' and status_jmoving='44';");$nm=$db->num_rows($rm);
                for ($im=1;$im<=$nm;$im++){ $select_id+=1;
                    $select_id_t=$db->result($rm,$im-1,"id");
                    $tpoint_id=$db->result($rm,$im-1,"tpoint_id");
                    $storage_id=$db->result($rm,$im-1,"storage_id");
                    $loc_type_id=$db->result($rm,$im-1,"loc_type_id");
                    $articles_amount=$db->result($rm,$im-1,"articles_amount");
                    $amount=$db->result($rm,$im-1,"amount");
                    $volume=$db->result($rm,$im-1,"volume");
                    $weight_netto=$db->result($rm,$im-1,"weight_netto");
                    $weight_brutto=$db->result($rm,$im-1,"weight_brutto");
                    $cur_date=date("Y-m-d");
                    $db->query("insert into J_MOVING_SELECT (`id`,`jmoving_id`,`data`,`tpoint_id`,`storage_id`,`loc_type_id`,`articles_amount`,`amount`,`volume`,`weight_netto`,`weight_brutto`,`status_jmoving`) values ('$select_id','$jmoving_id','$cur_date','$tpoint_id','$storage_id','$loc_type_id','$articles_amount','$amount','$volume','$weight_netto','$weight_brutto','45');");
                    $db->query("delete from J_MOVING_SELECT_TEMP  where jmoving_id='$jmoving_id' and id='$select_id_t';");

                    $this->addJuornalRecord($jmoving_id,$select_id,$status_jmoving);

                    $rm2=$db->query("select * from J_MOVING_SELECT_STR_TEMP where jmoving_id='$jmoving_id' and select_id='$select_id_t';");$nm2=$db->num_rows($rm2);
                    for ($im2=1;$im2<=$nm2;$im2++){
                        $id2=$db->result($rm2,$im2-1,"id");
                        $art_id=$db->result($rm2,$im2-1,"art_id");
                        $article_nr_displ=$db->result($rm2,$im2-1,"article_nr_displ");
                        $brand_id=$db->result($rm2,$im2-1,"brand_id");
                        $amount=$db->result($rm2,$im2-1,"amount");
                        $storage_id_from=$db->result($rm2,$im2-1,"storage_id_from");
    //					$cell_id_from=$db->result($rm2,$im2-1,"cell_id_from");

                        $rsc=$dbt->query("select * from `T2_ARTICLES_STRORAGE_CELLS` where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_from';");$nsc=$dbt->num_rows($rsc);
                        if ($nsc>0){
                            for ($isc=1;$isc<=$nsc;$isc++){ $er=0;
                                $amount_sc=$dbt->result($rsc,$isc-1,"AMOUNT");
                                $reserv_amount_sc=$dbt->result($rsc,$isc-1,"RESERV_AMOUNT");
                                $storage_cells_id_sc=$dbt->result($rsc,$isc-1,"STORAGE_CELLS_ID");

                                if ($amount_sc>=$amount && $amount_sc>0){$isc=$nsc+1;$er=1;
                                    $amount_sc-=$amount;
                                    $reserv_amount_sc+=$amount;
                                    $db->query("insert into J_MOVING_SELECT_STR (`jmoving_id`,`select_id`,`art_id`,`article_nr_displ`,`brand_id`,`amount`,`storage_id_from`,`cell_id_from`) values ('$jmoving_id','$select_id','$art_id','$article_nr_displ','$brand_id','$amount','$storage_id_from','$storage_cells_id_sc');");
                                    $dbt->query("update `T2_ARTICLES_STRORAGE_CELLS` set `AMOUNT`='$amount_sc', `RESERV_AMOUNT`='$reserv_amount_sc' where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_from' and `STORAGE_CELLS_ID`='$storage_cells_id_sc' limit 1;");
                                }
                                if ($amount_sc<$amount && $amount_sc>0 && $er==0){
                                    $amount-=$amount_sc;
                                    $reserv_amount_sc+=$amount_sc;
                                    $db->query("insert into J_MOVING_SELECT_STR (`jmoving_id`,`select_id`,`art_id`,`article_nr_displ`,`brand_id`,`amount`,`storage_id_from`,`cell_id_from`) values ('$jmoving_id','$select_id','$art_id','$article_nr_displ','$brand_id','$amount_sc','$storage_id_from','$storage_cells_id_sc');");
                                    $dbt->query("update `T2_ARTICLES_STRORAGE_CELLS` set `AMOUNT`='0', `RESERV_AMOUNT`='$reserv_amount_sc' where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_from' and `STORAGE_CELLS_ID`='$storage_cells_id_sc' limit 1;");
                                }
                            }
                        }
                        if ($nsc==0){
                            $rsc2=$dbt->query("select * from `T2_ARTICLES_STRORAGE` where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_from' limit 0,1;");$nsc2=$dbt->num_rows($rsc2);
                            if ($nsc2==1){
                                $amount_sc=$dbt->result($rsc2,0,"AMOUNT");
                                $reserv_amount_sc=$dbt->result($rsc2,0,"RESERV_AMOUNT");

                                if ($amount_sc>=$amount && $amount_sc>0){
                                    $amount_sc-=$amount;
                                    $reserv_amount_sc+=$amount;
                                    $db->query("insert into J_MOVING_SELECT_STR (`jmoving_id`,`select_id`,`art_id`,`article_nr_displ`,`brand_id`,`amount`,`storage_id_from`) values ('$jmoving_id','$select_id','$art_id','$article_nr_displ','$brand_id','$amount','$storage_id_from');");
                                    //$dbt->query("update `T2_ARTICLES_STRORAGE` set `AMOUNT`='$amount_sc', `RESERV_AMOUNT`='$reserv_amount_sc' where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_from' limit 1;");
                                }
                            }
                        }
                        $db->query("delete from J_MOVING_SELECT_STR_TEMP  where jmoving_id='$jmoving_id' and id='$id2';");
                    }
                    $db->query("update J_MOVING_STR set status_jmoving='45', select_id='$select_id' where jmoving_id='$jmoving_id' and storage_id_from='$storage_id_from'  and status_jmoving='44';");
                }
                $answer=1;$err="";
            }
        }
        return array($answer,$err);
    }*/

    function makesJmovingStorageSelectLocal($jmoving_id){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="";
        $jmoving_id=$slave->qq($jmoving_id);$storage_id_from=$cell_id_from=0;
        $r=$db->query("select oper_status, status_jmoving, storage_id_to from J_MOVING where id='$jmoving_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $oper_status=$db->result($r,0,"oper_status");
            $status_jmoving=$db->result($r,0,"status_jmoving");
            $storage_id_to=$db->result($r,0,"storage_id_to");
            if ($storage_id_to==0){$answer=0;$err="Не зазначено склад переміщення.";}
            if ($status_jmoving>47 || $oper_status>30){$answer=0;$err="Переміщення заблоковано. Зміни вносити заборонено.";}
            if ($oper_status==30 && $status_jmoving>=44 && $status_jmoving<=47 && $storage_id_to>0) {
                $db->query("update J_MOVING set status_jmoving='45' where id='$jmoving_id';");
                $rm=$db->query("select max(id) as mid from J_MOVING_SELECT;");$select_id=0+$db->result($rm,0,"mid");
                $rm=$db->query("select * from J_MOVING_LOCAL_SELECT_TEMP where jmoving_id='$jmoving_id' and status_jmoving='44';");$nm=$db->num_rows($rm);
                for ($im=1;$im<=$nm;$im++){ $select_id+=1;
                    $select_id_t=$db->result($rm,$im-1,"id");
                    $tpoint_id=$db->result($rm,$im-1,"tpoint_id");
                    $storage_id=$db->result($rm,$im-1,"storage_id");
                    $loc_type_id=$db->result($rm,$im-1,"loc_type_id");
                    $articles_amount=$db->result($rm,$im-1,"articles_amount");
                    $amount=$db->result($rm,$im-1,"amount");
                    $volume=$db->result($rm,$im-1,"volume");
                    $weight_netto=$db->result($rm,$im-1,"weight_netto");
                    $weight_brutto=$db->result($rm,$im-1,"weight_brutto");
                    $cur_date=date("Y-m-d");
                    $db->query("insert into J_MOVING_SELECT (`id`,`jmoving_id`,`data`,`tpoint_id`,`storage_id`,`loc_type_id`,`articles_amount`,`amount`,`volume`,`weight_netto`,`weight_brutto`,`status_jmoving`) values ('$select_id','$jmoving_id','$cur_date','$tpoint_id','$storage_id','$loc_type_id','$articles_amount','$amount','$volume','$weight_netto','$weight_brutto','45');");
                    $db->query("delete from J_MOVING_LOCAL_SELECT_TEMP where jmoving_id='$jmoving_id' and id='$select_id_t';");

                    $this->addJuornalRecord($jmoving_id,$select_id,$status_jmoving);

                    $rm2=$db->query("select * from J_MOVING_LOCAL_SELECT_STR_TEMP where jmoving_id='$jmoving_id' and select_id='$select_id_t';");$nm2=$db->num_rows($rm2);
                    for ($im2=1;$im2<=$nm2;$im2++){
                        $id2=$db->result($rm2,$im2-1,"id");
                        $art_id=$db->result($rm2,$im2-1,"art_id");
                        $article_nr_displ=$db->result($rm2,$im2-1,"article_nr_displ");
                        $brand_id=$db->result($rm2,$im2-1,"brand_id");
                        $amount=$db->result($rm2,$im2-1,"amount");
                        $storage_id_from=$db->result($rm2,$im2-1,"storage_id_from");
                        $cell_id_from=$db->result($rm2,$im2-1,"cell_id_from");
                        $cell_id_to=$db->result($rm2,$im2-1,"cell_id_to");

                        $db->query("insert into J_MOVING_SELECT_STR (`jmoving_id`,`select_id`,`art_id`,`article_nr_displ`,`brand_id`,`amount`,`storage_id_from`,`cell_id_from`,`storage_id_to`,`cell_id_to`,`status`) values ('$jmoving_id','$select_id','$art_id','$article_nr_displ','$brand_id','$amount','$storage_id_from','$cell_id_from','$storage_id_from','$cell_id_to',1);");
                        /*$rsc=$dbt->query("select * from `T2_ARTICLES_STRORAGE_CELLS` where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_from' and `STORAGE_CELLS_ID`='$cell_id_from';");$nsc=$dbt->num_rows($rsc);
                        print "select * from `T2_ARTICLES_STRORAGE_CELLS` where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_from' and `STORAGE_CELLS_ID`='$cell_id_from'; nsc=$nsc\n";
                        if ($nsc>0){
                            for ($isc=1;$isc<=$nsc;$isc++){ $er=0;
                                $amount_sc=$dbt->result($rsc,$isc-1,"AMOUNT");
                                $reserv_amount_sc=$dbt->result($rsc,$isc-1,"RESERV_AMOUNT");
                                print "$amount_sc>=$amount && $amount_sc>0";
                                //if ($amount_sc>=$amount && $amount_sc>0){$isc=$nsc+1;$er=1;
                                    $amount_sc-=$amount;
                                    $reserv_amount_sc+=$amount;
                                    //$db->query("insert into J_MOVING_SELECT_STR (`jmoving_id`,`select_id`,`art_id`,`article_nr_displ`,`brand_id`,`amount`,`storage_id_from`,`cell_id_from`,`storage_id_to`,`cell_id_to`) values ('$jmoving_id','$select_id','$art_id','$article_nr_displ','$brand_id','$amount','$storage_id_from','$cell_id_from','$storage_id_from','$cell_id_to');");
                                    //print "insert into J_MOVING_SELECT_STR (`jmoving_id`,`select_id`,`art_id`,`article_nr_displ`,`brand_id`,`amount`,`storage_id_from`,`cell_id_from`,`storage_id_to`,`cell_id_to`) values ('$jmoving_id','$select_id','$art_id','$article_nr_displ','$brand_id','$amount','$storage_id_from','$cell_id_from','$storage_id_from','$cell_id_to');\n";
                                    //$dbt->query("update `T2_ARTICLES_STRORAGE_CELLS` set `AMOUNT`='$amount_sc', `RESERV_AMOUNT`='$reserv_amount_sc' where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_from' and `STORAGE_CELLS_ID`='$cell_id_from' limit 1;");
                                //}
                                /*if ($amount_sc<$amount && $amount_sc>0 && $er==0){
                                    $amount-=$amount_sc;
                                    $reserv_amount_sc+=$amount_sc;
                                    $db->query("insert into J_MOVING_SELECT_STR (`jmoving_id`,`select_id`,`art_id`,`article_nr_displ`,`brand_id`,`amount`,`storage_id_from`,`cell_id_from`,`storage_id_to`,`cell_id_to`) values ('$jmoving_id','$select_id','$art_id','$article_nr_displ','$brand_id','$amount_sc','$storage_id_from','$cell_id_from','$storage_id_from','$cell_id_to');");
                                    $dbt->query("update `T2_ARTICLES_STRORAGE_CELLS` set `AMOUNT`='0', `RESERV_AMOUNT`='$reserv_amount_sc' where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_from' and `STORAGE_CELLS_ID`='$cell_id_from' limit 1;");
                                }*/
                            //}
                        //}
                        $db->query("delete from J_MOVING_LOCAL_SELECT_STR_TEMP  where jmoving_id='$jmoving_id' and id='$id2';");
                    }
                    $db->query("update J_MOVING_STR set status_jmoving='45', select_id='$select_id' where jmoving_id='$jmoving_id' and `storage_id_from`='$storage_id_from' and `cell_id_from`='$cell_id_from' and status_jmoving='44';");
                }
                $answer=1;$err="";
            }
        }
        return array($answer,$err);
    }

    function clearJmovingStorageSelect($jmoving_id){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="";
        $jmoving_id=$slave->qq($jmoving_id);
        $r=$db->query("select oper_status, status_jmoving, storage_id_to from J_MOVING where id='$jmoving_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $oper_status=$db->result($r,0,"oper_status");
            $status_jmoving=$db->result($r,0,"status_jmoving");
            $storage_id_to=$db->result($r,0,"storage_id_to");
            if ($storage_id_to==0){$answer=0;$err="Не зазначено склад переміщення.";}
            if ($status_jmoving>47 || $oper_status>30){$answer=0;$err="Переміщення заблоковано. Зміни вносити заборонено.";}
            if ($oper_status==30 && $status_jmoving>=44 && $status_jmoving<=47 && $storage_id_to>0) {
                $db->query("delete from J_MOVING_SELECT_TEMP where jmoving_id='$jmoving_id' and status_jmoving='44';");
                $db->query("delete from J_MOVING_SELECT_STR_TEMP  where jmoving_id='$jmoving_id';");
                $answer=1;$err="";
            }
        }
        return array($answer,$err);
    }

    function clearJmovingStorageSelectLocal($jmoving_id){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="";
        $jmoving_id=$slave->qq($jmoving_id);
        $r=$db->query("select oper_status, status_jmoving, storage_id_to from J_MOVING where id='$jmoving_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $oper_status=$db->result($r,0,"oper_status");
            $status_jmoving=$db->result($r,0,"status_jmoving");
            $storage_id_to=$db->result($r,0,"storage_id_to");
            if ($storage_id_to==0){$answer=0;$err="Не зазначено склад переміщення.";}
            if ($status_jmoving>47 || $oper_status>30){$answer=0;$err="Переміщення заблоковано. Зміни вносити заборонено.";}
            if ($oper_status==30 && $status_jmoving>=44 && $status_jmoving<=47 && $storage_id_to>0) {
                $db->query("delete from J_MOVING_LOCAL_SELECT_TEMP where jmoving_id='$jmoving_id' and status_jmoving='44';");
                $db->query("delete from J_MOVING_LOCAL_SELECT_STR_TEMP  where jmoving_id='$jmoving_id';");
                $answer=1;$err="";
            }
        }
        return array($answer,$err);
    }

    function loadJmovingStorageSelect($jmoving_id,$jmoving_status){$db=DbSingleton::getDb();
        $form="";$form_htm=RD."/tpl/jmoving_storage_select_list.htm";if ($jmoving_status==45){$form_htm=RD."/tpl/jmoving_storage_select_list_finish.htm";}
        if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select status_jmoving from J_MOVING j where j.id='$jmoving_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==0){$form_htm=RD."/tpl/access_deny.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);} }
        if ($n==1){
            list($list,$kol_rows)=$this->showJmovingSkladStorageSelectList($jmoving_id,$jmoving_status);
            $form=str_replace("{SkladStorageSelectList}",$list,$form);
            $form=str_replace("{kol_rows}",$kol_rows,$form);
            $form=str_replace("{jmoving_id}",$jmoving_id,$form);
            if ($kol_rows==1) {$kol_status2=" disabled "; $kol_status1="";} else {$kol_status1=" disabled "; $kol_status2="";}
            $form=str_replace("{kol_status1}",$kol_status1,$form);
            $form=str_replace("{kol_status2}",$kol_status2,$form);
        }
        return $form;
    }

    /*
    function showJmovingSkladStorageSelectList($jmoving_id,$jmoving_status){$db=DbSingleton::getDb();$slave=new slave;$cat=new catalogue;$manual=new manual;$gmanual=new gmanual;$list="";
        $tmp="";$where_status="and ms.status_jmoving='$jmoving_status'"; if ($jmoving_status==44){$tmp="_TEMP";} if ($jmoving_status>44){ $where_status="and ms.status_jmoving in (45,46,47,48)"; }
        $r=$db->query("select ms.*, p.name as tpoint_name, s.name as storage_name, ml.mcaption as loc_type_name, mt.mcaption as status_jmoving_name from J_MOVING_SELECT$tmp ms
                        left outer join T_POINT p on p.id=ms.tpoint_id
                        left outer join STORAGE s on s.id=ms.storage_id
                        left outer join manual ml on ml.id=ms.loc_type_id
                        left outer join manual mt on mt.id=ms.status_jmoving
                        where ms.jmoving_id='$jmoving_id' $where_status and ms.status='1' order by ms.id asc;");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $tpoint_id=$db->result($r,$i-1,"tpoint_id");
            $tpoint_name=$db->result($r,$i-1,"tpoint_name");
            $storage_id=$db->result($r,$i-1,"storage_id");
            $storage_name=$db->result($r,$i-1,"storage_name");
            $loc_type_id=$db->result($r,$i-1,"loc_type_id");
            $loc_type_name=$db->result($r,$i-1,"loc_type_name");
            $articles_amount=$db->result($r,$i-1,"articles_amount");
            $amount=$db->result($r,$i-1,"amount");
            $volume=$db->result($r,$i-1,"volume");
            $weight_netto=$db->result($r,$i-1,"weight_netto");
            $weight_brutto=$db->result($r,$i-1,"weight_brutto");
            $status_jmoving=$db->result($r,$i-1,"status_jmoving");
            $status_jmoving_name=$db->result($r,$i-1,"status_jmoving_name");

            $list.="<tr id='strStsRow_$i'>
                <td>$i</td>";
                if ($jmoving_status>44){
                    $list.="<td style='min-width:140px;'>СкВ-$id</td>";
                }
            $list.="<td style='min-width:140px;'>$tpoint_name</td>
                <td style='min-width:120px;'>$storage_name</td>
                <td style='min-width:80px;'>$loc_type_name</td>
                <td align='center' style='min-width:80px;'>$articles_amount</td>
                <td align='center' style='min-width:80px;'>$amount</td>
                <td align='right'>$volume</td>
                <td align='right'>$weight_netto</td>
                <td align='right'>$weight_brutto</td>";
                if ($jmoving_status==44){
                    $list.="<td align='center'><button class='btn btn-xs btn-warning' onClick='cutJmovingStorage(\"$jmoving_id\",\"$id\");'><i class='fa fa-cut'></i></button></td>";
                }
                $list.="<td align='center'><button class='btn btn-xs btn-primary' onClick='viewJmovingStorageSelect(\"$jmoving_id\",\"$id\",$status_jmoving);'><i class='fa fa-eye'></i></button></td>";
                if ($jmoving_status==44){
                    $list.="<td align='center'><button class='btn btn-xs btn-danger' onClick='dropJmovingStorageSelect(\"$jmoving_id\",\"$id\");'><i class='fa fa-trash'></i></button></td>";
                }
            $list.="
                <td align='center'>$status_jmoving_name</td>

            </tr>";

        }
        return array($list,$n);
    }*/

    function loadJmovingStorageCount($jmoving_id) {$db=DbSingleton::getDb();
        $r=$db->query("select sel.*, s.name as storage_name, t.name as tpoint_name from J_SELECT sel
            left outer join T_POINT t on t.id=sel.tpoint_id
            left outer join STORAGE s on s.id=sel.storage_id
        where sel.status=1 and sel.parrent_doc_type_id='1' and sel.parrent_doc_id='$jmoving_id' 
        order by sel.status_select asc, sel.data_create desc, sel.id desc;"); $n=$db->num_rows($r);
        if ($n==0) {
            $r=$db->query("select ms.*, p.name as tpoint_name, s.name as storage_name, ml.mcaption as loc_type_name, mt.mcaption as status_jmoving_name 
            from J_MOVING_SELECT_TEMP ms 
                left outer join T_POINT p on p.id=ms.tpoint_id
                left outer join STORAGE s on s.id=ms.storage_id
                left outer join manual ml on ml.id=ms.loc_type_id
                left outer join manual mt on mt.id=ms.status_jmoving
            where ms.jmoving_id='$jmoving_id' and ms.status_jmoving='44' and ms.status='1' order by ms.id asc;");
            $n=$db->num_rows($r);
        }
        return $n;
    }

    function showJmovingSkladStorageSelectList($jmoving_id,$jmoving_status){$db=DbSingleton::getDb();$gmanual=new gmanual;$list="";$n=0;
        if ($jmoving_status==44){
            $r=$db->query("select ms.*, p.name as tpoint_name, s.name as storage_name, ml.mcaption as loc_type_name, mt.mcaption as status_jmoving_name 
            from J_MOVING_SELECT_TEMP ms 
                left outer join T_POINT p on p.id=ms.tpoint_id
                left outer join STORAGE s on s.id=ms.storage_id
                left outer join manual ml on ml.id=ms.loc_type_id
                left outer join manual mt on mt.id=ms.status_jmoving
            where ms.jmoving_id='$jmoving_id' and ms.status_jmoving='$jmoving_status' and ms.status='1' order by ms.id asc;");$n=$db->num_rows($r);

            for ($i=1;$i<=$n;$i++){
                $id=$db->result($r,$i-1,"id");
                $tpoint_name=$db->result($r,$i-1,"tpoint_name");
                $storage_name=$db->result($r,$i-1,"storage_name");
                $loc_type_name=$db->result($r,$i-1,"loc_type_name");
                $articles_amount=$db->result($r,$i-1,"articles_amount");
                $amount=$db->result($r,$i-1,"amount");
                $volume=$db->result($r,$i-1,"volume");
                $weight_netto=$db->result($r,$i-1,"weight_netto");
                $weight_brutto=$db->result($r,$i-1,"weight_brutto");
                $status_jmoving=$db->result($r,$i-1,"status_jmoving");
                $status_jmoving_name=$db->result($r,$i-1,"status_jmoving_name");

                $list.="<tr id='strStsRow_$i'>
                    <td><span class='select_id' id='$id'>$i</span></td>
                    <td style='min-width:140px;'>$tpoint_name</td>
                    <td style='min-width:120px;'>$storage_name</td>
                    <td style='min-width:80px;'>$loc_type_name</td>
                    <td align='center' style='min-width:80px;'>$articles_amount</td>
                    <td align='center' style='min-width:80px;'>$amount</td>
                    <td align='right'>$volume</td>
                    <td align='right'>$weight_netto</td>
                    <td align='right'>$weight_brutto</td>
                    <td align='center'><button class='btn btn-xs btn-warning' onClick='cutJmovingStorage(\"$jmoving_id\",\"$id\");'><i class='fa fa-cut'></i></button></td>
                    <td align='center'><button class='btn btn-xs btn-primary' onClick='viewJmovingStorageSelect(\"$jmoving_id\",\"$id\",$status_jmoving);'><i class='fa fa-eye'></i></button></td>
                    <td align='center'><button class='btn btn-xs btn-danger' onClick='dropJmovingStorageSelect(\"$jmoving_id\",\"$id\");'><i class='fa fa-trash'></i></button></td>
                    <td align='center'>$status_jmoving_name</td>
                </tr>";
            }
        }
        if ($jmoving_status>44){ $loc_type_name="";
            $r=$db->query("select sel.*, s.name as storage_name, t.name as tpoint_name from J_SELECT sel
                left outer join T_POINT t on t.id=sel.tpoint_id
                left outer join STORAGE s on s.id=sel.storage_id
            where sel.status=1 and sel.parrent_doc_type_id='1' and sel.parrent_doc_id='$jmoving_id' order by sel.status_select asc, sel.data_create desc, sel.id desc;");
            $n=$db->num_rows($r);
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
                    <td><span class='select_id' id='$id'>$i</span></td>
                    <td style='min-width:140px;'>СкВ-$id</td>
                    <td style='min-width:140px;'>$tpoint_name</td>
                    <td style='min-width:120px;'>$storage_name</td>
                    <td style='min-width:80px;'>$loc_type_name</td>
                    <td align='center' style='min-width:80px;'>$articles_amount</td>
                    <td align='center' style='min-width:80px;'>$amount</td>
                    <td align='right'>$volume</td>
                    <td align='right'>$weight_netto</td>
                    <td align='right'>$weight_brutto</td>
                    <td align='center'><button class='btn btn-xs btn-primary' onClick='viewJmovingStorageSelect(\"$jmoving_id\",\"$id\",\"$status_select\");'><i class='fa fa-eye'></i></button></td>
                    <td align='center'>$status_select_cap</td>
                </tr>";
            }
        }
        return array($list,$n);
    }

    function loadJmovingStorageSelectLocal($jmoving_id,$jmoving_status){$db=DbSingleton::getDb();
        $form="";$form_htm=RD."/tpl/jmoving_local_storage_select_list.htm";if ($jmoving_status==45){$form_htm=RD."/tpl/jmoving_local_storage_select_list_finish.htm";}
        if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select status_jmoving from J_MOVING j where j.id='$jmoving_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==0){$form_htm=RD."/tpl/access_deny.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);} }
        if ($n==1){
            list($list,$kol_rows)=$this->showJmovingSkladStorageSelectListLocal($jmoving_id,$jmoving_status);
            $form=str_replace("{SkladStorageSelectList}",$list,$form);
            $form=str_replace("{kol_rows}",$kol_rows,$form);
            $form=str_replace("{jmoving_id}",$jmoving_id,$form);
            if ($kol_rows==1) {$kol_status2=" disabled "; $kol_status1="";} else {$kol_status1=" disabled "; $kol_status2="";}
            $form=str_replace("{kol_status1}",$kol_status1,$form);
            $form=str_replace("{kol_status2}",$kol_status2,$form);
        }
        return $form;
    }

    function showJmovingSkladStorageSelectListLocal($jmoving_id,$jmoving_status){$db=DbSingleton::getDb();$list="";
        $tmp="J_MOVING_SELECT";$where_status="and ms.status_jmoving='$jmoving_status'"; if ($jmoving_status==44){$tmp="J_MOVING_LOCAL_SELECT_TEMP";} if ($jmoving_status>44){ $where_status="and ms.status_jmoving in (45,46,47,48)"; }
        $query="select ms.*, p.name as tpoint_name, s.name as storage_name, mt.mcaption as status_jmoving_name from $tmp ms 
            left outer join T_POINT p on p.id=ms.tpoint_id
            left outer join STORAGE s on s.id=ms.storage_id
            left outer join manual mt on mt.id=ms.status_jmoving
        where ms.jmoving_id='$jmoving_id' $where_status and ms.status='1' order by ms.id asc;";
        $r=$db->query($query);$n=$db->num_rows($r);

        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $tpoint_name=$db->result($r,$i-1,"tpoint_name");
            $storage_name=$db->result($r,$i-1,"storage_name");
            $articles_amount=$db->result($r,$i-1,"articles_amount");
            $amount=$db->result($r,$i-1,"amount");
            $volume=$db->result($r,$i-1,"volume");
            $weight_netto=$db->result($r,$i-1,"weight_netto");
            $weight_brutto=$db->result($r,$i-1,"weight_brutto");
            $status_jmoving=$db->result($r,$i-1,"status_jmoving");
            $status_jmoving_name=$db->result($r,$i-1,"status_jmoving_name");

            $list.="<tr id='strStsRow_$i'><td>$i</td>";
            if ($jmoving_status>44){
                $list.="<td style='min-width:140px;'>СкВн-$id</td>";
            }
            $list.="<td style='min-width:140px;'>$tpoint_name</td>
                <td style='min-width:120px;'>$storage_name</td>
                <td align='center' style='min-width:80px;'>$articles_amount</td>
                <td align='center' style='min-width:80px;'>$amount</td>
                <td align='right'>$volume</td>
                <td align='right'>$weight_netto</td>
                <td align='right'>$weight_brutto</td>";
                $list.="<td align='center'><button class='btn btn-xs btn-primary' onClick='viewJmovingStorageSelectLocal(\"$jmoving_id\",\"$id\",$status_jmoving);'><i class='fa fa-eye'></i></button></td>";
                if ($jmoving_status==44){
                    $list.="<td align='center'><button class='btn btn-xs btn-danger' onClick='dropJmovingStorageSelectLocal(\"$jmoving_id\",\"$id\");'><i class='fa fa-trash'></i></button></td>";
                }
            $list.="<td align='center'>$status_jmoving_name</td></tr>";
        }
        return array($list,$n);
    }

    function getTpointDataByStorage($storage_id){$db=DbSingleton::getDb(); $tpoint_id=0;$loc_type_id=0;
        $r=$db->query("select `tpoint_id`,`local` from T_POINT_STORAGE where storage_id='$storage_id' order by id asc limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){	$tpoint_id=$db->result($r,0,"tpoint_id"); $loc_type_id=$db->result($r,0,"local");}
        return array($tpoint_id,$loc_type_id);
    }

    function viewJmovingStorageSelect($jmoving_id,$select_id,$jmoving_status){$db=DbSingleton::getDb();$cat=new catalogue;$gmanual=new gmanual;$select_status=0; $form=""; $list="";
        if ($jmoving_status==44){
            $form_htm=RD."/tpl/jmoving_storage_select_view.htm"; $tmp="_TEMP";
            $disabled46=" disabled";$disabled47=" disabled";$disabled48=" disabled";
            if ($jmoving_status==45){$disabled46=" ";}
            if ($jmoving_status==46){$disabled47=" ";}
            if ($jmoving_status==47 || $jmoving_status==48){
                $disabled48=" ";
                $form_htm=RD."/tpl/jmoving_storage_select_view_finish.htm";
            }
            if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}

            $r=$db->query("select * from J_MOVING_SELECT_STR$tmp  where jmoving_id='$jmoving_id' and select_id='$select_id' order by id asc;");$n=$db->num_rows($r);
            for ($i=1;$i<=$n;$i++){
                    $id=$db->result($r,$i-1,"id");
                    $art_id=$db->result($r,$i-1,"art_id");
                    $article_nr_displ=$db->result($r,$i-1,"article_nr_displ");
                    $brand_id=$db->result($r,$i-1,"brand_id");$brand_name=$cat->getBrandName($brand_id);
                    $amount=$db->result($r,$i-1,"amount");
                    $storage_id_from=$db->result($r,$i-1,"storage_id_from");
                    $storage_name_from=$this->getStorageName($storage_id_from);
                    $list47="";
                    if ($jmoving_status==47 || $jmoving_status==48){
                        $amount_barcodes=$db->result($r,$i-1,"amount_barcodes");
                        $amount_barcodes_noscan=$db->result($r,$i-1,"amount_barcodes_noscan"); $amount_accept=$amount_barcodes+$amount_barcodes_noscan;
                        $select_bug_list=$this->getStorageSelectBugList($jmoving_id,$select_id,$art_id,$id);
                        $amount_bug=$db->result($r,$i-1,"amount_bug");
                        $list47="<td>$amount_accept</td>
                        <td>$amount_bug</td>
                        <td>$select_bug_list</td>";
                    }
                    $list.="<tr align='right'>
                        <td align='left'>$i</td>
                        <td align='left'>$article_nr_displ</td>
                        <td align='center'>$brand_name</td>
                        <td>$storage_name_from</td>
                        <td>$amount</td>
                        $list47
                    </tr>";
            }
            list($select_nom,$select_data,$storage_id,$storage_name,$storage_name_to,$articles_amount,$amount,$volume,$weight_netto,$weight_brutto,$jmoving_comment,$select_datatime)=$this->getJmovingSkladStorageSelectInfo($jmoving_id,$select_id);
            $form=str_replace("{select_start}",$select_datatime,$form);
            $form=str_replace("{ArticlesList}",$list,$form);
            $form=str_replace("{select_id}",$select_id,$form);
            $form=str_replace("{jmoving_id}",$jmoving_id,$form);
            $form=str_replace("{disabled46}",$disabled46,$form);
            $form=str_replace("{disabled47}",$disabled47,$form);
            $form=str_replace("{disabled48}",$disabled48,$form);
            if ($jmoving_status==44){
                $form=str_replace("{preview-hidden}","hidden disabled",$form);
            }
            $form=str_replace("{preview-hidden}","",$form);
            $data_records=$this->getJmovingSelectJournalRecords($jmoving_id,$select_id);
            $form=str_replace("{data_46}",$data_records[46],$form);
            $form=str_replace("{data_52}",$data_records[52],$form);
            $form=str_replace("{data_47}",$data_records[47],$form);
            $form=str_replace("{data_48}",$data_records[48],$form);
        }
        if ($jmoving_status>44){ $storsel=new storsel; $select_status=$jmoving_status;$gmanual=new gmanual;
            $form_htm=RD."/tpl/jmoving_storage_select_view_finish.htm";	if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
            $r=$db->query("select * from J_SELECT_STR where select_id='$select_id' and status=1 order by id asc;");$n=$db->num_rows($r);
            for ($i=1;$i<=$n;$i++){
                $id=$db->result($r,$i-1,"id");
                $art_id=$db->result($r,$i-1,"art_id");
                $article_nr_displ=$db->result($r,$i-1,"article_nr_displ");
                $brand_id=$db->result($r,$i-1,"brand_id");$brand_name=$cat->getBrandName($brand_id);
                $amount=$db->result($r,$i-1,"amount");
                $storage_id_from=$db->result($r,$i-1,"storage_id_from");
                $storage_name_from=$this->getStorageName($storage_id_from);
                $amount_barcodes=$db->result($r,$i-1,"amount_barcodes");
                $amount_barcodes_noscan=$db->result($r,$i-1,"amount_barcodes_noscan"); $amount_accept=$amount_barcodes+$amount_barcodes_noscan;
                $select_bug_list=$this->getStorageSelectBugList($jmoving_id,$select_id,$art_id,$id);
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
        }
        return array($form,"Структура складського відбору № СкВ-$select_id; Статус відбору: ".$gmanual->get_gmanual_caption($select_status));
    }

    function viewJmovingStorageSelectLocal($jmoving_id,$select_id,$jmoving_status){$db=DbSingleton::getDb();$cat=new catalogue;
        $form="";$form_htm=RD."/tpl/jmoving_local_storage_select_view.htm"; $list="";
        $tmp="J_MOVING_SELECT_STR";if ($jmoving_status==44){$tmp="J_MOVING_LOCAL_SELECT_STR_TEMP";}
        $disabled46=" disabled";$disabled47=" disabled";$disabled48=" disabled";

        if ($jmoving_status==45){$disabled46=" ";}
        if ($jmoving_status==46){$disabled47=" ";}
        if ($jmoving_status==47 || $jmoving_status==48){
            $disabled48=" ";
            $form_htm=RD."/tpl/jmoving_local_storage_select_view_finish.htm";
        }

        if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}

        $r=$db->query("select * from $tmp  where jmoving_id='$jmoving_id' and select_id='$select_id' order by id asc;");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
                $article_nr_displ=$db->result($r,$i-1,"article_nr_displ");
                $brand_id=$db->result($r,$i-1,"brand_id");$brand_name=$cat->getBrandName($brand_id);
                $amount=$db->result($r,$i-1,"amount");
                $cell_id_from=$db->result($r,$i-1,"cell_id_from");$cell_name_from=$this->getStorageCellName($cell_id_from);
                $cell_id_to=$db->result($r,$i-1,"cell_id_to");$cell_name_to=$this->getStorageCellName($cell_id_to);
                $list47="";
                /*if ($jmoving_status==47 || $jmoving_status==48){
                    $amount_barcodes=$db->result($r,$i-1,"amount_barcodes");
                    $amount_barcodes_noscan=$db->result($r,$i-1,"amount_barcodes_noscan"); $amount_accept=$amount_barcodes+$amount_barcodes_noscan;
                    $select_bug_list=$this->getStorageSelectBugList($jmoving_id,$select_id,$art_id);
                    $amount_bug=$db->result($r,$i-1,"amount_bug");
                    $list47="<td>$amount_accept</td>
                    <td>$amount_bug</td>
                    <td>$select_bug_list</td>";
                }*/
                $list.="<tr align='right'>
                    <td align='left'>$i</td>
                    <td align='left'>$article_nr_displ</td>
                    <td align='center'>$brand_name</td>
                    <td>$cell_name_from</td>
                    <td>$cell_name_to</td>
                    <td>$amount</td>
                    $list47
                </tr>";
        }
        list($select_nom,$select_data,$storage_id,$storage_name,$storage_name_to,$articles_amount,$amount,$volume,$weight_netto,$weight_brutto,$jmoving_comment,$select_datatime)=$this->getJmovingSkladStorageSelectInfoLocal($jmoving_id,$select_id);
        $form=str_replace("{select_start}",$select_datatime,$form);
        $form=str_replace("{ArticlesList}",$list,$form);
        $form=str_replace("{select_id}",$select_id,$form);
        $form=str_replace("{jmoving_id}",$jmoving_id,$form);
        $form=str_replace("{disabled46}",$disabled46,$form);
        $form=str_replace("{disabled47}",$disabled47,$form);
        $form=str_replace("{disabled48}",$disabled48,$form);
        if ($jmoving_status==44){
            $form=str_replace("{preview-hidden}","hidden disabled",$form);
        }
        $form=str_replace("{preview-hidden}","",$form);
        $data_records=$this->getJmovingSelectJournalRecords($jmoving_id,$select_id);
        $form=str_replace("{data_46}",$data_records[46],$form);
        $form=str_replace("{data_52}",$data_records[52],$form);
        $form=str_replace("{data_47}",$data_records[47],$form);
        $form=str_replace("{data_48}",$data_records[48],$form);
        return array($form,"Структура складського відбору № СкВн-$select_id");
    }

    function dropJmovingStorageSelect($jmoving_id,$select_id){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка видалення запису!";
        $jmoving_id=$slave->qq($jmoving_id);$select_id=$slave->qq($select_id);
        if ($jmoving_id>0 && $select_id>0){
            $r=$db->query("select count(id) as kol from J_MOVING_SELECT_STR_TEMP where jmoving_id='$jmoving_id' and select_id='$select_id';");$kol=$db->result($r,0,"kol");
            if ($kol>0){
                $db->query("delete from J_MOVING_SELECT_STR_TEMP where jmoving_id='$jmoving_id' and select_id='$select_id';");
                $db->query("delete from J_MOVING_SELECT_TEMP where jmoving_id='$jmoving_id' and id='$select_id';");
                $db->query("delete from J_MOVING_STR where `jmoving_id`='$jmoving_id' and select_id='0';");
                $answer=1;$err="";
            }
        }
        return array($answer,$err);
    }

    function dropJmovingStorageSelectLocal($jmoving_id,$select_id){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка видалення запису!";
        $jmoving_id=$slave->qq($jmoving_id);$select_id=$slave->qq($select_id);
        if ($jmoving_id>0 && $select_id>0){
            $r=$db->query("select count(id) as kol from J_MOVING_LOCAL_SELECT_STR_TEMP where jmoving_id='$jmoving_id' and select_id='$select_id';");$kol=$db->result($r,0,"kol");
            if ($kol>0){
                $db->query("delete from J_MOVING_LOCAL_SELECT_STR_TEMP where jmoving_id='$jmoving_id' and select_id='$select_id';");
                $db->query("delete from J_MOVING_LOCAL_SELECT_TEMP where jmoving_id='$jmoving_id' and id='$select_id';");
                $db->query("delete from J_MOVING_STR where `jmoving_id`='$jmoving_id' and select_id='0';");
                $answer=1;$err="";
            }
        }
        return array($answer,$err);
    }

    function collectJmovingStorageSelect($jmoving_id,$select_id){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка обробки запису!";
        $jmoving_id=$slave->qq($jmoving_id);$select_id=$slave->qq($select_id);
        if ($jmoving_id>0 && $select_id>0){
            $r=$db->query("select status_jmoving from J_MOVING_SELECT where jmoving_id='$jmoving_id' and id='$select_id' limit 0,1;");$n=$db->num_rows($r);
            if ($n==1){
                $status_jmoving=$db->result($r,0,"status_jmoving");
                if ($status_jmoving==45){
                    $db->query("update J_MOVING_SELECT set status_jmoving='46' where jmoving_id='$jmoving_id' and id='$select_id' limit 1;");
                    $this->addJuornalRecord($jmoving_id,$select_id,46);
                    $answer=1;$err="";
                }
            }
        }
        return array($answer,$err);
    }

    function collectJmovingStorageSelectLocal($jmoving_id,$select_id){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка обробки запису!";
        $jmoving_id=$slave->qq($jmoving_id);$select_id=$slave->qq($select_id);
        if ($jmoving_id>0 && $select_id>0){
            $r=$db->query("select status_jmoving from J_MOVING_SELECT where jmoving_id='$jmoving_id' and id='$select_id' limit 0,1;");$n=$db->num_rows($r);
            if ($n==1){
                $status_jmoving=$db->result($r,0,"status_jmoving");
                if ($status_jmoving==45){
                    $db->query("update J_MOVING_SELECT set status_jmoving='46' where jmoving_id='$jmoving_id' and id='$select_id' limit 1;");
                    $this->addJuornalRecord($jmoving_id,$select_id,46);
                    $answer=1;$err="";
                }
            }
        }
        return array($answer,$err);
    }

    function cutJmovingStorageAll($jmoving_id,$select,$comment){ $db=DbSingleton::getDb(); $slave=new slave; $ids=[]; $comment_sent="Передано в ID: ";
        session_start();$user_id=$_SESSION["media_user_id"];$answer=0;$err="Помилка обробки даних!";
        $jmoving_id=$slave->qq($jmoving_id);
        foreach ($select as $select_id){
            $r=$db->query("select count(id) as kol from J_MOVING_SELECT_STR_TEMP where jmoving_id='$jmoving_id' and select_id='$select_id';");$kol=$db->result($r,0,"kol");
            if ($kol>0){
                $rm=$db->query("select max(id) as mid from J_MOVING;");$new_jmoving_id=0+$db->result($rm,0,"mid")+1;
                $rm=$db->query("select * from J_MOVING where id='$jmoving_id' limit 0,1;");$nm=$db->num_rows($rm);
                if($nm==1){
                    $prefix=$db->result($rm,0,"prefix");
                    $type_id=$db->result($rm,0,"type_id");
                    $doc_nom=$this->get_df_doc_nom_new();
                    $data=date("Y-m-d");
                    $storage_id_to=$db->result($rm,0,"storage_id_to");
                    $cell_use=$db->result($rm,0,"cell_use");
                    $cell_id_to=$db->result($rm,0,"cell_id_to");

                    $db->query("insert into J_MOVING (`id`,`prefix`,`doc_nom`,`type_id`,`data`,`storage_id_to`,`cell_use`,`cell_id_to`,`user_id`,`comment`) values ('$new_jmoving_id','$prefix','$doc_nom','$type_id','$data','$storage_id_to','$cell_use','$cell_id_to','$user_id','$comment');");

                    $rs=$db->query("select * from J_MOVING_SELECT_STR_TEMP where jmoving_id='$jmoving_id' and select_id='$select_id' order by id asc;");$ns=$db->num_rows($rs);
                    for ($is=1;$is<=$ns;$is++){
                        $art_id=$db->result($rs,$is-1,"art_id");
                        $article_nr_displ=$db->result($rs,$is-1,"article_nr_displ");
                        $brand_id=$db->result($rs,$is-1,"brand_id");
                        $amount=$db->result($rs,$is-1,"amount");
                        $storage_id_from=$db->result($rs,$is-1,"storage_id_from");
                        $cell_id_from=$db->result($rs,$is-1,"cell_id_from");
                        $db->query("insert into J_MOVING_STR (`jmoving_id`,`art_id`,`article_nr_displ`,`brand_id`,`amount`,`storage_id_from`,`cell_id_from`,`user_id`) values ('$new_jmoving_id','$art_id','$article_nr_displ','$brand_id','$amount','$storage_id_from','$cell_id_from','$user_id');");
                    }
                    //$db->query("delete from J_MOVING_STR where jmoving_id='$jmoving_id' and select_id='0';");
                    //$db->query("delete from J_MOVING_SELECT_STR_TEMP where jmoving_id='$jmoving_id' and select_id='$select_id';");
                    //$db->query("delete from J_MOVING_SELECT_TEMP where jmoving_id='$jmoving_id' and id='$select_id';");
                    $comment_sent.=$new_jmoving_id." ";
                    $this->updateJmovingWeightVolume($jmoving_id);
                    $this->updateJmovingWeightVolume($new_jmoving_id);
                    $answer=1;$err="";array_push($ids,$new_jmoving_id);
                }
            }
        }
        $db->query("update J_MOVING set status=0, status_jmoving=106, comment='$comment_sent' where id='$jmoving_id';");
        $db->query("update J_MOVING_STR set status_jmoving=106 where jmoving_id='$jmoving_id' and status_jmoving=44;");
        return array($answer,$err,$ids);
    }

    function cutJmovingStorage($jmoving_id,$select_id){$db=DbSingleton::getDb();$slave=new slave;session_start();$user_id=$_SESSION["media_user_id"];$answer=0;$err="Помилка обробки даних!";
        $jmoving_id=$slave->qq($jmoving_id);$select_id=$slave->qq($select_id);
        if ($jmoving_id>0 && $select_id>0){
            $r=$db->query("select count(id) as kol from J_MOVING_SELECT_STR_TEMP where jmoving_id='$jmoving_id' and select_id='$select_id';");$kol=$db->result($r,0,"kol");
            if ($kol>0){
                $rm=$db->query("select max(id) as mid from J_MOVING;");$new_jmoving_id=0+$db->result($rm,0,"mid")+1;
                $rm=$db->query("select * from J_MOVING where id='$jmoving_id' limit 0,1;");$nm=$db->num_rows($rm);
                if($nm==1){
                    $prefix=$db->result($rm,0,"prefix");
                    $type_id=$db->result($rm,0,"type_id");
                    $doc_nom=$this->get_df_doc_nom_new();
                    $data=date("Y-m-d");
                    $storage_id_to=$db->result($rm,0,"storage_id_to");
                    $cell_use=$db->result($rm,0,"cell_use");
                    $cell_id_to=$db->result($rm,0,"cell_id_to");

                    $db->query("insert into J_MOVING (`id`,`prefix`,`doc_nom`,`type_id`,`data`,`storage_id_to`,`cell_use`,`cell_id_to`,`user_id`) values ('$new_jmoving_id','$prefix','$doc_nom','$type_id','$data','$storage_id_to','$cell_use','$cell_id_to','$user_id');");
                    $rs=$db->query("select * from J_MOVING_SELECT_STR_TEMP where jmoving_id='$jmoving_id' and select_id='$select_id' order by id asc;");$ns=$db->num_rows($rs);
                    for ($is=1;$is<=$ns;$is++){
                        $art_id=$db->result($rs,$is-1,"art_id");
                        $article_nr_displ=$db->result($rs,$is-1,"article_nr_displ");
                        $brand_id=$db->result($rs,$is-1,"brand_id");
                        $amount=$db->result($rs,$is-1,"amount");
                        $storage_id_from=$db->result($rs,$is-1,"storage_id_from");
                        $cell_id_from=$db->result($rs,$is-1,"cell_id_from");
                        $db->query("insert into J_MOVING_STR (`jmoving_id`,`art_id`,`article_nr_displ`,`brand_id`,`amount`,`storage_id_from`,`cell_id_from`,`user_id`) values ('$new_jmoving_id','$art_id','$article_nr_displ','$brand_id','$amount','$storage_id_from','$cell_id_from','$user_id');");
                    }
                    $db->query("delete from J_MOVING_STR where jmoving_id='$jmoving_id' and select_id='0';");
                    $db->query("delete from J_MOVING_SELECT_STR_TEMP where jmoving_id='$jmoving_id' and select_id='$select_id';");
                    $db->query("delete from J_MOVING_SELECT_TEMP where jmoving_id='$jmoving_id' and id='$select_id';");
                    $this->updateJmovingWeightVolume($jmoving_id);
                    $this->updateJmovingWeightVolume($new_jmoving_id);
    //				$this->startJmovingStorageSelect($new_jmoving_id);
                    $answer=1;$err="";
                }
            }
        }
        return array($answer,$err);
    }

    function getJmovingInfo($jmoving_id){$db=DbSingleton::getDb();
        $r=$db->query("select * from J_MOVING where id='$jmoving_id' limit 0,1;");
        $prefix=$db->result($r,0,"prefix");
        $doc_nom=$db->result($r,0,"doc_nom");
        $data=$db->result($r,0,"data");
        $storage_id_to=$db->result($r,0,"storage_id_to");$storage_name_to=$this->getStorageName($storage_id_to);
        $comment=$db->result($r,0,"comment");
        $parrent_type_id=$db->result($r,0,"parrent_type_id");
        $parrent_doc_id=$db->result($r,0,"parrent_doc_id");
        return array($prefix,$doc_nom,$data,$storage_id_to,$storage_name_to,$comment,$parrent_type_id,$parrent_doc_id);
    }

    function getJmovingSkladStorageSelectInfo($jmoving_id,$select_id){$db=DbSingleton::getDb();
        $r=$db->query("select ms.*, p.name as tpoint_name, s.name as storage_name, ml.mcaption as loc_type_name, mt.mcaption as status_jmoving_name, j.storage_id_to, j.comment 
        from J_MOVING_SELECT ms 
            left outer join J_MOVING j on j.id=ms.jmoving_id
            left outer join T_POINT p on p.id=ms.tpoint_id
            left outer join STORAGE s on s.id=ms.storage_id
            left outer join manual ml on ml.id=ms.loc_type_id
            left outer join manual mt on mt.id=ms.status_jmoving
        where ms.jmoving_id='$jmoving_id' and ms.id='$select_id' and ms.status='1' order by ms.id asc limit 0,1;");
        $data=$db->result($r,0,"data");
        $datatime=$db->result($r,0,"datatime");
        $storage_id=$db->result($r,0,"storage_id");
        $storage_name=$db->result($r,0,"storage_name");
        $storage_id_to=$db->result($r,0,"storage_id_to");$storage_name_to=$this->getStorageName($storage_id_to);
        $articles_amount=$db->result($r,0,"articles_amount");
        $amount=$db->result($r,0,"amount");
        $volume=$db->result($r,0,"volume");
        $weight_netto=$db->result($r,0,"weight_netto");
        $weight_brutto=$db->result($r,0,"weight_brutto");
        $comment=$db->result($r,0,"comment");
        return array("СКВ-$select_id/$storage_name",$data,$storage_id,$storage_name,$storage_name_to,$articles_amount,$amount,$volume,$weight_netto,$weight_brutto,$comment,$datatime);
    }

    function getJmovingSkladStorageSelectInfoLocal($jmoving_id,$select_id){$db=DbSingleton::getDb();
        $r=$db->query("select ms.*, p.name as tpoint_name, s.name as storage_name, mt.mcaption as status_jmoving_name, j.storage_id_to, j.comment 
        from J_MOVING_SELECT ms 
            left outer join J_MOVING j on j.id=ms.jmoving_id
            left outer join T_POINT p on p.id=ms.tpoint_id
            left outer join STORAGE s on s.id=ms.storage_id
            left outer join manual mt on mt.id=ms.status_jmoving
        where ms.jmoving_id='$jmoving_id' and ms.id='$select_id' and ms.status='1' order by ms.id asc limit 0,1;");
        $data=$db->result($r,0,"data");
        $datatime=$db->result($r,0,"datatime");
        $storage_id=$db->result($r,0,"storage_id");
        $storage_name=$db->result($r,0,"storage_name");
        $storage_id_to=$db->result($r,0,"storage_id_to");$storage_name_to=$this->getStorageName($storage_id_to);
        $articles_amount=$db->result($r,0,"articles_amount");
        $amount=$db->result($r,0,"amount");
        $volume=$db->result($r,0,"volume");
        $weight_netto=$db->result($r,0,"weight_netto");
        $weight_brutto=$db->result($r,0,"weight_brutto");
        $comment=$db->result($r,0,"comment");
        return array("СкВн-$select_id/$storage_name",$data,$storage_id,$storage_name,$storage_name_to,$articles_amount,$amount,$volume,$weight_netto,$weight_brutto,$comment,$datatime);
    }

    function printJmovingStorageSelect($jmoving_id,$select_id){$db=DbSingleton::getDb();$cat=new catalogue;$slave=new slave;session_start();$user_name=$_SESSION["user_name"];
        $form="";$form_htm=RD."/tpl/jmoving_storage_select_print.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);} $list="";
        $r=$db->query("select * from J_MOVING_SELECT_STR  where jmoving_id='$jmoving_id' and select_id='$select_id' order by id asc;");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $art_id=$db->result($r,$i-1,"art_id");
            $article_nr_displ=$db->result($r,$i-1,"article_nr_displ"); $article_name=$this->getArticleName($art_id);
            $brand_id=$db->result($r,$i-1,"brand_id");$brand_name=$cat->getBrandName($brand_id);
            $amount=$db->result($r,$i-1,"amount");
            $cell_id_from=$db->result($r,$i-1,"cell_id_from");
            $cell_name_from=$this->getStorageCellName($cell_id_from);
            $list.="<tr>
                <td align='center'>$i</td>
                <td align='center'>$cell_name_from</td>
                <td align='center'>$article_nr_displ</td>
                <td align='center'>$brand_name</td>
                <td align='center'>$amount</td>
                <td align='left'>$article_name</td>
                <td>&nbsp;</td>
            </tr>";
        }
        $form=str_replace("{ArticlesList}",$list,$form);
        $form=str_replace("{select_id}",$select_id,$form);
        $form=str_replace("{jmoving_id}",$jmoving_id,$form);
        list($select_nom,$select_data,$storage_id,$storage_name,$storage_name_to,$articles_amount,$amount,$volume,$weight_netto,$weight_brutto,$jmoving_comment,$select_datatime)=$this->getJmovingSkladStorageSelectInfo($jmoving_id,$select_id);
        $form=str_replace("{select_nom}",$select_nom,$form);
        $form=str_replace("{select_data}",$select_data,$form);
        $form=str_replace("{storage_from}",$storage_name,$form);
        $form=str_replace("{storage_to}",$storage_name_to,$form);
        $form=str_replace("{articles_amount}",$articles_amount,$form);
        $form=str_replace("{amount}",$amount,$form);
        $form=str_replace("{volume}",$volume,$form);
        $form=str_replace("{weight_netto}",$weight_netto,$form);
        $form=str_replace("{weight_brutto}",$weight_brutto,$form);
        $form=str_replace("{jmoving_comment}",$jmoving_comment,$form);
        $form=str_replace("{user_name}",$user_name,$form);
        $form=str_replace("{curtime}",date("Y-m-d H:i:s"),$form);$pData="";
        $form=str_replace("{pData}",$slave->data_word($pData),$form);
        $form=str_replace("{qrImage}","<img src='/phpqrcode/qrimage2.php?url=http://portal.myparts.pro/Jmoving/printJmS1/$jmoving_id/$select_id/".time()."'>",$form);
        $this->addJuornalRecord($jmoving_id,$select_id,52);

        //"Структура складського відбору"
        $mp=new media_print;
        $mp->print_document($form,array(210,280));
        return $form;
    }

    function printJmovingStorageSelectLocal($jmoving_id,$select_id){$db=DbSingleton::getDb();$cat=new catalogue;$slave=new slave;session_start();$user_name=$_SESSION["user_name"];
        $form="";$form_htm=RD."/tpl/jmoving_local_storage_select_print.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);} $list="";
        $r=$db->query("select * from J_MOVING_SELECT_STR  where jmoving_id='$jmoving_id' and select_id='$select_id' order by id asc;");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $art_id=$db->result($r,$i-1,"art_id");
            $article_nr_displ=$db->result($r,$i-1,"article_nr_displ"); $article_name=$this->getArticleName($art_id);
            $brand_id=$db->result($r,$i-1,"brand_id");$brand_name=$cat->getBrandName($brand_id);
            $amount=$db->result($r,$i-1,"amount");
            $cell_id_from=$db->result($r,$i-1,"cell_id_from");$cell_name_from=$this->getStorageCellName($cell_id_from);
            $cell_id_to=$db->result($r,$i-1,"cell_id_to");$cell_name_to=$this->getStorageCellName($cell_id_to);
            $list.="<tr>
                <td align='center'>$i</td>
                <td align='center'>$cell_name_from</td>
                <td align='center'>$article_nr_displ</td>
                <td align='center'>$brand_name</td>
                <td align='center'>$amount</td>
                <td align='center'>$cell_name_to</td>
                <td align='left'>$article_name</td>
                <td>&nbsp;</td>
            </tr>";
        }
        $form=str_replace("{ArticlesList}",$list,$form);
        $form=str_replace("{select_id}",$select_id,$form);
        $form=str_replace("{jmoving_id}",$jmoving_id,$form);
        list($select_nom,$select_data,$storage_id,$storage_name,$storage_name_to,$articles_amount,$amount,$volume,$weight_netto,$weight_brutto,$jmoving_comment,$select_datatime)=$this->getJmovingSkladStorageSelectInfo($jmoving_id,$select_id);
        $form=str_replace("{select_nom}",$select_nom,$form);
        $form=str_replace("{select_data}",$select_data,$form);
        $form=str_replace("{storage_from}",$storage_name,$form);
        $form=str_replace("{storage_to}",$storage_name_to,$form);
        $form=str_replace("{articles_amount}",$articles_amount,$form);
        $form=str_replace("{amount}",$amount,$form);
        $form=str_replace("{volume}",$volume,$form);
        $form=str_replace("{weight_netto}",$weight_netto,$form);
        $form=str_replace("{weight_brutto}",$weight_brutto,$form);
        $form=str_replace("{jmoving_comment}",$jmoving_comment,$form);
        $form=str_replace("{user_name}",$user_name,$form);
        $form=str_replace("{curtime}",date("Y-m-d H:i:s"),$form);$pData="";
        $form=str_replace("{pData}",$slave->data_word($pData),$form);
        $form=str_replace("{qrImage}","<img src='/phpqrcode/qrimage2.php?url=http://portal.myparts.pro/Jmoving/printJmS1/$jmoving_id/$select_id/".time()."'>",$form);
        $this->addJuornalRecord($jmoving_id,$select_id,52);

        //"Структура складського відбору"
        $mp=new media_print;
        $mp->print_document($form,array(210,280));
        return $form;
    }

    function addJuornalRecord($jmoving_id,$select_id,$status_jmoving){$db=DbSingleton::getDb();session_start();$user_id=$_SESSION["media_user_id"];
        $db->query("insert into J_MOVING_SELECT_JOURNAL (`jmoving_id`,`select_id`,`user_id`,`status_jmoving`) values ('$jmoving_id','$select_id','$user_id','$jmoving_id');"); return;
    }

    function getJmovingSelectJournalRecords($jmoving_id,$select_id){$db=DbSingleton::getDb();$data=array();
        $r=$db->query("select * from J_MOVING_SELECT_JOURNAL where `jmoving_id`='$jmoving_id' and `select_id`='$select_id' order by id asc;"); $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $status_jmoving=$db->result($r,$i-1,"status_jmoving");
            $datatime=$db->result($r,$i-1,"datatime");
            $data[$status_jmoving]=$datatime;
        }
        return $data;
    }

    function showJmovingStorageSelectBarcodeForm($jmoving_id,$select_id){$db=DbSingleton::getDb();$cat=new catalogue;
        $form="";$form_htm=RD."/tpl/jmoving_storage_select_barcode_list.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);} $list="";
        $r=$db->query("select * from J_MOVING_SELECT_STR  where jmoving_id='$jmoving_id' and select_id='$select_id' order by id asc;");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $art_id=$db->result($r,$i-1,"art_id");
            $article_nr_displ=$db->result($r,$i-1,"article_nr_displ"); $article_name=$this->getArticleName($art_id);
            $brand_id=$db->result($r,$i-1,"brand_id");$brand_name=$cat->getBrandName($brand_id);
            $amount=$db->result($r,$i-1,"amount");
            $amount_barcodes=$db->result($r,$i-1,"amount_barcodes");
            $amount_barcodes_noscan=$db->result($r,$i-1,"amount_barcodes_noscan");
            $amount_bug=$db->result($r,$i-1,"amount_bug");
            $dif_amount_barcodes=$amount-$amount_barcodes-$amount_barcodes_noscan-$amount_bug;
            if ($dif_amount_barcodes<0){$dif_amount_barcodes=0;}
            $storage_select_list=$this->getStorageSelectBugList($jmoving_id,$select_id,$art_id,$id);

            $list.="<tr>
                <td align='center'>$i</td>
                <td align='center' id='amrai_$id'>$article_nr_displ</td>
                <td align='center' id='amrab_$id'>$brand_name</td>
                <td align='left' id='amran_$id'>$article_name</td>
                <td align='center'>$amount</td>
                <td align='center' id='amr_$id'>$amount_barcodes</td>
                <td align='center' id='amrd_$id'>$dif_amount_barcodes</td>
                <td align='center' id='amrns_$id'>$amount_barcodes_noscan</td>
                <td align='center'><button class='btn btn-xs btn-default' onclick='showJmovingStorageSelectNoscanForm(\"$jmoving_id\",\"$select_id\",\"$art_id\",\"$id\");' title='Фіксація без сканування' alt='Фіксація без сканування'><i class='fa fa-cube'></i></button></td>
                <td align='center'><button class='btn btn-xs btn-danger' onclick='showJmovingStorageSelectBugForm(\"$jmoving_id\",\"$select_id\",\"$id\");' title='відхилення/брак/недостача' alt='відхилення/брак/недостача'><i class='fa fa-bug'></i></button></td>
                <td align='center' id='ambg_$id'>$amount_bug</td>
                <td id='ssbug_$id'>$storage_select_list</td>
            </tr>";
        }
        $form=str_replace("{ArticlesList}",$list,$form);
        $form=str_replace("{select_id}",$select_id,$form);
        $form=str_replace("{jmoving_id}",$jmoving_id,$form);
        list($select_nom,$select_data,$storage_id,$storage_name,$storage_name_to,$articles_amount,$amount,$volume,$weight_netto,$weight_brutto,$jmoving_comment,$select_datatime)=$this->getJmovingSkladStorageSelectInfo($jmoving_id,$select_id);
        $form=str_replace("{select_nom}",$select_nom,$form);
        $form=str_replace("{select_data}",$select_data,$form);
        $form=str_replace("{jmoving_comment}",$jmoving_comment,$form);$user_name="";
        $form=str_replace("{user_name}",$user_name,$form);
        $form=str_replace("{curtime}",date("Y-m-d H:i:s"),$form);
        //$this->addJuornalRecord($jmoving_id,$select_id,47);
        $answer=1;$err="";
        return array($answer,$err,$form,"Пакування товару по штрих-кодам");
    }

    function saveJmovingStorageSelectBarcodeForm($jmoving_id,$select_id,$barcode){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка індексу!!";
        $jmoving_id=$slave->qq($jmoving_id);$select_id=$slave->qq($select_id);$barcode=$slave->qq($barcode);
        $id=$amount_barcodes=$dif_amount_barcodes=0;
        if ($jmoving_id>0 && $select_id>0 && $barcode!=""){
            $art_id=$this->getArtIdByBarcode($barcode);
            $r=$db->query("select * from J_MOVING_SELECT_STR where jmoving_id='$jmoving_id' and select_id='$select_id' and art_id='$art_id' and amount>amount_barcodes limit 0,1;");$n=$db->num_rows($r);
            if ($n==1){
                $id=$db->result($r,0,"id");
                $amount=$db->result($r,0,"amount");
                $amount_barcodes=$db->result($r,0,"amount_barcodes");
                $amount_barcodes_noscan=$db->result($r,0,"amount_barcodes_noscan");
                $amount_bug=$db->result($r,0,"amount_bug");
                if ($amount>($amount_barcodes+$amount_barcodes_noscan+$amount_bug)){
                    $amount_barcodes+=1;
                    $dif_amount_barcodes=$amount-$amount_barcodes-$amount_barcodes_noscan-$amount_bug;
                    $db->query("update J_MOVING_SELECT_STR set amount_barcodes='$amount_barcodes' where id='$id' limit 1;");
                    $answer=1;$err="";
                }
            }
        } else {$answer=0;$err="Помилка штрих-коду";}
        return array($answer,$err,$id,$amount_barcodes,$dif_amount_barcodes);
    }

    function finishJmovingStorageSelectBarcodeForm($jmoving_id,$select_id){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка індексу!!";
        $jmoving_id=$slave->qq($jmoving_id);$select_id=$slave->qq($select_id);$id=0;
        if ($jmoving_id>0 && $select_id>0){
            $r=$db->query("select * from J_MOVING_SELECT_STR where jmoving_id='$jmoving_id' and select_id='$select_id' and amount>(amount_barcodes+amount_barcodes_noscan+amount_bug);");$n=$db->num_rows($r);
            if ($n>0){
                $answer=0;$err="Не завершено перевірку по штрих-кодам";
            }
            if ($n==0){
                $r=$db->query("select (SUM(amount_barcodes)+SUM(amount_barcodes_noscan)) as new_amount from J_MOVING_SELECT_STR where jmoving_id='$jmoving_id' and select_id='$select_id';");$n=$db->num_rows($r);
                if ($n==1){
                    $new_amount=$db->result($r,0,"new_amount");
                    $db->query("update J_MOVING_SELECT set status_jmoving='47', amount='$new_amount' where jmoving_id='$jmoving_id' and id='$select_id' limit 1;");

                    $r1=$db->query("select art_id, sum(amount_barcodes+amount_barcodes_noscan) as amount_js from J_MOVING_SELECT_STR where jmoving_id='$jmoving_id' and select_id='$select_id';");$n1=$db->num_rows($r1);
                    for ($i=1;$i<=$n1;$i++){
                        $art_id=$db->result($r1,$i-1,"art_id");
                        $amount_js=$db->result($r1,$i-1,"amount_js");
                        $db->query("update J_MOVING_STR set amount='$amount_js' where jmoving_id='$jmoving_id' and select_id='$select_id' and art_id='$art_id' limit 1;");
                    }
                }
                $this->addJuornalRecord($jmoving_id,$select_id,47);
                $answer=1;$err="";
            }
        } else {$answer=0;$err="Помилка штрих-коду";}
        return array($answer,$err,$id,47);
    }

    function finishJmovingLocalStorageSelect($jmoving_id,$select_id){$db=DbSingleton::getDb();$slave=new slave;
        $jmoving_id=$slave->qq($jmoving_id);$select_id=$slave->qq($select_id);$id=0;$new_amount=0;
        if ($jmoving_id>0 && $select_id>0){
            $db->query("update J_MOVING_SELECT set status_jmoving='47', amount='$new_amount' where jmoving_id='$jmoving_id' and id='$select_id' limit 1;");
            $this->addJuornalRecord($jmoving_id,$select_id,47);
            $answer=1;$err="";
        } else {$answer=0;$err="Помилка документу";}
        return array($answer,$err,$id,47);
    }

    function checkJmovingAmountToTruck($jmoving_id){$db=DbSingleton::getDb();
        $r=$db->query("select SUM(amount) as sum_amount from J_MOVING_STR where jmoving_id='$jmoving_id' and status_jmoving='45';");$amount=$db->result($r,0,"sum_amount");
        return $amount;
    }

    /*
    function setJmovingStorageSelectSendTruck($jmoving_id){$db=DbSingleton::getDb();$slave=new slave;$cat=new catalogue;$answer=0;$err="Помилка індексу!!";
        $jmoving_id=$slave->qq($jmoving_id);
        if ($jmoving_id>0){
            $jmoving_amount=$this->checkJmovingAmountToTruck($jmoving_id);
            if ($jmoving_amount>0){
                $prefix=$this->get_jmoving_prefix($jmoving_id);$doc_nom=0;
                $r=$db->query("select max(doc_nom) as doc_nom from J_MOVING where oper_status='31' and status='1' and `type_id`='1' limit 0,1;");$doc_nom=0+$db->result($r,0,"doc_nom")+1;
                $db->query("update J_MOVING_SELECT set status_jmoving='48'  where jmoving_id='$jmoving_id' and status='1';");
                $db->query("update J_MOVING set status_jmoving='48', prefix='$prefix', `doc_nom`='$doc_nom', `oper_status`='31' where id='$jmoving_id' and status='1';");
                $this->addJuornalRecord($jmoving_id,0,48);
                $answer=1;$err="";
            }
            if ($jmoving_amount<=0){$answer=0;$err="У переміщенні немає товару.\n Переміщення без товару не відправляється";}
        }
        return array($answer,$err,$id,$amount_barcodes,$dif_amount_barcodes);
    }
    */

    function setJmovingSendTruck($jmoving_id){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка індексу!!";
        $jmoving_id=$slave->qq($jmoving_id);
        if ($jmoving_id>0){
            $jmoving_amount=$this->checkJmovingAmountToTruck($jmoving_id);
            if ($jmoving_amount>0){
                $prefix=$this->get_jmoving_prefix($jmoving_id);
                $r=$db->query("select max(doc_nom) as doc_nom from J_MOVING where oper_status='31' and status='1' and `type_id`='1' limit 0,1;");$doc_nom=0+$db->result($r,0,"doc_nom")+1;
                $db->query("update J_MOVING set status_jmoving='48', prefix='$prefix', `doc_nom`='$doc_nom', `oper_status`='31' where id='$jmoving_id' and status='1';");
                $this->addJuornalRecord($jmoving_id,0,48);
                $r3=$db->query("select select_id from J_MOVING_STR where jmoving_id='$jmoving_id';"); $n3=$db->num_rows($r3);
                $select_str="0";
                for ($i3=1;$i3<=$n3;$i3++){
                    $select_str.=",".$db->result($r3,$i3-1,"select_id");
                }
                $db->query("update J_SELECT set status_select='127' where id in ($select_str);");
                $answer=1;$err="";
            }
            if ($jmoving_amount<=0){$answer=0;$err="У переміщенні немає товару.\n Переміщення без товару не відправляється";}
        }
        $id=$amount_barcodes=$dif_amount_barcodes=0;
        return array($answer,$err,$id,$amount_barcodes,$dif_amount_barcodes);
    }

    function printJmovingTruckList($jmoving_id){$db=DbSingleton::getDb();$cat=new catalogue;$slave=new slave;session_start();$user_name=$_SESSION["user_name"]; $storsels=[];$list="";
        $storage_id_from=$select_id=0;$storage_name_from=$tpoint_name=$tpoint_address="";
        $form="";$form_htm=RD."/tpl/jmoving_truck_print.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select * from J_MOVING_STR where jmoving_id='$jmoving_id' order by id asc;");$n=$db->num_rows($r);$storsel=new storsel;$storsel_prefix=$storsel->storsel_prefix; $dp=new dp;
        for ($i=1;$i<=$n;$i++){
            $art_id=$db->result($r,$i-1,"art_id");
            $select_id=$db->result($r,$i-1,"select_id");
            array_push($storsels,"$storsel_prefix-$select_id");
            $article_nr_displ=$db->result($r,$i-1,"article_nr_displ"); $article_name=$this->getArticleName($art_id);
            $brand_id=$db->result($r,$i-1,"brand_id");$brand_name=$cat->getBrandName($brand_id);
            $amount=$db->result($r,$i-1,"amount");
            $storage_id_from=$db->result($r,$i-1,"storage_id_from");
            $storage_name_from=$this->getStorageName($storage_id_from);
            if ($amount>0) {
                $list.="<tr>
                    <td align='center'>$i</td>
                    <td align='center'>$storage_name_from</td>
                    <td align='center'>$article_nr_displ</td>
                    <td align='center'>$brand_name</td>
                    <td align='center'>$amount</td>
                    <td align='left'>$article_name</td>
                    <td>&nbsp;</td>
                </tr>";
            }
        }
        $form=str_replace("{ArticlesList}",$list,$form);
        $form=str_replace("{jmoving_id}",$jmoving_id,$form);

        list($prefix,$doc_nom,$data,$storage_id_to,$storage_name_to,$comment,$parrent_type_id,$parrent_doc_id)=$this->getJmovingInfo($jmoving_id);
        $dp_info="Без замовлення";

        if ($parrent_type_id==1){
            list($dp_prefix,$dp_doc_nom,$dp_data,$s,$s,$s)=$dp->getdpInfo($parrent_doc_id);
            $dp_info="$dp_prefix-$dp_doc_nom/$dp_data";
            $tpoint_id=$dp->getDpTpoint($parrent_doc_id);
            $tpoint_name=$dp->getTpointName($tpoint_id);
            $tpoint_address=$dp->getTpointAddress($tpoint_id);
            //foreach ($select_ar as $sel_id){ $storsel_list.="$storsel_prefix-$sel_id "; }
        }
        $tp_to=$this->getTpointName($storage_id_to);
        $tp_from=$this->getTpointName($storage_id_from);
        $form=str_replace("{storage_to}",$storage_name_to,$form);
        $form=str_replace("{storage_from}",$storage_name_from,$form);
        $form=str_replace("{tp_to}",$tp_to,$form);
        $form=str_replace("{tp_from}",$tp_from,$form);
        $form=str_replace("{jmoving_prefix_doc_nom}",$prefix."-".$doc_nom,$form);
        $form=str_replace("{jmoving_data}",$slave->data_word($data),$form);
        $storsels=array_unique($storsels);
        $storsel_list=implode(",",$storsels);
        $form=str_replace("{tpoint_name}",$tpoint_name,$form);
        $form=str_replace("{tpoint_address}",$tpoint_address,$form);$jmoving_comment="";
        $form=str_replace("{jmoving_comment}",$jmoving_comment,$form);
        $form=str_replace("{user_name}",$user_name,$form);
        $form=str_replace("{curtime}",date("Y-m-d H:i:s"),$form);
        $form=str_replace("{storsel_list}",$storsel_list,$form);
        $form=str_replace("{dp_info}",$dp_info,$form);$pData="";
        $form=str_replace("{pData}",$slave->data_word($pData),$form);
        $form=str_replace("{qrImage}","<img src='/phpqrcode/qrimage2.php?url=http://portal.myparts.pro/Jmoving/printJmSTP/$jmoving_id/$select_id/".time()."'>",$form);

        //"Структура складського відбору"
        $mp=new media_print;
        $mp->print_document($form,array(210,280));
        return $form;
    }

    function getTpointName($storage_id) {$db=DbSingleton::getDb();
        $r=$db->query("select tpoint_id from T_POINT_STORAGE where storage_id='$storage_id';");
        $tpoint_id=$db->result($r,0,"tpoint_id");
        $r=$db->query("select name from T_POINT where id='$tpoint_id';");
        $name=$db->result($r,0,"name");
        return $name;
    }

    function printJmovingStorageSelectTruckList($jmoving_id,$select_id){$db=DbSingleton::getDb();$cat=new catalogue;$slave=new slave;session_start();$user_name=$_SESSION["user_name"];$list="";
        $form="";$form_htm=RD."/tpl/jmoving_storage_select_truck_print.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select * from J_MOVING_SELECT_STR  where jmoving_id='$jmoving_id' and select_id='$select_id' order by id asc;");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $art_id=$db->result($r,$i-1,"art_id");
            $article_nr_displ=$db->result($r,$i-1,"article_nr_displ"); $article_name=$this->getArticleName($art_id);
            $brand_id=$db->result($r,$i-1,"brand_id");$brand_name=$cat->getBrandName($brand_id);
            $amount=$db->result($r,$i-1,"amount");
            $cell_id_from=$db->result($r,$i-1,"cell_id_from");
            $cell_name_from=$this->getStorageCellName($cell_id_from);
            $list.="<tr>
                <td align='center'>$i</td>
                <td align='center'>$cell_name_from</td>
                <td align='center'>$article_nr_displ</td>
                <td align='center'>$brand_name</td>
                <td align='center'>$amount</td>
                <td align='left'>$article_name</td>
                <td>&nbsp;</td>
            </tr>";
        }
        $form=str_replace("{ArticlesList}",$list,$form);
        $form=str_replace("{select_id}",$select_id,$form);
        $form=str_replace("{jmoving_id}",$jmoving_id,$form);
        list($select_nom,$select_data,$storage_id,$storage_name,$storage_name_to,$articles_amount,$amount,$volume,$weight_netto,$weight_brutto,$jmoving_comment,$select_datatime)=$this->getJmovingSkladStorageSelectInfo($jmoving_id,$select_id);
        $form=str_replace("{select_nom}",$select_nom,$form);
        $form=str_replace("{select_data}",$select_data,$form);
        $form=str_replace("{storage_to}",$storage_name_to,$form);
        $form=str_replace("{articles_amount}",$articles_amount,$form);
        $form=str_replace("{amount}",$amount,$form);
        $form=str_replace("{volume}",$volume,$form);
        $form=str_replace("{weight_netto}",$weight_netto,$form);
        $form=str_replace("{weight_brutto}",$weight_brutto,$form);
        $form=str_replace("{jmoving_comment}",$jmoving_comment,$form);
        $form=str_replace("{user_name}",$user_name,$form);
        $form=str_replace("{curtime}",date("Y-m-d H:i:s"),$form);$pData="";
        $form=str_replace("{pData}",$slave->data_word($pData),$form);
        $form=str_replace("{qrImage}","<img src='/phpqrcode/qrimage2.php?url=http://portal.myparts.pro/Jmoving/printJmSTP/$jmoving_id/$select_id/".time()."'>",$form);

        //"Структура складського відбору"
        $mp=new media_print;
        $mp->print_document($form,array(210,280));
        return $form;
    }

    function showJmovingStorageSelectBugForm($jmoving_id,$select_id,$str_id){$db=DbSingleton::getDb();$cat=new catalogue;$manual=new manual;$answer=0;$err="Помилка індексу";
        $form="";$form_htm=RD."/tpl/jmoving_storage_select_bug_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select * from J_MOVING_SELECT_STR where jmoving_id='$jmoving_id' and select_id='$select_id' and id='$str_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $art_id=$db->result($r,0,"art_id");
            $article_nr_displ=$db->result($r,0,"article_nr_displ"); $article_name=$this->getArticleName($art_id);
            $brand_id=$db->result($r,0,"brand_id");$brand_name=$cat->getBrandName($brand_id);
            $amount=$db->result($r,0,"amount");
            $amount_barcodes=$db->result($r,0,"amount_barcodes");
            $dif_amount_barcodes=$amount-$amount_barcodes;
            $form=str_replace("{select_id}",$select_id,$form);
            $form=str_replace("{jmoving_id}",$jmoving_id,$form);
            $form=str_replace("{str_id}",$str_id,$form);
            $form=str_replace("{article_name}",$article_name,$form);
            $form=str_replace("{brand_name}",$brand_name,$form);
            $form=str_replace("{article_nr_displ}",$article_nr_displ,$form);
            $form=str_replace("{dif_amount_barcode}",$dif_amount_barcodes,$form);
            $form=str_replace("{amount}",$amount,$form);
            $form=str_replace("{bugs_list}",$manual->showManualSelectList("storage_select_bug",""),$form);
            $answer=1;$err="";
        }
        return array($answer,$err,$form,"");
    }

    function saveJmovingStorageSelectBugForm($jmoving_id,$select_id,$str_id,$storage_select_bug,$amount_bug){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка індексу!!";
        $storage_select_bug_list=""; $id=$dif_amount_barcodes=$new_amount_bug=$amount_barcodes=$amount_barcodes_noscan=0;
        $jmoving_id=$slave->qq($jmoving_id);$select_id=$slave->qq($select_id);$str_id=$slave->qq($str_id);$storage_select_bug=$slave->qq($storage_select_bug);$amount_bug=$slave->qq($amount_bug);
        if ($jmoving_id>0 && $select_id>0 && $str_id>0 && $storage_select_bug>0 && $amount_bug>0){
            $r=$db->query("select * from J_MOVING_SELECT_STR where jmoving_id='$jmoving_id' and select_id='$select_id' and id='$str_id' limit 0,1;");$n=$db->num_rows($r);
            if ($n==1){
                $id=$db->result($r,0,"id");
                $art_id=$db->result($r,0,"art_id");
                $article_nr_displ=$db->result($r,0,"article_nr_displ");
                $amount=$db->result($r,0,"amount");
                $amount_barcodes=$db->result($r,0,"amount_barcodes");
                $amount_barcodes_noscan=$db->result($r,0,"amount_barcodes_noscan");
                $amount_bug_ex=$db->result($r,0,"amount_bug");
                $storage_id_from=$db->result($r,0,"storage_id_from");
                $cell_id_from=$db->result($r,0,"cell_id_from");
                $cell_use=0;if ($cell_id_from>0){$cell_use=1;}
                $ex_dif_amount=$amount-$amount_bug;

                if ($ex_dif_amount<($amount_bug_ex)){
                    $answer=0;$err="Кількість відхилення не відповідає обліковій кількості";
                }
                if ($ex_dif_amount>=$amount_bug_ex){
                    $new_amount_bug=$amount_bug+$amount_bug_ex;
                    $noscan_am=$amount-$amount_bug_ex-$amount_barcodes-$amount_barcodes_noscan-$amount_bug;
                    //if ($noscan_am>=0){
                        //$amount_barcodes_noscan=$amount_barcodes_noscan-$noscan_am;
                    //}
                    if ($noscan_am<0){
                        $amount_barcodes_noscan=$amount_barcodes_noscan+$noscan_am;
                    }

                    if ($amount_barcodes_noscan<0){
                        $amount_barcodes=$amount_barcodes+$amount_barcodes_noscan;
                        $amount_barcodes_noscan=0;
                    }

                    if ($amount_barcodes<0){$amount_barcodes=0;}

                    $db->query("update J_MOVING_SELECT_STR set amount_bug='$new_amount_bug', amount_barcodes='$amount_barcodes', amount_barcodes_noscan='$amount_barcodes_noscan' where id='$id' limit 1;");
                    $dif_amount_barcodes=$amount-$amount_barcodes-$amount_barcodes_noscan-$new_amount_bug;
                    if ($dif_amount_barcodes<0){$dif_amount_barcodes=0;}

                    $db->query("insert into J_MOVING_SELECT_STR_BUG (`jmoving_id`,`select_id`,`art_id`,`str_id`,`article_nr_displ`,`storage_select_bug`,`amount_bug`) values ('$jmoving_id','$select_id','$art_id','$str_id','$article_nr_displ','$storage_select_bug','$amount_bug');");

                    /*ОБНОВИТЬ РЕЗЕРВЫ ПОСЛЕ ФИКСАЦИИ ОТКЛОНЕНИЯ*/
                    $this->updateStockStorageBug($art_id,$storage_id_from,$cell_id_from,$cell_use,$amount_bug);

                    $storage_select_bug_list=$this->getStorageSelectBugList($jmoving_id,$select_id,$art_id,$id);
                    $answer=1;$err="";
                }
            }
        }else{ $answer=0;$err="Помилка штрих-коду";}
        return array($answer,$err,$id,$storage_select_bug_list,$dif_amount_barcodes,$new_amount_bug,$amount_barcodes,$amount_barcodes_noscan);
    }

    function getStorageSelectBugList($jmoving_id,$select_id,$art_id,$str_id){$db=DbSingleton::getDb();$manual=new manual;$list="";
        $r=$db->query("select * from J_MOVING_SELECT_STR_BUG where jmoving_id='$jmoving_id' and select_id='$select_id' and art_id='$art_id' and str_id='$str_id' order by id asc;");$n=$db->num_rows($r);
        //if ($n==0){
            //$r=$db->query("select * from J_MOVING_SELECT_STR_BUG where jmoving_id='$jmoving_id' and select_id='$select_id' and art_id='$art_id' order by id asc;");$n=$db->num_rows($r);
        //}
        for ($i=1;$i<=$n;$i++){
            $storage_select_bug=$db->result($r,$i-1,"storage_select_bug");
            $amount_bug=$db->result($r,$i-1,"amount_bug");
            $storage_select_bug_name=$manual->getManualMCaption("storage_select_bug",$storage_select_bug);
            $list.="$amount_bug"."шт. - $storage_select_bug_name";if ($i<$n){$list.="<br>";}
        }
        return $list;
    }

    function getJmovingBugList($jmoving_id,$art_id){$db=DbSingleton::getDb();$manual=new manual;$list="";
        $r=$db->query("select * from J_MOVING_STR_BUG where jmoving_id='$jmoving_id' and art_id='$art_id' order by id asc;");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $storage_select_bug=$db->result($r,$i-1,"storage_select_bug");
            $amount_bug=$db->result($r,$i-1,"amount_bug");
            $storage_select_bug_name=$manual->getManualMCaption("storage_select_bug",$storage_select_bug);
            $list.="$amount_bug"."шт. - $storage_select_bug_name";if ($i<$n){$list.="<br>";}
        }
        return $list;
    }

    function getJmovingBugListTrue($jmoving_str){$db=DbSingleton::getDb();$manual=new manual;$list="";
        $r=$db->query("select * from J_MOVING_STR_BUG where str_id='$jmoving_str' order by id asc;");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $storage_select_bug=$db->result($r,$i-1,"storage_select_bug");
            $amount_bug=$db->result($r,$i-1,"amount_bug");
            $storage_select_bug_name=$manual->getManualMCaption("storage_select_bug",$storage_select_bug);
            $list.="$amount_bug"."шт. - $storage_select_bug_name";if ($i<$n){$list.="<br>";}
        }
        return $list;
    }

    function addStatusJmoving($jmoving_id,$status) {$db=DbSingleton::getDb(); $user_id=$_SESSION["media_user_id"]; $data=date("Y-m-d H:i:sa");
        if ($jmoving_id>0) {
            $r=$db->query("select * from J_MOVING where id='$jmoving_id' limit 0,1;");
            $user_accepting=$db->result($r,0,"user_accepting");
            $user_accepted=$db->result($r,0,"user_accepted");
            if ($status==1 && $user_accepting==0) //приймається
                $db->query("update J_MOVING set user_accepting='$user_id', user_data_accepting='$data' where id='$jmoving_id';");
            if ($status==2 && $user_accepted==0) //прийнято
                $db->query("update J_MOVING set user_accepted='$user_id', user_data_accepted='$data' where id='$jmoving_id';");
        }
    }

    function showJmovingStorageAcceptForm($jmoving_id){$db=DbSingleton::getDb();$cat=new catalogue;$list="";session_start();$user_name=$_SESSION["user_name"];
        $form="";$form_htm=RD."/tpl/jmoving_accept_barcode_list.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $this->setJmovingStorageAcceptStart($jmoving_id);
        $r=$db->query("select * from J_MOVING_STR where jmoving_id='$jmoving_id' order by id asc;");$n=$db->num_rows($r);
        $this->addStatusJmoving($jmoving_id,1);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $art_id=$db->result($r,$i-1,"art_id");
            $article_nr_displ=$db->result($r,$i-1,"article_nr_displ"); $article_name=$this->getArticleName($art_id);
            $brand_id=$db->result($r,$i-1,"brand_id");$brand_name=$cat->getBrandName($brand_id);
            $amount=$db->result($r,$i-1,"amount");
            $amount_barcodes=$db->result($r,$i-1,"amount_barcodes");
            $amount_barcodes_noscan=$db->result($r,$i-1,"amount_barcodes_noscan");
            $amount_bug=$db->result($r,$i-1,"amount_bug");
            $dif_amount_barcodes=$amount-$amount_barcodes-$amount_barcodes_noscan;
            $bug_list=$this->getJmovingBugListTrue($id);
    //		$bug_list=$this->getJmovingBugList($jmoving_id,$art_id);
            $list.="<tr>
                <td align='center'>$i</td>
                <td align='center' id='amrai_$id'>$article_nr_displ</td>
                <td align='center' id='amrab_$id'>$brand_name</td>
                <td align='left' id='amran_$id'>$article_name</td>
                <td align='center'>$amount</td>
                <td align='center' id='amr_$id'>$amount_barcodes</td>
                <td align='center' id='amrd_$id'>$dif_amount_barcodes</td>
                <td align='center' id='amrns_$id'>$amount_barcodes_noscan</td>
                <td id='amrd_$id' align='center'><button class='btn btn-xs btn-default' onclick='showJmovingAcceptNoscanForm(\"$jmoving_id\",\"$art_id\",\"$id\");' title='Фіксація без сканування' alt='Фіксація без сканування'><i class='fa fa-cube'></i></button></td>
                <td id='amrd_$id' align='center'><button class='btn btn-xs btn-danger' onclick='showJmovingAcceptBugForm(\"$jmoving_id\",\"$id\");' title='відхилення/брак/недостача' alt='відхилення/брак/недостача'><i class='fa fa-bug'></i></button></td>
                <td align='center' id='ambg_$id'>$amount_bug</td>
                <td id='ssbug_$id'>$bug_list</td>
            </tr>";
        }
        $form=str_replace("{ArticlesList}",$list,$form);
        $form=str_replace("{jmoving_id}",$jmoving_id,$form);
        //list($select_nom,$select_data,$storage_id,$storage_name,$storage_name_to,$articles_amount,$amount,$volume,$weight_netto,$weight_brutto,$jmoving_comment,$select_datatime)=$this->getJmovingSkladStorageSelectInfo($jmoving_id,$select_id);
        $jmoving_nom=$jmoving_data=$jmoving_comment=""; //???
        $form=str_replace("{jmoving_nom}",$jmoving_nom,$form);
        $form=str_replace("{jmoving_data}",$jmoving_data,$form);
        $form=str_replace("{jmoving_comment}",$jmoving_comment,$form);
        $form=str_replace("{user_name}",$user_name,$form);
        $form=str_replace("{curtime}",date("Y-m-d H:i:s"),$form);
        //$this->addJuornalRecord($jmoving_id,$select_id,47);
        $answer=1;$err="";
        return array($answer,$err,$form,"Отримання товару по штрих-кодам");
    }

    function setJmovingStorageAcceptStart($jmoving_id){$db=DbSingleton::getDb();$slave=new slave;
        $jmoving_id=$slave->qq($jmoving_id);
        if ($jmoving_id>0){
            $r=$db->query("select status_jmoving from J_MOVING where id='$jmoving_id' limit 0,1;");$n=$db->num_rows($r);
            if ($n==1){
                $status_jmoving=$db->result($r,0,"status_jmoving");
                if ($status_jmoving==48){
                    $db->query("update J_MOVING set status_jmoving='49' where id='$jmoving_id' limit 1;");
                }
            }
        }
        return;
    }

    function saveJmovingAcceptBarcodeForm($jmoving_id,$barcode){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка індексу!!";
        $jmoving_id=$slave->qq($jmoving_id);$barcode=$slave->qq($barcode);
        $id=$amount_barcodes=$dif_amount_barcodes=0;
        if ($jmoving_id>0 && $barcode!=""){
            $art_id=$this->getArtIdByBarcode($barcode);
            $r=$db->query("select * from J_MOVING_STR where jmoving_id='$jmoving_id' and art_id='$art_id' and amount>amount_barcodes limit 0,1;");$n=$db->num_rows($r);
            if ($n==1){
                $id=$db->result($r,0,"id");
                $amount=$db->result($r,0,"amount");
                $amount_barcodes=$db->result($r,0,"amount_barcodes");
                $amount_barcodes_noscan=$db->result($r,0,"amount_barcodes_noscan");
                $ex=$amount_barcodes+$amount_barcodes_noscan;
                if ($amount>$ex){
                    $amount_barcodes+=1;
                    $dif_amount_barcodes=$amount-$amount_barcodes-$amount_barcodes_noscan;
                    $db->query("update J_MOVING_STR set amount_barcodes='$amount_barcodes' where id='$id' limit 1;");
                    $answer=1;$err="";
                }
            }
        } else {$answer=0;$err="Помилка штрих-коду";}
        return array($answer,$err,$id,$amount_barcodes,$dif_amount_barcodes);
    }

    function showJmovingStorageSelectNoscanForm($jmoving_id,$select_id,$art_id,$str_id){$db=DbSingleton::getDb();$cat=new catalogue;$answer=0;$err="Помилка індексу";
        $form="";$form_htm=RD."/tpl/jmoving_storage_select_noscan_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select * from J_MOVING_SELECT_STR where jmoving_id='$jmoving_id' and select_id='$select_id' and id='$str_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $art_id=$db->result($r,0,"art_id");
            $article_nr_displ=$db->result($r,0,"article_nr_displ"); $article_name=$this->getArticleName($art_id);
            $brand_id=$db->result($r,0,"brand_id");$brand_name=$cat->getBrandName($brand_id);
            $amount=$db->result($r,0,"amount");
            $amount_barcodes=$db->result($r,0,"amount_barcodes");
            $amount_barcodes_noscan=$db->result($r,0,"amount_barcodes_noscan");
            $dif_amount_barcodes=$amount-$amount_barcodes-$amount_barcodes_noscan;
            $form=str_replace("{jmoving_id}",$jmoving_id,$form);
            $form=str_replace("{select_id}",$select_id,$form);
            $form=str_replace("{str_id}",$str_id,$form);
            $form=str_replace("{art_id}",$art_id,$form);
            $form=str_replace("{article_name}",$article_name,$form);
            $form=str_replace("{brand_name}",$brand_name,$form);
            $form=str_replace("{article_nr_displ}",$article_nr_displ,$form);
            $form=str_replace("{dif_amount_barcode}",$dif_amount_barcodes,$form);
            $form=str_replace("{amount_barcodes_noscan}",$amount_barcodes_noscan,$form);
            $answer=1;$err="";
        }
        return array($answer,$err,$form,"");
    }

    function saveJmovingStorageSelectNoscanForm($jmoving_id,$select_id,$art_id,$str_id,$amount_barcode_noscan){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка індексу!!";
        $jmoving_id=$slave->qq($jmoving_id);$select_id=$slave->qq($select_id);$art_id=$slave->qq($art_id);$str_id=$slave->qq($str_id);$amount_barcode_noscan=$slave->qq($amount_barcode_noscan);
        $id=$dif_amount_barcodes=$new_amount_barcode_noscan=0;
        if ($jmoving_id>0 && $select_id>0 && $art_id>0 && $str_id>0 && $amount_barcode_noscan>0){
            $r=$db->query("select * from J_MOVING_SELECT_STR where jmoving_id='$jmoving_id' and art_id='$art_id' and id='$str_id' limit 0,1;");$n=$db->num_rows($r);
            if ($n==1){
                $id=$db->result($r,0,"id");
                $amount=$db->result($r,0,"amount");
                $amountBarcodes=$db->result($r,0,"amount_barcodes");
                $amountBarcodesNoscan=$db->result($r,0,"amount_barcodes_noscan");
                $amountBug=$db->result($r,0,"amount_bug");
                $ex_dif_amount=($amount-$amountBarcodes-$amountBarcodesNoscan-$amountBug);
                if ($ex_dif_amount<$amount_barcode_noscan){
                    $answer=0;$err="Кількість не відповідає обліковій кількості $ex_dif_amount";
                }
                if ($ex_dif_amount>=$amount_barcode_noscan){
                    $new_amount_barcode_noscan=$amount_barcode_noscan+$amountBarcodesNoscan;
                    $dif_amount_barcodes=$amount-$amountBarcodes-$new_amount_barcode_noscan-$amountBug;
                    $db->query("update J_MOVING_SELECT_STR set amount_barcodes_noscan='$new_amount_barcode_noscan' where id='$id' limit 1;");
                    $answer=1;$err="";
                }
            }
        } else {$answer=0;$err="Помилка штрих-коду";}
        return array($answer,$err,$id,$dif_amount_barcodes,$new_amount_barcode_noscan);
    }

    function showJmovingAcceptNoscanForm($jmoving_id,$art_id,$str_id){$db=DbSingleton::getDb();$cat=new catalogue;$answer=0;$err="Помилка індексу";
        $form="";$form_htm=RD."/tpl/jmoving_accept_noscan_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select * from J_MOVING_STR where jmoving_id='$jmoving_id' and id='$str_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $art_id=$db->result($r,0,"art_id");
            $article_nr_displ=$db->result($r,0,"article_nr_displ"); $article_name=$this->getArticleName($art_id);
            $brand_id=$db->result($r,0,"brand_id");$brand_name=$cat->getBrandName($brand_id);
            $amount=$db->result($r,0,"amount");
            $amount_barcodes=$db->result($r,0,"amount_barcodes");
            $amount_barcodes_noscan=$db->result($r,0,"amount_barcodes_noscan");
            $dif_amount_barcodes=$amount-$amount_barcodes-$amount_barcodes_noscan;
            $form=str_replace("{jmoving_id}",$jmoving_id,$form);
            $form=str_replace("{str_id}",$str_id,$form);
            $form=str_replace("{art_id}",$art_id,$form);
            $form=str_replace("{article_name}",$article_name,$form);
            $form=str_replace("{brand_name}",$brand_name,$form);
            $form=str_replace("{article_nr_displ}",$article_nr_displ,$form);
            $form=str_replace("{dif_amount_barcode}",$dif_amount_barcodes,$form);
            $form=str_replace("{amount_barcodes_noscan}",$amount_barcodes_noscan,$form);
            $answer=1;$err="";
        }
        return array($answer,$err,$form,"");
    }

    function saveJmovingAcceptNoscanForm($jmoving_id,$art_id,$str_id,$amount_barcode_noscan){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка індексу!!";
        $jmoving_id=$slave->qq($jmoving_id);$art_id=$slave->qq($art_id);$str_id=$slave->qq($str_id);$amount_barcode_noscan=$slave->qq($amount_barcode_noscan);
        $id=$dif_amount_barcodes=$new_amount_barcode_noscan=0;
        if ($jmoving_id>0 && $art_id>0 && $str_id>0 && $amount_barcode_noscan>0){
            $r=$db->query("select * from J_MOVING_STR where jmoving_id='$jmoving_id' and art_id='$art_id' and id='$str_id' limit 0,1;");$n=$db->num_rows($r);
            if ($n==1){
                $id=$db->result($r,0,"id");
                $amount=$db->result($r,0,"amount");
                $amountBarcodes=$db->result($r,0,"amount_barcodes");
                $amountBarcodesNoscan=$db->result($r,0,"amount_barcodes_noscan");
                $ex_dif_amount=$amount-$amountBarcodes-$amountBarcodesNoscan;
                if ($ex_dif_amount<$amount_barcode_noscan){
                    $answer=0;$err="Кількість не відповідає обліковій кількості $ex_dif_amount";
                }
                if ($ex_dif_amount>=$amount_barcode_noscan){
                    $new_amount_barcode_noscan=$amountBarcodesNoscan+$amount_barcode_noscan;
                    $dif_amount_barcodes=$amount-$amountBarcodes-$new_amount_barcode_noscan;
                    $db->query("update J_MOVING_STR set amount_barcodes_noscan='$new_amount_barcode_noscan' where id='$id' limit 1;");
                    $answer=1;$err="";
                }
            }
        } else {$answer=0;$err="Помилка штрих-коду";}
        return array($answer,$err,$id,$dif_amount_barcodes,$new_amount_barcode_noscan);
    }

    function showJmovingAcceptBugForm($jmoving_id,$str_id){$db=DbSingleton::getDb();$cat=new catalogue;$manual=new manual;$answer=0;$err="Помилка індексу";
        $form="";$form_htm=RD."/tpl/jmoving_accept_bug_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select * from J_MOVING_STR where jmoving_id='$jmoving_id' and id='$str_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $art_id=$db->result($r,0,"art_id");
            $article_nr_displ=$db->result($r,0,"article_nr_displ"); $article_name=$this->getArticleName($art_id);
            $brand_id=$db->result($r,0,"brand_id");$brand_name=$cat->getBrandName($brand_id);
            $amount=$db->result($r,0,"amount");
            $amount_barcodes=$db->result($r,0,"amount_barcodes");
            $dif_amount_barcodes=$amount-$amount_barcodes;
            $form=str_replace("{jmoving_id}",$jmoving_id,$form);
            $form=str_replace("{str_id}",$str_id,$form);
            $form=str_replace("{article_name}",$article_name,$form);
            $form=str_replace("{brand_name}",$brand_name,$form);
            $form=str_replace("{article_nr_displ}",$article_nr_displ,$form);
            $form=str_replace("{dif_amount_barcode}",$dif_amount_barcodes,$form);
            $form=str_replace("{amount}",$amount,$form);
            $form=str_replace("{bugs_list}",$manual->showManualSelectList("storage_select_bug",""),$form);
            $answer=1;$err="";
        }
        return array($answer,$err,$form,"");
    }

    function checkJmovingBugs($jmoving_id) {$db=DbSingleton::getDb(); $kol=0;
        $r=$db->query("select * from J_MOVING_STR where jmoving_id='$jmoving_id';"); $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $amount_bug=$db->result($r,$i-1,"amount_bug");
            $kol+=intval($amount_bug);
        }
        $kol>0 ? $result=true : $result=false;
        return $result;
    }

    function saveJmovingAcceptBugForm($jmoving_id,$str_id,$storage_select_bug,$amount_bug){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка індексу!!";
        $jmoving_id=$slave->qq($jmoving_id);$str_id=$slave->qq($str_id);$storage_select_bug=$slave->qq($storage_select_bug);$amount_bug=$slave->qq($amount_bug);
        $id=$dif_amount_barcodes=$new_amount_bug=0; $bug_list="";
        if ($jmoving_id>0 && $str_id>0 && $storage_select_bug>0 && $amount_bug>0){
            $r=$db->query("select * from J_MOVING_STR where jmoving_id='$jmoving_id' and id='$str_id' limit 0,1;");$n=$db->num_rows($r);
            if ($n==1){
                $id=$db->result($r,0,"id");
                $amount=$db->result($r,0,"amount");
                $amount_barcodes=$db->result($r,0,"amount_barcodes");
                $amount_barcodes_noscan=$db->result($r,0,"amount_barcodes_noscan");
                $amount_bug_ex=$db->result($r,0,"amount_bug");

                $ex_dif_amount=$amount-$amount_barcodes;
                if ($ex_dif_amount!=$amount_bug){
                    $answer=0;$err="Кількість відхилення не відповідає обліковій кількості $ex_dif_amount";
                }
                if ($ex_dif_amount>=($amount_bug+$amount_bug_ex)){
                    $dif_amount_barcodes=$amount-$amount_barcodes-$amount_barcodes_noscan-$amount_bug-$amount_bug_ex;
                    $new_amount_bug=$amount_bug+$amount_bug_ex;
                    $db->query("update J_MOVING_STR set amount_bug='$new_amount_bug' where id='$id' limit 1;");$art_id=0;$article_nr_displ="";//???
                    $db->query("insert into J_MOVING_STR_BUG (`jmoving_id`,`art_id`,`str_id`,`article_nr_displ`,`storage_select_bug`,`amount_bug`) values ('$jmoving_id','$art_id','$id','$article_nr_displ','$storage_select_bug','$amount_bug');");
                    //$bug_list=$this->getJmovingBugList($jmoving_id,$art_id);
                    $bug_list=$this->getJmovingBugListTrue($id);
                    $answer=1;$err="";
                }
            }
        } else {$answer=0;$err="Помилка відхилення";}
        return array($answer,$err,$id,$bug_list,$dif_amount_barcodes,$new_amount_bug);
    }

    function finishJmovingAcceptForm($jmoving_id){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;$answer=0;$err="Помилка індексу!!";
        $jmoving_id=$slave->qq($jmoving_id);$id=$storage_id_to=$cell_id_to=$cell_use_to=0;$rr_amount=$rr_reserv=0;$er_to=$er_from=0;$parrent_doc_id=$parrent_type_id=0;$select_id=$storage_id_from=0;
        if ($jmoving_id>0){
            $rc=$db->query("select * from J_MOVING_STR where jmoving_id='$jmoving_id' and amount>(amount_barcodes+amount_barcodes_noscan);");$nc=$db->num_rows($rc);
            if ($nc>0){
                $answer=0;$err="Не завершено перевірку по штрих-кодам";
            }
            if ($nc==0){
                $this->addStatusJmoving($jmoving_id,2);
                $r=$db->query("select * from J_MOVING where id='$jmoving_id' limit 0,1;");$n=$db->num_rows($r);
                if ($n==1){
                    $storage_id_to=$db->result($r,0,"storage_id_to");
                    $cell_use_to=$db->result($r,0,"cell_use");
                    $cell_id_to=$db->result($r,0,"cell_id_to");
                    if ($cell_id_to>0){$cell_use_to=1;}
                    $parrent_type_id=$db->result($r,0,"parrent_type_id");
                    $parrent_doc_id=$db->result($r,0,"parrent_doc_id");
                }

                if ($storage_id_to>0){
                    $query="select ss.* from J_SELECT_STR ss 
                        left outer join J_SELECT sel on sel.id=ss.select_id
                    where sel.parrent_doc_type_id='1' and sel.parrent_doc_id='$jmoving_id';";
                    $rs=$db->query($query);$ns=$db->num_rows($rs);
                    for ($is=1;$is<=$ns;$is++){
                        $art_id=$db->result($rs,$is-1,"art_id");
                        $select_id=$db->result($rs,$is-1,"select_id");
                        $amount=$db->result($rs,$is-1,"amount_collect");
                        $storage_id_from=$db->result($rs,$is-1,"storage_id_from");
                        $cell_id_from=$db->result($rs,$is-1,"cell_id_from");
                        $cell_use_from=0;if ($cell_id_from>0){$cell_use_from=1;}

                        $slave->addJuornalArtDocs(2,$jmoving_id,$art_id,$amount);

                        $er_from=$this->updateStockFromStorage($art_id,$storage_id_from,$cell_id_from,$cell_use_from,$amount);
                        if ($er_from==0){
                            $er_to=$this->updateStockToStorage($art_id,$storage_id_to,$cell_id_to,$cell_use_to,$amount);
                        }
                        if ($er_to==1 || $er_from==1){$is+=1;$answer=0;$err="Помилка оновлення залишків на складі";}
                    }
                }
                if ($er_to==0 && $er_from==0){
                    $db->query("update J_MOVING set status_jmoving='57', `data`=CURDATE() where id='$jmoving_id' limit 1;");
                    $this->addJuornalRecord($jmoving_id,$select_id,57);
                    $answer=1;$err="";
                    if ($parrent_doc_id>0 && $parrent_type_id>0){
                        if ($parrent_type_id==1){ // JMOVING FOR DP, create local storsel
                            $dp=new dp;$dp_id=$parrent_doc_id; $tpoint_id=$dp->getDpTpoint($dp_id);
                            $select_id=$dp->createStorsel($dp_id,$tpoint_id,$storage_id_to);$s_volume=0;$s_weight_netto=0;$s_amount=0;$s_articles_amount=0;

                            $query="select * from J_MOVING_STR where jmoving_id='$jmoving_id';";
                            $r1=$db->query($query);$n1=$db->num_rows($r1);
                            for ($i1=1;$i1<=$n1;$i1++){
                                $id=$db->result($r1,$i1-1,"id");
                                $art_id=$db->result($r1,$i1-1,"art_id");
                                $article_nr_displ=$db->result($r1,$i1-1,"article_nr_displ");
                                $brand_id=$db->result($r1,$i1-1,"brand_id");
                                $amount=$db->result($r1,$i1-1,"amount");
                                $amount_bug=$db->result($r1,$i1-1,"amount_bug");
                                $amount_to_storsel=$amount-$amount_bug;
                                //$weight_netto=$db->result($r1,$i1-1,"weight_netto");
                                //$volume=$db->result($r1,$i1-1,"volume");
                                //$s_volume+=$volume;$s_weight_netto+=$weight_netto;
                                $s_amount+=$amount_to_storsel;$s_articles_amount+=1;
                                $rt2=$db->query("select max(id) as mid from J_SELECT_STR;");$str_id=0+$db->result($rt2,0,"mid")+1;
                                $db->query("insert into J_SELECT_STR (`id`,`select_id`,`art_id`,`article_nr_displ`,`brand_id`,`amount`,`storage_id_from`,`cell_id_from`,`status`) values ('$str_id','$select_id','$art_id','$article_nr_displ','$brand_id','$amount_to_storsel','$storage_id_to','$cell_id_to',1);");
                                $db->query("update J_DP_STR set cur_select_str_id='$str_id', amount_collect='$amount_to_storsel', location_storage_id='$storage_id_to', status_dps='94' where art_id='$art_id' and dp_id='$dp_id' and storage_id_from='$storage_id_from' limit 1;\n");

                                $slave->addJuornalArtDocs(2,$jmoving_id,$art_id,$amount_to_storsel);

                                //move art_id to reserv
                                $rr=$dbt->query("select * from T2_ARTICLES_STRORAGE where ART_ID='$art_id' and STORAGE_ID ='$storage_id_to' limit 0,1;");$nr=$dbt->num_rows($rr);
                                if ($nr==1){
                                    $rr_amount=$dbt->result($rr,0,"AMOUNT");
                                    $rr_reserv=$dbt->result($rr,0,"RESERV_AMOUNT");
                                }
                                $rr_amount-=$amount_to_storsel;$rr_reserv+=$amount_to_storsel;
                                $dbt->query("update T2_ARTICLES_STRORAGE set AMOUNT='$rr_amount', `RESERV_AMOUNT`='$rr_reserv' where ART_ID='$art_id' and STORAGE_ID ='$storage_id_to';");

                                //CELL SET RESERV
                                if ($cell_use_to==1){
                                    $rr=$dbt->query("select * from T2_ARTICLES_STRORAGE_CELLS where ART_ID='$art_id' and STORAGE_ID ='$storage_id_to' and STORAGE_CELLS_ID='$cell_id_to' limit 0,1;");$nr=$dbt->num_rows($rr);
                                    if ($nr==1){
                                        $rr_amount=$dbt->result($rr,0,"AMOUNT");
                                        $rr_reserv=$dbt->result($rr,0,"RESERV_AMOUNT");
                                    }
                                    $rr_amount-=$amount_to_storsel;$rr_reserv+=$amount_to_storsel;
                                    $dbt->query("update T2_ARTICLES_STRORAGE_CELLS set AMOUNT='$rr_amount', `RESERV_AMOUNT`='$rr_reserv' where ART_ID='$art_id' and STORAGE_ID ='$storage_id_to' and STORAGE_CELLS_ID='$cell_id_to';");
                                }
                            }
                            $db->query("update J_SELECT set `amount`='$s_amount', `articles_amount`='$s_articles_amount',`volume`='$s_volume', `weight_netto`='$s_weight_netto' where id='$select_id' limit 1;");
                        }
                    }
                }
            }
        } else {$answer=0;$err="Помилка штрих-коду";}
        return array($answer,$err,$id);
    }

    function finishJmovingLocalAcceptForm($jmoving_id){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка індексу!!";$id=0;
        $jmoving_id=$slave->qq($jmoving_id);$storage_id_to=$select_id=0;$er_from=0;
        if ($jmoving_id>0){
            $r=$db->query("select * from J_MOVING where id='$jmoving_id' limit 0,1;");$n=$db->num_rows($r);
            if ($n==1){
                $storage_id_to=$db->result($r,0,"storage_id_to");
            }
            if ($storage_id_to>0){
                $rs=$db->query("select js.*, jss.cell_id_from as cell_id_from2, jss.cell_id_to as cell_id_to2 
                from J_MOVING_STR js 
                    left outer join J_MOVING_SELECT_STR jss on (jss.select_id=js.select_id)
                where js.jmoving_id='$jmoving_id' and jss.jmoving_id='$jmoving_id' group by js.id;");$ns=$db->num_rows($rs);
                for ($is=1;$is<=$ns;$is++){
                    $art_id=$db->result($rs,$is-1,"art_id");
                    $select_id=$db->result($rs,$is-1,"select_id");
                    $amount=$db->result($rs,$is-1,"amount");
                    $cell_id_from2=$db->result($rs,$is-1,"cell_id_from");
                    $cell_id_to2=$db->result($rs,$is-1,"cell_id_to");
                    $slave->addJuornalArtDocs(2,$jmoving_id,$art_id,$amount);
                    $er_from=$this->updateStockFromStorageLocal($art_id,$storage_id_to,$cell_id_from2,$cell_id_to2,$amount);
                }
            }
            if ($er_from==0){
                $prefix=$this->get_jmoving_prefix($jmoving_id);
                $db->query("update J_MOVING set status_jmoving='57', `oper_status`='31' where id='$jmoving_id' limit 1;");
                $r=$db->query("select max(doc_nom) as doc_nom from J_MOVING where oper_status='31' and status='1' and type_id='0' and prefix='В-ПР' and status_jmoving='57' limit 0,1;");$doc_nom=0+$db->result($r,0,"doc_nom")+1;
                $db->query("update J_MOVING set prefix='$prefix', `doc_nom`='$doc_nom', `data`=CURDATE() where id='$jmoving_id' limit 1;");
                $this->addJuornalRecord($jmoving_id,$select_id,57);
                $answer=1;$err="";
            }
        } else {$answer=0;$err="Помилка штрих-коду";}
        return array($answer,$err,$id);
    }

    function updateStockStorageBug($art_id,$storage_id_from,$cell_id_from,$cell_use,$amount){$dbt=DbSingleton::getTokoDb(); $er=1;
        $r=$dbt->query("select `AMOUNT`, `RESERV_AMOUNT` from T2_ARTICLES_STRORAGE where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_from' limit 0,1;");$n=$dbt->num_rows($r);
        if ($n==1){
            $t2s_reserv_amount=$dbt->result($r,0,"RESERV_AMOUNT");
            $t2s_amount=$dbt->result($r,0,"AMOUNT");
            if ($amount<=$t2s_reserv_amount){
                $t2s_reserv_amount=$t2s_reserv_amount-$amount;
                $t2s_amount=$t2s_amount+$amount;
                $dbt->query("update T2_ARTICLES_STRORAGE set `RESERV_AMOUNT`='$t2s_reserv_amount',`AMOUNT`='$t2s_amount' where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_from' limit 1;");
                if ($cell_use==1){
                    $r1=$dbt->query("select `AMOUNT`, `RESERV_AMOUNT` from T2_ARTICLES_STRORAGE_CELLS where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_from' and `STORAGE_CELLS_ID`='$cell_id_from' limit 0,1;");$n1=$dbt->num_rows($r1);
                    if ($n1==1){
                        $t2sc_reserv_amount=$dbt->result($r1,0,"RESERV_AMOUNT");
                        $t2sc_amount=$dbt->result($r1,0,"AMOUNT");
                        if ($amount>0){
                            $t2sc_reserv_amount=$t2sc_reserv_amount-$amount;
                            $t2sc_amount=$t2sc_amount+$amount;
                            $dbt->query("update T2_ARTICLES_STRORAGE_CELLS set `RESERV_AMOUNT`='$t2sc_reserv_amount', `AMOUNT`='$t2sc_amount' where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_from' and `STORAGE_CELLS_ID`='$cell_id_from' limit 1;");
                        }
                    }
                }
                $er=0;
            }
        }
        return $er;
    }

    /*function updateStockFromStorage($art_id,$storage_id_from,$cell_id_from,$cell_use,$amount){$dbt=DbSingleton::getTokoDb(); $er=1;
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
    }*/

    function updateStockFromStorage($art_id,$storage_id_from,$cell_id_from,$cell_use,$amount){$dbt=DbSingleton::getTokoDb();
        $dbt->query("update T2_ARTICLES_STRORAGE set `RESERV_AMOUNT`=`RESERV_AMOUNT` - $amount 
        where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_from' limit 1;");
        if ($cell_use==1){
            $dbt->query("update T2_ARTICLES_STRORAGE_CELLS set `RESERV_AMOUNT`=`RESERV_AMOUNT` - $amount 
            where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_from' and `STORAGE_CELLS_ID`='$cell_id_from' limit 1;");
        }
        $er=0;
        return $er;
    }

    /*function updateStockToStorage($art_id,$storage_id_to,$cell_id_to,$cell_use,$amount){$dbt=DbSingleton::getTokoDb(); $er=1;
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
    }*/

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
            $dbt->query("update T2_ARTICLES_STRORAGE set `AMOUNT`=`AMOUNT` + $amount where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_to' limit 1;");
            if ($cell_use==1){
                $r1=$dbt->query("select `AMOUNT` from T2_ARTICLES_STRORAGE_CELLS where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_to' and `STORAGE_CELLS_ID`='$cell_id_to' limit 0,1;");$n1=$dbt->num_rows($r1);
                if ($n1==0){
                    $dbt->query("insert into T2_ARTICLES_STRORAGE_CELLS (`ART_ID`,`AMOUNT`,`RESERV_AMOUNT`,`STORAGE_ID`,`STORAGE_CELLS_ID`) values ('$art_id','$amount','0','$storage_id_to','$cell_id_to');");
                }
                if ($n1==1){
                    $dbt->query("update T2_ARTICLES_STRORAGE_CELLS set `AMOUNT`=`AMOUNT` + $amount where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_to' and `STORAGE_CELLS_ID`='$cell_id_to' limit 1;");
                }
            }
            $er=0;
        }
        return $er;
    }

    function updateStockFromStorageLocal($art_id,$storage_id_from,$cell_id_from,$cell_id_to,$amount){$dbt=DbSingleton::getTokoDb();$er=1;
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

    function showSelectStatusList() {$db=DbSingleton::getDb(); $list="";
        $r=$db->query("select * from `manual` where `key`='status_jmoving' order by `mid` asc;"); $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $mcaption=$db->result($r,$i-1,"mcaption");
            $list.="<option value='$id'>$mcaption</option>";
        }
        return $list;
    }

    function getJmovingData($jmoving_id) {$db=DbSingleton::getDb();
        $r=$db->query("select * from J_MOVING where id='$jmoving_id' limit 1;");
        $prefix=$db->result($r,0,"prefix");
        $doc_nom=$db->result($r,0,"doc_nom");
        $storage_id_to=$db->result($r,0,"storage_id_to");
        $cell_id_to=$db->result($r,0,"cell_id_to");
        $type_id=$db->result($r,0,"type_id");
        return array($type_id,$prefix,$doc_nom,$storage_id_to,$cell_id_to,);
    }

    function separateJmovingByDefect($jmoving_id) {$db=DbSingleton::getDb(); $bugs=[]; $bug1=0; $bug2=0; $bug3=0; $bug4=0; $storage_id_from=0; $jmoving1=$jmoving2=$jmoving3=$jmoving4=0;
        list($type_id,$prefix,$doc_nom,$storage_id_to,$cell_id_to) = $this->getJmovingData($jmoving_id);
        $r=$db->query("select jb.storage_select_bug as bug, jb.amount_bug as bug_count, js.* from J_MOVING_STR_BUG jb
            left outer join J_MOVING_STR js on (js.id=jb.str_id) 	
        where jb.jmoving_id='$jmoving_id';"); $n=$db->num_rows($r);

        if ($n>0){
            for($i=1; $i<=$n; $i++) {
                $storage_select_bug=$db->result($r,$i-1,"bug");
                $amount_bug=$db->result($r,$i-1,"bug_count");
                $art_id=$db->result($r,$i-1,"art_id");
                $article_nr_displ=$db->result($r,$i-1,"article_nr_displ");
                $brand_id=$db->result($r,$i-1,"brand_id");
                $amount=$db->result($r,$i-1,"amount");
                $storage_id_from=$db->result($r,$i-1,"storage_id_from");
                $cell_id_from=$db->result($r,$i-1,"cell_id_from");

                if ($storage_select_bug==1) $bug1++; if ($storage_select_bug==2) $bug2++; if ($storage_select_bug==3) $bug3++; if ($storage_select_bug==4) $bug4++;

                $bugs[$i]=["storage_select_bug"=>$storage_select_bug,"art_id"=>$art_id,"article_nr_displ"=>$article_nr_displ,"brand_id"=>$brand_id,"amount"=>$amount,"storage_id_to"=>$storage_id_to,"cell_id_to"=>$cell_id_to,"amount_bug"=>$amount_bug];

                $this->storageJmovingDefect($art_id,$storage_id_to,$storage_id_from,$amount_bug);
                $this->cellsJmovingDefect($art_id,$storage_id_to,$storage_id_from,$cell_id_to,$cell_id_from,$amount_bug);
            }

            if ($bug1>0) {$comment="`Брак` згідно переміщення $prefix-$doc_nom"; $jmoving1=$this->insertIntoJmoving($type_id,$storage_id_from,$cell_id_to,$comment);}
            if ($bug2>0) {$comment="`Недостача` згідно переміщення $prefix-$doc_nom"; $jmoving2=$this->insertIntoJmoving($type_id,$storage_id_from,$cell_id_to,$comment);}
            if ($bug3>0) {$comment="`Пересорт` згідно переміщення $prefix-$doc_nom"; $jmoving3=$this->insertIntoJmoving($type_id,$storage_id_from,$cell_id_to,$comment);}
            if ($bug4>0) {$comment="`Відмова клієнта` згідно переміщення $prefix-$doc_nom"; $jmoving4=$this->insertIntoJmoving($type_id,$storage_id_from,$cell_id_to,$comment);}

            for ($i=1; $i<=$n; $i++){
                if ($bugs[$i]["storage_select_bug"]==1) {$this->insertIntoJmovingStr($jmoving1,$bugs[$i]["art_id"],$bugs[$i]["article_nr_displ"],$bugs[$i]["brand_id"],0,$bugs[$i]["storage_id_to"],$bugs[$i]["cell_id_to"],$bugs[$i]["amount_bug"]);}
                if ($bugs[$i]["storage_select_bug"]==2) {$this->insertIntoJmovingStr($jmoving2,$bugs[$i]["art_id"],$bugs[$i]["article_nr_displ"],$bugs[$i]["brand_id"],0,$bugs[$i]["storage_id_to"],$bugs[$i]["cell_id_to"],$bugs[$i]["amount_bug"]);}
                if ($bugs[$i]["storage_select_bug"]==3) {$this->insertIntoJmovingStr($jmoving3,$bugs[$i]["art_id"],$bugs[$i]["article_nr_displ"],$bugs[$i]["brand_id"],0,$bugs[$i]["storage_id_to"],$bugs[$i]["cell_id_to"],$bugs[$i]["amount_bug"]);}
                if ($bugs[$i]["storage_select_bug"]==4) {$this->insertIntoJmovingStr($jmoving4,$bugs[$i]["art_id"],$bugs[$i]["article_nr_displ"],$bugs[$i]["brand_id"],0,$bugs[$i]["storage_id_to"],$bugs[$i]["cell_id_to"],$bugs[$i]["amount_bug"]);}
            }
            $db->query("update J_MOVING set status_jmoving=57 where id='$jmoving_id';");
            $answer=1; $err="";
        }
        else {$answer=0;$err="Помилка розділення по відхиленням!";}
        return array($answer,$err);
    }

    function insertIntoJmoving($type_id,$storage_id_from,$cell_id_to,$comment) { $db=DbSingleton::getDb(); session_start(); $user_id=$_SESSION["media_user_id"];
        $r=$db->query("select max(id) as mid from J_MOVING;"); $jmoving_id=0+$db->result($r,0,"mid")+1; $doc_nom=$this->get_df_doc_nom_new(); $prefix=$this->prefix_new;
        $db->query("insert into J_MOVING (`id`,`type_id`,`prefix`,`doc_nom`,`user_id`,`data`,`storage_id_to`,`cell_id_to`,`status_jmoving`,`oper_status`,`comment`) values ('$jmoving_id','$type_id','$prefix','$doc_nom','$user_id',CURDATE(),'$storage_id_from','$cell_id_to','44','30','$comment');");
        return $jmoving_id;
    }

    function insertIntoJmovingStr($jmoving_id,$art_id,$article_nr_displ,$brand_id,$amount,$storage_id_to,$cell_id_from,$amount_bug) { $db=DbSingleton::getDb(); session_start(); $user_id=$_SESSION["media_user_id"];
        $rstr=$db->query("select * from J_MOVING_STR where jmoving_id='$jmoving_id' and art_id='$art_id';"); $nstr=$db->num_rows($rstr); $old_amount_bug=$db->result($rstr,0,"amount_bug"); $id=0;
        if ($nstr>0){
            $new_amount_bug=intval($old_amount_bug)+intval($amount_bug);
            $db->query("update J_MOVING_STR set amount_bug='$new_amount_bug' where jmoving_id='$jmoving_id' and art_id='$art_id';");
        }
        else{
            $r=$db->query("select max(id) as mid from J_MOVING_STR;"); $id=0+$db->result($r,0,"mid")+1;
            $db->query("insert into J_MOVING_STR (`id`,`jmoving_id`,`art_id`,`article_nr_displ`,`brand_id`,`amount`,`storage_id_from`,`cell_id_from`,`user_id`) values ('$id','$jmoving_id','$art_id','$article_nr_displ','$brand_id','$amount_bug','$storage_id_to','$cell_id_from','$user_id');");
        }
        return $id;
    }

    function storageJmovingDefect($art_id,$storage_to_id,$storage_from_id,$reserv) { $dbt=DbSingleton::getTokoDb();
        $r1=$dbt->query("select * from T2_ARTICLES_STRORAGE where ART_ID='$art_id' and STORAGE_ID='$storage_from_id' limit 1;");
        //$amount1=floatval($dbt->result($r1,0,"AMOUNT"));
        $reserv_amount1=floatval($dbt->result($r1,0,"RESERV_AMOUNT"));
        $nerezerv=$reserv_amount1-floatval($reserv);

        $r2=$dbt->query("select * from T2_ARTICLES_STRORAGE where ART_ID='$art_id' and STORAGE_ID='$storage_to_id' limit 1;");
        $amount2=floatval($dbt->result($r2,0,"AMOUNT"));
        $reserv_amount2=floatval($dbt->result($r2,0,"RESERV_AMOUNT"));
        $amount=$amount2 + floatval($nerezerv);
        $reserv_amount=$reserv_amount2 + floatval($reserv);

        $dbt->query("update T2_ARTICLES_STRORAGE set RESERV_AMOUNT=0 where ART_ID='$art_id' and STORAGE_ID='$storage_from_id';");

        $res=$dbt->query("select * from T2_ARTICLES_STRORAGE where ART_ID='$art_id' and STORAGE_ID='$storage_to_id';"); $n=$dbt->num_rows($res);

        if ($n==0){
            $dbt->query("insert into T2_ARTICLES_STRORAGE (ART_ID,AMOUNT,RESERV_AMOUNT,STORAGE_ID) values ('$art_id','$amount','$reserv_amount','$storage_to_id');");
        }
        else{
            $dbt->query("update T2_ARTICLES_STRORAGE set AMOUNT='$amount',RESERV_AMOUNT='$reserv_amount' where ART_ID='$art_id' and STORAGE_ID='$storage_to_id';");
        }
    }

    function cellsJmovingDefect($art_id,$storage_to_id,$storage_from_id,$cell_to_id,$cell_from_id,$reserv) { $dbt=DbSingleton::getTokoDb();
        $r1=$dbt->query("select * from T2_ARTICLES_STRORAGE_CELLS where ART_ID='$art_id' and STORAGE_ID='$storage_from_id' and STORAGE_CELLS_ID='$cell_from_id' limit 1;");
        //$amount1=floatval($dbt->result($r1,0,"AMOUNT"));
        $reserv_amount1=floatval($dbt->result($r1,0,"RESERV_AMOUNT"));
        $nerezerv=$reserv_amount1-floatval($reserv);

        $r2=$dbt->query("select * from T2_ARTICLES_STRORAGE_CELLS where ART_ID='$art_id' and STORAGE_ID='$storage_to_id' and STORAGE_CELLS_ID='$cell_to_id' limit 1;");
        $amount2=floatval($dbt->result($r2,0,"AMOUNT"));
        $reserv_amount2=floatval($dbt->result($r2,0,"RESERV_AMOUNT"));
        $amount=$amount2 + floatval($nerezerv);
        $reserv_amount=$reserv_amount2 + floatval($reserv);

        $dbt->query("update T2_ARTICLES_STRORAGE_CELLS set RESERV_AMOUNT=0 where ART_ID='$art_id' and STORAGE_ID='$storage_from_id' and STORAGE_CELLS_ID='$cell_from_id';");

        $res=$dbt->query("select * from T2_ARTICLES_STRORAGE_CELLS where ART_ID='$art_id' and STORAGE_ID='$storage_to_id' and STORAGE_CELLS_ID='$cell_to_id';"); $n=$dbt->num_rows($res);

        if ($n==0){
            $dbt->query("insert into T2_ARTICLES_STRORAGE_CELLS (ART_ID,AMOUNT,RESERV_AMOUNT,STORAGE_ID,STORAGE_CELLS_ID) values ('$art_id','$amount','$reserv_amount','$storage_to_id','$cell_to_id');");
        }

        $amount+=$reserv_amount;
        if ($n==0){
            $dbt->query("insert into T2_ARTICLES_STRORAGE_CELLS (ART_ID,AMOUNT,RESERV_AMOUNT,STORAGE_ID,STORAGE_CELLS_ID) values ('$art_id','$amount','0','$storage_to_id','$cell_to_id');");
        }
        else{
            $dbt->query("update T2_ARTICLES_STRORAGE_CELLS set AMOUNT='$amount',RESERV_AMOUNT='0' where ART_ID='$art_id' and STORAGE_ID='$storage_to_id' and STORAGE_CELLS_ID='$cell_to_id';");
        }
    }

}
