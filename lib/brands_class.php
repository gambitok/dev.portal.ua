<?php

class brands {

    function show_brands_list() { $db=DbSingleton::getTokoDb();
        $manual=new manual; $list="";
        $r=$db->query("SELECT b.*, t2cn.COUNTRY_NAME, t2k.CAPTION 
        FROM `T2_BRANDS` b
            LEFT OUTER JOIN `T2_COUNTRIES` t2cn on t2cn.COUNTRY_ID=b.COUNTRY_ID
            LEFT OUTER JOIN `T2_BRANDS_KIND` t2k on t2k.KIND_ID=b.KIND;"); $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++) {
            $id=$db->result($r,$i-1,"BRAND_ID");
            $name=$db->result($r,$i-1,"BRAND_NAME");
            $kind=$db->result($r,$i-1,"CAPTION");
            $country=$db->result($r,$i-1,"COUNTRY_NAME");
            if ($country=="") $country=$manual->getManualMCaption("COUNTRY_NAME",$db->result($r,$i-1,"COUNTRY_NAME"));
            $visible=$manual->getManualMCaption("VISIBLE",$db->result($r,$i-1,"VISIBLE"));
            $list.="<tr style='cursor:pointer' onClick='showBrandsCard(\"$id\")'>
                <td>$id</td>
                <td>$name</td>
                <td>$kind</td>
                <td>$country</td>
                <td>$visible</td>
            </tr>";
        }
        return $list;
    }

    function newBrandsCard() { $dbt=DbSingleton::getTokoDb();
        $r=$dbt->query("SELECT MAX(`BRAND_ID`) as mid FROM `T2_BRANDS`;");
        $brands_id=0+$dbt->result($r,0,"mid")+1;
        $dbt->query("INSERT INTO `T2_BRANDS` (`BRAND_ID`) VALUES ('$brands_id');");
        return $brands_id;
    }

