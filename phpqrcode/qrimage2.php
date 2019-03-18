 <?php

    include('qrlib.php');
        
    $param = $_GET['url']; // remember to sanitize that - it is user input!
    
    // we need to be sure ours script does not output anything!!!
    // otherwise it will break up PNG binary!
    
    ob_start("callback");
    
    // here DB request or some processing
	$name="MYPARTS PRO";
	$phone="+380 (67) 166-21-25";
	$email="office@myparts.pro";
	$website="http://portal.myparts.pro/";
	
    $codeContents .= iconv("windows-1251","utf-8",$name)."\n";
	$codeContents .= iconv("windows-1251","utf-8",$phone)."\n";
	$codeContents .= iconv("windows-1251","utf-8",$email)."\n";
	$codeContents .= $website; 
	$codeContents .= $url; 
	
    
    // end of processing here
    $debugLog = ob_get_contents();
    ob_end_clean();
    
    // outputs image directly into browser, as PNG stream
    QRcode::png($codeContents,false, QR_ECLEVEL_L, 1, 1); 
?>