<?php
  require_once dirname(__FILE__).'/../util/response_json.php';
  require_once dirname(__FILE__).'/../util/db_mongodb.php';

  header('content-type:application/json;charset=utf-8');
  header("Access-Control-Allow-Origin: *");

  $Request_Method = strtoupper($_SERVER['REQUEST_METHOD']);
  if ($Request_Method == 'POST') {

    if(isset($_POST['action'])){

      $action = strtolower($_POST['action']);
      if($action == 'save'){
        if(isset($_POST['mediacover'])){
          $mc = new MediasCrud();
          $mc ->save($_POST['mediacover']);
        }else {
          Response_Json::json(0, 'no data to save');
        }
      // } elseif ($action == 'query') {
      //   $mc = new MediasCrud();
      //   $res = $mc ->query($_POST['offset'], $_POST['pagesize']);
      //   Response_Json::json(1, "query finished", $res);
      } else {
        Response_Json::json(0, "invalid operation");
      }
    }else {
      Response_Json::json(0, "invalid operation");
    }
  }else {
    Response_Json::json(0, "invalid operation");
  }

  class MediasCrud {
    //读数据库
    private $mongo_db = null;
    private $coll = "medias_cover"; // playgate 数据集
    private function initmongodb(){
      if(empty($this->mongo_db))
      {
        $this->mongo_db = new DB_MongoDB_Handler("mobox");
      }
    }

    public function save($data){
      if(!empty($data["_id"])){
        $this->initmongodb();
        // 1.如果有id，则替换，没有则增加
        $_id = trim(strtolower($data["_id"]['$oid']));
        $filter = ["_id" => new \MongoDB\BSON\ObjectId($_id)];
        $data['mocode'] = strtoupper($data['mocode']);
        $data['hot'] = intval($data['hot']);
        $data['pubdate'] = $data['pubdate'] ? strtotime($data['pubdate'])*1000 : time()*1000;  // 上架日期 => 时间戳
        $data['latime'] = time()*1000;
        unset($data['_id']);
        $seter = ['$set' => $data];
        $options =['upsert' => true];
        $this->mongo_db->update($this->coll, $seter, $filter, $options);
        Response_Json::json(1, "数据已更新");
      }else {
        $this->initmongodb();
        unset($data['_id']);
        $data['mocode'] = strtoupper($data['mocode']);
        $data['hot'] = intval($data['hot']);
        $data['pubdate'] = $data['pubdate'] ? strtotime($data['pubdate'])*1000 : time()*1000;  // 上架日期 => 时间戳
        $data['latime'] = time()*1000;
        $this->mongo_db->insert($data, "medias_cover");
        Response_Json::json(1, "数据已新增");
      }
    }
    // public function query($offset=0, $pagesize=30, $filter=[]){
    //   $this->initmongodb();
    //   $options = [
    //     "skip" => $offset,
    //     "limit" => $pagesize,
    //     "sort" => ["year" => -1, 'hot'=>-1]
    //   ];

    //   return $this->mongo_db->query($this->coll, $filter, $options);
    // }

    // protected function queryCount(){
    //   $this->initmongodb();
    //   return $this->mongo_db->queryCount($this->coll);
    // }
  }
?>