<?php

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

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/agency.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="font-awesome-4.1.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="http://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css">
    <link href='http://fonts.googleapis.com/css?family=Kaushan+Script' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Droid+Serif:400,700,400italic,700italic' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Roboto+Slab:400,100,300,700' rel='stylesheet' type='text/css'>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <style>

body {
    padding-top: 100px; /* Required padding for .navbar-fixed-top. Remove if using .navbar-static-top. Change if height of navigation changes. */
}
    .friends
    {

    }
    .friends:hover
    {
        background-color: #F2F2F2;
    }
    .ulliright
    {
        color: #000;
    }
  .leftprofile
      {
       background-color: #F5F5F5;
         -webkit-font-smoothing: inherit;
             -moz-font-smoothing: inherit;
             -ms-font-smoothing: inherit;
             -o-font-smoothing: inherit;
             font-smoothing: inherit;
      }
      .leftprofile1
      {
       background-color: #FAFAFA;
         -webkit-font-smoothing: inherit;
             -moz-font-smoothing: inherit;
             -ms-font-smoothing: inherit;
             -o-font-smoothing: inherit;
             font-smoothing: inherit;
      }
       .cutstring-toggle {
    color: #236D75;
    
    cursor: hand;
    cursor: pointer;
        }
        .cutstring-toggle:hover {
    color: #19555C;
    
    cursor: hand;
    cursor: pointer;
        }
</style>

 

</head>
<body>

    <!-- Navigation -->
   <nav class="navbar navbar-default navbar-fixed-top navbar-shrink">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header page-scroll">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#page-top">   <font class="menutexttop">FriendFeed</a></font> 
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="navbar-right">
                 <li class="ulliright">
                         <?php 
                         session_start();
                         ?>
                      <div class="media">
                        <a class="pull-left" href="#">
                  
                            <img class="media-object" src=<?php echo $_SESSION['img']; ?> width="40px" heigth="20px">
                            </a>
                                <div class="media-body">
                            <h6><strong>&nbsp;<?php echo $_SESSION['fullname']; ?>&nbsp;</strong></h6>
                   
                            </div>

                        </div>

                   <?php
                    // echo $_SESSION['fullname'];
                    session_write_close();
                       
                    ?>
                
                 </li>

                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container-fluid -->
<hr>
    </nav>


<?php
// здесь пока будет блок вспомогательных функций, потом в отдельный файл вынесу

function FeedDiffarray($a, $b) {
    if ($a === $b) { return 0; }
    return ($a > $b)? 1:-1;
}


set_time_limit(3600);
//замер времени выполнения кода 


// пока не используется PDO, используется обычный драйвер, отлвоа ошибок ничего нет
$host = "localhost";   
$user = "root";   
$pass = "13";    
 if(!mysql_connect($host, $user, $pass)) exit(mysql_error()); 
mysql_query("SET character_set_client='UTF8'"); 
mysql_query("SET character_set_results='UTF8'"); 
mysql_query("SET collation_connection='UTF8'");
mysql_query("SET NAMES UTF8");
mysql_select_db("frfeed") or die(mysql_error()); 

 //Создаём новый объект. Также можно писать и в процедурном стиле
    $memcache_obj = new Memcache;
 
    //Соединяемся с нашим сервером
    $memcache_obj->connect('127.0.0.1', 11211) or die("could not connect");


/**
 * @class VkApi
 * @author Maslakov Alexander <jmas.ukraine@gmail.com> класс для работы с VK написал вот этот чувачек
 */

