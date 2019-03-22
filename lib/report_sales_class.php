<?php

class report_sales {
	
	function showReportSales($date,$tpoint) {$db=DbSingleton::getTokoDb(); $list=""; 
		if ($tpoint=="0") $where_tpoint=""; else $where_tpoint="and TPOINT_ID=$tpoint";
		if ($date=="0") $where_date=""; else $where_date="and MONTH='$date"."-00'";
											 
		$form_htm=RD."/tpl/report_sales_list.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}	
		$r=$db->query("select * from T2_ARTICLES_SALES where art_id>0 $where_tpoint $where_date;"); $n=$db->num_rows($r);
		if ($n>0) {
			for ($i=1;$i<=$n;$i++){
				$art_id=$db->result($r,$i-1,"ART_ID");
				list($article,$brand_id)=$this->getArticle($art_id); $brand=$this->getBrandName($brand_id);
				$tpoint_id=$db->result($r,$i-1,"TPOINT_ID"); $tpoint_name=$this->getTpointNameById($tpoint_id);
				$amount=$db->result($r,$i-1,"AMOUNT");
				$list.="<tr>
					<td>$i</td>
					<td>$article</td>
					<td>$brand</td>
					<td>$art_id</td>
					<td>$tpoint_name</td>
					<td>$amount</td>
				</tr>";
			} 
		} 
		$form=str_replace("{report_sales_list}",$list,$form);
		return $form;
	}
	
	function getBrandName($id){$db=DbSingleton::getTokoDb();
		$r=$db->query("select BRAND_NAME from T2_BRANDS where BRAND_ID='$id' limit 1;");
		$name=$db->result($r,0,"BRAND_NAME");	
		return $name;	
	}
	
	function getTpointNameById($sel_id, $field="name"){$name=""; $db=DbSingleton::getDb();
		$r=$db->query("select `$field` from T_POINT where id='$sel_id' limit 0,1;");$n=$db->num_rows($r);
		if ($n==1){$name=$db->result($r,0,"$field");}
		return $name;
	}
	
	function getArticle($art_id){$db=DbSingleton::getTokoDb();
		$r=$db->query("select ARTICLE_NR_DISPL,BRAND_ID from T2_ARTICLES where ART_ID='$art_id' limit 1;"); $n=$db->num_rows($r);
		if ($n==1){	
			$article=$db->result($r,0,"ARTICLE_NR_DISPL");	
			$brand_id=$db->result($r,0,"BRAND_ID");	
		}
		return array($article,$brand_id);	
	}
	
	function getTpointList() { $db=DbSingleton::getDb(); $list=""; $list="<option value='0'>Всі торгові точки</option>";
		$r=$db->query("select * from T_POINT where status=1 order by id asc;"); $n=$db->num_rows($r);
		for ($i=1;$i<=$n;$i++){
			$id=$db->result($r,$i-1,"id");
			$name=$db->result($r,$i-1,"name");
			$caption=$db->result($r,$i-1,"full_name");
			$list.="<option value='$id'>$caption ($name)</option>";
		}
		return $list;	
	}
}