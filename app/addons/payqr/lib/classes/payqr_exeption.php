<?php
/**
 * Исключение во время работы с API PayQR
 */

class payqr_exeption extends Exception{

  public $response; // объект ответа PayQR

  /**
   * Default Constructor
   *
   * @param string|null $message
   * @param int  $code
   */
  public function __construct($message = null, $code = 0, $response = false)
  {
    parent::__construct($message, $code);
    $this->response = $response;
    payqr_logs::log('Вызвано исключение : '.$this->errorMessage());
  }
  /**
   * prints error message
   *
   * @return string
   */
  public function errorMessage()
  {
    $errorMsg = 'Error on line ' . $this->getLine() . ' in ' . $this->getFile()
      . ': ' . $this->getMessage();
    return $errorMsg;
  }

  /**
   * Возвращает содержимое полученного ответа
   * @return bool
   */
  public function getResponse()
  {
    return $this->response;
  }
} 