class VkApi
{
    public $apiKey;
    public $appId;
    public $login;
    public $password;
    public $authRedirectUrl;
    public $apiUrl = 'https://api.vk.com/method/';
    public $v = '2.0';
    private $_sid;
   
   
    public function __construct($options)
    {
        foreach ($options as $key=>$value) {
            $this->{$key} = $value;
        }
       
        $this->_auth();
    }
   
   
    private function _auth()
    {


       // $token = file_get_contents('./Cookies.txt');
       $token = $_SESSION['tok'];
        if (isset($_GET['code'])) {
            $url  = 'https://oauth.vk.com/access_token?client_id='.$this->appId.'&client_secret='. $this->apiKey .'&code=' . $_GET['code'] . '&redirect_uri=' . $this->authRedirectUrl;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_REFERER, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
           
            $responce = curl_exec($ch);
           
            curl_close($ch);
   
            $responce = json_decode($responce);
       
            if (isset($responce->access_token)) {
                $_SESSION['tok'] = $responce->access_token;
             //    file_put_contents('./Cookies.txt', $responce->access_token);
                 $token = $responce->access_token;
                 $owner_id = $responce->user_id;
                 //поулчаем данные о себе чтобы записать нас в сессию
                 $GetProfile = file_get_contents("https://api.vk.com/method/users.get?fields=nickname,screen_name,photo_medium,photo_big,city,bdate,sex&uid=$owner_id&access_token=$token");
                 $profile = json_decode($GetProfile , true);

                 $fullName = $profile['response']['0']['first_name']." ".$profile['response']['0']['last_name'];
                 $profileimg = $profile['response']['0']['photo_medium'];
                 $profilescreenname = $profile['response']['0']['nickname'];
                 // скидываем себя самого в сессию, чтобы быстро извлекать после авторизации
                 $_SESSION['id'] = $owner_id;
                $_SESSION['fullname'] = $fullName;
                $_SESSION['img'] = $profileimg;
            // если такой есть в базе то ничего, идем дальше, то есть мы
            if(empty(mysql_fetch_assoc(mysql_query("SELECT * FROM users WHERE id_vk = '$owner_id'"))))
               {                
                    mysql_query("INSERT INTO users VALUES (null, '$owner_id', '$profileimg', '$fullName', '$profilescreenname')") or die(mysql_error());
                    mysql_close();                
            
                }

            } else  throw new Exception('VK API error.');             
            
        }
       
        if (empty($token)) {

            $url = "https://oauth.vk.com/authorize?client_id="
                   . $this->appId . "&redirect_uri=http://192.168.1.141/index.php&display=page&response_type=code&scope=video,offline,groups,friends,photos,notify";
 
            header('Location: ' . $url); 
      
        }
       
        $this->_accessToken = $token;

    }
 
