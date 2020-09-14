<?php
require_once (RD."/lib/claim_class.php");$claim=new Claim;
$form_htm=RD."/tpl/claim.htm";$form="";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
$form=str_replace("{claim_range}", $claim->showClaimList(), $form);
$content=str_replace("{work_window}", $form, $content);

//$hostname = '{imap.gmail.com:993/imap/ssl}INBOX';
////$username = 'gambitokgd@gmail.com';
////$password = 'gambit100323358';
////
/////* try to connect */
////$inbox = imap_open($hostname,$username,$password) or die('Cannot connect to Gmail: ' .imap_last_error());