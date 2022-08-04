<?php

class SettingsNewClass
{

    function showSeoFooterForm()
    {
        $db = DbSingleton::getTokoDb();
        $list = "";
        $form = ""; $form_htm = RD . "/tpl/seo_footer/form.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }

        $r = $db->query("SELECT * FROM `T2_SEO_FOOTER` WHERE 1;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id         = $db->result($r, $i - 1, "ID");
            $router     = $db->result($r, $i - 1, "ROUTER");
            $link       = $db->result($r, $i - 1, "LINK");
            $text_ru    = $db->result($r, $i - 1, "TEXT_RU");
            $text_ua    = $db->result($r, $i - 1, "TEXT_UA");
            $text_en    = $db->result($r, $i - 1, "TEXT_EN");

            if ($text_ru !== "") {
                $text_ru = "+";
            }
            if ($text_ua !== "") {
                $text_ua = "+";
            }
            if ($text_en !== "") {
                $text_en = "+";
            }

            $list .= "
            <tr onclick=\"showSeoFooterCard('$id');\">
                <td>$id</td>
                <td>$router</td>
                <td>$link</td>
                <td>$text_ru</td>
                <td>$text_ua</td>
                <td>$text_en</td>
            </tr>";
        }

        $form = str_replace("{seo_footer_range}", $list, $form);

        return $form;
    }

    function showSeoFooterCard($id)
    {
        $db = DbSingleton::getTokoDb();

        if ($id == 0) {
            session_start();
            $router     = $link = $text_ru = $text_ua = $text_en = "";
            $user_id    = (int)$_SESSION["media_user_id"];
            $date       = date("Y-m-d H:i:s");
        } else {
            $r = $db->query("SELECT * FROM `T2_SEO_FOOTER` WHERE `ID` = $id LIMIT 1;");
            $router     = $db->result($r, 0, "ROUTER");
            $link       = $db->result($r, 0, "LINK");
            $text_ru    = $db->result($r, 0, "TEXT_RU");
            $text_ua    = $db->result($r, 0, "TEXT_UA");
            $text_en    = $db->result($r, 0, "TEXT_EN");
            $user_id    = $db->result($r, 0, "USER_ID");
            $date       = $db->result($r, 0, "DATA");
        }

        $form = ""; $form_htm = RD . "/tpl/seo_footer/card.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $form = str_replace("{seo_id}", $id, $form);
        $form = str_replace("{seo_router}", $router, $form);
        $form = str_replace("{seo_link}", $link, $form);
        $form = str_replace("{seo_text_ru}", $text_ru, $form);
        $form = str_replace("{seo_text_ua}", $text_ua, $form);
        $form = str_replace("{seo_text_en}", $text_en, $form);
        $form = str_replace("{seo_user}", $this->getMediaUserName($user_id), $form);
        $form = str_replace("{seo_data}", $date, $form);

        return $form;
    }