    public function get($method, $params=false)
    {

                if (! $params) $params = array();
 
                $params['format'] = 'json';
        $url = $this->apiUrl . $method;
        $params['access_token'] = $this->_accessToken;
               
        ksort($params);
               
        $sig = '';
               
        foreach ($params as $k=>$v) {
                        $sig .= $k.'='.$v;
                }
               
        $sig .= $this->apiKey;
               
        $params['sig'] = md5($sig);
               
        $query = $url . '?' . $this->_params($params);
       
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_URL, $query);
        curl_setopt($ch, CURLOPT_REFERER, $query);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)");
        $res = curl_exec($ch);
        curl_close($ch);
 
        return json_decode($res, true);

        }
   
 
    public function getLikesCountByUrl($pageUrl)
    {
        if (parse_url($pageUrl, PHP_URL_HOST) != parse_url($this->authRedirectUrl, PHP_URL_HOST)) {
            throw new Exception('Page URL not valid!');
        }
       
        $request = $this->get('likes.getList', array(
            'type' => 'sitepage',
            'owner_id' => $this->appId,
            'page_url' => $pageUrl,
        ));
       
        $this->_responce($request);
    }
 
 
    public function searchVideo($q, $offset)
    {        
        $request = $this->get('video.search', array(
            'q' => $q,
            'offset' => $offset,
        ));
 
        return $this->_responce($request);
    }
   
   
    public function getWallGroups($owner_id)
    {
        
        $request = $this->get('wall.get', array(
            'owner_id' => "-".$owner_id, 
            'count' => "4",          
            
        ));
       
        return $this->_responce($request);
    }

    public function getGroups($owner_id)
    {
        $request = $this->get('groups.get', array(
            'user_id' => $owner_id,
            'fields' => 'description,activity,members_count',
            'extended' => '1',            
        ));
 
        return $this->_responce($request);
    }

     public function getGroupsforWall($owner_id)
    {
        $request = $this->get('groups.get', array(
            'user_id' => $owner_id,                      
        ));
 
        return $this->_responce($request);
    }

     public function getFriends($owner_id)
    {
        $request = $this->get('friends.get', array(
            'owner_id' => $owner_id,
            'fields' => 'photo_medium',
            
        ));
 
        return $this->_responce($request);
    }

     public function getUsers($owner_id)
    {
        $request = $this->get('users.get', array(
            'uids' => $owner_id,
            'fields' => 'uid, first_name, last_name, nickname, photo_medium',
            
        ));
 
        return $this->_responce($request);
    }
       public function Friendsonline($owner_id)
    {
        $request = $this->get('friends.getOnline', array(
            'user_id' => $owner_id,            
            
        ));
 
        return $this->_responce($request);
    }


      public function getFeed($owner_id)
    {
        $request = $this->get('newsfeed.get', array(
            'owner_id' => $owner_id,
            
        ));
 
        return $this->_responce($request);
    }
    
       public function getGroupsById($ids)
    {
        $request = $this->get('groups.getById', array(
            'group_ids' => $ids,            
            
        ));
 
        return $this->_responce($request);
    }
 
 
        private function _params($params) {
                $pice = array();
                foreach($params as $k=>$v) {
                        $pice[] = $k.'='.urlencode($v);
                }
                return implode('&',$pice);
        }
 
 
    private function _responce($request)
    {
        if (isset($request['response'])) {
            return $request['response'];
        } else if (isset($request['error'])) {
            throw new Exception($request['error']['error_msg']);
        }
       
        return null;
    } 

    public function getExecuteFeedFriends($code)
    {
        $request = $this->get('execute', array(
            'code' => $code,                     
        ));
 
        return $this->_responce($request);
    }

}
//  класс с методами для формирования конечного вида ленты
class FriendFeed
{

    public function timeAgo($timestamp, $granularity=2, $format='Y-m-d H:i:s')
    { 
        $difference = time() - $timestamp; 
        if($difference < 0) return 'только что'; 
        elseif($difference < 864000)
            { 
                $periods = array('нд' => 604800,'дн' => 86400,'ч' => 3600,'м' => 60,'с' => 1); 
                $output = ''; 
                foreach($periods as $key => $value)
                    { if($difference >= $value)
                        { $time = round($difference / $value); 
                            $difference %= $value; $output .= ($output ? ' ' : '').$time.' '; 
                            $output .= (($time > 1 && $key == 'дней') ? $key.'секунд' : $key); 
                            $granularity--; 
                        } if($granularity == 0) break; 
                    } 
                    return ($output ? $output : '0 с').' назад'; 
            } 
                else return date($format, $timestamp); 
    }

    public function TimeFeedSort($FriendFeedarray)
    {
        for ($iii=0; $iii < count($FriendFeedarray); $iii++) 
            { 
                $TimeDatearray[] = $FriendFeedarray[$iii]['date'];
            }
         array_multisort($TimeDatearray,SORT_DESC,$FriendFeedarray);
         return $FriendFeedarray;
    }
     public function Timestamparray($FriendFeedarray)
    {
        for ($iii=0; $iii < count($FriendFeedarray); $iii++) 
            { 
                $TimeDatearray[] = $FriendFeedarray[$iii]['date'];
            }
       //  array_multisort($TimeDatearray,SORT_DESC,$FriendFeedarray);
         return $TimeDatearray;
    }
    // функция нужна для подкачки большего количества элементов ленты, мы будем пересчитывать смещение и строить куски ленты вручную
    public function Feedoffset($current, $now = 4)
    {


    }

