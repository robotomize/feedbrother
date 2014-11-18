<?php
set_time_limit(3600);
session_start();
$vk = new VkApi(array(
    'apiKey' => '',
    'appId' => '',
    'authRedirectUrl' => 'http://192.168.1.141/me',
)); 
$sessionid = $_SESSION['id'];
    /*
        рефакторинг требуется
    */
    $friendid['0']['last_name'] = "";                   
    $friendid['0']['first_name'] = $_SESSION['fullname'];
    $friendid['0']['photo_medium'] = $_SESSION['img'];
session_write_close();
$FriendFeedarray = [];
$urlMyPage = "http://192.168.1.141/profile";
$sessionid = $_SESSION['id'];

$init_obj = new InitUriFromRouter($sessionid);
$newiduser = $init_obj->sessionid;
$init_obj->newiduser = $init_obj->sessionid;
$stat_obj = new Statistic();
$cache_obj = new Caching();
$FriendObj = new Friends();
$url_obj = new Urlstorage($init_obj->newiduser);
        /*
            Блок определения данных для вывода статистики в профиле
        */
        $watchotherfeeds = $stat_obj->seemyfeedwatching($init_obj->sessionid);  // -->class static
        $followers = $stat_obj->seemyfeedfollowers($init_obj->sessionid);       // -->class static
        $newgroupres = $stat_obj->viewnewgroup($init_obj->sessionid);           // -->class static

