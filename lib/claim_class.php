<?php

class Claim {

    public function showClaimList(): string
    {
        $db=DbSingleton::getDb();
        $list="";$clients=new clients;$cat=new catalogue;
        $r=$db->query("SELECT * FROM `clients_claim`;");
        $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $art_id=$db->result($r,$i-1,"art_id");
            [$article_nr_displ, , ,] = $cat->getArticleNrDisplBrand($art_id);
            $brand_id=$db->result($r,$i-1,"brand_id");$brand_name=$cat->getBrandName($brand_id);
            $count=$db->result($r,$i-1,"amount");
            $client_id=$db->result($r,$i-1,"client_id");$client=$clients->getClientNameById($client_id);
            $text=$db->result($r,$i-1,"text_ru");
            $state=$db->result($r,$i-1,"state"); $state=$this->getManualName($state);
            $list.="<tr style='cursor:pointer' onClick='showClaimCard(\"$id\")'>
                <td>$article_nr_displ</td>
                <td>$brand_name</td>
                <td>$count</td>
                <td>$client</td>
                <td>$text</td>
                <td>$state</td>
            </tr>";
        }
        return $list;
    }

    public function getManualName($id)
    {
        $db=DbSingleton::getDb();
        $r=$db->query("SELECT `mcaption` FROM `manual` WHERE `id`='$id';");
        $caption=$db->result($r,0,"mcaption");
        if ($caption==="") {
            $caption = "Не вибрано";
        }
        return $caption;
    }

    public function getClaimSelect($state): string
    {
        $db=DbSingleton::getDb();
        $list="";
        $r=$db->query("SELECT * FROM `manual` WHERE `key`='claim_state';");
        $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++) {
            $id = $db->result($r, $i - 1, "id");
            $mcaption = $db->result($r, $i - 1, "mcaption");
            if ((int)$state===(int)$id) {
                $sel = "selected";
            } else {
                $sel = "";
            }
            $list.="<option value='$id' $sel>$mcaption</option>";
        }
        return $list;
    }

    public function saveClaimCard($claim_id, $amount, $data, $supplier, $manufacturer, $state, $text_ru, $text_ua, $text_en): array
    {
        $db=DbSingleton::getDb();
        $answer=0; $err="Помилка збереження даних";
        if ($claim_id>0) {
            $db->query("UPDATE `clients_claim` SET 
            amount='$amount', `data`='$data', `supplier`='$supplier', `manufacturer`='$manufacturer', `state`='$state', `text_ru`='$text_ru', `text_ua`='$text_ua', `text_en`='$text_en' 
            WHERE `id`='$claim_id';");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    public function showClaimCard($claim_id)
    {
        $db=DbSingleton::getDb(); $clients=new clients; $cat=new catalogue; $sale_invoice=new sale_invoice;
        $form="";$form_htm=RD."/tpl/claim_card.htm";
        if (file_exists($form_htm)){ $form = file_get_contents($form_htm);}

        $r=$db->query("SELECT * FROM `clients_claim` WHERE `id`='$claim_id';");
        $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $client_id=$db->result($r,$i-1,"client_id");$client=$clients->getClientNameById($client_id);
            $art_id=$db->result($r,$i-1,"art_id");
            [$article_nr_displ, , ,] = $cat->getArticleNrDisplBrand($art_id);
            $brand_id=$db->result($r,$i-1,"brand_id");$brand_name=$cat->getBrandName($brand_id);
            $amount=$db->result($r,$i-1,"amount");
            $supplier=$db->result($r,$i-1,"supplier"); $supplier_list=$this->getSuppls($supplier);
            $manufacturer=$db->result($r,$i-1,"manufacturer"); $manufacturer_list=$this->getManufacturers($manufacturer);
            $client_invoice=$db->result($r,$i-1,"client_invoice"); $client_invoice_name=$sale_invoice->getSaleInvoiceName($client_invoice);
            $show_invoice="showSaleInvoiceCard('$client_invoice')";
            $comment=$db->result($r,$i-1,"comment");
            $receipt_doc=$db->result($r,$i-1,"receipt_doc");
            $kilo1=$db->result($r,$i-1,"kilo_from");
            $kilo2=$db->result($r,$i-1,"kilo_to"); $kilometers=$kilo2-$kilo1;
            $state=$db->result($r,$i-1,"state");
            $text_ru=$db->result($r,$i-1,"text_ru");
            $text_ua=$db->result($r,$i-1,"text_ua");
            $text_en=$db->result($r,$i-1,"text_en");
            $data=$db->result($r,$i-1,"data");

            $form=str_replace("{claim_id}",$id,$form);
            $form=str_replace("{claim_art}",$article_nr_displ,$form);
            $form=str_replace("{claim_brand}",$brand_name,$form);
            $form=str_replace("{claim_count}",$amount,$form);
            $form=str_replace("{claim_supplier}",$supplier_list,$form);
            $form=str_replace("{claim_manufacturer}",$manufacturer_list,$form);
            $form=str_replace("{claim_client}",$client,$form);
            $form=str_replace("{claim_client_invoice}",$client_invoice_name,$form);
            $form=str_replace("{claim_comment}",$comment,$form);
            $form=str_replace("{claim_receipt_doc}",$receipt_doc,$form);
            $form=str_replace("{claim_kilometers}",$kilometers,$form);
            $form=str_replace("{claim_state}",$this->getClaimSelect($state),$form);
            $form=str_replace("{claim_text_ru}",$text_ru,$form);
            $form=str_replace("{claim_text_ua}",$text_ua,$form);
            $form=str_replace("{claim_text_en}",$text_en,$form);
            $form=str_replace("{claim_data}",$data,$form);
            $form=str_replace("{show_invoice}",$show_invoice,$form);
        }

        return $form;
    }

    public function loadClaimAct($claim_id)
    {
        $db=DbSingleton::getDb();
        $form="";$form_htm=RD."/tpl/claim_card_act.htm";
        if (file_exists($form_htm)){ $form = file_get_contents($form_htm);}

        $r=$db->query("SELECT cc.*, ac.full_name as client_name, ad.address_fakt as client_address, ac.phone as client_phone 
        FROM `clients_claim` cc
            LEFT OUTER JOIN A_CLIENTS ac on ac.id=cc.client_id
            LEFT OUTER JOIN  A_CLIENT_DETAILS ad on (ad.client_id=cc.client_id and ad.main=1)
         WHERE cc.id='$claim_id';");
        $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $sto=$db->result($r,$i-1,"client_sto");
            $address=$db->result($r,$i-1,"client_address");
            $client=$db->result($r,$i-1,"client_name");
            $phone=$db->result($r,$i-1,"client_phone");
            $auto=$db->result($r,$i-1,"client_auto");
            $year=$db->result($r,$i-1,"client_year");
            $vin=$db->result($r,$i-1,"client_vin");
            $number=$db->result($r,$i-1,"client_number");
            $amount=$db->result($r,$i-1,"amount");
            $date1=$db->result($r,$i-1,"date_install");
            $date2=$db->result($r,$i-1,"date_deinstall");
            $kilo1=$db->result($r,$i-1,"kilo_from");
            $kilo2=$db->result($r,$i-1,"kilo_to");
            $text_ru=$db->result($r,$i-1,"text_ru");
            $state=$db->result($r,$i-1,"state");

            $form=str_replace("{claim_id}",$id,$form);
            $form=str_replace("{claim_sto}",$sto,$form);
            $form=str_replace("{claim_adress}",$address,$form);
            $form=str_replace("{claim_client}",$client,$form);
            $form=str_replace("{claim_phone}",$phone,$form);
            $form=str_replace("{claim_auto}",$auto,$form);
            $form=str_replace("{claim_year}",$year,$form);
            $form=str_replace("{claim_vin}",$vin,$form);
            $form=str_replace("{claim_number}",$number,$form);
            $form=str_replace("{claim_amount}",$amount,$form);
            $form=str_replace("{claim_date1}",$date1,$form);
            $form=str_replace("{claim_date2}",$date2,$form);
            $form=str_replace("{claim_kilo1}",$kilo1,$form);
            $form=str_replace("{claim_kilo2}",$kilo2,$form);
            $form=str_replace("{claim_text_ru}",$text_ru,$form);
            $form=str_replace("{claim_state}",$this->getClaimSelect($state),$form);
        }

        return $form;
    }

    public function getManufacturers($man_id): string
    {
        $db=DbSingleton::getTokoDb();
        $list="";
        $r=$db->query("SELECT * FROM `T2_MANUF`;");
        $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++) {
            $ID = $db->result($r, $i - 1, "ID");
            $NAME = $db->result($r, $i - 1, "NAME");
            if ((int)$ID===(int)$man_id) {
                $sel = "selected";
            } else {
                $sel = "";
            }
            $list.="<option value='$ID' $sel>$NAME</option>";
        }

        return $list;
    }

    public function getSuppls($suppl_id): string
    {
        $db=DbSingleton::getDb();
        $list="";
        $r=$db->query("SELECT ac.* from `A_CLIENTS` ac
            LEFT OUTER JOIN A_CLIENTS_CATEGORY cc on cc.client_id=ac.id
        WHERE cc.category_id=2;");
        $n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++) {
            $id = $db->result($r, $i - 1, "id");
            $name = $db->result($r, $i - 1, "name");
            if ((int)$id===(int)$suppl_id) {
                $sel = "selected";
            } else {
                $sel = "";
            }
            $list.="<option value='$id' $sel>$name</option>";
        }

        return $list;

    }

}