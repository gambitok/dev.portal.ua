<?php

class undistribcells {

    function show_undistribcells_list(){$db=DbSingleton::getDb();
        $r=$db->query("select t2asc.*, i.prefix, i.doc_nom, i.data, s.name as storage_name, sc.cell_value  
        from T2_ARTICLES_STRORAGE_CELLS t2asc
            left outer join J_INCOME i on i.id=t2asc.INCOME_ID
            left outer join STORAGE s on s.id=t2asc.STORAGE_ID
            left outer join STORAGE_CELLS sc on sc.id=t2asc.STORAGE_CELLS_ID
        where sc.default='1' and i.status='1' and i.oper_status='31' group by t2asc.STORAGE_CELLS_ID,INCOME_ID;");$n=$db->num_rows($r);$list="";

        for ($i=1;$i<=$n;$i++){
            $prefix=$db->result($r,$i-1,"prefix");
            $doc_nom=$db->result($r,$i-1,"doc_nom");if ($doc_nom==0){$doc_nom="-";}
            $data=$db->result($r,$i-1,"data");
            $storage_name=$db->result($r,$i-1,"storage_name");
            $cell_value=$db->result($r,$i-1,"cell_value");
            $income_id=$db->result($r,$i-1,"income_id");
            $storage_cells_id=$db->result($r,$i-1,"storage_cells_id");
            $amount=$this->countUndistribCellsArticles($storage_cells_id,$income_id);
            $list.="<tr style='cursor:pointer' onClick='showUndistribCellsCard(\"$storage_cells_id\",\"$income_id\")'>
                <td>$prefix$doc_nom</td>
                <td align='center'>$data</td>
                <td>$storage_name / $cell_value</td>
                <td align='right'>$amount</td>
            </tr>";
        }
        return $list;
    }

    function countUndistribCellsArticles($storage_cells_id,$income_id){$db=DbSingleton::getDb();$amount=0;
        $r=$db->query("select SUM(amount) as cellAmount from T2_ARTICLES_STRORAGE_CELLS t2asc
            left outer join J_INCOME i on i.id=t2asc.INCOME_ID
            left outer join STORAGE s on s.id=t2asc.STORAGE_ID
            left outer join STORAGE_CELLS sc on sc.id=t2asc.STORAGE_CELLS_ID
        where sc.default='1' and i.status='1' and i.oper_status='31' and t2asc.STORAGE_CELLS_ID='$storage_cells_id' and t2asc.INCOME_ID='$income_id';");$n=$db->num_rows($r);
        if ($n==1){ $amount=$db->result($r,0,"cellAmount");}
        return $amount;
    }

    function showUndistribCellsCard($storage_cells_id,$income_id){$db=DbSingleton::getDb();$cat=new catalogue;
        $prefix=$data=$storage_name=$cell_value="";$doc_nom=0;
        $form="";$form_htm=RD."/tpl/undistribcells_list_articles.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $query="select t2asc.*,ist.article_nr_displ,ist.brand_id, i.prefix, i.doc_nom, i.data, s.name as storage_name, sc.cell_value  from T2_ARTICLES_STRORAGE_CELLS t2asc
            left outer join J_INCOME i on i.id=t2asc.INCOME_ID
            left outer join J_INCOME_STR ist on ist.income_id=t2asc.INCOME_ID and ist.art_id=t2asc.ART_ID
            left outer join STORAGE s on s.id=t2asc.STORAGE_ID
            left outer join STORAGE_CELLS sc on sc.id=t2asc.STORAGE_CELLS_ID
        where sc.default='1' and i.status='1' and i.oper_status='31' and t2asc.STORAGE_CELLS_ID='$storage_cells_id' and t2asc.INCOME_ID='$income_id';";
        $r=$db->query($query);$n=$db->num_rows($r);$list="";
        for ($i=1;$i<=$n;$i++){
            $art_id=$db->result($r,$i-1,"art_id");
            $amount=$db->result($r,$i-1,"amount");
            $article_nr_displ=$db->result($r,$i-1,"article_nr_displ");
            $brand_id=$db->result($r,$i-1,"brand_id");
            $brand_name=$cat->getBrandName($brand_id);
            $prefix=$db->result($r,$i-1,"prefix");
            $doc_nom=$db->result($r,$i-1,"doc_nom");if ($doc_nom==0){$doc_nom="-";}
            $data=$db->result($r,$i-1,"data");
            $storage_id=$db->result($r,$i-1,"storage_id");
            $storage_name=$db->result($r,$i-1,"storage_name");
            $cell_value=$db->result($r,$i-1,"cell_value");
            $list.="<tr align='center'>
                <td>$i</td>
                <td>$article_nr_displ</td>
                <td>$brand_name</td>
                <td align='right'>$amount</td>
                <td><button class='btn btn-sm btn-warning' onclick='showStorageCellSelectForm(\"$art_id\",\"$storage_id\",\"$income_id\",\"$amount\");'><i class='fa fa-delicious' title='Переміщення у комірку'></i></button></td>
            </tr>";
        }
        $form=str_replace("{art_list}",$list,$form);
        $form=str_replace("{income_doc_prefix_nom}",$prefix.$doc_nom,$form);
        $form=str_replace("{income_data}",$data,$form);
        $form=str_replace("{storage_name}",$storage_name,$form);
        $form=str_replace("{storage_cell_name}",$cell_value,$form);
        $form=str_replace("{storage_cells_id}",$storage_cells_id,$form);
        return array($form);
    }

    function showStorageCellSelectForm($art_id,$income_id,$storage_id,$amount){$cat=new catalogue;
        $form="";$form_htm=RD."/tpl/undistribcells_storage_cells_select_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        list($article_nr_displ,$brand_id,$brand_name)=$cat->getArticleNrDisplBrand($art_id);
        $form=str_replace("{art_id}",$art_id,$form);
        $form=str_replace("{article_nr_displ}",$article_nr_displ." ".$brand_name,$form);
        $form=str_replace("{income_id}",$income_id,$form);
        $form=str_replace("{storage_id}",$storage_id,$form);
        $form=str_replace("{amount}",$amount,$form);
        $form=str_replace("{storage_cells_list}",$this->showStorageCellsSelectList($storage_id,0),$form);
        return array($form);
    }

    function showStorageSelectList($sel_id){$db=DbSingleton::getDb();
        $r=$db->query("select * from `STORAGE` where status='1' order by name,id asc;");$n=$db->num_rows($r);$list="";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"name");
            $sel="";if ($sel_id==$id){$sel="selected='selected'";}
            $list.="<option value='$id' $sel>$name</option>";
        }
        return $list;
    }

