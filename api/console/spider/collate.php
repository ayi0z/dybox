<?php

  require_once dirname(__FILE__)."/../../util/fastclosecon.php";

  header("content-type:application/json;charset=utf-8");

  $Request_Method = strtoupper($_SERVER['REQUEST_METHOD']);
  if ($Request_Method === 'POST' || $Request_Method === 'GET') {

    if(isset($_POST["token"]) && isset($_POST["spider"])){
      $token = $_POST["token"];
      $spider = $_POST["spider"];

      /**
       * 根据spider值自动引入对应的处理文件，spider的值可能是 tencent,youku,iqiyi等
       * 对应的，与collate同目录下必须有对应的文件路径 {spider}/sp_{spider}_collate.php
       * 每个文件中的class必须只能是 Spider_Collate，并接收$token用于记录进度消息，
       * class 必须实现 collating 方法
       */ 
      $spider_lower = strtolower($spider);
      $classpath = $spider_lower."/sp_collate.php";
      if(file_exists($classpath)){

        include_once $classpath;

        $collate = new Spider_Collate($token);
        $collate -> collating();
      }
    }
  }

  ?>