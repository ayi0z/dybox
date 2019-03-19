<?php
  include_once dirname(__FILE__)."/../simple_html_dom.php";
  require_once dirname(__FILE__)."/../../../util/db_mongodb.php";
  require_once dirname(__FILE__)."/../../progress.php";

  set_time_limit(0);

  /*
   * 腾讯视频
   * url: https://v.qq.com/x/list/movie?sort=19&offset={offset}
   * sort 排序规则: 19->最新上架， 18->最近热播， 16->最受好评， 21->豆瓣好评
   * offset 查询便宜量， 0, 30, 60, ... 4999
   * 腾讯最大可搜索出5000条数剧， 所以offset最大只能是4999，5000开始就查不到数据了
   * ul.class=figures_list 当前页查询到的电影列表
   *    -》li.class=list_item 一条电影信息
   *        -》a.class=figure.href 属性得到电影的播放页面链接 -》https://v.qq.com/x/cover/ewh18dn62hvddln.html -》 ewh18dn62hvddln 是很重要的一个信息
   *                         .data-float 属性可以得到 ewh18dn62hvddln 这样的信息，可以称之为cover_id
   * 抓取coverid后，用coverid到 https://v.qq.com/x/cover/{coverid}.html 播放页抓取更相信信息
   *    此版本使用的方法是抓取 coverid 后到播放页抓取 COVER_INFO 参数，
   *    COVER_INFO： 这是一个被放在播放页面上的，存储了视频大部分信息的参数，可以解析成json格式
   */
  class Spider_Crawling {

    /**
     * 进度条口令，如果null将不会输出进度数据
     */
    private $pg_token = null;
    public function __construct($_pg_token =null) {
      $this->pg_token = $_pg_token;
    }

    /**
     * 爬取电影列表，获得 cover_id 列表， from https://v.qq.com/x/list/movie?sort={sort}&offset={offset}
     * 
     */
    protected function crawling_coverid($sort){
      Progress::set($this->pg_token, 0 ,"开始抓取 CoverId of sort.".$sort);
      $ls_coverid = Array();
      $return = false;
      // offset 从0开始，每次加30，最大为4999   4999-30 > 4969
      for($offset = 0; $offset < 5000; $offset = $offset+30){
        $html = file_get_html('https://v.qq.com/x/list/movie?sort='.$sort.'&offset='.$offset);
        foreach($html->find("ul.figures_list li.list_item a.figure") as $found) {
          $return - true;
          array_push($ls_coverid, $found->getAttribute("data-float"));
        }
        $html->clear();
        unset($html);

        $pageindex = ($offset/30+1);
        Progress::set_m($this->pg_token, $pageindex, 167, "正在抓取 CoverId of sort.".$sort."列表...".$pageindex."/167 页");

        $offset = $offset > 4969 && $offset < 4999 ? 4969 : $offset;
      }
      // 去重
      $ls_coverid = array_unique($ls_coverid);
      Progress::set($this->pg_token, 100, "共抓取 ".count($ls_coverid)." 个 CoverId of sort.".$sort);
      return $ls_coverid;
    }

    /**
     * 执行爬取数据操作，
     * 爬取过程中分批次存储到数据库
     * step 1: 爬取电影列表，获得 cover_id 列表， from https://v.qq.com/x/list/movie?sort={sort}&offset={offset}
     * step 2: 循环遍历 cover_id 列表, 抓取详细信息，并存储数据库 from https://v.qq.com/x/cover/{cover_id}}.html
     */
    public function crawling(){

      // step 1 >> 爬取电影列表，获得 cover_id 列表
      /**
       * $sort 指定抓取的排序规则
       *    19->最新上架
       *    18->最近热播
       *    16->最受好评
       *    21->豆瓣好评
       */
      $ls_coverid_18 = $this->crawling_coverid(18); // 最近热播
      
      $ls_coverid_19 = $this->crawling_coverid(19); // 最新上架
      $ls_coverid_16 = $this->crawling_coverid(16); // 最受好评
      $ls_coverid_21 = $this->crawling_coverid(21); // 豆瓣好评

      // coverid 合并列表 eg：g9vy8krbp214wsk，coverid是构建视频播放页面地址的核心参数
      $ls_coverid = array_merge($ls_coverid_18, $ls_coverid_19, $ls_coverid_16, $ls_coverid_21);
      if(empty($ls_coverid)){
        Progress::set($this->pg_token, 100, "没有获取任何 CoverId", 1);
        exit;
      }

      // 去重
      $ls_coverid = array_unique($ls_coverid);  // 去除重复的coverid

      // 读取数据库已爬去的cid
      $last_cids = $this->query_all_cid();
      $r_cids =  array_intersect($ls_coverid, $last_cids);
      unset($last_cids);
      $new_cids = array_diff($ls_coverid, $r_cids);
      unset($r_cids);
      unset($ls_coverid);

      $new_cids = array_values($new_cids);  // 重置索引，从0开始递增索引
      $coverid_count = count($new_cids);
      Progress::set($this->pg_token, 100, "共抓取 ".$coverid_count." 个 CoverId", 0);

      // step 2 >> 循环遍历 cover_id 列表, 抓取详细信息，并存储数据库
      $ls_videos = Array();
      $counter = 1; // 计数器
      $store_count = 300; // 指示每多少笔写一次数据库
      $cid_maxindex = $coverid_count-1;
      $store_total_count =0;  //实际记录到数据里数量

      Progress::set($this->pg_token, 0, "准备抓取每个 CoverId 的详细数据");

      foreach ($new_cids as $key => $value) {
        $html = file_get_html("https://v.qq.com/x/cover/".$value.".html");

        if(is_callable(array($html, "save"))){
          $str_html = $html->save();  // 将$html转成字符串
          $keyword_s="var COVER_INFO = ";
          $keyword_e="var COLUMN_INFO";
          $index_s=strpos($str_html, $keyword_s);
          $index_e=strpos($str_html, $keyword_e);

          if( $index_s >=0 && $index_e >= 0){
            $res_mid = substr($str_html, $index_s, $index_e - $index_s);
            $res_str = str_replace($keyword_s, "", $res_mid);
        
            $cover = json_decode($res_str);
            array_push($ls_videos, $cover);
            
            Progress::set_m($this->pg_token, ($key+1), $coverid_count, "正在抓取CoverId的详细数据...".($key+1)."/".$coverid_count."");
          }

          $html->clear();
          unset($html);
        }
    
        // 存一次数据库 存储目的：数据库.tencent_raw_{date('Ymd')}/原始的
        if($counter >= $store_count || $cid_maxindex == $key){
          $this->store($ls_videos);
          $store_total_count = $store_total_count + count($ls_videos);
          unset($ls_videos);
          $ls_videos = Array();
          $counter = 1 ;  // 重置计数器
        }else{ $counter = $counter + 1; }
      }

      Progress::set($this->pg_token, 100, "共抓取 ".$store_total_count." 条数据。", 1);

      unset($new_cids);
    }

    // 写数据库
    private $mongo_db = null;
    protected function store($data){
      if(!empty($data)){

        if(empty($this->mongo_db))
        {
          $this->mongo_db = new DB_MongoDB_Handler("mobox");
        }
        
        $this->mongo_db->insertMany($data, "tencent_raw".date('Ymd'));  // 存储到腾讯视频信息原始数据集

        unset($data);
      }
    }

    protected function query_all_cid(){
      if(empty($this->mongo_db))
      {
        $this->mongo_db = new DB_MongoDB_Handler("mobox");

        $res = $this->mongo_db->query("medias_cover", [], ['projection' => ['cid'=>1]]);
        return array_column($res, 'cid');
      }
    }
  }
?>