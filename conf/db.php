<?php

/*
	Расширение Mysql пока для совместимость, пока перевожу код на PDO
*/
$host = "localhost";   
$user = "root";   
$pass = "13"; 
$dbname = "frfeed"; 

 if(!mysql_connect($host, $user, $pass)) exit(mysql_error()); 
mysql_query("SET character_set_client='UTF8'"); 
mysql_query("SET character_set_results='UTF8'"); 
mysql_query("SET collation_connection='UTF8'");
mysql_query("SET NAMES UTF8");
mysql_select_db("frfeed") or die(mysql_error()); 

$memcache_obj = new Memcache;
$memcache_obj->connect('127.0.0.1', 11211) or die("could not connect");

?>