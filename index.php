<?php
//xdebug_start_trace();
set_time_limit(3600);
if (isset($_GET['act']) AND $_GET['act']=="logout") {
  session_start();
  session_destroy();
  header("Location: http://".$_SERVER['HTTP_HOST']."/index.php");
  exit;
}


require 'conf/db.php';
require 'classes/base.php';
session_start();


$vk = new VkApi(array(
    'apiKey' => '',
    'appId' => '',
    'authRedirectUrl' => 'http://192.168.1.141/index.php',
));

$GroupIdsStr = "";
$FriendFeedarray = [];
$sessionid = $_SESSION['id'];
$urlMyPage = "http://192.168.1.141/me.php?back=".$sessionid;
 $stat_obj = new Statistic();
session_write_close();
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>FriendFeed просматривай ленты друзей</title>
     <link href='http://fonts.googleapis.com/css?family=Hammersmith+One' rel='stylesheet' type='text/css'>
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/agency.css" rel="stylesheet">
     <link rel="stylesheet" type="text/css" href="source/jquery.fancybox.css?v=2.1.5" media="screen" />
    <link rel="stylesheet" type="text/css" href="source/helpers/jquery.fancybox-buttons.css?v=1.0.5" />
    <link rel="stylesheet" type="text/css" href="source/helpers/jquery.fancybox-thumbs.css?v=1.0.7" />
    <link href="font-awesome-4.1.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="http://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css">
    <link href='http://fonts.googleapis.com/css?family=Kaushan+Script' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Droid+Serif:400,700,400italic,700italic' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Roboto+Slab:400,100,300,700' rel='stylesheet' type='text/css'> 
     <script src="js/jquery-1.11.0.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/agency.js"></script>
    <script src="js/cutstring.js"></script>
    <script src="https://s3.amazonaws.com/intercoolerjs.org/release/intercooler-0.4.1.min.js"></script> 
    <script type="text/javascript" src="lib/jquery.mousewheel-3.0.6.pack.js"></script>   
    <script type="text/javascript" src="source/jquery.fancybox.js?v=2.1.5"></script>    
    <script type="text/javascript" src="source/helpers/jquery.fancybox-buttons.js?v=1.0.5"></script>  
    <script type="text/javascript" src="source/helpers/jquery.fancybox-thumbs.js?v=1.0.7"></script>
    <script type="text/javascript" src="source/helpers/jquery.fancybox-media.js?v=1.0.6"></script>               
    <style>

body 
{
     padding-top: 100px; 
     background-color: #E8E8E8;
}

</style>
</head>
<body>

   <nav class="navbar navbar-default navbar-fixed-top navbar-shrink">
        <div class="container">
            <div class="navbar-header page-scroll">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="http://192.168.1.141/index.php">  <font class="menutexttopglyph"> <span class="glyphicon glyphicon-th-list "></span></font><font class="menutexttop">&nbsp;FriendFeed</a></font> 
            </div>
            <?php 
            session_start();
            ?>            
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right">
                    <li>
                     <center><a href=<?php echo $urlMyPage; ?>>   <img src=<?php echo $_SESSION['img']; ?> width="40px" heigth="40px" class="img-circle"></a>&nbsp;&nbsp;&nbsp;&nbsp;</center>
                    </li>
                    <li  class="toppullrightlink">  
                      <a href="http://192.168.1.141/index.php?act=logout" class="toppullrightlink"><font class="toppullrightlink smalarrow">выйти</font></a>
                    </li>
                </ul>
            </div>      
        </div>
        <?php
        session_write_close();
        ?>
    </nav> 
<?php


