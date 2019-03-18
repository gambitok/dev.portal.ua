<?php
class db{
	var $query='';
	function load_auth_param(){
		$this->host = 'localhost';
		$this->dbname = 'myparts_dba';
		$this->username = 'myparts_usr';
		$this->password = 'sdF98723KJef82';
	}
	function connect(){
		$this->load_auth_param();
		$this->db_id = @mysql_connect($this->host, $this->username, $this->password);
		@mysql_select_db($this->dbname, $this->db_id);
		mysql_query ("set character_set_client='cp1251'");
		mysql_query ("set character_set_results='cp1251'");
		mysql_query ("set collation_connection='cp1251_general_ci'");
	}
	function close(){ @mysql_close($this->db_id); }
	function num_rows($result){ $this->n=mysql_numrows($result); return $this->n; }
	function query($query){
		if ($query!="")
		if(!$this->db_id){ $this->connect();}
		$this->r = mysql_query($query, $this->db_id);
		if (mysql_error()!=""){ print mysql_error().":: query=$query\n";}
		return $this->r;
	}
	function queryP($query){
		if ($query!="")
		if(!$this->db_id){ $this->connect();}
		$this->r = mysql_query($query, $this->db_id); // || $_SERVER['REMOTE_ADDR']=="46.63.47.178"
		if ($_SERVER['REMOTE_ADDR']=="78.152.169.139" || $_SERVER['REMOTE_ADDR']=="192.168.3.106"){print mysql_error()." :: query=$query\n";}
		if (mysql_error()!=""){ print mysql_error()."query=$query\n";}
		return $this->r;
	}
	function queryS($query){
		if ($query!="")
		print "queryS=$query\n";
		return $this->r;
	}
	
	function result($result,$number,$field_name) { return mysql_result($result,$number,"$field_name"); }
}

class dbt{
	var $query='';
	function load_auth_param(){
		$this->host = 'localhost';
		$this->dbname = 'toko_dba';
		$this->username = 'toko_usr';
		$this->password = 'Xm53R9H4znZda4YH'; 
	}
	function connect(){
		$this->load_auth_param();
		$this->db_id = @mysql_connect($this->host, $this->username, $this->password);
		@mysql_select_db($this->dbname, $this->db_id);
		mysql_query ("set character_set_client='cp1251'");
		mysql_query ("set character_set_results='cp1251'");
		mysql_query ("set collation_connection='cp1251_general_ci'");
	}
	function close(){ @mysql_close($this->db_id); }
	function num_rows($result){ $this->n=mysql_numrows($result); return $this->n; }
	function query($query){
		if ($query!="")
		if(!$this->db_id){ $this->connect();}
		$this->r = mysql_query($query, $this->db_id);
		if (mysql_error()!=""){ print mysql_error()."query=$query\n";}
		return $this->r;
	}
	function queryP($query){
		if ($query!="")
		if(!$this->db_id){ $this->connect();}
		$this->r = mysql_query($query, $this->db_id);
		if ($_SERVER['REMOTE_ADDR']=="78.152.169.139" || $_SERVER['REMOTE_ADDR']=="192.168.3.106"){print mysql_error()."query=".$query;}
		if (mysql_error()!=""){ print mysql_error()."query=$query\n";}
		return $this->r;
	}
	function queryS($query){
		if ($query!="")
		print "queryS=$query\n";
		return $this->r;
	}
	function result($result,$number,$field_name) { return mysql_result($result,$number,"$field_name"); }
}

class dbp{
	var $query='';
	function load_auth_param(){
		$this->host = 'localhost';
		$this->dbname = 'toko_dba';
		$this->username = 'toko_usr';
		$this->password = 'Xm53R9H4znZda4YH'; 
		/*$this->host = '35.187.23.250';
		$this->dbname = 'tokodba';
		$this->username = 'root';
		$this->password = 'eKJ3G2cmjIayjswA'; 
		*/
		/*
		$this->host = 'localhost';
		$this->dbname = 'toko_dba';
		$this->username = 'toko_usr';
		$this->password = 'Xm53R9H4znZda4YH'; 
		*/	
	}
	function connect(){
		$this->load_auth_param();
		$this->db_id = @mysql_connect($this->host, $this->username, $this->password);
		@mysql_select_db($this->dbname, $this->db_id);
		mysql_query ("set character_set_client='cp1251'");
		mysql_query ("set character_set_results='cp1251'");
		mysql_query ("set collation_connection='cp1251_general_ci'");
		mysql_query ("SET sql_mode = ''");
	}
	function close(){ @mysql_close($this->db_id);}
	function num_rows($result){ $this->n=mysql_numrows($result); return $this->n; }
	function query($query){
		if(!$this->db_id){ $this->connect();}
		$this->r = mysql_query($query, $this->db_id);
		if (mysql_error()!="" && $_SERVER["REMOTE_ADDR"]=="78.152.169.139" || $_SERVER['REMOTE_ADDR']=="192.168.3.106"){ print mysql_error()."<br>query=".$query; }
		return $this->r;
	}
	function result($result,$number,$field_name) { return mysql_result($result,$number,"$field_name"); }
}
?>