    public function FeedArraySlayer($array)
    {
        return array_slice($array, 0, 100);
    }
 

}   



if(!empty($_GET['news']))
{

//echo "ok";
session_start();


  $FF = new FriendFeed();
  $FriendFeedarray = $memcache_obj->get($_SESSION['id'].$_SESSION['id']);
  session_write_close();
  if(!empty($FriendFeedarray))
  {

   // 
   //var_dump($FriendFeedarray);
   // exit;
        
        
//echo count($FriendFeedarray);

 for ($iiii=0; $iiii < count($FriendFeedarray); $iiii++) 
    {       
                
        ?>
        <table class="table table-bordered row-fluid leftprofile1">
        <tr>
            <td >
                
                  <img src=<?php echo $FriendFeedarray[$iiii]['groupphoto'];   ?>>&nbsp; &nbsp;
                
                     <?php echo $FriendFeedarray[$iiii]['groupname'];   ?>
                 <br>
               <?php  
                if(!empty($FriendFeedarray[$iiii]['text']))
               {
                ?>
                    <br>
                    <div class="cutstring" data-display="none" data-max-length="200" data-show-text="Показать полностью.." data-hide-text="Свернуть..">                  
                        <?php echo $FriendFeedarray[$iiii]['text']; ?></div>
                        <br>
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
               
                    <img src=<?php echo $FriendFeedarray[$iiii]['photo'][$ii];   ?>> <br> 
                    <br> 
                <?php
                 }

                ?>
                   
               
                <br>

                <?php } 
                ?>
                <font class="timetextago"><?php echo $FF->timeAgo($FriendFeedarray[$iiii]['date']);   ?></font>
                <br>
               
             
                
                <br><a href="#">Открыть группу вконтакте <span class="glyphicon glyphicon-chevron-right"></span></a>
                  
                 
                  
               </td>
               </tr>  
                 </table> 

     

<?php
//echo $iiii;
}
session_start();

$memcache_obj->set($_SESSION['id'], $FriendFeedarray, false, 86400);
  }
  else
  {
    $FriendFeedarray = $memcache_obj->get($_SESSION['id']);
    session_write_close();
//var_dump($FriendFeedarray);
        for ($iiii=0; $iiii < count($FriendFeedarray); $iiii++) 
                { 
      
                
                            ?>
                        <table class="table table-bordered row-fluid leftprofile1">
                             <tr>
                                <td >
                
                                  <img src=<?php echo $FriendFeedarray[$iiii]['groupphoto'];   ?>>&nbsp; &nbsp;
                
                                      <?php echo $FriendFeedarray[$iiii]['groupname'];   ?>
                                       <br>
                                             <?php  
                                             if(!empty($FriendFeedarray[$iiii]['text']))
                                                 {
                                                  ?>
                                                  <br>
                                          <div class="cutstring" data-display="none" data-max-length="200" data-show-text="Показать полностью.." data-hide-text="Свернуть..">                  
                                           <?php echo $FriendFeedarray[$iiii]['text'];   ?></div>
                                              <br>
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
               
                                                         <img src=<?php echo $FriendFeedarray[$iiii]['photo'][$ii];   ?>> <br> 
                                                             <br> 
                                                                <?php
                                                                 }

                                                                ?>
                   
               
                                                                         <br>

                                                                        <?php } 
                                                ?>
                                             <font class="timetextago"><?php echo $FF->timeAgo($FriendFeedarray[$iiii]['date']);   ?></font>
                                             <br>         
                             
                                        <br><a href="#">Открыть группу вконтакте <span class="glyphicon glyphicon-chevron-right"></span></a>            
                                  
                                             </td>
                                                 </tr>  
                                                      </table>                                                    

                                                <?php
                                                    }


                    }                


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

session_start();


$vk = new VkApi(array(
    'apiKey' => 'E8tyn9sgbwaM2MG9ZCSq',
    'appId' => '4581515',
    'authRedirectUrl' => 'http://192.168.1.141/index.php',
));
  session_write_close();
 // строка для отправки запроса в виде строки с gids групп для groups.get 
