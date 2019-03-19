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
        if(isset($_POST['paygate'])){
          $pg = new PlayGate();
          $pg ->save($_POST['paygate']);
        }else {
          Response_Json::json(0, 'no data to save');
        }
      } elseif ($action == 'query') {
        $pg = new PlayGate();
        $res = $pg ->query($_POST['offset'], $_POST['pagesize']);
        Response_Json::json(1, "query finished", $res);
      } elseif ($action == "queryurl") {
        echo "{'action':'0'}";
      } else {
        Response_Json::json(0, "invalid operation");
      }
    }else {
      Response_Json::json(0, "invalid operation");
    }
  }else {
    Response_Json::json(0, "invalid operation");
  }

  class PlayGate {
    //读数据库
    private $mongo_db = null;
    private $coll = "playgate"; // playgate 数据集
    private function initmongodb(){
      if(empty($this->mongo_db))
      {
        $this->mongo_db = new DB_MongoDB_Handler("mobox");
      }
    }

    public function save($data){
      if(!empty($data["gateurl"])){
        $this->initmongodb();
        // 1.如果有id，则更新
        // 2.如果没有id，检测url，如果有，则更新
        $gpurl = trim(strtolower($data["gateurl"]));
        $filter= null;
        if(empty($data["id"])){
          $filter = [
            "gateurl" => $gpurl
          ];
        } else {
          $filter = [
            '$or' => [
              ["_id" => new \MongoDB\BSON\ObjectId($data["id"])],
              ["gateurl" => $gpurl]
            ]
          ];
        }
        $seter = ['$set' => [
          "gateurl" => $gpurl,
          "isoff" => $data["isoff"],
          "inscope" => isset($data["inscope"])?$data["inscope"]:[]
        ],'$setOnInsert'=>["hit"=>0]];
        $options =['multi' => true, 'upsert' => true];
        $this->mongo_db->update($this->coll, $seter, $filter, $options);
        // $res = $this->query(0 ,1,$filter);
        Response_Json::json(1, "数据已更新", null);
      }else {
        Response_Json::json(0, "no data to save");
      }
    }

    public function query($offset=0, $pagesize=10, $filter=[]){
      $this->initmongodb();
      $options = [
        "skip" => $offset,
        "limit" => $pagesize,
        "sort" => ["hit" => -1]
      ];

      return $this->mongo_db->query($this->coll, $filter, $options);
    }

    protected function queryCount(){
      $this->initmongodb();
      return $this->mongo_db->queryCount($this->coll);
    }
  }
?>