<?php

class slave {

    var $month_id;

	function get_weekday_name($week_day){
		$wks = array ( '1' => "Ïîíåä³ëîê", '2' => "Â³âòîðîê", '3' => "Ñåðåäà", '4' => "×åòâåð", '5' => "Ï'ÿòíèöÿ", '6' => "Ñóáîòà", '7' => "Íåä³ëÿ");
		return $wks["$week_day"];
	}

	function get_weekday_abr($week_day){
		$wks = array ( '1' => "Ïí", '2' => "Âò", '3' => "Ñð", '4' => "×ò", '5' => "Ïò", '6' => "Ñá", '7' => "Íä");
		return $wks["$week_day"];
	}
	
	function showWeekdaySelectList($week_day){
        $list="";
		$wks = array ( '1' => "Ïîíåä³ëîê", '2' => "Â³âòîðîê", '3' => "Ñåðåäà", '4' => "×åòâåð", '5' => "Ï'ÿòíèöÿ", '6' => "Ñóáîòà", '7' => "Íåä³ëÿ");
		for ($i=1;$i<=7;$i++){
			$sel="";if ($i==$week_day){$sel=" selected='selected'";}
			$list.="<option value='$i' $sel>".$wks[$i]."</option>";
		}
		return $list;
	}

	function get_month_name($month_id){
		$mnths = array ( 'm01' => "Ñ³÷åíü", 'm02' => "Ëþòèé", 'm03' => "Áåðåçåíü", 'm04' => "Êâ³òåíü", 'm05' => "Òðàâåíü", 'm06' => "×åðâåíü", 'm07' => "Ëèïåíü", 'm08' => "Ñåðïåíü", 'm09' => "Âåðåñåíü",	'm10' => "Æîâòåíü",	'm11' => "Ëèñòîïàä",'m12' => "Ãðóäåíü");
		if (strlen($month_id)<2){$month_id="0".$month_id;}
		return $mnths["m$month_id"];
	}

	function data_word($data){
	    $dataw="-";$dt=explode(" ",$data);$time=$dt[1];$data=$dt[0];if ($time=="00:00:00"){$time="";}
		if ($data!="0000-00-00" && $data!=""){
			$mon=substr($data,5,2);$day="";
			$mnths = array ( '01'=>"Ñ³÷íÿ",'02'=>"Ëþòîãî",'03'=>"Áåðåçíÿ",'04'=>"Êâ³òíÿ",'05'=>"Òðàâíÿ",'06'=>"×åðâíÿ",'07'=>"Ëèïíÿ",'08'=>"Ñåðïíÿ",'09'=>"Âåðåñíÿ",'10'=>"Æîâòíÿ",'11'=>"Ëèñòîïàäà",'12'=>"Ãðóäíÿ");
			if (substr($data,8,1)=="0"){$day=substr($data,9,1);}
			if (substr($data,8,1)!="0"){$day=substr($data,8,2);}
			$dataw=$day." ".$mnths[$mon]." ".substr($data,0,4)." ð.";
		}
		$dataw.=" ".$time;
		return $dataw;
	}

	function data_word_short($data){
        $dataw="-";$dt=explode(" ",$data);$time=$dt[1];$data=$dt[0];if ($time=="00:00:00"){$time="";}
		if ($data!="0000-00-00" && $data!=""){
			$mon=substr($data,5,2);$day="";
			$mnths = array ( '01'=>"Ñ³÷",'02'=>"Ëþò",'03'=>"Áåð",'04'=>"Êâ³",'05'=>"Òðà",'06'=>"×åð",'07'=>"Ëèï",'08'=>"Ñåð",'09'=>"Âåð",'10'=>"Æîâ",'11'=>"Ëèñ",'12'=>"Ãðó");
			if (substr($data,8,1)=="0"){$day=substr($data,9,1);}
			if (substr($data,8,1)!="0"){$day=substr($data,8,2);}
			$dataw=$day." ".$mnths[$mon]." ".substr($data,2,2)."ð.";
		}
		$dataw.=" ".$time;
		return $dataw;
	}

