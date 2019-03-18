<?php
class import_rest{

function show_import_rest_form(){$db=new db;$slave=new slave;session_start();$user_id=$_SESSION["media_user_id"];$user_name=$_SESSION["user_name"];
	$form_htm=RD."/tpl/import_rest_str_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
	
	list($csv_exist,$csv_file_name,$pre_table)=$this->showCsvPreview();
	$form=str_replace("{records_list}","<tr><td colspan=10 align='center'>������ �� �����������</td></tr>",$form);
	$form=str_replace("{import_file_name}","������ ����",$form);
	$form=str_replace("{csv_str_file}",$pre_table,$form);
	
	return $form;
}


function showCsvPreview(){$db=new db;$slave=new slave; $csv_exist=0;$csv_file_name="������ ����";$pre_table="<h3 align='center'>������ �� �����������</h3>"; session_start();$user_id=$_SESSION["media_user_id"];
	$r=$db->query("select * from J_IMPORT_REST_CSV where user_id='$user_id' order by id desc limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){
		$file_name=$db->result($r,0,"file_name");
		$file_path=RD."/cdn/import_rest_files/$user_id/$file_name";
		if (file_exists($file_path)){
			$form_htm=RD."/tpl/csv_rest_str_file.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
			$cols_list=""; $records_list="";
			
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
						}if ($row!=""){
							$records_list.="<tr>$row</tr>";
						}
					}
					if ($fn==30){break;}
				}
				fclose($handle);
			}
			$form=str_replace("{cols_list}",$cols_list,$form);
			$form=str_replace("{records_list}",$records_list,$form);
			$form=str_replace("{kol_cols}",$kol_cols,$form);
			$csv_file_name=$file_name;$csv_exist=1;$pre_table=$form;
		}
	}
	return array($csv_exist,$csv_file_name,$pre_table);
}

