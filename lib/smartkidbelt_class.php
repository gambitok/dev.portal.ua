<?php

class smartkidbelt {

    function showBrandsList() { $db = DbSingleton::getTokoDb();
        $list = "";
        $r = $db->query("SELECT * FROM `SMART_BRANDS` WHERE 1;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $brand_id=$db->result($r,$i-1,"BRAND_ID");
            $brand_name=$db->result($r,$i-1,"BRAND_NAME");
            $brand_text=$db->result($r,$i-1,"BRAND_TEXT");
            $brand_image=$db->result($r,$i-1,"IMAGE");
            $brand_pos=$db->result($r,$i-1,"POSITION");
            $brand_status=$db->result($r,$i-1,"STATUS");
            $list.="<tr class='pointer' onclick='showBrandCard(\"$brand_id\")'>
                <td>$brand_id</td>
                <td>$brand_name</td>
                <td>$brand_text</td>
                <td>$brand_image</td>
                <td>$brand_pos</td>
                <td>$brand_status</td>
            </tr>";
        }
        return $list;
    }

    function showBrandCard($brand_id) { $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT * FROM `SMART_BRANDS` WHERE `BRAND_ID`='$brand_id' LIMIT 1;");
        $brand_name=$db->result($r,0,"BRAND_NAME");
        $brand_text=$db->result($r,0,"BRAND_TEXT");
        $brand_pos=$db->result($r,0,"POSITION");
        $brand_status=$db->result($r,0,"STATUS");
        $form = ""; $form_htm=RD."/tpl/smart_brands_card.htm";
        if (file_exists("$form_htm")) {$form = file_get_contents($form_htm);}
        $form = str_replace("{brand_id}",$brand_id,$form);
        $form = str_replace("{brand_name}",$brand_name,$form);
        $form = str_replace("{brand_text}",$brand_text,$form);
        $form = str_replace("{brand_pos}",$brand_pos,$form);
        $form = str_replace("{brand_status}",$brand_status ? "checked='checked'" : "",$form);
        $form = str_replace("{store_range}",$this->getStoreRange($brand_id),$form);
        $form = str_replace("{image_disabled}",$brand_id==0 ? "disabled" : "",$form);
        return $form;
    }

    function saveBrandCard($brand_id, $brand_name, $brand_text, $brand_pos, $brand_status) { $db = DbSingleton::getTokoDb();
        $answer = 0; $err = "Помилка збереження даних!";
        if ($brand_id>0) {
            $db->query("UPDATE `SMART_BRANDS` SET `BRAND_NAME`='$brand_name', `BRAND_TEXT`='$brand_text', `POSITION`='$brand_pos', `STATUS`='$brand_status' WHERE `BRAND_ID`='$brand_id' LIMIT 1;");
            $answer = 1; $err = "";
        }
        if ($brand_id==0) {
            $r = $db->query("SELECT MAX(`BRAND_ID`) as mid FROM `SMART_BRANDS`;");
            $brand_id=0+$db->result($r,0,"mid")+1;
            $db->query("INSERT INTO `SMART_BRANDS` (`BRAND_ID`, `BRAND_NAME`, `BRAND_TEXT`, `POSITION`, `STATUS`) VALUES ('$brand_id', '$brand_name', '$brand_text', '$brand_pos', '$brand_status');");
            $answer = 1; $err = "";
        }
        return array($answer, $err, $brand_id);
    }

    function getStoreRange($brand_id) { $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT * FROM `SMART_STORES` WHERE `BRAND_ID`='$brand_id';");
        $n = $db->num_rows($r);
        $list = "";
        for ($i = 1; $i <= $n; $i++) {
            $store_id = $db->result($r, $i - 1, "STORE_ID");
            $address = $db->result($r, $i - 1, "ADDRESS");
            $position = $db->result($r, $i - 1, "POSITION");
            $status = $db->result($r, $i - 1, "STATUS");
            $list.="<tr class='pointer' onclick='showStoreCard(\"$store_id\")'>
                <td>$store_id</td>
                <td>$address</td>
                <td>$position</td>
                <td>$status</td>
            </tr>";
        }
        return $list;
    }

    function getSmartBrandName($brand_id) { $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT * FROM `SMART_BRANDS` WHERE `BRAND_ID`='$brand_id' LIMIT 1;");
        return $db->result($r,0,"BRAND_NAME");
    }