    function saveSeoFooter($id, $router, $link, $text_ru, $text_ua, $text_en)
    {
        $db = DbSingleton::getTokoDb();
        session_start();
        $user_id = (int)$_SESSION["media_user_id"];
        $text_ru = ($text_ru === "<p><br></p>") ? "" : $text_ru;
        $text_ua = ($text_ua === "<p><br></p>") ? "" : $text_ua;
        $text_en = ($text_en === "<p><br></p>") ? "" : $text_en;

        if ($id > 0) {
            $db->query("UPDATE `T2_SEO_FOOTER` SET `ROUTER` = '$router', `LINK` = '$link', `TEXT_RU` = \"$text_ru\", `TEXT_UA` = \"$text_ua\", `TEXT_EN` = \"$text_en\", `USER_ID` = $user_id WHERE `ID` = $id LIMIT 1;");
            $answer = 1; $err = "Успішно редаговано";
        } else {
            $r = $db->query("SELECT MAX(`ID`) as mid FROM `T2_SEO_FOOTER`;");
            $max_id = 0 + $db->result($r, 0, "mid") + 1;
            $db->query("INSERT INTO `T2_SEO_FOOTER` (`ID`, `ROUTER`, `LINK`, `TEXT_RU`, `TEXT_UA`, `TEXT_EN`, `USER_ID`) 
            VALUES ($max_id, '$router', '$link', \"$text_ru\", \"$text_ua\", \"$text_en\", $user_id);");
            $answer = 1; $err = "Успішно додано";
        }

        return array($answer, $err);
    }

    function dropSeoFooter($id)
    {
        $db = DbSingleton::getTokoDb();
        $answer = 0; $err = "Помилка збереження даних!";

        if ($id > 0) {
            $db->query("DELETE FROM `T2_SEO_FOOTER` WHERE `id` = $id;");
            $answer = 1; $err = "";
        }

        return array($answer, $err);
    }

    function showSeoTextForm()
    {
        $db = DbSingleton::getTokoDb();
        $list = ""; $list_title = $list_generate = "";
        $form = ""; $form_htm = RD . "/tpl/seo_client/form.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }

        $r = $db->query("SELECT `ID`, `ROUTER`, `LINK`, `CONTENT_RU`, `CONTENT_UA`, `CONTENT_EN` FROM `T2_SEO_TEXT` WHERE 1;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id         = $db->result($r, $i - 1, "ID");
            $router     = $db->result($r, $i - 1, "ROUTER");
            $link       = $db->result($r, $i - 1, "LINK");
            $text_ru    = $db->result($r, $i - 1, "CONTENT_RU");
            $text_ua    = $db->result($r, $i - 1, "CONTENT_UA");
            $text_en    = $db->result($r, $i - 1, "CONTENT_EN");

            if ($text_ru !== "") {
                $text_ru = "+";
            }
            if ($text_ua !== "") {
                $text_ua = "+";
            }
            if ($text_en !== "") {
                $text_en = "+";
            }

            $list .= "
            <tr onclick=\"showSeoTextCard('$id');\">
                <td>$id</td>
                <td>$router</td>
                <td>$link</td>
                <td>$text_ru</td>
                <td>$text_ua</td>
                <td>$text_en</td>
            </tr>";
        }

        $r = $db->query("SELECT `ID`, `ROUTER`, `LINK`, `TITLE_RU`, `TITLE_UA`, `TITLE_EN`, `DESCR_RU`, `DESCR_UA`, `DESCR_EN` FROM `T2_SEO_TITLE` WHERE 1;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id         = $db->result($r, $i - 1, "ID");
            $router     = $db->result($r, $i - 1, "ROUTER");
            $link       = $db->result($r, $i - 1, "LINK");
            $title_ru   = $db->result($r, $i - 1, "TITLE_RU");
            $title_ua   = $db->result($r, $i - 1, "TITLE_UA");
            $title_en   = $db->result($r, $i - 1, "TITLE_EN");
            $descr_ru   = $db->result($r, $i - 1, "DESCR_RU");
            $descr_ua   = $db->result($r, $i - 1, "DESCR_UA");
            $descr_en   = $db->result($r, $i - 1, "DESCR_EN");
            $title_ru   = ($title_ru !== "") ? "+" : $title_ru;
            $title_ua   = ($title_ua !== "") ? "+" : $title_ua;
            $title_en   = ($title_en !== "") ? "+" : $title_en;
            $descr_ru   = ($descr_ru !== "") ? "+" : $descr_ru;
            $descr_ua   = ($descr_ua !== "") ? "+" : $descr_ua;
            $descr_en   = ($descr_en !== "") ? "+" : $descr_en;

            $list_title .= "
            <tr onclick=\"showSeoTitleCard('$id');\">
                <td>$id</td>
                <td>$router</td>
                <td>$link</td>
                <td>$title_ru</td>
                <td>$title_ua</td>
                <td>$title_en</td>
                <td>$descr_ru</td>
                <td>$descr_ua</td>
                <td>$descr_en</td>
            </tr>";
        }

        $r = $db->query("SELECT `ID`, `ROUTER`, `LINK`, `TEXT_RU`, `TEXT_UA`, `TEXT_EN` FROM `T2_SEO_GENERATE` WHERE 1;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id         = $db->result($r, $i - 1, "ID");
            $router     = $db->result($r, $i - 1, "ROUTER");
            $link       = $db->result($r, $i - 1, "LINK");
            $title_ru   = $db->result($r, $i - 1, "TEXT_RU");
            $title_ua   = $db->result($r, $i - 1, "TEXT_UA");
            $title_en   = $db->result($r, $i - 1, "TEXT_EN");
            $title_ru   = ($title_ru !== "") ? "+" : $title_ru;
            $title_ua   = ($title_ua !== "") ? "+" : $title_ua;
            $title_en   = ($title_en !== "") ? "+" : $title_en;

            $list_generate .= "
            <tr onclick=\"showSeoGenerateCard('$id');\">
                <td>$id</td>
                <td>$router</td>
                <td>$link</td>
                <td>$title_ru</td>
                <td>$title_ua</td>
                <td>$title_en</td>
            </tr>";
        }

        $form = str_replace("{seo_range}", $list, $form);
        $form = str_replace("{seo_title_range}", $list_title, $form);
        $form = str_replace("{seo_generate_range}", $list_generate, $form);

        return $form;
    }

    function showSeoTitleCard($id)
    {
        $db = DbSingleton::getTokoDb();

        if ($id == 0) {
            session_start();
            $router     = $link = $title_ru = $title_ua = $title_en = $descr_ru = $descr_ua = $descr_en = "";
            $user_id    = (int)$_SESSION["media_user_id"];
            $date       = date("Y-m-d H:i:s");
        } else {
            $r = $db->query("SELECT * FROM `T2_SEO_TITLE` WHERE `ID` = $id LIMIT 1;");
            $router     = $db->result($r, 0, "ROUTER");
            $link       = $db->result($r, 0, "LINK");
            $title_ru   = $db->result($r, 0, "TITLE_RU");
            $title_ua   = $db->result($r, 0, "TITLE_UA");
            $title_en   = $db->result($r, 0, "TITLE_EN");
            $descr_ru   = $db->result($r, 0, "DESCR_RU");
            $descr_ua   = $db->result($r, 0, "DESCR_UA");
            $descr_en   = $db->result($r, 0, "DESCR_EN");
            $user_id    = $db->result($r, 0, "USER_ID");
            $date       = $db->result($r, 0, "DATA");
        }

        $form = ""; $form_htm = RD . "/tpl/seo_client/card_title.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $form = str_replace("{seo_id}", $id, $form);
        $form = str_replace("{seo_router}", $router, $form);
        $form = str_replace("{seo_link}", $link, $form);
        $form = str_replace("{seo_text_ru}", $title_ru, $form);
        $form = str_replace("{seo_text_ua}", $title_ua, $form);
        $form = str_replace("{seo_text_en}", $title_en, $form);
        $form = str_replace("{seo_descr_ru}", $descr_ru, $form);
        $form = str_replace("{seo_descr_ua}", $descr_ua, $form);
        $form = str_replace("{seo_descr_en}", $descr_en, $form);
        $form = str_replace("{seo_user}", $this->getMediaUserName($user_id), $form);
        $form = str_replace("{seo_data}", $date, $form);

        return $form;
    }

    function saveSeoTitle($id, $router, $link, $text_ru, $text_ua, $text_en, $descr_ru, $descr_ua, $descr_en)
    {
        $db = DbSingleton::getTokoDb();
        session_start();
        $user_id    = (int)$_SESSION["media_user_id"];

        $text_ru    = ($text_ru === "<p><br></p>") ? "" : $text_ru;
        $text_ua    = ($text_ua === "<p><br></p>") ? "" : $text_ua;
        $text_en    = ($text_en === "<p><br></p>") ? "" : $text_en;

        $descr_ru   = ($descr_ru === "<p><br></p>") ? "" : $descr_ru;
        $descr_ua   = ($descr_ua === "<p><br></p>") ? "" : $descr_ua;
        $descr_en   = ($descr_en === "<p><br></p>") ? "" : $descr_en;

        $text_ru    = str_replace("&amp;", "&", $text_ru);
        $text_ua    = str_replace("&amp;", "&", $text_ua);
        $text_en    = str_replace("&amp;", "&", $text_en);

        $descr_ru   = str_replace("&amp;", "&", $descr_ru);
        $descr_ua   = str_replace("&amp;", "&", $descr_ua);
        $descr_en   = str_replace("&amp;", "&", $descr_en);

        if ($id > 0) {
            $db->query("INSERT INTO `T2_SEO_TITLE_ARCHIVE` (`ROUTER`, `LINK`, `TITLE_RU`, `TITLE_UA`, `TITLE_EN`, `DESCR_RU`, `DESCR_UA`, `DESCR_EN`, `DATA`, `USER_ID`)
            SELECT `ROUTER`, `LINK`, `TITLE_RU`, `TITLE_UA`, `TITLE_EN`, `DESCR_RU`, `DESCR_UA`, `DESCR_EN`, `DATA`, `USER_ID` FROM `T2_SEO_TITLE` WHERE `ID` = $id LIMIT 1;");
            $db->query("UPDATE `T2_SEO_TITLE` SET `ROUTER` = '$router', `LINK` = '$link', `TITLE_RU` = \"$text_ru\", `TITLE_UA` = \"$text_ua\", `TITLE_EN` = \"$text_en\", `DESCR_RU` = \"$descr_ru\", `DESCR_UA` = \"$descr_ua\", `DESCR_EN` = \"$descr_en\", `USER_ID` = $user_id WHERE `ID` = $id LIMIT 1;");
            $answer = 1; $err = "Успішно редаговано";
        } else {
            $r = $db->query("SELECT MAX(`ID`) as mid FROM `T2_SEO_TITLE`;");
            $max_id = 0 + $db->result($r, 0, "mid") + 1;
            $db->query("INSERT INTO `T2_SEO_TITLE` (`ID`, `ROUTER`, `LINK`, `TITLE_RU`, `TITLE_UA`, `TITLE_EN`, `DESCR_RU`, `DESCR_UA`, `DESCR_EN`, `USER_ID`) 
            VALUES ($max_id, '$router', '$link', \"$text_ru\", \"$text_ua\", \"$text_en\", \"$descr_ru\", \"$descr_ua\", \"$descr_en\", $user_id);");
            $answer = 1; $err = "Успішно додано";
        }

        return array($answer, $err);
    }

    function dropSeoTitle($id)
    {
        $db = DbSingleton::getTokoDb();
        $answer = 0; $err = "Помилка збереження даних!";
        if ($id > 0) {
            $db->query("DELETE FROM `T2_SEO_TITLE` WHERE `id` = $id;");
            $answer = 1; $err = "";
        }

        return array($answer, $err);
    }

    function showSeoTextCard($id)
    {
        $db = DbSingleton::getTokoDb();
        if ($id == 0) {
            session_start();
            $router     = $link = $text_ru = $text_ua = $text_en = "";
            $user_id    = (int)$_SESSION["media_user_id"];
            $date       = date("Y-m-d H:i:s");
        } else {
            $r = $db->query("SELECT * FROM `T2_SEO_TEXT` WHERE `ID` = $id LIMIT 1;");
            $router     = $db->result($r, 0, "ROUTER");
            $link       = $db->result($r, 0, "LINK");
            $text_ru    = $db->result($r, 0, "CONTENT_RU");
            $text_ua    = $db->result($r, 0, "CONTENT_UA");
            $text_en    = $db->result($r, 0, "CONTENT_EN");
            $user_id    = $db->result($r, 0, "USER_ID");
            $date       = $db->result($r, 0, "DATA");
        }

        $form = ""; $form_htm = RD . "/tpl/seo_client/card.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $form = str_replace("{seo_id}", $id, $form);
        $form = str_replace("{seo_router}", $router, $form);
        $form = str_replace("{seo_link}", $link, $form);
        $form = str_replace("{seo_text_ru}", $text_ru, $form);
        $form = str_replace("{seo_text_ua}", $text_ua, $form);
        $form = str_replace("{seo_text_en}", $text_en, $form);
        $form = str_replace("{seo_user}", $this->getMediaUserName($user_id), $form);
        $form = str_replace("{seo_data}", $date, $form);

        return $form;
    }

    function saveSeoText($id, $router, $link, $text_ru, $text_ua, $text_en)
    {
        $db = DbSingleton::getTokoDb();
        session_start();
        $user_id = (int)$_SESSION["media_user_id"];
        $text_ru = ($text_ru === "<p><br></p>") ? "" : $text_ru;
        $text_ua = ($text_ua === "<p><br></p>") ? "" : $text_ua;
        $text_en = ($text_en === "<p><br></p>") ? "" : $text_en;

        if ($id > 0) {
            $db->query("INSERT INTO `T2_SEO_TEXT_ARCHIVE` (`ROUTER`, `LINK`, `CONTENT_RU`, `CONTENT_UA`, `CONTENT_EN`, `DATA`, `USER_ID`) 
            SELECT `ROUTER`, `LINK`, `CONTENT_RU`, `CONTENT_UA`, `CONTENT_EN`, `DATA`, `USER_ID` FROM `T2_SEO_TEXT` WHERE `ID` = $id LIMIT 1;;");
            $db->query("UPDATE `T2_SEO_TEXT` SET `ROUTER` = '$router', `LINK` = '$link', `CONTENT_RU` = \"$text_ru\", `CONTENT_UA` = \"$text_ua\", `CONTENT_EN` = \"$text_en\", `USER_ID` = $user_id WHERE `ID` = $id LIMIT 1;");
            $answer = 1; $err = "Успішно редаговано";
        } else {
            $r = $db->query("SELECT MAX(`ID`) as mid FROM `T2_SEO_TEXT`;");
            $max_id = 0 + $db->result($r, 0, "mid") + 1;
            $db->query("INSERT INTO `T2_SEO_TEXT` (`ID`, `ROUTER`, `LINK`, `CONTENT_RU`, `CONTENT_UA`, `CONTENT_EN`, `USER_ID`) 
            VALUES ($max_id, '$router', '$link', \"$text_ru\", \"$text_ua\", \"$text_en\", $user_id);");
            $answer = 1; $err = "Успішно додано";
        }

        return array($answer, $err);
    }

    function dropSeoText($id)
    {
        $db = DbSingleton::getTokoDb();
        $answer = 0; $err = "Помилка збереження даних!";
        if ($id > 0) {
            $db->query("DELETE FROM `T2_SEO_TEXT` WHERE `id` = $id;");
            $answer = 1; $err = "";
        }

        return array($answer, $err);
    }

    function showSeoGenerateCard($id)
    {
        $db = DbSingleton::getTokoDb();
        if ($id == 0) {
            session_start();
            $router     = $link = $text_ru = $text_ua = $text_en = "";
            $user_id    = (int)$_SESSION["media_user_id"];
            $date       = date("Y-m-d H:i:s");
        } else {
            $r = $db->query("SELECT * FROM `T2_SEO_GENERATE` WHERE `ID` = $id LIMIT 1;");
            $router     = $db->result($r, 0, "ROUTER");
            $link       = $db->result($r, 0, "LINK");
            $text_ru    = $db->result($r, 0, "TEXT_RU");
            $text_ua    = $db->result($r, 0, "TEXT_UA");
            $text_en    = $db->result($r, 0, "TEXT_EN");
            $user_id    = $db->result($r, 0, "USER_ID");
            $date       = $db->result($r, 0, "DATA");
        }

        $form = ""; $form_htm = RD . "/tpl/seo_client/card_generate.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $form = str_replace("{seo_id}", $id, $form);
        $form = str_replace("{seo_router}", $router, $form);
        $form = str_replace("{seo_link}", $link, $form);
        $form = str_replace("{seo_text_ru}", $text_ru, $form);
        $form = str_replace("{seo_text_ua}", $text_ua, $form);
        $form = str_replace("{seo_text_en}", $text_en, $form);
        $form = str_replace("{seo_user}", $this->getMediaUserName($user_id), $form);
        $form = str_replace("{seo_data}", $date, $form);

        return $form;
    }

    function saveSeoGenerate($id, $router, $link, $text_ru, $text_ua, $text_en)
    {
        $db = DbSingleton::getTokoDb();
        session_start();
        $user_id = (int)$_SESSION["media_user_id"];
        $text_ru = ($text_ru === "<p><br></p>") ? "" : $text_ru;
        $text_ua = ($text_ua === "<p><br></p>") ? "" : $text_ua;
        $text_en = ($text_en === "<p><br></p>") ? "" : $text_en;

        if ($id > 0) {
            $db->query("UPDATE `T2_SEO_GENERATE` SET `ROUTER` = '$router', `LINK` = '$link', `TEXT_RU` = \"$text_ru\", `TEXT_UA` = \"$text_ua\", `TEXT_EN` = \"$text_en\", `USER_ID` = $user_id WHERE `ID` = $id LIMIT 1;");
            $answer = 1; $err = "Успішно редаговано";
        } else {
            $r = $db->query("SELECT MAX(`ID`) as mid FROM `T2_SEO_GENERATE`;");
            $max_id = 0 + $db->result($r, 0, "mid") + 1;
            $db->query("INSERT INTO `T2_SEO_GENERATE` (`ID`, `ROUTER`, `LINK`, `TEXT_RU`, `TEXT_UA`, `TEXT_EN`, `USER_ID`) 
            VALUES ($max_id, '$router', '$link', \"$text_ru\", \"$text_ua\", \"$text_en\", $user_id);");
            $answer = 1; $err = "Успішно додано";
        }

        return array($answer, $err);
    }

    function dropSeoGenerate($id)
    {
        $db = DbSingleton::getTokoDb();
        $answer = 0; $err = "Помилка збереження даних!";
        if ($id > 0) {
            $db->query("DELETE FROM `T2_SEO_GENERATE` WHERE `id` = $id;");
            $answer = 1; $err = "";
        }

        return array($answer, $err);
    }

    /*
     * Language
     * */
    function showLanguageList()
    {
        $db = DbSingleton::getTokoDb();
        $list = "";
        $form = ""; $form_htm = RD . "/tpl/new/language.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $r = $db->query("SELECT `id`, `variable` FROM `new_lang_wd` WHERE 1;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r, $i - 1, "id");
            $vr = $db->result($r, $i - 1, "variable");
            $list .= "
            <tr style='cursor: pointer' onClick='showLanguageCard(\"$id\");'>
                <td>$i</td>
                <td>$vr</td>";
            for ($j = 1; $j <= 3; $j++) {
                $rs = $db->query("SELECT `caption` FROM `new_lang_wdv` WHERE `lang_id` = $j AND `wd` = $id;");
                $cap = $db->result($rs, 0, "caption");
                $list .= "
                <td>$cap</td>";
            }
            $list .= "
            </tr>";
        }
        $form = str_replace("{lang_range}", $list, $form);

        return $form;
    }

    function loadLanguageList()
    {
        $db = DbSingleton::getTokoDb();
        $list = "";
        $r = $db->query("SELECT `id`, `variable` FROM `new_lang_wd` WHERE 1;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id = $db->result($r, $i - 1, "id");
            $vr = $db->result($r, $i - 1, "variable");
            $list .= "
            <tr style='cursor:pointer' onClick='showLanguageCard(\"$id\")'>
                <td>$i</td>
                <td>$vr</td>";
            for ($j = 1; $j <= 3; $j++) {
                $rs = $db->query("SELECT `caption` FROM `new_lang_wdv` WHERE `lang_id` = $j AND `wd` = $id;");
                $cap = $db->result($rs, 0, "caption");
                $list .= "
                <td>$cap</td>";
            }
            $list .= "
            </tr>";
        }

        return $list;
    }

    function newLanguageCard($lang_var)
    {
        $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT MAX(`id`) as mid FROM `new_lang_wd`;");
        $max_id = 0 + $db->result($r, 0, "mid") + 1;
        $db->query("INSERT INTO `new_lang_wd` (`id`, `variable`) VALUES ('$max_id', '$lang_var');");

        return $max_id;
    }

    function showLanguageCard($id)
    {
        $db = DbSingleton::getTokoDb();
        $lang_arr = [];
        $form = ""; $form_htm = RD . "/tpl/new/language_card.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }

        $r = $db->query("SELECT * FROM `new_lang_wd` WHERE `id` = $id;");
        $lang_var = $db->result($r, 0, "variable");
        for ($j = 1; $j <= 3; $j++) {
            $rs = $db->query("SELECT `caption` FROM `new_lang_wdv` WHERE `lang_id` = $j AND `wd` = $id;");
            $cap = $db->result($rs, 0, "caption");
            $lang_arr[] = $cap;
        }

        [$lang_ru, $lang_ua, $lang_eng] = $lang_arr;

        $form = str_replace("{id}", $id, $form);
        $form = str_replace("{lang_var}", $lang_var, $form);
        $form = str_replace("{lang_ru}", $lang_ru, $form);
        $form = str_replace("{lang_ua}", $lang_ua, $form);
        $form = str_replace("{lang_eng}", $lang_eng, $form);

        return $form;
    }

    function saveLanguage($lang_id, $lang_ru, $lang_ua, $lang_eng)
    {
        $db = DbSingleton::getTokoDb();
        $answer = 0; $err = "Помилка збереження даних!";

        if ($lang_id > 0) {
            $r = $db->query("SELECT * FROM `new_lang_wdv` WHERE `lang_id` = 1 AND `wd` = $lang_id;");
            $n = $db->num_rows($r);
            if ($n > 0) {
                $db->query("UPDATE `new_lang_wdv` SET `caption` = '$lang_ru' WHERE `lang_id` = 1 AND `wd` = $lang_id;");
            } else {
                $db->query("INSERT INTO `new_lang_wdv` (`lang_id`, `wd`, `caption`) VALUES (1, $lang_id, '$lang_ru');");
            }
            $r = $db->query("SELECT * FROM `new_lang_wdv` WHERE `lang_id` = 2 and `wd` = $lang_id;");
            $n = $db->num_rows($r);
            if ($n > 0) {
                $db->query("UPDATE `new_lang_wdv` SET `caption` = '$lang_ua' WHERE `lang_id` = 2 AND `wd` = $lang_id;");
            } else {
                $db->query("INSERT INTO `new_lang_wdv` (`lang_id`, `wd`, `caption`) VALUES (2, $lang_id, '$lang_ua');");
            }
            $r = $db->query("SELECT * FROM `new_lang_wdv` WHERE `lang_id` = 3 and `wd` = $lang_id;");
            $n = $db->num_rows($r);
            if ($n > 0) {
                $db->query("UPDATE `new_lang_wdv` SET `caption` = '$lang_eng' WHERE `lang_id` = 3 AND `wd` = $lang_id;");
            } else {
                $db->query("INSERT INTO `new_lang_wdv` (`lang_id`, `wd`, `caption`) VALUES (3, $lang_id, '$lang_eng');");
            }
            $answer = 1; $err = "";
        }

        return array($answer, $err);
    }

    function dropLanguage($lang_id)
    {
        $db = DbSingleton::getTokoDb();
        $answer = 0; $err = "Помилка збереження даних!";
        if ($lang_id > 0) {
            $db->query("DELETE FROM `new_lang_wd` WHERE `id` = $lang_id;");
            $db->query("DELETE FROM `new_lang_wdv` WHERE `wd` = $lang_id;");
            $answer = 1; $err = "";
        }

        return array($answer, $err);
    }

	/*
	 * Contacts
	 * */
	
	function getLangCap($lang_id)
    {
        $db = DbSingleton::getTokoDb();
		$r = $db->query("SELECT `caption` FROM `new_lang` WHERE `id` = $lang_id;");

        return $db->result($r, 0, "caption");
	}
	
	function showContactsList()
    {
        $db = DbSingleton::getTokoDb();
        $list = "";
		$form = ""; $form_htm = RD . "/tpl/new/contacts.htm";
		if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }

		$r = $db->query("SELECT * FROM `contacts_new` WHERE `status` = 1;");
		$n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
			$id         = $db->result($r, $i - 1, "id");
			$title      = $db->result($r, $i - 1, "title");
			$address    = $db->result($r, $i - 1, "address");
			$schedule   = $db->result($r, $i - 1, "schedule");
			$phone      = $db->result($r, $i - 1, "phone");
			$lang_id    = $this->getLangCap($db->result($r, $i - 1, "lang_id"));

			$list .= "
            <tr style='cursor:pointer' onClick='showContactsCard(\"$id\")'>
				<td>$title</td>
				<td>$address</td>
				<td>$schedule</td>
				<td>$phone</td>
				<td>$lang_id</td>
			</tr>";
		}

		$form = str_replace("{contacts_range}", $list, $form);

		return $form;
	}
		
	function loadContactsList()
    {
        $db = DbSingleton::getTokoDb();
        $list = "";

		$r = $db->query("SELECT * FROM `contacts_new` WHERE `status` = 1;");
		$n = $db->num_rows($r);
		for ($i = 1; $i <= $n; $i++) {
			$id         = $db->result($r, $i - 1, "id");
			$title      = $db->result($r, $i - 1, "title");
			$address    = $db->result($r, $i - 1, "address");
			$schedule   = $db->result($r, $i - 1, "schedule");
			$phone      = $db->result($r, $i - 1, "phone");

			$list .= "
            <tr style='cursor:pointer' onClick='showContactsCard(\"$id\")'>
				<td>$title</td>
				<td>$address</td>
				<td>$schedule</td>
				<td>$phone</td>
			</tr>";
		}

		return $list;
	}
	
	function newContactsCard($lang_var)
    {
        $db = DbSingleton::getTokoDb();
		$r = $db->query("SELECT MAX(`id`) as mid FROM `contacts_new`;");
		$max_id = 0 + $db->result($r, 0, "mid") + 1;
		$db->query("INSERT INTO `contacts_new` (`id`, `status`, `lang_id`) VALUES ('$max_id', 1, '$lang_var');");

		return $max_id;
	}
	
	function showContactsCard($contact_id)
    {
        $db = DbSingleton::getTokoDb();
        $form = ""; $form_htm = RD . "/tpl/new/contacts_card.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }

		$r = $db->query("SELECT `id`, `title`, `address`, `schedule`, `phone` FROM `contacts_new` WHERE `id` = '$contact_id';");
        $n = $db->num_rows($r);
		if ($n > 0) {
			$id         = $db->result($r, 0, "id");
			$title      = $db->result($r, 0, "title");
			$address    = $db->result($r, 0, "address");
			$schedule   = $db->result($r, 0, "schedule");
			$phone      = $db->result($r, 0, "phone");

			$form = str_replace("{id}", $id, $form);
			$form = str_replace("{title}", $title, $form);
			$form = str_replace("{address}", $address, $form);
			$form = str_replace("{schedule}", $schedule, $form);
			$form = str_replace("{phone}", $phone, $form);
		}

		return $form;
  	}
	
	function saveContacts($contact_id, $title, $address, $schedule, $phone)
    {
        $db = DbSingleton::getTokoDb();
	    $answer = 0; $err = "Помилка збереження даних!";
		if ($contact_id > 0) {
			$db->query("UPDATE `contacts_new` SET `title` = '$title', `address` = '$address', `schedule` = '$schedule', `phone` = '$phone' WHERE `id` = $contact_id;");
			$answer = 1; $err = "";
		}

		return array($answer, $err);
	}
	
	function dropContacts($contact_id)
    {
        $db = DbSingleton::getTokoDb();
	    $answer = 0; $err = "Помилка збереження даних!";
		if ($contact_id > 0) {
			$db->query("DELETE FROM `contacts_new` WHERE `id` = $contact_id;");
			$answer = 1; $err = "";
		}

		return array($answer, $err);
    }
	
    /*
     * Locations
     * */
	
	function showLocations()
    {
        $db = DbSingleton::getTokoDb();
        $list = "";
		$form = ""; $form_htm = RD . "/tpl/new/locations.htm";
		if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }

		$r = $db->query("SELECT t2c.CITY_NAME, t2r.REGION_NAME, t2s.STATE_NAME 
		FROM `T2_CITY` t2c
			LEFT OUTER JOIN `T2_REGION` t2r ON (t2r.REGION_ID = t2c.REGION_ID)
			LEFT OUTER JOIN `T2_STATE` t2s ON (t2s.STATE_ID = t2r.STATE_ID);");
		$n = $db->num_rows($r);
		for ($i = 1; $i <= $n; $i++) {
			$city   = $db->result($r, $i - 1, "CITY_NAME");
			$region = $db->result($r, $i - 1, "REGION_NAME");
			$state  = $db->result($r, $i - 1, "STATE_NAME");

			$list .= "
            <tr>
				<td>$state</td>
				<td>$region</td>
				<td>$city</td>
			</tr>";
		}

		$form = str_replace("{location_range}", $list, $form);

		return $form;
	}

	/*
	 * Contacts bottom
	 * */
	
	function getStatusCaption($status)
    {
        return $status ? "Активний" : "Відключений";
	}
	
	function showIcontSelectList($sel_id)
    {
        $db = DbSingleton::getTokoDb();
        $list = "";
		$r = $db->query("SELECT `id`, `name` FROM `new_icons` WHERE 1;");
		$n = $db->num_rows($r);
		for ($i = 1; $i <= $n; $i++) {
			$id     = $db->result($r, $i - 1, "id");
			$name   = $db->result($r, $i - 1, "name");
			$sel    = ($id == $sel_id) ? "selected" : "";

			$list .= "<option value='$id' $sel>$name</option>";
		}

		return $list;
	}
	
	function getIcon($id)
    {
        $db = DbSingleton::getTokoDb();
		$r = $db->query("SELECT `name`, `icon` FROM `new_icons` WHERE `id` = $id LIMIT 1;");
		$name = $db->result($r, 0, "name");
		$icon = $db->result($r, 0, "icon");

        return "<i class='fa $icon'> $name</i>";
	}
	
	function showContactsBotList()
    {
        $db = DbSingleton::getTokoDb();
        $list = "";
		$form = ""; $form_htm = RD . "/tpl/new/contacts_bottom.htm";
		if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }

		$r = $db->query("SELECT `id`, `text`, `icon`, `link`, `status` FROM `contacts_bottom_new`;");
		$n = $db->num_rows($r);
		for ($i = 1; $i <= $n; $i++) {
			$id     = $db->result($r, $i - 1, "id");
			$text   = $db->result($r, $i - 1, "text");
			$icon   = $db->result($r, $i - 1, "icon");
			$icon   = $this->getIcon($icon);
			$link   = $db->result($r, $i - 1, "link");
			$status = $db->result($r, $i - 1, "status");
			$status = $this->getStatusCaption($status);

			$list .= "
            <tr style='cursor: pointer' onClick='showContactsBotCard(\"$id\")'>
				<td>$text</td>
				<td>$icon</td>
				<td>$link</td>
				<td>$status</td>
			</tr>";
		}
		$form = str_replace("{contacts_range}", $list, $form);

		return $form;
	}
		
	function loadContactsBotList()
    {
        $db = DbSingleton::getTokoDb();
        $list = "";
	    $r = $db->query("SELECT * FROM `contacts_bottom_new` WHERE 1;");
		$n = $db->num_rows($r);
		for ($i = 1; $i <= $n; $i++) {
			$id     = $db->result($r, $i - 1, "id");
			$text   = $db->result($r, $i - 1, "text");
			$icon   = $this->getIcon($db->result($r, $i - 1, "icon"));
			$link   = $db->result($r, $i - 1, "link");
			$status = $this->getStatusCaption($db->result($r, $i - 1, "status"));

			$list .= "
            <tr style='cursor: pointer' onClick='showContactsBotCard(\"$id\");'>
				<td>$text</td>
				<td>$icon</td>
				<td>$link</td>
				<td>$status</td>
			</tr>";
		}

		return $list;
	}
	
	function newContactsBotCard()
    {
        $db = DbSingleton::getTokoDb();
		$r = $db->query("SELECT MAX(`id`) as mid FROM `contacts_bottom_new`;");
		$max_id = $db->result($r, 0, "mid") + 1;
		$db->query("INSERT INTO `contacts_bottom_new` (`id`, `status`) VALUES ('$max_id', 1);");

		return $max_id;
	}
	
	function showContactsBotCard($contact_id)
    {
        $db = DbSingleton::getTokoDb();
        $form = ""; $form_htm = RD . "/tpl/new/contacts_bottom_card.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }

		$r = $db->query("SELECT * FROM `contacts_bottom_new` WHERE `id` = $contact_id;");
        $n = $db->num_rows($r);
		if ($n > 0) {
			$id     = $db->result($r, 0, "id");
			$text   = $db->result($r, 0, "text");
			$icon   = $db->result($r, 0, "icon");
			$link   = $db->result($r, 0, "link");
			$status = $db->result($r, 0, "status");

			$form = str_replace("{id}", $id, $form);
			$form = str_replace("{text}", $text, $form);
			$form = str_replace("{icon_select}", $this->showIcontSelectList($icon), $form);
			$form = str_replace("{link}", $link, $form);
			$form = str_replace("{status}", ($status > 0) ? "checked" : "", $form);
		}

		return $form;
  	}
	
	function saveContactsBot($contact_id, $text, $icon, $link, $status)
    {
        $db = DbSingleton::getTokoDb();
	    $answer = 0; $err = "Помилка збереження даних!";
		if ($contact_id > 0) {
			$db->query("UPDATE `contacts_bottom_new` SET `text` = '$text', `type_contact` = '$icon', `icon` = '$icon', `link` = '$link', `status` = '$status' WHERE `id` = $contact_id;");
			$answer = 1; $err = "";
		}

		return array($answer, $err);
	}
	
	function dropContactsBot($contact_id)
    {
        $db = DbSingleton::getTokoDb();
	    $answer = 0; $err = "Помилка збереження даних!";
		if ($contact_id > 0) {
			$db->query("DELETE FROM `contacts_bottom_new` WHERE `id` = $contact_id;");
			$answer = 1; $err = "";
		}

		return array($answer, $err);
    }
	
	/*
	 * News
	 * */
	
	function getLangCaption($lang_id)
    {
        $db = DbSingleton::getTokoDb();
		$r = $db->query("SELECT `caption` FROM `lang` WHERE `id` = $lang_id LIMIT 1;");

        return $db->result($r, 0, "caption");
	}
	
	function showNewsList()
    {
        $db = DbSingleton::getTokoDb();
        $list = "";
	    $date = date("Y-m-d");
		$form = ""; $form_htm = RD . "/tpl/new/news.htm";
		if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }

		$r = $db->query("SELECT * FROM `news` ORDER BY `data` DESC;");
		$n = $db->num_rows($r);
		for ($i = 1; $i <= $n; $i++) {
			$id             = $db->result($r, $i - 1, "id");
			$lang           = $db->result($r, $i - 1, "lang_id");
			$lang           = $this->getLangCaption($lang);
			$caption        = $db->result($r, $i - 1, "caption");
			$short_desc     = $db->result($r, $i - 1, "short_desc");
			$data           = $db->result($r, $i - 1, "data");
            $color_data     = ($data > $date) ? "style='background:coral'" : "";
			$status         = $db->result($r, $i - 1, "status");
            $color_status   = ($status) ? "style='background:lightgreen'" : "style='background:lightpink'";
			$status         = $this->getStatusCaption($status);

			$list .= "
            <tr style='cursor:pointer' onClick='showNewsCard(\"$id\")'>
				<td $color_data>$data</td>
				<td>$lang</td>
				<td>$caption</td>
				<td>$short_desc</td>
				<td $color_status>$status</td>
			</tr>";
		}
		$form = str_replace("{news_range}", $list, $form);

		return $form;
	}
		
