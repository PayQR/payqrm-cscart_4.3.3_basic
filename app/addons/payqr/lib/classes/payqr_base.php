<?php

/**
 * Проверка конфигурации библиотеки PayQR
 */
class payqr_base
{

  public static $apiUrl = "https://payqr.ru/shop/api/1.0"; // Не меняйте этот адрес без получения официальных извещений PayQR

  public static function checkConfig()
  {
    payqr_config::$secretKeyIn = trim(payqr_config::$secretKeyIn);
    payqr_config::$secretKeyOut = trim(payqr_config::$secretKeyOut);
    payqr_config::$logFile = trim(payqr_config::$logFile);
    payqr_config::$merchantID = trim(payqr_config::$merchantID);

    if (payqr_config::$secretKeyIn == "") {
      throw new payqr_exeption("Поле payqr_config::secretKeyIn не может быть пустым, проверьте конфигурацию библиотеки PayQR");
    }
    if (payqr_config::$secretKeyOut == "") {
      throw new payqr_exeption("Поле payqr_config::secretKeyOut не может быть пустым, проверьте конфигурацию библиотеки PayQR");
    }
    if (payqr_config::$enabledLog && payqr_config::$logFile == "") {
      throw new payqr_exeption("Поле payqr_config::logFile не может быть пустым, проверьте конфигурацию библиотеки PayQR");
    }
    if (payqr_config::$merchantID == "") {
      throw new payqr_exeption("Поле payqr_config::merchantID не может быть пустым, проверьте конфигурацию библиотеки PayQR");
    }
  }

  /**
   * Эквивалент apache_request_headers()
   * @return mixed
   */
  public static function getallheaders()
  {
    foreach ($_SERVER as $name => $value) {
      if (substr($name, 0, 5) == 'HTTP_') {
        $name = str_replace(' ', '-', str_replace('_', ' ', substr($name, 5)));
        $headers[$name] = $value;
      } else {
        if ($name == "CONTENT_TYPE") {
          $headers["Content-Type"] = $value;
        } else {
          if ($name == "CONTENT_LENGTH") {
            $headers["Content-Length"] = $value;
          }
        }
      }
    }
    return $headers;
  }

  /**
   * utf8
   * @param $data
   * @return mixed
   */
  static function encode($data){
    return preg_replace_callback('/\\\\u([0-9a-f]{4})/i',
      function($val){
        return mb_decode_numericentity('&#'.intval($val[1], 16).';', array(0, 0xffff, 0, 0xffff), 'utf-8');
      }, $data
    );
  }
} 