	function data_r($data){
		$mon=substr($data,5,2);$day="";
		if (substr($data,8,1)=="0"){$day=substr($data,9,1);}
		if (substr($data,8,1)!="0"){$day=substr($data,8,2);}
		return $day."-".$mon."-".substr($data,0,4);
	}

	function get_calendar($name){
		return "<a href='javascript:void(0)' onclick='gfPop.fPopCalendar(document.getElementById(\"$name\"));return false;' HIDEFOCUS><img name='popcal' align='absbottom' src='js/calendar/calbtn.gif' width='34' height='22' border='0' alt=''></a>
				<iframe width=174 height=189 name='gToday:normal:agenda.js' id='gToday:normal:agenda.js' src='js/calendar/ipopeng.htm' scrolling='no' frameborder='0' style='visibility:visible; z-index:999; position:absolute; left:-500px; top:0px;'></iframe>";
	}
	
	function addJuornalArtDocs($doc_type, $doc_id, $art_id, $amount) { $db = DbSingleton::getDb();
		//doc_type: 1-income, 2-move, 3-sale, 4-backclient, 5-backsuppl, 6-write_off
	  	$db->query("INSERT INTO `J_ART_DOCS` (`art_id`,`amount`,`doc_type`,`doc_id`) VALUES ('$art_id','$amount','$doc_type',$doc_id);");
		//if (($doc_type>=1 && $doc_type<=5) && $doc_id>0 && $art_id!=""){ }
		return true;
	}
	
	function print_debug($inf){
		if($_SERVER['REMOTE_ADDR']=="78.152.169.139"){print $inf;}
	}

	function findFirstAndLastDay($anyDate){
	    list($yr,$mn,$dt)=split('-',$anyDate);
    	$timeStamp=mktime(0,0,0,$mn,1,$yr);
	    $firstDay=date('D',$timeStamp);
    	list($y,$m,$t)=split('-',date('Y-m-t',$timeStamp));
	    $lastDayTimeStamp=mktime(0,0,0,$m,$t,$y);
    	$lastDay=date('D',$lastDayTimeStamp);
	    $arrDay=array("$firstDay","$lastDay");
    	return $arrDay;
	}
	
	function qq($q){ 
		$q=str_replace("''", "'", $q);
		$q=str_replace("''", "'", $q);
		$q=str_replace("'", "&rsquo;", $q);
		return $q; 
	}

	function qqback($q) { 
		$q=str_replace("&rsquo;", "'", $q);
		return $q; 
	}

	function qqback_in($q) { 
		$q=str_replace("&rsquo;", "'", $q);
		$q=str_replace("\"", htmlentities("\""), $q);
		return $q; 
	}

	function qqback_js($q) { 
		$q=str_replace("&rsquo;", "'", $q);
		$q=str_replace('"', "»", $q);
		return $q; 
	}

	function translit($st) {
		$st=strtr($st,"àáâãäå¸çèéêëìíîïðñòóôõúûý_", "abvgdeeziyklmnoprstufh'iei");
		$st=strtr($st,"ÀÁÂÃÄÅ¨ÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÚÛÝ_", "ABVGDEEZIYKLMNOPRSTUFH'IEI");
		$st=strtr($st, array(
			"æ"=>"zh", "ö"=>"ts", "÷"=>"ch", "ø"=>"sh", 
			"ù"=>"shch","ü"=>"", "þ"=>"yu", "ÿ"=>"ya",
			"Æ"=>"ZH", "Ö"=>"TS", "×"=>"CH", "Ø"=>"SH", 
			"Ù"=>"SHCH","Ü"=>"", "Þ"=>"YU", "ß"=>"YA",
			"¿"=>"i", "¯"=>"Yi", "º"=>"ye", "ª"=>"Ye", "³"=>"i", "²"=>"I"
		));
		return $st;
	}

