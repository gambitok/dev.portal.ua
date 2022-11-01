<?php

class SettingsNewClass
{

    public function showSeoFooterForm()
    {
        $db = DbSingleton::getTokoDb();
        $list = "";
        $form = ""; $form_htm = RD . "/tpl/seo_footer/form.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }

        $r = $db->query("SELECT * FROM `T2_SEO_FOOTER` WHERE 1");
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

    public function showSeoFooterCard($id)
    {
        $db = DbSingleton::getTokoDb();
        if ((int)$id === 0) {
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
        $form = str_replace(array("{seo_id}", "{seo_router}", "{seo_link}", "{seo_text_ru}", "{seo_text_ua}", "{seo_text_en}", "{seo_user}", "{seo_data}"), array($id, $router, $link, $text_ru, $text_ua, $text_en, $this->getMediaUserName($user_id), $date), $form);

        return $form;
    }

    public function saveSeoFooter($id, $router, $link, $text_ru, $text_ua, $text_en): array
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

    public function dropSeoFooter($id): array
    {
        $db = DbSingleton::getTokoDb();
        $answer = 0; $err = "Помилка збереження даних!";
        if ($id > 0) {
            $db->query("DELETE FROM `T2_SEO_FOOTER` WHERE `id` = $id;");
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

    public function showSeoTextForm()
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

        $form = str_replace(array("{seo_range}", "{seo_title_range}", "{seo_generate_range}"), array($list, $list_title, $list_generate), $form);

        return $form;
    }

    public function showSeoTitleCard($id)
    {
        $db = DbSingleton::getTokoDb();
        if ((int)$id === 0) {
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
        $form = str_replace(array("{seo_id}", "{seo_router}", "{seo_link}", "{seo_text_ru}", "{seo_text_ua}", "{seo_text_en}", "{seo_descr_ru}", "{seo_descr_ua}", "{seo_descr_en}", "{seo_user}", "{seo_data}"), array($id, $router, $link, $title_ru, $title_ua, $title_en, $descr_ru, $descr_ua, $descr_en, $this->getMediaUserName($user_id), $date), $form);

        return $form;
    }

    public function saveSeoTitle($id, $router, $link, $text_ru, $text_ua, $text_en, $descr_ru, $descr_ua, $descr_en): array
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

    public function dropSeoTitle($id): array
    {
        $db = DbSingleton::getTokoDb();
        $answer = 0; $err = "Помилка збереження даних!";
        if ($id > 0) {
            $db->query("DELETE FROM `T2_SEO_TITLE` WHERE `id` = $id;");
            $answer = 1; $err = "";
        }

        return array($answer, $err);
    }

    public function showSeoTextCard($id)
    {
        $db = DbSingleton::getTokoDb();
        if ((int)$id === 0) {
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
        $form = str_replace(array("{seo_id}", "{seo_router}", "{seo_link}", "{seo_text_ru}", "{seo_text_ua}", "{seo_text_en}", "{seo_user}", "{seo_data}"), array($id, $router, $link, $text_ru, $text_ua, $text_en, $this->getMediaUserName($user_id), $date), $form);

        return $form;
    }

    public function saveSeoText($id, $router, $link, $text_ru, $text_ua, $text_en): array
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

    public function dropSeoText($id): array
    {
        $db = DbSingleton::getTokoDb();
        $answer = 0; $err = "Помилка збереження даних!";
        if ($id > 0) {
            $db->query("DELETE FROM `T2_SEO_TEXT` WHERE `id` = $id;");
            $answer = 1; $err = "";
        }

        return array($answer, $err);
    }

    public function showSeoGenerateCard($id)
    {
        $db = DbSingleton::getTokoDb();
        if ((int)$id === 0) {
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
        $form = str_replace(array("{seo_id}", "{seo_router}", "{seo_link}", "{seo_text_ru}", "{seo_text_ua}", "{seo_text_en}", "{seo_user}", "{seo_data}"), array($id, $router, $link, $text_ru, $text_ua, $text_en, $this->getMediaUserName($user_id), $date), $form);

        return $form;
    }

    public function saveSeoGenerate($id, $router, $link, $text_ru, $text_ua, $text_en): array
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

    public function dropSeoGenerate($id): array
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

    public function showLanguageList()
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

    public function loadLanguageList(): string
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

    public function newLanguageCard($lang_var)
    {
        $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT MAX(`id`) as mid FROM `new_lang_wd`;");
        $max_id = 0 + $db->result($r, 0, "mid") + 1;
        $db->query("INSERT INTO `new_lang_wd` (`id`, `variable`) VALUES ('$max_id', '$lang_var');");
        return $max_id;
    }

    public function showLanguageCard($id)
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

        $form = str_replace(array("{id}", "{lang_var}", "{lang_ru}", "{lang_ua}", "{lang_eng}"), array($id, $lang_var, $lang_ru, $lang_ua, $lang_eng), $form);

        return $form;
    }

    public function saveLanguage($lang_id, $lang_ru, $lang_ua, $lang_eng): array
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

    public function dropLanguage($lang_id): array
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
	
	public function getLangCap($lang_id)
    {
        $db = DbSingleton::getTokoDb();
		$r = $db->query("SELECT `caption` FROM `new_lang` WHERE `id` = $lang_id;");
        return $db->result($r, 0, "caption");
	}
	
	public function showContactsList()
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
		
	public function loadContactsList(): string
    {
        $db = DbSingleton::getTokoDb();
        $list = "";

		$r = $db->query("SELECT * FROM `contacts_new` WHERE `status` = 1;");
		$n = $db->num_rows($r);
		for ($i = 1; $i <= $n; $i++) {
			$id = $db->result($r, $i - 1, "id");
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
	
	public function newContactsCard($lang_var)
    {
        $db = DbSingleton::getTokoDb();
		$r = $db->query("SELECT MAX(`id`) as mid FROM `contacts_new`;");
		$max_id = 0 + $db->result($r, 0, "mid") + 1;
		$db->query("INSERT INTO `contacts_new` (`id`, `status`, `lang_id`) VALUES ('$max_id', 1, '$lang_var');");
		return $max_id;
	}
	
	public function showContactsCard($contact_id)
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

            $form = str_replace(array("{id}", "{title}", "{address}", "{schedule}", "{phone}"), array($id, $title, $address, $schedule, $phone), $form);
		}

		return $form;
  	}
	
	public function saveContacts($contact_id, $title, $address, $schedule, $phone): array
    {
        $db = DbSingleton::getTokoDb();
	    $answer = 0; $err = "Помилка збереження даних!";
		if ($contact_id > 0) {
			$db->query("UPDATE `contacts_new` SET `title` = '$title', `address` = '$address', `schedule` = '$schedule', `phone` = '$phone' WHERE `id` = $contact_id;");
			$answer = 1; $err = "";
		}
		return array($answer, $err);
	}
	
	public function dropContacts($contact_id): array
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
	
	public function showLocations()
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
	
	public function getStatusCaption($status): string
    {
        return $status ? "Активний" : "Відключений";
	}
	
	public function showIcontSelectList($sel_id): string
    {
        $db = DbSingleton::getTokoDb();
        $list = "";

		$r = $db->query("SELECT `id`, `name` FROM `new_icons` WHERE 1;");
		$n = $db->num_rows($r);
		for ($i = 1; $i <= $n; $i++) {
			$id     = (int)$db->result($r, $i - 1, "id");
			$name   = $db->result($r, $i - 1, "name");
			$sel    = ($id === (int)$sel_id) ? "selected" : "";

			$list .= "
                <option value='$id' $sel>$name</option>
            ";
		}

		return $list;
	}
	
	public function getIcon($id): string
    {
        $db = DbSingleton::getTokoDb();
		$r = $db->query("SELECT `name`, `icon` FROM `new_icons` WHERE `id` = $id LIMIT 1;");
		$name = $db->result($r, 0, "name");
		$icon = $db->result($r, 0, "icon");

        return "<i class='fa $icon'> $name</i>";
	}
	
	public function showContactsBotList()
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
		
	public function loadContactsBotList(): string
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
	
	public function newContactsBotCard()
    {
        $db = DbSingleton::getTokoDb();
		$r = $db->query("SELECT MAX(`id`) as mid FROM `contacts_bottom_new`;");
		$max_id = $db->result($r, 0, "mid") + 1;
		$db->query("INSERT INTO `contacts_bottom_new` (`id`, `status`) VALUES ('$max_id', 1);");

		return $max_id;
	}
	
	public function showContactsBotCard($contact_id)
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

            $form = str_replace(
                array("{id}", "{text}", "{icon_select}", "{link}", "{status}"),
                array($id, $text, $this->showIcontSelectList($icon), $link, ($status > 0) ? "checked" : ""), $form);
		}

		return $form;
  	}
	
	public function saveContactsBot($contact_id, $text, $icon, $link, $status): array
    {
        $db = DbSingleton::getTokoDb();
	    $answer = 0; $err = "Помилка збереження даних!";
		if ($contact_id > 0) {
			$db->query("UPDATE `contacts_bottom_new` SET `text` = '$text', `type_contact` = '$icon', `icon` = '$icon', `link` = '$link', `status` = '$status' WHERE `id` = $contact_id;");
			$answer = 1; $err = "";
		}
		return array($answer, $err);
	}
	
	public function dropContactsBot($contact_id): array
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
	
	public function getLangCaption($lang_id)
    {
        $db = DbSingleton::getTokoDb();
		$r = $db->query("SELECT `caption` FROM `lang` WHERE `id` = $lang_id LIMIT 1;");
        return $db->result($r, 0, "caption");
	}
	
	public function showNewsList()
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
		
	public function loadNewsList(): string
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
	
	public function newNewsCard($lang)
    {
        $db = DbSingleton::getTokoDb();
	    $date = date("Y-m-d");
		$r = $db->query("SELECT MAX(`id`) as mid FROM `news`;");
		$max_id = 0 + $db->result($r, 0, "mid") + 1;
		$db->query("INSERT INTO `news` (`id`, `status`, `data`, `lang_id`) VALUES ('$max_id', 0, '$date', '$lang');");

		return $max_id;
	}
	
	public function showNewsCard($news_id)
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

            $form = str_replace(array("{id}", "{caption}", "{lang_id}", "{lang_val}", "{short}", "{data}", "{descr}", "{status}"), array($id, $caption, $lang_id, $this->getLangCaption($lang_id), $short_desc, $data, $descr, ($status > 0) ? "checked" : ""), $form);

			$r2 = $db->query("SELECT `id` FROM `news_galery` WHERE `cat` = '$id' LIMIT 1;");
			$file_id = $db->result($r2, 0, "id");
			$form = str_replace("{file_id}", $file_id, $form);
		}

		return $form;
  	}
	
	public function saveNews($news_id, $caption, $data, $short, $descr, $status): array
    {
        $db = DbSingleton::getTokoDb();
	    $answer = 0; $err = "Помилка збереження даних!";
		if ($news_id > 0) {
			$db->query("UPDATE `news` SET `caption` = '$caption', `data` = '$data', `short_desc` = '$short', `desc` = '$descr', `status` = '$status' WHERE `id` = $news_id;");
			$answer = 1; $err = "";
		}
		return array($answer, $err);
	}
	
	public function dropNews($news_id): array
    {
        $db = DbSingleton::getTokoDb();
	    $answer = 0; $err = "Помилка збереження даних!";
		if ($news_id > 0) {
			$db->query("DELETE FROM `news` WHERE `id` = $news_id;");
			$answer = 1; $err = "";
		}
		return array($answer, $err);
    }
	
	public function loadNewsPhoto($news_id,$lang_id): string
    {
        $db = DbSingleton::getTokoDb();
        $list = "";
        $form = ""; $form_htm = RD . "/tpl/new/news_photo_block.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }

		$r = $db->query("SELECT `id`, `caption` FROM `news_galery` WHERE `cat` = $news_id;");
        $n = (int)$db->num_rows($r);
		for ($i = 1; $i <= $n; $i++) {
			$file  = $db->result($r, $i - 1, "id");
			$block = $form;
            $block = str_replace(
                array("{logo_name}", "{link}"),
                array($db->result($r, $i - 1, "caption"), "https://toko.ua/uploads/images/news/$lang_id/$news_id/$file.jpg"), $block);
			$list .= $block;
		}
		if ($n === 0) {
		    $list = "<h3 class='text-center'>Зображення відсутнє</h3>";
		}

		return $list;
	}
	
	public function deleteNewsLogo($news_id): array
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

    public function showRequestsList()
    {
        $form = ""; $form_htm = RD . "/tpl/new/requests.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $form = str_replace("{requests_range}", $this->loadRequestsList(), $form);
        return $form;
    }

    public function loadRequestsList(): string
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

    public function getMediaUserName($user_id)
    {
        $db = DbSingleton::getDb();
        $name = "";
        $r = $db->query("SELECT `name` FROM `media_users` WHERE `id` = $user_id LIMIT 1;");
        $n = (int)$db->num_rows($r);
        if ($n === 1) {
            $name = $db->result($r, 0, "name");
        }
        return $name;
    }

    public function showRequestCard($request_id)
    {
        $db = DbSingleton::getTokoDb();
        session_start();
        $user_id = (int)$_SESSION["media_user_id"];
        $form = ""; $form_htm = RD . "/tpl/new/requests_card.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }

        $r = $db->query("SELECT * FROM `T2_QUESTIONS` WHERE `ID` = $request_id;");
        $n = (int)$db->num_rows($r);
        if ($n === 0) {
            $id = $user_use = $status = 0;
            $vin = $phone = $text = $data_create = $data_update = "";
        } else {
            $id             = $db->result($r, 0, "ID");
            $vin            = $db->result($r, 0, "VIN");
            $phone          = $db->result($r, 0, "PHONE");
            $text           = $db->result($r, 0, "TEXT");
            $data_create    = $db->result($r, 0, "DATA_CREATE");
            $data_update    = $db->result($r, 0, "DATA_UPDATE");
            $user_use       = (int)$db->result($r, 0, "USER_USE");
            $status         = $db->result($r, 0, "STATUS");
        }

        if ($user_id !== $user_use && $user_use > 0) {
            $form_htm = RD . "/tpl/dp_use_deny.htm";
            if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
            $form = str_replace("{user_name}",$this->getMediaUserName($user_use),$form);
            $admin_unlock = "";
            if ($user_id === 1 || $user_id === 2 || $user_id === 7) {
                $admin_unlock = "
                <button class='btn btn-sm btn-warning' onClick='unlockRequestCard(\"$id\");'><i class='fa fa-unlock'></i> Розблокувати</button>";
            }
            $form = str_replace("{admin_unlock}", $admin_unlock, $form);
        }

        $form = str_replace(array("{request_id}", "{request_vin}", "{request_phone}", "{request_text}", "{request_data_create}", "{request_data_update}", "{reqest_disabled}"), array($id, $vin, $phone, $text, $data_create, $data_update, ($status) ? "" : "disabled"), $form);

        return $form;
    }

    public function saveRequest($request_id, $vin, $phone, $text): array
    {
        $db = DbSingleton::getTokoDb();
        if ((int)$request_id === 0) {
            $answer = 0; $err = "Помилка збереження даних!";
        } else {
            $data_update = date("Y-m-d H:i:s");
            $db->query("UPDATE `T2_QUESTIONS` SET `VIN` = '$vin', `PHONE` = '$phone', `TEXT` = '$text', `DATA_UPDATE` = '$data_update', `STATUS` = '0' WHERE `ID` = $request_id;");
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

    public function dropRequest($request_id): array
    {
        $db = DbSingleton::getTokoDb();
        $answer = 0; $err = "Помилка збереження даних!";
        if ($request_id > 0) {
            $db->query("DELETE FROM `T2_QUESTIONS` WHERE `ID` = $request_id LIMIT 1;");
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

    public function closeRequestCard($request_id): int
    {
        $db = DbSingleton::getTokoDb();
        session_start();
        $user_id = $_SESSION["media_user_id"];
        if ($request_id > 0 && $user_id > 0) {
            $db->query("UPDATE `T2_QUESTIONS` SET `USER_USE` = '0' WHERE `ID` = $request_id;");
        }
        return 1;
    }

    public function unlockRequestCard($request_id): int
    {
        $db = DbSingleton::getTokoDb();
        session_start();
        $user_id = (int)$_SESSION["media_user_id"];
        $answer = 0;
        if ($user_id === 1 || $user_id === 2 || $user_id === 7) {
            $db->query("UPDATE `T2_QUESTIONS` SET `USER_USE` = '0' WHERE `ID` = $request_id;");
            $answer = 1;
        }
        return $answer;
    }

    /*
     * T_REVIEWS
     * */

    public function showReviewsList()
    {
        $form = ""; $form_htm = RD . "/tpl/new/reviews.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }
        $form = str_replace("{reviews_range}", $this->loadReviewsList(), $form);
        return $form;
    }

    public function loadReviewsList(): string
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

    /*
     * ========================
     * */

    public function showReviewCardInfo($id, $lang_id)
    {
        $id = (int)$id;
        $lang_id = (int)$lang_id;

        $db = DbSingleton::getTokoDb();
        $form = ""; $form_htm = RD . "/tpl/new/reviews_lang_card.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }

        $r = $db->query("SELECT * FROM `T2_REVIEWS` WHERE `ID` = $id;");
        $n = (int)$db->num_rows($r);

        if ($lang_id === 2) {
            $prefix = "UA";
        } elseif ($lang_id === 3) {
            $prefix = "EN";
        } else {
            $prefix = "RU";
        }

        if ($n === 0) {
            $t = $d = $text = "";
        } else {
            $t          = $db->result($r, 0, "T_$prefix");
            $d          = $db->result($r, 0, "D_$prefix");
            $text       = $db->result($r, 0, "TEXT_$prefix");
        }

        $form = str_replace(
            array("{lang_id}", "{title_lang}", "{review_t}", "{review_d}", "{review_text}"),
            array($lang_id, "МОВА - $prefix", $t, $d, $text)
            , $form);

        return $form;
    }

    public function saveReviewCardInfo($review_id, $lang_id, $t, $d, $title, $text): array
    {
        $db = DbSingleton::getTokoDb();
        $review_id = (int)$review_id;
        $lang_id = (int)$lang_id;

        if ($lang_id === 2) {
            $prefix = "UA";
        } elseif ($lang_id === 3) {
            $prefix = "EN";
        } else {
            $prefix = "RU";
        }

        if ($text === "<p><br></p>") {
            $text = "";
        }

        if ($review_id === 0) {
            $r = $db->query("SELECT MAX(`ID`) as mid FROM `T2_REVIEWS`;");
            $max_id = 0 + $db->result($r, 0, "mid") + 1;
            $db->query("INSERT INTO `T2_REVIEWS` (`ID`, `TITLE_$prefix`) VALUES ($max_id, \"$title\");");
            $db->query('UPDATE `T2_REVIEWS` SET `TEXT_$prefix` = "' . $text.'" WHERE `ID` = "' . $max_id.'";');
            $db->query('UPDATE `T2_REVIEWS` SET `T_$prefix` = "' . $t.'" WHERE `ID` = "' . $max_id.'";');
            $db->query('UPDATE `T2_REVIEWS` SET `D_$prefix` = "' . $d.'" WHERE `ID` = "' . $max_id.'";');
            $answer = 1; $err = "";
        } else {
            $db->query("UPDATE `T2_REVIEWS` SET `TITLE_$prefix` = \"$title\" WHERE `ID` = $review_id;");
            $db->query('UPDATE `T2_REVIEWS` SET `TEXT_' . $prefix . '` = "' . $text.'" WHERE `ID` = "' . $review_id.'";');
            $db->query('UPDATE `T2_REVIEWS` SET `T_' . $prefix . '` = "' . $t.'" WHERE `ID` = "' . $review_id.'";');
            $db->query('UPDATE `T2_REVIEWS` SET `D_' . $prefix . '` = "' . $d.'" WHERE `ID` = "' . $review_id.'";');
            $answer = 1; $err = "";
        }

        return array($answer, $err);
    }

    public function showReviewCard($id)
    {
        $db = DbSingleton::getTokoDb();
        $form = ""; $form_htm = RD . "/tpl/new/reviews_card.htm";
        if (file_exists($form_htm)) { $form = file_get_contents($form_htm); }

        $r = $db->query("SELECT * FROM `T2_REVIEWS` WHERE `ID` = $id;");
        $n = (int)$db->num_rows($r);
        if ($n === 0) {
            $id         = 0;
            $status     = 0;
            $disabled   = "disabled";
            $title      = $title_ua = $title_en = $data = $data_create = $img = "";
        } else {
            $title      = $db->result($r, 0, "TITLE_RU");
            $title_ua   = $db->result($r, 0, "TITLE_UA");
            $title_en   = $db->result($r, 0, "TITLE_EN");
            $data       = $db->result($r, 0, "DATA");
            $data_create= $db->result($r, 0, "DATA_CREATE");
            $status     = $db->result($r, 0, "STATUS");
            $img        = $db->result($r, 0, "IMG");
            $disabled   = "";
        }

        $form = str_replace(
            array("{review_id}", "{review_title}", "{review_title_ua}", "{review_title_en}", "{review_data}", "{review_data_create}", "{review_status}", "{review_image}", "{review_remove_disabled}", "{groups_list}"),
            array($id, $title, $title_ua, $title_en, $data, $data_create, $status ? "checked" : "", $img, $disabled, $this->getGroupsList($this->getGroupReviewList($id)))
        , $form);

        return $form;
    }
    
    public function getGroupReviewList($review_id): array
    {
        $db = DbSingleton::getTokoDb();

        $r = $db->query("SELECT `GROUP_ID` FROM `T2_GROUP_REVIEW` WHERE `REVIEW_ID` = $review_id;");
        $n = $db->num_rows($r);
        $group_ids = [];
        for ($i = 1; $i <= $n; $i++) {
            $group_ids[] = $db->result($r, $i - 1, "GROUP_ID");
        }

        return $group_ids;
    }

    public function getGroupsList($group_ids = []): string
    {
        $db = DbSingleton::getTokoDb();
        $list = "";

        $r = $db->query("SELECT `GROUP_ID`, `TEX_RU` FROM `T2_TREE_GROUP_EXIST` WHERE `STATUS` = 1;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $group_id   = $db->result($r, $i - 1, "GROUP_ID");
            $group_name = $db->result($r, $i - 1, "TEX_RU");

            $sel = "";
            if (in_array($group_id, $group_ids, true)) {
                $sel = "selected='selected'";
            }

            $list .= "
            <option value='$group_id' $sel>$group_name</option>";
        }

        return $list;
    }

    public function saveReview($review_id, $title, $title_ua, $title_en, $data, $data_create, $status, $groups): array
    {
        $db = DbSingleton::getTokoDb();

        if ((int)$review_id === 0) {
            $r = $db->query("SELECT MAX(`ID`) as mid FROM `T2_REVIEWS`;");
            $max_id = 0 + $db->result($r, 0, "mid") + 1;
            $db->query("INSERT INTO `T2_REVIEWS` (`ID`, `TITLE_RU`, `TITLE_UA`, `TITLE_EN`, `DATA`, `DATA_CREATE`, `STATUS`) VALUES ($max_id, \"$title\", \"$title_ua\", \"$title_en\", '$data', '$data_create', '$status');");
            $answer = 1; $err = "";
        } else {
            $db->query("UPDATE `T2_REVIEWS` SET `TITLE_RU` = \"$title\", `TITLE_UA` = \"$title_ua\", `TITLE_EN` = \"$title_en\", `DATA` = '$data', `DATA_CREATE` = '$data_create', `STATUS` = '$status' WHERE `ID` = $review_id;");
            $answer = 1; $err = "";
        }

        if (!empty($groups)) {
            foreach ($groups as $group_id) {
                $db->query("INSERT INTO `T2_GROUP_REVIEW` (`GROUP_ID`, `REVIEW_ID`) VALUES ('$group_id', '$review_id');");
            }
        }

        return array($answer, $err);
    }

    public function choseReviewCardImage($review_id, $file_name): array
    {
        $db = DbSingleton::getTokoDb();

        $answer = 0; $err = "Error";

        if ($review_id > 0) {

            $db->query("UPDATE `T2_REVIEWS` SET `IMG` = '$file_name' WHERE `ID` = $review_id LIMIT 1;");

            $answer = 1; $err = "";
        }

        return array($answer, $err);

    }

    public function dropReview($review_id): array
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

    public function showConfigForm()
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