function finishRestCsvImport(){$db=new db;$dbt=new dbt;$slave=new slave;session_start();$user_id=$_SESSION["media_user_id"];$user_name=$_SESSION["user_name"];$answer=0;$err="������� ���������� �����!";
	$r=$db->query("select * from J_IMPORT_REST_CSV where user_id='$user_id' order by id desc limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){
		$file_name=$db->result($r,0,"file_name");
		$file_path=RD."/cdn/import_rest_files/$user_id/$file_name";
		if (file_exists($file_path)){
			$form_htm=RD."/tpl/csv_rest_str_file.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
			require_once(RD."/lib/income_class.php");$income=new income;
			$income_id=0;$income_name=$prev_income_name=""; $index="";$brand="";$price_su=0;$cash_id=1;$tam_code="";$suppl_id=0;$firm_id=0;$cours_to_uah=0;$storage_id=0;$cell_id=0;$amount=0; $prev_brand="";$prev_tam_code="";$invoice_summ=0;
			list($usd_to_uah,$eur_to_uah)=$this->loadIncomeKours(1,date("Y-m-d"));
			$handle = @fopen($file_path, "r");
			if ($handle) { //$db->query("delete from catalogue_price where provider='$provider';");
				set_time_limit(0);
				while (($buffer = fgets($handle, 4096)) !== false) {$fn+=1;
					if ($buffer!=""){
						$buf=explode(";",$buffer);
						if ($fn>=2){
							$buf=str_replace("'","\'",$buf);$buf=str_replace('"','\"',$buf);
							$income_name=trim($buf[0]);
							if ($income_name!=$prev_income_name){
								if ($prev_income_name!=""){
									list($prefix,$doc_nom)=$income->getIncomeClientPrefixDocument($income_id,$firm_id);
									$db->query("update J_INCOME set invoice_summ='$invoice_summ', summ_end='$invoice_summ', oper_status='31', `prefix`='$prefix', `doc_nom`='$doc_nom' where id='$income_id';");
				
									$this->recalculatePrice($income_id,$invoice_summ,$cours_to_uah,$cash_id,$usd_to_uah,$eur_to_uah,$storage_id);
									$invoice_summ=0;
								}
								$suppl_id=trim($buf[6]);$firm_id=trim($buf[7]); $cash_id=trim($buf[4]);$cours_to_uah=trim($buf[8]);
								$cours_to_uah=str_replace(",",".",$cours_to_uah);$cours_to_uah=str_replace(" ","",$cours_to_uah);
								$storage_id=trim($buf[9]);$cell_id=trim($buf[10]);
								$income_id=$this->getNewIncomeId($income_name,$suppl_id,$firm_id,$cash_id,$cours_to_uah,$storage_id,$cell_id);
								$prev_income_name=$income_name;
							}
							$index=trim($buf[1]);$brand=trim($buf[2]); $price_su=trim($buf[3]);
							$tam_code=trim($buf[5]); $cell_id=trim($buf[10]);$amount=trim($buf[11]);
							$price_su=str_replace(",",".",$price_su);$price_su=str_replace(" ","",$price_su);
							$amount=str_replace(",",".",$amount);$amount=str_replace(" ","",$amount);
							
							if ($brand!=$prev_brand){							
								$brand_id=$this->getBrandId($brand);
								$prev_brand=$brand;
							}
							$art_id=$this->getArtId($index,$brand_id);
							
							if ($art_id>0 && $brand_id>0){
								if ($tam_code!=$prev_tam_code){
									$costum_id=$this->getCostumsId($tam_code);
									$prev_tam_code=$tam_code;
								}
								$this->checkArtCostumCode($art_id,$costum_id);
								
								if ($cash_id==1){
									$price_buh_cashin=$price_su; $price_man_cashin=$price_su; $price_man_usd=round($price_su/$cours_to_uah,2);
									$price_man_uah=$price_su; $price_buh_uah=$price_man_uah;
								}
								if ($cash_id==2){
									$price_buh_cashin=$price_su; $price_man_cashin=$price_su; $price_man_usd=$price_su;
									$price_man_uah=$price_su*$cours_to_uah; $price_buh_uah=$price_man_uah;
								}
								if ($cash_id==3){
									$price_buh_cashin=$price_su; $price_man_cashin=$price_su; 
									$price_man_usd=round($price_su/$cours_to_uah,2);
									$price_man_uah=$price_su; $price_buh_uah=$price_man_uah;
								}
								$invoice_summ+=$price_su*$amount;
								$db->query("insert into J_INCOME_STR (`income_id`,`art_id`,`article_nr_displ`,`brand_id`,`costums_id`,`amount`,`price_buh_cashin`,`price_man_cashin`,`price_man_uah`,`price_buh_uah`,`price_man_usd`,`import_cell_id`) values ('$income_id','$art_id','$index','$brand_id','$costum_id','$amount','$price_buh_cashin','$price_man_cashin','$price_man_uah','$price_buh_uah','$price_man_usd','$cell_id');");
								
								$slave->addJuornalArtDocs(1,$income_id,$art_id,$amount);
							}
							
						}
						//if ($fn==30){break;}
					}
				}
				list($prefix,$doc_nom)=$income->getIncomeClientPrefixDocument($income_id,$firm_id);
				$db->query("update J_INCOME set invoice_summ='$invoice_summ', summ_end='$invoice_summ', oper_status='31', `prefix`='$prefix', `doc_nom`='$doc_nom' where id='$income_id';");
				$this->recalculatePrice($income_id,$invoice_summ,$cours_to_uah,$cash_id,$usd_to_uah,$eur_to_uah,$storage_id);
				fclose($handle);
				if (file_exists(RD."/cdn/import_rest_files/$user_id/$file_name")){unlink(RD."/cdn/import_rest_files/$user_id/$file_name");}
				$db->query("update J_IMPORT_REST_CSV set status=0 where `user_id`='$user_id';");
				$answer=1;$err="";
			}
		}
	}
	return array($answer,$err);
}
	