	function loadNewsList()
    {
        $db = DbSingleton::getTokoDb();
        $list = "";
	    $date = date("Y-m-d");

		$r = $db->query("SELECT * FROM `news` ORDER BY `data` DESC;");
		$n = $db->num_rows($r);
		for ($i = 1; $i <= $n; $i++) {
			$id             = $db->result($r, $i - 1, "id");
			$lang           = $this->getLangCaption($db->result($r, $i - 1, "lang_id"));
			$caption        = $db->result($r, $i - 1, "caption");
			$short_desc     = $db->result($r, $i - 1, "short_desc");
			$data           = $db->result($r, $i - 1, "data");
            $color_data     = ($data > $date) ? "style='background:coral'" : "";
			$status         = $db->result($r, $i - 1, "status");
            $color_status   = ($status) ? "style='background:lightgreen'" : "style='background:lightpink'";
			$status         = $this->getStatusCaption($status);

			$list .= "
            <tr style='cursor:pointer' onClick='showNewsCard(\"$id\")'>
				<td $color_data>$data</td>
				<td>$lang</td>
				<td>$caption</td>
				<td>$short_desc</td>
				<td $color_status>$status</td>
			</tr>";
		}

		return $list;
	}
	
	function newNewsCard($lang)
    {
        $db = DbSingleton::getTokoDb();
	    $date = date("Y-m-d");
		$r = $db->query("SELECT MAX(`id`) as mid FROM `news`;");
		$max_id = 0 + $db->result($r, 0, "mid") + 1;
		$db->query("INSERT INTO `news` (`id`, `status`, `data`, `lang_id`) VALUES ('$max_id', 0, '$date', '$lang');");

		return $max_id;
	}
	