$GroupIdsStr = "";
$FriendFeedarray = [];

$CurrentUsrarray = mysql_fetch_assoc(mysql_query("SELECT * from users WHERE id_vk='$_SESSION[id]'"));



if(!empty($_GET['groups']))
{

        $GroupIds[] = $vk->getGroupsforWall($_GET['groups']);
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
               $FriendFeedarray[] = array("groupname" => $Groupname, "groupphoto" => $Groupphoto, "text" => $viewMyFeed['0'][$cc][$jj]['text'], "photo" => $Feedphotoarray, "date" => $viewMyFeed['0'][$cc][$jj]['date'],"gid" => $gidd);
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
               $FriendFeedarray[] = array("groupname" => $Groupname, "groupphoto" => $Groupphoto, "text" => $viewMyFeed['0'][$cc][$jj]['text'], "photo" => $Feedphotoarray, "date" => $viewMyFeed['0'][$cc][$jj]['date'],"gid" => $gidd);
          unset($Feedphotoarray); 
        } 
    

    }

}

// Этот блок нужен для выполнения многих фоновых задач кеширования, обновление счетчика новых постов в ленте, кеширование друзей и кеширвоание ленты, 
//склейка и обрезование кеширвоанной ленты
 
   if(empty($memcache_obj->get($_SESSION['id']."friends")))
    {
        $listFriends[] = $vk->getFriends();
        $memcache_obj->set($_SESSION['id']."friends", $listFriends, false, 1200);
    }
    else
    {
        $listFriends = $memcache_obj->get($_SESSION['id']."friends");
    }

// наш главный класс в котором пока есть методы только для сортировки и работы с датами
$FF = new FriendFeed();
//$oldFeedarray = [];
$FriendFeednewarr = $FF->TimeFeedSort($FriendFeedarray);
//$FriendFeedarray = $FF->TimeFeedSort($FriendFeedarray);
$oldFeedarray = $memcache_obj->get($_SESSION['id']);
//$FriendFeedarray = $FF->Newsdiffarray($FriendFeedarray,$oldFeedarray);

$FriendFeedarray = array_udiff($FriendFeednewarr, $oldFeedarray, "FeedDiffarray");
echo count($FriendFeedarray);

session_start();

$memcache_obj->set($_SESSION['id'].$_SESSION['id'], $FriendFeednewarr, false, 86400);
session_write_close();
//$memcache_obj->set('our_var');

//var_dump($FriendFeedarray);
//$FriendFeedarray[] = $oldFeedarray;
//var_dump($FriendFeedarray);
//$memcache_obj->set('our_var1', $FriendFeednewarr, false, 1200);

?>
 
<?php


exit;

}

if(isset($_GET['id']))
{
    // кешируем список друзей пользователя 
  
    if(empty($memcache_obj->get($_SESSION['id']."friends")))
    {
        $listFriends[] = $vk->getFriends();
        $memcache_obj->set($_SESSION['id']."friends", $listFriends, false, 1200);
    }
    else
    {
        $listFriends = $memcache_obj->get($_SESSION['id']."friends");
    }
    
   

?>

<div class="container">

        <div class="row">

                <!-- Blog Sidebar Widgets Column -->
            <div class="col-md-2">

            

                <!-- Blog Categories Well -->
               
                    <h5>Ленты друзей</h5>
                    <br>
                    <div class="row">
                    <div class="col-md-12">

                     <?php
                         
                     for ($i=0; $i <1 ; $i++) for ($j=0; $j < count($listFriends['0']) ; $j++) 
                        {

                        ?> <div class="friends"><a href=<?php echo "http://192.168.1.141/index.php?id=".$listFriends[$i][$j]['uid']; ?>> <?php echo $listFriends[$i][$j]['first_name']." ".$listFriends[$i][$j]['last_name']; ?></a><br><img src=<?php echo $listFriends[$i][$j]['photo_medium']; ?>><hr></div>
                        <?php
                         } 
                         ?>             
                    </div>
                </div>

            </div>
            <!-- Blog Entries Column -->



<?php
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
               $FriendFeedarray[] = array("groupname" => $Groupname, "groupphoto" => $Groupphoto, "text" => $viewMyFeed['0'][$cc][$jj]['text'], "photo" => $Feedphotoarray, "date" => $viewMyFeed['0'][$cc][$jj]['date'],"gid" => $gidd);
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
               $FriendFeedarray[] = array("groupname" => $Groupname, "groupphoto" => $Groupphoto, "text" => $viewMyFeed['0'][$cc][$jj]['text'], "photo" => $Feedphotoarray, "date" => $viewMyFeed['0'][$cc][$jj]['date'],"gid" => $gidd);
               unset($Feedphotoarray);
        } 
    

    }

}
//var_dump($viewMyFeed);
// наш главный класс в котором пока есть методы только для сортировки и работы с датами
$FF = new FriendFeed();

