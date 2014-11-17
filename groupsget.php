
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
$m = explode('/', $sm->params['1']);
$newiduser = $m['1'];
$init_obj->newiduser = $newiduser;

$stat_obj = new Statistic();
$cache_obj = new Caching();
$FriendObj = new Friends();

$FF = new FriendFeed();
if($memcache_obj->get($init_obj->newiduser."countnewmessage") == 0)
{
    echo "0";    
    $memcache_obj->set($init_obj->newiduser."countnewmessage", 1, false, 300);   
    exit;
}
else
{
     if(rand(0,12) == 4)
     { 
        try {  $FriendFeedarray = $FF->mainf($init_obj->newiduser,$vk); }   // -->class friendfeed
        catch (Exception $e) { echo "не сработал главный метод создания ленты groupsget.php"; }       

        try { $listFriends = $FriendObj->CheckFriendlistFromCache($memcache_obj,$init_obj->sessionid,$vk); }   // -->class friends
        catch (Exception $e) { echo "Не сработал метод кеширования друзей groupsget.php"; }

        $FriendFeednewarr = $FF->TimeFeedSort($FriendFeedarray);
        $oldFeedarray = $memcache_obj->get($init_obj->newiduser);

        $FriendFeedarray = array_udiff($FriendFeednewarr, $oldFeedarray, "FeedDiffarray");

        echo count($FriendFeedarray);
        $memcache_obj->set($init_obj->newiduser."countnewmessage", count($FriendFeedarray), false, 300);
        $memcache_obj->set($init_obj->newiduser.$init_obj->newiduser, $FriendFeednewarr, false, 86400);
        if($init_obj->newiduser == $init_obj->newiduser) $memcache_obj->set($init_obj->newiduser."me", $FriendFeednewarr, false, 86400);
        $memcache_obj->set($init_obj->sessionid."idpage", $init_obj->newiduser, false, 86400);

    }  
     else
     {      
        if($memcache_obj->get($init_obj->newiduser."countnewmessage") == 1)
        {
            echo "0";
            $memcache_obj->set($init_obj->newiduser."countnewmessage",0,false,300);
        }
        else echo $memcache_obj->get($init_obj->newiduser."countnewmessage");
     
     }
}   


?>