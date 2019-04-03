<?php

class buh_invoice {

    function getMediaUserName($user_id){$db=DbSingleton::getDb();$name="";
        $r=$db->query("select name from media_users where id='$user_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$name=$db->result($r,0,"name");}
        return $name;
    }

    function show_sale_invoice_list(){$db=DbSingleton::getDb();$sales=[];$list="";$gmanual=new gmanual;
        $form="";$form_htm=RD."/tpl/buh_invoice_range.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $data_cur=date("Y-m-d"); $summ_uah=$summ_usd=$summ_eur=0;
        $where=" and sv.time_stamp>='$data_cur 00:00:00' and sv.time_stamp<='$data_cur 23:59:59'";
        $r=$db->query("select sv.*, dp.prefix as dp_prefix, dp.doc_nom as dp_nom, t.name as tpoint_name, sl.name as seller_name, cl.name as client_name, dt.mvalue as doc_type_name,ch.abr2 as cash_abr from J_SALE_INVOICE sv
            left outer join J_DP dp on dp.id=sv.dp_id
            left outer join CASH ch on ch.id=sv.cash_id
            left outer join T_POINT t on t.id=sv.tpoint_id
            left outer join A_CLIENTS sl on sl.id=sv.seller_id
            left outer join A_CLIENTS cl on cl.id=sv.client_conto_id
            left outer join manual dt on dt.key='client_sale_type' and dt.id=sv.doc_type_id
        where sv.status=1 $where and sv.doc_type_id=61 order by sv.time_stamp desc, sv.status_invoice asc, sv.data_create desc, sv.prefix asc, sv.id desc;");$n=$db->num_rows($r);

        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id"); array_push($sales,$id);
            $dp_nom=$db->result($r,$i-1,"dp_prefix").$db->result($r,$i-1,"dp_nom");
            $prefix=$db->result($r,$i-1,"prefix");
            $doc_nom=$db->result($r,$i-1,"doc_nom");
            $time_stamp=$db->result($r,$i-1,"time_stamp");
            $tpoint_name=$db->result($r,$i-1,"tpoint_name");
            $seller_name=$db->result($r,$i-1,"seller_name");
            $client_name=$db->result($r,$i-1,"client_name");
            $doc_type_name=$db->result($r,$i-1,"doc_type_name");
            $summ=$db->result($r,$i-1,"summ");
            $summ_debit=$db->result($r,$i-1,"summ_debit");
            $cash_id=$db->result($r,$i-1,"cash_id");
            $cash_abr=$db->result($r,$i-1,"cash_abr");
            $data_pay=$db->result($r,$i-1,"data_pay");
            $user_name=$this->getMediaUserName($db->result($r,$i-1,"user_id"));
            $status_select=$db->result($r,$i-1,"status_select");
            $status_select_cap=$gmanual->get_gmanual_caption($status_select);

            if ($cash_id==1) $summ_uah+=$summ; if ($cash_id==2) $summ_usd+=$summ; if ($cash_id==3) $summ_eur+=$summ;
            if ($summ_debit==0) $summ_cap=""; else $summ_cap="$summ_debit $cash_abr";
            $summ_pdv=round($summ/6,2);

            $list.="<tr id='strStsRow_$i' style='cursor:pointer' align='center' onClick='showSaleInvoiceCard(\"$id\");'>
                <td >$i</td>
                <td>$prefix-$doc_nom</td>
                <td align='center'>$time_stamp</td>
                <td>$dp_nom</td>
                <td>$tpoint_name</td>
                <td align='left'>$seller_name</td>
                <td align='left'>$client_name</td>
                <td>$doc_type_name</td>
                <td align='center' style='min-width:80px;'>$summ $cash_abr</td>
                <td align='right'>$summ_cap</td>
                <td align='right'>$summ_pdv $cash_abr</td>
                <td align='right'>$data_pay</td>
                <td align='left'>$user_name</td>
                <td align='center'>$status_select_cap</td>
            </tr>";
        }
        $form=str_replace("{sale_invoice_range}",$list,$form);
        $form=str_replace("{sale_invoice_summ}","$summ_uah UAH / $summ_usd USD / $summ_eur EUR",$form);
        return $form;
    }

    function show_sale_invoice_list_filter($data_start,$data_end){$db=DbSingleton::getDb();$gmanual=new gmanual;$list="";
        $data_cur=date("Y-m-d"); $summ_uah=$summ_usd=$summ_eur=0;
        if ($data_start!='' && $data_end!='') $where=" and sv.time_stamp>='$data_start 00:00:00' and sv.time_stamp<='$data_end 23:59:59' and sv.doc_type_id=61"; else
        $where=" and sv.time_stamp>='$data_cur 00:00:00' and sv.time_stamp<='$data_cur 23:59:59' and sv.doc_type_id=61";
        $r=$db->query("select sv.*, dp.prefix as dp_prefix, dp.doc_nom as dp_nom, t.name as tpoint_name, sl.name as seller_name, cl.name as client_name, dt.mvalue as doc_type_name,ch.abr2 as cash_abr 
        from J_SALE_INVOICE sv
            left outer join J_DP dp on dp.id=sv.dp_id
            left outer join CASH ch on ch.id=sv.cash_id
            left outer join T_POINT t on t.id=sv.tpoint_id
            left outer join A_CLIENTS sl on sl.id=sv.seller_id
            left outer join A_CLIENTS cl on cl.id=sv.client_conto_id
            left outer join manual dt on dt.key='client_sale_type' and dt.id=sv.doc_type_id
        where sv.status=1 $where order by sv.time_stamp desc, sv.status_invoice asc, sv.data_create desc, sv.prefix asc, sv.id desc;"); $n=$db->num_rows($r);

        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $dp_nom=$db->result($r,$i-1,"dp_prefix").$db->result($r,$i-1,"dp_nom");
            $prefix=$db->result($r,$i-1,"prefix");
            $doc_nom=$db->result($r,$i-1,"doc_nom");
            $time_stamp=$db->result($r,$i-1,"time_stamp");
            $tpoint_name=$db->result($r,$i-1,"tpoint_name");
            $seller_name=$db->result($r,$i-1,"seller_name");
            $client_name=$db->result($r,$i-1,"client_name");
            $doc_type_name=$db->result($r,$i-1,"doc_type_name");
            $summ=$db->result($r,$i-1,"summ");
            $summ_debit=$db->result($r,$i-1,"summ_debit");
            $cash_id=$db->result($r,$i-1,"cash_id");
            $cash_abr=$db->result($r,$i-1,"cash_abr");
            $data_pay=$db->result($r,$i-1,"data_pay");
            $user_name=$this->getMediaUserName($db->result($r,$i-1,"user_id"));
            $status_select=$db->result($r,$i-1,"status_select");
            $status_select_cap=$gmanual->get_gmanual_caption($status_select);

            if ($cash_id==1) $summ_uah+=$summ; if ($cash_id==2) $summ_usd+=$summ; if ($cash_id==3) $summ_eur+=$summ;
            if ($summ_debit==0) $summ_cap=""; else $summ_cap="$summ_debit $cash_abr";
            $summ_pdv=round($summ/6,2);

            $list.="<tr id='strStsRow_$i' style='cursor:pointer' align='center' onClick='showSaleInvoiceCard(\"$id\");'>
                <td>$i</td>
                <td>$prefix-$doc_nom</td>
                <td align='center'>$time_stamp</td>
                <td>$dp_nom</td>
                <td>$tpoint_name</td>
                <td align='left'>$seller_name</td>
                <td align='left'>$client_name</td>
                <td>$doc_type_name</td>
                <td align='center' style='min-width:80px;'>$summ $cash_abr</td>
                <td align='right'>$summ_cap</td>
                <td align='right'>$summ_pdv $cash_abr</td>
                <td align='right'>$data_pay</td>
                <td align='left'>$user_name</td>
                <td align='center'>$status_select_cap</td>
            </tr>";
        }
        $summ_price="$summ_uah UAH / $summ_usd USD / $summ_eur EUR";
        return array($list,$summ_price);
    }

    function getKoursData(){$db=DbSingleton::getDb();$slave=new slave;$usd_to_uah=0;$eur_to_uah=0;
        $r=$db->query("select kours_value from J_KOURS where cash_id='2' and in_use='1' order by id desc limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$usd_to_uah=$slave->to_money(round($db->result($r,0,"kours_value"),2));}
        $r=$db->query("select kours_value from J_KOURS where cash_id='3' and in_use='1' order by id desc limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$eur_to_uah=$slave->to_money(round($db->result($r,0,"kours_value"),2));}
        return array($usd_to_uah,$eur_to_uah);
    }

    function getSaleInvoiceName($id){$db=DbSingleton::getDb();$name="";
        $r=$db->query("select * from J_SALE_INVOICE where status=1 and id='$id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){ $name=$db->result($r,0,"prefix")."-".$db->result($r,0,"doc_nom"); }
        return $name;
    }

    function getJPayName($id){$db=DbSingleton::getDb();$name="";$pay_type_id=0;
        $r=$db->query("select p.*, m.mcaption as pay_type_name from J_PAY p left outer join manual m on (m.id=p.pay_type_id and m.`key`='pay_type_id') where p.status=1 and p.id='$id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){ $pay_type_id=$db->result($r,0,"pay_type_id"); $name=$db->result($r,0,"pay_type_name")." №".$db->result($r,0,"doc_nom"); }
        return array($pay_type_id,$name);
    }

    function checkTaxExist($invoice_id){$db=DbSingleton::getDb();$tax_id=0;
        $r=$db->query("select id from J_TAX_INVOICE where sale_invoice_id='$invoice_id' and status=1 limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){ $tax_id=$db->result($r,0,"id"); }
        return $tax_id;
    }

    function createTaxInvoice($invoice_id){$db=DbSingleton::getDb();$cat=new catalogue; session_start();$user_id=$_SESSION["media_user_id"];$tax_id=0;$answer=0;$err="Помилка!";
        $r=$db->query("select * from J_SALE_INVOICE where id='$invoice_id' limit 0,1"); $n=$db->num_rows($r);
        if ($n==1){
            $seller_id=$db->result($r,0,"seller_id");
            $client_id=$db->result($r,0,"client_conto_id");
            $tpoint_id=$db->result($r,0,"tpoint_id");
            //$data_create=$db->result($r,0,"data_create");
            $cash_id=$db->result($r,0,"cash_id");
            $summ=$db->result($r,0,"summ");

            $rt=$db->query("select max(id) as mid from J_TAX_INVOICE;");$tax_id=0+$db->result($rt,0,"mid")+1;
            $rt=$db->query("select max(doc_nom) as mid from J_TAX_INVOICE where seller_id='$seller_id';");$tax_nom=0+$db->result($rt,0,"mid")+1;
            $db->query("insert into J_TAX_INVOICE (`id`,`tax_type_id`,`doc_nom`,`data_create`,`sale_invoice_id`,`tpoint_id`,`seller_id`,`client_id`,`cash_id`,`summ`,`user_id`) values ('$tax_id','160','$tax_nom',CURDATE(),'$invoice_id','$tpoint_id','$seller_id','$client_id','$cash_id','$summ','$user_id');");

            $r1=$db->query("select * from J_SALE_INVOICE_STR where invoice_id='$invoice_id' order by id asc;");$n1=$db->num_rows($r1);
            for ($i=1;$i<=$n1;$i++){
                $art_id=$db->result($r1,$i-1,"art_id");
                //$article_nr_displ=$db->result($r1,$i-1,"article_nr_displ");
                $amount=$db->result($r1,$i-1,"amount");
                $price=$db->result($r1,$i-1,"price_end");
                $summ=$db->result($r1,$i-1,"summ");
                $zed=$cat->getArticleZED($art_id);$art_name=$cat->getArticleNameLang($art_id);

                $db->query("insert into J_TAX_INVOICE_STR (`tax_id`,`zed`,`art_id`,`goods_name`,`amount`,`price`,`summ`) values ('$tax_id','$zed','$art_id','$art_name','$amount','$price','$summ');");
            }
            $answer=1;$err="";
        }
        return array($answer,$err,$tax_id);
    }

    function showSaleInvoiceCard($invoice_id){$db=DbSingleton::getDb();$cat=new catalogue;$list="";$prefix="";$doc_nom=0;
        $form="";$form_htm=RD."/tpl/sale_invoice_card.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select sv.*, t.name as tpoint_name, sl.name as seller_name, cl.name as client_name, dt.mcaption as doc_type_name, dt.mvalue as doc_type_abr,ch.abr2 as cash_abr 
        from J_SALE_INVOICE sv
            left outer join CASH ch on ch.id=sv.cash_id
            left outer join T_POINT t on t.id=sv.tpoint_id
            left outer join A_CLIENTS sl on sl.id=sv.seller_id
            left outer join A_CLIENTS cl on cl.id=sv.client_conto_id
            left outer join manual dt on dt.key='client_sale_type' and dt.id=sv.doc_type_id
        where sv.status=1 and sv.id='$invoice_id' limit 0,1;");$n=$db->num_rows($r);

        if ($n==0){$form_htm=RD."/tpl/access_deny.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);} }
        if ($n==1){
            $prefix=$db->result($r,0,"prefix");
            $doc_nom=$db->result($r,0,"doc_nom");
            $data_create=$db->result($r,0,"data_create");
            //$tpoint_id=$db->result($r,0,"tpoint_id");
            $tpoint_name=$db->result($r,0,"tpoint_name");
            //$seller_id=$db->result($r,0,"seller_id");
            $seller_name=$db->result($r,0,"seller_name");
            //$client_id=$db->result($r,0,"client_id");
            $client_name=$db->result($r,0,"client_name");
            $doc_type_id=$db->result($r,0,"doc_type_id");
            $doc_type_name=$db->result($r,0,"doc_type_name");
            //$doc_type_abr=$db->result($r,0,"doc_type_abr");
            $summ=$db->result($r,0,"summ");
            //$cash_id=$db->result($r,0,"cash_id");
            $cash_abr=$db->result($r,0,"cash_abr");
            $data_pay=$db->result($r,0,"data_pay");
            //$user_name=$this->getMediaUserName($db->result($r,0,"user_id"));
            //$status_invoice=$db->result($r,0,"status_invoice");
            //$status_invoice_cap=$gmanual->get_gmanual_caption($status_invoice);

            //$usd_to_uah=$db->result($r,0,"usd_to_uah");
            //$eur_to_uah=$db->result($r,0,"eur_to_uah");
            $volume=0;//???

            //list($usd_to_uah_new,$eur_to_uah_new)=$this->getKoursData();
            //if($usd_to_uah!=$usd_to_uah_new){$usd_to_uah=$usd_to_uah_new;}
            //if($eur_to_uah!=$eur_to_uah_new){$eur_to_uah=$eur_to_uah_new;}

            $form=str_replace("{invoice_id}",$invoice_id,$form);
            $form=str_replace("{data}",$data_create,$form);
            $form=str_replace("{data_pay}",$data_pay,$form);
            $form=str_replace("{prefix}",$prefix,$form);
            $form=str_replace("{doc_nom}",$doc_nom,$form);
            $form=str_replace("{tpoint_name}",$tpoint_name,$form);
            $form=str_replace("{seller_name}",$seller_name,$form);
            $form=str_replace("{client_name}",$client_name,$form);
            $form=str_replace("{doc_type_name}",$doc_type_name,$form);
            $form=str_replace("{invoice_summ}",$summ,$form);
            $form=str_replace("{cash_name}",$cash_abr,$form);
            $form=str_replace("{volume}",$volume,$form);

            $tax_hidden=" hidden";
            if ($doc_type_id=="61") {$form=str_replace("{oper_visible}","",$form);
                $tax_hidden=""; if ($this->checkTaxExist($invoice_id)>0){
                    $tax_hidden=" hidden";
                }
            }else
            {$form=str_replace("{oper_visible}"," disabled style=\"display:none;\"",$form); }

            if ($doc_type_id==64) {$style_doc_id="style='display:none;'";} else {$style_doc_id="";}
            $form=str_replace("{style_doc_id}",$style_doc_id,$form);
            $form=str_replace("{hidden_tax}",$tax_hidden,$form);

            $r=$db->query("select * from J_SALE_INVOICE_STR where invoice_id='$invoice_id' order by id asc;");$n=$db->num_rows($r);
            for ($i=1;$i<=$n;$i++){
                //$art_id=$db->result($r,$i-1,"art_id");
                $article_nr_displ=$db->result($r,$i-1,"article_nr_displ");
                $brand_id=$db->result($r,$i-1,"brand_id");$brand_name=$cat->getBrandName($brand_id);
                $amount=$db->result($r,$i-1,"amount");
                $price=$db->result($r,$i-1,"price");
                $price_end=$db->result($r,$i-1,"price_end");
                $discount=$db->result($r,$i-1,"discount");
                $summ=$db->result($r,$i-1,"summ");

                $list.="<tr align='right'>
                    <td align='left'>$i</td>
                    <td align='left'>$article_nr_displ</td>
                    <td align='center'>$brand_name</td>
                    <td>$amount</td>
                    <td>$price</td>
                    <td>$discount</td>
                    <td>$price_end</td>
                    <td>$summ</td>
                </tr>";
            }
            $form=str_replace("{sale_invoice_str_list}",$list,$form);
        }
        return array($form,"$prefix-$doc_nom");
    }

    function getClientCashConditions($client_id){$db=DbSingleton::getDb();$cash_id=0;$credit_cash_id=0;
        $r=$db->query("select cash_id,credit_cash_id from A_CLIENTS_CONDITIONS where client_id ='$client_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $cash_id=$db->result($r,0,"cash_id");
            $credit_cash_id=$db->result($r,0,"credit_cash_id");
        }
        return array($cash_id,$credit_cash_id);
    }

    function getClientOrgType($client_id){$db=DbSingleton::getDb();$org_type=0;
        $r=$db->query("select org_type from A_CLIENTS where id ='$client_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$org_type=$db->result($r,0,"org_type");}
        return $org_type;
    }

    function showCategoryCheckList($dp_id){$db=DbSingleton::getDb();$list="";
        $r=$db->query("select  * from A_CATEGORY where parrent_id=0 order by id asc;");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"name");
            $sel=$this->checkdpCategorySelect($dp_id,$id);
            $ch="";if ($sel==1){$ch=" checked=''";}
            $list.="<label><input type='checkbox' class='i-checks' id='c_category_$i' value='$id' $ch> - $name;</label> ";
        }$list.="<input type='hidden' id='c_category_kol' value='$n'>";
        return $list;
    }

    function checkdpCategorySelect($dp_id,$category_id){$db=DbSingleton::getDb();$ch=0;
        $r=$db->query("select category_id from A_CLIENTS_CATEGORY where dp_id='$dp_id' and category_id='$category_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$ch=1;}
        return $ch;
    }

    function showMovingOpListSelect($sel_id){$db=DbSingleton::getDb();$list="";
        $r=$db->query("select * from J_DP_OP where in_show='1' order by id asc;");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"name");
            $sel="";if ($sel_id==$id){$sel="selected='selected'";}
            $list.="<option value='$id' $sel>$name</option>";
        }
        return $list;
    }

    function showdpDocumentList($dp_id,$dp_op_id,$document_id){$income=new income;$document_list="";$form="";
        if ($dp_op_id==1){
            $form_htm=RD."/tpl/dp_documents_list.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
            $document_list=$income->search_documents_income_list("");
        }
        $form=str_replace("{documents_list}",$document_list,$form);

        $form=str_replace("{dp_id}",$dp_id,$form);
        $form=str_replace("{dp_op_id}",$dp_op_id,$form);

        return array($form,"Реєстр документів основи");
    }

    function finddpDocumentsSearch($dp_id,$dp_op_id,$s_nom){$income=new income;$document_list="";
        if ($dp_op_id==1){$document_list=$income->search_documents_income_list($s_nom);}
        return $document_list;
    }

    function getArtIdByBarcode($barcode){$db=DbSingleton::getTokoDb();$art_id=0;
        $r=$db->query("select ART_ID from T2_BARCODES where BARCODE='$barcode' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){	$art_id=$db->result($r,0,"ART_ID");	}
        return $art_id;
    }

    function getArtId($code,$brand_id){$db=DbSingleton::getTokoDb();$slave=new slave;$cat=new catalogue;$id=0; $code=$slave->qq($code); $code=$cat->clearArticle($code);
        $r=$db->query("select ART_ID from T2_ARTICLES where ARTICLE_NR_SEARCH='$code' and BRAND_ID='$brand_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){	$id=$db->result($r,0,"ART_ID");	}
        return $id;
    }

    function getCostumsId($code){$db=DbSingleton::getTokoDb();$slave=new slave;$id=0; $code=$slave->qq($code);
        $r=$db->query("select COSTUMS_ID from T2_COSTUMS where COSTUMS_CODE='$code' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){	$id=$db->result($r,0,"COSTUMS_ID");	}
        return $id;
    }

    function getCountryId($code){$db=DbSingleton::getTokoDb();$slave=new slave;$id=0; $code=$slave->qq($code);
        $r=$db->query("select COUNTRY_ID from T2_COUNTRIES where COUNTRY_NAME='$code' or `ALFA2`='$code' or `ALFA3`='$code' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){	$id=$db->result($r,0,"COUNTRY_ID");	}
        return $id;
    }

    function getBrandId($code){$db=DbSingleton::getTokoDb();$slave=new slave;$id=0; $code=$slave->qq($code);
        $r=$db->query("select BRAND_ID from T2_BRANDS where BRAND_NAME='$code' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){	$id=$db->result($r,0,"BRAND_ID");	}
        return $id;
    }

    function getBrandName($id){$db=DbSingleton::getTokoDb();$name="";
        $r=$db->query("select BRAND_NAME from T2_BRANDS where BRAND_ID='$id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){	$name=$db->result($r,0,"BRAND_NAME");	}
        return $name;
    }

    function getTpointName($id){$db=DbSingleton::getDb();$name="";
        $r=$db->query("select name from T_POINT where id='$id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){	$name=$db->result($r,0,"name");	}
        return $name;
    }

    function getTpointFullName($id){$db=DbSingleton::getDb();$name="";
        $r=$db->query("select full_name from T_POINT where id='$id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){	$name=$db->result($r,0,"full_name");	}
        return $name;
    }

    function getClientName($id){$db=DbSingleton::getDb();$name="";
        $r=$db->query("select name from A_CLIENTS where id='$id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){	$name=$db->result($r,0,"name");	}
        return $name;
    }

    function showWorkPairForm($dp_id){$db=DbSingleton::getDb();$list="";
        $r=$db->query("select PAIR_INDEX from T2_WORK_PAIR where ART_ID='$dp_id';");$n=$db->num_rows($r);
        for ($i=1;$i<=$n+3;$i++){
            $pair_index="";
            if ($i<=$n){$pair_index=$db->result($r,$i-1,"PAIR_INDEX");}
            $list.="<tr><td><input type='text' id='work_pair_$i' value='$pair_index' class='form-control'></td></tr>";
        }$list.="<input type='hidden' id='work_pair_n' value='".($n+3)."'>";
        return $list;
    }

    function labelArtEmptyCount($dp_id,$kol){$label="";
        if ($kol==0 || $kol==""){
            list($weight,$volume,$kol)=$this->updateDpWeightVolume($dp_id);
        }
        if ($kol>0){$label="<span class='label label-tab label-info'>$kol</span>";}
        return array($kol,$label);
    }

    function labelCommentsCount($dp_id){$db=DbSingleton::getDb();$label="";
        $r=$db->query("select count(id) as kol from J_DP_COMMENTS where dp_id='$dp_id';");$kol=0+$db->result($r,0,"kol");
        if ($kol>0){$label="<span class='label label-tab label-info'>$kol</span>";}
        return array($kol,$label);
    }

    function loaddpUnknownArticles($dp_id){$db=DbSingleton::getDb();
        $form="";$form_htm=RD."/tpl/dp_unknown_articles_list.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select * from J_DP j where j.id='$dp_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==0){$form_htm=RD."/tpl/access_deny.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);} }
        if ($n==1){
            list($list,$kol_rows)=$this->showdpUnknownStrList($dp_id);
            $form=str_replace("{UnknownArticlesList}",$list,$form);
            $form=str_replace("{kol_rows}",$kol_rows,$form);
            $form=str_replace("{dp_id}",$dp_id,$form);
        }
        return $form;
    }

    //======================================================================================

    function getSellerId($tpoint_id,$doc_type_id){$db=DbSingleton::getDb();$seller_id=0;
        //$sale_type=array(61=>86, 62=>87, 63=>87, 64=>88);
        //print "tpoint_id=$tpoint_id; doc_type_id=".$doc_type_id."; \n sale_type=".$sale_type[$doc_type_id];
        $r=$db->query("select `client_id` from T_POINT_CLIENTS where tpoint_id='$tpoint_id' and sale_type='$doc_type_id' and in_use='1' and status='1' order by id asc limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$seller_id=$db->result($r,0,"client_id");}
        return $seller_id;
    }

    function getSellerPrefixDocNom($seller_id,$doc_type_id){$db=DbSingleton::getDb();$prefix="";
        $sale_type=array(61=>86, 62=>87, 63=>87, 64=>88);$sale_type_id=$sale_type[$doc_type_id];
        $r=$db->query("select `prefix` from A_CLIENTS_DOCUMENT_PREFIX where client_id='$seller_id' and doc_type_id='$sale_type_id' and status='1' order by id asc limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$prefix=$db->result($r,0,"prefix");}
        $prefix=str_replace("{year}",date("Y"),$prefix);
        $prefix=str_replace("{month}",date("m"),$prefix);
        $prefix=str_replace("{day}",date("d"),$prefix);
        $prefix=str_replace("{rnd010}",rand(0,10),$prefix);
        $r=$db->query("select IFNULL( max( doc_nom ) , 0 ) AS doc_nom from J_SALE_INVOICE where seller_id='$seller_id' and doc_type_id='$doc_type_id' and status='1';");$doc_nom=0+$db->result($r,0,"doc_nom")+1;
        return array($prefix,$doc_nom);
    }

    function updateClientBalans($client_conto_id,$cash_id,$summ){$db=DbSingleton::getDb();
        $r=$db->query("select * from B_CLIENT_BALANS where client_id='$client_conto_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==0){
            $db->query("insert into B_CLIENT_BALANS (`client_id`,`cash_id`) values ('$client_conto_id','$cash_id');");$n=1;
        }
        if ($n==1){
            $db->query("update B_CLIENT_BALANS set saldo=saldo-'$summ', cash_id='$cash_id', last_update=NOW() where client_id='$client_conto_id';");
        }
        return;
    }

    //===============			PARTIOTIONS 	==================================

    function loadSaleInvoicePartitions($invoice_id){$db=DbSingleton::getDb();$income=new income;$prev_doc_id=0;$list="";$doc_name=0;
        $form="";$form_htm=RD."/tpl/sale_invoice_partitions_list.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select ps.*, ap.parrent_type_id, ap.parrent_doc_id from J_SALE_INVOICE_PARTITION_STR ps
            left outer join T2_ARTICLES_PARTITIONS ap on (ap.id=ps.partition_id)
        where ps.status=1 and ps.invoice_id='$invoice_id' order by ps.id asc;");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            if ($i==1){$doc_name="";}
            $parrent_type_id=$db->result($r,$i-1,"parrent_type_id");
            $parrent_doc_id=$db->result($r,$i-1,"parrent_doc_id");
            $article_nr_displ=$db->result($r,$i-1,"article_nr_displ");
            $brand_id=$db->result($r,$i-1,"brand_id");
            $partition_amount=$db->result($r,$i-1,"partition_amount");
            $oper_price_partition=$db->result($r,$i-1,"oper_price_partition");
            $price_partition=$db->result($r,$i-1,"price_partition");
            $price_buh_uah=$db->result($r,$i-1,"price_buh_uah");
            $price_man_uah=$db->result($r,$i-1,"price_man_uah");
            $price_invoice=$db->result($r,$i-1,"price_invoice");
            if ($parrent_type_id==1){
                if ($parrent_doc_id!=$prev_doc_id){
                    $doc_name="".$income->getIncomeDocNom($parrent_doc_id);
                    $prev_doc_id=$parrent_doc_id;
                }
            }

            $list.="<tr id='strStsRow_$i'>
                <td align='center'>$i</td>
                <td align='center'>$doc_name</td>
                <td align='center'>$article_nr_displ</td>
                <td align='center'>$brand_id</td>
                <td align='right'>$partition_amount</td>
                <td align='right'>$price_partition</td>
                <td align='right'>$oper_price_partition</td>
                <td align='right'>$price_buh_uah</td>
                <td align='right'>$price_man_uah</td>
                <td align='right'>$price_invoice</td>
            </tr>";
        }
        if ($n==0){$list="<tr><td colspan=8 align='center'>Записи відсутні</td></tr>";}
        $form=str_replace("{partitions_list}",$list,$form);
        $form=str_replace("{invoice_id}",$invoice_id,$form);
        return $form;
    }

    //===============			MONEY PAY 		==================================

    function loadSaleInvoiceMoneyPay($invoice_id){$db=DbSingleton::getDb();$list="";
        $form="";$form_htm=RD."/tpl/sale_invoice_money_pay_list.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select pay.*, pt.mcaption as pay_type_caption, pb.name as paybox_name, c.abr from J_PAY pay
            left outer join J_PAY_STR pst on pst.pay_id=pay.id
            left outer join CASH c on c.id=pay.cash_id
            left outer join T_POINT_PAY_BOX pb on pb.id=pay.paybox_id
            left outer join manual pt on (pt.key='pay_type_id' and pt.id=pay.pay_type_id)
        where pay.status=1 and pst.parrent_doc_id='$invoice_id' group by pay.id order by pay.data_time desc, pay.id desc;");$n=$db->num_rows($r);

        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $data_time=$db->result($r,$i-1,"data_time");
            $pay_type_caption=$db->result($r,$i-1,"pay_type_caption");
            $paybox_name=$db->result($r,$i-1,"paybox_name");
            $doc_nom=$db->result($r,$i-1,"doc_nom");
            $summ=$db->result($r,$i-1,"summ");
            $cash_name=$db->result($r,$i-1,"abr");
            $user_name=$this->getMediaUserName($db->result($r,$i-1,"user_id"));

            $list.="<tr id='strStsRow_$i' onClick='viewDpMoneyPay(\"$invoice_id\",\"$id\");'>
                <td align='center'>$i</td>
                <td align='center'>$data_time</td>
                <td>$pay_type_caption</td>
                <td align='center' style='min-width:140px;'>$doc_nom</td>
                <td align='center' style='min-width:140px;'>$paybox_name</td>
                <td align='right' style='min-width:120px;'>$summ $cash_name</td>
                <td>$user_name</td>
            </tr>";
        }
        if ($n==0){$list="<tr><td colspan=7 align='center'>Документи оплати відсутні</td></tr>";}
        $form=str_replace("{money_pay_list}",$list,$form);
        $form=str_replace("{invoice_id}",$invoice_id,$form);
        return $form;
    }

    function showSaleInvoceMoneyPayForm($invoice_id,$pay_id){$db=DbSingleton::getDb();$gmanual=new gmanual;$cash_kours="";
        $form="";$form_htm=RD."/tpl/sale_invoice_money_pay_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("select * from J_SALE_INVOICE where id='$invoice_id' limit 0,1;");
        $cash_id=$db->result($r,0,"cash_id");$cash_name=$this->getCashAbr($cash_id);
        $tpoint_id=$db->result($r,0,"tpoint_id");
        $seller_id=$db->result($r,0,"seller_id");
        $summ=$db->result($r,0,"summ");
        $summ_debit=$db->result($r,0,"summ_debit");
        $summ_kredit=$summ-($summ-$summ_debit);

        if ($pay_id==0){$print_pay_disabled="disabled"; $cash_kours="1";}else {$print_pay_disabled="";}
        $form=str_replace("{invoice_id}",$invoice_id,$form);
        $form=str_replace("{pay_id}",$pay_id,$form);
        $form=str_replace("{doc_cash_id}",$cash_id,$form);
        $form=str_replace("{cash_name}",$cash_name,$form);
        $form=str_replace("{print_pay_disabled}",$print_pay_disabled,$form);
        $form=str_replace("{sale_invoice_summ}",$summ,$form);
        $form=str_replace("{sale_invoice_debit}",$summ_debit,$form);
        $form=str_replace("{sale_invoice_kredit}",$summ_kredit,$form);
        $form=str_replace("{cash_kours}",$cash_kours,$form);
        $form=str_replace("{paybox_list}",$this->showTpointPayBoxSelectList($seller_id,$tpoint_id),$form);
        $form=str_replace("{pay_type_list}",$gmanual->showGmanualSelectList('pay_type_id','89'),$form);
        $form=str_replace("{cash_list}",$this->showCashListSelect($cash_id),$form);
        $form=str_replace("{pay_type_id_disabled}","disabled",$form);
        return $form;
    }

    function getCashAbr($cash_id){$db=DbSingleton::getDb();$name="";
        $r=$db->query("select abr from CASH where id ='$cash_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$name=$db->result($r,0,"abr");}
        return $name;
    }

    function getCashName($cash_id){$db=DbSingleton::getDb();$name="";
        $r=$db->query("select name from CASH where id ='$cash_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$name=$db->result($r,0,"name");}
        return $name;
    }

    function showCashListSelect($sel_id,$ns=""){$db=DbSingleton::getDb();if ($ns==""){$ns=1;}
        $r=$db->query("select * from CASH order by name asc;");$n=$db->num_rows($r);$list="";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"abr");
            if ($ns==2){ $name=$db->result($r,$i-1,"name");}
            $sel="";if ($sel_id==$id){$sel="selected='selected'";}
            $list.="<option value='$id' $sel>$name</option>";
        }
        return $list;
    }

    function unlockSaleInvoiceMoneyPayKours($invoice_id,$pay_id){session_start();$user_id=$_SESSION["media_user_id"];$answer=0;$err="Помилка збереження даних!";
        if ($invoice_id==0 || $invoice_id==""){
            $err="Не вказано номер накладної для оплати";$answer=0;
        }
        if ($invoice_id>0 && $pay_id==0){
            if ($user_id==1){
                $err="";$answer=1;
            }else{$err="Нашу гріх на душу брати! ";$answer=0;}
        }
        if ($invoice_id>0 && $pay_id>0){
            $err="Оплату проведено. Зміну курсу заблоковано";$answer=0;
        }
        return array($answer,$err);
    }

    function showTpointPayBoxSelectList($client_id,$tpoint_id){$db=DbSingleton::getDb(); $list="";
        $r=$db->query("select * from T_POINT_PAY_BOX where client_id='$client_id' and tpoint_id='$tpoint_id' order by name asc;");$n=$db->num_rows($r);
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"id");
            $name=$db->result($r,$i-1,"name");
            $list.="<option value='$id'";if ($i==1){$list.=" selected";}$list.=">$name</option>";
        }
        return $list;
    }

    function getCashKoursSaleInvoiceMoneyPay($doc_cash_id,$cash_id){$db=DbSingleton::getDb();$slave=new slave;$answer=0;$err="Помилка!";
        $cash_id=$slave->qq($cash_id);$kours_value=1;$kours_usd=$kours_eur=1;
        if ($cash_id==0 || $cash_id==""){ $err="Не вказано валюту";$answer=0; }
        if ($cash_id>0){
            if ($doc_cash_id==$cash_id){ $kours_value=1; $answer=1;$err="";}
            if ($doc_cash_id!=$cash_id){
                $r=$db->query("select kours_value from J_KOURS where cash_id='2' and in_use='1' limit 0,1;");$n=$db->num_rows($r);
                if ($n==1){ $kours_usd=$db->result($r,0,"kours_value");}
                $r=$db->query("select kours_value from J_KOURS where cash_id='3' and in_use='1' limit 0,1;");$n=$db->num_rows($r);
                if ($n==1){ $kours_eur=$db->result($r,0,"kours_value");}

                if ($doc_cash_id==1 && $cash_id==1){$kours_value=1;}
                if ($doc_cash_id==1 && $cash_id==2){$kours_value=$kours_usd;}
                if ($doc_cash_id==1 && $cash_id==3){$kours_value=$kours_eur;}

                if ($doc_cash_id==2 && $cash_id==1){$kours_value=$kours_usd;}
                if ($doc_cash_id==2 && $cash_id==2){$kours_value=1;}
                if ($doc_cash_id==2 && $cash_id==3){$kours_value=round($kours_eur/$kours_usd,2);}

                if ($doc_cash_id==3 && $cash_id==1){$kours_value=$kours_eur;}
                if ($doc_cash_id==3 && $cash_id==2){$kours_value=round($kours_eur/$kours_usd,2);}
                if ($doc_cash_id==3 && $cash_id==3){$kours_value=1;}

                $answer=1;$err="";
                if ($n==0){ $kours_value=1; $answer=1;$err="";}
            }
        }
        return array($answer,$err,$kours_value);
    }

    function saveSaleInvoiceMoneyPay($invoice_id,$pay_id,$kredit,$pay_type_id,$paybox_id,$doc_cash_id,$cash_id,$cash_kours){$db=DbSingleton::getDb();$slave=new slave;session_start();$user_id=$_SESSION["media_user_id"];$answer=0;$err="Помилка збереження даних!";
        $invoice_id=$slave->qq($invoice_id);$invoice_client_id=0;$invoice_summ_debit=0;$invoice_doc_type_id=0;$invoice_summ=0;$doc_nom=0;
        if ($invoice_id==0 || $invoice_id==""){
            $err="Не вказано номер накладної для оплати";$answer=0;
        }
        if ($invoice_id>0 && $pay_id==0){
            $pay_id=$slave->qq($pay_id);$kredit=$slave->qq($kredit);$pay_type_id=$slave->qq($pay_type_id);$paybox_id=$slave->qq($paybox_id);
            if ($pay_id==0){
                $r=$db->query("select max(id) as mid from J_PAY;");$pay_id=$db->result($r,0,"mid")+1;
                $r=$db->query("select max(doc_nom) as doc_nom from J_PAY where paybox_id='$paybox_id';");$doc_nom=$db->result($r,0,"doc_nom")+1;

                $r=$db->query("select * from J_SALE_INVOICE where id='$invoice_id';");$n=$db->num_rows($r);
                if ($n==1){
                    $invoice_summ=$db->result($r,0,"summ");
                    $invoice_summ_debit=$db->result($r,0,"summ_debit");
                    $invoice_doc_type_id=$db->result($r,0,"doc_type_id");
                    $invoice_client_id=$db->result($r,0,"client_id");
                }

            }
            if ($pay_id>0 && $kredit>0 && $pay_type_id==89 && $paybox_id>0){
                list($balans_before,$balans_before_cash_id)=$this->getClientGeneralSaldo($invoice_client_id);
                $doc_sum_pay=0;
                if ($doc_cash_id==$cash_id){$doc_sum_pay=$kredit;}
                if ($doc_cash_id!=$cash_id){

                    if ($doc_cash_id==1 && $cash_id==2){$doc_sum_pay=$cash_kours*$kredit;}
                    if ($doc_cash_id==1 && $cash_id==3){$doc_sum_pay=$cash_kours*$kredit;}

                    if ($doc_cash_id==2 && $cash_id==1){$doc_sum_pay=round($kredit/$cash_kours,2);}
                    if ($doc_cash_id==2 && $cash_id==3){$doc_sum_pay=round($kredit*$cash_kours,2);}

                    if ($doc_cash_id==3 && $cash_id==1){$doc_sum_pay=round($kredit/$cash_kours,2);}
                    if ($doc_cash_id==3 && $cash_id==2){$doc_sum_pay=round($kredit/$cash_kours,2);}
                }

                if ($invoice_summ_debit>=$doc_sum_pay){ // if sum pay less then invoice summ
                    $db->query("insert into J_PAY (`id`,`pay_type_id`,`paybox_id`,`doc_nom`,`cash_id`,`summ`,`user_id`) values ('$pay_id','$pay_type_id','$paybox_id','$doc_nom','$cash_id','$kredit','$user_id');");
                    $db->query("insert into J_PAY_STR (`pay_id`,`parrent_doc_type_id`,`parrent_doc_id`,`summ_doc`,`doc_cash_id`,`summ_pay`,`pay_cash_id`,`pay_cash_kours`,`pay_cash_summ`) values ('$pay_id','$invoice_doc_type_id','$invoice_id','$invoice_summ','$doc_cash_id','$doc_sum_pay','$cash_id','$cash_kours','$kredit');");
                    $new_summ_debit=$invoice_summ_debit-$doc_sum_pay;
                    if ($new_summ_debit<0){$new_summ_debit=0;}
                    $db->query("update J_SALE_INVOICE set summ_debit='$new_summ_debit' where id='$invoice_id' limit 1;");

                    $balans_after=$balans_before+$doc_sum_pay;
                    $db->query("insert into B_CLIENT_BALANS_JOURNAL (`client_id`,`cash_id`,`balans_before`,`deb_kre`,`summ`,`balans_after`,`doc_type_id`,`doc_id`) values ('$invoice_client_id','$doc_cash_id','$balans_before','2','$doc_sum_pay','$balans_after','2','$pay_id');");
                    $db->query("update B_CLIENT_BALANS set saldo=`saldo`+$doc_sum_pay, last_update=NOW() where client_id='$invoice_client_id';");
                }

                if ($invoice_summ_debit<$doc_sum_pay){ // if sum pay more then invoice summ
                    $avans_summ=$doc_sum_pay-$invoice_summ_debit;
                    $kredit2=$invoice_summ_debit;

                    if ($doc_cash_id!=$cash_id){
                        if ($doc_cash_id==1 && $cash_id==2){$kredit2=round($kredit2/$cash_kours,2);}
                        if ($doc_cash_id==1 && $cash_id==3){$kredit2=round($kredit2/$cash_kours,2);}

                        if ($doc_cash_id==2 && $cash_id==1){$kredit2=round($kredit2*$cash_kours,2);}
                        if ($doc_cash_id==2 && $cash_id==3){$kredit2=round($kredit2/$cash_kours,2);}

                        if ($doc_cash_id==3 && $cash_id==1){$kredit2=round($kredit2*$cash_kours,2);}
                        if ($doc_cash_id==3 && $cash_id==2){$kredit2=round($kredit2*$cash_kours,2);}
                    }

                    $db->query("insert into J_PAY (`id`,`pay_type_id`,`paybox_id`,`doc_nom`,`cash_id`,`summ`,`user_id`) values ('$pay_id','$pay_type_id','$paybox_id','$doc_nom','$cash_id','$kredit2','$user_id');");
                    $db->query("insert into J_PAY_STR (`pay_id`,`parrent_doc_type_id`,`parrent_doc_id`,`summ_doc`,`doc_cash_id`,`summ_pay`,`pay_cash_id`,`pay_cash_kours`,`pay_cash_summ`) values ('$pay_id','$invoice_doc_type_id','$invoice_id','$invoice_summ','$doc_cash_id','$invoice_summ_debit','$cash_id','$cash_kours','$kredit2');");
                    $new_summ_debit=0;
                    $db->query("update J_SALE_INVOICE set summ_debit='$new_summ_debit' where id='$invoice_id' limit 1;");

                    $balans_after=$balans_before+$invoice_summ_debit;
                    $db->query("insert into B_CLIENT_BALANS_JOURNAL (`client_id`,`cash_id`,`balans_before`,`deb_kre`,`summ`,`balans_after`,`doc_type_id`,`doc_id`) values ('$invoice_client_id','$doc_cash_id','$balans_before','2','$invoice_summ_debit','$balans_after','2','$pay_id');");
                    $db->query("update B_CLIENT_BALANS set saldo=`saldo`+$invoice_summ_debit, last_update=NOW() where client_id='$invoice_client_id';");
                    // end payment for invoice

                    //creating avans pay
                    $r=$db->query("select max(id) as mid from J_PAY;");$pay_id=$db->result($r,0,"mid")+1;
                    $r=$db->query("select max(doc_nom) as doc_nom from J_PAY where paybox_id='$paybox_id';");$doc_nom=$db->result($r,0,"doc_nom")+1;

                    $db->query("insert into J_PAY (`id`,`pay_type_id`,`paybox_id`,`doc_nom`,`cash_id`,`summ`,`user_id`) values ('$pay_id','$pay_type_id','$paybox_id','$doc_nom','$cash_id','$avans_summ','$user_id');");
                    $db->query("insert into J_PAY_STR (`pay_id`,`parrent_doc_type_id`,`parrent_doc_id`,`summ_doc`,`doc_cash_id`,`summ_pay`,`pay_cash_id`,`pay_cash_kours`,`pay_cash_summ`) values ('$pay_id','0','0','$avans_summ','$cash_id','$avans_summ','$cash_id','1','$avans_summ');");

                    $balans_before=$balans_after;
                    $balans_after=$balans_before+$avans_summ;

                    $db->query("insert into B_CLIENT_BALANS_JOURNAL (`client_id`,`cash_id`,`balans_before`,`deb_kre`,`summ`,`balans_after`,`doc_type_id`,`doc_id`) values ('$invoice_client_id','$cash_id','$balans_before','2','$avans_summ','$balans_after','3','$pay_id');");
                    $db->query("update B_CLIENT_BALANS set saldo=`saldo`+$avans_summ, last_update=NOW() where client_id='$invoice_client_id';");
                }
                $answer=1;$err="";
            }
        }
        return array($answer,$err,$pay_id);
    }

    function getClientGeneralSaldo($sel_id){$db=DbSingleton::getDb();$saldo="0";$cash_id=1;
        $r=$db->query("select `saldo`,cash_id from B_CLIENT_BALANS where client_id='$sel_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $saldo=$db->result($r,0,"saldo");
            $cash_id=$db->result($r,0,"cash_id");
        }
        return array($saldo,$cash_id);
    }

    //===============			MONEY PAY 		==================================

    function printSaleInvoice($invoice_id){$db=DbSingleton::getDb();$cat=new catalogue;$slave=new slave;$money=new toMoney;$invoice_summ=0;$invoice_summ_bez=0;$list="";$form="";
        $r=$db->query("select sv.*, t.name as tpoint_name, sl.full_name as seller_name, sld.vytjag, sld.edrpou, sld.account, sld.bank, sld.mfo, ot.name as org_type_abr, cl.name as client_name, dt.mcaption as doc_type_name, dp.prefix, ss.select_id, sv.dp_id, dt.mvalue as doc_type_abr,ch.abr2 as cash_abr,dp.delivery_address 
        from J_SALE_INVOICE sv
            left outer join CASH ch on ch.id=sv.cash_id
            left outer join T_POINT t on t.id=sv.tpoint_id
            left outer join A_CLIENTS sl on sl.id=sv.seller_id
            left outer join A_CLIENT_DETAILS sld on sld.client_id=sv.seller_id
            left outer join A_ORG_TYPE ot on ot.id=sl.org_type
            left outer join J_DP dp on dp.id=sv.dp_id
            left outer join J_SALE_INVOICE_STORSEL ss on ss.dp_id=sv.dp_id
            left outer join A_CLIENTS cl on cl.id=sv.client_conto_id
            left outer join manual dt on dt.key='client_sale_type' and dt.id=sv.doc_type_id
        where sv.status=1 and sv.id='$invoice_id' limit 0,1;");$n=$db->num_rows($r);

        if ($n==1){
            $prefix=$db->result($r,0,"prefix");
            $doc_nom=$db->result($r,0,"doc_nom");
            $data_create=$db->result($r,0,"data_create");
            //$tpoint_id=$db->result($r,0,"tpoint_id");
            $tpoint_name=$db->result($r,0,"tpoint_name");
            //$seller_id=$db->result($r,0,"seller_id");
            $seller_name=$db->result($r,0,"seller_name");
            $edrpou=$db->result($r,0,"edrpou");
            $account=$db->result($r,0,"account");
            $bank=$db->result($r,0,"bank");
            $mfo=$db->result($r,0,"mfo");
            $vat=$db->result($r,0,"vytjag");
            $org_type_abr=$db->result($r,0,"org_type_abr");
            $client_id=$db->result($r,0,"client_id");
            $client_conto_id=$db->result($r,0,"client_conto_id");
            $client_name=$db->result($r,0,"client_name");
            $doc_type_id=$db->result($r,0,"doc_type_id");
            $dp_id=$db->result($r,0,"dp_id");
            $doc_type_name=$db->result($r,0,"doc_type_name");
            //$doc_type_abr=$db->result($r,0,"doc_type_abr");
            $select_id=$db->result($r,0,"select_id");
            $dp_name="ДП-$dp_id";
            $sel_ar[$select_id]=$select_id;
            //$summ=$db->result($r,0,"summ");
            //$cash_id=$db->result($r,0,"cash_id");
            $cash_abr=$db->result($r,0,"cash_abr");
            $data_pay=$db->result($r,0,"data_pay");
            //$user_name=$this->getMediaUserName($db->result($r,0,"user_id"));
            //$status_invoice=$db->result($r,0,"status_invoice");
            //$status_invoice_cap=$gmanual->get_gmanual_caption($status_invoice);
            $delivery_address=$db->result($r,0,"delivery_address");

            $r=$db->query("select * from J_SALE_INVOICE_STR where invoice_id='$invoice_id' order by id asc;");$n=$db->num_rows($r);
            for ($i=1;$i<=$n;$i++){
                $art_id=$db->result($r,$i-1,"art_id"); $article_name=$cat->getArticleNameLang($art_id);
                $article_nr_displ=$db->result($r,$i-1,"article_nr_displ");
                $brand_id=$db->result($r,$i-1,"brand_id");$brand_name=$cat->getBrandName($brand_id);
                $amount=$db->result($r,$i-1,"amount");
                $unit=$this->getUnitArticle($art_id); //if ($unit=="") $unit="шт.";
                //$price=$db->result($r,$i-1,"price");
                //$price_summ=$price*$amount;
                $price_end=$db->result($r,$i-1,"price_end");
                //$discount=$db->result($r,$i-1,"discount");
                $summ=$db->result($r,$i-1,"summ");
                $zed=$this->getZedArticle($art_id);
                $price_bez=round($price_end/1.2,2);
                $summ_bez=$price_bez*$amount;
                $invoice_summ+=$summ;
                $invoice_summ_bez+=$summ_bez;
                if ($doc_type_id==61) $zed_row="<td align='left'>$zed</td>"; else $zed_row="";

                $list.="<tr>
                    <td align='center'>$i</td>
                    <td align='left'>$article_nr_displ($brand_name)</td>
                    $zed_row
                    <td align='left'>$article_name</td>
                    <td align='center'>$unit</td>
                    <td align='center'>$amount</td>
                    <td align='right'>$price_end</td>
                    <td align='right'>$summ</td>
                </tr>";
            }

            $vat_summ=$invoice_summ/6;

            $storsel_list="СКВ-";
            foreach($sel_ar as $slr){
                $storsel_list.="$slr ";
            }

            $form=""; $max_row=40; $form_htm="";
            if ($doc_type_id==64){$form_htm=RD."/tpl/dp_sale_invoice_print_64.htm";}
            if ($doc_type_id==63){$form_htm=RD."/tpl/dp_sale_invoice_print_63.htm";}
            if ($doc_type_id==61){$form_htm=RD."/tpl/dp_sale_invoice_print_61.htm";}

            $ses_tpoint_id=$_SESSION["media_tpoint_id"];
            $ses_tpoint_name=$this->getTpointFullName($ses_tpoint_id);

            if ($n>$max_row && $doc_type_id==64) {$form_htm=RD."/tpl/dp_sale_invoice_print_64_a4.htm";}

            if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}

            list($mandate_nomber,$mandate_person,$mandate_data,$mandate_seria)=$this->getMandateData($client_id,$data_create);
            list($basis_nomber,$basis_date)=$this->getBasisData($client_id,$data_create);

            $data_create = date("d.m.Y", strtotime($data_create));
            $data_pay = date("d.m.Y", strtotime($data_pay));
            if ($mandate_data!=0) $mandate_data = date("d.m.Y", strtotime($mandate_data)); else $mandate_data="";
            $basis_date = date("d.m.Y", strtotime($basis_date));

            if($basis_nomber!="") $basis_data="№$basis_nomber, від $basis_date"; else $basis_data="б/н";

            $form=str_replace("{curtime}",date("d.m.Y H:i:s"),$form);
            $form=str_replace("{invoice_id}",$invoice_id,$form);
            $form=str_replace("{data}",$data_create,$form);
            $form=str_replace("{data_pay}",$data_pay,$form);
            $form=str_replace("{prefix}",$prefix,$form);
            $form=str_replace("{doc_nom}",$doc_nom,$form);
            $form=str_replace("{tpoint_name}",$tpoint_name,$form);
            $form=str_replace("{seller_name}",$seller_name,$form);
            $form=str_replace("{rr}",$account,$form);
            $form=str_replace("{bank}",$bank,$form);
            $form=str_replace("{mfo}",$mfo,$form);
            $form=str_replace("{dp_name}",$dp_name,$form);
            $form=str_replace("{dp_sale_invoice_storsel_list}",$storsel_list,$form);
            $form=str_replace("{mandate_person}",$mandate_person,$form);
            $form=str_replace("{mandate_nomber}",$mandate_nomber,$form);
            $form=str_replace("{mandate_data}",$mandate_data,$form);
            $form=str_replace("{mandate_seria}",$mandate_seria,$form);
            $form=str_replace("{basis_data}",$basis_data,$form);
            $form=str_replace("{client_id}",$client_conto_id,$form);
            $form=str_replace("{client_name}",$client_name,$form);
            $form=str_replace("{doc_type_name}",$doc_type_name,$form);
            $form=str_replace("{invoice_summ}",$slave->to_money($invoice_summ),$form);
            $form=str_replace("{invoice_summ_word}",$money->num2str($invoice_summ),$form);
            $form=str_replace("{vat_summ}",$slave->to_money($vat_summ),$form);
            $form=str_replace("{cash_name}",$cash_abr,$form);$address_send="";//???
            $form=str_replace("{address_send}",$address_send,$form);
            $form=str_replace("{edrpou}",$edrpou,$form);
            $form=str_replace("{ipn_nom}",$vat,$form);
            $form=str_replace("{org_type_abr}",$org_type_abr,$form);
            $form=str_replace("{cash_abr}",$cash_abr,$form);$volume="";//???
            $form=str_replace("{volume}",$volume,$form);
            $form=str_replace("{delivery_address}",$delivery_address,$form);
            $form=str_replace("{sale_invoice_str_list}",$list,$form);
            $form=str_replace("{ses_tpoint_name}",$ses_tpoint_name,$form);

            //"Формування друкованої форми"
            $mp=new media_print;
            if ($doc_type_id==63){$mp->print_document($form,"A4-L");}
            if ($n<=$max_row && $doc_type_id==64){$mp->print_document($form,"A4-L");}
            if ($n>$max_row && $doc_type_id==64) {$mp->print_document($form,"A4");}
            if ($doc_type_id==61){$mp->print_document($form,"A4");}
        }
        return $form;
    }

    function getUnitArticle($art_id) {$db=DbSingleton::getTokoDb(); $abr="";
        $r=$db->query("select t2u.abr from T2_PACKAGING t2p 
        left outer join units t2u on t2u.id=t2p.UNITS_ID
        where t2p.ART_ID='$art_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $abr=$db->result($r,0,"abr");
        }
        return $abr;
    }

    function getZedArticle($art_id){$db=DbSingleton::getTokoDb(); $costums_code="";
        $r=$db->query("select t2s.COSTUMS_CODE from T2_ZED t2z 
        left outer join T2_COSTUMS t2s on t2s.COSTUMS_ID=t2z.COSTUMS_ID
        where t2z.ART_ID='$art_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $costums_code=$db->result($r,0,"COSTUMS_CODE");
        }
        return $costums_code;
    }

    function getMandateData($client_id,$data) { $db=DbSingleton::getDb();$seria=$number=$receiver=$data_from="";
        $r=$db->query("select * from `A_CLIENTS_MANDATE` where status='1' and client_id='$client_id' and data_from<='$data' and data_to>='$data' limit 0,1;");
        $n=$db->num_rows($r);
        if ($n==1){
            $number=$db->result($r,0,"number");
            $receiver=$db->result($r,0,"receiver");
            $data_from=$db->result($r,0,"data_from");
            $seria=$db->result($r,0,"seria");
        }
        if ($seria!="") $seria="Серія: $seria,";
        return array($number,$receiver,$data_from,$seria);
    }

    function getBasisData($client_id,$data) { $db=DbSingleton::getDb();$number=0;$data_from="";
        $r=$db->query("select * from `A_CLIENTS_BASIS` where status='1' and client_id='$client_id' and data_from<='$data' and data_to>='$data' limit 0,1;");
        $n=$db->num_rows($r);
        if ($n==1){
            $number=$db->result($r,0,"number");
            $data_from=$db->result($r,0,"data_from");
        }
        return array($number,$data_from);
    }

    function getStorageName($sel_id){$db=DbSingleton::getTokoDb();$name="";
        $r=$db->query("select name from `STORAGE` where status='1' and id='$sel_id' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$name=$db->result($r,0,"name");}
        return $name;
    }

    function printSaleInvoiceBuh($invoice_id){$db=DbSingleton::getDb();$cat=new catalogue;$slave=new slave;$list="";$money=new toMoney;$invoice_summ=0;$form="";
        $r=$db->query("select sv.*, t.name as tpoint_name, sl.name as seller_name, sld.edrpou, ot.name as org_type_abr, cl.name as client_name, dt.mcaption as doc_type_name, dt.mvalue as doc_type_abr,ch.abr2 as cash_abr,dp.delivery_address 
        from J_SALE_INVOICE sv
            left outer join CASH ch on ch.id=sv.cash_id
            left outer join T_POINT t on t.id=sv.tpoint_id
            left outer join A_CLIENTS sl on sl.id=sv.seller_id
            left outer join A_CLIENT_DETAILS sld on sld.client_id=sv.seller_id
            left outer join A_ORG_TYPE ot on ot.id=sl.org_type
            left outer join J_DP dp on dp.id=sv.dp_id
            left outer join A_CLIENTS cl on cl.id=sv.client_conto_id
            left outer join manual dt on dt.key='client_sale_type' and dt.id=sv.doc_type_id
        where sv.status=1 and sv.id='$invoice_id' limit 0,1;");$n=$db->num_rows($r);

        if ($n==1){
            $prefix=$db->result($r,0,"prefix");
            $doc_nom=$db->result($r,0,"doc_nom");
            $data_create=$db->result($r,0,"data_create");
            //$tpoint_id=$db->result($r,0,"tpoint_id");
            $tpoint_name=$db->result($r,0,"tpoint_name");
            //$seller_id=$db->result($r,0,"seller_id");
            $seller_name=$db->result($r,0,"seller_name");
            $edrpou=$db->result($r,0,"edrpou");
            $org_type_abr=$db->result($r,0,"org_type_abr");
            //$client_id=$db->result($r,0,"client_id");
            $client_name=$db->result($r,0,"client_name");
            $doc_type_id=$db->result($r,0,"doc_type_id");
            $doc_type_name=$db->result($r,0,"doc_type_name");
           // $doc_type_abr=$db->result($r,0,"doc_type_abr");
            //$summ=$db->result($r,0,"summ");
            //$cash_id=$db->result($r,0,"cash_id");
            $cash_abr=$db->result($r,0,"cash_abr");
            $data_pay=$db->result($r,0,"data_pay");
           // $user_name=$this->getMediaUserName($db->result($r,0,"user_id"));
            //$status_invoice=$db->result($r,0,"status_invoice");
            //$status_invoice_cap=$gmanual->get_gmanual_caption($status_invoice);
            $delivery_address=$db->result($r,0,"delivery_address");

            $r=$db->query("select * from J_SALE_INVOICE_STR where invoice_id='$invoice_id' order by id asc;");$n=$db->num_rows($r);
            for ($i=1;$i<=$n;$i++){
                $art_id=$db->result($r,$i-1,"art_id");
                $article_nr_displ=$db->result($r,$i-1,"article_nr_displ");
                $brand_id=$db->result($r,$i-1,"brand_id");$brand_name=$cat->getBrandName($brand_id);
                $amount=$db->result($r,$i-1,"amount");
                //$price=$db->result($r,$i-1,"price");
                $price_end=$db->result($r,$i-1,"price_end");
                //$discount=$db->result($r,$i-1,"discount");
                $summ=$db->result($r,$i-1,"summ");
                $invoice_summ+=$summ;
                $list.="<tr>
                    <td align='center'>$i</td>
                    <td align='left'>$art_id</td>
                    <td align='left'>$article_nr_displ</td>
                    <td align='center'>$brand_name</td>
                    <td align='center'>$amount</td>
                    <td align='right'>$price_end</td>
                    <td align='right'>$summ</td>
                </tr>";
            }

            $form_htm="";
            if ($doc_type_id==64){$form_htm=RD."/tpl/dp_sale_invoice_print_64.htm";}
            if ($doc_type_id==63){$form_htm=RD."/tpl/dp_sale_invoice_print_63.htm";}
            if ($doc_type_id==61){$form_htm=RD."/tpl/dp_sale_invoice_print_61.htm";}
            if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}

            $form=str_replace("{curtime}",date("d/m/Y H:i:s"),$form);
            $form=str_replace("{invoice_id}",$invoice_id,$form);
            $form=str_replace("{data}",$data_create,$form);
            $form=str_replace("{data_pay}",$data_pay,$form);
            $form=str_replace("{prefix}",$prefix,$form);
            $form=str_replace("{doc_nom}",$doc_nom,$form);
            $form=str_replace("{tpoint_name}",$tpoint_name,$form);
            $form=str_replace("{seller_name}",$seller_name,$form);
            $form=str_replace("{client_name}",$client_name,$form);
            $form=str_replace("{doc_type_name}",$doc_type_name,$form);
            $form=str_replace("{invoice_summ}",$slave->to_money($invoice_summ),$form);
            $form=str_replace("{invoice_summ_word}",$money->num2str($invoice_summ),$form);
            $form=str_replace("{cash_name}",$cash_abr,$form);$address_send="";//???
            $form=str_replace("{address_send}",$address_send,$form);
            $form=str_replace("{edrpou}",$edrpou,$form);
            $form=str_replace("{org_type_abr}",$org_type_abr,$form);
            $form=str_replace("{cash_abr}",$cash_abr,$form);$volume="";//???
            $form=str_replace("{volume}",$volume,$form);
            $form=str_replace("{delivery_address}",$delivery_address,$form);
            $form=str_replace("{sale_invoice_str_list}",$list,$form);

            $mp=new media_print;
            if ($doc_type_id==63){$mp->print_document($form,array(210,280));}
            if ($doc_type_id==64){$mp->print_document($form,array(210,280));}
            if ($doc_type_id==61){$mp->print_document($form,"A4-L");}
        }
        return $form;
    }

    function exportSaleInvoiceExcel($invoice_id){$db=DbSingleton::getDb();$cat=new catalogue;$invoice_summ=0;
        $r=$db->query("select sv.*, t.name as tpoint_name, sl.name as seller_name, sld.edrpou, ot.name as org_type_abr, cl.name as client_name, dt.mcaption as doc_type_name, dt.mvalue as doc_type_abr,ch.abr2 as cash_abr,dp.delivery_address 
        from J_SALE_INVOICE sv
            left outer join CASH ch on ch.id=sv.cash_id
            left outer join T_POINT t on t.id=sv.tpoint_id
            left outer join A_CLIENTS sl on sl.id=sv.seller_id
            left outer join A_CLIENT_DETAILS sld on sld.client_id=sv.seller_id
            left outer join A_ORG_TYPE ot on ot.id=sl.org_type
            left outer join J_DP dp on dp.id=sv.dp_id
            left outer join A_CLIENTS cl on cl.id=sv.client_conto_id
            left outer join manual dt on dt.key='client_sale_type' and dt.id=sv.doc_type_id
        where sv.status=1 and sv.id='$invoice_id' limit 0,1;");$n=$db->num_rows($r);

        if ($n==1){
            $prefix=$db->result($r,0,"prefix");
            $doc_nom=$db->result($r,0,"doc_nom");
            $data_create=$db->result($r,0,"data_create");
            //$tpoint_id=$db->result($r,0,"tpoint_id");
            //$tpoint_name=$db->result($r,0,"tpoint_name");
            //$seller_id=$db->result($r,0,"seller_id");
            $seller_name=$db->result($r,0,"seller_name");
            //$edrpou=$db->result($r,0,"edrpou");
            //$org_type_abr=$db->result($r,0,"org_type_abr");
            //$client_id=$db->result($r,0,"client_id");
            $client_name=$db->result($r,0,"client_name");
            //$doc_type_id=$db->result($r,0,"doc_type_id");
            //$doc_type_name=$db->result($r,0,"doc_type_name");
            //$doc_type_abr=$db->result($r,0,"doc_type_abr");
            //$summ=$db->result($r,0,"summ");
            //$cash_id=$db->result($r,0,"cash_id");
            //$cash_abr=$db->result($r,0,"cash_abr");
            //$data_pay=$db->result($r,0,"data_pay");
            //$user_name=$this->getMediaUserName($db->result($r,0,"user_id"));
            //$status_invoice=$db->result($r,0,"status_invoice");
            //$status_invoice_cap=$gmanual->get_gmanual_caption($status_invoice);
            //$delivery_address=$db->result($r,0,"delivery_address");

            $list=array();
            $r=$db->query("select * from J_SALE_INVOICE_STR where invoice_id='$invoice_id' order by id asc;");$n=$db->num_rows($r);
            for ($i=1;$i<=$n;$i++){
                $art_id=$db->result($r,$i-1,"art_id");
                $article_nr_displ=$db->result($r,$i-1,"article_nr_displ"); $article_name=$cat->getArticleNameLang($art_id);
                $brand_id=$db->result($r,$i-1,"brand_id"); $brand_name=$cat->getBrandName($brand_id);
                $amount=$db->result($r,$i-1,"amount");
                //$price=$db->result($r,$i-1,"price");
                $price_end=$db->result($r,$i-1,"price_end");
                //$discount=$db->result($r,$i-1,"discount");
                $summ=$db->result($r,$i-1,"summ");
                $invoice_summ+=$summ;
                array_push($list,"$i;$article_nr_displ;$brand_name;$article_name;$amount;$price_end;$summ\n");
            }

            $filename="$client_name"."_"."$prefix-$doc_nom"."_"."$data_create";
            $filename=str_replace(" ","_",$filename);
            $filename=str_replace(".","_",$filename);
            $filename=str_replace("/","_",$filename);
            $filename=str_replace("'","",$filename);
            $filename=str_replace('"',"",$filename);
            $filename=str_replace("«","",$filename);
            $filename=str_replace("»","",$filename);

            $header = "№п/п;Індекс;Бренд;Найменування;К-сть;Ціна;Сума\n";

            header('Content-Type: text/csv; charset=utf-8');
            header("Content-Disposition: attachment; filename=$filename.csv");
            $output = fopen('php://output', 'w'); $nakladna="Видакова накладна №$prefix-$doc_nom-$client_name від $data_create\n";
            fputs($output, $nakladna);
            fputs($output, "Продавець: $seller_name\n");
            fputs($output, "Покупець: $client_name\n");
            fputs($output, $header);
            foreach ($list as $row) {
                fwrite($output, $row);
            }
            exit(0);
        }
        return;
    }

}
