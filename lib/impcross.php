<?php
function ImportCross($impfile) 
{
	$db=new db;
	$file = fopen($file, 'r');
	while ($row=fgetcsv($file)) {
		$value = "'".implode("','", $row)."'";
		$r=$db->query("INSERT INTO T2_BRANDS_KIND(KIND_ID,CAPTION) VALUES(".$value.")");
	}

}
?>
