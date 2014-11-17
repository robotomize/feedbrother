<?php
session_start();
//Создаём новый объект. Также можно писать и в процедурном стиле
    $memcache_obj = new Memcache;
 
    //Соединяемся с нашим сервером
    $memcache_obj->connect('127.0.0.1', 11211) or die("could not connect");
    //print_r($memcache_obj->get("Globals"));
    //$memcache_obj->get("sql_query");
  // echo  "offset ".$memcache_obj->get($_SESSION['id']."offset")."offset\n\n";
  //  echo  "debugid ".$memcache_obj->get($_SESSION['id']."debid")."debugid\n\n";
   //  "debuggroup ".var_dump($memcache_obj->get($_SESSION['id']."debgroup"))."debuggroup\n\n";
     //var_dump($memcache_obj->get($_SESSION['id'].$_SESSION['id']));
   //  echo "huihuihuih";
   // var_dump($memcache_obj->get($_SESSION['id']."me"));
  //  echo "huihuihuih";
// var_dump($memcache_obj->get($_SESSION['id'].$_SESSION['id']));
    var_dump($sm->params);

    echo $memcache_obj->get($_SESSION['id']."idpage");
    var_dump($memcache_obj->get($_SESSION['id']));
    echo "heeeeello";
 var_dump($memcache_obj->get($_SESSION['id'].$_SESSION['id']));
   // echo "hui";
    //var_dump($memcache_obj->get($_SESSION['id']."groups"));

    //var_dump($memcache_obj->get($_SESSION['id']."me"));
 	//	var_dump($memcache_obj->get($_SESSION['id'].$_SESSION['id']));
 //echo $memcache_obj->get($_SESSION['id']."idpage");
 ?>