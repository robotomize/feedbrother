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
$stat_obj = new Statistic();
$cache_obj = new Caching();
$FF = new FriendFeed();
$FriendFeedarray = $memcache_obj->get($init_obj->sessionid.$init_obj->sessionid);
$NewmessageCount = $memcache_obj->get($init_obj->sessionid."countnewmessage");

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
$memcache_obj->set($init_obj->sessionid, $FriendFeedarray, false, 86400);
  }
  else
  {
    $FriendFeedarray = $memcache_obj->get($init_obj->sessionid);
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
                           echo " "; ?> &nbsp; &nbsp; <div class="cutstring" data-display="none" data-max-length="200" data-show-text="Показать полностью.." data-hide-text="Свернуть..">                  
                        &nbsp;<?php echo link_it($FriendFeedarray[$iiii]['text']); ?></div>                        
                            <?php                    
                        }                  
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

              $memcache_obj->set($init_obj->sessionid."countnewmessage", 0, false, 300);                    
                      ?>
 <script>
$(function() {
    $('.cutstring').cutstring();
});
</script>
<?php