    function showBrandsCard($brands_id) { $dbt=DbSingleton::getTokoDb();
        session_start();$user_id=$_SESSION["media_user_id"];$user_name=$_SESSION["user_name"];
        $form="";$form_htm=RD."/tpl/brands_card.htm"; if (file_exists("$form_htm")){$form = file_get_contents($form_htm);}
        $r=$dbt->query("SELECT * FROM `T2_BRANDS` WHERE `BRAND_ID`='$brands_id' LIMIT 1;"); $n=$dbt->num_rows($r);
        if ($n==0){$form_htm=RD."/tpl/access_deny.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}}
        if ($n==1){
            $brands_id=$dbt->result($r,0,"BRAND_ID");
            $brands_name=$dbt->result($r,0,"BRAND_NAME");
            $brands_type=$dbt->result($r,0,"BRAND_TYPE");
            $brands_kind=$dbt->result($r,0,"KIND");
            $brands_country=$dbt->result($r,0,"COUNTRY_ID");
            $brands_visible=$dbt->result($r,0,"VISIBLE");
            $brands_checked="";if ($brands_visible==1){$brands_checked=" checked";}
            $form=str_replace("{brands_id}",$brands_id,$form);
            $form=str_replace("{brands_name}",$brands_name,$form);
            $form=str_replace("{brands_type}",$brands_type,$form);
            $form=str_replace("{kind_list}",$this->showSelectListBrands("T2_BRANDS_KIND","KIND_ID","CAPTION",$brands_kind),$form);
            $form=str_replace("{country_list}",$this->showSelectListBrands("T2_COUNTRIES","COUNTRY_ID","COUNTRY_NAME",$brands_country),$form);
            $form=str_replace("{brands_checked}",$brands_checked,$form);
            $form=str_replace("{my_user_id}",$user_id,$form);
            $form=str_replace("{my_user_name}",$user_name,$form);
        }
        return $form;
    }

    function saveBrandsGeneralInfo($brands_id, $brands_name, $brands_type, $brands_kind, $brands_country, $brands_visible) { $dbt=DbSingleton::getTokoDb();
        $slave=new slave; $answer=0; $err="Помилка збереження даних!";
        $brands_id=$slave->qq($brands_id);$brands_name=$slave->qq($brands_name);$brands_type=$slave->qq($brands_type);$brands_kind=$slave->qq($brands_kind);$brands_country=$slave->qq($brands_country);$brands_visible=$slave->qq($brands_visible);
        if ($brands_id>0){
            $dbt->query("UPDATE `T2_BRANDS` SET `BRAND_NAME`='$brands_name', `BRAND_TYPE`='$brands_type', `KIND`='$brands_kind', `COUNTRY_ID`='$brands_country', `VISIBLE`='$brands_visible' WHERE `BRAND_ID`='$brands_id';");
            $answer=1; $err="";
        }
        return array($answer, $err);
    }

    function saveBrandsDetails($brands_id, $descr, $link) { $dbt=DbSingleton::getTokoDb();
        $slave=new slave; $answer=0; $err="Помилка збереження даних!";
        $brands_id=$slave->qq($brands_id);$descr=$slave->qq($descr);$link=$slave->qq($link);
        if ($brands_id>0){
            $dbt->query("UPDATE `T2_BRAND_LINK` SET `descr`='$descr',`link`='$link' WHERE `brand_id`='$brands_id';");
            $answer=1; $err="";
        }
        return array($answer, $err);
    }

    function loadBrandsDetails($brands_id) { $db=DbSingleton::getTokoDb();
        $form="";$form_htm=RD."/tpl/brands_details.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("SELECT * FROM `T2_BRAND_LINK` WHERE `brand_id`='$brands_id';");
        $descr=$db->result($r,0,"descr");
        $link=$db->result($r,0,"link");
        $form=str_replace("{descr}",$descr,$form);
        $form=str_replace("{link}",$link,$form);
        return $form;
    }

    function loadBrandsPhoto($brands_id) { $db=DbSingleton::getTokoDb();
        $form="";$form_htm=RD."/tpl/brands_photo_block.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("SELECT * FROM `T2_BRAND_LINK` WHERE `brand_id`='$brands_id' ORDER BY `name` ASC;");$n=$db->num_rows($r); $list="";
        for ($i=1;$i<=$n;$i++){
            $logo_name=$db->result($r,$i-1,"logo_name");
            $file_name=trim(preg_replace('/\s\s+/', ' ', $logo_name));
            $link="http://portal.myparts.pro/cdn/brands_files/$file_name";
            $block=$form;
            $block=str_replace("{logo_name}",$logo_name,$block);
            $block=str_replace("{link}",$link,$block);
            $list.=$block;
        }
        if ($n==0){$list="<h3 class='text-center'>Логотип відсутній</h3>";}
        return $list;
    }

    function deleteBrandsLogo($brands_id) { $db=DbSingleton::getTokoDb();
        $answer=0; $err="Помилка видалення даних!";
        if ($brands_id>0){
            $db->query("UPDATE `T2_BRAND_LINK` SET `logo_name`='' WHERE `brand_id`='$brands_id';");
            $answer=1; $err="";
        }
        return array($answer, $err);
    }

    function showSelectListBrands($table, $field_id, $field, $sel_id) { $db=DbSingleton::getTokoDb();
        $list="<option value='0'></option>";
        $r=$db->query("SELECT `$field_id`, `$field` FROM `$table` ORDER BY `$field` ASC;");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"$field_id");
            $caption=$db->result($r,$i-1,"$field");
            $sel="";if ($id==$sel_id){$sel=" selected='selected'";}
            $list.="<option value='$id' $sel>$caption</option>";
        }
        return $list;
    }

}

function ExportBrands() { $db=DbSingleton::getTokoDb();
    $list=array();
	$r=$db->query("SELECT * FROM `T2_BRANDS`;"); $n=$db->num_rows($r);
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"BRAND_ID");
		$name=$db->result($r,$i-1,"BRAND_NAME");
		$kind=$db->result($r,$i-1,"KIND");
		$country=$db->result($r,$i-1,"COUNTRY_ID");
		$visible=$db->result($r,$i-1,"VISIBLE");
		$list[$i]=array($id, $name, $kind, $country, $visible);
	}
	return $list;
}

function ImportBrands() {
    $form="";$form_htm=RD."/tpl/brands_import.htm"; if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	list(,,$pre_table)=showCsvPreviewIndex();
	$form=str_replace("{records_list}","<tr><td colspan=10 align='center'>Записи не завантажено</td></tr>",$form);
	$form=str_replace("{import_file_name}","Оберіть файл",$form);
	$form=str_replace("{csv_str_file}",$pre_table,$form);
	return $form;
}

