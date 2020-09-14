<?php

class suppl {

    function clearArticle($art){
        $art=str_replace(" ","",$art);$art=str_replace("_","",$art);$art=str_replace("-","",$art);$art=str_replace(".","",$art);$art=str_replace("+","",$art);
        $art=str_replace("'","",$art);$art=str_replace("/","",$art);$art=str_replace('"',"",$art);$art=preg_replace ("/[^a-zA-ZА-Яа-я0-9\s]/","",$art);$art=strtolower($art);
        return $art;
    }

    function getBrandName($id){$db=DbSingleton::getTokoDb();$name="";
        $r=$db->query("SELECT `BRAND_NAME` FROM `T2_BRANDS` WHERE `BRAND_ID`='$id' LIMIT 1;");$n=$db->num_rows($r);
        if ($n==1){	$name=$db->result($r,0,"BRAND_NAME");	}
        return $name;
    }

    function getCashName($cash_id){$db=DbSingleton::getDb();$name="";
        $r=$db->query("SELECT `name` FROM `CASH` WHERE `id`='$cash_id' LIMIT 1;");$n=$db->num_rows($r);
        if ($n==1){$name=$db->result($r,0,"name");}
        return $name;
    }

    function show_suppl_list(){$db=DbSingleton::getDb();
        $r=$db->query("SELECT c.*, t2cn.COUNTRY_NAME, t2st.STATE_NAME 
        FROM `A_CLIENTS` c 
            LEFT OUTER JOIN `A_CLIENTS_CATEGORY` cc on cc.client_id=c.id 
            LEFT OUTER JOIN `T2_COUNTRIES` t2cn on t2cn.COUNTRY_ID=c.country 
            LEFT OUTER JOIN `T2_STATE` t2st on t2st.STATE_ID=c.state
        WHERE c.status='1' AND cc.category_id='2';");$n=$db->num_rows($r);$list="";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"name");
            $country=$db->result($r,$i-1,"COUNTRY_NAME");
            $state=$db->result($r,$i-1,"STATE_NAME");
            $list.="<tr style='cursor:pointer' onClick='showSupplCard(\"$id\")'>
                <td>$id</td>
                <td>$name</td>
                <td>$country</td>
                <td>$state</td>
            </tr>";
        }
        return $list;
    }

