<?php

class auto_class
{

    function getPHPMonth($str) {
        $year = substr($str,0,4);
        $month = substr($str,4,2);
        return $year . "-" . $month;
    }

    function showManufacturersList() { $db = DbSingleton::getTokoDb();
        $form = "";
        $form_htm = RD . "/tpl/manufacturers.htm";
        if (file_exists("$form_htm")) {
            $form = file_get_contents($form_htm);
        }
        $r = $db->query("SELECT * FROM `T_manufacturers`;");
        $n = $db->num_rows($r);
        $list = "";
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r,$i-1,"MFA_ID");
            $brand = $db->result($r,$i-1,"MFA_BRAND");
            $logo = $db->result($r,$i-1,"LOGO");
            $position = $db->result($r,$i-1,"POSITION");
            $active = $db->result($r,$i-1,"ACTIVE");
            $img = "<div style='width: 100px; height: 100px;'><img src='https://toko.ua/uploads/images/manufacturers/$logo' width='100'></div>";
            $list .= "<tr style='cursor:pointer' onclick='showManufacturersCard($id);'>
                <td>$id</td>
                <td>$brand</td>
                <td align='center'>$img</td>
                <td>$position</td>
                <td>$active</td>
            </tr>";
        }
        $form = str_replace("{manufacturers_range}", $list, $form);
        return $form;
    }

    function showManufacturersCard($mfa_id) { $db = DbSingleton::getTokoDb();
        $form ="";
        $form_htm=RD."/tpl/manufacturers_card.htm";
        if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}

        $r=$db->query("SELECT * FROM `T_manufacturers` WHERE `MFA_ID`=$mfa_id LIMIT 1;");
        $brand=$db->result($r,0,"MFA_BRAND");
        $logo=$db->result($r,0,"LOGO");
        $position=$db->result($r,0,"POSITION");
        $active=$db->result($r,0,"ACTIVE");

        $r=$db->query("SELECT * FROM `T_models` WHERE `MOD_MFA_ID`='$mfa_id';");
        $n=$db->num_rows($r);
        $list="";
        for($i=1;$i<=$n;$i++) {
            $mod_id=$db->result($r,$i-1,"MOD_ID");
            $mod_mfa_id=$db->result($r,$i-1,"MOD_MFA_ID");
            $model=$db->result($r,$i-1,"Model");
            $tex_text=$db->result($r,$i-1,"TEX_TEXT");
            $date_start=$db->result($r,$i-1,"MOD_PCON_START"); $date_start=$this->getPHPMonth($date_start);
            $date_end=$db->result($r,$i-1,"MOD_PCON_END"); $date_end=$this->getPHPMonth($date_end);
            $car_pict=$db->result($r,$i-1,"Car_pict");
            $img_status=$db->result($r,$i-1,"Active_pict");
            $active=$db->result($r,$i-1,"ACTIVE");
            $img="<div style='width: 100px; height: 100px;'><img src='https://toko.ua/uploads/images/models/$car_pict' width='100'></div>";
            $list.="<tr style='cursor: pointer' onclick='showModelsCard($mod_id);'>
                <td>$mod_id</td>
                <td>$mod_mfa_id</td>
                <td>$model</td>
                <td>$tex_text</td>
                <td>$date_start</td>
                <td>$date_end</td>
                <td align='center'>$img</td>
                <td>$img_status</td>
                <td>$active</td>
            </tr>";
        }

        $form=str_replace("{models_range}",$list,$form);
        $form=str_replace("{mfa_id}",$mfa_id,$form);
        $form=str_replace("{mfa_brand}",$brand,$form);
        $form=str_replace("{mfa_logo}",$logo,$form);
        $form=str_replace("{mfa_position}",$position,$form);
        $form=str_replace("{mfa_active}",$active ? "checked" : "",$form);
        return array($form, $brand);
    }

    function saveManufacturersCard($mfa_id, $brand, $logo, $position, $active) { $db = DbSingleton::getTokoDb();
        $answer = 0; $err = "Помилка збереження машини!";
        if ($mfa_id > 0) {
            $db->query("UPDATE `T_manufacturers` SET `MFA_BRAND`='$brand', `LOGO`='$logo', `POSITION`=$position, `ACTIVE`=$active WHERE `MFA_ID`=$mfa_id LIMIT 1;");
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

    function showModelsCard($mod_id) { $db = DbSingleton::getTokoDb();
        $form = "";
        $form_htm = RD . "/tpl/models_card.htm";
        if (file_exists("$form_htm")) {
            $form = file_get_contents($form_htm);
        }
        $r = $db->query("SELECT * FROM `T_models` WHERE `MOD_ID`=$mod_id LIMIT 1;");
        $mod_mfa_id=$db->result($r,0,"MOD_MFA_ID");
        $model=$db->result($r,0,"Model");
        $tex_text=$db->result($r,0,"TEX_TEXT");
        $date_start=$db->result($r,0,"MOD_PCON_START");
        $date_start=$this->getPHPMonth($date_start);
        $date_end=$db->result($r,0,"MOD_PCON_END");
        $date_end=$this->getPHPMonth($date_end);
        $logo=$db->result($r,0,"Car_pict");
        $img_status=$db->result($r,0,"Active_pict");
        $active=$db->result($r,0,"ACTIVE");

        $r = $db->query("SELECT * FROM `T_types` WHERE `TYP_MOD_ID`='$mod_id';");
        $n = $db->num_rows($r);
        $list = "";
        for ($i = 1; $i <= $n; $i++) {
            $typ_id=$db->result($r,$i-1,"TYP_ID");
            $typ_text=$db->result($r,$i-1,"TYP_TEXT");
            $typ_mmt=$db->result($r,$i-1,"TYP_MMT_TEXT");
            $typ_mod=$db->result($r,$i-1,"TYP_MOD_ID");
            $typ_sort=$db->result($r,$i-1,"TYP_SORT");
            $typ_pcon_start=$db->result($r,$i-1,"TYP_PCON_START");
            $typ_pcon_start=$this->getPHPMonth($typ_pcon_start);
            $typ_pcon_end=$db->result($r,$i-1,"TYP_PCON_END");
            $typ_pcon_end=$this->getPHPMonth($typ_pcon_end);
            $typ_kw_from=$db->result($r,$i-1,"TYP_KW_FROM");
            $typ_hp_from=$db->result($r,$i-1,"TYP_HP_FROM");
            $typ_ccm=$db->result($r,$i-1,"TYP_CCM");
            $fuel_id=$db->result($r,$i-1,"FUEL_ID");
            $body_id=$db->result($r,$i-1,"BODY_ID");
            $eng_cod=$db->result($r,$i-1,"ENG_Cod");
            $typ_active=$db->result($r,$i-1,"ACTIVE");
            $list .= "<tr style=\"cursor: pointer;\" onclick=\"showTypesCard('$typ_id');\">
                <td>$typ_id</td>
                <td>$typ_mod</td>
                <td>$typ_text</td>
                <td>$typ_mmt</td>
                <td>$typ_pcon_start</td>
                <td>$typ_pcon_end</td>
                <td>$typ_kw_from</td>
                <td>$typ_hp_from</td>
                <td>$typ_ccm</td>
                <td>$fuel_id</td>
                <td>$body_id</td>
                <td>$eng_cod</td>
                <td>$typ_sort</td>
                <td>$typ_active</td>
            </tr>";
        }

        $form=str_replace("{types_range}",$list,$form);
        $form=str_replace("{mod_id}",$mod_id,$form);
        $form=str_replace("{mod_mfa_id}",$mod_mfa_id,$form);
        $form=str_replace("{mod_model}",$model,$form);
        $form=str_replace("{mod_tex_text}",$tex_text,$form);
        $form=str_replace("{mod_date_start}",$date_start,$form);
        $form=str_replace("{mod_date_end}",$date_end,$form);
        $form=str_replace("{mod_img}",$logo,$form);
        $form=str_replace("{mod_img_status}",$img_status ? "checked" : "",$form);
        $form=str_replace("{mod_active}",$active ? "checked" : "",$form);
        return array($form, $tex_text);
    }

    function saveModelsCard($mod_id, $mod_mfa_id, $mod_model, $mod_tex_text, $mod_date_start, $mod_date_end, $mod_img, $mod_img_status, $mod_active) { $db = DbSingleton::getTokoDb();
        $answer=0; $err="Помилка збереження моделі машини!";
        if ($mod_id>0) {
            $mod_date_start=str_replace("-","",$mod_date_start);
            $mod_date_end=str_replace("-","",$mod_date_end);
            $db->query("UPDATE `T_models` SET `MOD_MFA_ID`='$mod_mfa_id', `Model`=\"$mod_model\", `TEX_TEXT`=\"$mod_tex_text\", `MOD_PCON_START`='$mod_date_start', `MOD_PCON_END`='$mod_date_end', `Car_pict`='$mod_img', `Active_pict`='$mod_img_status', `ACTIVE`='$mod_active' WHERE `MOD_ID`=$mod_id LIMIT 1;");
            $answer=1; $err="";
        }
        return array($answer, $err);
    }

    function showTypesCard($typ_id) { $db = DbSingleton::getTokoDb();
        $form ="";$form_htm=RD."/tpl/types_card.htm";
        if (file_exists("$form_htm")){ $form = file_get_contents($form_htm);}
        $r=$db->query("SELECT * FROM `T_types` WHERE `TYP_ID`='$typ_id' LIMIT 1;");
        $typ_text=$db->result($r,0,"TYP_TEXT");
        $typ_mmt=$db->result($r,0,"TYP_MMT_TEXT");
        $typ_mod=$db->result($r,0,"TYP_MOD_ID");
        $typ_sort=$db->result($r,0,"TYP_SORT");
        $typ_pcon_start=$db->result($r,0,"TYP_PCON_START"); $typ_pcon_start=$this->getPHPMonth($typ_pcon_start);
        $typ_pcon_end=$db->result($r,0,"TYP_PCON_END"); $typ_pcon_end=$this->getPHPMonth($typ_pcon_end);
        $typ_kw_from=$db->result($r,0,"TYP_KW_FROM");
        $typ_hp_from=$db->result($r,0,"TYP_HP_FROM");
        $typ_ccm=$db->result($r,0,"TYP_CCM");
        $fuel_id=$db->result($r,0,"FUEL_ID");
        $body_id=$db->result($r,0,"BODY_ID");
        $eng_cod=$db->result($r,0,"ENG_Cod");
        $typ_active=$db->result($r,0,"ACTIVE");
        $form=str_replace("{typ_id}",$typ_id,$form);
        $form=str_replace("{typ_text}",$typ_text,$form);
        $form=str_replace("{typ_mmt}",$typ_mmt,$form);
        $form=str_replace("{typ_mod}",$typ_mod,$form);
        $form=str_replace("{typ_sort}",$typ_sort,$form);
        $form=str_replace("{typ_pcon_start}",$typ_pcon_start,$form);
        $form=str_replace("{typ_pcon_end}",$typ_pcon_end,$form);
        $form=str_replace("{typ_kw_from}",$typ_kw_from,$form);
        $form=str_replace("{typ_hp_from}",$typ_hp_from,$form);
        $form=str_replace("{typ_ccm}",$typ_ccm,$form);
        $form=str_replace("{fuel_id}",$fuel_id,$form);
        $form=str_replace("{body_id}",$body_id,$form);
        $form=str_replace("{eng_cod}",$eng_cod,$form);
        $form=str_replace("{typ_active}",$typ_active ? "checked" : "",$form);
        return array($form, $typ_mmt);
    }

    function saveTypesCard($typ_id, $typ_text, $typ_mmt, $typ_mod, $typ_sort, $typ_pcon_start, $typ_pcon_end, $typ_kw_from, $typ_hp_from, $typ_ccm, $fuel_id, $body_id, $eng_cod, $typ_active) { $db = DbSingleton::getTokoDb();
        $answer=0; $err="Помилка збереження типу моделі машини!";
        if ($typ_id>0) {
            $typ_pcon_start=str_replace("-","",$typ_pcon_start);
            $typ_pcon_end=str_replace("-","",$typ_pcon_end);
            $db->query("UPDATE `T_types` SET `TYP_TEXT`='$typ_text', `TYP_MMT_TEXT`=\"$typ_mmt\", `TYP_MOD_ID`='$typ_mod', `TYP_SORT`='$typ_sort', `TYP_PCON_START`='$typ_pcon_start', `TYP_PCON_END`='$typ_pcon_end', 
            `TYP_KW_FROM`='$typ_kw_from', `TYP_HP_FROM`='$typ_hp_from', `TYP_CCM`='$typ_ccm', `FUEL_ID`='$fuel_id', `BODY_ID`='$body_id', `ENG_Cod`=\"$eng_cod\", `ACTIVE`='$typ_active' WHERE `TYP_ID`=$typ_id LIMIT 1;");
            $answer=1; $err="";
        }
        return array($answer, $err);
    }

}
