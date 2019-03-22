<?php
class suppl{
function show_suppl_list(){$db=DbSingleton::getDb();$slave=new slave;$where="";
	$r=$db->query("select c.*, t2cn.COUNTRY_NAME, t2st.STATE_NAME from A_CLIENTS c 
		left outer join A_CLIENTS_CATEGORY cc on cc.client_id=c.id 
		left outer join T2_COUNTRIES t2cn on t2cn.COUNTRY_ID=c.country 
		left outer join T2_STATE t2st on t2st.STATE_ID=c.state
		where c.status='1' and cc.category_id='2';");$n=$db->num_rows($r);$list="";
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		$name=$db->result($r,$i-1,"name");
		$email=$db->result($r,$i-1,"email");
		$phone=$db->result($r,$i-1,"phone");
		$country=$db->result($r,$i-1,"COUNTRY_NAME");
		$state=$db->result($r,$i-1,"STATE_NAME");
		$region=$db->result($r,$i-1,"REGION_NAME");
		$city=$db->result($r,$i-1,"CITY_NAME");
		$address=$db->result($r,$i-1,"address");
		$list.="<tr style='cursor:pointer' onClick='showSupplCard(\"$id\")'>
				<td>$id</td>
				<td>$name</td>
				<td>$country</td>
				<td>$state</td>
				</tr>";
	}
	return $list;
}

function showSupplCard($suppl_id){$db=DbSingleton::getDb();$slave=new slave;session_start();$user_id=$_SESSION["media_user_id"];$user_name=$_SESSION["user_name"];
	include_once RD."/lib/clients_class.php"; $clients=new clients;
	$form_htm=RD."/tpl/suppl_card.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	
	$r=$db->query("select c.*, ot.full_name as ot_full_name, t2cn.COUNTRY_NAME, t2st.STATE_NAME, t2rg.REGION_NAME, t2ct.CITY_NAME from A_CLIENTS c  
					left outer join A_ORG_TYPE ot on ot.id=c.org_type
					left outer join T2_COUNTRIES t2cn on t2cn.COUNTRY_ID=c.country 
					left outer join T2_STATE t2st on t2st.STATE_ID=c.state
					left outer join T2_REGION t2rg on t2rg.REGION_ID=c.region
					left outer join T2_CITY t2ct on t2ct.CITY_ID=c.city
					where c.id='$suppl_id' and c.status='1' limit 0,1;");$n=$db->num_rows($r);
	if ($n==0){$form_htm=RD."/tpl/access_deny.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);} }
	if ($n==1){
		$org_type=$db->result($r,0,"org_type");
		$org_type_full_name=$db->result($r,0,"ot_full_name");
		$full_name=$db->result($r,0,"full_name");
		$name=$db->result($r,0,"name");
		$email=$db->result($r,0,"email");
		$phone=$db->result($r,0,"phone");
		$parrent_id=$db->result($r,0,"parrent_id");
		$country=$db->result($r,0,"COUNTRY_NAME");
		$state=$db->result($r,0,"STATE_NAME");
		$region=$db->result($r,0,"REGION_NAME");
		$city=$db->result($r,0,"CITY_NAME");
		$status=$db->result($r,0,"status");
		
		
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

function saveSupplGeneralInfo($suppl_id,$name,$full_name,$address,$chief,$country_id,$state_id,$region_id,$city_id){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;session_start();$user_id=$_SESSION["media_user_id"];$user_name=$_SESSION["user_name"];$answer=0;$err="Помилка збереження даних!";

	$suppl_id=$slave->qq($suppl_id);$name=$slave->qq($name);$full_name=$slave->qq($full_name);$address=$slave->qq($address);$chief=$slave->qq($chief);
	$country_id=$slave->qq($country_id);$state_id=$slave->qq($state_id);$city_id=$slave->qq($city_id);$region_id=$slave->qq($region_id);
	if ($suppl_id>0){
		$db->query("update T_POINT set `name`='$name',status='1', `full_name`='$full_name', `address`='$address', `chief`='$chief', `country`='$country_id', `state`='$state_id', `city`='$city_id', `region`='$region_id' where `id`='$suppl_id';");
		$dbt->query("update T_POINT set `name`='$name', status='1', `full_name`='$full_name', `address`='$address', `chief`='$chief', `country`='$country_id', `state`='$state_id', `city`='$city_id', `region`='$region_id' where `id`='$suppl_id';");
		$answer=1;$err="";
	}
	return array($answer,$err);
}

function loadSupplIndex($suppl_id){$db=DbSingleton::getTokoDb();$slave=new slave;$gmanual=new gmanual;
	$form_htm=RD."/tpl/suppl_index_table.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	
	list($csv_exist,$csv_file_name,$pre_table)=$this->showCsvPreviewIndex($suppl_id);
	
	$form=str_replace("{records_list}","<tr><td colspan=10 align='center'>Записи не завантажено</td></tr>",$form);
	$form=str_replace("{import_file_name}","Оберіть файл",$form);
	$form=str_replace("{csv_str_file}",$pre_table,$form);
	$form=str_replace("{suppl_id}",$suppl_id,$form);
	return $form;
}


function loadSupplVat($suppl_id){$db=DbSingleton::getDb();$slave=new slave;$gmanual=new gmanual;
	$form_htm=RD."/tpl/suppl_vat_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$r=$db->query("select * from A_CLIENTS_VAT_CONDITIONS where client_id='$suppl_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){
		$price_in_vat=$db->result($r,0,"price_in_vat");$price_in_vat_checked=""; if ($price_in_vat==1){$price_in_vat_checked="checked";}
		$show_in_vat=$db->result($r,0,"show_in_vat");$show_in_vat_checked=""; if ($show_in_vat==1){$show_in_vat_checked="checked";}
		$price_add_vat=$db->result($r,0,"price_add_vat");$price_add_vat_checked=""; if ($price_add_vat==1){$price_add_vat_checked="checked";}
	}
	$form=str_replace("{suppl_id}",$suppl_id,$form);
	$form=str_replace("{price_in_vat_checked}",$price_in_vat_checked,$form);
	$form=str_replace("{show_in_vat_checked}",$show_in_vat_checked,$form);
	$form=str_replace("{price_add_vat_checked}",$price_add_vat_checked,$form);
	
	return $form;
}


function saveSupplVat($suppl_id,$price_in_vat,$show_in_vat,$price_add_vat){$db=DbSingleton::getDb();$slave=new slave;session_start();$media_user_id=$_SESSION["media_user_id"];$media_user_name=$_SESSION["user_name"];$answer=0;$err="Помилка збереження даних!";
	$suppl_id=$slave->qq($suppl_id);$price_in_vat=$slave->qq($price_in_vat);$show_in_vat=$slave->qq($show_in_vat);$price_add_vat=$slave->qq($price_add_vat);
	if ($suppl_id>0){
		$r=$db->query("select count(client_id) as kol from A_CLIENTS_VAT_CONDITIONS where client_id='$suppl_id';");$ex=$db->result($r,0,"kol");
		if ($ex==0 ){$db->query("insert into A_CLIENTS_VAT_CONDITIONS (client_id) values ('$suppl_id');");}
		$db->query("update A_CLIENTS_VAT_CONDITIONS set price_in_vat='$price_in_vat', `show_in_vat`='$show_in_vat', `price_add_vat`='$price_add_vat' where client_id='$suppl_id';");
		$answer=1;$err="";
	}
	return array($answer,$err);
}
	
function loadSupplOrderInfo($suppl_id){$db=DbSingleton::getDb();$slave=new slave;$gmanual=new gmanual;
	$form_htm=RD."/tpl/suppl_info_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$r=$db->query("select info from A_CLIENTS_SUPPL_INFO where client_id='$suppl_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){
		$info=$db->result($r,0,"info");
	}
	$form=str_replace("{info}",$info,$form);
	$form=str_replace("{suppl_id}",$suppl_id,$form);
	
	return $form;
}


function saveSupplOrderInfo($suppl_id,$info){$db=DbSingleton::getDb();$slave=new slave;session_start();$media_user_id=$_SESSION["media_user_id"];$media_user_name=$_SESSION["user_name"];$answer=0;$err="Помилка збереження даних!";
	$suppl_id=$slave->qq($suppl_id);$info=$slave->qq($info);
	if ($suppl_id>0){
		$r=$db->query("select count(client_id) as kol from A_CLIENTS_SUPPL_INFO where client_id='$suppl_id';");$ex=$db->result($r,0,"kol");
		if ($ex==0 ){$db->query("insert into A_CLIENTS_SUPPL_INFO (client_id) values ('$suppl_id');");}
		$db->query("update A_CLIENTS_SUPPL_INFO set info='$info' where client_id='$suppl_id';");
		$answer=1;$err="";
	}
	return array($answer,$err);
}

function loadSupplPrice($suppl_id){$db=DbSingleton::getTokoDb();$slave=new slave;$gmanual=new gmanual;
	$form_htm=RD."/tpl/suppl_price_table.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	
	list($csv_exist,$csv_file_name,$pre_table)=$this->showCsvPreviewPrice($suppl_id);
	
	$form=str_replace("{records_list}","<tr><td colspan=10 align='center'>Записи не завантажено</td></tr>",$form);
	$form=str_replace("{import_file_name}","Оберіть файл",$form);
	$form=str_replace("{csv_str_file}",$pre_table,$form);
	$form=str_replace("{suppl_id}",$suppl_id,$form);
	return $form;
}

function showCsvPreviewIndex($suppl_id){$db=DbSingleton::getDb();$slave=new slave; $csv_exist=0;$csv_file_name="Оберіть файл";$pre_table="<h3 align='center'>Записи відсутні</h3>";
	$r=$db->query("select * from T2_SUPPL_CSV where suppl_id='$suppl_id' and ftype='index' limit 0,1;");$n=$db->num_rows($r);require_once RD."/lib/clients_class.php"; $clients=new clients;
	if ($n==1){
		$file_name=$db->result($r,0,"file_name");
		$file_path=RD."/cdn/suppl_files/index/$suppl_id/$file_name";
		if (file_exists($file_path)){
			$form_htm=RD."/tpl/suppl_index_str_file.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
			$cols_list=""; $records_list="";
			$fna=explode(".",$file_name);$ft=count($fna);$file_type=$fna[$ft-1];
			if ($file_type=="xlsx"){
				require_once(RD.'/lib/excel-2/php-excel-reader/excel_reader2.php');
				require_once(RD.'/lib/excel-2/SpreadsheetReader.php');
				$Spreadsheet = new SpreadsheetReader($file_path);
				$Sheets=$Spreadsheet->Sheets(); $Spreadsheet -> ChangeSheet(0);$kol_cols=0;
				foreach ($Spreadsheet as $Key => $Row){$krs+=1;
					//echo $Key.': ';
					if($Row){$row="";$j=0;
						foreach($Row as $rw){$j+=1;
//							print(." | ");
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
				if ($handle) { //$db->query("delete from catalogue_price where provider='$provider';");
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


function finishSupplIndexImport($suppl_id,$start_row,$kol_cols,$cols){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;session_start();$user_id=$_SESSION["media_user_id"];$user_name=$_SESSION["user_name"];$answer=0;$err="Помилка збереження даних!";require_once RD."/lib/clients_class.php"; $clients=new clients;
	$suppl_id=$slave->qq($suppl_id);$start_row=$slave->qq($start_row);$kol_cols=$slave->qq($kol_cols);$cols=$slave->qq($cols);
	if ($suppl_id>0){		
		$r=$db->query("select * from T2_SUPPL_CSV where suppl_id='$suppl_id' and ftype='index' limit 0,1;");$n=$db->num_rows($r);
		if ($n==1){
			$file_name=$db->result($r,0,"file_name");
			$file_path=RD."/cdn/suppl_files/index/$suppl_id/$file_name";
			if (file_exists($file_path)){
				$cols_list=""; $records_list="";
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
					set_time_limit(0);$max_cols=0;
					$dbt->query("delete from T2_SUPPL_ARTICLES_IMPORT where suppl_id='$suppl_id';");$pkg_k=0;$max_pkg=500;$pkg="";
					foreach ($Spreadsheet as $Key => $Row){$krs+=1;
						if($Row){
							if ($krs>=$start_row){
								//print_r($Row);
								$suppl_index=trim(iconv("UTF-8", "Windows-1251",$Row[$index-1]));
								$suppl_brand=trim(iconv("UTF-8", "Windows-1251",$Row[$brand-1]));
								$suppl_art_id=trim(iconv("UTF-8", "Windows-1251",$Row[$art_id-1]));
								$suppl_return_delay=trim(iconv("UTF-8", "Windows-1251",$Row[$return_delay-1]));
								$suppl_warranty_info=trim(iconv("UTF-8", "Windows-1251",$Row[$warranty_info-1]));

								if ($pkg!=""){$pkg.=",";}
								$pkg.="('$suppl_id','$suppl_index','$suppl_brand','$suppl_art_id','$suppl_return_delay','$suppl_warranty_info')";
								$pkg_k+=1;
								if ($pkg_k==$max_pkg){ $dbt->query("insert into T2_SUPPL_ARTICLES_IMPORT (`suppl_id`,`suppl_index`,`suppl_brand`,`art_id`,`return_delay`,`warranty_info`) values $pkg;");$pkg="";$pkg_k=0;}
							}
						}
//						if ($krs==5){break;}
					}
					if ($pkg!=""){ $dbt->query("insert into T2_SUPPL_ARTICLES_IMPORT (`suppl_id`,`suppl_index`,`suppl_brand`,`art_id`,`return_delay`,`warranty_info`) values $pkg;");$pkg="";$pkg_k=0;}
					
					if (file_exists(RD."/cdn/suppl_files/index/$suppl_id/$file_name")){unlink(RD."/cdn/suppl_files/index/$suppl_id/$file_name");}
					$db->query("delete from T2_SUPPL_CSV  where `suppl_id`='$suppl_id' and ftype='index';");
					$answer=1;$err="";
				}
				if ($file_type=="csv"){
					$handle = @fopen($file_path, "r");
					if ($handle) { set_time_limit(0);$max_cols=0;
						$dbt->query("delete from T2_SUPPL_ARTICLES_IMPORT where suppl_id='$suppl_id'");$pkg_k=0;$max_pkg=500;$pkg="";
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
									if ($pkg_k==$max_pkg){ $dbt->query("insert into T2_SUPPL_ARTICLES_IMPORT (`suppl_id`,`suppl_index`,`suppl_brand`,`art_id`,`return_delay`,`warranty_info`) values $pkg;");$pkg="";$pkg_k=0;}
									//if ($krs==30){break;}
								}
							}
						}
						if ($pkg!=""){ $dbt->query("insert into T2_SUPPL_ARTICLES_IMPORT (`suppl_id`,`suppl_index`,`suppl_brand`,`art_id`,`return_delay`,`warranty_info`) values $pkg;");$pkg="";$pkg_k=0;}
						fclose($handle);
					}
					if (file_exists(RD."/cdn/suppl_files/index/$suppl_id/$file_name")){unlink(RD."/cdn/suppl_files/index/$suppl_id/$file_name");}
					$db->query("delete from T2_SUPPL_CSV  where `suppl_id`='$suppl_id' and ftype='index';");
					$answer=1;$err="";
				}
				
			}
		}
	}
	return array($answer,$err);
}


function getSupplStorageArray($suppl_id){$db=DbSingleton::getDb();$st=array();
	$r=$db->query("select id,name from A_CLIENTS_STORAGE where status='1' and client_id='$suppl_id' order by name asc;");$n=$db->num_rows($r);
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		$name=$db->result($r,$i-1,"name");
		$st[$i]["id"]=$id;
		$st[$i]["name"]=$name;
	}
	return $st;
	
}

function showCsvPreviewPrice($suppl_id){$db=DbSingleton::getDb();$slave=new slave; $csv_exist=0;$csv_file_name="Оберіть файл";$pre_table="<h3 align='center'>Записи відсутні</h3>";
	$r=$db->query("select * from T2_SUPPL_CSV where suppl_id='$suppl_id' and ftype='price' limit 0,1;");$n=$db->num_rows($r);require_once RD."/lib/clients_class.php"; $clients=new clients;
	if ($n==1){
		$file_name=$db->result($r,0,"file_name");
		$file_path=RD."/cdn/suppl_files/price/$suppl_id/$file_name";
		if (file_exists($file_path)){
			$storages=$this->getSupplStorageArray($suppl_id);
			$form_htm=RD."/tpl/price_str_file.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
			$cols_list=""; $records_list="";
			$fna=explode(".",$file_name);$ft=count($fna);$file_type=$fna[$ft-1];
			if ($file_type=="xlsx"){
				require_once(RD.'/lib/excel-2/php-excel-reader/excel_reader2.php');
				require_once(RD.'/lib/excel-2/SpreadsheetReader.php');
				$Spreadsheet = new SpreadsheetReader($file_path);
				$Sheets=$Spreadsheet->Sheets(); $Spreadsheet -> ChangeSheet(0);$kol_cols=0;
				foreach ($Spreadsheet as $Key => $Row){$krs+=1;
					//echo $Key.': ';
					if($Row){$row="";$j=0;
						foreach($Row as $rw){$j+=1;
//							print(." | ");
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
				require_once RD.'/lib/excel/excel_reader2.php';
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
				if ($handle) { //$db->query("delete from catalogue_price where provider='$provider';");
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

function findCachId($suppl_cash,$cash_data){$id=0;
	foreach($cash_data as $cash){
		if ($cash["abr"]==$suppl_cash){ $id=$cash["id"];break; }
		if ($cash["abr2"]==$suppl_cash){ $id=$cash["id"];break; }
		if ($cash["name"]==$suppl_cash){$id=$cash["id"];break; }
	}
	return $id;
}

function finishSupplPriceImport($suppl_id,$start_row,$kol_cols,$main_cash_id,$kours_usd,$kours_eur,$cols){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;session_start();$user_id=$_SESSION["media_user_id"];$user_name=$_SESSION["user_name"];$answer=0;$err="Помилка збереження даних!";require_once RD."/lib/clients_class.php"; $clients=new clients;
	$suppl_id=$slave->qq($suppl_id);$start_row=$slave->qq($start_row);$kol_cols=$slave->qq($kol_cols);$main_cash_id=$slave->qq($main_cash_id);$kours_usd=$slave->qq($kours_usd);$kours_eur=$slave->qq($kours_eur);$cols=$slave->qq($cols);
	if ($suppl_id>0){		
		$kours_usd=str_replace(",",".",$kours_usd);$kours_eur=str_replace(",",".",$kours_eur);
		

		$r=$db->query("select * from T2_SUPPL_CSV where suppl_id='$suppl_id' and ftype='price' limit 0,1;");$n=$db->num_rows($r);
		if ($n==1){
			$file_name=$db->result($r,0,"file_name");
			$file_path=RD."/cdn/suppl_files/price/$suppl_id/$file_name";
			//print "$file_path";
			if (file_exists($file_path)){
				$storages=$this->getSupplStorageArray($suppl_id); $kol_storages=count($storages);$suppl_storages_use=array();$sk=0;
				$cash_data=$clients->getCashDataArray(); 
//				$form_htm=RD."/tpl/price_str_file.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
				$cols_list=""; $records_list="";
				$index=0;$brand=0;$price_suppl=0;$cash=0;$stock=0; $storage_str="";
				for ($i=1;$i<=$kol_cols;$i++){
					if ($cols[$i]==1){$index=$i;}
					if ($cols[$i]==2){$brand=$i;}
					if ($cols[$i]==3){$price=$i;}
					if ($cols[$i]==4){$cash=$i;}
					for ($s=1;$s<=$kol_storages;$s++){
						$storage_id=$storages[$s]["id"];
						if ($cols[$i]=="stor_".$storage_id){
//							print "ok=$i; ";$sk+=1;
							$suppl_storages_use[$storage_id]=$i;
							$storage_str.="$storage_id,";
//							print "suppl_storages_use=$suppl_storages_use[$storage_id]\n";
						}
					}
				}if ($storage_str!=""){$storage_str=substr($storage_str,0,-1);}if ($storage_str==""){$storage_str=0;}
				
				$fna=explode(".",$file_name);$ft=count($fna);$file_type=$fna[$ft-1];$krs=0;
				if ($main_cash_id>0){$suppl_cash_id=$main_cash_id;}
				if ($file_type=="xlsx"){
					require_once(RD.'/lib/excel-2/php-excel-reader/excel_reader2.php');
					require_once(RD.'/lib/excel-2/SpreadsheetReader.php');
					$Spreadsheet = new SpreadsheetReader($file_path);
					$Sheets=$Spreadsheet->Sheets(); $Spreadsheet->ChangeSheet(0);
					set_time_limit(0);$max_cols=0;
					$dbt->query("update T2_SUPPL_IMPORT set status=0 where suppl_id='$suppl_id' and client_storage_id in ($storage_str);");$pkg_k=0;$max_pkg=50;$pkg="";
					foreach ($Spreadsheet as $Key => $Row){$krs+=1;
						if($Row){
							if ($krs>=$start_row){
								//print_r($Row);
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
	//							print "suppl_index=$suppl_index; $suppl_brand; $suppl_price; $suppl_cash\n";
	//							print_r($suppl_storages_use);
								for ($s=1;$s<=$kol_storages;$s++){
									$storage_id=$storages[$s]["id"];
									$stokCellNom=$suppl_storages_use[$storage_id]-1;
									$suppl_stock=trim(iconv("UTF-8", "Windows-1251", preg_replace('/\D/', '', $Row[$stokCellNom])));
									if ($suppl_stock>0){
										if ($pkg!=""){$pkg.=",";}
										$pkg.="('$suppl_id','$suppl_index','$suppl_brand','$suppl_price','$suppl_cash_id','$kours_usd','$price_usd','$storage_id','$suppl_stock',CURDATE())";
										$pkg_k+=1;
										if ($pkg_k==$max_pkg){ $dbt->query("insert into T2_SUPPL_IMPORT (`suppl_id`,`suppl_index`,`brand`,`price_suppl`, `cash_id`, `kours_usd`, `price_usd`, `client_storage_id`, `stock_suppl`,`data_update`) values $pkg;");$pkg="";$pkg_k=0;}
									}
								}
							}
						}
//						if ($krs==5){break;}
					}
					if ($pkg!=""){ $dbt->query("insert into T2_SUPPL_IMPORT (`suppl_id`,`suppl_index`,`brand`,`price_suppl`, `cash_id`, `kours_usd`, `price_usd`, `client_storage_id`, `stock_suppl`,`data_update`) values $pkg;");$pkg="";$pkg_k=0;}
					
					if (file_exists(RD."/cdn/suppl_files/price/$suppl_id/$file_name")){unlink(RD."/cdn/suppl_files/price/$suppl_id/$file_name");}
					$db->query("delete from T2_SUPPL_CSV  where `suppl_id`='$suppl_id' and ftype='price';");
					$answer=1;$err="";
				}
				if ($file_type=="csv"){
					$handle = @fopen($file_path, "r");
					if ($handle) { set_time_limit(0);$max_cols=0;
						$dbt->query("update T2_SUPPL_IMPORT set status=0 where suppl_id='$suppl_id' and client_storage_id in ($storage_str);");$pkg_k=0;$max_pkg=50;$pkg="";
						while (($buffer = fgets($handle, 4096)) !== false) {$krs+=1;
							$buf=explode(";",$buffer);
							if ($krs>=$start_row){
								if ($buffer!=""){
									$buf=str_replace("'","\'",$buf);$buf=str_replace('"','\"',$buf);
										
									$suppl_index=trim($buf[$index-1]);
									$suppl_brand=trim($buf[$brand-1]);
									$suppl_price=str_replace(",",".",trim($buf[$price-1]));
									//print "suppl_price=$suppl_price";
									if ($main_cash_id==0){
										$suppl_cash=trim($buf[$cash-1]);$suppl_cash_id=$this->findCachId($suppl_cash,$cash_data);
									}
									//print "suppl_cash_id=$suppl_cash_id\n";
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
											if ($pkg_k==$max_pkg){ $dbt->query("insert into T2_SUPPL_IMPORT (`suppl_id`,`suppl_index`,`brand`,`price_suppl`, `cash_id`, `kours_usd`, `price_usd`, `client_storage_id`, `stock_suppl`,`data_update`) values $pkg;");$pkg="";$pkg_k=0;}
										}
									}
									//if ($krs==30){break;}
								}
							}
						}
						if ($pkg!=""){ $dbt->query("insert into T2_SUPPL_IMPORT (`suppl_id`,`suppl_index`,`brand`,`price_suppl`, `cash_id`, `kours_usd`, `price_usd`, `client_storage_id`, `stock_suppl`,`data_update`) values $pkg;");$pkg="";$pkg_k=0;}
						fclose($handle);
					}
					if (file_exists(RD."/cdn/suppl_files/price/$suppl_id/$file_name")){unlink(RD."/cdn/suppl_files/price/$suppl_id/$file_name");}
					$db->query("delete from T2_SUPPL_CSV  where `suppl_id`='$suppl_id' and ftype='price';");
					$answer=1;$err="";
				}
				$r=$dbt->query("select * from `T2_SUPPL_ARTICLES_IMPORT` where suppl_id='$suppl_id';");$n=$dbt->num_rows($r);
				for ($i=1;$i<=$n;$i++){
					$art_id=$dbt->result($r,$i-1,"art_id");
					$suppl_brand=$dbt->result($r,$i-1,"suppl_brand");
					$suppl_index=$dbt->result($r,$i-1,"suppl_index");
					$return_delay=$dbt->result($r,$i-1,"return_delay");
					$dbt->query("update `T2_SUPPL_IMPORT` set `art_id`='$art_id', `return_delay`='$return_delay' WHERE `suppl_index` like '$suppl_index' and `suppl_id`='$suppl_id' and `brand` like '$suppl_brand' and `status`=1");
				}
				//$dbt->query("update `T2_SUPPL_IMPORT`, `T2_SUPPL_ARTICLES_IMPORT`  set `T2_SUPPL_IMPORT`.`art_id`=`T2_SUPPL_ARTICLES_IMPORT`.`art_id` WHERE `T2_SUPPL_IMPORT`.`suppl_index`=`T2_SUPPL_ARTICLES_IMPORT`.`suppl_index` and `T2_SUPPL_IMPORT`.`suppl_id`=`T2_SUPPL_ARTICLES_IMPORT`.`suppl_id` and `T2_SUPPL_IMPORT`.`status`=1");
				
			}
			
		}
	}
	return array($answer,$err);
}



function showSupplClientList($sel_id){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;
	$form_htm=RD."/tpl/clients_parrent_tree.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	$r=$db->query("select c.*,ot.name as org_type_name from A_CLIENTS c 
		left outer join A_ORG_TYPE ot on ot.id=c.org_type 
		left outer join A_CLIENTS_CATEGORY cc on cc.client_id=c.id
		left outer join A_CATEGORY ac on ac.id=cc.category_id
		
		where c.status=1 and ac.id=3 $where;");$n=$db->num_rows($r);$list="";
	$r=$db->query("select c.*,ot.name as org_type_name, t2cn.COUNTRY_NAME, t2st.STATE_NAME, t2rg.REGION_NAME, t2ct.CITY_NAME  from A_CLIENTS c 
		left outer join A_ORG_TYPE ot on ot.id=c.org_type 
		left outer join T2_COUNTRIES t2cn on t2cn.COUNTRY_ID=c.country 
		left outer join T2_STATE t2st on t2st.STATE_ID=c.state
		left outer join T2_REGION t2rg on t2rg.REGION_ID=c.region
		left outer join T2_CITY t2ct on t2ct.CITY_ID=c.city
		left outer join A_CLIENTS_CATEGORY cc on cc.client_id=c.id
		left outer join A_CATEGORY ac on ac.id=cc.category_id
		
		where c.status=1 and ac.id=3 $where;");$n=$db->num_rows($r);$list="";
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
		$address=$db->result($r,$i-1,"address");
		$cur="";$fn="<i class='fa fa-thumb-tack' onClick='setSupplClient(\"$id\", \"$name\")'></i>";
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

function showSupplStorageSelectList($sel_id){$db=DbSingleton::getTokoDb();$list="";
	$r=$db->query("select * from STORAGE where status='1' order by name,id asc;");$n=$db->num_rows($r);
	for ($i=1;$i<=$n;$i++){

		$id=$db->result($r,$i-1,"id");
		$name=$db->result($r,$i-1,"name");
		$sel="";if ($id==$sel_id){$sel=" selected";}
		$list.="<option value='$id' $sel>$name</option>";
	}
	return $list;
}

function getSupplNameById($sel_id, $field="name"){$name="";
	$r=$db->query("select `$field` from A_CLIENTS where id='$sel_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){$name=$db->result($r,0,"$field");}
	return $name;
}
function loadStateSelectList($country_id,$sel_id){$slave=new slave;
	$list=$slave->showSelectSubList("T2_STATE","COUNTRY_ID","$country_id","STATE_ID","STATE_NAME",$sel_id);
	return $list;	
}
function loadRegionSelectList($state_id,$sel_id){$slave=new slave;
	return $slave->showSelectSubList("T2_REGION","STATE_ID","$state_id","REGION_ID","REGION_NAME",$sel_id);
}
function loadCitySelectList($region_id,$sel_id){$slave=new slave;//$list="";
	return "<option value='NEW'>Добавити населений пункт</option>".$slave->showSelectSubList("T2_CITY","REGION_ID","$region_id","CITY_ID","CITY_NAME",$sel_id);
}

function getMediaUserName($user_id){$db=DbSingleton::getDb();$name="";
	$r=$db->query("select name from media_users where id='$user_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){$name=$db->result($r,0,"name");}
	return $name;
}
function getAClientName($clie_id){$db=DbSingleton::getDb();$name="";
	$r=$db->query("select name from A_CLIENTS where id='$client_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){$name=$db->result($r,0,"name");}
	return $name;
}
	
	
function showSupplCoopList(){$db=DbSingleton::getDb();$slave=new slave;$where=""; $manual=new manual;
	$r=$db->query("select * from J_SUPPLIERS_COOPERATION");$n=$db->num_rows($r);$list="";
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		$company=$db->result($r,$i-1,"company");
		$name=$db->result($r,$i-1,"name");
		$phone=$db->result($r,$i-1,"phone");
		$email=$db->result($r,$i-1,"email");
		$city_id=$db->result($r,$i-1,"city_id"); $city_name=$this->getCityName($city_id);
		$commentary=$db->result($r,$i-1,"commentary");
		$file_id=$db->result($r,$i-1,"file_id");
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
	$r=$db->query("select mcaption from manual where id='$key';");
	$cap=$db->result($r,0,"mcaption");	
	return $cap;
}
	
function getManualSelectList($keyId,$selId){$db=DbSingleton::getDb();$form="";
	$r=$db->query("select id,mcaption from `manual` where `key`='$keyId';");$n=$db->num_rows($r);
	for ($i=1;$i<=$n;$i++) {
		$id=$db->result($r,$i-1,"id");
		$form.="<option value='".$id."' ";if ($selId==$id){$form.=" selected='selected'";} $form.=">".$db->result($r,$i-1,"mcaption")."</option>";}
	return $form;
}
	
function showSupplCoopCard($suppl_id){$db=DbSingleton::getDb();$slave=new slave;session_start();$user_id=$_SESSION["media_user_id"];$user_name=$_SESSION["user_name"];
$manual=new manual;
	$form_htm=RD."/tpl/suppliers_cooperation_card.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	
	$r=$db->query("select c.*, t2ct.CITY_NAME from J_SUPPLIERS_COOPERATION c 
				left outer join T2_CITY t2ct on t2ct.CITY_ID=c.city_id
				where c.id='$suppl_id' limit 1;");
    $n=$db->num_rows($r);
									  
	if ($n==0){$form_htm=RD."/tpl/access_deny.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);} }
	if ($n==1){
		$id=$db->result($r,0,"id");
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
	
function saveSupplCoop($suppl_id,$company,$name,$phone,$email,$city_id,$comment,$status) { $db=DbSingleton::getDb(); $answer=0; $err="Помилка збереження даних!";
	if ($suppl_id>0){
		$db->query("update J_SUPPLIERS_COOPERATION set `company`='$company', `name`='$name', `phone`='$phone', `email`='$email', `city_id`='$city_id', `commentary`='$comment', `status`='$status' where `id`='$suppl_id';");
		$answer=1; $err="";
	}
	return array($answer,$err);
}
	
function getLocation($city_id) { $db=DbSingleton::getDb();
	$r=$db->query("select REGION_ID from T2_CITY where CITY_ID='$city_id' limit 1;"); $region_id=$db->result($r,0,"REGION_ID");
	$r=$db->query("select STATE_ID from T2_REGION where REGION_ID='$region_id' limit 1;"); $state_id=$db->result($r,0,"STATE_ID");
	$r=$db->query("select COUNTRY_ID from T2_STATE where STATE_ID='$state_id' limit 1;"); $country_id=$db->result($r,0,"COUNTRY_ID");
								
	return array($region_id,$state_id,$country_id);
}
	
function getCityName($city_id) { $db=DbSingleton::getDb();
	$r=$db->query("select CITY_NAME from T2_CITY where CITY_ID='$city_id' limit 1;");
	$city_name=$db->result($r,0,"CITY_NAME");
	return $city_name;
}
	
function getRegionName($region_id) { $db=DbSingleton::getDb();
	$r=$db->query("select REGION_NAME from T2_REGION where REGION_ID='$region_id' limit 1;");
	$region_name=$db->result($r,0,"REGION_NAME");
	return $region_name;
}
	
function getStateName($state_id) { $db=DbSingleton::getDb();
	$r=$db->query("select STATE_NAME from T2_STATE where STATE_ID='$state_id' limit 1;");
	$state_name=$db->result($r,0,"STATE_NAME");
	return $state_name;
}
	
function getCountryName($country_id) { $db=DbSingleton::getDb();
	$r=$db->query("select COUNTRY_NAME from T2_COUNTRIES where COUNTRY_ID='$country_id' limit 1;");
	$country_name=$db->result($r,0,"COUNTRY_NAME");
	return $country_name;
}


}
?>