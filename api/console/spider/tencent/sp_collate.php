<?php

  require_once dirname(__FILE__)."/../../../util/db_mongodb.php";
  require_once dirname(__FILE__)."/../../progress.php";

  set_time_limit(0);

  /*
   * 腾讯视频
   * 从数据中读取已经从网络中抓取的原始数据，
   * 循环整理成ui可以理解的json,
   * 将整理后的数据存储到数据库
   */
  class Spider_Collate {

    private $pg_token = null;
    public function __construct($_pg_token) {
      $this->pg_token = $_pg_token;
    }

    // 从数据库中读取抓取到的数据，进行整理后，更新数据库
    public function collating(){
      Progress::set($this->pg_token, 0 ,"开始加载原始数据...");

      $offset = 0;
      $pagesize = 100; // 每次预计取值的数量
      $counsize = 0; // 每次实际取出的数量

      $totalcountsize=0; // 每次实际取出的累计值

      $totalcount = $this->queryCount();  // 取出需要处理的数据的总数
      $ls_medias_cover_ui = Array();  // 存放处理好了的数据
      $count_medias_cover_ui = 0;  // 已经处理好了的数据数目，它应该总是等于count($ls_medias_cover_ui)

      do{
        $result = $this->query($offset, $pagesize);
        $counsize = count($result);
        $totalcountsize = $totalcountsize + $counsize;

        foreach ($result as $key => $value) {
          array_push($ls_medias_cover_ui, $this->to_ui_json($value));

          // if($count_medias_cover_ui<100){
          //   file_put_contents("debug", json_encode($ls_medias_cover_ui[$count_medias_cover_ui],JSON_UNESCAPED_UNICODE), FILE_APPEND);
          // }
          $count_medias_cover_ui ++;
        }

        Progress::set_m($this->pg_token, $totalcountsize, $totalcount, "已取出 ".$totalcountsize."/".$totalcount.", 已清洗".$count_medias_cover_ui, 0);
        
        $offset = $offset + $pagesize;
      }while($counsize == $pagesize);
      
      $count_medias_cover_ui = count($ls_medias_cover_ui);
      Progress::set($this->pg_token, 100, "共完成数据清洗 ".$count_medias_cover_ui."/".$totalcount, 0);

      Progress::set($this->pg_token, 0, "正在将数据写入数据库...", 0);
      $clips =  array_chunk($ls_medias_cover_ui, $count_medias_cover_ui/200+1);
      $storecount = 0;
      foreach ($clips as $key => $value) {
        $this->store($value);
        $storecount = $storecount + count($value);
        Progress::set_m($this->pg_token, $storecount, $count_medias_cover_ui, "正在将数据写入数据库...".$storecount."/".$count_medias_cover_ui, 0);
      }
      Progress::set($this->pg_token, 100, "数据已写入数据库", 1);
    }

    //读数据库
    private $mongo_db = null;
    protected function queryCount(){
      if(empty($this->mongo_db))
      {
        $this->mongo_db = new DB_MongoDB_Handler("mobox");
      }

      return $this->mongo_db->queryCount('tencent_raw'.date('Ymd'));
    }
    protected function query($offset=0, $pagesize=30){
      if(empty($this->mongo_db))
      {
        $this->mongo_db = new DB_MongoDB_Handler("mobox");
      }

      $filter = [];
      $options = [
        "skip" => $offset,
        "limit" => $pagesize,
        "sort" => ["_id" => 1]
      ];
      return $this->mongo_db->query('tencent_raw'.date('Ymd'), $filter, $options);
    }
    // 写数据库
    protected function store($data){
      if(!empty($data)){

        if(empty($this->mongo_db))
        {
          $this->mongo_db = new DB_MongoDB_Handler("mobox");
        }

        $this->mongo_db->insertMany($data, "medias_cover");

        unset($data);
      }
    }

    protected function to_ui_json($v_raw){
      $v_json_ui["cid"] = $v_raw->cover_id;
      $v_json_ui["type"] = $v_raw->type;
      $v_json_ui["title"] = $v_raw->title;
      $v_json_ui["title_en"] = $v_raw->title_en;
      $v_json_ui["alias"] = $v_raw->alias;
      $v_json_ui["year"] = $v_raw->year;
      $v_json_ui["area"] = $v_raw->area_name;
      $v_json_ui["langue"] = $v_raw->langue;
      $v_json_ui["director"] = $v_raw->director;
      $v_json_ui["actor"] = $v_raw->leading_actor;
      $v_json_ui["subtype"] = $v_raw->subtype;
      $v_json_ui["desc"] = $v_raw->description;
      $v_json_ui["score"]["douban"] = $v_raw->douban_score;
      $v_json_ui["score"]["tencent"] = $v_raw->score->score;
      $v_json_ui["score"]["imdb"] = $v_raw->imdb_score;
      $v_json_ui["score"]["mtime"] = $v_raw->mtime_score;
      $v_json_ui["pic"] = $v_raw->vertical_pic_url;
      $v_json_ui["hpic"] = $v_raw->horizontal_pic_url;
      //$v_json_ui["pubdate"] = $v_raw->publish_date;
      $v_json_ui["pubdate"] = time()*1000;
      $v_json_ui["istrailer"] = $v_raw->is_trailer;
      $v_json_ui["epscount"] = $v_raw->episode_all;

      $v_json_ui["eps"] = Array();
      foreach ($v_raw->video_ids as $key => $value) {
        $eps["epid"] = $value;
        $eps["title"] = strval($key+1);
        $eps["playgate"] = "tencent";
        $eps["url"] = "https://v.qq.com/x/cover/".$v_raw->cover_id."/".$value.".html";
        $eps["hot"] = 0;
        $eps["isoff"] = '0';
        array_push($v_json_ui["eps"], $eps);
      }
      $v_json_ui["hot"] = 0;
      $v_json_ui["mocode"] = "M".strtoupper(substr(strrev((string)$v_raw->_id), 0, 5));
      $v_json_ui["isoff"] = '0';
      $v_json_ui["latime"] = time()*1000;

      return $v_json_ui;
    }
  }
?>