function getKourForDate($cash_id_to,$cash_id_from,$data){$db=new db; $kours=1; if ($data=="0000-00-00"){$data=date("Y-m-d");}
	if ($cash_id_from!=$cash_id_to){
		$r=$db->query("select `kours_value` from `J_KOURS` where `cash_id`='$cash_id_from' and `data_from`<='$data' and (`data_to`='0000-00-00' or `data_to`>='$data') and in_use in (0,1) order by id desc limit 0,1;");$n=$db->num_rows($r);if ($n==1){$kours=$db->result($r,0,"kours_value");}
	}
	return $kours;
}
function loadIncomeKours($cash_id,$data){$usd_to_uah=1;$eur_to_uah=1;
	$usd_to_uah=$this->getKourForDate(1,2,$data);
	$eur_to_uah=$this->getKourForDate(1,3,$data);
	return array($usd_to_uah,$eur_to_uah);
}
function get_df_doc_nom_new(){ $db=new db;$doc_nom=0;
	$r=$db->query("select max(doc_nom) as mid from J_INCOME where oper_status='30' and status='1' limit 0,1;");$doc_nom=0+$db->result($r,0,"mid")+1;
	return $doc_nom;
}	
function getNewIncomeId($income_name,$suppl_id,$firm_id,$cash_id,$cours_to_uah,$storage_id,$cell_id){$db=new db;$slave=new slave;$manual=new manual; session_start();$user_id=$_SESSION["media_user_id"];$user_name=$_SESSION["user_name"]; $income_id=0;
	$r=$db->query("select max(id) as mid from J_INCOME;");$income_id=0+$db->result($r,0,"mid")+1;
	$doc_nom=$this->get_df_doc_nom_new();
	$db->query("insert into J_INCOME (`id`,`type_id`,`prefix`,`doc_nom`,`import_1c`,`user_id`,`data`,`invoice_income`,`invoice_data`,`client_id`,`client_seller`,`cash_id`,`cours_to_uah`,`storage_id`,`storage_cells_id`) values ('$income_id','0','��','$doc_nom','1','$user_id',CURDATE(),'$income_name',CURDATE(),'$firm_id','$suppl_id','$cash_id','$cours_to_uah','$storage_id','$cell_id');");
	return $income_id;
}

