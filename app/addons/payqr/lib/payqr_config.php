<?php
/**
 * Класс конфигурации
 * Подключите этот файл, чтобы обеспечить автозагрузку всех необходимых классов для работы с API PayQR
 */

if (!defined('PAYQR_ROOT')) {
  define('PAYQR_ROOT', dirname(__FILE__) . '/');
}
if (!defined('PAYQR_ERROR_HANDLER')) {
  define('PAYQR_ERROR_HANDLER', dirname(__FILE__) . '/handlers_errors/');
}
if (!defined('PAYQR_HANDLER')) {
  define('PAYQR_HANDLER', dirname(__FILE__) . '/handlers/');
}
require_once(PAYQR_ROOT . 'classes/payqr_autoload.php');

class payqr_config
{
// по умолчанию ниже продемонстрированы примеры значений, укажите актуальные значения для своего "Магазина"
  public static $merchantID = ""; // номер "Магазина" из личного кабинета PayQR (например, 000000-00000)

  public static $secretKeyIn = ""; // входящий ключ из личного кабинета PayQR (SecretKeyIn), используется в уведомлениях от PayQR (например, RqYAprQyaPRQyap)

  public static $secretKeyOut = ""; // исходящий ключ из личного кабинета PayQR (SecretKeyOut), используется в запросах в PayQR (например, yaPRqyAPRQyAPrq)

  public static $logFile = ""; // путь к файлу логов библиотеки PayQR (например, /var/www/payqr/logs/log)

  public static $enabledLog = false; // разрешить библиотеке PayQR вести лог

  public static $maxTimeOut = 10; // максимальное время ожидания ответа PayQR на запрос интернет-сайта в PayQR

  public static $checkHeader = true; // проверять секретный ключ SecretKeyIn в уведомлениях и ответах от PayQR

  public static $version_api = "1.2"; // версия библиотеки PayQR

// методы работы с этим классом конфигурации библиотеки PayQR
  /**
   * Установка номера магазина и секретных ключей
   * @param $merchantID
   * @param $secretKeyIn
   * @param $secretKeyOut
   */
  public static function init($merchantID, $secretKeyIn, $secretKeyOut)
  {
    self::$merchantID = trim($merchantID);
    self::$secretKeyIn = trim($secretKeyIn);
    self::$secretKeyOut = trim($secretKeyOut);
  }

  /**
   * Установка пути к файлу логов библиотеки PayQR
   * @param $logFile
   */
  public static function setLogFile($logFile)
  {
    self::$logFile = trim($logFile);
  }

  /**
   * Установка максимального времени ожидания ответа PayQR на запросы интернет-сайта в PayQR
   * @param $maxTimeOut
   */
  public static function setMaxTimeOut($maxTimeOut)
  {
    self::$maxTimeOut = intval($maxTimeOut)<10 ? 10 : intval($maxTimeOut);
  }

  /**
   * Установка разрешения вести логи библиотеки PayQR
   * @param $enabledLog
   */
  public static function setEnabledLog($enabledLog)
  {
    self::$enabledLog = (Boolean) $enabledLog;
  }

  /**
   * Установка проверки секретного ключа SecretKeyIn в уведомлениях и ответах от PayQR
   * @param $checkHeader
   */
  public static function setCheckHeader($checkHeader)
  {
    self::$checkHeader = (Boolean) $checkHeader;
  }

  private function  __construct(){

  }
} 