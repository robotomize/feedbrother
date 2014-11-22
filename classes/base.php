<?php
session_start();

// класс для инициализации переменных для Url и кеша, постпенно на него перевожу
class InitUriFromRouter
{
    public $sessionid = '';
    public $newiduser = '';

     function __construct($ses) 
     {
        $this->sessionid = $ses;        
     }
    public function mainf($in)
    {
       return explode('/', $in);
    }
}

class Urlstorage
{
    public $urlFeedCountUpdate = '';
    const urlFeedupdateold = "http://192.168.1.141/old";
    const urlFeedupdateoldcache = "http://192.168.1.141/oldcache";
    const urlMyProfile = "http://192.168.1.141/profile";
    const urlFeedupdate = "http://192.168.1.141/news";
    const urlhome = "http://192.168.1.141/";
     
     function __construct($urlfeedupd) 
     {
        $this->urlFeedCountUpdate = "http://192.168.1.141/groups/".$urlfeedupd;        
     } 

}

function link_it($s)
{
       return preg_replace('@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@', '<a href="$1" target="_blank">$1</a>', $s); 
}

function FeedDiffarray($a, $b) {
    if ($a === $b) { return 0; }
    return ($a > $b)? 1:-1;
}

function FeedArraySlayer($array,$num)
    {
        return array_slice($array, 0, 100);
    }

function checkdatearr($datear)
{
    $diff = time() - $datear;
    if($diff > 832000)
    {
        return 0;
    }
    else
    {
        return 1;
    }
}


// Код роутера
class uSitemap 
{
    public $title = '';
    public $params = null;
    public $classname = '';
    public $data = null;
 
    public $request_uri = '';
    public $url_info = array();
 
    public $found = false;
 
    function __construct() {
        $this->mapClassName();
    }
 