if(!empty($_GET['news']))
{

  $FF = new FriendFeed();
  $FriendFeedarray = $memcache_obj->get($sessionid.$sessionid);
  $NewmessageCount = $memcache_obj->get($sessionid."countnewmessage");
  session_write_close();
  if(!empty($FriendFeedarray))
  {
?>
<?php
 for ($iiii=0; $iiii < count($FriendFeedarray); $iiii++) 
    {  
    if(!empty($NewmessageCount))
        {
            if($iiii < $NewmessageCount)
            {
                ?>
                                     <table class="table table-bordered row-fluid newfeed">
                                        <tr>
                                         <td >                            
                                         <div class="media">
                                        <a class="pull-left" href=<?php echo "http://vk.com/".$FriendFeedarray[$iiii]['screen']; ?> target="_blank">                                          
                                        <img class="media-object" src= <?php echo $FriendFeedarray[$iiii]['groupphoto'];   ?>>
                                        </a>
                                        <div class="media-body">
                                         &nbsp;<a href=<?php echo "http://vk.com/".$FriendFeedarray[$iiii]['screen']; ?> target="_blank"><strong> <?php echo $FriendFeedarray[$iiii]['groupname'];   ?></strong></a>
                                                <?php  
                                                if(!empty($FriendFeedarray[$iiii]['text']))
                                                {
                                                  echo " "; ?> &nbsp; &nbsp; <div class="cutstring" data-display="none" data-max-length="200" data-show-text="Показать полностью.." data-hide-text="Свернуть..">                  
                                               <?php echo " &nbsp;".$FriendFeedarray[$iiii]['text']; ?></div>                                                
                                                    <?php                                           
                                                }                                              
                                                 if(!empty($FriendFeedarray[$iiii]['photo']))
                                                {
                                                 ?>
                                                    <br>
                                                    <?php
                                                    for ($ii=0; $ii < count($FriendFeedarray[$iiii]['photo']); $ii++) 
                                                    { 
                                                         if($FriendFeedarray[$iiii]['photo'][$ii] != "")
                                                    {
                                                    ?>                                            
                                                   <a class="fancybox-effects-a" href=<?php echo $FriendFeedarray[$iiii]['photo'][$ii];   ?> data-fancybox-group="gallery" title=""><img src=<?php echo $FriendFeedarray[$iiii]['photo'][$ii];   ?> width="90%" alt="" /></a><br>
                                                    <br>
                                                    <?php
                                                    }
                                                    }
                                                     } 
                                                        ?>                                     
                                             </div>                                     
                                     </div>
                                    <br>
                                     <div class="row">  
                                        <div class="col-md-3">
                                         &nbsp;&nbsp; <font class="timetextago"><span class="glyphicon glyphicon-time"> </span>&nbsp;<?php echo $FF->timeAgo($FriendFeedarray[$iiii]['date']);   ?></font> 
                                            </div>
                                                <div class="col-md-5 col-md-offset-4">
                                                <a href=<?php echo "http://vk.com/".$FriendFeedarray[$iiii]['screen']; ?> target="_blank"><font class="groupslink">Открыть группу  <?php echo iconv_substr($FriendFeedarray[$iiii]['groupname'], 0, 10, 'UTF-8')."...";  ?>&nbsp;<span class="glyphicon glyphicon-share-alt"> </span></font></a>
                                                </div>                                                                                        
                                    </div>                            
                                                        
                                 </td>
                                    </tr>  
                                     </table>  
                            <?php                
                            }
                            else
                            {
                           ?>
                                     <table class="table table-bordered row-fluid leftprofile1 feedliner">
                                        <tr>
                                        <td >                              
                                         <div class="media">
                                        <a class="pull-left" href=<?php echo "http://vk.com/".$FriendFeedarray[$iiii]['screen']; ?> target="_blank">                                  
                                        <img class="media-object" src= <?php echo $FriendFeedarray[$iiii]['groupphoto'];   ?>> </a>
                                        <div class="media-body">
                                         &nbsp;<a href=<?php echo "http://vk.com/".$FriendFeedarray[$iiii]['screen']; ?> target="_blank"><strong> <?php echo $FriendFeedarray[$iiii]['groupname'];   ?></strong></a>
                                           <?php  
                                                if(!empty($FriendFeedarray[$iiii]['text']))
                                                {
                                                 echo " "; ?> &nbsp; &nbsp; <div class="cutstring" data-display="none" data-max-length="200" data-show-text="Показать полностью.." data-hide-text="Свернуть..">                  
                                               <?php echo " &nbsp;".$FriendFeedarray[$iiii]['text']; ?></div>                                                
                                                    <?php                                            
                                                }                                           
                                                 if(!empty($FriendFeedarray[$iiii]['photo']))
                                                {
                                                 ?>
                                                    <br>
                                                    <?php
                                                 for ($ii=0; $ii < count($FriendFeedarray[$iiii]['photo']); $ii++) 
                                                    { 
                                                         if($FriendFeedarray[$iiii]['photo'][$ii] != "")
                                                    {
                                                    ?>                                            
                                                    <a class="fancybox-effects-a" href=<?php echo $FriendFeedarray[$iiii]['photo'][$ii];   ?> data-fancybox-group="gallery" title=""><img src=<?php echo $FriendFeedarray[$iiii]['photo'][$ii];   ?> width="90%" alt="" /></a><br>
                                                <br>
                                                    <?php
                                                }
                                                    }
                                                     } 
                                                        ?>                                         
                                                  </div>                                             
                                      </div>
                                    <br>
                                     <div class="row">                     
                                 <div class="col-md-3">
                                         &nbsp;&nbsp; <font class="timetextago"><span class="glyphicon glyphicon-time"> </span>&nbsp;<?php echo $FF->timeAgo($FriendFeedarray[$iiii]['date']);   ?></font> 
                                            </div>
                                                <div class="col-md-5 col-md-offset-4">
                                        <a href=<?php echo "http://vk.com/".$FriendFeedarray[$iiii]['screen']; ?> target="_blank"><font class="groupslink">Открыть группу  <?php echo iconv_substr($FriendFeedarray[$iiii]['groupname'], 0, 10, 'UTF-8')."...";  ?>&nbsp;<span class="glyphicon glyphicon-share-alt"> </span></font></a>
                                            </div>                                       
                                 </div>                                           
                               </td>
                               </tr>  
                                 </table> 
                                 <?php
            }
        }
        else
        {
        ?>
        <table class="table table-bordered row-fluid leftprofile1 feedliner">
        <tr>
            <td >             
                 <div class="media">
                <a class="pull-left" href=<?php echo "http://vk.com/".$FriendFeedarray[$iiii]['screen']; ?> target="_blank">                  
                <img class="media-object" src= <?php echo $FriendFeedarray[$iiii]['groupphoto'];   ?>>
                </a>
                <div class="media-body">
                 &nbsp;<a href=<?php echo "http://vk.com/".$FriendFeedarray[$iiii]['screen']; ?> target="_blank"><strong> <?php echo $FriendFeedarray[$iiii]['groupname'];   ?></strong></a>
                   <?php  
                        if(!empty($FriendFeedarray[$iiii]['text']))
                        {
                             echo " "; ?> &nbsp; &nbsp; <div class="cutstring" data-display="none" data-max-length="200" data-show-text="Показать полностью.." data-hide-text="Свернуть..">                  
                       <?php echo " &nbsp;".$FriendFeedarray[$iiii]['text']; ?></div>                        
                            <?php
                    
                        }
                   ?>

                        <?php
                         if(!empty($FriendFeedarray[$iiii]['photo']))
                        {

                         ?>
                            <br>
                            <?php
                         for ($ii=0; $ii < count($FriendFeedarray[$iiii]['photo']); $ii++) 
                            { 
                 
                            ?>
                    
                           <a class="fancybox-effects-a" href=<?php echo $FriendFeedarray[$iiii]['photo'][$ii];   ?> data-fancybox-group="gallery" title=""><img src=<?php echo $FriendFeedarray[$iiii]['photo'][$ii];   ?> width="90%" alt="" /></a><br>
                        <br>
                            <?php
                            }
                             } 
                                ?>                      
                                            
                       </div>                      
                       </div>
                    <br>
                     <div class="row"> 
                 <div class="col-md-3">
                         &nbsp;&nbsp; <font class="timetextago"><span class="glyphicon glyphicon-time"> </span>&nbsp;<?php echo $FF->timeAgo($FriendFeedarray[$iiii]['date']);   ?></font> 
                            </div>
                                <div class="col-md-5 col-md-offset-4">
                        <a href=<?php echo "http://vk.com/".$FriendFeedarray[$iiii]['screen']; ?> target="_blank"><font class="groupslink">Открыть группу  <?php echo iconv_substr($FriendFeedarray[$iiii]['groupname'], 0, 10, 'UTF-8')."...";  ?>&nbsp;<span class="glyphicon glyphicon-share-alt"> </span></font></a>
                            </div>                       
                 </div>                     
               </td>
               </tr>  
                 </table>     

<?php
}
}
$memcache_obj->set($sessionid, $FriendFeedarray, false, 86400);
  }
  else
  {
    $FriendFeedarray = $memcache_obj->get($sessionid);
        for ($iiii=0; $iiii < count($FriendFeedarray); $iiii++) 
                {                     
       ?>
     <table class="table table-bordered row-fluid leftprofile1 feedliner">
        <tr>
            <td >               
                 <div class="media">
                <a class="pull-left" href="#">                  
                <img class="media-object" src= <?php echo $FriendFeedarray[$iiii]['groupphoto'];   ?>>
                </a>
                <div class="media-body">
                 <strong>&nbsp; <?php echo $FriendFeedarray[$iiii]['groupname'];   ?></strong>
                   <?php  
                        if(!empty($FriendFeedarray[$iiii]['text']))
                        {
                            ?>                           
                           <?php echo " "; ?> &nbsp; &nbsp; <div class="cutstring" data-display="none" data-max-length="200" data-show-text="Показать полностью.." data-hide-text="Свернуть..">                  
                        &nbsp;<?php echo link_it($FriendFeedarray[$iiii]['text']); ?></div>                        
                            <?php                    
                        }
                   ?>
                        <?php
                         if(!empty($FriendFeedarray[$iiii]['photo']))
                        {
                         ?>
                                                     <br>
                            <?php
                         for ($ii=0; $ii < count($FriendFeedarray[$iiii]['photo']); $ii++) 
                            { 
                 
                            ?>
                    
                          <a class="fancybox-effects-a" href=<?php echo $FriendFeedarray[$iiii]['photo'][$ii];   ?> data-fancybox-group="gallery" title=""><img src=<?php echo $FriendFeedarray[$iiii]['photo'][$ii];   ?> width="90%" alt="" /></a><br>
                        <br>
                            <?php
                            }
                             } 
                                ?>
                            
                    

                            

                        </div>
                        
               

                    </div>
                    <br>
                     <div class="row">

                <!-- Blog Sidebar Widgets Column -->

                 <div class="col-md-3">
                         &nbsp;&nbsp; <font class="timetextago"><?php echo $FF->timeAgo($FriendFeedarray[$iiii]['date']);   ?></font> 
                            </div>
                                <div class="col-md-5 col-md-offset-4">
                        <a href="#"><font class="groupslink">Открыть группу  <?php echo iconv_substr($FriendFeedarray[$iiii]['groupname'], 0, 10, 'UTF-8')."...";  ?>&nbsp;<span class="glyphicon glyphicon-share-alt"> </span></font></a>
                            </div>                        
                         </div>                    
                                  
                  
                           </td>
                           </tr>  
                             </table>                                         

                         <?php
                        }

                    }  


              $memcache_obj->set($sessionid."countnewmessage", 0, false, 300);                    
                      ?>
 <script>
$(function() {
    $('.cutstring').cutstring();
});
</script>

<?php
//session_write_close();
exit;
}