	function translit_file($st) {
		$st=strtr($st,"àáâãäå¸çèéêëìíîïðñòóôõúûý_", "abvgdeeziyklmnoprstufh'iei");
		$st=strtr($st,"ÀÁÂÃÄÅ¨ÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÚÛÝ_", "ABVGDEEZIYKLMNOPRSTUFH-IEI");
		$st=strtr($st, array(
			"æ"=>"zh", "ö"=>"ts", "÷"=>"ch", "ø"=>"sh", 
			"ù"=>"shch","ü"=>"", "þ"=>"yu", "ÿ"=>"ya",
			"Æ"=>"ZH", "Ö"=>"TS", "×"=>"CH", "Ø"=>"SH", 
			"Ù"=>"SHCH","Ü"=>"", "Þ"=>"YU", "ß"=>"YA",
			"¿"=>"i", "¯"=>"Yi", "º"=>"ye", "ª"=>"Ye", 
			" "=>"-", '"'=>"-", "³"=>"i", "²"=>"I"
		));
		return $st;
	}

	function to_money($int) {
		$int=round($int,2);$money=0;
		if (strpos($int,".")==strlen($int)-3){$money=$int;}
		if (strpos($int,".")==strlen($int)-2){$money=$int."0";}
		if (strpos($int,".")===false){$money=$int.".00";}
		return $money; 
	}

	function point_valid($q) {
		$q=str_replace(",", ".", $q);
		return $q; 
	}
	
	function get_date_from(){ if ($_POST["date_from"]==""){return $_GET["date_from"];} if ($_POST["date_from"]!=""){return $_POST["date_from"];} }

	function get_date_to(){ if ($_POST["date_to"]==""){return $_GET["date_to"];} if ($_POST["date_to"]!=""){return $_POST["date_to"];} }
	
	function get_dep(){ if ($_POST["dep"]==""){return $_GET["dep"];} if ($_POST["dep"]!=""){return $_POST["dep"];} }

	function get_dep_up(){ if ($_POST["dep_up"]==""){return $_GET["dep_up"];} if ($_POST["dep_up"]!=""){return $_POST["dep_up"];} }

	function get_dep_cur(){ if ($_POST["dep_cur"]==""){return $_GET["dep_cur"];} if ($_POST["dep_cur"]!=""){return $_POST["dep_cur"];} }

	function get_cur_id(){ if ($_POST["cur_id"]==""){return $_GET["cur_id"];} if ($_POST["cur_id"]!=""){return $_POST["cur_id"];} }

	function get_w(){ if ($_POST["w"]==""){return $_GET["w"];} if ($_POST["w"]!=""){return $_POST["w"];} }

	function get_wn(){ if ($_POST["wn"]==""){return $_GET["wn"];} if ($_POST["wn"]!=""){return $_POST["wn"];} }

	function get_file(){ if ($_POST["file"]==""){return $_GET["file"];} if ($_POST["file"]!=""){return $_POST["file"];} }

	function get_module(){ if ($_POST["module"]==""){return $_GET["module"];} if ($_POST["module"]!=""){return $_POST["module"];} }

	function get_module_page(){ if ($_POST["module_page"]==""){return $_GET["module_page"];} if ($_POST["module_page"]!=""){return $_POST["module_page"];} }
	
	function get_var(){ if ($_POST["var"]==""){return $_GET["var"];} if ($_POST["var"]!=""){return $_POST["var"];} }

	function get_link($srch,$lnk){
		$lnk.="&";
		$pos=strpos($lnk,$srch);
		$srch_val="";
		for ($i=$pos+strlen($srch)+1;$i<=strlen($lnk);$i++){
			$s=substr($lnk,$i,1);
			if ($s=="&"){ $i=strlen($lnk)+2; return $srch_val; }
			if ($s!="&"){ $srch_val.=$s;}
		}
		return $srch_val;
	}
	
	function resizeimage($image,$size,$filedir,$prefix){
		$prod_img=$filedir.$image;
		$prod_img_thumb=$filedir.$prefix.$image;
		if (file_exists("$prod_img")) {
			$sizes = getimagesize("$prod_img");
			$aspect_ratio = $sizes[0]/$sizes[1]; 
			$type=$sizes[2];
			if ($sizes[0] <= $size){
				$new_width = $sizes[0];
				$new_height = $sizes[1];
			} else {
				$new_width = $size;
				$new_height = abs($new_width/$aspect_ratio);
			}
			$destimg=imagecreatetruecolor($new_width,$new_height);
			if ($type==1){	$srcimg=ImageCreateFromGIF($prod_img); }
			if ($type==2){	$srcimg=ImageCreateFromJPEG($prod_img); }
			if ($type==3){	$srcimg=ImageCreateFromPNG($prod_img); }
			if ($type==4){	$srcimg=ImageCreateFromWBMP($prod_img); }
			imagecopyresampled($destimg,$srcimg,0,0,0,0,$new_width,$new_height,ImageSX($srcimg),ImageSY($srcimg));
			if ($type==1){ ImageGIF($destimg,$prod_img_thumb,100); }
			if ($type==2){ ImageJPEG($destimg,$prod_img_thumb,100); }
			if ($type==3){ imagecolortransparent($destimg, "");ImagePNG($destimg,$prod_img_thumb,100); }
			if ($type==4){ ImageWBMP($destimg,$prod_img_thumb,100); }
			imagedestroy($destimg);
		}
		return;
	}