    function showSupplCard($suppl_id){$db=DbSingleton::getDb();
        $slave=new slave;session_start();$user_id=$_SESSION["media_user_id"];$user_name=$_SESSION["user_name"];$clients=new clients;
        $form="";$form_htm=RD."/tpl/suppl_card.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("SELECT c.*, ot.full_name as ot_full_name, t2cn.COUNTRY_NAME, t2st.STATE_NAME, t2rg.REGION_NAME, t2ct.CITY_NAME 
        FROM `A_CLIENTS` c  
            LEFT OUTER JOIN `A_ORG_TYPE` ot on ot.id=c.org_type
            LEFT OUTER JOIN `T2_COUNTRIES` t2cn on t2cn.COUNTRY_ID=c.country 
            LEFT OUTER JOIN `T2_STATE` t2st on t2st.STATE_ID=c.state
            LEFT OUTER JOIN `T2_REGION` t2rg on t2rg.REGION_ID=c.region
            LEFT OUTER JOIN `T2_CITY` t2ct on t2ct.CITY_ID=c.city
        WHERE c.id='$suppl_id' AND c.status='1' LIMIT 1;");$n=$db->num_rows($r);
        if ($n==0){$form_htm=RD."/tpl/access_deny.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);} }
        if ($n==1){
            $org_type_full_name=$db->result($r,0,"ot_full_name");
            $full_name=$db->result($r,0,"full_name");
            $name=$db->result($r,0,"name");
            $email=$db->result($r,0,"email");
            $phone=$db->result($r,0,"phone");
            $parrent_id=$db->result($r,0,"parrent_id");
            $country=$db->result($r,0,"COUNTRY_NAME");
            $state=$db->result($r,0,"STATE_NAME");
            $city=$db->result($r,0,"CITY_NAME");
            $form=str_replace("{suppl_id}",$suppl_id,$form);
            $form=str_replace("{org_type_name}",$org_type_full_name,$form);
            $form=str_replace("{parrent_name}",$clients->getClientNameById($parrent_id,"name"),$form);
            $form=str_replace("{client_full_name}",$slave->qqback_in($full_name),$form);
            $form=str_replace("{client_name}",$slave->qqback_in($name),$form);
            $form=str_replace("{email}",$email,$form);
            $form=str_replace("{phone}",$phone,$form);
            $form=str_replace("{country}",$country,$form);
            $form=str_replace("{state}",$state,$form);
            $form=str_replace("{region}",$city,$form);
            $form=str_replace("{city}",$city,$form);
            $form=str_replace("{my_user_id}",$user_id,$form);
            $form=str_replace("{my_user_name}",$user_name,$form);
        }
        return $form;
    }

    function saveSupplGeneralInfo($suppl_id,$name,$full_name,$address,$chief,$country_id,$state_id,$region_id,$city_id){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();
        $slave=new slave;$answer=0;$err="Помилка збереження даних!";
        $suppl_id=$slave->qq($suppl_id);$name=$slave->qq($name);$full_name=$slave->qq($full_name);$address=$slave->qq($address);$chief=$slave->qq($chief);
        $country_id=$slave->qq($country_id);$state_id=$slave->qq($state_id);$city_id=$slave->qq($city_id);$region_id=$slave->qq($region_id);
        if ($suppl_id>0){
            $db->query("UPDATE `T_POINT` SET `name`='$name',status='1', `full_name`='$full_name', `address`='$address', `chief`='$chief', `country`='$country_id', `state`='$state_id', `city`='$city_id', `region`='$region_id' WHERE `id`='$suppl_id';");
            $dbt->query("UPDATE `T_POINT` SET `name`='$name', status='1', `full_name`='$full_name', `address`='$address', `chief`='$chief', `country`='$country_id', `state`='$state_id', `city`='$city_id', `region`='$region_id' WHERE `id`='$suppl_id';");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function loadSupplIndex($suppl_id){
        $form="";$form_htm=RD."/tpl/suppl_index_table.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        list(,,$pre_table)=$this->showCsvPreviewIndex($suppl_id);
        $form=str_replace("{records_list}","<tr><td colspan=10 align='center'>Записи не завантажено</td></tr>",$form);
        $form=str_replace("{import_file_name}","Оберіть файл",$form);
        $form=str_replace("{csv_str_file}",$pre_table,$form);
        $form=str_replace("{suppl_id}",$suppl_id,$form);
        return $form;
    }

    function loadSupplVat($suppl_id){$db=DbSingleton::getDb();
        $form="";$form_htm=RD."/tpl/suppl_vat_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("SELECT * FROM `A_CLIENTS_VAT_CONDITIONS` WHERE `client_id`='$suppl_id' LIMIT 1;");
        $price_in_vat=$db->result($r,0,"price_in_vat");$price_in_vat_checked=""; if ($price_in_vat==1){$price_in_vat_checked="checked";}
        $show_in_vat=$db->result($r,0,"show_in_vat");$show_in_vat_checked=""; if ($show_in_vat==1){$show_in_vat_checked="checked";}
        $price_add_vat=$db->result($r,0,"price_add_vat");$price_add_vat_checked=""; if ($price_add_vat==1){$price_add_vat_checked="checked";}
        $form=str_replace("{suppl_id}",$suppl_id,$form);
        $form=str_replace("{price_in_vat_checked}",$price_in_vat_checked,$form);
        $form=str_replace("{show_in_vat_checked}",$show_in_vat_checked,$form);
        $form=str_replace("{price_add_vat_checked}",$price_add_vat_checked,$form);
        return $form;
    }

    function saveSupplVat($suppl_id,$price_in_vat,$show_in_vat,$price_add_vat){$db=DbSingleton::getDb();
        $slave=new slave;$answer=0;$err="Помилка збереження даних!";
        $suppl_id=$slave->qq($suppl_id);$price_in_vat=$slave->qq($price_in_vat);$show_in_vat=$slave->qq($show_in_vat);$price_add_vat=$slave->qq($price_add_vat);
        if ($suppl_id>0){
            $r=$db->query("SELECT COUNT(`client_id`) as kol FROM `A_CLIENTS_VAT_CONDITIONS` WHERE `client_id`='$suppl_id';");$ex=$db->result($r,0,"kol");
            if ($ex==0){$db->query("INSERT INTO `A_CLIENTS_VAT_CONDITIONS` (`client_id`) VALUES ('$suppl_id');");}
            $db->query("UPDATE `A_CLIENTS_VAT_CONDITIONS` SET `price_in_vat`='$price_in_vat', `show_in_vat`='$show_in_vat', `price_add_vat`='$price_add_vat' WHERE `client_id`='$suppl_id';");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function loadSupplOrderInfo($suppl_id){$db=DbSingleton::getDb();
        $form="";$form_htm=RD."/tpl/suppl_info_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("SELECT `info` FROM `A_CLIENTS_SUPPL_INFO` WHERE `client_id`='$suppl_id' LIMIT 1;");$n=$db->num_rows($r);$info="";
        if ($n==1){
            $info=$db->result($r,0,"info");
        }
        $form=str_replace("{info}",$info,$form);
        $form=str_replace("{suppl_id}",$suppl_id,$form);
        return $form;
    }

    function saveSupplOrderInfo($suppl_id,$info){$db=DbSingleton::getDb();
        $slave=new slave;$answer=0;$err="Помилка збереження даних!";
        $suppl_id=$slave->qq($suppl_id);$info=$slave->qq($info);
        if ($suppl_id>0){
            $r=$db->query("SELECT COUNT(`client_id`) as kol FROM `A_CLIENTS_SUPPL_INFO` WHERE `client_id`='$suppl_id';");$ex=$db->result($r,0,"kol");
            if ($ex==0){$db->query("INSERT INTO `A_CLIENTS_SUPPL_INFO` (`client_id`) VALUES ('$suppl_id');");}
            $db->query("UPDATE `A_CLIENTS_SUPPL_INFO` SET `info`='$info' WHERE `client_id`='$suppl_id';");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    /*================================================================================================================*/

    function loadSupplPrice($suppl_id){
        $form="";$form_htm=RD."/tpl/suppl_price_table.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $header="";$header_htm=RD."/tpl/suppl_price_header.htm";if (file_exists("$header_htm")){ $header = file_get_contents($header_htm);}
        list(,$csv_file_name,$pre_table)=$this->showCsvPreviewPrice($suppl_id);
        //$table=$this->showTablePreview($suppl_id);
        $table="";
        $form=str_replace("{ibox_header}",$table=="" ? $header : "",$form);
        $form=str_replace("{records_list}","<tr><td colspan=10 align='center'>Записи не завантажено</td></tr>",$form);
        $form=str_replace("{import_file_name}",$csv_file_name,$form);
        $form=str_replace("{suppl_id}",$suppl_id,$form);
        $form=str_replace("{csv_str_file}",$table=="" ? $pre_table : "",$form);
        $form=str_replace("{table_str_file}",$table,$form);
        return $form;
    }

    function loadSupplPricePrefix($suppl_id) {$db=DbSingleton::getDb();
        $list="<button class='btn btn-primary' onclick='showSupplPricePrefix();'><i class='fa fa-plus'></i> Додати префікс</button><br><br>"; $arr=[];
        $r=$db->query("SELECT * FROM `SUPPL_BRANDS_PREFIX` WHERE `suppl_id`='$suppl_id';"); $n=$db->num_rows($r);
        for($i=1;$i<=$n;$i++) {
            $prefix_id=$db->result($r,$i-1,"id");
            $suppl_brand=$db->result($r,$i-1,"suppl_brand");
            $brand_id=$db->result($r,$i-1,"brand_id");
            $brand_name=$this->getBrandName($brand_id);
            if (empty($arr[$suppl_brand])) $arr[$suppl_brand]=[];
            array_push($arr[$suppl_brand],["prefix_id"=>$prefix_id,"brand_name"=>$brand_name]);
        }

        foreach ($arr as $brand => $prefixes) {
            $list.="<b>$brand:</b><div>";
            foreach ($prefixes as $prefix) {
                $p_id=$prefix["prefix_id"];
                $br=$prefix["brand_name"];
                $list.="<a onclick='showSupplPricePrefix($p_id);'>`$br`</a><br>";
            }
            $list.="</div><br>";
        }

        return $list;
    }

    function showSupplPricePrefix($prefix_id=0) {$db=DbSingleton::getDb();
        $form="";$form_htm=RD."/tpl/suppl_price_prefix_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        if ($prefix_id==0) {
            $r=$db->query("SELECT MAX(`id`) as mid FROM `SUPPL_BRANDS_PREFIX`;");
            $prefix_id=0+$db->result($r,0,"mid")+1;
            $suppl_brand="";
            $brand_id=0;
            $prefix="";
            $return_delay=14;
            $warranty_info="";
        } else {
            $r=$db->query("SELECT * FROM `SUPPL_BRANDS_PREFIX` WHERE `id`=$prefix_id LIMIT 1;");
            $suppl_brand=$db->result($r,0,"suppl_brand");
            $brand_id=$db->result($r,0,"brand_id");
            $prefix=$db->result($r,0,"prefix");
            $return_delay=$db->result($r,0,"return_delay");
            $warranty_info=$db->result($r,0,"warranty_info");
        }
        $form=str_replace("{prefix_id}",$prefix_id,$form);
        $form=str_replace("{suppl_brand}",$suppl_brand,$form);
        $form=str_replace("{brand_select}",$this->getBrandList($brand_id),$form);
        $form=str_replace("{prefix}",$prefix,$form);
        $form=str_replace("{return_delay}",$return_delay,$form);
        $form=str_replace("{warranty_info}",$warranty_info,$form);
        return $form;
    }

    function getBrandList($sel) {$db=DbSingleton::getTokoDb();
        $r=$db->query("SELECT * FROM `T2_BRANDS`;"); $n=$db->num_rows($r); $list="";
        for ($i=1;$i<=$n;$i++) {
            $brand_id=$db->result($r,$i-1,"BRAND_ID");
            $brand_name=$db->result($r,$i-1,"BRAND_NAME");
            $brand_id==$sel ? $selected="selected" : $selected="";
            $list.="<option value='$brand_id' $selected>$brand_name</option>";
        }
        return $list;
    }

    function saveSupplPricePrefix($prefix_id, $suppl_id, $suppl_brand, $brand_id, $prefix, $return_delay, $warranty_info) {$db=DbSingleton::getDb();
        $answer=0;$err="Помилка збереження даних!";
        if ($prefix_id>0){
            $r=$db->query("SELECT * FROM `SUPPL_BRANDS_PREFIX` WHERE `id`='$prefix_id' LIMIT 1;"); $n=$db->num_rows($r);
            if ($n>0)
                $db->query("UPDATE `SUPPL_BRANDS_PREFIX` SET `suppl_id`='$suppl_id', `suppl_brand`='$suppl_brand', `brand_id`='$brand_id', `prefix`='$prefix', `return_delay`='$return_delay', `warranty_info`='$warranty_info' WHERE `id`='$prefix_id' LIMIT 1;");
            else
                $db->query("INSERT INTO `SUPPL_BRANDS_PREFIX` (`suppl_id`, `suppl_brand`, `brand_id`, `prefix`, `return_delay`, `warranty_info`) VALUE ('$suppl_id', '$suppl_brand', '$brand_id', '$prefix', '$return_delay', '$warranty_info');");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function dropSupplPricePrefix($prefix_id) {$db=DbSingleton::getDb();
        $answer=0;$err="Помилка збереження даних!";
        if ($prefix_id>0){
            $db->query("DELETE FROM `SUPPL_BRANDS_PREFIX` WHERE `id`='$prefix_id' LIMIT 1;");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function getSupplStorageArray($suppl_id) {$db=DbSingleton::getDb();
        $r=$db->query("SELECT `id`, `name` FROM `A_CLIENTS_STORAGE` WHERE `status`='1' AND `client_id`='$suppl_id' ORDER BY `name` ASC;");$n=$db->num_rows($r);$st=array();
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"name");
            $st[$i]["id"]=$id;
            $st[$i]["name"]=$name;
        }
        return $st;
    }

    function findCachId($suppl_cash,$cash_data){$id=0;
        foreach($cash_data as $cash){
            if ($cash["abr"]==$suppl_cash) { $id=$cash["id"];break; }
            if ($cash["abr2"]==$suppl_cash){ $id=$cash["id"];break; }
            if ($cash["name"]==$suppl_cash){ $id=$cash["id"];break; }
        }
        return $id;
    }

    function showCsvPreviewIndex($suppl_id){$db=DbSingleton::getDb();
        $csv_exist=0;$csv_file_name="Оберіть файл";$pre_table="<h3 align='center'>Записи відсутні</h3>";$krs=0;$sheet=0;$fn=0;$kol_cols=0;
        $r=$db->query("SELECT * FROM `T2_SUPPL_CSV` WHERE `suppl_id`='$suppl_id' AND `ftype`='index' LIMIT 1;");$n=$db->num_rows($r);
        //require_once RD."/lib/clients_class.php"; $clients=new clients;
        if ($n==1){
            $file_name=$db->result($r,0,"file_name");
            $file_path=RD."/cdn/suppl_files/index/$suppl_id/$file_name";
            if (file_exists($file_path)){
                $form="";$form_htm=RD."/tpl/suppl_index_str_file.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
                $cols_list=""; $records_list="";
                $fna=explode(".",$file_name);$ft=count($fna);$file_type=$fna[$ft-1];
                if ($file_type=="xlsx"){
                    require_once(RD.'/lib/excel-2/php-excel-reader/excel_reader2.php');
                    require_once(RD.'/lib/excel-2/SpreadsheetReader.php');
                    $Spreadsheet = new SpreadsheetReader($file_path);
                    $Sheets=$Spreadsheet->Sheets(); $Spreadsheet->ChangeSheet(0);$kol_cols=0;
                    foreach ($Spreadsheet as $Key => $Row){$krs+=1;
                        if($Row){$row="";$j=0;
                            foreach($Row as $rw){$j+=1;
                                if ($j==1){$row="<td>$krs</td>";}
                                $row.="<td>".trim(iconv("UTF-8", "Windows-1251",$rw))."</td>";
                            }
                            if ($row!=""){
                                $records_list.="<tr>$row</tr>";
                                if ($j>$kol_cols){$kol_cols=$j;}
                            }
                        }
                        if ($krs==20){break;}
                    }
                    if ($records_list!=""){
                        for ($i=1;$i<=$kol_cols;$i++){
                            $cols_list.="<th><select id=\"clm-$i\" style='width:auto;' size='1'><option value='0'>-</option><option value='1'>Індекс</option><option value='2'>Бренд</option><option value='3'>ART_ID</option><option value='4'>Дні для повернення</option><option value='5'>Текст гарантії</option></select></th>";
                        }
                    }
                }
                if ($file_type=="xls"){
                    require_once RD.'/lib/excel/excel_reader2.php';
                    set_time_limit(0);
                    $data = new Spreadsheet_Excel_Reader($file_path,true,"CP1251");
                    $rows=$data->rowcount($sheet); $kol_cols=$data->colcount($sheet);
                    if ($rows==0){ $sheet=0; $rows=$data->rowcount($sheet);$kol_cols=$data->colcount($sheet);}

                    for ($i=1;$i<=$rows;$i++){ $row="";
                        for ($j=1;$j<=$kol_cols;$j++){
                            if ($i==1){$cols_list.="<th><select id=\"clm-$j\" style='width:auto;' size='1'><option value='0'>-</option><option value='1'>Індекс</option><option value='2'>Бренд</option><option value='4'>Дні для повернення</option><option value='5'>Текст гарантії</option></select></th>";}
                            if ($j==1){$row="<td>$i</td>";}
                            $row.="<td>".trim($data->val($i,$j,$sheet))."</td>";
                        }
                        if ($row!=""){
                            $records_list.="<tr>$row</tr>";
                        }
                        if ($i==20){break;}
                    }
                }
                if ($file_type=="csv"){
                    $handle = @fopen($file_path, "r");
                    if ($handle) { //$db->query("DELETE FROM catalogue_price WHERE provider='$provider';");
                        set_time_limit(0);$max_cols=0;
                        while (($buffer = fgets($handle, 4096)) !== false) {$fn+=1;
                            $buf=explode(";",$buffer);
                            if ($buffer!=""){

                                if ($fn==1){$kol_cols=count($buf);}
                                $buf=str_replace("'","\'",$buf);$buf=str_replace('"','\"',$buf);$row="";$ex_cols=0;
                                if ($max_cols<$kol_cols){$ex_cols=1;$cols_list="";}
                                for ($i=1;$i<=$kol_cols;$i++){
                                    if ($i==1){$row="<td>$fn</td>";}
                                    $row.="<td>".trim($buf[$i-1])."</td>";
                                    if ($ex_cols==1){
                                        $cols_list.="<th><select id=\"clm-$i\" size='1'><option value='0'>-</option><option value='1'>Індекс</option><option value='2'>Бренд</option><option value='3'>ART_ID</option><option value='4'>Дні для повернення</option><option value='5'>Текст гарантії</option></select></th>";
                                    }
                                }if ($row!=""){
                                    $records_list.="<tr>$row</tr>";
                                }
                            }
                            if ($fn==20){break;}
                        }
                        fclose($handle);
                    }
                }
                $form=str_replace("{suppl_id}",$suppl_id,$form);
                $form=str_replace("{cols_list}",$cols_list,$form);
                $form=str_replace("{records_list}",$records_list,$form);
                $form=str_replace("{kol_cols}",$kol_cols,$form);
                $csv_file_name=$file_name;$csv_exist=1;$pre_table=$form;
            }
        }
        return array($csv_exist,$csv_file_name,$pre_table);
    }

    function finishSupplIndexImport($suppl_id,$start_row,$kol_cols,$cols){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();
        $slave=new slave;$answer=0;$err="Помилка збереження даних!";
        $suppl_id=$slave->qq($suppl_id);$start_row=$slave->qq($start_row);$kol_cols=$slave->qq($kol_cols);$cols=$slave->qq($cols);$return_delay=$warranty_info=0;
        if ($suppl_id>0){
            $r=$db->query("SELECT * FROM `T2_SUPPL_CSV` WHERE `suppl_id`='$suppl_id' AND `ftype`='index' LIMIT 1;");$n=$db->num_rows($r);
            if ($n==1){
                $file_name=$db->result($r,0,"file_name");
                $file_path=RD."/cdn/suppl_files/index/$suppl_id/$file_name";
                if (file_exists($file_path)){
                    $index=0;$brand=0;$art_id=0;
                    for ($i=1;$i<=$kol_cols;$i++){
                        if ($cols[$i]==1){$index=$i;}
                        if ($cols[$i]==2){$brand=$i;}
                        if ($cols[$i]==3){$art_id=$i;}
                        if ($cols[$i]==4){$return_delay=$i;}
                        if ($cols[$i]==5){$warranty_info=$i;}
                    }
                    $fna=explode(".",$file_name);$ft=count($fna);$file_type=$fna[$ft-1];$krs=0;
                    if ($file_type=="xlsx"){
                        require_once(RD.'/lib/excel-2/php-excel-reader/excel_reader2.php');
                        require_once(RD.'/lib/excel-2/SpreadsheetReader.php');
                        $Spreadsheet = new SpreadsheetReader($file_path);
                        $Sheets=$Spreadsheet->Sheets(); $Spreadsheet->ChangeSheet(0);
                        set_time_limit(0);
                        $dbt->query("DELETE FROM `T2_SUPPL_ARTICLES_IMPORT` WHERE `suppl_id`='$suppl_id';");$pkg_k=0;$max_pkg=500;$pkg="";
                        foreach ($Spreadsheet as $Key => $Row){$krs+=1;
                            if($Row){
                                if ($krs>=$start_row){
                                    $suppl_index=trim(iconv("UTF-8", "Windows-1251",$Row[$index-1]));
                                    $suppl_brand=trim(iconv("UTF-8", "Windows-1251",$Row[$brand-1]));
                                    $suppl_art_id=trim(iconv("UTF-8", "Windows-1251",$Row[$art_id-1]));
                                    $suppl_return_delay=trim(iconv("UTF-8", "Windows-1251",$Row[$return_delay-1]));
                                    $suppl_warranty_info=trim(iconv("UTF-8", "Windows-1251",$Row[$warranty_info-1]));
                                    if ($pkg!=""){$pkg.=",";}
                                    $pkg.="('$suppl_id','$suppl_index','$suppl_brand','$suppl_art_id','$suppl_return_delay','$suppl_warranty_info')";
                                    $pkg_k+=1;
                                    if ($pkg_k==$max_pkg){ $dbt->query("INSERT INTO `T2_SUPPL_ARTICLES_IMPORT` (`suppl_id`,`suppl_index`,`suppl_brand`,`art_id`,`return_delay`,`warranty_info`) VALUES $pkg;");$pkg="";$pkg_k=0;}
                                }
                            }
                            //	if ($krs==5){break;}
                        }
                        if ($pkg!=""){ $dbt->query("INSERT INTO `T2_SUPPL_ARTICLES_IMPORT` (`suppl_id`,`suppl_index`,`suppl_brand`,`art_id`,`return_delay`,`warranty_info`) VALUES $pkg;");/*$pkg="";$pkg_k=0;*/}
                        if (file_exists(RD."/cdn/suppl_files/index/$suppl_id/$file_name")){unlink(RD."/cdn/suppl_files/index/$suppl_id/$file_name");}
                        $db->query("DELETE FROM `T2_SUPPL_CSV` WHERE `suppl_id`='$suppl_id' AND `ftype`='index';");
                        $answer=1;$err="";
                    }
                    if ($file_type=="csv"){
                        $handle = @fopen($file_path, "r");
                        if ($handle) { set_time_limit(0);
                            $dbt->query("DELETE FROM `T2_SUPPL_ARTICLES_IMPORT` WHERE `suppl_id`='$suppl_id';");$pkg_k=0;$max_pkg=500;$pkg="";
                            while (($buffer = fgets($handle, 4096)) !== false) {$krs+=1;
                                $buf=explode(";",$buffer);
                                if ($buffer!=""){
                                    if ($krs>=$start_row){
                                        $buf=str_replace("'","\'",$buf);$buf=str_replace('"','\"',$buf);
                                        $suppl_index=trim($buf[$index-1]);
                                        $suppl_brand=trim($buf[$brand-1]);
                                        $suppl_art_id=trim($buf[$art_id-1]);
                                        $suppl_return_delay=trim($buf[$return_delay-1]);
                                        $suppl_warranty_info=trim($buf[$warranty_info-1]);
                                        if ($pkg!=""){$pkg.=",";}
                                        $pkg.="('$suppl_id','$suppl_index','$suppl_brand','$suppl_art_id','$suppl_return_delay','$suppl_warranty_info')";
                                        $pkg_k+=1;
                                        if ($pkg_k==$max_pkg){ $dbt->query("INSERT INTO `T2_SUPPL_ARTICLES_IMPORT` (`suppl_id`,`suppl_index`,`suppl_brand`,`art_id`,`return_delay`,`warranty_info`) VALUES $pkg;");$pkg="";$pkg_k=0;}
                                        //if ($krs==30){break;}
                                    }
                                }
                            }
                            if ($pkg!=""){ $dbt->query("INSERT INTO `T2_SUPPL_ARTICLES_IMPORT` (`suppl_id`,`suppl_index`,`suppl_brand`,`art_id`,`return_delay`,`warranty_info`) VALUES $pkg;");/*$pkg="";$pkg_k=0;*/}
                            fclose($handle);
                        }
                        if (file_exists(RD."/cdn/suppl_files/index/$suppl_id/$file_name")){unlink(RD."/cdn/suppl_files/index/$suppl_id/$file_name");}
                        $db->query("DELETE FROM `T2_SUPPL_CSV` WHERE `suppl_id`='$suppl_id' AND `ftype`='index';");
                        $answer=1;$err="";
                    }
                }
            }
        }
        return array($answer,$err);
    }

    function showCsvPreviewPrice($suppl_id){$db=DbSingleton::getDb();
        $csv_exist=0;$csv_file_name="Оберіть файл";$pre_table="<h3 align='center'>Записи відсутні</h3>";$krs=0;$sheet=0;$fn=0;$kol_cols=0;$storage_list="";
        $r=$db->query("SELECT * FROM `T2_SUPPL_CSV` WHERE `suppl_id`='$suppl_id' AND `ftype`='price' LIMIT 1;");$n=$db->num_rows($r);$clients=new clients;
        if ($n==1){
            $file_name=$db->result($r,0,"file_name");
            $file_path=RD."/cdn/suppl_files/price/$suppl_id/$file_name";
            if (file_exists($file_path)){
                $storages=$this->getSupplStorageArray($suppl_id);
                $form="";$form_htm=RD."/tpl/price_str_file.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
                $cols_list=""; $records_list="";
                $fna=explode(".",$file_name);$ft=count($fna);$file_type=$fna[$ft-1];
                if ($file_type=="xlsx"){
                    require_once(RD.'/lib/excel-2/php-excel-reader/excel_reader2.php');
                    require_once(RD.'/lib/excel-2/SpreadsheetReader.php');
                    $Spreadsheet = new SpreadsheetReader($file_path);
                    $Sheets=$Spreadsheet->Sheets(); $Spreadsheet -> ChangeSheet(0);$kol_cols=0;
                    foreach ($Spreadsheet as $Key => $Row){$krs+=1;
                        if($Row){$row="";$j=0;
                            foreach($Row as $rw){$j+=1;
                                if ($j==1){$row="<td>$krs</td>";}
                                $row.="<td>".trim(iconv("UTF-8", "Windows-1251",$rw))."</td>";
                            }
                            if ($row!=""){
                                $records_list.="<tr>$row</tr>";
                                if ($j>$kol_cols){$kol_cols=$j;}
                            }
                        }
                        if ($krs==20){break;}
                    }
                    if ($records_list!=""){
                        $storage_list="";
                        foreach($storages as $st){
                            $storage_list.="<option value='stor_".$st["id"]."'>".$st["name"]."</option>";
                        }
                        for ($i=1;$i<=$kol_cols;$i++){
                            $cols_list.="<th><select id=\"clm-$i\" style='width:auto;' size='1'><option value='0'>-</option><option value='1'>Індекс</option><option value='2'>Бренд</option><option value='3'>Ціна</option><option value='4'>Валюта</option>$storage_list</select></th>";
                        }
                    }
                }
                if ($file_type=="xls"){
                    // require_once RD.'/lib/excel/excel_reader2.php';
                    require_once(RD.'/lib/excel-2/php-excel-reader/excel_reader2.php');
                    set_time_limit(0);
                    $data = new Spreadsheet_Excel_Reader($file_path,true,"CP1251");
                    $rows=$data->rowcount($sheet); $kol_cols=$data->colcount($sheet);
                    if ($rows==0){ $sheet=0; $rows=$data->rowcount($sheet);$kol_cols=$data->colcount($sheet);}

                    for ($i=1;$i<=$rows;$i++){ $row="";
                        for ($j=1;$j<=$kol_cols;$j++){
                            if ($i==1){$cols_list.="<th><select id=\"clm-$j\" style='width:auto;' size='1'><option value='0'>-</option><option value='1'>Індекс</option><option value='2'>Бренд</option><option value='3'>Ціна</option><option value='4'>Валюта</option>$storage_list</select></th>";}
                            if ($j==1){$row="<td>$i</td>";}
                            $row.="<td>".trim($data->val($i,$j,$sheet))."</td>";
                        }
                        if ($row!=""){
                            $records_list.="<tr>$row</tr>";
                        }
                        if ($i==20){break;}
                    }
                }
                if ($file_type=="csv"){
                    $handle = @fopen($file_path, "r");
                    if ($handle) { //$db->query("DELETE FROM catalogue_price WHERE provider='$provider';");
                        set_time_limit(0);$max_cols=0;
                        while (($buffer = fgets($handle, 4096)) !== false) {$fn+=1;
                            $buf=explode(";",$buffer);
                            if ($buffer!=""){
                                if ($fn==1){$kol_cols=count($buf);}
                                $buf=str_replace("'","\'",$buf);$buf=str_replace('"','\"',$buf);$row="";$ex_cols=0;
                                if ($max_cols<$kol_cols){$ex_cols=1;$cols_list="";}
                                for ($i=1;$i<=$kol_cols;$i++){
                                    if ($i==1){$row="<td>$fn</td>";}
                                    $row.="<td>".trim($buf[$i-1])."</td>";
                                    if ($ex_cols==1){
                                        $storage_list="";
                                        foreach($storages as $st){
                                            $storage_list.="<option value='stor_".$st["id"]."'>".$st["name"]."</option>";
                                        }
                                        $cols_list.="<th><select id=\"clm-$i\" size='1'><option value='0'>-</option><option value='1'>Індекс</option><option value='2'>Бренд</option><option value='3'>Ціна</option><option value='4'>Валюта</option>$storage_list</select></th>";
                                    }
                                }if ($row!=""){
                                    $records_list.="<tr>$row</tr>";
                                }
                            }
                            if ($fn==20){break;}
                        }
                        fclose($handle);
                    }
                }
                $form=str_replace("{suppl_id}",$suppl_id,$form);
                $form=str_replace("{cols_list}",$cols_list,$form);
                $form=str_replace("{records_list}",$records_list,$form);
                $form=str_replace("{kol_cols}",$kol_cols,$form);
                $form=str_replace("{cash_list}",$clients->showCashListSelect(1,1),$form);
                $csv_file_name=$file_name;$csv_exist=1;$pre_table=$form;
            }
        }
        return array($csv_exist,$csv_file_name,$pre_table);
    }

    function finishSupplPriceImport($suppl_id,$start_row,$kol_cols,$main_cash_id,$kours_usd,$kours_eur,$cols){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();
        $slave=new slave;$answer=0;$err="Помилка збереження даних!";$clients=new clients;$price=0;
        session_start();$user_id=$_SESSION["media_user_id"];
        $suppl_id=$slave->qq($suppl_id);$start_row=$slave->qq($start_row);$kol_cols=$slave->qq($kol_cols);$main_cash_id=$slave->qq($main_cash_id);
        $kours_usd=$slave->qq($kours_usd);$kours_eur=$slave->qq($kours_eur);$cols=$slave->qq($cols);
        $suppl_cash_id=2;
        if ($suppl_id>0){
            $kours_usd=str_replace(",",".",$kours_usd);$kours_eur=str_replace(",",".",$kours_eur);
            $r=$db->query("SELECT * FROM `T2_SUPPL_CSV` WHERE `suppl_id`='$suppl_id' AND `ftype`='price' LIMIT 1;");$n=$db->num_rows($r);
            if ($n==1){
                $file_name=$db->result($r,0,"file_name");
                $file_path=RD."/cdn/suppl_files/price/$suppl_id/$file_name";
                if (file_exists($file_path)){
                    $storages=$this->getSupplStorageArray($suppl_id); $kol_storages=count($storages);$suppl_storages_use=array();
                    $cash_data=$clients->getCashDataArray();
                    //	$form_htm=RD."/tpl/price_str_file.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
                    $index=0;$brand=0;$cash=0;$storage_str="";
                    for ($i=1;$i<=$kol_cols;$i++){
                        if ($cols[$i]==1){$index=$i;}
                        if ($cols[$i]==2){$brand=$i;}
                        if ($cols[$i]==3){$price=$i;}
                        if ($cols[$i]==4){$cash=$i;}
                        for ($s=1;$s<=$kol_storages;$s++){
                            $storage_id=$storages[$s]["id"];
                            if ($cols[$i]=="stor_".$storage_id){
                                $suppl_storages_use[$storage_id]=$i;
                                $storage_str.="$storage_id,";
                            }
                        }
                    }
                    if ($storage_str!=""){$storage_str=substr($storage_str,0,-1);}if ($storage_str==""){$storage_str=0;}

                    $fna=explode(".",$file_name);$ft=count($fna);$file_type=$fna[$ft-1];$krs=0;
                    if ($main_cash_id>0){$suppl_cash_id=$main_cash_id;}
                    if ($file_type=="xlsx"){
                        require_once(RD.'/lib/excel-2/php-excel-reader/excel_reader2.php');
                        require_once(RD.'/lib/excel-2/SpreadsheetReader.php');
                        $Spreadsheet = new SpreadsheetReader($file_path);
                        $Sheets=$Spreadsheet->Sheets(); $Spreadsheet->ChangeSheet(0);
                        set_time_limit(0);
                        //$dbt->query("UPDATE `T2_SUPPL_IMPORT` SET `status`=0 WHERE `suppl_id`='$suppl_id' AND `client_storage_id` IN ($storage_str);");

                        $dbt->query("INSERT INTO `T2_SUPPL_IMPORT_ARCHIVE` (`suppl_id`,`suppl_index`,`brand`,`art_id`,`price_suppl`,`cash_id`,`kours_usd`,`price_usd`,`client_storage_id`,`stock_suppl`,`data_update`,`status`,`return_delay`,`warranty_info`)
                        SELECT `suppl_id`,`suppl_index`,`brand`,`art_id`,`price_suppl`,`cash_id`,`kours_usd`,`price_usd`,`client_storage_id`,`stock_suppl`,`data_update`,`status`,`return_delay`,`warranty_info` FROM `T2_SUPPL_IMPORT` 
                        WHERE `suppl_id`='$suppl_id' AND `client_storage_id` IN ($storage_str);");
                        $dbt->query("DELETE FROM `T2_SUPPL_IMPORT` WHERE `suppl_id`='$suppl_id' AND `client_storage_id` IN ($storage_str);");

                        $pkg_k=0;$max_pkg=50;$pkg="";
                        foreach ($Spreadsheet as $Key => $Row){$krs+=1;
                            if($Row){
                                if ($krs>=$start_row){
                                    $suppl_index=trim(iconv("UTF-8", "Windows-1251",$Row[$index-1]));
                                    $suppl_brand=trim(iconv("UTF-8", "Windows-1251",$Row[$brand-1]));
                                    $suppl_price=str_replace(",",".",trim(iconv("UTF-8", "Windows-1251",$Row[$price-1])));
                                    if ($main_cash_id==0){
                                        $suppl_cash=trim(iconv("UTF-8", "Windows-1251",$Row[$cash-1]));$suppl_cash_id=$this->findCachId($suppl_cash,$cash_data);
                                    }
                                    $price_usd=0;
                                    if ($suppl_cash_id==2){$price_usd=$suppl_price;}
                                    if ($suppl_cash_id==1){$price_usd=($suppl_price/$kours_usd);}
                                    if ($suppl_cash_id==3){$price_usd=($suppl_price*$kours_eur/$kours_usd);	}

                                    for ($s=1;$s<=$kol_storages;$s++){
                                        $storage_id=$storages[$s]["id"];
                                        $stokCellNom=$suppl_storages_use[$storage_id]-1;
                                        $suppl_stock=trim(iconv("UTF-8", "Windows-1251", preg_replace('/\D/', '', $Row[$stokCellNom])));
                                        if ($suppl_stock>0){
                                            if ($pkg!=""){$pkg.=",";}
                                            $pkg.="('$suppl_id','$suppl_index','$suppl_brand','$suppl_price','$suppl_cash_id','$kours_usd','$price_usd','$storage_id','$suppl_stock',CURDATE())";
                                            $pkg_k+=1;
                                            if ($pkg_k==$max_pkg){ $dbt->query("INSERT INTO `T2_SUPPL_IMPORT` (`suppl_id`, `suppl_index`, `brand`, `price_suppl`, `cash_id`, `kours_usd`, `price_usd`, `client_storage_id`, `stock_suppl`, `data_update`) VALUES $pkg;");$pkg="";$pkg_k=0;}
                                        }
                                    }
                                }
                            }
                            //	if ($krs==5){break;}
                        }
                        if ($pkg!=""){ $dbt->query("INSERT INTO `T2_SUPPL_IMPORT` (`suppl_id`, `suppl_index`, `brand`, `price_suppl`, `cash_id`, `kours_usd`, `price_usd`, `client_storage_id`, `stock_suppl`, `data_update`) VALUES $pkg;");}
                        if (file_exists(RD."/cdn/suppl_files/price/$suppl_id/$file_name")){unlink(RD."/cdn/suppl_files/price/$suppl_id/$file_name");}
                        $db->query("DELETE FROM `T2_SUPPL_CSV` WHERE `suppl_id`='$suppl_id' and `ftype`='price';");
                        $answer=1;$err="";
                    }
                    if ($file_type=="csv"){
                        $handle = @fopen($file_path, "r");
                        if ($handle) {
                            set_time_limit(0);
                            //$dbt->query("UPDATE `T2_SUPPL_IMPORT` SET `status`=0 WHERE `suppl_id`='$suppl_id' AND `client_storage_id` IN ($storage_str);");

                            $dbt->query("INSERT INTO `T2_SUPPL_IMPORT_ARCHIVE` (`suppl_id`,`suppl_index`,`brand`,`art_id`,`price_suppl`,`cash_id`,`kours_usd`,`price_usd`,`client_storage_id`,`stock_suppl`,`data_update`,`status`,`return_delay`,`warranty_info`)
                            SELECT `suppl_id`,`suppl_index`,`brand`,`art_id`,`price_suppl`,`cash_id`,`kours_usd`,`price_usd`,`client_storage_id`,`stock_suppl`,`data_update`,`status`,`return_delay`,`warranty_info` FROM `T2_SUPPL_IMPORT` 
                            WHERE `suppl_id`='$suppl_id' AND `client_storage_id` IN ($storage_str);");
                            $dbt->query("DELETE FROM `T2_SUPPL_IMPORT` WHERE `suppl_id`='$suppl_id' AND `client_storage_id` IN ($storage_str);");

                            $pkg_k=0;$max_pkg=50;$pkg="";
                            while (($buffer = fgets($handle, 4096)) !== false) {$krs+=1;
                                $buf=explode(";",$buffer);
                                if ($krs>=$start_row){
                                    if ($buffer!=""){
                                        $buf=str_replace("'","\'",$buf);$buf=str_replace('"','\"',$buf);
                                        $suppl_index=trim($buf[$index-1]);
                                        $suppl_brand=trim($buf[$brand-1]);
                                        $suppl_price=str_replace(",",".",trim($buf[$price-1]));
                                        if ($main_cash_id==0){
                                            $suppl_cash=trim($buf[$cash-1]);$suppl_cash_id=$this->findCachId($suppl_cash,$cash_data);
                                        }
                                        $price_usd=0;
                                        if ($suppl_cash_id==2){$price_usd=$suppl_price;}
                                        if ($suppl_cash_id==1){$price_usd=round($suppl_price/$kours_usd,2);}
                                        if ($suppl_cash_id==3){$price_usd=round($suppl_price*$kours_eur/$kours_usd,2);	}

                                        for ($s=1;$s<=$kol_storages;$s++){
                                            $storage_id=$storages[$s]["id"];
                                            $stokCellNom=$suppl_storages_use[$storage_id]-1;
                                            $suppl_stock=trim( preg_replace('/\D/', '', $buf[$stokCellNom]));
                                            if ($suppl_stock>0){
                                                if ($pkg!=""){$pkg.=",";}
                                                $pkg.="('$suppl_id','$suppl_index','$suppl_brand','$suppl_price','$suppl_cash_id','$kours_usd','$price_usd','$storage_id','$suppl_stock',CURDATE())";
                                                $pkg_k+=1;
                                                if ($pkg_k==$max_pkg){ $dbt->query("INSERT INTO `T2_SUPPL_IMPORT` (`suppl_id`, `suppl_index`, `brand`, `price_suppl`, `cash_id`, `kours_usd`, `price_usd`, `client_storage_id`, `stock_suppl`, `data_update`) VALUES $pkg;");$pkg="";$pkg_k=0;}
                                            }
                                        }
                                        //if ($krs==30){break;}
                                    }
                                }
                            }
                            if ($pkg!=""){ $dbt->query("INSERT INTO `T2_SUPPL_IMPORT` (`suppl_id`, `suppl_index`, `brand`, `price_suppl`, `cash_id`, `kours_usd`, `price_usd`, `client_storage_id`, `stock_suppl`, `data_update`) VALUES $pkg;");}
                            fclose($handle);
                        }
                        if (file_exists(RD."/cdn/suppl_files/price/$suppl_id/$file_name")){unlink(RD."/cdn/suppl_files/price/$suppl_id/$file_name");}
                        $db->query("DELETE FROM `T2_SUPPL_CSV` WHERE `suppl_id`='$suppl_id' AND `ftype`='price';");
                        $answer=1;$err="";
                    }

                    $db->query("INSERT INTO `cron_suppl_price_import` (`suppl_id`,`user_id`,`status`) VALUES ('$suppl_id','$user_id','1');");

                    $r=$dbt->query("SELECT * FROM `T2_SUPPL_ARTICLES_IMPORT` WHERE `suppl_id`='$suppl_id';");$n=$dbt->num_rows($r);
                    for ($i=1;$i<=$n;$i++){
                        $art_id=$dbt->result($r,$i-1,"art_id");
                        $suppl_brand=$dbt->result($r,$i-1,"suppl_brand");
                        $suppl_index=$dbt->result($r,$i-1,"suppl_index");
                        $return_delay=$dbt->result($r,$i-1,"return_delay");
                        $dbt->query("UPDATE `T2_SUPPL_IMPORT` SET `art_id`='$art_id', `return_delay`='$return_delay' WHERE `suppl_index` LIKE '$suppl_index' AND `suppl_id`='$suppl_id' AND `brand` LIKE '$suppl_brand' AND `status`=1;");
                    }
                }
            }
        }
        return array($answer,$err);
    }

    function showSupplClientList($sel_id){$db=DbSingleton::getDb();$slave=new slave;
        $form="";$form_htm=RD."/tpl/clients_parrent_tree.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("SELECT c.*, ot.name as org_type_name, t2cn.COUNTRY_NAME, t2st.STATE_NAME, t2rg.REGION_NAME, t2ct.CITY_NAME 
        FROM `A_CLIENTS` c 
            LEFT OUTER JOIN `A_ORG_TYPE` ot on ot.id=c.org_type 
            LEFT OUTER JOIN `T2_COUNTRIES` t2cn on t2cn.COUNTRY_ID=c.country 
            LEFT OUTER JOIN `T2_STATE` t2st on t2st.STATE_ID=c.state
            LEFT OUTER JOIN `T2_REGION` t2rg on t2rg.REGION_ID=c.region
            LEFT OUTER JOIN `T2_CITY` t2ct on t2ct.CITY_ID=c.city
            LEFT OUTER JOIN `A_CLIENTS_CATEGORY` cc on cc.client_id=c.id
            LEFT OUTER JOIN `A_CATEGORY` ac on ac.id=cc.category_id
        WHERE c.status=1 AND ac.id=3;");$n=$db->num_rows($r);$list="";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"name");
            $org_type_name=$db->result($r,$i-1,"org_type_name");
            $email=$db->result($r,$i-1,"email");
            $phone=$db->result($r,$i-1,"phone");
            $country=$slave->showTableFieldDBT("T2_COUNTRY","NAME","ID",$db->result($r,$i-1,"country"));
            $state=$slave->showTableFieldDBT("T2_STATE","NAME","ID",$db->result($r,$i-1,"state"));
            $region=$slave->showTableFieldDBT("T2_REGION","NAME","ID",$db->result($r,$i-1,"region"));
            $city=$slave->showTableFieldDBT("T2_CITY","NAME","ID",$db->result($r,$i-1,"city"));
            $cur="";$fn="<i class='fa fa-thumb-tack' onClick='setSupplClient(\"$id\", \"$name\")'></i>";
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

    function showSupplCoopList(){$db=DbSingleton::getDb();
        $r=$db->query("SELECT * FROM `J_SUPPLIERS_COOPERATION`;");$n=$db->num_rows($r);$list="";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $company=$db->result($r,$i-1,"company");
            $name=$db->result($r,$i-1,"name");
            $phone=$db->result($r,$i-1,"phone");
            $email=$db->result($r,$i-1,"email");
            $city_id=$db->result($r,$i-1,"city_id"); $city_name=$this->getCityName($city_id);
            $commentary=$db->result($r,$i-1,"commentary");
            $status=$db->result($r,$i-1,"status");
            $status_cap=$this->getManualCap($status);
            $color="background:{color};"; $clr="";
            if ($status==166){$clr="pink";}
            if ($status==167){$clr="lightyellow";}
            if ($status==168){$clr="lightgreen";}
            $color=str_replace("{color}",$clr,$color);
            $list.="<tr style='cursor:pointer; $color' onClick='showSupplCoopCard(\"$id\")'>
                <td>$id</td>
                <td>$company</td>
                <td>$name</td>
                <td>$phone</td>
                <td>$email</td>
                <td>$city_name</td>
                <td>$commentary</td>
                <td>$status_cap</td>
            </tr>";
        }
        return $list;
    }

    function getManualCap($key) {$db=DbSingleton::getDb();
        $r=$db->query("SELECT `mcaption` FROM `manual` WHERE `id`='$key';");
        $cap=$db->result($r,0,"mcaption");
        return $cap;
    }

    function getManualSelectList($keyId,$selId){$db=DbSingleton::getDb();
        $r=$db->query("SELECT `id`, `mcaption` FROM `manual` WHERE `key`='$keyId';");$n=$db->num_rows($r);$form="";
        for ($i=1;$i<=$n;$i++) {
            $id=$db->result($r,$i-1,"id");
            $form.="<option value='".$id."' ";if ($selId==$id){$form.=" selected='selected'";} $form.=">".$db->result($r,$i-1,"mcaption")."</option>";}
        return $form;
    }

    function showSupplCoopCard($suppl_id){$db=DbSingleton::getDb();
        $slave=new slave; $city_name=$region_name=$state_name=$country_name="";
        $form="";$form_htm=RD."/tpl/suppliers_cooperation_card.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("SELECT c.*, t2ct.CITY_NAME 
        FROM `J_SUPPLIERS_COOPERATION` c 
            LEFT OUTER JOIN `T2_CITY` t2ct on t2ct.CITY_ID=c.city_id
        WHERE c.id='$suppl_id' LIMIT 1;"); $n=$db->num_rows($r);
        if ($n==0){$form_htm=RD."/tpl/access_deny.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);} }
        if ($n==1){
            $company=$db->result($r,0,"company");
            $name=$db->result($r,0,"name");
            $phone=$db->result($r,0,"phone");
            $email=$db->result($r,0,"email");
            $comment=$db->result($r,0,"commentary");
            $file_id=$db->result($r,0,"file_id");
            $status=$db->result($r,0,"status");
            $city_id=$db->result($r,0,"city_id");
            list($region_id,$state_id,$country_id)=$this->getLocation($city_id);
            $form=str_replace("{country_list}",$slave->showSelectList("T2_COUNTRIES","COUNTRY_ID","COUNTRY_NAME",$country_id),$form);
            $form=str_replace("{state_list}",$slave->showSelectSubList("T2_STATE","COUNTRY_ID","$country_id","STATE_ID","STATE_NAME",$state_id),$form);
            $form=str_replace("{region_list}",$slave->showSelectSubList("T2_REGION","STATE_ID","$state_id","REGION_ID","REGION_NAME",$region_id),$form);
            $form=str_replace("{city_list}",$slave->showSelectSubList("T2_CITY","REGION_ID","$region_id","CITY_ID","CITY_NAME",$city_id),$form);
            $form=str_replace("{status_list}",$this->getManualSelectList("suppliers_cooperation_status",$status),$form);
            $form=str_replace("{suppl_id}",$suppl_id,$form);
            $form=str_replace("{suppl_company}",$company,$form);
            $form=str_replace("{suppl_name}",$name,$form);
            $form=str_replace("{suppl_phone}",$phone,$form);
            $form=str_replace("{suppl_email}",$email,$form);
            $form=str_replace("{suppl_comment}",$comment,$form);
            $form=str_replace("{suppl_file}",$file_id,$form);
            $form=str_replace("{suppl_status}",$status,$form);
            $form=str_replace("{city}",$city_name,$form);
            $form=str_replace("{region}",$region_name,$form);
            $form=str_replace("{state}",$state_name,$form);
            $form=str_replace("{country}",$country_name,$form);
        }
        return $form;
    }

    function saveSupplCoop($suppl_id,$company,$name,$phone,$email,$city_id,$comment,$status) { $db=DbSingleton::getDb();
        $answer=0; $err="Помилка збереження даних!";
        if ($suppl_id>0){
            $db->query("UPDATE `J_SUPPLIERS_COOPERATION` SET `company`='$company', `name`='$name', `phone`='$phone', `email`='$email', `city_id`='$city_id', `commentary`='$comment', `status`='$status' WHERE `id`='$suppl_id';");
            $answer=1; $err="";
        }
        return array($answer,$err);
    }

    function getLocation($city_id) { $db=DbSingleton::getDb();
        $r=$db->query("SELECT `REGION_ID` FROM `T2_CITY` WHERE `CITY_ID`='$city_id' LIMIT 1;"); $region_id=$db->result($r,0,"REGION_ID");
        $r=$db->query("SELECT `STATE_ID` FROM `T2_REGION` WHERE `REGION_ID`='$region_id' LIMIT 1;"); $state_id=$db->result($r,0,"STATE_ID");
        $r=$db->query("SELECT `COUNTRY_ID` FROM `T2_STATE` WHERE `STATE_ID`='$state_id' LIMIT 1;"); $country_id=$db->result($r,0,"COUNTRY_ID");
        return array($region_id,$state_id,$country_id);
    }

    function getCityName($city_id) { $db=DbSingleton::getDb();
        $r=$db->query("SELECT `CITY_NAME` FROM `T2_CITY` WHERE `CITY_ID`='$city_id' LIMIT 1;");
        $city_name=$db->result($r,0,"CITY_NAME");
        return $city_name;
    }

    function getFormatName($format_id) {
        $format_name="";
        if ($format_id==1) $format_name="CSV";
        if ($format_id==2) $format_name="Excel";
        if ($format_id==3) $format_name="TXT";
        return $format_name;
    }

    function showSupplImportList() {$db=DbSingleton::getDb();
        $client=new clients;
        $r=$db->query("SELECT * FROM `A_CLIENTS_SUPPL_IMPORT`;");$n=$db->num_rows($r);$list="";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $client_id=$db->result($r,$i-1,"client_id"); $client_name = $client->getClientNameById($client_id);
            $email=$db->result($r,$i-1,"email");
            $file_format=$db->result($r,$i-1,"file_format"); $format_name = $this->getFormatName($file_format);
            $cash_id=$db->result($r,$i-1,"cash_id"); $cash_abr = $client->getCashAbr($cash_id);
            $list.="<tr style='cursor:pointer' onClick='showSupplImportCard(\"$id\")'>
                <td>$id</td>
                <td>$client_name ($client_id)</td>
                <td>$email</td>
                <td>$format_name</td>
                <td>$cash_abr</td>
            </tr>";
        }
        return $list;
    }

    function showSupplImportCard($suppl_id) {$db=DbSingleton::getDb();
        $form="";$form_htm=RD."/tpl/suppl_import_card.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("SELECT * FROM `A_CLIENTS_SUPPL_IMPORT` WHERE `id`='$suppl_id' LIMIT 1;");$n=$db->num_rows($r);
        if ($n==1){
            $client_id=$db->result($r,0,"client_id");
            $email=$db->result($r,0,"email");
            $file_format=$db->result($r,0,"file_format");
            $cash_id=$db->result($r,0,"cash_id");
            $start_row=$db->result($r,0,"start_row");
            $delimiter=$db->result($r,0,"delimiter");
            $column_article=$db->result($r,0,"column_article");
            $column_brand=$db->result($r,0,"column_brand");
            $column_price=$db->result($r,0,"column_price");
            $client_disabled="disabled";
        } else {
            $client_id=0;
            $email="";
            $file_format=1;
            $cash_id=1;
            $start_row=0;
            $delimiter="";
            $column_article=0;
            $column_brand=0;
            $column_price=0;
            $client_disabled="";
        }
        $form=str_replace("{suppl_id}",$suppl_id,$form);
        $form=str_replace("{suppl_email}",$email,$form);
        $form=str_replace("{start_row}",$start_row,$form);
        $form=str_replace("{delimiter}",$delimiter,$form);
        $form=str_replace("{client_list}",$this->getClientSupplList($client_id),$form);
        $form=str_replace("{file_format_list}",$this->getFormatFileList($file_format),$form);
        $form=str_replace("{cash_list}",$this->getCashList($cash_id),$form);
        $form=str_replace("{column_article}",$column_article,$form);
        $form=str_replace("{column_brand}",$column_brand,$form);
        $form=str_replace("{column_price}",$column_price,$form);
        $form=str_replace("{client_disabled}",$client_disabled,$form);
        return $form;
    }

    function saveSupplImportGeneralInfo($suppl_id,$client_id,$email,$file_format,$cash_id,$start_row,$delimiter,$column_article,$column_brand,$column_price) { $db=DbSingleton::getDb();
        $answer=0;$err="Помилка збереження даних!";
        if ($start_row>0 && $column_article>0 && $column_brand>0 && $column_price>0) {
            if ($suppl_id>0){
                $db->query("UPDATE `A_CLIENTS_SUPPL_IMPORT` SET `client_id`='$client_id', `email`='$email', `file_format`='$file_format', `cash_id`='$cash_id', 
                `start_row`='$start_row', `delimiter`='$delimiter',`column_article`='$column_article', `column_brand`='$column_brand', `column_price`='$column_price' WHERE `id`='$suppl_id';");
                $answer=1;$err="";
            }
            if ($suppl_id==0) {
                $db->query("INSERT INTO `A_CLIENTS_SUPPL_IMPORT` (`client_id`,`email`,`file_format`,`cash_id`,`start_row`,`delimiter`,`column_article`,`column_brand`,`column_price`) 
                VALUES ('$client_id','$email','$file_format','$cash_id','$start_row','$delimiter','$column_article','$column_brand','$column_price');");
                $answer=1;$err="";
            }
        }
        return array($answer,$err);
    }

    function previewSupplImport($cash_id,$start_row,$column_article,$column_brand,$column_price) {
        $list = "";
        $max_col = max($column_article,$column_brand,$column_price);
        $max_row = $start_row + 3;
        $numbers="style='background:#f1f1f1;'";
        $cash_abr=$this->getCashName($cash_id);
        for ($i=0; $i<=$max_row; $i++) {
            if ($i==$start_row) $style="style='background:lightyellow;'"; else $style="";
            $list.="<tr $style>";
            for ($j=0; $j<=$max_col; $j++) {
                $text="";
                if ($i==$start_row && $j==$column_article) $text="ARTICLE";
                if ($i==$start_row && $j==$column_brand) $text="BRAND";
                if ($i==$start_row && $j==$column_price) $text="PRICE ($cash_abr)";
                if ($i>$start_row && $j==$column_article) $text="Article Example";
                if ($i>$start_row && $j==$column_brand) $text="Brand Example";
                if ($i>$start_row && $j==$column_price) $text="1000";
                if ($i==0 && $j==0) $list.="<td $numbers></td>"; else
                if ($i==0) $list.="<td $numbers>$j</td>"; else
                if ($j==0) $list.="<td $numbers>$i</td>"; else
                $list.="<td>$text</td>";
            }
            $list.="</tr>";
        }
        return $list;
    }

    function getClientSupplList($client_id) { $db=DbSingleton::getDb();
        $r=$db->query("SELECT c.* FROM `A_CLIENTS` c 
            LEFT OUTER JOIN `A_CLIENTS_CATEGORY` cc ON (cc.client_id=c.id) 
        WHERE c.status='1' AND cc.category_id='2';"); $n=$db->num_rows($r); $list="";

        $r2=$db->query("SELECT * FROM `A_CLIENTS_SUPPL_IMPORT`;"); $n2=$db->num_rows($r2); $clients=[];
        for ($i=1;$i<=$n2;$i++) {
            $cl_id = $db->result($r2, $i-1, "client_id");
            array_push($clients,$cl_id);
        }

        for ($i=1;$i<=$n;$i++) {
            $cl_id = $db->result($r, $i-1, "id");
            $cl_name = $db->result($r, $i-1, "full_name");
            $cl_id==$client_id ? $sel="selected" : $sel="";
            if (!in_array($cl_id,$clients) || $cl_id==$client_id) $list.="<option value='$cl_id' $sel>$cl_name</option>";
        }
        return $list;
    }

    function getFormatFileList($format_id) {
        $format_id==1 ? $sel_csv="selected" : $sel_csv="";
        $format_id==2 ? $sel_xsl="selected" : $sel_xsl="";
        $format_id==3 ? $sel_txt="selected" : $sel_txt="";
        $list="
            <option value='1' $sel_csv>CSV</option>
            <option value='2' $sel_xsl>Excel</option>
            <option value='3' $sel_txt>TXT</option>
        ";
        return $list;
    }

    function getCashList($cash_id) { $db=DbSingleton::getDb();
        $r=$db->query("SELECT * FROM `CASH`;"); $n=$db->num_rows($r); $list="";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"name");
            if ($id==$cash_id) $selected="selected"; else $selected="";
            $list.="<option value='$id' $selected>$name</option>";
        }
        return $list;
    }

    function showCashListSelect($sel_id,$ns="") { $db=DbSingleton::getDb();
        if ($ns==""){$ns=1;}
        $r=$db->query("SELECT * FROM `CASH` ORDER BY `name` ASC;");$n=$db->num_rows($r);$list="";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"abr");
            if ($ns==2){ $name=$db->result($r,$i-1,"name");}
            $sel="";if ($sel_id==$id){$sel="selected='selected'";}
            $list.="<option value='$id' $sel>$name</option>";
        }
        return $list;
    }

    /* SUPPL ORDERS */

    function show_suppl_orders_list() { $db=DbSingleton::getDb();
        $where="";$limit ="LIMIT 0,300"; if ($where!=""){$limit="";}
        $r=$db->query("SELECT j.*, CASH.name as cash_name, (jd.prefix +' '+ jd.doc_nom) as dp_name, tp.name as tpoint_name, c.name as suppl_name, 
        cs.name as suppl_storage_name, mu.name as user_name
        FROM `J_DP_SUPPL_ORDER` j
            LEFT OUTER JOIN `J_DP` jd on jd.id=j.dp_id
            LEFT OUTER JOIN `CASH` on CASH.id=j.cash_id
            LEFT OUTER JOIN `T_POINT` tp on tp.id=j.tpoint_id
            LEFT OUTER JOIN `A_CLIENTS` c on c.id=j.suppl_id
            LEFT OUTER JOIN `A_CLIENTS_STORAGE` cs on cs.id=j.suppl_storage_id
            LEFT OUTER JOIN `media_users` mu on mu.id=j.media_user_id
        WHERE j.status=1 $where ORDER BY j.id DESC $limit;");$n=$db->num_rows($r);$list="";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $dp_id=$db->result($r,$i-1,"dp_id");
            $dp_name=$db->result($r,$i-1,"dp_name");
            $datatime=$db->result($r,$i-1,"datatime");
            $article_nr_displ=$db->result($r,$i-1,"article_nr_displ");
            $brand_name=$db->result($r,$i-1,"brand_name");
            $amount=$db->result($r,$i-1,"amount");
            $price=$db->result($r,$i-1,"price");
            $cash_name=$db->result($r,$i-1,"cash_name");
            $suppl_name=$db->result($r,$i-1,"suppl_name");
            $suppl_storage_name=$db->result($r,$i-1,"suppl_storage_name");
            $tpoint_name=$db->result($r,$i-1,"tpoint_name");
            $delivery_time=$db->result($r,$i-1,"delivery_time");
            $delivery_type_id=$db->result($r,$i-1,"delivery_type_id");
            $suppl_order_status_id=$db->result($r,$i-1,"suppl_order_status_id");
            $suppl_order_doc=$db->result($r,$i-1,"suppl_order_doc");
            $user_name=$db->result($r,$i-1,"user_name");

            $function="showSupplOrder(\"$id\")";
            $list.="<tr style='cursor:pointer' onClick='$function'>
                <td align='center'>$datatime</td>
                <td align='center' data-dpid='$dp_id'>$dp_name</td>
                <td>$article_nr_displ</td>
                <td>$brand_name</td>
                <td>$amount</td>
                <td>$price</td>
                <td>$cash_name</td>
                <td>$suppl_name</td>
                <td>$suppl_storage_name</td>
                <td>$amount</td>
                <td>$tpoint_name</td>
                <td>$delivery_time</td>
                <td>$delivery_type_id</td>
                <td>$suppl_order_status_id</td>
                <td>$suppl_order_doc</td>
                <td>$user_name</td>
            </tr>";
        }
        return $list;
    }

    function showSupplOrder($so_id) { $db=DbSingleton::getDb();
        $cat=new catalogue;$gmanual=new gmanual;
        $form="";$form_htm=RD."/tpl/suppl_orders_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("SELECT j.*, CASH.name as cash_name, CONCAT(jd.prefix ,'-', jd.doc_nom,' від ', jd.time_stamp) as dp_name, tp.name as tpoint_name, 
        c.name as suppl_name, cs.name as suppl_storage_name, mu.name as user_name, si.info as suppl_info, cl.name as client_name
        FROM `J_DP_SUPPL_ORDER` j
            LEFT OUTER JOIN `J_DP` jd on jd.id=j.dp_id
            LEFT OUTER JOIN `CASH` on CASH.id=j.cash_id
            LEFT OUTER JOIN `T_POINT` tp on tp.id=j.tpoint_id
            LEFT OUTER JOIN `A_CLIENTS` cl on cl.id=jd.client_conto_id
            LEFT OUTER JOIN `A_CLIENTS` c on c.id=j.suppl_id
            LEFT OUTER JOIN `A_CLIENTS_STORAGE` cs on cs.id=j.suppl_storage_id
            LEFT OUTER JOIN `A_CLIENTS_SUPPL_INFO` si on si.client_id=c.id
            LEFT OUTER JOIN `media_users` mu on mu.id=j.media_user_id
        WHERE j.status=1 and j.id='$so_id' LIMIT 1;");$n=$db->num_rows($r);

        if ($n==1){
            $dp_name=$db->result($r,0,"dp_name");
            $art_id=$db->result($r,0,"art_id");
            $article_nr_displ=$db->result($r,0,"article_nr_displ");
            $suppl_art_id=$db->result($r,0,"SUPPL_ART_ID");
            $brand_name=$db->result($r,0,"brand_name");
            $amount=$db->result($r,0,"amount");
            $amount_order=$db->result($r,0,"amount_order");
            $cash_id=$db->result($r,0,"cash_id");
            $client_name=$db->result($r,0,"client_name");
            $suppl_name=$db->result($r,0,"suppl_name");
            $suppl_info=$db->result($r,0,"suppl_info");
            $suppl_storage_name=$db->result($r,0,"suppl_storage_name");
            $tpoint_name=$db->result($r,0,"tpoint_name");
            $delivery_data=$db->result($r,0,"delivery_data"); if ($delivery_data=="0000-00-00"){$delivery_data="";}
            $delivery_time=substr($db->result($r,0,"delivery_time"),0,5);
            $delivery_data_finish=$db->result($r,0,"delivery_data_finish");if ($delivery_data_finish=="0000-00-00"){$delivery_data_finish="";}
            $delivery_time_finish=substr($db->result($r,0,"delivery_time_finish"),0,5);
            $delivery_type_id=$db->result($r,0,"delivery_type_id");
            $suppl_order_status_id=$db->result($r,0,"suppl_order_status_id");
            $suppl_order_doc=$db->result($r,0,"suppl_order_doc");
            if ($amount_order==0 && $suppl_order_status_id==103){$amount_order=$amount;}

            $form=str_replace("{so_id}",$so_id,$form);
            $form=str_replace("{suppl_name}",$suppl_name,$form);
            $form=str_replace("{suppl_storage_name}",$suppl_storage_name,$form);
            $form=str_replace("{article_nr_displ}",$article_nr_displ,$form);
            $form=str_replace("{brand_name}",$brand_name,$form);
            $form=str_replace("{article_name}",$cat->getArticleName($art_id),$form);
            $form=str_replace("{suppl_info}",$suppl_info,$form);
            $form=str_replace("{suppl_art_id}",$suppl_art_id,$form);
            $form=str_replace("{amount}",$amount,$form);
            $form=str_replace("{amount_order}",$amount_order,$form);
            $form=str_replace("{delivery_data}",$delivery_data,$form);
            $form=str_replace("{delivery_time}",$delivery_time,$form);
            $form=str_replace("{delivery_data_finish}",$delivery_data_finish,$form);
            $form=str_replace("{delivery_time_finish}",$delivery_time_finish,$form);
            $form=str_replace("{suppl_order_doc}",$suppl_order_doc,$form);
            $form=str_replace("{dp_name}",$dp_name,$form);
            $form=str_replace("{tpoint_name}",$tpoint_name,$form);
            $form=str_replace("{dp_client}",$client_name,$form);
            $form=str_replace("{delivery_type_list}",$gmanual->showGmanualSelectList('delivery_type',$delivery_type_id),$form);
            $form=str_replace("{suppl_order_status_list}",$gmanual->showGmanualSelectList('suppl_order_status_id',$suppl_order_status_id),$form);
            $form=str_replace("{cash_list}",$this->showCashListSelect($cash_id),$form);
        }
        if ($n==0){$form_htm=RD."/tpl/access_deny.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);} }
        return $form;
    }

    function showDpSupplInfo($suppl_id, $suppl_storage_id) { $db=DbSingleton::getDb();
//        $cat=new catalogue;
        $form="";$form_htm=RD."/tpl/dp_suppl_info.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
//        $r=$db->query("SELECT j.*, CASH.name as cash_name, CONCAT(jd.prefix ,'-', jd.doc_nom,' від ', jd.time_stamp) as dp_name, tp.name as tpoint_name,
//        c.name as suppl_name, cs.name as suppl_storage_name, mu.name as user_name, si.info as suppl_info, cl.name as client_name
//        FROM `J_DP_SUPPL_ORDER` j
//            LEFT OUTER JOIN `J_DP` jd on jd.id=j.dp_id
//            LEFT OUTER JOIN `CASH` on CASH.id=j.cash_id
//            LEFT OUTER JOIN `T_POINT` tp on tp.id=j.tpoint_id
//            LEFT OUTER JOIN `A_CLIENTS` cl on cl.id=jd.client_conto_id
//            LEFT OUTER JOIN `A_CLIENTS` c on c.id=j.suppl_id
//            LEFT OUTER JOIN `A_CLIENTS_STORAGE` cs on cs.id=j.suppl_storage_id
//            LEFT OUTER JOIN `A_CLIENTS_SUPPL_INFO` si on si.client_id=c.id
//            LEFT OUTER JOIN `media_users` mu on mu.id=j.media_user_id
//        WHERE j.status=1 and j.id='$so_id' LIMIT 1;");$n=$db->num_rows($r);
        $r=$db->query("SELECT c.name as suppl_name, si.info as suppl_info, cs.name as suppl_storage_name 
        FROM `A_CLIENTS` c 
            LEFT JOIN `A_CLIENTS_SUPPL_INFO` si ON si.client_id=c.id
            LEFT JOIN `A_CLIENTS_STORAGE` cs ON cs.id=$suppl_storage_id
        WHERE c.id='$suppl_id';"); $n=$db->num_rows($r);

        if ($n==1) {
//            $dp_name=$db->result($r,0,"dp_name");
//            $art_id=$db->result($r,0,"art_id");
//            $article_nr_displ=$db->result($r,0,"article_nr_displ");
//            $suppl_art_id=$db->result($r,0,"SUPPL_ART_ID");
//            $brand_name=$db->result($r,0,"brand_name");
//            $cash_id=$db->result($r,0,"cash_id");
//            $client_name=$db->result($r,0,"client_name");
            $suppl_name=$db->result($r,0,"suppl_name");
            $suppl_info=$db->result($r,0,"suppl_info");
            $suppl_storage_name=$db->result($r,0,"suppl_storage_name");
//            $tpoint_name=$db->result($r,0,"tpoint_name");

            $form=str_replace("{suppl_name}",$suppl_name,$form);
            $form=str_replace("{suppl_storage_name}",$suppl_storage_name,$form);
//            $form=str_replace("{article_nr_displ}",$article_nr_displ,$form);
//            $form=str_replace("{brand_name}",$brand_name,$form);
//            $form=str_replace("{article_name}",$cat->getArticleName($art_id),$form);
            $form=str_replace("{suppl_info}",$suppl_info,$form);
//            $form=str_replace("{suppl_art_id}",$suppl_art_id,$form);
//            $form=str_replace("{dp_name}",$dp_name,$form);
//            $form=str_replace("{tpoint_name}",$tpoint_name,$form);
//            $form=str_replace("{dp_client}",$client_name,$form);
        }
        if ($n==0){$form_htm=RD."/tpl/access_deny.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);} }
        return $form;
    }

    function saveSupplOrder($so_id,$amount_order,$delivery_data_finish,$delivery_time_finish,$delivery_type_id,$suppl_order_status_id,$suppl_order_doc){$db=DbSingleton::getDb();
        $slave=new slave;$answer=0;$err="Помилка збереження даних!";$so_id=$slave->qq($so_id);
        if ($so_id>0){
            $amount_order=$slave->qq($amount_order);$delivery_data_finish=$slave->qq($delivery_data_finish);$delivery_time_finish=$slave->qq($delivery_time_finish);
            $delivery_type_id=$slave->qq($delivery_type_id);$suppl_order_status_id=$slave->qq($suppl_order_status_id);$suppl_order_doc=$slave->qq($suppl_order_doc);
            $db->query("UPDATE `J_DP_SUPPL_ORDER` SET `amount_order`='$amount_order', `delivery_data_finish`='$delivery_data_finish', `delivery_time_finish`='$delivery_time_finish', 
            `delivery_type_id`='$delivery_type_id', `suppl_order_status_id`='$suppl_order_status_id', `suppl_order_doc`='$suppl_order_doc'  WHERE `id`='$so_id';");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function showSupplList() { $db=DbSingleton::getDb();
        $r=$db->query("SELECT cl.* FROM `A_CLIENTS` cl
			LEFT OUTER JOIN `A_CLIENTS_CATEGORY` cc on cc.client_id=cl.id
			LEFT OUTER JOIN `A_CLIENTS_STORAGE` cs on cs.client_id=cl.id
		WHERE cc.category_id=2 AND cs.id>0 GROUP BY cs.client_id;"); $n=$db->num_rows($r); $list="";
        for ($i=1;$i<=$n;$i++){
            $suppl_id=$db->result($r,$i-1,"id");
            $suppl_name=$db->result($r,$i-1,"name");
            $list.="<option value='$suppl_id'>$suppl_name</option>";
        }
        return $list;
    }

    function checkBrandPrefix($suppl_id,$brand) { $db=DbSingleton::getDb();
        $r=$db->query("SELECT * FROM `SUPPL_BRANDS_PREFIX` WHERE `suppl_id`='$suppl_id' AND `suppl_brand`=\"$brand\" LIMIT 1;"); $n=$db->num_rows($r);
        if ($n>0) {
            $brand_id=$db->result($r,0,"brand_id");
        } else $brand_id=0;
        return $brand_id;
    }

    function showNumbersList($suppl_id) { $db=DbSingleton::getTokoDb(); $brands=[];
        $r=$db->query("SELECT * FROM `T2_SUPPL_IMPORT` 
        WHERE `status`=1 AND `art_id`=0 AND `suppl_id`=$suppl_id GROUP BY `suppl_index`, `brand`;"); $n=$db->num_rows($r); $list="";
        for ($i=1;$i<=$n;$i++){
            $suppl_index=$db->result($r,$i-1,"suppl_index");
            $brand=$db->result($r,$i-1,"brand");
            $price_suppl=$db->result($r,$i-1,"price_suppl");
            $cash_id=$db->result($r,$i-1,"cash_id"); $cash=$this->getCashName($cash_id);
            $kours_usd=$db->result($r,$i-1,"kours_usd");
            $price_usd=$db->result($r,$i-1,"price_usd");
            $client_storage_id=$db->result($r,$i-1,"client_storage_id");
            $stock_suppl=$db->result($r,$i-1,"stock_suppl");
            $data_update=$db->result($r,$i-1,"data_update");
            $return_delay=$db->result($r,$i-1,"return_delay");
            $warranty_info=$db->result($r,$i-1,"warranty_info");
            $list.="<tr>
				<td>$i</td>
				<td>0</td>
				<td>$suppl_index</td>
				<td>$brand</td>
				<th>$price_suppl</th>
				<th>$cash</th>
				<th>$kours_usd</th>
				<th>$price_usd</th>
				<th>$client_storage_id</th>
				<th>$stock_suppl</th>
				<th>$data_update</th>
				<th>$return_delay</th>
				<th>$warranty_info</th>
			</tr>";
            array_push($brands,$brand);
        }

        $brands=array_unique($brands); sort($brands);
        $select="<option value='0'>-Не вибрано</option>";
        foreach ($brands as $brand) {
            $brand_id=$this->checkBrandPrefix($suppl_id,$brand);
            if ($brand_id>0) $postfix="***"; else $postfix="";
            $select.="<option value='$brand'>$brand $postfix</option>";
        }
        return array($list,$select);
    }

    function showNumbersBrandList($suppl_id) { $db=DbSingleton::getTokoDb(); $brands=[];
        $r=$db->query("SELECT * FROM `T2_SUPPL_IMPORT` 
        WHERE `status`=1 AND `art_id`=0 AND `suppl_id`=$suppl_id GROUP BY `brand`;"); $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $brand=$db->result($r,$i-1,"brand");
            array_push($brands,$brand);
        }

        $brands=array_unique($brands); sort($brands);
        $select="<option value='0'>-Не вибрано</option>";
        foreach ($brands as $brand) {
            $brand_id=$this->checkBrandPrefix($suppl_id,$brand);
            if ($brand_id>0) $postfix="***"; else $postfix="";
            $select.="<option value='$brand'>$brand $postfix</option>";
        }
        return $select;
    }

    function showArticlesNumbersList($suppl_id,$suppl_brand,$brand_id,$prefix,$limit) { $db=DbSingleton::getTokoDb();

        $db->query("DELETE FROM `T2_ARTICLES_SUPPL_COPY`;");
        $db->query("INSERT INTO `T2_ARTICLES_SUPPL_COPY` (`ART_ID`,`ARTICLE_NR_DISPL`,`ARTICLE_NR_SEARCH`,`BRAND_ID`) 
        SELECT `ART_ID`,`ARTICLE_NR_DISPL`,`ARTICLE_NR_SEARCH`,`BRAND_ID` FROM `T2_ARTICLES` WHERE `BRAND_ID`=$brand_id;");

        $where_limit=""; if ($limit!="") $where_limit="LIMIT $limit";
        $r=$db->query("SELECT t2si.* FROM `T2_SUPPL_IMPORT` t2si
        WHERE t2si.`status`=1 AND t2si.`art_id`=0 AND t2si.`suppl_id`=$suppl_id AND t2si.`brand`='$suppl_brand' 
        GROUP BY t2si.`suppl_index`, t2si.`brand` $where_limit;"); $n=$db->num_rows($r); $list="";

        for ($i=1;$i<=$n;$i++){
            $suppl_index=$db->result($r,$i-1,"suppl_index");
            $brand=$db->result($r,$i-1,"brand");
            $price_suppl=$db->result($r,$i-1,"price_suppl");
            $cash_id=$db->result($r,$i-1,"cash_id"); $cash=$this->getCashName($cash_id);
            $kours_usd=$db->result($r,$i-1,"kours_usd");
            $price_usd=$db->result($r,$i-1,"price_usd");
            $client_storage_id=$db->result($r,$i-1,"client_storage_id");
            $stock_suppl=$db->result($r,$i-1,"stock_suppl");
            $data_update=$db->result($r,$i-1,"data_update");
            $return_delay=$db->result($r,$i-1,"return_delay");
            $warranty_info=$db->result($r,$i-1,"warranty_info");

            $suppl_index_search=str_replace("$prefix","",$suppl_index);
            $suppl_index_search=$this->clearArticle($suppl_index_search);
            $suppl_index_search=strtoupper($suppl_index_search);

            $r2=$db->query("SELECT * FROM `T2_ARTICLES_SUPPL_COPY` WHERE `ARTICLE_NR_SEARCH`='$suppl_index_search';");$n2=$db->num_rows($r2);
            $art_id=0;$art_index="";
            if ($n2>0) {
                $art_id=$db->result($r2,0,"ART_ID");
                $art_displ=$db->result($r2,0,"ARTICLE_NR_DISPL");
                $art_search=$db->result($r2,0,"ARTICLE_NR_SEARCH");
                $art_index="<br>$art_displ<br>($art_search)";
            }

            $art_id_count=$n2; $sort=0; $status="";
            if ($art_id!=0) {$status="style='background: lightgreen'";$sort=1;}
            if ($art_id_count>1) {$status="style='background: #f0ad4e; cursor:pointer;' onclick=\"showArticlesUnknown('$suppl_id','$suppl_index','$suppl_brand','$prefix');\""; $art_id=0; $art_index="Больше 2"; $sort=2;}

            $list.="<tr $status>
				<td>$i</td>
				<td>$sort. $art_id $art_index</td>
				<td>$suppl_index</td>
				<td>$brand</td>
				<th>$price_suppl</th>
				<th>$cash</th>
				<th>$kours_usd</th>
				<th>$price_usd</th>
				<th>$client_storage_id</th>
				<th>$stock_suppl</th>
				<th>$data_update</th>
				<th>$return_delay</th>
				<th>$warranty_info</th>
			</tr>";
        }
        return $list;
    }

    function showArticlesUnknown($suppl_id,$suppl_index,$suppl_brand,$prefix) {$db=DbSingleton::getTokoDb();
        $header="<h2>Відповідні артикули з каталогу для індекса <span class='badge badge-danger text-left' style='font-size: 1em;'>$suppl_index $suppl_brand</span>:</h2>";
        $list="<ul class='list-group'>";
        $suppl_index_search=str_replace("$prefix","",$suppl_index);
        $suppl_index_search=$this->clearArticle($suppl_index_search);
        $suppl_index_search=strtoupper($suppl_index_search);
        $r=$db->query("SELECT * FROM `T2_ARTICLES_SUPPL_COPY` WHERE `ARTICLE_NR_SEARCH`='$suppl_index_search';");$n=$db->num_rows($r);
        if ($n>0) {
            for ($i=1;$i<=$n;$i++) {
                $art_id=$db->result($r,$i-1,"ART_ID");
                $art_displ=$db->result($r,$i-1,"ARTICLE_NR_DISPL");
                $list.="<li class='list-group-item' style='font-size: 2em;'>
                    <button class='btn btn-primary' onclick=\"saveArticlesUnknown('$suppl_id', '$suppl_index', '$suppl_brand', '$art_id');\"><i class='fa fa-check'></i> Вибрати</button>
                    <span class='badge text-left'>ART_ID: $art_id</span>
                    <span class='badge text-left'>DISPLAY: $art_displ</span>
                </li>";
            }
        }
        $list.="</ul>";
        return array($list,$header);
    }

    function saveArticlesUnknown($suppl_id,$suppl_index,$suppl_brand,$art_id,$return_delay,$warranty_info) {$db=DbSingleton::getTokoDb();
        $date_cur=date("Y-m-d H:i:s");
        session_start();$user_id=$_SESSION["media_user_id"];
        $db->query("UPDATE `T2_SUPPL_IMPORT` SET `art_id`='$art_id',`return_delay`='$return_delay',`warranty_info`='$warranty_info' 
        WHERE `status`=1 AND `art_id`=0 AND `suppl_id`=$suppl_id AND `brand`='$suppl_brand' AND `suppl_index`='$suppl_index';");
        $r2=$db->query("SELECT * FROM `T2_SUPPL_ARTICLES_IMPORT` WHERE `suppl_id`=$suppl_id AND `suppl_index`='$suppl_index' AND `suppl_brand`=\"$suppl_brand\" AND `art_id`='$art_id';"); $n2=$db->num_rows($r2);
        if ($n2==0)
            $db->query("INSERT INTO `T2_SUPPL_ARTICLES_IMPORT` (`suppl_id`,`suppl_index`,`suppl_brand`,`art_id`,`return_delay`,`warranty_info`,`user_create`,`date_create`)
            VALUES ($suppl_id,'$suppl_index','$suppl_brand',$art_id,$return_delay,'$warranty_info','$user_id','$date_cur');");
        $answer=1;$err="";
        return array($answer,$err);
    }

    function saveArticlesNumbersList($suppl_id,$suppl_brand,$return_delay,$warranty_info,$prefix,$limit) {$db=DbSingleton::getTokoDb();
        $date_cur=date("Y-m-d H:i:s");
        session_start();$user_id=$_SESSION["media_user_id"];

        $where_limit=""; if ($limit!="") $where_limit="LIMIT $limit";

        $r=$db->query("SELECT t2si.* FROM `T2_SUPPL_IMPORT` t2si
        WHERE t2si.`status`=1 AND t2si.`art_id`=0 AND t2si.`suppl_id`=$suppl_id AND t2si.`brand`='$suppl_brand' $where_limit;"); $n=$db->num_rows($r);

        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"ID");
            $suppl_index=$db->result($r,$i-1,"suppl_index");
            $suppl_brand=$db->result($r,$i-1,"brand");

            $suppl_index_search=str_replace("$prefix","",$suppl_index);
            $suppl_index_search=$this->clearArticle($suppl_index_search);
            $suppl_index_search=strtoupper($suppl_index_search);

            $r2=$db->query("SELECT * FROM `T2_ARTICLES_SUPPL_COPY` WHERE `ARTICLE_NR_SEARCH`='$suppl_index_search';");$n2=$db->num_rows($r2);
            $art_id=0;
            if ($n2>0) {
                $art_id=$db->result($r2,0,"ART_ID");
            }
            $art_id_count=$n2;

            if ($art_id>0 && $art_id_count==1) {
                $db->query("UPDATE `T2_SUPPL_IMPORT` SET `art_id`='$art_id',`return_delay`='$return_delay',`warranty_info`='$warranty_info' WHERE `ID`=$id AND `art_id`=0 AND `status`=1;");
                $r2=$db->query("SELECT * FROM `T2_SUPPL_ARTICLES_IMPORT` WHERE `suppl_id`=$suppl_id AND `suppl_index`='$suppl_index' AND `suppl_brand`=\"$suppl_brand\" AND `art_id`='$art_id';"); $n2=$db->num_rows($r2);
                if ($n2==0)
                    $db->query("INSERT INTO `T2_SUPPL_ARTICLES_IMPORT` (`suppl_id`,`suppl_index`,`suppl_brand`,`art_id`,`return_delay`,`warranty_info`,`user_create`,`date_create`)
                    VALUES ($suppl_id,'$suppl_index','$suppl_brand',$art_id,$return_delay,'$warranty_info','$user_id','$date_cur');");
            }
        }

        $db->query("DELETE FROM `T2_ARTICLES_SUPPL_COPY`;");
        $answer=1;$err="";
        return array($answer,$err);
    }

    function showUnknownBrandIds($suppl_id, $brand) {$db=DbSingleton::getTokoDb();
        $list="<option value='0'>-Не вибрано-</option>";
        $sel_brand_id=$this->checkBrandPrefix($suppl_id,$brand);
        $where_sel="";
        if ($sel_brand_id>0) {
            $where_sel=" AND `BRAND_ID`!='$sel_brand_id'";
            $sel_brand_name=$this->getBrandName($sel_brand_id);
            $list.="<option value='$sel_brand_id' selected>$sel_brand_name</option>";
        }
        $r=$db->query("SELECT * FROM `T2_BRANDS` WHERE `BRAND_NAME` LIKE '%$brand%' $where_sel ORDER BY `BRAND_NAME` ASC;"); $n=$db->num_rows($r);
        for($i=1;$i<=$n;$i++) {
            $brand_id=$db->result($r,$i-1,"BRAND_ID");
            $brand_name=$db->result($r,$i-1,"BRAND_NAME");
            $list.="<option value='$brand_id'>$brand_name</option>";
        }
        return $list;
    }

    function showAllBrandIds() {$db=DbSingleton::getTokoDb();
        $list="<option value='0'>-Не вибрано-</option>";
        $r=$db->query("SELECT * FROM `T2_BRANDS` ORDER BY `BRAND_NAME` ASC;"); $n=$db->num_rows($r);
        for($i=1;$i<=$n;$i++) {
            $brand_id=$db->result($r,$i-1,"BRAND_ID");
            $brand_name=$db->result($r,$i-1,"BRAND_NAME");
            $list.="<option value='$brand_id'>$brand_name</option>";
        }
        return $list;
    }

    function saveSupplPrefix($suppl_id, $suppl_brand, $brand_id, $prefix, $return_delay, $warranty_info) {$db=DbSingleton::getDb();
        $answer=0;$err="Помилка збереження даних!";
        if ($suppl_id>0){
            $r=$db->query("SELECT * FROM `SUPPL_BRANDS_PREFIX` WHERE `suppl_id`='$suppl_id' AND `suppl_brand`=\"$suppl_brand\" AND `brand_id`='$brand_id' LIMIT 1;"); $n=$db->num_rows($r);
            if ($n>0)
                $db->query("UPDATE `SUPPL_BRANDS_PREFIX` SET `prefix`='$prefix', `return_delay`='$return_delay', `warranty_info`='$warranty_info' 
                WHERE `suppl_id`='$suppl_id' AND `suppl_brand`=\"$suppl_brand\" AND `brand_id`='$brand_id' LIMIT 1;");
            else
                $db->query("INSERT INTO `SUPPL_BRANDS_PREFIX` (`suppl_id`, `suppl_brand`, `brand_id`, `prefix`, `return_delay`, `warranty_info`) 
                VALUES ('$suppl_id', '$suppl_brand', '$brand_id', '$prefix', '$return_delay', '$warranty_info');");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function showUnknownBrandPrefix($suppl_id,$suppl_brand,$brand_id) {$db=DbSingleton::getDb();
        $r=$db->query("SELECT * FROM `SUPPL_BRANDS_PREFIX` WHERE `suppl_id`='$suppl_id' AND `suppl_brand`=\"$suppl_brand\" AND `brand_id`='$brand_id' LIMIT 1;"); $n=$db->num_rows($r);
        if ($n>0) {
            $prefix=$db->result($r,0,"prefix");
            $return_delay=$db->result($r,0,"return_delay");
            $warranty_info=$db->result($r,0,"warranty_info");
        } else {
            $prefix="";
            $return_delay=14;
            $warranty_info="";
        }
        return array($prefix,$return_delay,$warranty_info);
    }

    function showBackSupplList($date_start="", $date_end="", $suppl_id="") { $db=DbSingleton::getDb();
        $list="";
        $data_start=date("Y-m-d"); $data_end = date('Y-m-d', strtotime('-7 day', strtotime($data_start)));
        $where_date=""; if ($date_start!="" && $date_end!="") $where_date=" AND data>='$data_start' AND data<='$data_end'";
        $where_suppl=""; if ($suppl_id>0) $where_suppl=" AND `client_id`='$suppl_id'";
        $r=$db->query("SELECT * FROM `J_INCOME` WHERE 1 $where_date $where_suppl"); $n=$db->num_rows($r);
        for($i=1;$i<=$n;$i++) {
            $doc_name=$db->result($r,$i-1,"prefix")."-".$db->result($r,$i-1,"doc_nom");
            //$client_id=$db->result($r,$i-1,"client_id");
            $data=$db->result($r,$i-1,"data");
            $cash_id=$db->result($r,$i-1,"cash_id"); $cash_name=$this->getCashName($cash_id);
            $summ_end=$db->result($r,$i-1,"summ_end");
            $list.="<tr>
                <td>$i</td>
                <td>$data</td>
                <td>$cash_name</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>$doc_name</td>
                <td>$summ_end</td>
            </tr>";
        }
        return $list;
    }

    function showBackSupplSelect() { $db=DbSingleton::getDb();
        //A_CLIENTS_CATEGORY category_id=2 - постачальник
        $list="";
        $r=$db->query("SELECT ac.* FROM `A_CLIENTS_CATEGORY` cc
          LEFT JOIN `A_CLIENTS` ac ON ac.id=cc.client_id
        WHERE cc.category_id='2';"); $n=$db->num_rows($r);
        for($i=1;$i<=$n;$i++) {
            $id = $db->result($r, $i - 1, "id");
            $name = $db->result($r, $i - 1, "name");
            $list.="<option value='$id'>$name</option>";
        }
        return $list;
    }

}