	function showNewsCard($news_id)
    {
        $db = DbSingleton::getTokoDb();
        $form = ""; $form_htm = RD . "/tpl/new/news_card.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
		$r = $db->query("SELECT * FROM `news` WHERE `id` = '$news_id';");
        $n = $db->num_rows($r);

		if ($n > 0) {
			$id         = $db->result($r, 0, "id");
			$caption    = $db->result($r, 0, "caption");
			$lang_id    = $db->result($r, 0, "lang_id");
			$short_desc = $db->result($r, 0, "short_desc");
			$data       = $db->result($r, 0, "data");
			$status     = $db->result($r, 0, "status");
			$descr      = $db->result($r, 0, "desc");

			$form = str_replace("{id}", $id, $form);
			$form = str_replace("{caption}", $caption, $form);
			$form = str_replace("{lang_id}", $lang_id, $form);
			$form = str_replace("{lang_val}", $this->getLangCaption($lang_id), $form);
			$form = str_replace("{short}", $short_desc, $form);
			$form = str_replace("{data}", $data, $form);
			$form = str_replace("{descr}", $descr, $form);
			$form = str_replace("{status}", ($status > 0) ? "checked" : "", $form);

			$r2 = $db->query("SELECT `id` FROM `news_galery` WHERE `cat` = '$id' LIMIT 1;");
			$file_id = $db->result($r2, 0, "id");
			$form = str_replace("{file_id}", $file_id, $form);
		}

		return $form;
  	}
	
