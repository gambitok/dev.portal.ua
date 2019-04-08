<?php

class storsel {

    protected $prefix_new = 'СКв';
    public $storsel_prefix ='СКв';

    function getMediaUserName($user_id){$db=DbSingleton::getDb();$name="";
        $r=$db->query("select name from media_users where id='$user_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$name=$db->result($r,0,"name");}
        return $name;
    }

    function get_doc_client_prefix($client_id){ $db=DbSingleton::getDb();$prefix_id=0;$doc_type_id=40;
        $r=$db->query("select id from A_CLIENTS_DOCUMENT_PREFIX where client_id='$client_id' and doc_type_id='$doc_type_id' and status='1' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$prefix_id=$db->result($r,0,"id");}
        return $prefix_id;
    }

    function get_df_doc_nom_new(){ $db=DbSingleton::getDb();
        $r=$db->query("select max(doc_nom) as doc_nom from J_MOVING where oper_status='30' and status='1' limit 0,1;");$doc_nom=0+$db->result($r,0,"doc_nom")+1;
        return $doc_nom;
    }

    function getSaleInvoiceName($dp_id,$select_id){$db=DbSingleton::getDb();$name="";
        $r=$db->query("select s.prefix,s.doc_nom from J_SALE_INVOICE_STORSEL sis left outer join J_SALE_INVOICE s on s.id=sis.invoice_id where sis.dp_id='$dp_id' and sis.select_id='$select_id' and sis.`status`=1 limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $name=$db->result($r,0,"prefix")."".$db->result($r,0,"doc_nom")." / ";
        }
        return $name;
    }

    function getSaleInvoiceStatusId($dp_id,$select_id){$db=DbSingleton::getDb();$status_invoice=0;
        $r=$db->query("select s.status_invoice from J_SALE_INVOICE_STORSEL sis left outer join J_SALE_INVOICE s on s.id=sis.invoice_id where sis.dp_id='$dp_id' and sis.select_id='$select_id' and sis.`status`=1 limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$status_invoice=$db->result($r,0,"status_invoice");}
        return $status_invoice;
    }

    function statusStorage($user_id,$storage_id) {$db=DbSingleton::getDb();$users= new users;
        $r=$db->query("select * from media_users_storage where user_id='$user_id' and storage_id='$storage_id';");
        $n=$db->num_rows($r);
        $super_user = $users->getSuperUser($user_id);
        if (($super_user)||($n>0)) $status=true; else $status=false;
        return $status;
    }

    function show_storsel_list($status = null){$db=DbSingleton::getDb();$gmanual=new gmanual;$jmoving=new jmoving;$dp=new dp;session_start();$ses_tpoint_id=$_SESSION["media_tpoint_id"];$media_user_id=$_SESSION["media_user_id"];
        $count=0;$parrent_doc_status=0;$where=" and sel.tpoint_id='$ses_tpoint_id'";$list="";
        $users= new users; $super_user = $users->getSuperUser($media_user_id);
        if (($media_user_id==1) || ($media_user_id==7) || ($super_user)){$where="";}
        $r=$db->query("select sel.*, s.name as storage_name, t.name as tpoint_name from J_SELECT sel
            left outer join T_POINT t on t.id=sel.tpoint_id
            left outer join STORAGE s on s.id=sel.storage_id
        where sel.status=1 $where order by sel.status_select asc, sel.data_create desc, sel.id desc  limit 0,500;");$n=$db->num_rows($r);

        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $parrent_doc_type_id=$db->result($r,$i-1,"parrent_doc_type_id");
            $parrent_doc_id=$db->result($r,$i-1,"parrent_doc_id");
            $data_create=$db->result($r,$i-1,"data_create");
            //$tpoint_id=$db->result($r,$i-1,"tpoint_id");
            $tpoint_name=$db->result($r,$i-1,"tpoint_name");
            $storage_id=$db->result($r,$i-1,"storage_id");
            $storage_name=$db->result($r,$i-1,"storage_name");
            $articles_amount=$db->result($r,$i-1,"articles_amount");
            $amount=$db->result($r,$i-1,"amount");
            $volume=$db->result($r,$i-1,"volume");
            $weight_netto=$db->result($r,$i-1,"weight_netto");
            $weight_brutto=$db->result($r,$i-1,"weight_brutto");
            //$user_name=$this->getMediaUserName($db->result($r,$i-1,"user_id"));
            $user_create=$db->result($r,$i-1,"user_create");
            $status_select_id=$db->result($r,$i-1,"status_select");
            $status_select=$gmanual->get_gmanual_caption($status_select_id);

            switch($status_select_id) {
                case 82:  $color="background: pink"; break;
                case 83:  $color="background: #ffff4c"; break;
                case 84:  $color="background: #ffca67"; break;
                case 85:  $color="background: lightgreen"; break;
                case 127: $color="background: lightblue"; break;
                default: $color=""; break;
            }
            $doc_parrent="";
            if ($parrent_doc_type_id==1){
                $doc_parrent=$jmoving->getJmovingName($parrent_doc_id);
                $parrent_doc_status=$jmoving->getJmovingStatusId($parrent_doc_id);
            }
            if ($parrent_doc_type_id==2){
                $doc_parrent=$this->getSaleInvoiceName($parrent_doc_id,$id)."".$dp->getDpName($parrent_doc_id);
                $parrent_doc_status=$this->getSaleInvoiceStatusId($parrent_doc_id,$id);
            }
            if ($parrent_doc_type_id!=1) {
                $client_id=$dp->getDpClient($parrent_doc_id);
                $client_name=$dp->getClientName($client_id);
            }
            else $client_name="";
            //$user_create=$dp->getDpClient($user_create);
            $user_create=$dp->getMediaUserName($user_create);

            if($status=="") {
                //показувати всі, окрім тих, де в переміщеннях статус - 'В дорозі' || $parrent_doc_status==0
                if ($status_select_id==127 || $status_select_id<85 || $parrent_doc_status==107 || ($status_select==85 && $parrent_doc_status!=48)){
                    if ($this->statusStorage($media_user_id,$storage_id)) {
                        $function="showStorselCard(\"$id\")";
                        $list.="<tr align='center' style='cursor:pointer; $color' onClick='$function'>
                            <td title='parrent_doc_status=$parrent_doc_status; status_select_id=$status_select_id'>$this->prefix_new - $id</td>
                            <td align='center'>$data_create</td>
                            <td>$doc_parrent</td>
                            <td>$client_name</td>
                            <td>$tpoint_name</td>
                            <td>$storage_name</td>
                            <td>$articles_amount</td>
                            <td>$amount</td>
                            <td>$volume</td>
                            <td>$weight_netto</td>
                            <td>$weight_brutto</td>
                            <td>$status_select</td>
                            <td>$user_create</td>
                        </tr>";
                        $count++;
                    }
                }
            } else {
                if ($this->statusStorage($media_user_id,$storage_id)) {
                    $function="showStorselCard(\"$id\")";
                    $list.="<tr align='center' style='cursor:pointer; $color' onClick='$function'>
                        <td title='parrent_doc_status=$parrent_doc_status; status_select_id=$status_select_id'>$this->prefix_new - $id</td>
                        <td align='center'>$data_create</td>
                        <td>$doc_parrent</td>
                        <td>$client_name</td>
                        <td>$tpoint_name</td>
                        <td>$storage_name</td>
                        <td>$articles_amount</td>
                        <td>$amount</td>
                        <td>$volume</td>
                        <td>$weight_netto</td>
                        <td>$weight_brutto</td>
                        <td>$status_select</td>
                        <td>$user_create</td>
                    </tr>";
                    $count++;
                }
            }
        }

        $count=0;
        $r=$db->query("select sel.*, s.name as storage_name, t.name as tpoint_name from J_SELECT sel
            left outer join T_POINT t on t.id=sel.tpoint_id
            left outer join STORAGE s on s.id=sel.storage_id
        where sel.status=1 order by sel.status_select asc, sel.data_create desc, sel.id desc  limit 0,500;");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $parrent_doc_type_id=$db->result($r,$i-1,"parrent_doc_type_id");
            $parrent_doc_id=$db->result($r,$i-1,"parrent_doc_id");
            $storage_id=$db->result($r,$i-1,"storage_id");
            $status_select_id=$db->result($r,$i-1,"status_select");
            $status_select=$gmanual->get_gmanual_caption($status_select_id);
            //$doc_parrent="";
            if ($parrent_doc_type_id==1){
                //$doc_parrent=$jmoving->getJmovingName($parrent_doc_id);
                $parrent_doc_status=$jmoving->getJmovingStatusId($parrent_doc_id);
            }
            if ($parrent_doc_type_id==2){
                //$doc_parrent=$this->getSaleInvoiceName($parrent_doc_id,$id)."".$dp->getDpName($parrent_doc_id);
                $parrent_doc_status=$this->getSaleInvoiceStatusId($parrent_doc_id,$id);
            }
            if($status=="") {
            if ($status_select_id==127 || $status_select_id<85 || $parrent_doc_status==0 || $parrent_doc_status==107 || ($status_select==85 && $parrent_doc_status!=48)){
                if ($this->statusStorage($media_user_id,$storage_id)) {
                    $count++;
                }
            }
            } else {
                if ($this->statusStorage($media_user_id,$storage_id)) {
                    $count++;
                }
            }
        }
        return array($list,$count);
    }

    function updateStorselStatus($select_id) {$db=DbSingleton::getDb();$answer=0;$err="Помилка збереження даних!";
        if ($select_id>0) {
            $db->query("update J_SELECT set status_select=128 where id='$select_id';");
            $answer=1; $err="";
        }
        return array($answer,$err);
    }

    function showStorselCard($select_id){$db=DbSingleton::getDb();$gmanual=new gmanual;$jmoving=new jmoving; $dp=new dp;
        session_start();$user_id=$_SESSION["media_user_id"];$user_name=$_SESSION["user_name"];$id=0;
        $form="";$form_htm=RD."/tpl/storsel_card.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}

        $r=$db->query("select * from J_SELECT where id='$select_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==0){$form_htm=RD."/tpl/access_deny.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);} }
        if ($n==1){
            $id=$db->result($r,0,"id");
            $parrent_doc_type_id=$db->result($r,0,"parrent_doc_type_id");
            $parrent_doc_id=$db->result($r,0,"parrent_doc_id");

            $doc_parrent="";
            if ($parrent_doc_type_id==1){$doc_parrent=$jmoving->getJmovingName($parrent_doc_id);}
            if ($parrent_doc_type_id==2){$doc_parrent=$dp->getDpName($parrent_doc_id);}

            $data_create=$db->result($r,0,"data_create");
            $data_start=$db->result($r,0,"data_start");
            $data_collect=$db->result($r,0,"data_collect");

            $user_use=$db->result($r,0,"user_use");
            $user_create=$db->result($r,0,"user_create");
            $user_start=$db->result($r,0,"user_start");
            $user_collect=$db->result($r,0,"user_collect");

            if ($user_id!=$user_use && $user_use>0){
                $form_htm=RD."/tpl/storsel_use_deny.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
                $form=str_replace("{user_name}",$this->getMediaUserName($user_use),$form);
                $admin_unlock="";
                if ($user_id==1 || $user_id==2){$admin_unlock="<button class='btn btn-sm btn-warning' onClick='unlockStorselCard(\"$id\");'><i class='fa fa-unlock'></i> Розблокувати</button>";}
                $form=str_replace("{admin_unlock}",$admin_unlock,$form);
            }
            if ($user_id==$user_use || $user_use==0){
                $weight_netto=$db->result($r,0,"weight_netto");
                $weight_brutto=$db->result($r,0,"weight_brutto");
                $volume=$db->result($r,0,"volume");
                $status_select=$db->result($r,0,"status_select");
                $status_select_name=$gmanual->get_gmanual_caption($status_select);

                //change sthing
                if ($status_select>82){
                    $form=str_replace("{disabled83}"," disabled",$form);
                } else $form=str_replace("{disabled83}"," ",$form);

                if ($status_select==82 || $status_select==85 || $status_select==128 || $status_select==127){
                    $form=str_replace("{disabled85}"," disabled",$form);
                } else $form=str_replace("{disabled85}"," ",$form);

                if ($parrent_doc_type_id!=1) {
                    $client_id=$dp->getDpClient($parrent_doc_id);
                    $client_name=$dp->getClientName($client_id);
                }
                else $client_name="";

                if ($status_select==127){ $form=str_replace("{disabled_128}","",$form); } else { $form=str_replace("{disabled_128}","style='display:none;'",$form); }
                $form=str_replace("{oper_disabled}","",$form);
                $form=str_replace("{select_id}",$select_id,$form);
                $form=str_replace("{doc_parrent}",$doc_parrent,$form);
                $form=str_replace("{status_select_name}",$status_select_name,$form);
                $form=str_replace("{storsel_client}",$client_name,$form);
                $form=str_replace("{data_create}",$data_create,$form);
                $form=str_replace("{data_start}",$data_start,$form);
                $form=str_replace("{data_collect}",$data_collect,$form);
                $form=str_replace("{dp_note}",$dp->getDpNote($parrent_doc_id),$form);
                $form=str_replace("{dp_id}",$parrent_doc_id,$form);
                $user_create=$dp->getMediaUserName($user_create);
                $user_start=$dp->getMediaUserName($user_start);
                $user_collect=$dp->getMediaUserName($user_collect);
                $form=str_replace("{user_create}",$user_create,$form);
                $form=str_replace("{user_start}",$user_start,$form);
                $form=str_replace("{user_collect}",$user_collect,$form);
                $form=str_replace("{weight_brutto}",$weight_brutto,$form);
                $form=str_replace("{weight_netto}",$weight_netto,$form);
                $form=str_replace("{volume}",$volume,$form);

                list($storselChildsList,$kol_art_str)=$this->showStorselStrList($select_id);
                $form=str_replace("{storselChildsList}",$storselChildsList,$form);

                $storage_to_disabled=""; if ($status_select!=44){$storage_to_disabled=" disabled";}
                if ($kol_art_str>0){$storage_to_disabled=" disabled"; }
                $form=str_replace("{storage_to_disabled}",$storage_to_disabled,$form);
                $form=str_replace("{my_user_id}",$user_id,$form);
                $form=str_replace("{my_user_name}",$user_name,$form);

                list(,$label_comments)=$this->labelCommentsCount($select_id);
                $form=str_replace("{labelCommentsCount}",$label_comments,$form);
                $disabled48="disabled"; if ($status_select==47){$disabled48="";}
                $form=str_replace("{disabled48}",$disabled48,$form);
                $disabled49="disabled hidden"; if ($status_select!=57){$disabled49="";}
                $form=str_replace("{disabled49}",$disabled49,$form);

                $this->setStorselCardUserAccess($select_id,$user_id);
            }
        }
        return array($form,$this->prefix_new."-".$id);
    }

    function unlockStorselCard($select_id){session_start();$user_id=$_SESSION["media_user_id"];$answer=0;
        if ($user_id==1 || $user_id==2){$db=DbSingleton::getDb();
            $db->query("update J_SELECT set user_use='0' where id='$select_id';");
            $answer=1;
        }
        return $answer;
    }

    function closeStorselCard($select_id){session_start();$user_id=$_SESSION["media_user_id"];
        $this->unsetStorselCardUserAccess($select_id,$user_id); $answer=1;
        return $answer;
    }

    function setStorselCardUserAccess($select_id,$user_id){$db=DbSingleton::getDb();
        if($select_id>0 && $user_id>0){
            $db->query("update J_SELECT set user_use='$user_id' where id='$select_id';");
        }
        return;
    }

    function unsetStorselCardUserAccess($select_id,$user_id){$db=DbSingleton::getDb();
        if($select_id>0 && $user_id>0){
            $db->query("update J_SELECT set user_use='0' where id='$select_id';");
        }
        return;
    }

    function showStorselStrList($select_id){$db=DbSingleton::getDb();$cat=new catalogue;$list="";
        $r=$db->query("select * from J_SELECT_STR where select_id='$select_id' order by id asc;");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $art_id=$db->result($r,$i-1,"art_id");
            $article_nr_displ=$db->result($r,$i-1,"article_nr_displ");
            $brand_id=$db->result($r,$i-1,"brand_id");$brand_name=$cat->getBrandName($brand_id);
            $storage_id_from=$db->result($r,$i-1,"storage_id_from"); $storage_name_from=$this->getStorageName($storage_id_from);
            $cell_id_from=$db->result($r,$i-1,"cell_id_from"); if ($cell_id_from>0){$storage_name_from.=" ".$this->getStorageCellName($cell_id_from);}
            $amount=$db->result($r,$i-1,"amount");
            $amount_collect=$db->result($r,$i-1,"amount_collect");
            $amount_bug=$db->result($r,$i-1,"amount_bug");
            $select_bug_list=$this->getStorselBugList($select_id,$art_id,$id);

            $list.="<tr align='right'>
                <td align='left'>$i</td>
                <td align='left'>$article_nr_displ</td>
                <td align='center'>$brand_name</td>
                <td>$storage_name_from</td>
                <td>$amount</td>
                <td>$amount_collect</td>
                <td>$amount_bug</td>
                <td>$select_bug_list</td>
            </tr>";
        }
        return array($list,$n);
    }

