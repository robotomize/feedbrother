<?php
set_time_limit(3600);

require 'conf/db.php';
require 'classes/base.php';
session_start();
$vk = new VkApi(array(
    'apiKey' => 'E8tyn9sgbwaM2MG9ZCSq',
    'appId' => '4581515',
    'authRedirectUrl' => 'http://192.168.1.141/vk.php',
)); 
$GroupIdsStr = "";
$FriendFeedarray = [];
$sessionid = $_SESSION['id'];
$urlMyPage = "http://192.168.1.141/me.php?back=".$sessionid;
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
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="http://192.168.1.141/vk.php">  <font class="menutexttopglyph"> <span class="glyphicon glyphicon-th-list "></span></font><font class="menutexttop">&nbsp;FriendFeed</a></font> 
            </div>
            <?php 
            session_start();
            ?>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right">
	                <li>
	                 <center><a href=<?php echo $urlMyPage; ?>>   <img src=<?php echo $_SESSION['img']; ?> width="50px" heigth="50px" class="img-circle"></a>&nbsp;&nbsp;&nbsp;&nbsp;</center>
	                </li>
                    <li  class="toppullrightlink">  
                     <a href="http://192.168.1.141/vk.php?act=logout" class="toppullrightlink"><font class="toppullrightlink smalarrow">выйти</font></a>
                    </li>
                </ul>
            </div>
        </div>        
        <?php
        session_write_close();
        ?>
    </nav>
    <?php
    if(isset($_GET['back']))
	{
	    if(empty($memcache_obj->get($sessionid."idpage"))) $memcache_obj->set($sessionid."idpage", $sessionid, false, 1200);	   
	    else $memcache_obj->set($sessionid."idpage", $sessionid, false, 1200); 
	    $FriendFeedarray = $memcache_obj->get($sessionid."me");     
	 	 $FF = new FriendFeed();
	    if(empty($memcache_obj->get($sessionid."friends")))
	    {
	        $listFriends[] = $vk->getFriends();       
	        $memcache_obj->set($sessionid."friends", $listFriends, false, 1200);
	    }
	    else $listFriends = $memcache_obj->get($sessionid."friends");
	   
	      $friendid = [];
	      $frlist[] = $memcache_obj->get($sessionid."friends");   
	      session_start();
	                $friendid['0']['last_name'] = "";
	                $friendid['0']['first_name'] = $_SESSION['fullname'];
	                $friendid['0']['photo_medium'] = $_SESSION['img'];
	                session_write_close();            
	              ?>

			<div class="container">
	        <div class="row">
            <div class="col-md-2 friendlistblock">
                <div class="row">
                <div class="feedactiveprofile">
                 &nbsp;&nbsp;<h5>Активная лента</h5>                
                <div class="media">
                <a class="pull-left" href=<?php echo "http://192.168.1.141/vk.php?id=".$_GET['back']; ?>>
                  
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

                        ?> <div class="row"><div class="friends"><a href=<?php echo "http://192.168.1.141/vk.php?id=".$listFriends[$i][$j]['uid']; ?> class="friendsfont"> <strong><b><?php echo $listFriends[$i][$j]['first_name']." ".$listFriends[$i][$j]['last_name']; ?></b></strong></a><br><a href=<?php echo "http://192.168.1.141/vk.php?id=".$listFriends[$i][$j]['uid']; ?>><img src=<?php echo $listFriends[$i][$j]['photo_medium']; ?>></a><br></div></div>
                        <?php
                         } 
                         ?>             
                    </div>
                </div>

            </div>
			<?php
			$urlFeedupdate = "http://192.168.1.141/vk.php?news=".$_GET['back'];
			$urlFeedCountUpdate = "http://192.168.1.141/vk.php?groups=".$_GET['back'];
			$urlMyProfile = "http://192.168.1.141/vk.php?id=".$sessionid;
			session_write_close();
			$FF = new FriendFeed();
			?>
		   <div class="col-md-7">
		    <h5>
		        Новостная лента 
		    </h5><br>
		  	<center> <button class="btn" onclick="Intercooler.refresh($('#manual-update'));">Показать <font ic-src=<?php echo $urlFeedCountUpdate; ?> ic-poll="2s"></font> новых записей </button></center><br>
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
		$urlFeedupdateold = "http://192.168.1.141/vk.php?old=".$_GET['back'];
		$urlFeedupdateoldcache = "http://192.168.1.141/vk.php?oldcache=".$_GET['back'];
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
		                $friendid['0']['last_name'] = "";
		                $friendid['0']['first_name'] = $_SESSION['fullname'];
		               $friendid['0']['photo_medium'] = $_SESSION['img'];  
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
		    			<br><br>             
						</div>
		    			</div>
					<?php session_write_close(); ?>
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