	function saveNews($news_id, $caption, $data, $short, $descr, $status)
    {
        $db = DbSingleton::getTokoDb();
	    $answer = 0; $err = "Помилка збереження даних!";
		if ($news_id > 0) {
			$db->query("UPDATE `news` SET `caption` = '$caption', `data` = '$data', `short_desc` = '$short', `desc` = '$descr', `status` = '$status' WHERE `id` = $news_id;");
			$answer = 1; $err = "";
		}
		return array($answer, $err);
	}
	
	function dropNews($news_id)
    {
        $db = DbSingleton::getTokoDb();
	    $answer = 0; $err = "Помилка збереження даних!";
		if ($news_id > 0) {
			$db->query("DELETE FROM `news` WHERE `id` = $news_id;");
			$answer = 1; $err = "";
		}
		return array($answer, $err);
    }
	
	function loadNewsPhoto($news_id,$lang_id)
    {
        $db = DbSingleton::getTokoDb();
        $list = "";
        $form = ""; $form_htm = RD . "/tpl/new/news_photo_block.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
		$r = $db->query("SELECT `id`, `caption` FROM `news_galery` WHERE `cat` = $news_id;");
        $n = $db->num_rows($r);
		for ($i = 1; $i <= $n; $i++) {
			$file  = $db->result($r, $i - 1, "id");
			$block = $form;
			$block = str_replace("{logo_name}", $db->result($r, $i - 1, "caption"), $block);
			$block = str_replace("{link}", "https://toko.ua/uploads/images/news/$lang_id/$news_id/$file.jpg", $block);
			$list .= $block;
		}
		if ($n == 0) {
		    $list = "<h3 class='text-center'>Зображення відсутнє</h3>";
		}
		return $list;
	}
	
