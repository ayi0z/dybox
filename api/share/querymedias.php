<?php
    header("content-type:application/json;charset=utf-8");
    header("Access-Control-Allow-Origin: *");

    require_once dirname(__FILE__).'/../util/response_json.php';
    require_once dirname(__FILE__).'/../util/db_mongodb.php';

    /**
     * 媒体列表查询，client/console 共同使用
     * 可根据 标题/英文标题/关键字/类别/类型/年份/地区/sort查询/上架时间/
     */
    $Request_Method = strtoupper($_SERVER['REQUEST_METHOD']);
    if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
      if (isset($_POST["mid"])) {  // 查询影片信息
        $query_par = $_POST["mid"];
        Response_Json::json(1, "" , get_media_by_mid($query_par));
      } elseif (isset($_POST["title"])) {  // 查询影片信息
        $query_par = $_POST["title"];
        $offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;
        $pagesize = isset($_POST['pagesize']) ? intval($_POST['pagesize']) : 30;
        Response_Json::json(1, "" , get_medias_by_title($query_par, $offset, $pagesize));
      } else {
        Response_Json::json(0, "no query");
      }
    } else {
        Response_Json::json(0, "invalid request method");
    }

    function get_media_by_mid($query_par)
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

    function get_medias_by_title($query_par, $offset=0, $pagesize=30)
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
    exit;
?>
