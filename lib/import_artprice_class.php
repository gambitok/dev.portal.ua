<?php

class import_artprice {

    function getNBUKours($data,$val){$db=DbSingleton::getDb();$kours="";
        if ($val==1){$val="usd";} if ($val==2){$val="usd";} if ($val==3){$val="euro";}
        $r=$db->query("select `$val` from kours where data='$data' limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){$kours=$db->result($r,0,"$val"); }
        return $kours;
    }

    function getKourForDate($cash_id_to,$cash_id_from,$data){$db=DbSingleton::getDb(); $kours=1; if ($data=="0000-00-00"){$data=date("Y-m-d");}
        if ($cash_id_from!=$cash_id_to){
            $r=$db->query("select `kours_value` from `J_KOURS` 
            where `cash_id`='$cash_id_from' and `data_from`<='$data' and (`data_to`='0000-00-00' or `data_to`>='$data') and in_use in (0,1) order by id desc limit 0,1;");$n=$db->num_rows($r);
            if ($n==1){$kours=$db->result($r,0,"kours_value");}
        }
        return $kours;
    }

    function loadIncomeKours($cash_id,$data){
        $usd_to_uah=$this->getKourForDate(1,2,$data);
        $eur_to_uah=$this->getKourForDate(1,3,$data);
        return array($usd_to_uah,$eur_to_uah);
    }

    function show_import_artprice_form(){
        $form="";$form_htm=RD."/tpl/import_artprice_str_form.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        list($csv_exist,$csv_file_name,$pre_table)=$this->showCsvPreview();
        $form=str_replace("{records_list}","<tr><td colspan=10 align='center'>Записи не завантажено</td></tr>",$form);
        $form=str_replace("{import_file_name}","Оберіть файл",$form);
        $form=str_replace("{csv_str_file}",$pre_table,$form);
        return $form;
    }

    function showCsvPreview(){$db=DbSingleton::getDb();$csv_exist=0;$fn=0;$kol_cols=0;$csv_file_name="Оберіть файл";$pre_table="<h3 align='center'>Записи не завантажено</h3>"; session_start();$user_id=$_SESSION["media_user_id"];$form="";
        $r=$db->query("select * from J_IMPORT_ARTPRICE_CSV where user_id='$user_id' order by id desc limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $file_name=$db->result($r,0,"file_name");
            $file_path=RD."/cdn/import_artprice_files/$user_id/$file_name";
            if (file_exists($file_path)){
                $form_htm=RD."/tpl/csv_str_file.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
                $cols_list=""; $records_list="";
                $handle = @fopen($file_path, "r");
                if ($handle) {
                    //$db->query("delete from catalogue_price where provider='$provider';");
                    set_time_limit(0);$max_cols=0;
                    while (($buffer = fgets($handle, 4096)) !== false) {$fn+=1;
                        $buf=explode(";",$buffer);
                        if ($buffer!=""){
                            if ($fn==1){$kol_cols=count($buf);}
                            $buf=str_replace("'","\'",$buf);$buf=str_replace('"','\"',$buf);$row="";$ex_cols=0;
                            if ($max_cols<$kol_cols){$ex_cols=1;$cols_list="";}
                            for ($i=1;$i<=$kol_cols;$i++){
                                if ($i==1){$row="<td>$fn</td>";}
                                $row.="<td>".trim($buf[$i-1])."</td>";
                                if ($ex_cols==1){$cols_list.="<th><select id=\"clm-$i\" size='1'><option value='0'>-</option><option value='1'>ART_ID</option><option value='2'>ОС, $</option><option value='3'>Min, %</option>
                                <option value='4'>0 Прайс</option><option value='5'>1 Прайс</option><option value='6'>2 Прайс</option>
                                <option value='7'>3 Прайс</option><option value='8'>4 Прайс</option><option value='9'>5 Прайс</option>
                                <option value='10'>6 Прайс </option><option value='11'>7 Прайс</option><option value='12'>8 Прайс</option>
                                <option value='13'>9 Прайс</option><option value='14'>10 Прайс</option><option value='15'>11 Прайс</option>
                                <option value='16'>0 %</option><option value='17'>1 %</option><option value='18'>2 %</option>
                                <option value='19'>3 %</option><option value='20'>4 %</option><option value='21'>5 %</option>
                                <option value='22'>6 %</option><option value='23'>7 %</option><option value='24'>8 %</option>
                                <option value='25'>9 %</option><option value='26'>10 %</option><option value='27'>11 %</option>
                                </select></th>";}
                            }if ($row!=""){
                                $records_list.="<tr>$row</tr>";
                            }
                        }
                        if ($fn==30){break;}
                    }
                    fclose($handle);
                }
                $form=str_replace("{income_id}",0,$form);
                $form=str_replace("{cols_list}",$cols_list,$form);
                $form=str_replace("{records_list}",$records_list,$form);
                $form=str_replace("{kol_cols}",$kol_cols,$form);
                $csv_file_name=$file_name;$csv_exist=1;$pre_table=$form;
            }
        }
        return array($csv_exist,$csv_file_name,$pre_table);
    }

    function finishArtpriceCsvImport($start_row,$kol_cols,$cols){$db=DbSingleton::getDb();$dbt=DbSingleton::getTokoDb();$slave=new slave;session_start();$user_id=$_SESSION["media_user_id"];$answer=0;$err="Помилка збереження даних!";
        $start_row=$slave->qq($start_row);$kol_cols=$slave->qq($kol_cols);$cols=$slave->qq($cols);$fn=0;$Per=$Prc=[];
        $r=$db->query("select * from J_IMPORT_ARTPRICE_CSV where user_id='$user_id' order by id desc limit 0,1;");$n=$db->num_rows($r);
        if ($n==1){
            $file_name=$db->result($r,0,"file_name");
            $file_path=RD."/cdn/import_artprice_files/$user_id/$file_name";
            if (file_exists($file_path)){
                //$form_htm=RD."/tpl/csv_str_file.htm";if (file_exists("$form_htm")){$form=file_get_contents($form_htm);}
                //$cols_list=""; $records_list="";
                $art_id=0;$min_price=0;$price[0]=0;$price[1]=0;$price[2]=0;$price[3]=0;$price[4]=0;$price[5]=0;$price[6]=0;
                $price[7]=0;$price[8]=0;$price[9]=0;$price[10]=0;$price[11]=0;$perc[0]=0;$perc[1]=0;$perc[2]=0;$perc[3]=0;$perc[4]=0;
                $perc[5]=0;$perc[6]=0;$perc[7]=0;$perc[8]=0;$perc[9]=0;$perc[10]=0;$perc[11]=0;//$oc_price=0;

                for ($i=1;$i<=$kol_cols;$i++){
                    if ($cols[$i]==1){$art_id=$i;}
                    //if ($cols[$i]==2){$oc_price=$i;}
                    if ($cols[$i]==3){$min_price=$i;}
                    if ($cols[$i]==4){$price[0]=$i;}if ($cols[$i]==5){$price[1]=$i;}if ($cols[$i]==6){$price[2]=$i;}if ($cols[$i]==7){$price[3]=$i;}
                    if ($cols[$i]==8){$price[4]=$i;}if ($cols[$i]==9){$price[5]=$i;}if ($cols[$i]==10){$price[6]=$i;}
                    if ($cols[$i]==11){$price[7]=$i;}if ($cols[$i]==12){$price[8]=$i;}if ($cols[$i]==13){$price[9]=$i;}
                    if ($cols[$i]==14){$price[10]=$i;}if ($cols[$i]==15){$price[11]=$i;}
                    if ($cols[$i]==16){$perc[0]=$i;}if ($cols[$i]==17){$perc[1]=$i;}if ($cols[$i]==18){$perc[2]=$i;}if ($cols[$i]==19){$perc[3]=$i;}
                    if ($cols[$i]==20){$perc[4]=$i;}if ($cols[$i]==21){$perc[5]=$i;}if ($cols[$i]==22){$perc[6]=$i;}if ($cols[$i]==23){$perc[7]=$i;}
                    if ($cols[$i]==24){$perc[8]=$i;}if ($cols[$i]==25){$perc[9]=$i;}if ($cols[$i]==26){$perc[10]=$i;}
                    if ($cols[$i]==27){$perc[11]=$i;}
                }

                $handle = @fopen($file_path, "r");
                if ($handle) { //$db->query("delete from catalogue_price where provider='$provider';");
                    set_time_limit(0);$kol_elem=11;//$max_cols=0;
                    while (($buffer = fgets($handle, 4096)) !== false) {$fn+=1;
                        if ($buffer!=""){
                            $buf=explode(";",$buffer);
                            if ($fn>=$start_row){
                                $buf=str_replace("'","\'",$buf);$buf=str_replace('"','\"',$buf);
                                $ArtId=trim($buf[$art_id-1]);
                                //$OcPrice=trim($buf[$oc_price-1]);
                                $MinPrice=trim($buf[$min_price-1]);
                                for ($j=0;$j<=$kol_elem;$j++){
                                  $Prc[$j]=trim($buf[$price[$j]-1]);$Prc[$j]=str_replace(",",".",$Prc[$j]);$Prc[$j]=str_replace(" ","",$Prc[$j]);
                                  $Per[$j]=trim($buf[$perc[$j]-1]);$Per[$j]=str_replace(",",".",$Per[$j]);$Per[$j]=str_replace(" ","",$Per[$j]);
                                }
                                if ($ArtId!=0 && $ArtId!="" && $MinPrice!=0 && $MinPrice!="" && $Prc[0]!="" && $Prc[0]!=0 && $Prc[1]!="" && $Prc[1]!=0){
                                    $r=$dbt->query("select * from T2_ARTICLES_PRICE_RATING where art_id='$ArtId' and in_use='1' limit 0,1;");$n=$dbt->num_rows($r);
                                    if ($n==1){
                                        $dbt->query("update T2_ARTICLES_PRICE_RATING set in_use='0' where art_id='$ArtId' and in_use='1';");
                                    }
                                    $query="insert into T2_ARTICLES_PRICE_RATING (`art_id`,`in_use`,`data_update`,`user_id`,`template_id`,`minMarkup`";
                                    for ($i=1;$i<=$kol_elem+1;$i++){ $query.=",`price_$i`,`persent_$i`"; }
                                    $query.=") values ('$ArtId','1',CURDATE(),'$user_id','0','$MinPrice'";
                                    for ($i=0;$i<=$kol_elem;$i++){
                                        //$price=$prc[$i];$percent=$prs[$i];
                                        $query.=",'$Prc[$i]','$Per[$i]'";
                                    } $query.=");";
                                    $dbt->query($query);
                                }
                            }
                        }
                    }
                    fclose($handle);
                    if (file_exists(RD."/cdn/import_artprice_files/$user_id/$file_name")){unlink(RD."/cdn/import_artprice_files/$user_id/$file_name");}
                    $db->query("update J_IMPORT_ARTPRICE_CSV set status=0 where `user_id`='$user_id';");
                    $answer=1;$err="";
                }
            }
        }
        return array($answer,$err);
    }

}