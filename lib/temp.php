<?php 

function startJmovingStorageSelect($jmoving_id){$db=DbSingleton::getDb();$slave=new slave;$cat=new catalogue;$answer=0;$err="";
	$jmoving_id=$slave->qq($jmoving_id);
	$r=$db->query("select oper_status,status_jmoving,storage_id_to from J_MOVING where id='$jmoving_id' limit 0,1;");$n=$db->num_rows($r);
	if ($n==1){
		$oper_status=$db->result($r,0,"oper_status");
		$status_jmoving=$db->result($r,0,"status_jmoving");
		$storage_id_to=$db->result($r,0,"storage_id_to");
		if ($storage_id_to==0){$answer=0;$err="Не зазначено склад переміщення.";}
		if ($status_jmoving>47 || $oper_status>30){$answer=0;$err="Переміщення заблоковано. Зміни вносити заборонено.";}
		if ($oper_status==30 && $status_jmoving>=44 && $status_jmoving<=47 && $storage_id_to>0) {
			
			$ms_ar=array();
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
					
					$db->query("update J_MOVING_SELECT_TEMP set `articles_amount`='$sum_art_amount',`amount`='$sum_amount',`volume`='$sum_volume',`weight_netto`='$sum_weight_netto',`weight_brutto`='$sum_weight_brutto' where id='$select_id' and '$jmoving_id'='$jmoving_id';");
				}
			
				$answer=1;$err="";
			}
			
		}
	}
	return array($answer,$err);
}

function makesJmovingStorageSelect($jmoving_id){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;
	$cat=new catalogue;$answer=0;$err="";session_start();$user_id=$_SESSION["media_user_id"];$jmoving_id=$slave->qq($jmoving_id);
												
	$r=$db->query("select oper_status,status_jmoving,storage_id_to from J_MOVING where id='$jmoving_id' limit 0,1;");$n=$db->num_rows($r);
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
				$loc_type_id=$db->result($rm,$im-1,"loc_type_id");
				$articles_amount=$db->result($rm,$im-1,"articles_amount");
				$amount=$db->result($rm,$im-1,"amount");
				$volume=$db->result($rm,$im-1,"volume");
				$weight_netto=$db->result($rm,$im-1,"weight_netto");
			 	$weight_brutto=$db->result($rm,$im-1,"weight_brutto");
				$cur_date=date("Y-m-d H:i:s");
				$db->query("insert into J_SELECT (`id`,`parrent_doc_type_id`,`parrent_doc_id`,`data_create`,`tpoint_id`,`storage_id`,`articles_amount`,`amount`,`volume`,`weight_netto`,`weight_brutto`,`status_select`,`user_create`) values ('$select_id','1','$jmoving_id','$cur_date','$tpoint_id','$storage_id','$articles_amount','$amount','$volume','$weight_netto','$weight_brutto','82','$user_id');");
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
					
					$rsc=$dbt->query("select * from `T2_ARTICLES_STRORAGE_CELLS` where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_from';");$nsc=$dbt->num_rows($rsc);
					if ($nsc>0){
						for ($isc=1;$isc<=$nsc;$isc++){ $er=0;
							$amount_sc=$dbt->result($rsc,$isc-1,"AMOUNT");
							$reserv_amount_sc=$dbt->result($rsc,$isc-1,"RESERV_AMOUNT");
							$storage_cells_id_sc=$dbt->result($rsc,$isc-1,"STORAGE_CELLS_ID");
	
							if ($amount_sc>=$amount && $amount_sc>0){$isc=$nsc+1;$er=1;
								$amount_sc-=$amount;
								$reserv_amount_sc+=$amount;
								$db->query("insert into J_SELECT_STR (`select_id`,`art_id`,`article_nr_displ`,`brand_id`,`amount`,`storage_id_from`,`cell_id_from`) values ('$select_id','$art_id','$article_nr_displ','$brand_id','$amount','$storage_id_from','$storage_cells_id_sc');");
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
					if ($nsc==0){
						$rsc2=$dbt->query("select * from `T2_ARTICLES_STRORAGE` where `ART_ID`='$art_id' and `STORAGE_ID`='$storage_id_from' limit 0,1;");$nsc2=$dbt->num_rows($rsc2);
						if ($nsc2==1){ 
							$amount_sc=$dbt->result($rsc2,0,"AMOUNT");
							$reserv_amount_sc=$dbt->result($rsc2,0,"RESERV_AMOUNT");
	
							if ($amount_sc>=$amount && $amount_sc>0){
								$amount_sc-=$amount;
								$reserv_amount_sc+=$amount;
								$db->query("insert into J_SELECT_STR (`select_id`,`art_id`,`article_nr_displ`,`brand_id`,`amount`,`storage_id_from`) values ('$select_id','$art_id','$article_nr_displ','$brand_id','$amount','$storage_id_from');");
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