$FriendFeedarray = $FF->TimeFeedSort($FriendFeedarray);
//var_dump($FriendFeedarray);
//$FriendFeedarray = $FF->FeedArraySlayer($FriendFeedarray);

$urlFeedupdate = "http://192.168.1.141/index.php?news=".$_GET['id'];
$urlFeedCountUpdate = "http://192.168.1.141/index.php?groups=".$_GET['id'];

?>


   <div class="col-md-7">
    <h5>
        Новостная лента 
    </h5><br>

  <center> <button class="btn" onclick="Intercooler.refresh($('#manual-update'));">Показать <font ic-src=<?php echo $urlFeedCountUpdate; ?> ic-poll="15s"></font> новых записей </button></center><br>

      <!--   -->



      <div id="manual-update" ic-src=<?php echo $urlFeedupdate; ?>> 
                 


        <?php
       
            for ($iiii=0; $iiii < count($FriendFeedarray); $iiii++) 
                { 
      
                
        ?>
        <table class="table table-bordered row-fluid leftprofile1">
        <tr>
            <td >
                
                  <img src=<?php echo $FriendFeedarray[$iiii]['groupphoto'];   ?>>&nbsp; &nbsp;
                
                     <?php echo $FriendFeedarray[$iiii]['groupname'];   ?>
                 <br>
               <?php  
                if(!empty($FriendFeedarray[$iiii]['text']))
               {
                ?>
                    <br>
                    <div class="cutstring" data-display="none" data-max-length="200" data-show-text="Показать полностью.." data-hide-text="Свернуть..">                  
                        <?php echo $FriendFeedarray[$iiii]['text'];   ?></div>
                        <br>
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
               
                    <img src=<?php echo $FriendFeedarray[$iiii]['photo'][$ii];   ?>> <br> 
                    <br> 
                <?php
                 }

                ?>
                   
               
                <br>

                <?php } 
                ?>
                <font class="timetextago"><?php echo $FF->timeAgo($FriendFeedarray[$iiii]['date']);   ?></font>
                <br>
               
             
                
                <br><a href="#">Открыть группу вконтакте <span class="glyphicon glyphicon-chevron-right"></span></a>
                  
                 
                  
               </td>
               </tr>  
  </table> 

     

<?php
}
// генерация кеш  по id

 $memcache_obj->set($_SESSION['id'], $FriendFeedarray, false, 86400);

 //$_SESSION['id'] = $owner_id;
   //             $_SESSION['fullname'] = $fullName;
     //           $_SESSION['img'] = $profileimg;
?>
 
</div>


