<?php
 $start_time = microtime();
    $start_array = explode(" ",$start_time);
    $start_time = $start_array[1] + $start_array[0];
set_time_limit(3600);
 
$host = "localhost";   
$user = "root";   
$pass = "13";    

 if(!mysql_connect($host, $user, $pass)) exit(mysql_error()); 
 

mysql_query("SET character_set_client='UTF8'"); 
mysql_query("SET character_set_results='UTF8'"); 
mysql_query("SET collation_connection='UTF8'");
mysql_query("SET NAMES UTF8");
mysql_select_db("frfeed") or die(mysql_error()); 


// Предполагается, что сессия уже запущена, если нет - уберите комментарий
 session_start();
 
/**
 * @class VkApi
 * @author Maslakov Alexander <jmas.ukraine@gmail.com>
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
                //var_dump($responce);exit;
            
        }





      //  else header('Location: ' . "http://192.168.1.141/");
       
        if (empty($token)) {

            $url = "https://oauth.vk.com/authorize?client_id="
                   . $this->appId . "&redirect_uri=http://192.168.1.141/index.php&display=page&response_type=code&scope=video,offline,groups,friends,photos,notify";
 
            header('Location: ' . $url);
            // ищем юзера с нашим id


         //   exit;
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


      public function getFeed($owner_id)
    {
        $request = $this->get('newsfeed.get', array(
            'owner_id' => $owner_id,
            
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
   
   
 
// Пример использования
$vk = new VkApi(array(
    'apiKey' => '',
    'appId' => '',
    //'login' => '<LOGIN>',
   // 'password' => '<PASSWORD>',
    'authRedirectUrl' => 'http://192.168.1.141/index.php',
));
  
  //var_dump($_GET);
//$myy[] = $vk->getFriends();

//var_dump($myy);

//echo $_SESSION['id'];
$viewMyGroups[] = $vk->getGroups($_SESSION['id']);  //список групп
//var_dump($viewMyGroups);
//echo $_SESSION['id'];
//echo count($viewMyGroups['0']);


// удаление из базы в случае если ты выписался из сообществ, пока не реализовано
for ($i=1; $i <count($viewMyGroups['0']) ; $i++) 
    { 
        $myid = $_SESSION['id'];
        $gidGroup = $viewMyGroups['0'][$i]['gid'];
       if(empty(mysql_fetch_assoc(mysql_query("SELECT id FROM Cachegroups WHERE id_user='$myid' and id_group='$gidGroup'"))))
        {
       // echo $viewMyGroups['0'][$i]['name']."\n";
      //echo $i;
      //break;
        //echo $i;
        //echo $viewMyGroups['0']['1']['gid'];
        $gidGroup = $viewMyGroups['0'][$i]['gid'];
        //echo $gidGroup;
        $nameGroup = strip_tags(str_replace("'","",$viewMyGroups['0'][$i]['name']));
        //echo $nameGroup;
        $descriptionGroup = strip_tags(str_replace("'","",$viewMyGroups['0'][$i]['description']));
       // echo $descriptionGroup;
        $screen_nameGroup = $viewMyGroups['0'][$i]['screen_name'];
        $activityGroup = $viewMyGroups['0'][$i]['is_closed'];
        $members_countGroup = $viewMyGroups['0'][$i]['members_count'];
// ошибка, добавляются группы повторно или по несколько штук, проблема не решена
//break;
        mysql_query("INSERT INTO Cachegroups VALUES (null, '$_SESSION[id]', '$nameGroup', '$descriptionGroup', '$screen_nameGroup', '$activityGroup', '$members_countGroup','$gidGroup')") or die(mysql_error());
        //break;
        }

    }
//echo $_SESSION['tok']."\n";
//echo $_SESSION['id'];
//echo $_SESSION['fullname'];
//$listwall[] = $vk->getWallGroups("24532152");
//var_dump($listwall);

//echo $_SESSION['id'];
//var_dump($viewMyGroups);
 //$query = mysql_query("SELECT * FROM Cachegroups WHERE id_user='$_SESSION[id]'");

 //mysql_query("INSERT INTO users VALUES (null, '$owner_id', '$profileimg', '$fullName', '$profilescreenname')") or die(mysql_error());
       

 // $viewUsr[] = $vk->getUsers($_GET['owner_id']);
//var_dump($viewUsr);
//var_dump($_GET['gor']);
    //$token = $_SESSION['tok'];
    //echo $_SESSION['tok'];
//$GetProfile = file_get_contents("https://api.vk.com/method/execute.feeder?access_token=$token");
  //               $profile = json_decode($GetProfile , true);
//var_dump($profile);



    $i=0;
if(isset($_GET['id']))
{

     $codeStr = 'var a=API.groups.get({"user_id":"'.$_GET['id'].'"}); var b=a; var d=0;
var c = [];
//return b.length;
while (d < 24)
{
//return b[d];
 c.push(API.wall.get({"owner_id":-b[d],"count":"5"}));
 d = d+1; 
//return d;
};
return c;';
$viewMyFeed[] = $vk->getExecuteFeedFriends($codeStr);
    /*
   // echo $_GET['id'];
    $viewUsr[] = $vk->getUsers($_GET['id']);   // информация о пользователе
    $viewUsrGroups[] = $vk->getGroups($_GET['id']);  //список групп
   // $viewUsrWall[] = $vk->getWallGroups("9161696");  // 100 записей заданной группы
   // var_dump($viewUsrGroups);
    //echo $viewUsrGroups['0']['1']['gid'];
   
        for ($ii=1; $ii < 20; $ii++)
        {
        //формируем ленту итогового массива
           
           $viewUsrWallcache[] = $vk->getWallGroups($viewUsrGroups['0'][$ii]['gid']);
         
   // exit;

            //echo $ii;
          //  echo $viewUsrGroups['0'][$ii]['gid']."\n";
        //  echo count($viewUsrGroups['0'])-1;
           
         /*   for($j=0; $j<10;$j++)
            {
                $FeedStripArray[$i] = array($viewUsrWallcache[]); 0000
            }

             = $viewUsrGroups['0'][$i]['name'];
        */    
