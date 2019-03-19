<?php
  header("content-type:application/json;charset=utf-8");
  header("Access-Control-Allow-Origin: *");
  
  require_once dirname(__FILE__).'/../../util/response_json.php';
  require_once dirname(__FILE__)."/simple_html_dom.php";

  $Request_Method = strtoupper($_SERVER['REQUEST_METHOD']);
  if ($Request_Method === 'POST' || $Request_Method === 'GET') {

    if(isset($_POST["syncsource"])){
      $syncsource = $_POST["syncsource"];
      if(empty($syncsource)) { return Response_Json::json(0,'请设置源URL'); }

      $html = file_get_html($syncsource);
      if(is_callable(array($html, "save"))){
        $str_html = $html->save();  // 将$html转成字符串
        $keyword_s="var links=";
        $keyword_e=";var id=";
        $index_s=strpos($str_html, $keyword_s);
        $index_e=strpos($str_html, $keyword_e);

        if( $index_s >=0 && $index_e >= 0){
          $res_mid = substr($str_html, $index_s, $index_e - $index_s);
          
          $res_str = trim(str_replace($keyword_s, "", $res_mid),'\'');
          $links = array_filter(explode('|',$res_str));

          Response_Json::json(1, 'success', $links);
        }
      }
      Response_Json::json(0, '无法读取源数据');
      $html->clear();
      unset($html);
    }
  }
?>