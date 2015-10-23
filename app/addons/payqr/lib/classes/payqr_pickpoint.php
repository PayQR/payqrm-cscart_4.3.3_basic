<?php
/**
 * Формирование пунктов самовывоза в ответе на уведомление invoice.pickpoints.updating
 */

class payqr_pickpoint {
  /**
   * Внутренний идентификатор пункта самовывоза интернет-сайта (артикул)
   * @var
   */
  public $article;

  /**
   * Номер в списке, отображаемом покупателю в приложении PayQR (1, 2, 3 и т.д., через 1.1., 2.1.1, 2.1.2 и т.п. можно управлять уровнями списка)
   * @var
   */
  public $number;

  /**
   * Название пункта самовывоза (наименование торговой точки, станция метро и др.)
   * @var
   */
  public $name;

  /**
   * Описание пункта самовывоза (сроки получения, условия получения, полный адрес и др.)
   * @var
   */
  public $description;

  /**
   * Географическая долгота в градусах, например, 55.733500 (необязательно)
   * @var
   */
  public $longitude;

  /**
   * Географическая широта в градусах, например, 37.633231 (необязательно)
   */
  public $latitude;

  /**
   * Стоимость получения в пункте самовывоза "от" (допускается 0)
   * @var
   */
  public $amountFrom;

  /**
   * Стоимость получения в пункте самовывоза "до" (допускается 0)
   * @var
   */
  public $amountTo;
// если стоимость "от" и стоимость "до" равны, то наценка фиксированная, а если обе стоимости указаны как 0, то наценка за получение покупки в конкретном пункте самовывоза отсутствует (бесплатно)

  /**
   * Создание пункта самовывоза
   * @param $article
   * @param $number
   * @param $name
   * @param $description
   */
  public function __construct($article, $number, $name, $description)
  {
    $this->article = $article;
    $this->number = $number;
    $this->name = $name;
    $this->description = $description;
  }

  /**
   * Устанавливает географическую долготу пункта самовывоза (для отображения на карте в приложении PayQR)
   * @param $longitude
   */
  public function setLongitude($longitude)
  {
    $this->longitude = $longitude;
  }

  /**
   * Устанавливает географическую широту пункта самовывоза (для отображения на карте в приложении PayQR)
   * @param $latitude
   */
  public function setLatitude($latitude)
  {
    $this->latitude = $latitude;
  }

  /**
   * Устанавливает стоимость "от" для пункта самовывоза
   * @param $amountFrom
   */
  public function setAmountFrom($amountFrom)
  {
    $this->amountFrom = $amountFrom;
  }

  /**
   * Устанавливает стоимость "до" для пункта самовывоза
   * @param $amountTo
   */
  public function setAmountTo($amountTo)
  {
    $this->amountTo = $amountTo;
  }

  /**
   * Возвращает массив пункта самовывоза
   * @return array
   */
  public function getArray()
  {
    return array(
      'article' => $this->article,
      'number' => $this->number,
      'name' => $this->name,
      'description' => $this->description,
      'longitude' => $this->longitude,
      'latitude' => $this->latitude,
      'amountFrom' => $this->amountFrom,
      'amountTo' => $this->amountTo,
    );
  }

} 