	function deleteNewsLogo($news_id)
    {
        $db = DbSingleton::getTokoDb();
		$answer = 0; $err = "Помилка видалення даних!";
		if ($news_id > 0) {
			$db->query("DELETE FROM `news_galery` WHERE `cat` = $news_id;");
			$answer = 1; $err = "";
		}
		return array($answer, $err);
	}

	/*
	 * T_QUESTION
	 * */

    function showRequestsList()
    {
        $form = ""; $form_htm = RD . "/tpl/new/requests.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $form = str_replace("{requests_range}", $this->loadRequestsList(), $form);
        return $form;
    }

    function loadRequestsList()
    {
        $db = DbSingleton::getTokoDb();
        $list = "";
        $r = $db->query("SELECT * FROM `T2_QUESTIONS` ORDER BY `DATA_CREATE` DESC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id         = $db->result($r, $i - 1, "ID");
            $vin        = $db->result($r, $i - 1, "VIN");
            $text       = $db->result($r, $i - 1, "TEXT");
            $data       = $db->result($r, $i - 1, "DATA_CREATE");
            $data_upd   = $db->result($r, $i - 1, "DATA_UPDATE");
            $status     = $db->result($r, $i - 1, "STATUS");
            $style      = ($status > 0) ? "background: pink;" : "";

            $list .= "
            <tr style='cursor: pointer; $style' onClick='showRequestCard(\"$id\");'>
				<td>$id</td>
                <td>380*********</td>
				<td>$vin</td>
				<td>$text</td>
				<td>$data</td>
				<td>$data_upd</td>
			</tr>";
        }

        return $list;
    }

