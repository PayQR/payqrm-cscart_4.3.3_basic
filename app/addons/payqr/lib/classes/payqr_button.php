<?php
/**
 * Конструктор кнопки PayQR
 */

class payqr_button
{
  private $width = '163'; // Ширина кнопки PayQR
  private $height = '36'; // Высота кнопки PayQR
  private $scenario = 'buy'; // Сценарий, влияющий на название кнопки в приложении PayQR и другие текстовые изменения (buy - "Купить", pay - "Оплатить")
  private $productsArray; // Содержание заказа (массив позиций заказа)
  private $amount; // Сумма заказа

  // варианты оформления кнопки PayQR
  private $color = array(
    'default' => '',
    'orange' => 'payqr-button_orange',
    'red' => 'payqr-button_red',
    'blue' => 'payqr-button_blue',
    'green' => 'payqr-button_green'
  );

  private $borderRadius = array(
    'default' => '',
    'sharp' => 'payqr-button_sharp',
    'rude' => 'payqr-button_rude',
    'soft' => 'payqr-button_soft',
    'oval' => 'payqr-button_oval',
    'sleek' => 'payqr-button_sleek'
  );
  private $gradient = array(
    'default' => '',
    'flat' => 'payqr-button_flat',
    'gradient' => 'payqr-button_gradient'

  );
  private  $shadow = array(
    'default' => '',
    'shadow' => 'payqr-button_shadow',
    'noshadow' => 'payqr-button_noshadow'
  );
  private $textTransform = array(
    'default' => '',
    'upper' => 'payqr-button_text-uppercase',
    'lowercase' => 'payqr-button_text-lowercase',
    'standardcase' => 'payqr-button_text-standardcase'
  );
  private $fontWeight = array(
    'default' => '',
    'bold' => 'payqr-button_text-bold',
    'normal' => 'payqr-button_text-normal'
  );
  private $fontSize = array(
    'default' => '',
    'small' => 'payqr-button_text-small',
    'large' => 'payqr-button_text-large',
    'medium' => 'payqr-button_text-medium'
  );
  private $style = array(
    'color' => '',
    'borderRadius' => '',
    'fontSize' => '',
    'fontWeight' => '',
    'textTransform' => '',
    'gradient' => '',
    'shadow' => '',
  );
  // настройки процесса совершения покупки (какие данные собирать, какие этапы включать и так далее)
  public $promo_required = false; // сбор промо-кодов промо-кодов или номеров карт лояльности
  private $promo_description = false; // описание поля для ввода промо-кода или номера карты лояльности

  public $firstname_required = false; // запрос имени покупателя
  public $lastname_required = false; // запрос фамилии покупателя
  public $middlename_required = false; // запрос фамилии покупателя
  public $phone_required = false; // запрос телефона покупателя
  public $email_required = false; // запрос e-mail покупателя
  public $delivery_required = false; // запрос адреса доставки покупателя
  public $deliverycases_required = false; // выбор способа доставки
  public $pickpoints_required = false; // выбор пункта самовывоза

  private $orderid = false; // номер заказа
  private $ordergroup = false; // номер товарной группы

  private $message_text = false; // текст сообщения от продавца к покупкам, совершаемым через PayQR
  private $message_imageurl = false; // изображение к тексту сообщения от продавца к покупкам, совершаемым через PayQR
  private $message_url = false; // адрес, куда покупатель будет перенаправляться при нажатии на сообщение от продавца к покупкам, совершаемым через PayQR

  public $userdata = false; // заполнение любых дополнительных служебных/аналитических данных в свободном формате
  
  public function __construct($amount, $productsArray = array())
  {
    $this->amount = $amount;
    $this->productsArray = $productsArray;
  }

  /**
   * Возвращает код скрипта PayQR для размещения в head интернет-сайта
   */
  public static function getJs()
  {
    return '<script src="https://payqr.ru/popup.js?merchId=' . payqr_config::$merchantID . '"></script>';
  }

  /**
   * Устанавливает ширину кнопки PayQR
   * @param $width
   */
  public function setWidth($width)
  {
    $this->width = $width;
  }

  /**
   * Устанавливает высоту кнопки PayQR
   * @param $height
   */
  public function setHeight($height)
  {
    $this->height = $height;
  }

  /**
   * Устанавливает цвет кнопки PayQR из доступного набора
   * @param $color
   */
  public function setColor($color)
  {
    if (isset($this->color[$color])) {
      $this->style['color'] = $this->color[$color];
    }
  }

  /**
   * Устанавливает края кнопки PayQR из доступного набора
   * @param $borderRadius
   */
  public function setBorderRadius($borderRadius)
  {
    if (isset($this->borderRadius[$borderRadius])) {
      $this->style['borderRadius'] = $this->borderRadius[$borderRadius];
    }
  }

  /**
   * Устанавливает границы из набора
   * @param $gradient
   */
  public function setGradient($gradient)
  {
    if (isset($this->gradient[$gradient])) {
      $this->style['gradient'] = $this->gradient[$gradient];
    }
  }

