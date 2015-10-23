<?php
/**
 * Формирование способов доставки в ответе на уведомление invoice.deliverycases.updating
 */

class payqr_deliverycase {
  /**
   * Внутренний идентификатор способа доставки интернет-сайта (артикул)
   * @var
   */
  public $article;

  /**
   * Номер в списке, отображаемом покупателю в приложении PayQR (1, 2, 3 и т.д., через 1.1., 2.1.1, 2.1.2 и т.п. можно управлять уровнями списка)
   * @var
   */
  public $number;

  /**
   * Название способа доставки (Курьерская доставка, Почта России, DHL и др.)
   * @var
   */
  public $name;

  /**
   * Описание способа доставки (сроки доставки, условия доставки и получения и др.)
   * @var
   */
  public $description;

  /**
   * Стоимость доставки конкретным способом доставки "от" (допускается 0)
   * @var
   */
  public $amountFrom;

  /**
   * Стоимость доставки конкретным способом доставки "до" (допускается 0)
   * @var
   */
  public $amountTo;
// если стоимость "от" и стоимость "до" равны, то наценка фиксированная, а если обе стоимости указаны как 0, то наценка за доставку покупки выбранным способом доставки отсутствует (бесплатно)

  /**
   * Создание способа доставки
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
   * Устанавливает стоимость "от" для способа доставки
   * @param $amountFrom
   */
  public function setAmountFrom($amountFrom)
  {
    $this->amountFrom = $amountFrom;
  }

  /**
   * Устанавливает стоимость "до" для способа доставки
   * @param $amountTo
   */
  public function setAmountTo($amountTo)
  {
    $this->amountTo = $amountTo;
  }

  /**
   * Возвращает массив способа доставки
   * @return array
   */
  public function getArray()
  {
    return array(
      'article' => $this->article,
      'number' => $this->number,
      'name' => $this->name,
      'description' => $this->description,
      'amountFrom' => $this->amountFrom,
      'amountTo' => $this->amountTo,
    );
  }
} 