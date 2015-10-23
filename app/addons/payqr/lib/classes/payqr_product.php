<?php
/**
 * Формирование товарных позиций в кнопке PayQR
 */

class payqr_product {
  /**
   * Внутренний идентификатор товарной позиции интернет-сайта (артикул)
   * @var
   */
  public $article;
  
  /**
   * Название товарной позиции
   * @var
   */  
  public $name;
  
  /**
   * Абсолютная ссылка на изображение товарной позиции
   * @var
   */  
  public $imageurl;
  
  /**
   * Стоимость всей товарной позиции (цена товарной единицы, умноженная на количество)
   * @var
   */  
  public $amount;
  
  /**
   * Количество товарных единиц в товарной позиции
   * @var
   */  
  public $quantity;
  
  /**
   * Категория товарной позиции в соответствии с классификатором товаров/услуг PayQR (необязательно)
   * @var
   */  
  public $category;

  /**
   * Создание продукта
   * @param $article
   * @param $name
   * @param $amount
   * @param $quantity
   * @param $category   
   * @param string $imageurl
   */
  public function __construct($article, $name, $amount, $quantity, $category, $imageurl=""){
    $this->article = $article;
    $this->name = $name;
    $this->imageurl = $imageurl;
    $this->amount = $amount;
    $this->quantity = $quantity;
    $this->category = $category;	
    return $this;
  }

  /**
   * Возвращает массив товарной позиции
   * @return array
   */
  public function getArray(){
    return array(
      'article' => $this->article,
      'name' => $this->name,
      'imageurl' => $this->imageurl,
      'amount' => $this->amount,
      'quantity' => $this->quantity,
      'category' => $this->category,	  
    );
  }
}