    function mapClassName() {
 
        $this->classname = '';
        $this->title = '';
        $this->params = null;
 
        $map = &$GLOBALS['sitemap'];
        $this->request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);       
        $this->url_info = parse_url($this->request_uri);
        $uri = urldecode($this->url_info['path']);
        $data = false;
        foreach ($map as $term => $dd) {
            $match = array();
            $i = preg_match('@^'.$term.'$@Uu', $uri, $match);
            if ($i > 0) {
                // Get class name and main title part
                $m = explode(',', $dd);
                $data = array(
                    'classname' => isset($m[0])?strtolower(trim($m[0])):'',
                    'title' => isset($m[1])?trim($m[1]):'',
                    'params' => $match,
                );
                break;
            }
        }
        if ($data === false) {
            // 404
            if (isset($map['_404'])) {
                // Default 404 page
                $dd = $map['_404'];
                $m = explode(',', $dd);
                $this->classname = strtolower(trim($m[0]));
                $this->title = trim($m[1]);
                $this->params = array();
            }
            $this->found = false;
        } else {
            // Found!
            $this->classname = $data['classname'];
            $this->title = $data['title'];
            $this->params = $data['params'];
            $this->found = true;
        }
        return $this->classname;
    }
}



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
            if (isset($responce->access_token)) 
            {
                $_SESSION['tok'] = $responce->access_token;          
                 $token = $responce->access_token;
                 $owner_id = $responce->user_id;                 
                 $GetProfile = file_get_contents("https://api.vk.com/method/users.get?fields=nickname,screen_name,photo_medium,photo_big,city,bdate,sex&uid=$owner_id&access_token=$token");
                 $profile = json_decode($GetProfile , true);
                 $fullName = $profile['response']['0']['first_name']." ".$profile['response']['0']['last_name'];
                 $profileimg = $profile['response']['0']['photo_medium'];
                 $profilescreenname = $profile['response']['0']['nickname'];
                 $provider = "vk";
                 $email = $responce->email;
                 $_SESSION['id'] = $owner_id;
                $_SESSION['fullname'] = $fullName;
                $_SESSION['img'] = $profileimg;                 
                $id = null;

                $newuser = new UserModel($id,$owner_id, $profileimg,$fullName, $profilescreenname, $provider, $email);             
                $data = array($newuser->id,$newuser->id_vk,$newuser->img_src,$newuser->name, $newuser->screenname, $newuser->provider, $newuser->email);    
               try 
                {  
                    $STH = DBmodel::getInstance()->prepare("INSERT INTO usersVk (id,id_vk, img_src, name, screenname, provider, email) values (?,?,?,?,?,?,?)");
                    $STH->execute($data);  
                }  
                catch(PDOException $e) 
                {                      
                    file_put_contents('/var/www/FeedBrother/PDOErrors.txt', "ошибка создания пользователя", FILE_APPEND);  
                }                          

            } else  throw new Exception('VK API error.');             
            
        }
       
        if (empty($token)) 
        {

            $url = "https://oauth.vk.com/authorize?client_id="
                   . $this->appId . "&redirect_uri=http://192.168.1.141/me&display=page&response_type=code&scope=video,offline,groups,friends,photos,notify,email";
 
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

    public function getGroupsFordifference($owner_id)
    {
        $request = $this->get('groups.get', array(
            'user_id' => $owner_id,            
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

    /*
        Главный класс, который формирует нашу сказочную ленту из стены
    */
    public $offset = "0";

    public function mainf($idusr,$vk)
    {

    $GroupIds[] = $vk->getGroupsforWall($idusr);
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

            $codeStr = 'var a=API.groups.get({"user_id":"'.$idusr.'"}); var b=a; var d='.$CounterWallget.'; var v='.$CounterWallget24.';
                var c = [];
                while (d < v)
                {
                 c.push(API.wall.get({"owner_id":-b[d],"count":"3","offset":"'.$this->offset.'"}));
                 d = d+1; 
                };
                return c;';   
            $viewMyFeed[] = $vk->getExecuteFeedFriends($codeStr);
            for($cc=0; $cc<count($viewMyFeed['0']); $cc++)
            {
                
                for($jj = 1; $jj<4; $jj++)
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
                         if(checkdatearr($viewMyFeed['0'][$cc][$jj]['date']) == 1) $FriendFeedarray[] = array("groupname" => $Groupname, "groupphoto" => $Groupphoto, "text" => $viewMyFeed['0'][$cc][$jj]['text'], "photo" => $Feedphotoarray, "date" => $viewMyFeed['0'][$cc][$jj]['date'],"gid" => $gidd, "screen" => $gidscreen);    
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
            $codeStr = 'var a=API.groups.get({"user_id":"'.$idusr.'"}); var b=a; var d='.$CounterMod.'; var v='.$CounterModGroups.';
                var c = [];
                while (d < v)
                {
                 c.push(API.wall.get({"owner_id":-b[d],"count":"3","offset":"'.$this->offset.'"}));
                 d = d+1; 
                };
                return c;';       

                $viewMyFeed[] = $vk->getExecuteFeedFriends($codeStr);
                for($cc=0; $cc<count($viewMyFeed['0']); $cc++)
                {        
                    for($jj = 1; $jj<4; $jj++)
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
                             if(checkdatearr($viewMyFeed['0'][$cc][$jj]['date']) == 1) $FriendFeedarray[] = array("groupname" => $Groupname, "groupphoto" => $Groupphoto, "text" => $viewMyFeed['0'][$cc][$jj]['text'], "photo" => $Feedphotoarray, "date" => $viewMyFeed['0'][$cc][$jj]['date'],"gid" => $gidd, "screen" => $gidscreen);          
                           unset($Feedphotoarray);
                    }   

                }
            }

    return $FriendFeedarray;

    }    


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
     
         return $TimeDatearray;
    }
 
 public function SortAndUdiffarray($FriendFeedarray,$sessionid,$memcache_obj)
 {    
    function FeedDiffarray($a, $b) {
    if ($a === $b) { return 0; }
    return ($a > $b)? 1:-1;
        }
    $aarray = $this->TimeFeedSort($FriendFeedarray);
    $barray = $memcache_obj->get($sessionid);
    $FriendFeedarray = array_udiff($aarray, $barray, "FeedDiffarray");
    return count($FriendFeedarray);
 }

}

 class Friends
 {
    // првоерка на активность ленты, если id совпадает с сессией, то наполняем массив для отображения блока активная лента
    public function CheckActiveFeedFriend($in,$out,$frlist)
    {        
       if($in == $out)
            {
                $friendid['0']['last_name'] = "";
                $friendid['0']['first_name'] = $_SESSION['fullname'];
                $friendid['0']['photo_medium'] = $_SESSION['img'];             
            }
            else
            {

                 for ($fr=0; $fr < count($frlist['0']['0']); $fr++) 
                    { 
                        if($in == $frlist['0']['0'][$fr]['uid'])
                        {
                          $friendid[] = $frlist['0']['0'][$fr];
                          break;
                        }

                     }
            }
            return $friendid;
    }
     /*
    Блок проверяем есть ли наш список друзей в кеше и если нет, то делаем api запрос и кладем в кеш
    */
    public function CheckFriendlistFromCache($memcache_obj,$ses,$vk)
    {       
        if(empty($memcache_obj->get($ses."friends")))
        {
            $listFriends[] = $vk->getFriends();       
            $memcache_obj->set($ses."friends", $listFriends, false, 1200);
        }
        else  $listFriends = $memcache_obj->get($ses."friends");  
    return $listFriends;  
    }
 }
 class Caching extends FriendFeed
 {

     public function createusergroupsidscaching($GroupIds,$memcache_obj,$curid,$sessionid)
        {
        if(!empty($GroupIds)) 
        {
            if($curid != $sessionid)
            {
            $memcache_obj->set($sessionid."usrgroups", $GroupIds, false, 86400);  
            
            }
            else 
            {
              if(!empty($memcache_obj->get($sessionid."usrgroups"))) return $memcache_obj->get($sessionid."usrgroups");
              else return 0;
            }
    
        }
        else 
            {
                if(!empty($memcache_obj->get($sessionid."usrgroups"))) return $memcache_obj->get($sessionid."usrgroups");
                else return 0;
            }

        }


    public function createmegroupsidscaching($GroupIds,$memcache_obj,$curid,$sessionid)
    {
        if(!empty($GroupIds)) 
        {
            if($curid == $sessionid)
            {
            $memcache_obj->set($sessionid."groups", $GroupIds, false, 86400); 
            
            }
            else 
            {
              if(!empty($memcache_obj->get($sessionid."groups"))) return $memcache_obj->get($sessionid."groups");
              else 
              {
               $memcache_obj->set($sessionid."groups", $GroupIds, false, 86400); 
               return $GroupIds; 
              }
            }
    
        }
        else 
            {
                if(!empty($memcache_obj->get($sessionid."groups"))) return $memcache_obj->get($sessionid."groups");
                else return 0;
            }

    }
        public function checkNewGroup($gid,$cachearray)
        {
            $test = 1;
            for ($i=0; $i < count($cachearray['0']); $i++) 
            { 
              if($gid == $cachearray['0'][$i])
              {
                $test = 0;
                break;
              }
            }
            if($test == 0) return 0;
            else return 1;
        }

    public function isMecacheMyArray($in, $out,$memcache_obj,$feedarray)
    {     
        if($in == $out) $memcache_obj->set($in."me", $feedarray, false, 86400);
    }

    public function isFriendsOnCache($sessionid,$memcache_obj)
    {
               if(empty($memcache_obj->get($sessionid."friends")))
                 {   
                     $listFriends[] = $vk->getFriends();
                     $memcache_obj->set($sessionid."friends", $listFriends, false, 1200);
                }
                 else $listFriends = $memcache_obj->get($sessionid."friends"); 
               return $listFriends;
    }


 }
 /*
    В классе статистики находятся методы по работе с базой данных в основном. Методы отображающие статистические данные о пользователях или событиях, их действиях
 */
 class Statistic extends FriendFeed
 { 

    public function iswatching($in,$out)
    {
        if($in == $out) return "1";
        else return "0";
    }

    protected function determineiduser($user,$DBH)
    {
     try
     { 
        $result = DBmodel::getInstance()->prepare("SELECT id from usersVk where id_vk=?");
        $result->setFetchMode(PDO::FETCH_ASSOC); 
        $result->execute(array($user)); 
        while($row = $result->fetch()) 
        {  
            return $row['id'];       
        }
    }
    catch(PDOException $e){  file_put_contents('/var/www/FeedBrother/PDOErrors.txt', "ошибка получения id пользователя из базы", FILE_APPEND);  } 
     
    }
    protected function saveFeedWatching($iduser,$idvkuser,$idvkwatchuser)
    {
         $data = array(null,$iduser,$idvkuser,'0',$idvkwatchuser);    
               try 
                {  
                    $STH = DBmodel::getInstance()->prepare("INSERT INTO FeedWatching (id, id_user, id_vkuser, `like`, id_vkuserwatch) values (?,?,?,?,?)");
                    $STH->execute($data);  
                }  
                catch(PDOException $e) 
                {                      
                    file_put_contents('/var/www/FeedBrother/PDOErrors.txt', "Ошибка в методе для счетчика просмотров".$e->getMessage(), FILE_APPEND);  
                }       
    }
  
    protected function saveFeedmyfollowers($idvkuser,$idvkfollower)
    {
        $data = array(null,$idvkuser,$idvkfollower,'0');    
               try 
                {  
                    $STH = DBmodel::getInstance()->prepare("INSERT INTO FeedFollowers (id, id_vkuser, id_vkuserfollow, `like`) values (?,?,?,?)");
                    $STH->execute($data);  
                }  
                catch(PDOException $e) 
                {                      
                    file_put_contents('/var/www/FeedBrother/PDOErrors.txt', "Ошибка в методе для обновления счетчика просмотренных".$e->getMessage(), FILE_APPEND);  
                }
       
    }

    public function addFeedFollowersrecord($iduser,$iduserfollower)
    {
        if($this->iswatching($iduser,$iduserfollower) == 0)
        {           
                $this->saveFeedmyfollowers($iduser,$iduserfollower);
                return 1;           
        }
        else return 0;
    }

    public function addFeedWatchingrecord($iduser,$iduserwatch)
    {       

        if($this->iswatching($iduser,$iduserwatch) == 0)
        {            
                $myid = $this->determineiduser($iduser);                
                $this->saveFeedWatching($myid['id'],$iduser,$iduserwatch);
                return 1;            
        }
        else return 0;
    }


    public function seemyfeedwatching($user)
    {
        
        try
        { 
            $result = DBmodel::getInstance()->prepare("SELECT count(id) as id from FeedWatching where id_vkuser=?");
            $result->setFetchMode(PDO::FETCH_ASSOC); 
            $result->execute(array($user)); 
            return $result->fetchColumn();           
        }
        catch(PDOException $e){  file_put_contents('/var/www/FeedBrother/PDOErrors.txt', "ошибка получения id пользователя из базы", FILE_APPEND);  } 
   
    }
      public function seemyfeedfollowers($user)
    {
        try
        { 
            $result = DBmodel::getInstance()->prepare("SELECT count(id) as id from FeedFollowers where id_vkuser=?");
            $result->setFetchMode(PDO::FETCH_ASSOC); 
            $result->execute(array($user)); 
            return $result->fetchColumn();           
        }
        catch(PDOException $e){  file_put_contents('/var/www/FeedBrother/PDOErrors.txt', "ошибка получения id пользователя из базы", FILE_APPEND);  } 

    }

    public function viewnewgroup($user)
    {
         try
        { 
            $result = DBmodel::getInstance()->prepare("SELECT count(id) as id from GroupsResearched where id_vkuser=?");
            $result->setFetchMode(PDO::FETCH_ASSOC); 
            $result->execute(array($user)); 
            return $result->fetchColumn();           
        }
        catch(PDOException $e){  file_put_contents('/var/www/FeedBrother/PDOErrors.txt', "ошибка получения id пользователя из базы".$e->getMessage(), FILE_APPEND);  }      
    }

  
    protected function isNewResearchGroup($groupsids,$user)
    {
        $buf = 0;
        $outputids = [];
        $result = DBmodel::getInstance()->prepare("SELECT count(id) as id from GroupsResearched where id_vkuser=:id AND id_vkgroup=:group");
        $result->setFetchMode(PDO::FETCH_ASSOC);
        for ($i=0; $i < count($groupsids['0']); $i++) 
            {
                $buf = $groupsids['0'][$i];
                 try
                    {                         
                        $result->bindValue(':id', $user, PDO::PARAM_INT);
                        $result->bindValue(':group', $buf, PDO::PARAM_STR);
                        $result->execute();                                           
                        $count = $result->fetchColumn();
                        if($count == 0) $outputids[] = $buf;           
                    }
                    catch(PDOException $e){  file_put_contents('/var/www/FeedBrother/PDOErrors.txt', "ошибка кол.ва записей из GroupsResearched".$e->getMessage(), FILE_APPEND);  }               
            } 
       return $outputids;
    }
    protected function saveNewGroup($newgroupids,$myvkid)
    {
        $buf = 0;
        $myrealid = $this->determineiduser($myvkid);
        $STH = DBmodel::getInstance()->prepare("INSERT INTO GroupsResearched (id, id_user, id_vkgroup, id_vkuser) values (?,?,?,?)");
        for ($i=0; $i < count($newgroupids); $i++) 
            {
               $buf = $newgroupids[$i];
               $data = array(null,$myrealid,$buf,$myvkid);    
               try { $STH->execute($data); }  
                catch(PDOException $e) { file_put_contents('/var/www/FeedBrother/PDOErrors.txt', "Ошибка в методе для добавления новых записей в GroupsResearched".$e->getMessage(), FILE_APPEND); }
             }   
    }
    public function ResearchNewGroups($newgroupids,$myvkid)
    {
        $groupidsforsave = $this->isNewResearchGroup($newgroupids,$myvkid);
        if(!empty($groupidsforsave)) 
        {
                $this->saveNewGroup($groupidsforsave,$myvkid); 
                return 1;
        }     
        else return 0;
    }
         
 }

class GetNewGroupInfo extends Statistic
{

   static function FeedDiffarray($a, $b) 
   {
    if ($a === $b) { return 0; }
    return ($a > $b)? 1:-1;
    }

   static private function FeedArraySlayer($array,$num)
    {
        return array_slice($array, 0, $num);
    } 
    public function InitGroupDifference($me,$user,$vk,$memcache_obj)
    {    
            $Groupsme = $vk->getGroupsforWall($me);                  
            $Groupsuser = $vk->getGroupsforWall($user);
            array_shift($Groupsme); 
            array_shift($Groupsuser);

            $resultarray = array_udiff($Groupsuser,$Groupsme, "FeedDiffarray");            
            sort($resultarray);

            $newgroupsfromcache = $memcache_obj->get($me."newgroupdiffids");

            if(!empty($newgroupsfromcache)) 
                {
                    $resultarrayforcache = array_merge($resultarray,$newgroupsfromcache);                    
                    sort($resultarrayforcache);
                    $resultarrayforcache = array_unique($resultarrayforcache);
                    sort($resultarrayforcache);
                    $memcache_obj->set($me."newgroupdiffids",$resultarrayforcache, false, 286400);
                } 
                else $memcache_obj->set($me."newgroupdiffids",$resultarray, false, 286400);       
                
           return $resultarray;           
        }

    public function GetGroupInfoFromIds($memcache_obj,$vk,$me)
    {   $GroupIdsStr = "";
        $GroupIds = $memcache_obj->get($me."newgroupdiffids");
           for($mm=0;$mm<count($GroupIds);$mm++)
            {
                if($GroupIdsStr == "") $GroupIdsStr = $GroupIds[$mm];
                else $GroupIdsStr = $GroupIdsStr.",".$GroupIds[$mm];
            }
       return $vk->getGroupsById($GroupIdsStr); 
    }
    public function ListNewGroupsInfo($memcache_obj,$me)
    {
        $Groupinfo = $memcache_obj->get($me."newgroupdiff");
        shuffle($Groupinfo);
        return self::FeedArraySlayer($Groupinfo,3);
    }

}


/*
    Обертка для БД к PDO
*/
class DBmodel 
{
    private static $instance = NULL;
    private static $host = 'localhost';
    private static $dbname = 'frfeed';
    private static $pass = '13';
    private static $user = 'root';

    private function __construct()
    {

    }
    private function __clone()
    {

    }
    public static function getInstance()
    {
        if(!self::$instance)
        {
            self::$instance = new PDO('mysql:host='.self::$host.';dbname='.self::$dbname, self::$user, self::$pass,array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''));
            self::$instance-> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        }
        return self::$instance;
    }
}

/*
    пока главный класс работы с пользователями
*/
class UserModel extends DBmodel
{
    public $id;
    public $id_vk = '';
    public $img_src = '';
    public $name = '';
    public $screenname = '';
    public $provider = 'vk';
    public $email = '';

      function __construct($id,$id_vk,$img_src,$name,$screenname,$provider,$email) 
        {  
            $this->id = $id;  
            $this->id_vk = $id_vk;  
            $this->img_src = $img_src;
            $this->name = $name;
            $this->screenname = $screenname;
            $this->provider = $provider;
            $this->email = $email;
        }


}

class UserModelFB extends DBmodel
{

}

class UserModelIG extends DBmodel
{


}

class StatisticModel extends DBmodel
{

}
    
session_write_close();
