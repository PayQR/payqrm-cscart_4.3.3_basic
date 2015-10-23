<?php
/**
 * Реализация запросов в PayQR
 */
 
class payqr_base_request
{
  protected $curl;
  protected $errors_message = array(); // массив ошибок возникших во время выполнения запроса

  public function __construct()
  {
    if(function_exists('curl_init')){
      $this->curl = new payqr_curl();
    }
    else{
      $this->curl = new payqr_no_curl();
    }
    $this->curl->headers['PQRSecretKey'] = payqr_config::$secretKeyOut;
    $this->curl->headers['Content-Type'] = 'application/json';
    $this->curl->headers['Content-Length'] = '0';
  }

  /**
   * Отправка POST-запроса
   * @param $url
   * @param array $vars
   * @return mixed|string
   */
  public function post($url, $vars = array())
  {
    $response = $this->curl->post($url, $vars);
    payqr_logs::log(print_r($response, true), __FILE__." ".__FUNCTION__, __LINE__);
    $this->check_response($response);
    return $response->body;
  }

  /**
   * Отправка GET-запроса
   * @param $url
   * @param array $vars
   * @return mixed|string
   */
  public function get($url, $vars = array())
  {
    $response = $this->curl->get($url, $vars);
    payqr_logs::log(print_r($response, true), __FILE__." ".__FUNCTION__, __LINE__);
    $this->check_response($response);
    return $response->body;
  }

  /**
   * Отправка PUT-запроса
   * @param $url
   * @param array $vars
   * @return mixed|string
   */
  public function put($url, $vars = array())
  {
    $response = $this->curl->put($url, $vars);
    payqr_logs::log(print_r($response, true), __FILE__." ".__FUNCTION__, __LINE__);
    $this->check_response($response);
    return $response->body;
  }

  /**
   * Проверяем ответ от PayQR на отправленный запрос в PayQR
   */
  private function check_response(payqr_curl_response $response)
  {
    // Проверяем что ответ не пустой
    if (empty($response))
    {
      throw new payqr_exeption("Получен пустой ответ", 0);
    }
    // Проверяем код ответа
    if (!isset($response->headers['Status-Code']))
    {
      throw new payqr_exeption("Отсутствует заголовок с кодом ответа ".print_r($response, true), 0, $response);
    }
    // Проверяем код ответа
    if ($response->headers['Status-Code'] != '200' && $response->headers['Status-Code'] != '100')
    {
      $r = json_decode($response->body);
      // получаем сообщение ошибки от PayQr
      if(isset($r->message))
        $this->error_message[] = $r->message;
      throw new payqr_exeption("Получен ответ с кодом ошибки ".$response->headers['Status-Code']." ".print_r($response, true), 0, $response);
    }
    // Проверяем заголовок ответа
    if (!payqr_authentication::checkHeader(payqr_config::$secretKeyIn, $response->headers))
    {
      throw new payqr_exeption("Неверный параметр ['PQRSecretKey'] в headers ответа".print_r($response, true), 0, $response);
    }
  }

  /**
   * Возвращает сообщения об ошибках
   * @return array
   */
  public function getErrors(){
    return $this->errors_message;
  }

} 