if(!empty($_GET['groups']))
{


if($memcache_obj->get($sessionid."countnewmessage") == 0)
{
    echo "0";    
    $memcache_obj->set($sessionid."countnewmessage", 1, false, 300);   
    exit;
}
else
{
     if(rand(0,12) == 4)
     {   

        $GroupIds[] = $vk->getGroupsforWall($_GET['groups']);
for($mm=0;$mm<count($GroupIds['0']);$mm++)
{
    if($GroupIdsStr == "") $GroupIdsStr = $GroupIds['0'][$mm];
    else $GroupIdsStr = $GroupIdsStr.",".$GroupIds['0'][$mm];
}
//echo $GroupIdsStr;
$Groupinfo[] = $vk->getGroupsById($GroupIdsStr);
$CountDivGroups = floor(count($Groupinfo['0'])/24);

$CounterModGroups = count($Groupinfo['0']) % 24;
$CounterWallget = 0;
$CounterWallget24 = 24;

while($ccc<$CountDivGroups)
{

    $codeStr = 'var a=API.groups.get({"user_id":"'.$_GET['groups'].'"}); var b=a; var d='.$CounterWallget.'; var v='.$CounterWallget24.';
        var c = [];
        while (d < v)
        {
         c.push(API.wall.get({"owner_id":-b[d],"count":"4"}));
         d = d+1; 
        };
        return c;';       

    $viewMyFeed[] = $vk->getExecuteFeedFriends($codeStr);

    for($cc=0; $cc<count($viewMyFeed['0']); $cc++)
    {
        
        for($jj = 1; $jj<5; $jj++)
        { 
               for($vv=0; $vv<count($Groupinfo['0']);$vv++)
               {

                    if("-".$Groupinfo['0'][$vv]['gid'] == $viewMyFeed['0'][$cc][$jj]['from_id']) 
                    {   
                        $gidscreen = $Groupinfo['0'][$vv]['screen_name'];     
                        $gidd = $Groupinfo['0'][$vv]['gid'];              
                        $Groupphoto = $Groupinfo['0'][$vv]['photo'];
                        $Groupname = $Groupinfo['0'][$vv]['name'];                                           
                        break;
                    }
               }
             for ($vv=0; $vv < count($viewMyFeed['0'][$cc][$jj]['attachments']); $vv++) 
                { 
                    $Feedphotoarray[] = $viewMyFeed['0'][$cc][$jj]['attachments'][$vv]['photo']['src_big'];
                }
                if(checkdatearr($viewMyFeed['0'][$cc][$jj]['date']) == 1)
                {
                    $FriendFeedarray[] = array("groupname" => $Groupname, "groupphoto" => $Groupphoto, "text" => $viewMyFeed['0'][$cc][$jj]['text'], "photo" => $Feedphotoarray, "date" => $viewMyFeed['0'][$cc][$jj]['date'],"gid" => $gidd, "screen" => $gidscreen); 
                }
              
          unset($Feedphotoarray); 
        } 
    

    }
    unset($viewMyFeed);

    $CounterWallget = $CounterWallget + 24;
    $CounterWallget24 = $CounterWallget24 + 24;
    $ccc++;
}

if($CounterModGroups != 0)
{
    $CounterWallget = 0;
    $CounterWallget24 = 24;
    unset($viewMyFeed);

    $CounterMod = $ccc*24;
    $CounterModGroups = $CounterMod + $CounterModGroups;

    $codeStr = 'var a=API.groups.get({"user_id":"'.$_GET['groups'].'"}); var b=a; var d='.$CounterMod.'; var v='.$CounterModGroups.';
        var c = [];
        while (d < v)
        {
         c.push(API.wall.get({"owner_id":-b[d],"count":"4"}));
         d = d+1; 
        };
        return c;';       

    $viewMyFeed[] = $vk->getExecuteFeedFriends($codeStr);
    //var_dump($viewMyFeed);
    for($cc=0; $cc<count($viewMyFeed['0']); $cc++)
    {
        
        for($jj = 1; $jj<5; $jj++)
        {   
               for($vv=0; $vv<count($Groupinfo['0']);$vv++)
               {

                    if("-".$Groupinfo['0'][$vv]['gid'] == $viewMyFeed['0'][$cc][$jj]['from_id']) 
                    {  
                         $gidscreen = $Groupinfo['0'][$vv]['screen_name'];   
                        $gidd = $Groupinfo['0'][$vv]['gid'];             
                        $Groupphoto = $Groupinfo['0'][$vv]['photo'];
                        $Groupname = $Groupinfo['0'][$vv]['name']; 
                        break;
                    }
               }
            for ($vv=0; $vv < count($viewMyFeed['0'][$cc][$jj]['attachments']); $vv++) 
                { 
                    $Feedphotoarray[] = $viewMyFeed['0'][$cc][$jj]['attachments'][$vv]['photo']['src_big'];
                }
                  if(checkdatearr($viewMyFeed['0'][$cc][$jj]['date']) == 1)
                {
                    $FriendFeedarray[] = array("groupname" => $Groupname, "groupphoto" => $Groupphoto, "text" => $viewMyFeed['0'][$cc][$jj]['text'], "photo" => $Feedphotoarray, "date" => $viewMyFeed['0'][$cc][$jj]['date'],"gid" => $gidd, "screen" => $gidscreen); 
                }
               //$FriendFeedarray[] = array("groupname" => $Groupname, "groupphoto" => $Groupphoto, "text" => $viewMyFeed['0'][$cc][$jj]['text'], "photo" => $Feedphotoarray, "date" => $viewMyFeed['0'][$cc][$jj]['date'],"gid" => $gidd,"screen" => $gidscreen);
          unset($Feedphotoarray); 
        } 
    

    }

}

// Этот блок нужен для выполнения многих фоновых задач кеширования, обновление счетчика новых постов в ленте, кеширование друзей и кеширвоание ленты, 
//склейка и обрезование кеширвоанной ленты
  
      
   if(empty($memcache_obj->get($sessionid."friends")))
   {
   
        $listFriends[] = $vk->getFriends();
        $memcache_obj->set($sessionid."friends", $listFriends, false, 1200);

    }
    else $listFriends = $memcache_obj->get($sessionid."friends");
   


$FF = new FriendFeed();
$FriendFeednewarr = $FF->TimeFeedSort($FriendFeedarray);
//$FriendFeedarray = $FF->TimeFeedSort($FriendFeedarray);
$oldFeedarray = $memcache_obj->get($sessionid);
//$FriendFeedarray = $FF->Newsdiffarray($FriendFeedarray,$oldFeedarray);

$FriendFeedarray = array_udiff($FriendFeednewarr, $oldFeedarray, "FeedDiffarray");
//sleep(2);
echo count($FriendFeedarray);
$memcache_obj->set($sessionid."countnewmessage", count($FriendFeedarray), false, 300);


$memcache_obj->set($sessionid.$sessionid, $FriendFeednewarr, false, 86400);

if($sessionid == $_GET['groups'])
{
    $memcache_obj->set($sessionid."me", $FriendFeednewarr, false, 86400);
}


// Для модуля обработки статистики решил оставить этот код и только, когда приложение не работает с лентой, хотя если на главной будет выводиться моя лена, то уберу
// удаление из базы в случае если ты выписался из сообществ, пока не реализовано
 /*
    $viewMyGroups[] = $vk->getGroups($sessionid);  //список групп
for ($i=1; $i <count($viewMyGroups['0']) ; $i++) 
    { 
        $myid = $sessionid;
        $gidGroup = $viewMyGroups['0'][$i]['gid'];
       if(empty(mysql_fetch_assoc(mysql_query("SELECT id FROM Cachegroups WHERE id_user='$myid' and id_group='$gidGroup'"))))
        {
         $gidGroup = $viewMyGroups['0'][$i]['gid'];       
        $nameGroup = strip_tags(str_replace("'","",$viewMyGroups['0'][$i]['name']));       
        $descriptionGroup = strip_tags(str_replace("'","",$viewMyGroups['0'][$i]['description']));      
        $screen_nameGroup = $viewMyGroups['0'][$i]['screen_name'];
        $activityGroup = $viewMyGroups['0'][$i]['is_closed'];
        $members_countGroup = $viewMyGroups['0'][$i]['members_count'];

// ошибка, добавляются группы повторно или по несколько штук, проблема не решена
        mysql_query("INSERT INTO Cachegroups VALUES (null, '$_SESSION[id]', '$nameGroup', '$descriptionGroup', '$screen_nameGroup', '$activityGroup', '$members_countGroup','$gidGroup')") or die(mysql_error());       
        }

    }
*/



     }  
     else
     {
        // ошибка если новых записей 1, будет вечно 1 менятся на 0, нужно потом исправить
        if($memcache_obj->get($sessionid."countnewmessage") == 1)
        {
            echo "0";
            $memcache_obj->set($sessionid."countnewmessage",0,false,300);
        }
        else
        {
           // sleep(2);
            echo $memcache_obj->get($sessionid."countnewmessage");
        }
     }     


}


   
exit;
}

