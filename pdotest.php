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
      
               try 
                {  
                    $STH = $DBH->prepare("INSERT INTO users (id,id_vk, img_src, name, screenname, provider, email) values (?,?,?,?,?,?,?)");
                    $STH->execute($data);  
                }  
                catch(PDOException $e) 
                {                      
                    file_put_contents('/var/www/FeedBrother/PDOErrors.txt', "Проблема с добавлением нового пользователя", FILE_APPEND);  
                }


            ?>    