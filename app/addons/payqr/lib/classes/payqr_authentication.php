<?php
/**
 * Проверка валидности уведомлений и ответов от PayQR (проверка секретных ключей)
 */
 
class payqr_authentication
{

  /**
   * Проверяем header уведомлений и ответов от PayQR на соответствие значению SecretKeyIn
   *
   * @param $secretKeyIn
   * @return bool
   */
  public static function checkHeader($secretKeyIn, $headers=false)
  {
    if(!payqr_config::$checkHeader)
      return true;
    if(!$headers){
      if (!function_exists('getallheaders')){
        $headers = payqr_base::getallheaders();
      }
      else{
        $headers = getallheaders();
      }
    }
    if (!$headers) {
      header("HTTP/1.0 404 Not Found");
      payqr_logs::log("Не удалось выполнить проверку входящего секретного ключа SecretKeyIn, отсутствует headers", __FILE__." ".__METHOD__, __LINE__);
      return false;
    }
    // Проверяем соответствие пришедшего значения поля PQRSecretKey значению SecretKeyIn из конфигурации библиотеки
    if (isset($headers['PQRSecretKey']) && $headers['PQRSecretKey'] == $secretKeyIn) {
      return true;
    }
    foreach($headers as $key=>$header){
      $headers[strtolower($key)] = $header;
    }
    if (isset($headers['pqrsecretkey']) && $headers['pqrsecretkey'] == $secretKeyIn) {
      return true;
    }
    header("HTTP/1.0 404 Not Found");
    payqr_logs::log("Входящий секретный ключ из headers не совпадает с входящим ключом из файла конфигурации \n\r Текущее значение SecretKeyIn из вашего payqr_config.php: ".$secretKeyIn." \n\r Содержание headers полученного уведомления от PayQR: ".print_r($headers, true), __FILE__." ".__METHOD__, __LINE__);
    return false;
  }

} 