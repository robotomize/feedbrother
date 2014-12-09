<?php


$vk = new VkApi(array(
    'apiKey' => 'lUdVGPiTt52v81jjJEzK',
    'appId' => '4667352',
    'authRedirectUrl' => 'http://feedbrother.com/me',
));

$FriendFeedarray = [];
session_start();
$sessionid = $_SESSION['id'];
session_write_close();
$init_obj = new InitUriFromRouter($sessionid);
$FF = new FriendFeed();
$stat_obj = new Statistic();
$grstat_obj = new GetNewGroupInfo();
$FF->offset = $memcache_obj->get($init_obj->sessionid."offset"); 
$init_obj->newiduser = $memcache_obj->get($init_obj->sessionid."idpage");  

try {  $FriendFeedarray = $FF->mainf($init_obj->newiduser,$vk); }   // -->class friendfeed
catch (Exception $e) { echo "не сработал главный метод создания ленты в oldcacheget.php"; } 
$FriendFeedarray = $FF->TimeFeedSort($FriendFeedarray);                                                
$memcache_obj->set($init_obj->sessionid."oldentriescache", $FriendFeedarray, false, 86400);                                                              
$memcache_obj->set($init_obj->sessionid."idpage", $init_obj->newiduser, false, 86400);

/*
	Возможен рефакторинг, пишем новые группы в базу в момент загрузки старых записей ленты
*/
$GroupIds[] = $vk->getGroupsforWall($init_obj->newiduser);
if($init_obj->newiduser != $init_obj->sessionid) 
{
		try {  $stat_obj->ResearchNewGroups($GroupIds,$init_obj->sessionid); }   // -->class friendfeed
		catch (Exception $e){ file_put_contents('/var/www/FeedBrother/PDOErrors.txt', "Ошибка в записи количества новых групп".$e->getMessage(), FILE_APPEND); } 
}
/*
Нужен рефакторинг или через execute или преобразовать предыдущий метод
*/
/*
	Инициализация нового массива новых групп для пользователя
*/
  try { $grstat_obj->InitGroupDifference($init_obj->sessionid,$init_obj->newiduser,$vk,$memcache_obj); }   // -->class Statistic
  catch (Exception $e) { file_put_contents('/var/www/FeedBrother/PDOErrors.txt', "Ошибка в просчете разности групп с пользователем".$e->getMessage(), FILE_APPEND); } 

  try { $rr = $grstat_obj->GetGroupInfoFromIds($memcache_obj,$vk,$init_obj->sessionid);  }   // -->class Statistic	
  catch (Exception $e) { file_put_contents('/var/www/FeedBrother/PDOErrors.txt', "Ошибка в расчете информации о группах".$e->getMessage(), FILE_APPEND); } 

 $memcache_obj->set($init_obj->sessionid."newgroupdiff",$rr, false, 286400); // кладем в кеш информацию о группах

//$UserProfilegroupcache = $cache_obj->createusergroupsidscaching($GroupIds,$memcache_obj,$init_obj->newiduser,$init_obj->sessionid);		
	