</div>

 <div class="col-md-3">
   
    <div class="row">

        <div class="col-md-12 leftprofile disabled"> 
       <?php 
      $friendid = [];
      $frlist[] = $memcache_obj->get($_SESSION['id']."friends");
      //var_dump($frlist);
    //  echo $_GET['id']."\n";
    //  echo $_SESSION['id'];
     // echo $_SESSION['fullname'];
       if($_GET['id'] == $_SESSION['id'])
            {
                $friendid['0']['last_name'] = "";
                $friendid['0']['first_name'] = $_SESSION['fullname'];
                $friendid['0']['photo_medium'] = $_SESSION['img'];
               // var_dump($friendid);
               // break;
            }
            else
            {

                 for ($fr=0; $fr < count($frlist['0']['0']); $fr++) 
                    { 
                        if($_GET['id'] == $frlist['0']['0'][$fr]['uid'])
                        {
                         $friendid[] = $frlist['0']['0'][$fr];
                            break;
                        }

                     }
            }

       ?>

      &nbsp;&nbsp;<h5>Активная лента</h5><hr>




       
       <div class="row">     
              <div class="media">
                <a class="pull-left" href="#">
                  
                <img class="media-object" src=<?php echo $friendid['0']['photo_medium']; ?> width="80px" heigth="60px">
                </a>
                <div class="media-body">
                  <h6><strong><mark>&nbsp;<?php echo $friendid['0']['first_name']." ".$friendid['0']['last_name']; ?>&nbsp;</mark></strong></h6>
                   
                </div>

            </div>

        </div>
     <br><br>
    
    
          
