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

    while(true)
    {
         $FriendFeedarray = $memcache_obj->get($init_obj->sessionid."oldentriescache");  
         if(!empty($FriendFeedarray)) break;
    } 
              
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
            /*
            	Блок для рефакторинга , как нить разгрести эту кучу лишних переменных
            */
             $myid = $memcache_obj->get($init_obj->sessionid."idpage");
             $offset = $memcache_obj->get($init_obj->sessionid."offset");
             $offset = $offset+3;                                   
             $memcache_obj->set($init_obj->sessionid."offset", $offset, false, 86400);
             $memcache_obj->set($init_obj->sessionid."idpage", $myid, false, 86400);
             $urlFeedupdateold = "http://feedbrother.com/old";
             /*
             	Остальыне блоки в том числе с версткой нормально
             */
?>

  <div ic-src=<?php echo $urlFeedupdateold; ?> ic-trigger-on="scrolled-into-view" ic-indicator="#mars">   
  </div>
   <script>
$(function() {
    $('.cutstring').cutstring();
});
</script>

