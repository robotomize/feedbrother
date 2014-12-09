<?php
    //Создаём новый объект. Также можно писать и в процедурном стиле
    $memcache_obj = new Memcache;
 
    //Соединяемся с нашим сервером
    $memcache_obj->connect('127.0.0.1', 11211) or die("could not connect");
 
    //Попытаемся получить объект с ключом our_var
    $var_key = $memcache_obj->get('our_var');
    $varz[] = array("name" => "никитос иванов","lastname" => "иванов");
 
    if(!empty($var_key))
    {
        //Если объект закэширован, выводим его значение
        echo $var_key;
    }
 
    else
    {
        //Если в кэше нет объекта с ключом our_var, создадим его
        //Объект our_var будет храниться 5 секунд и не будет сжат
        $memcache_obj->set('our_var', $varz, false, 5);
 
        //Выведем закэшированные данные
        var_dump($memcache_obj->get('our_var'));
    }
 
    //Закрываем соединение с сервером Memcached
    $memcache_obj->close();
    ?>
