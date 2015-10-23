<?php
/**
 * Прием уведомлений от PayQR
 */

class payqr_receiver
{

  public $objectEvent; // объект уведомления

  public $objectOrder; // объект события

  public function __construct()
  {
    payqr_base::checkConfig();
    $this->objectResponse = false;
    $this->objectOrder = false;
    $this->orderId = 0;
    $this->errors = array();
  }

  /**
   * Получаем уведомление
   */
  public function receiving()
  {
    // Проверяем что уведомление действительно от PayQR (по секретному ключу SecretKeyIn)
    payqr_logs::log('payqr_receiver::receiving()');
    if (!payqr_authentication::checkHeader(payqr_config::$secretKeyIn)) {
      // если уведомление пришло не от PayQR, вызываем исключение
      throw new payqr_exeption("Неверный параметр ['PQRSecretKey'] в header уведомления", 1);
    }
    // Получаем данные из тела запроса
    $json = file_get_contents('php://input');
    // Проверяем валидность JSON данных
    payqr_json_validator::validate($json);
    // Получаем объект события из уведомления
    $this->objectEvent = json_decode($json);
    // Проверяем наличие свойства типа объекта
    if (!isset($this->objectEvent->object) || !isset($this->objectEvent->id) || !isset($this->objectEvent->type)) {
      throw new payqr_exeption("В уведомлении отстутствуют обязательные параметры object id type", 1, $json);
    }
    // Сохраняем тип уведомления
    $this->type = $this->objectEvent->type;

    payqr_logs::log("invoice.type: " . $this->type);

    // В зависимости от того какого типа уведомление, создаем объект
    switch ($this->objectEvent->data->object) {
      case 'invoice':
        $this->objectOrder = new payqr_invoice($this->objectEvent->data, $this->objectEvent->livemode);
        break;
      case 'revert':
        $this->objectOrder = new payqr_revert($this->objectEvent->data, $this->objectEvent->livemode);
        break;
      default:
        throw new payqr_exeption("В уведомлении отстутствуют обязательные параметры object id type", 1, $json);
        return false;
    }
    payqr_logs::log("Идентификатор счета на оплату: " . $this->objectEvent->id);
    // если все прошло успешно, возвращаем идентификатор счета на оплату
    return $this->objectEvent->id;
  }

  /**
   * Ответ на уведомление
   */
  public function response()
  {
    $this->validateResponse();
    header("PQRSecretKey:" . payqr_config::$secretKeyOut);
    if($this->objectOrder->cancel){ // если счет отмечен как отмененный
      header("HTTP/1.1 409 Conflict");
      payqr_logs::log('payqr_receiver::response() - Send HTTP/1.1 409 Conflict');
      return;
    }
    header("HTTP/1.1 200 OK");
    echo json_encode($this->objectEvent);
    payqr_logs::log('payqr_receiver::response()');
    payqr_logs::log(json_encode($this->objectEvent)."\n\r");
  }


  /**
   * Возвращает тип уведомления
   * @return mixed
   */
  public function getType()
  {
    return $this->type;
  }

  /**
   * Проверяет наличие обязательных параметров в ответе на уведомление от PayQR
   * @return bool
   */
  private function validateResponse()
  {
    switch($this->type){
      case 'invoice.order.creating':
        if(intval($this->objectOrder->getOrderId()) < 1 || strlen($this->objectOrder->getOrderId())==0){
          payqr_logs::log('Критическая ошибка: интернет-сайт не указал уникальный номер созданного заказа (orderId) в ответе на уведомление invoice.order.creating', 'payqr_receiver::validateResponse()', __LINE__);
          return false;
        }
    }
    return true;
  }
} 