  /** Устанавливает размер шрифта в кнопке PayQR из доступного набора
   * @param $fontSize
   */
  public function setFontSize($fontSize)
  {
    if (isset($this->fontSize[$fontSize])) {
      $this->style['fontSize'] = $this->fontSize[$fontSize];
    }
  }

  /**
   * Уставливает стиль текста в кнопке PayQR из доступного набора
   * @param $fontWeight
   */
  public function setFontWeight($fontWeight)
  {
    if (isset($this->fontWeight[$fontWeight])) {
      $this->style['fontWeight'] = $this->fontWeight[$fontWeight];
    }
  }

  /**
   * Устанавливает трансформацию текста в кнопке PayQR из доступного набора
   * @param $textTransform
   */
  public function setTextTransform($textTransform)
  {
    if (isset($this->textTransform[$textTransform])) {
      $this->style['textTransform'] = $this->textTransform[$textTransform];
    }
  }

  /**
   * Устанавливает тень в кнопке PayQR из доступного набора
   * @param $textTransform
   */
  public function setShadow($shadow)
  {
    if (isset($this->shadow[$shadow])) {
      $this->style['shadow'] = $this->shadow[$shadow];
    }
  }

  /**
   * Устанавливает текст сообщения от продавца к покупкам, совершаемым через PayQR
   * @param $message_text
   */
  public function setMessageText($message_text)
  {
    $this->message_text = $message_text;
  }

  /**
   * Устанавливает изображение к тексту сообщения от продавца к покупкам, совершаемым через PayQR
   * @param $message_imageurl
   */
  public function setMessageImageUrl($message_imageurl)
  {
    $this->message_imageurl = $message_imageurl;
  }

  /**
   * Устанавливает адрес, куда покупатель будет перенаправляться при нажатии на сообщение от продавца к покупкам, совершаемым через PayQR
   * @param $message_url
   */
  public function setMessageUrl($message_url)
  {
    $this->message_url = $message_url;
  }

  /**
   * Устанавливает номер товарной группы
   * @param $ordergroup
   */
  public function setOrderGroup($ordergroup)
  {
    $this->ordergroup = $ordergroup;
  }

  /**
   * Устанавливает описание поля для ввода промо-кода или номера карты лояльности
   * @param $promo_description
   */
  public function setPromoDescription($promo_description)
  {
    $this->promo_description = $promo_description;
  }

  /**
   * Устанавливает userdata
   * @param $userdata
   */
  public function setUserData($userdata){
    $this->userdata = $userdata;
  }

  /**
   * Устанавливает номер заказа в случаях, когда заказ уже создан на уровне формирования кнопки PayQR
   * @param $orderid
   */
  public function setOrderId($orderid)
  {
    $this->orderid = $orderid;
  }

  /**
   * Возвращает html кнопки
   * @return string
   */
  public function getHtmlButton()
  {
    //собираем стиль кнопки
    $style = "";
    if(intval($this->width)>0 || intval($this->height)>0){
      if(intval($this->width)>0){
        $style .="width:".intval($this->width)."px;";
      }
      if(intval($this->height)>0){
        $style .="height:".intval($this->height)."px;";
      }
      $style = 'style="'.$style.'"';
    }
    //собираем классы для кнопки
    foreach($this->style as $k=>$v)
      if($v==''){
        unset($this->style[$k]);
      }
    $class = implode(" ", $this->style);

    $html = '<button
      class="payqr-button '. $class .'"
      '. $style .'
      data-scenario="'. $this->scenario .'"
      data-cart=\''. json_encode($this->productsArray) .'\'
      '.'data-firstname-required="' . $this->firstname_required . '"' .'
      '.'data-lastname-required="' . $this->lastname_required . '"' .'
      '.'data-middlename-required="' . $this->middlename_required . '"' .'
      '.'data-phone-required="' . $this->phone_required . '"' .'
      '.'data-email-required="' . $this->email_required . '"' .'
      '.'data-delivery-required="' . $this->delivery_required . '"' .'
      '.'data-deliverycases-required="' . $this->deliverycases_required . '"' .'
      '.'data-pickpoints-required="' . $this->pickpoints_required . '"' .'
      '.'data-promo-required="' . $this->promo_required . '"' .'
      '.($this->ordergroup ? 'data-ordergroup=\''.$this->ordergroup.'\'' : '') .'
      '.($this->promo_description ? 'data-promo-description="'.$this->promo_description.'"' : '') .'
      '.($this->userdata ? "data-userdata='".$this->userdata."'" : '') .'
      '.($this->message_text ? 'data-message-text="'.$this->message_text.'"' : '') .'
      '.($this->message_imageurl ? 'data-message-imageurl="'.$this->message_imageurl.'"' : '') .'
      '.($this->message_url ? 'data-message-url="'.$this->message_url.'"' : '') .'
      data-amount="'. $this->amount .'"
      '.($this->orderid ? 'data-orderid="'.$this->orderid.'"' : '') .'
      >Купить быстрее
    </button>';
    return $html;
  }

} 