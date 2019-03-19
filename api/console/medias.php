<?php
    header("content-type:application/json;charset=utf-8");
    header("Access-Control-Allow-Origin: *");

    require_once dirname(__FILE__).'/../util/response_json.php';
    require_once dirname(__FILE__).'/../util/db_mongodb.php';
    require_once dirname(__FILE__).'/../util/curl.php';

    $Request_Method = strtoupper($_SERVER['REQUEST_METHOD']);
    if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {

      $offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;
      $pagesize = isset($_POST['pagesize']) ? intval($_POST['pagesize']) : 30;
    
      if (isset($_POST["mid"])) {  // 查询影片信息
        $query_par = $_POST["mid"];
        
        $mquery = new MediasQuery();
        Response_Json::json(1, "" , $mquery -> get_media_by_mid($query_par));
      } elseif (isset($_POST["mfilter"])) {  // 查询影片信息
        $query_par = $_POST["mfilter"];

        $mquery = new MediasQuery();
        if(!empty($query_par['_id']['$oid'])){
            // 从本地数据库读数据
            $localdb = $mquery -> get_media_by_mid($query_par['_id']['$oid']);
            $localdb = empty($localdb) ? null : $localdb[0];
            $localdb = json_decode(json_encode($localdb), true);
        }
        if(!empty($query_par['dbid'])){
            // 从 豆瓣 读 https://api.douban.com/v2/movie/subject/{$dbid}
            $douban = $mquery->get_media_from_douban(trim($query_par['dbid']));
        }

        if(!empty($query_par['from']) && $query_par['from']=='merge'){
            // 合并 本地 和 豆瓣 数据
            if(empty($localdb)){
                $res = $douban;
            } else {
                $res = empty($douban) ? $localdb : array_merge($localdb, $douban);
            }
        } elseif(!empty($query_par['from']) && $query_par['from']=='douban'){
            $mocode_pre=['1'=>'M','2'=>'T'];
            $douban['mocode'] = $mocode_pre[$douban['type']].substr(md5(uniqid(microtime(true),true)),0,5);
            $res = $douban; 
        } else {
            $res = $localdb;
        }

        Response_Json::json(1, "" , $res);
      }elseif (isset($_POST["title"])) {  // 查询影片信息
        $query_par = $_POST["title"];

        $mquery = new MediasQuery();
        Response_Json::json(1, "" , $mquery -> get_medias_by_title($query_par, $offset, $pagesize));
      } elseif (isset($_POST["query"])) {  // 查询影片信息
        $query_par = $_POST["query"];

        $sort = null;
        if(isset($_POST["sort"])){
            $sort = $_POST["sort"];
        }

        $mquery = new MediasQuery();
        Response_Json::json(1, "" , $mquery -> get_medias_list($query_par, $sort, $offset, $pagesize));
      } else {
        Response_Json::json(0, "no query");
      }
    } else {
        Response_Json::json(0, "invalid request method");
    }

    class MediasQuery {
        /**
         * 读取指定 _id 的影片信息
         * => cover info
         */
        public function get_media_by_mid($query_par)
        {
            if(empty($query_par)){
                Response_Json::json(0,"invalid query", null);
            }else{
                $coll = 'medias_cover';
                $mongo_db = new DB_MongoDB_Handler('mobox');
    
                $options = [
                    'skip' => 0, 
                    'limit' => 1
                ];
                $filter = [
                    '_id' => new \MongoDB\BSON\ObjectId($query_par)
                ];
                return $mongo_db->query($coll, $filter, $options);
            }
        }
        /**
         * 使用dbid从豆瓣api读影片信息
         * => cover info
         */
        public function get_media_from_douban($dbid){
            $dbid = trim($dbid);
            if(!empty($dbid)){
                $url = 'https://api.douban.com/v2/movie/subject/'.$dbid;
                $curl = new HttpCurl();
                $res = $curl->HttpGet($url);

                if(isset($res['code'])){return null;}

                $m['dbid']=$dbid;
                $m['type']=$res['subtype'] == 'tv' ? '2' : '1';
                $m['title'] = $res['title'];
                $m['title_en']=$res['original_title'];
                $m['alias']=$res['aka'];
                $m['year']=$res['year'];
                $m['area']=isset($res['countries'])?$res['countries'][0]:'';
                $m['director']=array_column($res['directors'],'name');
                $m['actor']=array_column($res['casts'],'name');
                $m['subtype']=$res['genres'];
                $m['desc']=$res['summary'];
                $m['score']['douban']=$res['rating']['average'];
                $m['pic']=$res['images']['small'];
                $m['epscount']=$res['episodes_count'];
                return $m;
            }
        }

        protected function partofilter($query_par){
            if(empty($query_par)){
                return [];
            }
            $filter = [];
            if(!empty(trim($query_par['title']))){
                $t = trim($query_par['title']);
                $t = '.*'.str_replace(' ', '.*', $t).'.*';
                $t = new MongoDB\BSON\Regex($t); 
                $filter['$or'] = [['title' => ['$regex'=>$t, '$options' => 'i']], 
                                  ['title_en' => ['$regex'=>$t, '$options' => 'i']],
                                  ['alias' => ['$regex'=>$t, '$options' => 'i']]];
            }
            if(!empty($query_par['year'])){
                $filter['year'] = ['$in'=>$query_par['year']];
            }
            if(!empty($query_par['area'])){
                $filter['area'] = ['$in'=> $query_par['area']];
            }
            if(!empty($query_par['langue'])){
                $filter['langue'] = ['$in'=>$query_par['langue']];
            }
            if(!empty($query_par['type'])){
                $filter['type'] = ['$in'=>$query_par['type']];
            }
            if(!empty($query_par['epsprog.isall'])){
                $filter['epsprog.isall'] = ['$in'=> $query_par['epsprog.isall']];
            }

            return $filter;
        }
        /**
         * 查询符合条件的影片总数
         * => int
         */
        public function get_medias_count($query_par){
            $filter = $this->partofilter($query_par);
            $coll = 'medias_cover';
            $mongo_db = new DB_MongoDB_Handler('mobox');
            return $mongo_db->queryCount($coll, $filter);
        }
        /**
         * 查询 medias list
         * => list
         */
        public function get_medias_list($query_par, $sort, $offset=0, $pagesize=30)
        {
            $coll = 'medias_cover';
            $mongo_db = new DB_MongoDB_Handler('mobox');

            $sort_op = ['latime'=>-1, 'pubdate' => -1, 'year' => -1, 'hot' => -1];
            if(isset($sort) && !empty($sort) && !empty($sort["prop"])){
                $sort_op = [$sort["prop"] => $sort["order"] == 'ascending' ? 1 : -1];
            }
            $options = [
                'skip' => $offset, 
                'limit' => $pagesize,
                'sort' => $sort_op
            ];
            $filter = $this->partofilter($query_par);
            return $mongo_db->query($coll, $filter, $options);
        }

        /**
         * 根据 标题title 或 英文标题title_en 模糊查询影片信息
         * => list
         */
        public function get_medias_by_title($query_par, $offset=0, $pagesize=30)
        {
            if(empty($query_par)){
                Response_Json::json(0,"invalid query", null);
            }else{
                $coll = 'medias_cover';
                $mongo_db = new DB_MongoDB_Handler('mobox');
    
                $options = [
                    'skip' => $offset, 
                    'limit' => $pagesize
                ];
                $filter = [
                    'isoff' => 0,
                    '$or'=> [['title' => ['$regex'=>$query_par, '$options' => 'i']], 
                             ['title_en' => ['$regex'=>$query_par, '$options' => 'i']]]
                ];
                return $mongo_db->query($coll, $filter, $options);
            }
        }
    }
    exit;
?>