function showCsvPreviewIndex() { $db=DbSingleton::getTokoDb();
    $csv_exist=$fn=$kol_cols=0; $csv_file_name="Оберіть файл"; $pre_table="<h3 align='center'>Записи відсутні</h3>";
	$r=$db->query("SELECT * FROM `T2_BRANDS_CSV` LIMIT 1;"); $n=$db->num_rows($r);
	if ($n==1){
		$file_name=$db->result($r,0,"FILE_NAME");
		$file_path=RD."/cdn/brands_files/index/$file_name";
		if (file_exists($file_path)){
            $form="";$form_htm=RD."/tpl/brands_index_str_file.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
			$records_list="";
			$import_file_name=$file_name;
			$fna=explode(".",$file_name);$ft=count($fna);$file_type=$fna[$ft-1];
			if ($file_type=="csv"){
				$handle = @fopen($file_path, "r");
				if ($handle) { 
					set_time_limit(0);
					while (($buffer = fgets($handle, 4096)) !== false) {$fn+=1;
						$buf=explode(";",$buffer);
						if ($buffer!=""){
							if ($fn==1){$kol_cols=count($buf);}
							$buf=str_replace("'","\'",$buf);$buf=str_replace('"','\"',$buf);$row="";
							for ($i=1;$i<=$kol_cols;$i++){
								if ($i==1){$row="<td>$fn</td>";}
								$row.="<td>".trim($buf[$i-1])."</td>";
							}
							if ($row!=""){
								$records_list.="<tr>$row</tr>";
							}
						}
						if ($fn==10){break;}
					}
					fclose($handle);
				}
			}
			$form=str_replace("{records_list}",$records_list,$form);
			$form=str_replace("{import_file_name}",$import_file_name,$form);
			$form=str_replace("{kol_cols}",$kol_cols,$form);
			$csv_file_name=$file_name;$csv_exist=1;$pre_table=$form;
		}
	}
	return array($csv_exist,$csv_file_name,$pre_table);
}
	
function finishBrandsIndexImport($start_row) { $db=DbSingleton::getTokoDb();
    $slave=new slave;$answer=0;$err="Помилка збереження даних!";$err2 = "Файл з дуплікатами індексів!";
	$start_row=$slave->qq($start_row);
    $r=$db->query("SELECT * FROM `T2_BRANDS_CSV` LIMIT 1;"); $n=$db->num_rows($r);
    if ($n==1){
        $file_name=$db->result($r,0,"FILE_NAME");
        $file_path=RD."/cdn/brands_files/index/$file_name";
        if (file_exists($file_path)){
            $fna=explode(".",$file_name);$ft=count($fna);$file_type=$fna[$ft-1];$krs=0;
            $handle2 = @fopen($file_path, "r");
                if ($handle2) {
                    $listUn = array();
                    //search duplicate index
                    while (($buffer2 = fgets($handle2, 4096)) !== false) {
                        $buf2=explode(";",$buffer2);
                        $buf2=str_replace("'","\'",$buf2);$buf2=str_replace('"','\"',$buf2);
                        $brands_id2=trim($buf2[0]);
                        array_push($listUn, $brands_id2);
                    }
                    if (isUnique($listUn)) {
                        return array($answer,$err2);
                    }
                    fclose($handle2);
                }
            if ($file_type=="csv"){
                $handle = @fopen($file_path, "r");
                if ($handle) { set_time_limit(0);
                    $pkg_k=0;$max_pkg=500;$pkg="";
                    while (($buffer = fgets($handle, 4096)) !== false) {$krs+=1;
                        $buf=explode(";",$buffer);
                        if ($buffer!=""){
                            if ($krs>=$start_row){
                                $buf=str_replace("'","\'",$buf);$buf=str_replace('"','\"',$buf);
                                $brands_id=trim($buf[0]);
                                $brands_name=trim($buf[1]);
                                $brands_type=trim($buf[2]);
                                $brands_country=trim($buf[3]);
                                $brands_visible=trim($buf[4]);
                                if ($pkg!=""){$pkg.=",";}
                                $pkg.="('$brands_id','$brands_name','$brands_type','$brands_country','$brands_visible')";
                                $pkg_k+=1;
                                if ($pkg_k==$max_pkg){
                                    $db->query("INSERT INTO `T2_BRANDS` (`BRAND_ID`,`BRAND_NAME`,`KIND`,`COUNTRY_ID`,`VISIBLE`) VALUES $pkg;");
                                    $pkg="";$pkg_k=0;
                                }
                            }
                        }
                    }
                    if ($pkg!=""){
                        $db->query("INSERT INTO `T2_BRANDS` (`BRAND_ID`,`BRAND_NAME`,`KIND`,`COUNTRY_ID`,`VISIBLE`) VALUES $pkg;");
                    }
                    fclose($handle);
                }
                if (file_exists(RD."/cdn/brands_files/index/$file_name")){unlink(RD."/cdn/brands_files/index/$file_name");}
                $answer=1; $err="";
            }
        }
    }
	return array($answer, $err);
}

function isUnique($array) {
	return(array_unique($array)!=$array); 
}
	
