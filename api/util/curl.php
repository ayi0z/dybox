<?php
  class HttpCurl {

    public function HttpGet($url, $protocol='https'){
      if(empty($url)) { return; }
      $protocol = strtolower(trim($protocol));

      $curl = curl_init ();
      curl_setopt($curl, CURLOPT_URL, $url);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_TIMEOUT, 30);
      // curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.106 Safari/537.36');
      
      if($protocol == 'https'){
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
      }
      $res = curl_exec($curl);
      curl_close($curl);
      return json_decode($res, true);
  }

   public function HttpPost($url, $param, $protocol='https'){
      if(empty($url)) { return; }
      $protocol = strtolower(trim($protocol));
      if(!empty($param)) {$param  = json_encode($param); }

      $ch = curl_init(); //初始化CURL句柄 
      curl_setopt($ch, CURLOPT_URL, $url); //设置请求的URL
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type:application/json;charset="utf-8"',"Accept:application/json",'Content-Length: ' . strlen($param)));
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $param); //设置提交的参数
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //设为TRUE把curl_exec()结果转化为字串，而不是直接输出 

      if($protocol == 'https'){
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
      }

      $res = curl_exec($ch);

      curl_close($ch);
      return json_decode($res, true);

  }
  }
?>