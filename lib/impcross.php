<?php

function ImportCross($impfile) {
	$db=DbSingleton::getDb();
	$file = fopen($impfile, 'r');
	while ($row=fgetcsv($file)) {
		$value = "'".implode("','", $row)."'";
		$db->query("INSERT INTO T2_BRANDS_KIND(KIND_ID,CAPTION) VALUES(".$value.")");
	}
}

