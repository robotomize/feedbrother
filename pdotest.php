<?php
require_once "conf/db.php";
require_once "classes/base.php";
$id = null;
$owner_id = "123";
$profileimg = "http://123.jpg";
$fullName = "sidor petrovich";
$profilescreenname = "huila";
$provider = "vk";
$email = "mail@mail.ru";

$newuser = new UserModel($id,$owner_id, $profileimg,$fullName, $profilescreenname, $provider, $email);  
$data = array($newuser->id,$newuser->id_vk,$newuser->img_src,$newuser->name,$newuser->screenname,$newuser->provider,$newuser->email);
      
$modeldb = new PdoModel();
         try 
            {  
            $DBH = new PDO("mysql:host=$modeldb->host;dbname=$modeldb->dbname", $modeldb->user, $modeldb->pass,array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''));  
            $DBH->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );                     
            }  
            catch(PDOException $e) { file_put_contents('/var/www/FeedBrother/PDOErrors.txt', $e->getMessage(), FILE_APPEND); }

$STH = $DBH->prepare('SELECT id from usersVk where id_vk=?');  
$STH->setFetchMode(PDO::FETCH_ASSOC);  
$STH->execute(array('6139701')); 
while($row = $STH->fetch()) {  
   // echo $row['id'] . "\n";  
     
}

try{ //try-catch - отлавливаем ошибки
    $result = DBmodel::getInstance()->prepare("SELECT id from usersVk where id_vk=?");
    $result->setFetchMode(PDO::FETCH_ASSOC); 
    $result->execute(array('6139701')); 
while($row = $result->fetch()) {  
    echo $row['id'];  
     
}
}catch(PDOException $e){ echo $e->getMessage(); }

            ?>    