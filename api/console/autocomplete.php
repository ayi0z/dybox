<?php
    header("content-type:application/json;charset=utf-8");
    header("Access-Control-Allow-Origin: *");

    require_once dirname(__FILE__).'/../util/response_json.php';
    require_once dirname(__FILE__).'/../util/db_mongodb.php';
    require_once dirname(__FILE__).'/../util/curl.php';

    $Request_Method = strtoupper($_SERVER['REQUEST_METHOD']);
    if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
        /**
         * 根据关键字查询 title / title_en；
         * 1-从数据库中查询
         * 2-从豆瓣接口查询
         */
      if (isset($_POST["query"])) {  // 查询影片信息
          $query_par = $_POST["query"];
          $offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;
          $pagesize = isset($_POST['pagesize']) ? intval($_POST['pagesize']) : 10;
          $q_title = $query_par['title'];
          $q_dbid = isset($query_par['dbid']) ? $query_par['dbid'] : null;

          $localdb = get_medias_lcoaldb_by_keywords($q_title, $offset, $pagesize);
          $localdb = json_decode(json_encode($localdb), true);
        //   $douban = get_medias_douban_by_keywords($q_title, $offset, $pagesize);
          $douban = empty($q_dbid) ? get_medias_douban_by_keywords($q_title, $offset, $pagesize) 
                                   : get_medias_douban_by_dbid($q_dbid);

          $tmp = [];
          if(isset($localdb))
          { 
            foreach ($localdb as $key => $value) {
                $tmpkey = empty($value['dbid'])
                        ? strtolower(trim($value['title'])).strtolower(trim($value['year']))
                        : trim($value['dbid']);
                if(isset($tmp[$tmpkey])){
                    $value['title_en'] = empty($tmp[$tmpkey]['title_en']) ? $value['title_en'] : $tmp[$tmpkey]['title_en'];
                    $tmp[$tmpkey] = array_merge($tmp[$tmpkey], $value);
                }else{
                    $tmp[$tmpkey] = $value;
                }
            }
          }
          if(isset($douban))
          { 
            foreach ($douban as $key => $value) {
                $newdbid = trim($value['dbid']);
                $tmpkey = $newdbid;
                if(isset($tmp[$tmpkey])){
                    $value['title_en'] = empty($tmp[$tmpkey]['title_en']) ? $value['title_en'] : $tmp[$tmpkey]['title_en'];
                    $tmp[$tmpkey] = array_merge($tmp[$tmpkey], $value);
                    $tmp[$tmpkey]['from']='merge';
                } else {
                    $tmpkey = strtolower(trim($value['title'])).strtolower(trim($value['year']));
                    if(isset($tmp[$tmpkey])){
                        if(empty($tmp[$tmpkey]['dbid'])){
                            $value['title_en'] = empty($tmp[$tmpkey]['title_en']) ? $value['title_en'] : $tmp[$tmpkey]['title_en'];
                            $tmp[$tmpkey] = array_merge($tmp[$tmpkey], $value);
                            $tmp[$tmpkey]['from']='merge';
                        }
                        else {
                            $tmp[$newdbid] = $value;
                        }
                    } else {
                        $tmp[$newdbid] = $value;
                    }
                }
            }
          }

        //   $res = array_merge($localdb, $douban);
        //   $tmp = [];
        //   foreach ($res as $key => $value) {
        //     $tit = strtolower(trim($value['title']));
        //     $year = strtolower(trim($value['year']));
        //     $tmpkey = $tit.$year;
        //     if(isset($tmp[$tmpkey])){
        //         $value['title_en'] = empty($tmp[$tmpkey]['title_en']) ? $value['title_en'] : $tmp[$tmpkey]['title_en'];
        //         $tmp[$tmpkey] = array_merge($tmp[$tmpkey], $value);
        //         $tmp[$tmpkey]['from']='merge';
        //     }else{
        //         $tmp[$tmpkey] = $value;
        //     }
        //   }
          $res = array_values($tmp);
          Response_Json::json(1, "" , $res);
      } else {
        Response_Json::json(0, "no query");
      }
    } else {
        Response_Json::json(0, "invalid request method");
    }
    // 备用接口 http://t.yushu.im
    // 从 豆瓣接口查询 https://api.douban.com/v2/movie/subject/{query_par}
    function get_medias_douban_by_dbid($query_par)
    {
        if(empty($query_par)){
            Response_Json::json(0,"invalid query", null);
        }else{
            $url = 'https://api.douban.com/v2/movie/subject/'.$query_par;
            $curl = new HttpCurl();
            $res = $curl->HttpGet($url);

            if(isset($res['code'])){
                // if(trim($res['code']) == '112'){
                //     $url = 'http://t.yushu.im/v2/movie/subject/'.$query_par;
                //     $res = $curl->HttpGet($url);
                //     if(isset($res['code'])){
                //         return [];
                //     }
                // }else {
                    return [];
                // }
            }

            $data = [];
            $m['dbid']=$res['id'];
            $m['title'] = $res['title'];
            $m['title_en']=$res['original_title'];
            $m['pic']=$res['images']['small'];
            $m['year']=$res['year'];
            $m['type']=$res['subtype'] == 'tv' ? 2 : 1;
            $m['from']='douban';
            array_push($data, $m);
            return $data;
        }
    }

    // 从 豆瓣接口查询 https://api.douban.com/v2/movie/search?q={query_par}
    function get_medias_douban_by_keywords($query_par, $offset=0, $pagesize=30)
    {
        if(empty($query_par)){
            Response_Json::json(0,"invalid query", null);
        }else{
            $url = 'https://api.douban.com/v2/movie/search?q='.str_replace(' ', '', $query_par);
            $curl = new HttpCurl();
            $res = $curl->HttpGet($url);

            // if(isset($res['code'])){
            //     // if(trim($res['code']) == '112'){
            //     //     $url = 'http://t.yushu.im/v2/movie/search?q='.$query_par;
            //     //     $res = $curl->HttpGet($url);
            //     //     if(isset($res['code'])){
            //     //         return [];
            //     //     }
            //     // }else {
            //         return [];
            //     // }
            // }
            $data = [];
            if(!empty($res)){
                foreach ($res['subjects'] as $key => $value) {
                    $m['dbid']=$value['id'];
                    $m['title'] = $value['title'];
                    $m['title_en']=$value['original_title'];
                    $m['pic']=$value['images']['small'];
                    $m['year']=$value['year'];
                    $m['type']=$value['subtype'] == 'tv' ? 2 : 1;
                    $m['from']='douban';
                    // $m['alt']=$value['alt'];
                    array_push($data, $m);
                }
            }
            return $data;
        }
    }

    // 从数据库中查询
    function get_medias_lcoaldb_by_keywords($query_par, $offset=0, $pagesize=30)
    {
        $query_par = trim($query_par);
        if(empty($query_par)){
            Response_Json::json(0,"invalid query", null);
        }else{
            $coll = 'medias_cover';
            $mongo_db = new DB_MongoDB_Handler('mobox');

            $options = [
                'projection' => ['_id'=>1, 'title'=>1, 'title_en'=>1, 'pic'=>1, 'year'=>1, 'dbid'=>1],
                'skip' => $offset, 
                'limit' => $pagesize
            ];
            $filter = [
                // 'isoff' => 0,
                 '$or'=> [['title' => ['$regex'=>$query_par, '$options' => 'i']], 
                 ['title_en' => ['$regex'=>$query_par, '$options' => 'i']]]
            ];
            return $mongo_db->query($coll, $filter, $options);
        }
    }
    exit;
?>
