<?php
/**
 * Ведение отладочных логов
 */
 
class payqr_logs
{

  /**
   * Добавление записи в лог файл
   *
   * @param $file
   * @param $message
   */
  public static function log($message, $file_name=false, $line=false)
  {
    $micro_date = microtime();
    $date_array = explode(" ",$micro_date);
    $date = date("Y-m-d H:i:s",$date_array[1]);
    $message = mb_convert_encoding($message, 'utf-8', mb_detect_encoding($message));
    $message = html_entity_decode($message, ENT_NOQUOTES, 'UTF-8');
    $message = payqr_base::encode($message);
    if(!payqr_config::$enabledLog)
      return;
    if($file_name && $line){
      @file_put_contents(payqr_config::$logFile, "\r\n==============" . $date." ".$date_array[0] . "==============\r\n", FILE_APPEND);
      @file_put_contents(payqr_config::$logFile, "\r\n====".$file_name." Line: ".$line."=====\r\n", FILE_APPEND);
    }
    else
      @file_put_contents(payqr_config::$logFile, "\r\n==============" . $date." ".$date_array[0] . "==============\r\n", FILE_APPEND);
    @file_put_contents(payqr_config::$logFile, $message, FILE_APPEND);
  }

  public static function addEnter()
  {
    @file_put_contents(payqr_config::$logFile, "\r\n\r\n\r\n\r\n", FILE_APPEND);
  }
} 