    function getSmartBrandImage($brand_id) { $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT * FROM `SMART_BRANDS` WHERE `BRAND_ID`='$brand_id' LIMIT 1;");
        $brand_image = $db->result($r,0,"IMAGE");
        return "<img src='https://smartkidbelt.com.ua/images/smart_brands/$brand_image' alt='$brand_image' width='300'>";
    }

    function deleteSmartPhoto($brand_id) { $db = DbSingleton::getTokoDb();
        $answer = 0; $err = "Помилка збереження даних!";
        if ($brand_id > 0) {
            $r = $db->query("SELECT * FROM `SMART_BRANDS` WHERE `BRAND_ID`='$brand_id' LIMIT 1;");
            $brand_image = $db->result($r,0,"IMAGE");
            $targetDir = RD."/uploads/images/smart_brands/";
            $targetFile = $targetDir.$brand_image;
            unlink($targetFile);
            $db->query("UPDATE `SMART_BRANDS` SET `IMAGE`='' WHERE `BRAND_ID`='$brand_id' LIMIT 1;");
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

    function showStoreCard($store_id, $brand_id) { $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT * FROM `SMART_STORES` WHERE `STORE_ID`='$store_id' LIMIT 1;");
        $address = $db->result($r,0,"ADDRESS");
        $store_pos = $db->result($r,0,"POSITION");
        $store_status = $db->result($r,0,"STATUS");
        $form = ""; $form_htm=RD."/tpl/smart_brands_store_card.htm";
        if (file_exists("$form_htm")) {$form = file_get_contents($form_htm);}
        $form = str_replace("{store_id}",$store_id,$form);
        $form = str_replace("{brand_name}",$this->getSmartBrandName($brand_id),$form);
        $form = str_replace("{address}",$address,$form);
        $form = str_replace("{store_pos}",$store_pos,$form);
        $form = str_replace("{store_status}",$store_status ? "checked='checked'" : "",$form);
        return $form;
    }

    function saveStoreCard($store_id, $brand_id, $address, $store_pos, $store_status) { $db = DbSingleton::getTokoDb();
        $answer = 0; $err = "Помилка збереження даних!";
        if ($store_id > 0) {
            $db->query("UPDATE `SMART_STORES` SET `BRAND_ID`='$brand_id', `ADDRESS`='$address', `POSITION`='$store_pos', `STATUS`='$store_status' WHERE `STORE_ID`='$store_id' LIMIT 1;");
            $answer = 1; $err = "";
        }
        if ($store_id == 0) {
            $r = $db->query("SELECT MAX(`STORE_ID`) as mid FROM `SMART_STORES`;");
            $store_id=0+$db->result($r,0,"mid")+1;
            $db->query("INSERT INTO `SMART_STORES` (`STORE_ID`, `BRAND_ID`, `ADDRESS`, `POSITION`, `STATUS`) VALUES ('$store_id', '$brand_id', ' $address', '$store_pos', '$store_status');");
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

    function showNavList() { $db = DbSingleton::getTokoDb();
        $list = "";
        $r = $db->query("SELECT * FROM `SMART_NAV` WHERE 1;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id=$db->result($r,$i-1,"ID");
            $text=$db->result($r,$i-1,"TEXT");
            $link=$db->result($r,$i-1,"LINK");
            $pos=$db->result($r,$i-1,"POSITION");
            $status=$db->result($r,$i-1,"STATUS");
            $list.="<tr class='pointer' onclick='showNavCard(\"$id\")'>
                <td>$id</td>
                <td>$text</td>
                <td>$link</td>
                <td>$pos</td>
                <td>$status</td>
            </tr>";
        }
        return $list;
    }

    function showNavCard($id) { $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT * FROM `SMART_NAV` WHERE `ID`='$id' LIMIT 1;");
        $text=$db->result($r,0,"TEXT");
        $text_ru=$db->result($r,0,"TEXT_RU");
        $link=$db->result($r,0,"LINK");
        $pos=$db->result($r,0,"POSITION");
        $status=$db->result($r,0,"STATUS");
        $form = ""; $form_htm=RD."/tpl/smart_nav_card.htm";
        if (file_exists("$form_htm")){$form = file_get_contents($form_htm);}
        $form = str_replace("{nav_id}",$id,$form);
        $form = str_replace("{nav_text}",$text,$form);
        $form = str_replace("{nav_text_ru}",$text_ru,$form);
        $form = str_replace("{nav_link}",$link,$form);
        $form = str_replace("{nav_pos}",$pos,$form);
        $form = str_replace("{nav_status}",$status ? "checked" : "",$form);
        return $form;
    }

    function saveNavCard($nav_id, $nav_text, $nav_text_ru, $nav_link, $nav_pos, $nav_status) { $db = DbSingleton::getTokoDb();
        $answer = 0; $err = "Помилка збереження даних!";
        if ($nav_id>0) {
            $db->query("UPDATE `SMART_NAV` SET `TEXT`='$nav_text', `TEXT_RU`='$nav_text_ru', `LINK`='$nav_link', `POSITION`='$nav_pos', `STATUS`='$nav_status' WHERE `ID`='$nav_id' LIMIT 1;");
            $answer = 1; $err = "";
        }
        if ($nav_id==0) {
            $r = $db->query("SELECT MAX(`ID`) as mid FROM `SMART_NAV`;");
            $nav_id=0+$db->result($r,0,"mid")+1;
            $db->query("INSERT INTO `SMART_NAV` (`ID`, `TEXT`, `TEXT_RU`, `LINK`, `POSITION`, `STATUS`) VALUES ('$nav_id', '$nav_text', '$nav_text_ru', '$nav_link', '$nav_pos', '$nav_status');");
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

    function showFaqList() { $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT * FROM `SMART_FAQ` WHERE 1;");
        $n = $db->num_rows($r);
        $list = "";
        for ($i = 1; $i <= $n; $i++) {
            $id=$db->result($r,$i-1,"ID");
            $question=$db->result($r,$i-1,"QUESTION");
            $pos=$db->result($r,$i-1,"POSITION");
            $status=$db->result($r,$i-1,"STATUS");
            $list.="<tr class='pointer' onclick='showFaqCard(\"$id\")'>
                <td>$id</td>
                <td>$question</td>
                <td>$pos</td>
                <td>$status</td>
            </tr>";
        }
        return $list;
    }

    function showFaqCard($id) { $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT * FROM `SMART_FAQ` WHERE `ID`='$id' LIMIT 1;");
        $faq_question=$db->result($r,0,"QUESTION");
        $faq_answer=$db->result($r,0,"ANSWER");
        $faq_question_ru=$db->result($r,0,"QUESTION_RU");
        $faq_answer_ru=$db->result($r,0,"ANSWER_RU");
        $pos=$db->result($r,0,"POSITION");
        $status=$db->result($r,0,"STATUS");
        $form = "";$form_htm=RD."/tpl/smart_faq_card.htm";
        if (file_exists("$form_htm")){$form = file_get_contents($form_htm);}
        $form = str_replace("{faq_id}",$id,$form);
        $form = str_replace("{faq_question}",$faq_question,$form);
        $form = str_replace("{faq_answer}",$faq_answer,$form);
        $form = str_replace("{faq_question_ru}",$faq_question_ru,$form);
        $form = str_replace("{faq_answer_ru}",$faq_answer_ru,$form);
        $form = str_replace("{faq_pos}",$pos,$form);
        $form = str_replace("{faq_status}",$status ? "checked" : "",$form);
        return $form;
    }

    function saveFaqCard($faq_id, $faq_question, $faq_answer, $faq_question_ru, $faq_answer_ru, $faq_pos, $faq_status) { $db = DbSingleton::getTokoDb();
        $answer = 0; $err = "Помилка збереження даних!";
        if ($faq_id>0) {
            $db->query("UPDATE `SMART_FAQ` SET `QUESTION`='$faq_question', `ANSWER`='$faq_answer', `QUESTION_RU`='$faq_question_ru', `ANSWER_RU`='$faq_answer_ru', `POSITION`='$faq_pos', `STATUS`='$faq_status' WHERE `ID`='$faq_id' LIMIT 1;");
            $answer = 1; $err = "";
        }
        if ($faq_id==0) {
            $r = $db->query("SELECT MAX(`ID`) as mid FROM `SMART_FAQ`;");
            $faq_id=0+$db->result($r,0,"mid")+1;
            $db->query("INSERT INTO `SMART_FAQ` (`ID`, `QUESTION`, `ANSWER`, `QUESTION_RU`, `ANSWER_RU`, `POSITION`, `STATUS`) VALUES ('$faq_id', '$faq_question', '$faq_answer', '$faq_question_ru', '$faq_answer_ru', '$faq_pos', '$faq_status');");
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

    function showNewsList() { $db = DbSingleton::getTokoDb();
        $list = "";
        $r = $db->query("SELECT * FROM `SMART_NEWS` WHERE 1;");
        $n = $db->num_rows($r);
        for ($i = 1; $i <= $n; $i++) {
            $id=$db->result($r,$i-1,"ID");
            $question=$db->result($r,$i-1,"TITLE");
            $pos=$db->result($r,$i-1,"TEXT");
            $status=$db->result($r,$i-1,"STATUS");
            $list.="<tr class='pointer' onclick='showSmartNewsCard(\"$id\")'>
                <td>$id</td>
                <td>$question</td>
                <td>$pos</td>
                <td>$status</td>
            </tr>";
        }
        return $list;
    }

    function showSmartNewsCard($id) { $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT * FROM `SMART_NEWS` WHERE `ID`='$id' LIMIT 1;");
        $faq_question=$db->result($r,0,"TITLE");
        $faq_answer=$db->result($r,0,"TEXT");
        $faq_question_ru=$db->result($r,0,"TITLE_RU");
        $faq_answer_ru=$db->result($r,0,"TEXT_RU");
        $pos=$db->result($r,0,"POSITION");
        $status=$db->result($r,0,"STATUS");
        $form = ""; $form_htm=RD."/tpl/smart_news_card.htm";
        if (file_exists("$form_htm")){$form = file_get_contents($form_htm);}
        $form = str_replace("{news_id}",$id,$form);
        $form = str_replace("{news_title}",$faq_question,$form);
        $form = str_replace("{news_text}",$faq_answer,$form);
        $form = str_replace("{news_title_ru}",$faq_question_ru,$form);
        $form = str_replace("{news_text_ru}",$faq_answer_ru,$form);
        $form = str_replace("{news_pos}",$pos,$form);
        $form = str_replace("{news_status}",$status ? "checked" : "",$form);
        $form = str_replace("{image_disabled}",$id==0 ? "disabled" : "",$form);
        return $form;
    }

    function saveSmartNewsCard($news_id, $news_title, $news_text, $news_title_ru, $news_text_ru, $pos, $status) { $db = DbSingleton::getTokoDb();
        $answer = 0; $err = "Помилка збереження даних!";
        if ($news_id>0) {
            $db->query("UPDATE `SMART_NEWS` SET `TITLE`='$news_title', `TEXT`='$news_text', `TITLE_RU`='$news_title_ru', `TEXT_RU`='$news_text_ru', `POSITION`='$pos', `STATUS`='$status' WHERE `ID`='$news_id' LIMIT 1;");
            $answer = 1; $err = "";
        }
        if ($news_id==0) {
            $r = $db->query("SELECT MAX(`ID`) as mid FROM `SMART_NEWS`;");
            $news_id=0+$db->result($r,0,"mid")+1;
            $db->query("INSERT INTO `SMART_NEWS` (`ID`, `TITLE`, `TEXT`, `TITLE_RU`, `TEXT_RU`, `POSITION`, `STATUS`) VALUES ('$news_id', '$news_title', '$news_text', '$news_title_ru', '$news_text_ru', '$pos', '$status');");
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

    function getSmartNewsImage($news_id) { $db = DbSingleton::getTokoDb();
        $r = $db->query("SELECT * FROM `SMART_NEWS` WHERE `ID`='$news_id' LIMIT 1;");
        $news_image=$db->result($r,0,"IMAGE");
        return "<img src='https://smartkidbelt.com.ua/img/smart_news/$news_image' alt='$news_image' width='300'>";
    }

    function deleteSmartNewsPhoto($news_id) { $db = DbSingleton::getTokoDb();
        $answer = 0; $err = "Помилка збереження даних!";
        if ($news_id>0) {
            $r = $db->query("SELECT * FROM `SMART_NEWS` WHERE `ID`='$news_id' LIMIT 1;");
            $news_image=$db->result($r,0,"IMAGE");
            $targetDir = RD."/uploads/images/news/";
            $targetFile = $targetDir.$news_image;
            unlink($targetFile);
            $db->query("UPDATE `SMART_NEWS` SET `IMAGE`='' WHERE `ID`='$news_id' LIMIT 1;");
            $answer = 1; $err = "";
        }
        return array($answer, $err);
    }

}