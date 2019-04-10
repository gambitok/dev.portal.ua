<?php

class Claim {
	
    function showClaimList() { $db=DbSingleton::getDb();$list="";
        $r=$db->query("select * from clients_claim;");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $art=$db->result($r,$i-1,"art");
            $brand=$db->result($r,$i-1,"brand");
            $count=$db->result($r,$i-1,"count");
            $client=$db->result($r,$i-1,"client");
            $text=$db->result($r,$i-1,"comment");
            $state=$db->result($r,$i-1,"state"); $state=$this->getClaimState($state);
            $function="showClaimCard(\"$id\")";
            $list.="<tr style='cursor:pointer' onClick='$function'>
                <td>$art</td>
                <td>$brand</td>
                <td>$count</td>
                <td>$client</td>
                <td>$text</td>
                <td>$state</td>
            </tr>";
        }
        return $list;
    }

    function getClaimState($id) {
        $id ? $cap="Устанавливался" : $cap="Не устанавливался";
        return $cap;
    }

    function saveClaimCard($claim_id,$art,$brand,$count,$date,$supplier,$manufacturer,$client,$client_invoice,$comment,$receipt_doc,$kilometers,$state,$text_ukr,$text_eng) { $db=DbSingleton::getDb();
        $db->query("update clients_claim SET art='$art', brand='$brand', count=$count, date=$date, supplier='$supplier', manufacturer='$manufacturer', client='$client', client_invoice='$client_invoice', comment='$comment', receipt_doc='$receipt_doc', kilometers=$kilometers, state=$state, text_ukr='$text_ukr', text_eng='$text_eng' 
        where id='$claim_id';");
        return true;
    }

    function showClaimCard($claim_id){ $db=DbSingleton::getDb();
        $form="";$form_htm=RD."/tpl/claim_card.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select * from clients_claim where id='$claim_id';");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $art=$db->result($r,$i-1,"art");
            $brand=$db->result($r,$i-1,"brand");
            $count=$db->result($r,$i-1,"count");
            $supplier=$db->result($r,$i-1,"supplier");
            $manufacturer=$db->result($r,$i-1,"manufacturer");
            $client=$db->result($r,$i-1,"client");
            $client_invoice=$db->result($r,$i-1,"client_invoice");
            $comment=$db->result($r,$i-1,"comment");
            $receipt_doc=$db->result($r,$i-1,"receipt_doc");
            $kilo1=$db->result($r,$i-1,"kilo_from"); $kilo2=$db->result($r,$i-1,"kilo_to"); $kilometers=$kilo2-$kilo1;
            $state=$db->result($r,$i-1,"state");
            $text_ukr=$db->result($r,$i-1,"text_ukr");
            $text_eng=$db->result($r,$i-1,"text_eng");
            $form=str_replace("{claim_id}",$id,$form);
            $form=str_replace("{claim_art}",$art,$form);
            $form=str_replace("{claim_brand}",$brand,$form);
            $form=str_replace("{claim_count}",$count,$form);
            $form=str_replace("{claim_supplier}",$supplier,$form);
            $form=str_replace("{claim_manufacturer}",$manufacturer,$form);
            $form=str_replace("{claim_client}",$client,$form);
            $form=str_replace("{claim_client_invoice}",$client_invoice,$form);
            $form=str_replace("{claim_comment}",$comment,$form);
            $form=str_replace("{claim_receipt_doc}",$receipt_doc,$form);
            $form=str_replace("{claim_kilometers}",$kilometers,$form);
            $form=str_replace("{claim_state}",$state,$form);
            $form=str_replace("{claim_text_ukr}",$text_ukr,$form);
            $form=str_replace("{claim_text_eng}",$text_eng,$form);
        }
        return $form;
    }

    function loadClaimAct($claim_id){ $db=DbSingleton::getDb();
        $form="";$form_htm=RD."/tpl/claim_card_act.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select * from clients_claim where id='$claim_id';");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $sto=$db->result($r,$i-1,"client_sto");
            $adress=$db->result($r,$i-1,"client_adress");
            $client=$db->result($r,$i-1,"client_name");
            $phone=$db->result($r,$i-1,"client_phone");
            $auto=$db->result($r,$i-1,"client_auto");
            $year=$db->result($r,$i-1,"client_year");
            $vin=$db->result($r,$i-1,"client_vin");
            $number=$db->result($r,$i-1,"client_number");
            $count=$db->result($r,$i-1,"count");
            $date1=$db->result($r,$i-1,"date_install");
            $date2=$db->result($r,$i-1,"date_deinstall");
            $kilo1=$db->result($r,$i-1,"kilo_from");
            $kilo2=$db->result($r,$i-1,"kilo_to");
            $comment=$db->result($r,$i-1,"comment");
            $state=$db->result($r,$i-1,"state");
            $form=str_replace("{claim_id}",$id,$form);
            $form=str_replace("{claim_sto}",$sto,$form);
            $form=str_replace("{claim_adress}",$adress,$form);
            $form=str_replace("{claim_client}",$client,$form);
            $form=str_replace("{claim_phone}",$phone,$form);
            $form=str_replace("{claim_auto}",$auto,$form);
            $form=str_replace("{claim_year}",$year,$form);
            $form=str_replace("{claim_vin}",$vin,$form);
            $form=str_replace("{claim_number}",$number,$form);
            $form=str_replace("{claim_count}",$count,$form);
            $form=str_replace("{claim_date1}",$date1,$form);
            $form=str_replace("{claim_date2}",$date2,$form);
            $form=str_replace("{claim_kilo1}",$kilo1,$form);
            $form=str_replace("{claim_kilo2}",$kilo2,$form);
            $form=str_replace("{claim_comment}",$comment,$form);
            $form=str_replace("{claim_state}",$state,$form);
        }
        return $form;
    }

    function closeClaimCard($claim_id){
        $answer=1;
        return $answer;
    }
	
}