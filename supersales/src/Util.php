<?php
/**
 * 共用元件
 */
class Util{

  const TOKEN = 'token';
  
  /**
   * 判斷是否為json字串 
   * @param  String $string
   * @return boolean
   */
  public static function isJSON($string){
    if(is_string($string) && is_array(json_decode($string, true))){
      return true;
    }
    return false;
  }

  /**
  * 讀取檔案裡面的json字串
  * 會檢查若字串非josn格式錯誤或是檔案不存在
  * @param  String $path
  * @return String isJSON
  */
  public static function readFilebyJson($path){
    $data = '';
    if(file_exists($path)){
      $file = fopen($path, 'r');
      while (!feof($file)) {
        $value = fgets($file);
        $value = trim($value);
        if(strlen($value) > 0){
          $data =  $data . $value;
        }
      }
    }else{
      throw new Exception('Cant find file in this path: '.$path, 1);
    }
    fclose($file);

    if(!Util::isJSON($data))
      throw new InvalidArgumentException('Error JSON format, data: '.$data, 1);
    
    return $data;
  }

 /**
  * 刪除apcu的token
  * @return String token value
  */
  public static function deleteToken(){
    if(apcu_exists(self::TOKEN)){
      apcu_delete(self::TOKEN);
    }
  }

  /**
   * 取token
   * @return String token value
   */
  public static function getToken(){
    if(apcu_exists(self::TOKEN)){
      return apcu_fetch(self::TOKEN);
    }
    $post = array(
      'client_id'=>TOKEN_CLIENT_ID,
      'client_secret'=>TOKEN_CLIENT_SECRET,
      'grant_type'=>'client_credentials',
      'redirect_uri'=>'cjob',
      'scope'=>'cjob'
    );
    // $curl =  new Curl(TOEKN_PATH); //mac test use false
    $curl =  new Curl(TOEKN_PATH,true);
    $curl->setHeader(array('Content-Type: application/x-www-form-urlencoded'));
    $data = $curl->post($post);
    $dataArr = json_decode($data, true);
    apcu_store(self::TOKEN, $dataArr['access_token']);
    return $dataArr['access_token'];
  }


}
