<?php
class country{

    function show_country_list(){$db=DbSingleton::getDb(); $manual=new manual;
        $r=$db->query("select t2cnt.* from T2_COUNTRIES t2cnt;");
        $n=$db->num_rows($r); $list="";
        for ($i=1;$i<=$n;$i++){
            $id=$db->result($r,$i-1,"COUNTRY_ID");
            $name=$db->result($r,$i-1,"COUNTRY_NAME");
            $alfa2=$db->result($r,$i-1,"ALFA2");
            $alfa3=$db->result($r,$i-1,"ALFA3");
            $duty=$manual->getManualMCaption("DUTY",$db->result($r,$i-1,"DUTY"));
            $risk=$manual->getManualMCaption("RISK",$db->result($r,$i-1,"RISK"));
            $flag=mb_strtolower($alfa2);
            $list.="<tr style='cursor:pointer'>
                <td onClick='showCountryCard(\"$id\");'>$id</td>
                <td onClick='showCountryCard(\"$id\");'>$name</td>
                <td onClick='showCountryCard(\"$id\");'>$alfa2</td>
                <td onClick='showCountryCard(\"$id\");'>$alfa3</td>
                <td onClick='showCountryCard(\"$id\");'>$duty</td>
                <td onClick='showCountryCard(\"$id\");'>$risk</td>
                <td onClick='showCountryCard(\"$id\");'><img class='flag flag-$flag'/></td>
                <td align='center'>
                    <button class='btn btn-xs btn-warning' onClick='showCountryCard(\"$id\");'><i class='fa fa-edit'></i></button>
                    <button class='btn btn-xs btn-default' onclick='DeleteCountry(\"$id\");'><i class='fa fa-trash'></i></button>
                </td>
            </tr>";
        }
        return $list;
    }

    function newCountryCard(){ $db=DbSingleton::getDb(); $dbt=DbSingleton::getTokoDb();
        $r=$db->query("select max(COUNTRY_ID) as mid from T2_COUNTRIES;");
        $country_id=0+$db->result($r,0,"mid")+1;
        $db->query("insert into T2_COUNTRIES (`COUNTRY_ID`) values ('$country_id');");
        $dbt->query("insert into T2_COUNTRIES (`COUNTRY_ID`) values ('$country_id');");
        return $country_id;
    }

    function showCountryCard($country_id){ $db=DbSingleton::getDb(); $slave=new slave; session_start(); $user_id=$_SESSION["media_user_id"]; $user_name=$_SESSION["user_name"];
        $form="";$form_htm=RD."/tpl/country_card.htm"; if (file_exists("$form_htm")){$form = file_get_contents($form_htm);}
        $r=$db->query("select t2cnt.* from T2_COUNTRIES t2cnt where t2cnt.COUNTRY_ID='$country_id' limit 0,1;"); $n=$db->num_rows($r);
        if ($n==0){$form_htm=RD."/tpl/access_deny.htm";if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}}
        if ($n==1){
            $country_id=$db->result($r,0,"COUNTRY_ID");
            $country_name=$db->result($r,0,"COUNTRY_NAME");
            $country_alfa2=$db->result($r,0,"ALFA2");
            $country_alfa3=$db->result($r,0,"ALFA3");
            $duty=$db->result($r,0,"DUTY");
            $risk=$db->result($r,0,"RISK");
            $form=str_replace("{country_id}",$country_id,$form);
            $form=str_replace("{country_name}",$country_name,$form);
            $form=str_replace("{country_alfa2}",$country_alfa2,$form);
            $form=str_replace("{country_alfa3}",$country_alfa3,$form);
            $form=str_replace("{duty_list}",$slave->showSelectSubList("manual","key","DUTY","mid","mcaption",$duty),$form);
            $form=str_replace("{risk_list}",$slave->showSelectSubList("manual","key","RISK","mid","mcaption",$risk),$form);
            $form=str_replace("{my_user_id}",$user_id,$form);
            $form=str_replace("{my_user_name}",$user_name,$form);
        }
        return $form;
    }

    function saveCountryGeneralInfo($country_id, $country_name, $country_alfa2, $country_alfa3, $country_duty, $country_risk) { $db=DbSingleton::getDb(); $dbt=DbSingleton::getTokoDb(); $slave=new slave;
        $answer=0; $err="Помилка збереження даних!";
        $country_id=$slave->qq($country_id);$country_name=$slave->qq($country_name);$country_alfa2=$slave->qq($country_alfa2);$country_alfa3=$slave->qq($country_alfa3);$country_duty=$slave->qq($country_duty);$country_risk=$slave->qq($country_risk);
        if ($country_id>0){
            $db->query("update T2_COUNTRIES set `COUNTRY_NAME`='$country_name',`ALFA2`='$country_alfa2', `ALFA3`='$country_alfa3', `DUTY`='$country_duty', `RISK`='$country_risk' where `COUNTRY_ID`='$country_id';");
            $dbt->query("update T2_COUNTRIES set `COUNTRY_NAME`='$country_name',`ALFA2`='$country_alfa2', `ALFA3`='$country_alfa3', `DUTY`='$country_duty', `RISK`='$country_risk' where `COUNTRY_ID`='$country_id';");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

    function DeleteCountry($country_id) {$db=DbSingleton::getDb();$slave=new slave;$answer=0; $err="Помилка видалення даних!";
        $country_id=$slave->qq($country_id);
        if ($country_id>0){
            $db->query("delete from T2_COUNTRIES where `COUNTRY_ID`='$country_id';");
            $answer=1;$err="";
        }
        return array($answer,$err);
    }

}