function getArtId($code,$brand_id){$db=new dbt;$slave=new slave;$cat=new catalogue;$id=0; $code=$slave->qq($code); $code=$cat->clearArticle($code);
	$r=$db->query("select ART_ID from T2_ARTICLES where ARTICLE_NR_SEARCH='$code' and BRAND_ID='$brand_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){	$id=$db->result($r,0,"ART_ID");	}
	return $id;	
}
function getCostumsId($code){$db=new dbt;$slave=new slave;$id=0; $code=$slave->qq($code);
	$r=$db->query("select COSTUMS_ID from T2_COSTUMS where COSTUMS_CODE='$code' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){	$id=$db->result($r,0,"COSTUMS_ID");	}
	return $id;	
}
function getCountryId($code){$db=new dbt;$slave=new slave;$id=0; $code=$slave->qq($code);
	$r=$db->query("select COUNTRY_ID from T2_COUNTRIES where COUNTRY_NAME='$code' or (`ALFA2`='$code' and `ALFA2`!='') or (`ALFA3`='$code' and `ALFA3`!='') limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){	$id=$db->result($r,0,"COUNTRY_ID");	}
	return $id;	
}
function getBrandId($code){$db=new dbt;$slave=new slave;$id=0; $code=$slave->qq($code);
	$r=$db->query("select BRAND_ID from T2_BRANDS where BRAND_NAME='$code' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){	$id=$db->result($r,0,"BRAND_ID");	}
	return $id;	
}

function checkArtCostumCode($art_id,$costum_id)	{$db=new dbt;
	$r=$db->query("select COSTUMS_ID from T2_ZED where ART_ID='$art_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==0){
		$db->query("insert into T2_ZED (ART_ID,COSTUMS_ID) values ('$art_id','$costum_id');");
	}
	if ($n==1){
		$ex_costums_id=$db->result($r,0,"COSTUMS_ID");
		if ($ex_costums_id!=$costum_id){
			$db->query("update T2_ZED set COSTUMS_ID='$costum_id' where ART_ID='$art_id';");
		}
	}
	return ;
}
	
	
function recalculatePrice($income_id,$invoice_summ,$cours_to_uah,$cash_id,$usd_to_uah,$eur_to_uah,$storage_id){$db=new db; $dbt=new dbt; 
	require_once(RD."/lib/catalogue_class.php");$cat=new catalogue;
	$costums_pd_uah=$costums_pp_uah=$costums_summ_uah=0;
	$tz=0;	$tl=0;	$rb=0;	$ro=0;	$sz=0; 
	$r=$db->query("select * from J_INCOME_STR where income_id='$income_id';");$n=$db->num_rows($r);
	for ($i=1;$i<=$n;$i++){
		$id=$db->result($r,$i-1,"id");
		$art_id=$db->result($r,$i-1,"art_id");
		$storage_cells_id=$db->result($r,$i-1,"import_cell_id");
		$amount=$db->result($r,$i-1,"amount");
		$price_income=$db->result($r,$i-1,"price_buh_cashin");
		$weight_netto=0;
		if ($amount>0 && $price_income>0){
			$p_weight=0;
			$p_money=round($amount*$price_income/$invoice_summ*100,4);
			
			$tl_uah=0;
			if ($p_weight>0){
				$tl_uah=round($tl*$p_weight/100,4);
			}
			$rb_uah=round($rb*$p_money/100,4);
			$ro_uah=round($ro*$p_money/100,4);
			
			$nds_uah=0;
			//if (vat_use==1){ nds_uah=round(parseFloat(price_income*amount*cours_to_uah*0.2),4);nds_uah=nds_uah.toFixed(4); }
			$kurs=1; $kurs_usd=$usd_to_uah;
			if ($cash_id==1){$kurs=1;}if ($cash_id==2){$kurs=$usd_to_uah;}if ($cash_id==3){$kurs=$eur_to_uah;}
			
			$suvsdo=$price_income*$amount+(($tl_uah+$rb_uah+$ro_uah+$nds_uah)/$kurs);
			$suvsdo=round($suvsdo/$amount,4);
			
			$price_man_cashin=$suvsdo;
			
			$su_usd=round($suvsdo*$kurs/$kurs_usd,4);
			$price_man_usd=$su_usd;
			
			
			$sb_uah=$price_income*$amount*$cours_to_uah+($tl_uah+$rb_uah);
			$sb_uah=$sb_uah/$amount;
			$sb_uah=round($sb_uah,4);
			$price_buh_uah=$sb_uah;
			
			$su_uah=($price_income*$amount*$cours_to_uah)+($tl_uah+$rb_uah+$ro_uah+$nds_uah);
			$su_uah=round($su_uah/$amount,4);
			$price_man_uah=$su_uah;
			$db->query("update J_INCOME_STR set price_man_cashin='$price_man_cashin', price_man_usd='$price_man_usd', price_buh_uah='$price_buh_uah', price_man_uah='$price_man_uah' where id='$id';");
			
			list($oper_price,$general_stock)=$cat->getArticleOperPriceGeneralStock($art_id);
			$new_oper_price=round((($oper_price*$general_stock)+($amount*$price_man_usd))/($amount+$general_stock),2);
			$new_general_stock=$amount+$general_stock;
			$cat->setArticleOperPriceGeneralStock($art_id,$new_oper_price,$new_general_stock);
					
			$dbt->query("insert into T2_ARTICLES_STOCK (`art_id`,`income_id`,`amount`,`price`,`oper_price`) value ('$art_id','$income_id','$amount','$price_man_usd','$new_oper_price')");
					
			$db->query("insert into T2_ARTICLES_PARTITIONS (`art_id`,`op_type`,`parrent_type_id`,`parrent_doc_id`,`amount`,`rest`,`price`,`oper_price`,`price_buh_uah`,`price_man_uah`) value ('$art_id','1','1','$income_id','$amount','$amount','$price_man_usd','$new_oper_price','$price_buh_uah','$price_man_uah')");
					
			$r2=$dbt->query("select `AMOUNT` from T2_ARTICLES_STRORAGE_CELLS where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id' and `STORAGE_CELLS_ID`='$storage_cells_id' limit 0,1;");$n2=$dbt->num_rows($r2);
			if ($n2==1){
				$amount_ex=$dbt->result($r2,0,"AMOUNT");
				$amount_ex+=$amount;
				$dbt->query("update T2_ARTICLES_STRORAGE_CELLS set `AMOUNT`='$amount_ex' where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id' and `STORAGE_CELLS_ID`='$storage_cells_id' limit 1;");
			}
			if ($n2==0){
				$dbt->query("insert into T2_ARTICLES_STRORAGE_CELLS (`ART_ID`,`AMOUNT`,`STORAGE_ID`,`STORAGE_CELLS_ID`) values ('$art_id','$amount','$storage_id','$storage_cells_id');");
			}

			$rs=$dbt->query("select SUM(`AMOUNT`) as `amount` from T2_ARTICLES_STRORAGE where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id';");$amount_ex=$db->result($rs,0,"amount")+0;$ers=0;
			if ($amount_ex!=0){$amount_ex+=$amount;$ers=1;
				$dbt->query("update T2_ARTICLES_STRORAGE set `AMOUNT`='$amount_ex' where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id';");
			}
			if ($amount_ex==0 && $ers==0){
				$dbt->query("insert into T2_ARTICLES_STRORAGE (`ART_ID`,`AMOUNT`,`RESERV_AMOUNT`,`STORAGE_ID`) values ('$art_id','$amount','0','$storage_id');");
			}

		}
	}	
}
	
	
	
}
?>