	function get_file_deps($file){ $db=DbSingleton::getDb();
		$r=$db->query("select id from module_files where file='$file';");
		$n=$db->num_rows($r);
		if ($n>0){
			$id=$db->result($r,0,"id");
			$r1=$db->query("select id,dep_up from deps where file='$id';");
			$n1=$db->num_rows($r1);
			if ($n1>0){ $dep_cur=$db->result($r1,0,"id");$dep_up=$db->result($r1,0,"dep_up");}
		}
		return array($dep_up,$dep_cur);
	}
	
	function showSelectList($table,$field_id,$field,$sel_id){$db=DbSingleton::getDb();
	    $list="<option value='0'></option>";
		$r=$db->query("select `$field_id`,`$field` from `$table` order by `$field` asc;");$n=$db->num_rows($r);
		for ($i=1;$i<=$n;$i++){
			$id=$db->result($r,$i-1,"$field_id");
			$caption=$db->result($r,$i-1,"$field");
			$sel="";if ($id==$sel_id){$sel=" selected='selected'";}
			$list.="<option value='$id' $sel>$caption</option>";
		}
		return $list;
	}

	function showSelectSubList($table,$prnt_field,$prnt_id,$field_id,$field,$sel_id){$db=DbSingleton::getTokoDb();
	    $list="<option value='0'></option>";
		$where=" and `$prnt_field`='$prnt_id'";//if ($prnt_id==0){$where="";}
		$r=$db->query("select `$field_id`,`$field` from `$table` where 1 $where order by `$field` asc;");$n=$db->num_rows($r);
		for ($i=1;$i<=$n;$i++){
			$id=$db->result($r,$i-1,"$field_id");
			$caption=$db->result($r,$i-1,"$field");
			$sel="";if ($id==$sel_id){$sel=" selected='selected'";}
			$list.="<option value='$id' $sel>$caption</option>";
		}
		return $list;
	}
	
	function showSelectSubListDBM($table,$prnt_field,$prnt_id,$field_id,$field,$sel_id){$db=DbSingleton::getDb();$list="<option value='0'></option>";
		$where=" and `$prnt_field`='$prnt_id'";//if ($prnt_id==0){$where="";}
		$r=$db->query("select `$field_id`,`$field` from `$table` where 1 $where order by `$field` asc;");$n=$db->num_rows($r);
		for ($i=1;$i<=$n;$i++){
			$id=$db->result($r,$i-1,"$field_id");
			$caption=$db->result($r,$i-1,"$field");
			$sel="";if ($id==$sel_id){$sel=" selected='selected'";}
			$list.="<option value='$id' $sel>$caption</option>";
		}
		return $list;
	}
	
	function showTableField($table,$field_name,$field_id,$field_id_val){$db=DbSingleton::getDb();$name="";
		$r=$db->query("select `$field_name` from `$table` where `$field_id`='$field_id_val' limit 0,1;");$n=$db->num_rows($r);
		if ($n==1){	$name=$db->result($r,0,"$field_name");}
		return $name;
	}

	function showTableFieldDBT($table,$field_name,$field_id,$field_id_val){$db=DbSingleton::getTokoDb();$name="";
		$r=$db->query("select `$field_name` from `$table` where `$field_id`='$field_id_val' limit 0,1;");$n=$db->num_rows($r);
		if ($n==1){	$name=$db->result($r,0,"$field_name");}
		return $name;
	}
	
}