    function getMediaUserName($user_id)
    {
        $db = DbSingleton::getDb();
        $name = "";
        $r = $db->query("SELECT `name` FROM `media_users` WHERE `id` = $user_id LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $name = $db->result($r, 0, "name");
        }
        return $name;
    }

    function showRequestCard($request_id)
    {
        $db = DbSingleton::getTokoDb();
        session_start(); $user_id = $_SESSION["media_user_id"];
        $form = ""; $form_htm = RD . "/tpl/new/requests_card.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $r = $db->query("SELECT * FROM `T2_QUESTIONS` WHERE `ID` = $request_id;");
        $n = $db->num_rows($r);
        if ($n == 0) {
            $id = $user_use = $status = 0;
            $vin = $phone = $text = $data_create = $data_update = "";
        } else {
            $id             = $db->result($r, 0, "ID");
            $vin            = $db->result($r, 0, "VIN");
            $phone          = $db->result($r, 0, "PHONE");
            $text           = $db->result($r, 0, "TEXT");
            $data_create    = $db->result($r, 0, "DATA_CREATE");
            $data_update    = $db->result($r, 0, "DATA_UPDATE");
            $user_use       = $db->result($r, 0, "USER_USE");
            $status         = $db->result($r, 0, "STATUS");
        }

        if ($user_id != $user_use && $user_use > 0) {
            $form_htm = RD . "/tpl/dp_use_deny.htm";
            if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
            $form = str_replace("{user_name}",$this->getMediaUserName($user_use),$form);
            $admin_unlock = "";
            if ($user_id == 1 || $user_id == 2 || $user_id == 7) {
                $admin_unlock = "
                <button class='btn btn-sm btn-warning' onClick='unlockRequestCard(\"$id\");'><i class='fa fa-unlock'></i> Розблокувати</button>";
            }
            $form = str_replace("{admin_unlock}", $admin_unlock, $form);
        }

        $form = str_replace("{request_id}", $id, $form);
        $form = str_replace("{request_vin}", $vin, $form);
        $form = str_replace("{request_phone}", $phone, $form);
        $form = str_replace("{request_text}", $text, $form);
        $form = str_replace("{request_data_create}", $data_create, $form);
        $form = str_replace("{request_data_update}", $data_update, $form);
        $form = str_replace("{reqest_disabled}", ($status) ? "" : "disabled", $form);

        return $form;
    }

    function saveRequest($request_id, $vin, $phone, $text)
    {
        $db = DbSingleton::getTokoDb();
        if ($request_id == 0) {
            $answer = 0; $err = "Помилка збереження даних!";
        } else {
            $data_update = date("Y-m-d H:i:s");
            $db->query("UPDATE `T2_QUESTIONS` SET `VIN` = '$vin', `PHONE` = '$phone', `TEXT` = '$text', `DATA_UPDATE` = '$data_update', `STATUS` = '0' WHERE `ID` = $request_id;");
            $answer = 1; $err = "";
        }

        return array($answer, $err);
    }

    function dropRequest($request_id)
    {
        $db = DbSingleton::getTokoDb();
        $answer = 0; $err = "Помилка збереження даних!";
        if ($request_id > 0) {
            $db->query("DELETE FROM `T2_QUESTIONS` WHERE `ID` = $request_id LIMIT 1;");
            $answer = 1; $err = "";
        }

        return array($answer, $err);
    }

    function closeRequestCard($request_id)
    {
        $db = DbSingleton::getTokoDb();
        session_start();
        $user_id = $_SESSION["media_user_id"];
        if ($request_id > 0 && $user_id > 0) {
            $db->query("UPDATE `T2_QUESTIONS` SET `USER_USE` = '0' WHERE `ID` = $request_id;");
        }
        return 1;
    }

    function unlockRequestCard($request_id)
    {
        $db = DbSingleton::getTokoDb();
        session_start();
        $user_id = $_SESSION["media_user_id"];
        $answer = 0;
        if ($user_id == 1 || $user_id == 2 || $user_id == 7) {
            $db->query("UPDATE `T2_QUESTIONS` SET `USER_USE` = '0' WHERE `ID` = $request_id;");
            $answer = 1;
        }
        return $answer;
    }

    /*
     * T_REVIEWS
     * */

    function showReviewsList()
    {
        $form = ""; $form_htm = RD . "/tpl/new/reviews.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $form = str_replace("{reviews_range}", $this->loadReviewsList(), $form);
        return $form;
    }

    function loadReviewsList()
    {
        $db = DbSingleton::getTokoDb();
        $list = "";
        $r = $db->query("SELECT `ID`, `TITLE_RU`, `TITLE_UA`, `TITLE_EN`, `TEXT_RU`, `TEXT_UA`, `TEXT_EN`, `DATA`, `STATUS` FROM `T2_REVIEWS` ORDER BY `DATA` DESC;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id         = $db->result($r, $i - 1, "ID");
            $data       = $db->result($r, $i - 1, "DATA");
            $status     = $db->result($r, $i - 1, "STATUS");
            $title_ru   = $db->result($r, $i - 1, "TITLE_RU");
            $title_ua   = $db->result($r, $i - 1, "TITLE_UA");
            $title_en   = $db->result($r, $i - 1, "TITLE_EN");
            $text_ru    = $db->result($r, $i - 1, "TEXT_RU");
            $text_ua    = $db->result($r, $i - 1, "TEXT_UA");
            $text_en    = $db->result($r, $i - 1, "TEXT_EN");

            $status_ru  = $status_ua = $status_en = "-";
            if ($title_ru !== "" && $text_ru !== "") {
                $status_ru = "+";
            }
            if ($title_ua !== "" && $text_ua !== "") {
                $status_ua = "+";
            }
            if ($title_en !== "" && $text_en !== "") {
                $status_en = "+";
            }

            $list .= "
            <tr style='cursor: pointer' onClick='showReviewCard(\"$id\");'>
				<td>$id</td>
				<td>$title_ru</td>
				<td>$data</td>
				<td>$status_ru</td>
				<td>$status_ua</td>
				<td>$status_en</td>
				<td>$status</td>
			</tr>";
        }

        return $list;
    }

