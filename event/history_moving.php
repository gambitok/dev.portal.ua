<?php

	$form_htm=RD."/tpl/history_moving.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}

	include_once 'lib/history_moving_class.php';$history=new HistoryMovingClass;
	
	$form=str_replace("{history_range}",$history->loadHistoryList(),$form);
	$content=str_replace("{work_window}", $form, $content);


?>