if(isset($_GET['oldcache']))
{
 
  
                                                $offset = $memcache_obj->get($sessionid."offset");
                                   // $FriendFeedarray = $memcache_obj->get($sessionid.$sessionid);
                                    //$FriendFeedarray1 = $memcache_obj->get($sessionid);
                                            $myid = $memcache_obj->get($sessionid."idpage");
                                               // echo $myid."\n";
                                               // exit;
                                   // if(empty($FriendFeedarray)) $FriendFeedarray = $FriendFeedarray1;
                                 //  echo "bitch".$offset."bitch";
                                   //echo "nyid".$myid."fucks";
                                            // дебагерские переменные
                                           $memcache_obj->set($sessionid."debid",$myid,false,7200);

                                                                $GroupIds[] = $vk->getGroupsforWall($myid);

                                                                  //echo "Group IDS ".count($GroupIds)."\n";
                                                                for($mm=0;$mm<count($GroupIds['0']);$mm++)
                                                                {
                                                             if($GroupIdsStr == "") $GroupIdsStr = $GroupIds['0'][$mm];
                                                                    else $GroupIdsStr = $GroupIdsStr.",".$GroupIds['0'][$mm];
                                                                }
                                                    //echo $GroupIdsStr;
                                                            $Groupinfo[] = $vk->getGroupsById($GroupIdsStr);
                                                     
                                                         // дебагерская переменаня
                                                          $memcache_obj->set($sessionid."debgroup",$Groupinfo,false,7200);
                                                    // целое число запросов к группам 
 
                                                    $CountDivGroups = floor(count($Groupinfo['0'])/24);

                                                    // остаток от деления на 24, максимальное число запросов в группам

                                                    $CounterModGroups = count($Groupinfo['0']) % 24;
                                                    $CounterWallget = 0;
                                                    $CounterWallget24 = 24;

                                                    while($ccc<$CountDivGroups)
                                                    {

                                                        $codeStr = 'var a=API.groups.get({"user_id":"'.$myid.'"}); var b=a; var d='.$CounterWallget.'; var v='.$CounterWallget24.';
                                                            var c = [];
                                                            while (d < v)
                                                            {
                                                             c.push(API.wall.get({"owner_id":-b[d],"count":"4","offset":"'.$offset.'"}));
                                                             d = d+1; 
                                                            };
                                                            return c;';       

                                                        $viewMyFeed[] = $vk->getExecuteFeedFriends($codeStr);

                                                        //var_dump($viewMyFeed);
                                                         // var_dump($viewMyFeed);
                                                        for($cc=0; $cc<count($viewMyFeed['0']); $cc++)
                                                        {
                                                            
                                                            for($jj = 1; $jj<5; $jj++)
                                                            {    
                                                                   for($vv=0; $vv<count($Groupinfo['0']);$vv++)
                                                                   {

                                                                        if("-".$Groupinfo['0'][$vv]['gid'] == $viewMyFeed['0'][$cc][$jj]['from_id']) 
                                                                        {
                                                                            $gidscreen = $Groupinfo['0'][$vv]['screen_name'];
                                                                            $gidd = $Groupinfo['0'][$vv]['gid'];
                                                                            $Groupphoto = $Groupinfo['0'][$vv]['photo'];
                                                                            $Groupname = $Groupinfo['0'][$vv]['name'];                                           
                                                                            break;
                                                                        }
                                                                   }
                                                                    for ($vv=0; $vv < count($viewMyFeed['0'][$cc][$jj]['attachments']); $vv++) 
                                                                    { 
                                                                        $Feedphotoarray[] = $viewMyFeed['0'][$cc][$jj]['attachments'][$vv]['photo']['src_big'];
                                                                    }
                                                                     if(checkdatearr($viewMyFeed['0'][$cc][$jj]['date']) == 1)
                                                                   {
                                                                        $FriendFeedarray[] = array("groupname" => $Groupname, "groupphoto" => $Groupphoto, "text" => $viewMyFeed['0'][$cc][$jj]['text'], "photo" => $Feedphotoarray, "date" => $viewMyFeed['0'][$cc][$jj]['date'],"gid" => $gidd, "screen" => $gidscreen); 
                                                                    }
                                                                //   $FriendFeedarray[] = array("groupname" => $Groupname, "groupphoto" => $Groupphoto, "text" => $viewMyFeed['0'][$cc][$jj]['text'], "photo" => $Feedphotoarray, "date" => $viewMyFeed['0'][$cc][$jj]['date'],"gid" => $gidd, "screen" => $gidscreen);
                                                              unset($Feedphotoarray);  
                                                            //  echo $viewMyFeed['0'][$cc][$jj]['date']."\n";   
                                                            } 
                                                        

                                                        }
                                                       // echo "viewmyfeed1 ".count($viewMyFeed)."\n";
                                                        unset($viewMyFeed);

                                                        $CounterWallget = $CounterWallget + 24;
                                                        $CounterWallget24 = $CounterWallget24 + 24;
                                                        $ccc++;
                                                    }

                                                    if($CounterModGroups != 0)
                                                    {
                                                        $CounterWallget = 0;
                                                        $CounterWallget24 = 24;
                                                        unset($viewMyFeed);

                                                        $CounterMod = $ccc*24;
                                                        $CounterModGroups = $CounterMod + $CounterModGroups;

                                                        $codeStr = 'var a=API.groups.get({"user_id":"'.$myid.'"}); var b=a; var d='.$CounterMod.'; var v='.$CounterModGroups.';
                                                            var c = [];
                                                            while (d < v)
                                                            {
                                                             c.push(API.wall.get({"owner_id":-b[d],"count":"4","offset":"'.$offset.'"}));
                                                             d = d+1; 
                                                            };
                                                            return c;';       

                                                        $viewMyFeed[] = $vk->getExecuteFeedFriends($codeStr);
                                                        //  var_dump($viewMyFeed);
                                                        for($cc=0; $cc<count($viewMyFeed['0']); $cc++)
                                                        {
                                                            
                                                            for($jj = 1; $jj<5; $jj++)
                                                            {    
                                                                   for($vv=0; $vv<count($Groupinfo['0']);$vv++)
                                                                   {

                                                                        if("-".$Groupinfo['0'][$vv]['gid'] == $viewMyFeed['0'][$cc][$jj]['from_id']) 
                                                                        {
                                                                            $gidscreen = $Groupinfo['0'][$vv]['screen_name'];
                                                                            $gidd = $Groupinfo['0'][$vv]['gid'];
                                                                            $Groupphoto = $Groupinfo['0'][$vv]['photo'];
                                                                            $Groupname = $Groupinfo['0'][$vv]['name']; 
                                                                            break;
                                                                        }
                                                                   }
                                                                    for ($vv=0; $vv < count($viewMyFeed['0'][$cc][$jj]['attachments']); $vv++) 
                                                                    { 
                                                                        $Feedphotoarray[] = $viewMyFeed['0'][$cc][$jj]['attachments'][$vv]['photo']['src_big'];
                                                                    }
                                                                     if(checkdatearr($viewMyFeed['0'][$cc][$jj]['date']) == 1)
                                                                    {
                                                                        $FriendFeedarray[] = array("groupname" => $Groupname, "groupphoto" => $Groupphoto, "text" => $viewMyFeed['0'][$cc][$jj]['text'], "photo" => $Feedphotoarray, "date" => $viewMyFeed['0'][$cc][$jj]['date'],"gid" => $gidd, "screen" => $gidscreen); 
                                                                    }
                                                                //   $FriendFeedarray[] = array("groupname" => $Groupname, "groupphoto" => $Groupphoto, "text" => $viewMyFeed['0'][$cc][$jj]['text'], "photo" => $Feedphotoarray, "date" => $viewMyFeed['0'][$cc][$jj]['date'],"gid" => $gidd,"screen" => $gidscreen);
                                                                   unset($Feedphotoarray);
                                                                    // echo $viewMyFeed['0'][$cc][$jj]['date']."\n";  
                                                            } 
                                                   

                                                    //var_dump($FriendFeedarray);
                                                  //  $memcache_obj->set($sessionid., $FriendFeedarray, false, 86400);
                                                    //$memcache_obj->set($sessionid.$sessionid, $FriendFeedarray, false, 86400);
                                                        }
                                                    } 
                                                     $FF = new FriendFeed();
                                                   //  var_dump($FriendFeedarray);
                                                    $FriendFeedarray = $FF->TimeFeedSort($FriendFeedarray); 

                                                   // var_dump($FriendFeedarray)."\n"; 
                                              
                                                       $memcache_obj->set($sessionid."oldentriescache", $FriendFeedarray, false, 86400);
                                                     
                                              exit;            
                                          

}

if(isset($_GET['old']))
{   

   // echo $memcache_obj->get($sessionid."offset");
    //exit;
    while(true)
    {
         $FriendFeedarray = $memcache_obj->get($sessionid."oldentriescache");  
         if(!empty($FriendFeedarray)) break;
    }
     //$myid = $memcache_obj->get($sessionid."idpage");
      // echo $myid."\n";
      $FF = new FriendFeed();
                  
                 
                        for ($iiii=0; $iiii < count($FriendFeedarray); $iiii++) 
                            { 
                  
                            
                    ?>
                     <table class="table table-bordered row-fluid leftprofile1">
                    <tr>
                        <td >
                            

                             <div class="media">
                            <a class="pull-left" href=<?php echo "http://vk.com/".$FriendFeedarray[$iiii]['screen']; ?> target="_blank">
                              
                            <img class="media-object" src= <?php echo $FriendFeedarray[$iiii]['groupphoto'];   ?>>
                            </a>
                            <div class="media-body">
                             &nbsp;<a href=<?php echo "http://vk.com/".$FriendFeedarray[$iiii]['screen']; ?> target="_blank"><strong> <?php echo $FriendFeedarray[$iiii]['groupname'];   ?></strong></a>
                               <?php  
                                    if(!empty($FriendFeedarray[$iiii]['text']))
                                    {
                                        ?>
                                       
                                       <?php echo " "; ?> &nbsp; &nbsp; <div class="cutstring" data-display="none" data-max-length="200" data-show-text="Показать полностью.." data-hide-text="Свернуть..">                  
                                   <?php echo " &nbsp;".$FriendFeedarray[$iiii]['text']; ?></div>
                                    
                                        <?php
                                
                                    }
                               ?>

                                    <?php
                                     if(!empty($FriendFeedarray[$iiii]['photo']))
                                    {

                                     ?>
                                        <br>
                                        <?php
                                     for ($ii=0; $ii < count($FriendFeedarray[$iiii]['photo']); $ii++) 
                                        { 
                                            if($FriendFeedarray[$iiii]['photo'][$ii] != "")
                                            {
                                        ?>
                                
                                        <img src=<?php echo $FriendFeedarray[$iiii]['photo'][$ii];   ?> width="98%"> <br> 
                                    <br>
                                        <?php
                                            }
                                        }
                                         } 
                                            ?>                                    
                                                                      

                                    </div>
                                    
                           

                                </div>
                                <br>
                                 <div class="row">

                            <!-- Blog Sidebar Widgets Column -->

                             <div class="col-md-3">
                                     &nbsp;&nbsp; <font class="timetextago"><span class="glyphicon glyphicon-time"> </span>&nbsp;<?php echo $FF->timeAgo($FriendFeedarray[$iiii]['date']);   ?></font> 
                                        </div>
                                            <div class="col-md-5 col-md-offset-4">
                                    <a href=<?php echo "http://vk.com/".$FriendFeedarray[$iiii]['screen']; ?> target="_blank"><font class="groupslink">Открыть группу  <?php echo iconv_substr($FriendFeedarray[$iiii]['groupname'], 0, 10, 'UTF-8')."...";  ?>&nbsp;<span class="glyphicon glyphicon-share-alt"> </span></font></a>
                                        </div>
                                        
                                           

                                    </div>                        
                                            
                              
                           </td>
                           </tr>  
                             </table> 
                 

            <?php
            
            }
             $myid = $memcache_obj->get($sessionid."idpage");
             $offset = $memcache_obj->get($sessionid."offset");
             $offset = $offset+4; 
                                   
             $memcache_obj->set($sessionid."offset", $offset, false, 86400);
             $memcache_obj->set($sessionid."idpage", $myid, false, 86400);
            
             $urlFeedupdateold = "http://192.168.1.141/index.php?old=".$myid;
?>

  <div ic-src=<?php echo $urlFeedupdateold; ?> ic-trigger-on="scrolled-into-view" ic-indicator="#mars">
   
  </div>
   <script>
$(function() {
    $('.cutstring').cutstring();
});
</script>
  <?php

exit;
}



