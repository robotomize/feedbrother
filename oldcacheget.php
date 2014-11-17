<?php


$vk = new VkApi(array(
    'apiKey' => '',
    'appId' => '',
    'authRedirectUrl' => 'http://192.168.1.141/me',
));

$FriendFeedarray = [];
session_start();
$sessionid = $_SESSION['id'];
session_write_close();
$init_obj = new InitUriFromRouter($sessionid);
$FF = new FriendFeed();
$FF->offset = $memcache_obj->get($init_obj->sessionid."offset"); 
$init_obj->newiduser = $memcache_obj->get($init_obj->sessionid."idpage");  

try {  $FriendFeedarray = $FF->mainf($init_obj->newiduser,$vk); }   // -->class friendfeed
catch (Exception $e) { echo "не сработал главный метод создания ленты в oldcacheget.php"; } 
$FriendFeedarray = $FF->TimeFeedSort($FriendFeedarray);                                                
$memcache_obj->set($init_obj->sessionid."oldentriescache", $FriendFeedarray, false, 86400);                                                              
$memcache_obj->set($init_obj->sessionid."idpage", $init_obj->newiduser, false, 86400);
?>