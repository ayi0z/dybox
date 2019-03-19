<?php
  header("content-type:application/json;charset=utf-8");
  header("Access-Control-Allow-Origin: *");

  $Request_Method = strtoupper($_SERVER['REQUEST_METHOD']);
  if ($Request_Method === 'POST' || $Request_Method === 'GET') {
    $token = $_POST["token"];
    if(isset($token)){
      Progress::get($token);
    }
  }
  
  class Progress{
    private static $tmpdir = "ptmp/";

    public static function set_m($token, $currentvalue, $maxvalue, $text, $over = 0) {
      
      if(empty($token)) { return; }

      $percent = 0;
      
      if($currentvalue==0) { $percent = 0; }
      elseif($maxvalue==0) { $percent = 100; }
      else { $percent = intval($currentvalue/$maxvalue*100); }

      self::set($token, $percent, $text, $over);
    } 

    public static function set($token, $value, $text, $over = 0) {

      if(empty($token)) { return; }

      $progress = '{"value":"'.$value.'","text":"'.$text.'","isover":'.$over.'}';

      $pgdir = $_SERVER['DOCUMENT_ROOT']."/".self::$tmpdir;

      if (!is_dir($pgdir)) 
      {
        mkdir($pgdir, 0777);
      }

      file_put_contents($pgdir.$token, $progress);
    } 

    public static function get($token){

      $pgdir = $_SERVER['DOCUMENT_ROOT']."/".self::$tmpdir.$token;

      if(file_exists($pgdir))
      {
        $pg_content = file_get_contents($pgdir);

        echo $pg_content;

        $json = json_decode($pg_content);
        if(property_exists($json, "isover"))
        {
          $isover = $json->isover;
          if($isover === 1){
            unset($json);
            unset($isover);
            unset($pg_content);
            self::delete($token);
          }
        }
        else {
          echo  '{"value":"0","text":"no loading infomation", "isover":0}';
        }
      }
    }

    public static function delete($token){

      $pgdir = $_SERVER['DOCUMENT_ROOT']."/".self::$tmpdir.$token;

      if(file_exists($pgdir))
      {
        chmod($pgdir, 0777);
        unlink($pgdir);
      }
    }
  }
?>