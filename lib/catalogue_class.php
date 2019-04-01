<?php

class catalogue {
	
	var $kol_price_rating=12;
	
    function getManualCap($id) {$db=DbSingleton::getDb();
        $r=$db->query("select mcaption from manual where id='$id';");
        $cap=$db->result($r,0,"mcaption");
        return $cap;
    }

    function getArticleJDocsData($doc_type,$doc_id,$art_id) {$db=DbSingleton::getDb();
        $prixod=$rasxod=$dvigen=""; $name=$prefix=$color=""; $storage_from=$storage_to=0;
        if ($doc_type==1) {
            $r=$db->query("select j.storage_id, j.storage_cells_id, j.client_id, j.client_seller, j.prefix, j.doc_nom, js.amount from J_INCOME j 
                left outer join J_INCOME_STR js on js.income_id=j.id
            where j.id='$doc_id' and js.art_id='$art_id';");

            $client_id=$db->result($r,0,"client_id"); $client_id=$this->getClientName($client_id);
            $client_seller=$db->result($r,0,"client_seller"); $client_seller=$this->getClientName($client_seller);
            $cell_to=$db->result($r,0,"storage_cells_id"); $cell_to=$this->getStorageCellName($cell_to);
            $storage_to=$db->result($r,0,"storage_id"); $storage_to=$this->getStorageName($storage_to)." ($cell_to) - $client_id";
            $storage_from=$client_seller;
            $amount=$db->result($r,0,"amount");
            $doc_nom=$db->result($r,0,"doc_nom");
            $prefix=$db->result($r,0,"prefix")."-$doc_nom";
            $prixod=$amount;
            $name="Прихідна накладна";
            $color="lightblue";
        }

        if ($doc_type==2) {
            $r=$db->query("select j.storage_id_to, j.cell_id_to, j.type_id, j.prefix, j.doc_nom, js.storage_id_from, js.cell_id_from as cell_from, js.cell_id_to as cell_to, js.amount, js.select_id from J_MOVING j
                left outer join J_MOVING_STR js on js.jmoving_id=j.id
            where j.id='$doc_id' and js.art_id='$art_id';");

            $type=$db->result($r,0,"type_id");
            $select_id=$db->result($r,0,"select_id");
            $cell4="";

            $r2=$db->query("select * from J_SELECT_STR where select_id='$select_id' and art_id='$art_id';"); $n=$db->num_rows($r);
            for ($i=1;$i<=$n;$i++){
                $amount=$db->result($r2,$i-1,"amount");
                $cell_id_from=$db->result($r2,$i-1,"cell_id_from"); $cell_id_from=$this->getStorageCellName($cell_id_from);
                if ($n==1) $cell4.="$cell_id_from";
                else $cell4.=" $cell_id_from = $amount шт.;";
            }

            $cell1=$db->result($r,0,"cell_from"); $cell1=$this->getStorageCellName($cell1);
            $cell2=$db->result($r,0,"cell_to"); $cell2=$this->getStorageCellName($cell2);
            $cell3=$db->result($r,0,"cell_id_to"); $cell3=$this->getStorageCellName($cell3);

            if ($type) {$cell_to=$cell3; $cell_from=$cell4;} else {$cell_to=$cell1; $cell_from=$cell2;}
            if ($type) $type="між складами"; else $type="внутрішнє";

            $storage_to=$db->result($r,0,"storage_id_to"); $storage_to=$this->getStorageName($storage_to)." ($cell_to)";
            $storage_from=$db->result($r,0,"storage_id_from"); $storage_from=$this->getStorageName($storage_from)." ($cell_from)";
            $amount=$db->result($r,0,"amount");
            $doc_nom=$db->result($r,0,"doc_nom");
            $prefix=$db->result($r,0,"prefix")."-$doc_nom";
            $dvigen=$amount;
            $name="Переміщення ($type)";
            $color="";
        }

        if ($doc_type==3) {
            $r=$db->query("select j.doc_type_id, j.dp_id, j.client_id, j.prefix, j.doc_nom, js.amount, js.storage_id_from, js.cell_id_from from J_SALE_INVOICE j
                left outer join J_SALE_INVOICE_STR js on js.invoice_id=j.id
            where j.id='$doc_id' and js.art_id='$art_id';"); $n=$db->num_rows($r);
            $cells="";$type="";

            for ($i=1;$i<=$n;$i++){
                $amount=$db->result($r,$i-1,"amount");
                $cell_from=$db->result($r,$i-1,"cell_id_from"); $cell_from=$this->getStorageCellName($cell_from);
                if ($n==1) $cells=$cell_from; else $cells.=" $cell_from = $amount шт.;";
                $storage_from=$db->result($r,$i-1,"storage_id_from"); $storage_from=$this->getStorageName($storage_from)." ($cells)";
                $doc_type_id=$db->result($r,$i-1,"doc_type_id"); $type=$this->getManualCap($doc_type_id);
                $client_id=$db->result($r,$i-1,"client_id"); $client_id=$this->getClientName($client_id); $storage_to=$client_id;
                $doc_nom=$db->result($r,$i-1,"doc_nom");
                $prefix=$db->result($r,$i-1,"prefix")."-$doc_nom";
                $rasxod+=$amount;
            }
            $name="Видаткова накладна ($type)";
            $color="lightgreen";
        }

        if ($doc_type==4) {
            $r=$db->query("select j.storage_id, j.client_id, j.prefix, j.doc_nom, js.amount, j.cell_id from J_BACK_CLIENTS j
                left outer join J_BACK_CLIENTS_STR js on js.back_id=j.id
            where j.id='$doc_id' and js.art_id='$art_id';");

            $client_id=$db->result($r,0,"client_id"); $client_id=$this->getClientName($client_id); $storage_from=$client_id;
            $cell_id=$db->result($r,0,"cell_id"); $cell_to=$this->getStorageCellName($cell_id);
            $storage_to=$db->result($r,0,"storage_id"); $storage_to=$this->getStorageName($storage_to)." ($cell_to)";
            $amount=$db->result($r,0,"amount");
            $doc_nom=$db->result($r,0,"doc_nom");
            $prefix=$db->result($r,0,"prefix")."-$doc_nom";
            $prixod=$amount;
            $name="Повернення від клієнта";
            $color="pink";
        }
        return array($name,$prefix,$color,$prixod,$rasxod,$dvigen,$storage_from,$storage_to);
    }

    function showArticleJDocs($art_id) {$db=DbSingleton::getDb();$form="";
        if ($art_id>0){
            $form_htm=RD."/tpl/catalogue_history_moving.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
            $r=$db->query("select *, sum(amount) as sum_amount from J_ART_DOCS where art_id='$art_id' group by art_id, doc_id order by data desc;"); $n=$db->num_rows($r);$list="";
            for ($i=1;$i<=$n;$i++){
                $amount=intval($db->result($r,$i-1,"sum_amount"));
                $data=$db->result($r,$i-1,"data");
                $doc_type=$db->result($r,$i-1,"doc_type");
                $doc_id=$db->result($r,$i-1,"doc_id");
                list($name,$prefix,$color,$prixod,$rasxod,$dvigen,$storage_from,$storage_to)=$this->getArticleJDocsData($doc_type,$doc_id,$art_id);
                $list.="<tr align='center' style='background:$color;'>
                    <td>$i</td>
                    <td>$data</td>
                    <td>$name</td>
                    <td>$prefix</td>
                    <td>$storage_from</td>
                    <td>$storage_to</td>
                    <td>$prixod</td>
                    <td>$rasxod</td>
                    <td>$dvigen</td>
                    <td>$amount</td>
                </tr>";
            }
            $form=str_replace("{list}",$list,$form);
            list($article_nr_displ,$brand_id,$brand_name)=$this->getArticleNrDisplBrand($art_id);
            $form=str_replace("{article_nr_displ}",$article_nr_displ." ".$brand_name,$form);
        }
        return array($form,"Історія переміщення");
    }

    function showCatNewArticle(){
        $form_htm=RD."/tpl/catalogue_new_article_form.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $form=str_replace("{brand_list}",$this->showBrandLetterListSelect(""),$form);
        $form=str_replace("{goods_list}",$this->showGoodsGroupLetterListSelect(""),$form);
        $form=str_replace("{manuf_list}",$this->showManufListSelect(""),$form);
        return array($form,"Генерування нового артиклу номенклатури");
    }

    function findNewArtNextNum($brand,$group,$sub_group,$manuf){$db=DbSingleton::getTokoDb();$ar=[];
        $brand=explode("-",$brand);$brand_key=$brand[1];
        $group=explode("-",$group);$group_key=$group[1];
        $sub_group=explode("-",$sub_group);$sub_group_key=$sub_group[1];
        $manuf=explode("-",$manuf);$manuf_key=$manuf[1];
        $ex=$brand_key."".$group_key."".$sub_group_key."".$manuf_key;
        $r=$db->query("select ARTICLE_NR_SEARCH from T2_ARTICLES where ARTICLE_NR_SEARCH like '$ex%' order by ARTICLE_NR_SEARCH asc;");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){$ar[$i]=substr($db->result($r,$i-1,"ARTICLE_NR_SEARCH"),0,8);}
        $num=intval(substr($ar[$n],4,3)); $num+=1; if (strlen($num)==1){$num="00".$num;}if (strlen($num)==2){$num="0".$num;}
        return $num;
    }

    function findNewArtID($brand){$db=DbSingleton::getTokoDb();$adp_from=1000;$adp_to=10000000;
        $brand=explode("-",$brand);$brand_key=$brand[1];
        if ($brand_key=="T"){$adp_from=100000000;$adp_to=1000000000;}
        $r=$db->query("select max(ART_ID) as mid from T2_ARTICLES where ART_ID>='$adp_from' and ART_ID<='$adp_to';");$mid=$db->result($r,0,"mid");
        $mid+=1;
        return $mid;
    }

    function checkCatalogueNewArt($num,$art_id){$db=DbSingleton::getTokoDb();$answer=0;$err="Помилка обробки данних!";
        $r=$db->query("select COUNT(ART_ID) as kol from T2_ARTICLES where ART_ID='$art_id'");$art_ex=$db->result($r,0,"kol")+0;
        $r=$db->query("select COUNT(ARTICLE_NR_SEARCH) as kol from T2_ARTICLES where ARTICLE_NR_SEARCH='$num'");$num_ex=$db->result($r,0,"kol")+0;
        if ($num_ex==0 && $art_ex==0){$answer=1;$err="";}
        if ($num_ex>0){$answer=0;$err="Індекс існує у базі";}
        if ($art_ex>0){$answer=0;$err="ART_ID існує у базі";}
        return array($answer,$err);
    }

    function saveCatalogueNewArt($num,$art_id,$brand,$group,$sub_group,$manuf){$db=DbSingleton::getTokoDb();$new_art_id="";$answer=0;$err="Помилка обробки данних!";
        $brand=explode("-",$brand);$brand_key=$brand[1];$brand_id=$brand[0];
        $group=explode("-",$group);$group_key=$group[1];$group_id=$group[0];
        $sub_group=explode("-",$sub_group);$sub_group_key=$sub_group[1];$sub_group_id=$sub_group[0];
        $manuf=explode("-",$manuf);$manuf_key=$manuf[1];$manuf_id=$manuf[0];
        if ($art_id!="" && $num!="" && $brand>0 && $sub_group_id>0){
            $r=$db->query("select COUNT(ART_ID) as kol from T2_ARTICLES where ART_ID='$art_id'");$art_ex=$db->result($r,0,"kol")+0;
            $r=$db->query("select COUNT(ARTICLE_NR_SEARCH) as kol from T2_ARTICLES where ARTICLE_NR_SEARCH='$num'");$num_ex=$db->result($r,0,"kol")+0;
            if ($num_ex>0){$answer=0;$err="Індекс існує у базі";}
            if ($art_ex>0){$answer=0;$err="ART_ID існує у базі";}
            if ($num_ex==0 && $art_ex==0){
                $num_up=strtoupper($num);
                $db->query("insert into T2_ARTICLES (`ART_ID`,`ARTICLE_NR_DISPL`,`ARTICLE_NR_SEARCH`,`BRAND_ID`) values ('$art_id','$num_up','$num_up','$brand_id');");
                $db->query("insert into T2_GOODS_GROUP (`ART_ID`,`GOODS_GROUP_ID`) values ('$art_id','$sub_group_id');");
                $db->query("insert into T2_CROSS (`ART_ID`,`SEARCH_NUMBER`,`KIND`,`BRAND_ID`,`DISPLAY_NR`,`RELATION`) values ('$art_id','$num_up','0','$brand_id','$num_up','0');");

                $sub_group_name=$this->getGoodsGroupName($sub_group_id);
                $db->query("insert into T2_NAMES (`ART_ID`,`LANG_ID`,`NAME`,`INFO`) values ('$art_id','16','$sub_group_name','');");
                /*$rg=$db->query("select STR_ID from T2_GTGG_CROSS where `GROUP_ID`='$sub_group_id' order by id asc;");$bg=$db->num_rows($rg);
                for ($i=1;$i<=$ng;$i++){
                    $str_id=$db->result($rg,$i-1,"STR_ID");
                    $db->query("insert into T2_TREE (`ART_ID`,`STR_ID`) values ('$art_id','$str_id');");
                }*/
                $answer=1;$err="";
            }
        }
        return array($answer,$err,$new_art_id);
    }

    function show_catalogue_range($art){
        $form_htm=RD."/tpl/catalogue.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $art=str_replace("--","/",$art);
        list($header_list,$range_list,$list_brand_select)=$this->showArticlesSearchList($art,"",0);
        $form=str_replace("{art}",$art,$form);
        $form=str_replace("{header_list}",$header_list,$form);
        $form=str_replace("{range_list}",$range_list,$form);
        $form=str_replace("{list_brand_select}",$list_brand_select,$form);
        $form=str_replace("{fil4BrandList}",$this->showBrandListSelect(""),$form);
        $form=str_replace("{fil4SupplList}",$this->showSupplListSelect(""),$form);
        $form=str_replace("{fil4GoodsGroupList}",$this->showGoodsGroupListSelect(""),$form);
        $form=str_replace("{fil4Top}","",$form);
        $form=str_replace("{fil4StokTo}","",$form);
        $form=str_replace("{fil4StokFrom}","",$form);
        $form=str_replace("{fil2ManufactureList}",$this->showManufactureListSelect(""),$form);
        $form=str_replace("{fil2StrId}","",$form);
        $form=str_replace("{fil2StrText}","",$form);
        return $form;
    }

    function showCatFieldsViewForm(){$db=DbSingleton::getDb();session_start();$user_id=$_SESSION["media_user_id"];$table_key="catalogue";
        $form="";$form_htm=RD."/tpl/catalogue_fields_view_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select * from CFN_TABLE_FIELDS where table_key='$table_key' order by id asc;");$n=$db->num_rows($r);$list="";$lst=array();
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $field_name=$db->result($r,$i-1,"field_name");
            $field_key=$db->result($r,$i-1,"field_key");
            list($checked,$pos)=$this->checkCatalogueFieldsUserCheck($user_id,$table_key,$field_key);if ($pos==0){$pos=$i;}
            $lst[$pos]="<tr id='usePos_".$id."'>
                <td><span class='glyphicon glyphicon-move'></span></td>
                <td>$i</td>
                <td>$field_name ($field_key)</td>
                <td>
                    <div class=\"switch\">
                        <div class=\"onoffswitch\">
                            <input type=\"checkbox\" $checked class=\"onoffswitch-checkbox\" id=\"use_$id\" value='1'>
                            <label class=\"onoffswitch-label\" for=\"use_$id\">
                                <span class=\"onoffswitch-inner\"></span>
                                <span class=\"onoffswitch-switch\"></span>
                            </label>
                        </div>
                    </div>
                </td>
            </tr>";
        }
        for ($i=1;$i<=$n;$i++){$list.=$lst[$i];}
        $form=str_replace("{fields_list}",$list,$form);
        $form=str_replace("{kol_fields}",$n,$form);
        $form=str_replace("{table_key}",$table_key,$form);
        return array($form,"Налаштування відображення таблиці Номенклатура");
    }

    function showCatFieldsViewDocForm(){$db=DbSingleton::getDb();session_start();$user_id=$_SESSION["media_user_id"];$table_key="catalogue_doc";
        $form="";$form_htm=RD."/tpl/catalogue_fields_view_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select * from CFN_TABLE_FIELDS where table_key='$table_key' order by id asc;");$n=$db->num_rows($r);$list="";$lst=array();
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $field_name=$db->result($r,$i-1,"field_name");
            $field_key=$db->result($r,$i-1,"field_key");
            list($checked,$pos)=$this->checkCatalogueFieldsUserCheck($user_id,$table_key,$field_key);if ($pos==0){$pos=$i;}
            $lst[$pos]="<tr id='usePos_".$id."'>
                <td><span class='glyphicon glyphicon-move'></span></td>
                <td>$i</td>
                <td>$field_name ($field_key)</td>
                <td>
                    <div class=\"switch\">
                        <div class=\"onoffswitch\">
                            <input type=\"checkbox\" $checked class=\"onoffswitch-checkbox\" id=\"use_$id\" value='1'>
                            <label class=\"onoffswitch-label\" for=\"use_$id\">
                                <span class=\"onoffswitch-inner\"></span>
                                <span class=\"onoffswitch-switch\"></span>
                            </label>
                        </div>
                    </div>
                </td>
            </tr>";
        }
        for ($i=1;$i<=$n;$i++){$list.=$lst[$i];}
        $form=str_replace("{fields_list}",$list,$form);
        $form=str_replace("{kol_fields}",$n,$form);
        $form=str_replace("{table_key}",$table_key,$form);
        return array($form,"Налаштування відображення таблиці Номенклатура");
    }

    function checkCatalogueFieldsUserCheck($user_id,$table_key,$field_key){$db=DbSingleton::getDb();$ch="checked";$field_pos=0;
        $r=$db->query("select field_active,field_pos from CFN_USERS_TABLE_CONFIG where table_key='$table_key' and user_id='$user_id' and field_key='$field_key' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $field_active=$db->result($r,0,"field_active");if ($field_active==0){$ch="";}
            $field_pos=$db->result($r,0,"field_pos");
        }
        return array($ch,$field_pos);
    }

    function saveCatalogueFieldsViewForm($kol_fields,$fl_id,$fl_ch,$table_key){$db=DbSingleton::getDb();$slave=new slave;session_start();$user_id=$_SESSION["media_user_id"];
        $answer=0;$err="Помилка збереження даних!";if ($table_key==""){$table_key="catalogue";}
        $kol_fields=$slave->qq($kol_fields);$fl_id=$slave->qq($fl_id);$fl_ch=$slave->qq($fl_ch);
        if ($kol_fields>0){
            $db->query("delete from CFN_USERS_TABLE_CONFIG where user_id='$user_id' and table_key='$table_key';");
            for ($i=1;$i<=$kol_fields;$i++){
                $field_id=$fl_id[$i];
                $field_ch=$fl_ch[$i];
                list($field_name,$field_key)=$this->getFieldInfo("$table_key",$field_id);
                $db->query("insert into CFN_USERS_TABLE_CONFIG (`user_id`,`table_key`,`field_name`,`field_key`,`field_active`,`field_pos`) values ('$user_id','$table_key','$field_name','$field_key','$field_ch','$i');");
            }
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function getFieldInfo($table_key,$field_id){$db=DbSingleton::getDb();$name="";$key="";
        $r=$db->query("select * from CFN_TABLE_FIELDS where table_key='$table_key' and id='$field_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $name=$db->result($r,0,"field_name");
            $key=$db->result($r,0,"field_key");
        }
        return array($name,$key);
    }

    function getCatalogueClientViewFieldsData($user_id,$table_key){$db=DbSingleton::getDb();if ($table_key==""){$table_key="catalogue";}$lst=array();
        $r=$db->query("select * from CFN_USERS_TABLE_CONFIG where table_key='$table_key' and user_id='$user_id' and field_active='1' order by field_pos,id asc;");
        $n=$db->num_rows($r);
        if ($n==0){
            $r=$db->query("select * from CFN_TABLE_FIELDS where table_key='$table_key' order by id asc;");$n=$db->num_rows($r);
        }
        if ($n>0){
            for ($i=1;$i<=$n;$i++){
                $field_name=$db->result($r,$i-1,"field_name");
                $field_key=$db->result($r,$i-1,"field_key");
                $lst[$i]["field_name"]=$field_name;
                $lst[$i]["field_key"]=$field_key;
            }
        }
        return array($lst,$n);
    }

    function showCatalogueBrandSelectList($r,$code_search){$db=DbSingleton::getDb();$slave=new slave;$list="";
        $n=$db->num_rows($r);$tkey=time();
        $db->query("CREATE TEMPORARY TABLE IF NOT EXISTS `NBRAND_RESULT_$tkey` (`art_id` INT NOT NULL ,`display_nr` VARCHAR( 100 ) NOT NULL ,`name` VARCHAR( 255 ) NOT NULL ,`brand_id` INT NOT NULL ,`brand_name` VARCHAR( 100 ) NOT NULL ,`kol_res` TINYINT NOT NULL) ENGINE = MYISAM ;");
        for ($i=1;$i<=$n;$i++){
            $art_id=$db->result($r,$i-1,"ART_ID");
            $display_nr=$db->result($r,$i-1,"DISPLAY_NR");
            $name=$slave->qq($db->result($r,$i-1,"NAME"));
            $brand_id=$db->result($r,$i-1,"BRAND_ID");
            $brand_name=$slave->qq($db->result($r,$i-1,"BRAND_NAME"));
            $kol_res=0;
            //$kol_res=$this->countCatalogueBrandSelectItems($code_search,$brand_id);
            $db->query("insert into `NBRAND_RESULT_$tkey` values ('$art_id','$display_nr','$name','$brand_id','$brand_name','$kol_res');");
        }

        $r=$db->query("select * from `NBRAND_RESULT_$tkey` order by `kol_res` desc;");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $art_id=$db->result($r,$i-1,"art_id");
            $display_nr=$db->result($r,$i-1,"display_nr");
            $display_nr=str_replace(" ","",$display_nr);
            $name=$db->result($r,$i-1,"name");
            $brand_id=$db->result($r,$i-1,"brand_id");
            $brand_name=$db->result($r,$i-1,"brand_name");
            $brand_name=str_replace(" ","",$brand_name);
            $display_nr2=str_replace("/","--",$display_nr);
            $trans_display=str_replace(" ","-",$name);
            $trans_display=str_replace('"',"-",$trans_display);
            $list.="<tr style='cursor:pointer;' onClick='location.href=\"/Catalogue/$display_nr2/$brand_id/$art_id/$display_nr-$brand_name-$trans_display\"'>
                <td>$display_nr</td>
                <td>$brand_name</td>
                <td>$name</td>
            </tr>";
        }
        $form="";
        if ($n>0){
            $form_htm=RD."/tpl/catalogue_brand_select_list.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
            $form=str_replace("{list}",$list,$form);
        }
        $db->query("DROP TEMPORARY TABLE IF EXISTS `NBRAND_RESULT_$tkey`;");
        return $form;
    }

    function showArticlesSearchList($art,$query_2,$search_type=null){$db=DbSingleton::getTokoDb();session_start();$user_id=$_SESSION["media_user_id"];;
        $list2="";$ak=$rk=[];$r="";$query="";
        if ($query_2=="" && $search_type==0){$n=0;
            $link=gnLink;
            if (substr($link,-1)=="/"){$link=substr($link,0,strlen($link)-1);}
            $links=explode("/", $link);
            $art=$this->clearArticle($art);$brand_id=$links[2];

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
            if ($n>1 && $brand_id==""){ $where_brand="";
                $list2=$this->showCatalogueBrandSelectList($r,$art);
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

                $query="select t2a.ART_ID,t2a.BRAND_ID,t2a.ARTICLE_NR_DISPL, t2b.BRAND_NAME, t2n.NAME, t2n.INFO, t2bc.BARCODE, t2ic.CROSS as inner_cross, gg.NAME as goods_group_name, u.name as unit_name, cs.COSTUMS_CODE, cc.COUNTRY_NAME
                from T2_ARTICLES t2a 
                    left outer join T2_BRANDS t2b on t2b.BRAND_ID=t2a.BRAND_ID 
                    left outer join T2_NAMES t2n on t2n.ART_ID=t2a.ART_ID 
                    left outer join T2_BARCODES t2bc on t2bc.ART_ID=t2a.ART_ID 
                    left outer join T2_INNER_CROSS t2ic on t2ic.ART_ID=t2a.ART_ID 
                    left outer join T2_GOODS_GROUP t2gg on t2gg.ART_ID=t2a.ART_ID 
                    left outer join GOODS_GROUP gg on gg.ID=t2gg.GOODS_GROUP_ID 
                    left outer join T2_PACKAGING t2p on t2p.ART_ID=t2a.ART_ID 
                    left outer join units u on u.id=t2p.UNITS_ID 
                    left outer join T2_ZED t2z on t2z.ART_ID=t2a.ART_ID 
                    left outer join T2_COUNTRIES cc on cc.COUNTRY_ID=t2z.COUNTRY_ID 
                    left outer join T2_COSTUMS cs on cs.COSTUMS_ID=t2z.COSTUMS_ID 	
                where t2a.ART_ID in ($art_id_str)";
            }
        }
        if ($query_2=="" && $search_type==1){
            $query="select t2a.ART_ID,t2a.BRAND_ID,t2a.ARTICLE_NR_DISPL, t2b.BRAND_NAME, t2n.NAME, t2n.INFO, t2bc.BARCODE, t2ic.CROSS as inner_cross, gg.NAME as goods_group_name, u.name as unit_name, cs.COSTUMS_CODE, cc.COUNTRY_NAME
            from T2_ARTICLES t2a 
                left outer join T2_BRANDS t2b on t2b.BRAND_ID=t2a.BRAND_ID 
                left outer join T2_NAMES t2n on t2n.ART_ID=t2a.ART_ID 
                left outer join T2_BARCODES t2bc on t2bc.ART_ID=t2a.ART_ID 
                left outer join T2_INNER_CROSS t2ic on t2ic.ART_ID=t2a.ART_ID 
                left outer join T2_GOODS_GROUP t2gg on t2gg.ART_ID=t2a.ART_ID 
                left outer join GOODS_GROUP gg on gg.ID=t2gg.GOODS_GROUP_ID 
                left outer join T2_PACKAGING t2p on t2p.ART_ID=t2a.ART_ID 
                left outer join units u on u.id=t2p.UNITS_ID 
                left outer join T2_ZED t2z on t2z.ART_ID=t2a.ART_ID 
                left outer join T2_COUNTRIES cc on cc.COUNTRY_ID=t2z.COUNTRY_ID 
                left outer join T2_COSTUMS cs on cs.COSTUMS_ID=t2z.COSTUMS_ID 
            where t2a.ARTICLE_NR_SEARCH='$art' or t2a.ARTICLE_NR_DISPL='$art';";
        }
        if ($query_2=="" && $search_type==2){
            $query="select t2a.ART_ID,t2a.BRAND_ID,t2a.ARTICLE_NR_DISPL, t2b.BRAND_NAME, t2n.NAME, t2n.INFO, t2bc.BARCODE, t2ic.CROSS as inner_cross, gg.NAME as goods_group_name, u.name as unit_name, cs.COSTUMS_CODE, cc.COUNTRY_NAME
            from T2_ARTICLES t2a 
                left outer join T2_BRANDS t2b on t2b.BRAND_ID=t2a.BRAND_ID 
                left outer join T2_NAMES t2n on t2n.ART_ID=t2a.ART_ID 
                left outer join T2_BARCODES t2bc on t2bc.ART_ID=t2a.ART_ID 
                left outer join T2_INNER_CROSS t2ic on t2ic.ART_ID=t2a.ART_ID 
                left outer join T2_GOODS_GROUP t2gg on t2gg.ART_ID=t2a.ART_ID 
                left outer join GOODS_GROUP gg on gg.ID=t2gg.GOODS_GROUP_ID 
                left outer join T2_PACKAGING t2p on t2p.ART_ID=t2a.ART_ID 
                left outer join units u on u.id=t2p.UNITS_ID 
                left outer join T2_ZED t2z on t2z.ART_ID=t2a.ART_ID 
                left outer join T2_COUNTRIES cc on cc.COUNTRY_ID=t2z.COUNTRY_ID 
                left outer join T2_COSTUMS cs on cs.COSTUMS_ID=t2z.COSTUMS_ID 
            where t2bc.BARCODE='$art';";
        }
        if ($query_2=="" && $search_type==3){
            $query="select t2a.ART_ID,t2a.BRAND_ID,t2a.ARTICLE_NR_DISPL, t2b.BRAND_NAME, t2n.NAME, t2n.INFO, t2bc.BARCODE, t2ic.CROSS as inner_cross, gg.NAME as goods_group_name, u.name as unit_name, cs.COSTUMS_CODE, cc.COUNTRY_NAME
            from T2_ARTICLES t2a 
                left outer join T2_BRANDS t2b on t2b.BRAND_ID=t2a.BRAND_ID 
                left outer join T2_NAMES t2n on t2n.ART_ID=t2a.ART_ID 
                left outer join T2_BARCODES t2bc on t2bc.ART_ID=t2a.ART_ID 
                left outer join T2_INNER_CROSS t2ic on t2ic.ART_ID=t2a.ART_ID 
                left outer join T2_GOODS_GROUP t2gg on t2gg.ART_ID=t2a.ART_ID 
                left outer join GOODS_GROUP gg on gg.ID=t2gg.GOODS_GROUP_ID 
                left outer join T2_PACKAGING t2p on t2p.ART_ID=t2a.ART_ID 
                left outer join units u on u.id=t2p.UNITS_ID 
                left outer join T2_ZED t2z on t2z.ART_ID=t2a.ART_ID 
                left outer join T2_COUNTRIES cc on cc.COUNTRY_ID=t2z.COUNTRY_ID 
                left outer join T2_COSTUMS cs on cs.COSTUMS_ID=t2z.COSTUMS_ID 
            where t2a.ART_ID='$art';";
        }
        if ($query_2!=""){$query=$query_2;}
        $r=$db->query($query);$n=$db->num_rows($r);$list="";$header_list="";$range_list="";
        if ($query_2!="" || $list2==""){  // сработал внешний фильр или основной поиск с выбором бренда
            list($fldcnf,$kol_f)=$this->getCatalogueClientViewFieldsData($user_id,"catalogue");
            for ($i=1;$i<=$kol_f;$i++){
                $header_list.="<th>".$fldcnf[$i]["field_name"]."</th>";
                $range_list.="<td onClick='showCatalogueCard(\"{art_id}\")'>{".$fldcnf[$i]["field_key"]."}</td>";
            }
            $header_list="<tr align='center'><th data-sortable=\"false\">Фото</th><th data-sortable=\"false\">Тип артикула</th>".$header_list."</tr>";
            $lst=array();

            for ($i=1;$i<=$n;$i++){
                $art_id=$db->result($r,$i-1,"ART_ID");
                $kind_id=$ak[$art_id];
                $relation=$rk[$art_id];
                $article_nr_displ=$db->result($r,$i-1,"ARTICLE_NR_DISPL");
                $brand_name=$db->result($r,$i-1,"BRAND_NAME");
                $name=$db->result($r,$i-1,"NAME");
                $info=$db->result($r,$i-1,"INFO");
                $barcode=$db->result($r,$i-1,"BARCODE");
                $inner_cross=$db->result($r,$i-1,"inner_cross");
                $goods_group_name=$db->result($r,$i-1,"goods_group_name");
                $unit_name=$db->result($r,$i-1,"unit_name");
                $costums_code=$db->result($r,$i-1,"COSTUMS_CODE");
                $country_name=$db->result($r,$i-1,"COUNTRY_NAME");
                $lst[$i]["kind"]=$kind_id;
                $lst[$i]["relation"]=$relation;
                /*
                $kind_name="";
                if ($kind_id==0 && $relation==0){ $kind_name="запитаний артикул"; }
                if ($kind_id>0 && $relation==0){ $kind_name="аналог"; }
                if (($kind_id==3 || $kind_id==4) && $relation==1){ $kind_name="артикул присутні в"; }
                if (($kind_id==3 || $kind_id==4) && $relation==2){ $kind_name="артикул включає в себе"; }
                if ($kind_id=="" || $relation==""){$kind_name="інше";}
                */
                $check_photo=$this->checkPhotoEmpty($art_id,$article_nr_displ);
                if ($check_photo>0) {
                $lst[$i]["data"]="<tr style='cursor:pointer'>
                    <td class='text-center'><button class='btn btn-sm btn-default' onclick='showArtilceGallery(\"$art_id\",\"$article_nr_displ\")'><i class='fa fa-image'></i></button></td>
                    <td class='text-center'>{kind_name}</td>
                    ".$range_list."</tr>";} else {
                    $lst[$i]["data"]="<tr style='cursor:pointer'>
                    <td class='text-center'></td>
                    <td class='text-center'>{kind_name}</td>
                    ".$range_list."</tr>";
                }
                $lst[$i]["data"]=str_replace("{art_id}",$art_id,$lst[$i]["data"]);
                $lst[$i]["data"]=str_replace("{article_nr_displ}",$article_nr_displ,$lst[$i]["data"]);
                $lst[$i]["data"]=str_replace("{brand_name}",$brand_name,$lst[$i]["data"]);
                $lst[$i]["data"]=str_replace("{name}",$name,$lst[$i]["data"]);
                $lst[$i]["data"]=str_replace("{info}",$info,$lst[$i]["data"]);
                $lst[$i]["data"]=str_replace("{barcode}",$barcode,$lst[$i]["data"]);
                $lst[$i]["data"]=str_replace("{art_id}",$art_id,$lst[$i]["data"]);
                $lst[$i]["data"]=str_replace("{inner_cross}",$inner_cross,$lst[$i]["data"]);
                $lst[$i]["data"]=str_replace("{goods_group_id}",$goods_group_name,$lst[$i]["data"]);
                $lst[$i]["data"]=str_replace("{units_id}",$unit_name,$lst[$i]["data"]);
                $lst[$i]["data"]=str_replace("{costums_id}",$costums_code,$lst[$i]["data"]);
                $lst[$i]["data"]=str_replace("{country_id}",$country_name,$lst[$i]["data"]);
            }
            $lst_kr=array();
            for ($i=1;$i<=$n;$i++){
                $kind=$lst[$i]["kind"];
                $relation=$lst[$i]["relation"];
                if ($kind==0 && $relation==0){ $lst_kr[1].=$lst[$i]["data"]; }
                if ($kind==1 && $relation==0){ $lst_kr[2].=$lst[$i]["data"]; }
                if (($kind==3 || $kind==4) && $relation==0){ $lst_kr[2].=$lst[$i]["data"]; }
                if (($kind==3 || $kind==4) && $relation==1){ $lst_kr[3].=$lst[$i]["data"]; }
                if (($kind==3 || $kind==4) && $relation==2){ $lst_kr[4].=$lst[$i]["data"]; }
                if ($kind=="" || $relation==""){$lst_kr[5].=$lst[$i]["data"];}

            }
            if ($lst_kr[1]!=""){$lst_kr[1]=str_replace("{kind_name}","<i style=\"width: 100%;height: 60px;\" title=\"запитаний артикул\" class=\"fa fa-key\"></i>",$lst_kr[1]);$list.=$lst_kr[1];}
            if ($lst_kr[2]!=""){$lst_kr[2]=str_replace("{kind_name}","<i style=\"width: 100%;height: 60px;\" title=\"аналог\" class=\"fa fa-link\"></i>",$lst_kr[2]);$list.=$lst_kr[2];}
            if ($lst_kr[3]!=""){$lst_kr[3]=str_replace("{kind_name}","<i style=\"width: 100%;height: 60px;\" title=\"артикул присутні в \" class=\"fa fa-level-down\"></i>",$lst_kr[3]);$list.=$lst_kr[3];}
            if ($lst_kr[4]!=""){$lst_kr[4]=str_replace("{kind_name}","<i style=\"width: 100%;height: 60px;\" title=\"артикул включає в себе\" class=\"fa fa-level-up\"></i>",$lst_kr[4]);$list.=$lst_kr[4];}
            if ($lst_kr[5]!=""){$lst_kr[5]=str_replace("{kind_name}","<i style=\"width: 100%;height: 60px;\" title=\"інше\" class=\"fa fa-ellipsis-h\"></i>",$lst_kr[5]);$list.=$lst_kr[5];}

            /*if ($lst_kr[1]!=""){$list.="".$lst_kr[1];}
            if ($lst_kr[2]!=""){$list.="".$lst_kr[2];}
            if ($lst_kr[3]!=""){$list.="".$lst_kr[3];}
            if ($lst_kr[4]!=""){$list.="".$lst_kr[4];}
            if ($lst_kr[5]!=""){$list=$lst_kr[5];}*/
        }
        return array($header_list,$list,$list2);
    }

    function getGoodsGroupName($gg_id){$db=DbSingleton::getTokoDb(); $name="";
        $r=$db->query ("select NAME from GOODS_GROUP where ID='$gg_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$name=$db->result($r,0,"NAME");}
        return $name;
    }

    function listSubGoodsGroup($gg_id){$db=DbSingleton::getTokoDb(); $list="";
        $r=$db->query ("select ID from GOODS_GROUP where PARRENT_ID='$gg_id' order by `KEY`,`ID` asc;");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"ID");
            $list.="'$id',".$this->listSubGoodsGroup($id);
        }
        return $list;
    }

    function showArticlesFil4SearchList($suppl_id,$brand_id,$goods_group_id,$top,$stok_to,$stok_from){
        $where_brand="";
        if ($brand_id!=""){
            foreach($brand_id as $brnd_id){
                $where_brand.="'$brnd_id',";
            }$where_brand=" and t2a.BRAND_ID in (".substr($where_brand,0,-1).") ";
        }
        $where_goods_group="";
        if ($goods_group_id!=""){
            foreach($goods_group_id as $gg_id){
                $where_goods_group.="'$gg_id',".$this->listSubGoodsGroup($gg_id);

            }$where_goods_group=" and t2gg.GOODS_GROUP_ID in (".substr($where_goods_group,0,-1).") ";
        }

        $query="select t2a.ART_ID,t2a.BRAND_ID,t2a.ARTICLE_NR_DISPL, t2b.BRAND_NAME, t2n.NAME, t2n.INFO, t2bc.BARCODE, t2ic.CROSS as inner_cross, gg.NAME as goods_group_name, u.name as unit_name, cs.COSTUMS_CODE, cc.COUNTRY_NAME
        from T2_ARTICLES t2a 
            left outer join T2_BRANDS t2b on t2b.BRAND_ID=t2a.BRAND_ID 
            left outer join T2_NAMES t2n on t2n.ART_ID=t2a.ART_ID 
            left outer join T2_BARCODES t2bc on t2bc.ART_ID=t2a.ART_ID 
            left outer join T2_INNER_CROSS t2ic on t2ic.ART_ID=t2a.ART_ID 
            left outer join T2_GOODS_GROUP t2gg on t2gg.ART_ID=t2a.ART_ID 
            left outer join GOODS_GROUP gg on gg.ID=t2gg.GOODS_GROUP_ID 
            left outer join T2_PACKAGING t2p on t2p.ART_ID=t2a.ART_ID 
            left outer join units u on u.id=t2p.UNITS_ID 
            left outer join T2_ZED t2z on t2z.ART_ID=t2a.ART_ID 
            left outer join T2_COUNTRIES cc on cc.COUNTRY_ID=t2z.COUNTRY_ID 
            left outer join T2_COSTUMS cs on cs.COSTUMS_ID=t2z.COSTUMS_ID 
        where 1 $where_brand $where_goods_group limit 0,3000;";

        list($header_list,$list,$list_brand_select)=$this->showArticlesSearchList("",$query);
        return array($header_list,$list,$list_brand_select);
    }

    function showArticlesFil2SearchList($mfa_id,$mod_id,$typ_id,$str_id,$art_ids){
        $where_artds="";
        if ($art_ids!=""){
            $where_artds=" and t2a.ART_ID in (".$art_ids.") ";
        }
        $query="select t2a.ART_ID, t2a.BRAND_ID, t2a.ARTICLE_NR_DISPL, t2b.BRAND_NAME, t2n.NAME, t2n.INFO, t2bc.BARCODE, t2ic.CROSS as inner_cross, gg.NAME as goods_group_name, u.name as unit_name, cs.COSTUMS_CODE, cc.COUNTRY_NAME
        from T2_ARTICLES t2a 
            left outer join T2_BRANDS t2b on t2b.BRAND_ID=t2a.BRAND_ID 
            left outer join T2_NAMES t2n on t2n.ART_ID=t2a.ART_ID 
            left outer join T2_BARCODES t2bc on t2bc.ART_ID=t2a.ART_ID 
            left outer join T2_INNER_CROSS t2ic on t2ic.ART_ID=t2a.ART_ID 
            left outer join T2_GOODS_GROUP t2gg on t2gg.ART_ID=t2a.ART_ID 
            left outer join GOODS_GROUP gg on gg.ID=t2gg.GOODS_GROUP_ID 
            left outer join T2_PACKAGING t2p on t2p.ART_ID=t2a.ART_ID 
            left outer join units u on u.id=t2p.UNITS_ID 
            left outer join T2_ZED t2z on t2z.ART_ID=t2a.ART_ID 
            left outer join T2_COUNTRIES cc on cc.COUNTRY_ID=t2z.COUNTRY_ID 
            left outer join T2_COSTUMS cs on cs.COSTUMS_ID=t2z.COSTUMS_ID 
        where 1 $where_artds limit 0,3000;";
        list($header_list,$list,$list_brand_select)=$this->showArticlesSearchList("",$query);
        return array($header_list,$list,$list_brand_select);
    }

    function getArticleOperPriceGeneralStock($art_id){$db=DbSingleton::getTokoDb();$oper_price=0;$general_stock=0;
        $r=$db->query("select * from T2_ARTICLES_PRICE_STOCK where ART_ID='$art_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){ $oper_price=$db->result($r,0,"OPER_PRICE"); $general_stock=$db->result($r,0,"GENERAL_STOCK"); }
        return array($oper_price,$general_stock);
    }

    function setArticleOperPriceGeneralStock($art_id,$new_oper_price,$new_general_stock){$db=DbSingleton::getTokoDb();
        $r=$db->query("select * from T2_ARTICLES_PRICE_STOCK where ART_ID='$art_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){ $db->query("update T2_ARTICLES_PRICE_STOCK set `OPER_PRICE`='$new_oper_price', `GENERAL_STOCK`='$new_general_stock' where ART_ID='$art_id';"); }
        if ($n==0){ $db->query("insert into T2_ARTICLES_PRICE_STOCK (`ART_ID`,`OPER_PRICE`,`GENERAL_STOCK`) values ('$art_id','$new_oper_price','$new_general_stock');"); }
        return;
    }

    function getArticleNrDisplBrand($art_id){$db=DbSingleton::getTokoDb();$article_nr_displ=$article_nr_search=$brand_name="";$brand_id=0;
        $r=$db->query("select t2a.ART_ID,t2a.BRAND_ID,t2a.ARTICLE_NR_DISPL,t2a.ARTICLE_NR_SEARCH,t2b.BRAND_NAME 
        from T2_ARTICLES t2a  
            left outer join T2_BRANDS t2b on t2b.BRAND_ID=t2a.BRAND_ID 
        where t2a.ART_ID='$art_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $brand_id=$db->result($r,0,"BRAND_ID");
            $article_nr_displ=$db->result($r,0,"ARTICLE_NR_DISPL");
            $brand_name=$db->result($r,0,"BRAND_NAME");
            $article_nr_search=$db->result($r,0,"ARTICLE_NR_SEARCH");
        }
        return array($article_nr_displ,$brand_id,$brand_name,$article_nr_search);
    }

    function getArticleName($art_id){$db=DbSingleton::getTokoDb();$name="";
        $r=$db->query("select NAME from T2_NAMES where ART_ID='$art_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){ $name=$db->result($r,0,"NAME"); }
        return $name;
    }

    function getArticleNameLang($art_id){$db=DbSingleton::getTokoDb();$name="";
        $r=$db->query("select NAME from T2_NAMES where ART_ID='$art_id' and LANG_ID='41' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){ $name=$db->result($r,0,"NAME"); } else {
            $r=$db->query("select NAME from T2_NAMES where ART_ID='$art_id' and LANG_ID='16' limit 0,1;");$n=$db->num_rows($r);
            if ($n==1){ $name=$db->result($r,0,"NAME"); }
        }
        return $name;
    }

    function getArtNameUkr($art_id) {$db=DbSingleton::getTokoDb();$name="";
        $r=$db->query("select NAME from T2_NAMES where ART_ID='$art_id' and LANG_ID='41' limit 1;");$n=$db->num_rows($r);
        if ($n==1){ $name=$db->result($r,0,"NAME");}
        return $name;
    }

    function getUniqueNumber($art_id) { $db=DbSingleton::getTokoDb();
        $r=$db->query("select UNIV_NUMBER from T2_ARTICLES_UNIV_NUMBER where ART_ID='$art_id' limit 1;");
        $number=$db->result($r,0,"UNIV_NUMBER");
        return $number;
    }

    function showCatalogueCard($art_id){$db=DbSingleton::getTokoDb();session_start();$user_id=$_SESSION["media_user_id"];$user_name=$_SESSION["user_name"];$article_nr_displ="";
        $form="";$form_htm=RD."/tpl/catalogue_card.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select t2a.ART_ID,t2a.BRAND_ID,t2a.ARTICLE_NR_DISPL, t2n.NAME, t2n.INFO, t2bc.BARCODE, t2ic.CROSS as INNER_CROSS, t2gg.GOODS_GROUP_ID, gg.NAME as GOODS_GROUP_NAME, IFNULL(t2ps.OPER_PRICE,0) as OPER_PRICE, IFNULL(t2ps.GENERAL_STOCK,0) as GENERAL_STOCK
        from T2_ARTICLES t2a 
            left outer join T2_NAMES t2n on t2n.ART_ID=t2a.ART_ID 
            left outer join T2_BARCODES t2bc on t2bc.ART_ID=t2a.ART_ID 
            left outer join T2_INNER_CROSS t2ic on t2ic.ART_ID=t2a.ART_ID 
            left outer join T2_GOODS_GROUP t2gg on t2gg.ART_ID=t2a.ART_ID 
            left outer join GOODS_GROUP gg on gg.ID=t2gg.GOODS_GROUP_ID 
            left outer join T2_ARTICLES_PRICE_STOCK t2ps on t2ps.ART_ID=t2a.ART_ID 
        where t2a.ART_ID='$art_id' limit 0,1;"); $n=$db->num_rows($r);

        if ($n==0){$form_htm=RD."/tpl/access_deny.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);} }
        if ($n==1){
            $art_id=$db->result($r,0,"ART_ID");
            $article_nr_displ=$db->result($r,0,"ARTICLE_NR_DISPL");
            $brand_id=$db->result($r,0,"BRAND_ID");
            $article_name=$db->result($r,0,"NAME");
            $article_info=$db->result($r,0,"INFO");
            $barcode=$db->result($r,0,"BARCODE");
            $inner_cross=$db->result($r,0,"INNER_CROSS");
            $goods_group_id=$db->result($r,0,"GOODS_GROUP_ID");
            $goods_group_name=$db->result($r,0,"GOODS_GROUP_NAME");
            $oper_price=$db->result($r,0,"OPER_PRICE");
            $general_stock=$db->result($r,0,"GENERAL_STOCK");
            $unnumber=$this->getUniqueNumber($art_id);
            $article_name_ukr=$this->getArtNameUkr($art_id);

            $form=str_replace("{art_id}",$art_id,$form);
            $form=str_replace("{article_nr_displ}",$article_nr_displ,$form);
            $form=str_replace("{barcode}",$barcode,$form);
            $form=str_replace("{inner_cross}",$inner_cross,$form);
            $form=str_replace("{brand_id}",$brand_id,$form);
            $form=str_replace("{brand_list}",$this->showBrandListSelect($brand_id),$form);
            $form=str_replace("{goods_group_id}",$goods_group_id,$form);
            $form=str_replace("{goods_group_name}",$goods_group_name,$form);
            $form=str_replace("{article_name}",$article_name,$form);
            $form=str_replace("{article_name_ukr}",$article_name_ukr,$form);
            $form=str_replace("{article_info}",$article_info,$form);
            $form=str_replace("{general_stock}",$general_stock,$form);
            $form=str_replace("{oper_price}",$oper_price,$form);
            $form=str_replace("{my_user_id}",$user_id,$form);
            $form=str_replace("{my_user_name}",$user_name,$form);
            $form=str_replace("{unique_number}",$unnumber,$form);
        }
        return array($form,$article_nr_displ);
    }

    function showArticleCross($art_id) {$db=DbSingleton::getTokoDb();$list="";
        $r=$db->query("select `CROSS` from `T2_INNER_CROSS` where `ART_ID`='$art_id' limit 1;");
        $cross=$db->result($r,0,"CROSS");
        $form="";$form_htm=RD."/tpl/catalogue_cross_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select `ART_ID` from `T2_INNER_CROSS` where `CROSS`='$cross';"); $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $art_id=$db->result($r,$i-1,"ART_ID");
            list($article_nr_displ,$brand_id,$brand_name)=$this->getArticleNrDisplBrand($art_id);
            list($amount, $reserv_amount)=$this->getArticlesStorage($art_id);
            $list.="<tr>
                <td>$i</td>
                <td>$art_id</td>
                <td>$article_nr_displ</td>
                <td>$brand_name</td>
                <td>$amount</td>
                <td>$reserv_amount</td>
            </tr>";
        }
        $form=str_replace("{cross_range}",$list,$form);
        $form=str_replace("{cross_value}",$cross,$form);
        return $form;
    }

    function saveArticleCross($cross,$new_cross) {$db=DbSingleton::getTokoDb();
        $answer=0;$err="Помилка збереження даних!";
        if ($cross!="") {
            $db->query("update `T2_INNER_CROSS` set `CROSS`='$new_cross' where `CROSS`='$cross';");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function getArticlesStorage($art_id) {$db=DbSingleton::getTokoDb(); $full_amount=$full_reserv_amount=0;
        $r=$db->query("select * from `T2_ARTICLES_STRORAGE` where `ART_ID`='$art_id';"); $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $AMOUNT=$db->result($r,$i-1,"AMOUNT");
            $RESERV_AMOUNT=$db->result($r,$i-1,"RESERV_AMOUNT");
            $full_amount+=$AMOUNT;
            $full_reserv_amount+=$RESERV_AMOUNT;
        }
        return array($full_amount, $full_reserv_amount);
    }

    function saveCatalogueGeneralInfo($art_id,$article_nr_displ,$barcode,$inner_cross,$brand_id,$goods_group_id,$article_name,$article_info,$article_name_ukr,$unique_number){$db=DbSingleton::getTokoDb();$slave=new slave;session_start();$user_id=$_SESSION["media_user_id"];$answer=0;$err="Помилка збереження даних!";
        $art_id=$slave->qq($art_id);$article_nr_displ=$slave->qq($article_nr_displ);$barcode=$slave->qq($barcode);$inner_cross=$slave->qq($inner_cross);$brand_id=$slave->qq($brand_id);$goods_group_id=$slave->qq($goods_group_id);$article_name=$slave->qq($article_name);$article_info=$slave->qq($article_info);
        if ($art_id>0){
            $article_nr_search=$this->clearArticle($article_nr_displ);
            $r=$db->query("select ARTICLE_NR_DISPL,BRAND_ID from T2_ARTICLES where `ART_ID`='$art_id' limit 1;");
            $ARTICLE_NR_DISPL_old=$db->result($r,0,"ARTICLE_NR_DISPL");
            $BRAND_ID_old=$db->result($r,0,"BRAND_ID");
            $r=$db->query("select `NAME` from `T2_NAMES` where `ART_ID`='$art_id' and `LANG_ID`='16' limit 1;");
            $NAME_old=$db->result($r,0,"NAME");
            $r=$db->query("select `NAME` from `T2_NAMES` where `ART_ID`='$art_id' and `LANG_ID`='41' limit 1;");
            $NAME_41_old=$db->result($r,0,"NAME");

            $db->query("update T2_ARTICLES set `ARTICLE_NR_DISPL`='$article_nr_displ', `ARTICLE_NR_SEARCH`='$article_nr_search', `BRAND_ID`='$brand_id' where `ART_ID`='$art_id';");

            //BARCODE UPDATE
            $r=$db->query("select `BARCODE` from `T2_BARCODES` where `ART_ID`='$art_id' limit 0,1;");$n=$db->num_rows($r);
            if ($n==0){
                $db->query("insert into T2_BARCODES (`ART_ID`,`BARCODE`) values ('$art_id','$barcode');");
            }
            if ($n==1){
                $barcode_db=$db->result($r,0,"BARCODE");
                if ($barcode_db!=$barcode){
                    $db->query("update T2_BARCODES set `BARCODE`='$barcode' where `ART_ID`='$art_id';");
                }
            }

            //T2_GOODS_GROUP UPDATE
            $r=$db->query("select `GOODS_GROUP_ID` from `T2_GOODS_GROUP` where `ART_ID`='$art_id' limit 0,1;");$n=$db->num_rows($r);
            if ($n==0){
                $db->query("insert into T2_GOODS_GROUP (`ART_ID`,`GOODS_GROUP_ID`) values ('$art_id','$goods_group_id');");
            }
            if ($n==1){
                $goods_group_id_db=$db->result($r,0,"GOODS_GROUP_ID");
                if ($goods_group_id_db!=$goods_group_id){
                    $db->query("update T2_GOODS_GROUP set `GOODS_GROUP_ID`='$goods_group_id' where `ART_ID`='$art_id';");
                }
            }

            //T2_INNER_CROSS UPDATE
            $r=$db->query("select `CROSS` from `T2_INNER_CROSS` where `ART_ID`='$art_id' limit 0,1;");$n=$db->num_rows($r);
            $inner_cross_old=$db->result($r,0,"CROSS");
            if ($n==0){
                $db->query("insert into T2_INNER_CROSS (`ART_ID`,`CROSS`) values ('$art_id','$inner_cross');");
            }
            if ($n==1){
                $inner_cross_db=$db->result($r,0,"CROSS");
                if ($inner_cross_db!=$inner_cross){
                    $db->query("update T2_INNER_CROSS set `CROSS`='$inner_cross' where `ART_ID`='$art_id';");
                }
            }

            //T2_ARTICLES_UNIV_NUMBER
            $r=$db->query("select `UNIV_NUMBER` from `T2_ARTICLES_UNIV_NUMBER` where `ART_ID`='$art_id' limit 1;");$n=$db->num_rows($r);
            $unique_number_old=$db->result($r,0,"UNIV_NUMBER");
            if ($n==0){
                $db->query("insert into T2_ARTICLES_UNIV_NUMBER (`ART_ID`,`UNIV_NUMBER`) values ('$art_id','$unique_number');");
            }
            if ($n==1){
                $unique_number_db=$db->result($r,0,"UNIV_NUMBER");
                if ($unique_number_db!=$unique_number){
                    $db->query("update T2_ARTICLES_UNIV_NUMBER set `UNIV_NUMBER`='$unique_number' where `ART_ID`='$art_id';");
                }
            }

            //T2_INNER_CROSS UPDATE
            $r=$db->query("select `NAME`, `INFO` from `T2_NAMES` where `ART_ID`='$art_id' and `LANG_ID`='16' limit 0,1;");$n=$db->num_rows($r);
            if ($n==0){
                $db->query("insert into T2_NAMES (`ART_ID`,`LANG_ID`,`NAME`,`INFO`) values ('$art_id','16','$article_name','$article_info');");
            }
            if ($n==1){
                $article_name_db=$db->result($r,0,"NAME");
                $article_info_db=$db->result($r,0,"INFO");
                if ($article_name_db!=$article_name || $article_info_db!=$article_info){
                    $db->query("update T2_NAMES set `NAME`='$article_name', `INFO`='$article_info' where `ART_ID`='$art_id' and `LANG_ID`='16';");
                }
            }

            //T2_INNER_CROSS UPDATE 41
            $r=$db->query("select `NAME`, `INFO` from `T2_NAMES` where `ART_ID`='$art_id' and `LANG_ID`='41' limit 0,1;");$n=$db->num_rows($r);
            if ($n==0){
                $db->query("insert into T2_NAMES (`ART_ID`,`LANG_ID`,`NAME`,`INFO`) values ('$art_id','41','$article_name_ukr','$article_info');");
            }
            if ($n==1){
                $article_name_db=$db->result($r,0,"NAME");
                $article_info_db=$db->result($r,0,"INFO");
                if ($article_name_db!=$article_name_ukr || $article_info_db!=$article_info){
                    $db->query("update T2_NAMES set `NAME`='$article_name_ukr', `INFO`='$article_info' where `ART_ID`='$art_id' and `LANG_ID`='41';");
                }
            }

            //Add history - T2_ARTICLES_LOGS
            $db->query("insert into T2_ARTICLES_LOGS (`user_id`,`art_id`,`article_nr_displ`,`brand_id`,`article_name_ru`,`article_name_ua`,`inner_cross`,`unique_number`,`article_nr_displ_old`,`brand_id_old`,`article_name_ru_old`,`article_name_ua_old`,`inner_cross_old`,`unique_number_old`) values ('$user_id','$art_id','$article_nr_displ','$brand_id','$article_name','$article_name_ukr','$inner_cross','$unique_number','$ARTICLE_NR_DISPL_old','$BRAND_ID_old','$NAME_old','$NAME_41_old','$inner_cross_old','$unique_number_old');");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function showArticleLogs($art_id) {$db=DbSingleton::getTokoDb(); $list="";
        $form="";$form_htm=RD."/tpl/catalogue_article_logs.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select * from T2_ARTICLES_LOGS where art_id='$art_id';"); $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $data=$db->result($r,$i-1,"data");
            $user_id=$db->result($r,$i-1,"user_id"); $user_name=$this->getMediaUserName($user_id);
            $article_nr_displ=$db->result($r,$i-1,"article_nr_displ");
            $brand_id=$db->result($r,$i-1,"brand_id"); $brand_name=$this->getBrandName($brand_id);
            $article_name=$db->result($r,$i-1,"article_name_ru");
            $article_name_41=$db->result($r,$i-1,"article_name_ua");
            $inner_cross=$db->result($r,$i-1,"inner_cross");
            $unique_number=$db->result($r,$i-1,"unique_number");
            $article_nr_displ_old=$db->result($r,$i-1,"article_nr_displ_old");
            $brand_id_old=$db->result($r,$i-1,"brand_id_old"); $brand_name_old=$this->getBrandName($brand_id_old);
            $article_name_old=$db->result($r,$i-1,"article_name_ru_old");
            $article_name_41_old=$db->result($r,$i-1,"article_name_ua_old");
            $inner_cross_old=$db->result($r,$i-1,"inner_cross_old");
            $unique_number_old=$db->result($r,$i-1,"unique_number_old");

            if ($article_nr_displ_old==$article_nr_displ) $style_displ=""; else $style_displ="style='background:pink;'";
            if ($brand_name_old==$brand_name) $style_brand=""; else $style_brand="style='background:pink;'";
            if ($article_name_old==$article_name) $style_name_ru=""; else $style_name_ru="style='background:pink;'";
            if ($article_name_41_old==$article_name_41) $style_name_ua=""; else $style_name_ua="style='background:pink;'";
            if ($inner_cross_old==$inner_cross) $style_cross=""; else $style_cross="style='background:pink;'";
            if ($unique_number_old==$unique_number) $style_unique=""; else $style_unique="style='background:pink;'";

            $list.="<tr>
                <td>$i</td>
                <td>$data</td>
                <td>$user_name</td>
                <td $style_displ>$article_nr_displ_old => $article_nr_displ</td>
                <td $style_brand>$brand_name_old => $brand_name</td>
                <td $style_name_ru>$article_name_old => $article_name</td>
                <td $style_name_ua>$article_name_41_old => $article_name_41</td>
                <td $style_cross>$inner_cross_old => $inner_cross</td>
                <td $style_unique>$unique_number_old => $unique_number</td>
            </tr>";
        }
        $form=str_replace("{article_logs_range}",$list,$form);
        return $form;
    }

    function clearArticle($art){
        $art=str_replace(" ","",$art);$art=str_replace("_","",$art);$art=str_replace("-","",$art);$art=str_replace(".","",$art);$art=str_replace("+","",$art);$art=str_replace("'","",$art);$art=str_replace("/","",$art);$art=str_replace('"',"",$art);$art=preg_replace ("/[^a-zA-ZА-Яа-я0-9\s]/","",$art);$art=strtolower($art);
        return $art;
    }

    function loadArticleCommets($art_id){$db=DbSingleton::getTokoDb();
        $form="";$form_htm=RD."/tpl/catalogue_comment_block.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select t2ac.* from T2_ARTICLES_COMMENTS t2ac where t2ac.ART_ID='$art_id' order by id desc;");$n=$db->num_rows($r);$list="";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"ID");
            $user_id=$db->result($r,$i-1,"USER_ID");
            $user_name=$this->getMediaUserName($user_id);
            $data=$db->result($r,$i-1,"DATA");
            $comment=$db->result($r,$i-1,"COMMENT");
            $block=$form;
            $block=str_replace("{art_id}",$art_id,$block);
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

    function saveArticleComment($art_id,$comment){$db=DbSingleton::getTokoDb();$slave=new slave;session_start();$user_id=$_SESSION["media_user_id"];
        $answer=0;$err="Помилка збереження даних!";
        $art_id=$slave->qq($art_id);$comment=$slave->qq($comment);
        if ($art_id>0 && $comment!=""){
            $db->query("insert into T2_ARTICLES_COMMENTS (`ART_ID`,`USER_ID`,`COMMENT`) values ('$art_id','$user_id','$comment');");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function dropArticleComment($art_id,$comment_id){$db=DbSingleton::getTokoDb();$slave=new slave;
        $answer=0;$err="Помилка видалення запису!";
        $art_id=$slave->qq($art_id);$comment_id=$slave->qq($comment_id);
        if ($art_id>0 && $comment_id>0){
            $r=$db->query("select * from T2_ARTICLES_COMMENTS where ART_ID='$art_id' and ID='$comment_id' limit 0,1;");$n=$db->num_rows($r);
            if ($n==1){
                $db->query("delete from T2_ARTICLES_COMMENTS where ART_ID='$art_id' and ID='$comment_id';");
                $answer=1;$err="";
            }
        }
        return array($answer,$err);
    }

    function getMediaUserName($user_id){$db=DbSingleton::getDb();$name="";
        $r=$db->query("select name from media_users where id='$user_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$name=$db->result($r,0,"name");}
        return $name;
    }

    function loadArticleCDN($art_id){$db=DbSingleton::getTokoDb();
        $form="";$form_htm=RD."/tpl/catalogue_cdn_block.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select t2ac.* from T2_ARTICLES_CDN t2ac 	where t2ac.ART_ID='$art_id' and t2ac.STATUS='1' order by t2ac.FILE_NAME asc;");$n=$db->num_rows($r);$list="";
        for ($i=1;$i<=$n;$i++){
            $file_id=$db->result($r,$i-1,"ID");
            $user_id=$db->result($r,$i-1,"USER_ID");
            $file_name=$db->result($r,$i-1,"FILE_NAME");
            $name=$db->result($r,$i-1,"NAME");
            $data=$db->result($r,$i-1,"DATA");
            $user_name=$this->getMediaUserName($user_id);
            $link="http://cdn.myparts.pro/artfiles/$art_id/$file_name";
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
            $block=str_replace("{art_id}",$art_id,$block);
            $block=str_replace("{link}",$link,$block);
            $block=str_replace("{file_view}",$file_view,$block);
            $list.=$block;
        }
        if ($n==0){$list="<h3 class='text-center'>Файли відсутні</h3>";}
        return $list;
    }

    function articlesCDNDropFile($art_id,$file_id){$db=DbSingleton::getTokoDb();$slave=new slave;
        $answer=0;$err="Помилка видалення файлу!";
        $art_id=$slave->qq($art_id);$file_id=$slave->qq($file_id);
        if ($art_id>0 && $file_id>0){
            $r=$db->query("select FILE_NAME from T2_ARTICLES_CDN where ART_ID='$art_id' and ID='$file_id' limit 0,1;");$n=$db->num_rows($r);
            if ($n==1){
                unlink(RD.'/cdn/artfiles/$art_id/$file_name');
                $db->query("delete from T2_ARTICLES_CDN where ART_ID='$art_id' and ID='$file_id';");
                $answer=1;$err="";
            }
        }
        return array($answer,$err);
    }

    function showArtilceGallery($art_id,$disp_nomber){$db=DbSingleton::getTokoDb();
        $form="";$form_htm=RD."/tpl/catalogue_article_gallery.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select t2af.* from T2_PHOTOS t2af where t2af.ART_ID='$art_id' and t2af.ACTIVE=1 order by t2af.PHOTO_NAME asc;");$n=$db->num_rows($r);$list="";$slide_list="";
        for ($i=1;$i<=$n;$i++){
            $file_name=$db->result($r,$i-1,"PHOTO_NAME"); $file_name=trim(preg_replace('/\s\s+/', ' ', $file_name));
            $main_v=""; $main_c="";	if ($i==1){$main_v="active";$main_c="class=\"active\"";}
            $link="https://toko.ua/uploads/images/catalogue/$file_name";
            $list.="<div class=\"item $main_v\">
                <img alt=\"image\"  class=\"img-responsive\" src=\"$link\" align='center'>
                <div class=\"carousel-caption\">
                    <p>$file_name</p>
                </div>
            </div>";
            $slide_list.="<li data-slide-to=\"".($i-1)."\" data-target=\"#carouselArticleModal\" $main_c></li>";
        }
        if ($n==0){$list="<h3 class='text-center'>Фото відсутні</h3>";}
        $form=str_replace("{items_list}",$list,$form);
        $form=str_replace("{slide_list}",$slide_list,$form);
        return array($form,"Фотогалерея артикула: $disp_nomber");
    }

    function loadArticleFoto($art_id){$db=DbSingleton::getTokoDb();
        $form="";$form_htm=RD."/tpl/catalogue_foto_block.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select t2af.* from T2_PHOTOS t2af where t2af.ART_ID='$art_id' and t2af.ACTIVE=1 order by t2af.PHOTO_NAME asc;");$n=$db->num_rows($r);$list="";
        for ($i=1;$i<=$n;$i++){
            $file_id=$db->result($r,$i-1,"ID");
            $user_id=$db->result($r,$i-1,"USER_ID");
            $file_name=$db->result($r,$i-1,"PHOTO_NAME"); $file_name=trim(preg_replace('/\s\s+/', ' ', $file_name));
            $data=$db->result($r,$i-1,"DATA");
            $user_name=$this->getMediaUserName($user_id);
            $main=$db->result($r,$i-1,"MAIN");
            $main_v="<a class=\"btn btn-xs btn-white\" onClick=\"setArticlesFotoMain('$art_id','$file_id')\"><i class=\"fa fa-check\"></i> Основне фото</a>";
            if ($main==1){$main_v=" <span class=\"btn btn-xs label-primary\"><i class=\"fa fa-check\"></i> Основне фото</span>";}
            $link="https://toko.ua/uploads/images/catalogue/$file_name";
            $block=$form;
            $block=str_replace("{file_id}",$file_id,$block);
            $block=str_replace("{foto_name}",$file_name,$block);
            $block=str_replace("{file_name}",$file_name,$block);
            $block=str_replace("{user_name}",$user_name,$block);
            $block=str_replace("{data}",$data,$block);
            $block=str_replace("{art_id}",$art_id,$block);
            $block=str_replace("{link}",$link,$block);
            $block=str_replace("{main}",$main_v,$block);
            $list.=$block;
        }
        if ($n==0){$list="<h3 class='text-center'>Фото відсутні</h3>";}
        return $list;
    }

    function setArticlesFotoMain($art_id,$file_id){$db=DbSingleton::getTokoDb();$slave=new slave;
        $answer=0;$err="Помилка обробки запиту!";
        $art_id=$slave->qq($art_id);$file_id=$slave->qq($file_id);
        if ($art_id>0 && $file_id>0){
            $db->query("update T2_PHOTOS set MAIN='0' where ART_ID='$art_id' and MAIN='1';");
            $db->query("update T2_PHOTOS set MAIN='1' where ART_ID='$art_id' and ID='$file_id';");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function articlesFotoDropFile($art_id,$file_id){$db=DbSingleton::getTokoDb();$slave=new slave;
        $answer=0;$err="Помилка видалення файлу!";
        $art_id=$slave->qq($art_id);$file_id=$slave->qq($file_id);
        if ($art_id>0 && $file_id>0){
            $r=$db->query("select PHOTO_NAME from T2_PHOTOS where ART_ID='$art_id' and ID='$file_id' limit 0,1;");$n=$db->num_rows($r);
            if ($n==1){
                unlink(RD.'/cdn/artfoto/$PHOTO_NAME');
                $db->query("delete from T2_PHOTOS where ART_ID='$art_id' and ID='$file_id';");
                $answer=1;$err="";
            }
        }
        return array($answer,$err);
    }

    function loadArticleScheme($template_id,$op){$db=DbSingleton::getTokoDb();
        $form="";$form_htm=RD."/tpl/catalogue_scheme_block.htm";if ($op==1){$form_htm=RD."/tpl/catalogue_scheme_view_block.htm";}if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select * from T2_ARTICLES_SCHEME where TEMPLATE_ID='$template_id' order by FILE_NAME asc;");$n=$db->num_rows($r);$list="";
        for ($i=1;$i<=$n;$i++){
            $file_id=$db->result($r,$i-1,"ID");
            $user_id=$db->result($r,$i-1,"USER_ID");
            $file_name=$db->result($r,$i-1,"FILE_NAME"); $file_name=trim(preg_replace('/\s\s+/', ' ', $file_name));
            $data=$db->result($r,$i-1,"DATA");
            $user_name=$this->getMediaUserName($user_id);
            $name=$db->result($r,$i-1,"name");
            $link="http://portal.myparts.pro/cdn/articles_scheme/$template_id/$file_name";
            $file="<img src='$link' class='image' alt='$name'>";
            $block=$form;
            $block=str_replace("{file_id}",$file_id,$block);
            $block=str_replace("{file}",$file,$block);
            $block=str_replace("{file_name}",$file_name,$block);
            $block=str_replace("{scheme_name}",$name,$block);
            $block=str_replace("{user_name}",$user_name,$block);
            $block=str_replace("{data}",$data,$block);
            $block=str_replace("{template_id}",$template_id,$block);
            $block=str_replace("{link}",$link,$block);
            $list.=$block;
        }
        if ($n==0){$list="<h3 class='text-center'>Схеми відсутні</h3>";}
        return $list;
    }

    function articlesSchemeDropFile($file_id){$db=DbSingleton::getTokoDb();$slave=new slave;
        $answer=0;$err="Помилка видалення файлу!";
        $file_id=$slave->qq($file_id);
        if ($file_id>0){
            $r=$db->query("select TEMPLATE_ID,FILE_NAME from T2_ARTICLES_SCHEME where ID='$file_id' limit 0,1;");$n=$db->num_rows($r);
            if ($n==1){
                unlink(RD.'/cdn/articles_scheme/$template_id/$file_name');
                $db->query("delete from T2_ARTICLES_SCHEME where ID='$file_id';");
                $answer=1;$err="";
            }
        }
        return array($answer,$err);
    }

    function showSupplListSelect($sel_id){$db=DbSingleton::getDb();
        $r=$db->query("select c.*,ot.name as org_type_name from A_CLIENTS c 
            inner join A_CLIENTS_CATEGORY cc on cc.client_id=c.id 
            left outer join A_ORG_TYPE ot on ot.id=c.org_type 
        where c.status='1' and cc.category_id='2' order by name,full_name,id asc;");$n=$db->num_rows($r);$list="<option value=''></option>";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $org_type=$db->result($r,$i-1,"org_type_name");
            $name=$db->result($r,$i-1,"name");if ($name==""){$name=$db->result($r,$i-1,"full_name");}
            $sel="";if ($sel_id==$id){$sel="selected='selected'";}
            $list.="<option value='$id' $sel>$name ($org_type)</option>";
        }
        return $list;
    }

    function showManufactureListSelect($sel_id){$db=DbSingleton::getTokoDb();
        $r=$db->query("select * from T_manufacturers order by MFA_BRAND asc;");$n=$db->num_rows($r);$list="<option value=''></option>";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"MFA_ID");
            $name=$db->result($r,$i-1,"MFA_BRAND");
            $sel="";if ($sel_id==$id){$sel="selected='selected'";}
            $list.="<option value='$id' $sel>$name</option>";
        }
        return $list;
    }

    function showModelSelectList($mfa_id,$sel_id){$db=DbSingleton::getTokoDb();
        $r=$db->query("select * from T_models where MOD_MFA_ID='$mfa_id' order by Model asc;");$n=$db->num_rows($r);$list="<option value=''></option>";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"MOD_ID");
            $name=$db->result($r,$i-1,"TEX_TEXT")." (";
            $name.=substr($db->result($r,$i-1,"MOD_PCON_START"),0,4).".";
            $name.=substr($db->result($r,$i-1,"MOD_PCON_START"),4,2)."-";
            if (strlen($db->result($r,$i-1,"MOD_PCON_END"))>1){
                $name.=substr($db->result($r,$i-1,"MOD_PCON_END"),0,4).".";
                $name.=substr($db->result($r,$i-1,"MOD_PCON_END"),4,2);
            }else{$name.="&infin;";}
            $name.=")";
            $sel="";if ($sel_id==$id){$sel="selected='selected'";}
            $list.="<option value='$id' $sel>$name</option>";
        }
        return $list;
    }

    function showFilterModificationSelectList($mod_id,$sel_id){$db=DbSingleton::getTokoDb();
        $r=$db->query("select * from T_types where TYP_MOD_ID='$mod_id' order by TYP_SORT,TYP_TEXT asc;");$n=$db->num_rows($r);$list="<option value=''></option>";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"TYP_ID");
            $name="";
            if ($db->result($r,$i-1,"Fuel")!=""){$name.=$db->result($r,$i-1,"Fuel")." | ";}
            $name.=$db->result($r,$i-1,"TYP_TEXT")." | ";
            $name.="(".substr($db->result($r,$i-1,"TYP_PCON_START"),0,4).".".substr($db->result($r,$i-1,"TYP_PCON_START"),4,2);
            $name.="-";
            if (strlen($db->result($r,$i-1,"TYP_PCON_END"))>1){
                $name.=substr($db->result($r,$i-1,"TYP_PCON_END"),0,4).".".substr($db->result($r,$i-1,"TYP_PCON_END"),4,2).") | ";
            }else{$name.="&infin;) | ";}
            $name.=$db->result($r,$i-1,"TYP_HP_FROM")."HP/";
            $name.=$db->result($r,$i-1,"TYP_KW_FROM")."kW | ";
            $name.=$db->result($r,$i-1,"TYP_CCM")."см<sup>3</sup> | ";
            $name.=$db->result($r,$i-1,"ENG_Cod");
            $sel="";if ($sel_id==$id){$sel="selected='selected'";}
            $list.="<option value='$id' $sel>$name</option>";
        }
        return $list;
    }

    function getTecGroupTreeChilds($str_id){$db = DbSingleton::getTokoDb();
        $r=$db->query("SELECT count(STR_ID) as kol FROM `T2_GROUP_TREE` where `STR_ID_PARENT`='$str_id';");
        $kol=0+$db->result($r,0,"kol");
        return $kol;
    }

    function createTDtree($mfa_id, $mod_id, $typ_id, $level, $parent_id, $tree) {$db = DbSingleton::getTokoDb(); $dbp=DbSingleton::getTokoDb();
        $query="SELECT ART_ID,LA_ID FROM T2_LINKS where TYP_ID ='$typ_id' GROUP BY ART_ID;";
        $r=$dbp->query($query);$n=$dbp->num_rows($r);$art_id_str="0";
        for ($i=1;$i<=$n;$i++){
            $art_id=$dbp->result($r,$i-1,"ART_ID");if ($art_id!=""){$art_id_str.=",$art_id";}
    //			$la_id=$dbp->result($r,$i-1,"LA_ID");if ($la_id!=""){$art_id_str.=",$la_id";}
        }
        $query="SELECT ART_ID FROM T2_ARTICLES where `ART_ID` in ($art_id_str);";
        $r=$db->query($query);$n=$db->num_rows($r);$art_id_str="0";
        for ($i=1;$i<=$n;$i++){
            $art_id=$db->result($r,$i-1,"ART_ID");$art_id_str.=",$art_id";
        }
        $query="SELECT * FROM T2_TREE where `ART_ID` in ($art_id_str);";
        $r=$dbp->query($query);$n=$dbp->num_rows($r);$str_id_str="0";$str_id_a=array();
        for ($i=1;$i<=$n;$i++){
            $str_id=$dbp->result($r,$i-1,"STR_ID");$str_id_str.=",$str_id";
            $art_id=$dbp->result($r,$i-1,"ART_ID");$str_id_a[$str_id][$art_id]=$art_id;
        }
        $td_array = array();
        $query="SELECT * FROM `T2_GROUP_TREE` where `STR_ID` in ($str_id_str);";
        $r=$db->query($query);$n=$dbp->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $str_id=$db->result($r,$i-1,"STR_ID");
            $str_id_parrent=$db->result($r,$i-1,"STR_ID_PARENT");if ($str_id_parrent==""){$str_id_parrent=0;}
            $str_level=$db->result($r,$i-1,"STR_LEVEL");
            $tex_text=$db->result($r,$i-1,"TEX_TEXT");
            $child=$this->getTecGroupTreeChilds($str_id);
            $art_ids=implode(",", $str_id_a[$str_id]);
            $td_array[$i]["id_tree"] = $str_id;
            $td_array[$i]["id_parent"] = $str_id_parrent;
            $td_array[$i]["level"] = $str_level;
            $td_array[$i]["name"] = $tex_text;
            $td_array[$i]["child"] = $child;
            $td_array[$i]["art_ids"] = $art_ids;
        }
        $tree = ""; $lvl = 1;
        for ($i = 1; $i <= 10; $i++) { $lvl += 1;
            foreach ($td_array as $elm) {
                if ($elm["level"] == $lvl) {
                    $str="<li><div>";
                    if ($elm["child"]>0){$str.=$elm["name"];}
                    // if ($elm["child"]==0){$str.="<a href='/Catalogue/td/$mfa_id/$mod_id/$typ_id/".$elm["level"]."/".$elm["id_tree"]."'>".$elm["name"]."</a>"; }
                    if ($elm["child"]==0){$str.="<a href='javascript:setFil2StrInfo(\"".$elm["id_tree"]."\",\"".$elm["name"]."\",\"".$elm["art_ids"]."\")'>".$elm["name"]."</a>"; }
                    $str.="</div>";
                    if ($elm["child"]>0){$str.="\n<ul>\n{p".$elm["id_tree"]."}</ul>\n";}
                    $str.="</li>\n";
                    if ($lvl==2){$tree.=$str;}
                    if ($lvl>2){$tree=str_replace("{p".$elm["id_parent"]."}",$str."{p".$elm["id_parent"]."}",$tree);}
                }
            }
        }
        foreach ($td_array as $elm){
            $tree=str_replace("{p".$elm["id_parent"]."}","",$tree);
            $tree=str_replace("{p".$elm["id_tree"]."}","",$tree);
        }
        return $tree;
    }

    function createTDtree_universal($art_id,$level, $parent_id, $tree) {$db = DbSingleton::getTokoDb(); $dbp=DbSingleton::getTokoDb();
        $query="SELECT * FROM T2_TREE where `ART_ID` in ($art_id);";
        $r=$dbp->query($query);$n=$dbp->num_rows($r);$str_id_str="0";$str_id_a=array();
        for ($i=1;$i<=$n;$i++){
            $str_id=$dbp->result($r,$i-1,"STR_ID");$str_id_str.=",$str_id";
            //$art_id=$dbp->result($r,$i-1,"ART_ID");$str_id_a[]=$str_id;
        }
        $td_array = array();
        $query="SELECT * FROM `T2_GROUP_TREE` where `LNG_ID` = '16';";
        $r=$db->query($query);$n=$dbp->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $str_id=$db->result($r,$i-1,"STR_ID");
            $str_id_parrent=$db->result($r,$i-1,"STR_ID_PARENT");if ($str_id_parrent==""){$str_id_parrent=0;}
            $str_level=$db->result($r,$i-1,"STR_LEVEL");
            $tex_text=$db->result($r,$i-1,"TEX_TEXT");
            $child=$this->getTecGroupTreeChilds($str_id);
            $art_ids=implode(",", $str_id_a[$str_id]);

            $td_array[$i]["id_tree"] = $str_id;
            $td_array[$i]["id_parent"] = $str_id_parrent;
            $td_array[$i]["level"] = $str_level;
            $td_array[$i]["name"] = $tex_text;
            $td_array[$i]["child"] = $child;
            $td_array[$i]["art_ids"] = $art_ids;
        }
        $tree = ""; $lvl = 1;
        for ($i = 1; $i <= 10; $i++) { $lvl += 1;
            foreach ($td_array as $elm) {
                if ($elm["level"] == $lvl) {
                    $str="<li><div>";
                    if ($elm["child"]>0){$str.=$elm["name"];}
    //                    if ($elm["child"]==0){$str.="<a href='/Catalogue/td/$mfa_id/$mod_id/$typ_id/".$elm["level"]."/".$elm["id_tree"]."'>".$elm["name"]."</a>"; }
                    $checked="";if (in_array($elm["id_tree"],$str_id_a)){$checked="checked='checked'";}
                    if ($elm["child"]==0){$str.="<a href='javascript:return;'><input type='checkbox' id='{na_tree_".$elm["id_tree"]."}' value='\"".$elm["id_tree"]."\"' $checked>".$elm["name"]."</a>"; }
                    $str.="</div>";
                    if ($elm["child"]>0){$str.="\n<ul>\n{p".$elm["id_tree"]."}</ul>\n";}
                    $str.="</li>\n";
                    if ($lvl==2){$tree.=$str;}
                    if ($lvl>2){$tree=str_replace("{p".$elm["id_parent"]."}",$str."{p".$elm["id_parent"]."}",$tree);}
                }
            }
        }$kol_elem=0;
        foreach ($td_array as $elm){$kol_elem+=1;
            $tree=str_replace("{p".$elm["id_parent"]."}","",$tree);
            $tree=str_replace("{p".$elm["id_tree"]."}","",$tree);
            $tree=str_replace("{na_tree_".$elm["id_tree"]."}","na_tree_".$kol_elem,$tree);
        }
        return array($tree,$kol_elem);
    }

    function loadFilterGroupTreeList($mfa_id, $mod_id, $typ_id,$sel_id){$level=$parent_id=0;
        $form="";$form_htm=RD."/tpl/catalogue_tecdoc_group_tree.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $form = str_replace("{menu}", $this->createTDtree($mfa_id, $mod_id, $typ_id, $level, $parent_id, ""), $form);
        return array($form,"Оберіть групу запчастин");
    }

    function loadFilterGroupTreeListSide($mfa_id, $mod_id, $typ_id,$sel_id){$level=$parent_id=0;
        $form="";$form_htm=RD."/tpl/catalogue_tecdoc_tree_result_form.htm";if (file_exists("$form_htm")){ $form= file_get_contents($form_htm);}
        $form_tree="";$form_htm=RD."/tpl/catalogue_tecdoc_group_tree.htm";if (file_exists("$form_htm")){ $form_tree = file_get_contents($form_htm);}
        $form_tree = str_replace("{menu}", $this->createTDtree($mfa_id, $mod_id, $typ_id, $level, $parent_id, ""), $form_tree);
        $form_result="";$form_htm=RD."/tpl/catalogue_search_result_table.htm";if (file_exists("$form_htm")){ $form_result = file_get_contents($form_htm);}
        list($header_list,$range_list,$list_brand_select)=$this->showArticlesSearchList("","",0);
        $form_result=str_replace("{header_list}",$header_list,$form_result);
        $form_result=str_replace("{range_list}",$range_list,$form_result);
        return array($form,$form_tree,$form_result);
    }

    function getBrandId($art_id){$db=DbSingleton::getTokoDb();$brand_id=0;
        $r=$db->query("select BRAND_ID from T2_ARTICLES where ART_ID='$art_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$brand_id=$db->result($r,0,"BRAND_ID");}
        return $brand_id;
    }

    function getBrandKind($sel_id){$db=DbSingleton::getTokoDb();$kind=4;
        $r=$db->query("select KIND from T2_BRANDS where BRAND_ID='$sel_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$kind=$db->result($r,0,"KIND");}
        return $kind;
    }

    function getBrandName($sel_id){$db=DbSingleton::getTokoDb();$brand_name="";
        $r=$db->query("select BRAND_NAME from T2_BRANDS where BRAND_ID='$sel_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$brand_name=$db->result($r,0,"BRAND_NAME");}
        return $brand_name;
    }

    function showManufListSelect($sel_id){$db=DbSingleton::getTokoDb();
        $r=$db->query("select * from T2_MANUF order by NAME asc;");$n=$db->num_rows($r);$list="<option value=''></option>";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"ID");
            $key=$db->result($r,$i-1,"KEY");
            $name=$db->result($r,$i-1,"NAME");
            $sel="";if ($sel_id==$id){$sel="selected='selected'";}
            $list.="<option value='$id-$key' $sel>$name</option>";
        }
        return $list;
    }

    function showBrandLetterListSelect($sel_id){$db=DbSingleton::getTokoDb();
        $r=$db->query("select * from T2_BRANDS where LETTER_CODE!='' order by BRAND_NAME asc;");$n=$db->num_rows($r);$list="<option value=''></option>";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"BRAND_ID");
            $letter_code=$db->result($r,$i-1,"LETTER_CODE");
            $name=$db->result($r,$i-1,"BRAND_NAME");
            $sel="";if ($sel_id==$id){$sel="selected='selected'";}
            $list.="<option value='$id-$letter_code' $sel>$name</option>";
        }
        return $list;
    }

    function showBrandListSelect($sel_id){$db=DbSingleton::getTokoDb();
        $r=$db->query("select * from T2_BRANDS order by BRAND_NAME asc;");$n=$db->num_rows($r);$list="<option value=''></option>";
        for ($i=1;$i<=$n;$i++){
            $brand_id=$db->result($r,$i-1,"BRAND_ID");
            $brand_name=$db->result($r,$i-1,"BRAND_NAME");
            $sel="";if ($sel_id==$brand_id){$sel="selected='selected'";}
            $list.="<option value='$brand_id' $sel>$brand_name</option>";
        }
        return $list;
    }

    function showUnitsListSelect($sel_id,$ns){$db=DbSingleton::getTokoDb();if ($ns==""){$ns=1;}
        $r=$db->query("select * from units order by name asc;");$n=$db->num_rows($r);$list="<option value=''></option>";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"abr");
            if ($ns==2){ $name=$db->result($r,$i-1,"name");}
            $sel="";if ($sel_id==$id){$sel="selected='selected'";}
            $list.="<option value='$id' $sel>$name</option>";
        }
        return $list;
    }

    function loadRefinementListSelect($group_id){$db=DbSingleton::getTokoDb();
        $r=$db->query("select * from T2_REFINEMENT where GROUP_ID='$group_id' order by `NAME`,`ID` asc;");$n=$db->num_rows($r);$list="<option value=''></option>";
        for ($i=1;$i<=$n;$i++){
            $name=$db->result($r,$i-1,"NAME");
            $key=$db->result($r,$i-1,"KEY");
            //$sel="";if ($sel_id==$id){$sel="selected='selected'";}
            $list.="<option value='$key'>$i. $name</option>";
        }
        return $list;
    }

    function showGoodsGroupLetterListSelect($prnt_id){$db=DbSingleton::getTokoDb();
        $r=$db->query("select * from GOODS_GROUP where PARRENT_ID='$prnt_id' order by `KEY`,`ID` asc;");$n=$db->num_rows($r);$list="<option value=''></option>";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"ID");
            $name=$db->result($r,$i-1,"NAME");
            $key=$db->result($r,$i-1,"KEY");
           // $sel="";if ($sel_id==$id){$sel="selected='selected'";}
            $list.="<option value='$id-$key'>$i. $name</option>";
        }
        return $list;
    }

    function showGoodsGroupListSelect($sel_id){$db=DbSingleton::getTokoDb();
        $r=$db->query("select * from GOODS_GROUP where PARRENT_ID='0' order by `KEY`,`ID` asc;");$n=$db->num_rows($r);$list="<option value=''></option>";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"ID");
            $name=$db->result($r,$i-1,"NAME");
            $sel="";if ($sel_id==$id){$sel="selected='selected'";}
            $list.="<option value='$id' style='font-weight:bold' $sel>$i. $name</option>".$this->showGoodsGroupSubListSelect($id,$sel_id,$i);
        }
        return $list;
    }

    function showGoodsGroupSubListSelect($parrent_id,$sel_id,$prn_i){$db=DbSingleton::getTokoDb();
        $r=$db->query("select * from GOODS_GROUP where PARRENT_ID='$parrent_id'  order by `KEY`,`ID` asc;");$n=$db->num_rows($r);$list="";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"ID");
            $name=$db->result($r,$i-1,"NAME");
            $sel="";if ($sel_id==$id){$sel="selected='selected'";}
            $list.="<option value='$id' $sel>$prn_i.$i. $name</option>".$this->showGoodsGroupSubListSelect($id,$sel_id,"$prn_i.$i");
        }
        return $list;
    }

    function unlinkCatalogueGoodGroup($art_id,$group_id){$db=DbSingleton::getTokoDb(); $answer=0;$err="Помилка обробки даних";
        $r=$db->query("select * from T2_GOODS_GROUP where ART_ID='$art_id' and `GOODS_GROUP_ID`='$group_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $db->query("delete from T2_GOODS_GROUP where ART_ID='$art_id' and `GOODS_GROUP_ID`='$group_id';");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function showGoodGroupTree($art_id,$sel_id){$db=DbSingleton::getTokoDb();$tree="";
        $form="";$form_htm=RD."/tpl/catalogue_goods_group_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select * from GOODS_GROUP where PARRENT_ID='0'  order by `KEY`,`ID` asc;");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"ID");
            $name=$db->result($r,$i-1,"NAME");
            $sel="";if ($sel_id==$id){$sel=" data-jstree='{\"selected\":true}'";}
            $tree.="<li id='$id'$sel>$name".$this->showGoodGroupSubLevel($id,$sel_id)."</li>";
        }
        $form=str_replace("{tree}",$tree,$form);
        $form=str_replace("{goods_group_id}",$sel_id,$form);
        return $form;
    }

    function showGoodGroupSubLevel($parrent_id,$sel_id){$db=DbSingleton::getTokoDb();$tree="";
        $r=$db->query("select * from GOODS_GROUP where PARRENT_ID='$parrent_id' order by `KEY`,`ID` asc;");$n=$db->num_rows($r);
        if ($n>0){$tree.="<ul>";
            for ($i=1;$i<=$n;$i++){
                $id=$db->result($r,$i-1,"ID");
                $name=$db->result($r,$i-1,"NAME");
                $sel="";if ($sel_id==$id){$sel=" data-jstree='{\"selected\":true}'";}
                $tree.="<li id='$id'$sel>$name".$this->showGoodGroupSubLevel($id,$sel_id)."</li>";
            }
            $tree.="</ul>";
        }
        return $tree;
    }

    function getArticleGoodsGroup($art_id){$db=DbSingleton::getTokoDb();$id=0;$caption="";
        $r=$db->query("select gg.* from GOODS_GROUP gg inner join T2_GOODS_GROUP t2gg on t2gg.GOODS_GROUP_ID=gg.ID where t2gg.ART_ID='$art_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$id=$db->result($r,0,"ID");$caption=$db->result($r,0,"NAME");}
        return array($id,$caption);
    }

    function getArticleGoodsGroupTemplateId($art_id,$goods_group_id){$db=DbSingleton::getTokoDb();$id=0;
        $r=$db->query("select TEMPLATE_ID from T2_GOODS_GROUP_TEMPLATES where ART_ID='$art_id' and GOODS_GROUP_ID='$goods_group_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$id=$db->result($r,0,"TEMPLATE_ID");}
        return $id;
    }

    function getArticleGoodsGroupTemplateCaption($id){$db=DbSingleton::getTokoDb();$name="";
        $r=$db->query("select NAME from GOODS_GROUP_TEMPLATE where ID='$id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$name=$db->result($r,0,"NAME");}
        return $name;
    }

    function getArticleGoodsGroupTemplateText($id){$db=DbSingleton::getTokoDb();$name="";
        $r=$db->query("select TEXT from GOODS_GROUP_TEMPLATE where ID='$id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$name=$db->result($r,0,"TEXT");}
        return $name;
    }

    function getArticleGoodsGroupTemplateDescr($id){$db=DbSingleton::getTokoDb();$name="";
        $r=$db->query("select DESCR from GOODS_GROUP_TEMPLATE where ID='$id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$name=$db->result($r,0,"DESCR");}
        return $name;
    }

    function showGoodsGroupTemplateListSelect($goods_group_id,$sel_id){$db=DbSingleton::getTokoDb();
        $r=$db->query("select * from GOODS_GROUP_TEMPLATE where GOODS_GROUP_ID='$goods_group_id' order by name,id asc;");$n=$db->num_rows($r);$list="";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"ID");
            $name=$db->result($r,$i-1,"NAME");
            $sel="";if ($sel_id==$id){$sel="selected='selected'";}
            $list.="<option value='$id' $sel>$name</option>";
        }
        return $list;
    }

    function getGoodsGroupParamValue($art_id,$param_id){$db=DbSingleton::getTokoDb();$value="";
        $r=$db->query("select PARAM_VALUE from T2_GOODS_GROUP_PARAMS_VALUE where ART_ID='$art_id' and PARAM_ID='$param_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$value=$db->result($r,0,"PARAM_VALUE");}
        return $value;
    }

    function getGoodsGroupParamsValueDataList($param_id,$template_id){$db=DbSingleton::getTokoDb();$list="<datalist id='datalist_".$param_id."'>";
        $r=$db->query("select PARAM_VALUE from T2_GOODS_GROUP_PARAMS_VALUE where PARAM_ID='$param_id' and TEMPLATE_ID='$template_id' group by PARAM_VALUE order by PARAM_VALUE asc;");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $param_value=$db->result($r,$i-1,"PARAM_VALUE");
            $list.="<option value='$param_value'>";
        }$list.="</datalist>";
        return $list;
    }

    function showGoodsGroupParamsList($art_id,$template_id){$db=DbSingleton::getTokoDb();$manual=new manual;
        $r=$db->query("select * from GOODS_GROUP_TEMPLATE_PARAMS where TEMPLATE_ID='$template_id' order by PARAM_ID asc;");$n=$db->num_rows($r);$list="";
        for ($i=1;$i<=$n;$i++){
            $param_name=$db->result($r,$i-1,"NAME");
            $param_id=$db->result($r,$i-1,"PARAM_ID");
            $field_type=$db->result($r,$i-1,"FIELD_TYPE");
            $type=$db->result($r,$i-1,"TYPE");
            $param_value=$this->getGoodsGroupParamValue($art_id,$param_id);
            $ftype="text";if ($field_type==1){$ftype="text'  min='0' pattern='^[0-9]' onkeypress='return isNumber(event)'  data-bind='value:replyNumber";$param_value=str_replace(",",".",$param_value);}
            $tparam=$manual->getManualMCaption("template_param_type",$type);
            $list.="<tr>
                <td>$i<input type='hidden' id='paramId_$i' value='$param_id'></td>
                <td>$param_name</td>
                <!--<td><select class='form-control' size=1 id='param_field_type_".$param_id."'>".$manual->showManualSelectList("template_param_field_type",$field_type)."</select></td>-->
                <td>".$manual->getManualMCaption("template_param_field_type",$field_type)."</td>
                <td><input type='$ftype' class='form-control' list='datalist_".$param_id."' id='param_value_".$param_id."' value='$param_value'>".$this->getGoodsGroupParamsValueDataList($param_id,$template_id)."</td>
                <td>$tparam</td>
            </tr>";
        }$list.="<input type='hidden' id='params_kol' value='$n'>";
        return $list;
    }

    function showGoodsGroupParamsNameList($art_id,$template_id){$db=DbSingleton::getTokoDb();$manual=new manual;$type=0;
        $r=$db->query("select * from GOODS_GROUP_TEMPLATE_PARAMS where TEMPLATE_ID='$template_id' order by PARAM_ID asc;");$n=$db->num_rows($r);$list="";
        for ($i=1;$i<=$n+15;$i++){ $param_name=""; $param_id=0;$field_type="";
            if ($i<=$n){
                $param_name=$db->result($r,$i-1,"NAME");
                $param_id=$db->result($r,$i-1,"PARAM_ID");
                $field_type=$db->result($r,$i-1,"FIELD_TYPE");
                $type=$db->result($r,$i-1,"TYPE");
            }
            $list.="<tr>
                <td>$i<input type='hidden' id='tmp_param_id_$i' value='$param_id'></td>
                <td><input type='text' class='form-control' id='tmp_param_name_".$i."' value='$param_name'></td>
                <td><select class='form-control' size=1 id='tmp_param_field_type_".$i."'>".$manual->showManualSelectList("template_param_field_type",$field_type)."</select></td>
                <td><select class='form-control' size=1 id='tmp_param_type_".$i."'>".$manual->showManualSelectList("template_param_type",$type)."</select></td>
            </tr>";
        }
        $list.="<input type='hidden' id='tmp_params_kol' value='".($n+15)."'>";
        return $list;
    }

    function showCatalogueGoodGroupTemplateForm($art_id,$template_id){
        $form="";$form_htm=RD."/tpl/catalogue_template_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $form=str_replace("{art_id}",$art_id,$form);
        $form=str_replace("{template_id}",$template_id,$form);
        $form=str_replace("{template_name}",$this->getArticleGoodsGroupTemplateCaption($template_id),$form);
        $form=str_replace("{template_caption}",$this->getArticleGoodsGroupTemplateText($template_id),$form);
        $form=str_replace("{template_descr}",$this->getArticleGoodsGroupTemplateDescr($template_id),$form);
        list($goods_group_id,$goods_group_caption)=$this->getArticleGoodsGroup($art_id);
        $form=str_replace("{goods_group_id}",$goods_group_id,$form);
        $form=str_replace("{goods_group_caption}",$goods_group_caption,$form);
        $form=str_replace("{params_list}",$this->showGoodsGroupParamsNameList($art_id,$template_id),$form);
        $form=str_replace("{scheme_list}",$this->loadArticleScheme($template_id,0),$form);
        return array($form,"Редагування шаблону");
    }

    function loadArticleParams($art_id){
        $form="";$form_htm=RD."/tpl/catalogue_params.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $form=str_replace("{art_id}",$art_id,$form);
        list($goods_group_id,$goods_group_caption)=$this->getArticleGoodsGroup($art_id);
        $form=str_replace("{goods_group_id}",$goods_group_id,$form);
        $form=str_replace("{goods_group_caption}",$goods_group_caption,$form);
        $template_id=$this->getArticleGoodsGroupTemplateId($art_id,$goods_group_id);
        $form=str_replace("{goods_group_template_list}",$this->showGoodsGroupTemplateListSelect($goods_group_id,$template_id),$form);
        $form=str_replace("{params_list}",$this->showGoodsGroupParamsList($art_id,$template_id),$form);
        $form=str_replace("{scheme_list}",$this->loadArticleScheme($template_id,1),$form);
        return $form;
    }

    function loadArticleInfo($art_id) {$db=DbSingleton::getTokoDb(); $list="";
        $form="";$form_htm=RD."/tpl/catalogue_article_info.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select * from T2_INFO where ART_ID='$art_id' order by `SORT` asc;"); $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $TEXT=$db->result($r,$i-1,"TEXT");
            $VALUE=$db->result($r,$i-1,"VALUE");
            $SORT=$db->result($r,$i-1,"SORT");
            $LANG_ID=$db->result($r,$i-1,"LANG_ID");
            $lang_select="<select class='form-control' disabled>".$this->getLangList($LANG_ID)."</select>";
            $list.="<div class='row' style='padding-bottom:15px;'>
                <div class='col-lg-2'><input id='info_text-$i' type='text' value='$TEXT' class='form-control'></div>
                <div class='col-lg-2'><input id='info_value-$i' type='text' value='$VALUE' class='form-control'></div>
                <div class='col-lg-2'><input id='info_sort-$i' type='text' value='$SORT' class='form-control'></div>
                <div class='col-lg-2'>$lang_select</div>
                <div class='col-lg-2'>
                    <button class='btn btn-primary' onClick=\"saveArticleInfo('$art_id','$LANG_ID','$SORT','$i');\"><i class='fa fa-save'></i> Зберегти</button>
                    <button class='btn btn-danger' onClick=\"dropArticleInfo('$art_id','$LANG_ID','$SORT');\"><i class='fa fa-save'></i> Видалити</button>
                </div>
            </div>";
        }
        if ($n==0) $list="Пусто";
        $form=str_replace("{article_info}",$list,$form);
        $form=str_replace("{add_info}","addArticleInfo($art_id);",$form);
        $form=str_replace("{lang_info}",$this->getLangList(),$form);
        return $form;
    }

    function saveArticleInfo($art_id,$lang_id,$text,$value,$sort,$new_sort) { $db=DbSingleton::getTokoDb();
        $answer=0;$err="Помилка збереження даних!";
        if ($art_id>0) {
            $db->query("update T2_INFO set TEXT='$text', VALUE='$value', SORT='$new_sort' where ART_ID='$art_id' and LANG_ID='$lang_id' and SORT='$sort';");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function dropArticleInfo($art_id,$lang_id,$sort) { $db=DbSingleton::getTokoDb();
        $answer=0;$err="Помилка збереження даних!";
        if ($art_id>0) {
            $db->query("delete from T2_INFO where ART_ID='$art_id' and LANG_ID='$lang_id' and SORT='$sort';");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function addArticleInfo($art_id,$lang_id,$text,$value,$sort) { $db=DbSingleton::getTokoDb();
        $answer=0; $err="Помилка збереження даних!";
        if ($art_id>0) {
            $r=$db->query("select * from T2_INFO where ART_ID='$art_id' and LANG_ID='$lang_id' and SORT='$sort';");	$n=$db->num_rows($r);
            if ($n==0) {
                $db->query("insert into T2_INFO (ART_ID,LANG_ID,TEXT,VALUE,SORT) values ('$art_id','$lang_id','$text','$value','$sort');");
                $answer=1;$err="";
            } else $err="Параметр з таким порядковим номером уже існує!";
        }
        return array($answer,$err);
    }

    function loadArticleShortInfo($art_id) {$db=DbSingleton::getTokoDb(); $list="";
        $form="";$form_htm=RD."/tpl/catalogue_article_info.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select * from T2_SHORT_INFO where ART_ID='$art_id' order by `SORT` asc;"); $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $TEXT=$db->result($r,$i-1,"TEXT");
            $VALUE=$db->result($r,$i-1,"VALUE");
            $SORT=$db->result($r,$i-1,"SORT");
            $LANG_ID=$db->result($r,$i-1,"LANG_ID");
            $lang_select="<select class='form-control' disabled>".$this->getLangList($LANG_ID)."</select>";
            $list.="<div class='row' style='padding-bottom:15px;'>
                <div class='col-lg-2'><input id='info_text-$i' type='text' value='$TEXT' class='form-control'></div>
                <div class='col-lg-2'><input id='info_value-$i' type='text' value='$VALUE' class='form-control'></div>
                <div class='col-lg-2'><input id='info_sort-$i' type='text' value='$SORT' class='form-control'></div>
                <div class='col-lg-2'>$lang_select</div>
                <div class='col-lg-2'>
                    <button class='btn btn-primary' onClick=\"saveArticleShortInfo('$art_id','$LANG_ID','$SORT','$i');\"><i class='fa fa-save'></i> Зберегти</button>
                    <button class='btn btn-danger' onClick=\"dropArticleShortInfo('$art_id','$LANG_ID','$SORT');\"><i class='fa fa-save'></i> Видалити</button>
                </div>
            </div>";
        }
        if ($n==0) $list="Пусто";
        $form=str_replace("{article_info}",$list,$form);
        $form=str_replace("{add_info}","addArticleShortInfo($art_id);",$form);
        $form=str_replace("{lang_info}",$this->getLangList(),$form);
        return $form;
    }

    function saveArticleShortInfo($art_id,$lang_id,$text,$value,$sort,$new_sort) { $db=DbSingleton::getTokoDb();
        $answer=0;$err="Помилка збереження даних!";
        if ($art_id>0) {
            $db->query("update T2_SHORT_INFO set TEXT='$text', VALUE='$value', SORT='$new_sort' where ART_ID='$art_id' and LANG_ID='$lang_id' and SORT='$sort';");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function dropArticleShortInfo($art_id,$lang_id,$sort) { $db=DbSingleton::getTokoDb();
        $answer=0;$err="Помилка збереження даних!";
        if ($art_id>0) {
            $db->query("delete from T2_SHORT_INFO where ART_ID='$art_id' and LANG_ID='$lang_id' and SORT='$sort';");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function addArticleShortInfo($art_id,$lang_id,$text,$value,$sort) { $db=DbSingleton::getTokoDb();
        $answer=0; $err="Помилка збереження даних!";
        if ($art_id>0) {
            $r=$db->query("select * from T2_SHORT_INFO where ART_ID='$art_id' and LANG_ID='$lang_id' and SORT='$sort';");$n=$db->num_rows($r);
            if ($n==0) {
                $db->query("insert into T2_SHORT_INFO (ART_ID,LANG_ID,TEXT,VALUE,SORT) values ('$art_id','$lang_id','$text','$value','$sort');");
                $answer=1;$err="";
            } else $err="Параметр з таким порядковим номером уже існує!";
        }
        return array($answer,$err);
    }

    function getLangList($lang_id="") {$db=DbSingleton::getTokoDb();$list="";
        $r=$db->query("select * from lang where `on`=1;"); $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $caption=$db->result($r,$i-1,"caption");
            $lang_tcd=$db->result($r,$i-1,"lang_tcd");
            if ($lang_id==$lang_tcd) $selected="selected"; else $selected="";
            $list.="<option value='$lang_tcd' $selected>$caption</option>";
        }
        return $list;
    }

    function loadCatalogueGoodGroupTemplateParams($art_id,$template_id){
        return array($this->showGoodsGroupParamsList($art_id,$template_id),$this->loadArticleScheme($template_id,1));
    }

    function saveCatalogueParams($art_id,$goods_group_id,$template_id,$fields_type,$params_value){$db=DbSingleton::getTokoDb();$slave=new slave;$answer=0;$err="Помилка збереження даних!";
        $art_id=$slave->qq($art_id);$goods_group_id=$slave->qq($goods_group_id);$template_id=$slave->qq($template_id);$params_value=$slave->qq($params_value);
        if ($art_id>0){
            $r=$db->query("select * from T2_GOODS_GROUP_TEMPLATES where ART_ID='$art_id' and `GOODS_GROUP_ID`='$goods_group_id' limit 0,1;");$n=$db->num_rows($r);
            if ($n==0){
                $db->query("INSERT INTO `T2_GOODS_GROUP_TEMPLATES` (`ART_ID`, `GOODS_GROUP_ID`, `TEMPLATE_ID`) VALUES ('$art_id', '$goods_group_id', '$template_id');");
            }
            if ($n==1){
                $db_template_id=$db->result($r,0,"TEMPLATE_ID");
                if ($db_template_id!=$template_id){
                    $db->query("update T2_GOODS_GROUP_TEMPLATES set `TEMPLATE_ID`='$template_id' where ART_ID='$art_id' and `GOODS_GROUP_ID`='$goods_group_id';");
                    $db->query("delete from T2_GOODS_GROUP_PARAMS_VALUE where ART_ID='$art_id' and `TEMPLATE_ID`='$db_template_id';");
                }
            }

            $r2=$db->query("select * from GOODS_GROUP_TEMPLATE_PARAMS where TEMPLATE_ID='$template_id' order by PARAM_ID asc;");$n2=$db->num_rows($r2);
            for ($i=1;$i<=$n2;$i++){
                $param_id=$db->result($r2,$i-1,"PARAM_ID");

                $r=$db->query("select PARAM_VALUE from T2_GOODS_GROUP_PARAMS_VALUE where ART_ID='$art_id' and PARAM_ID='$param_id' limit 0,1;");$n=$db->num_rows($r);
                if ($n==1){
                    $value=$db->result($r,0,"PARAM_VALUE");
                    if ($params_value[$param_id]!=$value){
                        $db->query("update T2_GOODS_GROUP_PARAMS_VALUE set `TEMPLATE_ID`='$template_id', PARAM_VALUE='".$params_value[$param_id]."' where ART_ID='$art_id' and PARAM_ID='$param_id';");
                    }
                }
                if ($n==0){
                    $db->query("insert into T2_GOODS_GROUP_PARAMS_VALUE (`ART_ID`,`TEMPLATE_ID`,`PARAM_ID`,`PARAM_VALUE`) values ('$art_id','$template_id','$param_id','".$params_value[$param_id]."');");
                }
                $answer=1;$err="";
            }if ($n2==0){$answer=1;$err="";}
        }
        return array($answer,$err);
    }

    function saveCatalogueParamsTemplate($art_id,$goods_group_id,$template_id,$template_name,$template_caption,$template_descr,$cn,$params_id,$fields_type,$params_name,$params_type){$db=DbSingleton::getTokoDb();$slave=new slave;$answer=0;$err="Помилка збереження даних!";
        $art_id=$slave->qq($art_id);$goods_group_id=$slave->qq($goods_group_id);$template_id=$slave->qq($template_id);$template_name=$slave->qq($template_name);$cn=$slave->qq($cn);$fields_type=$slave->qq($fields_type);$params_name=$slave->qq($params_name); $params_type=$slave->qq($params_type);
        if ($art_id>0){
            if ($template_id==0){
                $r=$db->query("select max(ID) as mid from GOODS_GROUP_TEMPLATE;");$template_id=0+$db->result($r,0,"mid")+1;
                $db->query("insert into GOODS_GROUP_TEMPLATE (`ID`,`GOODS_GROUP_ID`,`NAME`,`TEXT`,`DESCR`) values ('$template_id','$goods_group_id','$template_name','$template_caption','$template_descr');");
            }
            if ($template_id>0){
                $db->query("update GOODS_GROUP_TEMPLATE set `NAME`='$template_name', `TEXT`='$template_caption', `DESCR`='$template_descr' where `ID`='$template_id' and `GOODS_GROUP_ID`='$goods_group_id';");
                for ($i=1;$i<=$cn;$i++){
                    $param_id=$params_id[$i];
                    $param_name=$params_name[$i];
                    $field_type=$fields_type[$i];
                    $param_type=$params_type[$i];

                    if ($param_id==0 && $param_name!=""){
                        $db->query("insert into GOODS_GROUP_TEMPLATE_PARAMS (`PARAM_ID`,`TEMPLATE_ID`,`NAME`,`FIELD_TYPE`,`TYPE`) values ('','$template_id','$param_name','$field_type','$param_type');");
                    }
                    if ($param_id>0){
                        $db->query("update GOODS_GROUP_TEMPLATE_PARAMS set `NAME`='$param_name', `FIELD_TYPE`='$field_type', `TYPE`='$param_type' where `PARAM_ID`='$param_id' and `TEMPLATE_ID`='$template_id';");
                    }
                }
                $answer=1;$err="";
            }
        }
        return array($answer,$err);
    }

    function showCatalogueAnalogIndexSearch(){
        $form="";$form_htm=RD."/tpl/catalogue_analog_search.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        return array($form,"Пошук аналогу по індексу");
    }

    function findCatalogueAnalogIndexSearch($index){$db=DbSingleton::getTokoDb();$slave=new slave;$list="";
        $index=$slave->qq($index);
        if ($index!=""){
            $query="select t2a.ART_ID, t2a.BRAND_ID, t2a.ARTICLE_NR_DISPL, t2b.BRAND_NAME, t2n.NAME
            from T2_ARTICLES t2a 
                left outer join T2_BRANDS t2b on t2b.BRAND_ID=t2a.BRAND_ID 
                left outer join T2_NAMES t2n on t2n.ART_ID=t2a.ART_ID
            where t2a.ARTICLE_NR_SEARCH='$index' or t2a.ARTICLE_NR_DISPL='$index';";
            $r=$db->query($query);$n=$db->num_rows($r);$list="";
            for ($i=1;$i<=$n;$i++){
                $art_id=$db->result($r,$i-1,"ART_ID");
                $article_nr_displ=$db->result($r,$i-1,"ARTICLE_NR_DISPL");
                $brand_id=$db->result($r,$i-1,"BRAND_ID");
                $brand_name=$db->result($r,$i-1,"BRAND_NAME");
                $name=$db->result($r,$i-1,"NAME");
                $list.="<tr style=\"cursor:pointer;\" onClick='setAnalogSearchIndex(\"$art_id\",\"$article_nr_displ\",\"$brand_id\",\"$brand_name\");'>
                    <td>$art_id</td>
                    <td>$article_nr_displ</td>
                    <td>$brand_name</td>
                    <td>$name</td>
                </tr>";
            }
        }
        return $list;
    }

    function showCatalogueAnalogForm($art_id,$kind,$relation,$search_number){$db=DbSingleton::getTokoDb();
        $form="";$form_htm=RD."/tpl/catalogue_analog_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $form=str_replace("{art_id}",$art_id,$form);
        $form=str_replace("{kind}",$kind,$form); $kind_name=" ОЕ номер";if ($kind==4){$kind_name=" інший номер";}
        $form=str_replace("{relation}",$relation,$form);
        $form=str_replace("{search_number}",$search_number,$form);
        $r=$db->query("select * from T2_CROSS where ART_ID='$art_id' and KIND ='$kind' and `SEARCH_NUMBER`='$search_number' limit 0,1;");
        $display_nr=$db->result($r,0,"DISPLAY_NR");
        $brand_id=$db->result($r,0,"BRAND_ID");
        $form=str_replace("{display_nr}",$display_nr,$form);
        $form=str_replace("{brand_list}",$this->showBrandListSelect($brand_id),$form);
        return array($form,"Редагування аналогу".$kind_name);
    }

    function saveCatalogueAnalogForm($art_id,$kind,$relation,$search_number,$display_nr,$brand_id,$art_id2,$index2){$db=DbSingleton::getTokoDb();$slave=new slave;$answer=0;$err="Помилка збереження даних!";
        $art_id=$slave->qq($art_id);$kind=$slave->qq($kind);$relation=$slave->qq($relation);$search_number=$slave->qq($search_number);$display_nr=$slave->qq($display_nr);$brand_id=$slave->qq($brand_id);
        $art_id2=$slave->qq($art_id2);$index2=$slave->qq($index2);
        if ($art_id>0 && $kind>0 && $relation>=0 && $display_nr!="" && $brand_id>0){
            $old_search_number=$search_number;
            $search_number=$this->clearArticle($display_nr);$search_number_up=strtoupper($search_number);
            $old_kind=$kind;
            $new_kind=$this->getBrandKind($brand_id);
            if ($new_kind!=$kind && $new_kind>0){$kind=$new_kind;}
            if ($old_search_number==""){
                $db->query("insert into T2_CROSS (`ART_ID`,`SEARCH_NUMBER`,`KIND`,`BRAND_ID`,`DISPLAY_NR`,`RELATION`) values ('$art_id','$search_number_up','$kind','$brand_id','$display_nr','$relation');");
            }
            if ($old_search_number!=""){
                $db->query("update T2_CROSS set `SEARCH_NUMBER`='$search_number_up',`DISPLAY_NR`='$display_nr',`BRAND_ID`='$brand_id', `KIND`='$kind', `RELATION`='$relation' where `ART_ID`='$art_id' and `SEARCH_NUMBER`='$old_search_number' and `KIND`='$old_kind';");
            }

            if ($art_id2!="" && $index2!=""){$er=0;
                $index2_cl=$this->clearArticle($index2);
                if ($relation==1){$relation=2; $er=1;}if ($relation==2 && $er==0){$relation=1;}
                $brand_id=$this->getBrandId($art_id);
                $db->query("insert into T2_CROSS (`ART_ID`,`SEARCH_NUMBER`,`KIND`,`BRAND_ID`,`DISPLAY_NR`,`RELATION`) values ('$art_id2','$index2_cl','$kind','$brand_id','$index2','$relation');");
            }

            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function dropCatalogueAnalog($art_id,$kind,$relation,$search_number,$brand_id){$db=DbSingleton::getTokoDb();$slave=new slave;$answer=0;$err="Помилка збереження даних!";
        $art_id=$slave->qq($art_id);$kind=$slave->qq($kind);$relation=$slave->qq($relation);$search_number=$slave->qq($search_number);$brand_id=$slave->qq($brand_id);
        if ($art_id>0 && $kind>0 && $relation>=0 && $search_number!="" && $brand_id>0){
            $db->query("delete from T2_CROSS where `ART_ID`='$art_id' and `SEARCH_NUMBER`='$search_number' and `KIND`='$kind' and `BRAND_ID`='$brand_id' and `RELATION`='$relation' limit 1;");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function clearCatalogueAnalogArticle($art_id,$kind,$relation){$db=DbSingleton::getTokoDb();$slave=new slave;$answer=0;$err="Помилка збереження даних!";
        $art_id=$slave->qq($art_id);$kind=$slave->qq($kind);$relation=$slave->qq($relation);
        if ($art_id>0 && $kind>0 && $relation>=0){
            $db->query("delete from T2_CROSS where `ART_ID`='$art_id' and `KIND`='$kind' and `RELATION`='$relation';");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function loadArticleAnalogs($art_id){$db=DbSingleton::getTokoDb(); $ak1=$ak2="";$form="";
        $form_htm=RD."/tpl/catalogue_analog_block.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select t2c.*,t2b.BRAND_NAME 
        from T2_CROSS t2c 
            left outer join T2_BRANDS t2b on t2b.BRAND_ID=t2c.BRAND_ID  
        where t2c.ART_ID='$art_id' and t2c.KIND in (3,4) order by t2c.KIND asc;");$n=$db->num_rows($r);$ak3="";$ak4="";
        for ($i=1;$i<=$n;$i++){
            $search_number=$db->result($r,$i-1,"SEARCH_NUMBER");
            $display_nr=trim($db->result($r,$i-1,"DISPLAY_NR"));
            $kind=$db->result($r,$i-1,"KIND");
            $brand_id=$db->result($r,$i-1,"BRAND_ID");
            $brand_name=$db->result($r,$i-1,"BRAND_NAME");
            $relation=$db->result($r,$i-1,"relation");
            $row="<tr>
                <td>$search_number</td>
                <td><strong>$display_nr</strong></td>
                <td>$brand_name</td>
                <td>$brand_id</td>
                <td align='center'>
                    <button class='btn btn-xs btn-warning btn-bitbucket' onClick='showCatalogueAnalogForm(\"$art_id\",\"$kind\",\"$relation\",\"$search_number\");'><i class='fa fa-edit'></i></button>
                    <button class='btn btn-xs btn-danger btn-bitbucket' onClick='dropCatalogueAnalog(\"$art_id\",\"$kind\",\"$relation\",\"$search_number\",\"$brand_id\",\"$display_nr\");'><i class='fa fa-trash'></i></button>
                </td>
            </tr>";
            if ($kind==3 && $relation==0){$ak1.=$row;}
            if ($kind==4 && $relation==0){$ak2.=$row;}
            if (($kind==3 || $kind==4) && $relation==1){$ak3.=$row;}
            if (($kind==3 || $kind==4) && $relation==2){$ak4.=$row;}
        }
        $form=str_replace("{analog_list_1}",$ak1,$form);
        $form=str_replace("{analog_list_2}",$ak2,$form);
        $form=str_replace("{analog_list_3}",$ak3,$form);
        $form=str_replace("{analog_list_4}",$ak4,$form);
        $form=str_replace("{art_id}",$art_id,$form);
        return $form;
    }

    function loadArticleAplicability($art_id){$db=DbSingleton::getTokoDb();$dbp=DbSingleton::getTokoDb();$tab_list="";$form="";
        $form_htm=RD."/tpl/catalogue_aplicability_block.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$dbp->query("select TYP_ID from T2_LINKS where ART_ID='$art_id';");$n=$dbp->num_rows($r);$typ_id_str="";
        for ($i=1;$i<=$n;$i++){
            $typ_id=$dbp->result($r,$i-1,"TYP_ID");
            $typ_id_str.="$typ_id";if ($i<$n){$typ_id_str.=",";}
        }
        if ($typ_id_str!=""){
            $r=$db->query("select man.MFA_ID, man.MFA_BRAND from T_types tt inner join T_models tm on tm.MOD_ID=tt.TYP_MOD_ID inner join T_manufacturers man on man.MFA_ID=tm.MOD_MFA_ID where tt.TYP_ID in ($typ_id_str) group by man.MFA_ID order by man.MFA_BRAND asc;");$n=$db->num_rows($r);$tab_list="";$cont_list="";
            for ($i=1;$i<=$n;$i++){
                $mfa_id=$db->result($r,$i-1,"MFA_ID");
                $mfa_name=$db->result($r,$i-1,"MFA_BRAND");
                $tab_list.="<li><a data-toggle=\"tab\" href=\"#aplic_tab".$mfa_id."\" onClick=\"loadArticleAplicabilityModels('$art_id','$mfa_id');\"><i class=\"fa fa-car\"></i> $mfa_name</a></li>\n";
                $cont_list.="<div id=\"aplic_tab".$mfa_id."\" class=\"tab-pane \">
                    <div class=\"panel-body\">
                        <div class=\"sk-spinner sk-spinner-wave\"><div class=\"sk-rect1\"></div><div class=\"sk-rect2\"></div><div class=\"sk-rect3\"></div><div class=\"sk-rect4\"></div><div class=\"sk-rect5\"></div></div>
                    </div>
                </div>\n";
            }
        }else {$cont_list="<h3 class='text-center'>Привязка до авто відсутня</h3>";}
        $tab_list.="<a href=\"#aplic_new\" class='btn btn-primary pull-right' onClick=\"loadArticleAplicabilityNew('$art_id');\"><i class=\"fa fa-plus\"></i></a>";
        $form=str_replace("{manuf_tab_list}",$tab_list,$form);
        $form=str_replace("{manuf_cont_list}",$cont_list,$form);
        return $form;
    }

    function getLaIdComment($la_id){$db=DbSingleton::getTokoDb();$comment="";
        $r=$db->query("select TEXT from link_notes where LA_ID='$la_id' and `LANG_ID`='16' and `DISPLAY`=1;");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $comment.=$db->result($r,$i-1,"TEXT")."\n";
        }
        return $comment;
    }

    function loadArticleAplicabilityModels($art_id,$mfa_id){$db=DbSingleton::getTokoDb();$dbp=DbSingleton::getTokoDb();$slave=new slave;$list="";
        $form="";$form_htm=RD."/tpl/catalogue_aplicability_model_block.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $form=str_replace("{mfa_id}",$mfa_id,$form);
        $form=str_replace("{art_id}",$art_id,$form);
        $r=$dbp->query("select TYP_ID,LA_ID from T2_LINKS where ART_ID='$art_id';");$n=$dbp->num_rows($r);$typ_id_str="";$la_id_arr=array();
        for ($i=1;$i<=$n;$i++){
            $typ_id=$dbp->result($r,$i-1,"TYP_ID");$typ_id_str.="$typ_id";if ($i<$n){$typ_id_str.=",";}
            $la_id=$dbp->result($r,$i-1,"LA_ID");
            $la_id_arr[$typ_id]=$la_id;
            //$db->query("insert into link_notes (`LA_ID`,`LANG_ID`,`SORT`,`TEXT_NAME`,`TYPE`,`TEXT`,`DISPLAY`) values ('$la_id_new','16','1','','K','$comment','1');");
        }
        if ($typ_id_str==""){$typ_id_str=0;}
        //$query="select tt.*, tm.TEX_TEXT from T_types tt inner join T_models tm on tm.MOD_ID=tt.TYP_MOD_ID inner join T_manufacturers man on man.MFA_ID=tm.MOD_MFA_ID where tm.MOD_MFA_ID='$mfa_id' and tt.TYP_ID in ($typ_id_str)  order by tt.TYP_TEXT asc";
        $query="select tt.*, tm.TEX_TEXT from T_types tt inner join T_models tm on tm.MOD_ID=tt.TYP_MOD_ID inner join T_manufacturers man on man.MFA_ID=tm.MOD_MFA_ID where tm.MOD_MFA_ID='$mfa_id' and tt.TYP_ID in ($typ_id_str)  order by tt.TYP_TEXT asc";

        $r=$db->query($query);$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $typ_id=$db->result($r,$i-1,"TYP_ID");
            $engine=$db->result($r,$i-1,"TYP_TEXT");
            //$model_id=$db->result($r,$i-1,"TYP_MOD_ID");
            $typ_text=$db->result($r,$i-1,"TYP_MMT_TEXT");
            $fuel=$db->result($r,$i-1,"Fuel");
            $start=$db->result($r,$i-1,"TYP_PCON_START");if ($start==0){$start="";}if (strlen($start)==6){$start=substr($start,0,4).".".substr($start,4,2);}
            $end=$db->result($r,$i-1,"TYP_PCON_END");if ($end==0){$end="";}if (strlen($end)==6){$end=substr($end,0,4).".".substr($end,4,2);}
            $typ_kw_from=$db->result($r,$i-1,"TYP_KW_FROM");
            $typ_hp_from=$db->result($r,$i-1,"TYP_HP_FROM");
            $typ_ccm=$db->result($r,$i-1,"TYP_CCM");
            $eng_cod=$db->result($r,$i-1,"ENG_Cod");
            $la_id=$la_id_arr[$typ_id];
            $la_id_comment=$this->getLaIdComment($la_id);
            $list.="<tr>
                <td>$typ_text</td>
                <td>$engine</td>
                <td>$fuel</td>
                <td>$start - $end</td>
                <td>$typ_kw_from / $typ_hp_from</td>
                <td>$typ_ccm</td>
                <td>$eng_cod</td>
                <td>$la_id_comment <buttom class='btn btn-xs btn-warning' onclick=\"showLaIdCommentForm('$art_id','$typ_id');\"><i class='fa fa-edit'></i></td>
                <td align='center'><button class='btn btn-xs btn-danger btn-bitbucket' title='Відвязати авто' onclick='unlinkArticleAplicabilityModel(\"$mfa_id\",\"$art_id\",\"$typ_id\",\"".$slave->qq($typ_text)."\");'><i class='fa fa-times'></i></button></td>
            </tr>";
        }
        $form=str_replace("{models_list}",$list,$form);
        return $form;
    }

    function unlinkArticleAplicabilityModel($art_id,$typ_id){$db=DbSingleton::getTokoDb();$dbp=DbSingleton::getTokoDb();$answer="Помилка обробки даних";
        if ($art_id>0 && $typ_id>0){
            $query="delete from T2_LINKS where ART_ID='$art_id' and `TYP_ID`='$typ_id';";
            $dbp->query($query);$db->query($query); $answer=1;
        }
        return $answer;
    }

    function clearActicleAplicabilityManuf($art_id,$mfa_id){$db=DbSingleton::getTokoDb();$dbp=DbSingleton::getTokoDb();$answer="Помилка обробки даних";
        if ($art_id>0 && $mfa_id>0){
            $r=$db->query("select tt.TYP_ID from T_types tt left outer join T_models tm on tm.MOD_ID=tt.TYP_MOD_ID where tm.MOD_MFA_ID='$mfa_id';");$n=$db->num_rows($r);
            if ($n>0){
                for ($i=1;$i<=$n;$i++){
                    $typ_id=$db->result($r,$i-1,"TYP_ID");
                    $query="delete from T2_LINKS where ART_ID='$art_id' and `TYP_ID`='$typ_id';";
                    $dbp->query($query);$db->query($query);
                }
                $answer=1;
            }
        }
        return $answer;
    }

    function loadArticleAplicabilityNew($art_id,$index){$level=$parent_id=0;$form="";
        if ($art_id>0){
            $form_htm=RD."/tpl/catalogue_aplicability_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
            $form=str_replace("{art_id}",$art_id,$form);
            $form=str_replace("{display_number}",$index,$form);
            $form=str_replace("{mfa_list}",$this->loadManufList(0),$form);
            list($menu,$menu_kol_elem)=$this->createTDtree_universal($art_id,$level, $parent_id, "");
            $form = str_replace("{menu}", $menu, $form);
            $form=str_replace("{kol_elem}",$menu_kol_elem,$form);
        }
        return array($form,"Привязка до авто");
    }

    function saveCatalogueAplicabilityForm($art_id,$display_number,$comment,$typ_array,$str_array){$db=DbSingleton::getTokoDb();$dbp=DbSingleton::getTokoDb();$slave=new slave;$answer=0;$err="Помилка збереження даних!";
        $art_id=$slave->qq($art_id);$comment=$slave->qq($comment);$typ_array=$slave->qq($typ_array);
        if ($art_id>0){
            //$brand_id=$this->getBrandId($art_id);
            $typ_array=explode(",",$typ_array);
            foreach($typ_array as $typ_id){
                if ($typ_id>0 && $typ_id!=""){
                    $r2=$db->query("select max(LA_ID) as mid from link_notes;");$la_id_new=0+$db->result($r2,0,"mid")+1;
                    $db->query("insert into link_notes (`LA_ID`,`LANG_ID`,`SORT`,`TEXT_NAME`,`TYPE`,`TEXT`,`DISPLAY`) values ('$la_id_new','16','1','','K','$comment','1');");
                    //print "insert into link_notes (`LA_ID`,`LANG_ID`,`SORT`,`TEXT_NAME`,`TYPE`,`TEXT`,`DISPLAY`) values ('$la_id_new','16','1','','K','$comment','1');\n";
                    $dbp->query("insert into T2_LINKS (`TYP_ID`,`ART_ID`,`LA_ID`) values ('$typ_id','$art_id','$la_id_new');");
                    //print "insert into T2_LINKS (`TYP_ID`,`ART_ID`,`LA_ID`) values ('$typ_id','$art_id','$la_id_new');\n";
                    $db->query("insert into T2_LINKS (`TYP_ID`,`ART_ID`,`LA_ID`) values ('$typ_id','$art_id','$la_id_new');");
                    //print "insert into T2_LINKS (`TYP_ID`,`ART_ID`,`LA_ID`) values ('$typ_id','$art_id','$la_id_new');\n";
                }
            }
            $str_array=explode(",",$str_array);
            foreach($str_array as $str_id){
                if ($str_id>0 && $str_id!=""){
                    $dbp->query("insert into T2_TREE (`STR_ID`,`ART_ID`) values ('$str_id','$art_id');");
                    $db->query("insert into T2_TREE (`STR_ID`,`ART_ID`) values ('$str_id','$art_id');");
                }
            }
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function showLaIdCommentForm($art_id,$type_id){$db=DbSingleton::getTokoDb();$dbp=DbSingleton::getTokoDb();$form="";
        if ($art_id>0 && $type_id>0){
            $form_htm=RD."/tpl/catalogue_laid_comment_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
            $form=str_replace("{art_id}",$art_id,$form);
            $form=str_replace("{type_id}",$type_id,$form);

            $r=$dbp->query("select LA_ID from T2_LINKS where ART_ID='$art_id' and TYP_ID='$type_id';");$n=$dbp->num_rows($r);$laIdArr="0";
            for ($i=1;$i<=$n;$i++){
                $la_id=$dbp->result($r,$i-1,"LA_ID");
                $laIdArr.=",".$la_id;
            }
            $r=$db->query("select * from link_notes where LA_ID in ($laIdArr) and DISPLAY='1' and LANG_ID='16';");$n=$db->num_rows($r);$list="";
            for ($i=1;$i<=$n+1;$i++){$la_id=0;$sort="";	$text_name="";	$type="";$text="";$button="";
                if($i<=$n){
                    $la_id=$db->result($r,$i-1,"LA_ID");
                    $sort=$db->result($r,$i-1,"SORT");
                    $text_name=$db->result($r,$i-1,"TEXT_NAME");
                    $type=$db->result($r,$i-1,"TYPE");
                    $text=$db->result($r,$i-1,"TEXT");
                    $button="<button class='btn btn-xs btn-danger' onclick='dropLaIdComment(\"$art_id\",\"$type_id\",\"$la_id\",\"$sort\",\"$type\")'><i class='fa fa-trash'></i></botton>";
                }
                $list.="<div class='form-group'>
                    <input type='hidden' id='la_id_$i' value='$la_id'>
                    <label class='col-sm-1 control-label'>Позиція</label>
                    <div class='col-sm-1'><input class='form-control' type='text' id='sort_$i' value='$sort'></div>
                    <label class='col-sm-1 control-label'>Тип</label>
                    <div class='col-sm-1'><input class='form-control' type='text' placeholder='K' id='type_$i' value='$type'></div>
                    <label class='col-sm-1 control-label'>Параметр</label>
                    <div class='col-sm-2'><input class='form-control' type='text' id='text_name_$i' value='$text_name'></div>
                    <label class='col-sm-1 control-label'>Значення</label>
                    <div class='col-sm-3'><input class='form-control' type='text' id='text_$i' value='$text'></div>
                    <div class='col-sm-1'>$button</div>
                </div>";
            }
            $form=str_replace("{list_la_comment}",$list,$form);
            $form=str_replace("{kol_elem}",($n+1),$form);
        }
        return array($form,"Коментарі LA_ID");
    }

    function saveLaIdCommentForm($art_id,$type_id,$kol,$la_ids,$sorts,$types,$text_names,$texts){$db=DbSingleton::getTokoDb();$dbp=DbSingleton::getTokoDb();$slave=new slave;$answer=0;$err="Помилка збереження даних!";
        $art_id=$slave->qq($art_id);$type_id=$slave->qq($type_id);$kol=$slave->qq($kol);$la_ids=$slave->qq($la_ids);$sorts=$slave->qq($sorts);$types=$slave->qq($types);$text_names=$slave->qq($text_names);$texts=$slave->qq($texts);
        if ($art_id>0 && $type_id>0){
            for ($i=1;$i<=$kol;$i++){
                $la_id=$la_ids[$i];
                $sort=$sorts[$i];
                $type=$types[$i];
                $text_name=$text_names[$i];
                $text=$texts[$i];
                if ($la_id!="" && $la_id>0){
                    $db->query("update link_notes set `SORT`='$sort',`TEXT_NAME`='$text_name',`TYPE`='$type',`TEXT`='$text' where LA_ID='$la_id' and `DISPLAY`='1' and `LANG_ID`='16';");
                }
                if ($la_id==0 && $text!="" and $type!="" and $sort!=""){
                    $r2=$db->query("select max(LA_ID) as mid from link_notes;");$la_id_new=0+$db->result($r2,0,"mid")+1;
                    $db->query("insert into link_notes (`LA_ID`,`LANG_ID`,`SORT`,`TEXT_NAME`,`TYPE`,`TEXT`,`DISPLAY`) values ('$la_id_new','16','$sort','$text_name','$type','$text','1');");
                    $dbp->query("insert into T2_LINKS (`TYP_ID`,`ART_ID`,`LA_ID`) values ('$type_id','$art_id','$la_id_new');");
                    $db->query("insert into T2_LINKS (`TYP_ID`,`ART_ID`,`LA_ID`) values ('$type_id','$art_id','$la_id_new');");
                }
            }
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function dropLaIdComment($art_id,$type_id,$la_id,$sort,$type){$db=DbSingleton::getTokoDb();$slave=new slave;$answer=0;$err="Помилка збереження даних!";
        $art_id=$slave->qq($art_id);$type_id=$slave->qq($type_id);$la_id=$slave->qq($la_id);$sort=$slave->qq($sort);$type=$slave->qq($type);
        if ($art_id>0 && $type_id>0 && $la_id>0){
            $db->query("delete from link_notes where LA_ID='$la_id' and `DISPLAY`='1' and `LANG_ID`='16' and `SORT`='$sort' and `TYPE`='$type' limit 1;");
            //$dbp->query("delete from T2_LINKS where `TYP_ID`='$type_id' and `ART_ID`='$art_id' and `LA_ID`='$la_id' limit 1;");
            //$db->query("delete from T2_LINKS where `TYP_ID`='$type_id' and `ART_ID`='$art_id' and `LA_ID`='$la_id' limit 1;");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function loadManufList($sel_id){$db=DbSingleton::getTokoDb();$list="";
        $r=$db->query("select MFA_ID, MFA_BRAND from T_manufacturers order by MFA_BRAND asc;");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $mfa_id=$db->result($r,$i-1,"MFA_ID");
            $mfa_name=$db->result($r,$i-1,"MFA_BRAND");
            $list.="<option value='$mfa_id' onClick='loadAplicabilityModelList(\"$mfa_id\")'>$mfa_name</option>";
        }
        return $list;
    }

    function loadAplicabilityModelList($mfa_id,$sel_id){$db=DbSingleton::getTokoDb();$list="";
        $r=$db->query("select MOD_ID, TEX_TEXT from T_models where MOD_MFA_ID='$mfa_id' order by TEX_TEXT asc;");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $mod_id=$db->result($r,$i-1,"MOD_ID");
            $mod_name=$db->result($r,$i-1,"TEX_TEXT");
            $list.="<option value='$mod_id' onClick='loadAplicabilityModificationList(\"$mfa_id\",\"$mod_id\",)'>$mod_name</option>";
        }
        return $list;
    }

    function loadAplicabilityModificationList($mfa_id,$mod_id,$sel_id){$db=DbSingleton::getTokoDb();$list="";
        $r=$db->query("select * from T_types where TYP_MOD_ID='$mod_id' order by TYP_MMT_TEXT asc;");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $typ_id=$db->result($r,$i-1,"TYP_ID");
            $name="";
            if ($db->result($r,$i-1,"Fuel")!=""){$name.=$db->result($r,$i-1,"Fuel")." | ";}
            $name.=$db->result($r,$i-1,"TYP_TEXT")." | ";
            $name.="(".substr($db->result($r,$i-1,"TYP_PCON_START"),0,4).".".substr($db->result($r,$i-1,"TYP_PCON_START"),4,2);
            $name.="-";
            if (strlen($db->result($r,$i-1,"TYP_PCON_END"))>1){
                $name.=substr($db->result($r,$i-1,"TYP_PCON_END"),0,4).".".substr($db->result($r,$i-1,"TYP_PCON_END"),4,2).") | ";
            }else{$name.="&infin;) | ";}
            $name.=$db->result($r,$i-1,"TYP_HP_FROM")."HP/";
            $name.=$db->result($r,$i-1,"TYP_KW_FROM")."kW | ";
            $name.=$db->result($r,$i-1,"TYP_CCM")."см<sup>3</sup> | ";
            $name.=$db->result($r,$i-1,"ENG_Cod");
            $list.="<li><input type='checkbox' id='modif$i' value='$typ_id'> $name</li>";
        }if ($n>0){$list="<li><input id='modif0' value='0' onclick=\"checkModifAll(this)\" type=\"checkbox\"> - Відмітити все</li>".$list."<input type='hidden' id='modif_kol' value='$n'>";}
        return $list;
    }

    function loadArticleLogistic($art_id){$db=DbSingleton::getTokoDb();$form="";
        $form_htm=RD."/tpl/catalogue_logistic.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select * from T2_PACKAGING where ART_ID='$art_id' limit 0,1;");
        $index_pack=$db->result($r,0,"INDEX_PACK");
        $height=$db->result($r,0,"HEIGHT");
        $length=$db->result($r,0,"LENGTH");
        $width=$db->result($r,0,"WIDTH");
        $volume=$db->result($r,0,"VOLUME");
        $weight_netto=$db->result($r,0,"WEIGHT_NETTO");
        $weight_brutto=$db->result($r,0,"WEIGHT_BRUTTO");
        $necessary_amount_car=$db->result($r,0,"NECESSARY_AMOUNT_CAR");
        $units_id=$db->result($r,0,"UNITS_ID");
        $multiplicity_package=$db->result($r,0,"MULTIPLICITY_PACKAGE");
        $shoulder_delivery=$db->result($r,0,"SHOULDER_DELIVERY");
        $general_quant=$db->result($r,0,"GENERAL_QUANT");
        $form=str_replace("{art_id}",$art_id,$form);
        $form=str_replace("{index_pack}",$index_pack,$form);
        $form=str_replace("{height}",$height,$form);
        $form=str_replace("{length}",$length,$form);
        $form=str_replace("{width}",$width,$form);
        $form=str_replace("{volume}",$volume,$form);
        $form=str_replace("{weight_netto}",$weight_netto,$form);
        $form=str_replace("{weight_brutto}",$weight_brutto,$form);
        $form=str_replace("{necessary_amount_car}",$necessary_amount_car,$form);
        $form=str_replace("{units_list}",$this->showUnitsListSelect($units_id,1),$form);
        $form=str_replace("{multiplicity_package}",$multiplicity_package,$form);
        $form=str_replace("{shoulder_delivery}",$shoulder_delivery,$form);
        $form=str_replace("{general_quant}",$general_quant,$form);
        $form=str_replace("{work_pair_list}",$this->showWorkPairForm($art_id),$form);
        return $form;
    }

    function showWorkPairForm($art_id){$db=DbSingleton::getTokoDb();$list="";
        $r=$db->query("select PAIR_INDEX from T2_WORK_PAIR where ART_ID='$art_id';");$n=$db->num_rows($r);
        for ($i=1;$i<=$n+3;$i++){
            $pair_index="";
            if ($i<=$n){$pair_index=$db->result($r,$i-1,"PAIR_INDEX");}
            $list.="<tr><td><input type='text' id='work_pair_$i' value='$pair_index' class='form-control'></td></tr>";
        }
        $list.="<input type='hidden' id='work_pair_n' value='".($n+3)."'>";
        return $list;
    }

    function saveCatalogueLogistic($art_id,$index_pack,$height,$length,$width,$volume,$weight_netto,$weight_brutto,$necessary_amount_car,$units_id,$multiplicity_package,$shoulder_delivery,$general_quant,$work_pair){$db=DbSingleton::getTokoDb();$slave=new slave;$answer=0;$err="Помилка збереження даних!";
        $art_id=$slave->qq($art_id);$index_pack=$slave->qq($index_pack);$height=$slave->qq($slave->point_valid($height));$length=$slave->qq($slave->point_valid($length));$width=$slave->qq($slave->point_valid($width));$volume=$slave->qq($slave->point_valid($volume));$weight_netto=$slave->qq($slave->point_valid($weight_netto));$weight_brutto=$slave->qq($slave->point_valid($weight_brutto));$necessary_amount_car=$slave->qq($necessary_amount_car);$units_id=$slave->qq($units_id);
        $multiplicity_package=$slave->qq($multiplicity_package);$shoulder_delivery=$slave->qq($shoulder_delivery);$general_quant=$slave->qq($general_quant);
        if ($art_id>0){
            //T2_PACKAGING UPDATE
            $r=$db->query("select * from `T2_PACKAGING` where `ART_ID`='$art_id' limit 0,1;");$n=$db->num_rows($r);
            if ($n==0){
                $db->query("insert into T2_PACKAGING (`ART_ID`,`INDEX_PACK`,`HEIGHT`,`LENGTH`,`WIDTH`,`VOLUME`,`WEIGHT_NETTO`,`WEIGHT_BRUTTO`,`NECESSARY_AMOUNT_CAR`,`UNITS_ID`,`MULTIPLICITY_PACKAGE`,`SHOULDER_DELIVERY`,`GENERAL_QUANT`) values ('$art_id','$index_pack','$height','$length','$width','$volume','$weight_netto','$weight_brutto','$necessary_amount_car','$units_id','$multiplicity_package','$shoulder_delivery','$general_quant');");
            }
            if ($n==1){
                $db->query("update T2_PACKAGING set `INDEX_PACK`='$index_pack', `HEIGHT`='$height', `LENGTH`='$length', `WIDTH`='$width', `VOLUME`='$volume', `WEIGHT_NETTO`='$weight_netto', `WEIGHT_BRUTTO`='$weight_brutto', `NECESSARY_AMOUNT_CAR`='$necessary_amount_car', `UNITS_ID`='$units_id', `MULTIPLICITY_PACKAGE`='$multiplicity_package', `SHOULDER_DELIVERY`='$shoulder_delivery', `GENERAL_QUANT`='$general_quant' where `ART_ID`='$art_id';");
            }
            if ($work_pair!=""){
                $db->query("delete from T2_WORK_PAIR where ART_ID='$art_id';");
                foreach ($work_pair as $wp){
                    if ($wp!=""){
                        $db->query("insert into T2_WORK_PAIR  (`ART_ID`,`PAIR_INDEX`) values ('$art_id','$wp');");
                    }
                }
            }
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function getCountryName($sel_id){$db=DbSingleton::getTokoDb();$name="";
        $r=$db->query("select COUNTRY_NAME from T2_COUNTRIES where COUNTRY_ID='$sel_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){ $name=$db->result($r,0,"COUNTRY_NAME");}
        return $name;
    }

    function getCountryAbr($sel_id){$db=DbSingleton::getTokoDb();$abr="";
        $r=$db->query("select ALFA2 from T2_COUNTRIES where COUNTRY_ID='$sel_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){ $abr=$db->result($r,0,"ALFA2");}
        return $abr;
    }

    function showCountryManual($sel_id){$db=DbSingleton::getTokoDb();$manual=new manual;
        $form="";$form_htm=RD."/tpl/catalogue_country_list.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
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

    function showCountryForm($id){$db=DbSingleton::getTokoDb();$manual=new manual;
        $form="";$form_htm=RD."/tpl/catalogue_country_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
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

    function saveCatalogueCountryForm($id,$name,$alfa2,$alfa3,$duty,$risk){$db=DbSingleton::getTokoDb();$slave=new slave;$answer=0;$err="Помилка збереження даних!";
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

    function dropCountry($id){$db=DbSingleton::getTokoDb();$slave=new slave;$answer=0;$err="Помилка видалення запису!";
        $id=$slave->qq($id);
        if ($id>0){
            $r=$db->query("select * from T2_COUNTRIES where COUNTRY_ID='$id' limit 0,1;");$n=$db->num_rows($r);
            if ($n==1){
                $db->query("delete from T2_COUNTRIES where COUNTRY_ID='$id';");
                $answer=1;$err="";
            }
        }
        return array($answer,$err);
    }

    function showDocumentCountryManual($sel_id,$pos){$db=DbSingleton::getTokoDb();$manual=new manual;
        $form="";$form_htm=RD."/tpl/documents_country_list.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select * from T2_COUNTRIES order by COUNTRY_NAME asc;");$n=$db->num_rows($r);$list="";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"COUNTRY_ID");
            $name=$db->result($r,$i-1,"COUNTRY_NAME");
            $alfa2=$db->result($r,$i-1,"ALFA2");
            $alfa3=$db->result($r,$i-1,"ALFA3");
            $duty=$manual->getManualMCaption("DUTY",$db->result($r,$i-1,"DUTY"));
            $risk=$manual->getManualMCaption("RISK",$db->result($r,$i-1,"RISK"));
            $sel="";if ($sel_id==$id){$sel=" style='background-color:#d5fdf5'";}
            $list.="<tr onClick='selectCountryDocument(\"$id\",\"$alfa2\")' $sel>
                <td>$id</td>
                <td>$name</td>
                <td>$alfa2</td>
                <td>$alfa3</td>
                <td>$duty</td>
                <td>$risk</td>
            </tr>";
        }
        $form=str_replace("{list}",$list,$form);
        $form=str_replace("{pos}",$pos,$form);
        return $form;
    }

    function getCostumsCode($sel_id){$db=DbSingleton::getTokoDb();$code="";
        $r=$db->query("select COSTUMS_CODE from T2_COSTUMS where COSTUMS_ID='$sel_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){ $code=$db->result($r,0,"COSTUMS_CODE");}
        return $code;
    }

    function showCostumsManual($sel_id){$db=DbSingleton::getTokoDb();$manual=new manual;
        $form="";$form_htm=RD."/tpl/catalogue_costums_list.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select * from T2_COSTUMS order by COSTUMS_NAME asc;");$n=$db->num_rows($r);$list="";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"COSTUMS_ID");
            $code=$db->result($r,$i-1,"COSTUMS_CODE");
            $name=$db->result($r,$i-1,"COSTUMS_NAME");
            $preferential_rate=$db->result($r,$i-1,"PREFERENTIAL_RATE");
            $full_rate=$db->result($r,$i-1,"FULL_RATE");
            $sertification=$manual->getManualMCaption("costums_sertification",$db->result($r,$i-1,"SERTIFICATION"));
            $gos_standart=$manual->getManualMCaption("costums_gos_standart",$db->result($r,$i-1,"GOS_STANDART"));
            $type_declaration=$manual->getManualMCaption("costums_type_declaration",$db->result($r,$i-1,"TYPE_DECLARATION"));
            $sel="";if ($sel_id==$id){$sel=" style='background-color:#d5fdf5'";}
            $list.="<tr class='pointer' onClick='selectCostums(\"$id\",\"$code\")' $sel>
                <td>$code</td>
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

    function showCostumsForm($id){$db=DbSingleton::getTokoDb();$manual=new manual;
        $form="";$form_htm=RD."/tpl/catalogue_costums_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select * from T2_COSTUMS where COSTUMS_ID='$id' limit 0,1;");
        $code=$db->result($r,0,"COSTUMS_CODE");
        $name=$db->result($r,0,"COSTUMS_NAME");
        $preferential_rate=$db->result($r,0,"PREFERENTIAL_RATE");
        $full_rate=$db->result($r,0,"FULL_RATE");
        $sertification=$db->result($r,0,"SERTIFICATION");
        $gos_standart=$db->result($r,0,"GOS_STANDART");
        $type_declaration=$db->result($r,0,"TYPE_DECLARATION");
        $form=str_replace("{id}",$id,$form);
        $form=str_replace("{code}",$code,$form);
        $form=str_replace("{name}",$name,$form);
        $form=str_replace("{preferential_rate}",$preferential_rate,$form);
        $form=str_replace("{full_rate}",$full_rate,$form);
        $form=str_replace("{sertification}",$sertification,$form);
        $form=str_replace("{sertification_caption}",$manual->getManualMCaption("costums_sertification",$sertification),$form);
        $form=str_replace("{gos_standart}",$gos_standart,$form);
        $form=str_replace("{gos_standart_caption}",$manual->getManualMCaption("costums_gos_standart",$gos_standart),$form);
        $form=str_replace("{type_declaration}",$type_declaration,$form);
        $form=str_replace("{type_declaration_caption}",$manual->getManualMCaption("costums_type_declaration",$type_declaration),$form);
        return array($form,"Форма митного коду УКТЕЗД");
    }

    function saveCatalogueCostumsForm($id,$code,$name,$preferential_rate,$full_rate,$type_declaration,$sertification,$gos_standart){$db=DbSingleton::getTokoDb();$slave=new slave;$answer=0;$err="Помилка збереження даних!";
        $id=$slave->qq($id);$code=$slave->qq($code);$name=$slave->qq($name);$preferential_rate=$slave->qq($slave->point_valid($preferential_rate));$full_rate=$slave->qq($slave->point_valid($full_rate));$type_declaration=$slave->qq($type_declaration);$sertification=$slave->qq($sertification);$gos_standart=$slave->qq($gos_standart);
        if ($id>0){
            $r=$db->query("select * from `T2_COSTUMS` where `COSTUMS_ID`='$id' limit 0,1;");$n=$db->num_rows($r);
            if ($n==0){
                $db->query("insert into T2_COSTUMS (`COSTUMS_ID`,`COSTUMS_CODE`,`COSTUMS_NAME`,`PREFERENTIAL_RATE`,`FULL_RATE`,`SERTIFICATION`,`GOS_STANDART`,`TYPE_DECLARATION`) values ('$id','$code','$name','$preferential_rate','$full_rate','$sertification','$gos_standart','$type_declaration');");
            }
            if ($n==1){
                $db->query("update T2_COSTUMS set `COSTUMS_CODE`='$code', `COSTUMS_NAME`='$name', `PREFERENTIAL_RATE`='$preferential_rate', `FULL_RATE`='$full_rate', `SERTIFICATION`='$sertification', `GOS_STANDART`='$gos_standart', `TYPE_DECLARATION`='$type_declaration' where `COSTUMS_ID`='$id';");
            }
            $answer=1;$err="";
        }
        if ($id=="" && $name!=""){
            $db->query("insert into T2_COSTUMS (`COSTUMS_ID`,`COSTUMS_CODE`,`COSTUMS_NAME`,`PREFERENTIAL_RATE`,`FULL_RATE`,`SERTIFICATION`,`GOS_STANDART`,`TYPE_DECLARATION`) values ('$id','$code','$name','$preferential_rate','$full_rate','$sertification','$gos_standart','$type_declaration');"); $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function dropCostums($id){$db=DbSingleton::getTokoDb();$slave=new slave;$answer=0;$err="Помилка видалення запису!";
        $id=$slave->qq($id);
        if ($id>0){
            $r=$db->query("select * from T2_COSTUMS where COSTUMS_ID='$id' limit 0,1;");$n=$db->num_rows($r);
            if ($n==1){
                $db->query("delete from T2_COSTUMS where COSTUMS_ID='$id';");
                $answer=1;$err="";
            }
        }
        return array($answer,$err);
    }

    function showDocumentCostumsManual($sel_id,$pos){$db=DbSingleton::getTokoDb();$manual=new manual;
        $form="";$form_htm=RD."/tpl/documents_costums_list.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select * from T2_COSTUMS order by COSTUMS_NAME asc;");$n=$db->num_rows($r);$list="";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"COSTUMS_ID");
            $code=$db->result($r,$i-1,"COSTUMS_CODE");
            $name=$db->result($r,$i-1,"COSTUMS_NAME");
            $preferential_rate=$db->result($r,$i-1,"PREFERENTIAL_RATE");
            $full_rate=$db->result($r,$i-1,"FULL_RATE");
            $sertification=$manual->getManualMCaption("costums_sertification",$db->result($r,$i-1,"SERTIFICATION"));
            $gos_standart=$manual->getManualMCaption("costums_gos_standart",$db->result($r,$i-1,"GOS_STANDART"));
            $type_declaration=$manual->getManualMCaption("costums_type_declaration",$db->result($r,$i-1,"TYPE_DECLARATION"));
            $sel="";if ($sel_id==$id){$sel=" style='background-color:#d5fdf5'";}
            $list.="<tr class='pointer' style='cursor:pointer;' onClick='selectCostumsDocument(\"$id\",\"$code\")' $sel>
                <td>$code</td>
                <td>$name</td>
                <td align='right'>$preferential_rate</td>
                <td align='right'>$full_rate</td>
                <td align='center'>$type_declaration</td>
                <td align='center'>$sertification</td>
                <td align='center'>$gos_standart</td>
            </tr>";
        }
        $form=str_replace("{list}",$list,$form);
        $form=str_replace("{pos}",$pos,$form);
        return $form;
    }

    function getPriceRatingArray(){$db=DbSingleton::getTokoDb();$rating_ar=array();
        $r=$db->query("select * from T2_PRICE_RATING order by abr,id asc;");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $abr=$db->result($r,$i-1,"abr");
            $name=$db->result($r,$i-1,"name");
            $rating_ar[$i]["id"]=$id;
            $rating_ar[$i]["abr"]=$abr;
            $rating_ar[$i]["name"]=$name;
        }
        return $rating_ar;
    }

    function loadArticlePricing($art_id){$db=DbSingleton::getTokoDb();
        $form="";$form_htm=RD."/tpl/catalogue_pricing.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);} $list_price_rating="";
        $form=str_replace("{art_id}",$art_id,$form);
        $rating_ar=$this->getPriceRatingArray();$kol_rating=0;
        foreach ($rating_ar as $rar){$kol_rating+=1; $list_price_rating.="<th class='text-center' title='".$rar["name"]."'>Прайс<br>".$rar["abr"]."</th>"; }
        $form=str_replace("{list_price_rating}",$list_price_rating,$form);

        list($article_nr_displ,$brand_id,$brand_name,$article_nr_search)=$this->getArticleNrDisplBrand($art_id);
        $r=$db->query("select t2c.ART_ID,t2c.KIND,t2c.RELATION from T2_CROSS t2c 
            left outer join T2_BRANDS t2b on t2b.BRAND_ID=t2c.BRAND_ID 
        where t2c.SEARCH_NUMBER='$article_nr_search' order by t2c.KIND asc;");$n=$db->num_rows($r);
        $ak=array();$rk=array(); $art_id_str="";
        for ($i=1;$i<=$n;$i++){
            $ART_ID=$db->result($r,$i-1,"ART_ID");
            $KIND=$db->result($r,$i-1,"KIND");
            $RELATION=$db->result($r,$i-1,"RELATION");
            $art_id_str.="'$ART_ID'";if ($i<$n){$art_id_str.=",";}
            if (($ak[$ART_ID]=="") || $KIND==0){$ak[$ART_ID]=$KIND;}
            if (($rk[$ART_ID]=="") || $RELATION==0){$rk[$ART_ID]=$RELATION;}
        }
        if ($art_id_str==""){$art_id_str=0;}
        $r=$db->query("select t2a.ART_ID,t2a.BRAND_ID,t2a.ARTICLE_NR_DISPL, t2b.BRAND_NAME from T2_ARTICLES t2a 
            left outer join T2_BRANDS t2b on t2b.BRAND_ID=t2a.BRAND_ID 
            left outer join T2_BARCODES t2bc on t2bc.ART_ID=t2a.ART_ID 
        where t2a.ART_ID in ($art_id_str);");$n=$db->num_rows($r);$ak0="";$ak1="";$ak2="";$ak3="";$ak4="";
        for ($i=1;$i<=$n;$i++){
            $analog_art_id=$db->result($r,$i-1,"ART_ID");
            //$search_number=$db->result($r,$i-1,"SEARCH_NUMBER");
            $display_nr=trim($db->result($r,$i-1,"ARTICLE_NR_DISPL"));
            //$brand_id=$db->result($r,$i-1,"BRAND_ID");
            $brand_name=$db->result($r,$i-1,"BRAND_NAME");
            $kind_id=$ak[$analog_art_id];
            $relation=$rk[$analog_art_id];

            list($article_oper_price,$article_storage_amount)=$this->getArticleOperPriceGeneralStock($analog_art_id);
            if ($article_oper_price>0){
                //$article_sales="Відсутня інформація";
                $article_income_amount=0;
                //$minMarkUp=0;

                list($template_price_rating_id,$data_use,$author_id,$minMarkup,$prAr)=$this->getArticlePriceRating($analog_art_id);
                $author_name=$this->getMediaUserName($author_id);
                $template_price_rating_select=$this->showPriceRatingTemplateSelect($analog_art_id,$template_price_rating_id);

                //$rating_price=0;$rating_persent=0;
                $row2="";
                $row="<tr>
                    <td rowspan=2><input type='hidden' id='$rp_$i' value='$analog_art_id'><strong>$display_nr<br>$brand_name</strong></td>
                    <td><input type='text' class='form-control input-xs price_numbers nrCell' readonly id='artStorageAmount_$analog_art_id' value='$article_storage_amount'></td>
                    <td>$article_income_amount</td>
                    <td><input type='text' class='form-control input-xs price_numbers nrCell' readonly id='artOperPrice_$analog_art_id' value='$article_oper_price'></td>";
                for ($k=1;$k<=$this->kol_price_rating;$k++){
                    $rating_price=$prAr[$k]["price"];
                    $rating_persent=$prAr[$k]["persent"];
                    $row.="<td><input type='text' class='form-control input-xs price_numbers nrCell' id='artRatingPrice_".$analog_art_id."_$k' value='$rating_price' onKeyup='recalcPRArt(\"$analog_art_id\",\"$k\",\"1\")'></td>";
                    $row2.="<td><input type='text' class='form-control input-xs price_numbers nrCell' id='artRatingPersent_".$analog_art_id."_$k' value='$rating_persent' onKeyup='recalcPRArt(\"$analog_art_id\",\"$k\",\"0\")'></td>";
                }
                $row2="<tr>
                    <td colspan=3 align='right'>Шаблон націнки: $template_price_rating_select %</td>".$row2."
                    <td id='artDataUpdate_".$analog_art_id."'>$data_use</td>
                </tr>";
                $sales=$this->getLineArticleSales($analog_art_id);
                $row.="
                    <td rowspan=2><button class='btn btn-xs btn-default btn-block' onClick='saveArticlePriceRating(\"$analog_art_id\");return false;'><i class='fa fa-save'></i></button><button class='btn btn-xs btn-default btn-block' onClick='showArticlePriceRatingHistory(\"$analog_art_id\")'><i class='fa fa-table'></i></button></td>
                    <td rowspan=2><a href='#articleSales' onClick='showArticleSales(\"$analog_art_id\")'>$sales</a></td>
                    <td rowspan=2><input type='text' class='form-control input-xs price_numbers nrCell' id='artMinMarkUp_$analog_art_id' value='$minMarkup'></td>
                    <td id='artAuthorName_".$analog_art_id."'>$author_name</td>
                </tr>".$row2;

                if ($kind_id==0 && $relation==0){$ak0.=$row;}
                if ($kind_id==1 && $relation==0){$ak1.=$row;}
                if ($kind_id==3 && $relation==0){$ak2.=$row;}
                if ($kind_id==4 && $relation==0){$ak3.=$row;}
            }
        }
        $articles_list=$ak0.$ak1.$ak2.$ak3.$ak4;
        $form=str_replace("{articles_list}",$articles_list,$form);
        return $form;
    }

    function saveArticlePriceRating($art_id,$kol_elm,$template_id,$minMarkup,$prc,$prs){$db=DbSingleton::getTokoDb();$slave=new slave;session_start();$user_id=$_SESSION["media_user_id"];$answer=0;$err="Помилка збереження даних!";
        $art_id=$slave->qq($art_id);$kol_elm=$slave->qq($kol_elm);$template_id=$slave->qq($template_id);$minMarkup=$slave->qq($minMarkup);$prc=$slave->qq($prc);$prs=$slave->qq($prs);
        if ($art_id>0){
            $r=$db->query("select * from T2_ARTICLES_PRICE_RATING where art_id='$art_id' and in_use='1' limit 0,1;");$n=$db->num_rows($r);
            if ($n==1){
                $db->query("update T2_ARTICLES_PRICE_RATING set in_use='0' where art_id='$art_id' and in_use='1';");
            }
            $query="insert into T2_ARTICLES_PRICE_RATING (`art_id`,`in_use`,`data_update`,`user_id`,`template_id`,`minMarkup`";
            for ($i=1;$i<=$kol_elm;$i++){ $query.=",`price_$i`,`persent_$i`"; }
            $query.=") values ('$art_id','1',CURDATE(),'$user_id','$template_id','$minMarkup'";
            for ($i=1;$i<=$kol_elm;$i++){
                $price=$prc[$i];$percent=$prs[$i];
                $query.=",'$price','$percent'";
            } $query.=");";
            $db->query($query);
            $answer=1;$err="";
        }
        return array($answer,$err,$this->getMediaUserName($user_id),date("Y-m-d"));
    }

    function getArticlePriceRating($art_id){$db=DbSingleton::getTokoDb();$template_id=0;$user_id=0;$prAr=array();$kol_elm=$this->kol_price_rating;$data_update="";$minMarkup=0;
        $r=$db->query("select * from T2_ARTICLES_PRICE_RATING where art_id='$art_id' and in_use='1' limit 0,1");$n=$db->num_rows($r);
        if ($n==1){
            $user_id=$db->result($r,0,"user_id");
            $data_update=$db->result($r,0,"data_update");
            $template_id=$db->result($r,0,"template_id");
            $minMarkup=$db->result($r,0,"minMarkup");
            for ($i=1;$i<=$kol_elm;$i++){
                $prAr[$i]["price"]=$data=$db->result($r,0,"price_$i");
                $prAr[$i]["persent"]=$db->result($r,0,"persent_$i");
            }
        }
        return array($template_id,$data_update,$user_id,$minMarkup,$prAr);
    }

    function showPriceRatingTemplateSelect($art_id,$sel_id){$db=DbSingleton::getTokoDb();$list="";
        $form="<select id='priceRatingTemplate_".$art_id."' onChange='loadPriceRatingTemplate(\"$art_id\")' class='input-xs' style='width:100px;'><option value=0>-- -- --</option>{list}</select>";
        $r=$db->query("select * from price_rating_template where status='1' order by id asc;");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"name");
            $sel="";if ($id==$sel_id){$sel=" selected='selected'";}
            $list.="<option value='$id' $sel>$name</option>";
        }
        $form=str_replace("{list}",$list,$form);
        return $form;
    }

    function loadPriceRatingTemplateStr($art_id,$sel_id){$db=DbSingleton::getTokoDb();$min_markup=0;$kol_val=$this->kol_price_rating;$rating=[];
        $answer=0;$err="Помилка!";
        $r=$db->query("select * from price_rating_template_str where template_id='$sel_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $min_markup=$db->result($r,0,"min_markup");
            for ($i=0;$i<=$kol_val;$i++){
                $rating[$i]=$db->result($r,0,"rating_".$i);
            }
            $answer=1;$err="";
        }
        return array($answer,$err,$min_markup,$kol_val,$rating);
    }

    function showArticlePriceRatingHistory($art_id){$db=DbSingleton::getTokoDb();$answer=0;$err="Помилка індексу";
        $form="";$form_htm=RD."/tpl/catalogue_pricing_history.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        if ($art_id!=""){
            $r=$db->query("select * from T2_ARTICLES_PRICE_RATING where art_id='$art_id' order by data_update desc, id desc;");$n=$db->num_rows($r);$list="";
            for ($i=1;$i<=$n;$i++){
                $in_use=$db->result($r,$i-1,"in_use"); $clr="";if ($in_use==1){$clr=" style='background-color:#bce9e0;'";}
                $data_update=$db->result($r,$i-1,"data_update");
                $author=$this->getMediaUserName($db->result($r,$i-1,"user_id"));
                $template_id=trim($db->result($r,$i-1,"template_id"));
                $minMarkup=$db->result($r,$i-1,"minMarkup");
                $list.="<tr $clr>
                    <td>$data_update</td>
                    <td>$author</td>
                    <td>$template_id</td>
                    <td>$minMarkup</td>";
                for ($k=1;$k<=$this->kol_price_rating;$k++){
                    $price=$db->result($r,$i-1,"price_$k");
                    $persent=$db->result($r,$i-1,"persent_$k");
                    $list.="<td>$price<br>$persent</td>";
                }
            }
            $form=str_replace("{list}",$list,$form);
            $form=str_replace("{kol_elm}",$this->kol_price_rating,$form);
            $answer=1;$err="";
        }
        return array($answer,$err,$form,"Історія ціноутворення");
    }

    function getTpointName($tpoint_id){$db=DbSingleton::getDb(); $name="";
        $r=$db->query("select * from T_POINT where id='$tpoint_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$name=$db->result($r,0,"full_name");}
        return $name;
    }

    function getLineArticleSales($art_id){$db=DbSingleton::getTokoDb();$list="";
        if ($art_id!=""){
            for ($m=1;$m<=24;$m++){
                $month=date("Y-m-00",strtotime("-$m month"));
                $r=$db->query("select sum(AMOUNT) as sum_amount from T2_ARTICLES_SALES where art_id='$art_id' and MONTH='$month' group by `MONTH`;");$n=$db->num_rows($r);
                for ($i=1;$i<=$n;$i++){
                    $amount=$db->result($r,$i-1,"sum_amount");
                    $list.="$amount";
                }$list.=";";
            }
        }
        return $list;
    }

    function showArticleSales($art_id){$db=DbSingleton::getTokoDb();$answer=0;$err="Помилка індексу";$list="";$m=0;$article_displ_nomber="";
        $block="";$block_htm=RD."/tpl/catalogue_article_sales.htm";if (file_exists("$block_htm")){ $block = file_get_contents($block_htm);}
        if ($art_id!=""){
            list($article_displ_nomber,$brand_id)=$this->getArticleNrDisplBrand($art_id);
            $r1=$db->query("select * from T_POINT where status='1' order by position asc;");$n1=$db->num_rows($r1);
            for ($t=1;$t<=$n1;$t++){
                $tpoint_id=$db->result($r1,$t-1,"id");
                $tpoint_name=$db->result($r1,$t-1,"name");
                $tpoint_block=$block;
                $tpoint_block=str_replace("{tpoint_name}",$tpoint_name,$tpoint_block);$month_list="";$sale_list="";
                for ($m=1;$m<=36;$m++){
                    $month=date("Y-m-00",strtotime("-$m month"));
                    $month_list.="<th style='text-align:center'>".substr($month,5,2)."<br>".substr($month,0,4)."</th>";

                    $r=$db->query("select AMOUNT from T2_ARTICLES_SALES where art_id='$art_id' and TPOINT_ID='$tpoint_id' and MONTH='$month';");$n=$db->num_rows($r);
                    for ($i=1;$i<=$n;$i++){
                        $amount=$db->result($r,$i-1,"AMOUNT");
                        $style="";
                        if ($amount>0) $style="background:lightgreen;";
                        if ($amount<0) $style="background:pink;";
                        $sale_list.="<td style='text-align:center; $style'>$amount</td>";
                    }
                    if ($n==0){$sale_list.="<td style='text-align:center'>0</td>";}
                }
                $tpoint_block=str_replace("{month_list}",$month_list,$tpoint_block);
                $tpoint_block=str_replace("{sale_list}",$sale_list,$tpoint_block);
                $list.=$tpoint_block;
            }
            $list=str_replace("{kol_elm}",$m,$list);
            $answer=1;$err="";
        }
        return array($answer,$err,$list,"Інформація про продажі артикулу: $article_displ_nomber");
    }

    function getArticleZED($art_id){$db=DbSingleton::getTokoDb();$zed=0;
        $r=$db->query("select t2s.COSTUMS_CODE from T2_ZED t2z 
        left outer join T2_COSTUMS t2s on t2s.COSTUMS_ID=t2z.COSTUMS_ID
        where t2z.ART_ID='$art_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $zed=$db->result($r,0,"COSTUMS_CODE");
        }
        return $zed;
    }

    function loadArticleZED($art_id){$db=DbSingleton::getTokoDb();
        $form="";$form_htm=RD."/tpl/catalogue_zed.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select t2z.*, t2c.COUNTRY_NAME, t2s.COSTUMS_CODE from T2_ZED t2z 
            left outer join T2_COUNTRIES t2c on t2c.COUNTRY_ID=t2z.COUNTRY_ID
            left outer join T2_COSTUMS t2s on t2s.COSTUMS_ID=t2z.COSTUMS_ID
        where t2z.ART_ID='$art_id' limit 0,1;");
        $country_id=$db->result($r,0,"COUNTRY_ID");
        $country_name=$db->result($r,0,"COUNTRY_NAME");
        $costums_id=$db->result($r,0,"COSTUMS_ID");
        $costums_code=$db->result($r,0,"COSTUMS_CODE");
        $form=str_replace("{art_id}",$art_id,$form);
        $form=str_replace("{country_id}",$country_id,$form);
        $form=str_replace("{country_name}",$country_name,$form);
        $form=str_replace("{costums_id}",$costums_id,$form);
        $form=str_replace("{costums_code}",$costums_code,$form);
        return $form;
    }

    function saveCatalogueZED($art_id,$country_id,$costums_id){$db=DbSingleton::getTokoDb();$slave=new slave;$answer=0;$err="Помилка збереження даних!";
        $art_id=$slave->qq($art_id);$country_id=$slave->qq($country_id);$costums_id=$slave->qq($slave->point_valid($costums_id));
        if ($art_id>0){
            //T2_ZED UPDATE
            $r=$db->query("select * from `T2_ZED` where `ART_ID`='$art_id' limit 0,1;");$n=$db->num_rows($r);
            if ($n==0){
                $db->query("insert into T2_ZED (`ART_ID`,`COUNTRY_ID`,`COSTUMS_ID`) values ('$art_id','$country_id','$costums_id');");
            }
            if ($n==1){
                $db->query("update T2_ZED set `COUNTRY_ID`='$country_id', `COSTUMS_ID`='$costums_id' where `ART_ID`='$art_id';");
            }
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function showCatalogueBrandSelectDocumentList($r,$code_search){session_start();$db=DbSingleton::getTokoDb();$list="";
        $n=$db->num_rows($r); $tkey=time();
        $db->query("CREATE TEMPORARY TABLE IF NOT EXISTS `NBRAND_RESULT_$tkey` (`art_id` INT NOT NULL ,`display_nr` VARCHAR( 100 ) NOT NULL ,`name` VARCHAR( 255 ) NOT NULL ,`brand_id` INT NOT NULL ,`brand_name` VARCHAR( 100 ) NOT NULL ,`kol_res` TINYINT NOT NULL) ENGINE = MYISAM ;");
        for ($i=1;$i<=$n;$i++){
            $art_id=$db->result($r,$i-1,"ART_ID");
            $display_nr=$db->result($r,$i-1,"DISPLAY_NR");
            $name=$db->result($r,$i-1,"NAME");
            $brand_id=$db->result($r,$i-1,"BRAND_ID");
            $brand_name=$db->result($r,$i-1,"BRAND_NAME");
            $kol_res=0;
            //$kol_res=$this->countCatalogueBrandSelectItems($code_search,$brand_id);
            $db->query("insert into `NBRAND_RESULT_$tkey` values ('$art_id','$display_nr','$name','$brand_id','$brand_name','$kol_res');");
        }

        $r=$db->query("select * from `NBRAND_RESULT_$tkey` order by `kol_res` desc;");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $display_nr=$db->result($r,$i-1,"display_nr");
            $name=$db->result($r,$i-1,"name");
            $brand_id=$db->result($r,$i-1,"brand_id");
            $brand_name=$db->result($r,$i-1,"brand_name");
            $list.="<tr style='cursor:pointer;' onClick='selectFromList2(\"$brand_id\",\"$display_nr\")'>
                <td>$display_nr</td>
                <td>$brand_name</td>
                <td>$name</td>
            </tr>";
        }$form="";
        if ($n>0){
            $form_htm=RD."/tpl/catalogue_brand_select_list.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
            $form=str_replace("{list}",$list,$form);
        }
        $db->query("DROP TEMPORARY TABLE IF EXISTS `NBRAND_RESULT_$tkey`;");
        return $form;
    }

    function showArticlesSearchDocumentList($art,$brand_id,$search_type){$db=DbSingleton::getTokoDb();$n=0;$list2="";$r="";$query="";
        if ($search_type==0){
            $art=$this->clearArticle($art);//$brand_id=$links[2];
            $where_brand="";$group_brand="group by t2c.BRAND_ID"; if ($brand_id!="" && $brand_id>0){$where_brand=" and t2c.BRAND_ID='$brand_id'"; $group_brand="";}
            if ($art!=""){
                $query="select t2b.BRAND_NAME, t2n.NAME,t2c.BRAND_ID, t2c.DISPLAY_NR, t2c.ART_ID, t2c.KIND, t2c.RELATION 
                from T2_CROSS t2c 
                     inner join T2_BRANDS t2b on t2b.BRAND_ID=t2c.BRAND_ID
                     left outer join T2_NAMES t2n on t2n.ART_ID=t2c.ART_ID
                 where  t2c.SEARCH_NUMBER = '$art' $where_brand $group_brand order by t2n.NAME asc;";
                 $r=$db->query($query);$n=$db->num_rows($r);
            }
            $one_result=0;
            if ($n>1 && ($brand_id=="" || $brand_id==0)){ $where_brand="";
                $list2=$this->showCatalogueBrandSelectDocumentList($r,$art);
            }
            if ($n==1){
                $query="select t2b.BRAND_NAME, t2n.NAME,t2c.BRAND_ID, t2c.DISPLAY_NR, t2c.ART_ID, t2c.KIND, t2c.RELATION 
                from T2_CROSS t2c 
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

                $query="select t2a.ART_ID,t2a.BRAND_ID,t2a.ARTICLE_NR_DISPL, t2b.BRAND_NAME, t2n.NAME, t2n.INFO, t2bc.BARCODE, t2ic.CROSS as inner_cross, gg.NAME as goods_group_name, u.name as unit_name, cs.COSTUMS_CODE, cc.COUNTRY_NAME
                from T2_ARTICLES t2a 
                    left outer join T2_BRANDS t2b on t2b.BRAND_ID=t2a.BRAND_ID 
                    left outer join T2_NAMES t2n on t2n.ART_ID=t2a.ART_ID 
                    left outer join T2_BARCODES t2bc on t2bc.ART_ID=t2a.ART_ID 
                    left outer join T2_INNER_CROSS t2ic on t2ic.ART_ID=t2a.ART_ID 
                    left outer join T2_GOODS_GROUP t2gg on t2gg.ART_ID=t2a.ART_ID 
                    left outer join GOODS_GROUP gg on gg.ID=t2gg.GOODS_GROUP_ID 
                    left outer join T2_PACKAGING t2p on t2p.ART_ID=t2a.ART_ID 
                    left outer join units u on u.id=t2p.UNITS_ID 
                    left outer join T2_ZED t2z on t2z.ART_ID=t2a.ART_ID 
                    left outer join T2_COUNTRIES cc on cc.COUNTRY_ID=t2z.COUNTRY_ID 
                    left outer join T2_COSTUMS cs on cs.COSTUMS_ID=t2z.COSTUMS_ID 
                where t2a.ART_ID in ($art_id_str)";
            }
        }
        if ($search_type==1){
            $query="select t2a.ART_ID,t2a.BRAND_ID,t2a.ARTICLE_NR_DISPL, t2b.BRAND_NAME, t2n.NAME, t2n.INFO, t2bc.BARCODE, t2ic.CROSS as inner_cross, gg.NAME as goods_group_name, u.name as unit_name, cs.COSTUMS_ID, cs.COSTUMS_CODE, cc.COUNTRY_ID, cc.COUNTRY_NAME
            from T2_ARTICLES t2a 
                left outer join T2_BRANDS t2b on t2b.BRAND_ID=t2a.BRAND_ID 
                left outer join T2_NAMES t2n on t2n.ART_ID=t2a.ART_ID 
                left outer join T2_BARCODES t2bc on t2bc.ART_ID=t2a.ART_ID 
                left outer join T2_INNER_CROSS t2ic on t2ic.ART_ID=t2a.ART_ID 
                left outer join T2_GOODS_GROUP t2gg on t2gg.ART_ID=t2a.ART_ID 
                left outer join GOODS_GROUP gg on gg.ID=t2gg.GOODS_GROUP_ID 
                left outer join T2_PACKAGING t2p on t2p.ART_ID=t2a.ART_ID 
                left outer join units u on u.id=t2p.UNITS_ID 
                left outer join T2_ZED t2z on t2z.ART_ID=t2a.ART_ID 
                left outer join T2_COUNTRIES cc on cc.COUNTRY_ID=t2z.COUNTRY_ID 
                left outer join T2_COSTUMS cs on cs.COSTUMS_ID=t2z.COSTUMS_ID 
            where t2a.ARTICLE_NR_SEARCH='$art' or t2a.ARTICLE_NR_DISPL='$art';";
        }
        if ($search_type==2){
            $query="select t2a.ART_ID,t2a.BRAND_ID,t2a.ARTICLE_NR_DISPL, t2b.BRAND_NAME, t2n.NAME, t2n.INFO, t2bc.BARCODE, t2ic.CROSS as inner_cross, gg.NAME as goods_group_name, u.name as unit_name, cs.COSTUMS_ID, cs.COSTUMS_CODE, cc.COUNTRY_ID, cc.COUNTRY_NAME
            from T2_ARTICLES t2a 
                left outer join T2_BRANDS t2b on t2b.BRAND_ID=t2a.BRAND_ID 
                left outer join T2_NAMES t2n on t2n.ART_ID=t2a.ART_ID 
                left outer join T2_BARCODES t2bc on t2bc.ART_ID=t2a.ART_ID 
                left outer join T2_INNER_CROSS t2ic on t2ic.ART_ID=t2a.ART_ID 
                left outer join T2_GOODS_GROUP t2gg on t2gg.ART_ID=t2a.ART_ID 
                left outer join GOODS_GROUP gg on gg.ID=t2gg.GOODS_GROUP_ID 
                left outer join T2_PACKAGING t2p on t2p.ART_ID=t2a.ART_ID 
                left outer join units u on u.id=t2p.UNITS_ID 
                left outer join T2_ZED t2z on t2z.ART_ID=t2a.ART_ID 
                left outer join T2_COUNTRIES cc on cc.COUNTRY_ID=t2z.COUNTRY_ID 
                left outer join T2_COSTUMS cs on cs.COSTUMS_ID=t2z.COSTUMS_ID 
            where t2bc.BARCODE='$art';";
        }
        //print $query;
        $r=$db->query($query);$n=$db->num_rows($r);$list="";$header_list="";
        if ($list2==""){  // сработал внешний фильр или основной поиск с выбором бренда
            //$lst=array();
            $reserv=0;
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
                //$inner_cross=$db->result($r,$i-1,"inner_cross");
                $goods_group_name=$db->result($r,$i-1,"goods_group_name");
                //$unit_name=$db->result($r,$i-1,"unit_name");
                $costums_id=$db->result($r,$i-1,"COSTUMS_ID");
                $costums_code=$db->result($r,$i-1,"COSTUMS_CODE");
                $country_id=$db->result($r,$i-1,"COUNTRY_ID");
                $country_name=$db->result($r,$i-1,"COUNTRY_NAME");
                $color=""; if (strtoupper(trim($art))==strtoupper($this->clearArticle($article_nr_displ))){$color="background:#0a89da; color:#fff;";}
                $list.="<tr style='cursor:pointer; $color' onclick='setArticleToDoc(\"$art_id\",\"$article_nr_displ\",\"$brand_id\",\"$brand_name\",\"$costums_id\",\"$costums_code\",\"$country_id\",\"$country_name\")'>
                    <td class='text-center'>$i</td>
                    <td class='text-center'>$article_nr_displ</td>
                    <td class='text-center'>$brand_name</td>
                    <td class='text-center'>$name</td>
                    <td class='text-center'>$barcode</td>
                    <td class='text-center'>$goods_group_name</td>
                    <td class='text-center'>$reserv</td>
                    <td class='text-center'>$art_id</td>
                </tr>";
            }
        }
        return array($header_list,$list,$list2);
    }

    function showArticleStorageCellsRestForm($art_id){$db=DbSingleton::getTokoDb();$form="";
        if ($art_id>0){
            $form_htm=RD."/tpl/catalogue_storage_rest_list.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
            $r=$db->query("select t2s.*, s.name as storage_name from T2_ARTICLES_STRORAGE t2s
                left outer join STORAGE s on s.ID=t2s.STORAGE_ID
            where t2s.ART_ID='$art_id' and (t2s.amount>0 or t2s.reserv_amount>0);");$n=$db->num_rows($r);$list="";
            for ($i=1;$i<=$n;$i++){
                $amount=$db->result($r,$i-1,"AMOUNT");
                $reserv_amount=$db->result($r,$i-1,"RESERV_AMOUNT");
                $storage_id=$db->result($r,$i-1,"STORAGE_ID");
                $storage_name=$db->result($r,$i-1,"storage_name");
                $list.="<tr align='center'>
                    <td>$i</td>
                    <td>$storage_name</td>
                    <td>$amount</td>
                    <td>$reserv_amount</td>
                    <td><button class='btn btn-xs btn-default' title='Переглянути' onClick='viewArticleReservDocs(\"$art_id\",\"$storage_id\");'><i class='fa fa-eye'></i></button></td>
                    <td><button class='btn btn-xs btn-default' title='Переглянути' onClick='viewArticleCellsRest(\"$art_id\",\"$storage_id\");'><i class='fa fa-eye'></i></button></td>
                </tr>";
            }
            $form=str_replace("{list}",$list,$form);
            list($article_nr_displ,$brand_id,$brand_name)=$this->getArticleNrDisplBrand($art_id);
            $form=str_replace("{article_nr_displ}",$article_nr_displ." ".$brand_name,$form);
        }
        return array($form,"Наявність на складах");
    }

    function showArticlePartitionsRestForm($art_id){$db=DbSingleton::getDb();$form="";$doc_name=$doc_suppl_name="";
        if ($art_id>0){
            $form_htm=RD."/tpl/catalogue_partitions_rest_list.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
            $r=$db->query("select t2p.* from T2_ARTICLES_PARTITIONS t2p where t2p.ART_ID='$art_id' and t2p.rest>0 order by id desc limit 0,1000;");$n=$db->num_rows($r);$list="";
            for ($i=1;$i<=$n;$i++){
                $amount=$db->result($r,$i-1,"amount");
                $rest=$db->result($r,$i-1,"rest");
                $parrent_type_id=$db->result($r,$i-1,"parrent_type_id");
                $parrent_doc_id=$db->result($r,$i-1,"parrent_doc_id");
                $price=$db->result($r,$i-1,"price");
                $oper_price=$db->result($r,$i-1,"oper_price");
                $price_buh_uah=$db->result($r,$i-1,"price_buh_uah");
                $price_man_uah=$db->result($r,$i-1,"price_man_uah");
                if ($parrent_type_id==1){ $income=new income;
                    $doc_name="".$income->getIncomeDocNom($parrent_doc_id);
                    $doc_suppl_name="".$income->getIncomeSupplDocNom($parrent_doc_id);
                }
                $list.="<tr align='center'>
                    <td>$i</td>
                    <td>$doc_name</td>
                    <td>$doc_suppl_name</td>
                    <td>$amount</td>
                    <td>$rest</td>
                    <td>$price</td>
                    <td>$oper_price</td>
                    <td>$price_buh_uah</td>
                    <td>$price_man_uah</td>
                </tr>";
            }
            if ($n==0){$list="<tr><td colspan='8' align='center'>Записи відсутні</td></tr>";}
            $form=str_replace("{list}",$list,$form);
            list($article_nr_displ,$brand_id,$brand_name)=$this->getArticleNrDisplBrand($art_id);
            $form=str_replace("{article_nr_displ}",$article_nr_displ." ".$brand_name,$form);
        }
        return array($form,"Наявність по партіям");
    }

    function getClientName($id){$db=DbSingleton::getDb(); $name="";
        $r=$db->query("select name from A_CLIENTS where id='$id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$name=$db->result($r,0,"name");}
        return $name;
    }

    function viewArticleReservDocs($art_id,$storage_id){ $db=DbSingleton::getDb();$answer=0;$err="Помилка";$form="";$list="";
        if ($art_id>0){
            $form_htm=RD."/tpl/catalogue_storage_reserv_list.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
            list($article_nr_displ,$brand_id,$brand_name)=$this->getArticleNrDisplBrand($art_id);
            $form=str_replace("{article_nr_displ}",$article_nr_displ." ".$brand_name,$form);
            $r=$db->query("select j.id,j.prefix,j.doc_nom,j.type_id,j.data,SUM(js.amount) as amount,j.user_id from J_MOVING_STR js left outer join J_MOVING j on (j.id=js.jmoving_id) where js.art_id='$art_id' and js.status_jmoving in (44,45) and j.status='1' and js.amount>0 and js.storage_id_from='$storage_id' and (j.oper_status='30' or (j.oper_status='31' and j.status_jmoving='49' or j.status_jmoving='48')) and j.parrent_type_id=0 and j.parrent_doc_id=0 group by j.id;");$n=$db->num_rows($r);
            for ($i=1;$i<=$n;$i++){
                $prefix=$db->result($r,$i-1,"prefix");
                $doc_nom=$db->result($r,$i-1,"doc_nom");
                $data=$db->result($r,$i-1,"data");
                $amount=$db->result($r,$i-1,"amount");
                $jmoving_user_id=$db->result($r,$i-1,"user_id");
                $user_name=$this->getMediaUserName($jmoving_user_id);
                $list.="<tr>
                    <td>$i</td>
                    <td>$prefix-$doc_nom</td>
                    <td>$data</td>
                    <td>$amount</td>
                    <td></td>
                    <td>$user_name</td>
                </tr>";
            }
            //$r=$db->query("select dp.id,dp.prefix,dp.doc_nom,dp.data,SUM(dps.amount) as amount,dp.user_id,dp.client_id from J_DP_STR dps left outer join J_DP dp on (dp.id=dps.dp_id) left outer join J_SELECT s on (s.parrent_doc_type_id='2' and s.parrent_doc_id=dp.id) where dps.art_id='$art_id' and dps.status_dp in (79,80) and s.status_select<85 and dp.status='1' and dps.amount>0 and dps.storage_id_from='$storage_id' and (dp.oper_status='30' or dp.oper_status='31') group by dp.id;");$n=$db->num_rows($r);
            $r=$db->query("select dp.id,dp.prefix,dp.doc_nom,dp.data,SUM(dps.amount) as amount,SUM(dps.amount_collect) as amount_collect,dp.user_id,dp.client_id from J_DP_STR dps left outer join J_DP dp on (dp.id=dps.dp_id) where dps.art_id='$art_id' and dps.status_dps=93 and dp.status='1' and dps.amount>0 and dps.location_storage_id='$storage_id' and (dp.oper_status='30' or dp.oper_status='31') group by dp.id;");$n=$db->num_rows($r);$dp_id_str="0";
            for ($i=1;$i<=$n;$i++){
                $dp_id=$db->result($r,$i-1,"id"); $dp_id_str.=",$dp_id";
                $prefix=$db->result($r,$i-1,"prefix");
                $doc_nom=$db->result($r,$i-1,"doc_nom");
                $data=$db->result($r,$i-1,"data");
                $amount=$db->result($r,$i-1,"amount");$amount_dis=$amount;
                $amount_collect=$db->result($r,$i-1,"amount_collect");if ($amount_collect>0){$amount_dis=$amount_collect;}
                $dp_user_id=$db->result($r,$i-1,"user_id");
                $user_name=$this->getMediaUserName($dp_user_id);
                $dp_client_id=$db->result($r,$i-1,"client_id");
                $client_name=$this->getClientName($dp_client_id);
                $list.="<tr>
                    <td>$i</td>
                    <td>$prefix-$doc_nom</td>
                    <td>$data</td>
                    <td align='right'>$amount_dis</td>
                    <td>$client_name</td>
                    <td>$user_name</td>
                </tr>";
            }
            $r=$db->query("select dp.id,dp.prefix,dp.doc_nom,dp.data,SUM(dps.amount) as amount,SUM(dps.amount_collect) as amount_collect,dp.user_id,dp.client_id 
            from J_DP_STR dps 
                left outer join J_DP dp on (dp.id=dps.dp_id) 
            where dps.art_id='$art_id' and dps.status_dps in (94,95,96) and dp.status='1' and dps.amount>0 and dps.location_storage_id='$storage_id' and (dp.oper_status='30' or dp.oper_status='31') group by dp.id;");$n=$db->num_rows($r);// ищем в удаленном отборе склад
            for ($i=1;$i<=$n;$i++){
                $dp_id=$db->result($r,$i-1,"id"); $dp_id_str.=",$dp_id";
                $prefix=$db->result($r,$i-1,"prefix");
                $doc_nom=$db->result($r,$i-1,"doc_nom");
                $data=$db->result($r,$i-1,"data");
                $amount=$db->result($r,$i-1,"amount");$amount_dis=$amount;
                $amount_collect=$db->result($r,$i-1,"amount_collect");if ($amount_collect>0){$amount_dis=$amount_collect;}
                $dp_user_id=$db->result($r,$i-1,"user_id");
                $user_name=$this->getMediaUserName($dp_user_id);
                $dp_client_id=$db->result($r,$i-1,"client_id");
                $client_name=$this->getClientName($dp_client_id);
                $list.="<tr>
                    <td>$i</td>
                    <td>$prefix-$doc_nom</td>
                    <td>$data</td>
                    <td align='right'>$amount_dis</td>
                    <td>$client_name</td>
                    <td>$user_name</td>
                </tr>";
            }
            /*$r=$db->query("select dp.id,dp.prefix,dp.doc_nom,dp.data,SUM(dps.amount) as amount,SUM(dps.amount_collect) as amount_collect,dp.user_id,dp.client_id
                        from J_DP_STR dps
                        left outer join J_DP dp on (dp.id=dps.dp_id)
                        left outer join J_MOVING jm on (jm.parrent_type_id='1' and jm.parrent_doc_id =dp.id)
                        left outer join J_SELECT s on (s.parrent_doc_type_id='1' and s.parrent_doc_id=jm.id) where dps.art_id='$art_id' and dps.status_dps=95 and dp.status='1' and dps.amount>0 and dps.storage_id_from='$storage_id' and (dp.oper_status='30' or dp.oper_status='31') group by dp.id;");$n=$db->num_rows($r);// ищем в локальном отборе склад
            for ($i=1;$i<=$n;$i++){
                $dp_id=$db->result($r,$i-1,"id"); $dp_id_str.=",$dp_id";
                $prefix=$db->result($r,$i-1,"prefix");
                $doc_nom=$db->result($r,$i-1,"doc_nom");
                $type_id=$db->result($r,$i-1,"type_id");
                $data=$db->result($r,$i-1,"data");
                $amount=$db->result($r,$i-1,"amount");$amount_dis=$amount;
                $amount_collect=$db->result($r,$i-1,"amount_collect");if ($amount_collect>0){$amount_dis=$amount_collect;}
                $dp_user_id=$db->result($r,$i-1,"user_id");
                $user_name=$this->getMediaUserName($dp_user_id);
                $dp_client_id=$db->result($r,$i-1,"client_id");
                $client_name=$this->getClientName($dp_client_id);
                $list.="<tr>
                    <td>jm$i</td>
                    <td>$prefix-$doc_nom</td>
                    <td>$data</td>
                    <td align='right'>$amount_dis</td>
                    <td>$client_name</td>
                    <td>$user_name</td>
                </tr>";
            }
            /*$r=$db->query("select dp.id,dp.prefix,dp.doc_nom,dp.data,SUM(dps.amount) as amount,SUM(dps.amount_collect) as amount_collect,dp.user_id,dp.client_id from J_DP_STR dps left outer join J_DP dp on (dp.id=dps.dp_id) left outer join J_SELECT s on (s.parrent_doc_type_id='2' and s.parrent_doc_id=dp.id) where dps.art_id='$art_id' and dps.status_dps='94' and dp.status='1' and dps.amount>0 and dps.storage_id_from='$storage_id' and (dp.oper_status='30' or dp.oper_status='31') and s.status_select<85 and s.storage_id=dps.storage_id_from group by dp.id;");$n=$db->num_rows($r);// ищем в локальном отборе склад
            for ($i=1;$i<=$n;$i++){
                $dp_id=$db->result($r,$i-1,"id"); $dp_id_str.=",$dp_id";
                $prefix=$db->result($r,$i-1,"prefix");
                $doc_nom=$db->result($r,$i-1,"doc_nom");
                $type_id=$db->result($r,$i-1,"type_id");
                $data=$db->result($r,$i-1,"data");
                $amount=$db->result($r,$i-1,"amount");$amount_dis=$amount;
                $amount_collect=$db->result($r,$i-1,"amount_collect");if ($amount_collect>0){$amount_dis=$amount_collect;}
                $dp_user_id=$db->result($r,$i-1,"user_id");
                $user_name=$this->getMediaUserName($dp_user_id);
                $dp_client_id=$db->result($r,$i-1,"client_id");
                $client_name=$this->getClientName($dp_client_id);
                $list.="<tr>
                    <td>$i</td>
                    <td>$prefix-$doc_nom</td>
                    <td>$data</td>
                    <td align='right'>$amount_dis</td>
                    <td>$client_name</td>
                    <td>$user_name</td>
                </tr>";
            }
            /*$r=$db->queryP("select s.id,s.parrent_doc_id,s.data_create,SUM(ss.amount) as amount,SUM(ss.amount_collect) as amount_collect,s.user_id from J_SELECT_STR ss left outer join J_SELECT s on (s.id=ss.select_id) where ss.art_id='$art_id' and s.status='1' and ss.amount>0 and ss.storage_id_from='$storage_id' and (s.parrent_doc_type_id='2' and s.parrent_doc_id>'0' and s.parrent_doc_id not in ($dp_id_str)) group by s.id;");$n=$db->num_rows($r);
            for ($i=1;$i<=$n;$i++){
                $sel_id=$db->result($r,$i-1,"id");
                $parrent_doc_id=$db->result($r,$i-1,"parrent_doc_id");
                $data=$db->result($r,$i-1,"data_create");
                $amount=$db->result($r,$i-1,"amount");$amount_dis=$amount;
                $amount_collect=$db->result($r,$i-1,"amount_collect");if ($amount_collect>0){$amount_dis=$amount_collect;}
                $sel_user_id=$db->result($r,$i-1,"user_id");
                $user_name=$this->getMediaUserName($sel_user_id);
                $list.="<tr>
                    <td>й$i</td>
                    <td>СКв-$sel_id</td>
                    <td>$data</td>
                    <td>$amount_dis</td>
                    <td>ДП-$parrent_doc_id</td>
                    <td>$user_name</td>
                </tr>";
            }*/
            $form=str_replace("{list}",$list,$form);
            $answer=1;$err="";
        }
        return array($answer,$err,$form,"Наявність в документах переміщення");
    }

    function viewArticleCellsRest($art_id,$storage_id){ $db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$answer=0;$err="Помилка";$list="";$form="";
        if ($art_id>0){
            $form_htm=RD."/tpl/catalogue_storage_cells_rest_list.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
            list($article_nr_displ,$brand_id,$brand_name)=$this->getArticleNrDisplBrand($art_id);
            $form=str_replace("{article_nr_displ}",$article_nr_displ." ".$brand_name,$form);
            $r=$dbt->query("select t2sc.AMOUNT, t2sc.RESERV_AMOUNT, sc.cell_value from T2_ARTICLES_STRORAGE_CELLS t2sc 
                left outer join STORAGE_CELLS sc on (sc.id=t2sc.STORAGE_CELLS_ID) 
            where t2sc.storage_id='$storage_id' and t2sc.ART_ID='$art_id' and (t2sc.AMOUNT>0 or t2sc.RESERV_AMOUNT>0) order by sc.cell_value  asc;");$n=$db->num_rows($r);
            for ($i=1;$i<=$n;$i++){
                $amount=$db->result($r,$i-1,"AMOUNT");
                $reserv_amount=$db->result($r,$i-1,"RESERV_AMOUNT");
                $cell_value=$db->result($r,$i-1,"cell_value");
                $list.="<tr>
                    <td>$i</td>
                    <td>$cell_value</td>
                    <td>$amount</td>
                    <td>$reserv_amount</td>
                </tr>";
            }
            $form=str_replace("{list}",$list,$form);
            $form=str_replace("{storage_name}",$this->getStorageName($storage_id),$form);
            $answer=1;$err="";
        }
        return array($answer,$err,$form,"Наявність у комірках складу");
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

    function showCatalogueDonorForm($art_id){$form="";$kind_name="";
        $form_htm=RD."/tpl/catalogue_donor_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $form=str_replace("{art_id}",$art_id,$form);
        list($search_number,$d,$d)=$this->getArticleNrDisplBrand($art_id);
        $form=str_replace("{search_number}",$search_number,$form);
        $form=str_replace("{display_nr}","",$form);
        return array($form,"Імпорт інформації від донора".$kind_name);
    }

    function showCatalogueDonorIndexSearch(){$form="";
        $form_htm=RD."/tpl/catalogue_donor_search.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        return array($form,"Пошук аналогу по індексу");
    }

    function findCatalogueDonorIndexSearch($index){$db=DbSingleton::getTokoDb();$slave=new slave;$list="";
        $index=$slave->qq($index);
        if ($index!=""){
            $query="select t2a.ART_ID, t2a.BRAND_ID,t2a.ARTICLE_NR_DISPL, t2b.BRAND_NAME, t2n.NAME from T2_ARTICLES t2a 
                left outer join T2_BRANDS t2b on t2b.BRAND_ID=t2a.BRAND_ID 
                left outer join T2_NAMES t2n on t2n.ART_ID=t2a.ART_ID 
            where t2a.ARTICLE_NR_SEARCH='$index' or t2a.ARTICLE_NR_DISPL='$index';";
            $r=$db->query($query);$n=$db->num_rows($r);$list="";
            for ($i=1;$i<=$n;$i++){
                $art_id=$db->result($r,$i-1,"ART_ID");
                $article_nr_displ=$db->result($r,$i-1,"ARTICLE_NR_DISPL");
                $brand_id=$db->result($r,$i-1,"BRAND_ID");
                $brand_name=$db->result($r,$i-1,"BRAND_NAME");
                $name=$db->result($r,$i-1,"NAME");
                $list.="<tr style=\"cursor:pointer;\" onClick='setDonorSearchIndex(\"$art_id\",\"$article_nr_displ\",\"$brand_id\",\"$brand_name\");'>
                    <td>$art_id</td>
                    <td>$article_nr_displ</td>
                    <td>$brand_name</td>
                    <td>$name</td>
                </tr>";
            }
        }
        return $list;
    }

    function saveCatalogueDonorForm($art_id,$search_number,$display_nr,$art_id2,$ch){$db=DbSingleton::getTokoDb();$dbp=DbSingleton::getTokoDb();$slave=new slave;$answer=0;$err="Помилка збереження даних!";
        $art_id=$slave->qq($art_id);$art_id2=$slave->qq($art_id2);$ch=$slave->qq($ch);
        if ($art_id>0 && $display_nr!="" && $art_id2>0){
            if ($ch[1]==1){
                $r=$db->query("select * from T2_CROSS where ART_ID='$art_id2' and KIND = 3 and RELATION = 0;");$n=$db->num_rows($r);
                for ($i=1;$i<=$n;$i++){
                    $search_number=$db->result($r,$i-1,"SEARCH_NUMBER");
                    $kind=$db->result($r,$i-1,"KIND");
                    $brand_id=$db->result($r,$i-1,"BRAND_ID");
                    $relation=$db->result($r,$i-1,"relation");

                    $r2=$db->query("select count(ART_ID) as kol from T2_CROSS where `ART_ID`='$art_id' and `SEARCH_NUMBER`='$search_number' and `KIND`='$kind' and `BRAND_ID`='$brand_id' and `RELATION`='$relation';"); $ex_row=$db->result($r2,0,"kol");
                    if ($ex_row==0){
                        $query="insert into T2_CROSS (`ART_ID`,`SEARCH_NUMBER`,`KIND`,`BRAND_ID`,`DISPLAY_NR`,`RELATION`) values ('$art_id','$search_number','$kind','$brand_id','$search_number','$relation');";
                        $db->query($query);
                    }
                }
            }
            if ($ch[2]==1){
                $r=$db->query("select * from T2_CROSS where ART_ID='$art_id2' and KIND = 4 and RELATION = 0;");$n=$db->num_rows($r);
                for ($i=1;$i<=$n;$i++){
                    $search_number=$db->result($r,$i-1,"SEARCH_NUMBER");
                    $kind=$db->result($r,$i-1,"KIND");
                    $brand_id=$db->result($r,$i-1,"BRAND_ID");
                    $relation=$db->result($r,$i-1,"relation");

                    $r2=$db->query("select count(ART_ID) as kol from T2_CROSS where `ART_ID`='$art_id' and `SEARCH_NUMBER`='$search_number' and `KIND`='$kind' and `BRAND_ID`='$brand_id' and `RELATION`='$relation';"); $ex_row=$db->result($r2,0,"kol");
                    if ($ex_row==0){
                        $query="insert into T2_CROSS (`ART_ID`,`SEARCH_NUMBER`,`KIND`,`BRAND_ID`,`DISPLAY_NR`,`RELATION`) values ('$art_id','$search_number','$kind','$brand_id','$search_number','$relation');";
                        $db->query($query);
                    }
                }
            }
            if ($ch[3]==1){
                $r=$db->query("select * from T2_CROSS where ART_ID='$art_id2' and KIND in (3,4) and RELATION = 1;");$n=$db->num_rows($r);
                for ($i=1;$i<=$n;$i++){
                    $search_number=$db->result($r,$i-1,"SEARCH_NUMBER");
                    $kind=$db->result($r,$i-1,"KIND");
                    $brand_id=$db->result($r,$i-1,"BRAND_ID");
                    $relation=$db->result($r,$i-1,"relation");

                    $r2=$db->query("select count(ART_ID) as kol from T2_CROSS where `ART_ID`='$art_id' and `SEARCH_NUMBER`='$search_number' and `KIND`='$kind' and `BRAND_ID`='$brand_id' and `RELATION`='$relation';"); $ex_row=$db->result($r2,0,"kol");
                    if ($ex_row==0){
                        $query="insert into T2_CROSS (`ART_ID`,`SEARCH_NUMBER`,`KIND`,`BRAND_ID`,`DISPLAY_NR`,`RELATION`) values ('$art_id','$search_number','$kind','$brand_id','$search_number','$relation');";
                        $db->query($query);
                    }
                }
            }
            if ($ch[4]==1){
                $r=$db->query("select * from T2_CROSS where ART_ID='$art_id2' and KIND in (3,4) and RELATION = 2;");$n=$db->num_rows($r);
                for ($i=1;$i<=$n;$i++){
                    $search_number=$db->result($r,$i-1,"SEARCH_NUMBER");
                    $kind=$db->result($r,$i-1,"KIND");
                    $brand_id=$db->result($r,$i-1,"BRAND_ID");
                    $relation=$db->result($r,$i-1,"relation");

                    $r2=$db->query("select count(ART_ID) as kol from T2_CROSS where `ART_ID`='$art_id' and `SEARCH_NUMBER`='$search_number' and `KIND`='$kind' and `BRAND_ID`='$brand_id' and `RELATION`='$relation';"); $ex_row=$db->result($r2,0,"kol");
                    if ($ex_row==0){
                        $query="insert into T2_CROSS (`ART_ID`,`SEARCH_NUMBER`,`KIND`,`BRAND_ID`,`DISPLAY_NR`,`RELATION`) values ('$art_id','$search_number','$kind','$brand_id','$search_number','$relation');";
                        $db->query($query);
                    }
                }
            }
            if ($ch[5]==1){
                $r=$dbp->query("select TYP_ID,LA_ID from T2_LINKS where ART_ID='$art_id2';");$n=$dbp->num_rows($r);
                for ($i=1;$i<=$n;$i++){
                    $typ_id=$dbp->result($r,$i-1,"TYP_ID");
                    $r2=$dbp->query("select count(ART_ID) as kol from T2_LINKS where `ART_ID`='$art_id' and `TYP_ID`='$typ_id' and LA_ID='0';"); $ex_row=$dbp->result($r2,0,"kol");
                    if ($ex_row==0){
                        $query="insert into T2_LINKS (`ART_ID`,`TYP_ID`,`LA_ID`) values ('$art_id','$typ_id','0');";
                        $dbp->query($query);
                    }
                }
            }
            if ($ch[6]==1){
                $r=$dbp->query("select STR_ID from T2_TREE where ART_ID='$art_id2';");$n=$dbp->num_rows($r);
                for ($i=1;$i<=$n;$i++){
                    $str_id=$dbp->result($r,$i-1,"STR_ID");
                    $r2=$dbp->query("select count(ART_ID) as kol from T2_TREE where `ART_ID`='$art_id' and `STR_ID`='$str_id';"); $ex_row=$dbp->result($r2,0,"kol");
                    if ($ex_row==0){
                        $query="insert into T2_TREE (`ART_ID`,`STR_ID`) values ('$art_id','$str_id');";
                        $dbp->query($query);
                    }
                }
            }
            if ($ch[7]==1){
                $r=$dbp->query("select TYP_ID,LA_ID from T2_LINKS where ART_ID='$art_id2';");$n=$dbp->num_rows($r);
                for ($i=1;$i<=$n;$i++){
                    $typ_id=$dbp->result($r,$i-1,"TYP_ID");
                    $la_id=$dbp->result($r,$i-1,"LA_ID");
                    $r2=$db->query("select max(LA_ID) as mid from link_notes;"); $new_la_id=$db->result($r2,0,"mid")+0;
                    $r2=$db->query("select * from link_notes where `LA_ID`='$la_id' and `LANG_ID`='16' and DISPLAY='1';"); $n2=$db->num_rows($r2);
                    for ($j=1;$j<=$n2;$j++){
                        $sort=$db->result($r2,$j-1,"SORT");
                        $type=$db->result($r2,$j-1,"TYPE");
                        $text_name=$db->result($r2,$j-1,"TEXT_NAME");
                        $text=$db->result($r2,$j-1,"TEXT");
                        $new_la_id+=1;
                        $query="insert into T2_LINKS (`ART_ID`,`TYP_ID`,`LA_ID`) values ('$art_id','$typ_id','$new_la_id');";
                        $dbp->query($query);
                        $query="insert into link_notes (`LA_ID`,`LANG_ID`,`SORT`,`TEXT_NAME`,`TYPE`,`TEXT`,`DISPLAY`) values ('$new_la_id','16','$sort','$text_name','$type','$text','1');";
                        $db->query($query);
                    }
                }
            }
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    //showArticleSearchDocumentForm---------------------------------------------

    function showArticleSearchDocumentForm($art_id,$brand_id,$article_nr_display,$doc_type,$doc_id){$form="";
        $form_htm=RD."/tpl/catalogue_document.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $form=str_replace("{art}",$article_nr_display,$form);
        $form=str_replace("{brand_id}",$brand_id,$form);
        $form=str_replace("{doc_type}",$doc_type,$form);
        $form=str_replace("{doc_id}",$doc_id,$form);
        list($header_list,$range_list,$list_brand_select)=$this->showArticlesSearchListDoc($article_nr_display,$brand_id,"",0,$doc_type,$doc_id);
        $form=str_replace("{header_list}",$header_list,$form);
        $form=str_replace("{range_list}",$range_list,$form);
        $form=str_replace("{list_brand_select}",$list_brand_select,$form);
        $form=str_replace("{fil4BrandList}",$this->showBrandListSelect(""),$form);
        $form=str_replace("{fil4SupplList}",$this->showSupplListSelect(""),$form);
        $form=str_replace("{fil4GoodsGroupList}",$this->showGoodsGroupListSelect(""),$form);
        $form=str_replace("{fil4Top}","",$form);
        $form=str_replace("{fil4StokTo}","",$form);
        $form=str_replace("{fil4StokFrom}","",$form);
        $form=str_replace("{fil2ManufactureList}",$this->showManufactureListSelect(""),$form);
        $form=str_replace("{fil2StrId}","",$form);
        $form=str_replace("{fil2StrText}","",$form);
    //	list($range_list,$list_brand_select)=$this->showArticlesSearchDocumentList($article_nr_display,$brand_id,0,$dp_id,$tpoint_id);
        return $form;
    }

    function getTpointStorageList($tpoint_id){$db=DbSingleton::getDb(); $list=0; $week_day=date("N");$cur_time=date("H:i:s");
        $query="select ps.storage_id from T_POINT_STORAGE ps 
            left outer join T_POINT_DELIVERY_TIME pdt on pdt.storage_id=ps.storage_id 
        where ps.tpoint_id='$tpoint_id' and pdt.tpoint_id='$tpoint_id' and ps.status=1 and pdt.week_day='$week_day' and pdt.time_from<='$cur_time' and pdt.time_to>='$cur_time' order by pdt.delivery_days asc;";
        $r=$db->query($query);$n=$db->num_rows($r);
        //select delivery_days, giveout_time from T_POINT_DELIVERY_TIME where status='1' and tpoint_id='$tpoint_id' and storage_id='$storage_id' and week_day='$week_day' and time_from<='$cur_time' and time_to>='$cur_time' limit 0,1;
        for ($i=1;$i<=$n;$i++){ $list.=",".$db->result($r,$i-1,"storage_id"); }
        return $list;
    }

    function showArticlesSearchListDoc($art,$brand_id,$query_2,$search_type,$doc_type,$doc_id){$db=DbSingleton::getTokoDb();$slave=new slave;session_start();$user_id=$_SESSION["media_user_id"];
        require_once RD.'/lib/dp_class.php';$dp=new dp;
        $art=$this->clearArticle($art); if ($brand_id==0){$brand_id="";} $ak=$rk=[]; $margin_price_lvl=$tpoint_id=$margin_price_suppl_lvl=$client_vat=0;$r="";$query="";
        $cash_id=1;$price=0;$usd_to_uah=$euro_to_uah=1;
        $storage_id=$dp_id=0;$function_select_article=$reserv_type_color="";
        $suppl_id=$amountRestTpoint=$amountRestNotTpoint=0; $warranty_info=$return_delay=$delivery_info="";
        $doc_type=$slave->qq($doc_type);$doc_id=$slave->qq($doc_id);$tpoint_storage_list="0";$price_lvl=1;$list2="";$n=0;
        if ($doc_type=="dp"){$dp_id=$doc_id;
            list($price_lvl,$margin_price_lvl,$price_suppl_lvl,$margin_price_suppl_lvl,$client_vat)=$dp->getDpClientPriceLevels($dp_id);
            $tpoint_id=$dp->getDpTpoint($dp_id); //$tpoint_name=$this->getTpointName($tpoint_id);
            $tpoint_storage_list=$this->getTpointStorageList($tpoint_id);$cash_id=$dp->getDpCashId($dp_id);list($usd_to_uah,$euro_to_uah)=$dp->getKoursData();
        }

        $query_tpl="select t2a.ART_ID, t2a.BRAND_ID, t2a.ARTICLE_NR_DISPL, t2b.BRAND_NAME, t2n.NAME, t2n.INFO, t2bc.BARCODE, IFNULL(gg.NAME,'') as goods_group_name, IFNULL(t2sai.suppl_id,0) as suppl_id, IFNULL(s.id,0) as storage_id, IFNULL(s.name,'') as storage_name, IFNULL(t2apr.price_".$price_lvl.",0) as price, IFNULL(t2sai.return_delay,0) as return_delay, IFNULL(t2sai.warranty_info,'') as warranty_info, IFNULL(t2si.price_usd,0) as price_suppl, IFNULL(t2si.client_storage_id,0) as suppl_storage_id, IFNULL(t2si.stock_suppl,0) as suppl_stock
        from T2_ARTICLES t2a 
            left outer join T2_BRANDS t2b on t2b.BRAND_ID=t2a.BRAND_ID 
            left outer join T2_NAMES t2n on t2n.ART_ID=t2a.ART_ID 
            left outer join T2_BARCODES t2bc on t2bc.ART_ID=t2a.ART_ID 
            left outer join T2_GOODS_GROUP t2gg on t2gg.ART_ID=t2a.ART_ID 
            left outer join GOODS_GROUP gg on gg.ID=t2gg.GOODS_GROUP_ID 
            left outer join T2_ARTICLES_STRORAGE t2asc on (t2asc.ART_ID=t2a.ART_ID and t2asc.STORAGE_ID in ($tpoint_storage_list))
            left outer join T2_ARTICLES_PRICE_RATING t2apr on (t2apr.art_id=t2a.ART_ID and t2apr.in_use=1)
            left outer join T2_SUPPL_ARTICLES_IMPORT t2sai on (t2sai.art_id=t2a.ART_ID)
            left outer join T2_SUPPL_IMPORT t2si on (t2si.art_id=t2a.ART_ID and t2si.suppl_id=t2sai.suppl_id and t2si.status=1)
            left outer join STORAGE s on (s.id=t2asc.STORAGE_ID and s.status=1) ";

        if ($query_2=="" && $search_type==0){
            $where_brand="";$group_brand="group by t2c.BRAND_ID"; if ($brand_id!="" && $brand_id>0){$where_brand=" and t2c.BRAND_ID='$brand_id'"; $group_brand="";}
            if ($art!=""){
                $query="select t2b.BRAND_NAME, t2n.NAME,t2c.BRAND_ID, t2c.DISPLAY_NR, t2c.ART_ID, t2c.KIND, t2c.RELATION from T2_CROSS t2c 
                     inner join T2_BRANDS t2b on t2b.BRAND_ID=t2c.BRAND_ID
                     left outer join T2_NAMES t2n on t2n.ART_ID=t2c.ART_ID
                 where  t2c.SEARCH_NUMBER = '$art' $where_brand $group_brand order by t2n.NAME asc;";
                 $r=$db->query($query);$n=$db->num_rows($r);
            }
            $one_result=0;
            if ($n>1 && $brand_id==""){ $where_brand="";
                $list2=$this->showCatalogueBrandSelectListDoc($r,$art);
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
                $query=$query_tpl."	where t2a.ART_ID in ($art_id_str) and t2b.`VISIBLE`='1' order by suppl_id asc;";
            }
        }
        if ($query_2=="" && $search_type==1){
            $query=$query_tpl."	where t2a.ARTICLE_NR_SEARCH='$art' or t2a.ARTICLE_NR_DISPL='$art' and t2b.`VISIBLE`='1';";
        }
        if ($query_2=="" && $search_type==2){
            $query=$query_tpl." where t2bc.BARCODE='$art' and t2b.`VISIBLE`='1';";
        }
        if ($query_2=="" && $search_type==3){
            $query=$query_tpl." where t2a.ART_ID='$art' and t2b.`VISIBLE`='1';";
        }
        if ($query_2!=""){$query=$query_2;}
        $list="";$header_list="";
        $r=$db->query($query);$n=$db->num_rows($r);

        if ($query_2!="" || $list2==""){  // сработал внешний фильр или основной поиск с выбором бренда
            list($fldcnf,$kol_f)=$this->getCatalogueClientViewFieldsData($user_id,"catalogue_doc");$range_list="";
            for ($i=1;$i<=$kol_f;$i++){
                $header_list.="<th>".$fldcnf[$i]["field_name"]."</th>";
                $range_list.="<td onClick=\"{function_select_article}\">{".$fldcnf[$i]["field_key"]."}</td>";
            }$header_list="<tr align='center'><th data-sortable=\"false\">Фото</th><th data-sortable=\"false\">Тип артикула</th>".$header_list."</tr>";

            $sch_table="search_cat_$user_id";
            $sch_table_result="search_cat_$user_id"."_result";

            if ($query!=""){
            $db->query("drop table if exists `$sch_table`;");$db->query("drop table if exists `$sch_table_result`;");
            $db->query("create temporary table if not exists `$sch_table` as $query");
            $db->query("ALTER TABLE `$sch_table`  ADD INDEX ( `ART_ID` );");
            $db->query("ALTER TABLE `$sch_table` ADD `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST ;");
            $db->query("ALTER TABLE `$sch_table` ADD `tpoint_stock` MEDIUMINT NOT NULL AFTER `storage_name`, ADD `tpoint_reserv` MEDIUMINT NOT NULL AFTER `tpoint_stock`, ADD `not_tpoint_stock` MEDIUMINT NOT NULL AFTER `tpoint_reserv`, ADD `not_tpoint_reserv` MEDIUMINT NOT NULL AFTER `not_tpoint_stock`,ADD `delivery_info` VARCHAR( 255 ) NOT NULL AFTER `not_tpoint_reserv`;");
            $db->query("ALTER TABLE `$sch_table` ADD `kind_id` SMALLINT NOT NULL AFTER `ART_ID`, ADD `relation` SMALLINT NOT NULL AFTER `kind_id`;");

            $r=$db->query("select * from `$sch_table`;");$n=$db->num_rows($r);
            for ($i=1;$i<=$n;$i++){
                $id=$db->result($r,$i-1,"id");
                $art_id=$db->result($r,$i-1,"ART_ID");
                $kind_id=$ak[$art_id];
                $relation=$rk[$art_id];
                $suppl_id=$db->result($r,$i-1,"suppl_id");//$suppl_id_2=$suppl_id;
                $storage_id=$db->result($r,$i-1,"storage_id");
                if ($doc_type=="dp"){
                    //$suppl_id=0;
                    if ($suppl_id==0 || $storage_id>0){
                        $price=$db->result($r,$i-1,"price");
                        if ($margin_price_lvl>0){
                            $price=$price+round($price*$margin_price_lvl/100,2);
                        }
                        list($tpoint_stock,$tpoint_reserv)=$dp->getArticleRestTpoint($art_id,$tpoint_id);
                        list($not_tpoint_stock,$not_tpoint_reserv)=$dp->getArticleRestNotTpoint($art_id,$tpoint_id);
                        $delivery_info=$dp->getArticleTpointDeliveryInfo($tpoint_id,$art_id);
                        //$delivery_info=$dp->getTpointDeliveryInfo($tpoint_id,$storage_id);   ????
                        $db->query("update `$sch_table` set `kind_id`='$kind_id', `relation`='$relation', `price`='$price', `tpoint_stock`='$tpoint_stock', `tpoint_reserv`='$tpoint_reserv', `not_tpoint_stock`='$not_tpoint_stock', `not_tpoint_reserv`='$not_tpoint_reserv', `delivery_info`='$delivery_info' where id='$id';");
                        if ($tpoint_stock==0 && $tpoint_reserv==0 && $price==0){
                            $db->query("delete from `$sch_table` where id='$id' limit 1;");
                        }
                    }
                    //$suppl_id=$suppl_id_2;
                    if ($suppl_id>0 && $storage_id==0){
                        $suppl_storage_id=$db->result($r,$i-1,"suppl_storage_id");//$row_del=0;
                        if ($this->checkSupplStorageAllow($suppl_id,$suppl_storage_id)==1){
                            if ($this->checkSupplStorageTpointAllow($tpoint_id,$suppl_id,$suppl_storage_id)==1){
                                list($price_in_vat,$show_in_vat,$price_add_vat)=$dp->getSupplVatConditions($suppl_id);
                                $row_del=0;
                                //if ($client_vat==0 && $show_in_vat==0){
                                    //$db->query("delete from `$sch_table` where id='$id';"); $row_del=1;
                                //}
                                if ($row_del==0){
                                    list($suppl_margin_fm,$suppl_delivery_fm,$suppl_margin2_fm)=$dp->getTpointSupplFm($tpoint_id,$suppl_id,$suppl_storage_id,$price_suppl,$price_suppl_lvl);
                                    $price_suppl=$db->result($r,$i-1,"price_suppl");

                                    if ($suppl_margin_fm>0){
                                        $price=($price_suppl+$price_suppl*$suppl_margin_fm/100)-$price_suppl;

                                        if ($price>$suppl_delivery_fm){ $price=($price_suppl+$price_suppl*$suppl_margin_fm/100); }
                                        if ($price<=$suppl_delivery_fm){ $price=$price_suppl+$price_suppl*$suppl_margin2_fm/100+$suppl_delivery_fm; }
                                        //Step 2; Client Margin
                                        if ($margin_price_suppl_lvl>0 && $margin_price_suppl_lvl!=""){
                                            $price=$price+$price*$margin_price_suppl_lvl/100;
                                        }
                                        //Step 3; VAT //$price_in_vat,$show_in_vat,$price_add_vat
                                        //if ($client_vat==0){
                                            //if ($price_in_vat==0 && $show_in_vat==1 && $price_add_vat==1){
                                                //$price=$price+$price*20/100;
                                            //}
                                        //}
                                        if ($client_vat==1){
                                            if ($price_in_vat==0 && $show_in_vat==1 && $price_add_vat==1){
                                                $price=$price+$price*20/100;
                                            }
                                            if ($price_in_vat==1 || $show_in_vat==1){
                                                //$price=$price+$price*20/100;
                                            }else{$db->query("delete from `$sch_table` where id='$id';");$row_del=1;}
                                        }
                                        if ($row_del==0){
                                            $amountRestTpoint="";$suppl_stock_show="";
                                            $amountRestNotTpoint="$suppl_stock_show";
                                            //$delivery_info=$dp->getArticleTpointSupplDeliveryInfo($tpoint_id,$art_id);
                                            $delivery_info=$dp->getTpointSupplDeliveryInfo($tpoint_id,$suppl_id,$suppl_storage_id);
                                            $db->query("update `$sch_table` set `kind_id`='$kind_id', `relation`='$relation', `price_suppl`='$price',`price`='$price', `delivery_info`='$delivery_info' where id='$id';");
                                        }

                                    }else{$db->query("delete from `$sch_table` where id='$id';");}
                                }
                            }else{$db->query("delete from `$sch_table` where id='$id';");}
                        }else{$db->query("delete from `$sch_table` where id='$id';");}
                    }
                }
            }
            //$db->query("delete from `$sch_table` where price='0.00' and price_suppl='0.00' and suppl_stock='0' and tpoint_stock='0' and tpoint_reserv='0' and kind_id>1;");

            $db->query("create temporary table if not exists `$sch_table_result` as (select * from `$sch_table` order by art_id, delivery_info, price asc);");//$n=$db->num_rows($r);

            $r=$db->query("select * from `$sch_table_result`;");$n=$db->num_rows($r);$prev_art_id=0;
            for ($i=1;$i<=$n;$i++){
                $id=$db->result($r,$i-1,"id");
                $art_id=$db->result($r,$i-1,"ART_ID");
                if ($art_id==$prev_art_id){$db->query("delete from `$sch_table_result` where id='$id' limit 1;");}
                if ($art_id!=$prev_art_id){$prev_art_id=$art_id;}
            }

            $r=$db->query("select * from `$sch_table_result` order by kind_id asc;");$n=$db->num_rows($r);
            }
            $lst=array();
            for ($i=1;$i<=$n;$i++){ $tpoint_suppl_name="";
                $art_id=$db->result($r,$i-1,"ART_ID");
                $kind_id=$db->result($r,$i-1,"kind_id");
                $relation=$db->result($r,$i-1,"relation");
                $brand_id=$db->result($r,$i-1,"BRAND_ID");
                $article_nr_displ=$db->result($r,$i-1,"ARTICLE_NR_DISPL");
                $brand_name=$db->result($r,$i-1,"BRAND_NAME");
                $name=$db->result($r,$i-1,"NAME");
                $info=$db->result($r,$i-1,"INFO");
                $barcode=$db->result($r,$i-1,"BARCODE");
                $goods_group_name=$db->result($r,$i-1,"goods_group_name");
                if ($doc_type=="dp"){
                    $suppl_id=$db->result($r,$i-1,"suppl_id");
                    $storage_id=$db->result($r,$i-1,"storage_id");
                    $delivery_info=$db->result($r,$i-1,"delivery_info");
                    if ($suppl_id==0 || $storage_id>0){
                        $price=$db->result($r,$i-1,"price");
                        $tpoint_stock=$db->result($r,$i-1,"tpoint_stock");
                        $tpoint_reserv=$db->result($r,$i-1,"tpoint_reserv");
                        $amountRestTpoint="<span class='badge badge-primary'>$tpoint_stock / $tpoint_reserv</span>";
                        $not_tpoint_stock=$db->result($r,$i-1,"not_tpoint_stock");
                        $not_tpoint_reserv=$db->result($r,$i-1,"not_tpoint_reserv");
                        $amountRestNotTpoint="<span class='badge badge-warning'>$not_tpoint_stock / $not_tpoint_reserv</span>";
                    }
                    if ($suppl_id>0 && $storage_id==0){
                        $return_delay=$db->result($r,$i-1,"return_delay");
                        $warranty_info=$db->result($r,$i-1,"warranty_info");
                        $price=$db->result($r,$i-1,"price_suppl");
                        $suppl_stock=$db->result($r,$i-1,"stock_suppl");
                        $suppl_stock_show=$suppl_stock;
                        if ($suppl_stock_show>=10){$suppl_stock_show=">10";}
                        $amountRestTpoint="";
                        $amountRestNotTpoint="$suppl_stock_show";
                    }
                }
                if ($cash_id==1){$price=round($price*$usd_to_uah,2); }
                if ($cash_id==3){$price=round($price*$usd_to_uah/$euro_to_uah,2); }
                if ($cash_id==2){$price=round($price,2);}

                $lst[$i]["kind"]=$kind_id;
                $lst[$i]["relation"]=$relation;

                $check_photo=$this->checkPhotoEmpty($art_id,$article_nr_displ);$img_b="";
                if ($check_photo>0) { $img_b="<button class='btn btn-sm btn-default' onclick='showArtilceGallery(\"$art_id\",\"$article_nr_displ\")'><i class='fa fa-image'></i></button>";}
                $suppl_storage_code=0;
                $lst[$i]["data"]="<tr style='cursor:pointer'><td class='text-center'>$img_b</td><td class='text-center'>{kind_name}</td>".$range_list."</tr>";
                $lst[$i]["data"]=str_replace("{art_id}",$art_id,$lst[$i]["data"]);
                $lst[$i]["data"]=str_replace("{article_nr_displ}",$article_nr_displ,$lst[$i]["data"]);
                $lst[$i]["data"]=str_replace("{brand_name}",$brand_name,$lst[$i]["data"]);
                $lst[$i]["data"]=str_replace("{name}",$name,$lst[$i]["data"]);
                $lst[$i]["data"]=str_replace("{info}",$info,$lst[$i]["data"]);
                $lst[$i]["data"]=str_replace("{barcode}",$barcode,$lst[$i]["data"]);
                $lst[$i]["data"]=str_replace("{goods_group_id}",$goods_group_name,$lst[$i]["data"]);
                $lst[$i]["data"]=str_replace("{suppl_id}",$suppl_id,$lst[$i]["data"]);
                $lst[$i]["data"]=str_replace("{price}",$price,$lst[$i]["data"]);
                $lst[$i]["data"]=str_replace("{amountRestTpoint}",$amountRestTpoint,$lst[$i]["data"]);
                $lst[$i]["data"]=str_replace("{amountRestNotTpoint}",$amountRestNotTpoint,$lst[$i]["data"]);
                $lst[$i]["data"]=str_replace("{suppl_storage_code}","$suppl_storage_code",$lst[$i]["data"]);
                $lst[$i]["data"]=str_replace("{warranty_info}",$warranty_info,$lst[$i]["data"]);
                $lst[$i]["data"]=str_replace("{return_delay}",$return_delay,$lst[$i]["data"]);
                $lst[$i]["data"]=str_replace("{delivery_info}",$delivery_info,$lst[$i]["data"]);
                $lst[$i]["data"]=str_replace("{tpoint_suppl}",$tpoint_suppl_name,$lst[$i]["data"]);

                if ($doc_type=="dp"){
                    if ($suppl_id==0 || $storage_id>0){
                        $function_select_article="setArticleToSelectAmountDp('$art_id','$article_nr_displ','$brand_id','$brand_name','$dp_id');";
                    }
                    if ($suppl_id>0 && $storage_id==0){
                        $function_select_article="showSupplStorageSelectWindow('$art_id','$article_nr_displ','$brand_id','$brand_name','dp','$dp_id');";
                        $reserv_type_color="danger";
                    }
                    $lst[$i]["data"]=str_replace("{function_select_article}",$function_select_article,$lst[$i]["data"]);
                    $lst[$i]["data"]=str_replace("{reserv_type_color}",$reserv_type_color,$lst[$i]["data"]);
                }
            }
            $lst_kr=array();
            for ($i=1;$i<=$n;$i++){
                $kind=$lst[$i]["kind"];
                $relation=$lst[$i]["relation"];
                if ($kind==0 && $relation==0){ $lst_kr[1].=$lst[$i]["data"]; }
                if ($kind==1 && $relation==0){ $lst_kr[2].=$lst[$i]["data"]; }
                if (($kind==3 || $kind==4) && $relation==0){ $lst_kr[2].=$lst[$i]["data"]; }
                if (($kind==3 || $kind==4) && $relation==1){ $lst_kr[3].=$lst[$i]["data"]; }
                if (($kind==3 || $kind==4) && $relation==2){ $lst_kr[4].=$lst[$i]["data"]; }
                if ($kind=="" || $relation==""){$lst_kr[5].=$lst[$i]["data"];}

            }//$kol_f+=1;
            if ($lst_kr[1]!=""){$lst_kr[1]=str_replace("{kind_name}","<i style=\"width: 100%;height: 60px;\" title=\"запитаний артикул\" class=\"fa fa-key\"></i>",$lst_kr[1]);$list.=$lst_kr[1];}
            if ($lst_kr[2]!=""){$lst_kr[2]=str_replace("{kind_name}","<i style=\"width: 100%;height: 60px;\" title=\"аналог\" class=\"fa fa-link\"></i>",$lst_kr[2]);$list.=$lst_kr[2];}
            if ($lst_kr[3]!=""){$lst_kr[3]=str_replace("{kind_name}","<i style=\"width: 100%;height: 60px;\" title=\"артикул присутні в\" class=\"fa fa-level-down\"></i>",$lst_kr[3]);$list.=$lst_kr[3];}
            if ($lst_kr[4]!=""){$lst_kr[4]=str_replace("{kind_name}","<i style=\"width: 100%;height: 60px;\" title=\"артикул включає в себе\" class=\"fa fa-level-up\"></i>",$lst_kr[4]);$list.=$lst_kr[4];}
            if ($lst_kr[5]!=""){$lst_kr[5]=str_replace("{kind_name}","<i style=\"width: 100%;height: 60px;\" title=\"інше\" class=\"fa fa-ellipsis-h\"></i>",$lst_kr[5]);$list.=$lst_kr[5];}
        }
        return array($header_list,$list,$list2);
    }

    function checkSupplStorageAllow($suppl_id,$storage_id){$db=DbSingleton::getDb();
        $r=$db->query("select count(id) as kol from `A_CLIENTS_STORAGE` where visible='1' and id='$storage_id' and client_id='$suppl_id' limit 0,1;");$allow=$db->result($r,0,"kol")+0;
        return $allow;
    }

    function checkSupplStorageTpointAllow($tpoint_id,$suppl_id,$storage_id){$db=DbSingleton::getDb();
        $r=$db->query("select count(id) as kol from `T_POINT_SUPPL_STORAGE` where tpoint_id='$tpoint_id' and storage_id='$storage_id' and suppl_id='$suppl_id' limit 0,1;");$allow=$db->result($r,0,"kol")+0;
        return $allow;
    }

    function showSupplStorageSelectWindow($art_id,$article_nr_displ,$brand_id,$doc_type,$doc_id){$form="";
        $form_htm=RD."/tpl/catalogue_select_suppl_storage_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $list=$this->showArticleSupplStorageRestList($art_id,$article_nr_displ,$brand_id,$doc_type,$doc_id);
        $form=str_replace("{list}",$list,$form);
        $form=str_replace("{art_id}",$art_id,$form);
        $form=str_replace("{article_nr_displ}",$article_nr_displ,$form);
        return $form;
    }

    function showArticleSupplStorageRestList($art_id,$article_nr_displ,$brand_id,$doc_type,$doc_id){$db=DbSingleton::getTokoDb();$slave=new slave;
        require_once RD.'/lib/dp_class.php';$dp=new dp;
        $doc_type=$slave->qq($doc_type);$doc_id=$slave->qq($doc_id);$tpoint_id=0;
        $price_lvl=$margin_price_lvl=$margin_price_suppl_lvl=$client_vat=0;
        $cash_id=$usd_to_uah=$euro_to_uah=1;$suppl_id=$suppl_stock=$dp_id=$suppl_storage_id=0;
        $suppl_storage_code=$amountRestNotTpoint=0; $delivery_info=$return_delay=$warranty_info="";
        if ($doc_type=="dp"){$dp_id=$doc_id;
            list($price_lvl,$margin_price_lvl,$price_suppl_lvl,$margin_price_suppl_lvl,$client_vat)=$dp->getDpClientPriceLevels($dp_id);
            $tpoint_id=$dp->getDpTpoint($dp_id); //$tpoint_name=$this->getTpointName($tpoint_id);
            //$tpoint_storage_list=$this->getTpointStorageList($tpoint_id);
            $cash_id=$dp->getDpCashId($dp_id);list($usd_to_uah,$euro_to_uah)=$dp->getKoursData();
        }
        $query="select t2a.ART_ID, t2a.BRAND_ID, t2a.ARTICLE_NR_DISPL, t2b.BRAND_NAME, t2n.NAME, t2n.INFO, t2bc.BARCODE, gg.NAME as goods_group_name, t2sai.suppl_id, t2sai.return_delay, t2sai.warranty_info , t2si.price_usd, t2si.client_storage_id, t2si.stock_suppl
        from T2_ARTICLES t2a 
            left outer join T2_BRANDS t2b on t2b.BRAND_ID=t2a.BRAND_ID 
            left outer join T2_NAMES t2n on t2n.ART_ID=t2a.ART_ID 
            left outer join T2_BARCODES t2bc on t2bc.ART_ID=t2a.ART_ID 
            left outer join T2_GOODS_GROUP t2gg on t2gg.ART_ID=t2a.ART_ID 
            left outer join GOODS_GROUP gg on gg.ID=t2gg.GOODS_GROUP_ID 
            left outer join T2_SUPPL_ARTICLES_IMPORT t2sai on (t2sai.art_id=t2a.ART_ID)
            left outer join T2_SUPPL_IMPORT t2si on (t2si.art_id=t2a.ART_ID and t2si.suppl_id=t2sai.suppl_id and t2si.status=1)
            left outer join T_POINT_SUPPL_STORAGE pss on (pss.tpoint_id='$tpoint_id' and pss.suppl_id=t2sai.suppl_id and pss.storage_id=t2si.client_storage_id)
        where t2a.ART_ID = '$art_id' and t2b.`VISIBLE`='1' and t2sai.suppl_id>0;";
        $r=$db->query($query);$n=$db->num_rows($r);	$list="";
        
        for ($i=1;$i<=$n;$i++){
            //$tpoint_suppl_name="";
            $del_row=0;
            $art_id=$db->result($r,$i-1,"ART_ID");
            //$kind_id=$ak[$art_id];
            //$relation=$rk[$art_id];
            $brand_id=$db->result($r,$i-1,"BRAND_ID");
            $article_nr_displ=$db->result($r,$i-1,"ARTICLE_NR_DISPL");
            $brand_name=$db->result($r,$i-1,"BRAND_NAME");
            //$name=$db->result($r,$i-1,"NAME");
            //$info=$db->result($r,$i-1,"INFO");
            //$barcode=$db->result($r,$i-1,"BARCODE");
            //$goods_group_name=$db->result($r,$i-1,"goods_group_name");
            //$unit_name=$db->result($r,$i-1,"unit_name");
            $price=0;

            if ($doc_type=="dp"){
                $suppl_id=$db->result($r,$i-1,"suppl_id");
                if ($suppl_id==0){
                    $price=$db->result($r,$i-1,"price_".$price_lvl);
                    if ($margin_price_lvl>0){
                        $price=$price+round($price*$margin_price_lvl/100,2);
                    }
                    $storage_id=$db->result($r,$i-1,"storage_id");$storage_name=$db->result($r,$i-1,"storage_name");
                    $suppl_storage_code=$storage_name;
                   // $cell_id=$db->result($r,$i-1,"cell_id");
                    //$cell_name=$db->result($r,$i-1,"cell_name");
                    //$stock=$db->result($r,$i-1,"stock");
                    //$reserv=$db->result($r,$i-1,"reserv");
                    //$dp_amount=$dp->getArticleInDp($art_id,$dp_id);

                    //list($tpoint_stock,$tpoint_reserv)=$dp->getArticleRestTpoint($art_id,$tpoint_id);
                    //$amountRestTpoint="<span class='label label-primary'>$tpoint_stock/$tpoint_reserv</span>";
                    //$amountRestTpoint="$stock/$reserv";
                    list($not_tpoint_stock,$not_tpoint_reserv)=$dp->getArticleRestNotTpoint($art_id,$tpoint_id); $amountRestNotTpoint="<span class='label label-warning'>$not_tpoint_stock/$not_tpoint_reserv</span>";
                    $delivery_info=$dp->getTpointDeliveryInfo($tpoint_id,$storage_id);
                    //$tpoint_suppl_name=$tpoint_name;
                }
                if ($suppl_id>0){
                    $return_delay=$db->result($r,$i-1,"return_delay");
                    $warranty_info=$db->result($r,$i-1,"warranty_info");
                    $suppl_price_usd=$db->result($r,$i-1,"price_usd");

                    $suppl_storage_id=$db->result($r,$i-1,"client_storage_id"); $suppl_storage_name=$this->getSupplStorageName($suppl_storage_id);
                    if ($this->checkSupplStorageAllow($suppl_id,$suppl_storage_id)==1){
                        if ($this->checkSupplStorageTpointAllow($tpoint_id,$suppl_id,$suppl_storage_id)==1){
                            $suppl_stock=$db->result($r,$i-1,"stock_suppl");
                            list($price_in_vat,$show_in_vat,$price_add_vat)=$dp->getSupplVatConditions($suppl_id);

                            $suppl_storage_code="$suppl_storage_name (".$suppl_id.".".$suppl_storage_id.")";
                            $price_suppl=$suppl_price_usd;

                            list($suppl_margin_fm,$suppl_delivery_fm,$suppl_margin2_fm)=$dp->getTpointSupplFm($tpoint_id,$suppl_id,$suppl_storage_id,$price_suppl,$price_suppl_lvl);
                            if ($suppl_margin_fm>0){
                                $price=($price_suppl+$price_suppl*$suppl_margin_fm/100)-$price_suppl;
                                if ($price>$suppl_delivery_fm){ $price=($price_suppl+$price_suppl*$suppl_margin_fm/100); }
                                if ($price<=$suppl_delivery_fm){ $price=$price_suppl+$price_suppl*$suppl_margin2_fm/100+$suppl_delivery_fm; }
                                if ($margin_price_suppl_lvl>0 && $margin_price_suppl_lvl!=""){
                                    $price=$price+$price*$margin_price_suppl_lvl/100;
                                }
                            }
                            //if ($client_vat==0){
                                //if ($price_in_vat==1 && $show_in_vat==1 && $price_add_vat==1){
                                    //$price=$price+$price*20/100;
                                //}
                                //if ($price_in_vat==0){
    //									$price=0;$sh_vat=0;$del_row=1;
                                //}
                            //}
                            if ($client_vat==1){
                               // if ($price_in_vat==1 && $show_in_vat==1){
                                    //$price=$price+$price*20/100;
                               // }
                                if ($price_in_vat==0 && $show_in_vat==1 && $price_add_vat==1){
                                    $price=$price+$price*20/100;
                                }
                                if ($price_in_vat==0 && $show_in_vat==0){
                                    $price=0;/*$sh_vat=0;*/$del_row=1;
                                }
                            }
                            //else{$del_row=1;}
                            $suppl_stock_show=$suppl_stock;
                            if ($suppl_stock_show>=10){$suppl_stock_show=">10";}
                            $amountRestNotTpoint="$suppl_stock_show";
                            $delivery_info=$dp->getTpointSupplDeliveryInfo($tpoint_id,$suppl_id,$suppl_storage_id);
                        }else{$del_row=1;}
                    }else{$del_row=1;}
                }
            }
            if ($cash_id==1){$price=round($price*$usd_to_uah,2); }
            if ($cash_id==3){$price=round($price*$usd_to_uah/$euro_to_uah,2); }
            if ($cash_id==2){$price=round($price,2);}

            if ($suppl_id>0 && ($price>0 || $suppl_stock>0) && $del_row==0){
                $list.="<tr style='cursor:pointer' onClick=\"showDpSupplAmountInputWindow('$art_id','$article_nr_displ','$brand_id','$brand_name','$dp_id','$suppl_id','$suppl_storage_id','$price');\">
                    <td class='text-center'>$i</td>
                    <td class='text-center'>$suppl_storage_code</td>
                    <td class='text-center'>$amountRestNotTpoint</td>
                    <td class='text-center'>$price</td>
                    <td class='text-center'>$delivery_info</td>
                    <td class='text-center'>$return_delay</td>
                    <td class='text-center'>$warranty_info</td>
                </tr>";
            }
        }
        return $list;
    }

    function getSupplStorageName($suppl_storage_id){ $db=DbSingleton::getDb(); $name="";
        $r=$db->query("select name from A_CLIENTS_STORAGE where id='$suppl_storage_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){	$name=$db->result($r,0,"name");}
        return $name;
    }

    function showCatalogueBrandSelectListDoc($r,$code_search){$db=DbSingleton::getDb(); $list="";
        $n=$db->num_rows($r);$tkey=time();
        $db->query("CREATE TEMPORARY TABLE IF NOT EXISTS `NBRAND_RESULT_$tkey` (`art_id` INT NOT NULL ,`display_nr` VARCHAR( 100 ) NOT NULL ,`name` VARCHAR( 255 ) NOT NULL ,`brand_id` INT NOT NULL ,`brand_name` VARCHAR( 100 ) NOT NULL ,`kol_res` TINYINT NOT NULL) ENGINE = MYISAM ;");
        for ($i=1;$i<=$n;$i++){
            $art_id=$db->result($r,$i-1,"ART_ID");
            $display_nr=$db->result($r,$i-1,"DISPLAY_NR");
            $name=$db->result($r,$i-1,"NAME");
            $brand_id=$db->result($r,$i-1,"BRAND_ID");
            $brand_name=$db->result($r,$i-1,"BRAND_NAME");
            $kol_res=0;
            $db->query("insert into `NBRAND_RESULT_$tkey` values ('$art_id','$display_nr','$name','$brand_id','$brand_name','$kol_res');");
        }
        $r=$db->query("select * from `NBRAND_RESULT_$tkey` order by `kol_res` desc;");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            //$art_id=$db->result($r,$i-1,"art_id");
            $display_nr=$db->result($r,$i-1,"display_nr");
            $name=$db->result($r,$i-1,"name");
            $brand_id=$db->result($r,$i-1,"brand_id");
            $brand_name=$db->result($r,$i-1,"brand_name");
            //$kol_res=$db->result($r,$i-1,"kol_res");
            $display_nr2=str_replace("/","--",$display_nr);
            $list.="<tr style='cursor:pointer;' onClick='setArticleSearchBrand(\"$display_nr2\",\"$brand_id\");'>
                <td>$display_nr</td>
                <td>$brand_name</td>
                <td>$name</td>
            </tr>";
        }$form="";
        if ($n>0){
            $form_htm=RD."/tpl/catalogue_brand_select_list.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
            $form=str_replace("{list}",$list,$form);
        }
        $db->query("DROP TEMPORARY TABLE IF EXISTS `NBRAND_RESULT_$tkey`;");
        return $form;
    }

    function checkPhotoEmpty($art_id,$disp_nomber){ $db=DbSingleton::getTokoDb();
        $r=$db->query("select count(t2af.ID) as kol from T2_PHOTOS t2af where t2af.ART_ID='$art_id';"); $n=$db->result($r,0,"kol")+0;
        return $n;
    }

    function showIndexAddForm() {
        $form="";$form_htm=RD."/tpl/catalogue_index_add_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $art_id=$this->getMaxIndex();
        $form=str_replace("{art_id}",$art_id,$form);
        $form=str_replace("{brand_list}",$this->showBrandsSelect(),$form);
        return $form;
    }

    function getMaxIndex() {$db=DbSingleton::getTokoDb();
        $r=$db->query("SELECT max(`ART_ID`) as max_art FROM `T2_ARTICLES` where `ART_ID`>100000000 and `ART_ID` <110000000");
        $art_id=intval($db->result($r,0,"max_art"))+1;
        return $art_id;
    }

    function getMaxSupplIndex() {$db=DbSingleton::getTokoDb();
        $r=$db->query("SELECT max(`ART_ID`) as max_art FROM `T2_ARTICLES` where `ART_ID`>10000000");
        $art_id=intval($db->result($r,0,"max_art"))+1;
        return $art_id;
    }

    function showBrandsSelect() { $db=DbSingleton::getTokoDb();
        $r=$db->query("select b.* from T2_BRANDS b order by b.BRAND_NAME asc");
        $n=$db->num_rows($r); $list="";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"BRAND_ID");
            $name=$db->result($r,$i-1,"BRAND_NAME");
            $list.="<option value=".$id.">".$name."</option>";
        }
        return $list;
    }

    function saveIndexArticle($art_id,$suppl_status,$article_nr_displ,$brand_id,$article_name,$article_name_ukr,$article_info) { $db=DbSingleton::getTokoDb();
        $r=$db->query("select ART_ID from T2_ARTICLES where ART_ID=$art_id;"); $n=$db->num_rows($r);
        $article_nr_search=str_replace(str_split('\\/:*?"<>|+-()[]., '), '', $article_nr_displ);
        if ($art_id>0 && $art_id!="" && $n==0) {
            $db->query("insert into T2_ARTICLES (ART_ID,ARTICLE_NR_DISPL,ARTICLE_NR_SEARCH,BRAND_ID) values ($art_id,'$article_nr_displ','$article_nr_search',$brand_id);");
            $db->query("insert into T2_CROSS (ART_ID,SEARCH_NUMBER,`KIND`,BRAND_ID,DISPLAY_NR,`RELATION`) values ($art_id,'$article_nr_search',0,$brand_id,'$article_nr_displ',0);");
            $db->query("insert into T2_NAMES (ART_ID,LANG_ID,`NAME`,`INFO`) values ($art_id,'16','$article_name','$article_info');");
            $db->query("insert into T2_NAMES (ART_ID,LANG_ID,`NAME`,`INFO`) values ($art_id,'41','$article_name_ukr','$article_info');");
            return true;
        } else return false;
    }
	
}