?>
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
                <a class="navbar-brand" href="http://192.168.1.141/">  <font class="menutexttopglyph"> <span class="glyphicon glyphicon-th-list "></span></font><font class="menutexttop">&nbsp;<b>FeedBrother</b></a></font> 
                 <a class="navbar-brand" href="http://192.168.1.141/"><font class="menutexttopsm">&nbsp;<b>Получи доступ к <font class="menutexttopsminterest">лентам друзей</font>, просматривай <font class="menutexttopsminterest">интересное </font></b> </a></font>  
                 
            </div>
            <?php 
            session_start();
            ?>            
            <div class="collapse navbar-collapse">
                <ul class="nav navbar-nav navbar-right">
                    <li>
                     <center><font class="ownfeedtextsm"></font><a href=<?php echo $urlMyPage; ?>>   <img src=<?php echo $_SESSION['img']; ?> width="40px" heigth="40px" class="img-circle"></a></center>
                    </li>
                    <li  class="toppullrightlink">  
                      <a href="http://192.168.1.141/logout" class="toppullrightlink"><font class="toppullrightlink smalarrow"><b>выйти</b></font></a>
                    </li>
                </ul>
            </div>      
        </div>
        <?php
        session_write_close();
        ?>
    </nav> 
    <?php
    if(isset($newiduser))
	{
	    if(empty($memcache_obj->get($init_obj->sessionid."idpage"))) $memcache_obj->set($init_obj->sessionid."idpage", $init_obj->sessionid, false, 86400);	   
	    else $memcache_obj->set($init_obj->sessionid."idpage", $init_obj->sessionid, false, 86400); 
        /*
            Здесь инициализация ключевого для страницы массива из которого формируется лента
        */
	    $FriendFeedarray = $memcache_obj->get($init_obj->sessionid."me"); 
        // мы загоняем ленту обратно в кеш, чтобы перезаписать старый
        $memcache_obj->set($init_obj->sessionid, $FriendFeedarray, false, 86400);    
        $memcache_obj->set($init_obj->sessionid.$init_obj->sessionid, $FriendFeedarray, false, 86400);  
	 	$FF = new FriendFeed();
        /*
            Блок для рефакторинга, через класс friends
        */ 
         try { $listFriends = $FriendObj->CheckFriendlistFromCache($memcache_obj,$init_obj->sessionid,$vk); }   // -->class friends
         catch (Exception $e) { echo "Не сработал метод кеширования друзей me.php"; } 

	              ?>
			<div class="container">
        <div class="row">
            <div class="col-lg-2 col-md-2 friendlistblock">
                 <div class="row">
                <div class="feedactiveprofile">
                 &nbsp;&nbsp;<h5><b><strong><font class="maintextforfeedactiveprofile">Активная лента</font></strong></b></h5>                
                <div class="media">
                <a class="pull-left" href=<?php echo Urlstorage::urlMyProfile; ?>>                               
                <img class="media-object img-circle" src=<?php echo $friendid['0']['photo_medium']; ?> width="80px" heigth="60px">
                </a>
                <div class="media-body"><br>               
                 </div>
                    </div>
                      <h6><strong>&nbsp;<font class="maintextforfeedactiveprofile"><?php echo $friendid['0']['first_name']." ".$friendid['0']['last_name']; ?></font>&nbsp;</strong></h6><br>
                    </div>
                    </div>               
                    <h5><b>Ленты друзей</b></h5>                   
                    <div class="row">
                    <div class="col-md-12">
                     <?php                         
                     for ($i=0; $i <1 ; $i++) for ($j=0; $j < count($listFriends['0']) ; $j++) 
                        {
                        ?> <div class="row"><div class="friends"><a href=<?php echo "http://192.168.1.141/user/".$listFriends[$i][$j]['uid']; ?> class="friendsfont"> <strong><b><?php echo $listFriends[$i][$j]['first_name']." ".$listFriends[$i][$j]['last_name']; ?></b></strong></a><br><a href=<?php echo "http://192.168.1.141/user/".$listFriends[$i][$j]['uid']; ?>><img src=<?php echo $listFriends[$i][$j]['photo_medium']; ?> class="img-circle"></a><br></div></div>
                        <?php
                         } 
                         ?>             
                    </div>
                    </div>
            </div>
			<?php           
			session_write_close();           
			?>
		  <div class="col-md-7">
    <h5>
    <strong>   <b><font class="maintextforfeedactiveprofile"> Новостная лента </font></b></strong>
    </h5><br>

  <center> <button class="btn" onclick="Intercooler.refresh($('#manual-update'));"><b>Показать <font ic-src=<?php echo $url_obj->urlFeedCountUpdate; ?> ic-poll="2s"></font> новых записей </b></button></center><br>
      <div id="manual-update" ic-src=<?php echo Urlstorage::urlFeedupdate; ?>>              
        <?php       
            for ($iiii=0; $iiii < count($FriendFeedarray); $iiii++) 
                {                  
        ?>
         <table class="table table-bordered row-fluid leftprofile1 feedliner">
        <tr>
            <td >             
             <?php
                       if($newiduser != $init_obj->sessionid) 
                       {                    
                        if($MyProfilegroupcache != 0) 
                            {                               
                                if($cache_obj->checkNewGroup($FriendFeedarray[$iiii]['gid'],$MyProfilegroupcache) == 1)
                                {
                                  ?><h5><span class="label label-danger">Не подписан</span></h5><?php  
                                }
                                else
                                {
                                   ?><h5><span class="label label-success">&nbsp;&nbsp;Подписан</span></h5> <?php
                                }
                            }
                        }    
                        ?> 
                                 
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
                    <div class="col-md-3">
                       
                    
                    </div>        
                                <div class="col-md-5 col-md-offset-1">
                        <a href=<?php echo "http://vk.com/".$FriendFeedarray[$iiii]['screen']; ?> target="_blank"><font class="groupslink">Открыть группу  <?php echo iconv_substr($FriendFeedarray[$iiii]['groupname'], 0, 10, 'UTF-8')."...";  ?>&nbsp;<span class="glyphicon glyphicon-share-alt"> </span></font></a>
                            </div>                          
                             </div>                     
               
                   </td>
                   </tr>  
                     </table>    

		<?php
		}       
		$memcache_obj->set($init_obj->sessionid."offset", 3, false, 86400);       
      
		?>
		</div> 
		 <font ic-src=<?php echo Urlstorage::urlFeedupdateoldcache; ?> ic-poll="10s">Более старые записи</font>
		  <div ic-src=<?php echo Urlstorage::urlFeedupdateold; ?> ic-trigger-on="scrolled-into-view" ic-indicator="mars">   
		  </div>  
		</div>
		 <div class="col-md-3">   
		    <div class="row">
		        <div class="col-md-12 leftprofile disabled"> 		      
		      &nbsp;&nbsp;<h5><b><font class="maintextforfeedactiveprofile">Профиль</font></b></h5>    
		    <div class="row">     
              <div class="media">
                <a class="pull-left" href=<?php Urlstorage::urlMyProfile; ?>>                  
              <img class="media-object img-circle" src=<?php echo $_SESSION['img']; ?> width="80px" heigth="60px">               
                <div class="media-body">
                <a href=<?php echo Urlstorage::urlMyProfile; ?> class="profilelink"><h6><strong>&nbsp;<b><font class="maintextforfeedactiveprofile"><?php echo $_SESSION['fullname']; ?></font>&nbsp;</b></strong></h6></a>                   
                </div>
                <?php session_write_close(); ?>
            </div>
        </div>
        <hr>
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-4">   
            <div class="row">
                <font class="profilebadgetext"> Новых групп </font><br>
                   <font class="profiledigittext"><b> <?php echo $newgroupres['count']; ?></b></font>
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
		    			<br><br>             
						</div>
		    			</div>
					
		 			</div>
				<br>
				 <hr>
		        <footer>
		            <div class="row">
		                <div class="col-lg-12">
		                    <br>
		                    <p>FriendFeed</p>

		                </div>
		                <!-- /.col-lg-12 -->
		            </div>
		            <!-- /.row -->
		        </footer>
		</div>
		</div>
		<?php

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