    function getArticleName($art_id){$db=DbSingleton::getTokoDb();$name="";
        $r=$db->query("select * from T2_NAMES where ART_ID='$art_id' and `LANG_ID`='16' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
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

    function getArticleRestStorage($art_id,$storage_id){$db=DbSingleton::getTokoDb();$stock=0;$reserv=0;if ($storage_id==""){$storage_id=0;}
        $r=$db->query("select SUM(`AMOUNT`) as stock, SUM(`RESERV_AMOUNT`) as reserv from T2_ARTICLES_STRORAGE where ART_ID='$art_id' and `STORAGE_ID`='$storage_id';");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $stock+=$db->result($r,$i-1,"stock");
            $reserv+=$db->result($r,$i-1,"reserv");
        }
        return array($stock,$reserv);
    }

    function getArticleRestStorageCell($art_id,$storage_id,$cell_id){$db=DbSingleton::getTokoDb();$stock=0;$reserv=0;if ($storage_id==""){$storage_id=0;}if ($cell_id==""){$cell_id=0;}
        $r=$db->query("select `AMOUNT` as stock, `RESERV_AMOUNT` as reserv from T2_ARTICLES_STRORAGE_CELLS where ART_ID='$art_id' and `STORAGE_ID`='$storage_id' and `STORAGE_CELLS_ID`='$cell_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $stock=$db->result($r,0,"stock");
            $reserv=$db->result($r,0,"reserv");
        }
        return array($stock,$reserv);
    }

    function getStorageName($sel_id){$db=DbSingleton::getTokoDb();$name="";
        $r=$db->query("select name from `STORAGE` where status='1' and id='$sel_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$name=$db->result($r,0,"name");}
        return $name;
    }

    function getStorageCellName($sel_id){$db=DbSingleton::getTokoDb();$name="";
        $r=$db->query("select cell_value from `STORAGE_CELLS` where status='1' and id='$sel_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$name=$db->result($r,0,"cell_value");}
        return $name;
    }

    function loadStorselCommets($select_id){$db=DbSingleton::getDb();
        $form="";$form_htm=RD."/tpl/storsel_comment_block.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select cc.*, u.name from J_SELECT_COMMENTS cc 
            left outer join media_users u on u.id=cc.USER_ID 
        where cc.select_id='$select_id' order by id desc;");$n=$db->num_rows($r);$list="";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $user_id=$db->result($r,$i-1,"user_id");
            $user_name=$db->result($r,$i-1,"name");
            $data=$db->result($r,$i-1,"data");
            $comment=$db->result($r,$i-1,"comment");
            $block=$form;
            $block=str_replace("{select_id}",$select_id,$block);
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

    function saveStorselComment($select_id,$comment){$db=DbSingleton::getDb();$slave=new slave;session_start();$user_id=$_SESSION["media_user_id"];$answer=0;$err="Помилка збереження даних!";
        $select_id=$slave->qq($select_id);$comment=$slave->qq($comment);
        if ($select_id>0 && $comment!=""){
            $db->query("insert into J_SELECT_COMMENTS (`select_id`,`user_id`,`comment`) values ('$select_id','$user_id','$comment');");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function dropStorselComment($select_id,$comment_id){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка видалення запису!";
        $select_id=$slave->qq($select_id);$comment_id=$slave->qq($comment_id);
        if ($select_id>0 && $comment_id>0){
            $r=$db->query("select * from J_SELECT_COMMENTS where select_id='$select_id' and id='$comment_id' limit 0,1;");$n=$db->num_rows($r);
            if ($n==1){
                $db->query("delete from J_SELECT_COMMENTS where select_id='$select_id' and id='$comment_id';");
                $answer=1;$err="";
            }
        }
        return array($answer,$err);
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

    function labelCommentsCount($select_id){$db=DbSingleton::getDb();$label="";
        $r=$db->query("select count(id) as kol from J_SELECT_COMMENTS where select_id='$select_id';");$kol=0+$db->result($r,0,"kol");
        if ($kol>0){$label="<span class='label label-tab label-info'>$kol</span>";}
        return array($kol,$label);
    }

    function startJmovingStorageSelect($jmoving_id){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="";
        $jmoving_id=$slave->qq($jmoving_id);
        $r=$db->query("select oper_status, status_select, storage_id_to from J_MOVING where id='$jmoving_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $oper_status=$db->result($r,0,"oper_status");
            $status_select=$db->result($r,0,"status_select");
            $storage_id_to=$db->result($r,0,"storage_id_to");
            if ($storage_id_to==0){$answer=0;$err="Не зазначено склад переміщення.";}
            if ($status_select>47 || $oper_status>30){$answer=0;$err="Переміщення заблоковано. Зміни вносити заборонено.";}
            if ($oper_status==30 && $status_select>=44 && $status_select<=47 && $storage_id_to>0) {
                /* make calculation jmoving */
                $r1=$db->query("select storage_id_from from J_MOVING_STR where jmoving_id='$jmoving_id' and status_select='44' group by storage_id_from,cell_id_from order by storage_id_from asc;");$n1=$db->num_rows($r1);
                if ($n1==0){ $answer=0;$err="Відсутній товар для створення відбору";}
                if($n1>0){
                    for ($i=1;$i<=$n1;$i++){
                        $storage_id_from=$db->result($r1,$i-1,"storage_id_from");
                        list($tpoint_id,$loc_type_id)=$this->getTpointDataByStorage($storage_id_from);
                        $sum_art_amount=0;$sum_amount=0;$sum_volume=0;$sum_weight_netto=0;$sum_weight_brutto=0;
                        $rm=$db->query("select max(id) as mid from J_MOVING_SELECT_TEMP;");$select_id=0+$db->result($rm,0,"mid")+1;
                        $db->query("insert into J_MOVING_SELECT_TEMP (`id`,`jmoving_id`,`tpoint_id`,`storage_id`,`loc_type_id`,`status_select`) values ('$select_id','$jmoving_id','$tpoint_id','$storage_id_from','$loc_type_id','44');");

                        $ra=$db->query("select * from J_MOVING_STR where jmoving_id='$jmoving_id' and storage_id_from='$storage_id_from' and status_select='44';");$na=$db->num_rows($ra);
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
                        $db->query("update J_MOVING_SELECT_TEMP set `articles_amount`='$sum_art_amount',`amount`='$sum_amount',`volume`='$sum_volume',`weight_netto`='$sum_weight_netto',`weight_brutto`='$sum_weight_brutto' where id='$select_id' and '$jmoving_id'='$jmoving_id';");
                    }
                    $answer=1;$err="";
                }
            }
        }
        return array($answer,$err);
    }

    function makesJmovingStorageSelect($jmoving_id){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;$answer=0;$err="";
        $jmoving_id=$slave->qq($jmoving_id);$storage_id_from=0;
        $r=$db->query("select oper_status, status_select, storage_id_to from J_MOVING where id='$jmoving_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $oper_status=$db->result($r,0,"oper_status");
            $status_select=$db->result($r,0,"status_select");
            $storage_id_to=$db->result($r,0,"storage_id_to");
            if ($storage_id_to==0){$answer=0;$err="Не зазначено склад переміщення.";}
            if ($status_select>47 || $oper_status>30){$answer=0;$err="Переміщення заблоковано. Зміни вносити заборонено.";}
            if ($oper_status==30 && $status_select>=44 && $status_select<=47 && $storage_id_to>0) {
                $db->query("update J_MOVING set status_select='45' where id='$jmoving_id';");

                $rm=$db->query("select max(id) as mid from J_MOVING_SELECT;");$select_id=0+$db->result($rm,0,"mid");

                $rm=$db->query("select * from J_MOVING_SELECT_TEMP where jmoving_id='$jmoving_id' and status_select='44';");$nm=$db->num_rows($rm);
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
                    $db->query("insert into J_MOVING_SELECT (`id`,`jmoving_id`,`data`,`tpoint_id`,`storage_id`,`loc_type_id`,`articles_amount`,`amount`,`volume`,`weight_netto`,`weight_brutto`,`status_select`) 
                    values ('$select_id','$jmoving_id','$cur_date','$tpoint_id','$storage_id','$loc_type_id','$articles_amount','$amount','$volume','$weight_netto','$weight_brutto','45');");
                    $db->query("delete from J_MOVING_SELECT_TEMP  where jmoving_id='$jmoving_id' and id='$select_id_t';");

                    $this->addJournalRecord($select_id,$status_select);

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
                                    $db->query("insert into J_MOVING_SELECT_STR (`jmoving_id`,`select_id`,`art_id`,`article_nr_displ`,`brand_id`,`amount`,`storage_id_from`,`cell_id_from`) 
                                    values ('$jmoving_id','$select_id','$art_id','$article_nr_displ','$brand_id','$amount','$storage_id_from','$storage_cells_id_sc');");
                                    $dbt->query("update `T2_ARTICLES_STRORAGE_CELLS` set `AMOUNT`='$amount_sc', `RESERV_AMOUNT`='$reserv_amount_sc' where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_from' and `STORAGE_CELLS_ID`='$storage_cells_id_sc' limit 1;");
                                }
                                if ($amount_sc<$amount && $amount_sc>0 && $er==0){
                                    $amount-=$amount_sc;
                                    $reserv_amount_sc+=$amount_sc;
                                    $db->query("insert into J_MOVING_SELECT_STR (`jmoving_id`,`select_id`,`art_id`,`article_nr_displ`,`brand_id`,`amount`,`storage_id_from`,`cell_id_from`) 
                                    values ('$jmoving_id','$select_id','$art_id','$article_nr_displ','$brand_id','$amount_sc','$storage_id_from','$storage_cells_id_sc');");
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
                                    $db->query("insert into J_MOVING_SELECT_STR (`jmoving_id`,`select_id`,`art_id`,`article_nr_displ`,`brand_id`,`amount`,`storage_id_from`) 
                                    values ('$jmoving_id','$select_id','$art_id','$article_nr_displ','$brand_id','$amount','$storage_id_from');");
                                    //$dbt->query("update `T2_ARTICLES_STRORAGE` set `AMOUNT`='$amount_sc', `RESERV_AMOUNT`='$reserv_amount_sc' where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_from' limit 1;");
                                }
                            }
                        }
                        $db->query("delete from J_MOVING_SELECT_STR_TEMP where jmoving_id='$jmoving_id' and id='$id2';");
                    }
                    $db->query("update J_MOVING_STR set status_select='45', select_id='$select_id' where jmoving_id='$jmoving_id' and storage_id_from='$storage_id_from' and status_select='44';");
                }
                $answer=1;$err="";
            }
        }
        return array($answer,$err);
    }

    function makesJmovingStorageSelectLocal($jmoving_id){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="";
        $jmoving_id=$slave->qq($jmoving_id);$storage_id_from=$cell_id_from=0;
        $r=$db->query("select oper_status, status_select, storage_id_to from J_MOVING where id='$jmoving_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $oper_status=$db->result($r,0,"oper_status");
            $status_select=$db->result($r,0,"status_select");
            $storage_id_to=$db->result($r,0,"storage_id_to");
            if ($storage_id_to==0){$answer=0;$err="Не зазначено склад переміщення.";}
            if ($status_select>47 || $oper_status>30){$answer=0;$err="Переміщення заблоковано. Зміни вносити заборонено.";}
            if ($oper_status==30 && $status_select>=44 && $status_select<=47 && $storage_id_to>0) {
                $db->query("update J_MOVING set status_select='45' where id='$jmoving_id';");

                $rm=$db->query("select max(id) as mid from J_MOVING_SELECT;");$select_id=0+$db->result($rm,0,"mid");

                $rm=$db->query("select * from J_MOVING_LOCAL_SELECT_TEMP where jmoving_id='$jmoving_id' and status_select='44';");$nm=$db->num_rows($rm);
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
                    $db->query("insert into J_MOVING_SELECT (`id`,`jmoving_id`,`data`,`tpoint_id`,`storage_id`,`loc_type_id`,`articles_amount`,`amount`,`volume`,`weight_netto`,`weight_brutto`,`status_select`) 
                    values ('$select_id','$jmoving_id','$cur_date','$tpoint_id','$storage_id','$loc_type_id','$articles_amount','$amount','$volume','$weight_netto','$weight_brutto','45');");
                    $db->query("delete from J_MOVING_LOCAL_SELECT_TEMP  where jmoving_id='$jmoving_id' and id='$select_id_t';");

                    $this->addJournalRecord($select_id,$status_select);

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

                        $db->query("insert into J_MOVING_SELECT_STR (`jmoving_id`,`select_id`,`art_id`,`article_nr_displ`,`brand_id`,`amount`,`storage_id_from`,`cell_id_from`,`storage_id_to`,`cell_id_to`) values ('$jmoving_id','$select_id','$art_id','$article_nr_displ','$brand_id','$amount','$storage_id_from','$cell_id_from','$storage_id_from','$cell_id_to');");

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
                    $db->query("update J_MOVING_STR set status_select='45', select_id='$select_id' where jmoving_id='$jmoving_id' and `storage_id_from`='$storage_id_from' and `cell_id_from`='$cell_id_from' and status_select='44';");
                }
                $answer=1;$err="";
            }
        }
        return array($answer,$err);
    }

    function clearJmovingStorageSelect($jmoving_id){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="";
        $jmoving_id=$slave->qq($jmoving_id);
        $r=$db->query("select oper_status, status_select, storage_id_to from J_MOVING where id='$jmoving_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $oper_status=$db->result($r,0,"oper_status");
            $status_select=$db->result($r,0,"status_select");
            $storage_id_to=$db->result($r,0,"storage_id_to");
            if ($storage_id_to==0){$answer=0;$err="Не зазначено склад переміщення.";}
            if ($status_select>47 || $oper_status>30){$answer=0;$err="Переміщення заблоковано. Зміни вносити заборонено.";}
            if ($oper_status==30 && $status_select>=44 && $status_select<=47 && $storage_id_to>0) {
                $db->query("delete from J_MOVING_SELECT_TEMP where jmoving_id='$jmoving_id' and status_select='44';");
                $db->query("delete from J_MOVING_SELECT_STR_TEMP  where jmoving_id='$jmoving_id';");
                $answer=1;$err="";
            }
        }
        return array($answer,$err);
    }

    function clearJmovingStorageSelectLocal($jmoving_id){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="";
        $jmoving_id=$slave->qq($jmoving_id);
        $r=$db->query("select oper_status, status_select, storage_id_to from J_MOVING where id='$jmoving_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $oper_status=$db->result($r,0,"oper_status");
            $status_select=$db->result($r,0,"status_select");
            $storage_id_to=$db->result($r,0,"storage_id_to");
            if ($storage_id_to==0){$answer=0;$err="Не зазначено склад переміщення.";}
            if ($status_select>47 || $oper_status>30){$answer=0;$err="Переміщення заблоковано. Зміни вносити заборонено.";}
            if ($oper_status==30 && $status_select>=44 && $status_select<=47 && $storage_id_to>0) {
                $db->query("delete from J_MOVING_LOCAL_SELECT_TEMP where jmoving_id='$jmoving_id' and status_select='44';");
                $db->query("delete from J_MOVING_LOCAL_SELECT_STR_TEMP  where jmoving_id='$jmoving_id';");
                $answer=1;$err="";
            }
        }
        return array($answer,$err);
    }

    function loadJmovingStorageSelect($jmoving_id,$jmoving_status){$db=DbSingleton::getDb();
        $form="";$form_htm=RD."/tpl/jmoving_storage_select_list.htm";if ($jmoving_status==45){$form_htm=RD."/tpl/jmoving_storage_select_list_finish.htm";}
        if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select status_select from J_MOVING j where j.id='$jmoving_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==0){$form_htm=RD."/tpl/access_deny.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);} }
        if ($n==1){
            list($list,$kol_rows)=$this->showJmovingSkladStorageSelectList($jmoving_id,$jmoving_status);
            $form=str_replace("{SkladStorageSelectList}",$list,$form);
            $form=str_replace("{kol_rows}",$kol_rows,$form);
            $form=str_replace("{jmoving_id}",$jmoving_id,$form);
        }
        return $form;
    }

    function showJmovingSkladStorageSelectList($jmoving_id,$jmoving_status){$db=DbSingleton::getDb();$list="";
        $tmp="";$where_status="and ms.status_select='$jmoving_status'"; if ($jmoving_status==44){$tmp="_TEMP";} if ($jmoving_status>44){ $where_status="and ms.status_select in (45,46,47,48)"; }
        $r=$db->query("select ms.*, p.name as tpoint_name, s.name as storage_name, ml.mcaption as loc_type_name, mt.mcaption as status_select_name 
        from J_MOVING_SELECT$tmp ms 
            left outer join T_POINT p on p.id=ms.tpoint_id
            left outer join STORAGE s on s.id=ms.storage_id
            left outer join manual ml on ml.id=ms.loc_type_id
            left outer join manual mt on mt.id=ms.status_select
        where ms.jmoving_id='$jmoving_id' $where_status and ms.status='1' order by ms.id asc;");$n=$db->num_rows($r);
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
            $status_select=$db->result($r,$i-1,"status_select");
            $status_select_name=$db->result($r,$i-1,"status_select_name");

            $list.="<tr id='strStsRow_$i'><td>$i</td>";
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
                $list.="<td align='center'><button class='btn btn-xs btn-primary' onClick='viewJmovingStorageSelect(\"$jmoving_id\",\"$id\",$status_select);'><i class='fa fa-eye'></i></button></td>";
                if ($jmoving_status==44){
                    $list.="<td align='center'><button class='btn btn-xs btn-danger' onClick='dropJmovingStorageSelect(\"$jmoving_id\",\"$id\");'><i class='fa fa-trash'></i></button></td>";
                }
            $list.="<td align='center'>$status_select_name</td></tr>";
        }
        return array($list,$n);
    }

    function loadJmovingStorageSelectLocal($jmoving_id,$jmoving_status){$db=DbSingleton::getDb();
        $form="";$form_htm=RD."/tpl/jmoving_local_storage_select_list.htm";if ($jmoving_status==45){$form_htm=RD."/tpl/jmoving_local_storage_select_list_finish.htm";}
        if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select status_select from J_MOVING j where j.id='$jmoving_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==0){$form_htm=RD."/tpl/access_deny.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);} }
        if ($n==1){
            list($list,$kol_rows)=$this->showJmovingSkladStorageSelectListLocal($jmoving_id,$jmoving_status);
            $form=str_replace("{SkladStorageSelectList}",$list,$form);
            $form=str_replace("{kol_rows}",$kol_rows,$form);
            $form=str_replace("{jmoving_id}",$jmoving_id,$form);
        }
        return $form;
    }

    function showJmovingSkladStorageSelectListLocal($jmoving_id,$jmoving_status){$db=DbSingleton::getDb();$list="";
        $tmp="J_MOVING_SELECT";$where_status="and ms.status_select='$jmoving_status'"; if ($jmoving_status==44){$tmp="J_MOVING_LOCAL_SELECT_TEMP";} if ($jmoving_status>44){ $where_status="and ms.status_select in (45,46,47,48)"; }
        $query="select ms.*, p.name as tpoint_name, s.name as storage_name, mt.mcaption as status_select_name from $tmp ms 
            left outer join T_POINT p on p.id=ms.tpoint_id
            left outer join STORAGE s on s.id=ms.storage_id
            left outer join manual mt on mt.id=ms.status_select
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
            $status_select=$db->result($r,$i-1,"status_select");
            $status_select_name=$db->result($r,$i-1,"status_select_name");

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
                $list.="<td align='center'><button class='btn btn-xs btn-primary' onClick='viewJmovingStorageSelectLocal(\"$jmoving_id\",\"$id\",$status_select);'><i class='fa fa-eye'></i></button></td>";
                if ($jmoving_status==44){
                    $list.="<td align='center'><button class='btn btn-xs btn-danger' onClick='dropJmovingStorageSelectLocal(\"$jmoving_id\",\"$id\");'><i class='fa fa-trash'></i></button></td>";
                }
            $list.="<td align='center'>$status_select_name</td></tr>";
        }
        return array($list,$n);
    }

    function getTpointDataByStorage($storage_id){$db=DbSingleton::getDb(); $tpoint_id=0;$loc_type_id=0;
        $r=$db->query("select `tpoint_id`,`local` from T_POINT_STORAGE where storage_id='$storage_id' order by id asc limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){	$tpoint_id=$db->result($r,0,"tpoint_id"); $loc_type_id=$db->result($r,0,"local");}
        return array($tpoint_id,$loc_type_id);
    }

    function viewJmovingStorageSelect($jmoving_id,$select_id,$jmoving_status){$db=DbSingleton::getDb();$jmoving=new jmoving;$cat=new catalogue;$list="";
        $form="";$form_htm=RD."/tpl/jmoving_storage_select_view.htm";
        $tmp="";if ($jmoving_status==44){$tmp="_TEMP";}
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
                $select_bug_list=$this->getStorselBugList($select_id,$art_id,$id);
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
        list(,,,,,,,,,,,$select_datatime)=$jmoving->getJmovingSkladStorageSelectInfo($jmoving_id,$select_id);
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
        $data_records=$jmoving->getJmovingSelectJournalRecords($jmoving_id,$select_id);
        $form=str_replace("{data_46}",$data_records[46],$form);
        $form=str_replace("{data_52}",$data_records[52],$form);
        $form=str_replace("{data_47}",$data_records[47],$form);
        $form=str_replace("{data_48}",$data_records[48],$form);
        return array($form,"Структура складського відбору № СкВ-$select_id");
    }

    function viewJmovingStorageSelectLocal($jmoving_id,$select_id,$jmoving_status){$db=DbSingleton::getDb();$jmoving=new jmoving;$cat=new catalogue;$list="";
        $form="";$form_htm=RD."/tpl/jmoving_local_storage_select_view.htm";
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
                $select_bug_list=$this->getStorselBugList($jmoving_id,$select_id,$art_id);
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
        list(,,,,,,,,,,,$select_datatime)=$jmoving->getJmovingSkladStorageSelectInfoLocal($jmoving_id,$select_id);
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
        $data_records=$jmoving->getJmovingSelectJournalRecords($jmoving_id,$select_id);
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

    function collectStorsel($select_id){$db=DbSingleton::getDb();$slave=new slave;session_start();$user_id=$_SESSION["media_user_id"];$answer=0;$err="Помилка обробки запису!"; $date=date("Y-m-d H:i:s");
        $select_id=$slave->qq($select_id);
        if ($select_id>0){
            $r=$db->query("select status_select, parrent_doc_id from J_SELECT where id='$select_id' limit 0,1;");$n=$db->num_rows($r);
            if ($n==1){
                $status_select=$db->result($r,0,"status_select");
                $parrent_doc_id=$db->result($r,0,"parrent_doc_id");
                if ($status_select==82){
                    $db->query("update J_SELECT set status_select='83', user_start='$user_id', data_start='$date' where id='$select_id' limit 1;");
                    //Статус переміщення 'Збирається'
                    $db->query("update J_MOVING set status_jmoving='46' where id='$parrent_doc_id' limit 1;");
                    $this->addJournalRecord($select_id,83);
                    $answer=1;$err="";
                }
            }
        }
        return array($answer,$err);
    }

    function getTpointAddress($tpoint_id){$db=DbSingleton::getDb();$address="";
        $r=$db->query("select full_name, address from T_POINT where id='$tpoint_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){ $address=$db->result($r,0,"full_name")." ".$db->result($r,0,"address"); }
        return $address;
    }

    function getStorselInfo($select_id){$db=DbSingleton::getDb();
        $r=$db->query("select ms.*, p.name as tpoint_name, s.name as storage_name, mt.mcaption as status_select_name 
        from J_SELECT ms 
            left outer join T_POINT p on p.id=ms.tpoint_id
            left outer join STORAGE s on s.id=ms.storage_id
            left outer join manual mt on mt.id=ms.status_select
        where ms.id='$select_id' and ms.status='1' limit 0,1;");
        $tpoint_id=$db->result($r,0,"tpoint_id");
        $tpoint_name=$db->result($r,0,"tpoint_name");
        $data_create=$db->result($r,0,"data_create");
        $data_start=$db->result($r,0,"data_start");
        $data_collect=$db->result($r,0,"data_collect");
        $storage_id=$db->result($r,0,"storage_id");
        $storage_name=$db->result($r,0,"storage_name");
        $articles_amount=$db->result($r,0,"articles_amount");
        $amount=$db->result($r,0,"amount");
        $volume=$db->result($r,0,"volume");
        $weight_netto=$db->result($r,0,"weight_netto");
        $weight_brutto=$db->result($r,0,"weight_brutto");
        $parrent_doc_type_id=$db->result($r,0,"parrent_doc_type_id");
        $parrent_doc_id=$db->result($r,0,"parrent_doc_id");
        return array("СКв-$select_id/$storage_name",$data_create,$data_start,$data_collect,$storage_id,$storage_name,$articles_amount,$amount,$volume,$weight_netto,$weight_brutto,$tpoint_id,$tpoint_name,$parrent_doc_type_id,$parrent_doc_id);
    }

    function getStorageOrder($storage_id) { $db=DbSingleton::getTokoDb();$order_cap="";
        $r=$db->query("select order_by from STORAGE where id='$storage_id'");
        $order_by=$db->result($r,0,"order_by");
        if ($order_by==0) $order_cap="order by article_nr_displ asc";
        if ($order_by==1) $order_cap="order by cell_id_from desc, article_nr_displ asc";
        return $order_cap;
    }

    function printStorselView($select_id){$db=DbSingleton::getDb();$cat=new catalogue;$dp=new dp;$jmoving=new jmoving;$slave=new slave;session_start();$user_name=$_SESSION["user_name"];
        $dp_name=$jmoving_name=$client_name="";
        $form="";$form_htm=RD."/tpl/storsel_select_print.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);} $list="";
        $rstr=$db->query("select storage_id from J_SELECT where id='$select_id';");
        $storage_id=$db->result($rstr,0,"storage_id");
        $order_by=$this->getStorageOrder($storage_id);

        $r=$db->query("select * from J_SELECT_STR where select_id='$select_id' $order_by;");$n=$db->num_rows($r);
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

        list($select_nom,$data_create,,,,$storage_name,$articles_amount,$amount,$volume,$weight_netto,$weight_brutto,$tpoint_id,$tpoint_name,$parrent_doc_type_id,$parrent_doc_id)=$this->getStorselInfo($select_id);
        if ($tpoint_name==""){$tpoint_name="-------------";}

        if ($parrent_doc_type_id==1){ // Jmoving
            $jmoving_name=$jmoving->getJmovingName($parrent_doc_id);
            list(,,,$storage_id_to,,,,)=$jmoving->getJmovingInfo($parrent_doc_id);
            list($tpoint_id,)=$jmoving->getTpointDataByStorage($storage_id_to);
            $tpoint_name=$this->getTpointName($tpoint_id);
        }
        if ($parrent_doc_type_id==2){ // DP
            $dp_name=$dp->getDpName($parrent_doc_id);
            $client_name=$dp->getDpClientName($parrent_doc_id);
            $tpoint_name="---";
        }
        $tpoint_address=$this->getTpointAddress($tpoint_id);
        $form=str_replace("{select_nom}",$select_nom,$form);
        $form=str_replace("{data_create}",$data_create,$form);
        $form=str_replace("{storage_name}",$storage_name,$form);
        $form=str_replace("{tpoint_name}",$tpoint_name,$form);
        $form=str_replace("{dp_name}",$dp_name,$form);
        $form=str_replace("{jmoving_name}",$jmoving_name,$form);
        $form=str_replace("{articles_amount}",$articles_amount,$form);
        $form=str_replace("{amount}",$amount,$form);
        $form=str_replace("{volume}",$volume,$form);
        $form=str_replace("{weight_netto}",$weight_netto,$form);
        $form=str_replace("{weight_brutto}",$weight_brutto,$form);
        $form=str_replace("{user_name}",$user_name,$form);
        $form=str_replace("{curtime}",date("Y-m-d H:i:s"),$form);
        $form=str_replace("{client_name}",$client_name,$form);
        $form=str_replace("{tpoint_address}",$tpoint_address,$form);$pData="";
        $form=str_replace("{pData}",$slave->data_word($pData),$form);
        $form=str_replace("{qrImage}","<img src='/phpqrcode/qrimage2.php?url=http://portal.myparts.pro/Storsel/printStS1/$select_id/".time()."'>",$form);
        $form=str_replace("{dp_note}",$dp->getDpNote($parrent_doc_id),$form);

        $this->addJournalRecord($select_id,52);
        //"Структура складського відбору"
        $mp=new media_print;
        $mp->print_document($form,array(210,280));
        return $form;
    }

    function printStorselView2($select_id){$db=DbSingleton::getDb();$cat=new catalogue;$dp=new dp;$slave=new slave;session_start();$user_name=$_SESSION["user_name"];
        $client_name=$jmoving_name=$dp_name=$list="";$mas=[];
        $form="";$form_htm=RD."/tpl/storsel_select_print.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select j.*, cll.cell_value from J_SELECT_STR j
            left outer join STORAGE_CELLS cll on cll.id=j.cell_id_from
        where j.select_id='$select_id' and cll.status=1 order by cll.cell_value desc, j.article_nr_displ asc;"); $n=$db->num_rows($r);

        for ($i=1;$i<=$n;$i++){
            $art_id=$db->result($r,$i-1,"art_id");
            $article_nr_displ=$db->result($r,$i-1,"article_nr_displ");
            $brand_id=$db->result($r,$i-1,"brand_id");
            $amount=$db->result($r,$i-1,"amount");
            $storage_id_from=$db->result($r,$i-1,"storage_id_from");
            $storage_name_from=$this->getStorageName($storage_id_from);
            $cell_id_from=$db->result($r,$i-1,"cell_id_from");
            $cell_name_from=$this->getStorageCellName($cell_id_from);
            $cell_name_clear=str_replace("|","",$cell_name_from);
            $mas[$i]=["art_id"=>$art_id,"article_nr_displ"=>$article_nr_displ,"brand_id"=>$brand_id,"amount"=>$amount,"storage_id_from"=>$storage_id_from,"storage_name_from"=>$storage_name_from,"cell_id_from"=>$cell_id_from,"cell_name_from"=>"$cell_name_from","cell_name_clear"=>"$cell_name_clear"];
        }

        usort($mas, "myCmp"); $i=0;

        foreach ($mas as $key=>$value) {$i++;
            $id=$i;
            $art_id=$value["art_id"];
            $article_nr_displ=$value["article_nr_displ"]; $article_name=$this->getArticleName($art_id);
            $brand_id=$value["brand_id"]; $brand_name=$cat->getBrandName($brand_id);
            $amount=$value["amount"];
            $cell_name_from=$value["cell_name_from"];
            $list.="<tr>
                <td align='center'>$id</td>
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
        list($select_nom,$data_create,,,,$storage_name,$articles_amount,$amount,$volume,$weight_netto,$weight_brutto,$tpoint_id,$tpoint_name,$parrent_doc_type_id,$parrent_doc_id)=$this->getStorselInfo($select_id);
        if ($tpoint_name==""){$tpoint_name="-------------";}

        if ($parrent_doc_type_id==1){ // Jmoving
            $jmoving=new jmoving; $jmoving_name=$jmoving->getJmovingName($parrent_doc_id);
            list(,,,$storage_id_to,,,,)=$jmoving->getJmovingInfo($parrent_doc_id);
            list($tpoint_id,)=$jmoving->getTpointDataByStorage($storage_id_to);
            $tpoint_name=$this->getTpointName($tpoint_id);
        }
        if ($parrent_doc_type_id==2){ // DP
            $dp_name=$dp->getDpName($parrent_doc_id); $client_name=$dp->getDpClientName($parrent_doc_id);
            $tpoint_name="---";
        }

        $tpoint_address=$this->getTpointAddress($tpoint_id);
        $form=str_replace("{select_nom}",$select_nom,$form);
        $form=str_replace("{data_create}",$data_create,$form);
        $form=str_replace("{storage_name}",$storage_name,$form);
        $form=str_replace("{tpoint_name}",$tpoint_name,$form);
        $form=str_replace("{dp_name}",$dp_name,$form);
        $form=str_replace("{jmoving_name}",$jmoving_name,$form);
        $form=str_replace("{articles_amount}",$articles_amount,$form);
        $form=str_replace("{amount}",$amount,$form);
        $form=str_replace("{volume}",$volume,$form);
        $form=str_replace("{weight_netto}",$weight_netto,$form);
        $form=str_replace("{weight_brutto}",$weight_brutto,$form);
        $form=str_replace("{user_name}",$user_name,$form);
        $form=str_replace("{curtime}",date("Y-m-d H:i:s"),$form);
        $form=str_replace("{client_name}",$client_name,$form);
        $form=str_replace("{tpoint_address}",$tpoint_address,$form);$pData="";
        $form=str_replace("{pData}",$slave->data_word($pData),$form);
        $form=str_replace("{qrImage}","<img src='/phpqrcode/qrimage2.php?url=http://portal.myparts.pro/Storsel/printStS1/$select_id/".time()."'>",$form);
        $form=str_replace("{dp_note}",$dp->getDpNote($parrent_doc_id),$form);

        $this->addJournalRecord($select_id,52);
        //"Структура складського відбору"
        $mp=new media_print;
        $mp->print_document($form,array(210,280));
        return $form;
    }

    function getTpointName($tpoint_id){$db=DbSingleton::getDb(); $name="";
        $r=$db->query("select * from T_POINT where id='$tpoint_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$name=$db->result($r,0,"name");}
        return $name;
    }

    function addJournalRecord($select_id,$status_select){$db=DbSingleton::getDb();session_start();$user_id=$_SESSION["media_user_id"];
        $db->query("insert into J_SELECT_JOURNAL (`select_id`,`user_id`,`status_select`) values ('$select_id','$user_id','$status_select');"); return;
    }

    function getStorselJournalRecords($select_id){$db=DbSingleton::getDb();$data=array();
        $r=$db->query("select * from J_SELECT_JOURNAL where `select_id`='$select_id' order by id asc;"); $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $status_select=$db->result($r,$i-1,"status_select");
            $datatime=$db->result($r,$i-1,"datatime");
            $data[$status_select]=$datatime;
        }
        return $data;
    }

    function showStorselBarcodeForm($select_id){$db=DbSingleton::getDb();$cat=new catalogue;$list="";
        $form="";$form_htm=RD."/tpl/storsel_barcode_list.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select * from J_SELECT_STR  where select_id='$select_id' order by id asc;");$n=$db->num_rows($r);
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
            $storage_select_list=$this->getStorselBugList($select_id,$art_id,$id);

            $style="";
            if ($amount==$dif_amount_barcodes) $style="style='background:pink'";    //		Красный - вовсе не сканировался.
            if ($amount_barcodes>0 && $amount>$amount_barcodes) $style="style='background:lightyellow'";    //		Жёлтый - частично сканирован.
            //if (($amount_barcodes+$amount_barcodes_noscan)==$amount) $style="style='background:white'";
            if ($amount==$amount_barcodes) $style="style='background:white'";    //		Зелёный - полностью сканирован
            if ($amount_barcodes_noscan>0 && $amount_barcodes==0 && $dif_amount_barcodes==0) $style="style='background:lightblue'";    //		Синий - только ячейка, где без сканера

            $list.="<tr $style>
                <td align='center'>$i</td>
                <td align='center' id='amrai_$id'>$article_nr_displ</td>
                <td align='center' id='amrab_$id'>$brand_name</td>
                <td align='left' id='amran_$id'>$article_name</td>
                <td align='center'>$amount</td>
                <td align='center' id='amr_$id'>$amount_barcodes</td>
                <td align='center' id='amrd_$id'>$dif_amount_barcodes</td>
                <td align='center' id='amrns_$id'>$amount_barcodes_noscan</td>
                <td align='center'><button class='btn btn-xs btn-default' onclick='showStorselNoscanForm(\"$select_id\",\"$art_id\",\"$id\");' title='Фіксація без сканування'><i class='fa fa-cube'></i></button></td>
                <td align='center'><button class='btn btn-xs btn-danger' onclick='showStorselBugForm(\"$select_id\",\"$id\");' title='відхилення/брак/недостача'><i class='fa fa-bug'></i></button></td>
                <td align='center' id='ambg_$id'>$amount_bug</td>
                <td id='ssbug_$id'>$storage_select_list</td>
            </tr>";
        }
        $form=str_replace("{ArticlesList}",$list,$form);
        $form=str_replace("{select_id}",$select_id,$form);

        list($select_nom,,,,,,,,,,)=$this->getStorselInfo($select_id);
        $select_data=$user_name="";
        $form=str_replace("{select_nom}",$select_nom,$form);
        $form=str_replace("{select_data}",$select_data,$form);
        $form=str_replace("{user_name}",$user_name,$form);
        $form=str_replace("{curtime}",date("Y-m-d H:i:s"),$form);

        $db->query("update J_SELECT set status_select='84' where id='$select_id' limit 1;");

        $answer=1;$err="";
        return array($answer,$err,$form,"Пакування товару по штрих-кодам");
    }

    function saveStorselBarcodeForm($select_id,$barcode){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка індексу! Штрих-коду '$barcode' немає у відборі";
        $select_id=$slave->qq($select_id);$barcode=$slave->qq($barcode);$id=$amount_barcodes=$dif_amount_barcodes=0;
        if ($select_id>0 && $barcode!=""){
            $art_id=$this->getArtIdByBarcode($barcode);
            $r=$db->query("select * from J_SELECT_STR where select_id='$select_id' and art_id='$art_id' and amount>amount_barcodes order by id asc;");$n=$db->num_rows($r);
            for ($i=1;$i<=$n;$i++){
                $id=$db->result($r,$i-1,"id");
                $amount=$db->result($r,$i-1,"amount");
                $amount_barcodes=$db->result($r,$i-1,"amount_barcodes");
                $amount_barcodes_noscan=$db->result($r,$i-1,"amount_barcodes_noscan");
                $amount_bug=$db->result($r,$i-1,"amount_bug");
                if ($amount>($amount_barcodes+$amount_barcodes_noscan+$amount_bug)){
                    $amount_barcodes+=1;
                    $dif_amount_barcodes=$amount-$amount_barcodes-$amount_barcodes_noscan-$amount_bug;
                    $db->query("update J_SELECT_STR set amount_barcodes='$amount_barcodes' where id='$id' limit 1;");
                    $answer=1;$err="";
                    $i=$n+1;
                }
            }
        }else{ $answer=0;$err="Помилка штрих-коду";}
        return array($answer,$err,$id,$amount_barcodes,$dif_amount_barcodes);
    }

    function finishStorselBarcodeForm($select_id){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка індексу!!";$dp=new dp;$id=0;
        $select_id=$slave->qq($select_id); $cur_date=date("Y-m-d H:i:s"); session_start(); $user_id=$_SESSION["media_user_id"];
        if ($select_id>0){
            $r=$db->query("select * from J_SELECT_STR where select_id='$select_id' and amount>(amount_barcodes+amount_barcodes_noscan+amount_bug);");$n=$db->num_rows($r);
            if ($n>0){
                $answer=0;$err="Не завершено перевірку по штрих-кодам";
            }
            if ($n==0){
                $r=$db->query("select (SUM(amount_barcodes)+SUM(amount_barcodes_noscan)) as new_amount from J_SELECT_STR where select_id='$select_id';");$n=$db->num_rows($r);
                if ($n==1){
                    $new_amount=$db->result($r,0,"new_amount");
                    if ($new_amount>0){
                    $db->query("update J_SELECT set status_select='85', amount='$new_amount', data_collect='$cur_date', user_collect='$user_id' where id='$select_id' limit 1;");}
                    if ($new_amount==0){
                    $db->query("update J_SELECT set status_select='128', amount='$new_amount', data_collect='$cur_date', user_collect='$user_id' where id='$select_id' limit 1;");}
                    //Статус переміщення 'Відібрано'
                    $r=$db->query("select parrent_doc_id from J_SELECT where id='$select_id' limit 1;");$n=$db->num_rows($r);
                    $parrent_doc_id = $db->result($r,0,"parrent_doc_id");
                    if ($n>0) $db->query("update J_MOVING set status_jmoving='107' where id='$parrent_doc_id' limit 1;");

                    $r1=$db->query("select * from J_SELECT_STR where select_id='$select_id';");$n1=$db->num_rows($r1);
                    for ($i=1;$i<=$n1;$i++){
                        $str_id=$db->result($r1,$i-1,"id");
                        $art_id=$db->result($r1,$i-1,"art_id");
                        $amount_js=$db->result($r1,$i-1,"amount_barcodes")+$db->result($r1,$i-1,"amount_barcodes_noscan");
                        $select_storage_id_from=$db->result($r1,$i-1,"storage_id_from");
                        $select_cell_id_from=$db->result($r1,$i-1,"cell_id_from");

                        $db->query("update J_SELECT_STR set amount_collect='$amount_js' where select_id='$select_id' and id='$str_id' limit 1;");

                        list($parrent_doc_type_id,$parrent_doc_id,$parrent_doc_type_id2,$parrent_doc_id2)=$this->getStorselParrentData($select_id);
                        $ra=$db->query("select id, art_id, amount_barcodes, amount_barcodes_noscan, amount_bug, amount_collect as amount_js from J_SELECT_STR where select_id='$select_id' and art_id='$art_id'");$na=$db->num_rows($ra);
                        if ($na>0){
                            $storsel_amount_bug=0;
                            $new_amount_select=0;
                            for ($j=1;$j<=$na;$j++){
                                $new_amount_select+=floatval($db->result($ra,$j-1,"amount_js"));
                                $storsel_amount_bug+=floatval($db->result($ra,$j-1,"amount_bug"));
                            }
                            //$new_amount_barcodes_noscan=$db->result($ra,0,"amount_barcodes_noscan");
                            //$new_amount_barcodes=$db->result($ra,0,"amount_barcodes");
                            if ($parrent_doc_type_id==1){ // Перемещение
                                $db->query("update J_MOVING_STR set amount='$new_amount_select' where jmoving_id='$parrent_doc_id' and select_id='$select_id' and art_id='$art_id' and storage_id_from='$select_storage_id_from'  and (cell_id_from='$select_cell_id_from' or cell_id_from='0') limit 1;");
                                if ($parrent_doc_type_id2==1 && $parrent_doc_id2>0){$all_pd_summ=0; //предпродажа с перемещением
                                    $r2=$db->query("select * from J_DP_STR where dp_id='$parrent_doc_id2' and art_id='$art_id' and storage_id_from='$select_storage_id_from' limit 0,1;");$n2=$db->num_rows($r2);
                                    if ($n2==1){
                                        $dp_str_id=$db->result($r2,0,"id");
                                        $amount_dp=$db->result($r2,0,"amount");$amount_dp_bug=0;
                                        $amount_dp_collect=$db->result($r2,0,"amount_collect");
                                        if ($amount_dp_collect>0){ $amount_dp_bug=$amount_dp-$amount_dp_collect;}
                                        $price_dp=$db->result($r2,0,"price_end");
                                        $summ_dp=$db->result($r2,0,"summ");
                                        if ($amount_dp!=$new_amount_select){
                                            $summ_dp=round($price_dp*$new_amount_select,2); $all_pd_summ=1;
                                        }
                                        $db->query("update J_DP_STR set amount_collect='$new_amount_select', amount_bug='$amount_dp_bug', summ='$summ_dp' where id='$dp_str_id' limit 1;");
                                        if ($all_pd_summ==1){$dp->updateDpSumm($parrent_doc_id2);}
                                    }
                                }
                            }
                            if ($parrent_doc_type_id==2){ $all_pd_summ=0; // Предпродажа без перемещения
                                $r2=$db->query("select * from J_DP_STR where dp_id='$parrent_doc_id' and art_id='$art_id' and cur_select_str_id='$str_id' limit 0,1;");$n2=$db->num_rows($r2);
                                if ($n2==1){
                                    $dp_str_id=$db->result($r2,0,"id");
                                    $amount_dp=$db->result($r2,0,"amount");$amount_dp_bug=0;
                                    $amount_dp_collect=$db->result($r2,0,"amount_collect"); $ers=0;
                                    if ($amount_dp_collect>0){ $amount_dp_bug=$amount_dp-$amount_dp_collect;$ers=1;}
                                    if ($storsel_amount_bug>0 && $ers==0){ $amount_dp_bug=$storsel_amount_bug;}//$ers=1;
                                    $price_dp=$db->result($r2,0,"price_end");
                                    $summ_dp=$db->result($r2,0,"summ");
                                    if ($amount_dp!=$new_amount_select){
                                        $summ_dp=round($price_dp*$new_amount_select,2); $all_pd_summ=1;
                                    }
                                    if ($new_amount_select>0){
                                        $db->query("update J_DP_STR set amount_collect='$new_amount_select', amount_bug='$amount_dp_bug', summ='$summ_dp', status_dps='96' where id='$dp_str_id' limit 1;");
                                    }
                                    if ($new_amount_select==0){
                                        $db->query("update J_DP_STR set amount_collect='$new_amount_select', amount_bug='$amount_dp_bug', summ='$summ_dp', status_dps='170' where id='$dp_str_id' limit 1;");
                                    }
                                    if ($all_pd_summ==1){$dp->updateDpSumm($parrent_doc_id);}
                                }
                            }
                        }
                    }
                }
                $this->addJournalRecord($select_id,85);
                $answer=1;$err="";
            }
        }else{ $answer=0;$err="Помилка штрих-коду";}
        return array($answer,$err,$id,85);
    }

    function showStorselBugForm($select_id,$str_id){$db=DbSingleton::getDb();$cat=new catalogue;$manual=new manual;session_start();$answer=0;$err="Помилка індексу";
        $form="";$form_htm=RD."/tpl/storsel_bug_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select * from J_SELECT_STR where select_id='$select_id' and id='$str_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $art_id=$db->result($r,0,"art_id");
            $article_nr_displ=$db->result($r,0,"article_nr_displ"); $article_name=$this->getArticleName($art_id);
            $brand_id=$db->result($r,0,"brand_id");$brand_name=$cat->getBrandName($brand_id);
            $amount=$db->result($r,0,"amount");
            $amount_barcodes=$db->result($r,0,"amount_barcodes");
            $dif_amount_barcodes=$amount-$amount_barcodes;
            $form=str_replace("{select_id}",$select_id,$form);
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

    function saveStorselBugForm($select_id,$str_id,$storage_select_bug,$amount_bug){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка індексу!!";
        $select_id=$slave->qq($select_id);$str_id=$slave->qq($str_id);$storage_select_bug=$slave->qq($storage_select_bug);$amount_bug=$slave->qq($amount_bug);
        $id=$dif_amount_barcodes=$new_amount_bug=$amount_barcodes=$amount_barcodes_noscan=0;$storage_select_bug_list="";
        if ($select_id>0 && $str_id>0 && $storage_select_bug>0 && $amount_bug>0){
            $r=$db->query("select * from J_SELECT_STR where select_id='$select_id' and id='$str_id' limit 0,1;");$n=$db->num_rows($r);
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

                    $db->query("update J_SELECT_STR set amount_bug='$new_amount_bug', amount_barcodes='$amount_barcodes', amount_barcodes_noscan='$amount_barcodes_noscan' where id='$id' limit 1;");
                    //list($parrent_doc_type_id,$parrent_doc_id)=$this->getStorselParrentData($select_id);
                    //if ($parrent_doc_type_id==1){
                        //$new_amount_jmoving=$amount-$new_amount_bug;
                    //}
                    $dif_amount_barcodes=$amount-$amount_barcodes-$amount_barcodes_noscan-$new_amount_bug;
                    if ($dif_amount_barcodes<0){$dif_amount_barcodes=0;}

                    $db->query("insert into J_SELECT_STR_BUG (`select_id`,`art_id`,`str_id`,`article_nr_displ`,`storage_select_bug`,`amount_bug`) values ('$select_id','$art_id','$str_id','$article_nr_displ','$storage_select_bug','$amount_bug');");

                    /*ОБНОВИТЬ РЕЗЕРВЫ ПОСЛЕ ФИКСАЦИИ ОТКЛОНЕНИЯ*/
                    $this->updateStockStorageBug($art_id,$storage_id_from,$cell_id_from,$cell_use,$amount_bug);

                    $storage_select_bug_list=$this->getStorselBugList($select_id,$art_id,$id);
                    $answer=1;$err="";
                }
            }
        } else {$answer=0;$err="Помилка штрих-коду";}
        return array($answer,$err,$id,$storage_select_bug_list,$dif_amount_barcodes,$new_amount_bug,$amount_barcodes,$amount_barcodes_noscan);
    }

    function getStorselParrentData($select_id){$db=DbSingleton::getDb();$parrent_doc_type_id=0; $parrent_doc_id=0;$parrent_doc_type_id2=0; $parrent_doc_id2=0;
        $r=$db->query("select * from J_SELECT where id='$select_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $parrent_doc_type_id=$db->result($r,0,"parrent_doc_type_id");
            $parrent_doc_id=$db->result($r,0,"parrent_doc_id");
        }
        if ($parrent_doc_type_id==1){
            $r=$db->query("select * from J_MOVING where id='$parrent_doc_id' limit 0,1;");$n=$db->num_rows($r);
            if ($n==1){
                $parrent_doc_type_id2=$db->result($r,0,"parrent_type_id");
                $parrent_doc_id2=$db->result($r,0,"parrent_doc_id");
            }
        }
        return array($parrent_doc_type_id,$parrent_doc_id,$parrent_doc_type_id2,$parrent_doc_id2);
    }

    function getStorselBugList($select_id,$art_id,$str_id){$db=DbSingleton::getDb();$manual=new manual;$list="";
        $r=$db->query("select * from J_SELECT_STR_BUG where select_id='$select_id' and art_id='$art_id' and str_id='$str_id' order by id asc;");$n=$db->num_rows($r);
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

    function showStorselNoscanForm($select_id,$str_id){$db=DbSingleton::getDb();$cat=new catalogue;$answer=0;$err="Помилка індексу";
        $form="";$form_htm=RD."/tpl/storsel_noscan_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select * from J_SELECT_STR where select_id='$select_id' and id='$str_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $art_id=$db->result($r,0,"art_id");
            $article_nr_displ=$db->result($r,0,"article_nr_displ"); $article_name=$this->getArticleName($art_id);
            $brand_id=$db->result($r,0,"brand_id");$brand_name=$cat->getBrandName($brand_id);
            $amount=$db->result($r,0,"amount");
            $amount_barcodes=$db->result($r,0,"amount_barcodes");
            $amount_barcodes_noscan=$db->result($r,0,"amount_barcodes_noscan");
            $dif_amount_barcodes=$amount-$amount_barcodes-$amount_barcodes_noscan;
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

    function saveStorselNoscanForm($select_id,$art_id,$str_id,$amount_barcode_noscan){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка індексу!!";
        $select_id=$slave->qq($select_id);$art_id=$slave->qq($art_id);$str_id=$slave->qq($str_id);$amount_barcode_noscan=$slave->qq($amount_barcode_noscan);
        $id=$dif_amount_barcodes=$new_amount_barcode_noscan=0;
        if ($select_id>0 && $art_id>0 && $str_id>0 && $amount_barcode_noscan>0){
            $r=$db->query("select * from J_SELECT_STR where select_id='$select_id' and art_id='$art_id' and id='$str_id' limit 0,1;");$n=$db->num_rows($r);
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
                    $db->query("update J_SELECT_STR set amount_barcodes_noscan='$new_amount_barcode_noscan' where id='$id' limit 1;");
                    $answer=1;$err="";
                }
            }
        } else {$answer=0;$err="Помилка штрих-коду";}
        return array($answer,$err,$id,$dif_amount_barcodes,$new_amount_barcode_noscan);
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

    function updateStockFromStorage($art_id,$storage_id_from,$cell_id_from,$cell_use,$amount){$dbt=DbSingleton::getTokoDb();
        $dbt->query("update T2_ARTICLES_STRORAGE set `RESERV_AMOUNT`= `RESERV_AMOUNT` - $amount where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_from' limit 1;");
        if ($cell_use==1){
            $dbt->query("update T2_ARTICLES_STRORAGE_CELLS set `RESERV_AMOUNT`=`RESERV_AMOUNT` - $amount where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_from' and `STORAGE_CELLS_ID`='$cell_id_from' limit 1;");
        }
        $er=0;
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
            $dbt->query("update T2_ARTICLES_STRORAGE set `AMOUNT`= `AMOUNT` + $amount where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_to' limit 1;");
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

    function updateStockFromStorageLocal($art_id,$storage_id_from,$cell_id_from,$cell_id_to,$amount){$dbt=DbSingleton::getTokoDb(); $er=1;
        $r=$dbt->query("select `AMOUNT`, `RESERV_AMOUNT` from T2_ARTICLES_STRORAGE where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_from' limit 0,1;");$n=$dbt->num_rows($r);
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

    function calculateStorselParams($select_id) {$db=DbSingleton::getDb();$select_volume=$select_weight_netto=$select_weight_brutto=0;
        $r=$db->query("select * from J_SELECT_STR where select_id='$select_id';");$n=$db->num_rows($r);
        if ($n>0) {
            for ($i=1;$i<=$n;$i++){
                $art_id=$db->result($r,$i-1,"art_id");
                list($VOLUME,$WEIGHT_NETTO,$WEIGHT_BRUTTO)=$this->getArtLogistic($art_id);
                $select_volume+=$VOLUME;
                $select_weight_netto+=$WEIGHT_NETTO;
                $select_weight_brutto+=$WEIGHT_BRUTTO;
            }
            $db->query("update J_SELECT set volume='$select_volume', weight_netto='$select_weight_netto', weight_brutto='$select_weight_brutto' where id='$select_id';");
        }
        return "VOLUME=$select_volume, WEIGHT_NETTO=$select_weight_netto, WEIGHT_BRUTTO=$select_weight_brutto";
    }

    function getArtLogistic($art_id) {$db=DbSingleton::getTokoDb();$VOLUME=$WEIGHT_NETTO=$WEIGHT_BRUTTO=0;
        $r=$db->query("select * from T2_PACKAGING where ART_ID='$art_id' limit 1;");$n=$db->num_rows($r);
        if ($n>0) {
            $VOLUME=$db->result($r,0,"VOLUME");
            $WEIGHT_NETTO=$db->result($r,0,"WEIGHT_NETTO");
            $WEIGHT_BRUTTO=$db->result($r,0,"WEIGHT_BRUTTO");
        }
        return array($VOLUME,$WEIGHT_NETTO,$WEIGHT_BRUTTO);
    }

}

function myCmp($a, $b) {
   if ($a["cell_name_clear"] == $b["cell_name_clear"]) return 0;
   return $a["cell_name_clear"] > $b["cell_name_clear"] ? 1 : -1;
}