    function showReviewCard($id)
    {
        $db = DbSingleton::getTokoDb();
        $form = ""; $form_htm = RD . "/tpl/new/reviews_card.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }

        $r = $db->query("SELECT * FROM `T2_REVIEWS` WHERE `ID` = $id;");
        $n = $db->num_rows($r);
        if ($n == 0) {
            $id         = 0;
            $status     = 0;
            $disabled   = "disabled";
            $title      = $title_ua = $title_en = $text = $text_ua = $text_en = $data = $img = "";
            $t_ru = $t_ua = $t_en = $d_ru = $d_ua = $d_en = "";
        } else {
            $t_ru       = $db->result($r, 0, "T_RU");
            $t_ua       = $db->result($r, 0, "T_UA");
            $t_en       = $db->result($r, 0, "T_EN");
            $d_ru       = $db->result($r, 0, "D_RU");
            $d_ua       = $db->result($r, 0, "D_UA");
            $d_en       = $db->result($r, 0, "D_EN");
            $title      = $db->result($r, 0, "TITLE_RU");
            $title_ua   = $db->result($r, 0, "TITLE_UA");
            $title_en   = $db->result($r, 0, "TITLE_EN");
            $text       = $db->result($r, 0, "TEXT_RU");
            $text_ua    = $db->result($r, 0, "TEXT_UA");
            $text_en    = $db->result($r, 0, "TEXT_EN");
            $data       = $db->result($r, 0, "DATA");
            $status     = $db->result($r, 0, "STATUS");
            $img        = $db->result($r, 0, "IMG");
            $disabled   = "";
        }

        $form = str_replace("{review_id}", $id, $form);
        $form = str_replace("{review_t_ru}", $t_ru, $form);
        $form = str_replace("{review_t_ua}", $t_ua, $form);
        $form = str_replace("{review_t_en}", $t_en, $form);
        $form = str_replace("{review_d_ru}", $d_ru, $form);
        $form = str_replace("{review_d_ua}", $d_ua, $form);
        $form = str_replace("{review_d_en}", $d_en, $form);
        $form = str_replace("{review_title}", $title, $form);
        $form = str_replace("{review_title_ua}", $title_ua, $form);
        $form = str_replace("{review_title_en}", $title_en, $form);
        $form = str_replace("{review_text}", $text, $form);
        $form = str_replace("{review_text_ua}", $text_ua, $form);
        $form = str_replace("{review_text_en}", $text_en, $form);
        $form = str_replace("{review_data}", $data, $form);
        $form = str_replace("{review_status}", $status ? "checked" : "", $form);
        $form = str_replace("{review_image}", $img, $form);
        $form = str_replace("{review_remove_disabled}", $disabled, $form);

        return $form;
    }

    function saveReview($review_id, $t_ru, $t_ua, $t_en, $d_ru, $d_ua, $d_en, $title, $title_ua, $title_en, $text, $text_ua, $text_en, $data, $status)
    {
        $db = DbSingleton::getTokoDb();
        if ($text === "<p><br></p>") {
            $text = "";
        }
        if ($text_ua === "<p><br></p>") {
            $text_ua = "";
        }
        if ($text_en === "<p><br></p>") {
            $text_en = "";
        }

        if ($review_id == 0) {
            $r = $db->query("SELECT MAX(`ID`) as mid FROM `T2_REVIEWS`;");
            $max_id = 0 + $db->result($r, 0, "mid") + 1;
            $db->query("INSERT INTO `T2_REVIEWS` (`ID`, `TITLE_RU`, `TITLE_UA`, `TITLE_EN`, `DATA`, `STATUS`) VALUES ($max_id, '$title', '$title_ua', '$title_en', '$data', '$status');");
            $db->query('UPDATE `T2_REVIEWS` SET `TEXT_RU` = "' . $text.'" WHERE `ID` = "' . $max_id.'";');
            $db->query('UPDATE `T2_REVIEWS` SET `TEXT_UA` = "' . $text_ua.'" WHERE `ID` = "' . $max_id.'";');
            $db->query('UPDATE `T2_REVIEWS` SET `TEXT_EN` = "' . $text_en.'" WHERE `ID` = "' . $max_id.'";');
            $db->query('UPDATE `T2_REVIEWS` SET `T_RU` = "' . $t_ru.'" WHERE `ID` = "' . $max_id.'";');
            $db->query('UPDATE `T2_REVIEWS` SET `T_UA` = "' . $t_ua.'" WHERE `ID` = "' . $max_id.'";');
            $db->query('UPDATE `T2_REVIEWS` SET `T_EN` = "' . $t_en.'" WHERE `ID` = "' . $max_id.'";');
            $db->query('UPDATE `T2_REVIEWS` SET `D_RU` = "' . $d_ru.'" WHERE `ID` = "' . $max_id.'";');
            $db->query('UPDATE `T2_REVIEWS` SET `D_UA` = "' . $d_ua.'" WHERE `ID` = "' . $max_id.'";');
            $db->query('UPDATE `T2_REVIEWS` SET `D_EN` = "' . $d_en.'" WHERE `ID` = "' . $max_id.'";');
            $answer = 1; $err = "";
        } else {
            $db->query("UPDATE `T2_REVIEWS` SET `TITLE_RU` = '$title', `TITLE_UA` = '$title_ua', `TITLE_EN` = '$title_en', `DATA` = '$data', `STATUS` = '$status' WHERE `ID` = $review_id;");
            $db->query('UPDATE `T2_REVIEWS` SET `TEXT_RU` = "' . $text.'" WHERE `ID` = "' . $review_id.'";');
            $db->query('UPDATE `T2_REVIEWS` SET `TEXT_UA` = "' . $text_ua.'" WHERE `ID` = "' . $review_id.'";');
            $db->query('UPDATE `T2_REVIEWS` SET `TEXT_EN` = "' . $text_en.'" WHERE `ID` = "' . $review_id.'";');
            $db->query('UPDATE `T2_REVIEWS` SET `T_RU` = "' . $t_ru.'" WHERE `ID` = "' . $review_id.'";');
            $db->query('UPDATE `T2_REVIEWS` SET `T_UA` = "' . $t_ua.'" WHERE `ID` = "' . $review_id.'";');
            $db->query('UPDATE `T2_REVIEWS` SET `T_EN` = "' . $t_en.'" WHERE `ID` = "' . $review_id.'";');
            $db->query('UPDATE `T2_REVIEWS` SET `D_RU` = "' . $d_ru.'" WHERE `ID` = "' . $review_id.'";');
            $db->query('UPDATE `T2_REVIEWS` SET `D_UA` = "' . $d_ua.'" WHERE `ID` = "' . $review_id.'";');
            $db->query('UPDATE `T2_REVIEWS` SET `D_EN` = "' . $d_en.'" WHERE `ID` = "' . $review_id.'";');
            $answer = 1; $err = "";
        }

        return array($answer, $err);
    }

    function dropReview($review_id)
    {
        $db = DbSingleton::getTokoDb();
        $answer = 0; $err = "Помилка збереження даних!";
        if ($review_id > 0) {
            $db->query("DELETE FROM `T2_REVIEWS` WHERE `ID` = $review_id LIMIT 1;");
            $answer = 1; $err = "";
        }

        return array($answer, $err);
    }

    /*
     * New Configs
     * */

    function showConfigForm()
    {
        $db = DbSingleton::getTokoDb();
        $form = ""; $form_htm = RD . "/tpl/new/configs.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $list = "";
        $r = $db->query("SELECT `TEXT`, `STYLES`, `STATUS` FROM `T2_SITE_CONFIGS` WHERE `BLOCK` = 'site_warning_message' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n > 0) {
            $text   = $db->result($r, 0, "TEXT");
            $styles = $db->result($r, 0, "STYLES");
            $status = $db->result($r, 0, "STATUS");

            $list = "
            <table class='table'>
                <tr>
                    <td><b>TEXT:</b></td>
                    <td><input type='text' class='form-control' value='$text'></td>
                </tr>
                <tr>
                    <td><b>STYLES:</b></td>
                    <td><input type='text' class='form-control' value='$styles'></td>
                </tr>
                <tr>
                    <td><b>STATUS:</b></td>
                    <td><input type='text' class='form-control' value='$status'></td>
                </tr>
            </table>";
        }
        $form = str_replace("{configs_list}", $list, $form);

        return $form;
    }

}