    function showStorageCellsSelectList($storage_id,$sel_id){$db=DbSingleton::getDb();
        $r=$db->query("select * from `STORAGE_CELLS` where status='1' and storage_id='$storage_id' order by cell_value,id asc;");$n=$db->num_rows($r);$list="";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $cell_value=$db->result($r,$i-1,"cell_value");
            $sel="";if ($sel_id==$id){$sel="selected='selected'";}
            $list.="<option value='$id' $sel>$cell_value</option>";
        }
        return $list;
    }

    function saveUndistribCellsStorageCellForm($art_id,$income_id,$storage_id,$storage_cells_id,$amount){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка збереження даних!";
        $art_id=$slave->qq($art_id);$income_id=$slave->qq($income_id);$storage_id=$slave->qq($storage_id);$storage_cells_id=$slave->qq($storage_cells_id);$amount=$slave->qq($amount);
        if ($art_id>0 && $income_id>0 && $storage_id>0 && $storage_cells_id>0 && $amount>0){
            $r=$db->query("select  * from T2_ARTICLES_STRORAGE_CELLS where storage_id='$storage_id' and `income_id`='$income_id' and `art_id`='$art_id' limit 0,1;");$n=$db->num_rows($r);
            if ($n==1){
                $doc_amount=$db->result($r,0,"AMOUNT");
                $doc_storage_cells_id=$db->result($r,0,"STORAGE_CELLS_ID");
                if ($doc_storage_cells_id!=$storage_cells_id){
                    $new_amount=$doc_amount-$amount;$op=0;
                    if ($new_amount>0){
                        $db->query("update T2_ARTICLES_STRORAGE_CELLS set `AMOUNT`='$new_amount' where  `ART_ID`='$art_id' and `INCOME_ID`='$income_id' and `STORAGE_ID`='$storage_id' and `STORAGE_CELLS_ID`='$doc_storage_cells_id';");$op=1;
                    }
                    if ($new_amount==0){
                        $db->query("delete from T2_ARTICLES_STRORAGE_CELLS where  `ART_ID`='$art_id' and `INCOME_ID`='$income_id' and `STORAGE_ID`='$storage_id' and `STORAGE_CELLS_ID`='$doc_storage_cells_id';");$op=1;
                    }
//                    if ($new_amount<0){
//                        $answer=0;$err="Кількість переміщеного товару перевищує фактичну наявну!";
//                    }
                    if ($op==1){
                        $db->query("insert into T2_ARTICLES_STRORAGE_CELLS (`ART_ID`,`AMOUNT`,`INCOME_ID`,`STORAGE_ID`,`STORAGE_CELLS_ID`) values ('$art_id','$amount','$income_id','$storage_id','$storage_cells_id');");
                    }
                }
//                if ($doc_storage_cells_id==$storage_cells_id){
//                     $answer=0;$err="Оберіть комірку для переміщення товару відмінну від поточної!";
//                }
            }
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function showCountryForm($id){$db=DbSingleton::getDb();$manual=new manual;
        $form="";$form_htm=RD."/tpl/undistribcells_country_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
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

    function saveundistribcellsCountryForm($id,$name,$alfa2,$alfa3,$duty,$risk){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка збереження даних!";
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
        $form="";$form_htm=RD."/tpl/undistribcells_costums_list.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
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
        $form="";$form_htm=RD."/tpl/undistribcells_costums_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
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

    function saveundistribcellsCostumsForm($id,$name,$preferential_rate,$full_rate,$type_declaration,$sertification,$gos_standart){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка збереження даних!";
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

    function loadArticleZED($undistribcells_id){$db=DbSingleton::getDb();
        $form="";$form_htm=RD."/tpl/undistribcells_zed.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select t2z.*,t2c.COUNTRY_NAME, t2s.COSTUMS_NAME from T2_ZED t2z 
            left outer join T2_COUNTRIES t2c on t2c.COUNTRY_ID=t2z.COUNTRY_ID
            left outer join T2_COSTUMS t2s on t2s.COSTUMS_ID=t2z.COSTUMS_ID
        where t2z.ART_ID='$undistribcells_id' limit 0,1;");;
        $country_id=$db->result($r,0,"COUNTRY_ID");
        $country_name=$db->result($r,0,"COUNTRY_NAME");
        $costums_id=$db->result($r,0,"COSTUMS_ID");
        $costums_name=$db->result($r,0,"COSTUMS_NAME");
        $form=str_replace("{undistribcells_id}",$undistribcells_id,$form);
        $form=str_replace("{country_id}",$country_id,$form);
        $form=str_replace("{country_name}",$country_name,$form);
        $form=str_replace("{costums_id}",$costums_id,$form);
        $form=str_replace("{costums_name}",$costums_name,$form);
        return $form;
    }

    function saveundistribcellsZED($undistribcells_id,$country_id,$costums_id){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка збереження даних!";
        $undistribcells_id=$slave->qq($undistribcells_id);$country_id=$slave->qq($country_id);$costums_id=$slave->qq($slave->point_valid($costums_id));
        if ($undistribcells_id>0){
            //T2_ZED UPDATE
            $r=$db->query("select * from `T2_ZED` where `ART_ID`='$undistribcells_id' limit 0,1;");$n=$db->num_rows($r);
            if ($n==0){
                $db->query("insert into T2_ZED (`ART_ID`,`COUNTRY_ID`,`COSTUMS_ID`) values ('$undistribcells_id','$country_id','$costums_id');");
            }
            if ($n==1){
                $db->query("update T2_ZED set `COUNTRY_ID`='$country_id', `COSTUMS_ID`='$costums_id' where `ART_ID`='$undistribcells_id';");
            }
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

}