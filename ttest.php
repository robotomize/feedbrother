<?php
   session_start();
require_once "conf/db.php";
require_once "classes/base.php";



/*

*/
$GLOBALS['sitemap'] = array (
    '_404' => 'page404.php',   
    '/' => 'index.php',   
    '/upd(/[0-9]+)?' => 'upd.php',   
    '/stories(/[0-9]+)?' => 'storypage.php',  
    '/user(/[0-9]+)?' => 'vk.php',    
    '/me' => 'vk.php',
    '/profile' => 'me.php',
    '/news' => 'newsget.php',
    '/groups(/[0-9]+)?' => 'groupsget.php',
    '/oldcache' => 'oldcacheget.php',
    '/old' => 'oldget.php',
    '/logout' => 'logout.php',  
); 

$sm = new uSitemap();
$routed_file = $sm->classname; 
//if ($routed_file != "index.php") require_once "header.php";
if($routed_file != "page404.php")
{ 
   switch ($routed_file) 
 {
        case 'me.php':
          require_once "header.php";
          break;

         case 'vk.php':
          require_once "header.php";
          break;
        
      }  
}       
require_once('/var/www/FeedBrother/'.$routed_file); 
?>