if(!empty($_GET['id']))
{   
     
 try { $stat_obj->addFeedWatchingrecord($sessionid,$_GET['id']); }
 catch (Exception $e) { echo "Не добавился элемент, когда посмотрел чужую ленту"; }
 try { $stat_obj->addFeedFollowersrecord($_GET['id'],$sessionid); }
 catch (Exception $e) { echo "Не добавился элемент, когда посмотрел чужую ленту в его просмотры"; }       

    if(empty($memcache_obj->get($sessionid."friends")))
    {
        $listFriends[] = $vk->getFriends();       
        $memcache_obj->set($sessionid."friends", $listFriends, false, 1200);
    }
    else  $listFriends = $memcache_obj->get($sessionid."friends");  
   ?>
 <?php 

      $friendid = [];
      $frlist[] = $memcache_obj->get($sessionid."friends");  

      $FriendObj = new Friends();
      $friendid = $FriendObj->CheckActiveFeedFriend($_GET['id'],$sessionid,$frlist);
    ?>

<div class="container">
        <div class="row">
            <div class="col-md-2 friendlistblock">
                 <div class="row">
                <div class="feedactiveprofile">
                 &nbsp;&nbsp;<h5>Активная лента</h5>                
                <div class="media">
                <a class="pull-left" href="#">                  
                <img class="media-object" src=<?php echo $friendid['0']['photo_medium']; ?> width="80px" heigth="60px">
                </a>
                <div class="media-body"><br>               
                 </div>
                    </div>
                      <h6><strong>&nbsp;<?php echo $friendid['0']['first_name']." ".$friendid['0']['last_name']; ?>&nbsp;</strong></h6><br>
                    </div>
                    </div>               
                    <h5>Ленты друзей</h5>                   
                    <div class="row">
                    <div class="col-md-12">
                     <?php                         
                     for ($i=0; $i <1 ; $i++) for ($j=0; $j < count($listFriends['0']) ; $j++) 
                        {
                        ?> <div class="row"><div class="friends"><a href=<?php echo "http://192.168.1.141/index.php?id=".$listFriends[$i][$j]['uid']; ?> class="friendsfont"> <strong><b><?php echo $listFriends[$i][$j]['first_name']." ".$listFriends[$i][$j]['last_name']; ?></b></strong></a><br><a href=<?php echo "http://192.168.1.141/index.php?id=".$listFriends[$i][$j]['uid']; ?>><img src=<?php echo $listFriends[$i][$j]['photo_medium']; ?>></a><br></div></div>
                        <?php
                         } 
                         ?>             
                    </div>
                    </div>
            </div>
<?php

$FriendFeedarray = $memcache_obj->get($sessionid.$sessionid);
$FriendFeedarray1 = $memcache_obj->get($sessionid);

if(empty($memcache_obj->get($sessionid."idpage")))
{
   $memcache_obj->set($sessionid."idpage", $_GET['id'], false, 1200);

    // echo $_GET['id']."\n";
      //  echo $memcache_obj->get($sessionid."idpage");
   unset($FriendFeedarray);
   unset($FriendFeedarray1);
   if(empty($FriendFeedarray))
    {
    if(empty($FriendFeedarray1))
    {
        //unset($FriendFeedarray);
        $GroupIds[] = $vk->getGroupsforWall($_GET['id']);
for($mm=0;$mm<count($GroupIds['0']);$mm++)
{
    if($GroupIdsStr == "") $GroupIdsStr = $GroupIds['0'][$mm];
    else $GroupIdsStr = $GroupIdsStr.",".$GroupIds['0'][$mm];
}
//echo $GroupIdsStr;
$Groupinfo[] = $vk->getGroupsById($GroupIdsStr);

// целое число запросов к группам 

$CountDivGroups = floor(count($Groupinfo['0'])/24);

// остаток от деления на 24, максимальное число запросов в группам

$CounterModGroups = count($Groupinfo['0']) % 24;
$CounterWallget = 0;
$CounterWallget24 = 24;

while($ccc<$CountDivGroups)
{

    $codeStr = 'var a=API.groups.get({"user_id":"'.$_GET['id'].'"}); var b=a; var d='.$CounterWallget.'; var v='.$CounterWallget24.';
        var c = [];
        while (d < v)
        {
         c.push(API.wall.get({"owner_id":-b[d],"count":"4"}));
         d = d+1; 
        };
        return c;';       

    $viewMyFeed[] = $vk->getExecuteFeedFriends($codeStr);

     // var_dump($viewMyFeed);
    for($cc=0; $cc<count($viewMyFeed['0']); $cc++)
    {
        
        for($jj = 1; $jj<5; $jj++)
        {    
               for($vv=0; $vv<count($Groupinfo['0']);$vv++)
               {

                    if("-".$Groupinfo['0'][$vv]['gid'] == $viewMyFeed['0'][$cc][$jj]['from_id']) 
                    {
                        $gidscreen = $Groupinfo['0'][$vv]['screen_name'];
                        $gidd = $Groupinfo['0'][$vv]['gid'];
                        $Groupphoto = $Groupinfo['0'][$vv]['photo'];
                        $Groupname = $Groupinfo['0'][$vv]['name'];                                           
                        break;
                    }
               }
                for ($vv=0; $vv < count($viewMyFeed['0'][$cc][$jj]['attachments']); $vv++) 
                { 
                    $Feedphotoarray[] = $viewMyFeed['0'][$cc][$jj]['attachments'][$vv]['photo']['src_big'];
                }
                 if(checkdatearr($viewMyFeed['0'][$cc][$jj]['date']) == 1)
                {
                    $FriendFeedarray[] = array("groupname" => $Groupname, "groupphoto" => $Groupphoto, "text" => $viewMyFeed['0'][$cc][$jj]['text'], "photo" => $Feedphotoarray, "date" => $viewMyFeed['0'][$cc][$jj]['date'],"gid" => $gidd, "screen" => $gidscreen); 
                }
               //$FriendFeedarray[] = array("groupname" => $Groupname, "groupphoto" => $Groupphoto, "text" => $viewMyFeed['0'][$cc][$jj]['text'], "photo" => $Feedphotoarray, "date" => $viewMyFeed['0'][$cc][$jj]['date'],"gid" => $gidd,"screen" => $gidscreen);
          unset($Feedphotoarray);     
        } 
    

    }

    unset($viewMyFeed);

    $CounterWallget = $CounterWallget + 24;
    $CounterWallget24 = $CounterWallget24 + 24;
    $ccc++;
}

if($CounterModGroups != 0)
{
    $CounterWallget = 0;
    $CounterWallget24 = 24;
    unset($viewMyFeed);

    $CounterMod = $ccc*24;
    $CounterModGroups = $CounterMod + $CounterModGroups;

    $codeStr = 'var a=API.groups.get({"user_id":"'.$_GET['id'].'"}); var b=a; var d='.$CounterMod.'; var v='.$CounterModGroups.';
        var c = [];
        while (d < v)
        {
         c.push(API.wall.get({"owner_id":-b[d],"count":"4"}));
         d = d+1; 
        };
        return c;';       

    $viewMyFeed[] = $vk->getExecuteFeedFriends($codeStr);
    //  var_dump($viewMyFeed);
    for($cc=0; $cc<count($viewMyFeed['0']); $cc++)
    {
        
        for($jj = 1; $jj<5; $jj++)
        {    
               for($vv=0; $vv<count($Groupinfo['0']);$vv++)
               {

                    if("-".$Groupinfo['0'][$vv]['gid'] == $viewMyFeed['0'][$cc][$jj]['from_id']) 
                    {
                        $gidscreen = $Groupinfo['0'][$vv]['screen_name'];
                        $gidd = $Groupinfo['0'][$vv]['gid'];
                        $Groupphoto = $Groupinfo['0'][$vv]['photo'];
                        $Groupname = $Groupinfo['0'][$vv]['name']; 
                        break;
                    }
               }
                for ($vv=0; $vv < count($viewMyFeed['0'][$cc][$jj]['attachments']); $vv++) 
                { 
                    $Feedphotoarray[] = $viewMyFeed['0'][$cc][$jj]['attachments'][$vv]['photo']['src_big'];
                }
                 if(checkdatearr($viewMyFeed['0'][$cc][$jj]['date']) == 1)
                {
                    $FriendFeedarray[] = array("groupname" => $Groupname, "groupphoto" => $Groupphoto, "text" => $viewMyFeed['0'][$cc][$jj]['text'], "photo" => $Feedphotoarray, "date" => $viewMyFeed['0'][$cc][$jj]['date'],"gid" => $gidd, "screen" => $gidscreen); 
                }
               // $FriendFeedarray[] = array("groupname" => $Groupname, "groupphoto" => $Groupphoto, "text" => $viewMyFeed['0'][$cc][$jj]['text'], "photo" => $Feedphotoarray, "date" => $viewMyFeed['0'][$cc][$jj]['date'],"gid" => $gidd,"screen" => $gidscreen);
               unset($Feedphotoarray);
        } 
    

    }

}
//var_dump($viewMyFeed);
// наш главный класс в котором пока есть методы только для сортировки и работы с датами
$FF = new FriendFeed();
$FriendFeedarray = $FF->TimeFeedSort($FriendFeedarray);

$memcache_obj->set($sessionid, $FriendFeedarray, false, 86400);

    }
    else
    {
        $FF = new FriendFeed();
        $FriendFeedarray = $FriendFeedarray1;
    }
}

$FF = new FriendFeed();
//var_dump($FriendFeedarray);
//$FriendFeedarray = $FF->FeedArraySlayer($FriendFeedarray);

$memcache_obj->set($sessionid, $FriendFeedarray, false, 86400);

}
else
{
    if($_GET['id'] == $memcache_obj->get($sessionid."idpage"))
    {
       
            if(empty($FriendFeedarray))
             {
             if(empty($FriendFeedarray1))
            {

                 $GroupIds[] = $vk->getGroupsforWall($_GET['id']);
                 for($mm=0;$mm<count($GroupIds['0']);$mm++)
                     {
                        if($GroupIdsStr == "") $GroupIdsStr = $GroupIds['0'][$mm];
                         else $GroupIdsStr = $GroupIdsStr.",".$GroupIds['0'][$mm];
                    }

                    $Groupinfo[] = $vk->getGroupsById($GroupIdsStr);
                    $CountDivGroups = floor(count($Groupinfo['0'])/24);
                        $CounterModGroups = count($Groupinfo['0']) % 24;
                        $CounterWallget = 0;
                        $CounterWallget24 = 24;

                     while($ccc<$CountDivGroups)
                        {

                            $codeStr = 'var a=API.groups.get({"user_id":"'.$_GET['id'].'"}); var b=a; var d='.$CounterWallget.'; var v='.$CounterWallget24.';
                                var c = [];
                                while (d < v)
                                {
                                 c.push(API.wall.get({"owner_id":-b[d],"count":"4"}));
                                 d = d+1; 
                                };
                                return c;';       

                            $viewMyFeed[] = $vk->getExecuteFeedFriends($codeStr);

     // var_dump($viewMyFeed);
                            for($cc=0; $cc<count($viewMyFeed['0']); $cc++)
                            {
                                
                                for($jj = 1; $jj<5; $jj++)
                                {    
                                       for($vv=0; $vv<count($Groupinfo['0']);$vv++)
                                       {

                                            if("-".$Groupinfo['0'][$vv]['gid'] == $viewMyFeed['0'][$cc][$jj]['from_id']) 
                                            {
                                                $gidscreen = $Groupinfo['0'][$vv]['screen_name'];
                                                $gidd = $Groupinfo['0'][$vv]['gid'];
                                                $Groupphoto = $Groupinfo['0'][$vv]['photo'];
                                                $Groupname = $Groupinfo['0'][$vv]['name'];                                           
                                                break;
                                            }
                                       }
                                        for ($vv=0; $vv < count($viewMyFeed['0'][$cc][$jj]['attachments']); $vv++) 
                                        { 
                                            $Feedphotoarray[] = $viewMyFeed['0'][$cc][$jj]['attachments'][$vv]['photo']['src_big'];
                                        }
                                         if(checkdatearr($viewMyFeed['0'][$cc][$jj]['date']) == 1)
                                            {
                                                $FriendFeedarray[] = array("groupname" => $Groupname, "groupphoto" => $Groupphoto, "text" => $viewMyFeed['0'][$cc][$jj]['text'], "photo" => $Feedphotoarray, "date" => $viewMyFeed['0'][$cc][$jj]['date'],"gid" => $gidd, "screen" => $gidscreen); 
                                            }
                                      // $FriendFeedarray[] = array("groupname" => $Groupname, "groupphoto" => $Groupphoto, "text" => $viewMyFeed['0'][$cc][$jj]['text'], "photo" => $Feedphotoarray, "date" => $viewMyFeed['0'][$cc][$jj]['date'],"gid" => $gidd, "screen" => $gidscreen);
                                  unset($Feedphotoarray);     
                                }                             

                            }
                      //      var_dump($viewMyFeed);
                        unset($viewMyFeed);

                        $CounterWallget = $CounterWallget + 24;
                        $CounterWallget24 = $CounterWallget24 + 24;
                        $ccc++;
                        }

                    if($CounterModGroups != 0)
                    {
                        $CounterWallget = 0;
                        $CounterWallget24 = 24;
                        unset($viewMyFeed);

                        $CounterMod = $ccc*24;
                        $CounterModGroups = $CounterMod + $CounterModGroups;

                        $codeStr = 'var a=API.groups.get({"user_id":"'.$_GET['id'].'"}); var b=a; var d='.$CounterMod.'; var v='.$CounterModGroups.';
                            var c = [];
                            while (d < v)
                            {
                             c.push(API.wall.get({"owner_id":-b[d],"count":"4"}));
                             d = d+1; 
                            };
                            return c;';       

                        $viewMyFeed[] = $vk->getExecuteFeedFriends($codeStr);
                        //  var_dump($viewMyFeed);
                        for($cc=0; $cc<count($viewMyFeed['0']); $cc++)
                        {
                            
                            for($jj = 1; $jj<5; $jj++)
                            {    
                                   for($vv=0; $vv<count($Groupinfo['0']);$vv++)
                                   {

                                        if("-".$Groupinfo['0'][$vv]['gid'] == $viewMyFeed['0'][$cc][$jj]['from_id']) 
                                        {
                                            $gidscreen = $Groupinfo['0'][$vv]['screen_name'];
                                            $gidd = $Groupinfo['0'][$vv]['gid'];
                                            $Groupphoto = $Groupinfo['0'][$vv]['photo'];
                                            $Groupname = $Groupinfo['0'][$vv]['name']; 
                                            break;
                                        }
                                   }
                                    for ($vv=0; $vv < count($viewMyFeed['0'][$cc][$jj]['attachments']); $vv++) 
                                    { 
                                        $Feedphotoarray[] = $viewMyFeed['0'][$cc][$jj]['attachments'][$vv]['photo']['src_big'];
                                    }
                                     if(checkdatearr($viewMyFeed['0'][$cc][$jj]['date']) == 1)
                                        {
                                            $FriendFeedarray[] = array("groupname" => $Groupname, "groupphoto" => $Groupphoto, "text" => $viewMyFeed['0'][$cc][$jj]['text'], "photo" => $Feedphotoarray, "date" => $viewMyFeed['0'][$cc][$jj]['date'],"gid" => $gidd, "screen" => $gidscreen); 
                                        }
                                   //$FriendFeedarray[] = array("groupname" => $Groupname, "groupphoto" => $Groupphoto, "text" => $viewMyFeed['0'][$cc][$jj]['text'], "photo" => $Feedphotoarray, "date" => $viewMyFeed['0'][$cc][$jj]['date'],"gid" => $gidd, "screen" => $gidscreen);
                                   unset($Feedphotoarray);
                            } 
                        

                        }

                                }
            //var_dump($viewMyFeed);
            // наш главный класс в котором пока есть методы только для сортировки и работы с датами
            $FF = new FriendFeed();
            $FriendFeedarray = $FF->TimeFeedSort($FriendFeedarray);
       
            $memcache_obj->set($sessionid, $FriendFeedarray, false, 86400);
            $memcache_obj->set($sessionid.$sessionid, $FriendFeedarray, false, 86400);
           
            //$memcache_obj->set($sessionid, $FriendFeedarray, false, 86400);
            }
        else
            {
            $FF = new FriendFeed();
            $FriendFeedarray = $FriendFeedarray1;
            }
        }

    $FF = new FriendFeed();
  //  var_dump($FriendFeedarray);
    $FriendFeedarray = $FF->TimeFeedSort($FriendFeedarray);
    //echo "vse ok";
 
   $memcache_obj->set($sessionid, $FriendFeedarray, false, 86400);
$memcache_obj->set($sessionid.$sessionid, $FriendFeedarray, false, 86400);

    }
    else
    {
       
          unset($FriendFeedarray);
          unset($FriendFeedarray1);
        //echo $_GET['id']."\n";
       // echo $memcache_obj->get($sessionid."idpage")."\n";
            //echo $_GET['id'];
         // echo $sessionid;
        
             $memcache_obj->set($sessionid."idpage", $_GET['id'], false, 1200);
       
          //echo $memcache_obj->get($sessionid."idpage");
            // exit;
//exit;
           //  echo $memcache_obj->get($sessionid."idpage");
            // unset($FriendFeedarray);
            $GroupIds[] = $vk->getGroupsforWall($_GET['id']);
            for($mm=0;$mm<count($GroupIds['0']);$mm++)
            {
         if($GroupIdsStr == "") $GroupIdsStr = $GroupIds['0'][$mm];
                else $GroupIdsStr = $GroupIdsStr.",".$GroupIds['0'][$mm];
            }
//echo $GroupIdsStr;
        $Groupinfo[] = $vk->getGroupsById($GroupIdsStr);

// целое число запросов к группам 

$CountDivGroups = floor(count($Groupinfo['0'])/24);

// остаток от деления на 24, максимальное число запросов в группам

$CounterModGroups = count($Groupinfo['0']) % 24;
$CounterWallget = 0;
$CounterWallget24 = 24;

while($ccc<$CountDivGroups)
{

    $codeStr = 'var a=API.groups.get({"user_id":"'.$_GET['id'].'"}); var b=a; var d='.$CounterWallget.'; var v='.$CounterWallget24.';
        var c = [];
        while (d < v)
        {
         c.push(API.wall.get({"owner_id":-b[d],"count":"4"}));
         d = d+1; 
        };
        return c;';       

    $viewMyFeed[] = $vk->getExecuteFeedFriends($codeStr);
    //var_dump($viewMyFeed);
     // var_dump($viewMyFeed);
    for($cc=0; $cc<count($viewMyFeed['0']); $cc++)
    {
        
        for($jj = 1; $jj<5; $jj++)
        {    
               for($vv=0; $vv<count($Groupinfo['0']);$vv++)
               {

                    if("-".$Groupinfo['0'][$vv]['gid'] == $viewMyFeed['0'][$cc][$jj]['from_id']) 
                    {
                        $gidscreen = $Groupinfo['0'][$vv]['screen_name'];
                        $gidd = $Groupinfo['0'][$vv]['gid'];
                        $Groupphoto = $Groupinfo['0'][$vv]['photo'];
                        $Groupname = $Groupinfo['0'][$vv]['name'];                                           
                        break;
                    }
               }
                for ($vv=0; $vv < count($viewMyFeed['0'][$cc][$jj]['attachments']); $vv++) 
                { 
                    $Feedphotoarray[] = $viewMyFeed['0'][$cc][$jj]['attachments'][$vv]['photo']['src_big'];
                }
                 if(checkdatearr($viewMyFeed['0'][$cc][$jj]['date']) == 1)
                {
                    $FriendFeedarray[] = array("groupname" => $Groupname, "groupphoto" => $Groupphoto, "text" => $viewMyFeed['0'][$cc][$jj]['text'], "photo" => $Feedphotoarray, "date" => $viewMyFeed['0'][$cc][$jj]['date'],"gid" => $gidd, "screen" => $gidscreen); 
                }
            //   $FriendFeedarray[] = array("groupname" => $Groupname, "groupphoto" => $Groupphoto, "text" => $viewMyFeed['0'][$cc][$jj]['text'], "photo" => $Feedphotoarray, "date" => $viewMyFeed['0'][$cc][$jj]['date'],"gid" => $gidd, "screen" => $gidscreen);
          unset($Feedphotoarray);     
        } 
    

    }

    unset($viewMyFeed);

    $CounterWallget = $CounterWallget + 24;
    $CounterWallget24 = $CounterWallget24 + 24;
    $ccc++;
}

if($CounterModGroups != 0)
{
    $CounterWallget = 0;
    $CounterWallget24 = 24;
    unset($viewMyFeed);

    $CounterMod = $ccc*24;
    $CounterModGroups = $CounterMod + $CounterModGroups;

    $codeStr = 'var a=API.groups.get({"user_id":"'.$_GET['id'].'"}); var b=a; var d='.$CounterMod.'; var v='.$CounterModGroups.';
        var c = [];
        while (d < v)
        {
         c.push(API.wall.get({"owner_id":-b[d],"count":"4"}));
         d = d+1; 
        };
        return c;';       

    $viewMyFeed[] = $vk->getExecuteFeedFriends($codeStr);
    //  var_dump($viewMyFeed);
    for($cc=0; $cc<count($viewMyFeed['0']); $cc++)
    {
        
        for($jj = 1; $jj<5; $jj++)
        {    
               for($vv=0; $vv<count($Groupinfo['0']);$vv++)
               {

                    if("-".$Groupinfo['0'][$vv]['gid'] == $viewMyFeed['0'][$cc][$jj]['from_id']) 
                    {
                        $gidscreen = $Groupinfo['0'][$vv]['screen_name'];
                        $gidd = $Groupinfo['0'][$vv]['gid'];
                        $Groupphoto = $Groupinfo['0'][$vv]['photo'];
                        $Groupname = $Groupinfo['0'][$vv]['name']; 
                        break;
                    }
               }
                for ($vv=0; $vv < count($viewMyFeed['0'][$cc][$jj]['attachments']); $vv++) 
                { 
                    $Feedphotoarray[] = $viewMyFeed['0'][$cc][$jj]['attachments'][$vv]['photo']['src_big'];
                }
                 if(checkdatearr($viewMyFeed['0'][$cc][$jj]['date']) == 1)
                {
                    $FriendFeedarray[] = array("groupname" => $Groupname, "groupphoto" => $Groupphoto, "text" => $viewMyFeed['0'][$cc][$jj]['text'], "photo" => $Feedphotoarray, "date" => $viewMyFeed['0'][$cc][$jj]['date'],"gid" => $gidd, "screen" => $gidscreen); 
                }
            //   $FriendFeedarray[] = array("groupname" => $Groupname, "groupphoto" => $Groupphoto, "text" => $viewMyFeed['0'][$cc][$jj]['text'], "photo" => $Feedphotoarray, "date" => $viewMyFeed['0'][$cc][$jj]['date'],"gid" => $gidd,"screen" => $gidscreen);
               unset($Feedphotoarray);
        } 
$FF = new FriendFeed();
$FriendFeedarray = $FF->TimeFeedSort($FriendFeedarray); 

$memcache_obj->set($sessionid, $FriendFeedarray, false, 86400);
$memcache_obj->set($sessionid.$sessionid, $FriendFeedarray, false, 86400);
    }
}

}
}

$memcache_obj->set($sessionid, $FriendFeedarray, false, 86400);
$memcache_obj->set($sessionid.$sessionid, $FriendFeedarray, false, 86400);

// код для проверки на кешевый массив самого себя, если у нас совпадает что мы открыли сами себя то обновляем переменную с ешем
if($sessionid == $_GET['id'])
{
    $memcache_obj->set($sessionid."me", $FriendFeedarray, false, 86400);
}

$urlFeedupdate = "http://192.168.1.141/index.php?news=".$_GET['id'];
$urlFeedCountUpdate = "http://192.168.1.141/index.php?groups=".$_GET['id'];
$urlMyProfile = "http://192.168.1.141/index.php?id=".$sessionid;

$FF = new FriendFeed();
?>


   <div class="col-md-7">
    <h5>
        Новостная лента 
    </h5><br>
    <?php
   // var_dump($FriendFeedarray);
   // var_dump($FriendFeedarray);
    ?>

  <center> <button class="btn" onclick="Intercooler.refresh($('#manual-update'));">Показать <font ic-src=<?php echo $urlFeedCountUpdate; ?> ic-poll="2s"></font> новых записей </button></center><br>

      <!--   -->



      <div id="manual-update" ic-src=<?php echo $urlFeedupdate; ?>>              
        <?php       
            for ($iiii=0; $iiii < count($FriendFeedarray); $iiii++) 
                {      
                
        ?>
         <table class="table table-bordered row-fluid leftprofile1 feedliner">
        <tr>
            <td >
                

                 <div class="media">
                <a class="pull-left" href=<?php echo "http://vk.com/".$FriendFeedarray[$iiii]['screen']; ?> target="_blank">
                  
                <img class="media-object" src= <?php echo $FriendFeedarray[$iiii]['groupphoto'];   ?>>
                </a>
                <div class="media-body">
                 &nbsp;<a href=<?php echo "http://vk.com/".$FriendFeedarray[$iiii]['screen']; ?> target="_blank"><strong> <?php echo $FriendFeedarray[$iiii]['groupname'];   ?></strong></a>
                   <?php  
                        if(!empty($FriendFeedarray[$iiii]['text']))
                        {
                            ?>
                           
                           <?php echo " "; ?> &nbsp; &nbsp; <div class="cutstring" data-display="none" data-max-length="200" data-show-text="Показать полностью.." data-hide-text="Свернуть..">                  
                       <?php echo " &nbsp;".$FriendFeedarray[$iiii]['text']; ?></div>
                        
                            <?php
                    
                        }
                   ?>

                        <?php
                         if(!empty($FriendFeedarray[$iiii]['photo']))
                        {

                         ?>
                            <br>
                            <?php
                         for ($ii=0; $ii < count($FriendFeedarray[$iiii]['photo']); $ii++) 
                            { 
                                 if($FriendFeedarray[$iiii]['photo'][$ii] != "")
                                            {
                            ?>               

        <a class="fancybox-effects-a" href=<?php echo $FriendFeedarray[$iiii]['photo'][$ii];   ?> data-fancybox-group="gallery" title=""><img src=<?php echo $FriendFeedarray[$iiii]['photo'][$ii];   ?> width="90%" alt="" /></a>
<br> 
                        <br>
                            <?php
                        }
                            }
                             } 
                                ?>                        
                                          
                       </div>                     
                    </div>
                    <br>
                     <div class="row">
                 <div class="col-md-3">
                         &nbsp;&nbsp; <font class="timetextago"><span class="glyphicon glyphicon-time"> </span>&nbsp;<?php echo $FF->timeAgo($FriendFeedarray[$iiii]['date']);   ?></font> 
                            </div>
                                <div class="col-md-5 col-md-offset-4">
                        <a href=<?php echo "http://vk.com/".$FriendFeedarray[$iiii]['screen']; ?> target="_blank"><font class="groupslink">Открыть группу  <?php echo iconv_substr($FriendFeedarray[$iiii]['groupname'], 0, 10, 'UTF-8')."...";  ?>&nbsp;<span class="glyphicon glyphicon-share-alt"> </span></font></a>
                            </div>                          
                             </div>                        
                                
                  
               </td>
               </tr>  
                 </table> 
     

<?php
}

$memcache_obj->set($sessionid."offset", 4, false, 1200);
$urlFeedupdateold = "http://192.168.1.141/index.php?old=".$_GET['id'];
$urlFeedupdateoldcache = "http://192.168.1.141/index.php?oldcache=".$_GET['id'];
?>
</div> 
 <font ic-src=<?php echo $urlFeedupdateoldcache; ?> ic-poll="10s">Более старые записи</font>
  <div ic-src=<?php echo $urlFeedupdateold; ?> ic-trigger-on="scrolled-into-view" ic-indicator="mars">   
  </div>  
</div>
 <div class="col-md-3">   
    <div class="row">
        <div class="col-md-12 leftprofile disabled"> 
      <?php 
      $friendid = [];
      $frlist[] = $memcache_obj->get($sessionid."friends");
      
      session_start();
    
          
       ?>
<?php
$watchotherfeeds = $stat_obj->seemyfeedwatching($sessionid);
$followers = $stat_obj->seemyfeedfollowers($sessionid);
?>
      &nbsp;&nbsp;<h5>Профиль</h5>
       
       <div class="row">     
              <div class="media">
                <a class="pull-left" href=<?php echo $urlMyProfile; ?>>                  
              <img class="media-object" src=<?php echo $_SESSION['img']; ?> width="80px" heigth="60px">               
                <div class="media-body">
                <a href=<?php echo $urlMyProfile; ?> class="profilelink"><h6><strong>&nbsp;<b><?php echo $_SESSION['fullname']; ?>&nbsp;</b></strong></h6></a>                   
                </div>
            </div>
        </div>
        <br>
        <div class="col-md-12">
            <div class="row">
        <div class="col-md-4">   
            <div class="row">
                <font class="profilebadgetext"> Новое </font><br>
                   <font class="profiledigittext"><b> 0 </b></font>
             </div>
        </div>
        <div class="col-md-4">   
            <div class="row">
               <font class="profilebadgetext"> Просмотров </font><br>
                    <font class="profiledigittext"><b> <?php echo $followers['id']; ?> </b></font>
            </div>
        </div>
        <div class="col-md-4">   
            <div class="row">
           <font class="profilebadgetext">  Посмотрел </font><br>
               <font class="profiledigittext"><b> <?php echo $watchotherfeeds['id']; ?> </b></font>
            </div>
        </div>
    </div>
    </div>
    <br>
   

     <br><br>              
</div>
    </div>
<?php   session_write_close(); ?>
 </div>
<br>
 <hr>
 <footer>
        <div class="row">
                <div class="col-lg-12">
                    <br>
                    <p>FriendFeed</p>
                </div>         
            </div>    
        </footer>
</div>
</div>
<?php
$memcache_obj->set($sessionid."friends", $listFriends, false, 1200);  
}
else
{
   // echo "Web page not created";
}
?>  
 <script type="text/javascript">
                    $(document).ready(function() {
                     
                        $('.fancybox').fancybox();
                        $(".fancybox-effects-a").fancybox({
                            helpers: {
                                title : {
                                    type : 'outside'
                                },
                                overlay : {
                                    speedOut : 0
                                }
                            }
                        });
                     
                        $(".fancybox-effects-b").fancybox({
                            openEffect  : 'none',
                            closeEffect : 'none',

                            helpers : {
                                title : {
                                    type : 'over'
                                }
                            }
                        });

                        // Set custom style, close if clicked, change title type and overlay color
                        $(".fancybox-effects-c").fancybox({
                            wrapCSS    : 'fancybox-custom',
                            closeClick : true,

                            openEffect : 'none',

                            helpers : {
                                title : {
                                    type : 'inside'
                                },
                                overlay : {
                                    css : {
                                        'background' : 'rgba(238,238,238,0.85)'
                                    }
                                }
                            }
                        });

                        // Remove padding, set opening and closing animations, close if clicked and disable overlay
                        $(".fancybox-effects-d").fancybox({
                            padding: 0,

                            openEffect : 'elastic',
                            openSpeed  : 150,

                            closeEffect : 'elastic',
                            closeSpeed  : 150,

                            closeClick : true,

                            helpers : {
                                overlay : null
                            }
                        });

                        /*
                         *  Button helper. Disable animations, hide close button, change title type and content
                         */

                        $('.fancybox-buttons').fancybox({
                            openEffect  : 'none',
                            closeEffect : 'none',

                            prevEffect : 'none',
                            nextEffect : 'none',

                            closeBtn  : false,

                            helpers : {
                                title : {
                                    type : 'inside'
                                },
                                buttons : {}
                            },

                            afterLoad : function() {
                                this.title = 'Image ' + (this.index + 1) + ' of ' + this.group.length + (this.title ? ' - ' + this.title : '');
                            }
                        });


                        /*
                         *  Thumbnail helper. Disable animations, hide close button, arrows and slide to next gallery item if clicked
                         */

                        $('.fancybox-thumbs').fancybox({
                            prevEffect : 'none',
                            nextEffect : 'none',

                            closeBtn  : false,
                            arrows    : false,
                            nextClick : true,

                            helpers : {
                                thumbs : {
                                    width  : 50,
                                    height : 50
                                }
                            }
                        });

                        /*
                         *  Media helper. Group items, disable animations, hide arrows, enable media and button helpers.
                        */
                        $('.fancybox-media')
                            .attr('rel', 'media-gallery')
                            .fancybox({
                                openEffect : 'none',
                                closeEffect : 'none',
                                prevEffect : 'none',
                                nextEffect : 'none',

                                arrows : false,
                                helpers : {
                                    media : {},
                                    buttons : {}
                                }
                            });

                        /*
                         *  Open manually
                         */

                        $("#fancybox-manual-a").click(function() {
                            $.fancybox.open('1_b.jpg');
                        });

                        $("#fancybox-manual-b").click(function() {
                            $.fancybox.open({
                                href : 'iframe.html',
                                type : 'iframe',
                                padding : 5
                            });
                        });

                        $("#fancybox-manual-c").click(function() {
                            $.fancybox.open([
                                {
                                    href : '1_b.jpg',
                                    title : 'My title'
                                }, {
                                    href : '2_b.jpg',
                                    title : '2nd title'
                                }, {
                                    href : '3_b.jpg'
                                }
                            ], {
                                helpers : {
                                    thumbs : {
                                        width: 75,
                                        height: 50
                                    }
                                }
                            });
                        });


                    });
               </script> 
                   
                <script>
            $(function() {
                $('.cutstring').cutstring();
            });
            </script>  
</body>
</html>
<?php
//xdebug_stop_trace();
?>