var_dump($viewMyFeed);     
      
        
         //echo $viewUsrWallcache['0']['1']['text'];
        
   // var_dump($viewUsrWallcache); 
}
else
{
    $listFriends[] = $vk->getFriends();
for ($i=0; $i <1 ; $i++) for ($j=0; $j < count($listFriends['0']) ; $j++) echo "<pre><img src='".$listFriends[$i][$j]['photo_medium']."'><br><a href='http://192.168.1.141/vk.php?id=".$listFriends[$i][$j]['uid']."'>".$listFriends[$i][$j]['first_name']." ".$listFriends[$i][$j]['last_name']." ".$listFriends[$i][$j]['uid']."</a></pre>";


}
    

$end_time = microtime();
    $end_array = explode(" ",$end_time);
    $end_time = $end_array[1] + $end_array[0];
    $time = $end_time - $start_time;

    printf("Страница сгенерирована за %f секунд",$time)."<br>";


/*
echo '<pre>';
 echo $_GET['uid'];
 echo '</pre>';
//echo "hui";
*/


 //echo '<pre>';
 //var_dump($arr);
//echo '</pre>';
//echo count($arr['0']);


//echo '<pre>';
//var_dump($vk->getFriends($_GET['owner_id']));
//echo '</pre>';

//$fige = $vk->getFriends($_GET['owner_id'];
//var_dump($fige);
//foreach ($arr as $keys) 
//{
 //  echo $keys['nickname']."\n"; 
//}


/*echo '<pre>';
var_dump($vk->getGroups("253214704"));
echo '</pre>';*/

?>