</div>

    </div>






 </div>



 <hr>

        <!-- Footer -->
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
else
{
    $listFriends[] = $vk->getFriends();
// конец вывода времени работы скрипта для первой страницы

?>
<div class="container">

        <div class="row">

                <!-- Blog Sidebar Widgets Column -->
            <div class="col-md-4">

                    <h4>Друзьяшки</h4>
                    
                     <?php
                          $end_time = microtime();
                            $end_array = explode(" ",$end_time);
                            $end_time = $end_array[1] + $end_array[0];
                            $time = $end_time - $start_time;
                            printf("Страница сгенерирована за %f секунд",$time)."<br>";
                     for ($i=0; $i <1 ; $i++) for ($j=0; $j < count($listFriends['0']) ; $j++) echo "<pre><img src='".$listFriends[$i][$j]['photo_medium']."'><br><a href='http://192.168.1.141/index.php?id=".$listFriends[$i][$j]['uid']."'>".$listFriends[$i][$j]['first_name']." ".$listFriends[$i][$j]['last_name']."</a></pre>";

                     ?>
                      
                            </div>
            <!-- Blog Entries Column -->

 <div class="col-md-8">
    <h4>
        Новостная лента пользователя <?php  echo $CurrentUsrarray['name'];  ?>
    </h4>
<br>
        <?php

            $GroupIds[] = $vk->getGroupsforWall($CurrentUsrarray['id_vk']);
            //var_dump($GroupIds);
            //exit;
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

    $codeStr = 'var a=API.groups.get({"user_id":"'.$CurrentUsrarray['id_vk'].'"}); var b=a; var d='.$CounterWallget.'; var v='.$CounterWallget24.';
        var c = [];
        while (d < v)
        {
         c.push(API.wall.get({"owner_id":-b[d],"count":"3"}));
         d = d+1; 
        };
        return c;';       

    $viewMyFeed[] = $vk->getExecuteFeedFriends($codeStr);

    
//var_dump($Groupinfo['0']);
    for($cc=1; $cc<count($viewMyFeed['0']); $cc++)
    {
        
        for($jj = 1; $jj<4; $jj++)
        {    
           
               for($vv=0; $vv<count($Groupinfo['0']);$vv++)
               {

                    if("-".$Groupinfo['0'][$vv]['gid'] == $viewMyFeed['0'][$cc][$jj]['from_id']) 
                    {
                     
                        $Groupphoto = $Groupinfo['0'][$vv]['photo'];
                        $Groupname = $Groupinfo['0'][$vv]['name'];                                           
                        break;
                    }
               }
             
               $FriendFeedarray[] = array("groupname" => $Groupname, "groupphoto" => $Groupphoto, "text" => $viewMyFeed['0'][$cc][$jj]['text'], "photo" => $viewMyFeed['0'][$cc][$jj]['attachments']['0']['photo']['src_big'], "date" => $viewMyFeed['0'][$cc][$jj]['date']);
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

    $codeStr = 'var a=API.groups.get({"user_id":"'.$CurrentUsrarray['id_vk'].'"}); var b=a; var d='.$CounterMod.'; var v='.$CounterModGroups.';
        var c = [];
        while (d < v)
        {
         c.push(API.wall.get({"owner_id":-b[d],"count":"3"}));
         d = d+1; 
        };
        return c;';       

    $viewMyFeed[] = $vk->getExecuteFeedFriends($codeStr);

    for($cc=1; $cc<count($viewMyFeed['0']); $cc++)
    {
        
        for($jj = 1; $jj<4; $jj++)
        {    
               for($vv=0; $vv<count($Groupinfo['0']);$vv++)
               {

                    if("-".$Groupinfo['0'][$vv]['gid'] == $viewMyFeed['0'][$cc][$jj]['from_id']) 
                    {                    
                        $Groupphoto = $Groupinfo['0'][$vv]['photo'];
                        $Groupname = $Groupinfo['0'][$vv]['name']; 
                        break;
                    }
               }
           
               $FriendFeedarray[] = array("groupname" => $Groupname, "groupphoto" => $Groupphoto, "text" => $viewMyFeed['0'][$cc][$jj]['text'], "photo" => $viewMyFeed['0'][$cc][$jj]['attachments']['0']['photo']['src_big'], "date" => $viewMyFeed['0'][$cc][$jj]['date']);
        } 
    

    }

}
// наш главный класс в котором пока есть методы только для сортировки и работы с датами
$FF = new FriendFeed();
$FriendFeedarray = $FF->TimeFeedSort($FriendFeedarray);
$FriendFeedarray = $FF->FeedArraySlayer($FriendFeedarray);

            for ($iiii=0; $iiii < count($FriendFeedarray); $iiii++) 
                { 
      
                
        ?>
                <div class="container">

                     <div class="row">
                <div class="col-md-1">               
                
                  <img src=<?php echo $FriendFeedarray[$iiii]['groupphoto'];   ?>>
                
                 </div>
                 <div class="col-md-11">
                     <?php echo $FriendFeedarray[$iiii]['groupname'];   ?>
                 <br>
                 

                
                <p>
                        <?php echo $FriendFeedarray[$iiii]['text'];   ?>
                </p>
                <p>
                    <img src=<?php echo $FriendFeedarray[$iiii]['photo'];   ?>>
                 </p>   
                <p><span class="glyphicon glyphicon-time"></span>  <?php echo $FriendFeedarray[$iiii]['date'];   ?></p>
             
               
             
                
                <a class="btn btn-primary" href="#">Read More <span class="glyphicon glyphicon-chevron-right"></span></a>
                
                </div>
                 </div> 
                  </div> 
                <hr>       

<?php
}



?>


<?php





// Для модуля обработки статистики решил оставить этот код и только, когда приложение не работает с лентой, хотя если на главной будет выводиться моя лена, то уберу
// удаление из базы в случае если ты выписался из сообществ, пока не реализовано
    $viewMyGroups[] = $vk->getGroups($_SESSION['id']);  //список групп
for ($i=1; $i <count($viewMyGroups['0']) ; $i++) 
    { 
        $myid = $_SESSION['id'];
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
}

session_write_close();

?>
  <!-- /.container -->
   <!-- jQuery Version 1.11.0 -->
    <script src="js/jquery-1.11.0.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

    <script src="js/agency.js"></script>
      <script src="js/cutstring.js"></script>
      <script src="https://s3.amazonaws.com/intercoolerjs.org/release/intercooler-0.0.1.min.js"></script>
       
    <script>
$(function() {
    $('.cutstring').cutstring();
});
</script>

</body>

</html>